#
SET FOREIGN_KEY_CHECKS = 0;

#
# DDL START
#

UPDATE `SYSTEMCONFIG` SET `webversiondate` = '2022-01-24';
UPDATE `SYSTEMCONFIG` SET `webversionnumber` = '2021.3.4';
UPDATE `SYSTEMCONFIG` SET `webversionstring`= '2021r3.4';

ALTER TABLE `CONNECTORS` 
	ADD COLUMN `discountdata` MEDIUMBLOB,
    ADD COLUMN `discountdatalength` int NOT NULL DEFAULT 0,
    ADD COLUMN `discountdataupdated` TIMESTAMP DEFAULT current_timestamp
;

INSERT INTO `TASKS` (
  `datecreated`, `taskcode`, `taskname`, `intervaltype`, `intervalvalue`, `lastruntime`,
  `nextruntime`, `statuscode`, `statusmessage`, `runstatus`, `maxruncount`, `internal`,
  `scriptfilename`, `deleteexpiredinterval`, `active`
)
VALUES (
  NOW(), 'TAOPIX_CONNECTORPOPULATEDISCOUNTDATACACHE', 'en Connector Discount Data Cache Population', 1, '1', '0000-00-00 00:00:00',
  '0000-00-00 00:00:00', 0, '', 0, 10, 1, 'connectorPopulateDiscountDataCache.php', 10, 1
);

CREATE TABLE IF NOT EXISTS `CONNECTORSWEBHOOKDATA` (
  `id` int(11) NOT NULL auto_increment,
  `datecreated` TIMESTAMP  NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `connectortype` varchar(200) NOT NULL DEFAULT '',
  `webhooktopic` varchar(200) NOT NULL DEFAULT '',
  `ordernumber` varchar(50) NOT NULL DEFAULT '',
  `data` mediumblob,
  `datalength` int NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

#
# DDL END
#

SET FOREIGN_KEY_CHECKS = 1;

