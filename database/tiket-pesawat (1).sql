-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Oct 21, 2025 at 07:37 AM
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
-- Table structure for table `kode`
--

CREATE TABLE `kode` (
  `id` int NOT NULL,
  `id_pesawat` int NOT NULL,
  `id_user` int NOT NULL,
  `kode` varchar(50) NOT NULL,
  `status` enum('disetujui','menunggu') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `kode`
--

INSERT INTO `kode` (`id`, `id_pesawat`, `id_user`, `kode`, `status`) VALUES
(10, 3, 8, 'PEVPVC', 'disetujui'),
(11, 4, 8, 'AFHVCF', 'disetujui'),
(12, 8, 8, 'RQSFFU', 'disetujui'),
(13, 7, 9, 'BZUBMG', 'disetujui'),
(14, 3, 9, 'UPUZKR', 'menunggu'),
(15, 7, 8, 'PDQGUG', 'menunggu'),
(16, 9, 8, 'DSKRXO', 'menunggu'),
(18, 11, 8, 'RSAGSQ', 'menunggu');

-- --------------------------------------------------------

--
-- Table structure for table `pesawat`
--

CREATE TABLE `pesawat` (
  `id` int NOT NULL,
  `slug` varchar(100) NOT NULL,
  `nama` varchar(50) NOT NULL,
  `no_penerbangan` varchar(50) NOT NULL,
  `kelas` varchar(50) NOT NULL,
  `asal` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `tujuan` varchar(50) NOT NULL,
  `waktu_berangkat` time DEFAULT NULL,
  `harga` decimal(12,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `waktu_tiba` time DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `pesawat`
--

INSERT INTO `pesawat` (`id`, `slug`, `nama`, `no_penerbangan`, `kelas`, `asal`, `tujuan`, `waktu_berangkat`, `harga`, `created_at`, `updated_at`, `waktu_tiba`) VALUES
(3, 'boeing-0-bn-342', 'Boeing 447', 'BN-342', 'Ekonomi', 'malaysia', 'Jakarta', '10:19:33', '120000.00', '2025-09-13 03:20:26', '2025-09-13 03:20:26', '10:36:33'),
(4, 'garuda-indonesia-ga-001', 'Garuda Indonesia', 'GA-001', 'Ekonomi', 'Bandung', 'singapura', '10:19:33', '1200000.00', '2025-09-13 03:20:26', '2025-09-13 03:20:26', '10:36:33'),
(7, 'air-indonesia-345542323', 'air indonesia', 'GS-367', 'Ekonomi', 'bandung', 'bali', '14:25:00', '2000000.00', '2025-10-07 07:24:52', '2025-10-07 07:24:52', '18:30:00'),
(8, 'garuda-indonesia-23846786', 'garuda indonesia', 'GA-2267', 'Business', 'jakarta', 'singapura', '10:22:00', '200000000.00', '2025-10-13 01:22:51', '2025-10-13 01:22:51', '05:22:00'),
(9, 'sriwijaya-air-sa-376', 'Sriwijaya Air', 'SA-376', 'First Class', 'tanggerang', 'Padang', '10:00:00', '200000000.00', '2025-10-13 02:56:50', '2025-10-13 02:56:50', '03:00:00'),
(11, 'singapura-air-sa-376', 'Singapura air', 'SA-376', 'Ekonomi', 'jakarta', 'Padang', '20:00:00', '2000000.00', '2025-10-21 07:32:53', '2025-10-21 07:32:53', '23:00:00');

--
-- Triggers `pesawat`
--
DELIMITER $$
CREATE TRIGGER `auto_slug` BEFORE INSERT ON `pesawat` FOR EACH ROW BEGIN
    SET NEW.slug = LOWER(CONCAT(REPLACE(NEW.nama, ' ', '-'), '-', NEW.no_penerbangan));
END
$$
DELIMITER ;

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
(6, 'Admin', 'admin', 'admin@gmail.com', '08572346789', 'Laki-laki', '2018-06-28', 'Rongga', '21232f297a57a5a743894a0e4a801fc3', 'admin', '21601499-282e-421f-87b9-0a08b5f17f90'),
(8, 'kii', 'kii', 'kii@gmail.com', '3434554', 'Laki-laki', '2025-10-01', 'bunijaya', 'dde127dd9191cac4bf1837e9b66f1513', 'user', '261447fd-e7ab-4c65-b58a-0bcf9924b4cb'),
(9, 'Fahmi XD', 'fahmixd', 'fahmixd404@gmail.com', '085645645645', 'Laki-laki', '2025-10-20', 'gdfgdftrg', '202cb962ac59075b964b07152d234b70', 'user', 'e510e982-1609-44f7-812d-145a57422bd5');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `kode`
--
ALTER TABLE `kode`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `kode` (`kode`),
  ADD KEY `id_hotel` (`id_pesawat`),
  ADD KEY `id_user` (`id_user`);

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
-- AUTO_INCREMENT for table `kode`
--
ALTER TABLE `kode`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `pesawat`
--
ALTER TABLE `pesawat`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `kode`
--
ALTER TABLE `kode`
  ADD CONSTRAINT `id_hotel` FOREIGN KEY (`id_pesawat`) REFERENCES `pesawat` (`id`),
  ADD CONSTRAINT `id_user` FOREIGN KEY (`id_user`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
