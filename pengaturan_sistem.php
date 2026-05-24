<?php
require_once 'config.php';
require_once 'koneksi.php';
proteksi_halaman();

$pesan_sukses = '';

// Logika PHP untuk memproses perubahan saat tombol simpan diklik
if (isset($_POST['simpan_pengaturan'])) {
    $kuota_baru = mysqli_real_escape_string($koneksi, $_POST['kuota_harian']);
    
    $update_query = "UPDATE system_settings 
                     SET Setting_Value = '$kuota_baru' 
                     WHERE Setting_Key = 'max_kuota_harian'";
                     
    if (mysqli_query($koneksi, $update_query)) {
        $pesan_sukses = "Pengaturan sistem berhasil diperbarui!";
    }
}

// Ambil data kuota harian yang sedang aktif saat ini di database
$query = "SELECT Setting_Value FROM system_settings WHERE Setting_Key = 'max_kuota_harian' LIMIT 1";
$result = mysqli_query($koneksi, $query);
$setting = mysqli_fetch_assoc($result);
$kuota_sekarang = $setting['Setting_Value'] ?? 5; // Default 5 jika kosong
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hanbit Adm - Pengaturan Sistem</title>
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
                <p class="text-[10px] font-black text-yellow-500 uppercase tracking-[0.3em] mb-1">Parameter Operasional</p>
                <h1 class="text-3xl font-black text-slate-800 uppercase italic tracking-tight">Pengaturan Sistem</h1>
            </div>
        </header>

        <?php if ($pesan_sukses): ?>
            <div class="max-w-2xl bg-emerald-50 border border-emerald-200 text-emerald-700 text-xs font-bold px-5 py-4 rounded-2xl mb-6 flex items-center gap-3 shadow-sm">
                <i class="fas fa-check-circle text-base"></i> <?= $pesan_sukses; ?>
            </div>
        <?php endif; ?>

        <div class="max-w-2xl bg-white rounded-[2.5rem] shadow-xl shadow-slate-200/50 border border-gray-100 overflow-hidden">
            <div class="p-6 border-b border-gray-100 bg-gray-50/50">
                <h2 class="text-sm font-black text-slate-700 uppercase tracking-wider italic">Konfigurasi Pembatasan Reservasi</h2>
            </div>
            
            <form action="" method="POST" class="p-8 space-y-6">
                <div>
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-2 ml-1">Maksimal Kuota Booking Per Hari</label>
                    <div class="relative max-w-xs">
                        <i class="fas fa-sliders-h absolute left-4 top-1/2 -translate-y-1/2 text-slate-300 text-sm"></i>
                        <input type="number" name="kuota_harian" min="1" max="100" value="<?= $kuota_sekarang; ?>" required 
                               class="w-full pl-11 pr-4 py-3.5 bg-gray-50 border border-transparent rounded-2xl text-sm focus:bg-white focus:ring-2 focus:ring-yellow-400 outline-none transition font-bold text-slate-800">
                    </div>
                    <p class="text-[11px] text-slate-400 mt-2 ml-1 leading-relaxed">
                        *Angka ini menentukan jumlah maksimal slot orderan masuk yang dapat diterima oleh sistem web customer dalam kurun waktu satu hari.
                    </p>
                </div>

                <hr class="border-gray-100">

                <div class="flex justify-start">
                    <button type="submit" name="simpan_pengaturan" class="bg-[#facc15] hover:bg-[#eab308] text-black font-black px-6 py-3.5 rounded-2xl text-xs uppercase italic tracking-widest transition-all duration-300 shadow-lg shadow-yellow-400/10 flex items-center gap-2">
                        <i class="fas fa-save text-xs"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </main>

</body>
</html>