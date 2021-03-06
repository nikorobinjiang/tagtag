-- MySQL dump 10.13  Distrib 5.7.17, for Win64 (x86_64)
--
-- Host: 150.138.213.165    Database: ad_gm88
-- ------------------------------------------------------
-- Server version	5.6.39-log

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
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `failed_jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4369 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `password_resets`
--

DROP TABLE IF EXISTS `password_resets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `password_resets` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  KEY `password_resets_email_index` (`email`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ww_ad`
--

DROP TABLE IF EXISTS `ww_ad`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ww_ad` (
  `ad_id` int(11) NOT NULL AUTO_INCREMENT,
  `ad_sn` varchar(32) NOT NULL DEFAULT '' COMMENT '??????????????????',
  `position_id` int(11) NOT NULL DEFAULT '0' COMMENT '?????????ID',
  `ad_name` varchar(32) DEFAULT '' COMMENT '???????????? ?????? sub game',
  `game_id` int(11) NOT NULL DEFAULT '0' COMMENT '??????ID',
  `settlement` varchar(50) NOT NULL DEFAULT '' COMMENT '????????????,??????????????????',
  `ad_url_id` int(11) NOT NULL DEFAULT '0' COMMENT '??????????????????',
  `ad_site_id` int(11) NOT NULL DEFAULT '0' COMMENT '???????????????ID',
  `has_landing_page` tinyint(4) NOT NULL DEFAULT '0' COMMENT '??????????????????',
  `ad_url` varchar(255) DEFAULT '' COMMENT '???????????????URL',
  `style_id` int(11) NOT NULL DEFAULT '0' COMMENT '??????????????? ID???????????????',
  `style_type` tinyint(4) NOT NULL DEFAULT '0' COMMENT '???????????? 1?????? 3??????',
  `admin_id` int(11) DEFAULT '0' COMMENT '?????????ID',
  `create_time` int(11) DEFAULT '0' COMMENT '????????????',
  `is_hide` tinyint(4) NOT NULL DEFAULT '0',
  `media_id` int(11) NOT NULL DEFAULT '0' COMMENT '??????ID',
  `agent_id` int(11) NOT NULL DEFAULT '0' COMMENT '?????????ID',
  `promote_id` int(11) NOT NULL DEFAULT '0' COMMENT '????????????',
  `sub_promote_id` int(11) NOT NULL DEFAULT '0' COMMENT '???????????????',
  `template_id` int(11) NOT NULL DEFAULT '0' COMMENT '???????????????ID',
  `designer_ids` varchar(255) NOT NULL DEFAULT '' COMMENT '????????????????????????id',
  `material_info` varchar(1000) NOT NULL DEFAULT '' COMMENT '?????????',
  `add_time` int(11) NOT NULL DEFAULT '0',
  `edit_time` int(11) NOT NULL DEFAULT '0',
  `down_id` int(11) NOT NULL DEFAULT '0',
  `status` tinyint(4) NOT NULL DEFAULT '1' COMMENT '???????????????1->????????????   0->?????????',
  `is_multi_materials` int(1) NOT NULL DEFAULT '0' COMMENT '??????????????????????????? 0??????1???',
  `distribute` varchar(45) NOT NULL DEFAULT 'Normal' COMMENT '??????????????????Normal:???????????????Toutiao:????????????',
  `distribute_sync` tinyint(4) NOT NULL DEFAULT '0' COMMENT '???????????????0???????????????, 1?????????, 2?????????, 3????????????',
  `distribute_msg` varchar(255) NOT NULL DEFAULT '' COMMENT '??????????????????',
  `ad_group_id` int(11) NOT NULL DEFAULT '0' COMMENT '?????????id',
  `ad_tpl_id` int(11) NOT NULL DEFAULT '0' COMMENT '????????????id',
  `newad_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`ad_id`),
  UNIQUE KEY `sn` (`ad_sn`) USING BTREE,
  KEY `styleid` (`style_id`),
  KEY `idx_search` (`media_id`,`promote_id`,`agent_id`,`settlement`,`game_id`,`position_id`,`status`,`down_id`),
  KEY `down_id` (`down_id`)
) ENGINE=InnoDB AUTO_INCREMENT=342 DEFAULT CHARSET=utf8 COMMENT='?????????';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ww_ad_designers`
--

DROP TABLE IF EXISTS `ww_ad_designers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ww_ad_designers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ad_id` int(11) NOT NULL DEFAULT '0' COMMENT '??????ID',
  `pic` varchar(255) NOT NULL DEFAULT '' COMMENT '???????????????',
  `video` varchar(255) NOT NULL DEFAULT '' COMMENT '???????????????',
  `video_idx` varchar(255) NOT NULL DEFAULT '' COMMENT '?????????????????????',
  `lp` varchar(255) NOT NULL DEFAULT '' COMMENT '??????????????????',
  `all` varchar(255) NOT NULL DEFAULT '' COMMENT '???????????????',
  `pic_str` varchar(255) NOT NULL DEFAULT '',
  `video_str` varchar(255) NOT NULL DEFAULT '',
  `video_idx_str` varchar(255) NOT NULL DEFAULT '',
  `lp_str` varchar(255) NOT NULL DEFAULT '',
  `all_str` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=85 DEFAULT CHARSET=utf8 COMMENT='????????????????????????';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ww_ad_dw`
--

DROP TABLE IF EXISTS `ww_ad_dw`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ww_ad_dw` (
  `dw_id` int(11) NOT NULL AUTO_INCREMENT,
  `dw_date` date DEFAULT NULL COMMENT '??????',
  `ad_id` int(11) NOT NULL DEFAULT '0' COMMENT '??????ID',
  `user_count` int(11) NOT NULL DEFAULT '0' COMMENT '?????????',
  `ip_count` int(11) NOT NULL DEFAULT '0',
  `view_count` int(11) NOT NULL DEFAULT '0' COMMENT '??????',
  `load_count` int(11) NOT NULL DEFAULT '0' COMMENT '????????????',
  `down_count` int(11) NOT NULL DEFAULT '0' COMMENT '??????',
  `finish_count` int(11) NOT NULL DEFAULT '0' COMMENT '????????????',
  PRIMARY KEY (`dw_id`),
  UNIQUE KEY `da` (`dw_date`,`ad_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1083 DEFAULT CHARSET=utf8 COMMENT='???????????????';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ww_ad_dw_material`
--

DROP TABLE IF EXISTS `ww_ad_dw_material`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ww_ad_dw_material` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` date DEFAULT NULL COMMENT '??????',
  `ad_id` int(11) NOT NULL DEFAULT '0' COMMENT '??????ID',
  `material_id` int(11) NOT NULL DEFAULT '0' COMMENT '????????????????????????ID',
  `user_count` int(11) NOT NULL DEFAULT '0' COMMENT '?????????',
  `ip_count` int(11) NOT NULL DEFAULT '0',
  `view_count` int(11) NOT NULL DEFAULT '0' COMMENT '??????',
  `load_count` int(11) NOT NULL DEFAULT '0' COMMENT '????????????',
  `down_count` int(11) NOT NULL DEFAULT '0' COMMENT '??????',
  `finish_count` int(11) NOT NULL DEFAULT '0' COMMENT '????????????',
  `show` int(11) NOT NULL DEFAULT '0' COMMENT '?????????',
  `click` int(11) NOT NULL DEFAULT '0' COMMENT '?????????',
  PRIMARY KEY (`id`),
  UNIQUE KEY `da` (`date`,`ad_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='?????????????????????';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ww_ad_log_1706`
--

DROP TABLE IF EXISTS `ww_ad_log_1706`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ww_ad_log_1706` (
  `aid` int(11) NOT NULL AUTO_INCREMENT,
  `sid` varchar(32) NOT NULL DEFAULT '' COMMENT '??????????????????',
  `ad_id` int(11) NOT NULL DEFAULT '0' COMMENT '??????ID',
  `from_ip` varchar(32) NOT NULL DEFAULT '' COMMENT '??????IP',
  `add_date` varchar(32) NOT NULL DEFAULT '' COMMENT '????????????',
  `add_time` int(11) NOT NULL DEFAULT '0' COMMENT '????????????',
  `status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '?????? 0:??????1:????????????',
  `click_status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '????????????',
  `down_status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '????????????',
  `v_time` int(11) DEFAULT NULL,
  `device` varchar(32) DEFAULT NULL,
  `download` varchar(255) DEFAULT '',
  `link` varchar(255) DEFAULT '',
  `ad_url` varchar(255) NOT NULL DEFAULT '',
  `http_referer` varchar(2000) NOT NULL DEFAULT '',
  PRIMARY KEY (`aid`),
  KEY `aas` (`ad_id`,`add_time`,`status`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='???????????? ??????';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ww_ad_log_1805`
--

DROP TABLE IF EXISTS `ww_ad_log_1805`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ww_ad_log_1805` (
  `aid` bigint(20) NOT NULL AUTO_INCREMENT,
  `sid` varchar(32) NOT NULL,
  `ad_id` int(11) NOT NULL,
  `from_ip` varchar(32) NOT NULL,
  `add_date` varchar(32) NOT NULL,
  `add_time` int(11) NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '0:??????1:????????????',
  `click_status` tinyint(4) NOT NULL DEFAULT '0',
  `down_status` tinyint(4) NOT NULL DEFAULT '0',
  `v_time` int(11) DEFAULT NULL,
  `device` varchar(32) DEFAULT NULL,
  `download` varchar(255) DEFAULT '',
  `link` varchar(255) DEFAULT '',
  `ad_url` varchar(255) NOT NULL DEFAULT '',
  `http_referer` varchar(2000) NOT NULL DEFAULT '',
  PRIMARY KEY (`aid`),
  KEY `aas` (`ad_id`,`add_time`,`status`) USING BTREE,
  KEY `add_date` (`add_date`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COMMENT='???????????????';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ww_ad_log_1806`
--

DROP TABLE IF EXISTS `ww_ad_log_1806`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ww_ad_log_1806` (
  `aid` bigint(20) NOT NULL AUTO_INCREMENT,
  `sid` varchar(32) NOT NULL,
  `ad_id` int(11) NOT NULL,
  `from_ip` varchar(32) NOT NULL,
  `add_date` varchar(32) NOT NULL,
  `add_time` int(11) NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '0:??????1:????????????',
  `click_status` tinyint(4) NOT NULL DEFAULT '0',
  `down_status` tinyint(4) NOT NULL DEFAULT '0',
  `v_time` int(11) DEFAULT NULL,
  `device` varchar(32) DEFAULT NULL,
  `download` varchar(255) DEFAULT '',
  `link` varchar(255) DEFAULT '',
  `ad_url` varchar(255) NOT NULL DEFAULT '',
  `http_referer` varchar(2000) NOT NULL DEFAULT '',
  PRIMARY KEY (`aid`),
  KEY `aas` (`ad_id`,`add_time`,`status`) USING BTREE,
  KEY `add_date` (`add_date`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=481 DEFAULT CHARSET=utf8 COMMENT='???????????????';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ww_ad_log_1807`
--

DROP TABLE IF EXISTS `ww_ad_log_1807`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ww_ad_log_1807` (
  `aid` bigint(20) NOT NULL AUTO_INCREMENT,
  `sid` varchar(32) NOT NULL,
  `ad_id` int(11) NOT NULL,
  `from_ip` varchar(32) NOT NULL,
  `add_date` varchar(32) NOT NULL,
  `add_time` int(11) NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '0:??????1:????????????',
  `click_status` tinyint(4) NOT NULL DEFAULT '0',
  `down_status` tinyint(4) NOT NULL DEFAULT '0',
  `v_time` int(11) DEFAULT NULL,
  `device` varchar(32) DEFAULT NULL,
  `download` varchar(255) DEFAULT '',
  `link` varchar(255) DEFAULT '',
  `ad_url` varchar(255) NOT NULL DEFAULT '',
  `http_referer` varchar(2000) NOT NULL DEFAULT '',
  PRIMARY KEY (`aid`),
  KEY `aas` (`ad_id`,`add_time`,`status`) USING BTREE,
  KEY `add_date` (`add_date`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=774 DEFAULT CHARSET=utf8 COMMENT='???????????????';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ww_ad_log_1808`
--

DROP TABLE IF EXISTS `ww_ad_log_1808`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ww_ad_log_1808` (
  `aid` bigint(20) NOT NULL AUTO_INCREMENT,
  `sid` varchar(32) NOT NULL,
  `ad_id` int(11) NOT NULL,
  `from_ip` varchar(32) NOT NULL,
  `add_date` varchar(32) NOT NULL,
  `add_time` int(11) NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '0:??????1:????????????',
  `click_status` tinyint(4) NOT NULL DEFAULT '0',
  `down_status` tinyint(4) NOT NULL DEFAULT '0',
  `v_time` int(11) DEFAULT NULL,
  `device` varchar(32) DEFAULT NULL,
  `download` varchar(255) DEFAULT '',
  `link` varchar(255) DEFAULT '',
  `ad_url` varchar(255) NOT NULL DEFAULT '',
  `http_referer` varchar(2000) NOT NULL DEFAULT '',
  PRIMARY KEY (`aid`),
  KEY `aas` (`ad_id`,`add_time`,`status`) USING BTREE,
  KEY `add_date` (`add_date`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=650 DEFAULT CHARSET=utf8 COMMENT='???????????????';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ww_ad_log_1809`
--

DROP TABLE IF EXISTS `ww_ad_log_1809`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ww_ad_log_1809` (
  `aid` bigint(20) NOT NULL AUTO_INCREMENT,
  `sid` varchar(32) NOT NULL,
  `ad_id` int(11) NOT NULL,
  `from_ip` varchar(32) NOT NULL,
  `add_date` varchar(32) NOT NULL,
  `add_time` int(11) NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '0:??????1:????????????',
  `click_status` tinyint(4) NOT NULL DEFAULT '0',
  `down_status` tinyint(4) NOT NULL DEFAULT '0',
  `v_time` int(11) DEFAULT NULL,
  `device` varchar(32) DEFAULT NULL,
  `download` varchar(255) DEFAULT '',
  `link` varchar(255) DEFAULT '',
  `ad_url` varchar(255) NOT NULL DEFAULT '',
  `http_referer` varchar(2000) NOT NULL DEFAULT '',
  PRIMARY KEY (`aid`),
  KEY `aas` (`ad_id`,`add_time`,`status`) USING BTREE,
  KEY `add_date` (`add_date`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='???????????????';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ww_ad_log_month`
--

DROP TABLE IF EXISTS `ww_ad_log_month`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ww_ad_log_month` (
  `aid` bigint(20) NOT NULL AUTO_INCREMENT,
  `sid` varchar(32) NOT NULL,
  `ad_id` int(11) NOT NULL,
  `from_ip` varchar(32) NOT NULL,
  `add_date` varchar(32) NOT NULL,
  `add_time` int(11) NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '0:??????1:????????????',
  `click_status` tinyint(4) NOT NULL DEFAULT '0',
  `down_status` tinyint(4) NOT NULL DEFAULT '0',
  `v_time` int(11) DEFAULT NULL,
  `device` varchar(32) DEFAULT NULL,
  `download` varchar(255) DEFAULT '',
  `link` varchar(255) DEFAULT '',
  `ad_url` varchar(255) NOT NULL DEFAULT '',
  `http_referer` varchar(2000) NOT NULL DEFAULT '',
  PRIMARY KEY (`aid`),
  KEY `aas` (`ad_id`,`add_time`,`status`) USING BTREE,
  KEY `add_date` (`add_date`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='???????????????';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ww_ad_materials`
--

DROP TABLE IF EXISTS `ww_ad_materials`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ww_ad_materials` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ad_id` int(11) NOT NULL DEFAULT '0' COMMENT '??????ID',
  `lp_id` int(11) NOT NULL DEFAULT '0',
  `style_id` int(11) NOT NULL DEFAULT '0' COMMENT '???????????????ID',
  `creative_id` varchar(32) NOT NULL DEFAULT '' COMMENT '????????????ID',
  `status` varchar(32) NOT NULL DEFAULT '' COMMENT '????????????????????????????????????????????????',
  `opt_status` varchar(32) NOT NULL DEFAULT '' COMMENT '????????????, "enable"????????????, "disable"????????????',
  `material_ids` varchar(255) NOT NULL DEFAULT '' COMMENT '??????ids',
  `annex_ids` varchar(255) NOT NULL DEFAULT '' COMMENT '??????ids',
  `designer_img` varchar(255) NOT NULL DEFAULT '' COMMENT '??????????????? ids',
  `designer_video` varchar(255) NOT NULL DEFAULT '' COMMENT '??????????????? ids',
  `designer_video_cover` varchar(255) NOT NULL DEFAULT '' COMMENT '????????????????????? ids',
  `designer_lp` varchar(255) NOT NULL DEFAULT '' COMMENT '?????????????????? ids',
  `content` text,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ad_id` (`ad_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=340 DEFAULT CHARSET=utf8 COMMENT='?????????????????????';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ww_ad_position`
--

DROP TABLE IF EXISTS `ww_ad_position`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ww_ad_position` (
  `position_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL DEFAULT '' COMMENT '???????????????',
  `media_id` int(11) NOT NULL DEFAULT '0' COMMENT '????????????',
  `settlement` varchar(50) NOT NULL DEFAULT '' COMMENT '????????????',
  `remark` varchar(500) NOT NULL DEFAULT '' COMMENT '??????',
  `created_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`position_id`)
) ENGINE=MyISAM AUTO_INCREMENT=82 DEFAULT CHARSET=utf8 COMMENT='?????????';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ww_ad_position_style`
--

DROP TABLE IF EXISTS `ww_ad_position_style`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ww_ad_position_style` (
  `style_id` int(11) NOT NULL AUTO_INCREMENT,
  `position_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '???????????????',
  `style_name` varchar(100) NOT NULL DEFAULT '' COMMENT '????????????',
  `style_info` text NOT NULL COMMENT '????????????JSON',
  `enumerated_value` varchar(50) NOT NULL DEFAULT '' COMMENT '''?????????''',
  PRIMARY KEY (`style_id`),
  KEY `idx_position_id` (`position_id`)
) ENGINE=MyISAM AUTO_INCREMENT=290 DEFAULT CHARSET=utf8 COMMENT='?????????';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ww_ad_template`
--

DROP TABLE IF EXISTS `ww_ad_template`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ww_ad_template` (
  `template_id` int(11) NOT NULL AUTO_INCREMENT,
  `template_url` varchar(500) NOT NULL DEFAULT '' COMMENT '??????URL',
  `template_name` varchar(32) NOT NULL DEFAULT '' COMMENT '????????????',
  `designer` varchar(32) NOT NULL DEFAULT '' COMMENT '?????????',
  `game_id` int(11) NOT NULL DEFAULT '0' COMMENT '??????ID',
  `if_show` tinyint(4) NOT NULL DEFAULT '1' COMMENT '????????????',
  PRIMARY KEY (`template_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='???????????????';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ww_ad_url_site`
--

DROP TABLE IF EXISTS `ww_ad_url_site`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ww_ad_url_site` (
  `ad_url_site_id` int(11) NOT NULL AUTO_INCREMENT,
  `ad_url_site` varchar(32) DEFAULT '' COMMENT '??????',
  PRIMARY KEY (`ad_url_site_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COMMENT='????????????';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ww_ad_user_log`
--

DROP TABLE IF EXISTS `ww_ad_user_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ww_ad_user_log` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `view_time` int(11) DEFAULT NULL,
  `from_ip` varchar(32) DEFAULT NULL,
  `device` varchar(32) DEFAULT NULL,
  `view_times` int(11) DEFAULT NULL,
  PRIMARY KEY (`user_id`),
  KEY `device` (`device`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=132 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ww_admin`
--

DROP TABLE IF EXISTS `ww_admin`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ww_admin` (
  `uid` int(10) NOT NULL AUTO_INCREMENT COMMENT '?????????ID',
  `username` varchar(100) NOT NULL DEFAULT '' COMMENT '??????',
  `phone_mob` varchar(32) NOT NULL DEFAULT '',
  `logins` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '????????????',
  `reg_ip` char(20) NOT NULL DEFAULT '0' COMMENT '??????IP',
  `reg_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '????????????',
  `last_login_ip` char(20) NOT NULL DEFAULT '0' COMMENT '????????????IP',
  `last_login_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '??????????????????',
  `status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '??????????????? 0 ?????? 1??????',
  `modules` varchar(2000) NOT NULL COMMENT '????????????????????????id??????????????? , ??????',
  `about` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`uid`)
) ENGINE=MyISAM AUTO_INCREMENT=81 DEFAULT CHARSET=utf8 COMMENT='?????????';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ww_agent`
--

DROP TABLE IF EXISTS `ww_agent`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ww_agent` (
  `agent_id` int(11) NOT NULL AUTO_INCREMENT,
  `agent_name` varchar(32) NOT NULL DEFAULT '' COMMENT '???????????????',
  `linkman` varchar(32) NOT NULL DEFAULT '' COMMENT '?????????',
  `linkman_phone` varchar(32) NOT NULL DEFAULT '' COMMENT '????????????',
  `email` varchar(100) NOT NULL DEFAULT '' COMMENT '??????',
  `companys` varchar(500) NOT NULL DEFAULT '' COMMENT '?????????????????? ??????',
  `account` varchar(20) NOT NULL DEFAULT '' COMMENT '??????',
  `password` varchar(20) NOT NULL DEFAULT '' COMMENT '??????',
  `media_ids` varchar(255) NOT NULL DEFAULT '' COMMENT '????????????',
  `promote_ids` varchar(255) NOT NULL DEFAULT '' COMMENT '??????????????????',
  `game_ids` varchar(255) NOT NULL DEFAULT '' COMMENT '????????????',
  `can_create_ad` int(1) NOT NULL DEFAULT '0' COMMENT '????????????',
  `data_items` varchar(255) NOT NULL DEFAULT '' COMMENT '???????????????',
  `update_time` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`agent_id`),
  KEY `mediaid` (`media_ids`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=89 DEFAULT CHARSET=utf8 COMMENT='?????????';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ww_dist_toutiao`
--

DROP TABLE IF EXISTS `ww_dist_toutiao`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ww_dist_toutiao` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ad_id` int(11) NOT NULL DEFAULT '0' COMMENT '??????ID',
  `advertiser_id` bigint(20) NOT NULL DEFAULT '0' COMMENT '???????????????ID',
  `campaign_id` bigint(20) NOT NULL DEFAULT '0' COMMENT '???????????????ID',
  `plan_id` bigint(20) NOT NULL DEFAULT '0' COMMENT '??????????????????ID',
  `convert_id` varchar(32) NOT NULL DEFAULT '',
  `title` varchar(255) NOT NULL DEFAULT '' COMMENT '????????????',
  `budget_mode` varchar(45) NOT NULL DEFAULT '',
  `budget` double NOT NULL DEFAULT '0',
  `bid` double NOT NULL DEFAULT '0',
  `web_url` varchar(255) NOT NULL DEFAULT '',
  `app_name` varchar(255) NOT NULL DEFAULT '',
  `sub_title` varchar(255) NOT NULL DEFAULT '',
  `procedural_content` text COMMENT '????????????????????????',
  `status` varchar(32) NOT NULL DEFAULT '' COMMENT '????????????????????????',
  `opt_status` varchar(32) NOT NULL DEFAULT '' COMMENT '????????????????????????, "enable"????????????, "delete"????????????, "disable"????????????,?????????: "AD_STATUS_ENABLE", "AD_STATUS_DISABLE"',
  `audit_reject_reason` varchar(255) NOT NULL DEFAULT '' COMMENT '?????????????????????????????????',
  `modify_time` varchar(32) NOT NULL DEFAULT '' COMMENT '?????????????????????(?????????????????????,?????????????????????????????????????????????)',
  `need_audit` int(11) NOT NULL DEFAULT '0' COMMENT '??????????????????????????????????????????',
  `ad_modify_time` varchar(32) NOT NULL DEFAULT '' COMMENT '????????????????????????',
  `ad_create_time` varchar(32) NOT NULL DEFAULT '' COMMENT '??????????????????',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `district` varchar(255) NOT NULL DEFAULT '' COMMENT '????????????',
  `location_type` varchar(45) NOT NULL DEFAULT '' COMMENT '??????????????????',
  `city` varchar(255) NOT NULL DEFAULT '' COMMENT '????????????????????????????????????',
  `gender` varchar(45) NOT NULL DEFAULT '' COMMENT '??????',
  `age` varchar(255) NOT NULL DEFAULT '[]' COMMENT '??????',
  `ad_tag_type` int(1) NOT NULL DEFAULT '0' COMMENT '??????????????????',
  `ad_tag` varchar(255) NOT NULL DEFAULT '[]' COMMENT '????????????',
  `interest_tags` varchar(255) NOT NULL DEFAULT '[]' COMMENT '???????????????',
  `app_type` varchar(32) NOT NULL DEFAULT 'APP_ANDROID',
  `cpa_bid` float NOT NULL DEFAULT '0' COMMENT 'ocpc???ocpm??????????????????????????????',
  `platform` varchar(45) NOT NULL DEFAULT '' COMMENT '????????????',
  `ac` varchar(45) NOT NULL DEFAULT '[]' COMMENT '??????????????????',
  `android_osv` varchar(45) NOT NULL DEFAULT '',
  `schedule_type` varchar(45) NOT NULL DEFAULT '' COMMENT '????????????????????????',
  `start_time` varchar(45) NOT NULL DEFAULT '' COMMENT '????????????????????????',
  `end_time` varchar(45) NOT NULL DEFAULT '' COMMENT '????????????????????????',
  `schedule_time` varchar(336) NOT NULL DEFAULT '000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000',
  `flow_control_mode` varchar(45) NOT NULL DEFAULT '',
  `selectmode` varchar(45) NOT NULL DEFAULT '' COMMENT '??????????????????: procedural???????????????, customize?????????????????????',
  `creative_display_mode` varchar(45) NOT NULL DEFAULT '' COMMENT '??????????????????',
  `pricing` varchar(45) NOT NULL DEFAULT '' COMMENT '??????????????????',
  `smart_inventory` int(1) NOT NULL DEFAULT '1' COMMENT '??????????????????',
  `inventory_type` varchar(255) NOT NULL DEFAULT '' COMMENT '??????????????????',
  `is_comment_disable` int(1) NOT NULL DEFAULT '0',
  `close_video_detail` int(1) NOT NULL DEFAULT '0',
  `ad_category` int(11) NOT NULL DEFAULT '0',
  `ad_keywords` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=58 DEFAULT CHARSET=utf8 COMMENT='??????????????????????????????????????????????????????????????????';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ww_dist_toutiao_accounts`
--

DROP TABLE IF EXISTS `ww_dist_toutiao_accounts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ww_dist_toutiao_accounts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `promote_id` int(11) NOT NULL DEFAULT '0',
  `access_token` varchar(255) NOT NULL DEFAULT '' COMMENT '????????????ID',
  `expires_in` int(11) NOT NULL DEFAULT '0' COMMENT 'ccess_token??????????????????,??????(???)',
  `refresh_token` varchar(255) NOT NULL DEFAULT '' COMMENT '??????access_token,??????????????????access_token???refresh_token???????????????????????????',
  `advertiser_id` bigint(20) NOT NULL DEFAULT '0' COMMENT '???????????????????????????????????????ID',
  `advertiser_name` varchar(64) NOT NULL DEFAULT '',
  `refresh_token_expires_in` int(11) NOT NULL DEFAULT '0' COMMENT 'refresh_token??????????????????,??????(???)',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8 COMMENT='?????? access_token ??????';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ww_dist_toutiao_dw_plans`
--

DROP TABLE IF EXISTS `ww_dist_toutiao_dw_plans`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ww_dist_toutiao_dw_plans` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `dw_date` date DEFAULT NULL,
  `ad_id` int(11) NOT NULL DEFAULT '0',
  `plan_id` bigint(20) NOT NULL DEFAULT '0',
  `advertiser_id` bigint(20) NOT NULL DEFAULT '0',
  `activate` bigint(20) NOT NULL DEFAULT '0',
  `show` bigint(20) NOT NULL DEFAULT '0',
  `click_install` bigint(20) NOT NULL DEFAULT '0',
  `click_start` bigint(20) NOT NULL DEFAULT '0',
  `vote` bigint(20) NOT NULL DEFAULT '0',
  `shopping` bigint(20) NOT NULL DEFAULT '0',
  `redirect` bigint(20) NOT NULL DEFAULT '0',
  `lottery` bigint(20) NOT NULL DEFAULT '0',
  `send` bigint(20) NOT NULL DEFAULT '0',
  `click` bigint(20) NOT NULL DEFAULT '0',
  `download_finish` bigint(20) NOT NULL DEFAULT '0',
  `stat_cost` bigint(20) NOT NULL DEFAULT '0',
  `download_start` bigint(20) NOT NULL DEFAULT '0',
  `form` bigint(20) NOT NULL DEFAULT '0',
  `phone` bigint(20) NOT NULL DEFAULT '0',
  `wechat` bigint(20) NOT NULL DEFAULT '0',
  `consult` bigint(20) NOT NULL DEFAULT '0',
  `active` bigint(20) NOT NULL DEFAULT '0',
  `click_call` bigint(20) NOT NULL DEFAULT '0',
  `qq` bigint(20) NOT NULL DEFAULT '0',
  `convert` bigint(20) NOT NULL DEFAULT '0',
  `button` bigint(20) NOT NULL DEFAULT '0',
  `map_search` bigint(20) NOT NULL DEFAULT '0',
  `install_finish` bigint(20) NOT NULL DEFAULT '0',
  `stat_datetime` varchar(64) DEFAULT NULL,
  `view` bigint(20) NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=217 DEFAULT CHARSET=utf8 COMMENT='?????????????????????';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ww_dist_toutiao_files`
--

DROP TABLE IF EXISTS `ww_dist_toutiao_files`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ww_dist_toutiao_files` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(32) NOT NULL DEFAULT '' COMMENT '????????????',
  `annex_ids` text COMMENT '????????????ID',
  `file_id` varchar(64) NOT NULL DEFAULT '' COMMENT '????????????ID',
  `advertiser_id` bigint(20) NOT NULL DEFAULT '0' COMMENT '???????????????ID',
  `url` varchar(255) NOT NULL DEFAULT '' COMMENT '????????????',
  `signature` varchar(64) NOT NULL DEFAULT '' COMMENT '??????md5',
  `data` text COMMENT '????????????',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8 COMMENT='????????????????????????';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ww_dist_toutiao_group`
--

DROP TABLE IF EXISTS `ww_dist_toutiao_group`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ww_dist_toutiao_group` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `promote_id` int(11) NOT NULL DEFAULT '0',
  `media_id` int(11) NOT NULL DEFAULT '0',
  `campaign_id` bigint(20) NOT NULL DEFAULT '0' COMMENT '?????????ID',
  `advertiser_id` bigint(20) NOT NULL DEFAULT '0' COMMENT '?????????ID',
  `campaign_name` varchar(255) NOT NULL DEFAULT '' COMMENT '???????????????',
  `budget_mode` varchar(255) NOT NULL DEFAULT '' COMMENT '?????????????????????',
  `budget` float NOT NULL DEFAULT '0' COMMENT '???????????????',
  `landing_type` varchar(255) NOT NULL DEFAULT '' COMMENT '?????????????????????',
  `modify_time` varchar(32) NOT NULL DEFAULT '' COMMENT '??????????????????,?????????????????????,?????????????????????????????????????????????',
  `status` varchar(45) NOT NULL DEFAULT '' COMMENT '???????????????,???????????????-??????????????????',
  `campaign_create_time` varchar(45) NOT NULL DEFAULT '' COMMENT '?????????????????????',
  `campaign_modify_time` varchar(45) NOT NULL DEFAULT '' COMMENT '?????????????????????',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `distribute_sync` varchar(45) NOT NULL DEFAULT '' COMMENT '???????????????0???????????????, 1?????????, 2?????????, 3???????????????4????????????',
  `distribute_msg` varchar(45) NOT NULL DEFAULT '' COMMENT '?????????????????????',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=120 DEFAULT CHARSET=utf8 COMMENT='???????????????';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ww_dist_toutiao_tpl`
--

DROP TABLE IF EXISTS `ww_dist_toutiao_tpl`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ww_dist_toutiao_tpl` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL DEFAULT '' COMMENT '????????????',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `district` varchar(255) NOT NULL DEFAULT '' COMMENT '????????????',
  `location_type` varchar(45) NOT NULL DEFAULT '' COMMENT '??????????????????',
  `city` varchar(255) NOT NULL DEFAULT '' COMMENT '????????????????????????????????????',
  `gender` varchar(45) NOT NULL DEFAULT '' COMMENT '??????',
  `age` varchar(255) NOT NULL DEFAULT '[]' COMMENT '??????',
  `ad_tag_type` int(1) NOT NULL DEFAULT '0' COMMENT '??????????????????',
  `ad_tag` varchar(255) NOT NULL DEFAULT '[]' COMMENT '????????????',
  `interest_tags` varchar(255) NOT NULL DEFAULT '[]' COMMENT '???????????????',
  `app_type` varchar(45) NOT NULL DEFAULT '' COMMENT '????????????????????????',
  `platform` varchar(45) NOT NULL DEFAULT '' COMMENT '????????????',
  `ac` varchar(45) NOT NULL DEFAULT '[]' COMMENT '??????????????????',
  `android_osv` varchar(45) NOT NULL DEFAULT '',
  `schedule_type` varchar(45) NOT NULL DEFAULT '' COMMENT '????????????????????????',
  `start_time` varchar(45) NOT NULL DEFAULT '' COMMENT '????????????????????????',
  `end_time` varchar(45) NOT NULL DEFAULT '' COMMENT '????????????????????????',
  `schedule_time` varchar(336) NOT NULL DEFAULT '000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000',
  `flow_control_mode` varchar(45) NOT NULL DEFAULT '',
  `selectmode` varchar(45) NOT NULL DEFAULT '' COMMENT '??????????????????: procedural???????????????, customize?????????????????????',
  `creative_display_mode` varchar(45) NOT NULL DEFAULT '' COMMENT '??????????????????',
  `pricing` varchar(45) NOT NULL DEFAULT '' COMMENT '??????????????????',
  `smart_inventory` int(1) NOT NULL DEFAULT '1' COMMENT '??????????????????',
  `inventory_type` varchar(255) NOT NULL DEFAULT '' COMMENT '??????????????????',
  `is_comment_disable` int(1) NOT NULL DEFAULT '0',
  `close_video_detail` int(1) NOT NULL DEFAULT '0',
  `ad_category` int(11) NOT NULL DEFAULT '0',
  `ad_keywords` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8 COMMENT='??????????????????';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ww_down`
--

DROP TABLE IF EXISTS `ww_down`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ww_down` (
  `down_id` int(11) NOT NULL AUTO_INCREMENT,
  `admin_id` int(11) DEFAULT '0',
  `down_title` varchar(255) DEFAULT '',
  `down_url` varchar(255) DEFAULT '',
  `down_site` varchar(255) NOT NULL DEFAULT '',
  `game_id` int(11) NOT NULL DEFAULT '0',
  `promote_id` int(11) NOT NULL DEFAULT '0',
  `deliveryed` tinyint(4) NOT NULL DEFAULT '0',
  `game_name` varchar(32) NOT NULL DEFAULT '',
  `promote_name` varchar(32) NOT NULL DEFAULT '',
  `source_pkg` varchar(255) NOT NULL DEFAULT '' COMMENT '????????????',
  `pkg_status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '0???????????? 1????????????',
  `down_site_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`down_id`),
  KEY `game_id_with_promote_id` (`game_id`,`promote_id`),
  KEY `idx_source_pkg` (`source_pkg`)
) ENGINE=InnoDB AUTO_INCREMENT=413 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ww_down_history`
--

DROP TABLE IF EXISTS `ww_down_history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ww_down_history` (
  `history_id` int(11) NOT NULL AUTO_INCREMENT,
  `down_id` int(11) NOT NULL DEFAULT '0',
  `down_url` varchar(255) CHARACTER SET latin1 NOT NULL DEFAULT '',
  `add_time` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`history_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1067 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ww_down_site`
--

DROP TABLE IF EXISTS `ww_down_site`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ww_down_site` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `url` varchar(255) NOT NULL DEFAULT '' COMMENT '??????',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COMMENT='???????????????';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ww_game`
--

DROP TABLE IF EXISTS `ww_game`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ww_game` (
  `game_id` int(11) NOT NULL,
  `game_type` int(11) NOT NULL DEFAULT '0' COMMENT '0:app 1:wap',
  `game_name` varchar(32) NOT NULL DEFAULT '',
  `source_pkg` varchar(255) NOT NULL DEFAULT '' COMMENT '??????',
  `alike_game` varchar(255) NOT NULL DEFAULT '' COMMENT '????????????',
  `lasttime` int(11) NOT NULL DEFAULT '0',
  `access_mode` tinyint(4) NOT NULL DEFAULT '0' COMMENT '1:?????? 2:CPS 3:CPA',
  `game_download_url` varchar(255) NOT NULL DEFAULT '',
  `bundleid` varchar(100) NOT NULL DEFAULT '' COMMENT '??????',
  PRIMARY KEY (`game_id`),
  KEY `game_id` (`game_id`),
  KEY `i_g` (`game_name`) USING BTREE,
  KEY `down_source` (`source_pkg`),
  KEY `idx_access_mode` (`access_mode`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ww_keywords`
--

DROP TABLE IF EXISTS `ww_keywords`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ww_keywords` (
  `keywords_id` int(11) NOT NULL AUTO_INCREMENT,
  `keywords` varchar(32) DEFAULT NULL,
  PRIMARY KEY (`keywords_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ww_landing_page`
--

DROP TABLE IF EXISTS `ww_landing_page`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ww_landing_page` (
  `page_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL DEFAULT '' COMMENT '????????????',
  `game_id` int(11) NOT NULL DEFAULT '0' COMMENT '??????ID',
  `designer_id` int(11) NOT NULL DEFAULT '0' COMMENT '?????????',
  `page_zip_path` varchar(255) NOT NULL DEFAULT '' COMMENT 'zip??????',
  `preview_pic` int(11) DEFAULT NULL COMMENT '????????????',
  `page_path` varchar(255) NOT NULL DEFAULT '' COMMENT '???????????????',
  `remark` varchar(500) NOT NULL DEFAULT '' COMMENT '??????',
  `add_time` int(11) NOT NULL DEFAULT '0',
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`page_id`)
) ENGINE=InnoDB AUTO_INCREMENT=51 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ww_material`
--

DROP TABLE IF EXISTS `ww_material`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ww_material` (
  `material_id` int(11) NOT NULL AUTO_INCREMENT,
  `is_agent` int(11) NOT NULL DEFAULT '0' COMMENT '????????????????????? 1???',
  `designer_id` varchar(32) NOT NULL DEFAULT '0' COMMENT '?????????',
  `designer_name` varchar(100) NOT NULL DEFAULT '' COMMENT '???????????????',
  `name` varchar(100) NOT NULL DEFAULT '' COMMENT '????????????',
  `game_id` int(11) NOT NULL DEFAULT '0' COMMENT '??????ID',
  `media_id` int(11) NOT NULL DEFAULT '0' COMMENT '??????ID',
  `position_id` int(11) NOT NULL DEFAULT '0' COMMENT '?????????',
  `tags` varchar(500) NOT NULL DEFAULT '' COMMENT '?????? ??????1#??????2',
  `type` tinyint(4) NOT NULL DEFAULT '0' COMMENT '0 ?????? 1??????',
  `remark` varchar(500) NOT NULL COMMENT '??????',
  `add_time` int(11) NOT NULL DEFAULT '0' COMMENT '????????????',
  `last_edit_time` int(11) NOT NULL DEFAULT '0' COMMENT '??????????????????',
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`material_id`)
) ENGINE=MyISAM AUTO_INCREMENT=111 DEFAULT CHARSET=utf8 COMMENT='??????';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ww_material_annex`
--

DROP TABLE IF EXISTS `ww_material_annex`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ww_material_annex` (
  `annex_id` int(11) NOT NULL AUTO_INCREMENT,
  `material_id` int(11) NOT NULL DEFAULT '0' COMMENT '??????ID',
  `annex_type` tinyint(4) NOT NULL DEFAULT '0' COMMENT '???????????? 0 ?????? 1?????? 2????????? 3????????????',
  `file_type` varchar(20) NOT NULL DEFAULT '' COMMENT '????????????',
  `file_size` int(11) NOT NULL DEFAULT '0' COMMENT '????????????',
  `file_name` varchar(100) NOT NULL DEFAULT '' COMMENT '????????????',
  `file_width` double NOT NULL,
  `file_height` double NOT NULL,
  `file_path` varchar(255) NOT NULL DEFAULT '' COMMENT '????????????',
  `file_url` varchar(255) NOT NULL DEFAULT '' COMMENT '??????URL',
  `video_time` int(11) NOT NULL DEFAULT '0' COMMENT '????????????',
  `add_time` int(11) NOT NULL DEFAULT '0' COMMENT '????????????',
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`annex_id`),
  KEY `annex_type` (`annex_type`),
  KEY `material_id` (`material_id`),
  KEY `annex_type_with_material_id` (`annex_type`,`material_id`)
) ENGINE=MyISAM AUTO_INCREMENT=537 DEFAULT CHARSET=utf8 COMMENT='????????????';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ww_media`
--

DROP TABLE IF EXISTS `ww_media`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ww_media` (
  `media_id` int(11) NOT NULL AUTO_INCREMENT,
  `media_name` varchar(255) DEFAULT NULL,
  `manage_url` varchar(255) DEFAULT NULL COMMENT '????????????????????????',
  `settlement` varchar(100) DEFAULT NULL COMMENT '???????????? ??????',
  `remark` varchar(500) NOT NULL DEFAULT '' COMMENT '??????',
  `media_key` varchar(50) DEFAULT NULL,
  `update_time` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`media_id`)
) ENGINE=InnoDB AUTO_INCREMENT=99 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ww_newad`
--

DROP TABLE IF EXISTS `ww_newad`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ww_newad` (
  `newad_id` int(11) NOT NULL AUTO_INCREMENT,
  `ad_name` varchar(32) NOT NULL DEFAULT '',
  `ad_site` int(11) NOT NULL DEFAULT '0',
  `game_id` int(11) NOT NULL DEFAULT '0',
  `game_name` varchar(32) DEFAULT '',
  `media_id` int(11) NOT NULL DEFAULT '0',
  `media_name` varchar(32) NOT NULL DEFAULT '',
  `promote_id` int(11) NOT NULL DEFAULT '0',
  `promote_name` varchar(32) DEFAULT NULL,
  `sub_promote_ids` varchar(1000) NOT NULL,
  `sub_promote_name` varchar(2000) NOT NULL,
  `template_id` int(11) NOT NULL DEFAULT '0',
  `site_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`newad_id`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ww_promote`
--

DROP TABLE IF EXISTS `ww_promote`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ww_promote` (
  `promote_id` int(11) NOT NULL AUTO_INCREMENT,
  `promote_name` varchar(32) DEFAULT NULL,
  `promote_account` varchar(32) DEFAULT NULL,
  `promote_password` varchar(32) DEFAULT NULL,
  `media_id` int(11) NOT NULL DEFAULT '0',
  `agent_id` int(11) NOT NULL DEFAULT '0',
  `promote_parent` int(4) NOT NULL DEFAULT '0',
  `promote_type` tinyint(4) NOT NULL DEFAULT '1' COMMENT '1:???????????? 2:??????',
  `status` tinyint(4) DEFAULT NULL,
  `rules` varchar(255) NOT NULL DEFAULT '',
  `add_time` int(11) DEFAULT NULL,
  `edit_time` int(11) DEFAULT NULL,
  `promote_money` decimal(10,2) NOT NULL DEFAULT '0.00',
  `ad_account` varchar(255) NOT NULL DEFAULT '',
  `show_sales` tinyint(4) NOT NULL DEFAULT '0',
  `access_token` varchar(32) NOT NULL DEFAULT '',
  `ad_url_id` int(11) NOT NULL DEFAULT '0' COMMENT '????????????',
  `ad_site_id` int(11) NOT NULL DEFAULT '0' COMMENT '???????????????',
  `company_name` varchar(100) NOT NULL DEFAULT '' COMMENT '????????????(????????????',
  `company_addr` varchar(255) NOT NULL DEFAULT '' COMMENT '????????????',
  `company_tel` varchar(45) NOT NULL DEFAULT '' COMMENT '????????????',
  `manage_url` varchar(255) DEFAULT '' COMMENT '????????????',
  `promote_key` varchar(50) DEFAULT NULL,
  `update_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `distribute` varchar(45) NOT NULL DEFAULT 'Normal' COMMENT '????????????????????????Normal:???????????????Toutiao:????????????',
  `has_api` char(2) DEFAULT '0',
  PRIMARY KEY (`promote_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1526 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ww_province`
--

DROP TABLE IF EXISTS `ww_province`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ww_province` (
  `p_id` int(11) NOT NULL AUTO_INCREMENT,
  `p_name` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`p_id`)
) ENGINE=InnoDB AUTO_INCREMENT=36 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ww_rel_agent_media`
--

DROP TABLE IF EXISTS `ww_rel_agent_media`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ww_rel_agent_media` (
  `agent_id` int(11) NOT NULL DEFAULT '0',
  `media_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`agent_id`,`media_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='???????????????????????????';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ww_role`
--

DROP TABLE IF EXISTS `ww_role`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ww_role` (
  `role_id` int(11) NOT NULL AUTO_INCREMENT,
  `ad_id` int(11) NOT NULL,
  `role_name` varchar(255) NOT NULL DEFAULT '',
  `from_hour` tinyint(4) NOT NULL DEFAULT '0',
  `to_hour` tinyint(4) NOT NULL DEFAULT '24',
  `province` varchar(500) NOT NULL,
  `url` text NOT NULL,
  `sort` tinyint(255) NOT NULL DEFAULT '0',
  PRIMARY KEY (`role_id`),
  KEY `ah` (`ad_id`,`from_hour`,`to_hour`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ww_site`
--

DROP TABLE IF EXISTS `ww_site`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ww_site` (
  `site_id` int(11) NOT NULL AUTO_INCREMENT,
  `site_name` varchar(255) NOT NULL DEFAULT '' COMMENT '????????????',
  `site_url` varchar(255) NOT NULL COMMENT '??????',
  `icp` varchar(255) NOT NULL DEFAULT '' COMMENT 'ICP',
  PRIMARY KEY (`site_id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COMMENT='???????????????';
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2018-08-22 16:29:05
