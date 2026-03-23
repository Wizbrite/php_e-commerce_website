-- MySQL dump 10.13  Distrib 8.0.30, for Win64 (x86_64)
--
-- Host: localhost    Database: BA2A_PHP
-- ------------------------------------------------------
-- Server version	8.0.41

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `cart`
--

DROP TABLE IF EXISTS `cart`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cart` (
  `cart_id` int NOT NULL AUTO_INCREMENT,
  `product_id` int NOT NULL,
  `user_id` int NOT NULL,
  `quantity` int NOT NULL DEFAULT '1',
  PRIMARY KEY (`cart_id`),
  KEY `product_id` (`product_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `product` (`product_id`) ON DELETE CASCADE,
  CONSTRAINT `cart_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cart`
--

LOCK TABLES `cart` WRITE;
/*!40000 ALTER TABLE `cart` DISABLE KEYS */;
INSERT INTO `cart` VALUES (18,5,1,1),(20,3,1,1),(21,4,1,6);
/*!40000 ALTER TABLE `cart` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `category`
--

DROP TABLE IF EXISTS `category`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `category` (
  `category_id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `description` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`category_id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `category`
--

LOCK TABLES `category` WRITE;
/*!40000 ALTER TABLE `category` DISABLE KEYS */;
INSERT INTO `category` VALUES (1,'Electronics','electronics','Gadgets, phones, computers and more','2026-03-17 10:09:13'),(2,'Clothing','clothing','Fashion and apparel for all','2026-03-17 10:09:13'),(3,'Food & Drink','food-drink','Fresh and packaged food products','2026-03-17 10:09:13'),(4,'Home & Living','home-living','Furniture, decor and household items','2026-03-17 10:09:13'),(5,'Sports','sports','Sporting goods and fitness equipment','2026-03-17 10:09:13'),(6,'Education','education','All products related to academic work','2026-03-17 10:26:36');
/*!40000 ALTER TABLE `category` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payment`
--

DROP TABLE IF EXISTS `payment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `payment` (
  `payment_id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `product_id` int NOT NULL,
  `quantity` int NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `status` varchar(30) NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`payment_id`),
  KEY `user_id` (`user_id`),
  KEY `product_id` (`product_id`),
  CONSTRAINT `payment_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`),
  CONSTRAINT `payment_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `product` (`product_id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payment`
--

LOCK TABLES `payment` WRITE;
/*!40000 ALTER TABLE `payment` DISABLE KEYS */;
INSERT INTO `payment` VALUES (1,1234567890,1,10,100000.00,'paid','2026-03-17 08:28:54'),(2,1234567890,2,3,360000.00,'paid','2026-03-17 08:28:54'),(3,1234567892,2,1,120000.00,'paid','2026-03-17 10:30:49'),(4,1234567892,2,6,720000.00,'paid','2026-03-17 10:32:58'),(5,1234567890,2,5,600000.00,'paid','2026-03-17 10:41:15'),(6,1234567890,3,2,200000.00,'paid','2026-03-17 10:41:15'),(7,1234567893,3,1,100000.00,'paid','2026-03-18 07:20:25'),(8,1234567890,2,3,360000.00,'paid','2026-03-18 09:43:51'),(9,1234567890,5,1,5000.00,'paid','2026-03-18 09:46:02'),(10,1234567890,4,11,1100.00,'paid','2026-03-18 10:14:07'),(11,1234567890,1,2,20000.00,'paid','2026-03-18 10:14:07'),(12,1234567890,5,1,5000.00,'paid','2026-03-18 10:21:03'),(13,1,5,1,5000.00,'paid','2026-03-18 10:25:33'),(14,1234567894,2,1,120000.00,'paid','2026-03-18 10:58:21');
/*!40000 ALTER TABLE `payment` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `product`
--

DROP TABLE IF EXISTS `product`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `product` (
  `product_id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(150) NOT NULL,
  `description` text,
  `price` decimal(10,2) NOT NULL,
  `quantity` int NOT NULL DEFAULT '0',
  `image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `category_id` int DEFAULT NULL,
  PRIMARY KEY (`product_id`),
  KEY `fk_product_category` (`category_id`),
  CONSTRAINT `fk_product_category` FOREIGN KEY (`category_id`) REFERENCES `category` (`category_id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `product`
--

LOCK TABLES `product` WRITE;
/*!40000 ALTER TABLE `product` DISABLE KEYS */;
INSERT INTO `product` VALUES (1,'Nike Sneakers','Shoes for the dust',10000.00,8,'product_1773735605.webp','2026-03-17 08:20:05',2),(2,'Lenovo Laptop','Fast and reliable PC',120000.00,11,'product_1773735984.avif','2026-03-17 08:26:24',1),(3,'Infinix','Smart of the latest generation',100000.00,9,'product_1773738320.webp','2026-03-17 09:05:20',1),(4,'Pens','Steady ink flow ball point pens',100.00,89,'product_1773824817.webp','2026-03-18 09:06:57',6),(5,'Jeggings','Jegging for men',5000.00,97,'product_1773825111.webp','2026-03-18 09:11:51',2),(6,'Chips','Crunchy babies',500.00,100,'product_1773832701.png','2026-03-18 11:18:21',3),(7,'KFC','crispy toped fried KFC chicken',1000.00,200,'product_1773833135.jfif','2026-03-18 11:25:35',3);
/*!40000 ALTER TABLE `product` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user` (
  `user_id` int NOT NULL AUTO_INCREMENT,
  `user_name` varchar(50) NOT NULL,
  `user_password` varchar(200) DEFAULT NULL,
  `user_email` varchar(50) NOT NULL,
  `user_role` varchar(20) NOT NULL DEFAULT 'client',
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `user_email` (`user_email`)
) ENGINE=InnoDB AUTO_INCREMENT=1234567895 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user`
--

LOCK TABLES `user` WRITE;
/*!40000 ALTER TABLE `user` DISABLE KEYS */;
INSERT INTO `user` VALUES (1,'Njini Favour','$2y$10$z5NnEhx5nOuMcrcm2vLeUOnFsn25r08.fIIkMADm4sdO5Gg3fkXDu','testemail@gmail.com','client'),(4,'Njini Favour','$2y$10$GBCVDbK/4OfC5l.4EHc9Pe4.G6r6qqe0rSP8B/r1wLnDq2/EM3a.q','studentl@gmail.com','client'),(5,'Mark Lutter','$2y$10$UqfhpOlJuwxZ6FHczZOUGuEzGZykBoNUDdDZ.s085A3Tmy4U5m9i6','mark@gmail.com','client'),(1234567890,'Test Admin','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','admin@gmail.com','admin'),(1234567891,'Admin','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','admin@store.com','admin'),(1234567892,'Tokam Ndifor','$2y$10$icBVtfeUqNgzOWU34AWfi.TBfAVLIrpc0la.ZieoBCfJg2DXNvmu2','tokam@gmail.com','client'),(1234567893,'Serena William','$2y$10$6k4d5.88TyYYTFDxDyQkq.PKpAJ1mh61yyE9A2a7ufrXVPfKgQQ.2','serena@gmail.com','client'),(1234567894,'Yonta','$2y$10$IlKMN22XyQSAuKZ0XHg5gu2Cm4sH8YhChxHqxqOJK6Tmd4QP/meJi','yontabrice237@gmail.com','client');
/*!40000 ALTER TABLE `user` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-03-23 11:34:21
