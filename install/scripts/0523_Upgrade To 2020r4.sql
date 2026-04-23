#
SET FOREIGN_KEY_CHECKS = 0;

#
# DDL START
#

UPDATE `SYSTEMCONFIG` SET `webversiondate` = '2020-10-06';
UPDATE `SYSTEMCONFIG` SET `webversionnumber` = '2020.4.0';
UPDATE `SYSTEMCONFIG` SET `webversionstring`= '2020r4';

ALTER TABLE `BRANDING`
    ADD COLUMN `averagepicturesperpage` INT(11) unsigned NOT NULL DEFAULT '0' AFTER `totalpagesdropdownmode`;

ALTER TABLE `licensekeys` 
    ADD COLUMN `usedefaultaveragepicturesperpage` TINYINT(1) NOT NULL DEFAULT 1 AFTER `totalpagesdropdownmode`,
    ADD COLUMN `averagepicturesperpage` INT(11) unsigned NOT NULL DEFAULT '0' AFTER `usedefaultaveragepicturesperpage`;

ALTER TABLE `products` 
    ADD COLUMN `usedefaultaveragepicturesperpage` TINYINT(1) NOT NULL DEFAULT 1 AFTER `imagescalingbefore`,
    ADD COLUMN `averagepicturesperpage` INT(11) unsigned NOT NULL DEFAULT '0' AFTER `usedefaultaveragepicturesperpage`;

#
# DDL END
#

SET FOREIGN_KEY_CHECKS = 1;

