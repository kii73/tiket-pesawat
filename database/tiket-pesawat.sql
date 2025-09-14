-- phpMyAdmin SQL Dump
-- version 6.0.0-dev+20250829.99d1a9b7cd
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Sep 14, 2025 at 07:39 AM
-- Server version: 8.4.3
-- PHP Version: 8.3.16

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
  `kode` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `kode`
--

INSERT INTO `kode` (`id`, `id_pesawat`, `id_user`, `kode`) VALUES
(4, 4, 4, 'QWAWAQ');

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
  `waktu_berangkat` datetime NOT NULL,
  `harga` decimal(12,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `waktu_tiba` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `pesawat`
--

INSERT INTO `pesawat` (`id`, `slug`, `nama`, `no_penerbangan`, `kelas`, `asal`, `tujuan`, `waktu_berangkat`, `harga`, `created_at`, `updated_at`, `waktu_tiba`) VALUES
(3, 'boeing-0-bn-342', 'Boeing', 'BN-342', 'Ekonomi', 'Bandung', 'Jakarta', '2025-09-13 10:19:33', 120000.00, '2025-09-13 03:20:26', '2025-09-13 03:20:26', '2025-09-13 10:36:33'),
(4, 'garuda-indonesia-ga-001', 'Garuda Indonesia', 'GA-001', 'Ekonomi', 'Bandung', 'Jakarta', '2025-09-13 10:19:33', 1200000.00, '2025-09-13 03:20:26', '2025-09-13 03:20:26', '2025-09-13 10:36:33');

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
(1, 'Admin', 'admin', 'admin@gmail.com', '08827347897', 'Laki-laki', '2007-09-01', 'Rongga', '$2y$10$JrcU2.PS9Y1h26iVX8KPtebGaqjVafY7AYwk2qOYQMQoxCQCbxzMG', 'admin', '7af248ab-8f1c-4eba-849b-f9128411bdb4'),
(2, 'Fahmi XD', 'fahmixd', 'guesjis@gmail.com', '0847574545454', 'Laki-laki', '2025-09-25', 'Kp. CIlangari', '$2y$10$5sLTnUOkuwlVAdyyr4Yw0exLqBLCYsN6AorSnEhI1DsZdHpnryPA6', 'user', '489fc76c-5f91-4255-b6ac-b2efe08761df'),
(4, 'Mufly', 'mufly', 'ilham@gmail.com', '0847574545454', 'Laki-laki', '2025-09-22', 'Rongga', '$2y$10$Hc7sWnxi7IKmPKYzIqN/YeiLj6l4vEo/tXrav/Dp.OYlAt4aUA9Ye', 'user', '181eb37d-d0c4-40cb-a5ed-45e715e50ab4');

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
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `pesawat`
--
ALTER TABLE `pesawat`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

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
