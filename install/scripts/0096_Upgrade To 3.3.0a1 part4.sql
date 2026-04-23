#
SET FOREIGN_KEY_CHECKS = 0;

#
# DDL START
#

INSERT INTO `ACTIVITYLOG`
	(`id`, `datecreated`, `sessionid`, `orderid`, `userid`, `userlogin`, `username`, `importance`, `sectioncode`, `actioncode`, `actionnotes`, `success`)
	VALUES (0, now(), 0, 0, 0, '', (SELECT USER()), 0, 'UPGRADE', '3.3.0a1 part4', 'STARTED', 1);

UPDATE `SYSTEMCONFIG` SET `webversiondate` = '2012-08-08';
UPDATE `SYSTEMCONFIG` SET `webversionnumber` = '3.3.0.1';
UPDATE `SYSTEMCONFIG` SET `webversionstring`= '3.3.0a1';

ALTER TABLE `OUTPUTDEVICES` ADD COLUMN `copyfiles` TINYINT(1) NOT NULL DEFAULT 0 AFTER `pathserver`;
UPDATE `OUTPUTDEVICES` SET `copyfiles` = 1;
ALTER TABLE `ORDERITEMS` MODIFY COLUMN `statusdescription` VARCHAR(300) NOT NULL;

INSERT INTO `TRIGGERS` (`datecreated` ,`eventcode` ,`language` ,`exportformat` ,`includepaymentdata` ,`beautifiedxml` ,`subfolderformat` ,`filenameformat` ,`active`) 
VALUES (now(),'DECRYPTEDRENDERED', 'Default', 'XML', 0, 1, '', '', 0);

INSERT INTO `TRIGGERS` (`datecreated` ,`eventcode` ,`language` ,`exportformat` ,`includepaymentdata` ,`beautifiedxml` ,`subfolderformat` ,`filenameformat` ,`active`) 
VALUES (now(),'DECRYPTEDPROJECTELEMENTS', 'Default', 'XML', 0, 1, '', '', 0);

INSERT INTO `TRIGGERS` (`datecreated` ,`eventcode` ,`language` ,`exportformat` ,`includepaymentdata` ,`beautifiedxml` ,`subfolderformat` ,`filenameformat` ,`active`) 
VALUES (now(),'FILESSENTTOEXTERNALWORKFLOW', 'Default', 'XML', 0, 1, '', '', 0);

ALTER TABLE `OUTPUTFORMATS` ADD COLUMN `xmloutputfile` TINYINT(1) NOT NULL DEFAULT 0 AFTER `jobticketfilenameformat`,
 ADD COLUMN `xmlfilenameformat` VARCHAR(100) NOT NULL AFTER `xmloutputfile`,
 ADD COLUMN `xmldefaultoutputdevicecode` VARCHAR(50) NOT NULL AFTER `cover2defaultoutputdevicecode`,
 ADD COLUMN `xmlsubfoldernameformat` VARCHAR(100) NOT NULL AFTER `cover2subfoldernameformat`,
 ADD COLUMN `xmllanguage` VARCHAR(10) NOT NULL AFTER `xmlsubfoldernameformat`,
 ADD COLUMN `xmlincludepaymentdata` TINYINT(1) NOT NULL DEFAULT 0 AFTER `xmllanguage`,
 ADD COLUMN `xmlbeautified` TINYINT(1) NOT NULL DEFAULT 0 AFTER `xmlincludepaymentdata`;

ALTER TABLE `OUTPUTFORMATS` ADD COLUMN `printersmarkscolourspace` INTEGER NOT NULL DEFAULT 0 AFTER `xmlbeautified`;

UPDATE `OUTPUTFORMATS` SET `printersmarkscolourspace` = 1;

ALTER TABLE `ORDERITEMS` ADD COLUMN `xmloutputfilename` VARCHAR(200) NOT NULL AFTER `cover2outputfilename`,
 ADD COLUMN `xmloutputdevicecode` VARCHAR(100) NOT NULL AFTER `cover2outputdevicecode`;

ALTER TABLE `ORDERITEMS` ADD INDEX userid(`userid`);

INSERT INTO `ACTIVITYLOG`
	(`id`, `datecreated`, `sessionid`, `orderid`, `userid`, `userlogin`, `username`, `importance`, `sectioncode`, `actioncode`, `actionnotes`, `success`)
	VALUES (0, now(), 0, 0, 0, '', (SELECT USER()), 0, 'UPGRADE', '3.3.0a1 part4', 'FINISHED', 1);

#
# DDL END
#

SET FOREIGN_KEY_CHECKS = 1;
