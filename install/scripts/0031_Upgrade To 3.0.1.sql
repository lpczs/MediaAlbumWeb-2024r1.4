#
SET FOREIGN_KEY_CHECKS = 0;

#
# DDL START
#

INSERT INTO `ACTIVITYLOG`
	(`id`, `datecreated`, `sessionid`, `orderid`, `userid`, `userlogin`, `username`, `importance`, `sectioncode`, `actioncode`, `actionnotes`, `success`)
	VALUES (0, now(), 0, 0, 0, '', (SELECT USER()), 0, 'UPGRADE', '3.0.1', 'STARTED', 1);

ALTER TABLE `ORDERITEMS` ADD INDEX orderid(`orderid`),
ADD INDEX shippeddate(`shippeddate`),
ADD INDEX outputtimestamp(`outputtimestamp`);

ALTER TABLE `ORDERHEADER` ADD `orderready` TINYINT( 1 ) NOT NULL DEFAULT '0' AFTER `archivedate`;

UPDATE `SYSTEMCONFIG` SET `webversiondate` = '2011-10-10';
UPDATE `SYSTEMCONFIG` SET `webversionnumber` = '3.0.1';
UPDATE `SYSTEMCONFIG` SET `webversionstring`= '3.0.1';

INSERT INTO `ACTIVITYLOG`
	(`id`, `datecreated`, `sessionid`, `orderid`, `userid`, `userlogin`, `username`, `importance`, `sectioncode`, `actioncode`, `actionnotes`, `success`)
	VALUES (0, now(), 0, 0, 0, '', (SELECT USER()), 0, 'UPGRADE', '3.0.1', 'FINISHED', 1);

#
# DDL END
#

SET FOREIGN_KEY_CHECKS = 1;
