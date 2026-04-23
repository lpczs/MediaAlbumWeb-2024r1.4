#
SET FOREIGN_KEY_CHECKS = 0;

#
# DDL START
#

UPDATE `SYSTEMCONFIG` SET `webversiondate` = '2021-10-06';
UPDATE `SYSTEMCONFIG` SET `webversionnumber` = '2021.3.0';
UPDATE `SYSTEMCONFIG` SET `webversionstring`= '2021r3';

CREATE TABLE IF NOT EXISTS `CONNECTORS` (
  `id` int(11) NOT NULL auto_increment,
  `datecreated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `connectorname` varchar(50) NOT NULL DEFAULT '',
  `brandcode` varchar(50) NOT NULL DEFAULT '',
  `connectorurl` varchar(200) NOT NULL DEFAULT '',
  `connectorprimarydomain` VARCHAR(200) NOT NULL DEFAULT '',
  `connectorkey` varchar(50) NOT NULL DEFAULT '',
  `connectorsecret` varchar(200) NOT NULL DEFAULT '',
  `connectoraccesstoken1` varchar(50) NOT NULL DEFAULT '',
  `connectoraccesstoken2` VARCHAR(45) NOT NULL DEFAULT '',
  `connectorinstallurl` varchar(2048) NOT NULL DEFAULT '',
  `licensekeycode` VARCHAR(50) NOT NULL DEFAULT '',
  `productsactive` TINYINT(1) NOT NULL DEFAULT 0,
  `pricesincludetax` TINYINT(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `CONNECTORSPRODUCTCOLLECTIONLINK` (
  `id` int(11) NOT NULL auto_increment,
  `connectorurl` varchar(200) NOT NULL DEFAULT '',
  `collectioncode` varchar(50) NOT NULL DEFAULT '',
  `connectorproduct_id` varchar(50) NOT NULL DEFAULT '',
  `connectorproduct_datecreated` TIMESTAMP,
  `connectorproduct_dateupdated` TIMESTAMP,
  `metadata` blob,
  `metadatalength` int NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `TASKS` (
  `datecreated`, `taskcode`, `taskname`, `intervaltype`, `intervalvalue`, `lastruntime`,
  `nextruntime`, `statuscode`, `statusmessage`, `runstatus`, `maxruncount`, `internal`,
  `scriptfilename`, `deleteexpiredinterval`, `active`
)
VALUES (
  NOW(), 'TAOPIX_CONNECTORPRODUCTSYNC', 'en Connector Product Syncronisation', 1, '1', '0000-00-00 00:00:00',
  '0000-00-00 00:00:00', 0, '', 0, 10, 1, 'connectorProductSync.php', 10, 1
),
(
  NOW(), 'TAOPIX_CONNECTORPROCESSSYNCRESULTS', 'en Connector Process Syncronisation Results', 1, '1', '0000-00-00 00:00:00',
  '0000-00-00 00:00:00', 0, '', 0, 10, 1, 'connectorProcessSyncResults.php', 10, 1
),
(
  NOW(), 'TAOPIX_CLEANUPPROJECTORDERDATACACHE', 'en Clean-up External cart data cache', 1, '1', '0000-00-00 00:00:00',
  '0000-00-00 00:00:00', 0, '', 0, 10, 1, 'cleanUpProjectOrderDataCache.php', 10, 1
);

ALTER TABLE `PROJECTORDERDATACACHE`
    ADD COLUMN `connectorid` int NOT NULL default 0 AFTER `source`,
    ADD COLUMN `externalproductid` varchar(50) NOT NULL default '' AFTER `connectorid`,
    ADD COLUMN `orderdate` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' AFTER `externalproductid`,
    ADD COLUMN `ordernumber` varchar(50) NOT NULL default '' AFTER `orderdate`;

ALTER TABLE `SYSTEMCONFIG`
CHANGE COLUMN `config` `config` BIGINT NOT NULL;

#
# DDL END
#

SET FOREIGN_KEY_CHECKS = 1;

