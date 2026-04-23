#
SET FOREIGN_KEY_CHECKS = 0;

#
# DDL START
#


ALTER TABLE `PRODUCTPRICES` 
	ADD COLUMN `shoppingcarttype` TINYINT(1) NOT NULL DEFAULT 0 AFTER `groupcode`;

ALTER TABLE `ORDERHEADER` 
	ADD COLUMN `shoppingcarttype` TINYINT(1) NOT NULL DEFAULT 0 AFTER `groupcode`;

ALTER TABLE `ORDERHEADER` 
	ADD COLUMN `datelastmodified` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE NOW() AFTER `datecreated`;

UPDATE  `ORDERHEADER` 
	SET `datelastmodified` = IF (`statustimestamp` = '0000-00-00 00:0:00', `datecreated`, `statustimestamp`);

ALTER TABLE `ORDERITEMS` 
	ADD COLUMN `datelastmodified` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE NOW() AFTER `datecreated`;

UPDATE  `ORDERITEMS` 
	SET `datelastmodified` = IF (`statustimestamp` = '0000-00-00 00:0:00', `datecreated`, `statustimestamp`);

ALTER TABLE `ORDERSHIPPING` 
	ADD COLUMN `datelastmodified` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE NOW() AFTER `datecreated`;

UPDATE  `ORDERSHIPPING` 
	SET `datelastmodified` = `datecreated`;

