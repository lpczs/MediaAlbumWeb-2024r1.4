<?php

function encodeField($pField)
{
    return $pField . '<eof>';
}


function ob_logstdout($pSourceString)
{
    return strlen($pSourceString) . ' ' . gzcompress($pSourceString, 9);
}

class AppProductionAPI_view
{
    /**
   	* Echo's the login response back to the calling application
   	*
   	* @static
	*
	* @param array $pResultArray
   	*
   	* @author Kevin Gale
	* @since Version 3.0.0
 	*/
    static function login($pResultArray)
	{
	    global $ac_config;
	    global $gConstants;

    	ob_start('ob_logstdout');

    	$smarty = SmartyObj::newSmarty('AppAPI');

    	echo encodeField('OK');
        echo encodeField('13');
        echo '<eol>';

        echo encodeField($pResultArray['result']);
        echo encodeField($pResultArray['ref']);
        echo encodeField($pResultArray['sessionkey']);
        echo encodeField($pResultArray['systemcertificate']);
        echo encodeField($gConstants['config']);
        echo encodeField($ac_config['SERVERLOCATION']);
    	echo encodeField($pResultArray['id']);
        echo encodeField($pResultArray['username']);
        echo encodeField($pResultArray['sitecode']);
        echo encodeField($pResultArray['sitekey']);
        echo encodeField($pResultArray['licensedata1']);
        echo encodeField($pResultArray['licensedata2']);
        echo encodeField($ac_config['WEBBRANDFOLDERNAME']);
        echo encodeField($smarty->get_config_vars('str_LanguageList'));

        echo '<eol>';
	}


    /**
   	* Echo's the logout response back to the calling application
   	*
   	* @static
	*
   	* @author Kevin Gale
	* @since Version 3.0.0
 	*/
    static function logout()
    {
    	ob_start('ob_logstdout');

    	echo encodeField('OK');
        echo encodeField('1');
        echo '<eol>';
    }


    static function getProductionSites($pResultArray)
    {
    	ob_start('ob_logstdout');

    	echo encodeField('OK');
        echo encodeField('1');
        echo '<eol>';

    	$itemCount = count($pResultArray);
    	for ($i = 0; $i < $itemCount; $i++)
    	{
    	    $item = $pResultArray[$i];
    	    echo encodeField($item['id']);
    	    echo encodeField($item['code']);
    	    echo encodeField($item['key']);
    	    echo encodeField($item['name']);
    	    echo encodeField($item['isactive']);
            echo '<eol>';
        }
    }


    /**
   	* Echo's the production queue, output device status and output format status back to the calling application
   	*
   	* @static
	*
	* @param array $pResultArray
   	*
   	* @author Kevin Gale
	* @since Version 1.0.0
 	*/
	static function getProductionQueue($pResultArray)
	{
		global $ac_config;

    	ob_start('ob_logstdout');

    	echo encodeField('OK');
        echo encodeField('1');
        echo '<eol>';

    	echo encodeField($pResultArray['servertime']);
    	echo '<eol>';

    	echo encodeField($pResultArray['licensedata1']);
    	echo '<eol>';
    	echo encodeField($pResultArray['licensedata2']);
    	echo '<eol>';

    	$itemList = $pResultArray['queuelist'];
    	$itemCount = count($itemList);

        if ($pResultArray['queueretrieved'] == true)
        {
            if (($pResultArray['queuenewitems'] == true) || ($pResultArray['queuecount'] != $itemCount))
            {
                echo encodeField($itemCount);
                echo '<eol>';

                for ($i = 0; $i < $itemCount; $i++)
                {
                    $item = $itemList[$i];
                    if ($item['orderid'] == '<eol>')
                    {
                        echo $item['id'];
                    }
                    else
                    {
                        echo encodeField($item['id']);
                        echo encodeField($item['orderid']);
                        echo encodeField($item['shoppingcarttype']);
                        echo encodeField($item['userid']);
                        echo encodeField($item['orderlinecount']);
                        echo encodeField($item['orderlinenumber']);
                        echo encodeField($item['projectname']);
                        echo encodeField($item['productcode']);
                        echo encodeField($item['productname']);
                        echo encodeField($item['productheight']);
                        echo encodeField($item['productwidth']);
                        echo encodeField($item['covercode']);
                        echo encodeField($item['covername']);
                        echo encodeField($item['papercode']);
                        echo encodeField($item['papername']);
                        echo encodeField($item['productoutputformatcode']);
                        echo encodeField($item['productoutputformatname']);
                        echo encodeField($item['convertoutputformatcode']);
                        echo encodeField($item['convertoutputformatname']);
                        echo encodeField($item['qty']);
                        echo encodeField($item['uploaddatatype']);
                        echo encodeField($item['uploadmethod']);
                        echo encodeField($item['uploadgroupcode']);
                        echo encodeField($item['uploadordernumber']);
                        echo encodeField($item['uploadref']);
                        echo encodeField($item['filesreceivedtimestamp']);
                        echo encodeField($item['decryptfilesreceivedtimestamp']);
                        echo encodeField($item['jobticketoutputfilename']);
                        echo encodeField($item['pagesoutputfilename']);
                        echo encodeField($item['cover1outputfilename']);
                        echo encodeField($item['cover2outputfilename']);
                        echo encodeField($item['xmloutputfilename']);
                        echo encodeField($item['jobticketoutputdevicecode']);
                        echo encodeField($item['pagesoutputdevicecode']);
                        echo encodeField($item['cover1outputdevicecode']);
                        echo encodeField($item['cover2outputdevicecode']);
                        echo encodeField($item['xmloutputdevicecode']);
                        echo encodeField($item['outputcount']);
                        echo encodeField($item['onhold']);
                        echo encodeField($item['onholdreason']);
                        echo encodeField($item['status']);
                        echo encodeField($item['statusdescription']);
                        echo encodeField($item['orderdate']);
                        echo encodeField($item['ordernumber']);
                        echo encodeField($item['temporder']);
                        echo encodeField($item['temporderexpirydate']);
                        echo encodeField($item['offlineorder']);
                        echo encodeField($item['sessionid']);
                        echo encodeField($item['paymentreceived']);
                        echo encodeField($item['orderstatus']);
                        echo encodeField($item['groupcode']);
                        echo encodeField($item['brandcode']);
                        echo encodeField($item['accountcode']);
                        echo encodeField($item['companyname']);
                        echo encodeField($item['contactfirstname']);
                        echo encodeField($item['contactlastname']);
                        echo encodeField($item['currentowner']);
                        echo encodeField($item['ownerorderkey']);
                        echo encodeField($item['storecode']);
                        echo encodeField($item['source']);
                    }
                    echo '<eol>';
                }
            }
            else
            {
                // the production queue hasn't changed
                echo encodeField('-1');
                echo '<eol>';
            }
        }
        else
        {
            // the production queue hasn't changed
            echo encodeField('-1');
            echo '<eol>';
        }


        // return the number of output devices
        echo encodeField($pResultArray['outputdevicecount']);
    	echo '<eol>';


    	// return the number of output devices that have changed since last time
    	echo encodeField($pResultArray['outputdevicechangecount']);
    	echo '<eol>';


    	// return the number of output formats
    	echo encodeField($pResultArray['outputformatcount']);
    	echo '<eol>';


    	// return the number of output formats that have changed
    	echo encodeField($pResultArray['outputformatchangecount']);
    	echo '<eol>';


    	// return the number of paper components
    	echo encodeField($pResultArray['papercount']);
    	echo '<eol>';


    	// return the number of paper components that have changed
    	echo encodeField($pResultArray['paperchangecount']);
    	echo '<eol>';


    	// return the number of brands
    	echo encodeField($pResultArray['brandcount']);
    	echo '<eol>';


    	// return the number of brands that have changed
    	echo encodeField($pResultArray['brandchangecount']);
    	echo '<eol>';


    	// return the system config
    	echo encodeField($pResultArray['serverurl']);
    	echo encodeField($ac_config['SERVERLOCATION']);
    	echo encodeField($ac_config['PROCESSINGSERVERLOCATION']);
    	echo encodeField($ac_config['FTPURL']);
    	echo encodeField($ac_config['FTPUSER']);
    	echo encodeField($ac_config['FTPPASS']);
    	echo encodeField(UtilsObj::correctPath($ac_config['FTPORDERSROOTPATH']));

    	if ($ac_config['FTPGROUPORDERSBYCODE'] == '1')
        {
            echo encodeField('TRUE');
        }
        else
        {
            echo encodeField('FALSE');
        }

    	echo encodeField(UtilsObj::correctPath(UtilsObj::getArrayParam($ac_config, 'TAOPIXONLINEURL')));
        echo '<eol>';
    }


