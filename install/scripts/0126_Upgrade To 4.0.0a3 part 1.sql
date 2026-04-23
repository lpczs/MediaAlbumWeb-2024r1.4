#
SET FOREIGN_KEY_CHECKS = 0;

#
# DDL START
#

INSERT INTO `ACTIVITYLOG`
	(`id`, `datecreated`, `sessionid`, `orderid`, `userid`, `userlogin`, `username`, `importance`, `sectioncode`, `actioncode`, `actionnotes`, `success`)
	VALUES (0, now(), 0, 0, 0, '', (SELECT USER()), 0, 'UPGRADE', '4.0.0a3 part1', 'STARTED', 1);

UPDATE `SYSTEMCONFIG` SET `webversiondate` = '2013-07-12';
UPDATE `SYSTEMCONFIG` SET `webversionnumber` = '4.0.0.3';
UPDATE `SYSTEMCONFIG` SET `webversionstring`= '4.0.0a3';

ALTER TABLE `APPLICATIONFILES` ADD COLUMN `hasdesktoplayouts` TINYINT(1) NOT NULL DEFAULT 0 AFTER `hiddenfromuser`, ADD COLUMN `hasonlinelayouts` TINYINT(1) NOT NULL DEFAULT 0 AFTER `hasdesktoplayouts`;

UPDATE `APPLICATIONFILES` SET `hasdesktoplayouts` = 1 WHERE `type` = 0;

INSERT INTO `ACTIVITYLOG`
	(`id`, `datecreated`, `sessionid`, `orderid`, `userid`, `userlogin`, `username`, `importance`, `sectioncode`, `actioncode`, `actionnotes`, `success`)
	VALUES (0, now(), 0, 0, 0, '', (SELECT USER()), 0, 'UPGRADE', '4.0.0a3 part1', 'FINISHED', 1);

#
# DDL END
#

SET FOREIGN_KEY_CHECKS = 1;
