<?php
require_once 'config.php';
require_once 'koneksi.php';
proteksi_halaman();

// =========================================================================
// ENGINE SINKRONISASI MUTLAK: Menyimpan Perubahan Status ke DB & Tracking Customer
// =========================================================================
if (isset($_POST['update_status_instan'])) {
    $invoice_input = mysqli_real_escape_string($koneksi, trim($_POST['invoice']));
    $status_baru   = mysqli_real_escape_string($koneksi, trim($_POST['status_baru']));

    if ($status_baru === 'PENDING') {
        $status_baru = 'PENDING_ADMIN';
    }

    // Ambil data tanggal lama terlebih dahulu dari database untuk proteksi data log
    $q_tgl_lama = mysqli_query($koneksi, "SELECT tanggal_dikerjakan, tanggal_selesai FROM reservations WHERE no_invoice = '$invoice_input' LIMIT 1");
    $d_tgl_lama = mysqli_fetch_assoc($q_tgl_lama);
    
    $tgl_kerja   = $d_tgl_lama['tanggal_dikerjakan'] ? "'".$d_tgl_lama['tanggal_dikerjakan']."'" : "NULL";
    $tgl_selesai = $d_tgl_lama['tanggal_selesai'] ? "'".$d_tgl_lama['tanggal_selesai']."'" : "NULL";

    // 🔥 AUTOMATION LOGGER: Dropdown depan halaman utama sekarang resmi memicu pencatatan tanggal otomatis
    if (($status_baru === 'PENGECEKAN' || $status_baru === 'SEDANG DIKERJAKAN') && empty($d_tgl_lama['tanggal_dikerjakan'])) {
        $tgl_kerja = "'" . date('Y-m-d') . "'";
    }
    if ($status_baru === 'SELESAI' && empty($d_tgl_lama['tanggal_selesai'])) {
        $tgl_selesai = "'" . date('Y-m-d') . "'";
    }

    $query_update = "UPDATE reservations SET status_order = '$status_baru', tanggal_dikerjakan = $tgl_kerja, tanggal_selesai = $tgl_selesai WHERE no_invoice = '$invoice_input'";
    if (mysqli_query($koneksi, $query_update)) {
        echo "sukses";
    } else {
        echo "gagal";
    }
    exit;
}

// LOGIKA ENGINE: Proses Hapus Massal Berbasis Checkbox Array POST
if (isset($_POST['eksekusi_hapus_massal'])) {
    if (!empty($_POST['ids_hapus'])) {
        $ids = array_map(function ($id) use ($koneksi) {
            return "'" . mysqli_real_escape_string($koneksi, $id) . "'";
        }, $_POST['ids_hapus']);

        $set_ids = implode(',', $ids);
        $query_del = "DELETE FROM reservations WHERE no_invoice IN ($set_ids)";
        mysqli_query($koneksi, $query_del);
    }
    $view_asal = isset($_POST['view_asal']) ? trim($_POST['view_asal']) : 'aktif';
    header("Location: semua_pesanan.php?view=" . $view_asal);
    exit;
}

// Catch parameter filter pencarian, tab view, jenis paket, dan sorting tanggal
$search      = isset($_GET['search']) ? mysqli_real_escape_string($koneksi, trim($_GET['search'])) : '';
$view_tab    = isset($_GET['view']) ? trim($_GET['view']) : 'aktif';
$filter_tipe = isset($_GET['filter_tipe']) ? trim($_GET['filter_tipe']) : 'semua';
$sort_tgl    = isset($_GET['sort_tgl']) ? trim($_GET['sort_tgl']) : 'terbaru_booking';

// Fondasi Query Dasar
$query_str = "SELECT no_invoice, nama_pelanggan, no_whatsapp, laptop_detail, tanggal_booking, status_order, paket_tipe, created_at FROM reservations WHERE 1=1";

