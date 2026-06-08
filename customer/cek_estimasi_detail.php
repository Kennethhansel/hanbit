<?php
// =========================================================================
// SISI BACKEND: PROSES CEK PARAMETER DAN KONEKSI DATABASE
// =========================================================================
$host = "localhost"; $user = "root"; $pass = ""; $db = "db_hanbit";
$koneksi = mysqli_connect($host, $user, $pass, $db);

if (!$koneksi) { die("Koneksi database Hanbit gagal: " . mysqli_connect_error()); }

// AMBIL LIVE SAKELAR STATUS OPERASIONAL TOKO
$query_status = mysqli_query($koneksi, "SELECT status_toko FROM admin_accounts LIMIT 1");
$status_toko = mysqli_fetch_assoc($query_status);
$is_tutup = (isset($status_toko['status_toko']) && $status_toko['status_toko'] == 'tutup');

$brand_id       = isset($_GET['brand_id']) ? mysqli_real_escape_string($koneksi, $_GET['brand_id']) : '1';
$series_id      = isset($_GET['series_id']) ? mysqli_real_escape_string($koneksi, $_GET['series_id']) : '';
$id_masalah_raw = isset($_GET['id_masalah']) ? $_GET['id_masalah'] : ''; 
$masalah_custom = isset($_GET['masalah_custom']) ? mysqli_real_escape_string($koneksi, $_GET['masalah_custom']) : '';

// REVISI PARAMETER: Menggunakan nilai estimasi tengah (rata-rata biaya perbaikan riil)
$master_masalah = [
    '1' => ['nama' => 'Mati Total (No Power)', 'icon' => 'fa-power-off text-amber-500', 'penyebab' => 'Short pada jalur power IC, chipset, atau tegangan tidak stabil.', 'saran' => 'Memerlukan pengecekan jalur VCC pada motherboard.', 'harga' => 650000],
    '2' => ['nama' => 'Layar Bermasalah', 'icon' => 'fa-desktop text-amber-500', 'penyebab' => 'Layar pecah, bergaris, dead pixel, atau kabel fleksibel display.', 'saran' => 'Disarankan melakukan penggantian panel LCD baru original.', 'harga' => 750000],
    '3' => ['nama' => 'Keyboard Error/Macet', 'icon' => 'fa-keyboard text-amber-500', 'penyebab' => 'Terkena tumpahan cairan, lembab, atau korosi jalur.', 'saran' => 'Saran penggantian unit keyboard baru (Replacement).', 'harga' => 250000],
    '4' => ['nama' => 'Laptop Lemot', 'icon' => 'fa-trash-alt text-amber-500', 'penyebab' => 'OS korup, debu menumpuk, atau thermal paste kering.', 'saran' => 'Pembersihan debu total, ganti thermal paste, serta reinstal OS.', 'harga' => 175000],
    '5' => ['nama' => 'Upgrade Hardware', 'icon' => 'fa-rocket text-amber-500', 'penyebab' => 'Kapasitas RAM atau storage bawaan laptop sudah terlalu kecil.', 'saran' => 'Disarankan upgrade menambah kapasitas RAM atau beralih ke SSD.', 'harga' => 450000],
    '6' => ['nama' => 'Tidak Bisa Charge', 'icon' => 'fa-bolt text-amber-500', 'penyebab' => 'Baterai drop, sirkuit charging bermasalah, atau jack dc longgar.', 'saran' => 'Perlu konektor charger atau penggantian unit baterai baru.', 'harga' => 300000],
    '7' => ['nama' => 'Masalah Wifi', 'icon' => 'fa-wifi text-amber-500', 'penyebab' => 'Driver crash, antena internal putus, atau modul card rusak.', 'saran' => 'Perlu update software driver atau penggantian modul wifi.', 'harga' => 175000],
];

$array_id_masalah = !empty($id_masalah_raw) ? explode(',', $id_masalah_raw) : [];
$list_tampilan_analisa = [];
$total_estimasi_harga = 0;
$ada_masalah_custom = false;

