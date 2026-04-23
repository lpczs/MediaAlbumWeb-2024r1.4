#
SET FOREIGN_KEY_CHECKS = 0;

#
# DDL START
#

INSERT INTO `ACTIVITYLOG`
	(`id`, `datecreated`, `sessionid`, `orderid`, `userid`, `userlogin`, `username`, `importance`, `sectioncode`, `actioncode`, `actionnotes`, `success`)
	VALUES (0, now(), 0, 0, 0, '', (SELECT USER()), 0, 'UPGRADE', '3.3.0a2 part3', 'STARTED', 1);

UPDATE `SYSTEMCONFIG` SET `webversiondate` = '2012-12-20';
UPDATE `SYSTEMCONFIG` SET `webversionnumber` = '3.3.0.2';
UPDATE `SYSTEMCONFIG` SET `webversionstring`= '3.3.0a2';

ALTER TABLE `LICENSEKEYS` ADD COLUMN `totalvouchertaxpreference` TINYINT(1) NOT NULL DEFAULT 0 AFTER `shippingtaxcode`;

ALTER TABLE `USERS` ADD COLUMN `totalvouchertaxpreference` TINYINT(1) NOT NULL DEFAULT 0 AFTER `shippingtaxcode`;

ALTER TABLE `ORDERHEADER` 
ADD `orderfootersubtotal` DECIMAL( 10, 2 ) NULL DEFAULT '0.00' AFTER `total` ,
ADD `orderfootertotal` DECIMAL( 10, 2 ) NULL DEFAULT '0.00' AFTER `orderfootersubtotal` ,
ADD `orderfooterdiscountvalue` DECIMAL( 10, 2 ) NULL DEFAULT '0.00' AFTER `orderfootertotal`,
ADD `orderalltaxratesequal` TINYINT( 1 ) NOT NULL DEFAULT '0' AFTER `showtaxbreakdown`,
ADD `usergiftcardbalance` DECIMAL( 10, 2 ) NOT NULL DEFAULT '0.00' AFTER `giftcardamount`;


INSERT INTO `ACTIVITYLOG`
	(`id`, `datecreated`, `sessionid`, `orderid`, `userid`, `userlogin`, `username`, `importance`, `sectioncode`, `actioncode`, `actionnotes`, `success`)
	VALUES (0, now(), 0, 0, 0, '', (SELECT USER()), 0, 'UPGRADE', '3.3.0a2 part3', 'FINISHED', 1);

#
# DDL END
#

SET FOREIGN_KEY_CHECKS = 1;
