#
SET FOREIGN_KEY_CHECKS = 0;

#
# DDL START
#


ALTER TABLE `CONSTANTS` 
	ADD COLUMN `applicationname` VARCHAR(50) NOT NULL COLLATE utf8_general_ci AFTER `datecreated`,
	ADD COLUMN `smtpaddress` VARCHAR(100) NOT NULL COLLATE utf8_general_ci AFTER `defaultcreditlimit`,
	ADD COLUMN `smtpport` INT NOT NULL DEFAULT 25 AFTER `smtpaddress`,
	ADD COLUMN `smtpauthentication` TINYINT(1) NOT NULL DEFAULT 0 AFTER `smtpport`,
	ADD COLUMN `smtpauthenticateusername` VARCHAR(50) NOT NULL COLLATE utf8_general_ci AFTER `smtpauthentication`,
	ADD COLUMN `smtpauthenitcatepassword` VARCHAR(50) NOT NULL COLLATE utf8_general_ci AFTER `smtpauthenticateusername`,
	ADD COLUMN `smtpsystemfromname` VARCHAR(200) NOT NULL COLLATE utf8_general_ci AFTER `smtpauthenitcatepassword`,
	ADD COLUMN `smtpsystemfromaddress` VARCHAR(200) NOT NULL COLLATE utf8_general_ci AFTER `smtpsystemfromname`,
	ADD COLUMN `smtpsystemreplytoname` VARCHAR(200) NOT NULL COLLATE utf8_general_ci AFTER `smtpsystemfromaddress`,
	ADD COLUMN `smtpsystemreplytoaddress` VARCHAR(200) NOT NULL COLLATE utf8_general_ci AFTER `smtpsystemreplytoname`,
	ADD COLUMN `smtpadminname` VARCHAR(200) NOT NULL COLLATE utf8_general_ci AFTER `smtpsystemreplytoaddress`,
	ADD COLUMN `smtpadminaddress` VARCHAR(200) NOT NULL COLLATE utf8_general_ci AFTER `smtpadminname`,
	ADD COLUMN `smtpproductionname` VARCHAR(200) NOT NULL COLLATE utf8_general_ci AFTER `smtpadminaddress`,
	ADD COLUMN `smtpproductionaddress` VARCHAR(200) NOT NULL COLLATE utf8_general_ci AFTER `smtpproductionname`,
	ADD COLUMN `smtporderconfirmationname` VARCHAR(200) NOT NULL COLLATE utf8_general_ci AFTER `smtpproductionaddress`,
	ADD COLUMN `smtporderconfirmationaddress` VARCHAR(200) NOT NULL COLLATE utf8_general_ci AFTER `smtporderconfirmationname`,
	ADD COLUMN `smtpsaveordername` VARCHAR(200) NOT NULL COLLATE utf8_general_ci AFTER `smtporderconfirmationaddress`,
	ADD COLUMN `smtpsaveorderaddress` VARCHAR(200) NOT NULL COLLATE utf8_general_ci AFTER `smtpsaveordername`;

ALTER TABLE `BRANDING` 
	ADD COLUMN `weburl` VARCHAR(100) NOT NULL COLLATE utf8_general_ci AFTER `displayurl`;

ALTER TABLE `BRANDING` 
	ADD COLUMN `usedefaultemailsettings` TINYINT(1) NOT NULL DEFAULT 1 AFTER `paymentintegration`,
	ADD COLUMN `smtpaddress` VARCHAR(100) NOT NULL COLLATE utf8_general_ci AFTER `usedefaultemailsettings`,
	ADD COLUMN `smtpport` INT NOT NULL DEFAULT 25 AFTER `smtpaddress`,
	ADD COLUMN `smtpauthentication` TINYINT(1) NOT NULL DEFAULT 0 AFTER `smtpport`,
	ADD COLUMN `smtpauthenticateusername` VARCHAR(50) NOT NULL COLLATE utf8_general_ci AFTER `smtpauthentication`,
	ADD COLUMN `smtpauthenitcatepassword` VARCHAR(50) NOT NULL COLLATE utf8_general_ci AFTER `smtpauthenticateusername`,
	ADD COLUMN `smtpsystemfromname` VARCHAR(200) NOT NULL COLLATE utf8_general_ci AFTER `smtpauthenitcatepassword`,
	ADD COLUMN `smtpsystemfromaddress` VARCHAR(200) NOT NULL COLLATE utf8_general_ci AFTER `smtpsystemfromname`,
	ADD COLUMN `smtpsystemreplytoname` VARCHAR(200) NOT NULL COLLATE utf8_general_ci AFTER `smtpsystemfromaddress`,
	ADD COLUMN `smtpsystemreplytoaddress` VARCHAR(200) NOT NULL COLLATE utf8_general_ci AFTER `smtpsystemreplytoname`,
	ADD COLUMN `smtpadminname` VARCHAR(200) NOT NULL COLLATE utf8_general_ci AFTER `smtpsystemreplytoaddress`,
	ADD COLUMN `smtpadminaddress` VARCHAR(200) NOT NULL COLLATE utf8_general_ci AFTER `smtpadminname`,
	ADD COLUMN `smtpproductionname` VARCHAR(200) NOT NULL COLLATE utf8_general_ci AFTER `smtpadminaddress`,
	ADD COLUMN `smtpproductionaddress` VARCHAR(200) NOT NULL COLLATE utf8_general_ci AFTER `smtpproductionname`,
	ADD COLUMN `smtporderconfirmationname` VARCHAR(200) NOT NULL COLLATE utf8_general_ci AFTER `smtpproductionaddress`,
	ADD COLUMN `smtporderconfirmationaddress` VARCHAR(200) NOT NULL COLLATE utf8_general_ci AFTER `smtporderconfirmationname`,
	ADD COLUMN `smtpsaveordername` VARCHAR(200) NOT NULL COLLATE utf8_general_ci AFTER `smtporderconfirmationaddress`,
	ADD COLUMN `smtpsaveorderaddress` VARCHAR(200) NOT NULL COLLATE utf8_general_ci AFTER `smtpsaveordername`;

