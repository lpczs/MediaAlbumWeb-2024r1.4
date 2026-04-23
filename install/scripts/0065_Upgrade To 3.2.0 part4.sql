#
SET FOREIGN_KEY_CHECKS = 0;

#
# DDL START
#

INSERT INTO `ACTIVITYLOG`
	(`id`, `datecreated`, `sessionid`, `orderid`, `userid`, `userlogin`, `username`, `importance`, `sectioncode`, `actioncode`, `actionnotes`, `success`)
	VALUES (0, now(), 0, 0, 0, '', (SELECT USER()), 0, 'UPGRADE', '3.2.0a1part4', 'STARTED', 1);

ALTER TABLE `ORDERSHIPPING` DROP COLUMN `orderitemid`;

ALTER TABLE `OUTPUTFORMATS` CHANGE COLUMN `type` `pagestype` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
ADD COLUMN `cover1type` VARCHAR(50) NOT NULL AFTER `pagestype`,
ADD COLUMN `cover2type` VARCHAR(50) NOT NULL AFTER `cover1type`;

UPDATE `OUTPUTFORMATS` SET `cover1type` = `pagestype`, `cover2type` = `pagestype`;

UPDATE `OUTPUTFORMATS` SET `cover1type` = "PDFMULTIPAGE", `cover2type` = "PDFMULTIPAGE", `cover1separatefile` = 1, `cover2separatefile` = 1, `cover2outputwithcover1` = 0 WHERE `pagestype` = "PDFSINGLEPAGE";

ALTER TABLE `COMPONENTS` ADD COLUMN `datelastmodified` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' AFTER `datecreated`;

UPDATE `COMPONENTS` SET `datelastmodified` = `datecreated`;

ALTER TABLE `OUTPUTFORMATSPRODUCTLINK` ADD COLUMN `componentcode` VARCHAR(152) NOT NULL AFTER `productcode`;

ALTER TABLE `OUTPUTFORMATS` ADD COLUMN `bleedoverlapwidth` VARCHAR(15) NOT NULL AFTER `cropmarkcolour`;

UPDATE `OUTPUTFORMATS` SET `bleedoverlapwidth` = 0;

UPDATE `SYSTEMCONFIG` SET `webversiondate` = '2012-02-08';
UPDATE `SYSTEMCONFIG` SET `webversionnumber` = '3.2.0.1';
UPDATE `SYSTEMCONFIG` SET `webversionstring`= '3.2.0a1';

INSERT INTO `ACTIVITYLOG`
	(`id`, `datecreated`, `sessionid`, `orderid`, `userid`, `userlogin`, `username`, `importance`, `sectioncode`, `actioncode`, `actionnotes`, `success`)
	VALUES (0, now(), 0, 0, 0, '', (SELECT USER()), 0, 'UPGRADE', '3.2.0a1part4', 'FINISHED', 1);

#
# DDL END
#

SET FOREIGN_KEY_CHECKS = 1;
