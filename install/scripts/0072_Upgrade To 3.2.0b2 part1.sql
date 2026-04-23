#
SET FOREIGN_KEY_CHECKS = 0;

#
# DDL START
#

INSERT INTO `ACTIVITYLOG`
	(`id`, `datecreated`, `sessionid`, `orderid`, `userid`, `userlogin`, `username`, `importance`, `sectioncode`, `actioncode`, `actionnotes`, `success`)
	VALUES (0, now(), 0, 0, 0, '', (SELECT USER()), 0, 'UPGRADE', '3.2.0b2part1', 'STARTED', 1);
	
ALTER TABLE `COMPANIES` ADD COLUMN `licensedata1` VARCHAR(200) NOT NULL AFTER `ipaccesslist`,
ADD COLUMN `licensedata2` VARCHAR(200) NOT NULL AFTER `licensedata1`;

ALTER TABLE `COMPANIES` ADD COLUMN `licensedatadate` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' AFTER `ipaccesslist`;

UPDATE `SYSTEMCONFIG` SET `webversiondate` = '2012-03-21';
UPDATE `SYSTEMCONFIG` SET `webversionnumber` = '3.2.0.4';
UPDATE `SYSTEMCONFIG` SET `webversionstring`= '3.2.0b2';

INSERT INTO `ACTIVITYLOG`
	(`id`, `datecreated`, `sessionid`, `orderid`, `userid`, `userlogin`, `username`, `importance`, `sectioncode`, `actioncode`, `actionnotes`, `success`)
	VALUES (0, now(), 0, 0, 0, '', (SELECT USER()), 0, 'UPGRADE', '3.2.0b2part1', 'FINISHED', 1);

#
# DDL END
#

SET FOREIGN_KEY_CHECKS = 1;
