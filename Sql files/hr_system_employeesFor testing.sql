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
-- Table structure for table `employees`
--

DROP TABLE IF EXISTS `employees`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `employees` (
  `id` int unsigned NOT NULL,
  `employee_no` int unsigned NOT NULL,
  `file_reference_number` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `vacancy_id` bigint unsigned DEFAULT NULL,
  `application_referance_no` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `main_branch_id` int unsigned NOT NULL,
  `designation_id` bigint unsigned NOT NULL,
  `faculty_id` bigint unsigned NOT NULL,
  `department_id` bigint unsigned NOT NULL,
  `sub_department_id` varchar(15) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `carder_faculty_id` int NOT NULL DEFAULT '0',
  `carder_department_id` int NOT NULL DEFAULT '0',
  `carder_sub_department_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `initials` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `name_denoted_by_initials` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `civil_status_id` bigint unsigned NOT NULL,
  `gender_id` bigint unsigned NOT NULL,
  `race_id` int DEFAULT NULL,
  `religion_id` int DEFAULT NULL,
  `permanent_add1` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `permanent_add2` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `permanent_add3` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `permanent_city_id` bigint unsigned NOT NULL,
  `postal_add1` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `postal_add2` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `postal_add3` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `postal_city_id` bigint unsigned NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `personal_email` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `state_of_citizenship_id` bigint unsigned NOT NULL,
  `citizen_registration_no` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `initial_appointment_date` date DEFAULT NULL,
  `gratuity_cal_date` date DEFAULT NULL,
  `current_appointment_date` date DEFAULT NULL,
  `salary_termination_date_1` date DEFAULT NULL,
  `salary_termination_date_2` date DEFAULT NULL,
  `retirement_date` date DEFAULT NULL,
  `current_basic_salary` decimal(10,2) DEFAULT NULL,
  `increment_date` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `confirmation_date` date DEFAULT NULL,
  `sabbatical_start` date DEFAULT NULL,
  `sabbatical_end` date DEFAULT NULL,
  `etf_no` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `upf_no` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pension_reference_no` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `emp_highest_edu_level` int DEFAULT NULL,
  `added_ma_user_id` bigint unsigned DEFAULT NULL,
  `added_ma_date` date DEFAULT NULL,
  `updated_ma_user_id` bigint DEFAULT NULL,
  `updated_ma_date` date DEFAULT NULL,
  `approved_ar_user_id` bigint unsigned DEFAULT NULL,
  `approved_ar_date` date DEFAULT NULL,
  `assign_ma_user_id` bigint unsigned DEFAULT NULL,
  `assign_ma_date` date DEFAULT NULL,
  `previous_assign_ma_user_id` int DEFAULT NULL,
  `previous_assign_ma_change_date` date DEFAULT NULL,
  `status_id` bigint unsigned NOT NULL,
  `employee_status_id` int unsigned NOT NULL,
  `employee_status_type_id` int unsigned NOT NULL,
  `employee_work_type` int unsigned NOT NULL,
  `emp_decision_id` bigint unsigned NOT NULL,
  `mobile_no` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL,
  `telephone_no` varchar(15) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `office_ext` int DEFAULT NULL,
  `office_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nic` varchar(12) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `date_of_birth` date NOT NULL,
  `nic_old` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nic_new` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `active_nic` int DEFAULT NULL,
  `dob_gen` date DEFAULT NULL,
  `title_id` bigint unsigned NOT NULL,
  `promo_eligibility` int NOT NULL DEFAULT '0',
  `ma_promo_exam` int NOT NULL DEFAULT '0',
  `lock` int NOT NULL DEFAULT '0',
  `promotion_active_status` int NOT NULL DEFAULT '0',
  `increment_process_active` int NOT NULL DEFAULT '0',
  `user_account_status` int NOT NULL DEFAULT '0',
  `salary_payment_type` int NOT NULL DEFAULT '266',
  `pension_status` int NOT NULL DEFAULT '0',
  `salary_status` int NOT NULL DEFAULT '0',
  `zoom_active_status` int NOT NULL DEFAULT '0',
  `welcome_mail` int NOT NULL DEFAULT '0',
  `tin_no` int NOT NULL DEFAULT '0',
  `tin_no_reminder` int DEFAULT '0',
  `tin_no_batch` int DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `vote` int NOT NULL DEFAULT '0',
  `vote_add_user` int NOT NULL DEFAULT '0',
  `vote_time` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`employee_no`),
  KEY `employees_mobile_no_index` (`mobile_no`),
  KEY `employees_vacancy_id_index` (`vacancy_id`),
  KEY `employees_designation_id_index` (`designation_id`),
  KEY `employees_telephone_no_index` (`telephone_no`),
  KEY `employees_civil_status_id_index` (`civil_status_id`),
  KEY `employees_gender_id_index` (`gender_id`),
  KEY `employees_race_id_index` (`race_id`),
  KEY `employees_religion_id_index` (`religion_id`),
  KEY `employees_permanent_city_id_index` (`permanent_city_id`),
  KEY `employees_postal_city_id_index` (`postal_city_id`),
  KEY `employees_state_of_citizenship_id_index` (`state_of_citizenship_id`),
  KEY `employees_added_ma_user_id_index` (`added_ma_user_id`),
  KEY `employees_approved_ar_user_id_index` (`approved_ar_user_id`),
  KEY `employees_status_id_index` (`status_id`),
  KEY `employees_emp_decision_id_index` (`emp_decision_id`),
  KEY `employees_faculty_id_index` (`faculty_id`),
  KEY `employees_department_id_index` (`department_id`),
  KEY `employees_main_branch_id_index` (`main_branch_id`),
  KEY `employees_assign_ma_user_id_index` (`assign_ma_user_id`),
  KEY `employees_employee_status_id_index` (`employee_status_id`),
  KEY `employees_employee_status_type_id_index` (`employee_status_type_id`),
  KEY `employees_employee_work_type_index` (`employee_work_type`),
  KEY `employees_sub_department_id_index` (`sub_department_id`),
  KEY `employees_emp_highest_edu_level_index` (`emp_highest_edu_level`),
  KEY `employees_updated_ma_user_id_index` (`updated_ma_user_id`),
  KEY `employees_nic_index` (`nic`),
  KEY `employees_email_index` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `employees`
--

LOCK TABLES `employees` WRITE;
/*!40000 ALTER TABLE `employees` DISABLE KEYS */;
INSERT INTO `employees` VALUES (456,965,'USJP/AE/MGT/08/07/PF/66',NULL,NULL,52,442,3,306,NULL,3,306,NULL,'T.M.B.','Tennakoon Mudiyanselage Bandulasena','Palawatta',15,1,3,9,'600/7A Udaya Mawatha','Ihalabiyanwila','',140,'600/7A Udaya Mawatha','Ihalabiyanwila','',140,'bpalawatta@sjp.ac.lk',NULL,25,NULL,'1982-09-01','2016-11-30','2016-11-30','1970-01-01','1970-01-01','2024-02-04',156725.00,'07-28','1970-01-01',NULL,NULL,'418','S 000418','',80,7877,'2023-01-24',7877,'2024-03-30',NULL,NULL,15097,'2023-01-24',7877,'2025-06-05',1,111,146,139,41,'0714779102',NULL,NULL,NULL,'582101566V','1958-07-28','582101566V','195821001566',1,'1958-07-28',20,0,0,0,0,0,0,266,0,0,1,0,0,0,0,'2023-01-24 10:59:52','2025-02-25 16:03:03',NULL,0,0,NULL),(192,1041,'USJP/AE/HSS/10/04/PF/38',NULL,NULL,52,442,1,102,NULL,1,102,NULL,'H.D.Y.D.','Hettige Dona Yasanjalee Devika','Jayathilake',15,2,3,9,'88/1/A Meemanagoda Road','Kalalgoda','',73,'88/1/A Meemanagoda Road','Kalalgoda','',73,'yasanjalee@sjp.ac.lk',NULL,25,NULL,'1984-12-03','2017-11-19','2017-11-19','1970-01-01','1970-01-01','2024-12-31',159650.00,'11-19','1970-01-01',NULL,NULL,'522','S 000522','PS 000522',82,10390,'2022-12-07',10390,'2025-01-02',NULL,NULL,10390,'2022-12-07',NULL,NULL,1,111,146,139,41,'0714463980',NULL,NULL,NULL,'595070449V','1959-01-07','595070449V','195950700449',1,'1959-01-07',121,0,0,0,0,0,0,266,0,0,1,0,0,0,0,'2022-12-07 15:19:47','2025-02-25 16:03:03',NULL,0,0,NULL),(389,1052,'USJP/AE/HSS/10/04/PF/40',NULL,NULL,52,438,1,103,NULL,1,103,NULL,'M.P.A.A.','Madduma Patabendige Ashanthi Anuruddhika','Buddhadasa',15,2,3,9,'128/1  Elegant Homes','Neelammahara Road','',58,'128/1  Elegant Homes','Neelammahara Road','',58,'anuruddhika@sjp.ac.lk',NULL,25,NULL,'1985-11-01','1997-05-14','1997-05-14','1970-01-01','1970-01-01','2026-12-10',145560.00,'05-14','1970-01-01',NULL,NULL,'635','S 000635','PS 000635',79,10390,'2023-01-19',10390,'2025-03-31',NULL,NULL,10390,'2023-01-19',NULL,NULL,1,110,112,139,41,'0716353513',NULL,NULL,NULL,'196184501801','1961-12-10','618451801V','196184501801',2,'1961-12-10',23,0,0,0,0,0,0,266,0,0,1,0,0,1,0,'2023-01-19 10:39:11','2025-07-21 09:57:15',NULL,0,0,NULL),(204,1054,'USJP/AE/HSS/10/04/PF/42',NULL,NULL,52,370,1,102,NULL,1,102,NULL,'A.A.J.','Amarasinghe Arachchige Jayantha','Jayasiri',15,1,3,9,'75/2  Pelendagoda Road','Panagoda','',39,'75/2  Pelendagoda Road','Panagoda','',39,'jayantha@sjp.ac.lk',NULL,25,NULL,'1986-07-01','2019-12-15','2019-12-15','1970-01-01','2024-12-30','2026-01-09',147050.00,'12-15','1970-01-01',NULL,NULL,'713','S 000713','PS 000713',82,10390,'2022-12-08',10390,'2024-12-06',NULL,NULL,10390,'2022-12-08',NULL,NULL,1,110,152,139,41,'0718244125',NULL,NULL,NULL,'610090052V','1961-01-09','610090052V','196100900052',1,'1961-01-09',20,0,0,1,0,0,0,266,0,0,0,0,610090052,1,2,'2022-12-08 15:04:10','2025-07-23 11:09:18',NULL,0,0,NULL),(203,1055,'USJP/AE/HSS/10/04/PF/41',NULL,NULL,52,370,1,102,NULL,1,102,NULL,'A.P.N.','Arumadura Praneeth Nishad De Silva','Abhayasundere',15,1,3,9,'128/7  Moratuwa Road','','',77,'128/7  Moratuwa Road','','',77,'praneeth@sjp.ac.lk',NULL,25,NULL,'1986-07-01','2019-12-15','2019-12-15','1970-01-01','1970-01-01','2024-12-31',152450.00,'12-15','1970-01-01',NULL,NULL,'714','S 000714','PS 000714',82,10390,'2022-12-08',10390,'2024-12-17',NULL,NULL,10390,'2022-12-08',NULL,NULL,1,111,146,139,41,'0773718045',NULL,NULL,NULL,'592230410V','1959-08-10','592230410V','195922300410',1,'1959-08-10',20,0,0,1,0,0,0,266,0,0,0,0,0,0,0,'2022-12-08 14:19:16','2025-07-18 00:00:16',NULL,0,0,NULL),(377,1056,'USJP/AE/HSS/10/04/PF/39',NULL,NULL,52,24,1,103,NULL,1,103,NULL,'M.W.','Madawala Witharamalage','Jayasundara',15,1,3,9,'263/1  Thalduwa','','',5,'263/1  Thalduwa','','',5,'jayasundara@sjp.ac.lk',NULL,25,NULL,'1986-07-01','2009-11-27','2009-11-27','1970-01-01','2024-02-10','2025-02-10',143390.00,'11-27','1992-12-23',NULL,NULL,'715','S 000715','PS 000715',79,10390,'2023-01-16',10390,'2024-12-17',NULL,NULL,10390,'2023-01-16',NULL,NULL,1,111,146,139,41,'0714478755',NULL,NULL,NULL,'600412256V','1960-02-10','600412256V','196004102256',1,'1960-02-10',22,0,0,1,0,0,0,266,0,0,0,0,600412256,0,1,'2023-01-16 14:07:48','2025-07-18 00:00:27',NULL,0,0,NULL),(69,1068,'USJP/AE/HSS/10/07/PF/83',NULL,NULL,52,370,1,104,NULL,1,104,NULL,'D.P.S.','Duwa Pathirage Sarath','Chandrakumara',15,1,3,13,'67/11B  Namal Uyana','Niyandagala','',73,'67/11B  Namal Uyana','Niyandagala','',73,'chandra@sjp.ac.lk',NULL,25,NULL,'1989-02-27','1989-02-27','2016-05-30','1970-01-01','2025-01-11','2027-10-05',141650.00,'05-30','1970-01-01',NULL,NULL,'890','S 000890','PS 000890',82,11420,'2022-12-03',11420,'2025-04-01',NULL,NULL,11420,'2022-12-03',NULL,NULL,1,110,152,139,41,'0718098498',NULL,NULL,NULL,'622790173V','1962-10-05','622790173V','196227900173',1,'1962-10-05',20,0,0,0,0,0,0,266,0,0,1,0,106556865,0,1,'2022-12-03 15:47:15','2025-07-16 20:03:13',NULL,0,0,NULL),(65,1070,'USJP/AE/HSS/10/07/PF/81',NULL,NULL,52,442,1,104,NULL,1,104,NULL,'H.M.T.N.R.','Herath Mudiyanselage Tikiri Nimal Ranbandara','Herath',15,1,3,13,'105 Nallamudawa','','',965,'105 Nallamudawa','','',965,'n-herath@sjp.ac.lk',NULL,25,NULL,'1989-04-04','1989-04-04','2017-11-13','1970-01-01','1970-01-01','2024-12-31',153800.00,'11-13','1970-01-01',NULL,NULL,'893','S 000893','PS 000893',82,11420,'2022-12-03',11420,'2025-01-23',NULL,NULL,11420,'2022-12-03',NULL,NULL,1,111,146,139,41,'0714478784',NULL,NULL,NULL,'195918300716','1959-07-01','591830716V','195918300716',2,'1959-07-01',120,0,0,0,0,0,0,266,0,0,1,0,0,0,0,'2022-12-03 15:25:08','2025-02-25 16:03:03',NULL,0,0,NULL),(300,1073,'USJP/AE/HSS/10/04/PF/43',NULL,NULL,52,370,1,103,NULL,1,103,NULL,'W.M.','Wijesinghe Mudiyanselage','Dhanapala',15,1,3,9,'35/1/A  Dewananda Road','Nawinna','',58,'35/1/A  Dewananda Road','Nawinna','',58,'wmd@sjp.ac.lk',NULL,25,NULL,'1990-05-16','2017-03-31','2017-03-31','1970-01-01','2025-12-31','2028-02-10',147050.00,'03-31','1970-01-01',NULL,NULL,'934','S 000934','PS 000934',81,10390,'2023-01-11',10390,'2025-02-06',NULL,NULL,10390,'2023-01-11',NULL,NULL,1,110,152,139,41,'0787803931',NULL,NULL,NULL,'630413923V','1963-02-10','630413923V','196304103923',1,'1963-02-10',20,0,0,0,0,0,1,266,0,0,1,0,107140255,0,1,'2023-01-11 11:29:51','2025-07-20 17:34:27',NULL,0,0,NULL),(300092,1086,'USJP/AE/HSS/10/01/PF/01',NULL,NULL,52,442,1,107,NULL,1,107,NULL,'R.M.K.','Rathnayake Mudiyanselage Karunadasa','Rathnayake',15,1,3,9,'28  Subadra Mawatha','Madiwela','',55,'28  Subadra Mawatha','Madiwela','',55,'ratnayake@sjp.ac.lk',NULL,25,NULL,'1994-03-01','2023-09-25','2023-09-25','1970-01-01','1970-01-01','2027-08-14',147950.00,'09-25','1970-01-01',NULL,NULL,'1444','S 001444','PS 001444',82,10390,'2023-07-25',10390,'2024-12-10',NULL,NULL,10390,'2023-07-25',NULL,NULL,1,110,112,139,41,'0714439296',NULL,NULL,NULL,'622271648V','1962-08-14','622271648V','196222701648',1,'1962-08-14',120,0,0,0,0,0,0,266,0,0,1,0,106783802,0,1,'2023-07-25 14:42:13','2025-07-18 21:24:45',NULL,0,0,NULL),(300083,1090,'USJP/AE/HSS/10/05/PF/56',NULL,NULL,52,370,1,112,NULL,1,112,NULL,'N.','Naimbala','Dhammadassi',16,1,3,9,'62  Kethumathi Viharaya','Obahena Road','Madiwela',55,'62  Kethumathi Viharaya','Obahena Road','Madiwela',55,'ndhammadassi@sjp.ac.lk',NULL,25,NULL,'1991-01-01','2009-11-19','2009-11-19','1970-01-01','1970-01-01','2026-12-07',128150.00,'11-19','1970-01-01',NULL,NULL,'998','S 000998','PS 000998',82,10390,'2023-07-24',10390,'2023-10-18',NULL,NULL,10390,'2023-07-24',NULL,NULL,1,110,112,139,41,'0718011656',NULL,NULL,NULL,'613430890X','1961-12-08','613430890X','196134300890',1,'1961-12-08',127,0,0,0,0,0,0,266,0,0,0,0,613430890,1,1,'2023-07-24 13:45:11','2025-07-21 12:28:49',NULL,0,0,NULL),(500620,300082,'USJP/NAE/06/CWA/13PF',NULL,NULL,53,687,1,114,NULL,1,114,NULL,'A.G.P.N.','Aladeniye Gedara Pathum Niroshan','Priyantha',16,1,3,9,'No 182 Poromaruwa','Welamboda','',377,'No 182 Poromaruwa','Welamboda','',377,NULL,'pathumnirosh@gmail.com',25,NULL,NULL,NULL,'2024-09-05','2025-02-04',NULL,NULL,49825.00,NULL,NULL,'1970-01-01','1970-01-01',NULL,NULL,NULL,72,12466,'2024-10-13',NULL,NULL,NULL,NULL,12466,'2024-10-13',NULL,NULL,1,111,145,141,41,'00754788570',NULL,NULL,NULL,'200132502829','2001-11-20','013252829V','200132502829',2,'2001-11-20',22,0,0,0,0,0,0,266,0,0,0,1,0,0,0,'2024-10-13 15:01:51','2025-02-25 16:03:03',NULL,0,0,NULL),(500617,300083,'USJP/NAE/06/CWA/17PF',NULL,NULL,53,687,51,5101,NULL,51,5101,NULL,'J.M.K.N.','Jayasundara Mudiyanselage Kalana Nawodya','Jayasundara',16,1,3,9,'37/1 Hulogedara','','',1283,'37/1 Hulogedara','','',1283,NULL,'kalananawodya11333@gmail.com',25,NULL,NULL,NULL,'2024-09-05','2025-02-04',NULL,NULL,49825.00,NULL,NULL,'1970-01-01','1970-01-01',NULL,NULL,NULL,71,12466,'2024-10-13',NULL,NULL,NULL,NULL,12466,'2024-10-13',NULL,NULL,1,111,145,141,41,'0755415084',NULL,NULL,NULL,'200331701000','2003-11-12','033171000V','200331701000',2,'2003-11-12',22,0,0,0,0,0,0,266,0,0,0,1,0,0,0,'2024-10-13 11:28:18','2025-02-25 16:03:03',NULL,0,0,NULL),(500807,300084,'USJP/NAE/06/CWA/10PF',NULL,NULL,53,687,2,201,NULL,2,201,NULL,'T.M.A.S.','Thennakoon Mudiyanselage Aravinda Sampath','Thennakoon',16,1,3,9,'Yaya 07 Dangaswewa','Saliyawewa Junction','',1412,'Yaya 07 Dangaswewa','Saliyawewa Junction','',1412,NULL,NULL,25,NULL,NULL,NULL,'2024-09-05','2025-02-04',NULL,NULL,49825.00,NULL,NULL,'1970-01-01','1970-01-01',NULL,NULL,NULL,71,12466,'2024-11-06',NULL,NULL,NULL,NULL,12466,'2024-11-06',NULL,NULL,1,111,145,141,41,'0779604592',NULL,NULL,NULL,'981582160V','1998-06-06','981582160V','199815802160',1,'1998-06-06',22,0,0,0,0,0,0,266,0,0,0,0,0,0,0,'2024-11-06 10:05:36','2025-02-25 16:03:03',NULL,0,0,NULL),(500614,300085,'USJP/NAE/06/CWA/09PF',NULL,NULL,53,687,10,1006,NULL,10,1006,NULL,'W.P.A.K.N.','Wickrama Arachchi Pathiranalage Kaveesh Niroshana','Senevirathna',16,1,3,9,'83/4 Borukgamuwa','','',187,'83/4 Borukgamuwa','','',187,NULL,'kkvishniroshana@gmail.com',25,NULL,NULL,NULL,'2024-09-05','2025-02-04',NULL,NULL,49825.00,NULL,NULL,'1970-01-01','1970-01-01',NULL,NULL,NULL,71,12466,'2024-10-12',NULL,NULL,NULL,NULL,12466,'2024-10-12',NULL,NULL,1,111,145,141,41,'0755755596',NULL,NULL,NULL,'200225701252','2002-09-13','022571252V','200225701252',2,'2002-09-13',22,0,0,0,0,0,0,266,0,0,0,1,0,0,0,'2024-10-12 13:24:14','2025-02-25 16:03:03',NULL,0,0,NULL),(501359,500853,'',NULL,NULL,52,530,1,107,NULL,1,107,NULL,'H.K.U.','Hiniduma Kapuge Udara','Dewmini',16,2,NULL,NULL,'42/3 Yowun Mawatha','','',9,'42/3 Yowun Mawatha','','',9,NULL,NULL,25,NULL,NULL,NULL,'2025-06-30','2026-06-29',NULL,NULL,68145.00,NULL,NULL,NULL,NULL,NULL,NULL,NULL,76,11420,'2025-07-22',NULL,NULL,11542,'2025-07-22',11420,'2025-07-22',NULL,NULL,1,110,112,140,41,'0705735617',NULL,NULL,NULL,'200076802234','2000-09-24','007682234V','200076802234',2,'2000-09-24',117,0,0,0,0,0,0,266,0,0,0,0,0,0,0,'2025-07-22 11:20:32','2025-07-22 11:20:32',NULL,0,0,NULL);
/*!40000 ALTER TABLE `employees` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-08-05 11:49:15
