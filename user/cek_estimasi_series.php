<?php
// 1. BUAT KONEKSI MANDIRI DI BARIS PALING ATAS
$host = "localhost";
$user = "root";
$pass = "";
$db   = "db_hanbit";
$koneksi = mysqli_connect($host, $user, $pass, $db);

if (!$koneksi) {
    die("Koneksi database Hanbit gagal: " . mysqli_connect_error());
}

// 2. AMBIL PARAMETER BRAND ID DARI URL
$brand_id = isset($_GET['brand_id']) ? mysqli_real_escape_string($koneksi, $_GET['brand_id']) : '1';

if (empty($brand_id)) {
    header("Location: cek_estimasi.php");
    exit();
}

// 3. AMBIL NAMA BRAND AKTIF UNTUK JUDUL & NAMA FOLDER
$query_brand = "SELECT nama_brand FROM laptop_brands WHERE ID_Brand = '$brand_id' LIMIT 1";
$res_brand = mysqli_query($koneksi, $query_brand);
$data_brand = mysqli_fetch_assoc($res_brand);
$nama_brand_aktif = strtoupper($data_brand['nama_brand'] ?? 'LAPTOP');

// Buat nama folder huruf kecil sesuai strukturmu (misal: "asus", "lenovo")
$nama_folder_merek = strtolower($nama_brand_aktif);

$series_list = [];

// 4. TARIK DATA SERIES SECARA DINAMIS DARI DATABASE
$query_series = "SELECT ID_Series, Nama_Series FROM laptop_series WHERE ID_Brand = '$brand_id'";
$result_series = mysqli_query($koneksi, $query_series);

