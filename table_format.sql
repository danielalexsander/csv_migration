/*
Navicat MySQL Data Transfer

Source Server         : local
Source Server Version : 50505
Source Host           : localhost:3306
Source Database       : test

Target Server Type    : MYSQL
Target Server Version : 50505
File Encoding         : 65001

Date: 2020-04-02 08:37:57
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `tab_test`
-- ----------------------------
DROP TABLE IF EXISTS `tab_test`;
CREATE TABLE `tab_test` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `column1` text DEFAULT NULL,
  `column2` text DEFAULT NULL,
  `column3` text DEFAULT NULL,
  `column4` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of tab_test
-- ----------------------------
