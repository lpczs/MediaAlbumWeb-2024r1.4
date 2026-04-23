#
SET FOREIGN_KEY_CHECKS = 0;

#
# DDL START
#

UPDATE `SYSTEMCONFIG` SET `webversiondate` = '2017-05-05';
UPDATE `SYSTEMCONFIG` SET `webversionnumber` = '2017.1.0';
UPDATE `SYSTEMCONFIG` SET `webversionstring`= '2017r1';

ALTER TABLE `EVENTS`
ADD COLUMN `task1` VARCHAR(255) NOT NULL DEFAULT '' AFTER `param8`,
ADD COLUMN `task2` VARCHAR(255) NOT NULL DEFAULT '' AFTER `task1`;

ALTER TABLE `TAXZONES`
DROP COLUMN `useverifyscript`,
DROP COLUMN `usetaxscript`;

INSERT INTO `TAXRATES` (`datecreated`, `code`, `name`, `rate`)
VALUES (now(), 'TPX_CUSTOMTAX', 'en Taopix Custom tax', 0.0000);

ALTER TABLE `SYSTEMCONFIG`
	ADD COLUMN `tenantid` MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT 0 AFTER `systemcertificate`,
	ADD COLUMN `tenantkey` VARCHAR(10) NOT NULL DEFAULT '' AFTER `tenantid`,
	ADD COLUMN `tenantsecret` VARCHAR(32) NOT NULL DEFAULT '' AFTER `tenantkey`,
	ADD COLUMN `key` VARCHAR(50) NOT NULL DEFAULT '' AFTER `tenantsecret`,
	ADD COLUMN `secret` VARCHAR(100) NOT NULL DEFAULT '' AFTER `key`;

UPDATE `SYSTEMCONFIG` SET `key` = `ownercode`, `secret` = `systemkey`;

ALTER TABLE `OUTPUTDEVICES`
CHANGE COLUMN `jdfurl` `epwurl` VARCHAR(100) NOT NULL DEFAULT '',
CHANGE COLUMN `jdfurlversion` `epwurlversion` VARCHAR(50) NOT NULL DEFAULT '',
CHANGE COLUMN `jdfworkflowcode` `epwworkflowcode` VARCHAR(50) NOT NULL DEFAULT '',
CHANGE COLUMN `jdfworkflowname` `epwworkflowname` VARCHAR(50) NOT NULL DEFAULT '',
CHANGE COLUMN `jdfworkflowcompletionstatus` `epwworkflowcompletionstatus` INT(11) NOT NULL DEFAULT '0',
ADD COLUMN `epwaccountdetails` VARCHAR(200) NOT NULL DEFAULT '' AFTER `type`;

ALTER TABLE `ORDERITEMS`
CHANGE COLUMN `jobticketjdfjobticketid` `jobticketepwpartid` VARCHAR(100) NOT NULL DEFAULT '',
CHANGE COLUMN `pagesjdfjobticketid` `pagesepwpartid` VARCHAR(100) NOT NULL DEFAULT '',
CHANGE COLUMN `cover1jdfjobticketid` `cover1epwpartid` VARCHAR(100) NOT NULL DEFAULT '',
CHANGE COLUMN `cover2jdfjobticketid` `cover2epwpartid` VARCHAR(100) NOT NULL DEFAULT '',
CHANGE COLUMN `jobticketjdfqueueid` `jobticketepwsubmissionid` VARCHAR(100) NOT NULL DEFAULT '',
CHANGE COLUMN `pagesjdfqueueid` `pagesepwsubmissionid` VARCHAR(100) NOT NULL DEFAULT '',
CHANGE COLUMN `cover1jdfqueueid` `cover1epwsubmissionid` VARCHAR(100) NOT NULL DEFAULT '',
CHANGE COLUMN `cover2jdfqueueid` `cover2epwsubmissionid` VARCHAR(100) NOT NULL DEFAULT '',
CHANGE COLUMN `jobticketjdfcompletionstatus` `jobticketepwcompletionstatus` INT(11) NOT NULL DEFAULT '0',
CHANGE COLUMN `pagesjdfcompletionstatus` `pagesepwcompletionstatus` INT(11) NOT NULL DEFAULT '0',
CHANGE COLUMN `cover1jdfcompletionstatus` `cover1epwcompletionstatus` INT(11) NOT NULL DEFAULT '0',
CHANGE COLUMN `cover2jdfcompletionstatus` `cover2epwcompletionstatus` INT(11) NOT NULL DEFAULT '0',
CHANGE COLUMN `jobticketjdfstatus` `jobticketepwstatus` INT(11) NOT NULL DEFAULT '0',
CHANGE COLUMN `pagesjdfstatus` `pagesepwstatus` INT(11) NOT NULL DEFAULT '0',
CHANGE COLUMN `cover1jdfstatus` `cover1epwstatus` INT(11) NOT NULL DEFAULT '0',
CHANGE COLUMN `cover2jdfstatus` `cover2epwstatus` INT(11) NOT NULL DEFAULT '0',
ADD COLUMN `productoptions` TINYINT(3) NOT NULL DEFAULT 0 AFTER `producttype`,
DROP INDEX `jobticketjdfqueueid`,
ADD INDEX `jobticketepwsubmissionid` (`jobticketepwsubmissionid` ASC),
DROP INDEX `pagesjdfqueueid`,
ADD INDEX `pagesepwsubmissionid` (`pagesepwsubmissionid` ASC),
DROP INDEX `cover1jdfqueueid`,
ADD INDEX `cover1epwsubmissionid` (`cover1epwsubmissionid` ASC),
DROP INDEX `cover2jdfqueueid`,
ADD INDEX `cover2epwsubmissionid` (`cover2epwsubmissionid` ASC);

