<?php

require_once('../Utils/UtilsRoute.php');
require_once(__DIR__ . '/../AdminTaopixOnlineFontLists/AdminTaopixOnlineFontLists_model.php');

use Security\CSPConfigBuilder;
use GuzzleHttp\Client;

class AdminBranding_model
{
    static function getBrandCount()
    {
    	$brandCount = 0;

    	$dbObj = DatabaseObj::getGlobalDBConnection();

        if ($dbObj)
	    {
	        if ($stmt = $dbObj->prepare('SELECT COUNT(`id`) FROM BRANDING'))
	        {

               if ($stmt->bind_result($recordCount))
               {
                   if ($stmt->execute())
                   {
                        $stmt->fetch();
                   }
                }

	            $stmt->free_result();
	            $stmt->close();
            }

            $dbObj->close();
        }

        $brandCount = $recordCount;

        return $brandCount;
    }

    static function brandList()
    {
    	global $gSession, $ac_config;

    	$brandItem = array();
    	$resultArray = array();
    	$typesArray = array();
		$paramArray = array();
		$stmtArray = array();
		$totalCount = 0;

        $start = (isset($_POST['start'])) ? $_POST['start'] : 0;
        $limit = (isset($_POST['limit'])) ? $_POST['limit'] : 100;

		$sortBy = (isset($_POST['sort'])) ? $_POST['sort'] : '';
        $sortDir = (isset($_POST['dir'])) ? $_POST['dir'] : '';
        $searchFields = UtilsObj::getPOSTParam('fields');

	    if ((isset($gSession['userdata'])) && ($gSession['userdata']['companycode'] != ''))
	    {
	    	$companyCode = $gSession['userdata']['companycode'];
	    }
	    else
	    {
	    	$companyCode = (isset($_POST['companyCode'])) ? $_POST['companyCode'] : '';
	    }

	    $smarty = SmartyObj::newSmarty('AdminBranding');
        $defaults = DatabaseObj::getBrandingFromCode('');

	    $dbObj = DatabaseObj::getGlobalDBConnection();
	    if ($dbObj)
	    {
	    	if ($companyCode != '')
	    	{
	    		if ($companyCode == 'GLOBAL')
	    		{
	    			$stmtArray[] = '(`'. 'companycode' .'` = ?)';
					$paramArray[] = '';
					$typesArray[] = 's';
	    		}
	    		else
	    		{
	    			$stmtArray[] = '(`'. 'companycode' .'` LIKE ?)';
					$paramArray[] = '%'. $companyCode .'%';
					$typesArray[] = 's';
	    		}
	    	}

	    	$customSort = '';
    		if ($sortBy != '')
    		{
    			switch ($sortBy)
    			{
    				case 'foldername': $sortBy = 'name '.$sortDir; break;
    				case 'appname': $sortBy = 'applicationname '.$sortDir; break;
    				case 'displayurl': $sortBy = 'displayurl '.$sortDir; break;
    				case 'isactive': $sortBy = 'active '.$sortDir; break;
    			}
    			$customSort = ', '. $sortBy;
    		}

    		if ($searchFields != '')
			{
				$searchQuery = $_POST['query'];
				$selectedfields = explode(',', str_replace('"', "", str_replace("]", "", str_replace("[", "",$_POST['fields']))));

				if ($searchQuery != '')
				{
					foreach ($selectedfields as $value)
					{
						switch ($value)
    					{
    						case 'foldername': $value = 'name'; break;
    						case 'appname': $value = 'applicationname'; break;
    					}
						$stmtArray[] = '(`'.$value.'` LIKE ?)';
						$paramArray[] = '%'.$searchQuery.'%';
						$typesArray[] = 's';
					}
				}
			}

	    	if (count($stmtArray) > 0)
            {
                $stmtArray = ' WHERE (' . join(' OR ', $stmtArray) . ')';
            }
            else
            {
                $stmtArray = '';
            }

			if ($stmt = $dbObj->prepare('SELECT SQL_CALC_FOUND_ROWS `id`, `companycode`, `owner`, `code`, `name`, `applicationname`, `displayurl`, `active` FROM `BRANDING` '. $stmtArray. ' ORDER BY `companycode` '. $customSort . ' LIMIT ' . $limit . ' OFFSET ' . $start ))
			{
				$bindOK = DatabaseObj::bindParams($stmt, $typesArray, $paramArray);
				if ($bindOK)
				{
					$stmt->bind_result($recordID, $brandingCompany, $brandingOwner, $brandingCode, $brandingName, $applicationName, $displayURL, $isActive);
					if ($stmt->execute())
            		{
            			while ($stmt->fetch())
                		{
			 				if ($brandingName == '')
							{
								$brandingName = '<i>' . $smarty->get_config_vars('str_LabelDefault'). '</i>';
							}

        					if ($displayURL == '')
        					{
        	    				$displayURL = '<i>' . UtilsObj::correctPath($defaults['displayurl']) . ($ac_config['WEBBRANDFOLDERNAME'] == '' ? 'Branding' : $ac_config['WEBBRANDFOLDERNAME']) . '/' . $brandingName . '/' . '</i>';
        					}

      				      	$brandItem['recordid'] = "'" . UtilsObj::ExtJSEscape($recordID) . "'";
            				$brandItem['code'] = "'" . UtilsObj::ExtJSEscape($brandingCode) . "'";
      				      	$brandItem['company'] = "'" . UtilsObj::ExtJSEscape($brandingCompany) . "'";
            				$brandItem['foldername'] = "'" . UtilsObj::ExtJSEscape($brandingName) . "'";
            				$brandItem['appname'] = "'" . UtilsObj::ExtJSEscape($applicationName) . "'";
            				$brandItem['displayurl'] = "'" . UtilsObj::ExtJSEscape($displayURL) . "'";
            				$brandItem['isactive'] = "'" . UtilsObj::ExtJSEscape($isActive) . "'";
            				array_push($resultArray, '['.join(',', $brandItem).']');
						}
             		}

					if (($stmt = $dbObj->prepare("SELECT FOUND_ROWS()")) && ($stmt->bind_result($totalCount)))
					{
						if ($stmt->execute())
						{
							$stmt->fetch();
						}
					}
             		$stmt->free_result();
	         		$stmt->close();
	         		$stmt = null;
	        	}
			}
		}
        $dbObj->close();

        $summaryArray = join(',', $resultArray);
        if ($summaryArray != '')
        {
        	$summaryArray = ', ' . $summaryArray;
        }

        echo '[[' . $totalCount . ']' . $summaryArray.']';
        return;
    }


    static function getBrandRootPath($pBrandName)
    {
        global $ac_config;

        return $ac_config['WEBINTERNALROOTPATH'] . 'Branding/' . $pBrandName;
    }

    static function getBrandWebRootPath($pBrandName)
    {
        global $ac_config;

        return $ac_config['WEBINTERNALROOTPATH'] . 'webroot/' . $ac_config['WEBBRANDFOLDERNAME'] . '/' . $pBrandName;
    }

    static function createBrandIndexFile($pBrandCode, $pBrandName)
    {
        global $ac_config;

        $fileCreated = false;

        $brandWebRootPath = self::getBrandWebRootPath($pBrandName);

        // read the original index file from the web root folder
        $origIndexFilePath = $ac_config['WEBINTERNALROOTPATH'] . 'webroot/index.php';

        $origIndexFile = UtilsObj::readTextFile($origIndexFilePath);

        if ($origIndexFile != '')
        {
            // create a new index file containing the brand code
            $startDataPos = strpos($origIndexFile, '$gDefaultSiteBrandingCode = ');

            $brandData = $pBrandCode . "';\nchdir('../../');\n";
            $newIndexFile = substr($origIndexFile, 0, $startDataPos + 29) . $brandData . substr($origIndexFile, $startDataPos + 31);

            $newIndexFilePath = $brandWebRootPath . '/index.php';
			UtilsObj::deleteFile($newIndexFilePath);

            $fileCreated = UtilsObj::writeTextFile($newIndexFilePath, $newIndexFile);
        }

        return $fileCreated;
    }

    static function createBrandPath($pBrandCode, $pBrandName)
    {
        $brandRootPath = self::getBrandRootPath($pBrandName);
        $brandWebRootPath = self::getBrandWebRootPath($pBrandName);

        if (($brandRootPath != '') && ($brandWebRootPath != ''))
        {
			$pathCreated = true;
			$paths = [
				$brandRootPath . '/lang',
				$brandRootPath . '/email',
				$brandRootPath . '/templates',
				$brandWebRootPath,
			];

			$originalUmask = umask();
			umask(0002);

			foreach ($paths as $path)
			{
				if (! mkdir($path, 0775, true))
				{
					$pathCreated = false;
					break;
				}
			}

			if ($pathCreated)
			{
				$pathCreated = self::createBrandIndexFile($pBrandCode, $pBrandName);
			}

			umask($originalUmask);
        }
    }

    static function copyBrandFiles($pBrandCode, $pBrandName)
    {
        global $ac_config;

        $brandWebRootPath = self::getBrandWebRootPath($pBrandName);

		$originalUmask = umask();
		umask(0002);

        UtilsObj::dircopy($ac_config['WEBINTERNALROOTPATH'] . 'webroot/css', $brandWebRootPath . '/css', true);
        UtilsObj::dircopy($ac_config['WEBINTERNALROOTPATH'] . 'webroot/images', $brandWebRootPath . '/images', true);

        umask($originalUmask);
    }

    static function copySSOFiles($pBrandCode, $pBrandName)
    {
        global $ac_config;

        $brandWebRootPath = self::getBrandWebRootPath($pBrandName);

		$originalUmask = umask(0);
		umask(0002);

        UtilsObj::dircopy($ac_config['WEBINTERNALROOTPATH'] . 'templates/sso', $brandWebRootPath . '/sso', true);

        umask($originalUmask);
    }

    static function display()
	{
        return DatabaseObj::getBrandingList();
    }

    static function brandingActivate()
    {
        global $gSession;
        $brandList  = explode(',',$_POST['idlist']);
        $brandCount = count($brandList);
        $result = '';
		$resultParam = '';
		$isActive = $_POST['active'];

        $dbObj = DatabaseObj::getGlobalDBConnection();
		if ($dbObj)
		{
			for ($i = 0; $i < $brandCount; $i++)
			{
				$id = $brandList[$i];
				$brandDataArray = DatabaseObj::getBrandingFromID($id);

				if ($stmt = $dbObj->prepare('UPDATE `BRANDING` SET `active` = ? WHERE `id` = ?'))
            	{
                	if ($stmt->bind_param('ii', $isActive, $id))
                	{
                    	if ($stmt->execute())
                    	{
                        	if ($isActive == 1)
                        	{
                            	DatabaseObj::updateActivityLog($gSession['ref'], 0, $gSession['userid'], $gSession['userlogin'], $gSession['username'], 0,
                                    'ADMIN', 'BRANDING-DEACTIVATE', $id . ' ' . $brandDataArray['name'], 1);
                        	}
                        	else
                        	{
                            	DatabaseObj::updateActivityLog($gSession['ref'], 0, $gSession['userid'], $gSession['userlogin'], $gSession['username'], 0,
                                    'ADMIN', 'BRANDING-ACTIVATE', $id . ' ' . $brandDataArray['name'], 1);
                        	}
                    	}
                    	else
						{
							$result = 'str_DatabaseError';
							$resultParam = 'brandingActivate execute ' . $dbObj->error;
						}
                	}
                	else
					{
						$result = 'str_DatabaseError';
						$resultParam = 'brandingActivate bind ' . $dbObj->error;
					}
                	$stmt->free_result();
                	$stmt->close();
            	}
            	else
				{
					$result = 'str_DatabaseError';
					$resultParam = 'brandingActivate prepare ' . $dbObj->error;
				}
			}
			$dbObj->close();
		}
		else
		{
			$result = 'str_DatabaseError';
			$resultParam = 'brandingActivate connect ' . $dbObj->error;
		}

        if ($result == '')
		{
			echo "{'success':'true', 'msg':'" . '' . "' }";
		}
		else
		{
			echo '{"success":false,	"msg":"' . $resultParam . '"}';
        }
		return;
    }


