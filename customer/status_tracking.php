<?php
// =========================================================================
// SISI BACKEND: PENCARIAN TIKET TRANSAKSI DARI DATABASE HANBIT LABS
// =========================================================================
require_once '../admin/koneksi.php';

$invoice_id = isset($_GET['invoice']) ? mysqli_real_escape_string($koneksi, trim($_GET['invoice'])) : '';
$data_pesanan = null;
$error_msg = '';

if (!empty($invoice_id)) {
    $query = "SELECT * FROM reservations WHERE no_invoice = '$invoice_id' LIMIT 1";
    $result = mysqli_query($koneksi, $query);
    $data_pesanan = mysqli_fetch_assoc($result);
    
    if (!$data_pesanan) {
        $error_msg = "Nomor Invoice tidak terdaftar! Periksa kembali kode unik Anda (Contoh: INV-20260605-XXXX).";
    }
}

// Inisialisasi awal variabel stepper progres bar
$status_num = 1; 
$status_teks = "PENDING";
$catatan_teknisi_tampil = "";
$lebar_aktif = "w-0"; // Menetapkan default lebar progress bar awal agar aman dari crash program

if ($data_pesanan) {
    // SINKRONISASI LOGIKA REFRESH ADMIN: Jika di DB bernilai 'PENDING_ADMIN', kita standarkan teksnya kembali menjadi 'PENDING' untuk konsumsi customer
    $status_db_raw = strtoupper(trim($data_pesanan['status_order']));
    $status_teks = ($status_db_raw === 'PENDING_ADMIN') ? 'PENDING' : $status_db_raw;
    
    $is_paket_maintenance = ($data_pesanan['paket_tipe'] !== 'custom_estimasi');
    
    // Format Tanggal Cantik Indonesia untuk Teks Realtime
    function formatTglIndo($dateStr) {
        if(empty($dateStr)) return '-';
        $bulan_list = ["01" => "Januari", "02" => "Februari", "03" => "Maret", "04" => "April", "05" => "Mei", "06" => "Juni", "07" => "Juli", "08" => "Agustus", "09" => "September", "10" => "Oktober", "11" => "November", "12" => "Desember"];
        $t = strtotime($dateStr);
        return date('j', $t) . " " . $bulan_list[date('m', $t)] . " " . date('Y', $t);
    }
    
    $tgl_booking_cpt = formatTglIndo($data_pesanan['tanggal_booking']);
    $tgl_kerja_cpt   = formatTglIndo($data_pesanan['tanggal_dikerjakan'] ?? '');
    $tgl_selesai_cpt = formatTglIndo($data_pesanan['tanggal_selesai'] ?? '');

    // 1. LOGIKA STATUS STEPPER & DESKRIPSI CATATAN TEKNISI
    if ($status_teks == 'PENDING') {
        $status_num = 1;
        $lebar_aktif = "w-0";
        $catatan_teknisi_tampil = "Data reservasi awal berhasil divalidasi. Silakan segera serahkan unit laptop ke toko Hanbit Labs pada tanggal " . $tgl_booking_cpt . " agar masuk antrean pembongkaran.";
    } elseif ($status_teks == 'PENGECEKAN' || $status_teks == 'SEDANG DIKERJAKAN') {
        $status_num = 2;
        $lebar_aktif = "w-1/3";
        $catatan_teknisi_tampil = $is_paket_maintenance 
            ? "Unit fisik laptop telah diterima teknisi Hanbit Labs. Saat ini sedang dilakukan pembongkaran dan pemeriksaan jalur komponen internal serta pembersihan debu sasis." 
            : "Unit fisik laptop telah diterima teknisi Hanbit Labs. Saat ini unit sedang dibongkar untuk proses pengecekan fisik dan diagnosa jalur kerusakan komponen internal.";
    } elseif ($status_teks == 'PERBAIKAN') {
        $status_num = 3;
        $lebar_aktif = "w-2/3";
        $catatan_teknisi_tampil = $is_paket_maintenance
            ? "Proses pembersihan selesai. Teknisi sedang melakukan tindakan penggantian thermal paste premium dan optimasi penuh performa sistem laptop Anda sejak " . $tgl_kerja_cpt . "."
            : (!empty($data_pesanan['catatan_teknisi']) ? $data_pesanan['catatan_teknisi'] : "Unit kini masuk tahap perbaikan intensif oleh teknisi sejak tanggal " . $tgl_kerja_cpt . ". Menunggu pemasangan komponen baru.");
    } elseif ($status_teks == 'SELESAI') {
        $status_num = 4;
        $lebar_aktif = "w-full";
        $catatan_teknisi_tampil = $is_paket_maintenance
            ? "Seluruh rangkaian perawatan berkala selesai pada " . $tgl_selesai_cpt . ". Unit sudah lulus QC suhu dingin dan siap diambil kembali di toko."
            : (!empty($data_pesanan['catatan_teknisi']) ? $data_pesanan['catatan_teknisi'] : "Seluruh perbaikan komponen selesai pada " . $tgl_selesai_cpt . ". Laptop berfungsi 100% normal dan siap diambil.");
    }

    // 2. ATURAN LOGIKA KUNCI INVOICE BARU SESUAI PERMINTAAN ADMIN
    // Jika Maintenance -> Selalu Terbuka. Jika Kustom -> Terbuka HANYA jika status sudah PERBAIKAN atau SELESAI
    if ($is_paket_maintenance) {
        $akses_invoice_terbuka = true;
    } else {
        $akses_invoice_terbuka = ($status_teks === 'PERBAIKAN' || $status_teks === 'SELESAI');
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hanbit - Status Pengerjaan</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght=400;500;600;700;800&display=swap');
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-[#f8fafc] text-slate-900 antialiased min-h-screen flex flex-col justify-between">

    <nav class="bg-white border-b border-gray-100 sticky top-0 z-40 shadow-sm">
        <div class="max-w-7xl mx-auto px-6 h-20 flex justify-between items-center">
            <a href="index.php" class="flex items-center gap-3 hover:opacity-90 transition select-none">
                <img src="../admin/images/logo warna.png" alt="Logo Hanbit" class="w-10 h-10 object-contain">
                <span class="text-3xl font-extrabold tracking-tight text-slate-900">Hanbit</span>
            </a>
            <div class="hidden md:flex items-center gap-8 text-sm font-semibold text-slate-600">
                <a href="index.php" class="hover:text-yellow-600 transition">Home</a>
                <a href="katalog.php" class="hover:text-slate-900 transition">Katalog</a>
                <a href="index.php#layanan" class="hover:text-slate-900 transition">Layanan</a>
                <a href="index.php#kontak" class="hover:text-slate-900 transition">Kontak</a>
            </div>
        </div>
    </nav>

    <main class="max-w-4xl mx-auto w-full px-6 pt-10 pb-16 flex-1 flex flex-col justify-start gap-6">
        <div class="text-center space-y-1 mb-4">
            <h1 class="text-3xl md:text-4xl font-black italic tracking-tight text-slate-900 uppercase">STATUS PENGERJAAN LAPTOP</h1>
            <p class="text-sm text-slate-400 font-medium">Pantau lembar proses pengerjaan servis fisik laptop secara berkala.</p>
        </div>

        <?php if (!empty($error_msg)): ?>
            <div class="max-w-xl mx-auto w-full bg-rose-50 border border-rose-100 text-rose-600 rounded-xl p-4 text-center text-xs font-bold">
                <?= $error_msg; ?>
            </div>
        <?php endif; ?>

        <?php if ($data_pesanan): ?>
            <div class="bg-white border border-gray-200 rounded-[2rem] shadow-2xl overflow-hidden max-w-3xl w-full mx-auto flex flex-col justify-between">
                
                <div class="bg-slate-900 py-6 px-6 md:px-8 flex flex-row justify-between items-center gap-4">
                    <div class="space-y-0.5">
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">NOMOR INVOICE</p>
                        <h2 class="text-2xl md:text-3xl font-black text-[#ffd54f] tracking-tight"><?= $data_pesanan['no_invoice']; ?></h2>
                    </div>
                    <span class="bg-[#facc15] text-slate-950 text-[10px] font-black uppercase px-4 py-2 rounded-full tracking-wider shrink-0">
                        <?= $status_teks; ?>
                    </span>
                </div>

                <div class="p-6 md:p-8 space-y-8 bg-white">
                    <div class="relative flex flex-row justify-between items-center w-full px-4 md:px-8">
                        <div class="absolute top-5 left-8 right-8 h-[3px] bg-gray-200 z-0"></div>
                        <div class="absolute top-5 left-8 right-8 h-[3px] bg-yellow-400 z-0 transition-all duration-500 <?= $lebar_aktif; ?>"></div>

                        <?php
                        $tahapan = [
                            1 => ['title' => 'Booking', 'icon' => 'fa-calendar-check'],
                            2 => ['title' => 'Pengecekan', 'icon' => 'fa-wrench'],
                            3 => ['title' => 'Perbaikan', 'icon' => 'fa-microchip'],
                            4 => ['title' => 'Selesai', 'icon' => 'fa-box-open']
                        ];

                        foreach ($tahapan as $step_idx => $node):
                            $is_passed = ($status_num >= $step_idx);
                            $bg_circle  = $is_passed ? 'bg-[#ffd54f] text-slate-900 shadow-md ring-4 ring-yellow-400/10' : 'bg-gray-200 text-gray-400';
                            $text_color = $is_passed ? 'text-slate-900 font-extrabold' : 'text-slate-400 font-bold';
                        ?>
                            <div class="flex flex-col items-center space-y-2 relative z-10">
                                <div class="w-10 h-10 rounded-full <?= $bg_circle; ?> flex items-center justify-center text-sm transition-all duration-300">
                                    <i class="fas <?= $node['icon']; ?>"></i>
                                </div>
                                <span class="text-[11px] uppercase tracking-normal <?= $text_color; ?>"><?= $node['title']; ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 pt-4 border-t border-gray-100">
                        <div class="space-y-0.5">
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">UNIT DEVICE LAPTOP</p>
                            <p class="text-base font-extrabold text-slate-800 uppercase"><?= htmlspecialchars($data_pesanan['laptop_detail']); ?></p>
                        </div>
                        <div class="space-y-0.5">
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">TIPE LAYANAN SISTEM</p>
                            <p class="text-sm font-extrabold text-slate-700 uppercase italic">
                                <?= ($data_pesanan['paket_tipe'] == 'custom_estimasi') ? '🛠️ Perbaikan Kasus Khusus (Kustom)' : '✨ Paket Perawatan ' . strtoupper($data_pesanan['paket_tipe']); ?>
                            </p>
                        </div>
                    </div>

                    <div class="bg-blue-50/60 border-l-4 border-blue-500 rounded-r-2xl p-5 space-y-2">
                        <div class="flex items-center gap-2 text-blue-600 text-xs font-extrabold uppercase tracking-wide">
                            <i class="fas fa-comment-dots text-sm"></i> Catatan Pembaruan Teknisi:
                        </div>
                        <p class="text-xs text-blue-800 font-medium italic leading-relaxed">
                            "<?= htmlspecialchars($catatan_teknisi_tampil); ?>"
                        </p>
                    </div>
                </div>

                <div class="bg-[#f8fafc] border-t border-gray-100 p-6 flex flex-col sm:flex-row justify-between items-center gap-4">
                    <div>
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">TOTAL ESTIMASI BIAYA SERVIS</p>
                        <h2 class="text-2xl font-black text-slate-900 mt-0.5">
                            <?= ($data_pesanan['total_harga'] == 0) ? 'Menunggu Cek Fisik' : 'Rp ' . number_format($data_pesanan['total_harga'], 0, ',', '.'); ?>
                        </h2>
                    </div>

                    <div class="flex gap-3 shrink-0 w-full sm:w-auto">
                        <a href="https://wa.me/6285159794427" target="_blank" class="flex-1 sm:flex-none bg-[#00e676] hover:bg-[#00c853] text-white font-black text-xs uppercase px-5 py-3.5 rounded-xl flex items-center justify-center gap-2 tracking-wider shadow-sm transition select-none">
                            <i class="fab fa-whatsapp text-sm"></i> Chat Admin
                        </a>

                        <?php if ($akses_invoice_terbuka): ?>
                            <button type="button" onclick="bukaLembarInvoicePopup()" class="flex-1 sm:flex-none bg-emerald-600 hover:bg-emerald-700 text-white font-black text-xs uppercase px-5 py-3.5 rounded-xl flex items-center justify-center gap-2 tracking-wider shadow-sm transition select-none">
                                Lihat Invoice <i class="fas fa-file-invoice text-sm"></i>
                            </button>
                        <?php else: ?>
                            <button type="button" disabled class="flex-1 sm:flex-none bg-gray-100 text-gray-400 border border-gray-200 font-black text-xs uppercase px-5 py-3.5 rounded-xl flex items-center justify-center gap-2 cursor-not-allowed select-none opacity-60" title="Untuk perbaikan kustom, rincian invoice terkunci sebelum status masuk Perbaikan">
                                Invoice Terkunci <i class="fas fa-lock text-[10px]"></i>
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </main>

    <?php if ($data_pesanan && $akses_invoice_terbuka): ?>
        <div id="modal_invoice_live" class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-50 hidden items-center justify-center p-4 overflow-y-auto">
            <div class="max-w-xl w-full flex flex-col gap-4 my-auto">
                <div id="invoice_print_card" class="bg-white rounded-[2rem] shadow-2xl border overflow-hidden flex flex-col justify-between">
                    <div class="bg-slate-900 py-5 px-6 text-white flex justify-between items-center">
                        <div class="flex items-center gap-2">
                            <i class="fas fa-file-invoice-dollar text-[#facc15] text-lg"></i>
                            <h3 class="text-sm font-black uppercase tracking-wider text-white">INVOICE RESMI #HANBIT</h3>
                        </div>
                        <button type="button" onclick="tutupLembarInvoicePopup()" class="text-slate-400 hover:text-white"><i class="fas fa-times text-base"></i></button>
                    </div>
                    
                    <div class="p-6 space-y-5 bg-white text-xs font-semibold text-slate-600">
                        <div class="border-b pb-3 text-slate-900">
                            <span class="text-[9px] text-slate-400 font-bold block uppercase tracking-wider">Invoice No:</span>
                            <span class="text-lg font-black tracking-tight"><?= $data_pesanan['no_invoice']; ?></span>
                        </div>

                        <table class="w-full table-fixed border-collapse">
                            <tbody>
                                <tr class="align-middle">
                                    <td class="w-1/2 text-left py-2"><span class="text-slate-400 font-bold uppercase text-[9px] tracking-wider">Nama Pelanggan</span></td>
                                    <td class="w-1/2 text-right py-2"><span class="text-slate-900 font-extrabold uppercase text-xs"><?= htmlspecialchars($data_pesanan['nama_pelanggan']); ?></span></td>
                                </tr>
                                <tr><td colspan="2" class="border-t border-gray-100 py-0"></td></tr>
                                <tr class="align-middle">
                                    <td class="w-1/2 text-left py-2"><span class="text-slate-400 font-bold uppercase text-[9px] tracking-wider">Unit Device Laptop</span></td>
                                    <td class="w-1/2 text-right py-2"><span class="text-slate-900 font-black uppercase italic text-xs"><?= htmlspecialchars($data_pesanan['laptop_detail']); ?></span></td>
                                </tr>
                                <tr><td colspan="2" class="border-t border-gray-100 py-0"></td></tr>
                                <tr class="align-middle">
                                    <td class="w-1/2 text-left py-2"><span class="text-slate-400 font-bold uppercase text-[9px] tracking-wider">Tanggal Dikerjakan</span></td>
                                    <td class="w-1/2 text-right py-2"><span class="text-slate-800 font-bold text-xs"><?= !empty($data_pesanan['tanggal_dikerjakan']) ? date('d M Y', strtotime($data_pesanan['tanggal_dikerjakan'])) : '-'; ?></span></td>
                                </tr>
                                <tr><td colspan="2" class="border-t border-gray-100 py-0"></td></tr>
                                <tr class="align-middle">
                                    <td class="w-1/2 text-left py-2"><span class="text-slate-400 font-bold uppercase text-[9px] tracking-wider">Tanggal Selesai</span></td>
                                    <td class="w-1/2 text-right py-2"><span class="text-slate-800 font-bold text-xs"><?= !empty($data_pesanan['tanggal_selesai']) ? date('d M Y', strtotime($data_pesanan['tanggal_selesai'])) : '-'; ?></span></td>
                                </tr>
                            </tbody>
                        </table>

                        <div class="border-t pt-4 space-y-2">
                            <span class="text-[9px] text-slate-400 font-bold block uppercase tracking-wider mb-1">Rincian Komponen & Jasa Perbaikan:</span>
                            <table class="w-full table-fixed border-collapse bg-slate-50 rounded-xl p-2">
                                <tbody>
                                    <?php
                                    $q_det = mysqli_query($koneksi, "SELECT * FROM invoice_details WHERE no_invoice = '".$data_pesanan['no_invoice']."' ORDER BY id_detail ASC");
                                    if (mysqli_num_rows($q_det) > 0):
                                        while($det = mysqli_fetch_assoc($q_det)):
                                    ?>
                                        <tr class="align-middle border-b border-white last:border-none">
                                            <td class="w-8/12 text-left p-3 text-[11px] font-bold text-slate-700 uppercase"><?= htmlspecialchars($det['nama_item']); ?></td>
                                            <td class="w-4/12 text-right p-3 text-[11px] font-black text-slate-900">Rp <?= number_format($det['harga_item'], 0, ',', '.'); ?></td>
                                        </tr>
                                    <?php 
                                        endwhile;
                                    else:
                                    ?>
                                        <tr class="align-middle">
                                            <td class="w-8/12 text-left p-3 text-[11px] font-bold text-slate-400 italic">Biaya Jasa Cek Jalur & Diagnosa Utama</td>
                                            <td class="w-4/12 text-right p-3 text-[11px] font-black text-slate-900">Rp 0</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>

                        <div class="bg-yellow-400/10 border-2 border-dashed border-yellow-400 rounded-xl p-4 flex justify-between items-center text-slate-800">
                            <div>
                                <span class="text-[9px] font-black uppercase tracking-wider block text-slate-500">Total Tagihan Finansial</span>
                                <span class="text-[10px] text-slate-400 font-medium">(Pelunasan Langsung di Meja Kasir Offline)</span>
                            </div>
                            <span class="text-xl font-black text-slate-950">
                                <?= ($data_pesanan['total_harga'] == 0) ? 'Cek Fisik Toko' : 'Rp ' . number_format($data_pesanan['total_harga'], 0, ',', '.'); ?>
                            </span>
                        </div>
                    </div>

                    <div class="bg-slate-50 p-4 flex justify-end gap-2 border-t">
                        <button type="button" onclick="cetakNotaGambar()" class="bg-slate-800 hover:bg-slate-900 text-white font-bold text-xs uppercase px-5 py-2.5 rounded-xl transition shadow-sm"><i class="fas fa-print mr-1"></i> Cetak Gambar</button>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <footer class="bg-[#1e293b] text-slate-300 pt-12 pb-6 border-t border-slate-800">
        <div class="max-w-7xl mx-auto px-6 text-center text-[11px] font-medium text-slate-500">
            &copy; 2026 Hanbit. All rights reserved.
        </div>
    </footer>

    <script>
        function bukaLembarInvoicePopup() {
            const modal = document.getElementById('modal_invoice_live');
            if (modal) { modal.classList.remove('hidden'); modal.classList.add('flex'); }
        }
        function tutupLembarInvoicePopup() {
            const modal = document.getElementById('modal_invoice_live');
            if (modal) { modal.classList.remove('flex'); modal.classList.add('hidden'); }
        }
        function cetakNotaGambar() {
            html2canvas(document.getElementById('invoice_print_card'), { 
                scale: 3, 
                useCORS: true,
                onclone: (clonedDoc) => {
                    const tombolSembunyi = clonedDoc.querySelector('.bg-slate-50.p-4.flex.justify-end');
                    if (tombolSembunyi) { tombolSembunyi.style.display = 'none'; }
                }
            }).then(canvas => {
                const link = document.createElement('a');
                link.href = canvas.toDataURL('image/png');
                link.download = 'Invoice_Final_<?= $invoice_id; ?>.png';
                link.click();
                alert("✅ Lembar Invoice Resmi Berhasil Diekspor!");
            });
        }
    </script>
</body>
</html>