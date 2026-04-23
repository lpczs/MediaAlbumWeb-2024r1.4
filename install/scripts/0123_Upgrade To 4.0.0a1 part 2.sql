#
SET FOREIGN_KEY_CHECKS = 0;

#
# DDL START
#

INSERT INTO `ACTIVITYLOG`
	(`id`, `datecreated`, `sessionid`, `orderid`, `userid`, `userlogin`, `username`, `importance`, `sectioncode`, `actioncode`, `actionnotes`, `success`)
	VALUES (0, now(), 0, 0, 0, '', (SELECT USER()), 0, 'UPGRADE', '4.0.0a1 part2', 'STARTED', 1);

UPDATE `SYSTEMCONFIG` SET `webversiondate` = '2013-06-13';
UPDATE `SYSTEMCONFIG` SET `webversionnumber` = '4.0.0.1';
UPDATE `SYSTEMCONFIG` SET `webversionstring`= '4.0.0a1';

ALTER TABLE `APPLICATIONFILES` ADD COLUMN `onlinedependencies` MEDIUMTEXT NOT NULL AFTER `dependencies`;

ALTER TABLE `APPLICATIONFILES` MODIFY COLUMN `dependencies` MEDIUMTEXT;

ALTER TABLE `SESSIONDATA` ADD `onlinesession` TINYINT(1) NOT NULL DEFAULT 0 AFTER `productionsession`;

ALTER TABLE `LICENSEKEYS` ADD COLUMN `onlineactive` TINYINT(1) NOT NULL DEFAULT 0 AFTER `active`;

ALTER TABLE `BRANDING` ADD COLUMN `onlinedesignerurl` VARCHAR(100) NOT NULL DEFAULT '' AFTER `weburl`;

ALTER TABLE `BRANDING` ADD COLUMN `onlinedesignersigninregisterpromptdelay` INTEGER NOT NULL DEFAULT 10 AFTER `previewexpiresdays`;

ALTER TABLE `LICENSEKEYS` ADD COLUMN `onlinedesignerguestworkflowenabled` TINYINT(1) NOT NULL DEFAULT 0 AFTER `designerbannerenddate`;

ALTER TABLE `SYSTEMCONFIG` ADD COLUMN `config` INT(11) NOT NULL DEFAULT 0 AFTER `systemcertificate`;

UPDATE SYSTEMCONFIG SET `config` = (SELECT `config` FROM CONSTANTS);

ALTER TABLE `CONSTANTS` DROP COLUMN `config`;

ALTER TABLE `SYSTEMCONFIG` ADD COLUMN `supportedlocales` VARCHAR(200) NOT NULL DEFAULT '' AFTER `cronactive`;


INSERT INTO `ACTIVITYLOG`
	(`id`, `datecreated`, `sessionid`, `orderid`, `userid`, `userlogin`, `username`, `importance`, `sectioncode`, `actioncode`, `actionnotes`, `success`)
	VALUES (0, now(), 0, 0, 0, '', (SELECT USER()), 0, 'UPGRADE', '4.0.0a1 part2', 'FINISHED', 1);

#
# DDL END
#

SET FOREIGN_KEY_CHECKS = 1;
