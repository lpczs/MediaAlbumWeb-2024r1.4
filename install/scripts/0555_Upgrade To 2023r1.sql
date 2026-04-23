#
SET FOREIGN_KEY_CHECKS = 0;

#
# DDL START
#

UPDATE `SYSTEMCONFIG` SET `webversiondate` = '2023-08-11';
UPDATE `SYSTEMCONFIG` SET `webversionnumber` = '2023.1.0';
UPDATE `SYSTEMCONFIG` SET `webversionstring`= '2023r1';

UPDATE `COUNTRYREGION`
SET regionname = REPLACE(regionname, SUBSTRING(regionname, 1, 2), 'es') where countrycode = 'MX';

DELETE FROM `COUNTRYREGION` where countrycode = 'MX' and regioncode = 'DIF';

UPDATE `COUNTRIES` SET
	`displayfields` = 'country<p>firstname<p>lastname<p>company<p>add1<p>add2<p>city<p>state<p>postcode',
	`compulsoryfields` = 'firstname,lastname,add1,city,state,postcode',
 	`displayformat` = '[firstname] [lastname]<br>[company]<br>[add1]<br>[add2]<br>[city] [regioncode] [postcode]<br>[country]',
	`fieldlabels` = 'str_LabelCountry,str_LabelFirstName,str_LabelLastName,str_LabelCompanyName,str_LabelAddressLine1,str_LabelAddressLine2,str_LabelTownCity,str_LabelState,str_LabelZIPCode'
WHERE `isocode2` = 'MX';

INSERT INTO `COUNTRYREGION`
	(`datecreated`,  `countrycode`, `regioncode`, `regionname`, `regiongroupcode`)
VALUES
	(NOW(), 'MX' , 'CMX', 'es Ciudad de México', '');

UPDATE `USERS` SET `addressupdated` = '0' WHERE `countrycode` = 'MX';

ALTER TABLE `productcollectionlink`
    ADD COLUMN `productconfigurationflags` INT NOT NULL DEFAULT 0 AFTER `productwizardmodeonline`,
	ADD COLUMN `productpagecontentassignmode` INT NOT NULL DEFAULT 0 AFTER `productconfigurationflags`,
	ADD COLUMN `collectionsummary` VARCHAR(4096) NOT NULL DEFAULT '' AFTER `collectiontextengineversion`,
DROP COLUMN `collectioncategorycode`,
	DROP COLUMN `collectioncategoryname`,
    ADD COLUMN `productorientation` INT NOT NULL DEFAULT 0 AFTER `availableonline`,
	ADD COLUMN `productsizecode` VARCHAR(25) NOT NULL DEFAULT '' AFTER `productorientation`,
	ADD COLUMN `productsizename` MEDIUMTEXT NOT NULL AFTER `productsizecode`,
	ADD COLUMN `productsizearea` INT NOT NULL DEFAULT '0' AFTER `productsizename`,
    ADD COLUMN `collectionthumbnailresourcedevicepixelratio` TINYINT(3) NOT NULL DEFAULT 1 AFTER `productsizearea`,
    ADD COLUMN `collectionpreviewresourcedevicepixelratio` TINYINT(3) NOT NULL DEFAULT 1 AFTER `collectionthumbnailresourcedevicepixelratio`,
    ADD COLUMN `productthumbnailresourcedevicepixelratio` TINYINT(3) NOT NULL DEFAULT 1 AFTER `collectionpreviewresourcedevicepixelratio`,
    ADD COLUMN `productpreviewresourcedevicepixelratio` TINYINT(3) NOT NULL DEFAULT 1 AFTER `productthumbnailresourcedevicepixelratio`;


ALTER TABLE `vouchers`
DROP COLUMN `productcategoryname`,
	DROP COLUMN `productcategorycode`;

UPDATE `applicationfiles` SET `categorycode` = '', `categoryname` = '' WHERE `type` = 0;

UPDATE `productcollectionresources` SET `islatest` = '0', `nextpurgetime` = (curtime() + INTERVAL 1 DAY) WHERE `resourcekind` = 100;

UPDATE `productcollectionlink` SET `publishversion` = '0';

ALTER TABLE `licensekeys`
    ADD COLUMN `usedefaultaccountpagesurl` TINYINT(1) NOT NULL DEFAULT 1 AFTER `componentupsellsettings`,
    ADD COLUMN `accountpagesurl` VARCHAR(100) NOT NULL DEFAULT '' AFTER `usedefaultaccountpagesurl`,
    ADD COLUMN `keyfiledataversion` INT NOT NULL DEFAULT 1 AFTER `accountpagesurl`,
    ADD COLUMN `promopaneloverridemode` TINYINT(4) NOT NULL DEFAULT 0 AFTER `keyfiledataversion`,
    ADD COLUMN `promopaneloverridestartdate` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' AFTER `promopaneloverridemode`,
    ADD COLUMN `promopaneloverrideenddate` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' AFTER `promopaneloverridestartdate`,
    ADD COLUMN `promopaneloverrideurl` VARCHAR(100) NOT NULL DEFAULT '' AFTER `promopaneloverrideenddate`,
    ADD COLUMN `promopaneloverrideheight` INT NOT NULL DEFAULT 0 AFTER `promopaneloverrideurl`,
    ADD COLUMN `promopaneloverridepixelratio` TINYINT(4) NOT NULL DEFAULT 1 AFTER `promopaneloverrideheight`,
    ADD COLUMN `promopaneloverridehidpicantoggle` TINYINT(1) NOT NULL DEFAULT 0 AFTER `promopaneloverridepixelratio`;


ALTER TABLE `branding`
    ADD COLUMN `usedefaultaccountpagesurl` TINYINT(1) NOT NULL DEFAULT 1 AFTER `oauthtoken`,
    ADD COLUMN `accountpagesurl` VARCHAR(100) NOT NULL DEFAULT '' AFTER `usedefaultaccountpagesurl`;


TRUNCATE `cachedata`;

#
# DDL END
#

SET FOREIGN_KEY_CHECKS = 1;

