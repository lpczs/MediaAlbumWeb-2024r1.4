#
SET FOREIGN_KEY_CHECKS = 0;

#
# DDL START
#

INSERT INTO `ACTIVITYLOG`
	(`id`, `datecreated`, `sessionid`, `orderid`, `userid`, `userlogin`, `username`, `importance`, `sectioncode`, `actioncode`, `actionnotes`, `success`)
	VALUES (0, now(), 0, 0, 0, '', (SELECT USER()), 0, 'UPGRADE', '3.0.0a8', 'STARTED', 1);

ALTER TABLE `METADATA` ADD COLUMN `orderitemcomponentid` INTEGER(1) NOT NULL DEFAULT 0 AFTER `orderitemid`;

UPDATE `SYSTEMCONFIG` SET `webversiondate` = '2011-09-23';
UPDATE `SYSTEMCONFIG` SET `webversionnumber` = '3.0.0.8';
UPDATE `SYSTEMCONFIG` SET `webversionstring`= '3.0.0a8';

INSERT INTO `ACTIVITYLOG`
	(`id`, `datecreated`, `sessionid`, `orderid`, `userid`, `userlogin`, `username`, `importance`, `sectioncode`, `actioncode`, `actionnotes`, `success`)
	VALUES (0, now(), 0, 0, 0, '', (SELECT USER()), 0, 'UPGRADE', '3.0.0a8', 'FINISHED', 1);

#
# DDL END
#

SET FOREIGN_KEY_CHECKS = 1;
