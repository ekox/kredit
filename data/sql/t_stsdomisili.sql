/*
SQLyog Community v12.4.3 (64 bit)
MySQL - 10.1.37-MariaDB : Database - dp0
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
/*Table structure for table `t_stsdomisili` */

DROP TABLE IF EXISTS `t_stsdomisili`;

CREATE TABLE `t_stsdomisili` (
  `stsdomisili` int(11) NOT NULL,
  `uraian` varchar(255) DEFAULT NULL,
  `nourut` int(11) DEFAULT NULL,
  PRIMARY KEY (`stsdomisili`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `t_stsdomisili` */

insert  into `t_stsdomisili`(`stsdomisili`,`uraian`,`nourut`) values 
(0,'Lainnya',8),
(1,'Rumah Sendiri',1),
(2,'Orang Tua',2),
(3,'Keluarga',3),
(4,'Dinas',4),
(5,'Kontrak',5),
(6,'Sewa',6),
(7,'Tidak Tetap',7);

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
