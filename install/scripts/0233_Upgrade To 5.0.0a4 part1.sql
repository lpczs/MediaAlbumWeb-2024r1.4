#
SET FOREIGN_KEY_CHECKS = 0;

#
# DDL START
#

INSERT INTO `ACTIVITYLOG`
	(`id`, `datecreated`, `sessionid`, `orderid`, `userid`, `userlogin`, `username`, `importance`, `sectioncode`, `actioncode`, `actionnotes`, `success`)
	VALUES (0, now(), 0, 0, 0, '', (SELECT USER()), 0, 'UPGRADE', '5.0.0a4 part1', 'STARTED', 1);

UPDATE `SYSTEMCONFIG` SET `webversiondate` = '2014-06-11';
UPDATE `SYSTEMCONFIG` SET `webversionnumber` = '5.0.0.4';
UPDATE `SYSTEMCONFIG` SET `webversionstring`= '5.0.0a4';

UPDATE `COUNTRIES` SET `displayfields` = 'country<p>firstname<p>lastname<p>company<p>add1<p>add2<p>add4<p>city<p>county<p>postcode',
`compulsoryfields` = 'country,firstname,lastname,add1,add4,city,county,postcode',
`displayformat` = '[firstname] [lastname]<br>[company]<br>[add1]<br>[add2]<br>[add4]<br>[city]<br>[postcode] [county]<br>[country]',
`fieldlabels` = 'str_LabelCountry,str_LabelFirstName,str_LabelLastName,str_LabelCompanyName,str_LabelAddressLine1,str_LabelAddressLine2,str_LabelAddressLine4,str_LabelTownCity,str_LabelCounty,str_LabelPostalCode'
WHERE `COUNTRIES`.`isocode2` = 'IT';

UPDATE `USERS` SET `addressupdated` = '0' WHERE `USERS`.`countrycode` = 'IT';

INSERT INTO `ACTIVITYLOG`
(`id`, `datecreated`, `sessionid`, `orderid`, `userid`, `userlogin`, `username`, `importance`, `sectioncode`, `actioncode`, `actionnotes`, `success`)
VALUES (0, now(), 0, 0, 0, '', (SELECT USER()), 0, 'UPGRADE', '5.0.0a4 part1', 'FINISHED', 1);

#
# DDL END
#

SET FOREIGN_KEY_CHECKS = 1;
