#
SET FOREIGN_KEY_CHECKS = 0;

#
# DDL START
#

INSERT INTO `ACTIVITYLOG`
	(`id`, `datecreated`, `sessionid`, `orderid`, `userid`, `userlogin`, `username`, `importance`, `sectioncode`, `actioncode`, `actionnotes`, `success`)
	VALUES (0, now(), 0, 0, 0, '', (SELECT USER()), 0, 'UPGRADE', '3.3.0a1 part3', 'STARTED', 1);

UPDATE `SYSTEMCONFIG` SET `webversiondate` = '2012-08-08';
UPDATE `SYSTEMCONFIG` SET `webversionnumber` = '3.3.0.1';
UPDATE `SYSTEMCONFIG` SET `webversionstring`= '3.3.0a1';

INSERT INTO `COUNTRYREGION` ( `datecreated`, `countrycode`, `regioncode`, `regionname`, `regiongroupcode`)
VALUES (now(), 'ES', 'AN', 'es Andalucía', ''),
(now(), 'ES', 'AR', 'es Aragón', ''), 
(now(), 'ES', 'AS', 'es Principado de Asturias', ''),
(now(), 'ES', 'CN', 'es Canarias', ''),
(now(), 'ES', 'CB', 'es Cantabria', ''),
(now(), 'ES', 'CM', 'es Castilla-La Mancha', ''),
(now(), 'ES', 'CL', 'es Castilla y León', ''),
(now(), 'ES', 'CT', 'es Cataluña', ''),
(now(), 'ES', 'EX', 'es Extremadura', ''),
(now(), 'ES', 'GA', 'es Galicia', ''),
(now(), 'ES', 'IB', 'es Islas Baleares', ''),
(now(), 'ES', 'RI', 'es La Rioja', ''),
(now(), 'ES', 'MD', 'es Madrid, Comunidad de', ''),
(now(), 'ES', 'MC', 'es Murcia, Región de', ''),
(now(), 'ES', 'NC', 'es Navarra, Comunidad Foral de', ''),
(now(), 'ES', 'PV', 'es País Vasco', ''),
(now(), 'ES', 'VC', 'es Valenciana, Comunidad', ''),
(now(), 'ES', 'CE', 'es Ceuta', ''),
(now(), 'ES', 'ML', 'es Melilla', '');

UPDATE `COUNTRIES` SET `displayfields` = 'country<p>firstname<p>lastname<p>company<p>add1<p>add2<p>city<p>state<p>postcode',
`compulsoryfields` = 'country,firstname,lastname,add1,city,state,postcode',
`displayformat` = '[firstname] [lastname]<br>[company]<br>[add1]<br>[add2]<br>[city] [postcode]<br>[country]',
`fieldlabels` = 'str_LabelCountry,str_LabelFirstName,str_LabelLastName,str_LabelCompanyName,str_LabelAddressLine1,str_LabelAddressLine2,str_LabelTownCity,str_LabelState,str_LabelPostalCode'
WHERE `COUNTRIES`.`isocode2` = 'ES';

UPDATE `USERS` SET `addressupdated` = '0' WHERE `USERS`.`countrycode` = 'ES';

UPDATE `COUNTRIES` SET `displayfields` = 'country<p>firstname<p>lastname<p>company<p>add1<p>add2<p>city<p>postcode',
`compulsoryfields` = 'country,firstname,lastname,add1,city,state,postcode',
`displayformat` = '[firstname] [lastname]<br>[company]<br>[add1]<br>[add2]<br>[city] [postcode]<br>[country]',
`fieldlabels` = 'str_LabelCountry,str_LabelFirstName,str_LabelLastName,str_LabelCompanyName,str_LabelAddressLine1,str_LabelAddressLine2,str_LabelTownCity,str_LabelPostalCode'
WHERE `COUNTRIES`.`isocode2` = 'KR';

UPDATE `USERS` SET `addressupdated` = '0' WHERE `USERS`.`countrycode` = 'KR';

