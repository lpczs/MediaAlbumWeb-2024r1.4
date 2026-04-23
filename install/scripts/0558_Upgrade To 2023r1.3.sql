#
SET FOREIGN_KEY_CHECKS = 0;

#
# DDL START
#

UPDATE `SYSTEMCONFIG` SET `webversiondate` = '2024-07-10';
UPDATE `SYSTEMCONFIG` SET `webversionnumber` = '2023.1.3';
UPDATE `SYSTEMCONFIG` SET `webversionstring`= '2023r1.3';

CALL AddColumn('CONNECTORS','shippingprofiledata', 'MEDIUMBLOB');
CALL AddColumn('CONNECTORS','shippingprofiledatalength', 'int NOT NULL DEFAULT 0');

INSERT INTO `TASKS` (
`datecreated`, `taskcode`, `taskname`, `intervaltype`, `intervalvalue`, `lastruntime`,
`nextruntime`, `statuscode`, `statusmessage`, `runstatus`, `maxruncount`, `internal`,
`scriptfilename`, `deleteexpiredinterval`, `active`
)
SELECT NOW(), 'TAOPIX_CONNECTORSHIPPINGPROFILECACHE', 'en Connector Shipping Profile Cache Population', 1, '1', '0000-00-00 00:00:00',
'0000-00-00 00:00:00', 0, '', 0, 10, 1, 'connectorPopulateShippingProfileCache.php', 10, 1
FROM `TASKS`
WHERE NOT EXISTS (SELECT * FROM `TASKS` 
    WHERE `taskcode`='TAOPIX_CONNECTORSHIPPINGPROFILECACHE' LIMIT 1) 
LIMIT 1;

ALTER TABLE `KEYWORDS` MODIFY `name` VARCHAR(4096) NOT NULL DEFAULT '';


#
# DDL END
#

SET FOREIGN_KEY_CHECKS = 1;
