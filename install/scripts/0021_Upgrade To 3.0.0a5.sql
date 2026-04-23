#
SET FOREIGN_KEY_CHECKS = 0;

#
# DDL START
#

INSERT INTO `ACTIVITYLOG`
	(`id`, `datecreated`, `sessionid`, `orderid`, `userid`, `userlogin`, `username`, `importance`, `sectioncode`, `actioncode`, `actionnotes`, `success`)
	VALUES (0, now(), 0, 0, 0, '', (SELECT USER()), 0, 'UPGRADE', '3.0.0a5', 'STARTED', 1);


# COMPONENTS - STAGE 1

CREATE TABLE `COMPONENTCATEGORIES` (
  `id` 						INTEGER 		NOT NULL AUTO_INCREMENT,
  `datecreated` 			DATETIME 		NOT NULL DEFAULT '0000-00-00 00:00:00',
  `companycode` 			VARCHAR(50) 	CHARACTER SET utf8 NOT NULL DEFAULT "",
  `code`		 			VARCHAR(50) 	CHARACTER SET utf8 NOT NULL DEFAULT "",
  `name` 					VARCHAR(1024) 	CHARACTER SET utf8 NOT NULL DEFAULT "",
  `prompt` 					VARCHAR(1024) 	CHARACTER SET utf8 NOT NULL DEFAULT "",
  `pricingmodel`			INTEGER 		NOT NULL DEFAULT 0,
  `islist`					TINYINT(1) 		NOT NULL DEFAULT 0,
  `requirespagecount`		TINYINT(1)		NOT NULL DEFAULT 0,
  `private`					TINYINT(1) 		NOT NULL DEFAULT 0,
  `active`					TINYINT(1) 		NOT NULL DEFAULT 0,
  `deleted`					TINYINT(1) 		NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `SINGULAR` (`code`)
)
ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

INSERT INTO `COMPONENTCATEGORIES`
	(`id`, `datecreated`, `code`, `name`, `prompt`, `pricingmodel`, `islist`, `requirespagecount`, `private`, `active`)
	VALUES (0, now(), "COVER", "en Cover", "en Cover", 3, 1, 1, 1, 1);

INSERT INTO `COMPONENTCATEGORIES`
	(`id`, `datecreated`, `code`, `name`, `prompt`, `pricingmodel`, `islist`, `private`, `active`)
	VALUES (0, now(), "PAPER", "en Paper", "en Paper", 5, 1, 1, 1);

CREATE TABLE `ORDERITEMCOMPONENTS` (
  `id` 						INTEGER NOT NULL AUTO_INCREMENT,
  `datecreated` 			DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  `datemodified` 			DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  `orderid` 				INTEGER NOT NULL DEFAULT 0,
  `orderitemid` 			INTEGER NOT NULL DEFAULT 0,
  `userid` 					INTEGER NOT NULL DEFAULT 0,
  `parentcomponentid`		INTEGER NOT NULL DEFAULT 0,
  `componentcode` 			VARCHAR(152) 	CHARACTER SET utf8 NOT NULL DEFAULT "",
  `componentlocalcode`		VARCHAR(50) 	CHARACTER SET utf8 NOT NULL DEFAULT "",
  `skucode`					VARCHAR(50) 	CHARACTER SET utf8 NOT NULL DEFAULT "",
  `componentdefaultcode`	VARCHAR(152) 	CHARACTER SET utf8 NOT NULL DEFAULT "",
  `componentname` 			VARCHAR(1024) 	CHARACTER SET utf8 NOT NULL DEFAULT "",
  `componentpath` 			VARCHAR(1024) 	CHARACTER SET utf8 NOT NULL DEFAULT "",
  `componentcategorycode`	VARCHAR(50) 	CHARACTER SET utf8 NOT NULL DEFAULT "",
  `componentcategoryname`	VARCHAR(1024) 	CHARACTER SET utf8 NOT NULL DEFAULT "",
  `componentdescription`	VARCHAR(1024) 	CHARACTER SET utf8 NOT NULL DEFAULT "",
  `sortorder`	 			INTEGER 		NOT NULL DEFAULT 0,
  `islist` 					TINYINT(1) 		NOT NULL DEFAULT 0,
  `checkboxselected`		TINYINT(1) 		NOT NULL DEFAULT 0,
  `sectionid`	 			INTEGER 		NOT NULL DEFAULT 0,
  `componentselectioncount` INTEGER 		NOT NULL DEFAULT 0,
  `quantity` 				INTEGER 		NOT NULL DEFAULT 0,
  `componentunitcost` 		DECIMAL(10,2) 	NOT NULL DEFAULT 0,
  `componentunitsell` 		DECIMAL(10,2) 	NOT NULL DEFAULT 0,
  `componentunitweight` 	DECIMAL(10,2) 	NOT NULL DEFAULT 0,
  `componenttotalcost` 		DECIMAL(10,2) 	NOT NULL DEFAULT 0,
  `componenttotalsell` 		DECIMAL(10,2) 	NOT NULL DEFAULT 0,
  `componenttotalweight` 	DECIMAL(10,2) 	NOT NULL DEFAULT 0,
  `componenttotaltax` 		DECIMAL(10,2) 	NOT NULL DEFAULT 0,
  `branchunitcost` 			DECIMAL(10,2) 	NOT NULL DEFAULT 0,
  `branchunitsell` 			DECIMAL(10,2) 	NOT NULL DEFAULT 0,
  `branchunitweight` 		DECIMAL(10,2) 	NOT NULL DEFAULT 0,
  `branchtotalcost` 		DECIMAL(10,2) 	NOT NULL DEFAULT 0,
  `branchtotalsell` 		DECIMAL(10,2) 	NOT NULL DEFAULT 0,
  `branchtotalweight`	 	DECIMAL(10,2) 	NOT NULL DEFAULT 0,
  `branchtotaltax` 			DECIMAL(10,2) 	NOT NULL DEFAULT 0,
  `metadatacodelist` 		VARCHAR(200) 	CHARACTER SET utf8 NOT NULL DEFAULT "",
  PRIMARY KEY (`id`)
)
ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

ALTER TABLE `ORDERITEMCOMPONENTS` ADD INDEX orderitemid(`orderitemid`);
ALTER TABLE `ORDERITEMS` ADD INDEX orderid(`orderid`);

INSERT INTO ORDERITEMCOMPONENTS (datecreated, orderid, orderitemid, userid,
		componentcode, componentlocalcode,
		componentdefaultcode,
		componentname, componentpath, componentcategorycode, componentcategoryname,
		componentdescription, sortorder, islist, checkboxselected, sectionid,
		componentselectioncount, quantity,
		componentunitcost, componentunitsell, componentunitweight,
		componenttotalcost, componenttotalsell, componenttotalweight, componenttotaltax,
		branchunitcost, branchunitsell, branchunitweight,
		branchtotalcost, branchtotalsell, branchtotalweight, branchtotaltax)
	SELECT oi.datecreated, oi.orderid, oi.id, oi.userid,
		IF (oi.currentcompanycode = "", CONCAT("COVER",".",oi.covercode), CONCAT(oi.currentcompanycode,".","COVER",".",oi.covercode)) as componentcode, oi.covercode,
		oi.productdefaultcovercode,
		oi.covername, CONCAT("COVER","\\","COVER",".",oi.covercode) as componentpath, "COVER", cc.name,
		"", 1, cc.islist, 0, 0,
		oi.covercount, oi.qty,
		oi.coverunitcost, oi.coverunitsell, oi.coverunitweight,
		oi.covertotalcost, oi.covertotalsell, oi.covertotalweight, oi.covertotaltax,
		oi.coverunitcost, oi.coverunitsell, oi.coverunitweight,
		oi.covertotalcost, oi.covertotalsell, oi.covertotalweight, oi.covertotaltax
	FROM ORDERITEMS oi, COMPONENTCATEGORIES cc
	WHERE (covercode <> "") AND (cc.code = "COVER");

INSERT INTO ORDERITEMCOMPONENTS (datecreated, orderid, orderitemid, userid,
		componentcode, componentlocalcode,
		componentdefaultcode,
		componentname, componentpath, componentcategorycode, componentcategoryname,
		componentdescription, sortorder, islist, checkboxselected, sectionid,
		componentselectioncount, quantity,
		componentunitcost, componentunitsell, componentunitweight,
		componenttotalcost, componenttotalsell, componenttotalweight, componenttotaltax,
		branchunitcost, branchunitsell, branchunitweight,
		branchtotalcost, branchtotalsell, branchtotalweight, branchtotaltax)
	SELECT oi.datecreated, oi.orderid, oi.id, oi.userid,
		IF (oi.currentcompanycode = "", CONCAT("PAPER",".",oi.papercode), CONCAT(oi.currentcompanycode,".","PAPER",".",oi.papercode)) as componentcode, oi.papercode,
		oi.productdefaultpapercode,
		oi.papername, CONCAT("PAPER","\\","PAPER",".",oi.papercode) as componentpath, "PAPER", cc.name,
		"", (SELECT COUNT(id) + 1 FROM ORDERITEMCOMPONENTS WHERE orderitemid = oi.id) as sortorder, cc.islist, 0, 0,
		oi.papercount, oi.qty,
		oi.paperunitcost, oi.paperunitsell, oi.paperunitweight,
		oi.papertotalcost, oi.papertotalsell, oi.papertotalweight, oi.papertotaltax,
		oi.paperunitcost, oi.paperunitsell, oi.paperunitweight,
		oi.papertotalcost, oi.papertotalsell, oi.papertotalweight, oi.papertotaltax
	FROM ORDERITEMS oi, COMPONENTCATEGORIES cc
	WHERE (papercode <> "") AND (cc.code = "PAPER");

