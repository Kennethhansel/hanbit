<?php
require_once 'config.php';
require_once 'koneksi.php';
proteksi_halaman();

// Ambil konfigurasi global target omzet dari tabel admin
$id_admin = $_SESSION['id_user'] ?? 1;
$q_config = mysqli_query($koneksi, "SELECT target_omzet FROM admin_accounts WHERE id_admin = '$id_admin' LIMIT 1");
$target_omzet_db = mysqli_fetch_assoc($q_config)['target_omzet'] ?? 5000000;

// =========================================================================
// LOGIKA FILTER MAKRO (BULANAN & TAHUNAN SAJA - DEFAULT: BULANAN)
// =========================================================================
$pilihan_bulan = isset($_GET['bulan']) ? intval($_GET['bulan']) : intval(date('m'));
$pilihan_tahun = isset($_GET['tahun']) ? intval($_GET['tahun']) : intval(date('Y'));
$mode_filter   = isset($_GET['mode']) ? trim($_GET['mode']) : 'bulan'; // Default langsung bulanan

// Fondasi kriteria waktu SQL berdasarkan mode filter
if ($mode_filter === 'tahun') {
    $sql_kondisi_selesai = " AND YEAR(tanggal_booking) = $pilihan_tahun"; 
    $sql_kondisi_masuk   = " AND YEAR(created_at) = $pilihan_tahun";
} else {
    // Default mode: bulan
    $sql_kondisi_selesai = " AND MONTH(tanggal_booking) = $pilihan_bulan AND YEAR(tanggal_booking) = $pilihan_tahun";
    $sql_kondisi_masuk   = " AND MONTH(created_at) = $pilihan_bulan AND YEAR(created_at) = $pilihan_tahun";
}

// 1. Hitung Omzet & Total Laptop Selesai (status_order = 'SELESAI')
$res_finansial = mysqli_query($koneksi, "SELECT SUM(total_harga) as omzet, COUNT(*) as total_selesai FROM reservations WHERE status_order = 'SELESAI' $sql_kondisi_selesai");
$data_finansial = mysqli_fetch_assoc($res_finansial);
$total_pendapatan = $data_finansial['omzet'] ?? 0;
$total_selesai_period = $data_finansial['total_selesai'] ?? 0;

// 2. Hitung Tiket Masuk Baru
$res_masuk = mysqli_query($koneksi, "SELECT COUNT(*) as total_masuk FROM reservations WHERE 1=1 $sql_kondisi_masuk");
$total_masuk_period = mysqli_fetch_assoc($res_masuk)['total_masuk'] ?? 0;

// 3. Hitung Beban Meja Kerja (Antrean Berjalan Aktif)
$res_antrean = mysqli_query($koneksi, "SELECT COUNT(*) as total_aktif FROM reservations WHERE status_order NOT IN ('SELESAI', 'BATAL') $sql_kondisi_selesai");
$total_antrean_period = mysqli_fetch_assoc($res_antrean)['total_aktif'] ?? 0;

// Kalkulasi Rasio Pencapaian Finansial Toko terhadap Target Manajemen
$persentase_target = ($target_omzet_db > 0) ? ($total_pendapatan / $target_omzet_db) * 100 : 0;
if ($persentase_target > 100) {
    $persentase_target = 100;
}

// DATA GRAFIK DOUGHNUT (LIVE RATIO CHART BERDASARKAN PERIODE)
$total_chart_maintenance = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as total FROM reservations WHERE paket_tipe != 'custom_estimasi' $sql_kondisi_selesai"))['total'] ?? 0;
$total_chart_custom      = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as total FROM reservations WHERE paket_tipe = 'custom_estimasi' $sql_kondisi_selesai"))['total'] ?? 0;

