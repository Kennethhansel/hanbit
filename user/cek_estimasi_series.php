<?php
require_once '../koneksi.php';

$brand_id = isset($_GET['brand_id']) ? mysqli_real_escape_string($koneksi, $_GET['brand_id']) : '';

if (empty($brand_id)) {
    header("Location: cek_estimasi.php");
    exit();
}

// Ambil nama brand aktif
$query_brand = "SELECT nama_brand FROM laptop_brands WHERE ID_Brand = '$brand_id' LIMIT 1";
$res_brand = mysqli_query($koneksi, $query_brand);
$data_brand = mysqli_fetch_assoc($res_brand);
$nama_brand_aktif = strtoupper($data_brand['nama_brand'] ?? '');

// Jika database kosong, sistem akan memakai mapping 7 series untuk tiap brand di bawah ini
$series_list = [];

// Tarik dinamis dari database terlebih dahulu
$query_series = "SELECT ID_Series, Nama_Series FROM laptop_series WHERE ID_Brand = '$brand_id'";
$result_series = mysqli_query($koneksi, $query_series);

while ($row = mysqli_fetch_assoc($result_series)) {
    $nama_file_gambar = strtolower(str_replace(' ', '_', $row['Nama_Series'])) . '.png';
    $series_list[] = [
        'id' => $row['ID_Series'],
        'nama' => $row['Nama_Series'],
        'foto' => 'images/' . $nama_file_gambar
    ];
}

