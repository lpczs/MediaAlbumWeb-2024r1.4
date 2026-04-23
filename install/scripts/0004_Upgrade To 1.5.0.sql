SET FOREIGN_KEY_CHECKS = 0;

#
# DDL START
#

ALTER TABLE `CCILOG`
    MODIFY `responsecode` varchar(100) NOT NULL DEFAULT '' COMMENT '' COLLATE utf8_general_ci,
    ADD `payeremail` VARCHAR(100) NOT NULL DEFAULT '' COMMENT '' COLLATE utf8_general_ci AFTER `addressstatus`,
    ADD `charges` VARCHAR(20) NOT NULL DEFAULT '' COMMENT '' COLLATE utf8_general_ci AFTER `amount`,
    ADD `formattedcharges` DECIMAL(10,2) NOT NULL DEFAULT '0.00' COMMENT '' AFTER `formattedamount`, 
    ADD `formattedauthorisationid` VARCHAR(100) NOT NULL DEFAULT '' COMMENT '' COLLATE utf8_general_ci AFTER `formattedtransactionid`;
#
#  Fieldformat of
#    CCILOG.responsecode changed from varchar(50) NOT NULL DEFAULT '' COMMENT '' COLLATE utf8_general_ci to varchar(100) NOT NULL DEFAULT '' COMMENT '' COLLATE utf8_general_ci.
#  Possibly data modifications needed!
#


ALTER TABLE `COVERPRICES`
    DROP `info`;


ALTER TABLE `COVERS`
    ADD `previewtype` varchar(100) NOT NULL DEFAULT '' COMMENT '' COLLATE utf8_general_ci AFTER info;


ALTER TABLE `LICENSEKEYS`
	MODIFY `keyfilename` varchar(100) NOT NULL DEFAULT '' COMMENT '' COLLATE utf8_general_ci;


ALTER TABLE `ORDERHEADER` 
    ADD `shippingtotalweight` DECIMAL(10,4) NOT NULL DEFAULT '0.0000' AFTER `shippingtotaltax`,
    MODIFY `vouchername` varchar(1024) NOT NULL,
    MODIFY `paymentmethodname` varchar(1024) NOT NULL;


ALTER TABLE `ORDERITEMS`
	CHANGE `unitweight` `productunitweight` DECIMAL(10,4) NOT NULL DEFAULT '0.0000',
    CHANGE `totalweight` `producttotalweight` DECIMAL(10,4) NOT NULL DEFAULT '0.0000',
    ADD `productdefaultcovercode` varchar(50) NOT NULL DEFAULT '' COMMENT '' COLLATE utf8_general_ci AFTER productwidth,
    ADD `productdefaultpapercode` varchar(50) NOT NULL DEFAULT '' COMMENT '' COLLATE utf8_general_ci AFTER productdefaultcovercode,
    ADD `productdefaultpagecount` int(11) NOT NULL DEFAULT '0' COMMENT '' AFTER productdefaultpapercode,
    ADD `covercount` int(11) NOT NULL DEFAULT 0 AFTER `productdefaultpagecount`,
    ADD `papercount` int(11) NOT NULL DEFAULT 0 AFTER `covername`,
    ADD `pagecountpurchased` int(11) NOT NULL DEFAULT '0' COMMENT '' AFTER papername,
    ADD `coverunitweight` decimal(10,4) NOT NULL DEFAULT '0.0000' COMMENT '' AFTER producttotalweight,
    ADD `covertotalweight` decimal(10,4) NOT NULL DEFAULT '0.0000' COMMENT '' AFTER coverunitweight,
    ADD `paperunitweight` decimal(10,4) NOT NULL DEFAULT '0.0000' COMMENT '' AFTER covertotalweight,
    ADD `papertotalweight` decimal(10,4) NOT NULL DEFAULT '0.0000' COMMENT '' AFTER paperunitweight,
    ADD `totalshippingweight` decimal(10,4) NOT NULL DEFAULT '0.0000' COMMENT '' AFTER totaltax,
    ADD `uploadappversion` varchar(20) NOT NULL DEFAULT '' COMMENT '' COLLATE utf8_general_ci AFTER uploadmethod,
    ADD `uploadappplatform` varchar(20) NOT NULL DEFAULT '' COMMENT '' COLLATE utf8_general_ci AFTER uploadappversion,
    ADD `jobticketoutputfilename` varchar(200) NOT NULL DEFAULT '' COMMENT '' COLLATE utf8_general_ci AFTER convertoutputformatcode,
    ADD `pagesoutputfilename` varchar(200) NOT NULL DEFAULT '' COMMENT '' COLLATE utf8_general_ci AFTER jobticketoutputfilename,
    ADD `cover1outputfilename` varchar(200) NOT NULL DEFAULT '' COMMENT '' COLLATE utf8_general_ci AFTER pagesoutputfilename,
    ADD `cover2outputfilename` varchar(200) NOT NULL DEFAULT '' COMMENT '' COLLATE utf8_general_ci AFTER cover1outputfilename,
    ADD `jobticketoutputdevicecode` varchar(50) NOT NULL DEFAULT '' COMMENT '' COLLATE utf8_general_ci AFTER outputuserid,
    ADD `pagesoutputdevicecode` varchar(50) NOT NULL DEFAULT '' COMMENT '' COLLATE utf8_general_ci AFTER jobticketoutputdevicecode,
    ADD `cover1outputdevicecode` varchar(50) NOT NULL DEFAULT '' COMMENT '' COLLATE utf8_general_ci AFTER pagesoutputdevicecode,
    ADD `cover2outputdevicecode` varchar(50) NOT NULL DEFAULT '' COMMENT '' COLLATE utf8_general_ci AFTER cover1outputdevicecode,
    ADD `canuploadpagecountoverride` tinyint(1) NOT NULL DEFAULT '0' COMMENT '' AFTER canupload,
    MODIFY `covername` varchar(1024) NOT NULL,
    MODIFY `papername` varchar(1024) NOT NULL,
    ALTER `pagecount` SET DEFAULT 0,
    MODIFY `taxname` varchar(1024) NOT NULL,
    DROP `outputdeviceid`;