UPDATE `COUNTRIES` SET `displayfields` = 'country<p>firstname<p>lastname<p>company<p>add1<p>add2<p>add3<p>add4<p>city<p>county<p>state<p>postcode',
`compulsoryfields` = 'country,firstname,lastname,add1,city,state,postcode',
`displayformat` = '[firstname] [lastname]<br>[company]<br>[add1]<br>[add2]<br>[add3]<br>[add4]<br>[city] [postcode]<br>[county]<br>[state]<br>[country]',
`fieldlabels` = 'str_LabelCountry,str_LabelFirstName,str_LabelLastName,str_LabelCompanyName,str_LabelAddressLine1,str_LabelAddressLine2,str_LabelAddressLine3,str_LabelAddressLine4,str_LabelTownCity,str_LabelCounty,str_LabelProvince,str_LabelPostalCode'
WHERE `COUNTRIES`.`isocode2` = 'ID';

INSERT INTO `COUNTRYREGION` (`datecreated`, `countrycode`, `regioncode`, `regionname`, `regiongroupcode`)
VALUES(now(), 'ID','BA', 'en Bali<p>id Bali', ''),
(now(), 'ID','BB', 'en Bangka-Belitung<p>id Bangka-Belitung ', ''),
(now(), 'ID','BT', 'en Banten<p>id Banten', ''),
(now(), 'ID','BE', 'en Bengkulu<p>id Bengkulu', ''),
(now(), 'ID','JT', 'en Central Java<p>id Jawa Tengah', ''),
(now(), 'ID','KT', 'en Central Kalimantan<p>id Kalimantan Tengah ', ''),
(now(), 'ID','ST', 'en Central Sulawesi<p>id Seulawesi Tengah', ''),
(now(), 'ID','JI', 'en East Java<p>id Java Timur', ''),
(now(), 'ID','KI', 'en East Kalimantan<p>id Kalimantan Timur', ''),
(now(), 'ID','NT', 'en East Nusa Tenggara<p>id Nusa Tenggara Timur', ''),
(now(), 'ID','GO', 'en Gorontalo<p>id Gorontalo ', ''),
(now(), 'ID','JK', 'en Jakarta<p>id Jakarta', ''),
(now(), 'ID','JA', 'en Jambi<p>id Jambi', ''),
(now(), 'ID','LA', 'en Lampung<p>id Lampung', ''),
(now(), 'ID','MA', 'en Maluku<p>id Maluku', ''),
(now(), 'ID','AC', 'en Aceh Nanggroe Aceh Darussalam<p>id Nanggroe Aceh Darussalam', ''),
(now(), 'ID','MU', 'en North Maluku<p>id Maluku Utara', ''),
(now(), 'ID','SA', 'en North Sulawesi<p>id Sulawesi Utara', ''),
(now(), 'ID','SU', 'en North Sumatra<p>id Sumatra Utara', ''),
(now(), 'ID','PA', 'en Papua<p>id Papua', ''),
(now(), 'ID','RI', 'en Riau<p>id Riau', ''),
(now(), 'ID','KR', 'en Riau Islands<p>id Kepulawan Riau', ''),
(now(), 'ID','SG', 'en South East Sulawesi<p>id Sulawesi Tenggara', ''),
(now(), 'ID','KS', 'en South Kalimantan<p>id Kalimantan Selatan', ''),
(now(), 'ID','SN', 'en South Sulawesi<p>id Sulawesi Selatan', ''),
(now(), 'ID','SS', 'en South Sumatra<p>id Sumatra Selatan', ''),
(now(), 'ID','JB', 'en West Java<p>id Jawa Barat', ''),
(now(), 'ID','KB', 'en West Kalimantan<p>id Kalimantan Barat', ''),
(now(), 'ID','NB', 'en West Nusa Tenggara<p>id Nusa Tenggara Barat', ''),
(now(), 'ID','PB', 'en West Papua<p>id Papua Barat', ''),
(now(), 'ID','SR', 'en West Sulawesi<p>id Sulawesi Barat', ''),
(now(), 'ID','SB', 'en West Sumatra<p>id Sumatra Barat', ''),
(now(), 'ID','YO', 'en Yogyakarta Special Region<p>id D.I Yogyakarta', '');

UPDATE `USERS` SET `addressupdated` = '0' WHERE `USERS`.`countrycode` = 'ID';

