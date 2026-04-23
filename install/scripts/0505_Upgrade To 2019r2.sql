#
SET FOREIGN_KEY_CHECKS = 0;

#
# DDL START
#

UPDATE `SYSTEMCONFIG` SET `webversiondate` = '2019-09-09';
UPDATE `SYSTEMCONFIG` SET `webversionnumber` = '2019.2.0';
UPDATE `SYSTEMCONFIG` SET `webversionstring`= '2019r2';


ALTER TABLE `METADATA` RENAME TO `METADATA_ORIG`;

CREATE TABLE `METADATA` (
`id` INT(11) NOT NULL AUTO_INCREMENT,
`datecreated` DATETIME NOT NULL,
`orderid` INT(11) NOT NULL,
`orderitemid` INT(11) NOT NULL,
`orderitemcomponentid` INT(11) NOT NULL,
`userid` INT(11) NOT NULL,
`section` VARCHAR(10) NOT NULL,
PRIMARY KEY (`id`),
KEY (`orderid`),
KEY (`orderitemid`),
KEY (`orderitemcomponentid`),
KEY (`userid`))
ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `METADATA` (`id`, `datecreated`, `orderid`, `orderitemid`, `orderitemcomponentid`, `userid`, `section`)
(SELECT `id`, `datecreated`, `orderid`, `orderitemid`, `orderitemcomponentid`, `userid`, `section` FROM `METADATA_ORIG`);

CREATE TABLE `METADATAVALUES` (
`id` BIGINT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`metadataid` INT(11) NOT NULL,
`keywordref` INT(11) NOT NULL,
`value` VARCHAR(32768) NOT NULL,
PRIMARY KEY (`id`),
KEY (`metadataid`),
KEY (`keywordref`))
ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `KEYWORDS` ADD KEY (`ref`);


ALTER TABLE `BRANDING` 
ADD COLUMN `googletagmanagercccode` VARCHAR(20) NOT NULL AFTER `googletagmanageronlinecode`;


ALTER TABLE `ORDERITEMS` 
ADD COLUMN `projectlsdata` VARCHAR(200) NOT NULL DEFAULT '' AFTER `projectbuildduration`;

ALTER TABLE `AUTHENTICATIONDATASTORE` 
ADD COLUMN `ref` int(11) NOT NULL DEFAULT '0' AFTER `reason`;

ALTER TABLE `CONSTANTS` 
ADD COLUMN `minpasswordscore` tinyint(3) UNSIGNED NOT NULL DEFAULT 0;


CREATE TABLE `PROJECTORDERDATACACHE` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`datecreated` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
`projectref` varchar(200) NOT NULL DEFAULT '',
`projectdata` mediumblob NOT NULL,
`projectdatalength` int(11) NOT NULL DEFAULT '0',
`source` tinyint(1) NOT NULL DEFAULT '0',
PRIMARY KEY (`id`),
UNIQUE KEY `projectref` (`projectref`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

#
# DDL END
#

SET FOREIGN_KEY_CHECKS = 1;

