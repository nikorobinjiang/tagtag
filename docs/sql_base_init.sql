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
  `ad_sn` varchar(32) NOT NULL DEFAULT '' COMMENT '广告唯一编码',
  `position_id` int(11) NOT NULL DEFAULT '0' COMMENT '广告位ID',
  `ad_name` varchar(32) DEFAULT '' COMMENT '广告名称 生成 sub game',
  `game_id` int(11) NOT NULL DEFAULT '0' COMMENT '游戏ID',
  `settlement` varchar(50) NOT NULL DEFAULT '' COMMENT '结算方式,广告出价类型',
  `ad_url_id` int(11) NOT NULL DEFAULT '0' COMMENT '广告跳转域名',
  `ad_site_id` int(11) NOT NULL DEFAULT '0' COMMENT '落地页域名ID',
  `has_landing_page` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否有落地页',
  `ad_url` varchar(255) DEFAULT '' COMMENT '落地页完整URL',
  `style_id` int(11) NOT NULL DEFAULT '0' COMMENT '广告位样式 ID，考虑废弃',
  `style_type` tinyint(4) NOT NULL DEFAULT '0' COMMENT '素材样式 1单图 3三图',
  `admin_id` int(11) DEFAULT '0' COMMENT '管理员ID',
  `create_time` int(11) DEFAULT '0' COMMENT '创建时间',
  `is_hide` tinyint(4) NOT NULL DEFAULT '0',
  `media_id` int(11) NOT NULL DEFAULT '0' COMMENT '媒体ID',
  `agent_id` int(11) NOT NULL DEFAULT '0' COMMENT '代理商ID',
  `promote_id` int(11) NOT NULL DEFAULT '0' COMMENT '媒体账号',
  `sub_promote_id` int(11) NOT NULL DEFAULT '0' COMMENT '子媒体账号',
  `template_id` int(11) NOT NULL DEFAULT '0' COMMENT '落地页模板ID',
  `designer_ids` varchar(255) NOT NULL DEFAULT '' COMMENT '所有参与的设计师id',
  `material_info` varchar(1000) NOT NULL DEFAULT '' COMMENT '已废弃',
  `add_time` int(11) NOT NULL DEFAULT '0',
  `edit_time` int(11) NOT NULL DEFAULT '0',
  `down_id` int(11) NOT NULL DEFAULT '0',
  `status` tinyint(4) NOT NULL DEFAULT '1' COMMENT '广告状态：1->正在投放   0->已下架',
  `is_multi_materials` int(1) NOT NULL DEFAULT '0' COMMENT '是否支持多组素材： 0否，1是',
  `distribute` varchar(45) NOT NULL DEFAULT 'Normal' COMMENT '广告分发点，Normal:普通广告，Toutiao:头条广告',
  `distribute_sync` tinyint(4) NOT NULL DEFAULT '0' COMMENT '同步状态：0物料待完善, 1可同步, 2同步中, 3同步失败',
  `distribute_msg` varchar(255) NOT NULL DEFAULT '' COMMENT '广告分发结果',
  `ad_group_id` int(11) NOT NULL DEFAULT '0' COMMENT '广告组id',
  `ad_tpl_id` int(11) NOT NULL DEFAULT '0' COMMENT '广告模板id',
  `newad_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`ad_id`),
  UNIQUE KEY `sn` (`ad_sn`) USING BTREE,
  KEY `styleid` (`style_id`),
  KEY `idx_search` (`media_id`,`promote_id`,`agent_id`,`settlement`,`game_id`,`position_id`,`status`,`down_id`),
  KEY `down_id` (`down_id`)
) ENGINE=InnoDB AUTO_INCREMENT=342 DEFAULT CHARSET=utf8 COMMENT='广告表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ww_ad_designers`
--

