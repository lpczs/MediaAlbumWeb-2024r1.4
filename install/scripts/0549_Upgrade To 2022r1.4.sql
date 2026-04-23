#
SET FOREIGN_KEY_CHECKS = 0;

#
# DDL START
#

UPDATE `SYSTEMCONFIG` SET `webversiondate` = '2023-01-27';
UPDATE `SYSTEMCONFIG` SET `webversionnumber` = '2022.1.4';
UPDATE `SYSTEMCONFIG` SET `webversionstring`= '2022r1.4';

CREATE TABLE `OAUTHPROVIDER`
(
    `id` bigint unsigned auto_increment primary key,
    `providername` varchar(200)             not null,
    `scopes`       varchar(2000) default '' not null,
    `clientid`     varchar(2000) default '' not null,
    `clientsecret` varchar(2000) default '' not null,
    `provider`     varchar(2000) default '' not null,
    `authurl`      varchar(2000) default '' not null,
    `tokenurl`     varchar(2000) default '' not null,
    `ownerurl`     varchar(2000) default '' not null,
    `datecreated`  datetime                 null,
    `tenantid`     varchar(2000) default '' not null
);

CREATE TABLE `OAUTHREFRESHTOKEN`
(
    `id` bigint unsigned auto_increment primary key,
    `providerid`   int unsigned  null,
    `authemail`    varchar(320)  not null,
    `refreshtoken` varchar(2000) not null,
    `datecreated`  datetime      null
);

CREATE INDEX `authemail` ON `OAUTHREFRESHTOKEN` (`authemail`);
CREATE INDEX `providerid` ON `OAUTHREFRESHTOKEN` (`providerid`);

ALTER TABLE `BRANDING` ADD COLUMN `oauthprovider` bigint unsigned default '0' AFTER `active`;
ALTER TABLE `BRANDING` ADD COLUMN `oauthtoken` bigint unsigned default '0' AFTER `oauthprovider`;

ALTER TABLE `OUTPUTFORMATS`
    ADD COLUMN `jobticketcolourspace` INT NOT NULL DEFAULT 1 AFTER `bleedoverlapwidth`,
    ADD COLUMN `jobticketcolour` VARCHAR(20) NOT NULL DEFAULT "100,100,100,100" AFTER `jobticketcolourspace`,
    ADD COLUMN `leftpageslugbarcodeheight` VARCHAR(30) NOT NULL DEFAULT '0.0' AFTER `rightpagefilenameformat`,
    ADD COLUMN `rightpageslugbarcodeheight` VARCHAR(30) NOT NULL DEFAULT '0.0' AFTER `leftpageslugbarcodeheight`,
    ADD COLUMN `cover1slugbarcodeheight` VARCHAR(30) NOT NULL DEFAULT '0.0' AFTER `cover2filenameformat`,
    ADD COLUMN `cover2slugbarcodeheight` VARCHAR(30) NOT NULL DEFAULT '0.0' AFTER `cover1slugbarcodeheight`;

UPDATE `OUTPUTFORMATS`
	SET `leftpageoptions` = CONCAT(`leftpageoptions`, "-0000"), `rightpageoptions` = CONCAT(`rightpageoptions`, "-0000"),
		`frontcoveroptions` = CONCAT(`frontcoveroptions`, "-0000"), `backcoveroptions` = CONCAT(`backcoveroptions`, "-0000");

#
# DDL END
#

SET FOREIGN_KEY_CHECKS = 1;