foreach ($array_id_masalah as $id) {
    if ($id == '8') {
        $ada_masalah_custom = true;
        $list_tampilan_analisa[] = [
            'nama' => !empty($masalah_custom) ? $masalah_custom : 'Masalah Lainnya (Kustom)',
            'icon' => 'fa-bars text-amber-500',
            'is_custom' => true,
            'penyebab' => '-',
            'saran' => 'Memerlukan pengecekan dan diagnosa fisik langsung oleh teknisi Hanbit di toko.',
            'harga' => 0
        ];
    } elseif (isset($master_masalah[$id])) {
        $total_estimasi_harga += $master_masalah[$id]['harga'];
        $list_tampilan_analisa[] = [
            'nama' => $master_masalah[$id]['nama'],
            'icon' => $master_masalah[$id]['icon'],
            'is_custom' => false,
            'penyebab' => $master_masalah[$id]['penyebab'],
            'saran' => $master_masalah[$id]['saran'],
            'harga' => $master_masalah[$id]['harga']
        ];
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
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-[#f8fafc] text-slate-900 antialiased min-h-screen flex flex-col justify-between">

    <nav class="bg-white border-b border-gray-100 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-6 h-20 flex justify-between items-center">
            <a href="index.php" class="flex items-center gap-3 hover:opacity-90 transition select-none">
                <img src="../admin/images/logo warna.png" alt="Logo Hanbit" class="w-10 h-10 object-contain">
                <span class="text-3xl font-extrabold tracking-tight text-slate-900">Hanbit</span>
            </a>
            <div class="hidden md:flex items-center gap-8 text-sm font-semibold text-slate-600">
                <a href="index.php" class="hover:text-yellow-600 transition">Home</a>
                <a href="katalog.php" class="hover:text-slate-900 transition">Katalog</a>
                <a href="index.php#layanan" class="hover:text-slate-900 transition">Layanan</a>
                <a href="index.php#kontak" class="hover:text-slate-900 transition">Kontak</a>
            </div>
            <a href="https://wa.me/6285159794427" target="_blank" class="bg-[#00e676] hover:bg-[#00c853] text-white text-xs font-bold px-4 py-2.5 rounded-xl flex items-center gap-2 shadow-sm transition">
                <i class="fab fa-whatsapp text-sm"></i> Chat Via WA
            </a>
        </div>
    </nav>

    <main class="max-w-5xl mx-auto w-full px-6 pt-10 pb-16 flex-1 flex flex-col justify-start gap-5">
        <div class="text-center space-y-1">
            <h1 class="text-3xl md:text-4xl font-extrabold tracking-tight text-slate-900">Hasil Analisa <span class="text-yellow-400">Hanbit</span></h1>
            <p class="text-sm text-slate-400 font-medium">Berikut detail teknis dan estimasi biaya perbaikan Anda.</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            <div class="lg:col-span-7 space-y-6">
                <?php foreach ($list_tampilan_analisa as $analisa): ?>
                    <div class="bg-white border border-gray-100 rounded-[1.5rem] p-6 shadow-sm space-y-4">
                        <div class="flex items-center gap-3 border-b border-gray-50 pb-3">
                            <div class="w-10 h-10 rounded-xl bg-yellow-50/60 flex items-center justify-center">
                                <i class="fas <?= $analisa['icon']; ?> text-lg"></i>
                            </div>
                            <h2 class="text-base font-extrabold text-slate-800"><?= htmlspecialchars($analisa['nama']); ?></h2>
                        </div>
                        <div class="bg-slate-50/50 rounded-xl p-4 border border-gray-50">
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Saran Perbaikan</p>
                            <p class="text-[11px] text-slate-500 font-medium leading-relaxed"><?= htmlspecialchars($analisa['saran']); ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>

                <div class="bg-[#1e293b] rounded-[1.5rem] p-6 text-white flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 shadow-md">
                    <div>
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Total Acuan Estimasi Tengah</p>
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
                        <label class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider">WhatsApp</label>
                        <input type="tel" name="whatsapp" required placeholder="08xxxxxxxxxx" class="w-full bg-slate-50 border border-slate-100 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-yellow-400 focus:bg-white transition">
                    </div>

                    <div class="space-y-1">
                        <label class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider">Alamat Email</label>
                        <input type="email" name="email" required placeholder="contoh@email.com" class="w-full bg-slate-50 border border-slate-100 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-yellow-400 focus:bg-white transition">
                    </div>

                    <div class="space-y-1">
                        <label class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider">Alamat Lengkap Anda</label>
                        <textarea name="alamat_lengkap" rows="2" required placeholder="Tuliskan alamat rumah lengkap anda..." class="w-full bg-slate-50 border border-slate-100 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-yellow-400 focus:bg-white transition resize-none"></textarea>
                    </div>

                    <div class="space-y-1">
                        <label class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider">Tanggal Menyerahkan</label>
                        <input type="date" name="tanggal_menyerahkan" id="input_tanggal" onchange="periksaKuotaTanggal(this.value)" required class="w-full bg-slate-50 border border-slate-100 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-yellow-400 focus:bg-white transition">
                        <p id="notif_kuota" class="text-[10px] font-bold mt-1 hidden"></p>
                    </div>

                    <div class="flex gap-3 pt-2">
                        <button type="submit" id="btn_submit_booking" <?= $is_tutup ? 'disabled' : ''; ?>
                            class="w-full font-black text-xs uppercase py-4 rounded-xl flex items-center justify-center gap-2 tracking-wide transition shadow-sm <?= $is_tutup ? 'bg-gray-300 text-slate-400 cursor-not-allowed shadow-none' : 'bg-[#ffd54f] hover:bg-[#ffca28] text-slate-900'; ?>">
                            <?= $is_tutup ? 'Toko Sedang Tutup' : 'BOOKING SEKARANG'; ?> <i class="fas fa-calendar-check text-sm"></i>
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
        const daftarTanggalPenuh = ['2026-06-05', '2026-06-10'];
        let statusTanggalAman = true;

        document.addEventListener("DOMContentLoaded", function() {
            document.getElementById('input_tanggal').setAttribute('min', new Date().toISOString().split('T')[0]);
        });

        function periksaKuotaTanggal(tanggalValue) {
            const labelNotif = document.getElementById('notif_kuota');
            const btnSubmit = document.getElementById('btn_submit_booking');
            if (btnSubmit.hasAttribute('disabled')) return;

            if (daftarTanggalPenuh.includes(tanggalValue)) {
                statusTanggalAman = false;
                labelNotif.innerText = "❌ Kuota penuh! Pilih tanggal lain.";
                labelNotif.classList.remove('hidden', 'text-emerald-500'); labelNotif.classList.add('text-rose-500');
                btnSubmit.classList.add('opacity-50', 'cursor-not-allowed');
            } else {
                statusTanggalAman = true;
                labelNotif.innerText = "✅ Kuota tersedia!";
                labelNotif.classList.remove('hidden', 'text-rose-500'); labelNotif.classList.add('text-emerald-500');
                btnSubmit.classList.remove('opacity-50', 'cursor-not-allowed');
            }
        }

        function cekFinalKetersediaan(form) {
            if (!statusTanggalAman) { alert("Tanggal penuh!"); return false; }
            return true;
        }
    </script>
</body>
</html>