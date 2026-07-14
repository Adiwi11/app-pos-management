-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 12 Jul 2026 pada 17.08
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `sembako_bali`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `audit_log`
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
-- Dumping data untuk tabel `audit_log`
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
(31, 1, 'Berhasil Login ke sistem', 'pengguna', '2026-07-12 15:00:59', '::1');

-- --------------------------------------------------------

--
-- Struktur dari tabel `detail_pembelian`
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
-- Dumping data untuk tabel `detail_pembelian`
--

INSERT INTO `detail_pembelian` (`id_detail`, `id_pembelian`, `id_produk`, `jumlah`, `harga_beli`, `subtotal`) VALUES
(1, 1, 1, 3, 14000.00, 42000.00),
(2, 2, 2, 10, 30000.00, 300000.00);

-- --------------------------------------------------------

--
-- Struktur dari tabel `detail_penjualan`
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
-- Dumping data untuk tabel `detail_penjualan`
--

INSERT INTO `detail_penjualan` (`id_detail`, `id_penjualan`, `id_produk`, `jumlah`, `harga_jual`, `subtotal`) VALUES
(2, 2, 1, 1, 17000.00, 17000.00),
(3, 3, 1, 1, 7000.00, 7000.00),
(4, 4, 2, 5, 40000.00, 200000.00);

-- --------------------------------------------------------

--
-- Struktur dari tabel `hak_akses`
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
-- Dumping data untuk tabel `hak_akses`
--

INSERT INTO `hak_akses` (`id_hak_akses`, `id_role`, `nama_modul`, `akses_lihat`, `akses_tambah`, `akses_ubah`, `akses_hapus`) VALUES
(1, 3, 'dashboard', 1, 1, 1, 1),
(2, 3, 'pengguna', 0, 0, 0, 0),
(3, 3, 'hak_akses', 0, 0, 0, 0),
(4, 3, 'kategori', 1, 1, 1, 1),
(5, 3, 'produk', 0, 0, 0, 0),
(6, 3, 'supplier', 0, 0, 0, 0),
(7, 3, 'pelanggan', 1, 1, 1, 1),
(8, 3, 'pembelian', 1, 1, 1, 1),
(9, 3, 'penjualan', 1, 1, 1, 0),
(10, 3, 'gudang', 0, 0, 0, 0),
(11, 3, 'laporan', 0, 0, 0, 0),
(12, 3, 'setting', 0, 0, 0, 0),
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
(36, 1, 'setting', 1, 1, 1, 1);

-- --------------------------------------------------------

--
-- Struktur dari tabel `kategori`
--

