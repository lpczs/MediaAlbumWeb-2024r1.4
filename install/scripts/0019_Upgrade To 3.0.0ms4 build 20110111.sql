#
SET FOREIGN_KEY_CHECKS = 0;

#
# DDL START
#


INSERT INTO `ACTIVITYLOG` 
	(`id`, `datecreated`, `sessionid`, `orderid`, `userid`, `userlogin`, `username`, `importance`, `sectioncode`, `actioncode`, `actionnotes`, `success`) 
	VALUES (0, now(), 0, 0, 0, '', (SELECT USER()), 0, 'UPGRADE', '3.0.0ms4 build 20110111', 'STARTED', 1);


ALTER TABLE `SHIPPINGRATES` 
	MODIFY COLUMN `code` VARCHAR(50) NOT NULL,
	MODIFY COLUMN `uniquecode` VARCHAR(65) NOT NULL,
	MODIFY COLUMN `shippingmethodcode` VARCHAR(50) NOT NULL,
	MODIFY COLUMN `shippingzonecode` VARCHAR(101) NOT NULL,
	MODIFY COLUMN `productcode` VARCHAR(50) NOT NULL;

ALTER TABLE `SHIPPINGMETHODS` 
	MODIFY COLUMN `code` VARCHAR(50) NOT NULL;

ALTER TABLE `TAXRATES` 
	MODIFY COLUMN `code` VARCHAR(50) NOT NULL;

ALTER TABLE `ORDERITEMS` 
	MODIFY COLUMN `taxcode` VARCHAR(50) NOT NULL;

UPDATE SHIPPINGRATES sr SET shippingzonecode = IFNULL(NULLIF((SELECT CONCAT(sz.companycode, ".", sz.localcode) 
FROM SHIPPINGZONES sz WHERE (sz.code = sr.shippingzonecode) AND (sz.companycode <> "") 
AND (INSTR(sz.code, ".") = 0)), ""), shippingzonecode);

UPDATE SHIPPINGZONES SET code = CONCAT(companycode, ".", localcode) WHERE (companycode <> "") AND (INSTR(code, ".") = 0);

UPDATE TAXZONES SET code = CONCAT(companycode, ".", localcode) WHERE (companycode <> "") AND (INSTR(code, ".") = 0);


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

#
# ADDRESS FORM CHANGES
#
UPDATE `COUNTRIES` SET `displayfields` = REPLACE(`displayfields`, ',', '<p>');

UPDATE `COUNTRIES`
	SET `displayfields`    = 'country<p>company<p>firstname<p>lastname<p>add1=[add41], [add42] - [add43]<p>add2<p>postcode<p>city<p>state', 
		`fieldlabels`      = 'str_LabelCountry,str_LabelCompanyName,str_LabelFirstName,str_LabelLastName,str_LabelAddressLine1,str_LabelHouseNumber,str_LabelAddressLine2,str_LabelAddressLine3,str_LabelZIPCode,str_LabelTownCity,str_LabelState', 
		`compulsoryfields` = 'country,firstname,lastname,add41,add42,postcode,city,state', 
		`displayformat`    = '[company]<br>[firstname] [lastname]<br>[add1]<br>[add2]<br>[postcode] [city] [state]<br>[country]',
		`region`           = 'STATE'
	WHERE `isocode2`='BR';

# CONVERT EXISTING ADDRESS DATA FOR BRAZIL
# CONVERSION FOR TABLES USERS, COMPANIES, LICENSEKEYS, ORDERHEADER, ORDERSHIPPING, ORDERTEMP, SITES, 

# ORDERSHIPPING TABLE
# build address line 1, save additional address info 4 to address2, clear address3
UPDATE `ORDERSHIPPING` 
	SET `shippingcustomeraddress1` = CONCAT(`shippingcustomeraddress1`, "<,>", `shippingcustomeraddress2`, "<->", `shippingcustomeraddress3`, "<e>"),
		`shippingcustomeraddress2` = `shippingcustomeraddress4`,
		`shippingcustomeraddress3` = ""
	WHERE (`shippingcustomercountrycode` = "BR") AND (INSTR(`shippingcustomeraddress4`, "<p>") = 0);

# base field list in address4 on address line 1
UPDATE `ORDERSHIPPING` 
	SET `shippingcustomeraddress4` = REPLACE(`shippingcustomeraddress1`, "<e>", "")
	WHERE (`shippingcustomercountrycode` = "BR") AND (INSTR(`shippingcustomeraddress4`, "<p>") = 0);

