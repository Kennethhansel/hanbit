<?php
// Perbaikan path koneksi: admin berada satu tingkat di luar folder customer
require_once '../admin/koneksi.php';

// 🔥 TAMBAHAN LOGIKA: Ambil parameter status operasional DAN jam kerja dari database admin
$query_status = mysqli_query($koneksi, "SELECT status_toko, jam_buka_store, jam_tutup_store, pesan_penutupan FROM admin_accounts LIMIT 1");
$status_toko = mysqli_fetch_assoc($query_status);
$is_tutup = (isset($status_toko['status_toko']) && $status_toko['status_toko'] == 'tutup');
$pesan_tutup = $status_toko['pesan_penutupan'] ?? 'Maaf, toko kami saat ini sedang tutup.';

// Format tampilan jam operasional dari database (ambil format HH:MM)
$jam_buka = isset($status_toko['jam_buka_store']) ? substr($status_toko['jam_buka_store'], 0, 5) : '09:00';
$jam_tutup = isset($status_toko['jam_tutup_store']) ? substr($status_toko['jam_tutup_store'], 0, 5) : '18:00';

// 🔥 SINKRONISASI UTAMA: Ambil data seluruh paket maintenance secara live dari database terpusat
$query_paket = mysqli_query($koneksi, "SELECT * FROM master_packages ORDER BY id_paket ASC");
$list_paket = [];
while ($row_p = mysqli_fetch_assoc($query_paket)) {
    $list_paket[$row_p['kode_paket']] = $row_p;
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hanbit - Solusi Profesional Perawatan & Perbaikan Laptop</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght=400;500;600;700;800&display=swap');

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
    </style>
</head>

<body class="bg-white text-slate-800 antialiased">

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

    <section id="home" class="relative bg-[#0b1329] text-white pt-20 pb-40 z-10">
        <div class="absolute inset-0 opacity-15 bg-cover bg-center" style="background-image: url('images/utama.jpg?v=<?= time(); ?>');"></div>

        <div class="relative max-w-7xl mx-auto px-6 grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
            <div class="lg:col-span-10 space-y-6">

                <h1 class="flex flex-col text-3xl md:text-4xl lg:text-[3.25rem] font-extrabold uppercase tracking-tight leading-[1.1] lg:leading-[1.15]">
                    <span class="w-full">Solusi Profesional Untuk</span>
                    <span class="text-yellow-400 mt-1.5 block">Perawatan & Perbaikan</span>
                    <span class="text-white block mt-0.5">Laptop Anda</span>
                </h1>

                <div class="flex flex-wrap items-center gap-6 text-sm font-medium text-slate-300 pt-2">
                    <span class="flex items-center gap-2"><i class="fas fa-check-circle text-yellow-400"></i> Cepat</span>
                    <span class="flex items-center gap-2"><i class="fas fa-check-circle text-yellow-400"></i> Transparansi</span>
                    <span class="flex items-center gap-2"><i class="fas fa-check-circle text-yellow-400"></i> Bergaransi</span>
                </div>

                <div class="pt-4">
                    <a href="cek_estimasi.php" class="inline-block bg-yellow-400 hover:bg-yellow-500 text-slate-900 font-extrabold px-6 py-3.5 rounded-full text-sm transition shadow-lg shadow-yellow-400/20">
                        Cek Kerusakan & Estimasi Harga
                    </a>
                </div>
            </div>
        </div>

        <div class="absolute left-6 right-6 bottom-0 transform translate-y-1/2 max-w-4xl mx-auto z-30">
            <div class="bg-white text-slate-900 p-6 md:p-8 rounded-[2rem] shadow-2xl border border-gray-100">

                <?php if ($is_tutup): ?>
                    <div class="bg-rose-50 border border-rose-200 text-rose-700 p-4 rounded-2xl mb-4 text-center text-xs font-black uppercase tracking-wide animate-pulse">
                        <i class="fas fa-store-slash mr-1.5"></i> <?= htmlspecialchars($pesan_tutup); ?>
                    </div>
                <?php endif; ?>

                <div class="mb-4">
                    <h3 class="text-lg font-extrabold uppercase tracking-tight text-slate-900">Status Tracking</h3>
                    <p class="text-xs text-slate-400 font-medium">Lacak Progres Unit Service Anda</p>
                </div>

                <form action="status_tracking.php" method="GET" class="flex flex-col sm:flex-row gap-3">

                    <input type="text" name="invoice" placeholder="Masukkan Nomor Invoice Anda (Contoh: INV-20260608-XXXX)" required
                        <?= $is_tutup ? 'disabled' : ''; ?>
                        class="flex-1 px-5 py-3.5 bg-gray-50 border border-gray-200 rounded-2xl text-sm font-bold focus:bg-white focus:ring-2 focus:ring-yellow-400 outline-none transition <?= $is_tutup ? 'cursor-not-allowed opacity-50' : ''; ?>">

                    <button type="submit" <?= $is_tutup ? 'disabled' : ''; ?>
                        class="text-white font-bold text-xs uppercase px-8 py-4 rounded-2xl transition tracking-wider shrink-0 <?= $is_tutup ? 'bg-gray-400 cursor-not-allowed shadow-none' : 'bg-black hover:bg-slate-900'; ?>">
                        <?= $is_tutup ? 'Toko Tutup' : 'Lacak Status'; ?>
                    </button>
                </form>
            </div>
        </div>
    </section>

    <section id="layanan" class="pt-36 pb-20 bg-gray-50/50">
        <div class="max-w-7xl mx-auto px-6 text-center mb-12">
            <h2 class="text-3xl font-extrabold text-slate-900 tracking-tight mb-2">Layanan Kami</h2>
            <p class="text-xs text-slate-400 font-medium max-w-xl mx-auto">Kami menyediakan berbagai layanan service laptop dengan teknisi berpengalaman dan bergaransi.</p>
        </div>

        <div class="max-w-6xl mx-auto px-6 grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-gradient-to-br from-amber-50/40 to-yellow-50/20 p-6 rounded-3xl border border-yellow-100 flex items-start gap-5 shadow-sm">
                <div class="w-12 h-12 bg-amber-500 rounded-xl flex items-center justify-center text-white shrink-0 shadow-md shadow-amber-500/20">
                    <i class="fas fa-wrench text-lg"></i>
                </div>
                <div>
                    <h3 class="font-extrabold text-slate-900 text-base mb-1">Pergantian Hardware</h3>
                    <p class="text-xs text-slate-500 leading-relaxed font-medium">Replace keyboard, LCD, camera dan komponen lainnya</p>
                </div>
            </div>
            <div class="bg-gradient-to-br from-amber-50/40 to-yellow-50/20 p-6 rounded-3xl border border-yellow-100 flex items-start gap-5 shadow-sm">
                <div class="w-12 h-12 bg-amber-500 rounded-xl flex items-center justify-center text-white shrink-0 shadow-md shadow-amber-500/20">
                    <i class="fas fa-wrench text-lg"></i>
                </div>
                <div>
                    <h3 class="font-extrabold text-slate-900 text-base mb-1">Instalasi Software</h3>
                    <p class="text-xs text-slate-500 leading-relaxed font-medium">Install OS, aplikasi, driver, dan update sistem.</p>
                </div>
            </div>
            <div class="bg-gradient-to-br from-amber-50/40 to-yellow-50/20 p-6 rounded-3xl border border-yellow-100 flex items-start gap-5 shadow-sm">
                <div class="w-12 h-12 bg-amber-500 rounded-xl flex items-center justify-center text-white shrink-0 shadow-md shadow-amber-500/20">
                    <i class="fas fa-wrench text-lg"></i>
                </div>
                <div>
                    <h3 class="font-extrabold text-slate-900 text-base mb-1">Upgrade Komponen</h3>
                    <p class="text-xs text-slate-500 leading-relaxed font-medium">Upgrade RAM, SSD, HDD untuk performa maksimal.</p>
                </div>
            </div>
            <div class="bg-gradient-to-br from-amber-50/40 to-yellow-50/20 p-6 rounded-3xl border border-yellow-100 flex items-start gap-5 shadow-sm">
                <div class="w-12 h-12 bg-amber-500 rounded-xl flex items-center justify-center text-white shrink-0 shadow-md shadow-amber-500/20">
                    <i class="fas fa-wrench text-lg"></i>
                </div>
                <div>
                    <h3 class="font-extrabold text-slate-900 text-base mb-1">Maintenance</h3>
                    <p class="text-xs text-slate-500 leading-relaxed font-medium">Cleaning menyeluruh and perawatan rutin laptop.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="py-20 bg-gradient-to-t from-[#FFF9D7] to-white">
        <div class="max-w-7xl mx-auto px-6 text-center mb-16">
            <h2 class="text-3xl font-extrabold text-slate-900 tracking-tight mb-2">Paket Maintenance</h2>
            <p class="text-xs text-slate-400 font-medium max-w-xl mx-auto">Pilih paket maintenance sesuai kebutuhan laptop Anda. Harga terjangkau dengan kualitas terbaik!</p>
        </div>

        <div class="max-w-6xl mx-auto px-6 grid grid-cols-1 md:grid-cols-3 gap-8">

            <?php $p_basic = $list_paket['basic'] ?? ['nama_paket' => 'Paket Basic', 'harga_kantoran' => 75000, 'harga_gaming' => 100000, 'garansi' => 'Garansi 7 Hari', 'benefits' => '']; ?>
            <div class="bg-white border border-gray-200 rounded-[2rem] shadow-xl overflow-hidden flex flex-col justify-between">
                <div>
                    <div class="h-32 w-full overflow-hidden bg-slate-100">
                        <img src="images/paket-basic.jpg?v=<?= time(); ?>" alt="Paket Basic" class="w-full h-full object-cover">
                    </div>
                    <div class="p-6 text-center bg-[#1e293b] text-white">
                        <h3 class="text-base font-extrabold"><?= htmlspecialchars($p_basic['nama_paket']); ?></h3>
                        <p class="text-[11px] text-slate-300 mt-0.5">Cleaning & Pembersihan</p>
                    </div>
                    <div class="p-6 text-center border-b border-gray-100 bg-gray-50/50">
                        <p class="text-[11px] text-slate-400">Mulai dari <span class="text-2xl font-extrabold text-slate-900 block mt-1"><?= number_format($p_basic['harga_kantoran'] / 1000, 0); ?>K</span></p>
                        <p class="text-[10px] text-slate-400 font-bold mt-1">Laptop Kantoran</p>
                    </div>
                    <ul class="p-6 space-y-3.5 text-xs font-semibold text-slate-600">
                        <?php
                        $b_basic = explode("\n", str_replace("\r", "", $p_basic['benefits']));
                        foreach ($b_basic as $b) {
                            if (!empty(trim($b))) echo '<li class="flex items-start gap-2.5"><i class="fas fa-check-circle text-emerald-500 mt-0.5 shrink-0"></i> <span>' . htmlspecialchars($b) . '</span></li>';
                        }
                        ?>
                        <li class="flex items-center gap-2.5 text-slate-400 italic"><i class="fas fa-shield-alt text-amber-500"></i> Proteksi <?= htmlspecialchars($p_basic['garansi']); ?></li>
                    </ul>
                </div>
                <div class="p-6 pt-0 text-center">
                    <p class="text-[10px] font-bold text-slate-400 mb-4">Laptop Gaming: <span class="font-extrabold text-slate-800"><?= number_format($p_basic['harga_gaming'] / 1000, 0); ?>K</span></p>
                    <a href="buat_reservasi_paket.php?paket=basic" class="block w-full bg-black hover:bg-slate-900 text-white font-bold text-xs uppercase py-3.5 rounded-xl tracking-wider transition">Pilih Paket</a>
                </div>
            </div>

            <?php $p_std = $list_paket['standard'] ?? ['nama_paket' => 'Paket Standard', 'harga_kantoran' => 150000, 'harga_gaming' => 200000, 'garansi' => 'Garansi 14 Hari', 'benefits' => '']; ?>
            <div class="bg-white border-2 border-yellow-400 rounded-[2rem] shadow-2xl overflow-hidden flex flex-col justify-between relative transform lg:-translate-y-2">
                <span class="absolute top-3 right-3 bg-yellow-400 text-black text-[9px] font-black uppercase px-2.5 py-1 rounded-full tracking-wider z-10">POPULER</span>
                <div>
                    <div class="h-32 w-full overflow-hidden bg-slate-100">
                        <img src="images/paket-standard.jpg?v=<?= time(); ?>" alt="Paket Standard" class="w-full h-full object-cover">
                    </div>
                    <div class="p-6 text-center bg-yellow-500 text-slate-950">
                        <h3 class="text-base font-black"><?= htmlspecialchars($p_std['nama_paket']); ?></h3>
                        <p class="text-[11px] font-bold text-slate-800 mt-0.5">Repaste & Maintenance</p>
                    </div>
                    <div class="p-6 text-center border-b border-gray-100 bg-gray-50/50">
                        <p class="text-[11px] text-slate-400">Mulai dari <span class="text-2xl font-extrabold text-slate-900 block mt-1"><?= number_format($p_std['harga_kantoran'] / 1000, 0); ?>K</span></p>
                        <p class="text-[10px] text-slate-400 font-bold mt-1">Laptop Kantoran</p>
                    </div>
                    <ul class="p-6 space-y-3.5 text-xs font-semibold text-slate-700">
                        <?php
                        $b_std = explode("\n", str_replace("\r", "", $p_std['benefits']));
                        foreach ($b_std as $b) {
                            if (!empty(trim($b))) echo '<li class="flex items-start gap-2.5"><i class="fas fa-check-circle text-emerald-500 mt-0.5 shrink-0"></i> <span>' . htmlspecialchars($b) . '</span></li>';
                        }
                        ?>
                        <li class="flex items-center gap-2.5 text-slate-500 font-bold italic"><i class="fas fa-shield-alt text-yellow-600"></i> Proteksi <?= htmlspecialchars($p_std['garansi']); ?></li>
                    </ul>
                </div>
                <div class="p-6 pt-0 text-center">
                    <p class="text-[10px] font-bold text-slate-400 mb-4">Laptop Gaming: <span class="font-extrabold text-slate-800"><?= number_format($p_std['harga_gaming'] / 1000, 0); ?>K</span></p>
                    <a href="buat_reservasi_paket.php?paket=standard" class="block w-full bg-yellow-400 hover:bg-yellow-500 text-slate-955 font-black text-xs uppercase py-3.5 rounded-xl tracking-wider transition">Pilih Paket</a>
                </div>
            </div>

            <?php $p_prem = $list_paket['premium'] ?? ['nama_paket' => 'Paket Premium', 'harga_kantoran' => 250000, 'harga_gaming' => 250000, 'garansi' => 'Garansi 30 Hari', 'benefits' => '']; ?>
            <div class="bg-white border border-gray-200 rounded-[2rem] shadow-xl overflow-hidden flex flex-col justify-between">
                <div>
                    <div class="h-32 w-full overflow-hidden bg-slate-100">
                        <img src="images/paket-premium.jpg?v=<?= time(); ?>" alt="Paket Premium" class="w-full h-full object-cover">
                    </div>
                    <div class="p-6 text-center bg-[#1e293b] text-white">
                        <h3 class="text-base font-extrabold"><?= htmlspecialchars($p_prem['nama_paket']); ?></h3>
                        <p class="text-[11px] text-slate-300 mt-0.5">Full Optimization</p>
                    </div>
                    <div class="p-6 text-center border-b border-gray-100 bg-gray-50/50">
                        <p class="text-[11px] text-slate-400">Mulai dari <span class="text-2xl font-extrabold text-slate-900 block mt-1"><?= number_format($p_prem['harga_kantoran'] / 1000, 0); ?>K</span></p>
                        <p class="text-[10px] text-slate-400 font-bold mt-1">Laptop Kantoran</p>
                    </div>
                    <ul class="p-6 space-y-3.5 text-xs font-semibold text-slate-600">
                        <?php
                        $b_prem = explode("\n", str_replace("\r", "", $p_prem['benefits']));
                        foreach ($b_prem as $b) {
                            if (!empty(trim($b))) echo '<li class="flex items-start gap-2.5"><i class="fas fa-check-circle text-emerald-500 mt-0.5 shrink-0"></i> <span>' . htmlspecialchars($b) . '</span></li>';
                        }
                        ?>
                        <li class="flex items-center gap-2.5 text-slate-400 italic"><i class="fas fa-shield-alt text-amber-500"></i> Proteksi <?= htmlspecialchars($p_prem['garansi']); ?></li>
                    </ul>
                </div>
                <div class="p-6 pt-0 text-center">
                    <p class="text-[10px] font-bold text-slate-400 mb-4">Laptop Gaming: <span class="font-extrabold text-slate-800"><?= number_format($p_prem['harga_gaming'] / 1000, 0); ?>K</span></p>
                    <a href="buat_reservasi_paket.php?paket=premium" class="block w-full bg-black hover:bg-slate-900 text-white font-bold text-xs uppercase py-3.5 rounded-xl tracking-wider transition">Pilih Paket</a>
                </div>
            </div>
        </div>
    </section>

    <footer id="kontak" class="bg-[#1e293b] text-slate-300 pt-16 pb-8 border-t border-slate-800">
        <div class="max-w-7xl mx-auto px-6 grid grid-cols-1 md:grid-cols-12 gap-10 mb-12">
            <div class="md:col-span-5 space-y-4">
                <div class="flex items-center gap-3">
                    <img src="../admin/images/logo warna.png" alt="Logo Hanbit" class="w-8 h-8 object-contain">
                    <span class="text-lg font-black text-white tracking-tight">Hanbit</span>
                </div>
                <p class="text-xs text-slate-400 leading-relaxed font-medium max-w-sm">
                    Solusi profesional untuk perawatan dan perbaikan laptop Anda dengan garansi dan layanan terpercaya.
                </p>
            </div>
            <div class="md:col-span-4 space-y-3.5">
                <h4 class="text-white font-bold text-sm uppercase tracking-wider">Kontak</h4>
                <ul class="space-y-2.5 text-xs font-semibold text-slate-400">
                    <li class="flex items-center gap-3"><i class="fas fa-phone-alt text-yellow-400"></i> +62 851-5979-4427</li>
                    <li class="flex items-center gap-3"><i class="fas fa-envelope text-yellow-400"></i> hanbit0925@gmail.com</li>
                    <li class="flex items-start gap-3 border-b border-slate-800/60 pb-2.5"><i class="fas fa-map-marker-alt text-yellow-400 mt-0.5"></i> <span>Jl. Cihanjuang No.3 Kp. Centeng</span></li>
                    <li class="flex items-center gap-3 pt-1">
                        <i class="fab fa-instagram text-xl text-pink-500"></i>
                        <a href="https://instagram.com/hanbit.labs" target="_blank" class="text-slate-300 hover:text-yellow-400 font-bold transition">
                            @hanbit.labs
                        </a>
                    </li>
                </ul>
            </div>
            <div class="md:col-span-3 space-y-3.5">
                <h4 class="text-white font-bold text-sm uppercase tracking-wider">Jam Operasional</h4>
                <ul class="space-y-2 text-xs font-semibold text-slate-400">
                    <li>Senin - Jumat: <?= $jam_buka; ?> - <?= $jam_tutup; ?></li>
                    <li>Sabtu - Minggu: <?= $jam_buka; ?> - 15.00</li>
                </ul>
            </div>
        </div>
        <div class="max-w-7xl mx-auto px-6 pt-6 border-t border-slate-800/80 text-center text-[11px] font-medium text-slate-500">
            &copy; 2026 Hanbit. All rights reserved.
        </div>
    </footer>

</body>

</html>