    static function brandingAdd()
	{
		global $ac_config;
		global $gSession;
		global $gConstants;

		$result = '';
		$resultParam = '';
		$recordID = 0;
		$addBrands = true;
		$brandCount = self::getBrandCount();

		if ($gConstants['optionms'])
		{
			$owner =  UtilsObj::getPOSTParam('productionsite');
		}
		else
		{
			$owner = '';
		}

		$brandingCode = strtoupper(UtilsObj::getPOSTParam('code'));
		$brandingName = UtilsObj::getPOSTParam('name');
		$brandingApplicationName = UtilsObj::escapeInputForHTML(UtilsObj::getPOSTParam('applicationname'));
		$brandingDisplayURL = UtilsObj::getValidUrl(UtilsObj::getPOSTParam('displayurl'));
		$brandingWebURL = UtilsObj::getValidUrl(UtilsObj::getPOSTParam('weburl'));
		$mainWebsiteUrl = UtilsObj::getValidUrl(UtilsObj::getPOSTParam('mainwebsiteurl'));
		$macDownloadUrl = UtilsObj::getValidUrl(UtilsObj::getPOSTParam('macdownloadurl'));
		$win32DownloadUrl = UtilsObj::getValidUrl(UtilsObj::getPOSTParam('win32downloadurl'));

		$onlineDesignerURL = UtilsObj::getValidUrl(UtilsObj::getPOSTParam('onlinedesignerurl'));
		$onlineUIURL = UtilsObj::getValidUrl(UtilsObj::getPOSTParam('onlineuiurl'));
        $onlineAPIURL = UtilsObj::getValidUrl(UtilsObj::getPOSTParam('onlineapiurl'));
		$onlineAppKeyEntropyValue = (UtilsObj::getPOSTParam('onlineappkeyentropyvalue'));
		$onlineAboutURL = UtilsObj::getValidUrl(UtilsObj::getPOSTParam('onlineabouturl'));
        $onlineHelpURL = UtilsObj::getValidUrl(UtilsObj::getPOSTParam('onlinehelpurl'));
        $onlineTermsAndConditionsURL = UtilsObj::getValidUrl(UtilsObj::getPOSTParam('onlinetermsandconditionsurl'));
		$onlineDesignerLogoutURL = UtilsObj::getValidUrl(UtilsObj::getPOSTParam('onlinedesignerlogouturl'));
		$onlineDesingerSignInRegisterPromptDelay = UtilsObj::getPOSTParam('nagdelay');
		$onlineDataRetentionPolicy = UtilsObj::getPOSTParam('onlinedataretentionpolicy');

		$supportTelephone = UtilsObj::getPOSTParam('supporttelephonenumber');
		$supportEmail = UtilsObj::getPOSTParam('supportemailaddress');
        $shareByEmailMethod = UtilsObj::getPOSTParam('sharebyemailmethod');
        $shareHideBranding = UtilsObj::getPOSTParam('sharehidebranding');
        $previewDomainURL = UtilsObj::getPOSTParam('previewdomainurl');
        $registerUsingEmail = UtilsObj::getPOSTParam('registerusingemail');
		$brandingUseDefaultPaymentMethods = UtilsObj::getPOSTParam('usedefaultpaymentmethods');
		$brandingPaymentMethods = UtilsObj::getPOSTParam('paymentmethods');
		$brandingPaymentIntegration = UtilsObj::getPOSTParam('paymentintegration');
        $allowGiftCards = UtilsObj::getPOSTParam('allowgiftcards');
		$allowVouchers = UtilsObj::getPOSTParam('allowvouchers');
		$brandingIsActive = UtilsObj::getPOSTParam('isactive');
		$brandingPreviewLicenseKey = UtilsObj::getPOSTParam('previewlicensekey');
		$brandingGoogleCode = UtilsObj::getPOSTParam('googlecode');
		$brandingGoogleUserIDTracking = UtilsObj::getPOSTParam('googleuseridtracking');
		$brandingGoogleTagManagerOnlineCode = UtilsObj::getPOSTParam('googletagmanageronlinecode');
		$brandingGoogleTagManagerCCCode = UtilsObj::getPOSTParam('googletagmanagercccode');

		// Remove any invalid characters from previewlicensekey
		if (preg_match('/([^a-zA-Z0-9\/]+)/', $brandingPreviewLicenseKey))
		{
			$brandingPreviewLicenseKey = preg_replace('/([^a-zA-Z0-9\/]+)/', '', $brandingPreviewLicenseKey);
		}

		// SMTP settings moved from config to constants table in database
		$smtpAuth = UtilsObj::getPOSTParam('smtpauth');
		$useDefaultEmailSettings = UtilsObj::getPOSTParam('usedefaultemailsettings');

		// check to see if global email settings are to be used
		if ($useDefaultEmailSettings != 1)
		{
			$useDefaultEmailSettings = 0;
		}

		$smtpAddress = UtilsObj::getPOSTParam('smtpaddress');
		$smtpPort = UtilsObj::getPOSTParam('smtpport');
		$smtpAuthUser = UtilsObj::getPOSTParam('smtpauthuser');
		$smtpAuthPass = UtilsObj::getPOSTParam('_smtpauthpass');
        $smtpType = UtilsObj::getPOSTParam('smtptype');
		$smtpSysFromName = UtilsObj::getPOSTParam('smtpsysfromname');
		$smtpSysFromAddress = UtilsObj::getPOSTParam('smtpsysfromaddress');
		$smtpReplyName = UtilsObj::getPOSTParam('smtpreplyname');
		$smtpReplyAddress = UtilsObj::getPOSTParam('smtpreplyaddress');

		$smtpAdminName = UtilsObj::FormatEmailNameSettings($_POST['smtpadminname']);
		$smtpAdminAddress = UtilsObj::FormatEmailSettings($_POST['smtpadminaddress']);
		$smtpProdName = UtilsObj::FormatEmailNameSettings($_POST['smtpprodname']);
		$smtpProdAddress = UtilsObj::FormatEmailSettings($_POST['smtpprodaddress']);
		$smtpOrderConfName = UtilsObj::FormatEmailNameSettings($_POST['smtporderconfname']);
		$smtpOrderConfAddress = UtilsObj::FormatEmailSettings($_POST['smtporderconfaddress']);
		$smtpSaveOrderName = UtilsObj::FormatEmailNameSettings($_POST['smtpsaveordername']);
		$smtpSaveOrderAddress = UtilsObj::FormatEmailSettings($_POST['smtpsaveorderaddress']);
		$smtpShippingName = UtilsObj::FormatEmailNameSettings($_POST['smtpshippingname']);
		$smtpShippingAddress = UtilsObj::FormatEmailSettings($_POST['smtpshippingaddress']);
		$smtpNewAccountName = UtilsObj::FormatEmailNameSettings($_POST['smtpnewaccountname']);
		$smtpNewAccountAddress = UtilsObj::FormatEmailSettings($_POST['smtpnewaccountaddress']);
		$smtpResetPasswordName = UtilsObj::FormatEmailNameSettings($_POST['smtpresetpasswordname']);
		$smtpResetPasswordAddress = UtilsObj::FormatEmailSettings($_POST['smtpresetpasswordaddress']);
		$smtpOrderUploadedName = UtilsObj::FormatEmailNameSettings($_POST['smtporderuploadedname']);
		$smtpOrderUploadedAddress = UtilsObj::FormatEmailSettings($_POST['smtporderuploadedaddress']);

		$smtpAdminActive = UtilsObj::getPOSTParam('smtpadminactive');
		$smtpProdActive = UtilsObj::getPOSTParam('smtpproductionactive');
		$smtpOrderConfActive = UtilsObj::getPOSTParam('smtporderconfirmationactive');
		$smtpSaveOrderActive = UtilsObj::getPOSTParam('smtpsaveorderactive');
		$smtpShippingActive = UtilsObj::getPOSTParam('smtpshippingactive');
		$smtpNewAccountActive = UtilsObj::getPOSTParam('smtpnewaccountactive');
		$smtpResetPasswordActive = UtilsObj::getPOSTParam('smtpresetpasswordactive');
		$smtpOrderUploadedActive = UtilsObj::getPOSTParam('smtporderuploadedactive');
		$companyCode = '';

		$previewExpire= UtilsObj::getPOSTParam('previewExpire');
		$previewExpireDays= UtilsObj::getPOSTParam('previewExpireDays');
		if ($previewExpire == 0)
		{
			$previewExpireDays = 1;
		}

		$useMultiLineWorkflow = UtilsObj::getPOSTParam('usemultilinebasketworkflow');

		$imageScalingBefore = UtilsObj::getPOSTParam('imagescalingbefore');
		$imageScalingBeforeEnabled = UtilsObj::getPOSTParam('imagescalingbeforeenabled');
		$imageScalingAfter = UtilsObj::getPOSTParam('imagescalingafter');
		$imageScalingAfterEnabled = UtilsObj::getPOSTParam('imagescalingafterenabled');

        // make sure that the imagescaling value is set and is greater than 0 and less thant 999.99
        if (($imageScalingBefore == '') || ($imageScalingBefore < 0) || ($imageScalingBefore > 999.99))
        {
			$imageScalingBefore = 0.0;
        }

        // make sure that the imagescaling value is set and is greater than 0 and less thant 999.99
        if (($imageScalingAfter == '') || ($imageScalingAfter < 0) || ($imageScalingAfter > 999.99))
        {
			$imageScalingAfter = 0.0;
        }

		$shuffleLayout = UtilsObj::getPOSTParam('shufflelayout', 0);
		$showShuffleLayoutOptions = UtilsObj::getPOSTParam('showshufflelayoutoptions', 0);

		$onlineEditorMode = UtilsObj::getPOSTParam('onlineeditormode', TPX_ONLINE_EDITOR_MODE_EASY);
		$enableSwitchingEditor = UtilsObj::getPOSTParam('enableswitchingeditor', 0);

		$automaticallyApplyPerfectlyClear = UtilsObj::getPOSTParam('automaticallyapplyperfectlyclear');
		$allowUsersToTogglePerfectlyClear = UtilsObj::getPOSTParam('toggleperfectlyclear');

		// url for the link in the online designer logo
		$onlineDesignerLogoLinkUrl = UtilsObj::getPOSTParam('onlinedesignerlogolinkurl');
		$onlineDesignerLogoLinkTooltip = UtilsObj::getPOSTParam('onlinedesignerlogolinktooltip');

		// Smart Guides settings.
		$smartGuidesEnable = UtilsObj::getPOSTParam('smartguidesenable');
		$smartGuidesObjectGuideColour =  UtilsObj::getPOSTParam('smartguidesobjectguidecolour', TPX_SMARTGUIDES_OBJECT_GUIDECOLOUR);
		$smartGuidesPageGuideColour =  UtilsObj::getPOSTParam('smartguidespageguidecolour', TPX_SMARTGUIDES_PAGE_GUIDECOLOUR);

		// Size and position settings.
		$sizeAndPositionMeasurementUnits = UtilsObj::getPOSTParam('sizeandpositionmeasurementunits', TPX_COORDINATE_SCALE_INCHES);

		// CDN URL.
		$onlineDesignerCDNURL = UtilsObj::getValidUrl(UtilsObj::getPOSTParam('onlinedesignercdnurl', ''));

		// Page controls.
		$insertDeleteButtonsVisibilty = UtilsObj::getPOSTParam('insertdeletebuttonsvisibilty', TPX_INSERTDELETEBUTTONS_VISIBILITY_VISIBLE);
		$totalPagesDropdownMode = UtilsObj::getPOSTParam('totalpagesdropdownmode', TPX_TOTALPAGES_DROPDOWN_MODE_ENABLED);

		// redaction settings
		$redactionMode = UtilsObj::getPOSTParam('redactionmode');
		$automaticRedactionEnabled = UtilsObj::getPOSTParam('automaticredactionenabled');
		$automaticRedactionDays = UtilsObj::getPOSTParam('automaticredactiondays');
		$redactionNotificationDays = UtilsObj::getPOSTParam('redactionnotificationdays');
		$orderRedactionMode = UtilsObj::getPOSTParam('orderredactionmode', 0);
        $orderRedactionDays = UtilsObj::getPOSTParam('orderredactiondays', 0);
		$desktopThumbnailRedactionMode = UtilsObj::getPOSTParam('desktopthumbnaildeletionenabled');
		$orderedDesktopThumbnailRedactionDays = UtilsObj::getPOSTParam('ordereddesktopprojectthumbnaildeletiondays');

		$componentUpsellSettings = 6;

        // Average pcitures per page.
        $averagePicturesPerPage = UtilsObj::getPOSTParam('averagepicturesperpage', 0);

		// Update the custom branding files.
		$brandFileArray = json_decode(UtilsObj::getPOSTParam('brandfiles'), true);

		// add the trailing slash to the CDN URL
		if ($onlineDesignerCDNURL != '')
		{
			if (substr($onlineDesignerCDNURL, -1) != '/')
			{
				$onlineDesignerCDNURL .= '/';
			}
		}

		// Desktop Designer Settings
		$useDefaultAccountPagesURL = UtilsObj::getPOSTParam('usedefaultaccountpagesurl', 1);
		$accountPagesURL = UtilsObj::getPOSTParam('accountpagesurl', '');

		/**
		 * Need to set branding company to a company of production site selected.
		 * Only Sys Admin and Company Admin can see Branding tab.
		 * If the user is Company Admin then company should be set to the company user belongs to.
		 * If the user is Sys Admin then company is set to the company production site belongs to.
		 */
		if ($gConstants['optionms'])
		{
			if ($gSession['userdata']['usertype'] == TPX_LOGIN_COMPANY_ADMIN)
			{
				$companyCode = $gSession['userdata']['companycode'];
			}
			else
			{
				$siteDetails = DatabaseObj::getSite(0, $owner);
				$companyCode = $siteDetails['companycode'];
			}
		}
		else
		{
			$companyCode = '';
		}

		$oauthProviderId = UtilsObj::getPOSTParam('oauthprovider', 0);
		$oauthTokenId = UtilsObj::getPOSTParam('oauthrefreshtokenid', 0);

		//Are we allowed to add brands
		if ($gConstants['optionbc'] != 0)
		{
			if ($brandCount >= $gConstants['optionbc'])
			{
				$addBrands = false;
			}
		}

		if ($addBrands)
		{
			$dbObj = DatabaseObj::getGlobalDBConnection();
			if ($dbObj)
			{

				$stmt = $dbObj->prepare('INSERT INTO `BRANDING` (`id`, `datecreated`,`companycode`, `owner`, `code`, `name`,
                                            `applicationname`, `displayurl`, `weburl`, `onlinedesignerurl`, `onlineuiurl` , `onlineapiurl`, `onlinedesignerlogouturl`,
                                            `mainwebsiteurl`, `macdownloadurl`, `win32downloadurl`, `supporttelephonenumber`,
                                            `supportemailaddress`, `registerusingemail`, `sharebyemailmethod`, `sharehidebranding`,`previewdomainurl`,`usedefaultpaymentmethods`, `paymentmethods`, `paymentintegration`,
                                            `allowgiftcards`, `allowvouchers`, `usedefaultemailsettings`, `smtpaddress`, `smtpport`, `smtpauth`,
                                            `smtpauthusername`, `smtpauthpassword`, `smtptype`, `smtpsystemfromname`, `smtpsystemfromaddress`,
                                            `smtpsystemreplytoname`, `smtpsystemreplytoaddress`, `smtpadminname`, `smtpadminaddress`,
                                            `smtpproductionname`, `smtpproductionaddress`, `smtporderconfirmationname`,
                                            `smtporderconfirmationaddress`, `smtpsaveordername`, `smtpsaveorderaddress`, `active`,
                                            `smtpadminactive`, `smtpproductionactive`, `smtporderconfirmationactive`, `smtpsaveorderactive`,
                                            `smtpshippingname`, `smtpshippingaddress`, `smtpshippingactive`, `smtpnewaccountname`,
                                            `smtpnewaccountaddress`, `smtpnewaccountactive`, `smtpresetpasswordname`, `smtpresetpasswordaddress`,
                                            `smtpresetpasswordactive`, `smtporderuploadedname`, `smtporderuploadedaddress`, `smtporderuploadedactive`,
                                            `previewlicensekey`, `previewexpires`, `previewexpiresdays`, `onlinedesignersigninregisterpromptdelay`,
                                            `onlinedataretentionpolicy`,`onlinedesignerusemultilineworkflow`, `googleanalyticscode`, `googleanalyticsuseridtracking`,
											`googletagmanageronlinecode`, `googletagmanagercccode`, `redactionmode`, `automaticredactionenabled`, `automaticredactiondays`, `redactionnotificationdays`,
											`orderredactiondays`, `orderredactionmode`,
                                            `imagescalingbefore`, `imagescalingbeforeenabled`,
                                            `imagescalingafter`, `imagescalingafterenabled`,
                                            `shufflelayout`, `showshufflelayoutoptions`, `onlineeditormode`, `enableswitchingeditor`,
											`onlinedesignerlogolinkurl`, `onlinedesignerlogolinktooltip`,
											`sizeandpositionmeasurementunits`,
											`smartguidesenable`, `smartguidesobjectguidecolour`, `smartguidespageguidecolour`, `automaticallyapplyperfectlyclear`, `allowuserstotoggleperfectlyclear`,
											`onlinedesignercdnurl`, `insertdeletebuttonsvisibility`, `totalpagesdropdownmode`, `averagepicturesperpage`,
											`desktopthumbnaildeletionenabled`, `desktopthumbnaildeletionordereddays`, `oauthprovider`, `oauthtoken`,
											`usedefaultaccountpagesurl`, `accountpagesurl`, `onlineappkeyentropyvalue`, `onlineabouturl`, `onlinehelpurl`, `onlinetermsandconditionsurl`, `componentUpsellSettings`
											)
                                            VALUES (0, now(), ?, ?, ?, ?,
                                                    ?, ?, ?, ?, ?, ?, ?,
                                                    ?, ?, ?, ?,
                                                    ? ,? ,? ,?, ?, ?, ?, ?,
                                                    ?, ?, ?, ?, ?, ?,
                                                    ?, ?, ?, ?, ?,
                                                    ?, ?, ?, ?,
                                                    ?, ?, ?,
                                                    ?, ?, ?, ?,
                                                    ?, ?, ?, ?,
                                                    ?, ?, ?, ?,
                                                    ?, ?, ?, ?,
                                                    ?, ?, ?, ?,
                                                    ?, ?, ?, ?,
                                                    ?, ?, ?, ?,
                                                    ?, ?, ?, ?, ?, ?,
													?, ?,
                                                    ?, ?,
                                                    ?, ?,
													?, ?, ?, ?,
                                                    ?, ?,
													?,
													?, ?, ?, ?, ?,
													?, ?, ?, ?,
													?, ?, ?, ?,
													?, ?, ?, ?, ?, ?, ?)');

				if ($stmt)
				{
					if ($stmt->bind_param('ssss' . 'sssssss' . 'ssss' . 'siiisiss' . 'iiisii' . 'sssss' . 'ssss' . 'sss' . 'sssi' . 'iiii' . 'ssis' . 'siss' . 'issi' . 'siii' . 'iisi' .
											'ssiiii' . 'ii' . 'di' . 'di' . 'iiii' . 'ss' . 'i' . 'issii' . 'siii' . 'iiii' . 'isssssi',
                            $companyCode, $owner, $brandingCode, $brandingName,
                            $brandingApplicationName, $brandingDisplayURL, $brandingWebURL, $onlineDesignerURL, $onlineUIURL, $onlineAPIURL, $onlineDesignerLogoutURL,
                            $mainWebsiteUrl, $macDownloadUrl, $win32DownloadUrl, $supportTelephone,
                            $supportEmail, $registerUsingEmail, $shareByEmailMethod, $shareHideBranding, $previewDomainURL, $brandingUseDefaultPaymentMethods, $brandingPaymentMethods, $brandingPaymentIntegration,
                            $allowGiftCards, $allowVouchers, $useDefaultEmailSettings, $smtpAddress, $smtpPort, $smtpAuth,
                            $smtpAuthUser, $smtpAuthPass, $smtpType, $smtpSysFromName, $smtpSysFromAddress,
                            $smtpReplyName, $smtpReplyAddress, $smtpAdminName, $smtpAdminAddress,
                            $smtpProdName, $smtpProdAddress, $smtpOrderConfName,
                            $smtpOrderConfAddress, $smtpSaveOrderName, $smtpSaveOrderAddress, $brandingIsActive,
                            $smtpAdminActive, $smtpProdActive, $smtpOrderConfActive, $smtpSaveOrderActive,
                            $smtpShippingName, $smtpShippingAddress, $smtpShippingActive, $smtpNewAccountName,
                            $smtpNewAccountAddress, $smtpNewAccountActive, $smtpResetPasswordName, $smtpResetPasswordAddress,
                            $smtpResetPasswordActive, $smtpOrderUploadedName, $smtpOrderUploadedAddress, $smtpOrderUploadedActive,
                            $brandingPreviewLicenseKey, $previewExpire, $previewExpireDays, $onlineDesingerSignInRegisterPromptDelay,
                            $onlineDataRetentionPolicy, $useMultiLineWorkflow, $brandingGoogleCode, $brandingGoogleUserIDTracking,
							$brandingGoogleTagManagerOnlineCode, $brandingGoogleTagManagerCCCode, $redactionMode, $automaticRedactionEnabled, $automaticRedactionDays, $redactionNotificationDays,
							$orderRedactionDays, $orderRedactionMode,
                            $imageScalingBefore, $imageScalingBeforeEnabled,
                            $imageScalingAfter, $imageScalingAfterEnabled,
                            $shuffleLayout, $showShuffleLayoutOptions, $onlineEditorMode, $enableSwitchingEditor,
							$onlineDesignerLogoLinkUrl, $onlineDesignerLogoLinkTooltip,
							$sizeAndPositionMeasurementUnits,
							$smartGuidesEnable, $smartGuidesObjectGuideColour, $smartGuidesPageGuideColour, $automaticallyApplyPerfectlyClear, $allowUsersToTogglePerfectlyClear,
							$onlineDesignerCDNURL, $insertDeleteButtonsVisibilty, $totalPagesDropdownMode, $averagePicturesPerPage,
							$desktopThumbnailRedactionMode, $orderedDesktopThumbnailRedactionDays, $oauthProviderId, $oauthTokenId,
							$useDefaultAccountPagesURL, $accountPagesURL, $onlineAppKeyEntropyValue, $onlineAboutURL, $onlineHelpURL, $onlineTermsAndConditionsURL, $componentUpsellSettings
						))
					{
						if ($stmt->execute())
						{
							$recordID = $dbObj->insert_id;

							self::createBrandPath($brandingCode, $brandingName);
							self::copyBrandFiles($brandingCode, $brandingName);
							self::copySSOFiles($brandingCode, $brandingName);

							if($gConstants['optionms'])
							{
								RoutingObj::routingRuleAdd(TPX_ROUTE_BY_BRAND_CODE, TPX_TEST_FOR_EQUALITY, $brandingCode, $owner);
							}

							DatabaseObj::updateActivityLog($gSession['ref'], 0, $gSession['userid'], $gSession['userlogin'], $gSession['username'], 0,
								'ADMIN', 'BRANDING-ADD', $recordID . ' ' . $brandingName, 1);

							// If there is a data retention policy log that it has been assigned to the brand.
							if ($onlineDataRetentionPolicy != 0)
							{
								self::logDataRetentionPolicyUpdate('add', 0, $onlineDataRetentionPolicy, $brandingCode, $gSession);
							}

							// Generate the details to pass to update the CSP configuration.
							$cspDetails = [
								'brandCode' => $brandingCode,
								'displayUrl' => trim($brandingDisplayURL),
								'webUrl' => trim($brandingWebURL),
								'onlineDesignerUrl' => trim($onlineDesignerURL),
								'analytics' => ($brandingGoogleCode != ''),
								'tagmanager' => ($brandingGoogleTagManagerCCCode != ''),
							];

							// Update the CSP Config.
							self::updateCSPDetails($cspDetails);

							if ($gConstants['optiondesol']) {
								$fontListDetails = [
									'type' => UtilsObj::getPOSTParam('fontlisttype', -1),
									'fontlist' => UtilsObj::getPOSTParam('fontlist', null),
									'codes' => [('' === $brandingCode ? '__DEFAULT__' : $brandingCode)],
									'checkfield' => 'brandcode',
								];
								AdminTaopixOnlineFontLists_model::updateAssignments($fontListDetails, $ac_config);

                                // push the new brand to online
                                self::applyBrandUIConfigToOnline($recordID, 'create');
							}
						}
						else
						{
							// could not execute statement
							// first check for a duplicate key (branding code)
							if ($stmt->errno == 1062)
							{
								$result = 'str_ErrorBrandingExists';
							}
							else
							{
								$result = 'str_DatabaseError';
								$resultParam = 'brandingAdd execute ' . $dbObj->error;
							}
						}
					}
					else
					{
						// could not bind parameters
						$result = 'str_DatabaseError';
						$resultParam = 'brandingAdd bind ' . $dbObj->error;
					}
					$stmt->free_result();
					$stmt->close();
				}
				else
				{
					// could not prepare statement
					$result = 'str_DatabaseError';
					$resultParam = 'brandingAdd prepare ' . $dbObj->error;
				}

				$dbObj->close();
			}
			else
			{
				// could not open database connection
				$result = 'str_DatabaseError';
				$resultParam = 'brandingAdd connect ' . $dbObj->error;
			}

			// Update the brand image files and db records.
			if (($result == '') && (count($brandFileArray) > 0))
			{
				$updateResult = array('error' => '');

				$typeMessageArray = array(
					TPX_BRANDING_FILE_TYPE_CC_LOGO => 'str_LabelCustomerAccountLogo',
					TPX_BRANDING_FILE_TYPE_MARKETING => 'str_LabelCustomerAccountSidebar',
					TPX_BRANDING_FILE_TYPE_EMAIL_LOGO => 'str_LabelEmailLogo',
					TPX_BRANDING_FILE_TYPE_OL_LOGO => 'str_LabelOnlineDesignerLogo',
					TPX_BRANDING_FILE_TYPE_OL_LOGO_DARK => 'str_LabelOnlineDesignerLogoDark',
				);

				$resultParamArray = array();

				foreach ($brandFileArray as $typeRef => $actionArray)
				{
					switch ($typeRef)
					{
						case TPX_BRANDING_FILE_TYPE_CC_LOGO:
						case TPX_BRANDING_FILE_TYPE_MARKETING:
						case TPX_BRANDING_FILE_TYPE_EMAIL_LOGO:
						case TPX_BRANDING_FILE_TYPE_OL_LOGO:
						case TPX_BRANDING_FILE_TYPE_OL_LOGO_DARK:
						{
							if ($actionArray['action'] == 'update')
							{
								$updateResult = self::updateBrandingData($recordID, $typeRef, $actionArray['path']);
							}

							break;
						}
					}

					if ($updateResult['error'] != '')
					{
						$result = $updateResult['error'];

						$smarty = SmartyObj::newSmarty('AdminBranding');
						$resultParamArray[] = $smarty->get_config_vars($typeMessageArray[$typeRef]);
					}
				}

				if ($result != '')
				{
					$resultParam = implode(', ', $resultParamArray);
				}
			}

			$typeMessageArray = array(
				TPX_BRANDING_TEXT_TYPE_SIGNATURE => 'emailSignature'
			);

			// Update the branding text
			foreach ($typeMessageArray as $typeRef => $langPanelPrefix)
			{
				switch ($typeRef)
				{
					case TPX_BRANDING_TEXT_TYPE_SIGNATURE:
					{
						$enableCustomCheckID = $langPanelPrefix . 'enablecheck';
						$defaultCheckID = $langPanelPrefix . 'usedefaultcheck';
						$arrayKey = $langPanelPrefix . 'langpanel';

						$enableCustomCheck = UtilsObj::getPOSTParam($enableCustomCheckID);
						$useDefaultCheck = UtilsObj::getPOSTParam($defaultCheckID);
						$languageText = UtilsObj::getPOSTParam($arrayKey);

						$updateResult = self::updateBrandingTextData($recordID, $typeRef, $languageText, $useDefaultCheck, $enableCustomCheck);

						break;
					}
				}
			}
		}

		if ($result == '')
		{
			echo '{"success": true,	"msg":""}';
		}
		else
		{
			$smarty = SmartyObj::newSmarty('AdminBranding');
        	echo '{"success":false,	"msg":"' . $smarty->get_config_vars($result) . '. ' . $resultParam . '"}';
        }
	}

