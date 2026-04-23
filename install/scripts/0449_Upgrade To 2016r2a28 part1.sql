#
SET FOREIGN_KEY_CHECKS = 0;

#
# DDL START
#

INSERT INTO `ACTIVITYLOG`
	(`id`, `datecreated`, `sessionid`, `orderid`, `userid`, `userlogin`, `username`, `importance`, `sectioncode`, `actioncode`, `actionnotes`, `success`)
	VALUES (0, now(), 0, 0, 0, '', (SELECT USER()), 0, 'UPGRADE', '2016r2a28', 'STARTED', 1);

UPDATE `SYSTEMCONFIG` SET `webversiondate` = '2016-05-05';
UPDATE `SYSTEMCONFIG` SET `webversionnumber` = '2016.2.0.28';
UPDATE `SYSTEMCONFIG` SET `webversionstring`= '2016r2a28';

INSERT INTO `TASKS` (`datecreated`, `taskcode`, `taskname`, `intervaltype`, `intervalvalue`, `lastruntime`, `nextruntime`, `statuscode`, `statusmessage`, `runstatus`, `maxruncount`, `internal`, `scriptfilename`, `deleteexpiredinterval`, `active`) VALUES
	(now(), 'TAOPIX_CCNOTIFICATION','en Control Centre Notifications', 1, '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0,'', 0, 10, 1, 'ccNotifications.php', 10, 0);

INSERT INTO `ACTIVITYLOG`
	(`id`, `datecreated`, `sessionid`, `orderid`, `userid`, `userlogin`, `username`, `importance`, `sectioncode`, `actioncode`, `actionnotes`, `success`)
	VALUES (0, now(), 0, 0, 0, '', (SELECT USER()), 0, 'UPGRADE', '2016r2a28', 'FINISHED', 1);

#
# DDL END
#

SET FOREIGN_KEY_CHECKS = 1;