#
SET FOREIGN_KEY_CHECKS = 0;

#
# DDL START
#

INSERT INTO `ACTIVITYLOG`
	(`id`, `datecreated`, `sessionid`, `orderid`, `userid`, `userlogin`, `username`, `importance`, `sectioncode`, `actioncode`, `actionnotes`, `success`)
	VALUES (0, now(), 0, 0, 0, '', (SELECT USER()), 0, 'UPGRADE', '3.3.0a2 part1', 'STARTED', 1);

UPDATE `SYSTEMCONFIG` SET `webversiondate` = '2012-08-13';
UPDATE `SYSTEMCONFIG` SET `webversionnumber` = '3.3.0.2';
UPDATE `SYSTEMCONFIG` SET `webversionstring`= '3.3.0a2';

ALTER TABLE `VOUCHERS`
	ADD `type` INTEGER DEFAULT 0 NOT NULL AFTER `code`,
	ADD `redeemeduserid` INTEGER DEFAULT 0 NOT NULL AFTER `discountvalue`,
	ADD `agentfee` DECIMAL(10, 2) DEFAULT 0.00 NOT NULL AFTER `discountvalue`,
	ADD `sellprice` DECIMAL(10, 2) DEFAULT 0.00 NOT NULL AFTER `discountvalue`,
	ADD `redeemeddate` datetime default '0000-00-00 00:00:00' NOT NULL AFTER `redeemeduserid`,
	ADD `defaultdiscount` int default 0 NOT NULL AFTER `type`;

CREATE INDEX type ON `VOUCHERS` (type);

ALTER TABLE  `ORDERHEADER`
	ADD `vouchertype` INTEGER DEFAULT 0 NOT NULL AFTER `vouchercode`,
	ADD `voucheragentfee` DECIMAL(10, 2) DEFAULT 0.00 NOT NULL AFTER `voucherdiscountvalue`,
	ADD `vouchersellprice` DECIMAL(10, 2) DEFAULT 0.00 NOT NULL AFTER `voucherdiscountvalue`,
	ADD `totaltopay` DECIMAL(10, 2) DEFAULT 0.00 NOT NULL AFTER `total`,
	ADD `giftcardamount` DECIMAL(10, 2) DEFAULT 0.00 NOT NULL AFTER `total`;
	
UPDATE `ORDERHEADER` SET `totaltopay` = (`total` - `giftcardamount`);

ALTER TABLE  `USERS`
	ADD `giftcardbalance` DECIMAL(10, 2) DEFAULT 0.00 NOT NULL AFTER `accountbalance`;
	
ALTER TABLE `SESSIONDATA`
	ADD `giftcardtotal` DECIMAL (10, 2) DEFAULT 0.00 NOT NULL;

ALTER TABLE `BRANDING` ADD COLUMN `smtptype` VARCHAR(50) NOT NULL AFTER `smtpauthpassword`;

INSERT INTO `ACTIVITYLOG`
	(`id`, `datecreated`, `sessionid`, `orderid`, `userid`, `userlogin`, `username`, `importance`, `sectioncode`, `actioncode`, `actionnotes`, `success`)
	VALUES (0, now(), 0, 0, 0, '', (SELECT USER()), 0, 'UPGRADE', '3.3.0a2 part1', 'FINISHED', 1);

#
# DDL END
#

SET FOREIGN_KEY_CHECKS = 1;
