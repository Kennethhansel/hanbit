<?php
require_once 'config.php';
require_once 'koneksi.php';
proteksi_halaman();

// Ambil ID Admin dari session login
$id_admin = $_SESSION['id_user'] ?? 1; 
$pesan_sukses = '';

// Logika PHP untuk memproses perubahan saat tombol simpan atau radio button diklik
if (isset($_POST['simpan_pengaturan']) || isset($_POST['ajax_action'])) {
    $kuota_baru    = mysqli_real_escape_string($koneksi, $_POST['kuota_harian'] ?? 50);
    $status_baru   = mysqli_real_escape_string($koneksi, $_POST['status_toko']);
    $jam_buka_baru = mysqli_real_escape_string($koneksi, $_POST['jam_buka_store'] ?? '09:00');
    $jam_tutup_baru= mysqli_real_escape_string($koneksi, $_POST['jam_tutup_store'] ?? '18:00');
    $pesan_baru    = mysqli_real_escape_string($koneksi, $_POST['pesan_penutupan'] ?? '');
    
    $update_query = "UPDATE admin_accounts SET 
                        max_kuota_harian = '$kuota_baru',
                        status_toko = '$status_baru',
                        jam_buka_store = '$jam_buka_baru',
                        jam_tutup_store = '$jam_tutup_baru',
                        pesan_penutupan = '$pesan_baru'
                     WHERE id_admin = '$id_admin'";
                     
    if (mysqli_query($koneksi, $update_query)) {
        if (isset($_POST['simpan_pengaturan'])) {
            $pesan_sukses = "Konfigurasi operasional toko berhasil diperbarui!";
        }
    }
}

// Ambil data konfigurasi operasional terupdate
$query = "SELECT max_kuota_harian, status_toko, jam_buka_store, jam_tutup_store, pesan_penutupan FROM admin_accounts WHERE id_admin = '$id_admin' LIMIT 1";
$result = mysqli_query($koneksi, $query);
$setting = mysqli_fetch_assoc($result);

