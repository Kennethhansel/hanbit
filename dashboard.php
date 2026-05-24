<?php
require_once 'config.php';
require_once 'koneksi.php';
proteksi_halaman();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hanbit Adm - Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-gray-50 flex min-h-screen text-slate-900">

    <?php include 'sidebar.php'; ?>

    <main class="flex-1 p-8 overflow-y-auto">
        <header class="flex justify-between items-end mb-8">
            <div>
                <p class="text-[10px] font-black text-yellow-500 uppercase tracking-[0.3em] mb-1">Selamat Datang Kembali</p>
                <h1 class="text-3xl font-black text-slate-800 uppercase italic tracking-tight">Dashboard Hanbit</h1>
            </div>
            <div class="bg-white p-2.5 rounded-2xl shadow-sm border border-gray-100 flex items-center gap-4 px-6 italic">
                <div class="text-right">
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Administrator</p>
                    <p class="text-sm font-black text-slate-800 uppercase italic"><?= $_SESSION['nama_admin']; ?></p>
                </div>
                <div class="w-10 h-10 bg-slate-900 rounded-full flex items-center justify-center shadow-lg">
                    <i class="fas fa-user-tie text-yellow-400 text-sm"></i>
                </div>
            </div>
        </header>

        <div class="bg-white p-8 rounded-[2.5rem] shadow-xl shadow-slate-200/50 border border-gray-100">
            <h2 class="text-xl font-black text-slate-800 uppercase italic mb-2">Sistem Siap Digunakan!</h2>
            <p class="text-sm text-slate-500 leading-relaxed">Kamu berhasil tembus dari halaman login. Sekarang fondasi session aman, database terkoneksi dengan benar, dan layout sidebar kamu sudah terpasang dengan presisi.</p>
        </div>
    </main>

</body>
</html>