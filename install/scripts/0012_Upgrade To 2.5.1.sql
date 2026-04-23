#
SET FOREIGN_KEY_CHECKS = 0;

#
# DDL START
#


ALTER TABLE `ORDERHEADER` 
	ADD COLUMN `billingcustomerregioncode` VARCHAR(20) CHARACTER SET utf8 NOT NULL AFTER `billingcustomerstate`,
	ADD COLUMN `billingcustomerregion` VARCHAR(10) CHARACTER SET utf8 NOT NULL AFTER `billingcustomerregioncode`;

ALTER TABLE `ORDERTEMP` 
	ADD COLUMN `billingcustomerregioncode` VARCHAR(20) CHARACTER SET utf8 NOT NULL AFTER `billingcustomerstate`,
	ADD COLUMN `billingcustomerregion` VARCHAR(10) CHARACTER SET utf8 NOT NULL AFTER `billingcustomerregioncode`,
	ADD COLUMN `shippingcustomerregioncode` VARCHAR(20) CHARACTER SET utf8 NOT NULL AFTER `shippingcustomerstate`,
	ADD COLUMN `shippingcustomerregion` VARCHAR(10) CHARACTER SET utf8 NOT NULL AFTER `shippingcustomerregioncode`;

ALTER TABLE `ORDERSHIPPING` 
	ADD COLUMN `shippingcustomerregioncode` VARCHAR(20) CHARACTER SET utf8 NOT NULL AFTER `shippingcustomerstate`,
	ADD COLUMN `shippingcustomerregion` VARCHAR(10) CHARACTER SET utf8 NOT NULL AFTER `shippingcustomerregioncode`;

ALTER TABLE `USERS` 
	ADD COLUMN `regioncode` VARCHAR(20) CHARACTER SET utf8 NOT NULL AFTER `state`,
	ADD COLUMN `region` VARCHAR(10) CHARACTER SET utf8 NOT NULL AFTER `regioncode`,
	ADD COLUMN `addressupdated` TINYINT(1) NOT NULL DEFAULT 0 AFTER `region`;

ALTER TABLE `LICENSEKEYS` 
	ADD COLUMN `regioncode` VARCHAR(20) CHARACTER SET utf8 NOT NULL AFTER `state`,
	ADD COLUMN `region` VARCHAR(10) CHARACTER SET utf8 NOT NULL AFTER `regioncode`;

ALTER TABLE `COUNTRIES` 
	ADD COLUMN `region` VARCHAR(10) CHARACTER SET utf8 NOT NULL DEFAULT 'STATE' AFTER `isocode3`,
	ADD COLUMN `displayfields` VARCHAR(256) CHARACTER SET utf8 NOT NULL AFTER `region`,
	ADD COLUMN `compulsoryfields` VARCHAR(256) CHARACTER SET utf8 NOT NULL AFTER `displayfields`,
	ADD COLUMN `displayformat` VARCHAR(256) CHARACTER SET utf8 NOT NULL AFTER `compulsoryfields`,
	ADD COLUMN `fieldlabels` VARCHAR(1024) CHARACTER SET utf8 NOT NULL AFTER `displayformat`;

UPDATE `COUNTRIES`
	SET `displayfields`    = 'country,firstname,lastname,company,add1,add2,add3,add4,city,county,postcode', 
		`fieldlabels`      = 'str_LabelCountry,str_LabelFirstName,str_LabelLastName,str_LabelCompanyName,str_LabelAddressLine1,str_LabelAddressLine2,str_LabelAddressLine3,str_LabelAddressLine4,str_LabelTownCity,str_LabelCounty,str_LabelPostCode', 
		`compulsoryfields` = 'country,firstname,lastname,add1,city,county,postcode', 
		`displayformat`    = '[firstname] [lastname]<br>[company]<br>[add1]<br>[add2]<br>[add3]<br>[add4]<br>[city]<br>[county]<br>[postcode]<br>[country]',
		`region`           = 'COUNTY'
	WHERE `isocode2`='GB';