    /**
   	* Echo's the output format data back to the calling application
   	*
   	* @static
	*
	* @param array $pResultArray
   	*
   	* @author Kevin Gale
	* @since Version 3.0.0
 	*/
    static function getOutputFormats($pResultArray)
    {
    	ob_start('ob_logstdout');

    	echo encodeField('OK');
        echo encodeField('1');
        echo '<eol>';

    	$itemList = $pResultArray['outputformatlist'];
    	$itemCount = count($itemList);
    	echo encodeField($itemCount);
        echo '<eol>';

    	for ($i = 0; $i < $itemCount; $i++)
    	{
    	    $item = $itemList[$i];

    	    echo encodeField($item['id']);
    	    echo encodeField($item['code']);
    	    echo encodeField($item['localcode']);
    	    echo encodeField($item['name']);
            echo encodeField($item['pagestype']);
            echo encodeField($item['cover1type']);
            echo encodeField($item['cover2type']);
    	    echo encodeField($item['jobticketoptions']);
			echo encodeField($item['jobticketcolourspace']);
			echo encodeField($item['jobticketcolour']);
    	    echo encodeField($item['leftpageoptions']);
    	    echo encodeField($item['rightpageoptions']);
    	    echo encodeField($item['frontcoveroptions']);
    	    echo encodeField($item['backcoveroptions']);
    	    echo encodeField($item['steppagenumbers']);
    	    echo encodeField($item['leftpagefilenameformat']);
			echo encodeField($item['leftpageslugbarcodeheight']);
    	    echo encodeField($item['rightpagefilenameformat']);
			echo encodeField($item['rightpageslugbarcodeheight']);
    	    echo encodeField($item['iscover1separatefile']);
    	    echo encodeField($item['iscover1atfront']);
    	    echo encodeField($item['cover1filenameformat']);
			echo encodeField($item['cover1slugbarcodeheight']);
    	    echo encodeField($item['iscover2separatefile']);
    	    echo encodeField($item['cover2outputwithcover1']);
    	    echo encodeField($item['cover2filenameformat']);
			echo encodeField($item['cover2slugbarcodeheight']);
    	    echo encodeField($item['isjobticketseparatefile']);
    	    echo encodeField($item['jobticketfilenameformat']);
    	    echo encodeField($item['xmloutputfile']);
    	    echo encodeField($item['xmlfilenameformat']);
    	    echo encodeField($item['jobticketdefaultoutputdevicecode']);
    	    echo encodeField($item['pagesdefaultoutputdevicecode']);
    	    echo encodeField($item['cover1defaultoutputdevicecode']);
    	    echo encodeField($item['cover2defaultoutputdevicecode']);
    	    echo encodeField($item['xmldefaultoutputdevicecode']);
			echo encodeField($item['jobticketsubfoldernameformat']);
            echo encodeField($item['pagessubfoldernameformat']);
            echo encodeField($item['cover1subfoldernameformat']);
            echo encodeField($item['cover2subfoldernameformat']);
            echo encodeField($item['xmlsubfoldernameformat']);
            echo encodeField($item['xmllanguage']);
            echo encodeField($item['xmlincludepaymentdata']);
            echo encodeField($item['xmlbeautified']);
            echo encodeField($item['printersmarkscolourspace']);
            echo encodeField($item['sluginfocolour']);
            echo encodeField($item['cropmarkoffset']);
            echo encodeField($item['cropmarklength']);
            echo encodeField($item['cropmarkwidth']);
            echo encodeField($item['cropmarkborderwidth']);
            echo encodeField($item['cropmarkcolour']);
			echo encodeField($item['foldmarkoffset']);
            echo encodeField($item['foldmarklength']);
            echo encodeField($item['foldmarkwidth']);
            echo encodeField($item['foldmarkborderwidth']);
            echo encodeField($item['foldmarkcolour']);
			echo encodeField($item['foldmarkcentreline']);
			echo encodeField($item['foldmarkoutsidelines']);
			echo encodeField($item['foldmarkshowspinewidth']);
            echo encodeField($item['bleedoverlapwidth']);
            echo '<eol>';
        }

        $itemList = $pResultArray['productlinklist'];
    	$itemCount = count($itemList);
    	echo encodeField($itemCount);
        echo '<eol>';

    	for ($i = 0; $i < $itemCount; $i++)
    	{
    	    $item = $itemList[$i];

    	    echo encodeField($item['outputformatcode']);
    	    echo encodeField($item['productcode']);
    	    echo encodeField($item['componentcode']);
    	    echo '<eol>';
    	}
    }


    /**
   	* Echo's the result of adding a new output format back to the calling application
   	*
   	* @static
	*
	* @param array $pResultArray
   	*
   	* @author Kevin Gale
	* @since Version 3.0.0
 	*/
    static function outputFormatAdd($pResultArray)
    {
    	ob_start('ob_logstdout');

    	echo encodeField('OK');
        echo encodeField('1');
        echo '<eol>';

        $result = $pResultArray['result'];
        if ($result != '')
        {
            $smarty = SmartyObj::newSmarty('AdminOutputFormats', '', '', $pResultArray['langcode']);
            $result = $smarty->get_config_vars($result);
            $result = str_replace('^0', $pResultArray['resultparam'], $result);
        }

        echo encodeField($result);
        echo encodeField($pResultArray['id']);
        echo encodeField($pResultArray['code']);
        echo encodeField($pResultArray['localcode']);
        echo encodeField($pResultArray['name']);
        echo '<eol>';
    }


