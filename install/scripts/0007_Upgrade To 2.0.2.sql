#
SET FOREIGN_KEY_CHECKS = 0;

#
# DDL START
#


ALTER TABLE `LICENSEKEYS` 
	ADD COLUMN `usedefaultcurrency` TINYINT(1) NOT NULL DEFAULT 1 AFTER `webbrandcode`;

ALTER TABLE `LICENSEKEYS` 
	ADD COLUMN `currencycode` VARCHAR(20) NOT NULL AFTER `usedefaultcurrency`;

ALTER TABLE `CURRENCIES` 
	ADD COLUMN `exchangeratedateset` DATETIME NOT NULL AFTER `decimalplaces`;

ALTER TABLE `CURRENCIES` 
	ADD COLUMN `exchangerate` DECIMAL(10,4) NOT NULL DEFAULT 1.0000 AFTER `exchangeratedateset`;

ALTER TABLE `ORDERHEADER` 
	ADD COLUMN `currencyexchangerate` DECIMAL(10,4) NOT NULL DEFAULT 1.0000 AFTER `currencydecimalplaces`;

ALTER TABLE `ORDERHEADER` 
	ADD COLUMN `basecurrencycode` VARCHAR(20) NOT NULL AFTER `currencyexchangerate`;

ALTER TABLE `ORDERHEADER` 
	MODIFY `currencyname` varchar(1024) NOT NULL;

UPDATE `ORDERHEADER` 
	SET `basecurrencycode` = `currencycode`; 

#
# DDL END
#

SET FOREIGN_KEY_CHECKS = 1;
