#
SET FOREIGN_KEY_CHECKS = 0;

#
# DDL START
#

INSERT INTO `ACTIVITYLOG`
	(`id`, `datecreated`, `sessionid`, `orderid`, `userid`, `userlogin`, `username`, `importance`, `sectioncode`, `actioncode`, `actionnotes`, `success`)
	VALUES (0, now(), 0, 0, 0, '', (SELECT USER()), 0, 'UPGRADE', '2016r1a21', 'STARTED', 1);

UPDATE `SYSTEMCONFIG` SET `webversiondate` = '2015-12-02';
UPDATE `SYSTEMCONFIG` SET `webversionnumber` = '2016.1.0.21';
UPDATE `SYSTEMCONFIG` SET `webversionstring`= '2016r1a21';

UPDATE `COUNTRIES` SET `region` = 'COUNTY' WHERE `isocode2` = 'IT';

INSERT INTO `COUNTRYREGION`
	(`datecreated`,  `countrycode`, `regioncode`, `regionname`, `regiongroupcode`)
VALUES
	(NOW(), 'IT' , 'AG', 'it Agrigento', ''),
	(NOW(), 'IT' , 'AL', 'it Alessandria', ''),
	(NOW(), 'IT' , 'AN', 'it Ancona', ''),
	(NOW(), 'IT' , 'AO', 'it Aosta', ''),
	(NOW(), 'IT' , 'AR', 'it Arezzo', ''),
    (NOW(), 'IT' , 'AP', 'it Ascoli Piceno', ''),
    (NOW(), 'IT' , 'AT', 'it Asti', ''),
    (NOW(), 'IT' , 'AV', 'it Avellino', ''),
    (NOW(), 'IT' , 'BA', 'it Bari', ''),
    (NOW(), 'IT' , 'BT', 'it Barletta-Andria-Trani', ''),
    (NOW(), 'IT' , 'BL', 'it Belluno', ''),
    (NOW(), 'IT' , 'BN', 'it Benevento', ''),
    (NOW(), 'IT' , 'BG', 'it Bergamo', ''),
    (NOW(), 'IT' , 'BI', 'it Biella', ''),
    (NOW(), 'IT' , 'BO', 'it Bologna', ''),
	(NOW(), 'IT' , 'BZ', 'it Bolzano', ''),
	(NOW(), 'IT' , 'BS', 'it Brescia', ''),
	(NOW(), 'IT' , 'BR', 'it Brindisi', ''),
	(NOW(), 'IT' , 'CA', 'it Cagliari', ''),
	(NOW(), 'IT' , 'CL', 'it Caltanissetta', ''),
	(NOW(), 'IT' , 'CB', 'it Campobasso', ''),
	(NOW(), 'IT' , 'CI', 'it Carbonia-Iglesias', ''),
	(NOW(), 'IT' , 'CE', 'it Caserta', ''),
	(NOW(), 'IT' , 'CT', 'it Catania', ''),
	(NOW(), 'IT' , 'CZ', 'it Catanzaro', ''),
	(NOW(), 'IT' , 'CH', 'it Chieti', ''),
	(NOW(), 'IT' , 'CO', 'it Como', ''),
	(NOW(), 'IT' , 'CS', 'it Cosenza', ''),
	(NOW(), 'IT' , 'CR', 'it Cremona', ''),
	(NOW(), 'IT' , 'KR', 'it Crotone', ''),
	(NOW(), 'IT' , 'CN', 'it Cuneo', ''),
	(NOW(), 'IT' , 'EN', 'it Enna', ''),
	(NOW(), 'IT' , 'FM', 'it Fermo', ''),
	(NOW(), 'IT' , 'FE', 'it Ferrara', ''),
	(NOW(), 'IT' , 'FI', 'it Firenze', ''),
	(NOW(), 'IT' , 'FG', 'it Foggia', ''),
	(NOW(), 'IT' , 'FC', 'it Forlì-Cesena', ''),
	(NOW(), 'IT' , 'FR', 'it Frosinone', ''),
	(NOW(), 'IT' , 'GE', 'it Genova', ''),
	(NOW(), 'IT' , 'GO', 'it Gorizia', ''),
	(NOW(), 'IT' , 'GR', 'it Grosseto', ''),
	(NOW(), 'IT' , 'IM', 'it Imperia', ''),
	(NOW(), 'IT' , 'IS', 'it Isernia', ''),
	(NOW(), 'IT' , 'SP', 'it La Spezia', ''),
	(NOW(), 'IT' , 'AQ', 'it L\'Aquila', ''),
	(NOW(), 'IT' , 'LT', 'it Latina', ''),
	(NOW(), 'IT' , 'LE', 'it Lecce', ''),
	(NOW(), 'IT' , 'LC', 'it Lecco', ''),
	(NOW(), 'IT' , 'LI', 'it Livorno', ''),
	(NOW(), 'IT' , 'LO', 'it Lodi', ''),
	(NOW(), 'IT' , 'LU', 'it Lucca', ''),
	(NOW(), 'IT' , 'MC', 'it Macerata', ''),
	(NOW(), 'IT' , 'MN', 'it Mantova', ''),
	(NOW(), 'IT' , 'MS', 'it Massa-Carrara', ''),
	(NOW(), 'IT' , 'MT', 'it Matera', ''),
	(NOW(), 'IT' , 'ME', 'it Messina', ''),
	(NOW(), 'IT' , 'MI', 'it Milano', ''),
	(NOW(), 'IT' , 'MO', 'it Modena', ''),
	(NOW(), 'IT' , 'MB', 'it Monza e della Brianza', ''),
	(NOW(), 'IT' , 'NA', 'it Napoli', ''),
	(NOW(), 'IT' , 'NO', 'it Novara', ''),
	(NOW(), 'IT' , 'NU', 'it Nuoro', ''),
	(NOW(), 'IT' , 'OT', 'it Olbia-Tempio', ''),
	(NOW(), 'IT' , 'OR', 'it Oristano', ''),
	(NOW(), 'IT' , 'PD', 'it Padova', ''),
	(NOW(), 'IT' , 'PA', 'it Palermo', ''),
	(NOW(), 'IT' , 'PR', 'it Parma', ''),
	(NOW(), 'IT' , 'PV', 'it Pavia', ''),
	(NOW(), 'IT' , 'PG', 'it Perugia', ''),
	(NOW(), 'IT' , 'PU', 'it Pesaro e Urbino', ''),
	(NOW(), 'IT' , 'PE', 'it Pescara', ''),
	(NOW(), 'IT' , 'PC', 'it Piacenza', ''),
	(NOW(), 'IT' , 'PI', 'it Pisa', ''),
	(NOW(), 'IT' , 'PT', 'it Pistoia', ''),
	(NOW(), 'IT' , 'PN', 'it Pordenone', ''),
	(NOW(), 'IT' , 'PZ', 'it Potenza', ''),
	(NOW(), 'IT' , 'PO', 'it Prato', ''),
	(NOW(), 'IT' , 'RG', 'it Ragusa', ''),
	(NOW(), 'IT' , 'RA', 'it Ravenna', ''),
	(NOW(), 'IT' , 'RC', 'it Reggio Calabria', ''),
	(NOW(), 'IT' , 'RE', 'it Reggio Emilia', ''),
	(NOW(), 'IT' , 'RI', 'it Rieti', ''),
	(NOW(), 'IT' , 'RN', 'it Rimini', ''),
	(NOW(), 'IT' , 'RM', 'it Roma', ''),
	(NOW(), 'IT' , 'RO', 'it Rovigo', ''),
	(NOW(), 'IT' , 'SA', 'it Salerno', ''),
	(NOW(), 'IT' , 'VS', 'it Medio Campidano', ''),
	(NOW(), 'IT' , 'SS', 'it Sassari', ''),
	(NOW(), 'IT' , 'SV', 'it Savona', ''),
	(NOW(), 'IT' , 'SI', 'it Siena', ''),
	(NOW(), 'IT' , 'SR', 'it Siracusa', ''),
	(NOW(), 'IT' , 'SO', 'it Sondrio', ''),
	(NOW(), 'IT' , 'TA', 'it Taranto', ''),
	(NOW(), 'IT' , 'TE', 'it Teramo', ''),
	(NOW(), 'IT' , 'TR', 'it Terni', ''),
	(NOW(), 'IT' , 'TO', 'it Torino', ''),
	(NOW(), 'IT' , 'OG', 'it Ogliastra', ''),
	(NOW(), 'IT' , 'TP', 'it Trapani', ''),
	(NOW(), 'IT' , 'TN', 'it Trento', ''),
	(NOW(), 'IT' , 'TV', 'it Treviso', ''),
	(NOW(), 'IT' , 'TS', 'it Trieste', ''),
	(NOW(), 'IT' , 'UD', 'it Udine', ''),
	(NOW(), 'IT' , 'VA', 'it Varese', ''),
	(NOW(), 'IT' , 'VE', 'it Venezia', ''),
	(NOW(), 'IT' , 'VB', 'it Verbano-Cusio-Ossola', ''),
	(NOW(), 'IT' , 'VC', 'it Vercelli', ''),
	(NOW(), 'IT' , 'VR', 'it Verona', ''),
	(NOW(), 'IT' , 'AR', 'it Vibo Valentia', ''),
	(NOW(), 'IT' , 'VI', 'it Vicenza', ''),
	(NOW(), 'IT' , 'VT', 'it Viterbo', '');

UPDATE `USERS` SET `addressupdated` = '0' WHERE `countrycode` = 'IT';

INSERT INTO `ACTIVITYLOG`
	(`id`, `datecreated`, `sessionid`, `orderid`, `userid`, `userlogin`, `username`, `importance`, `sectioncode`, `actioncode`, `actionnotes`, `success`)
	VALUES (0, now(), 0, 0, 0, '', (SELECT USER()), 0, 'UPGRADE', '2016r1a21', 'FINISHED', 1);

#
# DDL END
#

SET FOREIGN_KEY_CHECKS = 1;