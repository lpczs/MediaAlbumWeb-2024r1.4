#
SET FOREIGN_KEY_CHECKS = 0;

#
# DDL START
#

INSERT INTO `ACTIVITYLOG`
	(`id`, `datecreated`, `sessionid`, `orderid`, `userid`, `userlogin`, `username`, `importance`, `sectioncode`, `actioncode`, `actionnotes`, `success`)
	VALUES (0, now(), 0, 0, 0, '', (SELECT USER()), 0, 'UPGRADE', '4.2.0a4 part1', 'STARTED', 1);

UPDATE `SYSTEMCONFIG` SET `webversiondate` = '2013-11-28';
UPDATE `SYSTEMCONFIG` SET `webversionnumber` = '4.2.0.4';
UPDATE `SYSTEMCONFIG` SET `webversionstring`= '4.2.0a4';

ALTER TABLE `VOUCHERS` CHANGE COLUMN `defaultdiscount` `defaultdiscount` TINYINT NOT NULL DEFAULT '0';

ALTER TABLE `VOUCHERS` DROP INDEX `newindex`;

ALTER TABLE `VOUCHERS` ADD INDEX `promotioncode` (`promotioncode` ASC);

ALTER TABLE `VOUCHERS` ADD INDEX `defaultdiscount` (`defaultdiscount` ASC);

INSERT INTO `ACTIVITYLOG`
	(`id`, `datecreated`, `sessionid`, `orderid`, `userid`, `userlogin`, `username`, `importance`, `sectioncode`, `actioncode`, `actionnotes`, `success`)
	VALUES (0, now(), 0, 0, 0, '', (SELECT USER()), 0, 'UPGRADE', '4.2.0a4 part1', 'FINISHED', 1);

#
# DDL END
#

SET FOREIGN_KEY_CHECKS = 1;
