-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 14, 2026 at 03:44 AM
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
-- Database: `grosir`
--

-- --------------------------------------------------------

--
-- Table structure for table `audit_log`
--

CREATE TABLE `audit_log` (
  `id_log` int(11) NOT NULL,
  `id_pengguna` int(11) DEFAULT NULL,
  `aksi` varchar(255) NOT NULL,
  `tabel_terkait` varchar(50) NOT NULL,
  `waktu` timestamp NOT NULL DEFAULT current_timestamp(),
  `ip_address` varchar(45) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `audit_log`
--

INSERT INTO `audit_log` (`id_log`, `id_pengguna`, `aksi`, `tabel_terkait`, `waktu`, `ip_address`) VALUES
(1, 1, 'Login gagal - Password salah', 'pengguna', '2026-07-11 08:45:19', '::1'),
(2, 1, 'Login gagal - Password salah', 'pengguna', '2026-07-11 08:45:33', '::1'),
(3, 1, 'Login gagal - Password salah', 'pengguna', '2026-07-11 09:27:56', '::1'),
(4, 1, 'Berhasil Login ke sistem', 'pengguna', '2026-07-11 09:28:02', '::1'),
(5, 1, 'Mengubah kategori id 2', 'kategori', '2026-07-11 15:06:25', '::1'),
(6, 1, 'Menambah record baru (Elektronik)', 'kategori', '2026-07-11 15:06:39', '::1'),
(7, 1, 'Menambah record baru (Sayuran)', 'kategori', '2026-07-11 15:07:08', '::1'),
(8, 1, 'Menambah record baru (Minuman Kaleng)', 'kategori', '2026-07-11 15:20:33', '::1'),
(9, 1, 'Buat produk baru: (Ayam Potong)', 'produk', '2026-07-11 15:31:12', '::1'),
(10, 1, 'Update produk SKU (PTG-1)', 'produk', '2026-07-11 15:31:22', '::1'),
(11, 1, 'Update produk SKU (PTG-23)', 'produk', '2026-07-11 15:31:35', '::1'),
(12, 1, 'Menginput Pembelian PO PO-20260711-173229', 'pembelian', '2026-07-11 15:32:48', '::1'),
(13, 1, 'Memverifikasi Stok Masuk dari Nota PO PO-20260711-173229', 'riwayat_stok', '2026-07-11 15:32:55', '::1'),
(14, 1, 'Transaksi Invoice Tercipta (INV/20260711/840819) total Rp.17,000', 'penjualan', '2026-07-11 15:34:31', '::1'),
(15, 1, 'Transaksi Invoice Tercipta (INV/20260711/453C45) total Rp.7,000', 'penjualan', '2026-07-11 15:49:05', '127.0.0.1'),
(16, 1, 'Mengubah kategori id 5', 'kategori', '2026-07-12 02:26:28', '::1'),
(17, 1, 'Mengubah kategori id 5', 'kategori', '2026-07-12 02:35:08', '::1'),
(18, 1, 'Menambah pengguna baru (cashier@gmail.com)', 'pengguna', '2026-07-12 02:38:05', '::1'),
(19, 1, 'Update Matriks Modul Role (ID 3)', 'hak_akses', '2026-07-12 02:39:02', '::1'),
(20, 1, 'Merubah Profil/Identitas Toko', 'konfigurasi', '2026-07-12 02:39:31', '::1'),
(21, 1, 'Merubah Profil/Identitas Toko', 'konfigurasi', '2026-07-12 02:45:13', '::1'),
(22, 1, 'Buat produk baru: (MIE Indomie)', 'produk', '2026-07-12 13:30:05', '::1'),
(23, 1, 'Menginput Pembelian PO PO-20260712-153012', 'pembelian', '2026-07-12 13:30:33', '::1'),
(24, 1, 'Memverifikasi Stok Masuk dari Nota PO PO-20260712-153012', 'riwayat_stok', '2026-07-12 13:30:49', '::1'),
(25, 1, 'Transaksi Invoice Tercipta (INV/20260712/D08D61) total Rp.220,000', 'penjualan', '2026-07-12 13:31:21', '::1'),
(26, 1, 'Menambahkan role baru (Supervisior)', 'role', '2026-07-12 13:33:02', '::1'),
(27, 1, 'Logout dari sistem', 'pengguna', '2026-07-12 13:48:06', '::1'),
(28, 2, 'Berhasil Login ke sistem', 'pengguna', '2026-07-12 13:48:12', '::1'),
(29, 2, 'Logout dari sistem', 'pengguna', '2026-07-12 14:47:31', '::1'),
(30, 1, 'Berhasil Login ke sistem', 'pengguna', '2026-07-12 14:47:49', '::1'),
(31, 1, 'Berhasil Login ke sistem', 'pengguna', '2026-07-12 15:00:59', '::1'),
(32, 1, 'Berhasil Login ke sistem', 'pengguna', '2026-07-12 15:15:31', '127.0.0.1'),
(33, 1, 'Update Matriks Modul Role (ID 2)', 'hak_akses', '2026-07-12 15:17:05', '127.0.0.1'),
(34, 1, 'Logout dari sistem', 'pengguna', '2026-07-12 15:17:23', '127.0.0.1'),
(35, 1, 'Berhasil Login ke sistem', 'pengguna', '2026-07-12 15:17:44', '127.0.0.1'),
(36, 1, 'Transaksi Invoice Tercipta (INV/20260712/3EEAAB) total Rp.17,000', 'penjualan', '2026-07-12 15:19:11', '127.0.0.1'),
(37, 1, 'Berhasil Login ke sistem', 'pengguna', '2026-07-12 15:20:29', '127.0.0.1'),
(38, 1, 'Berhasil Login ke sistem', 'pengguna', '2026-07-13 06:44:06', '127.0.0.1'),
(39, 1, 'Update Matriks Modul Role (ID 2)', 'hak_akses', '2026-07-13 06:48:25', '127.0.0.1'),
(40, 1, 'Update Matriks Modul Role (ID 2)', 'hak_akses', '2026-07-13 06:48:33', '127.0.0.1'),
(41, 1, 'Update Matriks Modul Role (ID 3)', 'hak_akses', '2026-07-13 06:51:06', '127.0.0.1'),
(42, 1, 'Update Matriks Modul Role (ID 4)', 'hak_akses', '2026-07-13 06:52:38', '127.0.0.1'),
(43, 1, 'Menambah pengguna baru (gudang@gmail.com)', 'pengguna', '2026-07-13 06:55:57', '127.0.0.1'),
(44, 1, 'Mengubah data pengguna (kasir@gmail.com)', 'pengguna', '2026-07-13 06:56:45', '127.0.0.1'),
(45, 1, 'Menambah pengguna baru (supervisor@gmail.com)', 'pengguna', '2026-07-13 06:57:37', '127.0.0.1'),
(46, 1, 'Merubah Profil/Identitas Toko', 'konfigurasi', '2026-07-13 06:58:10', '127.0.0.1'),
(47, 1, 'Berhasil Login ke sistem', 'pengguna', '2026-07-13 07:00:35', '127.0.0.1'),
(48, 1, 'Mengubah kategori id 5', 'kategori', '2026-07-13 08:16:19', '127.0.0.1'),
(49, 1, 'Mengubah kategori id 4', 'kategori', '2026-07-13 08:17:02', '127.0.0.1'),
(50, 1, 'Mengubah kategori id 4', 'kategori', '2026-07-13 08:17:16', '127.0.0.1'),
(51, 1, 'Mengubah kategori id 3', 'kategori', '2026-07-13 08:17:35', '127.0.0.1'),
(52, 1, 'Mengubah kategori id 4', 'kategori', '2026-07-13 08:17:59', '127.0.0.1'),
(53, 1, 'Mengubah kategori id 4', 'kategori', '2026-07-13 08:18:20', '127.0.0.1'),
(54, 1, 'Mengubah kategori id 2', 'kategori', '2026-07-13 08:19:17', '127.0.0.1'),
(55, 1, 'Mengubah kategori id 1', 'kategori', '2026-07-13 08:19:34', '127.0.0.1'),
(56, 1, 'Menambah record baru (Bahan kue dan roti)', 'kategori', '2026-07-13 08:19:51', '127.0.0.1'),
(57, 1, 'Menambah record baru (Rokok dan tembakau)', 'kategori', '2026-07-13 08:20:18', '127.0.0.1'),
(58, 1, 'Menambah record baru (Sabun dan perawatan diri)', 'kategori', '2026-07-13 08:20:46', '127.0.0.1'),
(59, 1, 'Menambah record baru (Kebutuhan rumah tangga)', 'kategori', '2026-07-13 08:21:16', '127.0.0.1'),
(60, 1, 'Menambah record baru (Sayuran dan bahan segar)', 'kategori', '2026-07-13 08:21:44', '127.0.0.1'),
(61, 1, 'Update produk SKU (SKU-GULA PASIR-1)', 'produk', '2026-07-13 08:27:03', '127.0.0.1'),
(62, 1, 'Update produk SKU (SKU-GULA PASIR-1)', 'produk', '2026-07-13 08:28:45', '127.0.0.1'),
(63, 1, 'Update produk SKU (SKU-BERAS-2)', 'produk', '2026-07-13 08:30:45', '127.0.0.1'),
(64, 1, 'Buat produk baru: (Minyak goreng)', 'produk', '2026-07-13 08:35:39', '127.0.0.1'),
(65, 1, 'Update produk SKU (SKU-MINYAK GORENG-3)', 'produk', '2026-07-13 08:36:03', '127.0.0.1'),
(66, 1, 'Update produk SKU (SKU-MINYAK GORENG-3)', 'produk', '2026-07-13 08:37:13', '127.0.0.1'),
(67, 1, 'Buat produk baru: (indomie)', 'produk', '2026-07-13 08:42:02', '127.0.0.1'),
(68, 1, 'Update produk SKU (SKU-MINYAK GORENG-3)', 'produk', '2026-07-13 08:44:06', '127.0.0.1'),
(69, 1, 'Update produk SKU (SKU-GULA PASIR-1)', 'produk', '2026-07-13 08:44:32', '127.0.0.1'),
(70, 1, 'Update produk SKU (SKU-MAKANAN INSTANT-4)', 'produk', '2026-07-13 08:44:54', '127.0.0.1'),
(71, 1, 'Update produk SKU (SKU-MINYAK GORENG-3)', 'produk', '2026-07-13 08:45:15', '127.0.0.1'),
(72, 1, 'Update produk SKU (SKU-GULA PASIR-1)', 'produk', '2026-07-13 08:45:54', '127.0.0.1'),
(73, 1, 'Buat produk baru: (Sarden kaleng)', 'produk', '2026-07-13 08:48:09', '127.0.0.1'),
(74, 1, 'Update produk SKU (SKU-SARDEN KALENG-5)', 'produk', '2026-07-13 08:48:31', '127.0.0.1'),
(75, 1, 'Buat produk baru: (Kornet)', 'produk', '2026-07-13 08:51:20', '127.0.0.1'),
(76, 1, 'Update produk SKU (SKU-KORNET-6)', 'produk', '2026-07-13 08:52:08', '127.0.0.1'),
(77, 1, 'Update produk SKU (SKU-BERAS-2)', 'produk', '2026-07-13 08:53:31', '127.0.0.1'),
(78, 1, 'Update produk SKU (SKU-BERAS-2)', 'produk', '2026-07-13 08:53:59', '127.0.0.1'),
(79, 1, 'Berhasil Login ke sistem', 'pengguna', '2026-07-14 00:50:31', '127.0.0.1'),
(80, 1, 'Mengubah data pengguna (kasir@gmail.com)', 'pengguna', '2026-07-14 00:51:23', '127.0.0.1');

-- --------------------------------------------------------

--
-- Table structure for table `detail_pembelian`
--

CREATE TABLE `detail_pembelian` (
  `id_detail` int(11) NOT NULL,
  `id_pembelian` int(11) NOT NULL,
  `id_produk` int(11) NOT NULL,
  `jumlah` int(11) NOT NULL,
  `harga_beli` decimal(15,2) NOT NULL,
  `subtotal` decimal(15,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `detail_pembelian`
--

INSERT INTO `detail_pembelian` (`id_detail`, `id_pembelian`, `id_produk`, `jumlah`, `harga_beli`, `subtotal`) VALUES
(1, 1, 1, 3, 14000.00, 42000.00),
(2, 2, 2, 10, 30000.00, 300000.00);

-- --------------------------------------------------------

--
-- Table structure for table `detail_penjualan`
--

CREATE TABLE `detail_penjualan` (
  `id_detail` int(11) NOT NULL,
  `id_penjualan` int(11) NOT NULL,
  `id_produk` int(11) NOT NULL,
  `jumlah` int(11) NOT NULL,
  `harga_jual` decimal(15,2) NOT NULL,
  `subtotal` decimal(15,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `detail_penjualan`
--

INSERT INTO `detail_penjualan` (`id_detail`, `id_penjualan`, `id_produk`, `jumlah`, `harga_jual`, `subtotal`) VALUES
(2, 2, 1, 1, 17000.00, 17000.00),
(3, 3, 1, 1, 7000.00, 7000.00),
(4, 4, 2, 5, 40000.00, 200000.00),
(5, 5, 1, 1, 17000.00, 17000.00);

-- --------------------------------------------------------

--
-- Table structure for table `hak_akses`
--

CREATE TABLE `hak_akses` (
  `id_hak_akses` int(11) NOT NULL,
  `id_role` int(11) NOT NULL,
  `nama_modul` varchar(50) NOT NULL,
  `akses_lihat` tinyint(1) DEFAULT 0,
  `akses_tambah` tinyint(1) DEFAULT 0,
  `akses_ubah` tinyint(1) DEFAULT 0,
  `akses_hapus` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `hak_akses`
--

INSERT INTO `hak_akses` (`id_hak_akses`, `id_role`, `nama_modul`, `akses_lihat`, `akses_tambah`, `akses_ubah`, `akses_hapus`) VALUES
(13, 5, 'dashboard', 1, 1, 1, 1),
(14, 5, 'pengguna', 1, 1, 1, 1),
(15, 5, 'hak_akses', 1, 1, 1, 1),
(16, 5, 'kategori', 1, 1, 1, 1),
(17, 5, 'produk', 1, 1, 1, 1),
(18, 5, 'supplier', 1, 1, 1, 1),
(19, 5, 'pelanggan', 1, 1, 1, 1),
(20, 5, 'pembelian', 1, 1, 1, 1),
(21, 5, 'penjualan', 1, 1, 1, 1),
(22, 5, 'gudang', 1, 1, 1, 1),
(23, 5, 'laporan', 1, 1, 1, 1),
(24, 5, 'setting', 1, 1, 1, 1),
(25, 1, 'dashboard', 1, 1, 1, 1),
(26, 1, 'pengguna', 1, 1, 1, 1),
(27, 1, 'hak_akses', 1, 1, 1, 1),
(28, 1, 'kategori', 1, 1, 1, 1),
(29, 1, 'produk', 1, 1, 1, 1),
(30, 1, 'supplier', 1, 1, 1, 1),
(31, 1, 'pelanggan', 1, 1, 1, 1),
(32, 1, 'pembelian', 1, 1, 1, 1),
(33, 1, 'penjualan', 1, 1, 1, 1),
(34, 1, 'gudang', 1, 1, 1, 1),
(35, 1, 'laporan', 1, 1, 1, 1),
(36, 1, 'setting', 1, 1, 1, 1),
(61, 2, 'dashboard', 1, 1, 1, 1),
(62, 2, 'pengguna', 0, 0, 0, 0),
(63, 2, 'hak_akses', 0, 0, 0, 0),
(64, 2, 'kategori', 1, 0, 0, 0),
(65, 2, 'produk', 1, 1, 1, 0),
(66, 2, 'supplier', 1, 1, 1, 0),
(67, 2, 'pelanggan', 1, 0, 0, 0),
(68, 2, 'pembelian', 1, 1, 1, 0),
(69, 2, 'penjualan', 0, 0, 0, 0),
(70, 2, 'gudang', 1, 1, 1, 0),
(71, 2, 'laporan', 1, 0, 0, 0),
(72, 2, 'setting', 0, 0, 0, 0),
(73, 3, 'dashboard', 1, 1, 1, 1),
(74, 3, 'pengguna', 0, 0, 0, 0),
(75, 3, 'hak_akses', 0, 0, 0, 0),
(76, 3, 'kategori', 1, 0, 0, 0),
(77, 3, 'produk', 1, 0, 0, 0),
(78, 3, 'supplier', 0, 0, 0, 0),
(79, 3, 'pelanggan', 1, 1, 1, 0),
(80, 3, 'pembelian', 1, 1, 1, 1),
(81, 3, 'penjualan', 1, 1, 1, 0),
(82, 3, 'gudang', 0, 0, 0, 0),
(83, 3, 'laporan', 0, 0, 0, 0),
(84, 3, 'setting', 0, 0, 0, 0),
(85, 4, 'dashboard', 1, 1, 1, 1),
(86, 4, 'pengguna', 1, 0, 0, 0),
(87, 4, 'hak_akses', 0, 0, 0, 0),
(88, 4, 'kategori', 1, 0, 0, 0),
(89, 4, 'produk', 1, 0, 0, 0),
(90, 4, 'supplier', 1, 0, 0, 0),
(91, 4, 'pelanggan', 1, 0, 0, 0),
(92, 4, 'pembelian', 1, 0, 0, 0),
(93, 4, 'penjualan', 1, 0, 0, 0),
(94, 4, 'gudang', 1, 0, 0, 0),
(95, 4, 'laporan', 1, 0, 0, 0),
(96, 4, 'setting', 0, 0, 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `kategori`
--

CREATE TABLE `kategori` (
  `id_kategori` int(11) NOT NULL,
  `nama_kategori` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `kategori`
--

INSERT INTO `kategori` (`id_kategori`, `nama_kategori`) VALUES
(6, 'Bahan kue dan roti'),
(4, 'Bumbu dapur'),
(9, 'Kebutuhan rumah tangga'),
(2, 'Makanan instant'),
(3, 'Minuman'),
(7, 'Rokok dan tembakau'),
(8, 'Sabun dan perawatan diri'),
(10, 'Sayuran dan bahan segar'),
(5, 'Sembako'),
(1, 'Snack dan makanan ringan');

-- --------------------------------------------------------

--
-- Table structure for table `konfigurasi`
--

CREATE TABLE `konfigurasi` (
  `id_konfig` int(11) NOT NULL,
  `nama_toko` varchar(100) DEFAULT NULL,
  `alamat` varchar(255) DEFAULT NULL,
  `telepon` varchar(20) DEFAULT NULL,
  `logo` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `konfigurasi`
--

INSERT INTO `konfigurasi` (`id_konfig`, `nama_toko`, `alamat`, `telepon`, `logo`) VALUES
(1, 'Grosir Bali', 'Jl. Bali Renon - Denpasar', '0812-3214-9988', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `pelanggan`
--

CREATE TABLE `pelanggan` (
  `id_pelanggan` int(11) NOT NULL,
  `nama_pelanggan` varchar(100) NOT NULL,
  `no_telp` varchar(20) DEFAULT NULL,
  `alamat` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pelanggan`
--

INSERT INTO `pelanggan` (`id_pelanggan`, `nama_pelanggan`, `no_telp`, `alamat`) VALUES
(1, 'Maureen', '08232332211', 'Renon Bali');

-- --------------------------------------------------------

--
-- Table structure for table `pembelian`
--

CREATE TABLE `pembelian` (
  `id_pembelian` int(11) NOT NULL,
  `no_nota` varchar(50) NOT NULL,
  `id_supplier` int(11) DEFAULT NULL,
  `id_pengguna` int(11) DEFAULT NULL,
  `tanggal` date NOT NULL,
  `total_harga` decimal(15,2) DEFAULT 0.00,
  `status_approval` enum('pending','approved','rejected') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pembelian`
--

INSERT INTO `pembelian` (`id_pembelian`, `no_nota`, `id_supplier`, `id_pengguna`, `tanggal`, `total_harga`, `status_approval`, `created_at`) VALUES
(1, 'PO-20260711-173229', 1, 1, '2026-07-11', 42000.00, 'approved', '2026-07-11 15:32:48'),
(2, 'PO-20260712-153012', 1, 1, '2026-07-12', 300000.00, 'approved', '2026-07-12 13:30:33');

-- --------------------------------------------------------

--
-- Table structure for table `pengguna`
--

CREATE TABLE `pengguna` (
  `id_pengguna` int(11) NOT NULL,
  `nama_lengkap` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `id_role` int(11) NOT NULL,
  `status` enum('aktif','nonaktif') DEFAULT 'aktif',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pengguna`
--

INSERT INTO `pengguna` (`id_pengguna`, `nama_lengkap`, `email`, `password`, `id_role`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Super Administrator', 'admin@wpms.local', '$2y$10$xaXazw6Ram6pjXHRns4EDOKgI4rXH/L1AjVm6ARYnswzYGt0eNRLK', 1, 'aktif', '2026-07-11 08:36:19', '2026-07-11 08:47:13'),
(2, 'Kasir', 'kasir@gmail.com', '$2y$10$wyes60zpUj9/l1vY8.eVJuRQiRThDquawA37YyvxlhlQwYiX2uJou', 1, 'aktif', '2026-07-12 02:38:05', '2026-07-14 00:51:23'),
(3, 'Admin gudang', 'gudang@gmail.com', '$2y$10$R0.wGBu8j35Oj8g.rZGWGOM.9M3hdmo8So70RsBWcA72xi1GKKRla', 2, 'aktif', '2026-07-13 06:55:57', '2026-07-13 06:55:57'),
(4, 'Supervisor', 'supervisor@gmail.com', '$2y$10$kJr4l9hjLFjkymA3ClnyM.WcF72yfC6A6mmJqAHPG6PGhJwlH942K', 4, 'aktif', '2026-07-13 06:57:37', '2026-07-13 06:57:37');

-- --------------------------------------------------------

--
-- Table structure for table `penjualan`
--

CREATE TABLE `penjualan` (
  `id_penjualan` int(11) NOT NULL,
  `no_invoice` varchar(50) NOT NULL,
  `id_pelanggan` int(11) DEFAULT NULL,
  `id_pengguna` int(11) DEFAULT NULL,
  `tanggal` timestamp NOT NULL DEFAULT current_timestamp(),
  `subtotal` decimal(15,2) DEFAULT 0.00,
  `diskon` decimal(15,2) DEFAULT 0.00,
  `pajak` decimal(15,2) DEFAULT 0.00,
  `total_bayar` decimal(15,2) DEFAULT 0.00,
  `uang_bayar` decimal(15,2) DEFAULT 0.00,
  `kembalian` decimal(15,2) DEFAULT 0.00,
  `metode_bayar` varchar(50) DEFAULT 'tunai'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `penjualan`
--

INSERT INTO `penjualan` (`id_penjualan`, `no_invoice`, `id_pelanggan`, `id_pengguna`, `tanggal`, `subtotal`, `diskon`, `pajak`, `total_bayar`, `uang_bayar`, `kembalian`, `metode_bayar`) VALUES
(2, 'INV/20260711/840819', NULL, 1, '2026-07-11 15:34:31', 17000.00, 0.00, 0.00, 17000.00, 20000.00, 3000.00, 'tunai'),
(3, 'INV/20260711/453C45', NULL, 1, '2026-07-11 15:49:05', 7000.00, 0.00, 0.00, 7000.00, 10000.00, 3000.00, 'tunai'),
(4, 'INV/20260712/D08D61', NULL, 1, '2026-07-12 13:31:21', 200000.00, 0.00, 20000.00, 220000.00, 300000.00, 80000.00, 'tunai'),
(5, 'INV/20260712/3EEAAB', NULL, 1, '2026-07-12 15:19:11', 17000.00, 0.00, 0.00, 17000.00, 20000.00, 3000.00, 'tunai');

-- --------------------------------------------------------

--
-- Table structure for table `produk`
--

CREATE TABLE `produk` (
  `id_produk` int(11) NOT NULL,
  `id_kategori` int(11) DEFAULT NULL,
  `sku` varchar(50) DEFAULT NULL,
  `barcode` varchar(100) DEFAULT NULL,
  `nama_produk` varchar(150) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `harga_beli` decimal(15,2) DEFAULT 0.00,
  `harga_jual` decimal(15,2) DEFAULT 0.00,
  `stok` int(11) DEFAULT 0,
  `gambar` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `produk`
--

INSERT INTO `produk` (`id_produk`, `id_kategori`, `sku`, `barcode`, `nama_produk`, `deskripsi`, `harga_beli`, `harga_jual`, `stok`, `gambar`) VALUES
(1, 5, 'SKU-BERAS-2', '10101021', 'Beras', 'Beras eceran', 150000.00, 17100.00, 0, 'prod_6a54a7a758c8b4.09353511.jpg'),
(2, 5, 'SKU-GULA PASIR-1', '121231231', 'Gula pasir', 'Gula pasir lokal', 180000.00, 19050.00, 5, 'prod_6a54a5c2b402f5.36636743.jpg'),
(3, 5, 'SKU-MINYAK GORENG-3', '101057789', 'Minyak goreng', 'Minyak goreng', 400000.00, 21000.00, 0, 'prod_6a54a59b8365c2.39441301.jpg'),
(4, 2, 'SKU-MAKANAN INSTANT-4', '181822561', 'indomie', 'Indomie', 70000.00, 3000.00, 0, 'prod_6a54a586d5a706.08899046.png'),
(5, 2, 'SKU-SARDEN KALENG-5', '171744123', 'Sarden kaleng', 'sarden kaleng', 300000.00, 15000.00, 0, 'prod_6a54a65f02ad29.37611851.jpg'),
(6, 2, 'SKU-KORNET-6', '151533456', 'Kornet', 'kornet', 250000.00, 20000.00, 0, 'prod_6a54a73827eb82.67634261.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `riwayat_stok`
--

CREATE TABLE `riwayat_stok` (
  `id_riwayat` int(11) NOT NULL,
  `id_produk` int(11) NOT NULL,
  `jenis` enum('masuk','keluar') NOT NULL,
  `jumlah` int(11) NOT NULL,
  `keterangan` varchar(255) DEFAULT NULL,
  `tanggal` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `riwayat_stok`
--

INSERT INTO `riwayat_stok` (`id_riwayat`, `id_produk`, `jenis`, `jumlah`, `keterangan`, `tanggal`) VALUES
(1, 1, 'masuk', 3, 'Penerimaan PO Inbound Rekod Nota : PO-20260711-173229', '2026-07-11 15:32:55'),
(2, 1, 'keluar', 1, 'Penjualan Eceran/Partai POS No. Nota: INV/20260711/840819', '2026-07-11 15:34:31'),
(3, 1, 'keluar', 1, 'Penjualan Eceran/Partai POS No. Nota: INV/20260711/453C45', '2026-07-11 15:49:05'),
(4, 2, 'masuk', 10, 'Penerimaan PO Inbound Rekod Nota : PO-20260712-153012', '2026-07-12 13:30:49'),
(5, 2, 'keluar', 5, 'Penjualan Eceran/Partai POS No. Nota: INV/20260712/D08D61', '2026-07-12 13:31:21'),
(6, 1, 'keluar', 1, 'Penjualan Eceran/Partai POS No. Nota: INV/20260712/3EEAAB', '2026-07-12 15:19:11');

-- --------------------------------------------------------

--
-- Table structure for table `role`
--

CREATE TABLE `role` (
  `id_role` int(11) NOT NULL,
  `nama_role` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `role`
--

INSERT INTO `role` (`id_role`, `nama_role`) VALUES
(1, 'Super Admin'),
(2, 'Admin Gudang'),
(3, 'Kasir'),
(4, 'Supervisior'),
(5, 'Superadmin');

-- --------------------------------------------------------

--
-- Table structure for table `supplier`
--

CREATE TABLE `supplier` (
  `id_supplier` int(11) NOT NULL,
  `nama_supplier` varchar(100) NOT NULL,
  `no_telp` varchar(20) DEFAULT NULL,
  `alamat` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `supplier`
--

INSERT INTO `supplier` (`id_supplier`, `nama_supplier`, `no_telp`, `alamat`) VALUES
(1, 'Yogik', '08912341234', 'Renon'),
(2, 'Zora Diva', '089512223123', 'Jambi');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `audit_log`
--
ALTER TABLE `audit_log`
  ADD PRIMARY KEY (`id_log`),
  ADD KEY `id_pengguna` (`id_pengguna`);

--
-- Indexes for table `detail_pembelian`
--
ALTER TABLE `detail_pembelian`
  ADD PRIMARY KEY (`id_detail`),
  ADD KEY `id_pembelian` (`id_pembelian`),
  ADD KEY `id_produk` (`id_produk`);

--
-- Indexes for table `detail_penjualan`
--
ALTER TABLE `detail_penjualan`
  ADD PRIMARY KEY (`id_detail`),
  ADD KEY `id_penjualan` (`id_penjualan`),
  ADD KEY `id_produk` (`id_produk`);

--
-- Indexes for table `hak_akses`
--
ALTER TABLE `hak_akses`
  ADD PRIMARY KEY (`id_hak_akses`),
  ADD KEY `id_role` (`id_role`);

--
-- Indexes for table `kategori`
--
ALTER TABLE `kategori`
  ADD PRIMARY KEY (`id_kategori`),
  ADD UNIQUE KEY `nama_kategori` (`nama_kategori`);

--
-- Indexes for table `konfigurasi`
--
ALTER TABLE `konfigurasi`
  ADD PRIMARY KEY (`id_konfig`);

--
-- Indexes for table `pelanggan`
--
ALTER TABLE `pelanggan`
  ADD PRIMARY KEY (`id_pelanggan`);

--
-- Indexes for table `pembelian`
--
ALTER TABLE `pembelian`
  ADD PRIMARY KEY (`id_pembelian`),
  ADD UNIQUE KEY `no_nota` (`no_nota`),
  ADD KEY `id_supplier` (`id_supplier`),
  ADD KEY `id_pengguna` (`id_pengguna`);

--
-- Indexes for table `pengguna`
--
ALTER TABLE `pengguna`
  ADD PRIMARY KEY (`id_pengguna`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `id_role` (`id_role`);

--
-- Indexes for table `penjualan`
--
ALTER TABLE `penjualan`
  ADD PRIMARY KEY (`id_penjualan`),
  ADD UNIQUE KEY `no_invoice` (`no_invoice`),
  ADD KEY `id_pelanggan` (`id_pelanggan`),
  ADD KEY `id_pengguna` (`id_pengguna`);

--
-- Indexes for table `produk`
--
ALTER TABLE `produk`
  ADD PRIMARY KEY (`id_produk`),
  ADD UNIQUE KEY `sku` (`sku`),
  ADD UNIQUE KEY `barcode` (`barcode`),
  ADD KEY `id_kategori` (`id_kategori`);

--
-- Indexes for table `riwayat_stok`
--
ALTER TABLE `riwayat_stok`
  ADD PRIMARY KEY (`id_riwayat`),
  ADD KEY `id_produk` (`id_produk`);

--
-- Indexes for table `role`
--
ALTER TABLE `role`
  ADD PRIMARY KEY (`id_role`);

--
-- Indexes for table `supplier`
--
ALTER TABLE `supplier`
  ADD PRIMARY KEY (`id_supplier`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `audit_log`
--
ALTER TABLE `audit_log`
  MODIFY `id_log` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=81;

--
-- AUTO_INCREMENT for table `detail_pembelian`
--
ALTER TABLE `detail_pembelian`
  MODIFY `id_detail` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `detail_penjualan`
--
ALTER TABLE `detail_penjualan`
  MODIFY `id_detail` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `hak_akses`
--
ALTER TABLE `hak_akses`
  MODIFY `id_hak_akses` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=97;

--
-- AUTO_INCREMENT for table `kategori`
--
ALTER TABLE `kategori`
  MODIFY `id_kategori` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `konfigurasi`
--
ALTER TABLE `konfigurasi`
  MODIFY `id_konfig` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `pelanggan`
--
ALTER TABLE `pelanggan`
  MODIFY `id_pelanggan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `pembelian`
--
ALTER TABLE `pembelian`
  MODIFY `id_pembelian` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `pengguna`
--
ALTER TABLE `pengguna`
  MODIFY `id_pengguna` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `penjualan`
--
ALTER TABLE `penjualan`
  MODIFY `id_penjualan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `produk`
--
ALTER TABLE `produk`
  MODIFY `id_produk` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `riwayat_stok`
--
ALTER TABLE `riwayat_stok`
  MODIFY `id_riwayat` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `role`
--
ALTER TABLE `role`
  MODIFY `id_role` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `supplier`
--
ALTER TABLE `supplier`
  MODIFY `id_supplier` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `audit_log`
--
ALTER TABLE `audit_log`
  ADD CONSTRAINT `audit_log_ibfk_1` FOREIGN KEY (`id_pengguna`) REFERENCES `pengguna` (`id_pengguna`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `detail_pembelian`
--
ALTER TABLE `detail_pembelian`
  ADD CONSTRAINT `detail_pembelian_ibfk_1` FOREIGN KEY (`id_pembelian`) REFERENCES `pembelian` (`id_pembelian`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `detail_pembelian_ibfk_2` FOREIGN KEY (`id_produk`) REFERENCES `produk` (`id_produk`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `detail_penjualan`
--
ALTER TABLE `detail_penjualan`
  ADD CONSTRAINT `detail_penjualan_ibfk_1` FOREIGN KEY (`id_penjualan`) REFERENCES `penjualan` (`id_penjualan`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `detail_penjualan_ibfk_2` FOREIGN KEY (`id_produk`) REFERENCES `produk` (`id_produk`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `hak_akses`
--
ALTER TABLE `hak_akses`
  ADD CONSTRAINT `hak_akses_ibfk_1` FOREIGN KEY (`id_role`) REFERENCES `role` (`id_role`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `pembelian`
--
ALTER TABLE `pembelian`
  ADD CONSTRAINT `pembelian_ibfk_1` FOREIGN KEY (`id_supplier`) REFERENCES `supplier` (`id_supplier`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `pembelian_ibfk_2` FOREIGN KEY (`id_pengguna`) REFERENCES `pengguna` (`id_pengguna`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `pengguna`
--
ALTER TABLE `pengguna`
  ADD CONSTRAINT `pengguna_ibfk_1` FOREIGN KEY (`id_role`) REFERENCES `role` (`id_role`) ON UPDATE CASCADE;

--
-- Constraints for table `penjualan`
--
ALTER TABLE `penjualan`
  ADD CONSTRAINT `penjualan_ibfk_1` FOREIGN KEY (`id_pelanggan`) REFERENCES `pelanggan` (`id_pelanggan`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `penjualan_ibfk_2` FOREIGN KEY (`id_pengguna`) REFERENCES `pengguna` (`id_pengguna`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `produk`
--
ALTER TABLE `produk`
  ADD CONSTRAINT `produk_ibfk_1` FOREIGN KEY (`id_kategori`) REFERENCES `kategori` (`id_kategori`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `riwayat_stok`
--
ALTER TABLE `riwayat_stok`
  ADD CONSTRAINT `riwayat_stok_ibfk_1` FOREIGN KEY (`id_produk`) REFERENCES `produk` (`id_produk`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
