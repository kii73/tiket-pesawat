-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Nov 17, 2025 at 07:45 AM
-- Server version: 8.0.30
-- PHP Version: 8.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `tiket-pesawat`
--

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `id` int NOT NULL,
  `id_pesawat` int NOT NULL,
  `id_user` int NOT NULL,
  `kode` varchar(50) NOT NULL,
  `status` enum('disetujui','menunggu') NOT NULL,
  `kelas` enum('ekonomi','bisnis','first class') NOT NULL,
  `jumlah` int NOT NULL DEFAULT '1',
  `total_harga` int NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `tanggal_beli` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`id`, `id_pesawat`, `id_user`, `kode`, `status`, `kelas`, `jumlah`, `total_harga`, `created_at`, `tanggal_beli`) VALUES
(37, 22, 12, 'YXBQEV', 'disetujui', 'bisnis', 1, 120000, '2025-11-17 07:29:49', '2025-11-17 07:29:49'),
(38, 22, 8, 'QCFGGO', 'disetujui', 'ekonomi', 3, 360000, '2025-11-17 07:37:06', '2025-11-17 07:37:06'),
(39, 23, 8, 'AFXZIN', 'disetujui', 'first class', 3, 360000, '2025-11-17 07:40:16', '2025-11-17 07:40:16');

-- --------------------------------------------------------

--
-- Table structure for table `expenses`
--

CREATE TABLE `expenses` (
  `id` int NOT NULL,
  `deskripsi` varchar(255) NOT NULL,
  `jumlah` decimal(12,2) NOT NULL,
  `tanggal_pengeluaran` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `expenses`
--

INSERT INTO `expenses` (`id`, `deskripsi`, `jumlah`, `tanggal_pengeluaran`, `created_at`) VALUES
(1, 'Gaji Karyawan', '5000000.00', '2025-11-01', '2025-11-13 01:02:48'),
(2, 'Biaya Pemeliharaan Kantor', '750000.00', '2025-11-05', '2025-11-13 01:02:48'),
(3, 'Biaya Iklan November', '1500000.00', '2025-11-10', '2025-11-13 01:02:48'),
(4, 'Biaya Promosi Oktober', '2000000.00', '2025-10-15', '2025-11-13 01:02:48');

-- --------------------------------------------------------

--
-- Table structure for table `pesawat`
--

CREATE TABLE `pesawat` (
  `id` int NOT NULL,
  `nama` varchar(50) NOT NULL,
  `no_penerbangan` varchar(50) NOT NULL,
  `asal` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `tujuan` varchar(50) NOT NULL,
  `tanggal_berangkat` date DEFAULT NULL,
  `waktu_berangkat` time DEFAULT NULL,
  `harga` decimal(12,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `waktu_tiba` time DEFAULT NULL,
  `kursi_tersedia` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `pesawat`
--

INSERT INTO `pesawat` (`id`, `nama`, `no_penerbangan`, `asal`, `tujuan`, `tanggal_berangkat`, `waktu_berangkat`, `harga`, `created_at`, `updated_at`, `waktu_tiba`, `kursi_tersedia`) VALUES
(22, 'Boeing ', 'BN-347', 'jakarta', 'padang', '2025-11-21', '14:26:00', '120000.00', '2025-11-17 07:27:06', '2025-11-17 07:27:06', '19:26:00', 18),
(23, 'sriwijaya air', 'BN-347', 'jakarta', 'bandung', '2025-12-01', '14:40:00', '120000.00', '2025-11-17 07:39:54', '2025-11-17 07:39:54', '20:45:00', 20);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `nama` varchar(50) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(50) NOT NULL,
  `no_hp` varchar(50) NOT NULL,
  `jenis_kelamin` enum('Laki-laki','Perempuan') NOT NULL,
  `tanggal_lahir` date NOT NULL,
  `alamat` text NOT NULL,
  `password` varchar(100) NOT NULL,
  `role` enum('admin','user') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT 'user',
  `remember_token` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `nama`, `username`, `email`, `no_hp`, `jenis_kelamin`, `tanggal_lahir`, `alamat`, `password`, `role`, `remember_token`) VALUES
(6, 'Admin', 'admin', 'admin@gmail.com', '08572346789', 'Laki-laki', '2018-06-28', 'Rongga', '21232f297a57a5a743894a0e4a801fc3', 'admin', '1f453891-7515-4fff-870f-d5651fe20a0e'),
(8, 'kii', 'kii', 'kii@gmail.com', '3434554', 'Laki-laki', '2025-10-01', 'bunijaya', 'dde127dd9191cac4bf1837e9b66f1513', 'user', '33d4fb6b-17b7-48bc-8a9f-9eb3f204ff74'),
(12, 'mufly', 'mufly', 'kii@gmail.com', '3434555', 'Laki-laki', '2025-10-20', 'gununghalu', 'be6446fa0b2c8a142cc79010cd6c408b', 'user', 'abb2c74b-3350-43b9-85ce-b26d0d0f7129');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `kode` (`kode`),
  ADD KEY `id_hotel` (`id_pesawat`),
  ADD KEY `id_user` (`id_user`);

--
-- Indexes for table `expenses`
--
ALTER TABLE `expenses`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pesawat`
--
ALTER TABLE `pesawat`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT for table `expenses`
--
ALTER TABLE `expenses`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `pesawat`
--
ALTER TABLE `pesawat`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `id_hotel` FOREIGN KEY (`id_pesawat`) REFERENCES `pesawat` (`id`),
  ADD CONSTRAINT `id_user` FOREIGN KEY (`id_user`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