ALTER TABLE `ORDERSHIPPING` 
    ADD `shippingratecalctax` tinyint(1) NOT NULL DEFAULT 0 AFTER `shippingratetaxrate`,
    MODIFY `shippingmethodname` varchar(1024) NOT NULL,
    MODIFY `shippingrateinfo` varchar(1024) NOT NULL,
    MODIFY `shippingratetaxname` varchar(1024) NOT NULL;


ALTER TABLE `OUTPUTFORMATS`
    ADD `leftpagefilenameformat` varchar(100) NOT NULL DEFAULT '' COMMENT '' COLLATE utf8_general_ci AFTER steppagenumbers,
    ADD `rightpagefilenameformat` varchar(100) NOT NULL DEFAULT '' COMMENT '' COLLATE utf8_general_ci AFTER leftpagefilenameformat,
    ADD `cover1separatefile` tinyint(1) NOT NULL DEFAULT '0' COMMENT '' AFTER rightpagefilenameformat,
    ADD `cover1filenameformat` varchar(100) NOT NULL DEFAULT '' COMMENT '' COLLATE utf8_general_ci AFTER cover1separatefile,
    ADD `cover2separatefile` tinyint(1) NOT NULL DEFAULT '0' COMMENT '' AFTER cover1filenameformat,
    ADD `cover2outputwithcover1` tinyint(1) NOT NULL DEFAULT '0' COMMENT '' AFTER cover2separatefile,
    ADD `cover2filenameformat` varchar(100) NOT NULL DEFAULT '' COMMENT '' COLLATE utf8_general_ci AFTER cover2outputwithcover1,
    ADD `jobticketseparatefile` tinyint(1) NOT NULL DEFAULT '0' COMMENT '' AFTER cover2filenameformat,
    ADD `jobticketfilenameformat` varchar(100) NOT NULL DEFAULT '' COMMENT '' COLLATE utf8_general_ci AFTER jobticketseparatefile,
    ADD `jobticketdefaultoutputdevicecode` varchar(50) NOT NULL DEFAULT '' COMMENT '' COLLATE utf8_general_ci AFTER jobticketfilenameformat,
    ADD `pagesdefaultoutputdevicecode` varchar(50) NOT NULL DEFAULT '' COMMENT '' COLLATE utf8_general_ci AFTER jobticketdefaultoutputdevicecode,
    ADD `cover1defaultoutputdevicecode` varchar(50) NOT NULL DEFAULT '' COMMENT '' COLLATE utf8_general_ci AFTER pagesdefaultoutputdevicecode,
    ADD `cover2defaultoutputdevicecode` varchar(50) NOT NULL DEFAULT '' COMMENT '' COLLATE utf8_general_ci AFTER cover1defaultoutputdevicecode;


