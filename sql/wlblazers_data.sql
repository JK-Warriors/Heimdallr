/*
Navicat MySQL Data Transfer

Source Server         : westserver_new
Source Server Version : 50536
Source Host           : localhost
Source Database       : wlblazers

Target Server Type    : MYSQL
Target Server Version : 50536
File Encoding         : 65001

Date: 2016-05-01 09:55:52
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for admin_menu
-- ----------------------------
DROP TABLE IF EXISTS `admin_menu`;
CREATE TABLE `admin_menu` (
  `menu_id` smallint(4) NOT NULL AUTO_INCREMENT,
  `menu_title` varchar(30) NOT NULL,
  `menu_level` tinyint(2) NOT NULL DEFAULT '0',
  `parent_id` tinyint(2) NOT NULL,
  `menu_url` varchar(255) DEFAULT NULL,
  `menu_icon` varchar(50) DEFAULT NULL,
  `system` tinyint(2) NOT NULL DEFAULT '0',
  `status` tinyint(2) NOT NULL DEFAULT '1',
  `display_order` smallint(4) NOT NULL DEFAULT '0',
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`menu_id`)
) ENGINE=InnoDB AUTO_INCREMENT=62 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of admin_menu
-- ----------------------------
INSERT INTO `admin_menu` VALUES ('10', 'Servers Configure', '1', '0', 'server', 'iconfont icon-icon-test', '0', '1', '2', CURRENT_TIMESTAMP);
INSERT INTO `admin_menu` VALUES ('11', 'MySQL Monitor', '1', '0', 'wl_mysql', 'iconfont icon-shujuku', '0', '1', '4', CURRENT_TIMESTAMP);
INSERT INTO `admin_menu` VALUES ('12', 'Oracle Monitor', '1', '0', 'wl_oracle', 'iconfont icon-shujuku', '0', '1', '3', CURRENT_TIMESTAMP);
INSERT INTO `admin_menu` VALUES ('13', 'SQLServer Monitor', '1', '0', 'wl_sqlserver', 'iconfont icon-shujuku', '0', '1', '5', CURRENT_TIMESTAMP);
-- INSERT INTO `admin_menu` VALUES ('14', 'MongoDB Monitor', '1', '0', 'wl_mongodb', 'icon-dashboard', '0', '1', '6', CURRENT_TIMESTAMP);
-- INSERT INTO `admin_menu` VALUES ('15', 'Redis Monitor', '1', '0', 'wl_redis', 'icon-dashboard', '0', '1', '7', CURRENT_TIMESTAMP);
INSERT INTO `admin_menu` VALUES ('20', 'OS Monitor', '1', '0', 'wl_os', 'iconfont icon-zhuji', '0', '1', '8', CURRENT_TIMESTAMP);
INSERT INTO `admin_menu` VALUES ('21', 'Alarm Panel', '1', '0', 'alarm', 'iconfont icon-jinggao', '0', '1', '9', CURRENT_TIMESTAMP);
INSERT INTO `admin_menu` VALUES ('22', 'Permission System', '1', '0', 'rabc', 'iconfont icon-icon-quanxianxg', '0', '1', '10', CURRENT_TIMESTAMP);

-- ------------------Configure--------------------
INSERT INTO `admin_menu` VALUES ('1001', 'Settings', '2', '10', 'settings/index', 'icon-list', '0', '1', '1', CURRENT_TIMESTAMP);
INSERT INTO `admin_menu` VALUES ('1002', 'MySQL', '2', '10', 'cfg_mysql/index', 'icon-list', '0', '1', '3', CURRENT_TIMESTAMP);
INSERT INTO `admin_menu` VALUES ('1003', 'Oracle', '2', '10', 'cfg_oracle/index', 'icon-list', '0', '1', '2', CURRENT_TIMESTAMP);
INSERT INTO `admin_menu` VALUES ('1004', 'SQLServer', '2', '10', 'cfg_sqlserver/index', 'icon-list', '0', '1', '4', CURRENT_TIMESTAMP);
-- INSERT INTO `admin_menu` VALUES ('1005', 'MongoDB', '2', '10', 'cfg_mongodb/index', 'icon-list', '0', '1', '5', CURRENT_TIMESTAMP);
-- INSERT INTO `admin_menu` VALUES ('1006', 'Redis', '2', '10', 'cfg_redis/index', 'icon-list', '0', '1', '6', CURRENT_TIMESTAMP);
INSERT INTO `admin_menu` VALUES ('1010', 'OS', '2', '10', 'cfg_os/index', 'icon-list', '0', '1', '10', CURRENT_TIMESTAMP);

-- ------------------MySQL Monitor--------------------
INSERT INTO `admin_menu` VALUES ('1101', 'Health Monitor', '2', '11', 'wl_mysql/index', ' icon-list', '0', '1', '1', CURRENT_TIMESTAMP);
INSERT INTO `admin_menu` VALUES ('1102', 'Resource Monitor', '2', '11', 'wl_mysql/resource', 'icon-list', '0', '1', '2', CURRENT_TIMESTAMP);
INSERT INTO `admin_menu` VALUES ('1103', 'Key Cache Monitor', '2', '11', 'wl_mysql/key_cache', 'icon-list', '0', '1', '3', CURRENT_TIMESTAMP);
INSERT INTO `admin_menu` VALUES ('1104', 'InnoDB Monitor', '2', '11', 'wl_mysql/innodb', 'icon-list', '0', '1', '4', CURRENT_TIMESTAMP);
INSERT INTO `admin_menu` VALUES ('1105', 'Replication Monitor', '2', '11', 'wl_mysql/replication', ' icon-list', '0', '1', '5', CURRENT_TIMESTAMP);
INSERT INTO `admin_menu` VALUES ('1106', 'BigTable Analysis', '2', '11', 'wl_mysql/bigtable', 'icon-list', '0', '1', '6', CURRENT_TIMESTAMP);
INSERT INTO `admin_menu` VALUES ('1107', 'Slowquery Analysis', '2', '11', 'wl_mysql/slowquery', 'icon-list', '0', '1', '7', CURRENT_TIMESTAMP);
INSERT INTO `admin_menu` VALUES ('1108', 'AWR Report', '2', '11', 'wl_mysql/awrreport', 'icon-list', '0', '1', '8', CURRENT_TIMESTAMP);

-- ------------------Oracle Monitor--------------------
INSERT INTO `admin_menu` VALUES ('1201', 'Health Montior', '2', '12', 'wl_oracle/index', 'icon-list', '0', '1', '1', CURRENT_TIMESTAMP);
INSERT INTO `admin_menu` VALUES ('1202', 'Tablespace Monitor', '2', '12', 'wl_oracle/tablespace', 'icon-list', '0', '1', '2', CURRENT_TIMESTAMP);
INSERT INTO `admin_menu` VALUES ('1203', 'DiskGroup Monitor', '2', '12', 'wl_oracle/diskgroup', 'icon-list', '0', '1', '3', CURRENT_TIMESTAMP);
INSERT INTO `admin_menu` VALUES ('1204', 'DataGuard Monitor', '2', '12', 'wl_oracle/dglist', 'icon-list', '0', '1', '4', CURRENT_TIMESTAMP);
INSERT INTO `admin_menu` VALUES ('1205', 'Flashback', '2', '12', 'wl_oracle/flashback', 'icon-list', '0', '1', '5', CURRENT_TIMESTAMP);

-- ------------------SQLServer Monitor--------------------
INSERT INTO `admin_menu` VALUES ('1301', 'Health Monitor', '2', '13', 'wl_sqlserver/index', 'icon-list', '0', '1', '1', CURRENT_TIMESTAMP);


-- ------------------MongoDB Monitor--------------------
-- INSERT INTO `admin_menu` VALUES ('1401', 'Health Montior', '2', '14', 'wl_mongodb/index', 'icon-list', '0', '1', '1', CURRENT_TIMESTAMP);
-- INSERT INTO `admin_menu` VALUES ('1402', 'Indexes Monitor', '2', '14', 'wl_mongodb/indexes', 'icon-list', '0', '0', '2', CURRENT_TIMESTAMP);
-- INSERT INTO `admin_menu` VALUES ('1403', 'Memory Monitor', '2', '14', 'wl_mongodb/memory', 'icon-list', '0', '1', '3', CURRENT_TIMESTAMP);

-- ------------------Redis Monitor--------------------
-- INSERT INTO `admin_menu` VALUES ('1501', 'Health Monitor', '2', '15', 'wl_redis/index', 'icon-list', '0', '1', '1', CURRENT_TIMESTAMP);
-- INSERT INTO `admin_menu` VALUES ('1502', 'Memory Monitor', '2', '15', 'wl_redis/memory', 'icon-list', '0', '1', '2', CURRENT_TIMESTAMP);
-- INSERT INTO `admin_menu` VALUES ('1503', 'Replication Monitor', '2', '15', 'wl_redis/replication', 'icon-list', '0', '0', '3', CURRENT_TIMESTAMP);


-- ------------------OS Monitor--------------------
INSERT INTO `admin_menu` VALUES ('2001', 'Health Monitor', '2', '20', 'wl_os/index', 'icon-list', '0', '1', '1', CURRENT_TIMESTAMP);
INSERT INTO `admin_menu` VALUES ('2002', 'Disk', '2', '20', 'wl_os/disk', 'icon-list', '0', '1', '2', CURRENT_TIMESTAMP);
INSERT INTO `admin_menu` VALUES ('2003', 'Disk IO', '2', '20', 'wl_os/disk_io', 'icon-list', '0', '1', '3', CURRENT_TIMESTAMP);


-- ------------------Alarm Panel--------------------
INSERT INTO `admin_menu` VALUES ('2101', 'Alarm List', '2', '21', 'alarm/index', '', '0', '1', '0', CURRENT_TIMESTAMP);


-- ------------------Permission System--------------------
INSERT INTO `admin_menu` VALUES ('2201', 'User', '2', '22', 'user/index', '', '0', '1', '1', CURRENT_TIMESTAMP);
INSERT INTO `admin_menu` VALUES ('2202', 'Role', '2', '22', 'role/index', '', '0', '1', '2', CURRENT_TIMESTAMP);
INSERT INTO `admin_menu` VALUES ('2203', 'Menu', '2', '22', 'menu/index', '', '0', '1', '3', CURRENT_TIMESTAMP);
INSERT INTO `admin_menu` VALUES ('2204', 'Privilege', '2', '22', 'privilege/index', '', '0', '1', '4', CURRENT_TIMESTAMP);
INSERT INTO `admin_menu` VALUES ('2205', 'Authorization', '2', '22', 'auth/index', '', '0', '1', '5', CURRENT_TIMESTAMP);



-- ----------------------------
-- Table structure for admin_privilege
-- ----------------------------
DROP TABLE IF EXISTS `admin_privilege`;
CREATE TABLE `admin_privilege` (
  `privilege_id` smallint(4) NOT NULL AUTO_INCREMENT,
  `privilege_title` varchar(30) DEFAULT NULL,
  `menu_id` smallint(4) DEFAULT NULL,
  `action` varchar(100) DEFAULT NULL,
  `display_order` smallint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`privilege_id`)
) ENGINE=InnoDB AUTO_INCREMENT=125 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of admin_privilege
-- ----------------------------

INSERT INTO `admin_privilege` VALUES ('1011', 'Admin User View', '2201', 'user/index', '11');
INSERT INTO `admin_privilege` VALUES ('1012', 'Admin User Add ', '2201', 'user/add', '12');
INSERT INTO `admin_privilege` VALUES ('1013', 'Admin User Edit', '2201', 'user/edit', '13');
INSERT INTO `admin_privilege` VALUES ('1014', 'Admin User Delete', '2201', 'user/forever_delete', '14');

INSERT INTO `admin_privilege` VALUES ('1021', 'Admin Role View', '2202', 'role/index', '21');
INSERT INTO `admin_privilege` VALUES ('1022', 'Admin Role Add', '2202', 'role/add', '22');
INSERT INTO `admin_privilege` VALUES ('1023', 'Admin Role Edit', '2202', 'role/edit', '23');
INSERT INTO `admin_privilege` VALUES ('1024', 'Admin Role Delete', '2202', 'role/forever_delete', '24');

INSERT INTO `admin_privilege` VALUES ('1031', 'Admin Menu View', '2203', 'menu/index', '31');
INSERT INTO `admin_privilege` VALUES ('1032', 'Admin Menu Add', '2203', 'menu/add', '32');
INSERT INTO `admin_privilege` VALUES ('1033', 'Admin Menu Edit', '2203', 'menu/edit', '33');
INSERT INTO `admin_privilege` VALUES ('1034', 'Admin Menu Delete', '2203', 'menu/forever_delete', '34');

INSERT INTO `admin_privilege` VALUES ('1041', 'Admin Privilege View', '2204', 'privilege/index', '41');
INSERT INTO `admin_privilege` VALUES ('1042', 'Admin Privilege Add', '2204', 'privilege/add', '42');
INSERT INTO `admin_privilege` VALUES ('1043', 'Admin Privilege Edit', '2204', 'privilege/edit', '43');
INSERT INTO `admin_privilege` VALUES ('1044', 'Admin Privilege Delete', '2204', 'privilege/forever_delete', '44');

INSERT INTO `admin_privilege` VALUES ('1051', 'Admin Auth View', '2205', 'auth/index', '51');
INSERT INTO `admin_privilege` VALUES ('1052', 'Admin Role Privilege Update', '2205', 'auth/update_role_privilege', '52');
INSERT INTO `admin_privilege` VALUES ('1053', 'Admin User Role Update', '2205', 'auth/update_user_role', '53');

INSERT INTO `admin_privilege` VALUES ('1061', 'Settings View', '1001', 'settings/index', '61');
INSERT INTO `admin_privilege` VALUES ('1062', 'Settings Save', '1001', 'settings/save', '62');

INSERT INTO `admin_privilege` VALUES ('1071', 'Login System', '0', 'index/index', '71');

INSERT INTO `admin_privilege` VALUES ('1081', 'Alarm View', '2101', 'alarm/index', '81');

-- -------------------------------------------------- MySQL -------------------------------------------------------
INSERT INTO `admin_privilege` VALUES ('1101', 'MySQL Config View', '1002', 'cfg_mysql/index', '101');
INSERT INTO `admin_privilege` VALUES ('1102', 'MySQL Config Add', '1002', 'cfg_mysql/add', '102');
INSERT INTO `admin_privilege` VALUES ('1103', 'MySQL Config Edit', '1002', 'cfg_mysql/edit', '103');
INSERT INTO `admin_privilege` VALUES ('1104', 'MySQL Config Trash', '1002', 'cfg_mysql/trash', '104');
INSERT INTO `admin_privilege` VALUES ('1105', 'MySQL Config Delete', '1002', 'cfg_mysql/delete', '105');
INSERT INTO `admin_privilege` VALUES ('1106', 'MySQL Config Batch Add', '1002', 'cfg_mysql/batch_add', '106');

INSERT INTO `admin_privilege` VALUES ('1151', 'MySQL Health Monitor', '1101', 'wl_mysql/index', '151');
INSERT INTO `admin_privilege` VALUES ('1152', 'MySQL Health Chart', '1101', 'wl_mysql/chart', '152');
INSERT INTO `admin_privilege` VALUES ('1153', 'MySQL Resource Monitor', '1102', 'wl_mysql/resource', '153');
INSERT INTO `admin_privilege` VALUES ('1154', 'MySQL Key Cache Monitor', '1103', 'wl_mysql/key_cache', '154');
INSERT INTO `admin_privilege` VALUES ('1155', 'MySQL InnoDB Monitor', '1104', 'wl_mysql/innodb', '155');
INSERT INTO `admin_privilege` VALUES ('1156', 'MySQL Replication Monitor', '1105', 'wl_mysql/replication', '156');
INSERT INTO `admin_privilege` VALUES ('1157', 'MySQL Replication Chart', '1105', 'wl_mysql/replication_chart', '157');
INSERT INTO `admin_privilege` VALUES ('1158', 'MySQL BigTable Analysis', '1106', 'wl_mysql/bigtable', '158');
INSERT INTO `admin_privilege` VALUES ('1159', 'MySQL BigTable Analysis Chart', '1106', 'wl_mysql/bigtable_chart', '159');
INSERT INTO `admin_privilege` VALUES ('1160', 'MySQLSlowQuery', '1107', 'wl_mysql/slowquery', '160');
INSERT INTO `admin_privilege` VALUES ('1161', 'MySQLSlowQuery Detail', '1107', 'wl_mysql/slowquery_detail', '161');
INSERT INTO `admin_privilege` VALUES ('1162', 'MySQL AWR Report', '1108', 'wl_mysql/awrreport', '162');

-- --------------------------------------------------- Oracle ------------------------------------------------------
INSERT INTO `admin_privilege` VALUES ('1201', 'Oracle Config View', '1003', 'cfg_oracle/index', '201');
INSERT INTO `admin_privilege` VALUES ('1202', 'Oracle Config Add', '1003', 'cfg_oracle/add', '202');
INSERT INTO `admin_privilege` VALUES ('1203', 'Oracle Config Edit', '1003', 'cfg_oracle/edit', '203');
INSERT INTO `admin_privilege` VALUES ('1204', 'Oracle Config Trash', '1003', 'cfg_oracle/trash', '204');
INSERT INTO `admin_privilege` VALUES ('1205', 'Oracle Config Delete', '1003', 'cfg_oracle/delete', '205');
INSERT INTO `admin_privilege` VALUES ('1206', 'Oracle Config Batch Add', '1003', 'cfg_oracle/batch_add', '206');

INSERT INTO `admin_privilege` VALUES ('1251', 'Oracle Health Monitor', '1201', 'wl_oracle/index', '251');
INSERT INTO `admin_privilege` VALUES ('1252', 'Oracle Health Chart', '1201', 'wl_oracle/chart', '252');
INSERT INTO `admin_privilege` VALUES ('1253', 'Oracle Tablespace Monitor', '1202', 'wl_oracle/tablespace', '253');
INSERT INTO `admin_privilege` VALUES ('1254', 'Oracle DiskGroup Monitor', '1203', 'wl_oracle/diskgroup', '254');
INSERT INTO `admin_privilege` VALUES ('1255', 'Oracle DataGuard List', '1204', 'wl_oracle/dglist', '255');
INSERT INTO `admin_privilege` VALUES ('1256', 'Oracle DataGuard Detail', '1204', 'wl_oracle/dataguard', '256');
INSERT INTO `admin_privilege` VALUES ('1257', 'Oracle DataGuard Manage', '1204', 'wl_oracle/dg_switch', '257');
INSERT INTO `admin_privilege` VALUES ('1258', 'Oracle Flashback', '1205', 'wl_oracle/flashback', '258');

-- --------------------------------------------------- OS ------------------------------------------------------
INSERT INTO `admin_privilege` VALUES ('1301', 'OS Config View', '1010', 'cfg_os/index', '301');
INSERT INTO `admin_privilege` VALUES ('1302', 'OS Config Add', '1010', 'cfg_os/add', '302');
INSERT INTO `admin_privilege` VALUES ('1303', 'OS Config Edit', '1010', 'cfg_os/edit', '303');
INSERT INTO `admin_privilege` VALUES ('1304', 'OS Config Delete', '1010', 'cfg_os/delete', '304');
INSERT INTO `admin_privilege` VALUES ('1305', 'OS Config Trash', '1010', 'cfg_os/trash', '305');
INSERT INTO `admin_privilege` VALUES ('1306', 'OS Config Batch Add', '1010', 'cfg_os/batch_add', '306');

INSERT INTO `admin_privilege` VALUES ('1351', 'OS Health View', '2001', 'wl_os/index', '351');
INSERT INTO `admin_privilege` VALUES ('1352', 'OS Health Chart View', '2001', 'wl_os/chart', '352');
INSERT INTO `admin_privilege` VALUES ('1353', 'OS Disk View', '2002', 'wl_os/disk', '353');
INSERT INTO `admin_privilege` VALUES ('1354', 'OS Disk Chart View', '2002', 'wl_os/disk_chart', '354');
INSERT INTO `admin_privilege` VALUES ('1355', 'OS Disk View', '2003', 'wl_os/disk_io', '355');
INSERT INTO `admin_privilege` VALUES ('1356', 'OS Disk Chart View', '2003', 'wl_os/disk_io_chart', '356');


-- --------------------------------------------------- SQLServer ------------------------------------------------------------
INSERT INTO `admin_privilege` VALUES ('1401', 'SQLServer Config View', '1004', 'cfg_sqlserver/index', '401');
INSERT INTO `admin_privilege` VALUES ('1402', 'SQLServer Config Add', '1004', 'cfg_sqlserver/add', '402');
INSERT INTO `admin_privilege` VALUES ('1403', 'SQLServer Config Edit', '1004', 'cfg_sqlserver/edit', '403');
INSERT INTO `admin_privilege` VALUES ('1404', 'SQLServer Config Trash', '1004', 'cfg_sqlserver/trash', '404');
INSERT INTO `admin_privilege` VALUES ('1405', 'SQLServer Config Delete', '1004', 'cfg_sqlserver/delete', '405');
INSERT INTO `admin_privilege` VALUES ('1406', 'SQLServer Config Batch Add', '1004', 'cfg_sqlserver/batch_add', '406');

INSERT INTO `admin_privilege` VALUES ('1451', 'SQLServer Health Monitor', '1301', 'wl_sqlserver/index', '451');
INSERT INTO `admin_privilege` VALUES ('1452', 'SQLServer Health Chart', '1301', 'wl_sqlserver/chart', '452');

-- ---------------------------------------------------- MongoDB -----------------------------------------------------------
-- INSERT INTO `admin_privilege` VALUES ('1501', 'MongoDB Config View', '1005', 'cfg_mongodb/index', '501');
-- INSERT INTO `admin_privilege` VALUES ('1502', 'MongoDB Config Add', '1005', 'cfg_mongodb/add', '502');
-- INSERT INTO `admin_privilege` VALUES ('1503', 'MongoDB Config Edit', '1005', 'cfg_mongodb/edit', '503');
-- INSERT INTO `admin_privilege` VALUES ('1504', 'MongoDB Config Trash', '1005', 'cfg_mongodb/trash', '504');
-- INSERT INTO `admin_privilege` VALUES ('1505', 'MongoDB Config Delete', '1005', 'cfg_mongodb/delete', '505');
-- INSERT INTO `admin_privilege` VALUES ('1506', 'MongoDB Config Batch Add', '1005', 'cfg_mongodb/batch_add', '506');

-- INSERT INTO `admin_privilege` VALUES ('1551', 'MongoDB Health View', '1401', 'wl_mongodb/index', '551');
-- INSERT INTO `admin_privilege` VALUES ('1552', 'Mongodb Health Chart View', '1401', 'wl_mongodb/chart', '552');
-- INSERT INTO `admin_privilege` VALUES ('1553', 'MongoDB Indexes View', '1402', 'wl_mongodb/indexes', '553');
-- INSERT INTO `admin_privilege` VALUES ('1554', 'MongoDB Memory View', '1403', 'wl_mongodb/memory', '554');

-- ----------------------------------------------------- Redis ----------------------------------------------------------
-- INSERT INTO `admin_privilege` VALUES ('1601', 'Redis Config View', '1006', 'cfg_redis/index', '601');
-- INSERT INTO `admin_privilege` VALUES ('1602', 'Redis Config Add', '1006', 'cfg_redis/add', '602');
-- INSERT INTO `admin_privilege` VALUES ('1603', 'Redis Config Edit', '1006', 'cfg_redis/edit', '603');
-- INSERT INTO `admin_privilege` VALUES ('1604', 'Redis Config Trash', '1006', 'cfg_redis/trash', '604');
-- INSERT INTO `admin_privilege` VALUES ('1605', 'Redis Config Delete', '1006', 'cfg_redis/delete', '605');
-- INSERT INTO `admin_privilege` VALUES ('1606', 'Redis Config Batch Add', '1006', 'cfg_redis/batch_add', '606');

-- INSERT INTO `admin_privilege` VALUES ('1651', 'Redis Health View', '1501', 'wl_redis/index', '651');
-- INSERT INTO `admin_privilege` VALUES ('1652', 'Redis Health Chart View', '1501', 'wl_redis/chart', '652');
-- INSERT INTO `admin_privilege` VALUES ('1653', 'Redis Memory View', '1502', 'wl_redis/memory', '653');
-- INSERT INTO `admin_privilege` VALUES ('1654', 'Redis Memory Chart View', '1502', 'wl_redis/memory_chart', '654');
-- INSERT INTO `admin_privilege` VALUES ('1655', 'Redis Replication View', '1503', 'wl_redis/replication', '655');
-- INSERT INTO `admin_privilege` VALUES ('1656', 'Redis Replication Chart View', '1503', 'wl_redis/replication_chart', '656');


-- ----------------------------
-- Table structure for admin_role
-- ----------------------------
DROP TABLE IF EXISTS `admin_role`;
CREATE TABLE `admin_role` (
  `role_id` smallint(6) NOT NULL AUTO_INCREMENT,
  `role_name` varchar(30) NOT NULL DEFAULT '0',
  PRIMARY KEY (`role_id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of admin_role
-- ----------------------------
INSERT INTO `admin_role` VALUES ('1', 'Administrator');
INSERT INTO `admin_role` VALUES ('7', 'guest_group');

-- ----------------------------
-- Table structure for admin_role_privilege
-- ----------------------------
DROP TABLE IF EXISTS `admin_role_privilege`;
CREATE TABLE `admin_role_privilege` (
  `role_id` smallint(4) NOT NULL,
  `privilege_id` smallint(4) NOT NULL,
  PRIMARY KEY (`role_id`,`privilege_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of admin_role_privilege
-- ----------------------------
INSERT INTO `admin_role_privilege` VALUES ('1', '1011');
INSERT INTO `admin_role_privilege` VALUES ('1', '1012');
INSERT INTO `admin_role_privilege` VALUES ('1', '1013');
INSERT INTO `admin_role_privilege` VALUES ('1', '1014');
INSERT INTO `admin_role_privilege` VALUES ('1', '1021');
INSERT INTO `admin_role_privilege` VALUES ('1', '1022');
INSERT INTO `admin_role_privilege` VALUES ('1', '1023');
INSERT INTO `admin_role_privilege` VALUES ('1', '1024');
INSERT INTO `admin_role_privilege` VALUES ('1', '1031');
INSERT INTO `admin_role_privilege` VALUES ('1', '1032');
INSERT INTO `admin_role_privilege` VALUES ('1', '1033');
INSERT INTO `admin_role_privilege` VALUES ('1', '1034');
INSERT INTO `admin_role_privilege` VALUES ('1', '1041');
INSERT INTO `admin_role_privilege` VALUES ('1', '1042');
INSERT INTO `admin_role_privilege` VALUES ('1', '1043');
INSERT INTO `admin_role_privilege` VALUES ('1', '1044');
INSERT INTO `admin_role_privilege` VALUES ('1', '1051');
INSERT INTO `admin_role_privilege` VALUES ('1', '1052');
INSERT INTO `admin_role_privilege` VALUES ('1', '1053');
INSERT INTO `admin_role_privilege` VALUES ('1', '1061');
INSERT INTO `admin_role_privilege` VALUES ('1', '1062');
INSERT INTO `admin_role_privilege` VALUES ('1', '1071');
INSERT INTO `admin_role_privilege` VALUES ('1', '1081');
INSERT INTO `admin_role_privilege` VALUES ('1', '1101');
INSERT INTO `admin_role_privilege` VALUES ('1', '1102');
INSERT INTO `admin_role_privilege` VALUES ('1', '1103');
INSERT INTO `admin_role_privilege` VALUES ('1', '1104');
INSERT INTO `admin_role_privilege` VALUES ('1', '1105');
INSERT INTO `admin_role_privilege` VALUES ('1', '1106');
INSERT INTO `admin_role_privilege` VALUES ('1', '1151');
INSERT INTO `admin_role_privilege` VALUES ('1', '1152');
INSERT INTO `admin_role_privilege` VALUES ('1', '1153');
INSERT INTO `admin_role_privilege` VALUES ('1', '1154');
INSERT INTO `admin_role_privilege` VALUES ('1', '1155');
INSERT INTO `admin_role_privilege` VALUES ('1', '1156');
INSERT INTO `admin_role_privilege` VALUES ('1', '1157');
INSERT INTO `admin_role_privilege` VALUES ('1', '1158');
INSERT INTO `admin_role_privilege` VALUES ('1', '1159');
INSERT INTO `admin_role_privilege` VALUES ('1', '1160');
INSERT INTO `admin_role_privilege` VALUES ('1', '1161');
INSERT INTO `admin_role_privilege` VALUES ('1', '1162');
INSERT INTO `admin_role_privilege` VALUES ('1', '1201');
INSERT INTO `admin_role_privilege` VALUES ('1', '1202');
INSERT INTO `admin_role_privilege` VALUES ('1', '1203');
INSERT INTO `admin_role_privilege` VALUES ('1', '1204');
INSERT INTO `admin_role_privilege` VALUES ('1', '1205');
INSERT INTO `admin_role_privilege` VALUES ('1', '1206');
INSERT INTO `admin_role_privilege` VALUES ('1', '1251');
INSERT INTO `admin_role_privilege` VALUES ('1', '1252');
INSERT INTO `admin_role_privilege` VALUES ('1', '1253');
INSERT INTO `admin_role_privilege` VALUES ('1', '1254');
INSERT INTO `admin_role_privilege` VALUES ('1', '1255');
INSERT INTO `admin_role_privilege` VALUES ('1', '1256');
INSERT INTO `admin_role_privilege` VALUES ('1', '1257');
INSERT INTO `admin_role_privilege` VALUES ('1', '1258');
INSERT INTO `admin_role_privilege` VALUES ('1', '1301');
INSERT INTO `admin_role_privilege` VALUES ('1', '1302');
INSERT INTO `admin_role_privilege` VALUES ('1', '1303');
INSERT INTO `admin_role_privilege` VALUES ('1', '1304');
INSERT INTO `admin_role_privilege` VALUES ('1', '1305');
INSERT INTO `admin_role_privilege` VALUES ('1', '1306');
INSERT INTO `admin_role_privilege` VALUES ('1', '1351');
INSERT INTO `admin_role_privilege` VALUES ('1', '1352');
INSERT INTO `admin_role_privilege` VALUES ('1', '1353');
INSERT INTO `admin_role_privilege` VALUES ('1', '1354');
INSERT INTO `admin_role_privilege` VALUES ('1', '1355');
INSERT INTO `admin_role_privilege` VALUES ('1', '1356');
INSERT INTO `admin_role_privilege` VALUES ('1', '1401');
INSERT INTO `admin_role_privilege` VALUES ('1', '1402');
INSERT INTO `admin_role_privilege` VALUES ('1', '1403');
INSERT INTO `admin_role_privilege` VALUES ('1', '1404');
INSERT INTO `admin_role_privilege` VALUES ('1', '1405');
INSERT INTO `admin_role_privilege` VALUES ('1', '1406');
INSERT INTO `admin_role_privilege` VALUES ('1', '1451');
INSERT INTO `admin_role_privilege` VALUES ('1', '1452');


-- ----------------------------
-- Table structure for admin_user
-- ----------------------------
DROP TABLE IF EXISTS `admin_user`;
CREATE TABLE `admin_user` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(30) NOT NULL,
  `password` varchar(100) NOT NULL,
  `realname` varchar(50) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `mobile` varchar(50) DEFAULT NULL,
  `login_count` int(11) DEFAULT '0',
  `last_login_ip` varchar(100) DEFAULT NULL,
  `last_login_time` datetime DEFAULT NULL,
  `status` tinyint(2) DEFAULT '1',
  `create_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of admin_user
-- ----------------------------
INSERT INTO `admin_user` VALUES ('1', 'admin', '21232f297a57a5a743894a0e4a801fc3', 'Administrator', 'admin@mail.com', '', '0', '192.168.129.1', CURRENT_TIMESTAMP, '1', CURRENT_TIMESTAMP);
-- ----------------------------
-- Table structure for admin_user_role
-- ----------------------------
DROP TABLE IF EXISTS `admin_user_role`;
CREATE TABLE `admin_user_role` (
  `user_id` int(10) NOT NULL,
  `role_id` smallint(4) NOT NULL,
  PRIMARY KEY (`user_id`,`role_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of admin_user_role
-- ----------------------------
INSERT INTO `admin_user_role` VALUES ('1', '1');

-- ----------------------------
-- Table structure for wlblazers_status
-- ----------------------------
DROP TABLE IF EXISTS `wlblazers_status`;
CREATE TABLE `wlblazers_status` (
  `wl_variables` varchar(255) NOT NULL,
  `wl_value` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wlblazers_status
-- ----------------------------
INSERT INTO `wlblazers_status` VALUES ('wlblazers_running', '1');
INSERT INTO `wlblazers_status` VALUES ('wlblazers_version', '1.0.0 Beta');
INSERT INTO `wlblazers_status` VALUES ('wlblazers_checktime', '2017-05-01 09:56:10');

-- ----------------------------
-- Table structure for options
-- ----------------------------
DROP TABLE IF EXISTS `options`;
CREATE TABLE `options` (
  `name` varchar(50) DEFAULT NULL,
  `value` varchar(255) DEFAULT NULL,
  `description` varchar(100) DEFAULT NULL,
  KEY `idx_name` (`name`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of options
-- ----------------------------
INSERT INTO `options` VALUES ('monitor', '1', '是否开启全局监控,此项如果关闭则所有项目都不会被监控，下面监控选项都失效');
INSERT INTO `options` VALUES ('monitor_mysql', '1', '是否开启MySQL状态监控');
INSERT INTO `options` VALUES ('send_alarm_mail', '1', '是否发送报警邮件');
INSERT INTO `options` VALUES ('send_mail_to_list', '', '报警邮件通知人员');
INSERT INTO `options` VALUES ('monitor_os', '1', '是否开启OS监控');
INSERT INTO `options` VALUES ('monitor_mongodb', '1', '是否监控MongoDB');
INSERT INTO `options` VALUES ('alarm', '1', '是否开启告警');
INSERT INTO `options` VALUES ('send_mail_max_count', '3', '发送邮件最大次数');
INSERT INTO `options` VALUES ('report_mail_to_list', '', '报告邮件推送接收人员');
INSERT INTO `options` VALUES ('frequency_monitor', '60', '监控频率');
INSERT INTO `options` VALUES ('send_mail_sleep_time', '720', '发送邮件休眠时间(分钟)');
INSERT INTO `options` VALUES ('mailtype', 'html', '邮件发送配置:邮件类型');
INSERT INTO `options` VALUES ('mailprotocol', 'smtp', '邮件发送配置:邮件协议');
INSERT INTO `options` VALUES ('smtp_host', 'smtp.163.com', '邮件发送配置:邮件主机');
INSERT INTO `options` VALUES ('smtp_port', '25', '邮件发送配置:邮件端口');
INSERT INTO `options` VALUES ('smtp_user', 'wlblazers', '邮件发送配置:用户');
INSERT INTO `options` VALUES ('smtp_pass', '', '邮件发送配置:密码');
INSERT INTO `options` VALUES ('smtp_timeout', '10', '邮件发送配置:超时时间');
INSERT INTO `options` VALUES ('mailfrom', 'wlblazers@163.com', '邮件发送配置:发件人');
INSERT INTO `options` VALUES ('monitor_redis', '1', '是否监控Redis');
INSERT INTO `options` VALUES ('monitor_oracle', '1', '是否监控Oracle');
INSERT INTO `options` VALUES ('send_alarm_sms', '0', '是否发生短信');
INSERT INTO `options` VALUES ('send_sms_to_list', '', '短信收件人列表');
INSERT INTO `options` VALUES ('send_sms_max_count', '3', '发送短信最大次数');
INSERT INTO `options` VALUES ('send_sms_sleep_time', '180', '发送短信休眠时间(分钟)');
INSERT INTO `options` VALUES ('sms_fetion_user', '', '飞信发送短信账号');
INSERT INTO `options` VALUES ('sms_fetion_pass', '', '飞信发送短信密码');
INSERT INTO `options` VALUES ('smstype', 'fetion', '发送短信方式：fetion/api');
INSERT INTO `options` VALUES ('monitor_sqlserver', '0', '是否开启SQLServer监控');
