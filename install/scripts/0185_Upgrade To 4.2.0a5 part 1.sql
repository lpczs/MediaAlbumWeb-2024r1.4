#
SET FOREIGN_KEY_CHECKS = 0;

#
# DDL START
#

INSERT INTO `ACTIVITYLOG`
	(`id`, `datecreated`, `sessionid`, `orderid`, `userid`, `userlogin`, `username`, `importance`, `sectioncode`, `actioncode`, `actionnotes`, `success`)
	VALUES (0, now(), 0, 0, 0, '', (SELECT USER()), 0, 'UPGRADE', '4.2.0a5 part1', 'STARTED', 1);

UPDATE `SYSTEMCONFIG` SET `webversiondate` = '2013-12-5';
UPDATE `SYSTEMCONFIG` SET `webversionnumber` = '4.2.0.5';
UPDATE `SYSTEMCONFIG` SET `webversionstring`= '4.2.0a5';

ALTER TABLE `SHAREDITEMS` CHANGE COLUMN `orderitemid` `orderitemid` INT(11) NOT NULL DEFAULT '0', CHANGE COLUMN `orderid` `orderid` INT(11) NOT NULL DEFAULT '0';

ALTER TABLE `SHAREDITEMS` DROP INDEX `codes`;

ALTER TABLE `SHAREDITEMS` ADD INDEX `uniqueid` (`uniqueid` ASC);

ALTER TABLE `SESSIONDATA` DROP COLUMN `onlinesession`;


INSERT INTO `ACTIVITYLOG`
	(`id`, `datecreated`, `sessionid`, `orderid`, `userid`, `userlogin`, `username`, `importance`, `sectioncode`, `actioncode`, `actionnotes`, `success`)
	VALUES (0, now(), 0, 0, 0, '', (SELECT USER()), 0, 'UPGRADE', '4.2.0a5 part1', 'FINISHED', 1);

#
# DDL END
#

SET FOREIGN_KEY_CHECKS = 1;
