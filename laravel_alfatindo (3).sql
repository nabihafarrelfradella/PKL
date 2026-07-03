-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jul 03, 2026 at 08:57 AM
-- Server version: 8.4.3
-- PHP Version: 8.3.26

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `laravel_alfatindo`
--

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int UNSIGNED NOT NULL,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2019_08_19_000000_create_failed_jobs_table', 1),
(2, '2019_12_14_000001_create_personal_access_tokens_table', 1),
(3, '2022_10_31_061811_create_menu_table', 1),
(4, '2022_11_01_041110_create_table_role', 1),
(5, '2022_11_01_083314_create_table_user', 1),
(6, '2022_11_03_023905_create_table_submenu', 1),
(7, '2022_11_03_064417_create_tbl_akses', 1),
(8, '2022_11_15_131148_create_tbl_jenisbarang', 1),
(9, '2022_11_15_173700_create_tbl_satuan', 1),
(10, '2022_11_15_180434_create_tbl_merk', 1),
(11, '2022_11_16_120018_create_tbl_appreance', 1),
(12, '2022_11_25_141731_create_tbl_barang', 1),
(13, '2022_11_26_011349_create_tbl_customer', 1),
(14, '2022_11_28_151108_create_tbl_barangmasuk', 1),
(15, '2022_11_30_115904_create_tbl_barangkeluar', 1),
(16, '2026_04_07_000000_add_tipe_barang_to_tbl_barang', 1),
(17, '2026_04_07_000001_add_status_return_to_tbl_barangkeluar', 1),
(18, '2026_04_07_000003_change_satuan_to_enum_in_tbl_barang', 1),
(19, '2026_04_07_000004_move_user_menu_under_master_barang', 1),
(20, '2026_04_07_000005_fix_user_menu_permissions', 1),
(21, '2026_04_07_000006_overhaul_rbac_system', 1),
(22, '2026_04_08_000001_add_transaksi_akses_for_pegawai', 1),
(23, '2026_04_11_000000_add_revisi_fields_to_tables', 1),
(24, '2026_04_15_032841_create_tbl_audit_log_table', 1),
(25, '2026_04_20_000001_add_phone_to_tbl_user', 1),
(26, '2026_05_06_000000_add_fields_to_tbl_user', 1),
(27, '2026_05_06_000001_add_kode_barang_unik_to_tbl_barangkeluar', 1),
(28, '2026_05_24_000001_finalize_rbac_permissions', 1),
(29, '2026_05_24_000002_sync_submenu_and_finalize_rbac', 1),
(30, '2026_05_27_013128_create_tbl_notifikasi', 1),
(31, '2026_06_12_175319_add_revisi_1_fields_and_cleanup', 1),
(32, '2026_06_12_201904_add_teknisi_nama_to_tbl_barangkeluar', 1),
(33, '2026_06_14_192000_change_bk_status_to_varchar_in_tbl_barangkeluar', 1),
(34, '2026_06_14_201500_backfill_tbl_barangkeluar_data', 1),
(35, '2026_06_30_134815_add_bk_lokasi_to_tbl_barangkeluar', 1),
(36, '2026_07_01_114044_alter_serial_number_on_tbl_barangmasuk_table', 1),
(37, '2026_07_01_154629_add_lat_lng_to_tbl_barangkeluar', 1),
(38, '2026_07_02_145618_add_deleted_at_to_barangmasuk_and_barangkeluar', 1),
(39, '2026_07_02_150558_add_deleted_at_to_tbl_barang', 1);

-- --------------------------------------------------------

--
-- Table structure for table `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_akses`
--

