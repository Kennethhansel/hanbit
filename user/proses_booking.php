<?php
// 1. BUAT KONEKSI KE DATABASE DULU
$host = "localhost";
$user = "root";
$pass = "";
$db   = "db_hanbit";
$koneksi = mysqli_connect($host, $user, $pass, $db);

if (!$koneksi) {
    die("Koneksi database Hanbit gagal: " . mysqli_connect_error());
}

// 2. TANGKAP SEMUA PARAMETER DARI HALAMAN SEBELUMNYA
$nama_pelanggan      = isset($_POST['nama_lengkap']) ? mysqli_real_escape_string($koneksi, $_POST['nama_lengkap']) : 'Kenneth Hansel';
$brand_id            = isset($_POST['brand_id']) ? $_POST['brand_id'] : '1';
$series_id           = isset($_POST['series_id']) ? mysqli_real_escape_string($koneksi, $_POST['series_id']) : '';
$id_masalah_raw      = isset($_POST['id_masalah']) ? $_POST['id_masalah'] : '';
$masalah_custom      = isset($_POST['masalah_custom']) ? $_POST['masalah_custom'] : '';
$tanggal_menyerahkan = isset($_POST['tanggal_menyerahkan']) ? $_POST['tanggal_menyerahkan'] : date('Y-m-d');

// AMBIL NAMA MEREK DARI DATABASE
$query_brand = "SELECT nama_brand FROM laptop_brands WHERE ID_Brand = '$brand_id' LIMIT 1";
$res_brand = mysqli_query($koneksi, $query_brand);
$data_brand = mysqli_fetch_assoc($res_brand);
$merek_laptop = ucwords(strtolower($data_brand['nama_brand'] ?? 'Asus'));

// AMBIL NAMA SERIES DARI DATABASE BERDASARKAN ID
$nama_series = '';
if (!empty($series_id)) {
    $query_series = "SELECT Nama_Series FROM laptop_series WHERE ID_Series = '$series_id' LIMIT 1";
    $res_series = mysqli_query($koneksi, $query_series);
    if ($res_series && mysqli_num_rows($res_series) > 0) {
        $data_series = mysqli_fetch_assoc($res_series);
        $nama_series = ucwords(strtolower($data_series['Nama_Series']));
    }
}

// GABUNGKAN MEREK DAN SERIES JADI SATU KALIMAT UTUH
if (empty($nama_series)) {
    // Matriks Cadangan Cadangan Pintu Darurat Untuk Semua Merek Laptop
    $master_static_all_brands = [
        '1' => ['1' => 'Republic Of Gamers', '2' => 'Zenbook', '3' => 'Vivobook', '4' => 'Tuf Gaming', '5' => 'Zenbook Pro', '6' => 'Transformer', '7' => 'Expert Book', '8' => 'Proart Series'],
        '2' => ['1' => 'Thinkpad', '2' => 'Ideapad', '3' => 'Yoga Series', '4' => 'Legion Gaming', '5' => 'Loq Series', '6' => 'Thinkbook', '7' => 'Flex Series', '8' => 'Slim Series'],
        '3' => ['1' => 'Pavilion', '2' => 'Envy Series', '3' => 'Spectre', '4' => 'Omen Gaming', '5' => 'Victus By Hp', '6' => 'Hp Elitebook', '7' => 'Probook', '8' => 'Hp Essential'],
        '4' => ['1' => 'Inspiron', '2' => 'Xps Series', '3' => 'Vostro', '4' => 'Latitude', '5' => 'Alienware', '6' => 'G Series Gaming', '7' => 'Precision', '8' => 'Chromebook'],
        '5' => ['1' => 'Aspire', '2' => 'Swift Series', '3' => 'Spin Series', '4' => 'Nitro Gaming', '5' => 'Predator Helios', '6' => 'Travelmate', '7' => 'Enduro', '8' => 'One Series'],
        '6' => ['1' => 'Soulmate', '2' => 'Workplus', '3' => 'Workpro', '4' => 'Pixelwar Gaming', '5' => '360 Stylus', '6' => 'Nasa Series', '7' => 'Startgo', '8' => 'Avio Series'],
        '7' => ['1' => 'Mybook', '2' => 'Slimbook', '3' => 'Pongo Gaming', '4' => 'Chronos', '5' => 'Saga Series', '6' => 'Hype Series', '7' => 'Windroid', '8' => 'Neon Series'],
        '8' => ['1' => 'Katana Gaming', '2' => 'Cyborg Series', '3' => 'Stealth', '4' => 'Titan Gt', '5' => 'Modern Series', '6' => 'Prestige', '7' => 'Creator', '8' => 'Raider Gaming']
    ];

    // Ambil baris kelompok mereknya, lalu ambil nama varian seriesnya
    $nama_series = $master_static_all_brands[$brand_id][$series_id] ?? 'Varian Series';
}

// Variabel final gabungan huruf rapi kapital yang dicetak di invoice
$unit_laptop = $merek_laptop . " " . ucwords(strtolower($nama_series));