DROP TABLE IF EXISTS `ww_ad_designers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ww_ad_designers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ad_id` int(11) NOT NULL DEFAULT '0' COMMENT '广告ID',
  `pic` varchar(255) NOT NULL DEFAULT '' COMMENT '图片设计师',
  `video` varchar(255) NOT NULL DEFAULT '' COMMENT '视频设计师',
  `video_idx` varchar(255) NOT NULL DEFAULT '' COMMENT '视频封面设计师',
  `lp` varchar(255) NOT NULL DEFAULT '' COMMENT '落地页设计师',
  `all` varchar(255) NOT NULL DEFAULT '' COMMENT '所有设计师',
  `pic_str` varchar(255) NOT NULL DEFAULT '',
  `video_str` varchar(255) NOT NULL DEFAULT '',
  `video_idx_str` varchar(255) NOT NULL DEFAULT '',
  `lp_str` varchar(255) NOT NULL DEFAULT '',
  `all_str` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=85 DEFAULT CHARSET=utf8 COMMENT='广告设计师统计表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ww_ad_dw`
--

DROP TABLE IF EXISTS `ww_ad_dw`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ww_ad_dw` (
  `dw_id` int(11) NOT NULL AUTO_INCREMENT,
  `dw_date` date DEFAULT NULL COMMENT '日期',
  `ad_id` int(11) NOT NULL DEFAULT '0' COMMENT '广告ID',
  `user_count` int(11) NOT NULL DEFAULT '0' COMMENT '用户量',
  `ip_count` int(11) NOT NULL DEFAULT '0',
  `view_count` int(11) NOT NULL DEFAULT '0' COMMENT '查看',
  `load_count` int(11) NOT NULL DEFAULT '0' COMMENT '加载完成',
  `down_count` int(11) NOT NULL DEFAULT '0' COMMENT '下载',
  `finish_count` int(11) NOT NULL DEFAULT '0' COMMENT '下载完成',
  PRIMARY KEY (`dw_id`),
  UNIQUE KEY `da` (`dw_date`,`ad_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1083 DEFAULT CHARSET=utf8 COMMENT='广告统计表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ww_ad_dw_material`
--

DROP TABLE IF EXISTS `ww_ad_dw_material`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ww_ad_dw_material` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` date DEFAULT NULL COMMENT '日期',
  `ad_id` int(11) NOT NULL DEFAULT '0' COMMENT '广告ID',
  `material_id` int(11) NOT NULL DEFAULT '0' COMMENT '广告素材（创意）ID',
  `user_count` int(11) NOT NULL DEFAULT '0' COMMENT '用户量',
  `ip_count` int(11) NOT NULL DEFAULT '0',
  `view_count` int(11) NOT NULL DEFAULT '0' COMMENT '查看',
  `load_count` int(11) NOT NULL DEFAULT '0' COMMENT '加载完成',
  `down_count` int(11) NOT NULL DEFAULT '0' COMMENT '下载',
  `finish_count` int(11) NOT NULL DEFAULT '0' COMMENT '下载完成',
  `show` int(11) NOT NULL DEFAULT '0' COMMENT '展示数',
  `click` int(11) NOT NULL DEFAULT '0' COMMENT '点击数',
  PRIMARY KEY (`id`),
  UNIQUE KEY `da` (`date`,`ad_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='广告素材统计表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ww_ad_log_1706`
--

DROP TABLE IF EXISTS `ww_ad_log_1706`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ww_ad_log_1706` (
  `aid` int(11) NOT NULL AUTO_INCREMENT,
  `sid` varchar(32) NOT NULL DEFAULT '' COMMENT '用户唯一标识',
  `ad_id` int(11) NOT NULL DEFAULT '0' COMMENT '广告ID',
  `from_ip` varchar(32) NOT NULL DEFAULT '' COMMENT '来源IP',
  `add_date` varchar(32) NOT NULL DEFAULT '' COMMENT '创建日期',
  `add_time` int(11) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '状态 0:点击1:加载完成',
  `click_status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '点击状态',
  `down_status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '下载状态',
  `v_time` int(11) DEFAULT NULL,
  `device` varchar(32) DEFAULT NULL,
  `download` varchar(255) DEFAULT '',
  `link` varchar(255) DEFAULT '',
  `ad_url` varchar(255) NOT NULL DEFAULT '',
  `http_referer` varchar(2000) NOT NULL DEFAULT '',
  PRIMARY KEY (`aid`),
  KEY `aas` (`ad_id`,`add_time`,`status`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='广告日志 月表';
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
  `status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '0:点击1:加载完成',
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
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COMMENT='广告日志表';
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
  `status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '0:点击1:加载完成',
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
) ENGINE=InnoDB AUTO_INCREMENT=481 DEFAULT CHARSET=utf8 COMMENT='广告日志表';
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
  `status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '0:点击1:加载完成',
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
) ENGINE=InnoDB AUTO_INCREMENT=774 DEFAULT CHARSET=utf8 COMMENT='广告日志表';
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
  `status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '0:点击1:加载完成',
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
) ENGINE=InnoDB AUTO_INCREMENT=650 DEFAULT CHARSET=utf8 COMMENT='广告日志表';
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
  `status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '0:点击1:加载完成',
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='广告日志表';
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
  `status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '0:点击1:加载完成',
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='广告日志表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ww_ad_materials`
--

