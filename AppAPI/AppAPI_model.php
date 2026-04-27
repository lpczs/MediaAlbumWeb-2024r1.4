<?php
use PricingEngine\OrderLoader;

class AppAPI_model
{
    /**
    * Decrypts an encrypted string posted to the server
    *
    * @return string
    *   the result will contain the decrypted data
    *
    * @author Kevin Gale
    * @version 2017.1.0
    * @since Version 2017.1.0
    */
	static function decodeEncryptedData($pKey, $pEncryptedData)
    {
    	$result = '';

        if ($pEncryptedData != '')
        {
            $pos = strpos($pEncryptedData, '.');
            $strLen = substr($pEncryptedData, 0, $pos);
            $strLen = (int)$strLen;

            if ($strLen < 0)
            {
                $strLen = abs($strLen);
                $compressed = true;
            }
            else
            {
                $compressed = false;
            }

            $pEncryptedData = substr($pEncryptedData, $pos + 1);

            $pos = strpos($pEncryptedData, '.');
            $iv = base64_decode(substr($pEncryptedData, 0, $pos));
            $pEncryptedData = substr($pEncryptedData, $pos + 1);
            $pEncryptedData = base64_decode($pEncryptedData);

            $pEncryptedData = mcrypt_decrypt(MCRYPT_BLOWFISH, $pKey, $pEncryptedData, MCRYPT_MODE_CBC, $iv);
            $result = substr($pEncryptedData, 0, $strLen);

            if ($compressed)
            {
                $pos = strpos($result, '.');
                $strLen = substr($result, 0, $pos);
                $strLen = (int)$strLen;
                $result = substr($result, $pos + 1);
                $result = gzuncompress($result, $strLen);
            }
        }

        return $result;
    }

    /**
    * Decrypts the new designer api parameters that have been posted to the server
    *
    * @return array
    *   the result array will contain either an empty array or a key/value array containing the parameters
    *
    * @author Kevin Gale
    * @version 3.0.0
    * @since Version 3.0.0
    */
    static function decodeDesignerEncryptedData($pEncryptedData, $pAppVersion, $pOwnerCode, $pAppDataVersion)
    {
    	$result = '';

        if ($pEncryptedData != '')
        {
        	$key = $pAppVersion . 'TDAPIIN' . $pOwnerCode . $pAppDataVersion;

        	$result = self::decodeEncryptedData($key, $pEncryptedData);
		}

        return $result;
    }


    static function decodeDesignerEncryptedDataPOST($encryptedString)
    {
        $appVersion = UtilsObj::getPOSTParam('appversion', '');
        $ownerCode = UtilsObj::getPOSTParam('ownercode', '');
        $appDataVersion = UtilsObj::getPOSTParam('appdataversion', '');

        $decryptedString = self::decodeDesignerEncryptedData($encryptedString, $appVersion, $ownerCode, $appDataVersion);

        return $decryptedString;
    }


    static function decodeDesignerCommandParams()
    {
        $resultArray = Array();

        $dataString = UtilsObj::getPOSTParam('data', '');
        if ($dataString != '')
        {
            $commandDataArray = explode("\n", self::decodeDesignerEncryptedDataPOST($dataString));
            foreach ($commandDataArray as &$item)
            {
                $pieces = explode('=', $item);
                $option = trim($pieces[0]);
                if (count($pieces) > 1)
                {
                    $value = trim($pieces[1]);
                }
                else
                {
                    $value = '';
                }
                $resultArray[$option] = $value;
            }
        }

        return $resultArray;
    }


    static function includeExternalShoppingCart()
    {
        global $gSession;
		$gSession['order']['externalcartscriptexists'] = 0;

        // include external shopping cart script.
        // first check if banded script exists. If not then check for global script.
        if (file_exists('../Customise/scripts/EDL_ExternalShoppingCart_' . $gSession['webbrandcode'] . '.php'))
        {
            require_once('../Customise/scripts/EDL_ExternalShoppingCart_' . $gSession['webbrandcode'] . '.php');
			$gSession['order']['externalcartscriptexists'] = 1;
        }
        elseif (file_exists('../Customise/scripts/EDL_ExternalShoppingCart.php'))
        {
            require_once('../Customise/scripts/EDL_ExternalShoppingCart.php');
			$gSession['order']['externalcartscriptexists'] = 1;
        }
    }

    static function getCartProjectRefsFromSession()
    {
        global $gSession;

        $cartProjectRefArray = array();

        foreach ($gSession['items'] as $lineItem)
        {
            $cartProjectRefArray[$lineItem['itemprojectref']] = $lineItem['itemprojectref'];
        }

        return $cartProjectRefArray;
    }


    static function prepareOrderData()
    {
        global $ac_config;
        global $gSession;

        $result = '';
        $orderDataArray = Array();

        $headerArray = Array();
        $cartArray = Array();

        $headerArray['apiversion'] = (int)UtilsObj::getPOSTParam('version', '1');
        $headerArray['appversion'] = UtilsObj::getPOSTParam('appversion', '');
        $headerArray['appdataversion'] = UtilsObj::getPOSTParam('appdataversion', '');
        $headerArray['uuid'] = UtilsObj::getPOSTParam('uuid', '');
        $headerArray['languagecode'] = UtilsObj::getPOSTParam('langcode', 'en');
        $headerArray['batchref'] = UtilsObj::getPOSTParam('batchref', '');
        $headerArray['groupdata'] = UtilsObj::getPOSTParam('codedata', '');
        $headerArray['canuseexternalcart'] = (int)UtilsObj::getPOSTParam('enableexternalcart', '1');
        $headerArray['outputdeliverymethods'] = 'PRODUCT';
        $headerArray['basketapiworkflowtype'] = TPX_BASKETWORKFLOWTYPE_NORMAL;
        $headerArray['highlevelbasketref'] = '';
        $headerArray['ssotoken'] = '';
        $headerArray['ssoprivatedata'] = array();

        $uploadAppVersion = '';
        $uploadAppPlatform = '';
        $uploadAppCPUType = '';
        $uploadAppOSVersion = '';

        if (array_key_exists('ownercode', $_POST))
        {
            $headerArray['ownercode'] = $_POST['ownercode'];
            if ($headerArray['ownercode'] == '')
            {
                $result = 'str_NoOwnerCode';
            }
        }
        else
        {
            $result = 'str_NoOwnerCode';
        }

        if ($result == '')
        {
            if (array_key_exists('code', $_POST))
            {
                $headerArray['groupcode'] = $_POST['code'];
                if ($headerArray['groupcode'] == '')
                {
                    $result = 'str_NoGroupCode';
                }
            }
            else
            {
                $result = 'str_NoGroupCode';
            }
        }

        if ($result == '')
        {
            if (array_key_exists('name', $_POST))
            {
                $headerArray['groupname'] = $_POST['name'];
            }
            else
            {
                $result = 'str_NoGroupName';
            }
        }

        if ($result == '')
        {
            if (array_key_exists('address1', $_POST))
            {
                $headerArray['groupaddress1'] = $_POST['address1'];
            }
            else
            {
                $result = 'str_NoGroupAddress';
            }

            if (array_key_exists('address2', $_POST))
            {
                $headerArray['groupaddress2'] = $_POST['address2'];
            }
            else
            {
                $result = 'str_NoGroupAddress';
            }

            if (array_key_exists('address3', $_POST))
            {
                $headerArray['groupaddress3'] = $_POST['address3'];
            }
            else
            {
                $result = 'str_NoGroupAddress';
            }

            if (array_key_exists('address4', $_POST))
            {
                $headerArray['groupaddress4'] = $_POST['address4'];
            }
            else
            {
                $result = 'str_NoGroupAddress';
            }

            if (array_key_exists('city', $_POST))
            {
                $headerArray['groupaddresscity'] = $_POST['city'];
            }
            else
            {
                $result = 'str_NoGroupAddress';
            }

            if (array_key_exists('county', $_POST))
            {
                $headerArray['groupaddresscounty'] = $_POST['county'];
            }
            else
            {
                $result = 'str_NoGroupAddress';
            }

            if (array_key_exists('state', $_POST))
            {
                $headerArray['groupaddressstate'] = $_POST['state'];
            }
            else
            {
                $result = 'str_NoGroupAddress';
            }

            if (array_key_exists('email', $_POST))
            {
                $headerArray['groupemailaddress'] = $_POST['email'];
            }
            else
            {
                $result = 'str_NoGroupEmailAddress';
            }

            if (array_key_exists('telephone', $_POST))
            {
                $headerArray['grouptelephonenumber'] = $_POST['telephone'];
            }
            else
            {
                $result = 'str_NoGroupTelephoneNumber';
            }

            if (array_key_exists('contactfname', $_POST))
            {
                $headerArray['groupcontactfirstname'] = $_POST['contactfname'];
            }
            else
            {
                $result = 'str_NoGroupContactName';
            }

            if (array_key_exists('contactlname', $_POST))
            {
                $headerArray['groupcontactlastname'] = $_POST['contactlname'];
            }
            else
            {
                $result = 'str_NoGroupContactName';
            }
        }

        if ($result == '')
        {
            if (array_key_exists('postcode', $_POST))
            {
                $headerArray['grouppostcode'] = $_POST['postcode'];
            }
            else
            {
                $result = 'str_NoGroupPostCode';
            }
        }

        if ($result == '')
        {
            if (array_key_exists('countrycode', $_POST))
            {
                $headerArray['groupcountrycode'] = $_POST['countrycode'];
            }
            else
            {
                $result = 'str_NoGroupCountryCode';
            }

            if (array_key_exists('devicesettings', $_POST))
            {
                $headerArray['devicesettings'] = $_POST['devicesettings'];
            }
            else
            {
                $headerArray['devicesettings'] = 'unknown';
            }
        }


        $cartItemsCount = 0;
        $projectCount = '';
        if ($result == '')
        {
            if (array_key_exists('projectcount', $_POST))
            {
                $appendSuffix = true;
                $projectCount = (int)UtilsObj::getPOSTParam('projectcount', 1);
            }
            else
            {
                $appendSuffix = false;
                $projectCount = 1;
            }

            for ($i = 1; $i <= $projectCount; $i++)
            {
                $cartItemArray = Array();
                $cartItemArray['source'] = TPX_SOURCE_DESKTOP;

                if ($appendSuffix)
                {
                    $suffixString = $i;
                }
                else
                {
                    $suffixString = '';
                }


                if (array_key_exists('productcode' . $suffixString, $_POST))
                {
                    $cartItemArray['productcode'] = $_POST['productcode' . $suffixString];
                }
                else
                {
                    $result = 'str_NoProductCode';
                    break;
                }


                if (array_key_exists('projectname' . $suffixString, $_POST))
                {
                    $cartItemArray['projectname'] = $_POST['projectname' . $suffixString];
                    if ($cartItemArray['projectname'] == '')
                    {
                        $result = 'str_NoProjectName';
                        break;
                    }
                }
                else
                {
                    $result = 'str_NoProjectName';
                    break;
                }


                if (array_key_exists('productheight' . $suffixString, $_POST))
                {
                    $cartItemArray['productheight'] = $_POST['productheight' . $suffixString];
                }
                else
                {
                    $result = 'str_NoProductHeight';
                    break;
                }


                if (array_key_exists('productwidth' . $suffixString, $_POST))
                {
                    $cartItemArray['productwidth'] = $_POST['productwidth' . $suffixString];
                }
                else
                {
                    $result = 'str_NoProductWidth';
                    break;
                }


                if (array_key_exists('covercode' . $suffixString, $_POST))
                {
                    $cartItemArray['covercode'] = $_POST['covercode' . $suffixString];
                }
                else
                {
                    $result = 'str_NoCoverCode';
                    break;
                }


                if (array_key_exists('papercode' . $suffixString, $_POST))
                {
                    $cartItemArray['papercode'] = $_POST['papercode' . $suffixString];
                }
                else
                {
                    $result = 'str_NoPaperCode';
                    break;
                }


                if (array_key_exists('pagecount' . $suffixString, $_POST))
                {
                    $cartItemArray['pagecount'] = $_POST['pagecount' . $suffixString];
                }
                else
                {
                    $result = 'str_NoPageCount';
                    break;
                }

                if (array_key_exists('uploadref' . $suffixString, $_POST))
                {
                    $cartItemArray['uploadref'] = $_POST['uploadref' . $suffixString];
                }
                else
                {
                    $result = 'str_NoUploadRef';
                    break;
                }

                $cartItemArray['projectref'] = UtilsObj::getPOSTParam('projectref' . $suffixString, '');
                $cartItemArray['projectreforig'] = UtilsObj::getPOSTParam('projectreforig' . $suffixString, '');
                $cartItemArray['producttype'] = UtilsObj::getPOSTParam('producttype' . $suffixString, 0);
                $cartItemArray['productpageformat'] = UtilsObj::getPOSTParam('pageformat' . $suffixString, 0);
                $cartItemArray['productspreadformat'] = UtilsObj::getPOSTParam('spreadpageformat' . $suffixString, 0);
                $cartItemArray['productcover1format'] = UtilsObj::getPOSTParam('cover1format' . $suffixString, 0);
                $cartItemArray['productcover2format'] = UtilsObj::getPOSTParam('cover2format' . $suffixString, 0);
                $cartItemArray['productoutputformat'] = UtilsObj::getPOSTParam('productoutputformat' . $suffixString, 0);
                $cartItemArray['projectaimode'] = (int) UtilsObj::getPOSTParam('projectaimode' . $suffixString, TPX_AIMODE_DISABLED);
				$cartItemArray['projectaiprovider'] = UtilsObj::getPostParam('projectaiprovider' . $suffixString, 0);
				$cartItemArray['components'] = UtilsObj::getPostParam('componenttreedata' . $suffixString, '');

                $productCollectionArray = DatabaseObj::getProductCollectionFromCode(UtilsObj::getPOSTParam('productcollectioncode' . $suffixString, $cartItemArray['productcode']));

                $cartItemArray['collectioncode'] = $productCollectionArray['code'];
                $cartItemArray['collectionname'] = $productCollectionArray['name'];

                $cartItemArray['shareid'] = 0;
                $cartItemArray['origorderitemid'] = 0;
                $cartItemArray['uploadgroupcode'] = $headerArray['groupcode'];
                $cartItemArray['uploadorderid'] = 0;
                $cartItemArray['uploadordernumber'] = '';
                $cartItemArray['uploadorderitemid'] = 0;
                $cartItemArray['canupload'] = 1;
                $cartItemArray['previewsonline'] = 0;
                $cartItemArray['uploadappversion'] = $uploadAppVersion;
                $cartItemArray['uploadappplatform'] = $uploadAppPlatform;
                $cartItemArray['uploadappcputype'] = $uploadAppCPUType;
                $cartItemArray['uploadapposversion'] = $uploadAppOSVersion;
                $cartItemArray['projectstarttime'] = '';
                $cartItemArray['projectduration'] = 0;
                $cartItemArray['uploaddatasize'] = 0;
                $cartItemArray['uploadduration'] = 0;
                $cartItemArray['productcollectionorigownercode'] = UtilsObj::getPOSTParam('productcollectionorigownercode' . $suffixString, $headerArray['ownercode']);
                $cartItemArray['componenttreeproductcode'] = '';

                $cartArray[] = $cartItemArray;

				$resultArray['pictures' . $suffixString] = UtilsObj::getPOSTParam('pictures' . $suffixString, '');
                $resultArray['assets' . $suffixString] = UtilsObj::getPOSTParam('assets' . $suffixString, '');
                $resultArray['calendardata' . $suffixString] = UtilsObj::getPOSTParam('calendardata' . $suffixString, '');
                $resultArray['retroprints' . $suffixString] = UtilsObj::getPOSTParam('retroprints' . $suffixString, '');

                $cartItemsCount++;
            }
        }

        $resultArray['cartitemcount'] = $cartItemsCount;
        $resultArray['headerarray'] = $headerArray;
        $resultArray['cartarray'] = $cartArray;
        $resultArray['result'] = $result;

        return $resultArray;
    }

    static function order($pOrderDataArray)
    {
        global $ac_config;
        global $gSession;
        global $gConstants;

        $headerInfo = $pOrderDataArray['headerarray'];
        $cartArray = $pOrderDataArray['cartarray'];
        $cartItemsCount = $pOrderDataArray['cartitemcount'];
		$retroPrintsArray = [];

        $result = '';
        $resultParam = '';
        $recordID = 0;
        $ownerCode = $headerInfo['ownercode'];
        $groupCode = $headerInfo['groupcode'];
        $groupData = $headerInfo['groupdata'];
        $groupName = $headerInfo['groupname'];
        $groupAddress1 = $headerInfo['groupaddress1'];
        $groupAddress2 = $headerInfo['groupaddress2'];
        $groupAddress3 = $headerInfo['groupaddress3'];
        $groupAddress4 = $headerInfo['groupaddress4'];
        $groupAddressCity = $headerInfo['groupaddresscity'];
        $groupAddressCounty = $headerInfo['groupaddresscounty'];
        $groupAddressState = $headerInfo['groupaddressstate'];
        $groupPostCode = $headerInfo['grouppostcode'];
        $groupCountryCode = $headerInfo['groupcountrycode'];
        $groupEmailAddress = $headerInfo['groupemailaddress'];
        $groupTelephoneNumber = $headerInfo['grouptelephonenumber'];
        $groupContactFirstName = $headerInfo['groupcontactfirstname'];
        $groupContactLastName = $headerInfo['groupcontactlastname'];
        $basketAPIWorkFlowType = $headerInfo['basketapiworkflowtype'];
        $uuid = $headerInfo['uuid'];
        $highLevelBasketRef = $headerInfo['highlevelbasketref'];
        $ssoToken = $headerInfo['ssotoken'];
        $ssoPrivateData = $headerInfo['ssoprivatedata'];
        $pageCount = 0;
        $shoppingCartType = TPX_SHOPPINGCARTTYPE_INTERNAL;
        $shoppingCartURL = '';
		$shoppingCartData = [
			'method' => 'shoppingcarturl', // postmessage|shoppingcarturl
			'postmessagejavascripttarget' => '',
			'postmessagetargeturl' => '',
			'data' => []
		];
        $outputDeliveryMethods = $headerInfo['outputdeliverymethods'];
        $dataVersion = $headerInfo['appdataversion'];
        $jobTicketTemplate = '';
        $canCreateAccounts = 0;
        $orderPageCount = 0;
        $coverMaxPageCount = 0;
        $paperMaxPageCount = 0;
        $currencyExchangeRate = 1.0000;
        $useDefaultCurrency = 1;
        $currencyCode = '';
        $currencyDecimalPlaces = 2;
        $statusURL = '';
        $apiVersion = $headerInfo['apiversion'];
        $appVersion = $headerInfo['appversion'];
        $dataVersion = $headerInfo['appdataversion'];
        $languageCode = $headerInfo['languagecode'];
        $batchRef = $headerInfo['batchref'];
        $canUseExternalCart = $headerInfo['canuseexternalcart'];
        $outputDeliveryMethods = $headerInfo['outputdeliverymethods'];
        $deviceSettings = $headerInfo['devicesettings'];
        $webBrandCode = '';
		$currency = array();
		$AIComponentArray = array();
		$projectThumbnailData = [];
        $inactiveProductCollectionCode = '';
        $inactiveLayoutCode = '';

        // copy the cart array into the processed items array
        // this may be replaced later when handling an order that is in progress
        $processedItemsArray = $cartArray;

        // if we have no errors determine if the user's group code exists in the database
        if ($result == '')
        {
			$licenseKeyArray = DatabaseObj::getLicenseKeyFromCode($groupCode);

			$webBrandCode = $licenseKeyArray['webbrandcode'];

            // Set correct companycode for the customer. Check to see if the license key belongs to a brand if it does
            // use the brand company code. If not use the license key companycode
            $companyCode = $licenseKeyArray['companyCode'];

			$brandingArray = DatabaseObj::getBrandingFromCode($webBrandCode);

            if ($webBrandCode != '')
            {
                $companyCode = $brandingArray['companycode'];
            }

            $result = $licenseKeyArray['result'];
            $resultParam = $licenseKeyArray['resultparam'];

            // if no error has occurred we have found a license key with the correct details
            // check to see if it is active
            if ($result == '')
            {
                if ($licenseKeyArray['isactive'] == 0)
                {
                    $result = 'str_ErrorAccountNotActive';
                }

                $canCreateAccounts = $licenseKeyArray['cancreateaccounts'];
            }
        }

        // get exchange rate
        if ($result == '')
        {
            $useDefaultCurrency = $licenseKeyArray['usedefaultcurrency'];
            $currencyCode = $licenseKeyArray['currencycode'];
            if ($useDefaultCurrency == 1)
            {
                $currencyExchangeRate = 1;
            }
            else
            {
                // get exchangerate from database;
                $currency = DatabaseObj::getCurrency($currencyCode);
                if ($result == '')
                {
                    $currencyExchangeRate = $currency['exchangerate'];
                    $currencyDecimalPlaces = $currency['decimalplaces'];
                }
            }
        }


        if ($result == '')
        {
            for ($i = 0; $i < $cartItemsCount; $i++)
            {
                $cartItemArray = &$cartArray[$i];

                $productArray = DatabaseObj::getProductFromCollectionCodeAndLayoutCode($cartItemArray['collectioncode'], $cartItemArray['productcode']);

                $result = $productArray['result'];
                $resultParam = $productArray['resultparam'];

                // if we have no errors then the product matches so copy some properties from the array and check to see if the product is active
                if ($result == '')
                {
					$cartItemArray['productname'] = $productArray['name'];
					$cartItemArray['productskucode'] = $productArray['skucode'];
					$cartItemArray['productdefaultpagecount'] = $productArray['defaultpagecount'];
					$cartItemArray['producttaxlevel'] = $productArray['taxlevel'];
					$cartItemArray['productunitcost'] = $productArray['unitcost'];
					$cartItemArray['productunitweight'] = $productArray['weight'];
					$cartItemArray['productoptions'] = $productArray['productoptions'];
					$cartItemArray['pricetransformationstage'] = $productArray['pricetransformationstage'];

                    if ($productArray['isactive'] == 0)
                    {
                        $result = 'str_ProductCodeNotActive';
                        $inactiveProductCollectionCode = $cartItemArray['collectioncode'];
                        $inactiveLayoutCode = $cartItemArray['productcode'];

                        

                        break;
                    }

                    // get the code for the product tree linking for the product
                    $productLinkingArray = DatabaseObj::getApplicableProductLinkCode($cartItemArray['productcode']);

                    if ($productLinkingArray['error'] == '')
                    {
                        if ($productLinkingArray['linkedcode'] != '')
                        {
                            $cartItemArray['componenttreeproductcode'] = $productLinkingArray['linkedcode'];
                        }
                        else
                        {
                            $cartItemArray['componenttreeproductcode'] = $cartItemArray['productcode'];
                        }
                    }
                    else
                    {
                        $result = $productLinkingArray['error'];
                        $resultParam = $productLinkingArray['errorparam'];
                        $cartItemArray['componenttreeproductcode'] = '';

                        // we cannot price this product safely
                        break;
                    }
                }

                // only need to build assets, pictures and calendar customisations if we in a normal workflow or adding to a basket via high/low level apis
				if (($basketAPIWorkFlowType != TPX_BASKETWORKFLOWTYPE_LOWLEVELAPIEXTERNALCHECKOUT) && ($basketAPIWorkFlowType != TPX_BASKETWORKFLOWTYPE_HIGHLEVELCHECKOUT))
                {
                    if ($result == '')
                    {
                        // index to store the line count
                        $k = $i + 1;

                        // create an empty array to store the external assets used in the project
						$assetsArray = Array();

                        // grab the external assets from the order data array
						$assetData = $pOrderDataArray['assets' . $k];

                        // if there are some external assets then add them to the cart
						if ($assetData != '')
						{
							$assetDataArray = explode("\r", self::decodeDesignerEncryptedData($assetData, $appVersion, $ownerCode, $dataVersion));
							$itemCount = count($assetDataArray);

							$assetDataVersion = (int)$assetDataArray[0];

							for ($i2 = 1; $i2 < $itemCount; $i2++)
							{
								$assetDataItemArray = explode("\t", $assetDataArray[$i2]);

								$assetItemArray = Array();
								$assetItemArray['servicecode'] = (string)$assetDataItemArray[0];
								$assetItemArray['servicename'] = (string)$assetDataItemArray[1];
								$assetItemArray['id'] = (string)$assetDataItemArray[2];
								$assetItemArray['name'] = (string)$assetDataItemArray[3];
								$assetItemArray['pricetype'] = (int)$assetDataItemArray[4];
								$assetItemArray['assetunitcost'] = (float)$assetDataItemArray[5];
								$assetItemArray['assetunitsell'] = (float)$assetDataItemArray[6];
								$assetItemArray['unitcost'] = (float)$assetDataItemArray[7];
								$assetItemArray['unitsell'] = (float)$assetDataItemArray[8];
								$assetItemArray['expirationdate'] = (string)$assetDataItemArray[9];
								$assetItemArray['pageref'] = (int)$assetDataItemArray[10];
								$assetItemArray['pagenumber'] = (int)$assetDataItemArray[11];
								$assetItemArray['pagename'] = (string)$assetDataItemArray[12];
								$assetItemArray['boxref'] = (int)$assetDataItemArray[13];
								$assetsArray[] = $assetItemArray;
							}
						}

                        // add all the external assets to the cart array
						$cartItemArray['externalassets'] = $assetsArray;


                        /*
						 * Create a lookup table to store the picture data.
						 *
						 * ['key'] Contains a list keys for each individual picture .e.g. if a project has 50 unique pictures, then this array will contain 50 entries.
						 * ['data'] Contains the common properties grouped by the component, subcomponent and quantity. This can extract using the lookup from the ['key'] array.
						 * ['printdata'] Contains the unique values for each picture. This can be extracted from the array key number from the ['key'] array.
						 * ['asset'] Contains the unique asset values for each picture. This can be extracted from the lookup key from the ['key'] array plus the array key.
						 *		e.g. $lookupUpKey . TPX_PICTURES_LOOKUP_SEPERATOR . 10 to return the 11th item.
						 */
    					$pictureArray = array();
						$pictureTable = array();
						$printData = array();
						$pictureAssets = array();
						$pictureNames = array();

                        // grab the pictures from the order data array
    					$pictureData = $pOrderDataArray['pictures' . $k];

                        // if there are some pictures then add them to the cart
    					if ($pictureData != '')
    					{
    						$pictureDataArray = explode("\r", self::decodeDesignerEncryptedData($pictureData, $appVersion, $ownerCode, $dataVersion));
							$pictureDataItemArray[0] = 0;
    						$itemCount = count($pictureDataArray);
    						$pictureDataVersion = (int)$pictureDataArray[0];
    						$pictureCount = (int)$pictureDataArray[1];

    						$componentDataArrayCache = Array();
							$pictureIndex = 0;
							$pictureNamesIndex = 0;
							$pictureNamesLookup = array();

    						for ($i2 = 2; $i2 < $itemCount; $i2++)
    						{
                                $pictureDataItemArray = explode("\t", $pictureDataArray[$i2]);

                                $subComponentLookUpKeySuffix = '';

                                // only check for the subcomponentcode if the apiversion is version 6 or above.
                                if ($apiVersion >= 6)
                                {
                                    $subComponentLookUpKeySuffix = (string)$pictureDataItemArray[17];
                                }

								// Build a lookup table containing the picture data.
								$lookupUpKey = TPX_PICTURES_LOOKUP_CATEGORY_KEY . TPX_PICTURES_LOOKUP_SEPERATOR . (string)$pictureDataItemArray[1] . TPX_PICTURES_LOOKUP_SEPERATOR .  (int)$pictureDataItemArray[2]
										. TPX_PICTURES_LOOKUP_SEPERATOR . $subComponentLookUpKeySuffix;

								$uniqueLookup = $lookupUpKey . TPX_PICTURES_LOOKUP_SEPERATOR . $pictureIndex;

								// Check it has an asset service code.
								if ((string)$pictureDataItemArray[8] != '')
								{
									$assetDataArray = array();
									$assetDataArray['asc'] = (string)$pictureDataItemArray[8]; // asset service code
									$assetDataArray['asn'] = (string)$pictureDataItemArray[9]; // asset service name
									$assetDataArray['aid'] = (string)$pictureDataItemArray[10]; // asset id
									$assetDataArray['apt'] = (int)$pictureDataItemArray[11]; // asset price type
									$assetDataArray['ac'] = (float)$pictureDataItemArray[12]; // asset cost
									$assetDataArray['as'] = (float)$pictureDataItemArray[13]; // asset sell

									$pictureAssets[$uniqueLookup] = $assetDataArray;
								}

								if (! array_key_exists($lookupUpKey, $pictureTable))
    							{
									$pictureItemArray = array();
									$pictureItemArray['category'] = (string)$pictureDataItemArray[0];
									$pictureItemArray['code'] = (string)$pictureDataItemArray[1];
									$pictureItemArray['qty'] = (int)$pictureDataItemArray[2];
									$pictureItemArray['pagename'] = '';
									$pictureItemArray['picturename'] = '';

									$componentCode = $pictureItemArray['category'] . '.' . $pictureItemArray['code'];

									if (array_key_exists($componentCode, $componentDataArrayCache))
									{
										$componentArray = $componentDataArrayCache[$componentCode];
									}
									else
									{
										$componentArray = DatabaseObj::getComponentByCode($componentCode);
										$componentDataArrayCache[$componentCode] = $componentArray;
									}

									$pictureItemArray['name'] = $componentArray['name'];
									$pictureItemArray['skucode'] = $componentArray['skucode'];
									$pictureItemArray['unitcost'] = $componentArray['unitcost'];
									$pictureItemArray['unitweight'] = $componentArray['weight'];
									$pictureItemArray['pricetaxcode'] = '';
                                    $pictureItemArray['pricetaxrate'] = 0.00;
                                    $pictureItemArray['setid'] = 0;
                                    $pictureItemArray['setname'] = '';
									$pictureItemArray['subcategory'] = '';
									$pictureItemArray['subcode'] = '';
									$pictureItemArray['subname'] = '';
									$pictureItemArray['subskucode'] = '';
									$pictureItemArray['subunitsell'] = 0.00;
									$pictureItemArray['subunitcost'] = 0.00;
									$pictureItemArray['subunitweight'] = 0.00;
									$pictureItemArray['subpricetaxcode'] = '';
									$pictureItemArray['subpricetaxrate'] = '';

									if ($apiVersion >= 6)
									{
										$pictureItemArray['setid'] = (float)$pictureDataItemArray[14];
										$pictureItemArray['setname'] = (string)$pictureDataItemArray[15];
										$pictureItemArray['subcategory'] = (string)$pictureDataItemArray[16];
										$pictureItemArray['subcode'] = (string)$pictureDataItemArray[17];

										if ($pictureItemArray['subcode'] != '')
										{
											$subComponentCode = $pictureItemArray['subcategory'] . '.' . $pictureItemArray['subcode'];

											if (array_key_exists($subComponentCode, $componentDataArrayCache))
											{
												$subComponentArray = $componentDataArrayCache[$subComponentCode];
											}
											else
											{
												$subComponentArray = DatabaseObj::getComponentByCode($subComponentCode);
												$componentDataArrayCache[$subComponentCode] = $subComponentArray;
											}

											$pictureItemArray['subname'] = $subComponentArray['name'];
											$pictureItemArray['subskucode'] = $subComponentArray['skucode'];
											$pictureItemArray['subunitcost'] = $subComponentArray['unitcost'];
											$pictureItemArray['subunitweight'] = $subComponentArray['weight'];
										}
									}

									$pictureTable[$lookupUpKey] = $pictureItemArray;
								}

								// We need to know the key so we can store it in the print data so we can look it up later.
								if (! array_key_exists($pictureDataItemArray[7], $pictureNamesLookup))
								{
									$pictureNamesLookup[$pictureDataItemArray[7]] = $pictureNamesIndex;
									$pictureNames[$pictureNamesIndex] = $pictureDataItemArray[7];
									$pictureNameKey = $pictureNamesIndex;
									$pictureNamesIndex++;
								}
								else
								{
									$pictureNameKey = $pictureNamesLookup[$pictureDataItemArray[7]];
								}

								$pictureArray[] = $lookupUpKey;
								$printData[$uniqueLookup] = array(
									'fn' => $pictureNameKey,
									'us' => 0.00, // unit sell
									'tc' => 0.00, // total cost
									'ts' => 0.00, // total sell
									'tt' => 0.00, // total tax
									'tsnt' => 0.00, // total sell no tax
									'tswt' => 0.00, // total sell with tax
									'tw' => 0.0000, // total weight
									'subtc' => 0.00, // sub total cost
									'subts' => 0.00, // sub total sell
									'subtt' => 0.00, // sub total tax
									'subtsnt' => 0.00, // sub total sell no tax
									'subtswt' => 0.00, // sub total sell with tax
									'subtw' => 0.00, // sub total weight
									'pageref' => (int)$pictureDataItemArray[3],
									'pagenumber' => (int)$pictureDataItemArray[4],
									'boxref' => (int)$pictureDataItemArray[6]
								);

								$pictureIndex++;
    						}
    					}

                        // add the pictures to the cart array
    					$cartItemArray['pictures']['key'] = $pictureArray;
    					$cartItemArray['pictures']['data'] = $pictureTable;
    					$cartItemArray['pictures']['printdata'] = $printData;
    					$cartItemArray['pictures']['pname'] = $pictureNames;
    					$cartItemArray['pictures']['asset'] = $pictureAssets;

                        // add the calendar customisations to the cart array
						$cartItemArray['calendarcustomisations'] = array();

                        // grab the pictures from the order data array
                        $calendarData = $pOrderDataArray['calendardata' . $k];

                        // if there are some calendar customisations then add them to the cart
                        if ($calendarData != '')
                        {
                            $calendarDataArray = explode("\r", self::decodeDesignerEncryptedData($calendarData, $appVersion, $ownerCode, $dataVersion));

                            // make sure that the calendardata is valid
                            if (count($calendarDataArray) == 2)
                            {
                                // only apply this logic if the version is 1 or greater
                                if ($calendarDataArray[0] >= '1')
                                {
                                	// crate an empty calendar component item array
									$emptyCalendarComptItemArray = array();
									$emptyCalendarComptItemArray['componentname'] = '';
									$emptyCalendarComptItemArray['componentcategory'] = 'CALENDARCUSTOMISATION';
									$emptyCalendarComptItemArray['componentcode'] = '';
									$emptyCalendarComptItemArray['componentlocalcode'] = '';
									$emptyCalendarComptItemArray['info'] = '';
									$emptyCalendarComptItemArray['skucode'] = '';
									$emptyCalendarComptItemArray['unitsell'] = 0.00;
									$emptyCalendarComptItemArray['unitcost'] = 0.00;
									$emptyCalendarComptItemArray['unitweight'] = 0.00;
									$emptyCalendarComptItemArray['totalcost'] = 0.00;
									$emptyCalendarComptItemArray['totalsell'] = 0.00;
									$emptyCalendarComptItemArray['totaltax'] = 0.00;
									$emptyCalendarComptItemArray['totalsellnotax'] = 0.00;
									$emptyCalendarComptItemArray['totalsellwithtax'] = 0.00;
									$emptyCalendarComptItemArray['totalweight'] = 0.00;
									$emptyCalendarComptItemArray['pricetaxcode'] = '';
									$emptyCalendarComptItemArray['pricetaxrate'] = '';
									$emptyCalendarComptItemArray['islist'] = 1;
									$emptyCalendarComptItemArray['pricingmodel'] = TPX_PRICINGMODEL_PERPRODCMPQTY;
									$emptyCalendarComptItemArray['metadata'] = array();
									$emptyCalendarComptItemArray['subtotal'] = 0.00;
									$emptyCalendarComptItemArray['componentqty'] = 0;
									$emptyCalendarComptItemArray['orderfootertaxname'] = '';
									$emptyCalendarComptItemArray['orderfootertaxrate'] = 0.00;
									$emptyCalendarComptItemArray['discountvalue'] = 0.00;
									$emptyCalendarComptItemArray['discountedtax'] = 0.00;
									$emptyCalendarComptItemArray['priceinfo'] = '';
									$emptyCalendarComptItemArray['path'] = '$CALENDARCUSTOMISATION\\';
									$emptyCalendarComptItemArray['used'] = false;

									// set up a list of empty customisation items
									$cartItemArray['calendarcustomisations']['CALENDARCUSTOMISATION.DATE'] = $emptyCalendarComptItemArray;
									$cartItemArray['calendarcustomisations']['CALENDARCUSTOMISATION.DATE']['componentcode'] = 'CALENDARCUSTOMISATION.DATE';
									$cartItemArray['calendarcustomisations']['CALENDARCUSTOMISATION.DATE']['componentlocalcode'] = 'DATE';
									$cartItemArray['calendarcustomisations']['CALENDARCUSTOMISATION.EVENTSET'] = $emptyCalendarComptItemArray;
									$cartItemArray['calendarcustomisations']['CALENDARCUSTOMISATION.EVENTSET']['componentcode'] = 'CALENDARCUSTOMISATION.EVENTSET';
									$cartItemArray['calendarcustomisations']['CALENDARCUSTOMISATION.EVENTSET']['componentlocalcode'] = 'EVENTSET';
									$cartItemArray['calendarcustomisations']['CALENDARCUSTOMISATION.ANY'] = $emptyCalendarComptItemArray;
									$cartItemArray['calendarcustomisations']['CALENDARCUSTOMISATION.ANY']['componentcode'] = 'CALENDARCUSTOMISATION.ANY';
									$cartItemArray['calendarcustomisations']['CALENDARCUSTOMISATION.ANY']['componentlocalcode'] = 'ANY';

                                    $calendarCustomisationSplitArray = explode("\t", $calendarDataArray[1]);

                                    $cartItemArray['calendarcustomisations']['CALENDARCUSTOMISATION.EVENTSET']['componentqty'] = intval($calendarCustomisationSplitArray[0]);
                                    $cartItemArray['calendarcustomisations']['CALENDARCUSTOMISATION.DATE']['componentqty'] = intval($calendarCustomisationSplitArray[1]);

                                    // get all the calendar customisations which are attached to the products component tree
                                    $calendarCustomisationArray = DatabaseObj::getComponentsInOrderSectionByCategory('$CALENDARCUSTOMISATION\\', 'CALENDARCUSTOMISATION',
                                                                                            $companyCode, $cartItemArray['componenttreeproductcode'], $groupCode, 1.0, 2, -1, -1, -1, '', false, true);

                                    $componentItemCount = count($calendarCustomisationArray['component']);

                                    $useAny = true;
                                    $anyFoundOnTree = false;

                                    // loop through all the components from the database and set the relevant data to the calendar customisation array items
                                    for ($j = 0; $j < $componentItemCount; $j++)
                                    {
                                        $componentArray = $calendarCustomisationArray['component'][$j];

                                        $code = $componentArray['code'];

                                        $cartItemArray['calendarcustomisations'][$code]['componentcode'] = $code;
                                        $cartItemArray['calendarcustomisations'][$code]['componentname'] = $componentArray['name'];
                                        $cartItemArray['calendarcustomisations'][$code]['skucode'] = $componentArray['skucode'];
                                        $cartItemArray['calendarcustomisations'][$code]['info'] = $componentArray['info'];
                                        $cartItemArray['calendarcustomisations'][$code]['unitcost'] = $componentArray['unitcost'];
                                        $cartItemArray['calendarcustomisations'][$code]['unitweight'] = $componentArray['unitweight'];
                                        $cartItemArray['calendarcustomisations'][$code]['pricingmodel'] = $componentArray['pricingmodel'];
                                        $cartItemArray['calendarcustomisations'][$code]['pricetaxcode'] = $componentArray['pricetaxcode'];
                                        $cartItemArray['calendarcustomisations'][$code]['pricetaxrate'] = $componentArray['pricetaxrate'];
                                        $cartItemArray['calendarcustomisations'][$code]['priceinfo'] = $componentArray['priceinfo'];
                                        $cartItemArray['calendarcustomisations'][$code]['used'] = true;

                                        // flag that the ANY component isn't been used if either the DATE or EVENTSET components are
                                        if (($code == 'CALENDARCUSTOMISATION.EVENTSET') || ($code == 'CALENDARCUSTOMISATION.DATE'))
                                        {
                                            $useAny = false;
                                        }

                                        if ($code == 'CALENDARCUSTOMISATION.ANY')
                                        {
                                            $anyFoundOnTree = true;
                                        }
                                    }

                                    // ANY should only be used when DATE and EVENTSET are both missing
                                    if (!$useAny)
                                    {
                                        unset($cartItemArray['calendarcustomisations']['CALENDARCUSTOMISATION.ANY']);
                                    }
                                    else if ($anyFoundOnTree) // only set the component qty of ANY if it is on the tree
                                    {
                                        $cartItemArray['calendarcustomisations']['CALENDARCUSTOMISATION.ANY']['componentqty'] = $cartItemArray['calendarcustomisations']['CALENDARCUSTOMISATION.EVENTSET']['componentqty'] + $cartItemArray['calendarcustomisations']['CALENDARCUSTOMISATION.DATE']['componentqty'];
                                    }
                                }
                            }
						}

                        // Grab retro prints list from the order data array.
                        $retroPrintsData = $pOrderDataArray['retroprints' . $k];

                        if ($retroPrintsData != '')
                        {
                            $retroPrintsDataArray = explode("\r", self::decodeDesignerEncryptedData($retroPrintsData, $appVersion, $ownerCode, $dataVersion));
							$retroPrintsDataArrayCount = count($retroPrintsDataArray);

							for ($retroPrintIndex = 0; $retroPrintIndex < $retroPrintsDataArrayCount; $retroPrintIndex++)
							{
								list($retroPrintPageNumber, $retroPrintQty) = explode("\t", ltrim($retroPrintsDataArray[$retroPrintIndex]));

								$retroPrintsArray[] = [
									'pagenumber' => $retroPrintPageNumber,
									'quantity' => $retroPrintQty
								];
							}
                        }

						if (UtilsObj::getArrayParam($cartItemArray, 'projectaimode', TPX_AIMODE_DISABLED) == TPX_AIMODE_ENABLED)
						{
							// since there can only be one TAOPIXAI component don't look it up if we have previously retrieved it
							if (empty($AIComponentArray))
							{
								// As there is only one supported AI component grab it directly
								$AIComponentArray = DatabaseObj::getComponentByCode("TAOPIXAI.TAOPIXAI");
								$AIComponentArray['path'] = '$TAOPIXAI\\';
								$AIComponentArray['componentcode'] = "TAOPIXAI.TAOPIXAI";
								$AIComponentArray['componentqty'] = 1;
							}

							$cartItemArray['aicomponent'] = $AIComponentArray;
						}
                    }
                }

                if ($result == '')
                {
                    // make sure we have a valid price (we don't know the qty at this point so just check for any qty)
                    $priceArray = DatabaseObj::getProductPrice($cartItemArray['productcode'], $groupCode, $companyCode, $currencyExchangeRate, $currencyDecimalPlaces, -1);

                    $result = $priceArray['result'];
                    $resultParam = $priceArray['resultparam'];
                    if ($result == '')
                    {
                        // if we can use an external cart determine which cart to use
                        if ($canUseExternalCart == 1)
                        {
                            $shoppingCartType = $priceArray['shoppingcarttype'];

                            if (($basketAPIWorkFlowType == TPX_BASKETWORKFLOWTYPE_LOWLEVELAPI) || ($basketAPIWorkFlowType == TPX_BASKETWORKFLOWTYPE_LOWLEVELAPIEXTERNALCHECKOUT))
							{
								$shoppingCartType = TPX_SHOPPINGCARTTYPE_EXTERNAL;
							}
						}

						// check that the AI Component has been priced for this product if applicable
						if (array_key_exists('aicomponent',$cartItemArray))
						{
							$AIPriceArray = DatabaseObj::getPrice($cartItemArray['aicomponent']['path'], $cartItemArray['aicomponent']['componentcode'], false, $cartItemArray['componenttreeproductcode'],
																	$groupCode, $companyCode, $currencyExchangeRate, $currencyDecimalPlaces, 1, $cartItemArray['pagecount'], 1, 1, false, true, -1, 0, "", true);

							// if a price has not been found the component has not been priced for this product and we want to set it to unused so that it is not displayed on screen
							// we still want it in the order data so that future re-orders can load the component
							if (($AIPriceArray['result'] !== "") || ($AIPriceArray['isactive'] === 0))
							{
								$cartItemArray['aicomponent']['used'] = false;
								$cartItemArray['aicomponent']['unitsell'] = 0;
							}
							else
							{
								$cartItemArray['aicomponent']['used'] = true;
								// add the unit sell to the array for the external cart to use
								$cartItemArray['aicomponent']['unitsell'] = $AIPriceArray['unitsell'];
							}

							unset($AIPriceArray);
						}

                        // check to see if the calendar customisations have a price
                        foreach ($cartItemArray['calendarcustomisations'] as $calendarCustomisation)
                        {

                            if (($calendarCustomisation['componentqty'] > 0) && ($calendarCustomisation['used']))
                            {
                                // get the price from the database and make sure that it is valid and the quantity is in range
                                $calcustomPriceArray = DatabaseObj::getPrice($calendarCustomisation['path'], $calendarCustomisation['componentcode'], false,
                                                                                $cartItemArray['componenttreeproductcode'], $groupCode, $companyCode, $currencyExchangeRate,
                                                                                $currencyDecimalPlaces, -1, -1, $calendarCustomisation['componentqty'], $calendarCustomisation['componentqty'], true, true, -1, 0, '', true);

                                if (($calcustomPriceArray['result'] != '') || ($calcustomPriceArray['isactive'] == 0) || ($calcustomPriceArray['newqty'] != $calendarCustomisation['componentqty']))
                                {
                                    $result = 'str_ErrorNoComponent';
                                    break;
                                }
                            }
                        }

                        if ($result == '')
                        {
							$consolidatedPicturesSizeStockArray = array();
							$applyBasePriceLineSubtract = true;

							if ($cartItemArray['productoptions'] == TPX_PRODUCTOPTION_PRICING_PERCOMPONENTSUBCOMPONENT)
							{
								// consolidate the singleprint prices
								foreach ($cartItemArray['pictures']['key'] as $pictureLookup)
								{
									$picture = $cartItemArray['pictures']['data'][$pictureLookup];

									$componentSubComponentKey = $picture['code'];

									if ($picture['subcode'] != '')
									{
										$componentSubComponentKey .= '.' . $picture['subcode'];
									}

									if (!array_key_exists($componentSubComponentKey, $consolidatedPicturesSizeStockArray))
									{
										$consolidatedPicturesSizeStockArray[$componentSubComponentKey]['qty'] = $picture['qty'];
										$consolidatedPicturesSizeStockArray[$componentSubComponentKey]['basepricelinesubtractapplied'] = false;
									}
									else
									{
										$consolidatedPicturesSizeStockArray[$componentSubComponentKey]['qty'] += $picture['qty'];
									}
								}
							}

                            // check to see if all SINGLEPRINT components for the orderline have a price
							$picturesCount = count($cartItemArray['pictures']['key']);

							for ($pictureIndex = 0; $pictureIndex < $picturesCount; $pictureIndex++)
                            {
								$pictureLookup = $cartItemArray['pictures']['key'][$pictureIndex];
								$picture = $cartItemArray['pictures']['data'][$pictureLookup];

                                $lineBreakQTY = $picture['qty'];

								$componentSubComponentKey = $picture['code'];

								if ($picture['subcode'] != '')
								{
									$componentSubComponentKey .= '.' . $picture['subcode'];
								}

								if ($cartItemArray['productoptions'] == TPX_PRODUCTOPTION_PRICING_PERCOMPONENTSUBCOMPONENT)
								{
									$lineBreakQTY = $consolidatedPicturesSizeStockArray[$componentSubComponentKey]['qty'];

									$applyBasePriceLineSubtract = false;

									if (! $consolidatedPicturesSizeStockArray[$componentSubComponentKey]['basepricelinesubtractapplied'])
									{
										$applyBasePriceLineSubtract = true;
									}
								}

                                if ($applyBasePriceLineSubtract)
								{
									$applyBasePrice = 1;
								}
								else
								{
									$applyBasePrice = 0;
								}

                                $componentCode = 'SINGLEPRINT' . '.' . $picture['code'];

                                $pictureArrayCacheKey = $companyCode . '.' . $groupCode . '.' . $cartItemArray['productcode'];
    							$pictureArrayCacheKey .= '.' . $componentCode . '.' . $pageCount .  '.' . $lineBreakQTY . '.' . $picture['qty'] . '.' . $applyBasePrice;

    							$picturePriceArray = DatabaseObj::getPriceCacheData($pictureArrayCacheKey);

    							if (count($picturePriceArray) == 0)
    							{
    								$picturePriceArray = DatabaseObj::getPrice('$SINGLEPRINT\\', $componentCode, false, $cartItemArray['componenttreeproductcode'], $groupCode, $companyCode, $currencyExchangeRate, $currencyDecimalPlaces, -1, $pageCount, $lineBreakQTY, $picture['qty'], false, false, -1, 0, '', $applyBasePriceLineSubtract);
    								DatabaseObj::setPriceCacheData($pictureArrayCacheKey, $picturePriceArray);
    							}

								if ($cartItemArray['productoptions'] == TPX_PRODUCTOPTION_PRICING_PERCOMPONENTSUBCOMPONENT)
								{
									$consolidatedPicturesSizeStockArray[$componentSubComponentKey]['basepricelinesubtractapplied'] = true;
								}

                                $result = $picturePriceArray['result'];

                                if ($result == '')
                                {
                                	if ($picture['subcode'] != '')
                                	{
    									// check to see if all SINGLEPRINT SUBCOMPONENTS for the orderline have a price
    									$subComponentParentPath = '$SINGLEPRINT\\' . $picture['code'] . '\\$SINGLEPRINTOPTION\\';
    									$subComponentCode = 'SINGLEPRINTOPTION' . '.' . $picture['subcode'];

    									$subComponentArrayCacheKey = $companyCode . '.' . $groupCode . '.' . $cartItemArray['productcode'];
    									$subComponentArrayCacheKey .= '.' . $subComponentCode . '.' . $pageCount . '.' . $lineBreakQTY . '.' . $picture['qty'] . '.' . $applyBasePrice;

    									$subComponentPriceArray = DatabaseObj::getPriceCacheData($subComponentArrayCacheKey);

    									if (count($subComponentPriceArray) == 0)
    									{
    										$subComponentPriceArray = DatabaseObj::getPrice($subComponentParentPath, $subComponentCode, false, $cartItemArray['componenttreeproductcode'], $groupCode, $companyCode, $currencyExchangeRate, $currencyDecimalPlaces, -1, $pageCount, $lineBreakQTY, $picture['qty'], false, false, -1, 0, '', $applyBasePriceLineSubtract);
    										DatabaseObj::setPriceCacheData($subComponentArrayCacheKey, $subComponentPriceArray);
    									}

    									$result = $subComponentPriceArray['result'];

    									// if the result was not empty then we do not have any valid pricing
    									if ($result != '')
    									{
                                            // if no valid price check to see if subcomponent exists in the product configuration.

                                            // create a cache key for checking if the component is found
                                            $orderSectionCacheKey = 'osCacheKeyCompFound.' . $companyCode . '.' . $groupCode . '.' . $cartItemArray['productcode'];
                                            $orderSectionCacheKey .= '.' . $picture['code'] . '.' . 1 . '.' . 1;

                                            // check the cache for the cache key
                                            $orderSectionCacheArray = DatabaseObj::getPriceCacheData($orderSectionCacheKey);
                                            $subComponentFound = false;

                                            // if the cache key is not found call the database else use the the cached value
                                            if (count($orderSectionCacheArray) == 0)
                                            {
                                                // look up the subcomponent in the database
                                                $tempOptionArray = DatabaseObj::getComponentsInOrderSectionByCategory('$SINGLEPRINT\\' . $picture['code'] . '\\$SINGLEPRINTOPTION\\',
                                                                            'SINGLEPRINTOPTION', $companyCode, $cartItemArray['componenttreeproductcode'], $groupCode, 1.0, 2, -1, -1, -1, '', false, true);

                                                $tempOptionArray = $tempOptionArray['component'];

                                                $subComponentItemCount = count($tempOptionArray);

                                                // look through all the components and look for the sub component
                                                for ($j = 0; $j < $subComponentItemCount; $j++)
                                                {
                                                    $tempOptionItem = &$tempOptionArray[$j];

                                                    if ($tempOptionItem['code'] == $subComponentCode)
                                                    {
                                                        $subComponentFound = true;
                                                        break;
                                                    }
                                                }

                                                // cache the result of the find
                                                // setPriceCacheData required the value to be an array but we are only chaching a single value thus
                                                // we are adding an array with one value
                                                DatabaseObj::setPriceCacheData($orderSectionCacheKey, array($subComponentFound));
                                            }
                                            else
                                            {
                                                // since the cached value will only ever be an array containing one value we can be sure that
                                                // there is only one value in it
                                                $subComponentFound = $orderSectionCacheArray[0];
                                            }


    										// if the subcomponent was not found reset the pictureItemArray values and let it through
    										if (! $subComponentFound)
    										{
    											$pictureItemArray['setid'] = 0;
    											$pictureItemArray['setname'] = '';
    											$pictureItemArray['subskucode'] = '';
    											$pictureItemArray['subcategory'] = '';
    											$pictureItemArray['subcode'] = '';
    											$pictureItemArray['subname'] = '';
    											$pictureItemArray['subunitsell'] = 0.00;
    											$pictureItemArray['subunitcost'] = 0.00;
    											$pictureItemArray['subunitweight'] = 0.00;
												$pictureItemArray['subpricetaxcode'] = '';
    											$pictureItemArray['subpricetaxrate'] = '';

												// Get print data from lookup.
												$printData = $cartItemArray['pictures']['printdata'][$pictureLookup];

    											$printData['subtc'] = 0.00;
    											$printData['subts'] = 0.00;
    											$printData['subtt'] = 0.00;
    											$printData['subtsnt'] = 0.00;
    											$printData['subtswt'] = 0.00;
    											$printData['subtw'] = 0.00;
    										}
    										else
    										{
    											// we do not have a valid price for the subcomponent so we cannot continue
    											$result = 'str_SinglePrintNoPriceAvailableError';
    											$resultParam = LocalizationObj::getLocaleString($picture['subname'], $languageCode, true);
    											break;
    										}
    									}
    								}
    								else
    								{
                                        // if subcomponentcode is empty check for subcomponents in the product configuration

                                        // create a cache key for the component
                                        $orderSectionCacheKey = 'osCacheKey.' . $companyCode . '.' . $groupCode . '.' . $cartItemArray['productcode'];
                                        $orderSectionCacheKey .= '.' . $picture['code'] . '.' . 1 . '.' . 1;

                                        $orderSectionCacheArray = DatabaseObj::getPriceCacheData($orderSectionCacheKey);

                                        if (count($orderSectionCacheArray) == 0)
                                        {
                                            $tempOptionArray = DatabaseObj::getComponentsInOrderSectionByCategory('$SINGLEPRINT\\' . $picture['code'] . '\\$SINGLEPRINTOPTION\\',
                                                                        'SINGLEPRINTOPTION', $companyCode, $cartItemArray['componenttreeproductcode'], $groupCode, 1.0, 2, -1, -1, -1, '', false, true);

                                            DatabaseObj::setPriceCacheData($orderSectionCacheKey, $tempOptionArray);
                                        }
                                        else
                                        {
                                            $tempOptionArray = $orderSectionCacheArray;
                                        }

                                        $tempOptionArray = $tempOptionArray['component'];

                                        $subComponentItemCount = count($tempOptionArray);

                                        // if subcompoents have been found we must report an error
                                        if ($subComponentItemCount > 0)
                                        {
                                           $result = 'str_SinglePrintNoOptionProvidedError';
                                           $resultParam = LocalizationObj::getLocaleString($picture['name'], $languageCode, true);
                                           break;
                                        }

    								}
                                }
                                else
                                {
                                	$result = 'str_SinglePrintNoPriceAvailableError';
                                    $resultParam = LocalizationObj::getLocaleString($picture['name'], $languageCode, true);
                                    break;
                                }
                            }

                            unset($picture);
                        }
                    }
                    else
                    {
                        // we do not have a price for the product
                        break;
                    }
                }
            }
        }

        // if we have the form parameters determine what we need to do
        if ($result == '')
        {
            if ($gSession['ismobile'] == true)
            {
                $jobTicketTemplate = 'jobticket_small';
            }
            else
            {
                $jobTicketTemplate = 'jobticket_large';
            }

            $orderArray = DatabaseObj::orderProcessed($batchRef, $cartArray[0]['uploadref']);

            $processedItemsArray = $orderArray['processeditemslist'];
            $processedItemsCount = count($processedItemsArray);

            if ($processedItemsCount > 0)
            {
                $activeOrderedItemsArray = Array();
                $activeOrderedItemsCount = 0;
                $allComplete = true;
                $allCancelled = true;
                $allActive = true;
                $allInProduction = true;
                for ($i = 0; $i < $processedItemsCount; $i++)
                {
                    $processedItem = &$processedItemsArray[$i];

                    // if we can upload then check for matching items
                    // note. cancelled or completed orders have canupload set to false via the orderProcessed function
                    if ($processedItem['canupload'] == 1)
                    {
                        $matched = false;

                        for ($i2 = 0; $i2 < $cartItemsCount; $i2++)
                        {
                            $cartItemArray = &$cartArray[$i2];

                            if ($cartItemArray['uploadref'] == $processedItem['uploadref'])
                            {
                                $processedItem['newpagecount'] = $cartItemArray['pagecount'];
                                $processedItem['newproductcode'] = $cartItemArray['productcode'];
                                $processedItem['newproductname'] = $cartItemArray['productname'];
                                $processedItem['pictures'] = $cartItemArray['pictures'];
                                $processedItem['externalassets'] = $cartItemArray['externalassets'];
								$processedItem['calendarcustomisations'] = $cartItemArray['calendarcustomisations'];

								if (array_key_exists('aicomponent', $cartItemArray))
								{
									$processedItem['aicomponent'] = $cartItemArray['aicomponent'];
								}

                                $activeOrderedItemsArray[] = &$processedItem;
                                $activeOrderedItemsCount++;

                                $allInProduction = false;

                                break;
                            }
                        }

                        $allComplete = false;
                        $allCancelled = false;
                    }
                    else
                    {
                        // the item cannot be uploaded so check to see what it's status is
                        if ($processedItem['orderstatus'] == TPX_ORDER_STATUS_CANCELLED)
                        {
                                // the item has been cancelled
                                $allActive = false;

                                // if the apiversion is lower than 4 then taopix designer is older than version 3.1
                                // in this situation we need to allow cancelled orders to be re-ordered
                                if ($apiVersion < 4)
                                {
                                        $allInProduction = false;
                                        $allComplete = false;
                                }
                        }
                        elseif ($processedItem['orderstatus'] == TPX_ORDER_STATUS_COMPLETED)
                        {
                                // the item has been completed
                                $allCancelled = false;

                                // if the apiversion is lower than 4 then taopix designer is older than version 3.1
                                // in this situation we need to allow completed orders to be re-ordered
                                if ($apiVersion < 4)
                                {
                                        $allInProduction = false;
                                }
                        }
                        else
                        {
                                // the item is in production
                                $allCancelled = false;
                                $allComplete = false;
                        }
                    }

                    unset($processedItem);
                }

                if ($activeOrderedItemsCount > 0)
                {
                    // determines who handles this order
                    if ($shoppingCartType == TPX_SHOPPINGCARTTYPE_INTERNAL)
                    {
                        // taopix web is handling this order

                        for ($i = 0; $i < $activeOrderedItemsCount; $i++)
                        {
                            $processedItem = &$activeOrderedItemsArray[$i];

                            // check for a change in the product code
                            if (($processedItem['newproductcode'] != $processedItem['productcode']) && ($processedItem['canuploadproductcodeoverride'] == 0))
                            {
                                $result = 'PRODUCTCODEMISMATCH';
                                break;
                            }

                            $pageCount = $processedItem['newpagecount'];
                            $orderPageCount = $processedItem['pagecountpurchased'];
                            $paperMaxPageCount = $orderPageCount;

                            // first check to see if the order actually contains a paper component.
                            // if we have paper make sure the number of pages isn't outside the range purchased
                            if ($processedItem['papercode'] != '')
                            {
                                if (($pageCount > $orderPageCount) && ($processedItem['canuploadpagecountoverride'] == 0))
                                {
                                    // the project contains more pages than ordered determine if the paper sell price is more
                                    $paperPriceArray = DatabaseObj::getPrice('$PAPER\\', $processedItem['papercode'], false, $cartItemArray['componenttreeproductcode'], $groupCode, $companyCode,
                                                        $currencyExchangeRate, $currencyDecimalPlaces, $processedItem['qty'], $pageCount, 0, 0, true, true, -1, 0, '', true);

                                    if ($paperPriceArray['isactive'] == 1)
                                    {
                                        $sellPrice = $paperPriceArray['totalsell'];

                                        if ($sellPrice > $processedItem['papertotalsell'])
                                        {
                                            // the new paper price is more than the purchase paper price

                                            // get the number of pages they have purchased
                                            $purchasedPaperPriceArray = DatabaseObj::getPrice('$PAPER\\', $processedItem['papercode'], false, $cartItemArray['componenttreeproductcode'],
                                                            $groupCode, $companyCode, $currencyExchangeRate, $currencyDecimalPlaces, $processedItem['qty'], $orderPageCount, 0, 0, true, true, -1, 0, '', true);
                                            if (($paperPriceArray['recordid'] != $purchasedPaperPriceArray['recordid']) && ($purchasedPaperPriceArray['isactive'] == 1))
                                            {
                                                $paperMaxPageCount = $purchasedPaperPriceArray['endpagecount'];
                                            }

                                            // don't allow the user to upload their order
                                            $result = 'PAPERPAGECOUNTMISMATCH';
                                            break;
                                        }
                                    }
                                    else
                                    {
                                        // there is no paper price available
                                        // don't allow the user to upload their order
                                        $result = 'PAPERPAGECOUNTMISMATCH';
                                        break;
                                    }
                                }
                            }
                            else
                            {
                                // if we dont have a paper we still want to make sure that the number of pages isnt outside the range purchased
                                if (($pageCount > $orderPageCount) && ($processedItem['canuploadpagecountoverride'] == 0))
                                {
                                    // there is no paper price available
                                    // don't allow the user to upload their order
                                    $result = 'PAPERPAGECOUNTMISMATCH';
                                    break;
                                }
                            }

                            // if we have a cover make sure the number of pages isn't outside the cover range
                            if ($processedItem['covercode'] != '')
                            {
                                $coverArray = DatabaseObj::getCoverFromCode($processedItem['covercomponentcode']);
                                $coverMaxPageCount = $coverArray['maxpagecount'];
                                if ($pageCount > $coverMaxPageCount)
                                {
                                    // don't allow the user to upload their order as the cover is not valid for the number of pages in the project
                                    $result = 'COVERPAGECOUNTMISMATCH';
                                    break;
                                }
                            }

                            unset($processedItem);
                        }


                        // if no error has occurred re-calculate the totals
                        if ($result == '')
                        {
                            $dbObj = DatabaseObj::getConnection();
                            if ($dbObj)
                            {
                                for ($i = 0; $i < $activeOrderedItemsCount; $i++)
                                {
                                    $processedItem = &$activeOrderedItemsArray[$i];

                                    $uploadRef = $processedItem['uploadref'];
                                    $pageCount = $processedItem['newpagecount'];
                                    $orderPageCount = $processedItem['pagecountpurchased'];
                                    $paperMaxPageCount = $orderPageCount;

                                    // re-calculate the costs that may have changed
                                    $paperTotalCost = ($processedItem['qty'] * $pageCount) * $processedItem['paperunitcost'];
                                    $paperTotalWeight = ($processedItem['qty'] * $pageCount) * $processedItem['paperunitweight'];
                                    $itemTotalCost = $processedItem['producttotalcost'] + $processedItem['covertotalcost'] + $paperTotalCost;
                                    $itemTotalShippingWeight = $processedItem['producttotalweight'] + $processedItem['covertotalweight'] + $paperTotalWeight;
                                    $orderTotalCost = $itemTotalCost + $processedItem['shippingratecost'];

                                    // update the order item in ORDERITEMS and ORDERITEMCOMPONENTS tables
                                    $componentsList = DatabaseObj::getOrderItemComponents($processedItem['orderid'], $processedItem['recordid'], 'PAPER');
                                    $componentsList = $componentsList['components'];
                                    if (count($componentsList) > 0)
                                    {
                                        if ($stmt = $dbObj->prepare('UPDATE `ORDERITEMCOMPONENTS` SET `componenttotalcost` = ?, `componenttotalweight` = ?
                                            WHERE (`id` = ?)'))
                                        {
                                            if ($stmt->bind_param('ddi', $paperTotalCost, $paperTotalWeight, $componentsList[0]['id']))
                                            {
                                                if (! $stmt->execute())
                                                {
                                                    // could not execute statement
                                                    $result = 'str_DatabaseError';
                                                    $resultParam = 'updateorderitemcomponentinfo execute ' . $dbObj->error;
                                                }
                                            }
                                            else
                                            {
                                                // could not bind parameters
                                                $result = 'str_DatabaseError';
                                                $resultParam = 'updateorderitemcomponentinfo bind params ' . $dbObj->error;
                                            }
                                            $stmt->free_result();
                                            $stmt->close();
                                            $stmt = null;
                                        }
                                        else
                                        {
                                            // could not prepare statement
                                            $result = 'str_DatabaseError';
                                            $resultParam = 'updateorderitemcomponentinfo prepare ' . $dbObj->error;
                                        }
                                    }

                                    if ($result == '')
                                    {
                                        if ($stmt = $dbObj->prepare('UPDATE `ORDERITEMS` SET `totalcost` = ?, `totalshippingweight` = ? WHERE `uploadRef` = ?'))
                                        {
                                            if ($stmt->bind_param('dds', $itemTotalCost, $itemTotalShippingWeight, $uploadRef))
                                            {
                                                if (! $stmt->execute())
                                                {
                                                    // could not execute statement
                                                    $result = 'str_DatabaseError';
                                                    $resultParam = 'updateorderiteminfo execute ' . $dbObj->error;
                                                }
                                            }
                                            else
                                            {
                                                // could not bind parameters
                                                $result = 'str_DatabaseError';
                                                $resultParam = 'updateorderiteminfo bind params ' . $dbObj->error;
                                            }
                                            $stmt->free_result();
                                            $stmt->close();
                                            $stmt = null;
                                        }
                                        else
                                        {
                                            // could not prepare statement
                                            $result = 'str_DatabaseError';
                                            $resultParam = 'updateorderiteminfo prepare ' . $dbObj->error;
                                        }
                                    }

                                    if ($result == '')
                                    {
                                        // update the order header

                                        if ($stmt = $dbObj->prepare('UPDATE `ORDERHEADER` SET `itemtotalcost` = ?, `totalcost` = ?, `shippingtotalweight` = ?
                                            WHERE (`id` = ?)'))
                                        {
                                            if ($stmt->bind_param('dddi', $itemTotalCost, $orderTotalCost, $itemTotalShippingWeight, $processedItem['orderid']))
                                            {
                                                if (! $stmt->execute())
                                                {
                                                    // could not execute statement
                                                    $result = 'str_DatabaseError';
                                                    $resultParam = 'updateorderheaderinfo execute ' . $dbObj->error;
                                                }
                                            }
                                            else
                                            {
                                                // could not bind parameters
                                                $result = 'str_DatabaseError';
                                                $resultParam = 'updateorderheaderinfo bind params ' . $dbObj->error;
                                            }

                                            $stmt->free_result();
                                            $stmt->close();
                                            $stmt = null;
                                        }
                                        else
                                        {
                                            // could not prepare statement
                                            $result = 'str_DatabaseError';
                                            $resultParam = 'updateorderheaderinfo prepare ' . $dbObj->error;
                                        }
                                    }

                                    unset($processedItem);
                                }

                                $dbObj->close();
                            }
                            else
                            {
                                // could not open database connection
                                $result = 'str_DatabaseError';
                                $resultParam = 'updateordercost connect ' . $dbObj->error;
                            }
                        }

                    }
                    else
                    {
                        $externalShoppingCartCheckPageCountMethodExisits = false;
                        $basketAPICheckPageCountMethodExisits = false;
                        $buildExternalCartArray = false;

                        if (($basketAPIWorkFlowType == TPX_BASKETWORKFLOWTYPE_LOWLEVELAPIEXTERNALCHECKOUT) && ($cartArray[0]['source'] == TPX_SOURCE_ONLINE))
						{
							if (file_exists('../Customise/scripts/EDL_OnlineBasketAPI.php'))
							{
								require_once('../Customise/scripts/EDL_OnlineBasketAPI.php');
							}

							if (method_exists('OnlineBasketAPI', 'checkPageCount'))
							{
								$buildExternalCartArray = true;
								$basketAPICheckPageCountMethodExisits = true;
							}
						}

                        // the order is being handled by a 3rd party shopping cart
                        self::includeExternalShoppingCart();

                        if (method_exists('ExternalShoppingCart', 'checkPageCount'))
						{
							$buildExternalCartArray = true;
							$externalShoppingCartCheckPageCountMethodExisits = true;
						}

                        if ($buildExternalCartArray)
                        {
                            $externalCartArray = Array();
                            $externalCartArray['apiversion'] = $apiVersion;
                            $externalCartArray['languagecode'] = $languageCode;
                            $externalCartArray['ownercode'] = $ownerCode;
                            $externalCartArray['groupcode'] = $groupCode;
                            $externalCartArray['groupdata'] = $groupData;
                            $externalCartArray['brandcode'] = $licenseKeyArray['webbrandcode'];
                            $externalCartArray['orderid'] = $activeOrderedItemsArray[0]['orderid'];
                            $externalCartArray['ordernumber'] = $activeOrderedItemsArray[0]['ordernumber'];
                            $externalCartArray['items'] = Array();

                            for ($i = 0; $i < $activeOrderedItemsCount; $i++)
                            {
                                $processedItem = $activeOrderedItemsArray[$i];

                                $externalCartItemArray = Array();
                                $externalCartItemArray['orderitemid'] = $processedItem['recordid'];
                                $externalCartItemArray['productcode'] = $processedItem['newproductcode'];
                                $externalCartItemArray['orderedproductcode'] = $processedItem['productcodepurchased'];
                                $externalCartItemArray['uploadref'] = $processedItem['uploadref'];
                                $externalCartItemArray['pagecount'] = $processedItem['newpagecount'];
                                $externalCartItemArray['orderedpagecount'] = $processedItem['pagecountpurchased'];
                                $externalCartItemArray['canuploadproductcodeoverride'] = $processedItem['canuploadproductcodeoverride'];
                                $externalCartItemArray['canuploadpagecountoverride'] = $processedItem['canuploadpagecountoverride'];
                                $externalCartItemArray['externalassets'] = $processedItem['externalassets'];

								// add the single prints data to the item
								$picturesArray = Array();
								$origPictureCount = count($processedItem['pictures']);
								for ($i2 = 0; $i2 < $origPictureCount; $i2++)
                            	{
                            		$origPictureItem = $processedItem['pictures'][$i2];

                            		$pictureItem = Array();
                            		$pictureItem['setid'] = $origPictureItem['setid'];
									$pictureItem['setname'] = $origPictureItem['setname'];
									$pictureItem['componentqty'] = $origPictureItem['componentqty'];
									$pictureItem['componentcategory'] = $origPictureItem['componentcategory'];
									$pictureItem['componentcode'] = $origPictureItem['componentcode'];
									$pictureItem['componentname'] = $origPictureItem['componentname'];
									$pictureItem['skucode'] = $origPictureItem['skucode'];
									$pictureItem['unitcost'] = $origPictureItem['unitcost'];
									$pictureItem['unitweight'] = $origPictureItem['unitweight'];
									$pictureItem['unitsell'] = $origPictureItem['unitsell'];
									$pictureItem['subcomponentcategory'] = $origPictureItem['subcomponentcategory'];
									$pictureItem['subcomponentcode'] = $origPictureItem['subcomponentcode'];
									$pictureItem['subcomponentname'] = $origPictureItem['subcomponentname'];
									$pictureItem['subcomponentskucode'] = $origPictureItem['subcomponentskucode'];
									$pictureItem['subcomponentunitcost'] = $origPictureItem['subcomponentunitcost'];
									$pictureItem['subcomponentunitweight'] = $origPictureItem['subcomponentunitweight'];
									$pictureItem['subcomponentunitsell'] = $origPictureItem['subcomponentunitsell'];
									$pictureItem['assetservicecode'] = $origPictureItem['assetservicecode'];
									$pictureItem['assetservicename'] = $origPictureItem['assetservicename'];
									$pictureItem['assetpricetype'] = $origPictureItem['assetpricetype'];
									$pictureItem['assetid'] = $origPictureItem['assetid'];
									$pictureItem['assetcost'] = $origPictureItem['assetcost'];
									$pictureItem['assetsell'] = $origPictureItem['assetsell'];
									$pictureItem['pageref'] = $origPictureItem['pageref'];
									$pictureItem['pagenumber'] = $origPictureItem['pagenumber'];
									$pictureItem['pagename'] = $origPictureItem['pagename'];
									$pictureItem['boxref'] = $origPictureItem['boxref'];

                            		$picturesArray[] = $pictureItem;
                            	}
								$externalCartItemArray['pictures'] = $picturesArray;

                                // add the calendar custom data to the item
                                $calendarCustomisationArray = Array();

                                foreach($processedItem['calendarcustomisations'] as $code => $origCalendarCustomisation)
                                {
                                    $calendarCustomisation = Array();
                                    $calendarCustomisation['componentqty'] = $origCalendarCustomisation['componentqty'];
                                    $calendarCustomisation['componentcategory'] = $origCalendarCustomisation['category'];
                                    $calendarCustomisation['componentcode'] = $origCalendarCustomisation['componentcode'];
                                    $calendarCustomisation['componentname'] = $origCalendarCustomisation['componentname'];
                                    $calendarCustomisation['skucode'] = $origCalendarCustomisation['skucode'];
                                    $calendarCustomisation['unitcost'] = $origCalendarCustomisation['unitcost'];
                                    $calendarCustomisation['unitweight'] = $origCalendarCustomisation['unitweight'];
                                    $calendarCustomisation['unitsell'] = $origCalendarCustomisation['unitsell'];

                                    $calendarCustomisationArray[] = $calendarCustomisation;
                                }

                                $externalCartItemArray['calendarcustomisations'] = $calendarCustomisationArray;

								// add the item to the list
                                $externalCartArray['items'][] = $externalCartItemArray;
                            }

							$result = '';

							if (($basketAPIWorkFlowType == TPX_BASKETWORKFLOWTYPE_LOWLEVELAPI) && ($cartArray[0]['source'] == TPX_SOURCE_ONLINE) && ($basketAPICheckPageCountMethodExisits))
							{
								$result = OnlineBasketAPI::checkPageCount($externalCartArray);
							}

							if (($result == '') && ($externalShoppingCartCheckPageCountMethodExisits))
							{
								$result = ExternalShoppingCart::checkPageCount($externalCartArray);
							}

                            if (($result != '') && ($result != 'PRODUCTCODEMISMATCH') && ($result != 'PAPERPAGECOUNTMISMATCH') && ($result != 'COVERPAGECOUNTMISMATCH'))
                            {
                                $resultParam = $result;
                                $result = 'CUSTOMERROR';
                            }
                        }
                    }

                    if ($result == '')
                    {
                        // allow the user to upload their order
                        $result = 'UPLOAD';

                        // update the product info with the latest information
                        $dbObj = DatabaseObj::getConnection();
                        if ($dbObj)
                        {
                            $uploadRef = '';
                            $pageCount = 0;
                            $productCode = '';
                            $productName = '';

                            if ($stmt = $dbObj->prepare('UPDATE `ORDERITEMS` SET `productcode` = ?, `productname` = ?, `pagecount` = ? WHERE `uploadRef` = ? AND `parentorderitemid` = 0'))
                            {
                                if ($stmt->bind_param('ssis', $productCode, $productName, $pageCount, $uploadRef))
                                {
                                    for ($i = 0; $i < $activeOrderedItemsCount; $i++)
                                    {
                                        $processedItem = &$activeOrderedItemsArray[$i];

                                        $productCode = $processedItem['newproductcode'];
                                        $productName = $processedItem['newproductname'];
                                        $pageCount = $processedItem['newpagecount'];
                                        $uploadRef = $processedItem['uploadref'];

                                        if (! $stmt->execute())
                                        {
                                            // could not execute statement
                                            $result = 'str_DatabaseError';
                                            $resultParam = 'updateordercounts execute ' . $dbObj->error;

                                            break;
                                        }
                                    }
                                }

                                $stmt->free_result();
                                $stmt->close();
                                $stmt = null;
                            }

                            $dbObj->close();
                        }
                        else
                        {
                            // could not open database connection
                            $result = 'str_DatabaseError';
                            $resultParam = 'updateordercounts connect ' . $dbObj->error;
                        }
                    }
                }
                else
                {
                    // there are no items to upload

                    if ($allInProduction)
                    {
                         $result = 'PRODUCTION';
                    }
                    elseif ($allCancelled)
                    {
                        $result = 'ORDERCANCELLED';
                    }
                    else
                    {
                        $result = 'ORDERCOMPLETED';
                    }
                }
            }
            else
            {
                // we have no order at all so we must start the ordering process
                $result = 'ORDER';
            }
        }
        else
        {
            // tidy some of the error messages returned to the user
            if (($result == 'str_InvalidProductCode') || ($result == 'str_ProductCodeNotActive'))
            {
                $result = 'INACTIVEPRODUCT';
            }
        }

        // if no error has occurred attempt to initiate the session
        if (($result == '') || ($result == 'ORDER') || ($result == 'ORDERCANCELLED') || ($result == 'ORDERCOMPLETED'))
        {
            // check to make sure we are not using the low levelbasket api.
            if (($basketAPIWorkFlowType == TPX_BASKETWORKFLOWTYPE_NORMAL) || ($basketAPIWorkFlowType == TPX_BASKETWORKFLOWTYPE_HIGHLEVELCHECKOUT))
            {
				// include the shopping cart module
				require_once('../Order/Order_model.php');

                $resultArray = Order_model::orderSessionInitialize($languageCode, $appVersion, $licenseKeyArray['webbrandcode'], $shoppingCartType, $uuid, $jobTicketTemplate,
						$licenseKeyArray['showpriceswithtax'], $licenseKeyArray['showtaxbreakdown'], $licenseKeyArray['showzerotax'], $licenseKeyArray['showalwaystaxtotal'],
						$ownerCode, $groupCode, $groupData, $groupName, $groupAddress1, $groupAddress2, $groupAddress3, $groupAddress4, $groupAddressCity, $groupAddressCounty,
						$groupAddressState, $groupPostCode, $groupCountryCode, $groupTelephoneNumber, $groupEmailAddress, $groupContactFirstName, $groupContactLastName,
						$batchRef, $cartArray, $basketAPIWorkFlowType, $highLevelBasketRef, true);

				if ($resultArray['result'] != '')
				{
					$result = $resultArray['result'];
					$resultParam = $resultArray['resultparam'];
				}

				// assign the uploadrefs if they haven't been assigned
				$newUploadRef = $gSession['ref'];
				if ($newUploadRef > 999999)
				{
					$newUploadRef = $newUploadRef - 999999;
				}
				$newUploadRef = $groupCode . date('dmYHi') . sprintf('%06s', $newUploadRef);

				for ($i = 0; $i < $cartItemsCount; $i++)
				{
					$cartItemArray = &$cartArray[$i];
					$orderItem = &$gSession['items'][$i];

					// if the uploadref is 'ALLOCATE' then generate one based on the date/time & session reference
					if ($cartItemArray['uploadref'] == 'ALLOCATE')
					{
						if ($cartItemsCount == 1)
						{
							$cartItemArray['uploadref'] = $newUploadRef;
						}
						else
						{
							$cartItemArray['uploadref'] = $newUploadRef . '_' . ($i + 1);
						}

						$orderItem['itemuploadref'] = $cartItemArray['uploadref'];
						$orderItem['itemuploadbatchref'] = $batchRef;
					}

					// if no batch reference has been assigned then we use the uploadref if there is 1 item in the cart and this session ref if there is more than one item
					// using the uploadref for 1 item keeps this compatible with older versions that did not support multiple items in the cart
					if ($orderItem['itemuploadbatchref'] == '')
					{
						if ($cartItemsCount == 1)
						{
							$orderItem['itemuploadbatchref'] = $orderItem['itemuploadref'];
						}
						else
						{
							$orderItem['itemuploadbatchref'] = $gSession['ref'];
						}
					}
				}

				$batchRef = $gSession['items'][0]['itemuploadbatchref'];
			}
			else if (($basketAPIWorkFlowType == TPX_BASKETWORKFLOWTYPE_LOWLEVELAPI) || ($basketAPIWorkFlowType == TPX_BASKETWORKFLOWTYPE_HIGHLEVELAPI))
			{
				$newUploadRef = '';

				for ($i = 0; $i < $cartItemsCount; $i++)
				{
					$cartItemArray = &$cartArray[$i];

					// if the uploadref is 'ALLOCATE' then generate one based on the date/time & session reference
					if ($cartItemArray['uploadref'] == 'ALLOCATE')
					{
						// if we do not have an uploadref we need to generate one using the same method as a normal order
						// to do this we need to create a fake session so that we can obtain the reference number
						if ($newUploadRef == '')
						{
							$tempSessionResultArray = DatabaseObj::insertOrderSessionDataRecord($cartItemArray['projectref'], '');

							if ($tempSessionResultArray['result'] == '')
							{
								$newUploadRef = $tempSessionResultArray['ref'];
								if ($newUploadRef > 999999)
								{
									$newUploadRef = $newUploadRef - 999999;
								}
								$newUploadRef = $groupCode . date('dmYHi') . sprintf('%06s', $newUploadRef);


								// the session is not really needed so just delete it again
								DatabaseObj::deleteSession($tempSessionResultArray['ref']);
							}
						}

						if ($cartItemsCount == 1)
						{
							$cartItemArray['uploadref'] = $newUploadRef;
						}
						else
						{
							$cartItemArray['uploadref'] = $newUploadRef . '_' . ($i + 1);
						}
					}
				}

				// use the first uploadref as the batch reference
				$batchRef = $cartArray[0]['uploadref'];
			}

            // copy the latest cart array into the processed items array
            $processedItemsArray = $cartArray;

            if ($basketAPIWorkFlowType == TPX_BASKETWORKFLOWTYPE_HIGHLEVELAPI)
            {
            	require_once('../OnlineAPI/OnlineAPI_model.php');

            	// read the config file
				$hl_config = UtilsObj::readWebBrandConfigFile('../config/onlinebaskethighlevelapi.conf', $gSession['webbrandcode']);

				$maxOrderBatchSize = 1;

				if ($gConstants['optionscmlol'])
				{
					$maxOrderBatchSize = $licenseKeyArray['maxorderbatchsize'];
				}

				// we have to check to see if the user has logged in either via the high level product selector or via the online designer
				// if they have not then we need to throw an error. This is to stop any projects from being purged to early from the projects table and the
				// online basket table.
				if ($gSession['userid'] <= 0)
				{
                    $result = 'str_ErrorAccountNotActive';
				}

				$basketContentsArray = OnlineAPI_model::retrieveBasketContents($highLevelBasketRef, 1, false);
				$basketCount = $basketContentsArray['basketcount'];

				if (($basketCount + 1) > $maxOrderBatchSize)
				{
					$result = 'str_MessageShoppingCartFull';
				}

				if ($brandingArray['onlinedesignerlogouturl'] != '')
				{
					$shoppingCartURL = $brandingArray['onlinedesignerlogouturl'];
				}
				else
				{
					$shoppingCartURL = $hl_config['REDIRECTIONURL'];
				}

				$shoppingCartURL = UtilsObj::correctPath($shoppingCartURL, '/', true);
            }
            else
            {
            	$shoppingCartURL = UtilsObj::getBrandedWebUrl() . '?fsaction=Order.initialize&ref=' . $gSession['ref'];
            }

            if ($basketAPIWorkFlowType == TPX_BASKETWORKFLOWTYPE_HIGHLEVELCHECKOUT)
            {
            	$shoppingCartURL = UtilsObj::getBrandedWebUrl() . '?fsaction=Order.initialize&ref=' . $gSession['ref'].'&l='. $languageCode;
            }

            // if an external cart could be handling the order attempt to initialize the external cart
            if (($shoppingCartType > TPX_SHOPPINGCARTTYPE_INTERNAL) && ($basketAPIWorkFlowType != TPX_BASKETWORKFLOWTYPE_HIGHLEVELAPI))
            {
                $userID = $gSession['userid'];

                if ($gSession['userid'] > 0)
                {
                    $userDataArray = DatabaseObj::getUserAccountFromID($gSession['userid']);
                    $userLogin = $userDataArray['login'];
                    $userAccountCode = $userDataArray['accountcode'];
                    $userStatus = $userDataArray['addressupdated'];
                }
                else
                {
                    $userLogin = '';
                    $userAccountCode = '';
                    $userStatus = 1;
                }

				$buildExternalCartArray = false;

				if (($basketAPIWorkFlowType == TPX_BASKETWORKFLOWTYPE_LOWLEVELAPI) && ($cartArray[0]['source'] == TPX_SOURCE_ONLINE))
				{
					if (file_exists('../Customise/scripts/EDL_OnlineBasketAPI.php'))
					{
						require_once('../Customise/scripts/EDL_OnlineBasketAPI.php');
					}

					if (method_exists('OnlineBasketAPI', 'initialise'))
					{
						$buildExternalCartArray = true;
					}
					else
					{
						$basketAPIWorkFlowType = TPX_BASKETWORKFLOWTYPE_NORMAL;
					}
				}

				// we need to check to see if the external shopping cart is available.
				// if the initialise method exisits then we must set buildExternalCartArray to true so it builds the cart array.
				// we must also set the externalShoppingCartInitMethodExisits to true incase an order is being initiliased via the low level api.
				// this is incase the OnlineBasketAPI::initialise returns an empty shopping cart url.
				// If it does return an empty url then we must attempt to use the external shopping cart.

				self::includeExternalShoppingCart();

				$externalShoppingCartInitMethodExisits = false;

				if (method_exists('ExternalShoppingCart', 'initialise'))
				{
					if ($basketAPIWorkFlowType == TPX_BASKETWORKFLOWTYPE_NORMAL || $basketAPIWorkFlowType == TPX_BASKETWORKFLOWTYPE_HIGHLEVELCHECKOUT)
					{
						$buildExternalCartArray = true;
					}

					$externalShoppingCartInitMethodExisits = true;
				}

                if ($buildExternalCartArray)
                {
					$externalCartInitArray = Array();

                    // we only want to add the ref parameter if we know we are not adding to basket via the ecommerce API
                    if ($basketAPIWorkFlowType == TPX_BASKETWORKFLOWTYPE_NORMAL || $basketAPIWorkFlowType == TPX_BASKETWORKFLOWTYPE_HIGHLEVELCHECKOUT)
					{
                        $externalCartInitArray['ref'] = $gSession['ref'];
                    }

                    $externalCartInitArray['apiversion'] = $apiVersion;
                    $externalCartInitArray['languagecode'] = $languageCode;
                    $externalCartInitArray['ownercode'] = $ownerCode;
                    $externalCartInitArray['groupcode'] = $groupCode;
                    $externalCartInitArray['groupdata'] = $groupData;
                    $externalCartInitArray['brandcode'] = $licenseKeyArray['webbrandcode'];
                    $externalCartInitArray['userid'] = $userID;
                    $externalCartInitArray['userlogin'] = $userLogin;
                    $externalCartInitArray['userssotoken'] = $ssoToken;
                    $externalCartInitArray['userssoprivatedata'] = $ssoPrivateData;
                    $externalCartInitArray['useraccountcode'] = $userAccountCode;
                    $externalCartInitArray['userstatus'] = $userStatus;
                    $externalCartInitArray['uuid'] = $uuid;
                    $externalCartInitArray['origorderid'] = 0;
                    $externalCartInitArray['origordernumber'] = '';
                    $externalCartInitArray['shoppingcarturl'] = $shoppingCartURL;
                    $externalCartInitArray['reorder'] = 0;
                    $externalCartInitArray['batchref'] = $batchRef;
                    $externalCartInitArray['devicesettings'] = $deviceSettings;
					$externalCartInitArray['items'] = Array();

					// Connector data.
					$externalCartInitArray['shoppingcartdata'] = $shoppingCartData;

					// Build a list of all projects in the cart to be able to request the preview thumbnails for them all at once.
					$projectRefToRequestPreview = array_map(function($pCartItem)
					{
                        if ($pCartItem['source'] === TPX_SOURCE_ONLINE)
                        {
                            return $pCartItem['projectref'];
                        }
					}, $cartArray);

					// Request the thumbnail URLs.
					$requestProjectPreviewThumbnailResult = UtilsObj::requestProjectPreviewThumbnail($projectRefToRequestPreview);

					if ($requestProjectPreviewThumbnailResult['error'] === '')
					{
						$projectThumbnailData = $requestProjectPreviewThumbnailResult['data'];
					}

                    // add the items to the external cart array
                    for ($i = 0; $i < $cartItemsCount; $i++)
                    {
						$externalCartItemArray = $cartArray[$i];
						$externalCartItemArray['projectpreviewthumbnail'] = '';

						// Inject the projectpreviewthumbnail if one was returned.
						if (array_key_exists($externalCartItemArray['projectref'], $projectThumbnailData))
						{
							$projectPreviewThumbnailData = $projectThumbnailData[$externalCartItemArray['projectref']];

							if ($projectPreviewThumbnailData['error'] === TPX_ONLINE_ERROR_NONE)
							{
								$externalCartItemArray['projectpreviewthumbnail'] = $projectPreviewThumbnailData['thumbnail'];
							}
						}

                        if ($cartArray[$i]['source'] === TPX_SOURCE_DESKTOP)
                        {
                            //get the desktop thumbnail data
                            $projectThumbnailResultArray = DatabaseObj::getDesktopProjectThumbnailAvailabilityFromProjectRef($externalCartItemArray['projectref']);

                            //if we have found a valid desktop thumbnail then build the URL for it
                            if (($projectThumbnailResultArray['error'] === '') && ($projectThumbnailResultArray['available'] === true))
                            {
                                $externalCartItemArray['projectpreviewthumbnail'] = UtilsObj::buildDesktopProjectThumbnailWebURL($externalCartItemArray['projectref']);
                            }
                        }

						$externalCartItemArray['pictures'] = self::convertLineItemPicturesForExternalCart($externalCartItemArray['pictures']);

                        // add the calendar customisation data to the item
                        $externalCartItemArray['calendarcustomisations'] = self::convertLineItemCalendarCustomisationsForExternalCart($externalCartItemArray['calendarcustomisations']);

						// add the AI Component data to the item if applicable
						if (array_key_exists('aicomponent', $cartArray[$i]))
						{
							$externalCartItemArray['aicomponent'] = self::convertLineItemAIComponentForExternalCart($cartArray[$i]['aicomponent']);
						}

						// Only add Retro Print data if the key is present.
						if ($retroPrintsData != '')
						{
							$externalCartItemArray['retroprints'] = $retroPrintsArray;
						}

                        if (array_key_exists('components', $cartArray[$i]) && $cartArray[$i]['components'] !== '' && $cartArray[$i]['source'] === TPX_SOURCE_ONLINE)
                        {
                            $selectedComponentArray = json_decode($cartArray[$i]['components'], true);
                            $externalCartItemArray['qty'] = $selectedComponentArray[0]['productquantity'];
                            array_shift($selectedComponentArray);

                            $externalCartItemArray['components'] = self::convertLineItemComponentTreeForExternalCart($selectedComponentArray, $languageCode, false);

                            $componentSummaryHTML = '';

                            self::buildComponentSummary($externalCartItemArray['components'], $languageCode, $componentSummaryHTML, false);

                            $externalCartItemArray['componentsummary'] = $componentSummaryHTML;
                        }

                        // add the item to the list
                        $externalCartInitArray['items'][] = $externalCartItemArray;
					}


					$externalShoppingCartURL = '';
                    $calculatePriceScriptResult = 'NOTHANDLED';

                    $legacyPricingSystemInUse = UtilsObj::getArrayParam($ac_config,'USELEGACYPRICINGSYSTEM', 0);

                    // only attempt to calculate the line prices if the legacy pricing engine is not being used.
                    if ($legacyPricingSystemInUse == 0)
                    {
                        // As we might be calculating the price for each line in the order we need to take a copy of the session.
                        // This is due to the fact to calculate a line price the pricing engine requires a session to work from.
                        // If we did not take a copy of the session then the items array would be overwritten and only contain the last line the pricing engine processed.
                        $sessionBackup = $gSession;

						 // calculate line price
						 require_once('../Order/Order_model.php');

						 Order_model::orderSessionInitialize($languageCode, $appVersion, $licenseKeyArray['webbrandcode'], $shoppingCartType, $uuid, $jobTicketTemplate,
															 $licenseKeyArray['showpriceswithtax'], $licenseKeyArray['showtaxbreakdown'], $licenseKeyArray['showzerotax'], $licenseKeyArray['showalwaystaxtotal'],
															 $ownerCode, $groupCode, $groupData, $groupName, $groupAddress1, $groupAddress2, $groupAddress3, $groupAddress4, $groupAddressCity, $groupAddressCounty,
															 $groupAddressState, $groupPostCode, $groupCountryCode, $groupTelephoneNumber, $groupEmailAddress, $groupContactFirstName, $groupContactLastName,
															 $batchRef, $cartArray, $basketAPIWorkFlowType, '', false);

                        for ($i = 0; $i < $cartItemsCount; $i++)
                        {
                            if (($basketAPIWorkFlowType == TPX_BASKETWORKFLOWTYPE_LOWLEVELAPI) && ($cartArray[$i]['source'] == TPX_SOURCE_ONLINE))
                            {
                                if (method_exists('OnlineBasketAPI', 'calculateLinePrice'))
                                {
                                    $calculatePriceScriptResultArray = OnlineBasketAPI::calculateLinePrice($cartArray[$i]);
                                    $calculatePriceScriptResult = $calculatePriceScriptResultArray['result'];

                                    // check to see if we recieved a custom error from the script
                                    if (($calculatePriceScriptResult != 'TAOPIX') && ($calculatePriceScriptResult != 'NOTHANDLED'))
                                    {
                                        $calculatePriceScriptResult = 'CUSTOMERROR';
                                    }
                                }
                            }

                            if (($externalShoppingCartInitMethodExisits) && (($calculatePriceScriptResult == 'CUSTOMERROR') || ($calculatePriceScriptResult == 'NOTHANDLED')))
                            {
                                if (method_exists('ExternalShoppingCart', 'calculateLinePrice'))
                                {
                                    $calculatePriceScriptResultArray = ExternalShoppingCart::calculateLinePrice($cartArray[$i]);
                                    $calculatePriceScriptResult = $calculatePriceScriptResultArray['result'];

                                    // check to see if we recieved a custom error from the script
                                    if (($calculatePriceScriptResult != 'TAOPIX') && ($calculatePriceScriptResult != 'NOTHANDLED'))
                                    {
                                        $calculatePriceScriptResult = 'CUSTOMERROR';
                                    }
                                }
                            }

                            if ($calculatePriceScriptResult == 'TAOPIX')
                            {
                                $gSession['order']['uselegacypricingsystem'] = 0;
                                $gSession['order']['orderFooterCheckboxes'] = array();
                                $gSession['order']['currencyexchangerate'] = $currencyExchangeRate;
                                $gSession['order']['currencydecimalplaces'] = $currencyDecimalPlaces;

                                $gSession['order']['billingcustomercountrycode'] = $licenseKeyArray['countrycode'];
                                $gSession['order']['billingcustomerregioncode'] = $licenseKeyArray['regioncode'];
                                $gSession['shipping'][0]['shippingcustomercountrycode'] = $licenseKeyArray['countrycode'];
                                $gSession['shipping'][0]['shippingcustomerregioncode'] = $licenseKeyArray['regioncode'];

                                $productCodes = array();
                                Order_model::buildOrderLineComponentStructure($productCodes, $currencyExchangeRate, $currencyDecimalPlaces, $i);

                                Order_model::updateOrderTaxRate();

                                $externalCartInitArray['items'][$i]['itemtotalsell'] = $gSession['items'][$i]['itemtotalsell'];
                                $externalCartInitArray['items'][$i]['itemproducttotalsell'] = $gSession['items'][$i]['itemproducttotalsell'];
                                $externalCartInitArray['items'][$i]['itemsubtotal'] = $gSession['items'][$i]['itemsubtotal'];
                                $externalCartInitArray['items'][$i]['itemtotalsellwithtax'] = $gSession['items'][$i]['itemtotalsellwithtax'];

                                $externalCartInitArray['items'][$i]['pictures'] = self::convertLineItemPicturesForExternalCart($gSession['items'][$i]['pictures']);
								$externalCartInitArray['items'][$i]['calendarcustomisations'] = self::convertLineItemCalendarCustomisationsForExternalCart($gSession['items'][$i]['calendarcustomisations']);

                                // add the AI Component data to the item if applicable
                                 if (array_key_exists('aicomponent', $externalCartInitArray['items'][$i]))
                                 {
                                    $externalCartInitArray['items'][$i]['aicomponent'] = self::convertLineItemAIComponentForExternalCart($gSession['items'][$i]['aicomponent']);
                                 }

                                $externalCartInitArray['items'][$i]['externalassets'] = $gSession['items'][$i]['itemexternalassets'];

                                if (array_key_exists('components', $cartArray[$i]) && $cartArray[$i]['source'] === TPX_SOURCE_ONLINE)
                                {
                                    $externalCartInitArray['items'][$i]['components'] = self::convertLineItemComponentTreeForExternalCart($externalCartInitArray['items'][$i]['components'], $languageCode, true);
                                }

                                $externalCartInitArray['items'][$i]['itemtotalcost'] = $gSession['items'][$i]['itemtotalcost'];

                                // reset the script result for subsequest line items
                                $calculatePriceScriptResult = 'NOTHANDLED';
                            }
                            else if ($calculatePriceScriptResult == 'CUSTOMERROR')
                            {
                                // we have encounterd an error for at least one item in the cart. We cannot continue.
                                break;
                            }
                        }

                        // Copy the session back so that the cartitems are correct in the session database session record.
                        $gSession = $sessionBackup;
					}

                    if ($calculatePriceScriptResult != 'CUSTOMERROR')
                    {
                        if (($basketAPIWorkFlowType == TPX_BASKETWORKFLOWTYPE_LOWLEVELAPI) && ($cartArray[0]['source'] == TPX_SOURCE_ONLINE))
                        {
                            $externalShoppingCartConfig = OnlineBasketAPI::initialise($externalCartInitArray);
                            $externalShoppingCartURL = $externalShoppingCartConfig['shoppingcarturl'];
                            $externalShoppingCartConfig['usecustomshoppingcart'] = true;
                        }

                        if (($externalShoppingCartURL == '') && ($externalShoppingCartInitMethodExisits))
                        {
                            $externalShoppingCartConfig = ExternalShoppingCart::initialise($externalCartInitArray);
                            $externalShoppingCartURL = $externalShoppingCartConfig['shoppingcarturl'];
                        }

                        if ($externalShoppingCartConfig['result'] == '')
                        {
                            if (($externalShoppingCartConfig['usecustomshoppingcart']) && ($externalShoppingCartURL != ''))
                            {
                                $shoppingCartURL = $externalShoppingCartURL;
								$shoppingCartData = $externalShoppingCartConfig['shoppingcartdata'] ?? $shoppingCartData;

                                DatabaseObj::addProjectOrderDataCache($externalCartInitArray, $shoppingCartData);

                                if (($basketAPIWorkFlowType == TPX_BASKETWORKFLOWTYPE_NORMAL) || ($basketAPIWorkFlowType == TPX_BASKETWORKFLOWTYPE_HIGHLEVELCHECKOUT))
                                {
                                    // start the web session as the user will not be logging in via taopix web to start it
                                    $recordID = DatabaseObj::startSession(-1, '', '', TPX_LOGIN_API, '', '', $externalCartInitArray['brandcode'], $externalCartInitArray['groupcode'], '', Array());
                                }
                            }
                            else
                            {
                                if (($basketAPIWorkFlowType == TPX_BASKETWORKFLOWTYPE_LOWLEVELAPI))
                                {
                                    if ($userID > 0)
                                    {
                                        // include the shopping cart module
                                        require_once('../Order/Order_model.php');

                                        // inititalise function with the EDL_OnlineBasketAPI has not returned a shoppingcart url.
                                        // this means that the order will be passed to the Taopix cart therfore we must create an order sesison.
                                        $resultArray = Order_model::orderSessionInitialize($languageCode, $appVersion, $licenseKeyArray['webbrandcode'], $shoppingCartType, $uuid, $jobTicketTemplate,
                                                $licenseKeyArray['showpriceswithtax'], $licenseKeyArray['showtaxbreakdown'], $licenseKeyArray['showzerotax'], $licenseKeyArray['showalwaystaxtotal'],
                                                $ownerCode, $groupCode, $groupData, $groupName, $groupAddress1, $groupAddress2, $groupAddress3, $groupAddress4, $groupAddressCity, $groupAddressCounty,
                                                $groupAddressState, $groupPostCode, $groupCountryCode, $groupTelephoneNumber, $groupEmailAddress, $groupContactFirstName, $groupContactLastName,
                                                $batchRef, $cartArray, $basketAPIWorkFlowType, $highLevelBasketRef, true);

                                        // if we create the order session successfully then we need to generate a new shoppingcarturl with the new seesion ref
                                        if ($resultArray['result'] == '')
                                        {
                                            $shoppingCartURL = UtilsObj::getBrandedWebUrl() . '?fsaction=Order.initialize&ref=' . $gSession['ref'];
                                            $externalShoppingCartConfig['usecustomshoppingcart'] = false;
                                        }
                                        else
                                        {
                                            $result = $resultArray['result'];
                                            $resultParam = $resultArray['resultparam'];
                                        }
                                    }
                                    else
                                    {
                                        $result = 'str_ErrorGuestCheckoutNotSupported';
                                        $resultParam = '';
                                    }
                                }

                                $gSession['order']['shoppingcarttype'] = TPX_SHOPPINGCARTTYPE_INTERNAL;
                                $shoppingCartType = TPX_SHOPPINGCARTTYPE_INTERNAL;
                            }
                        }
                        else
                        {
                            $result = 'CUSTOMERROR';
                            $resultParam = $externalShoppingCartConfig['result'];
                        }
                    }
                    else
                    {
                        $result = 'CUSTOMERROR';
                        $resultParam = $calculatePriceScriptResultArray['resultparam'];
                    }
                }
                else
                {
                	$gSession['order']['shoppingcarttype'] = TPX_SHOPPINGCARTTYPE_INTERNAL;
                	$shoppingCartType = TPX_SHOPPINGCARTTYPE_INTERNAL;
                }
            }

            // if this is v4.2 or later of the desktop designer write the order status cache file to state that the order is in progress
            if (($result == 'ORDER') && ($apiVersion > 4) && (($cartArray[0]['source'] == TPX_SOURCE_DESKTOP) ||
                    ($basketAPIWorkFlowType == TPX_BASKETWORKFLOWTYPE_HIGHLEVELCHECKOUT && stripos($_SERVER['HTTP_USER_AGENT'], 'TPXWebView') !== false)))
            {
                if (Order_Model::writeOrderStatusCacheFile() != '')
                {
                    // get the weburl from the default brand
					$defaultBrand = DatabaseObj::getBrandingFromCode('');

					$statusURL = UtilsObj::getOrderStatusCacheURL($defaultBrand['weburl'], $batchRef);
					$gSession['order']['statusurl'] = $statusURL;
                }
            }

			// only do this if we know we will be using the Taopix shopping cart
            if (($basketAPIWorkFlowType == TPX_BASKETWORKFLOWTYPE_NORMAL) || ($basketAPIWorkFlowType == TPX_BASKETWORKFLOWTYPE_HIGHLEVELCHECKOUT) ||
                (($basketAPIWorkFlowType == TPX_BASKETWORKFLOWTYPE_LOWLEVELAPI) && (! $externalShoppingCartConfig['usecustomshoppingcart'])))
			{
                // finally update the session record as we may have changed some values
				if ($gSession['ref'] > 0)
				{
                    DatabaseObj::updateSession();
				}
            }
        }

        // if the order source is the online designer flag any existing order items so that the online project cannot be modified
        if (($result == 'UPLOAD') && ($gConstants['optiondesol']) && ($cartArray[0]['source'] == TPX_SOURCE_ONLINE))
        {
            self::updateProjectCanModify($activeOrderedItemsArray[0]['orderid'], $activeOrderedItemsArray[0]['recordid'], $activeOrderedItemsArray[0]['uploadref']);
        }

        $resultArray['result'] = $result;
        $resultArray['resultparam'] = $resultParam;
        $resultArray['apiversion'] = $apiVersion;
        $resultArray['ref'] = $gSession['ref'];
        $resultArray['shoppingcarttype'] = $shoppingCartType;
        $resultArray['shoppingcarturl'] = $shoppingCartURL;
        $resultArray['shoppingcartdata'] = $shoppingCartData;
        $resultArray['statusurl'] = $statusURL;
        $resultArray['items'] = &$processedItemsArray;
        $resultArray['batchref'] = $batchRef;
        $resultArray['cancreateaccounts'] = $canCreateAccounts;
        $resultArray['pagecount'] = $pageCount;
        $resultArray['orderpagecount'] = $orderPageCount;
        $resultArray['covermaxpagecount'] = $coverMaxPageCount;
        $resultArray['papermaxpagecount'] = $paperMaxPageCount;
        $resultArray['outputdeliverymethods'] = $outputDeliveryMethods;
        $resultArray['languageCode'] = $languageCode;
        $resultArray['webbrandcode'] = $webBrandCode;
        $resultArray['inactiveproductcollectioncode'] = $inactiveProductCollectionCode;
        $resultArray['inactivelayoutcode'] = $inactiveLayoutCode;

        return $resultArray;
    }

    static function convertLineItemPicturesForExternalCart($pLineItemPictures)
    {
        $origPicturesArray = $pLineItemPictures['key'];
        $origPictureCount = count($origPicturesArray);

        $picturesArray = Array();
        for ($i2 = 0; $i2 < $origPictureCount; $i2++)
        {
            $asset = array(
                'aid' => '', // asset id
                'asc' => '', // asset service code
                'asn' => '', // asset service name
                'apt' => 0, // asset price type
                'ac' => 0.00, // asset cost
                'as' => 0.00 // asset sell
            );

            $pictureLookup = $origPicturesArray[$i2];
            $uniqueLookup = $pictureLookup . TPX_PICTURES_LOOKUP_SEPERATOR . $i2;
            $origPictureItem = $pLineItemPictures['data'][$pictureLookup];
            $origPrintData = $pLineItemPictures['printdata'][$uniqueLookup];

            // Asset data.
            if (array_key_exists($uniqueLookup, $pLineItemPictures['asset']))
            {
                $asset = $pLineItemPictures['asset'][$uniqueLookup];
            }

            // Merge data.
            $origPictureItem = array_merge($origPictureItem, $origPrintData, $asset);

            // Page/Picture name.
            $pictureName = $pLineItemPictures['pname'][$origPrintData['fn']];

            $pictureItem = Array();
            $pictureItem['setid'] = $origPictureItem['setid'];
            $pictureItem['setname'] = $origPictureItem['setname'];
            $pictureItem['componentqty'] = $origPictureItem['qty'];
            $pictureItem['componentcategory'] = $origPictureItem['category'];
            $pictureItem['componentcode'] = $origPictureItem['code'];
            $pictureItem['componentname'] = $origPictureItem['name'];
            $pictureItem['skucode'] = $origPictureItem['skucode'];
            $pictureItem['unitcost'] = $origPictureItem['unitcost'];
            $pictureItem['unitweight'] = $origPictureItem['unitweight'];
            $pictureItem['unitsell'] = $origPictureItem['us'];
            $pictureItem['subcomponentcategory'] = $origPictureItem['subcategory'];
            $pictureItem['subcomponentcode'] = $origPictureItem['subcode'];
            $pictureItem['subcomponentname'] = $origPictureItem['subname'];
            $pictureItem['subcomponentskucode'] = $origPictureItem['subskucode'];
            $pictureItem['subcomponentunitcost'] = $origPictureItem['subunitcost'];
            $pictureItem['subcomponentunitweight'] = $origPictureItem['subunitweight'];
            $pictureItem['subcomponentunitsell'] = (array_key_exists('subus', $origPictureItem) ? $origPictureItem['subus'] : 0.00);
            $pictureItem['assetservicecode'] = $origPictureItem['asc'];
            $pictureItem['assetservicename'] = $origPictureItem['asn'];
            $pictureItem['assetpricetype'] = $origPictureItem['apt'];
            $pictureItem['assetid'] = $origPictureItem['aid'];
            $pictureItem['assetcost'] = $origPictureItem['ac'];
            $pictureItem['assetsell'] = $origPictureItem['as'];
            $pictureItem['pageref'] = $origPictureItem['pageref'];
            $pictureItem['pagenumber'] = $origPictureItem['pagenumber'];
            $pictureItem['pagename'] = $pictureName;
            $pictureItem['boxref'] = $origPictureItem['boxref'];

            $picturesArray[] = $pictureItem;
        }

        return $picturesArray;
    }

    static function convertLineItemCalendarCustomisationsForExternalCart($pLineItemCalendarCustomisations)
    {
        $calendarCustomisationArray = Array();

        foreach ($pLineItemCalendarCustomisations as $origCalendarCustomisation)
        {
            $calendarCustomisation = Array();
            $calendarCustomisation['componentqty'] = $origCalendarCustomisation['componentqty'];
            $calendarCustomisation['componentcategory'] = $origCalendarCustomisation['componentcategory'];
            $calendarCustomisation['componentcode'] = $origCalendarCustomisation['componentcode'];
            $calendarCustomisation['componentname'] = $origCalendarCustomisation['componentname'];
            $calendarCustomisation['skucode'] = $origCalendarCustomisation['skucode'];
            $calendarCustomisation['unitcost'] = $origCalendarCustomisation['unitcost'];
            $calendarCustomisation['unitweight'] = $origCalendarCustomisation['unitweight'];
            $calendarCustomisation['unitsell'] = $origCalendarCustomisation['unitsell'];

            $calendarCustomisationArray[] = $calendarCustomisation;
        }

        return $calendarCustomisationArray;
	}

	static function convertLineItemAIComponentForExternalCart($pLineItemAIComponent)
	{
		$returnArray = Array();

		$returnArray['componentqty'] = $pLineItemAIComponent['componentqty'];
		$returnArray['componentcategory'] = $pLineItemAIComponent['categorycode'];
		$returnArray['componentcode'] = $pLineItemAIComponent['code'];
		$returnArray['componentname'] = $pLineItemAIComponent['name'];
		$returnArray['skucode'] = $pLineItemAIComponent['skucode'];
		$returnArray['unitcost'] = $pLineItemAIComponent['unitcost'];
		$returnArray['unitweight'] = $pLineItemAIComponent['weight'];
		$returnArray['unitsell'] = $pLineItemAIComponent['unitsell'];

		return $returnArray;
	}

    static function convertLineItemComponentTreeForExternalCart($pLineItemComponentTree, $pLocale, $pGetPrice)
    {
        $formattedComponentArray = array();
        $componentPricePathArray = array();
        $componentCacheArray = array();

        if ($pGetPrice)
        {
            global $gSession;

            // we need to build up an array of paths for the components selected.
            // this is so we can extract the prices for them from the session
            $componentPricePathArray = self::buildComponentPathArray($componentPricePathArray, $pLineItemComponentTree, $pLocale);

            // add the checkboxes at the root of the product
            foreach ($gSession['items'][0]['checkboxes'] as $checkbox)
            {
                if (array_key_exists($checkbox['code'], $componentPricePathArray))
                {
                    $componentPricePathArray[$checkbox['code']]['componenttotalsell'] = $checkbox['totalsell'];
                    $componentPricePathArray[$checkbox['code']]['componenttotaltax'] = $checkbox['totaltax'];
                }
            }

            // add the checkboxes at the root of the LINEFOOTER
            foreach ($gSession['items'][0]['lineFooterCheckboxes'] as $checkbox)
            {
                if (array_key_exists($checkbox['path'] . $checkbox['code'], $componentPricePathArray))
                {
                    $targetPath = $checkbox['path'] . $checkbox['code'];
                    $componentPricePathArray[$targetPath]['componenttotalsell'] = $checkbox['totalsell'];
                    $componentPricePathArray[$targetPath]['componenttotaltax'] = $checkbox['totaltax'];
                }
            }
            // add sections checkboxes for component and subcomponent
            $componentPricePathArray = self::getComponentPricesFromPath($componentPricePathArray, $gSession['items'][0]['sections']);
            // add sections checkboxes for component and subcomponent in LINEFOOTER
            $componentPricePathArray = self::getComponentPricesFromPath($componentPricePathArray, $gSession['items'][0]['lineFooterSections']);


            $formattedComponentArray = self::applyPriceToFormattedComponentArray($componentPricePathArray, $pLineItemComponentTree);
        }
        else
        {
            foreach($pLineItemComponentTree as $key => $section)
            {
                $formattedComponentArray = self::getFormattedComponentData($formattedComponentArray, $section['children'], $pLocale, $componentPricePathArray, $componentCacheArray);
            }

            $formattedComponentArray = (self::mapSubComponentsToParentComponent($formattedComponentArray));
        }

        return $formattedComponentArray;
    }

    static function getFormattedComponentData(&$pFormattedComponentArray, $pSelectedComponentsArray, $pLocale, $pComponentPricePathArray, &$pComponentCacheArray)
    {
        foreach($pSelectedComponentsArray as $key => $component)
        {
            // if the componentcode is empty then we know we are dealing with a section and dont want to record it
            if ($component['componentcode'] != '')
            {
				if (($component['islist'] == 0) && ($component['selected'] == 0))
				{
					// Checkbox is not selected so ignore it.
					continue;
				}

                if (array_key_exists($component['componentcode'], $pComponentCacheArray))
                {
                    $componentArray = $pComponentCacheArray[$component['componentcode']];
                }
                else
                {
                    $componentArray = DatabaseObj::getComponentByCode($component['componentcode']);
                    $pComponentCacheArray[$component['componentcode']] = $componentArray;
                }

                $componentItem = array();
                $path = ($component['path'] == '' || $component['path'] == '$LINEFOOTER\\') ? $component['path'] : str_replace('\\\\', '\\', $component['path']) . '\\';

                $codeArray = explode('.', $component['componentcode']);
                $componentItem['componentcode'] = $component['componentcode'];
                $componentItem['componentcategorycode'] = $codeArray[0];
                $componentItem['componentlocalcode'] = $codeArray[1];
                $componentItem['componentpath'] = $path;
                $componentItem['componentname'] = $component['name'];
                $componentItem['quantity'] = $component['quantity'];
                $componentItem['skucode'] = $componentArray['skucode'];
                $componentItem['componentunitcost'] = $componentArray['unitcost'];
                $componentItem['componentunitweight'] = $componentArray['weight'];
                $componentItem['pricingmodel'] = $component['pricingmodel'];
                $componentItem['islist'] = $component['islist'];
                $componentItem['checkboxselected'] = ($component['islist'] == 1) ? 0 : 1;

                $keywordsArray = self::formatKeywords($component['keywords'], $pLocale);

                $componentItem['keywords'] = $keywordsArray['keywords'];
                $componentItem['metadatacodelist'] = $keywordsArray['metadatacodelist'];
                $componentItem['subcomponent'] = array();
                $pFormattedComponentArray[] = $componentItem;
            }

            if (!empty($component['children']))
            {
                self::getFormattedComponentData($pFormattedComponentArray, $component['children'], $pLocale, $pComponentPricePathArray, $pComponentCacheArray);
            }

        }
        return $pFormattedComponentArray;
    }

    static function mapSubComponentsToParentComponent($pComponentArray)
    {
        $returnArray = array();
        $itemCount = count($pComponentArray);

		for ($i = 0; $i < $itemCount; $i++)
		{
			$component = &$pComponentArray[$i];
			$parentPath = $component['componentpath'];

			if ($parentPath == '')
			{
				$returnArray[] = $component;
				continue;
			}

			for ($j = $i + 1; $j < $itemCount; $j++)
			{
				if (stripos($pComponentArray[$j]['componentpath'], $component['componentpath']) === false)
				{
					break;
				}

				$i = $j;
				$component['subcomponent'][] = $pComponentArray[$j];
			}

			$returnArray[] = $component;
		}

        return $returnArray;
    }

    static function buildComponentPathArray(&$pPathArray, $pSelectedComponentsArray)
    {
        foreach($pSelectedComponentsArray as $component)
        {
            $path = $component['componentpath'] . $component['componentcode'];

            if ($path == '' && $component['islist'] == 0)
            {
                $path = $component['componentcode'];
            }

            $pPathArray[$path] = [];

            if (!empty($component['subcomponent']))
            {
                self::buildComponentPathArray($pPathArray, $component['subcomponent']);
            }
        }

        return $pPathArray;
    }

    static function applyPriceToFormattedComponentArray($pComponentPricePathArray, &$pSelectedComponentsArray)
    {
        foreach($pSelectedComponentsArray as &$component)
        {
            $targetPath = $component['componentpath'] . $component['componentcode'];
            $component['componenttotalsell'] = $pComponentPricePathArray[$targetPath]['componenttotalsell'];
            $component['componenttotaltax'] = $pComponentPricePathArray[$targetPath]['componenttotaltax'];

            if (!empty($component['subcomponent']))
            {
                self::applyPriceToFormattedComponentArray($pComponentPricePathArray, $component['subcomponent']);
            }
        }

        return $pSelectedComponentsArray;
    }

    static function getComponentPricesFromPath(&$pComponentPricePathArray, $pSections)
	{
        foreach ($pSections as $section)
		{
			if (array_key_exists($section['path'] . $section['code'], $pComponentPricePathArray))
            {
                $targetPath = $section['path'] . $section['code'];
                $pComponentPricePathArray[$targetPath]['componenttotalsell'] = $section['totalsell'];
                $pComponentPricePathArray[$targetPath]['componenttotaltax'] = $section['totaltax'];
            }

            foreach ($section['checkboxes'] as $checkbox)
			{
				if (array_key_exists($checkbox['path'] . $checkbox['code'], $pComponentPricePathArray))
                {
                    $targetPath = $checkbox['path'] . $checkbox['code'];
                    $pComponentPricePathArray[$targetPath]['componenttotalsell'] = $checkbox['totalsell'];
                    $pComponentPricePathArray[$targetPath]['componenttotaltax'] = $checkbox['totaltax'];
                }
			}

			if (!empty($section['subsections']))
			{
				self::getComponentPricesFromPath($pComponentPricePathArray, $section['subsections']);
			}
		}

		return $pComponentPricePathArray;
	}

    static function formatKeywords($pKeywords, $pLocale = '')
    {
        $returnArray = array('keywords' => array(), 'metadatacodelist' => '');
        $refList = array();
        foreach ($pKeywords as &$keyword)
        {
            $refList[] = $keyword['code'];
            if (($keyword['type'] == 'RADIOGROUP') || ($keyword['type'] == 'POPUP'))
            {
                $thumbnailURL = '';
                $nameArray = explode('<br>', $keyword['name']);
                $keyword['name'] = LocalizationObj::getLocaleString($nameArray[0], $pLocale, true);
                $valueCode = $keyword['valuecode'];

                $codeArray = explode('<br>', $keyword['flags']);
                $count2 = count($codeArray);

                for ($j = 0; $j < $count2; $j++)
                {
                    $itemValue = $codeArray[$j];

                    if ($keyword['type'] == 'RADIOGROUP')
                    {
                        $itemValueArray = explode('<p>', $itemValue);
                        $itemValue = $itemValueArray[0];
                        if(count($itemValueArray) == 2)
                        {
                            $thumbnailURL = $itemValueArray[1];
                        }
                    }

                    if ($itemValue == $valueCode)
                    {
                        $keyword['value'] = $nameArray[$j + 1];

                        if ($pLocale != '')
                        {
                            $keyword['value'] = LocalizationObj::getLocaleString($keyword['value'], $pLocale, true);
                        }

                        break;
                    }
                }
            }
            else
            {
                $keyword['name'] = LocalizationObj::getLocaleString($keyword['name'], $pLocale, true);
            }

            unset($keyword['flags']);
        }

        $returnArray['keywords'] = $pKeywords;
        $returnArray['metadatacodelist'] = implode(',', $refList);

        return $returnArray;
    }

    static function buildComponentSummary($pComponents, $pLocale, &$pComponentSummaryHTML, $pSubComponent)
    {
        if (!$pSubComponent)
        {
            $className = 'tpx_componentitem';
            $pComponentSummaryHTML .= '<ul class="tpx_component">';
        }
        else
        {
            $className = 'tpx_subcomponentitem';
            $pComponentSummaryHTML .= '<ul class="tpx_subcomponent">';
        }

        foreach($pComponents as $component)
        {
            $pComponentSummaryHTML .= '<li class="'. $className .'">'. $component['quantity'] . ' x ' . LocalizationObj::getLocaleString($component['componentname'], $pLocale, true);

            $pComponentSummaryHTML .= '<ul class="tpx_metadata">';

            foreach ($component['keywords'] as $keyword)
            {
                $value = $keyword['value'];

                if ($keyword['type'] == 'CHECKBOX')
                {
                    $value = ($value == 1) ? '&#x2714;' : '&#x2718;';
                }

                $pComponentSummaryHTML .= '<li class="tpx_metadataitem">' . $keyword['name'] . ' : ' . $value . '</li>';
            }

            $pComponentSummaryHTML .= '</ul>';

            if (!empty($component['subcomponent']))
            {
                self::buildComponentSummary($component['subcomponent'], $pLocale, $pComponentSummaryHTML, true);
            }

            $pComponentSummaryHTML .= '</li>';
        }

        $pComponentSummaryHTML .= '</ul>';
    }


    static function updateProjectCanModify($pOrderID, $pOrderItemID, $pUploadRef)
    {
        $resultArray = Array('error' => '');

        $error = '';
        $ownerCode = '';
        $groupCode = '';
        $webBrandCode = '';
        $currentCompanyCode = '';
        $projectRef = '';

        $dbObj = DatabaseObj::getGlobalDBConnection();

        if ($dbObj)
        {
            $sql = 'SELECT oh.`ownercode`, oh.`userid`, oh.`groupcode`, oh.`webbrandcode`, ot.`currentcompanycode`, ot.`projectref` FROM `ORDERHEADER` oh, `ORDERITEMS` ot ';
            $sql .= 'WHERE ot.`orderid` = oh.`id` AND oh.`id` = ? AND ot.`id` = ?';
            $stmt = $dbObj->prepare($sql);
            if ($stmt)
            {
                if ($stmt->bind_param('ii', $pOrderID, $pOrderItemID))
                {
                    if ($stmt->bind_result($ownerCode, $userID, $groupCode, $webBrandCode, $currentCompanyCode, $projectRef))
                    {
                        if ($stmt->execute())
                        {
                            $stmt->fetch();
                        }
                        else
                        {
                            $error = 'updateProjectCanModify1: execute ' . $dbObj->error;
                        }
                    }
                    else
                    {
                        $error = 'updateProjectCanModify1: bind result ' . $dbObj->error;
                    }
                }
                $stmt->free_result();
                $stmt->close();
            }
            else
            {
                $error = 'updateProjectCanModify1: Prepare ' . $dbObj->error;
            }
            $dbObj->close();
        }

        if ($error == '')
        {
            $taskInfo = DatabaseObj::getTask('TAOPIX_ONLINEORDERCREATION');

            if ($taskInfo['result'] == '')
            {
                $eventResultArray = DatabaseObj::createEvent('TAOPIX_ONLINEORDERCREATION', $currentCompanyCode,
                                                            $groupCode, $webBrandCode,
                                                            $taskInfo['nextRunTime'], 0, $ownerCode,
                                                            $projectRef, $pUploadRef,
                                                            $pOrderID, '', '', '', '', $pOrderID,
                                                            $pOrderItemID, $userID, '', '', $userID);

                if ($eventResultArray['result'] == '')
                {
                    $dbObj = DatabaseObj::getGlobalDBConnection();
                    if ($dbObj)
                    {
                        $sql = 'UPDATE `ORDERITEMS` SET `canmodify` = 0 WHERE `id` = ?';
                        $stmt = $dbObj->prepare($sql);
                        if ($stmt)
                        {
                            if ($stmt->bind_param('i',$pOrderItemID))
                            {
                                $stmt->execute();
                            }
                            else
                            {
                                $error = 'updateProjectCanModify2: Bind_param ' . $dbObj->error;
                            }
                        }
                        else
                        {
                            $error = 'updateProjectCanModify2: Prepare ' . $dbObj->error;
                        }
                    }
                    $dbObj->close();
                }
                else
                {
                    $error = $eventResultArray['result'];
                }
            }
        }

        $resultArray['error'] = $error;

        return $resultArray;
    }


    static function orderConfirm()
    {
        global $ac_config;
        global $gSession;

        $result = 'ORDER';
		$resultParam = '';
        $shoppingCartType = TPX_SHOPPINGCARTTYPE_INTERNAL;
        $shoppingCartURL = '';
        $processedItemsArray = Array();

        $apiVersion = (int)UtilsObj::getPOSTParam('version', '1');
        $languageCode = UtilsObj::getPOSTParam('langcode', 'en');

        if (array_key_exists('ref', $_POST))
        {
            $recordID = $_POST['ref'];
            if ($recordID > 0)
            {
                switch ($gSession['result'])
                {
                    case '':
                        // no error was returned so the session data is still valid. we must now determine if the job ticket has been completed
                        $orderArray = DatabaseObj::orderProcessed($gSession['items'][0]['itemuploadbatchref'], $gSession['items'][0]['itemuploadref']);
                        $processedItemsArray = $orderArray['processeditemslist'];
                        $processedItemsCount = count($processedItemsArray);

                        if (count($processedItemsArray) > 0)
                        {
                           $result = 'UPLOAD';
                           $shoppingCartType = $processedItemsArray[0]['shoppingcarttype'];
                           $shoppingCartURL = UtilsObj::correctPath($ac_config['WEBURL']) . '?fsaction=Order.initialize&ref=' . $gSession['ref'];
                        }

                        if (($result == 'ORDER') && ($gSession['order']['shoppingcarttype'] > TPX_SHOPPINGCARTTYPE_INTERNAL))
                        {
                            self::includeExternalShoppingCart();
                            if (method_exists('ExternalShoppingCart', 'checkStatus'))
                            {
                                // update the session expiration time
                                if ($gSession['sessionsecondstoexpiration'] < 120)
                                {
                                    DatabaseObj::updateSessionExpire($gSession['ref']);
                                }

                                $statusParam = Array();
                                $statusParam['apiversion'] = $apiVersion;
                                $statusParam['languagecode'] = $languageCode;
                                $statusParam['groupcode'] = $gSession['licensekeydata']['groupcode'];
                                $statusParam['brandcode'] = $gSession['webbrandcode'];
                                $statusParam['sessionid'] = $gSession['ref'];

                                $currentStatus = ExternalShoppingCart::checkStatus($statusParam);

                                if ($currentStatus == 'ORDER')
                                {
                                    $result = 'ORDER';
                                }
                                else
                                {
                                    if ($currentStatus == 'CANCEL')
                                    {
                                        $result = 'CANCEL';
                                    }
                                    else
                                    {
                                        $result = 'CUSTOMERROR';
                                        $resultParam = $currentStatus;
                                    }

                                    // we need to get all the projectrefs from the current cart session so we can delete the project order data cache records
                                    $cartProjectRefArray = self::getCartProjectRefsFromSession();

                                    // clean up order project cache data
                                    DatabaseObj::insertCleanUpProjectOrderDataCacheTask($cartProjectRefArray);

                                    if (! method_exists('Order_model', 'cancel'))
                                    {
                                        // include the shopping cart module
                                        require_once('../Order/Order_model.php');
                                    }

                                    Order_model::cancel();
                                }
                            }
                        }

                       break;
                    case  'str_ErrorNoSessionRef':
                       // no session data reference exists anymore so the user must have cancelled the order in the browser
                       $result = 'CANCEL';
                       break;
                }

                // delete the order status cache file if it is no longer required
                if ($result != 'ORDER')
                {
                    if (! method_exists('Order_model', 'deleteOrderStatusCacheFile'))
                    {
                        // include the shopping cart module
                        require_once('../Order/Order_model.php');
                    }

                    Order_Model::deleteOrderStatusCacheFile(UtilsObj::getPOSTParam('batchref'));
                }
            }
        }

        $resultArray['result'] = $result;
        $resultArray['resultparam'] = $resultParam;
        $resultArray['apiversion'] = $apiVersion;
        $resultArray['languageCode'] = $languageCode;
        $resultArray['ref'] = $gSession['ref'];
        $resultArray['shoppingcarttype'] = $shoppingCartType;
        $resultArray['shoppingcarturl'] = $shoppingCartURL;
        $resultArray['statusurl'] = '';
        $resultArray['items'] = &$processedItemsArray;

        return $resultArray;
    }


    static function cancelOrderSessionPOST()
    {
    	global $gSession;

    	$resultArray = array();

        $apiVersion = (int)UtilsObj::getPOSTParam('version', '1');
        $languageCode = UtilsObj::getPOSTParam('langcode', 'en');
        $forceCancel = (int)UtilsObj::getPOSTParam('forcecancel', '0');

		// set the default return result (we do this here as the call to cancel the session is conditional and can return different versions of the array)
        // if we do not call the function then we just assume the order has been cancelled
        $resultArray['result'] = 'CANCEL';
        $resultArray['apiversion'] = $apiVersion;
        $resultArray['languageCode'] = $languageCode;
        $resultArray['ref'] = $gSession['ref'];

		// if we have been supplied with a valid ref in the POST data attempt to cancel the session
        if (array_key_exists('ref', $_POST))
        {
            $recordID = $_POST['ref'];
            if ($recordID > 0)
            {
                $resultArray = self::cancelOrderSession($apiVersion, $languageCode, $forceCancel);
            }
        }

        return $resultArray;
    }


    static function cancelOrderSession($pAPIVersion, $pLanguageCode, $pForceCancel)
    {
        global $gSession;

        $resultArray = array();
        $result = 'CANCEL';

        if ($gSession['result'] == '')
        {
            // no error was returned so the session data is still valid. we must now determine if the session can be cancelled
            if ($gSession['order']['currentstage'] != 'complete')
            {
				if ($gSession['order']['shoppingcarttype'] > TPX_SHOPPINGCARTTYPE_INTERNAL)
                {
                    self::includeExternalShoppingCart();
                    if (method_exists('ExternalShoppingCart', 'cancelSession'))
                    {
                        $statusParam = array();
                        $statusParam['apiversion'] = $pAPIVersion;
                        $statusParam['languagecode'] = $pLanguageCode;
                        $statusParam['groupcode'] = $gSession['licensekeydata']['groupcode'];
                        $statusParam['brandcode'] = $gSession['webbrandcode'];
                        $statusParam['sessionid'] = $gSession['ref'];
                        $statusParam['forcecancel'] = $pForceCancel;

                        if (ExternalShoppingCart::cancelSession($statusParam) == false)
                        {
                            $result = 'ORDERCANCELCONFIRM';
                        }
                    }
                }
                else
                {
                    // if we are not forcing a cancel check to see if we are taking payment
                    if ($pForceCancel == 0)
                    {
                        if ($gSession['order']['currentstage'] == 'promptforcard')
                        {
                            // we are taking payment so we don't want to allow the user to cancel
                            $result = 'ORDERCANCELCONFIRM';
                        }
                    }
                }
            }
            else
            {
                // it appears as if the order has been completed so check further
                $newResultArray = self::orderConfirm();
                if (($newResultArray['result'] == 'UPLOAD') || ($newResultArray['result'] == 'CUSTOMERROR'))
                {
                    return $newResultArray;
                }
            }

            // if no error has been returned or if we are forced to cancel the session do it now
            if (($result == 'CANCEL') || ($pForceCancel == 1))
            {
                // rather than deleting a session we just disable it
                // the user cannot log back in but the session is still there just incase it is still needed (cancelling on a payment page)
                DatabaseObj::disableSession($gSession['ref'], 0);
            }

            // delete the order status cache file if it is no longer required
            if ($result == 'CANCEL')
            {
                // include the shopping cart module
                require_once('../Order/Order_model.php');

                Order_Model::deleteOrderStatusCacheFile();
            }
        }

        $resultArray['result'] = $result;
        $resultArray['apiversion'] = $pAPIVersion;
        $resultArray['languageCode'] = $pLanguageCode;
        $resultArray['ref'] = $gSession['ref'];

        return $resultArray;
    }

    static function uploadCompleted()
    {
        // include the mailing address, email creation, data export and social sharing modules
        require_once('../Utils/UtilsAddress.php');
        require_once('../Utils/UtilsEmail.php');
        require_once('../Utils/UtilsDataExport.php');
        require_once('../Share/Share_model.php');

        global $ac_config;
        global $gConstants;
        global $gSession;

        $result = '';
        $resultParam = '';
        $uploadRef = '';
        $uploadMethodString = '';
        $uploadMethod = TPX_UPLOAD_DELIVERY_METHOD_INTERNET;
        $status = TPX_ITEM_STATUS_AWAITING_FILES;
        $uploadTypeString = '';
        $uploadType = TPX_UPLOAD_DATA_TYPE_RENDERED;
        $uploadAppVersion = '';
        $uploadAppPlatform = '';
        $uploadAppCPUType = '';
        $uploadAppOSVersion = '';
        $jobTicketArray = Array();
        $canUploadFiles = 1;
        $shoppingCartType = TPX_SHOPPINGCARTTYPE_INTERNAL;
        $recordId = 0;
        $userId = 0;
        $orderNumber = '';
        $shippingCustomerName = '';
        $shippingCustomerAddress1 = '';
        $shippingCustomerAddress2 = '';
        $shippingCustomerAddress3 = '';
        $shippingCustomerAddress4 = '';
        $shippingCustomerCity = '';
        $shippingCustomerCounty = '';
        $shippingCustomerState = '';
        $shippingCustomerPostCode = '';
        $shippingCustomerCountryCode = '';
        $shippingCustomerTelephoneNumber = '';
        $shippingCustomerEmailAddress = '';
        $shippingContactFirstName = '';
        $shippingContactLastName = '';
        $projectStartTime = '';
        $projectDuration = 0;
        $projectDataSize = 0;
        $projectUploadDuration = 0;

        $apiVersion = (int)UtilsObj::getPOSTParam('version', '1');
        $languageCode = UtilsObj::getPOSTParam('langcode', 'en');

        if (array_key_exists('ref', $_POST))
        {
            $uploadRef = $_POST['ref'];
        }
        else
        {
            $result = 'str_NoUploadRef';
        }

        if (array_key_exists('shoppingcarttype', $_POST))
        {
            $shoppingCartType = $_POST['shoppingcarttype'];
        }

        if ($uploadRef != '')
        {
            $brandingDefaults = DatabaseObj::getBrandingFromCode('');

            if (array_key_exists('method', $_POST))
            {
                $uploadMethodString = $_POST['method'];
                if ($uploadMethodString == 'UPLOAD')
                {
                    $uploadMethod = TPX_UPLOAD_DELIVERY_METHOD_INTERNET;
                    $canUploadFiles = 0;
                    if ($ac_config['SERVERLOCATION'] == 'REMOTE')
                    {
                        $status = TPX_ITEM_STATUS_FILES_ON_REMOTE_FTP_SERVER;
                    }
                    else
                    {
                        $status = TPX_ITEM_STATUS_FILES_RECEIVED;
                    }
                }
                else if ($uploadMethodString == 'MAIL')
                {
                    $uploadMethod = TPX_UPLOAD_DELIVERY_METHOD_MAIL;
                    $status = TPX_ITEM_STATUS_AWAITING_FILES;
                }
                else
                {
                    $result = 'str_NoUploadMethod';
                }

                if (array_key_exists('type', $_POST))
                {
                    $uploadTypeString = $_POST['type'];
                    if ($uploadTypeString == 'RAW')
                    {
                        $uploadType = TPX_UPLOAD_DATA_TYPE_RAW;
                    }
                }

                // retrieve the application version & platform
                $uploadAppVersion = UtilsObj::getPOSTParam('appversion');
                $uploadAppPlatform = UtilsObj::getPOSTParam('appplatform');
                $uploadAppCPUType = UtilsObj::getPOSTParam('appcputype');
                $uploadAppOSVersion = UtilsObj::getPOSTParam('apposversion');

                // retrieve some stats
                $projectStartTime = UtilsObj::getPOSTParam('projectstarttime', '');
                $projectDuration = UtilsObj::getPOSTParam('projectduration', 0);
                $projectDataSize = (int)UtilsObj::getPOSTParam('projectdatasize', 0);
                $projectUploadDuration = (int)UtilsObj::getPOSTParam('projectuploadduration', 0);
            }
            else
            {
                $result = 'str_NoUploadMethod';
            }
        }

        // if we have no error create the order preview thumbnails
        if ($result == '' && (TPX_SHOPPINGCARTTYPE_INTERNAL === (int) $shoppingCartType || 1 === (int) $ac_config['ENABLE_PREVIEW_THUMBNAILS'] ?? 0))
        {
            UtilsObj::processOrderThumbnails($apiVersion, $uploadRef);
        }

        // move the order folder if required
//        if ($result == '')
//        {
//            $sourcePath = $ac_config['INTERNALSYSTEMUPLOADROOTPATH'];
//            if ($ac_config['FTPGROUPORDERSBYCODE'] == '1')
//            {
//                $sourcePath = $sourcePath .
//            }
//            $sourcePath = $sourcePath . 'Order_' . $uploadRef;
//            $destPath = $ac_config['PRIVATEORDERSROOTPATH'];
//            mkdir($destPath, 0777, true);
//            $destPath = $destPath . 'Order_' . $uploadRef;
//            rename ($sourcePath, $destPath);
//        }

        // we need to update the order upload method and status and then perform any relevant email notifications
        if ($result == '')
        {
            $resultArray = DatabaseObj::updateOrderUploadStatus($uploadRef, $uploadType, $uploadMethod, $uploadAppVersion, $uploadAppPlatform, $uploadAppCPUType, $uploadAppOSVersion,
                                                                $projectStartTime, $projectDuration, $projectDataSize, $projectUploadDuration, $canUploadFiles, $status);
            $result = $resultArray['result'];
            $resultParam = $resultArray['resultparam'];


            // send notifications for all of the items that have been updated
            $itemsArray = $resultArray['items'];
            $itemCount = count($itemsArray);

            for ($i = 0; $i < $itemCount; $i++)
            {
                $theItem = $itemsArray[$i];

                $orderItemID = $theItem['id'];
                $orderID = $theItem['orderid'];
                $siteCode = $theItem['currentowner'];
                $parentOrderItemID = $theItem['parentorderitemid'];

                $sendNotification = true;
                $emailTemplate = '';
                $emailName = '';
                $emailAddress = '';
                $emailNameBCC = '';
                $emailAddressBCC = '';
                $smtpSaveOrderName = '';
                $smtpSaveOrderAddress = '';

                // we only send email notifications if the taopix web shopping cart is being used
                // we only want to send the email notifications for the parent order item and not for any companion albums
                if (($shoppingCartType == TPX_SHOPPINGCARTTYPE_INTERNAL) && ($parentOrderItemID == 0))
                {
                    $jobTicketArray = DatabaseObj::getJobTicket($orderItemID, $gConstants['defaultlanguagecode']);

                    $userId = $jobTicketArray['userid'];
                    $orderNumber = $jobTicketArray['ordernumber'];
                    $shippingCustomerName = $jobTicketArray['shippingcustomername'];
                    $shippingCustomerAddress1 = $jobTicketArray['shippingcustomeraddress1'];
                    $shippingCustomerAddress2 = $jobTicketArray['shippingcustomeraddress2'];
                    $shippingCustomerAddress3 = $jobTicketArray['shippingcustomeraddress3'];
                    $shippingCustomerAddress4 = $jobTicketArray['shippingcustomeraddress4'];
                    $shippingCustomerCity = $jobTicketArray['shippingcustomercity'];
                    $shippingCustomerCounty = $jobTicketArray['shippingcustomercounty'];
                    $shippingCustomerState = $jobTicketArray['shippingcustomerstate'];
                    $shippingCustomerPostCode = $jobTicketArray['shippingcustomerpostcode'];
                    $shippingCustomerCountryCode = $jobTicketArray['shippingcustomercountrycode'];
                    $shippingCustomerTelephoneNumber = $jobTicketArray['shippingcustomertelephonenumber'];
                    $shippingCustomerEmailAddress = $jobTicketArray['shippingcustomeremailaddress'];
                    $shippingContactFirstName = $jobTicketArray['shippingcontactfirstname'];
                    $shippingContactLastName = $jobTicketArray['shippingcontactlastname'];

                    $webBrandArray = AuthenticateObj::getWebBrandData($jobTicketArray['webbrandcode']);
                    $webBrandEmailSettingsArray = DatabaseObj::getBrandingFromCode($webBrandArray['webbrandcode']);

                    $shippingAddress = UtilsAddressObj::formatAddress($jobTicketArray, 'shipping', "\n");
                    $billingAddress = UtilsAddressObj::formatAddress($jobTicketArray, 'billing', "\n");

                    $userAccount = DatabaseObj::getUserAccountFromID($jobTicketArray['userid']);
                    $loginName = $userAccount['login'];

                    if ($theItem['temporder'] == 0)
                    {
                        if ($uploadMethodString == 'UPLOAD')
                        {
                            if (($webBrandArray['webbrandcode'] != '') && ($webBrandEmailSettingsArray['usedefaultemailsettings'] == 0))
                            {
                                if (($webBrandEmailSettingsArray['smtpproductionactive'] == 0) || ($webBrandEmailSettingsArray['smtpproductionname'] == ''))
                                {
                                    $sendNotification = false;
                                }
                            }
                            else
                            {
                                if (($brandingDefaults['smtpproductionactive'] == 0) || ($brandingDefaults['smtpproductionname'] == ''))
                                {
                                    $sendNotification = false;
                                }
                            }

                            if ($sendNotification == true)
                            {
                                // ms check
                                if ($gConstants['optionms'])
                                {
                                    $siteResultArray = DatabaseObj::getSiteFromCode($siteCode);

                                    // if there is a brand check to see if the brand is using its own email settings.
                                    if (($webBrandEmailSettingsArray['usedefaultemailsettings'] == 0) && ($webBrandEmailSettingsArray['isactive'] == 1))
                                    {
                                        // check to see if the brand email settings have a production email name and address to use.
                                        if (($webBrandEmailSettingsArray['smtpproductionname'] != '') && ($webBrandEmailSettingsArray['smtpproductionaddress'] != ''))
                                        {
                                            $emailName .= $webBrandEmailSettingsArray['smtpproductionname'];
                                            $emailAddress .= $webBrandEmailSettingsArray['smtpproductionaddress'];
                                        }

                                        // check to see if the site email settings have a production email name and address to use.
                                        if (($siteResultArray['smtpproductionname'] != '') && ($siteResultArray['smtpproductionaddress'] != ''))
                                        {
                                            // also use the sites production name and address.
                                            if ($emailName != '')
                                            {
                                                $emailName .= ";";
                                                $emailAddress .= ";";
                                            }

                                            $emailName .= $siteResultArray['smtpproductionname'];
                                            $emailAddress .= $siteResultArray['smtpproductionaddress'];
                                        }
                                    }
                                    //just send to site production.
                                    else
                                    {
                                        // if no brand just use the sites production name and email address
                                        $emailName = $siteResultArray['smtpproductionname'];
                                        $emailAddress = $siteResultArray['smtpproductionaddress'];
                                    }

                                    // check to see if the email settings have a production email name and address to use.
                                    if (($emailName == '') || ($emailAddress == ''))
                                    {
                                        // if no production name and email address use the constants production name and email address
                                        $emailName = $brandingDefaults['smtpproductionname'];
                                        $emailAddress = $brandingDefaults['smtpproductionaddress'];
                                    }

                                    $emailTemplate = 'admin_orderuploaded';
                                }
                                else
                                {
                                    // if no ms
                                    // check to see if the brand is using its own email settings
                                    if (($webBrandEmailSettingsArray['usedefaultemailsettings'] == 0) && ($webBrandEmailSettingsArray['isactive'] == 1))
                                    {
                                        $emailTemplate = 'admin_orderuploaded';
                                        $sendNotification = true;

                                        // check to see if the production name and email address is actaully available.
                                        if (($webBrandEmailSettingsArray['smtpproductionname'] != '') && ($webBrandEmailSettingsArray['smtpproductionaddress'] != ''))
                                        {
                                            $emailName = $webBrandEmailSettingsArray['smtpproductionname'];
                                            $emailAddress = $webBrandEmailSettingsArray['smtpproductionaddress'];
                                        }
                                        else
                                        {
                                            // if no production name and email address use the constants production name and email address
                                            $emailName = $brandingDefaults['smtpproductionname'];
                                            $emailAddress = $brandingDefaults['smtpproductionaddress'];
                                        }
                                    }
                                    //just send to global production.
                                    else
                                    {
                                        // if no brand set just use the contants production name and email address
                                        $emailTemplate = 'admin_orderuploaded';
                                        $emailName = $brandingDefaults['smtpproductionname'];
                                        $emailAddress = $brandingDefaults['smtpproductionaddress'];
                                    }
                                }
                            }
                        } // end production email
                        else
                        {
                            if (($webBrandArray['webbrandcode'] != '') && ($webBrandEmailSettingsArray['usedefaultemailsettings'] == 0))
                            {
                                $sendNotification = $webBrandEmailSettingsArray['smtpsaveorderactive'];
                            }
                            else
                            {
                                $sendNotification = $brandingDefaults['smtpsaveorderactive'];
                            }

                            if ($sendNotification == true)
                            {
                                if (($webBrandEmailSettingsArray['usedefaultemailsettings'] == 0) && ($webBrandEmailSettingsArray['isactive'] == 1))
                                {
                                    if (($webBrandEmailSettingsArray['smtpsaveordername'] != '') && ($webBrandEmailSettingsArray['smtpsaveorderaddress'] != ''))
                                    {
                                        $smtpSaveOrderName = $webBrandEmailSettingsArray['smtpsaveordername'];
                                        $smtpSaveOrderAddress = $webBrandEmailSettingsArray['smtpsaveorderaddress'];
                                    }
                                    else
                                    {
                                        $smtpSaveOrderName = $brandingDefaults['smtpsaveordername'];
                                        $smtpSaveOrderAddress = $brandingDefaults['smtpsaveorderaddress'];
                                    }
                                }
                                else
                                {
                                    $smtpSaveOrderName = $brandingDefaults['smtpsaveordername'];
                                    $smtpSaveOrderAddress = $brandingDefaults['smtpsaveorderaddress'];
                                }

                                if ($smtpSaveOrderAddress != '')
                                {
                                    $emailTemplate = 'admin_ordersaved';
                                    $emailName = $smtpSaveOrderName;
                                    $emailAddress = $smtpSaveOrderAddress;
                                    $sendNotification = true;
                                }
                                else
                                {
                                    //if the saveOrderAddress is empty then dont attempt to send email
                                    $sendNotification = false;
                                }
                            }
                        }

                        if ($sendNotification == true)
                        {
                            $emailObj = new TaopixMailer();
                            $emailObj->sendTemplateEmail($emailTemplate, $webBrandArray['webbrandcode'], $webBrandArray['webbrandapplicationname'],
                                            $webBrandArray['webbranddisplayurl'], $gConstants['defaultlanguagecode'], $emailName, $emailAddress, '', '', 0,
                                            Array(
                                                'orderid' => $jobTicketArray['orderid'],
                                                'itemnumber' => $jobTicketArray['itemnumber'],
                                                'orderitemid' => $jobTicketArray['recordid'],
                                                'userid' => $jobTicketArray['userid'],
                                                'loginname' => $loginName,
                                                'currencycode' => $jobTicketArray['currencycode'],
                                                'currencyname' => $jobTicketArray['currencyname'],
                                                'expirydate' => $jobTicketArray['temporderexpirydate'],
                                                'formattedexpirydatetime' => $jobTicketArray['formattedtemporderexpiryatetime'],
                                                'formattedexpirydate' => $jobTicketArray['formattedtemporderexpirydate'],
                                                'formattedexpirytime' => $jobTicketArray['formattedtemporderexpirytime'],
                                                'ordernumber' => $jobTicketArray['ordernumber'],
                                                'qty' => $jobTicketArray['qty'],
                                                'pagecount' => $jobTicketArray['pagecount'],
                                                'productcode' => $jobTicketArray['productcode'],
                                                'productname' => $jobTicketArray['productname'],
                                                'defaultcovercode' => $jobTicketArray['defaultcovercode'],
                                                'defaultpapercode' => $jobTicketArray['defaultpapercode'],
                                                'defaultpagecount' => $jobTicketArray['defaultpagecount'],
                                                'covercode' => $jobTicketArray['covercode'],
                                                'covername' => $jobTicketArray['covername'],
                                                'papercode' => $jobTicketArray['papercode'],
                                                'papername' => $jobTicketArray['papername'],
                                                'vouchercode' => $jobTicketArray['vouchercode'],
                                                'vouchername' => $jobTicketArray['vouchername'],
                                                'ordertotal' => $jobTicketArray['ordertotal'],
                                                'formattedordertotal' => $jobTicketArray['formattedordertotal'],

                                                'shippingcontactname' => $jobTicketArray['shippingcontactfirstname'] . ' ' . $jobTicketArray['shippingcontactlastname'],
                                                'shippingcontactfirstname' => $jobTicketArray['shippingcontactfirstname'],
                                                'shippingcontactlastname' => $jobTicketArray['shippingcontactlastname'],
                                                'shippingaddress' => $shippingAddress,
                                                'shippingmethodname' => $jobTicketArray['shippingmethodname'],
                                                // leave 'shippingmethod' in in order not to break existing templates,
                                                // but really it should be 'shippingmethodname'
                                                'shippingmethod' => $jobTicketArray['shippingmethodname'],

                                                'shippingqty' => $jobTicketArray['shippingqty'],
                                                'shippingcustomername' => $jobTicketArray['shippingcustomername'],
                                                'shippingcustomeraddress1' => $jobTicketArray['shippingcustomeraddress1'],
                                                'shippingcustomeraddress2' => $jobTicketArray['shippingcustomeraddress2'],
                                                'shippingcustomeraddress3' => $jobTicketArray['shippingcustomeraddress3'],
                                                'shippingcustomeraddress4' => $jobTicketArray['shippingcustomeraddress4'],
                                                'shippingcustomercity' => $jobTicketArray['shippingcustomercity'],
                                                'shippingcustomercounty' => $jobTicketArray['shippingcustomercounty'],
                                                'shippingcustomerstate' => $jobTicketArray['shippingcustomerstate'],
                                                'shippingcustomerregioncode' => $jobTicketArray['shippingcustomerregioncode'],
                                                'shippingcustomerregion' => $jobTicketArray['shippingcustomerregion'],
                                                'shippingcustomerpostcode' => $jobTicketArray['shippingcustomerpostcode'],
                                                'shippingcustomercountrycode' => $jobTicketArray['shippingcustomercountrycode'],
                                                'shippingcustomercountryname' => $jobTicketArray['shippingcustomercountryname'],
                                                'shippingcustomertelephonenumber' => $jobTicketArray['shippingcustomertelephonenumber'],
                                                'shippingcustomeremailaddress' => $jobTicketArray['shippingcustomeremailaddress'],
                                                'shippingmethodcode' => $jobTicketArray['shippingmethodcode'],
                                                'shippingratecode' => $jobTicketArray['shippingratecode'],
                                                'shippingrateinfo' => $jobTicketArray['shippingrateinfo'],
                                                'shippingratecost' => $jobTicketArray['shippingratecost'],
                                                'shippingratesell' => $jobTicketArray['shippingratesell'],
                                                'shippingratetaxcode' => $jobTicketArray['shippingratetaxcode'],
                                                'shippingratetaxname' => $jobTicketArray['shippingratetaxname'],
                                                'shippingratetaxrate' => $jobTicketArray['shippingratetaxrate'],
                                                'shippingratecalctax' => $jobTicketArray['shippingratecalctax'],
                                                'shippingratetaxtotal' => $jobTicketArray['shippingratetaxtotal'],

                                                'billingcontactname' => $jobTicketArray['billingcontactfirstname'] . ' ' . $jobTicketArray['billingcontactlastname'],
                                                'billingcontactfirstname' => $jobTicketArray['billingcontactfirstname'],
                                                'billingcontactlastname' => $jobTicketArray['billingcontactlastname'],
                                                'billingaddress' => $billingAddress,

                                                'billingcustomeraccountcode' => $jobTicketArray['billingcustomeraccountcode'],
                                                'billingcustomername' => $jobTicketArray['billingcustomername'],
                                                'billingcustomeraddress1' => $jobTicketArray['billingcustomeraddress1'],
                                                'billingcustomeraddress2' => $jobTicketArray['billingcustomeraddress2'],
                                                'billingcustomeraddress3' => $jobTicketArray['billingcustomeraddress3'],
                                                'billingcustomeraddress4' => $jobTicketArray['billingcustomeraddress4'],
                                                'billingcustomercity' => $jobTicketArray['billingcustomercity'],
                                                'billingcustomercounty' => $jobTicketArray['billingcustomercounty'],
                                                'billingcustomerstate' => $jobTicketArray['billingcustomerstate'],
                                                'billingcustomerregioncode' => $jobTicketArray['billingcustomerregioncode'],
                                                'billingcustomerregion' => $jobTicketArray['billingcustomerregion'],
                                                'billingcustomerpostcode' => $jobTicketArray['billingcustomerpostcode'],
                                                'billingcustomercountrycode' => $jobTicketArray['billingcustomercountrycode'],
                                                'billingcustomercountryname' => $jobTicketArray['billingcustomercountryname'],
                                                'billingcustomertelephonenumber' => $jobTicketArray['billingcustomertelephonenumber'],
                                                'billingcustomeremailaddress' => $jobTicketArray['billingcustomeremailaddress'],
                                                'paymentmethodname' => $jobTicketArray['paymentmethodname'],
												'targetuserid' => $jobTicketArray['userid']),
                                                '', ''
                                            );
                        }

                        // check if this type of notification is enabled first
                        if (($webBrandArray['webbrandcode'] != '') && ($webBrandEmailSettingsArray['usedefaultemailsettings'] == 0))
                        {
                            $sendNotification = $webBrandEmailSettingsArray['smtporderuploadedactive'];
                            $emailNameBCC = $webBrandEmailSettingsArray['smtporderuploadedname'];
                            $emailAddressBCC = $webBrandEmailSettingsArray['smtporderuploadedaddress'];
                        }
                        else
                        {
                            $sendNotification = $brandingDefaults['smtporderuploadedactive'];
                            $emailNameBCC = $brandingDefaults['smtporderuploadedname'];
                            $emailAddressBCC = $brandingDefaults['smtporderuploadedaddress'];
                        }

                        if ($sendNotification && $uploadMethodString == 'UPLOAD')
                        {
                            // get shared url for the item

                            $shareArray = Share_model::share($jobTicketArray['userid'], $orderItemID, 'EMAIL', 'CUSTOMER NOTIFICATION', $userAccount['emailaddress'], '', $webBrandArray['webbranddisplayurl']);
                            $result = $shareArray['result'];
                            $resultParam = $shareArray['resultparam'];
                            $sharedLink = $shareArray['sharedurl'];

                            // check what the order source is so we can send the appropriate email
                            if ($jobTicketArray['source'] == TPX_SOURCE_DESKTOP)
                            {
                                $emailTemplate = 'customer_orderuploaded';
                            }
                            else
                            {
                                $emailTemplate = 'customer_orderuploadedonline';
                            }

                            switch ($jobTicketArray['useremaildestination'])
                            {
                                case 0: // billing address;
                                    $emailName =$jobTicketArray['billingcontactfirstname'] . ' ' .$jobTicketArray['billingcontactlastname'];
                                    $emailAddress =$jobTicketArray['billingcustomeremailaddress'];
                                    break;
                                case 1: // shipping address
                                    $emailName = $jobTicketArray['shippingcontactfirstname'] . ' ' . $jobTicketArray['shippingcontactlastname'];
                                    $emailAddress = $jobTicketArray['shippingcustomeremailaddress'];
                                    break;
                                case 2: // shipping address and bcc to billing address
                                    $emailName = $jobTicketArray['shippingcontactfirstname'] . ' ' . $jobTicketArray['shippingcontactlastname'];
                                    $emailAddress = $jobTicketArray['shippingcustomeremailaddress'];
                                    $emailNameBCC .= ';' . $jobTicketArray['billingcontactfirstname'] . ' ' .$jobTicketArray['billingcontactlastname'];
                                    $emailAddressBCC .= ';' . $jobTicketArray['billingcustomeremailaddress'];
                                    break;
                                case 3: // billing address and bcc to shipping address
                                    $emailName =$jobTicketArray['billingcontactfirstname'] . ' ' .$jobTicketArray['billingcontactlastname'];
                                    $emailAddress =$jobTicketArray['billingcustomeremailaddress'];
                                    $emailNameBCC .= ';' . $jobTicketArray['shippingcontactfirstname'] . ' ' . $jobTicketArray['shippingcontactlastname'];
                                    $emailAddressBCC .= ';' . $jobTicketArray['shippingcustomeremailaddress'];
                                    break;
                            }

                            $emailObj = new TaopixMailer();
                            $emailObj->sendTemplateEmail($emailTemplate, $webBrandArray['webbrandcode'], $webBrandArray['webbrandapplicationname'],
                                $webBrandArray['webbranddisplayurl'], $jobTicketArray['languagecode'], $emailName,
                                $emailAddress, $emailNameBCC, $emailAddressBCC, 0,
                                Array(
                                    'orderid' => $jobTicketArray['orderid'],
                                    'orderitemid' => $jobTicketArray['recordid'],
                                    'userid' => $jobTicketArray['userid'],
                                    'ordernumber' => $jobTicketArray['ordernumber'],
                                    'billingcontactfirstname' => $jobTicketArray['billingcontactfirstname'],
                                    'billingcontactlastname' => $jobTicketArray['billingcontactlastname'],
                                    'sharedurl' => $sharedLink,
                                    'projectname' => $jobTicketArray['projectname'],
                                    'targetuserid' => $jobTicketArray['userid']
                                ),
                                '', ''
                            );
                        }
                    }
                    else
                    {
                        // temporary order upload
                        if ($jobTicketArray['source'] == TPX_SOURCE_DESKTOP)
                        {
                            $emailTemplate = 'admin_paylaterorderuploaded';
                        }
                        else
                        {
                            $emailTemplate = 'admin_paylaterorderuploadedonline';
                        }

                        if (($webBrandArray['webbrandcode'] != '') && ($webBrandEmailSettingsArray['usedefaultemailsettings'] == 0))
                        {
                            if (($webBrandEmailSettingsArray['smtpproductionactive'] == 0) || ($webBrandEmailSettingsArray['smtpproductionname'] == ''))
                            {
                                $sendNotification = false;
                            }
                        }
                        else
                        {
                            if (($brandingDefaults['smtpproductionactive'] == 0) || ($brandingDefaults['smtpproductionname'] == ''))
                            {
                                $sendNotification = false;
                            }
                        }

                        if ($sendNotification == true)
                        {
                            if (($webBrandEmailSettingsArray['usedefaultemailsettings'] == 0) && ($webBrandEmailSettingsArray['isactive'] == 1))
                            {
                                $emailName = $webBrandEmailSettingsArray['smtpproductionname'];
                                $emailAddress = $webBrandEmailSettingsArray['smtpproductionaddress'];
                            }
                            else
                            {
                                $emailName = $brandingDefaults['smtpproductionname'];
                                $emailAddress = $brandingDefaults['smtpproductionaddress'];
                            }
                        }

                        if ($sendNotification == true)
                        {
                            $emailObj = new TaopixMailer();
                            $emailObj->sendTemplateEmail($emailTemplate, $webBrandArray['webbrandcode'], $webBrandArray['webbrandapplicationname'],
                                $webBrandArray['webbranddisplayurl'], $gConstants['defaultlanguagecode'], $emailName, $emailAddress, '', '', 0,
                                    Array(
                                        'orderid' => $jobTicketArray['orderid'],
                                        'orderitemid' => $jobTicketArray['recordid'],
                                        'userid' => $jobTicketArray['userid'],
                                        'loginname' => $loginName,
                                        'currencycode' => $jobTicketArray['currencycode'],
                                        'currencyname' => $jobTicketArray['currencyname'],
                                        'expirydate' => $jobTicketArray['temporderexpirydate'],
                                        'formattedexpirydatetime' => $jobTicketArray['formattedtemporderexpiryatetime'],
                                        'formattedexpirydate' => $jobTicketArray['formattedtemporderexpirydate'],
                                        'formattedexpirytime' => $jobTicketArray['formattedtemporderexpirytime'],
                                        'ordernumber' => $jobTicketArray['ordernumber'],
                                        'qty' => $jobTicketArray['qty'],
                                        'pagecount' => $jobTicketArray['pagecount'],
                                        'productcode' => $jobTicketArray['productcode'],
                                        'productname' => $jobTicketArray['productname'],
                                        'defaultcovercode' => $jobTicketArray['defaultcovercode'],
                                        'defaultpapercode' => $jobTicketArray['defaultpapercode'],
                                        'defaultpagecount' => $jobTicketArray['defaultpagecount'],
                                        'covercode' => $jobTicketArray['covercode'],
                                        'covername' => $jobTicketArray['covername'],
                                        'papercode' => $jobTicketArray['papercode'],
                                        'papername' => $jobTicketArray['papername'],
                                        'vouchercode' => $jobTicketArray['vouchercode'],
                                        'vouchername' => $jobTicketArray['vouchername'],
                                        'ordertotal' => $jobTicketArray['ordertotal'],
                                        'formattedordertotal' => $jobTicketArray['formattedordertotal'],

                                        'shippingcontactname' => $jobTicketArray['shippingcontactfirstname'] . ' ' . $jobTicketArray['shippingcontactlastname'],
                                        'shippingcontactfirstname' => $jobTicketArray['shippingcontactfirstname'],
                                        'shippingcontactlastname' => $jobTicketArray['shippingcontactlastname'],
                                        'shippingaddress' => $shippingAddress,
                                        'shippingmethodname' => $jobTicketArray['shippingmethodname'],
                                        // leave 'shippingmethod' in in order not to break existing templates,
                                        // but really it should be 'shippingmethodname'
                                        'shippingmethod' => $jobTicketArray['shippingmethodname'],

                                        'shippingqty' => $jobTicketArray['shippingqty'],
                                        'shippingcustomername' => $jobTicketArray['shippingcustomername'],
                                        'shippingcustomeraddress1' => $jobTicketArray['shippingcustomeraddress1'],
                                        'shippingcustomeraddress2' => $jobTicketArray['shippingcustomeraddress2'],
                                        'shippingcustomeraddress3' => $jobTicketArray['shippingcustomeraddress3'],
                                        'shippingcustomeraddress4' => $jobTicketArray['shippingcustomeraddress4'],
                                        'shippingcustomercity' => $jobTicketArray['shippingcustomercity'],
                                        'shippingcustomercounty' => $jobTicketArray['shippingcustomercounty'],
                                        'shippingcustomerstate' => $jobTicketArray['shippingcustomerstate'],
                                        'shippingcustomerregioncode' => $jobTicketArray['shippingcustomerregioncode'],
                                        'shippingcustomerregion' => $jobTicketArray['shippingcustomerregion'],
                                        'shippingcustomerpostcode' => $jobTicketArray['shippingcustomerpostcode'],
                                        'shippingcustomercountrycode' => $jobTicketArray['shippingcustomercountrycode'],
                                        'shippingcustomercountryname' => $jobTicketArray['shippingcustomercountryname'],
                                        'shippingcustomertelephonenumber' => $jobTicketArray['shippingcustomertelephonenumber'],
                                        'shippingcustomeremailaddress' => $jobTicketArray['shippingcustomeremailaddress'],
                                        'shippingmethodcode' => $jobTicketArray['shippingmethodcode'],
                                        'shippingratecode' => $jobTicketArray['shippingratecode'],
                                        'shippingrateinfo' => $jobTicketArray['shippingrateinfo'],
                                        'shippingratecost' => $jobTicketArray['shippingratecost'],
                                        'shippingratesell' => $jobTicketArray['shippingratesell'],
                                        'shippingratetaxcode' => $jobTicketArray['shippingratetaxcode'],
                                        'shippingratetaxname' => $jobTicketArray['shippingratetaxname'],
                                        'shippingratetaxrate' => $jobTicketArray['shippingratetaxrate'],
                                        'shippingratecalctax' => $jobTicketArray['shippingratecalctax'],
                                        'shippingratetaxtotal' => $jobTicketArray['shippingratetaxtotal'],

                                        'billingcontactname' => $jobTicketArray['billingcontactfirstname'] . ' ' . $jobTicketArray['billingcontactlastname'],
                                        'billingcontactfirstname' => $jobTicketArray['billingcontactfirstname'],
                                        'billingcontactlastname' => $jobTicketArray['billingcontactlastname'],
                                        'billingaddress' => $billingAddress,

                                        'billingcustomeraccountcode' => $jobTicketArray['billingcustomeraccountcode'],
                                        'billingcustomername' => $jobTicketArray['billingcustomername'],
                                        'billingcustomeraddress1' => $jobTicketArray['billingcustomeraddress1'],
                                        'billingcustomeraddress2' => $jobTicketArray['billingcustomeraddress2'],
                                        'billingcustomeraddress3' => $jobTicketArray['billingcustomeraddress3'],
                                        'billingcustomeraddress4' => $jobTicketArray['billingcustomeraddress4'],
                                        'billingcustomercity' => $jobTicketArray['billingcustomercity'],
                                        'billingcustomercounty' => $jobTicketArray['billingcustomercounty'],
                                        'billingcustomerstate' => $jobTicketArray['billingcustomerstate'],
                                        'billingcustomerregioncode' => $jobTicketArray['billingcustomerregioncode'],
                                        'billingcustomerregion' => $jobTicketArray['billingcustomerregion'],
                                        'billingcustomerpostcode' => $jobTicketArray['billingcustomerpostcode'],
                                        'billingcustomercountrycode' => $jobTicketArray['billingcustomercountrycode'],
                                        'billingcustomercountryname' => $jobTicketArray['billingcustomercountryname'],
                                        'billingcustomertelephonenumber' => $jobTicketArray['billingcustomertelephonenumber'],
                                        'billingcustomeremailaddress' => $jobTicketArray['billingcustomeremailaddress'],
                                        'paymentmethodname' => $jobTicketArray['paymentmethodname'],
										'targetuserid' => $jobTicketArray['userid']),
                                        '', ''
                                    );
                        }
                    }
                }

                if ($uploadMethod == TPX_UPLOAD_DELIVERY_METHOD_INTERNET)
                {
                    // order upload complete data export event trigger
                    DataExportObj::EventTrigger(TPX_TRIGGER_ORDER_UPLOAD_COMPLETE, 'ORDERITEM', $orderItemID, $orderID);
                }
                elseif ($uploadMethod == TPX_UPLOAD_DELIVERY_METHOD_MAIL)
                {
                    // order saved to disk data export event trigger
                    DataExportObj::EventTrigger(TPX_TRIGGER_ORDER_SAVED_TO_DISK, 'ORDERITEM', $orderItemID, $orderID);
                }
            }
        }

        $resultArray['result'] = $result;
        $resultArray['resultparam'] = $resultParam;
        $resultArray['apiversion'] = $apiVersion;
        $resultArray['uploadmethod'] = $uploadMethodString;
        $resultArray['languageCode'] = $languageCode;
        $resultArray['userid'] = $userId;
        $resultArray['ordernumber'] = $orderNumber;
        $resultArray['shippingcustomername'] = $shippingCustomerName;
        $resultArray['shippingcustomeraddress1'] = $shippingCustomerAddress1;
        $resultArray['shippingcustomeraddress2'] = $shippingCustomerAddress2;
        $resultArray['shippingcustomeraddress3'] = $shippingCustomerAddress3;
        $resultArray['shippingcustomeraddress4'] = $shippingCustomerAddress4;
        $resultArray['shippingcustomercity'] = $shippingCustomerCity;
        $resultArray['shippingcustomercounty'] = $shippingCustomerCounty;
        $resultArray['shippingcustomerstate'] = $shippingCustomerState;
        $resultArray['shippingcustomerpostcode'] = $shippingCustomerPostCode;
        $resultArray['shippingcustomercountrycode'] = $shippingCustomerCountryCode;
        $resultArray['shippingcustomertelephonenumber'] = $shippingCustomerTelephoneNumber;
        $resultArray['shippingcustomeremailaddress'] = $shippingCustomerEmailAddress;
        $resultArray['shippingcontactfirstname'] = $shippingContactFirstName;
        $resultArray['shippingcontactlastname'] = $shippingContactLastName;

        return $resultArray;
    }

    /**
    * Processes the authentication request for the real license key when the designer has an internet license key
    *
    * @static
    *
    * @return array
    *   the result array will contain the response to be echo'd back to the calling application
    *
    * @author Kevin Gale
    * @since Version 1.0.0
    */
    static function getLicenseKey()
    {
        global $gConstants;

        $result = '';
        $resultParam = '';
        $licenseKeyFileName = '';
        $licenseKeyFileSize = 0;
        $licenseKeyFileChecksum = '';
        $licenseKeyData = '';
        $paramCount = 0;
        $authenticationMode = TPX_AUTHENTICATIONMODE_LOGINPASS;

        $apiVersion = (int)UtilsObj::getPOSTParam('version', '1');
        $appVersion = UtilsObj::getPOSTParam('appversion', '');
        $languageCode = UtilsObj::getPOSTParam('langcode', 'en');

        // if the apiversion is 3 or higher then this is taopix designer version 3 and later
        // the parameters are passed as an encrypted string that we must decode
        if ($apiVersion >= 3)
        {
            $commandDataArray = self::decodeDesignerCommandParams();

            // login
            $login = UtilsObj::getArrayParam($commandDataArray, 'login');

            // password
            $password = UtilsObj::getArrayParam($commandDataArray, 'password');

            // parameter count
            $paramCount = (int) UtilsObj::getArrayParam($commandDataArray, 'paramcount');

            $authenticationMode = (int) UtilsObj::getArrayParam($commandDataArray, 'mode', $authenticationMode);
        }
        else
        {
            // login
            $login = UtilsObj::getPOSTParam('login');

            // password
            $password = UtilsObj::getPOSTParam('password');

            $paramCount = 2;
        }

        if ($paramCount > 0)
        {
            if ($paramCount == 1)
            {
                $licenseKeyArray = DatabaseObj::getLicenseKeyFromLogin($login);
            }
            else
            {
                $licenseKeyArray = DatabaseObj::getLicenseKeyFromLoginAndPassword($login, $password);
            }


            // determine the license key to download via the external script
            if ($gConstants['optionwscrp'])
            {
                if (file_exists("../Customise/scripts/EDL_GetLicenseKey.php"))
                {
                    require_once('../Customise/scripts/EDL_GetLicenseKey.php');

                    if (method_exists('GetLicenseKeyObj', 'getLicenseKey'))
                    {
                        $paramArray = Array();
                        $paramArray['apiversion'] = $apiVersion;
                        $paramArray['groupcode'] = $licenseKeyArray['groupcode'];
                        $paramArray['authmode'] = $authenticationMode;
                        $paramArray['authparam1'] = $login;
                        $paramArray['authparam2'] = $password;

                        $externalResponse = GetLicenseKeyObj::getLicenseKey($paramArray);

                        if ($externalResponse['groupcode'] == '')
                        {
                            $licenseKeyArray['result'] = 'str_ErrorNoAccount';
                        }
                        else
                        {
                            $licenseKeyData = $externalResponse['groupdata'];

                            // if we have been supplied with a different license key code find it in the database
                            if ($externalResponse['groupcode'] != $licenseKeyArray['groupcode'])
                            {
                                $licenseKeyArray = DatabaseObj::getLicenseKeyFromCode($externalResponse['groupcode']);
                            }
                        }
                    }
                }
            }


            // retrieve the result
            $result = $licenseKeyArray['result'];
            $resultParam = $licenseKeyArray['resultparam'];

            // if no error has occurred we have found a license key with the correct details
            if ($result == '')
            {
                if ($licenseKeyArray['isactive'] == 1)
                {
                    $licenseKeyFileName = $licenseKeyArray['keyfilename'];
                    $licenseKeyFileSize = $licenseKeyArray['keyfilesize'];
                    $licenseKeyFileChecksum = $licenseKeyArray['keyfilechecksum'];
                }
                else
                {
                   $result = 'INVALIDLICENSEKEYLOGIN';
                }
            }
            else
            {
                $result = 'INVALIDLICENSEKEYLOGIN';
            }
        }
        else
        {
            $result = 'INVALIDLICENSEKEYLOGIN';
        }

        $resultArray['result'] = $result;
        $resultArray['resultparam'] = $resultParam;
        $resultArray['apiversion'] = $apiVersion;
        $resultArray['keyfilename'] = $licenseKeyFileName;
        $resultArray['keyfilesize'] = $licenseKeyFileSize;
        $resultArray['keyfilechecksum'] = $licenseKeyFileChecksum;
        $resultArray['keydata'] = $licenseKeyData;
        $resultArray['languageCode'] = $languageCode;
        $resultArray['authenticationMode'] = $authenticationMode;

        return $resultArray;
    }

    /**
    * Generates the auto-update data for TAOPIX™ Designer or the system data for TAOPIX™ Builder
    *
    * @static
    *
    * @return array
    *   the result array will contain the data to be echo'd back to the calling application
    *
    * @author Kevin Gale
    * @since Version 1.0.0
    */
	static function systemUpdateProcess($pOwnerCode, $pOwnerCode2, $pProductCollectionCode, $pGroupCode, $pWebBrandCode, $pAppVersion, $pOSVersion,
    					$pCPUType, $pAPIVersion, $pLanguageCode, $pCurrentProductCategoryVersion, $pCurrentCalendarDataVersion, $pUploadRefData,
    					$pPreviousProductAutoUpdateCacheVersion, $pPreviousMasksAutoUpdateCacheVersion, $pPreviousBackgroundsAutoUpdateCacheVersion,
    					$pPreviousScrapbookAutoUpdateCacheVersion,$pPreviousFramesAutoUpdateCacheVersion, $pAppPlatform)
    {
        global $gConstants;

        $resultArray = Array();
        $result = '';
        $resultParam = '';
        $webBrandCode = $pWebBrandCode;
        $companyCode = '';
        $currencyCode = '';
        $currencyName = '';
        $currencyISONumber = '';
        $currencySymbol = '';
        $currencySymbolAtFront = 1;
        $currencyDecimalPlaces = 2;
        $currencyExchangeRate = 1;
        $productCategoryVersion = '';
        $productCategoryData = '';
        $calendarDataVersion = '';
        $calendarData = '';
        $uploadRefArray = Array();
        $taxZoneDataArray = Array();
        $licenseKeyTaxCode = '';
        $showPricesWithTax = 0;
        $applicationBuildArray = Array();
        $masksArray = Array();
        $backgroundsArray = Array();
        $scrapbookPictureArray = Array();
        $framesArray = Array();
        $webBrandsArray = Array();
        $cacheUpToDate = false;
        $productCacheVersion = '';
        $auCacheVersionMasks = '';
        $auCacheVersionBackgrounds = '';
        $auCacheVersionScrapbook = '';
        $auCacheVersionFrames = '';

        $includeGlobalProducts = false;

        // get the license key data and determine if we allowed to return it
        $licenseKeyArray = DatabaseObj::getAutoUpdateLicenseKeyList($pGroupCode);

        $canReturnLicenseKeyList = false;
        if (($pGroupCode != '**ALL**') && ($pProductCollectionCode == ''))
        {
        	// the request has been invoked by the desktop designer

        	// we need to work around a limitation of the desktop designer license key autoupdate process
        	// designers older than 2017r2 revert the license key structure back to the one present in that version of the software
        	// this results in the loss of data if the key was created in a newer version (eg: ui customisations)
        	// to prevent this we remove license keys from the result until the user has upgraded their software
        	$compareAppVersionResult = UtilsObj::compareApplicationVersions($pAppVersion, '2017.2.0.19');
        	if (($compareAppVersionResult == '=') || ($compareAppVersionResult == '>'))
        	{
        		// the application is a compatible version so we can return the license key data
        		$canReturnLicenseKeyList = true;
        	}
        }
        else
        {
        	// the request was invoked by taopix creator or the online designer so we can return the license key data
        	$canReturnLicenseKeyList = true;
        }

        if ($canReturnLicenseKeyList)
        {
        	// we are allowed to return the list of license keys
        	$resultArray['licensekeys'] = $licenseKeyArray;
        }
        else
        {
        	// we aren't allowed to return the list of license keys

        	// set the structure for an empty list
        	$resultArray['licensekeys'] = array('licensekeylist' => array());
        }


        // perform the initial processing of the auto-update request
        if ($pGroupCode != '**ALL**')
        {
            // we are getting the system update data for a taopix designer license key

            // obtain the license key and set the company initially to the license key company
            $licenseKeyFromCode = DatabaseObj::getLicenseKeyFromCode($pGroupCode);
            $showPricesWithTax = $licenseKeyFromCode['showpriceswithtax'];
            $licenseKeyTaxCode = $licenseKeyFromCode['taxcode'];

            $companyCode = $licenseKeyFromCode['companyCode'];

            // if the license key is linked to an active brand we can obtain the settings from the brand
            $webBrandCode = $licenseKeyFromCode['webbrandcode'];
            $webBrandArray = DatabaseObj::getBrandingFromCode($webBrandCode);
            if ($webBrandCode != '')
            {
                $result = $webBrandArray['result'];
                $resultParam = $webBrandArray['resultparam'];
                if ($result == '')
                {
                    if ($webBrandArray['isactive'] == 0)
                    {
                        // if the brand is inactive use the non-branded data
                        $webBrandCode = '';
                        $webBrandArray = DatabaseObj::getBrandingFromCode($webBrandCode);
                    }
                    else
                    {
                        // if the brand is active and is linked to a production site then use its company
                        if ($webBrandArray['owner'] != '')
                        {
                            $companyCode = $webBrandArray['companycode'];
                        }
                    }
                }
                else
                {
                    // we could not obtain the brand data so use the non-branded data
                    $webBrandCode = '';
                    $webBrandArray = DatabaseObj::getBrandingFromCode($webBrandCode);
                }
            }

            // remember the cache versions
            $auCacheVersionMasks = $webBrandArray['aucacheversionmasks'];
            $auCacheVersionBackgrounds = $webBrandArray['aucacheversionbackgrounds'];
            $auCacheVersionScrapbook = $webBrandArray['aucacheversionscrapbook'];
            $auCacheVersionFrames = $webBrandArray['aucacheversionframes'];


            // get the currency for the license key
            if ($licenseKeyFromCode['usedefaultcurrency'] == 1)
            {
                $currencyArray = DatabaseObj::getCurrency($gConstants['defaultcurrencycode']);
                $currencyArray['exchangerate'] = 1; // we are using the default currency so set the exchange rate to 1
            }
            else
            {
                $currencyArray = DatabaseObj::getCurrency($licenseKeyFromCode['currencycode']);
            }

            // get the tax zone data for the company and the country / region within the license key
            $taxZoneDataArray = DatabaseObj::getTaxZoneDataFromRegion($companyCode, $licenseKeyFromCode['countrycode'], $licenseKeyFromCode['regioncode']);

            $currencyCode = $currencyArray['code'];
            $currencyName = $currencyArray['name'];
            $currencyISONumber = $currencyArray['isonumber'];
            $currencySymbol = $currencyArray['symbol'];
            $currencySymbolAtFront = $currencyArray['symbolatfront'];
            $currencyDecimalPlaces = $currencyArray['decimalplaces'];
            $currencyExchangeRate = $currencyArray['exchangerate'];

            // we must always include global products
            $includeGlobalProducts = true;
        }
        else
        {
            // we are getting the system update data for taopix creator

            if ($pAPIVersion >= 9)
            {
				$systemConfigArray = DatabaseObj::getSystemConfig();

				// if we have been supplied with an owner company use it to filter the data returned by business unit
				if (array_key_exists('ownercompany', $_POST))
				{
					$companyCode = $_POST['ownercompany'];
				}

				// check that the owner details match
				if (($pOwnerCode != $systemConfigArray['ownercode']) || ($pOwnerCode2 != $systemConfigArray['ownercode2']))
				{
					$result = 'str_LicenseKeyNotActive';
				}

				// check that access is allowed from the source ip address
				if ($result == '')
				{
					$isIPAllowed = DatabaseObj::isUserIPAllowed($_SERVER['REMOTE_ADDR'], '', 0, $companyCode);
					$result = $isIPAllowed['result'];
				}

				if ($result == '')
				{
					// now determine which products to obtain
					if ($companyCode == '')
					{
						// we do not have a company code so return all products
						$companyCode = '**ALL**';
						$includeGlobalProducts = true;
					}
					else
					{
						// we are requesting products for a company so do not include global products
						$includeGlobalProducts = false;
					}

					// get the default tax zone data
					$taxZoneDataArray = DatabaseObj::getTaxZoneDataFromRegion('', '', '');

					// get the web brand details
					$webBrandArray = DatabaseObj::getBrandingFromCode($pWebBrandCode);
				}
            }
            else
            {
            	// invalid creator version
            	$result = 'str_CreatorVersionMismatch';
            }
        }

        // if we have no error get the product list
        if ($result == '')
        {
            $buildProducts = true;
            $canLock = false;
            $isLocked = false;
            $cacheData = Array();

            if ($pGroupCode != '**ALL**')
            {
                // we are retrieving the product list for the designer
                $cacheKey = $pGroupCode . '.' . $companyCode . '.' . $pProductCollectionCode;
                $mutexName = 'taopix_auprd_mutex_' . $cacheKey;

                // before retrieving the data check to see if the cache is already being re-built by another process
                // this is done without creating a mutex so we don't run into any concurrency issues
                $mutexResultArray = DatabaseObj::waitForNoDBMutex($mutexName, 60);

                // get the license key data again as the cache version may have changed while waiting for the mutex
                $licenseKeyFromCode = DatabaseObj::getLicenseKeyFromCode($pGroupCode);
                $productCacheVersion = $licenseKeyFromCode['cacheversion'];

                if ($mutexResultArray['result'] == true)
                {
                    // the mutex was available so no other process was building the cache

                    // get the cache data
                    $cacheDataResult = DatabaseObj::getCacheData($cacheKey, $pPreviousProductAutoUpdateCacheVersion);
                    $cacheData = $cacheDataResult['cachedata'];

					/*
					 * Check if the cacheversion in both the cachedata and license key data match.
					 * If they do and the version cacheversionmatch flag is set to true (We have requested cache data we already have)
					 * Or cacheData is not false (Error gzuncompressing, or unserializing) and is not emty we will not need to rebuild the product list.
					 */
					if (($cacheDataResult['cacheversion'] == $licenseKeyFromCode['cacheversion'])
							&& (($cacheDataResult['cacheversionmatch']) || (($cacheData !== false) && (count($cacheData) > 0))))
                    {
                        // the cache is valid so there is no need to build the data
                        $buildProducts = false;

                        // if the cache versions match we can just return an empty products list
                        if ($cacheDataResult['cacheversionmatch'])
                        {
                            $cacheData['productlist'] = Array();

                            $cacheUpToDate = true;
                        }
                    }
                    else
                    {
                        // the cache is not valid so we need to re-build it exclusively
                        $canLock = true;
                    }
                }
                else
                {
                    // the mutex is not available so other processes must be building the cache
                    // in this situation we just build the data and return it
                    $canLock = false;
                }
            }


            // start the product data building process
            if (($buildProducts == true) && ($canLock == true))
            {
                // acquire a mutex as we want to perform this process exclusively
                $mutexResultArray = DatabaseObj::acquireDBMutex($mutexName);

                if ($mutexResultArray['result'] == true)
                {
                    // we have acquired the mutex
                    $isLocked = true;

                    // get the license key data again as the cache version may have changed while aquiring the mutex
                    $licenseKeyFromCode = DatabaseObj::getLicenseKeyFromCode($pGroupCode);

                    $productCacheVersion = $licenseKeyFromCode['cacheversion'];

                    // if the cache version has changed while aquiring the mutex another process may have built the data for us
                    $cacheDataResult = DatabaseObj::getCacheData($cacheKey, '');
                    $cacheData = $cacheDataResult['cachedata'];

                    /*
                     * If the cacheversion matches in the retrived cache and license key, there is no error and there is data
                     * we do not need to build the cache data or product list again.
                     */
                    if (($cacheDataResult['cacheversion'] == $productCacheVersion) && ($cacheData !== false) && (count($cacheData) > 0))
                    {
                        // the cache is valid so there is no need to build the products
                        $isLocked = false;
                        $buildProducts = false;

                        // release the mutex
                        DatabaseObj::releaseDBMutex($mutexName);
                    }
                }
                else
                {
                    // we could not create the mutex so just build the data and return it
                    $canLock = false;
                }
            }


            // build the product data or use the cached version
            if ($buildProducts == true)
            {
                $productArray = DatabaseObj::getAutoUpdateProductList($pProductCollectionCode, $pGroupCode, $companyCode, $taxZoneDataArray,
                                                        $includeGlobalProducts, $licenseKeyTaxCode);

                $result = $productArray['result'];
                $resultParam = $productArray['resultparam'];
                $productArray['productlist'] = self::unsetUnusedProductKeysForCache($productArray['productlist']);
                $resultArray['products'] = &$productArray;


                // if we have been performing the process exclusively we update the cache and then release the mutex
                if ($isLocked == true)
                {
                    DatabaseObj::insertCacheData($cacheKey, $pGroupCode, $companyCode, $resultArray['products'], $licenseKeyFromCode['cacheversion']);

                    DatabaseObj::releaseDBMutex($mutexName);
                }
            }
            else
            {
                $resultArray['products'] = $cacheData;
            }
        }


        // if we have no error and we are not processing the data for a specific product collection retrieve the auto-update data
        if ($result == '')
        {
            if ($pProductCollectionCode == '')
            {
                // get the product collections list if the cache is not up-to-date
                if (! $cacheUpToDate)
                {
                    $productCollectionArray = DatabaseObj::getAutoUpdateProductCollectionList($pGroupCode, $companyCode, $includeGlobalProducts);
                    $result = $productCollectionArray['result'];

                    $resultParam = $productCollectionArray['resultparam'];
                    $resultArray['productcollections'] = $productCollectionArray;
                }
                else
                {
                    $resultArray['productcollections'] = Array();
                    $resultArray['productcollections']['productlist'] = Array();
                }


                // if we have no error, the appapiversion is 3 or higher and we have been provided with a product category version attempt to retrieve the data
                if (($result == '') && ($pAPIVersion >= 3) && ($pCurrentProductCategoryVersion != ''))
                {
                    if ($webBrandArray['productcategoryassetid'] > 0)
                    {
                        $assetDataArray = DatabaseObj::getAssetFromID($webBrandArray['productcategoryassetid']);
                        if ($assetDataArray['result'] == '')
                        {
                            $productCategoryVersion = $webBrandArray['productcategoryassetversion'];

                            $currentCategoryVersionDate = strtotime($pCurrentProductCategoryVersion);
                            $newCategoryVersionDate = strtotime($productCategoryVersion);

                            if ($newCategoryVersionDate > $currentCategoryVersionDate)
                            {
                                // if this isn't a system update we want the asset data
                                if ($pGroupCode != '**ALL**')
                                {
                                    $productCategoryData = $assetDataArray['data'];
                                }
                            }
                        }
                    }
                }


                // if we have no error, the appapiversion is 8 or higher and we have been provided with a calendar data version attempt to retrieve the data
                if (($result == '') && ($pAPIVersion >= 8) && ($pCurrentCalendarDataVersion != ''))
                {
                    if ($webBrandArray['calendardataassetid'] > 0)
                    {
                        $assetDataArray = DatabaseObj::getAssetFromID($webBrandArray['calendardataassetid']);
                        if ($assetDataArray['result'] == '')
                        {
                            $calendarDataVersion = $webBrandArray['calendardataassetversion'];

                            $currentCalendarDataVersionDate = strtotime($pCurrentCalendarDataVersion);
                            $newCalendarDataVersionDate = strtotime($calendarDataVersion);

                            if ($newCalendarDataVersionDate > $currentCalendarDataVersionDate)
                            {
                                // if this isn't a system update we want the asset data
                                if ($pGroupCode != '**ALL**')
                                {
                                    $calendarData = $assetDataArray['data'];
                                }
                            }
                        }
                    }
                }


                // if we have no error get the application list
                if ($result == '')
                {
                    $applicationBuildArray = DatabaseObj::getAutoUpdateApplicationBuildDetails($webBrandCode, $pGroupCode, $pOSVersion, $pCPUType);
                    $result = $applicationBuildArray['result'];
                    $resultParam = $applicationBuildArray['resultparam'];
                }


                // if we have no error and this is a license key update compatible designer check license key version compatability
                if ($result == '')
                {
                    if (($pGroupCode != '**ALL**') && (count($licenseKeyArray['licensekeylist']) > 0))
                    {
                        // license keys with a data version of 2 have a data structure incompatible with desktop designers of versions prior to 2023.1.0.41
                        // thus we need to check compatability in the requesting designer and the available designer update package
                        if ($licenseKeyArray['licensekeylist'][0]['keyfiledataversion'] > 1)
                        {
                            $isLicenseKeyCompatible = false;

                            // check the requesting designer version
                            $compareAppVersionResult = UtilsObj::compareApplicationVersions($pAppVersion, '2023.1.0.41');
                            if (($compareAppVersionResult == '>') || ($compareAppVersionResult == '='))
                            {
                                $isLicenseKeyCompatible = true;
                            }
                            else
                            {
                                // check if an available updated application is compatible as license key and application updates cannot be done separately
                                if (strpos($pAppPlatform, 'Win32') !== false)
                                {
                                    $compareAppVersionResult = UtilsObj::compareApplicationVersions($applicationBuildArray['win32version'], '2023.1.0.41');
                                    if (($compareAppVersionResult == '=') || ($compareAppVersionResult == '>'))
                                    {
                                        $isLicenseKeyCompatible = true;
                                    }
                                }
                                elseif (strpos($pAppPlatform, 'Mac') !== false)
                                {
                                    $compareAppVersionResult = UtilsObj::compareApplicationVersions($applicationBuildArray['macversion'], '2023.1.0.41');
                                    if (($compareAppVersionResult == '=') || ($compareAppVersionResult == '>'))
                                    {
                                        $isLicenseKeyCompatible = true;
                                    }
                                }
                            }

                            // if our license key isn't compatible then we clear it out as we cannot allow the user to upgrade to it
                            if ($isLicenseKeyCompatible === false)
                            {
                                // set the structure for an empty list
        	                    $resultArray['licensekeys'] = array('licensekeylist' => array());
                            }
                        }
                    }
                }


                // if we have no error get the masks list
                if ($result == '')
                {
                    // if this is the creator or the mask cache versions do not match build the data
                    if (($pGroupCode == '**ALL**') || ($pPreviousMasksAutoUpdateCacheVersion == '') ||
                        ($auCacheVersionMasks != $pPreviousMasksAutoUpdateCacheVersion))
                    {
                        $masksArray = DatabaseObj::getAutoUpdateApplicationFilesList($pGroupCode, $webBrandCode, TPX_APPLICATION_FILE_TYPE_MASK, $pAPIVersion);
                        $result = $masksArray['result'];
                        $resultParam = $masksArray['resultparam'];
                    }
                    else
                    {
                        $masksArray['filelist'] = Array();
                    }
                }


                // if we have no error get the backgrounds list
                if ($result == '')
                {
                    // if this is the creator or the backgrounds cache versions do not match build the data
                    if (($pGroupCode == '**ALL**') || ($pPreviousBackgroundsAutoUpdateCacheVersion == '') ||
                        ($auCacheVersionBackgrounds != $pPreviousBackgroundsAutoUpdateCacheVersion))
                    {
                        $backgroundsArray = DatabaseObj::getAutoUpdateApplicationFilesList($pGroupCode, $webBrandCode, TPX_APPLICATION_FILE_TYPE_BACKGROUND, $pAPIVersion);
                        $result = $backgroundsArray['result'];
                        $resultParam = $backgroundsArray['resultparam'];
                    }
                    else
                    {
                        $backgroundsArray['filelist'] = Array();
                    }
                }


                // if we have no error get the scrapbook pictures list
                if ($result == '')
                {
                    // if this is the creator or the scrapbook cache versions do not match build the data
                    if (($pGroupCode == '**ALL**') || ($pPreviousScrapbookAutoUpdateCacheVersion == '') ||
                        ($auCacheVersionScrapbook != $pPreviousScrapbookAutoUpdateCacheVersion))
                    {
                        $scrapbookPictureArray = DatabaseObj::getAutoUpdateApplicationFilesList($pGroupCode, $webBrandCode, TPX_APPLICATION_FILE_TYPE_PICTURE, $pAPIVersion);
                        $result = $scrapbookPictureArray['result'];
                        $resultParam = $scrapbookPictureArray['resultparam'];
                    }
                    else
                    {
                        $scrapbookPictureArray['filelist'] = Array();
                    }
                }


                // if version number is greater than 1 and there is no error get the frames list
                if ($pAPIVersion != 1)
                {
                    if ($result == '')
                    {
                        // if this is the creator or the frame cache versions do not match build the data
                        if (($pGroupCode == '**ALL**') || ($pPreviousFramesAutoUpdateCacheVersion == '') ||
                            ($auCacheVersionFrames != $pPreviousFramesAutoUpdateCacheVersion))
                        {
                            $framesArray = DatabaseObj::getAutoUpdateApplicationFilesList($pGroupCode, $webBrandCode, TPX_APPLICATION_FILE_TYPE_FRAME, $pAPIVersion);
                            $result = $framesArray['result'];
                            $resultParam = $framesArray['resultparam'];
                        }
                        else
                        {
                            $framesArray['filelist'] = Array();
                        }
                    }
                }
            }
        }


        // if we have no error and we have been provided with a list of upload refs retrieve the latest status
        if ($result == '')
        {
            if ($pUploadRefData != '')
            {
                $uploadRefArray = DatabaseObj::getAutoUpdateUploadRefList($pUploadRefData);
                $uploadRefArray = $uploadRefArray['uploadrefslist'];
            }
        }


        // if we have no error and are processing all of the groups also include the brands list
        if ($result == '')
        {
            if ($pGroupCode == '**ALL**')
            {
                $webBrandsArray = DatabaseObj::getBrandingList();
            }
        }

        $resultArray['result'] = $result;
        $resultArray['resultparam'] = $resultParam;
        $resultArray['apiversion'] = $pAPIVersion;
        $resultArray['appversion'] = $pAppVersion;
        $resultArray['webbrandcode'] = $webBrandCode;
        $resultArray['languageCode'] = $pLanguageCode;
        $resultArray['showpriceswithtax'] = $showPricesWithTax;
        $resultArray['currencycode'] = $currencyCode;
        $resultArray['currencyname'] = $currencyName;
        $resultArray['currencyisonumber'] = $currencyISONumber;
        $resultArray['currencysymbol'] = $currencySymbol;
        $resultArray['currencysymbolatfront'] = $currencySymbolAtFront;
        $resultArray['currencydecimalplaces'] = $currencyDecimalPlaces;
        $resultArray['currencyexchangerate'] = $currencyExchangeRate;
        $resultArray['productcacheversion'] = $productCacheVersion;
        $resultArray['productcategoryversion'] = $productCategoryVersion;
        $resultArray['productcategorydata'] = $productCategoryData;
        $resultArray['calendardataversion'] = $calendarDataVersion;
        $resultArray['calendardata'] = $calendarData;
        $resultArray['uploadrefslist'] = $uploadRefArray;
        $resultArray['serverdatetime'] = DatabaseObj::getServerTimeUTC();
        $resultArray['applicationbuild'] = $applicationBuildArray;
        $resultArray['masks'] = $masksArray;
        $resultArray['backgrounds'] = $backgroundsArray;
        $resultArray['scrapbookpictures'] = $scrapbookPictureArray;
        $resultArray['frames'] = $framesArray;
        $resultArray['webbrands'] = $webBrandsArray;
        $resultArray['aucacheversionmasks'] = $auCacheVersionMasks;
        $resultArray['aucacheversionbackgrounds'] = $auCacheVersionBackgrounds;
        $resultArray['aucacheversionscrapbook'] = $auCacheVersionScrapbook;
        $resultArray['aucacheversionframes'] = $auCacheVersionFrames;

        return $resultArray;
    }

    /**
     * Unsets keys from product array that are not needed by desktop or online autoupdate
     * @param array $pProductArray
     * @return array The input array with unneeded keys removed
     */
    static function unsetUnusedProductKeysForCache($pProductArray)
    {
        $productCount = count($pProductArray);

        for ($i = 0; $i < $productCount; $i++)
        {
            $theProduct = &$pProductArray[$i];
            unset($theProduct['name']);
            unset($theProduct['description']);

            if (! empty($theProduct['coverlist']))
            {
                $theProduct['coverlist'] = array_map('AppAPI_model::unsetIndividualUnusedComponentKeysForCache', $theProduct['coverlist']);
            }

            if (! empty($theProduct['paperlist']))
            {
                $theProduct['paperlist'] = array_map('AppAPI_model::unsetIndividualUnusedComponentKeysForCache', $theProduct['paperlist']);
            }

            if (! empty($theProduct['singleprintlist']))
            {
                $theProduct['singleprintlist'] = array_map('AppAPI_model::unsetIndividualUnusedComponentKeysForCache', $theProduct['singleprintlist']);
            }

            if (! empty($theProduct['singleprintoptionlist']))
            {
                $theProduct['singleprintoptionlist'] = array_map('AppAPI_model::unsetIndividualUnusedComponentKeysForCache', $theProduct['singleprintoptionlist']);
            }

            if (! empty($theProduct['calendarcustomisationlist']))
            {
                $theProduct['calendarcustomisationlist'] = array_map('AppAPI_model::unsetIndividualUnusedComponentKeysForCache', $theProduct['calendarcustomisationlist']);
            }

            if (! empty($theProduct['taopixailist']))
            {
                $theProduct['taopixailist'] = array_map('AppAPI_model::unsetIndividualUnusedComponentKeysForCache', $theProduct['taopixailist']);
            }
        }

        return $pProductArray;
    }

    /**
     * Unsets keys from individual component array that are not needed by desktop or online autoupdate
     * @param array $pComponentArray
     * @return array The input array with unneeded keys removed
     */
    static function unsetIndividualUnusedComponentKeysForCache($pComponentArray)
    {
        unset($pComponentArray['datecreated']);
        unset($pComponentArray['skucode']);
        unset($pComponentArray['info']);
        unset($pComponentArray['assetid']);
        unset($pComponentArray['ticked']);
        unset($pComponentArray['previewtype']);
        unset($pComponentArray['unitcost']);
        unset($pComponentArray['unitweight']);
        unset($pComponentArray['priceinfo']);
        unset($pComponentArray['itemqtydropdown']);
        unset($pComponentArray['orderfooterusesproductquantity']);
        unset($pComponentArray['orderfootertaxlevel']);
        unset($pComponentArray['storewhennotselected']);
        unset($pComponentArray['quantity']);

        return $pComponentArray;
    }

    static function autoUpdatePathValid($pSourcePath)
    {
        $pathValid = true;

        $tempPath = $pSourcePath . time();

        if ($handle = fopen($tempPath, 'a'))
        {
            if (fwrite($handle, 'test') === false)
            {
                $pathValid = false;
            }
            fclose($handle);
        }
        else
        {
            $pathValid = false;
        }

        if (file_exists($tempPath))
        {
            UtilsObj::deleteFile($tempPath);
        }
        else
        {
            $pathValid = false;
        }

        return $pathValid;
    }

    static function systemUpdate($pProductCollectionCode = '')
    {
        $resultArray = Array();
        $result = '';
        $resultParam = '';

        $groupCode = '';

        if (array_key_exists('code', $_POST))
        {
            $groupCode = $_POST['code'];
            if ($groupCode == '')
            {
                $result = 'str_NoGroupCode';
            }
        }
        else
        {
            $result = 'str_NoGroupCode';
        }

        if ($result == '')
        {
			$ownerCode = UtilsObj::getPOSTParam('ownercode');
            $ownerCode2 = UtilsObj::getPOSTParam('code2');
            $webBrandCode = UtilsObj::getPOSTParam('webbrandcode');
            $appVersion = UtilsObj::getPOSTParam('appversion', '');
            $osVersion = UtilsObj::getPOSTParam('apposversion', '');
            $cpuType = UtilsObj::getPOSTParam('appcputype', '');
            $appPlatform = UtilsObj::getPOSTParam('appplatform', '');
            $apiVersion = (int)UtilsObj::getPOSTParam('version', '1');
            $languageCode = UtilsObj::getPOSTParam('langcode', 'en');
            $currentProductCategoryVersion = (string) UtilsObj::getPOSTParam('productcategoryversion', '');
            $currentCalendarDataVersion = (string) UtilsObj::getPOSTParam('calendardataversion', '');
            $uploadRefData = UtilsObj::getPOSTParam('uploadreflist', '');
            $pPreviousProductAutoUpdateCacheVersion = UtilsObj::getPOSTParam('cacheversionproducts', '');
            $pPreviousMasksAutoUpdateCacheVersion = UtilsObj::getPOSTParam('cacheversionmasks', '');
            $pPreviousBackgroundsAutoUpdateCacheVersion = UtilsObj::getPOSTParam('cacheversionbackgrounds', '');
            $pPreviousScrapbookAutoUpdateCacheVersion = UtilsObj::getPOSTParam('cacheversionscrapbook', '');
            $pPreviousFramesAutoUpdateCacheVersion = UtilsObj::getPOSTParam('cacheversionframes', '');

            $resultArray = self::systemUpdateProcess($ownerCode, $ownerCode2, $pProductCollectionCode, $groupCode, $webBrandCode, $appVersion, $osVersion,
                    $cpuType, $apiVersion, $languageCode, $currentProductCategoryVersion, $currentCalendarDataVersion, $uploadRefData, $pPreviousProductAutoUpdateCacheVersion,
                    $pPreviousMasksAutoUpdateCacheVersion, $pPreviousBackgroundsAutoUpdateCacheVersion, $pPreviousScrapbookAutoUpdateCacheVersion,
                    $pPreviousFramesAutoUpdateCacheVersion, $appPlatform);
        }
        else
        {
            $resultArray['result'] = $result;
            $resultArray['resultparam'] = $resultParam;
        }

        return $resultArray;
    }


    static function decodeCreatorCommandParams()
    {
        $resultArray = self::decodeDesignerCommandParams();
        $result = '';
        $resultParam = '';
        $login = '';
        $password = '';

		// decode the authentication parameters
        $authKey = UtilsObj::getArrayParam($resultArray, 'auth1');
        $authData = UtilsObj::getArrayParam($resultArray, 'auth2');
        $dataElementsKey = UtilsObj::getArrayParam($resultArray, 'auth3');

		if (($authKey != '') && ($authData != '') && ($dataElementsKey != ''))
        {
        	$systemConfigArray = DatabaseObj::getSystemConfig();

        	// decode the authentication key
        	$authKey = str_replace('_', '=', self::decodeEncryptedData($systemConfigArray['systemkey'], $authKey));

			// decode the data elements key
			$dataElementsKey = str_replace('_', '=', self::decodeEncryptedData($systemConfigArray['systemkey'], $dataElementsKey));

			// decode the authentication data
			if ((strlen($authKey) >= 40) && (strlen($dataElementsKey) >= 40))
			{
				// use the last 32 bytes for the key
				$authData = str_replace('_', '=', self::decodeEncryptedData(substr($authKey, -32), $authData));

				$authDataArray = explode("\t", $authData);
				if (count($authDataArray) == 2)
				{
					// decode the data elements
					$login = self::decodeEncryptedData(substr($dataElementsKey, 0, 32), $authDataArray[0]);
					$password = self::decodeEncryptedData(substr($dataElementsKey, -32), $authDataArray[1]);
				}
			}
        }

		// process the login
        if (($login != '') && ($password != ''))
        {
            $userAccountArray = DatabaseObj::getUserAccountFromLoginAndPassword($login, $password, TPX_PASSWORDFORMAT_CLEARTEXT);
            if ($userAccountArray['result'] == '')
            {
                if ($userAccountArray['usertype'] != TPX_LOGIN_CREATOR_ADMIN)
                {
                    // the login is not a creator admin
                    $result = 'str_ErrorNoAccount';
                }
                else
                {
                    // make sure that the user's ip address is allowed
                    $isIPAllowed = DatabaseObj::isUserIPAllowed($_SERVER['REMOTE_ADDR'], $userAccountArray['ipaccesslist'],
                            $userAccountArray['ipaccesstype'], $userAccountArray['companycode']);

                    $result = $isIPAllowed['result'];


                    // make sure that the login is active
                    if ($result == '')
                    {
                        if ($userAccountArray['isactive'] == 0)
                        {
                            $result = 'str_ErrorAccountNotActive';
                        }
                    }
                }
            }
            else
            {
                // the user account was not retrieved
                $result = $userAccountArray['result'];
                $resultParam = $userAccountArray['resultparam'];
            }
        }
        else
        {
            // no authentication parameters available
            $result = 'str_ErrorNoAccount';
        }

        $resultArray['result'] = $result;
        $resultArray['resultparam'] = $resultParam;

        return $resultArray;
    }

    /**
    * Processes and updates the product categories data with the meta-data supplied by TAOPIX™ Builder
    *
    * @static
    *
    * @return array
    *   the result array will contain the response to be echo'd back to the calling application
    *
    * @author Kevin Gale
    * @since Version 3.0.0
    */
    static function uploadProductCategories()
    {
        global $ac_config;
        global $gConstants;

        $result = '';
        $resultParam = '';
        $webBrandCode = '';
        $productCategoryVersion = '';
        $productCategoryData = '';

        $apiVersion = (int)UtilsObj::getPOSTParam('version', '1');
        $ownerCompany = UtilsObj::getPOSTParam('ownercompany');
        $languageCode = UtilsObj::getPOSTParam('langcode', $gConstants['defaultlanguagecode']);


        // first make sure that the authentication parameters are valid
        $dataArray = self::decodeCreatorCommandParams();
        $result = $dataArray['result'];
        $resultParam = $dataArray['resultparam'];

        if ($result == '')
        {
            if (array_key_exists('webbrandcode', $_POST))
            {
                $webBrandCode = $_POST['webbrandcode'];
            }
            else
            {
                $result = 'str_MissingParameter';
                $resultParam = 'webbrandcode';
            }

            if (array_key_exists('productcategoryversion', $_POST))
            {
                $productCategoryVersion = $_POST['productcategoryversion'];
            }
            else
            {
                $result = 'str_MissingParameter';
                $resultParam = 'productcategoryversion';
            }

            if (array_key_exists('productcategorydata', $_POST))
            {
                $productCategoryData = $_POST['productcategorydata'];

                // if the apiversion is newer than 4 then we must base 64 decode the data
                if ($apiVersion > 4)
                {
                    $productCategoryData = base64_decode($productCategoryData);
                }
            }
            else
            {
                $result = 'str_MissingParameter';
                $resultParam = 'productcategorydata';
            }
        }

        if ($result == '')
        {
            $webBrandArray = DatabaseObj::getBrandingFromCode($webBrandCode);
            $result = $webBrandArray['result'];
            $resultParam = $webBrandArray['resultparam'];

            if ($result == '')
            {
                // insert or update the product category data within the assets database
                $updateAssetResultArray = DatabaseObj::updateAssetRecord($webBrandArray['productcategoryassetid'], $webBrandArray['code'],
                        TPX_ASSETTYPE_PRODUCTCATEGORYDATA, $productCategoryData, 'productcategory', 0, 0);
                $result = $updateAssetResultArray['result'];
                $resultParam = $updateAssetResultArray['resultparam'];
                $assetID = $updateAssetResultArray['assetid'];

                // if we have no error then update the branding record with the asset information
                if ($result == '')
                {
                    $dbObj = DatabaseObj::getGlobalDBConnection();
                    if ($dbObj)
                    {
                        if ($stmt = $dbObj->prepare('UPDATE `BRANDING` SET `productcategoryassetid` = ?, `productcategoryassetversion` = ? WHERE `id` = ?'))
                        {
                            if ($stmt->bind_param('isi', $assetID, $productCategoryVersion, $webBrandArray['id']))
                            {
                                if (! $stmt->execute())
                                {
                                    // could not execute statement
                                    $result = 'str_DatabaseError';
                                    $resultParam = 'uploadProductCategories execute ' . $dbObj->error;
                                }
                            }
                            else
                            {
                                // could not bind parameters
                                $result = 'str_DatabaseError';
                                $resultParam = 'uploadProductCategories bind ' . $dbObj->error;
                            }

                            $stmt->free_result();
                            $stmt->close();
                            $stmt = null;
                        }
                        else
                        {
                            // could not prepare statement
                            $result = 'str_DatabaseError';
                            $resultParam = 'uploadProductCategories prepare ' . $dbObj->error;
                        }

                        $dbObj->close();
                    }
                    else
                    {
                        // could not open database connection
                        $result = 'str_DatabaseError';
                        $resultParam = 'uploadProductCategories connect ' . $dbObj->error;
                    }
                }
            }
        }

        $resultArray['result'] = $result;
        $resultArray['resultparam'] = $resultParam;
        $resultArray['apiversion'] = $apiVersion;
        $resultArray['languageCode'] = $languageCode;

        return $resultArray;
    }


    /**
    * Processes and updates the products database with the meta-data supplied by TAOPIX™ Builder
    *
    * @static
    *
    * @return array
    *   the result array will contain the response to be echo'd back to the calling application
    *
    * @author Kevin Gale
    * @since Version 1.0.0
    */
    static function uploadProductsUpdate()
    {
        global $ac_config;
        global $gConstants;

        $result = '';
        $resultParam = '';
        $itemCount = 0;
        $productCollectionCode = '';
        $productCollectionName = '';
        $productCollectionDescription = '';
        $productCollectionVersion = '';
        $productCollectionAppVersion = '';
        $productCollectionDataVersion = '';
        $productCollectionDependencies = '';
        $productCollectionOnlineDependencies = '';
        $productCollectionSize = 0;
        $productCollectionChecksum = '';
        $productCollectionSeparateComponents = 0;
        $productCollectionHasPreview = 0;
        $productsHaveDimensions = 0;
        $productCollectionPublishVersion = 0;
        $productCollectionMoreInformationURL = '';
        $productCollectionThumbnailResourceRef = '';
        $productCollectionThumbnailResourceDataUID = '';
        $productCollectionPreviewResourceRef = '';
        $productCollectionPreviewResourceDataUID = '';
        $productCollectionSortLevel = '';
        $productCollectionTextEngineVersion = 0;
        $productCollectionResourceFolderPath = '';
        $productCollectionThumbnailResourceDevicePixelRatio = 1;
        $productCollectionPreviewResourceDevicePixelRatio = 1;

        $productLayoutList = Array();

		$productLinkCache = array();

        $tempDestPath = '';
        $error = false;

        $apiVersion = (int)UtilsObj::getPOSTParam('version', '1');
        $ownerCompany = UtilsObj::getPOSTParam('ownercompany');
        $languageCode = UtilsObj::getPOSTParam('langcode', $gConstants['defaultlanguagecode']);


        // first make sure that the authentication parameters are valid
        $dataArray = self::decodeCreatorCommandParams();
        $result = $dataArray['result'];
        $resultParam = $dataArray['resultparam'];

        if ($result == '')
        {
            // process the compsulary parameters
            if (! self::autoUpdatePathValid($ac_config['INTERNALPRODUCTSROOTPATH']))
            {
                $result = 'str_InvalidAutoUpdatePath';
            }
            else
            {
                if (array_key_exists('count', $_POST))
                {
                    $itemCount = $_POST['count'];
                    if ($itemCount == '')
                    {
                        $result = 'str_NoItemCount';
                    }
                }
                else
                {
                    $result = 'str_NoItemCount';
                }
            }
        }

        if ($result == '')
        {
            for ($i = 1; $i <= $itemCount; $i++)
            {
                $itemCountString = str_pad($i, 2, '0', STR_PAD_LEFT);

                // product collection code
                if (array_key_exists('code' . $itemCountString, $_POST))
                {
                    $productCollectionCode = $_POST['code' . $itemCountString];
                }
                else
                {
                    $result = 'str_MissingParameter';
                    $resultParam = 'code' . $itemCountString;
                    break;
                }

                // product collection name
                if (array_key_exists('name' . $itemCountString, $_POST))
                {
                    $productCollectionName = $_POST['name' . $itemCountString];
                }
                else
                {
                    $result = 'str_MissingParameter';
                    $resultParam = 'name' . $itemCountString;
                    break;
                }

                // product collection description
                $productCollectionDescription = UtilsObj::getPOSTParam('description' . $itemCountString);

                // product collection version
                if (array_key_exists('version' . $itemCountString, $_POST))
                {
                    $productCollectionVersion = $_POST['version' . $itemCountString];
                }
                else
                {
                    $result = 'str_MissingParameter';
                    $resultParam = 'version' . $itemCountString;
                    break;
                }

                // product collection application version
                if (array_key_exists('appversion' . $itemCountString, $_POST))
                {
                    $productCollectionAppVersion = $_POST['appversion' . $itemCountString];
                }
                else
                {
                    $result = 'str_MissingParameter';
                    $resultParam = 'appversion' . $itemCountString;
                    break;
                }

                // product collection data version
                if (array_key_exists('dataversion' . $itemCountString, $_POST))
                {
                    $productCollectionDataVersion = $_POST['dataversion' . $itemCountString];
                }
                else
                {
                    $result = 'str_MissingParameter';
                    $resultParam = 'dataversion' . $itemCountString;
                    break;
                }


                // retrieve the optional parameters
                $productCollectionSize = (float)UtilsObj::getPOSTParam('size' . $itemCountString, '0'); // we use a float for the file size to avoid integer overflows
                $productCollectionChecksum = UtilsObj::getPOSTParam('checksum' . $itemCountString, '');
                $productCollectionDependencies = UtilsObj::getPOSTParam('dependencies' . $itemCountString, '');
                $productCollectionOnlineDependencies = UtilsObj::getPOSTParam('onlinedependencies' . $itemCountString, '');
                $productCollectionSeparateComponents = UtilsObj::getPOSTParam('separatecomponents' . $itemCountString, 'FALSE');
                $productCollectionHasPreview = UtilsObj::getPOSTParam('haspreview' . $itemCountString, 'FALSE');
                $productsHaveDimensions = UtilsObj::getPOSTParam('hasdimensions' . $itemCountString, 'FALSE');
                $productHasDesktopLayouts = UtilsObj::getPOSTParam('hasdesktoplayouts' . $itemCountString, 'TRUE');
                $productHasOnlineLayouts = UtilsObj::getPOSTParam('hasonlinelayouts' . $itemCountString, 'FALSE');
                $productCollectionMoreInformationURL = UtilsObj::getPOSTParam('moreinformationurl' . $itemCountString, '');
                $productCollectionThumbnailResourceRef = UtilsObj::getPOSTParam('thumbnailresourceref' . $itemCountString, '');
                $productCollectionThumbnailResourceDataUID = UtilsObj::getPOSTParam('thumbnailresourcedatauid' . $itemCountString, '');
                $productCollectionPreviewResourceRef = UtilsObj::getPOSTParam('previewresourceref' . $itemCountString, '');
                $productCollectionPreviewResourceDataUID = UtilsObj::getPOSTParam('previewresourcedatauid' . $itemCountString, '');
                $productCollectionSortLevel = UtilsObj::getPOSTParam('sortlevel' . $itemCountString, '');
                $productCollectionTextEngineVersion = UtilsObj::getPOSTParam('textengineversion'. $itemCountString, 0);
                $productCollectionPublishVersion = UtilsObj::getPOSTParam('publishversion' . $itemCountString, 0);
                $productCollectionSummary = UtilsObj::getPOSTParam('summary' . $itemCountString, '');
                $productCollectionThumbnailResourceDevicePixelRatio = UtilsObj::getPOSTParam('thumbnailresourcedevicepixelratio' . $itemCountString, 1);
                $productCollectionPreviewResourceDevicePixelRatio = UtilsObj::getPOSTParam('previewresourcedevicepixelratio' . $itemCountString, 1);

                // determine how many layouts we have
                $productLayoutList = Array();
                $layoutCount = (int)UtilsObj::getPOSTParam('layoutcount' . $itemCountString, '0');
                if ($layoutCount == 0)
                {
                    // if we have 0 layouts then this is an old product so we invent a layout with the product collection details
                    $layoutItem['code'] = $productCollectionCode;
                    $layoutItem['name'] = $productCollectionName;
                    $layoutItem['description'] = $productCollectionDescription;
                    $layoutItem['type'] = 0;
                    $layoutItem['minpagecount'] = 0;
                    $layoutItem['maxpagecount'] = 0;
                    $layoutItem['defaultpagecount'] = 0;
                    $layoutItem['pageinsertcount'] = 0;
                    $layoutItem['paperwidth'] = 0;
                    $layoutItem['paperheight'] = 0;
                    $layoutItem['paperbleed'] = 0;
                    $layoutItem['paperinsidebleed'] = 0;
                    $layoutItem['spreads'] = 0;
                    $layoutItem['width'] = 0;
                    $layoutItem['height'] = 0;
                    $layoutItem['firstpage'] = 0;
                    $layoutItem['cover1active'] = 0;
                    $layoutItem['cover1type'] = 0;
                    $layoutItem['cover1paperwidth'] = 0;
                    $layoutItem['cover1paperheight'] = 0;
                    $layoutItem['cover1bleed'] = 0;
                    $layoutItem['cover1backflap'] = 0;
                    $layoutItem['cover1frontflap'] = 0;
                    $layoutItem['cover1wraparound'] = 0;
                    $layoutItem['cover1spine'] = 0;
                    $layoutItem['cover1flexiblespine'] = 0;
                    $layoutItem['cover1width'] = 0;
                    $layoutItem['cover1height'] = 0;
                    $layoutItem['cover1flexiblespinedata'] = '';
                    $layoutItem['cover2active'] = 0;
                    $layoutItem['cover2paperwidth'] = 0;
                    $layoutItem['cover2paperheight'] = 0;
                    $layoutItem['cover2bleed'] = 0;
                    $layoutItem['cover2width'] = 0;
                    $layoutItem['cover2height'] = 0;

                    array_push($productLayoutList, $layoutItem);
                }
                else
                {
                    // process the individual layouts
                    for ($i2 = 1; $i2 <= $layoutCount; $i2++)
                    {
                        $itemCountString2 = str_pad($i2, 3, '0', STR_PAD_LEFT);

                        $layoutItem['code'] = UtilsObj::getPOSTParam('layoutcode' . $itemCountString . $itemCountString2);
                        $layoutItem['name'] = UtilsObj::getPOSTParam('layoutname' . $itemCountString . $itemCountString2);
                        $layoutItem['description'] = UtilsObj::getPOSTParam('layoutdescription' . $itemCountString . $itemCountString2);
                        $layoutItem['type'] = UtilsObj::getPOSTParam('layouttype' . $itemCountString . $itemCountString2);
                        $layoutItem['minpagecount'] = (int) UtilsObj::getPOSTParam('layoutminpagecount' . $itemCountString . $itemCountString2, '0');
                        $layoutItem['maxpagecount'] = (int) UtilsObj::getPOSTParam('layoutmaxpagecount' . $itemCountString . $itemCountString2, '0');
                        $layoutItem['defaultpagecount'] = (int) UtilsObj::getPOSTParam('layoutdefaultpagecount' . $itemCountString . $itemCountString2, '0');
                        $layoutItem['pageinsertcount'] = (int) UtilsObj::getPOSTParam('layoutpageinsertcount' . $itemCountString . $itemCountString2, '0');
                        $layoutItem['paperwidth'] = (string) UtilsObj::getPOSTParam('layoutpaperwidth' . $itemCountString . $itemCountString2, '0');
                        $layoutItem['paperheight'] = (string) UtilsObj::getPOSTParam('layoutpaperheight' . $itemCountString . $itemCountString2, '0');
                        $layoutItem['paperbleed'] = (string) UtilsObj::getPOSTParam('layoutpaperbleed' . $itemCountString . $itemCountString2, '0');
                        $layoutItem['paperinsidebleed'] = UtilsObj::getPOSTParam('layoutpaperinsidebleed' . $itemCountString . $itemCountString2, 'FALSE');
                        $layoutItem['spreads'] = UtilsObj::getPOSTParam('layoutspreads' . $itemCountString . $itemCountString2, 'FALSE');
                        $layoutItem['width'] = (string) UtilsObj::getPOSTParam('layoutwidth' . $itemCountString . $itemCountString2, '0');
                        $layoutItem['height'] = (string) UtilsObj::getPOSTParam('layoutheight' . $itemCountString . $itemCountString2, '0');
                        $layoutItem['firstpage'] = (int) UtilsObj::getPOSTParam('layoutfirstpage' . $itemCountString . $itemCountString2, '0');
                        $layoutItem['cover1active'] = UtilsObj::getPOSTParam('layoutcover1active' . $itemCountString . $itemCountString2, 'FALSE');
                        $layoutItem['cover1type'] = (int) UtilsObj::getPOSTParam('layoutcover1type' . $itemCountString . $itemCountString2, '0');
                        $layoutItem['cover1paperwidth'] = (string) UtilsObj::getPOSTParam('layoutcover1paperwidth' . $itemCountString . $itemCountString2, '0');
                        $layoutItem['cover1paperheight'] = (string) UtilsObj::getPOSTParam('layoutcover1paperheight' . $itemCountString . $itemCountString2, '0');
                        $layoutItem['cover1bleed'] = (string) UtilsObj::getPOSTParam('layoutcover1bleed' . $itemCountString . $itemCountString2, '0');
                        $layoutItem['cover1backflap'] = (string) UtilsObj::getPOSTParam('layoutcover1backflap' . $itemCountString . $itemCountString2, '0');
                        $layoutItem['cover1frontflap'] = (string) UtilsObj::getPOSTParam('layoutcover1frontflap' . $itemCountString . $itemCountString2, '0');
                        $layoutItem['cover1wraparound'] = (string) UtilsObj::getPOSTParam('layoutcover1wraparound' . $itemCountString . $itemCountString2, '0');
                        $layoutItem['cover1spine'] = (string) UtilsObj::getPOSTParam('layoutcover1spine' . $itemCountString . $itemCountString2, '0');
                        $layoutItem['cover1flexiblespine'] = UtilsObj::getPOSTParam('layoutcover1flexiblespine' . $itemCountString . $itemCountString2, 'FALSE');
                        $layoutItem['cover1width'] = (string) UtilsObj::getPOSTParam('layoutcover1width' . $itemCountString . $itemCountString2, '0');
                        $layoutItem['cover1height'] = (string) UtilsObj::getPOSTParam('layoutcover1height' . $itemCountString . $itemCountString2, '0');
                        $layoutItem['cover1flexiblespinedata'] = UtilsObj::getPOSTParam('layoutcover1flexiblespinedata' . $itemCountString . $itemCountString2);
                        $layoutItem['cover2active'] = UtilsObj::getPOSTParam('layoutcover2active' . $itemCountString . $itemCountString2, 'FALSE');
                        $layoutItem['cover2paperwidth'] = (string) UtilsObj::getPOSTParam('layoutcover2paperwidth' . $itemCountString . $itemCountString2, '0');
                        $layoutItem['cover2paperheight'] = (string) UtilsObj::getPOSTParam('layoutcover2paperheight' . $itemCountString . $itemCountString2, '0');
                        $layoutItem['cover2bleed'] = (string) UtilsObj::getPOSTParam('layoutcover2bleed' . $itemCountString . $itemCountString2, '0');
                        $layoutItem['cover2width'] = (string) UtilsObj::getPOSTParam('layoutcover2width' . $itemCountString . $itemCountString2, '0');
                        $layoutItem['cover2height'] = (string) UtilsObj::getPOSTParam('layoutcover2height' . $itemCountString . $itemCountString2, '0');
                        $layoutItem['availabledesktop'] = (string) UtilsObj::getPOSTParam('layoutavailabledesktop' . $itemCountString . $itemCountString2, '0');
                        $layoutItem['availableonline'] = (string) UtilsObj::getPOSTParam('layoutavailableonline' . $itemCountString . $itemCountString2, '0');
                        $layoutItem['productmoreinformationurl'] = UtilsObj::getPOSTParam('layoutmoreinformationurl' . $itemCountString . $itemCountString2, '');
                        $layoutItem['productthumbnailresourceref'] = UtilsObj::getPOSTParam('layoutthumbnailresourceref' . $itemCountString . $itemCountString2, '');
                        $layoutItem['productthumbnailresourcedatauid'] = UtilsObj::getPOSTParam('layoutthumbnailresourcedatauid' . $itemCountString . $itemCountString2, '');
                        $layoutItem['productpreviewresourceref'] = UtilsObj::getPOSTParam('layoutpreviewresourceref' . $itemCountString . $itemCountString2, '');
                        $layoutItem['productpreviewresourcedatauid'] = UtilsObj::getPOSTParam('layoutpreviewresourcedatauid' . $itemCountString . $itemCountString2, '');
                        $layoutItem['productpagesafemargin'] = UtilsObj::getPOSTParam('layoutpagesafemargin' . $itemCountString . $itemCountString2, '0');
                        $layoutItem['productcover1safemargin'] = UtilsObj::getPOSTParam('layoutcover1safemargin' . $itemCountString . $itemCountString2, '0');
                        $layoutItem['productcover2safemargin'] = UtilsObj::getPOSTParam('layoutcover2safemargin' . $itemCountString . $itemCountString2, '0');
                        $layoutItem['productselectormodedesktop'] = UtilsObj::getPOSTParam('layoutproductselectormodedesktop' . $itemCountString . $itemCountString2, 0);
                        $layoutItem['productconfigurationflags'] = UtilsObj::getPOSTParam('layoutconfigurationflags' . $itemCountString . $itemCountString2, 0);
                        $layoutItem['productpagecontentassignmode'] = UtilsObj::getPOSTParam('layoutpagecontentassignmode' . $itemCountString . $itemCountString2, 0);
                        $layoutItem['productaimodedesktop'] = UtilsObj::getPOSTParam('layoutaimodedesktop' . $itemCountString . $itemCountString2, 0);
                        $layoutItem['productaimodeonline'] = UtilsObj::getPOSTParam('layoutaimodeonline' . $itemCountString . $itemCountString2, 0);
                        $layoutItem['productcalendarlocale'] = UtilsObj::getPOSTParam('layoutcalendarlocale' . $itemCountString . $itemCountString2, '');
                        $layoutItem['productcalendarlocalecanchange'] = self::desktopBoolStringToInt(UtilsObj::getPOSTParam('layoutcalendarlocalecanchange' . $itemCountString . $itemCountString2, 0));
                        $layoutItem['productcalendarstartday'] = UtilsObj::getPOSTParam('layoutcalendarstartday' . $itemCountString . $itemCountString2, 0);
                        $layoutItem['productcalendarstartdaycanchange'] = self::desktopBoolStringToInt(UtilsObj::getPOSTParam('layoutcalendarstartdaycanchange' . $itemCountString . $itemCountString2, 0));
                        $layoutItem['productcalendarstartmonth'] = UtilsObj::getPOSTParam('layoutcalendarstartmonth' . $itemCountString . $itemCountString2, 0);
                        $layoutItem['productcalendarstartmonthcanchange'] = self::desktopBoolStringToInt(UtilsObj::getPOSTParam('layoutcalendarstartmonthcanchange' . $itemCountString . $itemCountString2, 0));
                        $layoutItem['productcalendarstartyear'] = UtilsObj::getPOSTParam('layoutcalendarstartyear' . $itemCountString . $itemCountString2, 0);
                        $layoutItem['productcalendarstartyearcanchange'] = self::desktopBoolStringToInt(UtilsObj::getPOSTParam('layoutcalendarstartyearcanchange' . $itemCountString . $itemCountString2, 0));
                        $layoutItem['productsortorder'] = UtilsObj::getPOSTParam('layoutsortorder' . $itemCountString . $itemCountString2, 0);
                        $layoutItem['producttarget'] = UtilsObj::getPOSTParam('layouttarget' . $itemCountString . $itemCountString2, 0);
                        $layoutItem['productorientation'] = UtilsObj::getPOSTParam('layoutorientation' . $itemCountString . $itemCountString2, 0);
                        $layoutItem['productsizecode'] = UtilsObj::getPOSTParam('layoutsizecode' . $itemCountString . $itemCountString2, '');
                        $layoutItem['productsizename'] = UtilsObj::getPOSTParam('layoutsizename' . $itemCountString . $itemCountString2, '');
                        $layoutItem['productsizearea'] = UtilsObj::getPOSTParam('layoutsizearea' . $itemCountString . $itemCountString2, 0);
                        $layoutItem['productthumbnailresourcedevicepixelratio'] = UtilsObj::getPOSTParam('layoutthumbnailresourcedevicepixelratio' . $itemCountString . $itemCountString2, 1);
                        $layoutItem['productpreviewresourcedevicepixelratio'] = UtilsObj::getPOSTParam('layoutpreviewresourcedevicepixelratio' . $itemCountString . $itemCountString2, 1);

                        array_push($productLayoutList, $layoutItem);
                    }
                }

                $productCollectionResourceCount = UtilsObj::getPOSTParam('productcollectionresourcescount' . $itemCountString, 0);
                $productCollectionResourceArray = Array();

                if ($productCollectionResourceCount > 0)
                {
                    for ($i2 = 0; $i2 < $productCollectionResourceCount; $i2++)
                    {
                        $theResource = Array();

                        $theResource['ref'] = UtilsObj::getPOSTParam('resourceref' . $i2, '');
                        $theResource['kind'] = UtilsObj::getPOSTParam('resourcekind' . $i2, 0);
                        $theResource['datauid'] = UtilsObj::getPOSTParam('resourcedatauid' . $i2, '');
                        $theResource['data'] = base64_decode(UtilsObj::getPOSTParam('resourcedata' . $i2, ''));

                        $productCollectionResourceArray[] = $theResource;
                    }
                }


                // at this point we should have the data we need for the product
                $newFilesTempArray = Array();
                $newFilesArray = Array();
                $tempFilesArray = Array();
                $backupFilesArray = Array();
                $backupFileCount = 0;

                // check to see if the uploaded product collection archive file exists
                $sourceFilePath = $ac_config['INTERNALSYSTEMUPLOADROOTPATH'] . 'Products/' . $productCollectionCode . '.zip';
                $destFilePath = $ac_config['INTERNALPRODUCTSROOTPATH'] . $productCollectionCode . '.zip';
                if (file_exists($sourceFilePath))
                {
                    $newFilesTempArray[] = $sourceFilePath;
                    $newFilesArray[] = $destFilePath;

                    // if a file already exists back it up
                    if (file_exists($destFilePath))
                    {
                        $tempDestFilePath = $ac_config['INTERNALPRODUCTSROOTPATH'] . $productCollectionCode . '_temp.zip';
                        $backupFilesArray[] = $destFilePath;
                        $tempFilesArray[] = $tempDestFilePath;
                    }
                }
                else
                {
                    $result = 'str_MissingFile';
                    $resultParam = $sourceFilePath;
                    $error = true;
                }

                if (! $error)
                {
                    $productCollectionResourceFolderPath = UtilsObj::getProductCollectionResourceFolderPath($productCollectionCode, $productCollectionVersion);

                    if (! file_exists($productCollectionResourceFolderPath))
                    {
                        if (! UtilsObj::createAllFolders($productCollectionResourceFolderPath))
                        {
                            $error = true;
                            $result = 'str_FolderCreationError';
                            $resultParam = $productCollectionResourceFolderPath;
                        }
                    }
                }

                // process the product collection resources
                if (! $error)
                {
                    if ($productCollectionResourceCount > 0)
                    {
                        for ($i2 = 0; $i2 < $productCollectionResourceCount; $i2++)
                        {
                            $theResource = $productCollectionResourceArray[$i2];

                            $destFilePath = $productCollectionResourceFolderPath . DIRECTORY_SEPARATOR . $theResource['ref'] . '.dat';
                            $sourceFilePath = $productCollectionResourceFolderPath . DIRECTORY_SEPARATOR . $theResource['ref'] . '_newtemp.dat';

                            $newFilesTempArray[] = $sourceFilePath;
                            $newFilesArray[] = $destFilePath;

                            UtilsObj::deleteFile($sourceFilePath);
                            $writeSuccess = UtilsObj::writeTextFile($sourceFilePath, $theResource['data']);

                            if (! $writeSuccess)
                            {
                                $result = 'str_MissingFile';
                                $resultParam = $sourceFilePath;
                                $error = true;
                            }
                            else
                            {
                                // if we have a pre-existing file temporarily back it up so we can roll back in the event of a failure
                                if (file_exists($destFilePath))
                                {
                                    $backupFilesArray[] = $destFilePath;
                                    $tempFilesArray[] = $productCollectionResourceFolderPath . DIRECTORY_SEPARATOR . $theResource['ref'] . '_temp.dat';
                                }
                            }
                        }
                    }
                }


                // backup the files
                if (! $error)
                {
                    $backupFilesCount = count($backupFilesArray);
                    for ($i2 = 0; $i2 < $backupFilesCount; $i2++)
                    {
                        $tempDestPath = $tempFilesArray[$i2];
                        UtilsObj::deleteFile($tempDestPath);

                        if (rename($backupFilesArray[$i2], $tempDestPath))
                        {
                            $error = ! file_exists($tempDestPath);
                        }
                        else
                        {
                            $error = true;
                        }

                        if (! $error)
                        {
                            $backupFileCount++;
                        }
                        else
                        {
                            $result = 'str_FileMoveError';
                            $resultParam = $tempDestPath;
                            $error = true;
                        }
                    }
                }

                if (! $error)
                {
                    // if appAPI is version 1 then convert strings to multi-lingual
                    if ($apiVersion == 1)
                    {
                        $productCollectionName = $languageCode ." ". $productCollectionName;
                    }

					if ($gConstants['optiontdpv'])
					{
						// get list of all products collection layouts before they get updated/removed
						$getProductCollectionLinkByCollectionCodeResult = DatabaseObj::getProductCollectionLinkByCollectionCode($productCollectionCode);

						// don't return an error if the call fails
						if ($getProductCollectionLinkByCollectionCodeResult['result'] == '')
						{
							$productLinkCache = $getProductCollectionLinkByCollectionCodeResult['data'];
						}
					}

                    // Grab a database connection here and then start the transaction so that we can rollback in the event of a copy failure
                    $dbObj = DatabaseObj::getGlobalDBConnection();

                    if ($dbObj)
                    {
                        if ($dbObj->begin_transaction())
                        {

                            // at this point we must now update the products database
                            $dbResultArray = DatabaseObj::uploadProductsUpdate($dbObj, $ownerCompany, $productCollectionCode, $productCollectionName, $productCollectionDescription,
                            $productCollectionVersion, $productCollectionAppVersion, $productCollectionDataVersion, $productCollectionSize,
                            $productCollectionChecksum, $productCollectionDependencies, $productCollectionOnlineDependencies, $productCollectionSeparateComponents, $productCollectionHasPreview,
                            $productsHaveDimensions, $productHasDesktopLayouts, $productHasOnlineLayouts, $productLayoutList,
                            $productCollectionMoreInformationURL, $productCollectionThumbnailResourceRef, $productCollectionThumbnailResourceDataUID, $productCollectionPreviewResourceRef,
                            $productCollectionPreviewResourceDataUID, $productCollectionSortLevel, $productCollectionTextEngineVersion, $productCollectionPublishVersion, $productCollectionSummary,
                            $productCollectionThumbnailResourceDevicePixelRatio, $productCollectionPreviewResourceDevicePixelRatio);

                            $result = $dbResultArray['result'];
                            $resultParam = $dbResultArray['resultparam'];

                            // deprecate the records for the old resources
                            if ($result === '')
                            {
                                $dbDeprecateResultArray = DatabaseObj::deprecateProductCollectionResources($dbObj, $productCollectionCode);

                                $result = $dbDeprecateResultArray['error'];
                                $resultParam = $dbDeprecateResultArray['errorparam'];
                            }

                            // create the new json metadata file
                            if ($result === '')
                            {
                                $metaDataCreationResultArray = self::buildDesignerJSONMetadataFile($dbObj, $productCollectionCode, $productCollectionVersion);

                                $result = $metaDataCreationResultArray['error'];
                                $resultParam = $metaDataCreationResultArray['errorparam'];
                                $metadataCreationDataArray = $metaDataCreationResultArray['data'];

                                if ($result === '')
                                {
                                    // we have succesfully created a metadata file, create a database record for it
                                    $theResource = Array();

                                    $theResource['ref'] = 'JSONMETADATAFILE';
                                    $theResource['kind'] = TPX_DESKTOP_RESOURCEKIND_NONCREATORRESOURCE;
                                    $theResource['datauid'] = $metadataCreationDataArray['datauid'];
                                    // there is no data to store in here and it should not be used after this point but fill it here to maintain consistency
                                    $theResource['data'] = '';

                                    // if we have backed up a previous file add it to the backup list
                                    if ($metadataCreationDataArray['backuptemppath'] !== '')
                                    {
                                        $backupFileCount++;
                                        $backupFilesArray[] = $metadataCreationDataArray['destpath'];
                                        $tempFilesArray[] = $metadataCreationDataArray['backuptemppath'];
                                    }

                                    $newFilesTempArray[] = $metadataCreationDataArray['sourcepath'];
                                    $newFilesArray[] = $metadataCreationDataArray['destpath'];
                                    $productCollectionResourceArray[] = $theResource;
                                    $productCollectionResourceCount++;
                                }
                            }

                            // insert the records for the new resources
                            if (($result === '') && ($productCollectionResourceCount > 0))
                            {
                                $dbInsertResultArray = DatabaseObj::insertProductCollectionResourceRecords($dbObj, $productCollectionResourceArray, $productCollectionVersion, $productCollectionCode);

                                $result = $dbInsertResultArray['error'];
                                $resultParam = $dbInsertResultArray['errorparam'];
                            }

                            if ($result === '')
                            {
                                // we can now finally attempt to move the files to their final correct location
                                for ($i2 = 0; $i2 < count($newFilesArray); $i2++)
                                {
                                    clearstatcache();

                                    $destPath = $newFilesArray[$i2];
                                    UtilsObj::deleteFile($destPath);

                                    if (rename($newFilesTempArray[$i2], $destPath))
                                    {
                                        $error = ! file_exists($destPath);
                                    }
                                    else
                                    {
                                        $error = true;
                                    }

                                    if ($error)
                                    {
                                        $result = 'str_FileMoveError';
                                        $resultParam = $destPath;
                                        break;
                                    }
                                }

                                // now that all files have been moved we want to attempt to commit the database changes
                                if (! $error)
                                {
                                    $error = ! $dbObj->commit();
                                }

                                // if no error occurred we can remove any temporary files
                                if (! $error)
                                {
                                    for ($i2 = 0; $i2 < count($tempFilesArray); $i2++)
                                    {
                                        UtilsObj::deleteFile($tempFilesArray[$i2]);
                                    }
                                }
                                else
                                {
                                    // an error has occured so we need to rollback the database changes
                                    $dbObj->rollback();
                                }
                            }
                            else
                            {
                                $error = true;
                            }
                        }
                        else
                        {
                            // could not begin transaction connection
                            $result = 'str_DatabaseError';
                            $resultParam = 'uploadProductsUpdate begin transaction ' . $dbObj->error;
                        }

                        $dbObj->close();
                    }
                    else
                    {
                        // could not open database connection
                        $result = 'str_DatabaseError';
                        $resultParam = 'uploadProductsUpdate connect ' . $dbObj->error;
                    }
                }

                // if we have an error delete the uploaded / thumbnail / preview files and restore the other files before we exit
                if ($error)
                {
                    for ($i = 0; $i < count($newFilesTempArray); $i++)
                    {
                        UtilsObj::deleteFile($newFilesTempArray[$i]);
                    }

                    for ($i = 0; $i < $backupFileCount; $i++)
                    {
                        rename($tempFilesArray[$i], $backupFilesArray[$i]);
                    }

                    break;
                }
                else
                {
                    if ($gConstants['optiondesol'])
                    {
                        $taskInfo = DatabaseObj::getTask('TAOPIX_ONLINEASSETPUSH');
                        if ($taskInfo['result'] == '')
                        {
                            $eventResultArray = DatabaseObj::createEvent('TAOPIX_ONLINEASSETPUSH', '', '', '', $taskInfo['nextRunTime'], 0, $dbResultArray['applicationfilerecordid'], TPX_APPLICATION_FILE_TYPE_PRODUCTCOLLECTION, '', '', '', '', '', '', 0, 0, 0, '', '', 0);

							// if $productLinkCache is empty then it is a new product collection so model links do not need to be updated
							if (($gConstants['optiontdpv']) && (count($productLinkCache) > 0))
							{
								$updateModelDataResult = self::update3DModelProductLink($productLayoutList, $productLinkCache);

								// don't return an error if the call fails
								if ($updateModelDataResult['result'] == '')
								{
									$modelLinksToDelete = $updateModelDataResult['modellinkstodelete'];

									if ($modelLinksToDelete != '')
									{
										$eventResultArray = DatabaseObj::createEvent('TAOPIX_ONLINEASSETPUSH', '', '', '', $taskInfo['nextRunTime'], 0, $dbResultArray['applicationfilerecordid'], TPX_APPLICATION_FILE_TYPE_3DMODEL, $modelLinksToDelete, '', '', '', '', '', 0, 0, 0, '', '', 0);
									}
								}
							}
                        }
                    }
                }
            }
        }

        $resultArray['result'] = $result;
        $resultArray['resultparam'] = $resultParam;
        $resultArray['apiversion'] = $apiVersion;
        $resultArray['languageCode'] = $languageCode;

        return $resultArray;
    }

    static function desktopBoolStringToInt($pString)
    {
        $result = 0;

        if ($pString === "TRUE")
        {
            $result = 1;
        }

        return $result;
    }

	static function update3DModelProductLink($pNewProductLayoutList, $pExistingProductLayoutList)
	{
		$resultArray = array();
		$result = '';
        $resultParam = '';
		$modelLinksToDelete = array();
		$layoutCodesToCheck = array();
		$productLinkProductCode = '';

		$dbObj = DatabaseObj::getGlobalDBConnection();
		if ($dbObj)
		{
			// check the collection type has not changed (e.g. photobook to calendar)
			if ($pNewProductLayoutList[0]['type'] == $pExistingProductLayoutList[0]['collectiontype'])
			{
				foreach ($pExistingProductLayoutList as $productCollectionLink)
				{
					$found = false;

					foreach ($pNewProductLayoutList as $newProductLayout)
					{
						$found = false;

						if ($productCollectionLink['productcode'] == $newProductLayout['code'])
						{
							$found = true;

							// check if the product is still available in Online
							if ($newProductLayout['availableonline'] == 'FALSE')
							{
								if (! array_key_exists($productCollectionLink['productcode'], $layoutCodesToCheck))
								{
									$layoutCodesToCheck[$productCollectionLink['productcode']] = 0;
								}
							}

							break;
						}
						else
						{
							$found = false;
						}
					}

					if (! $found)
					{
						if (! array_key_exists($productCollectionLink['productcode'], $layoutCodesToCheck))
						{
							$layoutCodesToCheck[$productCollectionLink['productcode']] = 0;
						}
					}
				}
			}
			else
			{
				// collection type has changed, use the original layputs to check
				foreach ($pExistingProductLayoutList as $productLayoutList)
				{
					if (! array_key_exists($productLayoutList['productcode'], $layoutCodesToCheck))
					{
						$layoutCodesToCheck[$productLayoutList['productcode']] = 0;
					}
				}
			}

			if (count($layoutCodesToCheck) > 0)
			{
				// find all photobook products with the same code as they may have a link to a 3d model
				// if there are no other products with the same product code then we can safely remove the link

				$productCodesToCheck = '"' . implode('", "', array_keys($layoutCodesToCheck)) . '"';

				if (($stmt = $dbObj->prepare('SELECT `productcode`
					FROM
						`PRODUCTCOLLECTIONLINK`
					WHERE
						`productcode` IN (' . $productCodesToCheck . ')
					AND
						`availableonline` = 1
					AND
						`collectiontype` = ' . TPX_PRODUCTCOLLECTIONTYPE_PHOTOBOOK)))

				{
					if ($stmt->bind_result($productLinkProductCode))
					{
						if ($stmt->execute())
						{
							if ($stmt->store_result())
							{
								if ($stmt->num_rows > 0)
								{
									while ($stmt->fetch())
									{
										if (array_key_exists($productLinkProductCode, $layoutCodesToCheck))
										{
											$layoutCodesToCheck[$productLinkProductCode]++;
										}
									}

									foreach($layoutCodesToCheck as $productCode => $count)
									{
										if ($count == 0)
										{
											if (! in_array($productCode, $modelLinksToDelete))
											{
												$modelLinksToDelete[] = $productCode;
											}
										}
									}
								}
								else
								{
									// no products exist with that code so delete all of them
									foreach ($layoutCodesToCheck as $layoutCode => $count)
									{
										if (! in_array($layoutCode, $modelLinksToDelete))
										{
											$modelLinksToDelete[] = $layoutCode;
										}
									}
								}
							}
						}
						else
						{
							// could not execute statement
							$result = 'str_DatabaseError';
							$resultParam = __FUNCTION__ . ' select product collection link execute ' . $dbObj->error;
						}
					}
					else
					{
						// could not bind result
						$result = 'str_DatabaseError';
						$resultParam = __FUNCTION__ . ' select product collection link bind result ' . $dbObj->error;
					}

					$stmt->free_result();
					$stmt->close();
					$stmt = null;
				}
				else
				{
					// could not prepare statement
					$result = 'str_DatabaseError';
					$resultParam = __FUNCTION__ . ' select product collection link prepare ' . $dbObj->error;
				}
			}

			if (count($modelLinksToDelete) > 0)
			{
				// delete the records in Control Centre
				if ($stmt = $dbObj->prepare('DELETE FROM `PRODUCTONLINESYSTEMRESOURCELINK` WHERE `productcode` = ? AND `type` = ' . TPX_SYSTEM_RESOURCE_TYPE_3DMODEL))
				{
					if ($stmt->bind_param('s', $productCode))
					{
						foreach($modelLinksToDelete as $productCode)
						{
							if (! $stmt->execute())
							{
								$result = 'str_DatabaseError';
								$resultParam  = __FUNCTION__ .' delete PRODUCTONLINESYSTEMRESOURCELINK execute error: ' . $dbObj->error;
							}
						}
					}
					else
					{
						$result = 'str_DatabaseError';
						$resultParam  = __FUNCTION__ . ' delete PRODUCTONLINESYSTEMRESOURCELINK bind param error: ' . $dbObj->error;
					}

					$stmt->free_result();
					$stmt->close();
					$stmt = null;
				}
				else
				{
					$result = 'str_DatabaseError';
					$resultParam  = __FUNCTION__ . ' delete PRODUCTONLINESYSTEMRESOURCELINK prepare error: ' . $dbObj->error;
				}
			}

			$dbObj->close();
		}

		$resultArray['result'] = $result;
		$resultArray['resultparam'] = $resultParam;
		$resultArray['modellinkstodelete'] = implode(',', $modelLinksToDelete);
		return $resultArray;
	}

    /**
    * Processes and updates the global calendar event set data with the meta-data supplied by Taopix Creator
    *
    * @static
    *
    * @return array
    *   the result array will contain the response to be echo'd back to the calling application
    *
    * @author Kevin Gale
    * @since Version 2016.1.0
    */
    static function uploadCalendarData()
    {
        global $ac_config;
        global $gConstants;

        $result = '';
        $resultParam = '';
        $webBrandCode = '';
        $calendarDataVersion = '';
        $calendarData = '';

        $apiVersion = (int)UtilsObj::getPOSTParam('version', '1');
        $ownerCompany = UtilsObj::getPOSTParam('ownercompany');
        $languageCode = UtilsObj::getPOSTParam('langcode', $gConstants['defaultlanguagecode']);


        // first make sure that the authentication parameters are valid
        $dataArray = self::decodeCreatorCommandParams();
        $result = $dataArray['result'];
        $resultParam = $dataArray['resultparam'];

        if ($result == '')
        {
            if (array_key_exists('webbrandcode', $_POST))
            {
                $webBrandCode = $_POST['webbrandcode'];
            }
            else
            {
                $result = 'str_MissingParameter';
                $resultParam = 'webbrandcode';
            }

            if (array_key_exists('calendardataversion', $_POST))
            {
                $calendarDataVersion = $_POST['calendardataversion'];
            }
            else
            {
                $result = 'str_MissingParameter';
                $resultParam = 'calendardataversion';
            }

            if (array_key_exists('calendardata', $_POST))
            {
                $calendarData = base64_decode($_POST['calendardata']);
            }
            else
            {
                $result = 'str_MissingParameter';
                $resultParam = 'calendardata';
            }
        }

        if ($result == '')
        {
            $webBrandArray = DatabaseObj::getBrandingFromCode($webBrandCode);
            $result = $webBrandArray['result'];
            $resultParam = $webBrandArray['resultparam'];

            if ($result == '')
            {
                // insert or update the calendar data data within the assets database
                $updateAssetResultArray = DatabaseObj::updateAssetRecord($webBrandArray['calendardataassetid'], 'CALENDAR EVENT SET DATA FOR BRAND ' . $webBrandArray['code'],
                        TPX_ASSETTYPE_CALENDARDATA, $calendarData, 'calendareventsetdata', 0, 0);
                $result = $updateAssetResultArray['result'];
                $resultParam = $updateAssetResultArray['resultparam'];
                $assetID = $updateAssetResultArray['assetid'];

                // if we have no error then update the branding record with the asset information
                if ($result == '')
                {
                    $dbObj = DatabaseObj::getGlobalDBConnection();
                    if ($dbObj)
                    {
                        if ($stmt = $dbObj->prepare('UPDATE `BRANDING` SET `calendardataassetid` = ?, `calendardataassetversion` = ? WHERE `id` = ?'))
                        {
                            if ($stmt->bind_param('isi', $assetID, $calendarDataVersion, $webBrandArray['id']))
                            {
                                if (! $stmt->execute())
                                {
                                    // could not execute statement
                                    $result = 'str_DatabaseError';
                                    $resultParam = 'uploadCalendarData execute ' . $dbObj->error;
                                }
                            }
                            else
                            {
                                // could not bind parameters
                                $result = 'str_DatabaseError';
                                $resultParam = 'uploadCalendarData bind ' . $dbObj->error;
                            }

                            $stmt->free_result();
                            $stmt->close();
                            $stmt = null;
                        }
                        else
                        {
                            // could not prepare statement
                            $result = 'str_DatabaseError';
                            $resultParam = 'uploadCalendarData prepare ' . $dbObj->error;
                        }

                        $dbObj->close();
                    }
                    else
                    {
                        // could not open database connection
                        $result = 'str_DatabaseError';
                        $resultParam = 'uploadCalendarData connect ' . $dbObj->error;
                    }
                }
            }
        }

        if ($result == '')
        {
            // if online is switched on for this system, generate a ONLINEASSETPUSH task so that the data is transmitted to online
            if ($gConstants['optiondesol'])
            {
                $taskInfo = DatabaseObj::getTask('TAOPIX_ONLINEASSETPUSH');
                if ($taskInfo['result'] == '')
                {
                    $eventResultArray = DatabaseObj::createEvent('TAOPIX_ONLINEASSETPUSH', '', '', $webBrandCode, $taskInfo['nextRunTime'], 0, $assetID, TPX_APPLICATION_FILE_TYPE_CALENDARDATA, 'Version: ' . $calendarDataVersion, '', '', '', '', '', 0, 0, 0, '', '', 0);
                }
            }
        }

        $resultArray['result'] = $result;
        $resultArray['resultparam'] = $resultParam;
        $resultArray['apiversion'] = $apiVersion;
        $resultArray['languageCode'] = $languageCode;

        return $resultArray;
    }


    static function uploadSystemDataInit()
    {
        global $gConstants;

        $resultArray = self::decodeCreatorCommandParams();

        $apiVersion = (int)UtilsObj::getPOSTParam('version', '1');
        $languageCode = UtilsObj::getPOSTParam('langcode', $gConstants['defaultlanguagecode']);
        $webBrandCode = UtilsObj::getPOSTParam('webbrandcode');

        if ($webBrandCode != '')
        {
            $webBrandPath = $webBrandCode . '/';
        }
        else
        {
            $webBrandPath = '';
        }

        $resultArray['apiversion'] = $apiVersion;
        $resultArray['languagecode'] = $languageCode;
        $resultArray['brandcode'] = $webBrandCode;
        $resultArray['brandpath'] = $webBrandPath;

        return $resultArray;
    }

    /**
    * Processes and updates the database with the meta-data supplied by TAOPIX™ Builder
    *
    * @static
    *
    * @return array
    *   the result array will contain the response to be echo'd back to the calling application
    *
    * @author Kevin Gale
    * @since Version 1.0.0
    */
    static function uploadApplicationFilesUpdate($itemType, $sourcePathSuffix, $destRootPath)
    {
        global $ac_config;
        global $gConstants;

        $result = '';
        $resultParam = '';
        $itemCount = 0;
        $itemRef = '';
        $itemAppVersion = '';
        $itemCategoryName = '';
        $itemFileName = '';
        $itemName = '';
        $itemDateModified = '';
        $itemHiddenFromuser = '';
        $itemProducts = '';
        $itemThemes = '';
        $itemEncrypted = '';
        $itemSize = '';
        $itemChecksum = '';
        $itemHasFPO = '';
        $itemHasPreview = '';
        $webBrandCode = '';
        $tempDestPath = '';
        $error = false;

        $webBrandCode = UtilsObj::getPOSTParam('webbrandcode');
        $apiVersion = (int)UtilsObj::getPOSTParam('version', '1');
        $languageCode = UtilsObj::getPOSTParam('langcode', $gConstants['defaultlanguagecode']);


        // first make sure that the authentication parameters are valid
        $dataArray = self::decodeCreatorCommandParams();
        $result = $dataArray['result'];
        $resultParam = $dataArray['resultparam'];

        if ($result == '')
        {
            if (! self::autoUpdatePathValid($destRootPath))
            {
                $result = 'str_InvalidAutoUpdatePath';
            }
            else
            {
                if (array_key_exists('count', $_POST))
                {
                    $itemCount = $_POST['count'];
                    if ($itemCount == '')
                    {
                        $result = 'str_NoItemCount';
                    }
                }
                else
                {
                    $result = 'str_NoItemCount';
                }
            }
        }

        if ($result == '')
        {
            // calculate the root path for the source files
            $sourceFileRootPath = $ac_config['INTERNALSYSTEMUPLOADROOTPATH'] . $sourcePathSuffix . '/';
            if ($webBrandCode != '')
            {
                $sourceFileRootPath .= $webBrandCode . '/';
            }

            // calculate the root path for the destination and attempt to create it if it doesn't exist
            if ($webBrandCode != '')
            {
                $destRootPath .= $webBrandCode . '/';
                if (! UtilsObj::createFolder($destRootPath))
                {
                    $result = 'str_FolderCreationError';
                    $resultParam = $destRootPath;
                }
            }
        }

        if ($result == '')
        {
            for ($i = 1; $i <= $itemCount; $i++)
            {
                $itemCountString = str_pad($i, 2, '0', STR_PAD_LEFT);

                $error = false;

                // item ref
                if (array_key_exists('ref' . $itemCountString, $_POST))
                {
                    $itemRef = $_POST['ref' . $itemCountString];
                }
                else
                {
                    $result = 'str_MissingParameter';
                    $resultParam = 'ref' . $itemCountString;
                    break;
                }

                // item category name
                if (array_key_exists('category' . $itemCountString, $_POST))
                {
                    $itemCategoryName = $_POST['category' . $itemCountString];
                }
                else
                {
                    $result = 'str_MissingParameter';
                    $resultParam = 'category' . $itemCountString;
                    break;
                }

                // item name
                if (array_key_exists('name' . $itemCountString, $_POST))
                {
                    $itemName = $_POST['name' . $itemCountString];
                }
                else
                {
                    $result = 'str_MissingParameter';
                    $resultParam = 'name' . $itemCountString;
                    break;
                }

                // item filename
                if (array_key_exists('filename' . $itemCountString, $_POST))
                {
                    $itemFileName = $_POST['filename' . $itemCountString];
                }
                else
                {
                    $result = 'str_MissingParameter';
                    $resultParam = 'filename' . $itemCountString;
                    break;
                }

                // item date modified
                if (array_key_exists('datemodified' . $itemCountString, $_POST))
                {
                    $itemDateModified = $_POST['datemodified' . $itemCountString];
                }
                else
                {
                    $result = 'str_MissingParameter';
                    $resultParam = 'datemodified' . $itemCountString;
                    break;
                }

                // item hidden from user
                if (array_key_exists('hiddenfromuser' . $itemCountString, $_POST))
                {
                    $itemHiddenFromuser = $_POST['hiddenfromuser' . $itemCountString];
                }
                else
                {
                    $result = 'str_MissingParameter';
                    $resultParam = 'hiddenfromuser' . $itemCountString;
                    break;
                }

                // retrieve the optional parameters
                $itemAppVersion = UtilsObj::getPOSTParam('appversion' . $itemCountString, '');
                $itemProducts = UtilsObj::getPOSTParam('products' . $itemCountString, '*ALL*');
                $itemThemes = UtilsObj::getPOSTParam('themes' . $itemCountString, '*ALL*');
                $itemEncrypted = UtilsObj::getPOSTParam('encrypted' . $itemCountString, 'FALSE');
                $itemSize = (float)UtilsObj::getPOSTParam('size' . $itemCountString, '0'); // we use a float for the file size to avoid integer overflows
                $itemChecksum = UtilsObj::getPOSTParam('checksum' . $itemCountString, '');
                $itemHasFPO = UtilsObj::getPOSTParam('hasfpo' . $itemCountString, 'FALSE');
                $itemHasPreview = UtilsObj::getPOSTParam('haspreview' . $itemCountString, 'FALSE');
                $itemFPOData = UtilsObj::getPOSTParam('fpodata' . $itemCountString, '');
                $itemPreviewData = UtilsObj::getPOSTParam('previewdata' . $itemCountString, '');

                // if the apiversion is newer than 4 then we must base 64 decode the data
                if ($apiVersion > 4)
                {
                    $itemFPOData = base64_decode($itemFPOData);
                    $itemPreviewData = base64_decode($itemPreviewData);
                }


                // at this point we should have the data we need for the asset
                $newFilesTempArray = Array();
                $newFilesArray = Array();
                $tempFilesArray = Array();
                $backupFilesArray = Array();
                $backupFileCount = 0;

                // check to see if the uploaded file exists
                $sourceFilePath = $sourceFileRootPath . $itemRef . '.zip';
                if (! file_exists($sourceFilePath))
                {
                    $result = 'str_MissingFile';
                    $resultParam = $sourceFilePath;
                    $error = true;
                }

                // check to see if we have a file to back up
                if (! $error)
                {
                    $destFilePath = $destRootPath. $itemRef . '.zip';

                    $newFilesTempArray[] = $sourceFilePath;
                    $newFilesArray[] = $destFilePath;

                    // if a file already exists back it up
                    if (file_exists($destFilePath))
                    {
                        $tempDestFilePath = $destRootPath . $itemRef . '_temp.zip';
                        $backupFilesArray[] = $destFilePath;
                        $tempFilesArray[] = $tempDestFilePath;
                    }
                }

                // check for a valid previews path and attempt to create it if it does not exist
                if (! $error)
                {
                    clearstatcache(); // clear the php file status caching to get the latest status

                    $previewParentFilePath = $destRootPath . '_previews/';
                    if (! file_exists($previewParentFilePath))
                    {
                        // if the path does not exist attempt to create it
                        if (! UtilsObj::createFolder($previewParentFilePath))
                        {
                            $result = 'str_FolderCreationError';
                            $resultParam = $previewParentFilePath;
                            $error = true;
                        }
                    }
                }

                // process the fpo
                if (! $error)
                {
                    $destFilePath = $previewParentFilePath . $itemRef . '.fpo';
                    if ($itemHasFPO == 'TRUE')
                    {
                        // write the fpo data to the temporary location
                        $sourceFilePath = $sourceFileRootPath . $itemRef . '.fpo';
                        $newFilesTempArray[] = $sourceFilePath;

                        UtilsObj::deleteFile($sourceFilePath);
                        UtilsObj::writeTextFile($sourceFilePath, $itemFPOData);
                        if (! $sourceFilePath)
                        {
                            $result = 'str_MissingFile';
                            $resultParam = $sourceFilePath;
                            $error = true;
                        }

                        $newFilesArray[] = $destFilePath;
                    }
                    if ((! $error) && (file_exists($destFilePath)))
                    {
                        $backupFilesArray[] = $destFilePath;
                        $tempFilesArray[] = $destFilePath . '_temp';
                    }
                }

                // process the preview
                if (! $error)
                {
                    $destFilePath = $previewParentFilePath . $itemRef . '.preview';
                    if ($itemHasPreview == 'TRUE')
                    {
                        // write the fpo data to the temporary location
                        $sourceFilePath = $sourceFileRootPath . $itemRef . '.preview';
                        $newFilesTempArray[] = $sourceFilePath;

                        UtilsObj::deleteFile($sourceFilePath);
                        UtilsObj::writeTextFile($sourceFilePath, $itemPreviewData);
                        if (! $sourceFilePath)
                        {
                            $result = 'str_MissingFile';
                            $resultParam = $sourceFilePath;
                            $error = true;
                        }

                        $newFilesArray[] = $destFilePath;
                    }
                    if ((! $error) && (file_exists($destFilePath)))
                    {
                        $backupFilesArray[] = $destFilePath;
                        $tempFilesArray[] = $destFilePath . '_temp';
                    }
                }

                // backup the files
                if (! $error)
                {
                    $backupFilesCount = count($backupFilesArray);
                    for ($i2 = 0; $i2 < $backupFilesCount; $i2++)
                    {
                        $tempDestPath = $tempFilesArray[$i2];
                        UtilsObj::deleteFile($tempDestPath);

                        if (rename($backupFilesArray[$i2], $tempDestPath))
                        {
                            $error = ! file_exists($tempDestPath);
                        }
                        else
                        {
                            $error = true;
                        }

                        if (! $error)
                        {
                            $backupFileCount++;
                        }
                        else
                        {
                            $result = 'str_FileMoveError';
                            $resultParam = $tempDestPath;
                            $error = true;
                        }
                    }
                }

                if (! $error)
                {
                    // if appApi is version 1 then convert strings to multi-lingual
                    if ($apiVersion == 1)
                    {
                        $itemName = $languageCode." ".$itemName;

                        if($itemCategoryName != "")
                        {
                            $itemCategoryName = $languageCode." ".$itemCategoryName;
                        }
                    }

                    $dbObj = DatabaseObj::getGlobalDBConnection();
                    // at this point we must now update the application files database resetting the priority
                    $dbResultArray = DatabaseObj::uploadApplicationFilesUpdate($dbObj, '', $itemType, $itemRef, $itemAppVersion, 0, '', $itemCategoryName, $itemName, '', $itemProducts, $itemThemes,
                                $itemFileName, $itemDateModified, $itemEncrypted, 0, '', '', $itemSize, $itemChecksum, $itemHasFPO, $itemHasPreview, 'FALSE', $itemHiddenFromuser, 'FALSE', 'FALSE', $webBrandCode);

                    $dbObj->close();

                    $result = $dbResultArray['result'];
                    $resultParam = $dbResultArray['resultparam'];
                    if ($result == '')
                    {
                        // we can now finally attempt to move the files to their final correct location
                        for ($i2 = 0; $i2 < count($newFilesArray); $i2++)
                        {
                            clearstatcache();

                            $destPath = $newFilesArray[$i2];
                            UtilsObj::deleteFile($destPath);

                            if (rename($newFilesTempArray[$i2], $destPath))
                            {
                                $error = ! file_exists($destPath);
                            }
                            else
                            {
                                $error = true;
                            }

                            if ($error)
                            {
                                $result = 'str_FileMoveError';
                                $resultParam = $destPath;
                                break;
                            }
                        }

                        // if no error occurred we can remove any temporary files
                        if (! $error)
                        {
                            for ($i2 = 0; $i2 < count($tempFilesArray); $i2++)
                            {
                                UtilsObj::deleteFile($tempFilesArray[$i2]);
                            }
                        }
                    }
                    else
                    {
                        $error = true;
                    }
                }

                // if we have an error delete the uploaded / thumbnail / preview files and restore the other files before we exit
                if ($error)
                {
                    for ($i = 0; $i < count($newFilesTempArray); $i++)
                    {
                        UtilsObj::deleteFile($newFilesTempArray[$i]);
                    }

                    for ($i = 0; $i < $backupFileCount; $i++)
                    {
                        rename($tempFilesArray[$i], $backupFilesArray[$i]);
                    }

                    break;
                }
                else
                {
                    if ($gConstants['optiondesol'] && $itemType != TPX_APPLICATION_FILE_TYPE_FRAME)
                    {
                        $taskInfo = DatabaseObj::getTask('TAOPIX_ONLINEASSETPUSH');
                        if ($taskInfo['result'] == '')
                        {
                            $eventResultArray = DatabaseObj::createEvent('TAOPIX_ONLINEASSETPUSH', '', '', $webBrandCode, $taskInfo['nextRunTime'], 0, $dbResultArray['recordid'], $itemType, '', '', '', '', '', '', 0, 0, 0, '', '', 0);
                        }
                    }
                }
            }
        }

        $resultArray['result'] = $result;
        $resultArray['resultparam'] = $resultParam;
        $resultArray['apiversion'] = $apiVersion;
        $resultArray['languageCode'] = $languageCode;

        return $resultArray;
    }

    static function uploadMasksUpdate()
    {
        global $ac_config;

        return self::uploadApplicationFilesUpdate(TPX_APPLICATION_FILE_TYPE_MASK, 'Masks', $ac_config['INTERNALAPPLICATIONMASKSROOTPATH']);
    }

    static function uploadBackgroundsUpdate()
    {
        global $ac_config;

        return self::uploadApplicationFilesUpdate(TPX_APPLICATION_FILE_TYPE_BACKGROUND, 'Backgrounds', $ac_config['INTERNALAPPLICATIONBACKGROUNDSROOTPATH']);
    }

    static function uploadScrapbookUpdate()
    {
        global $ac_config;

        return self::uploadApplicationFilesUpdate(TPX_APPLICATION_FILE_TYPE_PICTURE, 'Scrapbook', $ac_config['INTERNALAPPLICATIONSCRAPBOOKPICTURESROOTPATH']);
    }

     static function uploadFramesUpdate()
    {
        global $ac_config;

        return self::uploadApplicationFilesUpdate(TPX_APPLICATION_FILE_TYPE_FRAME, 'Frames', $ac_config['INTERNALAPPLICATIONFRAMESROOTPATH']);
    }

    /**
    * Processes and updates the license key database with the meta-data supplied by TAOPIX™ Builder
    *
    * @static
    *
    * @return array
    *   the result array will contain the response to be echo'd back to the calling application
    *
    * @author Kevin Gale
    * @since Version 1.0.0
    */
    static function uploadLicenseKeysUpdate()
    {
        global $ac_config;
        global $gConstants;

        $result = '';
        $resultParam = '';
        $itemCount = 0;
        $licenseKeyGroupCode = '';
        $licenseKeyFileName = '';
        $licenseKeyFileSize = 0;
        $licenseKeyFileChecksum = '';
        $licenseKeyVersion = '';
        $licenseKeyMaxSizeOfCart = 1;
        $licenseKeyDataVersion = 1;

        $customerName = '';
        $customerAddress1 = '';
        $customerAddress2 = '';
        $customerAddress3 = '';
        $customerAddress4 = '';
        $customerAddressCity = '';
        $customerAddressCounty = '';
        $customerAddressState = '';
        $customerRegionCode = '';
        $customerPostCode = '';
        $customerCountryCode = '';
        $customerEmailAddress = '';
        $customerTelephoneNumber = '';
        $customerContactFirstName = '';
        $customerContactLastName = '';

        $tempDestPath = '';
        $error = false;

        $apiVersion = (int)UtilsObj::getPOSTParam('version', '1');
        $licenseKeyOwnerCompany = UtilsObj::getPOSTParam('ownercompany');
        $languageCode = UtilsObj::getPOSTParam('langcode', 'en');


        // first make sure that the authentication parameters are valid
        $dataArray = self::decodeCreatorCommandParams();
        $result = $dataArray['result'];
        $resultParam = $dataArray['resultparam'];

        if ($result == '')
        {
            if (! self::autoUpdatePathValid($ac_config['INTERNALLICENSEKEYSROOTPATH']))
            {
                $result = 'str_InvalidAutoUpdatePath';
            }
            else
            {
                if (array_key_exists('count', $_POST))
                {
                    $itemCount = $_POST['count'];
                    if ($itemCount == '')
                    {
                        $result = 'str_NoItemCount';
                    }
                }
                else
                {
                    $result = 'str_NoItemCount';
                }
            }
        }

        if ($result == '')
        {
            for ($i = 1; $i <= $itemCount; $i++)
            {
                $itemCountString = str_pad($i, 2, '0', STR_PAD_LEFT);

                // license key group code
                if (array_key_exists('code' . $itemCountString, $_POST))
                {
                    $licenseKeyGroupCode = $_POST['code' . $itemCountString];
                }
                else
                {
                    $result = 'str_MissingParameter';
                    $resultParam = 'code' . $itemCountString;
                    break;
                }

                // license key filename
                if (array_key_exists('filename' . $itemCountString, $_POST))
                {
                    $licenseKeyFileName = $_POST['filename' . $itemCountString];
                }
                else
                {
                    $result = 'str_MissingParameter';
                    $resultParam = 'filename' . $itemCountString;
                    break;
                }

                // license key version
                if (array_key_exists('version' . $itemCountString, $_POST))
                {
                    $licenseKeyVersion = $_POST['version' . $itemCountString];
                }
                else
                {
                    $result = 'str_MissingParameter';
                    $resultParam = 'version' . $itemCountString;
                    break;
                }

                // license key maximum size of cart
                if (array_key_exists('maxorderbatchsize' . $itemCountString, $_POST))
                {
                    $licenseKeyMaxSizeOfCart = $_POST['maxorderbatchsize' . $itemCountString];
                }
                else
                {
                    $result = 'str_MissingParameter';
                    $resultParam = 'maxorderbatchsize' . $itemCountString;
                    break;
                }

                // customer name
                if (array_key_exists('name' . $itemCountString, $_POST))
                {
                    $customerName = $_POST['name' . $itemCountString];
                }
                else
                {
                    $result = 'str_MissingParameter';
                    $resultParam = 'name' . $itemCountString;
                    break;
                }

                // address1
                if (array_key_exists('address1' . $itemCountString, $_POST))
                {
                    $customerAddress1 = $_POST['address1' . $itemCountString];
                }
                else
                {
                    $result = 'str_MissingParameter';
                    $resultParam = 'address1' . $itemCountString;
                    break;
                }

                // address2
                if (array_key_exists('address2' . $itemCountString, $_POST))
                {
                    $customerAddress2 = $_POST['address2' . $itemCountString];
                }
                else
                {
                    $result = 'str_MissingParameter';
                    $resultParam = 'address2' . $itemCountString;
                    break;
                }

                // address3
                if (array_key_exists('address3' . $itemCountString, $_POST))
                {
                    $customerAddress3 = $_POST['address3' . $itemCountString];
                }
                else
                {
                    $result = 'str_MissingParameter';
                    $resultParam = 'address3' . $itemCountString;
                    break;
                }

                // address4
                if (array_key_exists('address4' . $itemCountString, $_POST))
                {
                    $customerAddress4 = $_POST['address4' . $itemCountString];
                }
                else
                {
                    $result = 'str_MissingParameter';
                    $resultParam = 'address4' . $itemCountString;
                    break;
                }

                // city
                if (array_key_exists('city' . $itemCountString, $_POST))
                {
                    $customerAddressCity = $_POST['city' . $itemCountString];
                }
                else
                {
                    $result = 'str_MissingParameter';
                    $resultParam = 'city' . $itemCountString;
                    break;
                }

                // county
                if (array_key_exists('county' . $itemCountString, $_POST))
                {
                    $customerAddressCounty = $_POST['county' . $itemCountString];
                }
                else
                {
                    $result = 'str_MissingParameter';
                    $resultParam = 'county' . $itemCountString;
                    break;
                }

                // state
                if (array_key_exists('state' . $itemCountString, $_POST))
                {
                    $customerAddressState = $_POST['state' . $itemCountString];
                }
                else
                {
                    $result = 'str_MissingParameter';
                    $resultParam = 'state' . $itemCountString;
                    break;
                }

                // regioncode
                if (array_key_exists('regioncode' . $itemCountString, $_POST))
                {
                    $customerRegionCode = $_POST['regioncode' . $itemCountString];
                }
                else
                {
                    // for the moment, don't let the system fall over
                    $customerRegionCode = '';
    //                 $result = 'str_MissingParameter';
    //                 $resultParam = 'postcode' . $itemCountString;
    //                 break;
                }

                // postcode
                if (array_key_exists('postcode' . $itemCountString, $_POST))
                {
                    $customerPostCode = $_POST['postcode' . $itemCountString];
                }
                else
                {
                    $result = 'str_MissingParameter';
                    $resultParam = 'postcode' . $itemCountString;
                    break;
                }

                // country
                if (array_key_exists('country' . $itemCountString, $_POST))
                {
                    $customerCountryCode = $_POST['country' . $itemCountString];
                }
                else
                {
                    $result = 'str_MissingParameter';
                    $resultParam = 'country' . $itemCountString;
                    break;
                }

                // email
                if (array_key_exists('email' . $itemCountString, $_POST))
                {
                    $customerEmailAddress = $_POST['email' . $itemCountString];
                }
                else
                {
                    $result = 'str_MissingParameter';
                    $resultParam = 'email' . $itemCountString;
                    break;
                }

                // telephone
                if (array_key_exists('telephone' . $itemCountString, $_POST))
                {
                    $customerTelephoneNumber = $_POST['telephone' . $itemCountString];
                }
                else
                {
                    $result = 'str_MissingParameter';
                    $resultParam = 'telephone' . $itemCountString;
                    break;
                }

                // contactfname
                if (array_key_exists('contactfname' . $itemCountString, $_POST))
                {
                    $customerContactFirstName = $_POST['contactfname' . $itemCountString];
                }
                else
                {
                    $result = 'str_MissingParameter';
                    $resultParam = 'contactfname' . $itemCountString;
                    break;
                }

                // contactlname
                if (array_key_exists('contactlname' . $itemCountString, $_POST))
                {
                    $customerContactLastName = $_POST['contactlname' . $itemCountString];
                }
                else
                {
                    $result = 'str_MissingParameter';
                    $resultParam = 'contactlname' . $itemCountString;
                    break;
                }

                // retrieve the optional parameters
                $licenseKeyFileSize = (float)UtilsObj::getPOSTParam('size' . $itemCountString, '0'); // we use a float for the file size to avoid integer overflows
                $licenseKeyFileChecksum = UtilsObj::getPOSTParam('checksum' . $itemCountString, '');
                $licenseKeyDataVersion = UtilsObj::getPOSTParam('dataversion'. $itemCountString, 1);

                // at this point we should have the data we need for the license key
                // check to see if the file exists
                $sourceFilePath = $ac_config['INTERNALSYSTEMUPLOADROOTPATH'] . 'LicenseKeys/' . $licenseKeyFileName;
                if (! file_exists($sourceFilePath))
                {
                    $result = 'str_MissingFile';
                    $resultParam = $sourceFilePath;
                    break;
                }

                $destFilePath = $ac_config['INTERNALLICENSEKEYSROOTPATH'] . $licenseKeyFileName;
                $tempDestPath = '';
                if (file_exists($destFilePath))
                {
                    // if a license key already exists temporarily move the file so that we can restore it if an error occurs
                    $tempDestPath = $ac_config['INTERNALLICENSEKEYSROOTPATH'] . 'temp_' . $licenseKeyFileName;
                    if (rename ($destFilePath, $tempDestPath))
                    {
                        $error = ! file_exists($tempDestPath);
                    }
                    else
                    {
                        $error = true;
                    }

                    if ($error)
                    {
                        UtilsObj::deleteFile($sourceFilePath);
                        $result = 'str_FileMoveError';
                        $resultParam = $tempDestPath;
                        break;
                    }
                }

                // at this point we must now update the license keys database
                $licenseKeyUpdateArray = Array();
                $licenseKeyUpdateArray['companycode'] = $licenseKeyOwnerCompany;
                $licenseKeyUpdateArray['groupcode'] = $licenseKeyGroupCode;
                $licenseKeyUpdateArray['filename'] = $licenseKeyFileName;
                $licenseKeyUpdateArray['version'] = $licenseKeyVersion;
                $licenseKeyUpdateArray['maxorderbatchsize'] = $licenseKeyMaxSizeOfCart;
                $licenseKeyUpdateArray['size'] = $licenseKeyFileSize;
                $licenseKeyUpdateArray['checksum'] = $licenseKeyFileChecksum;
                $licenseKeyUpdateArray['priority'] = 0;
                $licenseKeyUpdateArray['dataversion'] = $licenseKeyDataVersion;
                $licenseKeyUpdateArray['companyname'] = $customerName;
                $licenseKeyUpdateArray['address1'] = $customerAddress1;
                $licenseKeyUpdateArray['address2'] = $customerAddress2;
                $licenseKeyUpdateArray['address3'] = $customerAddress3;
                $licenseKeyUpdateArray['address4'] = $customerAddress4;
                $licenseKeyUpdateArray['city'] = $customerAddressCity;
                $licenseKeyUpdateArray['county'] = $customerAddressCounty;
                $licenseKeyUpdateArray['state'] = $customerAddressState;
                $licenseKeyUpdateArray['regioncode'] = $customerRegionCode;
                $licenseKeyUpdateArray['postcode'] = $customerPostCode;
                $licenseKeyUpdateArray['countrycode'] = $customerCountryCode;
                $licenseKeyUpdateArray['email'] = $customerEmailAddress;
                $licenseKeyUpdateArray['telephonenumber'] = $customerTelephoneNumber;
                $licenseKeyUpdateArray['contactfirstname'] = $customerContactFirstName;
                $licenseKeyUpdateArray['contactlastname'] = $customerContactLastName;

                $dbResultArray = DatabaseObj::uploadLicenseKeysUpdate($licenseKeyUpdateArray);
                $result = $dbResultArray['result'];
                $resultParam = $dbResultArray['resultparam'];
                if ($result == '')
                {
                    // we can now finally attempt to move the file to its correct location
                    if (rename ($sourceFilePath, $destFilePath))
                    {
                        $error = ! file_exists($destFilePath);
                    }
                    else
                    {
                        $error = true;
                    }

                    if ($error)
                    {
                        UtilsObj::deleteFile($sourceFilePath);

                        // if the original file was renamed move it back
                        if ($tempDestPath != '')
                        {
                            rename($tempDestPath, $destFilePath);
                        }

                        $result = 'str_FileMoveError';
                        $resultParam = $destFilePath;
                        break;
                    }
                    else
                    {
                        // finally, delete the old file if it was renamed
                        if ($tempDestPath != '')
                        {
                            UtilsObj::deleteFile($tempDestPath);
                        }

                        if ($gConstants['optiondesol'])
                        {
                            $taskInfo = DatabaseObj::getTask('TAOPIX_ONLINEASSETPUSH');
                            if ($taskInfo['result'] == '')
                            {
                                $eventResultArray = DatabaseObj::createEvent('TAOPIX_ONLINEASSETPUSH', '', '', '', $taskInfo['nextRunTime'], 0, $licenseKeyGroupCode, TPX_APPLICATION_FILE_TYPE_LICENSEKEY, '', '', '', '', '', '', 0, 0, 0, '', '', 0);
                            }
                        }
                    }
                }
                else
                {
                    UtilsObj::deleteFile($sourceFilePath);

                    // if the original file was renamed move it back
                    if ($tempDestPath != '')
                    {
                        rename($tempDestPath, $destFilePath);
                        $tempDestPath = '';
                    }
                }
            }
        }

        $resultArray['result'] = $result;
        $resultArray['resultparam'] = $resultParam;
        $resultArray['apiversion'] = $apiVersion;
        $resultArray['languageCode'] = $languageCode;

        return $resultArray;
    }

    /**
    * Processes and updates the client application database with the meta-data supplied by TAOPIX™ Builder
    *
    * @static
    *
    * @return array
    *   the result array will contain the response to be echo'd back to the calling application
    *
    * @author Kevin Gale
    * @since Version 1.0.0
    */
    static function uploadClientsUpdate()
    {
        global $ac_config;

        $result = '';
        $resultParam = '';
        $itemCount = 0;
        $macBuild = '';
        $macArchiveFilename = '';
        $macArchiveFileSize = 0;
        $macArchiveChecksum = '';
        $macArchiveHasPreview = '';
        $macArchivePreviewData = '';
        $macExeFilename = '';
        $win32Build = '';
        $win32ArchiveFilename = '';
        $win32ArchiveFileSize = 0;
        $win32ArchiveChecksum = '';
        $win32ArchiveHasPreview = '';
        $win32ArchivePreviewData = '';
        $win32ExeFilename = '';
        $tempDestPath = '';
        $webBrandCode = '';
        $error = false;

        $apiVersion = (int)UtilsObj::getPOSTParam('version', '1');
        $languageCode = UtilsObj::getPOSTParam('langcode', 'en');


        // first make sure that the authentication parameters are valid
        $dataArray = self::decodeCreatorCommandParams();
        $result = $dataArray['result'];
        $resultParam = $dataArray['resultparam'];

        if ($result == '')
        {
            if (! self::autoUpdatePathValid($ac_config['INTERNALCLIENTSROOTPATH']))
            {
                $result = 'str_InvalidAutoUpdatePath';
            }
            else
            {
                // mac archive name
                if (array_key_exists('macarchivename', $_POST))
                {
                    $macArchiveFilename = $_POST['macarchivename'];
                }
                else
                {
                    $result = 'str_MissingParameter';
                    $resultParam = 'macarchivename';
                }

                if ($macArchiveFilename != '')
                {
                    // mac build
                    if ($result == '')
                    {
                        if (array_key_exists('macbuild', $_POST))
                        {
                            $macBuild = $_POST['macbuild'];
                            if ($macBuild == '')
                            {
                                $result = 'str_MissingParameter';
                                $resultParam = 'macbuild';
                            }
                        }
                        else
                        {
                            $result = 'str_MissingParameter';
                            $resultParam = 'macbuild';
                        }
                    }

                    // mac exe name
                    if ($result == '')
                    {
                        if (array_key_exists('macexename', $_POST))
                        {
                            $macExeFilename = $_POST['macexename'];
                            if ($macExeFilename == '')
                            {
                                $result = 'str_MissingParameter';
                                $resultParam = 'macexename';
                            }
                        }
                        else
                        {
                            $result = 'str_MissingParameter';
                            $resultParam = 'macexename';
                        }
                    }
                }

                // win32 archive name
                if ($result == '')
                {
                    if (array_key_exists('win32archivename', $_POST))
                    {
                        $win32ArchiveFilename = $_POST['win32archivename'];
                    }
                    else
                    {
                        $result = 'str_MissingParameter';
                        $resultParam = 'win32archivename';
                    }
                }

                if ($win32ArchiveFilename != '')
                {
                    // win32 build
                    if ($result == '')
                    {
                        if (array_key_exists('win32build', $_POST))
                        {
                            $win32Build = $_POST['win32build'];
                            if ($win32Build == '')
                            {
                                $result = 'str_MissingParameter';
                                $resultParam = 'win32build';
                            }
                        }
                        else
                        {
                            $result = 'str_MissingParameter';
                            $resultParam = 'win32build';
                        }
                    }


                    // win32 exe name
                    if ($result == '')
                    {
                        if (array_key_exists('win32exename', $_POST))
                        {
                            $win32ExeFilename = $_POST['win32exename'];
                            if ($win32ExeFilename == '')
                            {
                                $result = 'str_MissingParameter';
                                $resultParam = 'win32exename';
                            }
                        }
                        else
                        {
                            $result = 'str_MissingParameter';
                            $resultParam = 'win32exename';
                        }
                    }
                }

                if ($result == '')
                {
                    // retrieve the optional parameters
                    $webBrandCode = UtilsObj::getPOSTParam('webbrandcode');
                    $macArchiveFileSize = (float)UtilsObj::getPOSTParam('macarchivesize', '0'); // we use a float for the file size to avoid integer overflows
                    $macArchiveChecksum = UtilsObj::getPOSTParam('macarchivechecksum', '');
                    $macHasPreview = UtilsObj::getPOSTParam('machaspreview', 'FALSE');
                    $macPreviewData = UtilsObj::getPOSTParam('macpreviewdata', '');
                    $win32ArchiveFileSize = (float)UtilsObj::getPOSTParam('win32archivesize', '0'); // we use a float for the file size to avoid integer overflows
                    $win32ArchiveChecksum = UtilsObj::getPOSTParam('win32archivechecksum', '');
                    $win32HasPreview = UtilsObj::getPOSTParam('win32haspreview', 'FALSE');
                    $win32PreviewData = UtilsObj::getPOSTParam('win32previewdata', '');

                    // if the apiversion is newer than 4 then we must base 64 decode the data
                    if ($apiVersion > 4)
                    {
                        $macPreviewData = base64_decode($macPreviewData);
                        $win32PreviewData = base64_decode($win32PreviewData);
                    }

                    // at this point we should have the data we need
                    $newFilesTempArray = Array();
                    $newFilesArray = Array();
                    $tempFilesArray = Array();
                    $backupFilesArray = Array();
                    $backupFileCount = 0;

                    $sourceFileRootPath = $ac_config['INTERNALSYSTEMUPLOADROOTPATH'] . 'Clients/';
                    $destFileRootPath = $ac_config['INTERNALCLIENTSROOTPATH'];
                    if ($webBrandCode != '')
                    {
                        $sourceFileRootPath .= $webBrandCode . '/';
                        $destFileRootPath .= $webBrandCode . '/';

                        if (! UtilsObj::createFolder($destFileRootPath))
                        {
                            $result = 'str_FolderCreationError';
                            $resultParam = $destFileRootPath;
                            $error = true;
                        }
                    }

                    // check for a valid previews path and attempt to create it if it does not exist
                    if (! $error)
                    {
                        clearstatcache(); // clear the php file status caching to get the latest status

                        $previewParentFilePath = $destFileRootPath . '_previews/';
                        if (! file_exists($previewParentFilePath))
                        {
                            // if the path does not exist attempt to create it
                            if (! UtilsObj::createFolder($previewParentFilePath))
                            {
                                $result = 'str_FolderCreationError';
                                $resultParam = $previewParentFilePath;
                                $error = true;
                            }
                        }
                    }


                    // process the mac archive
                    if ((! $error) && ($macArchiveFilename != ''))
                    {
                        // check to see if the uploaded file exists
                        $sourceFilePath = $sourceFileRootPath . $macArchiveFilename;
                        if (file_exists($sourceFilePath))
                        {
                            // check to see if we have a file to back up
                            $destFilePath = $destFileRootPath . $macArchiveFilename;

                            $newFilesTempArray[] = $sourceFilePath;
                            $newFilesArray[] = $destFilePath;

                            // if a file already exists back it up
                            if (file_exists($destFilePath))
                            {
                                $tempDestFilePath = $destFileRootPath . '_temp' . $macArchiveFilename;
                                $backupFilesArray[] = $destFilePath;
                                $tempFilesArray[] = $tempDestFilePath;
                            }

                            // process the preview
                            $destFilePath = $previewParentFilePath . $macArchiveFilename . '.preview';
                            if ($macHasPreview == 'TRUE')
                            {
                                // write the fpo data to the temporary location
                                $sourceFilePath = $sourceFileRootPath . $macArchiveFilename . '.preview';
                                $newFilesTempArray[] = $sourceFilePath;

                                UtilsObj::deleteFile($sourceFilePath);
                                UtilsObj::writeTextFile($sourceFilePath, $macPreviewData);
                                if (! $sourceFilePath)
                                {
                                    $result = 'str_MissingFile';
                                    $resultParam = $sourceFilePath;
                                    $error = true;
                                }

                                $newFilesArray[] = $destFilePath;
                            }
                            if ((! $error) && (file_exists($destFilePath)))
                            {
                                $backupFilesArray[] = $destFilePath;
                                $tempFilesArray[] = $destFilePath . '_temp';
                            }
                        }
                        else
                        {
                            $result = 'str_MissingFile';
                            $resultParam = $sourceFilePath;
                            $error = true;
                        }
                    }


                    // process the win32 archive
                    if ((! $error) && ($win32ArchiveFilename != ''))
                    {
                        // check to see if the uploaded file exists
                        $sourceFilePath = $sourceFileRootPath . $win32ArchiveFilename;
                        if (file_exists($sourceFilePath))
                        {
                            // check to see if we have a file to back up
                            $destFilePath = $destFileRootPath . $win32ArchiveFilename;

                            $newFilesTempArray[] = $sourceFilePath;
                            $newFilesArray[] = $destFilePath;

                            // if a file already exists back it up
                            if (file_exists($destFilePath))
                            {
                                $tempDestFilePath = $destFileRootPath . '_temp' . $win32ArchiveFilename;
                                $backupFilesArray[] = $destFilePath;
                                $tempFilesArray[] = $tempDestFilePath;
                            }

                            // process the preview
                            $destFilePath = $previewParentFilePath . $win32ArchiveFilename . '.preview';
                            if ($win32HasPreview == 'TRUE')
                            {
                                // write the fpo data to the temporary location
                                $sourceFilePath = $sourceFileRootPath . $win32ArchiveFilename . '.preview';
                                $newFilesTempArray[] = $sourceFilePath;

                                UtilsObj::deleteFile($sourceFilePath);
                                UtilsObj::writeTextFile($sourceFilePath, $win32PreviewData);
                                if (! $sourceFilePath)
                                {
                                    $result = 'str_MissingFile';
                                    $resultParam = $sourceFilePath;
                                    $error = true;
                                }

                                $newFilesArray[] = $destFilePath;
                            }
                            if ((! $error) && (file_exists($destFilePath)))
                            {
                                $backupFilesArray[] = $destFilePath;
                                $tempFilesArray[] = $destFilePath . '_temp';
                            }
                        }
                        else
                        {
                            $result = 'str_MissingFile';
                            $resultParam = $sourceFilePath;
                            $error = true;
                        }
                    }

                    // backup the files
                    if (! $error)
                    {
                        $backupFilesCount = count($backupFilesArray);
                        for ($i2 = 0; $i2 < $backupFilesCount; $i2++)
                        {
                            $tempDestPath = $tempFilesArray[$i2];
                            UtilsObj::deleteFile($tempDestPath);

                            if (rename($backupFilesArray[$i2], $tempDestPath))
                            {
                                $error = ! file_exists($tempDestPath);
                            }
                            else
                            {
                                $error = true;
                            }

                            if (! $error)
                            {
                                $backupFileCount++;
                            }
                            else
                            {
                                $result = 'str_FileMoveError';
                                $resultParam = $tempDestPath;
                                $error = true;
                            }
                        }
                    }

                    // process the archives
                    if (! $error)
                    {
                        if ($macArchiveFilename != '')
                        {
                            // at this point we must now update the application build database
                            $dbResultArray = DatabaseObj::uploadClientsUpdate($macBuild, $macArchiveFilename, $macArchiveFileSize, $macArchiveChecksum, $macExeFilename, 0, $macArchiveHasPreview,
                                    '', '', 0, '', '', 0, 'FALSE', $webBrandCode);
                            $result = $dbResultArray['result'];
                            $resultParam = $dbResultArray['resultparam'];

                            if ($result != '')
                            {
                                $error = true;
                            }
                        }

                        if ((! $error) && ($win32ArchiveFilename != ''))
                        {
                            // at this point we must now update the application build database
                            $dbResultArray = DatabaseObj::uploadClientsUpdate('', '', 0, '', '', 0, 'FALSE',
                                    $win32Build, $win32ArchiveFilename, $win32ArchiveFileSize, $win32ArchiveChecksum, $win32ExeFilename, 0, $win32ArchiveHasPreview, $webBrandCode);
                            $result = $dbResultArray['result'];
                            $resultParam = $dbResultArray['resultparam'];

                            if ($result != '')
                            {
                                $error = true;
                            }
                        }

                        if (! $error)
                        {
                            // we can now finally attempt to move the files to their final correct location
                            for ($i = 0; $i < count($newFilesArray); $i++)
                            {
                                clearstatcache();

                                $destPath = $newFilesArray[$i];
                                UtilsObj::deleteFile($destPath);

                                if (rename($newFilesTempArray[$i], $destPath))
                                {
                                    $error = ! file_exists($destPath);
                                }
                                else
                                {
                                    $error = true;
                                }

                                if ($error)
                                {
                                    $result = 'str_FileMoveError';
                                    $resultParam = $destPath;
                                    break;
                                }
                            }

                            // if no error occurred we can remove any temporary files
                            if (! $error)
                            {
                                for ($i = 0; $i < count($tempFilesArray); $i++)
                                {
                                    UtilsObj::deleteFile($tempFilesArray[$i]);
                                }
                            }
                        }
                    }

                    // if we have an error delete the uploaded / thumbnail / preview files and restore the other files before we exit
                    if ($error)
                    {
                        for ($i = 0; $i < count($newFilesTempArray); $i++)
                        {
                            UtilsObj::deleteFile($newFilesTempArray[$i]);
                        }

                        for ($i = 0; $i < $backupFileCount; $i++)
                        {
                            rename($tempFilesArray[$i], $backupFilesArray[$i]);
                        }
                    }
                }
            }
        }

        $resultArray['result'] = $result;
        $resultArray['resultparam'] = $resultParam;
        $resultArray['apiversion'] = $apiVersion;
        $resultArray['languageCode'] = $languageCode;

        return $resultArray;
    }


    /**
    * Return the list of graphics that can be changed dynamically within taopix designer
    *
    * @static
    *
    * @return array
    *   the result array will contain the response to be echo'd back to the calling application
    *
    * @author Kevin Gale
    * @since Version 3.3.0
    */
    static function getDynamicGraphics()
    {
        global $gConstants;

        $resultArray = Array();
        $logoArray = Array();

        $groupCode = UtilsObj::getPOSTParam('groupcode');
        $apiVersion = (int)UtilsObj::getPOSTParam('version', '1');
        $languageCode = UtilsObj::getPOSTParam('langcode', $gConstants['defaultlanguagecode']);
        $origSplashScreenDateModified = UtilsObj::getPOSTParam('splashscreendatemodified', '');
        $origBannerDateModified = UtilsObj::getPOSTParam('bannerdatemodified', '');

        $dbObj = DatabaseObj::getGlobalDBConnection();
        if ($dbObj)
        {
            if ($stmt = $dbObj->prepare('SELECT 0, `lk`.`designersplashscreenstartdate`, `lk`.`designersplashscreenenddate`, `lk`.`designersplashscreenassetid`, `a1`.`datemodified`, `a1`.`data`
                                            FROM `LICENSEKEYS` lk LEFT JOIN `ASSETDATA` `a1` ON `a1`.id = `lk`.`designersplashscreenassetid`
                                            WHERE `lk`.`groupcode` = ?
                                            UNION
                                            SELECT 1, `lk`.`designerbannerstartdate`, `lk`.`designerbannerenddate`, `lk`.`designerbannerassetid`, `a2`.`datemodified`, `a2`.`data`
                                            FROM `LICENSEKEYS` lk LEFT JOIN `ASSETDATA` `a2` ON `a2`.`id` = `lk`.`designerbannerassetid`
                                            WHERE `lk`.`groupcode` = ?'))
            {
                if ($stmt->bind_param('ss', $groupCode, $groupCode))
                {
                    if ($stmt->execute())
                    {
                        $stmt->store_result();

                        if ($stmt->bind_result($type, $startDate, $endDate, $id, $dateModified, $data))
                        {
                            while ($stmt->fetch())
                            {
                                if ($type == 0)
                                {
                                    if ($dateModified == $origSplashScreenDateModified)
                                    {
                                        $data = '';
                                    }
                                }
                                elseif ($type == 1)
                                {
                                    if ($dateModified == $origBannerDateModified)
                                    {
                                        $data = '';
                                    }
                                }

                                $itemArray = Array();
                                $itemArray['type'] = $type;
                                $itemArray['id'] = $id;
                                $itemArray['datemodified'] = $dateModified;
                                $itemArray['data'] = $data;
                                $itemArray['startdate'] = $startDate;
                                $itemArray['enddate'] = $endDate;

                                $logoArray[] = $itemArray;
                            }
                        }
                    }
                }

                $stmt->free_result();
                $stmt->close();
                $stmt = null;
            }

            $dbObj->close();
        }

        $resultArray['apiversion'] = $apiVersion;
        $resultArray['languageCode'] = $languageCode;
        $resultArray['logolist'] = $logoArray;
        $resultArray['serverdatetime'] = DatabaseObj::getServerTimeUTC();

        return $resultArray;
    }

    static function uploadProjectThumbnailsInit()
    {
        $resultArray = Array();
        $projectRefsToCheckArray = explode(",", UtilsObj::getPOSTParam("projectreflist"));
        $dateModifiedsArray = explode(",", UtilsObj::getPOSTParam("datemodifiedlist"));
        $projectRefCount = count($projectRefsToCheckArray);

        //only continue if our arrays are populated, have matched counts and do not go over the desktop maximum cart size
        //otherwise we have bad data
        if (($projectRefCount === count($dateModifiedsArray)) && ($projectRefCount <= TPX_DESKTOP_CART_MAX_ITEMS) && ($projectRefCount > 0))
        {
            $existingThumbnailRecordResult = DatabaseObj::getUnneededThumbnailsForUpload($projectRefsToCheckArray, $dateModifiedsArray);

            if ($existingThumbnailRecordResult['error'] === "")
            {
                $existingThumbnailRecords = $existingThumbnailRecordResult['projects'];
                $existingThumbnailRecordCount = count($existingThumbnailRecords);

                if ($existingThumbnailRecordCount > 0)
                {
                    //return the passed projectrefs that don't have up to date database records
                    $resultArray = array_diff($projectRefsToCheckArray, $existingThumbnailRecords);
                }
                else
                {
                    //we have no records for the requested projects so return the full passed list to upload
                    $resultArray = $projectRefsToCheckArray;
                }

                //mark any existing records as unavailable to prevent out of date thumbs being displayed
                DatabaseObj::markDesktopProjectThumbnailsUnavailable($resultArray);
            }

        }

        return $resultArray;
    }

    static function uploadProjectThumbnails()
    {
        $resultArray = UtilsObj::getReturnArray();

        $thumbFilePath = '';
        $recordDataArray = Array();

        $thumbnailCount = UtilsObj::getPOSTParam('thumbcount', 0);

        if ($thumbnailCount > 0)
        {
            // check to see if the thumbnail data has been provided as a file
            if (array_key_exists('thumbdata', $_FILES))
            {
                $thumbFileDataArray = $_FILES['thumbdata'];

                // check for a temporary file path
                $thumbFilePath = $thumbFileDataArray['tmp_name'];

                if ($thumbFilePath != '')
                {
                    // make sure the data was uploaded without any errors
                    if ($thumbFileDataArray['error'] == 0)
                    {
                        // attempt to open the temporary file
                        $fp = @fopen($thumbFilePath, 'rb');
                        if (! $fp)
                        {
                            // the temporary file could not be opened so we cannot continue
                            $thumbnailCount = 0;
                        }
                    }
                    else
                    {
                        // an error occurred while uploading the thumbnail data so we cannot continue
                        $thumbnailCount = 0;
                    }
                }
                else
                {
                    // we do not have a temporary file path so we cannot continue
                    $thumbnailCount = 0;
                }
            }
        }

        // write the thumbnail data to the server
        for ($i = 0; $i < $thumbnailCount; $i++)
        {
            $thumbData = '';

            //read all of the data from the file
            $thumbRef = substr(fgets($fp), 0, -1);
            $projectDateModified = substr(fgets($fp), 0, -1);
            $thumbDataLength = (int) substr(fgets($fp), 0, -1);
            $thumbData = fread($fp, $thumbDataLength);
            //skip over the seperator using fread
            $skip = fread($fp, 1);

            if ($thumbData != '')
            {
                $theFilename = UtilsObj::getFullDesktopProjectThumbnailPath($thumbRef);

                //create the path and text file for the thumbnail
                UtilsObj::createAllFolders(substr($theFilename, 0, strrpos($theFilename, "/")));
                $writeResult = UtilsObj::writeTextFile($theFilename, $thumbData);

                //only create/update the database record if the thumbnail was succesfully written
                if ($writeResult === True)
                {
                    $recordDataArray[] = array("projectref" => $thumbRef, "projectdatemodified" => $projectDateModified);
                }
            }
        }

        //if we have any succesful uploads we need to update the database with the new details
        if (count($recordDataArray) > 0)
        {
            $groupCode = UtilsObj::getPOSTParam('code');
            $resultArray = DatabaseObj::insertDesktopProjectThumbnailRecords($recordDataArray, $groupCode);
        }

        return $resultArray;
    }

    /**
     * Build and save designer JSON metadata file to temporary location
     * @param taopixDB $pDBObj A Database object
     * @param string $pCollectionCode  The collection code to build the metadata file for
     * @param string $pCollectionVersion The data version to build the path with
     * @return array Standard taopix return array with data keys sourcepath, destpath, datauid and backuptemppath
     */
    static function buildDesignerJSONMetadataFile($pDBObj, $pCollectionCode, $pCollectionVersion)
    {
        $resultArray = UtilsObj::getReturnArray();
        $error = '';
        $errorParam = '';
        $resultArray['data']['backuptemppath'] = '';
        $resultArray['data']['sourcepath'] = '';
        $resultArray['data']['destpath'] = '';
        $resultArray['data']['datauid'] = '';

        $metadataDatabaseArray = DatabaseObj::getCollectionDataForJSONMetadataFile($pDBObj, $pCollectionCode);

        if ($metadataDatabaseArray['error'] == '')
        {
            $infoMetadataArray = Array("publishversion" => TPX_DESKTOP_PUBLISHVERSION_LIVEMETADATAV1, "datecreated" => date("Y-m-d H:i:s"));
            $metadataArrayStructure = Array("info" => $infoMetadataArray, "collection" => $metadataDatabaseArray['data']);
            $metadataJSONString = json_encode($metadataArrayStructure);

            if ($metadataJSONString !== false)
            {
                $compressedMetadataToOutput = bzcompress($metadataJSONString) . "TPXSOH" . strlen($metadataJSONString) . "TPXEOF";

                $productCollectionResourceFolderPath = UtilsObj::getProductCollectionResourceFolderPath($pCollectionCode, $pCollectionVersion);

                // We should always have the folder from an earlier operation but verify just in case
                if (! file_exists($productCollectionResourceFolderPath))
                {
                    if (! UtilsObj::createAllFolders($productCollectionResourceFolderPath))
                    {
                        $error = 'str_FolderCreationError';
                        $errorParam = $productCollectionResourceFolderPath;
                    }
                }

                if ($error === '')
                {
                    $destFilePath = $productCollectionResourceFolderPath . DIRECTORY_SEPARATOR . $pCollectionCode . '.ddd';
                    $sourceFilePath = $productCollectionResourceFolderPath . DIRECTORY_SEPARATOR . $pCollectionCode . '_newtemp.ddd';

                    UtilsObj::deleteFile($sourceFilePath);
                    $writeSuccess = UtilsObj::writeTextFile($sourceFilePath, $compressedMetadataToOutput);

                    if (! $writeSuccess)
                    {
                        $error = 'str_MissingFile';
                        $errorParam = $sourceFilePath;
                    }
                    else
                    {
                        // if we have a pre-existing file temporarily back it up so we can roll back in the event of a failure
                        if (file_exists($destFilePath))
                        {
                            $tempDestPath = $productCollectionResourceFolderPath . DIRECTORY_SEPARATOR . $pCollectionCode . '_temp.ddd';
                            UtilsObj::deleteFile($tempDestPath);

                            if (rename($destFilePath, $tempDestPath))
                            {
                                if (! file_exists($tempDestPath))
                                {
                                    $error = 'str_FileMoveError';
                                    $errorParam = $tempDestPath;
                                }
                            }
                            else
                            {
                                $error = 'str_FileMoveError';
                                $errorParam = $tempDestPath;
                            }

                            $resultArray['data']['backuptemppath'] = $tempDestPath;
                        }
                    }

                    if ($error === '')
                    {
                        // Verify that we have written and compressed a valid metadata file
                        if (! self::verifyDesignerJSONMetadataFile($sourceFilePath))
                        {
                            $error = 'str_ErrorInvalidZipFile';
                            $errorParam = $sourceFilePath;
                        }
                    }

                    if ($error === '')
                    {
                        $resultArray['data']['sourcepath'] = $sourceFilePath;
                        $resultArray['data']['destpath'] = $destFilePath;
                        $resultArray['data']['datauid'] = self::generateDataUID(TPX_DESKTOP_PUBLISHVERSION_LIVEMETADATAV1, $compressedMetadataToOutput);
                    }
                }
            }
            else
            {
                $error = 'str_MissingFile';
                $errorParam = 'Unable to create JSON string';
            }
        }
        else
        {
            $error = $metadataDatabaseArray['error'];
            $errorParam = $metadataDatabaseArray['errorparam'];
        }

        $resultArray['error'] = $error;
        $resultArray['errorparam'] = $errorParam;

        return $resultArray;
    }

    /**
     * Verifies that the metadata file stored at the given path can be decompressed into valid json
     *
     * @param string $pMetadataFilePath The path pointing to the metadata file to verify
     * @return boolean containing whether it is valid compressed json
     */
    static function verifyDesignerJSONMetadataFile($pMetadataFilePath)
    {
        $result = false;
        $metadataFileContents = file_get_contents($pMetadataFilePath);

        if ($metadataFileContents !== false)
        {
            // pull out the bzip portion of the file
            $startOfHeaderMarkerPosition = strpos($metadataFileContents, 'TPXSOH');
            $compressedData = substr($metadataFileContents, 0, $startOfHeaderMarkerPosition);

            $decompressedMetadata = bzdecompress($compressedData);

            if ($decompressedMetadata !== false)
            {
                $metadataJSON = json_decode($decompressedMetadata);

                if ($metadataJSON !== false)
                {
                    // our metadata file is valid compressed json so set the result to true
                    $result = true;
                }
            }
        }

        return $result;
    }

    /**
     * Generates the Data UID for a product collection resource
     * @param string $pKind The product collection resource kind constant for the UID being generated
     * @param string $pData The data to generate the UID for
     * @return string the datauid for the passed resource
     */
    static function generateDataUID($pKind, $pData)
    {
        return $pKind . '_' . strlen($pData) . '_' . md5($pData);
    }

    /**
     * Generates, stores and returns a token and callback url for use by asset services
     *
     * @param string $pOAuthVersion The OAuth spec to build the redirect URI for
     * @return array standard taopix return array with subkeys token and url in the data key
     */
    static function getAssetServiceRequestInformation($pOAuthVersion)
    {
        $resultArray = UtilsObj::getReturnArray('data');
        $error = '';
        $errorParam = '';
        $url = '';
        $token = '';
        $redirectURI = '';

        if ($pOAuthVersion == TPX_OAUTH_VERSION_1)
        {
            $insertResultArray = AuthenticateObj::createAssetServiceDataRecord(array("success" => TPX_OAUTH_STATUS_WAITING, "code" => "", "token" => ""),
            TPX_AUTHENTICATIONTYPE_ASSETSERVICE, TPX_USER_AUTH_REASON_ASSETSERVICE_OAUTH1LOGIN);
        }
        elseif ($pOAuthVersion == TPX_OAUTH_VERSION_2)
        {
            $insertResultArray = AuthenticateObj::createAssetServiceDataRecord(array("success" => TPX_OAUTH_STATUS_WAITING, "code" => ""),
            TPX_AUTHENTICATIONTYPE_ASSETSERVICE, TPX_USER_AUTH_REASON_ASSETSERVICE_OAUTH2LOGIN);
        }
        else
        {
            $insertResultArray = array('result' => 'str_ErrorClientInvalid', 'resultparam' => '');
        }

        if ($insertResultArray['result'] === '')
        {
            $token = $insertResultArray['authkey'];

            $idResultArray = AuthenticateObj::getAuthenticationDataStoreRecordID($token, TPX_AUTHENTICATIONTYPE_ASSETSERVICE);

            if ($idResultArray['error'] === '')
            {
                $recordID = $idResultArray['id'];
                $url = self::getOauthStatusCacheURL(UtilsObj::getBrandedDisplayUrl(), $token, $recordID);
                self::writeOAuthStatusFile($token, "", $recordID);
            }
            else
            {
                $error = $idResultArray['error'];
                $errorParam = $idResultArray['errorparam'];
            }
        }
        else
        {
            $error = $insertResultArray['result'];
            $errorParam = $insertResultArray['resultparam'];
        }

        if ($pOAuthVersion == TPX_OAUTH_VERSION_1)
        {
            $redirectURI = UtilsObj::getBrandedDisplayUrl() . "AssetServicesCallbacks/OAuth.php?state=" . $token;
        }
        elseif ($pOAuthVersion == TPX_OAUTH_VERSION_2)
        {
            $redirectURI = UtilsObj::getBrandedDisplayUrl() . "AssetServicesCallbacks/OAuth.php";
        }

        $resultArray['data']['token'] = $token;
        $resultArray['data']['checkurl'] = $url;
        $resultArray['data']['redirecturi'] = $redirectURI;
        $resultArray['error'] = $error;
        $resultArray['errorparam'] = $errorParam;

        return $resultArray;
    }

    /**
     * Gets the authcode from the database for the passed state string
     *
     * @param string $pToken The token to search on
     * @return array Standard Taopix result array with code and success status in the data key on code and success subkeys
     */
    static function getAssetServiceAuthCode($pToken)
    {
        $resultArray = UtilsObj::getReturnArray('data');

        $authenticationResultArray = AuthenticateObj::getAuthenticationDataRecord(TPX_AUTHENTICATIONTYPE_ASSETSERVICE, $pToken, true);
        $success = UtilsObj::getArrayParam($authenticationResultArray['data'], 'success', TPX_OAUTH_STATUS_WAITING);

        // we have retrieved a completed status thus clean up the status file
        if ($success > TPX_OAUTH_STATUS_WAITING)
        {
            $idResultArray = AuthenticateObj::getAuthenticationDataStoreRecordID($pToken, TPX_AUTHENTICATIONTYPE_ASSETSERVICE);

            if ($idResultArray['error'] === '')
            {
                self::deleteOAuthStatusFile($pToken, $idResultArray);
            }
        }

        $resultArray['error'] = $authenticationResultArray['result'];
        $resultArray['errorparam'] = $authenticationResultArray['resultparam'];
        $resultArray['data']['code'] = UtilsObj::getArrayParam($authenticationResultArray['data'], 'code', '');
        $resultArray['data']['success'] = $success;

        return $resultArray;
    }

    /**
     * Gets the cache url for the passed branded url and state token
     *
     * @param string $pWebURL The branded URL to build the cache URL for
     * @param string $pToken The Token to build the cache URL for
     * @param int $pRecordID The recordid of the data store record
     * @return string The requested cache URL
     */
    static function getOauthStatusCacheURL($pWebURL, $pToken, $pRecordID)
    {
        return UtilsObj::correctPath($pWebURL) . 'oauthstatus/' . self::getOAuthStatusCacheFileName($pToken, $pRecordID);
    }

    /**
     * Gets the cache filepath for the passed branded url and state token
     *
     * @param string $pToken The Token to build the cache URL for
     * @param int $pRecordID The recordid of the data store record
     * @return string The requested cache filepath
     */
    static function calcOAuthStatusCachePath($pToken, $pRecordID)
	{
		return self::getOAuthStatusCachePath() . self::getOAuthStatusCacheFileName($pToken, $pRecordID);
	}

    /**
     * Gets the filename for the passed branded url and state token
     *
     * @param string $pToken The Token to build the filename for
     * @param int $pRecordID The recordid of the data store record
     * @return string The requested filename
     */
    static function getOAuthStatusCacheFileName($pToken, $pRecordID)
    {
        // we do not want to put the token in the file system as it can be used to retrieve the auth code
        // create an opaque, predictable value for the filename
        // note that we XOR the recordid to somewhat obfuscate it at first glance
        return crc32($pToken) . ($pRecordID ^ 6239631) . '.inf';
    }

    /**
	 * Returns the OAuthStatusCache folder from the config file.
	 *
	 * @global array $ac_config The global config file array.
	 * @return string The path to the OAuth status cache folder with a trailing slash.
	 */
	static function getOAuthStatusCachePath()
	{
		global $ac_config;

		$oauthStatusCachePath = '';

		if (array_key_exists('CONTROLCENTREOAUTHSTATUSCACHEPATH', $ac_config))
		{
			$oauthStatusCachePath = UtilsObj::correctPath($ac_config['CONTROLCENTREOAUTHSTATUSCACHEPATH']);
		}

		return $oauthStatusCachePath;
	}

    /**
     * Write a OAuth cache file to the configured cache path
     *
     * @param string $pState The state code to use for constructing the file name
     * @param string $pText The text to write in the file
     * @param int $pRecordID The record ID of the data store record
     * @return void No return as we cannot react to failure
     */
    static function writeOAuthStatusFile($pState, $pText, $pRecordID)
    {
        // write a cache file for the designer to run head requests against to reduce the overhead of polling the server for a result
        $filePath = self::calcOAuthStatusCachePath($pState, $pRecordID);

        // if we have a file path we write the file based on if one already exists and the parameters provided
        if ($filePath != '')
        {
            // first delete any existing file
            UtilsObj::deleteFile($filePath);

            // attempt up to 10 times to write the cache file
            // if we're unable to write the cache file it is not an issue as the designer will do periodic full queries as well as the head checks
            $retryCount = 10;
            do
            {
                $towrite = $pText;
                if (UtilsObj::writeTextFile($filePath, $towrite))
                {
                    $result = $filePath;
                    $retryCount = 0;
                }
                else
                {
                    // the file could not be written so retry
                    $retryCount--;
                    usleep(10000);
                }
            } while ($retryCount > 0);
        }
    }

    /**
     * Deletes the oauth status file for the passed recordid and state string
     * Also cleans up old status files
     *
     * @param string $pState The state code to use for constructing the file name
     * @param string $pText The text to write in the file
     */
    static function deleteOAuthStatusFile($pState, $pRecordID)
    {
        $filePath = self::calcOAuthStatusCachePath($pState, $pRecordID);

        if ($filePath != '')
        {
            UtilsObj::deleteFile($filePath);
        }

        // clean-up any orphaned status files
        self::cleanUpOrderStatusCachePath();
    }

    /**
     * Deletes any oauth status file over 240 minutes old
     */
    static function cleanUpOrderStatusCachePath()
    {
        // delete any cache files older than 4 hours
        $cacheFolderPath = self::getOAuthStatusCachePath();

        if ($cacheFolderPath != '')
        {
            UtilsObj::deleteOldFiles($cacheFolderPath, 240);
        }
    }

    /**
     * Retrieves the account pages URL for the provided license key group code
     * @param string $pGroupCode The group code to retrieve the license key for
     * @return string The configured account pages URL or an empty string if something has failed
     */
    static function getAccountPagesURL($pGroupCode)
    {
        $overrideAccountPagesURL = '';
        $brandDisplayURL = '';
        $licenseKeyAccountPagesURL = '';
        $brandAccountPagesURL = '';
        $licenseKeyUseDefault = true;
        $brandUseDefault = true;
        $error = '';
        $errorParam = '';

        $dbObj = DatabaseObj::getGlobalDBConnection();

        if ($dbObj)
        {
            $sql = "SELECT `licensekeys`.`usedefaultaccountpagesurl`, `licensekeys`.`accountpagesurl`, `branding`.`usedefaultaccountpagesurl`, `branding`.`accountpagesurl`, `branding`.`displayurl`
                FROM `licensekeys`
                INNER JOIN `branding` ON `licensekeys`.`webbrandcode` = `branding`.`code`
                WHERE `licensekeys`.`groupcode` = ?";

            $stmt = $dbObj->prepare($sql);

            if ($stmt)
            {
                $bindOK = $stmt->bind_param('s', $pGroupCode);

                if ($bindOK)
                {
                    if ($stmt->execute())
                    {
                        if ($stmt->store_result())
                        {
                            if ($stmt->bind_result($licenseKeyUseDefault, $licenseKeyAccountPagesURL, $brandUseDefault, $brandAccountPagesURL, $brandDisplayURL))
                            {
                                while ($stmt->fetch())
                                {
                                    if ($licenseKeyUseDefault == 0)
                                    {
                                        $overrideAccountPagesURL = $licenseKeyAccountPagesURL;
                                    }
                                    else
                                    {
                                        if ($brandUseDefault == 0)
                                        {
                                            $overrideAccountPagesURL = $brandAccountPagesURL;
                                        }
                                        else
                                        {
                                            $overrideAccountPagesURL = $brandDisplayURL;
                                        }
                                    }
                                }
                            }
                            else
                            {
                                // could not bind result
                                $error = 'str_DatabaseError';
                                $errorParam = __FUNCTION__ . ' bindresult ' . $dbObj->error;
                            }
                        }
                        else
                        {
                            // could not store result
                            $error = 'str_DatabaseError';
                            $errorParam = __FUNCTION__ . ' storeresult ' . $dbObj->error;
                        }
                    }
                    else
                    {
                        // could not execute
                        $error = 'str_DatabaseError';
                        $errorParam = __FUNCTION__ . ' execute ' . $dbObj->error;
                    }
                }
                else
                {
                    // could not bindparam
                    $error = 'str_DatabaseError';
                    $errorParam = __FUNCTION__ . ' bindparam ' . $dbObj->error;
                }
            }
            else
            {
                // could not prepare
                $error = 'str_DatabaseError';
                $errorParam = __FUNCTION__ . ' prepare ' . $dbObj->error;
            }
        }
        else
        {
            // could not connect
            $error = 'str_DatabaseError';
            $errorParam = __FUNCTION__ . ' connect ' . $dbObj->error;
        }

        // return only the account pages url as the designer does not need to know the specifics of the error
        return $overrideAccountPagesURL;
    }
}
?>
