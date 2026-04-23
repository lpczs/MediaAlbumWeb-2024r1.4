#
SET FOREIGN_KEY_CHECKS = 0;

#
# DDL START
#

INSERT INTO `ACTIVITYLOG`
	(`id`, `datecreated`, `sessionid`, `orderid`, `userid`, `userlogin`, `username`, `importance`, `sectioncode`, `actioncode`, `actionnotes`, `success`)
	VALUES (0, now(), 0, 0, 0, '', (SELECT USER()), 0, 'UPGRADE', '2016r1a22', 'STARTED', 1);

UPDATE `SYSTEMCONFIG` SET `webversiondate` = '2015-12-04';
UPDATE `SYSTEMCONFIG` SET `webversionnumber` = '2016.1.0.22';
UPDATE `SYSTEMCONFIG` SET `webversionstring`= '2016r1a22';

#
# MEXICO ADDRESS FORMAT AND STATES UPDATE
#

UPDATE `COUNTRIES` SET
	`displayfields` = 'firstname<p>lastname<p>company<p>add1<p>add2<p>state<p>country<p>postcode' ,
	`compulsoryfields` = 'firstname,lastname,add1,state,postcode',
	`displayformat` = '[firstname] [lastname]<br>[company]<br>[add1]<br>[add2]<br>[state]<br>[country]<br>[postcode]',
	`fieldlabels` = 'str_LabelFirstName,str_LabelLastName,str_LabelCompanyName,str_LabelAddressLine1,str_LabelAddressLine2,str_LabelState,str_LabelCountry,str_LabelZIPCode'
WHERE `isocode2` = 'MX';

INSERT INTO `COUNTRYREGION`
	(`datecreated`,  `countrycode`, `regioncode`, `regionname`, `regiongroupcode`)
VALUES
	(NOW(), 'MX' , 'AGU', 'sp Aguascalientes', ''),
	(NOW(), 'MX' , 'BCN', 'sp Baja California', ''),
	(NOW(), 'MX' , 'BCS', 'sp Baja California Sur', ''),
	(NOW(), 'MX' , 'CAM', 'sp Campeche', ''),
	(NOW(), 'MX' , 'CHP', 'sp Chiapas', ''),
	(NOW(), 'MX' , 'CHH', 'sp Chihuahua', ''),
	(NOW(), 'MX' , 'COA', 'sp Coahuila', ''),
	(NOW(), 'MX' , 'COL', 'sp Colima', ''),
	(NOW(), 'MX' , 'DIF', 'sp Distrito Federal', ''),
	(NOW(), 'MX' , 'DUR', 'sp Durango', ''),
	(NOW(), 'MX' , 'GUA', 'sp Guanajuato', ''),
	(NOW(), 'MX' , 'GRO', 'sp Guerrero', ''),
	(NOW(), 'MX' , 'HID', 'sp Hidalgo', ''),
	(NOW(), 'MX' , 'JAL', 'sp Jalisco', ''),
	(NOW(), 'MX' , 'MEX', 'sp Estado de México', ''),
	(NOW(), 'MX' , 'MIC', 'sp Michoacán', ''),
	(NOW(), 'MX' , 'MOR', 'sp Morelos', ''),
	(NOW(), 'MX' , 'NAY', 'sp Nayarit', ''),
	(NOW(), 'MX' , 'NLE', 'sp Nuevo León', ''),
	(NOW(), 'MX' , 'OAX', 'sp Oaxaca', ''),
	(NOW(), 'MX' , 'PUE', 'sp Puebla', ''),
	(NOW(), 'MX' , 'QUE', 'sp Querétaro', ''),
	(NOW(), 'MX' , 'ROO', 'sp Quintana Roo', ''),
	(NOW(), 'MX' , 'SLP', 'sp San Luis Potosí', ''),
	(NOW(), 'MX' , 'SIN', 'sp Sinaloa', ''),
	(NOW(), 'MX' , 'SON', 'sp Sonora', ''),
	(NOW(), 'MX' , 'TAB', 'sp Tabasco', ''),
	(NOW(), 'MX' , 'TAM', 'sp Tamaulipas', ''),
	(NOW(), 'MX' , 'TLA', 'sp Tlaxcala', ''),
	(NOW(), 'MX' , 'VER', 'sp Veracruz', ''),
	(NOW(), 'MX' , 'YUC', 'sp Yucatán', ''),
	(NOW(), 'MX' , 'ZAC', 'sp Zacatecas', '');

