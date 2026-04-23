#
SET FOREIGN_KEY_CHECKS = 0;

#
# DDL START
#

INSERT INTO `ACTIVITYLOG`
	(`id`, `datecreated`, `sessionid`, `orderid`, `userid`, `userlogin`, `username`, `importance`, `sectioncode`, `actioncode`, `actionnotes`, `success`)
	VALUES (0, now(), 0, 0, 0, '', (SELECT USER()), 0, 'UPGRADE', '5.0.0a15 part1', 'STARTED', 1);

UPDATE `SYSTEMCONFIG` SET `webversiondate` = '2014-08-14';
UPDATE `SYSTEMCONFIG` SET `webversionnumber` = '5.0.0.15';
UPDATE `SYSTEMCONFIG` SET `webversionstring`= '5.0.0a15';


ALTER TABLE `ORDERITEMS` 
 ADD COLUMN `jobticketoutputsubfoldername` VARCHAR(200) NOT NULL DEFAULT '' AFTER `convertoutputformatcode`,
 ADD COLUMN `pagesoutputsubfoldername` VARCHAR(200) NOT NULL DEFAULT '' AFTER `jobticketoutputsubfoldername`,
 ADD COLUMN `cover1outputsubfoldername` VARCHAR(200) NOT NULL DEFAULT '' AFTER `pagesoutputsubfoldername`,
 ADD COLUMN `cover2outputsubfoldername` VARCHAR(200) NOT NULL DEFAULT '' AFTER `cover1outputsubfoldername`,
 ADD COLUMN `xmloutputsubfoldername` VARCHAR(200) NOT NULL DEFAULT '' AFTER `cover2outputsubfoldername`;

INSERT INTO `ACTIVITYLOG`
(`id`, `datecreated`, `sessionid`, `orderid`, `userid`, `userlogin`, `username`, `importance`, `sectioncode`, `actioncode`, `actionnotes`, `success`)
VALUES (0, now(), 0, 0, 0, '', (SELECT USER()), 0, 'UPGRADE', '5.0.0a15 part1', 'FINISHED', 1);

#
# DDL END
#

SET FOREIGN_KEY_CHECKS = 1;
