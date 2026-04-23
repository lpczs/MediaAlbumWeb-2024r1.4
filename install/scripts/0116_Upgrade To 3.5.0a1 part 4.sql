#
SET FOREIGN_KEY_CHECKS = 0;

#
# DDL START
#

INSERT INTO `ACTIVITYLOG`
	(`id`, `datecreated`, `sessionid`, `orderid`, `userid`, `userlogin`, `username`, `importance`, `sectioncode`, `actioncode`, `actionnotes`, `success`)
	VALUES (0, now(), 0, 0, 0, '', (SELECT USER()), 0, 'UPGRADE', '3.5.0a1 part4', 'STARTED', 1);

UPDATE `SYSTEMCONFIG` SET `webversiondate` = '2013-01-15';
UPDATE `SYSTEMCONFIG` SET `webversionnumber` = '3.5.0.1';
UPDATE `SYSTEMCONFIG` SET `webversionstring`= '3.5.0a1';

UPDATE `SHIPPINGRATES` SET `rate` = CONCAT(REPLACE(TRIM(`rate`), " ", "-0 "), "-0");

ALTER TABLE `LICENSEKEYS` DROP COLUMN `totalvouchertaxpreference`;

ALTER TABLE `USERS` DROP COLUMN `totalvouchertaxpreference`;

INSERT INTO `ACTIVITYLOG`
	(`id`, `datecreated`, `sessionid`, `orderid`, `userid`, `userlogin`, `username`, `importance`, `sectioncode`, `actioncode`, `actionnotes`, `success`)
	VALUES (0, now(), 0, 0, 0, '', (SELECT USER()), 0, 'UPGRADE', '3.5.0a1 part4', 'FINISHED', 1);

#
# DDL END
#

SET FOREIGN_KEY_CHECKS = 1;