UPDATE `USERS` SET `addressupdated` = '0' WHERE `countrycode` = 'MX';

#
# PERU ADDRESS FORMAT AND STATES UPDATE
#

UPDATE `COUNTRIES` SET
	`displayfields` = 'firstname<p>lastname<p>company<p>add1<p>add2<p>state<p>country<p>postcode' ,
	`compulsoryfields` = 'firstname,lastname,add1,state,postcode',
	`displayformat` = '[firstname] [lastname]<br>[company]<br>[add1]<br>[add2]<br>[state]<br>[country]<br>[postcode]',
	`fieldlabels` = 'str_LabelFirstName,str_LabelLastName,str_LabelCompanyName,str_LabelAddressLine1,str_LabelAddressLine2,str_LabelState,str_LabelCountry,str_LabelZIPCode'
WHERE `isocode2` = 'PE';

INSERT INTO `COUNTRYREGION`
	(`datecreated`,  `countrycode`, `regioncode`, `regionname`, `regiongroupcode`)
VALUES
	(NOW(), 'PE' , 'AMA', 'sp Amazonas', ''),
	(NOW(), 'PE' , 'ANC', 'sp Ancash', ''),
	(NOW(), 'PE' , 'APU', 'sp Apurímac', ''),
	(NOW(), 'PE' , 'ARE', 'sp Arequipa', ''),
	(NOW(), 'PE' , 'AYA', 'sp Ayacucho', ''),
	(NOW(), 'PE' , 'CAJ', 'sp Cajamarca', ''),
	(NOW(), 'PE' , 'CAL', 'sp Callao', ''),
	(NOW(), 'PE' , 'CUS', 'sp Cusco', ''),
	(NOW(), 'PE' , 'HUC', 'sp Huánuco', ''),
	(NOW(), 'PE' , 'HUV', 'sp Huancavelica', ''),
	(NOW(), 'PE' , 'ICA', 'sp Ica', ''),
	(NOW(), 'PE' , 'JUN', 'sp Junín', ''),
	(NOW(), 'PE' , 'LAL', 'sp La Libertad', ''),
	(NOW(), 'PE' , 'LAM', 'sp Lambayeque', ''),
	(NOW(), 'PE' , 'LIM', 'sp Lima (departamento)', ''),
	(NOW(), 'PE' , 'LMA', 'sp Lima (provincia de la capital)', ''),
	(NOW(), 'PE' , 'LOR', 'sp Loreto', ''),
	(NOW(), 'PE' , 'MDD', 'sp Madre de Dios', ''),
	(NOW(), 'PE' , 'MOQ', 'sp Moquegua', ''),
	(NOW(), 'PE' , 'PAS', 'sp Pasco', ''),
	(NOW(), 'PE' , 'PIU', 'sp Piura', ''),
	(NOW(), 'PE' , 'AMA', 'sp Amazonas', ''),
	(NOW(), 'PE' , 'PUN', 'sp Puno', ''),
	(NOW(), 'PE' , 'SAM', 'sp San Martín', ''),
	(NOW(), 'PE' , 'TAC', 'sp Tacna', ''),
	(NOW(), 'PE' , 'TUM', 'sp Tumbes', ''),
	(NOW(), 'PE' , 'UCA', 'sp Ucayali', '');

UPDATE `USERS` SET `addressupdated` = '0' WHERE `countrycode` = 'PE';

INSERT INTO `ACTIVITYLOG`
	(`id`, `datecreated`, `sessionid`, `orderid`, `userid`, `userlogin`, `username`, `importance`, `sectioncode`, `actioncode`, `actionnotes`, `success`)
	VALUES (0, now(), 0, 0, 0, '', (SELECT USER()), 0, 'UPGRADE', '2016r1a22', 'FINISHED', 1);

#
# DDL END
#

SET FOREIGN_KEY_CHECKS = 1;