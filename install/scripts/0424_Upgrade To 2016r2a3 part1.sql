#
SET FOREIGN_KEY_CHECKS = 0;

#
# DDL START
#

INSERT INTO `ACTIVITYLOG`
	(`id`, `datecreated`, `sessionid`, `orderid`, `userid`, `userlogin`, `username`, `importance`, `sectioncode`, `actioncode`, `actionnotes`, `success`)
	VALUES (0, now(), 0, 0, 0, '', (SELECT USER()), 0, 'UPGRADE', '2016r2a3', 'STARTED', 1);

UPDATE `SYSTEMCONFIG` SET `webversiondate` = '2016-03-02';
UPDATE `SYSTEMCONFIG` SET `webversionnumber` = '2016.2.0.3';
UPDATE `SYSTEMCONFIG` SET `webversionstring`= '2016r2a3';

#
# NETHERLANDS ADDRESS FORMAT UPDATE
#

UPDATE `COUNTRIES` SET 
	`displayfields` = 'country<p>firstname<p>lastname<p>company<p>add1<p>city<p>postcode',
	`compulsoryfields` = 'firstname,lastname,add1,city,postcode',
	`displayformat` = '[country]<br>[firstname] [lastname]<br>[company]<br>[add1]<br>[city]<br>[postcode]',
	`fieldlabels` = 'str_LabelCountry,str_LabelFirstName,str_LabelLastName,str_LabelCompanyName,str_LabelAddressLine1,str_LabelTownCity,str_LabelZIPCode'
WHERE `isocode2` = 'NL';

INSERT INTO `ACTIVITYLOG`
	(`id`, `datecreated`, `sessionid`, `orderid`, `userid`, `userlogin`, `username`, `importance`, `sectioncode`, `actioncode`, `actionnotes`, `success`)
	VALUES (0, now(), 0, 0, 0, '', (SELECT USER()), 0, 'UPGRADE', '2016r2a3', 'FINISHED', 1);

#
# DDL END
#

SET FOREIGN_KEY_CHECKS = 1;