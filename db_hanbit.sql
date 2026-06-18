-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 18, 2026 at 09:03 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_hanbit`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin_accounts`
--

CREATE TABLE `admin_accounts` (
  `id_admin` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nama_lengkap` varchar(100) NOT NULL,
  `max_kuota_harian` int(11) NOT NULL DEFAULT 50,
  `target_omzet` int(11) DEFAULT 5000000,
  `status_toko` varchar(10) NOT NULL DEFAULT 'buka',
  `jam_tutup_store` time NOT NULL DEFAULT '18:00:00',
  `pesan_penutupan` text DEFAULT NULL,
  `jam_buka_store` time NOT NULL DEFAULT '09:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin_accounts`
--

INSERT INTO `admin_accounts` (`id_admin`, `username`, `password`, `nama_lengkap`, `max_kuota_harian`, `target_omzet`, `status_toko`, `jam_tutup_store`, `pesan_penutupan`, `jam_buka_store`) VALUES
(1, 'adminkenneth', 'labs123', 'Kenneth', 10, 5000000, 'buka', '18:00:00', 'Maaf, Hanbit sedang tidak menerima antrean perbaikan untuk sementara waktu.', '10:00:00'),
(2, 'adminalicia', 'Labs123', 'Alicia', 50, 5000000, 'buka', '18:00:00', NULL, '09:00:00'),
(3, 'adminalbertus', 'Labs123', 'Albertus', 50, 5000000, 'buka', '18:00:00', NULL, '09:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `id_customer` int(11) NOT NULL,
  `nama_customer` varchar(100) NOT NULL,
  `no_hp` varchar(20) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `alamat` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`id_customer`, `nama_customer`, `no_hp`, `email`, `alamat`) VALUES
(13, 'Kenneth Hansel', '628991839055', 'hanselkenneth30@gmail.com', 'erfe'),
(14, 'Hansel', '6285159794427', 'hanselkenneth30@gmail.com', 'tes'),
(15, 'Hansel', '5476547', 'dgfss@gmail.com', NULL),
(16, 'Trstgrsfg', '54645646', 'sdfgsf@gmail.com', 'dfdsgdfsg');

-- --------------------------------------------------------

--
-- Table structure for table `invoice_details`
--

CREATE TABLE `invoice_details` (
  `id_detail` int(11) NOT NULL,
  `no_invoice` varchar(50) NOT NULL,
  `nama_item` varchar(255) NOT NULL,
  `harga_item` int(11) NOT NULL,
  `deskripsi_tambahan` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `invoice_details`
--

INSERT INTO `invoice_details` (`id_detail`, `no_invoice`, `nama_item`, `harga_item`, `deskripsi_tambahan`) VALUES
(15, 'INV-20260610-D769', 'Keyboard Error/Macet', 250000, 'Estimasi awal pilihan customer'),
(16, 'INV-20260610-304A', 'Keyboard Error/Macet', 20000, 'Estimasi awal pilihan customer'),
(17, 'INV-20260610-98EF', 'Masalah Koneksi Wifi Card', 17500, 'Estimasi awal pilihan customer'),
(18, 'INV-20260610-F5C8', 'Layanan Paket Basic Maintenance Package', 75000, 'Harga flat paket perawatan berkala'),
(19, 'INV-20260610-9A7F', 'Laptop Lemot / OS Corrupt', 175000, 'Estimasi awal pilihan customer'),
(20, 'INV-20260610-2C55', 'Laptop Lemot / OS Corrupt', 175000, 'Estimasi awal pilihan customer'),
(21, 'INV-20260610-282B', 'Masalah Koneksi Wifi Card', 175000, 'Estimasi awal pilihan customer'),
(22, 'INV-20260610-D615', 'Keluhan Khusus: Tes', 0, 'Menunggu analisa fisik lapangan oleh teknisi'),
(23, 'INV-20260610-88F4', 'Mati Total', 100003, 'Estimasi awal pilihan customer'),
(24, 'INV-20260610-88F4', 'RAM', 3423423, 'tes'),
(25, 'INV-20260610-0B1C', 'Layanan Paket Standard Maintenance Package', 150000, 'Harga flat paket perawatan berkala'),
(26, 'INV-20260610-59DA', 'Mati Total', 34556456, 'Estimasi awal pilihan customer'),
(27, 'INV-20260610-59DA', 'RAM', 435634, 'gf'),
(28, 'INV-20260610-1918', 'Masalah Wifi', 175001, 'Estimasi awal pilihan customer'),
(29, 'INV-20260610-4C57', 'Masalah Wifi', 175000, 'Estimasi awal pilihan customer'),
(30, 'INV-20260610-98CB', 'Laptop Lemot', 175000, 'Estimasi awal pilihan customer'),
(31, 'INV-20260611-547D', 'Keyboard Error', 250000, 'Estimasi awal pilihan customer'),
(32, 'INV-20260614-4A89', 'Mobo Rusak', 1000000, 'Ganti IC'),
(33, 'INV-20260618-5404', 'Layanan Paket Basic Maintenance Package', 75000, 'Harga flat paket perawatan berkala'),
(34, 'INV-20260614-4A89', 'RAM', 235435, 'Tes'),
(35, 'INV-20260618-5B29', 'Layanan Paket Standard Maintenance Package', 150000, 'Harga flat paket perawatan berkala');

-- --------------------------------------------------------

--
-- Table structure for table `laptop_brands`
--

CREATE TABLE `laptop_brands` (
  `id_brand` int(11) NOT NULL,
  `nama_brand` varchar(50) NOT NULL,
  `logo` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `laptop_brands`
--

INSERT INTO `laptop_brands` (`id_brand`, `nama_brand`, `logo`) VALUES
(1, 'ASUS', 'asus.png'),
(2, 'LENOVO', 'lenovo.png'),
(3, 'HP', 'hp.png'),
(4, 'DELL', 'dell.png'),
(5, 'ACER', 'acer.png'),
(6, 'ADVAN', 'advan.png'),
(7, 'AXIOO', 'axioo.png'),
(8, 'MSI', 'msi.png');

-- --------------------------------------------------------

--
-- Table structure for table `laptop_series`
--

CREATE TABLE `laptop_series` (
  `id_series` int(11) NOT NULL,
  `id_brand` int(11) NOT NULL,
  `nama_series` varchar(100) NOT NULL,
  `foto` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `laptop_series`
--

INSERT INTO `laptop_series` (`id_series`, `id_brand`, `nama_series`, `foto`) VALUES
(1, 1, 'REPUBLIC OF GAMERS', 'republic_of_gamers.png'),
(2, 1, 'ZENBOOK', 'zenbook.png'),
(3, 1, 'VIVOBOOK', 'vivobook.png'),
(4, 1, 'TUF GAMING', 'tuf_gaming.png'),
(5, 1, 'ZENBOOK PRO', 'zenbook_pro.png'),
(6, 1, 'TRANSFORMER', 'transformer.png'),
(7, 1, 'EXPERT BOOK', 'expert_book.png'),
(8, 1, 'PROART SERIES', 'proart_series.png'),
(9, 2, 'THINKPAD', 'thinkpad.png'),
(10, 2, 'IDEAPAD', 'ideapad.png'),
(11, 2, 'YOGA SERIES', 'yoga_series.png'),
(12, 2, 'LEGION GAMING', 'legion_gaming.png'),
(13, 2, 'LOQ SERIES', 'loq_series.png'),
(14, 2, 'THINKBOOK', 'thinkbook.png'),
(15, 2, 'FLEX SERIES', 'flex_series.png'),
(16, 2, 'SLIM SERIES', 'slim_series.png'),
(17, 3, 'PAVILION', 'pavilion.png'),
(18, 3, 'ENVY SERIES', 'envy_series.png'),
(19, 3, 'SPECTRE', 'spectre.png'),
(20, 3, 'OMEN GAMING', 'omen_gaming.png'),
(21, 3, 'VICTUS BY HP', 'victus_by_hp.png'),
(22, 3, 'HP ELITEBOOK', 'hp_elitebook.png'),
(23, 3, 'PROBOOK', 'probook.png'),
(24, 3, 'HP ESSENTIAL', 'hp_essential.png'),
(25, 4, 'INSPIRON', 'inspiron.png'),
(26, 4, 'XPS SERIES', 'xps_series.png'),
(27, 4, 'VOSTRO', 'vostro.png'),
(28, 4, 'LATITUDE', 'latitude.png'),
(29, 4, 'ALIENWARE', 'alienware.png'),
(30, 4, 'G SERIES GAMING', 'g_series_gaming.png'),
(31, 4, 'PRECISION', 'precision.png'),
(32, 4, 'CHROMEBOOK', 'chromebook.png'),
(33, 5, 'ASPIRE', 'aspire.png'),
(34, 5, 'SWIFT SERIES', 'swift_series.png'),
(35, 5, 'SPIN SERIES', 'spin_series.png'),
(36, 5, 'NITRO GAMING', 'nitro_gaming.png'),
(37, 5, 'PREDATOR HELIOS', 'predator_helios.png'),
(38, 5, 'TRAVELMATE', 'travelmate.png'),
(39, 5, 'CHROMEBOOK', 'chromebook.png'),
(40, 5, 'ASPIRE LITE', 'aspire_lite.png'),
(41, 6, 'SOULMATE', 'soulmate.png'),
(42, 6, 'WORKPLUS', 'workplus.png'),
(43, 6, 'WORKPRO', 'workpro.png'),
(44, 6, 'PIXELWAR GAMING', 'pixelwar_gaming.png'),
(45, 6, '360 STYLUS', '360_stylus.png'),
(46, 6, 'T BOOK TRANSFOMER', 't_book_transfomer.png'),
(47, 6, 'AI GEN', 'ai_gen.png'),
(48, 6, 'WORKMATE', 'workmate.png'),
(49, 7, 'MYBOOK', 'mybook.png'),
(50, 7, 'SLIMBOOK', 'slimbook.png'),
(51, 7, 'PONGO GAMING', 'pongo_gaming.png'),
(53, 7, 'SAGA SERIES', 'saga_series.png'),
(54, 7, 'HYPE SERIES', 'hype_series.png'),
(57, 8, 'KATANA GAMING', 'katana_gaming.png'),
(58, 8, 'CYBORG SERIES', 'cyborg_series.png'),
(59, 8, 'STEALTH', 'stealth.png'),
(60, 8, 'TITAN GT', 'titan_gt.png'),
(61, 8, 'MODERN SERIES', 'modern_series.png'),
(62, 8, 'PRESTIGE', 'prestige.png'),
(63, 8, 'CREATOR', 'creator.png'),
(64, 8, 'RAIDER GAMING', 'raider_gaming.png');

-- --------------------------------------------------------

--
-- Table structure for table `master_masalah`
--

CREATE TABLE `master_masalah` (
  `id_masalah` int(11) NOT NULL,
  `nama_masalah` varchar(100) DEFAULT NULL,
  `deskripsi_masalah` text DEFAULT NULL,
  `penyebab_masalah` text DEFAULT NULL,
  `saran_teknisi` text DEFAULT NULL,
  `harga_estimasi` int(11) NOT NULL DEFAULT 0,
  `deskripsi` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `master_masalah`
--

INSERT INTO `master_masalah` (`id_masalah`, `nama_masalah`, `deskripsi_masalah`, `penyebab_masalah`, `saran_teknisi`, `harga_estimasi`, `deskripsi`) VALUES
(1, 'Mati Total', 'Laptop tidak menyala sama sekali, tidak ada indikator lampu, atau No Power.', 'Terjadi short circuit (korsleting) pada jalur power IC utama, kerusakan komponen mofset motherboard, atau kerusakan total pada adaptor charger.', 'Diperlukan pembongkaran unit sasis untuk melakukan remap skema kelistrikan motherboard, penggantian IC power yang short, serta kalibrasi tegangan arus masuk.', 750000, NULL),
(2, 'Layar Bermasalah', 'Layar LCD bergaris, pecah fisik, berkedip (flicker), atau tidak tampil gambar.', 'Kerusakan fisik pada panel kristal cair LCD akibat tekanan, jalur flexible screen yang robek/korosi, atau gangguan output signal dari IC Display VRAM.', 'Disarankan melakukan penggantian satu set panel LCD Assembly baru dengan kualitas original bawaan pabrik serta pengecekan kelayakan kabel flexible display.', 750000, NULL),
(3, 'Keyboard Error', 'Tombol keyboard macet, tidak merespons saat ditekan, atau mengetik sendiri.', 'Adanya kerusakan pada jalur sirkuit membran konduktif di bawah tombol akibat tumpahan cairan, tumpukan debu tebal, atau aus karena faktor usia pemakaian.', 'Diperlukan penggantian part modul keyboard baru secara keseluruhan (replacement) guna mengembalikan fungsi input pengetikan agar normal dan responsif kembali.', 250000, NULL),
(4, 'Laptop Lemot', 'Performa lambat, laptop cepat panas, pembersihan debu internal, dan ganti thermal paste.', 'Mengeringnya thermal paste bawaan yang memicu overheat (suhu ekstrem), penyumbatan debu pada fan cooler, serta penumpukan berkas cache file sampah pada sistem operasi.', 'Perlu dilakukan tindakan Premium Cleaning (pembersihan total debu internal), repasting menggunakan thermal paste berkualitas tinggi (high-performance), serta optimasi konfigurasi startup OS.', 175000, NULL),
(5, 'Upgrade Hardware', 'Peningkatan kapasitas RAM atau pemasangan SSD agar boot sistem jauh lebih cepat.', 'Keterbatasan kecepatan baca/tulis dari komponen Harddisk (HDD) konvensional serta kapasitas memori RAM yang sudah tidak mencukupi kebutuhan aplikasi modern.', 'Sangat disarankan melakukan migrasi media penyimpanan utama ke SSD (Solid State Drive) berkecepatan tinggi serta melakukan upgrade ekspansi kapasitas RAM minimal menjadi 8GB atau 16GB.', 450000, NULL),
(6, 'Tidak Bisa Charge', 'Baterai tidak mengisi daya, drop, atau lubang colokan port charger longgar.', 'Degradasi (penurunan kesehatan) sel kimia di dalam baterai, port DC Jack di laptop goyang/patah, atau rusaknya IC Charging yang bertugas mengalirkan daya ke baterai.', 'Diperlukan tindakan perbaikan atau re-soldering kaki-kaki port DC Jack yang longgar, penggantian unit baterai replacement original, atau perbaikan pada modul IC Charger motherboard.', 300000, NULL),
(7, 'Masalah Wifi', 'Sinyal wifi lemah, sering terputus sendiri, atau kartu wireless tidak terdeteksi.', 'Kerusakan fisik pada card hardware Wireless Card adapter, lepasnya kabel konektor antena wifi internal di dalam casing, atau crash driver network pada sistem.', 'Diperlukan instalasi ulang driver network versi paling stabil, pengecekan posisi pin antena internal, atau penggantian modul hardware Wifi Card baru jika komponen lama terdeteksi mati.', 175000, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `master_packages`
--

CREATE TABLE `master_packages` (
  `id_paket` int(11) NOT NULL,
  `kode_paket` varchar(50) NOT NULL,
  `nama_paket` varchar(255) NOT NULL,
  `harga_kantoran` int(11) NOT NULL,
  `harga_gaming` int(11) NOT NULL,
  `garansi` varchar(50) NOT NULL,
  `benefits` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `master_packages`
--

INSERT INTO `master_packages` (`id_paket`, `kode_paket`, `nama_paket`, `harga_kantoran`, `harga_gaming`, `garansi`, `benefits`) VALUES
(1, 'basic', 'Paket Basic', 75000, 100000, 'Garansi 7 Hari', 'Pembersihan debu & kotoran sasis bagian internal laptop\r\nPembersihan eksternal pada sela-sela tombol keyboard\r\nPembersihan bercak noda dan debu pada permukaan layar\r\nPelumasan poros kipas laptop agar putaran kembali senyap'),
(2, 'standard', 'Paket Standard', 150000, 200000, 'Garansi 14 Hari', 'Pembersihan menyeluruh debu & kotoran pada komponen internal\r\nPembersihan sasis luar, sela-sela keyboard, dan layar laptop\r\nPenggantian Thermal Paste Premium untuk menurunkan suhu panas\r\nPembersihan mendalam pada bilah kipas (fan) & jalur heatsink\r\nPengecekan kesehatan hardware dasar & stabilitas software'),
(3, 'premium', 'Paket Premium', 200000, 250000, 'Garansi 30 Hari', 'Semua layanan pembersihan mendalam pada Paket Standard\r\nPenggantian Thermal Paste performa tinggi (High-Performance)\r\nOptimasi penuh kecepatan sistem operasi (OS) agar anti-lemot\r\nPembaruan (Update) driver hardware dan aplikasi esensial\r\nLayanan Install Ulang OS Windows secara bersih (Optional)');

-- --------------------------------------------------------

--
-- Table structure for table `reservations`
--

CREATE TABLE `reservations` (
  `no_invoice` varchar(50) NOT NULL,
  `id_customer` int(11) NOT NULL,
  `nama_pelanggan` varchar(100) NOT NULL,
  `no_whatsapp` varchar(20) NOT NULL,
  `laptop_detail` varchar(250) NOT NULL,
  `alamat_pelanggan` text NOT NULL,
  `email_pelanggan` varchar(100) DEFAULT NULL,
  `paket_tipe` varchar(50) NOT NULL,
  `segmen_laptop` varchar(250) NOT NULL,
  `total_harga` int(11) NOT NULL,
  `tanggal_booking` date NOT NULL,
  `tanggal_dikerjakan` date DEFAULT NULL,
  `tanggal_selesai` date DEFAULT NULL,
  `status_order` varchar(30) NOT NULL DEFAULT 'PENDING',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `catatan_teknisi` text DEFAULT NULL,
  `estimasi_selesai` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reservations`
--

INSERT INTO `reservations` (`no_invoice`, `id_customer`, `nama_pelanggan`, `no_whatsapp`, `laptop_detail`, `alamat_pelanggan`, `email_pelanggan`, `paket_tipe`, `segmen_laptop`, `total_harga`, `tanggal_booking`, `tanggal_dikerjakan`, `tanggal_selesai`, `status_order`, `created_at`, `catatan_teknisi`, `estimasi_selesai`) VALUES
('INV-20260610-0B1C', 13, 'Kenneth Hansel', '628991839055', '5656', '5656', NULL, 'standard', 'kantoran', 150000, '2026-06-19', '2026-06-13', NULL, 'PENGECEKAN', '2026-06-10 10:33:23', NULL, NULL),
('INV-20260610-1918', 13, 'Kenneth Hansel', '628991839055', 'Dell Inspiron', 'frer', NULL, 'custom_estimasi', '', 175001, '2026-06-23', '2026-06-13', NULL, 'PENGECEKAN', '2026-06-10 12:48:22', NULL, NULL),
('INV-20260610-282B', 13, 'Kenneth Hansel', '628991839055', 'Dell Inspiron', 'dsds', NULL, 'custom_estimasi', '', 175000, '2026-06-10', NULL, NULL, 'PENDING_ADMIN', '2026-06-10 05:10:30', NULL, NULL),
('INV-20260610-2C55', 13, 'Kenneth Hansel', '628991839055', 'Asus Transformer', 'fdfdf', NULL, 'custom_estimasi', '', 175000, '2026-06-10', NULL, NULL, 'PENDING', '2026-06-10 05:10:14', NULL, NULL),
('INV-20260610-304A', 13, 'Kenneth Hansel', '628991839055', 'Acer Chromebook', 'dfgfg', NULL, 'custom_estimasi', '', 20000, '2026-06-26', NULL, NULL, 'PENDING', '2026-06-10 04:40:54', NULL, NULL),
('INV-20260610-4C57', 13, 'Kenneth Hansel', '628991839055', 'Asus Transformer', 'dfdf', NULL, 'custom_estimasi', '', 175000, '2026-06-18', '2026-06-10', '2026-06-10', 'SELESAI', '2026-06-10 12:50:53', '', NULL),
('INV-20260610-59DA', 13, 'Kenneth Hansel', '628991839055', 'Lenovo Yoga Series', 'tyhgyh', NULL, 'custom_estimasi', '', 34992090, '2026-06-18', '2026-06-10', NULL, 'PENDING_ADMIN', '2026-06-10 11:23:38', '', NULL),
('INV-20260610-88F4', 13, 'Kenneth Hansel', '628991839055', 'Advan Pixelwar Gaming', '898', NULL, 'custom_estimasi', '', 3523426, '2026-06-11', '2026-06-10', NULL, 'PERBAIKAN', '2026-06-10 10:17:33', 'tes', NULL),
('INV-20260610-98CB', 13, 'Kenneth Hansel', '628991839055', 'Lenovo Thinkpad', 'df', NULL, 'custom_estimasi', '', 175000, '2026-06-20', '2026-06-10', NULL, 'PENGECEKAN', '2026-06-10 12:53:22', NULL, NULL),
('INV-20260610-98EF', 13, 'Kenneth Hansel', '628991839055', 'Acer Chromebook', 'gfg', NULL, 'custom_estimasi', '', 17500, '2026-06-19', '2026-06-10', NULL, 'PERBAIKAN', '2026-06-10 04:49:19', 'fgfdg', NULL),
('INV-20260610-9A7F', 13, 'Kenneth Hansel', '628991839055', 'Axioo Slimbook', 'rer', NULL, 'custom_estimasi', '', 175000, '2026-06-11', NULL, '2026-06-10', 'SELESAI', '2026-06-10 04:59:21', NULL, NULL),
('INV-20260610-D615', 13, 'Kenneth Hansel', '628991839055', 'Advan Pixelwar Gaming', 'fg', NULL, 'custom_estimasi', 'Tes', 0, '2026-06-12', '2026-06-13', NULL, 'PENGECEKAN', '2026-06-10 09:39:51', NULL, NULL),
('INV-20260610-D769', 13, 'Kenneth Hansel', '628991839055', 'Acer Swift Series', 'fd', NULL, 'custom_estimasi', '', 250000, '2026-06-24', '2026-06-10', '2026-06-10', 'SELESAI', '2026-06-10 04:37:18', 'dsfds', NULL),
('INV-20260610-F5C8', 13, 'Kenneth Hansel', '628991839055', 'fdf', 'dfd', NULL, 'basic', 'kantoran', 75000, '2026-06-19', NULL, NULL, 'PENDING_ADMIN', '2026-06-10 04:56:28', 'tes', NULL),
('INV-20260611-547D', 13, 'Kenneth Hansel', '628991839055', 'Msi Titan Gt', 'd', NULL, 'custom_estimasi', '', 250000, '2026-06-17', '2026-06-11', '2026-06-11', 'SELESAI', '2026-06-11 03:57:56', '', NULL),
('INV-20260614-4A89', 13, 'Kenneth Hansel', '628991839055', 'Msi Stealth', 'er', NULL, 'custom_estimasi', 'tes', 1235435, '2026-06-27', '2026-06-17', NULL, 'PERBAIKAN', '2026-06-14 07:38:39', 'Unit fisik laptop Msi Stealth telah diterima oleh teknisi pada tanggal 19 Juni 2026. Saat ini sedang dilakukan pembongkaran sasis untuk pengecekan fisik menyeluruh, diagnosa tegangan short sirkuit, dan pengecekan komponen internal.', NULL),
('INV-20260618-5404', 14, 'Rudi', '6285159794427', 'TEs', 'tes', NULL, 'basic', 'kantoran', 75000, '2026-06-18', '2026-06-18', NULL, 'PENGECEKAN', '2026-06-18 15:01:09', 'Unit fisik laptop TEs telah diterima oleh teknisi pada tanggal 18 Juni 2026. Saat ini sedang dilakukan pembongkaran sasis untuk pengecekan fisik menyeluruh, diagnosa tegangan short sirkuit, dan pengecekan komponen internal.', NULL),
('INV-20260618-5B29', 13, 'Kenneth Hansel', '628991839055', 'df', 'erfe', NULL, 'standard', 'kantoran', 150000, '2026-06-26', NULL, NULL, 'PENDING', '2026-06-18 18:28:23', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `tb_katalog`
--

CREATE TABLE `tb_katalog` (
  `id_produk` int(11) NOT NULL,
  `nama_produk` varchar(255) NOT NULL,
  `kategori` varchar(100) NOT NULL,
  `harga` int(11) NOT NULL,
  `deskripsi` text NOT NULL,
  `gambar` varchar(255) NOT NULL,
  `link_ecommerce` varchar(555) DEFAULT '#'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_katalog`
--

INSERT INTO `tb_katalog` (`id_produk`, `nama_produk`, `kategori`, `harga`, `deskripsi`, `gambar`, `link_ecommerce`) VALUES
(3, 'Keyboard Xiaomi RedmiBook Pro 15 XMA2101 XMA2101-BN XMA2101-BWt', 'keyboard', 250000, 'Original', '1780606469_keyboard_redmibook_pro_15.png', 'https://shopee.co.id/Keyboard-Xiaomi-RedmiBook-Pro-15-XMA2101-XMA2101-BN-XMA2101-BWt-i.24081738.27206305126?xptdk=47258f46-a5bd-431a-858f-84635cb03518'),
(14, 'Baterai Infinix X2', 'aksesoris', 500000, 'BATERAI LAPTOP INFINIX X2 417282-3S ORIGINAL  Model: 417282-3S  Capacity: 4330mAh  Voltage: 11.55V  Withour: 50.01W', '1781114324_infinix_infinix_baterai_laptop_infinix_inbook_x2_417282-3s_series_original_full01_opg6gzfk.jpg', 'https://shopee.co.id/BATERAI-LAPTOP-INFINIX-X2-417282-3S-ORIGINAL-i.6130060.27462349333?extraParams=%7B%22display_model_id%22%3A251667248512%2C%22model_selection_logic%22%3A3%7D'),
(15, 'THERMAL GRIZZLY PhaseSheet PTM 50x40x0.2mm Phase Change Material Thermal Pad Paste', 'pasta', 200000, 'Technical data   Color: Grey  Electrically conductive: non-conductive  Operating temperature: -75°C to 150°C  Typical Application: Thermal pads for processors and graphics chips  Length: 50 mm  Width: 40 mm  Height: 0,2 mm  Package size: 21 x 15 x 1,5 cm  *Gross weight: 21 g  *Net weight: 2 g', '1781114468_sg-11134201-7rdya-lzal4vwsuu768a.jpg', 'https://shopee.co.id/THERMAL-GRIZZLY-PhaseSheet-PTM-50x40x0.2mm-Phase-Change-Material-Thermal-Pad-Paste-i.20725967.25085206355?extraParams=%7B%22display_model_id%22%3A223140147608%2C%22model_selection_logic%22%3A3%7D');

-- --------------------------------------------------------

--
-- Table structure for table `tb_kategori_katalog`
--

CREATE TABLE `tb_kategori_katalog` (
  `id_kategori` int(11) NOT NULL,
  `nama_kategori` varchar(100) NOT NULL,
  `slug_kategori` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_kategori_katalog`
--

INSERT INTO `tb_kategori_katalog` (`id_kategori`, `nama_kategori`, `slug_kategori`) VALUES
(1, 'Memory RAM', 'memoryram'),
(2, 'Storage SSD/HDD', 'storage'),
(3, 'LCD Screen', 'lcd'),
(4, 'Keyboard', 'keyboard'),
(5, 'Charger', 'charger'),
(6, 'Thermal Paste', 'pasta'),
(7, 'Flashdisk', 'flashdisk'),
(8, 'Aksesori', 'aksesoris');

-- --------------------------------------------------------

--
-- Table structure for table `tb_kategori_porto`
--

CREATE TABLE `tb_kategori_porto` (
  `id_kategori` int(11) NOT NULL,
  `nama_kategori` varchar(100) NOT NULL,
  `slug_kategori` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_kategori_porto`
--

INSERT INTO `tb_kategori_porto` (`id_kategori`, `nama_kategori`, `slug_kategori`) VALUES
(1, 'Thermal Repasting Component', 'maintenance'),
(2, 'Hardware Upgrades', 'hardwareupgrades'),
(3, 'Motherboard IC Circuit Repair', 'matot'),
(5, 'Maintenance', 'maintenance5');

-- --------------------------------------------------------

--
-- Table structure for table `tb_portofolio`
--

CREATE TABLE `tb_portofolio` (
  `id_porto` int(11) NOT NULL,
  `kategori` varchar(50) NOT NULL,
  `tipe_media` enum('gambar','video') NOT NULL,
  `judul` varchar(255) NOT NULL,
  `deskripsi` text NOT NULL,
  `sumber_media` varchar(555) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_portofolio`
--

INSERT INTO `tb_portofolio` (`id_porto`, `kategori`, `tipe_media`, `judul`, `deskripsi`, `sumber_media`) VALUES
(5, 'Cleaning Maintenance', 'video', 'Maintenance Asus Vivobook', 'Tujuannya cuma satu: Jaga kesehatan laptop kamu supaya tetap vit, performa stabil, dan nggak cepat panas! ❄️⚡', 'https://www.instagram.com/reel/DUlE61hjreF/embed'),
(8, 'maintenance5', 'video', 'Maintenance Lenovo LOQ', 'Replace Keyboard & Reinstall OS', 'https://www.youtube.com/embed/_J2vyIiMeno?si=CtM9QlYlpQE6DWv5'),
(9, 'maintenance5', 'video', 'Asus Vivobook', 'Maintenance', 'https://www.youtube.com/embed/hUPXsbNw4uY?si=6CnrjKv0nalf-sYV');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin_accounts`
--
ALTER TABLE `admin_accounts`
  ADD PRIMARY KEY (`id_admin`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`id_customer`);

--
-- Indexes for table `invoice_details`
--
ALTER TABLE `invoice_details`
  ADD PRIMARY KEY (`id_detail`),
  ADD KEY `no_invoice` (`no_invoice`);

--
-- Indexes for table `laptop_brands`
--
ALTER TABLE `laptop_brands`
  ADD PRIMARY KEY (`id_brand`);

--
-- Indexes for table `laptop_series`
--
ALTER TABLE `laptop_series`
  ADD PRIMARY KEY (`id_series`),
  ADD KEY `id_brand` (`id_brand`);

--
-- Indexes for table `master_masalah`
--
ALTER TABLE `master_masalah`
  ADD PRIMARY KEY (`id_masalah`);

--
-- Indexes for table `master_packages`
--
ALTER TABLE `master_packages`
  ADD PRIMARY KEY (`id_paket`),
  ADD UNIQUE KEY `kode_paket` (`kode_paket`);

--
-- Indexes for table `reservations`
--
ALTER TABLE `reservations`
  ADD PRIMARY KEY (`no_invoice`);

--
-- Indexes for table `tb_katalog`
--
ALTER TABLE `tb_katalog`
  ADD PRIMARY KEY (`id_produk`);

--
-- Indexes for table `tb_kategori_katalog`
--
ALTER TABLE `tb_kategori_katalog`
  ADD PRIMARY KEY (`id_kategori`),
  ADD UNIQUE KEY `nama_kategori` (`nama_kategori`),
  ADD UNIQUE KEY `slug_kategori` (`slug_kategori`);

--
-- Indexes for table `tb_kategori_porto`
--
ALTER TABLE `tb_kategori_porto`
  ADD PRIMARY KEY (`id_kategori`),
  ADD UNIQUE KEY `nama_kategori` (`nama_kategori`),
  ADD UNIQUE KEY `slug_kategori` (`slug_kategori`);

--
-- Indexes for table `tb_portofolio`
--
ALTER TABLE `tb_portofolio`
  ADD PRIMARY KEY (`id_porto`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin_accounts`
--
ALTER TABLE `admin_accounts`
  MODIFY `id_admin` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `id_customer` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `invoice_details`
--
ALTER TABLE `invoice_details`
  MODIFY `id_detail` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT for table `laptop_brands`
--
ALTER TABLE `laptop_brands`
  MODIFY `id_brand` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `laptop_series`
--
ALTER TABLE `laptop_series`
  MODIFY `id_series` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=66;

--
-- AUTO_INCREMENT for table `master_masalah`
--
ALTER TABLE `master_masalah`
  MODIFY `id_masalah` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `master_packages`
--
ALTER TABLE `master_packages`
  MODIFY `id_paket` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `tb_katalog`
--
ALTER TABLE `tb_katalog`
  MODIFY `id_produk` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `tb_kategori_katalog`
--
ALTER TABLE `tb_kategori_katalog`
  MODIFY `id_kategori` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `tb_kategori_porto`
--
ALTER TABLE `tb_kategori_porto`
  MODIFY `id_kategori` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `tb_portofolio`
--
ALTER TABLE `tb_portofolio`
  MODIFY `id_porto` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `invoice_details`
--
ALTER TABLE `invoice_details`
  ADD CONSTRAINT `invoice_details_ibfk_1` FOREIGN KEY (`no_invoice`) REFERENCES `reservations` (`no_invoice`) ON DELETE CASCADE;

--
-- Constraints for table `laptop_series`
--
ALTER TABLE `laptop_series`
  ADD CONSTRAINT `laptop_series_ibfk_1` FOREIGN KEY (`id_brand`) REFERENCES `laptop_brands` (`id_brand`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
