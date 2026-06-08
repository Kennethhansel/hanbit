<?php
// =========================================================================
// SISI BACKEND: EKSEKUSI PENYIMPANAN DATA UTAMA & OTOMATISASI MEMBER CRM
// =========================================================================
require_once '../admin/koneksi.php';

$nama_pelanggan      = 'Kenneth Hansel';
$unit_laptop         = 'Laptop Varian';
$tanggal_booking     = date('Y-m-d');
$paket_tipe          = 'basic';
$segmen_laptop       = 'kantoran';
$total_harga_final   = 0;
$no_whatsapp         = '';
$alamat_pelanggan    = '';
$email_pelanggan     = ''; // Inisialisasi awal variabel email penampung data CRM

// Generate Invoice Unik Hanbit Labs Formal (Contoh: INV-20260605-ABCD)
$nomor_invoice = "INV-" . date('Ymd') . "-" . strtoupper(substr(md5(time()), 0, 4));

// A. KONDISI 1: JIKA DATANG DARI BUAT_RESERVASI_PAKET.PHP
if (isset($_POST['proses_simpan_paket'])) {
    $paket_tipe        = mysqli_real_escape_string($koneksi, trim($_POST['paket_tipe']));
    $segmen_laptop     = mysqli_real_escape_string($koneksi, trim($_POST['segmen_laptop']));
    $total_harga_final = intval($_POST['total_harga_final']);
    $nama_pelanggan    = mysqli_real_escape_string($koneksi, trim($_POST['nama_pelanggan']));
    $no_whatsapp       = mysqli_real_escape_string($koneksi, trim($_POST['whatsapp']));
    $email_pelanggan   = mysqli_real_escape_string($koneksi, trim($_POST['email'])); // Menangkap data email form paket
    $unit_laptop       = mysqli_real_escape_string($koneksi, trim($_POST['laptop_detail']));
    $alamat_pelanggan  = mysqli_real_escape_string($koneksi, trim($_POST['alamat_lengkap']));
    $tanggal_booking   = mysqli_real_escape_string($koneksi, trim($_POST['tanggal_booking']));
} 
// B. KONDISI 2: JIKA DATANG DARI CEK_ESTIMASI_DETAIL.PHP
elseif (isset($_POST['proses_simpan_estimasi'])) {
    $brand_id            = $_POST['brand_id'];
    $series_id           = $_POST['series_id'];
    $masalah_custom      = mysqli_real_escape_string($koneksi, trim($_POST['masalah_custom']));
    $total_harga_final   = intval($_POST['total_harga_final']);
    $nama_pelanggan      = mysqli_real_escape_string($koneksi, trim($_POST['nama_lengkap']));
    $no_whatsapp         = mysqli_real_escape_string($koneksi, trim($_POST['whatsapp']));
    $email_pelanggan     = mysqli_real_escape_string($koneksi, trim($_POST['email'])); // Menangkap data email form kustom estimasi
    $alamat_pelanggan    = mysqli_real_escape_string($koneksi, trim($_POST['alamat_lengkap']));
    $tanggal_booking     = mysqli_real_escape_string($koneksi, trim($_POST['tanggal_menyerahkan']));
    $paket_tipe          = 'custom_estimasi';
    $segmen_laptop       = $masalah_custom; // Simpan deskripsi keluhan kustom di kolom ini

    // Tarik nama brand & series dari DB riil agar teks invoice rapi bagus
    $q_b = mysqli_query($koneksi, "SELECT nama_brand FROM laptop_brands WHERE id_brand = '$brand_id' LIMIT 1");
    $d_b = mysqli_fetch_assoc($q_b); $merek = ucwords(strtolower($d_b['nama_brand'] ?? 'Laptop'));
    
    $q_s = mysqli_query($koneksi, "SELECT nama_series FROM laptop_series WHERE id_series = '$series_id' LIMIT 1");
    $d_s = mysqli_fetch_assoc($q_s); $seri = ucwords(strtolower($d_s['nama_series'] ?? 'Series'));
    $unit_laptop = $merek . " " . $seri;
} else {
    // Paksa tendang balik ke home jika tidak ada data terkirim resmi
    header("Location: index.php");
    exit();
}

