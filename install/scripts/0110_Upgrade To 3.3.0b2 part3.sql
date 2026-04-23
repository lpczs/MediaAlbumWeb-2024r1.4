#
SET FOREIGN_KEY_CHECKS = 0;

#
# DDL START
#

INSERT INTO `ACTIVITYLOG`
	(`id`, `datecreated`, `sessionid`, `orderid`, `userid`, `userlogin`, `username`, `importance`, `sectioncode`, `actioncode`, `actionnotes`, `success`)
	VALUES (0, now(), 0, 0, 0, '', (SELECT USER()), 0, 'UPGRADE', '3.3.0b2 part3', 'STARTED', 1);

UPDATE `SYSTEMCONFIG` SET `webversiondate` = '2012-10-01';
UPDATE `SYSTEMCONFIG` SET `webversionnumber` = '3.3.0.4';
UPDATE `SYSTEMCONFIG` SET `webversionstring`= '3.3.0b2';

DROP INDEX `newindex` ON `ORDERHEADER`;
ALTER TABLE `ORDERHEADER` ADD INDEX designeruuid(`designeruuid`);

ALTER TABLE `ORDERITEMCOMPONENTS` ADD `componentpriceinfo` VARCHAR( 1024 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER `componentinfo`;

UPDATE `ORDERHEADER` SET `billingcustomercounty` = 'Teesside' WHERE `billingcustomerregioncode` = 'TEESIDE';
UPDATE `USERS` SET `county` = 'Teesside' WHERE `regioncode` = 'TEESIDE';
UPDATE `COUNTRYREGION` SET `regionname` = 'en Teesside' WHERE `regioncode` = 'TEESIDE';
UPDATE `ORDERSHIPPING` SET `shippingcustomercounty` = 'Teesside' WHERE `shippingcustomerregioncode` = 'TEESIDE';
UPDATE `LICENSEKEYS` SET `county` = 'Teesside' WHERE `regioncode` = 'TEESIDE';
UPDATE `COMPANIES` SET `county` = 'Teesside' WHERE `regioncode` = 'TEESIDE';
UPDATE `SITES` SET `county` = 'Teesside' WHERE `regioncode` = 'TEESIDE';

INSERT INTO `ACTIVITYLOG`
	(`id`, `datecreated`, `sessionid`, `orderid`, `userid`, `userlogin`, `username`, `importance`, `sectioncode`, `actioncode`, `actionnotes`, `success`)
	VALUES (0, now(), 0, 0, 0, '', (SELECT USER()), 0, 'UPGRADE', '3.3.0b2 part3', 'FINISHED', 1);

#
# DDL END
#

SET FOREIGN_KEY_CHECKS = 1;
