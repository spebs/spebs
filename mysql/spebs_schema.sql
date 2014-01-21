-- MySQL dump 10.13  Distrib 5.1.63, for portbld-freebsd7.4 (amd64)
--
-- Host: localhost    Database: spebs_phase2
-- ------------------------------------------------------
-- Server version	5.1.63

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
-- Table structure for table `access_logs`
--

DROP TABLE IF EXISTS `access_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `access_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ip` varchar(40) CHARACTER SET latin1 DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `actiontime` datetime DEFAULT NULL,
  `action` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=154823 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `aggregation_per_connection`
--

DROP TABLE IF EXISTS `aggregation_per_connection`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `aggregation_per_connection` (
  `connection_id` int(11) NOT NULL DEFAULT '0',
  `isp_id` int(11) DEFAULT NULL,
  `workingDay` tinyint(4) DEFAULT NULL,
  `peakHour` tinyint(4) DEFAULT NULL,
  `avgup` double(32,2) DEFAULT NULL,
  `avgdown` double(32,2) DEFAULT NULL,
  `avgrtt` double(32,2) DEFAULT NULL,
  `avgloss` double(32,2) DEFAULT NULL,
  `avgjitter` double(32,2) DEFAULT NULL,
  `contract` varchar(23) CHARACTER SET utf8 DEFAULT NULL,
  `postal_code` int(11) DEFAULT NULL,
  `municipality` int(11) DEFAULT NULL,
  `prefecture` int(11) DEFAULT NULL,
  `periphery` int(11) DEFAULT NULL,
  `country` int(11) DEFAULT NULL,
  `connection_active` tinyint(4) DEFAULT NULL,
  `measurements_count` bigint(21) NOT NULL DEFAULT '0',
  KEY `connindex` (`connection_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `aggregation_per_connection_glasnost`
--

