-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Versi server:                 5.6.14 - MySQL Community Server (GPL)
-- OS Server:                    Win32
-- HeidiSQL Versi:               8.0.0.4396
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

-- Dumping database structure for db_kredit
DROP DATABASE IF EXISTS `db_kredit`;
CREATE DATABASE IF NOT EXISTS `db_kredit` /*!40100 DEFAULT CHARACTER SET latin1 */;
USE `db_kredit`;


-- Dumping structure for table db_kredit.d_debitur
DROP TABLE IF EXISTS `d_debitur`;
CREATE TABLE IF NOT EXISTS `d_debitur` (
  `nik` varchar(16) NOT NULL,
  `nokk` varchar(16) NOT NULL,
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
  `id_hunian` int(11) NOT NULL,
  `status` int(11) NOT NULL DEFAULT '0',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
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
  CONSTRAINT `FK_d_debitur_t_agama` FOREIGN KEY (`kdagama`) REFERENCES `t_agama` (`kdagama`),
  CONSTRAINT `FK_d_debitur_t_bpjs` FOREIGN KEY (`kdbpjs`) REFERENCES `t_bpjs` (`kdbpjs`),
  CONSTRAINT `FK_d_debitur_t_jenkredit` FOREIGN KEY (`kdjenkredit`) REFERENCES `t_jenkredit` (`kdjenkredit`),
  CONSTRAINT `FK_d_debitur_t_kawin` FOREIGN KEY (`kdkawin`) REFERENCES `t_kawin` (`kdkawin`),
  CONSTRAINT `FK_d_debitur_t_kelamin` FOREIGN KEY (`kdkelamin`) REFERENCES `t_kelamin` (`kdkelamin`),
  CONSTRAINT `FK_d_debitur_t_pekerjaan` FOREIGN KEY (`kdpekerjaan`) REFERENCES `t_pekerjaan` (`kdpekerjaan`),
  CONSTRAINT `FK_d_debitur_t_pendidikan` FOREIGN KEY (`kdpendidikan`) REFERENCES `t_pendidikan` (`kdpendidikan`),
  CONSTRAINT `FK_d_debitur_t_status_debitur` FOREIGN KEY (`status`) REFERENCES `t_status_debitur` (`status`),
  CONSTRAINT `FK_d_debitur_t_tipe_kredit` FOREIGN KEY (`kdtipe`) REFERENCES `t_tipe_kredit` (`kdtipe`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Dumping data for table db_kredit.d_debitur: ~5 rows (approximately)
DELETE FROM `d_debitur`;
/*!40000 ALTER TABLE `d_debitur` DISABLE KEYS */;
INSERT INTO `d_debitur` (`nik`, `nokk`, `npwp`, `nama`, `tglhr`, `kotalhr`, `kdkelamin`, `nmibu`, `kdagama`, `kdpendidikan`, `kdpekerjaan`, `kdkawin`, `kdbpjs`, `kdjenkredit`, `kdtipe`, `email`, `nohp`, `jmltanggung`, `jmlkjp`, `jmlbpjs`, `jmltinggal`, `jmlroda2`, `jmlroda4`, `jmlrmh`, `pengeluaran`, `tgpemohon`, `id_hunian`, `status`, `created_at`, `updated_at`) VALUES
	('1111111111111111', '2222222222222222', '111111111111111', 'test', '0000-00-00', 'test', 1, 'test', 1, 5, 5, 2, 1, '1', '2', 'ekox69@gmail.com', '12345', 1, 1, 1, 20, 1, 1, 1, 7500000, '2018-08-01', 5, 4, '2018-08-04 16:31:55', '2018-08-04 16:31:55'),
	('1111111111111113', '3333333333333333', '222222222222444', 'test', '0000-00-00', 'test', 1, 'test', 1, 5, 5, 2, 1, '1', '2', 'test@gmail.com', '123', 7, 1, 1, 5, 1, 1, 1, 3500000, '2018-08-01', 5, 4, '2018-08-04 21:57:29', '2018-08-04 21:57:29'),
	('12312143566788', '12334556677885', '123445667', 'tes', '0000-00-00', 'jakarta', 1, 'tesa', 1, 5, 15, 2, 1, '1', '1', 'ttes@gmail.com', '1234567888', 1, 3, 2, 5, 1, 0, 0, 0, '2018-08-04', 5, 3, '2018-08-05 00:09:23', '2018-08-05 00:09:23'),
	('3222222222222222', '1111111111111133', '111111111111111', 'ets', '1986-06-30', 'test', 1, 'test', 1, 4, 5, 3, 2, '1', '1', 'test@gmail.com', '123454', 0, 0, 2, 6, 0, 0, 0, 5000000, '2018-08-01', 5, 2, '2018-08-06 00:08:00', '2018-08-06 00:08:00'),
	('3312056677660001', '3312056545340001', '124886876557000', 'Eko Saprol', '1986-07-01', 'Sukabumi', 1, 'Sutarmi', 1, 5, 5, 1, 1, '1', '1', 'eko@gmail.com', '0', 1, 0, 1, 5, 0, 0, 0, 1000000, '2018-08-09', 5, 0, '2018-08-09 03:39:03', '2018-08-09 03:39:03');
/*!40000 ALTER TABLE `d_debitur` ENABLE KEYS */;


-- Dumping structure for table db_kredit.d_debitur_alamat
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
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=latin1;

-- Dumping data for table db_kredit.d_debitur_alamat: ~10 rows (approximately)
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
	(12, '3312056677660001', '2', '31', '73', '05', '1003', '12343', '02102232', 'Lapangan Banteng', '2018-08-09 03:39:03', '2018-08-09 03:39:03');
/*!40000 ALTER TABLE `d_debitur_alamat` ENABLE KEYS */;


-- Dumping structure for table db_kredit.d_debitur_dok
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
) ENGINE=InnoDB AUTO_INCREMENT=63 DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT;

-- Dumping data for table db_kredit.d_debitur_dok: ~40 rows (approximately)
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
	(62, '3312056677660001', 13, '13_5ce496a05dbc587fffa639277496ff51.pdf', '2018-08-09 03:37:20', '2018-08-09 03:37:20');
/*!40000 ALTER TABLE `d_debitur_dok` ENABLE KEYS */;


-- Dumping structure for table db_kredit.d_debitur_dok_temp
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
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT;

-- Dumping data for table db_kredit.d_debitur_dok_temp: ~13 rows (approximately)
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
	(13, 'ceef745541857297b2b14a93f96fe4a5', 3, '3_bb953a7292f6da6a9d268fd25131aae2.png', '2018-08-04 23:45:43', '2018-08-04 23:45:43');
/*!40000 ALTER TABLE `d_debitur_dok_temp` ENABLE KEYS */;


-- Dumping structure for table db_kredit.d_debitur_dukcapil
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

-- Dumping data for table db_kredit.d_debitur_dukcapil: ~0 rows (approximately)
DELETE FROM `d_debitur_dukcapil`;
/*!40000 ALTER TABLE `d_debitur_dukcapil` DISABLE KEYS */;
INSERT INTO `d_debitur_dukcapil` (`nik`, `nokk`, `nama`, `tglhr`, `kotalhr`, `kdkelamin`, `kdagama`, `kdpekerjaan`, `kdkawin`, `kdprop`, `kdkabkota`, `kdkec`, `kdkel`, `alamat`, `rt`, `rw`, `created_at`, `updated_at`) VALUES
	('1111111111111111', '2222222222222222', 'Test aja', '1970-01-01', 'Sukabumi', 1, 1, 5, 2, '31', '73', '01', '1001', 'Teeadcds', 9, 3, '2018-08-05 11:46:44', '2018-08-05 11:46:44'),
	('1111111111111113', '3333333333333333', 'Test lagi', '2018-08-05', 'Test', 1, 1, 5, 2, '31', '75', '01', '1001', 'Test', 10, 2, '2018-08-05 18:01:41', NULL),
	('3222222222222222', '1111111111111133', 'Sony', '1988-07-01', 'Kotabumi', 1, 1, 5, 2, '31', '73', '01', '1001', 'Test', 1, 1, '2018-08-09 03:23:19', NULL);
/*!40000 ALTER TABLE `d_debitur_dukcapil` ENABLE KEYS */;


-- Dumping structure for table db_kredit.d_debitur_hutang
DROP TABLE IF EXISTS `d_debitur_hutang`;
CREATE TABLE IF NOT EXISTS `d_debitur_hutang` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `nik` varchar(16) DEFAULT NULL,
  `kdhutang` varchar(1) DEFAULT NULL,
  `total` decimal(18,0) DEFAULT NULL,
  `angsuran` decimal(18,0) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `Index 2` (`nik`,`kdhutang`),
  KEY `FK_d_debitur_hutang_t_hutang` (`kdhutang`),
  CONSTRAINT `FK_d_debitur_hutang_d_debitur` FOREIGN KEY (`nik`) REFERENCES `d_debitur` (`nik`),
  CONSTRAINT `FK_d_debitur_hutang_t_hutang` FOREIGN KEY (`kdhutang`) REFERENCES `t_hutang` (`kdhutang`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT;

-- Dumping data for table db_kredit.d_debitur_hutang: ~2 rows (approximately)
DELETE FROM `d_debitur_hutang`;
/*!40000 ALTER TABLE `d_debitur_hutang` DISABLE KEYS */;
INSERT INTO `d_debitur_hutang` (`id`, `nik`, `kdhutang`, `total`, `angsuran`, `created_at`, `updated_at`) VALUES
	(1, '1111111111111111', '1', 100000000, 2000000, '2018-08-04 16:31:55', '2018-08-04 16:31:55'),
	(3, '1111111111111113', '1', 100000000, 2500000, '2018-08-04 21:57:29', '2018-08-04 21:57:29'),
	(4, '3222222222222222', '1', 100000000, 1000000, '2018-08-06 00:08:00', '2018-08-06 00:08:00'),
	(5, '3312056677660001', '1', 20000000, 1000000, '2018-08-09 03:39:03', '2018-08-09 03:39:03');
/*!40000 ALTER TABLE `d_debitur_hutang` ENABLE KEYS */;


-- Dumping structure for table db_kredit.d_debitur_pasangan
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
  PRIMARY KEY (`id`),
  UNIQUE KEY `Index 2` (`nik`,`nik_p`),
  KEY `FK_d_debitur_pasangan_t_kelamin` (`kdkelamin`),
  KEY `FK_d_debitur_pasangan_t_agama` (`kdagama`),
  KEY `FK_d_debitur_pasangan_t_pekerjaan` (`kdpekerjaan`),
  KEY `FK_d_debitur_pasangan_t_pendidikan` (`kdpendidikan`),
  KEY `FK_d_debitur_pasangan_t_kelurahan` (`kdprop`,`kdkabkota`,`kdkec`,`kdkel`),
  CONSTRAINT `FK_d_debitur_pasangan_d_debitur` FOREIGN KEY (`nik`) REFERENCES `d_debitur` (`nik`),
  CONSTRAINT `FK_d_debitur_pasangan_t_agama` FOREIGN KEY (`kdagama`) REFERENCES `t_agama` (`kdagama`),
  CONSTRAINT `FK_d_debitur_pasangan_t_kelamin` FOREIGN KEY (`kdkelamin`) REFERENCES `t_kelamin` (`kdkelamin`),
  CONSTRAINT `FK_d_debitur_pasangan_t_kelurahan` FOREIGN KEY (`kdprop`, `kdkabkota`, `kdkec`, `kdkel`) REFERENCES `t_kelurahan` (`kdprop`, `kdkabkota`, `kdkec`, `kdkel`),
  CONSTRAINT `FK_d_debitur_pasangan_t_pekerjaan` FOREIGN KEY (`kdpekerjaan`) REFERENCES `t_pekerjaan` (`kdpekerjaan`),
  CONSTRAINT `FK_d_debitur_pasangan_t_pendidikan` FOREIGN KEY (`kdpendidikan`) REFERENCES `t_pendidikan` (`kdpendidikan`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;

-- Dumping data for table db_kredit.d_debitur_pasangan: ~2 rows (approximately)
DELETE FROM `d_debitur_pasangan`;
/*!40000 ALTER TABLE `d_debitur_pasangan` DISABLE KEYS */;
INSERT INTO `d_debitur_pasangan` (`id`, `nik`, `nik_p`, `nama`, `tglhr`, `kotalhr`, `kdkelamin`, `kdagama`, `kdpekerjaan`, `kdpendidikan`, `kdprop`, `kdkabkota`, `kdkec`, `kdkel`, `kodepos`, `alamat`, `telp`, `nohp`, `email`, `created_at`, `updated_at`) VALUES
	(1, '1111111111111111', '2222222222222222', 'test', '0000-00-00', 'test', 2, 3, NULL, 5, '31', '71', '02', '1002', '12345', 'test', '123', '123', 'test@gmail.com', '2018-08-04 16:31:55', '2018-08-04 16:31:55'),
	(3, '1111111111111113', '5555555555555555', 'test', '0000-00-00', 'test', 2, 1, NULL, 5, '31', '75', '01', '1006', '12345', 'test', '123', '12345', 'test@gmail.com', '2018-08-04 21:57:29', '2018-08-04 21:57:29'),
	(4, '12312143566788', '1234455677889990', 'tes', '0000-00-00', 'jakata', 2, 1, NULL, 5, '31', '73', '01', '1002', '12334', 'jakarta', '12233', '1223456677', 'test@gmail.com', '2018-08-05 00:09:23', '2018-08-05 00:09:23');
/*!40000 ALTER TABLE `d_debitur_pasangan` ENABLE KEYS */;


-- Dumping structure for table db_kredit.d_debitur_pekerjaan
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
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;

-- Dumping data for table db_kredit.d_debitur_pekerjaan: ~2 rows (approximately)
DELETE FROM `d_debitur_pekerjaan`;
/*!40000 ALTER TABLE `d_debitur_pekerjaan` DISABLE KEYS */;
INSERT INTO `d_debitur_pekerjaan` (`id`, `nik`, `nmkantor`, `bidang`, `jenis`, `alamat`, `jabatan`, `atasan`, `telp`, `tgkerja`, `penghasilan`, `created_at`, `updated_at`) VALUES
	(1, '1111111111111111', 'test', '-', '-', '-', '-', '-', '123', '2018-08-01', 10000000, '2018-08-04 16:31:55', '2018-08-04 16:31:55'),
	(3, '1111111111111113', 'test', '-', '-', '-', '-', '-', '123', '2018-08-01', 10000000, '2018-08-04 21:57:29', '2018-08-04 21:57:29'),
	(4, '12312143566788', 'PT', 'test', 'test', 'test', 'staff', 'Bapak test', '1234456', '2015-06-16', 5000000, '2018-08-05 00:09:23', '2018-08-05 00:09:23'),
	(5, '3222222222222222', '-', '-', '-', '-', '-', '-', '123', '2018-08-01', 10000000, '2018-08-06 00:08:00', '2018-08-06 00:08:00'),
	(6, '3312056677660001', 'Kantor Sosial', '-', '-', '-', '-', '-', '02112345', '2009-01-01', 7000000, '2018-08-09 03:39:03', '2018-08-09 03:39:03');
/*!40000 ALTER TABLE `d_debitur_pekerjaan` ENABLE KEYS */;


-- Dumping structure for table db_kredit.d_debitur_pekerjaan_p
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
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT;

-- Dumping data for table db_kredit.d_debitur_pekerjaan_p: ~2 rows (approximately)
DELETE FROM `d_debitur_pekerjaan_p`;
/*!40000 ALTER TABLE `d_debitur_pekerjaan_p` DISABLE KEYS */;
INSERT INTO `d_debitur_pekerjaan_p` (`id`, `nik`, `nmkantor`, `bidang`, `jenis`, `alamat`, `jabatan`, `atasan`, `telp`, `tgkerja`, `penghasilan`, `created_at`, `updated_at`) VALUES
	(1, '1111111111111111', 'test', '-', '-', '-', '-', '-', '123', '2018-08-01', 5000000, '2018-08-04 16:31:55', '2018-08-04 16:31:55'),
	(3, '1111111111111113', 'test', '-', '-', '-', '-', '-', '123', '2018-08-01', 5000000, '2018-08-04 21:57:29', '2018-08-04 21:57:29');
/*!40000 ALTER TABLE `d_debitur_pekerjaan_p` ENABLE KEYS */;


-- Dumping structure for table db_kredit.d_debitur_skoring
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

-- Dumping data for table db_kredit.d_debitur_skoring: ~2 rows (approximately)
DELETE FROM `d_debitur_skoring`;
/*!40000 ALTER TABLE `d_debitur_skoring` DISABLE KEYS */;
INSERT INTO `d_debitur_skoring` (`id`, `nik`, `nilai`, `created_at`, `updated_at`) VALUES
	(11, '1111111111111113', 76, '2018-08-05 21:30:13', '2018-08-05 21:30:13'),
	(13, '1111111111111111', 68, '2018-08-05 21:34:41', '2018-08-05 21:34:41');
/*!40000 ALTER TABLE `d_debitur_skoring` ENABLE KEYS */;


-- Dumping structure for table db_kredit.d_form
DROP TABLE IF EXISTS `d_form`;
CREATE TABLE IF NOT EXISTS `d_form` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `kdpetugas` varchar(2) NOT NULL,
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
) ENGINE=InnoDB AUTO_INCREMENT=51 DEFAULT CHARSET=latin1;

-- Dumping data for table db_kredit.d_form: ~39 rows (approximately)
DELETE FROM `d_form`;
/*!40000 ALTER TABLE `d_form` DISABLE KEYS */;
INSERT INTO `d_form` (`id`, `kdpetugas`, `tahun`, `nourut`, `status`, `created_at`, `updated_at`) VALUES
	(1, '01', '2018', 1, 0, '2018-08-02 18:09:20', '2018-08-02 18:09:20'),
	(2, '01', '2018', 2, 0, '2018-08-02 18:09:20', '2018-08-02 18:09:20'),
	(3, '01', '2018', 3, 0, '2018-08-02 18:09:20', '2018-08-02 18:09:20'),
	(4, '01', '2018', 4, 1, '2018-08-02 18:09:20', '2018-08-02 18:09:20'),
	(5, '01', '2018', 5, 0, '2018-08-02 18:09:20', '2018-08-02 18:09:20'),
	(6, '01', '2018', 6, 0, '2018-08-02 18:09:20', '2018-08-02 18:09:20'),
	(7, '01', '2018', 7, 0, '2018-08-02 18:09:20', '2018-08-02 18:09:20'),
	(8, '01', '2018', 8, 0, '2018-08-02 18:09:20', '2018-08-02 18:09:20'),
	(9, '01', '2018', 9, 0, '2018-08-02 18:09:20', '2018-08-02 18:09:20'),
	(10, '01', '2018', 10, 0, '2018-08-02 18:09:20', '2018-08-02 18:09:20'),
	(11, '01', '2018', 11, 0, '2018-08-02 18:17:40', '2018-08-02 18:17:40'),
	(12, '01', '2018', 12, 0, '2018-08-02 18:17:40', '2018-08-02 18:17:40'),
	(13, '01', '2018', 13, 0, '2018-08-02 18:17:40', '2018-08-02 18:17:40'),
	(14, '01', '2018', 14, 0, '2018-08-02 18:17:40', '2018-08-02 18:17:40'),
	(15, '01', '2018', 15, 0, '2018-08-02 18:17:40', '2018-08-02 18:17:40'),
	(16, '01', '2018', 16, 0, '2018-08-02 18:17:40', '2018-08-02 18:17:40'),
	(17, '01', '2018', 17, 0, '2018-08-02 18:17:40', '2018-08-02 18:17:40'),
	(18, '01', '2018', 18, 0, '2018-08-02 18:17:40', '2018-08-02 18:17:40'),
	(19, '01', '2018', 19, 0, '2018-08-02 18:17:40', '2018-08-02 18:17:40'),
	(20, '01', '2018', 20, 0, '2018-08-02 18:17:40', '2018-08-02 18:17:40'),
	(21, '01', '2018', 21, 0, '2018-08-02 18:17:40', '2018-08-02 18:17:40'),
	(22, '01', '2018', 22, 0, '2018-08-02 18:17:40', '2018-08-02 18:17:40'),
	(23, '01', '2018', 23, 0, '2018-08-02 18:17:40', '2018-08-02 18:17:40'),
	(24, '01', '2018', 24, 0, '2018-08-02 18:17:40', '2018-08-02 18:17:40'),
	(31, '01', '2018', 25, 0, '2018-08-02 19:04:25', '2018-08-02 19:04:25'),
	(32, '01', '2018', 26, 0, '2018-08-02 19:04:25', '2018-08-02 19:04:25'),
	(33, '01', '2018', 27, 0, '2018-08-02 19:04:25', '2018-08-02 19:04:25'),
	(34, '01', '2018', 28, 0, '2018-08-02 19:04:25', '2018-08-02 19:04:25'),
	(35, '01', '2018', 29, 0, '2018-08-02 19:04:25', '2018-08-02 19:04:25'),
	(36, '01', '2018', 30, 0, '2018-08-05 00:13:23', '2018-08-05 00:13:23'),
	(37, '01', '2018', 31, 0, '2018-08-05 00:13:23', '2018-08-05 00:13:23'),
	(38, '01', '2018', 32, 0, '2018-08-05 00:13:23', '2018-08-05 00:13:23'),
	(39, '01', '2018', 33, 0, '2018-08-05 00:13:23', '2018-08-05 00:13:23'),
	(40, '01', '2018', 34, 0, '2018-08-05 00:13:23', '2018-08-05 00:13:23'),
	(41, '01', '2018', 35, 0, '2018-08-05 00:13:23', '2018-08-05 00:13:23'),
	(42, '01', '2018', 36, 0, '2018-08-05 00:13:23', '2018-08-05 00:13:23'),
	(43, '01', '2018', 37, 0, '2018-08-05 00:13:23', '2018-08-05 00:13:23'),
	(44, '01', '2018', 38, 0, '2018-08-05 00:13:23', '2018-08-05 00:13:23'),
	(45, '01', '2018', 39, 0, '2018-08-05 00:13:23', '2018-08-05 00:13:23'),
	(46, '01', '2018', 40, 0, '2018-08-09 00:36:22', '2018-08-09 00:36:22'),
	(47, '01', '2018', 41, 0, '2018-08-09 00:36:22', '2018-08-09 00:36:22'),
	(48, '01', '2018', 42, 0, '2018-08-09 00:36:22', '2018-08-09 00:36:22'),
	(49, '01', '2018', 43, 0, '2018-08-09 00:36:22', '2018-08-09 00:36:22'),
	(50, '01', '2018', 44, 0, '2018-08-09 00:36:22', '2018-08-09 00:36:22');
/*!40000 ALTER TABLE `d_form` ENABLE KEYS */;


-- Dumping structure for table db_kredit.d_form_debitur
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
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

-- Dumping data for table db_kredit.d_form_debitur: ~0 rows (approximately)
DELETE FROM `d_form_debitur`;
/*!40000 ALTER TABLE `d_form_debitur` DISABLE KEYS */;
INSERT INTO `d_form_debitur` (`id`, `id_form`, `nik`, `created_at`, `updated_at`) VALUES
	(1, 4, '1111111111111111', '2018-08-04 16:31:55', '2018-08-04 16:31:55');
/*!40000 ALTER TABLE `d_form_debitur` ENABLE KEYS */;


-- Dumping structure for table db_kredit.d_hunian
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

-- Dumping data for table db_kredit.d_hunian: ~0 rows (approximately)
DELETE FROM `d_hunian`;
/*!40000 ALTER TABLE `d_hunian` DISABLE KEYS */;
INSERT INTO `d_hunian` (`id`, `id_developer`, `kdprop`, `kdkabkota`, `kdkec`, `kdkel`, `kodepos`, `alamat`, `nmhunian`, `created_at`, `updated_at`) VALUES
	(5, 1, '31', '71', '01', '1001', '12345', 'Jl. Pinang Raya No. 3-10, Jakarta Timur', 'Kelapa village', '2018-08-02 20:36:55', '2018-08-02 20:36:56');
/*!40000 ALTER TABLE `d_hunian` ENABLE KEYS */;


-- Dumping structure for table db_kredit.d_log_sistem
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

-- Dumping data for table db_kredit.d_log_sistem: ~0 rows (approximately)
DELETE FROM `d_log_sistem`;
/*!40000 ALTER TABLE `d_log_sistem` DISABLE KEYS */;
/*!40000 ALTER TABLE `d_log_sistem` ENABLE KEYS */;


-- Dumping structure for table db_kredit.t_agama
DROP TABLE IF EXISTS `t_agama`;
CREATE TABLE IF NOT EXISTS `t_agama` (
  `kdagama` int(10) NOT NULL DEFAULT '0',
  `nmagama` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`kdagama`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Dumping data for table db_kredit.t_agama: ~7 rows (approximately)
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


-- Dumping structure for table db_kredit.t_alamat
DROP TABLE IF EXISTS `t_alamat`;
CREATE TABLE IF NOT EXISTS `t_alamat` (
  `kdalamat` varchar(2) NOT NULL,
  `nmalamat` varchar(255) DEFAULT NULL,
  `wajib` varchar(1) DEFAULT NULL,
  PRIMARY KEY (`kdalamat`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Dumping data for table db_kredit.t_alamat: ~4 rows (approximately)
DELETE FROM `t_alamat`;
/*!40000 ALTER TABLE `t_alamat` DISABLE KEYS */;
INSERT INTO `t_alamat` (`kdalamat`, `nmalamat`, `wajib`) VALUES
	('1', 'Alamat KTP', '1'),
	('2', 'Alamat Domisili', '1'),
	('3', 'Alamat Surat-menyurat', '0'),
	('4', 'Alamat Lainnya', '0');
/*!40000 ALTER TABLE `t_alamat` ENABLE KEYS */;


-- Dumping structure for table db_kredit.t_api_setting
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

-- Dumping data for table db_kredit.t_api_setting: ~0 rows (approximately)
DELETE FROM `t_api_setting`;
/*!40000 ALTER TABLE `t_api_setting` DISABLE KEYS */;
INSERT INTO `t_api_setting` (`id`, `uraian`, `jenis`, `url`, `user`, `pass`, `token`, `aktif`) VALUES
	(1, 'API DUKCAPIL', '1', 'http://10.0.0.44/dprkp/wsdprkp.php', 'DPRKPDp0', '0Rup1ahDp', '-', '1');
/*!40000 ALTER TABLE `t_api_setting` ENABLE KEYS */;


-- Dumping structure for table db_kredit.t_app_version
DROP TABLE IF EXISTS `t_app_version`;
CREATE TABLE IF NOT EXISTS `t_app_version` (
  `versi` varchar(50) NOT NULL,
  `nama` varchar(50) DEFAULT NULL,
  `ket` varchar(255) DEFAULT NULL,
  `status` varchar(1) DEFAULT NULL,
  PRIMARY KEY (`versi`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT;

-- Dumping data for table db_kredit.t_app_version: ~0 rows (approximately)
DELETE FROM `t_app_version`;
/*!40000 ALTER TABLE `t_app_version` DISABLE KEYS */;
INSERT INTO `t_app_version` (`versi`, `nama`, `ket`, `status`) VALUES
	('v.1.0.0', 'Aplikasi', 'Prototype', '1');
/*!40000 ALTER TABLE `t_app_version` ENABLE KEYS */;


-- Dumping structure for table db_kredit.t_bpjs
DROP TABLE IF EXISTS `t_bpjs`;
CREATE TABLE IF NOT EXISTS `t_bpjs` (
  `kdbpjs` int(10) NOT NULL,
  `nmbpjs` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`kdbpjs`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Dumping data for table db_kredit.t_bpjs: ~4 rows (approximately)
DELETE FROM `t_bpjs`;
/*!40000 ALTER TABLE `t_bpjs` DISABLE KEYS */;
INSERT INTO `t_bpjs` (`kdbpjs`, `nmbpjs`) VALUES
	(0, 'Belum Terdaftar'),
	(1, 'Kelas I'),
	(2, 'Kelas II'),
	(3, 'Kelas III');
/*!40000 ALTER TABLE `t_bpjs` ENABLE KEYS */;


-- Dumping structure for table db_kredit.t_developer
DROP TABLE IF EXISTS `t_developer`;
CREATE TABLE IF NOT EXISTS `t_developer` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `nmdeveloper` varchar(255) NOT NULL,
  `npwp` varchar(15) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

-- Dumping data for table db_kredit.t_developer: ~0 rows (approximately)
DELETE FROM `t_developer`;
/*!40000 ALTER TABLE `t_developer` DISABLE KEYS */;
INSERT INTO `t_developer` (`id`, `nmdeveloper`, `npwp`, `created_at`, `updated_at`) VALUES
	(1, 'CV. Bangun Perkasa', '000000000000000', '2018-08-02 20:34:12', '2018-08-02 20:34:12');
/*!40000 ALTER TABLE `t_developer` ENABLE KEYS */;


-- Dumping structure for table db_kredit.t_dok
DROP TABLE IF EXISTS `t_dok`;
CREATE TABLE IF NOT EXISTS `t_dok` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `nmdok` varchar(255) DEFAULT NULL,
  `tipe` varchar(255) DEFAULT NULL,
  `is_wajib` varchar(1) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=latin1;

-- Dumping data for table db_kredit.t_dok: ~17 rows (approximately)
DELETE FROM `t_dok`;
/*!40000 ALTER TABLE `t_dok` DISABLE KEYS */;
INSERT INTO `t_dok` (`id`, `nmdok`, `tipe`, `is_wajib`) VALUES
	(1, 'e-KTP', 'jpg,png,pdf', '1'),
	(2, 'Kartu Keluarga (KK)', 'jpg,png,pdf', '1'),
	(3, 'NPWP', 'jpg,png,pdf', '1'),
	(4, 'Surat Ket.Penghasilan', 'jpg,png,pdf', '1'),
	(5, 'Foto Pribadi', 'jpg,png,pdf', '1'),
	(6, 'Form Permohonan', 'jpg,png,pdf', '1'),
	(7, 'Surat Pernyataan (Pergub)', 'jpg,png,pdf', '1'),
	(8, 'BPJS', 'jpg,png,pdf', '0'),
	(9, 'KJP', 'jpg,png,pdf', '0'),
	(10, 'Foto Kendaraan Roda 2', 'jpg,png,pdf', '0'),
	(11, 'Foto Kendaraan Roda 4', 'jpg,png,pdf', '0'),
	(12, 'Foto Tanah/Rumah', 'jpg,png,pdf', '0'),
	(13, 'Rek.Koran 3 bulan terakhir', 'jpg,png,pdf', '1'),
	(14, 'e-KTP (Pasangan)', 'jpg,png,pdf', '0'),
	(15, 'NPWP (Pasangan)', 'jpg,png,pdf', '0'),
	(16, 'Surat Ket.Penghasilan (Pasangan)', 'jpg,png,pdf', '0'),
	(17, 'Foto Pasangan', 'jpg,png,pdf', '0');
/*!40000 ALTER TABLE `t_dok` ENABLE KEYS */;


-- Dumping structure for table db_kredit.t_hutang
DROP TABLE IF EXISTS `t_hutang`;
CREATE TABLE IF NOT EXISTS `t_hutang` (
  `kdhutang` varchar(1) NOT NULL DEFAULT '',
  `nmhutang` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`kdhutang`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Dumping data for table db_kredit.t_hutang: ~2 rows (approximately)
DELETE FROM `t_hutang`;
/*!40000 ALTER TABLE `t_hutang` DISABLE KEYS */;
INSERT INTO `t_hutang` (`kdhutang`, `nmhutang`) VALUES
	('1', 'Perbankan'),
	('2', 'Koperasi'),
	('3', 'Perorangan');
/*!40000 ALTER TABLE `t_hutang` ENABLE KEYS */;


-- Dumping structure for table db_kredit.t_jenkredit
DROP TABLE IF EXISTS `t_jenkredit`;
CREATE TABLE IF NOT EXISTS `t_jenkredit` (
  `kdjenkredit` varchar(1) NOT NULL,
  `nmjenkredit` varchar(255) DEFAULT NULL,
  `aktif` varchar(1) DEFAULT NULL,
  PRIMARY KEY (`kdjenkredit`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Dumping data for table db_kredit.t_jenkredit: ~4 rows (approximately)
DELETE FROM `t_jenkredit`;
/*!40000 ALTER TABLE `t_jenkredit` DISABLE KEYS */;
INSERT INTO `t_jenkredit` (`kdjenkredit`, `nmjenkredit`, `aktif`) VALUES
	('1', 'DP 0 Rupiah', '1'),
	('2', 'Rusunawa', '0'),
	('3', 'Komersil', '0'),
	('4', 'Lainnya', '0');
/*!40000 ALTER TABLE `t_jenkredit` ENABLE KEYS */;


-- Dumping structure for table db_kredit.t_kabkota
DROP TABLE IF EXISTS `t_kabkota`;
CREATE TABLE IF NOT EXISTS `t_kabkota` (
  `kdprop` varchar(2) NOT NULL DEFAULT '',
  `kdkabkota` varchar(2) NOT NULL DEFAULT '',
  `nmkabkota` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`kdprop`,`kdkabkota`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Dumping data for table db_kredit.t_kabkota: ~6 rows (approximately)
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


-- Dumping structure for table db_kredit.t_kawin
DROP TABLE IF EXISTS `t_kawin`;
CREATE TABLE IF NOT EXISTS `t_kawin` (
  `kdkawin` int(10) NOT NULL DEFAULT '0',
  `nmkawin` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`kdkawin`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Dumping data for table db_kredit.t_kawin: ~4 rows (approximately)
DELETE FROM `t_kawin`;
/*!40000 ALTER TABLE `t_kawin` DISABLE KEYS */;
INSERT INTO `t_kawin` (`kdkawin`, `nmkawin`) VALUES
	(1, 'BELUM KAWIN'),
	(2, 'KAWIN'),
	(3, 'CERAI HIDUP'),
	(4, 'CERAI MATI');
/*!40000 ALTER TABLE `t_kawin` ENABLE KEYS */;


-- Dumping structure for table db_kredit.t_kecamatan
DROP TABLE IF EXISTS `t_kecamatan`;
CREATE TABLE IF NOT EXISTS `t_kecamatan` (
  `kdprop` varchar(2) NOT NULL,
  `kdkabkota` varchar(2) NOT NULL,
  `kdkec` varchar(2) NOT NULL,
  `nmkec` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`kdprop`,`kdkabkota`,`kdkec`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Dumping data for table db_kredit.t_kecamatan: ~44 rows (approximately)
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


-- Dumping structure for table db_kredit.t_kelamin
DROP TABLE IF EXISTS `t_kelamin`;
CREATE TABLE IF NOT EXISTS `t_kelamin` (
  `kdkelamin` int(10) NOT NULL,
  `nmkelamin` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`kdkelamin`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Dumping data for table db_kredit.t_kelamin: ~2 rows (approximately)
DELETE FROM `t_kelamin`;
/*!40000 ALTER TABLE `t_kelamin` DISABLE KEYS */;
INSERT INTO `t_kelamin` (`kdkelamin`, `nmkelamin`) VALUES
	(1, 'LAKI-LAKI'),
	(2, 'PEREMPUAN');
/*!40000 ALTER TABLE `t_kelamin` ENABLE KEYS */;


-- Dumping structure for table db_kredit.t_kelurahan
DROP TABLE IF EXISTS `t_kelurahan`;
CREATE TABLE IF NOT EXISTS `t_kelurahan` (
  `kdprop` varchar(2) NOT NULL,
  `kdkabkota` varchar(2) NOT NULL,
  `kdkec` varchar(2) NOT NULL,
  `kdkel` varchar(4) NOT NULL,
  `nmkel` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`kdprop`,`kdkabkota`,`kdkec`,`kdkel`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT;

-- Dumping data for table db_kredit.t_kelurahan: ~267 rows (approximately)
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


-- Dumping structure for table db_kredit.t_level
DROP TABLE IF EXISTS `t_level`;
CREATE TABLE IF NOT EXISTS `t_level` (
  `kdlevel` varchar(2) NOT NULL,
  `nmlevel` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`kdlevel`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Dumping data for table db_kredit.t_level: ~2 rows (approximately)
DELETE FROM `t_level`;
/*!40000 ALTER TABLE `t_level` DISABLE KEYS */;
INSERT INTO `t_level` (`kdlevel`, `nmlevel`) VALUES
	('00', 'Administrator'),
	('01', 'Operator'),
	('02', 'Supervisor'),
	('99', 'Super Administrator');
/*!40000 ALTER TABLE `t_level` ENABLE KEYS */;


-- Dumping structure for table db_kredit.t_menu
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

-- Dumping data for table db_kredit.t_menu: ~9 rows (approximately)
DELETE FROM `t_menu`;
/*!40000 ALTER TABLE `t_menu` DISABLE KEYS */;
INSERT INTO `t_menu` (`id`, `nmmenu`, `url`, `nmfile`, `is_parent`, `parent_id`, `nourut`, `icon`, `kdlevel`, `aktif`, `new_tab`) VALUES
	(1, 'Beranda', '', 'home.html', '0', 0, 1, 'fa fa-home', '+00+01+02+', '1', '0'),
	(2, 'Form Kredit', '#', NULL, '1', 0, 2, 'fa fa-edit', '+01+', '1', '0'),
	(3, 'Debitur', '#', NULL, '1', 0, 3, 'fa fa-group', '+00+01+02+', '1', '0'),
	(4, 'Referensi', '#', NULL, '1', 0, 4, 'fa fa-list', '+00+01+02+', '1', '0'),
	(5, 'Generate', 'form/rekam', 'form-rekam.html', '0', 2, 1, '', '+00+01+02+', '1', '0'),
	(6, 'Data', 'debitur/rekam', 'debitur-rekam.html', '0', 3, 1, '', '+00+01+02+', '1', '0'),
	(7, 'User', 'ref/user', 'ref-user.html', '0', 4, 1, '', '+00+01+02+', '1', '0'),
	(8, 'Petugas', 'ref/petugas', 'ref-petugas.html', '0', 4, 2, '', '+00+01+02+', '1', '0'),
	(9, 'Kuota', 'ref/kuota', 'ref-kuota.html', '0', 4, 3, '', '+00+01+02+', '1', '0'),
	(10, 'Skoring', 'debitur/skoring', 'debitur-skoring.html', '0', 3, 2, '', '+00+', '1', '0');
/*!40000 ALTER TABLE `t_menu` ENABLE KEYS */;


-- Dumping structure for table db_kredit.t_pekerjaan
DROP TABLE IF EXISTS `t_pekerjaan`;
CREATE TABLE IF NOT EXISTS `t_pekerjaan` (
  `kdpekerjaan` int(11) NOT NULL,
  `nmpekerjaan` varchar(255) NOT NULL,
  PRIMARY KEY (`kdpekerjaan`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Dumping data for table db_kredit.t_pekerjaan: ~92 rows (approximately)
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


-- Dumping structure for table db_kredit.t_pendidikan
DROP TABLE IF EXISTS `t_pendidikan`;
CREATE TABLE IF NOT EXISTS `t_pendidikan` (
  `kdpendidikan` int(10) NOT NULL DEFAULT '0',
  `nmpendidikan` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`kdpendidikan`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Dumping data for table db_kredit.t_pendidikan: ~6 rows (approximately)
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


-- Dumping structure for table db_kredit.t_petugas
DROP TABLE IF EXISTS `t_petugas`;
CREATE TABLE IF NOT EXISTS `t_petugas` (
  `kdpetugas` varchar(2) NOT NULL DEFAULT '',
  `nmpetugas` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`kdpetugas`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Dumping data for table db_kredit.t_petugas: ~3 rows (approximately)
DELETE FROM `t_petugas`;
/*!40000 ALTER TABLE `t_petugas` DISABLE KEYS */;
INSERT INTO `t_petugas` (`kdpetugas`, `nmpetugas`) VALUES
	('01', 'Petugas A'),
	('02', 'Petugas B'),
	('03', 'Petugas C');
/*!40000 ALTER TABLE `t_petugas` ENABLE KEYS */;


-- Dumping structure for table db_kredit.t_petugas_kuota
DROP TABLE IF EXISTS `t_petugas_kuota`;
CREATE TABLE IF NOT EXISTS `t_petugas_kuota` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `kdpetugas` varchar(2) NOT NULL,
  `tahun` varchar(4) NOT NULL,
  `kuota` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `Index 2` (`kdpetugas`,`tahun`),
  CONSTRAINT `FK_t_petugas_kuota_t_petugas` FOREIGN KEY (`kdpetugas`) REFERENCES `t_petugas` (`kdpetugas`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;

-- Dumping data for table db_kredit.t_petugas_kuota: ~3 rows (approximately)
DELETE FROM `t_petugas_kuota`;
/*!40000 ALTER TABLE `t_petugas_kuota` DISABLE KEYS */;
INSERT INTO `t_petugas_kuota` (`id`, `kdpetugas`, `tahun`, `kuota`) VALUES
	(2, '02', '2018', 100),
	(4, '01', '2018', 60),
	(5, '03', '2018', 50);
/*!40000 ALTER TABLE `t_petugas_kuota` ENABLE KEYS */;


-- Dumping structure for table db_kredit.t_prop
DROP TABLE IF EXISTS `t_prop`;
CREATE TABLE IF NOT EXISTS `t_prop` (
  `kdprop` varchar(2) NOT NULL DEFAULT '0',
  `nmprop` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`kdprop`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Dumping data for table db_kredit.t_prop: ~0 rows (approximately)
DELETE FROM `t_prop`;
/*!40000 ALTER TABLE `t_prop` DISABLE KEYS */;
INSERT INTO `t_prop` (`kdprop`, `nmprop`) VALUES
	('31', 'DKI JAKARTA');
/*!40000 ALTER TABLE `t_prop` ENABLE KEYS */;


-- Dumping structure for table db_kredit.t_skoring
DROP TABLE IF EXISTS `t_skoring`;
CREATE TABLE IF NOT EXISTS `t_skoring` (
  `kdskoring` varchar(1) NOT NULL,
  `nmskoring` varchar(255) DEFAULT NULL,
  `max` int(11) DEFAULT NULL,
  `bobot` int(10) DEFAULT NULL,
  PRIMARY KEY (`kdskoring`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Dumping data for table db_kredit.t_skoring: ~4 rows (approximately)
DELETE FROM `t_skoring`;
/*!40000 ALTER TABLE `t_skoring` DISABLE KEYS */;
INSERT INTO `t_skoring` (`kdskoring`, `nmskoring`, `max`, `bobot`) VALUES
	('1', 'Tanggungan', 5, 40),
	('2', 'Lama tinggal', 5, 40),
	('3', 'Lokasi', 5, 20),
	('4', 'KJP', 0, 0);
/*!40000 ALTER TABLE `t_skoring` ENABLE KEYS */;


-- Dumping structure for table db_kredit.t_skoring_lama_tinggal
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

-- Dumping data for table db_kredit.t_skoring_lama_tinggal: ~2 rows (approximately)
DELETE FROM `t_skoring_lama_tinggal`;
/*!40000 ALTER TABLE `t_skoring_lama_tinggal` DISABLE KEYS */;
INSERT INTO `t_skoring_lama_tinggal` (`id`, `kdskoring`, `tahun1`, `tahun2`, `nilai`) VALUES
	(1, '2', 0, 4, 0),
	(2, '2', 5, 10, 3),
	(3, '2', 11, 100, 5);
/*!40000 ALTER TABLE `t_skoring_lama_tinggal` ENABLE KEYS */;


-- Dumping structure for table db_kredit.t_skoring_lokasi
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

-- Dumping data for table db_kredit.t_skoring_lokasi: ~3 rows (approximately)
DELETE FROM `t_skoring_lokasi`;
/*!40000 ALTER TABLE `t_skoring_lokasi` DISABLE KEYS */;
INSERT INTO `t_skoring_lokasi` (`id`, `kdskoring`, `lokasi`, `nilai`) VALUES
	(1, '3', 'kdkabkota', 2),
	(2, '3', 'kdkec', 3),
	(3, '3', 'kdkel', 5);
/*!40000 ALTER TABLE `t_skoring_lokasi` ENABLE KEYS */;


-- Dumping structure for table db_kredit.t_skoring_tanggungan
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

-- Dumping data for table db_kredit.t_skoring_tanggungan: ~6 rows (approximately)
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


-- Dumping structure for table db_kredit.t_status_debitur
DROP TABLE IF EXISTS `t_status_debitur`;
CREATE TABLE IF NOT EXISTS `t_status_debitur` (
  `status` int(10) NOT NULL DEFAULT '0',
  `nmstatus` varchar(255) DEFAULT NULL,
  `skoring` varchar(1) DEFAULT NULL,
  PRIMARY KEY (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT;

-- Dumping data for table db_kredit.t_status_debitur: ~5 rows (approximately)
DELETE FROM `t_status_debitur`;
/*!40000 ALTER TABLE `t_status_debitur` DISABLE KEYS */;
INSERT INTO `t_status_debitur` (`status`, `nmstatus`, `skoring`) VALUES
	(0, 'Pengajuan online', '0'),
	(1, 'Pengajuan petugas', '0'),
	(2, 'Pengajuan valid', '1'),
	(3, 'Pengajuan tidak valid', '0'),
	(4, 'Skoring debitur', '1');
/*!40000 ALTER TABLE `t_status_debitur` ENABLE KEYS */;


-- Dumping structure for table db_kredit.t_status_form
DROP TABLE IF EXISTS `t_status_form`;
CREATE TABLE IF NOT EXISTS `t_status_form` (
  `status` int(10) NOT NULL DEFAULT '0',
  `nmstatus` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Dumping data for table db_kredit.t_status_form: ~4 rows (approximately)
DELETE FROM `t_status_form`;
/*!40000 ALTER TABLE `t_status_form` DISABLE KEYS */;
INSERT INTO `t_status_form` (`status`, `nmstatus`) VALUES
	(0, 'Generate form permohonan'),
	(1, 'Data pemohon direkam'),
	(2, 'Proses verifikasi'),
	(3, 'Data disetujui'),
	(4, 'Data ditolak');
/*!40000 ALTER TABLE `t_status_form` ENABLE KEYS */;


-- Dumping structure for table db_kredit.t_status_skoring
DROP TABLE IF EXISTS `t_status_skoring`;
CREATE TABLE IF NOT EXISTS `t_status_skoring` (
  `status` int(10) DEFAULT NULL,
  `range1` int(10) DEFAULT NULL,
  `range2` int(10) DEFAULT NULL,
  `nmstatus` varchar(255) DEFAULT NULL,
  `warna` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Dumping data for table db_kredit.t_status_skoring: ~3 rows (approximately)
DELETE FROM `t_status_skoring`;
/*!40000 ALTER TABLE `t_status_skoring` DISABLE KEYS */;
INSERT INTO `t_status_skoring` (`status`, `range1`, `range2`, `nmstatus`, `warna`) VALUES
	(1, 0, 60, 'Permohonan Ditolak', '#ee1111'),
	(2, 61, 80, 'Permohonan Dipertimbangkan', '#2d89ef'),
	(3, 81, 100, 'Permohonan Diterima', '#00a300');
/*!40000 ALTER TABLE `t_status_skoring` ENABLE KEYS */;


-- Dumping structure for table db_kredit.t_tipe_kredit
DROP TABLE IF EXISTS `t_tipe_kredit`;
CREATE TABLE IF NOT EXISTS `t_tipe_kredit` (
  `kdtipe` varchar(1) NOT NULL DEFAULT '',
  `nmtipe` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`kdtipe`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Dumping data for table db_kredit.t_tipe_kredit: ~2 rows (approximately)
DELETE FROM `t_tipe_kredit`;
/*!40000 ALTER TABLE `t_tipe_kredit` DISABLE KEYS */;
INSERT INTO `t_tipe_kredit` (`kdtipe`, `nmtipe`) VALUES
	('1', 'Individu'),
	('2', 'Join Income');
/*!40000 ALTER TABLE `t_tipe_kredit` ENABLE KEYS */;


-- Dumping structure for table db_kredit.t_user
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
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;

-- Dumping data for table db_kredit.t_user: ~3 rows (approximately)
DELETE FROM `t_user`;
/*!40000 ALTER TABLE `t_user` DISABLE KEYS */;
INSERT INTO `t_user` (`id`, `username`, `password`, `nama`, `nik`, `alamat`, `email`, `telp`, `foto`, `aktif`, `created_at`, `updated_at`) VALUES
	(1, 'petugas1', '741406c6940752b8ccf1834696338373', 'Eko Firmansyah', '1986070120060210', 'Jakarta', 'test2@jakarta.go.id', '123', 'no-image.png', '1', '2018-08-01 23:43:24', '2018-08-01 23:43:25'),
	(3, 'supervisor', '741406c6940752b8ccf1834696338373', 'Tomy Suhartanto', '0000000000000001', 'Jakarta', 'test1@jakarta.go.id', '123', 'no-image.png', '1', '2018-08-05 09:42:40', NULL),
	(4, 'admin', '741406c6940752b8ccf1834696338373', 'Dzikran Kurniawan', '0000000000000000', 'Jakarta', 'test3@jakarta.go.id', '123', 'no-image.png', '1', '2018-08-05 11:52:54', NULL);
/*!40000 ALTER TABLE `t_user` ENABLE KEYS */;


-- Dumping structure for table db_kredit.t_user_level
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
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;

-- Dumping data for table db_kredit.t_user_level: ~4 rows (approximately)
DELETE FROM `t_user_level`;
/*!40000 ALTER TABLE `t_user_level` DISABLE KEYS */;
INSERT INTO `t_user_level` (`id`, `id_user`, `kdlevel`, `aktif`) VALUES
	(1, 1, '01', '1'),
	(2, 1, '00', '0'),
	(3, 3, '02', '1'),
	(4, 4, '00', '1');
/*!40000 ALTER TABLE `t_user_level` ENABLE KEYS */;


-- Dumping structure for table db_kredit.t_user_petugas
DROP TABLE IF EXISTS `t_user_petugas`;
CREATE TABLE IF NOT EXISTS `t_user_petugas` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `id_user` int(10) NOT NULL,
  `kdpetugas` varchar(2) NOT NULL,
  `aktif` varchar(1) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `Index 2` (`id_user`),
  KEY `FK_t_user_petugas_t_petugas` (`kdpetugas`),
  CONSTRAINT `FK_t_user_petugas_t_petugas` FOREIGN KEY (`kdpetugas`) REFERENCES `t_petugas` (`kdpetugas`),
  CONSTRAINT `FK_t_user_petugas_t_user` FOREIGN KEY (`id_user`) REFERENCES `t_user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

-- Dumping data for table db_kredit.t_user_petugas: ~0 rows (approximately)
DELETE FROM `t_user_petugas`;
/*!40000 ALTER TABLE `t_user_petugas` DISABLE KEYS */;
INSERT INTO `t_user_petugas` (`id`, `id_user`, `kdpetugas`, `aktif`) VALUES
	(1, 1, '01', '1');
/*!40000 ALTER TABLE `t_user_petugas` ENABLE KEYS */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
