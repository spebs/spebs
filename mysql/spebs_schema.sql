-- MySQL dump 10.13  Distrib 5.5.13, for FreeBSD8.2 (amd64)
--
-- Host: localhost    Database: spebs
-- ------------------------------------------------------
-- Server version	5.5.13

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
  `ip` varchar(40) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `actiontime` datetime DEFAULT NULL,
  `action` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=389 DEFAULT CHARSET=latin1;
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
  `avgup` double DEFAULT NULL,
  `avgdown` double DEFAULT NULL,
  `avgrtt` double DEFAULT NULL,
  `avgloss` double DEFAULT NULL,
  `avgjitter` decimal(14,4) DEFAULT NULL,
  `postal_code` int(11) DEFAULT NULL,
  `municipality` int(11) DEFAULT NULL,
  `prefecture` int(11) DEFAULT NULL,
  `periphery` int(11) DEFAULT NULL,
  `country` int(11) DEFAULT NULL,
  `measurements_count` bigint(21) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
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
  `avgup` double DEFAULT NULL,
  `avgdown` double DEFAULT NULL,
  `avgrtt` double DEFAULT NULL,
  `avgloss` double DEFAULT NULL,
  `avgjitter` decimal(18,8) DEFAULT NULL,
  `connections_count` bigint(21) NOT NULL DEFAULT '0',
  `measurements_sum` decimal(42,0) DEFAULT NULL,
  KEY `mindex` (`municipality`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
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
  `avgup` double DEFAULT NULL,
  `avgdown` double DEFAULT NULL,
  `avgrtt` double DEFAULT NULL,
  `avgloss` double DEFAULT NULL,
  `avgjitter` decimal(18,8) DEFAULT NULL,
  `connections_count` bigint(21) NOT NULL DEFAULT '0',
  `measurements_sum` decimal(42,0) DEFAULT NULL,
  KEY `pindex` (`periphery`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
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
  `avgup` double DEFAULT NULL,
  `avgdown` double DEFAULT NULL,
  `avgrtt` double DEFAULT NULL,
  `avgloss` double DEFAULT NULL,
  `avgjitter` decimal(18,8) DEFAULT NULL,
  `connections_count` bigint(21) NOT NULL DEFAULT '0',
  `measurements_sum` decimal(42,0) DEFAULT NULL,
  KEY `pcindex` (`postal_code`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
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
  `avgup` double DEFAULT NULL,
  `avgdown` double DEFAULT NULL,
  `avgrtt` double DEFAULT NULL,
  `avgloss` double DEFAULT NULL,
  `avgjitter` decimal(18,8) DEFAULT NULL,
  `connections_count` bigint(21) NOT NULL DEFAULT '0',
  `measurements_sum` decimal(42,0) DEFAULT NULL,
  KEY `pindex` (`prefecture`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
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
  `address` varchar(50) DEFAULT NULL,
  `street` varchar(50) DEFAULT NULL,
  `str_number` varchar(5) DEFAULT NULL,
  `municipality` int(11) DEFAULT NULL,
  `postal_code` varchar(10) DEFAULT NULL,
  `city` varchar(20) DEFAULT NULL,
  `country` varchar(20) DEFAULT NULL,
  `longitude` decimal(10,7) NOT NULL,
  `latitude` decimal(10,7) NOT NULL,
  `local_loop_name` varchar(50) DEFAULT NULL,
  `distance_to_exchange` int(11) DEFAULT NULL,
  `contention_ratio` decimal(10,0) DEFAULT NULL,
  `status` bit(1) DEFAULT NULL,
  `creation_time` datetime DEFAULT NULL,
  `destruction_time` datetime DEFAULT NULL,
  `purchased_bandwidth_dl_kbps` int(11) DEFAULT NULL,
  `purchased_bandwidth_ul_kbps` int(11) DEFAULT NULL,
  PRIMARY KEY (`connection_id`),
  KEY `lng_index` (`longitude`),
  KEY `lat_index` (`latitude`),
  KEY `municipality_on_connection` (`municipality`),
  KEY `postal_code_on_connection` (`postal_code`),
  KEY `latlng_index` (`longitude`,`latitude`)
) ENGINE=MyISAM AUTO_INCREMENT=17 DEFAULT CHARSET=utf8;
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
  `enc_pol_points` text,
  `enc_pol_levels` text,
  `comment` varchar(30) DEFAULT NULL,
  PRIMARY KEY (`polid`,`aa`),
  KEY `lng_on_det_pols` (`longitude`),
  KEY `lat_on_det_pols` (`latitude`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
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
  `measurement_id` int(11) NOT NULL AUTO_INCREMENT,
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
) ENGINE=MyISAM AUTO_INCREMENT=162 DEFAULT CHARSET=utf8;
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
  `jitter` int(11) DEFAULT NULL,
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
-- Table structure for table `isp`
--

DROP TABLE IF EXISTS `isp`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `isp` (
  `isp_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`isp_id`)
) ENGINE=MyISAM AUTO_INCREMENT=25 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `isp_temp`
--

DROP TABLE IF EXISTS `isp_temp`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `isp_temp` (
  `isp_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`isp_id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;
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
  `exchange_id` int(11) NOT NULL AUTO_INCREMENT,
  `address` varchar(50) DEFAULT NULL,
  `postal_code` varchar(10) DEFAULT NULL,
  `city` varchar(20) DEFAULT NULL,
  `country` varchar(20) DEFAULT NULL,
  `longitude` decimal(10,7) NOT NULL,
  `latitude` decimal(10,7) NOT NULL,
  `creation_time` datetime DEFAULT NULL,
  `destruction_time` datetime DEFAULT NULL,
  `le_uname` varchar(60) DEFAULT NULL,
  `le_description` varchar(60) DEFAULT NULL,
  `status` tinyint(4) DEFAULT NULL,
  PRIMARY KEY (`exchange_id`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `municipalities`
--

DROP TABLE IF EXISTS `municipalities`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `municipalities` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name_el` varchar(50) DEFAULT NULL,
  `name_en` varchar(50) DEFAULT NULL,
  `name_el_gen_caps` varchar(50) DEFAULT NULL,
  `name_el_no_accents` varchar(50) DEFAULT NULL,
  `longitude` decimal(10,7) DEFAULT NULL,
  `latitude` decimal(10,7) DEFAULT NULL,
  `prefecture` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `lat_index` (`latitude`),
  KEY `lng_index` (`longitude`)
) ENGINE=MyISAM AUTO_INCREMENT=5412 DEFAULT CHARSET=utf8;
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
-- Table structure for table `peripheries`
--

DROP TABLE IF EXISTS `peripheries`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `peripheries` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name_el` varchar(50) DEFAULT NULL,
  `name_en` varchar(50) DEFAULT NULL,
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
-- Table structure for table `peripheries_multipolygons`
--

DROP TABLE IF EXISTS `peripheries_multipolygons`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `peripheries_multipolygons` (
  `periphery_id` int(11) DEFAULT NULL,
  `SHAPE` geometry NOT NULL,
  `id` double DEFAULT NULL,
  `objectid` double DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

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
  `municipality` int(11) DEFAULT NULL,
  `occurences` int(11) DEFAULT NULL,
  `name_el` varchar(50) DEFAULT NULL,
  `name_en` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`code`),
  KEY `lat_index` (`latitude`),
  KEY `lng_index` (`longitude`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `prefecture_polygon`
--

DROP TABLE IF EXISTS `prefecture_polygon`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `prefecture_polygon` (
  `prid` int(11) NOT NULL,
  `polid` int(11) NOT NULL,
  `grpolid` int(11) DEFAULT NULL,
  `name_el` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`prid`,`polid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `prefectures`
--

DROP TABLE IF EXISTS `prefectures`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `prefectures` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name_el` varchar(50) DEFAULT NULL,
  `name_en` varchar(50) DEFAULT NULL,
  `longitude` decimal(10,7) DEFAULT NULL,
  `latitude` decimal(10,7) DEFAULT NULL,
  `periphery` int(11) DEFAULT NULL,
  `area` polygon DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `lat_index` (`latitude`),
  KEY `lng_index` (`longitude`)
) ENGINE=MyISAM AUTO_INCREMENT=52 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `prefectures_multipolygons`
--

DROP TABLE IF EXISTS `prefectures_multipolygons`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `prefectures_multipolygons` (
  `OGR_FID` int(11) NOT NULL DEFAULT '0',
  `SHAPE` geometry NOT NULL,
  `id` double DEFAULT NULL,
  `objectid` double DEFAULT NULL,
  `shape_leng` double(18,6) DEFAULT NULL,
  `type_land` double DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
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
  `name_en` varchar(50) DEFAULT NULL,
  `name_el` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`postal_code`,`municipality_id`),
  KEY `prf` (`prefix`),
  KEY `num` (`number`),
  KEY `tkprefecture` (`prefecture_id`),
  KEY `tkmunicipality` (`municipality_id`)
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
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=MyISAM AUTO_INCREMENT=14 DEFAULT CHARSET=utf8;
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
  `measurement_id` int(11) NOT NULL,
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
  PRIMARY KEY (`measurement_id`),
  KEY `measurement_id` (`measurement_id`),
  KEY `connection_id` (`measurement_id`),
  KEY `user_id` (`measurement_id`),
  KEY `triplet` (`measurement_id`,`user_id`,`connection_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2011-07-24 14:55:23