// -------------------------------------------------------------------------
// 🔥 SISTEM OTOMATISASI DATA PELANGGAN (CRM ENGINE ANTI-DUPLIKASI)
// -------------------------------------------------------------------------
// Bersihkan format nomor HP agar seragam memakai kode negara 62
$whatsapp_clean = preg_replace('/[^0-9]/', '', $no_whatsapp);
if (strpos($whatsapp_clean, '0') === 0) {
    $whatsapp_clean = '62' . substr($whatsapp_clean, 1);
}

// Cek apakah pelanggan dengan nomor WA ini sudah pernah terdaftar di database
$cek_member = mysqli_query($koneksi, "SELECT id_customer FROM customers WHERE no_hp = '$whatsapp_clean' LIMIT 1");
$data_member = mysqli_fetch_assoc($cek_member);

if ($data_member) {
    // Jika sudah ada (Pelanggan Lama), pakai id_customer yang sudah ada agar tidak ganda
    $id_customer_final = $data_member['id_customer'];
    // Update data email terbaru jika pelanggan melakukan update atau repeat order dengan email baru
    mysqli_query($koneksi, "UPDATE customers SET email = '$email_pelanggan' WHERE id_customer = $id_customer_final");
} else {
    // Jika belum ada (Pelanggan Baru), OTOMATIS daftarkan identITAS baru ke tabel customers beserta emailnya
    $query_register = "INSERT INTO customers (nama_customer, no_hp, email) VALUES ('$nama_pelanggan', '$whatsapp_clean', '$email_pelanggan')";
    mysqli_query($koneksi, $query_register);
    
    // Tangkap ID baru yang otomatis dibuat oleh MySQL
    $id_customer_final = mysqli_insert_id($koneksi);
}

// EKSEKUSI PENYIMPANAN DATA RESERVASI KE TABEL RESERVATIONS UTAMA
$query_insert = "INSERT INTO reservations (
                    no_invoice, id_customer, nama_pelanggan, no_whatsapp, laptop_detail, 
                    alamat_pelanggan, paket_tipe, segmen_laptop, total_harga, 
                    tanggal_booking, status_order
                 ) VALUES (
                    '$nomor_invoice', '$id_customer_final', '$nama_pelanggan', '$whatsapp_clean', '$unit_laptop', 
                    '$alamat_pelanggan', '$paket_tipe', '$segmen_laptop', $total_harga_final, 
                    '$tanggal_booking', 'PENDING'
                 )";

if (!mysqli_query($koneksi, $query_insert)) {
    die("Gagal menyimpan reservasi Hanbit Labs: " . mysqli_error($koneksi));
}

