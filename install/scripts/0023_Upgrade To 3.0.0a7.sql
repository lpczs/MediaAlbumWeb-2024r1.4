#
SET FOREIGN_KEY_CHECKS = 0;

#
# DDL START
#

INSERT INTO `ACTIVITYLOG`
	(`id`, `datecreated`, `sessionid`, `orderid`, `userid`, `userlogin`, `username`, `importance`, `sectioncode`, `actioncode`, `actionnotes`, `success`)
	VALUES (0, now(), 0, 0, 0, '', (SELECT USER()), 0, 'UPGRADE', '3.0.0a7', 'STARTED', 1);

ALTER TABLE `SECTIONS` ADD COLUMN `deleted` TINYINT(1) NOT NULL DEFAULT 0 AFTER `active`;

ALTER TABLE `COMPONENTCATEGORIES` ADD COLUMN `componentpricingdecimalplaces` INTEGER NOT NULL DEFAULT 2 AFTER `requirespagecount`;

ALTER TABLE `EVENTS` MODIFY COLUMN `statusmessage` VARCHAR(200) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;

UPDATE `SYSTEMCONFIG` SET `webversiondate` = '2011-09-08';
UPDATE `SYSTEMCONFIG` SET `webversionnumber` = '3.0.0.7';
UPDATE `SYSTEMCONFIG` SET `webversionstring`= '3.0.0a7';

INSERT INTO `ACTIVITYLOG`
	(`id`, `datecreated`, `sessionid`, `orderid`, `userid`, `userlogin`, `username`, `importance`, `sectioncode`, `actioncode`, `actionnotes`, `success`)
	VALUES (0, now(), 0, 0, 0, '', (SELECT USER()), 0, 'UPGRADE', '3.0.0a7', 'FINISHED', 1);

#
# DDL END
#

SET FOREIGN_KEY_CHECKS = 1;
