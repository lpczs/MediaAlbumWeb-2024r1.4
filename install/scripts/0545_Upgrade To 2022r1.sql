#
SET FOREIGN_KEY_CHECKS = 0;

#
# DDL START
#

UPDATE `SYSTEMCONFIG` SET `webversiondate` = '2022-07-04';
UPDATE `SYSTEMCONFIG` SET `webversionnumber` = '2022.1.0';
UPDATE `SYSTEMCONFIG` SET `webversionstring`= '2022r1';

ALTER TABLE `COMPONENTS`
ADD COLUMN `moreinfolinkurl` VARCHAR(100) NOT NULL DEFAULT '' AFTER `info`,
ADD COLUMN `moreinfolinktext` VARCHAR(1024) NOT NULL DEFAULT '' AFTER `moreinfolinkurl`;

TRUNCATE `cachedata`;

ALTER TABLE `pricelink`
ADD COLUMN `linkedproductcode` VARCHAR(50) NOT NULL DEFAULT '' AFTER `productcode`;

ALTER TABLE `products`
ADD INDEX `deleted_companycode` (`deleted` ASC, `companycode` ASC) VISIBLE;

ALTER TABLE `TRIGGERS`
ADD COLUMN `webhook1url` VARCHAR(200) NOT NULL DEFAULT '' AFTER `task1`,
ADD COLUMN `webhook2url` VARCHAR(200) NOT NULL DEFAULT '' AFTER `task2`;

ALTER TABLE `TRIGGERS`
DROP COLUMN `task2`,
DROP COLUMN `task1`;

INSERT INTO `TASKS` (
  `datecreated`, `taskcode`, `taskname`, `intervaltype`, `intervalvalue`, `lastruntime`,
  `nextruntime`, `statuscode`, `statusmessage`, `runstatus`, `maxruncount`, `internal`,
  `scriptfilename`, `deleteexpiredinterval`, `active`
)
VALUES (
  NOW(), 'TAOPIX_WEBHOOK', 'en Taopix Webhook Task', 1, '1', '0000-00-00 00:00:00',
  '0000-00-00 00:00:00', 0, '', 0, 10, 1, 'webhookTask.php', 10, 1
);

ALTER TABLE `COMPONENTCATEGORIES`
	ADD column `displaystage` int not null default 2 AFTER `active`;

ALTER TABLE `BRANDING`
	ADD column `componentupsellsettings`int not null default 3 AFTER `averagepicturesperpage`;

ALTER TABLE `LICENSEKEYS`
	ADD column `componentupsellsettings` int not null default 3 AFTER `averagepicturesperpage`,
	ADD column `usedefaultcomponentupsellsettings` int not null default 1 AFTER `averagepicturesperpage`;

UPDATE `COMPONENTCATEGORIES` SET `displaystage` = 0
WHERE `code` IN ('SINGLEPRINT', 'SINGLEPRINTOPTION');

UPDATE `COMPONENTCATEGORIES` SET `displaystage` = 3
WHERE `code` IN ('CALENDARCUSTOMISATION', 'TAOPIXAI');


#
# DDL END
#

SET FOREIGN_KEY_CHECKS = 1;

