<?php
require_once 'config.php';
require_once 'koneksi.php';
proteksi_halaman();

if (isset($_POST['update_paket'])) {
    $id_paket       = intval($_POST['id_paket']);
    $nama_paket     = mysqli_real_escape_string($koneksi, trim($_POST['nama_paket']));
    $harga_kantoran = intval($_POST['harga_kantoran']);
    $harga_gaming   = intval($_POST['harga_gaming']);
    $garansi        = mysqli_real_escape_string($koneksi, trim($_POST['garansi']));
    $benefits       = mysqli_real_escape_string($koneksi, trim($_POST['benefits']));

    $query_update = "UPDATE master_packages SET 
                        nama_paket = '$nama_paket', 
                        harga_kantoran = $harga_kantoran, 
                        harga_gaming = $harga_gaming, 
                        garansi = '$garansi', 
                        benefits = '$benefits' 
                     WHERE id_paket = $id_paket";

    if (mysqli_query($koneksi, $query_update)) {
        header("Location: kelola_paket.php?status=sukses");
        exit;
    }
}

$cek_data = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM master_packages");
$row_cek = mysqli_fetch_assoc($cek_data);
if ($row_cek['total'] == 0) {
    mysqli_query($koneksi, "INSERT INTO master_packages (id_paket, kode_paket, nama_paket, harga_kantoran, harga_gaming, garansi, benefits) VALUES 
    (1, 'basic', 'Paket Basic (Cleaning & Pembersihan)', 75000, 100000, 'Garansi 7 Hari', 'Pembersihan debu & kotoran sasis bagian internal laptop\nPembersihan eksternal pada sela-sela tombol keyboard\nPembersihan bercak noda dan debu pada permukaan layar\nPelumasan poros kipas laptop agar putaran kembali senyap'),
    (2, 'standard', 'Paket Standard (Repaste & Maintenance)', 150000, 200000, 'Garansi 14 Hari', 'Pembersihan menyeluruh debu & kotoran pada komponen internal\nPembersihan sasis luar, sela-sela keyboard, dan layar laptop\nPenggantian Thermal Paste Premium untuk menurunkan suhu panas\nPembersihan mendalam pada bilah kipas (fan) & jalur heatsink\nPengecekan kesehatan hardware dasar & stabilitas software'),
    (3, 'premium', 'Paket Premium (Full Optimization)', 250000, 250000, 'Garansi 30 Hari', 'Semua layanan pembersihan mendalam pada Paket Standard\nPenggantian Thermal Paste performa tinggi (High-Performance)\nOptimasi penuh kecepatan sistem operasi (OS) agar anti-lemot\nPembaruan (Update) driver hardware dan aplikasi esensial\nLayanan Install Ulang OS Windows secara bersih (Optional)')");
}

$ambil_paket = mysqli_query($koneksi, "SELECT * FROM master_packages ORDER BY id_paket ASC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hanbit Admin - Kelola Paket Perawatan</title>
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
        <div class="max-w-full mx-auto px-2 space-y-6">
            
            <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-4 min-h-[56px]">
                <div>
                    <h1 class="text-2xl font-black tracking-tight text-slate-900">Kelola Paket Perawatan</h1>
                    <p class="text-xs text-slate-400 font-medium">Ubah nominal harga laptop kantoran, gaming, serta rincian keuntungan paket maintenance live.</p>
                </div>
            </div>

            <?php if (isset($_GET['status']) && $_GET['status'] == 'sukses'): ?>
                <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 text-xs font-bold px-4 py-3 rounded-xl shadow-sm flex items-center gap-2">
                    <i class="fas fa-check-circle text-emerald-500"></i> ✅ Perubahan detail dan harga paket berhasil disimpan dan disinkronkan ke sistem!
                </div>
            <?php endif; ?>

            <div class="grid grid-cols-1 gap-6">
                <?php while($pkg = mysqli_fetch_assoc($ambil_paket)): ?>
                    <div class="bg-white border border-gray-200/80 p-6 rounded-2xl shadow-sm space-y-4">
                        <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                            <h3 class="text-xs font-extrabold uppercase text-slate-900 tracking-wider flex items-center gap-2">
                                <i class="fas fa-box text-amber-500"></i> Kode Referensi: <code class="bg-slate-100 text-blue-600 px-1.5 py-0.5 rounded font-mono text-[11px] font-bold lowercase"><?= $pkg['kode_paket']; ?></code>
                            </h3>
                            <span class="bg-emerald-50 border border-emerald-100 text-emerald-700 px-2.5 py-0.5 rounded-lg font-bold text-[10px] uppercase tracking-wide flex items-center gap-1">
                                <i class="fas fa-shield-alt text-[9px]"></i> <?= $pkg['garansi']; ?>
                            </span>
                        </div>

                        <form action="" method="POST" class="grid grid-cols-1 md:grid-cols-12 gap-4 text-xs font-semibold text-slate-600">
                            <input type="hidden" name="id_paket" value="<?= $pkg['id_paket']; ?>">

                            <div class="md:col-span-6 space-y-1">
                                <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Nama Judul Paket</label>
                                <input type="text" name="nama_paket" value="<?= htmlspecialchars($pkg['nama_paket']); ?>" required class="w-full px-4 py-2.5 bg-slate-50 border border-gray-200 rounded-xl font-bold focus:outline-none focus:border-yellow-400 focus:bg-white transition text-slate-800">
                            </div>

                            <div class="md:col-span-3 space-y-1">
                                <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Harga Laptop Kantoran (Rp)</label>
                                <input type="number" name="harga_kantoran" value="<?= $pkg['harga_kantoran']; ?>" required class="w-full px-4 py-2.5 bg-slate-50 border border-gray-200 rounded-xl focus:outline-none focus:border-yellow-400 focus:bg-white transition text-slate-800 font-bold">
                            </div>

                            <div class="md:col-span-3 space-y-1">
                                <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Harga Laptop Gaming (Rp)</label>
                                <input type="number" name="harga_gaming" value="<?= $pkg['harga_gaming']; ?>" required class="w-full px-4 py-2.5 bg-slate-50 border border-gray-200 rounded-xl focus:outline-none focus:border-yellow-400 focus:bg-white transition text-slate-800 font-bold">
                            </div>

                            <div class="md:col-span-4 space-y-1">
                                <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Masa Berlaku Garansi</label>
                                <input type="text" name="garansi" value="<?= htmlspecialchars($pkg['garansi']); ?>" required class="w-full px-4 py-2.5 bg-slate-50 border border-gray-200 rounded-xl focus:outline-none focus:border-yellow-400 focus:bg-white transition text-slate-700">
                            </div>

                            <div class="md:col-span-8 space-y-1">
                                <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Layanan Keuntungan Paket (*Pisahkan Setiap Baris Benefit Baru)</label>
                                <textarea name="benefits" rows="3" required class="w-full px-4 py-2.5 bg-slate-50 border border-gray-200 rounded-xl font-medium focus:outline-none focus:border-yellow-400 focus:bg-white transition resize-none text-slate-600 leading-relaxed font-sans"><?= htmlspecialchars($pkg['benefits']); ?></textarea>
                            </div>

                            <div class="md:col-span-12 flex justify-end pt-3 border-t border-gray-100">
                                <button type="submit" name="update_paket" class="bg-slate-900 hover:bg-slate-800 text-white font-black text-[10px] uppercase px-5 py-3 rounded-xl tracking-wider transition shadow-sm flex items-center gap-1.5">
                                    <i class="fas fa-save text-[9px]"></i> Simpan Perubahan Paket
                                </button>
                            </div>
                        </form>
                    </div>
                <?php endwhile; ?>
            </div>

        </div>
    </main>

</body>
</html>