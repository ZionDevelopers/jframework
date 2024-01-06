/*
 Navicat Premium Data Transfer

 Source Server         : juliocesar.me
 Source Server Type    : MySQL
 Source Server Version : 50161
 Source Host           : ftp2624.hospedaria.com.br:3306
 Source Schema         : juliocesar_me_2

 Target Server Type    : MySQL
 Target Server Version : 50161
 File Encoding         : 65001

 Date: 30/06/2023 20:03:46
*/

SET NAMES utf8;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for ipblock
-- ----------------------------
DROP TABLE IF EXISTS `ipblock`;
CREATE TABLE `ipblock`  (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `ip` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `date` datetime NULL DEFAULT NULL,
  `post` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL,
  `get` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL,
  `server` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL,
  `url` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '',
  `hits` bigint(255) NULL DEFAULT 0,
  `last_access` datetime NULL DEFAULT NULL,
  `enabled` tinyint(1) NULL DEFAULT 1,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE utf8_general_ci ROW_FORMAT = Dynamic;

SET FOREIGN_KEY_CHECKS = 1;
