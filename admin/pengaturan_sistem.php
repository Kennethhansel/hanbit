<?php
require_once 'config.php';
require_once 'koneksi.php';
proteksi_halaman();

$id_admin = $_SESSION['id_user'] ?? 1; 
$pesan_sukses = '';

if (isset($_POST['simpan_pengaturan']) || isset($_POST['ajax_action'])) {
    $kuota_baru    = mysqli_real_escape_string($koneksi, $_POST['kuota_harian'] ?? 50);
    $target_baru   = mysqli_real_escape_string($koneksi, $_POST['target_omzet'] ?? 5000000); 
    $status_baru   = mysqli_real_escape_string($koneksi, $_POST['status_toko']);
    $jam_buka_baru = mysqli_real_escape_string($koneksi, $_POST['jam_buka_store'] ?? '09:00');
    $jam_tutup_baru= mysqli_real_escape_string($koneksi, $_POST['jam_tutup_store'] ?? '18:00');
    $pesan_baru    = mysqli_real_escape_string($koneksi, $_POST['pesan_penutupan'] ?? '');
    
    $update_query = "UPDATE admin_accounts SET 
                        max_kuota_harian = '$kuota_baru',
                        target_omzet = '$target_baru',
                        status_toko = '$status_baru',
                        jam_buka_store = '$jam_buka_baru',
                        jam_tutup_store = '$jam_tutup_baru',
                        pesan_penutupan = '$pesan_baru'
                     WHERE id_admin = '$id_admin'";
    
    if (mysqli_query($koneksi, $update_query)) {
        $pesan_sukses = 'Konfigurasi operasional sistem berhasil disimpan dan disinkronkan!';
    }
}

// Ambil parameter data terkini termasuk target_omzet
$ambil_data = mysqli_query($koneksi, "SELECT max_kuota_harian, target_omzet, status_toko, jam_buka_store, jam_tutup_store, pesan_penutupan FROM admin_accounts WHERE id_admin = '$id_admin'");
$data_skrg  = mysqli_fetch_assoc($ambil_data);