CREATE TABLE `tbl_akses` (
  `akses_id` int UNSIGNED NOT NULL,
  `menu_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `submenu_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `othermenu_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `role_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `akses_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tbl_akses`
--

INSERT INTO `tbl_akses` (`akses_id`, `menu_id`, `submenu_id`, `othermenu_id`, `role_id`, `akses_type`, `created_at`, `updated_at`) VALUES
(1, '1667444041', NULL, NULL, '2', 'view', '2026-07-03 08:25:22', '2026-07-03 08:25:22'),
(2, '1667444041', NULL, NULL, '2', 'create', '2026-07-03 08:25:22', '2026-07-03 08:25:22'),
(3, '1667444041', NULL, NULL, '2', 'update', '2026-07-03 08:25:22', '2026-07-03 08:25:22'),
(4, '1667444041', NULL, NULL, '2', 'delete', '2026-07-03 08:25:22', '2026-07-03 08:25:22'),
(5, '1000000001', NULL, NULL, '2', 'view', '2026-07-03 08:25:22', '2026-07-03 08:25:22'),
(6, NULL, '4', NULL, '2', 'view', '2026-07-03 08:25:22', '2026-07-03 08:25:22'),
(7, NULL, '4', NULL, '2', 'create', '2026-07-03 08:25:22', '2026-07-03 08:25:22'),
(8, NULL, '4', NULL, '2', 'update', '2026-07-03 08:25:22', '2026-07-03 08:25:22'),
(9, NULL, '4', NULL, '2', 'delete', '2026-07-03 08:25:22', '2026-07-03 08:25:22'),
(10, NULL, '5', NULL, '2', 'view', '2026-07-03 08:25:22', '2026-07-03 08:25:22'),
(11, NULL, '5', NULL, '2', 'create', '2026-07-03 08:25:22', '2026-07-03 08:25:22'),
(12, NULL, '5', NULL, '2', 'update', '2026-07-03 08:25:22', '2026-07-03 08:25:22'),
(13, NULL, '5', NULL, '2', 'delete', '2026-07-03 08:25:22', '2026-07-03 08:25:22'),
(14, NULL, '6', NULL, '2', 'view', '2026-07-03 08:25:22', '2026-07-03 08:25:22'),
(15, NULL, '6', NULL, '2', 'create', '2026-07-03 08:25:22', '2026-07-03 08:25:22'),
(16, NULL, '6', NULL, '2', 'update', '2026-07-03 08:25:22', '2026-07-03 08:25:22'),
(17, NULL, '6', NULL, '2', 'delete', '2026-07-03 08:25:22', '2026-07-03 08:25:22'),
(18, '1000000002', NULL, NULL, '2', 'view', '2026-07-03 08:25:22', '2026-07-03 08:25:22'),
(19, NULL, '7', NULL, '2', 'view', '2026-07-03 08:25:22', '2026-07-03 08:25:22'),
(20, NULL, '7', NULL, '2', 'create', '2026-07-03 08:25:22', '2026-07-03 08:25:22'),
(21, NULL, '7', NULL, '2', 'update', '2026-07-03 08:25:22', '2026-07-03 08:25:22'),
(22, NULL, '7', NULL, '2', 'delete', '2026-07-03 08:25:22', '2026-07-03 08:25:22'),
(23, NULL, '8', NULL, '2', 'view', '2026-07-03 08:25:22', '2026-07-03 08:25:22'),
(24, NULL, '8', NULL, '2', 'create', '2026-07-03 08:25:22', '2026-07-03 08:25:22'),
(25, NULL, '8', NULL, '2', 'update', '2026-07-03 08:25:22', '2026-07-03 08:25:22'),
(26, NULL, '8', NULL, '2', 'delete', '2026-07-03 08:25:22', '2026-07-03 08:25:22'),
(27, NULL, '9', NULL, '2', 'view', '2026-07-03 08:25:22', '2026-07-03 08:25:22'),
(28, NULL, '9', NULL, '2', 'create', '2026-07-03 08:25:22', '2026-07-03 08:25:22'),
(29, NULL, '9', NULL, '2', 'update', '2026-07-03 08:25:22', '2026-07-03 08:25:22'),
(30, NULL, '9', NULL, '2', 'delete', '2026-07-03 08:25:22', '2026-07-03 08:25:22'),
(31, '1000000003', NULL, NULL, '2', 'view', '2026-07-03 08:25:22', '2026-07-03 08:25:22'),
(32, NULL, '10', NULL, '2', 'view', '2026-07-03 08:25:22', '2026-07-03 08:25:22'),
(33, NULL, '10', NULL, '2', 'create', '2026-07-03 08:25:22', '2026-07-03 08:25:22'),
(34, NULL, '10', NULL, '2', 'update', '2026-07-03 08:25:22', '2026-07-03 08:25:22'),
(35, NULL, '10', NULL, '2', 'delete', '2026-07-03 08:25:22', '2026-07-03 08:25:22'),
(36, NULL, '11', NULL, '2', 'view', '2026-07-03 08:25:22', '2026-07-03 08:25:22'),
(37, NULL, '11', NULL, '2', 'create', '2026-07-03 08:25:22', '2026-07-03 08:25:22'),
(38, NULL, '11', NULL, '2', 'update', '2026-07-03 08:25:22', '2026-07-03 08:25:22'),
(39, NULL, '11', NULL, '2', 'delete', '2026-07-03 08:25:22', '2026-07-03 08:25:22'),
(40, NULL, '12', NULL, '2', 'view', '2026-07-03 08:25:22', '2026-07-03 08:25:22'),
(41, NULL, '12', NULL, '2', 'create', '2026-07-03 08:25:22', '2026-07-03 08:25:22'),
(42, NULL, '12', NULL, '2', 'update', '2026-07-03 08:25:22', '2026-07-03 08:25:22'),
(43, NULL, '12', NULL, '2', 'delete', '2026-07-03 08:25:22', '2026-07-03 08:25:22'),
(44, '1667444041', NULL, NULL, '3', 'view', '2026-07-03 08:25:22', '2026-07-03 08:25:22'),
(45, '1000000002', NULL, NULL, '3', 'view', '2026-07-03 08:25:22', '2026-07-03 08:25:22'),
(46, NULL, '7', NULL, '3', 'view', '2026-07-03 08:25:22', '2026-07-03 08:25:22'),
(47, NULL, '8', NULL, '3', 'view', '2026-07-03 08:25:22', '2026-07-03 08:25:22'),
(48, NULL, '8', NULL, '3', 'create', '2026-07-03 08:25:22', '2026-07-03 08:25:22');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_appreance`
--

CREATE TABLE `tbl_appreance` (
  `appreance_id` int UNSIGNED NOT NULL,
  `user_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `appreance_layout` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `appreance_theme` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `appreance_menu` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `appreance_header` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `appreance_sidestyle` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_audit_log`
--

CREATE TABLE `tbl_audit_log` (
  `audit_id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `role_slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `activity` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `module` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `details` text COLLATE utf8mb4_unicode_ci,
  `ip_address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tbl_audit_log`
--

INSERT INTO `tbl_audit_log` (`audit_id`, `user_id`, `role_slug`, `activity`, `module`, `details`, `ip_address`, `created_at`, `updated_at`) VALUES
(1, 1, 'owner', 'UPDATE_TEKNISI', 'User Management', 'Owner updated Teknisi account: teknisi1 (user_id: 3)', '127.0.0.1', '2026-07-03 08:31:54', '2026-07-03 08:31:54'),
(2, 1, 'owner', 'UPDATE_ADMIN_GUDANG', 'User Management', 'Owner updated Admin Gudang: admingudang (user_id: 2)', '127.0.0.1', '2026-07-03 08:38:41', '2026-07-03 08:38:41'),
(3, 1, 'owner', 'UPDATE_ADMIN_GUDANG', 'User Management', 'Owner updated Admin Gudang: admingudang (user_id: 2)', '127.0.0.1', '2026-07-03 08:44:12', '2026-07-03 08:44:12'),
(4, 1, 'owner', 'UPDATE_ADMIN_GUDANG', 'User Management', 'Owner updated Admin Gudang: admingudang (user_id: 2)', '127.0.0.1', '2026-07-03 08:44:27', '2026-07-03 08:44:27'),
(5, 1, 'owner', 'UPDATE_ADMIN_GUDANG', 'User Management', 'Owner updated Admin Gudang: admingudang (user_id: 2)', '127.0.0.1', '2026-07-03 08:50:24', '2026-07-03 08:50:24');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_barang`
--

CREATE TABLE `tbl_barang` (
  `barang_id` int UNSIGNED NOT NULL,
  `jenisbarang_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `satuan_id` enum('Meter','Pcs','Roll','Unit') COLLATE utf8mb4_unicode_ci DEFAULT 'Unit',
  `merk_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `barang_kode` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `barang_nama` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `barang_slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `barang_harga` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `barang_stok` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tipe_barang` enum('Barang Kembali','Barang Habis Pakai') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Barang Kembali',
  `barang_gambar` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `serial_number` text COLLATE utf8mb4_unicode_ci,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_barangkeluar`
--

CREATE TABLE `tbl_barangkeluar` (
  `bk_id` int UNSIGNED NOT NULL,
  `bk_kode` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `barang_kode` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `kode_barang_unik` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bk_tanggal` date NOT NULL,
  `bk_tujuan` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bk_lokasi` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bk_map_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bk_lat` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bk_lng` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bk_jumlah` int NOT NULL,
  `bk_status` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Dipinjam',
  `bk_tgl_kembali` date DEFAULT NULL,
  `bk_kondisi_kembali` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bk_jumlah_kembali` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `serial_number` text COLLATE utf8mb4_unicode_ci,
  `jam_keluar` datetime DEFAULT NULL,
  `teknisi` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `teknisi_nama` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `keterangan` text COLLATE utf8mb4_unicode_ci,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_barangmasuk`
--

CREATE TABLE `tbl_barangmasuk` (
  `bm_id` int UNSIGNED NOT NULL,
  `bm_kode` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `barang_kode` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_id` int NOT NULL,
  `bm_tanggal` date NOT NULL,
  `bm_jumlah` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `serial_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `kode_barang_unik` text COLLATE utf8mb4_unicode_ci,
  `jam_masuk` datetime DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_customer`
--

CREATE TABLE `tbl_customer` (
  `customer_id` int UNSIGNED NOT NULL,
  `customer_kode` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `customer_nama` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_alamat` text COLLATE utf8mb4_unicode_ci,
  `customer_notelp` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_jenisbarang`
--

CREATE TABLE `tbl_jenisbarang` (
  `jenisbarang_id` int UNSIGNED NOT NULL,
  `jenisbarang_nama` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `jenisbarang_slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `jenisbarang_keterangan` enum('Barang Kembali','Barang Habis Pakai') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Barang Kembali',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tbl_jenisbarang`
--

INSERT INTO `tbl_jenisbarang` (`jenisbarang_id`, `jenisbarang_nama`, `jenisbarang_slug`, `jenisbarang_keterangan`, `created_at`, `updated_at`) VALUES
(1, 'Barang Kembali', 'barang-kembali', 'Barang Kembali', '2026-07-03 08:25:22', '2026-07-03 08:25:22'),
(2, 'Barang Habis Pakai', 'barang-habis-pakai', 'Barang Habis Pakai', '2026-07-03 08:25:22', '2026-07-03 08:25:22');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_menu`
--

CREATE TABLE `tbl_menu` (
  `menu_id` int UNSIGNED NOT NULL,
  `menu_judul` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `menu_slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `menu_icon` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `menu_redirect` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `menu_sort` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `menu_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tbl_menu`
--

INSERT INTO `tbl_menu` (`menu_id`, `menu_judul`, `menu_slug`, `menu_icon`, `menu_redirect`, `menu_sort`, `menu_type`, `created_at`, `updated_at`) VALUES
(1000000001, 'Master Barang', 'master-barang', 'package', '-', '2', '2', '2026-07-03 08:25:19', '2026-07-03 08:25:19'),
(1000000002, 'Transaksi', 'transaksi', 'repeat', '-', '3', '2', '2026-07-03 08:25:19', '2026-07-03 08:25:19'),
(1000000003, 'Laporan', 'laporan', 'printer', '-', '4', '2', '2026-07-03 08:25:19', '2026-07-03 08:25:19'),
(1667444041, 'Dashboard', 'dashboard', 'home', '/dashboard', '1', '1', '2026-07-03 08:25:22', '2026-07-03 08:25:22'),
(1783067117, 'User', 'user', 'users', '-', '2', '2', '2026-07-03 08:25:17', '2026-07-03 08:25:17');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_merk`
--

CREATE TABLE `tbl_merk` (
  `merk_id` int UNSIGNED NOT NULL,
  `merk_nama` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `merk_slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `merk_keterangan` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tbl_merk`
--

INSERT INTO `tbl_merk` (`merk_id`, `merk_nama`, `merk_slug`, `merk_keterangan`, `created_at`, `updated_at`) VALUES
(1, 'Krisbow', 'krisbow', 'Alat & Perkakas', '2026-07-03 08:25:22', '2026-07-03 08:25:22');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_notifikasi`
--

CREATE TABLE `tbl_notifikasi` (
  `notif_id` bigint UNSIGNED NOT NULL,
  `notif_type` enum('peminjaman','pengembalian','habis_pakai') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'peminjaman',
  `notif_pesan` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `notif_dari` int UNSIGNED NOT NULL,
  `notif_nama_teknisi` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `notif_barang` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `notif_customer` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bk_id` bigint UNSIGNED DEFAULT NULL,
  `is_read_owner` tinyint(1) NOT NULL DEFAULT '0',
  `is_read_gudang` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_role`
--

CREATE TABLE `tbl_role` (
  `role_id` int UNSIGNED NOT NULL,
  `role_title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role_slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role_desc` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tbl_role`
--

INSERT INTO `tbl_role` (`role_id`, `role_title`, `role_slug`, `role_desc`, `created_at`, `updated_at`) VALUES
(1, 'Owner', 'owner', 'Akses penuh ke seluruh sistem. Tidak dapat diubah.', '2026-07-03 08:25:22', '2026-07-03 08:25:22'),
(2, 'Admin Gudang', 'admin-gudang', 'Mengelola data barang, transaksi masuk/keluar, dan laporan.', '2026-07-03 08:25:22', '2026-07-03 08:25:22'),
(3, 'Pegawai Teknisi', 'pegawai-teknisi', 'Melihat data barang dan mengajukan peminjaman barang.', '2026-07-03 08:25:22', '2026-07-03 08:25:22');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_satuan`
--

CREATE TABLE `tbl_satuan` (
  `satuan_id` int UNSIGNED NOT NULL,
  `satuan_nama` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `satuan_slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `satuan_keterangan` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_submenu`
--

CREATE TABLE `tbl_submenu` (
  `submenu_id` int UNSIGNED NOT NULL,
  `menu_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `submenu_judul` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `submenu_slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `submenu_redirect` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `submenu_sort` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tbl_submenu`
--

INSERT INTO `tbl_submenu` (`submenu_id`, `menu_id`, `submenu_judul`, `submenu_slug`, `submenu_redirect`, `submenu_sort`, `created_at`, `updated_at`) VALUES
(1, '1783067117', 'Role', 'role', '/role', '1', '2026-07-03 08:25:17', '2026-07-03 08:25:17'),
(2, '1783067117', 'List', 'list', '/user', '2', '2026-07-03 08:25:17', '2026-07-03 08:25:17'),
(3, '1783067117', 'Akses', 'akses', '/akses/role', '3', '2026-07-03 08:25:17', '2026-07-03 08:25:17'),
(4, '1000000001', 'Jenis Barang', 'jenisbarang', '/jenisbarang', '1', '2026-07-03 08:25:19', '2026-07-03 08:25:19'),
(5, '1000000001', 'Merk Barang', 'merk', '/merk', '2', '2026-07-03 08:25:19', '2026-07-03 08:25:19'),
(6, '1000000001', 'Data Barang', 'barang', '/barang', '3', '2026-07-03 08:25:19', '2026-07-03 08:25:19'),
(7, '1000000002', 'Barang Masuk', 'barang-masuk', '/barang-masuk', '1', '2026-07-03 08:25:19', '2026-07-03 08:25:19'),
(8, '1000000002', 'Barang Keluar', 'barang-keluar', '/barang-keluar', '2', '2026-07-03 08:25:19', '2026-07-03 08:25:19'),
(9, '1000000002', 'Barang Tracking', 'barang-tracking', '/barang-tracking', '3', '2026-07-03 08:25:19', '2026-07-03 08:25:19'),
(10, '1000000003', 'Lap. Barang Masuk', 'lap-barang-masuk', '/lap-barang-masuk', '1', '2026-07-03 08:25:19', '2026-07-03 08:25:19'),
(11, '1000000003', 'Lap. Barang Keluar', 'lap-barang-keluar', '/lap-barang-keluar', '2', '2026-07-03 08:25:19', '2026-07-03 08:25:19'),
(12, '1000000003', 'Lap. Stok Barang', 'lap-stok-barang', '/lap-stok-barang', '3', '2026-07-03 08:25:19', '2026-07-03 08:25:19');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_user`
--

CREATE TABLE `tbl_user` (
  `user_id` int UNSIGNED NOT NULL,
  `role_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_nmlengkap` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `jenis_kelamin` enum('M','F') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tanggal_lahir` date DEFAULT NULL,
  `teknisi_sn` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_nama` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_phone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_foto` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tbl_user`
--

INSERT INTO `tbl_user` (`user_id`, `role_id`, `user_nmlengkap`, `jenis_kelamin`, `tanggal_lahir`, `teknisi_sn`, `user_nama`, `user_email`, `user_phone`, `user_foto`, `user_password`, `created_at`, `updated_at`) VALUES
(1, '1', 'Owner Alfatindo', NULL, NULL, NULL, 'owner', 'owner@alfatindo.com', NULL, 'undraw_profile.svg', '25d55ad283aa400af464c76d713c07ad', '2026-07-03 08:25:22', '2026-07-03 08:25:22'),
(2, '2', 'Admin Gudang', NULL, NULL, NULL, 'admingudang', 'admingudang@alfatindo.com', NULL, 'undraw_profile.svg', '25d55ad283aa400af464c76d713c07ad', '2026-07-03 08:25:22', '2026-07-03 08:50:24'),
(3, '3', 'Ahmad', 'M', '1995-01-15', 'M-15-1995', 'teknisi1', 'ahmad@alfatindo.com', '085853484206', 'oTUbQi82n4SVM1WVRi7poeGLBdqfrNnNawKD0SGR.jpg', '25d55ad283aa400af464c76d713c07ad', '2026-07-03 08:25:22', '2026-07-03 08:31:54');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

--
-- Indexes for table `tbl_akses`
--
ALTER TABLE `tbl_akses`
  ADD PRIMARY KEY (`akses_id`);

--
-- Indexes for table `tbl_appreance`
--
ALTER TABLE `tbl_appreance`
  ADD PRIMARY KEY (`appreance_id`);

--
-- Indexes for table `tbl_audit_log`
--
ALTER TABLE `tbl_audit_log`
  ADD PRIMARY KEY (`audit_id`);

--
-- Indexes for table `tbl_barang`
--
ALTER TABLE `tbl_barang`
  ADD PRIMARY KEY (`barang_id`);

--
-- Indexes for table `tbl_barangkeluar`
--
ALTER TABLE `tbl_barangkeluar`
  ADD PRIMARY KEY (`bk_id`),
  ADD UNIQUE KEY `tbl_barangkeluar_bk_kode_unique` (`bk_kode`);

--
-- Indexes for table `tbl_barangmasuk`
--
ALTER TABLE `tbl_barangmasuk`
  ADD PRIMARY KEY (`bm_id`),
  ADD UNIQUE KEY `tbl_barangmasuk_bm_kode_unique` (`bm_kode`);

--
-- Indexes for table `tbl_customer`
--
ALTER TABLE `tbl_customer`
  ADD PRIMARY KEY (`customer_id`),
  ADD UNIQUE KEY `tbl_customer_customer_kode_unique` (`customer_kode`);

--
-- Indexes for table `tbl_jenisbarang`
--
ALTER TABLE `tbl_jenisbarang`
  ADD PRIMARY KEY (`jenisbarang_id`);

--
-- Indexes for table `tbl_menu`
--
ALTER TABLE `tbl_menu`
  ADD PRIMARY KEY (`menu_id`);

--
-- Indexes for table `tbl_merk`
--
ALTER TABLE `tbl_merk`
  ADD PRIMARY KEY (`merk_id`);

--
-- Indexes for table `tbl_notifikasi`
--
ALTER TABLE `tbl_notifikasi`
  ADD PRIMARY KEY (`notif_id`),
  ADD KEY `tbl_notifikasi_bk_id_index` (`bk_id`),
  ADD KEY `tbl_notifikasi_is_read_owner_created_at_index` (`is_read_owner`,`created_at`),
  ADD KEY `tbl_notifikasi_is_read_gudang_created_at_index` (`is_read_gudang`,`created_at`);

--
-- Indexes for table `tbl_role`
--
ALTER TABLE `tbl_role`
  ADD PRIMARY KEY (`role_id`);

--
-- Indexes for table `tbl_satuan`
--
ALTER TABLE `tbl_satuan`
  ADD PRIMARY KEY (`satuan_id`);

--
-- Indexes for table `tbl_submenu`
--
ALTER TABLE `tbl_submenu`
  ADD PRIMARY KEY (`submenu_id`);

--
-- Indexes for table `tbl_user`
--
ALTER TABLE `tbl_user`
  ADD PRIMARY KEY (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_akses`
--
ALTER TABLE `tbl_akses`
  MODIFY `akses_id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=49;

--
-- AUTO_INCREMENT for table `tbl_appreance`
--
ALTER TABLE `tbl_appreance`
  MODIFY `appreance_id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_audit_log`
--
ALTER TABLE `tbl_audit_log`
  MODIFY `audit_id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `tbl_barang`
--
ALTER TABLE `tbl_barang`
  MODIFY `barang_id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_barangkeluar`
--
ALTER TABLE `tbl_barangkeluar`
  MODIFY `bk_id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_barangmasuk`
--
ALTER TABLE `tbl_barangmasuk`
  MODIFY `bm_id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_customer`
--
ALTER TABLE `tbl_customer`
  MODIFY `customer_id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_jenisbarang`
--
ALTER TABLE `tbl_jenisbarang`
  MODIFY `jenisbarang_id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `tbl_menu`
--
ALTER TABLE `tbl_menu`
  MODIFY `menu_id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1783067118;

--
-- AUTO_INCREMENT for table `tbl_merk`
--
ALTER TABLE `tbl_merk`
  MODIFY `merk_id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `tbl_notifikasi`
--
ALTER TABLE `tbl_notifikasi`
  MODIFY `notif_id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_role`
--
ALTER TABLE `tbl_role`
  MODIFY `role_id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `tbl_satuan`
--
ALTER TABLE `tbl_satuan`
  MODIFY `satuan_id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_submenu`
--
ALTER TABLE `tbl_submenu`
  MODIFY `submenu_id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `tbl_user`
--
ALTER TABLE `tbl_user`
  MODIFY `user_id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
