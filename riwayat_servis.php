<?php
require_once 'config.php';
require_once 'koneksi.php';
proteksi_halaman();

// Query SQL untuk mengambil data reservasi yang status pengerjaannya SUDAH SELESAI
$query = "SELECT r.*, c.Nama_Customer, c.No_Hp, s.Nama_Series 
          FROM reservations r
          JOIN customers c ON r.ID_Customer = c.ID_Customer
          JOIN laptop_series s ON r.ID_Series = s.ID_Series
          WHERE r.Status_Aktif = 1 AND r.Status_Pengerjaan = 'Selesai'
          ORDER BY r.Tgl_Reservasi DESC, r.Jam_Slot DESC";

$result = mysqli_query($koneksi, $query);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hanbit Adm - Riwayat Servis</title>
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
                <p class="text-[10px] font-black text-emerald-500 uppercase tracking-[0.3em] mb-1">Arsip Transaksi</p>
                <h1 class="text-3xl font-black text-slate-800 uppercase italic tracking-tight">Riwayat Servis</h1>
            </div>
            
            <div class="text-xs font-bold text-slate-400 italic bg-white px-5 py-3 rounded-2xl border border-gray-100 shadow-sm flex items-center gap-2">
                <i class="fas fa-archive text-slate-400"></i> Data Terarsip Otomatis
            </div>
        </header>

        <div class="bg-white rounded-[2.5rem] shadow-xl shadow-slate-200/50 border border-gray-100 overflow-hidden">
            <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                <h2 class="text-sm font-black text-slate-700 uppercase tracking-wider italic">Rekam Jejak Servis Selesai</h2>
                <span class="bg-emerald-600 text-white text-[10px] font-bold px-3 py-1 rounded-full uppercase tracking-wider">
                    Selesai: <?= mysqli_num_rows($result); ?> Unit
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-gray-100 text-[10px] font-black text-slate-400 uppercase tracking-widest bg-gray-50/30">
                            <th class="py-5 px-6">Kode Order</th>
                            <th class="py-5 px-6">Nama Pelanggan</th>
                            <th class="py-5 px-6">Seri Laptop</th>
                            <th class="py-5 px-6">Tanggal Selesai</th>
                            <th class="py-5 px-6">Status Log</th>
                            <th class="py-5 px-6 text-center">Nota</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 text-sm font-medium text-slate-600">
                        <?php if (mysqli_num_rows($result) > 0): ?>
                            <?php while($row = mysqli_fetch_assoc($result)): ?>
                                <tr class="hover:bg-gray-50/50 transition-colors">
                                    <td class="py-5 px-6 font-bold text-slate-400">#<?= $row['Kode_Order']; ?></td>
                                    <td class="py-5 px-6">
                                        <p class="font-bold text-slate-800 uppercase text-xs"><?= $row['Nama_Customer']; ?></p>
                                        <p class="text-[11px] text-slate-400 mt-0.5"><i class="fab fa-whatsapp text-slate-400 mr-1"></i><?= $row['No_Hp']; ?></p>
                                    </td>
                                    <td class="py-5 px-6 text-xs font-bold text-slate-600 uppercase italic"><?= $row['Nama_Series']; ?></td>
                                    <td class="py-5 px-6">
                                        <p class="text-xs font-bold text-slate-800"><?= date('d M Y', strtotime($row['Tgl_Reservasi'])); ?></p>
                                        <p class="text-[11px] text-emerald-600 font-bold mt-0.5"><i class="fas fa-check-circle mr-1"></i>Slot <?= $row['Jam_Slot']; ?></p>
                                    </td>
                                    <td class="py-5 px-6">
                                        <span class="bg-emerald-50 text-emerald-700 border border-emerald-100 text-[10px] font-black uppercase px-3 py-1.5 rounded-xl inline-flex items-center gap-1">
                                            <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full animate-pulse"></span> Archived
                                        </span>
                                    </td>
                                    <td class="py-5 px-6 text-center">
                                        <button class="w-8 h-8 bg-slate-100 text-slate-700 hover:bg-emerald-600 hover:text-white rounded-xl flex items-center justify-center text-xs transition-all duration-200" title="Cetak Nota / Invoice">
                                            <i class="fas fa-print"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="py-12 text-center text-slate-400 italic text-xs">
                                    <i class="fas fa-history text-2xl block mb-2 text-slate-300"></i> Belum ada riwayat servis yang diselesaikan.
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