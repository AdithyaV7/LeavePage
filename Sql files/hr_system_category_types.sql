-- MySQL dump 10.13  Distrib 8.0.27, for Win64 (x86_64)
--
-- Host: localhost    Database: hr_system
-- ------------------------------------------------------
-- Server version	8.0.39-0ubuntu0.20.04.1

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
-- Table structure for table `category_types`
--

DROP TABLE IF EXISTS `category_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `category_types` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `category_type_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=58 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `category_types`
--

LOCK TABLES `category_types` WRITE;
/*!40000 ALTER TABLE `category_types` DISABLE KEYS */;
INSERT INTO `category_types` VALUES (1,'gender','2023-01-27 15:55:52','2023-01-27 15:55:52'),(2,'race','2023-01-27 15:55:52','2023-01-27 15:55:52'),(3,'religion','2023-01-27 15:55:52','2023-01-27 15:55:52'),(4,'civil status','2023-01-27 15:55:52','2023-01-27 15:55:52'),(5,'title','2023-01-27 15:55:52','2023-01-27 15:55:52'),(6,'citizenship','2023-01-27 15:55:52','2023-01-27 15:55:52'),(7,'vacancy decision status','2023-01-27 15:55:52','2023-01-27 15:55:52'),(8,'application decision status','2023-01-27 15:55:52','2023-01-27 15:55:52'),(9,'employee decision status','2023-01-27 15:55:52','2023-01-27 15:55:52'),(10,'employee type','2023-01-27 15:55:52','2023-01-27 15:55:52'),(11,'common status','2023-01-27 15:55:52','2023-01-27 15:55:52'),(12,'completion status','2023-01-27 15:55:52','2023-01-27 15:55:52'),(13,'main branch','2023-01-27 15:55:52','2023-01-27 15:55:52'),(14,'grade','2023-01-27 15:55:52','2023-01-27 15:55:52'),(15,'al stream','2023-01-27 15:55:52','2023-01-27 15:55:52'),(16,'education level','2023-01-27 15:55:52','2023-01-27 15:55:52'),(17,'staff grade','2023-01-27 15:55:52','2023-01-27 15:55:52'),(18,'main group','2023-01-27 15:55:52','2023-01-27 15:55:52'),(19,'ugc mis','2023-01-27 15:55:52','2023-01-27 15:55:52'),(20,'ugc finance','2023-01-27 15:55:52','2023-01-27 15:55:52'),(21,'employee status id','2023-01-27 15:55:52','2023-01-27 15:55:52'),(22,'employee status type id','2023-01-27 15:55:52','2023-01-27 15:55:52'),(23,'employee category','2023-01-27 15:55:52','2023-01-27 15:55:52'),(24,'bond type','2023-01-27 15:55:52','2023-01-27 15:55:52'),(25,'bond category','2023-01-27 15:55:52','2023-01-27 15:55:52'),(26,'university type','2023-01-27 15:55:52','2023-01-27 15:55:52'),(27,'bond status','2023-01-27 15:55:52','2023-01-27 15:55:52'),(28,'increment deffer options','2023-01-27 15:55:52','2023-01-27 15:55:52'),(29,'service type','2023-01-27 15:55:52','2023-01-27 15:55:52'),(30,'external transfer type','2023-01-27 15:55:52','2023-01-27 15:55:52'),(31,'commendation','2023-01-27 15:55:52','2023-01-27 15:55:52'),(32,'Performance','2023-02-07 15:23:08','2023-02-07 15:23:08'),(33,'Appointment Type','2023-02-14 08:30:58','2023-02-14 08:30:58'),(34,'Vacancy Visibility','2023-02-27 13:21:16','2023-02-27 13:21:16'),(36,'Interview Type','2023-05-17 13:31:27','2023-05-17 13:31:27'),(37,'Interview Panel Member Type','2023-05-24 14:22:16','2023-05-24 14:22:16'),(38,'Promotion Related Letters','2023-05-30 15:25:23','2023-05-30 15:25:23'),(39,'Deegree Type','2023-06-15 09:13:24','2023-06-15 09:13:24'),(40,'Degree Classes','2023-06-15 09:13:57','2023-06-15 09:13:57'),(41,'Diploma Type','2023-06-23 11:59:30','2023-06-23 11:59:30'),(42,'Special Qulification','2023-06-26 21:55:22','2023-06-26 21:55:22'),(43,'Membership Type','2023-06-27 09:18:04','2023-06-27 09:18:04'),(44,'Interview Panel Member Position','2023-06-27 13:10:35','2023-06-27 13:10:35'),(45,'Head Position','2023-07-24 14:45:39','2023-07-24 14:45:49'),(46,'Exam Type','2023-08-08 13:22:13','2023-08-08 13:22:13'),(47,'Academic SOR Category','2023-09-06 22:14:26','2023-09-06 22:14:26'),(48,'Salary Payement Type','2023-10-19 13:03:18','2023-10-19 13:03:18'),(49,'Notification Type','2023-11-08 11:40:14','2023-11-08 11:40:14'),(50,'Promotion Salary Step Types','2023-11-16 14:48:32','2023-11-16 14:48:32'),(51,'Designation Type','2023-12-05 10:11:54','2023-12-05 10:11:54'),(52,'Employee update type','2024-07-24 18:47:36','2024-07-24 18:47:56'),(53,'Academic increment SER sections','2024-08-14 15:50:57','2024-08-14 15:50:57'),(54,'Academic leave type','2024-09-27 13:11:39','2024-09-27 13:11:39'),(55,'Leave salary pay type','2024-10-14 22:11:21','2024-10-14 22:11:21'),(56,'Increment type','2024-11-26 13:37:09','2024-11-26 13:37:09'),(57,'Service Category','2025-04-29 10:28:51','2025-04-29 10:28:51');
/*!40000 ALTER TABLE `category_types` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-08-05 11:43:51
