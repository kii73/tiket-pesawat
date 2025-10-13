-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Waktu pembuatan: 13 Okt 2025 pada 04.17
-- Versi server: 8.0.30
-- Versi PHP: 8.1.10

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
-- Struktur dari tabel `kode`
--

CREATE TABLE `kode` (
  `id` int NOT NULL,
  `id_pesawat` int NOT NULL,
  `id_user` int NOT NULL,
  `kode` varchar(50) NOT NULL,
  `status` enum('disetujui','menunggu') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data untuk tabel `kode`
--

INSERT INTO `kode` (`id`, `id_pesawat`, `id_user`, `kode`, `status`) VALUES
(10, 3, 8, 'PEVPVC', 'disetujui'),
(11, 4, 8, 'AFHVCF', 'disetujui'),
(12, 8, 8, 'RQSFFU', 'disetujui');

-- --------------------------------------------------------

--
-- Struktur dari tabel `pesawat`
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
-- Dumping data untuk tabel `pesawat`
--

INSERT INTO `pesawat` (`id`, `slug`, `nama`, `no_penerbangan`, `kelas`, `asal`, `tujuan`, `waktu_berangkat`, `harga`, `created_at`, `updated_at`, `waktu_tiba`) VALUES
(3, 'boeing-0-bn-342', 'Boeing 447', 'BN-342', 'Ekonomi', 'malaysia', 'Jakarta', '10:19:33', 120000.00, '2025-09-13 03:20:26', '2025-09-13 03:20:26', '10:36:33'),
(4, 'garuda-indonesia-ga-001', 'Garuda Indonesia', 'GA-001', 'Ekonomi', 'Bandung', 'singapura', '10:19:33', 1200000.00, '2025-09-13 03:20:26', '2025-09-13 03:20:26', '10:36:33'),
(7, 'air-indonesia-345542323', 'air indonesia', 'GS-367', 'Ekonomi', 'bandung', 'bali', '14:25:00', 2000000.00, '2025-10-07 07:24:52', '2025-10-07 07:24:52', '18:30:00'),
(8, 'garuda-indonesia-23846786', 'garuda indonesia', 'GA-2267', 'Business', 'jakarta', 'singapura', '10:22:00', 200000000.00, '2025-10-13 01:22:51', '2025-10-13 01:22:51', '05:22:00'),
(9, 'sriwijaya-air-sa-376', 'Sriwijaya air', 'SA-376', 'Business', 'tanggerang', 'Padang', '10:00:00', 200000000.00, '2025-10-13 02:56:50', '2025-10-13 02:56:50', '03:00:00');

--
-- Trigger `pesawat`
--
DELIMITER $$
CREATE TRIGGER `auto_slug` BEFORE INSERT ON `pesawat` FOR EACH ROW BEGIN
    SET NEW.slug = LOWER(CONCAT(REPLACE(NEW.nama, ' ', '-'), '-', NEW.no_penerbangan));
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
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
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `nama`, `username`, `email`, `no_hp`, `jenis_kelamin`, `tanggal_lahir`, `alamat`, `password`, `role`, `remember_token`) VALUES
(6, 'Admin', 'admin', 'admin@gmail.com', '08572346789', 'Laki-laki', '2018-06-28', 'Rongga', '21232f297a57a5a743894a0e4a801fc3', 'admin', '9df93c70-477a-44d2-b0f3-775374a694b1'),
(8, 'kii', 'kii', 'kii@gmail.com', '3434554', 'Laki-laki', '2025-10-01', 'bunijaya', 'dde127dd9191cac4bf1837e9b66f1513', 'user', '9794b035-b570-475a-92a2-270fa3d21ab7');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `kode`
--
ALTER TABLE `kode`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `kode` (`kode`),
  ADD KEY `id_hotel` (`id_pesawat`),
  ADD KEY `id_user` (`id_user`);

--
-- Indeks untuk tabel `pesawat`
--
ALTER TABLE `pesawat`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `kode`
--
ALTER TABLE `kode`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT untuk tabel `pesawat`
--
ALTER TABLE `pesawat`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `kode`
--
ALTER TABLE `kode`
  ADD CONSTRAINT `id_hotel` FOREIGN KEY (`id_pesawat`) REFERENCES `pesawat` (`id`),
  ADD CONSTRAINT `id_user` FOREIGN KEY (`id_user`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
