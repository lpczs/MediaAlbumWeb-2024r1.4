#
SET FOREIGN_KEY_CHECKS = 0;

#
# DDL START
#

INSERT INTO `ACTIVITYLOG`
	(`id`, `datecreated`, `sessionid`, `orderid`, `userid`, `userlogin`, `username`, `importance`, `sectioncode`, `actioncode`, `actionnotes`, `success`)
	VALUES (0, now(), 0, 0, 0, '', (SELECT USER()), 0, 'UPGRADE', '3.0.1', 'STARTED', 1);

ALTER TABLE `SECTIONS` ADD COLUMN `companycode` VARCHAR(50) NOT NULL AFTER `datecreated`;

ALTER TABLE `BRANDING` ADD COLUMN `previewexpires` INTEGER(1) NOT NULL DEFAULT 0 AFTER `previewlicensekey`;
ALTER TABLE `BRANDING` ADD COLUMN `previewexpiresdays` INTEGER NOT NULL DEFAULT 0 AFTER `previewexpires`;

ALTER TABLE `SHAREDITEMS` ADD COLUMN `datemodified` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' AFTER `datecreated`;

UPDATE `SHAREDITEMS` SET `datemodified` = `datecreated`;

ALTER TABLE `ORDERITEMS` DROP COLUMN `origuploadref`;

ALTER TABLE `ORDERHEADER` ADD COLUMN `designeruuid` VARCHAR(200) NOT NULL AFTER `shoppingcarttype`;

ALTER TABLE `ORDERITEMS` ADD COLUMN `projectref` VARCHAR(255) NOT NULL AFTER `parentorderitemid`;
ALTER TABLE `ORDERITEMS` ADD COLUMN `projectreforig` VARCHAR(255) NOT NULL AFTER `projectref`;

ALTER TABLE `ORDERITEMS` ADD COLUMN `uploadorderid` INTEGER NOT NULL DEFAULT 0 AFTER `userid`;
ALTER TABLE `ORDERITEMS` ADD COLUMN `uploadordernumber` VARCHAR(50) NOT NULL AFTER `uploadorderid`;
ALTER TABLE `ORDERITEMS` ADD COLUMN `uploadorderitemid` INTEGER NOT NULL DEFAULT 0 AFTER `uploadordernumber`;

UPDATE `ORDERITEMS` SET `uploadorderid` = IF(`origorderid` > 0, `origorderid`, `orderid`);
UPDATE `ORDERITEMS` SET `uploadorderitemid` = IF(`origorderitemid` > 0, `origorderitemid`, `id`);
UPDATE `ORDERITEMS` oi SET `oi`.`uploadordernumber` = (SELECT `oh`.`ordernumber` FROM `ORDERHEADER` oh WHERE `oh`.`id` = `oi`.`uploadorderid`);

ALTER TABLE `ORDERITEMS` ADD COLUMN `uploadbatchref` VARCHAR(200) NOT NULL AFTER `uploadorderitemid`;
ALTER TABLE `ORDERITEMS` ADD INDEX uploadbatchref(`uploadbatchref`);
UPDATE `ORDERITEMS` SET `uploadbatchref` = `uploadref`;

UPDATE `SYSTEMCONFIG` SET `webversiondate` = '2011-10-14';
UPDATE `SYSTEMCONFIG` SET `webversionnumber` = '3.0.1';
UPDATE `SYSTEMCONFIG` SET `webversionstring`= '3.0.1';

INSERT INTO `ACTIVITYLOG`
	(`id`, `datecreated`, `sessionid`, `orderid`, `userid`, `userlogin`, `username`, `importance`, `sectioncode`, `actioncode`, `actionnotes`, `success`)
	VALUES (0, now(), 0, 0, 0, '', (SELECT USER()), 0, 'UPGRADE', '3.0.1', 'FINISHED', 1);

#
# DDL END
#

SET FOREIGN_KEY_CHECKS = 1;
