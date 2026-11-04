-- Database Mini Digital Library (Rak Baca)
CREATE DATABASE IF NOT EXISTS `db_perpustakaan` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `db_perpustakaan`;

DROP TABLE IF EXISTS `buku`;
DROP TABLE IF EXISTS `users`;

CREATE TABLE `buku` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `judul` varchar(150) NOT NULL,
  `kategori` varchar(100) NOT NULL,
  `penulis` varchar(100) NOT NULL,
  `tahun_terbit` year(4) NOT NULL,
  `stok` int(11) NOT NULL DEFAULT 0,
  `sampul` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `users` (`id`, `username`, `password`) VALUES
(1, 'admin', '$2y$10$z0KM9t50itDPsMxqWxFyaOXFT2QBM6WwgelS.c9HHWJQdHlkvrsAe');

INSERT INTO `buku` (`id`, `judul`, `kategori`, `penulis`, `tahun_terbit`, `stok`, `sampul`) VALUES
(1, 'Laskar Pelangi', 'Novel', 'Andrea Hirata', 2005, 5, NULL),
(2, 'Filosofi Teras', 'Pengembangan Diri', 'Henry Manampiring', 2018, 0, NULL),
(3, 'Sistem Basis Data', 'Teknologi', 'Abdul Kadir', 2018, 2, NULL);
