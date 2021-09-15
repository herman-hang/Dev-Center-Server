/*
 Navicat MySQL Data Transfer

 Source Server         : root
 Source Server Type    : MySQL
 Source Server Version : 50726
 Source Host           : localhost:3306
 Source Schema         : install

 Target Server Type    : MySQL
 Target Server Version : 50726
 File Encoding         : 65001

 Date: 15/09/2021 16:37:51
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for dev_admin
-- ----------------------------
DROP TABLE IF EXISTS `dev_admin`;
CREATE TABLE `dev_admin`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '管理员ID',
  `user` varchar(16) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '管理员用户名',
  `password` char(60) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '管理员密码',
  `photo` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '头像',
  `name` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '真实姓名',
  `card` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '身份证号码',
  `sex` enum('0','1','2') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '2' COMMENT '性别(0为女,1为男,2为保密)',
  `age` tinyint(3) NULL DEFAULT NULL COMMENT '年龄',
  `region` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '住址',
  `mobile` varchar(11) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '手机号码',
  `email` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '邮箱',
  `introduction` varchar(200) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '简介',
  `create_time` int(10) NOT NULL COMMENT '创建时间',
  `update_time` int(10) NOT NULL COMMENT '更新时间',
  `status` enum('0','1') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '1' COMMENT '状态(0已停用,1已启用)',
  `login_sum` int(11) NOT NULL DEFAULT 0 COMMENT '登录总数',
  `role_id` int(11) NOT NULL DEFAULT 1 COMMENT '权限组ID',
  `lastlog_time` int(10) NULL DEFAULT NULL COMMENT '上一次登录时间',
  `lastlog_ip` varchar(10) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '上一次登录IP地址',
  `login_error` tinyint(3) NOT NULL DEFAULT 0 COMMENT '登录错误次数',
  `error_time` int(10) NULL DEFAULT NULL COMMENT '登录错误时间',
  `ban_time` int(10) NULL DEFAULT NULL COMMENT '登录封禁时间',
  `weixin_openid` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '微信openid',
  `qq_openid` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT 'QQopenid',
  `weibo_openid` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '微博openid',
  `gitee_openid` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT 'Gitee openid',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 12 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of dev_admin
-- ----------------------------
INSERT INTO `dev_admin` VALUES (1, 'admin', '$2y$10$q2Nhgq.ab8j8Qm3tvONxU.f6IP55vx2H7CydX0erDBEMCEmYeExwe', '', '', NULL, '1', 3, '', '', '', '', 1629377219, 1631424557, '1', 0, 1, NULL, '', 0, NULL, NULL, NULL, NULL, '', '');

-- ----------------------------
-- Table structure for dev_admin_log
-- ----------------------------
DROP TABLE IF EXISTS `dev_admin_log`;
CREATE TABLE `dev_admin_log`  (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '日志id',
  `type` enum('1','2') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '2' COMMENT '日志类型(1为登录日志，2为操作日志)',
  `admin_id` int(11) NOT NULL COMMENT '管理员id(外键)',
  `content` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '日志内容',
  `ip` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '客户端ip',
  `create_time` int(10) NOT NULL COMMENT '记录时间',
  `update_time` int(10) NOT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1166 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of dev_admin_log
-- ----------------------------

-- ----------------------------
-- Table structure for dev_advertising
-- ----------------------------
DROP TABLE IF EXISTS `dev_advertising`;
CREATE TABLE `dev_advertising`  (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '广告ID',
  `type` enum('0','1') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '0' COMMENT '广告类型（0文字，1图片）',
  `name` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '广告名称',
  `url` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '跳转链接',
  `text` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '广告文本',
  `img` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '图片地址',
  `status` enum('0','1') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '0' COMMENT '状态（0投放中，1已下架）',
  `star_time` bigint(13) NOT NULL COMMENT '投放时间',
  `end_time` bigint(13) NOT NULL COMMENT '过期时间',
  `create_time` int(10) NOT NULL COMMENT '创建时间',
  `update_time` int(10) NOT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 13 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of dev_advertising
-- ----------------------------

-- ----------------------------
-- Table structure for dev_app
-- ----------------------------
DROP TABLE IF EXISTS `dev_app`;
CREATE TABLE `dev_app`  (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '应用ID',
  `name` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '应用名称',
  `img` varchar(200) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '缩略图',
  `is_pay` enum('0','1') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '0' COMMENT '是否付费（0免费，1付费）',
  `money` decimal(10, 2) NOT NULL DEFAULT 0.00 COMMENT '付费金额',
  `author` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '作者',
  `introduce` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '介绍',
  `status` enum('0','1','2','3') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '2' COMMENT '状态（0已下架，1审核中，2已发布，3已驳回）',
  `create_time` int(10) NOT NULL COMMENT '发布时间',
  `update_time` int(10) NOT NULL COMMENT '更新时间',
  `download` int(11) NOT NULL DEFAULT 0 COMMENT '下载量',
  `type` enum('0','1') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '0' COMMENT '应用类型（0插件，1模板）',
  `zip` varchar(200) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '应用包下载地址',
  `developer_id` int(11) NOT NULL DEFAULT 0 COMMENT '开发者ID（0为官方发布，其他为开发者发布）',
  `cause` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '驳回原因',
  `user_id` int(11) NOT NULL DEFAULT 0 COMMENT '用户ID（0为官方发布，其他为开发者发布）',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 22 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of dev_app
-- ----------------------------

-- ----------------------------
-- Table structure for dev_authorization
-- ----------------------------
DROP TABLE IF EXISTS `dev_authorization`;
CREATE TABLE `dev_authorization`  (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '授权站点ID',
  `name` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '授权站点名称',
  `ip` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '授权IP地址',
  `domain_one` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '授权域名1',
  `domain_two` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '授权域名2',
  `domain_tree` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '授权域名3',
  `create_time` int(10) NOT NULL COMMENT '授权时间',
  `update_time` int(10) NOT NULL COMMENT '更新时间',
  `auth_plug` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '已授权的插件ID',
  `auth_temp` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '已授权的模板ID',
  `user_id` int(11) NOT NULL COMMENT '用户ID',
  `status` enum('0','1') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '1' COMMENT '状态（0封禁，1正常）',
  `level` enum('0','1','2','3') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '0' COMMENT '授权服务（0铁牌，1铜牌，2银牌，3金牌）',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 91 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of dev_authorization
-- ----------------------------

-- ----------------------------
-- Table structure for dev_authorization_config
-- ----------------------------
DROP TABLE IF EXISTS `dev_authorization_config`;
CREATE TABLE `dev_authorization_config`  (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '授权配置ID',
  `copper` decimal(10, 2) NOT NULL DEFAULT 0.00 COMMENT '铜牌授权价格',
  `copper_server_content` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '铜牌享受的服务内容',
  `silver` decimal(10, 2) NOT NULL DEFAULT 0.00 COMMENT '银牌授权价格',
  `silver_server_content` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '银牌享受的服务内容',
  `gold` decimal(10, 2) NOT NULL DEFAULT 0.00 COMMENT '金牌授权价格',
  `gold_server_content` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '金牌享受的服务内容',
  `copper_silver` decimal(10, 2) NOT NULL DEFAULT 0.00 COMMENT '铜牌升级银牌授权价格',
  `copper_gold` decimal(10, 2) NOT NULL DEFAULT 0.00 COMMENT '铜牌升级金牌授权价格',
  `silver_gold` decimal(10, 2) NOT NULL DEFAULT 0.00 COMMENT '银牌升级金牌授权价格',
  `iron_copper` decimal(10, 2) NOT NULL COMMENT '铁牌升级铜牌授权价格',
  `iron_silver` decimal(10, 2) NOT NULL COMMENT '铁牌升级银牌授权价格',
  `iron_gold` decimal(10, 2) NOT NULL COMMENT '铁牌升级金牌授权价格',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 2 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of dev_authorization_config
-- ----------------------------
INSERT INTO `dev_authorization_config` VALUES (1, 0.10, '', 0.10, '', 0.10, '', 0.10, 0.10, 0.10, 0.10, 0.10, 0.10);

-- ----------------------------
-- Table structure for dev_developer_config
-- ----------------------------
DROP TABLE IF EXISTS `dev_developer_config`;
CREATE TABLE `dev_developer_config`  (
  `id` int(11) NOT NULL COMMENT '开发者配置ID',
  `condition` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '申请开发者条件说明',
  `copper` tinyint(11) NOT NULL DEFAULT 25 COMMENT '铜牌服务费率（单位：%）',
  `silver` tinyint(11) NOT NULL DEFAULT 20 COMMENT '银牌服务费率（单位：%）',
  `gold` tinyint(11) NOT NULL DEFAULT 10 COMMENT '金牌服务费率（单位：%）',
  `copper_silver` int(11) NOT NULL DEFAULT 5 COMMENT '铜牌升级到银牌需要发布几个应用',
  `silver_gold` int(11) NOT NULL DEFAULT 10 COMMENT '银牌升级到金牌需要发布几个应用',
  `is_audit` enum('0','1') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '1' COMMENT '升级方式（0为手动升级，1为自动升级）',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = COMPACT;

-- ----------------------------
-- Records of dev_developer_config
-- ----------------------------
INSERT INTO `dev_developer_config` VALUES (1, '申请条件内容', 25, 20, 10, 5, 10, '1');

-- ----------------------------
-- Table structure for dev_developer_withdraw
-- ----------------------------
DROP TABLE IF EXISTS `dev_developer_withdraw`;
CREATE TABLE `dev_developer_withdraw`  (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键提现ID',
  `developer_id` int(11) NOT NULL COMMENT '开发者ID',
  `money` decimal(10, 2) NOT NULL DEFAULT 0.00 COMMENT '提现金额',
  `create_time` int(10) NOT NULL COMMENT '提现时间',
  `cause` varchar(200) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '驳回原因',
  `status` enum('0','1','2') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '0' COMMENT '状态（0审核中，1通过，2驳回）',
  `indent` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '订单号',
  `withdraw_account` enum('0','1','2') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '0' COMMENT '提款账户（0支付宝，1微信，2QQ）',
  `user_id` int(11) NOT NULL COMMENT '用户ID',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 8 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of dev_developer_withdraw
-- ----------------------------

-- ----------------------------
-- Table structure for dev_email
-- ----------------------------
DROP TABLE IF EXISTS `dev_email`;
CREATE TABLE `dev_email`  (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '邮箱id',
  `email` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '邮箱',
  `sll` char(4) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '发信端口',
  `key` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '邮件key',
  `stmp` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '发信stmp',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of dev_email
-- ----------------------------
INSERT INTO `dev_email` VALUES (1, NULL, NULL, NULL, NULL);

-- ----------------------------
-- Table structure for dev_group
-- ----------------------------
DROP TABLE IF EXISTS `dev_group`;
CREATE TABLE `dev_group`  (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '权限组id',
  `name` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '权限组名称',
  `rules` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '权限组拥有的权限',
  `status` enum('0','1') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '1' COMMENT '状态(0停用,1启用)',
  `instruction` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '说明',
  `create_time` int(10) NOT NULL COMMENT '创建时间',
  `update_time` int(10) NOT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 28 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of dev_group
-- ----------------------------
INSERT INTO `dev_group` VALUES (1, '超级管理员', '3,4,8,9,10,11,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35,36,37,38,39,40,41,42,43,44,45,46,47,48,49,50,51,52,53,54,55,56,57,58,59,60,61,62,63,64,65,66,67,68,69,70,71,72,73,74,81,82,83,75,76,77,78,79,80', '1', '掌握所有权限', 1611725691, 1629367001);

-- ----------------------------
-- Table structure for dev_menu
-- ----------------------------
DROP TABLE IF EXISTS `dev_menu`;
CREATE TABLE `dev_menu`  (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '规则id',
  `pid` int(11) NOT NULL COMMENT '父id',
  `name` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '规则名称',
  `type` tinyint(3) NOT NULL DEFAULT 1 COMMENT '类型(为1condition字段可以定义规则表达式)',
  `url` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '地址(模块/控制器/方法)',
  `sort` int(11) NOT NULL COMMENT '排序',
  `status` enum('0','1') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '1' COMMENT '状态(0停用,1启用)',
  `condition` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '规则附加条件',
  `level` tinyint(3) NOT NULL DEFAULT 0 COMMENT '级别',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `pid`(`pid`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 89 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of dev_menu
-- ----------------------------
INSERT INTO `dev_menu` VALUES (1, 0, '管理员管理', 1, '', 1, '1', NULL, 0);
INSERT INTO `dev_menu` VALUES (2, 1, '管理员列表', 1, 'admin/list', 2, '1', NULL, 1);
INSERT INTO `dev_menu` VALUES (3, 2, '添加管理员', 1, 'admin/add', 3, '1', NULL, 2);
INSERT INTO `dev_menu` VALUES (4, 2, '编辑管理员', 1, 'admin/edit', 4, '1', NULL, 2);
INSERT INTO `dev_menu` VALUES (5, 2, '删除管理员', 1, 'admin/delete', 5, '1', NULL, 2);
INSERT INTO `dev_menu` VALUES (6, 2, '修改状态', 1, 'admin/statusEdit', 6, '1', NULL, 2);
INSERT INTO `dev_menu` VALUES (7, 0, '系统管理', 1, '', 101, '1', NULL, 0);
INSERT INTO `dev_menu` VALUES (8, 7, '系统设置', 1, 'system/system', 102, '1', NULL, 1);
INSERT INTO `dev_menu` VALUES (9, 8, '编辑系统设置', 1, 'system/systemEdit', 103, '1', NULL, 2);
INSERT INTO `dev_menu` VALUES (10, 7, '安全配置', 1, 'system/security', 104, '1', NULL, 1);
INSERT INTO `dev_menu` VALUES (11, 10, '编辑安全配置', 1, 'system/securityEdit', 105, '1', NULL, 2);
INSERT INTO `dev_menu` VALUES (12, 7, '开关管理', 1, 'system/switch', 106, '1', NULL, 1);
INSERT INTO `dev_menu` VALUES (13, 12, '编辑开关', 1, 'system/switchEdit', 107, '1', NULL, 2);
INSERT INTO `dev_menu` VALUES (14, 7, '修改密码', 1, 'system/pass', 108, '1', NULL, 1);
INSERT INTO `dev_menu` VALUES (15, 14, '编辑修改密码', 1, 'system/passEdit', 109, '1', NULL, 2);
INSERT INTO `dev_menu` VALUES (16, 1, '权限组列表', 1, 'group/list', 7, '1', NULL, 1);
INSERT INTO `dev_menu` VALUES (17, 16, '添加权限组', 1, 'group/add', 8, '1', NULL, 2);
INSERT INTO `dev_menu` VALUES (18, 16, '编辑权限组', 1, 'group/edit', 9, '1', NULL, 2);
INSERT INTO `dev_menu` VALUES (19, 16, '删除权限组', 1, 'group/delete', 10, '1', NULL, 2);
INSERT INTO `dev_menu` VALUES (20, 16, '修改状态', 1, 'group/statusEdit', 11, '1', NULL, 2);
INSERT INTO `dev_menu` VALUES (21, 0, '日志信息', 1, '', 201, '1', NULL, 0);
INSERT INTO `dev_menu` VALUES (22, 21, '日志记录', 1, 'adminlog/list', 202, '1', NULL, 1);
INSERT INTO `dev_menu` VALUES (23, 0, '功能配置', 1, NULL, 301, '1', NULL, 0);
INSERT INTO `dev_menu` VALUES (24, 23, '支付配置', 1, 'functional/pay', 302, '1', NULL, 1);
INSERT INTO `dev_menu` VALUES (25, 24, '编辑支付配置', 1, 'functional/payEdit', 303, '1', NULL, 2);
INSERT INTO `dev_menu` VALUES (26, 23, '短信配置', 1, 'functional/sms', 304, '1', NULL, 1);
INSERT INTO `dev_menu` VALUES (27, 26, '编辑短信配置', 1, 'functional/smsEdit', 305, '1', NULL, 2);
INSERT INTO `dev_menu` VALUES (28, 26, '短信测试', 1, 'functional/testSms', 306, '1', NULL, 2);
INSERT INTO `dev_menu` VALUES (29, 23, '邮件配置', 1, 'functional/email', 307, '1', NULL, 1);
INSERT INTO `dev_menu` VALUES (30, 29, '编辑邮件配置', 1, 'functional/emailEdit', 308, '1', NULL, 2);
INSERT INTO `dev_menu` VALUES (31, 29, '邮件测试', 1, 'functional/testEmail', 309, '1', NULL, 2);
INSERT INTO `dev_menu` VALUES (32, 23, '第三方登录配置', 1, 'functional/thirdparty', 310, '1', NULL, 1);
INSERT INTO `dev_menu` VALUES (33, 32, '编辑第三方登录配置', 1, 'functional/thirdpartyEdit', 311, '1', NULL, 2);
INSERT INTO `dev_menu` VALUES (34, 0, '用户管理', 1, NULL, 401, '1', NULL, 0);
INSERT INTO `dev_menu` VALUES (35, 34, '用户列表', 1, 'user/list', 402, '1', NULL, 1);
INSERT INTO `dev_menu` VALUES (36, 35, '添加用户', 1, 'user/add', 403, '1', NULL, 2);
INSERT INTO `dev_menu` VALUES (37, 35, '编辑用户', 1, 'user/edit', 404, '1', NULL, 2);
INSERT INTO `dev_menu` VALUES (38, 35, '删除用户', 1, 'user/delete', 405, '1', NULL, 2);
INSERT INTO `dev_menu` VALUES (39, 35, '修改状态', 1, 'user/statusEdit', 406, '1', NULL, 2);
INSERT INTO `dev_menu` VALUES (40, 34, '消费明细', 1, 'user/buyLog', 407, '1', NULL, 1);
INSERT INTO `dev_menu` VALUES (41, 0, '通知公告', 1, '', 501, '1', NULL, 0);
INSERT INTO `dev_menu` VALUES (42, 41, '公告列表', 1, 'notice/list', 502, '1', NULL, 1);
INSERT INTO `dev_menu` VALUES (43, 42, '发布公告', 1, 'notice/add', 503, '1', NULL, 2);
INSERT INTO `dev_menu` VALUES (44, 42, '编辑公告', 1, 'notice/edit', 504, '1', NULL, 2);
INSERT INTO `dev_menu` VALUES (45, 42, '删除公告', 1, 'notice/delete', 505, '1', NULL, 2);
INSERT INTO `dev_menu` VALUES (46, 42, '修改状态', 1, 'notice/statusEdit', 506, '1', NULL, 2);
INSERT INTO `dev_menu` VALUES (47, 0, '广告管理', 1, NULL, 601, '1', NULL, 0);
INSERT INTO `dev_menu` VALUES (48, 47, '广告列表', 1, 'advertising/list', 602, '1', NULL, 1);
INSERT INTO `dev_menu` VALUES (49, 48, '添加广告', 1, 'advertising/add', 603, '1', NULL, 2);
INSERT INTO `dev_menu` VALUES (50, 48, '编辑广告', 1, 'advertising/edit', 604, '1', NULL, 2);
INSERT INTO `dev_menu` VALUES (51, 48, '删除广告', 1, 'advertising/delete', 605, '1', NULL, 2);
INSERT INTO `dev_menu` VALUES (52, 48, '修改状态', 1, 'advertising/statusEdit', 606, '1', NULL, 2);
INSERT INTO `dev_menu` VALUES (53, 0, '应用中心', 1, NULL, 701, '1', NULL, 0);
INSERT INTO `dev_menu` VALUES (54, 53, '应用列表', 1, 'app/list', 702, '1', NULL, 1);
INSERT INTO `dev_menu` VALUES (55, 54, '发布应用', 1, 'app/add', 703, '1', NULL, 2);
INSERT INTO `dev_menu` VALUES (56, 54, '编辑应用', 1, 'app/edit', 704, '1', NULL, 2);
INSERT INTO `dev_menu` VALUES (57, 54, '删除应用', 1, 'app/delete', 705, '1', NULL, 2);
INSERT INTO `dev_menu` VALUES (58, 54, '修改状态', 1, 'app/statusEdit', 706, '1', NULL, 2);
INSERT INTO `dev_menu` VALUES (59, 53, '审核列表', 1, 'app/auditList', 707, '1', NULL, 1);
INSERT INTO `dev_menu` VALUES (60, 59, '审核通过操作', 1, 'app/pass', 708, '1', NULL, 2);
INSERT INTO `dev_menu` VALUES (61, 59, '驳回操作', 1, 'app/reject', 709, '1', NULL, 2);
INSERT INTO `dev_menu` VALUES (62, 0, '授权管理', 1, NULL, 801, '1', NULL, 0);
INSERT INTO `dev_menu` VALUES (63, 62, '授权列表', 1, 'authorization/list', 802, '1', NULL, 1);
INSERT INTO `dev_menu` VALUES (64, 63, '添加授权', 1, 'authorization/add', 803, '1', NULL, 2);
INSERT INTO `dev_menu` VALUES (65, 63, '编辑授权', 1, 'authorization/edit', 804, '1', NULL, 2);
INSERT INTO `dev_menu` VALUES (66, 63, '删除授权', 1, 'authorization/delete', 805, '1', NULL, 2);
INSERT INTO `dev_menu` VALUES (67, 63, '修改状态', 1, 'authorization/statusEdit', 806, '1', NULL, 2);
INSERT INTO `dev_menu` VALUES (68, 0, '开发者管理', 1, NULL, 901, '1', NULL, 0);
INSERT INTO `dev_menu` VALUES (69, 68, '开发者列表', 1, 'developer/list', 902, '1', NULL, 1);
INSERT INTO `dev_menu` VALUES (70, 69, '编辑开发者', 1, 'developer/add', 903, '1', NULL, 2);
INSERT INTO `dev_menu` VALUES (71, 69, '降为用户', 1, 'developer/demote', 904, '1', NULL, 2);
INSERT INTO `dev_menu` VALUES (72, 68, '审核列表', 1, 'developer/auditList', 905, '1', NULL, 1);
INSERT INTO `dev_menu` VALUES (73, 72, '审核通过操作', 1, 'auditList/pass', 906, '1', NULL, 2);
INSERT INTO `dev_menu` VALUES (74, 72, '驳回操作', 1, 'auditList/reject', 907, '1', NULL, 2);
INSERT INTO `dev_menu` VALUES (75, 0, '升级中心', 1, NULL, 1001, '1', NULL, 0);
INSERT INTO `dev_menu` VALUES (76, 75, '版本发布', 1, 'upgrade/list', 1002, '1', NULL, 1);
INSERT INTO `dev_menu` VALUES (77, 76, '发布升级版', 1, 'upgrade/add', 1003, '1', NULL, 2);
INSERT INTO `dev_menu` VALUES (78, 76, '编辑升级包', 1, 'upgrade/edit', 1004, '1', NULL, 2);
INSERT INTO `dev_menu` VALUES (79, 76, '删除升级包', 1, 'upgrade/delete', 1005, '1', NULL, 2);
INSERT INTO `dev_menu` VALUES (80, 76, '修改状态', 1, 'upgrade/statusEdit', 1006, '1', NULL, 2);
INSERT INTO `dev_menu` VALUES (81, 68, '提现审核', 1, 'withdraw/list', 908, '1', NULL, 1);
INSERT INTO `dev_menu` VALUES (82, 81, '审核通过操作', 1, 'withdraw/add', 909, '1', NULL, 2);
INSERT INTO `dev_menu` VALUES (83, 81, '驳回操作', 1, 'withdraw/edit', 910, '1', NULL, 2);
INSERT INTO `dev_menu` VALUES (84, 62, '盗版记录', 1, 'pirate/list', 807, '1', NULL, 1);
INSERT INTO `dev_menu` VALUES (85, 62, '授权配置', 1, 'authorization/authConfig', 808, '1', NULL, 1);
INSERT INTO `dev_menu` VALUES (86, 85, '编辑授权配置', 1, 'authorization/authConfigEdit', 809, '1', NULL, 2);
INSERT INTO `dev_menu` VALUES (87, 68, '开发者配置', 1, 'developer/developerConfig', 911, '1', NULL, 1);
INSERT INTO `dev_menu` VALUES (88, 87, '编辑开发者配置', 1, 'developer/developerConfigEdit', 912, '1', NULL, 2);

-- ----------------------------
-- Table structure for dev_notice
-- ----------------------------
DROP TABLE IF EXISTS `dev_notice`;
CREATE TABLE `dev_notice`  (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '公告ID',
  `title` varchar(64) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '标题',
  `content` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '内容',
  `inscribe` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '落款',
  `create_time` int(11) NOT NULL COMMENT '发布时间',
  `update_time` int(11) NOT NULL COMMENT '更新时间',
  `status` enum('0','1') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '1' COMMENT '状态（0为已下架，1已发布）',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 13 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of dev_notice
-- ----------------------------

-- ----------------------------
-- Table structure for dev_pay
-- ----------------------------
DROP TABLE IF EXISTS `dev_pay`;
CREATE TABLE `dev_pay`  (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '支付id',
  `alipay_private_id` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '支付宝AppID',
  `alipay_public_key` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '支付宝公钥',
  `alipay_private_key` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '应用私钥',
  `wxpay_mchid` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '微信商户号',
  `wxpay_key` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '微信商户key',
  `wxpay_appid` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '公众号AppID',
  `alipayf2f_private_id` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '支付宝当面付AppID',
  `alipayf2f_private_key` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '支付宝当面付应用私钥',
  `alipayf2f_public_key` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '支付宝公钥',
  `qqpay_mchid` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT 'QQ商户号',
  `qqpay_key` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT 'QQ商户key',
  `epay_api` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '易支付API',
  `epay_appid` varchar(10) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '易支付商户号',
  `epay_key` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '易支付key',
  `alipay_type` enum('0','1') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '0' COMMENT '支付宝支付类型（0为官方支付，1为当面付）',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of dev_pay
-- ----------------------------
INSERT INTO `dev_pay` VALUES (1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0');

-- ----------------------------
-- Table structure for dev_pirate
-- ----------------------------
DROP TABLE IF EXISTS `dev_pirate`;
CREATE TABLE `dev_pirate`  (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键ID',
  `domain` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '域名',
  `ip` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT 'IP地址',
  `create_time` int(10) NOT NULL COMMENT '记录时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 6 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of dev_pirate
-- ----------------------------

-- ----------------------------
-- Table structure for dev_sms
-- ----------------------------
DROP TABLE IF EXISTS `dev_sms`;
CREATE TABLE `dev_sms`  (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '短信id',
  `sign_id` int(11) NULL DEFAULT NULL COMMENT '签名ID',
  `app_code` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT 'AppCode',
  `smsbao_account` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '短信宝账号',
  `smsbao_pass` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '短信宝密码',
  `sms_type` enum('0','1') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '0' COMMENT '短信发送接口（0为ThinkAPI，1为短信宝）',
  `bind_mobile` int(11) NULL DEFAULT NULL COMMENT '绑定手机短信模板ID',
  `relieve_mobile` int(11) NULL DEFAULT NULL COMMENT '解除手机短信模板ID',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of dev_sms
-- ----------------------------
INSERT INTO `dev_sms` VALUES (1, NULL, NULL, NULL, NULL, '0', NULL, NULL);

-- ----------------------------
-- Table structure for dev_switch
-- ----------------------------
DROP TABLE IF EXISTS `dev_switch`;
CREATE TABLE `dev_switch`  (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '开关id',
  `wxpay_switch` enum('0','1') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '1' COMMENT '微信支付开关(0为关，1为开)',
  `alipay_switch` enum('0','1') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '1' COMMENT '支付宝支付开关(0为关，1为开)',
  `qqpay_switch` enum('0','1') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '1' COMMENT 'QQ支付开关(0为关，1为开)',
  `epay_switch` enum('0','1') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '1' COMMENT '易支付开关(0为关，1为开)',
  `qqlogin_switch` enum('0','1') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '1' COMMENT 'QQ登录开关（0为关，1为开）',
  `weixinlogin_switch` enum('0','1') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '1' COMMENT '微信登录开关（0为关，1为开）',
  `sinalogin_switch` enum('0','1') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '1' COMMENT '微博登录开关（0为关，1为开）',
  `giteelogin_switch` enum('0','1') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '1' COMMENT 'Gitee登录开关（0为关，1为开）',
  `register_switch` enum('0','1') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '1' COMMENT '用户注册开关（0为关，1为开）',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of dev_switch
-- ----------------------------
INSERT INTO `dev_switch` VALUES (1, '1', '1', '1', '1', '1', '1', '1', '1', '1');

-- ----------------------------
-- Table structure for dev_system
-- ----------------------------
DROP TABLE IF EXISTS `dev_system`;
CREATE TABLE `dev_system`  (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '系统信息id',
  `name` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '网站名称',
  `title` varchar(70) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '网站标题',
  `description` varchar(200) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '网站描述',
  `keywords` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '网站关键词',
  `logo` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '网站LOGO',
  `ico` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '网站ICO',
  `record` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '备案号',
  `copyright` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '底部版权声明',
  `is_website` enum('0','1') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '1' COMMENT '网站开关(0为关，1为开)',
  `email` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '邮箱',
  `qq` int(11) NULL DEFAULT NULL COMMENT 'qq',
  `usergroup` int(11) NULL DEFAULT NULL COMMENT 'qq群',
  `phone` varchar(11) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '电话',
  `tuomaogz` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '官方公众号',
  `address` varchar(200) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '地址',
  `statistical` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '统计代码',
  `version` varchar(10) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '1.0.0' COMMENT '当前系统版本',
  `ip` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '指定ip访问后台',
  `max_logerror` tinyint(3) NOT NULL DEFAULT 5 COMMENT '登录错误最大次数',
  `domain` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '网站域名',
  `file_storage` enum('0','1','2','3') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '0' COMMENT '文件存储区域（0本地，1阿里云，2腾讯云，3七牛云）',
  `images_storage` enum('0','1','2','3') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '0' COMMENT '图片存储区域（0本地，1阿里云，2腾讯云，3七牛云）',
  `access` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '后台入口',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of dev_system
-- ----------------------------
INSERT INTO `dev_system` VALUES (1, '开发者中心', '一款轻量级，开源，免费的系统', '开发者中心v1.0是一款轻量级，开源，免费的系统，采用ThinkPHP6.0.x轻量级框架和ElementUI共同开发完成的，模式采用前后端分离开发，前后台可以独立分开部署，整套程序不超过10MB。', '轻量级、开源、免费', NULL, NULL, NULL, NULL, '1', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1.0.0', NULL, 5, NULL, '0', '0', 'admin');

-- ----------------------------
-- Table structure for dev_thirdparty
-- ----------------------------
DROP TABLE IF EXISTS `dev_thirdparty`;
CREATE TABLE `dev_thirdparty`  (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '第三方id',
  `wx_appid` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '微信appid',
  `wx_secret` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '微信secret',
  `qq_appid` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT 'QQ appid',
  `qq_secret` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT 'QQ secret',
  `weibo_appid` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '微博appid',
  `weibo_secret` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '微博secret',
  `gitee_appid` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT 'Gitee appid',
  `gitee_secret` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT 'Gitee secret',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of dev_thirdparty
-- ----------------------------
INSERT INTO `dev_thirdparty` VALUES (1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);

-- ----------------------------
-- Table structure for dev_upgrade
-- ----------------------------
DROP TABLE IF EXISTS `dev_upgrade`;
CREATE TABLE `dev_upgrade`  (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '版本ID',
  `title` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '版本标题',
  `content` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '升级内容',
  `type` enum('0','1') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '0' COMMENT '升级包类型（0增量，1全量）',
  `version` varchar(10) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '升级版本号',
  `wgt_url` varchar(200) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '升级包下载地址',
  `way` enum('0','1','2') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '0' COMMENT '升级方式（0提示更新，1热更新，2强制更新）',
  `create_time` int(10) NOT NULL COMMENT '发布时间',
  `update_time` int(10) NOT NULL COMMENT '更新时间',
  `status` enum('0','1') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '1' COMMENT '升级状态（0已下线，1已上线）',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 9 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of dev_upgrade
-- ----------------------------

-- ----------------------------
-- Table structure for dev_user
-- ----------------------------
DROP TABLE IF EXISTS `dev_user`;
CREATE TABLE `dev_user`  (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '用户id',
  `user` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '用户名',
  `password` char(60) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '用户密码',
  `photo` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '用户头像',
  `nickname` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '昵称',
  `name` varchar(10) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '真实姓名',
  `card` char(20) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '身份证号码',
  `sex` enum('0','1','2') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '2' COMMENT '性别(0为女,1为男，2为保密)',
  `age` tinyint(3) NULL DEFAULT NULL COMMENT '年龄',
  `region` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '地区',
  `mobile` varchar(11) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '手机号码',
  `email` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '邮箱',
  `qq` varchar(11) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT 'QQ',
  `introduction` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '简介',
  `create_time` int(10) NOT NULL COMMENT '注册时间',
  `update_time` int(10) NOT NULL COMMENT '更新时间',
  `status` enum('0','1') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '1' COMMENT '状态(0已停用,1已启用)',
  `money` decimal(10, 2) NOT NULL DEFAULT 0.00 COMMENT '余额',
  `expenditure` decimal(10, 2) NOT NULL DEFAULT 0.00 COMMENT '总消费',
  `is_developer` enum('0','1','2','3') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '0' COMMENT '是否为开发者（0为用户，1审核中，2开发者，3驳回）',
  `cause` varchar(200) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '驳回原因',
  `weixin_openid` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '微信openid',
  `qq_openid` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT 'QQopenid',
  `weibo_openid` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '微博openid',
  `gitee_openid` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT 'Gitee openid',
  `login_error` tinyint(3) NOT NULL DEFAULT 0 COMMENT '登录错误次数',
  `error_time` int(10) NULL DEFAULT NULL COMMENT '登录错误时间',
  `ban_time` int(10) NULL DEFAULT NULL COMMENT '登录封禁时间',
  `lastlog_ip` varchar(10) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '上一次登录IP地址',
  `lastlog_time` int(10) NULL DEFAULT NULL COMMENT '上一次登录时间',
  `login_sum` int(11) NOT NULL DEFAULT 0 COMMENT '登录总数',
  `api_key` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT 'API KEY',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 24 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of dev_user
-- ----------------------------

-- ----------------------------
-- Table structure for dev_user_buylog
-- ----------------------------
DROP TABLE IF EXISTS `dev_user_buylog`;
CREATE TABLE `dev_user_buylog`  (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '用户购买日志id',
  `uid` int(11) NOT NULL COMMENT '用户id',
  `indent` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '订单号',
  `product_id` int(11) NOT NULL COMMENT '产品id',
  `product_type` enum('0','1') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '产品类型(0插件，1为模板)',
  `buy_type` enum('0','1') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '购买类型(0为购买,1为续费)',
  `pay_type` enum('0','1','2') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '支付方式(0为微信,1为QQ,2为支付宝)',
  `buy_ip` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '购买IP',
  `create_time` int(10) NOT NULL COMMENT '购买时间',
  `status` enum('0','1') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '订单状态(0为未付款,1为已付款)',
  `introduction` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '订单说明',
  `money` decimal(10, 2) NOT NULL DEFAULT 0.00 COMMENT '金额',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `uid`(`uid`) USING BTREE,
  INDEX `product_id`(`product_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 5 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of dev_user_buylog
-- ----------------------------

-- ----------------------------
-- Table structure for dev_user_developer
-- ----------------------------
DROP TABLE IF EXISTS `dev_user_developer`;
CREATE TABLE `dev_user_developer`  (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '开发者ID',
  `alipay` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '支付宝账户',
  `alipay_name` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '支付宝真实姓名',
  `wxpay` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '微信账户',
  `wxpay_name` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '微信真实姓名',
  `qqpay` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT 'QQ账户',
  `qqpay_name` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT 'Q名',
  `user_id` int(11) NOT NULL COMMENT '用户ID',
  `level` enum('0','1','2') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '0' COMMENT '开发者等级（0铜牌开发者，1银牌开发者，2金牌开发者）',
  `brokerage` tinyint(4) NOT NULL DEFAULT 25 COMMENT '服务费',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 16 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of dev_user_developer
-- ----------------------------

SET FOREIGN_KEY_CHECKS = 1;
