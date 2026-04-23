SET FOREIGN_KEY_CHECKS = 0;

#
# DDL START
#



ALTER TABLE `ACTIVITYLOG`
    ADD `userlogin` varchar(50) NOT NULL DEFAULT '' COMMENT '' COLLATE utf8_general_ci AFTER userid,
    ADD `sectioncode` varchar(20) NOT NULL DEFAULT '' COMMENT '' COLLATE utf8_general_ci AFTER importance,
    ADD `success` tinyint(1) NOT NULL DEFAULT '0' COMMENT '' AFTER actionnotes,
    MODIFY `datecreated` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '',
    MODIFY `actioncode` varchar(50) NOT NULL DEFAULT '' COMMENT '' COLLATE utf8_general_ci,
    DROP `actionname`,
    MODIFY `actionnotes` varchar(200) NOT NULL DEFAULT '' COMMENT '' COLLATE utf8_general_ci,
    ADD INDEX `datecreated` (`datecreated`);
    
#
#  Fieldformat of
#    ACTIVITYLOG.datecreated changed from timestamp NOT NULL DEFAULT 'CURRENT_TIMESTAMP' COMMENT '' to datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT ''.
#  Possibly data modifications needed!
#

ALTER TABLE `APPLICATIONBUILD`
    MODIFY `datecreated` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '';
#
#  Fieldformat of
#    APPLICATIONBUILD.datecreated changed from timestamp NOT NULL DEFAULT 'CURRENT_TIMESTAMP' COMMENT '' to datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT ''.
#  Possibly data modifications needed!
#

ALTER TABLE `APPLICATIONFILES`
    MODIFY `datecreated` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '';
#
#  Fieldformat of
#    APPLICATIONFILES.datecreated changed from timestamp NOT NULL DEFAULT 'CURRENT_TIMESTAMP' COMMENT '' to datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT ''.
#  Possibly data modifications needed!
#

ALTER TABLE `CCILOG`
    ADD `formattedpaymentdate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '' AFTER paymentmeans,
    ADD `formattedtransactionid` varchar(20) NOT NULL DEFAULT '' COMMENT '' COLLATE utf8_general_ci AFTER formattedpaymentdate,
    ADD `formattedcardnumber` varchar(20) NOT NULL DEFAULT '' COMMENT '' COLLATE utf8_general_ci AFTER formattedtransactionid,
    MODIFY `datecreated` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '';
#
#  Fieldformat of
#    CCILOG.datecreated changed from timestamp NOT NULL DEFAULT 'CURRENT_TIMESTAMP' COMMENT '' to datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT ''.
#  Possibly data modifications needed!
#

ALTER TABLE `CONSTANTS`
    MODIFY `datecreated` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '';
#
#  Fieldformat of
#    CONSTANTS.datecreated changed from timestamp NOT NULL DEFAULT 'CURRENT_TIMESTAMP' COMMENT '' to datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT ''.
#  Possibly data modifications needed!
#

ALTER TABLE `COVERPRICES`
    MODIFY `datecreated` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '';
#
#  Fieldformat of
#    COVERPRICES.datecreated changed from timestamp NOT NULL DEFAULT 'CURRENT_TIMESTAMP' COMMENT '' to datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT ''.
#  Possibly data modifications needed!
#

ALTER TABLE `COVERS`
    MODIFY `datecreated` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '';
#
#  Fieldformat of
#    COVERS.datecreated changed from timestamp NOT NULL DEFAULT 'CURRENT_TIMESTAMP' COMMENT '' to datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT ''.
#  Possibly data modifications needed!
#

ALTER TABLE `CURRENCIES`
    MODIFY `datecreated` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '';
#
#  Fieldformat of
#    CURRENCIES.datecreated changed from timestamp NOT NULL DEFAULT 'CURRENT_TIMESTAMP' COMMENT '' to datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT ''.
#  Possibly data modifications needed!
#

ALTER TABLE `LICENSEKEYS`
    ADD `useaddressforbilling` tinyint(1) NOT NULL DEFAULT '0' COMMENT '' AFTER createaccounts,
    ADD `useaddressforshipping` tinyint(1) NOT NULL DEFAULT '0' COMMENT '' AFTER useaddressforbilling,
    ADD `modifyshippingcontactdetails` tinyint(1) NOT NULL DEFAULT '0' COMMENT '' AFTER useaddressforshipping,
    MODIFY `datecreated` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '';
#
#  Fieldformat of
#    LICENSEKEYS.datecreated changed from timestamp NOT NULL DEFAULT 'CURRENT_TIMESTAMP' COMMENT '' to datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT ''.
#  Possibly data modifications needed!
#

ALTER TABLE `ORDERHEADER`
    ADD `ownercode` varchar(50) NOT NULL DEFAULT '' COMMENT '' COLLATE utf8_general_ci AFTER datecreated,
    ADD `archived` tinyint(1) NOT NULL DEFAULT '0' COMMENT '' AFTER canreorder,
    ADD `archivedate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '' AFTER archived,
    MODIFY `datecreated` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '';
