CREATE DATABASE  IF NOT EXISTS `sapient` /*!40100 DEFAULT CHARACTER SET latin1 */;
USE `sapient`;
-- MySQL dump 10.13  Distrib 5.6.17, for Win32 (x86)
--
-- Host: mansystems.dedicated.co.za    Database: sapient
-- ------------------------------------------------------
-- Server version	5.5.38-0ubuntu0.12.04.1

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
-- Table structure for table `store`
--

DROP TABLE IF EXISTS `store`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `store` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `sid` varchar(50) DEFAULT NULL COMMENT 'Sapient ID',
  `host` varchar(150) DEFAULT NULL,
  `name` varchar(50) DEFAULT NULL,
  `logo` varchar(50) DEFAULT NULL,
  `theme` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `HOST` (`host`),
  UNIQUE KEY `SAPIENT_ID` (`sid`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `store`
--

LOCK TABLES `store` WRITE;
/*!40000 ALTER TABLE `store` DISABLE KEYS */;
INSERT INTO `store` VALUES (2,'STR_2','castlewalk.boekwinkel.info','Castlewalk','castlewalk.png','theme-castlewalk.css'),(3,'STR_3','klofies.boekwinkel.info',NULL,'klofies.png',NULL),(4,'STR_4','kempies.boekwinkel.info',NULL,'kempies.png',NULL),(5,'STR_5','midstreamcollege.boekwinkel.info',NULL,'midstream-college.png',NULL),(6,'STR_7','midstreamprimary.boekwinkel.info',NULL,'midstream-primary.png',NULL),(7,'STR_9','duo-edu.boekwinkel.info',NULL,'duoedu.png',NULL),(8,'STR_10','garsies.boekwinkel.info',NULL,'garsies.png',NULL),(9,'STR_11','midstreamridge.boekwinkel.info',NULL,'midstream-ridge.png',NULL),(10,'STR_20','cbc.boekwinkel.info','cbc ','CBCbookshop.png',NULL);
/*!40000 ALTER TABLE `store` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2014-10-31  9:29:31
