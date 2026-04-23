#
SET FOREIGN_KEY_CHECKS = 0;

#
# DDL START
#

INSERT INTO `ACTIVITYLOG`
	(`id`, `datecreated`, `sessionid`, `orderid`, `userid`, `userlogin`, `username`, `importance`, `sectioncode`, `actioncode`, `actionnotes`, `success`)
	VALUES (0, now(), 0, 0, 0, '', (SELECT USER()), 0, 'UPGRADE', '3.0.1', 'STARTED', 1);


UPDATE `ORDERHEADER` SET orderready = 1;

UPDATE ORDERITEMS oi set `oi`.`productdefaultpagecount` = (SELECT `defaultpagecount` FROM PRODUCTS p WHERE `oi`.`productcode` = `p`.`code`) 
WHERE `oi`.`productdefaultpagecount` = 0;

ALTER TABLE `SHAREDITEMS` ADD COLUMN `password` VARCHAR(70) NOT NULL DEFAULT '' AFTER `recipient`;

UPDATE BRANDING SET `previewexpiresdays` = 30;

ALTER TABLE `OUTPUTFORMATS` ADD COLUMN `cover1atfront` TINYINT(1) NOT NULL DEFAULT 0 AFTER `cover1separatefile`;
ALTER TABLE `ORDERITEMS` ADD COLUMN `shareid` INT NOT NULL DEFAULT 0 AFTER `origorderitemid`;
ALTER TABLE `ORDERITEMS` ADD COLUMN `uploadgroupcode` VARCHAR(50) NOT NULL AFTER `userid`;

UPDATE ORDERITEMS oi LEFT JOIN ORDERHEADER oh ON oh.id = oi.uploadorderid SET `uploadgroupcode` = IFNULL(oh.groupcode, '');

UPDATE `SYSTEMCONFIG` SET `webversiondate` = '2011-10-20';
UPDATE `SYSTEMCONFIG` SET `webversionnumber` = '3.0.1';
UPDATE `SYSTEMCONFIG` SET `webversionstring`= '3.0.1';

INSERT INTO `ACTIVITYLOG`
	(`id`, `datecreated`, `sessionid`, `orderid`, `userid`, `userlogin`, `username`, `importance`, `sectioncode`, `actioncode`, `actionnotes`, `success`)
	VALUES (0, now(), 0, 0, 0, '', (SELECT USER()), 0, 'UPGRADE', '3.0.1', 'FINISHED', 1);

#
# DDL END
#

SET FOREIGN_KEY_CHECKS = 1;
