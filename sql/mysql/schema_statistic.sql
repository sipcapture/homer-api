
SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO,ALLOW_INVALID_DATES";

--
-- Database: `homer_statistic`
--

-- --------------------------------------------------------

--
-- Table structure for table `alarm_config`
--

CREATE TABLE IF NOT EXISTS `alarm_config` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(200) NOT NULL DEFAULT '',
  `startdate` datetime NOT NULL,
  `stopdate` datetime NOT NULL,
  `type` varchar(50) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `value` int(5) NOT NULL DEFAULT 0,
  `notify` tinyint(1) NOT NULL DEFAULT '1',
  `email` varchar(200) NOT NULL DEFAULT '',
  `createdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `active` int(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `type` (`type`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1  ;

-- --------------------------------------------------------

--
-- Table structure for table `alarm_data`
--

CREATE TABLE IF NOT EXISTS `alarm_data` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `type` varchar(50) NOT NULL DEFAULT '',
  `total` int(20) NOT NULL DEFAULT 0,
  `source_ip` varchar(150) NOT NULL DEFAULT '0.0.0.0',
  `description` varchar(256) NOT NULL DEFAULT '',
  `status` int(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`,`create_date`),
  KEY `to_date` (`create_date`),
  KEY `method` (`type`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ROW_FORMAT=COMPRESSED KEY_BLOCK_SIZE=8
/*!50100 PARTITION BY RANGE ( UNIX_TIMESTAMP(`create_date`))
(PARTITION pmax VALUES LESS THAN MAXVALUE ENGINE = InnoDB) */;

-- --------------------------------------------------------

--
-- Table structure for table `alarm_data_mem`
--

CREATE TABLE IF NOT EXISTS `alarm_data_mem` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `type` varchar(50) NOT NULL DEFAULT '',
  `total` int(20) NOT NULL DEFAULT 0,
  `source_ip` varchar(150) NOT NULL DEFAULT '0.0.0.0',
  `description` varchar(256) NOT NULL DEFAULT '',
  `status` int(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `type` (`type`,`source_ip`),
  KEY `to_date` (`create_date`),
  KEY `method` (`type`)
) ENGINE=MEMORY DEFAULT CHARSET=latin1  ;

-- --------------------------------------------------------

--
-- Table structure for table `stats_data`
--

CREATE TABLE IF NOT EXISTS `stats_data` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `from_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `to_date` timestamp NOT NULL DEFAULT '1971-01-01 00:00:01',
  `type` varchar(50) NOT NULL DEFAULT '',
  `total` int(20) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`,`from_date`),
  UNIQUE KEY `datemethod` (`from_date`,`to_date`,`type`),
  KEY `from_date` (`from_date`),
  KEY `to_date` (`to_date`),
  KEY `method` (`type`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ROW_FORMAT=COMPRESSED KEY_BLOCK_SIZE=8
/*!50100 PARTITION BY RANGE ( UNIX_TIMESTAMP(`from_date`))
(PARTITION pmax VALUES LESS THAN MAXVALUE ENGINE = InnoDB) */ ;

-- --------------------------------------------------------

--
-- Table structure for table `stats_ip`
--

CREATE TABLE IF NOT EXISTS `stats_ip` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `from_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `to_date` timestamp NOT NULL DEFAULT '1971-01-01 00:00:01',
  `method` varchar(50) NOT NULL DEFAULT '',
  `source_ip` varchar(255) NOT NULL DEFAULT '0.0.0.0',
  `total` int(20) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`,`from_date`),
  UNIQUE KEY `datemethod` (`from_date`,`to_date`,`method`,`source_ip`),
  KEY `from_date` (`from_date`),
  KEY `to_date` (`to_date`),
  KEY `method` (`method`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ROW_FORMAT=COMPRESSED KEY_BLOCK_SIZE=8
/*!50100 PARTITION BY RANGE ( UNIX_TIMESTAMP(`from_date`))
(PARTITION pmax VALUES LESS THAN MAXVALUE ENGINE = InnoDB) */ ;

-- --------------------------------------------------------

--
-- Table structure for table `stats_ip_mem`
--

CREATE TABLE IF NOT EXISTS `stats_ip_mem` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `method` varchar(50) NOT NULL DEFAULT '',
  `source_ip` varchar(255) NOT NULL DEFAULT '0.0.0.0',
  `total` int(20) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `datemethod` (`method`,`source_ip`)
) ENGINE=MEMORY  DEFAULT CHARSET=latin1;


-- --------------------------------------------------------

--
-- Table structure for table `stats_geo_mem`
--

CREATE TABLE IF NOT EXISTS `stats_geo_mem` (
`id` bigint(20) unsigned NOT NULL AUTO_INCREMENT ,
  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `method` varchar(50) NOT NULL DEFAULT '',
  `country` varchar(255) NOT NULL DEFAULT 'UN',
  `lat` float NOT NULL DEFAULT '0',
  `lon` float NOT NULL DEFAULT '0',
  `total` int(20) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `datemethod` (`method`,`country`)
) ENGINE=MEMORY DEFAULT CHARSET=latin1;


