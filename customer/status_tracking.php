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
$lebar_aktif = "w-0";
$akses_invoice_terbuka = false; // Default awal terkunci demi keamanan

if ($data_pesanan) {
    $status_db_raw = strtoupper(trim($data_pesanan['status_order']));
    $status_teks = ($status_db_raw === 'PENDING_ADMIN') ? 'PENDING' : $status_db_raw;

    $is_custom_estimasi = ($data_pesanan['paket_tipe'] === 'custom_estimasi');

    // Format Tanggal Cantik Indonesia untuk Teks Realtime
    function formatTglIndo($dateStr)
    {
        if (empty($dateStr) || $dateStr === '0000-00-00') return '-';
        $bulan_list = ["01" => "Januari", "02" => "Februari", "03" => "Maret", "04" => "April", "05" => "Mei", "06" => "Juni", "07" => "Juli", "08" => "Agustus", "09" => "September", "10" => "Oktober", "11" => "November", "12" => "Desember"];
        $t = strtotime($dateStr);
        return date('j', $t) . " " . $bulan_list[date('m', $t)] . " " . date('Y', $t);
    }

    $tgl_booking_cpt = formatTglIndo($data_pesanan['created_at']);
    $tgl_kerja_cpt   = formatTglIndo($data_pesanan['tanggal_dikerjakan'] ?? '');
    $tgl_selesai_cpt = formatTglIndo($data_pesanan['tanggal_selesai'] ?? '');

    // Logika Catatan Teknisi Dinamis Lintas Status
    if (!empty($data_pesanan['catatan_teknisi'])) {
        $catatan_teknisi_tampil = $data_pesanan['catatan_teknisi'];
    } else {
        if ($status_teks == 'PENDING') {
            $catatan_teknisi_tampil = "Data pendaftaran berhasil divalidasi. Silakan serahkan unit laptop ke workshop Hanbit Labs sesuai jadwal kedatangan pilihan Anda.";
        } elseif ($status_teks == 'PENGECEKAN' || $status_teks == 'SEDANG DIKERJAKAN') {
            $catatan_teknisi_tampil = "Unit fisik laptop telah diterima oleh teknisi pada " . $tgl_kerja_cpt . ". Saat ini sedang dilakukan pembongkaran sasis untuk pengecekan fisik menyeluruh.";
        } elseif ($status_teks == 'PERBAIKAN') {
            $catatan_teknisi_tampil = "Unit telah lulus uji diagnosa awal dan kini masuk tahap proses eksekusi perbaikan intensif oleh tim teknisi Hanbit.";
        } elseif ($status_teks == 'SELESAI') {
            $catatan_teknisi_tampil = "Seluruh rangkaian pengerjaan laptop selesai total pada " . $tgl_selesai_cpt . ". Unit telah lulus uji QC stabilitas dan siap diambil kembali.";
        }
    }

    // Mengatur nomor step visual tracking bar
    if ($status_teks == 'PENDING') {
        $status_num = 1;
        $lebar_aktif = "w-0";
    } elseif ($status_teks == 'PENGECEKAN' || $status_teks == 'SEDANG DIKERJAKAN') {
        $status_num = 2;
        $lebar_aktif = "w-1/3";
    } elseif ($status_teks == 'PERBAIKAN') {
        $status_num = 3;
        $lebar_aktif = "w-2/3";
    } elseif ($status_teks == 'SELESAI') {
        $status_num = 4;
        $lebar_aktif = "w-full";
    }

    // Invoice dibuka di status Perbaikan/Selesai untuk Kustom Estimasi
    if (!$is_custom_estimasi) {
        $akses_invoice_terbuka = true;
    } else {
        if ($status_teks === 'PERBAIKAN' || $status_teks === 'SELESAI') {
            $akses_invoice_terbuka = true;
        } else {
            $akses_invoice_terbuka = false;
        }
    }

    // SINKRONISASI TOTAL HARGA LIVE DARI DATABASE UTAMA
    $harga_tampilan = 0;
    if ($is_custom_estimasi && $status_num < 3) {
        $invoice_clean = mysqli_real_escape_string($koneksi, $data_pesanan['no_invoice']);
        $q_sum_estimasi = mysqli_query($koneksi, "SELECT SUM(mm.harga_estimasi) as total_est 
             FROM invoice_details id 
             JOIN master_masalah mm ON id.nama_item = mm.nama_masalah 
             WHERE id.no_invoice = '$invoice_clean'");
        $res_sum_estimasi = mysqli_fetch_assoc($q_sum_estimasi);
        $harga_tampilan = ($res_sum_estimasi['total_est'] > 0) ? $res_sum_estimasi['total_est'] : $data_pesanan['total_harga'];
    } else {
        $harga_tampilan = $data_pesanan['total_harga'];
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
                <span class="text-3xl font-extrabold tracking-tight text-slate-900">Hanbit</span>
            </a>
            <div class="hidden md:flex items-center gap-8 text-sm font-semibold text-slate-600">
                <a href="#home" class="text-yellow-600 hover:text-yellow-500 transition">Home</a>
                <a href="katalog.php" class="hover:text-slate-900 transition">Katalog</a>
                <a href="#layanan" class="hover:text-slate-900 transition">Layanan</a>
                <a href="portofolio.php" class="hover:text-slate-900 transition">Portofolio</a>
                <a href="#kontak" class="hover:text-slate-900 transition">Kontak</a>
            </div>
            <a href="https://wa.me/6285159794427" target="_blank" class="bg-[#00e676] hover:bg-[#00c853] text-white text-xs font-bold px-4 py-2.5 rounded-xl flex items-center gap-2 shadow-md shadow-emerald-500/10 transition">
                <i class="fab fa-whatsapp text-sm"></i> Chat Via WA
            </a>
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
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">
                            <?= ($is_custom_estimasi && $status_num < 3) ? 'TOTAL ESTIMASI BIAYA SERVIS' : 'TOTAL TAGIHAN BIAYA SERVIS'; ?>
                        </p>
                        <h2 class="text-2xl font-black text-slate-900 mt-0.5">
                            <?= ($harga_tampilan == 0) ? 'Menunggu Cek Fisik' : 'Rp ' . number_format($harga_tampilan, 0, ',', '.'); ?>
                        </h2>
                    </div>

                    <div class="flex gap-3 shrink-0 w-full sm:w-auto">
                        <a href="https://wa.me/6285159794427" target="_blank" class="bg-[#00e676] hover:bg-[#00c853] text-white font-black text-xs uppercase px-5 py-3.5 rounded-xl flex items-center justify-center gap-2 tracking-wider shadow-sm transition select-none w-full sm:w-auto">
                            <i class="fab fa-whatsapp text-sm"></i> Chat Admin
                        </a>

                        <?php if ($akses_invoice_terbuka): ?>
                            <button type="button" onclick="bukaLembarInvoicePopup()" class="bg-emerald-600 hover:bg-emerald-700 text-white font-black text-xs uppercase px-5 py-3.5 rounded-xl flex items-center justify-center gap-2 tracking-wider shadow-sm transition select-none w-full sm:w-auto">
                                Lihat Invoice <i class="fas fa-file-invoice text-sm"></i>
                            </button>
                        <?php else: ?>
                            <button type="button" disabled class="bg-slate-200 text-slate-400 border border-slate-300 font-black text-xs uppercase px-5 py-3.5 rounded-xl flex items-center justify-center gap-2 tracking-wider cursor-not-allowed w-full sm:w-auto" title="Rincian nota invoice terkunci sementara selama proses analisa fisik.">
                                Invoice Terkunci <i class="fas fa-lock text-[11px]"></i>
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
                        <div class="border-b pb-3 text-slate-900 flex justify-between items-end">
                            <div>
                                <span class="text-[9px] text-slate-400 font-bold block uppercase tracking-wider">Invoice No:</span>
                                <span class="text-base font-black tracking-tight"><?= $data_pesanan['no_invoice']; ?></span>
                            </div>
                            <span class="text-[10px] font-bold text-slate-400 uppercase">Unit: <?= htmlspecialchars($data_pesanan['laptop_detail']); ?></span>
                        </div>

                        <div class="grid grid-cols-3 gap-2 bg-slate-50 p-3 rounded-xl border border-gray-100 text-[10px] font-bold text-center text-slate-700 select-none">
                            <div>
                                <span class="text-slate-400 block uppercase text-[8px] tracking-wider">1. Tgl Masuk</span>
                                <span class="text-slate-800 font-mono"><?= date('d/m/Y', strtotime($data_pesanan['created_at'])); ?></span>
                            </div>
                            <div>
                                <span class="text-slate-400 block uppercase text-[8px] tracking-wider">2. Tgl Mulai</span>
                                <span class="text-slate-800 font-mono"><?= (!empty($data_pesanan['tanggal_dikerjakan']) && $data_pesanan['tanggal_dikerjakan'] !== '0000-00-00') ? date('d/m/Y', strtotime($data_pesanan['tanggal_dikerjakan'])) : '-'; ?></span>
                            </div>
                            <div>
                                <span class="text-slate-400 block uppercase text-[8px] tracking-wider">3. Tgl Selesai</span>
                                <span class="text-slate-800 font-mono"><?= (!empty($data_pesanan['tanggal_selesai']) && $data_pesanan['tanggal_selesai'] !== '0000-00-00') ? date('d/m/Y', strtotime($data_pesanan['tanggal_selesai'])) : '-'; ?></span>
                            </div>
                        </div>

                        <div class="space-y-2">
                            <span class="text-[9px] text-slate-400 font-bold block uppercase tracking-wider mb-1">Rincian Item & Jasa:</span>
                            <table class="w-full table-fixed border-collapse bg-slate-50 rounded-xl">
                                <thead>
                                    <tr class="text-[9px] text-slate-400 uppercase tracking-wider border-b border-white">
                                        <th class="p-2.5 text-left pl-3">Item Tindakan / Deskripsi</th>
                                        <th class="p-2.5 text-right pr-3 w-28">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-white text-slate-600">
                                    <?php
                                    $q_det = mysqli_query($koneksi, "SELECT * FROM invoice_details WHERE no_invoice = '" . $data_pesanan['no_invoice'] . "' ORDER BY id_detail ASC");
                                    if (mysqli_num_rows($q_det) > 0):
                                        while ($det = mysqli_fetch_assoc($q_det)):
                                            $item_price = $det['harga_item'];
                                            if ($is_custom_estimasi && $status_num < 3) {
                                                $nama_item_clean = mysqli_real_escape_string($koneksi, $det['nama_item']);
                                                $q_harga_live = mysqli_query($koneksi, "SELECT harga_estimasi FROM master_masalah WHERE nama_masalah = '$nama_item_clean' LIMIT 1");
                                                $data_harga_live = mysqli_fetch_assoc($q_harga_live);
                                                if ($data_harga_live) {
                                                    $item_price = $data_harga_live['harga_estimasi'];
                                                }
                                            }
                                    ?>
                                            <tr class="align-middle">
                                                <td class="p-3 text-[11px] font-bold text-slate-800 uppercase">
                                                    <div><?= htmlspecialchars($det['nama_item']); ?></div>
                                                    <div class="text-[9px] text-slate-400 lowercase font-medium mt-0.5 italic normal-case"><?= htmlspecialchars($det['deskripsi_tambahan'] ?? ''); ?></div>
                                                </td>
                                                <td class="p-3 text-right text-[11px] font-black text-slate-900 pr-3">Rp <?= number_format($item_price, 0, ',', '.'); ?></td>
                                            </tr>
                                        <?php
                                        endwhile;
                                    else:
                                        ?>
                                        <tr class="align-middle">
                                            <td class="p-3 text-[11px] font-bold text-slate-400 italic pl-3">Biaya Jasa Cek Jalur & Diagnosa Utama</td>
                                            <td class="p-3 text-right text-[11px] font-black text-slate-900 pr-3">Rp 0</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>

                        <div class="bg-yellow-400/10 border-2 border-dashed border-yellow-400 rounded-xl p-4 flex justify-between items-center text-slate-800">
                            <div>
                                <span class="text-[9px] font-black uppercase tracking-wider block text-slate-500">
                                    <?= ($is_custom_estimasi && $status_num < 3) ? 'TOTAL ESTIMASI BIAYA' : 'TOTAL TAGIHAN AKHIR'; ?>
                                </span>
                                <span class="text-[10px] text-slate-400 font-medium">(Pelunasan di Kasir Toko Hanbit)</span>
                            </div>
                            <span class="text-xl font-black text-slate-950">
                                Rp <?= number_format($harga_tampilan, 0, ',', '.'); ?>
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

    <footer class="bg-[#1e293b] text-slate-300 pt-12 pb-6 border-t border-slate-800 mt-auto">
        <div class="max-w-7xl mx-auto px-6 text-center text-[11px] font-medium text-slate-500">
            &copy; 2026 Hanbit. All rights reserved.
        </div>
    </footer>

    <script>
        function bukaLembarInvoicePopup() {
            const modal = document.getElementById('modal_invoice_live');
            if (modal) {
                modal.classList.remove('hidden');
                modal.classList.add('flex');
            }
        }

        // 🔥 PERBAIKAN SINTAKS MUTLAK: Bebas typo petik dan garis miring luar halaman
        function tutupLembarInvoicePopup() {
            const modal = document.getElementById('modal_invoice_live');
            if (modal) {
                modal.classList.remove('flex');
                modal.classList.add('hidden');
            }
        }

        function cetakNotaGambar() {
            html2canvas(document.getElementById('invoice_print_card'), {
                scale: 3,
                useCORS: true,
                onclone: (clonedDoc) => {
                    const tombolSembunyi = clonedDoc.querySelector('.bg-slate-50.p-4.flex.justify-end');
                    if (tombolSembunyi) {
                        tombolSembunyi.style.display = 'none';
                    }
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