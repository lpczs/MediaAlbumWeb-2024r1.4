#
SET FOREIGN_KEY_CHECKS = 0;

#
# DDL START
#


DELETE FROM `COUNTRYREGION` WHERE (`countrycode` = 'US') AND (`regioncode` = 'DC');
DELETE FROM `COUNTRYREGION` WHERE (`countrycode` = 'US') AND (`regioncode` = 'PR');

DELETE FROM `COUNTRYREGION` WHERE (`countrycode` = 'US') AND (`regioncode` = 'VI');
DELETE FROM `COUNTRYREGION` WHERE (`countrycode` = 'US') AND (`regioncode` = 'AS');
DELETE FROM `COUNTRYREGION` WHERE (`countrycode` = 'US') AND (`regioncode` = 'GU');
DELETE FROM `COUNTRYREGION` WHERE (`countrycode` = 'US') AND (`regioncode` = 'MP');

DELETE FROM `COUNTRYREGION` WHERE (`countrycode` = 'US') AND (`regioncode` = 'FM');
DELETE FROM `COUNTRYREGION` WHERE (`countrycode` = 'US') AND (`regioncode` = 'MH');
DELETE FROM `COUNTRYREGION` WHERE (`countrycode` = 'US') AND (`regioncode` = 'PW');

DELETE FROM `COUNTRYREGION` WHERE (`countrycode` = 'US') AND (`regioncode` = 'AE');
DELETE FROM `COUNTRYREGION` WHERE (`countrycode` = 'US') AND (`regioncode` = 'AA');
DELETE FROM `COUNTRYREGION` WHERE (`countrycode` = 'US') AND (`regioncode` = 'AP');

DELETE FROM `COUNTRYREGIONGROUP` WHERE `countrycode` = 'US';

UPDATE `COUNTRYREGION` SET `regiongroupcode` = 'ST' WHERE `countrycode` = 'US';

INSERT INTO `COUNTRYREGION` (`datecreated`, `countrycode`, `regioncode`, `regionname`, `regiongroupcode`)
VALUES 
	(now(), 'US','DC','en District of Columbia', 'ST'),
	(now(), 'US','PR','en Puerto Rico', 'ST'),

	(now(), 'US','VI','en U.S. Virgin Islands', 'OT'),
	(now(), 'US','AS','en American Samoa', 'OT'),
	(now(), 'US','GU','en Guam', 'OT'),
	(now(), 'US','MP','en Northern Mariana Islands', 'OT'),

	(now(), 'US','FM','en Federated States of Micronesia', 'AS'),
	(now(), 'US','MH','en Marshall Islands', 'AS'),
	(now(), 'US','PW','en Palau', 'AS'),
	
	(now(), 'US','AE','en Armed Forces', 'MM'),
	(now(), 'US','AA','en Armed Forces Americas (except Canada)', 'MM'),
	(now(), 'US','AP','en Armed Forces Pacific', 'MM');

INSERT INTO `COUNTRYREGIONGROUP` (`datecreated`, `countrycode`, `sortorder`, `regiongroupcode`, `regiongroupname`)
	VALUES 
		(now(), 'US','0001', 'ST',"en States"),
		(now(), 'US','0003', 'OT',"en Overseas Territories"),
		(now(), 'US','0004', 'AS',"en Associated States of the United States"),
		(now(), 'US','0005', 'MM',"en US Military mail");

#
# DDL END
#

SET FOREIGN_KEY_CHECKS = 1;