    /**
   	* Echo's the result of updating an existing output format back to the calling application
   	*
   	* @static
	*
	* @param array $pResultArray
   	*
   	* @author Kevin Gale
	* @since Version 3.0.0
 	*/
    static function outputFormatEdit($pResultArray)
    {
    	ob_start('ob_logstdout');

    	echo encodeField('OK');
        echo encodeField('1');
        echo '<eol>';

        $result = $pResultArray['result'];
        if ($result != '')
        {
            $smarty = SmartyObj::newSmarty('AdminOutputFormats', '', '', $pResultArray['langcode']);
            $result = $smarty->get_config_vars($result);
            $result = str_replace('^0', $pResultArray['resultparam'], $result);
        }

        echo encodeField($result);
        echo encodeField($pResultArray['id']);
        echo encodeField($pResultArray['code']);
        echo encodeField($pResultArray['localcode']);
        echo encodeField($pResultArray['name']);
        echo '<eol>';
    }


    /**
   	* Echo's the result of deleting an existing output format back to the calling application
   	*
   	* @static
	*
	* @param array $pResultArray
   	*
   	* @author Kevin Gale
	* @since Version 3.0.0
 	*/
    static function outputFormatDelete($pResultArray)
    {
    	ob_start('ob_logstdout');

    	echo encodeField('OK');
        echo encodeField('1');
        echo '<eol>';

         $result = $pResultArray['result'];
        if ($result != '')
        {
            $smarty = SmartyObj::newSmarty('AdminOutputFormats', '', '', $pResultArray['langcode']);
            $result = $smarty->get_config_vars($result);
            $result = str_replace('^0', $pResultArray['resultparam'], $result);
        }

        echo encodeField($result);
        echo '<eol>';
    }


    /**
   	* Echo's the output device data back to the calling application
   	*
   	* @static
	*
	* @param array $pResultArray
   	*
   	* @author Kevin Gale
	* @since Version 1.0.0
 	*/
    static function getOutputDevices($pResultArray)
	{
    	ob_start('ob_logstdout');

    	echo encodeField('OK');
        echo encodeField('1');
        echo '<eol>';

    	$itemCount = count($pResultArray);
    	for ($i = 0; $i < $itemCount; $i++)
    	{
    	    $item = $pResultArray[$i];
    	    echo encodeField($item['id']);
    	    echo encodeField($item['owner']);
    	    echo encodeField($item['code']);
    	    echo encodeField($item['localcode']);
    	    echo encodeField($item['name']);
    	    echo encodeField($item['type']);
    	    echo encodeField($item['epwaccountdetails']);
    	    echo encodeField($item['epwurl']);
    	    echo encodeField($item['epwurlversion']);
    	    echo encodeField($item['epwworkflowcode']);
    	    echo encodeField($item['epwworkflowname']);
    	    echo encodeField($item['epwworkflowcompletionstatus']);
    	    echo encodeField($item['pathmac']);
    	    echo encodeField($item['pathwin']);
    	    echo encodeField($item['pathserver']);
    	    echo encodeField($item['copyfiles']);
			echo encodeField($item['additionalsettings']);
    	    echo encodeField($item['isactive']);
            echo '<eol>';
        }
	}


    /**
   	* Echo's the result of adding a new output device back to the calling application
   	*
   	* @static
	*
	* @param array $pResultArray
   	*
   	* @author Kevin Gale
	* @since Version 1.0.0
 	*/
    static function outputDeviceAdd($pResultArray)
    {
    	ob_start('ob_logstdout');

    	echo encodeField('OK');
        echo encodeField('1');
        echo '<eol>';

        $result = $pResultArray['result'];
        if ($result != '')
        {
            $smarty = SmartyObj::newSmarty('AdminOutputDevices', '', '', $pResultArray['langcode']);
            $result = $smarty->get_config_vars($result);
            $result = str_replace('^0', $pResultArray['resultparam'], $result);
        }

        echo encodeField($result);
        echo encodeField($pResultArray['id']);
        echo '<eol>';
    }


    /**
   	* Echo's the result of updating an existing output device back to the calling application
   	*
   	* @static
	*
	* @param array $pResultArray
   	*
   	* @author Kevin Gale
	* @since Version 1.0.0
 	*/
    static function outputDeviceEdit($pResultArray)
    {
    	ob_start('ob_logstdout');

    	echo encodeField('OK');
        echo encodeField('1');
        echo '<eol>';

        $result = $pResultArray['result'];
        if ($result != '')
        {
            $smarty = SmartyObj::newSmarty('AdminOutputDevices', '', '', $pResultArray['langcode']);
            $result = $smarty->get_config_vars($result);
            $result = str_replace('^0', $pResultArray['resultparam'], $result);
        }

        echo encodeField($result);
        echo encodeField($pResultArray['id']);
        echo '<eol>';
    }

    /**
   	* Echo's the result of deleting an existing output device back to the calling application
   	*
   	* @static
	*
	* @param array $pResultArray
   	*
   	* @author Kevin Gale
	* @since Version 1.0.0
 	*/
    static function outputDeviceDelete($pResultArray)
    {
    	ob_start('ob_logstdout');

    	echo encodeField('OK');
        echo encodeField('1');
        echo '<eol>';

         $result = $pResultArray['result'];
        if ($result != '')
        {
            $smarty = SmartyObj::newSmarty('AdminOutputDevices', '', '', $pResultArray['langcode']);
            $result = $smarty->get_config_vars($result);
            $result = str_replace('^0', $pResultArray['resultparam'], $result);
        }

        echo encodeField($result);
        echo '<eol>';
    }


    /**
   	* Echo's the product list data back to the calling application
   	*
   	* @static
	*
	* @param array $pResultArray
   	*
   	* @author Kevin Gale
	* @since Version 3.0.0
 	*/
    static function getProductsList($pResultArray)
    {
    	ob_start('ob_logstdout');

    	echo encodeField('OK');
        echo encodeField('1');
        echo '<eol>';

        $itemCount = count($pResultArray);
        echo encodeField($itemCount);
        echo '<eol>';

    	for ($i = 0; $i < $itemCount; $i++)
    	{
    	    $item = $pResultArray[$i];
    	    echo encodeField($item['productcode']);
    	    echo encodeField($item['productname']);
    	    echo '<eol>';
    	}
    }

    /**
   	* Echo's the component list data back to the calling application
   	*
   	* @static
	*
	* @param array $pResultArray
   	*
   	* @author Kevin Gale
	* @since Version 3.0.0
 	*/
    static function getComponentsList($pResultArray)
    {
    	ob_start('ob_logstdout');

    	echo encodeField('OK');
        echo encodeField('1');
        echo '<eol>';

        $itemCount = count($pResultArray);
    	for ($i = 0; $i < $itemCount; $i++)
    	{
    	    $item = $pResultArray[$i];
    	    echo encodeField($item['id']);
    	    echo encodeField($item['code']);
    	    echo encodeField($item['localcode']);
    	    echo encodeField($item['name']);
    	    echo encodeField($item['active']);
    	    echo '<eol>';
    	}
    }


