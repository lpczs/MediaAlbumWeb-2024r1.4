#
SET FOREIGN_KEY_CHECKS = 0;

#
# DDL START
#

INSERT INTO `ACTIVITYLOG`
	(`id`, `datecreated`, `sessionid`, `orderid`, `userid`, `userlogin`, `username`, `importance`, `sectioncode`, `actioncode`, `actionnotes`, `success`)
	VALUES (0, now(), 0, 0, 0, '', (SELECT USER()), 0, 'UPGRADE', '3.2.0a1part5', 'STARTED', 1);

ALTER TABLE `SHIPPINGRATES` MODIFY COLUMN `rate` VARCHAR(4096) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;


UPDATE `SYSTEMCONFIG` SET `webversiondate` = '2012-02-14';
UPDATE `SYSTEMCONFIG` SET `webversionnumber` = '3.2.0.1';
UPDATE `SYSTEMCONFIG` SET `webversionstring`= '3.2.0a1';

INSERT INTO `ACTIVITYLOG`
	(`id`, `datecreated`, `sessionid`, `orderid`, `userid`, `userlogin`, `username`, `importance`, `sectioncode`, `actioncode`, `actionnotes`, `success`)
	VALUES (0, now(), 0, 0, 0, '', (SELECT USER()), 0, 'UPGRADE', '3.2.0a1part5', 'FINISHED', 1);

#
# DDL END
#

SET FOREIGN_KEY_CHECKS = 1;
