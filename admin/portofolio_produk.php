<?php
require_once 'config.php';
require_once 'koneksi.php';
proteksi_halaman();

// ==========================================
// A. LOGIKA PROSES TAMBAH PORTOFOLIO (CREATE)
// ==========================================
if (isset($_POST['tambah_porto'])) {
    $judul       = mysqli_real_escape_string($koneksi, $_POST['judul']);
    $kategori    = mysqli_real_escape_string($koneksi, $_POST['kategori']);
    $tipe_media  = mysqli_real_escape_string($koneksi, $_POST['tipe_media']);
    $deskripsi   = mysqli_real_escape_string($koneksi, $_POST['deskripsi']);
    
    if ($tipe_media == 'gambar') {
        $nama_gambar = $_FILES['gambar_file']['name'];
        $tmp_name    = $_FILES['gambar_file']['tmp_name'];
        $media_final = time() . '_' . $nama_gambar;
        $jalur_simpan = '../customer/images/portofolio/' . $media_final; 

        if (!is_dir('../customer/images/portofolio/')) {
            mkdir('../customer/images/portofolio/', 0777, true);
        }
        move_uploaded_file($tmp_name, $jalur_simpan);
    } else {
        $media_final = mysqli_real_escape_string($koneksi, $_POST['video_url']);
    }

    $query_insert = "INSERT INTO tb_portofolio (judul, kategori, tipe_media, deskripsi, sumber_media) 
                     VALUES ('$judul', '$kategori', '$tipe_media', '$deskripsi', '$media_final')";
    mysqli_query($koneksi, $query_insert);
    header("Location: portofolio_produk.php?status=sukses");
    exit;
}

// ==========================================
// B. LOGIKA PROSES EDIT PORTOFOLIO (UPDATE)
// ==========================================
if (isset($_POST['edit_porto'])) {
    $id_porto    = intval($_POST['id_porto']);
    $judul       = mysqli_real_escape_string($koneksi, $_POST['judul']);
    $kategori    = mysqli_real_escape_string($koneksi, $_POST['kategori']);
    $tipe_media  = mysqli_real_escape_string($koneksi, $_POST['tipe_media']);
    $deskripsi   = mysqli_real_escape_string($koneksi, $_POST['deskripsi']);

    if ($tipe_media == 'gambar') {
        if (!empty($_FILES['gambar_file']['name'])) {
            $nama_gambar = $_FILES['gambar_file']['name'];
            $tmp_name    = $_FILES['gambar_file']['tmp_name'];
            $media_final = time() . '_' . $nama_gambar;
            $jalur_simpan = '../customer/images/portofolio/' . $media_final;

            if (move_uploaded_file($tmp_name, $jalur_simpan)) {
                $cek_lama = mysqli_query($koneksi, "SELECT tipe_media, sumber_media FROM tb_portofolio WHERE id_porto = $id_porto");
                $data_lama = mysqli_fetch_assoc($cek_lama);
                if ($data_lama && $data_lama['tipe_media'] == 'gambar') {
                    $path_hapus = '../customer/images/portofolio/' . $data_lama['sumber_media'];
                    if (file_exists($path_hapus)) { unlink($path_hapus); }
                }
                $query_update = "UPDATE tb_portofolio SET judul='$judul', kategori='$kategori', tipe_media='$tipe_media', deskripsi='$deskripsi', sumber_media='$media_final' WHERE id_porto=$id_porto";
            }
        } else {
            $query_update = "UPDATE tb_portofolio SET judul='$judul', kategori='$kategori', tipe_media='$tipe_media', deskripsi='$deskripsi' WHERE id_porto=$id_porto";
        }
    } else {
        $media_final = mysqli_real_escape_string($koneksi, $_POST['video_url']);
        $cek_lama = mysqli_query($koneksi, "SELECT tipe_media, sumber_media FROM tb_portofolio WHERE id_porto = $id_porto");
        $data_lama = mysqli_fetch_assoc($cek_lama);
        if ($data_lama && $data_lama['tipe_media'] == 'gambar') {
            $path_hapus = '../customer/images/portofolio/' . $data_lama['sumber_media'];
            if (file_exists($path_hapus)) { unlink($path_hapus); }
        }
        $query_update = "UPDATE tb_portofolio SET judul='$judul', kategori='$kategori', tipe_media='$tipe_media', deskripsi='$deskripsi', sumber_media='$media_final' WHERE id_porto=$id_porto";
    }

    mysqli_query($koneksi, $query_update);
    header("Location: portofolio_produk.php?status=diperbarui");
    exit;
}

