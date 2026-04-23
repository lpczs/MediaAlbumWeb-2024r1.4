#
SET FOREIGN_KEY_CHECKS = 0;

#
# DDL START
#

INSERT INTO `ACTIVITYLOG`
	(`id`, `datecreated`, `sessionid`, `orderid`, `userid`, `userlogin`, `username`, `importance`, `sectioncode`, `actioncode`, `actionnotes`, `success`)
	VALUES (0, now(), 0, 0, 0, '', (SELECT USER()), 0, 'UPGRADE', '3.0.2a3part3', 'STARTED', 1);

ALTER TABLE `ORDERTEMP` ADD COLUMN `uploadbatchref` VARCHAR(200) NOT NULL AFTER `orderitemid`,
 ADD INDEX newindex(`uploadbatchref`);

UPDATE `ORDERTEMP` SET `uploadbatchref` = `uploadref`;

ALTER TABLE `ORDERITEMS` ADD COLUMN `productcodepurchased` VARCHAR(50) NOT NULL AFTER `productcode`;

UPDATE `ORDERITEMS` SET `productcodepurchased` = `productcode`;

ALTER TABLE `ORDERITEMS` ADD COLUMN `canuploadproductcodeoverride` TINYINT(1) NOT NULL DEFAULT 0 AFTER `canupload`;

UPDATE `ORDERITEMS` SET `canuploadproductcodeoverride` = 0;

ALTER TABLE `ORDERHEADER` ADD COLUMN `groupdata` VARCHAR(50) NOT NULL AFTER `groupcode`;

ALTER TABLE `ORDERITEMS` ADD COLUMN `active` int(11) NOT NULL DEFAULT '0' AFTER `statustimestamp`;

ALTER TABLE `ORDERITEMS` ADD COLUMN `activetimestamp` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' AFTER `active`;

ALTER TABLE `ORDERITEMS` ADD COLUMN `activeuserid` int(11) NOT NULL DEFAULT '0' AFTER `activetimestamp`;

UPDATE `ORDERITEMS` oi JOIN `ORDERHEADER` oh ON `oh`.`id` = `oi`.`orderid` 
SET `oi`.`active` = `oh`.`status`, `oi`.`activetimestamp` = `oh`.`statustimestamp`, `oi`.`activeuserid` = `oh`.`statususerid`;

UPDATE `ORDERITEMS` SET `activetimestamp` = `datecreated` WHERE `activetimestamp` = '0000-00-00 00:00:00';

ALTER TABLE `ORDERITEMS` ADD INDEX active(`active`);

ALTER TABLE `ORDERHEADER` DROP COLUMN `status`,
 DROP COLUMN `statustimestamp`,
 DROP COLUMN `statususerid`,
 DROP COLUMN `statusdescription`;

ALTER TABLE `ORDERITEMS` ADD COLUMN `decryptfilesreceivedtimestamp` DATETIME NOT NULL DEFAULT '0000-00-00' AFTER `decrypttimestamp`;

UPDATE `ORDERITEMS` SET `decryptfilesreceivedtimestamp` = `filesreceivedtimestamp`;

ALTER TABLE `ORDERITEMS` ADD COLUMN `canuploadenablesaveoverride` TINYINT(1) NOT NULL DEFAULT 0 AFTER `canuploadpagecountoverride`;

UPDATE `ORDERITEMS` SET `canuploadenablesaveoverride` = 0;

UPDATE `SYSTEMCONFIG` SET `webversiondate` = '2011-11-09';
UPDATE `SYSTEMCONFIG` SET `webversionnumber` = '3.0.2.3';
UPDATE `SYSTEMCONFIG` SET `webversionstring`= '3.0.2a3';

INSERT INTO `ACTIVITYLOG`
	(`id`, `datecreated`, `sessionid`, `orderid`, `userid`, `userlogin`, `username`, `importance`, `sectioncode`, `actioncode`, `actionnotes`, `success`)
	VALUES (0, now(), 0, 0, 0, '', (SELECT USER()), 0, 'UPGRADE', '3.0.2a3part3', 'FINISHED', 1);

#
# DDL END
#

SET FOREIGN_KEY_CHECKS = 1;
