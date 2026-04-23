#
SET FOREIGN_KEY_CHECKS = 0;

#
# DDL START
#

UPDATE `SYSTEMCONFIG` SET `webversiondate` = '2018-03-28';
UPDATE `SYSTEMCONFIG` SET `webversionnumber` = '2018.1.0';
UPDATE `SYSTEMCONFIG` SET `webversionstring`= '2018r1';

ALTER TABLE BRANDING
ADD COLUMN `enableswitchingeditor` TINYINT(1) NOT NULL DEFAULT 1 AFTER `allowfulldesignerproductsinwizarddesigner`,
ADD COLUMN `automaticallyapplyperfectlyclear` TINYINT(1) NOT NULL DEFAULT 0 AFTER `smartguidespageguidecolour`,
ADD COLUMN `allowuserstotoggleperfectlyclear` TINYINT(1) NOT NULL DEFAULT 0 AFTER `automaticallyapplyperfectlyclear`,
ADD COLUMN `onlinedesignercdnurl` VARCHAR(100) NOT NULL DEFAULT '' AFTER `allowuserstotoggleperfectlyclear`;

ALTER TABLE BRANDING
CHANGE COLUMN `allowfulldesignerproductsinwizarddesigner` `onlineeditormode` TINYINT(3) NOT NULL DEFAULT 0;

UPDATE `BRANDING`
SET `onlineeditormode` = 0, `enableswitchingeditor` = 1;

ALTER TABLE `LICENSEKEYS`
ADD COLUMN `usedefaultautomaticallyapplyperfectlyclear` TINYINT(1) NOT NULL DEFAULT 1 AFTER `usedefaultsmartguidessettings`,
ADD COLUMN `automaticallyapplyperfectlyclear` TINYINT(1) NOT NULL DEFAULT 0 AFTER `usedefaultautomaticallyapplyperfectlyclear`,
ADD COLUMN `allowuserstotoggleperfectlyclear` TINYINT(1) NOT NULL DEFAULT 0 AFTER `automaticallyapplyperfectlyclear`,
ADD COLUMN `onlineeditormode` TINYINT(3) NOT NULL DEFAULT 0 AFTER `usedefaultshufflelayout`,
ADD COLUMN `enableswitchingeditor` TINYINT(1) NOT NULL DEFAULT 0 AFTER `onlineeditormode`,
ADD COLUMN `usedefaultonlineeditormode` TINYINT(1) NOT NULL DEFAULT 1 AFTER `enableswitchingeditor`;

ALTER TABLE `USERS`
CHANGE COLUMN `password` `password` VARCHAR(255) NOT NULL DEFAULT '';

ALTER TABLE `SHAREDITEMS`
CHANGE COLUMN `password` `password` VARCHAR(255) NOT NULL DEFAULT '';

#
# DDL END
#

SET FOREIGN_KEY_CHECKS = 1;

