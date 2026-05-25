<?php
require_once 'config.php';
require_once 'koneksi.php';
proteksi_halaman();

// Query untuk mengambil data master Brand (Menggunakan huruf kecil)
$query_brand = "SELECT * FROM laptop_brands ORDER BY nama_brand ASC";
$result_brand = mysqli_query($koneksi, $query_brand);

// Query untuk mengambil data master Series (JOIN menggunakan huruf kecil)
$query_series = "SELECT s.*, b.nama_brand 
                 FROM laptop_series s
                 JOIN laptop_brands b ON s.id_brand = b.id_brand
                 ORDER BY b.nama_brand ASC, s.nama_series ASC";
$result_series = mysqli_query($koneksi, $query_series);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hanbit Adm - Katalog Data</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght=400;500;600;700;800&display=swap');
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-gray-50 flex min-h-screen text-slate-900">

    <?php include 'sidebar.php'; ?>

    <main class="flex-1 p-8 overflow-y-auto">
        
        <header class="flex justify-between items-end mb-8">
            <div>
                <p class="text-[10px] font-black text-yellow-500 uppercase tracking-[0.3em] mb-1">Konfigurasi Master Data</p>
                <h1 class="text-3xl font-black text-slate-800 uppercase italic tracking-tight">Katalog Data Laptop</h1>
            </div>
            
            <div class="flex gap-3">
                <button class="bg-slate-900 hover:bg-slate-800 text-white font-black px-5 py-3.5 rounded-2xl text-xs uppercase italic tracking-widest transition-all duration-300 shadow-lg flex items-center gap-2">
                    <i class="fas fa-plus text-yellow-400 text-xs"></i> Brand
                </button>
                <button class="bg-[#facc15] hover:bg-[#eab308] text-black font-black px-5 py-3.5 rounded-2xl text-xs uppercase italic tracking-widest transition-all duration-300 shadow-lg shadow-yellow-400/10 flex items-center gap-2">
                    <i class="fas fa-plus text-xs"></i> Tipe Series
                </button>
            </div>
        </header>

        <div class="grid grid-cols-1 xl:grid-cols-3 gap-8">
            
            <div class="bg-white rounded-[2.5rem] shadow-xl shadow-slate-200/50 border border-gray-100 overflow-hidden h-fit">
                <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                    <h2 class="text-sm font-black text-slate-700 uppercase tracking-wider italic">Master Brand</h2>
                    <span class="bg-slate-900 text-yellow-400 text-[10px] font-bold px-2.5 py-0.5 rounded-full uppercase">
                        <?= mysqli_num_rows($result_brand); ?> Brand
                    </span>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-gray-100 text-[10px] font-black text-slate-400 uppercase tracking-widest bg-gray-50/30">
                                <th class="py-4 px-6">ID</th>
                                <th class="py-4 px-6">Nama Brand</th>
                                <th class="py-4 px-6 text-center">Aksi</th>
                            </tr>
                        </table>
                        <table class="w-full text-left border-collapse">
                        <tbody class="divide-y divide-gray-50 text-sm font-medium text-slate-600">
                            <?php while($row_b = mysqli_fetch_assoc($result_brand)): ?>
                                <tr class="hover:bg-gray-50/50 transition-colors">
                                    <td class="py-4 px-6 font-bold text-slate-400">#BRD-<?= $row_b['id_brand']; ?></td>
                                    <td class="py-4 px-6 font-black text-slate-800 uppercase text-xs tracking-wider"><?= htmlspecialchars($row_b['nama_brand']); ?></td>
                                    <td class="py-4 px-6 text-center">
                                        <div class="flex items-center justify-center gap-1.5">
                                            <button class="w-7 h-7 bg-slate-100 text-slate-600 hover:bg-slate-900 hover:text-yellow-400 rounded-lg flex items-center justify-center text-xs transition-all">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="xl:col-span-2 bg-white rounded-[2.5rem] shadow-xl shadow-slate-200/50 border border-gray-100 overflow-hidden h-fit">
                <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                    <h2 class="text-sm font-black text-slate-700 uppercase tracking-wider italic">Daftar Seri & Tipe Laptop</h2>
                    <span class="bg-yellow-400 text-black text-[10px] font-black px-2.5 py-0.5 rounded-full uppercase">
                        <?= mysqli_num_rows($result_series); ?> Tipe
                    </span>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-gray-100 text-[10px] font-black text-slate-400 uppercase tracking-widest bg-gray-50/30">
                                <th class="py-4 px-6">ID Series</th>
                                <th class="py-4 px-6">Brand</th>
                                <th class="py-4 px-6">Nama Series Laptop</th>
                                <th class="py-4 px-6 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50 text-sm font-medium text-slate-600">
                            <?php while($row_s = mysqli_fetch_assoc($result_series)): ?>
                                <tr class="hover:bg-gray-50/50 transition-colors">
                                    <td class="py-4 px-6 font-bold text-slate-400">#SRS-<?= str_pad($row_s['id_series'], 3, '0', STR_PAD_LEFT); ?></td>
                                    <td class="py-4 px-6">
                                        <span class="bg-slate-100 text-slate-800 text-[10px] font-black uppercase px-2.5 py-1 rounded-lg border border-slate-200/40">
                                            <?= htmlspecialchars($row_s['nama_brand']); ?>
                                        </span>
                                    </td>
                                    <td class="py-4 px-6 font-bold text-slate-700 uppercase text-xs italic tracking-wide">
                                        <?= htmlspecialchars($row_s['nama_series']); ?>
                                    </td>
                                    <td class="py-4 px-6 text-center">
                                        <div class="flex items-center justify-center gap-1.5">
                                            <button class="w-7 h-7 bg-slate-100 text-slate-600 hover:bg-slate-900 hover:text-yellow-400 rounded-lg flex items-center justify-center text-xs transition-all">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="w-7 h-7 bg-red-50 text-red-600 hover:bg-red-600 hover:text-white rounded-lg flex items-center justify-center text-xs transition-all">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </main>

</body>
</html>