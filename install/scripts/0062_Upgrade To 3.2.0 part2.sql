#
SET FOREIGN_KEY_CHECKS = 0;

#
# DDL START
#

INSERT INTO `ACTIVITYLOG`
	(`id`, `datecreated`, `sessionid`, `orderid`, `userid`, `userlogin`, `username`, `importance`, `sectioncode`, `actioncode`, `actionnotes`, `success`)
	VALUES (0, now(), 0, 0, 0, '', (SELECT USER()), 0, 'UPGRADE', '3.2.0a1part2', 'STARTED', 1);

ALTER TABLE `SHIPPINGMETHODS` ADD COLUMN `canmodifycontactdetails` INTEGER NOT NULL DEFAULT 0 AFTER `usedefaultshippingaddress`;

UPDATE `SYSTEMCONFIG` SET `webversiondate` = '2012-01-23';
UPDATE `SYSTEMCONFIG` SET `webversionnumber` = '3.2.0.1';
UPDATE `SYSTEMCONFIG` SET `webversionstring`= '3.2.0a1';

INSERT INTO `ACTIVITYLOG`
	(`id`, `datecreated`, `sessionid`, `orderid`, `userid`, `userlogin`, `username`, `importance`, `sectioncode`, `actioncode`, `actionnotes`, `success`)
	VALUES (0, now(), 0, 0, 0, '', (SELECT USER()), 0, 'UPGRADE', '3.2.0a1part2', 'FINISHED', 1);

#
# DDL END
#

SET FOREIGN_KEY_CHECKS = 1;