	static function getOrderStatusList(&$pResultArray)
	{
    	ob_start('ob_logstdout');

    	// increase the timeout as we could be transmitting a lot of data here
    	UtilsObj::resetPHPScriptTimeout(120);

    	echo encodeField('OK');
        echo encodeField('2');
        echo '<eol>';

    	echo encodeField($pResultArray['webversionnumber']);
        echo '<eol>';

    	$itemArray = &$pResultArray['orders'];
    	$itemCount = count($itemArray);
    	echo encodeField($itemCount);
        echo '<eol>';

    	for ($i = 0; $i < $itemCount; $i++)
    	{
    	    $item = &$itemArray[$i];
    	    echo encodeField($item['id']);
    	    echo encodeField($item['status']);
    	    echo encodeField($item['statustimestamp']);
            echo '<eol>';

            // clear the contents of this entry as it is not needed anymore
            $item = Array();
        }

        $itemArray = &$pResultArray['brands'];
    	$itemCount = count($itemArray);
    	echo encodeField($itemCount);
        echo '<eol>';

    	for ($i = 0; $i < $itemCount; $i++)
    	{
    	    $item = &$itemArray[$i];
    	    echo encodeField($item['code']);
    	    echo encodeField($item['name']);
    	    echo encodeField($item['applicationname']);
    	    echo encodeField($item['displayurl']);
    	    echo encodeField($item['weburl']);
    	    echo encodeField($item['mainwebsiteurl']);
    	    echo encodeField($item['macdownloadurl']);
    	    echo encodeField($item['win32downloadurl']);
            echo '<eol>';
        }
	}

    /**
   	* Echo's the order status data list data back to the calling application
   	*
   	* @static
	*
	* @param array $pResultArray
   	*
   	* @author Kevin Gale
	* @since Version 1.0.0
 	*/
	static function getOrderStatusData($pResultArray)
	{
    	ob_start('ob_logstdout');

    	echo encodeField('OK');
        echo encodeField('13'); // using version 13 of this API
        echo '<eol>';

    	$itemCount = count($pResultArray);
    	for ($i = 0; $i < $itemCount; $i++)
    	{
    	    $item = $pResultArray[$i];
    	    echo encodeField($item['ownercode']);
    	    echo encodeField($item['currentcompanycode']);
    	    echo encodeField($item['currentowner']);
    	    echo encodeField($item['currentownertype']);
    	    echo encodeField($item['orderdate']);
    	    echo encodeField($item['orderid']);
    	    echo encodeField($item['ordernumber']);
    	    echo encodeField($item['orderstatus']);
    	    echo encodeField($item['groupcode']);
    	    echo encodeField($item['userid']);
    	    echo encodeField($item['orderitemid']);
    	    echo encodeField($item['uploadref']);
    	    echo encodeField($item['uploadappversion']);
    	    echo encodeField($item['uploadappplatform']);
    	    echo encodeField($item['uploadappcputype']);
    	    echo encodeField($item['uploadapposversion']);
    	    echo encodeField($item['currencycode']);
    	    echo encodeField($item['currencyname']);
    	    echo encodeField($item['currencyisonumber']);
    	    echo encodeField($item['currencysymbol']);
    	    echo encodeField($item['currencysymbolatfront']);
    	    echo encodeField($item['currencydecimalplaces']);
    	    echo encodeField($item['billingcountrycode']);
    	    echo encodeField($item['shippingcountrycode']);
    	    echo encodeField($item['shippingmethodcode']);
    	    echo encodeField($item['shippingmethodname']);
    	    echo encodeField($item['projectname']);
    	    echo encodeField($item['productcode']);
    	    echo encodeField($item['productname']);
    	    echo encodeField($item['productheight']);
    	    echo encodeField($item['productwidth']);
    	    echo encodeField($item['covercode']);
    	    echo encodeField($item['covername']);
    	    echo encodeField($item['papercode']);
    	    echo encodeField($item['papername']);
    	    echo encodeField($item['pagecount']);
    	    echo encodeField($item['productunitcost']);
    	    echo encodeField($item['productunitsell']);
    	    echo encodeField($item['coverunitcost']);
    	    echo encodeField($item['coverunitsell']);
    	    echo encodeField($item['paperunitcost']);
    	    echo encodeField($item['paperunitsell']);
    	    echo encodeField($item['taxcode']);
    	    echo encodeField($item['taxname']);
    	    echo encodeField($item['taxrate']);
    	    echo encodeField($item['qty']);
    	    echo encodeField($item['producttotalcost']);
    	    echo encodeField($item['producttotalsell']);
    	    echo encodeField($item['covertotalcost']);
    	    echo encodeField($item['covertotalsell']);
    	    echo encodeField($item['papertotalcost']);
    	    echo encodeField($item['papertotalsell']);
    	    echo encodeField($item['subtotal']);
    	    echo encodeField($item['discountvalue']);
    	    echo encodeField($item['totalcost']);
    	    echo encodeField($item['totalsell']);
    	    echo encodeField($item['totaltax']);
    	    echo encodeField($item['outputtimestamp']);
    	    echo encodeField($item['shippedtimestamp']);
    	    echo encodeField($item['shippeddate']);
    	    echo encodeField($item['statustimestamp']);
    	    echo encodeField($item['itemstatus']);
    	    echo encodeField($item['groupdata']);
    	    echo encodeField($item['shoppingcarttype']);
    	    echo encodeField($item['designeruuid']);
    	    echo encodeField($item['browsertype']);
    	    echo encodeField($item['webbrandcode']);
    	    echo encodeField($item['voucherpromotioncode']);
    	    echo encodeField($item['voucherpromotionname']);
    	    echo encodeField($item['vouchercode']);
    	    echo encodeField($item['vouchername']);
    	    echo encodeField($item['voucherdiscountsection']);
    	    echo encodeField($item['voucherdiscountype']);
    	    echo encodeField($item['voucherdiscountvalue']);
    	    echo encodeField($item['itemcount']);
    	    echo encodeField($item['shippingtotalcost']);
    	    echo encodeField($item['shippingtotalsell']);
    	    echo encodeField($item['shippingtotaltax']);
    	    echo encodeField($item['itemnumber']);
    	    echo encodeField($item['shareid']);
    	    echo encodeField($item['origorderitemid']);
    	    echo encodeField($item['projectref']);
    	    echo encodeField($item['collectioncode']);
    	    echo encodeField($item['collectionname']);
    	    echo encodeField($item['producttype']);
    	    echo encodeField($item['productpageformat']);
    	    echo encodeField($item['productspreadformat']);
    	    echo encodeField($item['productcover1format']);
    	    echo encodeField($item['productcover2format']);
    	    echo encodeField($item['productoutputformat']);
    	    echo encodeField($item['orderwebversion']);
    	    echo encodeField($item['vouchertype']);
    	    echo encodeField($item['vouchersellprice']);
    	    echo encodeField($item['voucheragentfee']);
    	    echo encodeField($item['shippingtotalsellbeforediscount']);
    	    echo encodeField($item['shippingdiscountvalue']);
    	    echo encodeField($item['ordertotal']);
    	    echo encodeField($item['ordertotaltax']);
    	    echo encodeField($item['ordertotaldiscount']);
    	    echo encodeField($item['ordergiftcardamount']);
    	    echo encodeField($item['ordertotaltopay']);
    	    echo encodeField($item['orderoffline']);
    	    echo encodeField($item['projectbuildstartdate']);
    	    echo encodeField($item['projectbuildduration']);
    	    echo encodeField($item['uploaddatasize']);
    	    echo encodeField($item['uploadduration']);
    	    echo encodeField($item['uploaddatatype']);
    	    echo encodeField($item['uploadmethod']);
    	    echo encodeField($item['paymentmethodcode']);
    	    echo encodeField($item['paymentgatewaysubcode']);
    	    echo encodeField($item['pricesincludetax']);
    	    echo encodeField($item['discountname']);
    	    echo encodeField($item['source']);
    	    echo encodeField($item['paymentgatewaycode']);
    	    echo encodeField($item['voucherapplicationmethod']);
    	    echo encodeField($item['voucherapplytoqty']);
    	    echo encodeField($item['ordertotalsellbeforediscount']);
    	    echo encodeField($item['ordertotalitemsellwithtax']);
    	    echo encodeField($item['orderfootertotalwithtax']);
    	    echo encodeField($item['orderfootertotalnotax']);
    	    echo encodeField($item['orderfootertotalnotaxnodiscount']);
    	    echo encodeField($item['orderfootertaxratesequal']);
    	    echo encodeField($item['orderfootersubtotal']);
    	    echo encodeField($item['orderfootertotal']);
    	    echo encodeField($item['orderfootertotaltax']);
    	    echo encodeField($item['orderfooterdiscountvalue']);
    	    echo encodeField($item['productcollectionorigownercode']);
			echo encodeField($item['parentorderitemid']);
			echo encodeField($item['orderpricingengineversion']);
			echo encodeField($item['projectlsdata']);
            echo '<eol>';
        }
	}

