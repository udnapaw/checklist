/*
SQLyog Ultimate v12.5.1 (64 bit)
MySQL - 10.4.8-MariaDB : Database - checklist
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
CREATE DATABASE /*!32312 IF NOT EXISTS*/`checklist` /*!40100 DEFAULT CHARACTER SET utf8mb4 */;

USE `checklist`;

/*Table structure for table `checklistitems` */

DROP TABLE IF EXISTS `checklistitems`;

CREATE TABLE `checklistitems` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `checklist_id` bigint(20) unsigned NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `due` datetime DEFAULT NULL,
  `urgency` int(11) DEFAULT NULL,
  `is_completed` tinyint(1) NOT NULL,
  `completed_at` datetime DEFAULT NULL,
  `completed_by` bigint(20) unsigned DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `assignee_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `due_interval` int(11) DEFAULT NULL,
  `due_unit` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `checklistitems_created_by_foreign` (`created_by`),
  KEY `checklistitems_updated_by_foreign` (`updated_by`),
  KEY `checklistitems_checklist_id_foreign` (`checklist_id`),
  KEY `checklistitems_completed_by_foreign` (`completed_by`),
  CONSTRAINT `checklistitems_checklist_id_foreign` FOREIGN KEY (`checklist_id`) REFERENCES `checklists` (`id`) ON DELETE CASCADE,
  CONSTRAINT `checklistitems_completed_by_foreign` FOREIGN KEY (`completed_by`) REFERENCES `users` (`id`),
  CONSTRAINT `checklistitems_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`),
  CONSTRAINT `checklistitems_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=42 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `checklistitems` */

