<?php
require_once '../koneksi.php';

// REVISI JALUR: Menambahkan sub-folder /logo/ sesuai dengan struktur folder barumu
$brands_list = [
    ['id' => 1, 'nama' => 'ASUS', 'logo' => 'images/logo/asus.png'],
    ['id' => 2, 'nama' => 'LENOVO', 'logo' => 'images/logo/lenovo.png'],
    ['id' => 3, 'nama' => 'HP', 'logo' => 'images/logo/hp.png'],
    ['id' => 4, 'nama' => 'DELL', 'logo' => 'images/logo/dell.png'],
    ['id' => 5, 'nama' => 'ACER', 'logo' => 'images/logo/acer.png'],
    ['id' => 6, 'nama' => 'ADVAN', 'logo' => 'images/logo/advan.png'],
    ['id' => 7, 'nama' => 'AXIOO', 'logo' => 'images/logo/axioo.png'],
    ['id' => 8, 'nama' => 'MSI', 'logo' => 'images/logo/msi.png']
];
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hanbit - Estimasi Harga</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght=400;500;600;700;800&display=swap');
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>

<body class="bg-[#f8fafc] text-slate-900 antialiased min-h-screen flex flex-col justify-between">

    <!-- Navbar Area -->
    <nav class="bg-white border-b border-gray-100 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-6 h-20 flex justify-between items-center">
            <a href="index.php" class="flex items-center gap-3 hover:opacity-90 transition select-none">
                <img src="../logo warna.png" alt="Logo Hanbit" class="w-10 h-10 object-contain">
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

    <!-- Main Content Area -->
    <main class="max-w-5xl mx-auto w-full px-6 pt-10 pb-16 flex-1 flex flex-col justify-start gap-5">
        
        <div class="text-center space-y-1">
            <h1 class="text-3xl md:text-4xl font-extrabold tracking-tight text-slate-900">Pilih Merek Laptop Anda</h1>
            <p class="text-sm text-slate-400 font-medium">Silakan pilih merek Laptop yang sesuai dengan Laptop anda.</p>
        </div>

        <!-- Stepper Progress Bar -->
        <div class="flex flex-row flex-nowrap items-center justify-center gap-x-3 md:gap-x-5 text-[11px] md:text-xs font-black uppercase overflow-x-auto whitespace-nowrap">
            <div class="text-slate-900 flex items-center gap-2 shrink-0">
                <span class="w-7 h-7 rounded-full bg-[#facc15] text-slate-950 flex items-center justify-center font-black text-xs shadow-sm">1</span> 
                <span class="whitespace-nowrap">Pilih Merek</span>
            </div>
            <div class="h-[2px] w-6 md:w-10 bg-gray-200 shrink-0"></div>
            
            <div class="text-slate-400 flex items-center gap-2 shrink-0">
                <span class="w-7 h-7 rounded-full bg-gray-200 text-slate-400 flex items-center justify-center font-black text-xs">2</span> 
                <span class="whitespace-nowrap">Pilih Series</span>
            </div>
            <div class="h-[2px] w-6 md:w-10 bg-gray-200 shrink-0"></div>
            
            <div class="text-slate-400 flex items-center gap-2 shrink-0">
                <span class="w-7 h-7 rounded-full bg-gray-200 text-slate-400 flex items-center justify-center font-black text-xs">3</span> 
                <span class="whitespace-nowrap">Pilih Masalah</span>
            </div>
            <div class="h-[2px] w-6 md:w-10 bg-gray-200 shrink-0"></div>
            
            <div class="text-slate-400 flex items-center gap-2 shrink-0">
                <span class="w-7 h-7 rounded-full bg-gray-200 text-slate-400 flex items-center justify-center font-black text-xs">4</span> 
                <span class="whitespace-nowrap">Detail & Book</span>
            </div>
        </div>

        <!-- Form Pemilihan Brand -->
        <form action="cek_estimasi_series.php" method="GET" onsubmit="return validasiMerek()" class="space-y-6 pt-2">
            <input type="hidden" name="brand_id" id="selected_brand_id">

            <div class="grid grid-cols-2 sm:grid-cols-4 gap-5">
                <?php foreach ($brands_list as $brand): ?>
                    <div type="button" onclick="pilihBrand(this, '<?= $brand['id']; ?>')" 
                            class="brand-card flex flex-col items-center justify-center pt-8 pb-6 px-4 bg-white border border-gray-200 rounded-2xl cursor-pointer hover:border-yellow-400 hover:bg-yellow-50/10 transition-all duration-200 group select-none shadow-sm shadow-slate-100">
                        <div class="w-28 h-16 flex items-center justify-center mb-4 bg-white">
                            <!-- Ditambahkan cache buster ?v=time() agar update fisik file di folder langsung segar di browser -->
                            <img src="<?= $brand['logo']; ?>?v=<?= time(); ?>" alt="Logo <?= $brand['nama']; ?>" 
                                 class="max-w-full max-h-full object-contain filter grayscale group-hover:grayscale-0 transition-all duration-200">
                        </div>
                        <span class="text-xs font-extrabold uppercase tracking-normal text-slate-700 group-hover:text-slate-900">
                            <?= $brand['nama']; ?>
                        </span>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Tombol Navigasi Bawah -->
            <div class="flex justify-between items-center pt-4">
                <a href="index.php" class="bg-[#e2e8f0] hover:bg-[#cbd5e1] text-slate-600 font-bold text-xs uppercase px-7 py-3 rounded-lg flex items-center gap-2 transition">
                    <i class="fas fa-chevron-left text-[10px]"></i> Kembali
                </a>
                <button type="submit" class="bg-[#ffd54f] hover:bg-[#ffca28] text-slate-900 font-extrabold text-xs uppercase px-7 py-3 rounded-lg flex items-center gap-2 tracking-normal transition shadow-sm">
                    Selanjutnya <i class="fas fa-chevron-right text-[10px]"></i>
                </button>
            </div>
        </form>
    </main>

    <!-- Footer Area Section -->
    <footer class="bg-[#1e293b] text-slate-300 pt-12 pb-6 border-t border-slate-800">
        <div class="max-w-7xl mx-auto px-6 text-center text-[11px] font-medium text-slate-500">
            &copy; 2026 Hanbit. All rights reserved.
        </div>
    </footer>

    <!-- POP-UP MODAL VALIDASI CUSTOM (MUNCUL DI TENGAH LAYAR) -->
    <div id="modal_validasi_merek" class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-50 hidden flex items-center justify-center px-4">
        <div class="bg-white rounded-[1.5rem] max-w-sm w-full p-6 shadow-xl space-y-4 border border-gray-100 text-center">
            <div class="w-12 h-12 bg-amber-100 rounded-full flex items-center justify-center mx-auto text-amber-500">
                <i class="fas fa-exclamation-circle text-xl"></i>
            </div>
            <div class="space-y-1">
                <h3 class="text-base font-extrabold text-slate-900">Merek Belum Dipilih</h3>
                <p class="text-xs text-slate-400 font-medium leading-relaxed">Silakan pilih salah satu Merek Laptop anda terlebih dahulu sebelum melanjutkan ke tahap berikutnya!</p>
            </div>
            <div class="pt-2">
                <button type="button" onclick="tutupModalValidasi()" class="w-full bg-[#ffd54f] hover:bg-[#ffca28] text-slate-900 text-xs font-black py-3 rounded-xl transition block text-center shadow-sm uppercase tracking-wider">
                    Paham, Pilih Merek
                </button>
            </div>
        </div>
    </div>

    <!-- JAVASCRIPT LOGIKA KONTROL INTERAKTIF -->
    <script>
        function pilihBrand(elemenTarget, idBrand) {
            const semuaCard = document.querySelectorAll('.brand-card');
            semuaCard.forEach(card => {
                card.classList.remove('border-2', 'border-yellow-400', 'bg-yellow-50/10');
                card.classList.add('border-gray-200', 'bg-white');
                card.querySelector('img').classList.add('grayscale');
            });
            elemenTarget.classList.remove('border-gray-200', 'bg-white');
            elemenTarget.classList.add('border-2', 'border-yellow-400', 'bg-yellow-50/10');
            elemenTarget.querySelector('img').classList.remove('grayscale');
            
            // Set data ID ke input hidden
            document.getElementById('selected_brand_id').value = idBrand;
        }

        // FUNGSI PENGUNCI POP-UP MODAL CUSTOM DI TENGAH LAYAR
        function validasiMerek() {
            const idMerekTerpilih = document.getElementById('selected_brand_id').value;
            
            if (idMerekTerpilih === "" || idMerekTerpilih === null) {
                // Munculkan boks modal tengah dengan melepas class hidden
                document.getElementById('modal_validasi_merek').classList.remove('hidden');
                return false; // Mengunci / membatalkan perpindahan halaman
            }
            return true; // Lolos ke halaman pilih series
        }

        function tutupModalValidasi() {
            document.getElementById('modal_validasi_merek').classList.add('hidden');
        }
    </script>
</body>

</html>