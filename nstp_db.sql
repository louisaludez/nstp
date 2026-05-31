-- MySQL dump 10.13  Distrib 8.0.46, for Win64 (x86_64)
--
-- Host: localhost    Database: nstp_db
-- ------------------------------------------------------
-- Server version	8.0.46

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `accomplishment_reports`
--

DROP TABLE IF EXISTS `accomplishment_reports`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `accomplishment_reports` (
  `id` int NOT NULL AUTO_INCREMENT,
  `instructor_id` int NOT NULL,
  `section_id` int DEFAULT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `location` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `completed_date` date DEFAULT NULL,
  `participants_count` int DEFAULT '0',
  `accomplishments` text COLLATE utf8mb4_unicode_ci,
  `files_attached` int DEFAULT '0',
  `status` enum('Draft','Pending','Reviewed','Revision') COLLATE utf8mb4_unicode_ci DEFAULT 'Pending',
  `submitted_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `accomplishment_reports`
--

LOCK TABLES `accomplishment_reports` WRITE;
/*!40000 ALTER TABLE `accomplishment_reports` DISABLE KEYS */;
INSERT INTO `accomplishment_reports` VALUES (1,5,2,'Testing Report','TBA','2026-05-23',42,'good',0,'Reviewed','2026-05-21 03:36:07'),(2,5,2,'Testing Report','TBA','2026-05-23',42,'owa',0,'Pending','2026-05-21 03:37:10'),(3,5,2,'Testing Report','TBA','2026-05-23',42,'gfhfgh',0,'Pending','2026-05-21 03:38:23'),(4,5,2,'Testing Report','TBA','2026-05-23',42,'wa',0,'Pending','2026-05-21 03:39:21');
/*!40000 ALTER TABLE `accomplishment_reports` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `activities`
--

DROP TABLE IF EXISTS `activities`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `activities` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `component` enum('CWTS','LTS','ROTC','All Programs') COLLATE utf8mb4_unicode_ci NOT NULL,
  `activity_date` date NOT NULL,
  `activity_time` time NOT NULL,
  `location` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `activities`
--

LOCK TABLES `activities` WRITE;
/*!40000 ALTER TABLE `activities` DISABLE KEYS */;
INSERT INTO `activities` VALUES (1,'smaw','LTS','2026-04-09','09:46:00','Gym','basta test','2026-04-08 01:45:49'),(2,'o;pl\'[p','CWTS','2026-04-13','14:53:00','Gym','uyiuoiu','2026-04-13 06:53:06');
/*!40000 ALTER TABLE `activities` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `activity_plans`
--

DROP TABLE IF EXISTS `activity_plans`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `activity_plans` (
  `id` int NOT NULL AUTO_INCREMENT,
  `instructor_id` int NOT NULL,
  `section_id` int DEFAULT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `location` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `scheduled_date` date DEFAULT NULL,
  `scheduled_time` time DEFAULT NULL,
  `objectives` text COLLATE utf8mb4_unicode_ci,
  `files_attached` int DEFAULT '0',
  `status` enum('Draft','Pending','Approved','Rejected') COLLATE utf8mb4_unicode_ci DEFAULT 'Pending',
  `submitted_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `activity_plans`
--

LOCK TABLES `activity_plans` WRITE;
/*!40000 ALTER TABLE `activity_plans` DISABLE KEYS */;
INSERT INTO `activity_plans` VALUES (1,10,NULL,'te','tyr','dyfd','2026-04-15','10:29:00','y',1,'Approved','2026-04-13 02:31:39'),(3,10,NULL,'saf','dfs','dfgdf','2026-04-16','10:37:00','sddfgd',1,'Approved','2026-04-13 02:35:10'),(4,5,2,'Testing','Created via Quick Template','TBA','2026-05-23',NULL,'223',0,'Approved','2026-05-21 03:32:41'),(5,5,2,'efef','Created via Quick Template','TBA','2026-05-22',NULL,'efre',0,'Pending','2026-05-21 03:33:19'),(6,5,2,'fdgfdhg','Created via Quick Template','TBA','2026-05-23',NULL,'fghfgh',0,'Pending','2026-05-21 03:38:17');
/*!40000 ALTER TABLE `activity_plans` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `announcements`
--

DROP TABLE IF EXISTS `announcements`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `announcements` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `content` text COLLATE utf8mb4_general_ci NOT NULL,
  `source` varchar(100) COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'NSTP Office',
  `is_pinned` tinyint(1) DEFAULT '0',
  `target_role` varchar(50) COLLATE utf8mb4_general_ci DEFAULT 'All',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `announcements`
--

LOCK TABLES `announcements` WRITE;
/*!40000 ALTER TABLE `announcements` DISABLE KEYS */;
INSERT INTO `announcements` VALUES (1,'Mid-semester accomplishment reports due May 22','All instructors must submit consolidated accomplishment reports by 11:59 PM.','NSTP Office',1,'All','2026-05-21 01:20:45'),(2,'Field activity safety briefing — mandatory','Briefing scheduled Friday, May 16, 3:00 PM at the AVR.','Dean\'s Office',0,'All','2026-05-20 03:20:45'),(3,'Updated rubric for accomplishment reports','Rubric v3.2 is now in effect.','Program Coordinator',0,'Instructor','2026-05-09 03:20:45'),(4,'Grade encoding window opens May 25','OCR-assisted grade upload available end of month.','Registrar',0,'All','2026-05-08 03:20:45');
/*!40000 ALTER TABLE `announcements` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `attendance`
--

DROP TABLE IF EXISTS `attendance`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `attendance` (
  `id` int NOT NULL AUTO_INCREMENT,
  `student_id` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `section_id` int NOT NULL,
  `date` date NOT NULL,
  `status` enum('Present','Absent','Late') COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `attendance`
--

LOCK TABLES `attendance` WRITE;
/*!40000 ALTER TABLE `attendance` DISABLE KEYS */;
/*!40000 ALTER TABLE `attendance` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `audit_logs`
--

DROP TABLE IF EXISTS `audit_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `audit_logs` (
  `id` int NOT NULL AUTO_INCREMENT,
  `action_type` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `details` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `audit_logs`
--

LOCK TABLES `audit_logs` WRITE;
/*!40000 ALTER TABLE `audit_logs` DISABLE KEYS */;
INSERT INTO `audit_logs` VALUES (1,'Created Student','Coordinator Maria','Added student Juan Dela Cruz (2021-00001)','2026-04-07 14:32:15'),(2,'Updated Grade','Instructor Santos','Changed grade for Maria Santos from 90 to 92','2026-04-07 14:15:22'),(3,'Assigned Instructor','Coordinator Maria','Assigned Prof. Garcia to CWTS-A section','2026-04-07 13:45:10'),(4,'Generated Certificate','System','Certificate NSTP-2026-0987 for Pedro Reyes','2026-04-07 12:30:05'),(5,'Created Student','Razul Atowan','Enrolled sdsfs  (345435) under BSIT','2026-04-13 08:43:58'),(6,'Created Instructor','Razul Atowan','Created account for Prof. Dane Macarine (CWTS)','2026-04-13 08:48:40'),(7,'Deleted Student','Razul Atowan','Deleted student with ID (34325345)','2026-04-13 09:04:08'),(8,'Deleted Student','Razul Atowan','Deleted student with ID (345435)','2026-04-13 09:04:11'),(9,'Deleted Student','Razul Atowan','Deleted student with ID (2022-00764)','2026-04-13 09:04:18'),(10,'Created Student','Razul Atowan','Enrolled SALUDEZ JOHN LOUI (12121) under BSIT','2026-04-13 09:04:28'),(11,'Assigned Instructor','Razul Atowan','Assigned Prof. Dane Macarine to section CWTS-A','2026-04-13 12:56:13'),(12,'Deleted Student','Razul Atowan','Deleted student with ID (12121)','2026-04-13 13:15:58'),(13,'Created Student','Razul Atowan','Enrolled tfhgh  (2022-0675) under ghjghj','2026-04-13 13:16:10'),(14,'Assigned Instructor','Razul Atowan','Assigned Prof. Dane Macarine to section ROTC-A','2026-04-13 13:22:03'),(15,'Assigned Instructor','Razul Atowan','Assigned Prof. Dane Macarine to section ROTC-A','2026-04-13 13:22:41'),(16,'Created Student','Razul Atowan','Enrolled sdsdsdsds  (32534534) under ssdsd','2026-04-13 13:24:40'),(17,'Enrolled Student','Razul Atowan','Enrolled student (32534534) into section ID 1','2026-04-13 13:26:31'),(18,'Enrolled Student','Razul Atowan','Enrolled student (2022-0675) into section ID 8','2026-04-13 13:28:35'),(19,'Created Student','Razul Atowan','Enrolled rfthnygkuijl  (756767) under yuyi','2026-04-14 08:49:44'),(20,'Enrolled Student','Razul Atowan','Enrolled student (756767) into section ID 5','2026-04-14 08:50:09'),(21,'Assigned Instructor','Razul Atowan','Assigned Prof. Jasper bibot to section ROTC-B','2026-04-14 14:02:31'),(22,'Created Student','Razul Atowan','Added test1  (2022-424) under bsit [LTS]','2026-04-14 15:25:15'),(23,'Assigned Instructor','Razul Atowan','Assigned Prof. Dane Macarine to section LTS-A','2026-04-14 15:25:29'),(24,'Created Instructor','Razul Atowan','Created account for Prof. Proj Macarine (IT)','2026-05-19 13:58:39'),(25,'Updated Instructor','Razul Atowan','Updated details for Prof. Dane Macarine','2026-05-21 09:37:00'),(26,'Updated Instructor','Razul Atowan','Updated details for Prof. Dane Macarine','2026-05-21 09:37:12'),(27,'Updated Instructor','coordinator','Updated details for Prof. Instructor','2026-05-21 13:16:24');
/*!40000 ALTER TABLE `audit_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `enrollments`
--

DROP TABLE IF EXISTS `enrollments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `enrollments` (
  `id` int NOT NULL AUTO_INCREMENT,
  `student_id` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `section_id` int NOT NULL,
  `final_grade` decimal(5,2) DEFAULT NULL,
  `status` enum('Pending','Passed','Failed','Dropped') COLLATE utf8mb4_unicode_ci DEFAULT 'Pending',
  `serial_number` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `serial_number` (`serial_number`),
  KEY `student_id` (`student_id`),
  KEY `section_id` (`section_id`),
  CONSTRAINT `enrollments_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`student_id`) ON DELETE CASCADE,
  CONSTRAINT `enrollments_ibfk_2` FOREIGN KEY (`section_id`) REFERENCES `sections` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `enrollments`
--

LOCK TABLES `enrollments` WRITE;
/*!40000 ALTER TABLE `enrollments` DISABLE KEYS */;
INSERT INTO `enrollments` VALUES (1,'32534534',1,NULL,'Pending',NULL,'2026-04-13 05:26:31'),(3,'756767',5,NULL,'Pending',NULL,'2026-04-14 00:50:09'),(4,'2022-424',4,NULL,'Pending',NULL,'2026-04-14 07:25:15');
/*!40000 ALTER TABLE `enrollments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `notifications`
--

DROP TABLE IF EXISTS `notifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `notifications` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `role` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `message` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `link` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_read` tinyint(1) DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notifications`
--

LOCK TABLES `notifications` WRITE;
/*!40000 ALTER TABLE `notifications` DISABLE KEYS */;
INSERT INTO `notifications` VALUES (1,10,NULL,'Your activity plan \'te\' has been approved.','activity_plans.php',1,'2026-04-13 02:32:14'),(2,10,NULL,'Your activity plan \'te\' has been rejected.','activity_plans.php',1,'2026-04-13 02:34:31'),(3,10,NULL,'Your activity plan \'te\' has been rejected.','activity_plans.php',1,'2026-04-13 02:34:34'),(4,10,NULL,'Your activity plan \'te\' has been rejected.','activity_plans.php',1,'2026-04-13 02:34:38'),(5,10,NULL,'Your activity plan \'saf\' has been approved.','activity_plans.php',1,'2026-04-13 02:35:14'),(6,10,NULL,'Your activity plan \'saf\' has been approved.','activity_plans.php',1,'2026-04-13 02:35:17'),(7,5,NULL,'Your activity plan \'Testing\' has been approved.','activity_plans.php',0,'2026-05-21 03:35:11'),(8,5,NULL,'Your accomplishment report \'Testing Report\' has been reviewed.','reports.php',0,'2026-05-21 03:36:41'),(9,5,NULL,'Your accomplishment report \'Testing Report\' has been reviewed.','reports.php',0,'2026-05-21 03:37:17');
/*!40000 ALTER TABLE `notifications` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sections`
--

DROP TABLE IF EXISTS `sections`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sections` (
  `id` int NOT NULL AUTO_INCREMENT,
  `component` enum('CWTS','LTS','ROTC') COLLATE utf8mb4_unicode_ci NOT NULL,
  `section_name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `school_year` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `semester` enum('1st','2nd','Summer') COLLATE utf8mb4_unicode_ci NOT NULL,
  `instructor_id` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `instructor_id` (`instructor_id`),
  CONSTRAINT `sections_ibfk_1` FOREIGN KEY (`instructor_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sections`
--

LOCK TABLES `sections` WRITE;
/*!40000 ALTER TABLE `sections` DISABLE KEYS */;
INSERT INTO `sections` VALUES (1,'CWTS','CWTS-A','2026-2027','1st',NULL,'2026-04-13 04:54:37'),(2,'CWTS','CWTS-B','2026-2027','1st',NULL,'2026-04-13 05:16:43'),(3,'CWTS','CWTS-D','2026-2027','1st',NULL,'2026-04-13 05:16:43'),(4,'LTS','LTS-A','2026-2027','1st',NULL,'2026-04-13 05:16:43'),(5,'LTS','LTS-B','2026-2027','1st',NULL,'2026-04-13 05:16:43'),(9,'CWTS','t','2026-2027','1st',NULL,'2026-05-19 03:55:16'),(10,'ROTC','BSIT 1','2026-2027','1st',15,'2026-05-21 05:17:03');
/*!40000 ALTER TABLE `sections` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `students`
--

DROP TABLE IF EXISTS `students`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `students` (
  `student_id` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `first_name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `course` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `year_level` int DEFAULT NULL,
  `component` enum('CWTS','LTS','ROTC') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `enrollment_status` enum('Active','Completed','Dropped') COLLATE utf8mb4_unicode_ci DEFAULT 'Active',
  `date_of_birth` date DEFAULT NULL,
  `sex` enum('Male','Female') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contact_number` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `complete_address` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`student_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `students`
--

LOCK TABLES `students` WRITE;
/*!40000 ALTER TABLE `students` DISABLE KEYS */;
INSERT INTO `students` VALUES ('2022-0675','tfhgh','','ghjghj',1,'ROTC','Active',NULL,NULL,'12321312','saludez.johnloui@dnsc.edu.ph',NULL,'2026-04-13 05:16:10'),('2022-424','test1','','bsit',1,'LTS','Active',NULL,NULL,'09700909910','saludez.johnloui@dnsc.edu.ph',NULL,'2026-04-14 07:25:15'),('32534534','sdsdsdsds','','ssdsd',2,'CWTS','Active',NULL,NULL,'09700909910','saludez.johnloui@dnsc.edu.ph',NULL,'2026-04-13 05:24:40'),('756767','rfthnygkuijl','','yuyi',1,'LTS','Active',NULL,NULL,'09700909910','saludez.johnloui@dnsc.edu.ph',NULL,'2026-04-14 00:49:44');
/*!40000 ALTER TABLE `students` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `full_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` enum('Admin','Instructor','ROTC','System Admin') COLLATE utf8mb4_unicode_ci NOT NULL,
  `component` enum('CWTS','LTS','ROTC') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `degree_type` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `degree_title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contact_number` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `status` enum('Active','Inactive') COLLATE utf8mb4_unicode_ci DEFAULT 'Active',
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (13,'System Administrator','sysadmin@dnsc.edu.ph','$2y$10$w3PoDDUHElrW79FfwcIHGu8es/0b0a.7Qkd9rDDkil4nPNelK8HtO','System Admin',NULL,NULL,NULL,NULL,'2026-05-21 04:52:45','Active'),(14,'coordinator','coor@dnsc.edu.ph','$2y$10$QJe85ADPhJHnn3xIUQfA4uC/l0V4NojUn4PdK3KyR.lUruzOwozpi','Admin',NULL,'Bachelor','ada','09700909910','2026-05-21 04:59:52','Active'),(15,'Instructor','ins@dnsc.edu.ph','$2y$10$23m64XD8bIZ.4bssHdMaROn8msIe41eV13HjWmt/pI8ELZjTlC0cu','Instructor','ROTC','Masteral','BSIT','09083920792','2026-05-21 05:14:29','Active'),(16,'rotc','rotc@dnsc.edu.ph','$2y$10$oHPMBx08KjwaDivXMzono.xxJn6NOYSH2i7LH5MAOBfS.YMbM3yfq','ROTC',NULL,'Bachelor','BSIT','340534634','2026-05-21 05:14:53','Active');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-05-27  9:41:53

--
-- Table structure for table `certificate_templates`
--

DROP TABLE IF EXISTS `certificate_templates`;
CREATE TABLE `certificate_templates` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `program_type` enum('All Programs','CWTS/LTS','ROTC') COLLATE utf8mb4_unicode_ci NOT NULL,
  `badge_color` enum('Indigo','Emerald','Amber','Rose') COLLATE utf8mb4_unicode_ci NOT NULL,
  `image_path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT 'Active',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