insert  into `checklistitems`(`id`,`checklist_id`,`description`,`due`,`urgency`,`is_completed`,`completed_at`,`completed_by`,`deleted_at`,`assignee_id`,`due_interval`,`due_unit`,`created_by`,`updated_by`,`created_at`,`updated_at`) values 
(2,11,'Tes update checklist item2','2020-11-25 16:30:00',2,1,'2020-11-26 22:38:05',NULL,NULL,NULL,NULL,NULL,1,1,'2020-11-26 06:23:15','2020-11-27 21:31:12'),
(9,16,'item1','2020-11-25 16:35:00',NULL,1,'2020-11-26 22:38:05',NULL,NULL,'123',NULL,NULL,1,1,'2020-11-26 11:55:22','2020-11-26 22:38:05'),
(10,16,'item2','2020-11-25 16:25:00',NULL,1,'2020-11-26 22:38:05',NULL,NULL,'123',NULL,NULL,1,1,'2020-11-26 11:55:22','2020-11-26 22:38:05'),
(11,17,'item1','2020-11-25 16:40:00',NULL,1,'2020-11-26 22:38:05',NULL,NULL,'123',NULL,NULL,1,1,'2020-11-26 11:55:24','2020-11-26 22:38:05'),
(12,17,'item2',NULL,NULL,1,'2020-11-26 22:39:04',NULL,NULL,'123',NULL,NULL,1,1,'2020-11-26 11:55:24','2020-11-26 22:39:04'),
(13,18,'item1',NULL,NULL,1,'2020-11-26 22:39:04',NULL,'2020-11-26 16:56:21','123',NULL,NULL,1,1,'2020-11-26 13:05:53','2020-11-26 22:39:04'),
(14,18,'item2',NULL,NULL,1,'2020-11-26 22:39:04',NULL,NULL,'123',NULL,NULL,1,1,'2020-11-26 13:05:53','2020-11-26 22:39:04'),
(15,19,'item1',NULL,NULL,1,'2020-11-26 22:39:04',NULL,NULL,'123',NULL,NULL,1,1,'2020-11-26 13:05:55','2020-11-26 22:39:04'),
(16,19,'item2',NULL,NULL,1,'2020-11-27 21:38:39',1,NULL,NULL,NULL,NULL,1,1,'2020-11-26 13:05:55','2020-11-27 21:38:39'),
(17,20,'item1',NULL,NULL,1,'2020-11-27 21:38:39',1,NULL,NULL,NULL,NULL,1,1,'2020-11-26 13:05:56','2020-11-27 21:38:39'),
(18,20,'item2',NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,'2020-11-26 13:05:56','2020-11-26 13:05:56'),
(19,21,'item1',NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,'2020-11-26 13:05:58','2020-11-26 13:05:58'),
(20,21,'item2',NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,'2020-11-26 13:05:58','2020-11-26 13:05:58'),
(21,22,'Tes update bulk','2020-11-26 22:25:00',2,0,NULL,NULL,NULL,NULL,NULL,NULL,1,1,'2020-11-26 13:05:59','2020-11-26 22:19:55'),
(22,22,'Tes update bulk 2','2020-11-26 22:20:00',2,0,NULL,NULL,NULL,NULL,NULL,NULL,1,1,'2020-11-26 13:05:59','2020-11-26 22:19:55'),
(23,11,'Tes create checklist item','2020-11-25 15:00:00',1,0,NULL,NULL,NULL,'123',NULL,NULL,1,NULL,'2020-11-26 15:02:54','2020-11-26 15:02:54'),
(24,11,'Tes create checklist item2','2020-11-25 15:05:00',2,0,NULL,NULL,NULL,'123',NULL,NULL,1,NULL,'2020-11-26 15:03:40','2020-11-26 15:03:40'),
(25,16,'Tes create checklist item2','2020-11-27 21:00:00',2,0,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,'2020-11-27 21:26:01','2020-11-27 21:26:01'),
(26,23,'item1',NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,'2020-11-27 21:41:44','2020-11-27 21:41:44'),
(27,23,'item2',NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,'2020-11-27 21:41:44','2020-11-27 21:41:44'),
(28,24,'item1',NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,'2020-11-27 21:42:26','2020-11-27 21:42:26'),
(29,24,'item2',NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,'2020-11-27 21:42:26','2020-11-27 21:42:26'),
(34,31,'my foo item','2020-11-28 19:43:13',NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,'2020-11-28 16:03:13','2020-11-28 16:03:13'),
(35,31,'my bar item','2020-11-28 20:13:13',NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,'2020-11-28 16:03:13','2020-11-28 16:03:13'),
(38,33,'my foo item2',NULL,NULL,0,NULL,NULL,NULL,NULL,40,'minute',1,NULL,'2020-11-28 17:18:00','2020-11-28 17:18:00'),
(39,33,'my bar item2',NULL,NULL,0,NULL,NULL,NULL,NULL,30,'minute',1,NULL,'2020-11-28 17:18:00','2020-11-28 17:18:00'),
(40,34,'my foo item2',NULL,NULL,0,NULL,NULL,NULL,NULL,40,'minute',1,NULL,'2020-11-28 18:02:55','2020-11-28 18:02:55'),
(41,34,'my bar item2',NULL,NULL,0,NULL,NULL,NULL,NULL,30,'minute',1,NULL,'2020-11-28 18:02:55','2020-11-28 18:02:55');

/*Table structure for table `checklists` */

DROP TABLE IF EXISTS `checklists`;

CREATE TABLE `checklists` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `due` datetime DEFAULT NULL,
  `urgency` int(11) DEFAULT NULL,
  `object_id` int(11) DEFAULT NULL,
  `object_domain` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_completed` tinyint(1) NOT NULL,
  `completed_at` datetime DEFAULT NULL,
  `task_id` int(11) DEFAULT NULL,
  `due_interval` int(11) DEFAULT NULL,
  `due_unit` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `checklists_created_by_foreign` (`created_by`),
  KEY `checklists_updated_by_foreign` (`updated_by`),
  CONSTRAINT `checklists_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`),
  CONSTRAINT `checklists_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=35 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `checklists` */

