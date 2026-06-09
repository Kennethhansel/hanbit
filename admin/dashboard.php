<?php
require_once 'config.php';
require_once 'koneksi.php';
proteksi_halaman();

// Ambil konfigurasi global target omzet dari tabel admin
$id_admin = $_SESSION['id_user'] ?? 1;
$q_config = mysqli_query($koneksi, "SELECT target_omzet FROM admin_accounts WHERE id_admin = '$id_admin' LIMIT 1");
$target_omzet_db = mysqli_fetch_assoc($q_config)['target_omzet'] ?? 5000000;

// =========================================================================
// LOGIKA FILTER SPESIFIK (DEFAULT: HARI INI)
// =========================================================================
$pilihan_hari  = isset($_GET['hari']) ? trim($_GET['hari']) : date('Y-m-d');
$pilihan_bulan = isset($_GET['bulan']) ? intval($_GET['bulan']) : intval(date('m'));
$pilihan_tahun = isset($_GET['tahun']) ? intval($_GET['tahun']) : intval(date('Y'));
$mode_filter   = isset($_GET['mode']) ? trim($_GET['mode']) : 'hari'; // Default selalu per hari saat pertama dibuka

// Query dasar untuk data finansial dan operasional (Menghitung yang status_order = 'SELESAI')
$query_pendapatan_str = "SELECT SUM(total_harga) as total FROM reservations WHERE status_order = 'SELESAI'";
$query_selesai_today_str = "SELECT COUNT(*) as total FROM reservations WHERE status_order = 'SELESAI'";

// Query dasar untuk grafik perbandingan tipe booking (Sinkron murni berdasarkan riwayat selesai)
$query_chart_maintenance_str = "SELECT COUNT(*) as total FROM reservations WHERE status_order = 'SELESAI' AND paket_tipe != 'custom_estimasi'";
$query_chart_custom_str = "SELECT COUNT(*) as total FROM reservations WHERE status_order = 'SELESAI' AND paket_tipe = 'custom_estimasi'";

// Menggunakan tanggal_booking agar semua data di riwayat terhitung sempurna dan tidak hilang
if ($mode_filter == 'tahun') {
    $kondisi_sql = " AND YEAR(tanggal_booking) = $pilihan_tahun";
} elseif ($mode_filter == 'bulan') {
    $kondisi_sql = " AND MONTH(tanggal_booking) = $pilihan_bulan AND YEAR(tanggal_booking) = $pilihan_tahun";
} else {
    // Mode hari spesifik (Default: Hari ini)
    $kondisi_sql = " AND DATE(tanggal_booking) = '$pilihan_hari'";
}

// 1. Eksekusi query pendapatan terfilter
$q_pendapatan = mysqli_query($koneksi, $query_pendapatan_str . $kondisi_sql);
$total_pendapatan = mysqli_fetch_assoc($q_pendapatan)['total'] ?? 0;

// 2. Eksekusi query hitung jumlah unit yang SELESAI (Servis Rampung)
$q_selesai_today = mysqli_query($koneksi, $query_selesai_today_str . $kondisi_sql);
$total_selesai_hari_ini = mysqli_fetch_assoc($q_selesai_today)['total'] ?? 0;

// 3. Statistik Antrean Aktif Saat Ini (Semua unit berjalan/belum selesai, realtime tidak terikat filter)
$query_aktif = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM reservations WHERE status_order != 'SELESAI'"); 
$total_antrean_aktif = mysqli_fetch_assoc($query_aktif)['total'] ?? 0;

// 4. Eksekusi data pembanding untuk grafik doughnut (Membaca data riwayat selesai)
$total_chart_maintenance = mysqli_fetch_assoc(mysqli_query($koneksi, $query_chart_maintenance_str . $kondisi_sql))['total'] ?? 0;
$total_chart_custom = mysqli_fetch_assoc(mysqli_query($koneksi, $query_chart_custom_str . $kondisi_sql))['total'] ?? 0;

