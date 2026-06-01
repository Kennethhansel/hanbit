<?php
// 1. BUAT KONEKSI KE DATABASE
$host = "localhost";
$user = "root";
$pass = "";
$db   = "db_hanbit";
$koneksi = mysqli_connect($host, $user, $pass, $db);

if (!$koneksi) {
    die("Koneksi database Hanbit gagal: " . mysqli_connect_error());
}

// 2. TANGKAP PARAMETER DARI HALAMAN SEBELUMNYA
$brand_id = isset($_GET['brand_id']) ? mysqli_real_escape_string($koneksi, $_GET['brand_id']) : '1';
$series_id = isset($_GET['series_id']) ? mysqli_real_escape_string($koneksi, $_GET['series_id']) : '';

// Ambil nama brand untuk navigasi/informasi
$query_brand = "SELECT nama_brand FROM laptop_brands WHERE ID_Brand = '$brand_id' LIMIT 1";
$res_brand = mysqli_query($koneksi, $query_brand);
$data_brand = mysqli_fetch_assoc($res_brand);
$nama_brand_aktif = strtoupper($data_brand['nama_brand'] ?? 'LAPTOP');

// 3. DAFTAR MASALAH (Fix Ikon Charge pakai fa-bolt)
$masalah_list = [
    ['id' => '1', 'nama' => 'Mati Total', 'deskripsi' => 'Laptop tidak menyala sama sekali atau No Power.', 'icon' => 'fa-power-off text-amber-500'],
    ['id' => '2', 'nama' => 'Layar Bermasalah', 'deskripsi' => 'Layar pecah, bergaris, atau tidak tampil gambar.', 'icon' => 'fa-desktop text-amber-500'],
    ['id' => '3', 'nama' => 'Keyboard/Touchpad', 'deskripsi' => 'Tombol macet, mengetik sendiri, atau tidak responsif.', 'icon' => 'fa-keyboard text-amber-500'],
    ['id' => '4', 'nama' => 'Laptop Lemot', 'deskripsi' => 'Pembersihan debu, ganti thermal paste, atau optimasi OS.', 'icon' => 'fa-trash-alt text-amber-500'],
    ['id' => '5', 'nama' => 'Upgrade Hardware', 'deskripsi' => 'Tambah kapasitas RAM atau ganti SSD agar lebih cepat.', 'icon' => 'fa-rocket text-amber-500'],
    ['id' => '6', 'nama' => 'Tidak Bisa Charge', 'deskripsi' => 'Baterai tidak mengisi atau lubang charger longgar.', 'icon' => 'fa-bolt text-amber-500'],
    ['id' => '7', 'nama' => 'Masalah Wifi', 'deskripsi' => 'Sinyal lemah, tidak terdeteksi, atau sering putus.', 'icon' => 'fa-wifi text-amber-500'],
];
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hanbit - Kendala Laptop</title>
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
            <h1 class="text-3xl md:text-4xl font-extrabold tracking-tight text-slate-900">Apa Kendala Laptop Anda?</h1>
            <p class="text-sm text-slate-400 font-medium">Pilih satu atau beberapa masalah yang dialami untuk mendapatkan estimasi harga akurat.</p>
        </div>

        <div class="flex flex-row flex-nowrap items-center justify-center gap-x-3 md:gap-x-5 text-[11px] md:text-xs font-black uppercase overflow-x-auto whitespace-nowrap py-2">
            <div class="text-slate-400 flex items-center gap-2 shrink-0">
                <span class="w-7 h-7 rounded-full bg-gray-200 text-slate-400 flex items-center justify-center font-black text-xs">1</span> 
                <span>Pilih Merek</span>
            </div>
            <div class="h-[2px] w-6 md:w-10 bg-gray-200 shrink-0"></div>
            
            <div class="text-slate-400 flex items-center gap-2 shrink-0">
                <span class="w-7 h-7 rounded-full bg-gray-200 text-slate-400 flex items-center justify-center font-black text-xs">2</span> 
                <span>Pilih Series</span>
            </div>
            <div class="h-[2px] w-6 md:w-10 bg-gray-200 shrink-0"></div>
            
            <div class="text-slate-900 flex items-center gap-2 shrink-0">
                <span class="w-7 h-7 rounded-full bg-[#facc15] text-slate-950 flex items-center justify-center font-black text-xs shadow-sm">3</span> 
                <span>Pilih Masalah</span>
            </div>
            <div class="h-[2px] w-6 md:w-10 bg-gray-200 shrink-0"></div>
            
            <div class="text-slate-400 flex items-center gap-2 shrink-0">
                <span class="w-7 h-7 rounded-full bg-gray-200 text-slate-400 flex items-center justify-center font-black text-xs">4</span> 
                <span>Detail & Book</span>
            </div>
        </div>

        <form action="cek_estimasi_detail.php" method="GET" onsubmit="return validasiMasalah()" class="space-y-8 pt-4">
            <input type="hidden" name="brand_id" value="<?= htmlspecialchars($brand_id); ?>">
            <input type="hidden" name="series_id" value="<?= htmlspecialchars($series_id); ?>">
            <input type="hidden" name="id_masalah" id="selected_masalah_id">
            <input type="hidden" name="masalah_custom" id="masalah_custom_input">

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-5 justify-center">
                <?php foreach($masalah_list as $m): ?>
                    <div type="button" data-id="<?= $m['id']; ?>" onclick="toggleMasalah(this, '<?= $m['id']; ?>')"
                         class="masalah-card flex flex-col items-center justify-start pt-8 pb-6 px-5 bg-white border border-gray-100 rounded-[1.5rem] cursor-pointer hover:border-yellow-400 hover:bg-yellow-50/5 transition-all duration-200 group select-none shadow-sm shadow-slate-100 min-h-[200px]">
                        
                        <div class="w-12 h-12 flex items-center justify-center mb-4 bg-yellow-50/60 group-hover:bg-yellow-100 rounded-xl transition-colors">
                            <i class="fas <?= $m['icon']; ?> text-xl"></i>
                        </div>
                        
                        <h3 class="text-sm font-extrabold text-slate-800 text-center mb-1 group-hover:text-slate-900"><?= $m['nama']; ?></h3>
                        <p class="text-[11px] text-slate-400 font-medium text-center leading-normal"><?= $m['deskripsi']; ?></p>
                    </div>
                <?php endforeach; ?>

                <div id="card_masalah_lainnya" data-id="8" type="button" onclick="bukaModalMasalahLainnya()"
                     class="masalah-card flex flex-col items-center justify-start pt-8 pb-6 px-5 bg-white border border-gray-100 rounded-[1.5rem] cursor-pointer hover:border-yellow-400 hover:bg-yellow-50/5 transition-all duration-200 group select-none shadow-sm shadow-slate-100 min-h-[200px]">
                    
                    <div class="w-12 h-12 flex items-center justify-center mb-4 bg-yellow-50/60 group-hover:bg-yellow-100 rounded-xl transition-colors">
                        <i class="fas fa-bars text-amber-500 text-xl"></i>
                    </div>
                    
                    <h3 class="text-sm font-extrabold text-slate-800 text-center mb-1 group-hover:text-slate-900">Masalah Lainnya</h3>
                    <p id="deskripsi_masalah_lainnya" class="text-[11px] text-slate-400 font-medium text-center leading-normal">Kendala fisik atau software lainnya.</p>
                </div>
            </div>

            <div class="flex justify-between items-center pt-6 border-t border-gray-100">
                <a href="cek_estimasi_series.php?brand_id=<?= $brand_id; ?>" class="bg-[#e2e8f0] hover:bg-[#cbd5e1] text-slate-600 font-bold text-xs uppercase px-7 py-3 rounded-lg flex items-center gap-2 transition">
                    <i class="fas fa-chevron-left text-[10px]"></i> Kembali
                </a>
                <button type="submit" class="bg-[#ffd54f] hover:bg-[#ffca28] text-slate-900 font-extrabold text-xs uppercase px-7 py-3 rounded-lg flex items-center gap-2 tracking-normal transition shadow-sm">
                    Cek Hasil Analisa <i class="fas fa-search text-[10px]"></i>
                </button>
            </div>
        </form>
    </main>

    <div id="modal_masalah_lainnya" class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-50 hidden flex items-center justify-center px-4">
        <div class="bg-white rounded-[1.5rem] max-w-md w-full p-6 shadow-xl space-y-4 border border-gray-100">
            <div class="flex justify-between items-center border-b border-gray-100 pb-3">
                <h3 class="text-base font-extrabold text-slate-900"><i class="fas fa-edit text-amber-500 mr-2"></i> Tulis Kendala Laptop</h3>
                <button type="button" onclick="tutupModalMasalahLainnya()" class="text-slate-400 hover:text-slate-600 text-sm"><i class="fas fa-times text-lg"></i></button>
            </div>
            <div class="space-y-2">
                <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Deskripsi Masalah</label>
                <textarea id="text_masalah_custom" rows="4" class="w-full px-4 py-3 rounded-xl border border-slate-200 text-sm focus:outline-none focus:border-yellow-400 transition" placeholder="Contoh: Laptop kena air lalu mati..."></textarea>
            </div>
            <div class="flex justify-end gap-3 pt-2">
                <button type="button" onclick="tutupModalMasalahLainnya()" class="bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-bold px-4 py-2.5 rounded-xl transition">Batal</button>
                <button type="button" onclick="simpanMasalahCustom()" class="bg-[#ffd54f] hover:bg-[#ffca28] text-slate-900 text-xs font-extrabold px-4 py-2.5 rounded-xl transition shadow-sm">Simpan Masalah</button>
            </div>
        </div>
    </div>

    <footer class="bg-[#1e293b] text-slate-300 pt-16 pb-8 border-t border-slate-800">
        <div class="max-w-7xl mx-auto px-6 text-center text-[11px] font-medium text-slate-500">
            &copy; 2026 Hanbit. All rights reserved.
        </div>
    </footer>

    <script>
        // Array global untuk menampung banyak ID masalah terklik
        let kumpulanMasalahTerpilih = [];

        function toggleMasalah(elemenTarget, idMasalah) {
            const index = kumpulanMasalahTerpilih.indexOf(idMasalah);

            if (index > -1) {
                // Jika ID sudah ada di array, berarti user klik ulang untuk MEMBATALKAN (Remove)
                kumpulanMasalahTerpilih.splice(index, 1);
                elemenTarget.classList.remove('border-2', 'border-yellow-400', 'bg-yellow-50/10');
                elemenTarget.classList.add('border-gray-100', 'bg-white');
            } else {
                // Jika belum ada, MASUKKAN ke array pilihan (Add)
                kumpulanMasalahTerpilih.push(idMasalah);
                elemenTarget.classList.remove('border-gray-100', 'bg-white');
                elemenTarget.classList.add('border-2', 'border-yellow-400', 'bg-yellow-50/10');
            }

            // Gabungkan barisan array jadi string pakai koma (misal: "1,3,6") ke input hidden
            document.getElementById('selected_masalah_id').value = kumpulanMasalahTerpilih.join(',');
        }

        // Fungsi kontrol Pop-up Modal
        function bukaModalMasalahLainnya() {
            document.getElementById('modal_masalah_lainnya').classList.remove('hidden');
        }

        function tutupModalMasalahLainnya() {
            document.getElementById('modal_masalah_lainnya').classList.add('hidden');
        }

        function simpanMasalahCustom() {
            const textInput = document.getElementById('text_masalah_custom').value.trim();
            const cardLainnya = document.getElementById('card_masalah_lainnya');
            
            if (textInput !== "") {
                document.getElementById('masalah_custom_input').value = textInput;
                document.getElementById('deskripsi_masalah_lainnya').innerText = textInput;
                
                // Jika belum terpilih, masukkan ID 8 ke array Multi-select
                if (kumpulanMasalahTerpilih.indexOf('8') === -1) {
                    kumpulanMasalahTerpilih.push('8');
                    cardLainnya.classList.remove('border-gray-100', 'bg-white');
                    cardLainnya.classList.add('border-2', 'border-yellow-400', 'bg-yellow-50/10');
                    document.getElementById('selected_masalah_id').value = kumpulanMasalahTerpilih.join(',');
                }
                
                tutupModalMasalahLainnya();
            } else {
                // Jika dikosongkan saat edit, hapus ID 8 dari pilihan
                const index = kumpulanMasalahTerpilih.indexOf('8');
                if (index > -1) {
                    kumpulanMasalahTerpilih.splice(index, 1);
                    cardLainnya.classList.remove('border-2', 'border-yellow-400', 'bg-yellow-50/10');
                    cardLainnya.classList.add('border-gray-100', 'bg-white');
                    document.getElementById('selected_masalah_id').value = kumpulanMasalahTerpilih.join(',');
                }
                document.getElementById('masalah_custom_input').value = "";
                document.getElementById('deskripsi_masalah_lainnya').innerText = "Kendala fisik atau software lainnya.";
                tutupModalMasalahLainnya();
            }
        }

        // PENGUNCI FORM: Wajib klik minimal 1 masalah sebelum next
        function validasiMasalah() {
            const nilaiInput = document.getElementById('selected_masalah_id').value;
            if (nilaiInput === "" || nilaiInput === null || kumpulanMasalahTerpilih.length === 0) {
                alert("Silakan pilih minimal satu kendala laptop Anda terlebih dahulu!");
                return false;
            }
            return true;
        }
    </script>
</body>
</html>