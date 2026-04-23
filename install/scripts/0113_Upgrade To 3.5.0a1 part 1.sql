#
SET FOREIGN_KEY_CHECKS = 0;

#
# DDL START
#

INSERT INTO `ACTIVITYLOG`
	(`id`, `datecreated`, `sessionid`, `orderid`, `userid`, `userlogin`, `username`, `importance`, `sectioncode`, `actioncode`, `actionnotes`, `success`)
	VALUES (0, now(), 0, 0, 0, '', (SELECT USER()), 0, 'UPGRADE', '3.5.0a1 part1', 'STARTED', 1);

UPDATE `SYSTEMCONFIG` SET `webversiondate` = '2012-11-15';
UPDATE `SYSTEMCONFIG` SET `webversionnumber` = '3.5.0.1';
UPDATE `SYSTEMCONFIG` SET `webversionstring`= '3.5.0a1';

INSERT INTO `TASKS` (`datecreated`, `taskcode`, `taskname`, `intervaltype`, `intervalvalue`, `lastruntime`, `nextruntime`, `statuscode`, `statusmessage`, `runstatus`, `maxruncount`, `internal`, `scriptfilename`, `deleteexpiredinterval`, `active`) VALUES
	(now(), 'TAOPIX_UPLOADREMINDER','en Upload Reminder', 2, '00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0,'', 0, 10, 1, 'uploadReminder.php', 10, 0);
	
UPDATE `COUNTRIES` SET `displayfields` = 'country<p>firstname<p>lastname<p>company<p>add1<p>add2<p>city<p>postcode',
`compulsoryfields` = 'country,firstname,lastname,add1,city,postcode',
`displayformat` = '[firstname] [lastname]<br>[company]<br>[add1]<br>[add2]<br>[city] [postcode]<br>[country]',
`fieldlabels` = 'str_LabelCountry,str_LabelFirstName,str_LabelLastName,str_LabelCompanyName,str_LabelAddressLine1,str_LabelAddressLine2,str_LabelTownCity,str_LabelPostalCode'
WHERE `COUNTRIES`.`isocode2` = 'SE';

UPDATE `USERS` SET `addressupdated` = '0' WHERE `USERS`.`countrycode` = 'SE';

UPDATE `COUNTRIES` SET `displayformat` = '[firstname] [lastname]<br>[company]<br>[add1]<br>[add2]<br>[city] [postcode]<br>[state]<br>[country]'
WHERE `COUNTRIES`.`isocode2` = 'ES';

INSERT INTO `TRIGGERS` (`datecreated` ,`eventcode` ,`language` ,`exportformat` ,`includepaymentdata` ,`beautifiedxml` ,`subfolderformat` ,`filenameformat` ,`active`) VALUES (now(),'ORDERSAVEDTODISK' ,    'Default' , 'XML' , 0 , 1 , '' , '' , 0);

ALTER TABLE `APPLICATIONFILES` MODIFY COLUMN `PRODUCTS` VARCHAR(2048) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;

UPDATE `ORDERHEADER` SET `giftcardamount`= 0.00 WHERE `giftcarddeleted`=1;

ALTER TABLE `ORDERHEADER` DROP COLUMN `giftcarddeleted`;

INSERT INTO `TRIGGERS` (`datecreated` ,`eventcode` ,`language` ,`exportformat` ,`includepaymentdata` ,`beautifiedxml` ,`subfolderformat` ,`filenameformat` ,`active`) VALUES (now(),'IMPORTEDFILES' , 'Default' , 'XML' , 0 , 1 , '' , '' , 0);

INSERT INTO `TRIGGERS` (`datecreated` ,`eventcode` ,`language` ,`exportformat` ,`includepaymentdata` ,`beautifiedxml` ,`subfolderformat` ,`filenameformat` ,`active`) VALUES (now(),'IMPORTEDRENDERED' , 'Default' , 'XML' , 0 , 1 , '' , '' , 0);

INSERT INTO `TRIGGERS` (`datecreated` ,`eventcode` ,`language` ,`exportformat` ,`includepaymentdata` ,`beautifiedxml` ,`subfolderformat` ,`filenameformat` ,`active`) VALUES (now(),'IMPORTEDPROJECTELEMENTS' , 'Default' , 'XML' , 0 , 1 , '' , '' , 0);

INSERT INTO `ACTIVITYLOG`
	(`id`, `datecreated`, `sessionid`, `orderid`, `userid`, `userlogin`, `username`, `importance`, `sectioncode`, `actioncode`, `actionnotes`, `success`)
	VALUES (0, now(), 0, 0, 0, '', (SELECT USER()), 0, 'UPGRADE', '3.5.0a1 part1', 'FINISHED', 1);

#
# DDL END
#

SET FOREIGN_KEY_CHECKS = 1;
