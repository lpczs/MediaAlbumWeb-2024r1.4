#
SET FOREIGN_KEY_CHECKS = 0;

#
# DDL START
#

UPDATE `SYSTEMCONFIG` SET `webversiondate` = '2018-08-20';
UPDATE `SYSTEMCONFIG` SET `webversionnumber` = '2018.4.0';
UPDATE `SYSTEMCONFIG` SET `webversionstring`= '2018r4';


ALTER TABLE `SESSIONDATA`
  ADD `csrftoken` CHAR(65) DEFAULT NULL AFTER `basketref`;


INSERT INTO `TRIGGERS`
  (`datecreated` ,`eventcode` ,`language` ,`exportformat` ,`includepaymentdata` ,`beautifiedxml` ,`subfolderformat` ,`filenameformat` ,`active`)
VALUES
  (now(),'ORDERPAID' , 'Default' , 'XML' , 0 , 1 , '' , '' , 0);


ALTER TABLE `CONSTANTS`
  ADD COLUMN `maxloginattempts` TINYINT NOT NULL DEFAULT 10 AFTER `defaultcreditlimit`,
  ADD COLUMN `accountlockouttime` INT NOT NULL DEFAULT 15 AFTER `maxloginattempts`,
  ADD COLUMN `maxiploginattempts` TINYINT UNSIGNED NOT NULL DEFAULT 15 AFTER `accountlockouttime`,
  ADD COLUMN `maxiploginattemptsminutes` INT UNSIGNED NOT NULL DEFAULT 15 AFTER `maxiploginattempts`;

ALTER TABLE `USERS`
  ADD COLUMN `nextvalidlogindate` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' AFTER `lastlogindate`,
  ADD COLUMN `loginattemptcount` INT(11) NOT NULL DEFAULT 0 AFTER `nextvalidlogindate`;

CREATE TABLE  `USERSBLOCKEDIPADDRESSLIST` (
  `id` int(11) UNSIGNED NOT NULL auto_increment,
  `datecreated` DATETIME NOT NULL default NOW(),
  `datelastupdate` DATETIME NOT NULL default NOW() on update NOW(),
  `ipaddressraw` VARCHAR(45) NOT NULL default '',
  `ipaddress` VARBINARY(16) NOT NULL default '',
  `nextvalidlogindate` DATETIME NOT NULL default '0000-00-00 00:00:00',
  `blockcount` int(11) UNSIGNED NOT NULL default 0,
PRIMARY KEY  (`id`),
KEY `ipaddress` (`ipaddress`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `ACTIVITYLOG`
  CHANGE COLUMN `ipaddress` `ipaddress` VARCHAR(45) NOT NULL DEFAULT '' ,
  ADD COLUMN `remoteipaddress` VARBINARY(16) NOT NULL DEFAULT '' AFTER `ipaddress`;


ALTER TABLE `DATAPOLICIES`
  ADD COLUMN `unsavedemailfrequency` SMALLINT(5) UNSIGNED NOT NULL DEFAULT 1 AFTER `unsavedemail`,
  ADD COLUMN `notorderedemailfrequency` SMALLINT(5) UNSIGNED NOT NULL DEFAULT 1 AFTER `notorderedemail`,
  ADD COLUMN `orderedemailfrequency` SMALLINT(5) UNSIGNED NOT NULL DEFAULT 1 AFTER `orderedemail`;

UPDATE `DATAPOLICIES` SET
  `unsavedemailfrequency` = GREATEST(1, `emailfrequency`),
  `notorderedemailfrequency` = GREATEST(1, `emailfrequency`),
  `orderedemailfrequency` = GREATEST(1, `emailfrequency`),
  `unsavedemail` = IF(`emailfrequency` = 0, 0, `unsavedemail`),
  `notorderedemail` = IF(`emailfrequency` = 0, 0, `notorderedemail`),
  `orderedemail` = IF(`emailfrequency` = 0, 0, `orderedemail`);

ALTER TABLE `DATAPOLICIES`
  DROP COLUMN `emailfrequency`;

#
# DDL END
#

SET FOREIGN_KEY_CHECKS = 1;

