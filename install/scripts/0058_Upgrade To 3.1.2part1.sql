#
SET FOREIGN_KEY_CHECKS = 0;

#
# DDL START
#

INSERT INTO `ACTIVITYLOG`
	(`id`, `datecreated`, `sessionid`, `orderid`, `userid`, `userlogin`, `username`, `importance`, `sectioncode`, `actioncode`, `actionnotes`, `success`)
	VALUES (0, now(), 0, 0, 0, '', (SELECT USER()), 0, 'UPGRADE', '3.1.2a1', 'STARTED', 1);

UPDATE `SYSTEMCONFIG` SET `webversiondate` = '2011-12-21';
UPDATE `SYSTEMCONFIG` SET `webversionnumber` = '3.1.2.1';
UPDATE `SYSTEMCONFIG` SET `webversionstring`= '3.1.2a1';

# Delete all country regions of Brazil and reinsert them because of the bug with encoding in install script
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


# Delete all country regions of Canada and reinsert them because of the bug with encoding in install script
DELETE FROM `COUNTRYREGION` WHERE `countrycode` = 'CA';

INSERT INTO `COUNTRYREGION` (`datecreated`, `countrycode`, `regioncode`, `regionname`, `regiongroupcode`)
VALUES
	(now(), 'CA','AB', 'en Alberta<p>fr Alberta', ''),
	(now(), 'CA','BC', 'en British Columbia<p>fr Colombie-Britannique', ''),
	(now(), 'CA','MB', 'en Manitoba<p>fr Manitoba', ''),
	(now(), 'CA','NB', 'en New Brunswick<p>fr Nouveau-Brunswick', ''),
	(now(), 'CA','NL', 'en Newfoundland and Labrador<p>fr Terre-Neuve-et-Labrador', ''),
	(now(), 'CA','NS', 'en Nova Scotia<p>fr Nouvelle-Écosse', ''),
	(now(), 'CA','NU', 'en Nunavut<p>fr Nunavut', ''),
	(now(), 'CA','NWT','en Northwest Territories<p>fr Territoires-du-Nord-Ouest', ''),
	(now(), 'CA','ON', 'en Ontario<p>fr Ontario', ''),
	(now(), 'CA','PE', 'en Prince Edward Island<p>fr Île-du-Prince-Édouard', ''),
	(now(), 'CA','QC', 'en Quebec<p>fr Québec', ''),
	(now(), 'CA','SK', 'en Saskatchewan<p>fr Saskatchewan', ''),
	(now(), 'CA','YT', 'en Yukon<p>fr Yukon', '');


# Delete all country regions of China and reinsert them because of the bug with encoding in install script
DELETE FROM `COUNTRYREGION` WHERE `countrycode` = 'CN';

# define regions for China
INSERT INTO `COUNTRYREGION` (`datecreated`, `countrycode`, `regioncode`, `regionname`, `regiongroupcode`)
VALUES
	(now(), 'CN','ANHUI', 'zh_cn 安徽省<p>en AnHui', ''),
	(now(), 'CN','BEIJING', 'zh_cn 北京市<p>en BeiJing', ''),
	(now(), 'CN','CHONGQING', 'zh_cn 重庆市<p>en ChongQing', ''),
	(now(), 'CN','FUJIAN', 'zh_cn 福建省<p>en FuJian', ''),
	(now(), 'CN','GANSU', 'zh_cn 甘肃省<p>en GanSu', ''),
	(now(), 'CN','GUANGDONG', 'zh_cn 广东省<p>en GuangDong', ''),
	(now(), 'CN','GUANGXI', 'zh_cn 广西自治区<p>en GuangXi', ''),
	(now(), 'CN','GUIZHOU', 'zh_cn 贵州省<p>en GuiZhou', ''),
	(now(), 'CN','HAINAN', 'zh_cn 海南省<p>en HaiNan', ''),
	(now(), 'CN','HEBEI', 'zh_cn 湖北省<p>en HeBei', ''),
	(now(), 'CN','HEILONGJIANG', 'zh_cn 黑龙江省<p>en HeiLongJiang', ''),
	(now(), 'CN','HENAN', 'zh_cn 河南省<p>en HeNan', ''),
	(now(), 'CN','HONGKONG', 'zh_cn 香港特别行政区<p>en HongKong', ''),
	(now(), 'CN','HUBEI', 'zh_cn 河北省<p>en HuBei', ''),
	(now(), 'CN','HUNAN', 'zh_cn 湖南省<p>en HuNan', ''),
	(now(), 'CN','JIANGXI', 'zh_cn 江西省<p>en Jiangxi', ''),
	(now(), 'CN','JIANGSU', 'zh_cn 江苏省<p>en JiangSu', ''),
	(now(), 'CN','JILIN', 'zh_cn 吉林省<p>en JiLin', ''),
	(now(), 'CN','LIAONING', 'zh_cn 辽宁省<p>en LiaoNing', ''),
	(now(), 'CN','MACAU', 'zh_cn 澳门特别行政区<p>en Macau', ''),
	(now(), 'CN','NEIMENG', 'zh_cn 内蒙古<p>en NeiMeng', ''),
	(now(), 'CN','NINGXIA', 'zh_cn 宁夏自治区<p>en NingXia', ''),
	(now(), 'CN','QINGHAI', 'zh_cn 青海省<p>en QingHai', ''),
	(now(), 'CN','SHANGDONG', 'zh_cn 山东省<p>en ShangDong', ''),
	(now(), 'CN','SHANGHAI', 'zh_cn 上海市<p>en ShangHai', ''),
	(now(), 'CN','SHANXI', 'zh_cn 山西省<p>en Shānxī', ''),
	(now(), 'CN','SHAANXI', 'zh_cn 陕西省<p>en Shǎnxī', ''),
	(now(), 'CN','SICUAN', 'zh_cn 四川省<p>en SiCuan', ''),
	(now(), 'CN','TAIWAN', 'zh_cn 台湾省<p>en Taiwan', ''),
	(now(), 'CN','TIANJIN', 'zh_cn 天津市<p>en TianJin', ''),
	(now(), 'CN','XINJIANG', 'zh_cn 新疆自治区<p>en XinJiang', ''),
	(now(), 'CN','XIZANG', 'zh_cn 西藏自治区<p>en XiZang', ''),
	(now(), 'CN','YUNNAN', 'zh_cn 云南省<p>en YunNan', ''),
	(now(), 'CN','ZHEJIANG', 'zh_cn 浙江省<p>en ZheJiang', '');

ALTER TABLE `SESSIONDATA` MODIFY COLUMN `sessionarraydata` BLOB;

ALTER TABLE `SESSIONDATA` ADD COLUMN `serializeddatalength` INTEGER NOT NULL DEFAULT 0 AFTER `userid`;

INSERT INTO `ACTIVITYLOG`
	(`id`, `datecreated`, `sessionid`, `orderid`, `userid`, `userlogin`, `username`, `importance`, `sectioncode`, `actioncode`, `actionnotes`, `success`)
	VALUES (0, now(), 0, 0, 0, '', (SELECT USER()), 0, 'UPGRADE', '3.1.2a1', 'FINISHED', 1);

#
# DDL END
#

SET FOREIGN_KEY_CHECKS = 1;
