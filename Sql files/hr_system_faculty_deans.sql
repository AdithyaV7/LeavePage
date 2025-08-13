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
-- Table structure for table `faculty_deans`
--

DROP TABLE IF EXISTS `faculty_deans`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `faculty_deans` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `faculty_id` int unsigned NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `emp_no` int unsigned NOT NULL,
  `appointmemt_type` int NOT NULL,
  `dean_user_id` int DEFAULT NULL,
  `start_date` date NOT NULL,
  `end_date` date DEFAULT NULL,
  `active_status` int NOT NULL DEFAULT '1',
  `added_user_id` int unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `faculty_deans_faculty_id_index` (`faculty_id`),
  KEY `faculty_deans_emp_no_index` (`emp_no`),
  KEY `faculty_deans_added_user_id_index` (`added_user_id`),
  KEY `faculty_deans_appointmemt_type_index` (`appointmemt_type`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `faculty_deans`
--

LOCK TABLES `faculty_deans` WRITE;
/*!40000 ALTER TABLE `faculty_deans` DISABLE KEYS */;
INSERT INTO `faculty_deans` VALUES (1,11,'priyakepalipana@sjp.ac.lk',12775,199,51,'2025-04-08','2028-04-07',1,11981,'2023-03-02 11:21:36','2025-05-20 11:39:22'),(2,12,'pradeep@sjp.ac.lk',1864,199,58,'2022-07-06','2025-07-05',0,5296,'2023-03-02 11:48:10','2023-03-02 11:48:10'),(3,13,'pja@sjp.ac.lk',8222,199,60,'2023-12-20','2026-12-19',1,5296,'2023-03-02 11:53:47','2024-04-02 14:16:03'),(4,2,'upuls@sjp.ac.lk',3288,199,70,'2025-03-14','2028-03-13',1,8872,'2023-03-02 12:43:15','2025-03-17 15:28:19'),(5,9,'nilminil@sjp.ac.lk',10135,199,72,'2023-09-09','2026-09-08',1,8796,'2023-03-02 13:14:32','2023-09-14 11:50:35'),(6,8,'nishanmd@sjp.ac.lk',10286,199,80,'2023-09-20','2026-09-19',1,8796,'2023-03-02 13:34:41','2023-11-24 14:10:58'),(7,4,'manorigamage@sjp.ac.lk',6272,199,162,'2023-11-17','2026-11-16',1,8633,'2023-03-07 11:25:58','2023-11-24 11:21:16'),(8,3,'dushan@sjp.ac.lk',7239,199,110,'2023-05-29','2026-05-28',1,7877,'2023-03-08 18:02:16','2023-06-09 15:16:16'),(9,5,'rasika@sjp.ac.lk',6205,199,170,'2023-11-13','2026-11-12',1,8633,'2023-04-06 16:24:36','2023-11-24 14:09:30'),(10,10,'deepthi@sjp.ac.lk',6788,199,40,'2023-03-23','2026-03-22',1,12597,'2023-04-06 16:30:40','2023-12-07 08:57:26'),(11,1,'shiran@sjp.ac.lk',5045,199,137,'2025-07-01','2028-06-30',1,11420,'2023-04-07 10:40:42','2025-07-31 11:16:51'),(12,51,'pathmalal@sjp.ac.lk',1932,199,136,'2023-07-28','2026-07-27',1,8872,'2023-11-24 11:18:47','2023-11-24 11:56:36'),(13,7,'rupika@sjp.ac.lk',1966,199,197,'2024-05-03','2027-05-02',1,11981,'2023-11-24 11:18:47','2024-07-29 18:35:24'),(14,50,'weliwita@sjp.ac.lk',9918,199,125,'2020-11-30','1970-01-01',1,7859,'2023-11-24 11:18:47','2023-11-24 11:18:47');
/*!40000 ALTER TABLE `faculty_deans` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-08-05 11:43:56
