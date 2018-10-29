-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               10.1.36-MariaDB - mariadb.org binary distribution
-- Server OS:                    Win32
-- HeidiSQL Version:             9.5.0.5295
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;


-- Dumping database structure for dp0
DROP DATABASE IF EXISTS `dp0`;
CREATE DATABASE IF NOT EXISTS `dp0` /*!40100 DEFAULT CHARACTER SET latin1 */;
USE `dp0`;

-- Dumping structure for table dp0.d_debitur
DROP TABLE IF EXISTS `d_debitur`;
CREATE TABLE IF NOT EXISTS `d_debitur` (
  `nik` varchar(16) NOT NULL,
  `nokk` varchar(16) NOT NULL,
  `noreg` varchar(8) DEFAULT NULL,
  `npwp` varchar(15) NOT NULL,
  `nama` varchar(255) NOT NULL,
  `tglhr` date NOT NULL,
  `kotalhr` varchar(255) NOT NULL,
  `kdkelamin` int(11) NOT NULL,
  `nmibu` varchar(255) NOT NULL,
  `kdagama` int(11) NOT NULL,
  `kdpendidikan` int(11) NOT NULL,
  `kdpekerjaan` int(11) NOT NULL,
  `kdkawin` int(11) NOT NULL,
  `kdbpjs` int(11) NOT NULL,
  `kdjenkredit` varchar(1) NOT NULL DEFAULT '1',
  `kdtipe` varchar(1) NOT NULL,
  `email` varchar(255) NOT NULL,
  `nohp` varchar(20) NOT NULL,
  `jmltanggung` int(11) NOT NULL DEFAULT '0',
  `jmlkjp` int(11) NOT NULL DEFAULT '0',
  `jmlbpjs` int(11) NOT NULL DEFAULT '0',
  `jmltinggal` int(11) NOT NULL DEFAULT '0',
  `jmlroda2` int(11) NOT NULL DEFAULT '0',
  `jmlroda4` int(11) NOT NULL DEFAULT '0',
  `jmlrmh` int(11) NOT NULL DEFAULT '0',
  `pengeluaran` decimal(18,0) NOT NULL DEFAULT '0',
  `tgpemohon` date NOT NULL DEFAULT '0000-00-00',
  `is_huni` varchar(1) NOT NULL DEFAULT '0',
  `is_alm_ktp` varchar(1) DEFAULT NULL,
  `status` int(11) NOT NULL DEFAULT '0',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `cek_dukcapil` varchar(1) DEFAULT NULL,
  PRIMARY KEY (`nik`),
  KEY `FK_d_debitur_t_kelamin` (`kdkelamin`),
  KEY `FK_d_debitur_t_agama` (`kdagama`),
  KEY `FK_d_debitur_t_pendidikan` (`kdpendidikan`),
  KEY `FK_d_debitur_t_pekerjaan` (`kdpekerjaan`),
  KEY `FK_d_debitur_t_kawin` (`kdkawin`),
  KEY `FK_d_debitur_t_jenkredit` (`kdjenkredit`),
  KEY `FK_d_debitur_t_tipe_kredit` (`kdtipe`),
  KEY `FK_d_debitur_t_status_debitur` (`status`),
  KEY `FK_d_debitur_t_bpjs` (`kdbpjs`),
  CONSTRAINT `FK_d_debitur_t_bpjs` FOREIGN KEY (`kdbpjs`) REFERENCES `t_bpjs` (`kdbpjs`),
  CONSTRAINT `FK_d_debitur_t_jenkredit` FOREIGN KEY (`kdjenkredit`) REFERENCES `t_jenkredit` (`kdjenkredit`),
  CONSTRAINT `FK_d_debitur_t_pekerjaan` FOREIGN KEY (`kdpekerjaan`) REFERENCES `t_pekerjaan` (`kdpekerjaan`),
  CONSTRAINT `FK_d_debitur_t_status_debitur` FOREIGN KEY (`status`) REFERENCES `t_status_debitur` (`status`),
  CONSTRAINT `FK_d_debitur_t_tipe_kredit` FOREIGN KEY (`kdtipe`) REFERENCES `t_tipe_kredit` (`kdtipe`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Dumping data for table dp0.d_debitur: ~25 rows (approximately)
DELETE FROM `d_debitur`;
/*!40000 ALTER TABLE `d_debitur` DISABLE KEYS */;
INSERT INTO `d_debitur` (`nik`, `nokk`, `noreg`, `npwp`, `nama`, `tglhr`, `kotalhr`, `kdkelamin`, `nmibu`, `kdagama`, `kdpendidikan`, `kdpekerjaan`, `kdkawin`, `kdbpjs`, `kdjenkredit`, `kdtipe`, `email`, `nohp`, `jmltanggung`, `jmlkjp`, `jmlbpjs`, `jmltinggal`, `jmlroda2`, `jmlroda4`, `jmlrmh`, `pengeluaran`, `tgpemohon`, `is_huni`, `is_alm_ktp`, `status`, `created_at`, `updated_at`, `cek_dukcapil`) VALUES
	('0123129432194314', '', 'cjb55yba', '000000000000000', 'testadfsdfs', '0000-00-00', '', 0, '', 0, 0, 5, 1, 1, '1', '1', 'test@gmail.com', '0810000', 0, 0, 0, 5, 1, 0, 0, 3000000, '2018-10-22', '1', '1', 1, '2018-10-23 01:17:12', '2018-10-23 01:17:12', NULL),
	('1111111111111111', '2222222222222222', NULL, '111111111111111', 'test', '0000-00-00', 'test', 1, 'test', 1, 5, 5, 2, 1, '1', '2', 'ekox69@gmail.com', '12345', 1, 1, 1, 20, 1, 1, 1, 7500000, '2018-08-01', '1', NULL, 4, '2018-08-04 16:31:55', '2018-08-04 16:31:55', NULL),
	('1111111111111113', '3333333333333333', NULL, '222222222222444', 'test', '0000-00-00', 'test', 1, 'test', 1, 5, 5, 2, 1, '1', '2', 'test@gmail.com', '123', 7, 1, 1, 5, 1, 1, 1, 3500000, '2018-08-01', '1', NULL, 4, '2018-08-04 21:57:29', '2018-08-04 21:57:29', NULL),
	('1111111111111117', '', 'jwflub37', '000000000000000', 'test', '0000-00-00', '', 0, '', 0, 0, 5, 1, 0, '1', '1', 'test@gmail.com', '123', 0, 0, 0, 5, 1, 0, 0, 2000000, '2018-10-10', '1', NULL, 0, '2018-10-11 02:08:47', '2018-10-11 02:08:47', NULL),
	('1111111111111199', '1111111111111199', 'lbl5nv', '888888888888888', 'Test aja', '1989-01-03', 'test', 1, 'test', 1, 4, 5, 2, 0, '1', '1', 'ekox69@gmail.com', '0821000000', 1, 0, 0, 5, 1, 0, 0, 2000000, '2018-09-30', '1', NULL, 0, '2018-09-30 22:09:42', '2018-09-30 22:09:42', NULL),
	('1231000123200121', '1231000123200121', '832sfw', '123000000000000', 'Eko Firmansyah', '1986-01-01', 'Sukabumi', 1, 'Siti', 1, 4, 5, 2, 1, '1', '1', 'ekox69@gmail.com', '0812000000', 5, 1, 3, 5, 1, 0, 0, 3000000, '2018-10-10', '1', NULL, 0, '2018-10-10 17:27:02', '2018-10-10 17:27:02', NULL),
	('12312143566788', '12334556677885', NULL, '123445667', 'tes', '0000-00-00', 'jakarta', 1, 'tesa', 1, 5, 15, 2, 1, '1', '1', 'ttes@gmail.com', '1234567888', 1, 3, 2, 5, 1, 0, 0, 0, '2018-08-04', '1', NULL, 3, '2018-08-05 00:09:23', '2018-08-05 00:09:23', NULL),
	('1231231232121223', '', 'a0774gga', '123343434343443', 'test', '0000-00-00', '', 0, '', 0, 0, 5, 3, 1, '1', '2', 'test@gmail.com', '123', 0, 0, 0, 5, 1, 0, 0, 3000000, '2018-10-10', '1', '0', 0, '2018-10-11 02:41:47', '2018-10-11 02:41:47', NULL),
	('1234567489789456', '', 'mi6tsrhs', '123456781241646', 'Achok', '0000-00-00', '', 0, '', 0, 0, 90, 1, 1, '1', '1', 'achox@gmail.com', '08124151543154', 0, 0, 0, 10, 2, 0, 0, 2000000, '2018-10-23', '1', '1', 1, '2018-10-23 22:58:56', '2018-10-23 22:58:56', NULL),
	('1242134325345643', '', '6q7yaspe', '352234124125432', 'JUMALI', '0000-00-00', '', 0, '', 0, 0, 15, 1, 2, '1', '1', 'ali@gmail.com', '34654756873452763246', 0, 0, 0, 10, 2, 0, 0, 4000000, '2018-10-23', '1', '1', 1, '2018-10-23 11:48:10', '2018-10-23 11:48:10', NULL),
	('2134234234234234', '', '35i3kdtx', '234234234324234', 'tsst', '0000-00-00', '', 0, '', 0, 0, 45, 4, 2, '1', '2', 'test@gmail.com', '2342423', 0, 0, 0, 5, 1, 0, 0, 4500000, '2018-10-22', '1', '0', 0, '2018-10-22 23:47:42', '2018-10-22 23:47:42', NULL),
	('2233555667909877', '', 'td5g761u', '800006876600000', 'fadillah', '0000-00-00', '', 0, '', 0, 0, 5, 1, 1, '1', '1', 'ahhdjfkkhf@yahoo.com', '08123466788', 0, 0, 0, 5, 2, 0, 0, 1000000, '2018-10-23', '1', '1', 1, '2018-10-23 12:47:13', '2018-10-23 12:47:13', NULL),
	('3124124314233542', '', 'a9h3ht6e', '213124232584585', 'siapa nama saya', '0000-00-00', '', 0, '', 0, 0, 6, 1, 1, '1', '2', 'sjdjs@hma.cio', '08949582943', 0, 0, 0, 5, 2, 1, 0, 2500000, '2018-10-23', '1', '1', 1, '2018-10-23 13:07:52', '2018-10-23 13:07:52', NULL),
	('3171214098700009', '', '9gib72qx', '239123812193249', 'suharyadi eko nugroho', '0000-00-00', '', 0, '', 0, 0, 48, 2, 0, '1', '1', 'suharyadi.eko14@gmail.com', '081210950725', 0, 0, 0, 4, 5, 2, 0, 30000000, '2018-10-23', '1', '1', 1, '2018-10-23 11:22:46', '2018-10-23 11:22:46', NULL),
	('3174062304920004', '', 'ouldgxpv', '123456789012544', 'mandra', '0000-00-00', '', 0, '', 0, 0, 6, 1, 2, '1', '1', 'contoh@contoh.com', '0254786542', 0, 0, 0, 7, 1, 1, 0, 800000, '2018-10-23', '1', '1', 1, '2018-10-23 11:39:50', '2018-10-23 11:39:50', NULL),
	('3222222222222222', '1111111111111133', NULL, '111111111111111', 'ets', '1986-06-30', 'test', 1, 'test', 1, 4, 5, 3, 2, '1', '1', 'test@gmail.com', '123454', 0, 0, 2, 6, 0, 0, 0, 5000000, '2018-08-01', '1', NULL, 2, '2018-08-06 00:08:00', '2018-08-06 00:08:00', NULL),
	('3276012212760006', '', 'ri9wshjx', '258429380016000', 'muhamad ramdani', '0000-00-00', '', 0, '', 0, 0, 5, 1, 4, '1', '1', 'danimtars@yahoo.com', '081319994929', 0, 0, 0, 5, 1, 1, 0, 3, '2018-10-23', '1', '1', 1, '2018-10-23 13:19:40', '2018-10-23 13:19:40', NULL),
	('3312056677660001', '3312056545340001', NULL, '124886876557000', 'Eko Saprol', '1986-07-01', 'Sukabumi', 1, 'Sutarmi', 1, 5, 5, 1, 1, '1', '1', 'eko@gmail.com', '0', 1, 0, 1, 5, 0, 0, 0, 1000000, '2018-08-09', '1', NULL, 0, '2018-08-09 03:39:03', '2018-08-09 03:39:03', NULL),
	('3332323232323232', '', 'iz9wqkxz', '000000000000000', 'test', '0000-00-00', '', 0, '', 0, 0, 5, 1, 2, '1', '2', 'test@gmail.com', '081000000', 0, 0, 0, 5, 2, 0, 0, 7500000, '2018-10-22', '1', '0', 1, '2018-10-23 01:14:37', '2018-10-23 01:14:37', NULL),
	('3421354125125412', '', 'fpkep8fd', '124143614512353', 'Gigi', '0000-00-00', '', 0, '', 0, 0, 4, 2, 0, '1', '1', 'gigit@gmail.com', '081233334444', 0, 0, 0, 20, 1, 0, 0, 1500000, '2018-10-23', '1', '1', 1, '2018-10-24 00:10:45', '2018-10-24 00:10:45', NULL),
	('3674044210970006', '', '6ev1gcvg', '121212121212121', 'mmmmmmmmmmmmmmmmmmmm', '0000-00-00', '', 0, '', 0, 0, 5, 2, 0, '1', '1', 'jhjhjhjhjhj@gmail.com', '21212121212121212121', 0, 0, 0, 11, 2, 2, 0, 4540000, '2018-10-23', '1', '1', 1, '2018-10-23 11:46:46', '2018-10-23 11:46:46', NULL),
	('3751000000000000', '', '1rr2sb04', '233434545454555', 'annisa ayu suryani', '0000-00-00', '', 0, '', 0, 0, 5, 1, 3, '1', '2', 'annisasoerdiono@gmail.com', '08568721583', 0, 0, 0, 10, 1, 0, 0, 2000000, '2018-10-23', '1', '1', 1, '2018-10-23 11:50:45', '2018-10-23 11:50:45', NULL),
	('5325226363747474', '', 'obixv18b', '525263474743839', 'edy suryadi', '0000-00-00', '', 0, '', 0, 0, 5, 1, 2, '1', '1', 'gahajdkkah@yahoo.com', '081561141677', 0, 0, 0, 5, 1, 0, 0, 2000000, '2018-10-23', '1', '1', 1, '2018-10-23 12:42:03', '2018-10-23 12:42:03', NULL),
	('6371050107860300', '3175011206170014', 'lbl5nv', '123', 'Eko Firmansyah', '1986-07-01', 'Sukabumi', 1, 'Kuraesin', 1, 5, 5, 1, 1, '1', '1', 'ekox69@gmail.com', '0', 1, 0, 0, 0, 0, 0, 0, 0, '2018-10-01', '1', NULL, 0, '2018-08-09 03:39:03', '2018-08-09 03:39:03', NULL),
	('7127394858669559', '', 'kob0jg8g', '891017000220000', 'Zakaria', '0000-00-00', '', 0, '', 0, 0, 5, 1, 3, '1', '1', 'asjkjkdwhwh@yahoo.com', '08997866544', 0, 0, 0, 12, 1, 0, 0, 1500000, '2018-10-24', '1', '1', 1, '2018-10-24 08:55:25', '2018-10-24 08:55:25', NULL);
/*!40000 ALTER TABLE `d_debitur` ENABLE KEYS */;

-- Dumping structure for table dp0.d_debitur_alamat
DROP TABLE IF EXISTS `d_debitur_alamat`;
CREATE TABLE IF NOT EXISTS `d_debitur_alamat` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `nik` varchar(16) NOT NULL,
  `kdalamat` varchar(2) NOT NULL,
  `kdprop` varchar(2) NOT NULL,
  `kdkabkota` varchar(2) NOT NULL,
  `kdkec` varchar(2) NOT NULL,
  `kdkel` varchar(4) NOT NULL,
  `kodepos` varchar(5) NOT NULL,
  `telp` varchar(20) NOT NULL,
  `alamat` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `Index 2` (`nik`,`kdalamat`),
  KEY `FK_d_debitur_alamat_t_alamat` (`kdalamat`),
  KEY `FK_d_debitur_alamat_t_kelurahan` (`kdprop`,`kdkabkota`,`kdkec`,`kdkel`),
  CONSTRAINT `FK_d_debitur_alamat_d_debitur` FOREIGN KEY (`nik`) REFERENCES `d_debitur` (`nik`),
  CONSTRAINT `FK_d_debitur_alamat_t_alamat` FOREIGN KEY (`kdalamat`) REFERENCES `t_alamat` (`kdalamat`),
  CONSTRAINT `FK_d_debitur_alamat_t_kelurahan` FOREIGN KEY (`kdprop`, `kdkabkota`, `kdkec`, `kdkel`) REFERENCES `t_kelurahan` (`kdprop`, `kdkabkota`, `kdkec`, `kdkel`)
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=latin1;

-- Dumping data for table dp0.d_debitur_alamat: ~17 rows (approximately)
DELETE FROM `d_debitur_alamat`;
/*!40000 ALTER TABLE `d_debitur_alamat` DISABLE KEYS */;
INSERT INTO `d_debitur_alamat` (`id`, `nik`, `kdalamat`, `kdprop`, `kdkabkota`, `kdkec`, `kdkel`, `kodepos`, `telp`, `alamat`, `created_at`, `updated_at`) VALUES
	(1, '1111111111111111', '1', '31', '73', '01', '1001', '12345', '123', 'test', '2018-08-04 16:31:55', '2018-08-04 16:31:55'),
	(2, '1111111111111111', '2', '31', '71', '05', '1002', '12345', '123', 'test', '2018-08-04 16:31:55', '2018-08-04 16:31:55'),
	(5, '1111111111111113', '1', '31', '75', '01', '1006', '12345', '123', 'test', '2018-08-04 21:57:29', '2018-08-04 21:57:29'),
	(6, '1111111111111113', '2', '31', '75', '01', '1006', '12345', '123', 'test', '2018-08-04 21:57:29', '2018-08-04 21:57:29'),
	(7, '12312143566788', '1', '31', '73', '01', '1002', '12345', '13134', 'jakarta', '2018-08-05 00:09:23', '2018-08-05 00:09:23'),
	(8, '12312143566788', '2', '31', '73', '01', '1001', '12345', '12344', 'test@gmail.com', '2018-08-05 00:09:23', '2018-08-05 00:09:23'),
	(9, '3222222222222222', '1', '31', '74', '04', '1003', '12345', '234', 'test', '2018-08-06 00:08:00', '2018-08-06 00:08:00'),
	(10, '3222222222222222', '2', '31', '73', '04', '1005', '12345', '234', 'test', '2018-08-06 00:08:00', '2018-08-06 00:08:00'),
	(11, '3312056677660001', '1', '31', '71', '01', '1004', '12343', '02102232', 'Lapangan Banteng', '2018-08-09 03:39:03', '2018-08-09 03:39:03'),
	(12, '3312056677660001', '2', '31', '73', '05', '1003', '12343', '02102232', 'Lapangan Banteng', '2018-08-09 03:39:03', '2018-08-09 03:39:03'),
	(13, '1111111111111199', '1', '31', '71', '01', '1001', '12345', '02100000', 'test', '2018-09-30 22:09:42', '2018-09-30 22:09:42'),
	(14, '1111111111111199', '2', '31', '71', '01', '1001', '12345', '02100000', 'test', '2018-09-30 22:09:42', '2018-09-30 22:09:42'),
	(15, '1231000123200121', '1', '31', '71', '01', '1001', '12345', '021', '-', '2018-10-10 17:27:02', '2018-10-10 17:27:02'),
	(16, '1231000123200121', '2', '31', '71', '01', '1001', '12345', '021', '-', '2018-10-10 17:27:02', '2018-10-10 17:27:02'),
	(22, '1231231232121223', '2', '31', '71', '01', '1001', '12345', '123', 'test', '2018-10-11 02:41:47', '2018-10-11 02:41:47'),
	(25, '2134234234234234', '2', '31', '71', '01', '1001', '12343', '', 'test', '2018-10-22 23:47:42', '2018-10-22 23:47:42'),
	(27, '3332323232323232', '2', '31', '71', '01', '1001', '12345', '112344', 'test', '2018-10-23 01:14:37', '2018-10-23 01:14:37');
/*!40000 ALTER TABLE `d_debitur_alamat` ENABLE KEYS */;

-- Dumping structure for table dp0.d_debitur_dok
DROP TABLE IF EXISTS `d_debitur_dok`;
CREATE TABLE IF NOT EXISTS `d_debitur_dok` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `nik` varchar(16) DEFAULT NULL,
  `id_dok` int(11) DEFAULT NULL,
  `nmfile` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `Index 2` (`nik`,`id_dok`),
  KEY `FK_d_debitur_dok_t_dok` (`id_dok`),
  CONSTRAINT `FK_d_debitur_dok_d_debitur` FOREIGN KEY (`nik`) REFERENCES `d_debitur` (`nik`),
  CONSTRAINT `FK_d_debitur_dok_t_dok` FOREIGN KEY (`id_dok`) REFERENCES `t_dok` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=118 DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT;

-- Dumping data for table dp0.d_debitur_dok: ~75 rows (approximately)
DELETE FROM `d_debitur_dok`;
/*!40000 ALTER TABLE `d_debitur_dok` DISABLE KEYS */;
INSERT INTO `d_debitur_dok` (`id`, `nik`, `id_dok`, `nmfile`, `created_at`, `updated_at`) VALUES
	(1, '1111111111111111', 1, '1_0f849bf9444f98543c9243f58a118d46.png', '2018-08-04 16:29:08', '2018-08-04 16:29:08'),
	(2, '1111111111111111', 2, '2_4eeb1dd679d5dd9ea0123301ec3dce3d.png', '2018-08-04 16:29:10', '2018-08-04 16:29:10'),
	(3, '1111111111111111', 3, '3_ea3677655d15502c9522cc0ce84691fb.png', '2018-08-04 16:29:13', '2018-08-04 16:29:13'),
	(4, '1111111111111111', 4, '4_bb75508a13943947cc86538328a31825.png', '2018-08-04 16:29:18', '2018-08-04 16:29:18'),
	(5, '1111111111111111', 5, '5_df9da29a05982fafdcf9c6a75738298b.png', '2018-08-04 16:29:21', '2018-08-04 16:29:21'),
	(6, '1111111111111111', 6, '6_131f2a2595ee60384bd5077479bafd04.png', '2018-08-04 16:29:24', '2018-08-04 16:29:24'),
	(7, '1111111111111111', 7, '7_5b64c4836566b068caaac4490035ef69.png', '2018-08-04 16:29:32', '2018-08-04 16:29:32'),
	(8, '1111111111111111', 13, '13_115b514f3f6f4a4d0d1769f445d5aa76.png', '2018-08-04 16:29:41', '2018-08-04 16:29:41'),
	(31, '1111111111111113', 1, '1_e6ac29b440e51ecf6774da306b1a0795.png', '2018-08-04 21:53:46', '2018-08-04 21:53:46'),
	(32, '1111111111111113', 2, '2_6003b5023fefd82aa5e1f23d0b06fcec.png', '2018-08-04 21:53:54', '2018-08-04 21:53:54'),
	(33, '1111111111111113', 3, '3_29f2ebd34c18684406c574fda51213ca.png', '2018-08-04 21:53:56', '2018-08-04 21:53:56'),
	(34, '1111111111111113', 4, '4_d7497372c0d18971cefad131d77c42df.png', '2018-08-04 21:53:59', '2018-08-04 21:53:59'),
	(35, '1111111111111113', 5, '5_5676d6466390c81cf40eba850716339d.png', '2018-08-04 21:54:05', '2018-08-04 21:54:05'),
	(36, '1111111111111113', 6, '6_61e4fc8eaf4ee167eb0babdc073b9cf0.png', '2018-08-04 21:54:08', '2018-08-04 21:54:08'),
	(37, '1111111111111113', 7, '7_f127d96d6c876b2be81fc049811a30ec.png', '2018-08-04 21:54:12', '2018-08-04 21:54:12'),
	(38, '1111111111111113', 13, '13_78062e117d4aed1018709f90c4467da8.png', '2018-08-04 21:54:24', '2018-08-04 21:54:24'),
	(39, '12312143566788', 1, '1_f17f184f598dfe2557f3c2f5ba50f11f.png', '2018-08-04 21:54:24', '2018-08-05 00:08:06'),
	(40, '12312143566788', 2, '2_a51ad0e0a17340e219aa490b963dbe05.png', '2018-08-04 21:54:24', '2018-08-05 00:08:10'),
	(41, '12312143566788', 3, '3_f16bf846d39f06b3fcdb445de6381d92.png', '2018-08-04 21:54:24', '2018-08-05 00:08:14'),
	(42, '12312143566788', 4, '4_55d8f857149d82252b43b03444977183.png', '2018-08-04 21:54:24', '2018-08-05 00:08:18'),
	(43, '12312143566788', 5, '5_b9cabb938fc755ecbee6ba15e5b7757d.png', '2018-08-04 21:54:24', '2018-08-05 00:08:32'),
	(44, '12312143566788', 6, '6_6de29b2e94de5be4bf4bad17b8a18161.png', '2018-08-04 21:54:24', '2018-08-05 00:08:43'),
	(45, '12312143566788', 7, '7_948e47b2ac43d2752c47260133ab3f3e.png', '2018-08-04 21:54:24', '2018-08-05 00:08:48'),
	(46, '12312143566788', 13, '13_fdcc1cd1d1769883c9b8db5ea4d54b21.png', '2018-08-04 21:54:24', '2018-08-05 00:09:06'),
	(47, '3222222222222222', 1, '1_9a53ef21079a9a4ef06ae2439cc1d820.png', '2018-08-05 23:50:23', '2018-08-05 23:50:23'),
	(48, '3222222222222222', 2, '2_716914781929bc6187a3d4432c451a5c.png', '2018-08-05 23:50:25', '2018-08-05 23:50:25'),
	(49, '3222222222222222', 3, '3_f57f85cdfd0c4e5bd13bdef6c5f22642.png', '2018-08-05 23:50:28', '2018-08-05 23:50:28'),
	(50, '3222222222222222', 4, '4_8f125de0a56758771a8e39bfa971f773.png', '2018-08-05 23:50:31', '2018-08-05 23:50:31'),
	(51, '3222222222222222', 5, '5_171217c5e95e0836ccaf422d47f49843.png', '2018-08-05 23:50:36', '2018-08-05 23:50:36'),
	(52, '3222222222222222', 6, '6_5672fc0bd4c2b2e7d0fc734974da7d12.png', '2018-08-05 23:50:39', '2018-08-05 23:50:39'),
	(53, '3222222222222222', 7, '7_4a74dd2fb73ef52bab27a1d6bbf48941.png', '2018-08-05 23:50:42', '2018-08-05 23:50:42'),
	(54, '3222222222222222', 13, '13_1171cfd1bd365621c7755709dd7df98f.png', '2018-08-05 23:50:48', '2018-08-05 23:50:48'),
	(55, '3312056677660001', 1, '1_c61d096d0b2c15b73197fb98734a218f.pdf', '2018-08-09 03:36:34', '2018-08-09 03:36:34'),
	(56, '3312056677660001', 2, '2_e1317f64014f73a8d38c91803556d07c.pdf', '2018-08-09 03:36:36', '2018-08-09 03:36:36'),
	(57, '3312056677660001', 3, '3_3830d65c3c20af9c78be6dfc531e421a.pdf', '2018-08-09 03:36:38', '2018-08-09 03:36:38'),
	(58, '3312056677660001', 4, '4_5fbef0cabfc04ca5a7c04a4a0008b62f.pdf', '2018-08-09 03:36:41', '2018-08-09 03:36:41'),
	(59, '3312056677660001', 5, '5_5895e24b999ef74475e682e77736d3ce.pdf', '2018-08-09 03:36:46', '2018-08-09 03:36:46'),
	(60, '3312056677660001', 6, '6_d57ec7365a84263deaab41f94607eeba.pdf', '2018-08-09 03:36:49', '2018-08-09 03:36:49'),
	(61, '3312056677660001', 7, '7_9692062013b7e91a245391c4c80300b0.pdf', '2018-08-09 03:37:02', '2018-08-09 03:37:02'),
	(62, '3312056677660001', 13, '13_5ce496a05dbc587fffa639277496ff51.pdf', '2018-08-09 03:37:20', '2018-08-09 03:37:20'),
	(63, '1111111111111199', 1, '1_4a9516e23ebdb1ceb54be84bf69b3932.pdf', '2018-09-30 22:08:01', '2018-09-30 22:08:01'),
	(64, '1111111111111199', 2, '2_d2f27ff8b19224d0b92f3fd5a4af1d43.pdf', '2018-09-30 22:08:04', '2018-09-30 22:08:04'),
	(65, '1111111111111199', 3, '3_55ce9bd4ba7755644bf9c4c5a750c0e6.pdf', '2018-09-30 22:08:07', '2018-09-30 22:08:07'),
	(66, '1111111111111199', 4, '4_62a9a26947882b0403fd2dde1c96388e.pdf', '2018-09-30 22:08:13', '2018-09-30 22:08:13'),
	(67, '1111111111111199', 5, '5_c32cb04b0f706964ee1275ea6c10c16e.pdf', '2018-09-30 22:08:16', '2018-09-30 22:08:16'),
	(68, '1111111111111199', 6, '6_189644505de0fd62b3e9b4c3864f4b18.pdf', '2018-09-30 22:08:20', '2018-09-30 22:08:20'),
	(69, '1111111111111199', 7, '7_7c3456694c60a3c363dee877a203cc93.pdf', '2018-09-30 22:08:25', '2018-09-30 22:08:25'),
	(70, '1111111111111199', 13, '13_2d8f6085304a07323582e62d588806ee.pdf', '2018-09-30 22:08:35', '2018-09-30 22:08:35'),
	(78, '1231000123200121', 1, '1_a3b531bf7d8c356946b5cf87c5cb0e7c.pdf', '2018-10-10 17:25:31', '2018-10-10 17:25:31'),
	(79, '1231000123200121', 2, '2_8bd805ceb88c94f7b375ed51e7665084.pdf', '2018-10-10 17:25:34', '2018-10-10 17:25:34'),
	(80, '1231000123200121', 3, '3_704e1e9c25193bbfb2e8428142afee96.pdf', '2018-10-10 17:25:37', '2018-10-10 17:25:37'),
	(81, '1231000123200121', 4, '4_77e64b63b42ef9ad0cce858c1f96487a.pdf', '2018-10-10 17:25:40', '2018-10-10 17:25:40'),
	(82, '1231000123200121', 5, '5_252679836ad74c553267837a4dc2bf34.pdf', '2018-10-10 17:25:45', '2018-10-10 17:25:45'),
	(83, '1231000123200121', 6, '6_241c1aa7140492be802e7e1c943a832e.pdf', '2018-10-10 17:25:48', '2018-10-10 17:25:48'),
	(84, '1231000123200121', 7, '7_3db44993a82f2f5ab7fdbd2a6f0bbbea.pdf', '2018-10-10 17:25:51', '2018-10-10 17:25:51'),
	(85, '1231000123200121', 8, '8_a2b84282b1575a5a4ff79a7f00a1bde0.pdf', '2018-10-10 17:25:54', '2018-10-10 17:25:54'),
	(86, '1231000123200121', 10, '10_e254dae1d844406b00ddb2a207716cff.pdf', '2018-10-10 17:25:59', '2018-10-10 17:25:59'),
	(87, '1231000123200121', 13, '13_8d8d3f746c9dfad2a87ec8b61f67e2fb.pdf', '2018-10-10 17:26:03', '2018-10-10 17:26:03'),
	(93, '1111111111111117', 6, '6_083d926f36256768bc847b1d10d71e53.pdf', '2018-10-11 00:49:26', '2018-10-11 00:49:26'),
	(94, '1231231232121223', 6, '6_2e571ef19400bf999174091cc1bfef8f.pdf', '2018-10-11 02:29:40', '2018-10-11 02:29:40'),
	(95, '2134234234234234', 6, '6_472a96a8f7c07c4c0af4abeb3ba72876.pdf', '2018-10-22 23:38:38', '2018-10-22 23:38:38'),
	(96, '3332323232323232', 6, '6_88540c0ece5356000e65051cdc7d4963.pdf', '2018-10-23 01:08:46', '2018-10-23 01:08:46'),
	(97, '0123129432194314', 6, '6_ad488351d9d685a7e03bc74113e2b961.pdf', '2018-10-23 01:16:37', '2018-10-23 01:16:37'),
	(98, '3171214098700009', 6, '6_6eed7f6f40f27257a45965b0483e535a.pdf', '2018-10-23 11:22:41', '2018-10-23 11:22:41'),
	(99, '3174062304920004', 6, '6_4ce3d0f8dddc23ccb559ac25d9df7f8f.pdf', '2018-10-23 11:39:42', '2018-10-23 11:39:42'),
	(100, '3674044210970006', 6, '6_215f701214f9e334662449c32b635c33.png', '2018-10-23 11:46:38', '2018-10-23 11:46:38'),
	(101, '1242134325345643', 6, '6_468363cff3575eb34b4f2725c4f1812d.pdf', '2018-10-23 11:48:10', '2018-10-23 11:48:10'),
	(106, '3751000000000000', 6, '6_00a965017075e6bcbd7ceccfcd8bf4b6.pdf', '2018-10-23 11:50:42', '2018-10-23 11:50:42'),
	(107, '5325226363747474', 6, '6_d7b1b3d1d6b683179bdb1d41d3a0ae0d.pdf', '2018-10-23 12:41:57', '2018-10-23 12:41:57'),
	(108, '2233555667909877', 6, '6_c4d8cdfdde67be0d33a6ccab1a67b252.pdf', '2018-10-23 12:47:08', '2018-10-23 12:47:08'),
	(113, '3124124314233542', 6, '6_2115c370e657059c15e613d453d84302.pdf', '2018-10-23 13:06:45', '2018-10-23 13:06:45'),
	(114, '3276012212760006', 6, '6_eb451b1081a0c11e2e44c8f8c0ec0374.jpg', '2018-10-23 13:17:36', '2018-10-23 13:17:36'),
	(115, '1234567489789456', 6, '6_ef9cd4fc0dac3cee4c0fa137f5e41812.pdf', '2018-10-23 22:58:42', '2018-10-23 22:58:42'),
	(116, '3421354125125412', 6, '6_ba0e9b50c2b0a18044249d26023e683a.pdf', '2018-10-24 00:09:14', '2018-10-24 00:09:14'),
	(117, '7127394858669559', 6, '6_489016845eb35aef7ac51c698fc44cbf.pdf', '2018-10-24 08:50:36', '2018-10-24 08:50:36');
/*!40000 ALTER TABLE `d_debitur_dok` ENABLE KEYS */;

-- Dumping structure for table dp0.d_debitur_dok_temp
DROP TABLE IF EXISTS `d_debitur_dok_temp`;
CREATE TABLE IF NOT EXISTS `d_debitur_dok_temp` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `sesi_upload` varchar(255) NOT NULL DEFAULT '0',
  `id_dok` int(11) DEFAULT NULL,
  `nmfile` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_d_debitur_dok_t_dok` (`id_dok`),
  CONSTRAINT `d_debitur_dok_temp_ibfk_2` FOREIGN KEY (`id_dok`) REFERENCES `t_dok` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=41 DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT;

-- Dumping data for table dp0.d_debitur_dok_temp: ~17 rows (approximately)
DELETE FROM `d_debitur_dok_temp`;
/*!40000 ALTER TABLE `d_debitur_dok_temp` DISABLE KEYS */;
INSERT INTO `d_debitur_dok_temp` (`id`, `sesi_upload`, `id_dok`, `nmfile`, `created_at`, `updated_at`) VALUES
	(1, '4318bfbba5f2d056c79976f115c51505', 1, '1_f5387bff725246bc97e5c8c1604edb69.png', '2018-08-04 14:44:37', '2018-08-04 14:44:37'),
	(2, '4318bfbba5f2d056c79976f115c51505', 2, '2_a28d4a289faed43653cc266f9a87c08a.png', '2018-08-04 15:00:35', '2018-08-04 15:00:35'),
	(3, '4318bfbba5f2d056c79976f115c51505', 3, '3_ce9239e1448dc98ab51a3e85c0940221.png', '2018-08-04 15:00:39', '2018-08-04 15:00:39'),
	(4, '4318bfbba5f2d056c79976f115c51505', 4, '4_89091a94af68f0f6c1be38f3451aeb8b.png', '2018-08-04 15:00:42', '2018-08-04 15:00:42'),
	(5, '4318bfbba5f2d056c79976f115c51505', 5, '5_911c4cc2cd8f6b20c13073567a069cf5.png', '2018-08-04 15:00:45', '2018-08-04 15:00:45'),
	(6, '4318bfbba5f2d056c79976f115c51505', 6, '6_af1b2d42978cc67f4a29232c77e48670.png', '2018-08-04 15:00:58', '2018-08-04 15:00:58'),
	(7, '4318bfbba5f2d056c79976f115c51505', 7, '7_9ae70fa7035e8ee865337994db321d76.png', '2018-08-04 15:01:01', '2018-08-04 15:01:01'),
	(8, '4318bfbba5f2d056c79976f115c51505', 8, '8_177db5349c70d440bb7faf614a7b7f5e.png', '2018-08-04 15:01:06', '2018-08-04 15:01:06'),
	(9, '4318bfbba5f2d056c79976f115c51505', 9, '9_6955eea0d1562e454cad3d4b110e2889.png', '2018-08-04 15:01:11', '2018-08-04 15:01:11'),
	(10, '4318bfbba5f2d056c79976f115c51505', 13, '13_b4607f9d365f57b832e9be279d17f0b0.png', '2018-08-04 15:01:18', '2018-08-04 15:01:18'),
	(11, 'ceef745541857297b2b14a93f96fe4a5', 1, '1_ac7c5383e7a93e5be428c03d30ae62d1.png', '2018-08-04 23:44:22', '2018-08-04 23:44:22'),
	(12, 'ceef745541857297b2b14a93f96fe4a5', 2, '2_6198a410ac7dfe32b391f5cec48c6e93.png', '2018-08-04 23:45:34', '2018-08-04 23:45:34'),
	(13, 'ceef745541857297b2b14a93f96fe4a5', 3, '3_bb953a7292f6da6a9d268fd25131aae2.png', '2018-08-04 23:45:43', '2018-08-04 23:45:43'),
	(19, 'ce48b8e6249bc65f76b563d891f986a4', 6, '6_7953c0e5647f6a0be7d78ed0dacf8d20.png', '2018-10-23 10:58:52', '2018-10-23 10:58:52'),
	(20, '6601c6d400246d3ea6ac13047e3423df', 6, '6_8d0a66af5f7637bd35bbed97c77cce1d.pdf', '2018-10-23 11:09:21', '2018-10-23 11:09:21'),
	(26, 'e72ce2a45042f542fe7a3a72b1f7febc', 6, '6_9688fe7933068c98adebd85def61428f.png', '2018-10-23 11:49:34', '2018-10-23 11:49:34'),
	(31, '0dc52e98478af98b6f3ed935e975e460', 6, '6_8df4dbb68e128a81db685bcfcbded020.pdf', '2018-10-23 12:58:54', '2018-10-23 12:58:54'),
	(39, '6c47bc599dba30172990a86602805798', 6, '6_4255099166ef68d23fe190e4be045209.pdf', '2018-10-29 10:03:04', '2018-10-29 10:03:04'),
	(40, '432150b5ca807336bb5ba40261940107', 6, '6_3a7e78c0317688c2c9b67bfaf39fab43.pdf', '2018-10-29 10:09:45', '2018-10-29 10:09:45');
/*!40000 ALTER TABLE `d_debitur_dok_temp` ENABLE KEYS */;

-- Dumping structure for table dp0.d_debitur_dukcapil
DROP TABLE IF EXISTS `d_debitur_dukcapil`;
CREATE TABLE IF NOT EXISTS `d_debitur_dukcapil` (
  `nik` varchar(16) NOT NULL,
  `nokk` varchar(16) NOT NULL,
  `nama` varchar(255) NOT NULL,
  `tglhr` date NOT NULL,
  `kotalhr` varchar(255) NOT NULL,
  `kdkelamin` int(11) NOT NULL,
  `kdagama` int(11) NOT NULL,
  `kdpekerjaan` int(11) NOT NULL,
  `kdkawin` int(11) NOT NULL,
  `kdprop` varchar(2) NOT NULL,
  `kdkabkota` varchar(2) NOT NULL,
  `kdkec` varchar(2) NOT NULL,
  `kdkel` varchar(4) NOT NULL,
  `alamat` varchar(255) NOT NULL,
  `rt` int(11) NOT NULL,
  `rw` int(11) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`nik`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT;

-- Dumping data for table dp0.d_debitur_dukcapil: ~3 rows (approximately)
DELETE FROM `d_debitur_dukcapil`;
/*!40000 ALTER TABLE `d_debitur_dukcapil` DISABLE KEYS */;
INSERT INTO `d_debitur_dukcapil` (`nik`, `nokk`, `nama`, `tglhr`, `kotalhr`, `kdkelamin`, `kdagama`, `kdpekerjaan`, `kdkawin`, `kdprop`, `kdkabkota`, `kdkec`, `kdkel`, `alamat`, `rt`, `rw`, `created_at`, `updated_at`) VALUES
	('1111111111111111', '2222222222222222', 'Test aja', '1970-01-01', 'Sukabumi', 1, 1, 5, 2, '31', '73', '01', '1001', 'Teeadcds', 9, 3, '2018-08-05 11:46:44', '2018-08-05 11:46:44'),
	('1111111111111113', '3333333333333333', 'Test lagi', '2018-08-05', 'Test', 1, 1, 5, 2, '31', '75', '01', '1001', 'Test', 10, 2, '2018-08-05 18:01:41', NULL),
	('3222222222222222', '1111111111111133', 'Sony', '1988-07-01', 'Kotabumi', 1, 1, 5, 2, '31', '73', '01', '1001', 'Test', 1, 1, '2018-08-09 03:23:19', NULL);
/*!40000 ALTER TABLE `d_debitur_dukcapil` ENABLE KEYS */;

-- Dumping structure for table dp0.d_debitur_hunian
DROP TABLE IF EXISTS `d_debitur_hunian`;
CREATE TABLE IF NOT EXISTS `d_debitur_hunian` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nik` varchar(16) NOT NULL,
  `id_hunian_dtl` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nik` (`nik`,`id_hunian_dtl`),
  KEY `id_hunian_dtl` (`id_hunian_dtl`),
  CONSTRAINT `d_debitur_hunian_ibfk_1` FOREIGN KEY (`nik`) REFERENCES `d_debitur` (`nik`),
  CONSTRAINT `d_debitur_hunian_ibfk_2` FOREIGN KEY (`id_hunian_dtl`) REFERENCES `d_hunian_dtl` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=latin1;

-- Dumping data for table dp0.d_debitur_hunian: ~15 rows (approximately)
DELETE FROM `d_debitur_hunian`;
/*!40000 ALTER TABLE `d_debitur_hunian` DISABLE KEYS */;
INSERT INTO `d_debitur_hunian` (`id`, `nik`, `id_hunian_dtl`) VALUES
	(3, '0123129432194314', 3),
	(21, '1234567489789456', 5),
	(7, '1242134325345643', 5),
	(1, '2134234234234234', 3),
	(14, '2233555667909877', 5),
	(19, '3124124314233542', 4),
	(4, '3171214098700009', 5),
	(5, '3174062304920004', 6),
	(20, '3276012212760006', 1),
	(2, '3332323232323232', 2),
	(22, '3421354125125412', 5),
	(6, '3674044210970006', 6),
	(12, '3751000000000000', 1),
	(13, '5325226363747474', 2),
	(23, '7127394858669559', 5);
/*!40000 ALTER TABLE `d_debitur_hunian` ENABLE KEYS */;

-- Dumping structure for table dp0.d_debitur_hutang
DROP TABLE IF EXISTS `d_debitur_hutang`;
CREATE TABLE IF NOT EXISTS `d_debitur_hutang` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `nik` varchar(16) DEFAULT NULL,
  `kdhutang` varchar(1) DEFAULT NULL,
  `kdkreditur` varchar(1) DEFAULT NULL,
  `total` decimal(18,0) DEFAULT NULL,
  `angsuran` decimal(18,0) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `Index 2` (`nik`,`kdhutang`),
  KEY `FK_d_debitur_hutang_t_hutang` (`kdhutang`),
  KEY `kdkreditur` (`kdkreditur`),
  CONSTRAINT `FK_d_debitur_hutang_d_debitur` FOREIGN KEY (`nik`) REFERENCES `d_debitur` (`nik`),
  CONSTRAINT `FK_d_debitur_hutang_t_hutang` FOREIGN KEY (`kdhutang`) REFERENCES `t_hutang` (`kdhutang`),
  CONSTRAINT `d_debitur_hutang_ibfk_1` FOREIGN KEY (`kdkreditur`) REFERENCES `t_kreditur` (`kdkreditur`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT;

-- Dumping data for table dp0.d_debitur_hutang: ~19 rows (approximately)
DELETE FROM `d_debitur_hutang`;
/*!40000 ALTER TABLE `d_debitur_hutang` DISABLE KEYS */;
INSERT INTO `d_debitur_hutang` (`id`, `nik`, `kdhutang`, `kdkreditur`, `total`, `angsuran`, `created_at`, `updated_at`) VALUES
	(1, '1111111111111111', '1', '1', 100000000, 2000000, '2018-08-04 16:31:55', '2018-08-04 16:31:55'),
	(3, '1111111111111113', '1', '1', 100000000, 2500000, '2018-08-04 21:57:29', '2018-08-04 21:57:29'),
	(4, '3222222222222222', '1', '1', 100000000, 1000000, '2018-08-06 00:08:00', '2018-08-06 00:08:00'),
	(5, '3312056677660001', '1', '1', 20000000, 1000000, '2018-08-09 03:39:03', '2018-08-09 03:39:03'),
	(6, '1111111111111199', '2', '1', 10000000, 500000, '2018-09-30 22:09:42', '2018-09-30 22:09:42'),
	(7, '1111111111111117', '1', NULL, 100000000, 1000000, '2018-10-11 02:08:47', '2018-10-11 02:08:47'),
	(8, '1231231232121223', '2', '1', 100000000, 2000000, '2018-10-11 02:41:47', '2018-10-11 02:41:47'),
	(9, '2134234234234234', '1', '1', 100000000, 2000000, '2018-10-22 23:47:42', '2018-10-22 23:47:42'),
	(10, '3332323232323232', '1', '1', 100000000, 1500000, '2018-10-23 01:14:38', '2018-10-23 01:14:38'),
	(11, '3171214098700009', '2', '1', 30000000, 2000000, '2018-10-23 11:22:46', '2018-10-23 11:22:46'),
	(12, '3174062304920004', '1', '1', 2500000, 120000, '2018-10-23 11:39:50', '2018-10-23 11:39:50'),
	(13, '1242134325345643', '2', '3', 10000000, 1000000, '2018-10-23 11:48:10', '2018-10-23 11:48:10'),
	(14, '5325226363747474', '2', '3', 7000000, 1300000, '2018-10-23 12:42:03', '2018-10-23 12:42:03'),
	(15, '2233555667909877', '2', '2', 7000000, 1200000, '2018-10-23 12:47:13', '2018-10-23 12:47:13'),
	(20, '3124124314233542', '2', '3', 3000000, 500000, '2018-10-23 13:07:52', '2018-10-23 13:07:52'),
	(21, '3276012212760006', '2', '1', 172000000, 5700000, '2018-10-23 13:19:40', '2018-10-23 13:19:40'),
	(22, '1234567489789456', '4', '3', 1500000, 150000, '2018-10-23 22:58:56', '2018-10-23 22:58:56'),
	(23, '3421354125125412', '3', '1', 1000000, 300000, '2018-10-24 00:10:46', '2018-10-24 00:10:46'),
	(24, '7127394858669559', '2', '3', 2000000, 440000, '2018-10-24 08:55:25', '2018-10-24 08:55:25');
/*!40000 ALTER TABLE `d_debitur_hutang` ENABLE KEYS */;

-- Dumping structure for table dp0.d_debitur_pasangan
DROP TABLE IF EXISTS `d_debitur_pasangan`;
CREATE TABLE IF NOT EXISTS `d_debitur_pasangan` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `nik` varchar(16) DEFAULT NULL,
  `nik_p` varchar(16) DEFAULT NULL,
  `nama` varchar(255) DEFAULT NULL,
  `tglhr` date DEFAULT NULL,
  `kotalhr` varchar(255) DEFAULT NULL,
  `kdkelamin` int(11) DEFAULT NULL,
  `kdagama` int(11) DEFAULT NULL,
  `kdpekerjaan` int(11) DEFAULT NULL,
  `kdpendidikan` int(11) DEFAULT NULL,
  `is_alm_pemohon` varchar(1) DEFAULT NULL,
  `kdprop` varchar(2) DEFAULT NULL,
  `kdkabkota` varchar(2) DEFAULT NULL,
  `kdkec` varchar(2) DEFAULT NULL,
  `kdkel` varchar(4) DEFAULT NULL,
  `kodepos` varchar(5) DEFAULT NULL,
  `alamat` varchar(255) DEFAULT NULL,
  `telp` varchar(20) DEFAULT NULL,
  `nohp` varchar(20) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `cek_dukcapil` varchar(1) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `Index 2` (`nik`,`nik_p`),
  KEY `FK_d_debitur_pasangan_t_kelamin` (`kdkelamin`),
  KEY `FK_d_debitur_pasangan_t_agama` (`kdagama`),
  KEY `FK_d_debitur_pasangan_t_pekerjaan` (`kdpekerjaan`),
  KEY `FK_d_debitur_pasangan_t_pendidikan` (`kdpendidikan`),
  KEY `FK_d_debitur_pasangan_t_kelurahan` (`kdprop`,`kdkabkota`,`kdkec`,`kdkel`),
  CONSTRAINT `FK_d_debitur_pasangan_d_debitur` FOREIGN KEY (`nik`) REFERENCES `d_debitur` (`nik`),
  CONSTRAINT `FK_d_debitur_pasangan_t_pekerjaan` FOREIGN KEY (`kdpekerjaan`) REFERENCES `t_pekerjaan` (`kdpekerjaan`)
) ENGINE=InnoDB AUTO_INCREMENT=44 DEFAULT CHARSET=latin1;

-- Dumping data for table dp0.d_debitur_pasangan: ~22 rows (approximately)
DELETE FROM `d_debitur_pasangan`;
/*!40000 ALTER TABLE `d_debitur_pasangan` DISABLE KEYS */;
INSERT INTO `d_debitur_pasangan` (`id`, `nik`, `nik_p`, `nama`, `tglhr`, `kotalhr`, `kdkelamin`, `kdagama`, `kdpekerjaan`, `kdpendidikan`, `is_alm_pemohon`, `kdprop`, `kdkabkota`, `kdkec`, `kdkel`, `kodepos`, `alamat`, `telp`, `nohp`, `email`, `created_at`, `updated_at`, `cek_dukcapil`) VALUES
	(1, '1111111111111111', '2222222222222222', 'test', '0000-00-00', 'test', 2, 3, NULL, 5, NULL, '31', '71', '02', '1002', '12345', 'test', '123', '123', 'test@gmail.com', '2018-08-04 16:31:55', '2018-08-04 16:31:55', NULL),
	(3, '1111111111111113', '5555555555555555', 'test', '0000-00-00', 'test', 2, 1, NULL, 5, NULL, '31', '75', '01', '1006', '12345', 'test', '123', '12345', 'test@gmail.com', '2018-08-04 21:57:29', '2018-08-04 21:57:29', NULL),
	(4, '12312143566788', '1234455677889990', 'tes', '0000-00-00', 'jakata', 2, 1, NULL, 5, NULL, '31', '73', '01', '1002', '12334', 'jakarta', '12233', '1223456677', 'test@gmail.com', '2018-08-05 00:09:23', '2018-08-05 00:09:23', NULL),
	(5, '1111111111111199', '2222222222222299', 'test', '0000-00-00', 'test', 2, 1, NULL, 3, NULL, '31', '71', '01', '1001', '12345', 'test', '12345', '021', 'test@gmail.com', '2018-09-30 22:09:42', '2018-09-30 22:09:42', NULL),
	(6, '1231000123200121', '1231100000001231', 'Rima', '0000-00-00', 'Banjarmasin', 2, 1, NULL, 4, NULL, '31', '71', '01', '1001', '12345', 'test', '021', '0810000', 'test@gmail.com', '2018-10-10 17:27:02', '2018-10-10 17:27:02', NULL),
	(9, '1111111111111117', '1111111111111118', 'test', '0000-00-00', '', 0, 0, NULL, 0, NULL, '', '', '', '', '', '', '', '123', '', '2018-10-11 02:08:47', '2018-10-11 02:08:47', NULL),
	(13, '1231231232121223', '1232132131232132', 'test', '0000-00-00', '', 0, 0, NULL, 0, '0', '', '', '', '', '', '', '', '123', '', '2018-10-11 02:41:47', '2018-10-11 02:41:47', NULL),
	(16, '2134234234234234', '2354356456456456', 'test', '0000-00-00', '', 0, 0, NULL, 0, '0', '31', '71', '01', '1001', '23432', 'test', '', '23525435345', '', '2018-10-22 23:47:42', '2018-10-22 23:47:42', NULL),
	(17, '3332323232323232', '2345778999999999', 'test', '0000-00-00', '', 0, 0, NULL, 0, '1', '', '', '', '', '', '', '', '000000', '', '2018-10-23 01:14:37', '2018-10-23 01:14:37', NULL),
	(18, '0123129432194314', '2342342342342342', '53w5', '0000-00-00', '', 0, 0, NULL, 0, '1', '', '', '', '', '', '', '', '081', '', '2018-10-23 01:17:12', '2018-10-23 01:17:12', NULL),
	(19, '3171214098700009', '3231731234941889', 'suka suka saya', '0000-00-00', '', 0, 0, NULL, 0, '1', '', '', '', '', '', '', '', '08192839293', '', '2018-10-23 11:22:46', '2018-10-23 11:22:46', NULL),
	(20, '3174062304920004', '3174062303920004', 'Atun', '0000-00-00', '', 0, 0, NULL, 0, '1', '', '', '', '', '', '', '', '0214557885', '', '2018-10-23 11:39:50', '2018-10-23 11:39:50', NULL),
	(21, '3674044210970006', '3175121212121212', 'jhjhjhjhjhjhjhjhjhjhj', '0000-00-00', '', 0, 0, NULL, 0, '1', '', '', '', '', '', '', '', '21321321321321321321', '', '2018-10-23 11:46:46', '2018-10-23 11:46:46', NULL),
	(22, '1242134325345643', '6345234125437457', 'susi', '0000-00-00', '', 0, 0, NULL, 0, '1', '', '', '', '', '', '', '', '23523513451356436767', '', '2018-10-23 11:48:10', '2018-10-23 11:48:10', NULL),
	(27, '3751000000000000', '2343545454555555', 'andrew', '0000-00-00', '', 0, 0, NULL, 0, '1', '', '', '', '', '', '', '', '08568721583', '', '2018-10-23 11:50:45', '2018-10-23 11:50:45', NULL),
	(28, '5325226363747474', '2637374848483992', 'elfry', '0000-00-00', '', 0, 0, NULL, 0, '1', '', '', '', '', '', '', '', '08333564478', '', '2018-10-23 12:42:03', '2018-10-23 12:42:03', NULL),
	(29, '2233555667909877', '7822003933547857', 'aura', '0000-00-00', '', 0, 0, NULL, 0, '1', '', '', '', '', '', '', '', '08134456773', '', '2018-10-23 12:47:13', '2018-10-23 12:47:13', NULL),
	(35, '3124124314233542', '2343243242341214', 'tebak nama saya', '0000-00-00', '', 0, 0, NULL, 0, '1', '', '', '', '', '', '', '', '9848433294', '', '2018-10-23 13:07:52', '2018-10-23 13:07:52', NULL),
	(36, '3276012212760006', '3276014606800006', 'nurhikmah', '0000-00-00', '', 0, 0, NULL, 0, '1', '', '', '', '', '', '', '', '082260979477', '', '2018-10-23 13:19:40', '2018-10-23 13:19:40', NULL),
	(37, '1234567489789456', '1234567891376164', 'Istri', '0000-00-00', '', 0, 0, NULL, 0, '1', '', '', '', '', '', '', '', '0812154234213', '', '2018-10-23 22:58:56', '2018-10-23 22:58:56', NULL),
	(38, '3421354125125412', '1231513524235124', 'gogo', '0000-00-00', '', 0, 0, NULL, 0, '1', '', '', '', '', '', '', '', '081222225555', '', '2018-10-24 00:10:45', '2018-10-24 00:10:45', NULL),
	(39, '7127394858669559', '9889796555008876', 'Zakaria Anshor', '0000-00-00', '', 0, 0, NULL, 0, '1', '', '', '', '', '', '', '', '08998567244', '', '2018-10-24 08:55:25', '2018-10-24 08:55:25', NULL);
/*!40000 ALTER TABLE `d_debitur_pasangan` ENABLE KEYS */;

-- Dumping structure for table dp0.d_debitur_pekerjaan
DROP TABLE IF EXISTS `d_debitur_pekerjaan`;
CREATE TABLE IF NOT EXISTS `d_debitur_pekerjaan` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `nik` varchar(16) NOT NULL,
  `nmkantor` varchar(255) DEFAULT NULL,
  `bidang` varchar(255) DEFAULT NULL,
  `jenis` varchar(255) DEFAULT NULL,
  `alamat` varchar(255) DEFAULT NULL,
  `jabatan` varchar(255) DEFAULT NULL,
  `atasan` varchar(255) DEFAULT NULL,
  `telp` varchar(20) DEFAULT NULL,
  `tgkerja` date DEFAULT NULL,
  `penghasilan` decimal(18,0) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `Index 2` (`nmkantor`,`nik`),
  KEY `FK_d_debitur_pekerjaan_d_debitur` (`nik`),
  CONSTRAINT `FK_d_debitur_pekerjaan_d_debitur` FOREIGN KEY (`nik`) REFERENCES `d_debitur` (`nik`)
) ENGINE=InnoDB AUTO_INCREMENT=44 DEFAULT CHARSET=latin1;

-- Dumping data for table dp0.d_debitur_pekerjaan: ~24 rows (approximately)
DELETE FROM `d_debitur_pekerjaan`;
/*!40000 ALTER TABLE `d_debitur_pekerjaan` DISABLE KEYS */;
INSERT INTO `d_debitur_pekerjaan` (`id`, `nik`, `nmkantor`, `bidang`, `jenis`, `alamat`, `jabatan`, `atasan`, `telp`, `tgkerja`, `penghasilan`, `created_at`, `updated_at`) VALUES
	(1, '1111111111111111', 'test', '-', '-', '-', '-', '-', '123', '2018-08-01', 10000000, '2018-08-04 16:31:55', '2018-08-04 16:31:55'),
	(3, '1111111111111113', 'test', '-', '-', '-', '-', '-', '123', '2018-08-01', 10000000, '2018-08-04 21:57:29', '2018-08-04 21:57:29'),
	(4, '12312143566788', 'PT', 'test', 'test', 'test', 'staff', 'Bapak test', '1234456', '2015-06-16', 5000000, '2018-08-05 00:09:23', '2018-08-05 00:09:23'),
	(5, '3222222222222222', '-', '-', '-', '-', '-', '-', '123', '2018-08-01', 10000000, '2018-08-06 00:08:00', '2018-08-06 00:08:00'),
	(6, '3312056677660001', 'Kantor Sosial', '-', '-', '-', '-', '-', '02112345', '2009-01-01', 7000000, '2018-08-09 03:39:03', '2018-08-09 03:39:03'),
	(7, '1111111111111199', 'Kantor Pemda', 'Keuangan', '-', '-', '-', '-', '021', '2010-01-20', 5000000, '2018-09-30 22:09:42', '2018-09-30 22:09:42'),
	(8, '1231000123200121', 'Kantor Keuangan', '-', '-', '-', '-', '-', '-', '2012-02-01', 7000000, '2018-10-10 17:27:02', '2018-10-10 17:27:02'),
	(9, '1111111111111117', '-', '', '', '-', '', '', '-', '0000-00-00', 5000000, '2018-10-11 02:08:47', '2018-10-11 02:08:47'),
	(13, '1231231232121223', 'test', '', '', '-', '', '', '-', '0000-00-00', 10000000, '2018-10-11 02:41:47', '2018-10-11 02:41:47'),
	(16, '2134234234234234', 'test', '', '', 'test', '', '', '', '0000-00-00', 10000000, '2018-10-22 23:47:42', '2018-10-22 23:47:42'),
	(17, '3332323232323232', 'Kantor Kimpet', '', '', '-', '', '', '-', '0000-00-00', 10000000, '2018-10-23 01:14:37', '2018-10-23 01:14:37'),
	(18, '0123129432194314', '-', '', '', '-', '', '', '-', '0000-00-00', 10000000, '2018-10-23 01:17:12', '2018-10-23 01:17:12'),
	(19, '3171214098700009', 'Kantor DPR RI', '', '', 'Jl Gatsu', '', '', '021830381', '0000-00-00', 20000000, '2018-10-23 11:22:46', '2018-10-23 11:22:46'),
	(20, '3174062304920004', 'jakarta', '', '', 'jakarta', '', '', '5855251401', '0000-00-00', 5000000, '2018-10-23 11:39:50', '2018-10-23 11:39:50'),
	(21, '3674044210970006', 'ajjajajajjajajjajaj', '', '', 'jjajajajjajjajajjajaj', '', '', '12121212123213213123', '0000-00-00', 12145454, '2018-10-23 11:46:46', '2018-10-23 11:46:46'),
	(22, '1242134325345643', 'PT. SUSAH HIDUP', '', '', 'Di Jakarta', '', '', '00', '0000-00-00', 7000000, '2018-10-23 11:48:10', '2018-10-23 11:48:10'),
	(27, '3751000000000000', 'wewe', '', '', 'wewe', '', '', '58356230', '0000-00-00', 4000000, '2018-10-23 11:50:45', '2018-10-23 11:50:45'),
	(28, '5325226363747474', 'kantor walikota jakarta utara', '', '', 'jl. laksda', '', '', '0217865565', '0000-00-00', 5000000, '2018-10-23 12:42:03', '2018-10-23 12:42:03'),
	(29, '2233555667909877', 'jl,ahshgjhffjssa', '', '', 'akakslldkjfhddbs', '', '', '091172663', '0000-00-00', 5000000, '2018-10-23 12:47:13', '2018-10-23 12:47:13'),
	(35, '3124124314233542', 'kantor tentara', '', '', 'disana', '', '', '0839490224', '0000-00-00', 5000000, '2018-10-23 13:07:52', '2018-10-23 13:07:52'),
	(36, '3276012212760006', 'Dinas', '', '', 'Jati baru', '', '', '021 7242504', '0000-00-00', 7, '2018-10-23 13:19:40', '2018-10-23 13:19:40'),
	(37, '1234567489789456', 'Kantor kehakiman', '', '', '.', '', '', '021', '0000-00-00', 7000000, '2018-10-23 22:58:56', '2018-10-23 22:58:56'),
	(38, '3421354125125412', 'Maju mundur', '', '', '.', '', '', '0214755506', '0000-00-00', 5000000, '2018-10-24 00:10:46', '2018-10-24 00:10:46'),
	(39, '7127394858669559', 'Kantor Walikota Jakarta Utara', '', '', 'Jl.Laksda Yos Sudarso 27-29', '', '', '0219540003', '0000-00-00', 4500000, '2018-10-24 08:55:25', '2018-10-24 08:55:25');
/*!40000 ALTER TABLE `d_debitur_pekerjaan` ENABLE KEYS */;

-- Dumping structure for table dp0.d_debitur_pekerjaan_p
DROP TABLE IF EXISTS `d_debitur_pekerjaan_p`;
CREATE TABLE IF NOT EXISTS `d_debitur_pekerjaan_p` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `nik` varchar(16) NOT NULL,
  `nmkantor` varchar(255) DEFAULT NULL,
  `bidang` varchar(255) DEFAULT NULL,
  `jenis` varchar(255) DEFAULT NULL,
  `alamat` varchar(255) DEFAULT NULL,
  `jabatan` varchar(255) DEFAULT NULL,
  `atasan` varchar(255) DEFAULT NULL,
  `telp` varchar(20) DEFAULT NULL,
  `tgkerja` date DEFAULT NULL,
  `penghasilan` decimal(18,0) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `Index 2` (`nmkantor`,`nik`),
  KEY `FK_d_debitur_pekerjaan_p_d_debitur` (`nik`),
  CONSTRAINT `FK_d_debitur_pekerjaan_p_d_debitur` FOREIGN KEY (`nik`) REFERENCES `d_debitur` (`nik`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT;

-- Dumping data for table dp0.d_debitur_pekerjaan_p: ~7 rows (approximately)
DELETE FROM `d_debitur_pekerjaan_p`;
/*!40000 ALTER TABLE `d_debitur_pekerjaan_p` DISABLE KEYS */;
INSERT INTO `d_debitur_pekerjaan_p` (`id`, `nik`, `nmkantor`, `bidang`, `jenis`, `alamat`, `jabatan`, `atasan`, `telp`, `tgkerja`, `penghasilan`, `created_at`, `updated_at`) VALUES
	(1, '1111111111111111', 'test', '-', '-', '-', '-', '-', '123', '2018-08-01', 5000000, '2018-08-04 16:31:55', '2018-08-04 16:31:55'),
	(3, '1111111111111113', 'test', '-', '-', '-', '-', '-', '123', '2018-08-01', 5000000, '2018-08-04 21:57:29', '2018-08-04 21:57:29'),
	(4, '1231231232121223', '-', '', '', '-', '', '', '-', '0000-00-00', 3000000, '2018-10-11 02:41:47', '2018-10-11 02:41:47'),
	(6, '2134234234234234', 'Dukun', '', '', '-', '', '', '', '0000-00-00', 5000000, '2018-10-22 23:47:42', '2018-10-22 23:47:42'),
	(7, '3332323232323232', 'Kantor Cuk', '', '', '-', '', '', '-', '0000-00-00', 5000000, '2018-10-23 01:14:38', '2018-10-23 01:14:38'),
	(8, '3751000000000000', 'wewe', '', '', 'wewe', '', '', '58356230', '0000-00-00', 7000000, '2018-10-23 11:50:45', '2018-10-23 11:50:45'),
	(9, '3124124314233542', 'dagang ', '', '', 'dimana aja', '', '', '948328595', '0000-00-00', 2000000, '2018-10-23 13:07:52', '2018-10-23 13:07:52');
/*!40000 ALTER TABLE `d_debitur_pekerjaan_p` ENABLE KEYS */;

-- Dumping structure for table dp0.d_debitur_skoring
DROP TABLE IF EXISTS `d_debitur_skoring`;
CREATE TABLE IF NOT EXISTS `d_debitur_skoring` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `nik` varchar(16) NOT NULL,
  `nilai` int(11) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `Index 2` (`nik`),
  CONSTRAINT `FK_d_debitur_skoring_d_debitur` FOREIGN KEY (`nik`) REFERENCES `d_debitur` (`nik`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=latin1;

-- Dumping data for table dp0.d_debitur_skoring: ~2 rows (approximately)
DELETE FROM `d_debitur_skoring`;
/*!40000 ALTER TABLE `d_debitur_skoring` DISABLE KEYS */;
INSERT INTO `d_debitur_skoring` (`id`, `nik`, `nilai`, `created_at`, `updated_at`) VALUES
	(11, '1111111111111113', 76, '2018-08-05 21:30:13', '2018-08-05 21:30:13'),
	(13, '1111111111111111', 68, '2018-08-05 21:34:41', '2018-08-05 21:34:41');
/*!40000 ALTER TABLE `d_debitur_skoring` ENABLE KEYS */;

-- Dumping structure for table dp0.d_form
DROP TABLE IF EXISTS `d_form`;
CREATE TABLE IF NOT EXISTS `d_form` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `kdpetugas` varchar(5) NOT NULL,
  `tahun` varchar(4) NOT NULL,
  `nourut` int(11) NOT NULL,
  `status` int(11) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `Index 2` (`nourut`,`tahun`,`kdpetugas`),
  KEY `FK_d_form_t_petugas` (`kdpetugas`),
  KEY `FK_d_form_t_status_form` (`status`),
  CONSTRAINT `FK_d_form_t_petugas` FOREIGN KEY (`kdpetugas`) REFERENCES `t_petugas` (`kdpetugas`),
  CONSTRAINT `FK_d_form_t_status_form` FOREIGN KEY (`status`) REFERENCES `t_status_form` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=580 DEFAULT CHARSET=latin1;

-- Dumping data for table dp0.d_form: ~403 rows (approximately)
DELETE FROM `d_form`;
/*!40000 ALTER TABLE `d_form` DISABLE KEYS */;
INSERT INTO `d_form` (`id`, `kdpetugas`, `tahun`, `nourut`, `status`, `created_at`, `updated_at`) VALUES
	(151, '00001', '2018', 1, 1, '2018-10-23 00:48:03', '2018-10-23 01:14:38'),
	(152, '00001', '2018', 2, 1, '2018-10-23 00:48:03', '2018-10-23 01:17:12'),
	(153, '00001', '2018', 3, 0, '2018-10-23 00:48:03', '2018-10-23 00:48:03'),
	(154, '00001', '2018', 4, 0, '2018-10-23 00:48:03', '2018-10-23 00:48:03'),
	(155, '00001', '2018', 5, 0, '2018-10-23 00:48:03', '2018-10-23 00:48:03'),
	(156, '00001', '2018', 6, 0, '2018-10-23 00:48:03', '2018-10-23 00:48:03'),
	(157, '00001', '2018', 7, 0, '2018-10-23 00:48:03', '2018-10-23 00:48:03'),
	(158, '00001', '2018', 8, 0, '2018-10-23 00:48:03', '2018-10-23 00:48:03'),
	(159, '00001', '2018', 9, 0, '2018-10-23 00:48:03', '2018-10-23 00:48:03'),
	(160, '00001', '2018', 10, 0, '2018-10-23 00:48:03', '2018-10-23 00:48:03'),
	(161, '00001', '2018', 11, 0, '2018-10-23 00:48:03', '2018-10-23 00:48:03'),
	(162, '00001', '2018', 12, 0, '2018-10-23 00:48:03', '2018-10-23 00:48:03'),
	(163, '00001', '2018', 13, 0, '2018-10-23 00:48:03', '2018-10-23 00:48:03'),
	(164, '00001', '2018', 14, 0, '2018-10-23 00:48:03', '2018-10-23 00:48:03'),
	(165, '00001', '2018', 15, 0, '2018-10-23 00:48:03', '2018-10-23 00:48:03'),
	(166, '00001', '2018', 16, 0, '2018-10-23 00:48:03', '2018-10-23 00:48:03'),
	(167, '00001', '2018', 17, 0, '2018-10-23 00:48:03', '2018-10-23 00:48:03'),
	(168, '00001', '2018', 18, 0, '2018-10-23 00:48:03', '2018-10-23 00:48:03'),
	(169, '00001', '2018', 19, 0, '2018-10-23 00:48:03', '2018-10-23 00:48:03'),
	(170, '00001', '2018', 20, 0, '2018-10-23 00:48:03', '2018-10-23 00:48:03'),
	(171, '00001', '2018', 21, 0, '2018-10-23 00:48:03', '2018-10-23 00:48:03'),
	(172, '00001', '2018', 22, 0, '2018-10-23 00:48:03', '2018-10-23 00:48:03'),
	(173, '00001', '2018', 23, 0, '2018-10-23 00:48:03', '2018-10-23 00:48:03'),
	(174, '00001', '2018', 24, 0, '2018-10-23 00:48:03', '2018-10-23 00:48:03'),
	(175, '00001', '2018', 25, 0, '2018-10-23 00:48:03', '2018-10-23 00:48:03'),
	(176, '00001', '2018', 26, 0, '2018-10-23 00:48:03', '2018-10-23 00:48:03'),
	(177, '00001', '2018', 27, 0, '2018-10-23 00:48:03', '2018-10-23 00:48:03'),
	(178, '00001', '2018', 28, 0, '2018-10-23 00:48:03', '2018-10-23 00:48:03'),
	(179, '00001', '2018', 29, 0, '2018-10-23 00:48:03', '2018-10-23 00:48:03'),
	(180, '00001', '2018', 30, 0, '2018-10-23 00:48:03', '2018-10-23 00:48:03'),
	(181, '00001', '2018', 31, 0, '2018-10-23 00:48:03', '2018-10-23 00:48:03'),
	(182, '00001', '2018', 32, 0, '2018-10-23 00:48:03', '2018-10-23 00:48:03'),
	(183, '00001', '2018', 33, 0, '2018-10-23 00:48:03', '2018-10-23 00:48:03'),
	(184, '00001', '2018', 34, 0, '2018-10-23 00:48:03', '2018-10-23 00:48:03'),
	(185, '00001', '2018', 35, 0, '2018-10-23 00:48:03', '2018-10-23 00:48:03'),
	(186, '00001', '2018', 36, 0, '2018-10-23 00:48:03', '2018-10-23 00:48:03'),
	(187, '00001', '2018', 37, 0, '2018-10-23 00:48:03', '2018-10-23 00:48:03'),
	(188, '00001', '2018', 38, 0, '2018-10-23 00:48:03', '2018-10-23 00:48:03'),
	(189, '00001', '2018', 39, 0, '2018-10-23 00:48:03', '2018-10-23 00:48:03'),
	(190, '00001', '2018', 40, 0, '2018-10-23 00:48:03', '2018-10-23 00:48:03'),
	(191, '00001', '2018', 41, 0, '2018-10-23 00:48:03', '2018-10-23 00:48:03'),
	(192, '00001', '2018', 42, 0, '2018-10-23 00:48:03', '2018-10-23 00:48:03'),
	(193, '00001', '2018', 43, 0, '2018-10-23 00:48:03', '2018-10-23 00:48:03'),
	(194, '00001', '2018', 44, 0, '2018-10-23 00:48:03', '2018-10-23 00:48:03'),
	(195, '00001', '2018', 45, 0, '2018-10-23 00:48:03', '2018-10-23 00:48:03'),
	(196, '00001', '2018', 46, 0, '2018-10-23 00:48:03', '2018-10-23 00:48:03'),
	(197, '00001', '2018', 47, 0, '2018-10-23 00:48:03', '2018-10-23 00:48:03'),
	(198, '00001', '2018', 48, 0, '2018-10-23 00:48:03', '2018-10-23 00:48:03'),
	(199, '00001', '2018', 49, 0, '2018-10-23 00:48:03', '2018-10-23 00:48:03'),
	(200, '00001', '2018', 50, 0, '2018-10-23 00:48:03', '2018-10-23 00:48:03'),
	(201, '00001', '2018', 51, 0, '2018-10-23 00:48:03', '2018-10-23 00:48:03'),
	(202, '00001', '2018', 52, 0, '2018-10-23 00:48:03', '2018-10-23 00:48:03'),
	(203, '00001', '2018', 53, 0, '2018-10-23 00:48:03', '2018-10-23 00:48:03'),
	(204, '00001', '2018', 54, 0, '2018-10-23 00:48:03', '2018-10-23 00:48:03'),
	(205, '00001', '2018', 55, 0, '2018-10-23 00:48:03', '2018-10-23 00:48:03'),
	(206, '00001', '2018', 56, 0, '2018-10-23 00:48:03', '2018-10-23 00:48:03'),
	(207, '00001', '2018', 57, 0, '2018-10-23 00:48:03', '2018-10-23 00:48:03'),
	(208, '00001', '2018', 58, 0, '2018-10-23 00:48:03', '2018-10-23 00:48:03'),
	(209, '00001', '2018', 59, 0, '2018-10-23 00:48:03', '2018-10-23 00:48:03'),
	(210, '00001', '2018', 60, 0, '2018-10-23 00:48:03', '2018-10-23 00:48:03'),
	(211, '00001', '2018', 61, 0, '2018-10-23 00:48:03', '2018-10-23 00:48:03'),
	(212, '00001', '2018', 62, 0, '2018-10-23 00:48:03', '2018-10-23 00:48:03'),
	(213, '00001', '2018', 63, 0, '2018-10-23 00:48:03', '2018-10-23 00:48:03'),
	(214, '00001', '2018', 64, 0, '2018-10-23 00:48:03', '2018-10-23 00:48:03'),
	(215, '00001', '2018', 65, 0, '2018-10-23 00:48:03', '2018-10-23 00:48:03'),
	(216, '00001', '2018', 66, 0, '2018-10-23 00:48:03', '2018-10-23 00:48:03'),
	(217, '00001', '2018', 67, 0, '2018-10-23 00:48:03', '2018-10-23 00:48:03'),
	(218, '00001', '2018', 68, 0, '2018-10-23 00:48:03', '2018-10-23 00:48:03'),
	(219, '00001', '2018', 69, 0, '2018-10-23 00:48:03', '2018-10-23 00:48:03'),
	(220, '00001', '2018', 70, 0, '2018-10-23 00:48:03', '2018-10-23 00:48:03'),
	(221, '00001', '2018', 71, 0, '2018-10-23 00:48:03', '2018-10-23 00:48:03'),
	(222, '00001', '2018', 72, 0, '2018-10-23 00:48:03', '2018-10-23 00:48:03'),
	(223, '00001', '2018', 73, 0, '2018-10-23 00:48:03', '2018-10-23 00:48:03'),
	(224, '00001', '2018', 74, 0, '2018-10-23 00:48:03', '2018-10-23 00:48:03'),
	(225, '00001', '2018', 75, 0, '2018-10-23 00:48:03', '2018-10-23 00:48:03'),
	(226, '00001', '2018', 76, 0, '2018-10-23 00:48:03', '2018-10-23 00:48:03'),
	(227, '00001', '2018', 77, 0, '2018-10-23 00:48:03', '2018-10-23 00:48:03'),
	(228, '00001', '2018', 78, 0, '2018-10-23 00:48:03', '2018-10-23 00:48:03'),
	(229, '00001', '2018', 79, 0, '2018-10-23 00:48:03', '2018-10-23 00:48:03'),
	(230, '00001', '2018', 80, 0, '2018-10-23 00:48:03', '2018-10-23 00:48:03'),
	(231, '00001', '2018', 81, 0, '2018-10-23 00:48:03', '2018-10-23 00:48:03'),
	(232, '00001', '2018', 82, 0, '2018-10-23 00:48:03', '2018-10-23 00:48:03'),
	(233, '00001', '2018', 83, 0, '2018-10-23 00:48:03', '2018-10-23 00:48:03'),
	(234, '00001', '2018', 84, 0, '2018-10-23 00:48:03', '2018-10-23 00:48:03'),
	(235, '00001', '2018', 85, 0, '2018-10-23 00:48:03', '2018-10-23 00:48:03'),
	(236, '00001', '2018', 86, 0, '2018-10-23 00:48:03', '2018-10-23 00:48:03'),
	(237, '00001', '2018', 87, 0, '2018-10-23 00:48:03', '2018-10-23 00:48:03'),
	(238, '00001', '2018', 88, 0, '2018-10-23 00:48:03', '2018-10-23 00:48:03'),
	(239, '00001', '2018', 89, 0, '2018-10-23 00:48:03', '2018-10-23 00:48:03'),
	(240, '00001', '2018', 90, 0, '2018-10-23 00:48:03', '2018-10-23 00:48:03'),
	(241, '00001', '2018', 91, 0, '2018-10-23 00:48:03', '2018-10-23 00:48:03'),
	(242, '00001', '2018', 92, 0, '2018-10-23 00:48:03', '2018-10-23 00:48:03'),
	(243, '00001', '2018', 93, 0, '2018-10-23 00:48:03', '2018-10-23 00:48:03'),
	(244, '00001', '2018', 94, 0, '2018-10-23 00:48:03', '2018-10-23 00:48:03'),
	(245, '00001', '2018', 95, 0, '2018-10-23 00:48:03', '2018-10-23 00:48:03'),
	(246, '00001', '2018', 96, 0, '2018-10-23 00:48:03', '2018-10-23 00:48:03'),
	(247, '00001', '2018', 97, 0, '2018-10-23 00:48:03', '2018-10-23 00:48:03'),
	(248, '00001', '2018', 98, 0, '2018-10-23 00:48:03', '2018-10-23 00:48:03'),
	(249, '00001', '2018', 99, 0, '2018-10-23 00:48:03', '2018-10-23 00:48:03'),
	(251, '00010', '2018', 1, 1, '2018-10-23 10:48:05', '2018-10-23 11:22:46'),
	(252, '00010', '2018', 2, 0, '2018-10-23 10:48:05', '2018-10-23 10:48:05'),
	(253, '00010', '2018', 3, 0, '2018-10-23 10:48:05', '2018-10-23 10:48:05'),
	(254, '00010', '2018', 4, 0, '2018-10-23 10:48:05', '2018-10-23 10:48:05'),
	(255, '00010', '2018', 5, 0, '2018-10-23 10:48:05', '2018-10-23 10:48:05'),
	(256, '00010', '2018', 6, 0, '2018-10-23 10:48:05', '2018-10-23 10:48:05'),
	(257, '00010', '2018', 7, 0, '2018-10-23 10:48:05', '2018-10-23 10:48:05'),
	(258, '00010', '2018', 8, 0, '2018-10-23 10:48:05', '2018-10-23 10:48:05'),
	(259, '00010', '2018', 9, 0, '2018-10-23 10:48:05', '2018-10-23 10:48:05'),
	(260, '00010', '2018', 10, 0, '2018-10-23 10:48:05', '2018-10-23 10:48:05'),
	(261, '00101', '2018', 1, 1, '2018-10-23 11:06:26', '2018-10-23 11:48:10'),
	(262, '00101', '2018', 2, 0, '2018-10-23 11:06:26', '2018-10-23 11:06:26'),
	(263, '00101', '2018', 3, 0, '2018-10-23 11:06:26', '2018-10-23 11:06:26'),
	(264, '00101', '2018', 4, 0, '2018-10-23 11:06:26', '2018-10-23 11:06:26'),
	(265, '00101', '2018', 5, 0, '2018-10-23 11:06:26', '2018-10-23 11:06:26'),
	(266, '00101', '2018', 6, 0, '2018-10-23 11:06:26', '2018-10-23 11:06:26'),
	(267, '00101', '2018', 7, 0, '2018-10-23 11:06:26', '2018-10-23 11:06:26'),
	(268, '00101', '2018', 8, 0, '2018-10-23 11:06:26', '2018-10-23 11:06:26'),
	(269, '00101', '2018', 9, 0, '2018-10-23 11:06:26', '2018-10-23 11:06:26'),
	(270, '00101', '2018', 10, 0, '2018-10-23 11:06:26', '2018-10-23 11:06:26'),
	(272, '00006', '2018', 1, 0, '2018-10-23 11:11:55', '2018-10-23 11:11:55'),
	(273, '00006', '2018', 2, 0, '2018-10-23 11:11:55', '2018-10-23 11:11:55'),
	(274, '00006', '2018', 3, 0, '2018-10-23 11:11:55', '2018-10-23 11:11:55'),
	(275, '00006', '2018', 4, 0, '2018-10-23 11:11:55', '2018-10-23 11:11:55'),
	(276, '00006', '2018', 5, 0, '2018-10-23 11:11:55', '2018-10-23 11:11:55'),
	(277, '00006', '2018', 6, 0, '2018-10-23 11:11:55', '2018-10-23 11:11:55'),
	(278, '00006', '2018', 7, 0, '2018-10-23 11:11:55', '2018-10-23 11:11:55'),
	(279, '00006', '2018', 8, 0, '2018-10-23 11:11:55', '2018-10-23 11:11:55'),
	(280, '00006', '2018', 9, 0, '2018-10-23 11:11:55', '2018-10-23 11:11:55'),
	(281, '00006', '2018', 10, 0, '2018-10-23 11:11:55', '2018-10-23 11:11:55'),
	(282, '00202', '2018', 1, 1, '2018-10-23 11:13:31', '2018-10-23 11:50:45'),
	(283, '00202', '2018', 2, 1, '2018-10-23 11:13:31', '2018-10-23 11:46:46'),
	(284, '00202', '2018', 3, 0, '2018-10-23 11:13:31', '2018-10-23 11:13:31'),
	(285, '00202', '2018', 4, 0, '2018-10-23 11:13:31', '2018-10-23 11:13:31'),
	(286, '00202', '2018', 5, 0, '2018-10-23 11:13:31', '2018-10-23 11:13:31'),
	(287, '00202', '2018', 6, 0, '2018-10-23 11:13:31', '2018-10-23 11:13:31'),
	(288, '00202', '2018', 7, 0, '2018-10-23 11:13:31', '2018-10-23 11:13:31'),
	(289, '00202', '2018', 8, 0, '2018-10-23 11:13:31', '2018-10-23 11:13:31'),
	(290, '00202', '2018', 9, 0, '2018-10-23 11:13:31', '2018-10-23 11:13:31'),
	(291, '00202', '2018', 10, 0, '2018-10-23 11:13:31', '2018-10-23 11:13:31'),
	(292, '00301', '2018', 1, 1, '2018-10-23 11:19:49', '2018-10-23 11:39:51'),
	(293, '00301', '2018', 2, 0, '2018-10-23 11:19:49', '2018-10-23 11:19:49'),
	(294, '00301', '2018', 3, 0, '2018-10-23 11:19:49', '2018-10-23 11:19:49'),
	(295, '00301', '2018', 4, 0, '2018-10-23 11:19:49', '2018-10-23 11:19:49'),
	(296, '00301', '2018', 5, 0, '2018-10-23 11:19:49', '2018-10-23 11:19:49'),
	(297, '00301', '2018', 6, 0, '2018-10-23 11:19:49', '2018-10-23 11:19:49'),
	(298, '00301', '2018', 7, 0, '2018-10-23 11:19:49', '2018-10-23 11:19:49'),
	(299, '00301', '2018', 8, 0, '2018-10-23 11:19:49', '2018-10-23 11:19:49'),
	(300, '00301', '2018', 9, 0, '2018-10-23 11:19:49', '2018-10-23 11:19:49'),
	(301, '00301', '2018', 10, 0, '2018-10-23 11:19:49', '2018-10-23 11:19:49'),
	(302, '00301', '2018', 11, 0, '2018-10-23 11:19:49', '2018-10-23 11:19:49'),
	(303, '00301', '2018', 12, 0, '2018-10-23 11:19:49', '2018-10-23 11:19:49'),
	(304, '00301', '2018', 13, 0, '2018-10-23 11:19:49', '2018-10-23 11:19:49'),
	(305, '00301', '2018', 14, 0, '2018-10-23 11:19:49', '2018-10-23 11:19:49'),
	(306, '00301', '2018', 15, 0, '2018-10-23 11:19:49', '2018-10-23 11:19:49'),
	(307, '00401', '2018', 1, 1, '2018-10-23 11:20:51', '2018-10-23 12:42:04'),
	(308, '00401', '2018', 2, 1, '2018-10-23 11:20:51', '2018-10-23 12:47:13'),
	(322, '00202', '2018', 11, 0, '2018-10-23 11:46:05', '2018-10-23 11:46:05'),
	(323, '00202', '2018', 12, 0, '2018-10-23 11:46:05', '2018-10-23 11:46:05'),
	(324, '00202', '2018', 13, 0, '2018-10-23 11:46:05', '2018-10-23 11:46:05'),
	(325, '00202', '2018', 14, 0, '2018-10-23 11:46:05', '2018-10-23 11:46:05'),
	(326, '00202', '2018', 15, 0, '2018-10-23 11:46:05', '2018-10-23 11:46:05'),
	(327, '00202', '2018', 16, 0, '2018-10-23 11:46:05', '2018-10-23 11:46:05'),
	(328, '00202', '2018', 17, 0, '2018-10-23 11:46:05', '2018-10-23 11:46:05'),
	(329, '00202', '2018', 18, 0, '2018-10-23 11:46:05', '2018-10-23 11:46:05'),
	(330, '00202', '2018', 19, 0, '2018-10-23 11:46:05', '2018-10-23 11:46:05'),
	(331, '00202', '2018', 20, 0, '2018-10-23 11:46:05', '2018-10-23 11:46:05'),
	(332, '00502', '2018', 1, 1, '2018-10-23 12:57:11', '2018-10-23 13:07:52'),
	(333, '00103', '2018', 1, 1, '2018-10-23 13:03:22', '2018-10-23 13:19:40'),
	(334, '00302', '2018', 1, 0, '2018-10-23 13:09:45', '2018-10-23 13:09:45'),
	(335, '00302', '2018', 2, 0, '2018-10-23 13:09:45', '2018-10-23 13:09:45'),
	(336, '00302', '2018', 3, 0, '2018-10-23 13:09:45', '2018-10-23 13:09:45'),
	(337, '00302', '2018', 4, 0, '2018-10-23 13:09:45', '2018-10-23 13:09:45'),
	(338, '00302', '2018', 5, 0, '2018-10-23 13:09:45', '2018-10-23 13:09:45'),
	(339, '00302', '2018', 6, 0, '2018-10-23 13:09:45', '2018-10-23 13:09:45'),
	(340, '00302', '2018', 7, 0, '2018-10-23 13:09:45', '2018-10-23 13:09:45'),
	(341, '00302', '2018', 8, 0, '2018-10-23 13:09:45', '2018-10-23 13:09:45'),
	(342, '00302', '2018', 9, 0, '2018-10-23 13:09:45', '2018-10-23 13:09:45'),
	(343, '00302', '2018', 10, 0, '2018-10-23 13:09:45', '2018-10-23 13:09:45'),
	(344, '00302', '2018', 11, 0, '2018-10-23 13:09:45', '2018-10-23 13:09:45'),
	(345, '00302', '2018', 12, 0, '2018-10-23 13:09:45', '2018-10-23 13:09:45'),
	(346, '00302', '2018', 13, 0, '2018-10-23 13:09:45', '2018-10-23 13:09:45'),
	(347, '00302', '2018', 14, 0, '2018-10-23 13:09:45', '2018-10-23 13:09:45'),
	(348, '00302', '2018', 15, 0, '2018-10-23 13:09:45', '2018-10-23 13:09:45'),
	(349, '00222', '2018', 1, 1, '2018-10-23 22:50:15', '2018-10-23 22:58:56'),
	(350, '00222', '2018', 2, 1, '2018-10-23 22:50:15', '2018-10-24 00:10:46'),
	(351, '00222', '2018', 3, 0, '2018-10-23 22:50:15', '2018-10-23 22:50:15'),
	(352, '00222', '2018', 4, 0, '2018-10-23 22:50:15', '2018-10-23 22:50:15'),
	(353, '00222', '2018', 5, 0, '2018-10-23 22:50:15', '2018-10-23 22:50:15'),
	(354, '00222', '2018', 6, 0, '2018-10-23 22:50:15', '2018-10-23 22:50:15'),
	(355, '00222', '2018', 7, 0, '2018-10-23 22:50:15', '2018-10-23 22:50:15'),
	(356, '00222', '2018', 8, 0, '2018-10-23 22:50:15', '2018-10-23 22:50:15'),
	(357, '00222', '2018', 9, 0, '2018-10-23 22:50:15', '2018-10-23 22:50:15'),
	(358, '00222', '2018', 10, 0, '2018-10-23 22:50:15', '2018-10-23 22:50:15'),
	(359, '00222', '2018', 11, 0, '2018-10-23 22:50:15', '2018-10-23 22:50:15'),
	(360, '00222', '2018', 12, 0, '2018-10-23 22:50:15', '2018-10-23 22:50:15'),
	(361, '00222', '2018', 13, 0, '2018-10-23 22:50:15', '2018-10-23 22:50:15'),
	(362, '00222', '2018', 14, 0, '2018-10-23 22:50:15', '2018-10-23 22:50:15'),
	(363, '00222', '2018', 15, 0, '2018-10-23 22:50:15', '2018-10-23 22:50:15'),
	(364, '00222', '2018', 16, 0, '2018-10-23 22:50:15', '2018-10-23 22:50:15'),
	(365, '00222', '2018', 17, 0, '2018-10-23 22:50:15', '2018-10-23 22:50:15'),
	(366, '00222', '2018', 18, 0, '2018-10-23 22:50:15', '2018-10-23 22:50:15'),
	(367, '00222', '2018', 19, 0, '2018-10-23 22:50:15', '2018-10-23 22:50:15'),
	(368, '00222', '2018', 20, 0, '2018-10-23 22:50:15', '2018-10-23 22:50:15'),
	(369, '00222', '2018', 21, 0, '2018-10-23 22:50:15', '2018-10-23 22:50:15'),
	(370, '00222', '2018', 22, 0, '2018-10-23 22:50:15', '2018-10-23 22:50:15'),
	(371, '00222', '2018', 23, 0, '2018-10-23 22:50:15', '2018-10-23 22:50:15'),
	(372, '00222', '2018', 24, 0, '2018-10-23 22:50:15', '2018-10-23 22:50:15'),
	(373, '00222', '2018', 25, 0, '2018-10-23 22:50:15', '2018-10-23 22:50:15'),
	(374, '00222', '2018', 26, 0, '2018-10-23 22:50:15', '2018-10-23 22:50:15'),
	(375, '00222', '2018', 27, 0, '2018-10-23 22:50:15', '2018-10-23 22:50:15'),
	(376, '00222', '2018', 28, 0, '2018-10-23 22:50:15', '2018-10-23 22:50:15'),
	(377, '00222', '2018', 29, 0, '2018-10-23 22:50:15', '2018-10-23 22:50:15'),
	(378, '00222', '2018', 30, 0, '2018-10-23 22:50:15', '2018-10-23 22:50:15'),
	(379, '00222', '2018', 31, 0, '2018-10-23 22:50:15', '2018-10-23 22:50:15'),
	(380, '00222', '2018', 32, 0, '2018-10-23 22:50:15', '2018-10-23 22:50:15'),
	(381, '00222', '2018', 33, 0, '2018-10-23 22:50:15', '2018-10-23 22:50:15'),
	(382, '00222', '2018', 34, 0, '2018-10-23 22:50:15', '2018-10-23 22:50:15'),
	(383, '00222', '2018', 35, 0, '2018-10-23 22:50:15', '2018-10-23 22:50:15'),
	(384, '00222', '2018', 36, 0, '2018-10-23 22:50:15', '2018-10-23 22:50:15'),
	(385, '00222', '2018', 37, 0, '2018-10-23 22:50:15', '2018-10-23 22:50:15'),
	(386, '00222', '2018', 38, 0, '2018-10-23 22:50:15', '2018-10-23 22:50:15'),
	(387, '00222', '2018', 39, 0, '2018-10-23 22:50:15', '2018-10-23 22:50:15'),
	(388, '00222', '2018', 40, 0, '2018-10-23 22:50:15', '2018-10-23 22:50:15'),
	(389, '00222', '2018', 41, 0, '2018-10-23 22:50:15', '2018-10-23 22:50:15'),
	(390, '00222', '2018', 42, 0, '2018-10-23 22:50:15', '2018-10-23 22:50:15'),
	(391, '00222', '2018', 43, 0, '2018-10-23 22:50:15', '2018-10-23 22:50:15'),
	(392, '00222', '2018', 44, 0, '2018-10-23 22:50:15', '2018-10-23 22:50:15'),
	(393, '00222', '2018', 45, 0, '2018-10-23 22:50:15', '2018-10-23 22:50:15'),
	(394, '00222', '2018', 46, 0, '2018-10-23 22:50:15', '2018-10-23 22:50:15'),
	(395, '00222', '2018', 47, 0, '2018-10-23 22:50:15', '2018-10-23 22:50:15'),
	(396, '00222', '2018', 48, 0, '2018-10-23 22:50:15', '2018-10-23 22:50:15'),
	(397, '00222', '2018', 49, 0, '2018-10-23 22:50:15', '2018-10-23 22:50:15'),
	(398, '00222', '2018', 50, 0, '2018-10-23 22:50:15', '2018-10-23 22:50:15'),
	(399, '00222', '2018', 51, 0, '2018-10-23 22:50:15', '2018-10-23 22:50:15'),
	(400, '00222', '2018', 52, 0, '2018-10-23 22:50:15', '2018-10-23 22:50:15'),
	(401, '00222', '2018', 53, 0, '2018-10-23 22:50:15', '2018-10-23 22:50:15'),
	(402, '00222', '2018', 54, 0, '2018-10-23 22:50:15', '2018-10-23 22:50:15'),
	(403, '00222', '2018', 55, 0, '2018-10-23 22:50:15', '2018-10-23 22:50:15'),
	(404, '00222', '2018', 56, 0, '2018-10-23 22:50:15', '2018-10-23 22:50:15'),
	(405, '00222', '2018', 57, 0, '2018-10-23 22:50:15', '2018-10-23 22:50:15'),
	(406, '00222', '2018', 58, 0, '2018-10-23 22:50:15', '2018-10-23 22:50:15'),
	(407, '00222', '2018', 59, 0, '2018-10-23 22:50:15', '2018-10-23 22:50:15'),
	(408, '00222', '2018', 60, 0, '2018-10-23 22:50:15', '2018-10-23 22:50:15'),
	(409, '00222', '2018', 61, 0, '2018-10-23 22:50:15', '2018-10-23 22:50:15'),
	(410, '00222', '2018', 62, 0, '2018-10-23 22:50:15', '2018-10-23 22:50:15'),
	(411, '00222', '2018', 63, 0, '2018-10-23 22:50:15', '2018-10-23 22:50:15'),
	(412, '00222', '2018', 64, 0, '2018-10-23 22:50:15', '2018-10-23 22:50:15'),
	(413, '00222', '2018', 65, 0, '2018-10-23 22:50:15', '2018-10-23 22:50:15'),
	(414, '00222', '2018', 66, 0, '2018-10-23 22:50:15', '2018-10-23 22:50:15'),
	(415, '00222', '2018', 67, 0, '2018-10-23 22:50:15', '2018-10-23 22:50:15'),
	(416, '00222', '2018', 68, 0, '2018-10-23 22:50:15', '2018-10-23 22:50:15'),
	(417, '00222', '2018', 69, 0, '2018-10-23 22:50:15', '2018-10-23 22:50:15'),
	(418, '00222', '2018', 70, 0, '2018-10-23 22:50:15', '2018-10-23 22:50:15'),
	(419, '00222', '2018', 71, 0, '2018-10-23 22:50:15', '2018-10-23 22:50:15'),
	(420, '00222', '2018', 72, 0, '2018-10-23 22:50:15', '2018-10-23 22:50:15'),
	(421, '00222', '2018', 73, 0, '2018-10-23 22:50:15', '2018-10-23 22:50:15'),
	(422, '00222', '2018', 74, 0, '2018-10-23 22:50:15', '2018-10-23 22:50:15'),
	(423, '00222', '2018', 75, 0, '2018-10-23 22:50:15', '2018-10-23 22:50:15'),
	(424, '00222', '2018', 76, 0, '2018-10-23 22:50:15', '2018-10-23 22:50:15'),
	(425, '00222', '2018', 77, 0, '2018-10-23 22:50:15', '2018-10-23 22:50:15'),
	(426, '00222', '2018', 78, 0, '2018-10-23 22:50:15', '2018-10-23 22:50:15'),
	(427, '00222', '2018', 79, 0, '2018-10-23 22:50:15', '2018-10-23 22:50:15'),
	(428, '00222', '2018', 80, 0, '2018-10-23 22:50:15', '2018-10-23 22:50:15'),
	(429, '00222', '2018', 81, 0, '2018-10-23 22:50:15', '2018-10-23 22:50:15'),
	(430, '00222', '2018', 82, 0, '2018-10-23 22:50:15', '2018-10-23 22:50:15'),
	(431, '00222', '2018', 83, 0, '2018-10-23 22:50:15', '2018-10-23 22:50:15'),
	(432, '00222', '2018', 84, 0, '2018-10-23 22:50:15', '2018-10-23 22:50:15'),
	(433, '00222', '2018', 85, 0, '2018-10-23 22:50:15', '2018-10-23 22:50:15'),
	(434, '00222', '2018', 86, 0, '2018-10-23 22:50:15', '2018-10-23 22:50:15'),
	(435, '00222', '2018', 87, 0, '2018-10-23 22:50:15', '2018-10-23 22:50:15'),
	(436, '00222', '2018', 88, 0, '2018-10-23 22:50:15', '2018-10-23 22:50:15'),
	(437, '00222', '2018', 89, 0, '2018-10-23 22:50:15', '2018-10-23 22:50:15'),
	(438, '00222', '2018', 90, 0, '2018-10-23 22:50:15', '2018-10-23 22:50:15'),
	(439, '00222', '2018', 91, 0, '2018-10-23 22:50:15', '2018-10-23 22:50:15'),
	(440, '00222', '2018', 92, 0, '2018-10-23 22:50:15', '2018-10-23 22:50:15'),
	(441, '00222', '2018', 93, 0, '2018-10-23 22:50:15', '2018-10-23 22:50:15'),
	(442, '00222', '2018', 94, 0, '2018-10-23 22:50:15', '2018-10-23 22:50:15'),
	(443, '00222', '2018', 95, 0, '2018-10-23 22:50:15', '2018-10-23 22:50:15'),
	(444, '00222', '2018', 96, 0, '2018-10-23 22:50:15', '2018-10-23 22:50:15'),
	(445, '00222', '2018', 97, 0, '2018-10-23 22:50:15', '2018-10-23 22:50:15'),
	(446, '00222', '2018', 98, 0, '2018-10-23 22:50:15', '2018-10-23 22:50:15'),
	(447, '00222', '2018', 99, 0, '2018-10-23 22:50:15', '2018-10-23 22:50:15'),
	(448, '00222', '2018', 100, 0, '2018-10-23 22:50:15', '2018-10-23 22:50:15'),
	(449, '00222', '2018', 101, 0, '2018-10-23 23:53:54', '2018-10-23 23:53:54'),
	(450, '00222', '2018', 102, 0, '2018-10-23 23:53:54', '2018-10-23 23:53:54'),
	(451, '00222', '2018', 103, 0, '2018-10-23 23:53:54', '2018-10-23 23:53:54'),
	(452, '00222', '2018', 104, 0, '2018-10-23 23:53:54', '2018-10-23 23:53:54'),
	(453, '00222', '2018', 105, 0, '2018-10-23 23:53:54', '2018-10-23 23:53:54'),
	(454, '00222', '2018', 106, 0, '2018-10-23 23:53:54', '2018-10-23 23:53:54'),
	(455, '00222', '2018', 107, 0, '2018-10-23 23:53:54', '2018-10-23 23:53:54'),
	(456, '00222', '2018', 108, 0, '2018-10-23 23:53:54', '2018-10-23 23:53:54'),
	(457, '00222', '2018', 109, 0, '2018-10-23 23:53:54', '2018-10-23 23:53:54'),
	(458, '00222', '2018', 110, 0, '2018-10-23 23:53:54', '2018-10-23 23:53:54'),
	(459, '00222', '2018', 111, 0, '2018-10-23 23:53:54', '2018-10-23 23:53:54'),
	(460, '00222', '2018', 112, 0, '2018-10-23 23:53:54', '2018-10-23 23:53:54'),
	(461, '00222', '2018', 113, 0, '2018-10-23 23:53:54', '2018-10-23 23:53:54'),
	(462, '00222', '2018', 114, 0, '2018-10-23 23:53:54', '2018-10-23 23:53:54'),
	(463, '00222', '2018', 115, 0, '2018-10-23 23:53:54', '2018-10-23 23:53:54'),
	(464, '00222', '2018', 116, 0, '2018-10-23 23:53:54', '2018-10-23 23:53:54'),
	(465, '00222', '2018', 117, 0, '2018-10-23 23:53:54', '2018-10-23 23:53:54'),
	(466, '00222', '2018', 118, 0, '2018-10-23 23:53:54', '2018-10-23 23:53:54'),
	(467, '00222', '2018', 119, 0, '2018-10-23 23:53:54', '2018-10-23 23:53:54'),
	(468, '00222', '2018', 120, 0, '2018-10-23 23:53:54', '2018-10-23 23:53:54'),
	(469, '00222', '2018', 121, 0, '2018-10-23 23:53:54', '2018-10-23 23:53:54'),
	(470, '00222', '2018', 122, 0, '2018-10-23 23:53:54', '2018-10-23 23:53:54'),
	(471, '00222', '2018', 123, 0, '2018-10-23 23:53:54', '2018-10-23 23:53:54'),
	(472, '00222', '2018', 124, 0, '2018-10-23 23:53:54', '2018-10-23 23:53:54'),
	(473, '00222', '2018', 125, 0, '2018-10-23 23:53:54', '2018-10-23 23:53:54'),
	(474, '00222', '2018', 126, 0, '2018-10-23 23:53:54', '2018-10-23 23:53:54'),
	(475, '00222', '2018', 127, 0, '2018-10-23 23:53:54', '2018-10-23 23:53:54'),
	(476, '00222', '2018', 128, 0, '2018-10-23 23:53:54', '2018-10-23 23:53:54'),
	(477, '00222', '2018', 129, 0, '2018-10-23 23:53:54', '2018-10-23 23:53:54'),
	(478, '00222', '2018', 130, 0, '2018-10-23 23:53:54', '2018-10-23 23:53:54'),
	(479, '00222', '2018', 131, 0, '2018-10-23 23:53:54', '2018-10-23 23:53:54'),
	(480, '00222', '2018', 132, 0, '2018-10-23 23:53:54', '2018-10-23 23:53:54'),
	(481, '00222', '2018', 133, 0, '2018-10-23 23:53:54', '2018-10-23 23:53:54'),
	(482, '00222', '2018', 134, 0, '2018-10-23 23:53:54', '2018-10-23 23:53:54'),
	(483, '00222', '2018', 135, 0, '2018-10-23 23:53:54', '2018-10-23 23:53:54'),
	(484, '00222', '2018', 136, 0, '2018-10-23 23:53:54', '2018-10-23 23:53:54'),
	(485, '00222', '2018', 137, 0, '2018-10-23 23:53:54', '2018-10-23 23:53:54'),
	(486, '00222', '2018', 138, 0, '2018-10-23 23:53:54', '2018-10-23 23:53:54'),
	(487, '00222', '2018', 139, 0, '2018-10-23 23:53:54', '2018-10-23 23:53:54'),
	(488, '00222', '2018', 140, 0, '2018-10-23 23:53:54', '2018-10-23 23:53:54'),
	(489, '00222', '2018', 141, 0, '2018-10-23 23:53:54', '2018-10-23 23:53:54'),
	(490, '00222', '2018', 142, 0, '2018-10-23 23:53:54', '2018-10-23 23:53:54'),
	(491, '00222', '2018', 143, 0, '2018-10-23 23:53:54', '2018-10-23 23:53:54'),
	(492, '00222', '2018', 144, 0, '2018-10-23 23:53:54', '2018-10-23 23:53:54'),
	(493, '00222', '2018', 145, 0, '2018-10-23 23:53:54', '2018-10-23 23:53:54'),
	(494, '00222', '2018', 146, 0, '2018-10-23 23:53:54', '2018-10-23 23:53:54'),
	(495, '00222', '2018', 147, 0, '2018-10-23 23:53:54', '2018-10-23 23:53:54'),
	(496, '00222', '2018', 148, 0, '2018-10-23 23:53:54', '2018-10-23 23:53:54'),
	(497, '00222', '2018', 149, 0, '2018-10-23 23:53:54', '2018-10-23 23:53:54'),
	(498, '00222', '2018', 150, 0, '2018-10-23 23:53:54', '2018-10-23 23:53:54'),
	(499, '00222', '2018', 151, 0, '2018-10-23 23:53:54', '2018-10-23 23:53:54'),
	(500, '00222', '2018', 152, 0, '2018-10-23 23:53:54', '2018-10-23 23:53:54'),
	(501, '00222', '2018', 153, 0, '2018-10-23 23:53:54', '2018-10-23 23:53:54'),
	(502, '00222', '2018', 154, 0, '2018-10-23 23:53:54', '2018-10-23 23:53:54'),
	(503, '00222', '2018', 155, 0, '2018-10-23 23:53:54', '2018-10-23 23:53:54'),
	(504, '00222', '2018', 156, 0, '2018-10-23 23:53:54', '2018-10-23 23:53:54'),
	(505, '00222', '2018', 157, 0, '2018-10-23 23:53:54', '2018-10-23 23:53:54'),
	(506, '00222', '2018', 158, 0, '2018-10-23 23:53:54', '2018-10-23 23:53:54'),
	(507, '00222', '2018', 159, 0, '2018-10-23 23:53:54', '2018-10-23 23:53:54'),
	(508, '00222', '2018', 160, 0, '2018-10-23 23:53:54', '2018-10-23 23:53:54'),
	(509, '00222', '2018', 161, 0, '2018-10-23 23:53:54', '2018-10-23 23:53:54'),
	(510, '00222', '2018', 162, 0, '2018-10-23 23:53:54', '2018-10-23 23:53:54'),
	(511, '00222', '2018', 163, 0, '2018-10-23 23:53:54', '2018-10-23 23:53:54'),
	(512, '00222', '2018', 164, 0, '2018-10-23 23:53:54', '2018-10-23 23:53:54'),
	(513, '00222', '2018', 165, 0, '2018-10-23 23:53:54', '2018-10-23 23:53:54'),
	(514, '00222', '2018', 166, 0, '2018-10-23 23:53:54', '2018-10-23 23:53:54'),
	(515, '00222', '2018', 167, 0, '2018-10-23 23:53:54', '2018-10-23 23:53:54'),
	(516, '00222', '2018', 168, 0, '2018-10-23 23:53:54', '2018-10-23 23:53:54'),
	(517, '00222', '2018', 169, 0, '2018-10-23 23:53:54', '2018-10-23 23:53:54'),
	(518, '00222', '2018', 170, 0, '2018-10-23 23:53:54', '2018-10-23 23:53:54'),
	(519, '00222', '2018', 171, 0, '2018-10-23 23:53:54', '2018-10-23 23:53:54'),
	(520, '00222', '2018', 172, 0, '2018-10-23 23:53:54', '2018-10-23 23:53:54'),
	(521, '00222', '2018', 173, 0, '2018-10-23 23:53:54', '2018-10-23 23:53:54'),
	(522, '00222', '2018', 174, 0, '2018-10-23 23:53:54', '2018-10-23 23:53:54'),
	(523, '00222', '2018', 175, 0, '2018-10-23 23:53:54', '2018-10-23 23:53:54'),
	(524, '00222', '2018', 176, 0, '2018-10-23 23:53:54', '2018-10-23 23:53:54'),
	(525, '00222', '2018', 177, 0, '2018-10-23 23:53:54', '2018-10-23 23:53:54'),
	(526, '00222', '2018', 178, 0, '2018-10-23 23:53:54', '2018-10-23 23:53:54'),
	(527, '00222', '2018', 179, 0, '2018-10-23 23:53:54', '2018-10-23 23:53:54'),
	(528, '00222', '2018', 180, 0, '2018-10-23 23:53:54', '2018-10-23 23:53:54'),
	(529, '00222', '2018', 181, 0, '2018-10-23 23:53:54', '2018-10-23 23:53:54'),
	(530, '00222', '2018', 182, 0, '2018-10-23 23:53:54', '2018-10-23 23:53:54'),
	(531, '00222', '2018', 183, 0, '2018-10-23 23:53:54', '2018-10-23 23:53:54'),
	(532, '00222', '2018', 184, 0, '2018-10-23 23:53:54', '2018-10-23 23:53:54'),
	(533, '00222', '2018', 185, 0, '2018-10-23 23:53:54', '2018-10-23 23:53:54'),
	(534, '00222', '2018', 186, 0, '2018-10-23 23:53:54', '2018-10-23 23:53:54'),
	(535, '00222', '2018', 187, 0, '2018-10-23 23:53:54', '2018-10-23 23:53:54'),
	(536, '00222', '2018', 188, 0, '2018-10-23 23:53:54', '2018-10-23 23:53:54'),
	(537, '00222', '2018', 189, 0, '2018-10-23 23:53:54', '2018-10-23 23:53:54'),
	(538, '00222', '2018', 190, 0, '2018-10-23 23:53:54', '2018-10-23 23:53:54'),
	(539, '00222', '2018', 191, 0, '2018-10-23 23:53:54', '2018-10-23 23:53:54'),
	(540, '00222', '2018', 192, 0, '2018-10-23 23:53:54', '2018-10-23 23:53:54'),
	(541, '00222', '2018', 193, 0, '2018-10-23 23:53:54', '2018-10-23 23:53:54'),
	(542, '00222', '2018', 194, 0, '2018-10-23 23:53:54', '2018-10-23 23:53:54'),
	(543, '00222', '2018', 195, 0, '2018-10-23 23:53:54', '2018-10-23 23:53:54'),
	(544, '00222', '2018', 196, 0, '2018-10-23 23:53:54', '2018-10-23 23:53:54'),
	(545, '00222', '2018', 197, 0, '2018-10-23 23:53:54', '2018-10-23 23:53:54'),
	(546, '00222', '2018', 198, 0, '2018-10-23 23:53:54', '2018-10-23 23:53:54'),
	(547, '00222', '2018', 199, 0, '2018-10-23 23:53:54', '2018-10-23 23:53:54'),
	(548, '00222', '2018', 200, 0, '2018-10-23 23:53:54', '2018-10-23 23:53:54'),
	(549, '00401', '2018', 3, 1, '2018-10-24 08:43:44', '2018-10-24 08:55:25'),
	(550, '00401', '2018', 4, 0, '2018-10-24 08:43:44', '2018-10-24 08:43:44'),
	(551, '00401', '2018', 5, 0, '2018-10-24 08:43:44', '2018-10-24 08:43:44'),
	(552, '00401', '2018', 6, 0, '2018-10-24 08:43:44', '2018-10-24 08:43:44'),
	(553, '00401', '2018', 7, 0, '2018-10-24 08:43:44', '2018-10-24 08:43:44'),
	(554, '00401', '2018', 8, 0, '2018-10-24 08:43:44', '2018-10-24 08:43:44'),
	(555, '00401', '2018', 9, 0, '2018-10-24 08:43:44', '2018-10-24 08:43:44'),
	(556, '00401', '2018', 10, 0, '2018-10-24 08:43:44', '2018-10-24 08:43:44'),
	(557, '00401', '2018', 11, 0, '2018-10-24 08:43:44', '2018-10-24 08:43:44'),
	(558, '00401', '2018', 12, 0, '2018-10-24 08:43:44', '2018-10-24 08:43:44'),
	(559, '00302', '2018', 16, 0, '2018-10-25 09:34:00', '2018-10-25 09:34:00'),
	(560, '00302', '2018', 17, 0, '2018-10-25 09:34:00', '2018-10-25 09:34:00'),
	(561, '00302', '2018', 18, 0, '2018-10-25 09:34:00', '2018-10-25 09:34:00'),
	(562, '00302', '2018', 19, 0, '2018-10-25 09:34:00', '2018-10-25 09:34:00'),
	(563, '00302', '2018', 20, 0, '2018-10-25 09:34:00', '2018-10-25 09:34:00'),
	(564, '00302', '2018', 21, 0, '2018-10-25 09:34:00', '2018-10-25 09:34:00'),
	(565, '00302', '2018', 22, 0, '2018-10-25 09:34:00', '2018-10-25 09:34:00'),
	(566, '00302', '2018', 23, 0, '2018-10-25 09:34:00', '2018-10-25 09:34:00'),
	(567, '00302', '2018', 24, 0, '2018-10-25 09:34:00', '2018-10-25 09:34:00'),
	(568, '00302', '2018', 25, 0, '2018-10-25 09:34:00', '2018-10-25 09:34:00'),
	(569, '00102', '2018', 1, 0, '2018-10-29 09:07:55', '2018-10-29 09:07:55'),
	(570, '00102', '2018', 2, 0, '2018-10-29 09:07:55', '2018-10-29 09:07:55'),
	(571, '00102', '2018', 3, 0, '2018-10-29 09:07:55', '2018-10-29 09:07:55'),
	(572, '00102', '2018', 4, 0, '2018-10-29 09:07:55', '2018-10-29 09:07:55'),
	(573, '00102', '2018', 5, 0, '2018-10-29 09:07:55', '2018-10-29 09:07:55'),
	(574, '00102', '2018', 6, 0, '2018-10-29 09:07:55', '2018-10-29 09:07:55'),
	(575, '00102', '2018', 7, 0, '2018-10-29 09:07:55', '2018-10-29 09:07:55'),
	(576, '00102', '2018', 8, 0, '2018-10-29 09:07:55', '2018-10-29 09:07:55'),
	(577, '00102', '2018', 9, 0, '2018-10-29 09:07:55', '2018-10-29 09:07:55'),
	(578, '00102', '2018', 10, 0, '2018-10-29 09:07:55', '2018-10-29 09:07:55'),
	(579, '00102', '2018', 11, 0, '2018-10-29 09:08:20', '2018-10-29 09:08:20');
/*!40000 ALTER TABLE `d_form` ENABLE KEYS */;

-- Dumping structure for table dp0.d_form_debitur
DROP TABLE IF EXISTS `d_form_debitur`;
CREATE TABLE IF NOT EXISTS `d_form_debitur` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `id_form` int(10) NOT NULL,
  `nik` varchar(16) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `Index 2` (`id_form`),
  UNIQUE KEY `Index 4` (`nik`),
  CONSTRAINT `FK_d_form_debitur_d_debitur` FOREIGN KEY (`nik`) REFERENCES `d_debitur` (`nik`),
  CONSTRAINT `FK_d_form_debitur_d_form` FOREIGN KEY (`id_form`) REFERENCES `d_form` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=latin1;

-- Dumping data for table dp0.d_form_debitur: ~14 rows (approximately)
DELETE FROM `d_form_debitur`;
/*!40000 ALTER TABLE `d_form_debitur` DISABLE KEYS */;
INSERT INTO `d_form_debitur` (`id`, `id_form`, `nik`, `created_at`, `updated_at`) VALUES
	(1, 151, '3332323232323232', '2018-10-23 01:14:38', '2018-10-23 01:14:38'),
	(2, 152, '0123129432194314', '2018-10-23 01:17:12', '2018-10-23 01:17:12'),
	(3, 251, '3171214098700009', '2018-10-23 11:22:46', '2018-10-23 11:22:46'),
	(4, 292, '3174062304920004', '2018-10-23 11:39:50', '2018-10-23 11:39:50'),
	(5, 283, '3674044210970006', '2018-10-23 11:46:46', '2018-10-23 11:46:46'),
	(6, 261, '1242134325345643', '2018-10-23 11:48:10', '2018-10-23 11:48:10'),
	(11, 282, '3751000000000000', '2018-10-23 11:50:45', '2018-10-23 11:50:45'),
	(12, 307, '5325226363747474', '2018-10-23 12:42:04', '2018-10-23 12:42:04'),
	(13, 308, '2233555667909877', '2018-10-23 12:47:13', '2018-10-23 12:47:13'),
	(18, 332, '3124124314233542', '2018-10-23 13:07:52', '2018-10-23 13:07:52'),
	(19, 333, '3276012212760006', '2018-10-23 13:19:40', '2018-10-23 13:19:40'),
	(20, 349, '1234567489789456', '2018-10-23 22:58:56', '2018-10-23 22:58:56'),
	(21, 350, '3421354125125412', '2018-10-24 00:10:46', '2018-10-24 00:10:46'),
	(22, 549, '7127394858669559', '2018-10-24 08:55:25', '2018-10-24 08:55:25');
/*!40000 ALTER TABLE `d_form_debitur` ENABLE KEYS */;

-- Dumping structure for table dp0.d_hunian
DROP TABLE IF EXISTS `d_hunian`;
CREATE TABLE IF NOT EXISTS `d_hunian` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `id_developer` int(10) NOT NULL,
  `kdprop` varchar(2) NOT NULL,
  `kdkabkota` varchar(2) NOT NULL,
  `kdkec` varchar(2) NOT NULL,
  `kdkel` varchar(4) NOT NULL,
  `kodepos` varchar(5) NOT NULL,
  `alamat` varchar(255) NOT NULL,
  `nmhunian` varchar(255) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_d_hunian_t_kelurahan` (`kdprop`,`kdkabkota`,`kdkec`,`kdkel`),
  KEY `FK_d_hunian_t_developer` (`id_developer`),
  CONSTRAINT `FK_d_hunian_t_developer` FOREIGN KEY (`id_developer`) REFERENCES `t_developer` (`id`),
  CONSTRAINT `FK_d_hunian_t_kelurahan` FOREIGN KEY (`kdprop`, `kdkabkota`, `kdkec`, `kdkel`) REFERENCES `t_kelurahan` (`kdprop`, `kdkabkota`, `kdkec`, `kdkel`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;

-- Dumping data for table dp0.d_hunian: ~1 rows (approximately)
DELETE FROM `d_hunian`;
/*!40000 ALTER TABLE `d_hunian` DISABLE KEYS */;
INSERT INTO `d_hunian` (`id`, `id_developer`, `kdprop`, `kdkabkota`, `kdkec`, `kdkel`, `kodepos`, `alamat`, `nmhunian`, `created_at`, `updated_at`) VALUES
	(5, 1, '31', '71', '01', '1001', '12345', 'Jl. Pinang Raya No. 3-10, Jakarta Timur', 'Kelapa village', '2018-08-02 20:36:55', '2018-08-02 20:36:56');
/*!40000 ALTER TABLE `d_hunian` ENABLE KEYS */;

-- Dumping structure for table dp0.d_hunian_dtl
DROP TABLE IF EXISTS `d_hunian_dtl`;
CREATE TABLE IF NOT EXISTS `d_hunian_dtl` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_hunian` int(11) NOT NULL,
  `kdkategori` varchar(3) DEFAULT NULL,
  `kdtipe` varchar(3) DEFAULT NULL,
  `nilrmh` decimal(10,0) DEFAULT NULL,
  `nilppn` decimal(10,0) DEFAULT NULL,
  `niljual` decimal(10,0) DEFAULT NULL,
  `jmlunit` decimal(10,0) DEFAULT NULL,
  `id_user` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id_hunian` (`id_hunian`),
  KEY `kdkategori` (`kdkategori`),
  KEY `kdtipe` (`kdtipe`),
  CONSTRAINT `d_hunian_dtl_ibfk_1` FOREIGN KEY (`id_hunian`) REFERENCES `d_hunian` (`id`),
  CONSTRAINT `d_hunian_dtl_ibfk_2` FOREIGN KEY (`kdkategori`) REFERENCES `t_kategori` (`kdkategori`),
  CONSTRAINT `d_hunian_dtl_ibfk_3` FOREIGN KEY (`kdtipe`) REFERENCES `t_tipe` (`kdtipe`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;

-- Dumping data for table dp0.d_hunian_dtl: ~6 rows (approximately)
DELETE FROM `d_hunian_dtl`;
/*!40000 ALTER TABLE `d_hunian_dtl` DISABLE KEYS */;
INSERT INTO `d_hunian_dtl` (`id`, `id_hunian`, `kdkategori`, `kdtipe`, `nilrmh`, `nilppn`, `niljual`, `jmlunit`, `id_user`, `created_at`, `updated_at`) VALUES
	(1, 5, '21', 'ST', 184800000, 0, 184800000, 160, 1, '2018-10-10 22:59:24', '2018-10-10 22:59:24'),
	(2, 5, '21', 'STC', 195800000, 0, 195800000, 80, 1, '2018-10-10 22:59:24', '2018-10-10 22:59:24'),
	(3, 5, '21', '1BA', 210760000, 0, 210760000, 160, 1, '2018-10-10 22:59:24', '2018-10-10 22:59:24'),
	(4, 5, '21', '1BC', 213400000, 0, 213400000, 20, 1, '2018-10-10 22:59:24', '2018-10-10 22:59:24'),
	(5, 5, '36', '2BA', 304920000, 30492000, 335412000, 340, 1, '2018-10-10 22:59:24', '2018-10-10 22:59:24'),
	(6, 5, '36', '2BC', 310640000, 31064000, 341704000, 20, 1, '2018-10-10 22:59:24', '2018-10-10 22:59:24');
/*!40000 ALTER TABLE `d_hunian_dtl` ENABLE KEYS */;

-- Dumping structure for table dp0.d_log_sistem
DROP TABLE IF EXISTS `d_log_sistem`;
CREATE TABLE IF NOT EXISTS `d_log_sistem` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `jenis` varchar(255) DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL,
  `request` varchar(255) DEFAULT NULL,
  `response` varchar(255) DEFAULT NULL,
  `id_user` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Dumping data for table dp0.d_log_sistem: ~0 rows (approximately)
DELETE FROM `d_log_sistem`;
/*!40000 ALTER TABLE `d_log_sistem` DISABLE KEYS */;
/*!40000 ALTER TABLE `d_log_sistem` ENABLE KEYS */;

-- Dumping structure for table dp0.t_agama
DROP TABLE IF EXISTS `t_agama`;
CREATE TABLE IF NOT EXISTS `t_agama` (
  `kdagama` int(10) NOT NULL DEFAULT '0',
  `nmagama` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`kdagama`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Dumping data for table dp0.t_agama: ~7 rows (approximately)
DELETE FROM `t_agama`;
/*!40000 ALTER TABLE `t_agama` DISABLE KEYS */;
INSERT INTO `t_agama` (`kdagama`, `nmagama`) VALUES
	(1, 'ISLAM'),
	(2, 'KRISTEN'),
	(3, 'KATHOLIK'),
	(4, 'HINDU'),
	(5, 'BUDHA'),
	(6, 'KHONGHUCU'),
	(7, 'ALIRAN KEPERCAYAAN');
/*!40000 ALTER TABLE `t_agama` ENABLE KEYS */;

-- Dumping structure for table dp0.t_alamat
DROP TABLE IF EXISTS `t_alamat`;
CREATE TABLE IF NOT EXISTS `t_alamat` (
  `kdalamat` varchar(2) NOT NULL,
  `nmalamat` varchar(255) DEFAULT NULL,
  `wajib` varchar(1) DEFAULT NULL,
  PRIMARY KEY (`kdalamat`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Dumping data for table dp0.t_alamat: ~4 rows (approximately)
DELETE FROM `t_alamat`;
/*!40000 ALTER TABLE `t_alamat` DISABLE KEYS */;
INSERT INTO `t_alamat` (`kdalamat`, `nmalamat`, `wajib`) VALUES
	('1', 'Alamat KTP', '1'),
	('2', 'Alamat Domisili', '1'),
	('3', 'Alamat Surat-menyurat', '0'),
	('4', 'Alamat Lainnya', '0');
/*!40000 ALTER TABLE `t_alamat` ENABLE KEYS */;

-- Dumping structure for table dp0.t_api_log
DROP TABLE IF EXISTS `t_api_log`;
CREATE TABLE IF NOT EXISTS `t_api_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `url` varchar(255) DEFAULT NULL,
  `header` text,
  `request` text,
  `response` text,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Dumping data for table dp0.t_api_log: ~0 rows (approximately)
DELETE FROM `t_api_log`;
/*!40000 ALTER TABLE `t_api_log` DISABLE KEYS */;
/*!40000 ALTER TABLE `t_api_log` ENABLE KEYS */;

-- Dumping structure for table dp0.t_api_setting
DROP TABLE IF EXISTS `t_api_setting`;
CREATE TABLE IF NOT EXISTS `t_api_setting` (
  `id` int(10) DEFAULT NULL,
  `uraian` varchar(255) DEFAULT NULL,
  `jenis` varchar(2) DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL,
  `user` varchar(255) DEFAULT NULL,
  `pass` varchar(255) DEFAULT NULL,
  `token` varchar(255) DEFAULT NULL,
  `aktif` varchar(2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Dumping data for table dp0.t_api_setting: ~1 rows (approximately)
DELETE FROM `t_api_setting`;
/*!40000 ALTER TABLE `t_api_setting` DISABLE KEYS */;
INSERT INTO `t_api_setting` (`id`, `uraian`, `jenis`, `url`, `user`, `pass`, `token`, `aktif`) VALUES
	(1, 'API DUKCAPIL', '1', 'http://uptik.or.id:4444/dprkp/wsdprkp.php', 'DPRKPDp0', '0Rup1ahDP', '-', '0');
/*!40000 ALTER TABLE `t_api_setting` ENABLE KEYS */;

-- Dumping structure for table dp0.t_app_version
DROP TABLE IF EXISTS `t_app_version`;
CREATE TABLE IF NOT EXISTS `t_app_version` (
  `versi` varchar(50) NOT NULL,
  `nama` varchar(50) DEFAULT NULL,
  `ket` varchar(255) DEFAULT NULL,
  `status` varchar(1) DEFAULT NULL,
  PRIMARY KEY (`versi`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT;

-- Dumping data for table dp0.t_app_version: ~1 rows (approximately)
DELETE FROM `t_app_version`;
/*!40000 ALTER TABLE `t_app_version` DISABLE KEYS */;
INSERT INTO `t_app_version` (`versi`, `nama`, `ket`, `status`) VALUES
	('v.1.0.0', 'Aplikasi', 'Prototype', '1');
/*!40000 ALTER TABLE `t_app_version` ENABLE KEYS */;

-- Dumping structure for table dp0.t_bpjs
DROP TABLE IF EXISTS `t_bpjs`;
CREATE TABLE IF NOT EXISTS `t_bpjs` (
  `kdbpjs` int(10) NOT NULL,
  `nmbpjs` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`kdbpjs`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Dumping data for table dp0.t_bpjs: ~5 rows (approximately)
DELETE FROM `t_bpjs`;
/*!40000 ALTER TABLE `t_bpjs` DISABLE KEYS */;
INSERT INTO `t_bpjs` (`kdbpjs`, `nmbpjs`) VALUES
	(0, '0'),
	(1, '1'),
	(2, '2'),
	(3, '3'),
	(4, 'diatas 3');
/*!40000 ALTER TABLE `t_bpjs` ENABLE KEYS */;

-- Dumping structure for table dp0.t_developer
DROP TABLE IF EXISTS `t_developer`;
CREATE TABLE IF NOT EXISTS `t_developer` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `nmdeveloper` varchar(255) NOT NULL,
  `npwp` varchar(15) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

-- Dumping data for table dp0.t_developer: ~1 rows (approximately)
DELETE FROM `t_developer`;
/*!40000 ALTER TABLE `t_developer` DISABLE KEYS */;
INSERT INTO `t_developer` (`id`, `nmdeveloper`, `npwp`, `created_at`, `updated_at`) VALUES
	(1, 'CV. Bangun Perkasa', '000000000000000', '2018-08-02 20:34:12', '2018-08-02 20:34:12');
/*!40000 ALTER TABLE `t_developer` ENABLE KEYS */;

-- Dumping structure for table dp0.t_dok
DROP TABLE IF EXISTS `t_dok`;
CREATE TABLE IF NOT EXISTS `t_dok` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `nmdok` varchar(255) DEFAULT NULL,
  `tipe` varchar(255) DEFAULT NULL,
  `is_wajib` varchar(1) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=latin1;

-- Dumping data for table dp0.t_dok: ~17 rows (approximately)
DELETE FROM `t_dok`;
/*!40000 ALTER TABLE `t_dok` DISABLE KEYS */;
INSERT INTO `t_dok` (`id`, `nmdok`, `tipe`, `is_wajib`) VALUES
	(1, 'e-KTP', 'jpg,png,pdf', '0'),
	(2, 'Kartu Keluarga (KK)', 'jpg,png,pdf', '0'),
	(3, 'NPWP', 'jpg,png,pdf', '0'),
	(4, 'Surat Ket.Penghasilan', 'jpg,png,pdf', '0'),
	(5, 'Foto Pribadi', 'jpg,png,pdf', '0'),
	(6, 'Form Permohonan', 'jpg,png,pdf', '1'),
	(7, 'Surat Pernyataan (Pergub)', 'jpg,png,pdf', '0'),
	(8, 'BPJS', 'jpg,png,pdf', '0'),
	(9, 'KJP', 'jpg,png,pdf', '0'),
	(10, 'Foto Kendaraan Roda 2', 'jpg,png,pdf', '0'),
	(11, 'Foto Kendaraan Roda 4', 'jpg,png,pdf', '0'),
	(12, 'Foto Tanah/Rumah', 'jpg,png,pdf', '0'),
	(13, 'Rek.Koran 3 bulan terakhir', 'jpg,png,pdf', '0'),
	(14, 'e-KTP (Pasangan)', 'jpg,png,pdf', '0'),
	(15, 'NPWP (Pasangan)', 'jpg,png,pdf', '0'),
	(16, 'Surat Ket.Penghasilan (Pasangan)', 'jpg,png,pdf', '0'),
	(17, 'Foto Pasangan', 'jpg,png,pdf', '0');
/*!40000 ALTER TABLE `t_dok` ENABLE KEYS */;

-- Dumping structure for table dp0.t_hutang
DROP TABLE IF EXISTS `t_hutang`;
CREATE TABLE IF NOT EXISTS `t_hutang` (
  `kdhutang` varchar(1) NOT NULL DEFAULT '',
  `nmhutang` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`kdhutang`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Dumping data for table dp0.t_hutang: ~4 rows (approximately)
DELETE FROM `t_hutang`;
/*!40000 ALTER TABLE `t_hutang` DISABLE KEYS */;
INSERT INTO `t_hutang` (`kdhutang`, `nmhutang`) VALUES
	('1', 'KPR'),
	('2', 'KTA'),
	('3', 'KKB'),
	('4', 'Lainnya');
/*!40000 ALTER TABLE `t_hutang` ENABLE KEYS */;

-- Dumping structure for table dp0.t_jenkredit
DROP TABLE IF EXISTS `t_jenkredit`;
CREATE TABLE IF NOT EXISTS `t_jenkredit` (
  `kdjenkredit` varchar(1) NOT NULL,
  `nmjenkredit` varchar(255) DEFAULT NULL,
  `aktif` varchar(1) DEFAULT NULL,
  PRIMARY KEY (`kdjenkredit`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Dumping data for table dp0.t_jenkredit: ~4 rows (approximately)
DELETE FROM `t_jenkredit`;
/*!40000 ALTER TABLE `t_jenkredit` DISABLE KEYS */;
INSERT INTO `t_jenkredit` (`kdjenkredit`, `nmjenkredit`, `aktif`) VALUES
	('1', 'DP 0 Rupiah', '1'),
	('2', 'Rusunawa', '0'),
	('3', 'Komersil', '0'),
	('4', 'Lainnya', '0');
/*!40000 ALTER TABLE `t_jenkredit` ENABLE KEYS */;

-- Dumping structure for table dp0.t_kabkota
DROP TABLE IF EXISTS `t_kabkota`;
CREATE TABLE IF NOT EXISTS `t_kabkota` (
  `kdprop` varchar(2) NOT NULL DEFAULT '',
  `kdkabkota` varchar(2) NOT NULL DEFAULT '',
  `nmkabkota` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`kdprop`,`kdkabkota`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Dumping data for table dp0.t_kabkota: ~6 rows (approximately)
DELETE FROM `t_kabkota`;
/*!40000 ALTER TABLE `t_kabkota` DISABLE KEYS */;
INSERT INTO `t_kabkota` (`kdprop`, `kdkabkota`, `nmkabkota`) VALUES
	('31', '01', 'ADM. KEPULAUAN SERIBU'),
	('31', '71', 'JAKARTA PUSAT'),
	('31', '72', 'JAKARTA UTARA'),
	('31', '73', 'JAKARTA BARAT'),
	('31', '74', 'JAKARTA SELATAN'),
	('31', '75', 'JAKARTA TIMUR');
/*!40000 ALTER TABLE `t_kabkota` ENABLE KEYS */;

-- Dumping structure for table dp0.t_kategori
DROP TABLE IF EXISTS `t_kategori`;
CREATE TABLE IF NOT EXISTS `t_kategori` (
  `kdkategori` varchar(3) NOT NULL,
  `nmkategori` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`kdkategori`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Dumping data for table dp0.t_kategori: ~2 rows (approximately)
DELETE FROM `t_kategori`;
/*!40000 ALTER TABLE `t_kategori` DISABLE KEYS */;
INSERT INTO `t_kategori` (`kdkategori`, `nmkategori`) VALUES
	('21', 'TIPE 21'),
	('36', 'TIPE 26');
/*!40000 ALTER TABLE `t_kategori` ENABLE KEYS */;

-- Dumping structure for table dp0.t_kawin
DROP TABLE IF EXISTS `t_kawin`;
CREATE TABLE IF NOT EXISTS `t_kawin` (
  `kdkawin` int(10) NOT NULL DEFAULT '0',
  `nmkawin` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`kdkawin`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Dumping data for table dp0.t_kawin: ~2 rows (approximately)
DELETE FROM `t_kawin`;
/*!40000 ALTER TABLE `t_kawin` DISABLE KEYS */;
INSERT INTO `t_kawin` (`kdkawin`, `nmkawin`) VALUES
	(1, 'KAWIN'),
	(2, 'TIDAK KAWIN');
/*!40000 ALTER TABLE `t_kawin` ENABLE KEYS */;

-- Dumping structure for table dp0.t_kecamatan
DROP TABLE IF EXISTS `t_kecamatan`;
CREATE TABLE IF NOT EXISTS `t_kecamatan` (
  `kdprop` varchar(2) NOT NULL,
  `kdkabkota` varchar(2) NOT NULL,
  `kdkec` varchar(2) NOT NULL,
  `nmkec` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`kdprop`,`kdkabkota`,`kdkec`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Dumping data for table dp0.t_kecamatan: ~44 rows (approximately)
DELETE FROM `t_kecamatan`;
/*!40000 ALTER TABLE `t_kecamatan` DISABLE KEYS */;
INSERT INTO `t_kecamatan` (`kdprop`, `kdkabkota`, `kdkec`, `nmkec`) VALUES
	('31', '01', '01', 'KEPULAUAN SERIBU UTARA'),
	('31', '01', '02', 'KEPULAUAN SERIBU SELATAN'),
	('31', '71', '01', 'GAMBIR'),
	('31', '71', '02', 'SAWAH BESAR'),
	('31', '71', '03', 'KEMAYORAN'),
	('31', '71', '04', 'S E N E N'),
	('31', '71', '05', 'CEMPAKA PUTIH'),
	('31', '71', '06', 'MENTENG'),
	('31', '71', '07', 'TANAH ABANG'),
	('31', '71', '08', 'JOHAR BARU'),
	('31', '72', '01', 'PENJARINGAN'),
	('31', '72', '02', 'TANJUNG PRIOK'),
	('31', '72', '03', 'KOJA'),
	('31', '72', '04', 'CILINCING'),
	('31', '72', '05', 'PADEMANGAN'),
	('31', '72', '06', 'KELAPA GADING'),
	('31', '73', '01', 'CENGKARENG'),
	('31', '73', '02', 'GROGOL PETAMBURAN'),
	('31', '73', '03', 'TAMAN SARI'),
	('31', '73', '04', 'TAMBORA'),
	('31', '73', '05', 'KEBON JERUK'),
	('31', '73', '06', 'KALI DERES'),
	('31', '73', '07', 'PAL MERAH'),
	('31', '73', '08', 'KEMBANGAN'),
	('31', '74', '01', 'TEBET'),
	('31', '74', '02', 'SETIA BUDI'),
	('31', '74', '03', 'MAMPANG PRAPATAN'),
	('31', '74', '04', 'PASAR MINGGU'),
	('31', '74', '05', 'KEBAYORAN LAMA'),
	('31', '74', '06', 'CILANDAK'),
	('31', '74', '07', 'KEBAYORAN BARU'),
	('31', '74', '08', 'PANCORAN'),
	('31', '74', '09', 'JAGAKARSA'),
	('31', '74', '10', 'PESANGGRAHAN'),
	('31', '75', '01', 'MATRAMAN'),
	('31', '75', '02', 'PULO GADUNG'),
	('31', '75', '03', 'JATINEGARA'),
	('31', '75', '04', 'KRAMATJATI'),
	('31', '75', '05', 'PASAR REBO'),
	('31', '75', '06', 'CAKUNG'),
	('31', '75', '07', 'DUREN SAWIT'),
	('31', '75', '08', 'MAKASAR'),
	('31', '75', '09', 'CIRACAS'),
	('31', '75', '10', 'CIPAYUNG');
/*!40000 ALTER TABLE `t_kecamatan` ENABLE KEYS */;

-- Dumping structure for table dp0.t_kelamin
DROP TABLE IF EXISTS `t_kelamin`;
CREATE TABLE IF NOT EXISTS `t_kelamin` (
  `kdkelamin` int(10) NOT NULL,
  `nmkelamin` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`kdkelamin`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Dumping data for table dp0.t_kelamin: ~2 rows (approximately)
DELETE FROM `t_kelamin`;
/*!40000 ALTER TABLE `t_kelamin` DISABLE KEYS */;
INSERT INTO `t_kelamin` (`kdkelamin`, `nmkelamin`) VALUES
	(1, 'LAKI-LAKI'),
	(2, 'PEREMPUAN');
/*!40000 ALTER TABLE `t_kelamin` ENABLE KEYS */;

-- Dumping structure for table dp0.t_kelurahan
DROP TABLE IF EXISTS `t_kelurahan`;
CREATE TABLE IF NOT EXISTS `t_kelurahan` (
  `kdprop` varchar(2) NOT NULL,
  `kdkabkota` varchar(2) NOT NULL,
  `kdkec` varchar(2) NOT NULL,
  `kdkel` varchar(4) NOT NULL,
  `nmkel` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`kdprop`,`kdkabkota`,`kdkec`,`kdkel`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT;

-- Dumping data for table dp0.t_kelurahan: ~267 rows (approximately)
DELETE FROM `t_kelurahan`;
/*!40000 ALTER TABLE `t_kelurahan` DISABLE KEYS */;
INSERT INTO `t_kelurahan` (`kdprop`, `kdkabkota`, `kdkec`, `kdkel`, `nmkel`) VALUES
	('31', '01', '01', '1001', 'PULAU PANGGANG'),
	('31', '01', '01', '1002', 'PULAU KELAPA'),
	('31', '01', '01', '1003', 'PULAU HARAPAN'),
	('31', '01', '02', '1001', 'PULAU UNTUNG JAWA'),
	('31', '01', '02', '1002', 'PULAU TIDUNG'),
	('31', '01', '02', '1003', 'PULAU PARI'),
	('31', '71', '01', '1001', 'GAMBIR'),
	('31', '71', '01', '1002', 'CIDENG'),
	('31', '71', '01', '1003', 'PETOJO UTARA'),
	('31', '71', '01', '1004', 'PETOJO SELATAN'),
	('31', '71', '01', '1005', 'KEBON KELAPA'),
	('31', '71', '01', '1006', 'DURI PULO'),
	('31', '71', '02', '1001', 'PASAR BARU'),
	('31', '71', '02', '1002', 'KARANG ANYAR'),
	('31', '71', '02', '1003', 'KARTINI'),
	('31', '71', '02', '1004', 'GUNUNG SAHARI UTARA'),
	('31', '71', '02', '1005', 'MANGGA DUA SELATAN'),
	('31', '71', '03', '1001', 'KEMAYORAN'),
	('31', '71', '03', '1002', 'KEBON KOSONG'),
	('31', '71', '03', '1003', 'HARAPAN MULIA'),
	('31', '71', '03', '1004', 'SERDANG'),
	('31', '71', '03', '1005', 'GUNUNG SAHARI SELATAN'),
	('31', '71', '03', '1006', 'CEMPAKA BARU'),
	('31', '71', '03', '1007', 'SUMUR BATU'),
	('31', '71', '03', '1008', 'UTAN PANJANG'),
	('31', '71', '04', '1001', 'SENEN'),
	('31', '71', '04', '1002', 'KENARI'),
	('31', '71', '04', '1003', 'PASEBAN'),
	('31', '71', '04', '1004', 'KRAMAT'),
	('31', '71', '04', '1005', 'KWITANG'),
	('31', '71', '04', '1006', 'BUNGUR'),
	('31', '71', '05', '1001', 'CEMPAKA PUTIH TIMUR'),
	('31', '71', '05', '1002', 'CEMPAKA PUTIH BARAT'),
	('31', '71', '05', '1003', 'RAWASARI'),
	('31', '71', '06', '1001', 'MENTENG'),
	('31', '71', '06', '1002', 'PEGANGSAAN'),
	('31', '71', '06', '1003', 'CIKINI'),
	('31', '71', '06', '1004', 'GONDANGDIA'),
	('31', '71', '06', '1005', 'KEBON SIRIH'),
	('31', '71', '07', '1001', 'GELORA'),
	('31', '71', '07', '1002', 'BENDUNGAN HILIR'),
	('31', '71', '07', '1003', 'KARET TENGSIN'),
	('31', '71', '07', '1004', 'PETAMBURAN'),
	('31', '71', '07', '1005', 'KEBON MELATI'),
	('31', '71', '07', '1006', 'KEBON KACANG'),
	('31', '71', '07', '1007', 'KAMPUNG BALI'),
	('31', '71', '08', '1001', 'JOHAR BARU'),
	('31', '71', '08', '1002', 'KAMPUNG RAWA'),
	('31', '71', '08', '1003', 'GALUR'),
	('31', '71', '08', '1004', 'TANAH TINGGI'),
	('31', '72', '01', '1001', 'PENJARINGAN'),
	('31', '72', '01', '1002', 'KAMAL MUARA'),
	('31', '72', '01', '1003', 'KAPUK MUARA'),
	('31', '72', '01', '1004', 'PEJAGALAN'),
	('31', '72', '01', '1005', 'PLUIT'),
	('31', '72', '02', '1001', 'TANJUNG PRIOK'),
	('31', '72', '02', '1002', 'SUNTER JAYA'),
	('31', '72', '02', '1003', 'PAPANGGO'),
	('31', '72', '02', '1004', 'SUNGAI BAMBU'),
	('31', '72', '02', '1005', 'KEBON BAWANG'),
	('31', '72', '02', '1006', 'SUNTER AGUNG'),
	('31', '72', '02', '1007', 'WARAKAS'),
	('31', '72', '03', '1001', 'KOJA'),
	('31', '72', '03', '1002', 'TUGU UTARA'),
	('31', '72', '03', '1003', 'LAGOA'),
	('31', '72', '03', '1004', 'RAWA BADAK UTARA'),
	('31', '72', '03', '1005', 'TUGU SELATAN'),
	('31', '72', '03', '1006', 'RAWA BADAK SELATAN'),
	('31', '72', '04', '1001', 'CILINCING'),
	('31', '72', '04', '1002', 'SUKAPURA'),
	('31', '72', '04', '1003', 'MARUNDA'),
	('31', '72', '04', '1004', 'KALIBARU'),
	('31', '72', '04', '1005', 'SEMPER TIMUR'),
	('31', '72', '04', '1006', 'ROROTAN'),
	('31', '72', '04', '1007', 'SEMPER BARAT'),
	('31', '72', '05', '1001', 'PADEMANGAN TIMUR'),
	('31', '72', '05', '1002', 'PADEMANGAN BARAT'),
	('31', '72', '05', '1003', 'ANCOL'),
	('31', '72', '06', '1001', 'KELAPA GADING TIMUR'),
	('31', '72', '06', '1002', 'PEGANGSAAN DUA'),
	('31', '72', '06', '1003', 'KELAPA GADING BARAT'),
	('31', '73', '01', '1001', 'CENGKARENG BARAT'),
	('31', '73', '01', '1002', 'DURI KOSAMBI'),
	('31', '73', '01', '1003', 'RAWA BUAYA'),
	('31', '73', '01', '1004', 'KEDAUNG KALI ANGKE'),
	('31', '73', '01', '1005', 'KAPUK'),
	('31', '73', '01', '1006', 'CENGKARENG TIMUR'),
	('31', '73', '02', '1001', 'GROGOL'),
	('31', '73', '02', '1002', 'TANJUNG DUREN UTARA'),
	('31', '73', '02', '1003', 'TOMANG'),
	('31', '73', '02', '1004', 'JELAMBAR'),
	('31', '73', '02', '1005', 'TANJUNG DUREN SELATAN'),
	('31', '73', '02', '1006', 'JELAMBAR BARU'),
	('31', '73', '02', '1007', 'WIJAYA KUSUMA'),
	('31', '73', '03', '1001', 'TAMAN SARI'),
	('31', '73', '03', '1002', 'KRUKUT'),
	('31', '73', '03', '1003', 'MAPHAR'),
	('31', '73', '03', '1004', 'TANGKI'),
	('31', '73', '03', '1005', 'MANGGA BESAR'),
	('31', '73', '03', '1006', 'KEAGUNGAN'),
	('31', '73', '03', '1007', 'GLODOK'),
	('31', '73', '03', '1008', 'PINANGSIA'),
	('31', '73', '04', '1001', 'TAMBORA'),
	('31', '73', '04', '1002', 'KALI ANYAR'),
	('31', '73', '04', '1003', 'DURI UTARA'),
	('31', '73', '04', '1004', 'TANAH SEREAL'),
	('31', '73', '04', '1005', 'KRENDANG'),
	('31', '73', '04', '1006', 'JEMBATAN BESI'),
	('31', '73', '04', '1007', 'ANGKE'),
	('31', '73', '04', '1008', 'JEMBATAN LIMA'),
	('31', '73', '04', '1009', 'PEKOJAN'),
	('31', '73', '04', '1010', 'ROA MALAKA'),
	('31', '73', '04', '1011', 'DURI SELATAN'),
	('31', '73', '05', '1001', 'KEBON JERUK'),
	('31', '73', '05', '1002', 'SUKABUMI UTARA'),
	('31', '73', '05', '1003', 'SUKABUMI SELATAN'),
	('31', '73', '05', '1004', 'KELAPA DUA'),
	('31', '73', '05', '1005', 'DURI KEPA'),
	('31', '73', '05', '1006', 'KEDOYA UTARA'),
	('31', '73', '05', '1007', 'KEDOYA SELATAN'),
	('31', '73', '06', '1001', 'KALIDERES'),
	('31', '73', '06', '1002', 'SEMANAN'),
	('31', '73', '06', '1003', 'TEGAL ALUR'),
	('31', '73', '06', '1004', 'KAMAL'),
	('31', '73', '06', '1005', 'PEGADUNGAN'),
	('31', '73', '07', '1001', 'PALMERAH'),
	('31', '73', '07', '1002', 'SLIPI'),
	('31', '73', '07', '1003', 'KOTA BAMBU UTARA'),
	('31', '73', '07', '1004', 'JATIPULO'),
	('31', '73', '07', '1005', 'KEMANGGISAN'),
	('31', '73', '07', '1006', 'KOTA BAMBU SELATAN'),
	('31', '73', '08', '1001', 'KEMBANGAN UTARA'),
	('31', '73', '08', '1002', 'MERUYA UTARA'),
	('31', '73', '08', '1003', 'MERUYA SELATAN'),
	('31', '73', '08', '1004', 'SRENGSENG'),
	('31', '73', '08', '1005', 'JOGLO'),
	('31', '73', '08', '1006', 'KEMBANGAN SELATAN'),
	('31', '74', '01', '1001', 'TEBET TIMUR'),
	('31', '74', '01', '1002', 'TEBET BARAT'),
	('31', '74', '01', '1003', 'MENTENG DALAM'),
	('31', '74', '01', '1004', 'KEBON BARU'),
	('31', '74', '01', '1005', 'BUKIT DURI'),
	('31', '74', '01', '1006', 'MANGGARAI SELATAN'),
	('31', '74', '01', '1007', 'MANGGARAI'),
	('31', '74', '02', '1001', 'SETIA BUDI'),
	('31', '74', '02', '1002', 'KARET SEMANGGI'),
	('31', '74', '02', '1003', 'KARET KUNINGAN'),
	('31', '74', '02', '1004', 'KARET'),
	('31', '74', '02', '1005', 'MENTENG ATAS'),
	('31', '74', '02', '1006', 'PASAR MANGGIS'),
	('31', '74', '02', '1007', 'GUNTUR'),
	('31', '74', '02', '1008', 'KUNINGAN TIMUR'),
	('31', '74', '03', '1001', 'MAMPANG PRAPATAN'),
	('31', '74', '03', '1002', 'BANGKA'),
	('31', '74', '03', '1003', 'PELA MAMPANG'),
	('31', '74', '03', '1004', 'TEGAL PARANG'),
	('31', '74', '03', '1005', 'KUNINGAN BARAT'),
	('31', '74', '04', '1001', 'PASAR MINGGU'),
	('31', '74', '04', '1002', 'JATI PADANG'),
	('31', '74', '04', '1003', 'CILANDAK TIMUR'),
	('31', '74', '04', '1004', 'RAGUNAN'),
	('31', '74', '04', '1005', 'PEJATEN TIMUR'),
	('31', '74', '04', '1006', 'PEJATEN BARAT'),
	('31', '74', '04', '1007', 'KEBAGUSAN'),
	('31', '74', '05', '1001', 'KEBAYORAN LAMA UTARA'),
	('31', '74', '05', '1002', 'PONDOK PINANG'),
	('31', '74', '05', '1003', 'CIPULIR'),
	('31', '74', '05', '1004', 'GROGOL UTARA'),
	('31', '74', '05', '1005', 'GROGOL SELATAN'),
	('31', '74', '05', '1006', 'KEBAYORAN LAMA SELATAN'),
	('31', '74', '06', '1001', 'CILANDAK BARAT'),
	('31', '74', '06', '1002', 'LEBAK BULUS'),
	('31', '74', '06', '1003', 'PONDOK LABU'),
	('31', '74', '06', '1004', 'GANDARIA SELATAN'),
	('31', '74', '06', '1005', 'CIPETE SELATAN'),
	('31', '74', '07', '1001', 'MELAWAI'),
	('31', '74', '07', '1002', 'GUNUNG'),
	('31', '74', '07', '1003', 'KRAMAT PELA'),
	('31', '74', '07', '1004', 'SELONG'),
	('31', '74', '07', '1005', 'RAWA BARAT'),
	('31', '74', '07', '1006', 'SENAYAN'),
	('31', '74', '07', '1007', 'PULO'),
	('31', '74', '07', '1008', 'PETOGOGAN'),
	('31', '74', '07', '1009', 'GANDARIA UTARA'),
	('31', '74', '07', '1010', 'CIPETE UTARA'),
	('31', '74', '08', '1001', 'PANCORAN'),
	('31', '74', '08', '1002', 'KALIBATA'),
	('31', '74', '08', '1003', 'RAWAJATI'),
	('31', '74', '08', '1004', 'DUREN TIGA'),
	('31', '74', '08', '1005', 'PENGADEGAN'),
	('31', '74', '08', '1006', 'CIKOKO'),
	('31', '74', '09', '1001', 'JAGAKARSA'),
	('31', '74', '09', '1002', 'SRENGSENG SAWAH'),
	('31', '74', '09', '1003', 'CIGANJUR'),
	('31', '74', '09', '1004', 'LENTENG AGUNG'),
	('31', '74', '09', '1005', 'TANJUNG BARAT'),
	('31', '74', '09', '1006', 'CIPEDAK'),
	('31', '74', '10', '1001', 'PESANGGRAHAN'),
	('31', '74', '10', '1002', 'BINTARO'),
	('31', '74', '10', '1003', 'PETUKANGAN UTARA'),
	('31', '74', '10', '1004', 'PETUKANGAN SELATAN'),
	('31', '74', '10', '1005', 'ULUJAMI'),
	('31', '75', '01', '1001', 'PISANGAN BARU'),
	('31', '75', '01', '1002', 'UTAN KAYU UTARA'),
	('31', '75', '01', '1003', 'KAYU MANIS'),
	('31', '75', '01', '1004', 'PALMERIAM'),
	('31', '75', '01', '1005', 'KEBON MANGGIS'),
	('31', '75', '01', '1006', 'UTAN KAYU SELATAN'),
	('31', '75', '02', '1001', 'PULO GADUNG'),
	('31', '75', '02', '1002', 'PISANGAN TIMUR'),
	('31', '75', '02', '1003', 'CIPINANG'),
	('31', '75', '02', '1004', 'JATINEGARA KAUM'),
	('31', '75', '02', '1005', 'RAWAMANGUN'),
	('31', '75', '02', '1006', 'KAYU PUTIH'),
	('31', '75', '02', '1007', 'JATI'),
	('31', '75', '03', '1001', 'KAMPUNG MELAYU'),
	('31', '75', '03', '1002', 'BIDARA CINA'),
	('31', '75', '03', '1003', 'BALI MESTER'),
	('31', '75', '03', '1004', 'RAWA BUNGA'),
	('31', '75', '03', '1005', 'CIPINANG CEMPEDAK'),
	('31', '75', '03', '1006', 'CIPINANG MUARA'),
	('31', '75', '03', '1007', 'CIPINANG BESAR SELATAN'),
	('31', '75', '03', '1008', 'CIPINANG BESAR UTARA'),
	('31', '75', '04', '1001', 'KRAMATJATI'),
	('31', '75', '04', '1002', 'TENGAH'),
	('31', '75', '04', '1003', 'DUKUH'),
	('31', '75', '04', '1004', 'BATU AMPAR'),
	('31', '75', '04', '1005', 'BALEKAMBANG'),
	('31', '75', '04', '1006', 'CILILITAN'),
	('31', '75', '04', '1007', 'CAWANG'),
	('31', '75', '05', '1001', 'GEDONG'),
	('31', '75', '05', '1002', 'BARU'),
	('31', '75', '05', '1003', 'CIJANTUNG'),
	('31', '75', '05', '1004', 'KALISARI'),
	('31', '75', '05', '1005', 'PEKAYON'),
	('31', '75', '06', '1001', 'JATINEGARA'),
	('31', '75', '06', '1002', 'RAWA TERATE'),
	('31', '75', '06', '1003', 'PENGGILINGAN'),
	('31', '75', '06', '1004', 'CAKUNG TIMUR'),
	('31', '75', '06', '1005', 'PULO GEBANG'),
	('31', '75', '06', '1006', 'UJUNG MENTENG'),
	('31', '75', '06', '1007', 'CAKUNG BARAT'),
	('31', '75', '07', '1001', 'DUREN SAWIT'),
	('31', '75', '07', '1002', 'PONDOK BAMBU'),
	('31', '75', '07', '1003', 'KLENDER'),
	('31', '75', '07', '1004', 'PONDOK KELAPA'),
	('31', '75', '07', '1005', 'MALAKA SARI'),
	('31', '75', '07', '1006', 'MALAKA JAYA'),
	('31', '75', '07', '1007', 'PONDOK KOPI'),
	('31', '75', '08', '1001', 'MAKASAR'),
	('31', '75', '08', '1002', 'PINANGRANTI'),
	('31', '75', '08', '1003', 'KEBON PALA'),
	('31', '75', '08', '1004', 'HALIM PERDANA KUSUMAH'),
	('31', '75', '08', '1005', 'CIPINANG MELAYU'),
	('31', '75', '09', '1001', 'CIRACAS'),
	('31', '75', '09', '1002', 'CIBUBUR'),
	('31', '75', '09', '1003', 'KELAPA DUA WETAN'),
	('31', '75', '09', '1004', 'SUSUKAN'),
	('31', '75', '09', '1005', 'RAMBUTAN'),
	('31', '75', '10', '1001', 'CIPAYUNG'),
	('31', '75', '10', '1002', 'CILANGKAP'),
	('31', '75', '10', '1003', 'PONDOK RANGGON'),
	('31', '75', '10', '1004', 'MUNJUL'),
	('31', '75', '10', '1005', 'SETU'),
	('31', '75', '10', '1006', 'BAMBU APUS'),
	('31', '75', '10', '1007', 'LUBANG BUAYA'),
	('31', '75', '10', '1008', 'CEGER');
/*!40000 ALTER TABLE `t_kelurahan` ENABLE KEYS */;

-- Dumping structure for table dp0.t_kppn
DROP TABLE IF EXISTS `t_kppn`;
CREATE TABLE IF NOT EXISTS `t_kppn` (
  `kdkppn` char(3) NOT NULL,
  `tipekppn` char(1) DEFAULT NULL,
  `kdktua` char(2) DEFAULT NULL,
  `kdkanwil` char(2) DEFAULT NULL,
  `kddatidua` char(2) DEFAULT NULL,
  `kdlokasi` char(2) DEFAULT NULL,
  `nmkppn` varchar(35) DEFAULT NULL,
  `almkppn` varchar(35) DEFAULT NULL,
  `telkppn` varchar(70) DEFAULT NULL,
  `kotakppn` varchar(35) DEFAULT NULL,
  `kddefa` char(1) DEFAULT NULL,
  `email` varchar(35) DEFAULT NULL,
  `kdkcbi` char(1) DEFAULT NULL,
  `kodepos` varchar(5) DEFAULT NULL,
  `faxkppn` varchar(70) DEFAULT NULL,
  `kdjenkppn` char(1) NOT NULL DEFAULT '0',
  `nmkppni` varchar(35) NOT NULL,
  `kotakppni` varchar(35) NOT NULL,
  PRIMARY KEY (`kdkppn`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Dumping data for table dp0.t_kppn: ~179 rows (approximately)
DELETE FROM `t_kppn`;
/*!40000 ALTER TABLE `t_kppn` DISABLE KEYS */;
INSERT INTO `t_kppn` (`kdkppn`, `tipekppn`, `kdktua`, `kdkanwil`, `kddatidua`, `kdlokasi`, `nmkppn`, `almkppn`, `telkppn`, `kotakppn`, `kddefa`, `email`, `kdkcbi`, `kodepos`, `faxkppn`, `kdjenkppn`, `nmkppni`, `kotakppni`) VALUES
	('001', 'A', '01', '01', '00', '06', 'BANDA ACEH', 'Jl.', '(0651) 22460', 'BANDA ACEH', '0', 'kpkn001@wasantara.net.id', '0', '23241', '(0651) 32612', '0', '', ''),
	('002', 'B', '01', '01', '00', '06', 'LANGSA', 'Jl. Jend. A. Yani No. 2', '', 'LANGSA', '0', '', '0', '', '', '0', '', ''),
	('003', 'B', '01', '01', '00', '06', 'MEULABOH', 'Jl. Sisingamangaraja No. 3', '', 'MEULABOH', '0', '', '0', '', '', '0', '', ''),
	('004', 'A', '02', '02', '00', '07', 'MEDAN I', 'Jl. Diponegoro No. 30 A', '', 'MEDAN', '0', '', '', '', '', '0', '', ''),
	('005', 'B', '03', '02', '00', '07', 'PEMATANG SIANTAR', 'Jl. Brigjen. Rajamin Purba, SH', '', 'PEMATANG SIANTAR', '0', '', '', '', '', '0', '', ''),
	('006', 'B', '02', '02', '12', '07', 'PADANG SIDEMPUAN', 'Jl. Kenangan No. 50', '', 'PADANG SIDEMPUAN', '0', '', '0', '', '', '0', '', ''),
	('007', 'B', '02', '02', '00', '07', 'GUNUNG SITOLI', 'Jl. Pancasila No. 13', '', 'GUNUNG SITOLI', '0', '', '', '', '', '0', '', ''),
	('008', 'B', '05', '04', '00', '09', 'PEKANBARU', 'Jl. Jend. Sudirman No. 249', '', 'PEKANBARU', '0', '', '0', '', '', '0', '', ''),
	('009', 'B', '05', '04', '00', '32', 'TANJUNG PINANG', 'Jl. Diponegoro No. 5', '', 'TANJUNG PINANG', '0', '', '', '', '', '0', '', ''),
	('010', 'A', '04', '03', '00', '08', 'PADANG', 'Jl. Perintis Kemerdekaan No. 79', '', 'PADANG', '0', '', '0', '', '', '0', '', ''),
	('011', 'A', '04', '03', '00', '08', 'BUKIT TINGGI', 'Jl. Batang Agam Belakang Balok', '', 'BUKITTINGGI', '0', '', '', '', '', '0', '', ''),
	('012', 'B', '07', '05', '00', '10', 'JAMBI', 'Jl. Jend Ahmand Yani No. 7', '', 'JAMBI', '0', '', '', '', '', '0', '', ''),
	('013', 'B', '07', '05', '00', '10', 'SUNGAI PENUH', 'Jl. H. Bahri No. 16', '', 'SUNGAI PENUH', '0', '', '', '', '', '0', '', ''),
	('014', 'A', '06', '06', '60', '11', 'PALEMBANG', 'Jl. Kapten A. Rifai No. 2', '', 'PALEMBANG', '0', '', '', '', '', '1', '', ''),
	('015', 'B', '06', '09', '61', '30', 'PANGKAL PINANG', 'Jl. Kejaksaan Negeri', '', 'PANGKAL PINANG', '0', '', '', '', '', '0', '', ''),
	('016', 'B', '09', '08', '00', '26', 'BENGKULU', 'Jl. Sukarno - Hatta', '', 'BENGKULU', '0', '', '', '', '', '0', '', ''),
	('017', 'B', '08', '07', '00', '12', 'BANDAR LAMPUNG', 'Jl. Jend. Gatot Subroto No. 91', '', 'BANDAR LAMPUNG', '0', '', '', '', '', '0', '', ''),
	('018', 'A', '10', '11', '60', '01', 'JAKARTA I', 'Jl. Ir. H. Juanda No 19', '', 'JAKARTA', '0', '', '1', '', '', '1', '', ''),
	('019', 'A', '12', '11', '60', '01', 'JAKARTA II', 'Jl. DR. Wahidin II No. 3', '', 'JAKARTA', '0', '', '', '', '', '1', '', ''),
	('020', 'B', '13', '10', '00', '29', 'SERANG', 'Jl. KH. A. Fatah Hasan', '', 'SERANG', '0', '', '', '', '', '0', '', ''),
	('021', 'B', '13', '12', '20', '02', 'PURWAKARTA', 'Jl. Ibrahim Singadilaga No. 65', '', 'PURWAKARTA', '0', '', '', '', '', '0', '', ''),
	('022', 'A', '13', '12', '61', '02', 'BANDUNG I', 'Jl. Asia Afrika No. 114', '', 'BANDUNG', '0', '', '', '', '', '0', '', ''),
	('023', 'B', '13', '12', '62', '02', 'BOGOR', 'Jl. Ir. H. Juanda No 62', '', 'BOGOR', '0', '', '', '', '', '0', '', ''),
	('024', 'B', '14', '12', '17', '02', 'CIREBON', 'Jl. Tuparev No. 14', '', 'CIREBON', '0', '', '', '', '', '0', '', ''),
	('025', 'B', '14', '12', '12', '02', 'TASIKMALAYA', 'Jl. Manonjaya No. 50 Cibereum', '', 'TASIKMALAYA', '0', '', '', '', '', '0', '', ''),
	('026', 'A', '15', '13', '00', '03', 'SEMARANG I', 'Jl. Ki Mangunsarkoro No. 34', '', 'SEMARANG', '0', '', '', '', '', '0', '', ''),
	('027', 'B', '16', '13', '06', '03', 'PURWOREJO', 'Jl. Urip Sumoharjo No. 83', '', 'PURWOREJO', '0', '', '', '', '', '0', '', ''),
	('028', 'A', '15', '13', '00', '03', 'SURAKARTA', 'Jl. Slamet Riyadi No. 467', '', 'SURAKARTA', '0', '', '', '', '', '0', '', ''),
	('029', 'B', '16', '13', '31', '03', 'PURWOKERTO', 'Jl. D.I. Panjaitan No. 62', '', 'PURWOKERTO', '0', '', '', '', '', '0', '', ''),
	('030', 'A', '17', '14', '60', '04', 'YOGYAKARTA', 'Jl. Kusumanegara No. 11', '', 'YOGYAKARTA', '0', '', '', '', '', '1', '', ''),
	('031', 'A', '18', '15', '60', '05', 'SURABAYA I', 'Jl. Indrapura No. 5', '', 'SURABAYA', '0', '', '', '', '', '0', '', ''),
	('032', 'B', '19', '15', '61', '05', 'MALANG', 'Jl. Mederka Selatan No 2', '', 'MALANG', '0', '', '', '', '', '0', '', ''),
	('033', 'B', '19', '15', '62', '05', 'MADIUN', 'Jl. Salak No. 52', '', 'MADIUN', '0', '', '', '', '', '0', '', ''),
	('034', 'B', '19', '15', '63', '05', 'KEDIRI', 'Jl. P.K. Bangsa No. 53', '', 'KEDIRI', '0', '', '1', '', '', '0', '', ''),
	('035', 'B', '18', '15', '22', '05', 'BONDOWOSO', 'XX', '', 'BONDOWOSO', '0', '', '', '', '', '0', '', ''),
	('036', 'B', '18', '15', '26', '05', 'PAMEKASAN', 'Jl. Jokotole No. 55C', '', 'PAMEKASAN', '0', '', '', '', '', '0', '', ''),
	('037', 'A', '24', '20', '00', '22', 'DENPASAR', 'Jl. DR. Kusumaatmaja Niti Mandala', '', 'DENPASAR', '0', '', '', '', '', '1', '', ''),
	('038', 'B', '25', '21', '00', '23', 'MATARAM', 'Jl. Langko No. 40', '', 'MATARAM', '0', '', '', '', '', '1', '', ''),
	('039', 'B', '26', '22', '00', '24', 'KUPANG', 'Jl. El Tari II Walikota Baru', '', 'KUPANG', '0', '', '', '', '', '1', '', ''),
	('040', 'B', '26', '22', '00', '24', 'ENDE', 'Jl. Kelimutu No. 53', '', 'ENDE', '0', '', '', '', '', '0', '', ''),
	('041', 'B', '26', '22', '00', '24', 'WAINGAPU', 'Jl. Ampera', '', 'WAINGAPU', '0', '', '', '', '', '0', '', ''),
	('042', 'B', '20', '16', '00', '13', 'PONTIANAK', 'Jl. K. Sasuit Tubun No. 36', '', 'PONTIANAK', '0', '', '', '', '', '1', '', ''),
	('043', 'B', '21', '17', '60', '14', 'PALANGKARAYA', 'Jl. P. Tendean No. 4', '', 'PALANGKARAYA', '0', '', '', '', '', '0', '', ''),
	('044', 'B', '21', '17', '00', '14', 'SAMPIT', 'JL.', '', 'SAMPIT', '0', '', '', '', '', '0', '', ''),
	('045', 'B', '22', '18', '60', '15', 'BANJARMASIN', 'Jl. Mayjen DI. Panjaitan No. 10', '', 'BANJARMASIN', '0', '', '', '', '', '1', '', ''),
	('046', 'B', '23', '19', '60', '16', 'SAMARINDA', 'Jl. Moh Yamin No. 25', '', 'SAMARINDA', '0', '', '', '', '', '0', '', ''),
	('047', 'B', '23', '19', '00', '16', 'BALIKPAPAN', 'Jl. A. Yani 28', '', 'BALIKPAPAN', '0', '', '', '', '', '0', '', ''),
	('048', 'B', '23', '19', '00', '16', 'TARAKAN', 'Jl. Gunung Belah', '', 'TARAKAN', '0', '', '', '', '', '0', '', ''),
	('049', 'B', '31', '27', '00', '17', 'MANADO', 'Jl. Bethesda 8', '', 'MANADO', '0', '', '', '', '', '1', '', ''),
	('050', 'B', '31', '26', '00', '31', 'GORONTALO', 'Jl. Jend Sudirman No. 58', '', 'GORONTALO', '0', '', '', '', '', '1', '', ''),
	('051', 'B', '32', '24', '00', '18', 'PALU', 'Jl. Tanjung Dako No 11', '', 'P A L S U', '0', '', '0', '', '', '0', '', ''),
	('052', 'B', '32', '24', '00', '18', 'POSO', 'Jl. Kalimantan No. 16', '', 'POSO', '0', '', '', '', '', '0', '', ''),
	('053', 'B', '32', '24', '04', '18', 'LUWUK', 'Jl. Jend A. Yani No 4', '', 'LUWUK', '0', '', '', '', '', '0', '', ''),
	('054', 'A', '28', '23', '00', '19', 'MAKASSARI', 'Jl. Jend Urip Sumoharjo KM 4', '', 'MAKASSAR', '0', '', '', '', '', '0', '', ''),
	('055', 'B', '29', '23', '00', '19', 'WATAMPONE', 'Jl. Basso Kayuara No. 7', '', 'WATAMPONE', '0', '', '', '', '', '0', '', ''),
	('056', 'B', '29', '23', '10', '19', 'BANTAENG', 'Jl. Teratai No. 7', '', 'BANTAENG', '0', '', '', '', '', '0', '', ''),
	('057', 'B', '28', '23', '61', '19', 'PARE-PARE', 'Jl. Karaeong Burane No 20', '', 'PARE - PARE', '0', '', '', '', '', '0', '', ''),
	('058', 'B', '29', '23', '00', '19', 'PALOPO', 'Jl. Opu Tossapaile', '', 'PALOPO', '0', '', '', '', '', '0', '', ''),
	('059', 'B', '28', '23', '19', '19', 'MAJENE', 'Jl. Jend Sudirman No. 96', '', 'MAJENE', '0', '', '', '', '', '0', '', ''),
	('060', 'B', '30', '25', '00', '20', 'KENDARI', 'Jl. Mayjend Sutopo No. 5', '', 'KENDARI', '0', '', '', '', '', '0', '', ''),
	('061', 'B', '33', '29', '00', '21', 'AMBON', 'Jl. Kapitan Ulupaha No. 1', '', 'AMBON', '0', '', '', '', '', '1', '', ''),
	('062', 'B', '33', '28', '00', '28', 'TERNATE', 'Jl. Yos sudarso', '', 'TERNATE', '0', '', '', '', '', '0', '', ''),
	('063', 'B', '34', '30', '00', '25', 'JAYAPURA', 'Jl. Jend A. Yani No. 8', '', 'JAYAPURA', '0', '', '', '', '', '1', '', ''),
	('064', 'B', '34', '30', '00', '25', 'BIAK', 'Jl. Majapahit', '', 'BIAK', '0', '', '', '', '', '0', '', ''),
	('065', 'B', '34', '30', '00', '33', 'MANOKWARI', 'Jl. Yos Sudarso No. 1003', '', 'MANOKWARI', '0', '', '', '', '', '0', '', ''),
	('066', 'B', '34', '30', '00', '33', 'SORONG', 'Jl. Basuki Rachmad Km.7', '', 'SORONG', '0', '', '', '', '', '0', '', ''),
	('067', 'B', '34', '30', '00', '33', 'FAK-FAK', 'Jl. Jend A. Yani', '', 'FAK - FAK', '0', '', '', '', '', '0', '', ''),
	('068', 'B', '34', '30', '00', '25', 'MERAUKE', 'Jl. Prajurit No. 1', '', 'MERAUKE', '0', '', '', '', '', '0', '', ''),
	('070', '', '', '06', '', '11', 'LUBUK LINGGAU', '', '', '', '0', '', '', '', '', '0', '', ''),
	('071', 'B', '25', '21', '00', '23', 'BIMA', 'Jl. Pendidikan No. 16', '', 'BIMA', '0', '', '', '', '', '0', '', ''),
	('072', 'B', '16', '13', '00', '03', 'PEKALONGAN', 'Jl. Bahagia No. 44', '', 'PEKALONGAN', '0', '', '', '', '', '0', '', ''),
	('073', 'B', '19', '15', '05', '05', 'BOJONEGORO', 'Jl. Untung Suropati No. 63', '', 'BOJONEGORO', '0', '', '', '', '', '0', '', ''),
	('074', 'B', '01', '01', '00', '06', 'TAPAK TUAN', 'Jl. Jend. Sudirman No. 69', '', 'TAPAK TUAN', '0', '', '', '', '', '0', '', ''),
	('075', 'B', '03', '02', '00', '07', 'RANTAU PRAPAT', 'Jl. Sisingamangaraja No. 62', '', 'RANTAU PRAPAT', '0', '', '', '', '', '0', '', ''),
	('076', 'B', '03', '02', '00', '07', 'TANJUNG BALAI ASAHAN', 'Jl. Pahlawan No. 22', '', 'TANJUNG BALAI', '0', '', '', '', '', '0', '', ''),
	('077', '', '', '03', '', '08', 'SIJUNJUNG', '', '', '', '0', '', '', '', '', '0', '', ''),
	('078', 'B', '07', '05', '00', '10', 'MUARA BUNGO', 'Jl. Sultan Thaha', '', 'MUARA BUNGO', '0', '', '', '', '', '0', '', ''),
	('079', 'B', '20', '16', '00', '13', 'SINTANG', 'Jl. Adi Sucipto No. 1', '', 'SINTANG', '0', '', '', '', '', '0', '', ''),
	('080', 'B', '21', '17', '00', '14', 'BUNTOK', 'Jl. Pelita Raya No. 341', '', 'BUNTOK', '0', '', '', '', '', '0', '', ''),
	('081', 'B', '22', '18', '00', '15', 'KOTABARU', 'Jl Yakut', '', 'KOTABARU', '0', '', '', '', '', '0', '', ''),
	('082', 'B', '32', '24', '00', '18', 'TOLI-TOLI', 'Jl. Magamu No 6-8', '', 'TOLI - TOLI', '0', '', '', '', '', '0', '', ''),
	('083', 'B', '31', '27', '00', '17', 'TAHUNA', 'Jl. Malahasa', '', 'TAHUNA', '0', '', '', '', '', '0', '', ''),
	('084', 'B', '33', '29', '00', '21', 'TUAL', 'Jl. Batu Watdek', '', 'TUAL', '0', '', '', '', '', '0', '', ''),
	('085', 'B', '34', '30', '00', '25', 'NABIRE', 'Jl. Merdeka No. 46', '', 'NABIRE', '0', '', '', '', '', '0', '', ''),
	('086', 'B', '13', '12', '21', '02', 'KARAWANG', 'Jl. Kertabumi', '', 'KARAWANG', '0', '', '', '', '', '0', '', ''),
	('087', 'B', '14', '12', '10', '02', 'SUMEDANG', 'JL.', '', 'SUMEDANG', '0', '', '', '', '', '0', '', ''),
	('088', 'A', '11', '11', '00', '01', 'JAKARTA III', 'Jl. Otto Iskandar Dinata No. 53-55', '', 'JAKARTA', '0', '', '', '', '', '0', '', ''),
	('089', 'B', '01', '01', '00', '06', 'LHOKSEUMAWE', 'Jl. Pasar Inpres No. 1', '', 'LHOK SEUMAWE', '0', '', '', '', '', '0', '', ''),
	('090', 'B', '04', '03', '00', '08', 'SOLOK', 'Jl. Kota Baru', '', 'SOLOK', '0', '', '', '', '', '0', '', ''),
	('091', 'B', '04', '03', '00', '08', 'LUBUK SIKAPING', 'Jl. Jend Sudirman No. 93', '', 'LUBUK SIKAPING', '0', '', '', '', '', '0', '', ''),
	('092', 'B', '05', '04', '00', '09', 'RENGAT', 'Jl. Diponegoro No. 2', '', 'RENGAT', '0', '', '', '', '', '0', '', ''),
	('093', 'B', '20', '16', '00', '13', 'SINGKAWANG', 'Jl. Firdaus H. Rais No. 65', '', 'SINGKAWANG', '0', '', '', '', '', '0', '', ''),
	('094', 'B', '20', '16', '00', '13', 'KETAPANG', 'Jl. Jend. Sudirman No. 55', '', 'KETAPANG', '0', '', '', '', '', '0', '', ''),
	('095', 'A', '13', '12', '61', '02', 'BANDUNG II', 'Jl. Asia Afrika No. 114', '', 'BANDUNG', '0', '', '', '', '', '1', '', ''),
	('096', 'B', '14', '12', '11', '02', 'GARUT', 'XX', '', 'GARUT', '0', '', '', '', '', '0', '', ''),
	('097', 'B', '15', '13', '00', '03', 'PATI', 'Jl. Diponegoro No. 102', '', 'PATI', '0', '', '', '', '', '0', '', ''),
	('098', 'B', '19', '15', '00', '05', 'MOJOKERTO', 'Jl. Gajah Mada No. 147', '', 'MOJOKERTO', '0', '', '', '', '', '0', '', ''),
	('099', 'B', '19', '15', '12', '05', 'PACITAN', 'Jl. Letjen. S. Parman No. 47', '', 'PACITAN', '0', '', '', '', '', '0', '', ''),
	('100', 'B', '18', '15', '00', '05', 'BANYUWANGI', 'Jl. Jend. A. Yani  No. 118', '', 'BANYUWANGI', '0', '', '', '', '', '0', '', ''),
	('101', 'B', '25', '21', '00', '23', 'SUMBAWA BESAR', 'Jl. Garuda No. 107', '', 'SUMBAWA BESAR', '0', '', '', '', '', '0', '', ''),
	('102', 'B', '21', '17', '00', '14', 'PANGKALAN BUN', 'Jl. Sutan Sahrir No. 9 Kotak Pos 18', '', 'PANGKALAN BUN', '0', '', '', '', '', '0', '', ''),
	('103', 'B', '30', '25', '00', '20', 'BAU-BAU', 'Jl. Anoa No. 1', '', 'BAU - BAU', '0', '', '', '', '', '0', '', ''),
	('104', 'B', '33', '29', '00', '21', 'SAUMLAKI', 'XX', '', 'SAUMLAKI', '0', '', '', '', '', '0', '', ''),
	('105', 'B', '01', '01', '00', '06', 'KUTACANE', 'Jl. Blangkejeran Km. 3', '', 'KUTACANE', '0', '', '', '', '', '0', '', ''),
	('106', 'B', '03', '02', '00', '07', 'SIBOLGA', 'Jl. DR. Sutomo No. 7', '', 'SIBOLGA', '0', '', '', '', '', '0', '', ''),
	('107', 'B', '06', '09', '00', '30', 'TANJUNG PANDAN', 'Jl. Sriwijaya Pali 1', '', 'TANJUNG PANDAN', '0', '', '', '', '', '0', '', ''),
	('109', 'B', '06', '06', '00', '11', 'BATURAJA', 'Jl. Jend P. Panjaitan No. 471', '', 'BATURAJA', '0', '', '', '', '', '0', '', ''),
	('110', 'B', '22', '18', '00', '15', 'BARABAI', 'Jl. Ir. PHM. Noor No. 28', '', 'BARABAI', '0', '', '', '', '', '0', '', ''),
	('111', 'B', '26', '22', '00', '24', 'RUTENG', 'Jl. Adi Sucipto', '', 'RUTENG', '0', '', '', '', '', '0', '', ''),
	('112', '', '33', '28', '', '28', 'TOBELO', '', '', '', '0', '', '', '', '', '0', '', ''),
	('113', 'B', '34', '30', '00', '25', 'WAMENA', 'Jl. Yos Sudarso', '', 'WAMENA', '0', '', '', '', '', '0', '', ''),
	('115', 'B', '16', '13', '60', '03', 'MAGELANG', 'Jl. Veteran No. 3', '', 'MAGELANG', '0', '', '', '', '', '0', '', ''),
	('116', 'B', '08', '07', '00', '12', 'KOTABUMI', 'Jl. Jend. Sudirman Km.3', '', 'KOTA BUMI', '0', '', '', '', '', '0', '', ''),
	('117', 'B', '20', '16', '00', '13', 'PUTUSSIBAU', 'Jl. Wr. Soepratman', '', 'PUTUSSIBAU', '0', '', '', '', '', '0', '', ''),
	('118', 'B', '16', '13', '28', '03', 'TEGAL', 'XX', '', 'TEGAL', '0', '', '', '', '', '0', '', ''),
	('119', '', '', '02', '00', '07', 'SIDIKALANG', '', '', '', '0', '', '', '', '', '0', '', ''),
	('120', 'B', '05', '04', '00', '09', 'DUMAI', 'Jl. Jend Sudirman No. 25', '', 'DUMAI', '0', '', '', '', '', '0', '', ''),
	('121', 'B', '09', '08', '00', '26', 'MANNA', 'Jl. Affan Baksin No. 103', '', 'MANNA', '0', '', '', '', '', '0', '', ''),
	('122', 'B', '01', '01', '00', '06', 'TAKENGON', 'Jl. Rumah sakit Umum No. 98', '', 'TAKENGON', '0', '', '', '', '', '0', '', ''),
	('123', 'A', '02', '02', '00', '07', 'MEDAN II', 'Jl. Diponegoro No. 30 A', '', 'MEDAN', '0', '', '', '', '', '1', '', ''),
	('124', 'B', '03', '02', '00', '07', 'TEBING TINGGI', 'XX', '', 'TEBING TINGGI', '0', '', '', '', '', '0', '', ''),
	('125', 'B', '02', '02', '00', '07', 'BALIGE', 'Jl. Pelabuhan No.2', '', 'BALIGE', '0', '', '', '', '', '0', '', ''),
	('126', 'B', '08', '07', '00', '12', 'METRO LAMPUNG', 'XX', '', 'METRO LAMPUNG', '0', '', '', '', '', '0', '', ''),
	('127', '', '', '10', '', '29', 'TANGERANG', '', '', '', '0', '', '', '', '', '0', '', ''),
	('128', 'B', '13', '12', '06', '02', 'SUKABUMI', 'Jl. Suryakencana No. 20', '', 'SUKABUMI', '0', '', '', '', '', '0', '', ''),
	('129', 'B', '15', '13', '19', '03', 'KUDUS', 'XX', '', 'KUDUS', '0', '', '', '', '', '0', '', ''),
	('130', 'B', '16', '13', '30', '03', 'CILACAP', 'XX', '', 'CILACAP', '0', '', '', '', '', '0', '', ''),
	('131', 'B', '18', '15', '30', '05', 'JEMBER', 'Jl. Kalimantan No. 35', '', 'JEMBER', '0', '', '', '', '', '0', '', ''),
	('132', 'B', '24', '20', '00', '22', 'SINGARAJA', 'Jl. Udayana No. 10', '', 'SINGARAJA', '0', '', '', '', '', '0', '', ''),
	('133', 'A', '10', '11', '00', '01', 'JAKARTA IV', 'Jl. Ir. H. Juanda No 19            ', '021-3516657                                                           ', 'JAKARTA                            ', '1', '                                   ', '1', '     ', '                                                                      ', '1', '', ''),
	('134', 'A', '15', '13', '00', '03', 'SEMARANG II', 'Jl. Ki Mangunsarkoro No. 34', '', 'SEMARANG', '0', '', '', '', '', '1', '', ''),
	('135', 'A', '18', '15', '60', '05', 'SURABAYA II', 'Jl. Indrapura No. 5', '', 'SURABAYA', '0', '', '', '', '', '1', '', ''),
	('136', 'A', '28', '23', '00', '19', 'MAKASSAR II', 'Jl. Jend Urip Sumoharjo KM 4', '', 'MAKASSAR', '0', '', '', '', '', '1', '', ''),
	('137', 'B', '05', '04', '61', '32', 'BATAM', 'Jl. Raja Haji - Sekupang', '', 'BATAM', '0', '', '', '', '', '0', '', ''),
	('138', 'B', '34', '30', '11', '25', 'SERUI', 'Jl. Maluku', '', 'SERUI', '0', '', '', '', '', '0', '', ''),
	('139', 'A', '11', '11', '00', '01', 'JAKARTA V', 'Jl. TB. Simatupang Jkt. Selatan', '', 'JAKARTA', '0', '', '', '', '', '0', '', ''),
	('140', '', '', '11', '', '01', 'JAKARTA VI(KHUSUS)', '', '', '', '0', '', '', '', '', '0', '', ''),
	('141', '', '', '30', '', '25', 'TIMIKA', '', '', '', '0', '', '', '', '', '0', '', ''),
	('142', '', '', '03', '', '08', 'PAINAN', '', '', '', '0', '', '', '', '', '0', '', ''),
	('143', '', '', '05', '', '10', 'KUALA TUNGKAL', '', '', '', '0', '', '', '', '', '0', '', ''),
	('144', '', '', '06', '', '11', 'LAHAT', '', '', '', '0', '', '', '', '', '0', '', ''),
	('145', '', '', '07', '', '12', 'LIWA', '', '', '', '0', '', '', '', '', '0', '', ''),
	('146', '', '', '08', '', '26', 'CURUP', '', '', '', '0', '', '', '', '', '0', '', ''),
	('147', '', '', '12', '', '02', 'KUNINGAN', '', '', '', '0', '', '', '', '', '0', '', ''),
	('148', '', '', '13', '', '03', 'KLATEN', '', '', '', '0', '', '', '', '', '0', '', ''),
	('149', '', '', '14', '', '04', 'WONOSARI', '', '', '', '0', '', '', '', '', '0', '', ''),
	('150', '', '', '15', '', '05', 'BLITAR', '', '', '', '0', '', '', '', '', '0', '', ''),
	('151', '', '', '18', '', '15', 'TANJUNG', '', '', '', '0', '', '', '', '', '0', '', ''),
	('152', '', '', '19', '', '16', 'NUNUKAN', '', '', '', '0', '', '', '', '', '0', '', ''),
	('153', '', '', '19', '', '16', 'TANJUNGREDEP', '', '', '', '0', '', '', '', '', '0', '', ''),
	('154', '', '', '20', '', '22', 'AMLAPURA', '', '', '', '0', '', '', '', '', '0', '', ''),
	('155', '', '', '23', '', '19', 'BENTENG', '', '', '', '0', '', '', '', '', '0', '', ''),
	('156', '', '', '25', '', '20', 'KOLAKA', '', '', '', '0', '', '', '', '', '0', '', ''),
	('157', '', '', '25', '', '20', 'RAHA', '', '', '', '0', '', '', '', '', '0', '', ''),
	('158', '', '', '27', '', '17', 'KOTAMOBAGU', '', '', '', '0', '', '', '', '', '0', '', ''),
	('159', '', '', '05', '', '10', 'BANGKO', '', '', '', '0', '', '', '', '', '0', '', ''),
	('160', '', '', '06', '', '11', 'SEKAYU', '', '', '', '0', '', '', '', '', '0', '', ''),
	('161', '', '', '10', '', '29', 'RANGKASBITUNG', '', '', '', '0', '', '', '', '', '0', '', ''),
	('162', '', '', '13', '', '03', 'SRAGEN', '', '', '', '0', '', '', '', '', '0', '', ''),
	('163', '', '', '13', '', '03', 'PURWODADI', '', '', '', '0', '', '', '', '', '0', '', ''),
	('164', '', '', '13', '', '03', 'BANJAR NEGARA', '', '', '', '0', '', '', '', '', '0', '', ''),
	('165', '', '', '15', '', '05', 'SIDOARJO', '', '', '', '0', '', '', '', '', '0', '', ''),
	('166', '', '', '15', '', '05', 'TUBAN', '', '', '', '0', '', '', '', '', '0', '', ''),
	('167', '', '', '16', '', '13', 'SANGGAU', '', '', '', '0', '', '', '', '', '0', '', ''),
	('168', '', '', '18', '', '15', 'PELAIHARI', '', '', '', '0', '', '', '', '', '0', '', ''),
	('169', '', '', '21', '', '23', 'SELONG', '', '', '', '0', '', '', '', '', '0', '', ''),
	('170', '', '', '23', '', '19', 'MAKALE', '', '', '', '0', '', '', '', '', '0', '', ''),
	('171', '', '', '12', '', '02', 'BEKASI', '', '', '', '0', '', '', '', '', '0', '', ''),
	('172', '', '', '22', '', '24', 'ATAMBUA', '', '', '', '0', '', '', '', '', '0', '', ''),
	('173', '', '', '29', '', '21', 'MASOHI', '', '', '', '0', '', '', '', '', '0', '', ''),
	('174', '', '', '22', '', '24', 'LARANTUKA', '', '', '', '0', '', '', '', '', '0', '', ''),
	('175', '', '01', '01', '00', '06', 'JAKARTA VI', '', '', '', '0', '', '', '', '', '0', '', ''),
	('176', 'A', '', '', '', '04', 'WATES', '', '', '', '0', '', '', '', '', '0', '', ''),
	('177', '', '', '', '', '19', 'SINJAI', '', '', '', '0', '', '', '', '', '0', '', ''),
	('178', '', '', '', '', '34', 'MAMUJU', '', '', '', '0', '', '', '', '', '0', '', ''),
	('179', '', '', '', '', '17', 'BITUNG', '', '', '', '0', '', '', '', '', '0', '', ''),
	('180', '', '', '', '', '31', 'MARISA', '', '', '', '0', '', '', '', '', '0', '', ''),
	('181', '', '', '', '', '26', 'MUKOMUKO', '', '', '', '0', '', '', '', '', '0', '', ''),
	('999', '', '', '11', '51', '01', 'DIREKTORATPENGELOLAANKASNEGARA', '', '', '', '0', '', '', '', '', '0', '', '');
/*!40000 ALTER TABLE `t_kppn` ENABLE KEYS */;

-- Dumping structure for table dp0.t_kreditur
DROP TABLE IF EXISTS `t_kreditur`;
CREATE TABLE IF NOT EXISTS `t_kreditur` (
  `kdkreditur` varchar(1) NOT NULL,
  `nmkreditur` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`kdkreditur`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Dumping data for table dp0.t_kreditur: ~4 rows (approximately)
DELETE FROM `t_kreditur`;
/*!40000 ALTER TABLE `t_kreditur` DISABLE KEYS */;
INSERT INTO `t_kreditur` (`kdkreditur`, `nmkreditur`) VALUES
	('1', 'Bank'),
	('2', 'Lembaga Keuangan'),
	('3', 'Pribadi'),
	('4', 'Lainnya');
/*!40000 ALTER TABLE `t_kreditur` ENABLE KEYS */;

-- Dumping structure for table dp0.t_level
DROP TABLE IF EXISTS `t_level`;
CREATE TABLE IF NOT EXISTS `t_level` (
  `kdlevel` varchar(2) NOT NULL,
  `nmlevel` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`kdlevel`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Dumping data for table dp0.t_level: ~4 rows (approximately)
DELETE FROM `t_level`;
/*!40000 ALTER TABLE `t_level` DISABLE KEYS */;
INSERT INTO `t_level` (`kdlevel`, `nmlevel`) VALUES
	('00', 'Administrator'),
	('01', 'Operator'),
	('02', 'Supervisor'),
	('99', 'Super Administrator');
/*!40000 ALTER TABLE `t_level` ENABLE KEYS */;

-- Dumping structure for table dp0.t_lokasi
DROP TABLE IF EXISTS `t_lokasi`;
CREATE TABLE IF NOT EXISTS `t_lokasi` (
  `kdlokasi` char(2) NOT NULL,
  `nmlokasi` varchar(70) DEFAULT NULL,
  PRIMARY KEY (`kdlokasi`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Dumping data for table dp0.t_lokasi: ~43 rows (approximately)
DELETE FROM `t_lokasi`;
/*!40000 ALTER TABLE `t_lokasi` DISABLE KEYS */;
INSERT INTO `t_lokasi` (`kdlokasi`, `nmlokasi`) VALUES
	('01', 'DKI JAKARTA                                                           '),
	('02', 'JAWA BARAT                                                            '),
	('03', 'JAWA TENGAH                                                           '),
	('04', 'DI YOGYAKARTA                                                         '),
	('05', 'JAWA TIMUR                                                            '),
	('06', 'NANGGROE ACEH DARUSSALAM                                              '),
	('07', 'SUMATERA UTARA                                                        '),
	('08', 'SUMATERA BARAT                                                        '),
	('09', 'RIAU                                                                  '),
	('10', 'JAMBI                                                                 '),
	('11', 'SUMATERA SELATAN                                                      '),
	('12', 'LAMPUNG                                                               '),
	('13', 'KALIMANTAN BARAT                                                      '),
	('14', 'KALIMANTAN TENGAH                                                     '),
	('15', 'KALIMANTAN SELATAN                                                    '),
	('16', 'KALIMANTAN TIMUR                                                      '),
	('17', 'SULAWESI UTARA                                                        '),
	('18', 'SULAWESI TENGAH                                                       '),
	('19', 'SULAWESI SELATAN                                                      '),
	('20', 'SULAWESI TENGGARA                                                     '),
	('21', 'MALUKU                                                                '),
	('22', 'BALI                                                                  '),
	('23', 'NUSA TENGGARA BARAT                                                   '),
	('24', 'NUSA TENGGARA TIMUR                                                   '),
	('25', 'PAPUA                                                                 '),
	('26', 'BENGKULU                                                              '),
	('28', 'MALUKU UTARA                                                          '),
	('29', 'BANTEN                                                                '),
	('30', 'BANGKA BELITUNG                                                       '),
	('31', 'GORONTALO                                                             '),
	('32', 'KEPULAUAN RIAU                                                        '),
	('33', 'PAPUA BARAT                                                           '),
	('34', 'SULAWESI BARAT                                                        '),
	('50', 'PERWAKILAN RI DI LUAR NEGERI                                          '),
	('51', 'AMERIKA UTARA                                                         '),
	('52', 'AMERIKA SELATAN                                                       '),
	('53', 'EROPA TIMUR DAN UTARA                                                 '),
	('54', 'EROPA BARAT                                                           '),
	('55', 'AFRIKA                                                                '),
	('56', 'ASIA TENGAH DAN TIMUR                                                 '),
	('57', 'ASIA PASIFIK                                                          '),
	('58', 'ASIA TENGGARA                                                         '),
	('59', 'TIMUR TENGAH                                                          ');
/*!40000 ALTER TABLE `t_lokasi` ENABLE KEYS */;

-- Dumping structure for table dp0.t_menu
DROP TABLE IF EXISTS `t_menu`;
CREATE TABLE IF NOT EXISTS `t_menu` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `nmmenu` varchar(50) NOT NULL,
  `url` varchar(100) NOT NULL,
  `nmfile` varchar(255) DEFAULT NULL,
  `is_parent` varchar(50) NOT NULL,
  `parent_id` int(11) NOT NULL,
  `nourut` int(11) NOT NULL,
  `icon` varchar(50) NOT NULL,
  `kdlevel` varchar(100) NOT NULL,
  `aktif` varchar(1) NOT NULL,
  `new_tab` varchar(1) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT;

-- Dumping data for table dp0.t_menu: ~10 rows (approximately)
DELETE FROM `t_menu`;
/*!40000 ALTER TABLE `t_menu` DISABLE KEYS */;
INSERT INTO `t_menu` (`id`, `nmmenu`, `url`, `nmfile`, `is_parent`, `parent_id`, `nourut`, `icon`, `kdlevel`, `aktif`, `new_tab`) VALUES
	(1, 'Beranda', '', 'home.html', '0', 0, 1, 'fa fa-home', '+00+01+02+', '1', '0'),
	(2, 'Form Kredit', '#', NULL, '1', 0, 2, 'fa fa-edit', '+01+', '1', '0'),
	(3, 'Debitur', '#', NULL, '1', 0, 3, 'fa fa-group', '+00+01+02+', '1', '0'),
	(4, 'Referensi', '#', NULL, '1', 0, 4, 'fa fa-list', '+00+01+02+', '1', '0'),
	(5, 'Generate', 'form/rekam', 'form-rekam.html', '0', 2, 1, '', '+01+02+', '1', '0'),
	(6, 'Data', 'debitur/rekam', 'debitur-rekam.html', '0', 3, 1, '', '+00+01+02+', '1', '0'),
	(7, 'User', 'ref/user', 'ref-user.html', '0', 4, 1, '', '+00+', '1', '0'),
	(8, 'Petugas', 'ref/petugas', 'ref-petugas.html', '0', 4, 2, '', '+00+', '1', '0'),
	(9, 'Kuota', 'ref/kuota', 'ref-kuota.html', '0', 4, 3, '', '+00+', '1', '0'),
	(10, 'Skoring', 'debitur/skoring', 'debitur-skoring.html', '0', 3, 2, '', '+00+', '1', '0');
/*!40000 ALTER TABLE `t_menu` ENABLE KEYS */;

-- Dumping structure for table dp0.t_pekerjaan
DROP TABLE IF EXISTS `t_pekerjaan`;
CREATE TABLE IF NOT EXISTS `t_pekerjaan` (
  `kdpekerjaan` int(11) NOT NULL,
  `nmpekerjaan` varchar(255) NOT NULL,
  PRIMARY KEY (`kdpekerjaan`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Dumping data for table dp0.t_pekerjaan: ~92 rows (approximately)
DELETE FROM `t_pekerjaan`;
/*!40000 ALTER TABLE `t_pekerjaan` DISABLE KEYS */;
INSERT INTO `t_pekerjaan` (`kdpekerjaan`, `nmpekerjaan`) VALUES
	(1, 'BELUM/TIDAK BEKERJA'),
	(2, 'MENGURUS RUMAH TANGGA'),
	(3, 'PELAJAR/MAHASISWA'),
	(4, 'PENSIUNAN'),
	(5, 'PEGAWAI NEGERI SIPIL (PNS)'),
	(6, 'TENTARA NASIONAL INDONESIA (TNI)'),
	(7, 'KEPOLISIAN RI (POLRI)'),
	(8, 'PERDAGANGAN'),
	(9, 'PETANI/PEKEBUN'),
	(10, 'PETERNAK'),
	(11, 'NELAYAN/PERIKANAN'),
	(12, 'INDUSTRI'),
	(13, 'KONSTRUKSI'),
	(14, 'TRANSPORTASI'),
	(15, 'KARYAWAN SWASTA'),
	(16, 'KARYAWAN BUMN'),
	(17, 'KARYAWAN BUMD'),
	(18, 'KARYAWAN HONORER'),
	(19, 'BURUH HARIAN LEPAS'),
	(20, 'BURUH TANI/PERKEBUNAN'),
	(21, 'BURUH NELAYAN/PERIKANAN'),
	(22, 'BURUH PETERNAKAN'),
	(23, 'PEMBANTU RUMAH TANGGA'),
	(24, 'TUKANG CUKUR'),
	(25, 'TUKANG LISTRIK'),
	(26, 'TUKANG BATU'),
	(27, 'TUKANG KAYU'),
	(28, 'TUKANG SOL SEPATU'),
	(29, 'TUKANG LAS/PANDAI BESI'),
	(30, 'TUKANG JAHIT'),
	(31, 'TUKANG GIGI'),
	(32, 'PENATA RIAS'),
	(33, 'PENATA BUSANA'),
	(34, 'PENATA RAMBUT'),
	(35, 'MEKANIK'),
	(36, 'SENIMAN'),
	(37, 'TABIB'),
	(38, 'PARAJI'),
	(39, 'PERANCANG BUSANA'),
	(40, 'PENTERJEMAH'),
	(41, 'IMAM MASJID'),
	(42, 'PENDETA'),
	(43, 'PASTOR'),
	(44, 'WARTAWAN'),
	(45, 'USTADZ/MUBALIGH'),
	(46, 'JURU MASAK'),
	(47, 'PROMOTOR ACARA'),
	(48, 'ANGGOTA DPR RI'),
	(49, 'ANGGOTA DPD'),
	(50, 'ANGGOTA BPK'),
	(51, 'PRESIDEN'),
	(52, 'WAKIL PRESIDEN'),
	(53, 'ANGGOTA MAHKAMAH AGUNG/KONSTITUSI'),
	(54, 'ANGGOTA KABINET KEMENTRIAN'),
	(55, 'DUTA BESAR'),
	(56, 'GUBERNUR'),
	(57, 'WAKIL GUBERNUR'),
	(58, 'BUPATI'),
	(59, 'WAKIL BUPATI'),
	(60, 'WALIKOTA'),
	(61, 'WAKIL WALIKOTA'),
	(62, 'ANGGOTA DPRD PROP.'),
	(63, 'ANGGOTA DPRD KAB.'),
	(64, 'DOSEN'),
	(65, 'GURU'),
	(66, 'PILOT'),
	(67, 'PENGACARA'),
	(68, 'NOTARIS'),
	(69, 'ARSITEK'),
	(70, 'AKUNTAN'),
	(71, 'KONSULTAN'),
	(72, 'DOKTER'),
	(73, 'BIDAN'),
	(74, 'PERAWAT'),
	(75, 'APOTEKER'),
	(76, 'PSIKIATER/PSIKOLOG'),
	(77, 'PENYIAR TELEVISI'),
	(78, 'PENYIAR RADIO'),
	(79, 'PELAUT'),
	(80, 'PENELITI'),
	(81, 'SOPIR'),
	(82, 'PIALANG'),
	(83, 'PARANORMAL'),
	(84, 'PEDAGANG'),
	(85, 'PERANGKAT DESA'),
	(86, 'KEPALA DESA'),
	(87, 'BIARAWAN/BIARAWATI'),
	(88, 'WIRASWASTA'),
	(89, 'PEKERJAAN LAINNYA'),
	(90, 'HAKIM'),
	(91, 'HAKIM AGUNG'),
	(92, 'KETUA DPRD PROV.');
/*!40000 ALTER TABLE `t_pekerjaan` ENABLE KEYS */;

-- Dumping structure for table dp0.t_pendidikan
DROP TABLE IF EXISTS `t_pendidikan`;
CREATE TABLE IF NOT EXISTS `t_pendidikan` (
  `kdpendidikan` int(10) NOT NULL DEFAULT '0',
  `nmpendidikan` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`kdpendidikan`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Dumping data for table dp0.t_pendidikan: ~7 rows (approximately)
DELETE FROM `t_pendidikan`;
/*!40000 ALTER TABLE `t_pendidikan` DISABLE KEYS */;
INSERT INTO `t_pendidikan` (`kdpendidikan`, `nmpendidikan`) VALUES
	(1, 'SD'),
	(2, 'SMP'),
	(3, 'SMA'),
	(4, 'DIPLOMA'),
	(5, 'S1'),
	(6, 'S2'),
	(7, 'S3');
/*!40000 ALTER TABLE `t_pendidikan` ENABLE KEYS */;

-- Dumping structure for table dp0.t_petugas
DROP TABLE IF EXISTS `t_petugas`;
CREATE TABLE IF NOT EXISTS `t_petugas` (
  `kdpetugas` varchar(5) NOT NULL DEFAULT '',
  `nmpetugas` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`kdpetugas`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Dumping data for table dp0.t_petugas: ~15 rows (approximately)
DELETE FROM `t_petugas`;
/*!40000 ALTER TABLE `t_petugas` DISABLE KEYS */;
INSERT INTO `t_petugas` (`kdpetugas`, `nmpetugas`) VALUES
	('00001', 'Petugas Dinas A'),
	('00006', 'Pulau1000'),
	('00010', 'Sudin PRK Jakarta Pusat'),
	('00101', 'Sudin PRKP Jakarta Selatan'),
	('00102', 'Sudin PRKP Jakarta Selatan'),
	('00103', 'Sudin PRKP Jakarta Selatan'),
	('00201', 'Sudin PRKP Jakarta Barat'),
	('00202', 'Sudin PRKP Jakarta Barat'),
	('00222', 'Adnani Taufiq'),
	('00301', 'sudin PRKP Jakarta Timur'),
	('00302', 'Sudin PRKP Jakarta Timur'),
	('00401', 'Sudin PRKP Jakarta Utara '),
	('00402', 'sudin PRKP Jakarta Utara'),
	('00502', 'Sudin PRKP Jakata Pusat'),
	('00701', 'UFPRS DPRKP');
/*!40000 ALTER TABLE `t_petugas` ENABLE KEYS */;

-- Dumping structure for table dp0.t_petugas_kuota
DROP TABLE IF EXISTS `t_petugas_kuota`;
CREATE TABLE IF NOT EXISTS `t_petugas_kuota` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `kdpetugas` varchar(5) NOT NULL,
  `tahun` varchar(4) NOT NULL,
  `kuota` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `Index 2` (`kdpetugas`,`tahun`),
  CONSTRAINT `FK_t_petugas_kuota_t_petugas` FOREIGN KEY (`kdpetugas`) REFERENCES `t_petugas` (`kdpetugas`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=latin1;

-- Dumping data for table dp0.t_petugas_kuota: ~12 rows (approximately)
DELETE FROM `t_petugas_kuota`;
/*!40000 ALTER TABLE `t_petugas_kuota` DISABLE KEYS */;
INSERT INTO `t_petugas_kuota` (`id`, `kdpetugas`, `tahun`, `kuota`) VALUES
	(2, '00001', '2018', 2500),
	(3, '00010', '2018', 100),
	(4, '00006', '2018', 100),
	(5, '00101', '2018', 100),
	(8, '00301', '2018', 100),
	(9, '00401', '2018', 100),
	(10, '00202', '2018', 100),
	(11, '00502', '2018', 100),
	(12, '00102', '2018', 100),
	(13, '00302', '2018', 100),
	(14, '00103', '2018', 100),
	(15, '00222', '2018', 1000);
/*!40000 ALTER TABLE `t_petugas_kuota` ENABLE KEYS */;

-- Dumping structure for table dp0.t_prop
DROP TABLE IF EXISTS `t_prop`;
CREATE TABLE IF NOT EXISTS `t_prop` (
  `kdprop` varchar(2) NOT NULL DEFAULT '0',
  `nmprop` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`kdprop`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Dumping data for table dp0.t_prop: ~1 rows (approximately)
DELETE FROM `t_prop`;
/*!40000 ALTER TABLE `t_prop` DISABLE KEYS */;
INSERT INTO `t_prop` (`kdprop`, `nmprop`) VALUES
	('31', 'DKI JAKARTA');
/*!40000 ALTER TABLE `t_prop` ENABLE KEYS */;

-- Dumping structure for table dp0.t_satker
DROP TABLE IF EXISTS `t_satker`;
CREATE TABLE IF NOT EXISTS `t_satker` (
  `kdsatker` char(6) NOT NULL,
  `nokarwas` char(4) DEFAULT NULL,
  `nmsatker` char(200) DEFAULT NULL,
  `kddept` char(3) DEFAULT NULL,
  `kdunit` char(2) DEFAULT NULL,
  `kdlokasi` char(2) DEFAULT NULL,
  `kdkabkota` char(2) DEFAULT NULL,
  `nomorsp` char(4) DEFAULT NULL,
  `kdkppn` char(3) DEFAULT NULL,
  `kdjnssat` char(1) DEFAULT NULL,
  `kdupdate` char(1) DEFAULT NULL,
  `tgupdate` datetime NOT NULL,
  `nodsupdate` char(100) NOT NULL,
  `tgdsupdate` datetime NOT NULL,
  `jnssekolah` char(2) DEFAULT '',
  `userid` char(9) DEFAULT '',
  `kdruh` char(1) NOT NULL,
  `kdunit_awal` char(3) DEFAULT '',
  `kdpusda` char(1) NOT NULL,
  `kdseksi` char(1) DEFAULT NULL,
  `kddefa` char(1) DEFAULT NULL,
  PRIMARY KEY (`kdsatker`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Dumping data for table dp0.t_satker: ~51 rows (approximately)
DELETE FROM `t_satker`;
/*!40000 ALTER TABLE `t_satker` DISABLE KEYS */;
INSERT INTO `t_satker` (`kdsatker`, `nokarwas`, `nmsatker`, `kddept`, `kdunit`, `kdlokasi`, `kdkabkota`, `nomorsp`, `kdkppn`, `kdjnssat`, `kdupdate`, `tgupdate`, `nodsupdate`, `tgdsupdate`, `jnssekolah`, `userid`, `kdruh`, `kdunit_awal`, `kdpusda`, `kdseksi`, `kddefa`) VALUES
	('010026', '0001', 'DINAS OLAHRAGA DAN PEMUDA PROVINSI DKI JAKARTA', '092', '01', '01', '00', '0017', '088', '4', '', '0000-00-00 00:00:00', '', '0000-00-00 00:00:00', '', '', '', '', '2', '1', ''),
	('010125', '0001', 'DINAS OLAHRAGA DAN PEMUDA KOTAMADYA JAKARTA PUSAT (01)', '092', '01', '01', '51', '0050', '', '4', '', '0000-00-00 00:00:00', '', '0000-00-00 00:00:00', '', '', '', '', '', '1', ''),
	('010225', '0001', 'DINAS OLAHRAGA DAN PEMUDA KOTAMADYA JAKARTA UTARA (01)', '092', '01', '01', '52', '0051', '', '4', '', '0000-00-00 00:00:00', '', '0000-00-00 00:00:00', '', '', '', '', '', '1', ''),
	('010325', '0001', 'DINAS OLAHRAGA DAN PEMUDA KOTAMADYA JAKARTA BARAT (01)', '092', '01', '01', '53', '0052', '', '4', '', '0000-00-00 00:00:00', '', '0000-00-00 00:00:00', '', '', '', '', '', '1', ''),
	('010425', '0001', 'DINAS OLAHRAGA DAN PEMUDA KOTAMADYA JAKARTA TIMUR (01)', '092', '01', '01', '55', '0054', '', '4', '', '0000-00-00 00:00:00', '', '0000-00-00 00:00:00', '', '', '', '', '', '1', ''),
	('010525', '0001', 'DINAS OLAHRAGA DAN PEMUDA KOTAMADYA JAKARTA SELATAN (01)', '092', '01', '01', '54', '0053', '', '4', '', '0000-00-00 00:00:00', '', '0000-00-00 00:00:00', '', '', '', '', '', '1', ''),
	('020066', '0001', 'DINAS OLAHRAGA DAN PEMUDA PROVINSI JAWA BARAT', '092', '01', '02', '00', '0018', '022', '4', '', '0000-00-00 00:00:00', '', '0000-00-00 00:00:00', '', '', '', '', '2', '1', ''),
	('021922', '0001', 'KANTOR PEMUDA DAN OLAHRAGA KABUPATEN INDRAMAYU', '092', '01', '02', '19', '0055', '', '4', '', '0000-00-00 00:00:00', '', '0000-00-00 00:00:00', '', '', '', '', '', '1', ''),
	('030073', '0001', 'DINAS PEMUDA DAN OLAH RAGA PROVINSI JAWA TENGAH', '092', '01', '03', '00', '0019', '026', '4', '', '0000-00-00 00:00:00', '', '0000-00-00 00:00:00', '', '', '', '', '2', '1', ''),
	('040060', '0001', 'DINAS PENDIDIKAN, PEMUDA DAN OLAHRAGA PROVINSI D.I. YOGYAKARTA', '092', '01', '04', '00', '0020', '030', '4', '', '0000-00-00 00:00:00', '', '0000-00-00 00:00:00', '', '', '', '', '2', '1', ''),
	('050005', '0001', 'DINAS PEMUDA DAN OLAH RAGA PROVINSI JAWA TIMUR', '092', '01', '05', '00', '0021', '031', '4', '', '0000-00-00 00:00:00', '', '0000-00-00 00:00:00', '', '', '', '', '2', '1', ''),
	('060053', '0001', 'DINAS PEMUDA DAN OLAHRAGA PROVINSI NANGGROE ACEH DARUSSALAM', '092', '01', '06', '00', '0022', '001', '4', '', '0000-00-00 00:00:00', '', '0000-00-00 00:00:00', '', '', '', '', '2', '1', ''),
	('070051', '0001', 'DINAS PEMUDA DAN OLAH RAGA PROVINSI SUMATERA UTARA', '092', '01', '07', '00', '0023', '004', '4', '', '0000-00-00 00:00:00', '', '0000-00-00 00:00:00', '', '', '', '', '2', '1', ''),
	('070906', '0001', 'DINAS PENDIDIKAN PEMUDA DAN OLAHRAGA KAB. TAPANULI SELATAN', '092', '01', '07', '09', '0056', '', '4', '', '0000-00-00 00:00:00', '', '0000-00-00 00:00:00', '', '', '', '', '', '1', ''),
	('080072', '0001', 'Dinas Pemuda dan Olahraga Provinsi Sumatera Barat', '092', '01', '08', '00', '0024', '010', '4', '', '0000-00-00 00:00:00', '', '0000-00-00 00:00:00', '', '', '', '', '2', '1', ''),
	('090018', '0001', 'DINAS PEMUDA DAN OLAH RAGA PROVINSI RIAU', '092', '01', '09', '00', '0025', '008', '4', '', '0000-00-00 00:00:00', '', '0000-00-00 00:00:00', '', '', '', '', '2', '1', ''),
	('090223', '0001', 'KANTOR PEMUDA DAN OLAHRAGA  KABUPATEN BENGKALIS', '092', '01', '09', '02', '0057', '', '4', '', '0000-00-00 00:00:00', '', '0000-00-00 00:00:00', '', '', '', '', '', '1', ''),
	('090412', '0001', 'DINAS PEMUDA DAN OR, BUDAYA DAN PARIWISATA KABUPATEN INDRAGIRI HULU', '092', '01', '09', '04', '0058', '', '4', '', '0000-00-00 00:00:00', '', '0000-00-00 00:00:00', '', '', '', '', '', '1', ''),
	('100066', '0001', 'DINAS PEMUDA DAN OLAHRAGA PROVINSI JAMBI', '092', '01', '10', '00', '0026', '012', '4', '', '0000-00-00 00:00:00', '', '0000-00-00 00:00:00', '', '', '', '', '2', '1', ''),
	('110031', '0001', 'DINAS PEMUDA DAN OLAH RAGA PROVINSI SUMATERA SELATAN', '092', '01', '11', '00', '0027', '014', '4', '', '0000-00-00 00:00:00', '', '0000-00-00 00:00:00', '', '', '', '', '2', '1', ''),
	('120067', '0001', 'DINAS PEMUDA DAN OLAH RAGA  PROVINSI LAMPUNG', '092', '01', '12', '00', '0028', '017', '4', '', '0000-00-00 00:00:00', '', '0000-00-00 00:00:00', '', '', '', '', '2', '1', ''),
	('130060', '0001', 'DINAS PEMUDA DAN OLAHRAGA PROVINSI KALIMANTAN BARAT', '092', '01', '13', '00', '0029', '042', '4', '', '0000-00-00 00:00:00', '', '0000-00-00 00:00:00', '', '', '', '', '2', '1', ''),
	('137608', NULL, 'Balitbang Kemendikbud', '023', '11', NULL, NULL, NULL, NULL, NULL, NULL, '0000-00-00 00:00:00', '', '0000-00-00 00:00:00', '', '', '', '', '', NULL, NULL),
	('140071', '0001', 'DINAS PEMUDA DAN OLAH RAGA PROVINSI KALIMANTAN TENGAH', '092', '01', '14', '00', '0030', '043', '4', '', '0000-00-00 00:00:00', '', '0000-00-00 00:00:00', '', '', '', '', '2', '1', ''),
	('150071', '0001', 'DINAS PEMUDA, OLAHRAGA, KEBUDAYAAN DAN PARIWISATA PROVINSI KALIMANTAN SELATAN', '092', '01', '15', '00', '0031', '045', '4', '', '0000-00-00 00:00:00', '', '0000-00-00 00:00:00', '', '', '', '', '2', '1', ''),
	('160063', '0001', 'DINAS PEMUDA DAN OLAHRAGA PROVINSI KALIMANTAN TIMUR', '092', '01', '16', '00', '0032', '046', '4', '', '0000-00-00 00:00:00', '', '0000-00-00 00:00:00', '', '', '', '', '2', '1', ''),
	('165127', '0001', 'KANTOR PEMUDA & OLAH RAGA KOTA SAMARINDA', '092', '01', '16', '51', '0059', '', '4', '', '0000-00-00 00:00:00', '', '0000-00-00 00:00:00', '', '', '', '', '', '1', ''),
	('170076', '0001', 'DINAS PEMUDA DAN OLAHRAGA PROPINSI SULAWESI UTARA', '092', '01', '17', '00', '0033', '049', '4', '', '0000-00-00 00:00:00', '', '0000-00-00 00:00:00', '', '', '', '', '2', '1', ''),
	('180062', '0001', 'DINAS PEMUDA DAN OLAH RAGA PROVINSI SULAWESI TENGAH', '092', '01', '18', '00', '0034', '051', '4', '', '0000-00-00 00:00:00', '', '0000-00-00 00:00:00', '', '', '', '', '2', '1', ''),
	('190051', '0001', 'DINAS PEMUDA DAN OLAHRAGA PROVINSI SULAWESI SELATAN', '092', '01', '19', '00', '0035', '054', '4', '', '0000-00-00 00:00:00', '', '0000-00-00 00:00:00', '', '', '', '', '2', '1', ''),
	('200018', '0001', 'DINAS PEMUDA DAN OLAHRAGA PROVINSI SULAWESI TENGGARA', '092', '01', '20', '00', '0036', '060', '4', '', '0000-00-00 00:00:00', '', '0000-00-00 00:00:00', '', '', '', '', '2', '1', ''),
	('210066', '0001', 'DINAS PENDIDIKAN, PEMUDA DAN OLAHRAGA PROVINSI MALUKU', '092', '01', '21', '00', '0037', '061', '4', '', '0000-00-00 00:00:00', '', '0000-00-00 00:00:00', '', '', '', '', '2', '1', ''),
	('210119', '0001', 'DINAS PEMUDA DAN OLAH RAGA KABUPATEN MALUKU TENGAH', '092', '01', '21', '01', '0060', '', '4', '', '0000-00-00 00:00:00', '', '0000-00-00 00:00:00', '', '', '', '', '', '1', ''),
	('220062', '0001', 'DINAS PENDIDIKAN, PEMUDA DAN OLAHRAGA PROVINSI  BALI', '092', '01', '22', '00', '0038', '037', '4', '', '0000-00-00 00:00:00', '', '0000-00-00 00:00:00', '', '', '', '', '2', '1', ''),
	('230071', '0001', 'DINAS PENDIDIKAN, PEMUDA DAN OLAH RAGA  PROVINSI NUSA TENGGARA BARAT', '092', '01', '23', '00', '0039', '038', '4', '', '0000-00-00 00:00:00', '', '0000-00-00 00:00:00', '', '', '', '', '2', '1', ''),
	('240018', '0001', 'DINAS PENDIDIKAN, PEMUDA DAN OLAHRAGA PROV. NUSA TENGGARA TIMUR', '092', '01', '24', '00', '0040', '039', '4', '', '0000-00-00 00:00:00', '', '0000-00-00 00:00:00', '', '', '', '', '2', '1', ''),
	('250015', '0001', 'DINAS PENDIDIKAN, PEMUDA DAN OLAHRAGA PROVINSI PAPUA', '092', '01', '25', '00', '0041', '063', '4', '', '0000-00-00 00:00:00', '', '0000-00-00 00:00:00', '', '', '', '', '2', '1', ''),
	('250734', '0001', 'KANTOR PEMUDA DAN OLAH RAGA KAB. MERAUKE', '092', '01', '25', '07', '0061', '', '4', '', '0000-00-00 00:00:00', '', '0000-00-00 00:00:00', '', '', '', '', '', '1', ''),
	('260017', '0001', 'DINAS PEMUDA DAN OLAH RAGA PROVINSI BENGKULU', '092', '01', '26', '00', '0042', '016', '4', '', '0000-00-00 00:00:00', '', '0000-00-00 00:00:00', '', '', '', '', '2', '1', ''),
	('280057', '0001', 'DINAS PEMUDA DAN OLAHRAGA  PROVINSI  MALUKU UTARA', '092', '01', '28', '00', '0043', '062', '4', '', '0000-00-00 00:00:00', '', '0000-00-00 00:00:00', '', '', '', '', '2', '1', ''),
	('290062', '0001', 'DINAS PEMUDA DAN OLAH RAGA  PROVINSI BANTEN', '092', '01', '29', '00', '0044', '020', '4', '', '0000-00-00 00:00:00', '', '0000-00-00 00:00:00', '', '', '', '', '2', '1', ''),
	('300051', '0001', 'DINAS PEMUDA DAN OLAH RAGA PROVINSI KEPULAUAN BANGKA BELITUNG', '092', '01', '30', '00', '0045', '015', '4', '', '0000-00-00 00:00:00', '', '0000-00-00 00:00:00', '', '', '', '', '2', '1', ''),
	('310052', '0001', 'DINAS PENDIDIKAN, PEMUDA DAN OLAHRAGA PROVINSI  GORONTALO', '092', '01', '31', '00', '0046', '050', '4', '', '0000-00-00 00:00:00', '', '0000-00-00 00:00:00', '', '', '', '', '2', '1', ''),
	('320030', '0001', 'DINAS PEMUDA DAN OLAH RAGA PROVINSI KEPULAUAN RIAU', '092', '01', '32', '00', '0047', '009', '4', '', '0000-00-00 00:00:00', '', '0000-00-00 00:00:00', '', '', '', '', '2', '1', ''),
	('330040', '0001', 'DINAS PEMUDA DAN OLAHRAGA PROVINSI PAPUA BARAT', '092', '01', '33', '00', '0048', '065', '4', '', '0000-00-00 00:00:00', '', '0000-00-00 00:00:00', '', '', '', '', '2', '1', ''),
	('340031', '0001', 'DINAS PEMUDA, OLAHRAGA DAN PARIWISATA PROVINSI SULAWESI BARAT', '092', '01', '34', '00', '0049', '178', '4', '', '0000-00-00 00:00:00', '', '0000-00-00 00:00:00', '', '', '', '', '2', '1', ''),
	('632505', '0001', 'LEMBAGA KETAHANAN NASIONAL REPUBLIK INDONESIA', '064', '01', '01', '00', NULL, '175', '4', NULL, '0000-00-00 00:00:00', '', '0000-00-00 00:00:00', '', '', '', '', '', NULL, NULL),
	('664319', '0001', 'KEMENTERIAN NEGARA PEMUDA DAN OLAH RAGA', '092', '01', '01', '51', '0015', '088', '1', '', '0000-00-00 00:00:00', '', '0000-00-00 00:00:00', '', '', '', '', '1', '1', ''),
	('666206', '0001', 'KOMITE OLAHRAGA NASIONAL INDONESIA', '092', '01', '01', '51', '0016', '088', '1', 'x', '0000-00-00 00:00:00', '', '0000-00-00 00:00:00', '', '', '', '', '1', '1', ''),
	('673591', '0001', 'DINAS OLAHRAGA DAN PEMUDA PROVINSI JAWA BARAT', '092', '01', '02', '00', '0062', '', '4', '', '0000-00-00 00:00:00', '', '0000-00-00 00:00:00', '', '', '', '', '', '1', ''),
	('673606', '0001', 'DINAS PEMUDA, OLAHRAGA, KEBUDAYAAN DAN PARIWISATA PROVINSI KALIMANTAN SELATAN', '092', '01', '15', '00', '0063', '', '4', '', '0000-00-00 00:00:00', '', '0000-00-00 00:00:00', '', '', '', '', '', '1', '');
/*!40000 ALTER TABLE `t_satker` ENABLE KEYS */;

-- Dumping structure for table dp0.t_skoring
DROP TABLE IF EXISTS `t_skoring`;
CREATE TABLE IF NOT EXISTS `t_skoring` (
  `kdskoring` varchar(1) NOT NULL,
  `nmskoring` varchar(255) DEFAULT NULL,
  `max` int(11) DEFAULT NULL,
  `bobot` int(10) DEFAULT NULL,
  PRIMARY KEY (`kdskoring`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Dumping data for table dp0.t_skoring: ~4 rows (approximately)
DELETE FROM `t_skoring`;
/*!40000 ALTER TABLE `t_skoring` DISABLE KEYS */;
INSERT INTO `t_skoring` (`kdskoring`, `nmskoring`, `max`, `bobot`) VALUES
	('1', 'Tanggungan', 5, 40),
	('2', 'Lama tinggal', 5, 40),
	('3', 'Lokasi', 5, 20),
	('4', 'KJP', 0, 0);
/*!40000 ALTER TABLE `t_skoring` ENABLE KEYS */;

-- Dumping structure for table dp0.t_skoring_lama_tinggal
DROP TABLE IF EXISTS `t_skoring_lama_tinggal`;
CREATE TABLE IF NOT EXISTS `t_skoring_lama_tinggal` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `kdskoring` varchar(1) DEFAULT NULL,
  `tahun1` int(11) DEFAULT NULL,
  `tahun2` int(11) DEFAULT NULL,
  `nilai` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_t_skoring_lama_tinggal_t_skoring` (`kdskoring`),
  CONSTRAINT `FK_t_skoring_lama_tinggal_t_skoring` FOREIGN KEY (`kdskoring`) REFERENCES `t_skoring` (`kdskoring`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;

-- Dumping data for table dp0.t_skoring_lama_tinggal: ~3 rows (approximately)
DELETE FROM `t_skoring_lama_tinggal`;
/*!40000 ALTER TABLE `t_skoring_lama_tinggal` DISABLE KEYS */;
INSERT INTO `t_skoring_lama_tinggal` (`id`, `kdskoring`, `tahun1`, `tahun2`, `nilai`) VALUES
	(1, '2', 0, 4, 0),
	(2, '2', 5, 10, 3),
	(3, '2', 11, 100, 5);
/*!40000 ALTER TABLE `t_skoring_lama_tinggal` ENABLE KEYS */;

-- Dumping structure for table dp0.t_skoring_lokasi
DROP TABLE IF EXISTS `t_skoring_lokasi`;
CREATE TABLE IF NOT EXISTS `t_skoring_lokasi` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `kdskoring` varchar(1) DEFAULT NULL,
  `lokasi` varchar(255) DEFAULT NULL,
  `nilai` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_t_skoring_lokasi_t_skoring` (`kdskoring`),
  CONSTRAINT `FK_t_skoring_lokasi_t_skoring` FOREIGN KEY (`kdskoring`) REFERENCES `t_skoring` (`kdskoring`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;

-- Dumping data for table dp0.t_skoring_lokasi: ~3 rows (approximately)
DELETE FROM `t_skoring_lokasi`;
/*!40000 ALTER TABLE `t_skoring_lokasi` DISABLE KEYS */;
INSERT INTO `t_skoring_lokasi` (`id`, `kdskoring`, `lokasi`, `nilai`) VALUES
	(1, '3', 'kdkabkota', 2),
	(2, '3', 'kdkec', 3),
	(3, '3', 'kdkel', 5);
/*!40000 ALTER TABLE `t_skoring_lokasi` ENABLE KEYS */;

-- Dumping structure for table dp0.t_skoring_tanggungan
DROP TABLE IF EXISTS `t_skoring_tanggungan`;
CREATE TABLE IF NOT EXISTS `t_skoring_tanggungan` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `kdskoring` varchar(1) DEFAULT NULL,
  `jmltanggung` int(11) DEFAULT NULL,
  `nilai` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_t_skoring_tanggungan_t_skoring` (`kdskoring`),
  CONSTRAINT `FK_t_skoring_tanggungan_t_skoring` FOREIGN KEY (`kdskoring`) REFERENCES `t_skoring` (`kdskoring`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;

-- Dumping data for table dp0.t_skoring_tanggungan: ~6 rows (approximately)
DELETE FROM `t_skoring_tanggungan`;
/*!40000 ALTER TABLE `t_skoring_tanggungan` DISABLE KEYS */;
INSERT INTO `t_skoring_tanggungan` (`id`, `kdskoring`, `jmltanggung`, `nilai`) VALUES
	(1, '1', 1, 1),
	(2, '1', 2, 2),
	(3, '1', 3, 3),
	(4, '1', 4, 4),
	(5, '1', 5, 5),
	(6, '1', 0, 0);
/*!40000 ALTER TABLE `t_skoring_tanggungan` ENABLE KEYS */;

-- Dumping structure for table dp0.t_status_debitur
DROP TABLE IF EXISTS `t_status_debitur`;
CREATE TABLE IF NOT EXISTS `t_status_debitur` (
  `status` int(10) NOT NULL DEFAULT '0',
  `nmstatus` varchar(255) DEFAULT NULL,
  `skoring` varchar(1) DEFAULT NULL,
  PRIMARY KEY (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT;

-- Dumping data for table dp0.t_status_debitur: ~5 rows (approximately)
DELETE FROM `t_status_debitur`;
/*!40000 ALTER TABLE `t_status_debitur` DISABLE KEYS */;
INSERT INTO `t_status_debitur` (`status`, `nmstatus`, `skoring`) VALUES
	(0, 'Pengajuan online', '0'),
	(1, 'Pengajuan petugas', '0'),
	(2, 'Pengajuan valid', '1'),
	(3, 'Pengajuan tidak valid', '0'),
	(4, 'Skoring debitur', '1');
/*!40000 ALTER TABLE `t_status_debitur` ENABLE KEYS */;

-- Dumping structure for table dp0.t_status_form
DROP TABLE IF EXISTS `t_status_form`;
CREATE TABLE IF NOT EXISTS `t_status_form` (
  `status` int(10) NOT NULL DEFAULT '0',
  `nmstatus` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Dumping data for table dp0.t_status_form: ~5 rows (approximately)
DELETE FROM `t_status_form`;
/*!40000 ALTER TABLE `t_status_form` DISABLE KEYS */;
INSERT INTO `t_status_form` (`status`, `nmstatus`) VALUES
	(0, 'Generate form permohonan'),
	(1, 'Data pemohon direkam'),
	(2, 'Proses verifikasi'),
	(3, 'Data disetujui'),
	(4, 'Data ditolak');
/*!40000 ALTER TABLE `t_status_form` ENABLE KEYS */;

-- Dumping structure for table dp0.t_status_skoring
DROP TABLE IF EXISTS `t_status_skoring`;
CREATE TABLE IF NOT EXISTS `t_status_skoring` (
  `status` int(10) DEFAULT NULL,
  `range1` int(10) DEFAULT NULL,
  `range2` int(10) DEFAULT NULL,
  `nmstatus` varchar(255) DEFAULT NULL,
  `warna` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Dumping data for table dp0.t_status_skoring: ~3 rows (approximately)
DELETE FROM `t_status_skoring`;
/*!40000 ALTER TABLE `t_status_skoring` DISABLE KEYS */;
INSERT INTO `t_status_skoring` (`status`, `range1`, `range2`, `nmstatus`, `warna`) VALUES
	(1, 0, 60, 'Permohonan Ditolak', '#ee1111'),
	(2, 61, 80, 'Permohonan Dipertimbangkan', '#2d89ef'),
	(3, 81, 100, 'Permohonan Diterima', '#00a300');
/*!40000 ALTER TABLE `t_status_skoring` ENABLE KEYS */;

-- Dumping structure for table dp0.t_tipe
DROP TABLE IF EXISTS `t_tipe`;
CREATE TABLE IF NOT EXISTS `t_tipe` (
  `kdtipe` varchar(3) NOT NULL,
  `nmtipe` varchar(255) DEFAULT NULL,
  `ukuran` decimal(14,2) DEFAULT NULL,
  PRIMARY KEY (`kdtipe`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Dumping data for table dp0.t_tipe: ~6 rows (approximately)
DELETE FROM `t_tipe`;
/*!40000 ALTER TABLE `t_tipe` DISABLE KEYS */;
INSERT INTO `t_tipe` (`kdtipe`, `nmtipe`, `ukuran`) VALUES
	('1BA', '1BA', 23.95),
	('1BC', '1BC', 24.25),
	('2BA', '2BA', 34.65),
	('2BC', '2BC', 35.30),
	('ST', 'ST', 21.00),
	('STC', 'STC', 22.25);
/*!40000 ALTER TABLE `t_tipe` ENABLE KEYS */;

-- Dumping structure for table dp0.t_tipe_kredit
DROP TABLE IF EXISTS `t_tipe_kredit`;
CREATE TABLE IF NOT EXISTS `t_tipe_kredit` (
  `kdtipe` varchar(1) NOT NULL DEFAULT '',
  `nmtipe` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`kdtipe`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Dumping data for table dp0.t_tipe_kredit: ~2 rows (approximately)
DELETE FROM `t_tipe_kredit`;
/*!40000 ALTER TABLE `t_tipe_kredit` DISABLE KEYS */;
INSERT INTO `t_tipe_kredit` (`kdtipe`, `nmtipe`) VALUES
	('1', 'Individu'),
	('2', 'Join Income');
/*!40000 ALTER TABLE `t_tipe_kredit` ENABLE KEYS */;

-- Dumping structure for table dp0.t_user
DROP TABLE IF EXISTS `t_user`;
CREATE TABLE IF NOT EXISTS `t_user` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nama` varchar(255) DEFAULT NULL,
  `nik` varchar(16) NOT NULL,
  `alamat` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `telp` varchar(255) DEFAULT NULL,
  `foto` varchar(255) DEFAULT 'no-image.png',
  `aktif` varchar(2) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `Index 2` (`username`),
  UNIQUE KEY `Index 4` (`nik`),
  UNIQUE KEY `Index 3` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=latin1;

-- Dumping data for table dp0.t_user: ~17 rows (approximately)
DELETE FROM `t_user`;
/*!40000 ALTER TABLE `t_user` DISABLE KEYS */;
INSERT INTO `t_user` (`id`, `username`, `password`, `nama`, `nik`, `alamat`, `email`, `telp`, `foto`, `aktif`, `created_at`, `updated_at`) VALUES
	(1, 'petugas1', 'f65f45555d09de1aabb780c6ad458323', 'Eko', '1986070120060210', '-', 'testlagi@gmail.com', '-', 'no-image.png', '1', '2018-08-01 23:43:24', '2018-08-01 23:43:25'),
	(3, 'supervisor', '741406c6940752b8ccf1834696338373', 'Tomy Suhartanto', '0000000000000001', 'Jakarta', 'test1@jakarta.go.id', '123', 'no-image.png', '1', '2018-08-05 09:42:40', NULL),
	(4, 'admin', '741406c6940752b8ccf1834696338373', 'Nama Default', '0000000000000000', 'Jakarta', 'jakarta@gmail.com', '-', 'no-image.png', '1', '2018-08-05 11:52:54', NULL),
	(11, '742018', 'f65f45555d09de1aabb780c6ad458323', 'Idah Jubaidah', '3275054105650017', 'Jakarta', 'perumahanjp@gmail.com', '08176958500', 'no-image.png', '1', NULL, NULL),
	(12, 'budihandoyo', 'f65f45555d09de1aabb780c6ad458323', 'budihandoyo', '3175072608760009', 'Jakarta', 'budihandoyo260876@gmail.com', '08561368881', 'no-image.png', '1', NULL, NULL),
	(13, 'kep.seribu', 'f65f45555d09de1aabb780c6ad458323', 'Indra Sukma', '3172023105790014', 'Jln.Warakas V Gg.IV Rt.013/007 Jakarta Utara', 'rumahgedungkepseribu@gmail.com', '087780033489', 'no-image.png', '1', NULL, NULL),
	(14, 'sudinbarat2', 'f65f45555d09de1aabb780c6ad458323', 'sudinbarat2', '3674044210970006', 'Jakarta', 'perumahan.barat@gmail.com', '0895344020247', 'no-image.png', '1', NULL, NULL),
	(15, 'sdprkpjt', 'f65f45555d09de1aabb780c6ad458323', 'Yora Mandala', '3174062303920005', 'Jakarta', 'sdprkp.jaktim@gmmail.com', '081212795000', 'no-image.png', '1', NULL, NULL),
	(16, 'sdprkpju', '79bdf410c678a0dd506dc429c0667471', 'Yanuar Arifin', '3174100702920001', 'Jakarta', 'tusdpgpju@yahoo.co.id', '087877464622', 'no-image.png', '1', NULL, NULL),
	(17, 'galih123', 'f65f45555d09de1aabb780c6ad458323', 'Galih Triastuti', '3175034306810006', 'Jakarta', 'triastutiprayogo@gmail.com', '08558402590', 'no-image.png', '1', NULL, NULL),
	(18, 'sdprkpjt2', 'f65f45555d09de1aabb780c6ad458323', 'Chaeria Putri', '3175025502940005', 'Jakarta', 'sdprkp.jaktim@gmail.com', '087777880508', 'no-image.png', '1', NULL, NULL),
	(19, 'sdprkpju2', 'f65f45555d09de1aabb780c6ad458323', 'Afri Febriansyah', '3671062301820002', 'Jakarta', 'tusdpgpju@yahoo.com', '081291888592', 'no-image.png', '1', NULL, NULL),
	(20, 'Muhamad Ramdani', 'f65f45555d09de1aabb780c6ad458323', 'Muhamad Ramdani', '3276012212760006', 'Depok', 'danimtars@yahoo.com', '081319994929', 'no-image.png', '1', NULL, NULL),
	(21, 'suharyadi', 'f65f45555d09de1aabb780c6ad458323', 'Suharyadi Eko Nugroho', '3276021409870009', 'Jakarta', 'suharyadi.eko14@gmail.com', '081210950725', 'no-image.png', '1', NULL, NULL),
	(22, 'sudinbarat1', 'f65f45555d09de1aabb780c6ad458323', 'Annisa Ayu Suryani', '3173025201830008', 'Jakarta', 'annisasoerdiono@yahoo.com', '08568721583', 'no-image.png', '1', NULL, NULL),
	(23, 'adnani', 'f65f45555d09de1aabb780c6ad458323', 'Adnani Taufiq', '3175062412840005', 'jakarta', 'adnanitaufiq@gmail.com', '081', 'no-image.png', '1', NULL, NULL),
	(24, 'ufprsdprkp1', 'f65f45555d09de1aabb780c6ad458323', 'Muhammad Ali', '3671011110730003', 'Jakarta', 'uptfprs.dprkp@gmail.com', '081210122211', 'no-image.png', '1', NULL, NULL);
/*!40000 ALTER TABLE `t_user` ENABLE KEYS */;

-- Dumping structure for table dp0.t_user_level
DROP TABLE IF EXISTS `t_user_level`;
CREATE TABLE IF NOT EXISTS `t_user_level` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `id_user` int(10) NOT NULL,
  `kdlevel` varchar(2) NOT NULL,
  `aktif` varchar(1) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `Index 2` (`id_user`,`kdlevel`),
  KEY `FK_t_user_level_t_level` (`kdlevel`),
  CONSTRAINT `FK_t_user_level_t_level` FOREIGN KEY (`kdlevel`) REFERENCES `t_level` (`kdlevel`),
  CONSTRAINT `FK_t_user_level_t_user` FOREIGN KEY (`id_user`) REFERENCES `t_user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=35 DEFAULT CHARSET=latin1;

-- Dumping data for table dp0.t_user_level: ~19 rows (approximately)
DELETE FROM `t_user_level`;
/*!40000 ALTER TABLE `t_user_level` DISABLE KEYS */;
INSERT INTO `t_user_level` (`id`, `id_user`, `kdlevel`, `aktif`) VALUES
	(1, 1, '01', '1'),
	(2, 1, '00', '0'),
	(4, 4, '00', '1'),
	(10, 11, '01', '1'),
	(11, 12, '01', '1'),
	(12, 13, '01', '1'),
	(16, 14, '01', '1'),
	(18, 15, '01', '1'),
	(20, 16, '01', '1'),
	(24, 20, '01', '1'),
	(26, 17, '01', '1'),
	(27, 18, '01', '1'),
	(28, 19, '01', '1'),
	(29, 21, '01', '1'),
	(30, 22, '01', '1'),
	(31, 3, '00', '1'),
	(32, 3, '02', '0'),
	(33, 23, '01', '1'),
	(34, 24, '01', '1');
/*!40000 ALTER TABLE `t_user_level` ENABLE KEYS */;

-- Dumping structure for table dp0.t_user_petugas
DROP TABLE IF EXISTS `t_user_petugas`;
CREATE TABLE IF NOT EXISTS `t_user_petugas` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `id_user` int(10) NOT NULL,
  `kdpetugas` varchar(5) NOT NULL,
  `aktif` varchar(1) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id_user` (`id_user`,`kdpetugas`),
  KEY `kdpetugas` (`kdpetugas`),
  CONSTRAINT `t_user_petugas_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `t_user` (`id`),
  CONSTRAINT `t_user_petugas_ibfk_2` FOREIGN KEY (`kdpetugas`) REFERENCES `t_petugas` (`kdpetugas`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=latin1;

-- Dumping data for table dp0.t_user_petugas: ~15 rows (approximately)
DELETE FROM `t_user_petugas`;
/*!40000 ALTER TABLE `t_user_petugas` DISABLE KEYS */;
INSERT INTO `t_user_petugas` (`id`, `id_user`, `kdpetugas`, `aktif`) VALUES
	(6, 1, '00001', '1'),
	(8, 11, '00010', '1'),
	(9, 12, '00101', '1'),
	(10, 13, '00006', '1'),
	(11, 14, '00202', '1'),
	(12, 15, '00301', '1'),
	(13, 16, '00401', '1'),
	(14, 17, '00102', '1'),
	(15, 18, '00302', '1'),
	(16, 19, '00402', '1'),
	(18, 21, '00502', '1'),
	(19, 20, '00103', '1'),
	(20, 22, '00201', '1'),
	(21, 23, '00222', '1'),
	(22, 24, '00701', '1');
/*!40000 ALTER TABLE `t_user_petugas` ENABLE KEYS */;

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
