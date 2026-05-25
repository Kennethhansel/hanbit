-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 25, 2026 at 07:40 AM
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
  `nama_lengkap` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin_accounts`
--

INSERT INTO `admin_accounts` (`id_admin`, `username`, `password`, `nama_lengkap`) VALUES
(1, 'admin', 'admin123', 'Kenneth Hansel');

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
(3, 'ACER');

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
(1, 1, 'ROG Zephyrus G14'),
(2, 2, 'Legion 5 Pro'),
(3, 3, 'Swift 3 Infinite');

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
-- Dumping data for table `reservations`
--

INSERT INTO `reservations` (`id_reservasi`, `kode_order`, `id_customer`, `id_series`, `tgl_reservasi`, `jam_slot`, `keluhan`, `status_pengerjaan`, `status_aktif`) VALUES
(1, 'HB260520-001', 1, 1, '2026-05-25', '09.00 - 11.00', 'Ganti thermal paste & cleaning debu fan', 'Menunggu Unit', 1),
(2, 'HB260520-002', 2, 2, '2026-05-25', '13.00 - 15.00', 'Laptop lemot, mau upgrade SSD', 'Sedang Dikerjakan', 1);

-- --------------------------------------------------------

--
-- Table structure for table `system_settings`
--

CREATE TABLE `system_settings` (
  `id_setting` int(11) NOT NULL,
  `setting_key` varchar(50) NOT NULL,
  `setting_value` varchar(100) NOT NULL,
  `keterangan` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `system_settings`
--

INSERT INTO `system_settings` (`id_setting`, `setting_key`, `setting_value`, `keterangan`) VALUES
(1, 'max_kuota_harian', '4', 'Batasan maksimal unit laptop harian');

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
-- Indexes for table `system_settings`
--
ALTER TABLE `system_settings`
  ADD PRIMARY KEY (`id_setting`),
  ADD UNIQUE KEY `setting_key` (`setting_key`);

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
  MODIFY `id_brand` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `laptop_series`
--
ALTER TABLE `laptop_series`
  MODIFY `id_series` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `reservations`
--
ALTER TABLE `reservations`
  MODIFY `id_reservasi` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `system_settings`
--
ALTER TABLE `system_settings`
  MODIFY `id_setting` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

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
