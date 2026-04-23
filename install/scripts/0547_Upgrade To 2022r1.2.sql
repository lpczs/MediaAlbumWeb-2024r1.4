#
SET FOREIGN_KEY_CHECKS = 0;

#
# DDL START
#

UPDATE `SYSTEMCONFIG` SET `webversiondate` = '2022-10-28';
UPDATE `SYSTEMCONFIG` SET `webversionnumber` = '2022.1.2';
UPDATE `SYSTEMCONFIG` SET `webversionstring`= '2022r1.2';

ALTER TABLE `VOUCHERS`
ADD COLUMN `minordervalue` DECIMAL(10,2) NOT NULL DEFAULT 0.00 AFTER `orderid`,
ADD COLUMN `minordervalueincshipping` TINYINT(1) NOT NULL DEFAULT 0 AFTER `minordervalue`,
ADD COLUMN `minordervalueinctax` TINYINT(1) NOT NULL DEFAULT 0 AFTER `minordervalueincshipping`;

ALTER TABLE `OUTPUTFORMATS`
ADD COLUMN `foldmarkoffset` VARCHAR(30) NOT NULL DEFAULT '0.00' AFTER `cropmarkcolour`,
ADD COLUMN `foldmarklength` VARCHAR(30) NOT NULL DEFAULT '0.00' AFTER `foldmarkoffset`,
ADD COLUMN `foldmarkwidth` VARCHAR(15) NOT NULL DEFAULT '0.0' AFTER `foldmarklength`,
ADD COLUMN `foldmarkborder` VARCHAR(15) NOT NULL DEFAULT '0.0' AFTER `foldmarkwidth`,
ADD COLUMN `foldmarkcolour` VARCHAR(20) NOT NULL AFTER `foldmarkborder`,
ADD COLUMN `foldmarkcentreline` TINYINT(1) NOT NULL DEFAULT 0 AFTER `foldmarkcolour`,
ADD COLUMN `foldmarkoutsidelines` TINYINT(1) NOT NULL DEFAULT 0 AFTER `foldmarkcentreline`,
ADD COLUMN `foldmarkshowspinewidth` TINYINT(1) NOT NULL DEFAULT 0 AFTER `foldmarkoutsidelines`;

ALTER TABLE `OUTPUTDEVICES`
ADD COLUMN `additionalsettings` VARCHAR(2048) NOT NULL DEFAULT '' AFTER `pathserver`;

UPDATE `OUTPUTFORMATS` SET `foldmarkcolour` = IF(`printersmarkscolourspace` = 1, "0,0,0,0", "0,0,0");

#
# DDL END
#

SET FOREIGN_KEY_CHECKS = 1;

