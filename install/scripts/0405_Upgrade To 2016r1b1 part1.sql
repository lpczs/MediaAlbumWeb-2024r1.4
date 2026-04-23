#
SET FOREIGN_KEY_CHECKS = 0;

#
# DDL START
#

INSERT INTO `ACTIVITYLOG`
	(`id`, `datecreated`, `sessionid`, `orderid`, `userid`, `userlogin`, `username`, `importance`, `sectioncode`, `actioncode`, `actionnotes`, `success`)
	VALUES (0, now(), 0, 0, 0, '', (SELECT USER()), 0, 'UPGRADE', '2016r1b1', 'STARTED', 1);

UPDATE `SYSTEMCONFIG` SET `webversiondate` = '2016-01-29';
UPDATE `SYSTEMCONFIG` SET `webversionnumber` = '2016.1.0.31';
UPDATE `SYSTEMCONFIG` SET `webversionstring`= '2016r1b1';

CREATE TABLE `ONLINEBASKET` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `datecreated` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `webbrandcode` varchar(50) NOT NULL DEFAULT '',
  `groupcode` varchar(50) NOT NULL DEFAULT '',
  `basketref` varchar(100) NOT NULL DEFAULT '',
  `token` varchar(100) NOT NULL DEFAULT '',
  `basketexpiredate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `projectref` varchar(50) NOT NULL DEFAULT '',
  `userid` int(11) NOT NULL DEFAULT '0',
  `projectname` varchar(200) NOT NULL DEFAULT '',
  `collectioncode` varchar(50) NOT NULL DEFAULT '',
  `collectionname` varchar(1024) NOT NULL DEFAULT '',
  `layoutcode` varchar(50) NOT NULL DEFAULT '',
  `layoutname` varchar(1024) NOT NULL DEFAULT '',
  `projectdata` mediumblob NOT NULL,
  `projectdatalength` int(11) NOT NULL DEFAULT '0',
  `inbasket` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `basketref` (`basketref`),
  KEY `projectref` (`projectref`),
  KEY `userid` (`userid`),
  KEY `token` (`token`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `SESSIONDATA`
ADD COLUMN `basketref` VARCHAR(100) NOT NULL DEFAULT '' AFTER `giftcardtotal`,
ADD INDEX `basketref` (`basketref` ASC);

ALTER TABLE `LICENSEKEYS` ADD COLUMN `maxorderbatchsize` INT(11) NOT NULL DEFAULT 1 AFTER `onlinedesignerguestworkflowmode`;

INSERT INTO `ACTIVITYLOG`
	(`id`, `datecreated`, `sessionid`, `orderid`, `userid`, `userlogin`, `username`, `importance`, `sectioncode`, `actioncode`, `actionnotes`, `success`)
	VALUES (0, now(), 0, 0, 0, '', (SELECT USER()), 0, 'UPGRADE', '2016r1b1', 'FINISHED', 1);

#
# DDL END
#

SET FOREIGN_KEY_CHECKS = 1;