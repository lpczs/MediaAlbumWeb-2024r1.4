<?php

use Security\CsrfTokenGenerator;

class AuthenticateObj
{
    static function createSessionOrderData()
    {
        // create an empty array containing the session order data
        $orderArray = Array();

        $orderArray['id'] = 0;
        $orderArray['starttime'] = 0;
        $orderArray['shoppingcarttype'] = TPX_SHOPPINGCARTTYPE_INTERNAL;
        $orderArray['basketapiworkflowtype'] = TPX_BASKETWORKFLOWTYPE_NORMAL;
        $orderArray['temporder'] = 0;
        $orderArray['temporderid'] = 0;
        $orderArray['tempordernumber'] = '';
        $orderArray['temporderexpirydate'] = '';
        $orderArray['isofflineorder'] = 0;
        $orderArray['isofflineordercompletedbycustomer'] = 0;
        $orderArray['offlineordersitecode'] = '';
        $orderArray['uuid'] = '';
        $orderArray['ordernumber'] = '';
        $orderArray['isreorder'] = 0;
        $orderArray['jobtickettemplate'] = '';
        $orderArray['currencycode'] = '';
        $orderArray['currencyname'] = '';
        $orderArray['currencyisonumber'] = '';
        $orderArray['currencysymbol'] = '';
        $orderArray['currencysymbolatfront'] = 0;
        $orderArray['currencydecimalplaces'] = 0;
        $orderArray['basecurrencycode'] = '';
        $orderArray['currencyexchangerate'] = 1.0000;
        $orderArray['useripaddress'] = '';
        $orderArray['userbrowser'] = '';
        $orderArray['confirmationhtml'] = '';
        $orderArray['useremaildestination'] = 0;
        $orderArray['voucherpromotioncode'] = '';
        $orderArray['voucherpromotionname'] = '';
        $orderArray['vouchercode'] = '';
        $orderArray['vouchername'] = '';
        $orderArray['voucherminqty'] = 1;
        $orderArray['vouchermaxqty'] = 9999;
        $orderArray['voucherlockqty'] = 0;
        $orderArray['voucheractive'] = 0;
        $orderArray['voucherdiscountsection'] = '';
        $orderArray['voucherdiscounttype'] = '';
        $orderArray['voucherdiscountvalue'] = 0.00;
        $orderArray['voucherstatus'] = '';
        $orderArray['giftcardstatus'] = '';
        $orderArray['vouchertype'] = TPX_VOUCHER_TYPE_DISCOUNT;
        $orderArray['vouchersellprice'] = 0.00;
        $orderArray['voucheragentfee'] = 0.00;
        $orderArray['vouchercustommessage'] = '';
        $orderArray['voucherapplicationmethod'] = TPX_VOUCHER_APPLY_EACH_MATCHING_PRODUCT;
        $orderArray['voucherapplytoqty'] = 9999;
        $orderArray['externalcartscriptexists'] = 0;

        $orderArray['defaultdiscount'] = Array();
        $orderArray['defaultdiscountactive'] = true;

        $orderArray['showpriceswithtax'] = 0;
        $orderArray['showtaxbreakdown'] = 1;
        $orderArray['showzerotax'] = 1;
        $orderArray['showalwaystaxtotal'] = 0;
        $orderArray['linecount'] = 1;
        $orderArray['currentline'] = 0; // current order line
        $orderArray['nextorderlineid'] = 1; // next order line id

        $orderArray['itemsdiscounted'] = array();

        $orderArray['ordertotalitemdiscountable'] = 0;
        $orderArray['ordertotalitemcost'] = 0.00;
        $orderArray['ordertotalitemsell'] = 0.00;
        $orderArray['ordertotalitemsellnotax'] = 0.00;
        $orderArray['ordertotalitemsellnotaxnodiscount'] = 0.00;
        $orderArray['ordertotalitemsellnotaxalldiscounted'] = 0.00;
        $orderArray['ordertotalitemsellwithtax'] = 0.00;
        $orderArray['ordertotalitemsellwithtaxnodiscount'] = 0.00;
        $orderArray['ordertotalitemsellwithtaxalldiscounted'] = 0.00;
        $orderArray['ordertotalitemtax'] = 0.00;
        $orderArray['ordertotalshippingcost'] = 0.00;
        $orderArray['orderfootertotalnotaxnodiscount'] = 0.00;
        $orderArray['orderfootertotalwithtaxnodiscount'] = 0.00;

        $orderArray['producttaxlevel1'] = Array('result' => '','resultparam' => '','recordid' => 0,'datecreated' => '0000-00-00 00:00:00','code' => '','name' => '','rate' => 0.00);
        $orderArray['producttaxlevel2'] = Array('result' => '','resultparam' => '','recordid' => 0,'datecreated' => '0000-00-00 00:00:00','code' => '','name' => '','rate' => 0.00);
        $orderArray['producttaxlevel3'] = Array('result' => '','resultparam' => '','recordid' => 0,'datecreated' => '0000-00-00 00:00:00','code' => '','name' => '','rate' => 0.00);
        $orderArray['producttaxlevel4'] = Array('result' => '','resultparam' => '','recordid' => 0,'datecreated' => '0000-00-00 00:00:00','code' => '','name' => '','rate' => 0.00);
        $orderArray['producttaxlevel5'] = Array('result' => '','resultparam' => '','recordid' => 0,'datecreated' => '0000-00-00 00:00:00','code' => '','name' => '','rate' => 0.00);

        $orderArray['ordertotalshippingsellbeforediscount'] = 0.00;
        $orderArray['ordertotalshippingsellafterdiscount'] = 0.00;

        $orderArray['ordertotalshippingtax'] = 0.00;
        $orderArray['ordertotalshippingweight'] = 0.0000;
        $orderArray['ordertotalcost'] = 0.00;
        $orderArray['ordertotalsellbeforediscount'] = 0.00;
        $orderArray['ordertotaltaxbeforediscount'] = 0.00;
        $orderArray['ordertotalbeforediscount'] = 0.00;
        $orderArray['ordertotaldiscount'] = 0.00;
        $orderArray['ordertotalsell'] = 0.00;
        $orderArray['ordertotaltax'] = 0.00;
        $orderArray['ordertotal'] = 0.00;

        $orderArray['canmodifyshippingaddress'] = 0;
        $orderArray['canmodifyshippingcontactdetails'] = 0;
        $orderArray['canmodifybillingaddress'] = 0;
        $orderArray['sameshippingandbillingaddress'] = 0;
        $orderArray['paymentmethodcode'] = '';
        $orderArray['paymentmethodname'] = '';
        $orderArray['paymentgatewaycode'] = '';
        $orderArray['shippingrequiresdelivery'] = 0;
        $orderArray['ccitype'] = '';
        $orderArray['ccilogid'] = 0;
        $orderArray['ccitransactionid'] = '';
        $orderArray['cciauthorised'] = 0;
        $orderArray['ccidata'] = "";
        $orderArray['ccicookie'] = "";
        $orderArray['ccipaymentreceived'] = 0;
        $orderArray['ccipaymentreceiveddatetime'] = "";
		$orderArray['ccicachefileneeded'] = 0;

        $orderArray['billingcustomeraccountcode'] = '';
        $orderArray['billingcustomername'] = '';
        $orderArray['billingcustomeraddress1'] = '';
        $orderArray['billingcustomeraddress2'] = '';
        $orderArray['billingcustomeraddress3'] = '';
        $orderArray['billingcustomeraddress4'] = '';
        $orderArray['billingcustomercity'] = '';
        $orderArray['billingcustomercounty'] = '';
        $orderArray['billingcustomerstate'] = '';
        $orderArray['billingcustomerregioncode'] = '';
        $orderArray['billingcustomerregion'] = '';
        $orderArray['billingcustomerpostcode'] = '';
        $orderArray['billingcustomercountrycode'] = '';
        $orderArray['billingcustomercountryname'] = '';
        $orderArray['billingcustomertelephonenumber'] = '';
        $orderArray['billingcustomeremailaddress'] = '';
        $orderArray['billingcontactfirstname'] = '';
        $orderArray['billingcontactlastname'] = '';
        $orderArray['billingregisteredtaxnumbertype'] = TPX_REGISTEREDTAXNUMBERTYPE_NA;
        $orderArray['billingregisteredtaxnumber'] = '';

        $orderArray['defaultbillingcustomeraccountcode'] = '';
        $orderArray['defaultbillingcustomername'] = '';
        $orderArray['defaultbillingcustomeraddress1'] = '';
        $orderArray['defaultbillingcustomeraddress2'] = '';
        $orderArray['defaultbillingcustomeraddress3'] = '';
        $orderArray['defaultbillingcustomeraddress4'] = '';
        $orderArray['defaultbillingcustomercity'] = '';
        $orderArray['defaultbillingcustomercounty'] = '';
        $orderArray['defaultbillingcustomerstate'] = '';
        $orderArray['defaultbillingcustomerregioncode'] = '';
        $orderArray['defaultbillingcustomerregion'] = '';
        $orderArray['defaultbillingcustomerpostcode'] = '';
        $orderArray['defaultbillingcustomercountrycode'] = '';
        $orderArray['defaultbillingcustomercountryname'] = '';
        $orderArray['defaultbillingcustomertelephonenumber'] = '';
        $orderArray['defaultbillingcustomeremailaddress'] = '';
        $orderArray['defaultbillingcontactfirstname'] = '';
        $orderArray['defaultbillingcontactlastname'] = '';
        $orderArray['defaultbillingregisteredtaxnumbertype'] = TPX_REGISTEREDTAXNUMBERTYPE_NA;
        $orderArray['defaultbillingregisteredtaxnumber'] = '';

        $orderArray['orderFooterSections'] = Array();
        $orderArray['orderFooterCheckboxes'] = Array();

        $orderArray['ordertaxbreakdown'] = Array();
        $orderArray['ordertaxproductbreakdown'] = Array();
        $orderArray['orderalltaxratesequal'] = 0;
        $orderArray['orderfootertaxratesequal'] = 0;
        $orderArray['orderfootertotalwithtax'] = 0;
        $orderArray['orderfootertotalnotax'] = 0;


        $orderArray['completed'] = 0;
        $orderArray['initialized'] = 0;
        $orderArray['processed'] = 0;
        $orderArray['currentstage'] = '';
        $orderArray['shippingaddressmodified'] = false;

        $orderArray['usetaxratefromproduct'] = false;
        $orderArray['fixedtaxrate'] = '';
		$orderArray['hascompanionalbums'] = TPX_ORDERHASCOMPANIONALBUMS_UNKOWN;
		$orderArray['uselegacypricingsystem'] = false;

        return $orderArray;
    }

    static function createSessionShippingLine()
    {
        // create an empty array containing the data for a single shipping line
        $shippingArray = array();

        $shippingArray['shippingqty'] = 0;

        $shippingArray['shippingcustomername'] = '';
        $shippingArray['shippingcustomeraddress1'] = '';
        $shippingArray['shippingcustomeraddress2'] = '';
        $shippingArray['shippingcustomeraddress3'] = '';
        $shippingArray['shippingcustomeraddress4'] = '';
        $shippingArray['shippingcustomercity'] = '';
        $shippingArray['shippingcustomercounty'] = '';
        $shippingArray['shippingcustomerstate'] = '';
        $shippingArray['shippingcustomerregioncode'] = '';
        $shippingArray['shippingcustomerregion'] = '';
        $shippingArray['shippingcustomerpostcode'] = '';
        $shippingArray['shippingcustomercountrycode'] = '';
        $shippingArray['shippingcustomercountryname'] = '';
        $shippingArray['shippingcustomertelephonenumber'] = '';
        $shippingArray['shippingcustomeremailaddress'] = '';
        $shippingArray['shippingcontactfirstname'] = '';
        $shippingArray['shippingcontactlastname'] = '';
        $shippingArray['shippingregisteredtaxnumbertype'] = TPX_REGISTEREDTAXNUMBERTYPE_NA;
        $shippingArray['shippingregisteredtaxnumber'] = '';

        $shippingArray['defaultshippingcustomername'] = '';
        $shippingArray['defaultshippingcustomeraddress1'] = '';
        $shippingArray['defaultshippingcustomeraddress2'] = '';
        $shippingArray['defaultshippingcustomeraddress3'] = '';
        $shippingArray['defaultshippingcustomeraddress4'] = '';
        $shippingArray['defaultshippingcustomercity'] = '';
        $shippingArray['defaultshippingcustomercounty'] = '';
        $shippingArray['defaultshippingcustomerstate'] = '';
        $shippingArray['defaultshippingcustomerregioncode'] = '';
        $shippingArray['defaultshippingcustomerregion'] = '';
        $shippingArray['defaultshippingcustomerpostcode'] = '';
        $shippingArray['defaultshippingcustomercountrycode'] = '';
        $shippingArray['defaultshippingcustomercountryname'] = '';
        $shippingArray['defaultshippingcustomertelephonenumber'] = '';
        $shippingArray['defaultshippingcustomeremailaddress'] = '';
        $shippingArray['defaultshippingcontactfirstname'] = '';
        $shippingArray['defaultshippingcontactlastname'] = '';
        $shippingArray['defaultshippingregisteredtaxnumbertype'] = TPX_REGISTEREDTAXNUMBERTYPE_NA;
        $shippingArray['defaultshippingregisteredtaxnumber'] = '';

        $shippingArray['shippingmethodcode'] = '';
        $shippingArray['shippingmethodname'] = '';
        $shippingArray['shippingmethodusedefaultshippingaddress'] = '';
        $shippingArray['shippingmethodusedefaultbillingaddress'] = '';
        $shippingArray['shippingmethodcanmodifycontactdetails'] = '';
        $shippingArray['shippingratecode'] = '';
        $shippingArray['shippingrateinfo'] = '';
        $shippingArray['shippingratecost'] = 0.00;
        $shippingArray['shippingratesell'] = 0.00;
        $shippingArray['shippingratesellnotax'] = 0.00;
        $shippingArray['shippingratesellwithtax'] = 0.00;
        $shippingArray['shippingratepricetaxcode'] = '';
        $shippingArray['shippingratepricetaxrate'] = 0.00;

        $shippingArray['shippingratediscountvalue'] = 0.00;
        $shippingArray['shippingratetotalsell'] = 0.00;
        $shippingArray['shippingratetotalsellnotax'] = 0.00;
        $shippingArray['shippingratetotalsellwithtax'] = 0.00;

        $shippingArray['shippingratetaxcode'] = '';
        $shippingArray['shippingratetaxname'] = '';
        $shippingArray['shippingratetaxrate'] = 0.00;
        $shippingArray['shippingratetaxexempt'] = 0;
        $shippingArray['shippingratecalctax'] = 0;
        $shippingArray['shippingratetaxtotal'] = 0.00;

        $shippingArray['collectfromstore'] = false;
        $shippingArray['payinstoreallowed'] = 0;
        $shippingArray['storeid'] = '';
        $shippingArray['distributioncentrecode'] = '';
        $shippingArray['shippingMethods'] = array();
        $shippingArray['shippingprivatedata'] = array();

        return $shippingArray;
    }

    static function createSessionOrderLine()
    {
        // create an empty array containing the data for a single order line
        $orderItemArray = Array();

        $orderItemArray['orderlineid'] = 0;
        $orderItemArray['source'] = TPX_SOURCE_DESKTOP;
        $orderItemArray['itemshareid'] = 0;
        $orderItemArray['itemproductcollectioncode'] = '';
        $orderItemArray['itemproductcollectionname'] = '';
        $orderItemArray['itemuploadgroupcode'] = '';
        $orderItemArray['itemuploadorderid'] = 0;
        $orderItemArray['itemuploadordernumber'] = '';
        $orderItemArray['itemuploadorderitemid'] = 0;
        $orderItemArray['itemuploadbatchref'] = '';
        $orderItemArray['itemuploadref'] = '';
        $orderItemArray['itemprojectref'] = '';
        $orderItemArray['itemprojectreforig'] = '';
        $orderItemArray['itemprojectname'] = '';
        $orderItemArray['itemprojectstarttime'] = '';
        $orderItemArray['itemprojectduration'] = 0;
        $orderItemArray['itemproductcode'] = '';
        $orderItemArray['itemproductskucode'] = '';
        $orderItemArray['itemproductname'] = '';
        $orderItemArray['itemproducttype'] = 0;
        $orderItemArray['itemproductpageformat'] = 0;
        $orderItemArray['itemproductspreadpageformat'] = 0;
        $orderItemArray['itemproductcover1format'] = 0;
        $orderItemArray['itemproductcover2format'] = 0;
        $orderItemArray['itemproductoutputformat'] = 0;
        $orderItemArray['itemproductheight'] = 0.00;
        $orderItemArray['itemproductwidth'] = 0.00;
        $orderItemArray['itemproductdefaultpagecount'] = 0;
        $orderItemArray['itemproductinfo'] = '';
        $orderItemArray['itempagecount'] = 0;
        $orderItemArray['itemproductunitcost'] = 0.00;
        $orderItemArray['itemproductunitsell'] = 0.00;
        $orderItemArray['itemproductunitweight'] = 0.0000;
        $orderItemArray['itemproducttotalweight'] = 0.0000;
        $orderItemArray['itemproducttaxlevel'] = 1;
        $orderItemArray['itemtaxcode'] = '';
        $orderItemArray['itemtaxname'] = '';
        $orderItemArray['itemtaxrate'] = 0.00;
        $orderItemArray['itemtaxratetaxempt'] = 0;
        $orderItemArray['itemhasproductprice'] = 0;

        $orderItemArray['itemqty'] = 0;
        $orderItemArray['itemqtydropdown'] = array();
        $orderItemArray['itemproducttotalcost'] = 0.00;
        $orderItemArray['itemproducttotalsell'] = 0.00;
        $orderItemArray['itemproducttotaltax'] = 0.00;
        $orderItemArray['itemproducttotalsellnotax'] = 0.00;
        $orderItemArray['itemproducttotalsellwithtax'] = 0.00;

        $orderItemArray['checkboxes'] = array();
        $orderItemArray['sections'] = array();
        $orderItemArray['lineFooterSections'] = array();
        $orderItemArray['lineFooterCheckboxes'] = array();

        $orderItemArray['pricetaxcode'] = '';
        $orderItemArray['pricetaxrate'] = '';
        $orderItemArray['itemsubtotal'] = 0.00;
        $orderItemArray['itemvoucherapplied'] = 0;
        $orderItemArray['itemdiscountvalue'] = 0.00;
        $orderItemArray['itemdiscountvalueraw'] = 0.00;
        $orderItemArray['itemdiscountvaluenotax'] = 0.00;
        $orderItemArray['itemdiscountvaluenwithtax'] = 0.00;
        $orderItemArray['itemvouchername'] = '';
        $orderItemArray['itemtotalcost'] = 0.00;
        $orderItemArray['itemtotalsell'] = 0.00;
        $orderItemArray['itemtotalsellnotax'] = 0.00;
        $orderItemArray['itemtotalsellnotaxnodiscount'] = 0.00;
        $orderItemArray['itemtotalsellnotaxalldiscounted'] = 0.00;
        $orderItemArray['itemtotalsellwithtax'] = 0.00;
        $orderItemArray['itemtotalsellwithtaxnodiscount'] = 0.00;
        $orderItemArray['itemtotalsellwithtaxalldiscounted'] = 0.00;
        $orderItemArray['itemtotalweight'] = 0.0000;
        $orderItemArray['itemtaxtotal'] = 0.00;

        $orderItemArray['previewsonline'] = 0;
        $orderItemArray['canupload'] = 1;
        $orderItemArray['assetid'] = 0;

        $orderItemArray['itemuploadappversion'] = '';
        $orderItemArray['itemuploadappplatform'] = '';
        $orderItemArray['itemuploadappcputype'] = '';
        $orderItemArray['itemuploadapposversion'] = '';
        $orderItemArray['itemuploaddatasize'] = 0;
        $orderItemArray['itemuploadduration'] = 0;
        $orderItemArray['itemuploaddatatype'] = TPX_UPLOAD_DATA_TYPE_RENDERED;
        $orderItemArray['itemhascompanions'] = false;
        $orderItemArray['parentorderitemid'] = 0;


        $orderItemArray['itemexternalassets'] = array();
		$orderItemArray['pictures'] = array();

		$orderItemArray['itemaimode'] = TPX_AIMODE_DISABLED;

        return $orderItemArray;
    }

    static function createSessionDataArray()
    {
        // create an empty array containing the standard session data
        $sessionDataArray = Array();

        $sessionDataArray['ref'] = 0;
        $sessionDataArray['issessionactive'] = 0;
        $sessionDataArray['isordersession'] = 0;
        $sessionDataArray['isadministrator'] = 0;
        $sessionDataArray['sessionexpiredate'] = 0;
        $sessionDataArray['sessionrevived'] = 0;
		$sessionDataArray['sessionkey'] = '';
		$sessionDataArray['authenticatecookie'] = 1;
        $sessionDataArray['applanguagecode'] = '';
        $sessionDataArray['appversion'] = '';
        $sessionDataArray['browserlanguagecode'] = '';
        $sessionDataArray['webbrandcode'] = '';
        $sessionDataArray['webbrandname'] = '';
        $sessionDataArray['webbrandapplicationname'] = '';
        $sessionDataArray['webbranddisplayurl'] = '';
        $sessionDataArray['webbrandweburl'] = '';
        $sessionDataArray['webbrandwebroot'] = '';
        $sessionDataArray['webbrandsupporttelephonenumber'] = '';
        $sessionDataArray['webbrandsupportemailaddress'] = '';
        $sessionDataArray['userpaymentmethods'] = '';
        $sessionDataArray['useracccountbalance'] = 0.00;
        $sessionDataArray['usercreditlimit'] = 0.00;
        $sessionDataArray['usergiftcardbalance'] = 0.00;
        $sessionDataArray['ordergiftcarddeleted'] = false;
        $sessionDataArray['showgiftcardmessage'] = 0;
        $sessionDataArray['ismobile'] = false;
        $sessionDataArray['islargescreen'] = false;

        $sessionDataArray['emailthumbnailtype'] = 1;

        $sessionDataArray['shippingmethodlogopath'] = '';   // temporary path to new shipping method logo
        $sessionDataArray['shippingmethodlogotype'] = '';   // image type of new shipping method logo

        $sessionDataArray['userid'] = 0;
        $sessionDataArray['userlogin'] = '';
        $sessionDataArray['username'] = '';
        $sessionDataArray['userdata']['userowner'] = '';
        $sessionDataArray['userdata']['companycode'] = '';
        $sessionDataArray['userdata']['usertype'] = '';
        $sessionDataArray['userdata']['webbrandcode'] = '';
        $sessionDataArray['userdata']['ssotoken'] = '';
        $sessionDataArray['userdata']['ssoprivatedata'] = Array();

		$sessionDataArray['customparameters'] = array();

        $licenseKeyArray = Array();
        $licenseKeyArray['systemkey'] = '';
        $licenseKeyArray['ownercode'] = '';
        $licenseKeyArray['groupcode'] = '';
        $licenseKeyArray['groupdata'] = '';
        $licenseKeyArray['groupname'] = '';
        $licenseKeyArray['groupaddress1'] = '';
        $licenseKeyArray['groupaddress2'] = '';
        $licenseKeyArray['groupaddress3'] = '';
        $licenseKeyArray['groupaddress4'] = '';
        $licenseKeyArray['groupcity'] = '';
        $licenseKeyArray['groupcounty'] = '';
        $licenseKeyArray['groupstate'] = '';
        $licenseKeyArray['grouppostcode'] = '';
        $licenseKeyArray['groupcountrycode'] = '';
        $licenseKeyArray['grouptelephonenumber'] = '';
        $licenseKeyArray['groupemailaddress'] = '';
        $licenseKeyArray['groupcontactfirstname'] = '';
        $licenseKeyArray['groupcontactlastname'] = '';

        $sessionDataArray['licensekeydata'] = $licenseKeyArray;
        $sessionDataArray['onlineclienttime'] = 0;

		$sessionDataArray['previewtype'] = '';
		$sessionDataArray['previewpath'] = '';

        return $sessionDataArray;
    }

    static function createEmptyUserAccount($pLicenseKeyArray, $pGroupCode, $pWebBrandCode, $pBrandingArray)
    {
        global $gConstants;

        // get the default account settings from the license key
        $userEmailDestination = 0;
        $useLicenseKeyForShippingAddress = 0;
        $modifyShippingAddress = 0;
        $modifyShippingContactDetails = 0;
        $useLicenseKeyForBillingAddress = 0;
        $modifyBillingAddress = 0;
        $paymentMethods = '';

        if ($pGroupCode != '')
        {
            $userEmailDestination = (($pLicenseKeyArray['useremaildestination'] != '') && $pLicenseKeyArray['useremaildestination'] != 0) ? $pLicenseKeyArray['useremaildestination'] : 0;

            $useLicenseKeyForShippingAddress = (($pLicenseKeyArray['useaddressforshipping'] != '') && ($pLicenseKeyArray['useaddressforshipping'] != 0)) ? $pLicenseKeyArray['useaddressforshipping'] : 0;
            if ($useLicenseKeyForShippingAddress == 1)
            {
                $modifyShippingAddress = 0;
                $modifyShippingContactDetails = (($pLicenseKeyArray['canmodifyshippingcontactdetails'] != '') && ($pLicenseKeyArray['canmodifyshippingcontactdetails'] != 0)) ? $pLicenseKeyArray['canmodifyshippingcontactdetails'] : 0;
            }
            else
            {
                $modifyShippingAddress = 1;
                $modifyShippingContactDetails = 1;
            }

            $useLicenseKeyForBillingAddress = (($pLicenseKeyArray['useaddressforbilling'] != 0) && ($pLicenseKeyArray['useaddressforbilling'] != '')) ? $pLicenseKeyArray['useaddressforbilling'] : 0;
            if ($useLicenseKeyForBillingAddress == 1)
            {
                $modifyBillingAddress = 0;
            }
            else
            {
                $modifyBillingAddress = 1;
            }

            if ($pLicenseKeyArray['usedefaultpaymentmethods'] == 1)
            {
                $paymentMethods = $pBrandingArray['paymentmethods'];
            }
            else
            {
                $paymentMethods = $pLicenseKeyArray['paymentmethods'];
            }

        }

        // create an empty user account array with default settings
        $externalUserAccountArray = DatabaseObj::getEmptyUserAccount();
        $externalUserAccountArray['groupcode'] = $pGroupCode;
        $externalUserAccountArray['webbrandcode'] = $pWebBrandCode;
        $externalUserAccountArray['companycode'] = $pBrandingArray['companycode'];
        $externalUserAccountArray['iscustomer'] = 1;
        $externalUserAccountArray['usedefaultcurrency'] = 1;
        $externalUserAccountArray['currencycode'] = $gConstants['defaultcurrencycode'];
        $externalUserAccountArray['usedefaultpaymentmethods'] = 1;
        $externalUserAccountArray['paymentmethods'] = $paymentMethods;
        $externalUserAccountArray['useremaildestination'] = $userEmailDestination;
        $externalUserAccountArray['creditlimit'] = $gConstants['defaultcreditlimit'];
        $externalUserAccountArray['uselicensekeyforshippingaddress'] = $useLicenseKeyForShippingAddress;
        $externalUserAccountArray['canmodifyshippingaddress'] = $modifyShippingAddress;
        $externalUserAccountArray['canmodifyshippingcontactdetails'] = $modifyShippingContactDetails;
        $externalUserAccountArray['uselicensekeyforbillingaddress'] = $useLicenseKeyForBillingAddress;
        $externalUserAccountArray['canmodifybillingaddress'] = $modifyBillingAddress;
        $externalUserAccountArray['addressupdated'] = -1;

        return $externalUserAccountArray;
    }

