<?php
require_once 'config.php';
require_once 'koneksi.php';
proteksi_halaman();

// ==========================================
// A. LOGIKA PROSES TAMBAH PRODUK (CREATE)
// ==========================================
if (isset($_POST['tambah_produk'])) { 
    $nama_produk = mysqli_real_escape_string($koneksi, $_POST['nama_produk']); 
    $kategori    = mysqli_real_escape_string($koneksi, $_POST['kategori']); 
    $harga       = intval($_POST['harga']); 
    $deskripsi   = mysqli_real_escape_string($koneksi, $_POST['deskripsi']); 
    $link_beli   = mysqli_real_escape_string($koneksi, $_POST['link_ecommerce']); 
    
    $nama_gambar = $_FILES['gambar']['name']; 
    $tmp_name    = $_FILES['gambar']['tmp_name']; 
    $gambar_baru = time() . '_' . $nama_gambar; 
    
    $jalur_simpan = '../customer/images/katalog/' . $gambar_baru; 

    if (move_uploaded_file($tmp_name, $jalur_simpan)) { 
        $query_insert = "INSERT INTO tb_katalog (nama_produk, kategori, harga, deskripsi, gambar, link_ecommerce) 
                         VALUES ('$nama_produk', '$kategori', '$harga', '$deskripsi', '$gambar_baru', '$link_beli')"; 
        mysqli_query($koneksi, $query_insert); 
        header("Location: katalog_produk.php?status=sukses"); 
        exit; 
    }
}

// ==========================================
// B. LOGIKA PROSES EDIT PRODUK (UPDATE)
// ==========================================
if (isset($_POST['edit_produk'])) {
    $id_produk   = intval($_POST['id_produk']);
    $nama_produk = mysqli_real_escape_string($koneksi, $_POST['nama_produk']);
    $kategori    = mysqli_real_escape_string($koneksi, $_POST['kategori']);
    $harga       = intval($_POST['harga']);
    $deskripsi   = mysqli_real_escape_string($koneksi, $_POST['deskripsi']);
    $link_beli   = mysqli_real_escape_string($koneksi, $_POST['link_ecommerce']);
    
    if (!empty($_FILES['gambar']['name'])) {
        $nama_gambar = $_FILES['gambar']['name'];
        $tmp_name    = $_FILES['gambar']['tmp_name'];
        $gambar_baru = time() . '_' . $nama_gambar;
        $jalur_simpan = '../customer/images/katalog/' . $gambar_baru;

        if (move_uploaded_file($tmp_name, $jalur_simpan)) {
            $cek_lama = mysqli_query($koneksi, "SELECT gambar FROM tb_katalog WHERE id_produk = $id_produk");
            $data_lama = mysqli_fetch_assoc($cek_lama);
            if ($data_lama) {
                $file_gambar = $data_lama['gambar'];
                $path_hapus = (strpos($file_gambar, 'images/') !== false) ? '../customer/' . $file_gambar : '../customer/images/katalog/' . $file_gambar;
                if (file_exists($path_hapus)) { unlink($path_hapus); }
            }
            $query_update = "UPDATE tb_katalog SET nama_produk='$nama_produk', kategori='$kategori', harga='$harga', deskripsi='$deskripsi', gambar='$gambar_baru', link_ecommerce='$link_beli' WHERE id_produk=$id_produk";
        }
    } else {
        $query_update = "UPDATE tb_katalog SET nama_produk='$nama_produk', kategori='$kategori', harga='$harga', deskripsi='$deskripsi', link_ecommerce='$link_beli' WHERE id_produk=$id_produk";
    }
    
    mysqli_query($koneksi, $query_update);
    header("Location: katalog_produk.php?status=diperbarui");
    exit;
}

// ==========================================
// C. LOGIKA ENGINE BARU: HAPUS MASSAL (BULK DELETE)
// ==========================================
if (isset($_POST['eksekusi_hapus_katalog_massal'])) {
    if (!empty($_POST['katalog_id_hapus'])) {
        foreach ($_POST['katalog_id_hapus'] as $id_katalog_hapus) {
            $id_clean = intval($id_katalog_hapus);
            $cek_gambar = mysqli_query($koneksi, "SELECT gambar FROM tb_katalog WHERE id_produk = $id_clean");
            $data_gambar = mysqli_fetch_assoc($cek_gambar);
            
            if ($data_gambar) { 
                $file_gambar = $data_gambar['gambar']; 
                $path_hapus = (strpos($file_gambar, 'images/') !== false) ? '../customer/' . $file_gambar : '../customer/images/katalog/' . $file_gambar; 
                if (file_exists($path_hapus)) { unlink($path_hapus); }
            }
            mysqli_query($koneksi, "DELETE FROM tb_katalog WHERE id_produk = $id_clean");
        }
        header("Location: katalog_produk.php?status=terhapus");
        exit;
    }
}

