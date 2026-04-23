#
SET FOREIGN_KEY_CHECKS = 0;

#
# DDL START
#

INSERT INTO `ACTIVITYLOG`
	(`id`, `datecreated`, `sessionid`, `orderid`, `userid`, `userlogin`, `username`, `importance`, `sectioncode`, `actioncode`, `actionnotes`, `success`)
	VALUES (0, now(), 0, 0, 0, '', (SELECT USER()), 0, 'UPGRADE', '3.3.0a3 part1', 'STARTED', 1);

UPDATE `SYSTEMCONFIG` SET `webversiondate` = '2012-08-21';
UPDATE `SYSTEMCONFIG` SET `webversionnumber` = '3.3.0.3';
UPDATE `SYSTEMCONFIG` SET `webversionstring`= '3.3.0a3';

INSERT INTO `COUNTRYREGION` ( `datecreated`, `countrycode`, `regioncode`, `regionname`, `regiongroupcode`)
VALUES (now(), 'PH', 'ABR', 'ph Abra', ''),
 (now(), 'PH', 'AGN', 'ph Agusan del Norte', ''),
 (now(), 'PH', 'AGS', 'ph Agusan del Sur', ''),
 (now(), 'PH', 'AKL', 'ph Aklan', ''),
 (now(), 'PH', 'ALB', 'ph Albay', ''),
 (now(), 'PH', 'ANT', 'ph Antique', ''),
 (now(), 'PH', 'APA', 'ph Apayao', ''),
 (now(), 'PH', 'AUR', 'ph Aurora', ''),
 (now(), 'PH', 'BAS', 'ph Basilan', ''),
 (now(), 'PH', 'BAN', 'ph Bataan', ''),
 (now(), 'PH', 'BTN', 'ph Batanes', ''),
 (now(), 'PH', 'BTG', 'ph Batangas', ''),
 (now(), 'PH', 'BEN', 'ph Benguet', ''),
 (now(), 'PH', 'BIL', 'ph Biliran', ''),
 (now(), 'PH', 'BOH', 'ph Bohol', ''),
 (now(), 'PH', 'BUK', 'ph Bukidnon', ''),
 (now(), 'PH', 'BUL', 'ph Bulacan', ''),
 (now(), 'PH', 'CAG', 'ph Cagayan', ''),
 (now(), 'PH', 'CAN', 'ph Camarines Norte', ''),
 (now(), 'PH', 'CAS', 'ph Camarines Sur', ''),
 (now(), 'PH', 'CAM', 'ph Camiguin', ''),
 (now(), 'PH', 'CAP', 'ph Capiz', ''),
 (now(), 'PH', 'CAT', 'ph Catanduanes', ''),
 (now(), 'PH', 'CAV', 'ph Cavite', ''),
 (now(), 'PH', 'CEB', 'ph Cebu', ''),
 (now(), 'PH', 'COM', 'ph Compostela Valley', ''),
 (now(), 'PH', 'NCO', 'ph Cotabato', ''),
 (now(), 'PH', 'DAV', 'ph Davao del Norte', ''),
 (now(), 'PH', 'DAS', 'ph Davao del Sur', ''),
 (now(), 'PH', 'DAO', 'ph Davao Oriental', ''),
 (now(), 'PH', 'DIN', 'ph Dinagat Islands', ''),
 (now(), 'PH', 'EAS', 'ph Eastern Samar', ''),
 (now(), 'PH', 'GUI', 'ph Guimaras', ''),
 (now(), 'PH', 'IFU', 'ph Ifugao', ''),
 (now(), 'PH', 'ILN', 'ph Ilocos Norte', ''),
 (now(), 'PH', 'ILS', 'ph Ilocos Sur', ''),
 (now(), 'PH', 'ILI', 'ph Iloilo', ''),
 (now(), 'PH', 'ISA', 'ph Isabela', ''),
 (now(), 'PH', 'KAL', 'ph Kalinga', ''),
 (now(), 'PH', 'LUN', 'ph La Union', ''),
 (now(), 'PH', 'LAG', 'ph Laguna', ''),
 (now(), 'PH', 'LAN', 'ph Lanao del Norte', ''),
 (now(), 'PH', 'LAS', 'ph Lanao del Sur', ''),
 (now(), 'PH', 'LEY', 'ph Leyte', ''),
 (now(), 'PH', 'MAG', 'ph Maguindanao', ''),
 (now(), 'PH', 'MAD', 'ph Marinduque', ''),
 (now(), 'PH', 'MAS', 'ph Masbate', ''),
 (now(), 'PH', 'MSC', 'ph Misamis Occidental', ''),
 (now(), 'PH', 'MSR', 'ph Misamis Oriental', ''),
 (now(), 'PH', 'MOU', 'ph Mountain Province', ''),
 (now(), 'PH', 'NEC', 'ph Negros Occidental', ''),
 (now(), 'PH', 'NER', 'ph Negros Oriental', ''),
 (now(), 'PH', 'NSA', 'ph Northern Samar', ''),
 (now(), 'PH', 'NUE', 'ph Nueva Ecija', ''),
 (now(), 'PH', 'NUV', 'ph Nueva Vizcaya', ''),
 (now(), 'PH', 'MDC', 'ph Occidental Mindoro', ''),
 (now(), 'PH', 'MDR', 'ph Oriental Mindoro', ''),
 (now(), 'PH', 'PLW', 'ph Palawan', ''),
 (now(), 'PH', 'PAM', 'ph Pampanga', ''),
 (now(), 'PH', 'PAN', 'ph Pangasinan', ''),
 (now(), 'PH', 'QUE', 'ph Quezon', ''),
 (now(), 'PH', 'QUI', 'ph Quirino', ''),
 (now(), 'PH', 'RIZ', 'ph Rizal', ''),
 (now(), 'PH', 'ROM', 'ph Romblon', ''),
 (now(), 'PH', 'WSA', 'ph Samar', ''),
 (now(), 'PH', 'SAR', 'ph Sarangani', ''),
 (now(), 'PH', 'SIG', 'ph Siquijor', ''),
 (now(), 'PH', 'SOR', 'ph Sorsogon', ''),
 (now(), 'PH', 'SCO', 'ph South Cotabato', ''),
 (now(), 'PH', 'SLE', 'ph Southern Leyte', ''),
 (now(), 'PH', 'SUK', 'ph Sultan Kudarat', ''),
 (now(), 'PH', 'SLU', 'ph Sulu', ''),
 (now(), 'PH', 'SUN', 'ph Surigao del Norte', ''),
 (now(), 'PH', 'SUR', 'ph Surigao del Sur', ''),
 (now(), 'PH', 'TAR', 'ph Tarlac', ''),
 (now(), 'PH', 'TAW', 'ph Tawi-Tawi', ''),
 (now(), 'PH', 'ZMB', 'ph Zambales', ''),
 (now(), 'PH', 'ZAN', 'ph Zamboanga del Norte', ''),
 (now(), 'PH', 'ZAS', 'ph Zamboanga del Sur', ''),
 (now(), 'PH', 'ZSI', 'ph Zamboanga Sibugay', ''),
 (now(), 'PH', 'NCR', 'ph Metro Manila', '');


