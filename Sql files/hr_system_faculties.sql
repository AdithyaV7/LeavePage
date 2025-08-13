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
-- Table structure for table `faculties`
--

DROP TABLE IF EXISTS `faculties`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `faculties` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `faculty_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `dean_email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `dean_user_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `faculty_prefix` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sorting_order` int unsigned NOT NULL,
  `faculty_dean_office` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `faculties_sorting_order_index` (`sorting_order`)
) ENGINE=InnoDB AUTO_INCREMENT=52 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `faculties`
--

LOCK TABLES `faculties` WRITE;
/*!40000 ALTER TABLE `faculties` DISABLE KEYS */;
INSERT INTO `faculties` VALUES (1,'Faculty of Humanities and Social Sciences','shiran@sjp.ac.lk','137','',1,101,'2023-01-27 15:55:52','2023-04-07 10:40:42',NULL),(2,'Faculty of Applied Sciences','upuls@sjp.ac.lk','70','',2,201,'2023-01-27 15:55:52','2023-03-02 12:43:15',NULL),(3,'Faculty of Management Studies & Commerce','dushan@sjp.ac.lk','110','',3,301,'2023-01-27 15:55:52','2023-06-09 15:16:16',NULL),(4,'Faculty of Medical Sciences','manorigamage@sjp.ac.lk','162','',4,401,'2023-01-27 15:55:52','2023-11-24 11:21:16',NULL),(5,'Faculty of Graduate Studies','rasika@sjp.ac.lk','170','',5,501,'2023-01-27 15:55:52','2023-11-24 14:09:30',NULL),(6,'Graduate Studies','','','',14,601,'2023-01-27 15:55:52','2023-02-23 10:57:04','2023-02-23 10:57:04'),(7,'External Degrees and Extension Courses Unit','rupika@sjp.ac.lk','197','',15,701,'2023-01-27 15:55:52','2024-07-29 18:35:24',NULL),(8,'Faculty of Engineering','nishanmd@sjp.ac.lk','80','',7,801,'2023-01-27 15:55:52','2023-11-24 14:10:58',NULL),(9,'Faculty of Technology','nilminil@sjp.ac.lk','72','',6,901,'2023-01-27 15:55:52','2023-09-14 11:50:35',NULL),(10,'Faculty of Allied Health Sciences','deepthi@sjp.ac.lk','40','',8,1001,'2023-01-27 15:55:52','2023-04-06 16:30:40',NULL),(11,'Faculty of Dental Sciences','priyakepalipana@sjp.ac.lk','51','',9,1101,'2023-01-27 15:55:52','2025-05-20 11:39:22',NULL),(12,'Faculty of Urban and Aquatic Bioresources','','0','',10,1201,'2023-01-27 15:55:52','2023-03-02 11:48:10',NULL),(13,'Faculty of Computing','pja@sjp.ac.lk','60','',11,1301,'2023-01-27 15:55:52','2023-03-02 11:53:47',NULL),(50,'Administration under Registrar','weliwita@sjp.ac.lk','125','',12,5002,'2023-01-27 15:55:52','2023-01-27 15:55:52',NULL),(51,'Administration under VC','pathmalal@sjp.ac.lk','136','',13,5001,'2023-01-27 15:55:52','2023-11-24 11:18:47',NULL);
/*!40000 ALTER TABLE `faculties` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-08-05 11:44:06
