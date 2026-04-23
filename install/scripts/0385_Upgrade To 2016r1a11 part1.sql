#
SET FOREIGN_KEY_CHECKS = 0;

#
# DDL START
#

INSERT INTO `ACTIVITYLOG`
	(`id`, `datecreated`, `sessionid`, `orderid`, `userid`, `userlogin`, `username`, `importance`, `sectioncode`, `actioncode`, `actionnotes`, `success`)
	VALUES (0, now(), 0, 0, 0, '', (SELECT USER()), 0, 'UPGRADE', '2016r1a11', 'STARTED', 1);

UPDATE `SYSTEMCONFIG` SET `webversiondate` = '2015-11-05';
UPDATE `SYSTEMCONFIG` SET `webversionnumber` = '2016.1.0.11';
UPDATE `SYSTEMCONFIG` SET `webversionstring`= '2016r1a11';

ALTER TABLE `ORDERITEMCOMPONENTS` ADD COLUMN `externalassetid` VARCHAR(100) NOT NULL DEFAULT '' AFTER `parentcomponentid`;

UPDATE ORDERITEMCOMPONENTS SET `externalassetid` = `skucode` WHERE `componentpath` = '$SINGLEPRINT\\';
UPDATE ORDERITEMCOMPONENTS SET `skucode` = '' WHERE `componentpath` = '$SINGLEPRINT\\';

INSERT INTO `ACTIVITYLOG`
(`id`, `datecreated`, `sessionid`, `orderid`, `userid`, `userlogin`, `username`, `importance`, `sectioncode`, `actioncode`, `actionnotes`, `success`)
VALUES (0, now(), 0, 0, 0, '', (SELECT USER()), 0, 'UPGRADE', '2016r1a11', 'FINISHED', 1);


#
# DDL END
#

SET FOREIGN_KEY_CHECKS = 1;