// Generate Kode Order Acak Formal (Contoh: #HB2026-0042)
$tahun_sekarang = date('Y');
$nomor_acak     = str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
$kode_order     = "#HB" . $tahun_sekarang . "-" . $nomor_acak;

// Hitung Estimasi Harga Kasar dari Parameter URL untuk Ditampilkan Kembali
$master_harga = ['1' => 500000, '2' => 450000, '3' => 200000, '4' => 150000, '5' => 350000, '6' => 250000, '7' => 150000];
$array_id = explode(',', $id_masalah_raw);
$total_estimasi = 0;
$ada_custom = false;

foreach ($array_id as $id) {
    if ($id == '8') { $ada_custom = true; }
    if (isset($master_harga[$id])) { $total_estimasi += $master_harga[$id]; }
}

// Format Tanggal Indonesia
$hari_list = ["Sunday" => "Minggu", "Monday" => "Senin", "Tuesday" => "Selasa", "Wednesday" => "Rabu", "Thursday" => "Kamis", "Friday" => "Jumat", "Saturday" => "Sabtu"];
$bulan_list = ["01" => "Januari", "02" => "Februari", "03" => "Maret", "04" => "April", "05" => "Mei", "06" => "Juni", "07" => "Juli", "08" => "Agustus", "09" => "September", "10" => "Oktober", "11" => "November", "12" => "Desember"];

$timestamp = strtotime($tanggal_menyerahkan);
$nama_hari = $hari_list[date('l', $timestamp)];
$tgl       = date('j', $timestamp);
$bln       = $bulan_list[date('m', $timestamp)];
$thn       = date('Y', $timestamp);
$tanggal_tampilan = "$nama_hari, $tgl $bln $thn; Pukul 18.00 WIB";
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hanbit - Booking Berhasil</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- LIBRARY HTML2CANVAS: Mengubah elemen HTML Card menjadi File Gambar PNG saat di-download -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght=400;500;600;700;800&display=swap');
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>

