#
SET FOREIGN_KEY_CHECKS = 0;

#
# DDL START
#

INSERT INTO `ACTIVITYLOG`
	(`id`, `datecreated`, `sessionid`, `orderid`, `userid`, `userlogin`, `username`, `importance`, `sectioncode`, `actioncode`, `actionnotes`, `success`)
	VALUES (0, now(), 0, 0, 0, '', (SELECT USER()), 0, 'UPGRADE', '3.0.2a4part4', 'STARTED', 1);

ALTER TABLE `ORDERHEADER` ADD COLUMN `useripaddress` VARCHAR(50) NOT NULL AFTER `userid`,
 ADD COLUMN `userbrowser` VARCHAR(500) NOT NULL AFTER `useripaddress`,
 ADD COLUMN `sessionid` INTEGER NOT NULL DEFAULT 0 AFTER `userbrowser`;

UPDATE `ORDERHEADER` SET `sessionid` = 0;

UPDATE `ORDERHEADER` `oh` SET `oh`.`sessionid` = IFNULL((SELECT `cci`.`sessionid` FROM `CCILOG` cci WHERE `cci`.`orderid` = `oh`.`id` LIMIT 1), 0);

ALTER TABLE `ORDERHEADER` ADD COLUMN `temporder` TINYINT NOT NULL DEFAULT 0 AFTER `origordernumber`;

UPDATE `ORDERHEADER` SET `temporder` = 0;

UPDATE `ORDERHEADER` SET `temporderid` = 0;

ALTER TABLE `ORDERHEADER` ADD COLUMN `temporderexpirydate` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' AFTER `temporderid`;

UPDATE `ORDERHEADER` SET `temporderexpirydate` = '0000-00-00 00:00:00';

DROP TABLE `ORDERTEMP`;

INSERT INTO `TRIGGERS` (`datecreated` ,`eventcode` ,`language` ,`exportformat` ,`includepaymentdata` ,`beautifiedxml` ,`subfolderformat` ,`filenameformat` ,`active`) 
VALUES (now(), 'TEMPORDERCREATED', 'Default', 'XML', 0, 1, '', '', 0);


UPDATE `SYSTEMCONFIG` SET `webversiondate` = '2011-11-15';
UPDATE `SYSTEMCONFIG` SET `webversionnumber` = '3.0.2.4';
UPDATE `SYSTEMCONFIG` SET `webversionstring`= '3.0.2a4';

INSERT INTO `ACTIVITYLOG`
	(`id`, `datecreated`, `sessionid`, `orderid`, `userid`, `userlogin`, `username`, `importance`, `sectioncode`, `actioncode`, `actionnotes`, `success`)
	VALUES (0, now(), 0, 0, 0, '', (SELECT USER()), 0, 'UPGRADE', '3.0.2a4part4', 'FINISHED', 1);

#
# DDL END
#

SET FOREIGN_KEY_CHECKS = 1;