ALTER TABLE `PRODUCTS` 
	ADD COLUMN `sitecode` VARCHAR(50) NOT NULL COLLATE utf8_general_ci AFTER `datecreated`,
	ADD COLUMN `categorycode` VARCHAR(50) NOT NULL COLLATE utf8_general_ci AFTER `code`,
	ADD COLUMN `description` VARCHAR(1024) NOT NULL COLLATE utf8_general_ci AFTER `name`,
	ADD COLUMN `deleted` TINYINT(1) NOT NULL DEFAULT 0 AFTER `active`;

ALTER TABLE `LICENSEKEYS` 
	ADD COLUMN `sitecode` VARCHAR(50) NOT NULL COLLATE utf8_general_ci AFTER `datecreated`;

ALTER TABLE `APPLICATIONFILES` 
	MODIFY COLUMN `categoryname` VARCHAR(1024) NOT NULL,
	MODIFY COLUMN `name` VARCHAR(1024) NOT NULL ;

ALTER TABLE `ORDERITEMS` 
	MODIFY COLUMN `productname` VARCHAR(1024) NOT NULL ;
	
ALTER TABLE `ORDERTEMP` 
	MODIFY COLUMN `productname` VARCHAR(1024) NOT NULL ;
	
ALTER TABLE `PRODUCTPRICES` 
	MODIFY COLUMN `pricedescription` VARCHAR(1024) NOT NULL;

UPDATE `PRODUCTS` SET `categorycode` = UPPER(TRIM(`categoryname`))  WHERE `categoryname` <> '';
UPDATE `PRODUCTS` SET `categorycode` = REPLACE(`categorycode`, ' ', '');

UPDATE `VOUCHERS` SET `productcategorycode` = UPPER(TRIM(`productcategoryname`)) WHERE `productcategoryname` <> '';
UPDATE `VOUCHERS` SET `productcategorycode` = REPLACE(`productcategorycode`, ' ', '');

UPDATE `PRODUCTS` SET `categoryname`= IF (LEFT(`categoryname`,1) = "(", MID(`categoryname`, 5), `categoryname`);
UPDATE `PRODUCTS` SET `categorycode`= IF (LEFT(`categorycode`,1) = "(", MID(`categorycode`, 5), `categorycode`);

UPDATE `VOUCHERS` SET `productcategoryname`= IF (LEFT(`productcategoryname`,1) = "(", MID(`productcategoryname`, 5), `productcategoryname`);
UPDATE `VOUCHERS` SET `productcategorycode`= IF (LEFT(`productcategorycode`,1) = "(", MID(`productcategorycode`, 5), `productcategorycode`);

UPDATE `VOUCHERS` SET `productcategoryname` = CONCAT((SELECT `defaultlanguagecode` FROM `CONSTANTS`), " " , `productcategoryname`) WHERE `productcategoryname` <> '';

UPDATE `PRODUCTS` SET `name` = CONCAT((SELECT `defaultlanguagecode` FROM `CONSTANTS`), " " , `name`);
UPDATE `PRODUCTS` SET `categoryname` = CONCAT((SELECT `defaultlanguagecode` FROM `CONSTANTS`), " " , `categoryname`) WHERE `categoryname` <> '';

UPDATE `PRODUCTPRICES` SET `pricedescription` = CONCAT((SELECT `defaultlanguagecode` FROM `CONSTANTS`), " " , `pricedescription`) WHERE `pricedescription` <> '';

UPDATE `ORDERTEMP` SET `productname` = CONCAT((SELECT `defaultlanguagecode` FROM `CONSTANTS`), " " , `productname`);
UPDATE `ORDERITEMS` SET `productname` = CONCAT((SELECT `defaultlanguagecode` FROM `CONSTANTS`), " " , `productname`);

UPDATE `APPLICATIONFILES` SET `name` = CONCAT((SELECT `defaultlanguagecode` FROM `CONSTANTS`), " " , `name`);
UPDATE `APPLICATIONFILES` SET `categoryname` = CONCAT((SELECT `defaultlanguagecode` FROM `CONSTANTS`), " " , `categoryname`) WHERE `categoryname` <> '';

#
# DDL END
#

SET FOREIGN_KEY_CHECKS = 1;