    static function updateOrInsertExternalAccount(&$pUserAccountID, $pUserAccountArray, $pIsSingleSignOn, $pSessionRef, $pLogin, $pPasswordFormat,
                                                                                $pWebBrandCode, $pGroupCode, $pCompanyCode, $pUpdateGroupCode,
                                                                                $pUpdateAccountDetails, $pUpdateAccountBalance, $pUpdateGiftCardBalance)
    {
        $userAccountID = $pUserAccountID;
        $origAddressUpdated = 0;
        $result = '';
        $resultParam = '';
        $pUserAccountArray['result'] = '';
        $pUserAccountArray['resultparam'] = '';
		$userAccountPassword = $pUserAccountArray['password'];

		// if the password is empty then create a random string and hash it to store against the user in Taopix
		// this password is not used when authenticating the user via edl scripts
		if ($userAccountPassword == '')
		{
			$userAccountPassword = UtilsObj::createRandomString(32);
		}

		// hash the password using the format passed via the script
		$generatePasswordHashResult = self::generatePasswordHash($userAccountPassword, $pPasswordFormat);

		if ($generatePasswordHashResult['result'] == '')
		{
			$userAccountPasswordHash = $generatePasswordHashResult['data'];

			// Only check for existing accounts when SSO.
			if ($pIsSingleSignOn)
			{
				if ($pUserAccountArray['accountcode'] != '')
				{
					// look for an existing taopix user account using the unique identifier in the account code
					$tempUserAccountArray = DatabaseObj::getUserAccountFromAccountCode($pUserAccountArray['accountcode']);

					$userAccountID = $tempUserAccountArray['recordid'];

					// if there is a session ref then we need to make sure that the user is the same as the one which
					// the session was started with. this makes sure that a different user isn't completing an order
					// or opening a project
					if ($pSessionRef > 0)
					{
						global $gSession;

						if (($gSession['userid'] != $userAccountID) && ($gSession['userid'] > 0))
						{
							$result = 'str_ErrorAccountTaskNotAllowed';
						}
					}

					if ($result == '')
					{
						// if we have a matching account retrieve the current status of the address
						if ($userAccountID > 0)
						{
							$origAddressUpdated = $tempUserAccountArray['addressupdated'];

							// if the record is found for the update account details flag is not set, we need to to set the user record
							// to be the one from the database and ignore the one from the EDL script.
							if (! $pUpdateAccountDetails)
							{
								$pUserAccountArray = $tempUserAccountArray;
							}

							// if the group code is not flagged as being update then use the one from the database instead
							// of the one from the EDL script
							if (! $pUpdateGroupCode)
							{
								$pUserAccountArray['groupcode'] = $tempUserAccountArray['groupcode'];
							}
						}

						$result = '';
						$resultParam = '';
					}
				}
				else
				{
					$result = 'str_ErrorAccountMisMatch';
				}
			}
		}
		else
		{
			$result = $generatePasswordHashResult['result'];
			$resultParam = $generatePasswordHashResult['resultparam'];
		}

        // if we have no error we must make sure the script has not altered certain data
        // a) the webbrandcode can never be altered by the script
        // b) if the groupcode is not empty then it cannot be altered by the script otherwise it needs to be set by the script
        if ($result == '')
        {
            // re-assign some of the properties so that they can never be changed by the script
            $pUserAccountArray['recordid'] = $userAccountID;
            $pUserAccountArray['iscustomer'] = 1;
            $pUserAccountArray['usertype'] = TPX_LOGIN_CUSTOMER;

            if (! $pIsSingleSignOn)
            {
                // if this is not the single sign-on test we need to make sure the login parameters have not been overwritten
                $pUserAccountArray['login'] = $pLogin;
                $pUserAccountArray['password'] = $userAccountPassword;
            }

            if (($pUserAccountArray['webbrandcode'] != $pWebBrandCode) || (($pGroupCode != '') && ($pUserAccountArray['groupcode'] != $pGroupCode)))
            {
                $result = 'str_ErrorAccountMisMatch';
            }
        }

        // make sure that a group code has been set
        if ($result == '')
        {
            if ($pUserAccountArray['groupcode'] == '')
            {
                $result = 'str_ErrorEmptyGroupCode';
            }
        }

        // process the external script result
        if ($result == '')
        {
            $dbObj = DatabaseObj::getGlobalDBConnection();

            if ($dbObj)
            {
                // update the address updated status if the external script says to keep it the same
                if ($pUserAccountArray['addressupdated'] == -1)
                {
                    $pUserAccountArray['addressupdated'] = $origAddressUpdated;
                }

                // if an account already exists update it with the information from the external system
                // otherwise insert a new record into the database with the information from the external system
                if ($userAccountID > 0)
                {
                    $accountDetailsUpdated = -1;

                    // update the main account details
                    if ($pUpdateAccountDetails)
                    {
                        if ($stmt = $dbObj->prepare('UPDATE `USERS` SET `companycode` = ?, `groupcode` = ?, `password` = ?, `usertype` = ?,
                                                    `companyname` = ?, `address1` = ?, `address2` = ?, `address3` = ?, `address4` = ?,
                                                    `city` = ?, `county` = ?, `state` = ?, `regioncode` = ?, `region` = ?, `addressupdated` = ?, `postcode` = ?,
                                                    `countrycode` = ?, `countryname` = ?, `telephonenumber` = ?, `emailaddress` = ?,
                                                    `contactfirstname` = ?, `contactlastname` = ?, `usedefaultpaymentmethods` = ?, `paymentmethods` = ?,
                                                    `taxcode` = ?, `shippingtaxcode` = ?, `registeredtaxnumbertype` = ?, `registeredtaxnumber` = ?, `uselicensekeyforshippingaddress` = ?,
                                                    `modifyshippingaddress` = ?, `modifyshippingcontactdetails` = ?, `uselicensekeyforbillingaddress` = ?, `modifybillingaddress` = ?,
                                                    `modifypassword` = ?, `sendmarketinginfo` = ?, `sendmarketinginfooptindate` = ?, `active` = ?, `useremaildestination` = ?, `defaultaddresscontrol` = ?,
													`usedefaultvouchersettings` = ?, `allowvouchers` = ?, `allowgiftcards` = ?, `usedefaultcurrency` = ?, `currencycode` = ?
                                                    WHERE `id` = ?'))
                        {
                            if ($stmt->bind_param('sssi' . 'sssss' . 'sssss' . 'isss' . 'ssss' . 'isss' . 'isi' . 'iii' . 'iii' . 'siii' . 'iiiis' . 'i',
                                    $pCompanyCode, $pUserAccountArray['groupcode'], $userAccountPasswordHash, $pUserAccountArray['usertype'],
                                    $pUserAccountArray['companyname'], $pUserAccountArray['address1'], $pUserAccountArray['address2'], $pUserAccountArray['address3'], $pUserAccountArray['address4'],
                                    $pUserAccountArray['city'], $pUserAccountArray['county'], $pUserAccountArray['state'], $pUserAccountArray['regioncode'], $pUserAccountArray['region'],
                                    $pUserAccountArray['addressupdated'], $pUserAccountArray['postcode'], $pUserAccountArray['countrycode'], $pUserAccountArray['countryname'],
                                    $pUserAccountArray['telephonenumber'], $pUserAccountArray['emailaddress'], $pUserAccountArray['contactfirstname'], $pUserAccountArray['contactlastname'],
                                    $pUserAccountArray['usedefaultpaymentmethods'], $pUserAccountArray['paymentmethods'], $pUserAccountArray['taxcode'], $pUserAccountArray['shippingtaxcode'],
                                    $pUserAccountArray['registeredtaxnumbertype'], $pUserAccountArray['registeredtaxnumber'], $pUserAccountArray['uselicensekeyforshippingaddress'],
                                    $pUserAccountArray['canmodifyshippingaddress'], $pUserAccountArray['canmodifyshippingcontactdetails'], $pUserAccountArray['uselicensekeyforbillingaddress'],
                                    $pUserAccountArray['canmodifybillingaddress'], $pUserAccountArray['canmodifypassword'], $pUserAccountArray['sendmarketinginfo'],
                                    $pUserAccountArray['sendmarketinginfooptindate'], $pUserAccountArray['isactive'], $pUserAccountArray['useremaildestination'], $pUserAccountArray['defaultaddresscontrol'],
									$pUserAccountArray['usedefaultvouchersettings'], $pUserAccountArray['allowvouchers'], $pUserAccountArray['allowgiftcards'], $pUserAccountArray['usedefaultcurrency'], $pUserAccountArray['currencycode'],
									$userAccountID))
                            {
                                if ($stmt->execute())
                                {
                                    $accountDetailsUpdated = 1;

                                    DatabaseObj::updateActivityLog($pSessionRef, 0, $pUserAccountArray['recordid'], $pUserAccountArray['login'], $pUserAccountArray['contactfirstname'] . ' ' . $pUserAccountArray['contactlastname'], 0, 'CUSTOMER', 'UPDATEPREFERENCES', $pUserAccountArray['sendmarketinginfo'] . ' ' . $pUserAccountArray['recordid'], 1);

									// include data export and trigger customer update
									require_once('../Utils/UtilsDataExport.php');
									DataExportObj::EventTrigger(TPX_TRIGGER_CUSTOMER_EDIT, 'CUSTOMER', $userAccountID, 0);
                                }
                                else
                                {
                                    $pUserAccountArray['result'] = 'str_DatabaseError';
                                    $pUserAccountArray['resultparam'] = 'externalloginupdate execute ' . $dbObj->error;
                                }
                            }
                            else
                            {
                                $pUserAccountArray['result'] = 'str_DatabaseError';
                                $pUserAccountArray['resultparam'] = 'externalloginupdate bind ' . $dbObj->error;
                            }

                            $stmt->free_result();
                            $stmt->close();
                            $stmt = null;
                        }
                        else
                        {
                            $pUserAccountArray['result'] = 'str_DatabaseError';
                            $pUserAccountArray['resultparam'] = 'externalloginupdate prepare ' . $dbObj->error;
                        }
                    }
                    else
                    {
                        // the script specified that the account details must not be updated so just pretend that they have been
                        $accountDetailsUpdated = 0;
                    }


                    // if the main account details have been updated see if we update other parts of the account
                    if ($accountDetailsUpdated > -1)
                    {
                        // update the credit limit / account balance if required
                        if ($pUpdateAccountBalance)
                        {
                            if ($stmt = $dbObj->prepare('UPDATE `USERS` SET `creditlimit` = ?, `accountbalance` = ? WHERE `id` = ?'))
                            {
                                if ($stmt->bind_param('ddi', $pUserAccountArray['creditlimit'], $pUserAccountArray['accountbalance'], $userAccountID))
                                {
                                    if (! $stmt->execute())
                                    {
                                        $pUserAccountArray['result'] = 'str_DatabaseError';
                                        $pUserAccountArray['resultparam'] = 'externalloginupdatecredit execute ' . $dbObj->error;
                                    }
                                    else
                                    {
                                        $accountDetailsUpdated += 1;
                                    }
                                }
                                else
                                {
                                    $pUserAccountArray['result'] = 'str_DatabaseError';
                                    $pUserAccountArray['resultparam'] = 'externalloginupdatecredit bind ' . $dbObj->error;
                                }

                                $stmt->free_result();
                                $stmt->close();
                                $stmt = null;
                            }
                            else
                            {
                                $pUserAccountArray['result'] = 'str_DatabaseError';
                                $pUserAccountArray['resultparam'] = 'externalloginupdatecredit prepare ' . $dbObj->error;
                            }
                        }


                        // if we have no error update the gift card balance if required
                        if ($pUserAccountArray['result'] == '')
                        {
                            if ($pUpdateGiftCardBalance)
                            {
                                if ($stmt = $dbObj->prepare('UPDATE `USERS` SET `giftcardbalance` = ? WHERE `id` = ?'))
                                {
                                    if ($stmt->bind_param('di', $pUserAccountArray['giftcardbalance'], $userAccountID))
                                    {
                                        if (! $stmt->execute())
                                        {
                                            $pUserAccountArray['result'] = 'str_DatabaseError';
                                            $pUserAccountArray['resultparam'] = 'externalloginupdategiftcard execute ' . $dbObj->error;
                                        }
                                        else
                                        {
                                            $accountDetailsUpdated += 1;
                                        }
                                    }
                                    else
                                    {
                                        $pUserAccountArray['result'] = 'str_DatabaseError';
                                        $pUserAccountArray['resultparam'] = 'externalloginupdategiftcard bind ' . $dbObj->error;
                                    }

                                    $stmt->free_result();
                                    $stmt->close();
                                    $stmt = null;
                                }
                                else
                                {
                                    $pUserAccountArray['result'] = 'str_DatabaseError';
                                    $pUserAccountArray['resultparam'] = 'externalloginupdategiftcard prepare ' . $dbObj->error;
                                }
                            }
                        }
                    }

                    // only update the activity log if any of the user account details have been updated
                    if ($accountDetailsUpdated > 0)
                    {
                        // if the update has worked re-load the user account and update the activity log
                        if ($pUserAccountArray['result'] == '')
                        {
                            $pUserAccountArray = DatabaseObj::getUserAccountFromID($userAccountID);

                            DatabaseObj::updateActivityLog($pSessionRef, 0, $pUserAccountArray['recordid'], $pUserAccountArray['login'], $pUserAccountArray['contactfirstname'] . ' ' . $pUserAccountArray['contactlastname'], 0, 'CUSTOMER', 'EXTERNAL LOGIN UPDATE', '', 1);
                        }
                    }
                }
                else
                {
                    if ($stmt = $dbObj->prepare('INSERT INTO USERS (`datecreated`, `companycode`, `webbrandcode`, `login`, `password`, `customer`, `usertype`, `groupcode`, `accountcode`,
                            `companyname`, `address1`, `address2`, `address3`, `address4`, `city`, `county`, `state`, `regioncode`, `region`, `addressupdated`,
                            `postcode`, `countrycode`, `countryname`, `telephonenumber`, `emailaddress`, `contactfirstname`, `contactlastname`, `usedefaultpaymentmethods`,
                            `paymentmethods`, `taxcode`, `shippingtaxcode`, `registeredtaxnumbertype`, `registeredtaxnumber`, `uselicensekeyforshippingaddress`,
                            `modifyshippingaddress`, `modifyshippingcontactdetails`, `uselicensekeyforbillingaddress`, `modifybillingaddress`, `useremaildestination`,
                            `defaultaddresscontrol`, `modifypassword`, `creditlimit`, `accountbalance`, `giftcardbalance`, `sendmarketinginfo`, `sendmarketinginfooptindate`,
							`usedefaultvouchersettings`, `allowvouchers`, `allowgiftcards`, `active`, `usedefaultcurrency`, `currencycode`)
                            VALUES (NOW(), ?, ?, ?, ?, 1, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,
                            ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'))
                    {
                        if ($stmt->bind_param('ssssis' . 'ssss' . 'ssss' . 'sssi' . 'ssss' . 'sss' . 'isss' . 'isi' . 'ii' . 'iii' . 'iid' . 'ddis' . 'iii' . 'iis',
                                $pCompanyCode, $pUserAccountArray['webbrandcode'], $pUserAccountArray['login'], $userAccountPasswordHash, $pUserAccountArray['usertype'], $pUserAccountArray['groupcode'],
                                $pUserAccountArray['accountcode'], $pUserAccountArray['companyname'], $pUserAccountArray['address1'], $pUserAccountArray['address2'],
                                $pUserAccountArray['address3'], $pUserAccountArray['address4'], $pUserAccountArray['city'], $pUserAccountArray['county'],
                                $pUserAccountArray['state'], $pUserAccountArray['regioncode'], $pUserAccountArray['region'], $pUserAccountArray['addressupdated'],
                                $pUserAccountArray['postcode'], $pUserAccountArray['countrycode'], $pUserAccountArray['countryname'], $pUserAccountArray['telephonenumber'],
                                $pUserAccountArray['emailaddress'], $pUserAccountArray['contactfirstname'], $pUserAccountArray['contactlastname'],
                                $pUserAccountArray['usedefaultpaymentmethods'], $pUserAccountArray['paymentmethods'], $pUserAccountArray['taxcode'], $pUserAccountArray['shippingtaxcode'],
                                $pUserAccountArray['registeredtaxnumbertype'], $pUserAccountArray['registeredtaxnumber'], $pUserAccountArray['uselicensekeyforshippingaddress'],
                                $pUserAccountArray['canmodifyshippingaddress'], $pUserAccountArray['canmodifyshippingcontactdetails'],
                                $pUserAccountArray['uselicensekeyforbillingaddress'], $pUserAccountArray['canmodifybillingaddress'], $pUserAccountArray['useremaildestination'],
                                $pUserAccountArray['defaultaddresscontrol'], $pUserAccountArray['canmodifypassword'], $pUserAccountArray['creditlimit'],
                                $pUserAccountArray['accountbalance'], $pUserAccountArray['giftcardbalance'], $pUserAccountArray['sendmarketinginfo'], $pUserAccountArray['sendmarketinginfooptindate'],
								$pUserAccountArray['usedefaultvouchersettings'], $pUserAccountArray['allowvouchers'], $pUserAccountArray['allowgiftcards'],
                                $pUserAccountArray['isactive'], $pUserAccountArray['usedefaultcurrency'], $pUserAccountArray['currencycode']))
                        {
                            if ($stmt->execute())
                            {
                                // if the insert has worked re-load the user account and update the activity log
                                $userAccountID = $dbObj->insert_id;
                                $pUserAccountArray = DatabaseObj::getUserAccountFromID($userAccountID);

                                DatabaseObj::updateActivityLog($pSessionRef, 0, $pUserAccountArray['recordid'], $pUserAccountArray['login'], $pUserAccountArray['contactfirstname'] . ' ' . $pUserAccountArray['contactlastname'], 0, 'CUSTOMER', 'EXTERNAL LOGIN INSERT', '', 1);

                                DatabaseObj::updateActivityLog($pSessionRef, 0, $pUserAccountArray['recordid'], $pUserAccountArray['login'], $pUserAccountArray['contactfirstname'] . ' ' . $pUserAccountArray['contactlastname'], 0, 'CUSTOMER', 'UPDATEPREFERENCES', $pUserAccountArray['sendmarketinginfo'] . ' ' . $pUserAccountArray['recordid'], 1);

								// include data export and trigger customer add
								require_once('../Utils/UtilsDataExport.php');
								DataExportObj::EventTrigger(TPX_TRIGGER_CUSTOMER_ADD, 'CUSTOMER', $userAccountID, 0);
                            }
                            else
                            {
                                // check for a duplicate index error which could occur if there is another type of account with the same credentials
                                if ($stmt->errno == 1062)
                                {
                                    $pUserAccountArray['result'] = 'str_ErrorDuplicateUserName';
                                    $pUserAccountArray['resultparam'] = '';
                                }
                                else
                                {
                                    $pUserAccountArray['result'] = 'str_DatabaseError';
                                    $pUserAccountArray['resultparam'] = 'externallogininsert execute ' . $dbObj->error;
                                }
                            }
                        }
                        else
                        {
                            $pUserAccountArray['result'] = 'str_DatabaseError';
                            $pUserAccountArray['resultparam'] = 'externallogininsert bind ' . $dbObj->error;
                        }

                        $stmt->free_result();
                        $stmt->close();
                        $stmt = null;
                    }
                    else
                    {
                        $pUserAccountArray['result'] = 'str_DatabaseError';
                        $pUserAccountArray['resultparam'] = 'externallogininsert prepare ' . $dbObj->error;
                    }
                }

                $dbObj->close();
            }
            else
            {
                $pUserAccountArray['result'] = 'str_DatabaseError';
                $pUserAccountArray['resultparam'] = 'externallogin connect ' . $dbObj->error;
            }
        }

        if ($pUserAccountArray['result'] == '')
        {
            $pUserAccountArray['result'] = $result;
            $pUserAccountArray['resultparam'] = $resultParam;
        }

        $pUserAccountID = $userAccountID;

        return $pUserAccountArray;
    }

    static function authenticateLogin($pReason, $pSessionRef, $pIsOrderSession, $pLanguageCode, $pWebBrandCode, $pGroupCode, $pSourceURL,
            $pLogin, $pPasswordFormat, $pPassword, $pOnlyAcceptCustomerLogins, $pStartSessionOnSuccess, $pIsSingleSignOnCheck, $pSSOToken,
            $pSSOPrivateData, $pBasketData, $pTaopixOnlineLogin = false, $pIPAddress = '')
    {
		global $ac_config;
        global $gConstants;

        $result = '';
        $resultParam = '';
        $resultArray = Array();
        $canCreateAccounts = 0;
        $isCustomer = 0;
        $userType = 0;
        $canCheckLogin = true;
        $companyCode = '';
        $ssoUsed = false;
        $ssoToken = '';
        $ssoPrivateDataArray = array();
        $assetServiceDataArray = array();
        $ssoExpireData = '';
        $ssoKey = '';
        $userAccountID = 0;
		$failedLoginID = 0;
		$loginAttemptCount = 0;
        $origAddressUpdated = 1;
        $active = 0;
        $sitetype = 0;
        $siteonline = 0;
        $licenseKeyArray = array();
        $passwordFormat = $pPasswordFormat;
		$ipAddress = ($pIPAddress == '') ? UtilsObj::getClientIPAddress() : $pIPAddress;
		$ipAddressAllowed = true;
		$foundBlockedIPAddress = false;
		$nextValidLoginDate = '0000-00-00 00:00:00';
		$blockReason = TPX_BLOCK_REASON_NONE;
		$evtStatusSuccess = TPX_TASKMANAGER_STATUS_COMPLETED;
		$taskCodeDeletion = 'TAOPIX_DATADELETION';
        $userEmailAccountArray = array();

        // Copy the value of $pLogin into $loginToUse.
        // Value contained in $pLogin may be a username or an email address.
        // If the value is an email address, it will be updated during the login process to
        // the username of the account, allowing the login to be processed as normal.
        $loginToUse = $pLogin;

        $sessionRef = $pSessionRef;

        // if this is a single sign-on request into control centre then we might not have a group code
        // in this situation we need to try and retrieve the default one for the brand
        if (($pIsSingleSignOnCheck) && ($pReason == TPX_USER_AUTH_REASON_WEB_INIT) && ($pGroupCode == ''))
        {
            if (($gConstants['optionwscrp']) && (file_exists('../Customise/scripts/EDL_ExternalCustomerAccount.php')))
            {
                require_once('../Customise/scripts/EDL_ExternalCustomerAccount.php');

                if (method_exists('ExternalCustomerAccountObj', 'ssoGetBrandDefaultGroupCode'))
                {
                    // configure the parameter array
                    $paramArray = Array();
                    $paramArray['brandcode'] = $pWebBrandCode;

                    // retrieve the default group code
                    $pGroupCode = ExternalCustomerAccountObj::ssoGetBrandDefaultGroupCode($paramArray);
                }
            }
        }

        // if we have been provided with a group code retrieve the license key data
        if ($pGroupCode != '')
        {
            $licenseKeyArray = DatabaseObj::getLicenseKeyFromCode($pGroupCode);
            $companyCode = $licenseKeyArray['companyCode'];
            $canCreateAccounts = $licenseKeyArray['cancreateaccounts'];
        }

		// Don't rate limit SSO checks, we will already know at this point that SSO is not being used because they've arrived at the our sign in screen.
		if (! $pIsSingleSignOnCheck)
		{
			// Lookup the IP address to check if it has been rate limited or blocked.
			$lookupBlockedIPAddressResult = self::lookupBlockedIPAddress($ipAddress);

			if ($lookupBlockedIPAddressResult['error'] == '')
			{
				// Date of next valid login to be used with the error message.
				$nextValidLoginDate = $lookupBlockedIPAddressResult['data']['nextvalidlogindate'];

				// Check if the IP is currently blocked.
				$ipAddressAllowed = $lookupBlockedIPAddressResult['data']['canlogin'];

				// Found a record for that IP address.
				$foundBlockedIPAddress = ($lookupBlockedIPAddressResult['data']['id'] > 0);

				// Why the IP was blocked (e.g. rate limit or too many failed logins).
				$blockReason = $lookupBlockedIPAddressResult['data']['blockreason'];
			}

			// If the IP address is allowed to login, check for any previous recent failed attempts,
			if ($ipAddressAllowed)
			{
				// Get number of failed login attempts from the Activity Log for the IP address.
				$getFailedLoginAttemptsResult = self::getFailedLoginAttemptsForIPAddress($ipAddress);

				if ($getFailedLoginAttemptsResult['error'] == '')
				{
					// Check the number of failed login attempts from a single IP address is greater than the configured value.
					if ($getFailedLoginAttemptsResult['failedloginscount'] >= $gConstants['maxiploginattempts'])
					{
						if ($foundBlockedIPAddress)
						{
							// Already has a previous entry so update it.
							$updateBlockedIPAddressResult = self::updateBlockedIPAddress($lookupBlockedIPAddressResult['data']['id'], false);

							if ($updateBlockedIPAddressResult['error'] == '')
							{
								$nextValidLoginDate = $updateBlockedIPAddressResult['nextvalidlogindate'];
							}
							else
							{
								$result = $updateBlockedIPAddressResult['error'];
								$resultParam = $updateBlockedIPAddressResult['errorparam'];
								$userAccountArray = DatabaseObj::getEmptyUserAccount();
							}
						}
						else
						{
							// Create a new entry for this IP address.
							$insertBlockedIPAddressResult = self::insertBlockedIPAddress($ipAddress, false);

							if ($insertBlockedIPAddressResult['error'] == '')
							{
								$nextValidLoginDate = $insertBlockedIPAddressResult['data']['nextvalidlogindate'];
							}
							else
							{
								$result = $insertBlockedIPAddressResult['error'];
								$resultParam = $insertBlockedIPAddressResult['errorparam'];
								$userAccountArray = DatabaseObj::getEmptyUserAccount();
							}
						}

						// IP address is blocked.
						$ipAddressAllowed = false;
						$blockReason = TPX_BLOCK_REASON_IP_BLOCK;
					}
				}
				else
				{
					$result = $getFailedLoginAttemptsResult['error'];
					$resultParam = $getFailedLoginAttemptsResult['errorparam'];
					$userAccountArray = DatabaseObj::getEmptyUserAccount();
				}
			}
		}

		if ($result == '')
		{
			// if this is not the single sign-on check then attempt to authenticate using parameters provided
			if (! $pIsSingleSignOnCheck)
			{
				// before performing any kind of authentication make sure that neither the login or password are empty
				if (($loginToUse != '') && ($pPassword != ''))
				{
					// first check the taopix database for any matching login
					$userAccountArray = DatabaseObj::getUserAccountFromLogin($loginToUse);

                    if ('' == $userAccountArray['result'])
                    {
                        // we have a customer account so we need to check again for a matching password and a matching brand
                        $userAccountArray = DatabaseObj::getUserAccountFromBrandAndLoginAndPassword($pWebBrandCode, $loginToUse, $pPassword, $pPasswordFormat);
                    }

                    // If no account with the login exists (str_ErrorNoAccount), check if the login was an email address and check the email field.
					if ($userAccountArray['result'] == 'str_ErrorNoAccount')
					{
						// Chack if the login was attempted using an email address.
						if (UtilsObj::validateEmailAddress($loginToUse))
						{
                            // The login attempt was made with an email address, get a list of customer accounts which use that email address.
                            $userEmailAccountArray = DatabaseObj::getValidUserAccountsForEmailAndBrand($pWebBrandCode, $loginToUse, $pPassword, $pPasswordFormat);

                            if ($userEmailAccountArray['result'] == '')
                            {
                                if ($userEmailAccountArray['validcount'] > 0)
                                {
                                    // Check the number of accounts found for the email address.
                                    if ($userEmailAccountArray['count'] == 1)
                                    {
                                        // A single account matched the details, use this to login.
                                        $loginToUse = $userEmailAccountArray['accounts'][0]['login'];

                                        // Get the details for the account, using the username.
                                        $userAccountArray = DatabaseObj::getUserAccountFromLogin($loginToUse);
                                    }
                                    else
                                    {
                                        // More than 1 account with the email address was found, do not sign in, and send an email with usernames.
                                        $userAccountArray['result'] = 'str_ErrorMultipleAccounts';

                                        // Prevent identifying of a specific account as a failed login attempt.
                                        $userAccountArray['failedloginid'] = 0;

                                        // Send an email listing usernames using the same email address.
                                        // $loginToUse will still be the email address used in the login attempt.
                                        self::sendExistingAccountsEmail($pWebBrandCode, $loginToUse, $userEmailAccountArray['accounts']);
                                    }
                                }
                                else
                                {
                                    // No valid accounts were found, set the result string to no account error.
                                    $userAccountArray['result'] = 'str_ErrorNoAccount';
                                }
                            }
                            else
                            {
                                // Update the $userAccountArray error messages.
                                $userAccountArray['result'] = $userEmailAccountArray['result'];
                                $userAccountArray['resultparam'] = $userEmailAccountArray['resultparam'];
                            }
						}
                    }

					if ($userAccountArray['result'] == '')
					{
						// if we have a non-customer account with a matching login then check again for a matching password so that we report the correct error
						// we also know at this point that we cannot check the login via an external script
						if ($userAccountArray['iscustomer'] == 0)
						{
							$userAccountArray = DatabaseObj::getUserAccountFromLoginAndPassword($loginToUse, $pPassword, $pPasswordFormat);

							$canCheckLogin = false;
						}
						else
						{
							// we have a customer account so we need to check again for a matching password and a matching brand
							$userAccountArray = DatabaseObj::getUserAccountFromBrandAndLoginAndPassword($pWebBrandCode, $loginToUse, $pPassword, $pPasswordFormat);

							// if we have found a matching record check to make sure the end user license key is active.
							if ($userAccountArray['result'] == '')
							{
								$userLicenseKeyArray = DatabaseObj::getLicenseKeyFromCode($userAccountArray['groupcode']);

								// if we have found a matching license key record we need to check to see if it is active
								if ($userLicenseKeyArray['result'] == '')
								{
									// if the license key is inactive then we cannot proceed to check login
									if ($userLicenseKeyArray['isactive'] == 0)
									{
										$userAccountArray['result'] = 'str_ErrorAccountNotActive';
										$canCheckLogin = false;
									}
								}
								else
								{
									$userAccountArray['result'] = $userLicenseKeyArray['result'];
									$canCheckLogin = false;
								}
							}
						}

						// if we have found a matching record remember the record id
						if ($userAccountArray['result'] == '')
						{
							$userAccountID = $userAccountArray['recordid'];
						}
						else
						{
							$failedLoginID = $userAccountArray['failedloginid'];
							$loginAttemptCount = $userAccountArray['loginattemptcount'];
						}
					}
					else
					{
						// we don't have any user accounts with this login so we are safe to check via an external script later on
						$failedLoginID = $userAccountArray['failedloginid'];
						$loginAttemptCount = $userAccountArray['loginattemptcount'];
					}
				}
				else
				{
					// we don't seem to have a login and password

					// there is no way we want to proceed with any type of login (not even external customer account) so just define an empty user account and set an error
					$userAccountArray = DatabaseObj::getEmptyUserAccount();
					$userAccountArray['result'] = 'str_ErrorNoAccount';
					$canCheckLogin = false;
				}
			}
			else
			{
				// this is a single sign-on check so start with an empty user account
				$userAccountArray = DatabaseObj::getEmptyUserAccount();
            }

			// if we have found a non-customer account then we cannot continue
			// if we have found a customer account then we can continue to verify via an external script
			// if this is a single sign-on check then we can also continue via an external script
			if (($canCheckLogin) && ($gConstants['optionwscrp']) && (file_exists('../Customise/scripts/EDL_ExternalCustomerAccount.php')))
			{
				require_once('../Customise/scripts/EDL_ExternalCustomerAccount.php');

				if (! $pIsSingleSignOnCheck)
				{
					// this is not the single sign-on check
					// make sure the login method exists and perform some additional searches to see if we have a matching account
					if (method_exists('ExternalCustomerAccountObj', 'login'))
					{
						// if we did not find a matching account check again to see if the login exists for the brand but with a different password
						if ($userAccountID == 0)
						{
							// before we look for the account we need to store the result of the last account lookup (brand, login, password)
							// this is because the following lookup (brand, login) may succeed which would then allow NOTHANDLED script results to succeed
							// no matter if the password is correct or not
							$result = $userAccountArray['result'];
							$resultParam = $userAccountArray['resultparam'];

							$userAccountArray = DatabaseObj::getUserAccountFromBrandAndLogin($pWebBrandCode, $loginToUse);

							if ($userAccountArray['result'] == '')
							{
								$userAccountID = $userAccountArray['recordid'];
							}
							else
							{
								$failedLoginID = $userAccountArray['failedloginid'];
								$loginAttemptCount = $userAccountArray['loginattemptcount'];
							}

							// restore the result of the previous (brand, login, password) lookup
							$userAccountArray['result'] = $result;
							$userAccountArray['resultparam'] = $resultParam;
						}
					}
					else
					{
						// the login method does not exist so we cannot continue
						$canCheckLogin = false;
					}
				}
				else
				{
					// this is a single sign-on check so make sure that method we need exists
					if (! method_exists('ExternalCustomerAccountObj', 'ssoLogin'))
					{
						// the single sign-on method does not exist so we cannot continue
						$canCheckLogin = false;
					}
				}

				if ($canCheckLogin)
				{
					// get the default values for the brand
					$brandingArray = DatabaseObj::getBrandingFromCode($pWebBrandCode);

					if ($pWebBrandCode != '')
					{
						$companyCode = $brandingArray['companycode'];
					}

					// create an empty user account based on the licensekey and brand settings
					$externalUserAccountArray = self::createEmptyUserAccount($licenseKeyArray, $pGroupCode, $pWebBrandCode, $brandingArray);

					// define the default parameter array
					$paramArray = Array();
					$paramArray['languagecode'] = $pLanguageCode;
					$paramArray['designergroupcode'] = $pGroupCode;
					$paramArray['brandcode'] = $pWebBrandCode;
					$paramArray['isordersession'] = $pIsOrderSession;
					$paramArray['useraccount'] = &$externalUserAccountArray;
					// default the address updated flag to 1 so that the user never needs to confirm their details
					$paramArray['useraccount']['addressupdated'] = 1;

					if (! $pIsSingleSignOnCheck)
					{
						// authenticate the user via the external script

						// set some additional defaults for the user array
						$externalUserAccountArray['accountcode'] = $userAccountArray['accountcode'];
						$externalUserAccountArray['login'] = $loginToUse;
						// we do not need to know the password as it is handled by the external script, we can improve security by not storing the same password in the our database

						// configure the parameter array
						$paramArray['accountgroupcode'] = $userAccountArray['groupcode'];
						$paramArray['id'] = $userAccountID;
						$paramArray['login'] = $loginToUse;
						$paramArray['accountcode'] = $userAccountArray['accountcode'];

						// pass in the plaintext/md5 password with the passwordformat, it's up to the script to handle verifying the password
						$paramArray['passwordformat'] = $pPasswordFormat;
						$paramArray['password'] = $pPassword;

						if ($userAccountArray['recordid'] > 0)
						{
							$origAddressUpdated = $userAccountArray['addressupdated'];
							$paramArray['status'] = $origAddressUpdated;
						}
						else
						{
							$paramArray['status'] = -1;
						}

						$externalResponse = ExternalCustomerAccountObj::login($paramArray);

						if ($externalResponse['result'] == '')
						{
							$userAccountArray = $externalResponse['useraccount'];

							// the external customer script does not allow for the group code to be changed
							$externalResponse['updategroupcode'] = false;

							// the user account details always needs updating when coming from the external customer api script
							$externalResponse['updateaccountdetails'] = true;

							// the format of the password should be returned by the script if they wish to store the password in Taopix
							$passwordFormat = $externalResponse['passwordformat'];

							// sometimes the external system might be communicating with an SSO server and the token from this server needs sending to online
							// for later purposes.
							if (array_key_exists('ssotoken', $externalResponse))
							{
								$ssoToken = $externalResponse['ssotoken'];
							}

							if (array_key_exists('ssoprivatedata', $externalResponse))
							{
								$ssoPrivateDataArray = $externalResponse['ssoprivatedata'];
							}
						}
					}
					else
					{
						// perform the single sign-on check
						$ssoUsed = true;

						// define the some additional parameter array properties
						$paramArray['reason'] = $pReason;
						$paramArray['ssotoken'] = $pSSOToken;
						$paramArray['ssoprivatedata'] = $pSSOPrivateData;
						$paramArray['ssostage'] = 1;

						// if we have been provided with a url pass that otherwise pass the current url
						if ($pSourceURL != '')
						{
							$paramArray['sourceurl'] = $pSourceURL;
						}
						else
						{
							$paramArray['sourceurl'] = UtilsObj::getCurrentURL();
						}

						// parse the url of the source url so that we can determine the sso stage
						$queryString = parse_url($paramArray['sourceurl'], PHP_URL_QUERY);

						// make sure the URL has a query string part
						if ($queryString != '')
						{
							$queryStringParts = array();

							// parse the query string into separate parts
							parse_str($queryString, $queryStringParts);

							// make sure the sso parameter is set
							if (!empty($queryStringParts['sso']))
							{
								// if the sso stage is set to 2 we have come back from an sso callback
								if ($queryStringParts['sso'] == '2')
								{
									// assume the worse that we can't move the stage on
									$canMoveStage = false;

									// read the ssokey from the URL
									$ssoKey = UtilsObj::getGETParam('ssokey', '');

									if ($ssoKey != '')
									{
										$ssoPrivateData = '';

										$paramArray['ssostage'] = 2;

										$canMoveStage = true;

										// get the data from the data store for the sso key
										$ssoDataRecordReturnArray = self::getSSODataRecord($ssoKey);

										if ($ssoDataRecordReturnArray['result'] == '')
										{
											// reason being set to -1 means that no record was found
											// either this is someone calling the system with sso=2 when they should or someone has refreshed the
											// page which has sso=2 in the query string
											if ($ssoDataRecordReturnArray['reason'] != -1)
											{
												$paramArray['ssoprivatedata'] = $ssoDataRecordReturnArray['data'];
											}
											else
											{
												$canMoveStage = false;
											}
										}
										else
										{
											$result = $ssoDataRecordReturnArray['result'];
											$resultParam = $ssoDataRecordReturnArray['resultparam'];
										}
									}

									if (! $canMoveStage)
									{
										// looks like the cookie has either been deleted or tampered with
										$paramArray['ssostage'] = 1;
									}

								}
								else
								{
									$paramArray['sourceurl'] = str_replace('sso=1', 'sso=2', $paramArray['sourceurl']);
								}
							}
							else
							{
								$paramArray['sourceurl'] = UtilsObj::correctPath($paramArray['sourceurl'], "/", false);

								// sso is missing so lets add it
								$paramArray['sourceurl'] = UtilsObj::addURLParameter($paramArray['sourceurl'], 'sso', 2);
							}
						}
						else
						{
							// no query string so let add sso to it
							$paramArray['sourceurl'] = UtilsObj::correctPath($paramArray['sourceurl'], "/", false) . '?sso=2';
						}

						if ($pReason === TPX_USER_AUTH_REASON_HIGHLEVEL_ONLINE_PROJECT_PREVIEW)
						{
							// Don't do a ssoLogin if opening a share link and force a NOTHANDLED result.
							$externalResponse = ['result' => 'NOTHANDLED'];
						}
						else
						{
							// Call the custom ssoLogin function.
							$externalResponse = ExternalCustomerAccountObj::ssoLogin($paramArray);
						}

						// if we have received no error process the result
						if ($externalResponse['result'] == '')
						{
							if ($ssoKey != '')
							{
								// delete the record from the database and clean up and expired records
								// we don't need to worry if this returns any errors since it is purely a clean
								// up task
								$ssoDataDeleteReturnArray = self::deleteAuthenticationDataRecords($ssoKey);
							}

							// retrieve the token and private data as we always use them no matter why single sign-on was called
							$ssoToken = $externalResponse['ssotoken'];
							$ssoPrivateDataArray = $externalResponse['ssoprivatedata'];
							$assetServiceDataArray = $externalResponse['assetservicedata'];
							$ssoExpireData = $externalResponse['ssoexpiredate'];

							// retrieve the data from the script which is relevant to the single sign-on reason
							$userAccountArray = $externalResponse['useraccount'];
						}
					}

					// the external authentication failed
					// the result SSOREDIRECT allow a redirection to occur to handle the collection of a single sign-on token from a different domain
					// the result NOTHANDLED is the same as if the script didn't execute so we process the login as normal
					if ($externalResponse['result'] == '')
					{
						// if we are reauthenticating there is no need to update or insert the user record since we was just checking the login
						if ($pReason != TPX_USER_AUTH_REASON_ONLINE_REAUTHENTICATE)
						{
							$userAccountArray = self::updateOrInsertExternalAccount($userAccountID, $userAccountArray, $pIsSingleSignOnCheck, $sessionRef,
																								$loginToUse, $passwordFormat, $pWebBrandCode, $pGroupCode, $companyCode,
																								$externalResponse['updategroupcode'], $externalResponse['updateaccountdetails'],
																								$externalResponse['updateaccountbalance'], $externalResponse['updategiftcardbalance']);
						}
					}
					elseif ($externalResponse['result'] == 'SSOREDIRECT')
					{
						$userAccountArray['result'] = $externalResponse['result'];

						$userAccountArray['resultparam'] = $externalResponse['resultparam'];


						$urlSource = $paramArray['sourceurl'];

						if (UtilsObj::isHighLevelSSOReason($pReason))
						{
							$hl_config = UtilsObj::readWebBrandConfigFile('../config/onlinebaskethighlevelapi.conf', $pWebBrandCode);

							$urlSource = UtilsObj::correctPath($hl_config['REDIRECTIONURL'], '/', true);

							if (($pReason == TPX_USER_AUTH_REASON_HIGHLEVEL_ONLINE_PROJECT_CREATE) ||
								($pReason == TPX_USER_AUTH_REASON_HIGHLEVEL_CHECKOUT) ||
								($pReason == TPX_USER_AUTH_REASON_HIGHLEVEL_ONLINE_PROJECT_EDIT))
							{
								$urlSource .= $hl_config['HIGHLEVELBASKETAPIREDIRECTPAGE'];
							}
						}

						$ssoSystemURL = $userAccountArray['resultparam'];

						// create the data record for later purposes
						$ssoDataRecordReturnArray = self::createSSODataRecord($externalResponse['ssoprivatedata'], $urlSource, $ssoSystemURL, $pReason, $sessionRef);

						if ($ssoDataRecordReturnArray['result'] == '')
						{
							$ssoKey = $ssoDataRecordReturnArray['authkey'];

							// get the brand url so that we have some where we can callback to
							$url = UtilsObj::getBrandedWebUrl($pWebBrandCode);

							// set the redirection url to be the proxy cookie broker script
							// getBrandedWebURL will correct the url so there is no need to
							$userAccountArray['resultparam'] = $url . 'sso/cookie.php?ssoref=' . $ssoKey . '&brandcode=' . $pWebBrandCode;
						}
						else
						{
							$result = $ssoDataRecordReturnArray['result'];
							$resultParam = $ssoDataRecordReturnArray['resultparam'];
						}

					}
					elseif ($externalResponse['result'] == 'NOTHANDLED')
					{
						// if this is a single sign-on request then not handled means no single sign-on
						// if this is not a single sign-on request then we use the result of trying to find the user account within the taopix database
						if ($pIsSingleSignOnCheck)
						{
							$userAccountArray['result'] = 'NOSSO';
						}
					}
					else
					{
						$userAccountArray['result'] = $externalResponse['result'];
						$userAccountArray['resultparam'] = $externalResponse['resultparam'];
					}
				}
			}

			// get the result of authenticating the user
			if ((($pIsSingleSignOnCheck) && ($ssoUsed)) || (! $pIsSingleSignOnCheck))
			{
				// we have either used single sign-on to authenticate or used the parameters to authenticate
				$result = $userAccountArray['result'];
				$resultParam = $userAccountArray['resultparam'];
			}
			else
			{
				// this was a single sign-on request but the system was not set up to handle it
				// set an error code so we don't perform any other processing
				$result = 'NOSSO';
			}

			// If no error has occurred make sure that the source IP address is allowed.
			if (($result == '') && ($userAccountArray['iscustomer'] == 0))
			{
				// If the user is NOT a customer, check if the IP address is in the access list.
				$isIPAllowed = DatabaseObj::isUserIPAllowed($_SERVER['REMOTE_ADDR'], $userAccountArray['ipaccesslist'], $userAccountArray['ipaccesstype'], $userAccountArray['companycode']);
				$result = $isIPAllowed['result'];
				$ipCount = $isIPAllowed['count'];

				// Check the result of isUserIPAllowed to see if the login can continue.
				if ((! $ipAddressAllowed) && (($result == '') && ($ipCount == 0)))
				{
					// The IP address was blocked, and the IP access list was empty, make sure the login is rejected.
					$result = self::getBlockedLoginReasonString($blockReason);
				}
			}
			else if ((! $ipAddressAllowed) && ($result != 'NOSSO'))
			{
				// If result is NOSSO then we don't want to override the result, this is from the first call to authenticateLogin via displayLogin to detect if SSO is being used.

				// The IP address has been blocked due to to many login attempts, and it does not exist in the list of allowed IP addresses.
				$result = self::getBlockedLoginReasonString($blockReason);
			}

			// if no error has occurred we have found an account with the correct details
			// check to see if the users assigned production site is active.
			if ($result == '')
			{
				if (($gConstants['optionms']) && ($userAccountArray['owner'] != ''))
				{
					$dbObj = DatabaseObj::getGlobalDBConnection();
					if ($dbObj)
					{
						if ($stmt = $dbObj->prepare('SELECT `active`, `sitetype`, `siteonline` FROM `SITES` WHERE `code` = ?'))
						{
							if ($stmt->bind_param('s', $userAccountArray['owner']))
							{
								if ($stmt->bind_result($active, $sitetype, $siteonline))
								{
									if ($stmt->execute())
									{
										if ($stmt->fetch())
										{
											if ($active == 0)
											{
												switch ($userAccountArray['usertype'])
												{
													case TPX_LOGIN_DISTRIBUTION_CENTRE_USER:
														$result = 'str_ErrorDistributionCentreInActive';
														break;
													case TPX_LOGIN_STORE_USER:
														$result = 'str_ErrorStoreInActive';
														break;
													default:
														$result = 'str_ErrorProductionSiteInActive';
												}
											}
											else
											{
												if (($sitetype == TPX_SITE_TYPE_DISTRIBUTION_CENTRE) && ($siteonline == 0))
												{
													$result = 'str_ErrorDistributionCentreOffline';
												}
												if (($sitetype == TPX_SITE_TYPE_STORE) && ($siteonline == 0) && ($userAccountArray['usertype'] == TPX_LOGIN_STORE_USER))
												{
													$result = 'str_ErrorStoreOffline';
												}
											}
										}
									}
								}

								$stmt->free_result();
								$stmt->close();
								$stmt = null;
							}
						}

						$dbObj->close();
					}
				}
			}

			if (($result != 'NOSSO') && ($result != 'SSOREDIRECT') && ($result != 'str_ErrorIPBlockedDueToSuspiciousActivity'))
			{
				if ($pSessionRef > 0)
				{
					global $gSession;

					// Make sure that the user that is trying to login is the user that created the original session
					if (($gSession['userid'] != $userAccountID) && ($gSession['userid'] > 0))
					{
						//  you should not be able to create an account only be allowed to login as the existing user
						$canCreateAccounts = 0;
						$result = 'str_ErrorAccountTaskNotAllowed';
					}
				}

				if ((!empty($pBasketData['ref'])) && ($pBasketData['ref'] != '') && ($pIsOrderSession))
				{
					$basketUserIDResult = DatabaseObj::getUserIDGroupCodeFromBasketRef($pBasketData['ref'], true);
					$basketUserID = $basketUserIDResult['basketuserid'];
					if ($basketUserID != $userAccountID)
					{
						$result = 'str_ErrorAccountTaskNotAllowed';
					}
				}
			}

			// if no error has occurred we have found an account with the correct details
			if ($result == '')
			{
				if ($userAccountArray['isactive'] == 0)
				{
					DatabaseObj::updateActivityLog(0, 0, $userAccountArray['recordid'], $userAccountArray['login'], $userAccountArray['contactfirstname'] . ' ' . $userAccountArray['contactlastname'], 0, 'CUSTOMER', 'LOGIN', 'str_ErrorAccountNotActive', 0, $ipAddress);

					$result = 'str_ErrorAccountNotActive';
				}
				else
				{
					// if we can only accept customer logins make sure the account is a customer
					if ($pOnlyAcceptCustomerLogins)
					{
						if ($userAccountArray['iscustomer'] == 0)
						{
							DatabaseObj::updateActivityLog($sessionRef, 0, $userAccountArray['recordid'], $userAccountArray['login'], $userAccountArray['contactfirstname'] . ' ' . $userAccountArray['contactlastname'], 0, 'ORDER', 'LOGIN', 'str_ErrorAccountNotACustomer', 0, $ipAddress);

							$result = 'str_ErrorAccountNotACustomer';
						}
					}
					else
					{
						// if logging in as a customer we must make sure that it is allowed
						if (($userAccountArray['iscustomer'] == 1) && ($ac_config['CUSTOMERACCOUNTLOGINSENABLED'] == '0'))
						{
							DatabaseObj::updateActivityLog($sessionRef, 0, $userAccountArray['recordid'], $userAccountArray['login'], $userAccountArray['contactfirstname'] . ' ' . $userAccountArray['contactlastname'], 0, 'CUSTOMER', 'LOGIN', 'str_ErrorAccountTaskNotAllowed', 0, $ipAddress);

							$result = 'str_ErrorAccountTaskNotAllowed';
						}
					}
				}

				$userType = $userAccountArray['usertype'];
				$isCustomer = $userAccountArray['iscustomer'];
			}
			elseif (($result != 'NOSSO') && ($result != 'SSOREDIRECT'))
			{
				// if this is an order session then
				if ($pIsOrderSession)
				{
					if ($pReason == TPX_USER_AUTH_REASON_WEB_CART_INIT)
					{
						DatabaseObj::updateActivityLog($sessionRef, 0, 0, $loginToUse, '', 0, 'ORDER', 'LOGIN', $result, 0, $ipAddress);
					}
					else
					{
						DatabaseObj::updateActivityLog($sessionRef, 0, 0, $loginToUse, '', 0, 'CUSTOMER', 'LOGIN', $result, 0, $ipAddress);
					}
				}
				else
				{
					DatabaseObj::updateActivityLog($sessionRef, 0, 0, $loginToUse, '', 0, 'CUSTOMER', 'LOGIN', $result, 0, $ipAddress);
				}
			}

			// if no error has occurred make sure the user group code matches the license key group code
			// this if to catch a none SSO or external customer login when from both online and desktop
            // only do this check if there is a license key array. if there isn't it will be becasue this is a re-authenticate
			if (($result == '') && (!empty($licenseKeyArray)))
			{
				if ((($pTaopixOnlineLogin) || ($sessionRef > 0)) && ($userAccountArray['groupcode'] != '') && ($userAccountArray['groupcode'] != $licenseKeyArray['groupcode']))
				{
					DatabaseObj::updateActivityLog($sessionRef, 0, $userAccountArray['recordid'], $userAccountArray['login'], $userAccountArray['contactfirstname'] . ' ' . $userAccountArray['contactlastname'], 0, 'ORDER', 'LOGIN', 'str_ErrorAccountMisMatch', 0, $ipAddress);

					$result = 'str_ErrorAccountMisMatch';
				}
			}

			// set the username for the login if the first and last names are missing
			// the first and last names could be missing because if they came from an external source, that source may not provide them
			if (($userAccountArray['contactfirstname'] == '') || ($userAccountArray['contactlastname'] == ''))
			{
				$resultArray['username'] = $userAccountArray['login'];
			}
			else
			{
				$resultArray['username'] = $userAccountArray['contactfirstname'] . ' ' . $userAccountArray['contactlastname'];
			}

			// if no error has occurred we have successfully authenticated so finish off what we need to do
			if ($result == '')
			{
				if ($isCustomer == 1)
				{
					$userType = TPX_LOGIN_CUSTOMER;
				}

				// Disallow the token in a cookie to be resumed, forcing a new token to be generated
				// as a result of a successful login
				CsrfTokenGenerator::allowResumeCookie(false);

				// determine if we need to create a session
				if ($pStartSessionOnSuccess)
				{
					// if this authentication has started from online designer which has been started from high level, update the basket ref and the session basket ref.
					if ((!empty($pBasketData['ref'])) && ($pBasketData['ref'] != ''))
					{
						require_once('../OnlineAPI/OnlineAPI_model.php');

						// We need to create a place holder record in the online basket table so we know a user has logged in from online
						$createBasketRecordResult = OnlineAPI_model::createBasketRecord($userAccountArray['webbrandcode'], $userAccountArray['groupcode'], $pBasketData['ref']);
						$basketRecordID = $createBasketRecordResult['basketrecordid'];

						// we then need to update the place holder record with the userid.
						$updateBasketRefTokenResult = OnlineAPI_model::updateBasketRecordBasketRefAndToken($basketRecordID, '', $pBasketData['ref'], $userAccountID, $userAccountArray['webbrandcode'], $userAccountArray['groupcode']);

						// Update userid the projects in the cart are assigned to.
						$updateUserIDBasketRefForProjectsInBasketResult = OnlineAPI_model::updateUserIDBasketRefForProjectsInBasket($userAccountID, $pBasketData['ref']);

						if ($updateUserIDBasketRefForProjectsInBasketResult['result'] == '')
						{
							$recordID = DatabaseObj::startSession($userAccountArray['recordid'], $userAccountArray['login'], $resultArray['username'], $userType, $userAccountArray['companycode'], $userAccountArray['owner'], $userAccountArray['webbrandcode'], $userAccountArray['groupcode'], $ssoToken, $ssoPrivateDataArray);

							$updateSessionResult = DatabaseObj::linkOnlineBasketToSession($recordID, $pBasketData['ref'], $userAccountID);

							if ($updateSessionResult['result'] == '')
							{
								UtilsObj::setSessionDeviceData(false);

								DatabaseObj::updateSession();

								DatabaseObj::updateActivityLog($recordID, 0, $userAccountArray['recordid'], $userAccountArray['login'], $resultArray['username'], 0, 'ONLINE', 'LOGIN', '', 1, $ipAddress);
							}
							else
							{
								$result = $updateSessionResult['result'];
								$resultParam = $updateSessionResult['resultparam'];
							}
						}
						else
						{
							$result = $updateUserIDBasketRefForProjectsInBasketResult['result'];
							$resultParam = $updateUserIDBasketRefForProjectsInBasketResult['resultparam'];
						}
					}
					else
					{
						$sessionRef = DatabaseObj::startSession($userAccountArray['recordid'], $userAccountArray['login'], $resultArray['username'], $userType, $userAccountArray['companycode'],
									$userAccountArray['owner'], $userAccountArray['webbrandcode'], $userAccountArray['groupcode'], $ssoToken, $ssoPrivateDataArray);

						UtilsObj::setSessionDeviceData();

						DatabaseObj::updateSession();

						if ($pIsOrderSession)
						{
							// logging in to order
							DatabaseObj::updateActivityLog($sessionRef, 0, $userAccountArray['recordid'], $userAccountArray['login'], $resultArray['username'], 0, 'ORDER', 'LOGIN', '', 1, $ipAddress);

							self::setSessionWebBrand($licenseKeyArray['webbrandcode']);
						}
						else
						{
							// logging in but not for an order
							if (($userType != TPX_LOGIN_PRODUCTION_USER) && ($userType != TPX_LOGIN_CUSTOMER))
							{
								// some type of admin
								DatabaseObj::updateActivityLog($sessionRef, 0, $userAccountArray['recordid'], $userAccountArray['login'], $resultArray['username'], 0, 'ADMIN', 'LOGIN', '', 1, $ipAddress);

								self::setSessionWebBrand('');
							}
							else
							{
								DatabaseObj::updateActivityLog($sessionRef, 0, $userAccountArray['recordid'], $userAccountArray['login'], $resultArray['username'], 0, 'CUSTOMER', 'LOGIN', '', 1, $ipAddress);
								$licenseKeyArray = DatabaseObj::getLicenseKeyFromCode($userAccountArray['groupcode']);

								if ($isCustomer == 1)
								{
									// customer
									if ($licenseKeyArray['webbrandcode'] != $pWebBrandCode)
									{
										// force the brand to be one that we have attempted to login at
										DatabaseObj::updateActivityLog($sessionRef, 0, $userAccountArray['recordid'], $userAccountArray['login'], $resultArray['username'], 0, 'ORDER', 'LOGIN', 'str_ErrorAccountMisMatch', 0, $ipAddress);
										$licenseKeyArray['webbrandcode'] = $pWebBrandCode;
										$result = 'str_ErrorAccountMisMatch';

									} else {

										if ($licenseKeyArray['webbrandcode'] != '')
										{
											$brandingInfo = DatabaseObj::getBrandingFromCode($licenseKeyArray['webbrandcode']);
											$userAccountArray['companycode'] = $brandingInfo['companycode'];
										}
										else
										{
											$userAccountArray['companycode'] = $licenseKeyArray['companyCode'];
										}

										$userAccountArray['webbrandcode'] = $licenseKeyArray['webbrandcode'];
									}
								}

								self::setSessionWebBrand($licenseKeyArray['webbrandcode']);
							}
						}
					}
				}
				else
				{
					/*
					 * We have specified that we don't want a session so just record an entry within the activity log.
					 * Updating activity log is handled by Admin_model::reauthenticate for system user re-authentications.
					 */
					if ($pReason != TPX_USER_AUTH_REASON_SYSTEMUSER_REAUTHENTICATE)
					{
						// set the action logged in the activity logged based on the reason this function has been called
						$activityAction = 'LOGIN';
						if ($pReason == TPX_USER_AUTH_REASON_ONLINE_REAUTHENTICATE)
						{
							$activityAction = 'REAUTHENTICATE';
						}

						DatabaseObj::updateActivityLog($sessionRef, 0, $userAccountArray['recordid'], $userAccountArray['login'], $resultArray['username'], 0, 'ONLINE', $activityAction, '', 1, $ipAddress);
					}
				}
			}

			// if the result is NOSSO then no single sign-on attempt was made so reset the error
			if ($result == 'NOSSO')
			{
				$result = '';
			}

			if ($result == '')
			{
				// Update the user last login date and ip
				AuthenticateObj::updateUserlastLogin($userAccountID);

				 // In the event of a successful user login, if the user is in the process of redaction and the last operation reset
				 // their redaction status, then we need to ensure that any queued events which have not yet run are completed.
				if(($userAccountArray['redactionprogress'] > TPX_REDACTION_NONE) && ($userType == TPX_LOGIN_CUSTOMER))
				{
					$evtAffectedRows = DatabaseObj::updateEventsByTargetUser($userAccountID, $evtStatusSuccess, $taskCodeDeletion);
				}
			}
			else if (($failedLoginID > 0) && ($result != 'str_ErrorIPBlockedDueToSuspiciousActivity'))
			{
				// Don't update the user if the IP is blocked.

				// The password has not matched, the limit need to be updated and the correct error returned.
				$nextValidLoginDateTemp = self::updateUserFailedLogin($failedLoginID, $loginAttemptCount);

				// Create the message.
				$getLimitResult = AuthenticateObj::getLimitErrorMessage($nextValidLoginDateTemp, $loginAttemptCount);
				$result = $getLimitResult['message'];
				$resultParam = $getLimitResult['value'];
			}

			// Rate limit IP address regardless if it was a successful login or not except when SSO is being checked and the IP isn't already blocked.
			if (((! $pIsSingleSignOnCheck)) && ($ipAddressAllowed))
			{
				if ($foundBlockedIPAddress)
				{
					// Already has a previous entry.
					self::updateBlockedIPAddress($lookupBlockedIPAddressResult['data']['id'], true);
				}
				else
				{
					// Create a new entry for this IP address.
					self::insertBlockedIPAddress($ipAddress, true);
				}
			}

			if ($result == 'str_ErrorIPBlockedDueToSuspiciousActivity')
			{
				$resultParam = abs(ceil((strtotime($nextValidLoginDate) - (strtotime(DatabaseObj::getServerTimeUTC()))) / 60));
			}
		}

        $resultArray['result'] = $result;
        $resultArray['resultparam'] = $resultParam;
        $resultArray['ref'] = $sessionRef;
        $resultArray['useraccountid'] = $userAccountID;
        $resultArray['iscustomer'] = $isCustomer;
        $resultArray['cancreateaccounts'] = $canCreateAccounts;
        $resultArray['usertype'] = $userType;
        $resultArray['isordersession'] = $pIsOrderSession;
        $resultArray['loginviasso'] = $ssoUsed;
        $resultArray['ssokey'] = $ssoKey;
        $resultArray['ssotoken'] = $ssoToken;
        $resultArray['ssoprivatedata'] = $ssoPrivateDataArray;
        $resultArray['ssoexpiredate'] = $ssoExpireData;
        $resultArray['assetservicedata'] = $assetServiceDataArray;
        $resultArray['login'] = $userAccountArray['login'];
        $resultArray['emailaddress'] = $userAccountArray['emailaddress'];
        $resultArray['accountcode'] = $userAccountArray['accountcode'] ?? '';

        return $resultArray;
    }

    static function createNewAccount($pData = array())
    {
        global $gSession;

        $createNewAccountParameters = array();
        $createNewAccountParameters['addressupdated'] = 1;
        $createNewAccountParameters['sendmarketinginfo'] = 0;
        $createNewAccountParameters['ref'] = -1;
        $createNewAccountParameters['ishighlevel'] = 0;
        $createNewAccountParameters['basketapiworkflowtype'] = TPX_BASKETWORKFLOWTYPE_NORMAL;
        $createNewAccountParameters['basketref'] = '';
        $createNewAccountParameters['sessionresult'] = '';
        $createNewAccountParameters['webbrandcode'] = '';

        $createNewAccountParameters['licensekeydata'] = array();
        $createNewAccountParameters['accountdetails'] = array();

        // Set the name of the POST variable to use as the login.
        // Default to email address.
        $createNewAccountParameters['loginparamname'] = 'email';

        $fromOnline = false;

        // if some data is passed in then this call has been invoked from online designer
        // this means the encrypted data has been decrypted
        if (count($pData) > 0)
        {
            $fromOnline = true;

            $createNewAccountParameters['addressupdated'] = UtilsObj::getArrayParam($pData, 'addressupdated', $createNewAccountParameters['addressupdated']);
            $createNewAccountParameters['sendmarketinginfo'] = UtilsObj::getArrayParam($pData, 'sendmarketinginfo', $createNewAccountParameters['sendmarketinginfo']);
            $createNewAccountParameters['ref'] = UtilsObj::getArrayParam($pData, 'ref', $createNewAccountParameters['ref']);
            $createNewAccountParameters['ishighlevel'] = UtilsObj::getArrayParam($pData, 'ishighlevel', $createNewAccountParameters['ishighlevel']);
            $createNewAccountParameters['licensekeydata'] = UtilsObj::getArrayParam($pData, 'licensekeydata', $createNewAccountParameters['licensekeydata']);
            $createNewAccountParameters['webbrandcode'] = UtilsObj::getArrayParam($pData, 'webbrandcode', $createNewAccountParameters['webbrandcode']);
            $createNewAccountParameters['basketapiworkflowtype'] = UtilsObj::getArrayParam($pData, 'basketapiworkflowtype', $createNewAccountParameters['basketapiworkflowtype']);
            $createNewAccountParameters['basketref'] = UtilsObj::getArrayParam($pData, 'basketref', $createNewAccountParameters['basketref']);

            // determin the application name and display url based on the web brand code
            $brandSettings = DatabaseObj::getBrandingFromCode($createNewAccountParameters['webbrandcode']);

            $createNewAccountParameters['applicationname'] = $brandSettings['applicationname'];
            $createNewAccountParameters['displayurl'] = $brandSettings['displayurl'];

            $createNewAccountParameters['accountdetails'] = UtilsObj::getArrayParam($pData, 'accountdetails', $createNewAccountParameters['accountdetails']);

            // Get the brand setting for registering with a username or email address.
            $createNewAccountParameters['registerusingemail'] = $brandSettings['registerusingemail'];
            $createNewAccountParameters['loginparamname'] = ($brandSettings['registerusingemail'] == TPX_REGISTER_USING_USERNAME) ? 'login' : 'email';

            // these values are not needed for online since the user will be prompted to enter them when they go to the shopping cart
            // becasue the addressupdated value is set to 2
            $createNewAccountParameters['accountdetails']['companyname'] = '';
            $createNewAccountParameters['accountdetails']['address1'] = '';
            $createNewAccountParameters['accountdetails']['address2'] = '';
            $createNewAccountParameters['accountdetails']['address3'] = '';
            $createNewAccountParameters['accountdetails']['address4'] = '';
            $createNewAccountParameters['accountdetails']['city'] = '';
            $createNewAccountParameters['accountdetails']['state'] = '';
            $createNewAccountParameters['accountdetails']['county'] = '';
            $createNewAccountParameters['accountdetails']['regioncode'] = '';
            $createNewAccountParameters['accountdetails']['region'] = '';
            $createNewAccountParameters['accountdetails']['postcode'] = '';
            $createNewAccountParameters['accountdetails']['countryname'] = '';
            $createNewAccountParameters['accountdetails']['telephonenumber'] = '';
            $createNewAccountParameters['accountdetails']['registeredtaxnumbertype'] = 0;
            $createNewAccountParameters['accountdetails']['registeredtaxnumber'] = '';
        }
        else
        {
            $createNewAccountParameters['addressupdated'] = UtilsObj::getPOSTParam('addressupdated', $createNewAccountParameters['addressupdated']);
            $createNewAccountParameters['sendmarketinginfo'] = UtilsObj::getPOSTParam('sendmarketinginfo', $createNewAccountParameters['sendmarketinginfo']);
            $createNewAccountParameters['ref'] = UtilsObj::getPOSTParam('ref', $createNewAccountParameters['ref']);
            $createNewAccountParameters['ishighlevel'] = UtilsObj::getPOSTParam('ishighlevel', $createNewAccountParameters['ishighlevel']);
            $createNewAccountParameters['webbrandcode'] = UtilsObj::getPOSTParam('webbrandcode', $createNewAccountParameters['webbrandcode']);
            $createNewAccountParameters['basketapiworkflowtype'] = UtilsObj::getPOSTParam('basketapiworkflowtype', $createNewAccountParameters['basketapiworkflowtype']);
            $createNewAccountParameters['basketref'] = UtilsObj::getPOSTParam('basketref', $createNewAccountParameters['basketref']);

            if ($createNewAccountParameters['ishighlevel'] == 1)
            {
                // determin the application name and display url by looking up the licenskey details and getting the
                // web brand code from this data

                $groupCode =  UtilsObj::getPOSTParam('groupcode', '');

                $createNewAccountParameters['licensekeydata'] = DatabaseObj::getLicenseKeyFromCode($groupCode);
                $createNewAccountParameters['webbrandcode'] = $createNewAccountParameters['licensekeydata']['webbrandcode'];

                // grab the brand settings from the web brand code
                $brandSettings = DatabaseObj::getBrandingFromCode($createNewAccountParameters['webbrandcode']);

                $createNewAccountParameters['applicationname'] = $brandSettings['applicationname'];
                $createNewAccountParameters['displayurl'] = $brandSettings['displayurl'];
            }
            else
            {
                $createNewAccountParameters['sessionresult'] = $gSession['result'];

                $createNewAccountParameters['licensekeydata'] = $gSession['licensekeydata'];
                $createNewAccountParameters['webbrandcode'] = $gSession['webbrandcode'];
                $createNewAccountParameters['applicationname'] = $gSession['webbrandapplicationname'];
                $createNewAccountParameters['displayurl'] = $gSession['webbranddisplayurl'];

                // grab the brand settings from the web brand code
                $brandSettings = DatabaseObj::getBrandingFromCode($createNewAccountParameters['webbrandcode']);
            }

            // Get the brand setting for registering with a username or email address.
            $createNewAccountParameters['registerusingemail'] = $brandSettings['registerusingemail'];
            $createNewAccountParameters['loginparamname'] = ($brandSettings['registerusingemail'] == TPX_REGISTER_USING_USERNAME) ? 'login' : 'email';

            // copy the customer details from the POST data to the accountdetails value
            $createNewAccountParameters['accountdetails']['login'] = UtilsObj::getPOSTParam('login', '');
            $createNewAccountParameters['accountdetails']['password'] = UtilsObj::getPOSTParam('password', '');
            $createNewAccountParameters['accountdetails']['format'] = UtilsObj::getPOSTParam('format', TPX_PASSWORDFORMAT_MD5);
            $createNewAccountParameters['accountdetails']['countrycode'] = UtilsObj::getPOSTParam('countrycode', '');

            // see if there are special address fields like
            // add1=add41, add42 - add43
            // meaning address1 = add41 + ", "  + add42 + " - " + add43
            // and     address4 = add41 + "<p>" + add42 + "<p>" + add43

            //include the mailing address module
            require_once('../Utils/UtilsAddress.php');

            // IMPORTANT! this function modifies the _POST variable. moving this function will break the address formatting
            UtilsAddressObj::specialAddressFields($createNewAccountParameters['accountdetails']['countrycode']);

            $createNewAccountParameters['accountdetails']['countryname'] = UtilsObj::getPOSTParam('countryname', '');
            $createNewAccountParameters['accountdetails']['contactfname'] = UtilsObj::getPOSTParam('contactfname', '');
            $createNewAccountParameters['accountdetails']['contactlname'] = UtilsObj::getPOSTParam('contactlname', '');
            $createNewAccountParameters['accountdetails']['companyname'] = UtilsObj::getPOSTParam('companyname', '');
            $createNewAccountParameters['accountdetails']['address1'] = UtilsObj::getPOSTParam('address1', '');
            $createNewAccountParameters['accountdetails']['address2'] = UtilsObj::getPOSTParam('address2', '');
            $createNewAccountParameters['accountdetails']['address3'] = UtilsObj::getPOSTParam('address3', '');
            $createNewAccountParameters['accountdetails']['address4'] = UtilsObj::getPOSTParam('address4', '');
            $createNewAccountParameters['accountdetails']['city'] = UtilsObj::getPOSTParam('city', '');
            $createNewAccountParameters['accountdetails']['state'] = UtilsObj::getPOSTParam('state', '');
            $createNewAccountParameters['accountdetails']['county'] = UtilsObj::getPOSTParam('county', '');
            $createNewAccountParameters['accountdetails']['regioncode'] = UtilsObj::getPOSTParam('regioncode', '');
            $createNewAccountParameters['accountdetails']['region'] = UtilsObj::getPOSTParam('region', '');
            $createNewAccountParameters['accountdetails']['postcode'] = UtilsObj::getPOSTParam('postcode', '');
            $createNewAccountParameters['accountdetails']['telephonenumber'] = UtilsObj::getPOSTParam('telephonenumber', '');
            $createNewAccountParameters['accountdetails']['email'] = UtilsObj::getPOSTParam('email', '');

            // registered tax number information
            $createNewAccountParameters['accountdetails']['registeredtaxnumbertype'] = UtilsObj::getPOSTParam('registeredtaxnumbertype', 0);
            $createNewAccountParameters['accountdetails']['registeredtaxnumber'] = UtilsObj::getPOSTParam('registeredtaxnumber', '');
        }

        return self::createNewAccountSuper($createNewAccountParameters, $fromOnline);
    }

    static function createNewAccountSuper($pParameters, $pFromOnlineDesigner)
    {
        global $gSession;
        global $gConstants;
		global $ac_config;

        $result = '';
        $resultParam = '';

		$userAccountArray = array();
        $userRecordID = 0;
        $additionalAccount = 0;
        $owner = '';

        $webBrandCode = $pParameters['webbrandcode'];
		$fromHigheLevelBasketRequest = $pParameters['ishighlevel'];
		$userAccountArray['reason'] = TPX_CUSTOMER_ACCOUNT_OVERRIDE_REASON_CUSTOMERREGISTER;
        $userAccountArray['addressupdated'] = $pParameters['addressupdated'];
        $basketAPIWorkFlowType = $pParameters['basketapiworkflowtype'];
        $recordID = $pParameters['ref'];
        $sessionResult = $pParameters['sessionresult'];
        $applicationName = $pParameters['applicationname'];
		$webDisplayURL = $pParameters['displayurl'];

		// variables for storing the SSO data in
		$ssoToken = "";
		$ssoPrivateDataArray = array();

        if ($recordID > 0)
        {
            $result = $sessionResult;
        }
        else
        {
            // only return session error if we didn't come here from online
            if (($pFromOnlineDesigner == 0) && ($fromHigheLevelBasketRequest == 0))
            {
                $result = 'str_ErrorNoSessionRef';
            }
        }

        // Set the login to be either the data entered into the 'login' field or the 'email' field, based on the brand setting.
        $login = UtilsObj::cleanseInput($pParameters['accountdetails'][$pParameters['loginparamname']]);

        // password - we do not cleanse input on passwords as it would strip out special characters
        $password = $pParameters['accountdetails']['password'];

		// password format
        $passwordFormat = (int) $pParameters['accountdetails']['format'];

		// calculate the password hash depending on if the page was secure or not
		$generatePasswordHashResult = self::generatePasswordHash($password, $passwordFormat);

		if ($generatePasswordHashResult['result'] == '')
		{
			$passwordHash = $generatePasswordHashResult['data'];

			$userAccountArray['countrycode'] = $pParameters['accountdetails']['countrycode'];

			// only if online designer cleans the input.
			if ($pFromOnlineDesigner == 1)
			{
				$userAccountArray['countrycode'] = UtilsObj::cleanseInput($userAccountArray['countrycode']);
			}

			// contact first name
			$userAccountArray['contactfirstname'] = UtilsObj::cleanseInput($pParameters['accountdetails']['contactfname']);

			// contact last name
			$userAccountArray['contactlastname'] = UtilsObj::cleanseInput($pParameters['accountdetails']['contactlname']);

			// company name
			$userAccountArray['companyname'] = UtilsObj::cleanseInput($pParameters['accountdetails']['companyname']);

			// address line 1
			$userAccountArray['address1'] = UtilsObj::cleanseInput($pParameters['accountdetails']['address1']);

			// address line 2
			$userAccountArray['address2'] = UtilsObj::cleanseInput($pParameters['accountdetails']['address2']);

			// address line 3
			$userAccountArray['address3'] = UtilsObj::cleanseInput($pParameters['accountdetails']['address3']);

			// address line 4
			$userAccountArray['address4'] = UtilsObj::cleanseInput($pParameters['accountdetails']['address4']);

			// we need to check to see if the string contains @@TAOPIXTAG@@. If it does then this means that it is a special address field.
			// we then need to convert @@TAOPIXTAG@@ back to a <p> so that it can be stored correctly in the database.
			$userAccountArray['address4'] = implode('<p>', mb_split('@@TAOPIXTAG@@', $userAccountArray['address4']));

			// city
			$userAccountArray['city'] = UtilsObj::cleanseInput($pParameters['accountdetails']['city']);

			// state
			$userAccountArray['state'] = UtilsObj::cleanseInput($pParameters['accountdetails']['state']);

			// county
			$userAccountArray['county'] = UtilsObj::cleanseInput($pParameters['accountdetails']['county']);

			// region code
			$userAccountArray['regioncode'] = UtilsObj::cleanseInput($pParameters['accountdetails']['regioncode']);

			// region
			$userAccountArray['region'] = UtilsObj::cleanseInput($pParameters['accountdetails']['region']);

			// postcode
			$userAccountArray['postcode'] = UtilsObj::cleanseInput($pParameters['accountdetails']['postcode']);

			// country name
			$userAccountArray['countryname'] = UtilsObj::cleanseInput($pParameters['accountdetails']['countryname']);

			// telephonenumber
			$userAccountArray['telephonenumber'] = UtilsObj::cleanseInput($pParameters['accountdetails']['telephonenumber']);

			// email
			$userAccountArray['emailaddress'] = UtilsObj::cleanseInput($pParameters['accountdetails']['email']);

			// marketing information
			$userAccountArray['sendmarketinginfo'] = UtilsObj::cleanseInput($pParameters['sendmarketinginfo']);

			// registered tax number information
			$userAccountArray['registeredtaxnumbertype'] = UtilsObj::cleanseInput($pParameters['accountdetails']['registeredtaxnumbertype']);

			$userAccountArray['registeredtaxnumber'] = UtilsObj::cleanseInput($pParameters['accountdetails']['registeredtaxnumber']);

			$isCustomer = 1;
			$userType = TPX_LOGIN_CUSTOMER;
			$userAccountArray['isactive'] = 1;

			if ($pParameters['ref'] > 0)
			{
				$groupCode = $pParameters['licensekeydata']['groupcode'];
			}
			else
			{
				if (($pFromOnlineDesigner == 1) || ($fromHigheLevelBasketRequest == 1))
				{
					$groupCode = $pParameters['licensekeydata']['groupcode'];
				}
				else
				{
					$groupCode = '';
				}
			}
		}
		else
		{
			$result = $generatePasswordHashResult['result'];
			$resultParam = $generatePasswordHashResult['resultparam'];
        }

        // Check that the login can be used, otherwise an account with a temp user name needs to be created.
        // An error of 'str_ErrorNoAccount' means no account has been found and registration can continue as normal.
        $uniqueLoginCheck = DatabaseObj::getUserAccountFromBrandAndLogin($webBrandCode, $login);

        if ('' == $uniqueLoginCheck['result'])
        {
            // If the brand is set to use email addresses to login, generate a temp user name, unless the hidden config is set to 1.
            if (($pParameters['registerusingemail'] == TPX_REGISTER_USING_EMAIL) && ((int) UtilsObj::getArrayParam($ac_config, 'DISABLETEMPUSERCREATION', 0) === 0))
            {
                // No error means an account has been found, a temp account needs to be generated.
                // Flag that an additional account is going to be created.
                $additionalAccount = 1;

                // Prevent potential infinite loops by limiting the number of generation attempts.
                $generateAttempts = 10;

                // Break when an account has been created.
                $canCreateTempAccount = false;

                // An account has been found which uses the login, a temp login needs to be created.
                while (($generateAttempts > 0) && (! $canCreateTempAccount))
                {
                    $generateAttempts--;

                    // Generate a new user name, based on unique string.
                    $login = 'tempuser' . substr(uniqid(), -6);

                    // Check that the new login can be used, otherwise carry out the regeneration again.
                    $uniqueLoginCheck = DatabaseObj::getUserAccountFromBrandAndLogin($webBrandCode, $login);

                    if ('str_ErrorNoAccount' == $uniqueLoginCheck['result'])
                    {
                        // No account exists, so new temp user name can be used.
                        $canCreateTempAccount = true;
                    }
                    else if ('' != $uniqueLoginCheck['result'])
                    {
                        // An error other than no account has been returned, the process can not continue.
                        $result = $uniqueLoginCheck['result'];
                        $resultParam = $uniqueLoginCheck['resultparam'];

                        $generateAttempts = -1;
                    }
                }
            }
            else
            {
                // An account with the username was found, the selected username can not be used.
                $result = 'str_ErrorAccountExists';
            }
        }
        else if ('str_ErrorNoAccount' != $uniqueLoginCheck['result'])
        {
            // An error other than no account has been returned, the process can not continue.
            $result = $uniqueLoginCheck['result'];
            $resultParam = $uniqueLoginCheck['resultparam'];
        }

		// if no error has occurred attempt to insert the customer record
		if ($result == '')
		{
            // Check for accounts using the original email address, get the user names, skipping the validity check as the user is registering.
            $userEmailAccountArray = DatabaseObj::getValidUserAccountsForEmailAndBrand($webBrandCode, $userAccountArray['emailaddress'], '', '');

			$licenseKeyArray = DatabaseObj::getLicenseKeyFromCode($groupCode);

			$brandingDefaults = DatabaseObj::getBrandingFromCode('');
			$userAccountArray['usedefaultcurrency'] = 1;
			$userAccountArray['currencycode'] = $gConstants['defaultcurrencycode'];
			$userAccountArray['usedefaultpaymentmethods'] = 1;
			$userAccountArray['paymentmethods'] = $brandingDefaults['paymentmethods'];
			$userAccountArray['creditlimit'] = $gConstants['defaultcreditlimit'];
			$userAccountArray['useremaildestination'] = $licenseKeyArray['useremaildestination'];
			$userAccountArray['defaultaddresscontrol'] = 1;
			$userAccountArray['taxcode'] = '';
			$userAccountArray['shippingtaxcode'] = '';
			$userAccountArray['canmodifypassword'] = 1;
			$userAccountArray['accountbalance'] = 0.00;
			$userAccountArray['giftcardbalance'] = 0.00;

			if ($webBrandCode != '')
			{
				$brandingArray = DatabaseObj::getBrandingFromCode($webBrandCode);
				$companyCode = $brandingArray['companycode'];
			}
			else
			{
				$companyCode = $licenseKeyArray['companyCode'];
			}

			$userAccountArray['uselicensekeyforshippingaddress'] = $licenseKeyArray['useaddressforshipping'];
			if ($userAccountArray['uselicensekeyforshippingaddress'] == 1)
			{
				$userAccountArray['canmodifyshippingaddress'] = 0;
				$userAccountArray['canmodifyshippingcontactdetails'] = $licenseKeyArray['canmodifyshippingcontactdetails'];
			}
			else
			{
				$userAccountArray['canmodifyshippingaddress'] = $licenseKeyArray['canmodifyshippingaddress'];
				$userAccountArray['canmodifyshippingcontactdetails'] = $licenseKeyArray['canmodifyshippingcontactdetails'];
			}

			$userAccountArray['uselicensekeyforbillingaddress'] = $licenseKeyArray['useaddressforbilling'];
			if ($userAccountArray['uselicensekeyforbillingaddress'] == 1)
			{
				$userAccountArray['canmodifybillingaddress'] = 0;
			}
			else
			{
				$userAccountArray['canmodifybillingaddress'] = $licenseKeyArray['canmodifybillingaddress'];
			}

			// check to see if the Taopix Customer Account API script is present.
			if (($gConstants['optionwscrp']) && (file_exists("../Customise/scripts/EDL_TaopixCustomerAccountAPI.php")))
			{
				require_once('../Customise/scripts/EDL_TaopixCustomerAccountAPI.php');

				// If the customer account override function exists pass account details to the external script
				if (method_exists('CustomerAccountAPI', 'customerAccountOverride'))
				{
					$userAccountArray['groupcode'] = $groupCode;
					$userAccountArray['brandcode'] = $webBrandCode;

					$userAccountArray = CustomerAccountAPI::customerAccountOverride($userAccountArray);
				}
			}

			$dbObj = DatabaseObj::getGlobalDBConnection();
			if ($dbObj)
			{
				// start a transaction so that the update can be rolled back if any external scripts fail
				$dbObj->query('START TRANSACTION');

                $sql = 'INSERT INTO USERS (`id`, `datecreated`,`companycode`, `webbrandcode`, `owner`, `login`, `password`, `customer`, `usertype`,
                        `groupcode`, `companyname`, `address1`, `address2`, `address3`, `address4`, `city`, `county`, `state`, `regioncode`, `region`, `addressupdated`,
                        `postcode`, `countrycode`, `countryname`, `telephonenumber`, `emailaddress`, `contactfirstname`, `contactlastname`, `usedefaultcurrency`,
                        `currencycode`, `usedefaultpaymentmethods`, `paymentmethods`, `registeredtaxnumbertype`, `registeredtaxnumber`, `uselicensekeyforshippingaddress`,
                        `modifyshippingaddress`, `modifyshippingcontactdetails`, `uselicensekeyforbillingaddress`, `modifybillingaddress`, `modifypassword`, `creditlimit`,
                        `accountbalance`, `giftcardbalance`, `sendmarketinginfo`, `active`, `useremaildestination`, `defaultaddresscontrol`, `taxcode`, `shippingtaxcode`';

                // only update the opt in date if the setting is on
                if ($userAccountArray['sendmarketinginfo'] == 1)
                {
                    $sql .= ', `sendmarketinginfooptindate`';
                }

                $sql .= ') VALUES (0, now(), ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?';

                // only update the opt in date if the setting is on
                if ($userAccountArray['sendmarketinginfo'] == 1)
                {
                    $sql .= ', now()';
                }

                $sql .= ')';

				if ($stmt = $dbObj->prepare($sql))
				{
					if ($stmt->bind_param('sssssiissss' . 'sssssssissss' . 'sssisis' . 'isiii' . 'iiidddiiiiss',
							$companyCode, $webBrandCode, $owner, $login, $passwordHash, $isCustomer, $userType, $groupCode, $userAccountArray['companyname'], $userAccountArray['address1'], $userAccountArray['address2'],
							$userAccountArray['address3'], $userAccountArray['address4'], $userAccountArray['city'], $userAccountArray['county'], $userAccountArray['state'], $userAccountArray['regioncode'],
							$userAccountArray['region'], $userAccountArray['addressupdated'], $userAccountArray['postcode'], $userAccountArray['countrycode'], $userAccountArray['countryname'], $userAccountArray['telephonenumber'],
							$userAccountArray['emailaddress'], $userAccountArray['contactfirstname'], $userAccountArray['contactlastname'], $userAccountArray['usedefaultcurrency'], $userAccountArray['currencycode'], $userAccountArray['usedefaultpaymentmethods'],
							$userAccountArray['paymentmethods'], $userAccountArray['registeredtaxnumbertype'], $userAccountArray['registeredtaxnumber'], $userAccountArray['uselicensekeyforshippingaddress'], $userAccountArray['canmodifyshippingaddress'], $userAccountArray['canmodifyshippingcontactdetails'],
							$userAccountArray['uselicensekeyforbillingaddress'], $userAccountArray['canmodifybillingaddress'], $userAccountArray['canmodifypassword'],
							$userAccountArray['creditlimit'], $userAccountArray['accountbalance'], $userAccountArray['giftcardbalance'], $userAccountArray['sendmarketinginfo'], $userAccountArray['isactive'], $userAccountArray['useremaildestination'],
							$userAccountArray['defaultaddresscontrol'], $userAccountArray['taxcode'], $userAccountArray['shippingtaxcode']))
					{
						if ($stmt->execute())
						{
							$userRecordID = $dbObj->insert_id;

                            DatabaseObj::updateActivityLog($recordID, 0, $userRecordID, $login, $login, 0, 'CUSTOMER', 'UPDATEPREFERENCES', $userAccountArray['sendmarketinginfo'] . " " . $userRecordID, 1);

							// the sql statement succeeded so pass the account details to the external script
							if (($gConstants['optionwscrp']) && (file_exists('../Customise/scripts/EDL_ExternalCustomerAccount.php')))
							{
								require_once('../Customise/scripts/EDL_ExternalCustomerAccount.php');

								if (method_exists('ExternalCustomerAccountObj', 'createAccount'))
								{
									unset($userAccountArray['reason']);

									// create the user account via the external script
									$paramArray = Array();
									$paramArray['languagecode'] = UtilsObj::getBrowserLocale();
									$paramArray['isadmin'] = 0;
									$paramArray['groupcode'] = $groupCode;
									$paramArray['brandcode'] = $webBrandCode;
									$paramArray['login'] = $login;
									$paramArray['accountcode'] = '';

									// pass the plaintext/md5 password, it's up to the script to verify the password
									$paramArray['passwordformat'] = $passwordFormat;
									$paramArray['password'] = $password;
									$paramArray['status'] = $userAccountArray['addressupdated'];
									$paramArray['useraccount'] = $userAccountArray;
									$paramArray['ssotoken'] = '';
									$paramArray['ssoprivatedata'] = array();

									$externalLoginResultArray = ExternalCustomerAccountObj::createAccount($paramArray);
									$result = $externalLoginResultArray['result'];
									$newAccountCode = $externalLoginResultArray['accountcode'];

									// if the sso data has been returned assign it to the local var which will get returned from this function
									if (array_key_exists('ssotoken', $externalLoginResultArray))
									{
										$ssoToken = $externalLoginResultArray['ssotoken'];
									}

									if (array_key_exists('ssoprivatedata', $externalLoginResultArray))
									{
										$ssoPrivateDataArray = $externalLoginResultArray['ssoprivatedata'];
									}

									// if the script has returned no error and has supplied a new account code update the record now
									if (($result == '') && ($newAccountCode != ''))
									{
										if ($stmt2 = $dbObj->prepare('UPDATE `USERS` SET `accountcode` = ? WHERE `id` = ?'))
										{
											if ($stmt2->bind_param('si', $newAccountCode, $userRecordID))
											{
												if (! $stmt2->execute())
												{
													$result = 'str_DatabaseError';
													$resultParam = 'createnewaccount update account code execute ' . $dbObj->error;
												}
											}
											else
											{
												// could not bind parameters
												$result = 'str_DatabaseError';
												$resultParam = 'createnewaccount update account code bind ' . $dbObj->error;
											}

											$stmt2->free_result();
											$stmt2->close();
											$stmt2 = null;
										}
										else
										{
											$result = 'str_DatabaseError';
											$resultParam = 'createnewaccount update account code prepare ' . $dbObj->error;
										}
									}
								}
							}
						}
						else
						{
							// could not execute statement
							// first check for a duplicate key (user account)
							if ($stmt->errno == 1062)
							{
								$result = 'str_ErrorAccountExists';
							}
							else
							{
								$result = 'str_DatabaseError';
								$resultParam = 'createnewaccount execute ' . $dbObj->error;
							}
						}
					}
					else
					{
						// could not bind parameters
						$result = 'str_DatabaseError';
						$resultParam = 'createnewaccount bind ' . $dbObj->error;
					}

					$stmt->free_result();
					$stmt->close();
					$stmt = null;
				}
				else
				{
					// could not prepare statement
					$result = 'str_DatabaseError';
					$resultParam = 'createnewaccount prepare ' . $dbObj->error;
				}

				// if no errors have occurred commit the transaction otherwise roll it back
				if ($result == '')
				{
					$dbObj->query('COMMIT');
				}
				else
				{
					$dbObj->query('ROLLBACK');
				}

				$dbObj->close();
			}
			else
			{
				// could not open database connection
				$result = 'str_DatabaseError';
				$resultParam = 'createnewaccount connect ' . $dbObj->error;
			}
        }
        else
        {
            // A user with matching details was found, get the companycode required for the return data.
            if ($webBrandCode != '')
            {
                $brandingArray = DatabaseObj::getBrandingFromCode($webBrandCode);
                $companyCode = $brandingArray['companycode'];
            }
            else
            {
                $licenseKeyArray = DatabaseObj::getLicenseKeyFromCode($groupCode);
                $companyCode = $licenseKeyArray['companyCode'];
            }
        }

        // if no error has occurred we have created an account so start the web session, update the activity log and send the user a welcome email
        if ($result == '')
        {
            // extract the user which has just been added from the database
            $userAccountArray = DatabaseObj::getUserAccountFromID($userRecordID);

            if ($pFromOnlineDesigner == 0)
            {
                $recordID = DatabaseObj::startSession($userAccountArray['recordid'], $userAccountArray['login'],
                                $userAccountArray['contactfirstname'] . ' ' . $userAccountArray['contactlastname'], TPX_LOGIN_CUSTOMER, $userAccountArray['companycode'],
                                $userAccountArray['owner'], $userAccountArray['webbrandcode'], $userAccountArray['groupcode'], '', Array());

                DatabaseObj::updateActivityLog($recordID, 0, $userAccountArray['recordid'], $login, $userAccountArray['contactfirstname'] . ' ' . $userAccountArray['contactlastname'], 0, 'ORDER', 'CUSTOMERNEWACCOUNT', '', 1);
            }
            else
            {
                if ($basketAPIWorkFlowType == TPX_BASKETWORKFLOWTYPE_HIGHLEVELAPI)
                {
                    require_once('../OnlineAPI/OnlineAPI_model.php');

					$basketRef = $pParameters['basketref'];

					// We need to create a place holder record in the online basket table so we know a user has logged in from online
                    $createBasketRecordResult = OnlineAPI_model::createBasketRecord($userAccountArray['webbrandcode'], $userAccountArray['groupcode'], $basketRef);
					$basketRecordID = $createBasketRecordResult['basketrecordid'];

					// we then need to update the place holder record with the userid.
					$updateBasketRefTokenResult = OnlineAPI_model::updateBasketRecordBasketRefAndToken($basketRecordID, '', $basketRef, $userRecordID, $userAccountArray['webbrandcode'], $userAccountArray['groupcode']);

                    // Update userid the projects in the cart are assigned to.
                    OnlineAPI_model::updateUserIDBasketRefForProjectsInBasket($userRecordID, $basketRef);

                    $recordID = DatabaseObj::startSession($userAccountArray['recordid'], $userAccountArray['login'], $userAccountArray['contactfirstname'] . ' ' . $userAccountArray['contactlastname'], 100, $userAccountArray['companycode'], $userAccountArray['owner'], $userAccountArray['webbrandcode'],
                        '', '', array());
                    $updateSessionResult = DatabaseObj::linkOnlineBasketToSession($recordID, $basketRef, 0);

                    DatabaseObj::updateSession();

                }

                DatabaseObj::updateActivityLog(0, 0, $userAccountArray['recordid'], $login, $userAccountArray['contactfirstname'] . ' ' . $userAccountArray['contactlastname'], 0, 'ORDER', 'CUSTOMERNEWACCOUNT', '', 1);
            }


            // check if brand allows this email to be sent
            if (($webBrandCode != '') && ($brandingArray['usedefaultemailsettings'] == 0))
            {
                $sendNotification = $brandingArray['smtpnewaccountactive'];
                $sBccName = $brandingArray['smtpnewaccountname'];
                $sBccAddress = $brandingArray['smtpnewaccountaddress'];
            }
            else
            {
                $sendNotification = $brandingDefaults['smtpnewaccountactive'];
                $sBccName = $brandingDefaults['smtpnewaccountname'];
                $sBccAddress = $brandingDefaults['smtpnewaccountaddress'];
            }

            if (($pFromOnlineDesigner == 0) && ($fromHigheLevelBasketRequest == 0))
            {
                // if this is an offline order that was not completed by the customer then we do not send an email notification to the user
                if (($gSession['order']['isofflineorder'] == 1) && ($gSession['order']['isofflineordercompletedbycustomer'] == 0))
                {
                    $sendNotification = false;
                }
            }

            if ($sendNotification == true)
            {
                // include the email creation module
                require_once('../Utils/UtilsEmail.php');

                $emailTemplate = 'customer_newaccount';
                $emailParamArray = array(
                    "userid" => $userAccountArray['recordid'],
                    "loginname" => $login,
                    "targetuserid" => $userAccountArray['recordid']
				);

                if (0 < $userEmailAccountArray['count'])
                {
                    $emailTemplate .= 'includingtempuser';
                    $emailParamArray['additionalaccount'] = $additionalAccount;
                    $emailParamArray['usernames'] = $userEmailAccountArray['accounts'];
                }

                $emailObj = new TaopixMailer();
                $emailObj->sendTemplateEmail(
                        $emailTemplate,
                        $webBrandCode,
                        $applicationName,
                        $webDisplayURL,
                        '',
                        $userAccountArray['contactfirstname'] . ' ' . $userAccountArray['contactlastname'],
                        $userAccountArray['emailaddress'],
                        $sBccName,
                        $sBccAddress,
                        $userAccountArray['recordid'],
                        $emailParamArray,
                        '',
                        ''
                );
            }

            // include the data export module
            require_once('../Utils/UtilsDataExport.php');

            DataExportObj::EventTrigger(TPX_TRIGGER_CUSTOMER_ADD, 'CUSTOMER', $userAccountArray['recordid'], 0);
        }

        $resultArray['result'] = $result;
        $resultArray['resultparam'] = $resultParam;
        $resultArray['ref'] = $recordID;
        $resultArray['companycode'] = $companyCode;
        $resultArray['groupcode'] = $groupCode;
        $resultArray['webbrandcode'] = $webBrandCode;
        $resultArray['owner'] = $owner;
        $resultArray['login'] = $login;
        $resultArray['password'] = $password;
        $resultArray['useraccountid'] = $userRecordID;
        $resultArray['username'] = $userAccountArray['contactfirstname'] . ' ' . $userAccountArray['contactlastname'];
        $resultArray['contactfname'] = $userAccountArray['contactfirstname'];
        $resultArray['contactlname'] = $userAccountArray['contactlastname'];
        $resultArray['companyname'] = $userAccountArray['companyname'];
        $resultArray['address1'] = $userAccountArray['address1'];
        $resultArray['address2'] = $userAccountArray['address2'];
        $resultArray['address3'] = $userAccountArray['address3'];
        $resultArray['address4'] = $userAccountArray['address4'];
        $resultArray['city'] = $userAccountArray['city'];
        $resultArray['state'] = $userAccountArray['state'];
        $resultArray['county'] = $userAccountArray['county'];
        $resultArray['regioncode'] = $userAccountArray['regioncode'];
        $resultArray['region'] = $userAccountArray['region'];
        $resultArray['postcode'] = $userAccountArray['postcode'];
        $resultArray['countrycode'] = $userAccountArray['countrycode'];
        $resultArray['countryname'] = $userAccountArray['countryname'];
        $resultArray['telephonenumber'] = $userAccountArray['telephonenumber'];
        $resultArray['emailaddress'] = $userAccountArray['emailaddress'];
        $resultArray['sendmarketinginfo'] = $userAccountArray['sendmarketinginfo'];
        $resultArray['cancreateaccounts'] = 1;
		$resultArray['ishighlevel'] = $fromHigheLevelBasketRequest;
		$resultArray['ssotoken'] = $ssoToken;
		$resultArray['ssoprivatedata'] = $ssoPrivateDataArray;
        $resultArray['registerusingemail'] = $pParameters['registerusingemail'];
        $resultArray['accountcode'] = $userAccountArray['accountcode'] ?? '';

        return $resultArray;
    }

    static function resetPasswordRequest($pRecordID, $pWebBrandCode, $pLogin, $pPasswordFormat, $pPasswordResetFormTokenValue, $pReturnInformation)
    {
        global $ac_config;
        global $gConstants;
        global $gSession;

        $resultArray = Array();
        $result = 'str_ErrorNoAccount';
        $resultParam = '';

        $isOrderSession = 0;
        $groupCode = '';
        $password = '';
        $sendNotification = false;
        $canCreateAccounts = 0;

        $userID = 0;
        $isCustomer = 0;
        $accountGroupCode = 0;
        $accountCode = '';
        $emailAddress = '';
        $contactFirstName = '';
        $contactLastName = '';
		$canModifyPassword = 0;
        $canResetPassword = false;
		$passwordResetAuthCode = 0;
		$authTokenStatus = 0;
		$tokenData = '';

		$nextValidLoginDate = '0000-00-00 00:00:00';
		$loginAttemptCount = 0;
        $userActive = 0;
        $redirectURL = '';
        $useExternalSystemProcess = false;
        $returnInformation = '';

        if ($pRecordID > 0)
        {
            if ($gSession['ref'] > 0)
            {
                $isOrderSession = $gSession['isordersession'];

                // if we have an order session grab the group code from the session
                if ($isOrderSession)
                {
                    $groupCode = $gSession['licensekeydata']['groupcode'];
                    $licenseKeyArray = DatabaseObj::getLicenseKeyFromCode($groupCode);
                    $canCreateAccounts = $licenseKeyArray['cancreateaccounts'];
                    $returnInformation = UtilsObj::getCurrentURL();
                }
            }
        }

        if (($pLogin != '') && ($ac_config['RESETPASSWORDENABLED'] == 1))
        {
            $determineIfUserCanResetPasswordResult = self::determineIfUserCanResetPassword($pRecordID, $pWebBrandCode, $pLogin);

            $result = $determineIfUserCanResetPasswordResult['result'];

			// if we have a match check to see if this is a customer account or an admin account
			if ($result == '')
			{
				// if return information has been set by a previous password reset attempt use it
				// do not overwrite the return information it has already been set by the function
				if (($pReturnInformation !== '') && ($returnInformation === ''))
				{
					$returnInformation = $pReturnInformation;
				}

				$userID = $determineIfUserCanResetPasswordResult['userid'];
            	$userDateCreated = $determineIfUserCanResetPasswordResult['userdatecreated'];
            	$isCustomer = $determineIfUserCanResetPasswordResult['iscustomer'];
            	$accountGroupCode = $determineIfUserCanResetPasswordResult['accountgroupcode'];
            	$accountCode = $determineIfUserCanResetPasswordResult['accountcode'];
            	$emailAddress = $determineIfUserCanResetPasswordResult['emailaddress'];
            	$contactFirstName = $determineIfUserCanResetPasswordResult['contactfirstname'];
            	$contactLastName = $determineIfUserCanResetPasswordResult['contactlastname'];
            	$userActive = $determineIfUserCanResetPasswordResult['useractive'];
            	$canResetPassword = $determineIfUserCanResetPasswordResult['canresetpassword'];
            	$canCreateAccounts = $determineIfUserCanResetPasswordResult['cancreateaccounts'];
            	$canCheckLogin = $determineIfUserCanResetPasswordResult['canchecklogin'];
            	$sendNotification = $determineIfUserCanResetPasswordResult['sendnotification'];

				$licenseKeyArray = DatabaseObj::getLicenseKeyFromCode($accountGroupCode);
				$canCreateAccounts = $licenseKeyArray['cancreateaccounts'];

				// if we can still continue attempt to reset the password via an external script
                if (($canCheckLogin) && ($gConstants['optionwscrp']))
                {
                    if (file_exists('../Customise/scripts/EDL_ExternalCustomerAccount.php'))
                    {
                        require_once('../Customise/scripts/EDL_ExternalCustomerAccount.php');

                        if (method_exists('ExternalCustomerAccountObj', 'resetPasswordInit'))
                        {
                            $paramArray = Array();
                            $paramArray['languagecode'] = UtilsObj::getBrowserLocale();
                            $paramArray['designergroupcode'] = $groupCode;
                            $paramArray['accountgroupcode'] = $accountGroupCode;
                            $paramArray['brandcode'] = $pWebBrandCode;
                            $paramArray['id'] = $userID;
                            $paramArray['login'] = $pLogin;
                            $paramArray['accountcode'] = $accountCode;
                            $paramArray['emailaddress'] = $emailAddress;
                            $paramArray['isordersession'] = $isOrderSession;

                            $externalResponse = ExternalCustomerAccountObj::resetPasswordInit($paramArray);
                            $scriptResult = $externalResponse['result'];

							/*
                            if the external script has not returned an error we need to re-enable the canresetpassword flag as the external script overrides what is in the user account
                            */
                            if ($scriptResult == '')
                            {
                                $canResetPassword = true;

                                $result = '';
                            }
                            else if ($scriptResult == 'REDIRECT')
                            {
                                /*
									if external script returns REDIRECT we must not send the email. If a redirect URL has been provided
									we will redirect to this URL. If the URL is empty then we will show the the TAOPIX reset password
									confirmation page with the authcode disabled.
                            	*/

                                $canResetPassword = true;
								$useExternalSystemProcess = true;
								$redirectURL = $externalResponse['redirecturl'];

                        		$result = '';

                            }
                            else if ($scriptResult == 'NOTHANDLED')
                            {
                                // the script hasn't handled the reset so just continue with the current result
                            }
                            else
                            {
                                $result = $scriptResult;
                            }
                        }
                    }
                }

				if ($canResetPassword)
				{
					if ($result == 'str_ErrorNoAccount')
					{
						// we couldn't match the account so log it
						DatabaseObj::updateActivityLog(0, 0, 0, $pLogin, $contactFirstName . ' ' . $contactLastName, 0, 'CUSTOMER', 'RESETPASSWORDREQUEST', 'str_ErrorNoAccount', 0);
					}

					// if we have no error then we need to send the confirmation email and data export trigger
					if (($result == '') && (! $useExternalSystemProcess))
					{
						// check if brand allows sending this type of emails
						$brandSettings = DatabaseObj::getBrandingFromCode($pWebBrandCode);

						// copy the brand settings. this array will be used for email settings
						$emailBrandSettings = $brandSettings;

						// if there is a brand and we are using the default brand email settings get the default brand settings from the database
						// and assign then to the emailBrandSettings variable.
						if (($brandSettings['usedefaultemailsettings'] == 1) && ($pWebBrandCode != ''))
						{
							$emailBrandSettings = DatabaseObj::getBrandingFromCode('');
						}

						// only allow the password to be reset if the email brand settings allow this.
						if ($emailBrandSettings['smtpresetpasswordactive'] == 0)
						{
							$sendNotification = false;
						}

						if ($sendNotification == true)
						{
							// create password reset token record
							$tokenResultArray = self::createResetPasswordRecord($userID, $userDateCreated, $returnInformation);

							if ($tokenResultArray['result'] == '')
							{
								$token = $tokenResultArray['token'];
								$passwordResetAuthCode = $tokenResultArray['resetpasswordauthcode'];

								$resetURL = $brandSettings['displayurl'];

								// build the brand URL based on default brand if displayurl is empty.
								$defaults = DatabaseObj::getBrandingFromCode('');
								if ($resetURL == '')
								{
									$resetURL = UtilsObj::correctPath($defaults['displayurl']) . ($ac_config['WEBBRANDFOLDERNAME'] == '' ? 'Branding' : $ac_config['WEBBRANDFOLDERNAME']) . '/' . $brandSettings['name'] . '/';
								}

								$resetURL .= '?fsaction=Welcome.resetPassword&td=' . $token;

								// include the email creation module
								require_once('../Utils/UtilsEmail.php');

								$emailObj = new TaopixMailer();
								$emailObj->sendTemplateEmail('customer_passwordreset', $pWebBrandCode, $brandSettings['applicationname'],
													$brandSettings['displayurl'], '', $contactFirstName . ' ' . $contactLastName, $emailAddress, '', '', $userID,
													Array("userid" => $userID,
														"loginname" => $pLogin,
														"targetuserid" => $userID,
														"reseturl" => $resetURL),
													'', ''
												);

								DatabaseObj::updateActivityLog(0, 0, $userID, $pLogin, $contactFirstName . ' ' . $contactLastName, 0, 'CUSTOMER', 'RESETPASSWORDREQUEST', $pPasswordResetFormTokenValue, 1);
							}
							else
							{
								DatabaseObj::updateActivityLog(0, 0, 0, $pLogin, '', 0, 'CUSTOMER', 'RESETPASSWORDREQUEST', $tokenResultArray['result'], 0);
							}
						}
					}
					else
					{
						if ($useExternalSystemProcess)
						{
							// the external system is dealing with the reset password request so we must log it.
							DatabaseObj::updateActivityLog(0, 0, 0, $pLogin, $contactFirstName . ' ' . $contactLastName, 0, 'CUSTOMER', 'RESETPASSWORDREQUEST', 'REDIRECT', 1);
						}
						else
						{
							DatabaseObj::updateActivityLog(0, 0, 0, $pLogin, '', 0, 'CUSTOMER', 'RESETPASSWORDREQUEST', $result, 0);
						}
					}
				}
				else
				{
					// cannot modify account
					DatabaseObj::updateActivityLog(0, 0, 0, $pLogin, $contactFirstName . ' ' . $contactLastName, 0, 'CUSTOMER', 'RESETPASSWORDREQUEST', 'str_ErrorCannotChangePassword', 0);

					$result = 'str_ErrorCannotChangePassword';

					// check if brand allows sending this type of emails
					$brandSettings = DatabaseObj::getBrandingFromCode($pWebBrandCode);

					// copy the brand settings. this array will be used for email settings
					$emailBrandSettings = $brandSettings;

					// if there is a brand and we are using the default brand email settings get the default brand settings from the database
					// and assign then to the emailBrandSettings variable.
					if (($brandSettings['usedefaultemailsettings'] == 1) && ($pWebBrandCode != ''))
					{
						$emailBrandSettings = DatabaseObj::getBrandingFromCode('');
					}

					// only allow the password to be reset if the email brand settings allow this.
					if ($emailBrandSettings['smtpresetpasswordactive'] == 0)
					{
						$sendNotification = false;
					}

					if ($sendNotification == true)
					{
						// include the email creation module
						require_once('../Utils/UtilsEmail.php');

						// Send email to user telling them to contact customer support to change their password
						$emailObj = new TaopixMailer();
						$emailObj->sendTemplateEmail('customer_contactsupportforpasswordreset', $pWebBrandCode, $brandSettings['applicationname'],
											$brandSettings['displayurl'], '', $contactFirstName . ' ' . $contactLastName, $emailAddress, '', '', $userID,
											Array("userid" => $userID,
												"loginname" => $pLogin,
												"targetuserid" => $userID),
											'', ''
										);
					}
				}
			}
			else
			{
				// we have not matched a record
				DatabaseObj::updateActivityLog(0, 0, 0, $pLogin, '', 0, 'CUSTOMER', 'RESETPASSWORDREQUEST', $result, 0);
			}
        }

        // Check for a 'str_ErrorNoAccount' result, or 'str_ErrorCannotChangePassword' result.
        if (('str_ErrorNoAccount' == $result) || ('str_ErrorCannotChangePassword' == $result))
        {
            // Failed to find a matching account, generate a fake code to display (if required), do not send the reset email.
            $result = '';
            $resultParam = '';

            // Generate a code to display, if configured.
            if (array_key_exists('RESETPASSWORDAUTHCODEENABLED', $ac_config))
            {
                if ((int) $ac_config['RESETPASSWORDAUTHCODEENABLED'] == 1)
                {
                    $passwordResetAuthCode = substr(mt_rand(), 0, 6);
                }
            }
        }

        $resultArray['result'] = $result;
        $resultArray['resultparam'] = $resultParam;
        $resultArray['ref'] = $pRecordID;
        $resultArray['cancreateaccounts'] = $canCreateAccounts;
        $resultArray['login'] = $pLogin;
        $resultArray['resetpasswordauthcode'] = $passwordResetAuthCode;
        $resultArray['validtoken'] = true;
        $resultArray['redirecturl'] = $redirectURL;

        return $resultArray;
    }

    /**
     * Generate the email including multiple user accounts
     *
     * @global array $ac_config
     * @global array $gConstants
     * @global array $gSession
     * @param integer $pRecordID
     * @param string $pWebBrandCode
     * @param string $pLogin
     * @param integer $pPasswordFormat
     * @param string $pPasswordResetFormTokenValue
     * @param array $pUserAccountArray
     *
     * @return array
     */
    static function resetPasswordRequestMultipleAccounts($pRecordID, $pWebBrandCode, $pLogin, $pPasswordFormat, $pPasswordResetFormTokenValue = '', $pUserAccountArray = array(), $pReturnInformation = '')
    {
        global $ac_config;
        global $gConstants;
        global $gSession;

        $resultArray = Array();
        $result = 'str_ErrorResetPasswordMultipleAccounts';
        $resultParam = '';

        $isOrderSession = 0;
        $groupCode = '';
        $canCreateAccounts = 0;
		$passwordResetAuthCode = 0;
		$redirectURL = '';
		$returnInformation = '';
		$token = '';

        if ($pRecordID > 0)
        {
            if ($gSession['ref'] > 0)
            {
                $isOrderSession = $gSession['isordersession'];

                // if we have an order session grab the group code from the session
                if ($isOrderSession)
                {
                    $groupCode = $gSession['licensekeydata']['groupcode'];
                    $licenseKeyArray = DatabaseObj::getLicenseKeyFromCode($groupCode);
					$canCreateAccounts = $licenseKeyArray['cancreateaccounts'];
					// it is possible that the user will wrongly enter the email address twice, in this case we want to use the pre-existing return information
					$returnInformation = ($pReturnInformation === '' ? UtilsObj::getCurrentURL() : $pReturnInformation);
					//we only need to create a record if we have return information to store
					$tokenArray = self::createMultipleAccountResetPasswordRecord($pUserAccountArray[0]['id'], $pUserAccountArray[0]['lastlogindate'], $returnInformation);
					$token = $tokenArray['token'];
                }
            }
        }

        // Get the brand settings to generate the url for the reset button.
        $brandSettings = DatabaseObj::getBrandingFromCode($pWebBrandCode);

        $resetURL = $brandSettings['displayurl'];

        // build the brand URL based on default brand if displayurl is empty.
        $defaults = DatabaseObj::getBrandingFromCode('');
        if ($resetURL == '')
        {
            $resetURL = UtilsObj::correctPath($defaults['displayurl']) . ($ac_config['WEBBRANDFOLDERNAME'] == '' ? 'Branding' : $ac_config['WEBBRANDFOLDERNAME']) . '/' . $brandSettings['name'] . '/';
        }

		$resetURL .= '?fsaction=Welcome.initForgotPasswordFromEmail';

		if ($token !== '')
		{
			$resetURL .= '&td=' . $token;
		}


        // include the email creation module
        require_once('../Utils/UtilsEmail.php');

        // Set the initial template for the email.
        $emailTemplate = 'customer_passwordresetincludingusernames';

        // Initial email parameters.
        $emailParamArray = array(
            "loginname" => $pLogin,
            "reseturl" => $resetURL,
            "accountlist" => $pUserAccountArray
        );

        // Generate and send the email.
        $emailObj = new TaopixMailer();
        $emailObj->sendTemplateEmail($emailTemplate, $pWebBrandCode, $brandSettings['applicationname'],
                            $brandSettings['displayurl'], '', '', $pLogin, '', '', 0,
                            $emailParamArray,
                            '', ''
                        );

        DatabaseObj::updateActivityLog(0, 0, 0, $pLogin, '', 0, 'CUSTOMER', 'RESETPASSWORDREQUEST', $pPasswordResetFormTokenValue, 1);

        $resultArray['result'] = $result;
        $resultArray['resultparam'] = $resultParam;
        $resultArray['ref'] = $pRecordID;
        $resultArray['cancreateaccounts'] = $canCreateAccounts;
        $resultArray['login'] = $pLogin;
        $resultArray['resetpasswordauthcode'] = $passwordResetAuthCode;
        $resultArray['validtoken'] = true;
        $resultArray['redirecturl'] = $redirectURL;

        return $resultArray;
    }

    static function generatePasswordResetToken($pUserID, $pUserDateCreated)
    {
    	$systemConfigArray = DatabaseObj::getSystemConfig();

    	// new token generation
		// the HMAC is another layer of integrtiy with regards to the data in the link.
		$tokenString = UtilsObj::createRandomString(32);
		$tokenData = 't=' . $tokenString;
		$tokenData .= chr(10) . 'hmac=' . hash_hmac('sha256', $pUserID . $pUserDateCreated, $systemConfigArray['systemkey']);

		$token = UtilsObj::encryptData($tokenData, $systemConfigArray['systemkey'], true);

		$tokenDataArray['tokenstring'] = $tokenString;
		$tokenDataArray['encryptedtoken'] = $token;

		return $tokenDataArray;
    }

    static function createResetPasswordRecord($pUserID, $pUserDateCreated, $pReturnInformation)
    {
		global $ac_config;

		$resultArray = array();
		$result = '';
		$resultParam = '';

		$expiryDuration = (int) $ac_config['RESETPASSWORDEXPIRYDURATION'];
		$passwordResetAuthCode = 0;
		$returnInformation = $pReturnInformation;

		$tokenDataArray = self::generatePasswordResetToken($pUserID, $pUserDateCreated);
		$tokenString = $tokenDataArray['tokenstring'];
		$token = $tokenDataArray['encryptedtoken'];

		// check to see if the reset password authcode is enabled.
		// if it is then we genereate a code using mt_rand but reduce the number of characters to 6.
        if (array_key_exists('RESETPASSWORDAUTHCODEENABLED', $ac_config))
        {
			if ((int) $ac_config['RESETPASSWORDAUTHCODEENABLED'] == 1)
			{
				$passwordResetAuthCode = substr(mt_rand(), 0, 6);
			}
        }

		$dbObj = DatabaseObj::getGlobalDBConnection();

		if ($dbObj)
		{
			// insert a record containing the token data
			if ($stmt = $dbObj->prepare('INSERT INTO `USERPASSWORDREQUESTS` (`datecreated`, `expirytime`, `userid`, `token`, `authenticationcode`, `returninformation`)
											VALUES (NOW(), DATE_ADD(NOW(), INTERVAL ? MINUTE), ?, ?, ?, ?)'))
			{
				if ($stmt->bind_param('iisss', $expiryDuration, $pUserID, $tokenString, $passwordResetAuthCode, $returnInformation))
				{
					if (! $stmt->execute())
					{
						// could not execute the statement
						$result = 'str_DatabaseError';
						$resultParam = 'Unable to execute - Insert into userpasswordrequests';
					}
				}
				else
				{
					// could not bind the parameters
					$result = 'str_DatabaseError';
					$resultParam = 'Unable to bind parameters - Insert into userpasswordrequests';
				}

				$stmt->free_result();
				$stmt->close();
				$stmt = null;
			}
			else
			{
				// could not prepare the statement
				$result = 'str_DatabaseError';
				$resultParam = 'Unable to prepare statement - Insert into userpasswordrequests';
			}

			$dbObj->close();
		}
		else
		{
			// could not open a database connection
			$result = 'str_DatabaseError';
			$resultParam = 'Unable to get database - Insert into userpasswordrequests';
		}

		// remove any expired reset password request records
		self::removeExpiredResetPasswordRequests();

		$resultArray['result'] = $result;
		$resultArray['resultparam'] = $resultParam;
		$resultArray['token'] = $token;
		$resultArray['resetpasswordauthcode'] = $passwordResetAuthCode;

		return $resultArray;

	}

	static function createMultipleAccountResetPasswordRecord($pUserID, $pUserDateCreated, $pReturnInformation)
	{
		global $ac_config;

		$resultArray = array();
		$result = '';
		$resultParam = '';
		$userIDForDatabase = -1;

		$expiryDuration = (int) $ac_config['RESETPASSWORDEXPIRYDURATION'];
		$returnInformation = $pReturnInformation;
		$userIDForDatabase = $pUserID * -1;

		$tokenDataArray = self::generatePasswordResetToken(($userIDForDatabase * -1), $pUserDateCreated);
		$tokenString = $tokenDataArray['tokenstring'];
		$token = $tokenDataArray['encryptedtoken'];


		$dbObj = DatabaseObj::getGlobalDBConnection();

		if ($dbObj)
		{
			// insert a record containing the token data
			if ($stmt = $dbObj->prepare('INSERT INTO `USERPASSWORDREQUESTS` (`datecreated`, `expirytime`, `userid`, `token`, `returninformation`)
											VALUES (NOW(), DATE_ADD(NOW(), INTERVAL ? MINUTE), ?, ?, ?)'))
			{
				if ($stmt->bind_param('iiss', $expiryDuration, $userIDForDatabase, $tokenString, $returnInformation))
				{
					if (! $stmt->execute())
					{
						// could not execute the statement
						$result = 'str_DatabaseError';
						$resultParam = 'Unable to execute - Insert into userpasswordrequests';
					}
				}
				else
				{
					// could not bind the parameters
					$result = 'str_DatabaseError';
					$resultParam = 'Unable to bind parameters - Insert into userpasswordrequests';
				}

				$stmt->free_result();
				$stmt->close();
				$stmt = null;
			}
			else
			{
				// could not prepare the statement
				$result = 'str_DatabaseError';
				$resultParam = 'Unable to prepare statement - Insert into userpasswordrequests';
			}

			$dbObj->close();
		}
		else
		{
			// could not open a database connection
			$result = 'str_DatabaseError';
			$resultParam = 'Unable to get database - Insert into userpasswordrequests';
		}

		// remove any expired reset password request records
		self::removeExpiredResetPasswordRequests();

		$resultArray['result'] = $result;
		$resultArray['resultparam'] = $resultParam;
		$resultArray['token'] = $token;

		return $resultArray;
	}

    static function removeExpiredResetPasswordRequests()
    {
    	// delete all reset password records that have expired

        $recordIDArray = array();
        $recordCount = 0;
        $recordID = 0;

        $dbObj = DatabaseObj::getGlobalDBConnection();
        if ($dbObj)
        {
            // find all the records which have expired
            $sqlStatement = 'SELECT id FROM `USERPASSWORDREQUESTS` WHERE `expirytime` < NOW()';

            if ($stmt = $dbObj->prepare($sqlStatement))
            {
				if ($stmt->execute())
				{
					if ($stmt->store_result())
					{
						if ($stmt->num_rows > 0)
						{
							if ($stmt->bind_result($recordID))
							{
								while ($stmt->fetch())
								{
									$recordIDArray[] = $recordID;
									$recordCount++;
								}
							}
							else
							{
								// could not bind result
								$result = 'str_DatabaseError';
								$resultParam = __FUNCTION__ . ' bind result: ' . $dbObj->error;
							}
						}
					}
					else
					{
						// could not store result
						$result = 'str_DatabaseError';
						$resultParam = __FUNCTION__ . ' store result: ' . $dbObj->error;
					}
				}
				else
				{
					// could not execute statement
					$result = 'str_DatabaseError';
					$resultParam = __FUNCTION__ . ' execute: ' . $dbObj->error;
				}

                $stmt->free_result();
                $stmt->close();
                $stmt = null;
            }
            else
            {
                // could not prepare statement
                $result = 'str_DatabaseError';
                $resultParam = __FUNCTION__ . ' prepare: ' . $dbObj->error;
            }

            // delete all the records which have expired
            if ($recordCount > 0)
            {
                $recordIDs = implode(',', $recordIDArray);

                if ($stmt = $dbObj->prepare('DELETE FROM `USERPASSWORDREQUESTS` WHERE `id` in (' . $recordIDs  . ')'))
                {
                    if (!$stmt->execute())
                    {
                        // could not execute statement
                        $result = 'str_DatabaseError';
                        $resultParam = __FUNCTION__ . ' execute: ' . $dbObj->error;
                    }

                    $stmt->close();
                }
                else
                {
                    // could not prepare statement
                    $result = 'str_DatabaseError';
                    $resultParam = __FUNCTION__ . ' prepare: ' . $dbObj->error;
                }
            }

            $dbObj->close();
        }
    }

	static function resetPasswordTokenLookUp($pTokenLookUpString)
	{
		$resultArray = Array();
		$result = '';

		$dateCreated = '';
		$userID = 0;
		$authenticationCode = 0;
		$returnInformation = '';
		$linkExpiryStatus = TPX_PASSWORDRESETLINKEXPIRY_EXPIREDNATURALLY;

		// see when the user last completed the reset password process
		$dbObj = DatabaseObj::getGlobalDBConnection();

		if ($dbObj)
		{
			// look up the reset password record based off the token but also make sure it has not expired.
			$stmt = $dbObj->prepare("SELECT UNIX_TIMESTAMP(`datecreated`), `userid`, `authenticationcode`, `returninformation` FROM `USERPASSWORDREQUESTS` WHERE `token` = ? AND `expirytime` > NOW()");
			if ($stmt)
			{
				if ($stmt->bind_param('s', $pTokenLookUpString))
				{
					if ($stmt->execute())
					{
						if ($stmt->store_result())
						{
							if ($stmt->num_rows > 0)
							{
								if ($stmt->bind_result($dateCreated, $userID, $authenticationCode, $returnInformation))
								{
									if ($stmt->fetch())
									{
										// if we find a valid record then we can set the link to be active.
										$linkExpiryStatus = TPX_PASSWORDRESETLINKEXPIRY_ACTIVE;

										// if the authentication code is not empty then we know the authentication code
										// feture is enabled and must show the user the authentication code screen.
										if ($authenticationCode != 0)
										{
											$linkExpiryStatus = TPX_PASSWORDRESETLINKEXPIRY_ACTIVEWITHAUTHCODE;
										}
									}
								}
								else
								{
									$result = 'resetPasswordTokenLookUp bind result ' . $dbObj->error;
								}
							}
						}
						else
						{
							$result = 'resetPasswordTokenLookUp store result ' . $dbObj->error;
						}
					}
					else
					{
						$result = 'resetPasswordTokenLookUp execute ' . $dbObj->error;
					}
				}
				else
				{
					$result = 'resetPasswordTokenLookUp bind param ' . $dbObj->error;
				}
			}
			else
			{
				$result = 'resetPasswordTokenLookUp prepare ' . $dbObj->error;
			}
		}
		else
		{
			$result = 'resetPasswordTokenLookUp connect ' . $dbObj->error;
		}

		$resultArray['result'] = $result;
		$resultArray['linkrequesttime'] = $dateCreated;
		$resultArray['linkexpirystatus'] = $linkExpiryStatus;
		$resultArray['userid'] = $userID;
		$resultArray['authenticationcode'] = $authenticationCode;
		$resultArray['returninformation'] = $returnInformation;

		return $resultArray;
	}

    static function determineIfUserCanResetPassword($pRecordID, $pWebBrandCode, $pLogin)
    {
    	$resultArray = Array();
		$result = 'str_ErrorNoAccount';
		$resultParam = '';

    	$canCheckLogin = true;
    	$sendNotification = false;
        $canCreateAccounts = 0;

        $userID = 0;
        $isCustomer = 0;
        $accountGroupCode = 0;
        $accountCode = '';
		$contactEmail = '';
        $contactFirstName = '';
        $contactLastName = '';
		$canModifyPassword = 0;
        $canResetPassword = false;
        $userActive = 0;
        $userDateCreated = '0000-00-00 00:00:00';
		$nextValidLoginDate = '0000-00-00 00:00:00';
		$loginAttemptCount = 0;

        $dbObj = DatabaseObj::getGlobalDBConnection();

		if ($dbObj)
		{
			if ($stmt = $dbObj->prepare('SELECT `id`, `datecreated`, `customer`, `groupcode`, `accountcode`, `emailaddress`, `contactfirstname`, `contactlastname`, `modifypassword`, `nextvalidlogindate`, `loginattemptcount`, `active`
						FROM `USERS` WHERE (`webbrandcode` = ?) AND (`login` = ?)'))
			{
				if ($stmt->bind_param('ss', $pWebBrandCode, $pLogin))
				{
					if ($stmt->execute())
					{
						if ($stmt->store_result())
						{
							if ($stmt->num_rows > 0)
							{
								if ($stmt->bind_result($userID, $userDateCreated, $isCustomer, $accountGroupCode, $accountCode, $contactEmail, $contactFirstName, $contactLastName, $canModifyPassword, $nextValidLoginDate, $loginAttemptCount, $userActive))
								{
									if ($stmt->fetch())
									{
										// Check if the account is locked. If $nextValidLoginDate is in the future, then the account is locked.
										if ((strtotime($nextValidLoginDate)) > (strtotime(DatabaseObj::getServerTimeUTC())))
										{
											// Account is locked, cannot reset password.
											$userID = 0;
											$canResetPassword = false;
											$result = 'str_ErrorAccountLockedUnableToResetPassword';
										}
										else if ($userActive == 1)
										{
											// we have matched the user name and email address so create a new random password
											$result = '';

											$sendNotification = true;
											$canResetPassword = ($canModifyPassword == 1) ? true : false;
										}
										else
										{
											$canResetPassword = false;
											$result = 'str_ErrorCannotChangePassword';
										}
									}
									else
									{
										// we have not matched the user name and email address
										$userID = 0;
										$canResetPassword = false;
									}
								}
								else
								{
									// could not bind result
									$result = 'str_DatabaseError';
									$resultParam = 'resetPassword bind result ' . $dbObj->error;
								}
							}
							else
							{
								// no rows found - we have not matched the user name and email address
								$userID = 0;
								$canResetPassword = false;
							}
						}
						else
						{
							// unable to store result
							$result = 'str_DatabaseError';
							$resultParam = 'resetPassword store result ' . $dbObj->error;
						}
					}
					else
					{
						// could not execute statement
						$result = 'str_DatabaseError';
						$resultParam = 'resetPassword execute ' . $dbObj->error;
					}
				}
				else
				{
					// could not bind parameters
					$result = 'str_DatabaseError';
					$resultParam = 'resetPassword bind params ' . $dbObj->error;
				}

				$stmt->free_result();
				$stmt->close();
				$stmt = null;
			}
			else
			{
				// could not prepare statement
				$result = 'str_DatabaseError';
				$resultParam = 'resetPassword prepare ' . $dbObj->error;
			}

			// if we have a match check to see if this is a customer account or an admin account
			if ($result == '')
			{
				$licenseKeyArray = DatabaseObj::getLicenseKeyFromCode($accountGroupCode);
				$canCreateAccounts = $licenseKeyArray['cancreateaccounts'];

				// the account does not belong to a customer so we must not check any further
				if ($isCustomer == 0)
				{
					$canCheckLogin = false;
				}
			}

			// if we have a user account make sure that the current brand matches the user license key brand
			if (($userID > 0) && ($canResetPassword))
			{
				if ($pWebBrandCode != $licenseKeyArray['webbrandcode'])
				{
					$result = 'str_ErrorNoAccount';
				}
			}
		}
		else
		{
			// could not open database connection
			$result = 'str_DatabaseError';
			$resultParam = 'resetPassword connect ' . $dbObj->error;
		}

		$resultArray['result'] = $result;
        $resultArray['resultparam'] = $resultParam;
        $resultArray['userid'] = $userID;
        $resultArray['userdatecreated'] = $userDateCreated;
        $resultArray['iscustomer'] = $isCustomer;
        $resultArray['accountgroupcode'] = $accountGroupCode;
        $resultArray['accountcode'] = $accountCode;
        $resultArray['emailaddress'] = $contactEmail;
        $resultArray['contactfirstname'] = $contactFirstName;
        $resultArray['contactlastname'] = $contactLastName;
        $resultArray['useractive'] = $userActive;
        $resultArray['canresetpassword'] = $canResetPassword;
        $resultArray['cancreateaccounts'] = $canCreateAccounts;
        $resultArray['canchecklogin'] = $canCheckLogin;
        $resultArray['sendnotification'] = $sendNotification;

       	return $resultArray;
    }

    static function resetPasswordProcess($pRecordID, $pWebBrandCode, $pLinkUserID, $pLogin, $pNewPassword, $pPasswordFormat, $pIsOrderSession)
    {
    	global $ac_config;
        global $gConstants;
        global $gSession;

        $resultArray = Array();
        $result = 'str_ErrorNoAccount';
        $resultParam = '';

        $userID = 0;
        $accountGroupCode = 0;
        $accountCode = '';
        $contactFirstName = '';
        $contactLastName = '';
        $emailAddress = '';
        $canResetPassword = false;
        $canStorePassword = true;
        $sendNotification = false;
		$passwordResetAuthCode = 0;
		$authTokenStatus = 0;
		$tokenData = '';

		// check to make sure a login an email address have been passed.
		// we also need to check if the resetpassword feature is still enabled
		// incase a licensee has turned it off for whatever reason.
        if (($pLogin != '') && ($ac_config['RESETPASSWORDENABLED'] == 1))
        {
			$determineIfUserCanResetPasswordResult = self::determineIfUserCanResetPassword($pRecordID, $pWebBrandCode, $pLogin);
			$userID = $determineIfUserCanResetPasswordResult['userid'];
			$accountGroupCode = $determineIfUserCanResetPasswordResult['accountgroupcode'];
			$accountCode = $determineIfUserCanResetPasswordResult['accountcode'];
			$emailAddress = $determineIfUserCanResetPasswordResult['emailaddress'];
			$contactFirstName = $determineIfUserCanResetPasswordResult['contactfirstname'];
			$contactLastName = $determineIfUserCanResetPasswordResult['contactlastname'];
			$canResetPassword = $determineIfUserCanResetPasswordResult['canresetpassword'];
			$canCheckLogin = $determineIfUserCanResetPasswordResult['canchecklogin'];
			$sendNotification = $determineIfUserCanResetPasswordResult['sendnotification'];
            $result = $determineIfUserCanResetPasswordResult['result'];

			// if we can still continue attempt to reset the password via an external script
			if (($canCheckLogin) && ($gConstants['optionwscrp']))
			{
				if (file_exists('../Customise/scripts/EDL_ExternalCustomerAccount.php'))
				{
					require_once('../Customise/scripts/EDL_ExternalCustomerAccount.php');

					if (method_exists('ExternalCustomerAccountObj', 'resetPassword'))
					{
						$paramArray = Array();
						$paramArray['languagecode'] = UtilsObj::getBrowserLocale();
						$paramArray['designergroupcode'] = $accountGroupCode;
						$paramArray['accountgroupcode'] = $accountGroupCode;
						$paramArray['brandcode'] = $pWebBrandCode;
						$paramArray['id'] = $userID;
						$paramArray['login'] = $pLogin;
						$paramArray['newpassword'] = $pNewPassword;
						$paramArray['passwordformat'] = $pPasswordFormat;
						$paramArray['accountcode'] = $accountCode;
						$paramArray['emailaddress'] = $emailAddress;
						$paramArray['isordersession'] = $pIsOrderSession;


						$externalResponse = ExternalCustomerAccountObj::resetPassword($paramArray);
						$canStorePassword = $externalResponse['storepassword'];
						$sendNotification = $externalResponse['sendnotification'];
						$contactFirstName = $externalResponse['contactfirstname'];
						$contactLastName = $externalResponse['contactlastname'];
						$scriptResult = $externalResponse['result'];

						/*
						if the external script has not returned an error we need to re-enable the canresetpassword flag as the external script overrides what is in the user account
						*/
						if ($scriptResult == '')
						{
							$canResetPassword = true;

							$result = '';
						}
						elseif ($scriptResult == 'NOTHANDLED')
						{
							// the script hasn't handled the reset so just continue with the current result
						}
						else
						{
							$result = $scriptResult;
						}
					}
				}
			}

			// if the result is empty then we can reset the password so make sure we are allowed to modify the password before updating it
			if ($result == '')
			{
				// check to make sure the userid in the reset link matches the userid we have retrieved based of the login they have provided.
				// if it does not match then we cant update the password/
				if ($pLinkUserID == $userID)
				{
					// we need to determine if the user can still cahnge their password as the licensee might have
					// disabled modify password for the user since they request the reset password.
					if ($canResetPassword)
					{
						if ($canStorePassword)
						{
							if (($result == '') && ($userID > 0))
							{
								// calculate the password hash depending on if the page was secure or not
								$generatePasswordHashResult = self::generatePasswordHash($pNewPassword, $pPasswordFormat);

								if ($generatePasswordHashResult['result'] == '')
								{
									$dbObj = DatabaseObj::getGlobalDBConnection();

									if ($dbObj)
									{
										if ($stmt = $dbObj->prepare('UPDATE `USERS` SET `password` = ? WHERE `id` = ?'))
										{
											if ($stmt->bind_param('si', $generatePasswordHashResult['data'], $userID))
											{
												if (! $stmt->execute())
												{
													$result = 'str_ErrorNoAccount';
												}
											}
											else
											{
												// could not bind parameters
												$result = 'str_DatabaseError';
												$resultParam = 'resetPassword bind params ' . $dbObj->error;
											}

											$stmt->free_result();
											$stmt->close();
										}
										else
										{
											// could not prepare statement
											$result = 'str_DatabaseError';
											$resultParam = 'resetPassword prepare ' . $dbObj->error;
										}
									}
									else
									{
										// could not prepare statement
										$result = 'str_DatabaseError';
										$resultParam = 'resetPassword connect ' . $dbObj->error;
									}
								}
								else
								{
									// could not generate password hash
									$result = $generatePasswordHashResult['result'];
									$resultParam = $generatePasswordHashResult['resultparam'];
								}
							}
						}

						if ($result == 'str_ErrorNoAccount')
						{
							// we couldn't match the account so log it
							DatabaseObj::updateActivityLog(0, 0, 0, $pLogin, $contactFirstName . ' ' . $contactLastName, 0, 'CUSTOMER', 'RESETPASSWORD', 'str_ErrorNoAccount', 0);
						}
						else
						{
							// we have updated the password so update the log and notify the user
							DatabaseObj::updateActivityLog(0, 0, $userID, $pLogin, $contactFirstName . ' ' . $contactLastName, 0, 'CUSTOMER', 'RESETPASSWORD', '', 1);
						}


						// if we have no error then we need to send the confirmation email and data export trigger
						if ($result == '')
						{
							// check if brand allows sending this type of emails
							$brandSettings = DatabaseObj::getBrandingFromCode($pWebBrandCode);

							// copy the brand settings. this array will be used for email settings
							$emailBrandSettings = $brandSettings;

							// if there is a brand and we are using the default brand email settings get the default brand settings from the database
							// and assign then to the emailBrandSettings variable.
							if (($brandSettings['usedefaultemailsettings'] == 1) && ($pWebBrandCode != ''))
							{
								$emailBrandSettings = DatabaseObj::getBrandingFromCode('');
							}

							// only allow the password to be reset if the email brand settings allow this.
							if ($emailBrandSettings['smtpresetpasswordactive'] == 0)
							{
								$sendNotification = false;
							}

							if ($sendNotification == true)
							{
								// include the email creation module
								require_once('../Utils/UtilsEmail.php');

								$emailObj = new TaopixMailer();
								$emailObj->sendTemplateEmail('customer_passwordresetconfirmation', $pWebBrandCode, $brandSettings['applicationname'],
													$brandSettings['displayurl'], '', $contactFirstName . ' ' . $contactLastName, $emailAddress, '', '', $userID,
													Array("userid" => $userID,
														"loginname" => $pLogin,
														"targetuserid" => $userID),
													'', ''
												);
							}

							// if we have a user account send the trigger
                            if ($userID > 0)
                            {
                                // include the data export module
                                require_once('../Utils/UtilsDataExport.php');

                                DataExportObj::EventTrigger(TPX_TRIGGER_PASSWORD_RESET, 'CUSTOMER', $userID, 0);
                            }
						}
					}
					else
					{
						// cannot modify account
						DatabaseObj::updateActivityLog(0, 0, 0, $pLogin, $contactFirstName . ' ' . $contactLastName, 0, 'CUSTOMER', 'RESETPASSWORD', 'str_ErrorCannotChangePassword', 0);

						$result = 'str_ErrorCannotChangePassword';
					}

				}
				else
				{
					// userid mismatch
					DatabaseObj::updateActivityLog(0, 0, 0, $pLogin, $contactFirstName . ' ' . $contactLastName, 0, 'CUSTOMER', 'RESETPASSWORD', 'str_MessageAuthMode_Login', 0);
					$result = 'str_MessageAuthMode_Login';
				}
			}
			else
			{
				// we have not matched a record
				DatabaseObj::updateActivityLog(0, 0, 0, $pLogin, '', 0, 'CUSTOMER', 'RESETPASSWORD', $result, 0);
			}
        }

        $resultArray['result'] = $result;
        $resultArray['resultparam'] = $resultParam;
        $resultArray['ref'] = $pRecordID;
        $resultArray['login'] = $pLogin;

        return $resultArray;
    }

    static function ssoRedirect($pResultArray)
    {
        global $ac_config;
        // do not cache the page we are about to return
        header("Expires: " . gmdate("D, j M Y H:i:s") . " GMT");
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
        header("Cache-Control: no-store, no-cache, must-revalidate");
        header("Cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache");

        $secure = "";

        if (UtilsObj::needSecureCookies())
        {
            $secure = '; secure';
        }

        // rather than just performing a re-direct we return a web page that sets the mawebtz cookie and then re-directs
        // this is so that we have the latest time offset for the user's browser which affects the session cookie expire time
        echo '<script type="text/javascript" {$nonce}>' .
            'var theDate = new Date();' .
            'var currentSecs = Math.round(theDate.getTime() / 1000);' .
            'theDate.setTime(theDate.getTime() + (2 * 60 * 60 * 1000));' .
            'document.cookie = "mawebtz=" + currentSecs + "; expires=" + theDate.toGMTString() + "; path=/' . $secure . ';";' .
            'location.replace("' . $pResultArray['resultparam'] . '");' .
        '</script>';

    }

    static function processLogin($pFromOnlineDesigner, $pIsMobile, $pData = array())
    {
        $processLoginParameters = array();
        $processLoginParameters['webbrandcode'] = '';
        $processLoginParameters['groupcode'] = '';
        $processLoginParameters['login'] = '';
        $processLoginParameters['password'] = '';
        $processLoginParameters['format'] = TPX_PASSWORDFORMAT_MD5;
        $processLoginParameters['basketref'] = '';
        $processLoginParameters['basketapiworkflowtype'] = TPX_BASKETWORKFLOWTYPE_NORMAL;
        $processLoginParameters['ref'] = -1;
        $processLoginParameters['ishighlevel'] = 0;
        $processLoginParameters['taopixonlinelogin'] = false;

        // if there is data then this has come from online
        if ($pFromOnlineDesigner == 1)
        {
            $processLoginParameters['webbrandcode'] = $pData['webbrandcode'];
            $processLoginParameters['groupcode'] = $pData['groupcode'];
            $processLoginParameters['login'] = $pData['login'];
            $processLoginParameters['password'] = $pData['password'];
            $processLoginParameters['format'] = (isset($pData['format']) ? $pData['format'] : TPX_PASSWORDFORMAT_MD5);
            $processLoginParameters['basketref'] = $pData['basketref'];
            $processLoginParameters['basketapiworkflowtype'] = $pData['basketapiworkflowtype'];
            $processLoginParameters['taopixonlinelogin'] = true;
            $processLoginParameters['reauthenticate'] = $pData['reauthenticate'];
            $processLoginParameters['ipaddress'] = $pData['ipaddress'];
        }
        else
        {
            $processLoginParameters['ref'] = UtilsObj::getPOSTParam('ref', -1);
            $processLoginParameters['ishighlevel'] = UtilsObj::getPOSTParam('ishighlevel', 0);

            $processLoginParameters['login'] = UtilsObj::getPOSTParam('login');
            $processLoginParameters['password'] = UtilsObj::getPOSTParam('password');
            $processLoginParameters['format'] = (int) UtilsObj::getPOSTParam('format', TPX_PASSWORDFORMAT_MD5);

            if ($processLoginParameters['ishighlevel'] == 1)
            {
                $processLoginParameters['groupcode'] = UtilsObj::getPOSTParam('groupcode', '');
                $processLoginParameters['taopixonlinelogin'] = true;
            }

			$processLoginParameters['ipaddress'] = UtilsObj::getClientIPAddress();
        }

		return self::processLoginSuper($pFromOnlineDesigner, $pIsMobile, $processLoginParameters);
    }

    static function processLoginSuper($pFromOnlineDesigner, $pIsMobile, $pProcessLoginParameters)
    {
        global $gSession;

        $result = '';
        $isOrderSession = 0;
        $sessionRef = 0;
        $login = '';
        $password = '';
        $passwordFormat = -1;
        $groupCode = '';
        $brandCode = '';
        $startSessionOnSuccess = false;
        $onlyAcceptCustomerLogins = false;
		$gSession['ismobile'] = $pIsMobile;
        $reason = TPX_USER_AUTH_REASON_WEB_LOGIN;

        $basketData = array('ref'=>'', 'apitype' => TPX_BASKETWORKFLOWTYPE_NORMAL);

        $recordID = $pProcessLoginParameters['ref'];
        $fromHigheLevelBasketRequest = $pProcessLoginParameters['ishighlevel'];
        $taopixOnlineLogin = $pProcessLoginParameters['taopixonlinelogin'];

        // determine what type of logins we can process
        if ($pFromOnlineDesigner == 1)
        {
            // requests from the online designer will only accept customer logins

            $sessionRef = 0;
            $recordID = 0;
            $brandCode = $pProcessLoginParameters['webbrandcode'];
            $groupCode = $pProcessLoginParameters['groupcode'];
            $basketData['apitype'] = $pProcessLoginParameters['basketapiworkflowtype'];
            $basketData['ref'] = $pProcessLoginParameters['basketref'];

            $isOrderSession = 0;
            $onlyAcceptCustomerLogins = true;
            $startSessionOnSuccess = false;

            // only start a new session if the login is not a reauthenticate call since there is already a session
            if ($pProcessLoginParameters['reauthenticate'] == 0)
            {
                $startSessionOnSuccess = ($basketData['apitype'] == TPX_BASKETWORKFLOWTYPE_HIGHLEVELAPI);
            }
            else
            {
                // set the reason to be requthenticate if we are only checking the password
                // this is changed so that the authenticateLogin function can take a different set of actions
                $reason = TPX_USER_AUTH_REASON_ONLINE_REAUTHENTICATE;
            }
        }
        else
        {
            $startSessionOnSuccess = true;

            if ($fromHigheLevelBasketRequest == 1)
            {
                $sessionRef = 0;
                $recordID = 0;
                $groupCode = $pProcessLoginParameters['groupcode'];

                $licenseKeyArray = DatabaseObj::getLicenseKeyFromCode($groupCode);
                $brandCode = $licenseKeyArray['webbrandcode'];

                $onlyAcceptCustomerLogins = true;

            }
            else
            {
                // requests from control centre accept different logins depending on the situation
                // if we have been provided with a ref then this is an order so we only accept customer logins
                // if we have not been provided with a ref then this is a general control centre login request
                $sessionRef = $gSession['ref'];
                $brandCode = $gSession['webbrandcode'];
                $onlyAcceptCustomerLogins = ($recordID > 0);

                if (($recordID > 0) && ($gSession['ref'] > 0))
                {
                    $isOrderSession = $gSession['isordersession'];

                    // if we have an order session grab the group code from the session
                    if ($isOrderSession)
                    {
                        $groupCode = $gSession['licensekeydata']['groupcode'];
                    }

                    $basketData['ref'] = $gSession['basketref'];

                }
            }
        }

        $login = UtilsObj::cleanseInput($pProcessLoginParameters['login']);
        $password = $pProcessLoginParameters['password'];
        $passwordFormat = (int) $pProcessLoginParameters['format'];

        if ($recordID > 0)
        {
            $result = $gSession['result'];
            if ($sessionRef > 0)
            {
                $isOrderSession = $gSession['isordersession'];

                // if we have an order session grab the group code from the session
                if ($isOrderSession)
                {
                    $groupCode = $gSession['licensekeydata']['groupcode'];
                }
            }
        }

		$ipAddress = $pProcessLoginParameters['ipaddress'];

        // process the login
        return self::authenticateLogin($reason, $sessionRef, $isOrderSession, UtilsObj::getBrowserLocale(),
                $brandCode, $groupCode, '', $login, $passwordFormat, $password, $onlyAcceptCustomerLogins, $startSessionOnSuccess, false, '', array(), $basketData, $taopixOnlineLogin, $ipAddress);
    }


    static function getSessionRef()
    {
        $sessionID = 0;

        // get the session reference from the GET or POST parameter
        if (array_key_exists('ref', $_GET))
        {
            $sessionID = $_GET['ref'];
        }
        else if (array_key_exists('ref', $_POST))
        {
            $sessionID = $_POST['ref'];
        }

        if (!is_numeric($sessionID))
        {
            $sessionID = 0;
        }

        return $sessionID;
    }

    static function endWebSession()
    {
        // end the web session
        $sessionID = self::getSessionRef();

        if ($sessionID > 0)
        {
            DatabaseObj::clearSessionExpire($sessionID);
        }
    }

    static function WebSessionActive()
    {
        // determine if the current session is active
        global $gAuthSession;

        $sessionID = self::getSessionRef();

        if ($sessionID > 0)
        {
            if (isset($_COOKIE['mawebdata' . $sessionID]))
            {
                $securityID = $_COOKIE['mawebdata' . $sessionID];
            }
            else
            {
                $securityID = -1;
            }

            $resultArray = DatabaseObj::getSessionExpire($sessionID);

            $sessionExpire = $resultArray['sessionexpiretime'];

            if (($securityID == md5(base64_encode($sessionID))) || ($gAuthSession == false))
            {
                // the security id matches
                if ($sessionExpire > -1)
                {
                    // we have an active session so determine its expiration status
                    if ($resultArray['servertime'] > $sessionExpire)
                    {
                        // the session has expired
                        return 0;
                    }
                    else
                    {
                        // the session is active so update the last access time
                        DatabaseObj::updateSessionExpire($sessionID);
                        return 1;
                    }
                }
                elseif ($sessionExpire == -2)
                {
                    // the session database record doesn't exist any more
                    return -2;
                }
                else
                {
                    // we have a session but it has never been started
                    return -1;
                }
            }
            else
            {
                // the security id does not match
                if ($sessionExpire == -2)
                {
                    // the session database record doesn't exist any more
                    return -2;
                }

                return -1;
            }
        }
        else
        {
            // we don't have a session
            return -1;
        }
    }

    static function checkHighLevelBasketSessionActive($pBasketRef, $pUpdateSessionExpire = true)
    {
    	// return the session expiration status
        $resultArray = Array();

        $result = '';
        $resultParam = '';

        $sessionID = 0;
        $isSessionActive = 0;
        $isSessionEnabled = 0;
        $sessionExpire = 0;
        $serverTime = 0;
        $userID = 0;
        $userName = '';
        $sessionActive = 0;
        $login = '';

		if (($pBasketRef != '') && ($pBasketRef != TPX_ONLINE_BASKETAPI_DEFAULTBASKETREF))
        {
			$dbObj = DatabaseObj::getGlobalDBConnection();

			$sql = 'SELECT `id`, `sessionactive`, `sessionenabled`, `sessionexpiredate`, now(), `userid` FROM `SESSIONDATA` WHERE (`basketref` = ?)
				AND (`sessionactive` = 1) AND (`ordersession` = 0) ORDER BY `id` DESC';

			$bindParamAttr = array('s', $pBasketRef);

			if ($dbObj)
			{
				if ($stmt = $dbObj->prepare($sql))
				{
					$bindOK = call_user_func_array(array(&$stmt, 'bind_param'), UtilsObj::makeValuesReferenced($bindParamAttr));

					if ($bindOK)
					{
						if ($stmt->execute())
						{
							if ($stmt->store_result())
							{
								if ($stmt->num_rows > 0)
								{
									if ($stmt->bind_result($sessionID, $isSessionActive, $isSessionEnabled, $sessionExpire, $serverTime, $userID))
									{
										if ($stmt->fetch())
										{
											if ($isSessionEnabled == 1)
											{
												if ($isSessionActive == 1)
												{
													if ($sessionExpire != '')
													{
														$sessionExpire = strtotime($sessionExpire);
														$serverTime = strtotime($serverTime);
													}
													else
													{
														// the session has not been started
														$sessionExpire = 0;
													}
												}
												else
												{
													// the session is not active
													$sessionExpire = -1;
												}
											}
											else
											{
												// the session has been disabled for user interaction (ie it has been completed)
												$sessionExpire = -2;
											}
										}
										else
										{
											// the session doesn't exist in the database
											$sessionExpire = -2;
										}
									}
									else
									{
										// could not bind result
										$result = 'str_DatabaseError';
										$resultParam = 'checkHighLevelBasketSessionActive bind result' . $dbObj->error;
									}
								}
								else
								{
									// no rows found - the session doesn't exist in the database
									$sessionExpire = -2;
								}
							}
							else
							{
								// could not store result
								$result = 'str_DatabaseError';
								$resultParam = 'checkHighLevelBasketSessionActive store result ' . $dbObj->error;
							}
						}
						else
						{
							$result = 'str_DatabaseError';
							$resultParam = 'checkHighLevelBasketSessionActive execute ' . $dbObj->error;
						}
					}
					else
					{
						// could not bind parameters
						$result = 'str_DatabaseError';
						$resultParam = 'checkHighLevelBasketSessionActive bind params ' . $dbObj->error;
					}

					$stmt->free_result();
					$stmt->close();
					$stmt = null;
				}
				else
				{
					// could not prepare statement
					$result = 'str_DatabaseError';
					$resultParam = 'checkHighLevelBasketSessionActive prepare ' . $dbObj->error;
				}
				$dbObj->close();
			}

			if ($sessionExpire > -1)
			{
				// we have an active session so determine its expiration status
				if ($serverTime > $sessionExpire)
				{
					// the session has expired
					$sessionActive = 0;
				}
				else
				{
					// the session is active
					$sessionActive = 1;

					// update the session expire time if requested
					if ($pUpdateSessionExpire)
					{
						DatabaseObj::updateSessionExpire($sessionID);
					}
				}
			}
			elseif ($sessionExpire == -2)
			{
				// the session database record doesn't exist any more
				$sessionActive = -2;
			}
			else
			{
				// we have a session but it has never been started
				$sessionActive = -1;
			}

			if ($sessionActive == 1)
			{
				$userAccountArray = DatabaseObj::getUserAccountFromID($userID);
				$userName = $userAccountArray['contactfirstname'] . ' ' . $userAccountArray['contactlastname'];
				$login = $userAccountArray['login'];
			}
		}
		else
		{
			$sessionActive = -2;
		}

		$resultArray['result'] = $result;
		$resultArray['resultparam'] = $resultParam;
		$resultArray['sessionid'] = $sessionID;
        $resultArray['sessionactive'] = $sessionActive;
        $resultArray['userid'] = $userID;
        $resultArray['username'] = $userName;
        $resultArray['login'] = $login;

        return $resultArray;
    }

    static function checkHighLevelBasketExpired($pBasketRef)
    {
    	// return the basket expiration status
        $resultArray = Array();

        $result = '';
        $resultParam = '';

        $basketExpire = 0;
        $serverTime = 0;
        $basketActive = 0;

        $dbObj = DatabaseObj::getGlobalDBConnection();

        $sql = 'SELECT `basketexpiredate`, now() FROM `ONLINEBASKET` WHERE `basketref` = ? order by `id` desc';

        if ($dbObj)
        {
            if ($stmt = $dbObj->prepare($sql))
            {
                if ($stmt->bind_param('s', $pBasketRef))
                {
                    if ($stmt->execute())
                    {
                        if ($stmt->store_result())
                        {
                            if ($stmt->num_rows > 0)
                            {
                                if ($stmt->bind_result($basketExpire, $serverTime))
                                {
                                    if ($stmt->fetch())
                                    {
										if ($basketExpire != '')
										{
											$basketExpire = strtotime($basketExpire);
											$serverTime = strtotime($serverTime);
										}
										else
										{
											// the session has not been started
											$basketExpire = 0;
										}
                                    }
                                    else
                                    {
                                        // the session doesn't exist in the database
                                        $basketExpire = -2;
                                    }
                                }
                                else
                                {
                                    // could not bind result
                                    $result = 'str_DatabaseError';
                                    $resultParam = 'checkHighLevelBasketExpired bind result' . $dbObj->error;
                                }
                            }
                            else
                            {
                                // no rows found - the session doesn't exist in the database
                                $basketExpire = -2;
                            }
                        }
                        else
                        {
                            // could not store result
                            $result = 'str_DatabaseError';
                            $resultParam = 'checkHighLevelBasketExpired store result ' . $dbObj->error;
                        }
                    }
                    else
                    {
                        $result = 'str_DatabaseError';
                        $resultParam = 'checkHighLevelBasketExpired execute ' . $dbObj->error;
                    }
                }
                else
                {
                    // could not bind parameters
                    $result = 'str_DatabaseError';
                    $resultParam = 'checkHighLevelBasketExpired bind params ' . $dbObj->error;
                }

                $stmt->free_result();
                $stmt->close();
                $stmt = null;
            }
            else
            {
                // could not prepare statement
                $result = 'str_DatabaseError';
                $resultParam = 'checkHighLevelBasketExpired prepare ' . $dbObj->error;
            }
            $dbObj->close();
        }

        if ($basketExpire > -1)
		{
			// we have an active basket so determine its expiration status
			if ($serverTime > $basketExpire)
			{
				// the basket has expired
				$basketActive = 0;
			}
			else
			{
				$basketActive = 1;
			}
		}
		elseif ($basketExpire == -2)
		{
			// the basket database record doesn't exist any more
			$basketActive = -2;
		}

		$resultArray['result'] = $result;
		$resultArray['resultparam'] = $resultParam;
        $resultArray['basketactive'] = $basketActive;

        return $resultArray;
    }

    static function adminSessionActive()
    {
		global $gSession;

        // determine if the admin session is active
        // if not then return the code to perform the log-out
        $sessionActive = self::WebSessionActive();

		$isAdminUser = false;
        if (isset($gSession['userdata']['usertype'])) {
			$isAdminUser = in_array($gSession['userdata']['usertype'], [
				TPX_LOGIN_SYSTEM_ADMIN,
				TPX_LOGIN_COMPANY_ADMIN,
				TPX_LOGIN_SITE_ADMIN,
				TPX_LOGIN_DISTRIBUTION_CENTRE_USER,
				TPX_LOGIN_STORE_USER,
				TPX_LOGIN_BRAND_OWNER,
				TPX_LOGIN_PRODUCTION_USER
			]);
		}

		if (($sessionActive != 1) || (!$isAdminUser))
        {
            // include the admin functions as these control the logout process
            require_once('../Admin/Admin_control.php');

            if (isset($_REQUEST['_lj']))
            {
                // the request is from LoadJavascript and expects initialize function back
                Admin_control::logout();
                echo 'function initialize(){logOut();}';
                return -1;
            }
            else
            {
                // modify the function that handles the response after formPost and if errorCode=0 then redirect to login page
                Admin_control::logout();
                echo '{"success":false, "errorcode":0}';
                return -1;
            }
        }

        return $sessionActive;
    }


    /**
    * Retrieves if the production session is active or not based on the reference based in the POST or GET parameter
    *
    * @static
    *
    * @return boolean
    *   the result will either be true or false depending on if the production session is active
    *
    * @author Kevin Gale
    * @since Version 3.0.0
    */
    static function productionSessionActive()
    {
        // determine if the production session is active

        $sessionActive = false;

        $sessionID = self::getSessionRef();

        if ($sessionID > 0)
        {
            $resultArray = DatabaseObj::getSessionExpire($sessionID);
            $sessionExpire = $resultArray['sessionexpiretime'];

            // the security id matches
            if ($sessionExpire > -1)
            {
                // we have an active session so determine its expiration status
                if ($resultArray['servertime'] > $sessionExpire)
                {
                    // the session has expired
                    $sessionActive = false;
                }
                else
                {
                    // the session is active so update the last access time
                    DatabaseObj::updateSessionExpire($sessionID);

                    $sessionActive = true;
                }
            }
            elseif ($sessionExpire == -2)
            {
                // the session database record doesn't exist any more
                $sessionActive = false;
            }
            else
            {
                // we have a session but it has never been started
                $sessionActive = false;
            }
        }

        return $sessionActive;
    }

    static function getCurrentSessionData()
    {
        // return the current session or an empty session if one does not exist
        $sessionData = DatabaseObj::getSessionData(self::getSessionRef());

        if ($sessionData['result'] != '')
        {
            $lastResult = $sessionData['result'];
            $sessionData = self::createSessionDataArray();
            $sessionData['result'] = $lastResult;
        }

        return $sessionData;
    }

    static function getWebBrandData($pWebBrandCode)
    {
        // return the brand data for the supplied brand code
        global $ac_config;

        $resultArray = Array();

        $defaultBrand = DatabaseObj::getBrandingFromCode('');

        $webBrandCode = $pWebBrandCode;
        $webBrandName = '';
        $webBrandApplicationName = $defaultBrand['applicationname'];
        $webBrandDisplayURL = $defaultBrand['displayurl'];
        $webBrandWebURL = $defaultBrand['weburl'];
        $webBrandWebRoot = $defaultBrand['weburl'];
        $webBrandSupportTelephoneNumber = $defaultBrand['supporttelephonenumber'];
        $webBrandSupportEmail = $defaultBrand['supportemailaddress'];
        $webDefaultCommunicationPreference = $defaultBrand['defaultcommunicationpreference'];

        if ($webBrandCode != '')
        {
            $webBrandArray = DatabaseObj::getBrandingFromCode($webBrandCode);

            if ($webBrandArray['isactive'] == 1)
            {
                $webBrandName = $webBrandArray['name'];
                $webBrandApplicationName = $webBrandArray['applicationname'];
                $webBrandSupportTelephoneNumber = $webBrandArray['supporttelephonenumber'];
                $webBrandSupportEmail = $webBrandArray['supportemailaddress'];
                $webDefaultCommunicationPreference = $webBrandArray['defaultcommunicationpreference'];

                if ($webBrandArray['displayurl'] != '')
                {
                    $webBrandDisplayURL = $webBrandArray['displayurl'];
                }
                else
                {
                    $webBrandDisplayURL = UtilsObj::correctPath($defaultBrand['displayurl']) . ($ac_config['WEBBRANDFOLDERNAME'] == '' ? 'Branding' : $ac_config['WEBBRANDFOLDERNAME']) .
                        '/' . $webBrandName . '/';
                }
                if ($webBrandArray['weburl'] != '')
                {
                    $webBrandWebURL = $webBrandArray['weburl'];
                    $webBrandWebRoot = $webBrandArray['weburl'];
                }
                else
                {
                    $webBrandWebURL = UtilsObj::correctPath($defaultBrand['weburl']) . ($ac_config['WEBBRANDFOLDERNAME'] == '' ? 'Branding' : $ac_config['WEBBRANDFOLDERNAME']) .
                        '/' . $webBrandName . '/';
                }
            }
            else
            {
                $webBrandCode = '';
            }
        }


        $resultArray['webbrandcode'] = $webBrandCode;
        $resultArray['webbrandname'] = $webBrandName;
        $resultArray['webbrandapplicationname'] = $webBrandApplicationName;
        $resultArray['webbranddisplayurl'] = $webBrandDisplayURL;
        $resultArray['webbrandweburl'] = $webBrandWebURL;
        $resultArray['webbrandwebroot'] = $webBrandWebRoot;
        $resultArray['webbrandsupporttelephonenumber'] = $webBrandSupportTelephoneNumber;
        $resultArray['webbrandsupportemailaddress'] = $webBrandSupportEmail;
        $resultArray['webbranddefaultcommunicationpreference'] = $webDefaultCommunicationPreference;

        return $resultArray;
    }

    static function setSessionWebBrand($pWebBrandCode)
    {
        // set the session web brand parameters from the supplied brand code
        global $gSession;

        $webBrandArray = self::getWebBrandData($pWebBrandCode);

        $gSession['webbrandcode'] = $webBrandArray['webbrandcode'];
        $gSession['webbrandname'] = $webBrandArray['webbrandname'];
        $gSession['webbrandapplicationname'] = $webBrandArray['webbrandapplicationname'];
        $gSession['webbranddisplayurl'] = $webBrandArray['webbranddisplayurl'];
        $gSession['webbrandweburl'] = $webBrandArray['webbrandweburl'];
        $gSession['webbrandwebroot'] = $webBrandArray['webbrandwebroot'];
        $gSession['webbrandsupporttelephonenumber'] = $webBrandArray['webbrandsupporttelephonenumber'];
        $gSession['webbrandsupportemailaddress'] = $webBrandArray['webbrandsupportemailaddress'];
        $gSession['webbranddefaultcommunicationpreference'] = $webBrandArray['webbranddefaultcommunicationpreference'];

    }

    static function defineSessionCCICookie()
    {
        // set the cookie value that we will use to track where the user is within the payment integration
        // note. the cookie itself is not set here

        global $gSession;

        $gSession['order']['ccicookie'] = time();
    }

    static function clearSessionCCICookie()
    {
        // clear the session payment integration cookie and reset the data fields

        global $gSession;
        global $ac_config;

        if (($gSession['order']['ccicookie'] != '') || ($gSession['order']['ccidata'] != ''))
        {
            setcookie('mawebcci' . $gSession['ref'], '', 1, '/', '', UtilsObj::needSecureCookies());
            $gSession['order']['ccicookie'] = '';
            $gSession['order']['ccidata'] = '';
            DatabaseObj::updateSession();
        }
    }

    static function clearSessionCookies()
    {
        // clear the session cookies even if there were not set

        global $gSession;
        global $ac_config;

        setcookie('mawebdata' . $gSession['ref'], '', 1, '/', '', UtilsObj::needSecureCookies(), true);
        setcookie('mawebcci' . $gSession['ref'], '', 1, '/', '', UtilsObj::needSecureCookies());
    }

     /**
    * Retrieves if the session is active or not based on the reference based in the POST or GET parameter
    *
    * @static
    *
    * @return boolean
    *   the result will either be true or false depending on if the session is active
    *
    * @author Loc Dinh
    * @since Version 3.0.0
    */
    static function dataAPISessionActive($pSessionID)
    {
        // determine if the session is active
        $sessionActive = false;

        if ($pSessionID > 0)
        {
            $resultArray = DatabaseObj::getSessionExpire($pSessionID);
            $sessionExpire = $resultArray['sessionexpiretime'];

            // the security id matches
            if ($sessionExpire > -1)
            {
                // we have an active session so determine it's expiration status
                if ($resultArray['servertime'] > $sessionExpire)
                {
                    // the session has expired
                    $sessionActive = false;
                }
                else
                {
                    // the session is active so update the last access time
                    DatabaseObj::updateSessionExpire($pSessionID);
                    $sessionActive = true;
                }
            }
            elseif ($sessionExpire == -2)
            {
                // the session database record doesn't exist any more
                $sessionActive = false;
            }
            else
            {
                // we have a session but it has never been started
                $sessionActive = false;
            }
        }

        return $sessionActive;
    }


    static function createOnlineSession($pWebSessionData)
    {
		require_once('../libs/internal/curl/Curl.php');

        global $ac_config;

        $resultArray = array('error' => '', 'errorparam' => '', 'brandurl' => '', 'maintenancemode' => false);

        $putParamArray = array('paramdata' => $pWebSessionData);

        $postResult = CurlObj::sendByPut($ac_config['TAOPIXONLINEURL'], 'OnlineAPI.Web.initOnlineSession', $putParamArray, true);

        if ($postResult['error'] === '')
        {
            // Decrypt the returned data
            $decodedData = $postResult['data'];

            if (isset($decodedData['result']) && $decodedData['result'] === '')
            {
                $extraParam = "";

                if (UtilsObj::getArrayParam($_GET, "_ga", "") != "")
                {
                    $extraParam = "&_ga=" . $_GET['_ga'];
                }
                else if (UtilsObj::getArrayParam($_POST, "_ga", "") != "")
                {
                    $extraParam = "&_ga=" . $_POST['_ga'];
                }

                $resultArray['brandurl'] = $decodedData['brandurl'] . $extraParam;
                $resultArray['projectref'] = $decodedData['projectref'];
                $resultArray['projectname'] = $decodedData['projectname'];
                $resultArray['userid'] = $decodedData['userid'];
            }
            else
            {
                if ($decodedData['result'] == TPX_ONLINE_ERROR_MAINTENANCEMODE)
                {
                    $resultArray['maintenancemode'] = true;
                    $resultArray['brandurl'] = $decodedData['brandurl'];
                }
                else
                {
                    $resultArray['error'] = $decodedData['result'];
                }
            }
        }
        else
        {
            $resultArray['error'] = $postResult['error'];
            $resultArray['errorparam'] = '';
        }

        return $resultArray;
    }

	/**
	 * Register the last login date and IP address for the user and reset limit data.
	 *
	 * @param int $pUserID ID of the user to be updated.
	 */
	static function updateUserlastLogin($pUserID)
	{
		$ipAddress = UtilsObj::getClientIPAddress();
		$blockReason = TPX_BLOCK_REASON_NONE;

		$sql = 'UPDATE `USERS` SET `lastlogindate` = now(),
					`lastloginip` = ?,
					`nextvalidlogindate`= ?,
					`loginattemptcount` = 0,
					`blockreason` = ?,
					`redactionprogress` = 0,
					`redactionstate` = 0,
					`redactionreason` = ""
				WHERE `id` = ?';

        $dbObj = DatabaseObj::getGlobalDBConnection();

        // insert a new entry into the activity log
        if ($stmt = $dbObj->prepare($sql))
        {
			$serverTime = DatabaseObj::getServerTimeUTC();

            if ($stmt->bind_param('ssii', $ipAddress, $serverTime, $blockReason, $pUserID))
            {
                $stmt->execute();
            }

            $stmt->free_result();
            $stmt->close();
        }
	}


	/**
	 * Update the user data for the limits in database. Attempts and lock time are updated.
	 *
	 * @param int $pUserID ID of the user to be updated.
	 * @param int $pLoginAttemptsCount Number of attempts executed since the last succeed log in.
	 * @return The date which the account will be unlocked.
	 */
	static function updateUserFailedLogin($pUserID, $pLoginAttemptsCount)
	{
		global $ac_config;
		global $gConstants;

		$blockReason = TPX_BLOCK_REASON_RATE_LIMIT;

		// Account is locked for 3 seconds in case of wrong password used.
		$lockTimeOut = (isset($ac_config['RATELIMITLOCKOUTTIME'])) ? $ac_config['RATELIMITLOCKOUTTIME'] : TPX_AUTHENTICATION_REPEAT_TRIES;

		// Check if the number of attempts is not over the maximium.
		if ($pLoginAttemptsCount >= $gConstants['maxloginattempts'])
		{
			// Set the blockreason to be IP blocked.
			$blockReason = TPX_BLOCK_REASON_IP_BLOCK;

			// Time is stored in a minute in database.
			$lockTimeOut = $gConstants['accountlockouttime'] * 60;

			if (self::canSendLockedAccountEmail($pLoginAttemptsCount))
			{
				// If we have locked the account send the user an email letting them know that their account is locked.
				self::sendLockedAccountEmail($pUserID,  $gConstants['accountlockouttime']);
			}
		}

		// Get the new time with the offset.
        $nextValidDate = date('Y-m-d H:i:s', strtotime(DatabaseObj::getServerTimeUTC()) + $lockTimeOut);
        $dbObj = DatabaseObj::getGlobalDBConnection();

		if ($dbObj)
		{
			$sql = 'UPDATE `USERS`
					SET `nextvalidlogindate`= ?,
						`loginattemptcount` = `loginattemptcount` + 1,
						`blockreason` = ?
					WHERE `id` = ?';

			$stmt = $dbObj->prepare($sql);

			// Update the user limits.
			if ($stmt)
			{
				if ($stmt->bind_param('sii', $nextValidDate, $blockReason, $pUserID))
				{
					$stmt->execute();
				}

				$stmt->free_result();
				$stmt->close();
			}
		}

		return $nextValidDate;
	}

	/**
	 * Return a the correct string and the replacement compare to the time left before the account is unlocked.
	 *
	 * @param datetime $pReleaseLockTime Date time until the account is locked.
	 * @param int $pLoginAttemptsCount Number of attempts executed since the last succeed log in.
	 * @retruns Error message and it's replacement if needed.
	 */
	static function getLimitErrorMessage($pReleaseLockTime, $pLoginAttemptsCount)
	{
		global $gConstants;

		$dateMessage = 'str_ErrorNoAccount';
		$resultValue = '';

		// Detect if the number of attempts is over the limit.
		if ($pLoginAttemptsCount >= $gConstants['maxloginattempts'])
		{
			// Get the difference between now and the lock end.
			$deltaTime = abs(ceil((strtotime($pReleaseLockTime) - (strtotime(DatabaseObj::getServerTimeUTC()))) / 60));

			if ($deltaTime <= 1)
			{
				$dateMessage = 'str_ErrorAccountLockedBecauseNumberOfAttemptsExceededMinute';
			}
			else
			{
				$dateMessage = 'str_ErrorAccountLockedBecauseNumberOfAttemptsExceededMinutes';
				$resultValue = $deltaTime;
			}
		}

		return array('message' => $dateMessage, 'value' => $resultValue);
	}


    static function userSessionActive($pUserID)
    {
        // determine if the user has an active session
		$sessionID = 0;
		$sessionExpire = '0000-00-00 00:00:00';
		$sessionsList = array();

		$resultArray = array('error' => '', 'errorparam' => '', 'sessionactive' => 0);

		// get sessions for user ID
		$sql = 'SELECT `id`, `sessionexpiredate` FROM `SESSIONDATA` WHERE `userid` = ? and `sessionactive` = 1';

		$dbObj = DatabaseObj::getGlobalDBConnection();

		if ($dbObj)
		{
			if ($stmt = $dbObj->prepare($sql))
			{
				if ($stmt->bind_param('i', $pUserID))
				{
					if ($stmt->execute())
					{
						if ($stmt->store_result())
						{
							if ($stmt->num_rows > 0)
							{
								if ($stmt->bind_result($sessionID, $sessionExpire))
								{
									while ($stmt->fetch())
									{
										// do something
										$sessionsList[] = $sessionID;
									}
								}
								else
								{
									$resultArray['error'] = 'str_DatabaseError';
									$resultArray['errorparam'] = __FUNCTION__ . ' store_result: ' . $dbObj->error;
								}
							}
						}
						else
						{
							$resultArray['error'] = 'str_DatabaseError';
							$resultArray['errorparam'] = __FUNCTION__ . ' store_result: ' . $dbObj->error;
						}
					}
					else
					{
						$resultArray['error'] = 'str_DatabaseError';
						$resultArray['errorparam'] = __FUNCTION__ . ' execute: ' . $dbObj->error;
					}
				}
				else
				{
					$resultArray['error'] = 'str_DatabaseError';
					$resultArray['errorparam'] = __FUNCTION__ . ' bind_param: ' . $dbObj->error;
				}

				$stmt->free_result();
				$stmt->close();
				$stmt = null;
			}
			else
			{
				$resultArray['error'] = 'str_DatabaseError';
				$resultArray['errorparam'] = __FUNCTION__ . ' prepare: ' . $dbObj->error;
			}

			$dbObj->close();
		}
		else
		{
			$resultArray['error'] = 'str_DatabaseError';
			$resultArray['errorparam'] = __FUNCTION__ . ' connect ' . $dbObj->error;
		}


		// sessions have been found, check if they have expired
		if (count($sessionsList) > 0)
		{
			foreach ($sessionsList as $sessionID)
			{
				$sessionExpireResult = DatabaseObj::getSessionExpire($sessionID);

				$sessionExpire = $sessionExpireResult['sessionexpiretime'];

				if ($sessionExpire > -1)
				{
					$resultArray['sessionactive'] = 0;
					// we have an active session so determine its expiration status
					if ($sessionExpireResult['servertime'] <= $sessionExpire)
					{
						// the session is active
						$resultArray['sessionactive'] = 1;
					}
				}
			}
		}

		return $resultArray;
    }

    static function readSSOCookie()
    {
        $returnArray = array('result' => '', 'resultparam' => '', 'cookievalue' => '');

        if (isset($_COOKIE[TPX_SSO_COOKIE_NAME]))
        {
            // grab the sso key from the cookie
            $returnArray['cookievalue'] = $_COOKIE[TPX_SSO_COOKIE_NAME];

            if ($returnArray['cookievalue'] == '')
            {
                $returnArray['result'] = 'str_ErrorSSOGeneral';
                $returnArray['resultparam'] = 'Error Code: ' . TPX_SSO_ERROR_CODE_EMPTY_COOKIE;
            }
        }
        else
        {
            // it was not possible to read the cookie
            $returnArray['result'] = 'str_ErrorSSOGeneral';
            $returnArray['resultparam'] = 'Error Code: ' . TPX_SSO_ERROR_CODE_MISSING_COOKIE;
        }

        return $returnArray;

    }

	static function removeSSOLLCookie()
	{
		setcookie(TPX_SSO_LL_COOKIE_NAME, null, -1, '/');
	}

    static function readSSOLLCookie()
    {
        $returnArray = array('result' => '', 'resultparam' => '', 'cookievalue' => '');

        if (isset($_COOKIE[TPX_SSO_LL_COOKIE_NAME]))
        {
            // grab the sso key from the cookie
            $returnArray['cookievalue'] = $_COOKIE[TPX_SSO_LL_COOKIE_NAME];

            if ($returnArray['cookievalue'] == '')
            {
                $returnArray['result'] = 'str_ErrorSSOGeneral';
                $returnArray['resultparam'] = 'Error Code: ' . TPX_SSO_ERROR_CODE_EMPTY_COOKIE;
            }
        }
        else
        {
            // it was not possible to read the cookie
            $returnArray['result'] = 'str_ErrorSSOGeneral';
            $returnArray['resultparam'] = 'Error Code: ' . TPX_SSO_ERROR_CODE_MISSING_COOKIE;
        }

        return $returnArray;

    }

    static function createSSODataRecord($pPrivateDataArray, $pOriginURL, $pSSOURL, $pReason, $pRef)
    {
        return self::createDataStoreRecord($pPrivateDataArray, $pOriginURL, $pSSOURL, TPX_AUTHENTICATIONTYPE_SSO, $pReason, $pRef, false);
    }

    static function createAuthenticationDataRecord($pPrivateDataArray, $pOriginURL, $pType, $pReason)
    {
        return self::createDataStoreRecord($pPrivateDataArray, $pOriginURL, '', $pType, $pReason, 0, false);
    }

    static function createEmailResetDataRecord($pPrivateDataArray, $pOriginURL, $pType, $pReason, $pRef)
    {
        return self::createDataStoreRecord($pPrivateDataArray, $pOriginURL, '', $pType, $pReason, $pRef, false);
    }

	static function createAssetServiceDataRecord($pPrivateDataArray, $pType, $pReason)
	{
		return self::createDataStoreRecord($pPrivateDataArray, '', '', $pType, $pReason, '', true);
	}

    static function createDataStoreRecord($pPrivateDataArray, $pOriginURL, $pSSOURL, $pType, $pReason, $pRef, $pJSON)
    {
    	$returnArray = array('result' => '', 'resultparam' => '', 'authkey' => '');

    	$authKey = hash("sha256", uniqid());

		if ($pJSON === true)
		{
			$serialPrivateData = json_encode($pPrivateDataArray);
		}
		else
		{
			$serialPrivateData = serialize($pPrivateDataArray);
		}

		$dbObj = DatabaseObj::getGlobalDBConnection();

		if ($dbObj)
		{
			// insert a record containing the token data
			if ($stmt = $dbObj->prepare('INSERT INTO `AUTHENTICATIONDATASTORE` (`datecreated`, `expiredate`, `key`, `originurl`, `ssourl`, `data`, `type`, `reason`, `ref`)
											VALUES (NOW(), DATE_ADD(NOW(), INTERVAL 2 DAY), ?, ?, ?, ?, ?, ?, ?)'))
			{
				if ($stmt->bind_param('ssssiii', $authKey, $pOriginURL, $pSSOURL, $serialPrivateData, $pType, $pReason, $pRef))
				{
					if (! $stmt->execute())
					{
						// could not execute the statement
						$returnArray['result'] = 'str_DatabaseError';
						$returnArray['resultparam'] = 'Unable to execute - Insert into authenticationdatastore';
					}
				}
				else
				{
					// could not bind the parameters
					$returnArray['result'] = 'str_DatabaseError';
					$returnArray['resultparam'] = 'Unable to bind parameters - Insert into authenticationdatastore';
				}

				$stmt->free_result();
				$stmt->close();
				$stmt = null;
			}
			else
			{
				// could not prepare the statement
				$returnArray['result'] = 'str_DatabaseError';
				$returnArray['resultparam'] = 'Unable to prepare statement - Insert into authenticationdatastore';
			}

			$dbObj->close();
		}
		else
		{
			// could not open a database connection
			$returnArray['result'] = 'str_DatabaseError';
			$returnArray['resultparam'] = 'Unable to get database - Insert into authenticationdatastore';
		}

		if ($returnArray['result'] == '')
		{
			$returnArray['authkey'] = $authKey;
		}

		return $returnArray;
    }

    static function getSSODataRecord($pAuthKey)
    {
        return self::getAuthenticationDataRecord(TPX_AUTHENTICATIONTYPE_SSO, $pAuthKey, false);
    }

	static function updateAuthenticationRecordData($pKey, $pPrivateDataArray)
    {
		$returnArray = array('result' => '', 'resultparam' => '');

		$privateData = serialize($pPrivateDataArray);

        $dbObj = DatabaseObj::getGlobalDBConnection();

        if ($dbObj)
        {
			$sql = 'UPDATE `AUTHENTICATIONDATASTORE` SET `data`=? where `key`=?';

            if ($stmt = $dbObj->prepare($sql))
            {
                if ($stmt)
                {
                    if ($stmt->bind_param('ss', $privateData, $pKey))
                    {
                        if (! $stmt->execute())
                        {
                            // could not execute the statement
                            $returnArray['result'] = 'str_DatabaseError';
                            $returnArray['resultparam'] = 'execute';
                        }
                    }
                    else
                    {
                        // could not bind the parameters
                        $returnArray['result'] = 'str_DatabaseError';
                        $returnArray['resultparam'] = 'bind';
                    }

                    $stmt->free_result();
                    $stmt->close();
                    $stmt = null;
                }
            }
            else
            {
                // could not prepare the statement
                $returnArray['result'] = 'str_DatabaseError';
                $returnArray['resultparam'] = 'prepare';
            }
        }

        return $returnArray;
    }

    static function updateSSODataRecord($pOldAuthKey, $pNewKey, $pOriginURL)
    {
		$returnArray = array('result' => '', 'resultparam' => '');

        $dbObj = DatabaseObj::getGlobalDBConnection();

        if ($dbObj)
        {
            // construct the Update SQL based on if the URL has been provided
            // the url is not provided when this function is called from the from sso cookie script for
            // normal sso actions (not high level)
            $sql = 'UPDATE `AUTHENTICATIONDATASTORE` SET';

            if ($pOriginURL != '')
            {
                $sql .= ' `originurl` = ?,';
			}

            $sql .= ' `key` = ? WHERE `key` = ?';

            if ($stmt = $dbObj->prepare($sql))
            {
                if ($stmt)
                {
                    $bindOK = false;

                    // make sure we bind the correct paramters based on if the URL has been passed in
                    if ($pOriginURL != '')
                    {
						$bindOK = $stmt->bind_param('sss', $pOriginURL, $pNewKey, $pOldAuthKey);
                    }
                    else
                    {
						$bindOK = $stmt->bind_param('ss', $pNewKey, $pOldAuthKey);
                    }

                    if ($bindOK)
                    {
                        if (! $stmt->execute())
                        {
                            // could not execute the statement
                            $returnArray['result'] = 'str_DatabaseError';
                            $returnArray['resultparam'] = 'execute';
                        }
                    }
                    else
                    {
                        // could not bind the parameters
                        $returnArray['result'] = 'str_DatabaseError';
                        $returnArray['resultparam'] = 'bind';
                    }

                    $stmt->free_result();
                    $stmt->close();
                    $stmt = null;
                }
            }
            else
            {
                // could not prepare the statement
                $returnArray['result'] = 'str_DatabaseError';
                $returnArray['resultparam'] = 'prepare';
            }
        }

        return $returnArray;
    }

    static function getAuthenticationDataRecord($pType, $pAuthKey, $pExpectJSONData)
    {
    	$returnArray = array('result' => '', 'resultparam' => '', 'originurl' => '', 'ssourl' => '', 'data' => array(), 'reason' => '', 'ref' => 0);

    	$privateData = '';
    	$originURL = '';
        $ssoURL = '';
    	$reason = -1;
		$found = false;
		$ref = 0;
		$data = array();

		$dbObj = DatabaseObj::getGlobalDBConnection();

		if ($dbObj)
		{
			if ($stmt = $dbObj->prepare('SELECT `originurl`, `ssourl`, `data`, `reason`, `ref` FROM `AUTHENTICATIONDATASTORE` WHERE `type` = ? AND `key` = ?'))
			{
				if ($stmt)
				{
					if ($stmt->bind_param('is', $pType, $pAuthKey))
					{
						if ($stmt->execute())
						{
							if ($stmt->store_result())
							{
								if ($stmt->bind_result($originURL, $ssoURL, $privateData, $reason, $ref))
								{
									if ($stmt->fetch())
									{
                                        $found = true;

										if ($pExpectJSONData === true)
										{
											$data = json_decode($privateData, true);
										}
										else
										{
											$data = unserialize($privateData);
										}
									}
								}
							}
						}
						else
						{
							// could not execute the statement
							$returnArray['result'] = 'str_DatabaseError';
							$returnArray['resultparam'] = 'execute';
						}
					}
					else
					{
						// could not bind the parameters
						$returnArray['result'] = 'str_DatabaseError';
						$returnArray['resultparam'] = 'bind';
					}

					$stmt->free_result();
					$stmt->close();
					$stmt = null;
				}
			}
			else
			{
				// could not prepare the statement
				$returnArray['result'] = 'str_DatabaseError';
				$returnArray['resultparam'] = 'prepare';
			}
		}

        if (! $found)
        {
            $originURL = '';
            $ssoURL = '';
			$reason = -1;
			$ref = 0;
        }

		$returnArray['originurl'] = $originURL;
        $returnArray['ssourl'] = $ssoURL;
		$returnArray['reason'] = $reason;
		$returnArray['found'] = $found;
		$returnArray['ref'] = $ref;
		$returnArray['data'] = $data;

		return $returnArray;
    }


    static function updateAuthenticationDataRecord($pAuthKey, $pAuthData, $pJSON)
    {
    	$returnArray = array('result' => '', 'resultparam' => '');

		if ($pJSON === true)
		{
			$serialPrivateData = json_encode($pAuthData);
		}
		else
		{
			$serialPrivateData = serialize($pAuthData);
		}

		$dbObj = DatabaseObj::getGlobalDBConnection();

		if ($dbObj)
		{
			if ($stmt = $dbObj->prepare('UPDATE `AUTHENTICATIONDATASTORE` SET `data` = ? WHERE `key` = ?'))
			{
				if ($stmt)
				{
					if ($stmt->bind_param('ss', $serialPrivateData, $pAuthKey))
					{
						if (! $stmt->execute())
						{
							// could not execute the statement
							$returnArray['result'] = 'str_DatabaseError';
							$returnArray['resultparam'] = '';
						}
					}
					else
					{
						// could not bind the parameters
						$returnArray['result'] = 'str_DatabaseError';
						$returnArray['resultparam'] = '';
					}

					$stmt->free_result();
					$stmt->close();
					$stmt = null;
				}
			}
			else
			{
				// could not prepare the statement
				$returnArray['result'] = 'str_DatabaseError';
				$returnArray['resultparam'] = '';
			}
		}

		return $returnArray;
	}

	/**
	 * Get the record ID for the passed token and type
	 *
	 * @param string $pToken The token to retrieve the ID for
	 * @param int $pType The type stored in the database for the record
	 * @return int The record ID
	 */
	static function getAuthenticationDataStoreRecordID($pToken, $pType)
	{
		$returnArray = UtilsObj::getReturnArray('id');
		$error = '';
		$errorParam = '';
		$id = -1;

		$dbObj = DatabaseObj::getGlobalDBConnection();

		if ($dbObj)
		{
			$stmt = $dbObj->prepare('SELECT `id` from `AUTHENTICATIONDATASTORE` WHERE `key` = ? AND `type` = ? LIMIT 1');
			if ($stmt)
			{
				$bindOK = $stmt->bind_param('si', $pToken, $pType);
				if ($bindOK)
				{
					if ($stmt->execute())
					{
						if ($stmt->store_result())
						{
							if ($stmt->bind_result($id))
							{
								$stmt->fetch();
							}
							else
							{
								// could not bind the result
								$error = 'str_DatabaseError';
								$errorParam = __FUNCTION__ . ' bindresult';
							}
						}
						else
						{
							// could not store the result
							$error = 'str_DatabaseError';
							$errorParam = __FUNCTION__ . ' storeresult';
						}
					}
					else
					{
						// could not execute the statement
						$error = 'str_DatabaseError';
						$errorParam = __FUNCTION__ . ' execute';
					}
				}
				else
				{
					// could not bind params
					$error = 'str_DatabaseError';
					$errorParam = __FUNCTION__ . ' bindparams';
				}
			}
			else
			{
				// could not prepare
				$error = 'str_DatabaseError';
				$errorParam = __FUNCTION__ . ' prepare';
			}
		}
		else
		{
			// could not bind params
			$error = 'str_DatabaseError';
			$errorParam = __FUNCTION__ . ' connect';
		}

		$returnArray['error'] = $error;
		$returnArray['errorparam'] = $errorParam;
		$returnArray['id'] = $id;

		return $returnArray;
	}

    static function deleteAuthenticationDataRecordsForUser($pUserId)
    {
    	$returnArray = array('result' => '', 'resultparam' => '');

    	$dbObj = DatabaseObj::getGlobalDBConnection();

		$sql = 'DELETE FROM `AUTHENTICATIONDATASTORE` WHERE `expiredate` < NOW() OR (`ref` = ? AND `reason` = ?)';
		$bindOK = true;

		if ($dbObj)
		{
			// clean up the current sso data and any data which has expired
			if ($stmt = $dbObj->prepare($sql))
			{
				$reasonType = TPX_USER_AUTH_REASON_EMAIL_UPDATE;
                $bindOK = $stmt->bind_param('ii', $pUserId, $reasonType);

				if ($bindOK)
				{
					if (! $stmt->execute())
					{
						// could not execute the statement
						$returnArrray['result'] = 'str_DatabaseError';
						$returnArrray['resultparam'] = 'Unable to execute - Delete from authenticationdatastore';
					}
				}
				else
				{
					// could not bind the parameters
					$returnArrray['result'] = 'str_DatabaseError';
					$returnArrray['resultparam'] = 'Unable to bind parameters - Delete from authenticationdatastore';
				}

				$stmt->free_result();
				$stmt->close();
				$stmt = null;
			}
			else
			{
				// could not prepare the statement
				$returnArrray['result'] = 'str_DatabaseError';
				$returnArrray['resultparam'] = 'Unable to prepare statement - Delete from authenticationdatastore';
			}

			$dbObj->close();
		}
		else
		{
			// could not open a database connection
			$returnArrray['result'] = 'str_DatabaseError';
			$returnArrray['resultparam'] = 'Unable to get database - Delete from authenticationdatastore';
		}

		return $returnArray;
    }

    static function deleteAuthenticationDataRecords($pAuthKey = '')
    {
    	$returnArray = array('result' => '', 'resultparam' => '');

    	$dbObj = DatabaseObj::getGlobalDBConnection();

		$sql = 'DELETE FROM `AUTHENTICATIONDATASTORE` WHERE `expiredate` < NOW()';
		$bindOK = true;

		if ($pAuthKey != '')
		{
			$sql .= ' OR `key` = ?';
		}

		if ($dbObj)
		{
			// clean up the current sso data and any data which has expired
			if ($stmt = $dbObj->prepare($sql))
			{
                if ($pAuthKey != '')
                {
                    $bindOK = $stmt->bind_param('s', $pAuthKey);
                }

				if ($bindOK)
				{
					if (! $stmt->execute())
					{
						// could not execute the statement
						$returnArrray['result'] = 'str_DatabaseError';
						$returnArrray['resultparam'] = 'Unable to execute - Delete from authenticationdatastore';
					}
				}
				else
				{
					// could not bind the parameters
					$returnArrray['result'] = 'str_DatabaseError';
					$returnArrray['resultparam'] = 'Unable to bind parameters - Delete from authenticationdatastore';
				}

				$stmt->free_result();
				$stmt->close();
				$stmt = null;
			}
			else
			{
				// could not prepare the statement
				$returnArrray['result'] = 'str_DatabaseError';
				$returnArrray['resultparam'] = 'Unable to prepare statement - Delete from authenticationdatastore';
			}

			$dbObj->close();
		}
		else
		{
			// could not open a database connection
			$returnArrray['result'] = 'str_DatabaseError';
			$returnArrray['resultparam'] = 'Unable to get database - Delete from authenticationdatastore';
		}

		return $returnArray;
    }

    static function authenticateHighLevelUserAction($pType, $pReason, $pURLDataArray)
    {
		global $gSession;
        global $ac_config;

		// to prevent highlevelSigninDisplay, highlevelRegisterDisplay and highlevelMyAccountDisplay URL's from
		// being hijacked we need to make sure that the token and token value on the URL matches what is stored in the database.
		$authenticationPassed = false;
		$lookForCheckPointCookie = false;
		$authKey = $pURLDataArray['mawebhlottk'];

		// first lookup the database record based of the token in the URL (authKey)
    	$authDataArray = self::getAuthenticationDataRecord($pType, $authKey, false);

		// check to make sure that there is a record with the token that matches the URL.
		// we also check to make sure that the URL action i.e signInDisplay matches the database records reason.
    	if (($authDataArray['result'] == '') && (count($authDataArray['data']) > 0) && ($authDataArray['reason'] == $pReason))
    	{
			$systemConfigArray = DatabaseObj::getSystemConfig();

			$origTokenValue = $authDataArray['data']['mawebhlottvorig'];
			$tokenValue = $authDataArray['data']['mawebhlottv'];
			$tokenRecordBasketRef = $authDataArray['data']['mawebhlbr'];
			$URLTokenValue = $pURLDataArray['mawebhlottv'];
			$URLBasketRef = $pURLDataArray['mawebhlbr'];

			// we now check to make sure that the token value in the database matches the token value on the URL
			if ($tokenValue == $URLTokenValue)
			{
				// we now check to see if the token value in the database record matches the origtoken value in the database.
				// this should only match when the URL is first invoked as we update the database record token value later.
				// this means that the token value will only change once and also means that trying to hack by predicting the new value will also fail
				if ($tokenValue == $origTokenValue)
				{
					// if the token value matches the origtoken then we create a new token value
					$newTokenValue = strtolower(UtilsObj::createRandomString(10));

					// update token value
					$authDataArray['data']['mawebhlottv'] = $newTokenValue;

					// update the token value in the database record using the new token value created above.
					$updateAuthResult = self::updateAuthenticationDataRecord($authKey, $authDataArray['data'], false);

					if ($updateAuthResult['result'] == '')
					{
						$authenticationPassed = true;

						// if we have successfully updated the token value then we must create a checkpoint cookie (mawebhlcp). This should only be created if the URL action is
						// signInDisplay or registerDisplay
						if (($pReason == TPX_USER_AUTH_REASON_HIGHLEVEL_SIGNINDISPLAY) || ($pReason == TPX_USER_AUTH_REASON_HIGHLEVEL_REGISTERDISPLAY))
						{
							$encryptedTokenValue = UtilsObj::encryptData($newTokenValue, $systemConfigArray['systemkey'], false);
							setcookie('mawebhlcp' . hash('sha512', $authKey), $encryptedTokenValue, 0, '/', '', UtilsObj::needSecureCookies(), true);
						}
					}
				}
			}
			else
			{
				// if the token value in the database record does not match the token value on the URL
				// then we know this URL has already been used and either the mawebhlcp cookie for signInDiplay
				// or registerDisplay has been created.
				// For the myAccountDisplay reason, the mawebdata cookie should have been created so we can look for this as the user should have a valid session.
				$lookForCheckPointCookie = true;
			}
    	}


    	// if the url has already been used then it is because:
    	// a) the owner has refreshed the page
    	// b) a hijacker is attempting to steal the url after it has already been used
    	// to prevent this we now rely on the owner having a cookie from their first successful use of the url
    	if ($lookForCheckPointCookie)
    	{
    		// check to see if the token value on the URL matches the orig value in the database record.
    		// if they dont match then we know the URL has been tampered with.
    		if ($URLTokenValue == $origTokenValue)
    		{
    			// if the URL action is signInDisplay or RegisterDisplay then we must check to see if the checkpoint cookie (mawebhlcp) exists for the authkey that is on the URL
    			// otherwise the action is to display the user's account so we rely on the session cookie being present
    			if (($pReason == TPX_USER_AUTH_REASON_HIGHLEVEL_SIGNINDISPLAY) || ($pReason == TPX_USER_AUTH_REASON_HIGHLEVEL_REGISTERDISPLAY))
				{
					if (isset($_COOKIE['mawebhlcp' .  hash('sha512', $authKey)]))
					{
						$encryptedCookieTokenValue = $_COOKIE['mawebhlcp' .  hash('sha512', $authKey)];
						$decryptedCookieTokenValue = UtilsObj::decryptData($encryptedCookieTokenValue, $systemConfigArray['systemkey'], false);

						// test to make sure that the token value in the database record matches the unencrypted value in the cookie.
						if ($tokenValue == $decryptedCookieTokenValue)
						{
							// as a precaution check to make sure that the basketref on the URL mataches the
							// basketref in the database record.
							if ($URLBasketRef == $tokenRecordBasketRef)
							{
								$authenticationPassed = true;
							}
						}
					}
				}
				else
				{
					// this check relates to the myAccountDispay URL action. As the user should already be logged in
					// we check for the mawebdata cookie which should tie up with the users session ref.
					// check to see if we have a valid session cookie
					$securityID = -1;

					// perform the same session cookie test as WebSessionActive()
					if (isset($_COOKIE['mawebdata' . $gSession['ref']]))
					{
						$securityID = $_COOKIE['mawebdata' . $gSession['ref']];
					}

					if ($securityID == md5(base64_encode($gSession['ref'])))
					{
						// the security check passed so we have a valid session cookie
						$authenticationPassed = true;
					}
					else
					{
						// the security check did not pass
						// forget about the session we loaded so that any further code thinks there was never a session
						$gSession = self::createSessionDataArray();
					}
				}
    		}
    	}

    	return $authenticationPassed;
    }

	static function clearOnlineBasketSessionIDWithReason($pBasketRef, $pReason)
	{
		$result = '';
		$reason = $pReason . '_';

		$dbObj = DatabaseObj::getGlobalDBConnection();

        if ($dbObj)
        {
            if ($stmt = $dbObj->prepare('UPDATE `ONLINEBASKET` SET `basketref` = CONCAT(?, `basketref`), `sessionid` = 0 WHERE `basketref` = ?'))
            {
                if ($stmt->bind_param('ss', $reason, $pBasketRef))
                {
                    if (!$stmt->execute())
                    {
                    	$result = __FUNCTION__ . ' update basketsessionid execute: ' . $dbObj->error;
                    }
                }
                else
                {
                	$result = __FUNCTION__ . '  update basketsessionid bind: ' . $dbObj->error;
                }

                $stmt->free_result();
                $stmt->close();
                $stmt = null;
            }
            else
            {
            	$result = __FUNCTION__ . '  update basketsessionid prepare: ' . $dbObj->error;
            }

           	 $dbObj->close();
        }

        return $result;
	}

	static function checkIfOnlineBasketRequiresAuthenticatedUser($pBasketRef)
	{
		// return the session expiration status
        $resultArray = Array();

        $result = '';
        $resultParam = '';

		$onlineBasketSessionID = 0;
        $sessionID = 0;
        $isSessionActive = 0;
        $isSessionEnabled = 0;
        $sessionExpire = 0;
        $serverTime = 0;
        $userID = 0;
        $userName = '';
        $authenticatedStatus = TPX_ONLINE_BASKETAPI_AUTHENTICATEDUSERSTATUS_SIGNEDINUSEREXPIRED;
        $login = '';
		$tempSessionID = -1;

		if (($pBasketRef != '') && ($pBasketRef != TPX_ONLINE_BASKETAPI_DEFAULTBASKETREF))
        {
			$dbObj = DatabaseObj::getGlobalDBConnection();

			// we need to see if records in the ONLINEBASKET table with sessions assigned to them still have session records in the SESSIONDATA table.
			// if there are records returned then we need to check to see if the session is still valid.
			// if no records are returned from then we know this user is a guest.

			$sql = 'SELECT `ol`.`sessionid`, IFNULL(`sd`.`id`,0), `sd`.`sessionactive`, `sd`.`sessionenabled`, `sd`.`sessionexpiredate`, now(), `sd`.`userid`
					FROM `ONLINEBASKET` `ol`
					LEFT JOIN `SESSIONDATA` `sd`
					ON `sd`.`id` = `ol`.`sessionid`
					WHERE (`ol`.`basketref` = ?) AND ((`ol`.`sessionid` > 0) OR (`ol`.`userid` > 0))';

			$bindParamAttr = array('s', $pBasketRef);

			if ($dbObj)
			{
				if ($stmt = $dbObj->prepare($sql))
				{
					$bindOK = call_user_func_array(array(&$stmt, 'bind_param'), UtilsObj::makeValuesReferenced($bindParamAttr));

					if ($bindOK)
					{
						if ($stmt->execute())
						{
							if ($stmt->store_result())
							{
								if ($stmt->num_rows > 0)
								{
									if ($stmt->bind_result($onlineBasketSessionID, $sessionID, $isSessionActive, $isSessionEnabled, $sessionExpire, $serverTime, $userID))
									{
										while ($stmt->fetch())
										{
											// check to see if the online basket record sessionid matches that of the session
											if ($onlineBasketSessionID == $sessionID)
											{
												if ($tempSessionID == -1)
												{
													$tempSessionID = $sessionID;
												}

												if ($tempSessionID == $sessionID)
												{
													if ($isSessionEnabled == 1)
													{
														if ($isSessionActive == 1)
														{
															if ($sessionExpire != '')
															{
																$sessionExpire = strtotime($sessionExpire);
																$serverTime = strtotime($serverTime);

																// we have an active session so determine its expiration status
																if ($serverTime <= $sessionExpire)
																{
																	// the session is active

																	// set the status but don't break out as we are going to test all rows in the result set
																	// (this will catch if for some reason a basket has been linked to more than one session)
																	$authenticatedStatus = TPX_ONLINE_BASKETAPI_AUTHENTICATEDUSERSTATUS_SIGNEDINUSER;
																}
																else
																{
																	// the session has expired
																	$authenticatedStatus = TPX_ONLINE_BASKETAPI_AUTHENTICATEDUSERSTATUS_SIGNEDINUSEREXPIRED;
																	break;
																}
															}
														}
														else
														{
															// the session is not active
															$authenticatedStatus = TPX_ONLINE_BASKETAPI_AUTHENTICATEDUSERSTATUS_SIGNEDINUSEREXPIRED;
															break;
														}
													}
													else
													{
														// the session has been disabled for user interaction (ie it has been completed)
														$authenticatedStatus = TPX_ONLINE_BASKETAPI_AUTHENTICATEDUSERSTATUS_SIGNEDINUSEREXPIRED;
														break;
													}
												}
												else
												{
													// an error has occurred as the session ids do not match. Some how a user has been assigned multiple sessions.
        											$authenticatedStatus = TPX_ONLINE_BASKETAPI_AUTHENTICATEDUSERSTATUS_SIGNEDINUSEREXPIRED;
													break;
												}
											}
											else
											{
												// an error has occurred as the session ids do not match.
												$authenticatedStatus = TPX_ONLINE_BASKETAPI_AUTHENTICATEDUSERSTATUS_SIGNEDINUSEREXPIRED;
												break;
											}
										}
									}
									else
									{
										// could not bind result
										$result = 'str_DatabaseError';
										$resultParam = 'checkIfOnlineBasketRequiresAuthenticatedUser bind result' . $dbObj->error;
									}
								}
								else
								{
									// no rows found - the session doesn't exist in the database treat the user as a guest
									$authenticatedStatus = TPX_ONLINE_BASKETAPI_AUTHENTICATEDUSERSTATUS_GUEST;
								}
							}
							else
							{
								// could not store result
								$result = 'str_DatabaseError';
								$resultParam = 'checkIfOnlineBasketRequiresAuthenticatedUser store result ' . $dbObj->error;
							}
						}
						else
						{
							$result = 'str_DatabaseError';
							$resultParam = 'checkIfOnlineBasketRequiresAuthenticatedUser execute ' . $dbObj->error;
						}
					}
					else
					{
						// could not bind parameters
						$result = 'str_DatabaseError';
						$resultParam = 'checkIfOnlineBasketRequiresAuthenticatedUser bind params ' . $dbObj->error;
					}

					$stmt->free_result();
					$stmt->close();
					$stmt = null;
				}
				else
				{
					// could not prepare statement
					$result = 'str_DatabaseError';
					$resultParam = 'checkIfOnlineBasketRequiresAuthenticatedUser prepare ' . $dbObj->error;
				}

				$dbObj->close();
			}

			if ($authenticatedStatus == TPX_ONLINE_BASKETAPI_AUTHENTICATEDUSERSTATUS_SIGNEDINUSER)
			{
				// update the session expire time
				DatabaseObj::updateSessionExpire($sessionID);

				$userAccountArray = DatabaseObj::getUserAccountFromID($userID);
				$userName = $userAccountArray['contactfirstname'] . ' ' . $userAccountArray['contactlastname'];
				$login = $userAccountArray['login'];
			}
			else
			{
				// if the sesion is inactive, expired or the is no session record returned from the session data table then we need to
				// expire the basketrefs by prefixing them with EXPIRED_ as well as resetting the sessionid in the ONLINEBASKET table to 0.
				if ($authenticatedStatus == TPX_ONLINE_BASKETAPI_AUTHENTICATEDUSERSTATUS_SIGNEDINUSEREXPIRED)
				{
					$clearOnlineBasketSessionIDResult = AuthenticateObj::clearOnlineBasketSessionIDWithReason($pBasketRef, TPX_ONLINE_BASKETAPI_INVALIDATEBASKETREFREASON_EXPIRED);
				}
			}
		}

		$resultArray['result'] = $result;
		$resultArray['resultparam'] = $resultParam;
		$resultArray['sessionid'] = $sessionID;
       	$resultArray['authenticatedstatus'] = $authenticatedStatus;
        $resultArray['userid'] = $userID;
        $resultArray['username'] = $userName;
        $resultArray['login'] = $login;

        return $resultArray;

	}

	static function generatePasswordHash($pPassword, $pPasswordFormat)
	{
		$returnArray = array('result' => '', 'resultparam' => '', 'data' => '');

		// generate the password hash
		$hashResult = password_hash($pPassword, PASSWORD_DEFAULT);

		if (($pPasswordFormat == TPX_PASSWORDFORMAT_MD5) && ($hashResult))
		{
			// prepend + to make the password our "md5+" format
			// this means the string passed into password_hash() was a md5 hash
			$hashResult = '+' . $hashResult;
		}

		if (! $hashResult)
		{
			$returnArray['result'] = 'str_Error';
			$returnArray['resultparam'] = 'Password hashing failed';
		}
		else
		{
			$returnArray['data'] = $hashResult;
		}

		return $returnArray;
	}

	static function checkPasswordNeedsRehash($pPassword)
	{
		// checks if the hashing algorithm used by php has changed so the hash needs refreshing
		return password_needs_rehash($pPassword, PASSWORD_DEFAULT);
	}

	static function rehashUserPassword($pPassword, $pUserID, $pPasswordFormat)
	{
		$returnArray = array('result' => '', 'resultparam' => '');

		// calculate the password hash depending on if the page was secure or not
		$passwordHashResult = self::generatePasswordHash($pPassword, $pPasswordFormat);

		if ($passwordHashResult['result'] == '')
		{
			// update user's password with the new password hash
			$updateUserPasswordResult = self::updateUserPassword($passwordHashResult['data'], $pUserID);

			if ($updateUserPasswordResult['result'] != '')
			{
				$returnArray['result'] = $updateUserPasswordResult['result'];
				$returnArray['resultparam'] = $updateUserPasswordResult['resultparam'];
			}
		}
		else
		{
			$returnArray['result'] = $passwordHashResult['result'];
			$returnArray['resultparam'] = $passwordHashResult['resultparam'];
		}

		return $returnArray;
	}

	static function updateUserPassword($pPassword, $pUserID)
	{
		$returnArray = array('result' => '', 'resultparam' => '');

    	$dbObj = DatabaseObj::getGlobalDBConnection();

		if ($dbObj)
		{
			if ($stmt = $dbObj->prepare('UPDATE `USERS` SET `password` = ? WHERE `id` = ?'))
			{
				if ($stmt)
				{
					if ($stmt->bind_param('si', $pPassword, $pUserID))
					{
						if (! $stmt->execute())
						{
							// could not execute the statement
							$returnArray['result'] = 'str_DatabaseError';
							$returnArray['resultparam'] = __FUNCTION__ . ' execute failed ' . $dbObj->error;
						}
					}
					else
					{
						$returnArray['result'] = 'str_DatabaseError';
						$returnArray['resultparam'] = __FUNCTION__ . ' bind_param failed ' . $dbObj->error;
					}
				}

				$stmt->close();
				$stmt = null;
			}
			else
			{
				// could not open a database connection
				$returnArrray['result'] = 'str_DatabaseError';
				$returnArrray['resultparam'] = __FUNCTION__ .  ' unable to get database ' . $dbObj->error;
			}

			$dbObj->close();
		}

		return $returnArray;
	}

	static function verifyPassword($pPlainTextPassword, $pPassword, $pPasswordFormat)
	{
		$returnArray = UtilsObj::getReturnArray();
		$passwordValid = false;
		$convertPasswordHash = false;
		$verifyPasswordHash = false;

		// check the password from the database is hashed using md5, md5+ or php password api
		$hashFirstCharacter = substr($pPassword, 0, 1);

		// calculate the md5 hashed password depending on if the page was secure or not
		if ($pPasswordFormat == TPX_PASSWORDFORMAT_CLEARTEXT)
		{
			switch ($hashFirstCharacter)
			{
				case '$': // php
				{
					$passwordValid = password_verify($pPlainTextPassword, $pPassword);

					if ($passwordValid)
					{
						$verifyPasswordHash = true;
					}
					break;
				}
				case '+': // md5+
				{
					$passwordMD5Hash = hash('md5', $pPlainTextPassword);
					$passwordValid = password_verify($passwordMD5Hash, substr($pPassword, 1));

					if ($passwordValid)
					{
						$convertPasswordHash = true;
					}
					break;
				}
				default: // md5
				{
					$passwordMD5Hash = hash('md5', $pPlainTextPassword);
					$passwordValid = ($passwordMD5Hash == $pPassword);

					if ($passwordValid)
					{
						$convertPasswordHash = true;
					}
					break;
				}
			}
		}
		else
		{
			// password will already be sent as a md5 hash
			// we cannot verify php password hashes from an md5 hash (plaintext passwords only)
			switch ($hashFirstCharacter)
			{
				case '+': // md5+
				{
					$passwordValid = password_verify($pPlainTextPassword, substr($pPassword, 1));
					$verifyPasswordHash = true;
					break;
				}
				default: // md5
				{
					$passwordValid = ($pPassword == $pPlainTextPassword);
					$convertPasswordHash = true;
					break;
				}
			}
		}

		$returnArray['data']['hashfirstcharacter'] = $hashFirstCharacter; // first character in the hash $ = php, + = md5+, other = md5
		$returnArray['data']['passwordvalid'] = $passwordValid; // password is valid
		$returnArray['data']['convertpasswordhash'] = $convertPasswordHash; // does the password need upgrading?
		$returnArray['data']['verifypasswordhash'] = $verifyPasswordHash; // does the password need to be ran through password_verify
		return $returnArray;
	}

	/**
	 * Function that adds the event to send the email letting the user know their account has been locked.
	 *
	 * @param int $pUserID ID of the user we are sending the locked account email to.
	 * @param int $pLockTime Duration of the account lock in mins.
	 */
	static function sendLockedAccountEmail($pUserID, $pLockTime)
	{
		// Only include the email class if it has not been included already
		if (! class_exists('TaopixMailer'))
		{
			require_once('../Utils/UtilsEmail.php');
		}

		$user = DatabaseObj::getUserAccountFromID($pUserID);
		$brandSettings = DatabaseObj::getBrandingFromCode($user['webbrandcode']);

		// If the user account is not a customer (admin account) and the email address is blank, send email to the admin address for the brand.
		if (($user['iscustomer'] == 0) && ($user['emailaddress'] == ''))
		{
			$user['emailaddress'] = $brandSettings['smtpadminaddress'];
		}

		$emailObj = new TaopixMailer();
		$emailObj->sendTemplateEmail('account_locked', $user['webbrandcode'], $brandSettings['applicationname'],
							$brandSettings['displayurl'], UtilsObj::getBrowserLocale(), $user['contactfirstname'] . ' ' . $user['contactlastname'], $user['emailaddress'], '', '', $pUserID,
							array("loginname" => $user['login'],
								"lockduration" => $pLockTime
								),
							'', ''
				);
	}

	/**
	 * Checks if we are able to send an email letting the user know their account has been locked.
	 * We should only send the email when the number of requests is a multiple of maxloginattempts.
	 *
	 * @param int $pLoginAttempts Number of login attempts the user has made.
	 * @return boolean returns true if we should send the email to the user notifying them that their account is locked.
	 */
	static function canSendLockedAccountEmail($pLoginAttempts)
	{
		global $gConstants;

		$canSend = ($pLoginAttempts % $gConstants['maxloginattempts'] == 0);

		return $canSend;
	}

	/**
	 * Lookup an IP Address is in the USERSBLOCKEDIPADDRESSLIST table.
	 *
	 * @param string $pIPAddress IPv4 or IPv6 IP Address to lookup.
	 * @return array id will be 0 if not found, else the id of the record.
	 */
	static function lookupBlockedIPAddress($pIPAddress)
	{
		$returnArray = UtilsObj::getReturnArray();
		$returnArray['data'] = array('id' => 0, 'canlogin' => true, 'nextvalidlogindate' => '0000-00-00 00:00:00', 'blockreason' => '');
		$id = 0;
		$nextValidLogindate = '0000-00-00 00:00:00';
		$canLogin = true;
		$blockReason = TPX_BLOCK_REASON_NONE;

    	$dbObj = DatabaseObj::getGlobalDBConnection();

		if ($dbObj)
		{
			$sql = 'SELECT `id`, `nextvalidlogindate`, (`nextvalidlogindate` < UTC_TIMESTAMP) as `canlogin`, `blockreason`
				FROM
					`USERSBLOCKEDIPADDRESSLIST`
				WHERE
					`ipaddress` = ?';

			if (($stmt = $dbObj->prepare($sql)))
			{
				$IPAddress = inet_pton($pIPAddress);

				if ($stmt->bind_param('s', $IPAddress))
				{
					if ($stmt->execute())
					{
						if ($stmt->store_result())
						{
							if ($stmt->num_rows == 1)
							{
								if ($stmt->bind_result($id, $nextValidLogindate, $canLogin, $blockReason))
								{
									if ($stmt->fetch())
									{
										$blockedIPArray = array();
										$blockedIPArray['id'] = $id;
										$blockedIPArray['nextvalidlogindate'] = $nextValidLogindate;
										$blockedIPArray['canlogin'] = ($canLogin == 1) ? true : false;
										$blockedIPArray['blockreason'] = $blockReason;
										$returnArray['data'] = $blockedIPArray;
									}
									else
									{

										$returnArray['result'] = 'str_DatabaseError';
										$returnArray['resultparam'] = __FUNCTION__ . ' fetch failed ' . $dbObj->error;
									}
								}
								else
								{
									$returnArray['result'] = 'str_DatabaseError';
									$returnArray['resultparam'] = __FUNCTION__ . ' bind_result failed ' . $dbObj->error;
								}
							}
						}
						else
						{
							$returnArray['result'] = 'str_DatabaseError';
							$returnArray['resultparam'] = __FUNCTION__ . ' store_result failed ' . $dbObj->error;
						}
					}
					else
					{
						$returnArray['result'] = 'str_DatabaseError';
						$returnArray['resultparam'] = __FUNCTION__ . ' execute failed ' . $dbObj->error;
					}
				}
				else
				{
					$returnArray['result'] = 'str_DatabaseError';
					$returnArray['resultparam'] = __FUNCTION__ . ' bind_param failed ' . $dbObj->error;
				}

				$stmt->free_result();
				$stmt->close();
				$stmt = null;
			}
			else
			{
				$returnArrray['result'] = 'str_DatabaseError';
				$returnArrray['resultparam'] = __FUNCTION__ .  ' prepare failed ' . $dbObj->error;
			}

			$dbObj->close();
		}
		else
		{
			// Could not open a database connection.
			$returnArrray['result'] = 'str_DatabaseError';
			$returnArrray['resultparam'] = __FUNCTION__ .  ' unable to get database ' . $dbObj->error;
		}

		return $returnArray;
	}

	/**
	 * Inserts a new record in the USERSBLOCKEDIPADDRESSLIST table.
	 *
	 * @param string $pIPAddress IPv4 or IPv6 IP address in human readable form.
	 * @param boolean $pRateLimit True if this is a rate limit or false if a block.
	 * @return array Returns the id and nextvalidlogindate of the new record.
	 */
	static function insertBlockedIPAddress($pIPAddress, $pRateLimit)
	{
		$returnArray = UtilsObj::getReturnArray('data');
		$returnArray['data'] = array();
		$id = 0;
		$nextValidLoginDate = '0000-00-00 00:00:00';
		$blockReason = TPX_BLOCK_REASON_IP_BLOCK;

    	$dbObj = DatabaseObj::getGlobalDBConnection();

		if ($dbObj)
		{
			// Convert IP to packed version to insert.
			$ipAddressPacked = inet_pton($pIPAddress);
			$nextValidLoginDateOffset = self::getNextValidLoginDateOffset($pRateLimit);

			// Set the block reason.
			if ($pRateLimit)
			{
				$blockReason = TPX_BLOCK_REASON_RATE_LIMIT;
			}

			$sql = 'INSERT INTO `USERSBLOCKEDIPADDRESSLIST` (`ipaddressraw`, `ipaddress`, `nextvalidlogindate`, `blockcount`, `blockreason`) VALUES (?, ?, DATE_ADD(UTC_TIMESTAMP, INTERVAL ? SECOND), 1, ?)';

			if (($stmt = $dbObj->prepare($sql)))
			{
				if ($stmt->bind_param('sssi', $pIPAddress, $ipAddressPacked, $nextValidLoginDateOffset, $blockReason))
				{
					if ($stmt->execute())
					{
						$id = $stmt->insert_id;

						$getNextValidLoginDateResult = self::getNextValidLoginDate($id);

						if ($getNextValidLoginDateResult['error'] == '')
						{
							$nextValidLoginDate = $getNextValidLoginDateResult['nextvalidlogindate'];
						}
						else
						{
							$returnArray['error'] = $getNextValidLoginDateResult['error'];
							$returnArray['errorparam'] = $getNextValidLoginDateResult['errorparam'];
						}
					}
					else
					{
						$returnArray['error'] = 'str_DatabaseError';
						$returnArray['errorparam'] = __FUNCTION__ . ' execute failed ' . $dbObj->error;
					}
				}
				else
				{
					$returnArray['error'] = 'str_DatabaseError';
					$returnArray['errorparam'] = __FUNCTION__ . ' bind_param failed ' . $dbObj->error;
				}

				$stmt->close();
				$stmt = null;
			}
			else
			{
				$returnArrray['error'] = 'str_DatabaseError';
				$returnArrray['errorparam'] = __FUNCTION__ .  ' prepare failed ' . $dbObj->error;
			}

			$dbObj->close();
		}
		else
		{
			// Could not open a database connection.
			$returnArrray['error'] = 'str_DatabaseError';
			$returnArrray['errorparam'] = __FUNCTION__ .  ' unable to get database ' . $dbObj->error;
		}

		$returnArray['data']['id'] = $id;
		$returnArray['data']['nextvalidlogindate'] = $nextValidLoginDate;
		return $returnArray;
	}

	/**
	 * Updates an existing record in the USERSBLOCKEDIPADDRESSLIST with a new nextvalidlogindate and increment the blockcount.
	 *
	 * @param int $pID ID of the record in the USERSBLOCKEDIPADDRESSLIST table.
	 * @param boolean $pRateLimit True if this is a rate limit or false if a block.
	 * @return array Contains next valid login date.
	 */
	static function updateBlockedIPAddress($pID, $pRateLimit)
	{
		$returnArray = UtilsObj::getReturnArray('nextvalidlogindate');
		$blockReason = TPX_BLOCK_REASON_IP_BLOCK;

    	$dbObj = DatabaseObj::getGlobalDBConnection();

		if ($dbObj)
		{
			$nextValidLoginDateOffset = self::getNextValidLoginDateOffset($pRateLimit);

			// Set the block reason.
			if ($pRateLimit)
			{
				$blockReason = TPX_BLOCK_REASON_RATE_LIMIT;
			}

			$sql = 'UPDATE `USERSBLOCKEDIPADDRESSLIST` SET `nextvalidlogindate` = DATE_ADD(UTC_TIMESTAMP, INTERVAL ? SECOND), `blockcount` = `blockcount` + 1, `blockreason` = ?  WHERE `id` = ?';

			if (($stmt = $dbObj->prepare($sql)))
			{
				if ($stmt->bind_param('sii', $nextValidLoginDateOffset, $blockReason, $pID))
				{
					if ($stmt->execute())
					{
						$getNextValidLoginDateResult = self::getNextValidLoginDate($pID);

						if ($getNextValidLoginDateResult['error'] == '')
						{
							$returnArray['nextvalidlogindate'] = $getNextValidLoginDateResult['nextvalidlogindate'];
						}
						else
						{
							$returnArray['error'] = $getNextValidLoginDateResult['error'];
							$returnArray['errorparam'] = $getNextValidLoginDateResult['errorparam'];
						}
					}
					else
					{
						$returnArray['error'] = 'str_DatabaseError';
						$returnArray['errorparam'] = __FUNCTION__ . ' execute failed ' . $dbObj->error;
					}
				}
				else
				{
					$returnArray['error'] = 'str_DatabaseError';
					$returnArray['errorparam'] = __FUNCTION__ . ' bind_param failed ' . $dbObj->error;
				}

				$stmt->close();
				$stmt = null;
			}
			else
			{
				$returnArrray['error'] = 'str_DatabaseError';
				$returnArrray['errorparam'] = __FUNCTION__ .  ' prepare failed ' . $dbObj->error;
			}

			$dbObj->close();
		}
		else
		{
			// Could not open a database connection.
			$returnArrray['error'] = 'str_DatabaseError';
			$returnArrray['errorparam'] = __FUNCTION__ .  ' unable to get database ' . $dbObj->error;
		}

		return $returnArray;
	}

	/**
	 * Get failed logins based on Ip Address.
	 *
	 * @param string $pIPAddress IPv4 or IPv6 human readable IP address.
	 * @return array {@see AuthenticateObj::getFailedLoginAttempts()}
	 */
	static function getFailedLoginAttemptsForIPAddress($pIPAddress)
	{
		return self::getFailedLoginAttempts($pIPAddress, '');
	}

	/**
	 * Get failed logins based on user login.
	 *
	 * @param string $pUserLogin User login of the use to check failed login attempts for.
	 * @return array {@see AuthenticateObj::getFailedLoginAttempts()}
	 */
	static function getFailedLoginAttemptsForUserLogin($pUserLogin)
	{
		return self::getFailedLoginAttempts('', $pUserLogin);
	}

	/**
	 * Checks the number of failed login attempts within the defined period from the Activity Log table.
	 *
	 * @param string $pIPAddress IPv4 or IPv6 IP address in human readable format to lookup on if passed.
	 * @param string $pUserLogin The userlogin to lookup up if passed.
	 * @return array Contains the number of failed logins.
	 */
	static function getFailedLoginAttempts($pIPAddress, $pUserLogin)
	{
		global $gConstants;

		$returnArray = UtilsObj::getReturnArray('failedloginscount');
		$failedLoginsCount = 0;

		$dbObj = DatabaseObj::getGlobalDBConnection();

		if ($dbObj)
		{
			// Set up the bind parameters array, adding the first parameter, which is the number of minutes to use to check for failed login attempts.
			// If checking for failed attempts from an IP address, use the Value configured in Control Centre.
			// If not checking based on IP address, use TPX_AUTHENTICATION_FAILED_LOGINS_MINUTES.
			$bindParamsArray = array('s');
			$bindParamsArray[] = ($pIPAddress == '') ? TPX_AUTHENTICATION_FAILED_LOGINS_MINUTES : $gConstants['maxiploginattemptsminutes'];

			// Generate the SQL to read the ACTIVITYLOG.
			$sql = "SELECT COUNT(*)
				FROM
					`ACTIVITYLOG`
				WHERE
					`datecreated` >= DATE_SUB(NOW(), INTERVAL ? MINUTE)
				AND
					`actioncode` = 'LOGIN'
				AND
					`actionnotes` = 'str_ErrorNoAccount'
				AND
					`success` = 0";

			// Add the IP address to the query and param array.
			if ($pIPAddress != '')
			{
				$bindParamsArray[0] .= 's';
				$bindParamsArray[] = inet_pton($pIPAddress);

				$sql .= " AND
					`remoteipaddress` = ?";
			}

			// Add the user id to the query and param array.
			if ($pUserLogin != '')
			{
				$bindParamsArray[0] .= 's';
				$bindParamsArray[] = $pUserLogin;

				$sql .= " AND
					`userlogin` = ?";
			}

			if (($stmt = $dbObj->prepare($sql)))
			{
				if (call_user_func_array(array(&$stmt, 'bind_param'), UtilsObj::makeValuesReferenced($bindParamsArray)))
				{
					if ($stmt->execute())
					{
						if ($stmt->store_result())
						{
							if ($stmt->bind_result($failedLoginsCount))
							{
								if (! $stmt->fetch())
								{
									$returnArray['error'] = 'str_DatabaseError';
									$returnArray['errorparam'] = __FUNCTION__ . ' fetch failed ' . $dbObj->error;
								}
							}
							else
							{
								$returnArray['error'] = 'str_DatabaseError';
								$returnArray['errorparam'] = __FUNCTION__ . ' bind_result failed ' . $dbObj->error;
							}
						}
						else
						{
							$returnArray['error'] = 'str_DatabaseError';
							$returnArray['errorparam'] = __FUNCTION__ . ' store_result failed ' . $dbObj->error;
						}
					}
					else
					{
						$returnArray['error'] = 'str_DatabaseError';
						$returnArray['errorparam'] = __FUNCTION__ . ' execute failed ' . $dbObj->error;
					}
				}
				else
				{
					$returnArray['error'] = 'str_DatabaseError';
					$returnArray['errorparam'] = __FUNCTION__ . ' bind_param failed ' . $dbObj->error;
				}

				$stmt->free_result();
				$stmt->close();
				$stmt = null;
			}
			else
			{
				$returnArrray['error'] = 'str_DatabaseError';
				$returnArrray['errorparam'] = __FUNCTION__ .  ' prepare failed ' . $dbObj->error;
			}

			$dbObj->close();
		}
		else
		{
			// Could not open a database connection.
			$returnArrray['error'] = 'str_DatabaseError';
			$returnArrray['errorparam'] = __FUNCTION__ .  ' unable to get database ' . $dbObj->error;
		}

		$returnArray['failedloginscount'] = $failedLoginsCount;
		return $returnArray;
	}

	/**
	 * Calculates the nextvalidlogindate value based if this is a rate limit or a block.
	 * If it is a block, use the value defined in Control Centre. If rate limited, use the constant value.
	 *
	 * @global array $gConstants Array containing constants values.
	 * @global array $ac_config Array containing config values.
	 * @param boolean $pRateLimit True if this is a rate limit, false if it's a block.
	 * @return string The new nextvalidlogindate value in seconds.
	 */
	static function getNextValidLoginDateOffset($pRateLimit)
	{
		global $gConstants;
		global $ac_config;

		// Account is rate limited on a failed login attempt.
		$lockTimeOut = (isset($ac_config['RATELIMITLOCKOUTTIME'])) ? $ac_config['RATELIMITLOCKOUTTIME'] : TPX_AUTHENTICATION_REPEAT_TRIES;

		// The account has been blocked due to too many failed login attempts.
		if (! $pRateLimit)
		{
			$lockTimeOut = ($gConstants['accountlockouttime'] * 60);
		}

		return $lockTimeOut;
	}

	/**
	 * Gets the nextvalidlogindate from the USERSBLOCKEDIPADDRESSLIST record to make sure it is the same format.
	 *
	 * @param int $pID ID of the record to get the nextvalidlogindate from.
	 * @return string nextvalidlogindate in YYYY-DD-MM HH:ii:ss format.
	 */
	static function getNextValidLoginDate($pID)
	{
		$returnArray = UtilsObj::getReturnArray('nextvalidlogindate');
		$nextValidLoginDate = '0000-00-00 00:00:00';

		$dbObj = DatabaseObj::getGlobalDBConnection();

		if ($dbObj)
		{
			$sql = "SELECT `nextvalidlogindate`
				FROM
					`USERSBLOCKEDIPADDRESSLIST`
				WHERE
					`id` = ?";

			if (($stmt = $dbObj->prepare($sql)))
			{
				if ($stmt->bind_param('i', $pID))
				{
					if ($stmt->execute())
					{
						if ($stmt->bind_result($nextValidLoginDate))
						{
							if (! $stmt->fetch())
							{
								$returnArray['error'] = 'str_DatabaseError';
								$returnArray['errorparam'] = __FUNCTION__ . ' fetch failed ' . $dbObj->error;
							}
						}
						else
						{
							$returnArray['error'] = 'str_DatabaseError';
							$returnArray['errorparam'] = __FUNCTION__ . ' bind_result failed ' . $dbObj->error;
						}
					}
					else
					{
						$returnArray['error'] = 'str_DatabaseError';
						$returnArray['errorparam'] = __FUNCTION__ . ' execute failed ' . $dbObj->error;
					}
				}
				else
				{
					$returnArray['error'] = 'str_DatabaseError';
					$returnArray['errorparam'] = __FUNCTION__ . ' bind_param failed ' . $dbObj->error;
				}

				$stmt->free_result();
				$stmt->close();
				$stmt = null;
			}
			else
			{
				$returnArrray['error'] = 'str_DatabaseError';
				$returnArrray['errorparam'] = __FUNCTION__ .  ' prepare failed. Error: ' . $dbObj->error;
			}
		}
		else
		{
			// Could not open a database connection.
			$returnArrray['error'] = 'str_DatabaseError';
			$returnArrray['errorparam'] = __FUNCTION__ .  ' unable to get database ' . $dbObj->error;
		}

		$returnArray['nextvalidlogindate'] = $nextValidLoginDate;
		return $returnArray;
	}

	/**
	 * Returns the correct string code based on the reason the login was blocked.
	 *
	 * @param int $pReason The reason code for why the login was blocked.
	 * @return string The string code.
	 */
	static function getBlockedLoginReasonString($pReason)
	{
		$blockReason = '';

		switch ($pReason)
		{
			case TPX_BLOCK_REASON_RATE_LIMIT:
			{
				$blockReason = 'str_ErrorThereWasAProblemWithYourRequest';
				break;
			}
			case TPX_BLOCK_REASON_IP_BLOCK:
			{
				$blockReason = 'str_ErrorIPBlockedDueToSuspiciousActivity';
				break;
			}
		}

		return $blockReason;
    }

	/**
     * Add an event to send an email with a list of accounts sharing an email address.
	 *
	 * @param string $pBrandCode Brand code.
	 * @param string $pEmail destination email address.
	 * @param array $pAccountArray accounts using the email address.
	 */
	static function sendExistingAccountsEmail($pBrandCode, $pEmail, $pAccountArray)
	{
		// Include the email class.
		require_once('../Utils/UtilsEmail.php');

        // Get the brand settings for the brand used.
        $brandSettings = DatabaseObj::getBrandingFromCode($pBrandCode);

        // Generate and send the email.
        $emailObj = new TaopixMailer();
        $emailObj->sendTemplateEmail('customer_existingaccounts', $pBrandCode, $brandSettings['applicationname'],
                                $brandSettings['displayurl'], '', '', $pEmail, '', '', $pAccountArray[0]['id'],
                                array("usernames" => $pAccountArray),
                                '', '');
	}

	/**
	 * Gets a count of the number of email change requests that a user has made.
	 *
	 * @param int $pUserId User id we are checking
	 * @return array Returns array containing error, errorMessage, and data array with a single key pendingemailupdates
	 * containing the number of email address updates pending that the user has.
	 */
	static function hasOutstandingEmailChange($pUserId)
	{
		$returnArray = UtilsObj::getReturnArray();
		$dbObj = DatabaseObj::getGlobalDBConnection();
		$updateCount = 0;

		if ($dbObj)
		{
			// Get any valid email change request for the passed user.
			$query = "SELECT count(id) FROM `AUTHENTICATIONDATASTORE` WHERE `reason`=? AND `ref`=? AND `expiredate` > NOW()";

			if ($stmt = $dbObj->prepare($query))
			{
				$reasonCode = TPX_USER_AUTH_REASON_EMAIL_UPDATE;
				if ($stmt->bind_param('ii', $reasonCode, $pUserId))
				{
					if ($stmt->execute())
					{
						if ($stmt->bind_result($updateCount))
						{
							if (! $stmt->fetch())
							{
								$returnArray['error'] = 'str_DatabaseError';
								$returnArray['errorparam'] = __FUNCTION__ . ' fetch failed ' . $dbObj->error;
							}
							else
							{
								$returnArray['data']['pendingemailupdates'] = $updateCount;
							}
						}
						else
						{
							$returnArray['error'] = 'str_DatabaseError';
							$returnArray['errorparam'] = __FUNCTION__ . ' bind_result failed ' . $dbObj->error;
						}
					}
					else
					{
						$returnArray['error'] = 'str_DatabaseError';
						$returnArray['errorparam'] = __FUNCTION__ . ' execute failed ' . $dbObj->error;
					}
				}
				else
				{
					$returnArray['error'] = 'str_DatabaseError';
					$returnArray['errorparam'] = __FUNCTION__ . ' bind_param failed ' . $dbObj->error;
				}

				$stmt->free_result();
				$stmt->close();
				$stmt = null;
			}
			else
			{
					$returnArray['error'] = 'str_DatabaseError';
					$returnArray['errorparam'] = __FUNCTION__ . ' prepare failed ' . $dbObj->error;
			}
		}
		else
		{
				$returnArray['error'] = 'str_DatabaseError';
				$returnArray['errorparam'] = __FUNCTION__ . ' unable to get db ' . $dbObj->error;
		}

		return $returnArray;
	}
}
?>