// Format Penanggalan Bahasa Indonesia
$hari_list = ["Sunday" => "Minggu", "Monday" => "Senin", "Tuesday" => "Selasa", "Wednesday" => "Rabu", "Thursday" => "Kamis", "Friday" => "Jumat", "Saturday" => "Sabtu"];
$bulan_list = ["01" => "Januari", "02" => "Februari", "03" => "Maret", "04" => "April", "05" => "Mei", "06" => "Juni", "07" => "Juli", "08" => "Agustus", "09" => "September", "10" => "Oktober", "11" => "November", "12" => "Desember"];
$timestamp = strtotime($tanggal_booking);
$tanggal_tampilan = $hari_list[date('l', $timestamp)] . ", " . date('j', $timestamp) . " " . $bulan_list[date('m', $timestamp)] . " " . date('Y', $timestamp) . "; Pukul 18.00 WIB";
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hanbit - Booking Berhasil</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght=400;500;600;700;800&display=swap');
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-[#f8fafc] text-slate-900 antialiased min-h-screen flex flex-col justify-center items-center py-10 px-4">

    <div class="text-center space-y-2 mb-6 max-w-md">
        <div class="w-14 h-14 bg-emerald-100 rounded-full flex items-center justify-center mx-auto text-emerald-500 shadow-sm">
            <i class="fas fa-check text-xl"></i>
        </div>
        <h1 class="text-2xl font-black text-slate-900 tracking-tight uppercase">BOOKING BERHASIL!</h1>
        <p class="text-xs text-slate-400 font-semibold leading-relaxed">Selamat data Anda sudah berhasil diregistrasi. Simpan struk invoice awal ini sebagai tanda bukti penyerahan unit.</p>
    </div>

    <div id="invoice_card" class="bg-white rounded-[2rem] shadow-xl border border-gray-100 max-w-xl w-full overflow-hidden flex flex-col justify-between">
        
        <div class="bg-[#ffd54f] py-7 px-6 text-center space-y-0.5">
            <p class="text-[9px] font-black text-slate-700 uppercase tracking-widest">NOMOR INVOICE OPERASIONAL</p>
            <h2 class="text-2xl md:text-3xl font-black text-slate-950 tracking-tight"><?= $nomor_invoice; ?></h2>
        </div>

        <div class="p-6 md:p-8 space-y-5 bg-white text-xs">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 border-b border-gray-100 pb-5">
                <div>
                    <p class="text-[9px] font-bold text-slate-400 uppercase tracking-wider">Nama Pelanggan</p>
                    <p class="text-sm font-extrabold text-slate-800 uppercase"><?= htmlspecialchars($nama_pelanggan); ?></p>
                </div>
                <div>
                    <p class="text-[9px] font-bold text-slate-400 uppercase tracking-wider">Unit Laptop</p>
                    <p class="text-sm font-extrabold text-slate-800 uppercase"><?= htmlspecialchars($unit_laptop); ?></p>
                </div>
                <div class="sm:col-span-2">
                    <p class="text-[9px] font-bold text-slate-400 uppercase tracking-wider">Jenis Klasifikasi Layanan</p>
                    <p class="text-xs font-black text-slate-700 uppercase italic">
                        <?= ($paket_tipe == 'custom_estimasi') ? '🛠️ Perbaikan Kasus Kerusakan Khusus' : '✨ Paket Perawatan Berkala Maintenance (' . strtoupper($paket_tipe) . ')'; ?>
                    </p>
                </div>
                <div class="sm:col-span-2">
                    <p class="text-[9px] font-bold text-slate-400 uppercase tracking-wider">Batas Tanggal Penyerahan</p>
                    <p class="text-xs font-extrabold text-amber-600"><?= $tanggal_tampilan; ?></p>
                </div>
            </div>

            <div class="bg-slate-50 border-l-4 border-yellow-400 rounded-r-xl p-4 space-y-1 text-[11px] text-slate-500 font-semibold">
                <h4 class="font-black text-slate-800 uppercase text-xs mb-1">📌 Prosedur Penyerahan Unit:</h4>
                <p>1. Datang langsung ke Toko Hanbit Labs (Jl. Cihanjuang No.3 Kp. Centeng, Bandung).</p>
                <p>2. Tunjukkan **Nomor Invoice** tanda terima awal ini pada teknisi registrasi toko.</p>
                <p>3. Teknisi akan langsung memproses klaim antrean fisik laptop Anda.</p>
            </div>

            <div class="bg-amber-50/50 border-l-4 border-amber-500 rounded-r-xl p-4 space-y-2">
                <h4 class="text-xs font-black text-slate-800 flex items-center gap-2 uppercase tracking-wide">
                    <i class="fas fa-wallet text-amber-600"></i> Informasi Ketentuan Pembayaran:
                </h4>
                <ul class="list-disc list-inside text-[11px] text-slate-500 font-semibold space-y-1.5 leading-relaxed">
                    <li>Jika ada penggantian sparepart/komponen dengan nilai <strong class="text-slate-800">di atas Rp 300.000</strong>, pelanggan diwajibkan membayar <strong class="text-amber-600">DP sebesar 50%</strong> terlebih dahulu di toko.</li>
                    <li>Pembayaran penuh dilakukan setelah proses perbaikan selesai dikerjakan atau saat pengambilan unit laptop.</li>
                    <li>Segala bentuk transaksi dilakukan secara <strong class="text-slate-800">Offline langsung di Toko Hanbit</strong> (Bisa Cash tunai atau Transfer rekening bank resmi toko). Kami tidak melayani sistem pembayaran online di website.</li>
                </ul>
            </div>
        </div>

        <div class="bg-[#1e293b] p-5 text-white flex justify-between items-center">
            <div>
                <p class="text-[9px] font-bold text-slate-400 uppercase tracking-wider">
                    <?= ($paket_tipe == 'custom_estimasi') ? 'Total Estimasi Biaya' : 'Total Nominal Biaya'; ?>
                </p>
                <h2 class="text-xl md:text-2xl font-black text-[#facc15]">
                    <?= ($paket_tipe == 'custom_estimasi' && $total_harga_final == 0) ? "Cek Fisik Toko" : "Rp " . number_format($total_harga_final, 0, ',', '.'); ?>
                </h2>
            </div>
            <button type="button" onclick="unduhStrukPng()" class="bg-slate-700 hover:bg-slate-600 text-white font-extrabold text-[11px] px-4 py-2.5 rounded-xl flex items-center gap-1.5 transition shadow-sm select-none">
                <i class="fas fa-download"></i> Simpan Gambar
            </button>
        </div>
    </div>

    <div class="mt-8 flex flex-col sm:flex-row items-center gap-4">
        <a href="status_tracking.php?invoice=<?= $nomor_invoice; ?>" class="bg-yellow-400 hover:bg-yellow-500 text-slate-950 font-black text-xs uppercase px-8 py-4 rounded-2xl tracking-wider transition shadow-lg shadow-yellow-400/10 flex items-center gap-2 select-none">
            Pantau Progres Laptop Kamu <i class="fas fa-arrow-right"></i>
        </a>
        <button type="button" onclick="cekKeamananBeranda()" class="text-slate-400 hover:text-slate-600 font-bold text-xs uppercase tracking-wide select-none">
            Kembali ke Beranda
        </button>
    </div>

    <div id="modal_peringatan" class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-50 hidden items-center justify-center px-4">
        <div class="bg-white rounded-[1.5rem] max-w-sm w-full p-6 shadow-xl space-y-4 border text-center">
            <div class="w-12 h-12 bg-amber-100 rounded-full flex items-center justify-center mx-auto text-amber-500"><i class="fas fa-exclamation-triangle text-xl"></i></div>
            <div class="space-y-1">
                <h3 class="text-base font-extrabold text-slate-900">Nota Belum Disimpan!</h3>
                <p class="text-xs text-slate-400 font-medium leading-relaxed">Struk invoice bukti registrasi ini hanya tampil sekali saja. Pastikan Anda mengklik tombol **Simpan Gambar** terlebih dahulu agar kode aman terunduh.</p>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="button" onclick="tutupPeringatan()" class="w-1/2 bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-bold py-3 rounded-xl transition">Amankan</button>
                <a href="index.php" class="w-1/2 bg-yellow-400 hover:bg-yellow-500 text-slate-950 text-xs font-black py-3 rounded-xl transition block text-center shadow-sm">Sudah Simpan</a>
            </div>
        </div>
    </div>

    <script>
        let notaTelahDiunduh = false;

        function unduhStrukPng() {
            html2canvas(document.getElementById('invoice_card'), { scale: 2, useCORS: true }).then(canvas => {
                const link = document.createElement('a');
                link.href = canvas.toDataURL('image/png');
                link.download = 'Nota_Awal_Hanbit_<?= $nomor_invoice; ?>.png';
                link.click();
                notaTelahDiunduh = true;
                alert("✅ Nota pendaftaran sukses diunduh! Silakan klik tombol pantau live progres untuk melihat detail.");
            });
        }

        function cekKeamananBeranda() {
            if (!notaTelahDiunduh) {
                document.getElementById('modal_peringatan').classList.remove('hidden');
                document.getElementById('modal_peringatan').classList.add('flex');
            } else {
                window.location.href = 'index.php';
            }
        }
        function tutupPeringatan() {
            document.getElementById('modal_peringatan').classList.remove('flex');
            document.getElementById('modal_peringatan').classList.add('hidden');
        }
    </script>
</body>
</html>