    static function displayEdit($pID)
	{
		global $ac_config;
		global $gConstants;
        $resultArray =  DatabaseObj::getBrandingFromID($pID);
        $suitableForBrands = 1;

        if ($resultArray['code'] == '' && $pID > 0)
        {
        	$suitableForBrands = 0;
        }

        $resultArray['paymentmethodslist'] = DatabaseObj::getPaymentMethodsList();
        $resultArray['paymentintegrationslist'] = DatabaseObj::getPaymentIntegrations($suitableForBrands);
        $resultArray['productionsites'] = RoutingObj::getProductionSiteNames();
        $resultArray['datapolicies'] = DatabaseObj::getOnlineDataPolicy(-1);
        $resultArray['allowimagescalingbefore'] = UtilsObj::getArrayParam($ac_config, "ALLOWIMAGESCALINGBEFORE", 0) == 1;

		$resultArray['cusomisedtext'] = array();
		$customisedText = DatabaseObj::getBrandCustomText($pID, TPX_BRANDING_TEXT_TYPE_SIGNATURE);

		// Get the default brands text.
		$defaultBrandCustomisedText = DatabaseObj::getBrandCustomText(1, TPX_BRANDING_TEXT_TYPE_SIGNATURE);
		$customisedText['data']['defaultdata'] = $defaultBrandCustomisedText['data']['data'];

        $resultArray['cusomisedtext'][$customisedText['data']['object']] = $customisedText['data'];

        // blank out the image scaling if the value is the default
        if ($resultArray['imagescalingbefore'] == 0.0)
        {
            $resultArray['imagescalingbefore'] = '';
        }

        // blank out the image scaling if the value is the default
        if ($resultArray['imagescalingafter'] == 0.0)
        {
            $resultArray['imagescalingafter'] = '';
        }

		$resultArray['massunsubscribetaskforbrandrunning'] = false;

        $massUnsubscribeEventsResult = self::checkForActiveMassUnsubscribeEventsForBrand($resultArray['code'], 'TAOPIX_MASSUNSUBSCRIBE');

		// we need to check if there are any active TAOPIX_MASSUNSUBSCRIBE events active for this brand.
		// if there is then we need to prevent another event from being created via the button on the admin screen.
        if ($massUnsubscribeEventsResult['activeeventcount'] > 0)
        {
        	$resultArray['massunsubscribetaskforbrandrunning'] = true;
        }

		// Check the status of each of the images used for branding.
		$brandAssetFileData = array(
					TPX_BRANDING_FILE_TYPE_CC_LOGO => 0,
					TPX_BRANDING_FILE_TYPE_MARKETING => 0,
					TPX_BRANDING_FILE_TYPE_EMAIL_LOGO => 0,
				);

		$resultArray['fontlists'] = [];
		$resultArray['fontlistselected'] = '';

		// If the customer has online add the online branding image to the list of assets to show, and update the fontlist details.
		if ($gConstants['optiondesol']) {
			$brandAssetFileData[TPX_BRANDING_FILE_TYPE_OL_LOGO] = 0;
			$brandAssetFileData[TPX_BRANDING_FILE_TYPE_OL_LOGO_DARK] = 0;

			$fontListDetails = AdminTaopixOnlineFontLists_model::getFontListData($ac_config, 'brandcode', ('' === $resultArray['code'] && $pID > -1 ? '__DEFAULT__' : $resultArray['code']));
			$resultArray['fontlists'] = $fontListDetails['fontlists'];
			$resultArray['fontlistselected'] = $fontListDetails['selected'];
		}

		$componentUpsellSettings = $resultArray['componentupsellsettings'];
		$resultArray['componentupsellenabled'] = ($componentUpsellSettings & TPX_COMPONENT_UPSELL_ENABLED) ? 1 : 0;
		$resultArray['componentupsellproductquantity'] = ($componentUpsellSettings & TPX_COMPONENT_UPSELL_ALLOW_PRODUCT_QTY) ? 1 : 0;

		$displayBrandImageFileWarning = 0;

		foreach ($brandAssetFileData as $typeRef => $location)
		{
			$brandAssetFileData[$typeRef] = self::checkForUploadedBrandFile($pID, $typeRef);
		}

		$resultArray['brandassetsdata'] = $brandAssetFileData;

		// Add details for OAuth providers
		$oauthProviders = DatabaseObj::getDataFromTable(['id', 'providername'], 'oauthprovider', DatabaseObj::getGlobalDBConnection(), '', true, []);
		$resultArray['oauthproviders'] = array_map(function($provider) { if ('' !== $provider['id']) { return array_values($provider); } }, $oauthProviders['data']);


		if (-1 === $pID) {
			$resultArray['entropy'] = \bin2hex(\openssl_random_pseudo_bytes(\openssl_cipher_iv_length("aes-128-cbc")));
			$resultArray['regenerateVisible'] = false;
		} else {
			$resultArray['entropy'] = '';
			$resultArray['regenerateVisible'] = true;
		}

        return $resultArray;
    }