UPDATE `COUNTRIES`
	SET `displayfields`    =  'country,firstname,lastname,company,add1,add2,city,state,county,postcode', 
		`fieldlabels`      = 'str_LabelCountry,str_LabelFirstName,str_LabelLastName,str_LabelCompanyName,str_LabelAddressLine1,str_LabelAddressLine2,str_LabelTownCity,str_LabelState,str_LabelCounty,str_LabelZIPCode', 
		`compulsoryfields` = 'country,firstname,lastname,add1,city,state,postcode', 
		`displayformat`    = '[firstname] [lastname]<br>[company]<br>[add1]<br>[add2]<br>[city] [regioncode] [postcode]<br>[country]'
	WHERE `isocode2`='US';

UPDATE `COUNTRIES`
	SET `displayfields`    = 'country,firstname,lastname,company,add1,add2,city,state,postcode', 
		`fieldlabels`      = 'str_LabelCountry,str_LabelFirstName,str_LabelLastName,str_LabelCompanyName,str_LabelAddressLine1,str_LabelAddressLine2,str_LabelTownCity,str_LabelProvince,str_LabelPostalCode', 
		`compulsoryfields` = 'country,firstname,lastname,add1,city,state,postcode',
		`displayformat`    = '[firstname] [lastname]<br>[company]<br>[add1]<br>[add2]<br>[city] [regioncode]  [postcode]<br>[country]'
	WHERE `isocode2`='CA';

UPDATE `COUNTRIES`
	SET `displayfields`    = 'country,firstname,lastname,company,add1,add2,add3,add4,city,county,postcode', 
		`fieldlabels`      = 'str_LabelCountry,str_LabelFirstName,str_LabelLastName,str_LabelCompanyName,str_LabelAddressLine1,str_LabelAddressLine2,str_LabelAddressLine3,str_LabelAddressLine4,str_LabelTownCity,str_LabelCounty,str_LabelPostCode', 
		`compulsoryfields` = 'country,firstname,lastname,add1,city,postcode', 
		`displayformat`    = '[firstname] [lastname]<br>[company]<br>[add1]<br>[add2]<br>[add3]<br>[add4]<br>[city] [postcode]<br>[county]<br>[country]',
		`region`           = 'COUNTY'
	WHERE `isocode2`='IE';

CREATE TABLE `COUNTRYREGIONGROUP` 
	(
	  `id` INTEGER NOT NULL AUTO_INCREMENT,
	  `datecreated` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
	  `countrycode` CHAR(2) CHARACTER SET utf8 NOT NULL,
	  `sortorder` CHAR(4) CHARACTER SET utf8 NOT NULL DEFAULT '0000',
	  `regiongroupcode` VARCHAR(50) CHARACTER SET utf8 NOT NULL,
	  `regiongroupname` VARCHAR(1024) CHARACTER SET utf8 NOT NULL,
	  PRIMARY KEY (`id`)
	)
	ENGINE = InnoDB CHARACTER SET utf8;

INSERT INTO `COUNTRYREGIONGROUP` (`datecreated`, `countrycode`, `sortorder`, `regiongroupcode`, `regiongroupname`)
	VALUES 
		(now(), 'GB','0001', 'EN',"en England"),
		(now(), 'GB','0002', 'SC',"en Scotland"),
		(now(), 'GB','0003', 'WA',"en Wales"),
		(now(), 'GB','0004', 'NI',"en Northern Ireland");

CREATE TABLE `COUNTRYREGION` 
	(
	  `id` INTEGER NOT NULL AUTO_INCREMENT,
	  `datecreated` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
	  `countrycode` CHAR(2) CHARACTER SET utf8 NOT NULL,
	  `regioncode` VARCHAR(50) CHARACTER SET utf8 NOT NULL,
	  `regionname` VARCHAR(1024) CHARACTER SET utf8 NOT NULL,
	  `regiongroupcode` VARCHAR(50) CHARACTER SET utf8 NOT NULL DEFAULT '',
	  PRIMARY KEY (`id`)
	)
	ENGINE = InnoDB CHARACTER SET utf8;

