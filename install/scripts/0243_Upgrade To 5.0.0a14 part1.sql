#
SET FOREIGN_KEY_CHECKS = 0;

#
# DDL START
#

INSERT INTO `ACTIVITYLOG`
	(`id`, `datecreated`, `sessionid`, `orderid`, `userid`, `userlogin`, `username`, `importance`, `sectioncode`, `actioncode`, `actionnotes`, `success`)
	VALUES (0, now(), 0, 0, 0, '', (SELECT USER()), 0, 'UPGRADE', '5.0.0a14 part1', 'STARTED', 1);

UPDATE `SYSTEMCONFIG` SET `webversiondate` = '2014-08-06';
UPDATE `SYSTEMCONFIG` SET `webversionnumber` = '5.0.0.14';
UPDATE `SYSTEMCONFIG` SET `webversionstring`= '5.0.0a14';

ALTER TABLE `PRODUCTCOLLECTIONLINK`
ADD COLUMN `collectioncategorycode` VARCHAR(50) NOT NULL DEFAULT '' AFTER `collectionname`,
ADD COLUMN `collectioncategoryname` VARCHAR(1048) NOT NULL DEFAULT '' AFTER `collectioncategorycode`,
ADD COLUMN `collectiontype` INT(11) NOT NULL DEFAULT '0' AFTER `collectioncategoryname`,
ADD COLUMN `productdescription` varchar(1024) NOT NULL DEFAULT '' AFTER `productname`,
ADD COLUMN `producthasdimensions` TINYINT(1) NOT NULL DEFAULT '0' AFTER `productdescription`,
ADD COLUMN `productminpagecount` INT(11) NOT NULL DEFAULT '0' AFTER `producthasdimensions`,
ADD COLUMN `productmaxpagecount` INT(11) NOT NULL DEFAULT '0' AFTER `productminpagecount`,
ADD COLUMN `productdefaultpagecount` INT(11) NOT NULL DEFAULT '0' AFTER `productmaxpagecount`,
ADD COLUMN `productpageinsertcount` INT(11) NOT NULL DEFAULT '0' AFTER `productdefaultpagecount`,
ADD COLUMN `productpagepaperwidth` VARCHAR(25) NOT NULL DEFAULT '' AFTER `productpageinsertcount`,
ADD COLUMN `productpagepaperheight` VARCHAR(25) NOT NULL DEFAULT '' AFTER `productpagepaperwidth`,
ADD COLUMN `productpagebleed` VARCHAR(25) NOT NULL DEFAULT '' AFTER `productpagepaperheight`,
ADD COLUMN `productpageisspreads` TINYINT(1) NOT NULL DEFAULT '0' AFTER `productpagebleed`,
ADD COLUMN `productpageinsidebleed` TINYINT(1) NOT NULL DEFAULT '0' AFTER `productpageisspreads`,
ADD COLUMN `productpagewidth` VARCHAR(25) NOT NULL DEFAULT '' AFTER `productpageinsidebleed`,
ADD COLUMN `productpageheight` VARCHAR(25) NOT NULL DEFAULT '' AFTER `productpagewidth`,
ADD COLUMN `productpagefirstpage` INT(11) NOT NULL DEFAULT '0' AFTER `productpageheight`,
ADD COLUMN `productcover1active` TINYINT(1) NOT NULL DEFAULT '0' AFTER `productpagefirstpage`,
ADD COLUMN `productcover1type` INT(11) NOT NULL DEFAULT '0' AFTER `productcover1active`,
ADD COLUMN `productcover1paperwidth` VARCHAR(25) NOT NULL DEFAULT '' AFTER `productcover1type`,
ADD COLUMN `productcover1paperheight` VARCHAR(25) NOT NULL DEFAULT '' AFTER `productcover1paperwidth`,
ADD COLUMN `productcover1bleed` VARCHAR(25) NOT NULL DEFAULT '' AFTER `productcover1paperheight`,
ADD COLUMN `productcover1backflap` VARCHAR(25) NOT NULL DEFAULT '' AFTER `productcover1bleed`,
ADD COLUMN `productcover1frontflap` VARCHAR(25) NOT NULL DEFAULT '' AFTER `productcover1backflap`,
ADD COLUMN `productcover1wraparound` VARCHAR(25) NOT NULL DEFAULT '' AFTER `productcover1frontflap`,
ADD COLUMN `productcover1spine` VARCHAR(25) NOT NULL DEFAULT '' AFTER `productcover1wraparound`,
ADD COLUMN `productcover1flexiblespine` TINYINT(1) NOT NULL DEFAULT '0' AFTER `productcover1spine`,
ADD COLUMN `productcover1width` VARCHAR(25) NOT NULL DEFAULT '' AFTER `productcover1flexiblespine`,
ADD COLUMN `productcover1height` VARCHAR(25) NOT NULL DEFAULT '' AFTER `productcover1width`,
ADD COLUMN `productcover1flexiblespinedata` VARCHAR(1024) NOT NULL DEFAULT '' AFTER `productcover1height`,
ADD COLUMN `productcover2active` TINYINT(1) NOT NULL DEFAULT '0' AFTER `productcover1flexiblespinedata`,
ADD COLUMN `productcover2paperwidth` VARCHAR(25) NOT NULL DEFAULT '' AFTER `productcover2active`,
ADD COLUMN `productcover2paperheight` VARCHAR(25) NOT NULL DEFAULT '' AFTER `productcover2paperwidth`,
ADD COLUMN `productcover2bleed` VARCHAR(25) NOT NULL DEFAULT '' AFTER `productcover2paperheight`,
ADD COLUMN `productcover2width` VARCHAR(25) NOT NULL DEFAULT '' AFTER `productcover2bleed`,
ADD COLUMN `productcover2height` VARCHAR(25) NOT NULL DEFAULT '' AFTER `productcover2width`;


