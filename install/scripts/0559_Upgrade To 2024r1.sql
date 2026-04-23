#
SET FOREIGN_KEY_CHECKS = 0;

#
# DDL START
#

UPDATE `SYSTEMCONFIG` SET `webversiondate` = '2024-08-15';
UPDATE `SYSTEMCONFIG` SET `webversionnumber` = '2024.1.0';
UPDATE `SYSTEMCONFIG` SET `webversionstring`= '2024r1';

ALTER TABLE `BRANDING`
    add `onlineuiurl` varchar(200) not null default '' after `onlinedesignerlogouturl`,
    add `onlineapiurl` varchar(200) not null default '' after `onlineuiurl`,
    add `onlineappkeyentropyvalue` varchar(64) not null default '',
    add `onlineabouturl` varchar(200) not null default '',
    add `onlinehelpurl` varchar(200) not null default '',
    add `onlinetermsandconditionsurl` varchar(200) not null default '';
 
ALTER TABLE `BRANDING` ADD COLUMN `theme` int NOT NULL DEFAULT '1';

UPDATE `BRANDING` SET `onlineapiurl` =IF(SUBSTRING(`onlinedesignerurl`, -1, 1) = '/', SUBSTRING(`onlinedesignerurl`, 1, LENGTH(`onlinedesignerurl`)-1), `onlinedesignerurl`), `onlineuiurl` =
	CONCAT(`onlinedesignerurl`, IF(SUBSTRING(`onlinedesignerurl`, -1, 1) = '/', 'ui/', '/ui/'), IF(`name` = '', 'default', `name`))
	WHERE `onlinedesignerurl` != '';

#
# DDL END
#

SET FOREIGN_KEY_CHECKS = 1;
