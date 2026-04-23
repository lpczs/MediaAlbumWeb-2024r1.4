#
SET FOREIGN_KEY_CHECKS = 0;

#
# DDL START
#

INSERT INTO `ACTIVITYLOG`
	(`id`, `datecreated`, `sessionid`, `orderid`, `userid`, `userlogin`, `username`, `importance`, `sectioncode`, `actioncode`, `actionnotes`, `success`)
	VALUES (0, now(), 0, 0, 0, '', (SELECT USER()), 0, 'UPGRADE', '3.2.0b4part1', 'STARTED', 1);


ALTER TABLE `ORDERITEMCOMPONENTS` CHANGE COLUMN `assetservicecode` `externalassetservicecode` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
ALTER TABLE `ORDERITEMCOMPONENTS` CHANGE COLUMN `assetservicename` `externalassetservicename` VARCHAR(1024) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
ALTER TABLE `ORDERITEMCOMPONENTS` CHANGE COLUMN `assetpricetype` `externalassetpricetype` INTEGER NOT NULL DEFAULT 0;
ALTER TABLE `ORDERITEMCOMPONENTS` CHANGE COLUMN `assetcharge` `externalassetcharge` DECIMAL(10,2) NOT NULL DEFAULT '0.00';
ALTER TABLE `ORDERITEMCOMPONENTS` CHANGE COLUMN `assetexpirydate` `externalassetexpirydate` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00';
ALTER TABLE `ORDERITEMCOMPONENTS` CHANGE COLUMN `assetpageref` `externalassetpageref` INTEGER NOT NULL DEFAULT 0;
ALTER TABLE `ORDERITEMCOMPONENTS` CHANGE COLUMN `assetpagenumber` `externalassetpagenumber` INTEGER NOT NULL DEFAULT 0;
ALTER TABLE `ORDERITEMCOMPONENTS` CHANGE COLUMN `assetpagename` `externalassetpagename` VARCHAR(1024) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
ALTER TABLE `ORDERITEMCOMPONENTS` CHANGE COLUMN `assetboxref` `externalassetboxref` INTEGER NOT NULL DEFAULT 0;

ALTER TABLE `ORDERHEADER` ADD INDEX newindex(`designeruuid`);

UPDATE `SYSTEMCONFIG` SET `webversiondate` = '2012-04-11';
UPDATE `SYSTEMCONFIG` SET `webversionnumber` = '3.2.0.6';
UPDATE `SYSTEMCONFIG` SET `webversionstring`= '3.2.0b4';

INSERT INTO `ACTIVITYLOG`
	(`id`, `datecreated`, `sessionid`, `orderid`, `userid`, `userlogin`, `username`, `importance`, `sectioncode`, `actioncode`, `actionnotes`, `success`)
	VALUES (0, now(), 0, 0, 0, '', (SELECT USER()), 0, 'UPGRADE', '3.2.0b4part1', 'FINISHED', 1);

#
# DDL END
#

SET FOREIGN_KEY_CHECKS = 1;
