#
SET FOREIGN_KEY_CHECKS = 0;

#
# DDL START
#

INSERT INTO `ACTIVITYLOG`
	(`id`, `datecreated`, `sessionid`, `orderid`, `userid`, `userlogin`, `username`, `importance`, `sectioncode`, `actioncode`, `actionnotes`, `success`)
	VALUES (0, now(), 0, 0, 0, '', (SELECT USER()), 0, 'UPGRADE', '3.5.0a2 part3', 'STARTED', 1);

UPDATE `SYSTEMCONFIG` SET `webversiondate` = '2013-02-13';
UPDATE `SYSTEMCONFIG` SET `webversionnumber` = '3.5.0.2';
UPDATE `SYSTEMCONFIG` SET `webversionstring`= '3.5.0a2';
 
UPDATE `USERS` SET `registeredtaxnumber` = `address3` WHERE `countrycode` = 'BR';

UPDATE `USERS` SET `address3` = '' WHERE `countrycode` = 'BR';

ALTER TABLE `SYSTEMCONFIG` ADD COLUMN `ownercode` VARCHAR(50) NOT NULL AFTER `datecreated`,
	ADD COLUMN `ownercode2` VARCHAR(50) NOT NULL AFTER `ownercode`;

ALTER TABLE `ORDERHEADER` ADD COLUMN `billingcustomerregisteredtaxnumbertype` TINYINT(1) NOT NULL DEFAULT 0 AFTER `billingcontactlastname`;

ALTER TABLE `ORDERHEADER` ADD COLUMN `billingcustomerregisteredtaxnumber` VARCHAR(50) NOT NULL AFTER `billingcustomerregisteredtaxnumbertype`;

UPDATE `COUNTRIES`
	SET `displayfields` = 'country<p>company<p>firstname<p>lastname<p>add1=[add41], [add42] - [add43]<p>add2<p>*bregtaxnumtype<p>*bregtaxnum<p>postcode<p>city<p>state', 
		`fieldlabels` = 'str_LabelCountry,str_LabelCompanyName,str_LabelFirstName,str_LabelLastName,str_LabelAddressLine1,str_LabelHouseNumber,str_LabelAddressLine2,str_LabelAddressLine3,str_TaxNumberType,str_TaxNumber,str_LabelZIPCode,str_LabelTownCity,str_LabelState', 
		`compulsoryfields` = 'country,firstname,lastname,add41,add42,regtaxnum,regtaxnumtype,postcode,city,state', 
		`displayformat` = '[company]<br>[firstname] [lastname]<br>[add1]<br>[add2]<br>[postcode] [city] [state]<br>[country]',
		`region` = 'STATE'
	WHERE `isocode2`='BR';


INSERT INTO `ACTIVITYLOG`
	(`id`, `datecreated`, `sessionid`, `orderid`, `userid`, `userlogin`, `username`, `importance`, `sectioncode`, `actioncode`, `actionnotes`, `success`)
	VALUES (0, now(), 0, 0, 0, '', (SELECT USER()), 0, 'UPGRADE', '3.5.0a2 part3', 'FINISHED', 1);

#
# DDL END
#

SET FOREIGN_KEY_CHECKS = 1;