#
#  Fieldformat of
#    ORDERHEADER.datecreated changed from timestamp NOT NULL DEFAULT 'CURRENT_TIMESTAMP' COMMENT '' to datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT ''.
#  Possibly data modifications needed!
#

ALTER TABLE `ORDERITEMS`
    ADD `productheight` decimal(10,6) NOT NULL DEFAULT '0.000000' COMMENT '' AFTER productname,
    ADD `productwidth` decimal(10,6) NOT NULL DEFAULT '0.000000' COMMENT '' AFTER productheight,
    ADD `covername` varchar(50) NOT NULL DEFAULT '' COMMENT '' COLLATE utf8_general_ci AFTER covercode,
    ADD `papername` varchar(50) NOT NULL DEFAULT '' COMMENT '' COLLATE utf8_general_ci AFTER papercode,
    ADD `filesreceivedtimestamp` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '' AFTER uploadmethod,
    ADD `filesreceiveduserid` int(11) NOT NULL DEFAULT '0' COMMENT '' AFTER filesreceivedtimestamp,
    ADD `decrypttimestamp` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '' AFTER filesreceiveduserid,
    ADD `decryptuserid` int(11) NOT NULL DEFAULT '0' COMMENT '' AFTER decrypttimestamp,
    ADD `converttimestamp` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '' AFTER decryptuserid,
    ADD `convertuserid` int(11) NOT NULL DEFAULT '0' COMMENT '' AFTER converttimestamp,
    ADD `convertoutputformatcode` varchar(50) NOT NULL DEFAULT '' COMMENT '' COLLATE utf8_general_ci AFTER convertuserid,
    ADD `outputtimestamp` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '' AFTER convertoutputformatcode,
    ADD `outputdeviceid` int(11) NOT NULL DEFAULT '0' COMMENT '' AFTER outputuserid,
    ADD `finishtimestamp` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '' AFTER outputdeviceid,
    ADD `canupload` tinyint(1) NOT NULL DEFAULT '1' COMMENT '' AFTER previewsonline,
    ADD `statustimestamp` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '' AFTER statusdescription,
    MODIFY `datecreated` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '',
    CHANGE COLUMN `taxtotal` `totaltax` DECIMAL(10,2) NOT NULL DEFAULT '0.00',
    DROP `outputdate`,
    ADD INDEX `status` (`status`);
#
#  Fieldformat of
#    ORDERITEMS.datecreated changed from timestamp NOT NULL DEFAULT 'CURRENT_TIMESTAMP' COMMENT '' to datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT ''.
#  Possibly data modifications needed!
#

ALTER TABLE `ORDERSHIPPING`
    MODIFY `datecreated` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '';
#
#  Fieldformat of
#    ORDERSHIPPING.datecreated changed from timestamp NOT NULL DEFAULT 'CURRENT_TIMESTAMP' COMMENT '' to datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT ''.
#  Possibly data modifications needed!
#

ALTER TABLE `OUTPUTDEVICES`
    MODIFY `datecreated` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '';
#
#  Fieldformat of
#    OUTPUTDEVICES.datecreated changed from timestamp NOT NULL DEFAULT 'CURRENT_TIMESTAMP' COMMENT '' to datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT ''.
#  Possibly data modifications needed!
#

ALTER TABLE `OUTPUTFORMATS`
    MODIFY `datecreated` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '';
#
#  Fieldformat of
#    OUTPUTFORMATS.datecreated changed from timestamp NOT NULL DEFAULT 'CURRENT_TIMESTAMP' COMMENT '' to datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT ''.
#  Possibly data modifications needed!
#

ALTER TABLE `PAPER`
    MODIFY `datecreated` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '';
#
#  Fieldformat of
#    PAPER.datecreated changed from timestamp NOT NULL DEFAULT 'CURRENT_TIMESTAMP' COMMENT '' to datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT ''.
#  Possibly data modifications needed!
#

