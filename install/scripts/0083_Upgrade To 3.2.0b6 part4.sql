#
SET FOREIGN_KEY_CHECKS = 0;

#
# DDL START
#

INSERT INTO `ACTIVITYLOG`
	(`id`, `datecreated`, `sessionid`, `orderid`, `userid`, `userlogin`, `username`, `importance`, `sectioncode`, `actioncode`, `actionnotes`, `success`)
	VALUES (0, now(), 0, 0, 0, '', (SELECT USER()), 0, 'UPGRADE', '3.2.0b6part4', 'STARTED', 1);

UPDATE ORDERHEADER oh SET oh.itemcount = (SELECT COUNT(*) FROM ORDERITEMS oi WHERE oi.orderid = oh.id) WHERE oh.itemcount = 0;

UPDATE ORDERITEMS oi JOIN (SELECT orderid, MIN(id) AS lowid FROM ORDERITEMS oi2 GROUP BY orderid) AS oi3 ON oi3.orderid = oi.orderid SET itemnumber = id - oi3.lowid + 1 WHERE oi.itemnumber = 0;

UPDATE `SYSTEMCONFIG` SET `webversiondate` = '2012-04-30';
UPDATE `SYSTEMCONFIG` SET `webversionnumber` = '3.2.0.8';
UPDATE `SYSTEMCONFIG` SET `webversionstring`= '3.2.0b6';

INSERT INTO `ACTIVITYLOG`
	(`id`, `datecreated`, `sessionid`, `orderid`, `userid`, `userlogin`, `username`, `importance`, `sectioncode`, `actioncode`, `actionnotes`, `success`)
	VALUES (0, now(), 0, 0, 0, '', (SELECT USER()), 0, 'UPGRADE', '3.2.0b6part4', 'FINISHED', 1);

#
# DDL END
#

SET FOREIGN_KEY_CHECKS = 1;