ALTER TABLE `PAPER`
    ADD `previewtype` varchar(100) NOT NULL DEFAULT '' COMMENT '' COLLATE utf8_general_ci AFTER info;


ALTER TABLE `PAPERPRICES`
    DROP `info`;


ALTER TABLE `PRODUCTS`
    ADD `defaultpagecount` int(11) NOT NULL DEFAULT '0' COMMENT '' AFTER weight,
    ADD `jobticketfield1name` varchar(100) NOT NULL DEFAULT '' COMMENT '' COLLATE utf8_general_ci AFTER defaultpagecount,
    ADD `jobticketfield1value` varchar(200) NOT NULL DEFAULT '' COMMENT '' COLLATE utf8_general_ci AFTER jobticketfield1name,
    ADD `jobticketfield2name` varchar(100) NOT NULL DEFAULT '' COMMENT '' COLLATE utf8_general_ci AFTER jobticketfield1value,
    ADD `jobticketfield2value` varchar(200) NOT NULL DEFAULT '' COMMENT '' COLLATE utf8_general_ci AFTER jobticketfield2name,
    ADD `jobticketfield3name` varchar(100) NOT NULL DEFAULT '' COMMENT '' COLLATE utf8_general_ci AFTER jobticketfield2value,
    ADD `jobticketfield3value` varchar(200) NOT NULL DEFAULT '' COMMENT '' COLLATE utf8_general_ci AFTER jobticketfield3name,
    ADD `jobticketfield4name` varchar(100) NOT NULL DEFAULT '' COMMENT '' COLLATE utf8_general_ci AFTER jobticketfield3value,
    ADD `jobticketfield4value` varchar(200) NOT NULL DEFAULT '' COMMENT '' COLLATE utf8_general_ci AFTER jobticketfield4name,
    ADD `jobticketfield5name` varchar(100) NOT NULL DEFAULT '' COMMENT '' COLLATE utf8_general_ci AFTER jobticketfield4value,
    ADD `jobticketfield5value` varchar(200) NOT NULL DEFAULT '' COMMENT '' COLLATE utf8_general_ci AFTER jobticketfield5name,
    ADD `createnewprojects` tinyint(1) NOT NULL DEFAULT '1' COMMENT '' AFTER jobticketfield5value,
    MODIFY `defaultpapercode` varchar(50) NOT NULL DEFAULT '' COMMENT '' COLLATE utf8_general_ci,
    MODIFY `defaultcovercode` varchar(50) NOT NULL DEFAULT '' COMMENT '' COLLATE utf8_general_ci,
    DROP `duplex`,
    DROP `defaultoutputdevice`;
#
#  Fieldformats of
#    PRODUCTS.defaultpapercode changed from varchar(20) NOT NULL DEFAULT '' COMMENT '' COLLATE utf8_general_ci to varchar(50) NOT NULL DEFAULT '' COMMENT '' COLLATE utf8_general_ci.
#    PRODUCTS.defaultcovercode changed from varchar(20) NOT NULL DEFAULT '' COMMENT '' COLLATE utf8_general_ci to varchar(50) NOT NULL DEFAULT '' COMMENT '' COLLATE utf8_general_ci.
#  Possibly data modifications needed!
#

ALTER TABLE `USERS`
    ADD `sendmarketinginfo` tinyint(1) NOT NULL DEFAULT '1' COMMENT '' AFTER accountbalance;


ALTER TABLE `VOUCHERS`
    ADD `productcategorycode` varchar(100) NOT NULL DEFAULT '' COMMENT '' COLLATE utf8_general_ci AFTER enddate,
    ADD `productcategoryname` varchar(100) NOT NULL DEFAULT '' COMMENT '' COLLATE utf8_general_ci AFTER productcategorycode;


#
# DDL END
#

SET FOREIGN_KEY_CHECKS = 1;

#
# UPDATE CCILOG DATA
#

UPDATE `CCILOG` SET `formattedauthorisationid` = `authorisationid`;


#
# UPDATE ORDERHEADER DATA
#

UPDATE `ORDERHEADER`, `ORDERITEMS` SET ORDERHEADER.shippingtotalweight = ORDERITEMS.totalshippingweight WHERE orderheader.id = orderitems.orderid;


#
# UPDATE ORDERITEMS DATA
#

UPDATE `ORDERITEMS` SET `pagecountpurchased` = `pagecount`;