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

// 2. TANGKAP SEMUA PARAMETER DARI HALAMAN SEBELUMNYA
$brand_id       = isset($_GET['brand_id']) ? mysqli_real_escape_string($koneksi, $_GET['brand_id']) : '1';
$series_id      = isset($_GET['series_id']) ? mysqli_real_escape_string($koneksi, $_GET['series_id']) : '';
$id_masalah_raw = isset($_GET['id_masalah']) ? $_GET['id_masalah'] : ''; // Berisi deretan ID, misal: "1,3,8"
$masalah_custom = isset($_GET['masalah_custom']) ? mysqli_real_escape_string($koneksi, $_GET['masalah_custom']) : '';

// Array statis data masalah bawaan untuk dicocokkan dengan ID dari URL
$master_masalah = [
    '1' => ['nama' => 'Mati Total (No Power)', 'icon' => 'fa-power-off text-amber-500', 'penyebab' => 'Short pada jalur power IC, kerusakan chipset, atau tegangan listrik yang tidak stabil dari adaptor.', 'saran' => 'Memerlukan pengecekan jalur VCC pada motherboard untuk memastikan komponen yang terbakar.', 'harga' => 500000],
    '2' => ['nama' => 'Layar Bermasalah', 'icon' => 'fa-desktop text-amber-500', 'penyebab' => 'Layar pecah, bergaris, terdapat dead pixel, atau terjadi kerusakan pada kabel fleksibel display.', 'saran' => 'Disarankan melakukan penggantian panel LCD baru original untuk memulihkan tampilan gambar 100%.', 'harga' => 450000],
    '3' => ['nama' => 'Keyboard Error/Macet', 'icon' => 'fa-keyboard text-amber-500', 'penyebab' => 'Terkena tumpahan cairan, kondisi lembab, atau jalur fleksibel yang korosi/getas karena usia pemakaian.', 'saran' => 'Saran penggantian unit keyboard baru (Replacement) untuk menjamin fungsi mengetik kembali normal.', 'harga' => 200000],
    '4' => ['nama' => 'Laptop Lemot', 'icon' => 'fa-trash-alt text-amber-500', 'penyebab' => 'Sistem operasi korup, debu menumpuk pada heatsink, atau thermal paste bawaan sudah kering keras.', 'saran' => 'Perlu layanan Pembersihan Debu total, ganti thermal paste berkualitas, serta optimalisasi/reinstal OS.', 'harga' => 150000],
    '5' => ['nama' => 'Upgrade Hardware', 'icon' => 'fa-rocket text-amber-500', 'penyebab' => 'Kapasitas RAM atau storage bawaan laptop sudah terlalu kecil untuk menangani beban aplikasi terbaru.', 'saran' => 'Disarankan upgrade menambah kapasitas RAM atau beralih menggunakan SSD agar performa ngebut.', 'harga' => 350000],
    '6' => ['nama' => 'Tidak Bisa Charge', 'icon' => 'fa-bolt text-amber-500', 'penyebab' => 'Baterai sudah drop (wear level tinggi), sirkuit charging bermasalah, atau lubang jack dc charger longgar.', 'saran' => 'Perlu pengecekan konektor charger atau penggantian unit baterai baru agar daya tersimpan normal.', 'harga' => 250000],
    '7' => ['nama' => 'Masalah Wifi', 'icon' => 'fa-wifi text-amber-500', 'penyebab' => 'Driver wireless card crash, kabel antena internal putus, atau modul card wifi mengalami kerusakan fisik.', 'saran' => 'Perlu update software driver atau penggantian komponen modul wireless card yang baru.', 'harga' => 150000],
];

// Pecah ID masalah yang dikirim lewat URL menjadi array
$array_id_masalah = !empty($id_masalah_raw) ? explode(',', $id_masalah_raw) : [];

$list_tampilan_analisa = [];
$total_estimasi_harga = 0;
$ada_masalah_custom = false;