DROP TABLE IF EXISTS `ww_ad_materials`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ww_ad_materials` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ad_id` int(11) NOT NULL DEFAULT '0' COMMENT '广告ID',
  `lp_id` int(11) NOT NULL DEFAULT '0',
  `style_id` int(11) NOT NULL DEFAULT '0' COMMENT '广告位样式ID',
  `creative_id` varchar(32) NOT NULL DEFAULT '' COMMENT '头条创意ID',
  `status` varchar(32) NOT NULL DEFAULT '' COMMENT '创意状态，包含审核状态和投放状态',
  `opt_status` varchar(32) NOT NULL DEFAULT '' COMMENT '创意状态, "enable"表示启用, "disable"表示暂停',
  `material_ids` varchar(255) NOT NULL DEFAULT '' COMMENT '素材ids',
  `annex_ids` varchar(255) NOT NULL DEFAULT '' COMMENT '附件ids',
  `designer_img` varchar(255) NOT NULL DEFAULT '' COMMENT '图片设计师 ids',
  `designer_video` varchar(255) NOT NULL DEFAULT '' COMMENT '视频设计师 ids',
  `designer_video_cover` varchar(255) NOT NULL DEFAULT '' COMMENT '视频封面设计师 ids',
  `designer_lp` varchar(255) NOT NULL DEFAULT '' COMMENT '落地页设计师 ids',
  `content` text,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ad_id` (`ad_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=340 DEFAULT CHARSET=utf8 COMMENT='广告素材信息表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ww_ad_position`
--

DROP TABLE IF EXISTS `ww_ad_position`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ww_ad_position` (
  `position_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL DEFAULT '' COMMENT '广告位名称',
  `media_id` int(11) NOT NULL DEFAULT '0' COMMENT '所属媒体',
  `settlement` varchar(50) NOT NULL DEFAULT '' COMMENT '结算方式',
  `remark` varchar(500) NOT NULL DEFAULT '' COMMENT '备注',
  `created_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`position_id`)
) ENGINE=MyISAM AUTO_INCREMENT=82 DEFAULT CHARSET=utf8 COMMENT='广告位';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ww_ad_position_style`
--

