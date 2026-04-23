#
SET FOREIGN_KEY_CHECKS = 0;

#
# DDL START
#


ALTER TABLE `VOUCHERS` ADD COLUMN `promotioncode` VARCHAR(50) NOT NULL AFTER `datecreated`,
 ADD INDEX newindex(`promotioncode`);
 
CREATE TABLE  `VOUCHERPROMOTIONS` (
  `id` int(11) NOT NULL auto_increment,
  `datecreated` datetime NOT NULL default '0000-00-00 00:00:00',
  `code` varchar(50) NOT NULL,
  `name` varchar(100) NOT NULL,
  `startdate` datetime NOT NULL default '0000-00-00 00:00:00',
  `enddate` datetime NOT NULL default '0000-00-00 00:00:00',
  `active` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `ORDERHEADER` ADD COLUMN `voucherpromotioncode` VARCHAR(50) NOT NULL AFTER `basecurrencycode`,
 ADD COLUMN `voucherpromotionname` VARCHAR(100) NOT NULL AFTER `voucherpromotioncode`; 

#
# DDL END
#

SET FOREIGN_KEY_CHECKS = 1;