ALTER TABLE `CONSTANTS` 
	MODIFY COLUMN `defaultpaymentmethods` VARCHAR(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;

ALTER TABLE `USERS` 
	MODIFY COLUMN `paymentmethods` VARCHAR(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;

ALTER TABLE `LICENSEKEYS` 
	ADD COLUMN `usedefaultpaymentmethods` TINYINT(1) NOT NULL DEFAULT 1 AFTER `currencycode`,
	ADD COLUMN `paymentmethods` VARCHAR(100) NOT NULL AFTER `usedefaultpaymentmethods`;

ALTER TABLE `BRANDING` 
	ADD COLUMN `usedefaultpaymentmethods` TINYINT(1) NOT NULL DEFAULT 1 AFTER `displayurl`,
	ADD COLUMN `paymentmethods` VARCHAR(100) NOT NULL AFTER `usedefaultpaymentmethods`,
	ADD COLUMN `paymentintegration` VARCHAR(20) NOT NULL DEFAULT 'DEFAULT' AFTER `paymentmethods`;

ALTER TABLE `CONSTANTS` 
	ADD COLUMN `defaultpaymentintegration` VARCHAR(20) NOT NULL DEFAULT 'NONE' AFTER `defaultpaymentmethods`;

ALTER TABLE `CCILOG` 
	ADD COLUMN `webbrandcode` VARCHAR(50) NOT NULL AFTER `currencycode`;

ALTER TABLE `SESSIONDATA` 
	ADD COLUMN `sessionrevived` TINYINT(1) NOT NULL DEFAULT 0 AFTER `sessionenabled`;

INSERT INTO `PAYMENTMETHODS` (`datecreated`, `code`, `name`, `availablewhenshipping`, `availablewhennotshipping`, `active`) 
	VALUES (NOW(),'PAYLATER','en Pay Later',1,1,0);

ALTER TABLE `ORDERHEADER` 
	ADD COLUMN `temporderid` INTEGER NOT NULL DEFAULT 0 AFTER `origordernumber`;

CREATE TABLE  `ORDERTEMP` (
  `id` int(11) NOT NULL auto_increment,
  `datecreated` datetime NOT NULL default '0000-00-00 00:00:00',
  `datelastmodified` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `dateexpires` datetime NOT NULL default '0000-00-00 00:00:00',
  `ownercode` varchar(50) NOT NULL,
  `groupcode` varchar(50) NOT NULL,
  `webbrandcode` varchar(50) NOT NULL,
  `languagecode` varchar(10) NOT NULL,
  `sessionid` int(11) NOT NULL default '0',
  `userid` int(11) NOT NULL default '0',
  `ref` VARCHAR(50) NOT NULL,
  `orderid` int(11) NOT NULL default '0',
  `orderdate` datetime NOT NULL default '0000-00-00 00:00:00',
  `ordernumber` varchar(50) NOT NULL,
  `orderitemid` int(11) NOT NULL default '0',
  `uploadref` varchar(200) NOT NULL,
  `projectname` varchar(200) NOT NULL,
  `productcode` varchar(50) NOT NULL,
  `productname` varchar(50) NOT NULL,
  `pagecount` int(11) NOT NULL default '0',
  `producttype` int(11) NOT NULL default '0',
  `productpageformat` int(11) NOT NULL default '0',
  `productspreadpageformat` int(11) NOT NULL default '0',
  `productcover1format` int(11) NOT NULL default '0',
  `productcover2format` int(11) NOT NULL default '0',
  `productoutputformat` int(11) NOT NULL default '0',
  `billingcustomeraccountcode` varchar(50) NOT NULL,
  `billingcustomername` varchar(200) NOT NULL,
  `billingcustomeraddress1` varchar(200) NOT NULL,
  `billingcustomeraddress2` varchar(200) NOT NULL,
  `billingcustomeraddress3` varchar(200) NOT NULL,
  `billingcustomeraddress4` varchar(200) NOT NULL,
  `billingcustomercity` varchar(200) NOT NULL,
  `billingcustomercounty` varchar(200) NOT NULL,
  `billingcustomerstate` varchar(200) NOT NULL,
  `billingcustomerpostcode` varchar(200) NOT NULL,
  `billingcustomercountrycode` varchar(2) NOT NULL,
  `billingcustomercountryname` varchar(64) NOT NULL,
  `billingcustomertelephonenumber` varchar(50) NOT NULL,
  `billingcustomeremailaddress` varchar(50) NOT NULL,
  `billingcontactfirstname` varchar(200) NOT NULL,
  `billingcontactlastname` varchar(200) NOT NULL,
  `shippingcustomername` varchar(200) NOT NULL,
  `shippingcustomeraddress1` varchar(200) NOT NULL,
  `shippingcustomeraddress2` varchar(200) NOT NULL,
  `shippingcustomeraddress3` varchar(200) NOT NULL,
  `shippingcustomeraddress4` varchar(200) NOT NULL,
  `shippingcustomercity` varchar(200) NOT NULL,
  `shippingcustomercounty` varchar(200) NOT NULL,
  `shippingcustomerstate` varchar(200) NOT NULL,
  `shippingcustomerpostcode` varchar(200) NOT NULL,
  `shippingcustomercountrycode` varchar(2) NOT NULL,
  `shippingcustomercountryname` varchar(64) NOT NULL,
  `shippingcustomertelephonenumber` varchar(50) NOT NULL,
  `shippingcustomeremailaddress` varchar(50) NOT NULL,
  `shippingcontactfirstname` varchar(200) NOT NULL,
  `shippingcontactlastname` varchar(200) NOT NULL,
  `uploaddatatype` int(11) NOT NULL default '0',
  `uploadmethod` int(11) NOT NULL default '0',
  `uploadappversion` varchar(20) NOT NULL,
  `uploadappplatform` varchar(20) NOT NULL,
  `canupload` tinyint(1) NOT NULL default '1',
  `canuploadpagecountoverride` tinyint(1) NOT NULL default '0',
  `filesreceivedtimestamp` datetime NOT NULL default '0000-00-00 00:00:00',
  `filesreceiveduserid` int(11) NOT NULL default '0',
  `previewsonline` tinyint(1) NOT NULL default '0',
  `status` int(11) NOT NULL default '0',
  `statusdescription` varchar(200) NOT NULL,
  `statustimestamp` datetime NOT NULL default '0000-00-00 00:00:00',
  `temporderstatus` int(11) NOT NULL default '0',
  `temporderstatustimestamp` datetime NOT NULL default '0000-00-00 00:00:00',
  `temporderstatususerid` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `uploadref` (`uploadref`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `ORDERTHUMBNAILS` (
  `id` int(11) NOT NULL auto_increment,
  `datecreated` datetime NOT NULL default '0000-00-00 00:00:00',
  `uploadref` varchar(200) NOT NULL,
  `pageref` varchar(50) NOT NULL,
  `pagename` varchar(1024) NOT NULL,
  `width` int(11) NOT NULL default '0',
  `height` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `uploadref` (`uploadref`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `ORDERITEMS` 
	ADD COLUMN `producttype` INTEGER NOT NULL DEFAULT 0 AFTER `productname`,
	ADD COLUMN `productpageformat` INTEGER NOT NULL DEFAULT 0 AFTER `producttype`,
	ADD COLUMN `productspreadpageformat` INTEGER NOT NULL DEFAULT 0 AFTER `productpageformat`,
	ADD COLUMN `productcover1format` INTEGER NOT NULL DEFAULT 0 AFTER `productspreadpageformat`,
	ADD COLUMN `productcover2format` INTEGER NOT NULL DEFAULT 0 AFTER `productcover1format`,
	ADD COLUMN `productoutputformat` INTEGER NOT NULL DEFAULT 0 AFTER `productcover2format`;

#
# DDL END
#

SET FOREIGN_KEY_CHECKS = 1;