# remove empty parts from address line 1
UPDATE `ORDERSHIPPING` 
	SET `shippingcustomeraddress1` = REPLACE(REPLACE(`shippingcustomeraddress1`, "<-><e>", "<e>"), "<,><->", "")
	WHERE (`shippingcustomercountrycode` = "BR") AND (INSTR(`shippingcustomeraddress4`, "<p>") = 0);

# remove more empty parts from address line 1
UPDATE `ORDERSHIPPING` 
	SET `shippingcustomeraddress1` = REPLACE(`shippingcustomeraddress1`, "<,><e>", "<e>")
	WHERE (`shippingcustomercountrycode` = "BR") AND (INSTR(`shippingcustomeraddress4`, "<p>") = 0);

# replace tags with actual commas and dashes
# these commas and dashes will only be there if there was info in address2 and address3
UPDATE `ORDERSHIPPING` 
	SET `shippingcustomeraddress1` = REPLACE(REPLACE(REPLACE(`shippingcustomeraddress1`, "<e>", ""), "<,>", ", "), "<->", " - ")
	WHERE (`shippingcustomercountrycode` = "BR") AND (INSTR(`shippingcustomeraddress4`, "<p>") = 0);

# replace tags in address4, the field list
# the tags have been left until now so we can select the rows that are being converted
UPDATE `ORDERSHIPPING` 
	SET `shippingcustomeraddress4` = REPLACE(REPLACE(`shippingcustomeraddress4`, "<,>", "<p>"), "<->", "<p>")
	WHERE (`shippingcustomercountrycode` = "BR") AND (INSTR(`shippingcustomeraddress4`, "<p>") = 0);


# ORDERTEMP TABLE
# build address line 1, save additional address info 4 to address2, clear address3
UPDATE `ORDERTEMP` 
	SET `shippingcustomeraddress1` = CONCAT(`shippingcustomeraddress1`, "<,>", `shippingcustomeraddress2`, "<->", `shippingcustomeraddress3`, "<e>"),
		`shippingcustomeraddress2` = `shippingcustomeraddress4`,
		`shippingcustomeraddress3` = ""
	WHERE (`shippingcustomercountrycode` = "BR") AND (INSTR(`shippingcustomeraddress4`, "<p>") = 0);

# base field list in address4 on address line 1
UPDATE `ORDERTEMP` 
	SET `shippingcustomeraddress4` = REPLACE(`shippingcustomeraddress1`, "<e>", "")
	WHERE (`shippingcustomercountrycode` = "BR") AND (INSTR(`shippingcustomeraddress4`, "<p>") = 0);

# remove empty parts from address line 1
UPDATE `ORDERTEMP` 
	SET `shippingcustomeraddress1` = REPLACE(REPLACE(`shippingcustomeraddress1`, "<-><e>", "<e>"), "<,><->", "")
	WHERE (`shippingcustomercountrycode` = "BR") AND (INSTR(`shippingcustomeraddress4`, "<p>") = 0);

# remove more empty parts from address line 1
UPDATE `ORDERTEMP` 
	SET `shippingcustomeraddress1` = REPLACE(`shippingcustomeraddress1`, "<,><e>", "<e>")
	WHERE (`shippingcustomercountrycode` = "BR") AND (INSTR(`shippingcustomeraddress4`, "<p>") = 0);

# replace tags with actual commas and dashes
# these commas and dashes will only be there if there was info in address2 and address3
UPDATE `ORDERTEMP` 
	SET `shippingcustomeraddress1` = REPLACE(REPLACE(REPLACE(`shippingcustomeraddress1`, "<e>", ""), "<,>", ", "), "<->", " - ")
	WHERE (`shippingcustomercountrycode` = "BR") AND (INSTR(`shippingcustomeraddress4`, "<p>") = 0);

# replace tags in address4, the field list
# the tags have been left until now so we can select the rows that are being converted
UPDATE `ORDERTEMP` 
	SET `shippingcustomeraddress4` = REPLACE(REPLACE(`shippingcustomeraddress4`, "<,>", "<p>"), "<->", "<p>")
	WHERE (`shippingcustomercountrycode` = "BR") AND (INSTR(`shippingcustomeraddress4`, "<p>") = 0);


