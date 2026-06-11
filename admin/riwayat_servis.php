<?php
require_once 'config.php';
require_once 'koneksi.php';
proteksi_halaman();

// LOGIKA ENGINE: Proses Hapus Massal Berkas Arsip Riwayat Servis
if (isset($_POST['eksekusi_hapus_arsip_massal'])) {
    if (!empty($_POST['arsip_invoice_hapus'])) {
        $ids = array_map(function($id) use ($koneksi) {
            return "'" . mysqli_real_escape_string($koneksi, $id) . "'";
        }, $_POST['arsip_invoice_hapus']);
        
        $set_ids = implode(',', $ids);
        $query_del = "DELETE FROM reservations WHERE no_invoice IN ($set_ids)";
        mysqli_query($koneksi, $query_del);
    }
    header("Location: riwayat_servis.php");
    exit;
}

// 🌟 REVISI: Ambil parameter filter periode
$pilihan_bulan = isset($_GET['bulan']) ? intval($_GET['bulan']) : intval(date('m'));
$pilihan_tahun = isset($_GET['tahun']) ? intval($_GET['tahun']) : intval(date('Y'));
$mode_filter   = isset($_GET['mode']) ? trim($_GET['mode']) : 'bulan';
$search        = isset($_GET['search']) ? mysqli_real_escape_string($koneksi, trim($_GET['search'])) : '';
$sort_tgl      = isset($_GET['sort_tgl']) ? trim($_GET['sort_tgl']) : 'terbaru';

// Query dasar
$query = "SELECT no_invoice, nama_pelanggan, no_whatsapp, laptop_detail, tanggal_booking, status_order, created_at, tanggal_selesai 
          FROM reservations 
          WHERE status_order = 'SELESAI'";

// 🔥 REVISI QUERY: Filter otomatis berdasarkan mode bulan atau tahun
if ($mode_filter == 'bulan') {
    $query .= " AND MONTH(tanggal_selesai) = $pilihan_bulan AND YEAR(tanggal_selesai) = $pilihan_tahun";
} else {
    $query .= " AND YEAR(tanggal_selesai) = $pilihan_tahun";
}

if (!empty($search)) {
    $query .= " AND (no_invoice LIKE '%$search%' OR nama_pelanggan LIKE '%$search%')";
}

