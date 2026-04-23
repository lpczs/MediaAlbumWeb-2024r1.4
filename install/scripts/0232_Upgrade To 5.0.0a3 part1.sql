#
SET FOREIGN_KEY_CHECKS = 0;

#
# DDL START
#

INSERT INTO `ACTIVITYLOG`
	(`id`, `datecreated`, `sessionid`, `orderid`, `userid`, `userlogin`, `username`, `importance`, `sectioncode`, `actioncode`, `actionnotes`, `success`)
	VALUES (0, now(), 0, 0, 0, '', (SELECT USER()), 0, 'UPGRADE', '5.0.0a3 part1', 'STARTED', 1);

UPDATE `SYSTEMCONFIG` SET `webversiondate` = '2014-06-11';
UPDATE `SYSTEMCONFIG` SET `webversionnumber` = '5.0.0.3';
UPDATE `SYSTEMCONFIG` SET `webversionstring`= '5.0.0a3';

#
# Add new Countries
#

INSERT INTO `COUNTRIES` (`name`, `isocode2`, `isocode3`, `region`, `displayfields`, `compulsoryfields`, `displayformat`, `fieldlabels`, `addressformatid`)
    SELECT 'Montenegro', 'ME', 'MNE', 'STATE', '','', '','',1 FROM DUAL WHERE NOT EXISTS (SELECT * FROM `COUNTRIES` WHERE `isocode2`='ME' AND `isocode3`='MNE');

INSERT INTO `COUNTRIES` (`name`, `isocode2`, `isocode3`, `region`, `displayfields`, `compulsoryfields`, `displayformat`, `fieldlabels`, `addressformatid`)
    SELECT 'Serbia', 'RS', 'SRB', 'STATE', '','', '','',1 FROM DUAL WHERE NOT EXISTS (SELECT * FROM `COUNTRIES` WHERE `isocode2`='RS' AND `isocode3`='SRB');


#
# Update Australia address
#

DELETE FROM `COUNTRYREGION` WHERE `countrycode` = 'AU';

INSERT INTO `COUNTRYREGION` ( `datecreated`, `countrycode`, `regioncode`, `regionname`, `regiongroupcode`)
VALUES (now(), 'AU', 'ACT', 'en Australian Capital Territory', ''),
(now(), 'AU', 'NSW', 'en New South Wales', ''),
(now(), 'AU', 'VIC', 'en Victoria', ''),
(now(), 'AU', 'QLD', 'en Queensland', ''),
(now(), 'AU', 'SA', 'en South Australia', ''),
(now(), 'AU', 'WA', 'en Western Australia', ''),
(now(), 'AU', 'TAS', 'en Tasmania', ''),
(now(), 'AU', 'NT', 'en Northern Territory', '');

UPDATE `COUNTRIES` SET `compulsoryfields` = 'country,firstname,lastname,add1,city,state,postcode' WHERE `COUNTRIES`.`isocode2` = 'AU';

UPDATE `USERS` SET `addressupdated` = '0' WHERE `USERS`.`countrycode` = 'AU';

ALTER TABLE `ORDERHEADER` ADD COLUMN `paymentgatewaysubcode` VARCHAR(20) NOT NULL DEFAULT '' AFTER `paymentgatewaycode`;
UPDATE `ORDERHEADER` SET `paymentgatewaysubcode` = `paymentgatewaycode`;

UPDATE `ORDERHEADER` SET `paymentgatewaycode` = '';

INSERT INTO `ACTIVITYLOG`
	(`id`, `datecreated`, `sessionid`, `orderid`, `userid`, `userlogin`, `username`, `importance`, `sectioncode`, `actioncode`, `actionnotes`, `success`)
	VALUES (0, now(), 0, 0, 0, '', (SELECT USER()), 0, 'UPGRADE', '5.0.0a3 part1', 'FINISHED', 1);

#
# DDL END
#

SET FOREIGN_KEY_CHECKS = 1;