# ORDERTEMP TABLE
# build address line 1, save additional address info 4 to address2, clear address3
UPDATE `ORDERTEMP` 
	SET `billingcustomeraddress1` = CONCAT(`billingcustomeraddress1`, "<,>", `billingcustomeraddress2`, "<->", `billingcustomeraddress3`, "<e>"),
		`billingcustomeraddress2` = `billingcustomeraddress4`,
		`billingcustomeraddress3` = ""
	WHERE (`billingcustomercountrycode` = "BR") AND (INSTR(`billingcustomeraddress4`, "<p>") = 0);

# base field list in address4 on address line 1
UPDATE `ORDERTEMP` 
	SET `billingcustomeraddress4` = REPLACE(`billingcustomeraddress1`, "<e>", "")
	WHERE (`billingcustomercountrycode` = "BR") AND (INSTR(`billingcustomeraddress4`, "<p>") = 0);

# remove empty parts from address line 1
UPDATE `ORDERTEMP` 
	SET `billingcustomeraddress1` = REPLACE(REPLACE(`billingcustomeraddress1`, "<-><e>", "<e>"), "<,><->", "")
	WHERE (`billingcustomercountrycode` = "BR") AND (INSTR(`billingcustomeraddress4`, "<p>") = 0);

# remove more empty parts from address line 1
UPDATE `ORDERTEMP` 
	SET `billingcustomeraddress1` = REPLACE(`billingcustomeraddress1`, "<,><e>", "<e>")
	WHERE (`billingcustomercountrycode` = "BR") AND (INSTR(`billingcustomeraddress4`, "<p>") = 0);

# replace tags with actual commas and dashes
# these commas and dashes will only be there if there was info in address2 and address3
UPDATE `ORDERTEMP` 
	SET `billingcustomeraddress1` = REPLACE(REPLACE(REPLACE(`billingcustomeraddress1`, "<e>", ""), "<,>", ", "), "<->", " - ")
	WHERE (`billingcustomercountrycode` = "BR") AND (INSTR(`billingcustomeraddress4`, "<p>") = 0);

# replace tags in address4, the field list
# the tags have been left until now so we can select the rows that are being converted
UPDATE `ORDERTEMP` 
	SET `billingcustomeraddress4` = REPLACE(REPLACE(`billingcustomeraddress4`, "<,>", "<p>"), "<->", "<p>")
	WHERE (`billingcustomercountrycode` = "BR") AND (INSTR(`billingcustomeraddress4`, "<p>") = 0);


# ORDERHEADER TABLE
# build address line 1, save additional address info 4 to address2, clear address3
UPDATE `ORDERHEADER` 
	SET `billingcustomeraddress1` = CONCAT(`billingcustomeraddress1`, "<,>", `billingcustomeraddress2`, "<->", `billingcustomeraddress3`, "<e>"),
		`billingcustomeraddress2` = `billingcustomeraddress4`,
		`billingcustomeraddress3` = ""
	WHERE (`billingcustomercountrycode` = "BR") AND (INSTR(`billingcustomeraddress4`, "<p>") = 0);

# base field list in address4 on address line 1
UPDATE `ORDERHEADER` 
	SET `billingcustomeraddress4` = REPLACE(`billingcustomeraddress1`, "<e>", "")
	WHERE (`billingcustomercountrycode` = "BR") AND (INSTR(`billingcustomeraddress4`, "<p>") = 0);

# remove empty parts from address line 1
UPDATE `ORDERHEADER` 
	SET `billingcustomeraddress1` = REPLACE(REPLACE(`billingcustomeraddress1`, "<-><e>", "<e>"), "<,><->", "")
	WHERE (`billingcustomercountrycode` = "BR") AND (INSTR(`billingcustomeraddress4`, "<p>") = 0);

# remove more empty parts from address line 1
UPDATE `ORDERHEADER` 
	SET `billingcustomeraddress1` = REPLACE(`billingcustomeraddress1`, "<,><e>", "<e>")
	WHERE (`billingcustomercountrycode` = "BR") AND (INSTR(`billingcustomeraddress4`, "<p>") = 0);

# replace tags with actual commas and dashes
# these commas and dashes will only be there if there was info in address2 and address3
UPDATE `ORDERHEADER` 
	SET `billingcustomeraddress1` = REPLACE(REPLACE(REPLACE(`billingcustomeraddress1`, "<e>", ""), "<,>", ", "), "<->", " - ")
	WHERE (`billingcustomercountrycode` = "BR") AND (INSTR(`billingcustomeraddress4`, "<p>") = 0);

