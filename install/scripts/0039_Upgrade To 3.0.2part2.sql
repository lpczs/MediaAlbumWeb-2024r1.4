#
SET FOREIGN_KEY_CHECKS = 0;

#
# DDL START
#

INSERT INTO `ACTIVITYLOG`
	(`id`, `datecreated`, `sessionid`, `orderid`, `userid`, `userlogin`, `username`, `importance`, `sectioncode`, `actioncode`, `actionnotes`, `success`)
	VALUES (0, now(), 0, 0, 0, '', (SELECT USER()), 0, 'UPGRADE', '3.0.2a2', 'STARTED', 1);

ALTER TABLE `OUTPUTFORMATSPRODUCTLINK` ADD INDEX ownerproductcode(`owner`, `productcode`);
ALTER TABLE `ORDERHEADER` ADD INDEX status(`status`);

ALTER TABLE `SHAREDITEMS` ADD COLUMN `active` TINYINT(1) NOT NULL DEFAULT 0 AFTER `password`;

UPDATE `SHAREDITEMS` SET `active` = 1 WHERE `action` = "SHARE" AND `datecreated` = `datemodified`;

UPDATE `SYSTEMCONFIG` SET `webversiondate` = '2011-11-07';
UPDATE `SYSTEMCONFIG` SET `webversionnumber` = '3.0.2.2';
UPDATE `SYSTEMCONFIG` SET `webversionstring`= '3.0.2a2';

INSERT INTO `ACTIVITYLOG`
	(`id`, `datecreated`, `sessionid`, `orderid`, `userid`, `userlogin`, `username`, `importance`, `sectioncode`, `actioncode`, `actionnotes`, `success`)
	VALUES (0, now(), 0, 0, 0, '', (SELECT USER()), 0, 'UPGRADE', '3.0.2a2', 'FINISHED', 1);

#
# DDL END
#

SET FOREIGN_KEY_CHECKS = 1;