<body class="bg-[#f8fafc] text-slate-900 antialiased min-h-screen flex flex-col justify-center items-center py-10 px-4">

    <!-- HEADER STRUKTURAL UTAMA -->
    <div class="text-center space-y-2 mb-6 max-w-md">
        <div class="w-16 h-16 bg-emerald-100 rounded-full flex items-center justify-center mx-auto text-emerald-500 shadow-sm">
            <i class="fas fa-check text-2xl"></i>
        </div>
        <h1 class="text-2xl md:text-3xl font-black text-slate-900 tracking-tight uppercase">BOOKING BERHASIL!</h1>
        <p class="text-xs md:text-sm text-slate-400 font-semibold leading-relaxed">Silakan simpan atau screenshot halaman ini sebagai bukti reservasi awal Anda.</p>
    </div>

    <!-- AREA KARTU INVOICE YANG AKAN DI-CAPTURE JADI GAMBAR -->
    <div id="invoice_card" class="bg-white rounded-[2rem] shadow-xl border border-gray-100 max-w-2xl w-full overflow-hidden flex flex-col justify-between">
        
        <!-- HEADER KUNING: TEMPAT KODE ORDER -->
        <div class="bg-[#ffd54f] py-8 px-6 text-center space-y-1">
            <p class="text-[10px] font-black text-slate-700 uppercase tracking-widest">KODE ORDER ANDA</p>
            <h2 class="text-3xl md:text-4xl font-black text-slate-950 tracking-tight"><?= $kode_order; ?></h2>
        </div>

        <!-- ISI KONTEN DATA RESERVASI -->
        <div class="p-6 md:p-8 space-y-6 bg-white">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 border-b border-gray-100 pb-6">
                <div class="space-y-0.5">
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Nama Pelanggan</p>
                    <p class="text-base font-extrabold text-slate-800"><?= htmlspecialchars($nama_pelanggan); ?></p>
                </div>
                <div class="space-y-0.5">
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Unit Laptop</p>
                    <p class="text-base font-extrabold text-slate-800"><?= htmlspecialchars($unit_laptop); ?></p>
                </div>
                <div class="space-y-0.5 sm:col-span-2">
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Tanggal Menyerahkan</p>
                    <p class="text-sm font-extrabold text-amber-600"><?= $tanggal_tampilan; ?></p>
                    <p class="text-[10px] text-slate-400 font-medium font-semibold mt-0.5">* Wajib Menyerahkan Sebelum Tanggal Tertera.</p>
                </div>
            </div>

            <!-- BOX INFORMASI 1: LANGKAH SELANJUTNYA -->
            <div class="bg-slate-50/80 border-l-4 border-yellow-400 rounded-r-xl p-4 space-y-2">
                <h4 class="text-xs font-black text-slate-800 flex items-center gap-2 uppercase tracking-wide">
                    <i class="fas fa-info-circle text-yellow-500"></i> Langkah Selanjutnya:
                </h4>
                <ol class="list-decimal list-inside text-[11px] text-slate-500 font-semibold space-y-1.5 leading-relaxed">
                    <li>Datang ke Workshop Hanbit sesuai jadwal (Jl. Cihanjuang No.3 Kp. Centeng).</li>
                    <li>Tunjukkan <strong class="text-slate-800">"Kode Order"</strong> di atas kepada teknisi kami di meja registrasi.</li>
                    <li>Teknisi akan melakukan pengecekan fisik unit sebelum pengerjaan dimulai.</li>
                </ol>
            </div>

            <!-- BOX INFORMASI 2: KEBIJAKAN PEMBAYARAN DAN SPAREPART -->
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

        <!-- FOOTER CARD KOTAK HITAM TOTAL HARGA & DOWNLOAD -->
        <div class="bg-[#1e293b] p-6 text-white flex flex-row justify-between items-center gap-4">
            <div>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Total Estimasi Harga</p>
                <h2 class="text-2xl md:text-3xl font-black text-[#facc15] mt-0.5">
                    <?= $ada_custom && $total_estimasi == 0 ? "Berdasarkan Cek Fisik" : "Rp " . number_format($total_estimasi, 0, ',', '.'); ?>
                </h2>
            </div>
            
            <!-- Tombol Simpan Gambar Nyata -->
            <button type="button" onclick="eksekusiUnduhGambar()" class="bg-slate-700/80 hover:bg-slate-700 text-slate-200 text-xs font-extrabold px-4 py-3 rounded-xl flex items-center gap-2 transition shadow-sm select-none shrink-0">
                <i class="fas fa-download"></i> Simpan Gambar
            </button>
        </div>
    </div>

    <!-- NAVIGATION MENU KEMBALI KE BERANDA (DILENGKAPI SCRIPT PENGUNCI ALERT) -->
    <div class="mt-6">
        <button type="button" onclick="keamananBeranda()" class="text-amber-600 hover:text-amber-700 font-extrabold text-sm flex items-center gap-2 transition select-none bg-transparent border-none cursor-pointer">
            <i class="fas fa-arrow-left"></i> Kembali ke Beranda
        </button>
    </div>


    <!-- POP-UP MODAL VALIDASI PENGINGAT (MUNCUL JIKA BELUM DOWNLOAD GAMBAR) -->
    <div id="modal_peringatan" class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-50 hidden flex items-center justify-center px-4">
        <div class="bg-white rounded-[1.5rem] max-w-sm w-full p-6 shadow-xl space-y-4 border border-gray-100 text-center">
            <div class="w-12 h-12 bg-amber-100 rounded-full flex items-center justify-center mx-auto text-amber-500">
                <i class="fas fa-exclamation-triangle text-xl"></i>
            </div>
            <div class="space-y-1">
                <h3 class="text-base font-extrabold text-slate-900">Peringatan Penting!</h3>
                <p class="text-xs text-slate-400 font-medium leading-relaxed">Halaman invoice ini hanya muncul **SATU KALI**. Apakah Anda benar-benar sudah menyimpan/screenshoot Kode Order Anda?</p>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="button" onclick="tutupModalPeringatan()" class="w-1/2 bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-bold py-3 rounded-xl transition">Belum, Simpan</button>
                <a href="index.php" class="w-1/2 bg-[#ffd54f] hover:bg-[#ffca28] text-slate-900 text-xs font-black py-3 rounded-xl transition block text-center shadow-sm">Ya, Sudah</a>
            </div>
        </div>
    </div>


    <!-- JAVASCRIPT KONTROL LOGIKA DOWNLOAD & PENGUNCI INTERAKTIF -->
    <script>
        // Flag mendeteksi status klik tombol simpan gambar
        let sudahUnduhGambar = false;

        function eksekusiUnduhGambar() {
            const elemenTarget = document.getElementById('invoice_card');
            
            // Konfigurasi html2canvas agar kualitas gambar jernih tinggi
            html2canvas(elemenTarget, {
                scale: 2, // Menyusun resolusi agar teks tidak pecah
                logging: false,
                useCORS: true
            }).then(canvas => {
                const urlGambar = canvas.toDataURL('image/png');
                const linkDownload = document.createElement('a');
                linkDownload.href = urlGambar;
                
                // Judul nama file gambar otomatis sesuai kode order kamu
                linkDownload.download = 'Invoice_Hanbit_<?= str_replace("#", "", $kode_order); ?>.png';
                document.body.appendChild(linkDownload);
                linkDownload.click();
                document.body.removeChild(linkDownload);
                
                // Set flag menjadi true karena user sudah sukses men-download bukti fisik
                sudahUnduhGambar = true;
                alert("✅ Invoice Berhasil Disimpan ke Folder Download Laptop Anda! Tunjukkan gambar ini ke teknisi meja registrasi Hanbit.");
            });
        }

        function keamananBeranda() {
            // Jika user langsung klik kembali ke beranda tanpa download gambar bukti
            if (!sudahUnduhGambar) {
                // Paksa munculkan pop-up modal peringatan penahan
                document.getElementById('modal_peringatan').classList.remove('hidden');
            } else {
                // Jika sudah download, langsung loloskan kembali ke halaman awal
                window.location.href = 'index.php';
            }
        }

        function tutupModalPeringatan() {
            document.getElementById('modal_peringatan').classList.add('hidden');
        }
    </script>
</body>
</html>