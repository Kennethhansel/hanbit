<?php
require_once 'config.php';
require_once 'koneksi.php';
proteksi_halaman();

// ==========================================
// 🌟 NEW FEATURE: LOGIKA UTAMA MANAJEMEN KATEGORI KATALOG (CRUD KATEGORI)
// ==========================================

// 1. TAMBAH KATEGORI KATALOG
if (isset($_POST['tambah_kategori_baru'])) {
    $nama_kat = mysqli_real_escape_string($koneksi, trim($_POST['nama_kategori_baru']));
    $slug_kat = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $nama_kat));

    if (!empty($nama_kat)) {
        $query_kat = "INSERT IGNORE INTO tb_kategori_katalog (nama_kategori, slug_kategori) VALUES ('$nama_kat', '$slug_kat')";
        mysqli_query($koneksi, $query_kat);
        header("Location: katalog_produk.php?status=sukses_kat");
        exit;
    }
}

// 2. EDIT KATEGORI KATALOG
if (isset($_POST['update_kategori_master'])) {
    $id_kat_edit = intval($_POST['id_kategori_master']);
    $nama_kat_baru = mysqli_real_escape_string($koneksi, trim($_POST['nama_kategori_edit']));
    $slug_kat_baru = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $nama_kat_baru));

    if (!empty($nama_kat_baru)) {
        // Ambil slug lama sebelum diupdate untuk sinkronisasi data relasi di tb_katalog
        $q_slug_lama = mysqli_query($koneksi, "SELECT slug_kategori FROM tb_kategori_katalog WHERE id_kategori = $id_kat_edit");
        $d_slug_lama = mysqli_fetch_assoc($q_slug_lama);
        
        if ($d_slug_lama) {
            $slug_lama = $d_slug_lama['slug_kategori'];
            // Update tabel master kategori katalog
            mysqli_query($koneksi, "UPDATE tb_kategori_katalog SET nama_kategori='$nama_kat_baru', slug_kategori='$slug_kat_baru' WHERE id_kategori=$id_kat_edit");
            // SINKRONISASI: Update seluruh item barang yang pakai kategori lama agar beralih ke slug baru
            mysqli_query($koneksi, "UPDATE tb_katalog SET kategori='$slug_kat_baru' WHERE kategori='$slug_lama'");
        }
        header("Location: katalog_produk.php?status=diperbarui_kat");
        exit;
    }
}

// 3. HAPUS KATEGORI KATALOG INDIVIDU
if (isset($_GET['hapus_kategori_id'])) {
    $id_kat_hapus = intval($_GET['hapus_kategori_id']);
    
    // Ambil slug yang mau dibersihkan
    $q_slug_hapus = mysqli_query($koneksi, "SELECT slug_kategori FROM tb_kategori_katalog WHERE id_kategori = $id_kat_hapus");
    $d_slug_hapus = mysqli_fetch_assoc($q_slug_hapus);
    
    if ($d_slug_hapus) {
        $slug_hapus = $d_slug_hapus['slug_kategori'];
        // Hapus kategorinya dari master
        mysqli_query($koneksi, "DELETE FROM tb_kategori_katalog WHERE id_kategori = $id_kat_hapus");
        // SINKRONISASI KEAMANAN: Set produk yang kategorinya dihapus menjadi 'uncategorized' agar web customer tidak crash
        mysqli_query($koneksi, "UPDATE tb_katalog SET kategori='uncategorized' WHERE kategori='$slug_hapus'");
    }
    header("Location: katalog_produk.php?status=terhapus_kat");
    exit;
}