    static function reRouteItems($pResultArray)
	{
    	ob_start('ob_logstdout');

    	echo encodeField('OK');
        echo encodeField('1');
        echo '<eol>';

        echo encodeField($pResultArray['items']);

        echo '<eol>';
	}

	static function updateOrderPaymentStatus()
	{
    	ob_start('ob_logstdout');

    	echo encodeField('OK');
        echo encodeField('1');
        echo '<eol>';
	}

	static function updateItemFilesReceivedStatus()
	{
    	ob_start('ob_logstdout');

    	echo encodeField('OK');
        echo encodeField('1');
        echo '<eol>';
	}

	static function updateItemCanModifyStatus()
	{
    	ob_start('ob_logstdout');

    	echo encodeField('OK');
        echo encodeField('1');
        echo '<eol>';
	}

	static function updateItemCanUploadFilesStatus()
	{
    	ob_start('ob_logstdout');

    	echo encodeField('OK');
        echo encodeField('1');
        echo '<eol>';
	}

	static function updateItemCanUploadFilesOverrideProductCodeStatus()
	{
    	ob_start('ob_logstdout');

    	echo encodeField('OK');
        echo encodeField('1');
        echo '<eol>';
	}

	static function updateItemCanUploadFilesOverridePageCountStatus()
	{
    	ob_start('ob_logstdout');

    	echo encodeField('OK');
        echo encodeField('1');
        echo '<eol>';
	}

	static function updateItemCanUploadFilesOverrideSaveStatus()
	{
    	ob_start('ob_logstdout');

    	echo encodeField('OK');
        echo encodeField('1');
        echo '<eol>';
	}

    static function updateItemImportStatus()
	{
    	ob_start('ob_logstdout');

    	echo encodeField('OK');
        echo encodeField('1');
        echo '<eol>';
	}

	static function updateItemDecryptQueueStatus($pResult)
	{
		ob_start('ob_logstdout');

		if ($pResult === "")
		{
			echo encodeField('OK');
			echo encodeField('1');
			echo '<eol>';
		}
		else
		{
			echo encodeField('ERROR');
			echo encodeField('1');
			echo encodeField($pResult);
			echo '<eol>';
		}
	}

	static function updateItemDecryptStatus()
	{
    	ob_start('ob_logstdout');

    	echo encodeField('OK');
        echo encodeField('1');
        echo '<eol>';
	}

	static function updateItemConvertStatus()
	{
    	ob_start('ob_logstdout');

    	echo encodeField('OK');
        echo encodeField('1');
        echo '<eol>';
	}

	static function updateItemStatus()
	{
    	ob_start('ob_logstdout');

    	echo encodeField('OK');
        echo encodeField('1');
        echo '<eol>';
	}

    static function updateOrderActiveStatus()
	{
    	ob_start('ob_logstdout');

    	echo encodeField('OK');
        echo encodeField('1');
        echo '<eol>';
	}

    static function updateItemActiveStatus()
	{
    	ob_start('ob_logstdout');

    	echo encodeField('OK');
        echo encodeField('1');
        echo '<eol>';
	}

	static function updateItemOnHoldStatus($pResultArray)
	{
    	ob_start('ob_logstdout');

		if ($pResultArray['error'] === "")
		{
			echo encodeField('OK');
			echo encodeField('1');
		}
		else
		{
			echo encodeField('ERROR');
       		echo encodeField('1');
        	echo encodeField('Update Hold Status Database error');
		}
		echo '<eol>';
		
	}

	static function updateItemOutputStatus($pResultArray)
	{
    	ob_start('ob_logstdout');

    	echo encodeField('OK');
        echo encodeField('1');
        echo '<eol>';

        echo encodeField($pResultArray['dataexport']);
        echo '<eol>';
	}

	static function updateItemFinishingStatus()
	{
    	ob_start('ob_logstdout');

    	echo encodeField('OK');
        echo encodeField('1');
        echo '<eol>';
	}

	static function updateItemShippingStatus()
	{
    	ob_start('ob_logstdout');

    	echo encodeField('OK');
        echo encodeField('1');
        echo '<eol>';
	}

