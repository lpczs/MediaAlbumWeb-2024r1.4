#
SET FOREIGN_KEY_CHECKS = 0;

#
# DDL START
#

INSERT INTO `ACTIVITYLOG`
	(`id`, `datecreated`, `sessionid`, `orderid`, `userid`, `userlogin`, `username`, `importance`, `sectioncode`, `actioncode`, `actionnotes`, `success`)
	VALUES (0, now(), 0, 0, 0, '', (SELECT USER()), 0, 'UPGRADE', '4.0.0a1 part1', 'STARTED', 1);

UPDATE `SYSTEMCONFIG` SET `webversiondate` = '2013-04-05';
UPDATE `SYSTEMCONFIG` SET `webversionnumber` = '4.0.0.1';
UPDATE `SYSTEMCONFIG` SET `webversionstring`= '4.0.0a1';

ALTER TABLE `APPLICATIONFILES` ADD COLUMN `onlineactive` INTEGER NOT NULL DEFAULT 0 AFTER `active`;

INSERT INTO `TASKS` (`datecreated`, `taskcode`, `taskname`, `intervaltype`, `intervalvalue`, `lastruntime`, `nextruntime`, `statuscode`, `statusmessage`, `runstatus`, `maxruncount`, `internal`, `scriptfilename`, `deleteexpiredinterval`, `active`) VALUES
	(now(), 'TAOPIX_ONLINEASSETPUSH','en Online Asset Push', 1, '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0,'', 0, 10, 1, 'onlineAssetPush.php', 10, 0);
	
ALTER TABLE `APPLICATIONFILES` ADD COLUMN `datemodifiedonline` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' AFTER `datemodified`;

INSERT INTO `ACTIVITYLOG`
	(`id`, `datecreated`, `sessionid`, `orderid`, `userid`, `userlogin`, `username`, `importance`, `sectioncode`, `actioncode`, `actionnotes`, `success`)
	VALUES (0, now(), 0, 0, 0, '', (SELECT USER()), 0, 'UPGRADE', '4.0.0a1 part1', 'FINISHED', 1);

#
# DDL END
#

SET FOREIGN_KEY_CHECKS = 1;
