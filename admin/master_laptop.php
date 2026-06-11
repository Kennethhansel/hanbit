<?php
require_once 'config.php';
require_once 'koneksi.php';
proteksi_halaman();

// ==========================================
// 1. LOGIKA PROSES DATA BRAND (CRUD)
// ==========================================
if (isset($_POST['tambah_brand'])) {
    $nama_brand = mysqli_real_escape_string($koneksi, strtoupper(trim($_POST['nama_brand'])));
    $nama_file   = $_FILES['logo_file']['name'];
    $tmp_name    = $_FILES['logo_file']['tmp_name'];
    $logo_baru   = time() . '_' . strtolower(str_replace(' ', '_', $nama_file));
    $jalur_simpan = '../customer/images/logo/' . $logo_baru;

    if (!is_dir('../customer/images/logo/')) {
        mkdir('../customer/images/logo/', 0777, true);
    }

    if (move_uploaded_file($tmp_name, $jalur_simpan)) {
        $query = "INSERT INTO laptop_brands (nama_brand, logo) VALUES ('$nama_brand', '$logo_baru')";
        mysqli_query($koneksi, $query);
        header("Location: master_laptop.php?status=brand_sukses");
        exit;
    }
}

if (isset($_POST['edit_brand'])) {
    $id_brand   = intval($_POST['id_brand']);
    $nama_brand = mysqli_real_escape_string($koneksi, strtoupper(trim($_POST['nama_brand'])));

    if (!empty($_FILES['logo_file']['name'])) {
        $nama_file   = $_FILES['logo_file']['name'];
        $tmp_name    = $_FILES['logo_file']['tmp_name'];
        $logo_baru   = time() . '_' . strtolower(str_replace(' ', '_', $nama_file));
        $jalur_simpan = '../customer/images/logo/' . $logo_baru;

        if (move_uploaded_file($tmp_name, $jalur_simpan)) {
            $cek_lama = mysqli_query($koneksi, "SELECT logo FROM laptop_brands WHERE id_brand = $id_brand");
            $data_lama = mysqli_fetch_assoc($cek_lama);
            if ($data_lama && !empty($data_lama['logo'])) {
                $path_hapus = '../customer/images/logo/' . $data_lama['logo'];
                if (file_exists($path_hapus)) {
                    unlink($path_hapus);
                }
            }
            $query = "UPDATE laptop_brands SET nama_brand = '$nama_brand', logo = '$logo_baru' WHERE id_brand = $id_brand";
        }
    } else {
        $query = "UPDATE laptop_brands SET nama_brand = '$nama_brand' WHERE id_brand = $id_brand";
    }
    mysqli_query($koneksi, $query);
    header("Location: master_laptop.php?status=brand_diperbarui");
    exit;
}

if (isset($_POST['eksekusi_hapus_brand_massal'])) {
    if (!empty($_POST['brands_id_hapus'])) {
        $gagal_hitung = 0;
        foreach ($_POST['brands_id_hapus'] as $id_brand_hapus) {
            $id_clean = intval($id_brand_hapus);
            $cek_relasi = mysqli_query($koneksi, "SELECT id_series FROM laptop_series WHERE id_brand = $id_clean LIMIT 1");
            if (mysqli_num_rows($cek_relasi) > 0) {
                $gagal_hitung++;
            } else {
                $cek_file = mysqli_query($koneksi, "SELECT logo FROM laptop_brands WHERE id_brand = $id_clean");
                $data_file = mysqli_fetch_assoc($cek_file);
                if ($data_file && !empty($data_file['logo'])) {
                    $path_hapus = '../customer/images/logo/' . $data_file['logo'];
                    if (file_exists($path_hapus)) {
                        unlink($path_hapus);
                    }
                }
                mysqli_query($koneksi, "DELETE FROM laptop_brands WHERE id_brand = $id_clean");
            }
        }
        header("Location: master_laptop.php?status=" . ($gagal_hitung > 0 ? "brand_gagal_relasi" : "brand_terhapus"));
        exit;
    }
}

// ==========================================
// 2. LOGIKA PROSES DATA SERIES (CRUD)
// ==========================================
if (isset($_POST['tambah_series'])) {
    $id_brand    = intval($_POST['id_brand']);
    $nama_series = mysqli_real_escape_string($koneksi, strtoupper(trim($_POST['nama_series'])));
    $nama_file   = $_FILES['foto_file']['name'];
    $tmp_name    = $_FILES['foto_file']['tmp_name'];
    $foto_baru   = time() . '_' . strtolower(str_replace(' ', '_', $nama_file));
    $jalur_simpan = '../customer/images/series/' . $foto_baru;

    if (!is_dir('../customer/images/series/')) {
        mkdir('../customer/images/series/', 0777, true);
    }

    if (move_uploaded_file($tmp_name, $jalur_simpan)) {
        $query = "INSERT INTO laptop_series (id_brand, nama_series, foto) VALUES ($id_brand, '$nama_series', '$foto_baru')";
        mysqli_query($koneksi, $query);
        header("Location: master_laptop.php?status=series_sukses");
        exit;
    }
}

if (isset($_POST['edit_series'])) {
    $id_series   = intval($_POST['id_series']);
    $id_brand    = intval($_POST['id_brand']);
    $nama_series = mysqli_real_escape_string($koneksi, strtoupper(trim($_POST['nama_series'])));

    if (!empty($_FILES['foto_file']['name'])) {
        $nama_file   = $_FILES['foto_file']['name'];
        $tmp_name    = $_FILES['foto_file']['tmp_name'];
        $foto_baru   = time() . '_' . strtolower(str_replace(' ', '_', $nama_file));
        $jalur_simpan = '../customer/images/series/' . $foto_baru;

        if (move_uploaded_file($tmp_name, $jalur_simpan)) {
            $cek_lama = mysqli_query($koneksi, "SELECT foto FROM laptop_series WHERE id_series = $id_series");
            $data_lama = mysqli_fetch_assoc($cek_lama);
            if ($data_lama && !empty($data_lama['foto'])) {
                $path_hapus = '../customer/images/series/' . $data_lama['foto'];
                if (file_exists($path_hapus)) {
                    unlink($path_hapus);
                }
            }
            $query = "UPDATE laptop_series SET id_brand = $id_brand, nama_series = '$nama_series', foto = '$foto_baru' WHERE id_series = $id_series";
        }
    } else {
        $query = "UPDATE laptop_series SET id_brand = $id_brand, nama_series = '$nama_series' WHERE id_series = $id_series";
    }
    mysqli_query($koneksi, $query);
    header("Location: master_laptop.php?status=series_diperbarui");
    exit;
}