UPDATE `COUNTRIES` SET `displayfields` = 'country<p>firstname<p>lastname<p>company<p>add1<p>add2<p>add3<p>add4<p>city<p>state<p>postcode',
`compulsoryfields` = 'country,firstname,lastname,add1,city,state,postcode',
`displayformat` = '[firstname] [lastname]<br>[company]<br>[add1]<br>[add2]<br>[add3]<br>[add4]<br>[city] [postcode]<br>[state]<br>[country]',
`fieldlabels` = 'str_LabelCountry,str_LabelFirstName,str_LabelLastName,str_LabelCompanyName,str_LabelAddressLine1,str_LabelAddressLine2,str_LabelAddressLine3,str_LabelAddressLine4,str_LabelTownCity,str_LabelState,str_LabelPostalCode'
WHERE `COUNTRIES`.`isocode2` = 'IN';

INSERT INTO `COUNTRYREGION` (`datecreated`, `countrycode`, `regioncode`, `regionname`, `regiongroupcode`)
VALUES(now(), 'IN','AP','en Andhra Pradesh', ''),
(now(), 'IN','AR','en Arunachal Pradesh', ''),
(now(), 'IN','AS','en Assam', ''),
(now(), 'IN','BR','en Bihar', ''),
(now(), 'IN','CT','en Chhattisgarh', ''),
(now(), 'IN','GA','en Goa', ''),
(now(), 'IN','GJ','en Gujarat', ''),
(now(), 'IN','HR','en Haryana', ''),
(now(), 'IN','HP','en Himachal Pradesh', ''),
(now(), 'IN','JK','en Jammu and Kashmir', ''),
(now(), 'IN','JH','en Jharkhand', ''),
(now(), 'IN','KA','en Karnataka', ''),
(now(), 'IN','KL','en Kerala', ''),
(now(), 'IN','MP','en Madhya Pradesh', ''),
(now(), 'IN','MH','en Maharashtra', ''),
(now(), 'IN','MN','en Manipur', ''),
(now(), 'IN','ML','en Meghalaya', ''),
(now(), 'IN','MZ','en Mizoram', ''),
(now(), 'IN','NL','en Nagaland', ''),
(now(), 'IN','OR','en Orissa', ''),
(now(), 'IN','PB','en Punjab', ''),
(now(), 'IN','RJ','en Rajasthan', ''),
(now(), 'IN','SK','en Sikkim', ''),
(now(), 'IN','TN','en Tamil Nadu', ''),
(now(), 'IN','TR','en Tripura', ''),
(now(), 'IN','UT','en Uttarakhand', ''),
(now(), 'IN','UP','en Uttar Pradesh', ''),
(now(), 'IN','WB','en West Bengal', ''),
(now(), 'IN','AN','en Andaman and Nicobar Islands Union territory', ''),
(now(), 'IN','CH','en Chandigarh Union territory', ''),
(now(), 'IN','DN','en Dadra and Nagar Haveli Union territory', ''),
(now(), 'IN','DD','en Daman and Diu Union territory', ''),
(now(), 'IN','DL','en Delhi Union territory', ''),
(now(), 'IN','LD','en Lakshadweep Union territory', ''),
(now(), 'IN','PY','en Pondicherry', '');

UPDATE `USERS` SET `addressupdated` = '0' WHERE `USERS`.`countrycode` = 'IN';

DELETE FROM `COUNTRYREGION` 
WHERE `countrycode` = 'GB' 
AND `regiongroupcode` = 'WA';