# replace tags in address4, the field list
# the tags have been left until now so we can select the rows that are being converted
UPDATE `ORDERHEADER` 
	SET `billingcustomeraddress4` = REPLACE(REPLACE(`billingcustomeraddress4`, "<,>", "<p>"), "<->", "<p>")
	WHERE (`billingcustomercountrycode` = "BR") AND (INSTR(`billingcustomeraddress4`, "<p>") = 0);


# SITES TABLE
# build address line 1, save additional address info 4 to address2, clear address3
UPDATE `SITES` 
	SET `address1` = CONCAT(`address1`, "<,>", `address2`, "<->", `address3`, "<e>"),
		`address2` = `address4`,
		`address3` = ""
	WHERE (`countrycode` = "BR") AND (INSTR(`address4`, "<p>") = 0);

# base field list in address4 on address line 1
UPDATE `SITES` 
	SET `address4` = REPLACE(`address1`, "<e>", "")
	WHERE (`countrycode` = "BR") AND (INSTR(`address4`, "<p>") = 0);

# remove empty parts from address line 1
UPDATE `SITES` 
	SET `address1` = REPLACE(REPLACE(`address1`, "<-><e>", "<e>"), "<,><->", "")
	WHERE (`countrycode` = "BR") AND (INSTR(`address4`, "<p>") = 0);

# remove more empty parts from address line 1
UPDATE `SITES` 
	SET `address1` = REPLACE(`address1`, "<,><e>", "<e>")
	WHERE (`countrycode` = "BR") AND (INSTR(`address4`, "<p>") = 0);

# replace tags with actual commas and dashes
# these commas and dashes will only be there if there was info in address2 and address3
UPDATE `SITES` 
	SET `address1` = REPLACE(REPLACE(REPLACE(`address1`, "<e>", ""), "<,>", ", "), "<->", " - ")
	WHERE (`countrycode` = "BR") AND (INSTR(`address4`, "<p>") = 0);

# replace tags in address4, the field list
# the tags have been left until now so we can select the rows that are being converted
UPDATE `SITES` 
	SET `address4` = REPLACE(REPLACE(`address4`, "<,>", "<p>"), "<->", "<p>")
	WHERE (`countrycode` = "BR") AND (INSTR(`address4`, "<p>") = 0);


# LICENSEKEYS TABLE
# build address line 1, save additional address info 4 to address2, clear address3
UPDATE `LICENSEKEYS` 
	SET `address1` = CONCAT(`address1`, "<,>", `address2`, "<->", `address3`, "<e>"),
		`address2` = `address4`,
		`address3` = ""
	WHERE (`countrycode` = "BR") AND (INSTR(`address4`, "<p>") = 0);

# base field list in address4 on address line 1
UPDATE `LICENSEKEYS` 
	SET `address4` = REPLACE(`address1`, "<e>", "")
	WHERE (`countrycode` = "BR") AND (INSTR(`address4`, "<p>") = 0);

# remove empty parts from address line 1
UPDATE `LICENSEKEYS` 
	SET `address1` = REPLACE(REPLACE(`address1`, "<-><e>", "<e>"), "<,><->", "")
	WHERE (`countrycode` = "BR") AND (INSTR(`address4`, "<p>") = 0);

# remove more empty parts from address line 1
UPDATE `LICENSEKEYS` 
	SET `address1` = REPLACE(`address1`, "<,><e>", "<e>")
	WHERE (`countrycode` = "BR") AND (INSTR(`address4`, "<p>") = 0);

# replace tags with actual commas and dashes
# these commas and dashes will only be there if there was info in address2 and address3
UPDATE `LICENSEKEYS` 
	SET `address1` = REPLACE(REPLACE(REPLACE(`address1`, "<e>", ""), "<,>", ", "), "<->", " - ")
	WHERE (`countrycode` = "BR") AND (INSTR(`address4`, "<p>") = 0);

# replace tags in address4, the field list
# the tags have been left until now so we can select the rows that are being converted
UPDATE `LICENSEKEYS` 
	SET `address4` = REPLACE(REPLACE(`address4`, "<,>", "<p>"), "<->", "<p>")
	WHERE (`countrycode` = "BR") AND (INSTR(`address4`, "<p>") = 0);


