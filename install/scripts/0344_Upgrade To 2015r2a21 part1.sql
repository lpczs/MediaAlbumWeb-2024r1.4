#
SET FOREIGN_KEY_CHECKS = 0;

#
# DDL START
#

INSERT INTO `ACTIVITYLOG`
	(`id`, `datecreated`, `sessionid`, `orderid`, `userid`, `userlogin`, `username`, `importance`, `sectioncode`, `actioncode`, `actionnotes`, `success`)
	VALUES (0, now(), 0, 0, 0, '', (SELECT USER()), 0, 'UPGRADE', '2015r2a21 part1', 'STARTED', 1);

UPDATE `SYSTEMCONFIG` SET `webversiondate` = '2015-06-19';
UPDATE `SYSTEMCONFIG` SET `webversionnumber` = '2015.2.0.21';
UPDATE `SYSTEMCONFIG` SET `webversionstring`= '2015r2a21';


INSERT INTO `ACTIVITYLOG`
(`id`, `datecreated`, `sessionid`, `orderid`, `userid`, `userlogin`, `username`, `importance`, `sectioncode`, `actioncode`, `actionnotes`, `success`)
VALUES (0, now(), 0, 0, 0, '', (SELECT USER()), 0, 'UPGRADE', '2015r2a21 part1', 'FINISHED', 1);


#
# DDL END
#

SET FOREIGN_KEY_CHECKS = 1;