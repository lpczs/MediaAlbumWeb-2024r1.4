#
SET FOREIGN_KEY_CHECKS = 0;

#
# DDL START
#

INSERT INTO `ACTIVITYLOG`
	(`id`, `datecreated`, `sessionid`, `orderid`, `userid`, `userlogin`, `username`, `importance`, `sectioncode`, `actioncode`, `actionnotes`, `success`)
	VALUES (0, now(), 0, 0, 0, '', (SELECT USER()), 0, 'UPGRADE', '4.0.0a2 part1', 'STARTED', 1);

UPDATE `SYSTEMCONFIG` SET `webversiondate` = '2013-07-02';
UPDATE `SYSTEMCONFIG` SET `webversionnumber` = '4.0.0.2';
UPDATE `SYSTEMCONFIG` SET `webversionstring`= '4.0.0a2';

ALTER TABLE `ORDERITEMS` ADD COLUMN `source` TINYINT(1) NOT NULL DEFAULT 0 AFTER `userid`;

INSERT INTO `TASKS` (`datecreated`, `taskcode`, `taskname`, `intervaltype`, `intervalvalue`, `lastruntime`, `nextruntime`, `statuscode`, `statusmessage`, `runstatus`, `maxruncount`, `internal`, `scriptfilename`, `deleteexpiredinterval`, `active`) VALUES
	(now(),'TAOPIX_ONLINEORDERCREATION','en Online Order Creation',1,'1','0000-00-00 00:00:00','0000-00-00 00:00:00',0,'',0,10,1,'onlineOrderCreation.php',10,1);

ALTER TABLE `LICENSEKEYS` ADD `cacheversion` VARCHAR(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' AFTER `onlineactive`;

CREATE TABLE IF NOT EXISTS `CACHEDATA`(
  `datacachekey` varchar(50) NOT NULL,
  `datecreated` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `productcode` varchar(50) NOT NULL,
  `productcollectioncode` varchar(50) NOT NULL,
  `groupcode` varchar(50) NOT NULL,
  `companycode` varchar(50) NOT NULL,
  `cachedata` blob NOT NULL,
  `cacheversion` varchar(30) NOT NULL DEFAULT '',
  PRIMARY KEY (`datacachekey`),
  KEY `datacachekey` (`datacachekey`)
) ENGINE=innoDB DEFAULT CHARSET=utf8;

INSERT INTO `ACTIVITYLOG`
	(`id`, `datecreated`, `sessionid`, `orderid`, `userid`, `userlogin`, `username`, `importance`, `sectioncode`, `actioncode`, `actionnotes`, `success`)
	VALUES (0, now(), 0, 0, 0, '', (SELECT USER()), 0, 'UPGRADE', '4.0.0a2 part1', 'FINISHED', 1);

#
# DDL END
#

SET FOREIGN_KEY_CHECKS = 1;