INSERT INTO `COUNTRYREGION` (`datecreated`, `countrycode`, `regioncode`, `regionname`, `regiongroupcode`)
VALUES 
	(now(), 'CA','AB', 'en Alberta<p>fr Alberta', ''),
	(now(), 'CA','BC', 'en British Columbia<p>fr Colombie-Britannique', ''),
	(now(), 'CA','MB', 'en Manitoba<p>fr Manitoba', ''),
	(now(), 'CA','NB', 'en New Brunswick<p>fr Nouveau-Brunswick', ''),
	(now(), 'CA','NL', 'en Newfoundland and Labrador<p>fr Terre-Neuve-et-Labrador', ''),
	(now(), 'CA','NS', 'en Nova Scotia<p>fr Nouvelle-Écosse', ''),
	(now(), 'CA','NU', 'en Nunavut<p>fr Nunavut', ''),
	(now(), 'CA','NWT','en Northwest Territories<p>fr Territoires-du-Nord-Ouest', ''),
	(now(), 'CA','ON', 'en Ontario<p>fr Ontario', ''),
	(now(), 'CA','PE', 'en Prince Edward Island<p>fr Île-du-Prince-Édouard', ''),
	(now(), 'CA','QC', 'en Quebec<p>fr Québec', ''),
	(now(), 'CA','SK', 'en Saskatchewan<p>fr Saskatchewan', ''),
	(now(), 'CA','YT', 'en Yukon<p>fr Yukon', '');

	

INSERT INTO `COUNTRYREGION` (`datecreated`, `countrycode`, `regioncode`, `regionname`)
VALUES 
	(now(), 'US','AL','en Alabama' ),
	(now(), 'US','AK','en Alaska' ),
	(now(), 'US','AZ','en Arizona' ),
	(now(), 'US','AR','en Arkansas' ),
	(now(), 'US','CA','en California' ),
	(now(), 'US','CO','en Colorado' ),
	(now(), 'US','CT','en Connecticut' ),
	(now(), 'US','DE','en Delaware' ),
	(now(), 'US','FL','en Florida' ),
	(now(), 'US','GA','en Georgia' ),
	(now(), 'US','HI','en Hawaii' ),
	(now(), 'US','ID','en Idaho' ),
	(now(), 'US','IL','en Illinois' ),
	(now(), 'US','IN','en Indiana' ),
	(now(), 'US','IA','en Iowa' ),
	(now(), 'US','KS','en Kansas' ),
	(now(), 'US','KY','en Kentucky' ),
	(now(), 'US','LA','en Louisiana<p>fr Louisiane' ),
	(now(), 'US','ME','en Maine' ),
	(now(), 'US','MD','en Maryland' ),
	(now(), 'US','MA','en Massachusetts' ),
	(now(), 'US','MI','en Michigan' ),
	(now(), 'US','MN','en Minnesota' ),
	(now(), 'US','MS','en Mississippi' ),
	(now(), 'US','MO','en Missouri' ),
	(now(), 'US','MT','en Montana' ),
	(now(), 'US','NE','en Nebraska' ),
	(now(), 'US','NV','en Nevada' ),
	(now(), 'US','NH','en New Hampshire' ),
	(now(), 'US','NJ','en New Jersey' ),
	(now(), 'US','NM','en New Mexico' ),
	(now(), 'US','NY','en New York' ),
	(now(), 'US','NC','en North Carolina' ),
	(now(), 'US','ND','en North Dakota' ),
	(now(), 'US','OH','en Ohio' ),
	(now(), 'US','OK','en Oklahoma' ),
	(now(), 'US','OR','en Oregon' ),
	(now(), 'US','PA','en Pennsylvania' ),
	(now(), 'US','RI','en Rhode Island' ),
	(now(), 'US','SC','en South Carolina' ),
	(now(), 'US','SD','en South Dakota' ),
	(now(), 'US','TN','en Tennessee' ),
	(now(), 'US','TX','en Texas' ),
	(now(), 'US','UT','en Utah' ),
	(now(), 'US','VT','en Vermont' ),
	(now(), 'US','VA','en Virginia' ),
	(now(), 'US','WA','en Washington' ),
	(now(), 'US','WV','en West Virginia' ),
	(now(), 'US','WI','en Wisconsin' ),
	(now(), 'US','WY','en Wyoming' );

