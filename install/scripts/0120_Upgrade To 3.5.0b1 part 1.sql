#
SET FOREIGN_KEY_CHECKS = 0;

#
# DDL START
#

INSERT INTO `ACTIVITYLOG`
	(`id`, `datecreated`, `sessionid`, `orderid`, `userid`, `userlogin`, `username`, `importance`, `sectioncode`, `actioncode`, `actionnotes`, `success`)
	VALUES (0, now(), 0, 0, 0, '', (SELECT USER()), 0, 'UPGRADE', '3.5.0b1 part1', 'STARTED', 1);

UPDATE `SYSTEMCONFIG` SET `webversiondate` = '2013-02-20';
UPDATE `SYSTEMCONFIG` SET `webversionnumber` = '3.5.0.3';
UPDATE `SYSTEMCONFIG` SET `webversionstring`= '3.5.0b1';

ALTER TABLE `EVENTS` ADD COLUMN `orderheaderid` INTEGER NOT NULL DEFAULT 0 AFTER `param8`,
 ADD COLUMN `orderitemid` INTEGER NOT NULL DEFAULT 0 AFTER `orderheaderid`,
 ADD COLUMN `userid` INTEGER NOT NULL DEFAULT 0 AFTER `orderitemid`;

INSERT INTO `ACTIVITYLOG`
	(`id`, `datecreated`, `sessionid`, `orderid`, `userid`, `userlogin`, `username`, `importance`, `sectioncode`, `actioncode`, `actionnotes`, `success`)
	VALUES (0, now(), 0, 0, 0, '', (SELECT USER()), 0, 'UPGRADE', '3.5.0b1 part1', 'FINISHED', 1);

#
# DDL END
#

SET FOREIGN_KEY_CHECKS = 1;
