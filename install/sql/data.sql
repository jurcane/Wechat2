SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for tp_wx_auto_reply
-- ----------------------------
DROP TABLE IF EXISTS `tp_wx_auto_reply`;
CREATE TABLE `tp_wx_auto_reply` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `type` enum('1','2') NOT NULL DEFAULT '1' COMMENT '1关注回复2无匹配回复',
  `is_content` enum('1','2') NOT NULL DEFAULT '1' COMMENT '1为直接内容2为绑定关键词',
  `content` text COMMENT '回复内容',
  `keyword` varchar(100) DEFAULT NULL COMMENT '绑定关键词',
  `push_num` int(10) NOT NULL DEFAULT '0' COMMENT '推送次数',
  `state` enum('1','2') NOT NULL DEFAULT '1' COMMENT '1开放2关闭',
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='关注自动回复和无匹配回复';

-- ----------------------------
-- Table structure for tp_wx_care_record
-- ----------------------------
DROP TABLE IF EXISTS `tp_wx_care_record`;
CREATE TABLE `tp_wx_care_record` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `fromuser` varchar(128) DEFAULT NULL COMMENT '微信用户标识符',
  `is_care` enum('1','0') NOT NULL DEFAULT '1' COMMENT '1为关注0为取消关注',
  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='关注与取消关注记录表';

-- ----------------------------
-- Table structure for tp_wx_common
-- ----------------------------
DROP TABLE IF EXISTS `tp_wx_common`;
CREATE TABLE `tp_wx_common` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `type` enum('1','2','3') NOT NULL DEFAULT '1' COMMENT '1:text,2:single,3:multi',
  `keyword` varchar(100) DEFAULT NULL,
  `title` varchar(50) DEFAULT NULL COMMENT '标题',
  `pic` varchar(150) DEFAULT NULL COMMENT '封面图片',
  `pic_show` enum('1','0') NOT NULL DEFAULT '0' COMMENT '1封面图在内容中显示0不显示',
  `des` varchar(120) DEFAULT NULL COMMENT '信息简介',
  `content` text COMMENT '信息内容',
  `url` varchar(500) DEFAULT NULL COMMENT '外链',
  `state` enum('1','2') NOT NULL DEFAULT '1' COMMENT '1开放2关闭',
  `push_num` int(10) NOT NULL DEFAULT '0' COMMENT '推送次数',
  `click_num` int(10) NOT NULL DEFAULT '0' COMMENT '浏览次数',
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='关键词回复信息库';

-- ----------------------------
-- Table structure for tp_wx_common_detail
-- ----------------------------
DROP TABLE IF EXISTS `tp_wx_common_detail`;
CREATE TABLE `tp_wx_common_detail` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `common_id` int(10) unsigned NOT NULL,
  `title` varchar(100) DEFAULT NULL,
  `pic` varchar(150) DEFAULT NULL,
  `content` text,
  `url` varchar(150) DEFAULT NULL,
  `pic_show` enum('1','0') NOT NULL DEFAULT '1',
  `order_num` tinyint(4) DEFAULT NULL COMMENT '序号',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for tp_wx_keyword
-- ----------------------------
DROP TABLE IF EXISTS `tp_wx_keyword`;
CREATE TABLE `tp_wx_keyword` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `common_id` int(10) DEFAULT NULL COMMENT '信息id',
  `type` varchar(30) NOT NULL DEFAULT 'info' COMMENT '关键词对应信息的类型，比如：text,single,multi,pic,music,vedio...',
  `keyword` varchar(30) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='关键词对应表';

