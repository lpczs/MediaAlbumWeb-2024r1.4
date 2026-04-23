#
SET FOREIGN_KEY_CHECKS = 0;

#
# DDL START
#

INSERT INTO `ACTIVITYLOG`
	(`id`, `datecreated`, `sessionid`, `orderid`, `userid`, `userlogin`, `username`, `importance`, `sectioncode`, `actioncode`, `actionnotes`, `success`)
	VALUES (0, now(), 0, 0, 0, '', (SELECT USER()), 0, 'UPGRADE', '3.0.0a6', 'STARTED', 1);

INSERT INTO `TRIGGERS` (`datecreated` ,`eventcode` ,`language` ,`exportformat` ,`includepaymentdata` ,`beautifiedxml` ,`subfolderformat` ,`filenameformat` ,`active`) VALUES (now(),'ORDERUPLOADCOMPLETE' ,    'Default' , 'XML' , 0 , 1 , '' , '' , 0);

ALTER TABLE `ORDERTHUMBNAILS` ADD COLUMN `version` INT(1) NOT NULL DEFAULT 1 AFTER `height`;

ALTER TABLE `BRANDING` ADD COLUMN `smtporderuploadedname` VARCHAR(50) NOT NULL AFTER `smtpresetpasswordactive`;
ALTER TABLE `BRANDING` ADD COLUMN `smtporderuploadedaddress` VARCHAR(50) NOT NULL AFTER `smtporderuploadedname`;
ALTER TABLE `BRANDING` ADD COLUMN `smtporderuploadedactive` INTEGER NOT NULL DEFAULT 0 AFTER `smtporderuploadedaddress`;

ALTER TABLE `PRODUCTS` ADD COLUMN `previewtype` INTEGER(1) NOT NULL DEFAULT 1 AFTER `createnewprojects`;
ALTER TABLE `PRODUCTS` ADD COLUMN `previewcovertype` TINYINT(1) NOT NULL DEFAULT 0 AFTER `previewtype`;
ALTER TABLE `PRODUCTS` ADD COLUMN `previewangle` INTEGER NOT NULL DEFAULT 0 AFTER `previewcovertype`;
ALTER TABLE `PRODUCTS` ADD COLUMN `previewautoflip` TINYINT(1) NOT NULL DEFAULT 0 AFTER `previewangle`;
ALTER TABLE `PRODUCTS` ADD COLUMN `previewthumbnailsview` TINYINT(1) NOT NULL DEFAULT 1 AFTER `previewautoflip`;
ALTER TABLE `PRODUCTS` ADD COLUMN `previewthumbnails` TINYINT(1) NOT NULL DEFAULT 1 AFTER `previewthumbnailsview`;

ALTER TABLE `BRANDING` ADD COLUMN `previewlicensekey` VARCHAR(1024) NOT NULL DEFAULT '' AFTER `designersplashscreenadvertenddate`;

UPDATE `SYSTEMCONFIG` SET `webversiondate` = '2011-09-08';
UPDATE `SYSTEMCONFIG` SET `webversionnumber` = '3.0.0.6';
UPDATE `SYSTEMCONFIG` SET `webversionstring`= '3.0.0a6';

INSERT INTO `ACTIVITYLOG`
	(`id`, `datecreated`, `sessionid`, `orderid`, `userid`, `userlogin`, `username`, `importance`, `sectioncode`, `actioncode`, `actionnotes`, `success`)
	VALUES (0, now(), 0, 0, 0, '', (SELECT USER()), 0, 'UPGRADE', '3.0.0a6', 'FINISHED', 1);

#
# DDL END
#

SET FOREIGN_KEY_CHECKS = 1;
