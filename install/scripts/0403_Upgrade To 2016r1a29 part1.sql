#
SET FOREIGN_KEY_CHECKS = 0;

#
# DDL START
#

INSERT INTO `ACTIVITYLOG`
	(`id`, `datecreated`, `sessionid`, `orderid`, `userid`, `userlogin`, `username`, `importance`, `sectioncode`, `actioncode`, `actionnotes`, `success`)
	VALUES (0, now(), 0, 0, 0, '', (SELECT USER()), 0, 'UPGRADE', '2016r1a29', 'STARTED', 1);

UPDATE `SYSTEMCONFIG` SET `webversiondate` = '2016-01-13';
UPDATE `SYSTEMCONFIG` SET `webversionnumber` = '2016.1.0.29';
UPDATE `SYSTEMCONFIG` SET `webversionstring`= '2016r1a29';



ALTER TABLE `BRANDING`
ADD COLUMN `redactionmode` TINYINT NOT NULL DEFAULT 0 AFTER `productcategoryassetversion`,
ADD COLUMN `automaticredactionenabled` TINYINT NOT NULL DEFAULT 0 AFTER `redactionmode`,
ADD COLUMN `automaticredactiondays` INT NOT NULL DEFAULT 365 AFTER `automaticredactionenabled`,
ADD COLUMN `redactionnotificationdays` INT NOT NULL DEFAULT 7 AFTER `automaticredactiondays`;


ALTER TABLE `USERS`
ADD COLUMN `lastlogindate` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' AFTER `ipaccesslist`,
ADD COLUMN `lastloginip` VARCHAR(46) NOT NULL DEFAULT '' AFTER `lastlogindate`,
ADD COLUMN `redactionprogress` INT(11) NOT NULL DEFAULT 0 AFTER `lastloginip`,
ADD COLUMN `redactionstate` INT(11) NOT NULL DEFAULT 0 AFTER `redactionprogress`,
ADD COLUMN `redactionreason` VARCHAR(200) NOT NULL DEFAULT '' AFTER `redactionstate`,
ADD COLUMN `redactiondate` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' AFTER `redactionreason`,
ADD COLUMN `protectfromredaction` TINYINT NOT NULL DEFAULT 0 AFTER `redactiondate`;

UPDATE `USERS` SET `protectfromredaction` = 1 WHERE `customer` = 0;


CREATE TABLE `PRODUCTIONEVENTS` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `datecreated` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `datelastmodified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `companycode` varchar(50) NOT NULL DEFAULT '',
  `owner` varchar(50) NOT NULL DEFAULT '',
  `userid` int(11) NOT NULL DEFAULT '0',
  `orderitemid` int(11) NOT NULL DEFAULT '0',
  `actioncode` varchar(50) NOT NULL DEFAULT '',
  `message` varchar(200) NOT NULL DEFAULT '',
  `status` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `userid` (`userid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


INSERT INTO `TASKS` (`taskcode`, `taskname`, `intervaltype`, `intervalvalue`, `maxruncount`, `internal`, `scriptfilename`, `deleteexpiredinterval`)
VALUES ('TAOPIX_DATADELETION', 'en Personal Data Deletion', 2, '03:00', 10, 1, 'dataDeletionTask.php', 10);



INSERT INTO `ACTIVITYLOG`
(`id`, `datecreated`, `sessionid`, `orderid`, `userid`, `userlogin`, `username`, `importance`, `sectioncode`, `actioncode`, `actionnotes`, `success`)
VALUES (0, now(), 0, 0, 0, '', (SELECT USER()), 0, 'UPGRADE', '2016r1a29', 'FINISHED', 1);


#
# DDL END
#

SET FOREIGN_KEY_CHECKS = 1;