// =========================================================================
// 🔥 DATA QUERY BAR CHART: MEREK LAPTOP PALING SERING SERVIS (LIVE SQL)
// =========================================================================
$labels_brand = [];
$data_brand_counts = [];
$q_top_brand = mysqli_query($koneksi, "SELECT SUBSTRING_INDEX(laptop_detail, ' ', 1) as brand_name, COUNT(*) as total FROM reservations GROUP BY brand_name ORDER BY total DESC LIMIT 5");
while ($row_tb = mysqli_fetch_assoc($q_top_brand)) {
    $labels_brand[] = strtoupper($row_tb['brand_name']);
    $data_brand_counts[] = intval($row_tb['total']);
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hanbit Admin - Dashboard Admin Hanbit</title>
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

    <main class="flex-1 p-8 overflow-y-auto pb-24">
        <div class="max-w-full mx-auto space-y-6">

            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 border-b pb-5 border-slate-200">
                <div>
                    <h1 class="text-2xl font-black tracking-tight text-slate-900">Dashboard Analitik</h1>
                    <p class="text-xs text-slate-400 font-medium">Pantau omzet makro, volume antrean laptop, dan konversi target performa berkala Hanbit Labs.</p>
                </div>

                <div class="flex flex-wrap items-center gap-3">
                    <a href="../customer/index.php" target="_blank"
                        class="inline-flex items-center gap-2 bg-gradient-to-r from-yellow-400 to-amber-500 hover:from-yellow-500 hover:to-amber-600 text-slate-955 text-xs font-black px-4 py-2.5 rounded-xl shadow-sm transition duration-300 transform hover:-translate-y-0.5 group">
                        <i class="fas fa-external-link-alt text-[11px] group-hover:rotate-12 transition-transform"></i>
                        WEBSITE HANBIT
                    </a>
                </div>
            </div>

            <div class="bg-white border border-gray-200/80 p-4 rounded-2xl shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
                <form method="GET" action="dashboard.php" id="form_filter_dasbor" class="flex flex-wrap items-center gap-3 text-xs font-bold">
                    <div class="flex bg-slate-100 p-1 rounded-xl border border-gray-200 select-none">
                        <button type="button" onclick="gantiModeFilter('bulan')" class="px-5 py-1.5 rounded-lg transition <?= $mode_filter === 'bulan' ? 'bg-white shadow-sm text-slate-955 font-black' : 'text-slate-500 hover:text-slate-800'; ?>">Bulanan</button>
                        <button type="button" onclick="gantiModeFilter('tahun')" class="px-5 py-1.5 rounded-lg transition <?= $mode_filter === 'tahun' ? 'bg-white shadow-sm text-slate-955 font-black' : 'text-slate-500 hover:text-slate-800'; ?>">Tahunan</button>
                    </div>
                    <input type="hidden" name="mode" id="input_mode_filter" value="<?= $mode_filter; ?>">

                    <div id="wrap_filter_bulan" class="flex items-center gap-2 <?= $mode_filter !== 'bulan' ? 'hidden' : ''; ?>">
                        <select name="bulan" onchange="document.getElementById('form_filter_dasbor').submit();" class="px-3 py-1.5 bg-slate-50 border border-gray-200 rounded-xl focus:outline-none cursor-pointer text-slate-700">
                            <?php
                            $nama_bulan_indo = [1 => "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
                            foreach ($nama_bulan_indo as $m_num => $m_name):
                                echo "<option value='$m_num' " . ($pilihan_bulan === $m_num ? 'selected' : '') . ">$m_name</option>";
                            endforeach;
                            ?>
                        </select>
                    </div>

                    <div id="wrap_filter_tahun">
                        <select name="tahun" onchange="document.getElementById('form_filter_dasbor').submit();" class="px-3 py-1.5 bg-slate-50 border border-gray-200 rounded-xl focus:outline-none cursor-pointer text-slate-700">
                            <?php
                            $tahun_skrg = intval(date('Y'));
                            for ($t = $tahun_skrg; $t >= $tahun_skrg - 3; $t--):
                                echo "<option value='$t' " . ($pilihan_tahun === $t ? 'selected' : '') . ">$t</option>";
                            endfor;
                            ?>
                        </select>
                    </div>
                </form>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                <div class="bg-white border border-gray-200/80 p-5 rounded-2xl shadow-sm flex items-center justify-between relative overflow-hidden group">
                    <div class="space-y-1.5 z-10">
                        <span class="text-[10px] font-extrabold uppercase text-slate-400 tracking-wider block">Omzet Periode</span>
                        <h3 class="text-lg font-black text-slate-900">Rp <?= number_format($total_pendapatan, 0, ',', '.'); ?></h3>
                        <p class="text-[10px] font-semibold text-emerald-600 flex items-center gap-1"><i class="fas fa-check-circle"></i> Dana Bersih Kas</p>
                    </div>
                    <div class="w-12 h-12 bg-slate-50 text-slate-800 rounded-xl flex items-center justify-center text-lg border border-gray-100 group-hover:scale-110 transition duration-300"><i class="fas fa-wallet"></i></div>
                </div>

                <div class="bg-white border border-gray-200/80 p-5 rounded-2xl shadow-sm flex items-center justify-between relative overflow-hidden group">
                    <div class="space-y-1.5 z-10">
                        <span class="text-[10px] font-extrabold uppercase text-slate-400 tracking-wider block">Total Laptop Selesai</span>
                        <h3 class="text-xl font-black text-slate-900"><?= $total_selesai_period; ?> <span class="text-xs font-bold text-slate-400">Unit</span></h3>
                        <p class="text-[10px] font-semibold text-blue-600 flex items-center gap-1"><i class="fas fa-arrow-alt-circle-right"></i> Berhasil Diambil</p>
                    </div>
                    <div class="w-12 h-12 bg-slate-50 text-slate-800 rounded-xl flex items-center justify-center text-lg border border-gray-100 group-hover:scale-110 transition duration-300"><i class="fas fa-laptop-medical"></i></div>
                </div>

                <div class="bg-white border border-gray-200/80 p-5 rounded-2xl shadow-sm flex items-center justify-between relative overflow-hidden group">
                    <div class="space-y-1.5 z-10">
                        <span class="text-[10px] font-extrabold uppercase text-slate-400 tracking-wider block">Total Registrasi Masuk</span>
                        <h3 class="text-xl font-black text-slate-900"><?= $total_masuk_period; ?> <span class="text-xs font-bold text-slate-400">Tiket</span></h3>
                        <p class="text-[10px] font-semibold text-amber-500 flex items-center gap-1"><i class="fas fa-clock"></i> Pendaftaran Masuk</p>
                    </div>
                    <div class="w-12 h-12 bg-slate-50 text-slate-800 rounded-xl flex items-center justify-center text-lg border border-gray-100 group-hover:scale-110 transition duration-300"><i class="fas fa-file-invoice"></i></div>
                </div>

                <div class="bg-white border border-gray-200/80 p-5 rounded-2xl shadow-sm flex items-center justify-between relative overflow-hidden group">
                    <div class="space-y-1.5 z-10">
                        <span class="text-[10px] font-extrabold uppercase text-slate-400 tracking-wider block">Beban Kerja Aktif</span>
                        <h3 class="text-xl font-black text-slate-900"><?= $total_antrean_period; ?> <span class="text-xs font-bold text-slate-400">Unit</span></h3>
                        <p class="text-[10px] font-semibold text-rose-500 flex items-center gap-1"><i class="fas fa-tools"></i> Berjalan di Toko</p>
                    </div>
                    <div class="w-12 h-12 bg-slate-50 text-slate-800 rounded-xl flex items-center justify-center text-lg border border-gray-100 group-hover:scale-110 transition duration-300"><i class="fas fa-microchip"></i></div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
                <div class="lg:col-span-7 bg-white border border-gray-200/80 p-6 rounded-2xl shadow-sm space-y-5">
                    <div class="flex justify-between items-center border-b pb-3 border-gray-100">
                        <h3 class="text-xs font-extrabold uppercase text-slate-900 tracking-wider flex items-center gap-1.5"><i class="fas fa-bullseye text-red-500"></i> Target Pendapatan Toko</h3>
                        <span class="text-[11px] font-bold px-2 py-0.5 rounded-md bg-slate-100 text-slate-600">Batas Acuan</span>
                    </div>

                    <div class="space-y-2">
                        <div class="flex justify-between text-xs font-bold">
                            <span class="text-slate-400">Pencapaian Kas Saat Ini:</span>
                            <span class="text-slate-900 font-black">Rp <?= number_format($total_pendapatan, 0, ',', '.'); ?> / <span class="text-slate-400 font-medium">Rp <?= number_format($target_omzet_db, 0, ',', '.'); ?></span></span>
                        </div>
                        <div class="w-full h-4 bg-slate-100 rounded-full overflow-hidden border p-0.5">
                            <div class="h-full bg-gradient-to-r from-slate-800 to-slate-950 rounded-full transition-all duration-1000" style="width: <?= $persentase_target; ?>%"></div>
                        </div>
                        <div class="flex justify-between text-[10px] font-bold text-slate-400">
                            <span>0% Mulai</span>
                            <span class="text-slate-800 font-extrabold bg-yellow-400/20 px-2 py-0.5 rounded-md">Terpenuhi: <?= number_format($persentase_target, 1); ?>%</span>
                            <span>100% Finish</span>
                        </div>
                    </div>

                    <div class="bg-slate-50 border p-4 rounded-xl text-[11px] font-medium text-slate-500 leading-relaxed space-y-1">
                        <div class="font-extrabold text-slate-800 uppercase text-[10px] tracking-wider flex items-center gap-1 mb-1"><i class="fas fa-info-circle text-blue-500"></i> Indikator Evaluasi Sistem:</div>
                        <?php if ($persentase_target >= 100): ?>
                            <span class="text-emerald-600 font-bold">🔥 Selamat! Target omzet manajemen Hanbit Labs untuk rentang periode makro ini telah tercapai 100% penuh. Performa bisnis berjalan sangat optimal.</span>
                        <?php else: ?>
                            <span>Sistem mendeteksi kas toko masih memerlukan penambahan omzet bersih sebesar <span class="text-slate-900 font-bold">Rp <?= number_format(max(0, $target_omzet_db - $total_pendapatan), 0, ',', '.'); ?></span> untuk menyentuh plafon batas target ideal yang telah dirumuskan admin akun di pengaturan.</span>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="lg:col-span-5 bg-white border border-gray-200/80 p-6 rounded-2xl shadow-sm space-y-4">
                    <h3 class="text-xs font-extrabold uppercase text-slate-900 tracking-wider border-b pb-3 border-gray-100"><i class="fas fa-chart-pie text-amber-500"></i> Perbandingan Varian Servis (Periode)</h3>
                    <div class="relative h-[180px] w-full mx-auto">
                        <?php if ($total_chart_maintenance == 0 && $total_chart_custom == 0): ?>
                            <div class="absolute inset-0 flex items-center justify-center text-xs font-bold text-slate-400 italic bg-slate-50 rounded-xl border border-dashed">Tidak ada data statistik untuk rasio grafik.</div>
                        <?php endif; ?>
                        <canvas id="grafikPerbandinganBooking"></canvas>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
                <div class="lg:col-span-12 bg-white border border-gray-200/80 p-6 rounded-2xl shadow-sm space-y-4">
                    <div class="flex justify-between items-center border-b pb-3 border-gray-100">
                        <h3 class="text-xs font-extrabold uppercase text-slate-900 tracking-wider flex items-center gap-1.5">
                            <i class="fas fa-chart-bar text-blue-500"></i> Statistik Merek Laptop Paling Sering Servis
                        </h3>
                        <span class="text-[9px] font-black uppercase tracking-wider bg-blue-50 text-blue-600 px-2 py-0.5 rounded border border-blue-100">Business Intelligence</span>
                    </div>
                    <div class="relative h-[250px] w-full">
                        <?php if (empty($labels_brand)): ?>
                            <div class="absolute inset-0 flex items-center justify-center text-xs font-bold text-slate-400 italic bg-slate-50 rounded-xl border border-dashed">Belum ada rekaman data brand masuk di database.</div>
                        <?php endif; ?>
                        <canvas id="grafikBatangBrandLaptop"></canvas>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
                <div class="lg:col-span-12 bg-white border border-gray-200/80 rounded-2xl shadow-sm overflow-hidden">
                    <div class="p-4 border-b border-gray-100 bg-slate-50/50 flex justify-between items-center">
                        <h3 class="text-xs font-extrabold uppercase text-slate-900 tracking-wider flex items-center gap-1.5">
                            <i class="fas fa-bolt text-amber-500 animate-pulse"></i> Quick View: 5 Antrean Registrasi Terbaru Masuk
                        </h3>
                        <a href="semua_pesanan.php" class="text-[10px] font-extrabold uppercase tracking-wide text-blue-500 hover:text-blue-700 flex items-center gap-1 transition">
                            Lihat Semua Antrean <i class="fas fa-arrow-right text-[8px]"></i>
                        </a>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse text-xs">
                            <thead>
                                <tr class="bg-slate-100/60 text-slate-400 font-bold uppercase border-b border-gray-100 select-none">
                                    <th class="p-4 w-32">No. Invoice</th>
                                    <th class="p-4">Identitas Pelanggan</th>
                                    <th class="p-4">Merek & Tipe Unit</th>
                                    <th class="p-4 w-36 text-center">Jadwal Kedatangan</th>
                                    <th class="p-4 w-36 text-center">Status Progres</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 font-medium text-slate-700">
                                <?php
                                $q_quick = mysqli_query($koneksi, "SELECT no_invoice, nama_pelanggan, no_whatsapp, laptop_detail, tanggal_booking, status_order FROM reservations ORDER BY created_at DESC LIMIT 5");
                                if (mysqli_num_rows($q_quick) > 0):
                                    while ($qv = mysqli_fetch_assoc($q_quick)):
                                        $status_raw = strtoupper(trim($qv['status_order']));
                                        $badge_class = "bg-slate-50 text-slate-400 border-slate-200";
                                        if ($status_raw === 'PENDING' || $status_raw === 'PENDING_ADMIN') $badge_class = "bg-blue-50 text-blue-600 border-blue-100";
                                        elseif ($status_raw === 'PENGECEKAN' || $status_raw === 'SEDANG DIKERJAKAN') $badge_class = "bg-amber-50 text-amber-600 border-amber-100";
                                        elseif ($status_raw === 'PERBAIKAN') $badge_class = "bg-orange-50 text-orange-600 border-orange-100";
                                        elseif ($status_raw === 'SELESAI') $badge_class = "bg-emerald-50 text-emerald-600 border-emerald-100";
                                ?>
                                    <tr class="hover:bg-slate-50/60 transition">
                                        <td class="p-4 font-bold text-slate-400">#<?= htmlspecialchars($qv['no_invoice']); ?></td>
                                        <td class="p-4">
                                            <div class="font-bold text-slate-900 uppercase text-[11px]"><?= htmlspecialchars($qv['nama_pelanggan']); ?></div>
                                            <div class="text-[9px] text-slate-400 mt-0.5"><i class="fab fa-whatsapp text-emerald-500"></i> <?= htmlspecialchars($qv['no_whatsapp']); ?></div>
                                        </td>
                                        <td class="p-4 font-bold text-slate-700 uppercase italic text-[11px]"><?= htmlspecialchars($qv['laptop_detail']); ?></td>
                                        <td class="p-4 text-center text-slate-500 font-semibold bg-slate-50/30"><?= date('d M Y', strtotime($qv['tanggal_booking'])); ?></td>
                                        <td class="p-4 text-center">
                                            <span class="px-2.5 py-1 rounded-md text-[9px] font-black uppercase tracking-wider border <?= $badge_class; ?>">
                                                <?= ($status_raw === 'PENDING_ADMIN') ? 'PENDING' : $status_raw; ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php 
                                    endwhile;
                                else: 
                                ?>
                                    <tr><td colspan="5" class="p-6 text-center text-slate-400 font-bold italic">Belum ada data pendaftaran unit yang masuk.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </main>

    <footer class="fixed bottom-0 right-0 left-0 bg-[#1e293b] text-slate-300 py-3 border-t border-slate-800 z-[-1] opacity-0">
        <div class="max-w-7xl mx-auto px-6 text-center text-[11px]">&copy; 2026 Hanbit. All rights reserved.</div>
    </footer>

    <script>
        function gantiModeFilter(modeValue) {
            document.getElementById('input_mode_filter').value = modeValue;
            const wrapBulan = document.getElementById('wrap_filter_bulan');
            if (modeValue === 'bulan') { wrapBulan.classList.remove('hidden'); } 
            else if (modeValue === 'tahun') { wrapBulan.classList.add('hidden'); }
            document.getElementById('form_filter_dasbor').submit();
        }

        document.addEventListener("DOMContentLoaded", function() {
            // RENDERING CHART 1: DOUGHNUT CHART VARIATION
            const ctx1 = document.getElementById('grafikPerbandinganBooking').getContext('2d');
            new Chart(ctx1, {
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
                        legend: { position: 'bottom', labels: { font: { family: 'Plus Jakarta Sans', size: 11, weight: '700' }, color: '#334155', padding: 15 } }
                    }
                }
            });

            // RENDERING CHART 2: 🔥 LIVE BAR CHART TOP 5 BRAND LAPTOP (NEW SCRIPT)
            const ctx2 = document.getElementById('grafikBatangBrandLaptop').getContext('2d');
            new Chart(ctx2, {
                type: 'bar',
                data: {
                    labels: <?= json_encode($labels_brand); ?>,
                    datasets: [{
                        label: 'Volume Kerusakan (Unit)',
                        data: <?= json_encode($data_brand_counts); ?>,
                        backgroundColor: '#1e293b',
                        hoverBackgroundColor: '#ffd54f',
                        borderRadius: 8,
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: { beginAtZero: true, ticks: { stepSize: 1, font: { family: 'Plus Jakarta Sans', size: 10, weight: '600' }, color: '#64748b' }, grid: { borderDash: [5, 5], color: '#e2e8f0' } },
                        x: { ticks: { font: { family: 'Plus Jakarta Sans', size: 10, weight: '800' }, color: '#334155' }, grid: { display: false } }
                    },
                    plugins: {
                        legend: { display: false },
                        tooltip: { callbacks: { label: function(context) { return ' Total: ' + context.raw + ' Unit Servis'; } } }
                    }
                }
            });
        });
    </script>
</body>

</html>