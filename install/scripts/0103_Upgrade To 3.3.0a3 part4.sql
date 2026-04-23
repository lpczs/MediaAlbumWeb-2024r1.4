#
SET FOREIGN_KEY_CHECKS = 0;

#
# DDL START
#

INSERT INTO `ACTIVITYLOG`
	(`id`, `datecreated`, `sessionid`, `orderid`, `userid`, `userlogin`, `username`, `importance`, `sectioncode`, `actioncode`, `actionnotes`, `success`)
	VALUES (0, now(), 0, 0, 0, '', (SELECT USER()), 0, 'UPGRADE', '3.3.0a3 part4', 'STARTED', 1);

UPDATE `SYSTEMCONFIG` SET `webversiondate` = '2012-08-28';
UPDATE `SYSTEMCONFIG` SET `webversionnumber` = '3.3.0.3';
UPDATE `SYSTEMCONFIG` SET `webversionstring`= '3.3.0a3';

ALTER TABLE `LICENSEKEYS` ADD COLUMN `designersplashscreenassetid` INTEGER NOT NULL DEFAULT 0 AFTER `paymentmethods`,
ADD COLUMN `designersplashscreenstartdate` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' AFTER `designersplashscreenassetid`,
ADD COLUMN `designersplashscreenenddate` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' AFTER `designersplashscreenstartdate`,
ADD COLUMN `designerbannerassetid` INTEGER NOT NULL DEFAULT 0 AFTER `designersplashscreenenddate`,
ADD COLUMN `designerbannerstartdate` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' AFTER `designerbannerassetid`,
ADD COLUMN `designerbannerenddate` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' AFTER `designerbannerstartdate`;

ALTER TABLE `BRANDING` DROP COLUMN `designersplashscreenadvertassetid`,
 DROP COLUMN `designersplashscreenadvertstartdate`,
 DROP COLUMN `designersplashscreenadvertenddate`;


INSERT INTO `ACTIVITYLOG`
	(`id`, `datecreated`, `sessionid`, `orderid`, `userid`, `userlogin`, `username`, `importance`, `sectioncode`, `actioncode`, `actionnotes`, `success`)
	VALUES (0, now(), 0, 0, 0, '', (SELECT USER()), 0, 'UPGRADE', '3.3.0a3 part4', 'FINISHED', 1);

#
# DDL END
#

SET FOREIGN_KEY_CHECKS = 1;
