#
SET FOREIGN_KEY_CHECKS = 0;

#
# DDL START
#

INSERT INTO `ACTIVITYLOG`
	(`id`, `datecreated`, `sessionid`, `orderid`, `userid`, `userlogin`, `username`, `importance`, `sectioncode`, `actioncode`, `actionnotes`, `success`)
	VALUES (0, now(), 0, 0, 0, '', (SELECT USER()), 0, 'UPGRADE', '2015r2a23 part1', 'STARTED', 1);

UPDATE `SYSTEMCONFIG` SET `webversiondate` = '2015-06-25';
UPDATE `SYSTEMCONFIG` SET `webversionnumber` = '2015.2.0.23';
UPDATE `SYSTEMCONFIG` SET `webversionstring`= '2015r2a23';


INSERT INTO `COUNTRIES` (`name`, `isocode2`, `isocode3`, `region`, `displayfields`, `compulsoryfields`, `displayformat`, `fieldlabels`, `addressformatid`)
  SELECT 'South Sudan', 'SS', 'SSD', 'STATE', '', '', '' , '', 1
    FROM DUAL
    WHERE NOT EXISTS(SELECT * FROM `COUNTRIES` WHERE `isocode2` = 'SS' AND `isocode3` = 'SSD');


INSERT INTO `COUNTRIES` (`name`, `isocode2`, `isocode3`, `region`, `displayfields`, `compulsoryfields`, `displayformat`, `fieldlabels`, `addressformatid`)
  SELECT 'Kosovo', 'XK', 'XKX', 'STATE', '', '', '' , '', 1
    FROM DUAL
    WHERE NOT EXISTS (SELECT * FROM `COUNTRIES` WHERE `isocode2` = 'XK' AND `isocode3` = 'XKX');


INSERT INTO `ACTIVITYLOG`
(`id`, `datecreated`, `sessionid`, `orderid`, `userid`, `userlogin`, `username`, `importance`, `sectioncode`, `actioncode`, `actionnotes`, `success`)
VALUES (0, now(), 0, 0, 0, '', (SELECT USER()), 0, 'UPGRADE', '2015r2a23 part1', 'FINISHED', 1);


#
# DDL END
#

SET FOREIGN_KEY_CHECKS = 1;