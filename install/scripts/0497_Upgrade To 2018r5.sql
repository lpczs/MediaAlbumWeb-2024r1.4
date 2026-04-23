#
SET FOREIGN_KEY_CHECKS = 0;

#
# DDL START
#

UPDATE `SYSTEMCONFIG` SET `webversiondate` = '2018-11-13';
UPDATE `SYSTEMCONFIG` SET `webversionnumber` = '2018.5.0';
UPDATE `SYSTEMCONFIG` SET `webversionstring`= '2018r5';


CREATE TABLE `USERPASSWORDREQUESTS` (
  `id` INT(11) UNSIGNED NOT NULL auto_increment,
  `datecreated` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  `expirytime` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  `userid` INT(11) NOT NULL DEFAULT 0,
  `token` VARCHAR(255) NOT NULL DEFAULT '',
  `authenticationcode` VARCHAR(10) NOT NULL DEFAULT '',
  `returninformation` VARCHAR(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  INDEX `expirytime` (`expirytime` ASC),
  INDEX `userid` (`userid` ASC),
  INDEX `token` (`token` ASC)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



CREATE TABLE `BRANDASSETLINK` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `brandid` int(11) unsigned NOT NULL,
  `assetdataid` int(11) unsigned NOT NULL,
  `objecttype` tinyint(3) unsigned NOT NULL,
  `enabled` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `link` (`brandid`,`assetdataid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

#
# DDL END
#

SET FOREIGN_KEY_CHECKS = 1;

