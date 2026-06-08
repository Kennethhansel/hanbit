<?php
// 1. BUAT KONEKSI DATABASE
$host = "localhost";
$user = "root";
$pass = "";
$db   = "db_hanbit";
$koneksi = mysqli_connect($host, $user, $pass, $db);

if (!$koneksi) {
    die("Koneksi database Hanbit gagal: " . mysqli_connect_error());
}

// 2. AMBIL DATA DINAMIS DARI DATABASE (Menggantikan Array Manual)
$portofolio_list = [];
$query = "SELECT id_porto AS id, kategori, tipe_media, judul, deskripsi, sumber_media FROM tb_portofolio ORDER BY id_porto DESC";
$result = mysqli_query($koneksi, $query);

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $portofolio_list[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hanbit - Portofolio Servis Laptop</title>
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
                <span class="text-3xl font-extrabold tracking-tight">Hanbit</span>
            </a>
            <div class="hidden md:flex items-center gap-8 text-sm font-semibold text-slate-600">
                <a href="index.php#home" class="hover:text-yellow-600 transition">Home</a>
                <a href="katalog.php" class="hover:text-slate-900 transition">Katalog</a>
                <a href="index.php#layanan" class="hover:text-slate-900 transition">Layanan</a>
                <a href="portofolio.php" class="hover:text-slate-900 transition">Portofolio</a>
                <a href="index.php#kontak" class="hover:text-slate-900 transition">Contact</a>
            </div>
            <a href="https://wa.me/6285159794427" target="_blank" class="bg-[#00e676] hover:bg-[#00c853] text-white text-xs font-bold px-4 py-2.5 rounded-xl flex items-center gap-2 shadow-sm transition">
                <i class="fab fa-whatsapp text-sm"></i> Chat Via WA
            </a>
        </div>
    </nav>

    <main class="max-w-6xl mx-auto w-full px-6 pt-10 pb-16 flex-1 flex flex-col justify-start gap-8">

        <div class="text-center space-y-2 max-w-xl mx-auto">
            <span class="bg-blue-100 text-blue-700 text-[10px] font-black uppercase px-3 py-1 rounded-full tracking-wider">Our Work Log</span>
            <h1 class="text-2xl sm:text-3xl md:text-4xl font-black tracking-tight text-slate-900">
                Portofolio Hanbit Labs
            </h1>
            <p class="text-sm text-slate-400 font-medium leading-relaxed">Dokumentasi kerja nyata penanganan unit konsumen kami secara transparan dan profesional.</p>
        </div>

        <div class="flex flex-wrap justify-center items-center gap-2 md:gap-3 border-b border-gray-100 pb-2">
            <button type="button" onclick="saringPorto('semua', this)" class="btn-filter bg-[#1e293b] text-white font-extrabold text-xs uppercase px-5 py-2.5 rounded-xl shadow-sm transition duration-200">
                Semua Kasus
            </button>
            <button type="button" onclick="saringPorto('maintenance', this)" class="btn-filter bg-white hover:bg-slate-50 border border-gray-200 text-slate-600 font-bold text-xs uppercase px-5 py-2.5 rounded-xl transition duration-200">
                Maintenance & Repaste
            </button>
            <button type="button" onclick="saringPorto('layar', this)" class="btn-filter bg-white hover:bg-slate-50 border border-gray-200 text-slate-600 font-bold text-xs uppercase px-5 py-2.5 rounded-xl transition duration-200">
                Ganti Layar LCD
            </button>
            <button type="button" onclick="saringPorto('keyboard', this)" class="btn-filter bg-white hover:bg-slate-50 border border-gray-200 text-slate-600 font-bold text-xs uppercase px-5 py-2.5 rounded-xl transition duration-200">
                Perbaikan Keyboard
            </button>
            <button type="button" onclick="saringPorto('matot', this)" class="btn-filter bg-white hover:bg-slate-50 border border-gray-200 text-slate-600 font-bold text-xs uppercase px-5 py-2.5 rounded-xl transition duration-200">
                Mati Total (Mobo Short)
            </button>
        </div>

        <div id="grid_portofolio" class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <?php foreach ($portofolio_list as $porto): ?>
                <div data-category="<?= $porto['kategori']; ?>"
                    class="item-porto bg-white border border-gray-200/80 rounded-[2rem] overflow-hidden flex flex-col justify-between shadow-sm hover:shadow-md transition-all duration-300">

                    <div class="w-full bg-black aspect-video overflow-hidden relative">
                        <?php if ($porto['tipe_media'] == 'video'): ?>
                            <iframe class="w-full h-full" src="<?= $porto['sumber_media']; ?>" title="Hanbit Video Player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                        <?php else: ?>
                            <?php 
                                // REVISI JALUR GAMBAR: Mendeteksi data bawaan lama (ada kata 'images/') vs upload admin baru (hanya nama file)
                                $src_media = (strpos($porto['sumber_media'], 'images/') !== false) ? $porto['sumber_media'] : 'images/portofolio/' . $porto['sumber_media'];
                            ?>
                            <img src="<?= $src_media; ?>?v=<?= time(); ?>" alt="<?= htmlspecialchars($porto['judul']); ?>" class="w-full h-full object-cover">
                        <?php endif; ?>
                    </div>

                    <div class="p-6 space-y-2 flex-1 flex flex-col justify-start">
                        <div class="flex items-center gap-2">
                            <span class="bg-amber-100 text-amber-800 text-[9px] font-extrabold uppercase px-2.5 py-0.5 rounded-md tracking-wider">
                                <?php
                                $labels = ['maintenance' => 'Maintenance', 'layar' => 'Ganti Layar', 'keyboard' => 'Keyboard', 'matot' => 'Mati Total'];
                                echo $labels[$porto['kategori']] ?? $porto['kategori'];
                                ?>
                            </span>
                            <span class="text-[10px] font-bold text-slate-400 uppercase"><i class="fas <?= $porto['tipe_media'] == 'video' ? 'fa-video' : 'fa-image'; ?> mr-1"></i><?= $porto['tipe_media']; ?> log</span>
                        </div>
                        <h3 class="text-base font-black text-slate-900 leading-snug"><?= htmlspecialchars($porto['judul']); ?></h3>
                        
                        <p class="text-xs text-slate-500 font-medium leading-relaxed whitespace-pre-line mt-1"><?= nl2br(htmlspecialchars($porto['deskripsi'])); ?></p>
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
        function saringPorto(kategoriTerpilih, elemenTombol) {
            const semuaTombol = document.querySelectorAll('.btn-filter');
            semuaTombol.forEach(btn => {
                btn.classList.remove('bg-[#1e293b]', 'text-white', 'font-extrabold');
                btn.classList.add('bg-white', 'text-slate-600', 'font-bold', 'border', 'border-gray-200');
            });

            elemenTombol.classList.remove('bg-white', 'text-slate-600', 'font-bold', 'border', 'border-gray-200');
            elemenTombol.classList.add('bg-[#1e293b]', 'text-white', 'font-extrabold');

            const semuaItem = document.querySelectorAll('.item-porto');
            semuaItem.forEach(item => {
                const kategoriItem = item.getAttribute('data-category');

                if (kategoriTerpilih === 'semua' || kategoriItem === kategoriTerpilih) {
                    item.classList.remove('hidden');
                } else {
                    item.classList.add('hidden');
                }
            });
        }
    </script>
</body>

</html>