    static function getJobInfo($pResultArray)
	{
    	ob_start('ob_logstdout');

    	echo encodeField('OK');
        echo encodeField('1');
        echo '<eol>';

		// job ticket data
        $jobTicketArray = $pResultArray['jobticket'];

		echo encodeField($jobTicketArray['webbrandcode']);
		echo encodeField($jobTicketArray['applicationname']);
		echo encodeField($jobTicketArray['projectname']);
		echo encodeField($jobTicketArray['productcode']);
		echo encodeField($jobTicketArray['productskucode']);
        echo encodeField($jobTicketArray['productname']);
        echo encodeField($jobTicketArray['producttype']);
        echo encodeField($jobTicketArray['productheight']);
        echo encodeField($jobTicketArray['productwidth']);
        echo encodeField($jobTicketArray['covercode']);
        echo encodeField($jobTicketArray['covername']);
        echo encodeField($jobTicketArray['papercode']);
        echo encodeField($jobTicketArray['papername']);
        echo encodeField($jobTicketArray['pagecountpurchased']);
        echo encodeField($jobTicketArray['pagecount']);
        echo encodeField($jobTicketArray['qty']);
        echo encodeField($jobTicketArray['source']);
        echo encodeField($jobTicketArray['uploaddatatype']);
        echo encodeField($jobTicketArray['uploadmethod']);
        echo encodeField($jobTicketArray['uploadref']);
        echo encodeField($jobTicketArray['uploadappversion']);
        echo encodeField($jobTicketArray['uploadappplatform']);
        echo encodeField($jobTicketArray['vouchercode']);
        echo encodeField($jobTicketArray['vouchername']);
        echo encodeField($jobTicketArray['shippeddate']);
        echo encodeField($jobTicketArray['shippingtrackingreference']);
        echo encodeField($jobTicketArray['groupcode']);
        echo encodeField($jobTicketArray['groupdata']);
        echo encodeField($jobTicketArray['orderdate']);
        echo encodeField($jobTicketArray['ordernumber']);
        echo encodeField($jobTicketArray['currencycode']);
        echo encodeField($jobTicketArray['currencydecimalplaces']);
        echo encodeField($jobTicketArray['ordertotal']);
        echo encodeField($jobTicketArray['formattedordertotal']);
        echo encodeField($jobTicketArray['ordergiftcardtotal']);
        echo encodeField($jobTicketArray['formattedordergiftcardtotal']);
        echo encodeField($jobTicketArray['ordertotaltopay']);
        echo encodeField($jobTicketArray['formattedordertotaltopay']);
        echo encodeField($jobTicketArray['paymentreceived']);
        echo encodeField($jobTicketArray['paymentreceiveddate']);
        echo encodeField($jobTicketArray['billingcustomeraccountcode']);
        echo encodeField($jobTicketArray['billingcustomername']);
        echo encodeField($jobTicketArray['billingcustomeraddress1']);
        echo encodeField($jobTicketArray['billingcustomeraddress2']);
        echo encodeField($jobTicketArray['billingcustomeraddress3']);
        echo encodeField($jobTicketArray['billingcustomeraddress4']);
        echo encodeField($jobTicketArray['billingcustomercity']);
        echo encodeField($jobTicketArray['billingcustomercounty']);
        echo encodeField($jobTicketArray['billingcustomerstate']);
        echo encodeField($jobTicketArray['billingcustomerpostcode']);
        echo encodeField($jobTicketArray['billingcustomercountryname']);
        echo encodeField($jobTicketArray['billingcustomertelephonenumber']);
        echo encodeField($jobTicketArray['billingcustomeremailaddress']);
        echo encodeField($jobTicketArray['billingcontactfirstname']);
        echo encodeField($jobTicketArray['billingcontactlastname']);
        echo encodeField($jobTicketArray['billingcustomerregisteredtaxnumbertype']);
        echo encodeField($jobTicketArray['billingcustomerregisteredtaxnumber']);
        echo encodeField($jobTicketArray['paymentmethodcode']);
        echo encodeField($jobTicketArray['paymentmethodname']);
        echo encodeField($jobTicketArray['shippingid']);
        echo encodeField($jobTicketArray['shippingcustomername']);
        echo encodeField($jobTicketArray['shippingcustomeraddress1']);
        echo encodeField($jobTicketArray['shippingcustomeraddress2']);
        echo encodeField($jobTicketArray['shippingcustomeraddress3']);
        echo encodeField($jobTicketArray['shippingcustomeraddress4']);
        echo encodeField($jobTicketArray['shippingcustomercity']);
        echo encodeField($jobTicketArray['shippingcustomercounty']);
        echo encodeField($jobTicketArray['shippingcustomerstate']);
        echo encodeField($jobTicketArray['shippingcustomerpostcode']);
        echo encodeField($jobTicketArray['shippingcustomercountrycode']);
        echo encodeField($jobTicketArray['shippingcustomercountryname']);
        echo encodeField($jobTicketArray['shippingcustomertelephonenumber']);
        echo encodeField($jobTicketArray['shippingcustomeremailaddress']);
        echo encodeField($jobTicketArray['shippingcontactfirstname']);
        echo encodeField($jobTicketArray['shippingcontactlastname']);
        echo encodeField($jobTicketArray['shippingmethodcode']);
        echo encodeField($jobTicketArray['shippingmethodname']);
        echo encodeField($jobTicketArray['shippingratecost']);
        echo encodeField($jobTicketArray['shippingdistributioncentrecode']);
        echo encodeField($jobTicketArray['shippingdistributioncentrename']);
        echo encodeField($jobTicketArray['shippingstorecode']);
        echo encodeField($jobTicketArray['jobticketfield1name']);
        echo encodeField($jobTicketArray['jobticketfield1value']);
        echo encodeField($jobTicketArray['jobticketfield2name']);
        echo encodeField($jobTicketArray['jobticketfield2value']);
        echo encodeField($jobTicketArray['jobticketfield3name']);
        echo encodeField($jobTicketArray['jobticketfield3value']);
        echo encodeField($jobTicketArray['jobticketfield4name']);
        echo encodeField($jobTicketArray['jobticketfield4value']);
        echo encodeField($jobTicketArray['jobticketfield5name']);
        echo encodeField($jobTicketArray['jobticketfield5value']);
        echo encodeField($jobTicketArray['cciid']);
        echo encodeField($jobTicketArray['ccitype']);
        echo encodeField($jobTicketArray['cciauthorised']);
        echo encodeField($jobTicketArray['ccitransactionid']);
        echo encodeField($jobTicketArray['cciresponsecode']);
        echo encodeField($jobTicketArray['cciresponsedescription']);
        echo encodeField($jobTicketArray['userordercount']);
    	echo encodeField($jobTicketArray['userorderitemcount']);


		// job info data
		$jobInfoArray = $pResultArray['jobinfo'];
		
		echo encodeField($jobInfoArray['paymentreceivedtimestamp']);
        echo encodeField($jobInfoArray['paymentreceivedusername']);
        echo encodeField($jobInfoArray['filesreceivedtimestamp']);
        echo encodeField($jobInfoArray['filesreceivedusername']);
        echo encodeField($jobInfoArray['decrypttimestamp']);
        echo encodeField($jobInfoArray['decryptusername']);
        echo encodeField($jobInfoArray['converttimestamp']);
        echo encodeField($jobInfoArray['convertusername']);
        echo encodeField($jobInfoArray['convertoutputformatcode']);
        echo encodeField($jobInfoArray['convertoutputformatname']);
        echo encodeField($jobInfoArray['productcover1format']);
        echo encodeField($jobInfoArray['productcover2format']);        
        echo encodeField($jobInfoArray['outputtimestamp']);
        echo encodeField($jobInfoArray['outputusername']);
        echo encodeField($jobInfoArray['jobticketoutputdevicename']);
        echo encodeField($jobInfoArray['pagesoutputdevicename']);
        echo encodeField($jobInfoArray['cover1outputdevicename']);
        echo encodeField($jobInfoArray['cover2outputdevicename']);
        echo encodeField($jobInfoArray['xmloutputdevicename']);
        echo encodeField($jobInfoArray['jobticketsubfoldername']);
        echo encodeField($jobInfoArray['pagessubfoldername']);
        echo encodeField($jobInfoArray['cover1subfoldername']);
        echo encodeField($jobInfoArray['cover2subfoldername']);
        echo encodeField($jobInfoArray['xmlsubfoldername']);
        echo encodeField($jobInfoArray['jobticketoutputfilename']);
		echo encodeField($jobInfoArray['pagesoutputfilename']);
		echo encodeField($jobInfoArray['cover1outputfilename']);
		echo encodeField($jobInfoArray['cover2outputfilename']);
		echo encodeField($jobInfoArray['xmloutputfilename']);
        echo encodeField($jobInfoArray['finishtimestamp']);
        echo encodeField($jobInfoArray['finishusername']);
        echo encodeField($jobInfoArray['shippedtimestamp']);
        echo encodeField($jobInfoArray['shippedusername']);
        echo encodeField($jobInfoArray['shippeddistributioncentrereceivedtimestamp']);
        echo encodeField($jobInfoArray['shippeddistributioncentrereceiveddate']);
        echo encodeField($jobInfoArray['shippeddistributioncentrereceivedusername']);
        echo encodeField($jobInfoArray['shippeddistributioncentreshippedtimestamp']);
        echo encodeField($jobInfoArray['shippeddistributioncentreshippeddate']);
        echo encodeField($jobInfoArray['shippeddistributioncentreshippedusername']);
        echo encodeField($jobInfoArray['shippedstorereceivedtimestamp']);
        echo encodeField($jobInfoArray['shippedstorereceiveddate']);
        echo encodeField($jobInfoArray['shippedstorereceivedusername']);
        echo encodeField($jobInfoArray['shippedstorecustomercollectedtimestamp']);
        echo encodeField($jobInfoArray['shippedstorecustomercollecteddate']);
        echo encodeField($jobInfoArray['shippedstorecustomercollectedusername']);
        echo encodeField($jobInfoArray['canmodify']);
        echo encodeField($jobInfoArray['canuploadfiles']);
        echo encodeField($jobInfoArray['canuploadproductcodeoverride']);
    	echo encodeField($jobInfoArray['canuploadpagecountoverride']);
    	echo encodeField($jobInfoArray['canuploadenablesaveoverride']);
    	echo encodeField($jobInfoArray['jobticketepwpartid']);
    	echo encodeField($jobInfoArray['pagesepwpartid']);
    	echo encodeField($jobInfoArray['cover1epwpartid']);
    	echo encodeField($jobInfoArray['cover2epwpartid']);
    	echo encodeField($jobInfoArray['jobticketepwsubmissionid']);
    	echo encodeField($jobInfoArray['pagesepwsubmissionid']);
    	echo encodeField($jobInfoArray['cover1epwsubmissionid']);
    	echo encodeField($jobInfoArray['cover2epwsubmissionid']);
    	echo encodeField($jobInfoArray['jobticketepwcompletionstatus']);
    	echo encodeField($jobInfoArray['pagesepwcompletionstatus']);
    	echo encodeField($jobInfoArray['cover1epwcompletionstatus']);
    	echo encodeField($jobInfoArray['cover2epwcompletionstatus']);
    	echo encodeField($jobInfoArray['jobticketepwstatus']);
    	echo encodeField($jobInfoArray['pagesepwstatus']);
    	echo encodeField($jobInfoArray['cover1epwstatus']);
		echo encodeField($jobInfoArray['cover2epwstatus']);
		echo encodeField($jobInfoArray['projectaimode']);


		// component data
        echo encodeField('COMPONENTS');
        $componentsArray = $pResultArray['components'];
        $count = count($componentsArray);
        echo encodeField($count);
        for ($i = 0; $i < $count; $i++)
		{
		    $componentItemArray = $componentsArray[$i];

		    echo encodeField($componentItemArray['id']);
		    echo encodeField($componentItemArray['orderitemid']);
            echo encodeField($componentItemArray['parentcomponentid']);
            echo encodeField($componentItemArray['assetservicecode']);
            echo encodeField($componentItemArray['assetservicename']);
            echo encodeField($componentItemArray['assetpricetype']);
            echo encodeField($componentItemArray['componentcode']);
            echo encodeField($componentItemArray['componentlocalcode']);
            echo encodeField($componentItemArray['componentskucode']);
            echo encodeField($componentItemArray['componentname']);
            echo encodeField($componentItemArray['componentpath']);
            echo encodeField($componentItemArray['componentcategorycode']);
            echo encodeField($componentItemArray['componentcategoryname']);
            echo encodeField($componentItemArray['componentdescription']);
            echo encodeField($componentItemArray['sortorder']);
            echo encodeField($componentItemArray['pricingmodel']);
            echo encodeField($componentItemArray['islist']);
            echo encodeField($componentItemArray['checkboxselected']);
            echo encodeField($componentItemArray['sectionid']);
            echo encodeField($componentItemArray['quantity']);
            echo encodeField($componentItemArray['componentunitcost']);
            echo encodeField($componentItemArray['componentunitsell']);
            echo encodeField($componentItemArray['assetunitcost']);
            echo encodeField($componentItemArray['assetunitsell']);
            echo encodeField($componentItemArray['componenttotalcost']);
            echo encodeField($componentItemArray['componenttotalsell']);
            echo encodeField($componentItemArray['assetexpirydate']);
            echo encodeField($componentItemArray['assetpageref']);
            echo encodeField($componentItemArray['assetpagenumber']);
            echo encodeField($componentItemArray['assetpagename']);
            echo encodeField($componentItemArray['assetboxref']);
            echo encodeField($componentItemArray['metadatacodelist']);
		}


		// component metadata
        echo encodeField('COMPONENTMETADATA');
        $metaDataArray = $pResultArray['componentmetadata'];
        $count = count($metaDataArray);
        echo encodeField($count);
        for ($i = 0; $i < $count; $i++)
		{
		    $metaDataItemArray = $metaDataArray[$i];

		    echo encodeField($metaDataItemArray['id']);
		    echo encodeField($metaDataItemArray['orderitemcomponentid']);
		    echo encodeField($metaDataItemArray['code']);
		    echo encodeField($metaDataItemArray['name']);
		    echo encodeField($metaDataItemArray['type']);
			echo encodeField($metaDataItemArray['valuecode']);
			echo encodeField($metaDataItemArray['value']);
		}


		// order metadata
        echo encodeField('ORDERMETADATA');
        $metaDataArray = $jobTicketArray['ordermetadata'];
        $count = count($metaDataArray);
        echo encodeField($count);
        for ($i = 0; $i < $count; $i++)
		{
		    $metaDataItemArray = $metaDataArray[$i];

		    echo encodeField($metaDataItemArray['code']);
		    echo encodeField($metaDataItemArray['name']);
		    echo encodeField($metaDataItemArray['type']);
			echo encodeField($metaDataItemArray['valuecode']);
			echo encodeField($metaDataItemArray['value']);
		}

        echo '<eol>';
	}


