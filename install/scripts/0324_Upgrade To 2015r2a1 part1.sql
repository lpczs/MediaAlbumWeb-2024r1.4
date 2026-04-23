#
SET FOREIGN_KEY_CHECKS = 0;

#
# DDL START
#

INSERT INTO `ACTIVITYLOG`
	(`id`, `datecreated`, `sessionid`, `orderid`, `userid`, `userlogin`, `username`, `importance`, `sectioncode`, `actioncode`, `actionnotes`, `success`)
	VALUES (0, now(), 0, 0, 0, '', (SELECT USER()), 0, 'UPGRADE', '2015r2a1 part1', 'STARTED', 1);

UPDATE `SYSTEMCONFIG` SET `webversiondate` = '2015-03-19';
UPDATE `SYSTEMCONFIG` SET `webversionnumber` = '2015.2.0.1';
UPDATE `SYSTEMCONFIG` SET `webversionstring`= '2015r2a1';

UPDATE `COUNTRIES`
SET `displayfields` = 'country<p>firstname<p>lastname<p>company<p>add1<p>add2<p>add3<p>add4<p>postcode', 
`compulsoryfields` = 'firstname,lastname,add1,postcode,country',
`displayformat` = '[firstname] [lastname]<br>[company]<br>[add1]<br>[add2]<br>[add3]<br>[add4]<br>[postcode]<br>[country]',
`fieldlabels` = 'str_LabelCountry,str_LabelFirstName,str_LabelLastName,str_LabelCompanyName,str_LabelAddressLine1,str_LabelAddressLine2,str_LabelAddressLine3,str_LabelAddressLine4,str_LabelPostalCode'
WHERE `isocode2` = 'SG';

UPDATE `USERS` SET `addressupdated` = 0 WHERE `countrycode` = 'SG';

INSERT INTO `ACTIVITYLOG`
(`id`, `datecreated`, `sessionid`, `orderid`, `userid`, `userlogin`, `username`, `importance`, `sectioncode`, `actioncode`, `actionnotes`, `success`)
VALUES (0, now(), 0, 0, 0, '', (SELECT USER()), 0, 'UPGRADE', '2015r2a1 part1', 'FINISHED', 1);


#
# DDL END
#

SET FOREIGN_KEY_CHECKS = 1;