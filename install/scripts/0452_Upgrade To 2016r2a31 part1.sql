#
SET FOREIGN_KEY_CHECKS = 0;

#
# DDL START
#

INSERT INTO `ACTIVITYLOG`
	(`id`, `datecreated`, `sessionid`, `orderid`, `userid`, `userlogin`, `username`, `importance`, `sectioncode`, `actioncode`, `actionnotes`, `success`)
	VALUES (0, now(), 0, 0, 0, '', (SELECT USER()), 0, 'UPGRADE', '2016r2a31', 'STARTED', 1);

UPDATE `SYSTEMCONFIG` SET `webversiondate` = '2016-05-25';
UPDATE `SYSTEMCONFIG` SET `webversionnumber` = '2016.2.0.31';
UPDATE `SYSTEMCONFIG` SET `webversionstring`= '2016r2a31';

ALTER TABLE `SESSIONDATA` ADD COLUMN `ssotoken` VARCHAR(200) NOT NULL DEFAULT '' AFTER `userid`,
 ADD INDEX `ssotoken`(`ssotoken`);

ALTER TABLE `AUTHENTICATIONDATASTORE` CHANGE `url` `originurl` VARCHAR(1000);

ALTER TABLE `AUTHENTICATIONDATASTORE` ADD COLUMN `ssourl` VARCHAR(1000) NOT NULL DEFAULT '' AFTER `originurl`;

INSERT INTO `ACTIVITYLOG`
	(`id`, `datecreated`, `sessionid`, `orderid`, `userid`, `userlogin`, `username`, `importance`, `sectioncode`, `actioncode`, `actionnotes`, `success`)
	VALUES (0, now(), 0, 0, 0, '', (SELECT USER()), 0, 'UPGRADE', '2016r2a31', 'FINISHED', 1);

#
# DDL END
#

SET FOREIGN_KEY_CHECKS = 1;