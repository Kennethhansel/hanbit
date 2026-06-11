<?php
require_once 'config.php';
require_once 'koneksi.php';
proteksi_halaman();

$invoice_id = isset($_GET['invoice']) ? mysqli_real_escape_string($koneksi, trim($_GET['invoice'])) : '';

if (empty($invoice_id)) {
    header("Location: semua_pesanan.php");
    exit;
}

// -------------------------------------------------------------------------
// FUNGSI UTILITAS: Hitung Ulang Total Harga & Sinkronisasi ke Tabel Utama
// -------------------------------------------------------------------------
function sinkronkanTotalHargaReservasi($koneksi, $invoice) {
    // Hitung akumulasi total dari semua rincian komponen/jasa kustom di tabel invoice_details
    $q_hitung = mysqli_query($koneksi, "SELECT SUM(harga_item) as total_riil FROM invoice_details WHERE no_invoice = '$invoice'");
    $res_hitung = mysqli_fetch_assoc($q_hitung);
    $total_baru = $res_hitung['total_riil'] ?? 0;

    // Selalu perbarui total_harga di tabel reservations agar sinkron secara real-time
    mysqli_query($koneksi, "UPDATE reservations SET total_harga = '$total_baru' WHERE no_invoice = '$invoice'");
}

// -------------------------------------------------------------------------
// PROSES A: LOGIKA UPDATE STATUS (TANGGAL AUTOMATICALLY LOGGED VIA WORKFLOW)
// -------------------------------------------------------------------------
if (isset($_POST['update_antrean'])) {
    $status_baru = mysqli_real_escape_string($koneksi, $_POST['status_order']);
    $catatan     = mysqli_real_escape_string($koneksi, $_POST['catatan_teknisi'] ?? '');

    // Ambil data tanggal lama terlebih dahulu dari database untuk proteksi data log
    $q_tgl_lama = mysqli_query($koneksi, "SELECT tanggal_dikerjakan, tanggal_selesai FROM reservations WHERE no_invoice = '$invoice_id' LIMIT 1");
    $d_tgl_lama = mysqli_fetch_assoc($q_tgl_lama);
    
    $tgl_kerja   = $d_tgl_lama['tanggal_dikerjakan'] ? "'".$d_tgl_lama['tanggal_dikerjakan']."'" : "NULL";
    $tgl_selesai = $d_tgl_lama['tanggal_selesai'] ? "'".$d_tgl_lama['tanggal_selesai']."'" : "NULL";

    // AUTOMATION ENGINE: Sinkronisasi waktu siklus hidup pengerjaan laptop
    if (($status_baru === 'PENGECEKAN' || $status_baru === 'SEDANG DIKERJAKAN') && empty($d_tgl_lama['tanggal_dikerjakan'])) {
        $tgl_kerja = "'" . date('Y-m-d') . "'";
    }
    if ($status_baru === 'SELESAI' && empty($d_tgl_lama['tanggal_selesai'])) {
        $tgl_selesai = "'" . date('Y-m-d') . "'";
    }

    $q_up = "UPDATE reservations SET 
                status_order = '$status_baru', 
                tanggal_dikerjakan = $tgl_kerja, 
                tanggal_selesai = $tgl_selesai, 
                catatan_teknisi = '$catatan' 
             WHERE no_invoice = '$invoice_id'";
             
    if (mysqli_query($koneksi, $q_up)) {
        sinkronkanTotalHargaReservasi($koneksi, $invoice_id);
        header("Location: detail_update_pesanan.php?invoice=" . $invoice_id . "&notif=sukses_status");
        exit;
    }
}

