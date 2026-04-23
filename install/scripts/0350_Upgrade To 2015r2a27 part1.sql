#
SET FOREIGN_KEY_CHECKS = 0;

#
# DDL START
#

INSERT INTO `ACTIVITYLOG`
	(`id`, `datecreated`, `sessionid`, `orderid`, `userid`, `userlogin`, `username`, `importance`, `sectioncode`, `actioncode`, `actionnotes`, `success`)
	VALUES (0, now(), 0, 0, 0, '', (SELECT USER()), 0, 'UPGRADE', '2015r2a27 part1', 'STARTED', 1);

UPDATE `SYSTEMCONFIG` SET `webversiondate` = '2015-07-03';
UPDATE `SYSTEMCONFIG` SET `webversionnumber` = '2015.2.0.27';
UPDATE `SYSTEMCONFIG` SET `webversionstring`= '2015r2a27';

INSERT INTO `ACTIVITYLOG`
(`id`, `datecreated`, `sessionid`, `orderid`, `userid`, `userlogin`, `username`, `importance`, `sectioncode`, `actioncode`, `actionnotes`, `success`)
VALUES (0, now(), 0, 0, 0, '', (SELECT USER()), 0, 'UPGRADE', '2015r2a27 part1', 'FINISHED', 1);


#
# DDL END
#

SET FOREIGN_KEY_CHECKS = 1;