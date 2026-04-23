#
SET FOREIGN_KEY_CHECKS = 0;

#
# DDL START
#

INSERT INTO `ACTIVITYLOG`
	(`id`, `datecreated`, `sessionid`, `orderid`, `userid`, `userlogin`, `username`, `importance`, `sectioncode`, `actioncode`, `actionnotes`, `success`)
	VALUES (0, now(), 0, 0, 0, '', (SELECT USER()), 0, 'UPGRADE', '3.3.0a2 part2', 'STARTED', 1);

UPDATE `SYSTEMCONFIG` SET `webversiondate` = '2012-08-16';
UPDATE `SYSTEMCONFIG` SET `webversionnumber` = '3.3.0.2';
UPDATE `SYSTEMCONFIG` SET `webversionstring`= '3.3.0a2';

ALTER TABLE `ORDERITEMS` ADD COLUMN `projectbuildstartdate` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' AFTER `projectname`,
 ADD COLUMN `projectbuildduration` INTEGER NOT NULL DEFAULT 0 AFTER `projectbuildstartdate`,
 ADD COLUMN `uploaddatasize` INTEGER NOT NULL DEFAULT 0 AFTER `uploadapposversion`,
 ADD COLUMN `uploadduration` INTEGER NOT NULL DEFAULT 0 AFTER `uploaddatasize`;

INSERT INTO `ACTIVITYLOG`
	(`id`, `datecreated`, `sessionid`, `orderid`, `userid`, `userlogin`, `username`, `importance`, `sectioncode`, `actioncode`, `actionnotes`, `success`)
	VALUES (0, now(), 0, 0, 0, '', (SELECT USER()), 0, 'UPGRADE', '3.3.0a2 part2', 'FINISHED', 1);

#
# DDL END
#

SET FOREIGN_KEY_CHECKS = 1;
