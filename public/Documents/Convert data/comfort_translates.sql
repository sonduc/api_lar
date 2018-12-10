-- MySQL dump 10.16  Distrib 10.1.35-MariaDB, for Win32 (AMD64)
--
-- Host: 127.0.0.1    Database: api-westay
-- ------------------------------------------------------
-- Server version	10.3.10-MariaDB-1:10.3.10+maria~bionic-log

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `comfort_translates`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `comfort_translates`
--

INSERT INTO `comfort_translates` (`id`, `comfort_id`, `name`, `description`, `lang`, `deleted_at`, `created_at`, `updated_at`) VALUES (1,8,'Máy giặt',NULL,'vi',NULL,'2017-04-24 04:56:01','2017-06-23 08:32:28'),(2,9,'Wifi',NULL,'vi',NULL,'2017-05-09 05:43:30','2017-11-10 03:14:03'),(3,10,'Tivi',NULL,'vi',NULL,'2017-06-06 18:09:57','2017-11-10 03:14:08'),(4,11,'Dầu gội, dầu xả',NULL,'vi',NULL,'2017-06-06 18:15:30','2017-06-06 18:15:30'),(5,12,'Nước khoáng',NULL,'vi',NULL,'2017-06-06 18:15:33','2017-06-06 18:15:33'),(6,13,'Khăn tắm',NULL,'vi',NULL,'2017-06-06 18:15:46','2017-06-06 18:15:46'),(7,14,'Kem đánh răng',NULL,'vi',NULL,'2017-06-06 18:15:56','2017-06-06 18:15:56'),(8,15,'Sữa tắm',NULL,'vi',NULL,'2017-06-06 18:16:04','2017-06-23 08:31:15'),(9,16,'Bếp ga/điện',NULL,'vi',NULL,'2017-06-23 08:29:02','2017-06-23 08:29:02'),(10,17,'Lò vi sóng',NULL,'vi',NULL,'2017-06-23 08:29:35','2017-06-23 08:29:35'),(11,18,'Tủ lạnh',NULL,'vi',NULL,'2017-06-23 08:30:21','2017-06-23 08:30:21'),(12,19,'Bàn chải',NULL,'vi',NULL,'2017-06-23 08:31:42','2017-06-23 08:31:42'),(13,20,'Điều hòa',NULL,'vi',NULL,'2017-06-23 08:32:06','2017-06-23 08:32:06'),(14,21,'Giấy vệ sinh',NULL,'vi',NULL,'2017-06-23 08:33:06','2017-06-23 08:33:06'),(15,22,'Giấy ăn',NULL,'vi',NULL,'2017-06-23 08:33:10','2017-06-23 08:33:10'),(16,23,'Thang máy',NULL,'vi',NULL,'2017-11-01 08:07:38','2017-11-10 03:13:36'),(17,24,'Bồn tắm',NULL,'vi',NULL,'2017-11-01 08:08:33','2017-11-10 03:13:46'),(18,25,'Cho phép thú nuôi',NULL,'vi',NULL,'2017-11-01 08:09:41','2017-11-10 03:13:56'),(19,26,'Bữa sáng',NULL,'vi',NULL,'2017-11-01 08:12:12','2017-11-01 08:12:12'),(20,27,'Bể bơi',NULL,'vi',NULL,'2017-11-01 08:12:18','2017-11-01 08:12:18'),(21,28,'Phòng Gym',NULL,'vi',NULL,'2017-11-01 08:12:27','2017-11-01 08:12:27'),(22,29,'Máy sấy tóc',NULL,'vi',NULL,'2017-11-01 08:12:32','2017-11-01 08:12:32'),(23,30,'Lối đi cho người khuyết tật',NULL,'vi',NULL,'2017-11-01 08:12:46','2017-11-01 08:12:46'),(24,31,'Lò sưởi',NULL,'vi',NULL,'2017-11-01 08:12:53','2017-11-01 08:12:53'),(25,32,'Internet',NULL,'vi',NULL,'2017-11-01 08:13:06','2017-11-01 08:13:06'),(26,34,'An toàn cho trẻ em',NULL,'vi',NULL,'2017-11-01 08:13:22','2017-11-01 08:13:22'),(27,35,'Góc làm việc',NULL,'vi',NULL,'2017-11-01 08:13:31','2017-11-01 08:13:31'),(28,36,'Móc treo quần áo',NULL,'vi',NULL,'2017-11-01 08:13:38','2017-11-01 08:13:38'),(29,37,'Truyền hình cáp',NULL,'vi',NULL,'2017-11-01 08:13:49','2017-11-01 08:13:49'),(30,38,'Chỗ để xe máy',NULL,'vi',NULL,'2017-11-08 09:55:36','2017-11-08 09:55:36'),(31,39,'Chỗ để ô tô',NULL,'vi',NULL,'2017-11-08 09:55:42','2017-11-08 09:55:42'),(32,40,'Nơi phơi đồ',NULL,'vi',NULL,'2017-11-08 09:56:15','2017-11-08 09:56:15'),(33,41,'Khu giặt giũ',NULL,'vi',NULL,'2017-11-08 09:56:36','2017-11-08 09:56:36'),(34,42,'Lò nướng',NULL,'vi',NULL,'2017-11-09 03:48:33','2017-11-09 03:48:33'),(35,43,'BBQ',NULL,'vi',NULL,'2017-11-10 03:14:23','2017-11-10 03:14:23'),(36,44,'Ban công',NULL,'vi',NULL,'2017-11-10 03:14:43','2017-11-10 03:14:43'),(37,45,'Cảnh quan đẹp',NULL,'vi',NULL,'2017-11-10 03:15:05','2017-11-10 03:15:05'),(38,46,'Hướng biển',NULL,'vi',NULL,'2017-11-10 03:15:12','2017-11-10 03:15:12'),(39,47,'Gần sân golf',NULL,'vi',NULL,'2017-11-10 03:15:25','2017-11-10 03:15:25'),(40,48,'Câu cá',NULL,'vi',NULL,'2017-11-10 03:15:29','2017-11-10 03:15:29'),(41,49,'Vườn',NULL,'vi',NULL,'2017-11-16 02:45:16','2017-11-16 02:45:16'),(42,50,'Không gian thư giãn ngoài trời',NULL,'vi',NULL,'2017-11-16 02:53:19','2017-11-16 02:53:19');
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2018-12-10 10:44:59
