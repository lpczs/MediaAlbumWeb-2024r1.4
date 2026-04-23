#
SET FOREIGN_KEY_CHECKS = 0;

#
# DDL START
#

INSERT INTO `ACTIVITYLOG`
	(`id`, `datecreated`, `sessionid`, `orderid`, `userid`, `userlogin`, `username`, `importance`, `sectioncode`, `actioncode`, `actionnotes`, `success`)
	VALUES (0, now(), 0, 0, 0, '', (SELECT USER()), 0, 'UPGRADE', '3.0.2a8', 'STARTED', 1);
	
# Delete all country regions of Brazil and reinsert them because they might have been duplicated before by mistake
DELETE FROM `COUNTRYREGION` WHERE `countrycode` = 'BR';
DELETE FROM `COUNTRYREGIONGROUP` WHERE `countrycode` = 'BR';

#
# Brazilian Regions
#
INSERT INTO `COUNTRYREGIONGROUP` (`datecreated`, `countrycode`, `sortorder`, `regiongroupcode`, `regiongroupname`)
	SELECT now(), 'BR','0001', 'RN',"pt Região Norte" FROM DUAL WHERE NOT EXISTS (SELECT * FROM `COUNTRYREGIONGROUP` WHERE countrycode='BR' AND regiongroupcode='RN');

INSERT INTO `COUNTRYREGIONGROUP` (`datecreated`, `countrycode`, `sortorder`, `regiongroupcode`, `regiongroupname`)
	SELECT now(), 'BR','0002', 'RNE',"pt Região Nordeste" FROM DUAL WHERE NOT EXISTS (SELECT * FROM `COUNTRYREGIONGROUP` WHERE countrycode='BR' AND regiongroupcode='RNE');

INSERT INTO `COUNTRYREGIONGROUP` (`datecreated`, `countrycode`, `sortorder`, `regiongroupcode`, `regiongroupname`)
	SELECT now(), 'BR','0003', 'RCO',"pt Região Centro-Oeste" FROM DUAL WHERE NOT EXISTS (SELECT * FROM `COUNTRYREGIONGROUP` WHERE countrycode='BR' AND regiongroupcode='RCO');

INSERT INTO `COUNTRYREGIONGROUP` (`datecreated`, `countrycode`, `sortorder`, `regiongroupcode`, `regiongroupname`)
	SELECT now(), 'BR','0004', 'RSE',"pt Região Sudeste" FROM DUAL WHERE NOT EXISTS (SELECT * FROM `COUNTRYREGIONGROUP` WHERE countrycode='BR' AND regiongroupcode='RSE');

INSERT INTO `COUNTRYREGIONGROUP` (`datecreated`, `countrycode`, `sortorder`, `regiongroupcode`, `regiongroupname`)
	SELECT now(), 'BR','0005', 'RS',"pt Região Sul" FROM DUAL WHERE NOT EXISTS (SELECT * FROM `COUNTRYREGIONGROUP` WHERE countrycode='BR' AND regiongroupcode='RS');

#
# Brazilian States
#
INSERT INTO `COUNTRYREGION` (`datecreated`, `countrycode`, `regioncode`, `regionname`, `regiongroupcode`)
	SELECT now(), 'BR', 'AC', 'pt Acre', 'RN' FROM DUAL WHERE NOT EXISTS (SELECT * FROM `COUNTRYREGION` WHERE countrycode='BR' AND regioncode='AC');

INSERT INTO `COUNTRYREGION` (`datecreated`, `countrycode`, `regioncode`, `regionname`, `regiongroupcode`)
	SELECT now(), 'BR', 'AL', 'pt Alagoas', 'RNE' FROM DUAL WHERE NOT EXISTS (SELECT * FROM `COUNTRYREGION` WHERE countrycode='BR' AND regioncode='AL');

INSERT INTO `COUNTRYREGION` (`datecreated`, `countrycode`, `regioncode`, `regionname`, `regiongroupcode`)
	SELECT now(), 'BR', 'AM', 'pt Amazonas', 'RN' FROM DUAL WHERE NOT EXISTS (SELECT * FROM `COUNTRYREGION` WHERE countrycode='BR' AND regioncode='AM');

INSERT INTO `COUNTRYREGION` (`datecreated`, `countrycode`, `regioncode`, `regionname`, `regiongroupcode`)
	SELECT now(), 'BR', 'AP', 'pt Amapá', 'RN' FROM DUAL WHERE NOT EXISTS (SELECT * FROM `COUNTRYREGION` WHERE countrycode='BR' AND regioncode='AP');

