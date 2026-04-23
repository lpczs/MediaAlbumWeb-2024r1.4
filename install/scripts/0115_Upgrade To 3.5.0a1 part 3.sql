#
SET FOREIGN_KEY_CHECKS = 0;

#
# DDL START
#

INSERT INTO `ACTIVITYLOG`
	(`id`, `datecreated`, `sessionid`, `orderid`, `userid`, `userlogin`, `username`, `importance`, `sectioncode`, `actioncode`, `actionnotes`, `success`)
	VALUES (0, now(), 0, 0, 0, '', (SELECT USER()), 0, 'UPGRADE', '3.5.0a1 part3', 'STARTED', 1);

UPDATE `SYSTEMCONFIG` SET `webversiondate` = '2012-12-24';
UPDATE `SYSTEMCONFIG` SET `webversionnumber` = '3.5.0.1';
UPDATE `SYSTEMCONFIG` SET `webversionstring`= '3.5.0a1';

ALTER TABLE `EVENTS` CHANGE COLUMN `param1` `param1` MEDIUMBLOB NOT NULL;

INSERT INTO `ACTIVITYLOG`
	(`id`, `datecreated`, `sessionid`, `orderid`, `userid`, `userlogin`, `username`, `importance`, `sectioncode`, `actioncode`, `actionnotes`, `success`)
	VALUES (0, now(), 0, 0, 0, '', (SELECT USER()), 0, 'UPGRADE', '3.5.0a1 part3', 'FINISHED', 1);

#
# DDL END
#

SET FOREIGN_KEY_CHECKS = 1;