-- ----------------------------
-- Table structure for tp_wx_member
-- ----------------------------
DROP TABLE IF EXISTS `tp_wx_member`;
CREATE TABLE `tp_wx_member` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `group_id` int(10) DEFAULT NULL COMMENT '分组id',
  `fromuser` varchar(50) DEFAULT NULL,
  `nickname` varchar(20) DEFAULT NULL COMMENT '呢称',
  `des` varchar(255) DEFAULT NULL COMMENT '备注',
  `sex` enum('1','2') NOT NULL DEFAULT '1' COMMENT '1男2女',
  `language` varchar(10) DEFAULT NULL,
  `city` varchar(50) DEFAULT NULL,
  `province` varchar(50) DEFAULT NULL,
  `country` varchar(50) DEFAULT NULL,
  `headimgurl` varchar(255) DEFAULT NULL COMMENT '头像源链接',
  `sendmsg_num` int(10) NOT NULL DEFAULT '0',
  `subscribe_time` int(11) DEFAULT NULL COMMENT '关注时间',
  `unsubscribe_time` int(11) DEFAULT NULL COMMENT '取消关注时间',
  `timesign` varchar(20) DEFAULT NULL COMMENT '互动时间戳',
  `status` enum('0','1') NOT NULL DEFAULT '1' COMMENT '0为已取消关注',
  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='微信用户表';

-- ----------------------------
-- Table structure for tp_wx_member_group
-- ----------------------------
DROP TABLE IF EXISTS `tp_wx_member_group`;
CREATE TABLE `tp_wx_member_group` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `customer_id` int(10) DEFAULT NULL,
  `group_id` int(10) DEFAULT NULL COMMENT '分组id',
  `group_name` varchar(64) DEFAULT NULL COMMENT '分组名',
  `group_num` int(10) DEFAULT NULL COMMENT '用户数，暂时不用',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for tp_wx_menu
-- ----------------------------
DROP TABLE IF EXISTS `tp_wx_menu`;
CREATE TABLE `tp_wx_menu` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(16) DEFAULT NULL COMMENT '类型(click关键词,view链接)',
  `title` varchar(50) DEFAULT NULL,
  `url` varchar(512) DEFAULT NULL,
  `pid` int(11) DEFAULT '0',
  `position` int(2) DEFAULT '0',
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for tp_wx_message
-- ----------------------------
DROP TABLE IF EXISTS `tp_wx_message`;
CREATE TABLE `tp_wx_message` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `fromuser` varchar(50) DEFAULT NULL COMMENT '微信用户标识符',
  `touser` varchar(50) DEFAULT NULL COMMENT '微信公众号标识符',
  `msgtype` varchar(20) DEFAULT NULL COMMENT '信息类型',
  `msg_content` text COMMENT '信息内容',
  `reply_list` text COMMENT '客服回复内容',
  `is_reply` enum('0','1') NOT NULL DEFAULT '0' COMMENT '1为已回复',
  `is_read` enum('1','0') NOT NULL DEFAULT '0' COMMENT '1为已阅读',
  `create_time` int(11) DEFAULT NULL,
  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='用户互动消息表';

-- ----------------------------
-- Table structure for tp_wx_token
-- ----------------------------
DROP TABLE IF EXISTS `tp_wx_token`;
CREATE TABLE `tp_wx_token` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `access_token` varchar(255) DEFAULT NULL,
  `expires_time` bigint(15) DEFAULT NULL COMMENT '过期的时间戳',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='access_token存储表';

-- ----------------------------
-- Table structure for tp_admin
-- ----------------------------
DROP TABLE IF EXISTS `tp_admin`;
CREATE TABLE `tp_admin` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(255) NOT NULL DEFAULT '',
  `password` varchar(32) NOT NULL DEFAULT '',
  `last_login_time` int(11) NOT NULL DEFAULT '0',
  `last_login_ip` varchar(255) DEFAULT NULL,
  `status` int(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='管理员列表';

-- ----------------------------
-- Table structure for tp_wx_config
-- ----------------------------
DROP TABLE IF EXISTS `tp_wx_config`;
CREATE TABLE `tp_wx_config` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL DEFAULT '',
  `token` varchar(255) NOT NULL DEFAULT '',
  `app_id` varchar(255) NOT NULL DEFAULT '',
  `app_secret` varchar(255) NOT NULL DEFAULT '',
  `aes_key` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='微信配置';