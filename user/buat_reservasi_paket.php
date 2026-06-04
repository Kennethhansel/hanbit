<?php
// 1. BUAT KONEKSI KE DATABASE
$host = "localhost";
$user = "root";
$pass = "";
$db   = "db_hanbit";
$koneksi = mysqli_connect($host, $user, $pass, $db);

if (!$koneksi) {
    die("Koneksi database Hanbit gagal: " . mysqli_connect_error());
}

// 2. TANGKAP PARAMETER PAKET DARI URL
$paket_pilihan = isset($_GET['paket']) ? trim(mysqli_real_escape_string($koneksi, $_GET['paket'])) : 'basic';

// 3. LOGIKA RINCIAN BENEFIT LENGKAP, GARANSI, DAN HARGA SEGMEN LAPTOP
if ($paket_pilihan == 'standard') {
    $nama_paket_id = "Paket Standard (Repaste & Maintenance)";
    $harga_kantoran = 150000;
    $harga_gaming = 200000;
    $garansi = "Garansi 14 Hari";
    $benefits_detail = [
        "Pembersihan menyeluruh debu & kotoran pada komponen internal",
        "Pembersihan sasis luar, sela-sela keyboard, dan layar laptop",
        "Penggantian Thermal Paste Premium untuk menurunkan suhu panas",
        "Pembersihan mendalam pada bilah kipas (fan) & jalur heatsink",
        "Pengecekan kesehatan hardware dasar & stabilitas software"
    ];
} elseif ($paket_pilihan == 'premium') {
    $nama_paket_id = "Paket Premium (Full Optimization)";
    $harga_kantoran = 250000;
    $harga_gaming = 250000;
    $garansi = "Garansi 30 Hari";
    $benefits_detail = [
        "Semua layanan pembersihan mendalam pada Paket Standard",
        "Penggantian Thermal Paste performa tinggi (High-Performance)",
        "Optimasi penuh kecepatan sistem operasi (OS) agar anti-lemot",
        "Pembaruan (Update) driver hardware dan aplikasi esensial",
        "Layanan Install Ulang OS Windows secara bersih (Optional)"
    ];
} else {
    // Default / Basic
    $paket_pilihan = 'basic';
    $nama_paket_id = "Paket Basic (Cleaning & Pembersihan)";
    $harga_kantoran = 75000;
    $harga_gaming = 100000;
    $garansi = "Garansi 7 Hari";
    $benefits_detail = [
        "Pembersihan debu & kotoran sasis bagian internal laptop",
        "Pembersihan eksternal pada sela-sela tombol keyboard",
        "Pembersihan bercak noda dan debu pada permukaan layar",
        "Pelumasan poros kipas laptop agar putaran kembali senyap"
    ];
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

    <!-- Navbar Area Navigation Bar -->
    <nav class="bg-white border-b border-gray-100 sticky top-0 z-50 shadow-sm">
        <div class="max-w-7xl mx-auto px-6 h-20 flex justify-between items-center">
            <a href="index.php" class="flex items-center gap-3 hover:opacity-90 transition select-none">
                <img src="../logo warna.png?v=<?= time(); ?>" alt="Logo Hanbit" class="w-10 h-10 object-contain">
                <span class="text-3xl font-extrabold tracking-tight text-slate-900">Hanbit</span>
            </a>
            <div class="hidden md:flex items-center gap-8 text-sm font-semibold text-slate-600">
                <a href="index.php#home" class="hover:text-yellow-600 transition">Home</a>
                <a href="katalog.php" class="hover:text-slate-900 transition">Katalog</a>
                <a href="index.php#layanan" class="hover:text-slate-900 transition">Layanan</a>
                <a href="portofolio.php" class="hover:text-slate-900 transition">Portofolio</a>
                <a href="index.php#kontak" class="hover:text-slate-900 transition">Kontak</a>
            </div>
            <a href="https://wa.me/6285159794427" target="_blank" class="bg-[#00e676] hover:bg-[#00c853] text-white text-xs font-bold px-4 py-2.5 rounded-xl flex items-center gap-2 shadow-sm transition">
                <i class="fab fa-whatsapp text-sm"></i> Chat Via WA
            </a>
        </div>
    </nav>

    <!-- Main Container Content -->
    <main class="max-w-6xl mx-auto w-full px-6 pt-10 pb-16 flex-1 flex flex-col justify-start gap-8">
        
        <!-- Judul Utama & Deskripsi Paket Perawatan Flat -->
        <div class="text-center space-y-1">
            <h1 class="text-3xl md:text-4xl font-extrabold tracking-tight text-slate-900">Detail Paket <span class="text-yellow-400">Perawatan</span></h1>
            <p class="text-xs text-slate-400 font-medium tracking-wide">Berikut rincian lengkap pengerjaan maintenance dan biaya flat paket unit Anda.</p>
        </div>

        <!-- LAYOUT GRID UTAMA -->
        <form action="proses_booking_paket.php" method="POST" class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            <input type="hidden" name="paket_tipe" value="<?= htmlspecialchars($paket_pilihan); ?>">
            <input type="hidden" name="segmen_laptop" id="input_segmen_laptop" value="kantoran">
            <input type="hidden" name="total_harga_final" id="input_harga_final" value="<?= $harga_kantoran; ?>">

            <!-- ================= BAGIAN KIRI (DETAIL PAKET JELAS & BOX TOTAL BIAYA FIX) ================= -->
            <div class="lg:col-span-7 space-y-6">
                
                <!-- Boks Putih Atas Detail Paket Maintenance -->
                <div class="bg-white border border-gray-100 p-6 md:p-8 rounded-[1.5rem] shadow-sm space-y-6">
                    <div class="flex items-center gap-3 border-b border-gray-50 pb-4">
                        <div class="w-10 h-10 bg-amber-50 rounded-xl flex items-center justify-center text-amber-500">
                            <i class="fas fa-tools text-base"></i>
                        </div>
                        <div>
                            <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider block">Jenis Perawatan Terpilih</span>
                            <h3 class="text-base font-black text-slate-900 uppercase tracking-tight"><?= $nama_paket_id; ?></h3>
                        </div>
                    </div>

                    <!-- Daftar Layanan Jelas & Lengkap -->
                    <div class="space-y-3.5">
                        <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block"><i class="fas fa-clipboard-list text-amber-500 mr-1"></i> Apa Saja Yang Didapat Pada Paket Ini:</label>
                        <ul class="space-y-3 text-xs text-slate-600 font-medium">
                            <?php foreach($benefits_detail as $benefit): ?>
                                <li class="flex items-start gap-3 leading-tight">
                                    <i class="fas fa-check-circle text-emerald-500 mt-0.5 shrink-0 text-sm"></i>
                                    <span><?= htmlspecialchars($benefit); ?></span>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>

                    <!-- Segmen Tipe Laptop (Kantoran VS Gaming) -->
                    <div class="space-y-2 pt-4 border-t border-gray-50">
                        <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Pilih Segmen Spesifikasi Unit Laptop:</label>
                        <div class="grid grid-cols-2 gap-4">
                            <div onclick="pilihSegmen('kantoran', <?= $harga_kantoran; ?>)" id="segmen_kantoran" 
                                 class="border-2 border-yellow-400 bg-yellow-50/10 p-4 rounded-xl cursor-pointer text-center transition-all select-none">
                                <i class="fas fa-briefcase text-slate-700 text-base mb-1 block"></i>
                                <span class="text-xs font-extrabold text-slate-900 block">Laptop Kantoran</span>
                            </div>
                            <div onclick="pilihSegmen('gaming', <?= $harga_gaming; ?>)" id="segmen_gaming" 
                                 class="border border-slate-200 bg-white p-4 rounded-xl cursor-pointer text-center transition-all hover:border-yellow-400 select-none">
                                <i class="fas fa-gamepad text-slate-400 text-base mb-1 block"></i>
                                <span class="text-xs font-bold text-slate-500 block">Laptop Gaming</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Boks Gelap Bawah Total Biaya Fix Terkunci -->
                <div class="bg-[#1a233a] text-white p-6 md:p-8 rounded-[1.5rem] shadow-xl flex flex-col sm:flex-row justify-between sm:items-center gap-4">
                    <div class="space-y-1">
                        <span class="text-[9px] md:text-[10px] font-bold text-yellow-400 uppercase tracking-wider block">TOTAL BIAYA FIX (PAS)</span>
                        <h2 class="text-3xl md:text-4xl font-black text-white" id="text_harga_total">Rp <?= number_format($harga_kantoran, 0, ',', '.'); ?></h2>
                    </div>
                    <div class="space-y-2 text-[11px] font-semibold text-slate-300">
                        <div class="flex items-center gap-2">
                            <i class="fas fa-check-circle text-emerald-400 text-xs"></i> <span>Termasuk Jasa Pasang & Pembersihan</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <i class="fas fa-check-circle text-emerald-400 text-xs"></i> <span id="text_garansi_label"><?= $garansi; ?></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ================= BAGIAN KANAN (BOKS LENGKAPI BOOKING FORM GARIS KUNING) ================= -->
            <div class="lg:col-span-5 bg-white border-2 border-[#ffd54f] p-6 md:p-7 rounded-[1.5rem] shadow-lg space-y-5">
                
                <!-- Alamat Workshop Atas -->
                <div class="bg-amber-50/50 border border-amber-100 p-4 rounded-xl space-y-1">
                    <div class="text-[11px] font-extrabold text-slate-900 uppercase flex items-center gap-1.5">
                        <i class="fas fa-map-marker-alt text-amber-500"></i> LOKASI HANBIT
                    </div>
                    <p class="text-[11px] text-slate-400 font-medium leading-relaxed">Jl. Cihanjuang No.3 Kp. Centeng, Bandung</p>
                    <a href="https://maps.google.com" target="_blank" class="text-[11px] font-bold text-amber-600 hover:underline block pt-0.5">Buka di Google Maps</a>
                </div>

                <div class="border-b border-gray-100 pb-1">
                    <h3 class="text-base font-black text-slate-900">Lengkapi Booking</h3>
                </div>

                <!-- Input Fields Form -->
                <div class="space-y-1.5">
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">NAMA LENGKAP</label>
                    <input type="text" name="nama_pelanggan" placeholder="Tuliskan nama lengkap anda..." required 
                           class="w-full px-4 py-3 bg-slate-50 border border-slate-200/60 rounded-xl text-sm font-medium focus:outline-none focus:border-yellow-400 focus:bg-white transition text-slate-700">
                </div>

                <div class="space-y-1.5">
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">WHATSAPP</label>
                    <input type="tel" name="no_whatsapp" placeholder="08xxxxxxxxxx" required 
                           class="w-full px-4 py-3 bg-slate-50 border border-slate-200/60 rounded-xl text-sm font-medium focus:outline-none focus:border-yellow-400 focus:bg-white transition text-slate-700">
                </div>

                <!-- REVISI: TAMBAHAN INPUT BARU MEREK & SERI LAPTOP -->
                <div class="space-y-1.5">
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">MEREK & SERI LAPTOP</label>
                    <input type="text" name="laptop_detail" placeholder="Contoh: ASUS ROG Strix G15 / HP Pavilion 14" required 
                           class="w-full px-4 py-3 bg-slate-50 border border-slate-200/60 rounded-xl text-sm font-medium focus:outline-none focus:border-yellow-400 focus:bg-white transition text-slate-700">
                </div>

                <div class="space-y-1.5">
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">ALAMAT LENGKAP ANDA</label>
                    <textarea name="alamat_pelanggan" rows="2" placeholder="Tuliskan alamat rumah lengkap anda..." required 
                              class="w-full px-4 py-3 bg-slate-50 border border-slate-200/60 rounded-xl text-sm font-medium focus:outline-none focus:border-yellow-400 focus:bg-white transition text-slate-700 resize-none"></textarea>
                </div>

                <div class="space-y-1.5">
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">TANGGAL MENYERAHKAN</label>
                    <input type="date" name="tanggal_booking" required 
                           class="w-full px-4 py-3 bg-slate-50 border border-slate-200/60 rounded-xl text-sm focus:outline-none focus:border-yellow-400 focus:bg-white transition text-slate-600 font-medium">
                </div>

                <!-- Action Buttons Samping-Sampingan -->
                <div class="flex gap-3 pt-2">
                    <a href="index.php" class="bg-slate-100 hover:bg-slate-200 text-slate-500 font-bold text-xs uppercase px-5 py-3.5 rounded-xl flex items-center justify-center gap-1.5 transition select-none">
                        <i class="fas fa-chevron-left text-[9px]"></i> KEMBALI
                    </a>
                    <button type="submit" class="flex-1 bg-[#ffd54f] hover:bg-[#ffca28] text-slate-900 font-black text-xs uppercase py-3.5 rounded-xl flex items-center justify-center gap-1.5 tracking-wider transition shadow-sm shadow-yellow-400/10">
                        BOOKING SEKARANG <i class="fas fa-calendar-check text-xs"></i>
                    </button>
                </div>
            </div>
        </form>
    </main>

    <!-- Footer Area Section -->
    <footer class="bg-[#1e293b] text-slate-300 pt-12 pb-6 border-t border-slate-800">
        <div class="max-w-7xl mx-auto px-6 text-center text-[11px] font-medium text-slate-500">
            &copy; 2026 Hanbit. All rights reserved.
        </div>
    </footer>

    <!-- INTERAKTIF JAVASCRIPT: UPDATE HARGA KONDISIONAL BERDASARKAN SEGMEN LAPTOP -->
    <script>
        function pilihSegmen(tipe, harga) {
            document.getElementById('input_segmen_laptop').value = tipe;
            document.getElementById('input_harga_final').value = harga;
            
            const boxKantoran = document.getElementById('segmen_kantoran');
            const boxGaming = document.getElementById('segmen_gaming');

            boxKantoran.className = "border border-slate-200 bg-white p-4 rounded-xl cursor-pointer text-center transition-all hover:border-yellow-400 select-none";
            boxKantoran.querySelector('i').className = "fas fa-briefcase text-slate-400 text-base mb-1 block";
            boxKantoran.querySelector('span').className = "text-xs font-bold text-slate-500 block";

            boxGaming.className = "border border-slate-200 bg-white p-4 rounded-xl cursor-pointer text-center transition-all hover:border-yellow-400 select-none";
            boxGaming.querySelector('i').className = "fas fa-gamepad text-slate-400 text-base mb-1 block";
            boxGaming.querySelector('span').className = "text-xs font-bold text-slate-500 block";

            if (tipe === 'kantoran') {
                boxKantoran.className = "border-2 border-yellow-400 bg-yellow-50/10 p-4 rounded-xl cursor-pointer text-center transition-all select-none";
                boxKantoran.querySelector('i').className = "fas fa-briefcase text-slate-700 text-base mb-1 block";
                boxKantoran.querySelector('span').className = "text-xs font-extrabold text-slate-900 block";
            } else {
                boxGaming.className = "border-2 border-yellow-400 bg-yellow-50/10 p-4 rounded-xl cursor-pointer text-center transition-all select-none";
                boxGaming.querySelector('i').className = "fas fa-gamepad text-slate-700 text-base mb-1 block";
                boxGaming.querySelector('span').className = "text-xs font-extrabold text-slate-900 block";
            }

            const formatRupiah = "Rp " + harga.toLocaleString('id-ID');
            document.getElementById('text_harga_total').innerText = formatRupiah;
        }
    </script>
</body>
</html>