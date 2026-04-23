#
SET FOREIGN_KEY_CHECKS = 0;

#
# DDL START
#

INSERT INTO `ACTIVITYLOG`
	(`id`, `datecreated`, `sessionid`, `orderid`, `userid`, `userlogin`, `username`, `importance`, `sectioncode`, `actioncode`, `actionnotes`, `success`)
	VALUES (0, now(), 0, 0, 0, '', (SELECT USER()), 0, 'UPGRADE', '4.0.0a5 part1', 'STARTED', 1);

UPDATE `SYSTEMCONFIG` SET `webversiondate` = '2013-07-25';
UPDATE `SYSTEMCONFIG` SET `webversionnumber` = '4.0.0.5';
UPDATE `SYSTEMCONFIG` SET `webversionstring`= '4.0.0a5';

ALTER TABLE `BRANDING` ADD COLUMN `onlinedesignerlogouturl` VARCHAR(100) NOT NULL DEFAULT '' AFTER `onlinedesignerurl`;

ALTER TABLE `PRODUCTCOLLECTIONLINK` ADD COLUMN `collectionname` VARCHAR(1024) NOT NULL DEFAULT '' AFTER `collectioncode` , 
	ADD COLUMN `productname` VARCHAR(1024) NOT NULL DEFAULT '' AFTER `productcode` , 
	ADD COLUMN `availabledesktop` TINYINT(1) NOT NULL DEFAULT 0  AFTER `productname` , 
	ADD COLUMN `availableonline` TINYINT(1) NOT NULL DEFAULT 0  AFTER `availabledesktop`;

ALTER TABLE `ORDERHEADER` ADD INDEX datelastmodified(`datelastmodified`);
ALTER TABLE `ORDERITEMS` ADD INDEX datelastmodified(`datelastmodified`);

ALTER TABLE `CACHEDATA` DROP INDEX `datacachekey`;

DELETE FROM `CACHEDATA`;

INSERT INTO `ACTIVITYLOG`
	(`id`, `datecreated`, `sessionid`, `orderid`, `userid`, `userlogin`, `username`, `importance`, `sectioncode`, `actioncode`, `actionnotes`, `success`)
	VALUES (0, now(), 0, 0, 0, '', (SELECT USER()), 0, 'UPGRADE', '4.0.0a5 part1', 'FINISHED', 1);

#
# DDL END
#

SET FOREIGN_KEY_CHECKS = 1;
