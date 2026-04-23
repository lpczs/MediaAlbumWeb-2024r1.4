#
SET FOREIGN_KEY_CHECKS = 0;

#
# DDL START
#

INSERT INTO `ACTIVITYLOG`
	(`id`, `datecreated`, `sessionid`, `orderid`, `userid`, `userlogin`, `username`, `importance`, `sectioncode`, `actioncode`, `actionnotes`, `success`)
	VALUES (0, now(), 0, 0, 0, '', (SELECT USER()), 0, 'UPGRADE', '4.3.0', 'STARTED', 1);

UPDATE `SYSTEMCONFIG` SET `webversiondate` = '2014-06-11';
UPDATE `SYSTEMCONFIG` SET `webversionnumber` = '4.3.0';
UPDATE `SYSTEMCONFIG` SET `webversionstring`= '4.3.0';

DELETE FROM `CACHEDATA`;

ALTER TABLE `CURRENCIES` CHANGE COLUMN `symbol` `symbol` VARCHAR(5) NOT NULL ;

INSERT INTO `ACTIVITYLOG`
	(`id`, `datecreated`, `sessionid`, `orderid`, `userid`, `userlogin`, `username`, `importance`, `sectioncode`, `actioncode`, `actionnotes`, `success`)
	VALUES (0, now(), 0, 0, 0, '', (SELECT USER()), 0, 'UPGRADE', '4.3.0', 'FINISHED', 1);

#
# DDL END
#

SET FOREIGN_KEY_CHECKS = 1;