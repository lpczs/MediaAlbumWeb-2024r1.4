#
SET FOREIGN_KEY_CHECKS = 0;

#
# DDL START
#

INSERT INTO `ACTIVITYLOG`
	(`id`, `datecreated`, `sessionid`, `orderid`, `userid`, `userlogin`, `username`, `importance`, `sectioncode`, `actioncode`, `actionnotes`, `success`)
	VALUES (0, now(), 0, 0, 0, '', (SELECT USER()), 0, 'UPGRADE', '3.2.1a1part3', 'STARTED', 1);

UPDATE `SYSTEMCONFIG` SET `webversiondate` = '2012-06-06';
UPDATE `SYSTEMCONFIG` SET `webversionnumber` = '3.2.1.1';
UPDATE `SYSTEMCONFIG` SET `webversionstring`= '3.2.1a1';

UPDATE `COUNTRIES`
	SET `displayfields`    = 'country<p>company<p>firstname<p>lastname<p>add1=[add41], [add42] - [add43]<p>add2<p>add3<p>postcode<p>city<p>state', 
		`fieldlabels`      = 'str_LabelCountry,str_LabelCompanyName,str_LabelFirstName,str_LabelLastName,str_LabelAddressLine1,str_LabelHouseNumber,str_LabelAddressLine2,str_LabelAddressLine3,str_LabelCnpjCpf,str_LabelZIPCode,str_LabelTownCity,str_LabelState', 
		`compulsoryfields` = 'country,firstname,lastname,add41,add42,postcode,city,state', 
		`displayformat`    = '[company]<br>[firstname] [lastname]<br>[add1]<br>[add2]<br>[postcode] [city] [state]<br>[country]',
		`region`           = 'STATE'
	WHERE `isocode2`='BR';

INSERT INTO `ACTIVITYLOG`
	(`id`, `datecreated`, `sessionid`, `orderid`, `userid`, `userlogin`, `username`, `importance`, `sectioncode`, `actioncode`, `actionnotes`, `success`)
	VALUES (0, now(), 0, 0, 0, '', (SELECT USER()), 0, 'UPGRADE', '3.2.1a1part3', 'FINISHED', 1);

#
# DDL END
#

SET FOREIGN_KEY_CHECKS = 1;