INSERT INTO `COUNTRYREGION` (`datecreated`, `countrycode`, `regioncode`, `regionname`, `regiongroupcode`)
	SELECT now(), 'BR', 'BA', 'pt Bahia', 'RNE' FROM DUAL WHERE NOT EXISTS (SELECT * FROM `COUNTRYREGION` WHERE countrycode='BR' AND regioncode='BA');

INSERT INTO `COUNTRYREGION` (`datecreated`, `countrycode`, `regioncode`, `regionname`, `regiongroupcode`)
	SELECT now(), 'BR', 'CE', 'pt Ceará', 'RNE' FROM DUAL WHERE NOT EXISTS (SELECT * FROM `COUNTRYREGION` WHERE countrycode='BR' AND regioncode='CE');

INSERT INTO `COUNTRYREGION` (`datecreated`, `countrycode`, `regioncode`, `regionname`, `regiongroupcode`)
	SELECT now(), 'BR', 'DF', 'pt Distrito Federal', 'RCO' FROM DUAL WHERE NOT EXISTS (SELECT * FROM `COUNTRYREGION` WHERE countrycode='BR' AND regioncode='DF');

INSERT INTO `COUNTRYREGION` (`datecreated`, `countrycode`, `regioncode`, `regionname`, `regiongroupcode`)
	SELECT now(), 'BR', 'ES', 'pt Espírito Santo', 'RSE' FROM DUAL WHERE NOT EXISTS (SELECT * FROM `COUNTRYREGION` WHERE countrycode='BR' AND regioncode='ES');

INSERT INTO `COUNTRYREGION` (`datecreated`, `countrycode`, `regioncode`, `regionname`, `regiongroupcode`)
	SELECT now(), 'BR', 'GO', 'pt Goiás', 'RCO' FROM DUAL WHERE NOT EXISTS (SELECT * FROM `COUNTRYREGION` WHERE countrycode='BR' AND regioncode='GO');

INSERT INTO `COUNTRYREGION` (`datecreated`, `countrycode`, `regioncode`, `regionname`, `regiongroupcode`)
	SELECT now(), 'BR', 'MA', 'pt Maranhão', 'RNE' FROM DUAL WHERE NOT EXISTS (SELECT * FROM `COUNTRYREGION` WHERE countrycode='BR' AND regioncode='MA');

INSERT INTO `COUNTRYREGION` (`datecreated`, `countrycode`, `regioncode`, `regionname`, `regiongroupcode`)
	SELECT now(), 'BR', 'MG', 'pt Minas Gerais', 'RSE' FROM DUAL WHERE NOT EXISTS (SELECT * FROM `COUNTRYREGION` WHERE countrycode='BR' AND regioncode='MG');

INSERT INTO `COUNTRYREGION` (`datecreated`, `countrycode`, `regioncode`, `regionname`, `regiongroupcode`)
	SELECT now(), 'BR', 'MS', 'pt Mato Grosso do Sul', 'RCO' FROM DUAL WHERE NOT EXISTS (SELECT * FROM `COUNTRYREGION` WHERE countrycode='BR' AND regioncode='MS');

INSERT INTO `COUNTRYREGION` (`datecreated`, `countrycode`, `regioncode`, `regionname`, `regiongroupcode`)
	SELECT now(), 'BR', 'MT', 'pt Mato Grosso', 'RCO' FROM DUAL WHERE NOT EXISTS (SELECT * FROM `COUNTRYREGION` WHERE countrycode='BR' AND regioncode='MT');

INSERT INTO `COUNTRYREGION` (`datecreated`, `countrycode`, `regioncode`, `regionname`, `regiongroupcode`)
	SELECT now(), 'BR', 'PA', 'pt Pará', 'RN' FROM DUAL WHERE NOT EXISTS (SELECT * FROM `COUNTRYREGION` WHERE countrycode='BR' AND regioncode='PA');

INSERT INTO `COUNTRYREGION` (`datecreated`, `countrycode`, `regioncode`, `regionname`, `regiongroupcode`)
	SELECT now(), 'BR', 'PB', 'pt Paraíba', 'RNE' FROM DUAL WHERE NOT EXISTS (SELECT * FROM `COUNTRYREGION` WHERE countrycode='BR' AND regioncode='PB');

INSERT INTO `COUNTRYREGION` (`datecreated`, `countrycode`, `regioncode`, `regionname`, `regiongroupcode`)
	SELECT now(), 'BR', 'PE', 'pt Pernambuco', 'RNE' FROM DUAL WHERE NOT EXISTS (SELECT * FROM `COUNTRYREGION` WHERE countrycode='BR' AND regioncode='PE');

INSERT INTO `COUNTRYREGION` (`datecreated`, `countrycode`, `regioncode`, `regionname`, `regiongroupcode`)
	SELECT now(), 'BR', 'PI', 'pt Piauí', 'RNE' FROM DUAL WHERE NOT EXISTS (SELECT * FROM `COUNTRYREGION` WHERE countrycode='BR' AND regioncode='PI');