foreach ($array_id_masalah as $id) {
    if ($id == '8') {
        // Jika user memilih Masalah Lainnya
        $ada_masalah_custom = true;
        $list_tampilan_analisa[] = [
            'nama' => !empty($masalah_custom) ? $masalah_custom : 'Masalah Lainnya (Kustom)',
            'icon' => 'fa-bars text-amber-500',
            'is_custom' => true,
            'penyebab' => '-',
            'saran' => 'Memerlukan pengecekan dan diagnosa fisik langsung oleh teknisi Hanbit di toko.',
            'harga' => 0
        ];
    } elseif (isset($master_masalah[$id])) {
        // Jika memilih masalah bawaan pabrik
        $total_estimasi_harga += $master_masalah[$id]['harga'];
        $list_tampilan_analisa[] = [
            'nama' => $master_masalah[$id]['nama'],
            'icon' => $master_masalah[$id]['icon'],
            'is_custom' => false,
            'penyebab' => $master_masalah[$id]['penyebab'],
            'saran' => $master_masalah[$id]['saran'],
            'harga' => $master_masalah[$id]['harga']
        ];
    }
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hanbit - Hasil Analisa Booking</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght=400;500;600;700;800&display=swap');

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
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
            <h1 class="text-3xl md:text-4xl font-extrabold tracking-tight text-slate-900">Hasil Analisa <span class="text-yellow-400">Hanbit</span></h1>
            <p class="text-sm text-slate-400 font-medium">Berikut detail teknis dan estimasi biaya perbaikan Anda.</p>
        </div>

        <div class="flex flex-row flex-nowrap items-center justify-center gap-x-3 md:gap-x-5 text-[11px] md:text-xs font-black uppercase overflow-x-auto whitespace-nowrap py-2 mb-4">
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

            <div class="text-slate-400 flex items-center gap-2 shrink-0">
                <span class="w-7 h-7 rounded-full bg-gray-200 text-slate-400 flex items-center justify-center font-black text-xs">3</span>
                <span>Pilih Masalah</span>
            </div>
            <div class="h-[2px] w-6 md:w-10 bg-gray-200 shrink-0"></div>

            <div class="text-slate-900 flex items-center gap-2 shrink-0">
                <span class="w-7 h-7 rounded-full bg-[#facc15] text-slate-950 flex items-center justify-center font-black text-xs shadow-sm">4</span>
                <span>Detail & Book</span>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

            <div class="lg:col-span-7 space-y-6">

                <?php foreach ($list_tampilan_analisa as $analisa): ?>
                    <div class="bg-white border border-gray-100 rounded-[1.5rem] p-6 shadow-sm shadow-slate-100/60 space-y-4">
                        <div class="flex items-center gap-3 border-b border-gray-50 pb-3">
                            <div class="w-10 h-10 rounded-xl bg-yellow-50/60 flex items-center justify-center">
                                <i class="fas <?= $analisa['icon']; ?> text-lg"></i>
                            </div>
                            <h2 class="text-base font-extrabold text-slate-800"><?= htmlspecialchars($analisa['nama']); ?></h2>
                        </div>

                        <?php if ($analisa['is_custom']): ?>
                            <div class="bg-slate-50/70 rounded-xl p-4 border border-dashed border-slate-200">
                                <p class="text-xs font-bold text-slate-500 uppercase tracking-wide mb-1"><i class="fas fa-info-circle mr-1.5"></i> Catatan Teknisi</p>
                                <p class="text-[11px] text-slate-500 leading-relaxed font-medium"><?= htmlspecialchars($analisa['saran']); ?></p>
                            </div>
                        <?php else: ?>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div class="bg-slate-50/50 rounded-xl p-4 border border-gray-50">
                                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Penyebab Umum</p>
                                    <p class="text-[11px] text-slate-500 font-medium leading-relaxed"><?= htmlspecialchars($analisa['penyebab']); ?></p>
                                </div>
                                <div class="bg-slate-50/50 rounded-xl p-4 border border-gray-50">
                                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Saran Perbaikan</p>
                                    <p class="text-[11px] text-slate-500 font-medium leading-relaxed"><?= htmlspecialchars($analisa['saran']); ?></p>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>

                <div class="bg-[#1e293b] rounded-[1.5rem] p-6 text-white flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 shadow-md">
                    <div>
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Total Estimasi Harga</p>
                        <h2 class="text-3xl font-black text-[#facc15] mt-1">
                            <?= $ada_masalah_custom && $total_estimasi_harga == 0 ? "Berdasarkan Cek Fisik" : "Rp " . number_format($total_estimasi_harga, 0, ',', '.'); ?>
                        </h2>
                    </div>
                    <div class="space-y-1.5 text-right sm:text-left text-[11px] font-semibold text-slate-400">
                        <div><i class="fas fa-check-circle text-emerald-400 mr-1.5"></i> Termasuk Jasa Pasang</div>
                        <div><i class="fas fa-check-circle text-emerald-400 mr-1.5"></i> Garansi 30 Hari</div>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-5 bg-white border-2 border-yellow-400 rounded-[1.5rem] p-6 shadow-sm shadow-slate-100">

                <div class="bg-amber-50/60 border border-amber-100 rounded-xl p-4 space-y-1 mb-6">
                    <div class="flex items-center gap-2 text-xs font-extrabold text-slate-800">
                        <i class="fas fa-map-marker-alt text-amber-500"></i> LOKASI HANBIT
                    </div>
                    <p class="text-[11px] font-medium text-slate-500">Jl. Cihanjuang No.3 Kp. Centeng, Bandung</p>
                    <a href="https://maps.app.goo.gl/Tags1Nf9vR87wZux6" target="_blank" class="text-[11px] font-bold text-amber-600 hover:underline inline-block pt-0.5">
                        Buka di Google Maps
                    </a>
                </div>

                <h2 class="text-lg font-black text-slate-800 mb-4 flex items-center gap-2">Lengkapi Booking</h2>

                <form action="proses_booking.php" method="POST" onsubmit="return cekFinalKetersediaan(this)" class="space-y-6">
                    <input type="hidden" name="brand_id" value="<?= htmlspecialchars($brand_id); ?>">
                    <input type="hidden" name="series_id" value="<?= htmlspecialchars($series_id); ?>"> <input type="hidden" name="id_masalah" value="<?= htmlspecialchars($id_masalah_raw); ?>">
                    <input type="hidden" name="masalah_custom" value="<?= htmlspecialchars($masalah_custom); ?>">

                    <div class="space-y-1">
                        <label class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider">Nama Lengkap</label>
                        <input type="text" name="nama_lengkap" required class="w-full bg-slate-50 border border-slate-100 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-yellow-400 focus:bg-white transition duration-200">
                    </div>

                    <div class="space-y-1">
                        <label class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider">WhatsApp</label>
                        <input type="tel" name="whatsapp" required placeholder="08xxxxxxxxxx" class="w-full bg-slate-50 border border-slate-100 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-yellow-400 focus:bg-white transition duration-200">
                    </div>

                    <div class="space-y-1">
                        <label class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider">Alamat Lengkap Anda</label>
                        <textarea name="alamat_lengkap" rows="2" required placeholder="Tuliskan alamat rumah lengkap anda..." class="w-full bg-slate-50 border border-slate-100 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-yellow-400 focus:bg-white transition duration-200"></textarea>
                    </div>

                    <div class="space-y-1">
                        <label class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider">Tanggal Menyerahkan</label>
                        <input type="date" name="tanggal_menyerahkan" id="input_tanggal" onchange="periksaKuotaTanggal(this.value)" required class="w-full bg-slate-50 border border-slate-100 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-yellow-400 focus:bg-white transition duration-200">
                        <p id="notif_kuota" class="text-[10px] font-bold mt-1 hidden"></p>
                    </div>

                    <!-- Tombol Navigasi Bawah Berdampingan -->
                    <div class="flex gap-3 pt-2">
                        <!-- TOMBOL KEMBALI BARU: Mengarah ke halaman masalah dengan membawa parameter ID lama -->
                        <a href="cek_estimasi_masalah.php?brand_id=<?= $brand_id; ?>&series_id=<?= $series_id; ?>&id_masalah=<?= htmlspecialchars($id_masalah_raw); ?>&masalah_custom=<?= urlencode($masalah_custom); ?>"
                            class="w-1/3 bg-[#e2e8f0] hover:bg-[#cbd5e1] text-slate-600 font-bold text-xs uppercase rounded-xl flex items-center justify-center gap-1.5 transition select-none">
                            <i class="fas fa-chevron-left text-[10px]"></i> Kembali
                        </a>

                        <!-- TOMBOL BOOKING (Ukurannya otomatis menyesuaikan sisa space 2/3) -->
                        <button type="submit" id="btn_submit_booking" class="w-2/3 bg-[#ffd54f] hover:bg-[#ffca28] text-slate-900 font-black text-xs uppercase py-4 rounded-xl flex items-center justify-center gap-2 tracking-wide transition shadow-sm">
                            BOOKING SEKARANG <i class="fas fa-calendar-check text-sm"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </main>

    <footer class="bg-[#1e293b] text-slate-300 pt-16 pb-8 border-t border-slate-800">
        <div class="max-w-7xl mx-auto px-6 text-center text-[11px] font-medium text-slate-500">
            &copy; 2026 Hanbit. All rights reserved.
        </div>
    </footer>

    <script>
        // Data simulasi kuota penuh per tanggal yang diset oleh admin (Contoh demo presentasi)
        // Tanggal '2026-06-05' disimulasikan sudah penuh terisi batas pesanan servis laptop
        const daftarTanggalPenuh = ['2026-06-05', '2026-06-10'];
        let statusTanggalAman = true;

        function periksaKuotaTanggal(tanggalValue) {
            const labelNotif = document.getElementById('notif_kuota');
            const btnSubmit = document.getElementById('btn_submit_booking');

            if (daftarTanggalPenuh.includes(tanggalValue)) {
                statusTanggalAman = false;
                labelNotif.innerText = "❌ Maaf, Kuota servis hari ini penuh! Silakan pilih tanggal lain.";
                labelNotif.classList.remove('hidden', 'text-emerald-500');
                labelNotif.classList.add('text-rose-500');

                // Redupkan tombol sebagai indikator terkunci
                btnSubmit.classList.add('opacity-50', 'cursor-not-allowed');
            } else {
                statusTanggalAman = true;
                labelNotif.innerText = "✅ Kuota tersedia! Anda bisa memilih tanggal ini.";
                labelNotif.classList.remove('hidden', 'text-rose-500');
                labelNotif.classList.add('text-emerald-500');
                btnSubmit.classList.remove('opacity-50', 'cursor-not-allowed');
            }
        }

        // Pengaman berlapis saat form disubmit klik
        function cekFinalKetersediaan(form) {
            if (!statusTanggalAman) {
                alert("Tanggal yang Anda pilih sudah penuh! Silakan ganti ke tanggal menyerahkan yang lain.");
                return false;
            }
            return true;
        }
    </script>
</body>

</html>