insert  into `checklists`(`id`,`description`,`due`,`urgency`,`object_id`,`object_domain`,`is_completed`,`completed_at`,`task_id`,`due_interval`,`due_unit`,`created_by`,`updated_by`,`created_at`,`updated_at`) values 
(11,'Need to verify this guy house.','2020-11-25 22:00:00',2,3,'contract',0,NULL,1,NULL,NULL,1,1,'2020-11-26 06:23:15','2020-11-26 16:20:46'),
(16,'testing4','2020-11-26 03:00:00',1,4,'contract',0,NULL,NULL,NULL,NULL,1,NULL,'2020-11-26 11:55:22','2020-11-26 11:55:22'),
(17,'testing4','2020-11-26 03:00:00',1,4,'contract',0,NULL,NULL,NULL,NULL,1,NULL,'2020-11-26 11:55:24','2020-11-26 11:55:24'),
(18,'testing4','2020-11-26 03:00:00',1,4,'contract',0,NULL,NULL,NULL,NULL,1,NULL,'2020-11-26 13:05:53','2020-11-26 13:05:53'),
(19,'testing4','2020-11-26 03:00:00',1,4,'contract',0,NULL,NULL,NULL,NULL,1,NULL,'2020-11-26 13:05:54','2020-11-26 13:05:54'),
(20,'testing4','2020-11-26 03:00:00',1,4,'contract',0,NULL,NULL,NULL,NULL,1,NULL,'2020-11-26 13:05:56','2020-11-26 13:05:56'),
(21,'testing4','2020-11-26 03:00:00',1,4,'contract',0,NULL,NULL,NULL,NULL,1,NULL,'2020-11-26 13:05:58','2020-11-26 13:05:58'),
(22,'testing4','2020-11-26 03:00:00',1,4,'contract',0,NULL,NULL,NULL,NULL,1,NULL,'2020-11-26 13:05:59','2020-11-26 13:05:59'),
(23,'testing4','2020-11-27 21:41:00',1,5,'contract',0,NULL,NULL,NULL,NULL,1,NULL,'2020-11-27 21:41:44','2020-11-27 21:41:44'),
(24,'testing4','2020-11-27 21:41:00',1,5,'contract',0,NULL,NULL,NULL,NULL,1,NULL,'2020-11-27 21:42:26','2020-11-27 21:42:26'),
(31,'my checklist','2020-11-28 19:03:13',NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,1,NULL,'2020-11-28 16:03:13','2020-11-28 16:03:13'),
(33,'my checklist2',NULL,NULL,NULL,NULL,0,NULL,NULL,3,'hour',1,NULL,'2020-11-28 17:18:00','2020-11-28 17:18:00'),
(34,'my checklist2',NULL,NULL,NULL,NULL,0,NULL,NULL,3,'hour',1,NULL,'2020-11-28 18:02:55','2020-11-28 18:02:55');

/*Table structure for table `histories` */

DROP TABLE IF EXISTS `histories`;

CREATE TABLE `histories` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `loggable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `loggable_id` bigint(20) NOT NULL,
  `action` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `kwuid` int(11) DEFAULT NULL,
  `value` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `histories_created_by_foreign` (`created_by`),
  KEY `histories_updated_by_foreign` (`updated_by`),
  CONSTRAINT `histories_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`),
  CONSTRAINT `histories_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=61 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `histories` */