INSERT INTO `COUNTRYREGION` (`datecreated`, `countrycode`, `regioncode`, `regionname`, `regiongroupcode`)
VALUES 
	(now(), 'GB','BEDFORDSHIRE','en Bedfordshire','EN'),
	(now(), 'GB','BERKSHIRE','en Berkshire','EN'),
	(now(), 'GB','BUCKINGHAMSHIRE','en Buckinghamshire','EN'),
	(now(), 'GB','CAMBRIDGESHIRE','en Cambridgeshire','EN'),
	(now(), 'GB','CHESHIRE','en Cheshire','EN'),
	(now(), 'GB','CORNWALL','en Cornwall','EN'),
	(now(), 'GB','CUMBERLAND','en Cumberland','EN'),
	(now(), 'GB','DERBYSHIRE','en Derbyshire','EN'),
	(now(), 'GB','DEVON','en Devon','EN'),
	(now(), 'GB','DORSET','en Dorset','EN'),
	(now(), 'GB','DURHAM','en Durham','EN'),
	(now(), 'GB','ESSEX','en Essex','EN'),
	(now(), 'GB','GLOUCESTERSHIRE','en Gloucestershire','EN'),
	(now(), 'GB','HAMPSHIRE','en Hampshire','EN'),
	(now(), 'GB','HEREFORDSHIRE','en Herefordshire','EN'),
	(now(), 'GB','HERTFORDSHIRE','en Hertfordshire','EN'),
	(now(), 'GB','HUNTINGDONSHIRE','en Huntingdonshire','EN'),
	(now(), 'GB','KENT','en Kent','EN'),
	(now(), 'GB','LANCASHIRE','en Lancashire','EN'),
	(now(), 'GB','LEICESTERSHIRE','en Leicestershire','EN'),
	(now(), 'GB','LINCOLNSHIRE','en Lincolnshire','EN'),
	(now(), 'GB','MIDDLESEX','en Middlesex','EN'),
	(now(), 'GB','NORFOLK','en Norfolk','EN'),
	(now(), 'GB','NORTHAMPTONSHIRE','en Northamptonshire','EN'),
	(now(), 'GB','NORTHUMBERLAND','en Northumberland','EN'),
	(now(), 'GB','NOTTINGHAMSHIRE','en Nottinghamshire','EN'),
	(now(), 'GB','OXFORDSHIRE','en Oxfordshire','EN'),
	(now(), 'GB','RUTLAND','en Rutland','EN'),
	(now(), 'GB','SHROPSHIRE','en Shropshire','EN'),
	(now(), 'GB','SOMERSET','en Somerset','EN'),
	(now(), 'GB','STAFFORDSHIRE','en Staffordshire','EN'),
	(now(), 'GB','SUFFOLK','en Suffolk','EN'),
	(now(), 'GB','SURREY','en Surrey','EN'),
	(now(), 'GB','SUSSEX','en Sussex','EN'),
	(now(), 'GB','TYNEANDWEAR','en Tyne and Wear','EN'),
	(now(), 'GB','WARWICKSHIRE','en Warwickshire','EN'),
	(now(), 'GB','WESTMORLAND','en Westmorland','EN'),
	(now(), 'GB','WILTSHIRE','en Wiltshire','EN'),
	(now(), 'GB','WORCESTERSHIRE','en Worcestershire','EN'),
	(now(), 'GB','YORKSHIRE','en Yorkshire','EN'),
	(now(), 'GB','ANTRIM','en Antrim','NI'),
	(now(), 'GB','ARMAGH','en Armagh','NI'),
	(now(), 'GB','DOWN','en Down','NI'),
	(now(), 'GB','FERMANAGH','en Fermanagh','NI'),
	(now(), 'GB','LONDONDERRY','en Londonderry','NI'),
	(now(), 'GB','TYRONE','en Tyrone','NI'),
	(now(), 'GB','ABERDEENSHIRE','en Aberdeenshire','SC'),
	(now(), 'GB','ANGUS','en Angus','SC'),
	(now(), 'GB','ARGYLLSHIRE','en Argyllshire','SC'),
	(now(), 'GB','AYRSHIRE','en Ayrshire','SC'),
	(now(), 'GB','BANFFSHIRE','en Banffshire','SC'),
	(now(), 'GB','BERWICKSHIRE','en Berwickshire','SC'),
	(now(), 'GB','BUTESHIRE','en Buteshire','SC'),
	(now(), 'GB','CAITHNESS','en Caithness','SC'),
	(now(), 'GB','CLACKMANNANSHIRE','en Clackmannanshire','SC'),
	(now(), 'GB','CROMARTYSHIRE','en Cromartyshire','SC'),
	(now(), 'GB','DUMFRIESSHIRE','en Dumfriesshire','SC'),
	(now(), 'GB','DUNBARTONSHIRE','en Dunbartonshire','SC'),
	(now(), 'GB','EASTLOTHIAN','en East Lothian','SC'),
	(now(), 'GB','FIFE','en Fife','SC'),
	(now(), 'GB','INVERNESSSHIRE','en Inverness-shire','SC'),
	(now(), 'GB','KINCARDINESHIRE','en Kincardineshire','SC'),
	(now(), 'GB','KINROSSSHIRE','en Kinross-shire','SC'),
	(now(), 'GB','KIRKCUDBRIGHTSHIRE','en Kirkcudbrightshire','SC'),
	(now(), 'GB','LANARKSHIRE','en Lanarkshire','SC'),
	(now(), 'GB','MIDLOTHIAN','en Midlothian','SC'),
	(now(), 'GB','MORAYSHIRE','en Morayshire','SC'),
	(now(), 'GB','NAIRNSHIRE','en Nairnshire','SC'),
	(now(), 'GB','ORKNEY','en Orkney','SC'),
	(now(), 'GB','PEEBLESSHIRE','en Peeblesshire','SC'),
	(now(), 'GB','PERTHSHIRE','en Perthshire','SC'),
	(now(), 'GB','RENFREWSHIRE','en Renfrewshire','SC'),
	(now(), 'GB','ROSSSHIRE','en Ross-shire','SC'),
	(now(), 'GB','ROXBURGHSHIRE','en Roxburghshire','SC'),
	(now(), 'GB','SELKIRKSHIRE','en Selkirkshire','SC'),
	(now(), 'GB','SHETLAND','en Shetland','SC'),
	(now(), 'GB','STIRLINGSHIRE','en Stirlingshire','SC'),
	(now(), 'GB','SUTHERLAND','en Sutherland','SC'),
	(now(), 'GB','WESTLOTHIAN','en West Lothian','SC'),
	(now(), 'GB','WIGTOWNSHIRE','en Wigtownshire','SC'),
	(now(), 'GB','ANGLESEY','en Anglesey','WA'),
	(now(), 'GB','BRECKNOCKSHIRE','en Brecknockshire','WA'),
	(now(), 'GB','CAERNARFONSHIRE','en Caernarfonshire','WA'),
	(now(), 'GB','CARDIGANSHIRE','en Cardiganshire','WA'),
	(now(), 'GB','CARMARTHENSHIRE','en Carmarthenshire','WA'),
	(now(), 'GB','DENBIGHSHIRE','en Denbighshire','WA'),
	(now(), 'GB','FLINTSHIRE','en Flintshire','WA'),
	(now(), 'GB','GLAMORGAN','en Glamorgan','WA'),
	(now(), 'GB','MERIONETH','en Merioneth','WA'),
	(now(), 'GB','MONMOUTHSHIRE','en Monmouthshire','WA'),
	(now(), 'GB','MONTGOMERYSHIRE','en Montgomeryshire','WA'),
	(now(), 'GB','PEMBROKESHIRE','en Pembrokeshire','WA'),
	(now(), 'GB','RADNORSHIRE','en Radnorshire','WA');

