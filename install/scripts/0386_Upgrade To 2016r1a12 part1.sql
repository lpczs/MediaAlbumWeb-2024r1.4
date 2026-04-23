#
SET FOREIGN_KEY_CHECKS = 0;

#
# DDL START
#

INSERT INTO `ACTIVITYLOG`
	(`id`, `datecreated`, `sessionid`, `orderid`, `userid`, `userlogin`, `username`, `importance`, `sectioncode`, `actioncode`, `actionnotes`, `success`)
	VALUES (0, now(), 0, 0, 0, '', (SELECT USER()), 0, 'UPGRADE', '2016r1a12', 'STARTED', 1);

UPDATE `SYSTEMCONFIG` SET `webversiondate` = '2015-11-11';
UPDATE `SYSTEMCONFIG` SET `webversionnumber` = '2016.1.0.12';
UPDATE `SYSTEMCONFIG` SET `webversionstring`= '2016r1a12';

INSERT INTO `TASKS` (`datecreated`, `taskcode`, `taskname`, `intervaltype`, `intervalvalue`, `lastruntime`, `nextruntime`, `statuscode`, `statusmessage`, `runstatus`, `maxruncount`, `internal`, `scriptfilename`, `deleteexpiredinterval`, `active`) VALUES
	(now(),'TAOPIX_ONLINEPURGETASK','en Online Purge Task',2,'03:00','0000-00-00 00:00:00','0000-00-00 00:00:00',0,'',0,10,1,'onlinePurgeTask.php',10,0);


CREATE TABLE `DATAPOLICIES` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `datecreated` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
    `code` varchar(50) NOT NULL DEFAULT '',
    `name` varchar(50) NOT NULL DEFAULT '',
    `guestprojects` tinyint(1) NOT NULL DEFAULT 0,
    `guestage` int(11) NOT NULL DEFAULT 3,
    `guestdays` int(11) NOT NULL DEFAULT 0,
    `unsavedprojects` tinyint(1) NOT NULL DEFAULT 0,
    `unsavedage` int(11) NOT NULL DEFAULT 7,
    `unsaveddays` int(11) NOT NULL DEFAULT 14,
    `unsavedemail` tinyint(1) NOT NULL DEFAULT 0,
    `notorderedprojects` tinyint(1) NOT NULL DEFAULT 0,
    `notorderedage` int(11) NOT NULL DEFAULT 90,
    `notordereddays` int(11) NOT NULL DEFAULT 60,
    `notorderedemail` tinyint(1) NOT NULL DEFAULT 0,
    `unusedassets` tinyint(1) NOT NULL DEFAULT 0,
    `unusedassetsage` int(11) NOT NULL DEFAULT 90,
    PRIMARY KEY (`id`)
)  ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `BRANDING`
ADD COLUMN `onlinedataretentionpolicy` INT(11) NOT NULL DEFAULT '0' AFTER `onlinedesignersigninregisterpromptdelay`;


INSERT INTO `ACTIVITYLOG`
(`id`, `datecreated`, `sessionid`, `orderid`, `userid`, `userlogin`, `username`, `importance`, `sectioncode`, `actioncode`, `actionnotes`, `success`)
VALUES (0, now(), 0, 0, 0, '', (SELECT USER()), 0, 'UPGRADE', '2016r1a12', 'FINISHED', 1);

#
# DDL END
#

SET FOREIGN_KEY_CHECKS = 1;