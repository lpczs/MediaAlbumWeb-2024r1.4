#
SET FOREIGN_KEY_CHECKS = 0;

#
# DDL START
#

INSERT INTO `ACTIVITYLOG`
	(`id`, `datecreated`, `sessionid`, `orderid`, `userid`, `userlogin`, `username`, `importance`, `sectioncode`, `actioncode`, `actionnotes`, `success`)
	VALUES (0, now(), 0, 0, 0, '', (SELECT USER()), 0, 'UPGRADE', '2016r1a15', 'STARTED', 1);

UPDATE `SYSTEMCONFIG` SET `webversiondate` = '2015-11-19';
UPDATE `SYSTEMCONFIG` SET `webversionnumber` = '2016.1.0.15';
UPDATE `SYSTEMCONFIG` SET `webversionstring`= '2016r1a15';

INSERT INTO COUNTRYREGION
	(`datecreated`,  `countrycode`, `regioncode`, `regionname`, `regiongroupcode`)
VALUES
	(NOW(), 'CL' , 'ARICA', 'sp Arica', ''),
	(NOW(), 'CL' , 'ANTOFAGASTA', 'sp Antofagasta', ''),
	(NOW(), 'CL' , 'CALAMA', 'sp Calama', ''),
	(NOW(), 'CL' , 'CHILLAN', 'sp Chillán', ''),
	(NOW(), 'CL' , 'CONCEPCION', 'sp Concepcion', ''),
	(NOW(), 'CL' , 'COPIAPO', 'sp Copiapó', ''),
	(NOW(), 'CL' , 'COQUIMBO', 'sp Coquimbo', ''),
	(NOW(), 'CL' , 'COYHAIQUE', 'sp Coyhaique', ''),
	(NOW(), 'CL' , 'CURICO', 'sp Curico', ''),
	(NOW(), 'CL' , 'IQUIQUE', 'sp Iquique', ''),
	(NOW(), 'CL' , 'LASERENA', 'sp La Serena', ''),
	(NOW(), 'CL' , 'LOSANGELES', 'sp Los Angeles', ''),
	(NOW(), 'CL' , 'OSORNO', 'sp Osorno', ''),
	(NOW(), 'CL' , 'OVALLE', 'sp Ovalle', ''),
	(NOW(), 'CL' , 'PUERTOMONTT', 'sp Puerto Montt', ''),
	(NOW(), 'CL' , 'PUNTAARENAS', 'sp Punta Arenas', ''),
	(NOW(), 'CL' , 'RANCAGUA', 'sp Rancagua', ''),
	(NOW(), 'CL' , 'SANANTONIO', 'sp San Antonio', ''),
	(NOW(), 'CL' , 'SANFERNANDO', 'sp San Fernando', ''),
	(NOW(), 'CL' , 'SANTIAGO', 'sp Santiago', ''),
	(NOW(), 'CL' , 'TALCA', 'sp Talca', ''),
	(NOW(), 'CL' , 'TEMUCO', 'sp Temuco', ''),
	(NOW(), 'CL' , 'VALDIVIA', 'sp Valdivia', ''),
	(NOW(), 'CL' , 'VALPARAISO', 'sp Valparaiso', ''),
	(NOW(), 'CL' , 'VINADELMAR', 'sp Viña Del Mar', '');

UPDATE `USERS` SET `addressupdated` = '0' WHERE `countrycode` = 'CL';

INSERT INTO `ACTIVITYLOG`
(`id`, `datecreated`, `sessionid`, `orderid`, `userid`, `userlogin`, `username`, `importance`, `sectioncode`, `actioncode`, `actionnotes`, `success`)
VALUES (0, now(), 0, 0, 0, '', (SELECT USER()), 0, 'UPGRADE', '2016r1a15', 'FINISHED', 1);

#
# DDL END
#

SET FOREIGN_KEY_CHECKS = 1;