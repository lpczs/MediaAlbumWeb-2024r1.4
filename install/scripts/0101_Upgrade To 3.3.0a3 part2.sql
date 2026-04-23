#
SET FOREIGN_KEY_CHECKS = 0;

#
# DDL START
#

INSERT INTO `ACTIVITYLOG`
	(`id`, `datecreated`, `sessionid`, `orderid`, `userid`, `userlogin`, `username`, `importance`, `sectioncode`, `actioncode`, `actionnotes`, `success`)
	VALUES (0, now(), 0, 0, 0, '', (SELECT USER()), 0, 'UPGRADE', '3.3.0a3 part2', 'STARTED', 1);

UPDATE `SYSTEMCONFIG` SET `webversiondate` = '2012-08-22';
UPDATE `SYSTEMCONFIG` SET `webversionnumber` = '3.3.0.3';
UPDATE `SYSTEMCONFIG` SET `webversionstring`= '3.3.0a3';

ALTER TABLE `PRICES` ADD COLUMN `taxcode` VARCHAR(20) NOT NULL AFTER `ispricelist`;
ALTER TABLE `SHIPPINGRATES` ADD COLUMN `taxcode` VARCHAR(20) NOT NULL AFTER `payinstoreallowed`;

ALTER TABLE `ORDERITEMS` ADD COLUMN `discountname` VARCHAR(1024) NOT NULL DEFAULT '' AFTER `discountvalue`;

INSERT INTO `ACTIVITYLOG`
	(`id`, `datecreated`, `sessionid`, `orderid`, `userid`, `userlogin`, `username`, `importance`, `sectioncode`, `actioncode`, `actionnotes`, `success`)
	VALUES (0, now(), 0, 0, 0, '', (SELECT USER()), 0, 'UPGRADE', '3.3.0a3 part2', 'FINISHED', 1);

#
# DDL END
#

SET FOREIGN_KEY_CHECKS = 1;