// MAP DATA FALLBACK: Mengunci 7 pilihan box series untuk tiap-tiap dari 8 merek laptop
if (empty($series_list)) {
    if ($brand_id == 1 || $nama_brand_aktif == 'ASUS') {
        $nama_brand_aktif = 'ASUS';
        $series_data = ['REPUBLIC OF GAMERS', 'ZENBOOK', 'VIVOBOOK', 'TUF GAMING', 'ZENBOOK PRO', 'TRANSFORMER', 'EXPERT BOOK'];
    } elseif ($brand_id == 2 || $nama_brand_aktif == 'LENOVO') {
        $nama_brand_aktif = 'LENOVO';
        $series_data = ['THINKPAD', 'IDEAPAD', 'YOGA SERIES', 'LEGION GAMING', 'LOQ SERIES', 'THINKBOOK', 'FLEX SERIES'];
    } elseif ($brand_id == 3 || $nama_brand_aktif == 'HP') {
        $nama_brand_aktif = 'HP';
        $series_data = ['PAVILION', 'ENVY SERIES', 'SPECTRE', 'OMEN GAMING', 'VICTUS BY HP', 'HP ELITEBOOK', 'PROBOOK'];
    } elseif ($brand_id == 4 || $nama_brand_aktif == 'DELL') {
        $nama_brand_aktif = 'DELL';
        $series_data = ['INSPIRON', 'XPS SERIES', 'VOSTRO', 'LATITUDE', 'ALIENWARE', 'G SERIES GAMING', 'PRECISION'];
    } elseif ($brand_id == 5 || $nama_brand_aktif == 'ACER') {
        $nama_brand_aktif = 'ACER';
        $series_data = ['ASPIRE', 'SWIFT SERIES', 'SPIN SERIES', 'NITRO GAMING', 'PREDATOR HELIOS', 'TRAVELMATE', 'ENDURO'];
    } elseif ($brand_id == 6 || $nama_brand_aktif == 'ADVAN') {
        $nama_brand_aktif = 'ADVAN';
        $series_data = ['SOULMATE', 'WORKPLUS', 'WORKPRO', 'PIXELWAR GAMING', '360 STYLUS', 'NASA SERIES', 'STARTGO'];
    } elseif ($brand_id == 7 || $nama_brand_aktif == 'AXIOO') {
        $nama_brand_aktif = 'AXIOO';
        $series_data = ['MYBOOK', 'SLIMBOOK', 'PONGO GAMING', 'CHRONOS', 'SAGA SERIES', 'HYPE SERIES', 'WINDROID'];
    } elseif ($brand_id == 8 || $nama_brand_aktif == 'MSI') {
        $nama_brand_aktif = 'MSI';
        $series_data = ['KATANA GAMING', 'CYBORG SERIES', 'STEALTH', 'TITAN GT', 'MODERN SERIES', 'PRESTIGE', 'CREATOR'];
    } else {
        $nama_brand_aktif = 'LAPTOP';
        $series_data = ['SERIES 1', 'SERIES 2', 'SERIES 3', 'SERIES 4', 'SERIES 5', 'SERIES 6', 'SERIES 7'];
    }

    // Bangun struktur array 7 box
    foreach ($series_data as $index => $nama_seri) {
        $nama_file_gambar = strtolower(str_replace(' ', '_', $nama_seri)) . '.png';
        $series_list[] = [
            'id' => $index + 1,
            'nama' => $nama_seri,
            'foto' => 'images/' . $nama_file_gambar
        ];
    }
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hanbit - Pilih Series <?= $nama_brand_aktif; ?></title>
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
            <h1 class="text-3xl md:text-4xl font-extrabold tracking-tight text-slate-900">Pilih Series <?= $nama_brand_aktif; ?> Anda</h1>
            <p class="text-sm text-slate-400 font-medium">Silakan pilih series Laptop yang sesuai dengan Laptop anda.</p>
        </div>

        <div class="flex flex-row flex-nowrap items-center justify-center gap-x-3 md:gap-x-5 text-[11px] md:text-xs font-black uppercase overflow-x-auto whitespace-nowrap">
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

        <form action="cek_estimasi_masalah.php" method="GET" class="space-y-8 pt-4">
            <input type="hidden" name="brand_id" value="<?= htmlspecialchars($brand_id); ?>">
            <input type="hidden" name="series_id" id="selected_series_id" required>

            <div class="flex flex-wrap justify-center gap-5">
                <?php foreach($series_list as $series): ?>
                    <div type="button" onclick="pilihSeries(this, '<?= $series['id']; ?>')" 
                            class="series-card flex flex-col items-center justify-center pt-8 pb-6 px-4 bg-white border border-gray-100 rounded-[1.5rem] cursor-pointer hover:border-yellow-400 hover:bg-yellow-50/5 transition-all duration-200 group select-none shadow-sm shadow-slate-100 w-[calc(50%-1.25rem)] sm:w-[calc(25%-1.25rem)] min-w-[180px]">
                        
                        <div class="w-full h-24 flex items-center justify-center mb-4 bg-white">
                            <img src="<?= $series['foto']; ?>" alt="Foto <?= $series['nama']; ?>" 
                                 class="max-w-full max-h-full object-contain transition-all duration-200">
                        </div>
                        
                        <span class="text-[11px] font-extrabold uppercase tracking-normal text-slate-700 text-center group-hover:text-slate-900 leading-tight">
                            <?= $series['nama']; ?>
                        </span>
                    </div>
                <?php endforeach; ?>
            </div>

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
        <div class="max-w-7xl mx-auto px-6 grid grid-cols-1 md:grid-cols-12 gap-10 mb-12">
            <div class="md:col-span-5 space-y-4">
                <div class="flex items-center gap-3">
                    <img src="../logo warna.png" alt="Logo Hanbit" class="w-8 h-8 object-contain">
                    <span class="text-lg font-black text-white tracking-tight">Hanbit</span>
                </div>
                <p class="text-xs text-slate-400 font-medium leading-relaxed max-w-sm">Solusi profesional untuk perawatan dan perbaikan laptop Anda dengan garansi terpercaya.</p>
            </div>
            <div class="md:col-span-4 space-y-3.5">
                <h4 class="text-white font-bold text-sm uppercase tracking-wider">Kontak</h4>
                <ul class="space-y-2.5 text-xs font-semibold text-slate-400">
                    <li><i class="fas fa-phone-alt text-yellow-400 mr-2"></i> +62 851-5979-4427</li>
                    <li><i class="fas fa-envelope text-yellow-400 mr-2"></i> hanbit0925@gmail.com</li>
                </ul>
            </div>
            <div class="md:col-span-3 space-y-3.5">
                <h4 class="text-white font-bold text-sm uppercase tracking-wider">Jam Operasional</h4>
                <ul class="space-y-2 text-xs font-semibold text-slate-400">
                    <li>Senin - Jumat: 09.00 - 18.00</li>
                </ul>
            </div>
        </div>
        <div class="max-w-7xl mx-auto px-6 pt-6 border-t border-slate-800/80 text-center text-[11px] font-medium text-slate-500">
            &copy; 2026 Hanbit. All rights reserved.
        </div>
    </footer>

    <script>
        function pilihSeries(elemenTarget, idSeries) {
            const semuaCard = document.querySelectorAll('.series-card');
            semuaCard.forEach(card => {
                card.classList.remove('border-2', 'border-yellow-400', 'bg-yellow-50/10');
                card.classList.add('border-gray-100', 'bg-white');
            });
            elemenTarget.classList.remove('border-gray-100', 'bg-white');
            elemenTarget.classList.add('border-2', 'border-yellow-400', 'bg-yellow-50/10');
            document.getElementById('selected_series_id').value = idSeries;
        }
    </script>
</body>
</html>