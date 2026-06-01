-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 01, 2026 at 07:58 AM
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
  `status_toko` varchar(10) NOT NULL DEFAULT 'buka',
  `jam_tutup_store` time NOT NULL DEFAULT '18:00:00',
  `pesan_penutupan` text DEFAULT NULL,
  `jam_buka_store` time NOT NULL DEFAULT '09:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin_accounts`
--

INSERT INTO `admin_accounts` (`id_admin`, `username`, `password`, `nama_lengkap`, `max_kuota_harian`, `status_toko`, `jam_tutup_store`, `pesan_penutupan`, `jam_buka_store`) VALUES
(1, 'admin', 'admin123', 'Kenneth Hansel', 50, 'buka', '18:00:00', 'Maaf, Hanbit sedang tidak menerima antrean perbaikan untuk sementara waktu.', '09:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `id_customer` int(11) NOT NULL,
  `nama_customer` varchar(100) NOT NULL,
  `no_hp` varchar(20) NOT NULL,
  `email` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`id_customer`, `nama_customer`, `no_hp`, `email`) VALUES
(1, 'Algazali', '628123456789', 'alga@gmail.com'),
(2, 'Griselda', '628987654321', 'grisel@gmail.com');

-- --------------------------------------------------------

--
-- Table structure for table `laptop_brands`
--

CREATE TABLE `laptop_brands` (
  `id_brand` int(11) NOT NULL,
  `nama_brand` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `laptop_brands`
--

INSERT INTO `laptop_brands` (`id_brand`, `nama_brand`) VALUES
(1, 'ASUS'),
(2, 'LENOVO'),
(3, 'HP'),
(4, 'DELL'),
(5, 'ACER'),
(6, 'ADVAN'),
(7, 'AXIOO'),
(8, 'MSI');

-- --------------------------------------------------------

--
-- Table structure for table `laptop_series`
--

CREATE TABLE `laptop_series` (
  `id_series` int(11) NOT NULL,
  `id_brand` int(11) NOT NULL,
  `nama_series` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `laptop_series`
--

INSERT INTO `laptop_series` (`id_series`, `id_brand`, `nama_series`) VALUES
(1, 1, 'REPUBLIC OF GAMERS'),
(2, 1, 'ZENBOOK'),
(3, 1, 'VIVOBOOK'),
(4, 1, 'TUF GAMING'),
(5, 1, 'ZENBOOK PRO'),
(6, 1, 'TRANSFORMER'),
(7, 1, 'EXPERT BOOK'),
(8, 1, 'PROART SERIES'),
(9, 2, 'THINKPAD'),
(10, 2, 'IDEAPAD'),
(11, 2, 'YOGA SERIES'),
(12, 2, 'LEGION GAMING'),
(13, 2, 'LOQ SERIES'),
(14, 2, 'THINKBOOK'),
(15, 2, 'FLEX SERIES'),
(16, 2, 'SLIM SERIES'),
(17, 3, 'PAVILION'),
(18, 3, 'ENVY SERIES'),
(19, 3, 'SPECTRE'),
(20, 3, 'OMEN GAMING'),
(21, 3, 'VICTUS BY HP'),
(22, 3, 'HP ELITEBOOK'),
(23, 3, 'PROBOOK'),
(24, 3, 'HP ESSENTIAL'),
(25, 4, 'INSPIRON'),
(26, 4, 'XPS SERIES'),
(27, 4, 'VOSTRO'),
(28, 4, 'LATITUDE'),
(29, 4, 'ALIENWARE'),
(30, 4, 'G SERIES GAMING'),
(31, 4, 'PRECISION'),
(32, 4, 'CHROMEBOOK'),
(33, 5, 'ASPIRE'),
(34, 5, 'SWIFT SERIES'),
(35, 5, 'SPIN SERIES'),
(36, 5, 'NITRO GAMING'),
(37, 5, 'PREDATOR HELIOS'),
(38, 5, 'TRAVELMATE'),
(39, 5, 'ENDURO'),
(40, 5, 'ONE SERIES'),
(41, 6, 'SOULMATE'),
(42, 6, 'WORKPLUS'),
(43, 6, 'WORKPRO'),
(44, 6, 'PIXELWAR GAMING'),
(45, 6, '360 STYLUS'),
(46, 6, 'NASA SERIES'),
(47, 6, 'STARTGO'),
(48, 6, 'AVIO SERIES'),
(49, 7, 'MYBOOK'),
(50, 7, 'SLIMBOOK'),
(51, 7, 'PONGO GAMING'),
(52, 7, 'CHRONOS'),
(53, 7, 'SAGA SERIES'),
(54, 7, 'HYPE SERIES'),
(55, 7, 'WINDROID'),
(56, 7, 'NEON SERIES'),
(57, 8, 'KATANA GAMING'),
(58, 8, 'CYBORG SERIES'),
(59, 8, 'STEALTH'),
(60, 8, 'TITAN GT'),
(61, 8, 'MODERN SERIES'),
(62, 8, 'PRESTIGE'),
(63, 8, 'CREATOR'),
(64, 8, 'RAIDER GAMING');

-- --------------------------------------------------------

--
-- Table structure for table `reservations`
--

CREATE TABLE `reservations` (
  `id_reservasi` int(11) NOT NULL,
  `kode_order` varchar(50) NOT NULL,
  `id_customer` int(11) NOT NULL,
  `id_series` int(11) NOT NULL,
  `tgl_reservasi` date NOT NULL,
  `jam_slot` varchar(20) NOT NULL,
  `keluhan` text NOT NULL,
  `status_pengerjaan` enum('Menunggu Unit','Sedang Dikerjakan','Selesai') NOT NULL DEFAULT 'Menunggu Unit',
  `status_aktif` int(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
-- Indexes for table `reservations`
--
ALTER TABLE `reservations`
  ADD PRIMARY KEY (`id_reservasi`),
  ADD UNIQUE KEY `kode_order` (`kode_order`),
  ADD KEY `id_customer` (`id_customer`),
  ADD KEY `id_series` (`id_series`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin_accounts`
--
ALTER TABLE `admin_accounts`
  MODIFY `id_admin` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `id_customer` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `laptop_brands`
--
ALTER TABLE `laptop_brands`
  MODIFY `id_brand` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `laptop_series`
--
ALTER TABLE `laptop_series`
  MODIFY `id_series` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=65;

--
-- AUTO_INCREMENT for table `reservations`
--
ALTER TABLE `reservations`
  MODIFY `id_reservasi` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `laptop_series`
--
ALTER TABLE `laptop_series`
  ADD CONSTRAINT `laptop_series_ibfk_1` FOREIGN KEY (`id_brand`) REFERENCES `laptop_brands` (`id_brand`) ON DELETE CASCADE;

--
-- Constraints for table `reservations`
--
ALTER TABLE `reservations`
  ADD CONSTRAINT `reservations_ibfk_1` FOREIGN KEY (`id_customer`) REFERENCES `customers` (`id_customer`) ON DELETE CASCADE,
  ADD CONSTRAINT `reservations_ibfk_2` FOREIGN KEY (`id_series`) REFERENCES `laptop_series` (`id_series`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