DROP INDEX `orderitemid` ON `ORDERITEMCOMPONENTS`;
DROP INDEX `orderid` ON `ORDERITEMS`;

ALTER TABLE `ORDERITEMS`
	DROP COLUMN `covercount`,
	DROP COLUMN `covercode`,
	DROP COLUMN `covername`,
	DROP COLUMN `papercount`,
	DROP COLUMN `papercode`,
	DROP COLUMN `papername`,
	DROP COLUMN `productdefaultcovercode`,
	DROP COLUMN `productdefaultpapercode`,
	DROP COLUMN `coverunitcost`,
	DROP COLUMN `coverunitsell`,
	DROP COLUMN `paperunitcost`,
	DROP COLUMN `paperunitsell`,
	DROP COLUMN `coverunitweight`,
	DROP COLUMN `covertotalweight`,
	DROP COLUMN `paperunitweight`,
	DROP COLUMN `papertotalweight`,
	DROP COLUMN `covertotalcost`,
	DROP COLUMN `covertotalsell`,
	DROP COLUMN `covertotaltax`,
	DROP COLUMN `papertotalcost`,
	DROP COLUMN `papertotalsell`,
	DROP COLUMN `papertotaltax`;

# END OF COMPONENTS - STAGE 1




#
# English Counties
#
INSERT INTO `countryregion` (`datecreated`, `countrycode`, `regioncode`, `regionname`, `regiongroupcode`) SELECT now(), 'GB', 'LONDON', 'en London', 'EN'
    FROM DUAL WHERE NOT EXISTS (SELECT * FROM `countryregion` WHERE  `regioncode`='LONDON' AND `countrycode` = 'GB');

INSERT INTO `countryregion` (`datecreated`, `countrycode`, `regioncode`, `regionname`, `regiongroupcode`) SELECT now(), 'GB', 'WESTMIDLANDS', 'en West Midlands', 'EN'
    FROM DUAL WHERE NOT EXISTS (SELECT * FROM `countryregion` WHERE  `regioncode`='WESTMIDLANDS' AND `countrycode` = 'GB');

INSERT INTO `countryregion` (`datecreated`, `countrycode`, `regioncode`, `regionname`, `regiongroupcode`) SELECT now(), 'GB', 'MANCHESTER', 'en Manchester', 'EN'
    FROM DUAL WHERE NOT EXISTS (SELECT * FROM `countryregion` WHERE  `regioncode`='MANCHESTER' AND `countrycode` = 'GB');

INSERT INTO `countryregion` (`datecreated`, `countrycode`, `regioncode`, `regionname`, `regiongroupcode`) SELECT now(), 'GB', 'MERSEYSIDE', 'en Merseyside', 'EN'
    FROM DUAL WHERE NOT EXISTS (SELECT * FROM `countryregion` WHERE  `regioncode`='MERSEYSIDE' AND `countrycode` = 'GB');

INSERT INTO `countryregion` (`datecreated`, `countrycode`, `regioncode`, `regionname`, `regiongroupcode`) SELECT now(), 'GB', 'SURREY', 'en Surrey', 'EN'
    FROM DUAL WHERE NOT EXISTS (SELECT * FROM `countryregion` WHERE  `regioncode`='SURREY' AND `countrycode` = 'GB');

INSERT INTO `countryregion` (`datecreated`, `countrycode`, `regioncode`, `regionname`, `regiongroupcode`) SELECT now(), 'GB', 'WESTSUSSEX', 'en West Sussex', 'EN'
    FROM DUAL WHERE NOT EXISTS (SELECT * FROM `countryregion` WHERE  `regioncode`='WESTSUSSEX' AND `countrycode` = 'GB');

INSERT INTO `countryregion` (`datecreated`, `countrycode`, `regioncode`, `regionname`, `regiongroupcode`) SELECT now(), 'GB', 'EASTSUSSEX', 'en East Sussex', 'EN'
    FROM DUAL WHERE NOT EXISTS (SELECT * FROM `countryregion` WHERE  `regioncode`='EASTSUSSEX' AND `countrycode` = 'GB');

INSERT INTO `countryregion` (`datecreated`, `countrycode`, `regioncode`, `regionname`, `regiongroupcode`) SELECT now(), 'GB', 'ISLEOFWIGHT', 'en Isle of Wight', 'EN'
    FROM DUAL WHERE NOT EXISTS (SELECT * FROM `countryregion` WHERE  `regioncode`='ISLEOFWIGHT' AND `countrycode` = 'GB');

INSERT INTO `countryregion` (`datecreated`, `countrycode`, `regioncode`, `regionname`, `regiongroupcode`) SELECT now(), 'GB', 'ISLESOFSCILLY', 'en Isles of Scilly', 'EN'
    FROM DUAL WHERE NOT EXISTS (SELECT * FROM `countryregion` WHERE  `regioncode`='ISLESOFSCILLY' AND `countrycode` = 'GB');

#
# Countries
#
INSERT INTO `COUNTRIES` (`name`, `isocode2`, `isocode3`, `region`, `displayfields`, `compulsoryfields`, `displayformat`, `fieldlabels`, `addressformatid`)
    SELECT 'Isle of Man', 'IM', 'IMN', 'STATE', '','', '','', 1 FROM DUAL WHERE NOT EXISTS (SELECT * FROM `COUNTRIES` WHERE `isocode2`='IM' AND `isocode3`='IMN');

INSERT INTO `COUNTRIES` (`name`, `isocode2`, `isocode3`, `region`, `displayfields`, `compulsoryfields`, `displayformat`, `fieldlabels`, `addressformatid`)
    SELECT 'Guernsey', 'GG', 'GGY', 'STATE', '','', '','',1 FROM DUAL WHERE NOT EXISTS (SELECT * FROM `COUNTRIES` WHERE `isocode2`='GG' AND `isocode3`='GGY');

INSERT INTO `COUNTRIES` (`name`, `isocode2`, `isocode3`, `region`, `displayfields`, `compulsoryfields`, `displayformat`, `fieldlabels`, `addressformatid`)
    SELECT 'Jersey', 'JE', 'JEY', 'STATE', '','', '','',1 FROM DUAL WHERE NOT EXISTS (SELECT * FROM `COUNTRIES` WHERE `isocode2`='JE' AND `isocode3`='JEY');



# COMPONENTS - STAGE 2