insert  into `histories`(`id`,`loggable_type`,`loggable_id`,`action`,`kwuid`,`value`,`created_by`,`updated_by`,`created_at`,`updated_at`) values 
(2,'checklist',11,'create',NULL,'{\"object_domain\":\"contract\",\"object_id\":1,\"description\":\"testing\",\"due\":\"2020-11-25 21:00:00\",\"urgency\":1,\"items\":[\"Testing item\"]}',1,NULL,'2020-11-26 06:23:15','2020-11-26 06:23:15'),
(3,'checklist',12,'create',NULL,'{\"object_domain\":\"contract\",\"object_id\":2,\"description\":\"testing2\",\"due\":\"2020-11-25 22:00:00\",\"urgency\":2}',1,NULL,'2020-11-26 06:28:49','2020-11-26 06:28:49'),
(4,'checklists',11,'update',NULL,'{\"data\":{\"type\":\"checklists\",\"id\":11,\"attributes\":{\"object_domain\":\"contract\",\"object_id\":\"1\",\"description\":\"Need to verify this guy house.\",\"due\":\"2020-11-25 22:00:00\",\"urgency\":2,\"task_id\":1,\"is_completed\":false,\"completed_at\":null,\"created_at\":\"2018-01-25T07:50:14+00:00\"},\"links\":{\"self\":\"https:\\/\\/dev-kong.command-api.kw.com\\/checklists\\/50127\"}}}',1,NULL,'2020-11-26 08:16:46','2020-11-26 08:16:46'),
(5,'checklists',11,'update',NULL,'{\"data\":{\"type\":\"checklists\",\"id\":11,\"attributes\":{\"object_domain\":\"contract\",\"object_id\":\"2\",\"description\":\"Need to verify this guy house.\",\"due\":\"2020-11-25 22:00:00\",\"urgency\":2,\"task_id\":1,\"is_completed\":false,\"completed_at\":null,\"created_at\":\"2018-01-25T07:50:14+00:00\"},\"links\":{\"self\":\"https:\\/\\/dev-kong.command-api.kw.com\\/checklists\\/50127\"}}}',1,NULL,'2020-11-26 01:18:03','2020-11-26 01:18:03'),
(6,'checklist',13,'create',NULL,'{\"object_domain\":\"contract\",\"object_id\":4,\"description\":\"testing4\",\"due\":\"2020-11-26 03:00:00\",\"urgency\":1,\"items\":[\"item1\",\"item2\"]}',1,NULL,'2020-11-26 01:29:51','2020-11-26 01:29:51'),
(7,'checklist',14,'create',NULL,'{\"object_domain\":\"contract\",\"object_id\":4,\"description\":\"testing4\",\"due\":\"2020-11-26 03:00:00\",\"urgency\":1,\"items\":[\"item1\",\"item2\"]}',1,NULL,'2020-11-26 01:35:15','2020-11-26 01:35:15'),
(8,'checklist',15,'create',NULL,'{\"object_domain\":\"contract\",\"object_id\":4,\"description\":\"testing4\",\"due\":\"2020-11-26 03:00:00\",\"urgency\":1,\"items\":[\"item1\",\"item2\"]}',1,NULL,'2020-11-26 01:41:44','2020-11-26 01:41:44'),
(9,'checklists',15,'delete',NULL,'{\"id\":15,\"description\":\"testing4\",\"due\":\"2020-11-26 03:00:00\",\"urgency\":1,\"object_id\":4,\"object_domain\":\"contract\",\"is_completed\":0,\"completed_at\":null,\"task_id\":null,\"created_by\":1,\"updated_by\":null,\"created_at\":\"2020-11-25T18:41:44.000000Z\",\"updated_at\":\"2020-11-25T18:41:44.000000Z\"}',1,NULL,'2020-11-26 01:44:21','2020-11-26 01:44:21'),
(10,'checklists',16,'create',NULL,'{\"object_domain\":\"contract\",\"object_id\":4,\"description\":\"testing4\",\"due\":\"2020-11-26 03:00:00\",\"urgency\":1,\"items\":[\"item1\",\"item2\"]}',1,NULL,'2020-11-26 11:55:22','2020-11-26 11:55:22'),
(11,'checklists',17,'create',NULL,'{\"object_domain\":\"contract\",\"object_id\":4,\"description\":\"testing4\",\"due\":\"2020-11-26 03:00:00\",\"urgency\":1,\"items\":[\"item1\",\"item2\"]}',1,NULL,'2020-11-26 11:55:24','2020-11-26 11:55:24'),
(12,'checklists',18,'create',NULL,'{\"object_domain\":\"contract\",\"object_id\":4,\"description\":\"testing4\",\"due\":\"2020-11-26 03:00:00\",\"urgency\":1,\"items\":[\"item1\",\"item2\"]}',1,NULL,'2020-11-26 13:05:53','2020-11-26 13:05:53'),
(13,'checklists',19,'create',NULL,'{\"object_domain\":\"contract\",\"object_id\":4,\"description\":\"testing4\",\"due\":\"2020-11-26 03:00:00\",\"urgency\":1,\"items\":[\"item1\",\"item2\"]}',1,NULL,'2020-11-26 13:05:55','2020-11-26 13:05:55'),
(14,'checklists',20,'create',NULL,'{\"object_domain\":\"contract\",\"object_id\":4,\"description\":\"testing4\",\"due\":\"2020-11-26 03:00:00\",\"urgency\":1,\"items\":[\"item1\",\"item2\"]}',1,NULL,'2020-11-26 13:05:56','2020-11-26 13:05:56'),
(15,'checklists',21,'create',NULL,'{\"object_domain\":\"contract\",\"object_id\":4,\"description\":\"testing4\",\"due\":\"2020-11-26 03:00:00\",\"urgency\":1,\"items\":[\"item1\",\"item2\"]}',1,NULL,'2020-11-26 13:05:58','2020-11-26 13:05:58'),
(16,'checklists',22,'create',NULL,'{\"object_domain\":\"contract\",\"object_id\":4,\"description\":\"testing4\",\"due\":\"2020-11-26 03:00:00\",\"urgency\":1,\"items\":[\"item1\",\"item2\"]}',1,NULL,'2020-11-26 13:05:59','2020-11-26 13:05:59'),
(17,'items',11,'create',NULL,'{\"description\":\"Tes create checklist item\",\"due\":\"2020-11-25 15:00:00\",\"urgency\":1,\"asignee_id\":\"123\"}',1,NULL,'2020-11-26 15:02:54','2020-11-26 15:02:54'),
(18,'items',11,'create',NULL,'{\"description\":\"Tes create checklist item2\",\"due\":\"2020-11-25 15:05:00\",\"urgency\":2,\"asignee_id\":\"12\"}',1,NULL,'2020-11-26 15:03:40','2020-11-26 15:03:40'),
(19,'checklists',11,'update',NULL,'{\"data\":{\"type\":\"checklists\",\"id\":11,\"attributes\":{\"object_domain\":\"contract\",\"object_id\":\"2\",\"description\":\"Need to verify this guy house.\",\"due\":\"2020-11-25 22:00:00\",\"urgency\":2,\"task_id\":1,\"is_completed\":false,\"completed_at\":null,\"created_at\":\"2018-01-25T07:50:14+00:00\"},\"links\":{\"self\":\"https:\\/\\/dev-kong.command-api.kw.com\\/checklists\\/50127\"}}}',1,NULL,'2020-11-26 16:19:40','2020-11-26 16:19:40'),
(20,'checklists',11,'update',NULL,'{\"data\":{\"type\":\"checklists\",\"id\":11,\"attributes\":{\"object_domain\":\"contract\",\"object_id\":\"3\",\"description\":\"Need to verify this guy house.\",\"due\":\"2020-11-25 22:00:00\",\"urgency\":2,\"task_id\":1,\"is_completed\":false,\"completed_at\":null,\"created_at\":\"2018-01-25T07:50:14+00:00\"},\"links\":{\"self\":\"https:\\/\\/dev-kong.command-api.kw.com\\/checklists\\/50127\"}}}',1,NULL,'2020-11-26 16:20:46','2020-11-26 16:20:46'),
(21,'items',2,'update',NULL,'{\"description\":\"Tes update checklist item2\",\"due\":\"2020-11-25 16:30:00\",\"urgency\":2,\"asignee_id\":\"12\"}',1,NULL,'2020-11-26 16:30:58','2020-11-26 16:30:58'),
(22,'items',13,'delete',NULL,'\"18-13\"',1,NULL,'2020-11-26 16:56:21','2020-11-26 16:56:21'),
(23,'items',64,'update',NULL,'{\"id\":\"64\",\"action\":\"update\",\"attributes\":{\"description\":\"\",\"due\":\"2019-01-19 18:34\\/51\",\"urgency\":\"2\"}}',1,NULL,'2020-11-26 22:14:19','2020-11-26 22:14:19'),
(24,'items',205,'update',NULL,'{\"id\":\"205\",\"action\":\"update\",\"attributes\":{\"description\":\"{{data.attributes.description}}\",\"due\":\"2019-01-19 18:34:51\",\"urgency\":\"2\"}}',1,NULL,'2020-11-26 22:14:19','2020-11-26 22:14:19'),
(25,'items',21,'update',NULL,'{\"id\":\"21\",\"action\":\"update\",\"attributes\":{\"description\":\"Tes update bulk\",\"due\":\"2020-11-26 22:20:00\",\"urgency\":\"2\"}}',1,NULL,'2020-11-26 22:16:55','2020-11-26 22:16:55'),
(26,'items',22,'update',NULL,'{\"id\":\"22\",\"action\":\"update\",\"attributes\":{\"description\":\"Tes update bulk 2\",\"due\":\"2020-11-26 22:20:00\",\"urgency\":\"2\"}}',1,NULL,'2020-11-26 22:16:56','2020-11-26 22:16:56'),
(27,'items',21,'update',NULL,'{\"id\":\"21\",\"action\":\"update\",\"attributes\":{\"description\":\"Tes update bulk\",\"due\":\"2020-11-26 22:20:00\",\"urgency\":\"2\"}}',1,NULL,'2020-11-26 22:19:55','2020-11-26 22:19:55'),
(28,'items',22,'update',NULL,'{\"id\":\"22\",\"action\":\"update\",\"attributes\":{\"description\":\"Tes update bulk 2\",\"due\":\"2020-11-26 22:20:00\",\"urgency\":\"2\"}}',1,NULL,'2020-11-26 22:19:55','2020-11-26 22:19:55'),
(29,'items',2,'completed',NULL,'{\"data\":[{\"item_id\":2},{\"item_id\":9},{\"item_id\":10},{\"item_id\":11}]}',1,NULL,'2020-11-26 22:38:05','2020-11-26 22:38:05'),
(30,'items',9,'completed',NULL,'{\"data\":[{\"item_id\":2},{\"item_id\":9},{\"item_id\":10},{\"item_id\":11}]}',1,NULL,'2020-11-26 22:38:05','2020-11-26 22:38:05'),
(31,'items',10,'completed',NULL,'{\"data\":[{\"item_id\":2},{\"item_id\":9},{\"item_id\":10},{\"item_id\":11}]}',1,NULL,'2020-11-26 22:38:05','2020-11-26 22:38:05'),
(32,'items',11,'completed',NULL,'{\"data\":[{\"item_id\":2},{\"item_id\":9},{\"item_id\":10},{\"item_id\":11}]}',1,NULL,'2020-11-26 22:38:05','2020-11-26 22:38:05'),
(33,'items',12,'completed',NULL,'{\"data\":[{\"item_id\":12},{\"item_id\":13},{\"item_id\":14},{\"item_id\":15}]}',1,NULL,'2020-11-26 22:39:04','2020-11-26 22:39:04'),
(34,'items',13,'completed',NULL,'{\"data\":[{\"item_id\":12},{\"item_id\":13},{\"item_id\":14},{\"item_id\":15}]}',1,NULL,'2020-11-26 22:39:04','2020-11-26 22:39:04'),
(35,'items',14,'completed',NULL,'{\"data\":[{\"item_id\":12},{\"item_id\":13},{\"item_id\":14},{\"item_id\":15}]}',1,NULL,'2020-11-26 22:39:04','2020-11-26 22:39:04'),
(36,'items',15,'completed',NULL,'{\"data\":[{\"item_id\":12},{\"item_id\":13},{\"item_id\":14},{\"item_id\":15}]}',1,NULL,'2020-11-26 22:39:04','2020-11-26 22:39:04'),
(37,'items',16,'completed',NULL,'{\"data\":[{\"item_id\":16},{\"item_id\":17}]}',1,NULL,'2020-11-26 22:39:39','2020-11-26 22:39:39'),
(38,'items',17,'completed',NULL,'{\"data\":[{\"item_id\":16},{\"item_id\":17}]}',1,NULL,'2020-11-26 22:39:40','2020-11-26 22:39:40'),
(39,'items',16,'completed',NULL,'{\"data\":[{\"item_id\":16},{\"item_id\":17}]}',1,NULL,'2020-11-26 22:40:41','2020-11-26 22:40:41'),
(40,'items',17,'completed',NULL,'{\"data\":[{\"item_id\":16},{\"item_id\":17}]}',1,NULL,'2020-11-26 22:40:41','2020-11-26 22:40:41'),
(41,'items',16,'incompleted',NULL,'{\"data\":[{\"item_id\":16},{\"item_id\":17}]}',1,NULL,'2020-11-26 22:43:01','2020-11-26 22:43:01'),
(42,'items',17,'incompleted',NULL,'{\"data\":[{\"item_id\":16},{\"item_id\":17}]}',1,NULL,'2020-11-26 22:43:01','2020-11-26 22:43:01'),
(43,'items',25,'create',NULL,'{\"description\":\"Tes create checklist item2\",\"due\":\"2020-11-27 21:00:00\",\"urgency\":2,\"asignee_id\":\"12\"}',1,NULL,'2020-11-27 21:26:01','2020-11-27 21:26:01'),
(44,'items',2,'update',NULL,'{\"description\":\"Tes update checklist item2\",\"due\":\"2020-11-25 16:30:00\",\"urgency\":2,\"asignee_id\":\"12\"}',1,NULL,'2020-11-27 21:31:12','2020-11-27 21:31:12'),
(45,'items',2,'update',NULL,'{\"description\":\"Tes update checklist item2\",\"due\":\"2020-11-25 16:30:00\",\"urgency\":2,\"asignee_id\":\"12\"}',1,NULL,'2020-11-27 21:38:07','2020-11-27 21:38:07'),
(46,'items',16,'completed',NULL,'{\"data\":[{\"item_id\":16},{\"item_id\":17}]}',1,NULL,'2020-11-27 21:38:39','2020-11-27 21:38:39'),
(47,'items',17,'completed',NULL,'{\"data\":[{\"item_id\":16},{\"item_id\":17}]}',1,NULL,'2020-11-27 21:38:39','2020-11-27 21:38:39'),
(48,'checklists',23,'create',NULL,'{\"object_domain\":\"contract\",\"object_id\":5,\"description\":\"testing4\",\"due\":\"2020-11-27 21:41:00\",\"urgency\":1,\"items\":[\"item1\",\"item2\"]}',1,NULL,'2020-11-27 21:41:44','2020-11-27 21:41:44'),
(49,'checklists',24,'create',NULL,'{\"object_domain\":\"contract\",\"object_id\":5,\"description\":\"testing4\",\"due\":\"2020-11-27 21:41:00\",\"urgency\":1,\"items\":[\"item1\",\"item2\"]}',1,NULL,'2020-11-27 21:42:26','2020-11-27 21:42:26'),
(50,'checklists',11,'update',NULL,'{\"data\":{\"type\":\"checklists\",\"id\":11,\"attributes\":{\"object_domain\":\"contract\",\"object_id\":\"3\",\"description\":\"Need to verify this guy house.\",\"due\":\"2020-11-25 22:00:00\",\"urgency\":2,\"task_id\":1,\"is_completed\":false,\"completed_at\":null,\"created_at\":\"2018-01-25T07:50:14+00:00\"},\"links\":{\"self\":\"https:\\/\\/dev-kong.command-api.kw.com\\/checklists\\/50127\"}}}',1,NULL,'2020-11-27 21:43:45','2020-11-27 21:43:45'),
(53,'template',3,'create',NULL,'{\"data\":{\"attributes\":{\"name\":\"foo template\",\"checklist\":{\"description\":\"my checklist\",\"due_interval\":3,\"due_unit\":\"hour\"},\"items\":[{\"description\":\"my foo item\",\"urgency\":2,\"due_interval\":40,\"due_unit\":\"minute\"},{\"description\":\"my bar item\",\"urgency\":3,\"due_interval\":30,\"due_unit\":\"minute\"}]}}}',1,NULL,'2020-11-28 16:03:13','2020-11-28 16:03:13'),
(54,'template',4,'create',NULL,'{\"data\":{\"attributes\":{\"name\":\"foo template2\",\"checklist\":{\"description\":\"my checklist2\",\"due_interval\":3,\"due_unit\":\"hour\"},\"items\":[{\"description\":\"my foo item2\",\"urgency\":2,\"due_interval\":40,\"due_unit\":\"minute\"},{\"description\":\"my bar item2\",\"urgency\":3,\"due_interval\":30,\"due_unit\":\"minute\"}]}}}',1,NULL,'2020-11-28 17:11:36','2020-11-28 17:11:36'),
(55,'template',5,'create',NULL,'{\"data\":{\"attributes\":{\"name\":\"foo template2\",\"checklist\":{\"description\":\"my checklist2\",\"due_interval\":3,\"due_unit\":\"hour\"},\"items\":[{\"description\":\"my foo item2\",\"urgency\":2,\"due_interval\":40,\"due_unit\":\"minute\"},{\"description\":\"my bar item2\",\"urgency\":3,\"due_interval\":30,\"due_unit\":\"minute\"}]}}}',1,NULL,'2020-11-28 17:18:00','2020-11-28 17:18:00'),
(56,'templates',4,'update',NULL,'{\"data\":{\"attributes\":{\"name\":\"foo template3\",\"checklist\":{\"description\":\"my checklist3\",\"due_interval\":3,\"due_unit\":\"hour\"},\"items\":[{\"description\":\"my foo item3\",\"urgency\":2,\"due_interval\":40,\"due_unit\":\"minute\"},{\"description\":\"my bar item3\",\"urgency\":3,\"due_interval\":30,\"due_unit\":\"minute\"}]}}}',1,NULL,'2020-11-28 17:42:07','2020-11-28 17:42:07'),
(57,'templates',4,'update',NULL,'{\"data\":{\"attributes\":{\"name\":\"foo template3\",\"checklist\":{\"description\":\"my checklist3\",\"due_interval\":3,\"due_unit\":\"hour\"},\"items\":[{\"description\":\"my foo item3\",\"urgency\":2,\"due_interval\":40,\"due_unit\":\"minute\"},{\"description\":\"my bar item3\",\"urgency\":3,\"due_interval\":30,\"due_unit\":\"minute\"}]}}}',1,NULL,'2020-11-28 17:45:15','2020-11-28 17:45:15'),
(58,'templates',4,'update',NULL,'{\"data\":{\"attributes\":{\"name\":\"foo template3\",\"checklist\":{\"description\":\"my checklist3\",\"due_interval\":3,\"due_unit\":\"hour\"},\"items\":[{\"description\":\"my foo item3\",\"urgency\":2,\"due_interval\":40,\"due_unit\":\"minute\"},{\"description\":\"my bar item3\",\"urgency\":3,\"due_interval\":30,\"due_unit\":\"minute\"}]}}}',1,NULL,'2020-11-28 17:46:36','2020-11-28 17:46:36'),
(59,'templates',4,'delete',NULL,'\"4\"',1,NULL,'2020-11-28 17:53:23','2020-11-28 17:53:23'),
(60,'template',6,'create',NULL,'{\"data\":{\"attributes\":{\"name\":\"foo template2\",\"checklist\":{\"description\":\"my checklist2\",\"due_interval\":3,\"due_unit\":\"hour\"},\"items\":[{\"description\":\"my foo item2\",\"urgency\":2,\"due_interval\":40,\"due_unit\":\"minute\"},{\"description\":\"my bar item2\",\"urgency\":3,\"due_interval\":30,\"due_unit\":\"minute\"}]}}}',1,NULL,'2020-11-28 18:02:55','2020-11-28 18:02:55');