ALTER TABLE `PAPERPRICES`
    MODIFY `datecreated` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '';
#
#  Fieldformat of
#    PAPERPRICES.datecreated changed from timestamp NOT NULL DEFAULT 'CURRENT_TIMESTAMP' COMMENT '' to datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT ''.
#  Possibly data modifications needed!
#

ALTER TABLE `PAYMENTMETHODS`
    MODIFY `datecreated` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '';
#
#  Fieldformat of
#    PAYMENTMETHODS.datecreated changed from timestamp NOT NULL DEFAULT 'CURRENT_TIMESTAMP' COMMENT '' to datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT ''.
#  Possibly data modifications needed!
#

ALTER TABLE `PRODUCTPRICES`
    MODIFY `datecreated` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '';
#
#  Fieldformat of
#    PRODUCTPRICES.datecreated changed from timestamp NOT NULL DEFAULT 'CURRENT_TIMESTAMP' COMMENT '' to datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT ''.
#  Possibly data modifications needed!
#

ALTER TABLE `PRODUCTS`
    MODIFY `datecreated` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '';
#
#  Fieldformat of
#    PRODUCTS.datecreated changed from timestamp NOT NULL DEFAULT 'CURRENT_TIMESTAMP' COMMENT '' to datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT ''.
#  Possibly data modifications needed!
#

ALTER TABLE `SESSIONDATA`
    ADD `sessionenabled` tinyint(1) NOT NULL DEFAULT '0' COMMENT '' AFTER sessionactive,
    MODIFY `datecreated` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '';
#
#  Fieldformat of
#    SESSIONDATA.datecreated changed from timestamp NOT NULL DEFAULT 'CURRENT_TIMESTAMP' COMMENT '' to datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT ''.
#  Possibly data modifications needed!
#

ALTER TABLE `SHIPPINGMETHODS`
    MODIFY `datecreated` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '';
#
#  Fieldformat of
#    SHIPPINGMETHODS.datecreated changed from timestamp NOT NULL DEFAULT 'CURRENT_TIMESTAMP' COMMENT '' to datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT ''.
#  Possibly data modifications needed!
#

ALTER TABLE `SHIPPINGRATES`
    MODIFY `datecreated` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '';
#
#  Fieldformat of
#    SHIPPINGRATES.datecreated changed from timestamp NOT NULL DEFAULT 'CURRENT_TIMESTAMP' COMMENT '' to datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT ''.
#  Possibly data modifications needed!
#

ALTER TABLE `SHIPPINGZONES`
    MODIFY `datecreated` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '';
#
#  Fieldformat of
#    SHIPPINGZONES.datecreated changed from timestamp NOT NULL DEFAULT 'CURRENT_TIMESTAMP' COMMENT '' to datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT ''.
#  Possibly data modifications needed!
#

ALTER TABLE `TAXRATES`
    MODIFY `datecreated` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '';
#
#  Fieldformat of
#    TAXRATES.datecreated changed from timestamp NOT NULL DEFAULT 'CURRENT_TIMESTAMP' COMMENT '' to datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT ''.
#  Possibly data modifications needed!
#

ALTER TABLE `TAXZONES`
    MODIFY `datecreated` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '';
#
#  Fieldformat of
#    TAXZONES.datecreated changed from timestamp NOT NULL DEFAULT 'CURRENT_TIMESTAMP' COMMENT '' to datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT ''.
#  Possibly data modifications needed!
#

ALTER TABLE `USERS`
    ADD `uselicensekeyforshippingaddress` tinyint(1) NOT NULL DEFAULT '0' COMMENT '' AFTER registeredtaxnumber,
    ADD `uselicensekeyforbillingaddress` tinyint(1) NOT NULL DEFAULT '0' COMMENT '' AFTER modifyshippingcontactdetails,
    MODIFY `datecreated` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '';
#
#  Fieldformat of
#    USERS.datecreated changed from timestamp NOT NULL DEFAULT 'CURRENT_TIMESTAMP' COMMENT '' to datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT ''.
#  Possibly data modifications needed!
#

ALTER TABLE `VOUCHERS`
    MODIFY `datecreated` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '';
#
#  Fieldformat of
#    VOUCHERS.datecreated changed from timestamp NOT NULL DEFAULT 'CURRENT_TIMESTAMP' COMMENT '' to datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT ''.
#  Possibly data modifications needed!
#

#
# DDL END
#

SET FOREIGN_KEY_CHECKS = 1;