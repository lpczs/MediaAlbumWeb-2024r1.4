SET FOREIGN_KEY_CHECKS = 0;

#
# DDL START
#



CREATE TABLE `BRANDING` (
    `id` int(11) NOT NULL COMMENT '' auto_increment,
    `datecreated` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '',
    `code` varchar(50) NOT NULL DEFAULT '' COMMENT '' COLLATE utf8_general_ci,
    `name` varchar(50) NOT NULL DEFAULT '' COMMENT '' COLLATE utf8_general_ci,
    `applicationname` varchar(50) NOT NULL DEFAULT '' COMMENT '' COLLATE utf8_general_ci,
    `displayurl` varchar(100) NOT NULL DEFAULT '' COMMENT '' COLLATE utf8_general_ci,
    `active` tinyint(1) NOT NULL DEFAULT '0' COMMENT '',
    PRIMARY KEY (`id`),
    UNIQUE `code` (`code`)
) DEFAULT CHARSET=utf8;

ALTER TABLE `APPLICATIONBUILD`
    ADD `webbrandcode` varchar(50) NOT NULL DEFAULT '' COMMENT '' COLLATE utf8_general_ci AFTER win32version;


ALTER TABLE `APPLICATIONFILES`
    ADD `webbrandcode` varchar(50) NOT NULL DEFAULT '' COMMENT '' COLLATE utf8_general_ci AFTER hiddenfromuser;


ALTER TABLE `CCILOG`
    ADD `parentlogid` int(11) NOT NULL DEFAULT '0' COMMENT '' AFTER userid,
    ADD `mode` varchar(20) NOT NULL DEFAULT '' COMMENT '' COLLATE utf8_general_ci AFTER type,
    ADD `addressstatus` varchar(50) NOT NULL DEFAULT '' COMMENT '' COLLATE utf8_general_ci AFTER paymentmeans,
    ADD `payerid` varchar(50) NOT NULL DEFAULT '' COMMENT '' COLLATE utf8_general_ci AFTER addressstatus,
    ADD `payerstatus` varchar(50) NOT NULL DEFAULT '' COMMENT '' COLLATE utf8_general_ci AFTER payerid,
    ADD `business` varchar(100) NOT NULL DEFAULT '' COMMENT '' COLLATE utf8_general_ci AFTER payerstatus,
    ADD `receiveremail` varchar(100) NOT NULL DEFAULT '' COMMENT '' COLLATE utf8_general_ci AFTER business,
    ADD `receiverid` varchar(50) NOT NULL DEFAULT '' COMMENT '' COLLATE utf8_general_ci AFTER receiveremail,
    ADD `pendingreason` varchar(50) NOT NULL DEFAULT '' COMMENT '' COLLATE utf8_general_ci AFTER receiverid,
    ADD `transactiontype` varchar(50) NOT NULL DEFAULT '' COMMENT '' COLLATE utf8_general_ci AFTER pendingreason,
    ADD `settleamount` varchar(20) NOT NULL DEFAULT '' COMMENT '' COLLATE utf8_general_ci AFTER transactiontype,
    ADD `currencycode` varchar(20) NOT NULL DEFAULT '' COMMENT '' COLLATE utf8_general_ci AFTER settleamount,
    ADD `formattedamount` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '' AFTER formattedcardnumber,
    MODIFY `transactionid` varchar(50) NOT NULL DEFAULT '' COMMENT '' COLLATE utf8_general_ci,
    MODIFY `responsecode` varchar(50) NOT NULL DEFAULT '' COMMENT '' COLLATE utf8_general_ci,
    MODIFY `authorisationid` varchar(100) NOT NULL DEFAULT '' COMMENT '' COLLATE utf8_general_ci,
    MODIFY `paymentdate` varchar(50) NOT NULL DEFAULT '' COMMENT '' COLLATE utf8_general_ci,
    MODIFY `formattedtransactionid` varchar(50) NOT NULL DEFAULT '' COMMENT '' COLLATE utf8_general_ci,
    ADD INDEX `transactionid` (`transactionid`);
