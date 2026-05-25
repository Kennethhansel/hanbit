<?php
require_once '../koneksi.php';

// Ambil kuota harian dari database untuk informasi dinamis bagi customer
$query_kuota = "SELECT Setting_Value FROM system_settings WHERE Setting_Key = 'max_kuota_harian' LIMIT 1";
$result_kuota = mysqli_query($koneksi, $query_kuota);
$setting = mysqli_fetch_assoc($result_kuota);
$kuota_maksimal = $setting['Setting_Value'] ?? 5;
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
            <div class="flex items-center gap-3">
                <img src="../logo warna.png" alt="Logo Hanbit" class="w-10 h-10 object-contain">
                <span class="text-3xl font-extrabold text-slate-900 tracking-tight">Hanbit</span>
            </div>
            <div class="hidden md:flex items-center gap-8 text-sm font-semibold text-slate-600">
                <a href="#home" class="text-yellow-600 hover:text-yellow-500 transition">Home</a>
                <a href="#layanan" class="hover:text-slate-900 transition">Katalog</a>
                <a href="#layanan" class="hover:text-slate-900 transition">Layanan</a>
                <a href="#kontak" class="hover:text-slate-900 transition">Kontak</a>
            </div>
            <a href="https://wa.me/6285159794427" target="_blank" class="bg-[#00e676] hover:bg-[#00c853] text-white text-xs font-bold px-4 py-2.5 rounded-xl flex items-center gap-2 shadow-md shadow-emerald-500/10 transition">
                <i class="fab fa-whatsapp text-sm"></i> Chat Via WA
            </a>
        </div>
    </nav>

    <section id="home" class="relative bg-[#0b1329] text-white pt-20 pb-40 z-10">
        <div class="absolute inset-0 opacity-15 bg-cover bg-center" style="background-image: url('images/utama.jpg');"></div>

        <div class="relative max-w-7xl mx-auto px-6 grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
            <div class="lg:col-span-8 space-y-6">
                <div class="inline-flex items-center gap-2 bg-slate-800/60 border border-slate-700/50 rounded-full px-4 py-1.5 shadow-inner">
                    <span class="w-2 h-2 bg-emerald-400 rounded-full animate-pulse"></span>
                    <p class="text-[11px] font-bold text-slate-300 uppercase tracking-wider">Slot Terbatas: Maksimal <?= $kuota_maksimal; ?> Laptop / Hari</p>
                </div>

                <h1 class="text-4xl md:text-5xl lg:text-[3.25rem] font-extrabold uppercase tracking-tight leading-none">
                    Solusi Profesional Untuk <br>
                    <span class="text-yellow-400">Perawatan</span> & <span class="text-yellow-400">Perbaikan</span> <br>
                    Laptop Anda
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
                <div class="mb-4">
                    <h3 class="text-lg font-extrabold uppercase tracking-tight text-slate-900">Status Tracking</h3>
                    <p class="text-xs text-slate-400 font-medium">Lacak Progres Unit Service Anda</p>
                </div>
                <form action="lacak_status.php" method="GET" class="flex flex-col sm:flex-row gap-3">
                    <input type="text" name="tiket" placeholder="Masukkan Nomor Tiket (Misal: HB260520-001)" required
                        class="flex-1 px-5 py-3.5 bg-gray-50 border border-gray-200 rounded-2xl text-sm focus:bg-white focus:ring-2 focus:ring-yellow-400 outline-none font-medium transition">
                    <button type="submit" class="bg-black hover:bg-slate-900 text-white font-bold text-xs uppercase px-8 py-4 rounded-2xl transition tracking-wider shrink-0">
                        Lacak
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
                    <p class="text-xs text-slate-500 leading-relaxed font-medium">Cleaning menyeluruh dan perawatan rutin laptop.</p>
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

            <div class="bg-white border border-gray-200 rounded-[2rem] shadow-xl overflow-hidden flex flex-col justify-between">
                <div>
                    <div class="h-32 w-full overflow-hidden bg-slate-100">
                        <img src="images/paket-basic.jpg" alt="Paket Basic" class="w-full h-full object-cover">
                    </div>
                    <div class="p-6 text-center bg-[#1e293b] text-white">
                        <h3 class="text-base font-extrabold">Paket Basic</h3>
                        <p class="text-[11px] text-slate-300 mt-0.5">Cleaning & Pembersihan</p>
                    </div>
                    <div class="p-6 text-center border-b border-gray-100 bg-gray-50/50">
                        <p class="text-[11px] text-slate-400">Mulai dari <span class="text-2xl font-extrabold text-slate-900 block mt-1">75K</span></p>
                        <p class="text-[10px] text-slate-400 font-bold mt-1">Laptop Kantoran</p>
                    </div>
                    <ul class="p-6 space-y-3.5 text-xs font-semibold text-slate-600">
                        <li class="flex items-center gap-2.5"><i class="fas fa-check-circle text-emerald-500"></i> Pembersihan internal & eksternal</li>
                        <li class="flex items-center gap-2.5"><i class="fas fa-check-circle text-emerald-500"></i> Pembersihan debu & kotoran</li>
                        <li class="flex items-center gap-2.5"><i class="fas fa-check-circle text-emerald-500"></i> Pembersihan keyboard & layar</li>
                        <li class="flex items-center gap-2.5"><i class="fas fa-check-circle text-emerald-500"></i> Garansi 7 hari</li>
                    </ul>
                </div>
                <div class="p-6 pt-0 text-center">
                    <p class="text-[10px] font-bold text-slate-400 mb-4">Laptop Gaming: <span class="font-extrabold text-slate-800">100K</span></p>
                    <a href="buat_reservasi.php?paket=basic" class="block w-full bg-black hover:bg-slate-900 text-white font-bold text-xs uppercase py-3.5 rounded-xl tracking-wider transition">Pilih Paket</a>
                </div>
            </div>

            <div class="bg-white border-2 border-yellow-400 rounded-[2rem] shadow-2xl overflow-hidden flex flex-col justify-between relative transform lg:-translate-y-2">
                <span class="absolute top-3 right-3 bg-yellow-400 text-black text-[9px] font-black uppercase px-2.5 py-1 rounded-full tracking-wider z-10">POPULER</span>
                <div>
                    <div class="h-32 w-full overflow-hidden bg-slate-100">
                        <img src="images/paket-standard.jpg" alt="Paket Standard" class="w-full h-full object-cover">
                    </div>
                    <div class="p-6 text-center bg-yellow-500 text-slate-950">
                        <h3 class="text-base font-black">Paket Standard</h3>
                        <p class="text-[11px] font-bold text-slate-800 mt-0.5">Repaste & Maintenance</p>
                    </div>
                    <div class="p-6 text-center border-b border-gray-100 bg-gray-50/50">
                        <p class="text-[11px] text-slate-400">Mulai dari <span class="text-2xl font-extrabold text-slate-900 block mt-1">150K</span></p>
                        <p class="text-[10px] text-slate-400 font-bold mt-1">Laptop Kantoran</p>
                    </div>
                    <ul class="p-6 space-y-3.5 text-xs font-semibold text-slate-700">
                        <li class="flex items-center gap-2.5"><i class="fas fa-check-circle text-emerald-500"></i> Semua fitur Paket Basic</li>
                        <li class="flex items-center gap-2.5"><i class="fas fa-check-circle text-emerald-500"></i> Repaste thermal paste premium</li>
                        <li class="flex items-center gap-2.5"><i class="fas fa-check-circle text-emerald-500"></i> Pembersihan fan & heatsink</li>
                        <li class="flex items-center gap-2.5"><i class="fas fa-check-circle text-emerald-500"></i> Cek hardware & software</li>
                        <li class="flex items-center gap-2.5"><i class="fas fa-check-circle text-emerald-500"></i> Garansi 14 hari</li>
                    </ul>
                </div>
                <div class="p-6 pt-0 text-center">
                    <p class="text-[10px] font-bold text-slate-400 mb-4">Laptop Gaming: <span class="font-extrabold text-slate-800">200K</span></p>
                    <a href="buat_reservasi.php?paket=standard" class="block w-full bg-yellow-400 hover:bg-yellow-500 text-slate-950 font-black text-xs uppercase py-3.5 rounded-xl tracking-wider transition">Pilih Paket</a>
                </div>
            </div>

            <div class="bg-white border border-gray-200 rounded-[2rem] shadow-xl overflow-hidden flex flex-col justify-between">
                <div>
                    <div class="h-32 w-full overflow-hidden bg-slate-100">
                        <img src="images/paket-premium.jpg" alt="Paket Premium" class="w-full h-full object-cover">
                    </div>
                    <div class="p-6 text-center bg-[#1e293b] text-white">
                        <h3 class="text-base font-extrabold">Paket Premium</h3>
                        <p class="text-[11px] text-slate-300 mt-0.5">Full Optimization</p>
                    </div>
                    <div class="p-6 text-center border-b border-gray-100 bg-gray-50/50">
                        <p class="text-[11px] text-slate-400">Mulai dari <span class="text-2xl font-extrabold text-slate-900 block mt-1">250K</span></p>
                        <p class="text-[10px] text-slate-400 font-bold mt-1">Laptop Gaming</p>
                    </div>
                    <ul class="p-6 space-y-3.5 text-xs font-semibold text-slate-600">
                        <li class="flex items-center gap-2.5"><i class="fas fa-check-circle text-emerald-500"></i> Semua fitur Paket Standard</li>
                        <li class="flex items-center gap-2.5"><i class="fas fa-check-circle text-emerald-500"></i> Optimasi performa maksimal</li>
                        <li class="flex items-center gap-2.5"><i class="fas fa-check-circle text-emerald-500"></i> Install ulang OS (optional)</li>
                        <li class="flex items-center gap-2.5"><i class="fas fa-check-circle text-emerald-500"></i> Update driver & software</li>
                        <li class="flex items-center gap-2.5"><i class="fas fa-check-circle text-emerald-500"></i> Garansi 30 hari</li>
                    </ul>
                </div>
                <div class="p-6 pt-0 text-center">
                    <p class="text-[10px] font-bold text-slate-400 mb-4">Laptop Gaming: <span class="font-extrabold text-slate-800">250K</span></p>
                    <a href="buat_reservasi.php?paket=premium" class="block w-full bg-black hover:bg-slate-900 text-white font-bold text-xs uppercase py-3.5 rounded-xl tracking-wider transition">Pilih Paket</a>
                </div>
            </div>

        </div>
    </section>

    <footer id="kontak" class="bg-[#1e293b] text-slate-300 pt-16 pb-8 border-t border-slate-800">
        <div class="max-w-7xl mx-auto px-6 grid grid-cols-1 md:grid-cols-12 gap-10 mb-12">
            <div class="md:col-span-5 space-y-4">
                <div class="flex items-center gap-3">
                    <img src="../logo warna.png" alt="Logo Hanbit" class="w-8 h-8 object-contain">
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
                    <li class="flex items-start gap-3"><i class="fas fa-map-marker-alt text-yellow-400 mt-0.5"></i> <span>Jl. Cihanjuang No.3 Kp. Centeng</span></li>
                </ul>
            </div>
            <div class="md:col-span-3 space-y-3.5">
                <h4 class="text-white font-bold text-sm uppercase tracking-wider">Jam Operasional</h4>
                <ul class="space-y-2 text-xs font-semibold text-slate-400">
                    <li>Senin - Jumat: 09.00 - 18.00</li>
                    <li>Sabtu - Minggu: 09.00 - 15.00</li>
                </ul>
            </div>
        </div>
        <div class="max-w-7xl mx-auto px-6 pt-6 border-t border-slate-800/80 text-center text-[11px] font-medium text-slate-500">
            &copy; 2026 Hanbit. All rights reserved.
        </div>
    </footer>

</body>

</html>