CREATE TABLE `kategori` (
  `id_kategori` int(11) NOT NULL,
  `nama_kategori` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `kategori`
--

INSERT INTO `kategori` (`id_kategori`, `nama_kategori`) VALUES
(3, 'Elektronik'),
(1, 'Makanan'),
(5, 'Minuman Kaleng'),
(2, 'Olahan Makanan'),
(4, 'Sayuran');

-- --------------------------------------------------------

--
-- Struktur dari tabel `konfigurasi`
--

CREATE TABLE `konfigurasi` (
  `id_konfig` int(11) NOT NULL,
  `nama_toko` varchar(100) DEFAULT NULL,
  `alamat` varchar(255) DEFAULT NULL,
  `telepon` varchar(20) DEFAULT NULL,
  `logo` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `konfigurasi`
--

INSERT INTO `konfigurasi` (`id_konfig`, `nama_toko`, `alamat`, `telepon`, `logo`) VALUES
(1, 'Warung Bali', 'Jl. Bali Renon - Denpasar', '0812-3214-9988', NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `pelanggan`
--

CREATE TABLE `pelanggan` (
  `id_pelanggan` int(11) NOT NULL,
  `nama_pelanggan` varchar(100) NOT NULL,
  `no_telp` varchar(20) DEFAULT NULL,
  `alamat` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `pelanggan`
--

INSERT INTO `pelanggan` (`id_pelanggan`, `nama_pelanggan`, `no_telp`, `alamat`) VALUES
(1, 'Maureen', '08232332211', 'Renon Bali');

-- --------------------------------------------------------

--
-- Struktur dari tabel `pembelian`
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
-- Dumping data untuk tabel `pembelian`
--

INSERT INTO `pembelian` (`id_pembelian`, `no_nota`, `id_supplier`, `id_pengguna`, `tanggal`, `total_harga`, `status_approval`, `created_at`) VALUES
(1, 'PO-20260711-173229', 1, 1, '2026-07-11', 42000.00, 'approved', '2026-07-11 15:32:48'),
(2, 'PO-20260712-153012', 1, 1, '2026-07-12', 300000.00, 'approved', '2026-07-12 13:30:33');

-- --------------------------------------------------------

--
-- Struktur dari tabel `pengguna`
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
-- Dumping data untuk tabel `pengguna`
--

INSERT INTO `pengguna` (`id_pengguna`, `nama_lengkap`, `email`, `password`, `id_role`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Super Administrator', 'admin@wpms.local', '$2y$10$xaXazw6Ram6pjXHRns4EDOKgI4rXH/L1AjVm6ARYnswzYGt0eNRLK', 1, 'aktif', '2026-07-11 08:36:19', '2026-07-11 08:47:13'),
(2, 'Cashier', 'cashier@gmail.com', '$2y$10$KMzxNmhioPZoJtZ0eZUokeHjnkD9gfbSeyXxnJ4k30HMz9SNUy8eG', 1, 'aktif', '2026-07-12 02:38:05', '2026-07-12 14:59:58');

-- --------------------------------------------------------

--
-- Struktur dari tabel `penjualan`
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
-- Dumping data untuk tabel `penjualan`
--

INSERT INTO `penjualan` (`id_penjualan`, `no_invoice`, `id_pelanggan`, `id_pengguna`, `tanggal`, `subtotal`, `diskon`, `pajak`, `total_bayar`, `uang_bayar`, `kembalian`, `metode_bayar`) VALUES
(2, 'INV/20260711/840819', NULL, 1, '2026-07-11 15:34:31', 17000.00, 0.00, 0.00, 17000.00, 20000.00, 3000.00, 'tunai'),
(3, 'INV/20260711/453C45', NULL, 1, '2026-07-11 15:49:05', 7000.00, 0.00, 0.00, 7000.00, 10000.00, 3000.00, 'tunai'),
(4, 'INV/20260712/D08D61', NULL, 1, '2026-07-12 13:31:21', 200000.00, 0.00, 20000.00, 220000.00, 300000.00, 80000.00, 'tunai');

-- --------------------------------------------------------

--
-- Struktur dari tabel `produk`
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
-- Dumping data untuk tabel `produk`
--

INSERT INTO `produk` (`id_produk`, `id_kategori`, `sku`, `barcode`, `nama_produk`, `deskripsi`, `harga_beli`, `harga_jual`, `stok`, `gambar`) VALUES
(1, 1, 'PTG-23', '10101021', 'Ayam Potong', 'Ayam Potong per 250/Gr', 14000.00, 17000.00, 1, 'prod_6a5261d7d99ee2.96929590.jpg'),
(2, 1, 'SKU-INDOMIE-1', '121231231', 'MIE Indomie', 'Indomie', 30000.00, 40000.00, 5, 'prod_6a5396dd949819.98851206.jpg');

-- --------------------------------------------------------

--
-- Struktur dari tabel `riwayat_stok`
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
-- Dumping data untuk tabel `riwayat_stok`
--

INSERT INTO `riwayat_stok` (`id_riwayat`, `id_produk`, `jenis`, `jumlah`, `keterangan`, `tanggal`) VALUES
(1, 1, 'masuk', 3, 'Penerimaan PO Inbound Rekod Nota : PO-20260711-173229', '2026-07-11 15:32:55'),
(2, 1, 'keluar', 1, 'Penjualan Eceran/Partai POS No. Nota: INV/20260711/840819', '2026-07-11 15:34:31'),
(3, 1, 'keluar', 1, 'Penjualan Eceran/Partai POS No. Nota: INV/20260711/453C45', '2026-07-11 15:49:05'),
(4, 2, 'masuk', 10, 'Penerimaan PO Inbound Rekod Nota : PO-20260712-153012', '2026-07-12 13:30:49'),
(5, 2, 'keluar', 5, 'Penjualan Eceran/Partai POS No. Nota: INV/20260712/D08D61', '2026-07-12 13:31:21');

-- --------------------------------------------------------

--
-- Struktur dari tabel `role`
--

CREATE TABLE `role` (
  `id_role` int(11) NOT NULL,
  `nama_role` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `role`
--

INSERT INTO `role` (`id_role`, `nama_role`) VALUES
(1, 'Super Admin'),
(2, 'Admin Gudang'),
(3, 'Kasir'),
(4, 'Supervisior'),
(5, 'Superadmin');

-- --------------------------------------------------------

--
-- Struktur dari tabel `supplier`
--

CREATE TABLE `supplier` (
  `id_supplier` int(11) NOT NULL,
  `nama_supplier` varchar(100) NOT NULL,
  `no_telp` varchar(20) DEFAULT NULL,
  `alamat` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `supplier`
--

INSERT INTO `supplier` (`id_supplier`, `nama_supplier`, `no_telp`, `alamat`) VALUES
(1, 'Yogik', '08912341234', 'Renon'),
(2, 'Zora Diva', '089512223123', 'Jambi');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `audit_log`
--
ALTER TABLE `audit_log`
  ADD PRIMARY KEY (`id_log`),
  ADD KEY `id_pengguna` (`id_pengguna`);

--
-- Indeks untuk tabel `detail_pembelian`
--
ALTER TABLE `detail_pembelian`
  ADD PRIMARY KEY (`id_detail`),
  ADD KEY `id_pembelian` (`id_pembelian`),
  ADD KEY `id_produk` (`id_produk`);

--
-- Indeks untuk tabel `detail_penjualan`
--
ALTER TABLE `detail_penjualan`
  ADD PRIMARY KEY (`id_detail`),
  ADD KEY `id_penjualan` (`id_penjualan`),
  ADD KEY `id_produk` (`id_produk`);

--
-- Indeks untuk tabel `hak_akses`
--
ALTER TABLE `hak_akses`
  ADD PRIMARY KEY (`id_hak_akses`),
  ADD KEY `id_role` (`id_role`);

--
-- Indeks untuk tabel `kategori`
--
ALTER TABLE `kategori`
  ADD PRIMARY KEY (`id_kategori`),
  ADD UNIQUE KEY `nama_kategori` (`nama_kategori`);

--
-- Indeks untuk tabel `konfigurasi`
--
ALTER TABLE `konfigurasi`
  ADD PRIMARY KEY (`id_konfig`);

--
-- Indeks untuk tabel `pelanggan`
--
ALTER TABLE `pelanggan`
  ADD PRIMARY KEY (`id_pelanggan`);

--
-- Indeks untuk tabel `pembelian`
--
ALTER TABLE `pembelian`
  ADD PRIMARY KEY (`id_pembelian`),
  ADD UNIQUE KEY `no_nota` (`no_nota`),
  ADD KEY `id_supplier` (`id_supplier`),
  ADD KEY `id_pengguna` (`id_pengguna`);

--
-- Indeks untuk tabel `pengguna`
--
ALTER TABLE `pengguna`
  ADD PRIMARY KEY (`id_pengguna`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `id_role` (`id_role`);

--
-- Indeks untuk tabel `penjualan`
--
ALTER TABLE `penjualan`
  ADD PRIMARY KEY (`id_penjualan`),
  ADD UNIQUE KEY `no_invoice` (`no_invoice`),
  ADD KEY `id_pelanggan` (`id_pelanggan`),
  ADD KEY `id_pengguna` (`id_pengguna`);

--
-- Indeks untuk tabel `produk`
--
ALTER TABLE `produk`
  ADD PRIMARY KEY (`id_produk`),
  ADD UNIQUE KEY `sku` (`sku`),
  ADD UNIQUE KEY `barcode` (`barcode`),
  ADD KEY `id_kategori` (`id_kategori`);

--
-- Indeks untuk tabel `riwayat_stok`
--
ALTER TABLE `riwayat_stok`
  ADD PRIMARY KEY (`id_riwayat`),
  ADD KEY `id_produk` (`id_produk`);

--
-- Indeks untuk tabel `role`
--
ALTER TABLE `role`
  ADD PRIMARY KEY (`id_role`);

--
-- Indeks untuk tabel `supplier`
--
ALTER TABLE `supplier`
  ADD PRIMARY KEY (`id_supplier`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `audit_log`
--
ALTER TABLE `audit_log`
  MODIFY `id_log` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT untuk tabel `detail_pembelian`
--
ALTER TABLE `detail_pembelian`
  MODIFY `id_detail` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `detail_penjualan`
--
ALTER TABLE `detail_penjualan`
  MODIFY `id_detail` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT untuk tabel `hak_akses`
--
ALTER TABLE `hak_akses`
  MODIFY `id_hak_akses` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT untuk tabel `kategori`
--
ALTER TABLE `kategori`
  MODIFY `id_kategori` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `konfigurasi`
--
ALTER TABLE `konfigurasi`
  MODIFY `id_konfig` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `pelanggan`
--
ALTER TABLE `pelanggan`
  MODIFY `id_pelanggan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `pembelian`
--
ALTER TABLE `pembelian`
  MODIFY `id_pembelian` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `pengguna`
--
ALTER TABLE `pengguna`
  MODIFY `id_pengguna` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `penjualan`
--
ALTER TABLE `penjualan`
  MODIFY `id_penjualan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT untuk tabel `produk`
--
ALTER TABLE `produk`
  MODIFY `id_produk` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `riwayat_stok`
--
ALTER TABLE `riwayat_stok`
  MODIFY `id_riwayat` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `role`
--
ALTER TABLE `role`
  MODIFY `id_role` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `supplier`
--
ALTER TABLE `supplier`
  MODIFY `id_supplier` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `audit_log`
--
ALTER TABLE `audit_log`
  ADD CONSTRAINT `audit_log_ibfk_1` FOREIGN KEY (`id_pengguna`) REFERENCES `pengguna` (`id_pengguna`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `detail_pembelian`
--
ALTER TABLE `detail_pembelian`
  ADD CONSTRAINT `detail_pembelian_ibfk_1` FOREIGN KEY (`id_pembelian`) REFERENCES `pembelian` (`id_pembelian`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `detail_pembelian_ibfk_2` FOREIGN KEY (`id_produk`) REFERENCES `produk` (`id_produk`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `detail_penjualan`
--
ALTER TABLE `detail_penjualan`
  ADD CONSTRAINT `detail_penjualan_ibfk_1` FOREIGN KEY (`id_penjualan`) REFERENCES `penjualan` (`id_penjualan`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `detail_penjualan_ibfk_2` FOREIGN KEY (`id_produk`) REFERENCES `produk` (`id_produk`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `hak_akses`
--
ALTER TABLE `hak_akses`
  ADD CONSTRAINT `hak_akses_ibfk_1` FOREIGN KEY (`id_role`) REFERENCES `role` (`id_role`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `pembelian`
--
ALTER TABLE `pembelian`
  ADD CONSTRAINT `pembelian_ibfk_1` FOREIGN KEY (`id_supplier`) REFERENCES `supplier` (`id_supplier`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `pembelian_ibfk_2` FOREIGN KEY (`id_pengguna`) REFERENCES `pengguna` (`id_pengguna`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `pengguna`
--
ALTER TABLE `pengguna`
  ADD CONSTRAINT `pengguna_ibfk_1` FOREIGN KEY (`id_role`) REFERENCES `role` (`id_role`) ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `penjualan`
--
ALTER TABLE `penjualan`
  ADD CONSTRAINT `penjualan_ibfk_1` FOREIGN KEY (`id_pelanggan`) REFERENCES `pelanggan` (`id_pelanggan`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `penjualan_ibfk_2` FOREIGN KEY (`id_pengguna`) REFERENCES `pengguna` (`id_pengguna`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `produk`
--
ALTER TABLE `produk`
  ADD CONSTRAINT `produk_ibfk_1` FOREIGN KEY (`id_kategori`) REFERENCES `kategori` (`id_kategori`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `riwayat_stok`
--
ALTER TABLE `riwayat_stok`
  ADD CONSTRAINT `riwayat_stok_ibfk_1` FOREIGN KEY (`id_produk`) REFERENCES `produk` (`id_produk`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
