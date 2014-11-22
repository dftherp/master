/*
SQLyog Enterprise - MySQL GUI v8.12 
MySQL - 5.5.27 : Database - thinkrbac
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

CREATE DATABASE /*!32312 IF NOT EXISTS*/`thinkrbac` /*!40100 DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci */;

/*Table structure for table `erp_role` */

DROP TABLE IF EXISTS `erp_role`;

CREATE TABLE `erp_role` (
  `roleid` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(30) DEFAULT NULL,
  `pid` tinyint(3) NOT NULL DEFAULT '0',
  `state` tinyint(3) NOT NULL DEFAULT '1',
  `created` int(11) DEFAULT NULL,
  `hiden` tinyint(3) NOT NULL DEFAULT '0',
  `level` tinyint(3) NOT NULL DEFAULT '1',
  PRIMARY KEY (`roleid`),
  KEY `sta` (`hiden`,`level`)
) ENGINE=MyISAM AUTO_INCREMENT=33 DEFAULT CHARSET=utf8;

/*Data for the table `erp_role` */

LOCK TABLES `erp_role` WRITE;

insert  into `erp_role`(`roleid`,`name`,`pid`,`state`,`created`,`hiden`,`level`) values (1,'技术部',0,1,NULL,0,1),(2,'管理员',1,1,NULL,0,2),(3,'财务部',0,1,NULL,0,1),(4,'会计',3,1,NULL,0,2);

UNLOCK TABLES;

/*Table structure for table `erp_role_access` */

DROP TABLE IF EXISTS `erp_role_access`;

CREATE TABLE `erp_role_access` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `roleid` int(8) DEFAULT NULL,
  `nodeid` int(8) DEFAULT NULL,
  `module` int(5) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `role_node` (`roleid`,`nodeid`)
) ENGINE=MyISAM AUTO_INCREMENT=4094 DEFAULT CHARSET=utf8;

/*Data for the table `erp_role_access` */

LOCK TABLES `erp_role_access` WRITE;

insert  into `erp_role_access`(`id`,`roleid`,`nodeid`,`module`) values (1,2,1,NULL),(2,2,2,NULL),(3,4,2,NULL);

UNLOCK TABLES;

/*Table structure for table `erp_role_node` */

DROP TABLE IF EXISTS `erp_role_node`;

CREATE TABLE `erp_role_node` (
  `nodeid` int(11) NOT NULL AUTO_INCREMENT,
  `actions` varchar(30) DEFAULT NULL,
  `args` varchar(100) DEFAULT NULL,
  `pid` int(9) DEFAULT NULL,
  `title` varchar(50) DEFAULT NULL,
  `level` tinyint(3) DEFAULT '1',
  `sort` tinyint(3) DEFAULT '5',
  `created` int(11) DEFAULT NULL,
  `hiden` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`nodeid`),
  KEY `hide` (`hiden`)
) ENGINE=MyISAM AUTO_INCREMENT=172 DEFAULT CHARSET=utf8;

/*Data for the table `erp_role_node` */

LOCK TABLES `erp_role_node` WRITE;

insert  into `erp_role_node`(`nodeid`,`actions`,`args`,`pid`,`title`,`level`,`sort`,`created`,`hiden`) values (1,'public',NULL,0,'共有',1,5,NULL,0),(2,'test',NULL,1,'测试',2,5,NULL,0);

UNLOCK TABLES;

/*Table structure for table `erp_role_user` */

DROP TABLE IF EXISTS `erp_role_user`;

CREATE TABLE `erp_role_user` (
  `roleid` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) DEFAULT NULL,
  PRIMARY KEY (`roleid`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Data for the table `erp_role_user` */

LOCK TABLES `erp_role_user` WRITE;

insert  into `erp_role_user`(`roleid`,`uid`) values (2,1),(4,2);

UNLOCK TABLES;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
