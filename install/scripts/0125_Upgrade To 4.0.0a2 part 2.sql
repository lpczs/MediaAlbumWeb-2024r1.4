#
SET FOREIGN_KEY_CHECKS = 0;

#
# DDL START
#

INSERT INTO `ACTIVITYLOG`
	(`id`, `datecreated`, `sessionid`, `orderid`, `userid`, `userlogin`, `username`, `importance`, `sectioncode`, `actioncode`, `actionnotes`, `success`)
	VALUES (0, now(), 0, 0, 0, '', (SELECT USER()), 0, 'UPGRADE', '4.0.0a2 part2', 'STARTED', 1);

UPDATE `SYSTEMCONFIG` SET `webversiondate` = '2013-07-04';
UPDATE `SYSTEMCONFIG` SET `webversionnumber` = '4.0.0.2';
UPDATE `SYSTEMCONFIG` SET `webversionstring`= '4.0.0a2';

ALTER TABLE `CACHEDATA`
  DROP `productcode`,
  DROP `productcollectioncode`;

ALTER TABLE `CACHEDATA` ADD `serializeddatalength` INT(11) NOT NULL DEFAULT 0 AFTER `cachedata`;

INSERT INTO `ACTIVITYLOG`
	(`id`, `datecreated`, `sessionid`, `orderid`, `userid`, `userlogin`, `username`, `importance`, `sectioncode`, `actioncode`, `actionnotes`, `success`)
	VALUES (0, now(), 0, 0, 0, '', (SELECT USER()), 0, 'UPGRADE', '4.0.0a2 part2', 'FINISHED', 1);

#
# DDL END
#

SET FOREIGN_KEY_CHECKS = 1;
