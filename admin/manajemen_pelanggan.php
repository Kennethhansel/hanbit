<?php
require_once 'config.php';
require_once 'koneksi.php';
proteksi_halaman();

// LOGIKA ENGINE: Proses Hapus Massal Database Customer via Form Post Checkbox
if (isset($_POST['eksekusi_hapus_customer_massal'])) {
    if (!empty($_POST['customers_id_hapus'])) {
        $ids = array_map('intval', $_POST['customers_id_hapus']);
        $set_ids = implode(',', $ids);
        $query_del = "DELETE FROM customers WHERE id_customer IN ($set_ids)";
        mysqli_query($koneksi, $query_del);
    }
    header("Location: manajemen_pelanggan.php");
    exit;
}

// Catch parameter filter pencarian dan parameter sorting loyalitas
$search = isset($_GET['search']) ? mysqli_real_escape_string($koneksi, trim($_GET['search'])) : '';
$sort_loyal = isset($_GET['sort_loyal']) ? trim($_GET['sort_loyal']) : 'tidak';

// Query Utama: Menghitung jumlah transaksi secara real-time dari tabel reservations berdasarkan id_customer
$query_str = "SELECT c.*, 
              (SELECT COUNT(*) FROM reservations r WHERE r.id_customer = c.id_customer) as total_transaksi 
              FROM customers c WHERE 1=1";

// Kondisi Filter Input Pencarian
if (!empty($search)) {
    $query_str .= " AND (c.nama_customer LIKE '%$search%' OR c.no_hp LIKE '%$search%' OR c.email LIKE '%$search%')";
}

// Kondisi Sorting Berdasarkan Jumlah Transaksi Terbanyak (Loyalitas Customer)
if ($sort_loyal == 'ya') {
    $query_str .= " ORDER BY total_transaksi DESC, c.nama_customer ASC";
} else {
    $query_str .= " ORDER BY c.nama_customer ASC";
}