// ==========================================
// C. LOGIKA ENGINE HAPUS MASSAL (BULK DELETE)
// ==========================================
if (isset($_POST['eksekusi_hapus_porto_massal'])) {
    if (!empty($_POST['porto_id_hapus'])) {
        foreach ($_POST['porto_id_hapus'] as $id_porto_hapus) {
            $id_clean = intval($id_porto_hapus);
            $cek_lama = mysqli_query($koneksi, "SELECT tipe_media, sumber_media FROM tb_portofolio WHERE id_porto = $id_clean");
            $data_lama = mysqli_fetch_assoc($cek_lama);
            
            if ($data_lama && $data_lama['tipe_media'] == 'gambar') {
                $path_hapus = '../customer/images/portofolio/' . $data_lama['sumber_media'];
                if (file_exists($path_hapus)) { unlink($path_hapus); }
            }
            mysqli_query($koneksi, "DELETE FROM tb_portofolio WHERE id_porto = $id_clean");
        }
        header("Location: portofolio_produk.php?status=terhapus");
        exit;
    }
}

$ambil_porto = mysqli_query($koneksi, "SELECT * FROM tb_portofolio ORDER BY id_porto DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hanbit Admin - Kelola Portofolio Kerja</title>
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
                    <h1 class="text-2xl font-black tracking-tight text-slate-900">Portofolio Kerja</h1>
                    <p class="text-xs text-slate-400 font-medium">Atur dokumentasi hasil perbaikan sasis fisik atau modifikasi internal laptop toko.</p>
                </div>
                
                <button type="button" id="btn_hapus_porto_massal" onclick="bukaModalHapusMassal()" 
                        class="hidden bg-red-500 hover:bg-red-600 text-white font-black px-6 py-2.5 rounded-xl text-xs uppercase tracking-wider transition-all duration-300 shadow-md flex items-center gap-2 shrink-0 transform scale-95 opacity-0">
                    <i class="fas fa-trash-alt text-xs"></i> Hapus Terpilih (<span id="count_porto_terpilih">0</span>)
                </button>
            </div>

            <?php if (isset($_GET['status'])): ?>
                <div class="text-xs font-bold px-4 py-3 rounded-xl border bg-white shadow-sm">
                    <?php
                    if ($_GET['status'] == 'sukses') echo "<span class='text-emerald-600'>✅ Dokumentasi portofolio kerja berhasil dipublikasikan live!</span>";
                    elseif ($_GET['status'] == 'diperbarui') echo "<span class='text-blue-600'>✅ Data berkas portofolio berhasil disinkronkan kembali!</span>";
                    elseif ($_GET['status'] == 'terhapus') echo "<span class='text-rose-600'>🗑️ Item berkas portofolio terpilih berhasil dihapus dari sistem.</span>";
                    ?>
                </div>
            <?php endif; ?>

            <div class="bg-white border border-gray-200/80 p-6 rounded-2xl shadow-sm space-y-4">
                <h3 class="text-xs font-extrabold uppercase text-slate-900 tracking-wider border-b pb-2">
                    <i class="fas fa-plus-circle text-amber-500 mr-1"></i> Portofolio Hanbit Labs
                </h3>
                <form action="" method="POST" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-12 gap-4 text-xs font-semibold text-slate-600">
                    
                    <div class="md:col-span-5 space-y-1">
                        <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Judul Kasus / Kerusakan</label>
                        <input type="text" name="judul" placeholder="Contoh: Repaste Liquid Metal ROG Zephyrus G14" required class="w-full px-4 py-2.5 bg-slate-50 border border-gray-200 rounded-xl font-bold focus:outline-none focus:border-yellow-400 focus:bg-white transition text-slate-800">
                    </div>

                    <div class="md:col-span-4 space-y-1">
                        <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Klasifikasi Kategori</label>
                        <select name="kategori" required class="w-full px-3 py-2.5 bg-slate-50 border border-gray-200 rounded-xl focus:outline-none focus:border-yellow-400 focus:bg-white transition text-slate-700 font-bold uppercase cursor-pointer">
                            <option value="Thermal Repasting">Thermal Repasting Component</option>
                            <option value="Hardware Upgrade">Hardware Upgrades (RAM/SSD)</option>
                            <option value="Motherboard Repair">Motherboard IC Circuit Repair</option>
                            <option value="Hinge & Sasis Restoration">Hinge & Sasis Physical Restoration</option>
                            <option value="Cleaning Maintenance">Deep Cleaning Maintenance</option>
                        </select>
                    </div>

                    <div class="md:col-span-3 space-y-1">
                        <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Tipe Format Media</label>
                        <select name="tipe_media" id="tipe_media_add" onchange="aturInputMedia(this.value, 'add')" required class="w-full px-3 py-2.5 bg-slate-50 border border-gray-200 rounded-xl focus:outline-none focus:border-yellow-400 focus:bg-white transition text-slate-700 font-bold uppercase cursor-pointer">
                            <option value="gambar">Berkas Gambar/Foto</option>
                            <option value="video">Embed Video URL (TikTok/YouTube)</option>
                        </select>
                    </div>

                    <div class="md:col-span-12 space-y-1">
                        <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Ringkasan Analisis Teknis Kasus Pengerjaan (Bisa Enter Baris Baru)</label>
                        <textarea name="deskripsi" rows="3" placeholder="Contoh:&#10;1. Suhu drop dari 95°C ke 78°C&#10;2. Menggunakan thermal paste premium GC-Extreme.&#10;3. Kipas dibersihkan total." required class="w-full px-4 py-2.5 bg-slate-50 border border-gray-200 rounded-xl font-medium focus:outline-none focus:border-yellow-400 focus:bg-white transition text-slate-700 resize-none leading-relaxed"></textarea>
                    </div>

                    <div class="md:col-span-12 space-y-1" id="wrap_gambar_add">
                        <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Unggah Foto Dokumentasi Kerusakan</label>
                        <input type="file" name="gambar_file" id="file_gambar_input_add" accept="image/*" required class="w-full px-2 py-1.5 bg-slate-50 border border-gray-200 rounded-xl focus:outline-none focus:border-yellow-400 transition">
                    </div>

                    <div class="md:col-span-12 space-y-1 hidden" id="wrap_video_add">
                        <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Sematkan Tautan Video URL (Embed Link)</label>
                        <input type="url" name="video_url" id="url_video_input_add" placeholder="Contoh: https://www.youtube.com/embed/..." class="w-full px-4 py-2.5 bg-slate-50 border border-gray-200 rounded-xl focus:outline-none focus:border-yellow-400 transition text-blue-600 font-sans">
                    </div>

                    <div class="md:col-span-12 pt-2 flex justify-end">
                        <button type="submit" name="tambah_porto" class="bg-black hover:bg-slate-900 text-white font-bold text-xs uppercase px-6 py-2.5 rounded-xl tracking-wider transition">
                            Publikasikan Karya
                        </button>
                    </div>
                </form>
            </div>

            <form id="form_porto_massal" action="portofolio_produk.php" method="POST">
                <div class="bg-white border border-gray-200/80 rounded-2xl shadow-sm overflow-hidden">
                    <div class="p-4 border-b border-gray-100 bg-slate-50/50">
                        <h3 class="text-xs font-extrabold uppercase text-slate-900 tracking-wider">Arsip Portofolio Hanbit Labs</h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse text-xs">
                            <thead>
                                <tr class="bg-slate-100/60 text-slate-400 font-bold uppercase border-b border-gray-100 select-none">
                                    <th class="p-4 w-12 text-center">
                                        <input type="checkbox" id="master_check_porto" onclick="toggleSemuaPorto(this)" class="w-4 h-4 rounded text-blue-600 border-gray-300 focus:ring-blue-500 cursor-pointer">
                                    </th>
                                    <th class="p-4 w-20 text-center">Pratinjau</th>
                                    <th class="p-4">Detail Pengerjaan Portofolio</th>
                                    <th class="p-4 w-44">Kategori Klasifikasi</th>
                                    <th class="p-4 w-32 text-center">Tipe Media</th>
                                    <th class="p-4 w-16 text-center">Edit</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 font-medium text-slate-700">
                                <?php if (mysqli_num_rows($ambil_porto) == 0): ?>
                                    <tr>
                                        <td colspan="6" class="p-8 text-center text-slate-400 font-bold italic">Belum ada karya portofolio dipajang. Silakan unggah lewat panel di atas.</td>
                                    </tr>
                                <?php else: ?>
                                    <?php while($row = mysqli_fetch_assoc($ambil_porto)): ?>
                                        <tr class="hover:bg-slate-50/60 transition">
                                            <td class="p-4 text-center">
                                                <input type="checkbox" name="porto_id_hapus[]" value="<?= $row['id_porto']; ?>" onchange="hitungPortoTerpilih()" class="check_porto_child w-4 h-4 rounded text-blue-600 border-gray-300 focus:ring-blue-500 cursor-pointer">
                                            </td>
                                            <td class="p-4 text-center">
                                                <?php if ($row['tipe_media'] == 'gambar'): ?>
                                                    <?php 
                                                        $media_src = (strpos($row['sumber_media'], 'images/') !== false) ? '../customer/' . $row['sumber_media'] : '../customer/images/portofolio/' . $row['sumber_media'];
                                                    ?>
                                                    <img src="<?= $media_src; ?>?v=<?= time(); ?>" class="w-12 h-12 object-cover rounded-lg border shadow-xs mx-auto">
                                                <?php else: ?>
                                                    <div class="w-12 h-12 bg-red-50 text-red-500 border border-red-100 rounded-lg flex items-center justify-center mx-auto text-sm shadow-xs"><i class="fas fa-video"></i></div>
                                                <?php endif; ?>
                                            </td>
                                            <td class="p-4">
                                                <div class="font-bold text-slate-900 uppercase text-[11px] tracking-wide"><?= htmlspecialchars($row['judul']); ?></div>
                                                <div class="text-[11px] text-slate-500 font-medium mt-1 leading-relaxed"><?= nl2br(htmlspecialchars($row['deskripsi'])); ?></div>
                                            </td>
                                            <td class="p-4 text-slate-600 font-semibold uppercase text-[10px] tracking-wide"><?= htmlspecialchars($row['kategori']); ?></td>
                                            <td class="p-4 text-center uppercase tracking-wider text-[9px] font-bold">
                                                <?php if($row['tipe_media'] == 'gambar'): ?>
                                                    <span class="bg-blue-50 text-blue-600 px-2 py-0.5 rounded border border-blue-100">📷 Image</span>
                                                <?php else: ?>
                                                    <span class="bg-red-50 text-red-600 px-2 py-0.5 rounded border border-red-100">🎥 Stream</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="p-4 text-center">
                                                <button type="button" onclick="bukaModalEditPorto('<?= $row['id_porto']; ?>', '<?= htmlspecialchars($row['judul'], ENT_QUOTES); ?>', '<?= $row['kategori']; ?>', '<?= $row['tipe_media']; ?>', '<?= htmlspecialchars(json_encode($row['deskripsi']), ENT_QUOTES); ?>', '<?= htmlspecialchars($row['sumber_media'], ENT_QUOTES); ?>')" class="text-blue-500 hover:text-blue-700 text-sm p-1.5 transition inline-block">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div id="modal_hapus_massal_porto" class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-50 hidden items-center justify-center p-4">
                    <div class="bg-white rounded-2xl max-w-sm w-full p-6 shadow-2xl border border-gray-100 space-y-4 text-center">
                        <div class="w-12 h-12 bg-red-50 text-red-500 rounded-full flex items-center justify-center text-xl mx-auto border border-red-100">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                        <div class="space-y-1">
                            <h3 class="text-sm font-black uppercase text-slate-900 tracking-wide">Hapus Portofolio</h3>
                            <p class="text-xs font-semibold text-slate-400 leading-relaxed">Apakah Anda yakin menghapus permanen (<span id="text_porto_total" class="text-red-500 font-bold">0</span> produk) karya terpilih dari database katalog?</p>
                        </div>
                        <div class="pt-2 flex gap-2">
                            <button type="button" onclick="tutupModalHapusMassal()" class="flex-1 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold py-2.5 rounded-xl transition uppercase tracking-wider text-[10px]">Batal</button>
                            <button type="submit" name="eksekusi_hapus_porto_massal" class="flex-1 bg-red-500 hover:bg-red-600 text-white font-black py-2.5 rounded-xl transition uppercase tracking-wider text-[10px] shadow-sm flex items-center justify-center">Ya, Hapus</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </main>

    <div id="modal_edit_porto" class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-50 hidden items-center justify-center px-4">
        <div class="bg-white rounded-[1.5rem] max-w-xl w-full p-6 shadow-2xl border border-gray-100 space-y-4 max-h-[90vh] overflow-y-auto transform transition-all">
            <div class="flex justify-between items-center border-b pb-3">
                <h3 class="text-sm font-black uppercase text-slate-900"><i class="fas fa-edit text-blue-500 mr-2"></i> Perbarui Arsip Portofolio</h3>
                <button type="button" onclick="tutupModalEditPorto()" class="text-slate-400 hover:text-slate-600"><i class="fas fa-times text-lg"></i></button>
            </div>
            
            <form action="" method="POST" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-12 gap-4 text-xs font-semibold text-slate-600">
                <input type="hidden" name="id_porto" id="edit_id_porto">

                <div class="md:col-span-12 space-y-1">
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Judul Kasus / Kerusakan</label>
                    <input type="text" name="judul" id="edit_judul" required class="w-full px-4 py-2.5 bg-slate-50 border border-gray-200 rounded-xl focus:outline-none focus:border-blue-400 font-bold text-slate-800">
                </div>
                
                <div class="md:col-span-6 space-y-1">
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Klasifikasi Kategori</label>
                    <select name="kategori" id="edit_kategori" required class="w-full px-3 py-2.5 bg-slate-50 border border-gray-200 rounded-xl focus:outline-none focus:border-blue-400 text-slate-700 font-bold uppercase">
                        <option value="Thermal Repasting">Thermal Repasting Component</option>
                        <option value="Hardware Upgrade">Hardware Upgrades (RAM/SSD)</option>
                        <option value="Motherboard Repair">Motherboard IC Circuit Repair</option>
                        <option value="Hinge & Sasis Restoration">Hinge & Sasis Physical Restoration</option>
                        <option value="Cleaning Maintenance">Deep Cleaning Maintenance</option>
                    </select>
                </div>

                <div class="md:col-span-6 space-y-1">
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Tipe Format Media</label>
                    <select name="tipe_media" id="edit_tipe_media" onchange="aturInputMedia(this.value, 'edit')" required class="w-full px-3 py-2.5 bg-slate-50 border border-gray-200 rounded-xl focus:outline-none focus:border-blue-400 text-slate-700 font-bold uppercase">
                        <option value="gambar">Berkas Gambar/Foto</option>
                        <option value="video">Embed Video URL (TikTok/YouTube)</option>
                    </select>
                </div>

                <div class="md:col-span-12 space-y-1">
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Ringkasan Analisis Teknis Kasus Pengerjaan</label>
                    <textarea name="deskripsi" id="edit_deskripsi" rows="3" required class="w-full px-4 py-2.5 bg-slate-50 border border-gray-200 rounded-xl focus:outline-none focus:border-blue-400 text-slate-700 resize-none leading-relaxed"></textarea>
                </div>

                <div class="md:col-span-12 space-y-1" id="wrap_gambar_edit">
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Ganti Foto Dokumentasi (Kosongkan jika tidak ingin diubah)</label>
                    <input type="file" name="gambar_file" id="file_gambar_input_edit" accept="image/*" class="w-full px-2 py-1.5 bg-slate-50 border border-gray-200 rounded-xl focus:outline-none focus:border-blue-400 transition">
                </div>

                <div class="md:col-span-12 space-y-1 hidden" id="wrap_video_edit">
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Sematkan Tautan Video URL (Embed Link)</label>
                    <input type="url" name="video_url" id="url_video_input_edit" placeholder="https://www.youtube.com/embed/..." class="w-full px-4 py-2.5 bg-slate-50 border border-gray-200 rounded-xl focus:outline-none focus:border-blue-400 transition text-blue-600 font-sans">
                </div>

                <div class="md:col-span-12 pt-3 flex justify-end gap-2 border-t">
                    <button type="button" onclick="tutupModalEditPorto()" class="bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold px-4 py-2 rounded-xl transition uppercase tracking-wider text-[10px]">Batal</button>
                    <button type="submit" name="edit_porto" class="bg-blue-500 hover:bg-blue-600 text-white font-black px-5 py-2 rounded-xl transition uppercase tracking-wider text-[10px] shadow-sm">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function toggleSemuaPorto(master) {
            const checkboxes = document.querySelectorAll('.check_porto_child');
            checkboxes.forEach(cb => cb.checked = master.checked);
            hitungPortoTerpilih();
        }

        function hitungPortoTerpilih() {
            const checkboxes = document.querySelectorAll('.check_porto_child');
            let totalTerpilih = 0;
            checkboxes.forEach(cb => { if(cb.checked) totalTerpilih++; });

            const btnHapus = document.getElementById('btn_hapus_porto_massal');
            document.getElementById('count_porto_terpilih').innerText = totalTerpilih;

            if(totalTerpilih > 0) {
                btnHapus.classList.remove('hidden');
                setTimeout(() => { btnHapus.classList.remove('scale-95', 'opacity-0'); btnHapus.classList.add('scale-100', 'opacity-100'); }, 10);
            } else {
                btnHapus.classList.add('scale-95', 'opacity-0');
                btnHapus.classList.remove('scale-100', 'opacity-100');
                setTimeout(() => { btnHapus.classList.add('hidden'); }, 300);
                document.getElementById('master_check_porto').checked = false;
            }
        }

        function bukaModalHapusMassal() {
            const total = document.getElementById('count_porto_terpilih').innerText;
            document.getElementById('text_porto_total').innerText = total;
            const m = document.getElementById('modal_hapus_massal_porto');
            m.classList.remove('hidden'); m.classList.add('flex');
        }

        function tutupModalHapusMassal() {
            const m = document.getElementById('modal_hapus_massal_porto');
            m.classList.remove('flex'); m.classList.add('hidden');
        }

        function aturInputMedia(value, context) {
            const suffix = (context === 'edit') ? '_edit' : '_add';
            const wrapGambar = document.getElementById('wrap_gambar' + suffix);
            const wrapVideo  = document.getElementById('wrap_video' + suffix);
            const fileInput  = document.getElementById('file_gambar_input' + suffix);
            const videoInput = document.getElementById('url_video_input' + suffix);

            if (value === 'gambar') {
                if(wrapGambar) wrapGambar.classList.remove('hidden');
                if(wrapVideo) wrapVideo.classList.add('hidden');
                if (context === 'add' && fileInput) fileInput.setAttribute('required', 'required');
                if (videoInput) videoInput.removeAttribute('required');
            } else {
                if(wrapGambar) wrapGambar.classList.add('hidden');
                if(wrapVideo) wrapVideo.classList.remove('hidden');
                if(fileInput) fileInput.removeAttribute('required');
                if (context === 'add' && videoInput) videoInput.setAttribute('required', 'required');
            }
        }

        function bukaModalEditPorto(id, judul, kategori, tipe, deskripsiJson, sumber) {
            document.getElementById('edit_id_porto').value = id;
            document.getElementById('edit_judul').value = judul;
            document.getElementById('edit_kategori').value = kategori;
            document.getElementById('edit_tipe_media').value = tipe;
            
            // 🔥 DECODE JSON SAFE: Mengembalikan enter baris baru secara utuh ke textarea
            let deskripsiAsli = JSON.parse(deskripsiJson);
            document.getElementById('edit_deskripsi').value = deskripsiAsli;

            aturInputMedia(tipe, 'edit');

            if (tipe === 'video') {
                document.getElementById('url_video_input_edit').value = sumber;
            } else {
                if(document.getElementById('url_video_input_edit')) {
                    document.getElementById('url_video_input_edit').value = "";
                }
            }

            const m = document.getElementById('modal_edit_porto');
            m.classList.remove('hidden'); m.classList.add('flex');
        }

        function tutupModalEditPorto() {
            const m = document.getElementById('modal_edit_porto');
            m.classList.remove('flex'); m.classList.add('hidden');
        }
    </script>
</body>
</html>