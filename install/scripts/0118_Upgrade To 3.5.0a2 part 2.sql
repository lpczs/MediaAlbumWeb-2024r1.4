#
SET FOREIGN_KEY_CHECKS = 0;

#
# DDL START
#

INSERT INTO `ACTIVITYLOG`
	(`id`, `datecreated`, `sessionid`, `orderid`, `userid`, `userlogin`, `username`, `importance`, `sectioncode`, `actioncode`, `actionnotes`, `success`)
	VALUES (0, now(), 0, 0, 0, '', (SELECT USER()), 0, 'UPGRADE', '3.5.0a2 part2', 'STARTED', 1);

UPDATE `SYSTEMCONFIG` SET `webversiondate` = '2012-01-31';
UPDATE `SYSTEMCONFIG` SET `webversionnumber` = '3.5.0.2';
UPDATE `SYSTEMCONFIG` SET `webversionstring`= '3.5.0a2';
 
ALTER TABLE `USERS` ADD COLUMN `registeredtaxnumbertype` TINYINT(1) NOT NULL DEFAULT 0 AFTER `shippingtaxcode`;

ALTER TABLE `LICENSEKEYS` ADD COLUMN `registeredtaxnumbertype` TINYINT(1) NOT NULL DEFAULT 0 AFTER `shippingtaxcode`;

ALTER TABLE `LICENSEKEYS` ADD COLUMN `registeredtaxnumber` VARCHAR(50) NOT NULL AFTER `registeredtaxnumbertype`;

INSERT INTO `ACTIVITYLOG`
	(`id`, `datecreated`, `sessionid`, `orderid`, `userid`, `userlogin`, `username`, `importance`, `sectioncode`, `actioncode`, `actionnotes`, `success`)
	VALUES (0, now(), 0, 0, 0, '', (SELECT USER()), 0, 'UPGRADE', '3.5.0a2 part2', 'FINISHED', 1);

#
# DDL END
#

SET FOREIGN_KEY_CHECKS = 1;
