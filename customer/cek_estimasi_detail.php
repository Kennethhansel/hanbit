<?php
// =========================================================================
// SISI BACKEND: PROSES CEK PARAMETER DAN KONEKSI DATABASE
// =========================================================================
$host = "localhost";
$user = "root";
$pass = "";
$db = "db_hanbit";
$koneksi = mysqli_connect($host, $user, $pass, $db);

if (!$koneksi) {
    die("Koneksi database Hanbit gagal: " . mysqli_connect_error());
}

$q_setting = mysqli_query($koneksi, "SELECT status_toko, max_kuota_harian FROM admin_accounts LIMIT 1");
$data_setting = mysqli_fetch_assoc($q_setting);

$is_tutup = (isset($data_setting['status_toko']) && $data_setting['status_toko'] == 'tutup');
$batas_kuota_maksimal = isset($data_setting['max_kuota_harian']) ? intval($data_setting['max_kuota_harian']) : 50;

$daftar_tanggal_penuh = [];
$q_cek_kuota = mysqli_query($koneksi, "SELECT tanggal_booking, COUNT(*) as total FROM reservations GROUP BY tanggal_booking HAVING total >= $batas_kuota_maksimal");
while ($row_kuota = mysqli_fetch_assoc($q_cek_kuota)) {
    $daftar_tanggal_penuh[] = $row_kuota['tanggal_booking'];
}

// =========================================================================
// 🔥 AMBIL DATA SECARA DINAMIS TERMASUK PENYEBAB & SARAN TERPISAH
// =========================================================================
$brand_id        = isset($_GET['brand_id']) ? mysqli_real_escape_string($koneksi, $_GET['brand_id']) : '';
$series_id       = isset($_GET['series_id']) ? mysqli_real_escape_string($koneksi, $_GET['series_id']) : '';
$id_masalah_raw  = isset($_GET['id_masalah']) ? mysqli_real_escape_string($koneksi, $_GET['id_masalah']) : '';
$masalah_custom  = isset($_GET['masalah_custom']) ? mysqli_real_escape_string($koneksi, $_GET['masalah_custom']) : '';

$array_id_masalah = !empty($id_masalah_raw) ? explode(',', $id_masalah_raw) : [];
$list_tampilan_analisa = [];
$total_estimasi_harga = 0;
$ada_masalah_custom = false;

$icon_pilihan = [
    1 => 'fa-power-off text-amber-500',
    2 => 'fa-desktop text-amber-500',
    3 => 'fa-keyboard text-amber-500',
    4 => 'fa-trash-alt text-amber-500',
    5 => 'fa-rocket text-amber-500',
    6 => 'fa-bolt text-amber-500',
    7 => 'fa-wifi text-amber-500'
];