$kuota_sekarang  = $setting['max_kuota_harian'] ?? 50;
$status_sekarang = $setting['status_toko'] ?? 'buka';
$jam_buka_skrg   = $setting['jam_buka_store'] ?? '09:00';
$jam_tutup_skrg  = $setting['jam_tutup_store'] ?? '18:00';
$pesan_sekarang  = $setting['pesan_penutupan'] ?? 'Maaf, Hanbit sedang tidak menerima antrean perbaikan untuk sementara waktu.';
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
                <h2 class="text-sm font-black text-slate-700 uppercase tracking-wider italic">Konfigurasi Operasional Hanbit</h2>
            </div>
            
            <form id="formPengaturan" action="" method="POST" class="p-8 space-y-6">
                <input type="hidden" name="ajax_action" id="ajax_action" value="">

                <div>
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-3 ml-1">Status Akses Reservasi Web</label>
                    <div class="grid grid-cols-2 gap-4 max-w-sm">
                        <div onclick="submitStatusToko('buka')" 
                             class="flex items-center justify-center gap-2 p-3 border-2 rounded-xl cursor-pointer font-bold text-xs uppercase transition-all duration-200 select-none <?= $status_sekarang == 'buka' ? 'border-yellow-400 bg-yellow-50/40 text-slate-900 shadow-sm' : 'border-gray-100 text-slate-400 bg-gray-50/50 hover:border-gray-200' ?>">
                            <input type="radio" name="status_toko" value="buka" class="hidden" <?= $status_sekarang == 'buka' ? 'checked' : '' ?>>
                            <i class="fas fa-door-open"></i> Website Buka
                        </div>
                        <div onclick="submitStatusToko('tutup')" 
                             class="flex items-center justify-center gap-2 p-3 border-2 rounded-xl cursor-pointer font-bold text-xs uppercase transition-all duration-200 select-none <?= $status_sekarang == 'tutup' ? 'border-red-400 bg-red-50/40 text-red-700 shadow-sm' : 'border-gray-100 text-slate-400 bg-gray-50/50 hover:border-gray-200' ?>">
                            <input type="radio" name="status_toko" value="tutup" class="hidden" <?= $status_sekarang == 'tutup' ? 'checked' : '' ?>>
                            <i class="fas fa-door-closed"></i> Website Tutup
                        </div>
                    </div>
                </div>

                <hr class="border-gray-100">

                <?php if($status_sekarang == 'tutup'): ?>
                <div class="bg-red-50/40 p-5 border border-red-100 rounded-2xl space-y-2.5">
                    <label class="text-[10px] font-black text-red-700 uppercase tracking-widest block ml-1">Pesan Pengumuman Libur (Terlihat di Customer)</label>
                    <textarea name="pesan_penutupan" rows="2" required 
                              class="w-full p-4 bg-white border border-gray-200 rounded-xl text-xs font-semibold text-slate-700 focus:ring-2 focus:ring-red-400 outline-none transition shadow-sm"><?= htmlspecialchars($pesan_sekarang); ?></textarea>
                </div>
                <hr class="border-gray-100">
                <?php else: ?>
                    <input type="hidden" name="pesan_penutupan" value="<?= htmlspecialchars($pesan_sekarang); ?>">
                <?php endif; ?>

                <div>
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-2 ml-1">Maksimal Kuota Booking Per Hari</label>
                    <div class="relative max-w-xs">
                        <i class="fas fa-sliders-h absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                        <input type="number" name="kuota_harian" min="1" max="200" value="<?= $kuota_sekarang; ?>" required 
                               class="w-full pl-11 pr-4 py-3 bg-gray-50 border border-gray-100 rounded-2xl text-sm focus:bg-white focus:ring-2 focus:ring-yellow-400 outline-none transition font-bold text-slate-800 shadow-sm">
                    </div>
                </div>

                <hr class="border-gray-100">

                <div>
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-3 ml-1">Jam Operasional & Batas Penyerahan Unit</label>
                    <div class="grid grid-cols-2 gap-4 max-w-md">
                        <div>
                            <span class="text-[10px] font-bold text-slate-400 block mb-1.5 ml-1">Jam Buka Toko</span>
                            <div class="relative">
                                <i class="fas fa-clock absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                                <input type="time" name="jam_buka_store" value="<?= substr($jam_buka_skrg, 0, 5); ?>" required 
                                       class="w-full pl-11 pr-4 py-3 bg-gray-50 border border-gray-100 rounded-2xl text-sm focus:bg-white focus:ring-2 focus:ring-yellow-400 outline-none transition font-bold text-slate-800 shadow-sm">
                            </div>
                        </div>
                        <div>
                            <span class="text-[10px] font-bold text-slate-400 block mb-1.5 ml-1">Jam Tutup & Batas Akhir</span>
                            <div class="relative">
                                <i class="fas fa-clock absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                                <input type="time" name="jam_tutup_store" value="<?= substr($jam_tutup_skrg, 0, 5); ?>" required 
                                       class="w-full pl-11 pr-4 py-3 bg-gray-50 border border-gray-100 rounded-2xl text-sm focus:bg-white focus:ring-2 focus:ring-yellow-400 outline-none transition font-bold text-slate-800 shadow-sm">
                            </div>
                        </div>
                    </div>
                </div>

                <hr class="border-gray-100">

                <div class="flex justify-start">
                    <button type="submit" name="simpan_pengaturan" class="bg-[#facc15] hover:bg-[#eab308] text-black font-black px-6 py-3.5 rounded-2xl text-xs uppercase italic tracking-widest transition-all duration-300 shadow-lg shadow-yellow-400/10 flex items-center gap-2">
                        <i class="fas fa-save text-xs"></i> Simpan Konfigurasi
                    </button>
                </div>
            </form>
        </div>
    </main>

    <script>
        function submitStatusToko(statusValue) {
            // Set penanda action radio click agar form auto-update tanpa alert sukses mengganggu
            document.getElementById('ajax_action').value = 'toggle_status';
            
            // Cari input radio yang sesuai di dalam form lalu centang dan submit
            const radios = document.getElementsByName('status_toko');
            for (let i = 0; i < radios.length; i++) {
                if (radios[i].value === statusValue) {
                    radios[i].checked = true;
                    break;
                }
            }
            // Submit form otomatis
            document.getElementById('formPengaturan').submit();
        }
    </script>

</body>
</html>