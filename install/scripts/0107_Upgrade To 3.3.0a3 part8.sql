#
SET FOREIGN_KEY_CHECKS = 0;

#
# DDL START
#

INSERT INTO `ACTIVITYLOG`
	(`id`, `datecreated`, `sessionid`, `orderid`, `userid`, `userlogin`, `username`, `importance`, `sectioncode`, `actioncode`, `actionnotes`, `success`)
	VALUES (0, now(), 0, 0, 0, '', (SELECT USER()), 0, 'UPGRADE', '3.3.0a3 part8', 'STARTED', 1);

UPDATE `SYSTEMCONFIG` SET `webversiondate` = '2012-09-12';
UPDATE `SYSTEMCONFIG` SET `webversionnumber` = '3.3.0.3';
UPDATE `SYSTEMCONFIG` SET `webversionstring`= '3.3.0a3';

ALTER TABLE `ORDERHEADER` ADD `offlineordercompletedbycustomer` TINYINT( 1 ) NOT NULL DEFAULT '0' AFTER `offlineorder`;
ALTER TABLE `ORDERHEADER` ADD `giftcarddeleted` TINYINT( 1 ) NOT NULL DEFAULT '0' AFTER `total`; 
ALTER TABLE `ORDERHEADER` ADD `orderfootertotalnotaxnodiscount` DECIMAL( 10, 2 ) NOT NULL DEFAULT '0.00' AFTER `orderfootertotalnotax`;

ALTER TABLE `ORDERHEADER` MODIFY COLUMN `orderfootersubtotal` DECIMAL(10,2) NOT NULL DEFAULT '0.00';
ALTER TABLE `ORDERHEADER` MODIFY COLUMN `orderfootertotal` DECIMAL(10,2) NOT NULL DEFAULT '0.00';
ALTER TABLE `ORDERHEADER` MODIFY COLUMN `orderfooterdiscountvalue` DECIMAL(10,2) NOT NULL DEFAULT '0.00';

ALTER TABLE `ORDERITEMCOMPONENTS` ADD `componentdiscountedtax` DECIMAL( 10, 2 ) NOT NULL DEFAULT '0.00' AFTER `discountvalue`; 

ALTER TABLE `ORDERITEMCOMPONENTS` CHANGE `orderfootertaxrate` `componenttaxrate` DECIMAL( 10, 4 ) NOT NULL DEFAULT '0.0000';
ALTER TABLE `ORDERITEMCOMPONENTS` CHANGE `orderfootertaxname` `componenttaxname` VARCHAR( 1024 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;

INSERT INTO `ACTIVITYLOG`
	(`id`, `datecreated`, `sessionid`, `orderid`, `userid`, `userlogin`, `username`, `importance`, `sectioncode`, `actioncode`, `actionnotes`, `success`)
	VALUES (0, now(), 0, 0, 0, '', (SELECT USER()), 0, 'UPGRADE', '3.3.0a3 part8', 'FINISHED', 1);

#
# DDL END
#

SET FOREIGN_KEY_CHECKS = 1;
