#
SET FOREIGN_KEY_CHECKS = 0;

#
# DDL START
#

INSERT INTO `ACTIVITYLOG`
	(`id`, `datecreated`, `sessionid`, `orderid`, `userid`, `userlogin`, `username`, `importance`, `sectioncode`, `actioncode`, `actionnotes`, `success`)
	VALUES (0, now(), 0, 0, 0, '', (SELECT USER()), 0, 'UPGRADE', '3.3.0a3 part3', 'STARTED', 1);

UPDATE `SYSTEMCONFIG` SET `webversiondate` = '2012-08-24';
UPDATE `SYSTEMCONFIG` SET `webversionnumber` = '3.3.0.3';
UPDATE `SYSTEMCONFIG` SET `webversionstring`= '3.3.0a3';

INSERT INTO `COUNTRYREGION` ( `datecreated`, `countrycode`, `regioncode`, `regionname`, `regiongroupcode`)
VALUES (now(), 'CO', 'AMAZONAS', 'en Amazonas<p>es Amazonas', ''),
(now(), 'CO', 'ANTIOQUIA', 'en Antioquia<p>es Antioquia', ''),
(now(), 'CO', 'ARAUCA', 'en Arauca<p>es Arauca', ''),
(now(), 'CO', 'ATLANTICO', 'en Atlantico <p>es Atlantico', ''),
(now(), 'CO', 'BOLIVA', 'en Bolivar<p>es Bolivar', ''),
(now(), 'CO', 'BOYACA', 'en Boyacá<p>es Boyacá', ''),
(now(), 'CO', 'CALDAS', 'en Caldas<p>es Caldas', ''),
(now(), 'CO', 'CAQUETA', 'en Caquetá<p>es Caquetá', ''),
(now(), 'CO', 'CASANARE', 'en Casanare<p>es Casanare', ''),
(now(), 'CO', 'CAUCA', 'en Cauca<p>es Cauca', ''),
(now(), 'CO', 'CESAR', 'en Cesar<p>es Cesar', ''),
(now(), 'CO', 'CHOCO', 'en Chocó<p>es Chocó', ''),
(now(), 'CO', 'CORDOBA', 'en Córdoba<p>es Córdoba', ''),
(now(), 'CO', 'CUNDINAMARCA', 'en Cundinamarca<p>es Cundinamarca', ''),
(now(), 'CO', 'GUAINIA', 'en Guainía<p>es Guainía', ''),
(now(), 'CO', 'GUAVIARE', 'en Guaviare<p>es Guaviare', ''),
(now(), 'CO', 'HUILA', 'en Huila<p>es Huila', ''),
(now(), 'CO', 'LAGUAJIRA', 'en La Guajira<p>es La Guajira', ''),
(now(), 'CO', 'MAGDALENA', 'en Magdalena<p>es Magdalena', ''),
(now(), 'CO', 'META', 'en Meta<p>es Meta', ''),
(now(), 'CO', 'NARINO', 'en Nariño<p>es Nariño', ''),
(now(), 'CO', 'NORTEDESANTANDER', 'en Norte de Santander<p>es Norte de Santander', ''),
(now(), 'CO', 'PUTUMAYO', 'en Putumayo<p>es Putumayo', ''),
(now(), 'CO', 'QUINDIO', 'en Quindio<p>es Quindio', ''),
(now(), 'CO', 'RISARALDA', 'en Risaralda<p>es Risaralda', ''),
(now(), 'CO', 'SANADRESYPROVINCIA', 'en San Andres y Providencia<p>es San Andres y Providencia', ''),
(now(), 'CO', 'SANTANDER', 'en Santander<p>es Santander', ''),
(now(), 'CO', 'PUTUMAYO', 'en Putumayo<p>es Putumayo', ''),
(now(), 'CO', 'QUINDIO', 'en Quindio<p>es Quindio', ''),
(now(), 'CO', 'RISARALDA', 'en Risaralda<p>es Risaralda', ''),
(now(), 'CO', 'SANADRESYPROVINCIA', 'en San Andres y Providencia<p>es San Andres y Providencia', ''),
(now(), 'CO', 'SANTANDER', 'en Santander<p>es Santander', ''),
(now(), 'CO', 'SUCRE', 'en Sucre<p>es Sucre', ''),
(now(), 'CO', 'TOLIMA', 'en Tolima<p>es Tolima', ''),
(now(), 'CO', 'VALLEDELCAUCA', 'en Valle del Cauca<p>es Valle del Cauca', ''),
(now(), 'CO', 'VAUPES', 'en Vaupés<p>es Vaupés', ''),
(now(), 'CO', 'VICHADA', 'en Vichada<p>es Vichada', '');

UPDATE `USERS` SET `addressupdated` = '0' WHERE `USERS`.`countrycode` = 'CO';

UPDATE `COUNTRIES` SET `displayfields` = 'country<p>firstname<p>lastname<p>company<p>add1<p>add2<p>city<p>state<p>postcode',
`compulsoryfields` = 'country,firstname,lastname,add1,city,state,postcode',
`displayformat` = '[firstname] [lastname]<br>[company]<br>[add1]<br>[add2]<br>[city] [postcode]<br>[state]<br>[country]',
`fieldlabels` = 'str_LabelCountry,str_LabelFirstName,str_LabelLastName,str_LabelCompanyName,str_LabelAddressLine1,str_LabelAddressLine2,str_LabelTownCity,str_LabelState,str_LabelPostalCode'
WHERE `COUNTRIES`.`isocode2` = 'CO';


INSERT INTO `ACTIVITYLOG`
	(`id`, `datecreated`, `sessionid`, `orderid`, `userid`, `userlogin`, `username`, `importance`, `sectioncode`, `actioncode`, `actionnotes`, `success`)
	VALUES (0, now(), 0, 0, 0, '', (SELECT USER()), 0, 'UPGRADE', '3.3.0a3 part3', 'FINISHED', 1);

#
# DDL END
#

SET FOREIGN_KEY_CHECKS = 1;