CREATE TABLE  `EXPORTEVENTS` 
	(
		`id` INTEGER NOT NULL AUTO_INCREMENT,
		`datecreated` datetime NOT NULL default '0000-00-00 00:00:00',
		`eventcode` varchar(20) NOT NULL default '',
		`language` varchar(10) NOT NULL,
		`exportformat` varchar(5) NOT NULL default '0',
		`includepaymentdata` tinyint(1) NOT NULL default '0',
		`beautifiedxml` tinyint(1) NOT NULL default '0',
		`subfolderformat` varchar(100) NOT NULL default '',
		`filenameformat` varchar(100) NOT NULL default '',
		`active` tinyint(1) NOT NULL default '0',
		PRIMARY KEY  (`id`)
	) 
	ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `EXPORTEVENTS`  (`datecreated` ,`eventcode`  ,`language`  ,`exportformat`  ,`includepaymentdata`  ,`beautifiedxml`  ,`subfolderformat`  ,`filenameformat`  ,`active`)
VALUES
	(now(),'DECRYPTEDFILES' , 		'Default' , 'XML' , 0 , 1 , '' , '' , 0),
	(now(),'CONVERTEDFILES' , 		'Default' , 'XML' , 0 , 1 , '' , '' , 0),
	(now(),'FILESPRINTED' , 		'Default' , 'XML' , 0 , 1 , '' , '' , 0),
	(now(),'FINISHINGCOMPLETE' ,	'Default' , 'XML' , 0 , 1 , '' , '' , 0),
	(now(),'SHIPPED' , 				'Default' , 'XML' , 0 , 1 , '' , '' , 0),
	(now(),'ORDERCOMPLETE' , 		'Default' , 'XML' , 0 , 1 , '' , '' , 0),
	(now(),'ORDERCANCELLED' , 		'Default' , 'XML' , 0 , 1 , '' , '' , 0),
	(now(),'ORDERACTIVATE' , 		'Default' , 'XML' , 0 , 1 , '' , '' , 0),
	(now(),'TEMPORDERCANCELLED' , 	'Default' , 'XML' , 0 , 1 , '' , '' , 0),
	(now(),'CUSTOMERADD' , 			'Default' , 'XML' , 0 , 1 , '' , '' , 0),
	(now(),'CUSTOMEREDIT' , 		'Default' , 'XML' , 0 , 1 , '' , '' , 0),
	(now(),'PASSWORDRESET' , 		'Default' , 'XML' , 0 , 1 , '' , '' , 0),
	(now(),'CUSTOMERDELETE' , 		'Default' , 'XML' , 0 , 1 , '' , '' , 0),
	(now(),'CUSTOMERACTIVATE' , 	'Default' , 'XML' , 0 , 1 , '' , '' , 0);