    static function checkForActiveMassUnsubscribeEventsForBrand($pWebBrandCode, $pTaskCode)
    {
        $result = '';
        $resultParam = '';
        $activeEventCount = 0;

        $dbObj = DatabaseObj::getGlobalDBConnection();

        if ($dbObj)
        {
            // retrieve the event data
            // we use an inner join here as it prevents mysql from retrieving blob data until we have a final list of rows we need to retrieve
            if ($stmt = $dbObj->prepare('SELECT count(`id`) FROM `EVENTS` WHERE (`taskcode` = ?)
                                        	AND (`active` = 1) AND (`statuscode` != 2) AND (`runcount` < `maxrunCount`)
                                        	AND (`webbrandcode` = ?)
                                            ORDER BY `priority` DESC, `nextruntime` DESC'))
            {
                if ($stmt->bind_param('ss', $pTaskCode, $pWebBrandCode))
                {
					if ($stmt->execute())
					{
						$stmt->store_result();

						if ($stmt->bind_result($activeEventCount))
						{
							if (! $stmt->fetch())
							{
								$result = 'str_DatabaseError';
								$resultParam = 'checkForActiveMassUnsubscribeEventsForBrand fetch ' . $dbObj->error;
							}
						}
					}
					else
					{
						$result = 'str_DatabaseError';
						$resultParam = 'checkForActiveMassUnsubscribeEventsForBrand execute ' . $dbObj->error;
					}
                }
                else
                {
                    $result = 'str_DatabaseError';
                    $resultParam = 'checkForActiveMassUnsubscribeEventsForBrand bind ' . $dbObj->error;
                }

                $stmt->free_result();
				$stmt->close();
				$stmt = null;
            }
            else
            {
                $result = 'str_DatabaseError';
                $resultParam = 'checkForActiveMassUnsubscribeEventsForBrand prepare ' . $dbObj->error;
            }

            $dbObj->close();
        }
        else
        {
            $result = 'str_DatabaseError';
            $resultParam = 'checkForActiveMassUnsubscribeEventsForBrand connect ' . $dbObj->error;
        }

        $resultArray['result'] = $result;
        $resultArray['resultparam'] = $resultParam;
        $resultArray['activeeventcount'] = $activeEventCount;

        return $resultArray;
    }

    static function brandingEdit()
	{
		global $ac_config;
		global $gSession;
		global $gConstants;

		$result = '';
		$resultParam = '';

		$id = UtilsObj::getPOSTParam('id');
		$brandingCode = UtilsObj::getPOSTParam('code');

		if ($gConstants['optionms'])
		{
			$owner = UtilsObj::getPOSTParam('productionsite');
		}
		else
		{
			$owner = '';
		}

		$brandingName = UtilsObj::getPOSTParam('name');
		$brandingApplicationName = UtilsObj::escapeInputForHTML(UtilsObj::getPOSTParam('applicationname'));
		$brandingDisplayURL = UtilsObj::getValidUrl(UtilsObj::getPOSTParam('displayurl'));
		$brandingWebURL = UtilsObj::getValidUrl(UtilsObj::getPOSTParam('weburl'));

		$onlineDesignerURL = UtilsObj::getValidUrl(UtilsObj::getPOSTParam('onlinedesignerurl'));
		$onlineUIURL = UtilsObj::getValidUrl(UtilsObj::getPOSTParam('onlineuiurl'));
		$onlineAPIURL = UtilsObj::getValidUrl(UtilsObj::getPOSTParam('onlineapiurl'));
		$onlineAboutURL = UtilsObj::getValidUrl(UtilsObj::getPOSTParam('onlineabouturl'));
        $onlineHelpURL = UtilsObj::getValidUrl(UtilsObj::getPOSTParam('onlinehelpurl'));
        $onlineTermsAndConditionsURL = UtilsObj::getValidUrl(UtilsObj::getPOSTParam('onlinetermsandconditionsurl'));
		$onlineDesignerLogoutURL = UtilsObj::getValidUrl(UtilsObj::getPOSTParam('onlinedesignerlogouturl'));
		$onlineDesingerSignInRegisterPromptDelay = UtilsObj::getPOSTParam('nagdelay');
		$onlineDataRetentionPolicy = UtilsObj::getPOSTParam('onlinedataretentionpolicy');

		$mainWebsiteUrl = UtilsObj::getValidUrl(UtilsObj::getPOSTParam('mainwebsiteurl'));
        $macDownloadUrl  = UtilsObj::getValidUrl(UtilsObj::getPOSTParam('macdownloadurl'));
        $win32DownloadUrl = UtilsObj::getValidUrl(UtilsObj::getPOSTParam('win32downloadurl'));
        $supportTelephone = UtilsObj::getPOSTParam('supporttelephonenumber');
        $supportEmail = UtilsObj::getPOSTParam('supportemailaddress');
        $defaultCommunicationPreference = UtilsObj::getPOSTParam('defaultcommunicationpreference');
        $shareByEmailMethod = UtilsObj::getPOSTParam('sharebyemailmethod');
        $shareHideBranding = UtilsObj::getPOSTParam('sharehidebranding');
        $previewDomainURL = UtilsObj::getPOSTParam('previewdomainurl');
        $orderFromPreview = UtilsObj::getPOSTParam('orderfrompreview');
        $registerUsingEmail = UtilsObj::getPOSTParam('registerusingemail');

		$brandingGoogleCode = UtilsObj::getPOSTParam('googlecode');
		$brandingGoogleUserIDTracking = UtilsObj::getPOSTParam('googleuseridtracking');
		$brandingGoogleTagManagerOnlineCode = UtilsObj::getPOSTParam('googletagmanageronlinecode');
		$brandingGoogleTagManagerCCCode = UtilsObj::getPOSTParam('googletagmanagercccode');
		$brandingUseDefaultPaymentMethods = UtilsObj::getPOSTParam('usedefaultpaymentmethods');
		$brandingPaymentMethods	= UtilsObj::getPOSTParam('paymentmethods');
		$brandingPaymentIntegration = UtilsObj::getPOSTParam('paymentintegration');
        $allowGiftCards	= UtilsObj::getPOSTParam('allowgiftcards');
		$allowVouchers = UtilsObj::getPOSTParam('allowvouchers');
		$brandingIsActive = UtilsObj::getPOSTParam('isactive');

		$brandingPreviewLicenseKey	= UtilsObj::getPOSTParam('previewlicensekey');

		// SMTP settings for branding
		$smtpAuth = UtilsObj::getPOSTParam('smtpauth');
		$useDefaultEmailSettings = UtilsObj::getPOSTParam('usedefaultemailsettings');

		if ($useDefaultEmailSettings != 1)
		{
			$useDefaultEmailSettings = 0;
		}

		if ($brandingCode == '')
		{
			$brandingName = '';
			$owner = '';
			$companyCode = '';
			$useDefaultEmailSettings = 0;
			$brandingUseDefaultPaymentMethods = 0;
			$brandingIsActive = 1;
		}

		$smtpAddress = UtilsObj::getPOSTParam('smtpaddress');
		$smtpPort = UtilsObj::getPOSTParam('smtpport');
		$smtpAuthUser = UtilsObj::getPOSTParam('smtpauthuser');
		$smtpAuthPass = UtilsObj::getPOSTParam('_smtpauthpass');
        $smtpType = UtilsObj::getPOSTParam('smtptype');
		$smtpSysFromName = UtilsObj::getPOSTParam('smtpsysfromname');
		$smtpSysFromAddress = UtilsObj::getPOSTParam('smtpsysfromaddress');
		$smtpReplyName = UtilsObj::getPOSTParam('smtpreplyname');
		$smtpReplyAddress = UtilsObj::getPOSTParam('smtpreplyaddress');

		$smtpAdminName = UtilsObj::FormatEmailNameSettings($_POST['smtpadminname']);
		$smtpAdminAddress = UtilsObj::FormatEmailSettings($_POST['smtpadminaddress']);
		$smtpProdName = UtilsObj::FormatEmailNameSettings($_POST['smtpprodname']);
		$smtpProdAddress = UtilsObj::FormatEmailSettings($_POST['smtpprodaddress']);
		$smtpOrderConfName = UtilsObj::FormatEmailNameSettings($_POST['smtporderconfname']);
		$smtpOrderConfAddress = UtilsObj::FormatEmailSettings($_POST['smtporderconfaddress']);
		$smtpSaveOrderName = UtilsObj::FormatEmailNameSettings($_POST['smtpsaveordername']);
		$smtpSaveOrderAddress = UtilsObj::FormatEmailSettings($_POST['smtpsaveorderaddress']);
		$smtpShippingName = UtilsObj::FormatEmailNameSettings($_POST['smtpshippingname']);
		$smtpShippingAddress = UtilsObj::FormatEmailSettings($_POST['smtpshippingaddress']);
		$smtpNewAccountName = UtilsObj::FormatEmailNameSettings($_POST['smtpnewaccountname']);
		$smtpNewAccountAddress = UtilsObj::FormatEmailSettings($_POST['smtpnewaccountaddress']);
		$smtpResetPasswordName = UtilsObj::FormatEmailNameSettings($_POST['smtpresetpasswordname']);
		$smtpResetPasswordAddress = UtilsObj::FormatEmailSettings($_POST['smtpresetpasswordaddress']);
		$smtpOrderUploadedName = UtilsObj::FormatEmailNameSettings($_POST['smtporderuploadedname']);
		$smtpOrderUploadedAddress = UtilsObj::FormatEmailSettings($_POST['smtporderuploadedaddress']);

		$smtpAdminActive = UtilsObj::getPOSTParam('smtpadminactive');
		$smtpProdActive = UtilsObj::getPOSTParam('smtpproductionactive');
		$smtpOrderConfActive = UtilsObj::getPOSTParam('smtporderconfirmationactive');
		$smtpSaveOrderActive = UtilsObj::getPOSTParam('smtpsaveorderactive');
		$smtpShippingActive = UtilsObj::getPOSTParam('smtpshippingactive');
		$smtpNewAccountActive = UtilsObj::getPOSTParam('smtpnewaccountactive');
		$smtpResetPasswordActive= UtilsObj::getPOSTParam('smtpresetpasswordactive');
		$smtpOrderUploadedActive= UtilsObj::getPOSTParam('smtporderuploadedactive');

		$previewExpire= UtilsObj::getPOSTParam('previewExpire');
		$previewExpireDays= UtilsObj::getPOSTParam('previewExpireDays');
		if ($previewExpire == 0)
		{
			$previewExpireDays = 1;
		}

		$redactionMode = UtilsObj::getPOSTParam('redactionmode');
		$automaticRedactionEnabled = UtilsObj::getPOSTParam('automaticredactionenabled');
		$automaticRedactionDays = UtilsObj::getPOSTParam('automaticredactiondays');
		$redactionNotificationDays = UtilsObj::getPOSTParam('redactionnotificationdays');
		$orderRedactionMode = UtilsObj::getPOSTParam('orderredactionmode', 0);
		$orderRedactionDays = UtilsObj::getPOSTParam('orderredactiondays', 0);
		$desktopThumbnailRedactionMode = UtilsObj::getPOSTParam('desktopthumbnaildeletionenabled');
		$orderedDesktopThumbnailRedactionDays = UtilsObj::getPOSTParam('ordereddesktopprojectthumbnaildeletiondays');


		$useMultiLineWorkflow = UtilsObj::getPOSTParam('usemultilinebasketworkflow');

		$imageScalingBefore = UtilsObj::getPOSTParam('imagescalingbefore');
		$imageScalingBeforeEnabled = UtilsObj::getPOSTParam('imagescalingbeforeenabled');

		$imageScalingAfter = UtilsObj::getPOSTParam('imagescalingafter');
		$imageScalingAfterEnabled = UtilsObj::getPOSTParam('imagescalingafterenabled');

        // make sure that the imagescaling value is set and is greater than 0 and less thant 999.99
        if (($imageScalingBefore == '') || ($imageScalingBefore < 0) || ($imageScalingBefore > 999.99))
        {
            $imageScalingBefore = 0.0;
        }

        // make sure that the imagescaling value is set and is greater than 0 and less thant 999.99
        if (($imageScalingAfter == '') || ($imageScalingAfter < 0) || ($imageScalingAfter > 999.99))
        {
            $imageScalingAfter = 0.0;
        }


		$onlineDesignerLogoLinkUrl = UtilsObj::getPOSTParam('onlinedesignerlogolinkurl');
		$onlineDesignerLogoLinkTooltip = UtilsObj::getPOSTParam('onlinedesignerlogolinktooltip');

		$automaticallyApplyPerfectlyClear = UtilsObj::getPOSTParam('automaticallyapplyperfectlyclear');
		$allowUsersToTogglePerfectlyClear = UtilsObj::getPOSTParam('toggleperfectlyclear');

		// CDN URL.
		$onlineDesignerCDNURL = UtilsObj::getValidUrl(UtilsObj::getPOSTParam('onlinedesignercdnurl', ''));

		// add the trailing slash to the CDN URL
		if ($onlineDesignerCDNURL != '')
		{
			if (substr($onlineDesignerCDNURL, -1) != '/')
			{
				$onlineDesignerCDNURL .= '/';
			}
		}

		// Update the custom branding files.
		$brandFileArray = json_decode(UtilsObj::getPOSTParam('brandfiles'), true);

		$oauthProviderId = UtilsObj::getPOSTParam('oauthprovider', 0);
		$oauthTokenId = UtilsObj::getPOSTParam('oauthrefreshtokenid', 0);

		// Desktop Designer Settings
		$useDefaultAccountPagesURL = UtilsObj::getPOSTParam('usedefaultaccountpagesurl', 1);
		$accountPagesURL = UtilsObj::getPOSTParam('accountpagesurl', '');

		/**
		* Need to set branding company to a company of production site selected.
		* Only Sys Admin and Company Admin can see Branding tab.
		* If the user is Company Admin then company should be set to the company user belongs to.
		* If the user is Sys Admin then company is set to the company production site belongs to.
		*/
		if ($gConstants['optionms'])
		{
		 	if ($gSession['userdata']['usertype'] == TPX_LOGIN_COMPANY_ADMIN)
			{
				$companyCode = $gSession['userdata']['companycode'];
			}
			else
			{
				$siteDetails = DatabaseObj::getSite(0, $owner);
				$companyCode = $siteDetails['companycode'];
			}
		}
		else
		{
			$companyCode = '';
		}

		if ($id > 0)
		{
			$origBrandArray = DatabaseObj::getBrandingFromID($id);

			$dbObj = DatabaseObj::getGlobalDBConnection();
			if ($dbObj)
			{

                $stmt = $dbObj->prepare('UPDATE `BRANDING` SET `code` = ?, `companycode` = ?, `owner` = ?, `name` = ?, `applicationname` = ?,
                                            `displayurl` = ?, `weburl` = ?, `onlinedesignerurl` = ?, `onlineuiurl` = ?, `onlineapiurl` = ?, `onlinedesignerlogouturl` = ?,
                                            `mainwebsiteurl` = ?,`macdownloadurl` = ?,`win32downloadurl` = ?,`supporttelephonenumber` = ?,
                                            `supportemailaddress` = ?, `defaultCommunicationPreference` = ?, `registerusingemail` = ?, `sharebyemailmethod` = ?,
                                            `orderfrompreview` = ?, `sharehidebranding` = ?, `previewdomainurl` = ?,`usedefaultpaymentmethods` = ?, `paymentmethods` = ?,
                                            `paymentintegration` = ?, `allowgiftcards` = ?, `allowvouchers` = ?, `usedefaultemailsettings` = ?,
                                            `smtpaddress` = ?, `smtpport` = ?, `smtpauth` = ?, `smtpauthusername` = ?, `smtpauthpassword` = ?,
                                            `smtptype` = ?, `smtpsystemfromname` = ?, `smtpsystemfromaddress` = ?, `smtpsystemreplytoname` = ?,
                                            `smtpsystemreplytoaddress` = ?, `smtpadminname` = ?, `smtpadminaddress` = ?,
                                            `smtpproductionname` = ?, `smtpproductionaddress` = ?, `smtporderconfirmationname` = ?,
                                            `smtporderconfirmationaddress` = ?, `smtpsaveordername` = ?, `smtpsaveorderaddress` = ?,
                                            `smtpadminactive` = ?, `smtpproductionactive` = ?, `smtporderconfirmationactive` = ?,
                                            `smtpsaveorderactive` = ?, `smtpshippingname` = ?, `smtpshippingaddress` = ?,
                                            `smtpshippingactive` = ?, `smtpnewaccountname` = ?, `smtpnewaccountaddress` = ?,
                                            `smtpnewaccountactive` = ?, `smtpresetpasswordname` = ?, `smtpresetpasswordaddress` = ?,
                                            `smtpresetpasswordactive` = ?, `smtporderuploadedname` = ?, `smtporderuploadedaddress` = ?,
                                            `smtporderuploadedactive` = ?, `previewlicensekey` = ?, `previewexpires` = ?,
                                            `previewexpiresdays` = ?, `onlinedesignersigninregisterpromptdelay` = ?, `onlinedataretentionpolicy` = ?,
                                            `onlinedesignerusemultilineworkflow` = ?,
											`googleanalyticscode` = ?, `redactionmode` = ?, `automaticredactionenabled` = ?, `automaticredactiondays` = ?,
											`redactionnotificationdays` = ?, `orderredactiondays` = ?, `orderredactionmode` = ?,
											`googleanalyticsuseridtracking` = ?, `googletagmanageronlinecode` = ?, `googletagmanagercccode` = ?,
											`imagescalingbefore` = ?, `imagescalingbeforeenabled` = ?,
											`imagescalingafter` = ?, `imagescalingafterenabled` = ?,
											`onlinedesignerlogolinkurl` = ?, `onlinedesignerlogolinktooltip` = ?,
											`automaticallyapplyperfectlyclear` = ?, `allowuserstotoggleperfectlyclear` = ?, `onlinedesignercdnurl` = ?,
											`desktopthumbnaildeletionenabled` = ?, `desktopthumbnaildeletionordereddays` = ?,
                                            `active` = ?, `oauthprovider` = ?, `oauthtoken` = ?, `usedefaultaccountpagesurl` = ?, `accountpagesurl` = ?,
                                            `onlineabouturl` = ?, `onlinehelpurl` = ?, `onlinetermsandconditionsurl` = ?, `datelastmodified` = CURRENT_TIMESTAMP
                                        WHERE `id` = ?');

				if ($stmt)
				{
					if ($stmt->bind_param('sssss' . 'ssssss' . 'ssss' . 'siii' . 'iisis' . 'siii' . 'siiss' . 'ssss' . 'sss' . 'sss' . 'sss' . 'iii' . 'iss' . 'iss' . 'iss' .
						'iss' . 'isi' . 'iii' . 'i' . 'siii' . 'iii' .'iss' . 'di' . 'di' . 'ss' . 'iis' . 'ii' . 'iiiissss' . 'i',
                        $brandingCode, $companyCode, $owner, $brandingName, $brandingApplicationName,
                        $brandingDisplayURL, $brandingWebURL, $onlineDesignerURL, $onlineUIURL, $onlineAPIURL, $onlineDesignerLogoutURL,
                        $mainWebsiteUrl, $macDownloadUrl, $win32DownloadUrl, $supportTelephone,
                        $supportEmail, $defaultCommunicationPreference, $registerUsingEmail, $shareByEmailMethod,
                        $orderFromPreview, $shareHideBranding, $previewDomainURL, $brandingUseDefaultPaymentMethods, $brandingPaymentMethods,
                        $brandingPaymentIntegration, $allowGiftCards, $allowVouchers, $useDefaultEmailSettings,
                        $smtpAddress, $smtpPort, $smtpAuth, $smtpAuthUser, $smtpAuthPass,
                        $smtpType, $smtpSysFromName, $smtpSysFromAddress, $smtpReplyName,
                        $smtpReplyAddress, $smtpAdminName, $smtpAdminAddress,
                        $smtpProdName, $smtpProdAddress, $smtpOrderConfName,
                        $smtpOrderConfAddress, $smtpSaveOrderName, $smtpSaveOrderAddress,
                        $smtpAdminActive, $smtpProdActive, $smtpOrderConfActive,
                        $smtpSaveOrderActive, $smtpShippingName, $smtpShippingAddress,
                        $smtpShippingActive, $smtpNewAccountName, $smtpNewAccountAddress,
                        $smtpNewAccountActive, $smtpResetPasswordName, $smtpResetPasswordAddress,
                        $smtpResetPasswordActive, $smtpOrderUploadedName, $smtpOrderUploadedAddress,
                        $smtpOrderUploadedActive, $brandingPreviewLicenseKey, $previewExpire,
                        $previewExpireDays, $onlineDesingerSignInRegisterPromptDelay, $onlineDataRetentionPolicy, $useMultiLineWorkflow,
						$brandingGoogleCode, $redactionMode, $automaticRedactionEnabled, $automaticRedactionDays,
						$redactionNotificationDays, $orderRedactionDays, $orderRedactionMode,
						$brandingGoogleUserIDTracking, $brandingGoogleTagManagerOnlineCode, $brandingGoogleTagManagerCCCode,
						$imageScalingBefore, $imageScalingBeforeEnabled,
						$imageScalingAfter, $imageScalingAfterEnabled,
						$onlineDesignerLogoLinkUrl,
						$onlineDesignerLogoLinkTooltip,
						$automaticallyApplyPerfectlyClear,
						$allowUsersToTogglePerfectlyClear, $onlineDesignerCDNURL,
						$desktopThumbnailRedactionMode, $orderedDesktopThumbnailRedactionDays,
						$brandingIsActive, $oauthProviderId, $oauthTokenId, $useDefaultAccountPagesURL, $accountPagesURL,
                        $onlineAboutURL, $onlineHelpURL, $onlineTermsAndConditionsURL,
						$id))
					{
						if ($stmt->execute())
						{
							// If the data retention policy has changed, log this in the activity log.
							if ($origBrandArray['onlinedataretentionpolicy'] != $onlineDataRetentionPolicy)
							{
								self::logDataRetentionPolicyUpdate('edit', $origBrandArray['onlinedataretentionpolicy'], $onlineDataRetentionPolicy, $brandingCode, $gSession);
							}

							if ($origBrandArray['name'] != $brandingName)
							{
								// rename the brand folders
								$origBrandRootPath = self::getBrandRootPath($origBrandArray['name']);
								$newBrandRootPath = self::getBrandRootPath($brandingName);
								if (file_exists($origBrandRootPath))
								{
									rename($origBrandRootPath, $newBrandRootPath);
								}

								$origBrandWebRootPath = self::getBrandWebRootPath($origBrandArray['name']);
								$newBrandWebRootPath = self::getBrandWebRootPath($brandingName);

								if (file_exists($origBrandWebRootPath))
								{
									rename($origBrandWebRootPath, $newBrandWebRootPath);
								}

								self::createBrandIndexFile($brandingCode, $brandingName);

								// update the auto-update application database entries for the brand
								if ($stmt = $dbObj->prepare('UPDATE `APPLICATIONBUILD` SET `webbrandcode` = ? WHERE `webbrandcode` = ?'))
								{
									if ($stmt->bind_param('ss', $brandingCode, $origBrandArray['code']))
									{
										if ($stmt->execute())
										{
											// rename the branded auto-update applications folder
											$origAutoUpdateFolder = UtilsObj::correctPath($ac_config['FTPCLIENTSROOTPATH']) . $origBrandArray['code'];
											$newAutoUpdateFolder = UtilsObj::correctPath($ac_config['FTPCLIENTSROOTPATH']) . $brandingCode;

											if (file_exists($origAutoUpdateFolder))
											{
												rename($origAutoUpdateFolder, $newAutoUpdateFolder);
											}
										}
									}
								}

								$stmt->free_result();
								$stmt->close();
								$stmt = null;

								// update the auto-update file database entries for the brand
								if ($stmt = $dbObj->prepare('UPDATE `APPLICATIONFILES` SET `webbrandcode` = ? WHERE `webbrandcode` = ?'))
								{
									if ($stmt->bind_param('ss', $brandingCode, $origBrandArray['code']))
									{
										if ($stmt->execute())
										{
											// rename the branded auto-update masks folder
											$origAutoUpdateFolder = UtilsObj::correctPath($ac_config['FTPAPPLICATIONMASKSROOTPATH']) . $origBrandArray['code'];
											$newAutoUpdateFolder = UtilsObj::correctPath($ac_config['FTPAPPLICATIONMASKSROOTPATH']) . $brandingCode;
											if (file_exists($origAutoUpdateFolder))
											{
												rename($origAutoUpdateFolder, $newAutoUpdateFolder);
											}

											// rename the branded auto-update backgrounds folder
											$origAutoUpdateFolder = UtilsObj::correctPath($ac_config['FTPAPPLICATIONBACKGROUNDSROOTPATH']) . $origBrandArray['code'];
											$newAutoUpdateFolder = UtilsObj::correctPath($ac_config['FTPAPPLICATIONBACKGROUNDSROOTPATH']) . $brandingCode;
											if (file_exists($origAutoUpdateFolder))
											{
												rename($origAutoUpdateFolder, $newAutoUpdateFolder);
											}

											// rename the branded auto-update scrapbook pictures folder
											$origAutoUpdateFolder = UtilsObj::correctPath($ac_config['FTPAPPLICATIONSCRAPBOOKPICTURESROOTPATH']) . $origBrandArray['code'];
											$newAutoUpdateFolder = UtilsObj::correctPath($ac_config['FTPAPPLICATIONSCRAPBOOKPICTURESROOTPATH']) . $brandingCode;
											if (file_exists($origAutoUpdateFolder))
											{
												rename($origAutoUpdateFolder, $newAutoUpdateFolder);
											}

											// rename the branded auto-update frames folder
											$origAutoUpdateFolder = UtilsObj::correctPath($ac_config['FTPAPPLICATIONFRAMESROOTPATH']) . $origBrandArray['code'];
											$newAutoUpdateFolder = UtilsObj::correctPath($ac_config['FTPAPPLICATIONFRAMESROOTPATH']) . $brandingCode;
											if (file_exists($origAutoUpdateFolder))
											{
												rename($origAutoUpdateFolder, $newAutoUpdateFolder);
											}
										}
									}
								}
							}

							$brandUserType = TPX_LOGIN_BRAND_OWNER;

							if ($brandingCode != $origBrandArray['code'])
							{
								/* Update Branding code in Order Routing table */
								if ($stmt = $dbObj->prepare("UPDATE `ORDERROUTING` SET `value` = ? WHERE `rule`='0' AND `value` = ?"))
								{
									if ($stmt->bind_param('ss', $brandingCode, $origBrandArray['code']))
									{
										$stmt->execute();
									}
								}
								/* Update Branding code for Brand User */
								if ($stmt = $dbObj->prepare("UPDATE `USERS` SET `webbrandcode` = ? WHERE `webbrandcode`=? AND `usertype`=?"))
								{
									if ($stmt->bind_param('ssi', $brandingCode, $origBrandArray['code'], $brandUserType))
									{
										$stmt->execute();
									}
								}
							}

							if (($gConstants['optionms']) && ($owner != $origBrandArray['owner']))
		 					{
								/* Update sitecode and companycode for Brand user */
								if ($stmt = $dbObj->prepare("UPDATE `USERS` SET `companycode` = ? WHERE `usertype`=? AND `webbrandcode` = ?"))
								{
									if ($stmt->bind_param('sis', $companyCode, $brandUserType, $brandingCode))
									{
										$stmt->execute();
									}
								}
								/* Update the sitecode in the orderrouting table */
								if ($gConstants['optionms'])
		 						{
									if ($stmt = $dbObj->prepare("UPDATE `ORDERROUTING` SET `sitecode` = ? WHERE `rule`='0' AND `value` = ? AND `sitecode` = ?"))
									{
										if ($stmt->bind_param('sss', $owner,$brandingCode, $origBrandArray['owner']))
										{
											$stmt->execute();
										}
									}
		 						}
		 					}
		 					$stmt->free_result();
							$stmt->close();
							$stmt = null;

							DatabaseObj::updateActivityLog($gSession['ref'], 0, $gSession['userid'], $gSession['userlogin'], $gSession['username'], 0,
								'ADMIN', 'BRANDING-UPDATE', $id . ' ' . $brandingName, 1);

							// Generate the details to pass to update the CSP configuration.
							$cspDetails = [
								'brandCode' => $brandingCode,
								'displayUrl' => trim($brandingDisplayURL),
								'webUrl' => trim($brandingWebURL),
								'onlineDesignerUrl' => trim($onlineDesignerURL),
								'analytics' => ($brandingGoogleCode != ''),
								'tagmanager' => ($brandingGoogleTagManagerCCCode != ''),
							];

							// Update the CSP Config.
							self::updateCSPDetails($cspDetails);

							if ($gConstants['optiondesol']) {
								$fontListDetails = [
									'type' => UtilsObj::getPOSTParam('fontlisttype', -1),
									'fontlist' => UtilsObj::getPOSTParam('fontlist', null),
									'codes' => [('' === $brandingCode ? '__DEFAULT__' : $brandingCode)],
									'checkfield' => 'brandcode',
								];
								AdminTaopixOnlineFontLists_model::updateAssignments($fontListDetails, $ac_config);
                            }
						}
						else
						{
							// first check for a duplicate key (branding code)
							if ($stmt->errno == 1062)
							{
								$result = 'str_ErrorBrandingExists';
							}
							else
							{
								$result = 'str_DatabaseError';
								$resultParam = 'brandingEdit execute ' . $dbObj->error;
							}
						}
					}
					else
					{
						// could not bind parameters
						$result = 'str_DatabaseError';
						$resultParam = 'brandingEdit bind ' . $dbObj->error;
					}
					if ($stmt)
					{
						$stmt->free_result();
						$stmt->close();
					}
				}
				else
				{
					// could not prepare statement
					$result = 'str_DatabaseError';
					$resultParam = 'brandingEdit prepare ' . $dbObj->error;
				}

				$dbObj->close();
			}
			else
			{
				// could not open database connection
				$result = 'str_DatabaseError';
				$resultParam = 'brandingEdit connect ' . $dbObj->error;
			}

			// Update the brand image files and db records.
			if (($result == '') && (count($brandFileArray) > 0))
			{
				$updateResult = array('error' => '');

				$typeMessageArray = array(
					TPX_BRANDING_FILE_TYPE_CC_LOGO => 'str_LabelCustomerAccountLogo',
					TPX_BRANDING_FILE_TYPE_MARKETING => 'str_LabelCustomerAccountSidebar',
					TPX_BRANDING_FILE_TYPE_EMAIL_LOGO => 'str_LabelEmailLogo',
					TPX_BRANDING_FILE_TYPE_OL_LOGO => 'str_LabelOnlineDesignerLogo',
					TPX_BRANDING_FILE_TYPE_OL_LOGO_DARK => 'str_LabelOnlineDesignerLogoDark'
				);

				$resultParamArray = array();

				foreach ($brandFileArray as $typeRef => $actionArray)
				{
					switch ($typeRef)
					{
						case TPX_BRANDING_FILE_TYPE_CC_LOGO:
						case TPX_BRANDING_FILE_TYPE_MARKETING:
						case TPX_BRANDING_FILE_TYPE_EMAIL_LOGO:
						case TPX_BRANDING_FILE_TYPE_OL_LOGO:
						case TPX_BRANDING_FILE_TYPE_OL_LOGO_DARK:
						{
							if ($actionArray['action'] == 'update')
							{
								$updateResult = self::updateBrandingData($id, $typeRef, $actionArray['path']);
							}
							else if ($actionArray['action'] == 'remove')
							{
								$updateResult = self::removeBrandingData($id, $typeRef);
							}
							break;
						}
					}

					if ($updateResult['error'] != '')
					{
						$result = $updateResult['error'];

						$smarty = SmartyObj::newSmarty('AdminBranding');
						$resultParamArray[] = $smarty->get_config_vars($typeMessageArray[$typeRef]);
					}
				}

				if ($result != '')
				{
					$resultParam = implode(', ', $resultParamArray);
				}
			}

			$typeMessageArray = array(
				TPX_BRANDING_TEXT_TYPE_SIGNATURE => 'emailSignature'
			);

			// Update the branding text
			foreach ($typeMessageArray as $typeRef => $langPanelPrefix)
			{
				switch ($typeRef)
				{
					case TPX_BRANDING_TEXT_TYPE_SIGNATURE:
					{
						$enableCustomCheckID = $langPanelPrefix . 'enablecheck';
						$defaultCheckID = $langPanelPrefix . 'usedefaultcheck';
						$arrayKey = $langPanelPrefix . 'langpanel';

						$enableCustomCheck = UtilsObj::getPOSTParam($enableCustomCheckID);
						$useDefaultCheck = UtilsObj::getPOSTParam($defaultCheckID);
						$languageText = UtilsObj::getPOSTParam($arrayKey);

						$updateResult = self::updateBrandingTextData($id, $typeRef, $languageText, $useDefaultCheck, $enableCustomCheck);

						break;
					}
				}
			}
		}

		if ($result == '')
		{
			echo '{"success": true,	"msg":""}';
		}
		else
		{
			if (! ($smarty instanceof MySmarty))
			{
				$smarty = SmartyObj::newSmarty('AdminBranding');
			}
        	echo '{"success":false,	"msg":"' . $smarty->get_config_vars($result) . '. ' . $resultParam . '"}';
        }
	}

    static function brandingDelete()
    {
        $brandingList  = explode(',',$_POST['idlist']);
        $brandingCount = count($brandingList);
        $result = '';
		$resultParam = '';
		$recordID = 0;
		$notDeletedArray = array();

		global $ac_config;
        global $gSession;
        global $gConstants;

        $dbObj = DatabaseObj::getGlobalDBConnection();
        if ($dbObj)
		{
			$cspConfigBuilder = new CSPConfigBuilder();
			$removedBrandCodes = [];

        	for ($i = 0; $i < $brandingCount; $i++)
        	{
        		$brandingID = $brandingList[$i];
        		$brandArray = DatabaseObj::getBrandingFromID($brandingID);
        		$brandingCode = $brandArray['code'];

        		if (($brandingID) && ($brandingCode))
        		{
        			$canDelete = true;
        			if ($stmt = $dbObj->prepare('SELECT `id` FROM `LICENSEKEYS` WHERE `webbrandcode` = ?'))
                	{
                    	if ($stmt->bind_param('s', $brandingCode))
                    	{
                        	if ($stmt->bind_result($recordID))
                        	{
                           		if ($stmt->execute())
                           		{
                                	if ($stmt->fetch())
                                	{
                                    	/*$result = 'str_ErrorUsedInLicenseKey';*/
                                    	$notDeletedArray[] = $brandingCode;
                                    	$canDelete = false;
                                	}
                           		}
                        	}
                    	}
                    	$stmt->free_result();
                    	$stmt->close();
                    	$stmt = null;
                	}

                	if ($canDelete == true)
                	{

						// Delete the uploaded custom branding files.
						// This need to be done before the data is removed from the Branding table.
						$brandFileTypesArray = array(
							TPX_BRANDING_FILE_TYPE_CC_LOGO => '',
							TPX_BRANDING_FILE_TYPE_MARKETING => '',
							TPX_BRANDING_FILE_TYPE_EMAIL_LOGO => '',
							TPX_BRANDING_FILE_TYPE_OL_LOGO => '',
							TPX_BRANDING_FILE_TYPE_OL_LOGO_DARK => '',
						);

						foreach ($brandFileTypesArray as $fileType => $removeResult)
						{
							$brandFileTypesArray[$fileType] = self::removeBrandingData($brandingID, $fileType);
						}

	        			if ($stmt = $dbObj->prepare('DELETE FROM `BRANDING` WHERE `id` = ?'))
                    	{
                    		if ($stmt->bind_param('i', $brandingID))
                        	{
                            	if ($stmt->execute())
                            	{
                    				$brandRootPath = self::getBrandRootPath($brandingCode);
                                	$brandWebRootPath = self::getBrandWebRootPath($brandingCode);

                                	UtilsObj::deleteFolder($brandRootPath);
                                	UtilsObj::deleteFolder($brandWebRootPath);

                                	$stmt->free_result();
                                	$stmt->close();
                                	$stmt = null;

                                	// delete the auto-update application database entries for the brand
                                	if ($stmt = $dbObj->prepare('DELETE FROM `APPLICATIONBUILD` WHERE `webbrandcode` = ?'))
                                	{
                                    	if ($stmt->bind_param('s', $brandingCode))
                                    	{
                                        	if ($stmt->execute())
                                        	{
                                            	// delete the branded auto-update applications folder
                                            	UtilsObj::deleteFolder(UtilsObj::correctPath($ac_config['FTPCLIENTSROOTPATH'] . $brandingCode));
                                        	}
                                    	}
                                    	$stmt->free_result();
                                		$stmt->close();
                                		$stmt = null;
                                	}

                    				if ($gConstants['optionms'])
                                	{
                                		//delete the routing rule from ORDERROUTING table for the brand
                                		if ($stmt = $dbObj->prepare('DELETE FROM `ORDERROUTING` WHERE `value` = ?'))
                                		{
                                    		if ($stmt->bind_param('s', $brandingCode))
                                    		{
                                    			$stmt->execute();
                                    		}
                                		}
                                		$stmt->free_result();
                                		$stmt->close();
                                		$stmt = null;
                                	}

                                	// delete the auto-update application file entries for the brand
                                	if ($stmt = $dbObj->prepare('DELETE FROM `APPLICATIONFILES` WHERE `webbrandcode` = ?'))
                                	{
                                    	if ($stmt->bind_param('s', $brandingCode))
                                    	{
                                        	if ($stmt->execute())
                                        	{
                                            	// delete the branded auto-update masks folder
                                            	UtilsObj::deleteFolder(UtilsObj::correctPath($ac_config['FTPAPPLICATIONMASKSROOTPATH']) . $brandingCode);
                                            	// delete the branded auto-update backgrounds folder
                                            	UtilsObj::deleteFolder(UtilsObj::correctPath($ac_config['FTPAPPLICATIONBACKGROUNDSROOTPATH']) . $brandingCode);
                                            	// delete the branded auto-update scrapbook pictures folder
                                            	UtilsObj::deleteFolder(UtilsObj::correctPath($ac_config['FTPAPPLICATIONSCRAPBOOKPICTURESROOTPATH']) . $brandingCode);
                                        	}
                                    	}
                                    	$stmt->free_result();
                                		$stmt->close();
                                		$stmt = null;
                                	}

                                	DatabaseObj::updateActivityLog($gSession['ref'], 0, $gSession['userid'], $gSession['userlogin'], $gSession['username'], 0, 'ADMIN', 'BRANDING-DELETE', $brandingID . ' ' . $brandingCode, 1);

									// Log that we have removed the following brandcode.
									$removedBrandCodes[] = $brandingCode;
                              	}
	        				}
	        			}
        			}
        		}
        	}

			// Remove the deleted brandcode csp details.
			$cspConfigBuilder->removeCSPConfigKeys($removedBrandCodes);

			if ($gConstants['optiondesol']) {
				$fontListDetails = [
					'codes' => $removedBrandCodes,
					'checkfield' => 'brandcode',
				];
				AdminTaopixOnlineFontLists_model::removeAssignments($fontListDetails, $ac_config);
			}
        }

        $smarty = SmartyObj::newSmarty('AdminBranding');
		$title = $smarty->get_config_vars('str_TitleConfirmation');

		if ($result == '')
		{
			$message = $smarty->get_config_vars('str_BrandingDeleted');

			if (count($notDeletedArray) > 0)
			{
				$title = $smarty->get_config_vars('str_TitleWarning');
				$message = $smarty->get_config_vars('str_ErrorFollowingUsedInLicenseKey') . ' ' .join(', ', $notDeletedArray);
			}
			echo "{'success':'true', 'title':'" . UtilsObj::ExtJSEscape($title) . "', 'msg':'" . UtilsObj::ExtJSEscape($message) . "' }";
		}
		else
		{
			echo '{"success":false,	"msg":"' . $resultParam . '"}';
        }

    }

	static function unsubscribeAllUsers($pBrandCode)
	{
		global $gSession;

		$returnArray = array('result' => '', 'resultparam' => '');
		$result = '';
		$resultParam = '';
		$maxUserID = 0;

		$dbObj = DatabaseObj::getGlobalDBConnection();

        if ($dbObj)
		{
			$sql = 'SELECT MAX(`id`) FROM `USERS`';

			if ($stmt = $dbObj->prepare('SELECT MAX(`id`) FROM `USERS`'))
        	{
				if ($stmt->bind_result($maxUserID))
				{
					if ($stmt->execute())
					{
						if (! $stmt->fetch())
						{
							$result = 'str_DatabaseError';
							$resultParam = 'unsubscribeAllUsers select fetch ' . $dbObj->error;
						}
					}
					else
					{
						$result = 'str_DatabaseError';
						$resultParam = 'unsubscribeAllUsers select execute ' . $dbObj->error;
					}
				}
				else
				{
					$result = 'str_DatabaseError';
					$resultParam = 'unsubscribeAllUsers select bind_result ' . $dbObj->error;
				}

            	$stmt->free_result();
            	$stmt->close();
            	$stmt = null;
        	}
        	else
        	{
				$result = 'str_DatabaseError';
				$resultParam = 'unsubscribeAllUsers select prepare ' . $dbObj->error;
        	}

			$dbObj->close();
		}
		else
		{
			$result = 'str_DatabaseError';
			$resultParam = 'unsubscribeAllUsers no database connection';
		}

		if ($result == '')
		{
			$brandingArray = DatabaseObj::getBrandingFromCode($pBrandCode);

			DatabaseObj::createEvent('TAOPIX_MASSUNSUBSCRIBE', '', '', $pBrandCode, date('Y-m-d') . ' 23:59:59', 0,	$pBrandCode,
										$brandingArray['applicationname'], $brandingArray['displayurl'], 0, $maxUserID, $gSession['userlogin'], $gSession['username'], $gSession['ref'], 0, 0, $gSession['userid'], '', '', 0);
		}

		$returnArray['result'] = $result;
		$returnArray['resultparam'] = $resultParam;

		return $returnArray;
	}

	/**
	 * Gets the requested brand file. Returns the custom asset if there is one, the asset in the branding folder if there is no custom asset and
	 * in the root if no branding asset was found.
	 * The file may also come from the tmp folder if the asset has just been uploaded and not yet saved against the brand.
	 * Defaults to the nopreview.gif image if no brand file can be found.
	 *
	 * @global array $ac_config MediaAlbumWeb config values.
	 * @param int $pBrandID ID of the brand to get the asset for.
	 * @param int $pTypeRef The asset to retrieve.
	 * @param boolean $pUseTempFile Use the file from the tmp folder if the asset has just been uploaded but not saved.
	 */
	static function getBrandFilePreview($pBrandID, $pTypeRef, $pUseTempFile)
	{
    	global $ac_config;

		$brandFileInfo = array();

		if ($pUseTempFile != '')
		{
			$tmpFile = sys_get_temp_dir() . DIRECTORY_SEPARATOR . $pUseTempFile;

			// Load the temp file to display as the preview.
			if (file_exists($tmpFile))
			{
				$imageData = getimagesize($tmpFile);

				$mimeType = $imageData['mime'];

				header('Content-type:' . $mimeType);
			}

			echo file_get_contents($tmpFile);
		}
		else
		{
			// Get the information for the brand asset.
			$brandFileInfo = DatabaseObj::getBrandAssetData($pBrandID, $pTypeRef, true);

			if ($brandFileInfo['result'] == '')
			{
				if ($brandFileInfo['data']['path'] != '')
				{
					if ($brandFileInfo['data']['id'] == 0)
					{
						// Default files found
						$location = $brandFileInfo['data']['path'];
					}
					else
					{
						// Customised files found.
						$location = UtilsObj::correctPath($ac_config['CONTROLCENTREASSETSPATH'], DIRECTORY_SEPARATOR, true) . 'images/' . $brandFileInfo['data']['path'];
					}

					header('Content-type:' . $brandFileInfo['data']['mime']);

					echo file_get_contents($location);
				}
			}
			else
			{
				header('Content-type: image/gif');
				echo file_get_contents($ac_config['WEBURL'] . 'images/admin/nopreview.gif');
			}
		}
	}

	/**
	 * Check if a brand asset has been uploaded for the selected file.
	 * Returns the id of the custom asset if there is one.
	 * Defaults to the 0 if no brand file can be found due to no asset uploaded or an error.
	 *
	 * @param int $pBrandID ID of the brand to get the asset for.
	 * @param int $pTypeRef The asset to retrieve.
	 */
	static function checkForUploadedBrandFile($pBrandID, $pTypeRef)
	{
		// Default to 0, this will be returned if an error has occurred when retrieving the uploaded asset data.
		$result = 0;

		// Get the information about the brand asset.
		$brandFileInfo = DatabaseObj::getBrandAssetData($pBrandID, $pTypeRef, false);

		// No error was reported, get the id of the uploaded brand asset.
		if ($brandFileInfo['result'] == '')
		{
			// Return the id of the uploaded asset.
			// This would be 0 if no asset has been uploaded.
			$result = $brandFileInfo['data']['id'];
		}

		return $result;
	}

	/**
	 * Uploads the brand file to a temporary folder.
	 *
	 * @param int $pTypeRef The type of asset that has been uploaded.
	 * @param array $pFileData Upload data from the $_FILES array.
	 * @return array
	 */
	static function uploadBrandFile($pTypeRef, $pFileData)
	{
        $resultArray = UtilsObj::getReturnArray();
        $result = '';
        $resultParam = '';
		$tempFileData = array();
        $fileType = strtolower($_FILES['preview']['type']);
		$validFileTypes = array();

		// Test the file uploaded is valid.
		switch ($pTypeRef)
		{
			case TPX_BRANDING_FILE_TYPE_CC_LOGO:
			case TPX_BRANDING_FILE_TYPE_MARKETING:
			case TPX_BRANDING_FILE_TYPE_EMAIL_LOGO:
			case TPX_BRANDING_FILE_TYPE_OL_LOGO:
			case TPX_BRANDING_FILE_TYPE_OL_LOGO_DARK:
			{
				$validFileTypes = array('image/jpeg', 'image/pjpeg', 'image/png', 'image/x-png');
				break;
			}
		}

		$validType = in_array($fileType, $validFileTypes);

		 // Make sure that the file is a valid type.
        if ($validType)
		{
            // Create a new temporary file.
            $customBrandFile = tempnam(sys_get_temp_dir(), 'CBF');

            if (move_uploaded_file($pFileData['tmp_name'], $customBrandFile))
            {
				$tempFileData['tmppath'] = basename($customBrandFile);
				$tempFileData['name'] = $pFileData['name'];
				$tempFileData['mime'] = $pFileData['type'];

				$imageData = getimagesize($customBrandFile);

				$currentSizeInfo = ['width' => $imageData[0], 'height' => $imageData[1]];
				$recommended = UtilsObj::getBrandAssetDetails($pTypeRef);
				$scaled = DatabaseObj::autoScaleImage($imageData['mime'], $customBrandFile, $currentSizeInfo, $recommended['maximums']);

			}
        }
        else
        {
            $result = 'str_ErrorUploadInvalidFileType';
        }

        $resultArray['result'] = $result;
        $resultArray['resultparam'] = $resultParam;
        $resultArray['data'] = $tempFileData;

        return $resultArray;
    }

	/**
	 * Inserts or updates the ASSETDATA table with the new or updated brand asset file and creates a link in the BRANDASSETLINK table if it is a new record
	 * and then moves the temporary file into the correct location in the TaopixData folder.
	 *
	 * @param int $pBrandID The ID of the brand to link the brand asset to.
	 * @param int $pTypeRef The type of asset that has been updated.
	 * @param string $pTempPath The path to where the temporary file is located.
	 * @return array If there was an error when updating the records or moving the file.
	 */
	static function updateBrandingData($pBrandID, $pTypeRef, $pTempPath)
	{
		global $ac_config;

		$resultArray = UtilsObj::getReturnArray();
		$error = '';
		$validFileTypes = array('image/jpeg' => '.jpeg', 'image/pjpeg' => '.jpeg', 'image/png' => '.png', 'image/x-png' => '.png');

		// Generate new file name.
		$tempFile = sys_get_temp_dir() . DIRECTORY_SEPARATOR . $pTempPath;
		$fileExt = '';
		$newFilePath = '';
		$fileToDelete = '';
		$updateID = '';
		$insertID = 0;
		$retrievalID = '';
		$iType = TPX_ASSETTYPE_IMAGE;

		if (file_exists($tempFile))
		{
			$imageData = getimagesize($tempFile);

			$fileExt = $validFileTypes[$imageData['mime']];

			$collisionCount = 0;
			while (($newFilePath == '') && ($collisionCount < 10))
			{
				// Generate new file name.
				$newFilePath = md5($pBrandID . $pTypeRef . $pTempPath . microtime(true) . $collisionCount);
				$retrievalID = strtoupper('cbf' . $newFilePath);
				$newFilePath = substr_replace($newFilePath, '/', 2, 0);
				$newFilePath .= $fileExt;

				$checkPath = UtilsObj::correctPath($ac_config['CONTROLCENTREASSETSPATH'], DIRECTORY_SEPARATOR, true) . 'images' . DIRECTORY_SEPARATOR . $newFilePath;

				// Check if the file name already exists.
				if (file_exists($checkPath))
				{
					$newFilePath = '';
				}
				$collisionCount++;
			}

			// Check if a record already exists and get the file name and asset ID.
			$existingData = DatabaseObj::getBrandAssetData($pBrandID, $pTypeRef, false);

			if ($existingData['result'] == '')
			{
				if ($existingData['data']['id'] > 0)
				{
					// An asset has been found.
					$fileToDelete = $existingData['data']['path'];
					$updateID = $existingData['data']['id'];
				}
			}

			// Set up query to update the assetdata table.
			$sqlAD = 'INSERT INTO `ASSETDATA` (`id`, `retrievalid`, `datecreated`, `datemodified`, `name`, `assettype`, `previewtype`, `previewwidth`, `previewheight`)
					VALUES (?, ?, NOW(), NOW(), ?, ?, ?, ?, ?)
					ON DUPLICATE KEY
					UPDATE `datemodified` = NOW(), `name` = ?, `previewtype` = ?, `previewwidth` = ?, `previewheight` = ?;';

			$bindParamsAD = array();
			$bindParamsAD[0] = 'issisiissii';
			$bindParamsAD[1] = $updateID;
			$bindParamsAD[2] = $retrievalID;
			$bindParamsAD[3] = $newFilePath;
			$bindParamsAD[4] = $iType;
			$bindParamsAD[5] = $imageData['mime'];
			$bindParamsAD[6] = $imageData[0];
			$bindParamsAD[7] = $imageData[1];
			$bindParamsAD[8] = $newFilePath;
			$bindParamsAD[9] = $imageData['mime'];
			$bindParamsAD[10] = $imageData[0];
			$bindParamsAD[11] = $imageData[1];

			// Set up query to update the brandassetlink table.
			$sqlBAL = 'INSERT INTO `BRANDASSETLINK` (`brandid`, `assetdataid`, `objecttype`)
					VALUES (?, ?, ?)';

			$bindParamsBAL = array();
			$bindParamsBAL[0] = 'iii';
			$bindParamsBAL[1] = $pBrandID;
			$bindParamsBAL[2] = 0; // Will require updating based on result of assetdata insert.
			$bindParamsBAL[3] = $pTypeRef;

			$dbObj = DatabaseObj::getGlobalDBConnection();

			if ($dbObj)
			{
				$dbObj->query('START TRANSACTION');

				// Update / Insert asset data.
				$stmt = $dbObj->prepare($sqlAD);
				if ($stmt)
				{
					$bindOk = call_user_func_array(array(&$stmt, 'bind_param'), UtilsObj::makeValuesReferenced($bindParamsAD));
					if ($bindOk)
					{
						if ($stmt->execute())
						{
							$insertID = $dbObj->insert_id;
						}
						else
						{
							$error = 'str_DatabaseError';
							$errorParam = __FUNCTION__ . ' execute: ' . $dbObj->error;
						}
					}
					else
					{
						$error = 'str_DatabaseError';
						$errorParam = __FUNCTION__ . ' bind_param: ' . $dbObj->error;
					}
					$stmt->free_result();
					$stmt->close();
					$stmt = null;
				}
				else
				{
					$error = __FUNCTION__ . ' prepare: ' . $dbObj->error;
				}

				if (($error == '') && ($insertID > 0) && ($updateID == ''))
				{
					// Update / Insert brand asset link.
					$bindParamsBAL[2] = $insertID;

					$stmt = $dbObj->prepare($sqlBAL);
					if ($stmt)
					{
						$bindOk = call_user_func_array(array(&$stmt, 'bind_param'), UtilsObj::makeValuesReferenced($bindParamsBAL));
						if ($bindOk)
						{
							if ($stmt->execute())
							{
								$insertID = $dbObj->insert_id;
							}
							else
							{
								$error = 'str_DatabaseError';
								$errorParam = __FUNCTION__ . ' execute: ' . $dbObj->error;
							}
						}
						else
						{
							$error = 'str_DatabaseError';
							$errorParam = __FUNCTION__ . ' bind_param: ' . $dbObj->error;
						}
						$stmt->free_result();
						$stmt->close();
						$stmt = null;
					}
					else
					{
						$error = 'str_DatabaseError';
						$errorParam = __FUNCTION__ . ' prepare: ' . $dbObj->error;
					}
				}

				// Move the temp file into the correct directory.
				if ($error == '')
				{
					$error = self::moveUploadedBrandFile($tempFile, $newFilePath);
				}

				if (($pTypeRef == TPX_BRANDING_FILE_TYPE_OL_LOGO || $pTypeRef == TPX_BRANDING_FILE_TYPE_OL_LOGO_DARK) && ($error == ''))
				{
					// Update the online database.
					global $ac_config;
					require_once('../libs/internal/curl/Curl.php');

					$brandInfo = DatabaseObj::getBrandingFromID($pBrandID);
					$brandCode = $brandInfo['code'];

					$updateData = array();
					$updateData['typeref'] = $pTypeRef;
					$updateData['name'] = $newFilePath;
					$updateData['brandcode'] = $brandCode;
					$updateData['width'] = $imageData[0];
					$updateData['height'] = $imageData[1];

					$dataToEncrypt = array('cmd' => 'UPDATEBRANDASSET', 'data' => $updateData);

					$onlineBrandAssetData = CurlObj::sendByPut($ac_config['TAOPIXONLINEURL'], 'AdminAPI.callback', $dataToEncrypt);

					if ($onlineBrandAssetData['error'] != '')
					{
						$error = $onlineBrandAssetData['error'];
					}
					else
					{
						if ($onlineBrandAssetData['data']['error'] != '')
						{
							$error = $onlineBrandAssetData['data']['error'];
						}
					}
				}

				if ($error == '')
				{
					// commit
					if (! $dbObj->query('COMMIT'))
					{
						// if the commit fails, rollback
						$dbObj->query('ROLLBACK');

						$error = 'str_DatabaseError';
						$errorParam = __FUNCTION__ . ' commit ' . $dbObj->error;
					}
					else
					{
						// Cleanup the old branding file, if it was found.
						if ($fileToDelete != '')
						{
							self::deleteUploadedBrandFile($fileToDelete);
						}
					}
				}
				else
				{
					// rollback
					$dbObj->query('ROLLBACK');

					// Cleanup the new branding file, if it was found.
					if ($newFilePath != '')
					{
						self::deleteUploadedBrandFile($newFilePath);
					}
				}

				$dbObj->close();
			}
			else
			{
				$error = 'str_DatabaseError';
				$errorParam = __FUNCTION__ . ' connection: Unable to connect to database';
			}
		}

		$resultArray['error'] = $error;

		return $resultArray;
	}

	/**
	 * Inserts or updates the ASSETDATA table with the new or updated brand asset file and creates a link in the BRANDASSETLINK table if it is a new record
	 * and then moves the temporary file into the correct location in the TaopixData folder.
	 *
	 * @param int $pBrandID The ID of the brand to link the brand asset to.
	 * @param int $pTypeRef The type of asset that has been updated.
	 * @param string $pStringData Text for custom signature.
	 * @param boolean $pUseDefault Use the text from the default brand.
	 * @param boolean $pEnableCustomSig Use a custom signature.
	 *
	 * @return array If there was an error when updating the records or moving the file.
	 */
	static function updateBrandingTextData($pBrandID, $pTypeRef, $pStringData, $pUseDefault, $pEnableCustomSig)
	{
		global $ac_config;

		$resultArray = UtilsObj::getReturnArray();
		$error = '';
		$errorParam = '';

		$iType = TPX_ASSETTYPE_EMAIL_TEXT;
		$newName = 'EMAIL SIGNATURE';

		// Generate new retrieval ID.
		$retrievalID = md5($pBrandID . $pTypeRef);
		$retrievalID = strtoupper('cbt' . $retrievalID);

		$updateIDArray = array('ad' => '', 'bal' => '');

		// Check if a record already exists and get the file name and asset ID.
		$existingData = DatabaseObj::getBrandAssetIDData($pBrandID, $pTypeRef, false);

		if ($existingData['result'] == '')
		{
			$updateIDArray = $existingData['data'];
		}

		$dbObj = DatabaseObj::getGlobalDBConnection();

		if ($dbObj)
		{
			$dbObj->query('START TRANSACTION');

			// Update / remove the data from the Asset data table.
			if ($pUseDefault)
			{
				// Remove the data if it exists
				if (($updateIDArray['ad'] != 0) && ($updateIDArray['ad'] != ''))
				{
					$sqlAD = 'DELETE FROM `ASSETDATA` WHERE `id` = ?';
					$bindParamsAD = array('i', $updateIDArray['ad']);

					// Update / Insert asset data.
					$stmt = $dbObj->prepare($sqlAD);
					if ($stmt)
					{
						$bindOk = call_user_func_array(array(&$stmt, 'bind_param'), UtilsObj::makeValuesReferenced($bindParamsAD));
						if ($bindOk)
						{
							if (! $stmt->execute())
							{
								$error = 'str_DatabaseError';
								$errorParam = __FUNCTION__ . ' execute: ' . $dbObj->error;
							}
						}
						else
						{
							$error = 'str_DatabaseError';
							$errorParam = __FUNCTION__ . ' bind_param: ' . $dbObj->error;
						}
						$stmt->free_result();
						$stmt->close();
						$stmt = null;
					}
					else
					{
						$error = 'str_DatabaseError';
						$errorParam = __FUNCTION__ . ' prepare: ' . $dbObj->error;
					}
				}

				// Set the new asset data id for the brand asset link table
				$updateIDArray['ad'] = 0;
			}
			else
			{
				// Set up query to update the assetdata table.
				$sqlAD = 'INSERT INTO `ASSETDATA` (`id`, `retrievalid`, `datecreated`, `datemodified`, `name`, `assettype`, `data`, `previewtype`)
						VALUES (?, ?, NOW(), NOW(), ?, ?, ?, ?)
						ON DUPLICATE KEY
						UPDATE `datemodified` = NOW(), `data` = ?;';

				$bindParamsAD = array();
				$bindParamsAD[0] = 'ississs';
				$bindParamsAD[1] = $updateIDArray['ad'];
				$bindParamsAD[2] = $retrievalID;
				$bindParamsAD[3] = $newName;
				$bindParamsAD[4] = $iType;
				$bindParamsAD[5] = $pStringData;
				$bindParamsAD[6] = 'text';
				$bindParamsAD[7] = $pStringData;

				// Update / Insert asset data.
				$stmt = $dbObj->prepare($sqlAD);
				if ($stmt)
				{
					$bindOk = call_user_func_array(array(&$stmt, 'bind_param'), UtilsObj::makeValuesReferenced($bindParamsAD));
					if ($bindOk)
					{
						if ($stmt->execute())
						{
							// Set the updated asset data id.
							$updateIDArray['ad'] = $dbObj->insert_id;
						}
						else
						{
							$error = 'str_DatabaseError';
							$errorParam = __FUNCTION__ . ' execute: ' . $dbObj->error;
						}
					}
					else
					{
						$error = 'str_DatabaseError';
						$errorParam = __FUNCTION__ . ' bind_param: ' . $dbObj->error;
					}
					$stmt->free_result();
					$stmt->close();
					$stmt = null;
				}
				else
				{
					$error = 'str_DatabaseError';
					$errorParam = __FUNCTION__ . ' prepare: ' . $dbObj->error;
				}
			}

			if ($error == '')
			{
				// Update the brand asset link table.
				// Set up query to update the brandassetlink table.
				$sqlBAL = 'INSERT INTO `BRANDASSETLINK` (`id`, `brandid`, `assetdataid`, `objecttype`, `enabled`)
						VALUES (?, ?, ?, ?, ?)
						ON DUPLICATE KEY
						UPDATE `assetdataid` = ?, `enabled` = ?';

				$bindParamsBAL = array();
				$bindParamsBAL[0] = 'iiiiiii';
				$bindParamsBAL[1] = $updateIDArray['bal'];
				$bindParamsBAL[2] = $pBrandID;
				$bindParamsBAL[3] = $updateIDArray['ad'];
				$bindParamsBAL[4] = $pTypeRef;
				$bindParamsBAL[5] = ($pEnableCustomSig) ? 1 : 0;
				$bindParamsBAL[6] = $updateIDArray['ad'];
				$bindParamsBAL[7] = ($pEnableCustomSig) ? 1 : 0;

				$stmt = $dbObj->prepare($sqlBAL);
				if ($stmt)
				{
					$bindOk = call_user_func_array(array(&$stmt, 'bind_param'), UtilsObj::makeValuesReferenced($bindParamsBAL));
					if ($bindOk)
					{
						if (! $stmt->execute())
						{
							$error = 'str_DatabaseError';
							$errorParam = __FUNCTION__ . ' execute: ' . $dbObj->error;
						}
					}
					else
					{
						$error = 'str_DatabaseError';
						$errorParam = __FUNCTION__ . ' bind_param: ' . $dbObj->error;
					}
					$stmt->free_result();
					$stmt->close();
					$stmt = null;
				}
				else
				{
					$error = 'str_DatabaseError';
					$errorParam = __FUNCTION__ . ' prepare: ' . $dbObj->error;
				}
			}

			if ($error == '')
			{
				// commit
				if (! $dbObj->query('COMMIT'))
				{
					// if the commit fails, rollback
					$dbObj->query('ROLLBACK');

					$error = 'str_DatabaseError';
					$errorParam = __FUNCTION__ . ' commit ' . $dbObj->error;
				}
			}
			else
			{
				// rollback
				$dbObj->query('ROLLBACK');
			}

			$dbObj->close();
		}
		else
		{
			$error = 'str_DatabaseError';
			$errorParam = __FUNCTION__ . ' connection: Unable to connect to database';
		}

		$resultArray['error'] = $error;

		return $resultArray;
	}

	/**
	 * Deletes the records in the ASSETSDATA and BRANDASSETLINK tables and deletes the custom file from the TaopixData folder. Also attempts to delete
	 * the folder if empty.
	 *
	 * @global array $ac_config MediaAlbumWeb config values.
	 * @param int $pBrandID ID of the brand to remove the custom brand asset from.
	 * @param int $pTypeRef The brand asset to remove.
	 * @return array If there was an error when removing the file.
	 */
	static function removeBrandingData($pBrandID, $pTypeRef)
	{
		global $ac_config;

		$resultArray = UtilsObj::getReturnArray();
		$error = '';
		$errorParam = '';
		$deleteID = 0;
		$fileToDelete = '';
		$pathToDelete = '';

		// Check if a record already exists and get the file name and asset ID.
		$existingData = DatabaseObj::getBrandAssetData($pBrandID, $pTypeRef, false);

		if (($existingData['result'] == '') && ($existingData['data']['id'] > 0))
		{
			$fileToDelete = $existingData['data']['name'];
			$deleteID = $existingData['data']['id'];
			$pathToDelete = UtilsObj::correctPath($ac_config['CONTROLCENTREASSETSPATH'], DIRECTORY_SEPARATOR, true) . 'images' . DIRECTORY_SEPARATOR . $existingData['data']['path'];
		}

		if ($deleteID > 0)
		{
			// Set up query to remove the brandassetlink table.
			$sqlBAL = 'DELETE FROM `BRANDASSETLINK` WHERE `assetdataid` = ?';

			// Set up query to remove the assetdata table.
			$sqlAD = 'DELETE FROM `ASSETDATA` WHERE `id` = ?';

			$dbObj = DatabaseObj::getGlobalDBConnection();

			if ($dbObj)
			{
				$dbObj->query('START TRANSACTION');

				// Delete brand asset link data.
				$stmt = $dbObj->prepare($sqlBAL);
				if ($stmt)
				{
					if ($stmt->bind_param('i', $deleteID))
					{
						if (! $stmt->execute())
						{
							$error = 'str_DatabaseError';
							$errorParam = __FUNCTION__ . ' execute: ' . $dbObj->error;
						}
					}
					else
					{
						$error = 'str_DatabaseError';
						$errorParam = __FUNCTION__ . ' bind_param: ' . $dbObj->error;
					}

					$stmt->close();
					$stmt = null;
				}
				else
				{
					$error = 'str_DatabaseError';
					$errorParam = __FUNCTION__ . ' prepare: ' . $dbObj->error;
				}

				if ($error == '')
				{
					// Delete asset data.
					$stmt = $dbObj->prepare($sqlAD);
					if ($stmt)
					{
						if ($stmt->bind_param('i', $deleteID))
						{
							if (! $stmt->execute())
							{
								$error = 'str_DatabaseError';
								$errorParam = __FUNCTION__ . ' execute: ' . $dbObj->error;
							}
						}
						else
						{
							$error = 'str_DatabaseError';
							$errorParam = __FUNCTION__ . ' bind_param: ' . $dbObj->error;
						}
						$stmt->close();
						$stmt = null;
					}
					else
					{
						$error = 'str_DatabaseError';
						$errorParam = __FUNCTION__ . ' prepare: ' . $dbObj->error;
					}
				}

				if (($pTypeRef == TPX_BRANDING_FILE_TYPE_OL_LOGO || $pTypeRef == TPX_BRANDING_FILE_TYPE_OL_LOGO_DARK) && ($error == ''))
				{
					// Send remove command to online.
					// Update the online database.
					global $ac_config;
					require_once('../libs/internal/curl/Curl.php');

					$brandInfo = DatabaseObj::getBrandingFromID($pBrandID);
					$brandCode = $brandInfo['code'];

					$updateData = array();
					$updateData['typeref'] = $pTypeRef;
					$updateData['brandcode'] = $brandCode;

					$dataToEncrypt = array('cmd' => 'REMOVEBRANDASSET', 'data' => $updateData);

					$onlineBrandAssetData = CurlObj::sendByPut($ac_config['TAOPIXONLINEURL'], 'AdminAPI.callback', $dataToEncrypt);

					if ($onlineBrandAssetData['error'] != '')
					{
						$error = $onlineBrandAssetData['error'];
					}
					else
					{
						if ($onlineBrandAssetData['data']['error'] != '')
						{
							$error = $onlineBrandAssetData['data']['error'];
						}
					}
				}

				if ($error == '')
				{
					// Commit.
					if (! $dbObj->query('COMMIT'))
					{
						// If the commit fails, rollback.
						$dbObj->query('ROLLBACK');

						$error = 'str_DatabaseError';
						$errorParam = __FUNCTION__ . ' commit ' . $dbObj->error;
					}
					else
					{
						// Delete the file if it exists.
						if (file_exists($pathToDelete))
						{
							self::deleteUploadedBrandFile($fileToDelete);
						}
					}
				}
				else
				{
					// Rollback.
					$dbObj->query('ROLLBACK');
				}

				$dbObj->close();
			}
			else
			{
				$error = 'str_DatabaseError';
				$errorParam = __FUNCTION__ . ' connection: Unable to connect to database';
			}

		}

		$resultArray['error'] = $error;
		$resultArray['errorparam'] = $errorParam;

		return $resultArray;
	}


	/**
	 * Moves the temporary brand asset file into the correct location, also creating the directory if necessary.
	 *
	 * @global array $ac_config MediaAlbumWeb config values.
	 * @param string $pTempPath Location of the temporary file to move.
	 * @param string $pDestinationPath Location to move the file to.
	 * @return string Empty if no errors, else contains the error string key.
	 */
	static function moveUploadedBrandFile($pTempPath, $pDestinationPath)
	{
		global $ac_config;

		$modifiedPath = UtilsObj::correctPath($ac_config['CONTROLCENTREASSETSPATH'], DIRECTORY_SEPARATOR, true) . 'images' . DIRECTORY_SEPARATOR . $pDestinationPath;

		return UtilsObj::moveUploadedFile($pTempPath, $modifiedPath);
	}

	/**
	 * Deletes the brand asset file. Also attempts to remove the
	 *
	 * @global array $ac_config MediaAlbumWeb config values.
	 * @param string $pFileToDelete File name and folder of the file to delete.
	 */
	static function deleteUploadedBrandFile($pFileToDelete)
	{
		global $ac_config;

		$pFileToDelete = UtilsObj::correctPath($ac_config['CONTROLCENTREASSETSPATH'], DIRECTORY_SEPARATOR, true) . 'images' . DIRECTORY_SEPARATOR . $pFileToDelete;

		if (@unlink($pFileToDelete))
		{
			// Try to delete the directory, it will fail if there are other files in the directory.
			@rmdir(dirname($pFileToDelete));
		}
	}

	/**
	 * Logs the change in data retention policy in the activity log.
	 *
	 * @param string $pMode Mode in which we are editing the brand, this is either add or edit.
	 * @param int $pOrigDataPolicy Id for the original policy
	 * @param int $pNewDataPolicy Id for the new policy.
	 * @param string $pBrandCode Brand code.
	 * @param array $pSession The global session.
	 */
	static function logDataRetentionPolicyUpdate($pMode, $pOrigDataPolicy, $pNewDataPolicy, $pBrandCode, $pSession)
	{
		$activityLogActionNotes = '';
		$brandCode = ($pBrandCode == '') ? 'Default' : $pBrandCode;

		if ($pNewDataPolicy == 0)
		{
			// The data retention policy is set to none.
			$origPolicy = self::getDataRetentionPolicyCode($pOrigDataPolicy);

			if ($origPolicy['error'] == false)
			{
				$activityLogActionNotes = 'Removed Data Retention Policy ' . $origPolicy['code'] . ' for brand ' . $brandCode;
			}
		}
		else
		{
			// The data retention policy is set to a new policy.
			$newPolicyCode = self::getDataRetentionPolicyCode($pNewDataPolicy);

			if ($newPolicyCode['error'] == false)
			{
				if ($pOrigDataPolicy != 0)
				{
					// The original policy was set to something.
					$origPolicy = self::getDataRetentionPolicyCode($pOrigDataPolicy);

					if ($origPolicy['error'] == false)
					{
						$activityLogActionNotes = 'Switched Data Retention Policy from ' . $origPolicy['code'] . ' to ' . $newPolicyCode['code'] . ' for brand ' . $brandCode;
					}
				}
				else
				{
					// There was no policy set previously.
					$activityLogActionNotes = 'Assigned Data Retention Policy ' . $newPolicyCode['code'] . ' for brand ' . $brandCode;
				}
			}
		}

		/*
		 * If the activityLogActionNotes is blank we have errored out when getting the policy code.
		 * We do not pass this upstream as the brand has already been updated at this point.
		 */
		if ($activityLogActionNotes != '')
		{
			$actionCode = (strtolower($pMode) == 'edit') ? 'BRANDING-UPDATE-DATA-RETENTION' : 'BRANDING-ADD-DATA-RETENTION';
			DatabaseObj::updateActivityLog($pSession['ref'], 0, $pSession['userid'], $pSession['userlogin'], $pSession['username'], 0,
					'ADMIN', $actionCode, $activityLogActionNotes, 1);
		}
	}

	/**
	 * Returns an array with the policy code in the data key of the returned array.
	 *
	 * @param int $pPolicyId id for the policy we want the code for.
	 * @return array
	 */
	static function getDataRetentionPolicyCode($pPolicyId)
	{
		$dbObj = DatabaseObj::getGlobalDBConnection();
		$returnArray = array(
			'error' => false,
			'code' => ''
		);

		if ($dbObj)
		{
			$code = '';
			$query = 'SELECT `code` FROM `DATAPOLICIES` WHERE `id` = ?';

			$stmt = $dbObj->prepare($query);
			if ($stmt)
			{
				if ($stmt->bind_param('i', $pPolicyId))
				{
					if (! $stmt->execute())
					{
						$returnArray['error'] = true;
					}
					else
					{
						if ($stmt->store_result())
						{
							if ($stmt->bind_result($code))
							{
								if ($stmt->fetch())
								{
									// Assign
									$returnArray['code'] = $code;
								}
								else
								{
									// Could not fetch result.
									$returnArray['error'] = true;
								}
							}
							else
							{
								// Could not bind result.
								$returnArray['error'] = true;
							}
						}
					}
				}
				else
				{
					// Unable to bind params.
					$returnArray['error'] = true;
				}
			}
			else
			{
				// Unable to prepare statement.
				$returnArray['error'] = true;
			}
		}
		else
		{
			// No database connection.
			$returnArray['error'] = true;
		}

		return $returnArray;
	}

	public static function updateCSPDetails($pDetails)
	{
		$cspConfigBuilder = new CSPConfigBuilder();
		$cspBrandCode = $pDetails['brandCode'] != '' ? $pDetails['brandCode'] : 'DEFAULT';
		$cspConfigArray = [];

		$cspConfigArray[$cspBrandCode] = [
			'urls' => [
				'displayUrl' => $cspConfigBuilder->parseUrl($pDetails['displayUrl']),
				'webUrl' => $cspConfigBuilder->parseUrl($pDetails['webUrl']),
				'onlineDesignerUrl' => $cspConfigBuilder->parseUrl($pDetails['onlineDesignerUrl']),
			],
			'analytics' => $pDetails['analytics'],
			'tagmanager' => $pDetails['tagmanager'],
		];

		if ($pDetails['displayUrl'] == '' || $pDetails['webUrl'] == '' || $pDetails['onlineDesignerUrl'] == '')
		{
			$defaultBrand = DatabaseObj::getBrandingFromCode('');

			if ($pDetails['displayUrl'] == '')
			{
				$cspConfigArray[$cspBrandCode]['urls']['displayUrl'] = $cspConfigBuilder->parseUrl(trim($defaultBrand['displayurl']));
			}

			if ($pDetails['webUrl'] == '')
			{
				$cspConfigArray[$cspBrandCode]['urls']['webUrl'] = $cspConfigBuilder->parseUrl(trim($defaultBrand['weburl']));
			}

			if ($pDetails['onlineDesignerUrl'] == '')
			{
				$cspConfigArray[$cspBrandCode]['urls']['onlineDesignerUrl'] = $cspConfigBuilder->parseUrl(trim($defaultBrand['onlinedesignerurl']));
			}
		}

		$cspConfigBuilder->buildCSPConfig($cspConfigArray);
	}

	public static function downloadKey(bool $regenerateIV = false, string $IV = '')
	{
        $dbObj = DatabaseObj::getGlobalDBConnection();
        if (!$dbObj) {
            return;
        }

		$brandID = $_GET['id'];
		$brandCode = $_GET['code'];
		$brandName = $_GET['name'];
		$URL = $_GET['URL'];
        $apiUrl = $_GET['APIURL'];
		$ciphering = "aes-128-cbc";
		$options = 0;
        $systemConfig = DatabaseObj::getSystemConfig();
        $systemKey = $systemConfig['systemkey'];
		$encryption_iv = '';

		// if IV is supplied, use that otherwise find/generate iv
		if ('' === $IV) {
			// check if there is an iv in the db already
			if (!$regenerateIV) {
				$stmt = $dbObj->prepare('SELECT `onlineappkeyentropyvalue` FROM `BRANDING` WHERE `id` = ?');
				if ($stmt) {
					if ($stmt->bind_param('i', $brandID)) {
						if ($stmt->execute()) {
							$stmt->bind_result($encryption_iv);
							$stmt->fetch();
							$stmt->close();
							$stmt = null;
							$encryption_iv = \hex2bin($encryption_iv);
						}
					}
				}
			}

			// if a new IV needs to be created or there isnt currently one in the DB, generate one and save it
			if ($regenerateIV || '' === $encryption_iv) {
				$encryption_iv = \openssl_random_pseudo_bytes(\openssl_cipher_iv_length($ciphering));
				$stmt = $dbObj->prepare('UPDATE `BRANDING` SET `onlineappkeyentropyvalue` = ?, `datelastmodified` = CURRENT_TIMESTAMP WHERE `id` = ?');
				if ($stmt) {
					$storeIv = \bin2hex($encryption_iv);
					if ($stmt->bind_param('si', $storeIv, $brandID)) {
						if ($stmt->execute()) {

                            self::applyBrandUIConfigToOnline($brandID, 'update');

                            $stmt->free_result();
							$stmt->close();
							$stmt = null;
						}
					}
				}
			}
		} else {
			$encryption_iv = $IV;
		}

		$encryption_key = \openssl_digest($systemKey, 'sha256', TRUE);
        $encryption = \openssl_encrypt('BRANDCODE<'. $brandCode . '>' . $URL, $ciphering, $encryption_key, $options, $encryption_iv);
		//Make the file here
		\header('Content-Type: application/json');
		\header('Content-Disposition: Attachment; filename=config.js');

		\header('Pragma: no-cache');
		\header('Expires: 0');
	   	echo 'window.config = ' . \json_encode(['baseURL' => $apiUrl, 'pubKey' => $encryption, 'brand' => ['code' => $brandCode, 'name' => $brandName]], JSON_UNESCAPED_SLASHES);
	}

    private static function applyBrandUIConfigToOnline(int $brandId, string $method): bool
    {
        global $ac_config;
        $verify = UtilsObj::getCurlPEMFilePath();

        $client = new Client([
            'base_uri' => UtilsObj::correctPath($ac_config['WEBURL']),
            'verify' => $verify
        ]);

		try {
			$response = $client->post('api/brand/applyBrandUIConfig', [
				'json' => [
					'brandId' => $brandId,
					'endpoint' => $method
				]
			]);
		} catch(Throwable $e){
			error_log($e->getMessage());
			return false;
		}

       if (200 === $response->getStatusCode()) {
        return true;
       }

       return false;
    }
}
?>
