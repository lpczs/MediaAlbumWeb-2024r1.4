#
SET FOREIGN_KEY_CHECKS = 0;

#
# DDL START
#


INSERT INTO `ACTIVITYLOG` 
	(`id`, `datecreated`, `sessionid`, `orderid`, `userid`, `userlogin`, `username`, `importance`, `sectioncode`, `actioncode`, `actionnotes`, `success`) 
	VALUES (0, now(), 0, 0, 0, '', (SELECT USER()), 0, 'UPGRADE', '3.0.0a3', 'STARTED', 1);

ALTER TABLE `SHIPPINGMETHODS` 
	ADD COLUMN `allowgroupingbycountry` TINYINT(1) NOT NULL DEFAULT 1 AFTER `allowgroupingbystoregroupname`,
	ADD COLUMN `allowgroupingbyregion` TINYINT(1) NOT NULL DEFAULT 1 AFTER `allowgroupingbycountry`,
	ADD COLUMN `usecollectfromstorescript` TINYINT(1) NOT NULL DEFAULT 0 AFTER `allowgroupingbyregion`;

ALTER TABLE `SHIPPINGMETHODS` 
	ADD COLUMN `storelocatorlogotype` varchar(100) NOT NULL default '' AFTER `usecollectfromstorescript`,
	ADD COLUMN `storelocatorlogowidth` INT NOT NULL default 0 AFTER `storelocatorlogotype`,
	ADD COLUMN `storelocatorlogoheight` INT NOT NULL default 0 AFTER `storelocatorlogowidth`,
	ADD COLUMN `storelocatorlogo` mediumblob AFTER `storelocatorlogoheight`;

# insert new export event
ALTER TABLE `EXPORTEVENTS` MODIFY COLUMN `eventcode` VARCHAR(50) NOT NULL;
    
INSERT INTO `EXPORTEVENTS` (`datecreated` ,`eventcode` ,`language` ,`exportformat` ,`includepaymentdata` ,`beautifiedxml` ,`subfolderformat` ,`filenameformat` ,`active`) 
VALUES (now(),'SHIPPEDDISTRIBUTIONCENTRERECEIVED' ,     'Default' , 'XML' , 0 , 1 , '' , '' , 0);

INSERT INTO `EXPORTEVENTS` (`datecreated` ,`eventcode` ,`language` ,`exportformat` ,`includepaymentdata` ,`beautifiedxml` ,`subfolderformat` ,`filenameformat` ,`active`) 
VALUES (now(),'SHIPPEDDISTRIBUTIONCENTRESHIPPED' ,     'Default' , 'XML' , 0 , 1 , '' , '' , 0);

INSERT INTO `EXPORTEVENTS` (`datecreated` ,`eventcode` ,`language` ,`exportformat` ,`includepaymentdata` ,`beautifiedxml` ,`subfolderformat` ,`filenameformat` ,`active`) 
VALUES (now(),'SHIPPEDSTORERECEIVED' ,     'Default' , 'XML' , 0 , 1 , '' , '' , 0);

INSERT INTO `EXPORTEVENTS` (`datecreated` ,`eventcode` ,`language` ,`exportformat` ,`includepaymentdata` ,`beautifiedxml` ,`subfolderformat` ,`filenameformat` ,`active`) 
VALUES (now(),'SHIPPEDSTORECUSTOMERCOLLECTED' ,     'Default' , 'XML' , 0 , 1 , '' , '' , 0);

ALTER TABLE `SITES` ADD COLUMN `isexternalstore` TINYINT(1) NOT NULL DEFAULT 0 AFTER `siteonline`;

INSERT INTO `ACTIVITYLOG` 
	(`id`, `datecreated`, `sessionid`, `orderid`, `userid`, `userlogin`, `username`, `importance`, `sectioncode`, `actioncode`, `actionnotes`, `success`) 
	VALUES (0, now(), 0, 0, 0, '', (SELECT USER()), 0, 'UPGRADE', '3.0.0a3', 'FINISHED', 1);

#
# DDL END
#

SET FOREIGN_KEY_CHECKS = 1;
