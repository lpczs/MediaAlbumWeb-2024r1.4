#
SET FOREIGN_KEY_CHECKS = 0;

#
# DDL START
#

INSERT INTO `ACTIVITYLOG`
	(`id`, `datecreated`, `sessionid`, `orderid`, `userid`, `userlogin`, `username`, `importance`, `sectioncode`, `actioncode`, `actionnotes`, `success`)
	VALUES (0, now(), 0, 0, 0, '', (SELECT USER()), 0, 'UPGRADE', '2016r1a24', 'STARTED', 1);

UPDATE `SYSTEMCONFIG` SET `webversiondate` = '2015-12-08';
UPDATE `SYSTEMCONFIG` SET `webversionnumber` = '2016.1.0.24';
UPDATE `SYSTEMCONFIG` SET `webversionstring`= '2016r1a24';

ALTER TABLE `COMPONENTS` ADD INDEX `keywordgroupheaderid` (`keywordgroupheaderid`);


INSERT INTO `ACTIVITYLOG`
	(`id`, `datecreated`, `sessionid`, `orderid`, `userid`, `userlogin`, `username`, `importance`, `sectioncode`, `actioncode`, `actionnotes`, `success`)
	VALUES (0, now(), 0, 0, 0, '', (SELECT USER()), 0, 'UPGRADE', '2016r1a24', 'FINISHED', 1);

#
# DDL END
#

SET FOREIGN_KEY_CHECKS = 1;