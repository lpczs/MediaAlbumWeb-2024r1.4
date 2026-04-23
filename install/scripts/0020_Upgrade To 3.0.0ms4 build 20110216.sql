#
SET FOREIGN_KEY_CHECKS = 0;

#
# DDL START
#



INSERT INTO `ACTIVITYLOG`
	(`id`, `datecreated`, `sessionid`, `orderid`, `userid`, `userlogin`, `username`, `importance`, `sectioncode`, `actioncode`, `actionnotes`, `success`)
	VALUES (0, now(), 0, 0, 0, '', (SELECT USER()), 0, 'UPGRADE', '3.0.0ms4 build 20110207', 'STARTED', 1);


UPDATE `COUNTRIES`
    SET `displayfields`    = 'country<p>company<p>lastname<p>firstname<p>postcode<p>state<p>city<p>add1<p>add2<p>add3<p>add4',
        `fieldlabels`      = 'str_LabelCountry,str_LabelCompanyName,str_LabelLastName,str_LabelFirstName,str_LabelZIPCode,str_LabelState,str_LabelTownCity,str_LabelAddressLine1,str_LabelAddressLine2,str_LabelAddressLine3,str_LabelAddressLine4',
        `compulsoryfields` = 'country,lastname,firstname,postcode,state,city,add1',
        `displayformat`    = '[company]<br>[lastname] [firstname]<br>[postcode]<br>[state]<br>[city]<br>[add1]<br>[add2]<br>[add3]<br>[add4]<br>[country]',
        `region`           = 'STATE'
   WHERE `isocode2`='JP';


INSERT INTO `ACTIVITYLOG`
	(`id`, `datecreated`, `sessionid`, `orderid`, `userid`, `userlogin`, `username`, `importance`, `sectioncode`, `actioncode`, `actionnotes`, `success`)
	VALUES (0, now(), 0, 0, 0, '', (SELECT USER()), 0, 'UPGRADE', '3.0.0ms4 build 20110216', 'FINISHED', 1);

#
# DDL END
#

SET FOREIGN_KEY_CHECKS = 1;
