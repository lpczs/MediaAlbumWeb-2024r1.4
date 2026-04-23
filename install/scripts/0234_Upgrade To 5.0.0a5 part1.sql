#
SET FOREIGN_KEY_CHECKS = 0;

#
# DDL START
#

INSERT INTO `ACTIVITYLOG`
	(`id`, `datecreated`, `sessionid`, `orderid`, `userid`, `userlogin`, `username`, `importance`, `sectioncode`, `actioncode`, `actionnotes`, `success`)
	VALUES (0, now(), 0, 0, 0, '', (SELECT USER()), 0, 'UPGRADE', '5.0.0a5 part1', 'STARTED', 1);

UPDATE `SYSTEMCONFIG` SET `webversiondate` = '2014-06-11';
UPDATE `SYSTEMCONFIG` SET `webversionnumber` = '5.0.0.5';
UPDATE `SYSTEMCONFIG` SET `webversionstring`= '5.0.0a5';

ALTER TABLE `VOUCHERS`
ADD COLUMN `applicationmethod` INT(11) NOT NULL DEFAULT '0' AFTER `discountvalue`,
ADD COLUMN `maxqtytoapplydiscountto` INT(11) NOT NULL DEFAULT 9999 AFTER `applicationmethod`;

ALTER TABLE `ORDERHEADER`
ADD COLUMN `voucherapplicationmethod` INT(11) NOT NULL DEFAULT '0' AFTER `voucherdiscountvalue`,
ADD COLUMN `vouchermaxqtytoapplydiscountto` INT(11) NOT NULL DEFAULT 9999 AFTER `voucherapplicationmethod`;

INSERT INTO `ACTIVITYLOG`
(`id`, `datecreated`, `sessionid`, `orderid`, `userid`, `userlogin`, `username`, `importance`, `sectioncode`, `actioncode`, `actionnotes`, `success`)
VALUES (0, now(), 0, 0, 0, '', (SELECT USER()), 0, 'UPGRADE', '5.0.0a5 part1', 'FINISHED', 1);

#
# DDL END
#

SET FOREIGN_KEY_CHECKS = 1;