DROP TABLE IF EXISTS `aggregation_per_connection_glasnost`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `aggregation_per_connection_glasnost` (
  `connection_id` int(11) NOT NULL DEFAULT '0',
  `isp_id` int(11) DEFAULT NULL,
  `workingDay` tinyint(4) DEFAULT NULL,
  `peakHour` tinyint(4) DEFAULT NULL,
  `bittorent_throttled_measurements` decimal(23,0) DEFAULT NULL,
  `emule_throttled_measurements` decimal(23,0) DEFAULT NULL,
  `gnutella_throttled_measurements` decimal(23,0) DEFAULT NULL,
  `http_throttled_measurements` decimal(23,0) DEFAULT NULL,
  `ssh_throttled_measurements` decimal(23,0) DEFAULT NULL,
  `pop_throttled_measurements` decimal(23,0) DEFAULT NULL,
  `imap_throttled_measurements` decimal(23,0) DEFAULT NULL,
  `flash_throttled_measurements` decimal(23,0) DEFAULT NULL,
  `bittorent_measurements` bigint(21) NOT NULL DEFAULT '0',
  `emule_measurements` bigint(21) NOT NULL DEFAULT '0',
  `gnutella_measurements` bigint(21) NOT NULL DEFAULT '0',
  `http_measurements` bigint(21) NOT NULL DEFAULT '0',
  `ssh_measurements` bigint(21) NOT NULL DEFAULT '0',
  `pop_measurements` bigint(21) NOT NULL DEFAULT '0',
  `imap_measurements` bigint(21) NOT NULL DEFAULT '0',
  `flash_measurements` bigint(21) NOT NULL DEFAULT '0',
  `contract` varchar(23) CHARACTER SET utf8 DEFAULT NULL,
  `postal_code` int(11) DEFAULT NULL,
  `municipality` int(11) DEFAULT NULL,
  `prefecture` int(11) DEFAULT NULL,
  `periphery` int(11) DEFAULT NULL,
  `country` int(11) DEFAULT NULL,
  `measurements_count` bigint(21) NOT NULL DEFAULT '0',
  KEY `connindex` (`connection_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `aggregation_per_municipality`
--

DROP TABLE IF EXISTS `aggregation_per_municipality`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `aggregation_per_municipality` (
  `municipality` int(11) DEFAULT NULL,
  `isp_id` int(11) DEFAULT NULL,
  `workingDay` tinyint(4) DEFAULT NULL,
  `peakHour` tinyint(4) DEFAULT NULL,
  `contract` varchar(23) CHARACTER SET utf8 DEFAULT NULL,
  `avgup` double(36,6) DEFAULT NULL,
  `avgdown` double(36,6) DEFAULT NULL,
  `avgrtt` double(36,6) DEFAULT NULL,
  `avgloss` double(36,6) DEFAULT NULL,
  `avgjitter` double(36,6) DEFAULT NULL,
  `connections_count` bigint(21) NOT NULL DEFAULT '0',
  `measurements_sum` decimal(42,0) DEFAULT NULL,
  KEY `mindex` (`municipality`,`workingDay`,`peakHour`,`contract`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `aggregation_per_municipality_glasnost`
--

DROP TABLE IF EXISTS `aggregation_per_municipality_glasnost`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `aggregation_per_municipality_glasnost` (
  `municipality` int(11) DEFAULT NULL,
  `isp_id` int(11) DEFAULT NULL,
  `workingDay` tinyint(4) DEFAULT NULL,
  `peakHour` tinyint(4) DEFAULT NULL,
  `contract` varchar(23) CHARACTER SET utf8 DEFAULT NULL,
  `bittorent_throttled_connections` decimal(23,0) DEFAULT NULL,
  `bittorent_measurements` decimal(42,0) DEFAULT NULL,
  `connections_with_bittorent_measurements` decimal(23,0) DEFAULT NULL,
  `emule_throttled_connections` decimal(23,0) DEFAULT NULL,
  `emule_measurements` decimal(42,0) DEFAULT NULL,
  `connections_with_emule_measurements` decimal(23,0) DEFAULT NULL,
  `gnutella_throttled_connections` decimal(23,0) DEFAULT NULL,
  `gnutella_measurements` decimal(42,0) DEFAULT NULL,
  `connections_with_gnutella_measurements` decimal(23,0) DEFAULT NULL,
  `http_throttled_connections` decimal(23,0) DEFAULT NULL,
  `http_measurements` decimal(42,0) DEFAULT NULL,
  `connections_with_http_measurements` decimal(23,0) DEFAULT NULL,
  `ssh_throttled_connections` decimal(23,0) DEFAULT NULL,
  `ssh_measurements` decimal(42,0) DEFAULT NULL,
  `connections_with_ssh_measurements` decimal(23,0) DEFAULT NULL,
  `pop_throttled_connections` decimal(23,0) DEFAULT NULL,
  `pop_measurements` decimal(42,0) DEFAULT NULL,
  `connections_with_pop_measurements` decimal(23,0) DEFAULT NULL,
  `imap_throttled_connections` decimal(23,0) DEFAULT NULL,
  `imap_measurements` decimal(42,0) DEFAULT NULL,
  `connections_with_imap_measurements` decimal(23,0) DEFAULT NULL,
  `flash_throttled_connections` decimal(23,0) DEFAULT NULL,
  `flash_measurements` decimal(42,0) DEFAULT NULL,
  `connections_with_flash_measurements` decimal(23,0) DEFAULT NULL,
  `connections_count` bigint(21) NOT NULL DEFAULT '0',
  `measurements_sum` decimal(42,0) DEFAULT NULL,
  KEY `mindex` (`municipality`,`workingDay`,`peakHour`,`contract`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `aggregation_per_periphery`
--

DROP TABLE IF EXISTS `aggregation_per_periphery`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `aggregation_per_periphery` (
  `periphery` int(11) DEFAULT NULL,
  `isp_id` int(11) DEFAULT NULL,
  `workingDay` tinyint(4) DEFAULT NULL,
  `peakHour` tinyint(4) DEFAULT NULL,
  `contract` varchar(23) CHARACTER SET utf8 DEFAULT NULL,
  `avgup` double(36,6) DEFAULT NULL,
  `avgdown` double(36,6) DEFAULT NULL,
  `avgrtt` double(36,6) DEFAULT NULL,
  `avgloss` double(36,6) DEFAULT NULL,
  `avgjitter` double(36,6) DEFAULT NULL,
  `connections_count` bigint(21) NOT NULL DEFAULT '0',
  `measurements_sum` decimal(42,0) DEFAULT NULL,
  KEY `pindex` (`periphery`,`workingDay`,`peakHour`,`contract`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `aggregation_per_periphery_glasnost`
--

DROP TABLE IF EXISTS `aggregation_per_periphery_glasnost`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `aggregation_per_periphery_glasnost` (
  `periphery` int(11) DEFAULT NULL,
  `isp_id` int(11) DEFAULT NULL,
  `workingDay` tinyint(4) DEFAULT NULL,
  `peakHour` tinyint(4) DEFAULT NULL,
  `contract` varchar(23) CHARACTER SET utf8 DEFAULT NULL,
  `bittorent_throttled_connections` decimal(23,0) DEFAULT NULL,
  `bittorent_measurements` decimal(42,0) DEFAULT NULL,
  `connections_with_bittorent_measurements` decimal(23,0) DEFAULT NULL,
  `emule_throttled_connections` decimal(23,0) DEFAULT NULL,
  `emule_measurements` decimal(42,0) DEFAULT NULL,
  `connections_with_emule_measurements` decimal(23,0) DEFAULT NULL,
  `gnutella_throttled_connections` decimal(23,0) DEFAULT NULL,
  `gnutella_measurements` decimal(42,0) DEFAULT NULL,
  `connections_with_gnutella_measurements` decimal(23,0) DEFAULT NULL,
  `http_throttled_connections` decimal(23,0) DEFAULT NULL,
  `http_measurements` decimal(42,0) DEFAULT NULL,
  `connections_with_http_measurements` decimal(23,0) DEFAULT NULL,
  `ssh_throttled_connections` decimal(23,0) DEFAULT NULL,
  `ssh_measurements` decimal(42,0) DEFAULT NULL,
  `connections_with_ssh_measurements` decimal(23,0) DEFAULT NULL,
  `pop_throttled_connections` decimal(23,0) DEFAULT NULL,
  `pop_measurements` decimal(42,0) DEFAULT NULL,
  `connections_with_pop_measurements` decimal(23,0) DEFAULT NULL,
  `imap_throttled_connections` decimal(23,0) DEFAULT NULL,
  `imap_measurements` decimal(42,0) DEFAULT NULL,
  `connections_with_imap_measurements` decimal(23,0) DEFAULT NULL,
  `flash_throttled_connections` decimal(23,0) DEFAULT NULL,
  `flash_measurements` decimal(42,0) DEFAULT NULL,
  `connections_with_flash_measurements` decimal(23,0) DEFAULT NULL,
  `connections_count` bigint(21) NOT NULL DEFAULT '0',
  `measurements_sum` decimal(42,0) DEFAULT NULL,
  KEY `pindex` (`periphery`,`workingDay`,`peakHour`,`contract`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `aggregation_per_postal_code`
--

DROP TABLE IF EXISTS `aggregation_per_postal_code`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `aggregation_per_postal_code` (
  `postal_code` int(11) DEFAULT NULL,
  `isp_id` int(11) DEFAULT NULL,
  `workingDay` tinyint(4) DEFAULT NULL,
  `peakHour` tinyint(4) DEFAULT NULL,
  `contract` varchar(23) CHARACTER SET utf8 DEFAULT NULL,
  `avgup` double(36,6) DEFAULT NULL,
  `avgdown` double(36,6) DEFAULT NULL,
  `avgrtt` double(36,6) DEFAULT NULL,
  `avgloss` double(36,6) DEFAULT NULL,
  `avgjitter` double(36,6) DEFAULT NULL,
  `connections_count` bigint(21) NOT NULL DEFAULT '0',
  `measurements_sum` decimal(42,0) DEFAULT NULL,
  KEY `pcindex` (`postal_code`,`workingDay`,`peakHour`,`contract`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `aggregation_per_postal_code_glasnost`
--

DROP TABLE IF EXISTS `aggregation_per_postal_code_glasnost`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `aggregation_per_postal_code_glasnost` (
  `postal_code` int(11) DEFAULT NULL,
  `isp_id` int(11) DEFAULT NULL,
  `workingDay` tinyint(4) DEFAULT NULL,
  `peakHour` tinyint(4) DEFAULT NULL,
  `contract` varchar(23) CHARACTER SET utf8 DEFAULT NULL,
  `bittorent_throttled_connections` decimal(23,0) DEFAULT NULL,
  `bittorent_measurements` decimal(42,0) DEFAULT NULL,
  `connections_with_bittorent_measurements` decimal(23,0) DEFAULT NULL,
  `emule_throttled_connections` decimal(23,0) DEFAULT NULL,
  `emule_measurements` decimal(42,0) DEFAULT NULL,
  `connections_with_emule_measurements` decimal(23,0) DEFAULT NULL,
  `gnutella_throttled_connections` decimal(23,0) DEFAULT NULL,
  `gnutella_measurements` decimal(42,0) DEFAULT NULL,
  `connections_with_gnutella_measurements` decimal(23,0) DEFAULT NULL,
  `http_throttled_connections` decimal(23,0) DEFAULT NULL,
  `http_measurements` decimal(42,0) DEFAULT NULL,
  `connections_with_http_measurements` decimal(23,0) DEFAULT NULL,
  `ssh_throttled_connections` decimal(23,0) DEFAULT NULL,
  `ssh_measurements` decimal(42,0) DEFAULT NULL,
  `connections_with_ssh_measurements` decimal(23,0) DEFAULT NULL,
  `pop_throttled_connections` decimal(23,0) DEFAULT NULL,
  `pop_measurements` decimal(42,0) DEFAULT NULL,
  `connections_with_pop_measurements` decimal(23,0) DEFAULT NULL,
  `imap_throttled_connections` decimal(23,0) DEFAULT NULL,
  `imap_measurements` decimal(42,0) DEFAULT NULL,
  `connections_with_imap_measurements` decimal(23,0) DEFAULT NULL,
  `flash_throttled_connections` decimal(23,0) DEFAULT NULL,
  `flash_measurements` decimal(42,0) DEFAULT NULL,
  `connections_with_flash_measurements` decimal(23,0) DEFAULT NULL,
  `connections_count` bigint(21) NOT NULL DEFAULT '0',
  `measurements_sum` decimal(42,0) DEFAULT NULL,
  KEY `pcindex` (`postal_code`,`workingDay`,`peakHour`,`contract`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `aggregation_per_prefecture`
--

DROP TABLE IF EXISTS `aggregation_per_prefecture`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `aggregation_per_prefecture` (
  `prefecture` int(11) DEFAULT NULL,
  `isp_id` int(11) DEFAULT NULL,
  `workingDay` tinyint(4) DEFAULT NULL,
  `peakHour` tinyint(4) DEFAULT NULL,
  `contract` varchar(23) CHARACTER SET utf8 DEFAULT NULL,
  `avgup` double(36,6) DEFAULT NULL,
  `avgdown` double(36,6) DEFAULT NULL,
  `avgrtt` double(36,6) DEFAULT NULL,
  `avgloss` double(36,6) DEFAULT NULL,
  `avgjitter` double(36,6) DEFAULT NULL,
  `connections_count` bigint(21) NOT NULL DEFAULT '0',
  `measurements_sum` decimal(42,0) DEFAULT NULL,
  KEY `pindex` (`prefecture`,`workingDay`,`peakHour`,`contract`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `aggregation_per_prefecture_glasnost`
--

DROP TABLE IF EXISTS `aggregation_per_prefecture_glasnost`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `aggregation_per_prefecture_glasnost` (
  `prefecture` int(11) DEFAULT NULL,
  `isp_id` int(11) DEFAULT NULL,
  `workingDay` tinyint(4) DEFAULT NULL,
  `peakHour` tinyint(4) DEFAULT NULL,
  `contract` varchar(23) CHARACTER SET utf8 DEFAULT NULL,
  `bittorent_throttled_connections` decimal(23,0) DEFAULT NULL,
  `bittorent_measurements` decimal(42,0) DEFAULT NULL,
  `connections_with_bittorent_measurements` decimal(23,0) DEFAULT NULL,
  `emule_throttled_connections` decimal(23,0) DEFAULT NULL,
  `emule_measurements` decimal(42,0) DEFAULT NULL,
  `connections_with_emule_measurements` decimal(23,0) DEFAULT NULL,
  `gnutella_throttled_connections` decimal(23,0) DEFAULT NULL,
  `gnutella_measurements` decimal(42,0) DEFAULT NULL,
  `connections_with_gnutella_measurements` decimal(23,0) DEFAULT NULL,
  `http_throttled_connections` decimal(23,0) DEFAULT NULL,
  `http_measurements` decimal(42,0) DEFAULT NULL,
  `connections_with_http_measurements` decimal(23,0) DEFAULT NULL,
  `ssh_throttled_connections` decimal(23,0) DEFAULT NULL,
  `ssh_measurements` decimal(42,0) DEFAULT NULL,
  `connections_with_ssh_measurements` decimal(23,0) DEFAULT NULL,
  `pop_throttled_connections` decimal(23,0) DEFAULT NULL,
  `pop_measurements` decimal(42,0) DEFAULT NULL,
  `connections_with_pop_measurements` decimal(23,0) DEFAULT NULL,
  `imap_throttled_connections` decimal(23,0) DEFAULT NULL,
  `imap_measurements` decimal(42,0) DEFAULT NULL,
  `connections_with_imap_measurements` decimal(23,0) DEFAULT NULL,
  `flash_throttled_connections` decimal(23,0) DEFAULT NULL,
  `flash_measurements` decimal(42,0) DEFAULT NULL,
  `connections_with_flash_measurements` decimal(23,0) DEFAULT NULL,
  `connections_count` bigint(21) NOT NULL DEFAULT '0',
  `measurements_sum` decimal(42,0) DEFAULT NULL,
  KEY `pindex` (`prefecture`,`workingDay`,`peakHour`,`contract`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `connection`
--

DROP TABLE IF EXISTS `connection`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `connection` (
  `connection_id` int(11) NOT NULL AUTO_INCREMENT,
  `description` varchar(50) DEFAULT NULL,
  `isp_id` int(11) DEFAULT NULL,
  `exchange_id` int(11) DEFAULT NULL,
  `distance_to_exchange` int(11) DEFAULT NULL,
  `max_bw_ondistance` int(11) DEFAULT NULL,
  `max_vdslbw_ondistance` int(11) DEFAULT NULL,
  `address` varchar(50) DEFAULT NULL,
  `street` varchar(50) DEFAULT NULL,
  `str_number` varchar(5) DEFAULT NULL,
  `containing_region` int(11) DEFAULT NULL,
  `postal_code` varchar(10) DEFAULT NULL,
  `city` varchar(20) DEFAULT NULL,
  `country` varchar(20) DEFAULT NULL,
  `longitude` decimal(10,7) NOT NULL,
  `latitude` decimal(10,7) NOT NULL,
  `local_loop_name` varchar(50) DEFAULT NULL,
  `contention_ratio` decimal(10,0) DEFAULT NULL,
  `status` tinyint(4) DEFAULT NULL,
  `creation_time` datetime DEFAULT NULL,
  `destruction_time` datetime DEFAULT NULL,
  `purchased_bandwidth_dl_kbps` int(11) DEFAULT NULL,
  `purchased_bandwidth_ul_kbps` int(11) DEFAULT NULL,
  PRIMARY KEY (`connection_id`),
  KEY `lng_index` (`longitude`),
  KEY `lat_index` (`latitude`),
  KEY `municipality_on_connection` (`containing_region`),
  KEY `postal_code_on_connection` (`postal_code`),
  KEY `latlng_index` (`longitude`,`latitude`)
) ENGINE=MyISAM AUTO_INCREMENT=6340 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `countries`
--

DROP TABLE IF EXISTS `countries`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `countries` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name_el` varchar(50) DEFAULT NULL,
  `name_en` varchar(50) DEFAULT NULL,
  `longitude` decimal(10,7) DEFAULT NULL,
  `latitude` decimal(10,7) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `lat_index` (`latitude`),
  KEY `lng_index` (`longitude`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `deactivcons`
--

DROP TABLE IF EXISTS `deactivcons`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `deactivcons` (
  `cid1` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `detailed_periphery_polygons`
--

DROP TABLE IF EXISTS `detailed_periphery_polygons`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `detailed_periphery_polygons` (
  `polid` int(11) NOT NULL DEFAULT '0',
  `prefecture_id` int(11) DEFAULT NULL,
  `periphery_id` int(11) DEFAULT NULL,
  `aa` int(11) NOT NULL DEFAULT '0',
  `SHAPE` geometry NOT NULL,
  `longitude` decimal(10,7) DEFAULT NULL,
  `latitude` decimal(10,7) DEFAULT NULL,
  `enc_pol_points` text CHARACTER SET latin1,
  `enc_pol_levels` text CHARACTER SET latin1,
  `comment` varchar(30) CHARACTER SET latin1 DEFAULT NULL,
  PRIMARY KEY (`polid`,`aa`),
  KEY `lng_on_det_pols` (`longitude`),
  KEY `lat_on_det_pols` (`latitude`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `detailed_prefecture_polygons`
--

DROP TABLE IF EXISTS `detailed_prefecture_polygons`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `detailed_prefecture_polygons` (
  `polid` int(11) NOT NULL DEFAULT '0',
  `prefecture_id` int(11) DEFAULT NULL,
  `periphery_id` int(11) DEFAULT NULL,
  `aa` int(11) NOT NULL DEFAULT '0',
  `SHAPE` geometry NOT NULL,
  `longitude` decimal(10,7) DEFAULT NULL,
  `latitude` decimal(10,7) DEFAULT NULL,
  `enc_pol_points` text,
  `enc_pol_levels` text,
  `comment` varchar(30) DEFAULT NULL,
  `record_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`polid`,`aa`),
  KEY `lng_on_det_pols` (`longitude`),
  KEY `lat_on_det_pols` (`latitude`),
  SPATIAL KEY `detailed_polys` (`SHAPE`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `generic_measurement`
--

DROP TABLE IF EXISTS `generic_measurement`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `generic_measurement` (
  `measurement_id` int(11) NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `reporting_host` varchar(50) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `connection_id` int(11) DEFAULT NULL,
  `measurement_tool` varchar(255) DEFAULT NULL,
  `version` varchar(255) DEFAULT NULL,
  `report_host` varchar(255) DEFAULT NULL,
  `report_port` int(10) unsigned DEFAULT NULL,
  `upstream_bw` double DEFAULT NULL,
  `downstream_bw` double DEFAULT NULL,
  `rtt` double DEFAULT NULL,
  `loss` double DEFAULT NULL,
  `jitter` int(11) DEFAULT NULL,
  PRIMARY KEY (`measurement_id`),
  KEY `created` (`created`),
  KEY `measurement_tool` (`measurement_tool`),
  KEY `userid` (`user_id`),
  KEY `connid` (`connection_id`)
) ENGINE=MyISAM AUTO_INCREMENT=37108 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 trigger insert_measurement_to_stats AFTER INSERT ON generic_measurement FOR EACH ROW INSERT INTO generic_measurements_stats SELECT NEW.measurement_id, NEW.connection_id, c.isp_id, DATE(NEW.created), DATE_FORMAT(NEW.created,"%w"), (49 mod (DATE_FORMAT(NEW.created,"%w") + 1)) AND 1, TIME(NEW.created), TIME_FORMAT(NEW.created,"%H"), TIME_FORMAT(NEW.created,"%H") div 8, NEW.upstream_bw,NEW.downstream_bw,NEW.rtt,NEW.loss,NEW.jitter, c.postal_code,c.municipality,m.prefecture,p.periphery,pr.country FROM connection c JOIN municipalities m ON c.municipality=m.id JOIN prefectures p ON m.prefecture=p.id JOIN peripheries pr ON p.periphery=pr.id WHERE c.connection_id=NEW.connection_id */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `generic_measurements_stats`
--

DROP TABLE IF EXISTS `generic_measurements_stats`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `generic_measurements_stats` (
  `mid` int(11) DEFAULT NULL,
  `connection_id` int(11) NOT NULL DEFAULT '0',
  `isp_id` int(11) DEFAULT NULL,
  `date_created` date NOT NULL DEFAULT '0000-00-00',
  `weekday` tinyint(4) DEFAULT NULL,
  `workingDay` tinyint(4) DEFAULT NULL,
  `time_created` time NOT NULL DEFAULT '00:00:00',
  `timehour` tinyint(4) DEFAULT NULL,
  `peakHour` tinyint(4) DEFAULT NULL,
  `upstream_bw` double DEFAULT NULL,
  `downstream_bw` double DEFAULT NULL,
  `rtt` double DEFAULT NULL,
  `loss` double DEFAULT NULL,
  `jitter` double DEFAULT NULL,
  `postal_code` int(11) DEFAULT NULL,
  `municipality` int(11) DEFAULT NULL,
  `prefecture` int(11) DEFAULT NULL,
  `periphery` int(11) DEFAULT NULL,
  `country` int(11) DEFAULT NULL,
  PRIMARY KEY (`connection_id`,`date_created`,`time_created`),
  KEY `date_on_measurements` (`date_created`),
  KEY `time_on_measurements` (`time_created`),
  KEY `weekday_on_measurements` (`weekday`),
  KEY `timehour_on_measurements` (`timehour`),
  KEY `peripheries_on_measurements_stats` (`periphery`),
  KEY `prefectures_on_measurements_stats` (`prefecture`),
  KEY `municipality_on_measurements_stats` (`municipality`),
  KEY `postal_code_on_measurements_stats` (`postal_code`),
  KEY `connid_on_measurements_stats` (`connection_id`),
  KEY `isp_on_measurements_stats` (`isp_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `geometry_columns`
--

DROP TABLE IF EXISTS `geometry_columns`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `geometry_columns` (
  `F_TABLE_CATALOG` varchar(256) DEFAULT NULL,
  `F_TABLE_SCHEMA` varchar(256) DEFAULT NULL,
  `F_TABLE_NAME` varchar(256) NOT NULL,
  `F_GEOMETRY_COLUMN` varchar(256) NOT NULL,
  `COORD_DIMENSION` int(11) DEFAULT NULL,
  `SRID` int(11) DEFAULT NULL,
  `TYPE` varchar(256) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `glasnost_measurement`
--

DROP TABLE IF EXISTS `glasnost_measurement`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `glasnost_measurement` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `protocol1` varchar(255) DEFAULT NULL,
  `protocol2` varchar(255) DEFAULT NULL,
  `hostip` varchar(255) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `connection_id` int(11) DEFAULT NULL,
  `timestamp` bigint(20) unsigned DEFAULT NULL,
  `duration` int(10) unsigned DEFAULT NULL,
  `repetitions` int(10) unsigned DEFAULT NULL,
  `port1` int(10) unsigned DEFAULT NULL,
  `port2` int(10) unsigned DEFAULT NULL,
  `server` varchar(255) DEFAULT NULL,
  `upload_indication` tinyint(1) DEFAULT NULL,
  `download_indication` tinyint(1) DEFAULT NULL,
  `max_pr_upload` int(10) unsigned DEFAULT NULL,
  `max_cf_upload` int(10) unsigned DEFAULT NULL,
  `max_pr_download` int(10) unsigned DEFAULT NULL,
  `max_cf_download` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id` (`user_id`,`connection_id`,`timestamp`)
) ENGINE=MyISAM AUTO_INCREMENT=2862 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 trigger insert_glasnost_measurement_to_stats after insert on glasnost_measurement for each row INSERT INTO glasnost_measurements_stats SELECT NEW.id, NEW.connection_id, NEW.user_id, c.isp_id, DATE(NEW.created), DATE_FORMAT(NEW.created,"%w"), (49 mod (DATE_FORMAT(NEW.created,"%w") + 1)) AND 1, TIME(NEW.created), TIME_FORMAT(NEW.created,"%H"), TIME_FORMAT(NEW.created,"%H") div 8, NEW.protocol1, NEW.protocol2, NEW.hostip, NEW.duration, NEW.repetitions, NEW.port1, NEW.port2, NEW.server, NEW.upload_indication, NEW.download_indication, NEW.max_pr_upload, NEW.max_cf_upload, NEW.max_pr_download, NEW.max_cf_download, c.postal_code, c.municipality, m.prefecture, p.periphery, pr.country FROM connection c JOIN municipalities m ON c.municipality=m.id JOIN prefectures p ON m.prefecture=p.id JOIN peripheries pr ON p.periphery=pr.id WHERE c.connection_id=NEW.connection_id */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `glasnost_measurements_stats`
--

DROP TABLE IF EXISTS `glasnost_measurements_stats`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `glasnost_measurements_stats` (
  `mid` int(11) DEFAULT NULL,
  `connection_id` int(11) NOT NULL DEFAULT '0',
  `user_id` int(11) DEFAULT NULL,
  `isp_id` int(11) DEFAULT NULL,
  `date_created` date NOT NULL DEFAULT '0000-00-00',
  `weekday` tinyint(4) DEFAULT NULL,
  `workingDay` tinyint(4) DEFAULT NULL,
  `time_created` time NOT NULL DEFAULT '00:00:00',
  `timehour` tinyint(4) DEFAULT NULL,
  `peakHour` tinyint(4) DEFAULT NULL,
  `protocol1` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `protocol2` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `hostip` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `duration` int(11) DEFAULT NULL,
  `repetitions` int(11) DEFAULT NULL,
  `port1` int(11) DEFAULT NULL,
  `port2` int(11) DEFAULT NULL,
  `server` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `upload_indication` tinyint(4) DEFAULT NULL,
  `download_indication` tinyint(4) DEFAULT NULL,
  `max_pr_upload` int(11) DEFAULT NULL,
  `max_cf_upload` int(11) DEFAULT NULL,
  `max_pr_download` int(11) DEFAULT NULL,
  `max_cf_download` int(11) DEFAULT NULL,
  `postal_code` int(11) DEFAULT NULL,
  `municipality` int(11) DEFAULT NULL,
  `prefecture` int(11) DEFAULT NULL,
  `periphery` int(11) DEFAULT NULL,
  `country` int(11) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Temporary table structure for view `glasnost_measurements_view`
--

DROP TABLE IF EXISTS `glasnost_measurements_view`;
/*!50001 DROP VIEW IF EXISTS `glasnost_measurements_view`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `glasnost_measurements_view` (
  `measurement_id` int(11),
  `measurement_date` varchar(10),
  `measurement_day` varchar(64),
  `measurement_time` varchar(13),
  `server` varchar(255),
  `service_protocol_name` varchar(255),
  `service_protocol_download_shaped` varchar(3),
  `service_protocol_max_download_bandwidth_kbps` int(10) unsigned,
  `control_flow_max_download_bandwidth_kbps` int(10) unsigned,
  `service_protocol_upload_shaped` varchar(3),
  `service_protocol_max_upload_bandwidth_kbps` int(10) unsigned,
  `control_flow_max_upload_bandwidth_kbps` int(10) unsigned,
  `connection_id` int(11),
  `ISP` varchar(50),
  `contract_download_mbps` decimal(14,4),
  `contract_upload_mbps` decimal(14,4),
  `connection_address` varchar(50),
  `connection_longitude` decimal(10,7),
  `connection_latitude` decimal(10,7),
  `connection_postal_code` varchar(10),
  `connection_region_id` int(11),
  `connection_region` varchar(50),
  `connection_exchange` int(11),
  `connection_exchange_longitude` decimal(10,7),
  `connection_exchange_latitude` decimal(10,7),
  `estimated_distance_to_exchange_m` int(11),
  `exchange_polygon_defined` varchar(3),
  `connection_outer_region1_id` int(11),
  `connection_region_l1` varchar(50),
  `connection_outer_region2_id` int(11),
  `connection_region_l2` varchar(50)
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `ipranges`
--

DROP TABLE IF EXISTS `ipranges`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ipranges` (
  `range_id` int(11) NOT NULL AUTO_INCREMENT,
  `isp_id` int(11) DEFAULT NULL,
  `ip_start` varchar(20) DEFAULT NULL,
  `ip_end` varchar(20) DEFAULT NULL,
  `ip_version` varchar(10) DEFAULT NULL,
  `creation_time` datetime DEFAULT NULL,
  `destruction_time` datetime DEFAULT NULL,
  `status` tinyint(4) DEFAULT NULL,
  PRIMARY KEY (`range_id`)
) ENGINE=MyISAM AUTO_INCREMENT=23 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ipv4_to_isp`
--

DROP TABLE IF EXISTS `ipv4_to_isp`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ipv4_to_isp` (
  `ip_start` int(4) unsigned NOT NULL DEFAULT '0',
  `ip_stop` int(4) unsigned NOT NULL DEFAULT '0',
  `ts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `isp_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`ip_start`,`ip_stop`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ipv6_to_isp`
--

DROP TABLE IF EXISTS `ipv6_to_isp`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ipv6_to_isp` (
  `entry_id` int(11) NOT NULL AUTO_INCREMENT,
  `start` varchar(39) NOT NULL,
  `stop` varchar(39) NOT NULL,
  `prefix` varchar(255) DEFAULT NULL,
  `ts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `isp_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`entry_id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `isp`
--

DROP TABLE IF EXISTS `isp`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `isp` (
  `isp_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`isp_id`)
) ENGINE=MyISAM AUTO_INCREMENT=29 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ispwebservices`
--

DROP TABLE IF EXISTS `ispwebservices`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ispwebservices` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `isp_id` int(11) DEFAULT NULL,
  `ws_name` varchar(20) DEFAULT NULL,
  `ws_url` varchar(200) DEFAULT NULL,
  `operation_name` varchar(20) DEFAULT NULL,
  `comment` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `local_exchange`
--

DROP TABLE IF EXISTS `local_exchange`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `local_exchange` (
  `id` int(11) NOT NULL,
  `municipality` int(11) DEFAULT NULL,
  `postal_code` varchar(10) CHARACTER SET utf8 DEFAULT NULL,
  `country` varchar(20) CHARACTER SET utf8 DEFAULT NULL,
  `longitude` decimal(10,7) DEFAULT NULL,
  `latitude` decimal(10,7) DEFAULT NULL,
  `creation_time` datetime DEFAULT NULL,
  `destruction_time` datetime DEFAULT NULL,
  `le_uname` varchar(60) CHARACTER SET utf8 DEFAULT NULL,
  `le_description` varchar(60) CHARACTER SET utf8 DEFAULT NULL,
  `status` tinyint(4) DEFAULT NULL,
  `polygon_id` int(11) DEFAULT NULL,
  `SHAPE` geometry NOT NULL,
  `polygon_type` set('real','voronoi','1000m') COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `local_exchange_centers`
--

DROP TABLE IF EXISTS `local_exchange_centers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `local_exchange_centers` (
  `id` int(11) NOT NULL DEFAULT '0',
  `longitude` decimal(10,7) DEFAULT NULL,
  `latitude` decimal(10,7) DEFAULT NULL,
  `SHAPE` geometry DEFAULT NULL,
  `description` varchar(200) CHARACTER SET utf8 DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `local_exchange_old`
--

DROP TABLE IF EXISTS `local_exchange_old`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `local_exchange_old` (
  `id` int(11) NOT NULL DEFAULT '0',
  `municipality` int(11) DEFAULT NULL,
  `postal_code` varchar(10) DEFAULT NULL,
  `country` varchar(20) DEFAULT NULL,
  `longitude` decimal(10,7) NOT NULL,
  `latitude` decimal(10,7) NOT NULL,
  `creation_time` datetime DEFAULT NULL,
  `destruction_time` datetime DEFAULT NULL,
  `le_uname` varchar(60) DEFAULT NULL,
  `le_description` varchar(60) DEFAULT NULL,
  `status` tinyint(4) DEFAULT NULL,
  `polygon_id` int(11) DEFAULT NULL,
  `SHAPE` geometry DEFAULT NULL,
  `enc_pol_points` text,
  `voronoi_id` int(11) DEFAULT NULL,
  `enc_pol_levels` text,
  `voronoiSHAPE` geometry DEFAULT NULL,
  `vor_enc_pol_points` text,
  `vor_enc_pol_levels` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `local_exchange_polygons`
--

DROP TABLE IF EXISTS `local_exchange_polygons`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `local_exchange_polygons` (
  `id` int(11) NOT NULL DEFAULT '0',
  `SHAPE` geometry NOT NULL,
  `longitude` decimal(10,7) DEFAULT NULL,
  `latitude` decimal(10,7) DEFAULT NULL,
  `enc_pol_points` text CHARACTER SET utf8,
  `enc_pol_levels` text CHARACTER SET utf8,
  `description` varchar(200) CHARACTER SET utf8 DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `local_exchange_polygons_1000m`
--

DROP TABLE IF EXISTS `local_exchange_polygons_1000m`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `local_exchange_polygons_1000m` (
  `id` int(11) NOT NULL DEFAULT '0',
  `SHAPE` geometry NOT NULL,
  `longitude` decimal(10,7) DEFAULT NULL,
  `latitude` decimal(10,7) DEFAULT NULL,
  `enc_pol_points` text CHARACTER SET utf8,
  `enc_pol_levels` text CHARACTER SET utf8,
  `description` varchar(200) CHARACTER SET utf8 DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `local_exchange_voronoi`
--

DROP TABLE IF EXISTS `local_exchange_voronoi`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `local_exchange_voronoi` (
  `id` int(11) NOT NULL DEFAULT '0',
  `SHAPE` geometry NOT NULL,
  `longitude` decimal(10,7) DEFAULT NULL,
  `latitude` decimal(10,7) DEFAULT NULL,
  `enc_pol_points` text CHARACTER SET utf8,
  `enc_pol_levels` text CHARACTER SET utf8,
  `description` varchar(200) CHARACTER SET utf8 DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ndt_logs`
--

DROP TABLE IF EXISTS `ndt_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ndt_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ip` varchar(40) COLLATE utf8_unicode_ci DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `connection_id` int(11) DEFAULT NULL,
  `logtime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `severity` enum('INFO','DEBUG','WARNING','ERROR') COLLATE utf8_unicode_ci DEFAULT 'INFO',
  `message` varchar(400) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=35 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ndt_measurement`
--

DROP TABLE IF EXISTS `ndt_measurement`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ndt_measurement` (
  `measurement_id` int(11) NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `reporting_host` varchar(50) DEFAULT NULL,
  `user_id` varchar(255) NOT NULL,
  `connection_id` varchar(255) NOT NULL,
  `measurement_tool` varchar(255) DEFAULT NULL,
  `version` varchar(255) DEFAULT NULL,
  `report_host` varchar(255) DEFAULT NULL,
  `report_port` int(10) unsigned DEFAULT NULL,
  `os_name` varchar(255) DEFAULT NULL,
  `os_version` varchar(255) DEFAULT NULL,
  `os_architecture` varchar(255) DEFAULT NULL,
  `java_version` varchar(255) DEFAULT NULL,
  `java_vendor` varchar(255) DEFAULT NULL,
  `is_application` tinyint(1) DEFAULT NULL,
  `upstream_bw` double DEFAULT NULL,
  `downstream_bw` double DEFAULT NULL,
  `rtt` double DEFAULT NULL,
  `mss` int(10) unsigned DEFAULT NULL,
  `out_of_order` double DEFAULT NULL,
  `sack_blocks` int(10) unsigned DEFAULT NULL,
  `loss` double DEFAULT NULL,
  `sack_enabled` tinyint(1) DEFAULT NULL,
  `nagle_enabled` tinyint(1) DEFAULT NULL,
  `ECN_enabled` tinyint(1) DEFAULT NULL,
  `time_stamping_enabled` tinyint(1) DEFAULT NULL,
  `timeouts` int(10) unsigned DEFAULT NULL,
  `retransmissions` int(10) unsigned DEFAULT NULL,
  `duplicate_acks` int(10) unsigned DEFAULT NULL,
  `ccip` varchar(255) DEFAULT NULL,
  `scip` varchar(255) DEFAULT NULL,
  `ssip` varchar(255) DEFAULT NULL,
  `wait_seconds` int(10) unsigned DEFAULT NULL,
  `jitter` int(11) DEFAULT NULL,
  PRIMARY KEY (`measurement_id`),
  KEY `measurement_id` (`measurement_id`),
  KEY `connection_id` (`measurement_id`),
  KEY `user_id` (`measurement_id`),
  KEY `triplet` (`measurement_id`,`user_id`,`connection_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Temporary table structure for view `ndt_measurements_view`
--

DROP TABLE IF EXISTS `ndt_measurements_view`;
/*!50001 DROP VIEW IF EXISTS `ndt_measurements_view`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `ndt_measurements_view` (
  `measurement_id` int(11),
  `measurement_date` varchar(10),
  `measurement_day` varchar(64),
  `measurement_time` varchar(13),
  `measured_downstream_mbps` double,
  `measured_upstream_mbps` double,
  `measured_rtt_msec` double,
  `measured_loss` double,
  `measured_jitter_msec` int(11),
  `client_operating_system` varchar(255),
  `client_operating_system_version` varchar(255),
  `client_operating_system_architecture` varchar(255),
  `client_java_version` varchar(255),
  `client_java_vendor` varchar(255),
  `connection_id` int(11),
  `ISP` varchar(50),
  `contract_download_mbps` decimal(14,4),
  `contract_upload_mbps` decimal(14,4),
  `connection_address` varchar(50),
  `connection_longitude` decimal(10,7),
  `connection_latitude` decimal(10,7),
  `connection_postal_code` varchar(10),
  `connection_region_id` int(11),
  `connection_region` varchar(50),
  `connection_exchange` int(11),
  `connection_exchange_longitude` decimal(10,7),
  `connection_exchange_latitude` decimal(10,7),
  `estimated_distance_to_exchange_m` int(11),
  `exchange_polygon_defined` varchar(3),
  `connection_outer_region1_id` int(11),
  `connection_region_l1` varchar(50),
  `connection_outer_region2_id` int(11),
  `connection_region_l2` varchar(50)
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `polygons_bak`
--

DROP TABLE IF EXISTS `polygons_bak`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `polygons_bak` (
  `OGR_FID` int(11) NOT NULL AUTO_INCREMENT,
  `SHAPE` geometry NOT NULL,
  `id` double DEFAULT NULL,
  `objectid` double DEFAULT NULL,
  `shape_leng` double(18,6) DEFAULT NULL,
  `type_land` double DEFAULT NULL,
  UNIQUE KEY `OGR_FID` (`OGR_FID`),
  SPATIAL KEY `SHAPE` (`SHAPE`)
) ENGINE=MyISAM AUTO_INCREMENT=666667 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `postal_codes`
--

DROP TABLE IF EXISTS `postal_codes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `postal_codes` (
  `code` int(11) NOT NULL,
  `longitude` decimal(10,7) DEFAULT NULL,
  `latitude` decimal(10,7) DEFAULT NULL,
  `containing_region` int(11) DEFAULT NULL,
  `occurences` int(11) DEFAULT NULL,
  `name_lang0` varchar(50) DEFAULT NULL,
  `name_lang1` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`code`),
  KEY `lat_index` (`latitude`),
  KEY `lng_index` (`longitude`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `region_level_0`
--

DROP TABLE IF EXISTS `region_level_0`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `region_level_0` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name_lang0` varchar(50) DEFAULT NULL,
  `name_lang1` varchar(50) DEFAULT NULL,
  `longitude` decimal(10,7) DEFAULT NULL,
  `latitude` decimal(10,7) DEFAULT NULL,
  `country` int(11) DEFAULT NULL,
  `polygon` text,
  PRIMARY KEY (`id`),
  KEY `lat_index` (`latitude`),
  KEY `lng_index` (`longitude`)
) ENGINE=MyISAM AUTO_INCREMENT=14 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `region_level_0_multipolygons`
--

DROP TABLE IF EXISTS `region_level_0_multipolygons`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `region_level_0_multipolygons` (
  `periphery_id` int(11) DEFAULT NULL,
  `SHAPE` geometry NOT NULL,
  `id` double DEFAULT NULL,
  `objectid` double DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `region_level_1`
--

DROP TABLE IF EXISTS `region_level_1`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `region_level_1` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name_lang0` varchar(50) DEFAULT NULL,
  `name_lang1` varchar(50) DEFAULT NULL,
  `longitude` decimal(10,7) DEFAULT NULL,
  `latitude` decimal(10,7) DEFAULT NULL,
  `containing_region` int(11) DEFAULT NULL,
  `area` polygon DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `lat_index` (`latitude`),
  KEY `lng_index` (`longitude`)
) ENGINE=MyISAM AUTO_INCREMENT=52 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `region_level_1_multipolygons`
--

DROP TABLE IF EXISTS `region_level_1_multipolygons`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `region_level_1_multipolygons` (
  `OGR_FID` int(11) NOT NULL DEFAULT '0',
  `SHAPE` geometry NOT NULL,
  `id` double DEFAULT NULL,
  `objectid` double DEFAULT NULL,
  `shape_leng` double(18,6) DEFAULT NULL,
  `type_land` double DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `region_level_1_polygon`
--

DROP TABLE IF EXISTS `region_level_1_polygon`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `region_level_1_polygon` (
  `prid` int(11) NOT NULL,
  `polid` int(11) NOT NULL,
  `grpolid` int(11) DEFAULT NULL,
  `name_el` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`prid`,`polid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `region_level_2`
--

DROP TABLE IF EXISTS `region_level_2`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `region_level_2` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name_lang0` varchar(50) DEFAULT NULL,
  `name_lang1` varchar(50) DEFAULT NULL,
  `name_lang0_gen_caps` varchar(50) DEFAULT NULL,
  `name_lang0_no_accents` varchar(50) DEFAULT NULL,
  `longitude` decimal(10,7) DEFAULT NULL,
  `latitude` decimal(10,7) DEFAULT NULL,
  `containing_region` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `lat_index` (`latitude`),
  KEY `lng_index` (`longitude`)
) ENGINE=MyISAM AUTO_INCREMENT=5412 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `rememberme_session`
--

DROP TABLE IF EXISTS `rememberme_session`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rememberme_session` (
  `user_id` int(11) NOT NULL DEFAULT '0',
  `email` varchar(50) CHARACTER SET utf8 DEFAULT NULL,
  `last_login` datetime DEFAULT NULL,
  `last_session` varchar(64) CHARACTER SET utf8 NOT NULL DEFAULT '',
  PRIMARY KEY (`user_id`,`last_session`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `spatial_ref_sys`
--

DROP TABLE IF EXISTS `spatial_ref_sys`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `spatial_ref_sys` (
  `SRID` int(11) NOT NULL,
  `AUTH_NAME` varchar(256) DEFAULT NULL,
  `AUTH_SRID` int(11) DEFAULT NULL,
  `SRTEXT` varchar(2048) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tk`
--

DROP TABLE IF EXISTS `tk`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tk` (
  `prefix` int(11) NOT NULL,
  `number` int(11) NOT NULL,
  `postal_code` int(11) NOT NULL DEFAULT '0',
  `city` varchar(50) DEFAULT NULL,
  `prefecture` varchar(50) DEFAULT NULL,
  `prefecture_id` int(11) DEFAULT NULL,
  `municipality_id` int(11) NOT NULL DEFAULT '0',
  `name_el_aux` varchar(50) DEFAULT NULL,
  `name_lang1` varchar(50) DEFAULT NULL,
  `name_lang0` varchar(50) DEFAULT NULL,
  `longitude` decimal(10,7) DEFAULT NULL,
  `latitude` decimal(10,7) DEFAULT NULL,
  PRIMARY KEY (`postal_code`,`municipality_id`),
  KEY `prf` (`prefix`),
  KEY `num` (`number`),
  KEY `tkprefecture` (`prefecture_id`),
  KEY `tkmunicipality` (`municipality_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tk_new`
--

DROP TABLE IF EXISTS `tk_new`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tk_new` (
  `postal_code` int(11) NOT NULL DEFAULT '0',
  `longitude` decimal(10,7) DEFAULT NULL,
  `latitude` decimal(10,7) DEFAULT NULL,
  PRIMARY KEY (`postal_code`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `firstname` varchar(50) DEFAULT '',
  `lastname` varchar(50) DEFAULT '',
  `email` varchar(50) DEFAULT NULL,
  `password` varchar(50) DEFAULT NULL,
  `profile` tinyint(4) DEFAULT NULL,
  `creation_time` datetime DEFAULT NULL,
  `last_login` datetime DEFAULT NULL,
  `active` tinyint(4) DEFAULT '0',
  `contact` bit(1) NOT NULL DEFAULT b'1',
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=MyISAM AUTO_INCREMENT=5207 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_connection`
--

DROP TABLE IF EXISTS `user_connection`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_connection` (
  `user_id` int(11) NOT NULL,
  `connection_id` int(11) NOT NULL,
  PRIMARY KEY (`user_id`,`connection_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `web100_measurement`
--

DROP TABLE IF EXISTS `web100_measurement`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `web100_measurement` (
  `measurement_id` int(11) NOT NULL AUTO_INCREMENT,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `reporting_host` varchar(50) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `connection_id` int(11) DEFAULT NULL,
  `measurement_tool` varchar(255) DEFAULT NULL,
  `version` varchar(255) DEFAULT NULL,
  `report_host` varchar(255) DEFAULT NULL,
  `report_port` int(10) unsigned DEFAULT NULL,
  `os_name` varchar(255) DEFAULT NULL,
  `os_version` varchar(255) DEFAULT NULL,
  `os_architecture` varchar(255) DEFAULT NULL,
  `java_version` varchar(255) DEFAULT NULL,
  `java_vendor` varchar(255) DEFAULT NULL,
  `is_application` tinyint(1) DEFAULT NULL,
  `upstream_bw` double DEFAULT NULL,
  `downstream_bw` double DEFAULT NULL,
  `rtt` double DEFAULT NULL,
  `mss` int(10) unsigned DEFAULT NULL,
  `out_of_order` double DEFAULT NULL,
  `sack_blocks` int(10) unsigned DEFAULT NULL,
  `loss` double DEFAULT NULL,
  `sack_enabled` tinyint(1) DEFAULT NULL,
  `nagle_enabled` tinyint(1) DEFAULT NULL,
  `ECN_enabled` tinyint(1) DEFAULT NULL,
  `time_stamping_enabled` tinyint(1) DEFAULT NULL,
  `timeouts` int(10) unsigned DEFAULT NULL,
  `retransmissions` int(10) unsigned DEFAULT NULL,
  `duplicate_acks` int(10) unsigned DEFAULT NULL,
  `ccip` varchar(255) DEFAULT NULL,
  `scip` varchar(255) DEFAULT NULL,
  `ssip` varchar(255) DEFAULT NULL,
  `wait_seconds` int(10) unsigned DEFAULT NULL,
  `jitter` int(11) DEFAULT NULL,
  `web100_SndWinScale` int(11) DEFAULT NULL,
  `web100_DSACKDups` int(11) DEFAULT NULL,
  `web100_CurRTO` int(11) DEFAULT NULL,
  `web100_MaxCwnd` int(11) DEFAULT NULL,
  `web100_spd` double DEFAULT NULL,
  `web100_MaxRTO` int(11) DEFAULT NULL,
  `web100_MaxRwinRcvd` int(11) DEFAULT NULL,
  `web100_avgrtt` double DEFAULT NULL,
  `web100_maxCWNDpeak` int(11) DEFAULT NULL,
  `web100_cwndtime` double DEFAULT NULL,
  `web100_rwintime` double DEFAULT NULL,
  `web100_CurMSS` int(11) DEFAULT NULL,
  `web100_FastRetran` int(11) DEFAULT NULL,
  `web100_congestion` int(11) DEFAULT NULL,
  `web100_PktsIn` int(11) DEFAULT NULL,
  `web100_DupAcksIn` int(11) DEFAULT NULL,
  `web100_SndLimBytesRwin` int(11) DEFAULT NULL,
  `web100_MinRwinRcvd` int(11) DEFAULT NULL,
  `web100_rttsec` double DEFAULT NULL,
  `web100_SndLimBytesSender` int(11) DEFAULT NULL,
  `web100_CurCwnd` int(11) DEFAULT NULL,
  `web100_DataPktsOut` int(11) DEFAULT NULL,
  `web100_SndLimBytesCwnd` int(11) DEFAULT NULL,
  `web100_X_Rcvbuf` int(11) DEFAULT NULL,
  `web100_SndLimTransCwnd` int(11) DEFAULT NULL,
  `web100_order` double DEFAULT NULL,
  `web100_s2cData` int(11) DEFAULT NULL,
  `web100_NagleEnabled` int(11) DEFAULT NULL,
  `web100_timesec` double DEFAULT NULL,
  `web100_link` int(11) DEFAULT NULL,
  `web100_DupAcksOut` int(11) DEFAULT NULL,
  `web100_waitsec` double DEFAULT NULL,
  `web100_AckPktsOut` int(11) DEFAULT NULL,
  `web100_loss` double DEFAULT NULL,
  `web100_sendtime` double DEFAULT NULL,
  `web100_CongestionSignals` int(11) DEFAULT NULL,
  `web100_MinRTO` int(11) DEFAULT NULL,
  `web100_MinRwinSent` int(11) DEFAULT NULL,
  `web100_rwin` double DEFAULT NULL,
  `web100_swin` double DEFAULT NULL,
  `web100_StartTimeUsec` int(11) DEFAULT NULL,
  `web100_Duration` int(11) DEFAULT NULL,
  `web100_TimestampsEnabled` int(11) DEFAULT NULL,
  `web100_RcvWinScale` int(11) DEFAULT NULL,
  `web100_SndLimTimeRwin` int(11) DEFAULT NULL,
  `web100_mismatch` int(11) DEFAULT NULL,
  `web100_MaxRTT` int(11) DEFAULT NULL,
  `web100_DataBytesOut` int(11) DEFAULT NULL,
  `web100_SndLimTransSender` int(11) DEFAULT NULL,
  `web100_c2sAck` int(11) DEFAULT NULL,
  `web100_SampleRTT` int(11) DEFAULT NULL,
  `web100_SndLimTransRwin` int(11) DEFAULT NULL,
  `web100_ECNEnabled` int(11) DEFAULT NULL,
  `web100_PktsOut` int(11) DEFAULT NULL,
  `web100_CountRTT` int(11) DEFAULT NULL,
  `web100_MinMSS` int(11) DEFAULT NULL,
  `web100_SmoothedRTT` int(11) DEFAULT NULL,
  `web100_DataPktsIn` int(11) DEFAULT NULL,
  `web100_CWND-Limited` double DEFAULT NULL,
  `web100_CurSsthresh` double DEFAULT NULL,
  `web100_CurRwinRcvd` int(11) DEFAULT NULL,
  `web100_CurRwinSent` int(11) DEFAULT NULL,
  `web100_SubsequentTimeouts` int(11) DEFAULT NULL,
  `web100_Timeouts` int(11) DEFAULT NULL,
  `web100_MaxRwinSent` int(11) DEFAULT NULL,
  `web100_SACKsRcvd` int(11) DEFAULT NULL,
  `web100_SACKEnabled` int(11) DEFAULT NULL,
  `web100_SendStall` int(11) DEFAULT NULL,
  `web100_cwin` double DEFAULT NULL,
  `web100_WinScaleRcvd` int(11) DEFAULT NULL,
  `web100_bad_cable` int(11) DEFAULT NULL,
  `web100_MaxSsthresh` int(11) DEFAULT NULL,
  `web100_OtherReductions` int(11) DEFAULT NULL,
  `web100_aspd` double DEFAULT NULL,
  `web100_bw` double DEFAULT NULL,
  `web100_CWNDpeaks` int(11) DEFAULT NULL,
  `web100_s2cAck` int(11) DEFAULT NULL,
  `web100_CongestionOverCount` int(11) DEFAULT NULL,
  `web100_Sndbuf` int(11) DEFAULT NULL,
  `web100_AckPktsIn` int(11) DEFAULT NULL,
  `web100_WinScaleSent` int(11) DEFAULT NULL,
  `web100_minCWNDpeak` int(11) DEFAULT NULL,
  `web100_X_Sndbuf` int(11) DEFAULT NULL,
  `web100_CongAvoid` int(11) DEFAULT NULL,
  `web100_PktsRetrans` int(11) DEFAULT NULL,
  `web100_c2sData` int(11) DEFAULT NULL,
  `web100_MinRTT` int(11) DEFAULT NULL,
  `web100_half_duplex` int(11) DEFAULT NULL,
  `web100_SlowStart` int(11) DEFAULT NULL,
  `web100_SndLimTimeSender` int(11) DEFAULT NULL,
  `web100_MaxMSS` int(11) DEFAULT NULL,
  `web100_DataBytesIn` int(11) DEFAULT NULL,
  `web100_BytesRetrans` int(11) DEFAULT NULL,
  `web100_SumRTT` int(11) DEFAULT NULL,
  `web100_SndLimTimeCwnd` int(11) DEFAULT NULL,
  `packet_size_preserved` bit(1) DEFAULT NULL,
  `window_scaling` bit(1) DEFAULT NULL,
  PRIMARY KEY (`measurement_id`),
  KEY `measurement_id` (`measurement_id`),
  KEY `connection_id` (`measurement_id`),
  KEY `user_id` (`measurement_id`),
  KEY `triplet` (`measurement_id`,`user_id`,`connection_id`)
) ENGINE=MyISAM AUTO_INCREMENT=38622 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER update_generic_measurement AFTER INSERT ON web100_measurement FOR EACH ROW INSERT INTO generic_measurement VALUES (NEW.measurement_id, NEW.created, NEW.reporting_host, NEW.user_id, NEW.connection_id, NEW.measurement_tool, NEW.version, NEW.report_host, NEW.report_port, NEW.upstream_bw, NEW.downstream_bw, NEW.rtt, NEW.loss, NEW.jitter) */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Final view structure for view `glasnost_measurements_view`
--

/*!50001 DROP TABLE IF EXISTS `glasnost_measurements_view`*/;
/*!50001 DROP VIEW IF EXISTS `glasnost_measurements_view`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `glasnost_measurements_view` AS select `gm`.`id` AS `measurement_id`,date_format(`gm`.`created`,'%Y-%m-%d') AS `measurement_date`,date_format(`gm`.`created`,'%W') AS `measurement_day`,date_format(`gm`.`created`,'%H:%i:%s') AS `measurement_time`,`gm`.`server` AS `server`,`gm`.`protocol1` AS `service_protocol_name`,if((`gm`.`download_indication` = 1),'yes','no') AS `service_protocol_download_shaped`,`gm`.`max_pr_download` AS `service_protocol_max_download_bandwidth_kbps`,`gm`.`max_cf_download` AS `control_flow_max_download_bandwidth_kbps`,if((`gm`.`upload_indication` = 1),'yes','no') AS `service_protocol_upload_shaped`,`gm`.`max_pr_upload` AS `service_protocol_max_upload_bandwidth_kbps`,`gm`.`max_cf_upload` AS `control_flow_max_upload_bandwidth_kbps`,`c`.`connection_id` AS `connection_id`,`i`.`name` AS `ISP`,(`c`.`purchased_bandwidth_dl_kbps` / 1000) AS `contract_download_mbps`,(`c`.`purchased_bandwidth_ul_kbps` / 1000) AS `contract_upload_mbps`,`c`.`address` AS `connection_address`,`c`.`longitude` AS `connection_longitude`,`c`.`latitude` AS `connection_latitude`,`c`.`postal_code` AS `connection_postal_code`,`c`.`containing_region` AS `connection_region_id`,`m`.`name_lang0` AS `connection_region`,`c`.`exchange_id` AS `connection_exchange`,`e`.`longitude` AS `connection_exchange_longitude`,`e`.`latitude` AS `connection_exchange_latitude`,`c`.`distance_to_exchange` AS `estimated_distance_to_exchange_m`,if((`e`.`polygon_type` = 'real'),'yes','no') AS `exchange_polygon_defined`,`m`.`containing_region` AS `connection_outer_region1_id`,concat(`p`.`name_lang0`) AS `connection_region_l1`,`p`.`containing_region` AS `connection_outer_region2_id`,`pe`.`name_lang0` AS `connection_region_l2` from ((((((`glasnost_measurement` `gm` join `connection` `c` on((`gm`.`connection_id` = `c`.`connection_id`))) join `isp` `i` on((`c`.`isp_id` = `i`.`isp_id`))) join `region_level_2` `m` on((`c`.`containing_region` = `m`.`id`))) join `region_level_1` `p` on((`m`.`containing_region` = `p`.`id`))) join `region_level_0` `pe` on((`p`.`containing_region` = `pe`.`id`))) join `local_exchange` `e` on((`c`.`exchange_id` = `e`.`id`))) where (`c`.`status` = 1) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `ndt_measurements_view`
--

/*!50001 DROP TABLE IF EXISTS `ndt_measurements_view`*/;
/*!50001 DROP VIEW IF EXISTS `ndt_measurements_view`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `ndt_measurements_view` AS select `nm`.`measurement_id` AS `measurement_id`,date_format(`nm`.`created`,'%Y-%m-%d') AS `measurement_date`,date_format(`nm`.`created`,'%W') AS `measurement_day`,date_format(`nm`.`created`,'%H:%i:%s') AS `measurement_time`,`nm`.`downstream_bw` AS `measured_downstream_mbps`,`nm`.`upstream_bw` AS `measured_upstream_mbps`,`nm`.`rtt` AS `measured_rtt_msec`,`nm`.`loss` AS `measured_loss`,`nm`.`jitter` AS `measured_jitter_msec`,`nm`.`os_name` AS `client_operating_system`,`nm`.`os_version` AS `client_operating_system_version`,`nm`.`os_architecture` AS `client_operating_system_architecture`,`nm`.`java_version` AS `client_java_version`,`nm`.`java_vendor` AS `client_java_vendor`,`c`.`connection_id` AS `connection_id`,`i`.`name` AS `ISP`,(`c`.`purchased_bandwidth_dl_kbps` / 1000) AS `contract_download_mbps`,(`c`.`purchased_bandwidth_ul_kbps` / 1000) AS `contract_upload_mbps`,`c`.`address` AS `connection_address`,`c`.`longitude` AS `connection_longitude`,`c`.`latitude` AS `connection_latitude`,`c`.`postal_code` AS `connection_postal_code`,`c`.`containing_region` AS `connection_region_id`,`m`.`name_lang0` AS `connection_region`,`c`.`exchange_id` AS `connection_exchange`,`e`.`longitude` AS `connection_exchange_longitude`,`e`.`latitude` AS `connection_exchange_latitude`,`c`.`distance_to_exchange` AS `estimated_distance_to_exchange_m`,if((`e`.`polygon_type` = 'real'),'yes','no') AS `exchange_polygon_defined`,`m`.`containing_region` AS `connection_outer_region1_id`,concat(`p`.`name_lang0`) AS `connection_region_l1`,`p`.`containing_region` AS `connection_outer_region2_id`,`pe`.`name_lang0` AS `connection_region_l2` from ((((((`web100_measurement` `nm` join `connection` `c` on((`nm`.`connection_id` = `c`.`connection_id`))) join `isp` `i` on((`c`.`isp_id` = `i`.`isp_id`))) join `region_level_2` `m` on((`c`.`containing_region` = `m`.`id`))) join `region_level_1` `p` on((`m`.`containing_region` = `p`.`id`))) join `region_level_0` `pe` on((`p`.`containing_region` = `pe`.`id`))) join `local_exchange` `e` on((`c`.`exchange_id` = `e`.`id`))) where (`c`.`status` = 1) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2014-01-21 20:04:48
