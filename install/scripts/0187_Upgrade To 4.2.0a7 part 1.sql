#
SET FOREIGN_KEY_CHECKS = 0;

#
# DDL START
#

INSERT INTO `ACTIVITYLOG`
	(`id`, `datecreated`, `sessionid`, `orderid`, `userid`, `userlogin`, `username`, `importance`, `sectioncode`, `actioncode`, `actionnotes`, `success`)
	VALUES (0, now(), 0, 0, 0, '', (SELECT USER()), 0, 'UPGRADE', '4.2.0a7 part1', 'STARTED', 1);

UPDATE `SYSTEMCONFIG` SET `webversiondate` = '2013-12-11';
UPDATE `SYSTEMCONFIG` SET `webversionnumber` = '4.2.0.7';
UPDATE `SYSTEMCONFIG` SET `webversionstring`= '4.2.0a7';

ALTER TABLE `VOUCHERS` CHANGE COLUMN `defaultdiscount` `defaultdiscount` TINYINT(1) NOT NULL DEFAULT '0';

ALTER TABLE `SHIPPINGMETHODS` CHANGE COLUMN `usedefaultbillingaddress` `usedefaultbillingaddress` TINYINT(1) NOT NULL DEFAULT '0', CHANGE COLUMN `usedefaultshippingaddress` `usedefaultshippingaddress` TINYINT(1) NOT NULL DEFAULT '0', CHANGE COLUMN `canmodifycontactdetails` `canmodifycontactdetails` TINYINT(1) NOT NULL DEFAULT '0';

INSERT INTO `ACTIVITYLOG`
	(`id`, `datecreated`, `sessionid`, `orderid`, `userid`, `userlogin`, `username`, `importance`, `sectioncode`, `actioncode`, `actionnotes`, `success`)
	VALUES (0, now(), 0, 0, 0, '', (SELECT USER()), 0, 'UPGRADE', '4.2.0a7 part1', 'FINISHED', 1);

#
# DDL END
#

SET FOREIGN_KEY_CHECKS = 1;