UPDATE `COUNTRIES` SET `displayfields` = 'country<p>firstname<p>lastname<p>company<p>add1<p>add2<p>city<p>state<p>postcode',
`compulsoryfields` = 'country,firstname,lastname,add1,state,postcode',
`displayformat` = '[firstname] [lastname]<br>[company]<br>[add1]<br>[add2]<br>[city]<br>[postcode] [state]<br>[country]',
`fieldlabels` = 'str_LabelCountry,str_LabelFirstName,str_LabelLastName,str_LabelCompanyName,str_LabelAddressLine1,str_LabelAddressLine2,str_LabelTownCity,str_LabelProvince,str_LabelPostalCode'
WHERE `COUNTRIES`.`isocode2` = 'PH';


UPDATE `USERS` SET `addressupdated` = '0' WHERE `USERS`.`countrycode` = 'PH';


INSERT INTO `ACTIVITYLOG`
	(`id`, `datecreated`, `sessionid`, `orderid`, `userid`, `userlogin`, `username`, `importance`, `sectioncode`, `actioncode`, `actionnotes`, `success`)
	VALUES (0, now(), 0, 0, 0, '', (SELECT USER()), 0, 'UPGRADE', '3.3.0a3 part1', 'FINISHED', 1);

#
# DDL END
#

SET FOREIGN_KEY_CHECKS = 1;