$result = mysqli_query($koneksi, $query_str);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hanbit Admin - Manajemen Pelanggan</title>
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
        <div class="max-w-7xl mx-auto space-y-6">
            
            <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-4 min-h-[56px]">
                <div>
                    <h1 class="text-2xl font-black tracking-tight text-slate-900">Manajemen Database Pelanggan</h1>
                    <p class="text-xs text-slate-400 font-medium">Kelola berkas identitas konsumen internal & riwayat kontak CRM yang terdaftar dalam sistem Hanbit Labs.</p>
                </div>
                
                <div class="flex items-center gap-3 shrink-0">
                    <button type="button" id="btn_hapus_customer_massal" onclick="bukaModalHapusMassal()" 
                            class="hidden bg-red-500 hover:bg-red-600 text-white font-black px-6 py-2.5 rounded-xl text-xs uppercase tracking-wider transition-all duration-300 shadow-md flex items-center gap-2 transform scale-95 opacity-0">
                        <i class="fas fa-trash-alt text-xs"></i> Hapus Terpilih (<span id="count_customer_terpilih">0</span>)
                    </button>
                </div>
            </div>

            <div class="bg-white border border-gray-200/80 p-4 rounded-2xl shadow-sm flex flex-col md:flex-row justify-between items-center gap-4 text-xs font-bold">
                <div class="flex flex-wrap gap-2 w-full md:w-auto select-none">
                    <a href="manajemen_pelanggan.php?sort_loyal=tidak&search=<?= urlencode($search); ?>" class="px-4 py-2 rounded-xl border transition <?= $sort_loyal == 'tidak' ? 'bg-slate-900 text-white border-slate-900 shadow-sm' : 'bg-slate-50 border-gray-200 text-slate-500 hover:bg-slate-100' ?>">
                        🔤 Urut Abjad A-Z
                    </a>
                    <a href="manajemen_pelanggan.php?sort_loyal=ya&search=<?= urlencode($search); ?>" class="px-4 py-2 rounded-xl border transition <?= $sort_loyal == 'ya' ? 'bg-amber-500 text-slate-950 border-amber-500 shadow-sm' : 'bg-slate-50 border-gray-200 text-slate-500 hover:bg-slate-100' ?>">
                        🏆 Transaksi Terbanyak 
                    </a>
                </div>

                <div class="flex gap-2 w-full md:w-80 items-center">
                    <div class="relative w-full">
                        <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                        <input type="text" id="search_input" value="<?= htmlspecialchars($search); ?>" placeholder="Cari Pelanggan / Kontak..." class="w-full pl-9 pr-3 py-2 bg-slate-50 border border-gray-200 rounded-xl font-medium focus:outline-none focus:border-blue-400 focus:bg-white transition">
                    </div>
                    <button type="button" onclick="jalankanCari()" class="bg-blue-500 hover:bg-blue-600 text-white px-5 py-2 rounded-xl transition">Cari</button>
                </div>
            </div>

            <form id="form_customer_massal" action="manajemen_pelanggan.php" method="POST">
                
                <div class="bg-white border border-gray-200/80 rounded-2xl shadow-sm overflow-hidden">
                    <div class="p-4 border-b border-gray-100 bg-slate-50/50 flex justify-between items-center">
                        <h3 class="text-xs font-extrabold uppercase text-slate-900 tracking-wider">Daftar Konsumen Terdaftar</h3>
                        <span class="bg-slate-900 text-white text-[10px] font-bold px-2.5 py-0.5 rounded-full uppercase">
                            Total Record: <?= mysqli_num_rows($result); ?> Orang
                        </span>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse text-xs">
                            <thead>
                                <tr class="bg-slate-100/60 text-slate-400 font-bold uppercase border-b border-gray-100 select-none">
                                    <th class="p-4 w-12 text-center">
                                        <input type="checkbox" id="check_all_customer" onclick="toggleSemuaCustomer(this)" class="w-4 h-4 rounded text-blue-600 border-gray-300 focus:ring-blue-500 cursor-pointer">
                                    </th>
                                    <th class="p-4 w-32">ID Pelanggan</th>
                                    <th class="p-4">Nama Lengkap</th>
                                    <th class="p-4">Kontak WhatsApp</th>
                                    <th class="p-4">Alamat Email</th>
                                    <th class="p-4 w-36 text-center">Jumlah Transaksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 font-medium text-slate-700">
                                <?php if (mysqli_num_rows($result) > 0): ?>
                                    <?php while($row = mysqli_fetch_assoc($result)): ?>
                                        <tr class="hover:bg-slate-50/60 transition">
                                            <td class="p-4 text-center">
                                                <input type="checkbox" name="customers_id_hapus[]" value="<?= $row['id_customer']; ?>" onchange="hitungCustomerTerpilih()" class="check_customer_child w-4 h-4 rounded text-blue-600 border-gray-300 focus:ring-blue-500 cursor-pointer">
                                            </td>
                                            <td class="p-4 font-bold text-slate-400">#CS-<?= str_pad($row['id_customer'], 3, '0', STR_PAD_LEFT); ?></td>
                                            <td class="p-4 font-bold text-slate-900 uppercase text-[11px] tracking-wide">
                                                <?= htmlspecialchars($row['nama_customer']); ?>
                                            </td>
                                            <td class="p-4">
                                                <?php 
                                                    $raw_phone = $row['no_hp'];
                                                    $clean_phone = preg_replace('/[^0-9]/', '', $raw_phone);
                                                    if (strpos($clean_phone, '0') === 0) { $clean_phone = '62' . substr($clean_phone, 1); }
                                                ?>
                                                <a href="https://api.whatsapp.com/send?phone=<?= $clean_phone; ?>" target="_blank" class="inline-flex items-center gap-1 text-emerald-600 hover:text-emerald-700 font-bold bg-emerald-50 px-2.5 py-1 rounded-lg border border-emerald-100/40 text-[11px] transition shadow-xs">
                                                    <i class="fab fa-whatsapp text-sm"></i> <?= htmlspecialchars($row['no_hp']); ?>
                                                </a>
                                            </td>
                                            <td class="p-4 text-slate-500 font-semibold text-[11px]">
                                                <?= !empty($row['email']) ? htmlspecialchars($row['email']) : '<span class="text-slate-300 italic text-[10px]">Tidak ada email</span>'; ?>
                                            </td>
                                            <td class="p-4 text-center">
                                                <?php 
                                                $total_tx = intval($row['total_transaksi']);
                                                if ($total_tx >= 3) {
                                                    // Badge Emas Khusus Loyalitas Tinggi (Repeat Order >= 3 Kali)
                                                    $badge_class = "bg-amber-100 text-amber-800 border-amber-200";
                                                } elseif ($total_tx > 0) {
                                                    $badge_class = "bg-slate-100 text-slate-800 border-slate-200";
                                                } else {
                                                    $badge_class = "bg-gray-50 text-gray-400 border-gray-100";
                                                }
                                                ?>
                                                <span class="px-3 py-1 rounded-full border text-[11px] font-extrabold font-mono <?= $badge_class; ?>">
                                                    <?= $total_tx; ?> × Servis
                                                </span>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="6" class="p-8 text-center text-slate-400 font-bold italic">Tidak ada data pelanggan yang cocok dengan pencarian.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div id="modal_hapus_massal_customer" class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-50 hidden items-center justify-center p-4">
                    <div class="bg-white rounded-2xl max-w-sm w-full p-6 shadow-2xl border border-gray-100 space-y-4 text-center">
                        <div class="w-12 h-12 bg-red-50 text-red-500 rounded-full flex items-center justify-center text-xl mx-auto border border-red-100">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                        <div class="space-y-1">
                            <h3 class="text-sm font-black uppercase text-slate-900 tracking-wide">Hapus Pelanggan</h3>
                            <p class="text-xs font-semibold text-slate-400 leading-relaxed">Apakah Anda yakin menghapus permanen (<span id="text_customer_total" class="text-red-500 font-bold">0</span> orang) profil pelanggan terpilih dari database?</p>
                        </div>
                        <div class="pt-2 flex gap-2">
                            <button type="button" onclick="tutupModalHapusMassal()" class="flex-1 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold py-2.5 rounded-xl transition uppercase tracking-wider text-[10px]">Batal</button>
                            <button type="submit" name="eksekusi_hapus_customer_massal" class="flex-1 bg-red-500 hover:bg-red-600 text-white font-black py-2.5 rounded-xl transition uppercase tracking-wider text-[10px] shadow-sm flex items-center justify-center">Ya, Hapus</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </main>

    <script>
        function toggleSemuaCustomer(master) {
            const checkboxes = document.querySelectorAll('.check_customer_child');
            checkboxes.forEach(cb => cb.checked = master.checked);
            hitungCustomerTerpilih();
        }
        function hitungCustomerTerpilih() {
            const checkboxes = document.querySelectorAll('.check_customer_child');
            let totalTerpilih = 0;
            checkboxes.forEach(cb => { if(cb.checked) totalTerpilih++; });
            const btnHapus = document.getElementById('btn_hapus_customer_massal');
            document.getElementById('count_customer_terpilih').innerText = totalTerpilih;
            if(totalTerpilih > 0) {
                btnHapus.classList.remove('hidden');
                setTimeout(() => { btnHapus.classList.remove('scale-95', 'opacity-0'); btnHapus.classList.add('scale-100', 'opacity-100'); }, 10);
            } else {
                btnHapus.classList.remove('scale-100', 'opacity-100'); btnHapus.classList.add('scale-95', 'opacity-0');
                setTimeout(() => { btnHapus.classList.add('hidden'); }, 300);
                document.getElementById('check_all_customer').checked = false;
            }
        }
        function bukaModalHapusMassal() {
            const total = document.getElementById('count_customer_terpilih').innerText;
            document.getElementById('text_customer_total').innerText = total;
            document.getElementById('modal_hapus_massal_customer').classList.remove('hidden');
            document.getElementById('modal_hapus_massal_customer').classList.add('flex');
        }
        function tutupModalHapusMassal() {
            document.getElementById('modal_hapus_massal_customer').classList.remove('flex');
            document.getElementById('modal_hapus_massal_customer').classList.add('hidden');
        }
        function jalankanCari() {
            const s = document.getElementById('search_input').value;
            window.location.href = `manajemen_pelanggan.php?sort_loyal=<?= $sort_loyal; ?>&search=${encodeURIComponent(s)}`;
        }
    </script>
</body>
</html>