ALTER TABLE `TAXZONES` 
	MODIFY COLUMN `countrycodes` VARCHAR(1024) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;

ALTER TABLE `TAXZONES` 
	ADD COLUMN `usetaxscript` TINYINT(1) NOT NULL DEFAULT 0 AFTER `name`;	

ALTER TABLE `TAXZONES` 
	ADD COLUMN `useverifyscript` TINYINT(1) NOT NULL DEFAULT 0 AFTER `usetaxscript`;	

ALTER TABLE `CONSTANTS` 
	ADD COLUMN `taxaddress` CHAR(1) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT "B" AFTER `defaultcreditlimit`;

ALTER TABLE `ORDERITEMS` 
	MODIFY COLUMN `taxrate` DECIMAL(10,4) NOT NULL DEFAULT '0.00';

ALTER TABLE `ORDERSHIPPING` 
	MODIFY COLUMN `shippingratetaxrate` DECIMAL(10,4) NOT NULL DEFAULT '0.00';
	
ALTER TABLE `TAXRATES` 
	MODIFY COLUMN `rate` DECIMAL(10,4) NOT NULL DEFAULT '0.00';

ALTER TABLE `CONSTANTS` 
	CHANGE COLUMN `smtpauthentication` `smtpauth` TINYINT(1) NOT NULL DEFAULT 0,
	CHANGE COLUMN `smtpauthenticateusername` `smtpauthusername` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
	CHANGE COLUMN `smtpauthenitcatepassword` `smtpauthpassword` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL;

ALTER TABLE `BRANDING` 
	CHANGE COLUMN `smtpauthentication` `smtpauth` TINYINT(1) NOT NULL DEFAULT 0,
	CHANGE COLUMN `smtpauthenticateusername` `smtpauthusername` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
	CHANGE COLUMN `smtpauthenitcatepassword` `smtpauthpassword` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL;

ALTER TABLE `ORDERITEMS` 
	ADD COLUMN `uploadappcputype` VARCHAR(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER `uploadappplatform`,
	ADD COLUMN `uploadapposversion` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER `uploadappcputype`;

ALTER TABLE `ORDERTEMP` 
	ADD COLUMN `uploadappcputype` VARCHAR(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER `uploadappplatform`,
	ADD COLUMN `uploadapposversion` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER `uploadappcputype`;

#
# DDL END
#

SET FOREIGN_KEY_CHECKS = 1;
