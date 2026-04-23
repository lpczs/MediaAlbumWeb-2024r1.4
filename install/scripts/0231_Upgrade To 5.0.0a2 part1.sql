#
SET FOREIGN_KEY_CHECKS = 0;

#
# DDL START
#

INSERT INTO `ACTIVITYLOG`
	(`id`, `datecreated`, `sessionid`, `orderid`, `userid`, `userlogin`, `username`, `importance`, `sectioncode`, `actioncode`, `actionnotes`, `success`)
	VALUES (0, now(), 0, 0, 0, '', (SELECT USER()), 0, 'UPGRADE', '5.0.0a2 part1', 'STARTED', 1);

UPDATE `SYSTEMCONFIG` SET `webversiondate` = '2014-06-11';
UPDATE `SYSTEMCONFIG` SET `webversionnumber` = '5.0.0.2';
UPDATE `SYSTEMCONFIG` SET `webversionstring`= '5.0.0a2';

ALTER TABLE `COMPONENTS` ADD COLUMN `storewhennotselected` TINYINT(1) NOT NULL DEFAULT 1 AFTER `orderfootertaxlevel`;

ALTER TABLE `BRANDING` ADD COLUMN `orderfrompreview` TINYINT( 1 ) NOT NULL DEFAULT '1' AFTER `sharebyemailmethod`;

ALTER TABLE `LICENSEKEYS` ADD COLUMN `orderfrompreview` TINYINT( 1 ) NOT NULL DEFAULT '2' AFTER `useremaildestination`;

UPDATE `COUNTRIES` SET `displayfields` = 'country<p>firstname<p>lastname<p>company<p>add1<p>add2<p>city<p>state<p>postcode',
`compulsoryfields` = 'country,firstname,lastname,add1,state,postcode',
`displayformat` = '[firstname] [lastname]<br>[company]<br>[add1]<br>[add2]<br>[city]<br>[state] [postcode]<br>[country]',
`fieldlabels` = 'str_LabelCountry,str_LabelFirstName,str_LabelLastName,str_LabelCompanyName,str_LabelAddressLine1,str_LabelAddressLine2,str_LabelTownCity,str_LabelState,str_LabelPostalCode'
WHERE `COUNTRIES`.`isocode2` = 'AU';

INSERT INTO `COUNTRYREGION` ( `datecreated`, `countrycode`, `regioncode`, `regionname`, `regiongroupcode`)
VALUES (now(), 'AU', 'ACT', 'au Australian Capital Territory', ''),
(now(), 'AU', 'NSW', 'au New South Wales', ''),
(now(), 'AU', 'VIC', 'au Victoria', ''),
(now(), 'AU', 'QLD', 'au Queensland', ''),
(now(), 'AU', 'SA', 'au South Australia', ''),
(now(), 'AU', 'WA', 'au Western Australia', ''),
(now(), 'AU', 'TAS', 'au Tasmania', ''),
(now(), 'AU', 'NT', 'au Northern Territory', '');

UPDATE `USERS` SET `addressupdated` = '0' WHERE `USERS`.`countrycode` = 'AU';

ALTER TABLE `BRANDING` ADD `defaultcommunicationpreference` INT(11) NOT NULL DEFAULT '1' AFTER `supportemailaddress`;

ALTER TABLE `ACTIVITYLOG` ADD `ipaddress` VARCHAR(20) NOT NULL DEFAULT '' AFTER `orderid`;

INSERT INTO `ACTIVITYLOG`
	(`id`, `datecreated`, `sessionid`, `orderid`, `userid`, `userlogin`, `username`, `importance`, `sectioncode`, `actioncode`, `actionnotes`, `success`)
	VALUES (0, now(), 0, 0, 0, '', (SELECT USER()), 0, 'UPGRADE', '5.0.0a2 part1', 'FINISHED', 1);

#
# DDL END
#

SET FOREIGN_KEY_CHECKS = 1;