    static function getRow($pResultArray)
	{
    	ob_start('ob_logstdout');

    	echo encodeField('OK');
        echo encodeField('1');
        echo '<eol>';

    	foreach ($pResultArray as $key=>$value)
        {
            echo encodeField($key);
            echo encodeField($value);
        }
        echo '<eol>';
    }

    static function execute($pResult)
    {
        echo $pResult;
    }


    /**
   	* Echo's the unknown command response back to the calling application
   	*
   	* @static
	*
   	* @author Kevin Gale
	* @since Version 1.0.0
 	*/
    static function unknownCommand()
    {
        ob_start('ob_logstdout');

        echo encodeField('ERROR');
        echo encodeField('1');
        echo encodeField('Unknown Command');
    	echo '<eol>';
    }


    /**
   	* Echo's the result of the offline order creation back to the calling application
   	*
   	* @static
	*
	* @param array $pResultArray
   	*
   	* @author Kevin Gale
	* @since Version 3.2.0
 	*/
    static function findOfflineOrder($pResultArray)
    {
        ob_start('ob_logstdout');

        echo encodeField('OK');
        echo encodeField('1');
        echo '<eol>';

        echo encodeField($pResultArray['ordernumber']);
        echo encodeField($pResultArray['offlinesessionref']);
        echo encodeField($pResultArray['offlineurl']);
        echo encodeField($pResultArray['userid']);
        echo encodeField($pResultArray['contactfirstname']);
        echo encodeField($pResultArray['contactlastname']);
        echo '<eol>';
    }


