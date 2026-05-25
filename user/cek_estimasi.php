<?php
require_once '../koneksi.php';

// Array data dummy 8 merek laptop sesuai figma kamu
$brands_list = [
    ['id' => 1, 'nama' => 'ASUS'],
    ['id' => 2, 'nama' => 'LENOVO'],
    ['id' => 3, 'nama' => 'HP'],
    ['id' => 4, 'nama' => 'DELL'],
    ['id' => 5, 'nama' => 'ACER'],
    ['id' => 6, 'nama' => 'ADVAN'],
    ['id' => 7, 'nama' => 'AXIOO'],
    ['id' => 8, 'nama' => 'MSI']
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

    <nav class="bg-white border-b border-gray-100 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-6 h-20 flex justify-between items-center">
            <div class="flex items-center gap-3">
                <img src="../logo warna.png" alt="Logo Hanbit" class="w-10 h-10 object-contain">
                <span class="text-3xl font-extrabold tracking-tight">Hanbit</span>
            </div>
            <div class="hidden md:flex items-center gap-8 text-sm font-semibold text-slate-600">
                <a href="index.php" class="hover:text-yellow-600 transition">Home</a>
                <a href="index.php#layanan" class="hover:text-slate-900 transition">Katalog</a>
                <a href="index.php#layanan" class="hover:text-slate-900 transition">Layanan</a>
                <a href="index.php#kontak" class="hover:text-slate-900 transition">Kontak</a>
            </div>
            <a href="https://wa.me/6285159794427" target="_blank" class="bg-[#00e676] hover:bg-[#00c853] text-white text-xs font-bold px-4 py-2.5 rounded-xl flex items-center gap-2 shadow-sm transition">
                <i class="fab fa-whatsapp text-sm"></i> Chat Via WA
            </a>
        </div>
    </nav>

    <main class="max-w-5xl mx-auto w-full px-6 py-12 flex-1 flex flex-col justify-center space-y-12">
        
        <div class="text-center space-y-2">
            <h1 class="text-3xl md:text-4xl font-extrabold tracking-tight uppercase">PILIH MEREK LAPTOP ANDA</h1>
            <p class="text-xs text-slate-400 font-medium tracking-wide">Silakan tentukan manufaktur atau brand perangkat laptop yang ingin Anda perbaiki</p>
        </div>

        <div class="flex flex-wrap items-center justify-center gap-6 md:gap-8 text-[11px] font-black tracking-wider uppercase border-b border-gray-200/60 pb-4">
            <div class="text-yellow-500 flex items-center gap-1.5"><span class="w-5 h-5 rounded-full bg-yellow-400 text-slate-950 flex items-center justify-center font-bold text-[10px]">1</span> PILIH MEREK</div>
            <div class="text-slate-400 flex items-center gap-1.5"><span class="w-5 h-5 rounded-full bg-white border border-gray-200 text-slate-400 flex items-center justify-center font-bold text-[10px]">2</span> PILIH SERIES</div>
            <div class="text-slate-400 flex items-center gap-1.5"><span class="w-5 h-5 rounded-full bg-white border border-gray-200 text-slate-400 flex items-center justify-center font-bold text-[10px]">3</span> PILIH MASALAH</div>
            <div class="text-slate-400 flex items-center gap-1.5"><span class="w-5 h-5 rounded-full bg-white border border-gray-200 text-slate-400 flex items-center justify-center font-bold text-[10px]">4</span> DETAIL DAN BOOK</div>
        </div>

        <form action="cek_estimasi_series.php" method="GET" class="space-y-12">
            
            <div class="text-center md:text-left">
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Pilih Merek Laptop :</p>
            </div>

            <input type="hidden" name="brand_id" id="selected_brand_id" required>

            <div class="grid grid-cols-2 sm:grid-cols-4 gap-6">
                <?php foreach($brands_list as $brand): 
                    $logo_file = "images/" . strtolower($brand['nama']) . ".png";
                ?>
                    <div type="button" onclick="pilihBrand(this, '<?= $brand['id']; ?>')" 
                            class="brand-card flex flex-col items-center justify-center p-6 bg-white border border-gray-200 rounded-2xl cursor-pointer hover:border-yellow-400 hover:bg-yellow-50/10 transition-all duration-200 group select-none shadow-sm">
                        
                        <div class="w-14 h-14 flex items-center justify-center mb-3">
                            <img src="<?= $logo_file; ?>" alt="Logo <?= $brand['nama']; ?>" 
                                 class="max-w-full max-h-full object-contain filter grayscale group-hover:grayscale-0 transition-all duration-200">
                        </div>
                        
                        <span class="text-xs font-bold uppercase tracking-wider text-slate-600 group-hover:text-slate-900">
                            <?= $brand['nama']; ?>
                        </span>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="flex justify-between items-center pt-6 border-t border-gray-200">
                <a href="index.php" class="bg-white border border-gray-200 hover:bg-gray-50 text-slate-700 font-extrabold text-xs uppercase px-8 py-3.5 rounded-xl shadow-sm transition">
                    Kembali
                </a>
                <button type="submit" class="bg-[#facc15] hover:bg-[#eab308] text-slate-950 font-black text-xs uppercase px-8 py-3.5 rounded-xl tracking-wider transition shadow-md shadow-yellow-400/10">
                    Selanjutnya
                </button>
            </div>

        </form>
    </main>

    <footer class="bg-[#1e293b] text-slate-300 pt-16 pb-8 border-t border-slate-800">
        <div class="max-w-7xl mx-auto px-6 grid grid-cols-1 md:grid-cols-12 gap-10 mb-12">
            <div class="md:col-span-5 space-y-4">
                <div class="flex items-center gap-3">
                    <img src="../logo warna.png" alt="Logo Hanbit" class="w-8 h-8 object-contain">
                    <span class="text-lg font-black text-white tracking-tight">Hanbit</span>
                </div>
                <p class="text-xs text-slate-400 font-medium leading-relaxed max-w-sm">Solusi profesional untuk perawatan dan perbaikan laptop Anda dengan garansi terpercaya.</p>
            </div>
            <div class="md:col-span-4 space-y-3.5">
                <h4 class="text-white font-bold text-sm uppercase tracking-wider">Kontak</h4>
                <ul class="space-y-2.5 text-xs font-semibold text-slate-400">
                    <li><i class="fas fa-phone-alt text-yellow-400 mr-2"></i> +62 851-5979-4427</li>
                    <li><i class="fas fa-envelope text-yellow-400 mr-2"></i> hanbit0925@gmail.com</li>
                </ul>
            </div>
            <div class="md:col-span-3 space-y-3.5">
                <h4 class="text-white font-bold text-sm uppercase tracking-wider">Jam Operasional</h4>
                <ul class="space-y-2 text-xs font-semibold text-slate-400">
                    <li>Senin - Jumat: 09.00 - 18.00</li>
                </ul>
            </div>
        </div>
        <div class="max-w-7xl mx-auto px-6 pt-6 border-t border-slate-800/80 text-center text-[11px] font-medium text-slate-500">
            &copy; 2026 Hanbit. All rights reserved.
        </div>
    </footer>

    <script>
        function pilihBrand(elemenTarget, idBrand) {
            const semuaCard = document.querySelectorAll('.brand-card');
            semuaCard.forEach(card => {
                card.classList.remove('border-yellow-400', 'bg-yellow-50/10');
                card.classList.add('border-gray-200', 'bg-white');
                card.querySelector('img').classList.add('grayscale');
            });

            elemenTarget.classList.remove('border-gray-200', 'bg-white');
            elemenTarget.classList.add('border-yellow-400', 'bg-yellow-50/10');
            elemenTarget.querySelector('img').classList.remove('grayscale');

            document.getElementById('selected_brand_id').value = idBrand;
        }
    </script>

</body>
</html>