#
#  Fieldformats of
#    CCILOG.transactionid changed from varchar(20) NOT NULL DEFAULT '' COMMENT '' COLLATE utf8_general_ci to varchar(50) NOT NULL DEFAULT '' COMMENT '' COLLATE utf8_general_ci.
#    CCILOG.responsecode changed from varchar(10) NOT NULL DEFAULT '' COMMENT '' COLLATE utf8_general_ci to varchar(50) NOT NULL DEFAULT '' COMMENT '' COLLATE utf8_general_ci.
#    CCILOG.authorisationid changed from varchar(20) NOT NULL DEFAULT '' COMMENT '' COLLATE utf8_general_ci to varchar(100) NOT NULL DEFAULT '' COMMENT '' COLLATE utf8_general_ci.
#    CCILOG.paymentdate changed from varchar(20) NOT NULL DEFAULT '' COMMENT '' COLLATE utf8_general_ci to varchar(50) NOT NULL DEFAULT '' COMMENT '' COLLATE utf8_general_ci.
#    CCILOG.formattedtransactionid changed from varchar(20) NOT NULL DEFAULT '' COMMENT '' COLLATE utf8_general_ci to varchar(50) NOT NULL DEFAULT '' COMMENT '' COLLATE utf8_general_ci.
#  Possibly data modifications needed!
#

ALTER TABLE `COVERS`
    ADD `minimumpagecount` int(11) NOT NULL DEFAULT '1' COMMENT '' AFTER unitcost,
    ADD `maximumpagecount` int(11) NOT NULL DEFAULT '1000' COMMENT '' AFTER minimumpagecount,
    MODIFY `weight` decimal(10,4) NOT NULL DEFAULT '0.0000' COMMENT '';
#
#  Fieldformat of
#    COVERS.weight changed from decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '' to decimal(10,4) NOT NULL DEFAULT '0.0000' COMMENT ''.
#  Possibly data modifications needed!
#

ALTER TABLE `LICENSEKEYS`
    ADD `webbrandcode` varchar(50) NOT NULL DEFAULT '' COMMENT '' COLLATE utf8_general_ci AFTER keyfilenameversion;


ALTER TABLE `ORDERHEADER`
    ADD `webbrandcode` varchar(50) NOT NULL DEFAULT '' COMMENT '' COLLATE utf8_general_ci AFTER origordernumber,
    ADD `totalsellbeforediscount` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '' AFTER totalcost,
    ADD `totaltaxbeforediscount` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '' AFTER totalsellbeforediscount,
    ADD `totalbeforediscount` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '' AFTER totaltaxbeforediscount,
    ADD `totaldiscount` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '' AFTER totalbeforediscount;


ALTER TABLE `ORDERITEMS`
    MODIFY `unitweight` decimal(10,4) NOT NULL DEFAULT '0.0000' COMMENT '',
    MODIFY `totalweight` decimal(10,4) NOT NULL DEFAULT '0.0000' COMMENT '';
#
#  Fieldformats of
#    ORDERITEMS.unitweight changed from decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '' to decimal(10,4) NOT NULL DEFAULT '0.0000' COMMENT ''.
#    ORDERITEMS.totalweight changed from decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '' to decimal(10,4) NOT NULL DEFAULT '0.0000' COMMENT ''.
#  Possibly data modifications needed!
#

ALTER TABLE `PAPER`
    MODIFY `weight` decimal(10,4) NOT NULL DEFAULT '0.0000' COMMENT '';
#
#  Fieldformat of
#    PAPER.weight changed from decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '' to decimal(10,4) NOT NULL DEFAULT '0.0000' COMMENT ''.
#  Possibly data modifications needed!
#

ALTER TABLE `PRODUCTS`
    MODIFY `weight` decimal(10,4) NOT NULL DEFAULT '0.0000' COMMENT '';
#
#  Fieldformat of
#    PRODUCTS.weight changed from decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '' to decimal(10,4) NOT NULL DEFAULT '0.0000' COMMENT ''.
#  Possibly data modifications needed!
#

ALTER TABLE `VOUCHERS`
    ADD `maximumqty` int(11) NOT NULL DEFAULT '9999' COMMENT '' AFTER minimumqty,
    ADD `lockqty` tinyint(1) NOT NULL DEFAULT '0' COMMENT '' AFTER maximumqty;


#
# DDL END
#

SET FOREIGN_KEY_CHECKS = 1;


#
# UPDATE ORDERHEADER DATA
#

UPDATE ORDERHEADER SET `totalbeforediscount` = `total`, `totalsellbeforediscount` = `totalsell`, `totaltaxbeforediscount` = `totaltax`;


#
# UPDATE CCILOG DATA
#

UPDATE CCILOG SET `formattedamount` = `amount`;


#
# UPDATE PAYMENT METHODS
#

INSERT INTO `PAYMENTMETHODS` (`id`,`datecreated`,`code`,`name`,`availablewhenshipping`,`availablewhennotshipping`,`active`) VALUES ('5','2008-02-26 13:00:00','PAYPAL','en PayPal','1','1','0');