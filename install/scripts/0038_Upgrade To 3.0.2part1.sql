#
SET FOREIGN_KEY_CHECKS = 0;

#
# DDL START
#

INSERT INTO `ACTIVITYLOG`
	(`id`, `datecreated`, `sessionid`, `orderid`, `userid`, `userlogin`, `username`, `importance`, `sectioncode`, `actioncode`, `actionnotes`, `success`)
	VALUES (0, now(), 0, 0, 0, '', (SELECT USER()), 0, 'UPGRADE', '3.0.2a1', 'STARTED', 1);

UPDATE PRICELINK SET `sortorder` = `id` WHERE (`sortorder` LIKE "%-%") OR (`sortorder` = '');
ALTER TABLE `pricelink` MODIFY COLUMN `sortorder` INT;

UPDATE `SYSTEMCONFIG` SET `webversiondate` = '2011-11-01';
UPDATE `SYSTEMCONFIG` SET `webversionnumber` = '3.0.2.1';
UPDATE `SYSTEMCONFIG` SET `webversionstring`= '3.0.2a1';

INSERT INTO `ACTIVITYLOG`
	(`id`, `datecreated`, `sessionid`, `orderid`, `userid`, `userlogin`, `username`, `importance`, `sectioncode`, `actioncode`, `actionnotes`, `success`)
	VALUES (0, now(), 0, 0, 0, '', (SELECT USER()), 0, 'UPGRADE', '3.0.2a1', 'FINISHED', 1);

#
# DDL END
#

SET FOREIGN_KEY_CHECKS = 1;
