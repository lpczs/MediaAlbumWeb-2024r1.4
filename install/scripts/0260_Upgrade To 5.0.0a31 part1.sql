#
SET FOREIGN_KEY_CHECKS = 0;

#
# DDL START
#

INSERT INTO `ACTIVITYLOG`
	(`id`, `datecreated`, `sessionid`, `orderid`, `userid`, `userlogin`, `username`, `importance`, `sectioncode`, `actioncode`, `actionnotes`, `success`)
	VALUES (0, now(), 0, 0, 0, '', (SELECT USER()), 0, 'UPGRADE', '5.0.0a31 part1', 'STARTED', 1);

UPDATE `SYSTEMCONFIG` SET `webversiondate` = '2014-10-01';
UPDATE `SYSTEMCONFIG` SET `webversionnumber` = '5.0.0.31';
UPDATE `SYSTEMCONFIG` SET `webversionstring`= '5.0.0a31';

ALTER TABLE `ORDERITEMCOMPONENTS` ADD COLUMN `setid` INT(11) NOT NULL DEFAULT 0 AFTER `metadatacodelist`;

ALTER TABLE `ORDERITEMCOMPONENTS` ADD COLUMN `setname` VARCHAR(200) NOT NULL DEFAULT '' AFTER `setid`;

ALTER TABLE `APPLICATIONFILES` CHANGE COLUMN `products` `products` VARCHAR(4096) NOT NULL DEFAULT '';

INSERT INTO `ACTIVITYLOG`
(`id`, `datecreated`, `sessionid`, `orderid`, `userid`, `userlogin`, `username`, `importance`, `sectioncode`, `actioncode`, `actionnotes`, `success`)
VALUES (0, now(), 0, 0, 0, '', (SELECT USER()), 0, 'UPGRADE', '5.0.0a31 part1', 'FINISHED', 1);

#
# DDL END
#

SET FOREIGN_KEY_CHECKS = 1;