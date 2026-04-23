#
SET FOREIGN_KEY_CHECKS = 0;

#
# DDL START
#

INSERT INTO `ACTIVITYLOG`
	(`id`, `datecreated`, `sessionid`, `orderid`, `userid`, `userlogin`, `username`, `importance`, `sectioncode`, `actioncode`, `actionnotes`, `success`)
	VALUES (0, now(), 0, 0, 0, '', (SELECT USER()), 0, 'UPGRADE', '3.1.0a1', 'STARTED', 1);

ALTER TABLE `ORDERHEADER` ADD COLUMN `itemcount` INT NOT NULL DEFAULT 0 AFTER `voucherdiscountvalue`;

UPDATE ORDERHEADER oh SET oh.itemcount = (SELECT COUNT(*) FROM ORDERITEMS oi WHERE oi.orderid = oh.id);

ALTER TABLE `ORDERITEMS` ADD COLUMN `itemnumber` INT NOT NULL DEFAULT 0 AFTER `orderid`;

UPDATE ORDERITEMS oi JOIN (SELECT orderid, MIN(id) AS lowid FROM ORDERITEMS oi2 GROUP BY orderid) AS oi3 ON oi3.orderid = oi.orderid SET itemnumber = id - oi3.lowid + 1;

UPDATE `SYSTEMCONFIG` SET `webversiondate` = '2011-12-13';
UPDATE `SYSTEMCONFIG` SET `webversionnumber` = '3.1.0.1';
UPDATE `SYSTEMCONFIG` SET `webversionstring`= '3.1.0a1';

INSERT INTO `ACTIVITYLOG`
	(`id`, `datecreated`, `sessionid`, `orderid`, `userid`, `userlogin`, `username`, `importance`, `sectioncode`, `actioncode`, `actionnotes`, `success`)
	VALUES (0, now(), 0, 0, 0, '', (SELECT USER()), 0, 'UPGRADE', '3.1.0a1', 'FINISHED', 1);

#
# DDL END
#

SET FOREIGN_KEY_CHECKS = 1;
