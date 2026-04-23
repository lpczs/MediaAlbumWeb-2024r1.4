# Upgrade To 3.0.0a2 part 1 version 17 November 2010
#
SET FOREIGN_KEY_CHECKS = 0;

#
# DDL START
#


INSERT INTO `ACTIVITYLOG` 
	(`id`, `datecreated`, `sessionid`, `orderid`, `userid`, `userlogin`, `username`, `importance`, `sectioncode`, `actioncode`, `actionnotes`, `success`) 
	VALUES (0, now(), 0, 0, 0, '', (SELECT USER()), 0, 'UPGRADE', '3.0.0a2', 'PART 1 STARTED', 1);

CREATE TABLE `COMPANIES` (
  `id` INTEGER(11) NOT NULL auto_increment,
  `datecreated` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  `code` VARCHAR(50) NOT NULL DEFAULT '',
  `companyname` VARCHAR(200) NOT NULL DEFAULT '',
  `address1` VARCHAR(200) NOT NULL DEFAULT '',
  `address2` VARCHAR(200) NOT NULL DEFAULT '',
  `address3` VARCHAR(200) NOT NULL DEFAULT '',
  `address4` VARCHAR(200) NOT NULL DEFAULT '',
  `city` VARCHAR(200) NOT NULL DEFAULT '',
  `county` VARCHAR(50) NOT NULL DEFAULT '',
  `state` VARCHAR(200) NOT NULL DEFAULT '',
  `regioncode` VARCHAR(20) NOT NULL DEFAULT '',
  `region` VARCHAR(10) NOT NULL DEFAULT '',
  `postcode` VARCHAR(200) NOT NULL DEFAULT '',
  `countrycode` VARCHAR(10) NOT NULL DEFAULT '',
  `countryname` VARCHAR(50) NOT NULL DEFAULT '',
  `telephonenumber` VARCHAR(50) NOT NULL DEFAULT '',
  `emailaddress` VARCHAR(50) NOT NULL DEFAULT '',
  `contactfirstname` VARCHAR(200) NOT NULL DEFAULT '',
  `contactlastname` VARCHAR(200) NOT NULL DEFAULT '',
  `taxaddress` INTEGER(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
)
ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

ALTER TABLE `USERS` ADD COLUMN `usertype` INTEGER NOT NULL DEFAULT 0 AFTER `customer`;

UPDATE `USERS` SET `usertype` = IF(`administrator`= 1, IF(`owner`="", 0, 2), 3) WHERE `customer`=0;

ALTER TABLE `USERS` 
	DROP COLUMN `administrator`,
	ADD COLUMN `companycode` VARCHAR(50)  NOT NULL DEFAULT '' AFTER `datecreated`,
	ADD COLUMN `webbrandcode` VARCHAR(50) NOT NULL DEFAULT '' AFTER `companycode`;

ALTER TABLE `BRANDING` 		ADD COLUMN `companycode` VARCHAR(50)  NOT NULL DEFAULT '' AFTER `datecreated`;
ALTER TABLE `SITES` 		ADD COLUMN `companycode` VARCHAR(50)  NOT NULL DEFAULT '' AFTER `datecreated`;
ALTER TABLE `SYSTEMCONFIG` 	ADD COLUMN `systemkey`   VARCHAR(100) NOT NULL DEFAULT '' AFTER `datecreated`;
ALTER TABLE `TAXZONES` 		ADD COLUMN `companycode` VARCHAR(50)  NOT NULL DEFAULT '' AFTER `datecreated`;
ALTER TABLE `VOUCHERS` 		ADD COLUMN `companycode` VARCHAR(50)  NOT NULL DEFAULT '' AFTER `datecreated`;

ALTER TABLE `SITES` 
	CHANGE COLUMN `companyname` `name` VARCHAR(200) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
	CHANGE COLUMN `store` `sitetype` INTEGER NOT NULL DEFAULT 0,
	ADD COLUMN `siteonline` TINYINT(1) NOT NULL DEFAULT 1 AFTER `sitetype`,
	ADD COLUMN `distributioncentrecode` VARCHAR(50) NOT NULL DEFAULT '' AFTER `sitegroup`;

CREATE TABLE `OUTPUTFORMATSPRODUCTLINK` (
  `id` INTEGER(11) NOT NULL auto_increment,
  `datecreated` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  `owner` VARCHAR(50) NOT NULL DEFAULT '',
  `outputformatcode` VARCHAR(100) NOT NULL,
  `productcode` VARCHAR(50) NOT NULL,
  PRIMARY KEY  (`id`)
) 
ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

INSERT INTO `OUTPUTFORMATSPRODUCTLINK` (datecreated, outputformatcode, productcode) 
	SELECT NOW() , p.outputformat, p.code FROM PRODUCTS p WHERE p.outputformat <> "";

ALTER TABLE `PRODUCTS` DROP COLUMN `outputformat`;

ALTER TABLE `OUTPUTDEVICES` ADD COLUMN `owner` VARCHAR(50) NOT NULL AFTER `datecreated`;

ALTER TABLE `OUTPUTDEVICES` 
	MODIFY COLUMN `code` VARCHAR(100) NOT NULL,
	ADD COLUMN `localcode` VARCHAR(50) NOT NULL AFTER `code`;

ALTER TABLE `OUTPUTDEVICES` ADD COLUMN `datelastmodified` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP On Update CURRENT_TIMESTAMP AFTER `datecreated`;

UPDATE `OUTPUTDEVICES` SET `localcode` = `code`;

ALTER TABLE `OUTPUTFORMATS` 
	MODIFY COLUMN `code` VARCHAR(100) NOT NULL,
	ADD COLUMN `localcode` VARCHAR(50) NOT NULL AFTER `code`;

ALTER TABLE `OUTPUTFORMATS` ADD COLUMN `datelastmodified` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP On Update CURRENT_TIMESTAMP AFTER `datecreated`;

ALTER TABLE `OUTPUTFORMATS` 
	ADD COLUMN `sluginfocolour` VARCHAR(20) NOT NULL AFTER `cover2subfoldernameformat`,
	ADD COLUMN `cropmarkoffset` VARCHAR(30) NOT NULL AFTER `sluginfocolour`,
	ADD COLUMN `cropmarklength` VARCHAR(30) NOT NULL AFTER `cropmarkoffset`,
	ADD COLUMN `cropmarkwidth` VARCHAR(15) NOT NULL AFTER `cropmarklength`,
	ADD COLUMN `cropmarkborderwidth` VARCHAR(15) NOT NULL AFTER `cropmarkwidth`,
	ADD COLUMN `cropmarkcolour` VARCHAR(20) NOT NULL AFTER `cropmarkborderwidth`;

UPDATE OUTPUTFORMATS 
	SET sluginfocolour = "0,0,0,100", cropmarkoffset = "0", cropmarklength = "0", cropmarkwidth = "0.0", cropmarkborderwidth = "0.0", cropmarkcolour = "0,0,0,100", 
		leftpageoptions = CONCAT(leftpageoptions, "-0000"), rightpageoptions = CONCAT(rightpageoptions, "-0000"), 
		frontcoveroptions = CONCAT(frontcoveroptions, "-0000"), backcoveroptions = CONCAT(backcoveroptions, "-0000");

UPDATE `OUTPUTFORMATS` SET `localcode` = `code`;

ALTER TABLE `ORDERITEMS` 
	MODIFY COLUMN `convertoutputformatcode` VARCHAR(100) NOT NULL,
	MODIFY COLUMN `jobticketoutputdevicecode` VARCHAR(100) NOT NULL,
	MODIFY COLUMN `pagesoutputdevicecode` VARCHAR(100) NOT NULL,
	MODIFY COLUMN `cover1outputdevicecode` VARCHAR(100) NOT NULL,
	MODIFY COLUMN `cover2outputdevicecode` VARCHAR(100) NOT NULL,
	ADD COLUMN `origcompanycode` VARCHAR(50) NOT NULL DEFAULT '' AFTER `datelastmodified`,
	ADD COLUMN `currentcompanycode` VARCHAR(50) NOT NULL DEFAULT '' AFTER `origownertype`;

UPDATE `ORDERITEMS` SET `status` = 44 WHERE `status` = 43;

UPDATE `ORDERITEMS` SET `status` = 43 WHERE `status` = 41;

ALTER TABLE `ORDERSHIPPING` ADD COLUMN `distributioncentrecode` VARCHAR(50) NOT NULL DEFAULT '' AFTER `qty`;

ALTER TABLE `SHIPPINGMETHODS` 
	DROP COLUMN `owner`,
	ADD COLUMN `collectfromstore` TINYINT(1) NOT NULL DEFAULT 0 AFTER `default`;		

ALTER TABLE `SHIPPINGRATES` 
	ADD COLUMN `payinstoreallowed` INTEGER NOT NULL DEFAULT 0 AFTER `ordervalueincludesdiscount`;
	
ALTER TABLE `SHIPPINGMETHODS` 
	ADD COLUMN `sitegrouplabel` varchar(1024) NOT NULL DEFAULT '' AFTER `collectfromstore`,
	ADD COLUMN `allowgroupingbystoregroupname` TINYINT(1) NOT NULL DEFAULT 0 AFTER `sitegrouplabel`;

ALTER TABLE `SHIPPINGRATES` DROP COLUMN `sitegrouplabel`;

INSERT INTO `PAYMENTMETHODS` (`datecreated`, `code`, `name`, `availablewhenshipping`, `availablewhennotshipping`, `active`) 
	VALUES (now(), 'PAYINSTORE' , 'en Pay In Store' , 0 , 1 , 0);

# insert default company
INSERT INTO `COMPANIES` (`datecreated`, `code`, `countrycode`, `countryname`, `taxaddress`) 
	VALUES (now(), '', (SELECT `homecountrycode` FROM `CONSTANTS`), (SELECT `name` FROM `COUNTRIES` WHERE `isocode2` = (SELECT `homecountrycode` FROM `CONSTANTS`)), IF ((SELECT `taxaddress` FROM `CONSTANTS`) = 'B', 0, 1));

ALTER TABLE `CONSTANTS` 
	DROP COLUMN `taxaddress`,
	DROP COLUMN `homecountrycode`;
	
ALTER TABLE `SITES` ADD UNIQUE INDEX code(`code`);
ALTER TABLE `SITEGROUPS` ADD UNIQUE INDEX code(`code`);

ALTER TABLE `TAXZONES` 
	MODIFY COLUMN `code` VARCHAR(100) NOT NULL,
	ADD COLUMN `localcode` VARCHAR(50) NOT NULL AFTER `code`;

UPDATE `TAXZONES` SET `localcode` = `code`;

ALTER TABLE `SHIPPINGZONES` 
	MODIFY COLUMN `code` VARCHAR(100) NOT NULL,
	ADD COLUMN `localcode` VARCHAR(50) NOT NULL AFTER `code`;

UPDATE `SHIPPINGZONES` SET `localcode` = `code`;

ALTER TABLE `ORDERITEMS` ADD COLUMN `shippeddistributioncentrereceivedtimestamp` datetime NOT NULL default '0000-00-00 00:00:00' AFTER `shippingtrackingreference`;
ALTER TABLE `ORDERITEMS` ADD COLUMN `shippeddistributioncentrereceiveddate` datetime NOT NULL default '0000-00-00 00:00:00' AFTER `shippeddistributioncentrereceivedtimestamp`;
ALTER TABLE `ORDERITEMS` ADD COLUMN `shippeddistributioncentrereceiveduserid` int(11) NOT NULL default '0' AFTER `shippeddistributioncentrereceiveddate`;

ALTER TABLE `ORDERITEMS` ADD COLUMN `shippeddistributioncentreshippedtimestamp` datetime NOT NULL default '0000-00-00 00:00:00' AFTER `shippeddistributioncentrereceiveduserid`;
ALTER TABLE `ORDERITEMS` ADD COLUMN `shippeddistributioncentreshippeddate` datetime NOT NULL default '0000-00-00 00:00:00' AFTER `shippeddistributioncentreshippedtimestamp`;
ALTER TABLE `ORDERITEMS` ADD COLUMN `shippeddistributioncentreshippeduserid` int(11) NOT NULL default '0' AFTER `shippeddistributioncentreshippeddate`;

ALTER TABLE `ORDERITEMS` ADD COLUMN `shippedstorereceivedtimestamp` datetime NOT NULL default '0000-00-00 00:00:00' AFTER `shippeddistributioncentreshippeduserid`;
ALTER TABLE `ORDERITEMS` ADD COLUMN `shippedstorereceiveddate` datetime NOT NULL default '0000-00-00 00:00:00' AFTER `shippedstorereceivedtimestamp`;
ALTER TABLE `ORDERITEMS` ADD COLUMN `shippedstorereceiveduserid` int(11) NOT NULL default '0' AFTER `shippedstorereceiveddate`;

ALTER TABLE `ORDERITEMS` ADD COLUMN `shippedcustomercollectedtimestamp` datetime NOT NULL default '0000-00-00 00:00:00' AFTER `shippedstorereceiveduserid`;
ALTER TABLE `ORDERITEMS` ADD COLUMN `shippedcustomercollecteddate` datetime NOT NULL default '0000-00-00 00:00:00' AFTER `shippedcustomercollectedtimestamp`;
ALTER TABLE `ORDERITEMS` ADD COLUMN `shippedcustomercollectedtimestampuserid` int(11) NOT NULL default '0' AFTER `shippedcustomercollecteddate`;

# insert new export event
INSERT INTO `EXPORTEVENTS`  (`datecreated` ,`eventcode`  ,`language`  ,`exportformat`  ,`includepaymentdata`  ,`beautifiedxml`  ,`subfolderformat`  ,`filenameformat`  ,`active`)
VALUES (now(),'ORDERCREATED' , 		'Default' , 'XML' , 0 , 1 , '' , '' , 0);

# ADD companycode
ALTER TABLE `COVERS`	 		ADD COLUMN `companycode` VARCHAR(50) NOT NULL DEFAULT '' AFTER `datecreated`;
ALTER TABLE `LICENSEKEYS` 		ADD COLUMN `companycode` VARCHAR(50) NOT NULL DEFAULT '' AFTER `datecreated`;
ALTER TABLE `PAPER` 			ADD COLUMN `companycode` VARCHAR(50) NOT NULL DEFAULT '' AFTER `datecreated`;
ALTER TABLE `PRODUCTS` 			ADD COLUMN `companycode` VARCHAR(50) NOT NULL DEFAULT '' AFTER `datecreated`;
ALTER TABLE `SHIPPINGRATES` 	ADD COLUMN `companycode` VARCHAR(50) NOT NULL DEFAULT '' AFTER `datecreated`;
ALTER TABLE `SHIPPINGZONES` 	ADD COLUMN `companycode` VARCHAR(50) NOT NULL DEFAULT '' AFTER `datecreated`;
ALTER TABLE `VOUCHERPROMOTIONS` ADD COLUMN `companycode` VARCHAR(50) NOT NULL DEFAULT '' AFTER `datecreated`;

ALTER TABLE `PRODUCTPRICES`		ADD COLUMN `companycode` VARCHAR(50) NOT NULL DEFAULT '' AFTER `parentid`;
ALTER TABLE `PAPERPRICES`		ADD COLUMN `companycode` VARCHAR(50) NOT NULL DEFAULT '' AFTER `parentid`;
ALTER TABLE `COVERPRICES`		ADD COLUMN `companycode` VARCHAR(50) NOT NULL DEFAULT '' AFTER `parentid`;

INSERT INTO `ACTIVITYLOG` 
	(`id`, `datecreated`, `sessionid`, `orderid`, `userid`, `userlogin`, `username`, `importance`, `sectioncode`, `actioncode`, `actionnotes`, `success`) 
	VALUES (0, now(), 0, 0, 0, '', (SELECT USER()), 0, 'UPGRADE', '3.0.0a2', 'PART 1 FINISHED', 1);

#
# DDL END
#

SET FOREIGN_KEY_CHECKS = 1;
