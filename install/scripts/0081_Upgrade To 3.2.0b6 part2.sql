#
SET FOREIGN_KEY_CHECKS = 0;

#
# DDL START
#

INSERT INTO `ACTIVITYLOG`
	(`id`, `datecreated`, `sessionid`, `orderid`, `userid`, `userlogin`, `username`, `importance`, `sectioncode`, `actioncode`, `actionnotes`, `success`)
	VALUES (0, now(), 0, 0, 0, '', (SELECT USER()), 0, 'UPGRADE', '3.2.0b6part2', 'STARTED', 1);
	
ALTER TABLE `ORDERITEMCOMPONENTS` CHANGE COLUMN `externalassetcharge` `externalassetunitsell` DECIMAL(10,2) NOT NULL DEFAULT '0.00';
ALTER TABLE `ORDERITEMCOMPONENTS` ADD COLUMN `externalassetunitcost` DECIMAL(10,2) NOT NULL DEFAULT '0.00' AFTER `componentunitsell`;

UPDATE `SYSTEMCONFIG` SET `webversiondate` = '2012-04-24';
UPDATE `SYSTEMCONFIG` SET `webversionnumber` = '3.2.0.8';
UPDATE `SYSTEMCONFIG` SET `webversionstring`= '3.2.0b6';

INSERT INTO `ACTIVITYLOG`
	(`id`, `datecreated`, `sessionid`, `orderid`, `userid`, `userlogin`, `username`, `importance`, `sectioncode`, `actioncode`, `actionnotes`, `success`)
	VALUES (0, now(), 0, 0, 0, '', (SELECT USER()), 0, 'UPGRADE', '3.2.0b6part2', 'FINISHED', 1);

#
# DDL END
#

SET FOREIGN_KEY_CHECKS = 1;
