#
SET FOREIGN_KEY_CHECKS = 0;

#
# DDL START
#

UPDATE `SYSTEMCONFIG` SET `webversiondate` = '2017-07-06';
UPDATE `SYSTEMCONFIG` SET `webversionnumber` = '2017.2.0';
UPDATE `SYSTEMCONFIG` SET `webversionstring`= '2017r2';


ALTER TABLE `PRODUCTS` DROP `previewangle`;


# Add a new field and index to the EVENTS table
ALTER TABLE `EVENTS`
ADD COLUMN `targetuserid` INT(11) NOT NULL DEFAULT 0 AFTER `userid`,
ADD INDEX `targetuserid` (`targetuserid` ASC);


UPDATE `BRANDING` SET `previewlicensekey` = '';

ALTER TABLE `BRANDING`
ADD COLUMN `onlinedesignerlogolinkurl` VARCHAR(100) NOT NULL DEFAULT '' AFTER `showshufflelayoutoptions`,
ADD COLUMN `onlinedesignerlogolinktooltip` VARCHAR(1024) NOT NULL DEFAULT '' AFTER `onlinedesignerlogolinkurl`;


ALTER TABLE `LICENSEKEYS`
ADD COLUMN `onlinedesignerlogolinkurl` VARCHAR(100) NOT NULL DEFAULT '' AFTER `usedefaultshufflelayout`,
ADD COLUMN `usedefaultonlinedesignerlogolinkurl` TINYINT(1) NOT NULL DEFAULT '1' AFTER `onlinedesignerlogolinkurl`,
ADD COLUMN `onlinedesignerlogolinktooltip` VARCHAR(1024) NOT NULL DEFAULT '' AFTER `usedefaultonlinedesignerlogolinkurl`;


ALTER TABLE `KEYWORDS`
CHANGE COLUMN `name` `name` VARCHAR(2048) NOT NULL DEFAULT '',
CHANGE COLUMN `flags` `flags` VARCHAR(4096) NOT NULL ;




#
# DDL END
#

SET FOREIGN_KEY_CHECKS = 1;