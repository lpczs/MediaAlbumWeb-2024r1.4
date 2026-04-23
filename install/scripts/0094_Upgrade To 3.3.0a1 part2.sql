#
SET FOREIGN_KEY_CHECKS = 0;

#
# DDL START
#

INSERT INTO `ACTIVITYLOG`
	(`id`, `datecreated`, `sessionid`, `orderid`, `userid`, `userlogin`, `username`, `importance`, `sectioncode`, `actioncode`, `actionnotes`, `success`)
	VALUES (0, now(), 0, 0, 0, '', (SELECT USER()), 0, 'UPGRADE', '3.3.0a1 part2', 'STARTED', 1);

UPDATE `SYSTEMCONFIG` SET `webversiondate` = '2012-08-07';
UPDATE `SYSTEMCONFIG` SET `webversionnumber` = '3.3.0.1';
UPDATE `SYSTEMCONFIG` SET `webversionstring`= '3.3.0a1';

ALTER TABLE `TAXZONES` CHANGE COLUMN `taxcode` `taxlevel1` VARCHAR(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
ALTER TABLE `TAXZONES` ADD COLUMN `taxlevel2` VARCHAR(20) NOT NULL AFTER `taxlevel1`;
ALTER TABLE `TAXZONES` ADD COLUMN `taxlevel3` VARCHAR(20) NOT NULL AFTER `taxlevel2`;
ALTER TABLE `TAXZONES` ADD COLUMN `taxlevel4` VARCHAR(20) NOT NULL AFTER `taxlevel3`;
ALTER TABLE `TAXZONES` ADD COLUMN `taxlevel5` VARCHAR(20) NOT NULL AFTER `taxlevel4`;

ALTER TABLE `PRODUCTS` ADD COLUMN `taxlevel` INT NOT NULL DEFAULT 1 AFTER `type`;

ALTER TABLE `PRODUCTS` DROP COLUMN `usedefaulttaxrate`;
ALTER TABLE `PRODUCTS` DROP COLUMN `taxratecode`;

ALTER TABLE `COMPONENTS` ADD COLUMN `orderfootertaxlevel` INT NOT NULL DEFAULT 1 AFTER `orderfooterusesproductquantity`;


INSERT INTO `ACTIVITYLOG`
	(`id`, `datecreated`, `sessionid`, `orderid`, `userid`, `userlogin`, `username`, `importance`, `sectioncode`, `actioncode`, `actionnotes`, `success`)
	VALUES (0, now(), 0, 0, 0, '', (SELECT USER()), 0, 'UPGRADE', '3.3.0a1 part2', 'FINISHED', 1);

#
# DDL END
#

SET FOREIGN_KEY_CHECKS = 1;
