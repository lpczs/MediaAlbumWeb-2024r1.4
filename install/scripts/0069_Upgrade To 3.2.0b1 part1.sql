#
SET FOREIGN_KEY_CHECKS = 0;

#
# DDL START
#

INSERT INTO `ACTIVITYLOG`
	(`id`, `datecreated`, `sessionid`, `orderid`, `userid`, `userlogin`, `username`, `importance`, `sectioncode`, `actioncode`, `actionnotes`, `success`)
	VALUES (0, now(), 0, 0, 0, '', (SELECT USER()), 0, 'UPGRADE', '3.2.0b1part1', 'STARTED', 1);


ALTER TABLE `ORDERITEMCOMPONENTS` ADD COLUMN `pricingmodel` INTEGER NOT NULL DEFAULT 0 AFTER `sortorder`;

ALTER TABLE `COMPONENTS` ADD COLUMN `orderfooterusesproductquantity` INTEGER NOT NULL DEFAULT 0 AFTER `keywordgroupheaderid`;

UPDATE `SYSTEMCONFIG` SET `webversiondate` = '2012-03-14';
UPDATE `SYSTEMCONFIG` SET `webversionnumber` = '3.2.0.3';
UPDATE `SYSTEMCONFIG` SET `webversionstring`= '3.2.0b1';

INSERT INTO `ACTIVITYLOG`
	(`id`, `datecreated`, `sessionid`, `orderid`, `userid`, `userlogin`, `username`, `importance`, `sectioncode`, `actioncode`, `actionnotes`, `success`)
	VALUES (0, now(), 0, 0, 0, '', (SELECT USER()), 0, 'UPGRADE', '3.2.0b1part1', 'FINISHED', 1);

#
# DDL END
#

SET FOREIGN_KEY_CHECKS = 1;
