#
SET FOREIGN_KEY_CHECKS = 0;

#
# DDL START
#

INSERT INTO `ACTIVITYLOG`
	(`id`, `datecreated`, `sessionid`, `orderid`, `userid`, `userlogin`, `username`, `importance`, `sectioncode`, `actioncode`, `actionnotes`, `success`)
	VALUES (0, now(), 0, 0, 0, '', (SELECT USER()), 0, 'UPGRADE', '2016r2a10', 'STARTED', 1);

UPDATE `SYSTEMCONFIG` SET `webversiondate` = '2016-03-29';
UPDATE `SYSTEMCONFIG` SET `webversionnumber` = '2016.2.0.10';
UPDATE `SYSTEMCONFIG` SET `webversionstring`= '2016r2a10';

ALTER TABLE `BRANDING` ADD COLUMN `onlinedesignerusemultilineworkflow` TINYINT(1) NOT NULL DEFAULT 0 AFTER `onlinedataretentionpolicy`;

INSERT INTO `ACTIVITYLOG`
	(`id`, `datecreated`, `sessionid`, `orderid`, `userid`, `userlogin`, `username`, `importance`, `sectioncode`, `actioncode`, `actionnotes`, `success`)
	VALUES (0, now(), 0, 0, 0, '', (SELECT USER()), 0, 'UPGRADE', '2016r2a10', 'FINISHED', 1);

#
# DDL END
#

SET FOREIGN_KEY_CHECKS = 1;