if (isset($_POST['eksekusi_hapus_series_massal'])) {
    if (!empty($_POST['series_id_hapus'])) {
        foreach ($_POST['series_id_hapus'] as $id_series_hapus) {
            $id_clean = intval($id_series_hapus);
            $cek_file = mysqli_query($koneksi, "SELECT foto FROM laptop_series WHERE id_series = $id_clean");
            $data_file = mysqli_fetch_assoc($cek_file);
            if ($data_file && !empty($data_file['foto'])) {
                $path_hapus = '../customer/images/series/' . $data_file['foto'];
                if (file_exists($path_hapus)) {
                    unlink($path_hapus);
                }
            }
            mysqli_query($koneksi, "DELETE FROM laptop_series WHERE id_series = $id_clean");
        }
        header("Location: master_laptop.php?status=series_terhapus");
        exit;
    }
}

// ==========================================
// 3. LOGIKA PROSES MASTER MASALAH (CRUD + MASS DELETION)
// ==========================================
if (isset($_POST['tambah_masalah'])) {
    $nama_masalah      = mysqli_real_escape_string($koneksi, trim($_POST['nama_masalah']));
    $deskripsi_masalah = mysqli_real_escape_string($koneksi, trim($_POST['deskripsi_masalah']));
    $penyebab_masalah  = mysqli_real_escape_string($koneksi, trim($_POST['penyebab_masalah']));
    $saran_teknisi     = mysqli_real_escape_string($koneksi, trim($_POST['saran_teknisi']));
    $harga_estimasi    = intval($_POST['harga_estimasi']);

    mysqli_query($koneksi, "INSERT INTO master_masalah (nama_masalah, deskripsi_masalah, penyebab_masalah, saran_teknisi, harga_estimasi) VALUES ('$nama_masalah', '$deskripsi_masalah', '$penyebab_masalah', '$saran_teknisi', $harga_estimasi)");
    header("Location: master_laptop.php?status=masalah_sukses");
    exit;
}

if (isset($_POST['edit_masalah'])) {
    $id_masalah        = intval($_POST['id_masalah']);
    $nama_masalah      = mysqli_real_escape_string($koneksi, trim($_POST['nama_masalah']));
    $deskripsi_masalah = mysqli_real_escape_string($koneksi, trim($_POST['deskripsi_masalah']));
    $penyebab_masalah  = mysqli_real_escape_string($koneksi, trim($_POST['penyebab_masalah']));
    $saran_teknisi     = mysqli_real_escape_string($koneksi, trim($_POST['saran_teknisi']));
    $harga_estimasi    = intval($_POST['harga_estimasi']);

    mysqli_query($koneksi, "UPDATE master_masalah SET nama_masalah = '$nama_masalah', deskripsi_masalah = '$deskripsi_masalah', penyebab_masalah = '$penyebab_masalah', saran_teknisi = '$saran_teknisi', harga_estimasi = $harga_estimasi WHERE id_masalah = $id_masalah");
    header("Location: master_laptop.php?status=masalah_diperbarui");
    exit;
}

if (isset($_POST['hapus_masalah'])) {
    $id_masalah = intval($_POST['id_masalah']);
    mysqli_query($koneksi, "DELETE FROM master_masalah WHERE id_masalah = $id_masalah");
    header("Location: master_laptop.php?status=masalah_terhapus");
    exit;
}

if (isset($_POST['eksekusi_hapus_masalah_massal'])) {
    if (!empty($_POST['masalah_id_hapus'])) {
        foreach ($_POST['masalah_id_hapus'] as $id_masalah_hapus) {
            $id_clean = intval($id_masalah_hapus);
            mysqli_query($koneksi, "DELETE FROM master_masalah WHERE id_masalah = $id_clean");
        }
        header("Location: master_laptop.php?status=masalah_terhapus");
        exit;
    }
}

// Ambil Data Terkini
$result_brand = mysqli_query($koneksi, "SELECT * FROM laptop_brands ORDER BY nama_brand ASC");
$brands_array = [];
while ($b_row = mysqli_fetch_assoc($result_brand)) {
    $brands_array[] = $b_row;
}