// -------------------------------------------------------------------------
// PROSES B: LOGIKA TAMBAH ITEM DETAIL RINCIAN KOMPONEN / JASA BARU OLEH TEKNISI
// -------------------------------------------------------------------------
if (isset($_POST['tambah_komponen_kustom'])) {
    $nama_item  = mysqli_real_escape_string($koneksi, trim($_POST['nama_item']));
    $harga_item = intval($_POST['harga_item']);
    $desc_add   = mysqli_real_escape_string($koneksi, trim($_POST['deskripsi_tambahan']));

    if (!empty($nama_item) && $harga_item >= 0) {
        $q_ins = "INSERT INTO invoice_details (no_invoice, nama_item, harga_item, deskripsi_tambahan) VALUES ('$invoice_id', '$nama_item', $harga_item, '$desc_add')";
        if (mysqli_query($koneksi, $q_ins)) {
            sinkronkanTotalHargaReservasi($koneksi, $invoice_id);
            header("Location: detail_update_pesanan.php?invoice=" . $invoice_id . "&notif=sukses_item");
            exit;
        }
    }
}

// -------------------------------------------------------------------------
// 🔥 PROSES C: LOGIKA UTAMA EDIT / SESUAIKAN RINCIAN ITEM BREAKDOWN INVOICE
// -------------------------------------------------------------------------
if (isset($_POST['proses_edit_item_invoice'])) {
    $id_detail_edit = intval($_POST['id_detail_item']);
    $nama_item_edit = mysqli_real_escape_string($koneksi, trim($_POST['nama_item_edit']));
    $harga_item_edit = intval($_POST['harga_item_edit']);
    $desc_item_edit = mysqli_real_escape_string($koneksi, trim($_POST['deskripsi_tambahan_edit']));

    if (!empty($nama_item_edit)) {
        mysqli_query($koneksi, "UPDATE invoice_details SET nama_item='$nama_item_edit', harga_item=$harga_item_edit, deskripsi_tambahan='$desc_item_edit' WHERE id_detail=$id_detail_edit AND no_invoice='$invoice_id'");
        sinkronkanTotalHargaReservasi($koneksi, $invoice_id);
        header("Location: detail_update_pesanan.php?invoice=" . $invoice_id . "&notif=sukses_update_item");
        exit;
    }
}

// -------------------------------------------------------------------------
// PROSES D: LOGIKA HAPUS INDIVIDU ITEM DETAIL RINCIAN KOMPONEN / JASA
// -------------------------------------------------------------------------
if (isset($_GET['hapus_item_id'])) {
    $id_hapus = mysqli_real_escape_string($koneksi, $_GET['hapus_item_id']);
    
    $q_del = "DELETE FROM invoice_details WHERE id_detail = '$id_hapus' AND no_invoice = '$invoice_id'";
    if (mysqli_query($koneksi, $q_del)) {
        sinkronkanTotalHargaReservasi($koneksi, $invoice_id);
        header("Location: detail_update_pesanan.php?invoice=" . $invoice_id . "&notif=sukses_hapus");
        exit;
    }
}

// Fetch Data Utama untuk Ditampilkan di Form Atas
$query_detail = mysqli_query($koneksi, "SELECT * FROM reservations WHERE no_invoice = '$invoice_id' LIMIT 1");
$data = mysqli_fetch_assoc($query_detail);

