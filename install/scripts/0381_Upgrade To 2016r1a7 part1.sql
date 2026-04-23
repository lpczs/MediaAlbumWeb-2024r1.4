#
SET FOREIGN_KEY_CHECKS = 0;

#
# DDL START
#

INSERT INTO `ACTIVITYLOG`
	(`id`, `datecreated`, `sessionid`, `orderid`, `userid`, `userlogin`, `username`, `importance`, `sectioncode`, `actioncode`, `actionnotes`, `success`)
	VALUES (0, now(), 0, 0, 0, '', (SELECT USER()), 0, 'UPGRADE', '2016r1a7', 'STARTED', 1);

UPDATE `SYSTEMCONFIG` SET `webversiondate` = '2015-10-23';
UPDATE `SYSTEMCONFIG` SET `webversionnumber` = '2016.1.0.7';
UPDATE `SYSTEMCONFIG` SET `webversionstring`= '2016r1a7';

ALTER TABLE `SESSIONDATA` CHANGE COLUMN `sessionarraydata` `sessionarraydata` MEDIUMBLOB NOT NULL;

INSERT INTO `ACTIVITYLOG`
(`id`, `datecreated`, `sessionid`, `orderid`, `userid`, `userlogin`, `username`, `importance`, `sectioncode`, `actioncode`, `actionnotes`, `success`)
VALUES (0, now(), 0, 0, 0, '', (SELECT USER()), 0, 'UPGRADE', '2016r1a7', 'FINISHED', 1);


#
# DDL END
#

SET FOREIGN_KEY_CHECKS = 1;