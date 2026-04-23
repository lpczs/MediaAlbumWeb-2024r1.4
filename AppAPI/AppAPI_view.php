<?php

class AppAPI_view
{
    /**
   	* Encrypts the response and sends it back to the calling application
   	*
   	* @static
	*
	* @param array $pSourceString
   	*
   	* @author Kevin Gale
	* @since Version 3.0.0
 	*/
    static function encryptAndSendResponse($pSourceString)
    {
        $resultData = '1' . strlen($pSourceString) . '.' . gzcompress($pSourceString, 9);

        $iv_size = mcrypt_get_iv_size(MCRYPT_BLOWFISH, MCRYPT_MODE_CBC);
        $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);

        $key = base64_encode($iv) . 'TDAPIOUT' . strlen($resultData);
        $resultData = strlen($resultData) . '.' . base64_encode($iv) . '.' .
            mcrypt_encrypt(MCRYPT_BLOWFISH, $key, $resultData, MCRYPT_MODE_CBC, $iv) . "\rEOF";

        echo $resultData;
    }


    static function newOrderSmarty($pLanguageCode)
    {
        return SmartyObj::newSmarty('Order', '', '', $pLanguageCode, false, false);
    }

    static function newAppAPISmarty($pLanguageCode = 'en')
    {
        return SmartyObj::newSmarty('AppAPI', '', '', $pLanguageCode, false, false);
    }

	static function order($resultArray)
	{
        global $ac_config;

        $apiVersion = $resultArray['apiversion'];
		$languageCode = $resultArray['languageCode'];

        switch ($resultArray['result'])
        {
            case 'ORDER':
                $smarty = self::newOrderSmarty($languageCode);
                $smarty->assign('result', 'ORDER');
                $smarty->assign('version', '1');
                $smarty->assign('resultParam1', $resultArray['shoppingcarturl']);
                $smarty->assign('resultParam2', $resultArray['ref']);

                $uploadRefs = '';
                $items = $resultArray['items'];
                $itemCount = count($items);
                for ($i = 0; $i < $itemCount; $i++)
                {
                    $uploadRefs .= $items[$i]['uploadref'] . ',';
                }
                $uploadRefs = substr($uploadRefs, 0, strlen($uploadRefs) - 1);

                $smarty->assign('resultParam3', $uploadRefs);
                $smarty->assign('resultParam4', $resultArray['statusurl']);
                $smarty->assign('resultParam5', '');
                $smarty->assign('resultParam6', '');
                $smarty->assign('resultParam7', '');
                $smarty->assign('resultParam8', '');
                $smarty->assign('resultParam9', '');
                break;
            case 'ORDERCANCELCONFIRM':
                $smarty = self::newOrderSmarty($languageCode);
                $smarty->assign('result', 'ORDERCANCELCONFIRM');
                $smarty->assign('version', '1');
                $smarty->assign('resultParam1', '');
                $smarty->assign('resultParam2', '');
                $smarty->assign('resultParam3', '');
                $smarty->assign('resultParam4', '');
                $smarty->assign('resultParam5', '');
                $smarty->assign('resultParam6', '');
                $smarty->assign('resultParam7', '');
                $smarty->assign('resultParam8', '');
                $smarty->assign('resultParam9', '');
                break;
			case 'ORDERCANCELLED':
                $smarty = self::newOrderSmarty($languageCode);
                $smarty->assign('result', 'ORDERCANCELLED');
                $smarty->assign('version', '1');
                $smarty->assign('resultParam1', '');
                $smarty->assign('resultParam2', '');
                $smarty->assign('resultParam3', '');
                $smarty->assign('resultParam4', '');
                $smarty->assign('resultParam5', '');
                $smarty->assign('resultParam6', '');
                $smarty->assign('resultParam7', '');
                $smarty->assign('resultParam8', '');
                $smarty->assign('resultParam9', '');
                break;
            case 'ORDERCOMPLETED':
                $smarty = self::newOrderSmarty($languageCode);
                $smarty->assign('result', 'ORDERCOMPLETED');
                $smarty->assign('version', '1');
                $smarty->assign('resultParam1', '');
                $smarty->assign('resultParam2', '');
                $smarty->assign('resultParam3', '');
                $smarty->assign('resultParam4', '');
                $smarty->assign('resultParam5', '');
                $smarty->assign('resultParam6', '');
                $smarty->assign('resultParam7', '');
                $smarty->assign('resultParam8', '');
                $smarty->assign('resultParam9', '');
                break;
            case 'UPLOAD':
                $smarty = self::newOrderSmarty($languageCode);
                $smarty->assign('result', 'UPLOAD');

                $orderedItemsArray = $resultArray['items'];
                $itemCount = count($orderedItemsArray);

                // if the apiversion is 4 or higher then this is taopix designer version 3.1 and later
                // in this situation we return information on all items within the order
                if ($apiVersion >= 4)
                {
                    $smarty->assign('version', '2');
                }
                else
                {
                    $smarty->assign('version', '1');
                }

                $smarty->assign('resultParam1', $orderedItemsArray[0]['ordernumber']);
                $smarty->assign('resultParam2', $ac_config['FTPURL']);
                $smarty->assign('resultParam3', $ac_config['FTPUSER']);
                $smarty->assign('resultParam4', $ac_config['FTPPASS']);
                $smarty->assign('resultParam5', UtilsObj::correctPath($ac_config['FTPORDERSROOTPATH']));
                if ($ac_config['FTPGROUPORDERSBYCODE'] == '1')
                {
                    $smarty->assign('resultParam6', 'TRUE');
                }
                else
                {
                    $smarty->assign('resultParam6', 'FALSE');
                }

                if ($apiVersion < 4)
                {
                    $smarty->assign('resultParam7', $orderedItemsArray[0]['outputdeliverymethods']);
                    $smarty->assign('resultParam8', '');
                    $smarty->assign('resultParam9', '');
                }
                else
                {
                    $uploadRefs = '';
                    $outputDeliveryMethods = '';
                    $canUpload = '';
                    $saveOverride = '';
                    for ($i = 0; $i < $itemCount; $i++)
                    {
                        $itemArray = $orderedItemsArray[$i];

                        $uploadRefs .= $itemArray['uploadref'] . ',';
                        $outputDeliveryMethods .= $itemArray['outputdeliverymethods'] . ',';

                        if ($itemArray['canupload'] == '1')
                        {
                            $canUpload .= 'TRUE,';
                        }
                        else
                        {
                            $canUpload .= 'FALSE,';
                        }

                        if ($itemArray['canuploadenablesaveoverride'] == '1')
                        {
                            $saveOverride .= 'TRUE,';
                        }
                        else
                        {
                            $saveOverride .= 'FALSE,';
                        }
                    }

                    // append the save override onto the end of the upload status
                    $canUpload .= '<br>' . $saveOverride;

                    $uploadRefs = substr($uploadRefs, 0, strlen($uploadRefs) - 1);
                    $outputDeliveryMethods = substr($outputDeliveryMethods, 0, strlen($outputDeliveryMethods) - 1);
                    $canUpload = substr($canUpload, 0, strlen($canUpload) - 1);

                    $smarty->assign('resultParam7', $uploadRefs);
                    $smarty->assign('resultParam8', $outputDeliveryMethods);
                    $smarty->assign('resultParam9', $canUpload);
                }

                break;
            case 'PRODUCTION':
                $smarty = self::newOrderSmarty($languageCode);
                $smarty->assign('result', 'MESSAGE');
                $smarty->assign('version', '1');
                $smarty->assign('resultParam1', $smarty->get_config_vars('str_Error') . '...');
                $smarty->assign('resultParam2', $smarty->get_config_vars('str_Error') . '.');
                $smarty->assign('resultParam3', $smarty->get_config_vars('str_ErrorCannotUploadOrder'));
                $smarty->assign('resultParam4', '');
                $smarty->assign('resultParam5', '');
                $smarty->assign('resultParam6', '');
                $smarty->assign('resultParam7', '');
                $smarty->assign('resultParam8', '');
                $smarty->assign('resultParam9', '');
                break;
            case 'PRODUCTCODEMISMATCH':
                $smarty = self::newOrderSmarty($languageCode);
                $smarty->assign('result', 'MESSAGE');
                $smarty->assign('version', '1');
                $smarty->assign('resultParam1', $smarty->get_config_vars('str_Error') . '...');
                $smarty->assign('resultParam2', $smarty->get_config_vars('str_Error') . '.');
                $smarty->assign('resultParam3', $smarty->get_config_vars('str_ErrorProductCodeMismatch'));
                $smarty->assign('resultParam4', '');
                $smarty->assign('resultParam5', '');
                $smarty->assign('resultParam6', '');
                $smarty->assign('resultParam7', '');
                $smarty->assign('resultParam8', '');
                $smarty->assign('resultParam9', '');
                break;
            case 'COVERPAGECOUNTMISMATCH':
                $smarty = self::newOrderSmarty($languageCode);
                $smarty->assign('result', 'MESSAGE');
                $smarty->assign('version', '1');
                $smarty->assign('resultParam1', $smarty->get_config_vars('str_Error') . '...');
                $smarty->assign('resultParam2', $smarty->get_config_vars('str_Error') . '.');
                SmartyObj::replaceParams($smarty, 'str_ErrorCoverPageCountMismatch', $resultArray['covermaxpagecount']);
                $smarty->assign('resultParam3', $smarty->get_template_vars('str_ErrorCoverPageCountMismatch'));
                $smarty->assign('resultParam4', '');
                $smarty->assign('resultParam5', '');
                $smarty->assign('resultParam6', '');
                $smarty->assign('resultParam7', '');
                $smarty->assign('resultParam8', '');
                $smarty->assign('resultParam9', '');
                break;
            case 'PAPERPAGECOUNTMISMATCH':
                $smarty = self::newOrderSmarty($languageCode);
                $smarty->assign('result', 'MESSAGE');
                $smarty->assign('version', '1');
                $smarty->assign('resultParam1', $smarty->get_config_vars('str_Error') . '...');
                $smarty->assign('resultParam2', $smarty->get_config_vars('str_Error') . '.');
                if ($resultArray['papermaxpagecount'] == 1)
                {
                    $smarty->assign('resultParam3', $smarty->get_config_vars('str_ErrorPaperPageCountMismatch'));
                }
                else
                {
                    SmartyObj::replaceParams($smarty, 'str_ErrorPaperPageCountMismatch2', $resultArray['papermaxpagecount']);
                    $smarty->assign('resultParam3', $smarty->get_template_vars('str_ErrorPaperPageCountMismatch2'));
                }
                $smarty->assign('resultParam4', '');
                $smarty->assign('resultParam5', '');
                $smarty->assign('resultParam6', '');
                $smarty->assign('resultParam7', '');
                $smarty->assign('resultParam8', '');
                $smarty->assign('resultParam9', '');
                break;
            case 'INACTIVEPRODUCT':
                $smarty = self::newOrderSmarty($languageCode);

                $msg = $smarty->getConfigVars('str_ErrorProductNotAvailable2');
                $msg = str_replace(['^0', '^1'], [$resultArray['inactiveproductcollectioncode'], $resultArray['inactiveproductcollectioncode']], $msg);

                $smarty->assign('result', 'MESSAGE');
                $smarty->assign('version', '1');
                $smarty->assign('resultParam1', $smarty->get_config_vars('str_Error') . '...');
                $smarty->assign('resultParam2', $smarty->get_config_vars('str_Error') . '.');
                $smarty->assign('resultParam3', $msg);
                $smarty->assign('resultParam4', '');
                $smarty->assign('resultParam5', '');
                $smarty->assign('resultParam6', '');
                $smarty->assign('resultParam7', '');
                $smarty->assign('resultParam8', '');
                $smarty->assign('resultParam9', '');
                break;
            case 'CANCEL':
                $smarty = self::newOrderSmarty($languageCode);
                $smarty->assign('result', 'CANCEL');
                $smarty->assign('version', '1');
                $smarty->assign('resultParam1', '');
                $smarty->assign('resultParam2', '');
                $smarty->assign('resultParam3', '');
                $smarty->assign('resultParam4', '');
                $smarty->assign('resultParam5', '');
                $smarty->assign('resultParam6', '');
                $smarty->assign('resultParam7', '');
                $smarty->assign('resultParam8', '');
                $smarty->assign('resultParam9', '');
                break;
            case 'WAITINGFORPAYMENT':
                $smarty = self::newOrderSmarty($languageCode);
                $smarty->assign('result', 'MESSAGE');
                $smarty->assign('version', '1');
                $smarty->assign('resultParam1', $smarty->get_config_vars('str_Error') . '...');
                $smarty->assign('resultParam2', $smarty->get_config_vars('str_Error') . '.');
                $smarty->assign('resultParam3', $smarty->get_config_vars('str_ErrorCannotUploadProject'));
                $smarty->assign('resultParam4', '');
                $smarty->assign('resultParam5', '');
                $smarty->assign('resultParam6', '');
                $smarty->assign('resultParam7', '');
                $smarty->assign('resultParam8', '');
                $smarty->assign('resultParam9', '');
                break;
            case 'CUSTOMERROR':
                // a custom error needs to be displayed
                $smarty = self::newOrderSmarty($languageCode);
                $smarty->assign('result', 'MESSAGE');
                $smarty->assign('version', '1');
                $smarty->assign('resultParam1', $smarty->get_config_vars('str_Error') . '...');
                $smarty->assign('resultParam2', $smarty->get_config_vars('str_Error') . '.');
                $smarty->assign('resultParam3', $resultArray['resultparam']);
                $smarty->assign('resultParam4', '');
                $smarty->assign('resultParam5', '');
                $smarty->assign('resultParam6', '');
                $smarty->assign('resultParam7', '');
                $smarty->assign('resultParam8', '');
                $smarty->assign('resultParam9', '');
                break;
            default:
				// an error occurred

				// calendar componenets could return this error as well as high level when exceeding the cart size
				if (($resultArray['result'] == 'str_ErrorNoComponent') || ($resultArray['result'] == 'str_MessageShoppingCartFull'))
				{
					$webBrandCode = UtilsObj::getArrayParam($resultArray, 'webbrandcode', '');

					$smarty = SmartyObj::newSmarty('Order', $webBrandCode, '', $languageCode, false, false);
				}
				else
				{
					$smarty = self::newAppAPISmarty($languageCode);
				}

				SmartyObj::replaceParams($smarty, $resultArray['result'], $resultArray['resultparam']);

				$resultParam1 = $smarty->get_template_vars($resultArray['result']);

				if (($resultArray['result'] == 'str_ErrorNoComponent') || ($resultArray['result'] == 'str_MessageShoppingCartFull'))
				{
					// no need for the \n when it is being returned to either desktop or online
					$resultParam1 = str_replace('\n', " ", $resultParam1);
				}

				$smarty->assign('result', 'ERROR');
				$smarty->assign('version', '1');
				$smarty->assign('resultParam1', $resultParam1);
				$smarty->assign('resultParam2', '');
				$smarty->assign('resultParam3', '');
				$smarty->assign('resultParam4', '');
				$smarty->assign('resultParam5', '');
				$smarty->assign('resultParam6', '');
				$smarty->assign('resultParam7', '');
				$smarty->assign('resultParam8', '');
				$smarty->assign('resultParam9', '');
				break;
        }

        // if the apiversion is 3 or higher then this is taopix designer version 3 and later so we encrypt the output
        if ($apiVersion >= 3)
        {
            self::encryptAndSendResponse($smarty->fetch('appapi.tpl'));
        }
        else
        {
            $smarty->display('appapi.tpl');
        }
	}

	static function uploadCompleted($resultArray)
	{
	    $apiVersion = $resultArray['apiversion'];

		$templateName = 'appapi.tpl';
		$smarty = self::newAppAPISmarty($resultArray['languageCode']);

		switch ($resultArray['result'])
		{
			case '':
				$smarty->assign('result', 'OK');
				$smarty->assign('version', '1');

                // if the apiversion is 4 or higher then this is taopix designer version 3.1 and later
                // in this situation we return the information all of the time otherwise just return it if the upload method is mail
				if (($apiVersion >= 4) || ($resultArray['uploadmethod'] == 'MAIL'))
				{
					$templateName = 'appapi2.tpl';
					$smarty->assign('resultParam1', $resultArray['ordernumber']);
					$smarty->assign('resultParam2', $resultArray['userid']);
					$smarty->assign('resultParam3', $resultArray['shippingcustomername']);
					$smarty->assign('resultParam4', $resultArray['shippingcustomeraddress1']);
					$smarty->assign('resultParam5', $resultArray['shippingcustomeraddress2']);
					$smarty->assign('resultParam6', $resultArray['shippingcustomeraddress3']);
					$smarty->assign('resultParam7', $resultArray['shippingcustomeraddress4']);
					$smarty->assign('resultParam8', $resultArray['shippingcustomercity']);
					$smarty->assign('resultParam9', $resultArray['shippingcustomercounty']);
					$smarty->assign('resultParam10', $resultArray['shippingcustomerstate']);
					$smarty->assign('resultParam11', $resultArray['shippingcustomerpostcode']);
					$smarty->assign('resultParam12', $resultArray['shippingcustomercountrycode']);
					$smarty->assign('resultParam13', $resultArray['shippingcustomertelephonenumber']);
					$smarty->assign('resultParam14', $resultArray['shippingcustomeremailaddress']);
					$smarty->assign('resultParam15', $resultArray['shippingcontactfirstname']);
					$smarty->assign('resultParam16', $resultArray['shippingcontactlastname']);
				}
				else
				{
					$smarty->assign('resultParam1', '');
					$smarty->assign('resultParam2', '');
					$smarty->assign('resultParam3', '');
					$smarty->assign('resultParam4', '');
					$smarty->assign('resultParam5', '');
					$smarty->assign('resultParam6', '');
				}
				break;
	         default:
	     	    // an error occurred
	     	    SmartyObj::replaceParams($smarty, $resultArray['result'], $resultArray['resultparam']);
	            $smarty->assign('result', 'ERROR');
	            $smarty->assign('version', '1');
	            $smarty->assign('resultParam1', $smarty->get_template_vars($resultArray['result']));
	            $smarty->assign('resultParam2', '');
	            $smarty->assign('resultParam3', '');
	            $smarty->assign('resultParam4', '');
	            $smarty->assign('resultParam5', '');
	            $smarty->assign('resultParam6', '');
	            break;
	    }

	    // if the apiversion is 3 or higher then this is taopix designer version 3 and later so we encrypt the output
	    if ($apiVersion >= 3)
	    {
	        self::encryptAndSendResponse($smarty->fetch($templateName));
	    }
	    else
	    {
	        $smarty->display($templateName);
	    }
	}

	static function testConnection()
	{
		global $ac_config;

		$apiVersion = (int)UtilsObj::getPOSTParam('version', '1');

		$smarty = self::newAppAPISmarty();

		$smarty->assign('result', 'TEST');
		$smarty->assign('version', '1');
	    $smarty->assign('resultParam1', $ac_config['FTPURL']);
	    $smarty->assign('resultParam2', $ac_config['FTPUSER']);
	    $smarty->assign('resultParam3', $ac_config['FTPPASS']);
	    $smarty->assign('resultParam4', UtilsObj::correctPath($ac_config['FTPSPEEDTESTROOTPATH']));
	    $smarty->assign('resultParam5', '');
	    $smarty->assign('resultParam6', '');

	    // if the apiversion is 3 or higher then this is taopix designer version 3 and later so we encrypt the output
	    if ($apiVersion >= 3)
	    {
	        self::encryptAndSendResponse($smarty->fetch('appapi.tpl'));
	    }
	    else
	    {
	        $smarty->display('appapi.tpl');
	    }
	}


    /**
   	* Echo's the get license key response back to the calling application
   	*
   	* @static
	*
	* @param array $pResultArray
   	*
   	* @author Kevin Gale
	* @since Version 1.0.0
 	*/
	static function getLicenseKey($pResultArray)
	{
		global $ac_config;

		$ftpDetails = UtilsObj::getAutoUpdateFTPDetails();

        $apiVersion = $pResultArray['apiversion'];

	    $smarty = self::newAppAPISmarty($pResultArray['languageCode']);

		switch ($pResultArray['result'])
		{
			case '':
				$smarty->assign('result', 'OK');
				$smarty->assign('version', '1');
	    		$smarty->assign('resultParam1', $ftpDetails['ftpurl']);
	    		$smarty->assign('resultParam2', $ftpDetails['ftpuser']);
				$smarty->assign('resultParam3', $ftpDetails['ftppass']);
	            $smarty->assign('resultParam4', UtilsObj::correctPath($ac_config['FTPLICENSEKEYSROOTPATH']));
	            $smarty->assign('resultParam5', $pResultArray['keyfilename']);
	            $smarty->assign('resultParam6', $pResultArray['keyfilesize']);
	            $smarty->assign('resultParam7', $pResultArray['keyfilechecksum']);
	            $smarty->assign('resultParam8', $pResultArray['keydata']);
	            break;
	        case 'INVALIDLICENSEKEYLOGIN':
                $smarty->assign('result', 'MESSAGE');
                $smarty->assign('version', '1');
                $smarty->assign('resultParam1', LocalizationObj::getConstantName($smarty, $pResultArray['authenticationMode'],'AUTHENTICATIONMODE'));
                $smarty->assign('resultParam2', '');
                $smarty->assign('resultParam3', '');
                $smarty->assign('resultParam4', '');
                $smarty->assign('resultParam5', '');
                $smarty->assign('resultParam6', '');
                break;
	        default:
	     	    // an error occurred
			    SmartyObj::replaceParams($smarty, $pResultArray['result'], $pResultArray['resultparam']);
	            $smarty->assign('result', 'ERROR');
	            $smarty->assign('version', '1');
	            $smarty->assign('resultParam1', $smarty->get_template_vars($pResultArray['result']));
	            $smarty->assign('resultParam2', '');
	            $smarty->assign('resultParam3', '');
	            $smarty->assign('resultParam4', '');
	            $smarty->assign('resultParam5', '');
	            $smarty->assign('resultParam6', '');
	            break;
	    }

	    // if the apiversion is 3 or higher then this is taopix designer version 3 and later so we encrypt the output
	    if ($apiVersion >= 3)
	    {
	        self::encryptAndSendResponse($smarty->fetch('appapi.tpl'));
	    }
	    else
	    {
	        $smarty->display('appapi.tpl');
	    }
	}


    /**
   	* Build the update data for application assets that will be sent back to either TAOPIX™ Builder or TAOPIX™ Designer
   	*
   	* @static
	*
   	* @author Kevin Gale
	* @since Version 3.0.0
 	*/
	static function systemUpdateCreateApplicationFileData($pResultArray, $pBrandPath, $pType)
	{
		global $ac_config;

		$ftpDetails = UtilsObj::getAutoUpdateFTPDetails();

		$apiVersion = $pResultArray['apiversion'];

		$itemData = '';

		switch ($pType)
		{
			case TPX_APPLICATION_FILE_TYPE_MASK:
				$itemData .= "MASKS\r<br>\r";
				$itemData .= '1' . "\r<br>\r";
				$itemData .= $ftpDetails['ftpurl'] . "\r<br>\r";
				$itemData .= $ftpDetails['ftpuser'] . "\r<br>\r";
				$itemData .= $ftpDetails['ftppass'] . "\r<br>\r";
				$itemData .= UtilsObj::correctPath($ac_config['FTPAPPLICATIONMASKSROOTPATH']) . $pBrandPath. "\r<br>\r";
				$itemList = $pResultArray['masks']['filelist'];
				$cacheVersion = $pResultArray['aucacheversionmasks'];
				break;
			case TPX_APPLICATION_FILE_TYPE_BACKGROUND:
				$itemData .= "BACKGROUNDS\r<br>\r";
				$itemData .= '1' . "\r<br>\r";
				$itemData .= $ftpDetails['ftpurl'] . "\r<br>\r";
				$itemData .= $ftpDetails['ftpuser'] . "\r<br>\r";
				$itemData .= $ftpDetails['ftppass'] . "\r<br>\r";
				$itemData .= UtilsObj::correctPath($ac_config['FTPAPPLICATIONBACKGROUNDSROOTPATH']) . $pBrandPath. "\r<br>\r";
				$itemList = $pResultArray['backgrounds']['filelist'];
				$cacheVersion = $pResultArray['aucacheversionbackgrounds'];
				break;
			case TPX_APPLICATION_FILE_TYPE_PICTURE:
				$itemData .= "SCRAPBOOK\r<br>\r";
				$itemData .= '1' . "\r<br>\r";
				$itemData .= $ftpDetails['ftpurl'] . "\r<br>\r";
				$itemData .= $ftpDetails['ftpuser'] . "\r<br>\r";
				$itemData .= $ftpDetails['ftppass'] . "\r<br>\r";
				$itemData .= UtilsObj::correctPath($ac_config['FTPAPPLICATIONSCRAPBOOKPICTURESROOTPATH']) . $pBrandPath. "\r<br>\r";
				$itemList = $pResultArray['scrapbookpictures']['filelist'];
				$cacheVersion = $pResultArray['aucacheversionscrapbook'];
				break;
			case TPX_APPLICATION_FILE_TYPE_FRAME:
				$itemData .= "FRAMES\r<br>\r";
				$itemData .= '1' . "\r<br>\r";
				$itemData .= $ftpDetails['ftpurl'] . "\r<br>\r";
				$itemData .= $ftpDetails['ftpuser'] . "\r<br>\r";
				$itemData .= $ftpDetails['ftppass'] . "\r<br>\r";
				$itemData .= UtilsObj::correctPath($ac_config['FTPAPPLICATIONFRAMESROOTPATH']) . $pBrandPath. "\r<br>\r";
				$itemList = $pResultArray['frames']['filelist'];
				$cacheVersion = $pResultArray['aucacheversionframes'];
				break;
		}

		// if the apiversion is 7 or higher then this is taopix version 4.2
		// in this situation we include the cache version
		if ($apiVersion >= 7)
		{
			$itemData .= $cacheVersion . "\r<br>\r";
		}

		$itemCount = count($itemList);
		$itemData .= $itemCount . "\r<br>\r";
		for ($i = 0; $i < $itemCount; $i++)
		{
			$itemData .= $itemList[$i]['ref'] . "\r<br>\r";

			if ($apiVersion == 1)
			{
				$itemData .= LocalizationObj::getLocaleString($itemList[$i]['categoryname'], $languageCode, true) . "\r<br>\r";
				$itemData .= LocalizationObj::getLocaleString($itemList[$i]['name'], $languageCode, true) . "\r<br>\r";
			}
			else
			{
				$itemData .= $itemList[$i]['categoryname'] . "\r<br>\r";
				$itemData .= $itemList[$i]['name'] . "\r<br>\r";
			}
			$itemData .= $itemList[$i]['filename'] . "\r<br>\r";

			$dateValue = $itemList[$i]['datemodified'];
			if ($dateValue != '0000-00-00 00:00:00')
			{
				$dateString = date('Y-m-d H:i:s', strtotime($dateValue));
			}
			else
			{
				$dateString = '';
			}

			$itemData .= $dateString . "\r<br>\r";
			if ($itemList[$i]['hiddenfromuser'] == 1)
			{
				$itemData .= "TRUE\r<br>\r";
			}
			else
			{
				$itemData .= "FALSE\r<br>\r";
			}

			// if the apiversion is 3 or higher then this is taopix designer version 3 and later so we add additional data
			if ($apiVersion >= 3)
			{
				$itemData .= $itemList[$i]['appversion'] . "\r<br>\r";
				$itemData .= $itemList[$i]['products'] . "\r<br>\r";
				$itemData .= $itemList[$i]['themes'] . "\r<br>\r";

				if ($itemList[$i]['isencrypted'] == 1)
                {
                    $itemData .= "TRUE\r<br>\r";
                }
                else
                {
                    $itemData .= "FALSE\r<br>\r";
                }

				$itemData .= $itemList[$i]['updatepriority'] . "\r<br>\r";
				$itemData .= $itemList[$i]['filesize'] . "\r<br>\r";
				$itemData .= $itemList[$i]['checksum'] . "\r<br>\r";

				if ($itemList[$i]['hasfpo'] == 1)
                {
                    $itemData .= "TRUE\r<br>\r";
                }
                else
                {
                    $itemData .= "FALSE\r<br>\r";
                }

                if ($itemList[$i]['haspreview'] == 1)
                {
                    $itemData .= "TRUE\r<br>\r";
                }
                else
                {
                    $itemData .= "FALSE\r<br>\r";
                }

                if ($itemList[$i]['isactive'] == 1)
                {
                    $itemData .= "TRUE\r<br>\r";
                }
                else
                {
                    $itemData .= "FALSE\r<br>\r";
                }
			}

		}

		return $itemData;
	}

    /**
	* Returns the component data formatted for the auto-update response.
	*
   	* @static
	*
	* @param string $pCategory
	* @param array $pComponentList
	*
   	* @return string
   	*
   	* @author Kevin Gale
	* @since Version 3.0.0
 	*/
    static function buildSystemUpdateComponentData($pCategoryCode, $pComponentList, $pAPIVersion)
    {
        $componentData = '';

        $defaultCode = '';
        $pricingModel = 0;
        $requiresPageCount = 0;
        $componentCount = count($pComponentList);

        if ($componentCount > 0)
        {
            for ($i = 0; $i < $componentCount; $i++)
            {
                if ($i == 0)
                {
                    $pricingModel = $pComponentList[$i]['pricingmodel'];
                    $requiresPageCount = $pComponentList[$i]['requirespagecount'];
                }

                if ($pComponentList[$i]['default'])
                {
                    $defaultCode = $pComponentList[$i]['localcode'];
                }

                $componentData .= $pComponentList[$i]['localcode'] . "\t";
                $componentData .= $pComponentList[$i]['name'] . "\t";
                $componentData .= $pComponentList[$i]['minpagecount'] . "\t";
                $componentData .= $pComponentList[$i]['maxpagecount'] . "\t";
                $componentData .= $pComponentList[$i]['pricedata'] . "\t";

                // if the apiversion is 6 or higher then this is taopix designer version 3.3 and later
                // in this situation we return the dropdown / price tax information
                if ($pAPIVersion >= 6)
                {
                    $componentData .= $pComponentList[$i]['quantityisdropdown'] . "\t";
                    $componentData .= $pComponentList[$i]['pricetaxrate'] . "\t";
                }
            }
            $componentData = $pCategoryCode . "\t" . $pricingModel . "\t" . $requiresPageCount . "\t" .
                    $componentCount . "\t" . $defaultCode . "\t" . $componentData;

            $componentData = substr($componentData, 0, -1); // remove the trailing separator

            // if the apiversion is 6 or higher then this is taopix designer version 3.3 and later
            // in this situation we add the data version number
            if ($pAPIVersion >= 6)
            {
                $componentData = "<2>\t" . $componentData;
            }
        }

        return $componentData;
    }

    /**
   	* Creates the update data that is sent back to either TAOPIX™ Builder or TAOPIX™ Designer
   	*
   	* @static
	*
	* @param array $pResultArray
	*
   	* @author Kevin Gale
	* @since Version 1.0.0
 	*/
    static function systemUpdate($pResultArray)
    {
        global $ac_config;

		$ftpDetails = UtilsObj::getAutoUpdateFTPDetails();

        $apiVersion = $pResultArray['apiversion'];
        $languageCode = $pResultArray['languageCode'];

        // determine if the desktop designer can support single prints with sub-components
        $compareAppVersionResult = UtilsObj::compareApplicationVersions($pResultArray['appversion'], '5.0.0');
		if (($compareAppVersionResult == '=') || ($compareAppVersionResult == '>'))
		{
			$singlePrintsSupportsSubComponents = true;
		}
		else
		{
			$singlePrintsSupportsSubComponents = false;
		}


		// build the output data
        $smarty = self::newAppAPISmarty($languageCode);

        switch ($pResultArray['result'])
        {
            case '':
                $itemData = '';

                // if the apiversion is 5 or higher then this is taopix designer version 3.2 and later
                // in this situation we return the server date/time
                if ($apiVersion >= 5)
                {
                    $itemData .= $pResultArray['serverdatetime'] . "\r<br>\r";
                }

                if ($pResultArray['webbrandcode'] != '')
                {
                    $brandPath = $pResultArray['webbrandcode'] . '/';
                }
                else
                {
                    $brandPath = '';
                }

                // product categories
                if (($apiVersion >= 3) && ($pResultArray['productcategoryversion'] != ''))
                {
                    $itemData .= "PRODUCTCATEGORIES\r<br>\r";
                    $itemData .= '1' . "\r<br>\r";
                    $itemData .= $pResultArray['productcategoryversion'] . "\r<br>\r";
                    $itemData .= base64_encode($pResultArray['productcategorydata']) . "\r<br>\r";
                }

                // products
                // if the apiversion is 3 or higher then this is taopix version 3
                // in this situation we include the product collections and products together
                // if the apiversion is less than 3 then this is a previous version of taopix
                // in this situation we include a combination of the collection and product data only if there is a collection for the product

                $collectionList = $pResultArray['productcollections']['productlist'];
				$collectionCount = count($collectionList);

				$productList = $pResultArray['products']['productlist'];
				$productCount = count($productList);

                // building the data involves iterating collection count x product count times
                // to reduce the number of loop iterations we build an index of all entries that match the collection code
                $layoutIndexArray = Array();
				for ($i = 0; $i < $productCount; $i++)
				{
					$collectionCode = $productList[$i]['collectioncode'];

					if (array_key_exists($collectionCode, $layoutIndexArray))
					{
						$indexList = $layoutIndexArray[$collectionCode];
					}
					else
					{
						$indexList = Array();
					}

					$indexList[] = $i;
					$layoutIndexArray[$collectionCode] = $indexList;
				}

                // process the product data
                if ($apiVersion >= 3)
                {
                    // taopix version 3 or newer
                    $includedCollectionData = '';
                    $includedCollectionCount = 0;
                    for ($i = 0; $i < $collectionCount; $i++)
                    {
                        $item = $collectionList[$i];
                        $collectionCode = $item['code'];
                        $collectionProductLinkArray = Array();

						// find the index entries for this collection and then merge all products for this collection
                        $itemData2 = '';
                        $collectionProductCount = 0;

                        if (array_key_exists($collectionCode, $layoutIndexArray))
						{
							$indexList = $layoutIndexArray[$collectionCode];
						}
						else
						{
							$indexList = Array();
						}

                        $indexCount = count($indexList);
						for ($i2 = 0; $i2 < $indexCount; $i2++)
						{
							$productItem = $productList[$indexList[$i2]];

                            if ($productItem['collectioncode'] == $collectionCode)
                            {
                                $itemData2 .= $productItem['code'] . "\r<br>\r";

                                // this field was removed from designer from versions 10 and onwards
                                if ($apiVersion <= 9)
                                {
                                    $itemData2 .= "\r<br>\r";
                                }

                                $itemData2 .= $productItem['pricedescription'] . "\r<br>\r";
                                $itemData2 .= $productItem['pricingmodel'] . "\r<br>\r";
                                $itemData2 .= $productItem['price'] . "\r<br>\r";

                                // if the apiversion is 5 or higher then this is taopix designer version 3.2 and later
                                // in this situation we return if the quantity is a dropdown
                                if ($apiVersion >= 5)
                                {
                                    if ($productItem['qtyisdropdown'] == 1)
                                    {
                                        $itemData2 .= "TRUE\r<br>\r";
                                    }
                                    else
                                    {
                                        $itemData2 .= "FALSE\r<br>\r";
                                    }
                                }

                                // if the apiversion is 6 or higher then this is taopix designer version 3.3 and later
                                // in this situation we return the price tax rate and if prices should be displayed with tax
                                if ($apiVersion >= 6)
                                {
                                    $itemData2 .= $productItem['pricetaxrate'] . "\r<br>\r";

                                    if ($pResultArray['showpriceswithtax'] == 1)
                                    {
                                        $itemData2 .= "TRUE\r<br>\r";
                                    }
                                    else
                                    {
                                        $itemData2 .= "FALSE\r<br>\r";
                                    }
                                }

                                // the collection link information will be the same for each product so only build it once per collection
                                if (empty($collectionProductLinkArray))
                                {
                                    $collectionProductLinkArray['publishversion'] = $productItem['publishversion'];
                                    $collectionProductLinkArray['collectionthumbnailresourceref'] = $productItem['collectionthumbnailresourceref'];
                                    $collectionProductLinkArray['collectionthumbnailresourcedatauid'] = $productItem['collectionthumbnailresourcedatauid'];
                                    $collectionProductLinkArray['collectionpreviewresourceref'] = $productItem['collectionpreviewresourceref'];
                                    $collectionProductLinkArray['collectionpreviewresourcedatauid'] = $productItem['collectionpreviewresourcedatauid'];
                                    $collectionProductLinkArray['collectionsortlevel'] = $productItem['collectionsortlevel'];
                                    $collectionProductLinkArray['collectiontextengineversion'] = $productItem['collectiontextengineversion'];
                                    $collectionProductLinkArray['type'] = $productItem['type'];
                                    $collectionProductLinkArray['collectionsummary'] = $productItem['collectionsummary'];
                                    $collectionProductLinkArray['collectionthumbnailresourcedevicepixelratio'] = $productItem['collectionthumbnailresourcedevicepixelratio'];
                                    $collectionProductLinkArray['collectionpreviewresourcedevicepixelratio'] = $productItem['collectionpreviewresourcedevicepixelratio'];
                                }


								// determine if we should include this product or not
								$includeProductItem = true;

								// if this is a single prints product we need to make sure the desktop designer supports the features
								if ($productItem['type'] == TPX_PRODUCTCOLLECTIONTYPE_SINGLEPRINTS)
								{
									// disable the product if it has sub-components and the desktop designer does not support them
									if ((count($productItem['singleprintoptionlist']) > 0) && (! $singlePrintsSupportsSubComponents))
									{
										$includeProductItem = false;
									}

									// disable the product if it is to be priced by grouping the component and subcomponent and the desktop designer does not support this
									if (($productItem['productoption'] & TPX_PRODUCTOPTION_PRICING_PERCOMPONENTSUBCOMPONENT) && ($apiVersion < 9))
									{
										$includeProductItem = false;
									}
								}


                                // build the component data if we are including this product item
                                if ($includeProductItem)
								{
									$componentData = self::buildSystemUpdateComponentData('COVER', $productItem['coverlist'], $apiVersion);

									$paperData = self::buildSystemUpdateComponentData('PAPER', $productItem['paperlist'], $apiVersion);
									if ($paperData != '')
									{
										if ($componentData != '')
										{
										   $componentData .= "\t";
										}

										$componentData .= $paperData;
									}

									$singlePrintData = self::buildSystemUpdateComponentData('SINGLEPRINT', $productItem['singleprintlist'], $apiVersion);
									if ($singlePrintData != '')
									{
										if ($componentData != '')
										{
										   $componentData .= "\t";
										}

										$componentData .= $singlePrintData;
									}

									$singlePrintPaperList = $productItem['singleprintoptionlist'];
									$singlePrintCount = count($singlePrintPaperList);
									if ($singlePrintCount > 0)
									{
										foreach ($singlePrintPaperList as $singlePrintPaperItem)
										{
											$singlePrintData = self::buildSystemUpdateComponentData('SINGLEPRINTOPTION', $singlePrintPaperItem, $apiVersion);
											if ($singlePrintData != '')
											{
												if ($componentData != '')
												{
												   $componentData .= "\t";
												}

												$componentData .= $singlePrintData;
											}
										}
									}

									$calendarCustomisationData = self::buildSystemUpdateComponentData('CALENDARCUSTOMISATION', $productItem['calendarcustomisationlist'], $apiVersion);
									if ($calendarCustomisationData != '')
									{
										if ($componentData != '')
										{
										   $componentData .= "\t";
                                        }

										$componentData .= $calendarCustomisationData;
									}

									$AIComponentData = self::buildSystemUpdateComponentData('TAOPIXAI', $productItem['taopixailist'], $apiVersion);
									if ($AIComponentData !== '')
									{
										if ($componentData !== '')
										{
											$componentData .= "\t";
										}

										$componentData .= $AIComponentData;
									}
                                }
                                else
                                {
                                	// the product is not being included so clear the component data
									$componentData = '';
                                }

                                $itemData2 .= $componentData . "\r<br>\r";

                                // if the apiversion is 5 or higher then this is taopix designer version 3.2 and later
                                // in this situation we return information on the product tax rate
                                if ($apiVersion >= 5)
                                {
                                    $itemData2 .= $productItem['taxrate'] . "\r<br>\r";
                                }

								// if the apiversion is 9 or higher then this is taopix designer 2017r1 and later
								// in this situation we return the product options data
								if ($apiVersion >= 9)
								{
									$itemData2 .= $productItem['productoption'] . "\r<br>\r";
								}

                                // this field was removed from designer from versions 10 and onwards
                                if ($apiVersion <= 9)
                                {
                                    $itemData2 .= "\r<br>\r"; // place holder for alternative products
                                }

                                // the product is only active if we are including the item and both the collection and product are active
                                if (($includeProductItem) && ($item['isactive'] == 1) && ($productItem['isactive'] == 1))
                                {
                                    $itemData2 .= "TRUE\r<br>\r";
                                }
                                else
                                {
                                    $itemData2 .= "FALSE\r<br>\r";
                                }

                                // if the apiversion is 10 or higher then this is taopix designer 2021r2 and later
                                // in this situation we include additional metadata for the product selector
                                if ($apiVersion >= 10)
                                {
                                    $itemData2 .= $productItem['producttarget'] . "\r<br>\r";
                                    $itemData2 .= $productItem['productminpagecount'] . "\r<br>\r";
                                    $itemData2 .= $productItem['productaimodedesktop'] . "\r<br>\r";
                                    $itemData2 .= $productItem['productselectormodedesktop'] . "\r<br>\r";
                                }

                                $collectionProductCount++;
                            }
                        }


                        // if we have one or more products for this collection include the data
                        if ($collectionProductCount > 0)
                        {
                            $includedCollectionData .= $item['company'] . "\r<br>\r";
                            $includedCollectionData .= $item['code'] . "\r<br>\r";

                            // if the apiversion is 10 or lower then this is taopix designer 2021r2 and earlier
                            // in this situation we include dummy filter codes and names
                            if ($apiVersion <= 10)
                            {
                                $includedCollectionData .= "\r<br>\r";
                                $includedCollectionData .= "\r<br>\r";
                            }
    
                            $includedCollectionData .= $item['name'] . "\r<br>\r";
                            
                            // this field was removed from designer from versions 10 and onwards
                            if ($apiVersion <= 9)
                            {
                                // this field is not used so supply placeholder
                                $includedCollectionData .= "\r<br>\r"; 
                            }

                            $dateValue = $item['version'];

                            if ($dateValue != '0000-00-00 00:00:00')
                            {
                                $dateString = date('Y-m-d H:i:s', strtotime($dateValue));
                            }
                            else
                            {
                                $dateString = '';
                            }
                            $includedCollectionData .= $dateString . "\r<br>\r";

                            $includedCollectionData .= $item['appversion'] . "\r<br>\r";
                            $includedCollectionData .= $item['dataversion'] . "\r<br>\r";
                            $includedCollectionData .= str_replace(array("\r\n", "\n", "\r"),
                                '<eol>', $item['dependencies']) . "\r<br>\r"; // replace newline characters with an <eol> tag to make the data compatible
                            $includedCollectionData .= $item['updatepriority'] . "\r<br>\r";
                            $includedCollectionData .= $item['filesize'] . "\r<br>\r";
                            $includedCollectionData .= $item['checksum'] . "\r<br>\r";

                            // this field was removed from designer from versions 10 and onwards
                            if ($apiVersion <= 9)
                            {
                                if ($item['haspreview'] == 1)
                                {
                                    $includedCollectionData .= "TRUE\r<br>\r";
                                }
                                else
                                {
                                    $includedCollectionData .= "FALSE\r<br>\r";
                                }
                            }

                            if ($item['separatecomponents'] == 1)
                            {
                                $includedCollectionData .= "TRUE\r<br>\r";
                            }
                            else
                            {
                                $includedCollectionData .= "FALSE\r<br>\r";
                            }

                            // if the apiversion is 10 or higher then this is taopix designer 2021r2 and later
                            // in this situation we include additional metadata for the product selector
                            if ($apiVersion >= 10)
                            {
                                $includedCollectionData .= $collectionProductLinkArray['publishversion'] . "\r<br>\r";
                                $includedCollectionData .= $collectionProductLinkArray['type'] . "\r<br>\r";
                                $includedCollectionData .= $collectionProductLinkArray['collectionthumbnailresourceref'] . "\r<br>\r";
                                $includedCollectionData .= $collectionProductLinkArray['collectionthumbnailresourcedatauid'] . "\r<br>\r";
                                $includedCollectionData .= $collectionProductLinkArray['collectionpreviewresourceref'] . "\r<br>\r";
                                $includedCollectionData .= $collectionProductLinkArray['collectionpreviewresourcedatauid'] . "\r<br>\r";
                                $includedCollectionData .= $collectionProductLinkArray['collectionsortlevel'] . "\r<br>\r";
                                $includedCollectionData .= $collectionProductLinkArray['collectiontextengineversion'] . "\r<br>\r";
                            }

                            // if the apiversion is 11 or higher then this is taopix designer 2023r1 and later
                            // in this situation we want to include the product selector summary and hidpi data
                            if ($apiVersion >= 11)
                            {
                                $includedCollectionData .= $collectionProductLinkArray['collectionsummary'] . "\r<br>\r";
                                $includedCollectionData .= $collectionProductLinkArray['collectionthumbnailresourcedevicepixelratio'] . "\r<br>\r";
                                $includedCollectionData .= $collectionProductLinkArray['collectionpreviewresourcedevicepixelratio'] . "\r<br>\r";
                            }

                            $includedCollectionData .= $collectionProductCount . "\r<br>\r";
                            $includedCollectionData .= $itemData2;

                            $includedCollectionCount++;
                        }
                    }


                    // add the header data
                    $itemData .= "PRODUCTS\r<br>\r";
                    $itemData .= '1' . "\r<br>\r";
                    $itemData .= $ftpDetails['ftpurl'] . "\r<br>\r";
                    $itemData .= $ftpDetails['ftpuser'] . "\r<br>\r";
                    $itemData .= $ftpDetails['ftppass'] . "\r<br>\r";
                    $itemData .= UtilsObj::correctPath($ac_config['FTPPRODUCTSROOTPATH']) . "\r<br>\r";

                    // if the apiversion is 7 or higher then this is taopix version 4.2
                    // in this situation we include the product cache version
                    if ($apiVersion >= 7)
                    {
                        $itemData .= $pResultArray['productcacheversion'] . "\r<br>\r";
                    }

                    $itemData .= $includedCollectionCount . "\r<br>\r";
                    $itemData .= $includedCollectionData;
                }
                else
                {
                    // older than taopix version 3
                    $itemList = Array();

                    // older versions of taopix do not have product collections but we assume the product code and product collection code are the same
                    // this means we have to loop around the product collections looking for a match within the products list
                    // we then merge the data into a list that is processed further down

                    for ($i = 0; $i < $collectionCount; $i++)
                    {
                        $collectionCode = $collectionList[$i]['code'];

                        // find the index entries for this collection
                        if (array_key_exists($collectionCode, $layoutIndexArray))
						{
							$indexList = $layoutIndexArray[$collectionCode];
						}
						else
						{
							$indexList = Array();
						}

                        $indexCount = count($indexList);
						for ($i2 = 0; $i2 < $indexCount; $i2++)
						{
                            $productItem = $productList[$indexList[$i2]];

                            if (($productItem['collectioncode'] == $collectionCode) && ($productItem['code'] == $collectionCode))
                            {
                                // we have a match so this is a product that has a code the same as the collection code
                                $item = Array();
                                $item['code'] = $collectionCode;
                                $item['categoryname'] = $collectionList[$i]['categoryname'];
                                $item['name'] = $collectionList[$i]['name'];
                                $item['pricedescription'] = $productItem['pricedescription'];
                                $item['version'] = $collectionList[$i]['version'];
                                $item['appversion'] = $collectionList[$i]['appversion'];
                                $item['dataversion'] = $collectionList[$i]['dataversion'];

                                // the active status is first determined by the collection and then by the product
                                $isActive = $collectionList[$i]['isactive'];
                                if ($isActive == 1)
                                {
                                    $isActive = $productItem['isactive'];
                                }
                                $item['isactive'] = $isActive;

                                $itemList[] =$item;

                                break;
                            }
                        }
                    }

                    $itemData .= "PRODUCTS\r<br>\r";
                    $itemData .= '1' . "\r<br>\r";
                    $itemData .= $ftpDetails['ftpurl'] . "\r<br>\r";
                    $itemData .= $ftpDetails['ftpuser'] . "\r<br>\r";
                    $itemData .= $ftpDetails['ftppass'] . "\r<br>\r";
                    $itemData .= UtilsObj::correctPath($ac_config['FTPPRODUCTSROOTPATH']) . "\r<br>\r";
                    $itemCount = count($itemList);
                    $itemData .= $itemCount . "\r<br>\r";
                    for ($i = 0; $i < $itemCount; $i++)
                    {
                        $item = $itemList[$i];

                        $itemData .= $item['code'] . "\r<br>\r";

                        if ($apiVersion == 1)
                        {
                            $itemData .= LocalizationObj::getLocaleString($item['categoryname'], $languageCode, true) . "\r<br>\r";
                            $itemData .= LocalizationObj::getLocaleString($item['name'], $languageCode, true) . "\r<br>\r";
                            $itemData .= LocalizationObj::getLocaleString($item['pricedescription'], $languageCode, true) . "\r<br>\r";
                        }
                        else
                        {
                            $itemData .= $item['categoryname'] . "\r<br>\r";
                            $itemData .= $item['name'] . "\r<br>\r";
                            $itemData .= $item['pricedescription'] . "\r<br>\r";
                        }

                        $dateValue = $item['version'];
                        if ($dateValue != '0000-00-00 00:00:00')
                        {
                            $dateString = date('Y-m-d H:i:s', strtotime($dateValue));
                        }
                        else
                        {
                            $dateString = '';
                        }
                        $itemData .= $dateString . "\r<br>\r";

                        $itemData .= $item['appversion'] . "\r<br>\r";
                        $itemData .= $item['dataversion'] . "\r<br>\r";

                        if ($item['isactive'] == 1)
                        {
                            $itemData .= "TRUE\r<br>\r";
                        }
                        else
                        {
                            $itemData .= "FALSE\r<br>\r";
                        }
                    }
                }


                // masks
                $itemData .= self::systemUpdateCreateApplicationFileData($pResultArray, $brandPath, TPX_APPLICATION_FILE_TYPE_MASK);

                // backgrounds
                $itemData .= self::systemUpdateCreateApplicationFileData($pResultArray, $brandPath, TPX_APPLICATION_FILE_TYPE_BACKGROUND);

                // scrapbook pictures
                $itemData .= self::systemUpdateCreateApplicationFileData($pResultArray, $brandPath, TPX_APPLICATION_FILE_TYPE_PICTURE);

                // frames
                $itemData .= self::systemUpdateCreateApplicationFileData($pResultArray, $brandPath, TPX_APPLICATION_FILE_TYPE_FRAME);


                // license keys
                $itemData .= "LICENSEKEYS\r<br>\r";
                $itemData .= '1' . "\r<br>\r";
                $itemData .= $ftpDetails['ftpurl'] . "\r<br>\r";
                $itemData .= $ftpDetails['ftpuser'] . "\r<br>\r";
                $itemData .= $ftpDetails['ftppass'] . "\r<br>\r";
                $itemData .= UtilsObj::correctPath($ac_config['FTPLICENSEKEYSROOTPATH']) . "\r<br>\r";
                $itemList = $pResultArray['licensekeys']['licensekeylist'];
                $itemCount = count($itemList);
                $itemData .= $itemCount . "\r<br>\r";
                for ($i = 0; $i < $itemCount; $i++)
                {
                    $item = $itemList[$i];

                    $itemData .= $item['groupcode'] . "\r<br>\r";
                    $itemData .= $item['filename'] . "\r<br>\r";

                    $dateValue = $item['version'];
                    if ($dateValue != '0000-00-00 00:00:00')
                    {
                        $dateString = date('Y-m-d H:i:s', strtotime($dateValue));
                    }
                    else
                    {
                        $dateString = '';
                    }

                    $itemData .= $dateString . "\r<br>\r";

                    // if the apiversion is 3 or higher then this is taopix designer version 3 and later so we include the additional data
                    if ($apiVersion >= 3)
                    {
                        $itemData .= $item['filesize'] . "\r<br>\r";
                        $itemData .= $item['filechecksum'] . "\r<br>\r";
                        $itemData .= $item['updatepriority'] . "\r<br>\r";
                    }
                }


                // currency
                // if the apiversion is 3 or higher then this is taopix designer version 3 and later so we include the currency data
                if ($apiVersion >= 3)
                {
                    $itemData .= "CURRENCY\r<br>\r";
                    $itemData .= '1' . "\r<br>\r";
                    $itemData .= $pResultArray['currencycode'] . "\r<br>\r";
                    $itemData .= $pResultArray['currencyname'] . "\r<br>\r";
                    $itemData .= $pResultArray['currencyisonumber'] . "\r<br>\r";
                    $itemData .= $pResultArray['currencysymbol'] . "\r<br>\r";

                    if ($pResultArray['currencysymbolatfront'] == 1)
                    {
                        $itemData .= "TRUE\r<br>\r";
                    }
                    else
                    {
                        $itemData .= "FALSE\r<br>\r";
                    }

                    $itemData .= $pResultArray['currencydecimalplaces'] . "\r<br>\r";
                    $itemData .= $pResultArray['currencyexchangerate'] . "\r<br>\r";
                }


                // application
                $itemData .= "CLIENT\r<br>\r";
                $itemData .= '1' . "\r<br>\r";
                $itemData .= $ftpDetails['ftpurl'] . "\r<br>\r";
                $itemData .= $ftpDetails['ftpuser'] . "\r<br>\r";
                $itemData .= $ftpDetails['ftppass'] . "\r<br>\r";
                $itemData .= UtilsObj::correctPath($ac_config['FTPCLIENTSROOTPATH']) . $brandPath. "\r<br>\r";

                // if the apiversion is 3 or higher then this is taopix designer version 3 and later so we include the additional data
                if ($apiVersion >= 3)
                {
                    $itemData .= $pResultArray['applicationbuild']['macversion'] . "\r<br>\r";
                    $itemData .= $pResultArray['applicationbuild']['macarchivefilename'] . "\r<br>\r";
                    $itemData .= $pResultArray['applicationbuild']['macexecutablefilename'] . "\r<br>\r";
                    $itemData .= $pResultArray['applicationbuild']['macarchivefilesize'] . "\r<br>\r";
                    $itemData .= $pResultArray['applicationbuild']['macarchivechecksum'] . "\r<br>\r";
                    $itemData .= $pResultArray['applicationbuild']['macupdatepriority'] . "\r<br>\r";

                    if ($pResultArray['applicationbuild']['machaspreview'] == 1)
                    {
                        $itemData .= "TRUE\r<br>\r";
                    }
                    else
                    {
                        $itemData .= "FALSE\r<br>\r";
                    }

                    $itemData .= $pResultArray['applicationbuild']['win32version'] . "\r<br>\r";
                    $itemData .= $pResultArray['applicationbuild']['win32archivefilename'] . "\r<br>\r";
                    $itemData .= $pResultArray['applicationbuild']['win32executablefilename'] . "\r<br>\r";
                    $itemData .= $pResultArray['applicationbuild']['win32archivefilesize'] . "\r<br>\r";
                    $itemData .= $pResultArray['applicationbuild']['win32archivechecksum'] . "\r<br>\r";
                    $itemData .= $pResultArray['applicationbuild']['win32updatepriority'] . "\r<br>\r";

                    if ($pResultArray['applicationbuild']['win32haspreview'] == 1)
                    {
                        $itemData .= "TRUE\r<br>\r";
                    }
                    else
                    {
                        $itemData .= "FALSE\r<br>\r";
                    }
                }
                else
                {
                    // older than taopix version 3
                    $itemData .= $pResultArray['applicationbuild']['macversion'] . "\r<br>\r";
                    $itemData .= $pResultArray['applicationbuild']['macarchivefilename'] . "\r<br>\r";
                    $itemData .= $pResultArray['applicationbuild']['macexecutablefilename'] . "\r<br>\r";
                    $itemData .= $pResultArray['applicationbuild']['win32version'] . "\r<br>\r";
                    $itemData .= $pResultArray['applicationbuild']['win32archivefilename'] . "\r<br>\r";
                    $itemData .= $pResultArray['applicationbuild']['win32executablefilename'] . "\r<br>\r";
                }

                // calendar data
                if (($apiVersion >= 8) && ($pResultArray['calendardataversion'] != ''))
                {
                    $itemData .= "CALENDAREVENTSETDATA\r<br>\r";
                    $itemData .= '1' . "\r<br>\r";
                    $itemData .= $pResultArray['calendardataversion'] . "\r<br>\r";
                    $itemData .= base64_encode($pResultArray['calendardata']) . "\r<br>\r";
                }

                // brands
                $itemData .= "BRANDING\r<br>\r";
                $itemData .= '1' . "\r<br>\r";
                $itemList = $pResultArray['webbrands'];
                $itemCount = count($itemList);
                $itemData .= $itemCount . "\r<br>\r";
                for ($i = 0; $i < $itemCount; $i++)
                {
                    $item = $itemList[$i];

                    $itemData .= $item['code'] . "\r<br>\r";
                    $itemData .= $item['applicationname'] . "\r<br>\r";
                }


                // upload refs (only include if we have been provided with some)
                $itemList = $pResultArray['uploadrefslist'];
                $itemCount = count($itemList);
                if ($itemCount > 0)
                {
                    $itemData .= "UPLOADREFS\r<br>\r";
                    $itemData .= '1' . "\r<br>\r";
                    $itemData .= $itemCount . "\r<br>\r";
                    for ($i = 0; $i < $itemCount; $i++)
                    {
                        $theItem = $itemList[$i];

                        $itemData .= $theItem['uploadbatchref'] . "\r<br>\r";
                        $itemData .= $theItem['uploadref'] . "\r<br>\r";
                        $itemData .= $theItem['status'] . "\r<br>\r";
                        $itemData .= $theItem['status2'] . "\r<br>\r";

                        if ($theItem['canmodify'] == 1)
                        {
                            $itemData .= "TRUE\r<br>\r";
                        }
                        else
                        {
                            $itemData .= "FALSE\r<br>\r";
                        }

                        if ($theItem['canupload'] == 1)
                        {
                            $itemData .= "TRUE\r<br>\r";
                        }
                        else
                        {
                            $itemData .= "FALSE\r<br>\r";
                        }

                        if ($theItem['canuploadproductcodeoverride'] == 1)
                        {
                            $itemData .= "TRUE\r<br>\r";
                        }
                        else
                        {
                            $itemData .= "FALSE\r<br>\r";
                        }

                        if ($theItem['canuploadpagecountoverride'] == 1)
                        {
                            $itemData .= "TRUE\r<br>\r";
                        }
                        else
                        {
                            $itemData .= "FALSE\r<br>\r";
                        }

                        if ($theItem['temporder'] == 1)
                        {
                            $itemData .= "TRUE\r<br>\r";
                        }
                        else
                        {
                            $itemData .= "FALSE\r<br>\r";
                        }

                        $itemData .= $theItem['temporderexpirydate'] . "\r<br>\r";
                    }
                }


                // set result code
                $smarty->assign('result', 'OK');

                // set the result version
                if ($apiVersion == 1)
                {
                    $smarty->assign('version', '1');
                }
                else
                {
                    $smarty->assign('version', '2');
                }

                // transfer the data to the template
                $smarty->assign('resultParam1', $itemData);
                break;
            default:
                // an error occurred
                SmartyObj::replaceParams($smarty, $pResultArray['result'], $pResultArray['resultparam']);
                $smarty->assign('result', 'ERROR');
                $smarty->assign('version', '1');
                $smarty->assign('resultParam1', $smarty->get_template_vars($pResultArray['result']));
                break;
        }

        // if the apiversion is 3 or higher then this is taopix designer version 3 and later so we encrypt the output
        if ($apiVersion >= 3)
        {
            self::encryptAndSendResponse($smarty->fetch('appapi3.tpl'));
        }
        else
        {
            $smarty->display('appapi3.tpl');
        }
    }

	static function uploadSystemDataInit($pResultArray)
	{
		global $ac_config;

		$ftpDetails = UtilsObj::getAutoUpdateFTPDetails();

		$smarty = self::newAppAPISmarty($pResultArray['languagecode']);

		if ($pResultArray['result'] == '')
		{
			$apiVersion = $pResultArray['apiversion'];

			$ftpPath = UtilsObj::correctPath($ac_config['FTPSYSTEMUPLOADROOTPATH']);
			$brandPath = '';

			switch ($pResultArray['type'])
			{
				case TPX_APPLICATION_FILE_TYPE_PRODUCTCOLLECTION:
					$ftpPath .= 'Products';

					break;
				case TPX_APPLICATION_FILE_TYPE_MASK:
					$ftpPath .= 'Masks';
					$brandPath = $pResultArray['brandpath'];

					break;
				case TPX_APPLICATION_FILE_TYPE_BACKGROUND:
					$ftpPath .= 'Backgrounds';
					$brandPath = $pResultArray['brandpath'];

					break;
				case TPX_APPLICATION_FILE_TYPE_PICTURE:
					$ftpPath .= 'Scrapbook';
					$brandPath = $pResultArray['brandpath'];

					break;
				case TPX_APPLICATION_FILE_TYPE_FRAME:
					$ftpPath .= 'Frames';
					$brandPath = $pResultArray['brandpath'];

					break;
				case TPX_APPLICATION_FILE_TYPE_LICENSEKEY:
					$ftpPath .= 'LicenseKeys';

					break;
				case TPX_APPLICATION_FILE_TYPE_APPLICATION_BUILD:
					$ftpPath .= 'Clients';
					$brandPath = $pResultArray['brandpath'];

					break;
			}

			$ftpPath .= '/' . $brandPath;

			$smarty->assign('result', 'OK');
			$smarty->assign('version', '1');
			$smarty->assign('resultParam1', $ftpDetails['ftpurl']);
			$smarty->assign('resultParam2', $ftpDetails['ftpuser']);
			$smarty->assign('resultParam3', $ftpDetails['ftppass']);
			$smarty->assign('resultParam4', $ftpPath);
		}
		else
		{
			SmartyObj::replaceParams($smarty, $pResultArray['result'], $pResultArray['resultparam']);
			$smarty->assign('result', 'ERROR');
			$smarty->assign('version', '1');
			$smarty->assign('resultParam1', $smarty->get_template_vars($pResultArray['result']));
			$smarty->assign('resultParam2', '');
			$smarty->assign('resultParam3', '');
			$smarty->assign('resultParam4', '');
		}

		$smarty->assign('resultParam5', '');
		$smarty->assign('resultParam6', '');

	    self::encryptAndSendResponse($smarty->fetch('appapi.tpl'));
	}


	static function uploadSystemUpdate($resultArray)
	{
		global $ac_config;

		$apiVersion = $resultArray['apiversion'];

		$smarty = self::newAppAPISmarty($resultArray['languageCode']);

		switch ($resultArray['result'])
		{
			case '':
				$smarty->assign('result', 'OK');
				$smarty->assign('version', '1');
				$smarty->assign('resultParam1', '');
				break;
			default:
				// an error occurred
				SmartyObj::replaceParams($smarty, $resultArray['result'], $resultArray['resultparam']);
				$smarty->assign('result', 'ERROR');
				$smarty->assign('version', '1');
				$smarty->assign('resultParam1', $smarty->get_template_vars($resultArray['result']));
				break;
		}

	    self::encryptAndSendResponse($smarty->fetch('appapi3.tpl'));
	}


    /**
   	* Returns the dynamic graphics back to the calling application
   	*
   	* @static
	*
	* @param array $pSourceString
   	*
   	* @author Kevin Gale
	* @since Version 3.3.0
 	*/
    static function getDynamicGraphics($pResultArray)
	{
		global $ac_config;

		$smarty = self::newAppAPISmarty();

		$itemData = '';

		$itemList = $pResultArray['logolist'];
		$itemCount = count($itemList);
		for ($i = 0; $i < $itemCount; $i++)
        {
            $item = $itemList[$i];

            $itemData .= $item['type'] . "\r<br>\r";
            $itemData .= $item['id'] . "\r<br>\r";
            $itemData .= $item['datemodified'] . "\r<br>\r";
            $itemData .= base64_encode($item['data']) . "\r<br>\r";
            $itemData .= $item['startdate'] . "\r<br>\r";
            $itemData .= $item['enddate'] . "\r<br>\r";
        }

		$smarty->assign('result', 'OK');
		$smarty->assign('version', '1');
		$smarty->assign('resultParam1', $pResultArray['serverdatetime']);
		$smarty->assign('resultParam2', $itemCount);
		$smarty->assign('resultParam3', $itemData);
		$smarty->assign('resultParam4', '');
		$smarty->assign('resultParam5', '');
		$smarty->assign('resultParam6', '');

		self::encryptAndSendResponse($smarty->fetch('appapi.tpl'));
	}

    static function uploadProjectThumbnailsInit($pResultArray)
    {
		$smarty = self::newAppAPISmarty();

        $smarty->assign('result', 'OK');
		$smarty->assign('version', '1');
		$smarty->assign('resultParam1', join(",", $pResultArray));

        self::encryptAndSendResponse($smarty->fetch('appapi.tpl'));
    }

    static function uploadProjectThumbnails($pResultArray)
    {
        $smarty = self::newAppAPISmarty();

        //the desktop designer only needs to know that the attempted operation has completed
        $smarty->assign('result', 'OK');
		$smarty->assign('version', '1');

        self::encryptAndSendResponse($smarty->fetch('appapi.tpl'));

    }

    static function getAssetServiceRequestInformation($pResultArray)
    {
        $smarty = self::newAppAPISmarty();

        if ($pResultArray['error'] === '')
        {
            $smarty->assign('result', 'OK');
            $smarty->assign('version', '1');
            $smarty->assign('resultParam1', $pResultArray['data']['token']);
            $smarty->assign('resultParam2', $pResultArray['data']['redirecturi']);
            $smarty->assign('resultParam3', $pResultArray['data']['checkurl']);    
        }
        else
        {
            $smarty->assign('result', 'ERROR');
            $smarty->assign('version', '1');
            $smarty->assign('resultParam1', $smarty->get_template_vars($pResultArray['error']));
        }

        self::encryptAndSendResponse($smarty->fetch('appapi.tpl'));
    }


    static function getAssetServiceAuthCode($pResultArray)
    {
        $smarty = self::newAppAPISmarty();

        if ($pResultArray['error'] === '')
        {
            $smarty->assign('result', 'OK');
            $smarty->assign('version', '1');
            $smarty->assign('resultParam1', $pResultArray['data']['success']);
            $smarty->assign('resultParam2', $pResultArray['data']['code']);
        }
        else
        {
            $smarty->assign('result', 'ERROR');
            $smarty->assign('version', '1');
            $smarty->assign('resultParam1', $smarty->get_template_vars($pResultArray['error']));
        }

        self::encryptAndSendResponse($smarty->fetch('appapi.tpl'));
    }
    
    static function getAccountPagesURL($pAccountPagesURL)
    {
        $smarty = self::newAppAPISmarty();

        $smarty->assign('result', 'OK');
		$smarty->assign('version', '1');
		$smarty->assign('resultParam1', $pAccountPagesURL);

        self::encryptAndSendResponse($smarty->fetch('appapi.tpl'));
    }

	static function unknownCommand()
	{
	    $apiVersion = (int)UtilsObj::getPOSTParam('version', '1');

		$smarty = self::newAppAPISmarty();

		$smarty->assign('result', 'ERROR');
		$smarty->assign('version', '1');
	    $smarty->assign('resultParam1', 'Unknown Command.');
	    $smarty->assign('resultParam2', '');
	    $smarty->assign('resultParam3', '');
	    $smarty->assign('resultParam4', '');
	    $smarty->assign('resultParam5', '');
	    $smarty->assign('resultParam6', '');

	    // if the apiversion is 3 or higher then this is taopix designer version 3 and later so we encrypt the output
	    if ($apiVersion >= 3)
	    {
	        self::encryptAndSendResponse($smarty->fetch('appapi.tpl'));
	    }
	    else
	    {
	        $smarty->display('appapi.tpl');
	    }
	}
}

?>
