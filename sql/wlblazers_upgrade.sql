-- ------------------------------------------------------------------------------------
-- ---------------------------- Table structure modify
-- ------------------------------------------------------------------------------------

/******************************* update Date: 2019-11-04  *******************************/
alter table db_cfg_oracle_dg add(`primary_db_dest` tinyint(2));
alter table db_cfg_oracle_dg add(`standby_db_dest` tinyint(2));



/******************************* update Date: 2019-11-14  *******************************/

alter table oracle_tablespace modify (max_rate float(10,2));
alter table oracle_tablespace_his modify (max_rate float(10,2));

-- ----------------------------
-- Table structure for sqlserver_space
-- ----------------------------
DROP TABLE IF EXISTS `sqlserver_space`;
CREATE TABLE `sqlserver_space` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `server_id` smallint(4) NOT NULL DEFAULT '0',
  `host` varchar(50) NOT NULL DEFAULT '0',
  `port` varchar(30) NOT NULL DEFAULT '0',
  `tags` varchar(50) NOT NULL DEFAULT '',
  `db_name` varchar(100) NOT NULL,
  `status` varchar(20) NOT NULL,
  `total_size` bigint(18) NOT NULL DEFAULT '0',
  `used_size` bigint(18) NOT NULL DEFAULT '0',
  `max_rate` float(10,2) NOT NULL DEFAULT '0',
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_server_id` (`server_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `sqlserver_session`;
CREATE TABLE `sqlserver_session` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `server_id` int(10) NOT NULL,
  `snap_id` bigint(20) DEFAULT NULL,
  `end_time` varchar(20) DEFAULT NULL,
  `total_session` bigint(20) DEFAULT NULL,
  `active_session` bigint(20) DEFAULT NULL,
  `create_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10000 DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `sqlserver_hit`;
CREATE TABLE `sqlserver_hit` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `server_id` int(10) NOT NULL,
  `snap_id` bigint(20) DEFAULT NULL,
  `end_time` varchar(20) DEFAULT NULL,
  `type` varchar(50) DEFAULT NULL,
  `rate` float(10,2) DEFAULT NULL,
  `create_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10000 DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `sqlserver_log`;
CREATE TABLE `sqlserver_log` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `server_id` int(10) NOT NULL,
  `snap_id` bigint(20) DEFAULT NULL,
  `end_time` varchar(20) DEFAULT NULL,
  `cntr_value` bigint(20) DEFAULT NULL,
  `incr_value` bigint(20) DEFAULT NULL,
  `create_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10000 DEFAULT CHARSET=utf8;


/******************************* update Date: 2019-11-17 *******************************/
alter table db_cfg_oracle_dg rename column network_card to network_card_s;
alter table db_cfg_oracle_dg add(network_card_p varchar(100));





-- ------------------------------------------------------------------------------------
-- ---------------------------- Init data modify
-- ------------------------------------------------------------------------------------