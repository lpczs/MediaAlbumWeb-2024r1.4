#
SET FOREIGN_KEY_CHECKS = 0;

#
# DDL START
#


CREATE TABLE `KEYWORDGROUP` (
    `id` int(11) NOT NULL COMMENT '' auto_increment,
    `datecreated` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '',
    `keywordgroupheaderid` int(11) NOT NULL DEFAULT '0' COMMENT '',
    `keywordcode` varchar(50) NOT NULL DEFAULT '' COMMENT '' COLLATE utf8_general_ci,
    `sortorder` int(11) NOT NULL DEFAULT '0' COMMENT '',
    `defaultvalue` varchar(100) NOT NULL DEFAULT '' COMMENT '' COLLATE utf8_general_ci,
    PRIMARY KEY (`id`)
) DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci COMMENT='';


CREATE TABLE `KEYWORDGROUPHEADER` (
    `id` int(11) NOT NULL COMMENT '' auto_increment,
    `datecreated` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '',
    `groupcode` varchar(50) NOT NULL DEFAULT '' COMMENT '' COLLATE utf8_general_ci,
    `section` varchar(10) NOT NULL DEFAULT '' COMMENT '' COLLATE utf8_general_ci,
    `productcodes` varchar(1024) NOT NULL DEFAULT '' COMMENT '' COLLATE utf8_general_ci,
    PRIMARY KEY (`id`)
) DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci COMMENT='';


CREATE TABLE `KEYWORDS` (
    `id` int(11) NOT NULL COMMENT '' auto_increment,
    `datecreated` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '',
    `ref` int(11) NOT NULL DEFAULT '0' COMMENT '',
    `code` varchar(50) NOT NULL DEFAULT '' COMMENT '' COLLATE utf8_general_ci,
    `name` varchar(1024) NOT NULL DEFAULT '' COMMENT '' COLLATE utf8_general_ci,
    `description` varchar(1024) NOT NULL DEFAULT '' COMMENT '' COLLATE utf8_general_ci,
    `type` varchar(10) NOT NULL DEFAULT '' COMMENT '' COLLATE utf8_general_ci,
    `maxlength` int(11) NOT NULL DEFAULT '0' COMMENT '',
    `height` int(11) NOT NULL DEFAULT '0' COMMENT '',
    `width` int(11) NOT NULL DEFAULT '0' COMMENT '',
    `flags` varchar(10) NOT NULL DEFAULT '' COMMENT '' COLLATE utf8_general_ci,
    PRIMARY KEY (`id`),
    UNIQUE `newindex` (`code`)
) DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci COMMENT='';


CREATE TABLE `METADATA` (
    `id` int(11) NOT NULL COMMENT '' auto_increment,
    `datecreated` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '',
    `orderid` int(11) NOT NULL DEFAULT '0' COMMENT '',
    `orderitemid` int(11) NOT NULL DEFAULT '0' COMMENT '',
    `userid` int(11) NOT NULL DEFAULT '0' COMMENT '',
    `section` varchar(10) NOT NULL DEFAULT '' COMMENT '' COLLATE utf8_general_ci,
    PRIMARY KEY (`id`),
    INDEX `orderid` (`orderid`),
    INDEX `orderitemid` (`orderitemid`),
    INDEX `userid` (`userid`)
) DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci COMMENT='';


ALTER TABLE `CCILOG`
    ADD `responsedescription` varchar(255) NOT NULL DEFAULT '' COMMENT '' COLLATE utf8_general_ci AFTER responsecode,
    ADD `postcodestatus` varchar(50) NOT NULL DEFAULT '' COMMENT '' COLLATE utf8_general_ci AFTER addressstatus,
    ADD `threedsecurestatus` varchar(50) NOT NULL DEFAULT '' COMMENT '' COLLATE utf8_general_ci AFTER settleamount,
    ADD `cavvresponsecode` varchar(50) NOT NULL DEFAULT '' COMMENT '' COLLATE utf8_general_ci AFTER threedsecurestatus,
    ADD `charityflag` varchar(20) NOT NULL DEFAULT '' COMMENT '' COLLATE utf8_general_ci AFTER cavvresponsecode,
    MODIFY `bankresponsecode` varchar(50) NOT NULL DEFAULT '' COMMENT '' COLLATE utf8_general_ci,
    MODIFY `cvvflag` varchar(50) NOT NULL DEFAULT '' COMMENT '' COLLATE utf8_general_ci,
    MODIFY `cvvresponsecode` varchar(50) NOT NULL DEFAULT '' COMMENT '' COLLATE utf8_general_ci;
