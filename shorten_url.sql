# Host: 192.168.48.105  (Version 5.0.95)
# Date: 2016-10-03 11:38:39
# Generator: MySQL-Front 5.3  (Build 7.6)

/*!40101 SET NAMES latin1 */;

#
# Structure for table "url_hits"
#

CREATE TABLE `url_hits` (
  `id` int(11) NOT NULL auto_increment,
  `url_id` int(11) default NULL,
  `target` enum('DESKTOP','PHONE','TABLET') default NULL,
  `hit_date` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

#
# Data for table "url_hits"
#

INSERT INTO `url_hits` VALUES (1,6,'DESKTOP','2016-10-01 04:34:24'),(2,8,'DESKTOP','2016-10-01 04:39:20'),(3,8,'PHONE','2016-10-01 04:39:44'),(4,8,'PHONE','2016-10-01 04:39:48'),(5,8,'DESKTOP','2016-10-01 04:40:39'),(6,8,'DESKTOP','2016-10-01 04:40:58'),(7,8,'DESKTOP','2016-10-01 04:47:44'),(8,8,'DESKTOP','2016-10-01 04:47:54'),(9,8,'DESKTOP','2016-10-01 04:47:56'),(10,8,'DESKTOP','2016-10-01 04:48:11'),(11,8,'TABLET','2016-10-01 04:48:15'),(12,8,'DESKTOP','2016-10-01 04:48:19'),(13,8,'DESKTOP','2016-10-01 04:48:59'),(14,9,'DESKTOP','2016-10-01 04:50:08'),(15,10,'PHONE','2016-10-01 04:51:20'),(16,10,'PHONE','2016-10-01 04:52:10'),(17,10,'PHONE','2016-10-01 04:52:17'),(18,10,'TABLET','2016-10-01 04:52:21'),(19,10,'PHONE','2016-10-01 04:52:29'),(20,10,'PHONE','2016-10-01 04:52:38'),(21,10,'PHONE','2016-10-01 04:52:43'),(22,9,'TABLET','2016-10-01 05:55:01'),(23,9,'DESKTOP','2016-10-01 05:55:14'),(24,12,'DESKTOP','2016-10-01 06:39:22');

#
# Structure for table "urls"
#

CREATE TABLE `urls` (
  `id` int(11) NOT NULL auto_increment,
  `desktop_url` varchar(2083) default NULL COMMENT 'Url for desktop browsers',
  `phone_url` varchar(2083) default NULL COMMENT 'Url for mobile browsers',
  `tablet_url` varchar(2083) default NULL COMMENT 'Url for tablets',
  `creation_date` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;

#
# Data for table "urls"
#

INSERT INTO `urls` VALUES (8,'http://www.google.com','http://url3.com/phone','http://www.google.com/tablet','2016-10-01 04:38:50'),(9,'http://www.google.com','http://url3.com/phone','http://www.google.com/tablet','2016-10-01 04:50:03'),(10,'http://url2.com','http://url3.com/phone','http://url4.com/tablet','2016-10-01 04:51:13'),(11,'http://url.com','http://phoneUrl.com','http://tabletUrl.com','2016-10-01 06:35:25'),(12,'http://url.com','http://phoneUrl.com','http://tabletUrl.com','2016-10-01 06:39:12'),(13,'http://url.com','http://phoneUrl.com','http://tabletUrl.com','2016-10-01 06:47:01'),(15,'http://urlUpdated.com','http://phoneUrlUpdated.com','http://tabletUrlUpdated.com','2016-10-01 06:50:26'),(16,'http://urlUpdated.com','http://phoneUrlUpdated.com','http://tabletUrlUpdated.com','2016-10-01 06:52:29'),(23,'http://www.google.com','http://www.google.com/phone','http://www.google.com/tablet','2016-10-03 04:19:13');
