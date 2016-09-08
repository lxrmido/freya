CREATE TABLE `character` ( `id` INT(11) UNSIGNED NOT NULL , `name` VARCHAR(32) NOT NULL , PRIMARY KEY (`id`) ) ENGINE = InnoDB;
ALTER TABLE `character` CHANGE `id` `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT;
ALTER TABLE `character` CHANGE `name` `name` VARCHAR(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
CREATE TABLE `auth_character_controller` ( `character_id` INT(11) UNSIGNED NOT NULL , `controller` VARCHAR(64) NOT NULL , `action` VARCHAR(64) NOT NULL , PRIMARY KEY (`character_id`, `controller`, `action`) , INDEX (`controller`) , INDEX (`action`) ) ENGINE = InnoDB;
CREATE TABLE `user_character` ( `uid` BIGINT(20) UNSIGNED NOT NULL , `cid` INT(11) UNSIGNED NOT NULL , PRIMARY KEY (`uid`, `cid`) ) ENGINE = InnoDB;
ALTER TABLE `user_character` ADD INDEX (`uid`) COMMENT '';
CREATE TABLE `user_group_character` ( `gid` INT(11) UNSIGNED NOT NULL , `cid` INT(11) UNSIGNED NOT NULL , PRIMARY KEY (`gid`, `cid`) ) ENGINE = InnoDB;
ALTER TABLE `user_group_character` ADD UNIQUE (`gid`) COMMENT '';
ALTER TABLE `user_group_character` DROP INDEX `gid`, ADD INDEX `gid` (`gid`) COMMENT '';


