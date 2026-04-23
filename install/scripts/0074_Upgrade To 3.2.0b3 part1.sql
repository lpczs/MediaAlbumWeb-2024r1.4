#
SET FOREIGN_KEY_CHECKS = 0;

#
# DDL START
#

INSERT INTO `ACTIVITYLOG`
	(`id`, `datecreated`, `sessionid`, `orderid`, `userid`, `userlogin`, `username`, `importance`, `sectioncode`, `actioncode`, `actionnotes`, `success`)
	VALUES (0, now(), 0, 0, 0, '', (SELECT USER()), 0, 'UPGRADE', '3.2.0b3part1', 'STARTED', 1);

ALTER TABLE `ORDERITEMCOMPONENTS` CHANGE COLUMN `assetunitsell` `assetcharge` DECIMAL(10,2) NOT NULL DEFAULT '0.00';

ALTER TABLE `PRICES` ADD `pricelistlocalcode` VARCHAR( 50 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' AFTER `pricelistcode`;

UPDATE PRICES SET pricelistlocalcode = pricelistcode, pricelistcode = IF(companycode <> "", CONCAT(companycode,".",pricelistcode),pricelistcode) WHERE ispricelist =1;

UPDATE PRICES SET pricelistcode = CONCAT('CUSTOM',id) WHERE ispricelist = 0;

ALTER TABLE `PRICES` ADD UNIQUE INDEX pricelistcode USING BTREE(`pricelistcode`);

UPDATE `SYSTEMCONFIG` SET `webversiondate` = '2012-03-30';
UPDATE `SYSTEMCONFIG` SET `webversionnumber` = '3.2.0.5';
UPDATE `SYSTEMCONFIG` SET `webversionstring`= '3.2.0b3';

INSERT INTO `ACTIVITYLOG`
	(`id`, `datecreated`, `sessionid`, `orderid`, `userid`, `userlogin`, `username`, `importance`, `sectioncode`, `actioncode`, `actionnotes`, `success`)
	VALUES (0, now(), 0, 0, 0, '', (SELECT USER()), 0, 'UPGRADE', '3.2.0b3part1', 'FINISHED', 1);

#
# DDL END
#

SET FOREIGN_KEY_CHECKS = 1;
