#
SET FOREIGN_KEY_CHECKS = 0;

#
# DDL START
#

UPDATE `SYSTEMCONFIG` SET `webversiondate` = '2017-08-31';
UPDATE `SYSTEMCONFIG` SET `webversionnumber` = '2017.3.0';
UPDATE `SYSTEMCONFIG` SET `webversionstring`= '2017r3';


ALTER TABLE `DATAPOLICIES`
ADD COLUMN `orderedprojects` tinyint(1) NOT NULL DEFAULT 0 AFTER `notorderedemail`,
ADD COLUMN `orderedage` int(11) NOT NULL DEFAULT 90 AFTER `orderedprojects`,
ADD COLUMN `ordereddays` int(11) NOT NULL DEFAULT 60 AFTER `orderedage`,
ADD COLUMN `orderedemail` tinyint(1) NOT NULL DEFAULT 0 AFTER `ordereddays`,
ADD UNIQUE INDEX `code_UNIQUE` (`code` ASC);


ALTER TABLE `USERS`
ADD COLUMN `usedefaultvouchersettings` TINYINT(1) NOT NULL DEFAULT '1' AFTER `protectfromredaction`,
ADD COLUMN `allowvouchers` TINYINT(1) NOT NULL DEFAULT '1' AFTER `usedefaultvouchersettings`,
ADD COLUMN `allowgiftcards` TINYINT(1) NOT NULL DEFAULT '1' AFTER `allowvouchers`;


ALTER TABLE `LICENSEKEYS`
ADD COLUMN `usedefaultvouchersettings` TINYINT(1) NOT NULL DEFAULT '1' AFTER `onlinedesignerlogolinktooltip`,
ADD COLUMN `allowvouchers` TINYINT(1) NOT NULL DEFAULT '1' AFTER `usedefaultvouchersettings`,
ADD COLUMN `allowgiftcards` TINYINT(1) NOT NULL DEFAULT '1' AFTER `allowvouchers`,
ADD COLUMN `usedefaultsizeandpositionsettings` TINYINT(1) NOT NULL DEFAULT '1' AFTER `allowgiftcards`,
ADD COLUMN `sizeandpositionmeasurementunits` TINYINT(1) NOT NULL DEFAULT '0' AFTER `usedefaultsizeandpositionsettings`,
ADD COLUMN `smartguidesenable` TINYINT(1) NOT NULL DEFAULT 1 AFTER `sizeandpositionmeasurementunits`,
ADD COLUMN `smartguidesobjectguidecolour` VARCHAR(6) NOT NULL DEFAULT '00CCFF' AFTER `smartguidesenable`,
ADD COLUMN `smartguidespageguidecolour` VARCHAR(6) NOT NULL DEFAULT 'FF00FF' AFTER `smartguidesobjectguidecolour`,
ADD COLUMN `usedefaultsmartguidessettings` TINYINT(1) NOT NULL DEFAULT 1 AFTER `smartguidespageguidecolour`;


ALTER TABLE `BRANDING`
ADD COLUMN `googleanalyticsuseridtracking` TINYINT(1) NOT NULL DEFAULT '0' AFTER `googleanalyticscode`,
ADD COLUMN `sizeandpositionmeasurementunits` TINYINT(1) NOT NULL DEFAULT '0' AFTER `onlinedesignerlogolinktooltip`,
ADD COLUMN `smartguidesenable` TINYINT(1) NOT NULL DEFAULT 1 AFTER `sizeandpositionmeasurementunits`,
ADD COLUMN `smartguidesobjectguidecolour` VARCHAR(6) NOT NULL DEFAULT '00CCFF' AFTER `smartguidesenable`,
ADD COLUMN `smartguidespageguidecolour` VARCHAR(6) NOT NULL DEFAULT 'FF00FF' AFTER `smartguidesobjectguidecolour`,
ADD COLUMN `allowfulldesignerproductsinwizarddesigner` TINYINT(3) NOT NULL DEFAULT '0' AFTER `showshufflelayoutoptions`;


ALTER TABLE `APPLICATIONFILES`
ADD INDEX `reftypedeleted` (`ref` ASC, `type` ASC, `deleted` ASC);


ALTER TABLE `PRICELINK`
ADD COLUMN `inheritparentqty` TINYINT(3) UNSIGNED NOT NULL DEFAULT 0 AFTER `pricedescription`;


#
# DDL END
#

SET FOREIGN_KEY_CHECKS = 1;