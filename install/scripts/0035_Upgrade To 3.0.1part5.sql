#
SET FOREIGN_KEY_CHECKS = 0;

#
# DDL START
#

INSERT INTO `ACTIVITYLOG`
	(`id`, `datecreated`, `sessionid`, `orderid`, `userid`, `userlogin`, `username`, `importance`, `sectioncode`, `actioncode`, `actionnotes`, `success`)
	VALUES (0, now(), 0, 0, 0, '', (SELECT USER()), 0, 'UPGRADE', '3.0.1', 'STARTED', 1);

ALTER TABLE `SHAREDITEMS` MODIFY COLUMN `action` VARCHAR(50) NOT NULL,
 MODIFY COLUMN `method` VARCHAR(50) NOT NULL;

UPDATE `SHAREDITEMS` SET `action` = "CUSTOMER NOTIFICATION" WHERE `action` = "CUSTOMER NOTIFICATIO";

UPDATE `SYSTEMCONFIG` SET `webversiondate` = '2011-10-27';
UPDATE `SYSTEMCONFIG` SET `webversionnumber` = '3.0.1';
UPDATE `SYSTEMCONFIG` SET `webversionstring`= '3.0.1';

INSERT INTO `ACTIVITYLOG`
	(`id`, `datecreated`, `sessionid`, `orderid`, `userid`, `userlogin`, `username`, `importance`, `sectioncode`, `actioncode`, `actionnotes`, `success`)
	VALUES (0, now(), 0, 0, 0, '', (SELECT USER()), 0, 'UPGRADE', '3.0.1', 'FINISHED', 1);

#
# DDL END
#

SET FOREIGN_KEY_CHECKS = 1;
