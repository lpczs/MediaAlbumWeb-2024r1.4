#
SET FOREIGN_KEY_CHECKS = 0;

#
# DDL START
#

INSERT INTO `ACTIVITYLOG`
	(`id`, `datecreated`, `sessionid`, `orderid`, `userid`, `userlogin`, `username`, `importance`, `sectioncode`, `actioncode`, `actionnotes`, `success`)
	VALUES (0, now(), 0, 0, 0, '', (SELECT USER()), 0, 'UPGRADE', '3.0.2a6part6', 'STARTED', 1);

ALTER TABLE `keywordgroupheader` MODIFY COLUMN `productcodes` VARCHAR(16384) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;

UPDATE `SYSTEMCONFIG` SET `webversiondate` = '2011-11-18';
UPDATE `SYSTEMCONFIG` SET `webversionnumber` = '3.0.2.6';
UPDATE `SYSTEMCONFIG` SET `webversionstring`= '3.0.2a6';

INSERT INTO `ACTIVITYLOG`
	(`id`, `datecreated`, `sessionid`, `orderid`, `userid`, `userlogin`, `username`, `importance`, `sectioncode`, `actioncode`, `actionnotes`, `success`)
	VALUES (0, now(), 0, 0, 0, '', (SELECT USER()), 0, 'UPGRADE', '3.0.2a6part6', 'FINISHED', 1);

#
# DDL END
#

SET FOREIGN_KEY_CHECKS = 1;
