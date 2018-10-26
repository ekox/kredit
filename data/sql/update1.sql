/*
SQLyog Community v12.4.3 (64 bit)
MySQL - 10.1.16-MariaDB : Database - db_kredit
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
/*Table structure for table `d_hunian_dtl_tenor` */

DROP TABLE IF EXISTS `d_hunian_dtl_tenor`;

CREATE TABLE `d_hunian_dtl_tenor` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_hunian_dtl` int(11) NOT NULL,
  `tenor` int(11) DEFAULT NULL,
  `angsuran` decimal(10,0) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id_hunian_dtl` (`id_hunian_dtl`,`tenor`),
  CONSTRAINT `d_hunian_dtl_tenor_ibfk_1` FOREIGN KEY (`id_hunian_dtl`) REFERENCES `d_hunian_dtl` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=latin1;

/*Data for the table `d_hunian_dtl_tenor` */

insert  into `d_hunian_dtl_tenor`(`id`,`id_hunian_dtl`,`tenor`,`angsuran`) values 
(1,1,10,1916494),
(2,1,15,1415555),
(3,1,20,1171531),
(4,2,10,2030571),
(5,2,15,1499814),
(6,2,20,1241265),
(7,3,10,2185716),
(8,3,15,1614406),
(9,3,20,1336103),
(10,4,10,2213094),
(11,4,15,1634629),
(12,4,20,1352839),
(13,5,10,3478437),
(14,5,15,2569232),
(15,5,20,2126328),
(16,6,10,3543689),
(17,6,15,2617428),
(18,6,20,2166216);

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