#
# COMPONENTS
#
CREATE TABLE `COMPONENTS` (
  `id`                      INTEGER			NOT NULL AUTO_INCREMENT,
  `datecreated`             DATETIME		NOT NULL DEFAULT '0000-00-00 00:00:00',
  `companycode`             VARCHAR(50)		CHARACTER SET utf8 NOT NULL DEFAULT "",
  `categorycode`            VARCHAR(50)		CHARACTER SET utf8 NOT NULL DEFAULT "",
  `code`                    VARCHAR(152)	CHARACTER SET utf8 NOT NULL DEFAULT "",
  `localcode`               VARCHAR(50) 	CHARACTER SET utf8 NOT NULL DEFAULT "",
  `skucode`                 VARCHAR(50)		CHARACTER SET utf8 NOT NULL DEFAULT "",
  `name`                    VARCHAR(1024)   CHARACTER SET utf8 NOT NULL DEFAULT "",
  `info`                    VARCHAR(1024)   CHARACTER SET utf8 NOT NULL DEFAULT "",
  `unitcost`                DECIMAL(10,2)   NOT NULL DEFAULT 0,
  `minimumpagecount`        INTEGER			NOT NULL DEFAULT 0,
  `maximumpagecount`        INTEGER			NOT NULL DEFAULT 0,
  `weight`                  DECIMAL(10,4)	NOT NULL DEFAULT 0,
  `default`                 TINYINT(1)		NOT NULL default -1,
  `assetid`                 INTEGER			NOT NULL DEFAULT 0,
  `keywordgroupheaderid`    INTEGER			NOT NULL DEFAULT 0,
  `active`                  TINYINT(1)		NOT NULL default 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `SINGULAR` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

#
# ASSETS
#
CREATE TABLE `ASSETDATA` (
  `id`						INTEGER			NOT NULL AUTO_INCREMENT,
  `retrievalid`				VARCHAR(50)		NOT NULL DEFAULT '',
  `datecreated`				DATETIME		NOT NULL DEFAULT '0000-00-00 00:00:00',
  `datemodified`			DATETIME		NOT NULL DEFAULT '0000-00-00 00:00:00',
  `name`					VARCHAR(1024)	CHARACTER SET utf8 NOT NULL DEFAULT "",
  `assettype`				INTEGER			NOT NULL DEFAULT 0,
  `data`					MEDIUMBLOB,
  `previewtype`				VARCHAR(100)	CHARACTER SET utf8 NOT NULL DEFAULT "",
  `previewwidth`			INTEGER			NOT NULL DEFAULT 0,
  `previewheight`			INTEGER			NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  INDEX (retrievalid)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;


#
# PRICE LINK
#
CREATE TABLE `PRICELINK` (
  `id`						INTEGER			NOT NULL AUTO_INCREMENT,
  `datecreated`				DATETIME		NOT NULL DEFAULT '0000-00-00 00:00:00',
  `parentid`				INTEGER			NOT NULL DEFAULT 0,
  `companycode`				VARCHAR(50)		CHARACTER SET utf8 NOT NULL DEFAULT "",
  `productcode`				VARCHAR(50)		CHARACTER SET utf8 NOT NULL DEFAULT "",
  `groupcode`				VARCHAR(50)		CHARACTER SET utf8 NOT NULL DEFAULT "",
  `componentcode`			VARCHAR(152)	CHARACTER SET utf8 NOT NULL DEFAULT "",
  `parentpath`				VARCHAR(400)	CHARACTER SET utf8 NOT NULL DEFAULT "",
  `sectionpath`				VARCHAR(400)	CHARACTER SET utf8 NOT NULL DEFAULT "",
  `sectioncode`				VARCHAR(50)		CHARACTER SET utf8 NOT NULL DEFAULT "",
  `sortorder`				VARCHAR(15)		CHARACTER SET utf8 NOT NULL DEFAULT "",
  `shoppingcarttype`		INTEGER			NOT NULL default 0,
  `priceid`    	        	INTEGER			NOT NULL DEFAULT 0,
  `priceinfo`				VARCHAR(1024)   CHARACTER SET utf8 NOT NULL DEFAULT "",
  `pricedescription`		VARCHAR(1024)   CHARACTER SET utf8 NOT NULL DEFAULT "",
  `isdefault`               TINYINT(1)		NOT NULL DEFAULT 0,
  `isvisible`               TINYINT(1)		NOT NULL DEFAULT 1,
  `active`                  TINYINT(1)		NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

#
# PRICES
#
CREATE TABLE `PRICES` (
  `id`						INTEGER			NOT NULL AUTO_INCREMENT,
  `datecreated`				DATETIME		NOT NULL DEFAULT '0000-00-00 00:00:00',
  `companycode` 			VARCHAR(50) 	NOT NULL DEFAULT "",
  `categorycode`			VARCHAR(50)		CHARACTER SET utf8 NOT NULL DEFAULT "",
  `linkedpricelistid`		INTEGER			NOT NULL DEFAULT 0,
  `pricingmodel`			INTEGER			NOT NULL DEFAULT 0,
  `price`					VARCHAR(1024)	CHARACTER SET utf8 NOT NULL DEFAULT "",
  `pricelistcode`			VARCHAR(50)		CHARACTER SET utf8 NOT NULL DEFAULT "",
  `pricelistname`			VARCHAR(50)		CHARACTER SET utf8 NOT NULL DEFAULT "",
  `quantityisdropdown` 		TINYINT(1) 		NOT NULL DEFAULT 0,
  `ispricelist`				TINYINT(1)		NOT NULL DEFAULT 0,
  `active`					TINYINT(1)		NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

ALTER TABLE `ASSETDATA`
    ADD `oldid` integer NOT NULL DEFAULT 0;

ALTER TABLE `PRICES`
    ADD `oldid` integer NOT NULL DEFAULT 0;

ALTER TABLE `PRICELINK`
    ADD `oldid` integer NOT NULL DEFAULT 0,
    ADD `oldcode` VARCHAR(50) CHARACTER SET utf8 NOT NULL DEFAULT "";


#
# Cover
#

# insert asset information from COVERS, oldid is used to make the link to cover components later on
INSERT INTO `ASSETDATA` (`datecreated`, `datemodified`, `name`, `assettype`, `data`, `previewtype`, `previewwidth`, `previewheight`, `oldid`)
    SELECT `datecreated`, `datecreated`, '', 0, `preview`, `previewtype`, 0, 0, `id` FROM COVERS WHERE `previewtype` <> "";

# set retrievalid to random number
UPDATE `ASSETDATA` SET `retrievalid` = CONCAT(`id`,"x",UPPER(CAST(MD5(RAND()) AS CHAR(32)))) WHERE `retrievalid` = "";

# copy all COVERS into COMPONENTS, plus the id of the asset inserted in the previous statement
INSERT INTO COMPONENTS (`datecreated`, `companycode`, `categorycode`, `code`, `localcode`, `name`, `info`, `unitcost`, `minimumpagecount`, `maximumpagecount`, `weight`, `active`, `assetid`)
    SELECT `datecreated`, `companycode`, 'COVER', (CASE WHEN `companycode` <> "" THEN CONCAT(`companycode`, '.COVER.', `code`) ELSE CONCAT('COVER.', `code`) END),
    `code`, `name`, `info`, `unitcost`, `minimumpagecount`, `maximumpagecount`, `weight`, `active`,
    IF((SELECT ad.retrievalid FROM ASSETDATA ad WHERE ad.oldid=p.id) IS NULL,0,(SELECT ad.id FROM ASSETDATA ad WHERE ad.oldid=p.id)) FROM COVERS p;

# copy cover prices only for master prices, i.e. that have no parent id, the other entries will go into PRICELINK table
# oldid is needed so a link can be made from PRICELINK
# convert price format so negative values can be used in prices
INSERT INTO PRICES (datecreated,companycode, pricingmodel, price, active, categorycode, oldid)
	SELECT cp.datecreated,cp.companycode, 3, REPLACE(cp.price, '-', '*'), cp.active, "COVER", cp.id FROM COVERPRICES cp WHERE cp.parentid = 0;

# at this point only insert entry for prices that have no parentid
# order by companycode so that sortorder will be consecutive later on
INSERT INTO PRICELINK (`datecreated`, `parentid`, `companycode`, `productcode`, `groupcode`, `componentcode`,`parentpath`, `sectioncode`,
		`active`, `oldid`, `oldcode` )
	SELECT cp.datecreated, cp.parentid, cp.companycode, cp.productcode, cp.groupcode, IF(c.companycode <>"", IF(cp.companycode <> "" , CONCAT(cp.companycode,".","COVER",".",cp.covercode), CONCAT(c.companycode,".","COVER",".",cp.covercode) ), CONCAT("COVER",".",cp.covercode)),
		IF(cp.productcode = '','','$COVER\\'), "COVER", cp.active, cp.id, cp.covercode
	FROM COVERPRICES cp, COVERS c
	WHERE cp.parentid = 0 AND c.code = cp.covercode		
	 ORDER BY companycode;

# set componentcode
#UPDATE PRICELINK pl SET `componentcode` = (SELECT c.code FROM COMPONENTS c WHERE categorycode="COVER" AND c.localcode=pl.oldcode)
#WHERE pl.sectioncode="COVER";

# set sortorder global default prices
SET @i = 0;
UPDATE PRICELINK SET sortorder=(@i:=@i+1) WHERE companycode="" AND productcode="" AND oldid>0;
UPDATE PRICELINK SET sortorder=RIGHT(CONCAT("000", sortorder),3) WHERE companycode="" AND productcode="" AND oldid>0;
UPDATE PRICELINK SET sortorder=CONCAT(sortorder,"-000-000-000") WHERE companycode="" AND productcode="" AND oldid>0;

# set sortorder global product-specific prices
SET @i = 0;
UPDATE PRICELINK SET sortorder=(@i:=@i+1) WHERE companycode="" AND productcode<>"" AND oldid>0;
UPDATE PRICELINK SET sortorder=RIGHT(CONCAT("000", sortorder),3) WHERE companycode="" AND productcode<>"" AND oldid>0;
UPDATE PRICELINK SET sortorder=CONCAT("000-",sortorder,"-000-000") WHERE companycode="" AND productcode<>"" AND oldid>0;

# set sortorder company default prices
SET @i = 0;
UPDATE PRICELINK SET sortorder=(@i:=@i+1) WHERE companycode<>"" AND productcode="" AND oldid>0;

# use priceid to store minimum sortorder per company
UPDATE PRICELINK pl
SET priceid=(SELECT mintable.minimum
			 FROM (SELECT MIN(sortorder)-1 AS minimum,companycode
					FROM PRICELINK
					WHERE companycode<>"" AND productcode="" AND oldid>0 GROUP BY companycode) AS mintable
			 WHERE mintable.companycode = pl.companycode)
WHERE pl.companycode<>"" AND pl.productcode="" AND pl.oldid>0;

# make sure sortorder starts on 1 for all companies
UPDATE PRICELINK
SET sortorder = sortorder-priceid
WHERE companycode<>"" AND productcode="" AND oldid>0;

# format sortorder
UPDATE PRICELINK SET sortorder=RIGHT(CONCAT("000", sortorder),3) WHERE companycode<>"" AND productcode="" AND oldid>0;
UPDATE PRICELINK SET sortorder=CONCAT("000-000-",sortorder,"-000") WHERE companycode<>"" AND productcode="" AND oldid>0;

# set sortorder company product-specific prices
SET @i = 0;
UPDATE PRICELINK SET sortorder=(@i:=@i+1) WHERE companycode<>"" AND productcode<>"" AND oldid>0;

# use priceid to store minimum sortorder per company
UPDATE PRICELINK pl
SET priceid=(SELECT mintable.minimum
			 FROM (SELECT MIN(sortorder)-1 AS minimum,companycode
					FROM PRICELINK
					WHERE companycode<>"" AND productcode<>"" AND oldid>0 GROUP BY companycode) AS mintable
			 WHERE mintable.companycode = pl.companycode)
WHERE pl.companycode<>"" AND pl.productcode<>"" AND pl.oldid>0;

# make sure sortorder starts on 1 for all companies
UPDATE PRICELINK
SET sortorder = sortorder-priceid
WHERE companycode<>"" AND productcode<>"" AND oldid>0;

# format sortorder
UPDATE PRICELINK SET sortorder=RIGHT(CONCAT("000", sortorder),3) WHERE companycode<>"" AND productcode<>"" AND oldid>0;
UPDATE PRICELINK SET sortorder=CONCAT("000-000-000-",sortorder) WHERE companycode<>"" AND productcode<>"" AND oldid>0;

# insert entries for prices that apply to more than one lkey, i.e. had a parent id in COVERPRICE is non-zero
INSERT INTO PRICELINK (`datecreated`, `parentid`, `companycode`, `productcode`, `groupcode`, `componentcode`, `parentpath`, `sectioncode`,`active`, `oldid`, `oldcode` , `sortorder` )
	SELECT cp.datecreated, pl.id, cp.companycode, cp.productcode, cp.groupcode, IF (cp.companycode = "", CONCAT("COVER",".",cp.covercode), CONCAT(cp.companycode,".","COVER",".",cp.covercode)), 
		IF(cp.productcode = '','','$COVER\\'), "COVER", cp.active, cp.id, cp.covercode, pl.sortorder
	FROM COVERPRICES cp JOIN PRICELINK pl on pl.oldcode = cp.covercode
	WHERE cp.parentid>0 and cp.parentid=pl.oldid;

CALL defaultPriceConvert('COVER');

# parent ids are never 0 after this has run
 UPDATE PRICELINK SET parentid=id WHERE parentid=0;

# link prices to pricelink entries
UPDATE PRICELINK pl INNER JOIN COVERPRICES cp ON (pl.oldid = cp.id) LEFT JOIN PRICES pr ON ((cp.id = pr.oldid OR cp.parentid = pr.oldid) AND (pr.categorycode = 'COVER'))
	SET pl.priceid = pr.id;


UPDATE `ASSETDATA` SET oldid = 0;
UPDATE PRICES SET oldid = 0;
UPDATE PRICELINK SET oldid = 0, oldcode = '';
#
# Paper
#

# insert asset information from PAPER, oldid is used to make the link to paper components later on
INSERT INTO `ASSETDATA` (`datecreated`, `datemodified`, `name`, `assettype`, `data`, `previewtype`, `previewwidth`, `previewheight`, `oldid`)
    SELECT `datecreated`, `datecreated`, '', 0, `preview`, `previewtype`, 0, 0, `id` FROM PAPER WHERE `previewtype` <> "";

# set retrievalid to random number
UPDATE `ASSETDATA` SET `retrievalid` = CONCAT(`id`,"x",UPPER(CAST(MD5(RAND()) AS CHAR(32)))) WHERE `retrievalid` = "";
# copy all PAPERs into COMPONENTS, plus the id of the asset inserted in the previous statement
INSERT INTO COMPONENTS (`datecreated`, `companycode`, `categorycode`, `code`, `localcode`, `name`, `info`, `unitcost`, `weight`, `active`, `assetid`)
    SELECT `datecreated`, `companycode`, 'PAPER', (CASE WHEN `companycode` <> "" THEN CONCAT(`companycode`, '.PAPER.', `code`) ELSE CONCAT('PAPER.', `code`) END),
    `code`, `name`, `info`, `unitcost`, `weight`, `active`,
    IF((SELECT ad.retrievalid FROM ASSETDATA ad WHERE ad.oldid=p.id) IS NULL,0,(SELECT ad.id FROM ASSETDATA ad WHERE ad.oldid=p.id)) FROM PAPER p;

# copy paper prices only for master prices, i.e. that have no parent id, the other entries will go into PRICELINK table
# oldid is needed so a link can be made from PRICELINK
# convert price format so negative values can be used in prices
INSERT INTO PRICES (datecreated, companycode, pricingmodel, price, active, categorycode, oldid)
	SELECT pp.datecreated, pp.companycode, 5, REPLACE(pp.price, '-', '*'), pp.active, "PAPER", pp.id FROM PAPERPRICES pp WHERE pp.parentid = 0;

# at this point only insert entry for prices that have no parentid
# order by companycode so that sortorder will be consecutive later on
INSERT INTO PRICELINK (`datecreated`, `parentid`, `companycode`, `productcode`, `groupcode`, `componentcode`, `parentpath`, `sectioncode`,
		`active`, `oldid`, `oldcode` )
	SELECT pp.datecreated, pp.parentid, pp.companycode, pp.productcode, pp.groupcode, IF(p.companycode <>"", IF(pp.companycode <> "" , CONCAT(pp.companycode,".","PAPER",".",pp.papercode), CONCAT(p.companycode,".","PAPER",".",pp.papercode) ), CONCAT("PAPER",".",pp.papercode)),	
		IF(pp.productcode = '','','$PAPER\\'), "PAPER", pp.active, pp.id, pp.papercode
	FROM PAPERPRICES pp, PAPER p
	WHERE pp.parentid = 0 AND p.code = pp.papercode	
	 ORDER BY companycode;

# set componentcode
#UPDATE PRICELINK pl SET `componentcode` = (SELECT c.code FROM COMPONENTS c WHERE categorycode="PAPER" AND c.localcode=pl.oldcode)
#WHERE pl.sectioncode="PAPER";

# set sortorder global default prices
SET @i = 0;
UPDATE PRICELINK SET sortorder=(@i:=@i+1) WHERE companycode="" AND productcode="" AND oldid>0;
UPDATE PRICELINK SET sortorder=RIGHT(CONCAT("000", sortorder),3) WHERE companycode="" AND productcode="" AND oldid>0;
UPDATE PRICELINK SET sortorder=CONCAT(sortorder,"-000-000-000") WHERE companycode="" AND productcode="" AND oldid>0;

# set sortorder global product-specific prices
SET @i = 0;
UPDATE PRICELINK SET sortorder=(@i:=@i+1) WHERE companycode="" AND productcode<>"" AND oldid>0;
UPDATE PRICELINK SET sortorder=RIGHT(CONCAT("000", sortorder),3) WHERE companycode="" AND productcode<>"" AND oldid>0;
UPDATE PRICELINK SET sortorder=CONCAT("000-",sortorder,"-000-000") WHERE companycode="" AND productcode<>"" AND oldid>0;

# set sortorder company default prices
SET @i = 0;
UPDATE PRICELINK SET sortorder=(@i:=@i+1) WHERE companycode<>"" AND productcode="" AND oldid>0;

# use priceid to store minimum sortorder per company
UPDATE PRICELINK pl
SET priceid=(SELECT mintable.minimum
			 FROM (SELECT MIN(sortorder)-1 AS minimum,companycode
					FROM PRICELINK
					WHERE companycode<>"" AND productcode="" AND oldid>0 GROUP BY companycode) AS mintable
			 WHERE mintable.companycode = pl.companycode)
WHERE pl.companycode<>"" AND pl.productcode="" AND pl.oldid>0;

# make sure sortorder starts on 1 for all companies
UPDATE PRICELINK
SET sortorder = sortorder-priceid
WHERE companycode<>"" AND productcode="" AND oldid>0;

# format sortorder
UPDATE PRICELINK SET sortorder=RIGHT(CONCAT("000", sortorder),3) WHERE companycode<>"" AND productcode="" AND oldid>0;
UPDATE PRICELINK SET sortorder=CONCAT("000-000-",sortorder,"-000") WHERE companycode<>"" AND productcode="" AND oldid>0;


# set sortorder company product-specific prices
SET @i = 0;
UPDATE PRICELINK SET sortorder=(@i:=@i+1) WHERE companycode<>"" AND productcode<>"" AND oldid>0;

# use priceid to store minimum sortorder per company
UPDATE PRICELINK pl
SET priceid=(SELECT mintable.minimum
			 FROM (SELECT MIN(sortorder)-1 AS minimum,companycode
					FROM PRICELINK
					WHERE companycode<>"" AND productcode<>"" AND oldid>0 GROUP BY companycode) AS mintable
			 WHERE mintable.companycode = pl.companycode)
WHERE pl.companycode<>"" AND pl.productcode<>"" AND pl.oldid>0;

# make sure sortorder starts on 1 for all companies
UPDATE PRICELINK
SET sortorder = sortorder-priceid
WHERE companycode<>"" AND productcode<>"" AND oldid>0;

# format sortorder
UPDATE PRICELINK SET sortorder=RIGHT(CONCAT("000", sortorder),3) WHERE companycode<>"" AND productcode<>"" AND oldid>0;
UPDATE PRICELINK SET sortorder=CONCAT("000-000-000-",sortorder) WHERE companycode<>"" AND productcode<>"" AND oldid>0;


# insert entries for prices that apply to more than one lkey, i.e. had a parent id in PAPERPRICE is non-zero
INSERT INTO PRICELINK (`datecreated`, `parentid`, `companycode`, `productcode`, `groupcode`, `componentcode`, `parentpath`, `sectioncode`,
		`active`, `oldid`, `oldcode` , `sortorder` )
	SELECT pp.datecreated, pl.id, pp.companycode, pp.productcode, pp.groupcode,
		IF (pp.companycode = "", CONCAT("PAPER",".",pp.papercode), CONCAT(pp.companycode,".","PAPER",".",pp.papercode)),IF(pp.productcode = '','','$PAPER\\') , "PAPER",
		pp.active, pp.id, pp.papercode, pl.sortorder
	FROM paperprices pp JOIN PRICELINK pl on pl.oldcode = pp.papercode WHERE pp.parentid>0 and pp.parentid=pl.oldid;

CALL defaultPriceConvert('PAPER');

# parent ids are never 0 after this has run
 UPDATE PRICELINK SET parentid=id WHERE parentid=0;

# link prices to pricelink entries
UPDATE PRICELINK pl INNER JOIN PAPERPRICES pp ON (pl.oldid = pp.id) LEFT JOIN PRICES pr ON ((pp.id = pr.oldid OR pp.parentid = pr.oldid) AND (pr.categorycode = 'PAPER'))
	SET pl.priceid = pr.id;



#
# Product prices
#

UPDATE PRICES SET oldid = 0;
UPDATE PRICELINK SET oldid = 0, oldcode = '';

# copy product prices only for master prices, i.e. that have no parent id, the other entries will go into PRICELINK table
# oldid is needed so a link can be made from PRICELINK
# convert price format so negative values can be used in prices
INSERT INTO PRICES (datecreated, companycode, pricingmodel, price, active, categorycode, oldid)
	SELECT pp.datecreated, pp.companycode, 3, REPLACE(pp.price, '-', '*'), pp.active, "PRODUCT", pp.id FROM PRODUCTPRICES pp WHERE pp.parentid = 0;

# at this point only insert entry for prices that have no parentid
INSERT INTO PRICELINK (`datecreated`, `parentid`, `companycode`, `productcode`, `groupcode`, `priceinfo`, `pricedescription`, `active`, `oldid`, `oldcode`)
	SELECT `datecreated`, `parentid`, `companycode`, `productcode`, `groupcode`, `info`, `pricedescription`,`active`, `id`, `productcode`
	FROM PRODUCTPRICES
	WHERE parentid = 0;

# insert entries for prices that apply to more than one lkey, i.e. had a parent id in PRODUCTPRICES that is non-zero
# set parentid
INSERT INTO PRICELINK (`datecreated`, `parentid`, `companycode`, `productcode`, `groupcode`, `priceinfo`, `pricedescription`, `active`, `oldid`, `oldcode` )
	SELECT DISTINCT pp.datecreated, pl.id, pp.companycode, pp.productcode, pp.groupcode, pp.info, pp.pricedescription, pp.active, pp.id, pp.productcode
	FROM PRODUCTPRICES pp JOIN PRICELINK pl on pl.oldid = pp.parentid WHERE pp.parentid <> 0;
# parent ids are never 0 after this has run
 UPDATE PRICELINK SET parentid=id WHERE parentid=0;

# link prices to pricelink
UPDATE PRICELINK pl INNER JOIN PRODUCTPRICES pp ON (pl.oldid = pp.id) LEFT JOIN PRICES pr ON ((pp.id = pr.oldid OR pp.parentid = pr.oldid) AND (pr.categorycode = 'PRODUCT'))
SET pl.priceid = pr.id;

# set default paper when known
# if price is defined
UPDATE PRICELINK pl INNER JOIN COMPONENTS cp ON cp.code=pl.componentcode LEFT JOIN PRODUCTS pr ON pl.productcode=pr.code
	SET pl.isdefault = 1
	WHERE cp.categorycode="PAPER" AND pr.defaultpapercode=cp.localcode;

# set default cover when known
# if price is defined
UPDATE PRICELINK pl INNER JOIN COMPONENTS cp ON cp.code=pl.componentcode LEFT JOIN PRODUCTS pr ON pl.productcode=pr.code
	SET pl.isdefault = 1
	WHERE cp.categorycode="COVER" AND pr.defaultcovercode=cp.localcode;

# update parentid
UPDATE PRICELINK pl SET `parentid` = id WHERE parentid = 0;


# remove temporary columns

ALTER TABLE PRICELINK
	DROP COLUMN oldid,
	DROP COLUMN oldcode;

ALTER TABLE PRICES
	DROP COLUMN oldid;


# END OF COMPONENTS - STAGE 2


#
# DEFAULT BRAND
#
INSERT INTO `BRANDING`
	(`datecreated`, `companycode`, `owner`, `code`, `name`, `applicationname`, `displayurl`, `weburl`, `usedefaultpaymentmethods`,
	`paymentmethods`, `paymentintegration`, `usedefaultemailsettings`,
	`smtpaddress`, `smtpport`, `smtpauth`, `smtpauthusername`, `smtpauthpassword`, `smtpsystemfromname`, `smtpsystemfromaddress`,
	`smtpsystemreplytoname`, `smtpsystemreplytoaddress`, `smtpadminname`, `smtpadminaddress`, `smtpproductionname`, `smtpproductionaddress`,
	`smtporderconfirmationname`, `smtporderconfirmationaddress`, `smtpsaveordername`, `smtpsaveorderaddress`, `active`)
SELECT now(), '' , '' , '' , '' , `applicationname`, '' , '' , 0 ,
	`defaultpaymentmethods`, `defaultpaymentintegration`, 0,
	`smtpaddress`, `smtpport`, `smtpauth`, `smtpauthusername`, `smtpauthpassword`, `smtpsystemfromname`, `smtpsystemfromaddress`,
	`smtpsystemreplytoname`, `smtpsystemreplytoaddress`, `smtpadminname`, `smtpadminaddress`, `smtpproductionname`, `smtpproductionaddress`,
	`smtporderconfirmationname`, `smtporderconfirmationaddress`, `smtpsaveordername`, `smtpsaveorderaddress`, 1 FROM `CONSTANTS`;

ALTER TABLE `CONSTANTS`
	DROP COLUMN `applicationname`,
	DROP COLUMN `defaultpaymentmethods`,
	DROP COLUMN `defaultpaymentintegration`,
	DROP COLUMN `smtpaddress`,
	DROP COLUMN `smtpport`,
	DROP COLUMN `smtpauth`,
	DROP COLUMN `smtpauthusername`,
	DROP COLUMN `smtpauthpassword`,
	DROP COLUMN `smtpsystemfromname`,
	DROP COLUMN `smtpsystemfromaddress`,
	DROP COLUMN `smtpsystemreplytoname`,
	DROP COLUMN `smtpsystemreplytoaddress`,
	DROP COLUMN `smtpadminname`,
	DROP COLUMN `smtpadminaddress`,
	DROP COLUMN `smtpproductionname`,
	DROP COLUMN `smtpproductionaddress`,
	DROP COLUMN `smtporderconfirmationname`,
	DROP COLUMN `smtporderconfirmationaddress`,
	DROP COLUMN `smtpsaveordername`,
	DROP COLUMN `smtpsaveorderaddress`;


#
# SECTIONS
#
CREATE TABLE `SECTIONS` (
  `id`						INTEGER         NOT NULL AUTO_INCREMENT,
  `datecreated`				DATETIME        NOT NULL DEFAULT '0000-00-00 00:00:00',
  `code`					VARCHAR(50)     CHARACTER SET utf8 NOT NULL DEFAULT "",
  `label`					VARCHAR(1024)   CHARACTER SET utf8 NOT NULL DEFAULT "",
  `name`					VARCHAR(1024)   CHARACTER SET utf8 NOT NULL DEFAULT "",
  `categorycode`            VARCHAR(50)		CHARACTER SET utf8 NOT NULL DEFAULT "",
  `displaytype`				INTEGER         NOT NULL DEFAULT 0,
  `sortorder`				INTEGER         NOT NULL DEFAULT 0,
  `itemorder`				INTEGER         NOT NULL DEFAULT 0,
  `displaysection`			INTEGER         NOT NULL DEFAULT 0,
  `private`					TINYINT(1) 		NOT NULL default 0,
  `active`					TINYINT(1) 		NOT NULL default 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `SINGULAR` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

INSERT INTO `SECTIONS` VALUES
	(0, now(), "COVER", "en Cover", "en Cover", "COVER", 1, 1, 0, 1, 1, 1),
	(0, now(), "PAPER", "en Paper", "en Paper", "PAPER", 1, 2, 0, 1, 1, 1),
	(0, now(), "LINEFOOTER", "en Linefooter", "en Linefooter", "", 1, -1, 0, 1, 1, 1),
	(0, now(), "ORDERFOOTER", "en Orderfooter", "en Orderfooter", "", 1, -1, 0, 1, 1, 1);


#
# PRICE ALIASES
#
CREATE TABLE `PRICEALIAS` (
  `id`						INTEGER         NOT NULL AUTO_INCREMENT,
  `datecreated`				DATETIME        NOT NULL DEFAULT '0000-00-00 00:00:00',
  `companycode`				VARCHAR(50)     CHARACTER SET utf8 NOT NULL DEFAULT "",
  `alias`					VARCHAR(50)     CHARACTER SET utf8 NOT NULL DEFAULT "",
  `realname`				VARCHAR(50)     CHARACTER SET utf8 NOT NULL DEFAULT "",
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;


#
# CONVERT OTHER ASSETS
#
UPDATE `ASSETDATA` SET oldid = 0;

INSERT INTO `ASSETDATA` (`datecreated`, `datemodified`, `name`, `assettype`, `data`, `previewtype`, `previewwidth`, `previewheight`, `oldid`)
	SELECT `datecreated`, `datecreated`, '', 0, `storelocatorlogo`, `storelocatorlogotype`, `storelocatorlogowidth`, `storelocatorlogoheight`, `id` FROM `SHIPPINGMETHODS` WHERE `storelocatorlogotype` <> "";

# set retrievalid to random number
UPDATE `ASSETDATA` SET `retrievalid` = CONCAT(`id`,"x",UPPER(CAST(MD5(RAND()) AS CHAR(32)))) WHERE `retrievalid` = "";

# next point to SHIPPINGMETHODS
# need to define assetid column in SHIPPINGMETHODS first
ALTER TABLE `SHIPPINGMETHODS`
	ADD `assetid` INTEGER NOT NULL DEFAULT '0',
	DROP COLUMN `storelocatorlogo`,
	DROP COLUMN `storelocatorlogotype`,
	DROP COLUMN `storelocatorlogowidth`,
	DROP COLUMN `storelocatorlogoheight`;

# set assetid
UPDATE `SHIPPINGMETHODS` sm
SET `assetid` = IF((SELECT `retrievalid` FROM `ASSETDATA` WHERE `oldid`=`sm`.`id`) IS NULL, 0, (SELECT `id` FROM `ASSETDATA` WHERE `oldid`=`sm`.`id`));

ALTER TABLE `ASSETDATA`
	DROP COLUMN oldid;

# limiting access by ip
ALTER TABLE COMPANIES ADD COLUMN usedefaultipaccesslist TINYINT(1) NOT NULL DEFAULT 1 AFTER taxaddress;
ALTER TABLE COMPANIES ADD COLUMN ipaccesslist VARCHAR(1024) NOT NULL DEFAULT '' AFTER usedefaultipaccesslist;

ALTER TABLE USERS ADD COLUMN ipaccesstype INTEGER NOT NULL DEFAULT 0 AFTER sendmarketinginfo;
ALTER TABLE USERS ADD COLUMN ipaccesslist VARCHAR(1024) NOT NULL DEFAULT '' AFTER ipaccesstype;

# web version date
ALTER TABLE `SYSTEMCONFIG` 	ADD COLUMN `webversiondate`		DATE 		NOT NULL DEFAULT '0000-00-00' 	AFTER `systemcertificate`;
ALTER TABLE `SYSTEMCONFIG` 	ADD COLUMN `webversionnumber`	VARCHAR(20) NOT NULL DEFAULT '' 			AFTER `webversiondate`;
ALTER TABLE `SYSTEMCONFIG` 	ADD COLUMN `webversionstring`	VARCHAR(20) NOT NULL DEFAULT '' 			AFTER `webversionnumber`;
ALTER TABLE `ORDERITEMS` 	ADD COLUMN `orderwebversion`	VARCHAR(20)	NOT NULL AFTER `uploadmethod`;

ALTER TABLE `PRODUCTS` 		ADD COLUMN `assetid`			INTEGER							NOT NULL DEFAULT 0  AFTER `weight`;
ALTER TABLE `PRODUCTS` 		ADD COLUMN `skucode`			VARCHAR(50) CHARACTER SET utf8 	NOT NULL DEFAULT '' AFTER `code`;
ALTER TABLE `ORDERITEMS` 	ADD COLUMN `skucode`			VARCHAR(50) CHARACTER SET utf8 	NOT NULL DEFAULT '' AFTER `productcode`;

ALTER TABLE `PRODUCTS`		DROP COLUMN `defaultpapercode`;
ALTER TABLE `PRODUCTS`		DROP COLUMN `defaultcovercode`;
ALTER TABLE `PRODUCTS`		DROP COLUMN `defaultpagecount`;

ALTER TABLE `APPLICATIONFILES` MODIFY COLUMN `ref` VARCHAR(255) NOT NULL;


# DUPLICATES / ALTERNATIVES

ALTER TABLE `ORDERITEMS` 	ADD COLUMN `isalternativeproduct`	TINYINT(1) 	NOT NULL DEFAULT 0 AFTER `origuploadref`;
ALTER TABLE `ORDERITEMS` 	ADD COLUMN `parentorderitemid`		INTEGER		NOT NULL DEFAULT 0 AFTER `isalternativeproduct`;


# TASKS

ALTER TABLE `EXPORTEVENTS` RENAME TO `TRIGGERS`;
ALTER TABLE `TRIGGERS` ADD COLUMN `task1` VARCHAR(50) NOT NULL DEFAULT '' AFTER `filenameformat`;
ALTER TABLE `TRIGGERS` ADD COLUMN `task2` VARCHAR(50) NOT NULL DEFAULT '' AFTER `task1`;

ALTER TABLE `SYSTEMCONFIG` ADD COLUMN `cronlastruntime` DATETIME 	NOT NULL DEFAULT '0000-00-00 00:00:00';
ALTER TABLE `SYSTEMCONFIG` ADD COLUMN `cronactive` 		TINYINT(1) 	NOT NULL DEFAULT '0';


CREATE TABLE `EVENTS` (
  `id` 				INTEGER 		NOT NULL AUTO_INCREMENT,
  `datecreated` 	DATETIME 		NOT NULL DEFAULT '0000-00-00 00:00:00',
  `companycode` 	VARCHAR(50) 	NOT NULL DEFAULT '',
  `groupcode` 		VARCHAR(50) 	NOT NULL DEFAULT '',
  `webbrandcode` 	VARCHAR(50) 	NOT NULL DEFAULT '',
  `taskcode` 		VARCHAR(50) 	NOT NULL DEFAULT '',
  `runcount` 		INT(11) 		NOT NULL DEFAULT '0',
  `maxruncount` 	INT(11) 		NOT NULL DEFAULT '0',
  `lastruntime` 	DATETIME 		NOT NULL DEFAULT '0000-00-00 00:00:00',
  `nextruntime` 	DATETIME 		NOT NULL DEFAULT '0000-00-00 00:00:00',
  `parentid` 		INT(11) 		NOT NULL DEFAULT '0',
  `statuscode` 		INT(11) 		NOT NULL DEFAULT '0',
  `statusmessage` 	VARCHAR(70) 	NOT NULL DEFAULT '',
  `active` 			TINYINT(1) 		NOT NULL DEFAULT '0',
  `priority` 		TINYINT(1) 		NOT NULL DEFAULT '0',
  `param1` 			BLOB 			NOT NULL,
  `param2` 			BLOB 			NOT NULL,
  `param3` 			VARCHAR(256) 	NOT NULL DEFAULT '',
  `param4` 			VARCHAR(256) 	NOT NULL DEFAULT '',
  `param5` 			VARCHAR(256) 	NOT NULL DEFAULT '',
  `param6` 			VARCHAR(256) 	NOT NULL DEFAULT '',
  `param7` 			VARCHAR(256) 	NOT NULL DEFAULT '',
  `param8` 			VARCHAR(256) 	NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

CREATE TABLE `TASKS` (
	`id` 					INTEGER 		NOT NULL AUTO_INCREMENT,
	`datecreated` 			DATETIME 		NOT NULL DEFAULT '0000-00-00 00:00:00',
	`taskcode` 				VARCHAR(50) 	NOT NULL DEFAULT '',
	`taskname` 				VARCHAR(255) 	NOT NULL DEFAULT '',
	`intervaltype` 			INT 			NOT NULL DEFAULT '0',
	`intervalvalue` 		VARCHAR(50) 	NOT NULL DEFAULT '',
	`lastruntime` 			DATETIME 		NOT NULL DEFAULT '0000-00-00 00:00:00',
	`nextruntime` 			DATETIME 		NOT NULL DEFAULT '0000-00-00 00:00:00',
	`statuscode` 			INT 			NOT NULL DEFAULT '0',
	`statusmessage` 		VARCHAR(70) 	NOT NULL DEFAULT '',
	`runstatus` 			INT 			NOT NULL DEFAULT '0',
	`maxruncount` 			INT 			NOT NULL DEFAULT '0',
	`internal` 				TINYINT(1) 		NOT NULL DEFAULT '0',
	`scriptfilename` 		VARCHAR(50) 	NOT NULL DEFAULT '',
	`deleteexpiredinterval` INT 			NOT NULL DEFAULT '0',
	`active` 				TINYINT(1) 		NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

INSERT INTO `TASKS` VALUES
	(1,now(),'TAOPIX_EMAIL','en Email Task',1,'1','0000-00-00 00:00:00','0000-00-00 00:00:00',0,'',0,10,1,'emailTask.php',10,1),
	(2,now(),'TAOPIX_EXPORT','en Export Task',1,'1','0000-00-00 00:00:00','0000-00-00 00:00:00',0,'',0,10,1,'exportTask.php',10,1);

# drop paper and cover related tables
DROP TABLE `COVERS`;
DROP TABLE `PAPER`;
DROP TABLE `COVERPRICES`;
DROP TABLE `PAPERPRICES`;
DROP TABLE `PRODUCTPRICES`;



# define regions for China
INSERT INTO `COUNTRYREGION` (`datecreated`, `countrycode`, `regioncode`, `regionname`, `regiongroupcode`)
VALUES
	(now(), 'CN','ANHUI', 'zh_cn 安徽省<p>en AnHui', ''),
	(now(), 'CN','BEIJING', 'zh_cn 北京市<p>en BeiJing', ''),
	(now(), 'CN','CHONGQING', 'zh_cn 重庆市<p>en ChongQing', ''),
	(now(), 'CN','FUJIAN', 'zh_cn 福建省<p>en FuJian', ''),
	(now(), 'CN','GANSU', 'zh_cn 甘肃省<p>en GanSu', ''),
	(now(), 'CN','GUANGDONG', 'zh_cn 广东省<p>en GuangDong', ''),
	(now(), 'CN','GUANGXI', 'zh_cn 广西自治区<p>en GuangXi', ''),
	(now(), 'CN','GUIZHOU', 'zh_cn 贵州省<p>en GuiZhou', ''),
	(now(), 'CN','HAINAN', 'zh_cn 海南省<p>en HaiNan', ''),
	(now(), 'CN','HEBEI', 'zh_cn 湖北省<p>en HeBei', ''),
	(now(), 'CN','HEILONGJIANG', 'zh_cn 黑龙江省<p>en HeiLongJiang', ''),
	(now(), 'CN','HENAN', 'zh_cn 河南省<p>en HeNan', ''),
	(now(), 'CN','HONGKONG', 'zh_cn 香港特别行政区<p>en HongKong', ''),
	(now(), 'CN','HUBEI', 'zh_cn 河北省<p>en HuBei', ''),
	(now(), 'CN','HUNAN', 'zh_cn 湖南省<p>en HuNan', ''),
	(now(), 'CN','JIANGXI', 'zh_cn 江西省<p>en Jiangxi', ''),
	(now(), 'CN','JIANGSU', 'zh_cn 江苏省<p>en JiangSu', ''),
	(now(), 'CN','JILIN', 'zh_cn 吉林省<p>en JiLin', ''),
	(now(), 'CN','LIAONING', 'zh_cn 辽宁省<p>en LiaoNing', ''),
	(now(), 'CN','MACAU', 'zh_cn 澳门特别行政区<p>en Macau', ''),
	(now(), 'CN','NEIMENG', 'zh_cn 内蒙古<p>en NeiMeng', ''),
	(now(), 'CN','NINGXIA', 'zh_cn 宁夏自治区<p>en NingXia', ''),
	(now(), 'CN','QINGHAI', 'zh_cn 青海省<p>en QingHai', ''),
	(now(), 'CN','SHANGDONG', 'zh_cn 山东省<p>en ShangDong', ''),
	(now(), 'CN','SHANGHAI', 'zh_cn 上海市<p>en ShangHai', ''),
	(now(), 'CN','SHANXI', 'zh_cn 山西省<p>en Shānxī', ''),
	(now(), 'CN','SHAANXI', 'zh_cn 陕西省<p>en Shǎnxī', ''),
	(now(), 'CN','SICUAN', 'zh_cn 四川省<p>en SiCuan', ''),
	(now(), 'CN','TAIWAN', 'zh_cn 台湾省<p>en Taiwan', ''),
	(now(), 'CN','TIANJIN', 'zh_cn 天津市<p>en TianJin', ''),
	(now(), 'CN','XINJIANG', 'zh_cn 新疆自治区<p>en XinJiang', ''),
	(now(), 'CN','XIZANG', 'zh_cn 西藏自治区<p>en XiZang', ''),
	(now(), 'CN','YUNNAN', 'zh_cn 云南省<p>en YunNan', ''),
	(now(), 'CN','ZHEJIANG', 'zh_cn 浙江省<p>en ZheJiang', '');

# define address format for China
UPDATE `COUNTRIES`
	SET `displayfields`    = 'country<p>firstname<p>lastname<p>company<p>state<p>city<p>add1<p>add2',
		`fieldlabels`      = 'str_LabelCountry,str_LabelFirstName,str_LabelLastName,str_LabelCompanyName,str_LabelProvince,str_LabelTownCity,str_LabelAddressLine1,str_LabelAddressLine2',
		`compulsoryfields` = 'country,firstname,lastname,state,city,add1',
		`displayformat`    = '[firstname] [lastname]<br>[company]<br>[add1]<br>[add2]<br>[city] [regioncode]  [postcode]<br>[country]'
	WHERE `isocode2`='CN';


# set current version of web code
UPDATE SYSTEMCONFIG SET webversiondate = '2011-05-09', webversionnumber = '3.0.0.5', webversionstring = '3.0.0a5';


ALTER TABLE `SYSTEMCONFIG`
ADD COLUMN `lastinstallscriptnumber` VARCHAR(4) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER `webversionstring`;

# change name of GB county Durham
UPDATE `COUNTRYREGION` SET `regionname` = "en County Durham" WHERE `countrycode` = "GB" AND `regioncode` = "DURHAM";

# add GB county Teeside
INSERT INTO `COUNTRYREGION` (`datecreated`, `countrycode`, `regioncode`, `regionname`, `regiongroupcode`)
VALUES (now(), 'GB','TEESIDE','en Teeside','EN');

# only in order items
ALTER TABLE `ORDERHEADER` DROP COLUMN `canreorder`;

# new branding email settings
ALTER TABLE BRANDING
	ADD COLUMN smtpadminactive 				INTEGER NOT NULL DEFAULT '1' AFTER smtpadminaddress,
	ADD COLUMN smtpproductionactive 		INTEGER NOT NULL DEFAULT '1' AFTER smtpproductionaddress,
	ADD COLUMN smtporderconfirmationactive 	INTEGER NOT NULL DEFAULT '1' AFTER smtporderconfirmationaddress,
	ADD COLUMN smtpsaveorderactive 			INTEGER NOT NULL DEFAULT '1' AFTER smtpsaveorderaddress,

	ADD COLUMN smtpshippingname 			VARCHAR(200) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' AFTER smtpsaveorderactive,
	ADD COLUMN smtpshippingaddress 			VARCHAR(200) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' AFTER smtpshippingname,
	ADD COLUMN smtpshippingactive 			INTEGER NOT NULL DEFAULT '1' AFTER smtpshippingaddress,

	ADD COLUMN smtpnewaccountname 			VARCHAR(200) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' AFTER smtpshippingactive,
	ADD COLUMN smtpnewaccountaddress 		VARCHAR(200) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' AFTER smtpnewaccountname,
	ADD COLUMN smtpnewaccountactive 		INTEGER NOT NULL DEFAULT '1' AFTER smtpnewaccountaddress,

	ADD COLUMN smtpresetpasswordname 		VARCHAR(200) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' AFTER smtpnewaccountactive,
	ADD COLUMN smtpresetpasswordaddress 	VARCHAR(200) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' AFTER smtpresetpasswordname,
	ADD COLUMN smtpresetpasswordactive 		INTEGER NOT NULL DEFAULT '1' AFTER smtpresetpasswordaddress;

# add new columns to BRANDING table
ALTER TABLE `BRANDING`
	ADD COLUMN `mainwebsiteurl` 					VARCHAR(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' AFTER `weburl`,
	ADD COLUMN `macdownloadurl` 					VARCHAR(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' AFTER `mainwebsiteurl`,
	ADD COLUMN `win32downloadurl` 					VARCHAR(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' AFTER `macdownloadurl`,
	ADD COLUMN `supporttelephonenumber`				VARCHAR(50)  CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' AFTER `win32downloadurl`,
	ADD COLUMN `supportemailaddress` 				VARCHAR(50)  CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' AFTER `supporttelephonenumber`,
	ADD COLUMN `designersplashscreenadvertassetid`	INTEGER NOT NULL DEFAULT 0 AFTER `smtpresetpasswordactive`,
	ADD COLUMN `designersplashscreenadvertstartdate` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' AFTER `designersplashscreenadvertassetid`,
	ADD COLUMN `designersplashscreenadvertenddate`	 DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' AFTER `designersplashscreenadvertstartdate`;

ALTER TABLE `BRANDING`
	MODIFY COLUMN `smtpauthusername` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
	MODIFY COLUMN `smtpauthpassword` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;

ALTER TABLE `CURRENCIES` ADD UNIQUE INDEX isonumber USING BTREE(`isonumber`);


#
# Autoupdate changes
#

ALTER TABLE `APPLICATIONFILES`
	ADD COLUMN `companycode` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' AFTER `datecreated`,
	ADD COLUMN `appversion` VARCHAR(10) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' AFTER `ref`,
	ADD COLUMN `dataversion` INTEGER NOT NULL DEFAULT 0 AFTER `appversion`,
	ADD COLUMN `categorycode` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' AFTER `dataversion`,
	ADD COLUMN `description` VARCHAR(1024) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' AFTER `name`,
	ADD COLUMN `products` VARCHAR(1024) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' AFTER `description`,
	ADD COLUMN `themes` VARCHAR(1024) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' AFTER `products`,
	ADD COLUMN `encrypted` TINYINT(1) NOT NULL DEFAULT 0 AFTER `datemodified`,
	ADD COLUMN `updatepriority` INTEGER NOT NULL DEFAULT 0 AFTER `encrypted`,
	ADD COLUMN `dependencies` VARCHAR(1024) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' AFTER `updatepriority`,
	ADD COLUMN `size` INTEGER UNSIGNED NOT NULL DEFAULT 0 AFTER `dependencies`,
	ADD COLUMN `checksum` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' AFTER `size`,
	ADD COLUMN `hasfpo` TINYINT NOT NULL DEFAULT 0 AFTER `checksum`,
	ADD COLUMN `haspreview` TINYINT(1) NOT NULL DEFAULT 0 AFTER `hasfpo`,
	ADD COLUMN `separatecomponents` TINYINT(1) NOT NULL DEFAULT 0 AFTER `haspreview`,
	ADD COLUMN `deleted` TINYINT(1) NOT NULL DEFAULT 0 AFTER `active`;

ALTER TABLE `APPLICATIONBUILD`
	ADD COLUMN `macarchivefilesize` INTEGER UNSIGNED NOT NULL DEFAULT 0 AFTER `win32version`,
	ADD COLUMN `win32archivefilesize` INTEGER UNSIGNED NOT NULL DEFAULT 0 AFTER `macarchivefilesize`,
	ADD COLUMN `macarchivechecksum` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' AFTER `win32archivefilesize`,
	ADD COLUMN `win32archivechecksum` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' AFTER `macarchivechecksum`,
	ADD COLUMN `macupdatepriority` INT NOT NULL DEFAULT 0 AFTER `win32archivechecksum`,
	ADD COLUMN `win32updatepriority` INT NOT NULL DEFAULT 0 AFTER `macupdatepriority`,
	ADD COLUMN `machaspreview` TINYINT(1) NOT NULL DEFAULT 0 AFTER `win32updatepriority`,
	ADD COLUMN `win32haspreview` TINYINT(1) NOT NULL DEFAULT 0 AFTER `machaspreview`;

INSERT INTO APPLICATIONFILES (`datecreated`, `companycode`, `type`, `ref`, `appversion`, `dataversion`, `categorycode`,
			`categoryname`, `name`, `description`, `filename`, `datemodified`, `active`, `deleted`, `products`, `themes`, `dependencies`,
			`checksum`)
	SELECT `datecreated`, `companycode`, 0, `code`, `appversion`, `dataversion`, `categorycode`, `categoryname`, `name`,
			`description`, `filename`, `version`, `active`, `deleted`, "", "", "", ""
	FROM PRODUCTS;

UPDATE APPLICATIONFILES SET `products` = "*ALL*", `themes` = "*ALL*" WHERE `type` > 0;

ALTER TABLE `PRODUCTS` ADD COLUMN `type` INT NOT NULL DEFAULT 0 AFTER `description`;

ALTER TABLE `PRODUCTS`
	ADD COLUMN `hasdimensions` TINYINT(1) NOT NULL DEFAULT 0 AFTER `assetid`,
	ADD COLUMN `minpagecount` INTEGER NOT NULL DEFAULT 0 AFTER `hasdimensions`,
	ADD COLUMN `maxpagecount` INTEGER NOT NULL DEFAULT 0 AFTER `minpagecount`,
	ADD COLUMN `defaultpagecount` INTEGER NOT NULL DEFAULT 0 AFTER `maxpagecount`,
	ADD COLUMN `pageinsertcount` INTEGER NOT NULL DEFAULT 0 AFTER `defaultpagecount`,
	ADD COLUMN `pagepaperwidth` VARCHAR(25) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' AFTER `pageinsertcount`,
	ADD COLUMN `pagepaperheight` VARCHAR(25) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' AFTER `pagepaperwidth`,
	ADD COLUMN `pagebleed` VARCHAR(25) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' AFTER `pagepaperheight`,
	ADD COLUMN `pageisspreads` TINYINT(1) NOT NULL DEFAULT 0 AFTER `pagebleed`,
	ADD COLUMN `pageinsidebleed` TINYINT(1) NOT NULL DEFAULT 0 AFTER `pageisspreads`,
	ADD COLUMN `pagewidth` VARCHAR(25) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' AFTER `pageinsidebleed`,
	ADD COLUMN `pageheight` VARCHAR(25) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' AFTER `pagewidth`,
	ADD COLUMN `pagefirstpage` INTEGER NOT NULL DEFAULT 0 AFTER `pageheight`,
	ADD COLUMN `cover1active` TINYINT(1) NOT NULL DEFAULT 0 AFTER `pagefirstpage`,
	ADD COLUMN `cover1type` INTEGER NOT NULL DEFAULT 0 AFTER `cover1active`,
	ADD COLUMN `cover1paperwidth` VARCHAR(25) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' AFTER `cover1type`,
	ADD COLUMN `cover1paperheight` VARCHAR(25) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' AFTER `cover1paperwidth`,
	ADD COLUMN `cover1bleed` VARCHAR(25) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' AFTER `cover1paperheight`,
	ADD COLUMN `cover1backflap` VARCHAR(25) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' AFTER `cover1bleed`,
	ADD COLUMN `cover1frontflap` VARCHAR(25) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' AFTER `cover1backflap`,
	ADD COLUMN `cover1wraparound` VARCHAR(25) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' AFTER `cover1frontflap`,
	ADD COLUMN `cover1spine` VARCHAR(25) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' AFTER `cover1wraparound`,
	ADD COLUMN `cover1flexiblespine` TINYINT(1) NOT NULL DEFAULT 0 AFTER `cover1spine`,
	ADD COLUMN `cover1width` VARCHAR(25) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' AFTER `cover1flexiblespine`,
	ADD COLUMN `cover1height` VARCHAR(25) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' AFTER `cover1width`,
	ADD COLUMN `cover1flexiblespinedata` VARCHAR(1024) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' AFTER `cover1height`,
	ADD COLUMN `cover2active` TINYINT(1) NOT NULL DEFAULT 0 AFTER `cover1flexiblespinedata`,
	ADD COLUMN `cover2paperwidth` VARCHAR(25) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' AFTER `cover2active`,
	ADD COLUMN `cover2paperheight` VARCHAR(25) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' AFTER `cover2paperwidth`,
	ADD COLUMN `cover2bleed` VARCHAR(25) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' AFTER `cover2paperheight`,
	ADD COLUMN `cover2width` VARCHAR(25) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' AFTER `cover2bleed`,
	ADD COLUMN `cover2height` VARCHAR(25) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' AFTER `cover2width`;

CREATE TABLE `PRODUCTCOLLECTIONLINK` (
	`id` int(11) NOT NULL auto_increment,
	`datecreated` datetime NOT NULL default '0000-00-00 00:00:00',
	`companycode` varchar(50) NOT NULL,
	`collectioncode` varchar(50) NOT NULL,
	`productcode` varchar(50) NOT NULL,
	PRIMARY KEY  (`id`),
	KEY `collectioncode` (`collectioncode`),
	KEY `productcode` (`productcode`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;


INSERT INTO PRODUCTCOLLECTIONLINK (`datecreated`, `companycode`, `collectioncode`, `productcode`)
	SELECT `datecreated`, `companycode`, `code`, `code` FROM PRODUCTS;

ALTER TABLE `LICENSEKEYS`
	ADD COLUMN `keyfilesize` INTEGER UNSIGNED NOT NULL DEFAULT 0 AFTER `keyfilenameversion`,
	ADD COLUMN `keyfilechecksum` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' AFTER `keyfilesize`,
	ADD COLUMN `keyupdatepriority` INTEGER NOT NULL DEFAULT 0 AFTER `keyfilechecksum`;

CREATE TABLE `SHAREDITEMS`
(
   `id` INT NOT NULL AUTO_INCREMENT,
   `datecreated` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
   `action` VARCHAR(20) NOT NULL,
   `method` VARCHAR(20) NOT NULL,
   `uniqueid` VARCHAR(50) NOT NULL,
   `userid` INTEGER NOT NULL DEFAULT 0,
   `orderitemid` VARCHAR(20) NOT NULL,
   `orderid` VARCHAR(20) NOT NULL,
   `productcode` VARCHAR(50) NOT NULL,
   `webbrandcode` VARCHAR(50) NOT NULL,
   `recipient` VARCHAR(1024) NOT NULL,
   PRIMARY KEY (`id`),
   INDEX codes (`uniqueid`)
)
ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

INSERT INTO `ACTIVITYLOG`
	(`id`, `datecreated`, `sessionid`, `orderid`, `userid`, `userlogin`, `username`, `importance`, `sectioncode`, `actioncode`, `actionnotes`, `success`)
	VALUES (0, now(), 0, 0, 0, '', (SELECT USER()), 0, 'UPGRADE', '3.0.0a5', 'FINISHED', 1);

#
# DDL END
#

SET FOREIGN_KEY_CHECKS = 1;