INSERT INTO `COUNTRYREGION` ( `datecreated`, `countrycode`, `regioncode`, `regionname`, `regiongroupcode`)
VALUES (now(), 'GB', 'ANGLESEY', 'en Anglesey', 'WA'),
(now(), 'GB', 'BLAENAUGWENT', 'en Blaenau Gwent', 'WA'),
(now(), 'GB', 'BRECKNOCKSHIRE', 'en Brecknockshire', 'WA'),
(now(), 'GB', 'BRIDGEND', 'en Bridgend', 'WA'),
(now(), 'GB', 'CAERNARFONSHIRE', 'en Caernarfonshire', 'WA'),
(now(), 'GB', 'CAERPHILLY', 'en Caerphilly', 'WA'),
(now(), 'GB', 'CARDIFF', 'en Cardiff', 'WA'),
(now(), 'GB', 'CARDIGANSHIRE', 'en Cardiganshire', 'WA'),
(now(), 'GB', 'CARMARTHENSHIRE', 'en Carmarthenshire', 'WA'),
(now(), 'GB', 'CEREDIGNON', 'en Ceredigion', 'WA'),
(now(), 'GB', 'CONWY', 'en Conwy', 'WA'),
(now(), 'GB', 'DENBIGHSHIRE', 'en Denbighshire', 'WA'),
(now(), 'GB', 'FLINTSHIRE', 'en Flintshire', 'WA'),
(now(), 'GB', 'GLAMORGAN', 'en Glamorgan', 'WA'),
(now(), 'GB', 'GWYNEDD', 'en Gwynedd', 'WA'),
(now(), 'GB', 'ISLEOFANGLESEY', 'en Isle of Anglesey', 'WA'),
(now(), 'GB', 'MERIONETH', 'en Merioneth', 'WA'),
(now(), 'GB', 'MERTHYRTYDFIL', 'en Merthyr Tydfil', 'WA'),
(now(), 'GB', 'MONMOUTHSHIRE', 'en Monmouthshire', 'WA'),
(now(), 'GB', 'MONTGOMERYSHIRE', 'en Montgomeryshire', 'WA'),
(now(), 'GB', 'NEATHPORTTALBOT', 'en Neath Port Talbot', 'WA'),
(now(), 'GB', 'NEWPORT', 'en Newport', 'WA'),
(now(), 'GB', 'PEMBROKESHIRE', 'en Pembrokeshire', 'WA'),
(now(), 'GB', 'POWYS', 'en Powys', 'WA'),
(now(), 'GB', 'RADNORSHIRE', 'en Radnorshire', 'WA'),
(now(), 'GB', 'RHONDDACYNONTAFF', 'en Rhondda Cynon Taff', 'WA'),
(now(), 'GB', 'SWANSEA', 'en Swansea', 'WA'),
(now(), 'GB', 'TORFAEN', 'en Torfaen', 'WA'),
(now(), 'GB', 'VALEOFGLAMORGAN', 'en Vale of Glamorgan', 'WA'),
(now(), 'GB', 'WREXHAM', 'en Wrexham', 'WA');


INSERT INTO `COUNTRYREGION` ( `datecreated`, `countrycode`, `regioncode`, `regionname`, `regiongroupcode`)
VALUES (now(), 'GB', 'CUMBRIA', 'en Cumbria', 'EN');

INSERT INTO `COUNTRYREGIONGROUP` (`datecreated`, `countrycode`, `sortorder`, `regiongroupcode`, `regiongroupname`) 
VALUES (now(), 'GB', '0005', 'IM', 'en Isle of Man');

INSERT INTO `COUNTRYREGION` ( `datecreated`, `countrycode`, `regioncode`, `regionname`, `regiongroupcode`)
VALUES (now(), 'GB', 'ISLEOFMAN', 'en Isle of Man', 'IM');

INSERT INTO `COUNTRYREGIONGROUP` (`datecreated`, `countrycode`, `sortorder`, `regiongroupcode`, `regiongroupname`) 
VALUES (now(), 'GB', '0006', 'CI', 'en Channel Islands');

INSERT INTO `COUNTRYREGION` ( `datecreated`, `countrycode`, `regioncode`, `regionname`, `regiongroupcode`)
VALUES (now(), 'GB', 'ALDERNEY', 'en Alderney', 'CI'),
(now(), 'GB', 'GUERNSEY', 'en Guernsey', 'CI'),
(now(), 'GB', 'HERM', 'en Herm', 'CI'),
(now(), 'GB', 'JERSEY', 'en Jersey', 'CI'),
(now(), 'GB', 'SARK', 'en Sark', 'CI');


INSERT INTO `ACTIVITYLOG`
	(`id`, `datecreated`, `sessionid`, `orderid`, `userid`, `userlogin`, `username`, `importance`, `sectioncode`, `actioncode`, `actionnotes`, `success`)
	VALUES (0, now(), 0, 0, 0, '', (SELECT USER()), 0, 'UPGRADE', '3.3.0a1 part3', 'FINISHED', 1);

#
# DDL END
#

SET FOREIGN_KEY_CHECKS = 1;