#
#  Fieldformats of
#    CCILOG.bankresponsecode changed from varchar(10) NOT NULL DEFAULT '' COMMENT '' COLLATE utf8_general_ci to varchar(50) NOT NULL DEFAULT '' COMMENT '' COLLATE utf8_general_ci.
#    CCILOG.cvvflag changed from varchar(10) NOT NULL DEFAULT '' COMMENT '' COLLATE utf8_general_ci to varchar(50) NOT NULL DEFAULT '' COMMENT '' COLLATE utf8_general_ci.
#    CCILOG.cvvresponsecode changed from varchar(10) NOT NULL DEFAULT '' COMMENT '' COLLATE utf8_general_ci to varchar(50) NOT NULL DEFAULT '' COMMENT '' COLLATE utf8_general_ci.
#  Possibly data modifications needed!
#


ALTER TABLE `ORDERHEADER`
    ADD `orderdate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '' AFTER userid,
    ADD `billingcustomeraccountcode` varchar(50) NOT NULL DEFAULT '' COMMENT '' COLLATE utf8_general_ci AFTER languagecode,
    ADD `metadatacodelist` varchar(200) NOT NULL DEFAULT '' COMMENT '' COLLATE utf8_general_ci AFTER ccilogid,
    MODIFY `ordernumber` varchar(50) NOT NULL DEFAULT '' COMMENT '' COLLATE utf8_general_ci,
    MODIFY `origordernumber` varchar(50) NOT NULL DEFAULT '' COMMENT '' COLLATE utf8_general_ci,
    ADD INDEX `billingaccountcode` (`billingcustomeraccountcode`),
    ADD INDEX `groupcode` (`groupcode`),
    ADD INDEX `userid` (`userid`),
    ADD INDEX `webbrandcode` (`webbrandcode`),
    ADD INDEX `orderdate` (`orderdate`);
#
#  Fieldformats of
#    ORDERHEADER.ordernumber changed from int(11) NOT NULL DEFAULT '0' COMMENT '' to varchar(50) NOT NULL DEFAULT '' COMMENT '' COLLATE utf8_general_ci.
#    ORDERHEADER.origordernumber changed from int(11) NOT NULL DEFAULT '0' COMMENT '' to varchar(50) NOT NULL DEFAULT '' COMMENT '' COLLATE utf8_general_ci.
#  Possibly data modifications needed!
#


ALTER TABLE `SHIPPINGMETHODS`
    ADD `ordervaluetype` varchar(10) NOT NULL DEFAULT '' COMMENT '' COLLATE utf8_general_ci AFTER requiresdelivery,
    ADD `orderminimumvalue` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '' AFTER ordervaluetype,
    ADD `ordermaximumvalue` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '' AFTER orderminimumvalue,
    ADD `ordervalueincludesdiscount` tinyint(1) NOT NULL DEFAULT '0' COMMENT '' AFTER ordermaximumvalue;


ALTER TABLE `SHIPPINGRATES`
    ADD `ordervaluetype` varchar(10) NOT NULL DEFAULT '' COMMENT '' COLLATE utf8_general_ci AFTER rate,
    ADD `orderminimumvalue` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '' AFTER ordervaluetype,
    ADD `ordermaximumvalue` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '' AFTER orderminimumvalue,
    ADD `ordervalueincludesdiscount` tinyint(1) NOT NULL DEFAULT '0' COMMENT '' AFTER ordermaximumvalue;


ALTER TABLE `USERS`
    ADD `accountcode` varchar(50) NOT NULL DEFAULT '' COMMENT '' COLLATE utf8_general_ci AFTER groupcode,
    ADD INDEX `accountcode` (`accountcode`);


#
# DDL END
#

SET FOREIGN_KEY_CHECKS = 1;

#
# UPDATE ORDERHEADER DATA
#

UPDATE ORDERHEADER SET orderdate = datecreated;

UPDATE ORDERHEADER SET ordernumber = LPAD(ordernumber, 7 , '0');