-- --------------------------------------------------------

--
-- Table structure for table `stats_geo`
--

CREATE TABLE IF NOT EXISTS `stats_geo` (
`id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `from_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `to_date` timestamp NOT NULL DEFAULT '1971-01-01 00:00:01',
  `method` varchar(50) NOT NULL DEFAULT '',
  `country` varchar(255) NOT NULL DEFAULT 'UN',
  `lat` float NOT NULL DEFAULT '0',
  `lon` float NOT NULL DEFAULT '0',
  `total` int(20) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`,`from_date`),
  UNIQUE KEY `datemethod` (`from_date`,`to_date`,`method`,`country`),
  KEY `from_date` (`from_date`),
  KEY `to_date` (`to_date`),
  KEY `method` (`method`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ROW_FORMAT=COMPRESSED KEY_BLOCK_SIZE=8
/*!50100 PARTITION BY RANGE ( UNIX_TIMESTAMP(`from_date`))
(PARTITION pmax VALUES LESS THAN MAXVALUE ENGINE = InnoDB) */ ;

-- --------------------------------------------------------

--
-- Table structure for table `stats_dest_mem`
--

CREATE TABLE IF NOT EXISTS `stats_dest_mem` (
`id` bigint(20) unsigned NOT NULL AUTO_INCREMENT ,
  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `prefix` varchar(50) NOT NULL,
  `method` varchar(50) NOT NULL DEFAULT '',
  `status_code` varchar(3) NOT NULL DEFAULT '',
  `reason_phrase` varchar(100) NOT NULL DEFAULT '',
  `country` varchar(255) NOT NULL DEFAULT 'UN',
  `lat` float NOT NULL DEFAULT '0',
  `lon` float NOT NULL DEFAULT '0',
  `duration` int,
  `total` int(20) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `datemethod` (`country` ,`prefix`, `method`, `status_code`)
) ENGINE=MEMORY DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `stats_dest_reply`
--

CREATE TABLE IF NOT EXISTS `stats_dest_reply` (
`id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `from_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `to_date` timestamp NOT NULL DEFAULT '1971-01-01 00:00:01',
  `prefix` varchar(50) NOT NULL,
  `method` varchar(50) NOT NULL DEFAULT '',
  `status_code` varchar(3) NOT NULL DEFAULT '',
  `reason_phrase` varchar(100) NOT NULL DEFAULT '',
  `country` varchar(255) NOT NULL DEFAULT 'UN',
  `lat` float NOT NULL DEFAULT '0',
  `lon` float NOT NULL DEFAULT '0',
  `total` int(20) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`,`from_date`),
  UNIQUE KEY `datemethod` (`from_date`,`to_date`, `country`, `prefix`, `method`, `status_code`),
  KEY `from_date` (`from_date`),
  KEY `to_date` (`to_date`),
  KEY `prefix` (`prefix`),
  KEY `method` (`method`),
  KEY `status_code` (`status_code`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ROW_FORMAT=COMPRESSED KEY_BLOCK_SIZE=8
/*!50100 PARTITION BY RANGE ( UNIX_TIMESTAMP(`from_date`))
(PARTITION pmax VALUES LESS THAN MAXVALUE ENGINE = InnoDB) */ ;

-- --------------------------------------------------------

--
-- Table structure for table `stats_method`
--

CREATE TABLE IF NOT EXISTS `stats_method` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `from_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `to_date` timestamp NOT NULL DEFAULT '1971-01-01 00:00:01',
  `method` varchar(50) NOT NULL DEFAULT '',
  `auth` tinyint(1) NOT NULL DEFAULT '0',
  `cseq` varchar(100) NOT NULL DEFAULT '',
  `totag` tinyint(1) NOT NULL DEFAULT 0,
  `total` int(20) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`,`from_date`),
  UNIQUE KEY `datemethod` (`from_date`,`to_date`,`method`,`auth`,`totag`,`cseq`),
  KEY `from_date` (`from_date`),
  KEY `to_date` (`to_date`),
  KEY `method` (`method`),
  KEY `completed` (`cseq`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ROW_FORMAT=COMPRESSED KEY_BLOCK_SIZE=8
/*!50100 PARTITION BY RANGE ( UNIX_TIMESTAMP(`from_date`))
(PARTITION pmax VALUES LESS THAN MAXVALUE ENGINE = InnoDB) */ ;

-- --------------------------------------------------------

--
-- Table structure for table `stats_method_mem`
--

CREATE TABLE IF NOT EXISTS `stats_method_mem` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `method` varchar(50) NOT NULL DEFAULT '',
  `auth` tinyint(1) NOT NULL DEFAULT '0',
  `cseq` varchar(100) NOT NULL DEFAULT '',
  `totag` tinyint(1) NOT NULL DEFAULT 0,
  `total` int(20) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `datemethod` (`method`,`auth`,`totag`,`cseq`),
  KEY `from_date` (`create_date`),
  KEY `method` (`method`),
  KEY `completed` (`cseq`)
) ENGINE=MEMORY DEFAULT CHARSET=latin1  ;