DROP TABLE IF EXISTS `ww_ad_position_style`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ww_ad_position_style` (
  `style_id` int(11) NOT NULL AUTO_INCREMENT,
  `position_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '所属广告位',
  `style_name` varchar(100) NOT NULL DEFAULT '' COMMENT '样式名称',
  `style_info` text NOT NULL COMMENT '样式详情JSON',
  `enumerated_value` varchar(50) NOT NULL DEFAULT '' COMMENT '''枚举值''',
  PRIMARY KEY (`style_id`),
  KEY `idx_position_id` (`position_id`)
) ENGINE=MyISAM AUTO_INCREMENT=290 DEFAULT CHARSET=utf8 COMMENT='广告位';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ww_ad_template`
--

DROP TABLE IF EXISTS `ww_ad_template`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ww_ad_template` (
  `template_id` int(11) NOT NULL AUTO_INCREMENT,
  `template_url` varchar(500) NOT NULL DEFAULT '' COMMENT '模板URL',
  `template_name` varchar(32) NOT NULL DEFAULT '' COMMENT '模板名称',
  `designer` varchar(32) NOT NULL DEFAULT '' COMMENT '设计师',
  `game_id` int(11) NOT NULL DEFAULT '0' COMMENT '游戏ID',
  `if_show` tinyint(4) NOT NULL DEFAULT '1' COMMENT '是否显示',
  PRIMARY KEY (`template_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='落地页模板';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ww_ad_url_site`
--

DROP TABLE IF EXISTS `ww_ad_url_site`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ww_ad_url_site` (
  `ad_url_site_id` int(11) NOT NULL AUTO_INCREMENT,
  `ad_url_site` varchar(32) DEFAULT '' COMMENT '域名',
  PRIMARY KEY (`ad_url_site_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COMMENT='广告域名';
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
  `uid` int(10) NOT NULL AUTO_INCREMENT COMMENT '管理员ID',
  `username` varchar(100) NOT NULL DEFAULT '' COMMENT '账号',
  `phone_mob` varchar(32) NOT NULL DEFAULT '',
  `logins` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '登录次数',
  `reg_ip` char(20) NOT NULL DEFAULT '0' COMMENT '注册IP',
  `reg_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '注册时间',
  `last_login_ip` char(20) NOT NULL DEFAULT '0' COMMENT '最后登录IP',
  `last_login_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '最后登录时间',
  `status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '管理员状态 0 禁用 1启用',
  `modules` varchar(2000) NOT NULL COMMENT '用户组拥有的规则id，多个规则 , 隔开',
  `about` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`uid`)
) ENGINE=MyISAM AUTO_INCREMENT=81 DEFAULT CHARSET=utf8 COMMENT='会员表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ww_agent`
--

DROP TABLE IF EXISTS `ww_agent`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ww_agent` (
  `agent_id` int(11) NOT NULL AUTO_INCREMENT,
  `agent_name` varchar(32) NOT NULL DEFAULT '' COMMENT '代理商名称',
  `linkman` varchar(32) NOT NULL DEFAULT '' COMMENT '联系人',
  `linkman_phone` varchar(32) NOT NULL DEFAULT '' COMMENT '联系电话',
  `email` varchar(100) NOT NULL DEFAULT '' COMMENT '邮箱',
  `companys` varchar(500) NOT NULL DEFAULT '' COMMENT '落地页公司名 多个',
  `account` varchar(20) NOT NULL DEFAULT '' COMMENT '账号',
  `password` varchar(20) NOT NULL DEFAULT '' COMMENT '密码',
  `media_ids` varchar(255) NOT NULL DEFAULT '' COMMENT '负责媒体',
  `promote_ids` varchar(255) NOT NULL DEFAULT '' COMMENT '负责媒体账号',
  `game_ids` varchar(255) NOT NULL DEFAULT '' COMMENT '负责游戏',
  `can_create_ad` int(1) NOT NULL DEFAULT '0' COMMENT '创建广告',
  `data_items` varchar(255) NOT NULL DEFAULT '' COMMENT '查看数据项',
  `update_time` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`agent_id`),
  KEY `mediaid` (`media_ids`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=89 DEFAULT CHARSET=utf8 COMMENT='代理商';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ww_dist_toutiao`
--

DROP TABLE IF EXISTS `ww_dist_toutiao`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ww_dist_toutiao` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ad_id` int(11) NOT NULL DEFAULT '0' COMMENT '广告ID',
  `advertiser_id` bigint(20) NOT NULL DEFAULT '0' COMMENT '头条广告主ID',
  `campaign_id` bigint(20) NOT NULL DEFAULT '0' COMMENT '头条广告组ID',
  `plan_id` bigint(20) NOT NULL DEFAULT '0' COMMENT '头条广告计划ID',
  `convert_id` varchar(32) NOT NULL DEFAULT '',
  `title` varchar(255) NOT NULL DEFAULT '' COMMENT '计划名称',
  `budget_mode` varchar(45) NOT NULL DEFAULT '',
  `budget` double NOT NULL DEFAULT '0',
  `bid` double NOT NULL DEFAULT '0',
  `web_url` varchar(255) NOT NULL DEFAULT '',
  `app_name` varchar(255) NOT NULL DEFAULT '',
  `sub_title` varchar(255) NOT NULL DEFAULT '',
  `procedural_content` text COMMENT '程序化创意元数据',
  `status` varchar(32) NOT NULL DEFAULT '' COMMENT '广告计划投放状态',
  `opt_status` varchar(32) NOT NULL DEFAULT '' COMMENT '广告计划操作状态, "enable"表示启用, "delete"表示删除, "disable"表示暂停,允许值: "AD_STATUS_ENABLE", "AD_STATUS_DISABLE"',
  `audit_reject_reason` varchar(255) NOT NULL DEFAULT '' COMMENT '广告计划审核不通过原因',
  `modify_time` varchar(32) NOT NULL DEFAULT '' COMMENT '上次修改时间戳(用于更新时提交,服务端判断是否基于最新信息修改)',
  `need_audit` int(11) NOT NULL DEFAULT '0' COMMENT '此次修改是否触发进入待审状态',
  `ad_modify_time` varchar(32) NOT NULL DEFAULT '' COMMENT '计划上次修改时间',
  `ad_create_time` varchar(32) NOT NULL DEFAULT '' COMMENT '计划创建时间',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `district` varchar(255) NOT NULL DEFAULT '' COMMENT '地域类型',
  `location_type` varchar(45) NOT NULL DEFAULT '' COMMENT '受众位置类型',
  `city` varchar(255) NOT NULL DEFAULT '' COMMENT '地域定向城市或者区县列表',
  `gender` varchar(45) NOT NULL DEFAULT '' COMMENT '性别',
  `age` varchar(255) NOT NULL DEFAULT '[]' COMMENT '年龄',
  `ad_tag_type` int(1) NOT NULL DEFAULT '0' COMMENT '兴趣范围类型',
  `ad_tag` varchar(255) NOT NULL DEFAULT '[]' COMMENT '兴趣分类',
  `interest_tags` varchar(255) NOT NULL DEFAULT '[]' COMMENT '兴趣关键词',
  `app_type` varchar(32) NOT NULL DEFAULT 'APP_ANDROID',
  `cpa_bid` float NOT NULL DEFAULT '0' COMMENT 'ocpc、ocpm广告第二阶段广告出价',
  `platform` varchar(45) NOT NULL DEFAULT '' COMMENT '受众平台',
  `ac` varchar(45) NOT NULL DEFAULT '[]' COMMENT '受众网络类型',
  `android_osv` varchar(45) NOT NULL DEFAULT '',
  `schedule_type` varchar(45) NOT NULL DEFAULT '' COMMENT '广告投放时间类型',
  `start_time` varchar(45) NOT NULL DEFAULT '' COMMENT '广告投放起始时间',
  `end_time` varchar(45) NOT NULL DEFAULT '' COMMENT '广告投放结束时间',
  `schedule_time` varchar(336) NOT NULL DEFAULT '000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000',
  `flow_control_mode` varchar(45) NOT NULL DEFAULT '',
  `selectmode` varchar(45) NOT NULL DEFAULT '' COMMENT '创意生成方式: procedural程序化创意, customize自定义组合创意',
  `creative_display_mode` varchar(45) NOT NULL DEFAULT '' COMMENT '创意展现方式',
  `pricing` varchar(45) NOT NULL DEFAULT '' COMMENT '广告出价类型',
  `smart_inventory` int(1) NOT NULL DEFAULT '1' COMMENT '设置投放位置',
  `inventory_type` varchar(255) NOT NULL DEFAULT '' COMMENT '创意投放位置',
  `is_comment_disable` int(1) NOT NULL DEFAULT '0',
  `close_video_detail` int(1) NOT NULL DEFAULT '0',
  `ad_category` int(11) NOT NULL DEFAULT '0',
  `ad_keywords` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=58 DEFAULT CHARSET=utf8 COMMENT='头条广告管理总表，其中包含头条计划、头条创意';
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
  `access_token` varchar(255) NOT NULL DEFAULT '' COMMENT '本地附件ID',
  `expires_in` int(11) NOT NULL DEFAULT '0' COMMENT 'ccess_token剩余有效时间,单位(秒)',
  `refresh_token` varchar(255) NOT NULL DEFAULT '' COMMENT '刷新access_token,用于获取新的access_token和refresh_token，并且刷新过期时间',
  `advertiser_id` bigint(20) NOT NULL DEFAULT '0' COMMENT '登录用户对应的头条广告账户ID',
  `advertiser_name` varchar(64) NOT NULL DEFAULT '',
  `refresh_token_expires_in` int(11) NOT NULL DEFAULT '0' COMMENT 'refresh_token剩余有效时间,单位(秒)',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8 COMMENT='头条 access_token 记录';
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
) ENGINE=InnoDB AUTO_INCREMENT=217 DEFAULT CHARSET=utf8 COMMENT='头条计划统计表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ww_dist_toutiao_files`
--

DROP TABLE IF EXISTS `ww_dist_toutiao_files`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ww_dist_toutiao_files` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(32) NOT NULL DEFAULT '' COMMENT '文件类型',
  `annex_ids` text COMMENT '本地附件ID',
  `file_id` varchar(64) NOT NULL DEFAULT '' COMMENT '头条文件ID',
  `advertiser_id` bigint(20) NOT NULL DEFAULT '0' COMMENT '头条广告主ID',
  `url` varchar(255) NOT NULL DEFAULT '' COMMENT '预览地址',
  `signature` varchar(64) NOT NULL DEFAULT '' COMMENT '文件md5',
  `data` text COMMENT '上传结果',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8 COMMENT='头条上传文件同步';
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
  `campaign_id` bigint(20) NOT NULL DEFAULT '0' COMMENT '广告组ID',
  `advertiser_id` bigint(20) NOT NULL DEFAULT '0' COMMENT '广告主ID',
  `campaign_name` varchar(255) NOT NULL DEFAULT '' COMMENT '广告组名称',
  `budget_mode` varchar(255) NOT NULL DEFAULT '' COMMENT '广告组预算类型',
  `budget` float NOT NULL DEFAULT '0' COMMENT '广告组预算',
  `landing_type` varchar(255) NOT NULL DEFAULT '' COMMENT '广告组推广目的',
  `modify_time` varchar(32) NOT NULL DEFAULT '' COMMENT '广告组时间戳,用于更新时提交,服务端判断是否基于最新信息修改',
  `status` varchar(45) NOT NULL DEFAULT '' COMMENT '广告组状态,详见【附录-广告组状态】',
  `campaign_create_time` varchar(45) NOT NULL DEFAULT '' COMMENT '广告组创建时间',
  `campaign_modify_time` varchar(45) NOT NULL DEFAULT '' COMMENT '广告组修改时间',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `distribute_sync` varchar(45) NOT NULL DEFAULT '' COMMENT '同步状态：0物料待完善, 1可同步, 2同步中, 3同步失败，4同步成功',
  `distribute_msg` varchar(45) NOT NULL DEFAULT '' COMMENT '广告组同步结果',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=120 DEFAULT CHARSET=utf8 COMMENT='头条广告组';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ww_dist_toutiao_tpl`
--

DROP TABLE IF EXISTS `ww_dist_toutiao_tpl`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ww_dist_toutiao_tpl` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL DEFAULT '' COMMENT '模板名称',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `district` varchar(255) NOT NULL DEFAULT '' COMMENT '地域类型',
  `location_type` varchar(45) NOT NULL DEFAULT '' COMMENT '受众位置类型',
  `city` varchar(255) NOT NULL DEFAULT '' COMMENT '地域定向城市或者区县列表',
  `gender` varchar(45) NOT NULL DEFAULT '' COMMENT '性别',
  `age` varchar(255) NOT NULL DEFAULT '[]' COMMENT '年龄',
  `ad_tag_type` int(1) NOT NULL DEFAULT '0' COMMENT '兴趣范围类型',
  `ad_tag` varchar(255) NOT NULL DEFAULT '[]' COMMENT '兴趣分类',
  `interest_tags` varchar(255) NOT NULL DEFAULT '[]' COMMENT '兴趣关键词',
  `app_type` varchar(45) NOT NULL DEFAULT '' COMMENT '广告应用下载类型',
  `platform` varchar(45) NOT NULL DEFAULT '' COMMENT '受众平台',
  `ac` varchar(45) NOT NULL DEFAULT '[]' COMMENT '受众网络类型',
  `android_osv` varchar(45) NOT NULL DEFAULT '',
  `schedule_type` varchar(45) NOT NULL DEFAULT '' COMMENT '广告投放时间类型',
  `start_time` varchar(45) NOT NULL DEFAULT '' COMMENT '广告投放起始时间',
  `end_time` varchar(45) NOT NULL DEFAULT '' COMMENT '广告投放结束时间',
  `schedule_time` varchar(336) NOT NULL DEFAULT '000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000',
  `flow_control_mode` varchar(45) NOT NULL DEFAULT '',
  `selectmode` varchar(45) NOT NULL DEFAULT '' COMMENT '创意生成方式: procedural程序化创意, customize自定义组合创意',
  `creative_display_mode` varchar(45) NOT NULL DEFAULT '' COMMENT '创意展现方式',
  `pricing` varchar(45) NOT NULL DEFAULT '' COMMENT '广告出价类型',
  `smart_inventory` int(1) NOT NULL DEFAULT '1' COMMENT '设置投放位置',
  `inventory_type` varchar(255) NOT NULL DEFAULT '' COMMENT '创意投放位置',
  `is_comment_disable` int(1) NOT NULL DEFAULT '0',
  `close_video_detail` int(1) NOT NULL DEFAULT '0',
  `ad_category` int(11) NOT NULL DEFAULT '0',
  `ad_keywords` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8 COMMENT='头条广告模板';
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
  `source_pkg` varchar(255) NOT NULL DEFAULT '' COMMENT '母包名称',
  `pkg_status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '0等待打包 1打包完成',
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
  `url` varchar(255) NOT NULL DEFAULT '' COMMENT '链接',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COMMENT='反劫持域名';
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
  `source_pkg` varchar(255) NOT NULL DEFAULT '' COMMENT '母包',
  `alike_game` varchar(255) NOT NULL DEFAULT '' COMMENT '相同游戏',
  `lasttime` int(11) NOT NULL DEFAULT '0',
  `access_mode` tinyint(4) NOT NULL DEFAULT '0' COMMENT '1:联运 2:CPS 3:CPA',
  `game_download_url` varchar(255) NOT NULL DEFAULT '',
  `bundleid` varchar(100) NOT NULL DEFAULT '' COMMENT '包名',
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
  `name` varchar(100) NOT NULL DEFAULT '' COMMENT '素材名称',
  `game_id` int(11) NOT NULL DEFAULT '0' COMMENT '游戏ID',
  `designer_id` int(11) NOT NULL DEFAULT '0' COMMENT '设计师',
  `page_zip_path` varchar(255) NOT NULL DEFAULT '' COMMENT 'zip路径',
  `preview_pic` int(11) DEFAULT NULL COMMENT '预览图片',
  `page_path` varchar(255) NOT NULL DEFAULT '' COMMENT '落地页路径',
  `remark` varchar(500) NOT NULL DEFAULT '' COMMENT '备注',
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
  `is_agent` int(11) NOT NULL DEFAULT '0' COMMENT '是否代理商素材 1是',
  `designer_id` varchar(32) NOT NULL DEFAULT '0' COMMENT '设计师',
  `designer_name` varchar(100) NOT NULL DEFAULT '' COMMENT '设计师名称',
  `name` varchar(100) NOT NULL DEFAULT '' COMMENT '素材名称',
  `game_id` int(11) NOT NULL DEFAULT '0' COMMENT '游戏ID',
  `media_id` int(11) NOT NULL DEFAULT '0' COMMENT '媒体ID',
  `position_id` int(11) NOT NULL DEFAULT '0' COMMENT '广告位',
  `tags` varchar(500) NOT NULL DEFAULT '' COMMENT '标签 标签1#标签2',
  `type` tinyint(4) NOT NULL DEFAULT '0' COMMENT '0 图片 1视频',
  `remark` varchar(500) NOT NULL COMMENT '备注',
  `add_time` int(11) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `last_edit_time` int(11) NOT NULL DEFAULT '0' COMMENT '最后更新时间',
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`material_id`)
) ENGINE=MyISAM AUTO_INCREMENT=111 DEFAULT CHARSET=utf8 COMMENT='素材';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ww_material_annex`
--

DROP TABLE IF EXISTS `ww_material_annex`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ww_material_annex` (
  `annex_id` int(11) NOT NULL AUTO_INCREMENT,
  `material_id` int(11) NOT NULL DEFAULT '0' COMMENT '素材ID',
  `annex_type` tinyint(4) NOT NULL DEFAULT '0' COMMENT '附件类型 0 图片 1视频 2压缩包 3视频预览',
  `file_type` varchar(20) NOT NULL DEFAULT '' COMMENT '文件类型',
  `file_size` int(11) NOT NULL DEFAULT '0' COMMENT '文件大小',
  `file_name` varchar(100) NOT NULL DEFAULT '' COMMENT '文件名称',
  `file_width` double NOT NULL,
  `file_height` double NOT NULL,
  `file_path` varchar(255) NOT NULL DEFAULT '' COMMENT '文件路径',
  `file_url` varchar(255) NOT NULL DEFAULT '' COMMENT '文件URL',
  `video_time` int(11) NOT NULL DEFAULT '0' COMMENT '视频时长',
  `add_time` int(11) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`annex_id`),
  KEY `annex_type` (`annex_type`),
  KEY `material_id` (`material_id`),
  KEY `annex_type_with_material_id` (`annex_type`,`material_id`)
) ENGINE=MyISAM AUTO_INCREMENT=537 DEFAULT CHARSET=utf8 COMMENT='素材附件';
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
  `manage_url` varchar(255) DEFAULT NULL COMMENT '媒体管理后台地址',
  `settlement` varchar(100) DEFAULT NULL COMMENT '结算方式 多选',
  `remark` varchar(500) NOT NULL DEFAULT '' COMMENT '备注',
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
  `promote_type` tinyint(4) NOT NULL DEFAULT '1' COMMENT '1:普通渠道 2:公会',
  `status` tinyint(4) DEFAULT NULL,
  `rules` varchar(255) NOT NULL DEFAULT '',
  `add_time` int(11) DEFAULT NULL,
  `edit_time` int(11) DEFAULT NULL,
  `promote_money` decimal(10,2) NOT NULL DEFAULT '0.00',
  `ad_account` varchar(255) NOT NULL DEFAULT '',
  `show_sales` tinyint(4) NOT NULL DEFAULT '0',
  `access_token` varchar(32) NOT NULL DEFAULT '',
  `ad_url_id` int(11) NOT NULL DEFAULT '0' COMMENT '广告域名',
  `ad_site_id` int(11) NOT NULL DEFAULT '0' COMMENT '落地页域名',
  `company_name` varchar(100) NOT NULL DEFAULT '' COMMENT '公司名称(代理商）',
  `company_addr` varchar(255) NOT NULL DEFAULT '' COMMENT '公司地址',
  `company_tel` varchar(45) NOT NULL DEFAULT '' COMMENT '公司电话',
  `manage_url` varchar(255) DEFAULT '' COMMENT '后台地址',
  `promote_key` varchar(50) DEFAULT NULL,
  `update_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `distribute` varchar(45) NOT NULL DEFAULT 'Normal' COMMENT '账号所属分发点，Normal:普通广告，Toutiao:头条广告',
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
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='代理商，媒体关联表';
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
  `site_name` varchar(255) NOT NULL DEFAULT '' COMMENT '域名名称',
  `site_url` varchar(255) NOT NULL COMMENT '域名',
  `icp` varchar(255) NOT NULL DEFAULT '' COMMENT 'ICP',
  PRIMARY KEY (`site_id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COMMENT='落地页域名';
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