$ambil_katalog = mysqli_query($koneksi, "SELECT * FROM tb_katalog ORDER BY id_produk DESC"); 
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hanbit Admin - Kelola Katalog Part & Aksesori</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght=400;500;600;700;800;900&display=swap');
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-[#f8fafc] text-slate-800 antialiased flex min-h-screen">

    <?php include 'sidebar.php'; ?> 

    <!-- OPTIMASI LAYOUT WIDE: Menggunakan max-w-full agar seimbang mengisi ruang putih kanan-kiri -->
    <main class="flex-1 p-8 overflow-y-auto"> 
        <div class="max-w-full mx-auto px-2 space-y-6"> 
            
            <!-- HEADER BLOK -->
            <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-4 min-h-[56px]">
                <div>
                    <h1 class="text-2xl font-black tracking-tight text-slate-900">Katalog Produk</h1> 
                    <p class="text-xs text-slate-400 font-medium">Tambah atau hapus rekomendasi sparepart upgrade dan aksesori Hanbit Labs.</p> 
                </div>
                
                <!-- Tombol Hapus Massal (Dinamis via JS) -->
                <button type="button" id="btn_hapus_katalog_massal" onclick="bukaModalHapusMassal()" 
                        class="hidden bg-red-500 hover:bg-red-600 text-white font-black px-6 py-2.5 rounded-xl text-xs uppercase tracking-wider transition-all duration-300 shadow-md flex items-center gap-2 shrink-0 transform scale-95 opacity-0">
                    <i class="fas fa-trash-alt text-xs"></i> Hapus Terpilih (<span id="count_katalog_terpilih">0</span>)
                </button>
            </div>

            <!-- STATUS TOAST ALERTS -->
            <?php if (isset($_GET['status'])): ?>
                <div class="text-xs font-bold px-4 py-3 rounded-xl border bg-white shadow-sm">
                    <?php
                    if ($_GET['status'] == 'sukses') echo "<span class='text-emerald-600'>✅ Komponen baru berhasil masuk katalog!</span>";
                    elseif ($_GET['status'] == 'diperbarui') echo "<span class='text-blue-600'>✅ Data spesifikasi komponen berhasil diperbarui!</span>";
                    elseif ($_GET['status'] == 'terhapus') echo "<span class='text-rose-600'>🗑️ Item produk terpilih berhasil dibersihkan dari katalog.</span>";
                    ?>
                </div>
            <?php endif; ?>

            <!-- FORM TAMBAH -->
            <div class="bg-white border border-gray-200/80 p-6 rounded-2xl shadow-sm space-y-4"> 
                <h3 class="text-sm font-extrabold uppercase text-slate-900 tracking-wider border-b pb-2"> 
                    <i class="fas fa-plus-circle text-amber-500 mr-1"></i> Tambah Part / Aksesori Baru 
                </h3>
                <form action="" method="POST" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-12 gap-4 text-xs font-semibold text-slate-600"> 
                    <div class="md:col-span-6 space-y-1"> 
                        <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Nama Komponen / Aksesori</label> 
                        <input type="text" name="nama_produk" placeholder="Contoh: RAM DDR4 Corsair 8GB" required class="w-full px-4 py-2.5 bg-slate-50 border border-gray-200 rounded-xl font-bold focus:outline-none focus:border-yellow-400 focus:bg-white transition text-slate-800"> 
                    </div>
                    <div class="md:col-span-3 space-y-1"> 
                        <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Kategori</label> 
                        <select name="kategori" required class="w-full px-3 py-2.5 bg-slate-50 border border-gray-200 rounded-xl focus:outline-none focus:border-yellow-400 focus:bg-white transition text-slate-700 font-bold uppercase cursor-pointer"> 
                            <option value="ram">Memory RAM</option> 
                            <option value="storage">Storage SSD/HDD</option> 
                            <option value="lcd">LCD Screen</option> 
                            <option value="keyboard">Keyboard</option> 
                            <option value="charger">Charger</option> 
                            <option value="pasta">Thermal Paste</option> 
                            <option value="flashdisk">Flashdisk</option> 
                            <option value="aksesoris">Aksesori</option> 
                        </select>
                    </div>
                    <div class="md:col-span-3 space-y-1"> 
                        <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Harga</label> 
                        <input type="number" name="harga" placeholder="Contoh: 450000" required class="w-full px-4 py-2.5 bg-slate-50 border border-gray-200 rounded-xl focus:outline-none focus:border-yellow-400 focus:bg-white transition text-slate-800 font-bold"> 
                    </div>
                    <div class="md:col-span-6 space-y-1"> 
                        <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Deskripsi & Spesifikasi Singkat</label> 
                        <input type="text" name="deskripsi" placeholder="Contoh: Speed up to 3200MHz, original garansi lifetime." required class="w-full px-4 py-2.5 bg-slate-50 border border-gray-200 rounded-xl font-medium focus:outline-none focus:border-yellow-400 focus:bg-white transition text-slate-700"> 
                    </div>
                    <div class="md:col-span-6 space-y-1"> 
                        <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Link E-Commerce</label> 
                        <input type="url" name="link_ecommerce" placeholder="Contoh: https://shopee.co.id/..." required class="w-full px-4 py-2.5 bg-slate-50 border border-gray-200 rounded-xl font-medium focus:outline-none focus:border-yellow-400 focus:bg-white transition text-blue-600 font-sans"> 
                    </div>
                    <div class="md:col-span-12 space-y-1"> 
                        <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Foto Fisik Barang</label> 
                        <input type="file" name="gambar" accept="image/*" required class="w-full px-2 py-1.5 bg-slate-50 border border-gray-200 rounded-xl focus:outline-none focus:border-yellow-400 transition"> 
                    </div>
                    <div class="md:col-span-12 pt-2 flex justify-end"> 
                        <button type="submit" name="tambah_produk" class="bg-black hover:bg-slate-900 text-white font-bold text-xs uppercase px-6 py-2.5 rounded-xl tracking-wider transition"> 
                            Simpan ke Katalog 
                        </button>
                    </div>
                </form>
            </div>

            <!-- FORM SEKARANG MEMBUNGKUS AREA TABEL UTK BULK ACTIONS -->
            <form id="form_katalog_massal" action="katalog_produk.php" method="POST">
                <!-- TABEL MONITORING DATA -->
                <div class="bg-white border border-gray-200/80 rounded-2xl shadow-sm overflow-hidden"> 
                    <div class="p-4 border-b border-gray-100 bg-slate-50/50"> 
                        <h3 class="text-xs font-extrabold uppercase text-slate-900 tracking-wider">Daftar Produk Aktif</h3> 
                    </div>
                    <div class="overflow-x-auto"> 
                        <table class="w-full text-left border-collapse text-xs"> 
                            <thead>
                                <tr class="bg-slate-100/60 text-slate-400 font-bold uppercase border-b border-gray-100 select-none"> 
                                    <th class="p-4 w-12 text-center">
                                        <input type="checkbox" id="master_check_katalog" onclick="toggleSemuaKatalog(this)" class="w-4 h-4 rounded text-blue-600 border-gray-300 focus:ring-blue-500 cursor-pointer">
                                    </th>
                                    <th class="p-4 w-20 text-center">Foto</th> 
                                    <th class="p-4">Nama Produk</th> 
                                    <th class="p-4 w-36">Kategori</th> 
                                    <th class="p-4 w-40">Harga</th> 
                                    <th class="p-4 w-16 text-center">Edit</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 font-medium text-slate-700"> 
                                <?php if (mysqli_num_rows($ambil_katalog) == 0): ?> 
                                    <tr>
                                        <td colspan="6" class="p-8 text-center text-slate-400 font-bold italic">Belum ada produk di katalog. Silakan tambah data di atas.</td> 
                                    </tr>
                                <?php else: ?>
                                    <?php while($row = mysqli_fetch_assoc($ambil_katalog)): ?> 
                                        <tr class="hover:bg-slate-50/60 transition"> 
                                            <td class="p-4 text-center">
                                                <input type="checkbox" name="katalog_id_hapus[]" value="<?= $row['id_produk']; ?>" onchange="hitungKatalogTerpilih()" class="check_katalog_child w-4 h-4 rounded text-blue-600 border-gray-300 focus:ring-blue-500 cursor-pointer">
                                            </td>
                                            <td class="p-4 text-center"> 
                                                <?php 
                                                    if (strpos($row['gambar'], 'images/') !== false) { 
                                                        $gambar_src = '../customer/' . $row['gambar']; 
                                                    } else {
                                                        $gambar_src = '../customer/images/katalog/' . $row['gambar']; 
                                                    }
                                                ?>
                                                <img src="<?= $gambar_src; ?>?v=<?= time(); ?>" class="w-12 h-12 object-cover rounded-lg border shadow-sm mx-auto"> 
                                            </td>
                                            <td class="p-4"> 
                                                <div class="font-bold text-slate-900 uppercase text-[11px] tracking-wide"><?= htmlspecialchars($row['nama_produk']); ?></div> 
                                                <div class="text-[11px] text-slate-400 mt-0.5"><?= htmlspecialchars($row['deskripsi']); ?></div> 
                                            </td>
                                            <td class="p-4 uppercase text-[10px] tracking-wider"><span class="bg-slate-100 px-2.5 py-1 rounded-md font-bold border border-slate-200/40 text-slate-800"><?= $row['kategori']; ?></span></td> 
                                            <td class="p-4 font-bold text-slate-900 text-[11px]">Rp <?= number_format($row['harga'], 0, ',', '.'); ?></td> 
                                            <td class="p-4 text-center">
                                                <button type="button" 
                                                        onclick="bukaModalEdit('<?= $row['id_produk']; ?>', '<?= htmlspecialchars($row['nama_produk'], ENT_QUOTES); ?>', '<?= $row['kategori']; ?>', '<?= $row['harga']; ?>', '<?= htmlspecialchars($row['deskripsi'], ENT_QUOTES); ?>', '<?= htmlspecialchars($row['link_ecommerce'], ENT_QUOTES); ?>')" 
                                                        class="text-blue-500 hover:text-blue-700 text-sm p-1.5 transition inline-block">
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

                <!-- MODAL BOX KONFIRMASI HAPUS KATALOG MASSAL (CENTERED) -->
                <div id="modal_hapus_massal_katalog" class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-50 hidden items-center justify-center p-4">
                    <div class="bg-white rounded-2xl max-w-sm w-full p-6 shadow-2xl border border-gray-100 space-y-4 text-center">
                        <div class="w-12 h-12 bg-red-50 text-red-500 rounded-full flex items-center justify-center text-xl mx-auto border border-red-100">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                        <div class="space-y-1">
                            <h3 class="text-sm font-black uppercase text-slate-900 tracking-wide">Hapus Komponen Terpilih</h3>
                            <p class="text-xs font-semibold text-slate-400 leading-relaxed">Apakah Anda yakin menghapus permanen (<span id="text_katalog_total" class="text-red-500 font-bold">0</span> produk) dari database katalog Hanbit Labs?</p>
                        </div>
                        <div class="pt-2 flex gap-2">
                            <button type="button" onclick="tutupModalHapusMassal()" class="flex-1 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold py-2.5 rounded-xl transition uppercase tracking-wider text-[10px]">Batal</button>
                            <button type="submit" name="eksekusi_hapus_katalog_massal" class="flex-1 bg-red-500 hover:bg-red-600 text-white font-black py-2.5 rounded-xl transition uppercase tracking-wider text-[10px] shadow-sm flex items-center justify-center">Ya, Hapus</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </main>

    <!-- ==========================================
    POP-UP MODAL EDIT BOX (LURUS DI TENHAH SCREEN)
    ========================================== -->
    <div id="modal_edit_produk" class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-50 hidden items-center justify-center px-4">
        <div class="bg-white rounded-[1.5rem] max-w-xl w-full p-6 shadow-2xl border border-gray-100 space-y-4 max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-center border-b pb-3">
                <h3 class="text-sm font-black uppercase text-slate-900"><i class="fas fa-edit text-blue-500 mr-2"></i> Perbarui Data Komponen</h3>
                <button type="button" onclick="tutupModalEdit()" class="text-slate-400 hover:text-slate-600"><i class="fas fa-times text-lg"></i></button>
            </div>
            
            <form action="" method="POST" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-12 gap-4 text-xs font-semibold text-slate-600">
                <input type="hidden" name="id_produk" id="edit_id_produk">

                <div class="md:col-span-12 space-y-1">
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Nama Komponen / Aksesori</label>
                    <input type="text" name="nama_produk" id="edit_nama_produk" required class="w-full px-4 py-2.5 bg-slate-50 border border-gray-200 rounded-xl focus:outline-none focus:border-blue-400 focus:bg-white transition text-slate-800 font-bold">
                </div>
                
                <div class="md:col-span-6 space-y-1">
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Kategori</label>
                    <select name="kategori" id="edit_kategori" required class="w-full px-3 py-2.5 bg-slate-50 border border-gray-200 rounded-xl focus:outline-none focus:border-blue-400 focus:bg-white transition text-slate-700 font-bold uppercase">
                        <option value="ram">Memory RAM</option>
                        <option value="storage">Storage SSD/HDD</option>
                        <option value="lcd">LCD Screen</option>
                        <option value="keyboard">Keyboard</option>
                        <option value="charger">Charger</option>
                        <option value="pasta">Thermal Paste</option>
                        <option value="flashdisk">Flashdisk</option>
                        <option value="aksesoris">Aksesori</option>
                    </select>
                </div>

                <div class="md:col-span-6 space-y-1">
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Harga Pas (Fix)</label>
                    <input type="number" name="harga" id="edit_harga" required class="w-full px-4 py-2.5 bg-slate-50 border border-gray-200 rounded-xl focus:outline-none focus:border-blue-400 focus:bg-white transition text-slate-800 font-bold">
                </div>

                <div class="md:col-span-12 space-y-1">
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Deskripsi & Spesifikasi Singkat</label>
                    <input type="text" name="deskripsi" id="edit_deskripsi" required class="w-full px-4 py-2.5 bg-slate-50 border border-gray-200 rounded-xl focus:outline-none focus:border-blue-400 focus:bg-white transition text-slate-700">
                </div>

                <div class="md:col-span-12 space-y-1">
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Link E-Commerce</label>
                    <input type="url" name="link_ecommerce" id="edit_link_ecommerce" required class="w-full px-4 py-2.5 bg-slate-50 border border-gray-200 rounded-xl focus:outline-none focus:border-blue-400 focus:bg-white transition text-blue-600 font-sans">
                </div>

                <div class="md:col-span-12 space-y-1">
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Ganti Foto Fisik (Kosongkan jika tidak ingin diubah)</label>
                    <input type="file" name="gambar" accept="image/*" class="w-full px-2 py-1.5 bg-slate-50 border border-gray-200 rounded-xl focus:outline-none focus:border-blue-400 transition">
                </div>

                <div class="md:col-span-12 pt-3 flex justify-end gap-2 border-t">
                    <button type="button" onclick="tutupModalEdit()" class="bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold px-4 py-2 rounded-xl transition uppercase tracking-wider text-[10px]">Batal</button>
                    <button type="submit" name="edit_produk" class="bg-blue-500 hover:bg-blue-600 text-white font-black px-5 py-2 rounded-xl transition uppercase tracking-wider text-[10px] shadow-sm">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- JAVASCRIPT BULK CONTROL & MODALS ENGINE -->
    <script>
        function toggleSemuaKatalog(master) {
            const checkboxes = document.querySelectorAll('.check_katalog_child');
            checkboxes.forEach(cb => cb.checked = master.checked);
            hitungKatalogTerpilih();
        }

        function hitungKatalogTerpilih() {
            const checkboxes = document.querySelectorAll('.check_katalog_child');
            let totalTerpilih = 0;
            checkboxes.forEach(cb => { if(cb.checked) totalTerpilih++; });

            const btnHapus = document.getElementById('btn_hapus_katalog_massal');
            document.getElementById('count_katalog_terpilih').innerText = totalTerpilih;

            if(totalTerpilih > 0) {
                btnHapus.classList.remove('hidden');
                setTimeout(() => { btnHapus.classList.remove('scale-95', 'opacity-0'); btnHapus.classList.add('scale-100', 'opacity-100'); }, 10);
            } else {
                btnHapus.classList.remove('scale-100', 'opacity-100'); btnHapus.classList.add('scale-95', 'opacity-0');
                setTimeout(() => { btnHapus.classList.add('hidden'); }, 300);
                document.getElementById('master_check_katalog').checked = false;
            }
        }

        function bukaModalHapusMassal() {
            const total = document.getElementById('count_katalog_terpilih').innerText;
            document.getElementById('text_katalog_total').innerText = total;
            const m = document.getElementById('modal_hapus_massal_katalog');
            m.classList.remove('hidden'); m.classList.add('flex');
        }

        function tutupModalHapusMassal() {
            const m = document.getElementById('modal_hapus_massal_katalog');
            m.classList.remove('flex'); m.classList.add('hidden');
        }

        function bukaModalEdit(id, nama, kategori, harga, deskiz, link) {
            document.getElementById('edit_id_produk').value = id;
            document.getElementById('edit_nama_produk').value = nama;
            document.getElementById('edit_kategori').value = kategori;
            document.getElementById('edit_harga').value = harga;
            document.getElementById('edit_deskripsi').value = deskiz;
            document.getElementById('edit_link_ecommerce').value = link;

            const m = document.getElementById('modal_edit_produk');
            m.classList.remove('hidden'); m.classList.add('flex');
        }

        function tutupModalEdit() {
            const m = document.getElementById('modal_edit_produk');
            m.classList.remove('flex'); m.classList.add('hidden');
        }
    </script>
</body>
</html>