-- --------------------------------------------------------

--
-- Table structure for table `stats_useragent`
--

CREATE TABLE IF NOT EXISTS `stats_useragent` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `from_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `to_date` timestamp NOT NULL DEFAULT '1971-01-01 00:00:01',
  `useragent` varchar(100) NOT NULL DEFAULT '',
  `method` varchar(50) NOT NULL DEFAULT '',
  `total` int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`,`from_date`),
  UNIQUE KEY `datemethodua` (`from_date`,`to_date`,`method`,`useragent`),
  KEY `from_date` (`from_date`),
  KEY `to_date` (`to_date`),
  KEY `useragent` (`useragent`),
  KEY `method` (`method`),
  KEY `total` (`total`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ROW_FORMAT=COMPRESSED KEY_BLOCK_SIZE=8
/*!50100 PARTITION BY RANGE ( UNIX_TIMESTAMP(`from_date`))
(PARTITION pmax VALUES LESS THAN MAXVALUE ENGINE = InnoDB) */;

-- --------------------------------------------------------

--
-- Table structure for table `stats_useragent_mem`
--

CREATE TABLE IF NOT EXISTS `stats_useragent_mem` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `useragent` varchar(100) NOT NULL DEFAULT '',
  `method` varchar(50) NOT NULL DEFAULT '',
  `total` int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `useragent` (`useragent`,`method`)
) ENGINE=MEMORY  DEFAULT CHARSET=latin1  ;


CREATE TABLE IF NOT EXISTS `stats_generic` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `from_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `to_date` timestamp NOT NULL DEFAULT '1971-01-01 00:00:01',
  `type` varchar(50) NOT NULL DEFAULT '',
  `tag` varchar(50) NOT NULL DEFAULT '',
  `total` int(20) NOT NULL,
  PRIMARY KEY (`id`,`from_date`),
  UNIQUE KEY `datemethod` (`from_date`,`to_date`,`type`,`tag`),
  KEY `from_date` (`from_date`),
  KEY `to_date` (`to_date`),
  KEY `method` (`type`,`tag`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPRESSED KEY_BLOCK_SIZE=8
/*!50100 PARTITION BY RANGE ( UNIX_TIMESTAMP(`from_date`))
(PARTITION pmax VALUES LESS THAN MAXVALUE ENGINE = InnoDB) */;

--
-- Table structure for table `stats_asracd_mem`
--

CREATE TABLE `stats_asracd_mem` (
      `datetime_create` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
      `method` varchar(16) NOT NULL DEFAULT '',
      `from_tag` varchar(64) NOT NULL DEFAULT '',
      `to_tag` varchar(64) NOT NULL DEFAULT '',
      `callid` varchar(255) NOT NULL DEFAULT '',
      `sip_code` varchar(3) NOT NULL DEFAULT '',
      `sip_reason` varchar(128) NOT NULL DEFAULT '',
      `datetime_answer` datetime DEFAULT '0000-00-00 00:00:00',
      `src_user` varchar(50) DEFAULT NULL,
      `src_ip` varchar(50) DEFAULT NULL,
      `dst_user` varchar(50) DEFAULT NULL,
      `dst_ip` varchar(50) DEFAULT NULL,
      `bye` datetime NOT NULL,
      `datetime_end` timestamp NULL DEFAULT '0000-00-00 00:00:00',
      UNIQUE KEY `callid` (`callid`),
      KEY `callid_idx` (`callid`)

) ENGINE=MEMORY DEFAULT CHARSET=latin1;


--
-- Table structure for table `stats_asracd`
--

DROP TABLE IF EXISTS `stats_asracd`;
CREATE TABLE `stats_asracd` (
      `from_date` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
      `to_date` timestamp NULL DEFAULT NULL,
      `src_ip` varchar(50) DEFAULT NULL,
      `dst_ip` varchar(50) DEFAULT NULL,
      `answered` decimal(23,0) DEFAULT NULL,
      `total` bigint(21) NOT NULL DEFAULT '0',
      `asr` decimal(30,4) DEFAULT NULL,
      `acd_minutes` decimal(35,4) DEFAULT NULL,
      `total_minutes` decimal(30,2) NOT NULL,
      KEY `by_date` (`from_date`,`src_ip`,`dst_ip`)

) ENGINE=InnoDB DEFAULT CHARSET=latin1
/*!50100 PARTITION BY RANGE ( UNIX_TIMESTAMP(`from_date`) )
(PARTITION pmax VALUES LESS THAN MAXVALUE ENGINE = InnoDB) */;

