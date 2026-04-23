#
SET FOREIGN_KEY_CHECKS = 0;

#
# DDL START
#

INSERT INTO `ACTIVITYLOG`
	(`id`, `datecreated`, `sessionid`, `orderid`, `userid`, `userlogin`, `username`, `importance`, `sectioncode`, `actioncode`, `actionnotes`, `success`)
	VALUES (0, now(), 0, 0, 0, '', (SELECT USER()), 0, 'UPGRADE', '3.2.1a1part4', 'STARTED', 1);

UPDATE `SYSTEMCONFIG` SET `webversiondate` = '2012-06-11';
UPDATE `SYSTEMCONFIG` SET `webversionnumber` = '3.2.1.1';
UPDATE `SYSTEMCONFIG` SET `webversionstring`= '3.2.1a1';

ALTER TABLE `COMPONENTS` MODIFY COLUMN `unitcost` DECIMAL(10,4) NOT NULL DEFAULT '0.0000';

ALTER TABLE `ORDERITEMCOMPONENTS` MODIFY COLUMN `componentunitcost` DECIMAL(10,4) NOT NULL DEFAULT '0.0000';

INSERT INTO `ACTIVITYLOG`
	(`id`, `datecreated`, `sessionid`, `orderid`, `userid`, `userlogin`, `username`, `importance`, `sectioncode`, `actioncode`, `actionnotes`, `success`)
	VALUES (0, now(), 0, 0, 0, '', (SELECT USER()), 0, 'UPGRADE', '3.2.1a1part4', 'FINISHED', 1);

#
# DDL END
#

SET FOREIGN_KEY_CHECKS = 1;