# COMPANIES TABLE
# build address line 1, save additional address info 4 to address2, clear address3
UPDATE `COMPANIES` 
	SET `address1` = CONCAT(`address1`, "<,>", `address2`, "<->", `address3`, "<e>"),
		`address2` = `address4`,
		`address3` = ""
	WHERE (`countrycode` = "BR") AND (INSTR(`address4`, "<p>") = 0);

# base field list in address4 on address line 1
UPDATE `COMPANIES` 
	SET `address4` = REPLACE(`address1`, "<e>", "")
	WHERE (`countrycode` = "BR") AND (INSTR(`address4`, "<p>") = 0);

# remove empty parts from address line 1
UPDATE `COMPANIES` 
	SET `address1` = REPLACE(REPLACE(`address1`, "<-><e>", "<e>"), "<,><->", "")
	WHERE (`countrycode` = "BR") AND (INSTR(`address4`, "<p>") = 0);

# remove more empty parts from address line 1
UPDATE `COMPANIES` 
	SET `address1` = REPLACE(`address1`, "<,><e>", "<e>")
	WHERE (`countrycode` = "BR") AND (INSTR(`address4`, "<p>") = 0);

# replace tags with actual commas and dashes
# these commas and dashes will only be there if there was info in address2 and address3
UPDATE `COMPANIES` 
	SET `address1` = REPLACE(REPLACE(REPLACE(`address1`, "<e>", ""), "<,>", ", "), "<->", " - ")
	WHERE (`countrycode` = "BR") AND (INSTR(`address4`, "<p>") = 0);

# replace tags in address4, the field list
# the tags have been left until now so we can select the rows that are being converted
UPDATE `COMPANIES` 
	SET `address4` = REPLACE(REPLACE(`address4`, "<,>", "<p>"), "<->", "<p>")
	WHERE (`countrycode` = "BR") AND (INSTR(`address4`, "<p>") = 0);


# USERS TABLE
# build address line 1, save additional address info 4 to address2, clear address3
UPDATE `USERS` 
	SET `address1` = CONCAT(`address1`, "<,>", `address2`, "<->", `address3`, "<e>"),
		`address2` = `address4`,
		`address3` = ""
	WHERE (`countrycode` = "BR") AND (INSTR(`address4`, "<p>") = 0);

# base field list in address4 on address line 1
UPDATE `USERS` 
	SET `address4` = REPLACE(`address1`, "<e>", "")
	WHERE (`countrycode` = "BR") AND (INSTR(`address4`, "<p>") = 0);

# remove empty parts from address line 1
UPDATE `USERS` 
	SET `address1` = REPLACE(REPLACE(`address1`, "<-><e>", "<e>"), "<,><->", "")
	WHERE (`countrycode` = "BR") AND (INSTR(`address4`, "<p>") = 0);

# remove more empty parts from address line 1
UPDATE `USERS` 
	SET `address1` = REPLACE(`address1`, "<,><e>", "<e>")
	WHERE (`countrycode` = "BR") AND (INSTR(`address4`, "<p>") = 0);

# replace tags with actual commas and dashes
# these commas and dashes will only be there if there was info in address2 and address3
UPDATE `USERS` 
	SET `address1` = REPLACE(REPLACE(REPLACE(`address1`, "<e>", ""), "<,>", ", "), "<->", " - ")
	WHERE (`countrycode` = "BR") AND (INSTR(`address4`, "<p>") = 0);

# replace tags in address4, the field list
# the tags have been left until now so we can select the rows that are being converted
UPDATE `USERS` 
	SET `address4` = REPLACE(REPLACE(`address4`, "<,>", "<p>"), "<->", "<p>")
	WHERE (`countrycode` = "BR") AND (INSTR(`address4`, "<p>") = 0);


INSERT INTO `ACTIVITYLOG` 
	(`id`, `datecreated`, `sessionid`, `orderid`, `userid`, `userlogin`, `username`, `importance`, `sectioncode`, `actioncode`, `actionnotes`, `success`) 
	VALUES (0, now(), 0, 0, 0, '', (SELECT USER()), 0, 'UPGRADE', '3.0.0ms4 build 20110111', 'FINISHED', 1);

#
# DDL END
#

SET FOREIGN_KEY_CHECKS = 1;
