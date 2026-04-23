#
SET FOREIGN_KEY_CHECKS = 0;

#
# DDL START
#

INSERT INTO `ACTIVITYLOG`
	(`id`, `datecreated`, `sessionid`, `orderid`, `userid`, `userlogin`, `username`, `importance`, `sectioncode`, `actioncode`, `actionnotes`, `success`)
	VALUES (0, now(), 0, 0, 0, '', (SELECT USER()), 0, 'UPGRADE', '3.2.0b2part2', 'STARTED', 1);
	
UPDATE ORDERITEMCOMPONENTS oic SET `oic`.`pricingmodel` = (SELECT `cc`.`pricingmodel` FROM COMPONENTCATEGORIES cc where `oic`.`componentcategorycode` = `cc`.`code`);

ALTER TABLE `ORDERITEMCOMPONENTS` ADD COLUMN `assetservicecode` VARCHAR(50) NOT NULL AFTER `parentcomponentid`;
ALTER TABLE `ORDERITEMCOMPONENTS` ADD COLUMN `assetservicename` VARCHAR(1024) NOT NULL AFTER `assetservicecode`;
ALTER TABLE `ORDERITEMCOMPONENTS` ADD COLUMN `assetpricetype` INT NOT NULL DEFAULT 0 AFTER `assetservicename`;
ALTER TABLE `ORDERITEMCOMPONENTS` ADD COLUMN `assetunitsell` DECIMAL(10,2) NOT NULL DEFAULT 0.00 AFTER `componentunitsell`;
ALTER TABLE `ORDERITEMCOMPONENTS` ADD COLUMN `assetexpirydate` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' AFTER `branchtotaltax`;
ALTER TABLE `ORDERITEMCOMPONENTS` ADD COLUMN `assetpageref` INTEGER NOT NULL DEFAULT 0 AFTER `assetexpirydate`;
ALTER TABLE `ORDERITEMCOMPONENTS` ADD COLUMN `assetpagenumber` INTEGER NOT NULL DEFAULT 0 AFTER `assetpageref`;
ALTER TABLE `ORDERITEMCOMPONENTS` ADD COLUMN `assetpagename` VARCHAR(1024) NOT NULL AFTER `assetpagenumber`;
ALTER TABLE `ORDERITEMCOMPONENTS` ADD COLUMN `assetboxref` INTEGER NOT NULL DEFAULT 0 AFTER `assetpagename`;

ALTER TABLE `ORDERHEADER` ADD COLUMN `shippingtotalsellbeforediscount` DECIMAL(10,2) NOT NULL DEFAULT 0.00 AFTER `shippingtotalcost`;

UPDATE `ORDERHEADER` SET `shippingtotalsellbeforediscount` = `ordertotalshippingsellbeforediscount`;

ALTER TABLE `ORDERHEADER` DROP COLUMN `ordertotalshippingsellbeforediscount`;

ALTER TABLE `ORDERITEMS` CHANGE COLUMN `itemproductinfo` `productinfo` VARCHAR(200) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;

ALTER TABLE `ORDERITEMS` MODIFY COLUMN `productinfo` VARCHAR(1024) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;

ALTER TABLE `PRICELINK` MODIFY COLUMN `sortorder` INTEGER NOT NULL DEFAULT 0;

ALTER TABLE `ORDERITEMS` DROP COLUMN `itemproducttotalsellnotax`, DROP COLUMN `itemproducttotalsellwithtax`;

ALTER TABLE `ORDERITEMCOMPONENTS` MODIFY COLUMN `skucode` VARCHAR(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;

ALTER TABLE `PRODUCTS` MODIFY COLUMN `skucode` VARCHAR(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;



UPDATE `SYSTEMCONFIG` SET `webversiondate` = '2012-03-28';
UPDATE `SYSTEMCONFIG` SET `webversionnumber` = '3.2.0.4';
UPDATE `SYSTEMCONFIG` SET `webversionstring`= '3.2.0b2';

INSERT INTO `ACTIVITYLOG`
	(`id`, `datecreated`, `sessionid`, `orderid`, `userid`, `userlogin`, `username`, `importance`, `sectioncode`, `actioncode`, `actionnotes`, `success`)
	VALUES (0, now(), 0, 0, 0, '', (SELECT USER()), 0, 'UPGRADE', '3.2.0b2part2', 'FINISHED', 1);

#
# DDL END
#

SET FOREIGN_KEY_CHECKS = 1;
