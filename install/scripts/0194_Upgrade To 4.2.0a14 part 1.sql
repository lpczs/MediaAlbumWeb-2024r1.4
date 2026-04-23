#
SET FOREIGN_KEY_CHECKS = 0;

#
# DDL START
#

INSERT INTO `ACTIVITYLOG`
	(`id`, `datecreated`, `sessionid`, `orderid`, `userid`, `userlogin`, `username`, `importance`, `sectioncode`, `actioncode`, `actionnotes`, `success`)
	VALUES (0, now(), 0, 0, 0, '', (SELECT USER()), 0, 'UPGRADE', '4.2.0a14 part1', 'STARTED', 1);

UPDATE `SYSTEMCONFIG` SET `webversiondate` = '2014-01-20';
UPDATE `SYSTEMCONFIG` SET `webversionnumber` = '4.2.0.14';
UPDATE `SYSTEMCONFIG` SET `webversionstring`= '4.2.0a14';

ALTER TABLE `BRANDING` ADD COLUMN `aucacheversionmasks` VARCHAR(30) NOT NULL DEFAULT '' AFTER `productcategoryassetversion`,
 ADD COLUMN `aucacheversionbackgrounds` VARCHAR(30) NOT NULL DEFAULT '' AFTER `aucacheversionmasks`,
 ADD COLUMN `aucacheversionscrapbook` VARCHAR(30) NOT NULL DEFAULT '' AFTER `aucacheversionbackgrounds`,
 ADD COLUMN `aucacheversionframes` VARCHAR(30) NOT NULL DEFAULT '' AFTER `aucacheversionscrapbook`;

UPDATE `BRANDING` SET `aucacheversionmasks` = CONCAT(NOW(), "_", floor(rand() * 100000));
UPDATE `BRANDING` SET `aucacheversionbackgrounds` = CONCAT(NOW(), "_", floor(rand() * 100000));
UPDATE `BRANDING` SET `aucacheversionscrapbook` = CONCAT(NOW(), "_", floor(rand() * 100000));
UPDATE `BRANDING` SET `aucacheversionframes` = CONCAT(NOW(), "_", floor(rand() * 100000));

INSERT INTO `ACTIVITYLOG`
	(`id`, `datecreated`, `sessionid`, `orderid`, `userid`, `userlogin`, `username`, `importance`, `sectioncode`, `actioncode`, `actionnotes`, `success`)
	VALUES (0, now(), 0, 0, 0, '', (SELECT USER()), 0, 'UPGRADE', '4.2.0a14 part1', 'FINISHED', 1);

#
# DDL END
#

SET FOREIGN_KEY_CHECKS = 1;