INSERT INTO `COUNTRYREGION` (`datecreated`, `countrycode`, `regioncode`, `regionname`, `regiongroupcode`)
	SELECT now(), 'BR', 'PR', 'pt Paraná', 'RS' FROM DUAL WHERE NOT EXISTS (SELECT * FROM `COUNTRYREGION` WHERE countrycode='BR' AND regioncode='PR');

INSERT INTO `COUNTRYREGION` (`datecreated`, `countrycode`, `regioncode`, `regionname`, `regiongroupcode`)
	SELECT now(), 'BR', 'RJ', 'pt Rio de Janeiro', 'RSE' FROM DUAL WHERE NOT EXISTS (SELECT * FROM `COUNTRYREGION` WHERE countrycode='BR' AND regioncode='RJ');

INSERT INTO `COUNTRYREGION` (`datecreated`, `countrycode`, `regioncode`, `regionname`, `regiongroupcode`)
	SELECT now(), 'BR', 'RN', 'pt Rio Grande do Norte', 'RNE' FROM DUAL WHERE NOT EXISTS (SELECT * FROM `COUNTRYREGION` WHERE countrycode='BR' AND regioncode='RN');

INSERT INTO `COUNTRYREGION` (`datecreated`, `countrycode`, `regioncode`, `regionname`, `regiongroupcode`)
	SELECT now(), 'BR', 'RO', 'pt Rondônia', 'RN' FROM DUAL WHERE NOT EXISTS (SELECT * FROM `COUNTRYREGION` WHERE countrycode='BR' AND regioncode='RO');

INSERT INTO `COUNTRYREGION` (`datecreated`, `countrycode`, `regioncode`, `regionname`, `regiongroupcode`)
	SELECT now(), 'BR', 'RR', 'pt Roraima', 'RN' FROM DUAL WHERE NOT EXISTS (SELECT * FROM `COUNTRYREGION` WHERE countrycode='BR' AND regioncode='RR');

INSERT INTO `COUNTRYREGION` (`datecreated`, `countrycode`, `regioncode`, `regionname`, `regiongroupcode`)
	SELECT now(), 'BR', 'RS', 'pt Rio Grande do Sul', 'RS' FROM DUAL WHERE NOT EXISTS (SELECT * FROM `COUNTRYREGION` WHERE countrycode='BR' AND regioncode='RS');

INSERT INTO `COUNTRYREGION` (`datecreated`, `countrycode`, `regioncode`, `regionname`, `regiongroupcode`)
	SELECT now(), 'BR', 'SC', 'pt Santa Catarina', 'RS' FROM DUAL WHERE NOT EXISTS (SELECT * FROM `COUNTRYREGION` WHERE countrycode='BR' AND regioncode='SC');

INSERT INTO `COUNTRYREGION` (`datecreated`, `countrycode`, `regioncode`, `regionname`, `regiongroupcode`)
	SELECT now(), 'BR', 'SE', 'pt Sergipe', 'RNE' FROM DUAL WHERE NOT EXISTS (SELECT * FROM `COUNTRYREGION` WHERE countrycode='BR' AND regioncode='SE');

INSERT INTO `COUNTRYREGION` (`datecreated`, `countrycode`, `regioncode`, `regionname`, `regiongroupcode`)
	SELECT now(), 'BR', 'SP', 'pt São Paulo', 'RSE' FROM DUAL WHERE NOT EXISTS (SELECT * FROM `COUNTRYREGION` WHERE countrycode='BR' AND regioncode='SP');

INSERT INTO `COUNTRYREGION` (`datecreated`, `countrycode`, `regioncode`, `regionname`, `regiongroupcode`)
	SELECT now(), 'BR', 'TO', 'pt Tocantins', 'RN' FROM DUAL WHERE NOT EXISTS (SELECT * FROM `COUNTRYREGION` WHERE countrycode='BR' AND regioncode='TO');

UPDATE `SYSTEMCONFIG` SET `webversiondate` = '2011-11-24';
UPDATE `SYSTEMCONFIG` SET `webversionnumber` = '3.0.2.8';
UPDATE `SYSTEMCONFIG` SET `webversionstring`= '3.0.2a8';

INSERT INTO `ACTIVITYLOG`
	(`id`, `datecreated`, `sessionid`, `orderid`, `userid`, `userlogin`, `username`, `importance`, `sectioncode`, `actioncode`, `actionnotes`, `success`)
	VALUES (0, now(), 0, 0, 0, '', (SELECT USER()), 0, 'UPGRADE', '3.0.2a8', 'FINISHED', 1);

#
# DDL END
#

SET FOREIGN_KEY_CHECKS = 1;
