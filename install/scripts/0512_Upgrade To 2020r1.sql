#
SET FOREIGN_KEY_CHECKS = 0;

#
# DDL START
#

UPDATE `SYSTEMCONFIG` SET `webversiondate` = '2020-01-31';
UPDATE `SYSTEMCONFIG` SET `webversionnumber` = '2020.1.0';
UPDATE `SYSTEMCONFIG` SET `webversionstring`= '2020r1';


ALTER TABLE `PRODUCTS` 
    ADD COLUMN `usedefaultimagescalingbefore` tinyint(1) NOT NULL DEFAULT '1' AFTER `minimumprintsperproject`,
    ADD COLUMN `imagescalingbeforeenabled` tinyint(1) NOT NULL DEFAULT '0' AFTER `usedefaultimagescalingbefore`,
    ADD COLUMN `imagescalingbefore` decimal(5,2) NOT NULL DEFAULT '0.00' AFTER `imagescalingbeforeenabled`;

ALTER TABLE `USERS`
    ADD INDEX `webbrandcodeemail` (`webbrandcode` ASC, `emailaddress` ASC);

ALTER TABLE `CONSTANTS`
    ADD COLUMN `customerupdateauthrequired` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0' AFTER `minpasswordscore`;

ALTER TABLE `BRANDING`
    ADD COLUMN `registerusingemail` TINYINT(1) NOT NULL DEFAULT 1 AFTER `defaultcommunicationpreference`,
    ADD COLUMN `sharehidebranding` TINYINT(3) NOT NULL DEFAULT '0' AFTER `orderfrompreview`,
    ADD COLUMN `previewdomainurl` VARCHAR(100) NULL DEFAULT '' AFTER `sharehidebranding`;

#
# DDL END
#

SET FOREIGN_KEY_CHECKS = 1;

