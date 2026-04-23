#
SET FOREIGN_KEY_CHECKS = 0;

#
# DDL START
#

INSERT INTO `ACTIVITYLOG`
	(`id`, `datecreated`, `sessionid`, `orderid`, `userid`, `userlogin`, `username`, `importance`, `sectioncode`, `actioncode`, `actionnotes`, `success`)
	VALUES (0, now(), 0, 0, 0, '', (SELECT USER()), 0, 'UPGRADE', '3.0.0a9', 'STARTED', 1);

ALTER TABLE `PRICELINK` 
 ADD INDEX productcode(`productcode`),
 ADD INDEX componentcode(`componentcode`),
 ADD INDEX pricecompound(`productcode`,`componentcode`,`parentpath`(255));

ALTER TABLE `SHAREDITEMS` ADD INDEX orderid(`orderid`),
 ADD INDEX orderitemid(`orderitemid`);

ALTER TABLE `EVENTS` ADD INDEX taskcode(`taskcode`);

ALTER TABLE `BRANDING` MODIFY COLUMN `active` INT,
 ADD COLUMN `productcategoryassetid` INTEGER NOT NULL DEFAULT 0 AFTER `previewlicensekey`;

ALTER TABLE `BRANDING`
 ADD COLUMN `productcategoryassetversion` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' AFTER `productcategoryassetid`;

UPDATE `SYSTEMCONFIG` SET `webversiondate` = '2011-09-29';
UPDATE `SYSTEMCONFIG` SET `webversionnumber` = '3.0.0.9';
UPDATE `SYSTEMCONFIG` SET `webversionstring`= '3.0.0a9';

INSERT INTO `ACTIVITYLOG`
	(`id`, `datecreated`, `sessionid`, `orderid`, `userid`, `userlogin`, `username`, `importance`, `sectioncode`, `actioncode`, `actionnotes`, `success`)
	VALUES (0, now(), 0, 0, 0, '', (SELECT USER()), 0, 'UPGRADE', '3.0.0a9', 'FINISHED', 1);

#
# DDL END
#

SET FOREIGN_KEY_CHECKS = 1;
