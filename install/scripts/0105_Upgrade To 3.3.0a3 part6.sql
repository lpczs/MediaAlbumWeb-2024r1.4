#
SET FOREIGN_KEY_CHECKS = 0;

#
# DDL START
#

INSERT INTO `ACTIVITYLOG`
	(`id`, `datecreated`, `sessionid`, `orderid`, `userid`, `userlogin`, `username`, `importance`, `sectioncode`, `actioncode`, `actionnotes`, `success`)
	VALUES (0, now(), 0, 0, 0, '', (SELECT USER()), 0, 'UPGRADE', '3.3.0a3 part6', 'STARTED', 1);

UPDATE `SYSTEMCONFIG` SET `webversiondate` = '2012-09-03';
UPDATE `SYSTEMCONFIG` SET `webversionnumber` = '3.3.0.3';
UPDATE `SYSTEMCONFIG` SET `webversionstring`= '3.3.0a3';

ALTER TABLE `ORDERHEADER` ADD `orderfootertotaltax` DECIMAL(10, 2) NOT NULL DEFAULT '0.00' AFTER `orderfootertotal`;

ALTER TABLE `ORDERHEADER` ADD `orderfootertotalwithtax` DECIMAL(10, 2) NOT NULL DEFAULT '0.00' AFTER `ordertotalitemsellwithtax`;
ALTER TABLE `ORDERHEADER` ADD `orderfootertotalnotax` DECIMAL(10, 2) NOT NULL DEFAULT '0.00' AFTER `orderfootertotalwithtax`;
ALTER TABLE `ORDERHEADER` ADD `orderfootertaxratesequal` INT(1) NOT NULL DEFAULT '0' AFTER `orderfootertotalnotax`;

INSERT INTO `ACTIVITYLOG`
	(`id`, `datecreated`, `sessionid`, `orderid`, `userid`, `userlogin`, `username`, `importance`, `sectioncode`, `actioncode`, `actionnotes`, `success`)
	VALUES (0, now(), 0, 0, 0, '', (SELECT USER()), 0, 'UPGRADE', '3.3.0a3 part6', 'FINISHED', 1);

#
# DDL END
#

SET FOREIGN_KEY_CHECKS = 1;
