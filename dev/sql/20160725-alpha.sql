
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

CREATE TABLE IF NOT EXISTS `cache_basic` (
  `key` varchar(120) NOT NULL COMMENT '键',
  `value` varchar(1024) NOT NULL COMMENT '值'
) ENGINE=MEMORY DEFAULT CHARSET=utf8 COMMENT='基本缓存表';

CREATE TABLE IF NOT EXISTS `log_admin` (
  `type` varchar(10) NOT NULL,
  `uid` int(11) unsigned NOT NULL,
  `time` int(11) unsigned NOT NULL,
  `data` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='管理日志';

CREATE TABLE IF NOT EXISTS `log_user` (
  `type` varchar(10) NOT NULL COMMENT '类型',
  `uid` int(11) NOT NULL COMMENT '用户ID',
  `data` text NOT NULL COMMENT '数据',
  `time` int(11) NOT NULL COMMENT '执行时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用户日志';

CREATE TABLE IF NOT EXISTS `runtime_var` (
  `key` varchar(20) NOT NULL,
  `value` varchar(500) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `user_basic` (
  `id` int(11) unsigned NOT NULL COMMENT '用户ID',
  `username` varchar(30) NOT NULL COMMENT '用户名',
  `password` varchar(50) NOT NULL COMMENT '密码',
  `salt` varchar(8) NOT NULL COMMENT '加密盐',
  `email` varchar(200) NOT NULL COMMENT '邮箱',
  `regdate` int(11) unsigned NOT NULL COMMENT '注册日期',
  `lastlogin` int(11) unsigned NOT NULL COMMENT '最后登录日期',
  `logintimes` int(11) unsigned NOT NULL COMMENT '登陆次数',
  `lastip` varchar(15) NOT NULL COMMENT '上次登录IP',
  `group` int(11) NOT NULL COMMENT '用户组',
  `ban` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='用户_基本信息表';

CREATE TABLE IF NOT EXISTS `user_group` (
  `id` int(11) NOT NULL COMMENT '编号',
  `name` varchar(30) NOT NULL COMMENT '名称',
  `char_list` text COMMENT '角色',
  `parent` int(11) unsigned NOT NULL COMMENT '父用户组'
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='用户组';

ALTER TABLE `cache_basic`
  ADD PRIMARY KEY (`key`) COMMENT 'key';

ALTER TABLE `log_admin`
  ADD KEY `type` (`type`);

ALTER TABLE `runtime_var`
  ADD PRIMARY KEY (`key`);

ALTER TABLE `user_basic`
  ADD PRIMARY KEY (`id`) COMMENT '用户ID主键', ADD KEY `group` (`group`);

ALTER TABLE `user_group`
  ADD PRIMARY KEY (`id`) COMMENT 'id', ADD KEY `parent` (`parent`);

ALTER TABLE `user_basic`
  MODIFY `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '用户ID',AUTO_INCREMENT=1;

ALTER TABLE `user_group`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '编号',AUTO_INCREMENT=1;

INSERT INTO `user_group` (`id`, `name`, `char_list`, `parent`) VALUES
(1, '管理员', NULL, 0),
(2, '普通用户', NULL, 0);

CREATE TABLE `auth_controller` ( `controller` VARCHAR(64) NOT NULL , `action` VARCHAR(64) NOT NULL , `controller_desc` VARCHAR(64) NOT NULL , `action_desc` VARCHAR(64) NOT NULL , INDEX (`controller`) ) ENGINE = InnoDB;
CREATE TABLE `auth_controller_ignore` ( `controller` VARCHAR(64) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL , `action` VARCHAR(64) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL , PRIMARY KEY (`controller`, `action`) ) ENGINE = InnoDB;
