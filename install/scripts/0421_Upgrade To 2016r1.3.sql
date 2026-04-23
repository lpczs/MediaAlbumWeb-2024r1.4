#
SET FOREIGN_KEY_CHECKS = 0;

#
# DDL START
#

INSERT INTO `ACTIVITYLOG`
	(`id`, `datecreated`, `sessionid`, `orderid`, `userid`, `userlogin`, `username`, `importance`, `sectioncode`, `actioncode`, `actionnotes`, `success`)
	VALUES (0, now(), 0, 0, 0, '', (SELECT USER()), 0, 'UPGRADE', '2016r1.3', 'STARTED', 1);

UPDATE `SYSTEMCONFIG` SET `webversiondate` = '2016-03-14';
UPDATE `SYSTEMCONFIG` SET `webversionnumber` = '2016.1.3';
UPDATE `SYSTEMCONFIG` SET `webversionstring`= '2016r1.3';

CREATE TABLE `AUTHENTICATIONDATASTORE` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`datecreated` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
	`expiredate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
	`key` varchar(255) NOT NULL DEFAULT '',
	`data` mediumtext NOT NULL,
	`url` varchar(1000) NOT NULL DEFAULT '',
	`type` TINYINT(1) NOT NULL DEFAULT 0,
	`reason` int(11) NOT NULL DEFAULT 0,
	PRIMARY KEY (`id`),
	KEY `expiredate` (`expiredate`),
	KEY `key` (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


INSERT INTO `ACTIVITYLOG`
	(`id`, `datecreated`, `sessionid`, `orderid`, `userid`, `userlogin`, `username`, `importance`, `sectioncode`, `actioncode`, `actionnotes`, `success`)
	VALUES (0, now(), 0, 0, 0, '', (SELECT USER()), 0, 'UPGRADE', '2016r1.3', 'FINISHED', 1);

#
# DDL END
#

SET FOREIGN_KEY_CHECKS = 1;