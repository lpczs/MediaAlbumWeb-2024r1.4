#
SET FOREIGN_KEY_CHECKS = 0;

#
# DDL START
#

UPDATE `SYSTEMCONFIG` SET `webversiondate` = '2018-07-10';
UPDATE `SYSTEMCONFIG` SET `webversionnumber` = '2018.3.0';
UPDATE `SYSTEMCONFIG` SET `webversionstring`= '2018r3';


ALTER TABLE `USERS`
  ADD COLUMN `sendmarketinginfooptindate` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' AFTER `sendmarketinginfo`;


ALTER TABLE `ORDERHEADER`
  ADD COLUMN `pricingengineversion` TINYINT(3) UNSIGNED NOT NULL DEFAULT 0 AFTER `currencycode`,
  ADD COLUMN `dbdata` DATETIME NULL DEFAULT '0000-00-00 00:00:00' AFTER `orderready`;


INSERT INTO `TASKS` (
  `datecreated`, `taskcode`, `taskname`, `intervaltype`, `intervalvalue`, `lastruntime`,
  `nextruntime`, `statuscode`, `statusmessage`, `runstatus`, `maxruncount`, `internal`,
  `scriptfilename`, `deleteexpiredinterval`, `active`)
VALUES
  (NOW(), 'TAOPIX_CUSTOMERDATAEXPORT', 'en Customer Data Export', 1, '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', 0, 10, 1, 'customerDataExportTask.php', 10, 1),
  (NOW(), 'TAOPIX_ORDERDATADELETION', 'en Order Data Deletion', 1, '60', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', 0, 10, 1, 'orderDataDeletionTask.php', 10, 1),
  (NOW(), 'TAOPIX_BULKEMAIL', 'en Bulk Email Task', 1, '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', 0, 10, 1, 'bulkEmailTask.php', 10, 1),
  (NOW(), 'TAOPIX_MASSUNSUBSCRIBE', 'en Mass Unsubscribe Task', 1, '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', 0, 10, 1, 'massUnsubscribeTask.php', 10, 1);


ALTER TABLE `PRODUCTS`
  ADD COLUMN `pricetransformationstage` TINYINT(3) UNSIGNED NOT NULL DEFAULT 2 AFTER `productoptions`;


DELETE FROM `CACHEDATA`;


ALTER TABLE `BRANDING`
  ADD COLUMN `orderredactiondays` INT(11) NOT NULL DEFAULT '0' AFTER `redactionnotificationdays`,
  ADD COLUMN `orderredactionmode` TINYINT(1) NOT NULL DEFAULT '0' AFTER `orderredactiondays`;


ALTER TABLE `ORDERITEMS`
  ADD COLUMN `purgedate` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' AFTER `activeuserid`,
  ADD COLUMN `purgenextcheckdate` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' AFTER `purgedate`,
  ADD COLUMN `productiondata` TINYINT(1) NOT NULL DEFAULT 0 AFTER `purgenextcheckdate`,
  ADD COLUMN `ftpdata` TINYINT(1) NOT NULL DEFAULT 0 AFTER `productiondata`,
  ADD COLUMN `dbdata` DATETIME NULL DEFAULT '0000-00-00 00:00:00' AFTER `ftpdata`,
  ADD COLUMN `message` VARCHAR(45) NULL DEFAULT '' AFTER `dbdata`;


-- Set all photo prints projects to use the pre transformation stage by default as part of the upgrade
UPDATE `PRODUCTS` p
	JOIN `PRODUCTCOLLECTIONLINK` l ON `l`.`productcode` = `p`.`code`
SET `p`.`pricetransformationstage` = 1
WHERE `l`.`collectiontype` = 2;


#
# DDL END
#

SET FOREIGN_KEY_CHECKS = 1;

