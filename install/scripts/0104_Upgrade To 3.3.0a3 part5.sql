#
SET FOREIGN_KEY_CHECKS = 0;

#
# DDL START
#

INSERT INTO `ACTIVITYLOG`
	(`id`, `datecreated`, `sessionid`, `orderid`, `userid`, `userlogin`, `username`, `importance`, `sectioncode`, `actioncode`, `actionnotes`, `success`)
	VALUES (0, now(), 0, 0, 0, '', (SELECT USER()), 0, 'UPGRADE', '3.3.0a3 part5', 'STARTED', 1);

UPDATE `SYSTEMCONFIG` SET `webversiondate` = '2012-08-31';
UPDATE `SYSTEMCONFIG` SET `webversionnumber` = '3.3.0.3';
UPDATE `SYSTEMCONFIG` SET `webversionstring`= '3.3.0a3';

ALTER TABLE `ORDERITEMCOMPONENTS` ADD `orderfootertaxrate` DECIMAL( 10, 4 ) NOT NULL DEFAULT '0.0000' AFTER `componentunitsell`;
ALTER TABLE `ORDERITEMCOMPONENTS` ADD `orderfootertaxname` VARCHAR( 1024 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER `orderfootertaxrate`;
ALTER TABLE `ORDERITEMCOMPONENTS` ADD `discountvalue` DECIMAL( 10, 2 ) NOT NULL DEFAULT '0.00' AFTER `componenttotaltax`;
ALTER TABLE `ORDERITEMCOMPONENTS` ADD `subtotal` DECIMAL( 10, 2 ) NOT NULL DEFAULT '0.00' AFTER `componenttotalcost`;
ALTER TABLE `ORDERHEADER` ADD `ordertotalitemsellwithtax` DECIMAL( 10, 2 ) NOT NULL DEFAULT '0.00' AFTER `totalsell`;

INSERT INTO `ACTIVITYLOG`
	(`id`, `datecreated`, `sessionid`, `orderid`, `userid`, `userlogin`, `username`, `importance`, `sectioncode`, `actioncode`, `actionnotes`, `success`)
	VALUES (0, now(), 0, 0, 0, '', (SELECT USER()), 0, 'UPGRADE', '3.3.0a3 part5', 'FINISHED', 1);

#
# DDL END
#

SET FOREIGN_KEY_CHECKS = 1;