// Hitung persentase capaian omzet terhadap target
$persentase_omzet = ($target_omzet_db > 0) ? min(round(($total_pendapatan / $target_omzet_db) * 100, 1), 100) : 0;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hanbit Admin - Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght=400;500;600;700;800;900&display=swap');
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-[#f8fafc] text-slate-800 antialiased flex min-h-screen">

    <?php include 'sidebar.php'; ?>

    <main class="flex-1 p-8 overflow-y-auto">
        <div class="max-w-full mx-auto px-2 space-y-6">
            
            <div class="flex flex-col xl:flex-row justify-between xl:items-center gap-4 min-h-[56px]">
                <div>
                    <h1 class="text-2xl font-black tracking-tight text-slate-900">Dashboard Utama</h1>
                    <p class="text-xs text-slate-400 font-medium">Panel kendali performa antrean dan parameter finansial Toko Hanbit.</p>
                </div>
            </div>

            <div class="bg-white border border-gray-200/80 p-5 rounded-2xl shadow-sm space-y-4">
                <div class="flex items-center gap-2 border-b pb-2">
                    <div class="w-2 h-2 rounded-full bg-blue-500"></div>
                    <h3 class="text-xs font-extrabold uppercase text-slate-900 tracking-wider">Parameter Filter Data Spesifik</h3>
                </div>
                
                <form method="GET" action="dashboard.php" class="grid grid-cols-1 sm:grid-cols-12 gap-3 text-xs font-bold text-slate-600">
                    <div class="sm:col-span-3">
                        <label class="text-[10px] text-slate-400 uppercase tracking-wider block mb-1">Pilih Mode Analisis</label>
                        <select name="mode" id="filter_mode" onchange="gantiFormInput(this.value)" class="w-full px-3 py-2.5 bg-slate-50 border border-gray-200 rounded-xl focus:outline-none focus:bg-white cursor-pointer transition">
                            <option value="hari" <?= $mode_filter == 'hari' ? 'selected' : ''; ?>>📅 Per Hari</option>
                            <option value="bulan" <?= $mode_filter == 'bulan' ? 'selected' : ''; ?>>📆 Per Bulan</option>
                            <option value="tahun" <?= $mode_filter == 'tahun' ? 'selected' : ''; ?>>🏢 Per Tahun</option>
                        </select>
                    </div>

                    <div id="wrapper_hari" class="sm:col-span-6 <?= $mode_filter == 'hari' ? '' : 'hidden'; ?>">
                        <label class="text-[10px] text-slate-400 uppercase tracking-wider block mb-1">Tentukan Tanggal</label>
                        <input type="date" name="hari" value="<?= $pilihan_hari; ?>" class="w-full px-4 py-2 bg-slate-50 border border-gray-200 rounded-xl focus:outline-none focus:bg-white transition text-slate-700">
                    </div>

                    <div id="wrapper_bulan" class="sm:col-span-3 <?= $mode_filter == 'bulan' ? '' : 'hidden'; ?>">
                        <label class="text-[10px] text-slate-400 uppercase tracking-wider block mb-1">Pilih Bulan</label>
                        <select name="bulan" class="w-full px-3 py-2.5 bg-slate-50 border border-gray-200 rounded-xl focus:outline-none focus:bg-white cursor-pointer transition">
                            <?php
                            $nama_bulan_arr = [1=>"Januari","Februari","Maret","April","Mei","Juni","Juli","Agustus","September","Otober","November","Desember"];
                            foreach ($nama_bulan_arr as $num => $nama) {
                                $sel = ($pilihan_bulan == $num) ? 'selected' : '';
                                echo "<option value='$num' $sel>$nama</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <div id="wrapper_tahun" class="sm:col-span-3 <?= ($mode_filter == 'bulan' || $mode_filter == 'tahun') ? '' : 'hidden'; ?>">
                        <label class="text-[10px] text-slate-400 uppercase tracking-wider block mb-1">Tentukan Tahun</label>
                        <input type="number" name="tahun" value="<?= $pilihan_tahun; ?>" min="2020" max="2035" class="w-full px-4 py-2 bg-slate-50 border border-gray-200 rounded-xl focus:outline-none focus:bg-white transition text-slate-700 font-bold">
                    </div>

                    <div class="sm:col-span-3 flex items-end">
                        <button type="submit" class="w-full bg-black hover:bg-slate-900 text-white font-bold text-xs uppercase py-2.5 rounded-xl tracking-wider transition shadow-sm flex items-center justify-center gap-1.5 h-[38px]">
                            <i class="fas fa-filter text-[10px]"></i> Terapkan Filter
                        </button>
                    </div>
                </form>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-6">
                
                <div class="bg-white border border-gray-200/80 p-5 rounded-2xl shadow-sm flex items-center justify-between transition-all duration-200 hover:shadow-md">
                    <div class="space-y-1">
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Total Pendapatan</span>
                        <h3 class="text-xl font-black text-slate-900">Rp <?= number_format($total_pendapatan, 0, ',', '.'); ?></h3>
                        <span class="text-[9px] font-bold text-emerald-600 inline-flex items-center gap-1 bg-emerald-50 px-2 py-0.5 rounded border border-emerald-100 uppercase tracking-wide">
                            <i class="fas fa-check-circle"></i> Selesai
                        </span>
                    </div>
                    <div class="w-11 h-11 bg-emerald-50 rounded-xl flex items-center justify-center text-emerald-500 text-base border border-emerald-100/50">
                        <i class="fas fa-wallet"></i>
                    </div>
                </div>

                <div class="bg-white border border-gray-200/80 p-5 rounded-2xl shadow-sm flex items-center justify-between transition-all duration-200 hover:shadow-md">
                    <div class="space-y-1">
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Target Omzet Bisnis</span>
                        <h3 class="text-xl font-black text-slate-900">Rp <?= number_format($target_omzet_db, 0, ',', '.'); ?></h3>
                        <div class="flex items-center gap-1.5">
                            <div class="w-16 bg-slate-100 rounded-full h-1.5 overflow-hidden border">
                                <div class="bg-blue-500 h-1.5 rounded-full" style="width: <?= $persentase_omzet; ?>%"></div>
                            </div>
                            <span class="text-[10px] font-bold text-blue-600"><?= $persentase_omzet; ?>%</span>
                        </div>
                    </div>
                    <div class="w-11 h-11 bg-blue-50 rounded-xl flex items-center justify-center text-blue-500 text-base border border-blue-100/50">
                        <i class="fas fa-bullseye"></i>
                    </div>
                </div>

                <div class="bg-white border border-gray-200/80 p-5 rounded-2xl shadow-sm flex items-center justify-between transition-all duration-200 hover:shadow-md">
                    <div class="space-y-1">
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Antrean Servis Aktif</span>
                        <h3 class="text-2xl font-black text-slate-900"><?= $total_antrean_aktif; ?> <span class="text-xs font-bold text-slate-400 font-sans">Unit</span></h3>
                        <a href="semua_pesanan.php?view=aktif" class="text-[10px] font-bold text-slate-500 hover:text-black inline-flex items-center gap-1">
                            Lihat Antrean <i class="fas fa-arrow-right text-[8px]"></i>
                        </a>
                    </div>
                    <div class="w-11 h-11 bg-amber-50 rounded-xl flex items-center justify-center text-amber-500 text-base border border-amber-100/50">
                        <i class="fas fa-spinner fa-spin"></i>
                    </div>
                </div>

                <div class="bg-white border border-gray-200/80 p-5 rounded-2xl shadow-sm flex items-center justify-between transition-all duration-200 hover:shadow-md">
                    <div class="space-y-1">
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Servis Selesai</span>
                        <h3 class="text-2xl font-black text-slate-900"><?= $total_selesai_hari_ini; ?> <span class="text-xs font-bold text-slate-400 font-sans">Unit</span></h3>
                        <span class="text-[9px] font-bold text-purple-600 inline-flex items-center gap-1 bg-purple-50 px-2 py-0.5 rounded border border-purple-100 uppercase tracking-wide">
                            <i class="fas fa-clipboard-check"></i> Telah Selesai
                        </span>
                    </div>
                    <div class="w-11 h-11 bg-purple-50 rounded-xl flex items-center justify-center text-purple-500 text-base border border-purple-100/50">
                        <i class="fas fa-laptop-house"></i>
                    </div>
                </div>

            </div>

            <div class="bg-white border border-gray-200/80 p-6 rounded-2xl shadow-sm space-y-4">
                <div class="flex justify-between items-center border-b pb-2">
                    <h3 class="text-xs font-extrabold uppercase text-slate-900 tracking-wider">
                        <i class="fas fa-chart-pie text-slate-700 mr-1"></i> Perbandingan Jenis Booking
                    </h3>
                    <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Perawatan Paket vs Masalah Khusus</span>
                </div>
                <div class="w-full max-w-xs mx-auto min-h-[200px] relative flex items-center justify-center">
                    <canvas id="grafikPerbandinganBooking"></canvas>
                </div>
            </div>

        </div>
    </main>

    <script>
        function gantiFormInput(modeValue) {
            const wrapHari  = document.getElementById('wrapper_hari');
            const wrapBulan = document.getElementById('wrapper_bulan');
            const wrapTahun = document.getElementById('wrapper_tahun');

            if (modeValue === 'hari') {
                wrapHari.classList.remove('hidden');
                wrapBulan.classList.add('hidden');
                wrapTahun.classList.add('hidden');
            } else if (modeValue === 'bulan') {
                wrapHari.classList.add('hidden');
                wrapBulan.classList.remove('hidden');
                wrapTahun.classList.remove('hidden');
            } else if (modeValue === 'tahun') {
                wrapHari.classList.add('hidden');
                wrapBulan.classList.add('hidden');
                wrapTahun.classList.remove('hidden');
            }
        }

        document.addEventListener("DOMContentLoaded", function() {
            const ctx = document.getElementById('grafikPerbandinganBooking').getContext('2d');
            new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: ['Perawatan Paket', 'Masalah Khusus'],
                    datasets: [{
                        data: [<?= $total_chart_maintenance; ?>, <?= $total_chart_custom; ?>],
                        backgroundColor: ['#1e293b', '#facc15'], 
                        borderWidth: 2,
                        borderColor: '#fff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                font: { family: 'Plus Jakarta Sans', size: 11, weight: '700' },
                                color: '#334155',
                                padding: 15
                            }
                        }
                    }
                }
            });
        });
    </script>
</body>
</html>