// 1. Kondisi Filter Tab View
if ($view_tab == 'hari_ini') {
    $query_str .= " AND DATE(created_at) = CURDATE()";
} elseif ($view_tab == 'datang_hari_ini') {
    $query_str .= " AND tanggal_booking = CURDATE()";
} else {
    $query_str .= " AND status_order != 'SELESAI'";
}

// 2. Kondisi Filter Input Pencarian (Invoice / Nama Pelanggan)
if (!empty($search)) {
    $query_str .= " AND (no_invoice LIKE '%$search%' OR nama_pelanggan LIKE '%$search%')";
}

// 3. Kondisi Filter Jenis Paket (Maintenance vs Custom)
if ($filter_tipe == 'maintenance') {
    $query_str .= " AND paket_tipe != 'custom_estimasi'";
} elseif ($filter_tipe == 'custom') {
    $query_str .= " AND paket_tipe = 'custom_estimasi'";
}

// 4. Kondisi Sorting Tanggal
if ($sort_tgl == 'menyerahkan_laptop') {
    $query_str .= " ORDER BY tanggal_booking ASC, created_at DESC";
} else {
    $query_str .= " ORDER BY created_at DESC, no_invoice DESC";
}

$result = mysqli_query($koneksi, $query_str);
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hanbit Admin - Antrean Pesanan Aktif</title>
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
                    <h1 class="text-2xl font-black tracking-tight text-slate-900">Manajemen Antrean Pesanan</h1>
                    <p class="text-xs text-slate-400 font-medium">Pantau progres pengerjaan fisik unit laptop dan kirim pembaruan status via WhatsApp CRM konsumen.</p>
                </div>

                <button type="button" id="btn_hapus_massal" onclick="bukaModalHapusMassal()"
                    class="hidden bg-red-500 hover:bg-red-600 text-white font-black px-6 py-2.5 rounded-xl text-xs uppercase tracking-wider transition-all duration-300 shadow-md flex items-center gap-2 shrink-0 transform scale-95 opacity-0">
                    <i class="fas fa-trash-alt text-xs"></i> Hapus Terpilih (<span id="count_terpilih">0</span>)
                </button>
            </div>

            <div class="bg-white border border-gray-200/80 p-4 rounded-2xl shadow-sm flex flex-col xl:flex-row justify-between items-center gap-4 text-xs font-bold">
                <div class="flex flex-wrap gap-1.5 w-full xl:w-auto">
                    <a href="semua_pesanan.php?view=aktif&filter_tipe=<?= $filter_tipe; ?>&sort_tgl=<?= $sort_tgl; ?>&search=<?= urlencode($search); ?>" class="px-4 py-2.5 rounded-xl transition <?= $view_tab == 'aktif' ? 'bg-slate-900 text-white shadow-sm' : 'bg-slate-50 text-slate-500 hover:bg-slate-100' ?>">
                        🔄 Antrean Berjalan
                    </a>
                    <a href="semua_pesanan.php?view=hari_ini&filter_tipe=<?= $filter_tipe; ?>&sort_tgl=<?= $sort_tgl; ?>&search=<?= urlencode($search); ?>" class="px-4 py-2.5 rounded-xl transition <?= $view_tab == 'hari_ini' ? 'bg-slate-900 text-white shadow-sm' : 'bg-slate-50 text-slate-500 hover:bg-slate-100' ?>">
                        📩 Booking Masuk Hari Ini
                    </a>
                    <a href="semua_pesanan.php?view=datang_hari_ini&filter_tipe=<?= $filter_tipe; ?>&sort_tgl=<?= $sort_tgl; ?>&search=<?= urlencode($search); ?>" class="px-4 py-2.5 rounded-xl transition <?= $view_tab == 'datang_hari_ini' ? 'bg-amber-500 text-slate-950 shadow-sm border border-amber-500' : 'bg-slate-50 text-slate-500 hover:bg-slate-100' ?>">
                        📦 Rencana Datang Hari Ini
                    </a>
                </div>

                <form id="form_filter_sistem" action="" method="GET" class="flex flex-col md:flex-row items-center gap-3 w-full xl:w-auto justify-end">
                    <input type="hidden" name="view" value="<?= $view_tab; ?>">

                    <div class="w-full md:w-56">
                        <select name="filter_tipe" onchange="document.getElementById('form_filter_sistem').submit()" class="w-full px-3 py-2 bg-slate-50 border border-gray-200 rounded-xl font-bold text-slate-600 focus:outline-none focus:bg-white cursor-pointer transition">
                            <option value="semua" <?= $filter_tipe == 'semua' ? 'selected' : ''; ?>>📦 Semua Jenis Layanan</option>
                            <option value="maintenance" <?= $filter_tipe == 'maintenance' ? 'selected' : ''; ?>>✨ Perawatan Paket</option>
                            <option value="custom" <?= $filter_tipe == 'custom' ? 'selected' : ''; ?>>🛠️ Kustom Kasus</option>
                        </select>
                    </div>

                    <div class="w-full md:w-52">
                        <select name="sort_tgl" onchange="document.getElementById('form_filter_sistem').submit()" class="w-full px-3 py-2 bg-slate-50 border border-gray-200 rounded-xl font-bold text-slate-600 focus:outline-none focus:bg-white cursor-pointer transition">
                            <option value="terbaru_booking" <?= $sort_tgl == 'terbaru_booking' ? 'selected' : ''; ?>>📅 Urut Booking Terbaru</option>
                            <option value="menyerahkan_laptop" <?= $sort_tgl == 'menyerahkan_laptop' ? 'selected' : ''; ?>>📅 Urut Jadwal Kedatangan</option>
                        </select>
                    </div>

                    <div class="relative w-full md:w-64">
                        <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                        <input type="text" id="search_input" name="search" value="<?= htmlspecialchars($search); ?>" placeholder="Cari Kode Invoice / Nama..." class="w-full pl-9 pr-3 py-2 bg-slate-50 border border-gray-200 rounded-xl font-medium focus:outline-none focus:border-blue-400 focus:bg-white transition text-slate-700">
                    </div>

                    <button type="button" onclick="jalankanCari()" class="w-full md:w-auto bg-blue-500 hover:bg-blue-600 text-white px-5 py-2 rounded-xl transition">Cari</button>
                </form>
            </div>

            <form id="form_antrean_massal" action="semua_pesanan.php" method="POST">
                <input type="hidden" name="view_asal" value="<?= $view_tab; ?>">

                <div class="bg-white border border-gray-200/80 rounded-2xl shadow-sm overflow-hidden">
                    <div class="p-4 border-b border-gray-100 bg-slate-50/50 flex justify-between items-center">
                        <h3 class="text-xs font-extrabold uppercase text-slate-900 tracking-wider">
                            <?php
                            if ($view_tab == 'hari_ini') echo '📋 Daftar Booking Masuk Hari Ini';
                            elseif ($view_tab == 'datang_hari_ini') echo '📋 Daftar Rencana Serah Unit Hari Ini';
                            else echo '📋 Antrean Servis Aktif';
                            ?>
                        </h3>
                        <span class="bg-slate-900 text-white text-[10px] font-bold px-2.5 py-0.5 rounded-full uppercase">
                            Total: <?= mysqli_num_rows($result); ?> Unit
                        </span>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse text-xs">
                            <thead>
                                <tr class="bg-slate-100/60 text-slate-400 font-bold uppercase border-b border-gray-100 select-none">
                                    <th class="p-4 w-12 text-center">
                                        <input type="checkbox" id="check_all_master" onclick="toggleSemuaCheckbox(this)" class="w-4 h-4 rounded text-blue-600 border-gray-300 focus:ring-blue-500 cursor-pointer">
                                    </th>
                                    <th class="p-4 w-30">No. Invoice</th>
                                    <th class="p-4">Identitas Pelanggan</th>
                                    <th class="p-4">Merek & Series Unit</th>
                                    <th class="p-4 w-32 text-center">Tanggal Booking</th>
                                    <th class="p-4 w-36 text-center">Jadwal Kedatangan</th>
                                    <th class="p-4 w-44 text-center">Ubah Status</th>
                                    <th class="p-4 w-16 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 font-medium text-slate-700">
                                <?php if (mysqli_num_rows($result) > 0): ?>
                                    <?php while ($row = mysqli_fetch_assoc($result)): ?>
                                        <tr class="hover:bg-slate-50/60 transition" id="row_<?= htmlspecialchars($row['no_invoice']); ?>">
                                            <td class="p-4 text-center">
                                                <input type="checkbox" name="ids_hapus[]" value="<?= htmlspecialchars($row['no_invoice']); ?>" onchange="hitungCheckboxTerpilih()" class="check_item_child w-4 h-4 rounded text-blue-600 border-gray-300 focus:ring-blue-500 cursor-pointer">
                                            </td>
                                            <td class="p-4 font-bold text-slate-400">#<?= htmlspecialchars($row['no_invoice']); ?></td>
                                            <td class="p-4">
                                                <div class="font-bold text-slate-900 uppercase text-[11px]"><?= htmlspecialchars($row['nama_pelanggan']); ?></div>
                                                <div class="text-[10px] text-slate-400 mt-0.5 inline-flex items-center gap-1">
                                                    <i class="fab fa-whatsapp text-emerald-500"></i> <?= htmlspecialchars($row['no_whatsapp']); ?>
                                                </div>
                                            </td>
                                            <td class="p-4">
                                                <div class="font-bold text-slate-700 uppercase italic text-[11px]"><?= htmlspecialchars($row['laptop_detail']); ?></div>
                                                <div class="text-[9px] font-bold uppercase tracking-wide mt-0.5 text-slate-400">
                                                    <?= ($row['paket_tipe'] == 'custom_estimasi') ? '🛠️ Kustom Kasus' : '✨ Perawatan Paket'; ?>
                                                </div>
                                            </td>
                                            <td class="p-4 text-center text-slate-500 font-medium">
                                                <?= !empty($row['created_at']) ? date('d M Y', strtotime($row['created_at'])) : '-'; ?>
                                            </td>
                                            <td class="p-4 text-center text-slate-500 font-semibold bg-slate-50/50">
                                                <?= date('d M Y', strtotime($row['tanggal_booking'])); ?>
                                            </td>
                                            <td class="p-4 text-center">
                                                <?php
                                                $curr_status = strtoupper(trim($row['status_order']));

                                                // Jika status masih bawaan pendaftaran customer (PENDING atau kosong), kotak select box berwarna abu-abu (Muted)
                                                if ($curr_status === 'PENDING' || $curr_status == '' || empty($curr_status)) {
                                                    $color_class = 'border-slate-200 text-slate-400 bg-slate-50/50 font-medium';
                                                }
                                                // Jika diubah oleh admin, warna disesuaikan seperti biasa
                                                elseif ($curr_status === 'PENDING_ADMIN') {
                                                    $color_class = 'border-blue-200 text-blue-600 bg-blue-50/20 font-extrabold';
                                                }
                                                elseif ($curr_status === 'PENGECEKAN' || $curr_status === 'SEDANG DIKERJAKAN') {
                                                    $color_class = 'border-amber-200 text-amber-600 bg-amber-50/20 font-extrabold';
                                                }
                                                elseif ($curr_status === 'PERBAIKAN') {
                                                    $color_class = 'border-orange-200 text-orange-600 bg-orange-50/20 font-extrabold';
                                                }
                                                elseif ($curr_status === 'SELESAI') {
                                                    $color_class = 'border-emerald-200 text-emerald-600 bg-emerald-50/20 font-extrabold';
                                                }
                                                ?>
                                                <select onchange="pemicuPerubahanStatus('<?= htmlspecialchars($row['no_invoice']); ?>', '<?= htmlspecialchars($row['no_whatsapp']); ?>', '<?= htmlspecialchars($row['nama_pelanggan'], ENT_QUOTES); ?>', '<?= htmlspecialchars($row['paket_tipe']); ?>', '<?= htmlspecialchars($row['laptop_detail'], ENT_QUOTES); ?>', this)"
                                                    data-status-asal="<?= $curr_status; ?>"
                                                    class="px-2.5 py-1.5 rounded-lg border text-[10px] font-extrabold uppercase focus:outline-none transition cursor-pointer <?= $color_class; ?>">
                                                    <option value="" <?= ($curr_status === 'PENDING' || $curr_status == '' || empty($curr_status)) ? 'selected' : ''; ?> class="text-slate-400 font-medium bg-white">-- Pilih Progres --</option>
                                                    <option value="PENDING" <?= ($curr_status === 'PENDING_ADMIN') ? 'selected' : ''; ?> class="text-blue-600 font-bold bg-white">Pending (Antrean)</option>
                                                    <option value="PENGECEKAN" <?= ($curr_status === 'PENGECEKAN' || $curr_status === 'SEDANG DIKERJAKAN') ? 'selected' : ''; ?> class="text-amber-600 font-bold bg-white">Pengecekan Fisik</option>
                                                    <option value="PERBAIKAN" <?= $curr_status === 'PERBAIKAN' ? 'selected' : ''; ?> class="text-orange-600 font-bold bg-white">Proses Perbaikan</option>
                                                    <option value="SELESAI" <?= $curr_status === 'SELESAI' ? 'selected' : ''; ?> class="text-emerald-600 font-bold bg-white">Selesai QC (Siap Ambil)</option>
                                                </select>
                                            </td>
                                            <td class="p-4 text-center">
                                                <a href="detail_update_pesanan.php?invoice=<?= $row['no_invoice']; ?>" class="text-blue-500 hover:text-blue-700 text-sm p-1.5 transition inline-block">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="8" class="p-8 text-center text-slate-400 font-bold">Tidak ada data antrean yang cocok dengan filter.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div id="modal_hapus_massal" class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-50 hidden items-center justify-center p-4">
                    <div class="bg-white rounded-2xl max-w-sm w-full p-6 shadow-2xl border border-gray-100 space-y-4 text-center">
                        <div class="w-12 h-12 bg-red-50 text-red-500 rounded-full flex items-center justify-center text-xl mx-auto border border-red-100">
                            <i class="fas fa-trash-alt"></i>
                        </div>
                        <div class="space-y-1">
                            <h3 class="text-sm font-black uppercase text-slate-900 tracking-wide">Hapus Massal Terpilih</h3>
                            <p class="text-xs font-semibold text-slate-400 leading-relaxed">Apakah Anda yakin ingin menghapus permanen seluruh data antrean (<span id="text_total_terpilih" class="text-red-500 font-bold">0</span> unit) yang telah dicentang?</p>
                        </div>
                        <div class="pt-2 flex gap-2">
                            <button type="button" onclick="tutupModalHapusMassal()" class="flex-1 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold py-2.5 rounded-xl transition uppercase tracking-wider text-[10px]">Batal</button>
                            <button type="submit" name="eksekusi_hapus_massal" class="w-full bg-red-500 hover:bg-red-600 text-white font-black py-2.5 rounded-xl transition uppercase tracking-wider text-[10px] shadow-sm flex items-center justify-center">Ya, Hapus Semua</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </main>

    <div id="modal_wa_editor" class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-50 hidden items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl border border-gray-100 space-y-4 flex flex-col">
            <div class="flex justify-between items-center border-b pb-3">
                <div class="flex items-center gap-2 text-emerald-600">
                    <i class="fab fa-whatsapp text-xl"></i>
                    <h3 class="text-sm font-black uppercase tracking-wider text-slate-900">Review Template Pesan WA</h3>
                </div>
                <button type="button" onclick="batalPerubahanStatus()" class="text-slate-400 hover:text-slate-600"><i class="fas fa-times text-base"></i></button>
            </div>
            <div class="space-y-3 text-xs font-semibold text-slate-600">
                <div class="grid grid-cols-2 gap-2 bg-slate-50 p-3 rounded-xl border border-slate-100">
                    <div><span class="text-[10px] text-slate-400 block uppercase">Tujuan Kirim:</span><span id="view_nama_pelanggan" class="text-slate-900 font-extrabold uppercase"></span></div>
                    <div><span class="text-[10px] text-slate-400 block uppercase">Invoice & Status Baru:</span><span id="view_status_baru" class="text-blue-600 font-bold uppercase"></span></div>
                </div>
                <div class="space-y-1">
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Isi Pesan Notifikasi:</label>
                    <textarea id="edit_isi_pesan" rows="6" class="w-full p-4 bg-slate-50 border border-gray-200 rounded-xl font-medium focus:outline-none focus:border-emerald-500 focus:bg-white transition text-slate-700 leading-relaxed font-sans resize-none"></textarea>
                </div>
            </div>
            <div class="pt-3 border-t flex justify-end gap-2">
                <button type="button" onclick="batalPerubahanStatus()" class="bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold px-4 py-2 rounded-xl transition uppercase tracking-wider text-[11px]">Batal</button>
                <button type="button" onclick="eksekusiSimpanDanKirimWA()" class="bg-[#00e676] hover:bg-[#00c853] text-white font-black px-5 py-2 rounded-xl transition uppercase tracking-wider text-[11px] flex items-center gap-1.5 shadow-sm">
                    Kirim & Update Status <i class="fas fa-paper-plane text-[10px]"></i>
                </button>
            </div>
        </div>
    </div>

    <script>
        let activeInvoice, activeWhatsApp, activeStatusBaru, elemenSelectAktif;

        function toggleSemuaCheckbox(master) {
            const checkboxes = document.querySelectorAll('.check_item_child');
            checkboxes.forEach(cb => cb.checked = master.checked);
            hitungCheckboxTerpilih();
        }

        function hitungCheckboxTerpilih() {
            const checkboxes = document.querySelectorAll('.check_item_child');
            let totalTerpilih = 0;
            checkboxes.forEach(cb => {
                if (cb.checked) totalTerpilih++;
            });
            const btnHapus = document.getElementById('btn_hapus_massal');
            document.getElementById('count_terpilih').innerText = totalTerpilih;
            if (totalTerpilih > 0) {
                btnHapus.classList.remove('hidden');
                setTimeout(() => {
                    btnHapus.classList.remove('scale-95', 'opacity-0');
                    btnHapus.classList.add('scale-100', 'opacity-100');
                }, 10);
            } else {
                btnHapus.classList.remove('scale-100', 'opacity-100');
                btnHapus.classList.add('scale-95', 'opacity-0');
                setTimeout(() => {
                    btnHapus.classList.add('hidden');
                }, 300);
                document.getElementById('check_all_master').checked = false;
            }
        }

        function bukaModalHapusMassal() {
            const total = document.getElementById('count_terpilih').innerText;
            document.getElementById('text_total_terpilih').innerText = total;
            document.getElementById('modal_hapus_massal').classList.remove('hidden');
            document.getElementById('modal_hapus_massal').classList.add('flex');
        }

        function tutupModalHapusMassal() {
            document.getElementById('modal_hapus_massal').classList.remove('flex');
            document.getElementById('modal_hapus_massal').classList.add('hidden');
        }

        function jalankanCari() {
            const s = document.getElementById('search_input').value;
            window.location.href = `semua_pesanan.php?view=<?= $view_tab; ?>&filter_tipe=<?= $filter_tipe; ?>&sort_tgl=<?= $sort_tgl; ?>&search=${encodeURIComponent(s)}`;
        }

        function pemicuPerubahanStatus(invoice, whatsapp, nama, paketTipe, laptop, selectElement) {
            activeInvoice = invoice;
            activeWhatsApp = whatsapp;
            activeStatusBaru = selectElement.value;
            elemenSelectAktif = selectElement;
            document.getElementById('view_nama_pelanggan').innerText = nama;
            document.getElementById('view_status_baru').innerText = `${invoice} ➔ ${activeStatusBaru === "" ? "Dikosongkan" : activeStatusBaru}`;
            const isCustom = (paketTipe === 'custom_estimasi');
            let templateChat = "";

            if (activeStatusBaru === 'PENDING') {
                templateChat = `Halo ${nama},\n\nTerima kasih telah melakukan booking di Hanbit Labs. Unit Anda terdaftar dengan nomor Invoice: ${invoice}.\n\nSilakan segera serahkan unit laptop *${laptop}* Anda ke toko kami untuk pemeriksaan fisik langsung. Terima kasih!`;
            } else if (activeStatusBaru === 'PENGECEKAN') {
                templateChat = isCustom ? `Halo ${nama},\n\nUnit laptop *${laptop}* dengan nomor Invoice: ${invoice} telah diterima oleh teknisi Hanbit Labs. Saat ini unit sedang dibongkar untuk proses pengecekan fisik dan diagnosa jalur kerusakan internal.` : `Halo ${nama},\n\nUnit laptop *${laptop}* dengan nomor Invoice: ${invoice} telah diterima oleh teknisi Hanbit Labs. Saat ini sedang dalam proses pembersihan komponen internal dan penggantian thermal paste berkala.`;
            } else if (activeStatusBaru === 'PERBAIKAN') {
                templateChat = isCustom ? `Halo ${nama},\n\nProses diagnosa selesai. Unit Anda dengan nomor Invoice: ${invoice} kini telah masuk tahap *PROSES PERBAIKAN* intensif oleh teknisi kami. Lembar rincian komponen sparepart terupdate kini sudah bisa Anda akses langsung secara live di halaman tracking website Hanbit Labs.` : `Halo ${nama},\n\nProses maintenance unit laptop *${laptop}* Anda dengan nomor Invoice: ${invoice} telah memasuki tahap akhir optimasi sistem operasi dan quality control stabilitas suhu panas.`;
            } else if (activeStatusBaru === 'SELESAI') {
                templateChat = `Halo ${nama},\n\nKabar gembira! Proses perbaikan unit laptop *${laptop}* dengan nomor Invoice: ${invoice} telah *SELESAI* dikerjakan seluruhnya.\n\nUnit telah lulus uji kelayakan QC dan siap diambil kembali di toko Hanbit Labs. Silakan tunjukkan nota bukti pendaftaran awal saat pengambilan di meja kasir. Terima kasih!`;
            } else {
                templateChat = `Halo ${nama}, status pembaruan unit laptop Anda dengan nomor Invoice: ${invoice} saat ini sedang ditinjau kembali oleh tim Hanbit Labs.`;
            }
            document.getElementById('edit_isi_pesan').value = templateChat;
            document.getElementById('modal_wa_editor').classList.remove('hidden');
            document.getElementById('modal_wa_editor').classList.add('flex');
        }

        function batalPerubahanStatus() {
            if (elemenSelectAktif) {
                elemenSelectAktif.value = elemenSelectAktif.getAttribute('data-status-asal');
            }
            document.getElementById('modal_wa_editor').classList.remove('flex');
            document.getElementById('modal_wa_editor').classList.add('hidden');
        }

        function eksekusiSimpanDanKirimWA() {
            const pesanFinal = document.getElementById('edit_isi_pesan').value;
            let nomorBersih = activeWhatsApp.replace(/[^0-9]/g, '');
            if (nomorBersih.startsWith('0')) {
                nomorBersih = '62' + nomorBersih.slice(1);
            }

            const formData = new FormData();
            formData.append('update_status_instan', '1');
            formData.append('invoice', activeInvoice);
            formData.append('status_baru', activeStatusBaru);

            fetch('semua_pesanan.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.text())
                .then(result => {
                    const waUrl = "https://api.whatsapp.com/send?phone=" + nomorBersih + "&text=" + encodeURIComponent(pesanFinal);
                    window.open(waUrl, '_blank');
                    window.location.reload();
                });
        }
    </script>
</body>

</html>