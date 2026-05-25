<?php
require_once 'config.php';
require_once 'koneksi.php';
proteksi_halaman();

// Query SQL disesuaikan dengan huruf kecil sesuai database db_hanbit yang baru
$query = "SELECT * FROM customers ORDER BY nama_customer ASC";
$result = mysqli_query($koneksi, $query);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hanbit Adm - Manajemen Pelanggan</title>
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
                <p class="text-[10px] font-black text-yellow-500 uppercase tracking-[0.3em] mb-1">Database Internal</p>
                <h1 class="text-3xl font-black text-slate-800 uppercase italic tracking-tight">Manajemen Pelanggan</h1>
            </div>
            
            <button class="bg-[#facc15] hover:bg-[#eab308] text-black font-black px-6 py-3.5 rounded-2xl text-xs uppercase italic tracking-widest transition-all duration-300 shadow-lg shadow-yellow-400/10 flex items-center gap-2">
                <i class="fas fa-user-plus text-xs"></i> Tambah Pelanggan
            </button>
        </header>

        <div class="bg-white rounded-[2.5rem] shadow-xl shadow-slate-200/50 border border-gray-100 overflow-hidden">
            <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                <h2 class="text-sm font-black text-slate-700 uppercase tracking-wider italic">Daftar Konsumen Terdaftar</h2>
                <span class="bg-slate-900 text-yellow-400 text-[10px] font-bold px-3 py-1 rounded-full uppercase tracking-wider">
                    Total: <?= mysqli_num_rows($result); ?> Orang
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-gray-100 text-[10px] font-black text-slate-400 uppercase tracking-widest bg-gray-50/30">
                            <th class="py-5 px-6">ID Pelanggan</th>
                            <th class="py-5 px-6">Nama Lengkap</th>
                            <th class="py-5 px-6">Kontak WhatsApp</th>
                            <th class="py-5 px-6">Alamat Email</th>
                            <th class="py-5 px-6 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 text-sm font-medium text-slate-600">
                        <?php if (mysqli_num_rows($result) > 0): ?>
                            <?php while($row = mysqli_fetch_assoc($result)): ?>
                                <tr class="hover:bg-gray-50/80 transition-colors">
                                    <td class="py-5 px-6 font-bold text-slate-400">#CS-<?= str_pad($row['id_customer'], 3, '0', STR_PAD_LEFT); ?></td>
                                    <td class="py-5 px-6 font-bold text-slate-800 uppercase text-xs tracking-wide">
                                        <?= htmlspecialchars($row['nama_customer']); ?>
                                    </td>
                                    <td class="py-5 px-6">
                                        <a href="https://wa.me/<?= $row['no_hp']; ?>" target="_blank" class="inline-flex items-center gap-1.5 text-emerald-600 hover:text-emerald-700 font-bold bg-emerald-50 px-3 py-1 rounded-xl border border-emerald-100/50 text-xs">
                                            <i class="fab fa-whatsapp text-sm"></i> <?= htmlspecialchars($row['no_hp']); ?>
                                        </a>
                                    </td>
                                    <td class="py-5 px-6 text-slate-500 font-semibold">
                                        <?= !empty($row['email']) ? htmlspecialchars($row['email']) : '<span class="text-slate-300 italic text-xs">Tidak ada email</span>'; ?>
                                    </td>
                                    <td class="py-5 px-6 text-center">
                                        <div class="flex items-center justify-center gap-2">
                                            <button class="w-8 h-8 bg-slate-100 text-slate-700 hover:bg-slate-900 hover:text-yellow-400 rounded-xl flex items-center justify-center text-xs transition-all duration-200" title="Edit Data">
                                                <i class="fas fa-user-edit"></i>
                                            </button>
                                            <button class="w-8 h-8 bg-red-50 text-red-600 hover:bg-red-600 hover:text-white rounded-xl flex items-center justify-center text-xs transition-all duration-200" title="Hapus Pelanggan">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="py-12 text-center text-slate-400 italic text-xs">
                                    <i class="fas fa-users-slash text-2xl block mb-2 text-slate-300"></i> Data pelanggan masih kosong.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

</body>
</html>