$query .= ($sort_tgl == 'terlama') ? " ORDER BY tanggal_selesai ASC" : " ORDER BY tanggal_selesai DESC";
$result = mysqli_query($koneksi, $query);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hanbit Admin - Riwayat Servis</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght=400;500;600;700;800;900&display=swap');
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-[#f8fafc] text-slate-800 antialiased flex min-h-screen">

    <?php include 'sidebar.php'; ?>

    <main class="flex-1 p-8 overflow-y-auto">
        <div class="max-w-full mx-auto space-y-6">
            
            <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-4 min-h-[56px]">
                <div>
                    <h1 class="text-2xl font-black tracking-tight text-slate-900">Riwayat Transaksi Servis</h1>
                    <p class="text-xs text-slate-400 font-medium">Rekam jejak seluruh berkas pengerjaan perbaikan laptop yang telah selesai total.</p>
                </div>
                
                <button type="button" id="btn_hapus_arsip_massal" onclick="bukaModalHapusMassal()" 
                        class="hidden bg-red-500 hover:bg-red-600 text-white font-black px-6 py-2.5 rounded-xl text-xs uppercase tracking-wider transition-all duration-300 shadow-md flex items-center gap-2 transform scale-95 opacity-0">
                    <i class="fas fa-trash-alt text-xs"></i> Hapus Terpilih (<span id="count_arsip_terpilih">0</span>)
                </button>
            </div>

            <div class="bg-white border border-gray-200/80 p-4 rounded-2xl shadow-sm flex flex-col md:flex-row justify-between items-center gap-4 text-xs font-bold">
                <form id="form_filter" action="" method="GET" class="flex flex-wrap items-center gap-3">
                    <div class="flex bg-slate-100 p-1 rounded-xl">
                        <button type="button" onclick="setMode('bulan')" class="px-4 py-1.5 rounded-lg transition <?= $mode_filter == 'bulan' ? 'bg-white shadow-sm font-black' : '' ?>">Bulanan</button>
                        <button type="button" onclick="setMode('tahun')" class="px-4 py-1.5 rounded-lg transition <?= $mode_filter == 'tahun' ? 'bg-white shadow-sm font-black' : '' ?>">Tahunan</button>
                    </div>
                    <input type="hidden" name="mode" id="mode_input" value="<?= $mode_filter ?>">
                    
                    <?php if($mode_filter == 'bulan'): ?>
                    <select name="bulan" onchange="this.form.submit()" class="px-3 py-2 bg-slate-50 border rounded-xl cursor-pointer">
                        <?php for($i=1; $i<=12; $i++) echo "<option value='$i' ".($pilihan_bulan==$i?'selected':'').">".date('F', mktime(0,0,0,$i,1))."</option>"; ?>
                    </select>
                    <?php endif; ?>
                    
                    <select name="tahun" onchange="this.form.submit()" class="px-3 py-2 bg-slate-50 border rounded-xl cursor-pointer">
                        <?php for($y=date('Y'); $y>=date('Y')-3; $y--) echo "<option value='$y' ".($pilihan_tahun==$y?'selected':'').">$y</option>"; ?>
                    </select>
                    
                    <select name="sort_tgl" onchange="this.form.submit()" class="px-3 py-2 bg-slate-50 border rounded-xl cursor-pointer">
                        <option value="terbaru" <?= $sort_tgl == 'terbaru' ? 'selected' : ''; ?>>📅 Terbaru</option>
                        <option value="terlama" <?= $sort_tgl == 'terlama' ? 'selected' : ''; ?>>📅 Terlama</option>
                    </select>
                </form>

                <form action="" method="GET" class="flex gap-2 w-full md:w-64 items-center">
                    <input type="hidden" name="mode" value="<?= $mode_filter ?>">
                    <input type="hidden" name="bulan" value="<?= $pilihan_bulan ?>">
                    <input type="hidden" name="tahun" value="<?= $pilihan_tahun ?>">
                    <div class="relative w-full">
                        <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                        <input type="text" name="search" value="<?= htmlspecialchars($search); ?>" placeholder="Cari Invoice / Nama..." class="w-full pl-9 pr-3 py-2 bg-slate-50 border border-gray-200 rounded-xl font-medium focus:outline-none focus:border-blue-400 transition">
                    </div>
                    <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-5 py-2 rounded-xl transition">Cari</button>
                </form>
            </div>

            <form id="form_arsip_massal" action="riwayat_servis.php" method="POST">
                <div class="bg-white border border-gray-200/80 rounded-2xl shadow-sm overflow-hidden">
                    <div class="p-4 border-b border-gray-100 bg-slate-50/50 flex justify-between items-center">
                        <h3 class="text-xs font-extrabold uppercase text-slate-900 tracking-wider">Arsip Nota Servis Selesai</h3>
                        <span class="bg-emerald-600 text-white text-[10px] font-bold px-2.5 py-0.5 rounded-full uppercase">
                            Selesai: <?= mysqli_num_rows($result); ?> Unit
                        </span>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse text-xs">
                            <thead>
                                <tr class="bg-slate-100/60 text-slate-400 font-bold uppercase border-b border-gray-100 select-none">
                                    <th class="p-4 w-12 text-center"><input type="checkbox" id="check_all_arsip" onclick="toggleSemuaArsip(this)" class="w-4 h-4 rounded text-blue-600 border-gray-300 focus:ring-blue-500 cursor-pointer"></th>
                                    <th class="p-4">No. Invoice</th>
                                    <th class="p-4">Pelanggan</th>
                                    <th class="p-4">Laptop</th>
                                    <th class="p-4 text-center">Tgl Booking</th>
                                    <th class="p-4 text-center">Tgl Kedatangan</th>
                                    <th class="p-4 text-center">Tgl Selesai</th>
                                    <th class="p-4 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 font-medium text-slate-700">
                                <?php if (mysqli_num_rows($result) > 0): ?>
                                    <?php while($row = mysqli_fetch_assoc($result)): ?>
                                        <tr class="hover:bg-slate-50/60 transition">
                                            <td class="p-4 text-center"><input type="checkbox" name="arsip_invoice_hapus[]" value="<?= htmlspecialchars($row['no_invoice']); ?>" onchange="hitungArsipTerpilih()" class="check_arsip_child w-4 h-4 rounded text-blue-600 border-gray-300 focus:ring-blue-500 cursor-pointer"></td>
                                            <td class="p-4 font-bold text-slate-400">#<?= htmlspecialchars($row['no_invoice']); ?></td>
                                            <td class="p-4">
                                                <div class="font-bold text-slate-900 uppercase text-[11px]"><?= htmlspecialchars($row['nama_pelanggan']); ?></div>
                                                <div class="text-[10px] text-slate-400"><i class="fab fa-whatsapp"></i> <?= htmlspecialchars($row['no_whatsapp']); ?></div>
                                            </td>
                                            <td class="p-4 font-bold text-slate-600 italic"><?= htmlspecialchars($row['laptop_detail']); ?></td>
                                            <td class="p-4 text-center"><?= date('d M Y', strtotime($row['created_at'])); ?></td>
                                            <td class="p-4 text-center font-bold text-slate-700"><?= date('d M Y', strtotime($row['tanggal_booking'])); ?></td>
                                            <td class="p-4 text-center font-bold text-emerald-600"><?= date('d M Y', strtotime($row['tanggal_selesai'])); ?></td>
                                            <td class="p-4 text-center">
                                                <a href="../customer/status_tracking.php?invoice=<?= urlencode($row['no_invoice']); ?>" target="_blank" class="w-8 h-8 bg-slate-100 text-slate-600 hover:bg-emerald-600 hover:text-white rounded-lg flex items-center justify-center text-xs transition mx-auto">
                                                    <i class="fas fa-print"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr><td colspan="8" class="p-8 text-center text-slate-400 font-bold">Tidak ada riwayat servis untuk periode ini.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div id="modal_hapus_massal_arsip" class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-50 hidden items-center justify-center p-4">
                    <div class="bg-white rounded-2xl max-w-sm w-full p-6 shadow-2xl border border-gray-100 space-y-4 text-center">
                        <div class="w-12 h-12 bg-red-50 text-red-500 rounded-full flex items-center justify-center text-xl mx-auto border border-red-100"><i class="fas fa-exclamation-triangle"></i></div>
                        <div class="space-y-1">
                            <h3 class="text-sm font-black uppercase text-slate-900 tracking-wide">Hapus Arsip</h3>
                            <p class="text-xs font-semibold text-slate-400 leading-relaxed">Yakin ingin hapus permanen (<span id="text_arsip_total" class="text-red-500 font-bold">0</span> nota) dari database?</p>
                        </div>
                        <div class="pt-2 flex gap-2">
                            <button type="button" onclick="tutupModalHapusMassal()" class="flex-1 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold py-2.5 rounded-xl transition uppercase tracking-wider text-[10px]">Batal</button>
                            <button type="submit" name="eksekusi_hapus_arsip_massal" class="flex-1 bg-red-500 hover:bg-red-600 text-white font-black py-2.5 rounded-xl transition uppercase tracking-wider text-[10px] shadow-sm flex items-center justify-center">Ya, Hapus</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </main>

    <script>
        function setMode(mode) {
            document.getElementById('mode_input').value = mode;
            document.getElementById('form_filter').submit();
        }
        function toggleSemuaArsip(master) {
            const checkboxes = document.querySelectorAll('.check_arsip_child');
            checkboxes.forEach(cb => cb.checked = master.checked);
            hitungArsipTerpilih();
        }
        function hitungArsipTerpilih() {
            const checkboxes = document.querySelectorAll('.check_arsip_child');
            let totalTerpilih = 0;
            checkboxes.forEach(cb => { if(cb.checked) totalTerpilih++; });
            const btnHapus = document.getElementById('btn_hapus_arsip_massal');
            document.getElementById('count_arsip_terpilih').innerText = totalTerpilih;
            if(totalTerpilih > 0) {
                btnHapus.classList.remove('hidden');
                setTimeout(() => { btnHapus.classList.remove('scale-95', 'opacity-0'); btnHapus.classList.add('scale-100', 'opacity-100'); }, 10);
            } else {
                btnHapus.classList.remove('scale-100', 'opacity-100'); btnHapus.classList.add('scale-95', 'opacity-0');
                setTimeout(() => { btnHapus.classList.add('hidden'); }, 300);
            }
        }
        function bukaModalHapusMassal() {
            const total = document.getElementById('count_arsip_terpilih').innerText;
            document.getElementById('text_arsip_total').innerText = total;
            document.getElementById('modal_hapus_massal_arsip').classList.remove('hidden');
            document.getElementById('modal_hapus_massal_arsip').classList.add('flex');
        }
        function tutupModalHapusMassal() {
            document.getElementById('modal_hapus_massal_arsip').classList.remove('flex');
            document.getElementById('modal_hapus_massal_arsip').classList.add('hidden');
        }
    </script>
</body>
</html>