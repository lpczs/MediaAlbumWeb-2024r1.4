#
SET FOREIGN_KEY_CHECKS = 0;

#
# DDL START
#

INSERT INTO `ACTIVITYLOG`
	(`id`, `datecreated`, `sessionid`, `orderid`, `userid`, `userlogin`, `username`, `importance`, `sectioncode`, `actioncode`, `actionnotes`, `success`)
	VALUES (0, now(), 0, 0, 0, '', (SELECT USER()), 0, 'UPGRADE', '4.1.0a2 part1', 'STARTED', 1);

UPDATE `SYSTEMCONFIG` SET `webversiondate` = '2013-10-14';
UPDATE `SYSTEMCONFIG` SET `webversionnumber` = '4.1.0.2';
UPDATE `SYSTEMCONFIG` SET `webversionstring`= '4.1.0a2';

UPDATE `USERS` AS `u` LEFT JOIN `LICENSEKEYS` AS `l` ON `l`.`groupcode` = `u`.`groupcode` SET `u`.`webbrandcode` = `l`.`webbrandcode` WHERE `u`.`customer` = 1;

ALTER TABLE `USERS` DROP INDEX `login`,
 ADD INDEX login(`login`),
 ADD UNIQUE INDEX webbrandcodelogin(`webbrandcode`, `login`);

INSERT INTO `ACTIVITYLOG`
	(`id`, `datecreated`, `sessionid`, `orderid`, `userid`, `userlogin`, `username`, `importance`, `sectioncode`, `actioncode`, `actionnotes`, `success`)
	VALUES (0, now(), 0, 0, 0, '', (SELECT USER()), 0, 'UPGRADE', '4.1.0a2 part1', 'FINISHED', 1);

#
# DDL END
#

SET FOREIGN_KEY_CHECKS = 1;
