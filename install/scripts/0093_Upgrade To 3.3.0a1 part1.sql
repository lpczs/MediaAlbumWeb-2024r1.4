#
SET FOREIGN_KEY_CHECKS = 0;

#
# DDL START
#

INSERT INTO `ACTIVITYLOG`
	(`id`, `datecreated`, `sessionid`, `orderid`, `userid`, `userlogin`, `username`, `importance`, `sectioncode`, `actioncode`, `actionnotes`, `success`)
	VALUES (0, now(), 0, 0, 0, '', (SELECT USER()), 0, 'UPGRADE', '3.3.0a1', 'STARTED', 1);

UPDATE `SYSTEMCONFIG` SET `webversiondate` = '2012-08-07';
UPDATE `SYSTEMCONFIG` SET `webversionnumber` = '3.3.0.1';
UPDATE `SYSTEMCONFIG` SET `webversionstring`= '3.3.0a1';

ALTER TABLE `USERS` ADD COLUMN `taxcode` VARCHAR(20) NOT NULL AFTER `paymentmethods`;
ALTER TABLE `USERS` ADD COLUMN `shippingtaxcode` VARCHAR(20) NOT NULL AFTER `taxcode`;

ALTER TABLE `LICENSEKEYS` ADD COLUMN `taxcode` VARCHAR(20) NOT NULL AFTER `currencycode`;
ALTER TABLE `LICENSEKEYS` ADD COLUMN `shippingtaxcode` VARCHAR(20) NOT NULL AFTER `taxcode`;

ALTER TABLE `USERS` ADD INDEX taxcode USING BTREE(`taxcode`);
ALTER TABLE `USERS` ADD INDEX shippingtaxcode USING BTREE(`shippingtaxcode`);

ALTER TABLE `LICENSEKEYS` ADD INDEX taxcode USING BTREE(`taxcode`);
ALTER TABLE `LICENSEKEYS` ADD INDEX shippingtaxcode USING BTREE(`shippingtaxcode`);

ALTER TABLE `OUTPUTDEVICES` ADD COLUMN `type` INTEGER NOT NULL DEFAULT 0 AFTER `name`,
ADD COLUMN `jdfurl` VARCHAR(100) NOT NULL AFTER `type`,
ADD COLUMN `jdfworkflowcode` VARCHAR(50) NOT NULL AFTER `jdfurl`,
ADD COLUMN `jdfworkflowname` VARCHAR(50) NOT NULL AFTER `jdfworkflowcode`,
ADD COLUMN `jdfworkflowcompletionstatus` INTEGER NOT NULL DEFAULT 0 AFTER `jdfworkflowname`;

ALTER TABLE `OUTPUTDEVICES` ADD COLUMN `jdfurlversion` VARCHAR(50) NOT NULL AFTER `jdfurl`;

ALTER TABLE `ORDERITEMS` ADD COLUMN `jobticketjdfjobticketid` VARCHAR(100) NOT NULL AFTER `cover2outputdevicecode`,
ADD COLUMN `pagesjdfjobticketid` VARCHAR(100) NOT NULL AFTER `jobticketjdfjobticketid`,
ADD COLUMN `cover1jdfjobticketid` VARCHAR(100) NOT NULL AFTER `pagesjdfjobticketid`,
ADD COLUMN `cover2jdfjobticketid` VARCHAR(100) NOT NULL AFTER `cover1jdfjobticketid`,
ADD COLUMN `jobticketjdfqueueid` VARCHAR(100) NOT NULL AFTER `cover2jdfjobticketid`,
ADD COLUMN `pagesjdfqueueid` VARCHAR(100) NOT NULL AFTER `jobticketjdfqueueid`,
ADD COLUMN `cover1jdfqueueid` VARCHAR(100) NOT NULL AFTER `pagesjdfqueueid`,
ADD COLUMN `cover2jdfqueueid` VARCHAR(100) NOT NULL AFTER `cover1jdfqueueid`,
ADD COLUMN `jobticketjdfcompletionstatus` INTEGER NOT NULL DEFAULT 0 AFTER `cover2jdfqueueid`,
ADD COLUMN `pagesjdfcompletionstatus` INTEGER NOT NULL DEFAULT 0 AFTER `jobticketjdfcompletionstatus`,
ADD COLUMN `cover1jdfcompletionstatus` INTEGER NOT NULL DEFAULT 0 AFTER `pagesjdfcompletionstatus`,
ADD COLUMN `cover2jdfcompletionstatus` INTEGER NOT NULL DEFAULT 0 AFTER `cover1jdfcompletionstatus`,
ADD COLUMN `jobticketjdfstatus` INTEGER NOT NULL DEFAULT 0 AFTER `cover2jdfcompletionstatus`,
ADD COLUMN `pagesjdfstatus` INTEGER NOT NULL DEFAULT 0 AFTER `jobticketjdfstatus`,
ADD COLUMN `cover1jdfstatus` INTEGER NOT NULL DEFAULT 0 AFTER `pagesjdfstatus`,
ADD COLUMN `cover2jdfstatus` INTEGER NOT NULL DEFAULT 0 AFTER `cover1jdfstatus`;

ALTER TABLE `ORDERITEMS` ADD INDEX jobticketjdfqueueid(`jobticketjdfqueueid`),
ADD INDEX pagesjdfqueueid(`pagesjdfqueueid`),
ADD INDEX cover1jdfqueueid(`cover1jdfqueueid`),
ADD INDEX cover2jdfqueueid(`cover2jdfqueueid`);

UPDATE `ORDERITEMS` SET `status` = 45 WHERE `status` = 44;

INSERT INTO `ACTIVITYLOG`
	(`id`, `datecreated`, `sessionid`, `orderid`, `userid`, `userlogin`, `username`, `importance`, `sectioncode`, `actioncode`, `actionnotes`, `success`)
	VALUES (0, now(), 0, 0, 0, '', (SELECT USER()), 0, 'UPGRADE', '3.3.0a1', 'FINISHED', 1);

#
# DDL END
#

SET FOREIGN_KEY_CHECKS = 1;
