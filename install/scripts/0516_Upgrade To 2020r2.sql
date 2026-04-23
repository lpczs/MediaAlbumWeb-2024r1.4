#
SET FOREIGN_KEY_CHECKS = 0;

#
# DDL START
#

UPDATE `SYSTEMCONFIG` SET `webversiondate` = '2020-05-04';
UPDATE `SYSTEMCONFIG` SET `webversionnumber` = '2020.2.0';
UPDATE `SYSTEMCONFIG` SET `webversionstring`= '2020r2';


INSERT INTO `PAYMENTMETHODS` (`datecreated`,`code`,`name`,`availablewhenshipping`,`availablewhennotshipping`,`active`) VALUES (now(),'KLARNA','en Klarna','1','1','0');

#
# DDL END
#

SET FOREIGN_KEY_CHECKS = 1;