while ($row = mysqli_fetch_assoc($result_series)) {
    // Hilangkan spasi gaib di akhir nama teks database (mengatasi masalah Vivobook )
    $nama_clean = trim($row['Nama_Series']);
    $nama_file_foto = strtolower(str_replace(' ', '_', $nama_clean)) . '.png';
    
    // JALUR DINAMIS BARU: mengarah ke images/series/merek/nama_seri.png
    $jalur_foto_lengkap = 'images/series/' . $nama_folder_merek . '/' . $nama_file_foto;
    
    $series_list[] = [
        'id' => $row['ID_Series'],
        'nama' => strtoupper($nama_clean),
        'foto' => $jalur_foto_lengkap
    ];
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hanbit - Pilih Series <?= htmlspecialchars($nama_brand_aktif); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght=400;500;600;700;800&display=swap');
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>

<body class="bg-[#f8fafc] text-slate-900 antialiased min-h-screen flex flex-col justify-between">

    <nav class="bg-white border-b border-gray-100 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-6 h-20 flex justify-between items-center">
            <div class="flex items-center gap-3">
                <img src="../logo warna.png" alt="Logo Hanbit" class="w-10 h-10 object-contain">
                <span class="text-3xl font-extrabold tracking-tight">Hanbit</span>
            </div>
            <div class="hidden md:flex items-center gap-8 text-sm font-semibold text-slate-600">
                <a href="index.php" class="hover:text-yellow-600 transition">Home</a>
                <a href="index.php#layanan" class="hover:text-slate-900 transition">Katalog</a>
                <a href="index.php#layanan" class="hover:text-slate-900 transition">Layanan</a>
                <a href="index.php#kontak" class="hover:text-slate-900 transition">Kontak</a>
            </div>
            <a href="https://wa.me/6285159794427" target="_blank" class="bg-[#00e676] hover:bg-[#00c853] text-white text-xs font-bold px-4 py-2.5 rounded-xl flex items-center gap-2 shadow-sm transition">
                <i class="fab fa-whatsapp text-sm"></i> Chat Via WA
            </a>
        </div>
    </nav>

    <main class="max-w-5xl mx-auto w-full px-6 pt-10 pb-16 flex-1 flex flex-col justify-start gap-5">
        
        <div class="text-center space-y-1">
            <h1 class="text-3xl md:text-4xl font-extrabold tracking-tight text-slate-900">Pilih Series Laptop <?= htmlspecialchars($nama_brand_aktif); ?> Anda</h1>
            <p class="text-sm text-slate-400 font-medium">Silakan pilih series Laptop yang sesuai dengan Laptop anda.</p>
        </div>

        <div class="flex flex-row flex-nowrap items-center justify-center gap-x-3 md:gap-x-5 text-[11px] md:text-xs font-black uppercase overflow-x-auto whitespace-nowrap py-2">
            <div class="text-slate-400 flex items-center gap-2 shrink-0">
                <span class="w-7 h-7 rounded-full bg-gray-200 text-slate-400 flex items-center justify-center font-black text-xs">1</span> 
                <span>Pilih Merek</span>
            </div>
            <div class="h-[2px] w-6 md:w-10 bg-gray-200 shrink-0"></div>
            
            <div class="text-slate-900 flex items-center gap-2 shrink-0">
                <span class="w-7 h-7 rounded-full bg-[#facc15] text-slate-950 flex items-center justify-center font-black text-xs shadow-sm">2</span> 
                <span>Pilih Series</span>
            </div>
            <div class="h-[2px] w-6 md:w-10 bg-gray-200 shrink-0"></div>
            
            <div class="text-slate-400 flex items-center gap-2 shrink-0">
                <span class="w-7 h-7 rounded-full bg-gray-200 text-slate-400 flex items-center justify-center font-black text-xs">3</span> 
                <span>Pilih Masalah</span>
            </div>
            <div class="h-[2px] w-6 md:w-10 bg-gray-200 shrink-0"></div>
            
            <div class="text-slate-400 flex items-center gap-2 shrink-0">
                <span class="w-7 h-7 rounded-full bg-gray-200 text-slate-400 flex items-center justify-center font-black text-xs">4</span> 
                <span>Detail & Book</span>
            </div>
        </div>

        <!-- Form dengan Tambahan onsubmit untuk Validasi -->
        <form action="cek_estimasi_masalah.php" method="GET" onsubmit="return validasiPilihan()" class="space-y-8 pt-4">
            <input type="hidden" name="brand_id" value="<?= htmlspecialchars($brand_id); ?>">
            <!-- Hapus atribut required HTML bawaan karena kita ganti pakai JavaScript yang lebih pintar -->
            <input type="hidden" name="series_id" id="selected_series_id">

            <!-- Tampilan Grid Box 8 Series per Merek -->
            <div class="flex flex-wrap justify-center gap-5">
                <?php foreach($series_list as $series): ?>
                    <div type="button" onclick="pilihSeries(this, '<?= htmlspecialchars($series['id']); ?>')" 
                            class="series-card flex flex-col items-center justify-center pt-8 pb-6 px-4 bg-white border border-gray-100 rounded-[1.5rem] cursor-pointer hover:border-yellow-400 hover:bg-yellow-50/5 transition-all duration-200 group select-none shadow-sm shadow-slate-100 w-[calc(50%-1.25rem)] sm:w-[calc(25%-1.25rem)] min-w-[180px]">
                        
                        <div class="w-full h-24 flex items-center justify-center mb-4 bg-white">
                            <img src="<?= htmlspecialchars($series['foto']); ?>" alt="Foto <?= htmlspecialchars($series['nama']); ?>" 
                                 class="max-w-full max-h-full object-contain transition-all duration-200">
                        </div>
                        
                        <span class="text-[11px] font-extrabold uppercase tracking-normal text-slate-700 text-center group-hover:text-slate-900 leading-tight">
                            <?= htmlspecialchars($series['nama']); ?>
                        </span>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Tombol Navigasi Bawah -->
            <div class="flex justify-between items-center pt-6 border-t border-gray-100">
                <a href="cek_estimasi.php" class="bg-[#e2e8f0] hover:bg-[#cbd5e1] text-slate-600 font-bold text-xs uppercase px-7 py-3 rounded-lg flex items-center gap-2 transition">
                    <i class="fas fa-chevron-left text-[10px]"></i> Kembali
                </a>
                <button type="submit" class="bg-[#ffd54f] hover:bg-[#ffca28] text-slate-900 font-extrabold text-xs uppercase px-7 py-3 rounded-lg flex items-center gap-2 tracking-normal transition shadow-sm">
                    Selanjutnya <i class="fas fa-chevron-right text-[10px]"></i>
                </button>
            </div>
        </form>
    </main>

    <footer class="bg-[#1e293b] text-slate-300 pt-16 pb-8 border-t border-slate-800">
        <div class="max-w-7xl mx-auto px-6 text-center text-[11px] font-medium text-slate-500">
            &copy; 2026 Hanbit. All rights reserved.
        </div>
    </footer>

    <!-- JavaScript dengan Fungsi Validasi Pengunci Halaman -->
    <script>
        function pilihSeries(elemenTarget, idSeries) {
            const semuaCard = document.querySelectorAll('.series-card');
            semuaCard.forEach(card => {
                card.classList.remove('border-2', 'border-yellow-400', 'bg-yellow-50/10');
                card.classList.add('border-gray-100', 'bg-white');
            });
            elemenTarget.classList.remove('border-gray-100', 'bg-white');
            elemenTarget.classList.add('border-2', 'border-yellow-400', 'bg-yellow-50/10');
            
            // Isi nilai ID Series ke input hidden
            document.getElementById('selected_series_id').value = idSeries;
        }

        // FUNGSI PENGUNCI: Memastikan user sudah klik salah satu kartu sebelum pindah
        function validasiPilihan() {
            const idSeriesTerpilih = document.getElementById('selected_series_id').value;
            
            if (idSeriesTerpilih === "" || idSeriesTerpilih === null) {
                // Munculkan notifikasi penolak
                alert("Silakan pilih salah satu Series Laptop anda terlebih dahulu sebelum melanjutkan!");
                
                // Kembalikan false agar form membatalkan proses submit dan tetap di halaman ini
                return false; 
            }
            
            // Jika aman, izinkan lanjut ke halaman masalah
            return true;
        }
    </script>
</body>
</html>