$result_masalah = mysqli_query($koneksi, "SELECT * FROM master_masalah ORDER BY id_masalah ASC");
$query_series = "SELECT s.*, b.nama_brand FROM laptop_series s JOIN laptop_brands b ON s.id_brand = b.id_brand ORDER BY b.nama_brand ASC, s.nama_series ASC";
$result_series = mysqli_query($koneksi, $query_series);
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hanbit Admin - Kelola Katalog & Rincian Harga</title>
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

            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4 min-h-[56px]">
                <div>
                    <h1 class="text-2xl font-black tracking-tight text-slate-900">Katalog Data Master Laptop</h1>
                    <p class="text-xs text-slate-400 font-medium">Kelola data merek, tipe, beserta rincian analisa penyebab kerusakan dan saran teknisi Hanbit Labs.</p>
                </div>
                <div class="flex items-center gap-2.5 shrink-0">
                    <button type="button" id="btn_hapus_brand_massal" onclick="bukaModalHapusBrand()" class="hidden bg-red-500 hover:bg-red-600 text-white font-black px-4 py-2.5 rounded-xl text-xs uppercase tracking-wider transition-all duration-300 shadow-md flex items-center gap-1.5 transform scale-95 opacity-0">
                        <i class="fas fa-trash-alt text-[10px]"></i> Hapus Merek (<span id="count_brand_terpilih">0</span>)
                    </button>
                    <button type="button" id="btn_hapus_masalah_massal" onclick="bukaModalHapusMasalahMassal()" class="hidden bg-red-500 hover:bg-red-600 text-white font-black px-4 py-2.5 rounded-xl text-xs uppercase tracking-wider transition-all duration-300 shadow-md flex items-center gap-1.5 transform scale-95 opacity-0">
                        <i class="fas fa-trash-alt text-[10px]"></i> Hapus Masalah (<span id="count_masalah_terpilih">0</span>)
                    </button>
                    <button type="button" id="btn_hapus_series_massal" onclick="bukaModalHapusSeries()" class="hidden bg-red-500 hover:bg-red-600 text-white font-black px-4 py-2.5 rounded-xl text-xs uppercase tracking-wider transition-all duration-300 shadow-md flex items-center gap-1.5 transform scale-95 opacity-0">
                        <i class="fas fa-trash-alt text-[10px]"></i> Hapus Tipe (<span id="count_series_terpilih">0</span>)
                    </button>

                    <button type="button" onclick="bukaModalMasalah('tambah')" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold px-4 py-2.5 rounded-xl text-xs uppercase tracking-wider transition shadow-sm flex items-center gap-1.5">
                        <i class="fas fa-plus text-[10px]"></i> Masalah & Rincian
                    </button>
                    <button type="button" onclick="bukaModalBrand('tambah')" class="bg-black hover:bg-slate-900 text-white font-bold px-4 py-2.5 rounded-xl text-xs uppercase tracking-wider transition shadow-sm flex items-center gap-1.5">
                        <i class="fas fa-plus text-[10px]"></i> Brand
                    </button>
                    <button type="button" onclick="bukaModalSeries('tambah')" class="bg-[#facc15] hover:bg-[#eab308] text-slate-955 font-black px-4 py-2.5 rounded-xl text-xs uppercase tracking-wider transition shadow-sm flex items-center gap-1.5">
                        <i class="fas fa-plus text-[10px]"></i> Tipe Series
                    </button>
                </div>
            </div>

            <?php if (isset($_GET['status'])): ?>
                <div class="text-xs font-bold px-4 py-3 rounded-xl border bg-white shadow-sm">
                    <?php
                    if ($_GET['status'] == 'brand_sukses') echo "<span class='text-emerald-600'>✅ Brand baru berhasil ditambahkan!</span>";
                    elseif ($_GET['status'] == 'brand_diperbarui') echo "<span class='text-blue-600'>✅ Nama & logo brand berhasil diperbarui!</span>";
                    elseif ($_GET['status'] == 'brand_terhapus') echo "<span class='text-rose-600'>🗑️ Data brand terpilih berhasil dibersihkan.</span>";
                    elseif ($_GET['status'] == 'brand_gagal_relasi') echo "<span class='text-red-600'>❌ Gagal menghapus! Masih ada tipe series laptop aktif yang terikat dengan merek tersebut.</span>";
                    elseif ($_GET['status'] == 'series_sukses') echo "<span class='text-emerald-600'>✅ Tipe series baru berhasil disimpan!</span>";
                    elseif ($_GET['status'] == 'series_diperbarui') echo "<span class='text-blue-600'>✅ File foto seri laptop berhasil diperbarui!</span>";
                    elseif ($_GET['status'] == 'series_terhapus') echo "<span class='text-rose-600'>🗑️ Varian seri laptop terpilih berhasil dihapus.</span>";
                    elseif ($_GET['status'] == 'masalah_sukses') echo "<span class='text-emerald-600'>✅ Masalah baru & rincian penyebab serta saran berhasil disimpan!</span>";
                    elseif ($_GET['status'] == 'masalah_diperbarui') echo "<span class='text-blue-600'>✅ Analisa kerusakan masalah berhasil diperbarui!</span>";
                    elseif ($_GET['status'] == 'masalah_terhapus') echo "<span class='text-rose-600'>🗑️ Opsi jenis kerusakan berhasil dihapus dari sistem.</span>";
                    ?>
                </div>
            <?php endif; ?>

            <div class="grid grid-cols-1 xl:grid-cols-12 gap-6 items-start">

                <div class="xl:col-span-3 bg-white border border-gray-200/80 rounded-2xl shadow-sm overflow-hidden">
                    <div class="p-4 border-b border-gray-100 bg-slate-50/50 flex justify-between items-center">
                        <h3 class="text-xs font-extrabold uppercase text-slate-900 tracking-wider">Master Brand</h3>
                        <span class="bg-slate-900 text-white text-[10px] font-bold px-2 py-0.5 rounded-full uppercase"><?= mysqli_num_rows($result_brand); ?> Brand</span>
                    </div>

                    <form id="form_brand_massal" action="master_laptop.php" method="POST" class="overflow-x-auto">
                        <table class="w-full text-left border-collapse text-xs">
                            <thead>
                                <tr class="bg-slate-100/60 text-slate-400 font-bold uppercase border-b border-gray-100 select-none">
                                    <th class="p-4 w-10 text-center"><input type="checkbox" id="master_check_brand" onclick="toggleSemuaBrand(this)" class="w-4 h-4 rounded text-blue-600 border-gray-300 focus:ring-blue-500 cursor-pointer"></th>
                                    <th class="p-4 w-12 text-center">Logo</th>
                                    <th class="p-4">Brand</th>
                                    <th class="p-4 text-center w-10">Edit</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 font-medium text-slate-700">
                                <?php mysqli_data_seek($result_brand, 0);
                                while ($row_b = mysqli_fetch_assoc($result_brand)): ?>
                                    <tr class="hover:bg-slate-50/60 transition">
                                        <td class="p-4 text-center"><input type="checkbox" name="brands_id_hapus[]" value="<?= $row_b['id_brand']; ?>" onchange="hitungBrandTerpilih()" class="check_brand_child w-4 h-4 rounded text-blue-600 border-gray-300 focus:ring-blue-500 cursor-pointer"></td>
                                        <td class="p-3 text-center">
                                            <?php $src_logo = (strpos($row_b['logo'], 'images/') !== false) ? '../customer/' . $row_b['logo'] : '../customer/images/logo/' . $row_b['logo']; ?>
                                            <img src="<?= $src_logo; ?>?v=<?= time(); ?>" class="w-7 h-7 object-contain rounded border bg-white mx-auto shadow-xs" onerror="this.src='../customer/images/logo/default.png'">
                                        </td>
                                        <td class="p-3 font-bold text-slate-900 uppercase text-[11px]"><?= htmlspecialchars($row_b['nama_brand']); ?></td>
                                        <td class="p-3 text-center">
                                            <button type="button" onclick="bukaModalBrand('edit', '<?= $row_b['id_brand']; ?>', '<?= htmlspecialchars($row_b['nama_brand'], ENT_QUOTES); ?>')" class="text-blue-500 hover:text-blue-700 text-sm p-1 transition"><i class="fas fa-edit"></i></button>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </form>
                </div>

                <div class="xl:col-span-5 bg-white border border-gray-200/80 rounded-2xl shadow-sm overflow-hidden">
                    <div class="p-4 border-b border-gray-100 bg-slate-50/50 flex justify-between items-center">
                        <h3 class="text-xs font-extrabold uppercase text-slate-900 tracking-wider">Master Kerusakan & Harga</h3>
                        <span class="bg-emerald-600 text-white text-[10px] font-bold px-2 py-0.5 rounded-full uppercase"><?= mysqli_num_rows($result_masalah); ?> Opsi</span>
                    </div>
                    
                    <form id="form_masalah_massal" action="master_laptop.php" method="POST" class="overflow-x-auto">
                        <table class="w-full text-left border-collapse text-xs">
                            <thead>
                                <tr class="bg-slate-100/60 text-slate-400 font-bold uppercase border-b border-gray-100 select-none">
                                    <th class="p-4 w-10 text-center">
                                        <input type="checkbox" id="master_check_masalah" onclick="toggleSemuaMasalah(this)" class="w-4 h-4 rounded text-emerald-600 border-gray-300 focus:ring-emerald-500 cursor-pointer">
                                    </th>
                                    <th class="p-4">Deskripsi Masalah</th>
                                    <th class="p-4 w-28 text-right">Harga Estimasi</th>
                                    <th class="p-4 text-center w-12">Edit</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 font-medium text-slate-700">
                                <?php if (mysqli_num_rows($result_masalah) == 0): ?>
                                    <tr>
                                        <td colspan="4" class="p-6 text-center text-slate-400 italic font-bold">Belum ada jenis kerusakan terdaftar.</td>
                                    </tr>
                                <?php else: ?>
                                    <?php while ($row_m = mysqli_fetch_assoc($result_masalah)): ?>
                                        <tr class="hover:bg-slate-50/60 transition">
                                            <td class="p-4 text-center">
                                                <input type="checkbox" name="masalah_id_hapus[]" value="<?= $row_m['id_masalah']; ?>" onchange="hitungMasalahTerpilih()" class="check_masalah_child w-4 h-4 rounded text-emerald-600 border-gray-300 focus:ring-emerald-500 cursor-pointer">
                                            </td>
                                            <td class="p-4 leading-relaxed">
                                                <div class="font-bold text-slate-800 text-[11px] uppercase tracking-wide"><?= htmlspecialchars($row_m['nama_masalah']); ?></div>
                                                <div class="text-[9px] text-slate-400 font-medium normal-case mt-0.5">
                                                    <strong>Teks Cust:</strong> <?= isset($row_m['deskripsi_masalah']) ? htmlspecialchars($row_m['deskripsi_masalah']) : '-'; ?>
                                                </div>
                                                <div class="text-[9px] text-blue-500 font-medium normal-case mt-0.5">
                                                    <strong>Penyebab:</strong> <?= isset($row_m['penyebab_masalah']) ? htmlspecialchars($row_m['penyebab_masalah']) : '-'; ?>
                                                </div>
                                                <div class="text-[9px] text-emerald-600 font-medium normal-case mt-0.5">
                                                    <strong>Saran:</strong> <?= isset($row_m['saran_teknisi']) ? htmlspecialchars($row_m['saran_teknisi']) : '-'; ?>
                                                </div>
                                            </td>
                                            <td class="p-4 text-right font-mono font-bold text-slate-900 whitespace-nowrap">Rp <?= number_format($row_m['harga_estimasi'], 0, ',', '.'); ?></td>
                                            <td class="p-4 text-center">
                                                <?php
                                                $masalah_id   = $row_m['id_masalah'];
                                                $masalah_nama = addslashes($row_m['nama_masalah']);
                                                $masalah_desk = isset($row_m['deskripsi_masalah']) ? addslashes($row_m['deskripsi_masalah']) : '';
                                                $masalah_penyebab = isset($row_m['penyebab_masalah']) ? addslashes($row_m['penyebab_masalah']) : '';
                                                $masalah_saran = isset($row_m['saran_teknisi']) ? addslashes($row_m['saran_teknisi']) : '';
                                                $masalah_hrga = $row_m['harga_estimasi'];
                                                ?>
                                                <button type="button"
                                                    onclick="bukaModalMasalah('edit', '<?= $masalah_id; ?>', '<?= $masalah_nama; ?>', '<?= $masalah_desk; ?>', '<?= $masalah_penyebab; ?>', '<?= $masalah_saran; ?>', '<?= $masalah_hrga; ?>')"
                                                    class="text-blue-500 hover:text-blue-700 text-sm transition" title="Edit Rincian">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </form>
                </div>

                <div class="xl:col-span-4 bg-white border border-gray-200/80 rounded-2xl shadow-sm overflow-hidden">
                    <div class="p-4 border-b border-gray-100 bg-slate-50/50 space-y-3">
                        <div class="flex justify-between items-center">
                            <h3 class="text-xs font-extrabold uppercase text-slate-900 tracking-wider">Daftar Seri Laptop</h3>
                            <span class="bg-slate-900 text-white text-[10px] font-bold px-2 py-0.5 rounded-full uppercase"><?= mysqli_num_rows($result_series); ?> Tipe</span>
                        </div>

                        <div class="flex flex-wrap gap-1 pt-1 border-t border-gray-100 select-none">
                            <button type="button" id="btn_filter_semua" onclick="filterSariLaptop('semua', this)" class="btn-tab-brand bg-slate-900 text-white font-bold text-[10px] uppercase px-2.5 py-1.5 rounded-xl transition">Semua</button>
                            <?php foreach ($brands_array as $brnd): ?>
                                <button type="button" onclick="filterSariLaptop('<?= $brnd['id_brand']; ?>', this)" class="btn-tab-brand bg-white hover:bg-slate-50 border border-gray-200 text-slate-600 font-bold text-[10px] uppercase px-2.5 py-1.5 rounded-xl transition"><?= htmlspecialchars($brnd['nama_brand']); ?></button>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <form id="form_series_massal" action="master_laptop.php" method="POST" class="overflow-x-auto">
                        <table class="w-full text-left border-collapse text-xs">
                            <thead>
                                <tr class="bg-slate-100/60 text-slate-400 font-bold uppercase border-b border-gray-100 select-none">
                                    <th class="p-4 w-10 text-center"><input type="checkbox" id="master_check_series" onclick="toggleSemuaSeries(this)" class="w-4 h-4 rounded text-blue-600 border-gray-300 cursor-pointer"></th>
                                    <th class="p-4 w-12 text-center">Foto</th>
                                    <th class="p-4">Nama Series</th>
                                    <th class="p-4 text-center w-12">Edit</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 font-medium text-slate-700">
                                <?php if (mysqli_num_rows($result_series) == 0): ?>
                                    <tr>
                                        <td colspan="4" class="p-8 text-center text-slate-400 font-bold italic">Belum ada seri laptop didaftarkan.</td>
                                    </tr>
                                <?php else: ?>
                                    <?php mysqli_data_seek($result_series, 0);
                                    while ($row_s = mysqli_fetch_assoc($result_series)): ?>
                                        <tr data-brand-id="<?= $row_s['id_brand']; ?>" class="row-series-item hover:bg-slate-50/60 transition">
                                            <td class="p-4 text-center"><input type="checkbox" name="series_id_hapus[]" value="<?= $row_s['id_series']; ?>" onchange="hitungSeriesTerpilih()" class="check_series_child w-4 h-4 rounded text-blue-600 border-gray-300 cursor-pointer"></td>
                                            <td class="p-3 text-center">
                                                <?php $src_foto = (strpos($row_s['foto'], 'images/') !== false) ? '../customer/' . $row_s['foto'] : '../customer/images/series/' . $row_s['foto']; ?>
                                                <img src="<?= $src_foto; ?>?v=<?= time(); ?>" class="w-10 h-8 object-cover rounded border bg-white mx-auto shadow-xs" onerror="this.src='../customer/images/series/default.png'">
                                            </td>
                                            <td class="p-3">
                                                <div class="font-black text-slate-900 uppercase italic text-[11px]"><?= htmlspecialchars($row_s['nama_series']); ?></div>
                                                <div class="mt-1">
                                                    <span class="bg-slate-100 text-slate-600 text-[8px] font-black uppercase px-1 py-0.5 rounded border"><?= htmlspecialchars($row_s['nama_brand']); ?></span>
                                                </div>
                                            </td>
                                            <td class="p-3 text-center">
                                                <button type="button" onclick="bukaModalSeries('edit', '<?= $row_s['id_series']; ?>', '<?= htmlspecialchars($row_s['nama_series'], ENT_QUOTES); ?>', '<?= $row_s['id_brand']; ?>')" class="bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white w-7 h-7 rounded-lg inline-flex items-center justify-center transition"><i class="fas fa-edit text-xs"></i></button>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </form>
                </div>

            </div>
        </div>
    </main>

    <div id="modal_masalah" class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-50 hidden items-center justify-center p-4">
        <div class="bg-white rounded-[1.5rem] max-w-sm w-full p-5 shadow-2xl border border-gray-100 space-y-3 max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-center border-b pb-2">
                <h3 id="judul_modal_masalah" class="text-sm font-extrabold text-slate-900 uppercase">Kelola Master Masalah</h3>
                <button type="button" onclick="tutupModalMasalah()" class="text-slate-400 hover:text-slate-600"><i class="fas fa-times text-base"></i></button>
            </div>
            <form action="master_laptop.php" method="POST" class="space-y-3 text-xs font-semibold">
                <input type="hidden" name="id_masalah" id="input_id_masalah">

                <div class="space-y-1">
                    <label class="text-[10px] font-bold text-slate-400 uppercase block">Nama Masalah Kerusakan</label>
                    <input type="text" name="nama_masalah" id="input_nama_masalah" required class="w-full px-4 py-2.5 bg-slate-50 border border-gray-200 rounded-xl uppercase text-slate-800 font-bold focus:outline-none focus:border-emerald-500">
                </div>

                <div class="space-y-1">
                    <label class="text-[10px] font-bold text-slate-400 uppercase block">Teks Deskripsi (Tampil Halaman Depan Customer)</label>
                    <textarea name="deskripsi_masalah" id="input_deskripsi_masalah" required class="w-full px-4 py-2 bg-slate-50 border border-gray-200 rounded-xl text-slate-800 font-bold h-14 resize-none focus:outline-none focus:border-emerald-500"></textarea>
                </div>

                <div class="space-y-1">
                    <label class="text-[10px] font-bold text-blue-500 uppercase block">Analisa Utama Penyebab Kerusakan</label>
                    <textarea name="penyebab_masalah" id="input_penyebab_masalah" required class="w-full px-4 py-2 bg-slate-50 border border-gray-200 rounded-xl text-slate-800 font-bold h-16 resize-none focus:outline-none focus:border-blue-500"></textarea>
                </div>

                <div class="space-y-1">
                    <label class="text-[10px] font-bold text-emerald-600 uppercase block">Saran Solusi Perbaikan Teknisi</label>
                    <textarea name="saran_teknisi" id="input_saran_teknisi" required class="w-full px-4 py-2 bg-slate-50 border border-gray-200 rounded-xl text-slate-800 font-bold h-16 resize-none focus:outline-none focus:border-emerald-500"></textarea>
                </div>

                <div class="space-y-1">
                    <label class="text-[10px] font-bold text-slate-400 uppercase block">Nominal Harga Estimasi</label>
                    <div class="relative">
                        <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 font-extrabold">Rp</span>
                        <input type="number" name="harga_estimasi" id="input_harga_estimasi" required class="w-full pl-9 pr-4 py-2.5 bg-slate-50 border border-gray-200 rounded-xl font-bold text-slate-800 focus:outline-none focus:border-emerald-500">
                    </div>
                </div>

                <div class="flex justify-end gap-2 pt-2 border-t">
                    <button type="button" onclick="tutupModalMasalah()" class="bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold px-4 py-2 rounded-xl transition uppercase text-[10px]">Batal</button>
                    <button type="submit" name="tambah_masalah" id="btn_submit_masalah" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold px-4 py-2 rounded-xl transition uppercase text-[10px]">Simpan Data</button>
                </div>
            </form>
        </div>
    </div>

    <div id="modal_brand" class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-50 hidden items-center justify-center p-4">
        <div class="bg-white rounded-[1.5rem] max-w-sm w-full p-6 shadow-2xl border border-gray-100 space-y-4">
            <div class="flex justify-between items-center border-b pb-3">
                <h3 id="judul_modal_brand" class="text-sm font-extrabold text-slate-900 uppercase">Kelola Data Brand</h3>
                <button type="button" onclick="tutupModalBrand()" class="text-slate-400 hover:text-slate-600"><i class="fas fa-times text-base"></i></button>
            </div>
            <form action="" method="POST" enctype="multipart/form-data" class="space-y-4 text-xs font-semibold">
                <input type="hidden" name="id_brand" id="input_id_brand">
                <div class="space-y-1">
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Nama Brand Laptop</label>
                    <input type="text" name="nama_brand" id="input_nama_brand" required class="w-full px-4 py-2.5 bg-slate-50 border border-gray-200 rounded-xl uppercase focus:outline-none focus:border-yellow-400 text-slate-800 font-bold">
                </div>
                <div class="space-y-1">
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block" id="label_foto_brand">File Gambar Logo Brand</label>
                    <input type="file" name="logo_file" id="logo_file" accept="image/*" class="w-full px-2 py-1.5 bg-slate-50 border border-gray-200 rounded-xl text-xs focus:outline-none">
                </div>
                <div class="flex justify-end gap-2 pt-3 border-t">
                    <button type="button" onclick="tutupModalBrand()" class="bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold px-4 py-2 rounded-xl transition uppercase text-[10px]">Batal</button>
                    <button type="submit" name="tambah_brand" id="btn_submit_brand" class="bg-black hover:bg-slate-900 text-white font-bold px-4 py-2 rounded-xl transition uppercase text-[10px]">Simpan Brand</button>
                </div>
            </form>
        </div>
    </div>

    <div id="modal_series" class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-50 hidden items-center justify-center p-4">
        <div class="bg-white rounded-[1.5rem] max-w-md w-full p-6 shadow-2xl border border-gray-100 space-y-4">
            <div class="flex justify-between items-center border-b pb-3">
                <h3 id="judul_modal_series" class="text-sm font-extrabold text-slate-900 uppercase">Kelola Tipe Series</h3>
                <button type="button" onclick="tutupModalSeries()" class="text-slate-400 hover:text-slate-600"><i class="fas fa-times text-base"></i></button>
            </div>
            <form action="" method="POST" enctype="multipart/form-data" class="space-y-4 text-xs font-semibold">
                <input type="hidden" name="id_series" id="input_id_series">
                <div class="space-y-1">
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Pilih Induk Merek / Brand</label>
                    <select name="id_brand" id="select_id_brand" required class="w-full px-3 py-2.5 bg-slate-50 border border-gray-200 rounded-xl focus:outline-none text-slate-700 font-bold uppercase cursor-pointer">
                        <option value="">-- PILIH BRAND LAPTOP --</option>
                        <?php foreach ($brands_array as $brand_item): ?>
                            <option value="<?= $brand_item['id_brand']; ?>"><?= strtoupper($brand_item['nama_brand']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="space-y-1">
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Nama Seri / Varian Laptop</label>
                    <input type="text" name="nama_series" id="input_nama_series" required class="w-full px-4 py-2.5 bg-slate-50 border border-gray-200 rounded-xl uppercase focus:outline-none text-slate-800 font-bold">
                </div>
                <div class="space-y-1">
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block" id="label_foto_series">Foto Fisik Unit Laptop</label>
                    <input type="file" name="foto_file" id="foto_file" accept="image/*" class="w-full px-2 py-1.5 bg-slate-50 border border-gray-200 rounded-xl text-xs focus:outline-none">
                </div>
                <div class="flex justify-end gap-2 pt-3 border-t">
                    <button type="button" onclick="tutupModalSeries()" class="bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold px-4 py-2 rounded-xl transition uppercase text-[10px]">Batal</button>
                    <button type="submit" name="tambah_series" id="btn_submit_series" class="bg-[#facc15] hover:bg-[#eab308] text-slate-955 font-black px-4 py-2 rounded-xl transition uppercase text-[10px]">Simpan Series</button>
                </div>
            </form>
        </div>
    </div>

    <div id="modal_hapus_brand_massal" class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-50 hidden items-center justify-center p-4"></div>
    <div id="modal_hapus_masalah_massal" class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-50 hidden items-center justify-center p-4"></div>
    <div id="modal_hapus_series_massal" class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-50 hidden items-center justify-center p-4"></div>

    <script>
        let currentActiveBrandFilter = 'semua';

        function bukaModalMasalah(mode, id = '', nama = '', deskripsi = '', penyebab = '', saran = '', harga = '0') {
            const judul = document.getElementById('judul_modal_masalah');
            const btn = document.getElementById('btn_submit_masalah');
            document.getElementById('input_id_masalah').value = id;
            document.getElementById('input_nama_masalah').value = nama;
            document.getElementById('input_deskripsi_masalah').value = deskripsi;
            document.getElementById('input_penyebab_masalah').value = penyebab;
            document.getElementById('input_saran_teknisi').value = saran;
            document.getElementById('input_harga_estimasi').value = harga;

            if (mode === 'tambah') {
                judul.innerHTML = '<i class="fas fa-plus-circle text-emerald-600 mr-1"></i> Tambah Kerusakan Baru';
                btn.name = 'tambah_masalah';
                btn.innerText = 'Simpan Opsi';
                btn.className = "bg-emerald-600 hover:bg-emerald-700 text-white font-bold px-4 py-2 rounded-xl transition uppercase text-[10px]";
            } else {
                judul.innerHTML = '<i class="fas fa-edit text-blue-500 mr-1"></i> Edit Nama Kerusakan';
                btn.name = 'edit_masalah';
                btn.innerText = 'Perbarui Data';
                btn.className = "bg-blue-500 hover:bg-blue-600 text-white font-bold px-4 py-2 rounded-xl transition uppercase text-[10px]";
            }
            const m = document.getElementById('modal_masalah');
            m.classList.remove('hidden'); m.classList.add('flex');
        }

        function tutupModalMasalah() {
            document.getElementById('modal_masalah').classList.remove('flex');
            document.getElementById('modal_masalah').classList.add('hidden');
        }

        // ==========================================
        // FITUR SCRIPT SELECT ALL & MASALAH MASS HAPUS
        // ==========================================
        function toggleSemuaMasalah(master) {
            const checkboxes = document.querySelectorAll('.check_masalah_child');
            checkboxes.forEach(cb => cb.checked = master.checked);
            hitungMasalahTerpilih();
        }

        function hitungMasalahTerpilih() {
            const checkboxes = document.querySelectorAll('.check_masalah_child');
            let totalTerpilih = 0;
            checkboxes.forEach(cb => { if (cb.checked) totalTerpilih++; });
            
            const btnHapus = document.getElementById('btn_hapus_masalah_massal');
            document.getElementById('count_masalah_terpilih').innerText = totalTerpilih;
            
            if (totalTerpilih > 0) {
                btnHapus.classList.remove('hidden');
                setTimeout(() => {
                    btnHapus.classList.remove('scale-95', 'opacity-0');
                    btnHapus.classList.add('scale-100', 'opacity-100');
                }, 10);
            } else {
                btnHapus.classList.remove('scale-100', 'opacity-100');
                btnHapus.classList.add('scale-95', 'opacity-0');
                setTimeout(() => { btnHapus.classList.add('hidden'); }, 300);
                document.getElementById('master_check_masalah').checked = false;
            }
        }

        function bukaModalHapusMasalahMassal() {
            const total = document.getElementById('count_masalah_terpilih').innerText;
            document.getElementById('text_masalah_total').innerText = total;
            document.getElementById('modal_hapus_masalah_massal').className = "fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-50 flex items-center justify-center p-4";
        }
        function tutupModalHapusMasalahMassal() { document.getElementById('modal_hapus_masalah_massal').className = "hidden"; }

        document.getElementById('modal_hapus_masalah_massal').innerHTML = `<div class="bg-white rounded-2xl max-w-sm w-full p-6 shadow-2xl border border-gray-100 space-y-4 text-center"><div class="w-12 h-12 bg-red-50 text-red-500 rounded-full flex items-center justify-center text-xl mx-auto border border-red-100"><i class="fas fa-exclamation-triangle"></i></div><div class="space-y-1"><h3 class="text-sm font-black uppercase text-slate-900 tracking-wide">Hapus Masalah Terpilih</h3><p class="text-xs font-semibold text-slate-400 leading-relaxed">Apakah Anda yakin menghapus permanen (<span id="text_masalah_total" class="text-red-500 font-bold">0</span> opsi) dari database?</p></div><div class="pt-2 flex gap-2"><button type="button" onclick="tutupModalHapusMasalahMassal()" class="flex-1 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold py-2.5 rounded-xl transition uppercase text-[10px]">Batal</button><button type="submit" form="form_masalah_massal" name="eksekusi_hapus_masalah_massal" class="flex-1 bg-red-500 hover:bg-red-600 text-white font-black py-2.5 rounded-xl transition uppercase text-[10px]">Ya, Hapus</button></div></div>`;

        function filterSariLaptop(brandId, btnTarget) {
            currentActiveBrandFilter = brandId;
            const semuaTab = document.querySelectorAll('.btn-tab-brand');
            semuaTab.forEach(tab => {
                tab.className = "btn-tab-brand bg-white hover:bg-slate-50 border border-gray-200 text-slate-600 font-bold text-[10px] uppercase px-2.5 py-2 rounded-xl transition";
            });
            btnTarget.className = "btn-tab-brand bg-slate-900 text-white font-bold text-[10px] uppercase px-3 py-2 rounded-xl transition shadow-sm";

            const semuaRow = document.querySelectorAll('.row-series-item');
            semuaRow.forEach(row => {
                const targetId = row.getAttribute('data-brand-id');
                if (brandId === 'semua' || targetId === brandId) {
                    row.classList.remove('hidden');
                } else {
                    row.classList.add('hidden');
                    const cb = row.querySelector('.check_series_child');
                    if (cb) cb.checked = false;
                }
            });
            hitungSeriesTerpilih();
        }

        function toggleSemuaBrand(master) {
            const checkboxes = document.querySelectorAll('.check_brand_child');
            checkboxes.forEach(cb => cb.checked = master.checked);
            hitungBrandTerpilih();
        }

        function hitungBrandTerpilih() {
            const checkboxes = document.querySelectorAll('.check_brand_child');
            let totalTerpilih = 0;
            checkboxes.forEach(cb => { if (cb.checked) totalTerpilih++; });
            const btnHapus = document.getElementById('btn_hapus_brand_massal');
            document.getElementById('count_brand_terpilih').innerText = totalTerpilih;
            if (totalTerpilih > 0) {
                btnHapus.classList.remove('hidden');
                setTimeout(() => {
                    btnHapus.classList.remove('scale-95', 'opacity-0');
                    btnHapus.classList.add('scale-100', 'opacity-100');
                }, 10);
            } else {
                btnHapus.classList.remove('scale-100', 'opacity-100');
                btnHapus.classList.add('scale-95', 'opacity-0');
                setTimeout(() => { btnHapus.classList.add('hidden'); }, 300);
                document.getElementById('master_check_brand').checked = false;
            }
        }

        function bukaModalHapusBrand() {
            const total = document.getElementById('count_brand_terpilih').innerText;
            document.getElementById('text_brand_total').innerText = total;
            document.getElementById('modal_hapus_brand_massal').className = "fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-50 flex items-center justify-center p-4";
        }
        function tutupModalHapusBrand() { document.getElementById('modal_hapus_brand_massal').className = "hidden"; }

        function toggleSemuaSeries(master) {
            const checkboxes = document.querySelectorAll('.row-series-item:not(.hidden) .check_series_child');
            checkboxes.forEach(cb => cb.checked = master.checked);
            hitungSeriesTerpilih();
        }

        document.getElementById('modal_hapus_brand_massal').innerHTML = `<div class="bg-white rounded-2xl max-w-sm w-full p-6 shadow-2xl border border-gray-100 space-y-4 text-center"><div class="w-12 h-12 bg-red-50 text-red-500 rounded-full flex items-center justify-center text-xl mx-auto border border-red-100"><i class="fas fa-exclamation-triangle"></i></div><div class="space-y-1"><h3 class="text-sm font-black uppercase text-slate-900 tracking-wide">Hapus Merek Terpilih</h3><p class="text-xs font-semibold text-slate-400 leading-relaxed">Apakah Anda yakin menghapus permanen (<span id="text_brand_total" class="text-red-500 font-bold">0</span> merek) dari database?</p></div><div class="pt-2 flex gap-2"><button type="button" onclick="tutupModalHapusBrand()" class="flex-1 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold py-2.5 rounded-xl transition uppercase text-[10px]">Batal</button><button type="submit" form="form_brand_massal" name="eksekusi_hapus_brand_massal" class="flex-1 bg-red-500 hover:bg-red-600 text-white font-black py-2.5 rounded-xl transition uppercase text-[10px]">Ya, Hapus</button></div></div>`;

        function hitungSeriesTerpilih() {
            const checkboxes = document.querySelectorAll('.check_series_child');
            let totalTerpilih = 0;
            checkboxes.forEach(cb => { if (cb.checked) totalTerpilih++; });
            const btnHapus = document.getElementById('btn_hapus_series_massal');
            document.getElementById('count_series_terpilih').innerText = totalTerpilih;
            if (totalTerpilih > 0) {
                btnHapus.classList.remove('hidden');
                setTimeout(() => {
                    btnHapus.classList.remove('scale-95', 'opacity-0');
                    btnHapus.className = btnHapus.className.replace('hidden', 'inline-flex');
                    btnHapus.classList.add('scale-100', 'opacity-100');
                }, 10);
            } else {
                btnHapus.classList.remove('scale-100', 'opacity-100');
                btnHapus.classList.add('scale-95', 'opacity-0');
                setTimeout(() => { btnHapus.classList.add('hidden'); }, 300);
                document.getElementById('master_check_series').checked = false;
            }
        }

        function bukaModalHapusSeries() {
            const total = document.getElementById('count_series_terpilih').innerText;
            document.getElementById('text_series_total').innerText = total;
            document.getElementById('modal_hapus_series_massal').className = "fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-50 flex items-center justify-center p-4";
        }
        function tutupModalHapusSeries() { document.getElementById('modal_hapus_series_massal').className = "hidden"; }

        document.getElementById('modal_hapus_series_massal').innerHTML = `<div class="bg-white rounded-2xl max-w-sm w-full p-6 shadow-2xl border border-gray-100 space-y-4 text-center"><div class="w-12 h-12 bg-red-50 text-red-500 rounded-full flex items-center justify-center text-xl mx-auto border border-red-100"><i class="fas fa-exclamation-triangle"></i></div><div class="space-y-1"><h3 class="text-sm font-black uppercase text-slate-900 tracking-wide">Hapus Varian Tipe Terpilih</h3><p class="text-xs font-semibold text-slate-400 leading-relaxed">Apakah Anda yakin menghapus permanen (<span id="text_series_total" class="text-red-500 font-bold">0</span> tipe) varian laptop yang dicentang?</p></div><div class="pt-2 flex gap-2"><button type="button" onclick="tutupModalHapusSeries()" class="flex-1 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold py-2.5 rounded-xl transition uppercase text-[10px]">Batal</button><button type="submit" form="form_series_massal" name="eksekusi_hapus_series_massal" class="flex-1 bg-red-500 hover:bg-red-600 text-white font-black py-2.5 rounded-xl transition uppercase text-[10px]">Ya, Hapus</button></div></div>`;

        function bukaModalBrand(mode, id = '', nama = '') {
            const judul = document.getElementById('judul_modal_brand');
            const btn = document.getElementById('btn_submit_brand');
            const fileInput = document.getElementById('logo_file');
            const labelFoto = document.getElementById('label_foto_brand');
            document.getElementById('input_id_brand').value = id;
            document.getElementById('input_nama_brand').value = nama;

            if (mode === 'tambah') {
                judul.innerHTML = '<i class="fas fa-plus-circle text-slate-800 mr-1"></i> Tambah Brand Baru';
                btn.name = 'tambah_brand'; btn.innerText = 'Simpan Brand';
                labelFoto.innerText = "File Gambar Logo Brand (Wajib)";
                fileInput.setAttribute('required', 'required');
                btn.className = "bg-black hover:bg-slate-900 text-white font-bold px-4 py-2 rounded-xl transition uppercase tracking-wider text-[10px]";
            } else {
                judul.innerHTML = '<i class="fas fa-edit text-blue-500 mr-1"></i> Edit Data Brand';
                btn.name = 'edit_brand'; btn.innerText = 'Perbarui Brand';
                labelFoto.innerText = "Ganti Logo Brand (Kosongkan jika tidak diubah)";
                fileInput.removeAttribute('required');
                btn.className = "bg-blue-500 hover:bg-blue-600 text-white font-bold px-4 py-2 rounded-xl transition uppercase tracking-wider text-[10px]";
            }
            const m = document.getElementById('modal_brand');
            m.classList.remove('hidden'); m.classList.add('flex');
        }

        function tutupModalBrand() {
            const m = document.getElementById('modal_brand');
            m.classList.remove('flex'); m.classList.add('hidden');
        }

        function bukaModalSeries(mode, id = '', nama = '', id_brand = '') {
            const judul = document.getElementById('judul_modal_series');
            const btn = document.getElementById('btn_submit_series');
            const fileInput = document.getElementById('foto_file');
            const labelFoto = document.getElementById('label_foto_series');

            document.getElementById('input_id_series').value = id;
            document.getElementById('input_nama_series').value = nama;
            document.getElementById('select_id_brand').value = id_brand;

            if (mode === 'tambah') {
                judul.innerHTML = '<i class="fas fa-plus-circle text-yellow-500 mr-1"></i> Tambah Series Baru';
                btn.name = 'tambah_series'; btn.innerText = 'Simpan Series';
                labelFoto.innerText = "Foto Fisik Unit Laptop (Wajib)";
                fileInput.setAttribute('required', 'required');
                btn.className = "bg-[#facc15] hover:bg-[#eab308] text-slate-955 font-black px-4 py-2 rounded-xl transition uppercase tracking-wider text-[10px]";
            } else {
                judul.innerHTML = '<i class="fas fa-edit text-blue-500 mr-1"></i> Edit Konfigurasi Series';
                btn.name = 'edit_series'; btn.innerText = 'Perbarui Series';
                labelFoto.innerText = "Ganti Foto Unit Laptop (Kosongkan jika tidak diubah)";
                fileInput.removeAttribute('required');
                btn.className = "bg-blue-500 hover:bg-blue-600 text-white font-bold px-4 py-2 rounded-xl transition uppercase tracking-wider text-[10px]";
            }
            const m = document.getElementById('modal_series');
            m.classList.remove('hidden'); m.classList.add('flex');
        }

        function tutupModalSeries() {
            const m = document.getElementById('modal_series');
            m.classList.remove('flex'); m.classList.add('hidden');
        }
    </script>
</body>

</html>