if (!$data) {
    header("Location: semua_pesanan.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hanbit Admin - Update Progress Antrean</title>
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
            
            <div class="flex justify-between items-center border-b border-gray-200 pb-4">
                <div>
                    <a href="semua_pesanan.php" class="text-xs font-bold text-blue-500 hover:text-blue-600 flex items-center gap-1 mb-1">
                        <i class="fas fa-arrow-left text-[10px]"></i> Kembali ke Manajamen Antrean Pesanan
                    </a>
                    <h1 class="text-xl font-black tracking-tight text-slate-900 uppercase">Update Invoice #<?= htmlspecialchars($data['no_invoice']); ?></h1>
                </div>
                <span class="bg-slate-900 text-white font-mono text-xs font-black px-4 py-2 rounded-xl">
                    STATUS AKTIF: <?= htmlspecialchars($data['status_order']); ?>
                </span>
            </div>

            <?php if (isset($_GET['notif'])): ?>
                <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 text-xs font-bold px-4 py-3 rounded-xl flex items-center gap-2">
                    <i class="fas fa-check-circle text-emerald-500"></i>
                    <?php
                    if($_GET['notif'] == 'sukses_status') echo "Status antrean otomatis disinkronkan!";
                    if($_GET['notif'] == 'sukses_item') echo "Komponen/jasa kustom tambahan baru sukses dipasang ke invoice!";
                    if($_GET['notif'] == 'sukses_update_item') echo "Perubahan data harga/deskripsi item breakdown sukses diperbarui!";
                    if($_GET['notif'] == 'sukses_hapus') echo "Item rincian perbaikan berhasil dihapus dan total tagihan disinkronkan!";
                    ?>
                </div>
            <?php endif; ?>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
                
                <div class="lg:col-span-7 bg-white border border-gray-200/80 rounded-2xl p-6 shadow-sm space-y-5">
                    <h2 class="text-xs font-extrabold uppercase text-slate-400 tracking-wider flex items-center gap-1.5 border-b pb-2">
                        <i class="fas fa-sliders-h"></i> Parameter Informasi Teknisi
                    </h2>

                    <form action="" method="POST" class="space-y-4 text-xs font-bold text-slate-600">
                        <input type="hidden" name="update_antrean" value="1">

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="space-y-1.5">
                                <label class="text-slate-400 block uppercase tracking-wide text-[10px]">Identitas Pemilik Unit</label>
                                <input type="text" disabled value="<?= htmlspecialchars($data['nama_pelanggan']); ?> (<?= htmlspecialchars($data['no_whatsapp']); ?>)" class="w-full px-3 py-2.5 bg-slate-50 border border-gray-200 text-slate-400 font-semibold rounded-xl focus:outline-none cursor-not-allowed uppercase">
                            </div>
                            <div class="space-y-1.5">
                                <label class="text-slate-400 block uppercase tracking-wide text-[10px]">Merek & Series Unit Fisik</label>
                                <input type="text" disabled value="<?= htmlspecialchars($data['laptop_detail']); ?>" class="w-full px-3 py-2.5 bg-slate-50 border border-gray-200 text-slate-400 font-semibold rounded-xl focus:outline-none cursor-not-allowed uppercase italic">
                            </div>
                        </div>

                        <div class="space-y-1.5">
                            <label class="text-slate-500 block uppercase tracking-wide text-[10px]">Tahapan Status Antrean</label>
                            <select name="status_order" required class="w-full px-3 py-2.5 bg-slate-50 border border-gray-200 rounded-xl focus:outline-none focus:bg-white focus:border-blue-400 cursor-pointer transition text-slate-700 font-extrabold uppercase text-[11px]">
                                <option value="PENDING" <?= $data['status_order'] == 'PENDING_ADMIN' ? 'selected' : ''; ?>>🔵 Pending</option>
                                <option value="PENGECEKAN" <?= $data['status_order'] == 'PENGECEKAN' ? 'selected' : ''; ?>>🟡 Pengecekan Fisik</option>
                                <option value="PERBAIKAN" <?= $data['status_order'] == 'PERBAIKAN' ? 'selected' : ''; ?>>🔴 Proses Eksekusi Perbaikan</option>
                                <option value="SELESAI" <?= $data['status_order'] == 'SELESAI' ? 'selected' : ''; ?>>🟢 Selesai</option>
                            </select>
                        </div>

                        <div class="grid grid-cols-3 gap-2 text-center bg-slate-50 p-3.5 rounded-xl border font-bold text-slate-700 text-[10px] select-none">
                            <div>
                                <span class="text-slate-400 block uppercase tracking-wider text-[8px] mb-0.5">1. Tgl Booking</span>
                                <code class="text-slate-900 font-mono text-[11px]"><?= date('d/m/Y', strtotime($data['created_at'])); ?></code>
                            </div>
                            <div>
                                <span class="text-slate-400 block uppercase tracking-wider text-[8px] mb-0.5">2. Tgl Mulai Kerja</span>
                                <code class="text-blue-600 font-mono text-[11px]"><?= (!empty($data['tanggal_dikerjakan']) && $data['tanggal_dikerjakan'] !== '0000-00-00') ? date('d/m/Y', strtotime($data['tanggal_dikerjakan'])) : 'Belum Mulai'; ?></code>
                            </div>
                            <div>
                                <span class="text-slate-400 block uppercase tracking-wider text-[8px] mb-0.5">3. Tgl Selesai QC</span>
                                <code class="text-emerald-600 font-mono text-[11px]"><?= (!empty($data['tanggal_selesai']) && $data['tanggal_selesai'] !== '0000-00-00') ? date('d/m/Y', strtotime($data['tanggal_selesai'])) : 'Dalam Proses'; ?></code>
                            </div>
                        </div>

                        <div class="space-y-1.5">
                            <label class="text-slate-500 block uppercase tracking-wide text-[10px]">Catatan Internal Teknisi Laptop</label>
                            <textarea name="catatan_teknisi" rows="4" placeholder="Tulis rincian keluhan nyata atau catatan komponen di sini..." class="w-full px-4 py-3 bg-slate-50 border border-gray-200 text-slate-700 font-medium rounded-xl focus:outline-none focus:bg-white focus:border-blue-400 transition leading-relaxed resize-none"><?= htmlspecialchars($data['catatan_teknisi'] ?? ''); ?></textarea>
                        </div>

                        <div class="flex gap-2 pt-2">
                            <button type="submit" class="w-full bg-slate-900 hover:bg-slate-800 text-white py-3 rounded-xl transition uppercase tracking-wider text-[11px] font-black shadow-sm">
                                💾 Simpan Pembaruan
                            </button>
                            <button type="button" onclick="kirimWaManual('<?= $data['no_whatsapp']; ?>', '<?= htmlspecialchars($data['nama_pelanggan'], ENT_QUOTES); ?>', '<?= $data['status_order']; ?>', '<?= $data['no_invoice']; ?>')" class="bg-emerald-500 hover:bg-emerald-600 text-white px-4 py-3 rounded-xl transition flex items-center justify-center text-sm shadow-sm" title="Kirim Notifikasi WA CRM">
                                <i class="fab fa-whatsapp text-lg"></i>
                            </button>
                        </div>
                    </form>
                </div>

                <div class="lg:col-span-5 space-y-6">
                    
                    <div class="bg-white border border-gray-200/80 rounded-2xl p-6 shadow-sm space-y-4">
                        <h2 class="text-xs font-extrabold uppercase text-slate-400 tracking-wider flex items-center gap-1.5 border-b pb-2">
                            <i class="fas fa-plus-circle"></i> Tambah Tindakan / Sparepart Baru
                        </h2>

                        <form action="" method="POST" class="space-y-3 text-xs font-bold text-slate-600">
                            <input type="hidden" name="tambah_komponen_kustom" value="1">
                            
                            <div class="space-y-1">
                                <label class="text-slate-500 uppercase tracking-wide text-[10px]">Nama Tindakan / Komponen</label>
                                <input type="text" name="nama_item" required placeholder="Contoh: Upgrade SSD NVMe 512GB" class="w-full px-3 py-2 bg-slate-50 border border-gray-200 text-slate-700 font-medium rounded-xl focus:outline-none focus:bg-white focus:border-blue-400 transition">
                            </div>

                            <div class="grid grid-cols-2 gap-2">
                                <div class="space-y-1">
                                    <label class="text-slate-500 uppercase tracking-wide text-[10px]">Harga (Rp)</label>
                                    <input type="number" name="harga_item" required placeholder="Contoh: 450000" class="w-full px-3 py-2 bg-slate-50 border border-gray-200 text-slate-700 font-bold rounded-xl focus:outline-none focus:bg-white focus:border-blue-400 transition">
                                </div>
                                <div class="space-y-1">
                                    <label class="text-slate-500 uppercase tracking-wide text-[10px]">Detail/Spesifikasi</label>
                                    <input type="text" name="deskripsi_tambahan" placeholder="Contoh: Merk V-GeN Original" class="w-full px-3 py-2 bg-slate-50 border border-gray-200 text-slate-700 font-medium rounded-xl focus:outline-none focus:bg-white focus:border-blue-400 transition">
                                </div>
                            </div>

                            <button type="submit" class="w-full bg-blue-500 hover:bg-blue-600 text-white py-2 rounded-xl transition uppercase tracking-wider text-[10px] font-black shadow-sm">
                                ➕ Tambah Item ke Invoice
                            </button>
                        </form>
                    </div>

                    <div class="bg-white border border-gray-200/80 rounded-2xl p-6 shadow-sm space-y-4">
                        <div class="flex justify-between items-center border-b pb-2">
                            <h2 class="text-xs font-extrabold uppercase text-slate-400 tracking-wider flex items-center gap-1.5">
                                <i class="fas fa-receipt"></i> Rincian Nota
                            </h2>
                            <span class="text-xs font-black text-blue-600 bg-blue-50 px-2 py-0.5 rounded-md">
                                Total: Rp <?= number_format($data['total_harga'], 0, ',', '.'); ?>
                            </span>
                        </div>

                        <div class="overflow-hidden rounded-xl border border-gray-100">
                            <table class="w-full text-left border-collapse text-[11px] font-bold">
                                <thead>
                                    <tr class="bg-slate-50 text-slate-400 border-b border-gray-100 uppercase text-[9px] tracking-wider select-none">
                                        <th class="p-3">Rincian Komponen / Tindakan</th>
                                        <th class="p-3 text-right w-24">Subtotal</th>
                                        <th class="p-3 text-center w-16">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-50 text-slate-600 font-semibold">
                                    <?php 
                                    $q_item = mysqli_query($koneksi, "SELECT * FROM invoice_details WHERE no_invoice = '$invoice_id' ORDER BY id_detail ASC");
                                    if (mysqli_num_rows($q_item) > 0):
                                        while ($row_item = mysqli_fetch_assoc($q_item)):
                                    ?>
                                        <tr class="hover:bg-slate-50/60 transition">
                                            <td class="p-3 uppercase">
                                                <div class="text-slate-800 font-bold leading-tight"><?= htmlspecialchars($row_item['nama_item']); ?></div>
                                                <div class="text-[9px] text-slate-400 lowercase font-medium mt-0.5 italic normal-case"><?= htmlspecialchars($row_item['deskripsi_tambahan'] ?? 'estimasi awal pilihan customer'); ?></div>
                                            </td>
                                            <td class="p-3 text-right text-slate-900 font-extrabold">Rp <?= number_format($row_item['harga_item'], 0, ',', '.'); ?></td>
                                            <td class="p-3 text-center space-x-1.5 shrink-0">
                                                <button type="button" onclick="bukaPopUpEditItem('<?= $row_item['id_detail']; ?>', '<?= htmlspecialchars($row_item['nama_item'], ENT_QUOTES); ?>', '<?= $row_item['harga_item']; ?>', '<?= htmlspecialchars($row_item['deskripsi_tambahan'] ?? '', ENT_QUOTES); ?>')" class="text-blue-500 hover:text-blue-700 transition" title="Edit Item">
                                                    <i class="fas fa-edit text-xs"></i>
                                                </button>
                                                <a href="detail_update_pesanan.php?invoice=<?= $invoice_id; ?>&hapus_item_id=<?= $row_item['id_detail']; ?>" 
                                                   onclick="return confirm('Apakah Anda yakin menghapus item tindakan ini? Total tagihan akan langsung dikalkulasi ulang otomatis.')" 
                                                   class="text-red-500 hover:text-red-700 transition" title="Hapus Item">
                                                    <i class="fas fa-times-circle text-xs"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php 
                                        endwhile;
                                    else:
                                    ?>
                                        <tr>
                                            <td colspan="3" class="p-4 text-center text-slate-400 italic font-medium text-[10px]">
                                                Belum ada rincian tindakan khusus. Silakan masukkan rincian di form atas.
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </main>

    <div id="modal_edit_item_breakdown" class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-50 hidden items-center justify-center px-4">
        <div class="bg-white rounded-[1.5rem] max-w-sm w-full p-6 shadow-2xl border border-gray-100 space-y-4">
            <div class="flex justify-between items-center border-b pb-2">
                <h3 class="text-xs font-black uppercase text-slate-900"><i class="fas fa-edit text-blue-500 mr-1"></i> Sesuaikan Poin Tagihan</h3>
                <button type="button" onclick="tutupPopUpEditItem()" class="text-slate-400 hover:text-slate-600"><i class="fas fa-times text-base"></i></button>
            </div>
            <form action="" method="POST" class="space-y-4 text-xs font-semibold text-slate-600">
                <input type="hidden" name="id_detail_item" id="edit_modal_id_detail">
                
                <div class="space-y-1">
                    <label class="text-[10px] font-bold text-slate-400 uppercase block">Nama Item/Tindakan</label>
                    <input type="text" name="nama_item_edit" id="edit_modal_nama_item" required class="w-full px-3 py-2 bg-slate-50 border rounded-xl font-bold text-slate-800 focus:outline-none focus:border-blue-400">
                </div>
                <div class="space-y-1">
                    <label class="text-[10px] font-bold text-slate-400 uppercase block">Harga Realisasi (Rp)</label>
                    <input type="number" name="harga_item_edit" id="edit_modal_harga_item" required class="w-full px-3 py-2 bg-slate-50 border rounded-xl font-black text-slate-900 focus:outline-none focus:border-blue-400">
                </div>
                <div class="space-y-1">
                    <label class="text-[10px] font-bold text-slate-400 uppercase block">Deskripsi / Detail Spesifikasi</label>
                    <input type="text" name="deskripsi_tambahan_edit" id="edit_modal_desc_item" class="w-full px-3 py-2 bg-slate-50 border rounded-xl font-medium text-slate-700 focus:outline-none focus:border-blue-400">
                </div>

                <div class="flex justify-end gap-2 pt-2 border-t">
                    <button type="button" onclick="tutupPopUpEditItem()" class="bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold px-4 py-2 rounded-xl transition text-[10px] uppercase">Batal</button>
                    <button type="submit" name="proses_edit_item_invoice" class="bg-blue-500 hover:bg-blue-600 text-white font-black px-5 py-2 rounded-xl transition text-[10px] uppercase shadow-sm">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function bukaPopUpEditItem(id, nama, harga, desc) {
            document.getElementById('edit_modal_id_detail').value = id;
            document.getElementById('edit_modal_nama_item').value = nama;
            document.getElementById('edit_modal_harga_item').value = harga;
            document.getElementById('edit_modal_desc_item').value = desc;
            
            const m = document.getElementById('modal_edit_item_breakdown');
            m.classList.remove('hidden'); m.classList.add('flex');
        }

        function tutupPopUpEditItem() {
            const m = document.getElementById('modal_edit_item_breakdown');
            m.classList.remove('flex'); m.classList.add('hidden');
        }

        function kirimWaManual(no_hp, nama, status, invoice) {
            let nomorBersih = no_hp.replace(/[^0-9]/g, '');
            if (nomorBersih.startsWith('0')) { nomorBersih = '62' + nomorBersih.slice(1); }
            let pesan = `Halo ${nama}, status pesanan Anda ${invoice} saat ini adalah: *${status}*. Terima kasih telah mempercayakan perbaikan laptop Anda di Hanbit Labs.`;
            let waUrl = "https://api.whatsapp.com/send?phone=" + nomorBersih + "&text=" + encodeURIComponent(pesan);
            window.open(waUrl, '_blank');
        }
    </script>
</body>
</html>