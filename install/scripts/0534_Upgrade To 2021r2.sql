#
SET FOREIGN_KEY_CHECKS = 0;

#
# DDL START
#

UPDATE `SYSTEMCONFIG` SET `webversiondate` = '2021-08-04';
UPDATE `SYSTEMCONFIG` SET `webversionnumber` = '2021.2.0';
UPDATE `SYSTEMCONFIG` SET `webversionstring`= '2021r2';

ALTER TABLE `PRODUCTS` ADD COLUMN `retroprints` TINYINT(1) NOT NULL DEFAULT 0 AFTER `averagepicturesperpage`;

ALTER TABLE `PRODUCTS` DROP COLUMN `assetid`;
ALTER TABLE `COMPONENTS` DROP COLUMN `assetid`;

ALTER TABLE `applicationfiles`
CHANGE COLUMN `appversion` `appversion` VARCHAR(20) NOT NULL DEFAULT '' ,
CHANGE COLUMN `datemodified` `versiondate` DATETIME NOT NULL ,
CHANGE COLUMN `datemodifiedonline` `versiondateonline` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00';

ALTER TABLE `vouchers`
  ADD COLUMN `description` varchar(512) NOT NULL DEFAULT '' AFTER `name`;

ALTER TABLE `DATAPOLICIES`
    CHANGE `unusedassets` `orderedunusedassets` TINYINT(1) DEFAULT 0 NOT NULL,
    CHANGE `unusedassetsage` `orderedunusedassetsage` INT(11) DEFAULT 90 NOT NULL,
    ADD `notorderedunusedassets` TINYINT(1) DEFAULT 0 NOT NULL AFTER `orderedunusedassetsage`,
    ADD `notorderedunusedassetsage` INT(11) DEFAULT 90 NOT NULL AFTER `notorderedunusedassets`;

UPDATE `DATAPOLICIES` SET `notorderedunusedassets` = `orderedunusedassets`, `notorderedunusedassetsage` = `orderedunusedassetsage`;

ALTER TABLE `ORDERHEADER` ADD COLUMN `redacted` TINYINT(1) DEFAULT 0 NOT NULL;

INSERT INTO `TASKS` (
  `datecreated`, `taskcode`, `taskname`, `intervaltype`, `intervalvalue`, `lastruntime`,
  `nextruntime`, `statuscode`, `statusmessage`, `runstatus`, `maxruncount`, `internal`,
  `scriptfilename`, `deleteexpiredinterval`, `active`
)
VALUES (
  NOW(), 'TAOPIX_PRODUCTCOLLECTIONRESOURCEDELETION', 'en  Product Collection Resource Deletion', 2, '03:00', '0000-00-00 00:00:00',
  '0000-00-00 00:00:00', 0, '', 0, 10, 1, 'productCollectionResourceDeletionTask.php', 10, 1
);

CREATE TABLE `PRODUCTCOLLECTIONRESOURCES` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `datecreated` DATETIME NOT NULL DEFAULT "0000-00-00 00:00:00",
  `collectionversiondate` DATETIME NOT NULL DEFAULT "0000-00-00 00:00:00",
  `collectioncode` VARCHAR(200) NOT NULL DEFAULT "",
  `resourceref` VARCHAR(50) NOT NULL DEFAULT "",
  `resourcekind` TINYINT(3) NOT NULL DEFAULT 0,
  `resourcedatauid` VARCHAR(100) NOT NULL DEFAULT "",
  `islatest` TINYINT(1) NOT NULL DEFAULT 0,
  `nextpurgetime` DATETIME NOT NULL DEFAULT "0000-00-00 00:00:00",
  PRIMARY KEY (`id`),
  UNIQUE INDEX `collectioncode_collectionversiondate` (`collectioncode` ASC, `collectionversiondate` ASC, `resourceref` ASC),
  INDEX `islatest_nextpurgetime` (`islatest` ASC, `nextpurgetime` ASC)
  )ENGINE=InnoDB DEFAULT CHARSET=utf8;


TRUNCATE `CACHEDATA`;