/*Table structure for table `migrations` */

DROP TABLE IF EXISTS `migrations`;

CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `migrations` */

insert  into `migrations`(`id`,`migration`,`batch`) values 
(1,'2020_11_22_181204_create_users_table',1),
(2,'2020_11_25_005714_create_checklists_table',1),
(3,'2020_11_25_005740_create_templates_table',1),
(4,'2020_11_25_211449_create_checklistitems_table',1),
(5,'2020_11_25_211838_create_histories_table',1),
(6,'2020_11_28_211508_create_objectdomains_table',2);

/*Table structure for table `templates` */

DROP TABLE IF EXISTS `templates`;

CREATE TABLE `templates` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `checklist_id` bigint(20) unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `templates_created_by_foreign` (`created_by`),
  KEY `templates_updated_by_foreign` (`updated_by`),
  KEY `templates_checklist_id_foreign` (`checklist_id`),
  CONSTRAINT `templates_checklist_id_foreign` FOREIGN KEY (`checklist_id`) REFERENCES `checklists` (`id`) ON DELETE CASCADE,
  CONSTRAINT `templates_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`),
  CONSTRAINT `templates_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `templates` */

insert  into `templates`(`id`,`checklist_id`,`name`,`created_by`,`updated_by`,`created_at`,`updated_at`) values 
(6,34,'foo template2',1,NULL,'2020-11-28 18:02:55','2020-11-28 18:02:55');

/*Table structure for table `users` */

DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `users` */

insert  into `users`(`id`,`name`,`email`,`email_verified_at`,`password`,`remember_token`,`created_at`,`updated_at`) values 
(1,'administrator','admin@admin.com',NULL,'$2y$10$ZXNaq3nbm.KZYfNX.0avFejroHBkosLr85r0IV8H0a40bKB13Pfr2',NULL,'2020-11-26 04:20:37','2020-11-26 04:20:37');

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
