#
SET FOREIGN_KEY_CHECKS = 0;

#
# DDL START
#

INSERT INTO `ACTIVITYLOG`
	(`id`, `datecreated`, `sessionid`, `orderid`, `userid`, `userlogin`, `username`, `importance`, `sectioncode`, `actioncode`, `actionnotes`, `success`)
	VALUES (0, now(), 0, 0, 0, '', (SELECT USER()), 0, 'UPGRADE', '4.2.0a8 part1', 'STARTED', 1);

UPDATE `SYSTEMCONFIG` SET `webversiondate` = '2013-12-16';
UPDATE `SYSTEMCONFIG` SET `webversionnumber` = '4.2.0.8';
UPDATE `SYSTEMCONFIG` SET `webversionstring`= '4.2.0a8';

ALTER TABLE `PRODUCTCOLLECTIONLINK` ADD COLUMN `hasbeenavailabledesktop` TINYINT(1) NOT NULL DEFAULT 0 AFTER `availabledesktop`;

UPDATE `PRODUCTCOLLECTIONLINK` SET `availabledesktop` = 1 WHERE (`availabledesktop` = 0) AND (`availableonline` = 0);

UPDATE `PRODUCTCOLLECTIONLINK` SET `hasbeenavailabledesktop` = `availabledesktop`;

DELETE FROM `CACHEDATA` where `datacachekey` LIKE '%..';

INSERT INTO `ACTIVITYLOG`
	(`id`, `datecreated`, `sessionid`, `orderid`, `userid`, `userlogin`, `username`, `importance`, `sectioncode`, `actioncode`, `actionnotes`, `success`)
	VALUES (0, now(), 0, 0, 0, '', (SELECT USER()), 0, 'UPGRADE', '4.2.0a8 part1', 'FINISHED', 1);

#
# DDL END
#

SET FOREIGN_KEY_CHECKS = 1;
