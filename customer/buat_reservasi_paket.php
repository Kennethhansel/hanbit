<?php
// =========================================================================
// SISI BACKEND: AMBIL DATA DARI TABEL MASTER_PACKAGES SECARA DINAMIS
// =========================================================================
require_once '../admin/koneksi.php';

// 🔥 TAMBAHAN LOGIKA: Ambil status toko dari database untuk mencegah input order saat libur/tutup
$query_status = mysqli_query($koneksi, "SELECT status_toko FROM admin_accounts LIMIT 1");
$status_toko = mysqli_fetch_assoc($query_status);
$is_tutup = (isset($status_toko['status_toko']) && $status_toko['status_toko'] == 'tutup');

$paket_pilihan = isset($_GET['paket']) ? trim(htmlspecialchars($_GET['paket'])) : 'basic';

// Tarik data konfigurasi paket dari database riil
$query_pkg = mysqli_query($koneksi, "SELECT * FROM master_packages WHERE kode_paket = '" . mysqli_real_escape_string($koneksi, $paket_pilihan) . "' LIMIT 1");
$data_pkg = mysqli_fetch_assoc($query_pkg);

// Jika di database belum di-insert datanya oleh admin, gunakan fallback aman agar halaman tidak crash saat demo
if (!$data_pkg) {
    if ($paket_pilihan == 'standard') {
        $nama_paket_id = "Paket Standard (Repaste & Maintenance)"; $harga_kantoran = 150000; $harga_gaming = 200000; $garansi = "Garansi 14 Hari";
        $benefits_raw = "Pembersihan menyeluruh debu & kotoran pada komponen internal\nPembersihan sasis luar, sela-sela keyboard, dan layar laptop\nPenggantian Thermal Paste Premium untuk menurunkan suhu panas\nPembersihan mendalam pada bilah kipas (fan) & jalur heatsink\nPengecekan kesehatan hardware dasar & stabilitas software";
    } elseif ($paket_pilihan == 'premium') {
        $nama_paket_id = "Paket Premium (Full Optimization)"; $harga_kantoran = 250000; $harga_gaming = 250000; $garansi = "Garansi 30 Hari";
        $benefits_raw = "Semua layanan pembersihan mendalam pada Paket Standard\nPenggantian Thermal Paste performa tinggi (High-Performance)\nOptimasi penuh kecepatan sistem operasi (OS) agar anti-lemot\nPembaruan (Update) driver hardware dan aplikasi esensial\nLayanan Install Ulang OS Windows secara bersih (Optional)";
    } else {
        $paket_pilihan = 'basic'; $nama_paket_id = "Paket Basic (Cleaning & Pembersihan)"; $harga_kantoran = 75000; $harga_gaming = 100000; $garansi = "Garansi 7 Hari";
        $benefits_raw = "Pembersihan debu & kotoran sasis bagian internal laptop\nPembersihan eksternal pada sela-sela tombol keyboard\nPembersihan bercak noda dan debu pada permukaan layar\nPelumasan poros kipas laptop agar putaran kembali senyap";
    }
    $benefits_detail = explode("\n", $benefits_raw);
} else {
    $nama_paket_id = $data_pkg['nama_paket'];
    $harga_kantoran = $data_pkg['harga_kantoran'];
    $harga_gaming = $data_pkg['harga_gaming'];
    $garansi = $data_pkg['garansi'];
    $benefits_detail = explode("\n", str_replace("\r", "", $data_pkg['benefits']));
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hanbit - Detail & Book Paket Maintenance</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght=400;500;600;700;800&display=swap');
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
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

    <main class="max-w-6xl mx-auto w-full px-6 pt-10 pb-16 flex-1 flex flex-col justify-start gap-8">
        <div class="text-center space-y-1">
            <h1 class="text-3xl md:text-4xl font-extrabold tracking-tight text-slate-900">Detail Paket <span class="text-yellow-400">Perawatan</span></h1>
            <p class="text-xs text-slate-400 font-medium tracking-wide">Berikut rincian lengkap pengerjaan maintenance dan biaya flat paket unit Anda.</p>
        </div>

        <form action="proses_booking.php" method="POST" class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            <input type="hidden" name="proses_simpan_paket" value="1">
            <input type="hidden" name="paket_tipe" value="<?= htmlspecialchars($paket_pilihan); ?>">
            <input type="hidden" name="segmen_laptop" id="input_segmen_laptop" value="kantoran">
            <input type="hidden" name="total_harga_final" id="input_harga_final" value="<?= $harga_kantoran; ?>">

            <div class="lg:col-span-7 space-y-6">
                <div class="bg-white border border-gray-100 p-6 md:p-8 rounded-[1.5rem] shadow-sm space-y-6">
                    <div class="flex items-center gap-3 border-b border-gray-50 pb-4">
                        <div class="w-10 h-10 bg-amber-50 rounded-xl flex items-center justify-center text-amber-500"><i class="fas fa-tools text-base"></i></div>
                        <div>
                            <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider block">Jenis Perawatan Terpilih</span>
                            <h3 class="text-base font-black text-slate-900 uppercase tracking-tight"><?= $nama_paket_id; ?></h3>
                        </div>
                    </div>
                    <div class="space-y-3.5">
                        <ul class="space-y-3 text-xs text-slate-600 font-medium">
                            <?php foreach($benefits_detail as $benefit): ?>
                                <?php if(empty(trim($benefit))) continue; ?>
                                <li class="flex items-start gap-3 leading-tight">
                                    <i class="fas fa-check-circle text-emerald-500 mt-0.5 shrink-0 text-sm"></i>
                                    <span><?= htmlspecialchars($benefit); ?></span>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <div class="space-y-2 pt-4 border-t border-gray-50">
                        <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Pilih Segmen Spesifikasi Unit Laptop:</label>
                        <div class="grid grid-cols-2 gap-4">
                            <div onclick="pilihSegmen('kantoran', <?= $harga_kantoran; ?>)" id="segmen_kantoran" class="border-2 border-yellow-400 bg-yellow-50/10 p-4 rounded-xl cursor-pointer text-center select-none">
                                <i class="fas fa-briefcase text-slate-700 text-base mb-1 block"></i>
                                <span class="text-xs font-extrabold text-slate-900 block">Laptop Kantoran</span>
                            </div>
                            <div onclick="pilihSegmen('gaming', <?= $harga_gaming; ?>)" id="segmen_gaming" class="border border-slate-200 bg-white p-4 rounded-xl cursor-pointer text-center select-none">
                                <i class="fas fa-gamepad text-slate-400 text-base mb-1 block"></i>
                                <span class="text-xs font-bold text-slate-500 block">Laptop Gaming</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-[#1a233a] text-white p-6 md:p-8 rounded-[1.5rem] shadow-xl flex flex-col sm:flex-row justify-between sm:items-center gap-4">
                    <div>
                        <span class="text-[9px] font-bold text-yellow-400 uppercase tracking-wider block">TOTAL BIAYA</span>
                        <h2 class="text-3xl md:text-4xl font-black text-white" id="text_harga_total">Rp <?= number_format($harga_kantoran, 0, ',', '.'); ?></h2>
                    </div>
                    <div class="space-y-1 text-[11px] font-semibold text-slate-300">
                        <div><i class="fas fa-check-circle text-emerald-400 text-xs"></i> <span id="text_garansi_label"><?= $garansi; ?></span></div>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-5 bg-white border-2 border-[#ffd54f] p-6 md:p-7 rounded-[1.5rem] shadow-lg space-y-5">
                <div class="space-y-1.5">
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">NAMA LENGKAP</label>
                    <input type="text" name="nama_pelanggan" required class="w-full px-4 py-3 bg-slate-50 border border-slate-200/60 rounded-xl text-sm font-medium focus:outline-none focus:border-yellow-400 focus:bg-white transition">
                </div>
                <div class="space-y-1.5">
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">WHATSAPP</label>
                    <input type="tel" name="whatsapp" placeholder="08xxxxxxxxxx" required class="w-full px-4 py-3 bg-slate-50 border border-slate-200/60 rounded-xl text-sm font-medium focus:outline-none focus:border-yellow-400 focus:bg-white transition">
                </div>
                <div class="space-y-1.5">
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">ALAMAT EMAIL</label>
                    <input type="email" name="email" placeholder="contoh@email.com" required class="w-full px-4 py-3 bg-slate-50 border border-slate-200/60 rounded-xl text-sm font-medium focus:outline-none focus:border-yellow-400 focus:bg-white transition">
                </div>
                <div class="space-y-1.5">
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">MEREK & SERI LAPTOP</label>
                    <input type="text" name="laptop_detail" placeholder="Contoh: ASUS ROG Strix G15" required class="w-full px-4 py-3 bg-slate-50 border border-slate-200/60 rounded-xl text-sm font-medium focus:outline-none focus:border-yellow-400 focus:bg-white transition">
                </div>
                <div class="space-y-1.5">
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">ALAMAT LENGKAP</label>
                    <textarea name="alamat_lengkap" rows="2" placeholder="Alamat lengkap anda..." required class="w-full px-4 py-3 bg-slate-50 border border-slate-200/60 rounded-xl text-sm font-medium focus:outline-none focus:border-yellow-400 focus:bg-white transition resize-none"></textarea>
                </div>
                <div class="space-y-1.5">
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">TANGGAL MENYERAHKAN UNIT</label>
                    <input type="date" name="tanggal_booking" id="tanggal_booking" required class="w-full px-4 py-3 bg-slate-50 border border-slate-200/60 rounded-xl text-sm focus:outline-none text-slate-600 font-medium">
                </div>
                
                <div class="flex gap-3 pt-2">
                    <button type="submit" <?= $is_tutup ? 'disabled' : ''; ?>
                        class="w-full text-slate-900 font-black text-xs uppercase py-3.5 rounded-xl tracking-wider transition shadow-sm flex items-center justify-center gap-2 <?= $is_tutup ? 'bg-gray-300 text-slate-400 cursor-not-allowed shadow-none' : 'bg-[#ffd54f] hover:bg-[#ffca28]'; ?>">
                        <?= $is_tutup ? 'Toko Sedang Tutup' : 'BOOKING SEKARANG'; ?> <i class="fas fa-calendar-check text-xs"></i>
                    </button>
                </div>
            </div>
        </form>
    </main>

    <footer class="bg-[#1e293b] text-slate-300 pt-12 pb-6 border-t border-slate-800">
        <div class="max-w-7xl mx-auto px-6 text-center text-[11px] font-medium text-slate-500">
            © 2026 Hanbit. All rights reserved.
        </div>
    </footer>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            document.getElementById('tanggal_booking').setAttribute('min', new Date().toISOString().split('T')[0]);
        });
        function pilihSegmen(tipe, harga) {
            document.getElementById('input_segmen_laptop').value = tipe;
            document.getElementById('input_harga_final').value = harga;
            const boxKantoran = document.getElementById('segmen_kantoran');
            const boxGaming = document.getElementById('segmen_gaming');
            boxKantoran.className = "border border-slate-200 bg-white p-4 rounded-xl cursor-pointer text-center select-none";
            boxGaming.className = "border border-slate-200 bg-white p-4 rounded-xl cursor-pointer text-center select-none";
            if (tipe === 'kantoran') {
                boxKantoran.className = "border-2 border-yellow-400 bg-yellow-50/10 p-4 rounded-xl cursor-pointer text-center select-none";
            } else {
                boxGaming.className = "border-2 border-yellow-400 bg-yellow-50/10 p-4 rounded-xl cursor-pointer text-center select-none";
            }
            document.getElementById('text_harga_total').innerText = "Rp " + harga.toLocaleString('id-ID');
        }
    </script>
</body>
</html>