ALTER TABLE `PRODUCTCOLLECTIONLINK`
ADD COLUMN `publishversion` TINYINT(3) NOT NULL DEFAULT 0 AFTER `datecreated`,
ADD COLUMN `collectiondescription` MEDIUMTEXT NOT NULL DEFAULT "" AFTER `collectionname`,
ADD COLUMN `collectionmoreinformationurl` VARCHAR(1024) NOT NULL DEFAULT "" AFTER `collectioncategoryname`,
ADD COLUMN `collectionthumbnailresourceref` VARCHAR(50) NOT NULL DEFAULT "" AFTER `collectionmoreinformationurl`,
ADD COLUMN `collectionthumbnailresourcedatauid` VARCHAR(50) NOT NULL DEFAULT ""	AFTER `collectionthumbnailresourceref`,
ADD COLUMN `collectionpreviewresourceref` VARCHAR(50) NOT NULL DEFAULT "" AFTER `collectionthumbnailresourcedatauid`,
ADD COLUMN `collectionpreviewresourcedatauid` VARCHAR(50) NOT NULL DEFAULT "" AFTER `collectionpreviewresourceref`,
ADD COLUMN `collectionsortlevel` VARCHAR(50) NOT NULL DEFAULT "" AFTER collectiontype,
ADD COLUMN `collectiontextengineversion` TINYINT(3) NOT NULL DEFAULT 0 AFTER `collectionsortlevel`,
ADD COLUMN `productmoreinformationurl` VARCHAR(1024) NOT NULL DEFAULT 0	AFTER `productdescription`,
ADD COLUMN `productthumbnailresourceref` VARCHAR(50) NOT NULL DEFAULT "" AFTER `productmoreinformationurl`,
ADD COLUMN `productthumbnailresourcedatauid` VARCHAR(50) NOT NULL DEFAULT "" AFTER `productthumbnailresourceref`,
ADD COLUMN `productpreviewresourceref` VARCHAR(50) NOT NULL	DEFAULT "" AFTER `productthumbnailresourcedatauid`,
ADD COLUMN `productpreviewresourcedatauid` VARCHAR(50) NOT NULL	DEFAULT "" AFTER `productpreviewresourceref`,
ADD COLUMN `productpagesafemargin` VARCHAR(25) NOT NULL DEFAULT 0 AFTER `productpageinsidebleed`,
ADD COLUMN `productcover1safemargin` VARCHAR(25) NOT NULL DEFAULT 0	AFTER `productcover1bleed`,
ADD COLUMN `productcover2safemargin` VARCHAR(25) NOT NULL DEFAULT 0 AFTER `productcover2bleed`,
ADD COLUMN `productselectormodedesktop` TINYINT(3) NOT NULL	DEFAULT 0 AFTER `productcover2height`,
ADD COLUMN `productaimodedesktop` TINYINT(3) NOT NULL DEFAULT 0	AFTER `productwizardmodeonline`,
ADD COLUMN `productaimodeonline` TINYINT(3) NOT NULL DEFAULT 0 AFTER `productaimodedesktop`,
ADD COLUMN `productcalendarlocale` VARCHAR(10) NOT NULL	DEFAULT "" AFTER `productaimodeonline`,
ADD COLUMN `productcalendarlocalecanchange` TINYINT(1) NOT NULL DEFAULT 0 AFTER `productcalendarlocale`,
ADD COLUMN `productcalendarstartday` TINYINT(3) NOT NULL DEFAULT 0 AFTER `productcalendarlocalecanchange`,
ADD COLUMN `productcalendarstartdaycanchange` TINYINT(1) NOT NULL DEFAULT 0	AFTER `productcalendarstartday`,
ADD COLUMN `productcalendarstartmonth` TINYINT(3) NOT NULL DEFAULT 0 AFTER `productcalendarstartdaycanchange`,
ADD COLUMN `productcalendarstartmonthcanchange`	TINYINT(1) NOT NULL	DEFAULT 0 AFTER `productcalendarstartmonth`,
ADD COLUMN `productcalendarstartyear` INT(11) NOT NULL DEFAULT 0 AFTER `productcalendarstartmonthcanchange`,
ADD COLUMN `productcalendarstartyearcanchange`	TINYINT(1) NOT NULL	DEFAULT 0 AFTER `productcalendarstartyear`,
ADD COLUMN `productsortorder` INT(11) NOT NULL DEFAULT 0 AFTER `productcalendarstartyearcanchange`,
ADD COLUMN `producttarget` TINYINT(3) NOT NULL DEFAULT 0 AFTER `productsortorder`,
CHANGE COLUMN `collectionname` `collectionname` VARCHAR(2048) NOT NULL DEFAULT '' ,
CHANGE COLUMN `collectiontype` `collectiontype` TINYINT(3) NOT NULL DEFAULT '0' ,
CHANGE COLUMN `productname` `productname` VARCHAR(2048) NOT NULL DEFAULT '' ,
CHANGE COLUMN `productdescription` `productdescription` MEDIUMTEXT NOT NULL DEFAULT '' ,
CHANGE COLUMN `productpagepaperwidth` `productpagepaperwidth` VARCHAR(25) NOT NULL DEFAULT '0' ,
CHANGE COLUMN `productpagepaperheight` `productpagepaperheight` VARCHAR(25) NOT NULL DEFAULT '0' ,
CHANGE COLUMN `productpagebleed` `productpagebleed` VARCHAR(25) NOT NULL DEFAULT '0' ,
CHANGE COLUMN `productpagewidth` `productpagewidth` VARCHAR(25) NOT NULL DEFAULT '0' ,
CHANGE COLUMN `productpageheight` `productpageheight` VARCHAR(25) NOT NULL DEFAULT '0' ,
CHANGE COLUMN `productcover1paperwidth` `productcover1paperwidth` VARCHAR(25) NOT NULL DEFAULT '0' ,
CHANGE COLUMN `productcover1paperheight` `productcover1paperheight` VARCHAR(25) NOT NULL DEFAULT '0' ,
CHANGE COLUMN `productcover1bleed` `productcover1bleed` VARCHAR(25) NOT NULL DEFAULT '0' ,
CHANGE COLUMN `productcover1backflap` `productcover1backflap` VARCHAR(25) NOT NULL DEFAULT '0' ,
CHANGE COLUMN `productcover1frontflap` `productcover1frontflap` VARCHAR(25) NOT NULL DEFAULT '0' ,
CHANGE COLUMN `productcover1wraparound` `productcover1wraparound` VARCHAR(25) NOT NULL DEFAULT '0' ,
CHANGE COLUMN `productcover1spine` `productcover1spine` VARCHAR(25) NOT NULL DEFAULT '0' ,
CHANGE COLUMN `productcover1width` `productcover1width` VARCHAR(25) NOT NULL DEFAULT '0' ,
CHANGE COLUMN `productcover1height` `productcover1height` VARCHAR(25) NOT NULL DEFAULT '0' ,
CHANGE COLUMN `productcover1flexiblespinedata` `productcover1flexiblespinedata` VARCHAR(4096) NOT NULL DEFAULT '' ,
CHANGE COLUMN `productcover2paperwidth` `productcover2paperwidth` VARCHAR(25) NOT NULL DEFAULT '0' ,
CHANGE COLUMN `productcover2paperheight` `productcover2paperheight` VARCHAR(25) NOT NULL DEFAULT '0' ,
CHANGE COLUMN `productcover2bleed` `productcover2bleed` VARCHAR(25) NOT NULL DEFAULT '0' ,
CHANGE COLUMN `productcover2width` `productcover2width` VARCHAR(25) NOT NULL DEFAULT '0' ,
CHANGE COLUMN `productcover2height` `productcover2height` VARCHAR(25) NOT NULL DEFAULT '0' ,
CHANGE COLUMN `productwizardmodeonline` `productwizardmodeonline` TINYINT(3) NOT NULL DEFAULT '0';

#
# DDL END
#

SET FOREIGN_KEY_CHECKS = 1;

