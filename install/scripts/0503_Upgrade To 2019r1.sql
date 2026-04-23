#
SET FOREIGN_KEY_CHECKS = 0;

#
# DDL START
#

UPDATE `SYSTEMCONFIG` SET `webversiondate` = '2019-04-03';
UPDATE `SYSTEMCONFIG` SET `webversionnumber` = '2019.1.0';
UPDATE `SYSTEMCONFIG` SET `webversionstring`= '2019r1';


UPDATE `DATAPOLICIES` SET `guestage` = `guestage` + `guestdays`;

ALTER TABLE `DATAPOLICIES`
ADD COLUMN `notorderedarchiveactive` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0 AFTER `notorderedemailfrequency`,
ADD COLUMN `notorderedarchivedays` SMALLINT(5) UNSIGNED NOT NULL DEFAULT 45 AFTER `notorderedarchiveactive`,
ADD COLUMN `orderedarchiveactive` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0 AFTER `orderedemailfrequency`,
ADD COLUMN `orderedarchivedays` SMALLINT(5) UNSIGNED NOT NULL DEFAULT 45 AFTER `orderedarchiveactive`,
ADD COLUMN `active` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0 AFTER `unusedassetsage`,
DROP COLUMN `guestdays`;

UPDATE `DATAPOLICIES` SET `active` = 1;

INSERT INTO `TASKS` (`datecreated`, `taskcode`, `taskname`, `intervaltype`, `intervalvalue`, `lastruntime`, `nextruntime`, `statuscode`, `statusmessage`, `runstatus`, `maxruncount`, `internal`, `scriptfilename`, `deleteexpiredinterval`, `active`) VALUES
(now(),'TAOPIX_ONLINEARCHIVETASK','en Online Archive Task',2,'03:00','0000-00-00 00:00:00','0000-00-00 00:00:00',0,'',0,10,1,'onlineArchiveTask.php',10,0);


INSERT INTO `TRIGGERS`
(`datecreated` ,`eventcode` ,`language` ,`exportformat` ,`includepaymentdata` ,`beautifiedxml` ,`subfolderformat` ,`filenameformat` ,`active`)
VALUES
(now(),'ONHOLDUPDATED' , 'Default' , 'XML' , 0 , 1 , '' , '' , 0);


ALTER TABLE `BRANDING` ADD COLUMN `googletagmanageronlinecode` VARCHAR(20) NOT NULL AFTER `googleanalyticsuseridtracking`;


ALTER TABLE `USERSBLOCKEDIPADDRESSLIST`
ADD COLUMN `blockreason` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0 AFTER `blockcount`;

ALTER TABLE `USERS`
ADD COLUMN `blockreason` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0 AFTER `loginattemptcount`;


ALTER TABLE `PRODUCTS`
ADD COLUMN `minimumprintsperproject` INT(11) UNSIGNED NOT NULL DEFAULT 1 AFTER `pricetransformationstage`;


UPDATE `COUNTRIES` SET 
`displayfields` = 'country<p>firstname<p>lastname<p>company<p>add1<p>add2<p>add3<p>city<p>state<p>postcode',
`compulsoryfields` = 'country,firstname,lastname,add1,add3,city,state,postcode',
`displayformat` = '[firstname] [lastname]<br>[company]<br>[add1]<br>[add2]<br>[add3]<br>[city]<br>[state]<br>[postcode]<br>[country]',
`fieldlabels` = 'str_LabelCountry,str_LabelFirstName,str_LabelLastName,str_LabelCompanyName,str_LabelAddressLine1,str_LabelAddressLine2,str_LabelSuburb,str_LabelTownCity,str_LabelProvince,str_LabelPostalCode'
WHERE `COUNTRIES`.`isocode2` = 'ZA';


INSERT INTO `COUNTRYREGION` ( `datecreated`, `countrycode`, `regioncode`, `regionname`, `regiongroupcode`)
VALUES
(now(), 'ZA', 'EC', 'en Eastern Cape', ''),
(now(), 'ZA', 'FS', 'en Free State', ''),
(now(), 'ZA', 'GAU', 'en Gauteng', ''),
(now(), 'ZA', 'KZN', 'en KwaZulu-Natal', ''),
(now(), 'ZA', 'LP', 'en Limpopo', ''),
(now(), 'ZA', 'MP', 'en Mpumalanga', ''),
(now(), 'ZA', 'NW', 'en North West', ''),
(now(), 'ZA', 'NC', 'en Northern Cape', ''),
(now(), 'ZA', 'WC', 'en Western Cape', '');


UPDATE `USERS` SET `addressupdated` = '0' WHERE `USERS`.`countrycode` = 'ZA';

#
# DDL END
#

SET FOREIGN_KEY_CHECKS = 1;

