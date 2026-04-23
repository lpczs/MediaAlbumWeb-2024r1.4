#
SET FOREIGN_KEY_CHECKS = 0;

#
# DDL START
#

UPDATE `SYSTEMCONFIG` SET `webversiondate` = '2019-08-05';
UPDATE `SYSTEMCONFIG` SET `webversionnumber` = '2019.3.0';
UPDATE `SYSTEMCONFIG` SET `webversionstring`= '2019r3';

ALTER TABLE `BRANDING` 
ADD COLUMN `insertdeletebuttonsvisibility` TINYINT(1) NOT NULL DEFAULT 1 AFTER `onlinedesignercdnurl`,
ADD COLUMN `totalpagesdropdownmode` TINYINT(1) NOT NULL DEFAULT 1 AFTER `insertdeletebuttonsvisibility`;

ALTER TABLE `licensekeys` 
ADD COLUMN `usedefaultinsertdeletebuttonsvisibility` TINYINT(1) NOT NULL DEFAULT 1 AFTER `allowuserstotoggleperfectlyclear`,
ADD COLUMN `insertdeletebuttonsvisibility` TINYINT(1) NOT NULL DEFAULT 1 AFTER `usedefaultinsertdeletebuttonsvisibility`,
ADD COLUMN `usedefaulttotalpagesdropdownmode` TINYINT(1) NOT NULL DEFAULT 1 AFTER `insertdeletebuttonsvisibility`,
ADD COLUMN `totalpagesdropdownmode` TINYINT(1) NOT NULL DEFAULT 1 AFTER `usedefaulttotalpagesdropdownmode`;

#
# DDL END
#

SET FOREIGN_KEY_CHECKS = 1;

