#
SET FOREIGN_KEY_CHECKS = 0;

#
# DDL START
#

INSERT INTO `ACTIVITYLOG`
	(`id`, `datecreated`, `sessionid`, `orderid`, `userid`, `userlogin`, `username`, `importance`, `sectioncode`, `actioncode`, `actionnotes`, `success`)
	VALUES (0, now(), 0, 0, 0, '', (SELECT USER()), 0, 'UPGRADE', '2016r1b4', 'STARTED', 1);

UPDATE `SYSTEMCONFIG` SET `webversiondate` = '2016-02-08';
UPDATE `SYSTEMCONFIG` SET `webversionnumber` = '2016.1.0.34';
UPDATE `SYSTEMCONFIG` SET `webversionstring`= '2016r1b4';

ALTER TABLE `BRANDING` ADD COLUMN `calendardataassetid` INTEGER NOT NULL DEFAULT 0 AFTER `productcategoryassetversion`,
ADD COLUMN `calendardataassetversion` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' AFTER `calendardataassetid`;

INSERT INTO `COMPONENTCATEGORIES` (`datecreated`, `code`, `name`, `pricingmodel`, `islist`, `requirespagecount`, `componentpricingdecimalplaces`, `private`, `active`)
VALUES (NOW(), "CALENDARCUSTOMISATION", "en Calendar Customisation", 7, 1, 0, 2, 1, 1);

INSERT INTO `SECTIONS` (`datecreated`, `code`, `label`, `name`, `categorycode`, `displaytype`, `sortorder`, `itemorder`, `displaysection`, `private`, `active`)
VALUES (NOW(), "CALENDARCUSTOMISATION", "en Calendar Customisation", "en Calendar Customisation", "CALENDARCUSTOMISATION", 1,
(SELECT MAX(`s`.`sortorder`) + 1 FROM SECTIONS `s` WHERE `s`.`sortorder` < 999999998), 0, 1, 1, 1);

INSERT INTO `COMPONENTS` (`datecreated`, `datelastmodified`, `categorycode`, `code`, `localcode`, `name`, `active`)
VALUES (NOW(), NOW(), "CALENDARCUSTOMISATION", "CALENDARCUSTOMISATION.ANY", "ANY", "en Any Customisation", 1);

INSERT INTO `COMPONENTS` (`datecreated`, `datelastmodified`, `categorycode`, `code`, `localcode`, `name`, `active`)
VALUES (NOW(), NOW(), "CALENDARCUSTOMISATION", "CALENDARCUSTOMISATION.DATE", "DATE", "en Date Customisation", 1);

INSERT INTO `COMPONENTS` (`datecreated`, `datelastmodified`, `categorycode`, `code`, `localcode`, `name`, `active`)
VALUES (NOW(), NOW(), "CALENDARCUSTOMISATION", "CALENDARCUSTOMISATION.EVENTSET", "EVENTSET", "en Event Set Customisation", 1);

DELETE FROM `CACHEDATA`;

INSERT INTO `ACTIVITYLOG`
	(`id`, `datecreated`, `sessionid`, `orderid`, `userid`, `userlogin`, `username`, `importance`, `sectioncode`, `actioncode`, `actionnotes`, `success`)
	VALUES (0, now(), 0, 0, 0, '', (SELECT USER()), 0, 'UPGRADE', '2016r1b4', 'FINISHED', 1);

#
# DDL END
#

SET FOREIGN_KEY_CHECKS = 1;