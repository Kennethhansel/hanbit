<?php
// 1. BUAT KONEKSI KE DATABASE
$host = "localhost";
$user = "root";
$pass = "";
$db   = "db_hanbit";
$koneksi = mysqli_connect($host, $user, $pass, $db);

if (!$koneksi) {
    die("Koneksi database Hanbit gagal: " . mysqli_connect_error());
}

// 2. LOGIKA PENCARIAN KODE ORDER VIA GET
$kode_cari = isset($_GET['kode_order']) ? trim(mysqli_real_escape_string($koneksi, $_GET['kode_order'])) : '';
$data_order = null;
$error_msg = '';

if (!empty($kode_cari)) {
    // Memastikan strtoupper bawaan PHP berjalan dengan benar tanpa Fatal Error
    if (strpos(strtoupper($kode_cari), 'HB') !== false) {

        // DEFAULT MOCK-DATA: Kondisi laptop sedang dikerjakan (Sesuai gambar STATUS ORDER_2.jpg kamu)
        $data_order = [
            'kode' => strtoupper($kode_cari),
            'unit' => 'Asus Zenbook Pro',
            'status_teks' => 'SEDANG DIKERJAKAN',
            'status_num' => 2, // 1: Booking, 2: Pengecekan, 3: Perbaikan, 4: Selesai
            'estimasi_selesai' => '6 Mei 2026 (15:00)',
            'catatan_teknisi' => 'Pengecekan komponen motherboard selesai. Ditemukan short pada IC Power. Sedang melakukan pembersihan jalur sebelum penggantian part baru.',
            'total_harga' => 1150000,
            'rincian_invoice' => [
                'komponen' => 'Keyboard Replacement Original + Thermal Paste',
                'harga_komponen' => 950000,
                'jasa_pasang' => 200000,
                'total_bayar' => 1150000,
                'metode' => 'Cash di Toko / Transfer'
            ]
        ];

        // LOGIKA KHUSUS TESTING: Jika statusnya mau disimulasikan sudah SELESAI total
        // Coba ketik kode order akhiran angka 4 atau ketik #HB2026-FINAL saat demo
        if (strtoupper($kode_cari) == '#HB2026-FINAL' || substr($kode_cari, -1) == '4' || strtoupper($kode_cari) == '#HB2026-0042') {
            $data_order['status_teks'] = 'SELESAI';
            $data_order['status_num'] = 4;
            $data_order['catatan_teknisi'] = 'Perbaikan selesai seluruhnya. Unit baru telah diuji coba dan berfungsi 100% normal. Laptop siap diambil.';
        }
    } else {
        $error_msg = "Kode Order tidak ditemukan! Pastikan format benar (Contoh: #HB2026-0042).";
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
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght=400;500;600;700;800&display=swap');

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
    </style>
</head>

<body class="bg-[#f8fafc] text-slate-900 antialiased min-h-screen flex flex-col justify-between">

    <nav class="bg-white border-b border-gray-100 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-6 h-20 flex justify-between items-center">
            <a href="index.php" class="flex items-center gap-3 hover:opacity-90 transition select-none">
                <img src="../logo warna.png" alt="Logo Hanbit" class="w-10 h-10 object-contain">
                <span class="text-3xl font-extrabold tracking-tight text-slate-900">Hanbit</span>
            </a>
            <div class="hidden md:flex items-center gap-8 text-sm font-semibold text-slate-600">
                <a href="index.php" class="hover:text-yellow-600 transition">Home</a>
                <a href="index.php#layanan" class="hover:text-slate-900 transition">Katalog</a>
                <a href="index.php#layanan" class="hover:text-slate-900 transition">Layanan</a>
                <a href="index.php#kontak" class="hover:text-slate-900 transition">Kontak</a>
            </div>
            <a href="https://wa.me/6285159794427" target="_blank" class="bg-[#00e676] hover:bg-[#00c853] text-white text-xs font-bold px-4 py-2.5 rounded-xl flex items-center gap-2 shadow-sm transition">
                <i class="fab fa-whatsapp text-sm"></i> Chat Via WA
            </a>
        </div>
    </nav>

    <main class="max-w-4xl mx-auto w-full px-6 pt-10 pb-16 flex-1 flex flex-col justify-start gap-6">

        <div class="text-center space-y-1">
            <h1 class="text-3xl md:text-4xl font-black italic tracking-tight text-slate-900 uppercase">STATUS PENGERJAAN</h1>
            <p class="text-sm text-slate-400 font-medium">Lacak progres perbaikan laptop Anda secara real-time.</p>
        </div>

        <div class="max-w-xl w-full mx-auto bg-white p-4 rounded-[1.5rem] shadow-sm border border-gray-100 mb-2">
            <form action="status_tracking.php" method="GET" class="flex flex-col sm:flex-row gap-3">
                <div class="relative flex-1">
                    <i class="fas fa-ticket-alt absolute left-4 top-4 text-slate-400"></i>
                    <input type="text" name="kode_order" value="<?= htmlspecialchars($kode_cari); ?>" placeholder="Masukkan Nomor Tiket (Misal: #HB2026-0042)" required
                        class="w-full bg-slate-50 border border-slate-100 rounded-xl pl-11 pr-4 py-3.5 text-sm font-bold focus:outline-none focus:border-yellow-400 focus:bg-white transition duration-200">
                </div>
                <button type="submit" class="bg-black hover:bg-slate-900 text-white font-bold text-xs uppercase px-8 py-4 rounded-xl transition tracking-wider shrink-0">
                    Lacak
                </button>
            </form>
        </div>

        <?php if (!empty($error_msg)): ?>
            <div class="max-w-xl mx-auto w-full bg-rose-50 border border-rose-100 text-rose-600 rounded-xl p-4 text-center text-xs font-bold">
                <?= $error_msg; ?>
            </div>
        <?php endif; ?>

        <?php if ($data_order): ?>
            <div class="bg-white border border-gray-200 rounded-[2rem] shadow-2xl overflow-hidden max-w-3xl w-full mx-auto flex flex-col justify-between">

                <div class="bg-[#1e293b] py-6 px-6 md:px-8 flex flex-row justify-between items-center gap-4">
                    <div class="space-y-0.5">
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">NOMOR TIKET</p>
                        <h2 class="text-2xl md:text-3xl font-black text-[#ffd54f] tracking-tight"><?= $data_order['kode']; ?></h2>
                    </div>
                    <span class="bg-[#facc15] text-slate-950 text-[10px] font-black uppercase px-4 py-2 rounded-full tracking-wider shrink-0">
                        <?= $data_order['status_teks']; ?>
                    </span>
                </div>

                <div class="p-6 md:p-8 space-y-8 bg-white">

                    <div class="relative flex flex-row justify-between items-center w-full px-4 md:px-8">
                        <div class="absolute top-5 left-8 right-8 h-[3px] bg-gray-200 z-0"></div>
                        <?php
                        $persentase_garis = [1 => 'w-0', 2 => 'w-[33%]', 3 => 'w-[66%]', 4 => 'w-[88%]'];
                        $lebar_aktif = $persentase_garis[$data_order['status_num']] ?? 'w-0';
                        ?>
                        <div class="absolute top-5 left-8 right-8 h-[3px] bg-yellow-400 z-0 transition-all duration-500 <?= $lebar_aktif; ?>"></div>

                        <?php
                        $tahapan = [
                            1 => ['title' => 'Booking', 'icon' => 'fa-check'],
                            2 => ['title' => 'Pengecekan', 'icon' => 'fa-wrench'],
                            3 => ['title' => 'Perbaikan', 'icon' => 'fa-microchip'],
                            4 => ['title' => 'Selesai', 'icon' => 'fa-box-open']
                        ];

                        foreach ($tahapan as $step_idx => $node):
                            $is_passed = ($data_order['status_num'] >= $step_idx);
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
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">UNIT LAPTOP</p>
                            <p class="text-base font-extrabold text-slate-800"><?= htmlspecialchars($data_order['unit']); ?></p>
                        </div>
                        <div class="space-y-0.5">
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">ESTIMASI SELESAI</p>
                            <p class="text-base font-extrabold text-slate-800"><?= $data_order['estimasi_selesai']; ?></p>
                        </div>
                    </div>

                    <div class="bg-blue-50/60 border-l-4 border-blue-500 rounded-r-2xl p-5 space-y-2">
                        <div class="flex items-center gap-2 text-blue-600 text-xs font-extrabold uppercase tracking-wide">
                            <i class="fas fa-comment-dots text-sm"></i> Catatan Teknisi:
                        </div>
                        <p class="text-xs text-blue-800 font-medium italic leading-relaxed">
                            "<?= htmlspecialchars($data_order['catatan_teknisi']); ?>"
                        </p>
                    </div>
                </div>

                <div class="bg-[#f8fafc] border-t border-gray-100 p-6 flex flex-col sm:flex-row justify-between items-center gap-4">
                    <div>
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">TOTAL ESTIMASI HARGA</p>
                        <h2 class="text-2xl font-black text-slate-900 mt-0.5">Rp <?= number_format($data_order['total_harga'], 0, ',', '.'); ?></h2>
                    </div>

                    <div class="flex gap-3 shrink-0 w-full sm:w-auto">
                        <a href="https://wa.me/6285159794427" target="_blank" class="flex-1 sm:flex-none bg-[#00e676] hover:bg-[#00c853] text-white font-black text-xs uppercase px-5 py-3.5 rounded-xl flex items-center justify-center gap-2 tracking-wider shadow-sm transition shrink-0 select-none font-bold">
                            <i class="fab fa-whatsapp text-sm"></i> Hubungi Admin
                        </a>

                        <?php if ($data_order['status_num'] == 4): ?>
                            <button type="button" onclick="bukaModalInvoiceFinal()" class="flex-1 sm:flex-none bg-[#1e293b] hover:bg-slate-800 text-[#facc15] font-black text-xs uppercase px-5 py-3.5 rounded-xl flex items-center justify-center gap-2 tracking-wider shadow-sm transition shrink-0 select-none">
                                Lihat Invoice Final <i class="fas fa-file-invoice-dollar text-sm"></i>
                            </button>
                        <?php else: ?>
                            <button type="button" disabled class="flex-1 sm:flex-none bg-gray-100 text-gray-400 border border-gray-200/60 font-black text-xs uppercase px-5 py-3.5 rounded-xl flex items-center justify-center gap-2 cursor-not-allowed shrink-0 select-none opacity-60" title="Invoice final baru tersedia setelah status Selesai">
                                Invoice Terkunci <i class="fas fa-lock text-[10px]"></i>
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="text-center pt-2">
                <a href="index.php" class="text-amber-600 hover:text-amber-700 font-extrabold text-sm flex items-center justify-center gap-2 transition select-none">
                    <i class="fas fa-arrow-left"></i> Kembali ke Beranda
                </a>
            </div>
        <?php endif; ?>
    </main>

    <?php if ($data_order && $data_order['status_num'] == 4 && $data_order['rincian_invoice']):
        $inv = $data_order['rincian_invoice'];
    ?>
        <div id="modal_invoice_final" class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
            <div class="bg-white rounded-[2rem] max-w-xl w-full overflow-hidden shadow-2xl border border-gray-100 flex flex-col justify-between">
                <div class="bg-[#1e293b] p-6 text-white flex justify-between items-center">
                    <div class="flex items-center gap-2.5">
                        <img src="../logo warna.png" class="w-6 h-6 object-contain" alt="Logo">
                        <h3 class="text-base font-black tracking-tight text-[#facc15]">INVOICE FINAL #HANBIT</h3>
                    </div>
                    <button type="button" onclick="tutupModalInvoiceFinal()" class="text-slate-400 hover:text-white transition"><i class="fas fa-times text-lg"></i></button>
                </div>
                <div class="p-6 space-y-6 bg-white">
                    <div class="grid grid-cols-2 gap-4 text-xs font-semibold text-slate-500 border-b border-gray-50 pb-4">
                        <div>
                            <span class="block text-[10px] text-slate-400 uppercase font-bold tracking-wider">Kode Order:</span>
                            <span class="text-slate-800 font-extrabold"><?= $data_order['kode']; ?></span>
                        </div>
                        <div class="text-right">
                            <span class="block text-[10px] text-slate-400 uppercase font-bold tracking-wider">Metode Pelunasan:</span>
                            <span class="text-emerald-600 font-extrabold"><?= $inv['metode']; ?></span>
                        </div>
                    </div>
                    <div class="space-y-3">
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider"><i class="fas fa-clipboard-list text-amber-500 mr-1"></i> Detail Pembayaran Riil:</p>
                        <div class="bg-slate-50/70 rounded-xl p-4 space-y-2.5 border border-gray-100 text-xs">
                            <div class="flex justify-between items-center font-medium">
                                <span class="text-slate-600"><?= $inv['komponen']; ?></span>
                                <span class="text-slate-800 font-bold">Rp <?= number_format($inv['harga_komponen'], 0, ',', '.'); ?></span>
                            </div>
                            <div class="flex justify-between items-center font-medium border-t border-gray-100/70 pt-2.5">
                                <span class="text-slate-600">Jasa Teknisi & Pembersihan Mesin</span>
                                <span class="text-slate-800 font-bold">Rp <?= number_format($inv['jasa_pasang'], 0, ',', '.'); ?></span>
                            </div>
                        </div>
                    </div>
                    <div class="bg-yellow-400/10 border-2 border-dashed border-yellow-400 rounded-xl p-4 flex justify-between items-center">
                        <div>
                            <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider block">Total Pembayaran</span>
                            <span class="text-xs text-slate-400 font-medium font-semibold">(Lunas di Toko Hanbit)</span>
                        </div>
                        <span class="text-xl font-black text-slate-950">Rp <?= number_format($inv['total_bayar'], 0, ',', '.'); ?></span>
                    </div>
                </div>
                <div class="bg-slate-50 px-6 py-4 flex justify-end">
                    <button type="button" onclick="window.print()" class="bg-slate-700 hover:bg-slate-800 text-white text-xs font-bold px-4 py-2.5 rounded-xl flex items-center gap-2 transition shadow-sm">
                        <i class="fas fa-print"></i> Cetak PDF Invoice
                    </button>
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
        function bukaModalInvoiceFinal() {
            const modal = document.getElementById('modal_invoice_final');
            if (modal) modal.classList.remove('hidden');
        }

        function tutupModalInvoiceFinal() {
            const modal = document.getElementById('modal_invoice_final');
            if (modal) modal.classList.add('hidden');
        }
    </script>
</body>

</html>