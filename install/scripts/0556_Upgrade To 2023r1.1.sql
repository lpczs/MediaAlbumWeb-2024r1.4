#
SET FOREIGN_KEY_CHECKS = 0;

#
# DDL START
#

UPDATE `SYSTEMCONFIG` SET `webversiondate` = '2023-12-05';
UPDATE `SYSTEMCONFIG` SET `webversionnumber` = '2023.1.1';
UPDATE `SYSTEMCONFIG` SET `webversionstring`= '2023r1.1';

ALTER TABLE `productcollectionlink`
    DROP COLUMN `productwizardmodeonline`;

UPDATE `productcollectionlink` SET `productselectormodedesktop` = 0;

#
# DDL END
#

SET FOREIGN_KEY_CHECKS = 1;
