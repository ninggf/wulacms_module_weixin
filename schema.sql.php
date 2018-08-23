<?php
@defined('APPROOT') or header('Page Not Found', true, 404) || die();

//table DDL
$tables ['1.0.0'] [] = "CREATE TABLE IF NOT EXISTS `{prefix}wx_account` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '账户ID',
    `name` VARCHAR(128) NOT NULL COMMENT '公众号名称',
    `wxid` VARCHAR(64) NOT NULL COMMENT '微信号',
    `origin_id` VARCHAR(32) NOT NULL COMMENT '原始ID',
    `type` ENUM('DY', 'FW', 'QY','XC','XY','OP','SF') NOT NULL DEFAULT 'DY' COMMENT 'DY:订阅号，FW:服务号，QY:企业,XC:小程序,XY:小游戏，OP:开放平台，第三方',
    `authed` TINYINT NOT NULL DEFAULT 0 COMMENT '是否认证',
    `avatar` VARCHAR(256) NULL COMMENT '头像',
    `qrcode` VARCHAR(256) NULL COMMENT '二维码',
    `app_id` VARCHAR(48) NULL COMMENT 'AppID',
    `app_secret` VARCHAR(48) NULL COMMENT 'AppSecret',
    `token` VARCHAR(24) NOT NULL COMMENT 'Token',
    `aeskey` VARCHAR(43) NULL COMMENT 'EncodingAESKey',
    `mode` ENUM('T', 'C', 'S') NOT NULL DEFAULT 'T' COMMENT 'T:明文模式; C:兼容模式;S:安全模式',
    `debug` TINYINT UNSIGNED NOT NULL DEFAULT 0 COMMENT '测试状态',
    `base_url` VARCHAR(128) NULL COMMENT 'base url',
    `create_time` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
    `create_uid` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建用户',
    `update_time` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间',
    `update_uid` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新用户',
    PRIMARY KEY (`id`),
    UNIQUE INDEX `UDX_OID` (`origin_id` ASC),
    UNIQUE INDEX `UDX_WXID` (`wxid` ASC),
    UNIQUE INDEX `UDX_TOKEN` (`token` ASC)
)  ENGINE=INNODB DEFAULT CHARACTER SET={encoding} COMMENT='微信账户（服务号，订阅号，企业号，第三方'";

$tables['1.0.0'][] = "CREATE TABLE `{prefix}wx_channel` (
  `channel` varchar(100) NOT NULL COMMENT '渠道标记',
  `channel_name` varchar(100) NOT NULL COMMENT '渠道名字',
  `click_num` int(11) NOT NULL DEFAULT '0' COMMENT '点击次数',
  `create_time` int(10) NOT NULL DEFAULT '0',
  `update_time` int(10) NOT NULL DEFAULT '0',
  `deleted` tinyint(2) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`channel`)
) ENGINE=InnoDB DEFAULT CHARSET={encoding} COMMENT='渠道统计';";