UPDATE `ORDERITEMS` SET `jobticketepwstatus` = 10 WHERE (`jobticketepwsubmissionid` <> "") AND (`jobticketepwstatus` = 1);

UPDATE `ORDERITEMS` SET `pagesepwstatus` = 10 WHERE (`pagesepwsubmissionid` <> "") AND (`pagesepwstatus` = 1);

UPDATE `ORDERITEMS` SET `cover1epwstatus` = 10 WHERE (`cover1epwsubmissionid` <> "") AND (`cover1epwstatus` = 1);

UPDATE `ORDERITEMS` SET `cover2epwstatus` = 10 WHERE (`cover2epwsubmissionid` <> "") AND (`cover2epwstatus` = 1);


ALTER TABLE `PRODUCTS`
ADD COLUMN `productoptions` TINYINT(3) NOT NULL DEFAULT 0 AFTER `previewthumbnails`;

UPDATE `PRODUCTS` SET `productoptions` = 1
WHERE `code` IN (SELECT pcl.productcode FROM PRODUCTCOLLECTIONLINK pcl WHERE pcl.collectiontype = 2);

TRUNCATE `CACHEDATA`;

ALTER TABLE `DATAPOLICIES`
ADD COLUMN `emailfrequency` TINYINT(3) UNSIGNED NOT NULL DEFAULT 0 AFTER `unusedassetsage`;

UPDATE `BRANDING` SET `paymentintegration` = 'PayTrail' WHERE `paymentintegration` = 'Suomen';

ALTER TABLE `BRANDING`
ADD COLUMN `imagescalingbefore` DECIMAL(5,2) NOT NULL DEFAULT 0.0 AFTER `aucacheversionframes`,
ADD COLUMN `imagescalingbeforeenabled` TINYINT(1) NOT NULL DEFAULT 0 AFTER `imagescalingbefore`,
ADD COLUMN `imagescalingafter` DECIMAL(5,2) NOT NULL DEFAULT 36.0 AFTER `imagescalingbeforeenabled`,
ADD COLUMN `imagescalingafterenabled` TINYINT(1) NOT NULL DEFAULT 1 AFTER `imagescalingafter`,
ADD COLUMN `shufflelayout` TINYINT(3) NOT NULL DEFAULT 0 AFTER `imagescalingafterenabled`,
ADD COLUMN `showshufflelayoutoptions` TINYINT(1) NOT NULL DEFAULT 0 AFTER `shufflelayout`;


ALTER TABLE `LICENSEKEYS`
ADD COLUMN `imagescalingbefore` DECIMAL(5,2) NOT NULL DEFAULT 0.0 AFTER `cacheversion`,
ADD COLUMN `imagescalingbeforeenabled` TINYINT(1) NOT NULL DEFAULT 0 AFTER `imagescalingbefore`,
ADD COLUMN `usedefaultimagescalingbefore` TINYINT(1) NOT NULL DEFAULT 1 AFTER `imagescalingbeforeenabled`,
ADD COLUMN `imagescalingafter` DECIMAL(5,2) NOT NULL DEFAULT 0 AFTER `usedefaultimagescalingbefore`,
ADD COLUMN `imagescalingafterenabled` TINYINT(1) NOT NULL DEFAULT 0 AFTER `imagescalingafter`,
ADD COLUMN `usedefaultimagescalingafter` TINYINT(1) NOT NULL DEFAULT 1 AFTER `imagescalingafterenabled`,
ADD COLUMN `shufflelayout` TINYINT(3) NOT NULL DEFAULT 0 AFTER `usedefaultimagescalingafter`,
ADD COLUMN `showshufflelayoutoptions` TINYINT(1) NOT NULL DEFAULT 0 AFTER `shufflelayout`,
ADD COLUMN `usedefaultshufflelayout` TINYINT(1) NOT NULL DEFAULT 1 AFTER `showshufflelayoutoptions`;

### 3D Preview ###

CREATE TABLE `PRODUCTONLINESYSTEMRESOURCELINK`
(
	`id` INT NOT NULL AUTO_INCREMENT,
    `resourcecode` VARCHAR(50) NOT NULL DEFAULT '',
    `productcode` VARCHAR(200) NOT NULL DEFAULT '',
    `type` INT(3) UNSIGNED NOT NULL DEFAULT 0,
	PRIMARY KEY (`id`),
    INDEX (`resourcecode`),
    INDEX (`productcode`),
	INDEX `resourceproductcode` (`resourcecode`, `productcode`)
);

ALTER TABLE `ONLINEBASKET` ADD COLUMN `sessionid` INT(11) UNSIGNED NOT NULL DEFAULT 0 AFTER `userid`;

#
# DDL END
#

SET FOREIGN_KEY_CHECKS = 1;