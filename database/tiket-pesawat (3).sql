-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Waktu pembuatan: 10 Nov 2025 pada 07.01
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
-- Struktur dari tabel `bookings`
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
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data untuk tabel `bookings`
--

INSERT INTO `bookings` (`id`, `id_pesawat`, `id_user`, `kode`, `status`, `kelas`, `jumlah`, `total_harga`, `created_at`) VALUES
(23, 3, 8, 'NLXUCU', 'disetujui', 'ekonomi', 1, 0, '2025-11-10 06:45:37'),
(24, 13, 8, 'ZELCSA', 'disetujui', 'ekonomi', 1, 0, '2025-11-10 06:45:37'),
(25, 12, 8, 'TTQKUQ', 'disetujui', 'ekonomi', 1, 0, '2025-11-10 06:45:37'),
(26, 13, 10, 'IUUOKZ', 'disetujui', 'ekonomi', 1, 0, '2025-11-10 06:47:40'),
(27, 12, 10, 'OJSJDC', 'disetujui', 'bisnis', 1, 0, '2025-11-10 06:49:46');

-- --------------------------------------------------------

--
-- Struktur dari tabel `pesawat`
--

CREATE TABLE `pesawat` (
  `id` int NOT NULL,
  `nama` varchar(50) NOT NULL,
  `no_penerbangan` varchar(50) NOT NULL,
  `asal` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `tujuan` varchar(50) NOT NULL,
  `waktu_berangkat` time DEFAULT NULL,
  `harga` decimal(12,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `waktu_tiba` time DEFAULT NULL,
  `kursi_tersedia` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data untuk tabel `pesawat`
--

INSERT INTO `pesawat` (`id`, `nama`, `no_penerbangan`, `asal`, `tujuan`, `waktu_berangkat`, `harga`, `created_at`, `updated_at`, `waktu_tiba`, `kursi_tersedia`) VALUES
(3, 'Boeing 423423423', 'BN-342', 'malaysia', 'Jakarta', '10:19:33', 120000.00, '2025-09-13 03:20:26', '2025-09-13 03:20:26', '10:36:33', 13),
(12, 'garuda indonesia', 'GA-001', 'jakarta', 'singapura', '08:30:00', 1200000.00, '2025-11-10 01:23:54', '2025-11-10 01:23:54', '13:30:00', 16),
(13, 'garuda indonesia', 'GA-2267', 'bandung', 'Jakarta', '09:30:00', 120000.00, '2025-11-10 01:26:00', '2025-11-10 01:26:00', '12:30:00', 13);

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
(6, 'Admin', 'admin', 'admin@gmail.com', '08572346789', 'Laki-laki', '2018-06-28', 'Rongga', '21232f297a57a5a743894a0e4a801fc3', 'admin', 'f961ab7f-f63f-4655-8358-9f1806e67deb'),
(8, 'kii', 'kii', 'kii@gmail.com', '3434554', 'Laki-laki', '2025-10-01', 'bunijaya', 'dde127dd9191cac4bf1837e9b66f1513', 'user', 'd0016ba5-cd7c-4a5c-a4a7-5571ee46e5b3'),
(10, 'mufly', 'mufly', 'kii@gmail.com', '3434554', 'Laki-laki', '2017-07-06', 'sadsfef', 'be6446fa0b2c8a142cc79010cd6c408b', 'user', '15471d49-6b2a-4376-b793-ad274de977c3');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `bookings`
--
ALTER TABLE `bookings`
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
-- AUTO_INCREMENT untuk tabel `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT untuk tabel `pesawat`
--
ALTER TABLE `pesawat`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `id_hotel` FOREIGN KEY (`id_pesawat`) REFERENCES `pesawat` (`id`),
  ADD CONSTRAINT `id_user` FOREIGN KEY (`id_user`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
