-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Waktu pembuatan: 06 Okt 2025 pada 04.15
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
  `kode` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data untuk tabel `kode`
--

INSERT INTO `kode` (`id`, `id_pesawat`, `id_user`, `kode`) VALUES
(4, 4, 4, 'QWAWAQ'),
(5, 4, 2, 'TBWNHI'),
(6, 3, 2, 'RVYQXQ');

-- --------------------------------------------------------

--
-- Struktur dari tabel `penerbagan`
--

CREATE TABLE `penerbagan` (
  `id` int NOT NULL,
  `kode_penerbagan` int NOT NULL,
  `maskapai` varchar(100) NOT NULL,
  `bandara_asal` varchar(50) NOT NULL,
  `bandara_tujuan` varchar(50) NOT NULL,
  `tanggal_berangkat` date NOT NULL,
  `jam_berangkat` time NOT NULL,
  `tanggal_tiba` date NOT NULL,
  `jam_tiba` time NOT NULL,
  `durasi` varchar(50) NOT NULL,
  `harga_tiket` varchar(100) NOT NULL,
  `gambar` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data untuk tabel `penerbagan`
--

INSERT INTO `penerbagan` (`id`, `kode_penerbagan`, `maskapai`, `bandara_asal`, `bandara_tujuan`, `tanggal_berangkat`, `jam_berangkat`, `tanggal_tiba`, `jam_tiba`, `durasi`, `harga_tiket`, `gambar`) VALUES
(2, 232344, 'garuda indonesia', 'sukarno', 'burangrai', '2025-09-19', '11:52:00', '2025-09-17', '02:50:00', '2jam', '600000', ''),
(3, 23234, 'garuda indonesia', 'sukarno', 'burangrai', '2025-09-10', '11:55:00', '2025-09-08', '11:54:00', '2jam', '3444444', ''),
(4, 2323423, 'garuda indonesia', 'kartajati', 'husaein', '2025-10-08', '14:22:00', '2025-10-23', '16:44:00', '2jam', '600000', '');

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
  `waktu_berangkat` datetime NOT NULL,
  `harga` decimal(12,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `waktu_tiba` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data untuk tabel `pesawat`
--

INSERT INTO `pesawat` (`id`, `slug`, `nama`, `no_penerbangan`, `kelas`, `asal`, `tujuan`, `waktu_berangkat`, `harga`, `created_at`, `updated_at`, `waktu_tiba`) VALUES
(3, 'boeing-0-bn-342', 'Boeing', 'BN-342', 'Ekonomi', 'Bandung', 'Jakarta', '2025-09-13 10:19:33', 120000.00, '2025-09-13 03:20:26', '2025-09-13 03:20:26', '2025-09-13 10:36:33'),
(4, 'garuda-indonesia-ga-001', 'Garuda Indonesia', 'GA-001', 'Ekonomi', 'Bandung', 'Jakarta', '2025-09-13 10:19:33', 1200000.00, '2025-09-13 03:20:26', '2025-09-13 03:20:26', '2025-09-13 10:36:33');

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
(2, 'Fahmi XD', 'fahmixd', 'guesjis@gmail.com', '0847574545454', 'Laki-laki', '2025-09-25', 'Kp. CIlangari', '$2y$10$5sLTnUOkuwlVAdyyr4Yw0exLqBLCYsN6AorSnEhI1DsZdHpnryPA6', 'user', 'fcd97e02-e9e7-4bec-87eb-c4dd603829e9'),
(4, 'Mufly', 'mufly', 'ilham@gmail.com', '0847574545454', 'Laki-laki', '2025-09-22', 'Rongga', '$2y$10$Hc7sWnxi7IKmPKYzIqN/YeiLj6l4vEo/tXrav/Dp.OYlAt4aUA9Ye', 'user', '181eb37d-d0c4-40cb-a5ed-45e715e50ab4'),
(5, 'admin', 'admin', 'kii@gmail.com', '3434554', 'Laki-laki', '2025-10-01', 'dfgdfgg', '$2y$10$KHgGOuepkbrigbvuW/uUhOsmdLJzwfIJSHLBuB6xYkD8RhdSMiPaC', 'admin', 'edcad641-1b04-48ae-878d-793677f5d65d');

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
-- Indeks untuk tabel `penerbagan`
--
ALTER TABLE `penerbagan`
  ADD PRIMARY KEY (`id`);

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
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT untuk tabel `penerbagan`
--
ALTER TABLE `penerbagan`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT untuk tabel `pesawat`
--
ALTER TABLE `pesawat`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

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
