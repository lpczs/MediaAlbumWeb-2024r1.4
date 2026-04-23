#
SET FOREIGN_KEY_CHECKS = 0;

#
# DDL START
#

INSERT INTO `ACTIVITYLOG`
	(`id`, `datecreated`, `sessionid`, `orderid`, `userid`, `userlogin`, `username`, `importance`, `sectioncode`, `actioncode`, `actionnotes`, `success`)
	VALUES (0, now(), 0, 0, 0, '', (SELECT USER()), 0, 'UPGRADE', '3.2.0b5part1', 'STARTED', 1);

ALTER TABLE `ORDERHEADER` MODIFY COLUMN `temporder` TINYINT(1) NOT NULL DEFAULT 0;

ALTER TABLE `ORDERHEADER` ADD COLUMN `offlineorder` TINYINT(1) NOT NULL DEFAULT 0 AFTER `temporderexpirydate`;

UPDATE `ORDERHEADER` SET `offlineorder` = 0;

ALTER TABLE `BRANDING` ADD COLUMN `datelastmodified` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER `datecreated`;

UPDATE `SYSTEMCONFIG` SET `webversiondate` = '2012-04-17';
UPDATE `SYSTEMCONFIG` SET `webversionnumber` = '3.2.0.7';
UPDATE `SYSTEMCONFIG` SET `webversionstring`= '3.2.0b5';

INSERT INTO `ACTIVITYLOG`
	(`id`, `datecreated`, `sessionid`, `orderid`, `userid`, `userlogin`, `username`, `importance`, `sectioncode`, `actioncode`, `actionnotes`, `success`)
	VALUES (0, now(), 0, 0, 0, '', (SELECT USER()), 0, 'UPGRADE', '3.2.0b5part1', 'FINISHED', 1);

#
# DDL END
#

SET FOREIGN_KEY_CHECKS = 1;
