<?php
// 1. BUAT KONEKSI KE DATABASE UTAMA
$host = "localhost";
$user = "root";
$pass = "";
$db   = "db_hanbit";
$koneksi = mysqli_connect($host, $user, $pass, $db);

if (!$koneksi) {
    die("Koneksi database Hanbit gagal: " . mysqli_connect_error());
}

// 2. AMBIL DATA DARI DATABASE (Sudah Diperbaiki Menggunakan nama tabel asli: tb_katalog)
$katalog_produk = [];
// id_produk diubah AS id, dan nama_produk diubah AS nama agar sinkron dengan variabel HTML bawaan di bawah
$query = "SELECT id_produk AS id, kategori, nama_produk AS nama, deskripsi, harga, gambar, link_ecommerce FROM tb_katalog ORDER BY id_produk DESC";
$result = mysqli_query($koneksi, $query);

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $katalog_produk[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hanbit - Katalog Rekomendasi Part & Aksesoris</title>
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

    <main class="max-w-7xl mx-auto w-full px-6 pt-10 pb-16 flex-1 flex flex-col justify-start gap-8">

        <div class="text-center space-y-2 max-w-xl mx-auto">
            <span class="bg-amber-100 text-amber-700 text-[10px] font-black uppercase px-3 py-1 rounded-full tracking-wider">Hanbit Approved</span>
            <h1 class="text-3xl md:text-4xl font-black tracking-tight text-slate-900">Katalog Komponen Laptop</h1>
            <p class="text-sm text-slate-400 font-medium leading-relaxed">Kami merekomendasikan hardware & aksesoris kualitas terbaik yang terjamin kompatibel untuk upgrade unit laptop Anda.</p>
        </div>

        <div class="flex flex-wrap justify-center items-center gap-2 md:gap-3 border-b border-gray-100 pb-2">
            <button type="button" onclick="saringKategori('semua', this)" class="btn-filter bg-[#1e293b] text-white font-extrabold text-xs uppercase px-4 py-2.5 rounded-xl shadow-sm transition duration-200">
                Semua Produk
            </button>
            <button type="button" onclick="saringKategori('ram', this)" class="btn-filter bg-white hover:bg-slate-50 border border-gray-200 text-slate-600 font-bold text-xs uppercase px-4 py-2.5 rounded-xl transition duration-200">
                RAM
            </button>
            <button type="button" onclick="saringKategori('storage', this)" class="btn-filter bg-white hover:bg-slate-50 border border-gray-200 text-slate-600 font-bold text-xs uppercase px-4 py-2.5 rounded-xl transition duration-200">
                Storage
            </button>
            <button type="button" onclick="saringKategori('lcd', this)" class="btn-filter bg-white hover:bg-slate-50 border border-gray-200 text-slate-600 font-bold text-xs uppercase px-4 py-2.5 rounded-xl transition duration-200">
                LCD Screen
            </button>
            <button type="button" onclick="saringKategori('keyboard', this)" class="btn-filter bg-white hover:bg-slate-50 border border-gray-200 text-slate-600 font-bold text-xs uppercase px-4 py-2.5 rounded-xl transition duration-200">
                Keyboard
            </button>
            <button type="button" onclick="saringKategori('charger', this)" class="btn-filter bg-white hover:bg-slate-50 border border-gray-200 text-slate-600 font-bold text-xs uppercase px-4 py-2.5 rounded-xl transition duration-200">
                Charger
            </button>
            <button type="button" onclick="saringKategori('pasta', this)" class="btn-filter bg-white hover:bg-slate-50 border border-gray-200 text-slate-600 font-bold text-xs uppercase px-4 py-2.5 rounded-xl transition duration-200">
                Thermal Paste
            </button>
            <button type="button" onclick="saringKategori('flashdisk', this)" class="btn-filter bg-white hover:bg-slate-50 border border-gray-200 text-slate-600 font-bold text-xs uppercase px-4 py-2.5 rounded-xl transition duration-200">
                Flashdisk
            </button>
            <button type="button" onclick="saringKategori('aksesoris', this)" class="btn-filter bg-white hover:bg-slate-50 border border-gray-200 text-slate-600 font-bold text-xs uppercase px-4 py-2.5 rounded-xl transition duration-200">
                Aksesoris
            </button>
        </div>

        <div id="grid_katalog" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
            <?php foreach ($katalog_produk as $produk): ?>
                <div data-category="<?= $produk['kategori']; ?>"
                    class="item-produk bg-white border border-gray-200/70 rounded-[1.8rem] overflow-hidden flex flex-col justify-between shadow-sm hover:shadow-md hover:border-yellow-400/60 transition-all duration-300 group">

                    <div class="p-5 space-y-4">
                        <div class="w-full h-44 bg-slate-50 rounded-2xl overflow-hidden flex items-center justify-center p-4 border border-gray-50/50 relative">
                            <span class="absolute top-2 left-2 bg-slate-900/80 backdrop-blur-sm text-white text-[9px] font-bold uppercase px-2.5 py-0.5 rounded-md tracking-wider">
                                <?php
                                $map_label = [
                                    'ram' => 'RAM',
                                    'storage' => 'Storage',
                                    'lcd' => 'LCD Screen',
                                    'keyboard' => 'Keyboard',
                                    'charger' => 'Charger',
                                    'pasta' => 'Thermal Paste',
                                    'flashdisk' => 'Flashdisk',
                                    'aksesoris' => 'Aksesoris'
                                ];
                                echo $map_label[$produk['kategori']] ?? $produk['kategori'];
                                ?>
                            </span>

                            <?php 
                                // REVISI AMAN: Deteksi otomatis tipe path gambar lama vs file baru dari admin agar tidak pecah
                                $src_gambar = (strpos($produk['gambar'], 'images/') !== false) ? $produk['gambar'] : 'images/katalog/' . $produk['gambar'];
                            ?>
                            <img src="<?= $src_gambar; ?>?v=<?= time(); ?>" alt="<?= htmlspecialchars($produk['nama']); ?>"
                                class="max-w-full max-h-full object-contain group-hover:scale-105 transition-transform duration-300">
                        </div>

                        <div class="space-y-1">
                            <h3 class="text-sm font-extrabold text-slate-800 line-clamp-2 leading-snug group-hover:text-yellow-600 transition-colors" title="<?= htmlspecialchars($produk['nama']); ?>">
                                <?= htmlspecialchars($produk['nama']); ?>
                            </h3>
                            <p class="text-[11px] text-slate-400 font-medium leading-relaxed line-clamp-2">
                                <?= htmlspecialchars($produk['deskripsi']); ?>
                            </p>
                        </div>
                    </div>

                    <div class="p-5 pt-0 border-t border-gray-50/50 flex items-center justify-between gap-3 mt-auto">
                        <div class="space-y-0.5">
                            <span class="block text-[9px] font-bold text-slate-400 uppercase tracking-wider">Estimasi Harga</span>
                            <span class="text-base font-black text-slate-900">Rp <?= number_format($produk['harga'], 0, ',', '.'); ?></span>
                        </div>

                        <a href="<?= $produk['link_ecommerce']; ?>" target="_blank"
                            class="bg-[#ffd54f] hover:bg-[#ffca28] text-slate-900 font-black text-[10px] uppercase px-4 py-3 rounded-xl flex items-center gap-1.5 transition shadow-sm tracking-wider">
                            Beli Produk <i class="fas fa-external-link-alt text-[9px]"></i>
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </main>

    <footer class="bg-[#1e293b] text-slate-300 pt-12 pb-6 border-t border-slate-800">
        <div class="max-w-7xl mx-auto px-6 text-center text-[11px] font-medium text-slate-500">
            &copy; 2026 Hanbit. All rights reserved.
        </div>
    </footer>

    <script>
        function saringKategori(kategoriTerpilih, elemenTombol) {
            const semuaTombol = document.querySelectorAll('.btn-filter');
            semuaTombol.forEach(btn => {
                btn.classList.remove('bg-[#1e293b]', 'text-white', 'font-extrabold');
                btn.classList.add('bg-white', 'text-slate-600', 'font-bold', 'border', 'border-gray-200');
            });

            elemenTombol.classList.remove('bg-white', 'text-slate-600', 'font-bold', 'border', 'border-gray-200');
            elemenTombol.classList.add('bg-[#1e293b]', 'text-white', 'font-extrabold');

            const semuaItem = document.querySelectorAll('.item-produk');
            semuaItem.forEach(item => {
                const kategoriItem = item.getAttribute('data-category');

                if (kategoriTerpilih === 'semua' || kategoriItem === kategoriTerpilih) {
                    item.classList.remove('hidden');
                } else {
                    item.hidden = true; 
                    item.classList.add('hidden');
                }
            });
        }
    </script>
</body>

</html>