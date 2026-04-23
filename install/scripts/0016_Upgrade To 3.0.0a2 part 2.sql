# Upgrade To 3.0.0a2 part 2 version 17 November 2010
#
#
SET FOREIGN_KEY_CHECKS = 0;

#
# DDL START
#


INSERT INTO `ACTIVITYLOG`
	(`id`, `datecreated`, `sessionid`, `orderid`, `userid`, `userlogin`, `username`, `importance`, `sectioncode`, `actioncode`, `actionnotes`, `success`)
	VALUES (0, now(), 0, 0, 0, '', (SELECT USER()), 0, 'UPGRADE', '3.0.0a2', 'PART 2 STARTED', 1);

# SET companycode based on owner, DROP owner

# COVERS
UPDATE 		`COVERS` c SET `companycode` = IFNULL((SELECT s.`companycode` FROM `sites` s WHERE s.`code` = c.`owner`), "");
ALTER TABLE `COVERS` DROP COLUMN `owner`;

# LICENSEKEYS
UPDATE 		`LICENSEKEYS` l SET `companycode` = IFNULL((SELECT s.`companycode` FROM `sites` s WHERE s.`code` = l.`owner`), "");
ALTER TABLE `LICENSEKEYS` DROP COLUMN `owner`;

# PAPER
UPDATE 		`PAPER` p SET `companycode` = IFNULL((SELECT s.`companycode` FROM `sites` s WHERE s.`code` = p.`owner`), "");
ALTER TABLE `PAPER` DROP COLUMN `owner`;

# PRODUCTS
UPDATE 		`PRODUCTS` p SET `companycode` = IFNULL((SELECT s.`companycode` FROM `sites` s WHERE s.`code` = p.`owner`), "");
ALTER TABLE `PRODUCTS` DROP COLUMN `owner`;

# SHIPPINGRATES
UPDATE 		`SHIPPINGRATES` r SET `companycode` = IFNULL((SELECT s.`companycode` FROM `sites` s WHERE s.`code` = r.`owner`), "");
ALTER TABLE `SHIPPINGRATES` DROP COLUMN `owner`;

# SHIPPINGZONES
UPDATE 		`SHIPPINGZONES` z SET `companycode` = IFNULL((SELECT s.`companycode` FROM `sites` s WHERE s.`code` = z.`owner`), "");
ALTER TABLE `SHIPPINGZONES` DROP COLUMN `owner`;

# VOUCHERPROMOTIONS
UPDATE 		`VOUCHERPROMOTIONS` v SET `companycode` = IFNULL((SELECT s.`companycode` FROM `sites` s WHERE s.`code` = v.`owner`), "");
ALTER TABLE `VOUCHERPROMOTIONS` DROP COLUMN `owner`;

# SET companycode based on owner

UPDATE `USERS` u 		SET `companycode` = IFNULL((SELECT s.`companycode` FROM `sites` s WHERE s.`code` = u.`owner`), "");
UPDATE `BRANDING` b 	SET `companycode` = IFNULL((SELECT s.`companycode` FROM `sites` s WHERE s.`code` = b.`owner`), "");
UPDATE `VOUCHERS` v 	SET `companycode` = IFNULL((SELECT s.`companycode` FROM `sites` s WHERE s.`code` = v.`owner`), "");

INSERT INTO `ACTIVITYLOG`
	(`id`, `datecreated`, `sessionid`, `orderid`, `userid`, `userlogin`, `username`, `importance`, `sectioncode`, `actioncode`, `actionnotes`, `success`)
	VALUES (0, now(), 0, 0, 0, '', (SELECT USER()), 0, 'UPGRADE', '3.0.0a2', 'PART 2 FINISHED', 1);

#
# DDL END
#

SET FOREIGN_KEY_CHECKS = 1;
