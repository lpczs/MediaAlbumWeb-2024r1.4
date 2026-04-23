#
SET FOREIGN_KEY_CHECKS = 0;

#
# DDL START
#

INSERT INTO `ACTIVITYLOG`
	(`id`, `datecreated`, `sessionid`, `orderid`, `userid`, `userlogin`, `username`, `importance`, `sectioncode`, `actioncode`, `actionnotes`, `success`)
	VALUES (0, now(), 0, 0, 0, '', (SELECT USER()), 0, 'UPGRADE', '3.1.0a3', 'STARTED', 1);

ALTER TABLE `ORDERHEADER` ADD `showzerotax` TINYINT( 1 ) NOT NULL DEFAULT '0' AFTER `totalsellbeforediscount`;
ALTER TABLE `ORDERHEADER` ADD `showtaxbreakdown` TINYINT( 1 ) NOT NULL DEFAULT '0' AFTER `showzerotax`;
ALTER TABLE `ORDERHEADER` ADD `showalwaystaxtotal` TINYINT( 1 ) NOT NULL DEFAULT '0' AFTER `showtaxbreakdown`;
ALTER TABLE `ORDERHEADER` ADD `ordertotalshippingsellbeforediscount` DECIMAL( 10, 2 ) NOT NULL AFTER `showalwaystaxtotal`;

ALTER TABLE `ORDERITEMS` ADD `itemproductinfo` VARCHAR( 200 ) NOT NULL DEFAULT '' AFTER `productname`;
ALTER TABLE `ORDERITEMS` ADD `assetid` INT( 11 ) NOT NULL DEFAULT '0' AFTER `productwidth`;
ALTER TABLE `ORDERITEMS` ADD `itemproducttotalsellnotax` DEC( 10,2 ) NOT NULL DEFAULT '0.00' AFTER `producttotalsell`;
ALTER TABLE `ORDERITEMS` ADD `itemproducttotalsellwithtax` DEC( 10,2 ) NOT NULL DEFAULT '0.00' AFTER `itemproducttotalsellnotax`;

UPDATE `SYSTEMCONFIG` SET `webversiondate` = '2011-12-15';
UPDATE `SYSTEMCONFIG` SET `webversionnumber` = '3.1.0.3';
UPDATE `SYSTEMCONFIG` SET `webversionstring`= '3.1.0a3';

INSERT INTO `ACTIVITYLOG`
	(`id`, `datecreated`, `sessionid`, `orderid`, `userid`, `userlogin`, `username`, `importance`, `sectioncode`, `actioncode`, `actionnotes`, `success`)
	VALUES (0, now(), 0, 0, 0, '', (SELECT USER()), 0, 'UPGRADE', '3.1.0a3', 'FINISHED', 1);

#
# DDL END
#

SET FOREIGN_KEY_CHECKS = 1;