$kuota_skrg      = $data_skrg['max_kuota_harian'] ?? 50;
$target_skrg     = $data_skrg['target_omzet'] ?? 5000000; 
$status_skrg     = $data_skrg['status_toko'] ?? 'buka';
$jam_buka_skrg   = $data_skrg['jam_buka_store'] ?? '09:00';
$jam_tutup_skrg  = $data_skrg['jam_tutup_store'] ?? '18:00';
$pesan_tutup_skrg= $data_skrg['pesan_penutupan'] ?? '';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hanbit Admin - Pengaturan Parameter Sistem</title>
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
                    <h1 class="text-2xl font-black tracking-tight text-slate-900">Konfigurasi Pengaturan Sistem</h1>
                    <p class="text-xs text-slate-400 font-medium">Kendalikan status operasional, kuota harian reservation website, dan jam pelayanan kasir toko.</p>
                </div>
            </div>

            <?php if (!empty($pesan_sukses)): ?>
                <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 text-xs font-bold px-4 py-3 rounded-xl shadow-sm flex items-center gap-2 animate-fade-in">
                    <i class="fas fa-check-circle text-emerald-500"></i> <?= $pesan_sukses; ?>
                </div>
            <?php endif; ?>

            <div class="bg-white border border-gray-200/80 p-6 rounded-2xl shadow-sm space-y-4">
                <h3 class="text-xs font-extrabold uppercase text-slate-900 tracking-wider">
                    ⚡ Sakelar Instan Status Toko (Gate Controller)
                </h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 select-none">
                    <div onclick="submitStatusToko('buka')" class="border rounded-2xl p-4 flex items-center justify-between cursor-pointer transition-all duration-200 <?= $status_skrg == 'buka' ? 'border-emerald-500 bg-emerald-50/30' : 'border-gray-200 bg-slate-50/50 hover:bg-slate-50' ?>">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl flex items-center justify-center text-xs <?= $status_skrg == 'buka' ? 'bg-emerald-100 text-emerald-600' : 'bg-slate-200 text-slate-400' ?>">
                                <i class="fas fa-door-open text-base"></i>
                            </div>
                            <div>
                                <span class="font-extrabold text-[11px] uppercase block <?= $status_skrg == 'buka' ? 'text-emerald-900' : 'text-slate-700' ?>">Toko Dibuka</span>
                                <span class="text-[10px] font-medium text-slate-400">Konsumen bisa melakukan booking secara online.</span>
                            </div>
                        </div>
                        <div class="w-5 h-5 rounded-full border flex items-center justify-center <?= $status_skrg == 'buka' ? 'border-emerald-500 bg-emerald-500' : 'border-gray-300' ?>">
                            <?php if ($status_skrg == 'buka'): ?><i class="fas fa-check text-[9px] text-white"></i><?php endif; ?>
                        </div>
                    </div>

                    <div onclick="submitStatusToko('tutup')" class="border rounded-2xl p-4 flex items-center justify-between cursor-pointer transition-all duration-200 <?= $status_skrg == 'tutup' ? 'border-rose-500 bg-rose-50/30' : 'border-gray-200 bg-slate-50/50 hover:bg-slate-50' ?>">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl flex items-center justify-center text-xs <?= $status_skrg == 'tutup' ? 'bg-rose-100 text-rose-600' : 'bg-slate-200 text-slate-400' ?>">
                                <i class="fas fa-door-closed text-base"></i>
                            </div>
                            <div>
                                <span class="font-extrabold text-[11px] uppercase block <?= $status_skrg == 'tutup' ? 'text-rose-900' : 'text-slate-700' ?>">Toko Ditutup</span>
                                <span class="text-[10px] font-medium text-slate-400">Sistem booking dikunci dan dialihkan ke pesan penutupan.</span>
                            </div>
                        </div>
                        <div class="w-5 h-5 rounded-full border flex items-center justify-center <?= $status_skrg == 'tutup' ? 'border-rose-500 bg-rose-500' : 'border-gray-300' ?>">
                            <?php if ($status_skrg == 'tutup'): ?><i class="fas fa-check text-[9px] text-white"></i><?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white border border-gray-200/80 p-6 rounded-2xl shadow-sm space-y-4">
                <h3 class="text-xs font-extrabold uppercase text-slate-900 tracking-wider border-b pb-2">
                    📋 Parameter Batas & Pesan Gerbang Toko
                </h3>
                
                <form id="form_utama_pengaturan" action="" method="POST" class="space-y-4 text-xs font-semibold text-slate-600">
                    <input type="hidden" name="ajax_action" id="ajax_action" value="">
                    <input type="radio" name="status_toko" value="buka" class="hidden" <?= $status_skrg == 'buka' ? 'checked' : ''; ?>>
                    <input type="radio" name="status_toko" value="tutup" class="hidden" <?= $status_skrg == 'tutup' ? 'checked' : ''; ?>>

                    <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
                        <div class="md:col-span-6 space-y-1">
                            <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Batas Kuota Antrean Harian (Unit)</label>
                            <input type="number" name="kuota_harian" value="<?= $kuota_skrg; ?>" required class="w-full px-4 py-2.5 bg-slate-50 border border-gray-200 rounded-xl focus:outline-none focus:border-blue-400 focus:bg-white transition font-bold text-slate-800">
                        </div>

                        <div class="md:col-span-6 space-y-1">
                            <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Target Omzet Bulanan (IDR)</label>
                            <input type="number" name="target_omzet" value="<?= $target_skrg; ?>" required class="w-full px-4 py-2.5 bg-slate-50 border border-gray-200 rounded-xl focus:outline-none focus:border-blue-400 focus:bg-white transition font-bold text-slate-800">
                        </div>

                        <div class="md:col-span-6 space-y-1">
                            <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Jam Mulai Buka Penerimaan</label>
                            <input type="time" name="jam_buka_store" value="<?= substr($jam_buka_skrg, 0, 5); ?>" required class="w-full px-4 py-2.5 bg-slate-50 border border-gray-200 rounded-xl focus:outline-none focus:border-blue-400 focus:bg-white transition font-bold text-slate-700">
                        </div>

                        <div class="md:col-span-6 space-y-1">
                            <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Jam Batas Tutup Penerimaan</label>
                            <input type="time" name="jam_tutup_store" value="<?= substr($jam_tutup_skrg, 0, 5); ?>" required class="w-full px-4 py-2.5 bg-slate-50 border border-gray-200 rounded-xl focus:outline-none focus:border-blue-400 focus:bg-white transition font-bold text-slate-700">
                        </div>

                        <div class="md:col-span-12 space-y-1">
                            <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Pesan Keterangan Khusus Saat Toko Tutup / Libur</label>
                            <input type="text" name="pesan_penutupan" value="<?= htmlspecialchars($pesan_tutup_skrg); ?>" placeholder="Contoh: Maaf, toko kami sedang dalam masa libur Idul Fitri. Buka kembali tanggal..." class="w-full px-4 py-2.5 bg-slate-50 border border-gray-200 rounded-xl font-medium focus:outline-none focus:border-blue-400 focus:bg-white transition text-slate-700">
                        </div>
                    </div>

                    <div class="pt-4 border-t flex justify-end">
                        <button type="submit" name="simpan_pengaturan" class="bg-black hover:bg-slate-900 text-white font-bold text-xs uppercase px-6 py-2.5 rounded-xl tracking-wider transition shadow-sm flex items-center gap-1.5">
                            <i class="fas fa-save text-[11px]"></i> Simpan Parameter Operasional
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </main>

    <script>
        function submitStatusToko(statusValue) {
            document.getElementById('ajax_action').value = 'toggle_status';
            const radios = document.getElementsByName('status_toko');
            for (let i = 0; i < radios.length; i++) {
                if (radios[i].value === statusValue) {
                    radios[i].checked = true;
                    break;
                }
            }
            document.getElementById('form_predikat_submit_opsi').click();
        }
    </script>
    <button id="form_predikat_submit_opsi" form="form_utama_pengaturan" type="submit" name="simpan_pengaturan" class="hidden"></button>
</body>
</html>