UPDATE `PRODUCTCOLLECTIONLINK` pcl JOIN `PRODUCTS` pr ON `pcl`.`productcode` = `pr`.`code` 
SET `pcl`.`collectioncategorycode` = `pr`.`categorycode`, 
`pcl`.`collectioncategoryname` = `pr`.`categoryname`, 
`pcl`.`collectiontype` = `pr`.`type`, 
`pcl`.`productdescription` = `pr`.`description`,
`pcl`.`producthasdimensions` = `pr`.`hasdimensions`,
`pcl`.`productminpagecount` = `pr`.`minpagecount`,
`pcl`.`productmaxpagecount` = `pr`.`maxpagecount`,
`pcl`.`productdefaultpagecount` = `pr`.`defaultpagecount`,
`pcl`.`productpageinsertcount` = `pr`.`pageinsertcount`,
`pcl`.`productpagepaperwidth` = `pr`.`pagepaperwidth`,
`pcl`.`productpagepaperheight` = `pr`.`pagepaperheight`,
`pcl`.`productpagebleed` = `pr`.`pagebleed`,
`pcl`.`productpageisspreads` = `pr`.`pageisspreads`,
`pcl`.`productpageinsidebleed` = `pr`.`pageinsidebleed`,
`pcl`.`productpagewidth` = `pr`.`pagewidth`,
`pcl`.`productpageheight` = `pr`.`pageheight`,
`pcl`.`productpagefirstpage` = `pr`.`pagefirstpage`,
`pcl`.`productcover1active` = `pr`.`cover1active`,
`pcl`.`productcover1type` = `pr`.`cover1type`,
`pcl`.`productcover1paperwidth` = `pr`.`cover1paperwidth`,
`pcl`.`productcover1paperheight` = `pr`.`cover1paperheight`,
`pcl`.`productcover1bleed` = `pr`.`cover1bleed`,
`pcl`.`productcover1backflap` = `pr`.`cover1backflap`,
`pcl`.`productcover1frontflap` = `pr`.`cover1frontflap`,
`pcl`.`productcover1wraparound` = `pr`.`cover1wraparound`,
`pcl`.`productcover1spine` = `pr`.`cover1spine`,
`pcl`.`productcover1flexiblespine` = `pr`.`cover1flexiblespine`,
`pcl`.`productcover1width` = `pr`.`cover1width`,
`pcl`.`productcover1height` = `pr`.`cover1height`,
`pcl`.`productcover1flexiblespinedata` = `pr`.`cover1flexiblespinedata`,
`pcl`.`productcover2active` = `pr`.`cover2active`,
`pcl`.`productcover2paperwidth` = `pr`.`cover2paperwidth`,
`pcl`.`productcover2paperheight` = `pr`.`cover2paperheight`,
`pcl`.`productcover2bleed` = `pr`.`cover2bleed`,
`pcl`.`productcover2width` = `pr`.`cover2width`,
`pcl`.`productcover2height` = `pr`.`cover2height`;


ALTER TABLE `PRODUCTS` 
DROP COLUMN `filename`,
DROP COLUMN `dataversion`,
DROP COLUMN `appversion`,
DROP COLUMN `version`,
DROP COLUMN `categorycode`,
DROP COLUMN `categoryname`,
DROP COLUMN `description`,
DROP COLUMN `type`,
DROP COLUMN `hasdimensions`,
DROP COLUMN `minpagecount`,
DROP COLUMN `maxpagecount`,
DROP COLUMN `defaultpagecount`,
DROP COLUMN `pageinsertcount`,
DROP COLUMN `pagepaperwidth`,
DROP COLUMN `pagepaperheight`,
DROP COLUMN `pagebleed`,
DROP COLUMN `pageisspreads`,
DROP COLUMN `pageinsidebleed`,
DROP COLUMN `pagewidth`,
DROP COLUMN `pageheight`,
DROP COLUMN `pagefirstpage`,
DROP COLUMN `cover1active`,
DROP COLUMN `cover1type`,
DROP COLUMN `cover1paperwidth`,
DROP COLUMN `cover1paperheight`,
DROP COLUMN `cover1bleed`,
DROP COLUMN `cover1backflap`,
DROP COLUMN `cover1frontflap`,
DROP COLUMN `cover1wraparound`,
DROP COLUMN `cover1spine`,
DROP COLUMN `cover1flexiblespine`,
DROP COLUMN `cover1width`,
DROP COLUMN `cover1height`,
DROP COLUMN `cover1flexiblespinedata`,
DROP COLUMN `cover2active`,
DROP COLUMN `cover2paperwidth`,
DROP COLUMN `cover2paperheight`,
DROP COLUMN `cover2bleed`,
DROP COLUMN `cover2width`,
DROP COLUMN `cover2height`;

INSERT INTO `ACTIVITYLOG`
(`id`, `datecreated`, `sessionid`, `orderid`, `userid`, `userlogin`, `username`, `importance`, `sectioncode`, `actioncode`, `actionnotes`, `success`)
VALUES (0, now(), 0, 0, 0, '', (SELECT USER()), 0, 'UPGRADE', '5.0.0a14 part1', 'FINISHED', 1);

#
# DDL END
#

SET FOREIGN_KEY_CHECKS = 1;
