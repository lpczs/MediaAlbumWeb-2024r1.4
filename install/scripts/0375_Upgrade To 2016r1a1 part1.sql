#
SET FOREIGN_KEY_CHECKS = 0;

#
# DDL START
#

INSERT INTO `ACTIVITYLOG`
	(`id`, `datecreated`, `sessionid`, `orderid`, `userid`, `userlogin`, `username`, `importance`, `sectioncode`, `actioncode`, `actionnotes`, `success`)
	VALUES (0, now(), 0, 0, 0, '', (SELECT USER()), 0, 'UPGRADE', '2016r1a1', 'STARTED', 1);

UPDATE `SYSTEMCONFIG` SET `webversiondate` = '2015-10-08';
UPDATE `SYSTEMCONFIG` SET `webversionnumber` = '2016.1.0.1';
UPDATE `SYSTEMCONFIG` SET `webversionstring`= '2016r1a1';


INSERT IGNORE INTO `currencies` (`datecreated`, `code`, `name`, `isonumber`, `symbolatfront`, `decimalplaces`, `exchangeratedateset`, `exchangerate`, `symbol`) VALUES (now(), 'ARS', 'en Argentine Peso', 032, 1, 2, 0, 1, '$');
INSERT IGNORE INTO `currencies` (`datecreated`, `code`, `name`, `isonumber`, `symbolatfront`, `decimalplaces`, `exchangeratedateset`, `exchangerate`, `symbol`) VALUES (now(), 'AUD', 'en Australian Dollar', 036, 1, 2, 0, 1, '$');
INSERT IGNORE INTO `currencies` (`datecreated`, `code`, `name`, `isonumber`, `symbolatfront`, `decimalplaces`, `exchangeratedateset`, `exchangerate`, `symbol`) VALUES (now(), 'BYR', 'en Belarussian Ruble', 974, 1, 0, 0, 1, 'p.');
INSERT IGNORE INTO `currencies` (`datecreated`, `code`, `name`, `isonumber`, `symbolatfront`, `decimalplaces`, `exchangeratedateset`, `exchangerate`, `symbol`) VALUES (now(), 'INR', 'en Indian Rupee', 356, 1, 2, 0, 1, '₹');
INSERT IGNORE INTO `currencies` (`datecreated`, `code`, `name`, `isonumber`, `symbolatfront`, `decimalplaces`, `exchangeratedateset`, `exchangerate`, `symbol`) VALUES (now(), 'BWP', 'en Pula', 072, 1, 2, 0, 1, 'P');
INSERT IGNORE INTO `currencies` (`datecreated`, `code`, `name`, `isonumber`, `symbolatfront`, `decimalplaces`, `exchangeratedateset`, `exchangerate`, `symbol`) VALUES (now(), 'NOK', 'en Norwegian Krone', 578, 1, 2, 0, 1, 'kr');
INSERT IGNORE INTO `currencies` (`datecreated`, `code`, `name`, `isonumber`, `symbolatfront`, `decimalplaces`, `exchangeratedateset`, `exchangerate`, `symbol`) VALUES (now(), 'BRL', 'en Brazilian Real', 986, 1, 2, 0, 1, 'R$');
INSERT IGNORE INTO `currencies` (`datecreated`, `code`, `name`, `isonumber`, `symbolatfront`, `decimalplaces`, `exchangeratedateset`, `exchangerate`, `symbol`) VALUES (now(), 'CAD', 'en Canadian Dollar', 124, 1, 2, 0, 1, '$');
INSERT IGNORE INTO `currencies` (`datecreated`, `code`, `name`, `isonumber`, `symbolatfront`, `decimalplaces`, `exchangeratedateset`, `exchangerate`, `symbol`) VALUES (now(), 'CLP', 'en Chilean Peso', 152, 1, 0, 0, 1, '$');
INSERT IGNORE INTO `currencies` (`datecreated`, `code`, `name`, `isonumber`, `symbolatfront`, `decimalplaces`, `exchangeratedateset`, `exchangerate`, `symbol`) VALUES (now(), 'CNY', 'en Yuan Renminbi', 156, 1, 2, 0, 1, '¥');
INSERT IGNORE INTO `currencies` (`datecreated`, `code`, `name`, `isonumber`, `symbolatfront`, `decimalplaces`, `exchangeratedateset`, `exchangerate`, `symbol`) VALUES (now(), 'COP', 'en Colombian Peso', 170, 1, 2, 0, 1, '$');
INSERT IGNORE INTO `currencies` (`datecreated`, `code`, `name`, `isonumber`, `symbolatfront`, `decimalplaces`, `exchangeratedateset`, `exchangerate`, `symbol`) VALUES (now(), 'NZD', 'en New Zealand Dollar', 554, 1, 2, 0, 1, '$');
INSERT IGNORE INTO `currencies` (`datecreated`, `code`, `name`, `isonumber`, `symbolatfront`, `decimalplaces`, `exchangeratedateset`, `exchangerate`, `symbol`) VALUES (now(), 'CZK', 'en Czech Koruna', 203, 0, 2, 0, 1, 'Kč');
INSERT IGNORE INTO `currencies` (`datecreated`, `code`, `name`, `isonumber`, `symbolatfront`, `decimalplaces`, `exchangeratedateset`, `exchangerate`, `symbol`) VALUES (now(), 'DKK', 'en Danish Krone', 208, 1, 2, 0, 1, 'kr');
INSERT IGNORE INTO `currencies` (`datecreated`, `code`, `name`, `isonumber`, `symbolatfront`, `decimalplaces`, `exchangeratedateset`, `exchangerate`, `symbol`) VALUES (now(), 'HKD', 'en Hong Kong Dollar', 344, 1, 2, 0, 1, 'HK$');
INSERT IGNORE INTO `currencies` (`datecreated`, `code`, `name`, `isonumber`, `symbolatfront`, `decimalplaces`, `exchangeratedateset`, `exchangerate`, `symbol`) VALUES (now(), 'HUF', 'en Forint', 348, 1, 2, 0, 1, 'Ft');
INSERT IGNORE INTO `currencies` (`datecreated`, `code`, `name`, `isonumber`, `symbolatfront`, `decimalplaces`, `exchangeratedateset`, `exchangerate`, `symbol`) VALUES (now(), 'ISK', 'en Iceland Krona', 352, 1, 0, 0, 1, 'kr');
INSERT IGNORE INTO `currencies` (`datecreated`, `code`, `name`, `isonumber`, `symbolatfront`, `decimalplaces`, `exchangeratedateset`, `exchangerate`, `symbol`) VALUES (now(), 'IDR', 'en Rupiah', 360, 1, 2, 0, 1, 'Rp');
INSERT IGNORE INTO `currencies` (`datecreated`, `code`, `name`, `isonumber`, `symbolatfront`, `decimalplaces`, `exchangeratedateset`, `exchangerate`, `symbol`) VALUES (now(), 'JPY', 'en Yen', 392, 1, 0, 0, 1, '¥');
INSERT IGNORE INTO `currencies` (`datecreated`, `code`, `name`, `isonumber`, `symbolatfront`, `decimalplaces`, `exchangeratedateset`, `exchangerate`, `symbol`) VALUES (now(), 'KES', 'en Kenyan Shilling', 404, 1, 2, 0, 1, 'KSh');
INSERT IGNORE INTO `currencies` (`datecreated`, `code`, `name`, `isonumber`, `symbolatfront`, `decimalplaces`, `exchangeratedateset`, `exchangerate`, `symbol`) VALUES (now(), 'KRW', 'en Won', 410, 1, 0, 0, 1, '₩');
INSERT IGNORE INTO `currencies` (`datecreated`, `code`, `name`, `isonumber`, `symbolatfront`, `decimalplaces`, `exchangeratedateset`, `exchangerate`, `symbol`) VALUES (now(), 'KWD', 'en Kuwaiti Dinar', 414, 0, 3, 0, 1, 'ك');
INSERT IGNORE INTO `currencies` (`datecreated`, `code`, `name`, `isonumber`, `symbolatfront`, `decimalplaces`, `exchangeratedateset`, `exchangerate`, `symbol`) VALUES (now(), 'ZAR', 'en Rand', 710, 1, 2, 0, 1, 'R');
INSERT IGNORE INTO `currencies` (`datecreated`, `code`, `name`, `isonumber`, `symbolatfront`, `decimalplaces`, `exchangeratedateset`, `exchangerate`, `symbol`) VALUES (now(), 'CHF', 'en Swiss Franc', 756, 1, 2, 0, 1, 'CHF');
INSERT IGNORE INTO `currencies` (`datecreated`, `code`, `name`, `isonumber`, `symbolatfront`, `decimalplaces`, `exchangeratedateset`, `exchangerate`, `symbol`) VALUES (now(), 'MOP', 'en Pataca', 446, 1, 2, 0, 1, 'MOP$');
INSERT IGNORE INTO `currencies` (`datecreated`, `code`, `name`, `isonumber`, `symbolatfront`, `decimalplaces`, `exchangeratedateset`, `exchangerate`, `symbol`) VALUES (now(), 'MYR', 'en Malaysian Ringgit', 458, 1, 2, 0, 1, 'RM');
INSERT IGNORE INTO `currencies` (`datecreated`, `code`, `name`, `isonumber`, `symbolatfront`, `decimalplaces`, `exchangeratedateset`, `exchangerate`, `symbol`) VALUES (now(), 'MXN', 'en Mexican Peso', 484, 1, 2, 0, 1, '$');
INSERT IGNORE INTO `currencies` (`datecreated`, `code`, `name`, `isonumber`, `symbolatfront`, `decimalplaces`, `exchangeratedateset`, `exchangerate`, `symbol`) VALUES (now(), 'MZN', 'en Mozambique Metical', 943, 1, 2, 0, 1, 'MT');
INSERT IGNORE INTO `currencies` (`datecreated`, `code`, `name`, `isonumber`, `symbolatfront`, `decimalplaces`, `exchangeratedateset`, `exchangerate`, `symbol`) VALUES (now(), 'NAD', 'en Namibia Dollar', 516, 1, 2, 0, 1, '$');
INSERT IGNORE INTO `currencies` (`datecreated`, `code`, `name`, `isonumber`, `symbolatfront`, `decimalplaces`, `exchangeratedateset`, `exchangerate`, `symbol`) VALUES (now(), 'PEN', 'en Nuevo Sol', 604, 1, 2, 0, 1, 'S/.');
INSERT IGNORE INTO `currencies` (`datecreated`, `code`, `name`, `isonumber`, `symbolatfront`, `decimalplaces`, `exchangeratedateset`, `exchangerate`, `symbol`) VALUES (now(), 'PHP', 'en Philippine Peso', 608, 1, 2, 0, 1, '₱');
INSERT IGNORE INTO `currencies` (`datecreated`, `code`, `name`, `isonumber`, `symbolatfront`, `decimalplaces`, `exchangeratedateset`, `exchangerate`, `symbol`) VALUES (now(), 'PLN', 'en Zloty', 985, 1, 2, 0, 1, 'zł');
INSERT IGNORE INTO `currencies` (`datecreated`, `code`, `name`, `isonumber`, `symbolatfront`, `decimalplaces`, `exchangeratedateset`, `exchangerate`, `symbol`) VALUES (now(), 'RUB', 'en Russian Ruble', 643, 1, 2, 0, 1, 'руб');
INSERT IGNORE INTO `currencies` (`datecreated`, `code`, `name`, `isonumber`, `symbolatfront`, `decimalplaces`, `exchangeratedateset`, `exchangerate`, `symbol`) VALUES (now(), 'SGD', 'en Singapore Dollar', 702, 1, 2, 0, 1, '$');
INSERT IGNORE INTO `currencies` (`datecreated`, `code`, `name`, `isonumber`, `symbolatfront`, `decimalplaces`, `exchangeratedateset`, `exchangerate`, `symbol`) VALUES (now(), 'LKR', 'en Sri Lanka Rupee', 144, 1, 2, 0, 1, '₨');
INSERT IGNORE INTO `currencies` (`datecreated`, `code`, `name`, `isonumber`, `symbolatfront`, `decimalplaces`, `exchangeratedateset`, `exchangerate`, `symbol`) VALUES (now(), 'SEK', 'en Swedish Krona', 752, 1, 2, 0, 1, 'kr');
INSERT IGNORE INTO `currencies` (`datecreated`, `code`, `name`, `isonumber`, `symbolatfront`, `decimalplaces`, `exchangeratedateset`, `exchangerate`, `symbol`) VALUES (now(), 'TWD', 'en New Taiwan Dollar', 901, 1, 2, 0, 1, 'NT$');
INSERT IGNORE INTO `currencies` (`datecreated`, `code`, `name`, `isonumber`, `symbolatfront`, `decimalplaces`, `exchangeratedateset`, `exchangerate`, `symbol`) VALUES (now(), 'THB', 'en Baht', 764, 1, 2, 0, 1, '฿');
INSERT IGNORE INTO `currencies` (`datecreated`, `code`, `name`, `isonumber`, `symbolatfront`, `decimalplaces`, `exchangeratedateset`, `exchangerate`, `symbol`) VALUES (now(), 'UAH', 'en Hryvnia', 980, 1, 2, 0, 1, '₴');
INSERT IGNORE INTO `currencies` (`datecreated`, `code`, `name`, `isonumber`, `symbolatfront`, `decimalplaces`, `exchangeratedateset`, `exchangerate`, `symbol`) VALUES (now(), 'AED', 'en UAE Dirham', 784, 0, 2, 0, 1, 'د.إ');

INSERT INTO `ACTIVITYLOG`
(`id`, `datecreated`, `sessionid`, `orderid`, `userid`, `userlogin`, `username`, `importance`, `sectioncode`, `actioncode`, `actionnotes`, `success`)
VALUES (0, now(), 0, 0, 0, '', (SELECT USER()), 0, 'UPGRADE', '2016r1a1', 'FINISHED', 1);


#
# DDL END
#

SET FOREIGN_KEY_CHECKS = 1;