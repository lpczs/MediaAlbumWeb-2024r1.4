#
SET FOREIGN_KEY_CHECKS = 0;

#
# DDL START
#


ALTER TABLE `LICENSEKEYS` 
	ADD COLUMN `modifyshippingaddress` TINYINT(1) NOT NULL DEFAULT 1 AFTER `useaddressforshipping`,
	ADD COLUMN `modifybillingaddress`  TINYINT(1) NOT NULL DEFAULT 1 AFTER `modifyshippingaddress`,
	ADD COLUMN `useremaildestination`  TINYINT(1) NOT NULL DEFAULT 0 AFTER `modifyshippingcontactdetails`,
	ADD COLUMN `showpriceswithtax`     TINYINT(1) NOT NULL DEFAULT 0 AFTER `useremaildestination`,
	ADD COLUMN `showtaxbreakdown`      TINYINT(1) NOT NULL DEFAULT 1 AFTER `showpriceswithtax`,
	ADD COLUMN `showzerotax`           TINYINT(1) NOT NULL DEFAULT 1 AFTER `showtaxbreakdown`,
	ADD COLUMN `showalwaystaxtotal`    TINYINT(1) NOT NULL DEFAULT 0 AFTER `showzerotax`;

ALTER TABLE `USERS` 
	ADD COLUMN `useremaildestination`  TINYINT(1) NOT NULL DEFAULT 0 AFTER `modifybillingaddress`,
	ADD COLUMN `defaultaddresscontrol` TINYINT(1) NOT NULL DEFAULT 1 AFTER `useremaildestination`;

ALTER TABLE `ORDERHEADER` 
	ADD COLUMN `useremaildestination`  TINYINT(1)  NOT NULL DEFAULT 0 AFTER `billingcontactlastname`,
	ADD COLUMN `paymentgatewaycode`    VARCHAR(20) NOT NULL DEFAULT '' COMMENT '' COLLATE utf8_general_ci AFTER `paymentmethodname`,
	ADD COLUMN `pricesincludetax`      TINYINT(1)  NOT NULL DEFAULT 0 AFTER `paymentgatewaycode`;

ALTER TABLE `ORDERITEMS` 
    ADD COLUMN `producttotaltax` Decimal(10,2) NOT NULL DEFAULT '0.00' AFTER `producttotalsell`,
    ADD COLUMN `covertotaltax`   Decimal(10,2) NOT NULL DEFAULT '0.00' AFTER `covertotalsell`,
    ADD COLUMN `papertotaltax`   Decimal(10,2) NOT NULL DEFAULT '0.00' AFTER `papertotalsell`;

ALTER TABLE `KEYWORDS` 
    MODIFY `flags` varchar(1024) NOT NULL;

ALTER TABLE `OUTPUTFORMATS` 
	ADD COLUMN `jobticketsubfoldernameformat` VARCHAR(100) NOT NULL DEFAULT '' COMMENT '' COLLATE utf8_general_ci,
	ADD COLUMN `pagessubfoldernameformat`     VARCHAR(100) NOT NULL DEFAULT '' COMMENT '' COLLATE utf8_general_ci,
	ADD COLUMN `cover1subfoldernameformat`    VARCHAR(100) NOT NULL DEFAULT '' COMMENT '' COLLATE utf8_general_ci,
	ADD COLUMN `cover2subfoldernameformat`    VARCHAR(100) NOT NULL DEFAULT '' COMMENT '' COLLATE utf8_general_ci;
          
ALTER TABLE `PRODUCTPRICES`
	ADD COLUMN `parentid` INTEGER NOT NULL DEFAULT 0 AFTER `datecreated`;
	
ALTER TABLE `PRODUCTPRICES` 
	ADD INDEX productcode(`productcode`),
	ADD INDEX groupcode(`groupcode`),
	ADD INDEX productandgroup(`productcode`, `groupcode`),
	ADD INDEX parentid(`parentid`);

ALTER TABLE `PAPERPRICES`
	ADD COLUMN `parentid` INTEGER NOT NULL DEFAULT 0 AFTER `datecreated`;
	
ALTER TABLE `PAPERPRICES`
	ADD INDEX productcode(`productcode`),
	ADD INDEX groupcode(`groupcode`),
	ADD INDEX productandgroup(`productcode`, `groupcode`),
	ADD INDEX parentid(`parentid`);

ALTER TABLE `COVERPRICES`
	ADD COLUMN `parentid` INTEGER NOT NULL DEFAULT 0 AFTER `datecreated`;

ALTER TABLE `COVERPRICES` 
	ADD INDEX productcode(`productcode`),
	ADD INDEX groupcode(`groupcode`),
	ADD INDEX productandgroup(`productcode`, `groupcode`),
	ADD INDEX parentid(`parentid`);

ALTER TABLE `SHIPPINGRATES`
	ADD COLUMN `groupcode` VARCHAR(50) NOT NULL DEFAULT '' COMMENT '' COLLATE utf8_general_ci AFTER `productcode`;
	
ALTER TABLE `SHIPPINGRATES` 
	ADD COLUMN `parentid` INTEGER NOT NULL DEFAULT 0 AFTER `datecreated`;
	
ALTER TABLE `SHIPPINGRATES` 
	ADD INDEX productcode(`productcode`),
	ADD INDEX groupcode(`groupcode`),
	ADD INDEX productandgroup(`productcode`, `groupcode`),
	ADD INDEX parentid(`parentid`);

ALTER TABLE `SHIPPINGRATES`
	DROP INDEX `code`,
	ADD INDEX code (`code`);

ALTER TABLE `SHIPPINGRATES` 
	ADD COLUMN `uniquecode` VARCHAR(40) NOT NULL DEFAULT '' COMMENT '' COLLATE utf8_general_ci AFTER `code`;

UPDATE `SHIPPINGRATES` 
	SET `uniquecode` = `code`;

ALTER TABLE `SHIPPINGRATES` 
	ADD UNIQUE INDEX uniquecode(`uniquecode`);

#
# DDL END
#

SET FOREIGN_KEY_CHECKS = 1;