foreach ($array_id_masalah as $id) {
    $id_clean = intval($id);
    if ($id_clean == 8) {
        $ada_masalah_custom = true;
        $list_tampilan_analisa[] = [
            'nama' => !empty($masalah_custom) ? $masalah_custom : 'Masalah Lainnya (Kustom)',
            'icon' => 'fa-bars text-amber-500',
            'is_custom' => true,
            'penyebab' => 'Belum terdeteksi, memerlukan cek komponen kelistrikan motherboard.',
            'saran' => 'Memerlukan pembongkaran casing dan diagnosa fisik langsung oleh teknisi Hanbit di workshop.'
        ];
    } else {
        $q_live = mysqli_query($koneksi, "SELECT * FROM master_masalah WHERE id_masalah = $id_clean LIMIT 1");
        $data_live = mysqli_fetch_assoc($q_live);

        if ($data_live) {
            $total_estimasi_harga += $data_live['harga_estimasi'];
            $icon_aktif = isset($icon_pilihan[$data_live['id_masalah']]) ? $icon_pilihan[$data_live['id_masalah']] : 'fa-tools text-amber-500';

            $list_tampilan_analisa[] = [
                'nama' => $data_live['nama_masalah'],
                'icon' => $icon_aktif,
                'is_custom' => false,
                'penyebab' => !empty($data_live['penyebab_masalah']) ? $data_live['penyebab_masalah'] : 'Terjadi keausan komponen pemakaian fisik unit berkala.',
                'saran' => !empty($data_live['saran_teknisi']) ? $data_live['saran_teknisi'] : 'Disarankan menyerahkan unit ke toko untuk pemeriksaan jalur.'
            ];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hanbit - Hasil Analisa Booking</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght=400;500;600;700;800&display=swap');

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
    </style>
</head>

<body class="bg-[#f8fafc] text-slate-900 antialiased min-h-screen flex flex-col justify-between">

    <nav class="bg-white border-b border-gray-100 sticky top-0 z-50 shadow-sm">
        <div class="max-w-7xl mx-auto px-6 h-20 flex justify-between items-center">
            <a href="index.php" class="flex items-center gap-3 hover:opacity-90 transition select-none">
                <img src="../admin/images/logo warna.png" alt="Logo Hanbit" class="w-10 h-10 object-contain">
                <span class="text-3xl font-extrabold tracking-tight text-slate-900">Hanbit</span>
            </a>
            <div class="hidden md:flex items-center gap-8 text-sm font-semibold text-slate-600">
                <a href="#home" class="text-yellow-600 hover:text-yellow-500 transition">Home</a>
                <a href="katalog.php" class="hover:text-slate-900 transition">Katalog</a>
                <a href="#layanan" class="hover:text-slate-900 transition">Layanan</a>
                <a href="portofolio.php" class="hover:text-slate-900 transition">Portofolio</a>
                <a href="#kontak" class="hover:text-slate-900 transition">Kontak</a>
            </div>
            <a href="https://wa.me/6285159794427" target="_blank" class="bg-[#00e676] hover:bg-[#00c853] text-white text-xs font-bold px-4 py-2.5 rounded-xl flex items-center gap-2 shadow-md shadow-emerald-500/10 transition">
                <i class="fab fa-whatsapp text-sm"></i> Chat Via WA
            </a>
        </div>
    </nav>

    <main class="max-w-5xl mx-auto w-full px-6 pt-10 pb-16 flex-1 flex flex-col justify-start gap-5">
        <div class="text-center space-y-1">
            <h1 class="text-3xl md:text-4xl font-extrabold tracking-tight text-slate-900">Hasil Analisa <span class="text-yellow-400">Hanbit</span></h1>
            <p class="text-sm text-slate-400 font-medium">Berikut lembar rincian diagnosa teknis dan estimasi biaya perbaikan Anda.</p>
        </div>

        <div class="flex flex-row flex-nowrap items-center justify-center gap-x-3 md:gap-x-5 text-[11px] md:text-xs font-black uppercase overflow-x-auto whitespace-nowrap py-2 select-none">
            <div class="text-slate-400 flex items-center gap-2 shrink-0">
                <span class="w-7 h-7 rounded-full bg-gray-200 text-slate-400 flex items-center justify-center font-black text-xs">1</span>
                <span>Pilih Merek</span>
            </div>
            <div class="h-[2px] w-6 md:w-10 bg-gray-200 shrink-0"></div>

            <div class="text-slate-400 flex items-center gap-2 shrink-0">
                <span class="w-7 h-7 rounded-full bg-gray-200 text-slate-400 flex items-center justify-center font-black text-xs">2</span>
                <span>Pilih Series</span>
            </div>
            <div class="h-[2px] w-6 md:w-10 bg-gray-200 shrink-0"></div>

            <div class="text-slate-400 flex items-center gap-2 shrink-0">
                <span class="w-7 h-7 rounded-full bg-gray-200 text-slate-400 flex items-center justify-center font-black text-xs">3</span>
                <span>Pilih Masalah</span>
            </div>
            <div class="h-[2px] w-6 md:w-10 bg-gray-200 shrink-0"></div>

            <div class="text-slate-900 flex items-center gap-2 shrink-0">
                <span class="w-7 h-7 rounded-full bg-[#facc15] text-slate-950 flex items-center justify-center font-black text-xs shadow-sm">4</span>
                <span>Detail & Book</span>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start pt-4">
            <div class="lg:col-span-7 space-y-5">
                <?php foreach ($list_tampilan_analisa as $analisa): ?>
                    <div class="bg-white border border-gray-100 rounded-[1.5rem] p-6 shadow-sm space-y-3">
                        <div class="flex items-center gap-3 border-b border-gray-50 pb-2">
                            <div class="w-10 h-10 rounded-xl bg-yellow-50/60 flex items-center justify-center">
                                <i class="fas <?= $analisa['icon']; ?> text-lg"></i>
                            </div>
                            <h2 class="text-base font-extrabold text-slate-800"><?= htmlspecialchars($analisa['nama']); ?></h2>
                        </div>

                        <div class="bg-blue-50/40 rounded-xl p-3.5 border border-blue-100/50">
                            <p class="text-[10px] font-black text-blue-600 uppercase tracking-wider mb-1"><i class="fas fa-search-minus"></i> Analisa Penyebab Kerusakan</p>
                            <p class="text-[11px] text-slate-600 font-medium leading-relaxed"><?= htmlspecialchars($analisa['penyebab']); ?></p>
                        </div>

                        <div class="bg-emerald-50/40 rounded-xl p-3.5 border border-emerald-100/50">
                            <p class="text-[10px] font-black text-emerald-600 uppercase tracking-wider mb-1"><i class="fas fa-check-circle"></i> Saran Tindakan Solusi</p>
                            <p class="text-[11px] text-slate-600 font-medium leading-relaxed"><?= htmlspecialchars($analisa['saran']); ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>

                <div class="bg-slate-900 rounded-[1.5rem] p-6 text-white flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 shadow-md">
                    <div>
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Total Estimasi Harga</p>
                        <h2 class="text-3xl font-black text-[#facc15] mt-1">
                            <?= $ada_masalah_custom && $total_estimasi_harga == 0 ? "Berdasarkan Cek Fisik" : "Rp " . number_format($total_estimasi_harga, 0, ',', '.'); ?>
                        </h2>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-5 bg-white border-2 border-yellow-400 rounded-[1.5rem] p-6 shadow-sm">
                <h2 class="text-lg font-black text-slate-800 mb-4 flex items-center gap-2">Lengkapi Booking</h2>

                <form action="proses_booking.php" method="POST" onsubmit="return cekFinalKetersediaan(this)" class="space-y-4">
                    <input type="hidden" name="proses_simpan_estimasi" value="1">
                    <input type="hidden" name="brand_id" value="<?= htmlspecialchars($brand_id); ?>">
                    <input type="hidden" name="series_id" value="<?= htmlspecialchars($series_id); ?>">
                    <input type="hidden" name="id_masalah" value="<?= htmlspecialchars($id_masalah_raw); ?>">
                    <input type="hidden" name="masalah_custom" value="<?= htmlspecialchars($masalah_custom); ?>">
                    <input type="hidden" name="total_harga_final" value="<?= $total_estimasi_harga; ?>">

                    <div class="space-y-1">
                        <label class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider">Nama Lengkap</label>
                        <input type="text" name="nama_lengkap" required class="w-full bg-slate-50 border border-slate-100 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-yellow-400 focus:bg-white transition">
                    </div>

                    <div class="space-y-1">
                        <label class="text-[10px] font-extrabold text-slate-400 tracking-wider uppercase block">WhatsApp</label>
                        <input type="tel" name="whatsapp" required placeholder="08xxxxxxxxxx" class="w-full bg-slate-50 border border-slate-100 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-yellow-400 focus:bg-white transition">
                    </div>

                    <div class="space-y-1">
                        <label class="text-[10px] font-extrabold text-slate-400 tracking-wider uppercase block">Alamat Email</label>
                        <input type="email" name="email" required placeholder="contoh@email.com" class="w-full bg-slate-50 border border-slate-100 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-yellow-400 focus:bg-white transition">
                    </div>

                    <div class="space-y-1">
                        <label class="text-[10px] font-extrabold text-slate-400 tracking-wider uppercase block">Alamat Lengkap Anda</label>
                        <textarea name="alamat_lengkap" rows="2" required placeholder="Tuliskan alamat rumah lengkap anda..." class="w-full bg-slate-50 border border-slate-100 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-yellow-400 focus:bg-white transition resize-none"></textarea>
                    </div>

                    <div class="space-y-1">
                        <label class="text-[10px] font-extrabold text-slate-400 tracking-wider uppercase block">Tanggal Menyerahkan</label>
                        <input type="date" name="tanggal_menyerahkan" id="input_tanggal" onchange="periksaKuotaTanggal(this.value)" required class="w-full bg-slate-50 border border-slate-100 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-yellow-400 focus:bg-white transition">
                        <p id="notif_kuota" class="text-[10px] font-bold mt-1 hidden"></p>
                    </div>

                    <div class="flex items-center gap-2 pt-2">
                        <a href="cek_estimasi_masalah.php?brand_id=<?= urlencode($brand_id); ?>&series_id=<?= urlencode($series_id); ?>"
                            class="bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold text-[11px] uppercase px-4 py-4 rounded-xl flex items-center justify-center gap-1.5 transition select-none border border-slate-200">
                            <i class="fas fa-chevron-left text-[9px]"></i> Kembali
                        </a>

                        <button type="submit" id="btn_submit_booking" <?= $is_tutup ? 'disabled' : ''; ?>
                            class="flex-1 font-black text-xs uppercase py-4 rounded-xl flex items-center justify-center gap-2 tracking-wide transition shadow-sm <?= $is_tutup ? 'bg-gray-300 text-slate-400 cursor-not-allowed shadow-none' : 'bg-[#ffd54f] hover:bg-[#ffca28] text-slate-900'; ?>">
                            <?= $is_tutup ? 'Toko Tutup' : 'BOOKING SEKARANG'; ?> <i class="fas fa-calendar-check text-sm"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </main>

    <footer class="bg-[#1e293b] text-slate-300 pt-12 pb-6 border-t border-slate-800">
        <div class="max-w-7xl mx-auto px-6 text-center text-[11px] font-medium text-slate-500">
            © 2026 Hanbit. All rights reserved.
        </div>
    </footer>

    <script>
        const daftarTanggalPenuh = <?= json_encode($daftar_tanggal_penuh); ?>;
        let statusTanggalAman = true;

        document.addEventListener("DOMContentLoaded", function() {
            document.getElementById('input_tanggal').setAttribute('min', new Date().toISOString().split('T')[0]);
        });

        function periksaKuotaTanggal(tanggalValue) {
            const labelNotif = document.getElementById('notif_kuota');
            const btnSubmit = document.getElementById('btn_submit_booking');
            if (btnSubmit.hasAttribute('disabled')) return;
            if (tanggalValue === '') return;

            if (daftarTanggalPenuh.includes(tanggalValue)) {
                statusTanggalAman = false;
                labelNotif.innerText = "❌ Kuota penuh! Slot pengerjaan toko pada tanggal ini sudah mencapai batas maksimum.";
                labelNotif.classList.remove('hidden', 'text-emerald-500');
                labelNotif.classList.add('text-rose-500');
                btnSubmit.classList.add('opacity-50', 'cursor-not-allowed');
            } else {
                statusTanggalAman = true;
                labelNotif.innerText = "✅ Kuota tersedia! Slot pengerjaan teknisi Hanbit Labs aman.";
                labelNotif.classList.remove('hidden', 'text-rose-500');
                labelNotif.classList.add('text-emerald-500');
                btnSubmit.classList.remove('opacity-50', 'cursor-not-allowed');
            }
        }

        function cekFinalKetersediaan(form) {
            if (!statusTanggalAman) {
                alert("Mohon pilih tanggal penyerahan lain, kuota pengerjaan teknisi untuk tanggal ini sudah penuh!");
                return false;
            }
            return true;
        }
    </script>
</body>

</html>