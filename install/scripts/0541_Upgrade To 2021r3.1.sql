#
SET FOREIGN_KEY_CHECKS = 0;

#
# DDL START
#

UPDATE `SYSTEMCONFIG` SET `webversiondate` = '2021-10-19';
UPDATE `SYSTEMCONFIG` SET `webversionnumber` = '2021.3.1';
UPDATE `SYSTEMCONFIG` SET `webversionstring`= '2021r3.1';

CREATE TABLE `productgroupheader` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `datecreated` DATETIME NOT NULL DEFAULT "0000-00-00 00:00:00",
  `companycode` VARCHAR(50) NOT NULL DEFAULT "",
  `name` VARCHAR(1024) NOT NULL DEFAULT "",
  `active` TINYINT(1) NOT NULL DEFAULT 0,
  UNIQUE INDEX `name` (`name` ASC) VISIBLE,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `productgroupproducts` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `datecreated` DATETIME NULL DEFAULT "0000-00-00 00:00:00",
  `productgroupid` INT(11) UNSIGNED NOT NULL DEFAULT 0,
  `collectioncode` VARCHAR(50) NOT NULL DEFAULT "",
  `productcode` VARCHAR(50) NOT NULL DEFAULT "",
  PRIMARY KEY (`id`),
  INDEX `productgroupid_collectioncode_productcode` (`productgroupid` ASC, `collectioncode` ASC, `productcode` ASC) VISIBLE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `productgrouplink` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `datecreated` DATETIME NOT NULL DEFAULT "0000-00-00 00:00:00",
  `productgroupid` INT(11) UNSIGNED NOT NULL DEFAULT 0,
  `assigneecode` VARCHAR(50) NOT NULL DEFAULT "",
  `assigneetype` TINYINT(3) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  INDEX `assigneecode_assigneetype` (`assigneetype` ASC, `assigneecode` ASC) VISIBLE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
  
ALTER TABLE `vouchers` 
	ADD COLUMN `hasproductgroup` TINYINT(1) NOT NULL DEFAULT '0' AFTER `userid`;

ALTER TABLE `ONLINEBASKET` ADD `dateofpurge` DATETIME DEFAULT '0000-00-00 00:00:00' NOT NULL;

CREATE TABLE IF NOT EXISTS `USERSYSTEMPREFERENCES` (
  `id` int(11) NOT NULL auto_increment,
  `type` varchar(50) NOT NULL DEFAULT '',
  `userid` int(11) NOT NULL DEFAULT 0,
  `data` blob,
  `datalength` int NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  CONSTRAINT `UC_PREFTYPE_USERID` UNIQUE (`type`,`userid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `CONNECTORSPRODUCTCOLLECTIONLINK` CHANGE COLUMN `metadata` `metadata` MEDIUMBLOB;

#
# DDL END
#

SET FOREIGN_KEY_CHECKS = 1;

