/*
SQLyog Ultimate v10.00 Beta1
MySQL - 5.5.5-10.1.9-MariaDB : Database - ci_invoicing
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
CREATE DATABASE /*!32312 IF NOT EXISTS*/`ci_invoicing` /*!40100 DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci */;

USE `ci_invoicing`;

/*Table structure for table `cisession` */

DROP TABLE IF EXISTS `cisession`;

CREATE TABLE `cisession` (
  `id` varchar(40) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `timestamp` int(10) unsigned NOT NULL DEFAULT '0',
  `data` blob NOT NULL,
  KEY `ci_sessions_timestamp` (`timestamp`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `cisession` */

insert  into `cisession`(`id`,`ip_address`,`timestamp`,`data`) values ('0e5da75e8a1e6b2bdb827ede11aebc49523ccaa1','127.0.0.1',1465728592,'__ci_last_regenerate|i:1465728585;identity|s:15:\"admin@admin.com\";email|s:15:\"admin@admin.com\";user_id|s:1:\"1\";old_last_login|s:10:\"1465728075\";first_name|s:5:\"Admin\";last_name|s:5:\"Admin\";is_admin|b:1;'),('f3c1f4e05e5a24ebcbad24d80dfac23b0939812d','82.132.239.242',1465728603,'__ci_last_regenerate|i:1465728603;identity|s:15:\"admin@admin.com\";email|s:15:\"admin@admin.com\";user_id|s:1:\"1\";old_last_login|s:10:\"1465728586\";first_name|s:5:\"Admin\";last_name|s:5:\"Admin\";is_admin|b:1;'),('d912e8098f049ebbfe8b3b2dfee9f676aa19e311','82.132.239.242',1465728604,'__ci_last_regenerate|i:1465728604;');

/*Table structure for table `clients` */

DROP TABLE IF EXISTS `clients`;

CREATE TABLE `clients` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `address1` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `address2` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `city` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `state` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `postcode` varchar(128) COLLATE utf8_unicode_ci DEFAULT NULL,
  `country` varchar(128) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_on` int(11) NOT NULL,
  `updated_on` int(11) NOT NULL,
  `rid` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Data for the table `clients` */

/*Table structure for table `estimates` */

DROP TABLE IF EXISTS `estimates`;

CREATE TABLE `estimates` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `client_id` int(11) NOT NULL,
  `estimate_number` int(11) unsigned NOT NULL,
  `tax_rate` float(8,2) DEFAULT '0.00',
  `estimate_date` int(11) NOT NULL,
  `due_date` int(11) DEFAULT NULL,
  `notes` text COLLATE utf8_unicode_ci,
  `is_invoiced` tinyint(1) NOT NULL DEFAULT '0',
  `created_on` int(11) NOT NULL,
  `updated_on` int(11) NOT NULL,
  `rid` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `client_id` (`client_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Data for the table `estimates` */

/*Table structure for table `estimates_lines` */

DROP TABLE IF EXISTS `estimates_lines`;

CREATE TABLE `estimates_lines` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL,
  `estimate_id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `description` text COLLATE utf8_unicode_ci,
  `rate` float(8,2) DEFAULT NULL,
  `quantity` float(8,2) DEFAULT NULL,
  `created_on` int(11) NOT NULL,
  `updated_on` int(11) NOT NULL,
  `rid` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `invoice_id` (`estimate_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Data for the table `estimates_lines` */

/*Table structure for table `groups` */

DROP TABLE IF EXISTS `groups`;

CREATE TABLE `groups` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL,
  `description` varchar(100) NOT NULL,
  `created_on` int(11) NOT NULL,
  `updated_on` int(11) NOT NULL,
  `rid` varchar(40) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

/*Data for the table `groups` */

insert  into `groups`(`id`,`name`,`description`,`created_on`,`updated_on`,`rid`) values (1,'admin','Administrator',1464600699,1464600699,'998c4ef45cdb8d00ce7bcb306fd3454db6a9e0nj'),(2,'members','General User',1464600699,1464600699,'998c4ef45cdb8d00ce7bcb306fd3454db6a9e0po');

/*Table structure for table `invoices` */

DROP TABLE IF EXISTS `invoices`;

CREATE TABLE `invoices` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `client_id` int(11) NOT NULL,
  `invoice_number` int(11) unsigned NOT NULL,
  `tax_rate` float(8,2) DEFAULT '0.00',
  `invoice_date` int(11) NOT NULL,
  `due_date` int(11) DEFAULT NULL,
  `notes` text COLLATE utf8_unicode_ci,
  `is_paid` tinyint(1) NOT NULL DEFAULT '0',
  `created_on` int(11) NOT NULL,
  `updated_on` int(11) NOT NULL,
  `rid` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `client_id` (`client_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Data for the table `invoices` */

/*Table structure for table `invoices_lines` */

DROP TABLE IF EXISTS `invoices_lines`;

CREATE TABLE `invoices_lines` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL,
  `invoice_id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `description` text COLLATE utf8_unicode_ci,
  `rate` float(8,2) DEFAULT NULL,
  `quantity` float(8,2) DEFAULT NULL,
  `created_on` int(11) NOT NULL,
  `updated_on` int(11) NOT NULL,
  `rid` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `invoice_id` (`invoice_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Data for the table `invoices_lines` */

/*Table structure for table `items` */

DROP TABLE IF EXISTS `items`;

CREATE TABLE `items` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `description` text COLLATE utf8_unicode_ci,
  `rate` float(8,2) DEFAULT '0.00',
  `created_on` int(11) DEFAULT NULL,
  `updated_on` int(11) DEFAULT NULL,
  `rid` varchar(40) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Data for the table `items` */

/*Table structure for table `keys` */

DROP TABLE IF EXISTS `keys`;

CREATE TABLE `keys` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `key` varchar(40) NOT NULL,
  `level` int(2) NOT NULL,
  `ignore_limits` tinyint(1) NOT NULL DEFAULT '0',
  `date_created` int(11) NOT NULL,
  `created_on` int(11) NOT NULL,
  `updated_on` int(11) NOT NULL,
  `rid` varchar(40) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

/*Data for the table `keys` */

insert  into `keys`(`id`,`key`,`level`,`ignore_limits`,`date_created`,`created_on`,`updated_on`,`rid`) values (1,'qnaSMNnufSemGQran211qnaSMNnufSemGQran2kj',0,0,0,1462539739,1462539739,'37d8f8769b605f54c1efedf1fe718cf6a74de81b'),(2,'83fa53be7169cda3ad79005feec7e5c8',2,0,0,1465727137,1465727137,'9330940ca61ffa42b020b7962aafc7ae2d453fdb');

/*Table structure for table `login_attempts` */

DROP TABLE IF EXISTS `login_attempts`;

CREATE TABLE `login_attempts` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `ip_address` varchar(15) NOT NULL,
  `login` varchar(100) NOT NULL,
  `time` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `login_attempts` */

/*Table structure for table `settings` */

DROP TABLE IF EXISTS `settings`;

CREATE TABLE `settings` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL,
  `content` text COLLATE utf8_unicode_ci,
  `created_on` int(11) NOT NULL,
  `updated_on` int(11) NOT NULL,
  `rid` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Data for the table `settings` */

insert  into `settings`(`id`,`user_id`,`content`,`created_on`,`updated_on`,`rid`) values (1,1,'{\"currency_symbol\":\"$\",\"invoice_number\":1,\"estimate_number\":1,\"address\":\"65 California City Blvd|#Unit 4|#Los Angeles|#California|#93505|#USA\"}',1463833195,1465728494,'1fec3605cb980df42be9be89cd835b924e2c3a53');

/*Table structure for table `users` */

DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `ip_address` varchar(45) NOT NULL,
  `username` varchar(100) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `salt` varchar(255) DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `activation_code` varchar(40) DEFAULT NULL,
  `forgotten_password_code` varchar(40) DEFAULT NULL,
  `forgotten_password_time` int(11) unsigned DEFAULT NULL,
  `remember_code` varchar(40) DEFAULT NULL,
  `created_on` int(11) unsigned NOT NULL,
  `updated_on` int(11) unsigned NOT NULL,
  `last_login` int(11) unsigned DEFAULT NULL,
  `active` tinyint(1) unsigned DEFAULT NULL,
  `first_name` varchar(50) DEFAULT NULL,
  `last_name` varchar(50) DEFAULT NULL,
  `company` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `api_key` varchar(32) DEFAULT NULL,
  `rid` varchar(40) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `rid_UNIQUE` (`rid`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

/*Data for the table `users` */

insert  into `users`(`id`,`ip_address`,`username`,`password`,`salt`,`email`,`activation_code`,`forgotten_password_code`,`forgotten_password_time`,`remember_code`,`created_on`,`updated_on`,`last_login`,`active`,`first_name`,`last_name`,`company`,`phone`,`api_key`,`rid`) values (1,'127.0.0.1','administrator','$2y$08$O9vUmpCsSrpF/n3JCP/ia.CGpBYnPYwiP53p5SXRIQe.IB9yUCbEi','','admin@admin.com','',NULL,NULL,NULL,1268889823,1465727406,1465728603,1,'Admin','Admin','ADMIN','0','1','998c4ef45cdb8d00ce7bcb306fd3454db6a9e0ku');

/*Table structure for table `users_groups_rel` */

DROP TABLE IF EXISTS `users_groups_rel`;

CREATE TABLE `users_groups_rel` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL,
  `group_id` mediumint(8) unsigned NOT NULL,
  `created_on` int(11) NOT NULL,
  `updated_on` int(11) NOT NULL,
  `rid` varchar(40) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uc_users_groups` (`user_id`,`group_id`),
  KEY `fk_users_groups_users1_idx` (`user_id`),
  KEY `fk_users_groups_groups1_idx` (`group_id`),
  CONSTRAINT `fk_users_groups_groups1` FOREIGN KEY (`group_id`) REFERENCES `groups` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT `fk_users_groups_users1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

/*Data for the table `users_groups_rel` */

insert  into `users_groups_rel`(`id`,`user_id`,`group_id`,`created_on`,`updated_on`,`rid`) values (1,1,1,1464600647,1465727406,'998c4ef45cdb8d00ce7bcb306fd3454db6a9e0ku');

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
