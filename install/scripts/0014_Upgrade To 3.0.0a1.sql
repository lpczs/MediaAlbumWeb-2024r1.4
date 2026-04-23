#
SET FOREIGN_KEY_CHECKS = 0;

#
# DDL START
#


CREATE TABLE  `SITES` 
	(
		`id` INTEGER NOT NULL AUTO_INCREMENT,
		`datecreated` datetime NOT NULL default '0000-00-00 00:00:00',
		`owner` VARCHAR(50) NOT NULL default '',
		`code` VARCHAR(50) NOT NULL default '',
		`productionsitekey` VARCHAR(200) NOT NULL default '',
		`productionsitetype` INTEGER NOT NULL DEFAULT 0,
		`acceptallproducts` TINYINT(1) NOT NULL DEFAULT 1,
		`companyname` VARCHAR(200) NOT NULL default '',
		`address1` VARCHAR(200) NOT NULL default '',
		`address2` VARCHAR(200) NOT NULL default '',
		`address3` VARCHAR(200) NOT NULL default '',
		`address4` VARCHAR(200) NOT NULL default '',
		`city` VARCHAR(200) NOT NULL default '',
		`county` VARCHAR(50) NOT NULL default '',
		`state` VARCHAR(200) NOT NULL default '',
		`regioncode` VARCHAR(20) NOT NULL default '',
		`region` VARCHAR(10) NOT NULL default '',
		`postcode` VARCHAR(200) NOT NULL default '',
		`countrycode` VARCHAR(10) NOT NULL default '',
		`countryname` VARCHAR(50) NOT NULL default '',
		`locationlon` FLOAT(10,6) NOT NULL default '999',
		`locationlat` FLOAT(10,6) NOT NULL default '999',
		`telephonenumber` VARCHAR(50) NOT NULL default '',
		`emailaddress` VARCHAR(50) NOT NULL default '',
		`contactfirstname` VARCHAR(200) NOT NULL default '',
		`contactlastname` VARCHAR(200) NOT NULL default '',
		`store` TINYINT(1) NOT NULL default '0',
		`sitegroup` VARCHAR(50) NOT NULL default '',
		`openingtimes` VARCHAR(1024) NOT NULL default '',
		`storeurl` VARCHAR(1024) NOT NULL default '',
		`smtpproductionname` VARCHAR(200) NOT NULL default '',
		`smtpproductionaddress` VARCHAR(200) NOT NULL default '',
		`active` TINYINT(1) NOT NULL default '0',
		PRIMARY KEY  (`id`)
	) 
	ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

	CREATE TABLE `ORDERROUTING` 
	(
		`id` INTEGER NOT NULL AUTO_INCREMENT,
		`datecreated` datetime NOT NULL default '0000-00-00 00:00:00',
		`rule` INTEGER NOT NULL default 0,
		`condition` INTEGER NOT NULL default 0,
		`value` VARCHAR(50) NOT NULL default '',
		`sitecode` VARCHAR(50) NOT NULL default '',
		`priority` INTEGER NOT NULL default 0,
		PRIMARY KEY (`id`)
	) 
	ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
	
	CREATE TABLE `SITEPRODUCTS` 
	(
		`id` INT NOT NULL AUTO_INCREMENT,
		`datecreated` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
		`ownercode` VARCHAR(50) NOT NULL,
		`productcode` VARCHAR(50) NOT NULL,
		PRIMARY KEY (`id`),
		INDEX codes (`ownercode`, `productcode`)
	)
	ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

	CREATE TABLE `SHIPPINGRATESITES` 
	(
		`id` INT NOT NULL AUTO_INCREMENT,
		`datecreated` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
		`shippingratecode` VARCHAR(50) NOT NULL,
		`sitegroupcode` VARCHAR(50) NOT NULL,
		PRIMARY KEY (`id`),
		INDEX codes (`shippingratecode`, `sitegroupcode`)
	)
	ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

	CREATE TABLE `SITEGROUPS` 
	(
		`id` INT NOT NULL AUTO_INCREMENT,
		`datecreated` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
		`code` VARCHAR(50) NOT NULL,
		`name` VARCHAR(1024) NOT NULL,
		PRIMARY KEY (`id`)
	)
	ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
	
	CREATE TABLE `SYSTEMCONFIG` 
	(
		`id` INT NOT NULL AUTO_INCREMENT,
		`datecreated` DATETIME NOT NULL default '0000-00-00 00:00:00',
		`systemcertificate` VARCHAR(16384) NOT NULL,
		PRIMARY KEY (`id`)
	) 
	ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

	INSERT INTO `EXPORTEVENTS`  (`datecreated` ,`eventcode`  ,`language`  ,`exportformat`  ,`includepaymentdata`  ,`beautifiedxml`  ,`subfolderformat`  ,`filenameformat`  ,`active`)
	VALUES (now(),'ORDERREROUTE' , 		'Default' , 'XML' , 0 , 1 , '' , '' , 0);

	ALTER TABLE `BRANDING` ADD COLUMN `owner` VARCHAR(50) NOT NULL AFTER `datecreated`;
	ALTER TABLE `CONSTANTS` ADD COLUMN `config` INTEGER NOT NULL AFTER `smtpsaveorderaddress`;
	ALTER TABLE `COVERS` ADD COLUMN `owner` VARCHAR(50) NOT NULL AFTER `datecreated`;

	ALTER TABLE `LICENSEKEYS` 
		ADD COLUMN `owner` VARCHAR(50) NOT NULL AFTER `datecreated`,
		DROP COLUMN `sitecode`;

	ALTER TABLE `ORDERITEMS` 
		ADD COLUMN `origowner` VARCHAR(50) NOT NULL AFTER `datelastmodified`,
		ADD COLUMN `origownertype` INT NOT NULL DEFAULT 0 AFTER `origowner`,
		ADD COLUMN `currentowner` VARCHAR(50) NOT NULL AFTER `origownertype`,
		ADD COLUMN `currentownertype` INT NOT NULL DEFAULT 0 AFTER `currentowner`,
		ADD COLUMN `ownerorderkey` VARCHAR(50) NOT NULL AFTER `currentownertype`,
		ADD INDEX currentowner (`currentowner`);
		
	ALTER TABLE `ORDERSHIPPING` 
		ADD COLUMN `storecode` VARCHAR(50) NOT NULL AFTER `qty`,
		ADD COLUMN `storename` VARCHAR(200) NOT NULL AFTER `storecode`;	

	ALTER TABLE `OUTPUTFORMATS` ADD COLUMN `owner` VARCHAR(50) NOT NULL AFTER `datecreated`;
	ALTER TABLE `PAPER` ADD COLUMN `owner` VARCHAR(50) NOT NULL AFTER `datecreated`;

	ALTER TABLE `PRODUCTS` 
		ADD COLUMN `owner` VARCHAR(50) NOT NULL AFTER `datecreated`,
		DROP COLUMN `sitecode`;

	ALTER TABLE `SHIPPINGMETHODS` ADD COLUMN `owner` VARCHAR(50) NOT NULL AFTER `datecreated`;

	ALTER TABLE `SHIPPINGRATES` 
		ADD COLUMN `owner` VARCHAR(50) NOT NULL AFTER `datecreated`,
		ADD COLUMN `sitegrouplabel` varchar(1024) NOT NULL AFTER `ordervalueincludesdiscount`;		

	ALTER TABLE `SHIPPINGZONES` ADD COLUMN `owner` VARCHAR(50) NOT NULL AFTER `datecreated`;

	ALTER TABLE `USERS` 
		ADD COLUMN `owner` VARCHAR(50) NOT NULL AFTER `datecreated`,
		ADD INDEX owner (`owner`);

	ALTER TABLE `VOUCHERPROMOTIONS` 
		ADD COLUMN `owner` VARCHAR(50) NOT NULL AFTER `datecreated`,
		ADD INDEX owner (`owner`);

	ALTER TABLE `VOUCHERS` 
		ADD COLUMN `owner` VARCHAR(50) NOT NULL AFTER `datecreated`,
		ADD INDEX owner (`owner`);

#
# DDL END
#

SET FOREIGN_KEY_CHECKS = 1;
