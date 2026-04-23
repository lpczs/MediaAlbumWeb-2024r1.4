#
SET FOREIGN_KEY_CHECKS = 0;

#
# DDL START
#

INSERT INTO `ACTIVITYLOG`
	(`id`, `datecreated`, `sessionid`, `orderid`, `userid`, `userlogin`, `username`, `importance`, `sectioncode`, `actioncode`, `actionnotes`, `success`)
	VALUES (0, now(), 0, 0, 0, '', (SELECT USER()), 0, 'UPGRADE', '3.0.0RC1', 'STARTED', 1);

ALTER TABLE `ORDERITEMCOMPONENTS` ADD INDEX orderitemid(`orderitemid`);

UPDATE `SYSTEMCONFIG` SET `webversiondate` = '2011-10-01';
UPDATE `SYSTEMCONFIG` SET `webversionnumber` = '3.0.0.10';
UPDATE `SYSTEMCONFIG` SET `webversionstring`= '3.0.0RC1';

INSERT INTO `ACTIVITYLOG`
	(`id`, `datecreated`, `sessionid`, `orderid`, `userid`, `userlogin`, `username`, `importance`, `sectioncode`, `actioncode`, `actionnotes`, `success`)
	VALUES (0, now(), 0, 0, 0, '', (SELECT USER()), 0, 'UPGRADE', '3.0.0RC1', 'FINISHED', 1);

#
# DDL END
#

SET FOREIGN_KEY_CHECKS = 1;