    /**
   	* Echo's the result of the offline order creation back to the calling application
   	*
   	* @static
	*
	* @param array $pResultArray
   	*
   	* @author Kevin Gale
	* @since Version 3.2.0
 	*/
    static function createOfflineOrder($pResultArray)
    {
        ob_start('ob_logstdout');

        echo encodeField('OK');
        echo encodeField('1');
        echo '<eol>';

        $smarty = SmartyObj::newSmarty('AppAPI', '', '', $pResultArray['languagecode'], false, false);
        SmartyObj::replaceParams($smarty, $pResultArray['result'], $pResultArray['resultparam']);

        echo encodeField($smarty->get_template_vars($pResultArray['result']));
        echo encodeField($pResultArray['ordernumber']);
        echo encodeField($pResultArray['offlinesessionref']);
        echo encodeField($pResultArray['offlineurl']);
        echo '<eol>';
    }


    /**
   	* Echo's the brand data data back to the calling application
   	*
   	* @static
	*
	* @param array $pResultArray
   	*
   	* @author Kevin Gale
	* @since Version 3.2.0
 	*/
    static function getBrands($pResultArray)
    {
        ob_start('ob_logstdout');

        echo encodeField('OK');
        echo encodeField('1');
        echo '<eol>';

        $count = count($pResultArray);
        echo encodeField($count);
        echo '<eol>';

        for ($i = 0; $i < $count; $i++)
		{
		    $brandItemArray = $pResultArray[$i];

		    echo encodeField($brandItemArray['id']);
		    echo encodeField($brandItemArray['owner']);
		    echo encodeField($brandItemArray['code']);
		    echo encodeField($brandItemArray['name']);
		    echo encodeField($brandItemArray['applicationname']);
		    echo encodeField($brandItemArray['displayurl']);
			echo encodeField($brandItemArray['weburl']);
			echo encodeField($brandItemArray['isactive']);
			echo '<eol>';
		}
    }


    /**
   	* Echo's the session expired response back to the calling application
   	*
   	* @static
	*
	* @param array $pResultArray
   	*
   	* @author Kevin Gale
	* @since Version 3.0.0
 	*/
    static function sessionExpired()
    {
        ob_start('ob_logstdout');

        echo encodeField('ERROR');
        echo encodeField('1');
        echo encodeField('Session Expired');
    	echo '<eol>';
    }


	/**
   	* Echo's the result of performing a list of item actions back to the calling application
   	*
   	* @static
	*
	* @param array $pResultArray
   	*
   	* @author Kevin Gale
	* @since Version 5.0.0
 	*/
    static function performItemActionList($pResultArray)
    {
        ob_start('ob_logstdout');

        echo encodeField('OK');
        echo encodeField('1');
    	echo '<eol>';

    	$result = $pResultArray['result'];
        if ($result != '')
        {
            $smarty = SmartyObj::newSmarty('ProductionAutomation', '', '', $pResultArray['langcode']);
            $result = $smarty->get_config_vars($result);
            $result = str_replace('^0', $pResultArray['resultparam'], $result);
        }

        echo $result;
        echo '<eol>';
    }


	/**
	 * getProductionEvents
	 * - echo the list of events found back to the calling application
	 *
	 * @param array $pResultArray
	 */
	static function getProductionEvents($pResultArray)
	{
        ob_start('ob_logstdout');

        echo encodeField('OK');
        echo encodeField('1');
        echo '<eol>';

		$productionEventList = $pResultArray['eventlist'];
        $count = count($productionEventList);
        for ($i = 0; $i < $count; $i++)
		{
		    $eventArray = $productionEventList[$i];

		    echo encodeField($eventArray['eventid']);
		    echo encodeField($eventArray['actioncode']);
		    echo encodeField($eventArray['orderid']);
		    echo encodeField($eventArray['groupcode']);
		    echo encodeField($eventArray['ordernumber']);
		    echo encodeField($eventArray['itemcount']);
		    echo encodeField($eventArray['orderitemid']);
		    echo encodeField($eventArray['itemnumber']);
		    echo encodeField($eventArray['uploadref']);
		    
			echo encodeField($eventArray['jobticketoutputdevicecode']);
			echo encodeField($eventArray['pagesoutputdevicecode']);
			echo encodeField($eventArray['cover1outputdevicecode']);
			echo encodeField($eventArray['cover2outputdevicecode']);
			echo encodeField($eventArray['xmloutputdevicecode']);
			
			echo encodeField($eventArray['jobticketsubfoldername']);
			echo encodeField($eventArray['pagessubfoldername']);
			echo encodeField($eventArray['cover1subfoldername']);
			echo encodeField($eventArray['cover2subfoldername']);
			echo encodeField($eventArray['xmlsubfoldername']);
			
			echo encodeField($eventArray['jobticketoutputfilename']);
			echo encodeField($eventArray['pagesoutputfilename']);
			echo encodeField($eventArray['cover1outputfilename']);
			echo encodeField($eventArray['cover2outputfilename']);
			echo encodeField($eventArray['xmloutputfilename']);
		    
			echo '<eol>';
		}
	}

	/**
	* updateProductionEventStatus
   	* - echo the result of updating the event status back to the calling application
   	*
	* @param array $pResultArray
 	*/
    static function updateProductionEventStatus($pResultArray)
    {
        ob_start('ob_logstdout');

        echo encodeField('OK');
        echo encodeField('1');
    	echo '<eol>';

    	$result = $pResultArray['result'];
        if ($result != '')
        {
            $smarty = SmartyObj::newSmarty('ProductionAutomation', '', '', $pResultArray['langcode']);
            $result = $smarty->get_config_vars($result);
            $result = str_replace('^0', $pResultArray['resultparam'], $result);
        }

        echo $result;
        echo '<eol>';
    }
}

?>