// ==========================================
// A. LOGIKA PROSES TAMBAH PRODUK (CREATE)
// ==========================================
if (isset($_POST['tambah_produk'])) { 
    $nama_produk = mysqli_real_escape_string($koneksi, trim($_POST['nama_produk'])); 
    $kategori    = mysqli_real_escape_string($koneksi, $_POST['kategori']); // Menyimpan slug kategori dinamis
    $harga       = intval($_POST['harga']); 
    $deskripsi   = mysqli_real_escape_string($koneksi, trim($_POST['deskripsi'])); 
    $link_beli   = mysqli_real_escape_string($koneksi, trim($_POST['link_ecommerce'])); 
    
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
    $nama_produk = mysqli_real_escape_string($koneksi, trim($_POST['nama_produk']));
    $kategori    = mysqli_real_escape_string($koneksi, $_POST['kategori']);
    $harga       = intval($_POST['harga']);
    $deskripsi   = mysqli_real_escape_string($koneksi, trim($_POST['deskripsi']));
    $link_beli   = mysqli_real_escape_string($koneksi, trim($_POST['link_ecommerce']));
    
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
// C. LOGIKA ENGINE HAPUS MASSAL (BULK DELETE)
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

    <main class="flex-1 p-8 overflow-y-auto"> 
        <div class="max-w-full mx-auto px-2 space-y-6"> 
            
            <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-4 min-h-[56px]">
                <div>
                    <h1 class="text-2xl font-black tracking-tight text-slate-900">Katalog Produk</h1> 
                    <p class="text-xs text-slate-400 font-medium">Tambah atau hapus rekomendasi sparepart upgrade dan aksesori Hanbit Labs.</p> 
                </div>
                
                <button type="button" id="btn_hapus_katalog_massal" onclick="bukaModalHapusMassal()" 
                        class="hidden bg-red-500 hover:bg-red-600 text-white font-black px-6 py-2.5 rounded-xl text-xs uppercase tracking-wider transition-all duration-300 shadow-md flex items-center gap-2 shrink-0 transform scale-95 opacity-0">
                    <i class="fas fa-trash-alt text-xs"></i> Hapus Terpilih (<span id="count_katalog_terpilih">0</span>)
                </button>
            </div>

            <?php if (isset($_GET['status'])): ?>
                <div class="text-xs font-bold px-4 py-3 rounded-xl border bg-white shadow-sm flex items-center gap-1.5">
                    <?php
                    if ($_GET['status'] == 'sukses') echo "<span class='text-emerald-600'>✅ Komponen baru berhasil masuk katalog!</span>";
                    elseif ($_GET['status'] == 'diperbarui') echo "<span class='text-blue-600'>✅ Data spesifikasi komponen berhasil diperbarui!</span>";
                    elseif ($_GET['status'] == 'terhapus') echo "<span class='text-rose-600'>🗑️ Item produk terpilih berhasil dibersihkan dari katalog.</span>";
                    elseif ($_GET['status'] == 'sukses_kat') echo "<span class='text-purple-600'>✅ Master Kategori Baru berhasil didaftarkan ke katalog!</span>";
                    elseif ($_GET['status'] == 'diperbarui_kat') echo "<span class='text-amber-600'>✅ Nama Kategori berhasil diubah dan disinkronkan ke seluruh produk!</span>";
                    elseif ($_GET['status'] == 'terhapus_kat') echo "<span class='text-red-600'>🗑️ Kategori berhasil dihapus, produk terkait dialihkan ke Uncategorized agar aman.</span>";
                    ?>
                </div>
            <?php endif; ?>

            <div class="grid grid-cols-1 xl:grid-cols-12 gap-6 items-start">
                
                <div class="xl:col-span-4 space-y-4">
                    <div class="bg-white border border-gray-200/80 p-5 rounded-2xl shadow-sm space-y-3">
                        <h3 class="text-xs font-extrabold uppercase text-slate-900 tracking-wider border-b pb-2">
                            <i class="fas fa-tags text-teal-600 mr-1"></i> Buat Kategori Baru
                        </h3>
                        <form action="" method="POST" class="space-y-3 text-xs font-semibold text-slate-600">
                            <input type="hidden" name="tambah_kategori_baru" value="1">
                            <div class="space-y-1">
                                <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Nama Kategori Baru</label>
                                <input type="text" name="nama_kategori_baru" required placeholder="Contoh: Battery Pack / Cooling Fan" class="w-full px-3 py-2 bg-slate-50 border border-gray-200 rounded-xl font-bold focus:outline-none focus:border-purple-400 focus:bg-white transition text-slate-800">
                            </div>
                            <button type="submit" class="w-full bg-teal-600 hover:bg-purple-700 text-white font-bold py-2.5 rounded-xl transition uppercase text-[10px] tracking-wider">
                                + Simpan Master Kategori
                            </button>
                        </form>
                    </div>

                    <div class="bg-white border border-gray-200/80 p-5 rounded-2xl shadow-sm space-y-3">
                        <h3 class="text-xs font-extrabold uppercase text-slate-900 tracking-wider border-b pb-2">
                            <i class="fas fa-list-ol text-slate-400 mr-1"></i> Daftar Kategori
                        </h3>
                        <div class="max-h-[220px] overflow-y-auto rounded-xl border border-gray-100">
                            <table class="w-full text-left border-collapse text-[11px] font-bold">
                                <tbody class="divide-y divide-gray-50 text-slate-600">
                                    <?php
                                    $q_list_k = mysqli_query($koneksi, "SELECT * FROM tb_kategori_katalog ORDER BY nama_kategori ASC");
                                    while($lk = mysqli_fetch_assoc($q_list_k)):
                                    ?>
                                        <tr class="hover:bg-slate-50/50 transition">
                                            <td class="p-3 text-slate-800 uppercase font-bold"><?= htmlspecialchars($lk['nama_kategori']); ?></td>
                                            <td class="p-3 text-right space-x-2 shrink-0 w-24">
                                                <button type="button" onclick="bukaModalEditKategori('<?= $lk['id_kategori']; ?>', '<?= htmlspecialchars($lk['nama_kategori'], ENT_QUOTES); ?>')" class="text-blue-500 hover:text-blue-700 text-xs" title="Ubah Nama Kategori">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <a href="katalog_produk.php?hapus_kategori_id=<?= $lk['id_kategori']; ?>" onclick="return confirm('Hapus master kategori ini? Produk terkait akan otomatis dialihkan ke status aman uncategorized.')" class="text-red-500 hover:text-red-700 text-xs" title="Hapus Kategori">
                                                    <i class="fas fa-trash-alt"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="xl:col-span-8 bg-white border border-gray-200/80 p-6 rounded-2xl shadow-sm space-y-4"> 
                    <h3 class="text-sm font-extrabold uppercase text-slate-900 tracking-wider border-b pb-2"> 
                        <i class="fas fa-plus-circle text-amber-500 mr-1"></i> Tambah Part / Aksesori Baru 
                    </h3>
                    <form action="" method="POST" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-12 gap-4 text-xs font-semibold text-slate-600"> 
                        <div class="md:col-span-6 space-y-1"> 
                            <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Komponen / Aksesori</label> 
                            <input type="text" name="nama_produk" placeholder="Contoh: RAM DDR4 Corsair 8GB" required class="w-full px-4 py-2.5 bg-slate-50 border border-gray-200 rounded-xl font-bold focus:outline-none focus:border-yellow-400 focus:bg-white transition text-slate-800"> 
                        </div>
                        <div class="md:col-span-3 space-y-1"> 
                            <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Kategori</label> 
                            <select name="kategori" required class="w-full px-3 py-2.5 bg-slate-50 border border-gray-200 rounded-xl focus:outline-none focus:border-yellow-400 focus:bg-white transition text-slate-700 font-bold uppercase cursor-pointer"> 
                                <?php
                                $q_kat_loop = mysqli_query($koneksi, "SELECT * FROM tb_kategori_katalog ORDER BY nama_kategori ASC");
                                while($k = mysqli_fetch_assoc($q_kat_loop)):
                                ?>
                                    <option value="<?= $k['slug_kategori']; ?>"><?= htmlspecialchars($k['nama_kategori']); ?></option>
                                <?php endwhile; ?>
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
            </div>

            <form id="form_katalog_massal" action="katalog_produk.php" method="POST">
                <div class="bg-white border border-gray-200/80 rounded-2xl shadow-sm overflow-hidden"> 
                    <div class="p-4 border-b border-gray-100 bg-slate-50/50"> 
                        <h3 class="text-xs font-extrabold uppercase text-slate-900 tracking-wider">Daftar Produk Rekomendasi</h3> 
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
                                    <th class="p-4 w-44">Kategori</th> 
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
                                            <td class="p-4 text-slate-600 font-semibold uppercase text-[10px] tracking-wide">
                                                <?php
                                                // Sinkronisasi dinamis label teks kategori dari database master baru
                                                $slug_tmp = $row['kategori'];
                                                if ($slug_tmp === 'uncategorized') {
                                                    echo '<span class="text-gray-400 italic">Uncategorized</span>';
                                                } else {
                                                    $q_k_text = mysqli_query($koneksi, "SELECT nama_kategori FROM tb_kategori_katalog WHERE slug_kategori = '$slug_tmp' LIMIT 1");
                                                    $d_k_text = mysqli_fetch_assoc($q_k_text);
                                                    echo htmlspecialchars($d_k_text['nama_kategori'] ?? $row['kategori']);
                                                }
                                                ?>
                                            </td> 
                                            <td class="p-4 font-bold text-slate-900 text-[11px]">Rp <?= number_format($row['harga'], 0, ',', '.'); ?></td> 
                                            <td class="p-4 text-center">
                                                <button type="button" 
                                                        class="btn-pemicu-edit-katalog text-blue-500 hover:text-blue-700 text-sm p-1.5 transition inline-block"
                                                        data-id="<?= $row['id_produk']; ?>"
                                                        data-nama="<?= htmlspecialchars($row['nama_produk'], ENT_QUOTES); ?>"
                                                        data-kategori="<?= $row['kategori']; ?>"
                                                        data-harga="<?= $row['harga']; ?>"
                                                        data-deskripsi="<?= htmlspecialchars($row['deskripsi'], ENT_QUOTES); ?>"
                                                        data-link="<?= htmlspecialchars($row['link_ecommerce'], ENT_QUOTES); ?>">
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
                        <?php
                        $q_kat_edit = mysqli_query($koneksi, "SELECT * FROM tb_kategori_katalog ORDER BY nama_kategori ASC");
                        while($ke = mysqli_fetch_assoc($q_kat_edit)):
                        ?>
                            <option value="<?= $ke['slug_kategori']; ?>"><?= htmlspecialchars($ke['nama_kategori']); ?></option>
                        <?php endwhile; ?>
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

    <div id="modal_edit_kategori_master" class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-50 hidden items-center justify-center px-4">
        <div class="bg-white rounded-[1.5rem] max-w-md w-full p-6 shadow-2xl border border-gray-100 space-y-4">
            <div class="flex justify-between items-center border-b pb-2">
                <h3 class="text-sm font-black uppercase text-slate-900"><i class="fas fa-tags text-blue-500 mr-1"></i> Ubah Master Kategori</h3>
                <button type="button" onclick="tutupModalEditKategori()" class="text-slate-400 hover:text-slate-600"><i class="fas fa-times text-lg"></i></button>
            </div>
            <form action="" method="POST" class="space-y-4 text-xs font-semibold text-slate-600">
                <input type="hidden" name="id_kategori_master" id="edit_id_kategori_master">
                <div class="space-y-1">
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Nama Kategori Baru</label>
                    <input type="text" name="nama_kategori_edit" id="edit_nama_kategori_master" required class="w-full px-4 py-2.5 bg-slate-50 border border-gray-200 rounded-xl focus:outline-none focus:border-blue-400 font-bold text-slate-800">
                </div>
                <div class="flex justify-end gap-2 pt-2 border-t">
                    <button type="button" onclick="tutupModalEditKategori()" class="bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold px-4 py-2 rounded-xl transition uppercase text-[10px]">Batal</button>
                    <button type="submit" name="update_kategori_master" class="bg-blue-500 hover:bg-blue-600 text-white font-black px-5 py-2 rounded-xl transition uppercase text-[10px] shadow-sm">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // DOM EVENT LISTENERS UNTUK EDIT DATA KOMPONEN (ANTI-CRASH MULTILINE)
        document.addEventListener('DOMContentLoaded', function() {
            const tombolEditKatalog = document.querySelectorAll('.btn-pemicu-edit-katalog');
            tombolEditKatalog.forEach(btn => {
                btn.addEventListener('click', function() {
                    const id        = this.getAttribute('data-id');
                    const nama      = this.getAttribute('data-nama');
                    const kategori  = this.getAttribute('data-kategori');
                    const harga     = this.getAttribute('data-harga');
                    const deskripsi = this.getAttribute('data-deskripsi');
                    const link      = this.getAttribute('data-link');

                    document.getElementById('edit_id_produk').value = id;
                    document.getElementById('edit_nama_produk').value = nama;
                    document.getElementById('edit_kategori').value = kategori;
                    document.getElementById('edit_harga').value = harga;
                    document.getElementById('edit_deskripsi').value = deskripsi;
                    document.getElementById('edit_link_ecommerce').value = link;

                    const m = document.getElementById('modal_edit_produk');
                    m.classList.remove('hidden'); m.classList.add('flex');
                });
            });
        });

        // Handler Pop-up Manajemen Master Kategori Dinamis
        function bukaModalEditKategori(id, nama) {
            document.getElementById('edit_id_kategori_master').value = id;
            document.getElementById('edit_nama_kategori_master').value = nama;
            const modalKat = document.getElementById('modal_edit_kategori_master');
            modalKat.classList.remove('hidden'); modalKat.classList.add('flex');
        }

        function tutupModalEditKategori() {
            const modalKat = document.getElementById('modal_edit_kategori_master');
            modalKat.classList.remove('flex'); modalKat.classList.add('hidden');
        }

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

        function tutupModalEdit() {
            const m = document.getElementById('modal_edit_produk');
            m.classList.remove('flex'); m.classList.add('hidden');
        }
    </script>
</body>
</html>