<?php

require_once('../OnlineAPI/OnlineAPI_model.php');
require_once('../OnlineAPI/OnlineAPI_view.php');
require_once('../Utils/Utils.php');
require_once('../Utils/UtilsDeviceDetection.php');
require_once('../Utils/UtilsSmarty.php');

use GuzzleHttp\Client;
use Taopix\ControlCentre\Helper\Create\Project;

class OnlineAPI_control
{
	static function create($pBasketWorkFlowType = TPX_BASKETWORKFLOWTYPE_NORMAL)
	{
        global $gSession;
		global $gConstants;
		global $ac_config;

		$createMethodExists = false;
        $systemConfigArray = DatabaseObj::getSystemConfig();

		$isSSOEnabled = false;
        $productIdent = UtilsObj::getGETParam('id', '');
        $productIdent = '0' != $productIdent ? $productIdent : '';
        $tenantID = $systemConfigArray['tenantid'];
		$groupData = '';
		$groupDataStatus = 0;
		$companyCode = '';
		$brandCode = '';
		$groupCode = '';
		$productCollectionCode = '';
		$productLayoutCode = '';
		$basketRef = '';
		$userID = $_COOKIE['TPX-USER-ID'] ?? 0; // TODO: Remove this when create is updated.
        $userName = '';
        $needsErrorRedirect = false;
        $sessionNeedsDeleting = false;
		$customParamArray = array();
		$customParamStatus = 0;
		$licenseKeyDataArray = array();
		$brandingArray = array();
		$smallScreenWizardMode = 0;
		$largeScreenWizardMode = 0;
		$enableSwitchingEditor = 0;
		$onlineEditorMode = 0;
		$aiModeOverride = -1;
		$newGuestWorkFlowMode = -1;

        if ($pBasketWorkFlowType == TPX_BASKETWORKFLOWTYPE_HIGHLEVELAPI)
        {
	        $isSSOEnabled = UtilsObj::getGETParam('ssoenabled', TPX_SSO_HIGHLEVEL_ENABLED_OFF) != TPX_SSO_HIGHLEVEL_ENABLED_OFF;
		}

		$resultArray = array(
			'result' => TPX_ONLINE_ERROR_NONE,
			'resultmessage' => '',
			'brandcode' => '',
			'groupcode' => '',
			'groupdata' => '',
			'collectioncode' => '',
			'collectionname' => '',
			'productcode' => '',
			'layoutname' => '',
			'projectref' => '',
			'projectname' => '',
			'userdata' => '',
			'designurl' => '');

		// check to see if a project is being created using the standard TAOPIX product URL's
		if ($productIdent != '')
		{
			$productIdentData = explode(chr(10), UtilsObj::decryptData($productIdent, $systemConfigArray['systemkey'], true), 2);

			// If this request is a post get custom params from POST otherwise get them from GET
			$parsedParamArray = UtilsObj::parseProductURLIdentData($productIdentData, ('GET' === $_SERVER['REQUEST_METHOD'] ? $_GET : $_POST));

			$productCollectionCode = $parsedParamArray['collectioncode'];
			$productLayoutCode = $parsedParamArray['layoutcode'];
			$groupCode =  $parsedParamArray['groupcode'];
			$groupDataStatus = $parsedParamArray['groupdata']['status'];
			$groupData = $parsedParamArray['groupdata']['code'];
			$customParamStatus = $parsedParamArray['customparams']['status'];
			$customParamArray = $parsedParamArray['customparams']['params'];
			$wizardModeOverrideStatus = $parsedParamArray['wizardmodeoverride']['status'];

			$smallScreenWizardMode = $parsedParamArray['wizardmodeoverride']['params']['wmoss'];
			$largeScreenWizardMode = (int) $parsedParamArray['wizardmodeoverride']['params']['wmols'];
			$enableSwitchingEditor = (int) $parsedParamArray['wizardmodeoverride']['params']['wmosd'];
			$onlineEditorMode = (int) $parsedParamArray['uioverridemode'];
			$aiModeOverride = (int) $parsedParamArray['aimodeoverride'];

			$licenseKeyDataArray = DatabaseObj::getLicenseKeyFromCode($groupCode);
			$companyCode = $licenseKeyDataArray['companyCode'];
			$brandCode = $licenseKeyDataArray['webbrandcode'];

			$guestWorkFlowMode = $licenseKeyDataArray['onlinedesignerguestworkflowmode'];

			if ($guestWorkFlowMode != TPX_GUESTWORKFLOWMODE_DISABLED)
			{
				if (array_key_exists('gwm', $_GET))
				{
					$newGuestWorkFlowMode = $_GET['gwm'];

					if ($newGuestWorkFlowMode <= TPX_GUESTWORKFLOWMODE_AUTOMATIC)
					{
						$guestWorkFlowMode = $newGuestWorkFlowMode;
					}
				}
			}
		}
		else
		{
			$guestWorkFlowMode = UtilsObj::getGETParam('gwm', TPX_GUESTWORKFLOWMODE_DISABLED);
			$groupData = UtilsObj::getGETParam('gd', '');
            $productCollectionCode = UtilsObj::getGETParam('collectioncode', '');
            $productLayoutCode = UtilsObj::getGETParam('productcode', '');
            $groupCode = UtilsObj::getGETParam('groupcode', '');
		}


		if ($pBasketWorkFlowType == TPX_BASKETWORKFLOWTYPE_HIGHLEVELAPI)
		{
			//we want to use the browserlocale only if the language parameter has nt been passed
			$browserLanguageCode = UtilsObj::cleanseLanguageCode(UtilsObj::getGETParam('browserlocale', ''), $gConstants['defaultlanguagecode']);
			$browserLanguageCode = UtilsObj::cleanseLanguageCode(UtilsObj::getGETParam('l', ''), $browserLanguageCode);
		}
		else
		{
			$browserLanguageCode = UtilsObj::cleanseLanguageCode(UtilsObj::getGETParam('l', ''), $gConstants['defaultlanguagecode']);
		}

		$createProjectConfig = array();
		$createProjectConfig['languagecode'] = $browserLanguageCode;
        $createProjectConfig['defaultlanguagecode'] = $gConstants['defaultlanguagecode'];
		$createProjectConfig['companycode'] = $companyCode;
		$createProjectConfig['brandcode'] = $brandCode;
		$createProjectConfig['groupcode'] = $groupCode;
		$createProjectConfig['groupdata'] = $groupData;
		$createProjectConfig['collectioncode'] = $productCollectionCode;
		$createProjectConfig['productcode'] = $productLayoutCode;
		$createProjectConfig['ccnotificationsenabled'] = false;
		$createProjectConfig['guestworkflowmode'] = $guestWorkFlowMode;
		$createProjectConfig['projectname'] = '';
		$createProjectConfig['devicedetection'] = UtilsObj::getGETParam('dd', '');
		$createProjectConfig['minlife'] = 0;
		$createProjectConfig['checkoutname'] = '';
		$createProjectConfig['abandonurl'] = '';
		$createProjectConfig['abandonname'] = '';
		$createProjectConfig['cansignin'] = 1;
		$createProjectConfig['cansignout'] = 1;
		$createProjectConfig['disablebackbutton'] = 0;
		$createProjectConfig['userdata'] = '';
		$createProjectConfig['editprojectnameonfirstsave'] = 1;
		$createProjectConfig['basketapiworkflowtype'] = $pBasketWorkFlowType;
		$createProjectConfig['ssotoken'] = '';
		$createProjectConfig['ssoprivatedata'] = array();
		$createProjectConfig['ssoexpiredate'] = '';
		$createProjectConfig['assetservicedata'] = array();
		$createProjectConfig['useraccount'] = array();
		$createProjectConfig['3dmodelfileurl'] = '';
		$createProjectConfig['3dmodelsystemresourcefileid'] = 0;
		$createProjectConfig['customparameters'] = $customParamArray;
		$createProjectConfig['onlinedesignerlogolinkurl'] = '';
		$createProjectConfig['onlinedesignerlogolinktooltip'] = '';
		$createProjectConfig['smallscreenwizardmode'] =	$smallScreenWizardMode;
		$createProjectConfig['largescreenwizardmode'] = $largeScreenWizardMode;
		$createProjectConfig['devicesettings'] = 'unknown';
		$createProjectConfig['enableswitchingeditor'] = $enableSwitchingEditor;
		$createProjectConfig['onlineeditormode'] = $onlineEditorMode;
		$createProjectConfig['aimodeoverride'] = $aiModeOverride;
		$createProjectConfig['requirepasswordforsessioninactivity'] = true;
        $createProjectConfig['canshareproject'] = true;
        $createProjectConfig['averagepicturesperpage'] = 0;
		$createProjectConfig['openmode'] = TPX_OPEN_MODE_NEW_PROJECT;
        $createProjectConfig['newwizardmode'] = -1;
		$edlScriptDifferences = [];


		// Detect if some feature are force to be turn on.
		$createProjectConfig['featuretoggle'] = UtilsObj::getGETParam('tpxft', '');

		if (($pBasketWorkFlowType == TPX_BASKETWORKFLOWTYPE_LOWLEVELAPI) || ($pBasketWorkFlowType == TPX_BASKETWORKFLOWTYPE_HIGHLEVELAPI))
		{
			$userID = 0;
			$login = '';
			$userName = '';
			$ssoResult = '';
			$ssoResultParam = '';
			$ssoToken = '';
			$ssoPrivateDataArray = Array();
			$ssoExpireDate = '';
			$startSession = true;
			$basketRecordID = 0;
			$basketSessionID = 0;

			if ($pBasketWorkFlowType == TPX_BASKETWORKFLOWTYPE_HIGHLEVELAPI)
			{
				$basketRef = UtilsObj::getHighLevelBasketAPIGETParams('mawebhlbr', TPX_ONLINE_BASKETAPI_DEFAULTBASKETREF);

				if ($basketRef != TPX_ONLINE_BASKETAPI_DEFAULTBASKETREF)
				{
					// we must check to see if there is a valid user session for the current basketref
					$highLevelBasketUserSesionResultArray = AuthenticateObj::checkHighLevelBasketSessionActive($basketRef);

					if (($highLevelBasketUserSesionResultArray['result'] == '') && ($highLevelBasketUserSesionResultArray['sessionactive'] == 1))
					{
						$gSession['ref'] = $highLevelBasketUserSesionResultArray['sessionid'];
						$basketSessionID = $highLevelBasketUserSesionResultArray['sessionid'];
						$startSession = false;

						$gSession = DatabaseObj::getSessionData($highLevelBasketUserSesionResultArray['sessionid']);

						$ssoToken = $gSession['userdata']['ssotoken'];
						$ssoPrivateDataArray = $gSession['userdata']['ssoprivatedata'];

					}
				}
			}

			// attempt to perform a single sign-on to the system
			$ssoResultArray = AuthenticateObj::authenticateLogin(TPX_USER_AUTH_REASON_HIGHLEVEL_ONLINE_PROJECT_CREATE, -1, false, $browserLanguageCode,
																$brandCode, $groupCode, '', '',
																TPX_PASSWORDFORMAT_CLEARTEXT,  '', true, $startSession, true, $ssoToken, $ssoPrivateDataArray, array());

			if ($ssoResultArray['result'] == '')
			{
				$userID = $ssoResultArray['useraccountid'];
				$userName = $ssoResultArray['username'];
				$ssoToken = $ssoResultArray['ssotoken'];
				$ssoPrivateDataArray = $ssoResultArray['ssoprivatedata'];
				$assetServiceDataArray = $ssoResultArray['assetservicedata'];
				$ssoExpireDate = $ssoResultArray['ssoexpiredate'];
			}
			else
			{
				$ssoResult = $ssoResultArray['result'];
				$ssoResultParam = $ssoResultArray['resultparam'];
			}

			// process the result of the single sign-on request
			switch ($ssoResult)
			{
				case 'SSOREDIRECT':
				{
					$resultArray['result'] = -2;

					// redirect to grab the single sign-on token
					$resultArray['designurl'] = $ssoResultArray['resultparam'];
					break;
				}
				case '':
				{
					if ((($userID > 0) && ($isSSOEnabled)) || (! $isSSOEnabled))
					{
						self::includeOnlineBasketAPI($pBasketWorkFlowType);

						if ($pBasketWorkFlowType == TPX_BASKETWORKFLOWTYPE_LOWLEVELAPI)
						{
							$EDLBasketClass = 'OnlineBasketAPI';
							$createMethodExists = method_exists($EDLBasketClass, 'createProject');
						}
						else
						{
							$EDLBasketClass = 'OnlineBasketHighLevelAPI';
							$createMethodExists = true;
						}

						if ($createMethodExists)
						{
							// we need to perform device detection settings earlier so that we can pass the devicesettings to the createProject functions within the external scripts.
							// we can only do this on e-commerce workflow if they have passed us the device detection parmater on the URL.
							// for multiline API we should always perform the check.
							if (($pBasketWorkFlowType == TPX_BASKETWORKFLOWTYPE_LOWLEVELAPI) && ($createProjectConfig['devicedetection'] != ''))
							{
								$_GET['dd'] = $createProjectConfig['devicedetection'];
								UtilsObj::setSessionDeviceData(true);
								$createProjectConfig['devicesettings'] = $gSession['islargescreen'] ? 'largescreen' : 'smallscreen';

							}
							elseif ($pBasketWorkFlowType == TPX_BASKETWORKFLOWTYPE_HIGHLEVELAPI)
							{
								UtilsObj::setSessionDeviceData();
								DatabaseObj::updateSession();
								$deviceActive = 1;
								$createProjectConfig['devicesettings'] = $gSession['islargescreen'] ? 'largescreen' : 'smallscreen';
							}

							if ($pBasketWorkFlowType == TPX_BASKETWORKFLOWTYPE_LOWLEVELAPI)
							{
								$companyCode = '';
								$brandCode = '';

								if ($createProjectConfig['groupcode'] == '')
								{
									if (method_exists($EDLBasketClass, 'getDefaultGroupCode'))
									{
										$createProjectConfig['groupcode'] = OnlineBasketAPI::getDefaultGroupCode();
									}
								}

								// only allow single sign on if the group code is provided
								if ($createProjectConfig['groupcode'] != '')
								{
									$licenseKeyDataArray = DatabaseObj::getLicenseKeyFromCode($createProjectConfig['groupcode']);

			    					$companyCode = $licenseKeyDataArray['companyCode'];
			    					$brandCode = $licenseKeyDataArray['webbrandcode'];

			    					$brandingArray = DatabaseObj::getBrandingFromCode($brandCode);
									$createProjectConfig['brandcode'] = $brandCode;

									$createProjectConfig['useraccount'] = AuthenticateObj::createEmptyUserAccount($licenseKeyDataArray, $createProjectConfig['groupcode'], $brandCode, $brandingArray);
								}

								$createOrigArray = $createProjectConfig;
								$createProjectConfig = OnlineBasketAPI::createProject($createProjectConfig);

								$edlScriptDifferences = self::recursiveDiff($createOrigArray, $createProjectConfig);

								// only proceed with single sign on if the useraccount is returned
								if (!empty($createProjectConfig['useraccount']) || isset($createProjectConfig['authkey']))
								{
									$userAccountID = 0;

                                    if (!isset($createProjectConfig['authkey'])){

                                        $userAccountArray = AuthenticateObj::updateOrInsertExternalAccount($userAccountID, $createProjectConfig['useraccount'], true, -1,
                                            '', '', $brandCode, $createProjectConfig['groupcode'], $companyCode,
                                            $createProjectConfig['updategroupcode'], $createProjectConfig['updateaccountdetails'],
                                            $createProjectConfig['updateaccountbalance'], $createProjectConfig['updategiftcardbalance']);
                                    }
                                    else {
                                        // clean up any authentication data records
                                        AuthenticateObj::deleteAuthenticationDataRecords();

                                        $authenticationRecord = AuthenticateObj::getAuthenticationDataRecord(TPX_AUTHENTICATIONTYPE_LOWLEVEL, $createProjectConfig['authkey'], true);

                                        if ($authenticationRecord['found'])
                                        {
                                            $userAccountID = $authenticationRecord['ref'];
                                            $userAccountArray = DatabaseObj::getUserAccountFromID($userAccountID);
                                        }
                                        else{
                                            $resultArray['result'] = TPX_ONLINE_ERROR_LOWLEVELINTERNALERROR;
                                        }
                                    }

			                    	if ($userAccountArray['result'] == '')
			                    	{
										$userID = $userAccountID;
										$userName = $userAccountArray['contactfirstname'] . ' ' . $userAccountArray['contactlastname'];
										$createProjectConfig['requirepasswordforsessioninactivity'] = $createProjectConfig['useraccount']['requirepasswordforsessioninactivity'];
									}
									else
									{
										if ($userAccountArray['result'] == 'str_DatabaseError')
										{
											$resultArray['result'] = TPX_ONLINE_ERROR_DATABASE;
										}
										elseif ($userAccountArray['result'] == 'str_ErrorEmptyGroupCode')
										{
											$resultArray['result'] = TPX_ONLINE_ERROR_EMPTYGROUPCODE;
										}
										elseif ($userAccountArray['result'] == 'str_ErrorAccountMisMatch')
										{
											$resultArray['result'] = TPX_ONLINE_ERROR_ACCOUNT_MISTMATCH;
										}
										elseif ($userAccountArray['result'] == 'str_ErrorAccountTaskNotAllowed')
										{
											$resultArray['result'] = TPX_ONLINE_ERROR_ACCOUNTTASKNOTALLOWED;
										}
									}

									// check if the groupcode has has changed (e.g. via the script) and reload the licensekeydatarray if needed
									if ((! array_key_exists('groupcode', $licenseKeyDataArray)) || ($licenseKeyDataArray['groupcode'] != $createProjectConfig['groupcode']))
									{
										$licenseKeyDataArray = DatabaseObj::getLicenseKeyFromCode($createProjectConfig['groupcode']);
									}
			                    }
			                    else
			                    {
			                    	if ($createProjectConfig['groupcode'] == '')
			                    	{
			                    		$resultArray['result'] = TPX_ONLINE_ERROR_EMPTYGROUPCODE;
			                    	}
			                    	else
			                    	{
			                    		if (! array_key_exists('groupcode', $licenseKeyDataArray))
			                    		{
			                    			$licenseKeyDataArray = DatabaseObj::getLicenseKeyFromCode($createProjectConfig['groupcode']);

											// Update the $createProjectConfig['brandcode'] with the value from the license key.
											$createProjectConfig['brandcode'] = $licenseKeyDataArray['webbrandcode'];
			                    		}
			                    	}
			                    }

								if ($resultArray['result'] == TPX_ONLINE_ERROR_NONE)
								{
									// check to make sure the EDL_OnlineBasketAPI script has not already set a logo link
									if ($createProjectConfig['onlinedesignerlogolinkurl'] == '')
									{
										// pass the logo link url set in control centre into the low level API
										if ($licenseKeyDataArray['usedefaultonlinedesignerlogolinkurl'] == 1)
										{
											if (empty($brandingArray))
											{
												$brandingArray = DatabaseObj::getBrandingFromCode($createProjectConfig['brandcode']);
											}

											// use url set by the brand
											$createProjectConfig['onlinedesignerlogolinkurl'] = $brandingArray['onlinedesignerlogolinkurl'];
											$createProjectConfig['onlinedesignerlogolinktooltip'] = $brandingArray['onlinedesignerlogolinktooltip'];
										}
										else
										{
											// use url set by the licensekey
											$createProjectConfig['onlinedesignerlogolinkurl'] = $licenseKeyDataArray['onlinedesignerlogolinkurl'];
											$createProjectConfig['onlinedesignerlogolinktooltip'] = $licenseKeyDataArray['onlinedesignerlogolinktooltip'];
										}
									}

									// if the generateSharePreviewLink method does not exist then we can't share a project.
									if (! method_exists($EDLBasketClass, 'generateSharePreviewLink'))
									{
										$createProjectConfig['canshareproject'] = false;
										$edlScriptDifferences['canshareproject'] = false;
									}
								}
							}
							else
							{
								// check if the groupcode has has changed (e.g. via the script) and reload the licensekeydatarray if needed
								if ((! array_key_exists('groupcode', $licenseKeyDataArray)) || ($licenseKeyDataArray['groupcode'] != $createProjectConfig['groupcode']))
								{
									$licenseKeyDataArray = DatabaseObj::getLicenseKeyFromCode($createProjectConfig['groupcode']);
								}

								// pass the logo link url set in control centre into the low level API
								if ($licenseKeyDataArray['usedefaultonlinedesignerlogolinkurl'] == 1)
								{
									$brandingArray = DatabaseObj::getBrandingFromCode($createProjectConfig['brandcode']);

									// use url set by the brand
									$createProjectConfig['onlinedesignerlogolinkurl'] = $brandingArray['onlinedesignerlogolinkurl'];
									$createProjectConfig['onlinedesignerlogolinktooltip'] = $brandingArray['onlinedesignerlogolinktooltip'];
								}
								else
								{
									// use url set by the licensekey
									$createProjectConfig['onlinedesignerlogolinkurl'] = $licenseKeyDataArray['onlinedesignerlogolinkurl'];
									$createProjectConfig['onlinedesignerlogolinktooltip'] = $licenseKeyDataArray['onlinedesignerlogolinktooltip'];
								}

                                $createOrigArray = $createProjectConfig;
                                $createProjectConfig = OnlineAPI_model::buildHighLevelProjectParams($createProjectConfig, 'createProject', $brandCode);
                                $createProjectConfig['ccnotificationsenabled'] = true;
                                $edlScriptDifferences = self::recursiveDiff($createOrigArray, $createProjectConfig);
							}

							if ($resultArray['result'] == TPX_ONLINE_ERROR_NONE)
							{
								$browserLanguageCode = UtilsObj::cleanseLanguageCode($createProjectConfig['languagecode'], $browserLanguageCode);

								$gSession['browserlanguagecode'] = $browserLanguageCode;

								$createProjectConfig['basketapiworkflowtype'] = $pBasketWorkFlowType;

								if (($createProjectConfig['devicedetection'] != '') || ($pBasketWorkFlowType == TPX_BASKETWORKFLOWTYPE_HIGHLEVELAPI))
								{
									// we must revalidate the device detection as the e-commerce external script could have overwritten the value that was orignally passed.
									if ($pBasketWorkFlowType == TPX_BASKETWORKFLOWTYPE_LOWLEVELAPI)
									{
										$_GET['dd'] = $createProjectConfig['devicedetection'];
										$deviceDetectionArray = UtilsObj::setSessionDeviceData(true);
										$deviceActive = $deviceDetectionArray['isactive'];
									}

									if ($deviceActive)
									{
										DatabaseObj::updateSession();


										if ($createProjectConfig['cansignin'] == 0)
										{
											$createProjectConfig['guestworkflowmode'] = TPX_GUESTWORKFLOWMODE_AUTOMATIC;
										}

										if (($createProjectConfig['guestworkflowmode'] >= 0) && ($createProjectConfig['guestworkflowmode'] <= 3))
										{
											if ($createProjectConfig['projectname'] != '')
											{
												$createProjectConfig['projectname'] = UtilsObj::cleanseInput($createProjectConfig['projectname']);
											}

											$licenseKeyDataArray = DatabaseObj::getLicenseKeyFromCode($createProjectConfig['groupcode']);
											$createProjectConfig['brandcode'] = $licenseKeyDataArray['webbrandcode'];
											$createProjectConfig['companycode'] = $licenseKeyDataArray['companyCode'];
											$resultArray['brandcode'] = $licenseKeyDataArray['webbrandcode'];
											$resultArray['groupcode'] = $createProjectConfig['groupcode'];
											$resultArray['groupdata'] = $createProjectConfig['groupdata'];
											$resultArray['collectioncode'] = $createProjectConfig['collectioncode'];
											$resultArray['productcode'] = $createProjectConfig['productcode'];
											$resultArray['userdata'] = $createProjectConfig['userdata'];

											 if ($pBasketWorkFlowType == TPX_BASKETWORKFLOWTYPE_HIGHLEVELAPI)
											 {
											 	$brandingArray = DatabaseObj::getBrandingFromCode($createProjectConfig['brandcode']);

											 	if ($brandingArray['usemultilinebasketworkflow'] == 0)
											 	{
											 		$resultArray['result'] = TPX_ONLINE_ERROR_HIGHLEVELNOTENABLED;
											 	}
											 }

											if ($licenseKeyDataArray['isactive'] == 1 && $licenseKeyDataArray['availableonline'] == 1)
											{
												if ($resultArray['result'] == TPX_ONLINE_ERROR_NONE)
												{
													$productArray = DatabaseObj::getProductFromCollectionCodeAndLayoutCode($createProjectConfig['collectioncode'], $createProjectConfig['productcode']);

													$createProjectConfig['producttreesdata'] = [];
													$createProjectConfig['minimumprintsperproject'] = $productArray['minimumprintsperproject'];
													$createProjectConfig['usedefaultimagescalingbefore'] = $productArray['usedefaultimagescalingbefore'];
													$createProjectConfig['imagescalingbeforeenabled'] = $productArray['imagescalingbeforeenabled'];
													$createProjectConfig['imagescalingbefore'] = $productArray['imagescalingbefore'];
													$createProjectConfig['usedefaultaveragepicturesperpage'] = $productArray['usedefaultaveragepicturesperpage'];
													$createProjectConfig['averagepicturesperpage'] = $productArray['averagepicturesperpage'];
													$createProjectConfig['retroprints'] = $productArray['retroprints'];

													$browserArray = OnlineAPI_model::checkBrowsers();

													if ($browserArray['browsersupported'])
													{
														// determine if perfectly clear is applied automatically.
														$createProjectConfig['automaticallyapplyperfectlyclearmode'] = self::determinePerfectlyClearAutomationMode($licenseKeyDataArray, $brandingArray);

														if ($pBasketWorkFlowType == TPX_BASKETWORKFLOWTYPE_HIGHLEVELAPI)
														{
															$basketRef = UtilsObj::getHighLevelBasketAPIGETParams('mawebhlbr', TPX_ONLINE_BASKETAPI_DEFAULTBASKETREF);

															$createBasketRecordResult = OnlineAPI_model::createBasketRecord($brandCode, $groupCode, '');
															$basketRecordID = $createBasketRecordResult['basketrecordid'];
															$basketExpireDate = $createBasketRecordResult['basketexpiredate'];
															$newBasketRef = false;

															if ($basketRef == TPX_ONLINE_BASKETAPI_DEFAULTBASKETREF)
															{
																$newBasketRef = true;
															}
															else
															{
																// we have a basketref check if it has expired
																$isBasketActiveArray = AuthenticateObj::checkHighLevelBasketExpired($basketRef);

																if ($isBasketActiveArray['basketactive'] != 1)
																{
																	$newBasketRef = true;
																}
															}

															if (! $newBasketRef)
															{
																// if we are not generating a new basket ref then we must check to see if this basket ref was ever assigned
																// to a user who had previously signed in before.
																$onlineBasketAuthenticated = AuthenticateObj::checkIfOnlineBasketRequiresAuthenticatedUser($basketRef);

																if ($onlineBasketAuthenticated['result'] != '')
																{
																	$resultArray['result'] = TPX_ONLINE_ERROR_HIGHLEVELINTERNALERROR;
																}
																else if ($onlineBasketAuthenticated['authenticatedstatus'] == TPX_ONLINE_BASKETAPI_AUTHENTICATEDUSERSTATUS_SIGNEDINUSER)
																{
																	// the user still has a valid sesison for the basket ref. Let them continue creating a project
																	// and take them into the online designer as signed in.

																	$userID = $onlineBasketAuthenticated['userid'];
																	$userName = $onlineBasketAuthenticated['username'];
																	$login = $onlineBasketAuthenticated['login'];
																	$basketSessionID = $onlineBasketAuthenticated['sessionid'];
																}
																else if (($onlineBasketAuthenticated['result'] == '') && ($onlineBasketAuthenticated['authenticatedstatus'] == TPX_ONLINE_BASKETAPI_AUTHENTICATEDUSERSTATUS_SIGNEDINUSEREXPIRED))
																{
																	// the session for the users basket ref has expired therefore we cannot let them continue with creating a project

																	$resultArray['result'] = TPX_ONLINE_ERROR_HIGHLEVELBASKETEXPIRED;
																}
															}

															if ($newBasketRef)
															{
																$basketRef = OnlineAPI_model::generateBasketRef($basketRecordID);
															}

															// we knew the user had signed in via sso therfore if a new basketref had been generated we need to consolidate
															// all of the users projects with the new basketref.
															if ($ssoResultArray['loginviasso'])
															{
																if ($newBasketRef)
																{
																	// Update userid the projects in the cart are assigned to.
																	OnlineAPI_model::updateUserIDBasketRefForProjectsInBasket($userID, $basketRef);
																}

																$updateSessionResult = DatabaseObj::linkOnlineBasketToSession($gSession['ref'], $basketRef, $userID);
															}
														}

														if ($resultArray['result'] == TPX_ONLINE_ERROR_NONE)
														{
															// we need to pass the basketref as part of the create project params
															$createProjectConfig['basketref'] = $basketRef;

															if ($pBasketWorkFlowType == TPX_BASKETWORKFLOWTYPE_HIGHLEVELAPI)
															{
																// pass the sso details over to online these are already set for lowlevel
																$createProjectConfig['ssotoken'] = $ssoToken;
																$createProjectConfig['ssoprivatedata'] = $ssoPrivateDataArray;
																$createProjectConfig['ssoexpiredate'] = $ssoExpireDate;

																// only get the asset service details for image led work flow if sso is enabled
																if ($isSSOEnabled)
																{
																	$createProjectConfig['assetservicedata'] = $assetServiceDataArray;
																}
															}

															$previewExisitingProjectDataArray = array();
															$previewExisitingProjectDataArray['userid'] = $userID;
															$previewExisitingProjectDataArray['username'] = $userName;
															$previewExisitingProjectDataArray['login'] = $login;

															if (TPX_PRODUCT_TYPE_SINGLE_PRINTS !== $productArray['collectiontype'])
															{
																$error = '';
																OnlineAPI_model::getOnlineEditorSettings($createProjectConfig['onlineeditormode'], $createProjectConfig['groupcode'], $licenseKeyDataArray, $brandingArray, $createProjectConfig['enableswitchingeditor']);

																// we have to re add the switching editor and editormode as potentially the setting s have come from the brand/lkey
																$edlScriptDifferences['enableswitchingeditor'] = ($createProjectConfig['enableswitchingeditor'] === -1) ? null : $createProjectConfig['enableswitchingeditor'];
																$edlScriptDifferences['onlineeditormode'] = ($createProjectConfig['onlineeditormode'] === -1) ? null : $createProjectConfig['onlineeditormode'];
																$edlScriptDifferences['largescreenwizardmode'] = ($createProjectConfig['largescreenwizardmode'] === -1) ? null : $createProjectConfig['largescreenwizardmode'];
																$edlScriptDifferences['enableswitchingeditor'] = ($createProjectConfig['enableswitchingeditor'] === -1) ? null : $createProjectConfig['enableswitchingeditor'];
																$edlScriptDifferences['aimodeoverride'] = ($createProjectConfig['aimodeoverride'] === -1) ? null : $createProjectConfig['aimodeoverride'];
																$createProjectConfig['experienceoverrides'] = $edlScriptDifferences;
																$createProjectConfig['theme'] = $brandingArray['theme'];
                                                                $createProjectConfig['ccnotificationsenabled'] = (bool) $createProjectConfig['ccnotificationsenabled'];
																$createProjectConfig['username'] = $userName;

																//multi item workflow
																try {
																	$client = new Client();
																	$urlCreator = new Project($client, UtilsObj::correctPath($brandingArray['onlinedesignerurl'], '/', false), $userID, $createProjectConfig);
																	$projectRef = $urlCreator->getProjectRef();
																	$projectName = $urlCreator->getProjectName();
																	$designURL = $urlCreator->getDesignerURL();
                                                                    $userID = $urlCreator->getUserID();
																}
																catch (GuzzleHttp\Exception\ClientException|GuzzleHttp\Exception\ServerException $e) {
																	$errorData = json_decode($e->getResponse()->getBody()->getContents(), true);
																	$error = $errorData['code'];
																}
															}
															else
															{
                                                                if ($createProjectConfig['projectname'] == ''){
                                                                    $smarty = SmartyObj::newSmarty('', '', '', $browserLanguageCode);
                                                                    $createProjectConfig['projectname'] = $smarty->get_config_vars('str_TitleUntitled') . ' ' . date(LocalizationObj::getLocaleFormatValue('str_DateTimeFormat'), time());
                                                                }

																$dataCacheKey = $createProjectConfig['groupcode'] . '.' . $createProjectConfig['companycode'] . '.' . $createProjectConfig['collectioncode'];
																$productTreesData = OnlineAPI_model::getProductTreesAPIData($createProjectConfig['productcode'], $createProjectConfig['collectioncode'], $tenantID, $dataCacheKey);
																$createProjectConfig['producttreesdata'] = $productTreesData;

																$onlineParamData = OnlineAPI_model::prepareParamDataToCreateOnlineSession(TPX_OPEN_MODE_NEW_PROJECT, '', $previewExisitingProjectDataArray, $createProjectConfig, $productArray['collectiontype']);
																$error = $onlineParamData['result'];
															}

															if ($error == '')
															{
																$onlineParamData['collectionname'] = $productArray['collectionname'];
																$onlineParamData['layoutname'] = $productArray['name'];
																$onlineParamData['tenantid'] = $tenantID;

																if (TPX_PRODUCT_TYPE_SINGLE_PRINTS === $productArray['collectiontype'])
																{
																	$onlineBrandURL = AuthenticateObj::createOnlineSession($onlineParamData);
																	$projectRef = $onlineBrandURL['projectref'];
																	$projectName = $onlineBrandURL['projectname'];
																	$designURL = $onlineBrandURL['brandurl'] . '&lsp=1';
                                                                    $userID = $onlineBrandURL['userid'];
																	$error = $onlineBrandURL['error'];
																}

																if ($error == '')
																{
																	if ($designURL != '')
																	{
																		if ($pBasketWorkFlowType == TPX_BASKETWORKFLOWTYPE_HIGHLEVELAPI)
																		{

																			$projectDataArray = array();
																			$projectDataArray['webbrandcode'] = $brandCode;
																			$projectDataArray['groupcode'] = $groupCode;
																			$projectDataArray['basketrecordid'] = $basketRecordID;
																			$projectDataArray['basketref'] = $basketRef;
																			$projectDataArray['basketexpiredate'] = date('Y-m-d H:i:s', $basketExpireDate);
																			$projectDataArray['projectref'] = $projectRef;
																			$projectDataArray['userid'] = $userID;
																			$projectDataArray['projectname'] = $projectName;
																			$projectDataArray['collectioncode'] = $productCollectionCode;
																			$projectDataArray['collectionname'] = $productArray['collectionname'];
																			$projectDataArray['layoutcode'] = $productLayoutCode;
																			$projectDataArray['layoutname'] = $productArray['name'];
																			$projectDataArray['projectdata'] = '';

																			$addProjcetToOnlineBasket = OnlineAPI_model::addProjectToOnlineBasket($projectDataArray);

																			$updateSessionResult = DatabaseObj::linkOnlineBasketToSession($basketSessionID, $basketRef, $userID);

																			if ($addProjcetToOnlineBasket['result'] == '')
																			{
																				$resultArray['basketref'] = $basketRef;
																				$resultArray['cookieexpirytime'] = $basketExpireDate;
																			}
																			else
																			{
																				$resultArray['result'] = $addProjcetToOnlineBasket['resultparam'];
																			}
																		}

																		$resultArray['onlineapiurl'] = $brandingArray['onlineapiurl'];
																		$resultArray['projectref'] = $projectRef;
																		$resultArray['designurl'] = $designURL;
																		$resultArray['projectname'] = $projectName;
                                                                        $resultArray['collectiontype'] = $productArray['collectiontype'];
                                                                        $resultArray['collectionname'] = $productArray['collectionname'];
                                                                        $resultArray['layoutname'] = $productArray['name'];
																	}
																	else
																	{
																		$resultArray['result'] = TPX_ONLINE_ERROR_COMMUNICATION_FAILED;
																	}
																}
																else
																{
																	$resultArray['result'] = TPX_ONLINE_ERROR_COMMUNICATION_FAILED;
																}
															}
															else
															{
																$resultArray['result'] = $error;
															}
														}
														else
														{
															// as we have encountered an error we need to delete the temporary basketrecord that has been created when attempting to create a project.
															if ($basketRecordID > 0)
															{
																DatabaseObj::deleteOnlineBasketRecordByRecordID($basketRecordID);
															}
														}
													}
													else
													{
														$resultArray['result'] = TPX_ONLINE_ERROR_BROWSERNOTSUPPORTED;
													}
												}
											}
											else
											{
												$resultArray['result'] = TPX_ONLINE_ERROR_INACTIVELICENSEKEY;
											}

										}
										else
										{
											$resultArray['result'] = TPX_ONLINE_ERROR_INVALIDPARAMETER;
										}
									}
									else
									{
										$resultArray['result'] = TPX_ONLINE_ERROR_INVALIDDEVICEDETECTIONDATA;
									}
								}
								else
								{
									$resultArray['result'] = TPX_ONLINE_ERROR_INVALIDDEVICEDETECTIONDATA;
								}
							}
						}
					}
					else
					{
						$resultArray['result'] = TPX_ONLINE_ERROR_ACCOUNT_MISTMATCH;

						$needsErrorRedirect = true;
						$sessionNeedsDeleting = true;
					}

					break;
				}
				default:
				{
					$needsErrorRedirect = true;
					$sessionNeedsDeleting = true;

					if ($ssoResult == 'str_DatabaseError')
					{
						$resultArray['result'] = TPX_ONLINE_ERROR_DATABASE;
					}
					elseif ($ssoResult == 'str_ErrorEmptyGroupCode')
					{
						$resultArray['result'] = TPX_ONLINE_ERROR_EMPTYGROUPCODE;
					}
					elseif (($ssoResult == 'str_ErrorAccountMisMatch') || ($ssoResult == 'str_ErrorDuplicateUserName'))
					{
						$resultArray['result'] = TPX_ONLINE_ERROR_ACCOUNT_MISTMATCH;
					}
					elseif ($ssoResult == 'str_ErrorAccountTaskNotAllowed')
					{
						$resultArray['result'] = TPX_ONLINE_ERROR_ACCOUNTTASKNOTALLOWED;
					}
					else
					{
						$returnArray['result'] = 99;
						$returnArray['resultmessage'] = $ssoResultParam;
					}

					break;
				}
			}

			if ($sessionNeedsDeleting)
			{
				OnlineAPI_model::deleteHighLevelUserSession($basketRef);
			}

			if ($resultArray['result'] != TPX_ONLINE_ERROR_NONE)
			{
				$hl_config = UtilsObj::readWebBrandConfigFile('../config/onlinebaskethighlevelapi.conf', $brandCode);

				$brandingArray = DatabaseObj::getBrandingFromCode($brandCode);

				if ($brandingArray['onlinedesignerlogouturl'] != '')
				{
					$homeURL = $brandingArray['onlinedesignerlogouturl'];
				}
				else
				{
					$homeURL = UtilsObj::getArrayParam($hl_config, 'REDIRECTIONURL');
				}

				$resultArray['redirecturl'] = $homeURL;
			}

			OnlineAPI_view::returnResultAPI($resultArray, $browserLanguageCode, $pBasketWorkFlowType, $licenseKeyDataArray['webbrandcode']);

		}
		else
		{
			$error = '';
			$isSingleItemWorkFlow = true;

			$brandingArray = DatabaseObj::getBrandingFromCode($brandCode);
			// first check to see if the system has brands set to use Multiline Basket API
			// this is to capture old product URL's being invoked when Multiline Basket API is set for the brand
			// now check to see if the brand used to create the project is using Multiline Basket API
			if ($brandingArray['usemultilinebasketworkflow'] == 1)
			{
				$isSingleItemWorkFlow = false;

				$hl_config = UtilsObj::readWebBrandConfigFile('../config/onlinebaskethighlevelapi.conf', $brandCode);

				if ($brandingArray['onlinedesignerlogouturl'] != '')
				{
					$homeURL = $brandingArray['onlinedesignerlogouturl'];
				}
				else
				{
					$homeURL = UtilsObj::getArrayParam($hl_config, 'REDIRECTIONURL');
				}

				if ($homeURL != '')
				{
					// redirect back to the home URL with the mawebhlcreate parameter which is set to the
					// original product URL id as well as any other parameters that might have been attached to the URL
					// this allows the creation of a basket for the user to allow multi line item ordering
					$homeURL = UtilsObj::correctPath($homeURL, '/', true);
					$homeURL .= $hl_config['HIGHLEVELBASKETAPIREDIRECTPAGE'] . '?mawebhlcreate='. $productIdent;

					if (array_key_exists('gwm', $_GET))
					{
						$homeURL .= '&gwm='	. $_GET['gwm'];
					}

					if (array_key_exists('gd', $_GET))
					{
						$homeURL .= '&gd='	. $_GET['gd'];
					}

					if (array_key_exists('l', $_GET))
					{
						$homeURL .= '&l='	. $_GET['l'];
					}

					if (array_key_exists('wmo', $_GET))
					{
						$homeURL .= '&wmo='	. $_GET['wmo'];
					}

					if (array_key_exists('wmols', $_GET))
					{
						$homeURL .= '&wmols='	. $_GET['wmols'];
					}

					if (array_key_exists('wmoss', $_GET))
					{
						$homeURL .= '&wmoss='	. $_GET['wmoss'];
					}

					if (array_key_exists('uio', $_GET))
					{
						$homeURL .= '&uio='	. $_GET['uio'];
					}

					if (array_key_exists('aimo', $_GET))
					{
						$homeURL .= '&aimo=' . $_GET['aimo'];
					}

					if (array_key_exists('wmosd', $_GET))
					{
						$homeURL .= '&wmosd='	. $_GET['wmosd'];
					}

					if (($customParamStatus == 1) && (count($customParamArray) > 0))
					{
						// custom parameters are appended to the url, add them to the redirection url prepending the cp to the parameter
						foreach ($customParamArray as $cpParam => $cpValue)
						{
							$homeURL .= '&cp' . $cpParam . '=' . $cpValue;
						}
					}

					// wizard mode parameters are appended to the url, these are wmo, wmoss and wmols
					if ($wizardModeOverrideStatus == 1)
					{
						foreach ($parsedParamArray['wizardmodeoverride']['params'] as $key => $value)
						{
							$homeURL .= '&' . $key . '=' . $value;
						}
					}

					OnlineAPI_view::redirectToURL($homeURL);
				}
			}

			if ($isSingleItemWorkFlow)
			{
				//Detecting the device parameters
				UtilsObj::setSessionDeviceData();

				DatabaseObj::updateSession();

				$productArray = DatabaseObj::getProductFromCollectionCodeAndLayoutCode($createProjectConfig['collectioncode'], $createProjectConfig['productcode']);

				$smarty = SmartyObj::newSmarty('', '', '', $browserLanguageCode);
				$createProjectConfig['projectname'] = $smarty->get_config_vars('str_TitleUntitled') . ' ' . date(LocalizationObj::getLocaleFormatValue('str_DateTimeFormat'), time());

				$createProjectConfig['producttreesdata'] = [];
				$createProjectConfig['minimumprintsperproject'] = $productArray['minimumprintsperproject'];
				$createProjectConfig['usedefaultimagescalingbefore'] = $productArray['usedefaultimagescalingbefore'];
				$createProjectConfig['imagescalingbeforeenabled'] = $productArray['imagescalingbeforeenabled'];
                $createProjectConfig['imagescalingbefore'] = $productArray['imagescalingbefore'];
                $createProjectConfig['usedefaultaveragepicturesperpage'] = $productArray['usedefaultaveragepicturesperpage'];
                $createProjectConfig['averagepicturesperpage'] = $productArray['averagepicturesperpage'];
				$createProjectConfig['retroprints'] = $productArray['retroprints'];

				$browserArray = OnlineAPI_model::checkBrowsers();

				if ($browserArray['browsersupported'])
				{
					// we need to pass the basketref as part of the create project params
					$createProjectConfig['basketref'] = $basketRef;

					$userID = $_COOKIE['TPX-USER-ID'] ?? 0;

					$userName = '';
					$login = '';
					$ssoResult = '';
					$ssoResultParam = '';
					$ssoToken = '';
					$ssoPrivateDataArray = Array();
					$ssoExpireDate = '';

					// set the authenticate sso reason
					$ssoReason = TPX_USER_AUTH_REASON_ONLINE_PROJECT_CREATE;

					// retrieve the default language when creating new projects
					$languageCode = UtilsObj::getGETParam('l', '');

					// determine the default language code
					if ($languageCode == '')
					{
						$languageCode = UtilsObj::getBrowserLocale();
					}

					// extract the license key data
					$licenseKeyDataArray = DatabaseObj::getLicenseKeyFromCode($groupCode);

					// attempt to perform a single sign-on to the system
					$ssoResultArray = AuthenticateObj::authenticateLogin($ssoReason, -1, false, $languageCode,
						$licenseKeyDataArray['webbrandcode'], $groupCode, '', '', TPX_PASSWORDFORMAT_CLEARTEXT,
						'', true, false, true, $gSession['userdata']['ssotoken'], $gSession['userdata']['ssoprivatedata'], array());

					if ($ssoResultArray['result'] == '')
					{
						$userID = 0 !== $ssoResultArray['useraccountid'] ? $ssoResultArray : $userID;
						$userName = $ssoResultArray['username'];
						$ssoToken = $ssoResultArray['ssotoken'];
						$ssoPrivateDataArray = $ssoResultArray['ssoprivatedata'];
						$assetServiceDataArray = $ssoResultArray['assetservicedata'];
						$ssoExpireDate = $ssoResultArray['ssoexpiredate'];
					}
					else
					{
						$ssoResult = $ssoResultArray['result'];
						$ssoResultParam = $ssoResultArray['resultparam'];
					}

					// process the result of the single sign-on request
					switch ($ssoResult)
					{
						case 'SSOREDIRECT':
						{
							// redirect to grab the single sign-on token
							AuthenticateObj::ssoRedirect($ssoResultArray);
							break;
						}
						case '':
						{
							$previewExisitingProjectDataArray = array();
							$previewExisitingProjectDataArray['userid'] = $userID;
							$previewExisitingProjectDataArray['username'] = $userName;
							$previewExisitingProjectDataArray['login'] = $login;

							// pass the sso details over to online
							$createProjectConfig['ssotoken'] = $ssoToken;
							$createProjectConfig['ssoprivatedata'] = $ssoPrivateDataArray;
							$createProjectConfig['ssoexpiredate'] = $ssoExpireDate;
							$createProjectConfig['assetservicedata'] = $assetServiceDataArray;

							//Assign the project language
							$createProjectConfig['languagecode'] = $languageCode;

							$licenseKeyDataArray = DatabaseObj::getLicenseKeyFromCode($createProjectConfig['groupcode']);

							if ($licenseKeyDataArray['isactive'] == 1 && $licenseKeyDataArray['availableonline'] == 1)
							{
								$companyCode = $licenseKeyDataArray['companyCode'];
								$brandCode = $licenseKeyDataArray['webbrandcode'];

								$brandingArray = DatabaseObj::getBrandingFromCode($brandCode);

								$createProjectConfig['useraccount'] = AuthenticateObj::createEmptyUserAccount($licenseKeyDataArray, $createProjectConfig['groupcode'], $brandCode, $brandingArray);

								// set the logo link url
								if ($licenseKeyDataArray['usedefaultonlinedesignerlogolinkurl'] == 1)
								{
									// use url set by the brand
									$createProjectConfig['onlinedesignerlogolinkurl'] = $brandingArray['onlinedesignerlogolinkurl'];
									$createProjectConfig['onlinedesignerlogolinktooltip'] = $brandingArray['onlinedesignerlogolinktooltip'];
								}
								else
								{
									// use url set by the licensekey
									$createProjectConfig['onlinedesignerlogolinkurl'] = $licenseKeyDataArray['onlinedesignerlogolinkurl'];
									$createProjectConfig['onlinedesignerlogolinktooltip'] = $licenseKeyDataArray['onlinedesignerlogolinktooltip'];
								}

								// determine if perfectly clear is applied automatically.
								$createProjectConfig['automaticallyapplyperfectlyclearmode'] = self::determinePerfectlyClearAutomationMode($licenseKeyDataArray, $brandingArray);

								// Force the ai mode to be off when not active into the license key.
								if (! $gConstants['optionai'])
								{
									$createProjectConfig['aimodeoverride'] = 0;
								}

								if (TPX_PRODUCT_TYPE_SINGLE_PRINTS !== $productArray['collectiontype'])
								{
									OnlineAPI_model::getOnlineEditorSettings($createProjectConfig['onlineeditormode'], $createProjectConfig['groupcode'], $licenseKeyDataArray, $brandingArray, $createProjectConfig['enableswitchingeditor']);

									// we have to re add the switching editor and editormode as potentially the setting s have come from the brand/lkey
									$edlScriptDifferences['enableswitchingeditor'] = ($createProjectConfig['enableswitchingeditor'] === -1) ? null : $createProjectConfig['enableswitchingeditor'];
									$edlScriptDifferences['onlineeditormode'] = ($createProjectConfig['onlineeditormode'] === -1) ? null : $createProjectConfig['onlineeditormode'];
									$edlScriptDifferences['largescreenwizardmode'] = ($createProjectConfig['largescreenwizardmode'] === -1) ? null : $createProjectConfig['largescreenwizardmode'];
									$edlScriptDifferences['enableswitchingeditor'] = ($createProjectConfig['enableswitchingeditor'] === -1) ? null : $createProjectConfig['enableswitchingeditor'];
									$edlScriptDifferences['aimodeoverride'] = ($createProjectConfig['aimodeoverride'] === -1) ? null : $createProjectConfig['aimodeoverride'];
									$edlScriptDifferences['guestworkflowmode'] = ($newGuestWorkFlowMode === -1) ? null : $newGuestWorkFlowMode;

									$createProjectConfig['experienceoverrides'] = $edlScriptDifferences;

									// set the theme in the config
									$createProjectConfig['theme'] = $brandingArray['theme'];
									$createProjectConfig['username'] = $userName;

									//single item workflow
									try {
										$client = new Client();
										$urlCreator = new Project($client, UtilsObj::correctPath($brandingArray['onlinedesignerurl'], '/', false), $userID, $createProjectConfig);
										$projectRef = $urlCreator->getProjectRef();
										$projectName = $urlCreator->getProjectName();
										$designURL = $urlCreator->getDesignerURL();
									}
									catch (GuzzleHttp\Exception\ClientException|GuzzleHttp\Exception\ServerException $e) {
										$errorData = json_decode($e->getResponse()->getBody()->getContents(), true);
										$error = $errorData['code'] ?? $e->getMessage();
									}
								}
								else
								{
									$dataCacheKey = $createProjectConfig['groupcode'] . '.' . $createProjectConfig['companycode'] . '.' . $createProjectConfig['collectioncode'];
									$productTreesData = OnlineAPI_model::getProductTreesAPIData($createProjectConfig['productcode'], $createProjectConfig['collectioncode'], $tenantID, $dataCacheKey);
									$createProjectConfig['producttreesdata'] = $productTreesData;

									$onlineParamData = OnlineAPI_model::prepareParamDataToCreateOnlineSession(TPX_OPEN_MODE_NEW_PROJECT, '', $previewExisitingProjectDataArray, $createProjectConfig, $productArray['collectiontype']);
									$error = $onlineParamData['result'];
								}
							}
							else
							{
								$error = TPX_ONLINE_ERROR_INACTIVELICENSEKEY;
							}

							if ($error == '')
							{
								$onlineParamData['collectionname'] = $productArray['collectionname'];
								$onlineParamData['layoutname'] = $productArray['name'];
								$onlineParamData['tenantid'] = $tenantID;

								if (TPX_PRODUCT_TYPE_SINGLE_PRINTS === $productArray['collectiontype'])
								{
									$onlineBrandURL = AuthenticateObj::createOnlineSession($onlineParamData);
									$projectRef = $onlineBrandURL['projectref'];
									$projectName = $onlineBrandURL['projectname'];
									$designURL = $onlineBrandURL['brandurl'];
									$error = $onlineBrandURL['error'];
								}

								if ($error == '')
								{
									// Redirect user to onlineURL once online sessions have been created successfully.
									if ($designURL != '')
									{
										OnlineAPI_view::redirectToURL($designURL);
									}
									else
									{
										OnlineAPI_model::sendCommunicationFailedEmail($onlineParamData, $error, '');
										OnlineAPI_view::communicationError();
									}
								}
								else
								{
									OnlineAPI_model::sendCommunicationFailedEmail($onlineParamData, $error, '');
									OnlineAPI_view::returnResult($error, $browserLanguageCode);
								}
							}
							else
							{
								OnlineAPI_view::returnResult($error, $browserLanguageCode);
							}

							break;
						}
						default:
						{
							require_once('../Welcome/Welcome_view.php');

							Welcome_view::displaySSOError($ssoResult, $ssoResultParam);
							break;
						}
					}
				}
				else
				{
					OnlineAPI_view::broswerNotSupported($browserArray, $browserLanguageCode);
				}
			}
		}
	}

	static function create2()
	{
		$deviceDetection = UtilsObj::getGETParam('dd', '');

		// check to see if the we need to generate the device detection string
		if ($deviceDetection == TPX_DEVICE_DETECTION_TPXGENERATE)
		{
			UtilsDeviceDetection::pingJS();
		}
		else
		{
			self::create(TPX_BASKETWORKFLOWTYPE_LOWLEVELAPI);
		}
	}

	static function create3()
	{
		self::create(TPX_BASKETWORKFLOWTYPE_HIGHLEVELAPI);
	}

	static function editProject($pBasketWorkFlowType = TPX_BASKETWORKFLOWTYPE_HIGHLEVELAPI)
	{
		self::openProject($pBasketWorkFlowType, TPX_OPEN_MODE_EXISTING_PROJECT, '');
	}

	static function previewSharedProject()
	{
		require_once('../Share/Share_model.php');
		require_once('../Share/Share_view.php');

		$hash = UtilsObj::getGETParam('ref2', '');
		$projectRefResult = Share_model::getProjectRefUsingHash($hash);

        // Populate the web brand session to enable access to all related branding on the session
        AuthenticateObj::setSessionWebBrand($projectRefResult['brandcode']);

		if ($projectRefResult['result'] === 0)
		{
			$projectRef = $projectRefResult['projectref'];
			$shareHideBranding = substr($projectRefResult['method'],-1);
			self::openProject(TPX_BASKETWORKFLOWTYPE_NORMAL, TPX_OPEN_MODE_PREVIEW_EXISITING, $projectRef, $shareHideBranding);
		}
		else
		{
			$resultArray = [
				"result" => $projectRefResult['result']
			];
			Share_view::previewSharedProject($resultArray);
		}

	}

	/**
	 * Gets the project URL which is used to open a project via API call.
	 *
	 * @param int $pBasketWorkFlowType The basket API type.
	 * @param int $pOpenMode The mode to open the project in.
	 * @param string $pProjectRef The project reference
	 */
	static function openProject($pBasketWorkFlowType = TPX_BASKETWORKFLOWTYPE_LOWLEVELAPI, $pOpenMode = TPX_OPEN_MODE_EXISTING_PROJECT, $pProjectRef = '', $pShareHideBranding = 0)
	{
		global $gConstants;
        global $gSession;

		$editMethodExists = false;

		$browserLanguageCode = UtilsObj::cleanseLanguageCode(UtilsObj::getGETParam('l', ''), $gConstants['defaultlanguagecode']);
		$deviceDetection = UtilsObj::getGETParam('dd', '');
		$editingType = UtilsObj::getGETParam('editingtype', 0);
		$basketRef = '';
		$webBrandCode = '';
		$isSSOEnabled = false;

		$resultArray = array(
			'result' => TPX_ONLINE_ERROR_NONE,
			'resultmessage' => '',
			'brandcode' => '',
			'groupcode' => '',
			'groupdata' => '',
			'collectioncode' => '',
			'collectionname' => '',
			'productcode' => '',
			'layoutname' => '',
			'projectref' => '',
			'projectname' => '',
			'userdata' => '',
			'designurl' => '');

		if ($pBasketWorkFlowType == TPX_BASKETWORKFLOWTYPE_HIGHLEVELAPI)
		{
			$browserLanguageCode = UtilsObj::cleanseLanguageCode(UtilsObj::getPOSTParam('browserlocale', ''), $browserLanguageCode);

	        $isSSOEnabled = (UtilsObj::getPOSTParam('ssoenabled', TPX_SSO_HIGHLEVEL_ENABLED_OFF) != TPX_SSO_HIGHLEVEL_ENABLED_OFF);
		}

		$forceKill = UtilsObj::getPostParam('forcekill', 0);

		if ($pBasketWorkFlowType == TPX_BASKETWORKFLOWTYPE_NORMAL)
		{
			$forceKill = 1;
		}

		$editProjectConfig = array();
		$editProjectConfig['result'] = '';
		$editProjectConfig['resultmessage'] = '';
		$editProjectConfig['languagecode'] = $browserLanguageCode;
        $editProjectConfig['defaultlanguagecode'] = $gConstants['defaultlanguagecode'];
        $editProjectConfig['projectref'] = UtilsObj::getPostParam('projectref', $pProjectRef);
		$editProjectConfig['ccnotificationsenabled'] = false;
		$editProjectConfig['forcekill'] = $forceKill;
		$editProjectConfig['canunlock'] = 0;
		$editProjectConfig['devicedetection'] = $deviceDetection;
		$editProjectConfig['minlife'] = -1;
		$editProjectConfig['checkoutname'] = '';
		$editProjectConfig['abandonurl'] = '';
		$editProjectConfig['abandonname'] = '';
		$editProjectConfig['disablebackbutton'] = 0;
		$editProjectConfig['cansignin'] = 1;
		$editProjectConfig['cansignout'] = 1;
		$editProjectConfig['editprojectnameonfirstsave'] = 1;
		$editProjectConfig['ssotoken'] = '';
		$editProjectConfig['ssoprivatedata'] = array();
		$editProjectConfig['ssoexpiredate'] = '';
		$editProjectConfig['assetservicedata'] = array();
		$editProjectConfig['useraccount'] = array();
		$editProjectConfig['groupcode'] = '';
		$editProjectConfig['3dmodelfileurl'] = '';
		$editProjectConfig['3dmodelsystemresourcefileid'] = 0;
		$editProjectConfig['previewmode'] = ($pOpenMode == TPX_OPEN_MODE_PREVIEW_EXISITING);
		$editProjectConfig['requirepasswordforsessioninactivity'] = true;
		$editProjectConfig['canshareproject'] = -1;
		$editProjectConfig['onlinedesignerlogolinkurl'] = '';
		$editProjectConfig['onlinedesignerlogolinktooltip'] = '';
		$edlScriptDifferences = [];

		$editFromHighLevelBasketList = 0;
		$ssoError = false;
		$unableToEditProject = false;

		self::includeOnlineBasketAPI($pBasketWorkFlowType);

		if ($pBasketWorkFlowType == TPX_BASKETWORKFLOWTYPE_LOWLEVELAPI)
		{
			$EDLBasketClass = 'OnlineBasketAPI';
			$editMethodExists = method_exists($EDLBasketClass, 'editProject');
		}
		else
		{
			$EDLBasketClass = 'OnlineBasketHighLevelAPI';
			$editMethodExists = true;
		}

		if ($editMethodExists)
		{
			if ($pBasketWorkFlowType == TPX_BASKETWORKFLOWTYPE_LOWLEVELAPI)
			{
				$companyCode = '';
				$brandingArray = array();
				$llSSO = false;

				if ($editProjectConfig['groupcode'] == '')
				{
					if (method_exists($EDLBasketClass, 'getDefaultGroupCode'))
					{
						$editProjectConfig['groupcode'] = OnlineBasketAPI::getDefaultGroupCode();
					}
				}

				if ($editProjectConfig['groupcode'] != '')
				{
					$licenseKeyArray = DatabaseObj::getLicenseKeyFromCode($editProjectConfig['groupcode']);

					$companyCode = $licenseKeyArray['companyCode'];
					$webBrandCode = $licenseKeyArray['webbrandcode'];

					$brandingArray = DatabaseObj::getBrandingFromCode($webBrandCode);

					$editProjectConfig['useraccount'] = AuthenticateObj::createEmptyUserAccount($licenseKeyArray, $editProjectConfig['groupcode'], $webBrandCode, $brandingArray);
				}

				// populate the sso private data from the record in the database
				$ssoLLPrivateData = self::getSSOLLPrivateData();

				$editProjectConfig['ssoprivatedata'] = $ssoLLPrivateData['ssoprivatedata'];

				$groupCode = $editProjectConfig['groupcode'];

				$editOrigArray = $editProjectConfig;
				$editProjectConfig = OnlineBasketAPI::editProject($editProjectConfig);
				$edlScriptDifferences = self::recursiveDiff($editOrigArray, $editProjectConfig);

				if ($editProjectConfig['groupcode'] !== $groupCode)
				{
					// The groupcode was changed by the script, get the license key again.
					$licenseKeyArray = DatabaseObj::getLicenseKeyFromCode($editProjectConfig['groupcode']);

					// Update the values.
					$companyCode = $licenseKeyArray['companyCode'];
					$webBrandCode = $licenseKeyArray['webbrandcode'];
				}

				// if the generateSharePreviewLink method does not exist then we can't share a project.
				if (! method_exists($EDLBasketClass, 'generateSharePreviewLink'))
				{
					$editProjectConfig['canshareproject'] = false;
				}

				self::updateSSOLLPrivateData($editProjectConfig['ssoprivatedata'], $ssoLLPrivateData['authkey']);

                if (!empty($editProjectConfig['useraccount']) || isset($editProjectConfig['authkey']))
				{
					$llSSO = true;
					$userAccountID = 0;

                    if (!isset($editProjectConfig['authkey'])){

                        $userAccountArray = AuthenticateObj::updateOrInsertExternalAccount($userAccountID, $editProjectConfig['useraccount'], true, -1,
                            '', '', $webBrandCode, $editProjectConfig['groupcode'], $companyCode,
                            $editProjectConfig['updategroupcode'], $editProjectConfig['updateaccountdetails'],
                            $editProjectConfig['updateaccountbalance'], $editProjectConfig['updategiftcardbalance']);
                    }
                    else {
                        // clean up any authentication data records
                        AuthenticateObj::deleteAuthenticationDataRecords();

                        $authenticationRecord = AuthenticateObj::getAuthenticationDataRecord(TPX_AUTHENTICATIONTYPE_LOWLEVEL, $editProjectConfig['authkey'], true);

                        if ($authenticationRecord['found'])
                        {
                            $userAccountID = $authenticationRecord['ref'];
                            $userAccountArray = DatabaseObj::getUserAccountFromID($userAccountID);
                        }
                        else{
                            $resultArray['result'] = TPX_ONLINE_ERROR_LOWLEVELINTERNALERROR;
                        }
                    }

                    if ($userAccountArray['result'] == '')
                	{
						$userID = $userAccountID;
						$userName = $userAccountArray['contactfirstname'] . ' ' . $userAccountArray['contactlastname'];
						$editProjectConfig['requirepasswordforsessioninactivity'] = $editProjectConfig['useraccount']['requirepasswordforsessioninactivity'];

						// if this is sso and the ssotoken is empty default it to 1. this prompts online that SSO has been used
						if ($editProjectConfig['ssotoken'] == '')
						{
							$editProjectConfig['ssotoken'] = '1';
						}
					}
					else
					{
						if ($userAccountArray['result'] == 'str_DatabaseError')
						{
							$resultArray['result'] = TPX_ONLINE_ERROR_DATABASE;
						}
						elseif ($userAccountArray['result'] == 'str_ErrorEmptyGroupCode')
						{
							$resultArray['result'] = TPX_ONLINE_ERROR_EMPTYGROUPCODE;
						}
						elseif ($userAccountArray['result'] == 'str_ErrorAccountMisMatch')
						{
							$resultArray['result'] = TPX_ONLINE_ERROR_ACCOUNT_MISTMATCH;
						}
						elseif ($userAccountArray['result'] == 'str_ErrorAccountTaskNotAllowed')
						{
							$resultArray['result'] = TPX_ONLINE_ERROR_ACCOUNTTASKNOTALLOWED;
						}
					}
                }
			}

			$newUserID = 0;

			if (($pBasketWorkFlowType == TPX_BASKETWORKFLOWTYPE_LOWLEVELAPI) && ($llSSO))
			{
				$newUserID = $userID;
			}

            $getEditProjectAPIDataArray = OnlineAPI_model::getEditProjectAPIData($editProjectConfig['projectref'], $newUserID);

			if (($pBasketWorkFlowType == TPX_BASKETWORKFLOWTYPE_LOWLEVELAPI) && ($llSSO))
			{
				// make sure that the user who is opening the project is the user who owns the project
				if (($getEditProjectAPIDataArray['userid'] > 0) && ($getEditProjectAPIDataArray['userid'] != $newUserID))
				{
					$resultArray['result'] = TPX_ONLINE_ERROR_INVALIDUSERID;
				}
			}

			if ($getEditProjectAPIDataArray['error'] != TPX_ONLINE_ERROR_NONE)
			{
				// is there an error during the restore process
				if ($getEditProjectAPIDataArray['projectdetails']['restoremessage'] == TPX_ARCHIVE_RESTORE_FAILED)
				{
					$resultArray['result'] = TPX_ONLINE_ERROR_RESTORE_FAILED;
				}
			}

			if ($resultArray['result'] == TPX_ONLINE_ERROR_NONE)
			{
				$licenseKeyArray = DatabaseObj::getLicenseKeyFromCode($getEditProjectAPIDataArray['groupcode']);

				$webBrandCode = $licenseKeyArray['webbrandcode'];

				$userID = 0;
				$userName = '';
				$ssoResult = '';
				$ssoResultParam = '';
				$ssoToken = '';
				$ssoPrivateDataArray = Array();
				$ssoExpireDate = '';
				$startSession = true;
				$basketData = array();

				$brandingArray = DatabaseObj::getBrandingFromCode($webBrandCode);

				// get the CDN URL from the brand
				$editProjectConfig['onlinedesignercdnurl'] = $brandingArray['onlinedesignercdnurl'];

				// check to make sure the EDL_OnlineBasketAPI script has not already set a logo link
				if ($editProjectConfig['onlinedesignerlogolinkurl'] == '')
				{
					// pass the logo link url set in control centre
					if ($licenseKeyArray['usedefaultonlinedesignerlogolinkurl'] == 1)
					{
						// use url set by the brand
						$editProjectConfig['onlinedesignerlogolinkurl'] = $brandingArray['onlinedesignerlogolinkurl'];
						$editProjectConfig['onlinedesignerlogolinktooltip'] = $brandingArray['onlinedesignerlogolinktooltip'];
					}
					else
					{
						// use url set by the licensekey
						$editProjectConfig['onlinedesignerlogolinkurl'] = $licenseKeyArray['onlinedesignerlogolinkurl'];
						$editProjectConfig['onlinedesignerlogolinktooltip'] = $licenseKeyArray['onlinedesignerlogolinktooltip'];
					}
				}

				if ($pBasketWorkFlowType == TPX_BASKETWORKFLOWTYPE_HIGHLEVELAPI)
				{
					$basketRef = UtilsObj::getHighLevelBasketAPIPOSTParams('mawebhlbr', TPX_ONLINE_BASKETAPI_DEFAULTBASKETREF);

					// we must check to see if there is a valid user session for the current basketref
					$highLevelBasketUserSesionResultArray = AuthenticateObj::checkHighLevelBasketSessionActive($basketRef);

					if (($highLevelBasketUserSesionResultArray['result'] == '') && ($highLevelBasketUserSesionResultArray['sessionactive'] == 1))
					{
						$startSession = false;

						$gSession = DatabaseObj::getSessionData($highLevelBasketUserSesionResultArray['sessionid']);

						$ssoToken = $gSession['userdata']['ssotoken'];
						$ssoPrivateDataArray = $gSession['userdata']['ssoprivatedata'];
					}
				}

				if ($pOpenMode === TPX_OPEN_MODE_PREVIEW_EXISITING)
				{
					// If opening preview, set the reason so it does not try to authenticate a user.
					$reason = TPX_USER_AUTH_REASON_HIGHLEVEL_ONLINE_PROJECT_PREVIEW;
				}
				else
				{
					$reason = TPX_USER_AUTH_REASON_HIGHLEVEL_ONLINE_PROJECT_EDIT;

					if ($editingType == 1)
					{
						$reason = TPX_USER_AUTH_REASON_HIGHLEVEL_ONLINE_PROJECT_CONTINUE_EDIT;
					}
				}

				$basketData['apitype'] = TPX_BASKETWORKFLOWTYPE_HIGHLEVELAPI;
				$basketData['ref'] = $basketRef;

				// attempt to perform a single sign-on to the system
				$ssoResultArray = AuthenticateObj::authenticateLogin($reason, -1, false, $browserLanguageCode,
																	$webBrandCode, $getEditProjectAPIDataArray['groupcode'], '', '',
																	TPX_PASSWORDFORMAT_CLEARTEXT,  '', true, $startSession, true, $ssoToken, $ssoPrivateDataArray, $basketData);

				if ($ssoResultArray['result'] == '')
				{
					$userID = $ssoResultArray['useraccountid'];
					$userName = $ssoResultArray['username'];
					$ssoToken = $ssoResultArray['ssotoken'];
					$ssoPrivateDataArray = $ssoResultArray['ssoprivatedata'];
					$assetServiceDataArray = $ssoResultArray['assetservicedata'];
					$ssoExpireDate = $ssoResultArray['ssoexpiredate'];
				}
				else
				{
					$ssoResult = $ssoResultArray['result'];
					$ssoResultParam = $ssoResultArray['resultparam'];
				}

				// process the result of the single sign-on request
				switch ($ssoResult)
				{
					case 'SSOREDIRECT':
					{
						// redirect to grab the single sign-on token
						$resultArray['designurl'] = $ssoResultArray['resultparam'];
						break;
					}
					case '':
					{
						if ((($userID > 0) && ($isSSOEnabled)) || (! $isSSOEnabled))
						{
							if ($pBasketWorkFlowType == TPX_BASKETWORKFLOWTYPE_HIGHLEVELAPI)
							{
								$editProjectConfig = OnlineAPI_model::buildHighLevelProjectParams($editProjectConfig, 'editProject', $webBrandCode);
							    $editProjectConfig['ccnotificationsenabled'] = true;
								$editFromHighLevelBasketList = UtilsObj::getPostParam('editfrombasket', 1);

								if ($isSSOEnabled)
								{
									// make sure that the user who is opening the project is the user who owns the project
									if (($getEditProjectAPIDataArray['userid'] > 0) && ($getEditProjectAPIDataArray['userid'] != $userID))
									{
										$editProjectConfig['result'] = TPX_ONLINE_ERROR_INVALIDUSERID;
										$ssoError = true;
									}
								}
							}

							$browserLanguageCode = UtilsObj::cleanseLanguageCode($editProjectConfig['languagecode'], $browserLanguageCode);

							if ($editProjectConfig['result'] == '')
							{
								$matchesCount = preg_match('/^[0-9]+[_]{1}[0-9]+$/', $editProjectConfig['projectref']);

								if ($matchesCount > 0)
								{
									if (($editProjectConfig['devicedetection'] != '') || ($pBasketWorkFlowType == TPX_BASKETWORKFLOWTYPE_HIGHLEVELAPI || $pBasketWorkFlowType == TPX_BASKETWORKFLOWTYPE_NORMAL))
									{
										if ($pBasketWorkFlowType == TPX_BASKETWORKFLOWTYPE_LOWLEVELAPI)
										{
											$_GET['dd'] = $editProjectConfig['devicedetection'];
											$deviceDetectionArray = UtilsObj::setSessionDeviceData(true);
											$deviceActive = $deviceDetectionArray['isactive'];
										}
										else
										{
											$deviceDetectionArray = array();
											UtilsObj::setSessionDeviceData();
											$deviceActive = 1;
										}

										if ($deviceActive)
										{
											DatabaseObj::updateSession();

											$paramArray = array();
											$paramArray['projectreflist'] = array($editProjectConfig['projectref']);
											$paramArray['forcekill'] = $editProjectConfig['forcekill'];
											$paramArray['purgedays'] = 0;
											$paramArray['canunlock'] = $editProjectConfig['canunlock'];
											$paramArray['action'] = ($pOpenMode === TPX_OPEN_MODE_EXISTING_PROJECT) ? 'editing' : 'previewexisting';
											$paramArray['basketref'] = '';

											$checkDeleteSessionResult = OnlineAPI_model::checkDeleteSession($paramArray);

											if ($checkDeleteSessionResult['error'] == '')
											{
												$projectRef = $editProjectConfig['projectref'];

												$project = $checkDeleteSessionResult['projectitemarray'][$projectRef];

												if ($project['projectexists'] == true)
												{
													if ($getEditProjectAPIDataArray['error'] == TPX_ONLINE_ERROR_NONE)
													{
														$onlineEditorMode = $getEditProjectAPIDataArray['onlineeditormode'];
														$switchEditor = $getEditProjectAPIDataArray['showswitcheditor'];

														$resultArray['brandcode'] = $webBrandCode;
														$resultArray['groupcode'] = $getEditProjectAPIDataArray['groupcode'];
														$resultArray['collectioncode'] = $getEditProjectAPIDataArray['collectioncode'];
														$resultArray['collectionname'] = $getEditProjectAPIDataArray['collectionname'];
														$resultArray['productcode'] = $getEditProjectAPIDataArray['layoutcode'];
														$resultArray['productname'] = $getEditProjectAPIDataArray['layoutname'];
														$resultArray['projectname'] = $getEditProjectAPIDataArray['projectname'];
														$resultArray['projectref'] = $projectRef;
													}
													else
													{
														$resultArray['result'] = TPX_ONLINE_ERROR_PROJECTDOESNOTEXIST;
														$unableToEditProject = true;
													}
												}
												else
												{
													$resultArray['result'] = TPX_ONLINE_ERROR_PROJECTDOESNOTEXIST;
													$unableToEditProject = true;
												}

												if ($resultArray['result'] == TPX_ONLINE_ERROR_NONE)
												{
													if (($project['projectlocked'] == 0) || (($project['projectlocked'] == 1) && ($editProjectConfig['canunlock'] == 1)))
													{
														if (($project['canmodify'] == 1) || ($pOpenMode === TPX_OPEN_MODE_PREVIEW_EXISITING))
														{
															if ($project['sessionactive'] == true)
															{
																// return kill session message
																$resultArray['result'] = TPX_ONLINE_ERROR_PROJECTALREADYOPEN;
																$unableToEditProject = true;
															}
															else
															{
																if ($project['projectexists'] == true)
																{
																	$openOnlineProjectConfig = array();
																	$openOnlineProjectConfig['editprojectfromapi'] = $editProjectConfig['projectref'];
																	$openOnlineProjectConfig['ccnotificationsenabled'] = $editProjectConfig['ccnotificationsenabled'];
																	$openOnlineProjectConfig['languagecode'] = $editProjectConfig['languagecode'];
                                                                    $openOnlineProjectConfig['defaultlanguagecode'] = $editProjectConfig['defaultlanguagecode'];
                                                                    $openOnlineProjectConfig['projectref'] = $editProjectConfig['projectref'];
																	$openOnlineProjectConfig['groupcode'] = $getEditProjectAPIDataArray['groupcode'];
																	$openOnlineProjectConfig['webbrandcode'] = $webBrandCode;
																	$openOnlineProjectConfig['companycode'] = $companyCode;
																	$openOnlineProjectConfig['userid'] = $getEditProjectAPIDataArray['userid'];
																	$openOnlineProjectConfig['collectioncode'] = $getEditProjectAPIDataArray['collectioncode'];
																	$openOnlineProjectConfig['collectionname'] = $getEditProjectAPIDataArray['collectionname'];
																	$openOnlineProjectConfig['layoutcode'] = $getEditProjectAPIDataArray['layoutcode'];
																	$openOnlineProjectConfig['layoutname'] = $getEditProjectAPIDataArray['layoutname'];
																	$openOnlineProjectConfig['workflowtype'] = $getEditProjectAPIDataArray['workflowtype'];
																	$openOnlineProjectConfig['minlife'] = $editProjectConfig['minlife'];
																	$openOnlineProjectConfig['basketapiworkflowtype'] = $pBasketWorkFlowType;
																	$openOnlineProjectConfig['basketref'] = $basketRef;
																	$openOnlineProjectConfig['cansignin'] = $editProjectConfig['cansignin'];
																	$openOnlineProjectConfig['cansignout'] = $editProjectConfig['cansignout'];
																	$openOnlineProjectConfig['checkoutname'] = $editProjectConfig['checkoutname'];
																	$openOnlineProjectConfig['editprojectnameonfirstsave'] = $editProjectConfig['editprojectnameonfirstsave'];
																	$openOnlineProjectConfig['abandonurl'] =  $editProjectConfig['abandonurl'];
																	$openOnlineProjectConfig['abandonname'] =  $editProjectConfig['abandonname'];
																	$openOnlineProjectConfig['disablebackbutton'] = $editProjectConfig['disablebackbutton'];
																	$openOnlineProjectConfig['loadedstatus'] = $getEditProjectAPIDataArray['loadedstatus'];
																	$openOnlineProjectConfig['templateref'] = $getEditProjectAPIDataArray['templateref'];
																	$openOnlineProjectConfig['originalref'] = $getEditProjectAPIDataArray['originalref'];
																	$openOnlineProjectConfig['3dmodelsystemresourcefileid'] = $getEditProjectAPIDataArray['3dmodelsystemresourcefileid'];
																	$openOnlineProjectConfig['onlinedesignerlogolinkurl'] = $editProjectConfig['onlinedesignerlogolinkurl'];
																	$openOnlineProjectConfig['onlinedesignerlogolinktooltip'] = $editProjectConfig['onlinedesignerlogolinktooltip'];
																	$openOnlineProjectConfig['onlinedesignercdnurl'] = $editProjectConfig['onlinedesignercdnurl'];
																	$openOnlineProjectConfig['featuretoggle'] = $getEditProjectAPIDataArray['featuretoggle'];
																	$openOnlineProjectConfig['enableswitchingeditor'] = $switchEditor;
																	$openOnlineProjectConfig['canshareproject'] = $editProjectConfig['canshareproject'];
																	$openOnlineProjectConfig['automaticallyapplyperfectlyclearmode'] = $getEditProjectAPIDataArray['automaticallyapplyperfectlyclearmode'];
																	$openOnlineProjectConfig['minimumprintsperproject'] = $getEditProjectAPIDataArray['minimumprintsperproject'];
																	$openOnlineProjectConfig['onlineeditormode'] = $onlineEditorMode;
																	$openOnlineProjectConfig['requirepasswordforsessioninactivity'] = $editProjectConfig['requirepasswordforsessioninactivity'];
																	$openOnlineProjectConfig['aimodeoverride'] = -1;
																	$openOnlineProjectConfig['forcekill'] = $editProjectConfig['forcekill'];

																	// if we are opening a project that has already been ordered and flagged for modification we must disable components in the designer
																	$openOnlineProjectConfig['allowupsell'] = ($project['canmodify'] == 1 && $project['orderfound']) ? false : true;

																	if ($project['orderfound'])
																	{
																		$openOnlineProjectConfig['orderfound'] = $project['orderfound'];
																	}

																	$openOnlineProjectConfig['experienceoverrides'] = $edlScriptDifferences;

																	if ($pBasketWorkFlowType == TPX_BASKETWORKFLOWTYPE_HIGHLEVELAPI)
																	{
																		// pass the sso details over to online
																		$openOnlineProjectConfig['ssotoken'] = $ssoToken;
																		$openOnlineProjectConfig['ssoprivatedata'] = $ssoPrivateDataArray;
																		$openOnlineProjectConfig['ssoexpiredate'] = $ssoExpireDate;
																		$openOnlineProjectConfig['assetservicedata'] = $assetServiceDataArray;
																	}
																	else if ($pBasketWorkFlowType == TPX_BASKETWORKFLOWTYPE_LOWLEVELAPI)
																	{
																		// pass the sso details over to online
																		$openOnlineProjectConfig['ssotoken'] = $editProjectConfig['ssotoken'];
																		$openOnlineProjectConfig['ssoprivatedata'] = $editProjectConfig['ssoprivatedata'];
																		$openOnlineProjectConfig['ssoexpiredate'] = $editProjectConfig['ssoexpiredate'];
																		$openOnlineProjectConfig['assetservicedata'] = $editProjectConfig['assetservicedata'];
																	}

																	// Preview mode settings.
																	$previewExistingProjectConfig = array();

																	if ($pOpenMode === TPX_OPEN_MODE_PREVIEW_EXISITING)
																	{
																		$previewExistingProjectConfig['groupcode'] = $openOnlineProjectConfig['groupcode'];
																		$previewExistingProjectConfig['webbrandcode'] = $openOnlineProjectConfig['webbrandcode'];
																		$previewExistingProjectConfig['projectref'] = $openOnlineProjectConfig['projectref'];
																		$previewExistingProjectConfig['userid'] = $openOnlineProjectConfig['userid'];
																		$previewExistingProjectConfig['productcollectioncode'] = $openOnlineProjectConfig['collectioncode'];
																		$previewExistingProjectConfig['productlayoutcode'] = $openOnlineProjectConfig['layoutcode'];
																		$previewExistingProjectConfig['previewviewsource'] = '';
																		$previewExistingProjectConfig['workflowtype'] = $openOnlineProjectConfig['workflowtype'];
																		$previewExistingProjectConfig['loadedstatus'] = $getEditProjectAPIDataArray['loadedstatus'];
																		$previewExistingProjectConfig['templateref'] = $getEditProjectAPIDataArray['templateref'];
																	}

                                                                    $openOnlineProjectResult = OnlineAPI_model::openOnlineProject($pOpenMode, $openOnlineProjectConfig, $previewExistingProjectConfig, true, false);

																	if ($openOnlineProjectResult['error'] == '')
																	{
                                                                        $resultArray['designurl'] = $openOnlineProjectResult['brandurl'];

																		if (($pBasketWorkFlowType == TPX_BASKETWORKFLOWTYPE_HIGHLEVELAPI) && ($editFromHighLevelBasketList == 1))
																		{
																			$projectRefToRemove = "'" . $editProjectConfig['projectref'] . "'";
																			$removeProjcetFromOnlineBasket = OnlineAPI_model::removeItemsFromBasket($projectRefToRemove);
																		}
																	}
																	else
																	{
																		$resultArray['result'] = $openOnlineProjectResult['error'];
																		$resultArray['resultmessage'] = $openOnlineProjectResult['error'];
																		$unableToEditProject = true;
																	}
																}
																else
																{
																	//the project has either been deleted or purged
																	$resultArray['result'] = TPX_ONLINE_ERROR_PROJECTDOESNOTEXIST;
																	$unableToEditProject = true;
																}
															}
														}
														else
														{
															$resultArray['result'] = TPX_ONLINE_ERROR_PROJECTINPRODUCTION;
															$unableToEditProject = true;
														}
													}
													else
													{
														$resultArray['result'] = TPX_ONLINE_ERROR_PROJECTLOCKED;
														$unableToEditProject = true;
													}
												}
											}
											else
											{
												$unableToEditProject = true;
											}
										}
										else
										{
											$resultArray['result'] = TPX_ONLINE_ERROR_INVALIDDEVICEDETECTIONDATA;
											$unableToEditProject = true;
										}
									}
									else
									{
										$resultArray['result'] = TPX_ONLINE_ERROR_INVALIDDEVICEDETECTIONDATA;
										$unableToEditProject = true;
									}
								}
								else
								{
									$resultArray['result'] = TPX_ONLINE_ERROR_PROJECTDOESNOTEXIST;
									$unableToEditProject = true;
								}
							}
							else
							{
								$resultArray['result'] = $editProjectConfig['result'];
								$resultArray['resultmessage'] = $editProjectConfig['resultmessage'];
								$unableToEditProject = true;
							}
						}
						else
						{
							$resultArray['result'] = TPX_ONLINE_ERROR_ACCOUNT_MISTMATCH;
							$ssoError = true;

							OnlineAPI_model::deleteHighLevelUserSession($basketRef);
						}

						break;
					}
					default:
					{
						$ssoError = true;

						if ($ssoResult == 'str_DatabaseError')
						{
							$resultArray['result'] = TPX_ONLINE_ERROR_DATABASE;
						}
						elseif ($ssoResult == 'str_ErrorEmptyGroupCode')
						{
							$resultArray['result'] = TPX_ONLINE_ERROR_EMPTYGROUPCODE;
						}
						elseif (($ssoResult == 'str_ErrorAccountMisMatch') || ($ssoResult == 'str_ErrorDuplicateUserName'))
						{
							$resultArray['result'] = TPX_ONLINE_ERROR_ACCOUNT_MISTMATCH;
						}
						elseif ($ssoResult == 'str_ErrorAccountTaskNotAllowed')
						{
							$resultArray['result'] = TPX_ONLINE_ERROR_ACCOUNTTASKNOTALLOWED;
						}
						else
						{
							$returnArray['result'] = 99;
							$returnArray['resultmessage'] = $ssoResultParam;
						}

						break;
					}
				}
			}

			if ($isSSOEnabled)
			{
				if ($ssoError)
				{
					OnlineAPI_model::deleteHighLevelUserSession($basketRef);
					$resultArray['ssoerror'] = true;
				}
				else
				{
					$resultArray['ssoerror'] = false;
				}

				if (($ssoError) || ($unableToEditProject))
				{
					$hl_config = UtilsObj::readWebBrandConfigFile('../config/onlinebaskethighlevelapi.conf', $webBrandCode);

					$brandingArray = DatabaseObj::getBrandingFromCode($webBrandCode);

					if ($brandingArray['onlinedesignerlogouturl'] != '')
					{
						$homeURL = $brandingArray['onlinedesignerlogouturl'];
					}
					else
					{
						$homeURL = UtilsObj::getArrayParam($hl_config, 'REDIRECTIONURL');
					}

					$resultArray['redirecturl'] = $homeURL;
				}
			}
			else
			{
				$resultArray['ssoerror'] = false;
			}

			// Switch used in preparation for future extensions to this function
			switch ($pBasketWorkFlowType)
			{
				case TPX_BASKETWORKFLOWTYPE_NORMAL:
					Share_view::previewSharedProject($resultArray, $pShareHideBranding);
					break;

				default:
					OnlineAPI_view::returnResultAPI($resultArray, $browserLanguageCode, $pBasketWorkFlowType, $webBrandCode);
			}
		}
	}

	static function getAPIInputParameters()
	{
		global $gConstants;

		$resultArray = array();

		$resultArray['langcode'] = UtilsObj::cleanseLanguageCode(UtilsObj::getPOSTParam('browserlocale', ''), $gConstants['defaultlanguagecode']);
		$resultArray['basketref'] = UtilsObj::getHighLevelBasketAPIPOSTParams('mawebhlbr', TPX_ONLINE_BASKETAPI_DEFAULTBASKETREF);
		$resultArray['projectref'] = UtilsObj::getPostParam('projectref', '');

		return $resultArray;
	}

	static function validateAPIInputParameters($pParamArray)
	{
		global $gConstants;

		$resultArray = array();
		$errorCode = TPX_ONLINE_ERROR_NONE;
		$brandCode = '';

		// does the project ref look correct?
		$matchesCount = preg_match('/^[0-9]+[_]{1}[0-9]+$/', $pParamArray['projectref']);

		if ($matchesCount > 0)
		{
			// we have a valid project ref

			// validate the project against the basket
			$errorCode = OnlineAPI_model::validateBasketRefForProjectRef($pParamArray['basketref'], $pParamArray['projectref']);
		}
		else
		{
			// the project ref format appears to be invalid
			// state that the project does not exist
			$errorCode = TPX_ONLINE_ERROR_PROJECTDOESNOTEXIST;
		}


		// if an error has occurred grab the brand code from the host as the error handler will need it
		if ($errorCode != TPX_ONLINE_ERROR_NONE)
		{
			$brandCode = UtilsObj::getGlobalValue('gDefaultSiteBrandingCode', '');
		}

		$resultArray['result'] = $errorCode;
		$resultArray['langcode'] = $pParamArray['langcode'];
		$resultArray['brandcode'] = $brandCode;
		$resultArray['basketref'] = $pParamArray['basketref'];
		$resultArray['projectref'] = $pParamArray['projectref'];

		return $resultArray;
	}

	static function highLevelEditProject()
	{
		$canEdit = false;

		$editingType = UtilsObj::getGETParam('editingtype', 0);
		$projectRef = UtilsObj::getGETParam('projectref', '');
		$basketRef = UtilsObj::getGETParam('mawebhlbr', '');

		// validate the action before we execute it
		$validationResultArray = self::validateAPIInputParameters(self::getAPIInputParameters());

		// Check to see if we are trying to continue editing an ordered project.
		// if trying to continue editing an ordered project then no need to do basket validation.
		// This is because the project is no longer in the basket table.
		// We must also check to make sure the basketref is valid as the user might have logged in directly to
		// their customer account pages in control centre. This is not supported as the user is expected to use the My Account API feature.
		if (($editingType == 1) && ($basketRef != '') && ($basketRef != TPX_ONLINE_BASKETAPI_DEFAULTBASKETREF))
		{
			// we are trying to continue editing a project that is in production.
			$canEdit = true;
		}
		else if ($validationResultArray['result'] == TPX_ONLINE_ERROR_NONE)
		{
			// everything appears to be okay so proceed with the action
			$canEdit = true;
		}

		if ($canEdit)
		{
			self::editProject(TPX_BASKETWORKFLOWTYPE_HIGHLEVELAPI);
		}
		else
		{
			// something went wrong

			// report an error back to the user
			OnlineAPI_view::returnResultAPI($validationResultArray, $validationResultArray['langcode'], TPX_BASKETWORKFLOWTYPE_HIGHLEVELAPI,
				$validationResultArray['brandcode']);
		}
	}

	static function deleteProject($pBasketWorkFlowType = TPX_BASKETWORKFLOWTYPE_LOWLEVELAPI)
	{
		global $gConstants;

		$returnArray = array();
		$deleteMethodExists = false;

		$browserLanguageCode = UtilsObj::cleanseLanguageCode(UtilsObj::getGETParam('l', ''), $gConstants['defaultlanguagecode']);
		$basketListItemID = 0;
		$brandCode = '';

		if ($pBasketWorkFlowType == TPX_BASKETWORKFLOWTYPE_HIGHLEVELAPI)
		{
			$browserLanguageCode = UtilsObj::cleanseLanguageCode(UtilsObj::getPOSTParam('browserlocale', ''), $browserLanguageCode);
		}

		$deleteProjectParams = array();
		$deleteProjectParams['result'] = '';
 		$deleteProjectParams['languagecode'] = $browserLanguageCode;
		$deleteProjectParams['projectreflist'] = array();
		$deleteProjectParams['purgedays'] = 0;
		$deleteProjectParams['canunlock'] = 0;
		$deleteProjectParams['forcekill'] = 0;
		$deleteProjectParams['ssoprivatedata'] = array();

		self::includeOnlineBasketAPI($pBasketWorkFlowType);

		if ($pBasketWorkFlowType == TPX_BASKETWORKFLOWTYPE_LOWLEVELAPI)
		{
			$EDLBasketClass = 'OnlineBasketAPI';
			$deleteMethodExists = method_exists($EDLBasketClass, 'deleteProject');
		}
		else
		{
			$EDLBasketClass = 'OnlineBasketHighLevelAPI';
			$deleteMethodExists = true;
		}

		if ($deleteMethodExists)
		{
			if ($pBasketWorkFlowType == TPX_BASKETWORKFLOWTYPE_LOWLEVELAPI)
			{
				// populate the sso private data from the record in the database
				$ssoLLPrivateData = self::getSSOLLPrivateData();

				$deleteProjectParams['ssoprivatedata'] = $ssoLLPrivateData['ssoprivatedata'];

				$deleteProjectParams = OnlineBasketAPI::deleteProject($deleteProjectParams);

                if (isset($deleteProjectParams['authkey'])) {

                    // clean up any authentication data records
                    AuthenticateObj::deleteAuthenticationDataRecords();

                    $authenticationRecord = AuthenticateObj::getAuthenticationDataRecord(TPX_AUTHENTICATIONTYPE_LOWLEVEL, $deleteProjectParams['authkey'], true);
                    if (!$authenticationRecord['found']) {
                        $deleteProjectParams['result'] = TPX_ONLINE_ERROR_LOWLEVELINTERNALERROR;
                    }
                }

				$browserLanguageCode = UtilsObj::cleanseLanguageCode($deleteProjectParams['languagecode'], $browserLanguageCode);

				self::updateSSOLLPrivateData($deleteProjectParams['ssoprivatedata'], $ssoLLPrivateData['authkey']);

			}
			else
			{
				$onlineBasketRef = UtilsObj::cleanseInput(UtilsObj::getHighLevelBasketAPIPOSTParams('mawebhlbr', TPX_ONLINE_BASKETAPI_DEFAULTBASKETREF), true);

				$deleteProjectParams['forcekill'] = UtilsObj::getPostParam('forcekill', 0);
				$deleteProjectParams = OnlineAPI_model::buildHighLevelProjectParams($deleteProjectParams, 'deleteProject', '');
				$browserLanguageCode = UtilsObj::cleanseLanguageCode($deleteProjectParams['languagecode'], $browserLanguageCode);
				$deleteProjectParams['projectreflist'] = array(UtilsObj::getPostParam('projectref', ''));
				$basketListItemID = UtilsObj::getPostParam('itemtoremoveid', 0);

				$basketGroupCodeResult = DatabaseObj::getUserIDGroupCodeFromBasketRef($onlineBasketRef);
				$licenseKeyDataArray = DatabaseObj::getLicenseKeyFromCode($basketGroupCodeResult['groupcode']);
				$brandCode = $licenseKeyDataArray['webbrandcode'];
			}

			// if a value lower than 0 is returend i.e -1 we need to set the purgedays to 0 for immediate deletion
			if ($deleteProjectParams['purgedays'] < 0)
			{
				$deleteProjectParams['purgedays'] = 0;
			}

			if ($deleteProjectParams['result'] == '')
			{
				$smarty = SmartyObj::newSmarty('Customer', '', '', $browserLanguageCode);

				if (count($deleteProjectParams['projectreflist']) <= TPX_ONLINE_BASKETAPI_PROJECTREFLIMIT)
				{
					$paramArray = array();
					$paramArray['projectreflist'] = $deleteProjectParams['projectreflist'];
					$paramArray['forcekill'] = $deleteProjectParams['forcekill'];
					$paramArray['canunlock'] = $deleteProjectParams['canunlock'];
					$paramArray['purgedays'] = $deleteProjectParams['purgedays'];
					$paramArray['action'] = 'delete';
					$paramArray['basketref'] = '';

					$checkDeleteSessionResult = OnlineAPI_model::checkDeleteSession($paramArray);

					if ($checkDeleteSessionResult['error'] == '')
					{
						foreach ($checkDeleteSessionResult['projectitemarray'] as &$project)
						{
							$result = TPX_ONLINE_ERROR_NONE;
							$resultMessage = '';

							$projectRef = $project['projectref'];

							if ($project['canmodify'] == 1)
							{
								if ($project['sessionactive'] == true)
								{
									$result = TPX_ONLINE_ERROR_PROJECTALREADYOPEN;
									$resultMessage = $smarty->get_config_vars('str_WarningTerminateOtherSession');
								}
								else
								{
									if ($project['projectexists'] == false)
									{
										$result = TPX_ONLINE_ERROR_PROJECTDOESNOTEXIST;
										$resultMessage = $smarty->get_config_vars('str_ErrorProjectDoesNotExist');
									}
									else
									{
										if ($project['projectlocked'] == true)
										{
											$result = TPX_ONLINE_ERROR_PROJECTLOCKED;
											$resultMessage = $smarty->get_config_vars('str_ErrorProjectLocked');
										}

										if ($pBasketWorkFlowType == TPX_BASKETWORKFLOWTYPE_HIGHLEVELAPI)
										{
											OnlineAPI_model::deleteOnlineBasketData("'". $projectRef ."'");

											$returnArray['itemtoremoveid'] = $basketListItemID;
										}
									}
								}
							}
							else
							{
								$result = TPX_ONLINE_ERROR_PROJECTINPRODUCTION;
								$resultMessage = $smarty->get_config_vars('str_ErrorOrderInProduction');
							}

							if ($pBasketWorkFlowType == TPX_BASKETWORKFLOWTYPE_HIGHLEVELAPI)
							{
								$returnArray['result'] = $result;
								$returnArray['resultmessage'] = $resultMessage;
							}
							else
							{
								$returnArray[$projectRef]['result'] = $result;
								$returnArray[$projectRef]['resultmessage'] = $resultMessage;
							}
						}
					}
				}
				else
				{
					$returnArray['result'] = TPX_ONLINE_ERROR_PROJECTREFLIMIT;
					$returnArray['resultmessage'] = $smarty->get_config_vars('str_ErrorProjectRefLimitReached');
				}
			}
			else
			{
                $returnArray['result'] = $deleteProjectParams['result'];
			}
		}

		// clean up any authentication data records
		AuthenticateObj::deleteAuthenticationDataRecords();

		OnlineAPI_view::returnResultAPI($returnArray, $browserLanguageCode, $pBasketWorkFlowType, $brandCode);
	}

	static function highLevelDeleteProject()
	{
		// validate the action before we execute it
		$validationResultArray = self::validateAPIInputParameters(self::getAPIInputParameters());

		// is it safe to continue?
		if ($validationResultArray['result'] == TPX_ONLINE_ERROR_NONE)
		{
			// everything appears to be okay so proceed with the action
			self::deleteProject(TPX_BASKETWORKFLOWTYPE_HIGHLEVELAPI);
		}
		else
		{
			// something went wrong

			// report an error back to the user
			OnlineAPI_view::returnResultAPI($validationResultArray, $validationResultArray['langcode'], TPX_BASKETWORKFLOWTYPE_HIGHLEVELAPI,
				$validationResultArray['brandcode']);
		}
	}

	static function renameProject($pBasketWorkFlowType = TPX_BASKETWORKFLOWTYPE_LOWLEVELAPI)
	{
		global $gConstants;

		$returnArray = array();
		$result = TPX_ONLINE_ERROR_NONE;
		$resultMessage = '';
		$brandCode = '';
		$basketListItemID = 0;
		$newName = '';
		$browserLanguageCode = UtilsObj::cleanseLanguageCode(UtilsObj::getGETParam('l', ''), $gConstants['defaultlanguagecode']);

		$onlineBasketReNameProjectParams = array();
 		$onlineBasketReNameProjectParams['languagecode'] = $browserLanguageCode;
		$onlineBasketReNameProjectParams['projectref'] = '';
		$onlineBasketReNameProjectParams['newname'] = '';
		$onlineBasketReNameProjectParams['canunlock'] = 0;
		$onlineBasketReNameProjectParams['ssoprivatedata'] = array();
		$onlineBasketReNameProjectParams['ccnotificationsenabled'] = false;
		$ccNotificationsEnabled = false;

		if ($pBasketWorkFlowType == TPX_BASKETWORKFLOWTYPE_LOWLEVELAPI)
		{
			self::includeOnlineBasketAPI(TPX_BASKETWORKFLOWTYPE_LOWLEVELAPI);

			// populate the sso private data from the record in the database
			$ssoLLPrivateData = self::getSSOLLPrivateData();

			$onlineBasketReNameProjectParams['ssoprivatedata'] = $ssoLLPrivateData['ssoprivatedata'];

			if (method_exists('OnlineBasketAPI', 'renameProject'))
			{
				$onlineBasketReNameProjectParams = OnlineBasketAPI::renameProject($onlineBasketReNameProjectParams);
				$ccNotificationsEnabled = $onlineBasketReNameProjectParams['ccnotificationsenabled'];

                if (isset($onlineBasketReNameProjectParams['authkey'])){

                    // clean up any authentication data records
                    AuthenticateObj::deleteAuthenticationDataRecords();

                    $authenticationRecord = AuthenticateObj::getAuthenticationDataRecord(TPX_AUTHENTICATIONTYPE_LOWLEVEL, $onlineBasketReNameProjectParams['authkey'], true);
                    if (!$authenticationRecord['found'])
                    {
                        $result = TPX_ONLINE_ERROR_LOWLEVELINTERNALERROR;
                    }
                }

				$browserLanguageCode = UtilsObj::cleanseLanguageCode($onlineBasketReNameProjectParams['languagecode'], $browserLanguageCode);

				self::updateSSOLLPrivateData($onlineBasketReNameProjectParams['ssoprivatedata'], $ssoLLPrivateData['authkey']);
			}
		}
		else
		{
			$ccNotificationsEnabled = true;
			$onlineBasketRef = UtilsObj::cleanseInput(UtilsObj::getHighLevelBasketAPIPOSTParams('mawebhlbr', TPX_ONLINE_BASKETAPI_DEFAULTBASKETREF), true);

			$onlineBasketReNameProjectParams['projectref'] = UtilsObj::getPostParam('projectref', '');
			$onlineBasketReNameProjectParams['newname'] = UtilsObj::getPostParam('newname', '');
			$basketListItemID = UtilsObj::getPostParam('basketitemidtoupdate', 0);
			$browserLanguageCode = UtilsObj::cleanseLanguageCode(UtilsObj::getPOSTParam('browserlocale', ''), $browserLanguageCode);

			$basketGroupCodeResult = DatabaseObj::getUserIDGroupCodeFromBasketRef($onlineBasketRef);
			$licenseKeyDataArray = DatabaseObj::getLicenseKeyFromCode($basketGroupCodeResult['groupcode']);
			$brandCode = $licenseKeyDataArray['webbrandcode'];
		}

        if ($result == TPX_ONLINE_ERROR_NONE) {
            // make sure a valid project ref has been provided
            $matchesCount = preg_match('/^[0-9]+[_]{1}[0-9]+$/', $onlineBasketReNameProjectParams['projectref']);

            if ($matchesCount > 0) {
                if ($onlineBasketReNameProjectParams['newname'] != '') {
                    $renameParamArray = array();
                    $renameParamArray['browserlanguagecode'] = $browserLanguageCode;
                    $renameParamArray['projectref'] = $onlineBasketReNameProjectParams['projectref'];
                    $renameParamArray['projectname'] = UtilsObj::cleanseInput($onlineBasketReNameProjectParams['newname']);
                    $renameParamArray['canunlock'] = $onlineBasketReNameProjectParams['canunlock'];
                    $renameParamArray['ccnotificationsenabled'] = $ccNotificationsEnabled;
                    $renameParamArray['basketapiworkflowtype'] = $pBasketWorkFlowType;
                    $renameParamArray['cmd'] = 'RENAMEPROJECT';

                    $resultArray = OnlineAPI_model::duplicateRenameOnlineProject($renameParamArray);

                    $renameResult = $resultArray['error'];

                    if ($renameResult == '') {
                        $projectDetails = $resultArray['projectdetails'];

                        if ($projectDetails['restoremessage'] != TPX_ARCHIVE_RESTORE_FAILED) {
                            if ($projectDetails['projectexists']) {
                                if (!$projectDetails['projectlocked']) {
                                    if ($projectDetails['nameexists'] != '') {
                                        $result = TPX_ONLINE_ERROR_PROJECTNAMEALREADYEXISTS;
                                    } else {
                                        if ($pBasketWorkFlowType == TPX_BASKETWORKFLOWTYPE_HIGHLEVELAPI) {
                                            $projectRef = $onlineBasketReNameProjectParams['projectref'];
                                            $newName = UtilsObj::escapeInputForHTML($onlineBasketReNameProjectParams['newname']);
                                        }
                                    }
                                } else {
                                    $result = TPX_ONLINE_ERROR_PROJECTLOCKED;
                                }
                            } else {
                                $result = TPX_ONLINE_ERROR_PROJECTDOESNOTEXIST;
                            }
                        } else {
                            $result = TPX_ONLINE_ERROR_RESTORE_FAILED;
                        }
                    }
                } else {
                    $result = TPX_ONLINE_ERROR_PROJECTNAMECANNOTBEEMPTY;
                }
            } else {
                $result = TPX_ONLINE_ERROR_PROJECTDOESNOTEXIST;
            }
        }

		$returnArray['result'] = $result;
		$returnArray['resultmessage'] = $resultMessage;

		if ($pBasketWorkFlowType == TPX_BASKETWORKFLOWTYPE_HIGHLEVELAPI)
		{
			$returnArray['newprojectname'] = $newName;
			$returnArray['basketitemidtoupdate'] = $basketListItemID;
		}

		// clean up any authentication data records
		AuthenticateObj::deleteAuthenticationDataRecords();

		OnlineAPI_view::returnResultAPI($returnArray, $browserLanguageCode, $pBasketWorkFlowType, $brandCode);
	}

	static function highLevelRenameProject()
	{
		// validate the action before we execute it
		$validationResultArray = self::validateAPIInputParameters(self::getAPIInputParameters());

		// is it safe to continue?
		if ($validationResultArray['result'] == TPX_ONLINE_ERROR_NONE)
		{
			// everything appears to be okay so proceed with the action
			self::renameProject(TPX_BASKETWORKFLOWTYPE_HIGHLEVELAPI);
		}
		else
		{
			// something went wrong

			// report an error back to the user
			OnlineAPI_view::returnResultAPI($validationResultArray, $validationResultArray['langcode'], TPX_BASKETWORKFLOWTYPE_HIGHLEVELAPI,
				$validationResultArray['brandcode']);
		}
	}

	static function duplicateProject($pBasketWorkFlowType = TPX_BASKETWORKFLOWTYPE_LOWLEVELAPI)
    {
        global $gConstants;
        global $gSession;

        $result = TPX_ONLINE_ERROR_NONE;
        $resultMessage = '';
        $brandCode = '';
        $duplicateMethodExists = false;
        $browserLanguageCode = UtilsObj::cleanseLanguageCode(UtilsObj::getGETParam('l', ''), $gConstants['defaultlanguagecode']);

        if ($pBasketWorkFlowType == TPX_BASKETWORKFLOWTYPE_HIGHLEVELAPI) {
            $browserLanguageCode = UtilsObj::cleanseLanguageCode(UtilsObj::getPOSTParam('browserlocale', ''), $browserLanguageCode);
        }

        $newProjectRef = '';
        $onlineBasketDuplicateProjectParams = array();
        $onlineBasketDuplicateProjectParams['languagecode'] = $browserLanguageCode;
        $onlineBasketDuplicateProjectParams['projectref'] = '';
        $onlineBasketDuplicateProjectParams['projectname'] = '';
        $onlineBasketDuplicateProjectParams['minlife'] = -1;
        $onlineBasketDuplicateProjectParams['ssoprivatedata'] = array();
        $onlineBasketDuplicateProjectParams['html'] = false;

        self::includeOnlineBasketAPI($pBasketWorkFlowType);

        if ($pBasketWorkFlowType == TPX_BASKETWORKFLOWTYPE_LOWLEVELAPI) {
            $EDLBasketClass = 'OnlineBasketAPI';
            $duplicateMethodExists = method_exists($EDLBasketClass, 'duplicateProject');
        } else {
            $EDLBasketClass = 'OnlineBasketHighLevelAPI';
            $duplicateMethodExists = true;
        }

        if ($duplicateMethodExists) {
            if ($pBasketWorkFlowType == TPX_BASKETWORKFLOWTYPE_LOWLEVELAPI) {

                // populate the sso private data from the record in the database
                $ssoLLPrivateData = self::getSSOLLPrivateData();

                $onlineBasketDuplicateProjectParams['ssoprivatedata'] = $ssoLLPrivateData['ssoprivatedata'];

                $onlineBasketDuplicateProjectParams = OnlineBasketAPI::duplicateProject($onlineBasketDuplicateProjectParams);

                if (isset($onlineBasketDuplicateProjectParams['authkey'])) {

                    // clean up any authentication data records
                    AuthenticateObj::deleteAuthenticationDataRecords();

                    $authenticationRecord = AuthenticateObj::getAuthenticationDataRecord(TPX_AUTHENTICATIONTYPE_LOWLEVEL, $onlineBasketDuplicateProjectParams['authkey'], true);
                    if (!$authenticationRecord['found']) {
                        $result = TPX_ONLINE_ERROR_LOWLEVELINTERNALERROR;
                    }
                }

                $browserLanguageCode = UtilsObj::cleanseLanguageCode($onlineBasketDuplicateProjectParams['languagecode'], $browserLanguageCode);

                self::updateSSOLLPrivateData($onlineBasketDuplicateProjectParams['ssoprivatedata'], $ssoLLPrivateData['authkey']);
            } else {
                $onlineBasketRef = UtilsObj::cleanseInput(UtilsObj::getHighLevelBasketAPIPOSTParams('mawebhlbr', TPX_ONLINE_BASKETAPI_DEFAULTBASKETREF), true);

                $basketGroupCodeResult = DatabaseObj::getUserIDGroupCodeFromBasketRef($onlineBasketRef);
                $groupCode = $basketGroupCodeResult['groupcode'];
                $licenseKeyDataArray = DatabaseObj::getLicenseKeyFromCode($groupCode);
                $brandCode = $licenseKeyDataArray['webbrandcode'];
                $userID = $basketGroupCodeResult['basketuserid'];

                $onlineBasketDuplicateProjectParams = OnlineAPI_model::buildHighLevelProjectParams($onlineBasketDuplicateProjectParams, 'duplicateProject', '');
                $onlineBasketDuplicateProjectParams['projectref'] = UtilsObj::getPostParam('projectref', '');
                $onlineBasketDuplicateProjectParams['projectname'] = UtilsObj::getPostParam('projectname', '');
                $productIdent = UtilsObj::getPostParam('productident', '');

                // if the the product ident is not empty then we know a user is trying
                // to duplicate a project via the my orders list under a brand that is high level
                if ($productIdent != '') {
                    $systemConfigArray = DatabaseObj::getSystemConfig();
                    $productIdentData = explode(chr(10), UtilsObj::decryptData($productIdent, $systemConfigArray['systemkey'], true), 2);

                    $collectionLayoutArray = explode(chr(9), $productIdentData[0]);

                    $collectionCode = $collectionLayoutArray[0];
                    $layoutCode = $collectionLayoutArray[1];

                    $groupCode = $gSession['licensekeydata']['groupcode'];
                    $licenseKeyDataArray = DatabaseObj::getLicenseKeyFromCode($groupCode);
                    $brandCode = $licenseKeyDataArray['webbrandcode'];
                    $userID = $gSession['userid'];
                }

                $browserUTC = UtilsObj::getPostParam('browserutc', 0);
                $browserLanguageCode = UtilsObj::cleanseLanguageCode($onlineBasketDuplicateProjectParams['languagecode'], $browserLanguageCode);
            }
        }

        if ($result == TPX_ONLINE_ERROR_NONE) {

            $matchesCount = preg_match('/^[0-9]+[_]{1}[0-9]+$/', $onlineBasketDuplicateProjectParams['projectref']);

            if ($matchesCount > 0) {
                $projectRef = $onlineBasketDuplicateProjectParams['projectref'];
                $projectName = UtilsObj::cleanseInput($onlineBasketDuplicateProjectParams['projectname']);

                if ($projectName != '') {
                    $duplicateParamArray = array();
                    $duplicateParamArray['browserlanguagecode'] = $browserLanguageCode;
                    $duplicateParamArray['projectref'] = $projectRef;
                    $duplicateParamArray['projectname'] = $projectName;
                    $duplicateParamArray['minlife'] = $onlineBasketDuplicateProjectParams['minlife'];
                    $duplicateParamArray['canunlock'] = 1;
                    $duplicateParamArray['basketapiworkflowtype'] = $pBasketWorkFlowType;
                    $duplicateParamArray['ccnotificationsenabled'] = false;
                    $duplicateParamArray['cmd'] = 'DUPLICATEPROJECT';

                    $resultArray = OnlineAPI_model::duplicateRenameOnlineProject($duplicateParamArray);

                    $duplicateResult = $resultArray['error'];

                    if ($duplicateResult == '') {
                        $newProjectRef = '';
                        $projectDetails = $resultArray['projectdetails'];

                        if ($projectDetails['restoremessage'] != TPX_ARCHIVE_RESTORE_FAILED) {
                            if ($projectDetails['projectexists']) {
                                if ($projectDetails['nameexists'] != '') {
                                    $result = TPX_ONLINE_ERROR_PROJECTNAMEALREADYEXISTS;
                                } else {
                                    $newProjectRef = $projectDetails['projectref'];

                                    if ($pBasketWorkFlowType == TPX_BASKETWORKFLOWTYPE_HIGHLEVELAPI) {
                                        if ($productIdent == '') {
                                            $newProjectBasketDataArray = OnlineAPI_model::getProjectFromBasketToDuplicate($projectRef, $newProjectRef, $projectName);
                                        } else {
                                            $productArray = DatabaseObj::getProductFromCollectionCodeAndLayoutCode($collectionCode, $layoutCode);

                                            $basketExpireDate = OnlineAPI_model::generateBasketCookieExpiryDate(($browserUTC / 1000), $brandCode);

                                            $newProjectBasketDataArray['result'] = '';
                                            $newProjectBasketDataArray['webbrandcode'] = $brandCode;
                                            $newProjectBasketDataArray['groupcode'] = $groupCode;
                                            $newProjectBasketDataArray['basketref'] = $onlineBasketRef;
                                            $newProjectBasketDataArray['basketexpiredate'] = date('Y-m-d H:i:s', $basketExpireDate);
                                            $newProjectBasketDataArray['projectref'] = $newProjectRef;
                                            $newProjectBasketDataArray['userid'] = $userID;
                                            $newProjectBasketDataArray['projectname'] = $projectName;
                                            $newProjectBasketDataArray['collectioncode'] = $collectionCode;
                                            $newProjectBasketDataArray['collectionname'] = $productArray['collectionname'];
                                            $newProjectBasketDataArray['layoutcode'] = $layoutCode;
                                            $newProjectBasketDataArray['layoutname'] = $productArray['name'];
                                            $newProjectBasketDataArray['saved'] = 1;
                                            $newProjectBasketDataArray['projectdata'] = '';
                                        }

                                        if ($newProjectBasketDataArray['result'] == '') {
                                            $addProjcetToOnlineBasket = OnlineAPI_model::addDuplicateProjectToBasket($newProjectBasketDataArray);

                                            if ($addProjcetToOnlineBasket['result'] != '') {
                                                $result = $addProjcetToOnlineBasket['resultparam'];
                                            }
                                        } else {
                                            $result = $newProjectBasketDataArray['resultparam'];
                                        }
                                    }
                                }
                            } else {
                                $result = TPX_ONLINE_ERROR_PROJECTDOESNOTEXIST;
                            }
                        } else {
                            $result = TPX_ONLINE_ERROR_RESTORE_FAILED;
                        }
                    }
                } else {
                    $result = TPX_ONLINE_ERROR_PROJECTNAMECANNOTBEEMPTY;
                }
            } else {
                $result = TPX_ONLINE_ERROR_PROJECTDOESNOTEXIST;
            }
        }

		// clean up any authentication data records
		AuthenticateObj::deleteAuthenticationDataRecords();

		$onlinedesignerurl = "";
		if ($onlineBasketDuplicateProjectParams['html'])
		{
			$brandingArray = DatabaseObj::getBrandingFromCode($brandCode);
			$onlinedesignerurl = UtilsObj::correctPath($brandingArray['onlinedesignerurl'], "/", false);
		}

		$apiReturnArray = array(
			'result' => $result,
			'resultmessage' => $resultMessage,
			'projectref' => $newProjectRef,
			'projectname' => $projectName,
			'productname' => $projectDetails['productlayoutname'],
			'datecreated' => $projectDetails['datecreated'],
			'thumbnailpath' => $projectDetails['thumbnailpath'],
			'projectpreviewthumbnail' => $projectDetails['projectpreviewthumbnail'],
			'canedit' => true,
			'candelete' => true,
			'onlinedesignerurl' => $onlinedesignerurl,
			'html' => ($onlineBasketDuplicateProjectParams['html'] ? 'projectrow':'')
		);

		OnlineAPI_view::returnResultAPI($apiReturnArray, $browserLanguageCode, $pBasketWorkFlowType, $brandCode);
	}

	static function highLevelDuplicateProject($pFromProductSelectorPage = true)
	{
		$validationResultArray = array();
		$validationResultArray['result'] = TPX_ONLINE_ERROR_NONE;

		if ($pFromProductSelectorPage)
		{
			// validate the action before we execute it
			$validationResultArray = self::validateAPIInputParameters(self::getAPIInputParameters());
		}

		// is it safe to continue?
		if ($validationResultArray['result'] == TPX_ONLINE_ERROR_NONE)
		{
			// everything appears to be okay so proceed with the action
			self::duplicateProject(TPX_BASKETWORKFLOWTYPE_HIGHLEVELAPI);
		}
		else
		{
			// something went wrong

			// report an error back to the user
			OnlineAPI_view::returnResultAPI($validationResultArray, $validationResultArray['langcode'], TPX_BASKETWORKFLOWTYPE_HIGHLEVELAPI,
				$validationResultArray['brandcode']);
		}
	}

	static function touchProject()
	{
		global $gConstants;

		$returnArray = array();

		$browserLanguageCode = UtilsObj::cleanseLanguageCode(UtilsObj::getGETParam('l', ''), $gConstants['defaultlanguagecode']);

		$touchProjectParams = array();
		$touchProjectParams['result'] = '';
 		$touchProjectParams['languagecode'] = $browserLanguageCode;
		$touchProjectParams['projectreflist'] = array();
		$touchProjectParams['newminlife'] = -1;

		self::includeOnlineBasketAPI(TPX_BASKETWORKFLOWTYPE_LOWLEVELAPI);

		if (method_exists('OnlineBasketAPI', 'touchProject'))
		{
			$touchProjectParams = OnlineBasketAPI::touchProject($touchProjectParams);

			$browserLanguageCode = UtilsObj::cleanseLanguageCode($touchProjectParams['languagecode'], $browserLanguageCode);

			if ($touchProjectParams['result'] == '')
			{
				$smarty = SmartyObj::newSmarty('Customer', '', '', $browserLanguageCode);

				if (count($touchProjectParams['projectreflist']) <= TPX_ONLINE_BASKETAPI_PROJECTREFLIMIT)
				{
					$touchProjectResult = OnlineAPI_model::touchProject($touchProjectParams);

					foreach ($touchProjectResult['projectreflist'] as &$project)
					{
						$projectRef = $project['projectref'];
						$result = $project['result'];

						$resultMessage = '';

						if ($result == TPX_ONLINE_ERROR_PROJECTDOESNOTEXIST)
						{
							$resultMessage = $smarty->get_config_vars('str_ErrorProjectDoesNotExist');
						}

						$returnArray[$projectRef]['result'] = $result;
						$returnArray[$projectRef]['resultmessage'] = $resultMessage;
					}
				}
				else
				{
					$returnArray['result'] = TPX_ONLINE_ERROR_PROJECTREFLIMIT;
				}
			}
			else
			{
				$returnArray['result'] = $touchProjectParams['result'];
			}
		}

		OnlineAPI_view::returnResultAPI($returnArray, $browserLanguageCode, TPX_BASKETWORKFLOWTYPE_LOWLEVELAPI, '');
	}

	static function externalCheckout()
	{
		global $gConstants;

		$browserLanguageCode = UtilsObj::cleanseLanguageCode(UtilsObj::getGETParam('l', ''), $gConstants['defaultlanguagecode']);

		$externalCheckoutParams = array();
		$externalCheckoutParams['result'] = '';
		$externalCheckoutParams['languagecode'] = $browserLanguageCode;
		$externalCheckoutParams['projectreflist'] = array();
		$externalCheckoutParams['cartdata'] = array();
		$externalCheckoutParams['lockperiod'] = 0;

		self::includeOnlineBasketAPI(TPX_BASKETWORKFLOWTYPE_LOWLEVELAPI);

		if (method_exists('OnlineBasketAPI', 'externalCheckout'))
		{
			$externalCheckoutParams = OnlineBasketAPI::externalCheckout($externalCheckoutParams);

			$browserLanguageCode = UtilsObj::cleanseLanguageCode($externalCheckoutParams['languagecode'], $browserLanguageCode);

			if ($externalCheckoutParams['result'] == '')
			{
				if (count($externalCheckoutParams['projectreflist']) <= TPX_ONLINE_BASKETAPI_PROJECTREFLIMIT)
				{
					$returnArray = OnlineAPI_model::externalCheckout($externalCheckoutParams, TPX_BASKETWORKFLOWTYPE_LOWLEVELAPIEXTERNALCHECKOUT, '');
				}
				else
				{
					$returnArray['result'] = TPX_ONLINE_ERROR_PROJECTREFLIMIT;
				}
			}
		}

		OnlineAPI_view::returnResultAPI($returnArray, $browserLanguageCode, TPX_BASKETWORKFLOWTYPE_LOWLEVELAPI, '');
	}

	static function clearProjectBatchRef()
	{
		global $gConstants;

		$browserLanguageCode = UtilsObj::cleanseLanguageCode(UtilsObj::getGETParam('l', ''), $gConstants['defaultlanguagecode']);

		$clearProjectBatchRefParams = array();
		$clearProjectBatchRefParams['result'] = '';
		$clearProjectBatchRefParams['languagecode'] = $browserLanguageCode;
		$clearProjectBatchRefParams['projectreflist'] = array();

		self::includeOnlineBasketAPI(TPX_BASKETWORKFLOWTYPE_LOWLEVELAPI);

		if (method_exists('OnlineBasketAPI', 'clearProjectBatchRef'))
		{
			$clearProjectBatchRefParams = OnlineBasketAPI::clearProjectBatchRef($clearProjectBatchRefParams);

			$browserLanguageCode = UtilsObj::cleanseLanguageCode($clearProjectBatchRefParams['languagecode'], $browserLanguageCode);

			if ($clearProjectBatchRefParams['result'] == '')
			{
				$smarty = SmartyObj::newSmarty('Customer', '', '', $browserLanguageCode);
				$clearProjectBatchRefParamCount = count($clearProjectBatchRefParams['projectreflist']);

				if ($clearProjectBatchRefParamCount == 0)
				{
					// if we do not have any project refs then the api has been called incorrecty and we need to return an error
					$returnArray['result'] = TPX_ONLINE_ERROR_INVALIDPARAMETER;
				}
				else if ($clearProjectBatchRefParamCount <= TPX_ONLINE_BASKETAPI_PROJECTREFLIMIT)
				{
					$clearProjectBatchRefResultArray = OnlineAPI_model::clearProjectBatchRef($clearProjectBatchRefParams);

					foreach ($clearProjectBatchRefResultArray['projectreflist'] as &$project)
					{
						$projectRef = $project['projectref'];
						$result = $project['result'];

						$resultMessage = '';

						if ($result == TPX_ONLINE_ERROR_PROJECTDOESNOTEXIST)
						{
							$resultMessage = $smarty->get_config_vars('str_ErrorProjectDoesNotExist');
						}

						$returnArray[$projectRef]['result'] = $result;
						$returnArray[$projectRef]['resultmessage'] = $resultMessage;
					}
				}
				else
				{
					$returnArray['result'] = TPX_ONLINE_ERROR_PROJECTREFLIMIT;
				}
			}
		}

		OnlineAPI_view::returnResultAPI($returnArray, $browserLanguageCode, TPX_BASKETWORKFLOWTYPE_LOWLEVELAPI, '');
	}

	static function deleteUnflagProject()
	{
		global $gConstants;

		$returnArray = array();

		$browserLanguageCode = UtilsObj::cleanseLanguageCode(UtilsObj::getGETParam('l', ''), $gConstants['defaultlanguagecode']);

		$deleteUnflagProjectParams = array();
		$deletUnflagProjectParams ['result'] = '';
 		$deleteUnflagProjectParams['languagecode'] = $browserLanguageCode;
		$deleteUnflagProjectParams['projectreflist'] = array();

		self::includeOnlineBasketAPI(TPX_BASKETWORKFLOWTYPE_LOWLEVELAPI);

		if (method_exists('OnlineBasketAPI', 'deleteUnflagProject'))
		{
			$deletUnflagProjectParams = OnlineBasketAPI::deleteUnflagProject($deleteUnflagProjectParams);

			$browserLanguageCode = UtilsObj::cleanseLanguageCode($deletUnflagProjectParams['languagecode'], $browserLanguageCode);

			$smarty = SmartyObj::newSmarty('Customer', '', '', $browserLanguageCode);

			if (count($deletUnflagProjectParams['projectreflist']) <= TPX_ONLINE_BASKETAPI_PROJECTREFLIMIT)
			{
				if ($deletUnflagProjectParams['result'] == '')
				{
					$deleteUnflagResult = OnlineAPI_model::deleteUnflagProject($deletUnflagProjectParams['projectreflist']);

					foreach ($deleteUnflagResult['projectreflist'] as &$project)
					{
						$projectRef = $project['projectref'];
						$result = $project['result'];

						$resultMessage = '';

						if ($result == TPX_ONLINE_ERROR_PROJECTDOESNOTEXIST)
						{
							$resultMessage = $smarty->get_config_vars('str_ErrorProjectDoesNotExist');
						}

						$returnArray[$projectRef]['result'] = $result;
						$returnArray[$projectRef]['resultmessage'] = $resultMessage;
					}
				}
				else
				{
					$returnArray['result'] = $deletUnflagProjectParams['result'];
				}
			}
			else
			{
				$returnArray['result'] = TPX_ONLINE_ERROR_PROJECTREFLIMIT;
			}
		}

		OnlineAPI_view::returnResultAPI($returnArray, $browserLanguageCode, TPX_BASKETWORKFLOWTYPE_LOWLEVELAPI, '');

	}

	static function lockProject()
	{
		global $gConstants;

		$browserLanguageCode = UtilsObj::cleanseLanguageCode(UtilsObj::getGETParam('l', ''), $gConstants['defaultlanguagecode']);

		$lockProjectParams = array();
		$lockProjectParams['result'] = '';
 		$lockProjectParams['languagecode'] = $browserLanguageCode;
		$lockProjectParams['projectreflist'] = array();
		$lockProjectParams['lockperiod'] = 0;

		self::includeOnlineBasketAPI(TPX_BASKETWORKFLOWTYPE_LOWLEVELAPI);

		if (method_exists('OnlineBasketAPI', 'lockProject'))
		{
			$onlineBasketLockProjectParams = OnlineBasketAPI::lockProject($lockProjectParams);

			$browserLanguageCode = UtilsObj::cleanseLanguageCode($onlineBasketLockProjectParams['languagecode'], $browserLanguageCode);

			$smarty = SmartyObj::newSmarty('Customer', '', '', $browserLanguageCode);

			if (count($onlineBasketLockProjectParams['projectreflist']) <= TPX_ONLINE_BASKETAPI_PROJECTREFLIMIT)
			{
				if ($onlineBasketLockProjectParams['result'] == '')
				{
					$lockResult = OnlineAPI_model::lockProject($onlineBasketLockProjectParams);

					foreach ($lockResult['projectreflist'] as &$project)
					{
						$projectRef = $project['projectref'];
						$result = $project['result'];

						$resultMessage = '';

						if ($result == TPX_ONLINE_ERROR_PROJECTDOESNOTEXIST)
						{
							$resultMessage = $smarty->get_config_vars('str_ErrorProjectDoesNotExist');
						}

						$returnArray[$projectRef]['result'] = $result;
						$returnArray[$projectRef]['resultmessage'] = $resultMessage;
					}
				}
				else
				{
					$returnArray['result'] = $onlineBasketLockProjectParams['result'];
				}
			}
			else
			{
				$returnArray['result'] = TPX_ONLINE_ERROR_PROJECTREFLIMIT;
			}
		}

		OnlineAPI_view::returnResultAPI($returnArray, $browserLanguageCode, TPX_BASKETWORKFLOWTYPE_LOWLEVELAPI, '');
	}

	static function highLevelBasketInitialise()
	{
		$basketDataArray = array('result' => 0, 'resultmessage' => '', 'items' => array(), 'basketcount' => 0);
		$brandCode = '';

		$inputParamArray = self::getAPIInputParameters();

		$basketRef = $inputParamArray['basketref'];
		$browserLanguageCode = $inputParamArray['langcode'];
		$brandCode = UtilsObj::getGlobalValue('gDefaultSiteBrandingCode', '');

		if (($basketRef != '') && ($basketRef != TPX_ONLINE_BASKETAPI_DEFAULTBASKETREF))
		{
			$onlineBasketAuthenticated = AuthenticateObj::checkIfOnlineBasketRequiresAuthenticatedUser($basketRef);

			if ($onlineBasketAuthenticated['result'] != '')
			{
				$basketDataArray['result'] = TPX_ONLINE_ERROR_HIGHLEVELINTERNALERROR;
			}
			else if ($onlineBasketAuthenticated['authenticatedstatus'] == TPX_ONLINE_BASKETAPI_AUTHENTICATEDUSERSTATUS_SIGNEDINUSER)
			{
				$basketDataArray = OnlineAPI_model::retrieveBasketContents($basketRef, 1, true);
				$basketDataArray['result'] = 0;
				$basketDataArray['resultmessage'] = '';
			}
			else if ($onlineBasketAuthenticated['authenticatedstatus'] == TPX_ONLINE_BASKETAPI_AUTHENTICATEDUSERSTATUS_SIGNEDINUSEREXPIRED)
			{
				$basketDataArray['result'] = TPX_ONLINE_ERROR_HIGHLEVELBASKETEXPIRED;
			}
		}

		OnlineAPI_view::returnResultAPI($basketDataArray, $browserLanguageCode, TPX_BASKETWORKFLOWTYPE_HIGHLEVELAPI, $brandCode);
	}

	static function highLevelViewProjectsList()
	{
		$basketDataArray = array('result' => 0, 'resultmessage' => '', 'items' => array(), 'basketcount' => 0);
		$brandCode = '';

		$inputParamArray = self::getAPIInputParameters();

		$basketRef = $inputParamArray['basketref'];
		$browserLanguageCode = $inputParamArray['langcode'];
		$brandCode = UtilsObj::getGlobalValue('gDefaultSiteBrandingCode', '');

		$onlineBasketAuthenticated = AuthenticateObj::checkIfOnlineBasketRequiresAuthenticatedUser($basketRef);

		if ($onlineBasketAuthenticated['result'] != '')
		{
			$basketDataArray['result'] = TPX_ONLINE_ERROR_HIGHLEVELINTERNALERROR;
		}
		else if (($onlineBasketAuthenticated['authenticatedstatus'] == TPX_ONLINE_BASKETAPI_AUTHENTICATEDUSERSTATUS_SIGNEDINUSER) || ($onlineBasketAuthenticated['authenticatedstatus'] == TPX_ONLINE_BASKETAPI_AUTHENTICATEDUSERSTATUS_GUEST))
		{
			$basketDataArray = OnlineAPI_model::retrieveBasketContents($basketRef, 0, true, $browserLanguageCode);
			$basketDataArray['result'] = 0;
			$basketDataArray['resultmessage'] = '';
		}
		else
		{
			// treat the user as a guest user and generate a new basketref.
			$createBasketRecordResult = OnlineAPI_model::createBasketRecord($brandCode, '', '');
			$basketRecordID = $createBasketRecordResult['basketrecordid'];
			$basketExpireDate = $createBasketRecordResult['basketexpiredate'];

			$basketRef = OnlineAPI_model::generateBasketRef($basketRecordID);

			// we need to delete the basket record we created as we only created the record in order to create a unique basketref.
			DatabaseObj::deleteOnlineBasketRecordByRecordID($basketRecordID);

			$basketDataArray['basketref'] = $basketRef;
			$basketDataArray['cookieexpirytime'] = $basketExpireDate;
			$basketDataArray['result'] = TPX_ONLINE_ERROR_HIGHLEVELBASKETEXPIRED;
		}

		OnlineAPI_view::returnResultAPI($basketDataArray, $browserLanguageCode, TPX_BASKETWORKFLOWTYPE_HIGHLEVELAPI, $brandCode);
	}

	static function highLevelRemoveItemFromBasket()
	{
		global $gConstants;

		$returnArray = array('result' => 0, 'resultmessage' => '');

		$forcekill = UtilsObj::cleanseInput(UtilsObj::getHighLevelBasketAPIPOSTParams('forcekill', 0));
		$itemToRemoveID = UtilsObj::cleanseInput(UtilsObj::getPOSTParam('itemtoremoveid', ''), true);

		$inputParamArray = self::getAPIInputParameters();
		$brandCode = UtilsObj::getGlobalValue('gDefaultSiteBrandingCode', '');
		$browserLanguageCode = $inputParamArray['langcode'];
		$onlineBasketRef = $inputParamArray['basketref'];
		$projectRef = $inputParamArray['projectref'];

		if (($onlineBasketRef != '') && ($onlineBasketRef != TPX_ONLINE_BASKETAPI_DEFAULTBASKETREF))
		{
			$onlineBasketAuthenticated = AuthenticateObj::checkIfOnlineBasketRequiresAuthenticatedUser($onlineBasketRef);

			if ($onlineBasketAuthenticated['result'] != '')
			{
				$returnArray['result'] = TPX_ONLINE_ERROR_HIGHLEVELINTERNALERROR;
			}
			else if ($onlineBasketAuthenticated['authenticatedstatus'] == TPX_ONLINE_BASKETAPI_AUTHENTICATEDUSERSTATUS_SIGNEDINUSER)
			{
				$projectRefList = "'" . $projectRef . "'";

				$paramArray = array();
				$paramArray['projectreflist'] = array(0 => $projectRef);
				$paramArray['forcekill'] = $forcekill;
				$paramArray['canunlock'] = 1;
				$paramArray['purgedays'] = 0;
				$paramArray['action'] = 'removefrombasket';
				$paramArray['basketref'] = $onlineBasketRef;

				$checkDeleteSessionArray = OnlineAPI_model::checkDeleteSession($paramArray);

				if ($checkDeleteSessionArray['error'] == '')
				{
					if ($checkDeleteSessionArray['projectitemarray'][$projectRef]['projectexists'] == 1)
					{
						if (($checkDeleteSessionArray['projectitemarray'][$projectRef]['shoppingcartsessionref'] == 0) || ($forcekill == 1))
						{
							$removeItemArray = OnlineAPI_model::removeItemsFromBasket($projectRefList);

							if ($removeItemArray['result'] == '')
							{
								$projectRefArray = array('projectreflist' => array($projectRef));

								$clearProjectBatchRefResult = OnlineAPI_model::clearProjectBatchRef($projectRefArray);

								if ($clearProjectBatchRefResult['projectreflist'][$projectRef]['result'] == TPX_ONLINE_ERROR_NONE)
								{
									$returnArray['result'] = TPX_ONLINE_ERROR_NONE;
									$returnArray['resultmessage'] = '';
									$returnArray['itemtoremoveid'] = $itemToRemoveID;
								}
							}
							else
							{
								$returnArray['result'] = $removeItemArray['result'];
								$returnArray['resultmessage'] = $removeItemArray['resultparam'];
							}
						}
						else
						{
							$returnArray['result'] = TPX_ONLINE_ERROR_HIGHLEVELPROJECTACTIVECHECKOUTSESSION;
							$returnArray['projectref'] = $projectRef;
							$returnArray['itemtoremoveid'] = $itemToRemoveID;
						}
					}
					else
					{
						$returnArray['result'] = TPX_ONLINE_ERROR_PROJECTDOESNOTEXIST;
					}
				}
				else
				{
					$returnArray['result'] = $checkDeleteSessionArray['error'];
					$returnArray['resultmessage'] = $checkDeleteSessionArray['error'];
				}
			}
			else if ($onlineBasketAuthenticated['authenticatedstatus'] == TPX_ONLINE_BASKETAPI_AUTHENTICATEDUSERSTATUS_SIGNEDINUSEREXPIRED)
			{
				$returnArray['result'] = TPX_ONLINE_ERROR_HIGHLEVELBASKETEXPIRED;
			}
		}
		else
		{
			$returnArray['result'] = TPX_ONLINE_ERROR_HIGHLEVELBASKETEXPIRED;
		}

		OnlineAPI_view::returnResultAPI($returnArray, $browserLanguageCode, TPX_BASKETWORKFLOWTYPE_HIGHLEVELAPI, $brandCode);

	}

	static function highLevelEmptyBasket()
	{
		global $gConstants;

		$returnArray = array('result' => 0, 'resultmessage' => '');

		$brandCode = UtilsObj::getGlobalValue('gDefaultSiteBrandingCode', '');
		$forcekill = UtilsObj::cleanseInput(UtilsObj::getHighLevelBasketAPIPOSTParams('forcekill', 0));

		$inputParamArray = self::getAPIInputParameters();
		$browserLanguageCode = $inputParamArray['langcode'];

		if (($inputParamArray['basketref'] != '') && ($inputParamArray['basketref'] != TPX_ONLINE_BASKETAPI_DEFAULTBASKETREF))
		{
			$onlineBasketAuthenticated = AuthenticateObj::checkIfOnlineBasketRequiresAuthenticatedUser($inputParamArray['basketref']);

			if ($onlineBasketAuthenticated['result'] != '')
			{
				$returnArray['result'] = TPX_ONLINE_ERROR_HIGHLEVELINTERNALERROR;
			}
			else if ($onlineBasketAuthenticated['authenticatedstatus'] == TPX_ONLINE_BASKETAPI_AUTHENTICATEDUSERSTATUS_SIGNEDINUSER)
			{
				$emptyBasketArray = OnlineAPI_model::emptyBasket($inputParamArray['basketref'], $forcekill);

				if ($emptyBasketArray['result'] != '')
				{
					if ($emptyBasketArray['result'] == TPX_ONLINE_ERROR_HIGHLEVELPROJECTACTIVECHECKOUTSESSION)
					{
						$returnArray['result'] = $emptyBasketArray['result'];
						$returnArray['resultmessage'] = $emptyBasketArray['resultparam'];
					}
					else
					{
						$returnArray['result'] = TPX_ONLINE_ERROR_DATABASE;
						$returnArray['resultmessage'] = $emptyBasketArray['resultparam'];
					}
				}
			}
			else if ($onlineBasketAuthenticated['authenticatedstatus'] == TPX_ONLINE_BASKETAPI_AUTHENTICATEDUSERSTATUS_SIGNEDINUSEREXPIRED)
			{
				$returnArray['result'] = TPX_ONLINE_ERROR_HIGHLEVELBASKETEXPIRED;
			}
		}
		else
		{
			$returnArray['result'] = TPX_ONLINE_ERROR_HIGHLEVELBASKETEXPIRED;
		}

		OnlineAPI_view::returnResultAPI($returnArray, $browserLanguageCode, TPX_BASKETWORKFLOWTYPE_HIGHLEVELAPI, $brandCode);
	}

	static function highLevelCheckout()
	{
		global $gConstants;
		global $gSession;

		$returnArray = array('result' => 0, 'resultmessage' => '');

		$ssoEnabled = UtilsObj::cleanseInput(UtilsObj::getPOSTParam('ssoenabled', TPX_SSO_HIGHLEVEL_ENABLED_OFF), true);

		$inputParamArray = self::getAPIInputParameters();

		$onlineBasketRef = $inputParamArray['basketref'];
		$browserLanguageCode = $inputParamArray['langcode'];
		$brandCode = UtilsObj::getGlobalValue('gDefaultSiteBrandingCode', '');

		if (($onlineBasketRef != '') && ($onlineBasketRef != TPX_ONLINE_BASKETAPI_DEFAULTBASKETREF))
		{

			$onlineBasketAuthenticated = AuthenticateObj::checkIfOnlineBasketRequiresAuthenticatedUser($onlineBasketRef);

			if ($onlineBasketAuthenticated['result'] != '')
			{
				$returnArray['result'] = TPX_ONLINE_ERROR_HIGHLEVELINTERNALERROR;
			}
			else if ($onlineBasketAuthenticated['authenticatedstatus'] == TPX_ONLINE_BASKETAPI_AUTHENTICATEDUSERSTATUS_SIGNEDINUSER)
			{
				$cartDataArray = OnlineAPI_model::getBasketContentsForCheckOut($onlineBasketRef);

				if (count($cartDataArray['projectreflist']) > 0)
				{
					$cartDataArray['languagecode'] = $browserLanguageCode;

					// set the onlineclienttime session variable from toapixonline
					// this will force any calls to the maweb which go through order.initialise to use this value rather than the
					// one in the cookie. this is becasue the cookie one might be too old.
					$gSession['onlineclienttime'] = UtilsObj::getPOSTParam('prtz', 0);

					$userID = $cartDataArray['userid'];
					$userDataArray = DatabaseObj::getUserAccountFromID($userID);

					// Although session data has not been created at this point we need to initialise userAddressUpdated & userid
					// so they can be passed to the external shopping cart.
					$gSession['useraddressupdated'] = $userDataArray['addressupdated'];
					$gSession['userid'] = $userID;

					if ($ssoEnabled != TPX_SSO_HIGHLEVEL_ENABLED_OFF)
					{
						$errorOccured = false;
						$startSession = true;

						$highLevelBasketUserSesionResultArray = AuthenticateObj::checkHighLevelBasketSessionActive($onlineBasketRef);

						if (($highLevelBasketUserSesionResultArray['result'] == '') && ($highLevelBasketUserSesionResultArray['sessionactive'] == 1))
						{
							$startSession = false;
						}

						// attempt a login. this will call the ssoLogin function
						$ssoResultArray = AuthenticateObj::authenticateLogin(TPX_USER_AUTH_REASON_HIGHLEVEL_CHECKOUT, -1, false, UtilsObj::getBrowserLocale(),
							$brandCode, '', '', '', TPX_PASSWORDFORMAT_CLEARTEXT, '', true, $startSession, true, '', array(), array('ref' => $onlineBasketRef));

						// the user has logged in so procceed
						if (($ssoResultArray['result'] == '') && ($ssoResultArray['useraccountid'] > 0))
						{
							// make sure the user who has logged in with sso is the same user who has is going to the cart
							if ($ssoResultArray['useraccountid'] != $userID)
							{
								$ssoResultArray['result'] = 'str_ErrorAccountTaskNotAllowed';
								$errorOccured = true;
							}
							else
							{
								$returnArray = OnlineAPI_model::externalCheckout($cartDataArray, TPX_BASKETWORKFLOWTYPE_HIGHLEVELCHECKOUT, $onlineBasketRef);

								if ($returnArray['result'] == TPX_ONLINE_ERROR_NONE)
								{
									// this will start the order session
									DatabaseObj::startSession($userID, $userDataArray['login'], $userDataArray['contactfirstname'] . ' ' . $userDataArray['contactlastname'],
										TPX_LOGIN_CUSTOMER, $userDataArray['companycode'], $userDataArray['owner'], $userDataArray['webbrandcode'],  $userDataArray['groupcode'], '', array());
								}
								else
								{
									$errorOccured = true;
									$ssoResultArray['result'] = $returnArray['result'];
								}
							}

						}
						elseif ($ssoResultArray['result'] == 'SSOREDIRECT')
						{
							// start the sso login work flow of the licensee
							$returnArray['shoppingcarturl'] = $ssoResultArray['resultparam'];
							$returnArray['result'] = 0;
						}
						else
						{
							$errorOccured = true;
						}

						if ($errorOccured)
						{
							// determine what type of error has occured
							if ($ssoResultArray['result'] == 'str_DatabaseError')
							{
								$returnArray['result'] = TPX_ONLINE_ERROR_DATABASE;
							}
							elseif ($ssoResultArray['result'] == 'str_ErrorEmptyGroupCode')
							{
								$returnArray['result'] = TPX_ONLINE_ERROR_EMPTYGROUPCODE;
							}
							elseif (($ssoResultArray['result'] == 'str_ErrorAccountMisMatch') || ($ssoResultArray['result'] == 'str_ErrorDuplicateUserName'))
							{
								$returnArray['result'] = TPX_ONLINE_ERROR_ACCOUNT_MISTMATCH;
							}
							elseif ($ssoResultArray['result'] == 'str_ErrorAccountTaskNotAllowed')
							{
								$returnArray['result'] = TPX_ONLINE_ERROR_ACCOUNTTASKNOTALLOWED;
							}

							// delete the session becuase there has been something wrong with the authentication request
							OnlineAPI_model::deleteHighLevelUserSession($onlineBasketRef);

							$brandingArray = DatabaseObj::getBrandingFromCode($brandCode);

							// find out where to redirect to. this url will most likely be the url back to the
							// product selector. this will cause checkUserSession to go off again and because we have logged the session out
							// it will log the user out of the product selector
							if ($brandingArray['onlinedesignerlogouturl'] != '')
							{
								$homeURL = $brandingArray['onlinedesignerlogouturl'];
							}
							else
							{
								$hl_config = UtilsObj::readWebBrandConfigFile('../config/onlinebaskethighlevelapi.conf', $brandCode);
								$homeURL = UtilsObj::getArrayParam($hl_config, 'REDIRECTIONURL');
							}

							$returnArray['shoppingcarturl'] = $homeURL;
						}
					}
					else
					{

						$startOrderSession = false;

						$highLevelBasketUserSesionResultArray = AuthenticateObj::checkHighLevelBasketSessionActive($onlineBasketRef);

						if (($highLevelBasketUserSesionResultArray['result'] == '') && ($highLevelBasketUserSesionResultArray['sessionactive'] == 1))
						{
							$startOrderSession = true;
						}

						$returnArray = OnlineAPI_model::externalCheckout($cartDataArray, TPX_BASKETWORKFLOWTYPE_HIGHLEVELCHECKOUT, $onlineBasketRef);

						if (($returnArray['result'] == TPX_ONLINE_ERROR_NONE) && ($startOrderSession))
						{

							DatabaseObj::startSession($userID, $userDataArray['login'], $userDataArray['contactfirstname'] . ' ' . $userDataArray['contactlastname'],
								TPX_LOGIN_CUSTOMER, $userDataArray['companycode'], $userDataArray['owner'], $userDataArray['webbrandcode'],  $userDataArray['groupcode'], '', array());
						}
						else if (($returnArray['result'] == TPX_ONLINE_ERROR_NONE) && (!$startOrderSession))
						{
							// there is no valid user session. Therfore we must force the user to log back in at the shopping cart using the same
							// user id that belongs to the basket. In order for us to capture this when at the shopping cart side we must tag a paramter
							// onto the shopping cart URL
							$returnArray['shoppingcarturl'] = $returnArray['shoppingcarturl'] . '&hlfbu=1';
						}
					}
				}
				else
				{
					$returnArray['result'] = TPX_ONLINE_ERROR_HIGHLEVELBASKETEMPTY;
				}
			}
			else if ($onlineBasketAuthenticated['authenticatedstatus'] == TPX_ONLINE_BASKETAPI_AUTHENTICATEDUSERSTATUS_SIGNEDINUSEREXPIRED)
			{
				$returnArray['result'] = TPX_ONLINE_ERROR_HIGHLEVELBASKETEXPIRED;
			}
		}
		else
		{
			$returnArray['result'] = TPX_ONLINE_ERROR_HIGHLEVELBASKETEXPIRED;
		}

		OnlineAPI_view::returnResultAPI($returnArray, $browserLanguageCode, TPX_BASKETWORKFLOWTYPE_HIGHLEVELAPI, $brandCode);
	}

	static function highLevelSignInInit()
	{
		global $gConstants;
		global $ac_config;

		$returnArray = array('result' => 0, 'resultmessage' => '', 'signinurl' => '');
		$browserLanguageCode = UtilsObj::cleanseLanguageCode(UtilsObj::getPOSTParam('browserlocale', ''), $gConstants['defaultlanguagecode']);
		$onlineBasketRef = UtilsObj::cleanseInput(UtilsObj::getHighLevelBasketAPIPOSTParams('mawebhlbr', TPX_ONLINE_BASKETAPI_DEFAULTBASKETREF), true);
		$onlineBasketUID = UtilsObj::cleanseInput(UtilsObj::getHighLevelBasketAPIPOSTParams('mawebhluid', 0), true);
		$isSSOEnabled = (UtilsObj::cleanseInput(UtilsObj::getPOSTParam('ssoenabled', TPX_SSO_HIGHLEVEL_ENABLED_OFF), true) != TPX_SSO_HIGHLEVEL_ENABLED_OFF);

		$groupCode = UtilsObj::getPOSTParam('groupcode');
		$host = $_SERVER['HTTP_HOST'];
		$brandCode = UtilsObj::getGlobalValue('gDefaultSiteBrandingCode', '');

		$signInInitParams = array();
 		$signInInitParams['languagecode'] = $browserLanguageCode;
		$signInInitParams['groupcode'] = $groupCode;

		self::includeOnlineBasketAPI(TPX_BASKETWORKFLOWTYPE_HIGHLEVELAPI);

		if (method_exists('OnlineBasketHighLevelAPI', 'signInInit'))
		{
			$signInInitParams = OnlineBasketHighLevelAPI::signInInit($signInInitParams);
			$groupCode = $signInInitParams['groupcode'];
		}

		if ($groupCode == '')
		{
			// read the config file for the default brand as at this point we have no groupcode
			$hl_config = UtilsObj::readWebBrandConfigFile('../config/onlinebaskethighlevelapi.conf', $brandCode);
			$groupCode = $hl_config['DEFAULTLICENSEKEYCODE'];
		}

		$licenseKeyDataArray = DatabaseObj::getLicenseKeyFromCode($groupCode);
		$brandCode = $licenseKeyDataArray['webbrandcode'];

		$brandingArray = DatabaseObj::getBrandingFromCode($brandCode);

		if ($brandingArray['usemultilinebasketworkflow'] == 1)
		{
			$notSSO = true;

			if ($isSSOEnabled)
			{
				$userID = 0;
				$userName = '';
				$ssoResult = '';
				$ssoResultParam = '';
				$ssoToken = '';
				$ssoPrivateDataArray = Array();
				$ssoExpireDate = '';

				// attempt to perform a single sign-on to the system
				$ssoResultArray = AuthenticateObj::authenticateLogin(TPX_USER_AUTH_REASON_HIGHLEVEL_LOGIN, -1, false, $browserLanguageCode,
																	$brandCode, $groupCode, '', '',
																	TPX_PASSWORDFORMAT_CLEARTEXT,  '', true, true, true, '', array(), array());

				if ($ssoResultArray['result'] == '')
				{
					$userID = $ssoResultArray['useraccountid'];
					$userName = $ssoResultArray['username'];
					$ssoToken = $ssoResultArray['ssotoken'];
					$ssoPrivateDataArray = $ssoResultArray['ssoprivatedata'];
					$assetServiceDataArray = $ssoResultArray['assetservicedata'];
					$ssoExpireDate = $ssoResultArray['ssoexpiredate'];
				}
				else
				{
					$ssoResult = $ssoResultArray['result'];
					$ssoResultParam = $ssoResultArray['resultparam'];
				}

				$notSSO = false;

				// process the result of the single sign-on request
				switch ($ssoResult)
				{
					case 'SSOREDIRECT':
					{
						// redirect to grab the single sign-on token
						$returnArray['signinurl'] = $ssoResultArray['resultparam'];
						break;
					}
					case '':
					{
						if ($userID > 0)
						{
							if ($onlineBasketRef == TPX_ONLINE_BASKETAPI_DEFAULTBASKETREF)
							{
								$userDataArray = DatabaseObj::getUserAccountFromID($userID);
								$groupCode = $userDataArray['groupcode'];
								$licenseKeyDataArray = DatabaseObj::getLicenseKeyFromCode($groupCode);
								$brandCode = $licenseKeyDataArray['webbrandcode'];

								$createBasketRecordResult = OnlineAPI_model::createBasketRecord($brandCode, $groupCode, '');
								$basketRecordID = $createBasketRecordResult['basketrecordid'];

								$onlineBasketRef = OnlineAPI_model::generateBasketRef($basketRecordID);

								// update the online basket record with the basket ref and the user id
								OnlineAPI_model::updateBasketRecordBasketRef($basketRecordID, $onlineBasketRef, $userID, $brandCode, $groupCode);

							}

							// Update userid the projects in the cart are assigned to.
							OnlineAPI_model::updateUserIDBasketRefForProjectsInBasket($userID, $onlineBasketRef);

							$updateSessionResult = DatabaseObj::linkOnlineBasketToSession($ssoResultArray['ref'], $onlineBasketRef, $userID);

							if ($updateSessionResult['result'] == '')
							{
								$productSelectorBrowserUTC = UtilsObj::getPOSTParam('prtz', 0);
								$expirationTime = OnlineAPI_model::generateBasketCookieExpiryDate($productSelectorBrowserUTC, $brandCode);
								$basketExpireDate = date('Y-m-d H:i:s', $expirationTime);

								// transfer the projects to the current basket based on the user who is logged in
								// no result expected from OnlineAPI_model::transferUserOnlineProjectsToBasket
								OnlineAPI_model::transferUserOnlineProjectsToBasket($brandCode, $groupCode, $userID, $onlineBasketRef, $basketExpireDate, $browserLanguageCode);

								$userSessionExpiryTime = UtilsObj::calcHighLevelClientCookieExpiryTime(((int) $ac_config['SESSIONDURATION'] * 60), $productSelectorBrowserUTC);

								$returnArray['ssotoken'] = $ssoToken;
								$returnArray['basketref'] = $onlineBasketRef;
								$returnArray['basketcookieexpirytime'] = $expirationTime;
								$returnArray['usercookieexpirytime'] = $userSessionExpiryTime;

							}

							if ($ssoResultArray['loginviasso'])
							{
								$returnArray['result'] = -2;
							}
						}
						else
						{
							$notSSO = true;
						}

						break;
					}
					default:
					{
						if ($ssoResult == 'str_DatabaseError')
						{
							$returnArray['result'] = TPX_ONLINE_ERROR_DATABASE;
						}
						elseif ($ssoResult == 'str_ErrorEmptyGroupCode')
						{
							$returnArray['result'] = TPX_ONLINE_ERROR_EMPTYGROUPCODE;
						}
						elseif (($ssoResult == 'str_ErrorAccountMisMatch') || ($ssoResult == 'str_ErrorDuplicateUserName'))
						{
							$returnArray['result'] = TPX_ONLINE_ERROR_ACCOUNT_MISTMATCH;
						}
						elseif ($ssoResult == 'str_ErrorAccountTaskNotAllowed')
						{
							$returnArray['result'] = TPX_ONLINE_ERROR_ACCOUNTTASKNOTALLOWED;
						}
						else
						{
							$returnArray['result'] = 99;
							$returnArray['resultmessage'] = $ssoResultParam;
						}

						break;
					}
				}
			}

			if ($notSSO)
			{
				if (($groupCode != '') && ($licenseKeyDataArray['isactive'] == 1))
				{
					$productSelectorUTC = UtilsObj::getPOSTParam('prtz', 0);
					$tokenValue = strtolower(UtilsObj::createRandomString(10));

					$privateDataArray = array(
						'mawebhluid' => $onlineBasketUID,
						'mawebhlbr' => $onlineBasketRef,
						'mawebhlottv' => $tokenValue,
						'mawebhlottvorig' => $tokenValue,
						'groupcode' => $groupCode,
						'l' => $browserLanguageCode,
						'prtz' => $productSelectorUTC
					);

					// to get the control centre signin page to display we need to redirect to a control centre domain
					// since we performing a redirect we have to attach everything control centre needs to the url
					// this could allow a session to be hijacked by copying and pasting the url so we need to prevent that
					// the solution is to add a token and token value to the database and return it on the url (a one time token)
					$authKeyDataArray = AuthenticateObj::createAuthenticationDataRecord($privateDataArray, '', TPX_AUTHENTICATIONTYPE_HIGHLEVEL, TPX_USER_AUTH_REASON_HIGHLEVEL_SIGNINDISPLAY);
					$authKey = $authKeyDataArray['authkey'];

					$redirectionURL = UtilsObj::correctPath(UtilsObj::getBrandedWebUrl($brandCode), '/', true);

					$returnArray['signinurl'] = $redirectionURL . '?fsaction=OnlineAPI.hlSignInDisplay&mawebhlottk=' . $authKey . '&mawebhlottv=' . $privateDataArray['mawebhlottv'] . '&mawebhluid=' . $onlineBasketUID. '&mawebhlbr=' . $onlineBasketRef . '&groupcode=' . $groupCode . '&l=' . $browserLanguageCode .'&prtz=' . $productSelectorUTC;
				}
				else
				{
					$returnArray['result'] = TPX_ONLINE_ERROR_INACTIVELICENSEKEY;
				}
			}
		}
		else
		{
			$returnArray['result'] = TPX_ONLINE_ERROR_HIGHLEVELNOTENABLED;
		}

		OnlineAPI_view::returnResultAPI($returnArray, $browserLanguageCode, TPX_BASKETWORKFLOWTYPE_HIGHLEVELAPI, $brandCode);
	}

	static function highLevelRegisterInit()
	{
		global $gConstants;

		$returnArray = array('result' => '', 'resultmessage' => '', 'signinurl' => '');
		$browserLanguageCode = UtilsObj::cleanseLanguageCode(UtilsObj::getPOSTParam('browserlocale', ''), $gConstants['defaultlanguagecode']);
		$onlineBasketRef = UtilsObj::cleanseInput(UtilsObj::getHighLevelBasketAPIPOSTParams('mawebhlbr', TPX_ONLINE_BASKETAPI_DEFAULTBASKETREF), true);
		$onlineBasketUID = UtilsObj::cleanseInput(UtilsObj::getHighLevelBasketAPIPOSTParams('mawebhluid', 0), true);

		$groupCode = UtilsObj::getPOSTParam('groupcode');
		$host = $_SERVER['HTTP_HOST'];
		$brandCode = UtilsObj::getGlobalValue('gDefaultSiteBrandingCode', '');

		$signInInitParams = array();
 		$signInInitParams['languagecode'] = $browserLanguageCode;
		$signInInitParams['groupcode'] = $groupCode;

		self::includeOnlineBasketAPI(TPX_BASKETWORKFLOWTYPE_HIGHLEVELAPI);

		if (method_exists('OnlineBasketHighLevelAPI', 'registerInit'))
		{
			$signInInitParams = OnlineBasketHighLevelAPI::registerInit($signInInitParams);
			$groupCode = $signInInitParams['groupcode'];
		}

		if ($groupCode == '')
		{
			// read the config file for the default brand as at this point we have no groupcode
			$hl_config = UtilsObj::readWebBrandConfigFile('../config/onlinebaskethighlevelapi.conf', $brandCode);
			$groupCode = $hl_config['DEFAULTLICENSEKEYCODE'];
		}

		$licenseKeyDataArray = DatabaseObj::getLicenseKeyFromCode($groupCode);
		$brandCode = $licenseKeyDataArray['webbrandcode'];

		$brandingArray = DatabaseObj::getBrandingFromCode($brandCode);

		if ($brandingArray['usemultilinebasketworkflow'] == 1)
		{
			if (($groupCode != '') && ($licenseKeyDataArray['isactive'] == 1))
			{
				$productSelectorUTC = UtilsObj::getPOSTParam('prtz', 0);
				$tokenValue = strtolower(UtilsObj::createRandomString(10));

				$privateDataArray = array(
					'mawebhluid' => $onlineBasketUID,
					'mawebhlbr' => $onlineBasketRef,
					'mawebhlottv' => $tokenValue,
					'mawebhlottvorig' => $tokenValue,
					'groupcode' => $groupCode,
					'l' => $browserLanguageCode,
					'prtz' => $productSelectorUTC
				);

				// to get the control centre register page to display we need to redirect to a control centre domain
				// since we performing a redirect we have to attach everything control centre needs to the url
				// this could allow a session to be hijacked by copying and pasting the url so we need to prevent that
				// the solution is to add a token and token value to the database and return it on the url (a one time token)
				$authKeyDataArray = AuthenticateObj::createAuthenticationDataRecord($privateDataArray, '', TPX_AUTHENTICATIONTYPE_HIGHLEVEL, TPX_USER_AUTH_REASON_HIGHLEVEL_REGISTERDISPLAY);
				$authKey = $authKeyDataArray['authkey'];

				$redirectionURL = UtilsObj::correctPath(UtilsObj::getBrandedWebUrl($brandCode), '/', true);

				$returnArray['signinurl'] = $redirectionURL . '?fsaction=OnlineAPI.hlRegisterDisplay&mawebhlottk=' . $authKey . '&mawebhlottv=' . $privateDataArray['mawebhlottv'] . '&mawebhluid=' . $onlineBasketUID. '&mawebhlbr=' . $onlineBasketRef . '&groupcode=' . $groupCode . '&l=' . $browserLanguageCode .'&prtz=' . $productSelectorUTC;
			}
			else
			{
				$returnArray['result'] = TPX_ONLINE_ERROR_INACTIVELICENSEKEY;
			}
		}
		else
		{
			$returnArray['result'] = TPX_ONLINE_ERROR_HIGHLEVELNOTENABLED;
		}

		OnlineAPI_view::returnResultAPI($returnArray, $browserLanguageCode, TPX_BASKETWORKFLOWTYPE_HIGHLEVELAPI, $brandCode);
	}

	static function highLevelMyAccountInit()
	{
		global $gConstants;
		global $gSession;

		$returnArray = array('result' => '', 'resultmessage' => '', 'signinurl' => '');

		$brandCode = '';

		$browserLanguageCode = UtilsObj::cleanseLanguageCode(UtilsObj::getPOSTParam('browserlocale', ''), $gConstants['defaultlanguagecode']);
		$onlineBasketRef = UtilsObj::cleanseInput(UtilsObj::getHighLevelBasketAPIPOSTParams('mawebhlbr', TPX_ONLINE_BASKETAPI_DEFAULTBASKETREF), true);

		if (($onlineBasketRef != '') && ($onlineBasketRef != TPX_ONLINE_BASKETAPI_DEFAULTBASKETREF))
		{
			// make sure we have a valid session for the user
			$highLevelBasketUserSesionResultArray = AuthenticateObj::checkHighLevelBasketSessionActive($onlineBasketRef);

			if (($highLevelBasketUserSesionResultArray['result'] == '') && ($highLevelBasketUserSesionResultArray['sessionactive'] == 1))
			{
				// we have a valid session

				// find the group code from the basket ref
				$basketGroupCodeResult = DatabaseObj::getUserIDGroupCodeFromBasketRef($onlineBasketRef);
				$groupCode = $basketGroupCodeResult['groupcode'];

				if ($groupCode != '')
				{
					$licenseKeyDataArray = DatabaseObj::getLicenseKeyFromCode($groupCode);

					if ($licenseKeyDataArray['isactive'] == 1)
					{
						$brandCode = $licenseKeyDataArray['webbrandcode'];

						$productSelectorUTC = UtilsObj::getPOSTParam('prtz', 0);
						$tokenValue = strtolower(UtilsObj::createRandomString(10));

						$privateDataArray = array(
							'mawebhlbr' => $onlineBasketRef,
							'mawebhlottv' => $tokenValue,
							'mawebhlottvorig' => $tokenValue,
							'l' => $browserLanguageCode
						);

						// to get the control centre my account pages to display we need to redirect to a control centre domain
						// since we performing a redirect we have to attach everything control centre needs to the url
						// this could allow a session to be hijacked by copying and pasting the url so we need to prevent that
						// the solution is to add a token and token value to the database and return it on the url (a one time token)
						$authKeyDataArray = AuthenticateObj::createAuthenticationDataRecord($privateDataArray, '', TPX_AUTHENTICATIONTYPE_HIGHLEVEL, TPX_USER_AUTH_REASON_HIGHLEVEL_MYACCOUNTDISPLAY);
						$authKey = $authKeyDataArray['authkey'];

						$redirectionURL = UtilsObj::correctPath(UtilsObj::getBrandedWebUrl($brandCode), '/', true);

						$returnArray['myaccounturl'] = $redirectionURL . '?fsaction=OnlineAPI.hlMyAccountDisplay&mawebhlottk=' . $authKey . '&mawebhlottv=' . $privateDataArray['mawebhlottv'] . '&mawebhlbr=' . $onlineBasketRef . '&l=' . $browserLanguageCode . '&t='.time();
					}
					else
					{
						// license key not active
						$returnArray['result'] = TPX_ONLINE_ERROR_INACTIVELICENSEKEY;
					}
				}
				else
				{
					// no license key
					$returnArray['result'] = TPX_ONLINE_ERROR_INACTIVELICENSEKEY;
				}
			}
			else
			{
				// no session
				$returnArray['result'] = TPX_ONLINE_ERROR_HIGHLEVELBASKETEXPIRED;
			}
		}
		else
		{
			$returnArray['result'] = TPX_ONLINE_ERROR_HIGHLEVELBASKETEXPIRED;
		}

		OnlineAPI_view::returnResultAPI($returnArray, $browserLanguageCode, TPX_BASKETWORKFLOWTYPE_HIGHLEVELAPI, $brandCode);
	}

	static function highLevelProcessLogin()
	{
		global $gSession;

		$isMObile = UtilsObj::getPOSTParam('mobile', false);
		$basketReturnToken = '';

        if ($isMObile == 'true')
        {
            $resultArray = OnlineAPI_model::processLogin(0, true);
        }
        else
        {
            $resultArray = OnlineAPI_model::processLogin(0, false);
        }

        if ($resultArray['result'] == '')
        {
			$onlineBasketRef = UtilsObj::cleanseInput(UtilsObj::getHighLevelBasketAPIPOSTParams('mawebhlbr', TPX_ONLINE_BASKETAPI_DEFAULTBASKETREF), true);
			$onlineBasketUID = UtilsObj::cleanseInput(UtilsObj::getHighLevelBasketAPIPOSTParams('mawebhluid', 0), true);

			$basketRefHasBeenClaimedResultArray = OnlineAPI_model::checkIfBasketIsAssignedToAUser($onlineBasketRef, $resultArray['useraccountid']);
			$basketRefBelongsToAnotherUser = $basketRefHasBeenClaimedResultArray['basketrefalreadyassigned'];

			if (($onlineBasketRef == TPX_ONLINE_BASKETAPI_DEFAULTBASKETREF) || ($basketRefBelongsToAnotherUser))
			{
				$userDataArray = DatabaseObj::getUserAccountFromID($resultArray['useraccountid']);
				$groupCode = $userDataArray['groupcode'];
				$licenseKeyDataArray = DatabaseObj::getLicenseKeyFromCode($groupCode);
				$brandCode = $licenseKeyDataArray['webbrandcode'];

				$createBasketRecordResult = OnlineAPI_model::createBasketRecord($brandCode, $groupCode, '');
				$basketRecordID = $createBasketRecordResult['basketrecordid'];

				$onlineBasketRef = OnlineAPI_model::generateBasketRef($basketRecordID);

				$updateBasketRefTokenResult = OnlineAPI_model::updateBasketRecordBasketRefAndToken($basketRecordID, $onlineBasketUID, $onlineBasketRef, $resultArray['useraccountid'], $brandCode, $groupCode);
				$basketReturnToken = $updateBasketRefTokenResult['token'];
			}

			// Update userid the projects in the cart are assigned to.
			OnlineAPI_model::updateUserIDBasketRefForProjectsInBasket($resultArray['useraccountid'], $onlineBasketRef);

			$updateSessionResult = DatabaseObj::linkOnlineBasketToSession($resultArray['ref'], $onlineBasketRef, $resultArray['useraccountid']);

			if ($updateSessionResult['result'] == '')
			{
				$brandDataArray = DatabaseObj::getBrandingFromCode($gSession['webbrandcode']);

				if ($brandDataArray['onlinedesignerlogouturl'] != '')
				{
					$redirectionURL = $brandDataArray['onlinedesignerlogouturl'];
				}
				else
				{
					$hl_config = UtilsObj::readWebBrandConfigFile('../config/onlinebaskethighlevelapi.conf', $gSession['webbrandcode']);
					$redirectionURL = $hl_config['REDIRECTIONURL'];
				}

				$redirectionURL = UtilsObj::correctPath($redirectionURL, '/', true);

				if ($basketReturnToken != '')
				{
					$redirectionURL = $redirectionURL . '?mawbt=' . $basketReturnToken;
				}

                OnlineAPI_view::redirectToURL($redirectionURL);
            }
        }
        else
        {
        	$error = $resultArray['result'];

        	if (substr($error, 0, 4) == 'str_')
			{
				$smarty = SmartyObj::newSmarty('Login', $gSession['webbrandcode'], $gSession['webbrandapplicationname']);
				SmartyObj::replaceParams($smarty, $error, $resultArray['resultparam']);
				$error = $smarty->get_template_vars($resultArray['result']);
			}

		    $fromRegisterLink = false;

		    if ($_POST['fromregisterlink'] == '1')
		    {
		    	$fromRegisterLink = true;
		    }

			if ($fromRegisterLink)
			{
				self::highLevelRegisterDisplay($error);
			}
			else
			{
				self::highLevelSignInDisplay($error, $fromRegisterLink);
			}
        }
	}

	static function highLevelSignInDisplay($pError = '', $pFromRegisterLink = false)
	{
		global $gConstants;

		self::cleanUpMAWebCookies();

		$browserLanguageCode = UtilsObj::cleanseLanguageCode(UtilsObj::getPOSTParam('browserlocale', ''), $gConstants['defaultlanguagecode']);
		$productSelectorUTC = UtilsObj::getGETParam('prtz', 0);
		$basketRef = UtilsObj::getHighLevelBasketAPIGETParams('mawebhlbr', TPX_ONLINE_BASKETAPI_DEFAULTBASKETREF);
		$highLevelUID = UtilsObj::cleanseInput(UtilsObj::getGETParam('mawebhluid', 0), true);
		$authKey = UtilsObj::getGETParam('mawebhlottk', '');
		$authKeyValue = UtilsObj::getGETParam('mawebhlottv', '');

		$groupCode = UtilsObj::getGETParam('groupcode', '');
		$host = $_SERVER['HTTP_HOST'];
		$brandCode = UtilsObj::getGlobalValue('gDefaultSiteBrandingCode', '');

		$signInInitParams = array();
 		$signInInitParams['languagecode'] = $browserLanguageCode;
		$signInInitParams['groupcode'] = $groupCode;

		self::includeOnlineBasketAPI(TPX_BASKETWORKFLOWTYPE_HIGHLEVELAPI);

		if (method_exists('OnlineBasketHighLevelAPI', 'signInInit'))
		{
			$signInInitParams = OnlineBasketHighLevelAPI::signInInit($signInInitParams);
			$groupCode = $signInInitParams['groupcode'];
		}

		if ($groupCode == '')
		{
			// read the config file as we dont have a groupcode
			$hl_config = UtilsObj::readWebBrandConfigFile('../config/onlinebaskethighlevelapi.conf', $brandCode);
			$groupCode = $hl_config['DEFAULTLICENSEKEYCODE'];
		}

		$URLDataArray = array(
			'mawebhlottk' => $authKey,
			'mawebhlottv' => $authKeyValue,
			'mawebhluid' => $highLevelUID,
			'mawebhlbr' => $basketRef,
			'groupcode' => $groupCode,
			'l' => $browserLanguageCode,
			'prtz' => $productSelectorUTC
		);

		// check to make sure that the signInDisplay URL has only been used once. This is to prevent basket from being highjacked by someone trying to use the same signInDisplay URL
		// in another browser.
		$actionAuthenticated = AuthenticateObj::authenticateHighLevelUserAction(TPX_AUTHENTICATIONTYPE_HIGHLEVEL, TPX_USER_AUTH_REASON_HIGHLEVEL_SIGNINDISPLAY, $URLDataArray);

		if ($actionAuthenticated)
		{
			$resultArray = OnlineAPI_model::displayLogin($groupCode, $pFromRegisterLink);
			$resultArray['mawebhluid'] = $highLevelUID;
			$resultArray['mawebhlbr'] = $basketRef;
			$resultArray['prtz'] = $productSelectorUTC;
			$resultArray['error'] = $pError;

			OnlineAPI_view::displayLogin($resultArray);
		}
		else
		{
			OnlineAPI_view::returnResult(TPX_ONLINE_ERROR_HIGHLEVELAUTH, $browserLanguageCode);
		}

		AuthenticateObj::deleteAuthenticationDataRecords();

	}

	static function highLevelRegisterDisplay($pError = '')
	{
		global $gConstants;

		$browserLanguageCode = UtilsObj::cleanseLanguageCode(UtilsObj::getPOSTParam('browserlocale', ''), $gConstants['defaultlanguagecode']);
		$productSelectorUTC = UtilsObj::getGETParam('prtz', 0);
		$basketRef = UtilsObj::getHighLevelBasketAPIGETParams('mawebhlbr', TPX_ONLINE_BASKETAPI_DEFAULTBASKETREF);
		$highLevelUID = UtilsObj::cleanseInput(UtilsObj::getGETParam('mawebhluid', 0), true);
		$authKey = UtilsObj::getGETParam('mawebhlottk', '');
		$authKeyValue = UtilsObj::getGETParam('mawebhlottv', '');

		$groupCode = UtilsObj::getGETParam('groupcode');
		$host = $_SERVER['HTTP_HOST'];
		$brandCode = UtilsObj::getGlobalValue('gDefaultSiteBrandingCode', '');

		$signInInitParams = array();
 		$signInInitParams['languagecode'] = $browserLanguageCode;
		$signInInitParams['groupcode'] = $groupCode;

		self::includeOnlineBasketAPI(TPX_BASKETWORKFLOWTYPE_HIGHLEVELAPI);

		if (method_exists('OnlineBasketHighLevelAPI', 'registerInit'))
		{
			$signInInitParams = OnlineBasketHighLevelAPI::registerInit($signInInitParams);
			$groupCode = $signInInitParams['groupcode'];
		}

		if ($groupCode == '')
		{
			// read the config file for the default brand as at this point we have no groupcode
			$hl_config = UtilsObj::readWebBrandConfigFile('../config/onlinebaskethighlevelapi.conf', $brandCode);
			$groupCode = $hl_config['DEFAULTLICENSEKEYCODE'];
		}

		$URLDataArray = array(
			'mawebhlottk' => $authKey,
			'mawebhlottv' => $authKeyValue,
			'mawebhluid' => $highLevelUID,
			'mawebhlbr' => $basketRef,
			'groupcode' => $groupCode,
			'l' => $browserLanguageCode,
			'prtz' => $productSelectorUTC
		);

		// check to make sure that the registerDisplay URL has only been used once. This is to prevent basket from being highjacked by someone trying to use the same registerDisplay URL
		// in another browser.
		$actionAuthenticated = AuthenticateObj::authenticateHighLevelUserAction(TPX_AUTHENTICATIONTYPE_HIGHLEVEL, TPX_USER_AUTH_REASON_HIGHLEVEL_REGISTERDISPLAY, $URLDataArray);

		if ($actionAuthenticated)
		{
			$resultArray = OnlineAPI_model::displayLogin($groupCode, true);
			$resultArray['mawebhluid'] = $highLevelUID;
			$resultArray['mawebhlbr'] = $basketRef;
			$resultArray['prtz'] = $productSelectorUTC;
			$resultArray['error'] = $pError;

			OnlineAPI_view::displayLogin($resultArray);
		}
		else
		{
			OnlineAPI_view::returnResult(TPX_ONLINE_ERROR_HIGHLEVELAUTH, $browserLanguageCode);
		}

		AuthenticateObj::deleteAuthenticationDataRecords();
	}

	static function highLevelMyAccountDisplay()
	{
		global $gConstants;
		global $gSession;
		global $ac_config;

		$actionAuthenticated = false;

		$browserLanguageCode = UtilsObj::cleanseLanguageCode(UtilsObj::getPOSTParam('browserlocale', ''), $gConstants['defaultlanguagecode']);
		$onlineBasketRef = UtilsObj::getHighLevelBasketAPIGETParams('mawebhlbr', TPX_ONLINE_BASKETAPI_DEFAULTBASKETREF);
		$authKey = UtilsObj::getGETParam('mawebhlottk', '');
		$authKeyValue = UtilsObj::getGETParam('mawebhlottv', '');

		// include the customer module
        require_once('../Customer/Customer_control.php');

		// check to see if we have a session for the basket
		// we don't update the expiry time during the check as it will also set the session cookie
		// we don't want this in case the security checks we perform afterwards fail as setting the cookie gives the user access
        $highLevelBasketUserSesionResultArray = AuthenticateObj::checkHighLevelBasketSessionActive($onlineBasketRef, false);

		if (($highLevelBasketUserSesionResultArray['result'] == '') && ($highLevelBasketUserSesionResultArray['sessionactive'] == 1))
		{
			// retrieve the session data for the basket
			$gSession = DatabaseObj::getSessionData($highLevelBasketUserSesionResultArray['sessionid']);

			$URLDataArray = array(
				'mawebhlottk' => $authKey,
				'mawebhlottv' => $authKeyValue,
				'mawebhlbr' => $onlineBasketRef,
				'l' => $browserLanguageCode
			);

			// check to make sure that the myAccountDisplay URL has only been used once.
			// this is to prevent a user session from being highjacked by someone trying to use the same myAccountDisplay URL in another browser.
			$actionAuthenticated = AuthenticateObj::authenticateHighLevelUserAction(TPX_AUTHENTICATIONTYPE_HIGHLEVEL, TPX_USER_AUTH_REASON_HIGHLEVEL_MYACCOUNTDISPLAY, $URLDataArray);
		}

		// if the session is valid display the customer account
		if ($actionAuthenticated)
		{
			// update the session expiry time which will set the session cookie
			DatabaseObj::updateSessionExpire($gSession['ref']);

			$userID = $highLevelBasketUserSesionResultArray['userid'];
			$hasFlaggedProjects = false;

			// check if the online designer is active
			if ($gConstants['optiondesol'] == 1)
			{
				$hasFlaggedProjects = Customer_model::getProjectsFlaggedForPurgeState($userID, $ac_config);
			}

			$resultArray['result'] = '';
			$resultArray['section'] = 'menu';
            $resultArray['user'] = DatabaseObj::getUserAccountFromID($userID);
            $resultArray['message'] = '';
            $resultArray['isConfirmation'] = '';
            $resultArray['ishighlevel'] = 1;
            $resultArray['showprojectsbutton'] = 0;
            $resultArray['onlinebasketref'] = $onlineBasketRef;
            $resultArray['hasflaggedprojects'] = $hasFlaggedProjects;

            Customer_view::display($resultArray);
		}
		else
		{
			// the session is not valid
			OnlineAPI_view::returnResult(TPX_ONLINE_ERROR_HIGHLEVELAUTH, $browserLanguageCode);
		}

	}

	static function highLevelCreateNewAccount($pIsLargeScreen)
	{
		global $gSession;

		$resultArray = OnlineAPI_model::createNewAccount();
		$basketReturnToken = '';

        if ($resultArray['result'] == '')
        {
			if ($pIsLargeScreen)
			{
				$onlineBasketRef = UtilsObj::cleanseInput(UtilsObj::getHighLevelBasketAPIPOSTParams('mawebhlbr', TPX_ONLINE_BASKETAPI_DEFAULTBASKETREF), true);
				$onlineBasketUID = UtilsObj::cleanseInput(UtilsObj::getHighLevelBasketAPIPOSTParams('mawebhluid', 0), true);

				$basketRefHasBeenClaimedResultArray = OnlineAPI_model::checkIfBasketIsAssignedToAUser($onlineBasketRef, $resultArray['useraccountid']);
				$basketRefBelongsToAnotherUser = $basketRefHasBeenClaimedResultArray['basketrefalreadyassigned'];

				if (($onlineBasketRef == TPX_ONLINE_BASKETAPI_DEFAULTBASKETREF) || ($basketRefBelongsToAnotherUser))
				{
					$userDataArray = DatabaseObj::getUserAccountFromID($resultArray['useraccountid']);
					$groupCode = $userDataArray['groupcode'];
					$licenseKeyDataArray = DatabaseObj::getLicenseKeyFromCode($groupCode);
					$brandCode = $licenseKeyDataArray['webbrandcode'];

					$createBasketRecordResult = OnlineAPI_model::createBasketRecord($brandCode, $groupCode, '');
					$basketRecordID = $createBasketRecordResult['basketrecordid'];

					$onlineBasketRef = OnlineAPI_model::generateBasketRef($basketRecordID);

					$updateBasketRefTokenResult = OnlineAPI_model::updateBasketRecordBasketRefAndToken($basketRecordID, $onlineBasketUID, $onlineBasketRef, $resultArray['useraccountid'], $brandCode, $groupCode);
					$basketReturnToken = $updateBasketRefTokenResult['token'];
				}

				// Update userid the projects in the cart are assigned to.
				OnlineAPI_model::updateUserIDBasketRefForProjectsInBasket($resultArray['useraccountid'], $onlineBasketRef);

				$updateSessionResult = DatabaseObj::linkOnlineBasketToSession($resultArray['ref'], $onlineBasketRef, 0);

				if ($updateSessionResult['result'] == '')
				{
					$hl_config = UtilsObj::readWebBrandConfigFile('../config/onlinebaskethighlevelapi.conf', $gSession['webbrandcode']);
					$brandDataArray = DatabaseObj::getBrandingFromCode($gSession['webbrandcode']);

					if ($brandDataArray['onlinedesignerlogouturl'] != '')
					{
						$redirectionURL = $brandDataArray['onlinedesignerlogouturl'];
					}
					else
					{
						$redirectionURL = $hl_config['REDIRECTIONURL'];
					}


					if ($basketReturnToken != '')
					{
						$redirectionURL = $redirectionURL . '?mawbt=' . $updateBasketRefTokenResult['token'];
					}

					OnlineAPI_view::redirectToURL($redirectionURL);
				}
			}
			else
			{
				require_once('../Welcome/Welcome_view.php');
				$resultArray['registerfsaction'] = 'OnlineAPI.createNewAccountSmall';
			 	Welcome_view::createNewAccountSmall($resultArray);
			}
        }
		else
		{
			 require_once('../Welcome/Welcome_view.php');

			 if ($pIsLargeScreen)
			 {
			 	$resultArray['registerfsaction'] = 'OnlineAPI.createNewAccountLarge';
			 	$resultArray['prtz'] = UtilsObj::getGETParam('prtz', 0);
			 	$resultArray['mawebhlbr'] = UtilsObj::getHighLevelBasketAPIGETParams('mawebhlbr', TPX_ONLINE_BASKETAPI_DEFAULTBASKETREF);
			 	$resultArray['mawebhluid'] = UtilsObj::cleanseInput(UtilsObj::getGETParam('mawebhluid', 0), true);

			 	Welcome_view::createNewAccountLarge($resultArray);
			 }
			 else
			 {
			 	$resultArray['registerfsaction'] = 'OnlineAPI.createNewAccountSmall';
			 	Welcome_view::createNewAccountSmall($resultArray);
			 }
		}
	}

	static function highLevelCreateNewAccountLarge()
	{
		self::highLevelCreateNewAccount(true);
	}

	static function highLevelCreateNewAccountSmall()
	{
		self::highLevelCreateNewAccount(false);
	}

	static function highLevelCheckUserSession()
	{
		global $gSession;
		global $gConstants;
		global $ac_config;

		$browserLanguageCode = UtilsObj::cleanseLanguageCode(UtilsObj::getPOSTParam('browserlocale', ''), $gConstants['defaultlanguagecode']);

		$returnArray = array('result' => TPX_ONLINE_ERROR_NONE, 'resultmessage' => '', 'basketcount' => 0);
		$ssoResultArray = array('result' => '', 'resultparam' => '');

		$groupCode = '';
		$brandCode = '';
		$basketRef = UtilsObj::cleanseInput(UtilsObj::getHighLevelBasketAPIPOSTParams('mawebhlbr', TPX_ONLINE_BASKETAPI_DEFAULTBASKETREF), true);
		$onlineBasketUID = UtilsObj::cleanseInput(UtilsObj::getHighLevelBasketAPIPOSTParams('mawebhluid', 0), true);
		$lookUpToken = UtilsObj::cleanseInput(UtilsObj::getHighLevelBasketAPIPOSTParams('lookuptoken', ''), true);
		$ssoEnabled = UtilsObj::cleanseInput(UtilsObj::getPOSTParam('ssoenabled', TPX_SSO_HIGHLEVEL_ENABLED_OFF), true);

		$userID = 0;
		$userName = '';
		$ssoResult = '';
		$ssoResultParam = '';
		$ssoToken = '';
		$ssoPrivateDataArray = Array();
		$ssoExpireDate = '';
		$sessionRef = -1;

		$host = $_SERVER['HTTP_HOST'];
		$brandCode = UtilsObj::getGlobalValue('gDefaultSiteBrandingCode', '');

		$hl_config = UtilsObj::readWebBrandConfigFile('../config/onlinebaskethighlevelapi.conf', $brandCode);
		$returnArray['continueshoppingmessageenabled'] = UtilsObj::getArrayParam($hl_config, 'CONTINUESHOPPINGMESSAGEENABLED', 1);

		// retrieve the default language when creating new projects
		$languageCode = UtilsObj::getGETParam('l', '');

		// determine the default language code
		if ($languageCode == '')
		{
			$languageCode = UtilsObj::getBrowserLocale();
		}

		if ($ssoEnabled != TPX_SSO_HIGHLEVEL_ENABLED_OFF)
		{
			$startSession = true;

			if ($basketRef != TPX_ONLINE_BASKETAPI_DEFAULTBASKETREF)
			{
				// we must check to see if there is a valid user session for the current basketref
				$highLevelBasketUserSesionResultArray = AuthenticateObj::checkHighLevelBasketSessionActive($basketRef);

				if (($highLevelBasketUserSesionResultArray['result'] == '') && ($highLevelBasketUserSesionResultArray['sessionactive'] == 1))
				{
					$sessionRef = $highLevelBasketUserSesionResultArray['sessionid'];
					$startSession = false;
				}
			}

			// default the reason to login
			if ($ssoEnabled == TPX_SSO_HIGHLEVEL_ENABLED_SIGNIN)
			{
				$reason = TPX_USER_AUTH_REASON_HIGHLEVEL_CHECK_SESSION;
			}
			else
			{
				$reason = TPX_USER_AUTH_REASON_HIGHLEVEL_LOGIN;
			}

			// if we are not starting a session then we must already have a session
			// use the id of this session to grab the session data, this will allow us to get the ssoToken and ssoPrivateData
			// which can be passed to the authenticate function
			if (! $startSession)
			{
				$gSession = DatabaseObj::getSessionData($highLevelBasketUserSesionResultArray['sessionid']);

				$ssoToken = $gSession['userdata']['ssotoken'];
				$ssoPrivateDataArray = $gSession['userdata']['ssoprivatedata'];

				$reason = TPX_USER_AUTH_REASON_HIGHLEVEL_CHECK_SESSION;

			}

			// attempt to perform a single sign-on to the system
			$ssoResultArray = AuthenticateObj::authenticateLogin($reason, -1, false, $languageCode, $brandCode, $hl_config['DEFAULTLICENSEKEYCODE'], '', '',
																	TPX_PASSWORDFORMAT_CLEARTEXT,  '', true, $startSession, true,
																									$ssoToken, $ssoPrivateDataArray, array());


			if ($startSession)
			{
				$sessionRef = $ssoResultArray['ref'];
			}

			// if the result is empty then we have sucessfully performed an SSO login
			// or sso login was not needed
			if ($ssoResultArray['result'] == '')
			{
				$userID = $ssoResultArray['useraccountid'];
				$userName = $ssoResultArray['username'];
				$ssoToken = $ssoResultArray['ssotoken'];
				$ssoPrivateDataArray = $ssoResultArray['ssoprivatedata'];
				$assetServiceDataArray = $ssoResultArray['assetservicedata'];
				$ssoExpireDate = $ssoResultArray['ssoexpiredate'];
			}
			else
			{
				$ssoResult = $ssoResultArray['result'];
				$ssoResultParam = $ssoResultArray['resultparam'];
			}

			// process the result of the single sign-on request
			switch ($ssoResult)
			{
				case 'SSOREDIRECT':
				{
					// redirect to grab the single sign-on token
					$returnArray['result'] = -2;
					$returnArray['ssoredirect'] = $ssoResultArray['resultparam'];
					break;
				}
				case '':
				{
					// lookup the basket ref based on the token then wipe out the token
					if ($lookUpToken != '')
					{
						$lookUpResult = OnlineAPI_model::lookUpBasketFromToken($onlineBasketUID, $lookUpToken);
						$groupCode = $lookUpResult['groupcode'];
						$licenseKeyDataArray = DatabaseObj::getLicenseKeyFromCode($groupCode);
						$brandCode = $licenseKeyDataArray['webbrandcode'];

						if (($lookUpResult['result'] == '') && ($lookUpResult['basketref'] != ''))
						{
							$basketRef = $lookUpResult['basketref'];
							OnlineAPI_model::emptyBasketToken($basketRef);
						}
					}

					// if there is an acutal basketref then get the group and brand code from it
					if ($basketRef != TPX_ONLINE_BASKETAPI_DEFAULTBASKETREF)
					{
						$basketGroupCodeResult = DatabaseObj::getUserIDGroupCodeFromBasketRef($basketRef);
						$groupCode = $basketGroupCodeResult['groupcode'];
						$licenseKeyDataArray = DatabaseObj::getLicenseKeyFromCode($groupCode);
						$brandCode = $licenseKeyDataArray['webbrandcode'];
					}

					if ($groupCode == '')
					{
						$groupCode = $hl_config['DEFAULTLICENSEKEYCODE'];
					}

					// attempt to link the basketref with a session
					$highLevelBasketUserSesionResultArray = AuthenticateObj::checkHighLevelBasketSessionActive($basketRef);

					$sessionUserID = 0;

					if (($highLevelBasketUserSesionResultArray['result'] == '') && ($highLevelBasketUserSesionResultArray['sessionactive'] == 1))
					{
						$sessionUserID = $highLevelBasketUserSesionResultArray['userid'];
					}

					// only run this code if the user has logged in and it is an SSO call
					if ($userID > 0)
					{
						if (($userID != $sessionUserID) && ($sessionUserID > 0))
						{
							// since we are logging in as a different user we need to kill the current session and return the error to the
							// front end. this will cause a log out and subsequently a new session to be created with the correct user
							$returnArray['result'] = TPX_ONLINE_ERROR_ACCOUNTTASKNOTALLOWED;

							// delete the session becuase there has been something wrong with the authentication request
							OnlineAPI_model::deleteHighLevelUserSession($basketRef);

							$brandingArray = DatabaseObj::getBrandingFromCode($brandCode);

							// find out where to redirect to. this url will most likely be the url back to the
							// product selector. this will cause checkUserSession to go off again and because we have logged the session out
							// it will log the user out of the product selector
							if ($brandingArray['onlinedesignerlogouturl'] != '')
							{
								$homeURL = $brandingArray['onlinedesignerlogouturl'];
							}
							else
							{
								$hl_config = UtilsObj::readWebBrandConfigFile('../config/onlinebaskethighlevelapi.conf', $brandCode);
								$homeURL = UtilsObj::getArrayParam($hl_config, 'REDIRECTIONURL');
							}

							$returnArray['redirecturl'] = $homeURL;
						}
						else
						{
							$newBasketRef = false;

							if ($basketRef == TPX_ONLINE_BASKETAPI_DEFAULTBASKETREF)
							{
								$createBasketRecordResult = OnlineAPI_model::createBasketRecord($brandCode, $groupCode, '');
								$basketRecordID = $createBasketRecordResult['basketrecordid'];
								$basketExpireDate = $createBasketRecordResult['basketexpiredate'];

								$basketRef = OnlineAPI_model::generateBasketRef($basketRecordID);

								$newBasketRef = true;
							}

							// only update the projects if the basket ref is is
							if ($newBasketRef)
							{
								// Update userid the projects in the cart are assigned to.
								OnlineAPI_model::updateUserIDBasketRefForProjectsInBasket($userID, $basketRef);

								// update the online basket record with the basket ref and the user id
								OnlineAPI_model::updateBasketRecordBasketRef($basketRecordID, $basketRef, $userID, $brandCode, $groupCode);
							}

							$productSelectorBrowserUTC = UtilsObj::getPOSTParam('prtz', 0);
							$expirationTime = OnlineAPI_model::generateBasketCookieExpiryDate($productSelectorBrowserUTC, $brandCode);
							$basketExpireDate = date('Y-m-d H:i:s', $expirationTime);

							$userSessionExpiryTime = UtilsObj::calcHighLevelClientCookieExpiryTime(((int) $ac_config['SESSIONDURATION'] * 60), $productSelectorBrowserUTC);

							$basketCountArray = OnlineAPI_model::retrieveBasketCount($basketRef);
							$basketCount = $basketCountArray['basketcount'];

							// transfer the projects to the current basket based on the user who is logged in
							// no result expected from OnlineAPI_model::transferUserOnlineProjectsToBasket
							OnlineAPI_model::transferUserOnlineProjectsToBasket($brandCode, $groupCode, $userID, $basketRef, $basketExpireDate, $browserLanguageCode);

							$updateSessionResult = DatabaseObj::linkOnlineBasketToSession($sessionRef, $basketRef, $userID);

							$returnArray['basketref'] = $basketRef;
							$returnArray['basketcookieexpirytime'] = $expirationTime;
							$returnArray['usercookieexpirytime'] = $userSessionExpiryTime;
							$returnArray['basketcount'] = $basketCount;
							$returnArray['ssotoken'] = $ssoToken;
						}
					}
					else
					{
						$returnArray['result'] = -1;
					}

					break;
				}
				default:
				{
					if ($ssoResult == 'str_DatabaseError')
					{
						$returnArray['result'] = TPX_ONLINE_ERROR_DATABASE;
					}
					elseif ($ssoResult == 'str_ErrorEmptyGroupCode')
					{
						$returnArray['result'] = TPX_ONLINE_ERROR_EMPTYGROUPCODE;
					}
					elseif (($ssoResult == 'str_ErrorAccountMisMatch') || ($ssoResult == 'str_ErrorDuplicateUserName'))
					{
						$returnArray['result'] = TPX_ONLINE_ERROR_ACCOUNT_MISTMATCH;
					}
					elseif ($ssoResult == 'str_ErrorAccountTaskNotAllowed')
					{
						$returnArray['result'] = TPX_ONLINE_ERROR_ACCOUNTTASKNOTALLOWED;
					}
					else
					{
						$returnArray['result'] = 99;
						$returnArray['resultmessage'] = $ssoResultParam;
					}

					// delete the session becuase there has been something wrong with the authentication request
					OnlineAPI_model::deleteHighLevelUserSession($basketRef);

					$brandingArray = DatabaseObj::getBrandingFromCode($brandCode);

					// find out where to redirect to. this url will most likely be the url back to the
					// product selector. this will cause checkUserSession to go off again and because we have logged the session out
					// it will log the user out of the product selector
					if ($brandingArray['onlinedesignerlogouturl'] != '')
					{
						$homeURL = $brandingArray['onlinedesignerlogouturl'];
					}
					else
					{
						$hl_config = UtilsObj::readWebBrandConfigFile('../config/onlinebaskethighlevelapi.conf', $brandCode);
						$homeURL = UtilsObj::getArrayParam($hl_config, 'REDIRECTIONURL');
					}

					$returnArray['redirecturl'] = $homeURL;

					break;
				}
			}
		}
		else
		{
			// lookup the basket ref based on the token then wipe out the token
			if ($lookUpToken != '')
			{
				$lookUpResult = OnlineAPI_model::lookUpBasketFromToken($onlineBasketUID, $lookUpToken);
				$groupCode = $lookUpResult['groupcode'];
				$licenseKeyDataArray = DatabaseObj::getLicenseKeyFromCode($groupCode);
				$brandCode = $licenseKeyDataArray['webbrandcode'];

				if (($lookUpResult['result'] == '') && ($lookUpResult['basketref'] != ''))
				{
					$basketRef = $lookUpResult['basketref'];
					OnlineAPI_model::emptyBasketToken($basketRef);
				}
			}

			if ($basketRef != TPX_ONLINE_BASKETAPI_DEFAULTBASKETREF)
			{
				$basketGroupCodeResult = DatabaseObj::getUserIDGroupCodeFromBasketRef($basketRef);
				$groupCode = $basketGroupCodeResult['groupcode'];
				$licenseKeyDataArray = DatabaseObj::getLicenseKeyFromCode($groupCode);
				$brandCode = $licenseKeyDataArray['webbrandcode'];
			}

			$highLevelBasketUserSesionResultArray = AuthenticateObj::checkHighLevelBasketSessionActive($basketRef);

			if (($highLevelBasketUserSesionResultArray['result'] == '') && ($highLevelBasketUserSesionResultArray['sessionactive'] == 1))
			{
				$sessionRef = $highLevelBasketUserSesionResultArray['sessionid'];
				$userID = $highLevelBasketUserSesionResultArray['userid'];

				$productSelectorBrowserUTC = UtilsObj::getPOSTParam('prtz', 0);
				$expirationTime = OnlineAPI_model::generateBasketCookieExpiryDate($productSelectorBrowserUTC, $brandCode);
				$basketExpireDate = date('Y-m-d H:i:s', $expirationTime);

				// transfer the projects to the current basket based on the user who is logged in
				// no result expected from OnlineAPI_model::transferUserOnlineProjectsToBasket
				OnlineAPI_model::transferUserOnlineProjectsToBasket($brandCode, $groupCode, $userID, $basketRef, $basketExpireDate, $browserLanguageCode);

				$updateSessionResult = DatabaseObj::linkOnlineBasketToSession($sessionRef, $basketRef, $userID);

				$userSessionExpiryTime = UtilsObj::calcHighLevelClientCookieExpiryTime(((int) $ac_config['SESSIONDURATION'] * 60), $productSelectorBrowserUTC);

				$basketCountArray = OnlineAPI_model::retrieveBasketCount($basketRef);
				$basketCount = $basketCountArray['basketcount'];

				$returnArray['basketref'] = $basketRef;
				$returnArray['basketcookieexpirytime'] = $expirationTime;
				$returnArray['usercookieexpirytime'] = $userSessionExpiryTime;
				$returnArray['basketcount'] = $basketCount;
			}
			else
			{
				$returnArray['result'] = -1;
			}
		}

		OnlineAPI_view::returnResultAPI($returnArray, $browserLanguageCode, TPX_BASKETWORKFLOWTYPE_HIGHLEVELAPI, $brandCode);
	}

	static function highLevelLogout()
	{
		global $gConstants;
		global $gSession;

		$browserLanguageCode = UtilsObj::cleanseLanguageCode(UtilsObj::getPOSTParam('browserlocale', ''), $gConstants['defaultlanguagecode']);

		$ssoPrivateDataArray = array();

        if ($gSession['ref'] > 0)
        {
			// extract the single sign-on data
			$ssoToken = $gSession['userdata']['ssotoken'];
			$ssoPrivateDataArray = $gSession['userdata']['ssoprivatedata'];
		}
		else
		{
			// attempt to get the ssotoken from the URL
			$ssoToken = UtilsObj::getGETParam('ssotoken', UtilsObj::getPOSTParam('ssotoken'));
		}

		$returnArray = array('result' => TPX_ONLINE_ERROR_NONE, 'resultmessage' => '');

		$basketRef = UtilsObj::cleanseInput(UtilsObj::getHighLevelBasketAPIPOSTParams('mawebhlbr', TPX_ONLINE_BASKETAPI_DEFAULTBASKETREF), true);
		$brandCode = '';

		$deleteHighLevelUserSessionResult = OnlineAPI_model::deleteHighLevelUserSession($basketRef);

		if ($deleteHighLevelUserSessionResult != '')
		{
			$returnArray['result'] = -1;
			$returnArray['resultmessage'] = $deleteHighLevelUserSessionResult;
		}

		$basketGroupCodeResult = DatabaseObj::getUserIDGroupCodeFromBasketRef($basketRef);
		$licenseKeyDataArray = DatabaseObj::getLicenseKeyFromCode($basketGroupCodeResult['groupcode']);
		$brandCode = $licenseKeyDataArray['webbrandcode'];

		$clearOnlineBasketSessionIDResult = AuthenticateObj::clearOnlineBasketSessionIDWithReason($basketRef, TPX_ONLINE_BASKETAPI_INVALIDATEBASKETREFREASON_PSLOGOUT);

		// call the single sign-out process if we have a token and the session had been started
		if (($ssoToken != '') && ($gConstants['optionwscrp']) && (file_exists('../Customise/scripts/EDL_ExternalCustomerAccount.php')))
		{
			require_once('../Customise/scripts/EDL_ExternalCustomerAccount.php');

			if (method_exists('ExternalCustomerAccountObj', 'ssoLogout'))
			{
				$ssoParamArray['reason'] = TPX_USER_LOGOUT_REASON_USER_LOGOUT;
				$ssoParamArray['ssotoken'] = $ssoToken;
				$ssoParamArray['ssoprivatedata'] = $ssoPrivateDataArray;

				$returnArray['result'] = -2;
				$returnArray['ssoredirect'] = ExternalCustomerAccountObj::ssoLogout($ssoParamArray);
			}
		}

		OnlineAPI_view::returnResultAPI($returnArray, $browserLanguageCode, TPX_BASKETWORKFLOWTYPE_HIGHLEVELAPI, $brandCode);
	}

	static function highLevelAccountPageLogout()
	{
		global $gConstants;
		global $gSession;

		$basketRef = UtilsObj::getPOSTParam('basketref');
		$brandCode = UtilsObj::getPOSTParam('webbrandcode');

		$ssoPrivateDataArray = array();

        if ($gSession['ref'] > 0)
        {
			// extract the single sign-on data
			$ssoToken = $gSession['userdata']['ssotoken'];
			$ssoPrivateDataArray = $gSession['userdata']['ssoprivatedata'];
		}
		else
		{
			// attempt to get the ssotoken from the URL
			$ssoToken = UtilsObj::getGETParam('ssotoken', UtilsObj::getPOSTParam('ssotoken'));
		}

		$deleteHighLevelUserSessionResult = OnlineAPI_model::deleteHighLevelUserSession($basketRef);
		$clearOnlineBasketSessionIDResult = AuthenticateObj::clearOnlineBasketSessionIDWithReason($basketRef, TPX_ONLINE_BASKETAPI_INVALIDATEBASKETREFREASON_CPLOGOUT);


		// call the single sign-out process if we have a token and the session had been started
		if (($ssoToken != '') && ($gConstants['optionwscrp']) && (file_exists('../Customise/scripts/EDL_ExternalCustomerAccount.php')))
		{
			require_once('../Customise/scripts/EDL_ExternalCustomerAccount.php');

			if (method_exists('ExternalCustomerAccountObj', 'ssoLogout'))
			{
				$ssoParamArray['reason'] = TPX_USER_LOGOUT_REASON_HIGHLEVEL_ACCOUNTPAGES;
				$ssoParamArray['ssotoken'] = $ssoToken;
				$ssoParamArray['ssoprivatedata'] = $ssoPrivateDataArray;

				$redirectionURL = ExternalCustomerAccountObj::ssoLogout($ssoParamArray);
			}
		}
		else
		{
			$hl_config = UtilsObj::readWebBrandConfigFile('../config/onlinebaskethighlevelapi.conf', $brandCode);
			$brandDataArray = DatabaseObj::getBrandingFromCode($brandCode);

			if ($brandDataArray['onlinedesignerlogouturl'] != '')
			{
				$redirectionURL = $brandDataArray['onlinedesignerlogouturl'];
			}
			else
			{
				$redirectionURL = $hl_config['REDIRECTIONURL'];
			}

			// only add odlo to the redirection URL if it is missing
			if ((strpos($redirectionURL, "?odlo=") === false) && (strpos($redirectionURL, "&odlo=") === false))
			{
				$redirectionURL = UtilsObj::addURLParameter($redirectionURL, "odlo", 1);
			}

		}

		// clear the session expiration date and cookie
        setcookie('mawebdata' . $gSession['ref'], '', 1, '/');

		header("Location: " . $redirectionURL);
	}

	static function unlockProject()
	{
		global $gConstants;

		$browserLanguageCode = UtilsObj::cleanseLanguageCode(UtilsObj::getGETParam('l', ''), $gConstants['defaultlanguagecode']);

		$unlockProjectParams = array();
		$unlockProjectParams['result'] = '';
 		$unlockProjectParams['languagecode'] = $browserLanguageCode;
		$unlockProjectParams['projectreflist'] = array();

		self::includeOnlineBasketAPI(TPX_BASKETWORKFLOWTYPE_LOWLEVELAPI);

		if (method_exists('OnlineBasketAPI', 'unlockProject'))
		{
			$unlockProjectParams = OnlineBasketAPI::unlockProject($unlockProjectParams);

			$browserLanguageCode = UtilsObj::cleanseLanguageCode($unlockProjectParams['languagecode'], $browserLanguageCode);

			$smarty = SmartyObj::newSmarty('Customer', '', '', $browserLanguageCode);

			if (count($unlockProjectParams['projectreflist']) <= TPX_ONLINE_BASKETAPI_PROJECTREFLIMIT)
			{
				if ($unlockProjectParams['result'] == '')
				{
					$unlockResult = OnlineAPI_model::unlockProject($unlockProjectParams);

					foreach ($unlockResult['projectreflist'] as &$project)
					{
						$projectRef = $project['projectref'];
						$result = $project['result'];

						$resultMessage = '';

						if ($result == TPX_ONLINE_ERROR_PROJECTDOESNOTEXIST)
						{
							$resultMessage = $smarty->get_config_vars('str_ErrorProjectDoesNotExist');
						}

						$returnArray[$projectRef]['result'] = $result;
						$returnArray[$projectRef]['resultmessage'] = $resultMessage;
					}
				}
				else
				{
					$returnArray['result'] = $unlockProjectParams['result'];
				}
			}
			else
			{
				$returnArray['result'] = TPX_ONLINE_ERROR_PROJECTREFLIMIT;
			}
		}

		OnlineAPI_view::returnResultAPI($returnArray, $browserLanguageCode, TPX_BASKETWORKFLOWTYPE_LOWLEVELAPI, '');
	}

	static function includeOnlineBasketAPI($pBasketWorkFlowType)
    {
        if ($pBasketWorkFlowType == TPX_BASKETWORKFLOWTYPE_LOWLEVELAPI)
        {
			// include external shopping cart script.
			if (file_exists('../Customise/scripts/EDL_OnlineBasketAPI.php'))
			{
				require_once('../Customise/scripts/EDL_OnlineBasketAPI.php');
			}
        }
        else
        {
			if (file_exists('../Customise/scripts/EDL_OnlineHighLevelBasketAPI.php'))
			{
				require_once('../Customise/scripts/EDL_OnlineHighLevelBasketAPI.php');
			}
        }
    }

	static function callback()
	{
        $apiCommand = '';
        $resultArray = array();

		// decrypt the post data
		$postDataArray = self::readEncryptedPostData();

		// make sure no errors occured
		if ($postDataArray['error'] == '')
		{
			$apiCommand = $postDataArray['data']['cmd'];

			$commandData = $postDataArray['data']['data'];

			// switch on the apiCommand to find out which end point to call
			switch($apiCommand)
			{
				case 'CCNOTIFICATION':
				{
					$result = self::ccNotificationDispatcher($commandData);
					OnlineAPI_view::ccNotificationDispatcher($result);
					break;
				}
				case 'CREATENEWACCOUNT':
				{
					$resultArray = self::createNewAccountFromOnline($commandData);
					OnlineAPI_view::createNewAccountFromOnline($resultArray);
					break;
				}
				case 'RESETPASSWORD':
				{
					$resultArray = self::resetPasswordFromOnline($commandData);
					OnlineAPI_view::resetPasswordFromOnline($resultArray);
					break;
				}
				case 'LOGIN':
				{
					$resultArray = self::processOnlineLogin($commandData);
					OnlineAPI_view::processOnlineLogin($resultArray);
					break;
				}
				case 'ORDER':
				{
					$resultArray = self::processOnlineOrder($commandData);
					OnlineAPI_view::processOnlineOrder($resultArray);
					break;
				}
				case 'HLLOGOUT':
				{
					$returnArray = self::highLevelOnlineDesignerLogout($commandData);
					OnlineAPI_view::onlineDesignerLogout($returnArray);
					break;
				}
				case 'SSOLOGOUT':
				{
					$returnArray = self::ssoDesignerLogout($commandData);
					OnlineAPI_view::onlineDesignerLogout($returnArray);
					break;
				}
				case 'GETBRANDASSETLOGO':
				{
					$returnData = self::getBrandAssetLogo($commandData);
					OnlineAPI_view::returnEncryptedData($returnData);
					break;
				}
				case 'GETSHAREURL':
				{
					$returnData = self::getShareURL($commandData);
					OnlineAPI_view::returnShareURL($returnData);
					break;
				}
				case 'CHANGELAYOUT':
				{
					$returnData = self::getProductListChangeProduct($commandData);
					OnlineAPI_view::returnEncryptedData($returnData);
					break;
				}
			}
		}
	}

	static function getShareURL($pData)
	{
		$returnArray = array();

		if ($pData['basketapiworkflowtype'] == TPX_BASKETWORKFLOWTYPE_LOWLEVELAPI)
		{
			$getShareURLResult = self::generateSharePreviewLink($pData['projectref'], true);
			$returnArray = array('result' => TPX_ONLINE_ERROR_NONE, 'resultmessage' => '', 'link' => $getShareURLResult['sharelink']);
		}
		else
		{
			require_once('../Share/Share_model.php');
			$getShareURLResult = Share_model::getShareOnlineProjectURL($pData);
			$returnArray = array('result' => TPX_ONLINE_ERROR_NONE, 'resultmessage' => '', 'link' => $getShareURLResult['shareurl'], 'brandcode' => $getShareURLResult['brandcode']);
		}

		return $returnArray;
	}

	static function highLevelShareProject()
	{
		$returnArray = array('result' => TPX_ONLINE_ERROR_NONE, 'resultmessage' => '', 'link' => '');

		$projectRef = UtilsObj::getPOSTParam('projectref', '');

		// validate the action before we execute it
		$validationResultArray = self::validateAPIInputParameters(self::getAPIInputParameters());
		$validationResultArray['basketapiworkflowtype'] = TPX_BASKETWORKFLOWTYPE_HIGHLEVELAPI;

		// is it safe to continue?
		if ($validationResultArray['result'] == TPX_ONLINE_ERROR_NONE)
		{
			// everything appears to be okay so proceed with the action
			$returnArray = self::getShareURL($validationResultArray);
			$brandCode = $returnArray['brandcode'];
		}
		else
		{
			$returnArray = $validationResultArray;
			$brandCode = $returnArray['brandcode'];
		}

		OnlineAPI_view::returnResultAPI($returnArray, $validationResultArray['langcode'], TPX_BASKETWORKFLOWTYPE_HIGHLEVELAPI,
		$brandCode);

	}

	// called by the ccNotification task
	// not called directly. invoked by the callback function
	static function highLevelOnlineDesignerLogout($pData)
	{
		global $gSession;
		global $gConstants;

		$returnArray = array('result' => TPX_ONLINE_ERROR_NONE, 'resultmessage' => '', 'redirecturl' => '');

		$ssoPrivateDataArray = array();
		$ssoToken = '';

        if ($gSession['ref'] > 0)
        {
			// extract the single sign-on data
			$ssoToken = $gSession['userdata']['ssotoken'];
			$ssoPrivateDataArray = $gSession['userdata']['ssoprivatedata'];
		}

		$basketRef = $pData['basketref'];
		$brandCode = $pData['brandcode'];

		if ($ssoToken == '')
		{
			$ssoToken = $pData['ssotoken'];
		}

		$deleteHighLevelUserSessionResult = OnlineAPI_model::deleteHighLevelUserSession($basketRef);

		if ($deleteHighLevelUserSessionResult == '')
		{
			$clearOnlineBasketSessionIDResult = AuthenticateObj::clearOnlineBasketSessionIDWithReason($basketRef, TPX_ONLINE_BASKETAPI_INVALIDATEBASKETREFREASON_OLLOGOUT);

			// call the single sign-out process if we have a token and the session had been started
			if (($ssoToken != '') && ($gConstants['optionwscrp']) && (file_exists('../Customise/scripts/EDL_ExternalCustomerAccount.php')))
			{
				require_once('../Customise/scripts/EDL_ExternalCustomerAccount.php');

				if (method_exists('ExternalCustomerAccountObj', 'ssoLogout'))
				{
					$ssoParamArray['reason'] = TPX_USER_LOGOUT_REASON_HIGHLEVEL_DESIGNER;
					$ssoParamArray['ssotoken'] = $ssoToken;
					$ssoParamArray['ssoprivatedata'] = $ssoPrivateDataArray;

					$redirectionURL = ExternalCustomerAccountObj::ssoLogout($ssoParamArray);
				}
			}
			else
			{
				$hl_config = UtilsObj::readWebBrandConfigFile('../config/onlinebaskethighlevelapi.conf', $brandCode);
				$brandDataArray = DatabaseObj::getBrandingFromCode($brandCode);

				if ($brandDataArray['onlinedesignerlogouturl'] != '')
				{
					$redirectionURL = $brandDataArray['onlinedesignerlogouturl'];
				}
				else
				{
					$redirectionURL = $hl_config['REDIRECTIONURL'];
				}

				// only add odlo to the redirection URL if it is missing
				if ((strpos($redirectionURL, "?odlo=") === false) && (strpos($redirectionURL, "&odlo=") === false))
				{
					$redirectionURL = UtilsObj::addURLParameter($redirectionURL, "odlo", 1);
				}

			}

			$returnArray['redirecturl'] = $redirectionURL;
		}
		else
		{
			$returnArray['result'] = -1;
			$returnArray['resultmessage'] = $deleteHighLevelUserSessionResult;
		}

		return $returnArray;
	}

	// called by the ccNotification task
	// not called directly. invoked by the callback function
	static function ssoDesignerLogout($pData)
	{
		global $gConstants;
		global $gSession;

		$browserLanguageCode = UtilsObj::cleanseLanguageCode(UtilsObj::getPOSTParam('browserlocale', ''), $gConstants['defaultlanguagecode']);

		$returnArray = array('result' => TPX_ONLINE_ERROR_NONE, 'resultmessage' => '', 'redirecturl' => '');

		$userID = $pData['userid'];
		$ssoToken = $pData['ssotoken'];

		$deletelUserSessionResult = OnlineAPI_model::deleteUserSession($userID);

		if ($deletelUserSessionResult != '')
		{
			$returnArray['result'] = TPX_ONLINE_ERROR_DATABASE;
			$returnArray['resultmessage'] = $deletelUserSessionResult;
		}

		// call the single sign-out process if we have a token and the session had been started
		if (($ssoToken != '') && ($gConstants['optionwscrp']) && (file_exists('../Customise/scripts/EDL_ExternalCustomerAccount.php')))
		{
			require_once('../Customise/scripts/EDL_ExternalCustomerAccount.php');

			if (method_exists('ExternalCustomerAccountObj', 'ssoLogout'))
			{
				$ssoParamArray['reason'] = TPX_USER_LOGOUT_REASON_USER_LOGOUT;
				$ssoParamArray['ssotoken'] = $ssoToken;
				$ssoParamArray['ssoprivatedata'] = array();

				$returnArray['redirecturl'] = ExternalCustomerAccountObj::ssoLogout($ssoParamArray);

				if ($returnArray['redirecturl'] != '')
				{
					$returnArray['redirecturl'] = UtilsObj::correctPath($returnArray['redirecturl'], '/', true);
				}
			}
		}

		return $returnArray;

	}

	// called by the ccNotification task
	// not called directly. invoked by the callback function
	// moved from AppAPI
    static function processOnlineOrder($pShoppingCartData)
    {
        global $gSession;

        $orderDataResult = $pShoppingCartData['data'];

        $userID = $orderDataResult['headerarray']['userid'];
        $basketAPIWorkFlowType = $orderDataResult['headerarray']['basketapiworkflowtype'];

        // set the onlineclienttime session variable from toapixonline
        // this will force any calls to the maweb which go through order.initialise to use this value rather than the
        // one in the cookie. this is becasue the cookie one might be too old.
        $gSession['onlineclienttime'] = $pShoppingCartData['onlineclienttime'];

        $userDataArray = DatabaseObj::getUserAccountFromID($userID);

        // Although session data has not been created at this point we need to initialise userAddressUpdated & userid
        // so they can be passed to the external shopping cart.
        $gSession['useraddressupdated'] = $userDataArray['addressupdated'];
        $gSession['userid'] = $userID;

        // project ref needs to be initialised here so it will be inserted into SESSIONDATA (used to determine if a project is in shopping cart)
        $gSession['projectref'] = $orderDataResult['cartarray'][0]['projectref'];

		$gSession['browserlanguagecode'] = $orderDataResult['headerarray']['languagecode'];

		// we don't need to reauthenticate once we are taken to the shopping cart from online
		$gSession['authenticatecookie'] = 0;

		require_once('../AppAPI/AppAPI_model.php');
        $resultArray = AppAPI_model::order($orderDataResult);

		if (($basketAPIWorkFlowType == TPX_BASKETWORKFLOWTYPE_HIGHLEVELAPI) && ($resultArray['result'] == 'ORDER'))
		{
			$orderDataResult['headerarray']['batchref'] = $resultArray['batchref'];

			$cartDataArray = array();
			$cartDataArray = $orderDataResult['headerarray'];
			$cartDataArray['items'] = $resultArray['items'];

			// add the project data to the project record in the onlinebasket table and set inbasket flag.
			$updateProjectInOnlineBasketResult = OnlineAPI_model::updateProjectInOnlineBasket($orderDataResult['cartarray'][0]['projectref'], $userID, $cartDataArray, $orderDataResult['cartarray'][0]['projectname']);
		}
        elseif (($basketAPIWorkFlowType == TPX_BASKETWORKFLOWTYPE_LOWLEVELAPI) && (stripos($_SERVER['HTTP_USER_AGENT'], 'TPXWebView') !== false))
        {
            $cartDataArray = array();
            $cartDataArray = $orderDataResult['headerarray'];
            $cartDataArray['items'] = $resultArray['items'];

            // update project order data cache with data from online
            $updateProjectInCacheResult = DatabaseObj::addProjectOrderDataCache($cartDataArray, []);
        }

        // Only create order session for Taopix shopping cart because session
        // for the external shopping cart should have been created at this point
        // this is also attaching the user to the order session which we creaeted in the order function previously in this function
        if (($resultArray['shoppingcarttype'] == TPX_SHOPPINGCARTTYPE_INTERNAL) &&
			(($basketAPIWorkFlowType == TPX_BASKETWORKFLOWTYPE_NORMAL) || ($basketAPIWorkFlowType == TPX_BASKETWORKFLOWTYPE_HIGHLEVELCHECKOUT)
			|| ($basketAPIWorkFlowType == TPX_BASKETWORKFLOWTYPE_LOWLEVELAPI)))
        {
			DatabaseObj::startSession($userID, $userDataArray['login'], $userDataArray['contactfirstname'] . ' ' . $userDataArray['contactlastname'],
            	TPX_LOGIN_CUSTOMER, $userDataArray['companycode'], $userDataArray['owner'], $userDataArray['webbrandcode'],  $userDataArray['groupcode'],
                $orderDataResult['headerarray']['ssotoken'], $orderDataResult['headerarray']['ssoprivatedata']);


            if (($orderDataResult['headerarray']['ssotoken'] != '') || (count($orderDataResult['headerarray']['ssoprivatedata']) > 0))
            {
                // only update the session data in the database if there is some sso data to update
                DatabaseObj::updateSession();
            }

        }

        return $resultArray;
    }

	// called by the ccNotification task
	// not called directly. invoked by the callback function
	static function ccNotificationDispatcher($pData)
	{
		$successfullNotificationRecordIDs = '';

		$formattedNotificationArray = array();

		for ($i = 0; $i < count($pData); $i++)
		{
			$notification = $pData[$i];

			$target = $notification['target'];

			if (! array_key_exists($target, $formattedNotificationArray))
			{
				$formattedNotificationArray[$target] = array();
			}

			$formattedNotificationArray[$target][] = $notification;
		}

		$successfulNotifications = array(
			'success' => array(),
			'fail' => array()
		);

		foreach ($formattedNotificationArray as $target => $notificationArray)
		{
			switch ($target)
			{
				case TPX_CCNOTIFICATION_TARGET_PURGE:
				{
					$purgeSuccessfulNotificationsArray = array();

					foreach ($notificationArray as $notification)
					{
						switch ($notification['action'])
						{
							case TPX_CCNOTIFICATION_ACTION_SETREORDERSTATE:
							{
								OnlineAPI_model::updateReorderState($notification['data']['reorder'], $notification['data']['projects']);

								$purgeSuccessfulNotificationsArray[] = $notification['id'];
								break;
							}
							case TPX_CCNOTIFICATION_ACTION_DELOLBASKETENTRY:
							{
								OnlineAPI_model::deleteOnlineBasketData($notification['data']['projects']);

								$purgeSuccessfulNotificationsArray[] = $notification['id'];
								break;
							}
							case TPX_CCNOTIFICATION_ACTION_CRTFLAGPROJEMAILS:
							{
								OnlineAPI_model::createFlaggedProjectEmails($notification['data']);

								$purgeSuccessfulNotificationsArray[] = $notification['id'];
								break;
							}
						}
					}

					// Merging into success subarray as $purgeSuccessfulNotificationsArray are to be deleted and that's what success is used for in online
					$successfulNotifications['success'] = array_merge($successfulNotifications['success'], $purgeSuccessfulNotificationsArray);

					break;
				}
				case TPX_CCNOTIFICATION_TARGET_HLAPI:
				{
                    $updateOnlineBasketResultArray = OnlineAPI_model::updateOnlineProjectsBasketData($notificationArray);

					$successfulNotifications = array_merge($successfulNotifications, $updateOnlineBasketResultArray);
					break;
				}
				case TPX_CCNOTIFICATION_TARGET_LLAPI:
				{
					$updateOnlineBasketResultArray = OnlineAPI_model::logLowLevelNotifcationTask($notificationArray);

					// Merging into success subarray as $updateOnlineBasketResultArray are to be deleted and that's what success is used for in online
					$successfulNotifications['success'] = array_merge($successfulNotifications['success'], $updateOnlineBasketResultArray);
					break;
				}
				case TPX_CCNOTIFICATION_TARGET_DBCONVPROC:
				{
					foreach ($notificationArray as $notification)
					{
						switch ($notification['action'])
						{
							case TPX_CCNOTIFICATION_ACTION_DBCONVPROCIMPORT:
							{
								$serverTimeOffset = strtotime(DatabaseObj::getServerTime()) - time();
								$runTime = time() + $serverTimeOffset;
								$nextRunTime = date('Y-m-d H:i:s', ($runTime + 60));
								$eventStatusCode = 2;

								$eventID = $notification['data']['eventid'];
								$errorText = $notification['data']['error'];

								if ($errorText == '')
								{
									$eventStatusCode = 2;
								}
								else
								{
									$errorText = "en " . $errorText . " Error Code: " . $notification['data']['errorcode'];
									$eventStatusCode = 1;
								}

								DatabaseObj::updateEvent($eventID, date('Y-m-d H:i:s', $runTime), $nextRunTime, $eventStatusCode, $errorText);

								$purgeSuccessfulNotificationsArray[] = $notification['id'];
								break;
							}
						}
					}

					// Merging into success subarray as $purgeSuccessfulNotificationsArray are to be deleted and that's what success is used for in online
					$successfulNotifications['success'] = array_merge($successfulNotifications['success'], $purgeSuccessfulNotificationsArray);
					break;
				}
			}
		}

		$successfullNotificationRecordIDs = json_encode($successfulNotifications);

		return $successfullNotificationRecordIDs;
	}

	// called by the ccNotification task
	// not called directly. invoked by the callback function
	static function createNewAccountFromOnline($pData)
	{
		return OnlineAPI_model::createNewOnlineAccount($pData);
	}

	// called by the ccNotification task
	// not called directly. invoked by the callback function
	static function resetPasswordFromOnline($pData)
	{
		$login = $pData['login'];
		$webBrandCode = $pData['webbrandcode'];
		$passwordFormat = $pData['format'];

		return OnlineAPI_model::resetPasswordRequest($webBrandCode, $login, $passwordFormat);
	}

	// called by the ccNotification task
	// not called directly. invoked by the callback function
	static function processOnlineLogin($pData)
	{
        return OnlineAPI_model::processOnlineLogin($pData, false);
	}

	static function queryOrderStatus()
	{
		global $gConstants;

		$returnArray = array();

		$browserLanguageCode = UtilsObj::cleanseLanguageCode(UtilsObj::getGETParam('l', ''), $gConstants['defaultlanguagecode']);

		$queryOrderStatusParams = array();
		$queryOrderStatusParams['result'] = '';
 		$queryOrderStatusParams['languagecode'] = $browserLanguageCode;
		$queryOrderStatusParams['projectreflist'] = array();

		self::includeOnlineBasketAPI(TPX_BASKETWORKFLOWTYPE_LOWLEVELAPI);

		if (method_exists('OnlineBasketAPI', 'queryOrderStatus'))
		{
			$queryOrderStatusParams = OnlineBasketAPI::queryOrderStatus($queryOrderStatusParams);

			$browserLanguageCode = UtilsObj::cleanseLanguageCode($queryOrderStatusParams['languagecode'], $browserLanguageCode);

			if ($queryOrderStatusParams['result'] == '')
			{
				$smarty = SmartyObj::newSmarty('Customer', '', '', $browserLanguageCode);

				if (count($queryOrderStatusParams['projectreflist']) <= TPX_ONLINE_BASKETAPI_PROJECTREFLIMIT)
				{
					$doesNotExistMessage = $smarty->get_config_vars('str_ErrorProjectDoesNotExist');
					$queryOrderStatusResult = OnlineAPI_model::queryOrderStatus($queryOrderStatusParams);

					foreach ($queryOrderStatusResult['projectreflist'] as &$project)
					{
						$projectRef = $project['projectref'];
						$result = $project['result'];
						$resultMessage = '';
						$status = '';

						switch ($project['orderstatus'])
						{
							case 0:
							case 1:
							case 3:
							{
								$status = 'str_LabelStatusInProduction';
								break;
							}
							case 2:
							{
								$status = 'str_LabelStatusShipped';
								break;
							}
							case 4:
							{
								$status = 'str_LabelStatusReadyToCollectAtStore';
								break;
							}
							case 5:
							{
								$status = 'str_LabelStatusCompleted';
								break;
							}
						}

						$status = $smarty->get_config_vars($status);

						if ($result == TPX_ONLINE_ERROR_PROJECTDOESNOTEXIST)
						{
							$resultMessage = $doesNotExistMessage;
							$status = '';
						}

						$returnArray[$projectRef]['result'] = $result;
						$returnArray[$projectRef]['resultmessage'] = $resultMessage;
						$returnArray[$projectRef]['canedit'] = $project['canedit'];
						$returnArray[$projectRef]['status'] = $status;
					}
				}
				else
				{
					$returnArray['result'] = TPX_ONLINE_ERROR_PROJECTREFLIMIT;
				}
			}
			else
			{
				$returnArray['result'] = $queryOrderStatusParams['result'];
			}
		}

		OnlineAPI_view::returnResultAPI($returnArray, $browserLanguageCode, TPX_BASKETWORKFLOWTYPE_LOWLEVELAPI, '');
	}

	static function reorder()
	{
		global $gConstants;

		$returnArray = array('result' => TPX_ONLINE_ERROR_NONE, 'resultmessage' => '');

		$browserLanguageCode = UtilsObj::cleanseLanguageCode(UtilsObj::getGETParam('l', ''), $gConstants['defaultlanguagecode']);

		$reorderParams = array();
		$reorderParams['result'] = '';
 		$reorderParams['languagecode'] = $browserLanguageCode;
		$reorderParams['projectref'] = '';
		$reorderParams['orderitemid'] = '';

		self::includeOnlineBasketAPI(TPX_BASKETWORKFLOWTYPE_LOWLEVELAPI);

		if (method_exists('OnlineBasketAPI', 'prepareReorderInit'))
		{
			$reorderParams = OnlineBasketAPI::prepareReorderInit($reorderParams);

			$browserLanguageCode = UtilsObj::cleanseLanguageCode($reorderParams['languagecode'], $browserLanguageCode);

			if ($reorderParams['result'] == '')
			{
				$matchesCount = preg_match('/^[0-9]+[_]{1}[0-9]+$/', $reorderParams['projectref']);

				if ($matchesCount > 0)
				{
					$smarty = SmartyObj::newSmarty('Customer', '', '', $browserLanguageCode);

					$reorderResult = OnlineAPI_model::reorder($reorderParams);

					if ($reorderResult['result'] != '')
					{
						if ($reorderResult['result'] == 'CUSTOMERROR')
						{
							$returnArray['result'] = TPX_ONLINE_ERROR_EDL_SCRIPT_RETURNERROR;
							$returnArray['resultmessage'] = $reorderResult['resultparam'];
						}
						else
						{
							$smarty = SmartyObj::newSmarty('Share','', '');
							SmartyObj::replaceParams($smarty, $reorderResult['result'], $reorderResult['resultparam']);

							$returnArray['result'] = TPX_ONLINE_ERROR_PRODUCT_CONFIGURATION;
							$returnArray['resultmessage'] = $smarty->get_template_vars($reorderResult['result']);
						}
					}
				}
				else
				{
					$returnArray['result'] = TPX_ONLINE_ERROR_PROJECTDOESNOTEXIST;
				}
			}
			else
			{
				$returnArray['result'] = $reorderParams['result'];
			}
		}

		OnlineAPI_view::returnResultAPI($returnArray, $browserLanguageCode, TPX_BASKETWORKFLOWTYPE_LOWLEVELAPI, '');
	}


	static function readEncryptedPostData()
	{
		$returnArray = array();
		$returnArray['data'] = '';
		$returnArray['error'] = '';
		$returnArray['config'] = array();

		// decode the tenantKey
		$key = UtilsObj::readKeyFromPOST();

		$systemConfigData = DatabaseObj::getSystemConfig();

		// make sure the call is for the correct tenant/system
		if ($systemConfigData['key'] == $key)
    	{
			$returnArray['data'] = unserialize(UtilsObj::decryptData(UtilsObj::getPOSTParam('data', ''), $systemConfigData['secret'], false));

			$returnArray['config'] = $systemConfigData;
		}
		else
		{
			$returnArray['error'] = 'Invalid Key: ' . $key;
		}

		return $returnArray;
	}

	static function determinePerfectlyClearAutomationMode($pLicenseKeyArray, $pBrandingArray)
	{
		// first check the licensekey. If use default is selected take the settings from the brand.

		$automaticallyApplyPerfectlyClearMode = TPX_AUTOMATICALLYAPPLYPERFECTLYCLEAR_MODE_OFF;

		$settingsArray = $pLicenseKeyArray;

		if ($settingsArray['usedefaultautomaticallyapplyperfectlyclear'] == 1)
		{
			$settingsArray = $pBrandingArray;
		}

		if ($settingsArray['automaticallyapplyperfectlyclear'] == 1)
		{
			$automaticallyApplyPerfectlyClearMode = TPX_AUTOMATICALLYAPPLYPERFECTLYCLEAR_MODE_ON;

			if ($settingsArray['allowuserstotoggleperfectlyclear'] == 1)
			{
				$automaticallyApplyPerfectlyClearMode = TPX_AUTOMATICALLYAPPLYPERFECTLYCLEAR_MODE_TOGGLE;
			}
		}

		return $automaticallyApplyPerfectlyClearMode;
	}

	static function checkDataAvailable()
	{
		global $gConstants;

		$returnArray = array();

		$returnArray['result'] = 0;
		$returnArray['resultmessage'] = '';
		$returnArray['orders'] = array();

		$dataAvailable = false;
		$resultMessage = '';
		$result = 0;

		// find the browser lang code
		$browserLanguageCode = UtilsObj::cleanseLanguageCode(UtilsObj::getGETParam('l', ''), $gConstants['defaultlanguagecode']);

		// set the params to pass to the licensees checkDataAvailable endpoint
		$checkDataAvailableParams = array();
		$checkDataAvailableParams['result'] = '';
 		$checkDataAvailableParams['languagecode'] = $browserLanguageCode;
		$checkDataAvailableParams['orderitemids'] = array();

		// incude the online basket API library files
		self::includeOnlineBasketAPI(TPX_BASKETWORKFLOWTYPE_LOWLEVELAPI);

		// make sure the checkDataAvailable function exists in the licensses script
		if (method_exists('OnlineBasketAPI', 'checkDataAvailable'))
		{
			// call the licensees function
			$checkDataAvailableParams = OnlineBasketAPI::checkDataAvailable($checkDataAvailableParams);

			// get the corrected browser language code
			$browserLanguageCode = UtilsObj::cleanseLanguageCode($checkDataAvailableParams['languagecode'], $browserLanguageCode);

			// make sure there has been no errors with the liencess call
			if ($checkDataAvailableParams['result'] == '')
			{
				// call the taopix checkDataAvailable method to query the database
				$checkDataAvailableResult = OnlineAPI_model::checkDataAvailable($checkDataAvailableParams);

				if ($checkDataAvailableResult['result'] == TPX_ONLINE_ERROR_NONE)
				{

					// use smarty to get the error message regarding the missing order
					$smarty = SmartyObj::newSmarty('Customer', '', '', $browserLanguageCode);
					$doesNotExistMessage = $smarty->get_config_vars('str_ErrorOrderDoesNotExist');

					$returnArray['orders'] = array_map(function($pOrder) use ($doesNotExistMessage)
					{
						$resultMessage = "";

						$result = $pOrder['result'];

						if ($result == TPX_ONLINE_ERROR_ORDERDOESNOTEXIST)
						{
							$resultMessage = $doesNotExistMessage;
						}

						return array( 	'orderitemid' => (int) $pOrder['orderitemid'],
										'dataavailable' => $pOrder['dataavailable'],
										'result' => $result,
										'resultmessage' => $resultMessage);
					}, $checkDataAvailableResult['orders']);
				}
				else
				{
					$returnArray['result'] = $checkDataAvailableResult['result'];
				}
			}
			else
			{
				$returnArray['result'] = $checkDataAvailableParams['result'];
			}
		}

		OnlineAPI_view::returnResultAPI($returnArray, $browserLanguageCode, TPX_BASKETWORKFLOWTYPE_LOWLEVELAPI, '');
	}

	/**
	 * Find a branded asset to use as the online logo.
	 *
	 * @param array $pCommandData
	 * @return array
	 */
	static function getBrandAssetLogo($pCommandData)
	{
		$brandCode = UtilsObj::getArrayParam($pCommandData, 'webbrandcode', '');

		return array('img' => self::getBrandAsset($brandCode, TPX_BRANDING_FILE_TYPE_OL_LOGO));
	}

	/**
	 * Find a branded asset for use with online.
	 *
	 * @global array $ac_config
	 * @param string $pBrandCode
	 * @param integer $pAssetTypeRef
	 * @return string
	 */
	static function getBrandAsset($pBrandCode, $pAssetTypeRef)
	{
		$brandAssetInfo = '';

		$brandingArray = DatabaseObj::getBrandingFromCode($pBrandCode);

		$brandFileInfo = DatabaseObj::getBrandAssetData($brandingArray['id'], $pAssetTypeRef, true);

		// Get the logo url to use in the online designer.
		if ($brandFileInfo['result'] == '')
		{
			if ($brandFileInfo['data']['id'] == 0)
			{
				// Default files found
				$brandAssetInfo = $brandFileInfo['data']['path'];
			}
			else
			{
				global $ac_config;

				// Customised files found.
				$brandAssetInfo = UtilsObj::correctPath($ac_config['WEBURL'], '/', true) . 'brandassets/images/' . $brandFileInfo['data']['path'];
			}
		}
		else
		{
			$brandAssetInfo = '';
		}

		return $brandAssetInfo;
	}

	static function getSSOLLPrivateData()
	{
		$returnData = array('ssoprivatedata'=>array(), 'authkey' => '');
		// Read the Low Level SSO cookie. This acts as a session to the view project list request. This is so that there is a way of storing the
		// SSO private data. Without this the licensee will not be able to persist their token data
		$cookieReadArray = AuthenticateObj::readSSOLLCookie();

		// If there is a cookie value lookup the record in the database
		if ($cookieReadArray['cookievalue'] != '')
		{
			// Extract the auth key from the cookie
			$returnData['authkey'] = $cookieReadArray['cookievalue'];

			// Get the record out of the database
			$authenticationRecord = AuthenticateObj::getAuthenticationDataRecord(TPX_AUTHENTICATIONTYPE_LOWLEVEL, $returnData['authkey'], false);

			// Read the data from the database into the private data field if found
			// If it is not found then remove the cookie because it is invalid
			if ($authenticationRecord['found'])
			{
				$returnData['ssoprivatedata'] = $authenticationRecord['data'];
			}
			else
			{
				AuthenticateObj::removeSSOLLCookie();
			}
		}

		return $returnData;
	}

	static function updateSSOLLPrivateData($pSSOPrivateData, $pAuthKey)
	{
		// If the function has returned some sso private data then we need to eith insert or update the database with it
		if (!empty($pSSOPrivateData))
		{
			// If there is no auth key then we need to insert the record and create the cookie
			// Else we update the record
			if ($pAuthKey == '')
			{
				$authenticationInsertArray = AuthenticateObj::createDataStoreRecord($pSSOPrivateData,'','',TPX_AUTHENTICATIONTYPE_LOWLEVEL, TPX_USER_AUTH_REASON_LOWLEVEL_SSO, 0, false);

				if ($authenticationInsertArray['result'] == '')
				{
					setcookie(TPX_SSO_LL_COOKIE_NAME, $authenticationInsertArray['authkey'], 0, '/', '', UtilsObj::needSecureCookies());
				}
			}
			else
			{
				$authenticationInsertArray = AuthenticateObj::updateAuthenticationRecordData($pAuthKey, $pSSOPrivateData);
			}
		}
	}

	/**
	 * Endpoint for showing the project list for low level API only.
	 *
	 * @global array $gConstants
	 * @return void
	 */
	static function usersProjectList($pMode)
	{
		global $gConstants;

		// The result array which will be passed to the view
		$resultArray = array(
			'result' => TPX_ONLINE_ERROR_NONE,
			'resultmessage' => '',
			'returntext' => '',
			'count' => 0,
			'languagecode' => '',
			'onlinedesignerurl' => '',
			'template' => '',
			'templateparams' => [],
			'projects' => array());

		// Add the low level API EDL script
		self::includeOnlineBasketAPI(TPX_BASKETWORKFLOWTYPE_LOWLEVELAPI);

		if (method_exists('OnlineBasketAPI', 'usersProjectListInit'))
		{
			$userAccount = array();

			// An array to store the brand data in
			$brandingArray = array();

			$ssoLLPrivateData = array();

			// Determine the browser language
			$resultArray['languagecode'] = UtilsObj::cleanseLanguageCode(UtilsObj::getGETParam('l', ''), $gConstants['defaultlanguagecode']);

			// If there is language on the parameter attempt to get it from the browser
			if ($resultArray['languagecode'] == '')
			{
				$resultArray['languagecode'] = UtilsObj::getBrowserLocale();
			}

			// Set up the parameters to pass to the EDL script
			$edlParamArray = array();
			$edlParamArray['mode'] = $pMode;
			$edlParamArray['returntext'] = '';
			$edlParamArray['languagecode'] = $resultArray['languagecode'];
			$edlParamArray['userid'] = -1;
			$edlParamArray['accountcode'] = '';
			$edlParamArray['allowedorigins'] = '';
			$edlParamArray['ssoprivatedata'] = array();
			$edlParamArray['template'] = '';
			$edlParamArray['templateparams'] = [];

			$brandCode = '';

			if ($pMode == TPX_USER_PROJECT_LIST_MODE_VIEW)
			{
				// populate the sso private data from the record in the database
				$ssoLLPrivateData = self::getSSOLLPrivateData();

				$edlParamArray['ssoprivatedata'] = $ssoLLPrivateData['ssoprivatedata'];
			}

			// Invoke the EDL view project list function
			$usersProjectListInitArray = OnlineBasketAPI::usersProjectListInit($edlParamArray);

			$resultArray['languagecode'] = $usersProjectListInitArray['languagecode'];
			$resultArray['returntext'] = $usersProjectListInitArray['returntext'];

			if ($pMode == TPX_USER_PROJECT_LIST_MODE_VIEW)
			{
				self::updateSSOLLPrivateData($usersProjectListInitArray['ssoprivatedata'], $ssoLLPrivateData['authkey']);
			}

            $userID = $usersProjectListInitArray['userid'];

            if ($pMode == TPX_USER_PROJECT_LIST_MODE_GET && isset($_GET['authkey']) && $userID == -1){

                 // clean up any authentication data records
                AuthenticateObj::deleteAuthenticationDataRecords();

                $authenticationRecord = AuthenticateObj::getAuthenticationDataRecord(TPX_AUTHENTICATIONTYPE_LOWLEVEL, $_GET['authkey'], true);

                if ($authenticationRecord['found'])
                {
                    $userID = $authenticationRecord['ref'];
                }
                else {
                	$resultArray['result'] = TPX_ONLINE_ERROR_LOWLEVELINTERNALERROR;
                }
            }


			// if there is no Taopix user id provided then attempt to get the account with the accountcode
			if ($userID == -1)
			{
				if ($usersProjectListInitArray['accountcode'] != '')
				{
					// get the users account with the account code
					// we need the account so that we can get the user id and brandcode
					$userAccount = DatabaseObj::getUserAccountFromAccountCode($usersProjectListInitArray['accountcode']);

					if ($userAccount['result'] == '')
					{
						$userID = $userAccount['recordid'];
					}
					else
					{
						$resultArray['result'] = TPX_ONLINE_ERROR_INVALIDUSERID;
					}
				}
				else
				{
					$resultArray['result'] = TPX_ONLINE_ERROR_INVALIDUSERID;
				}
			}
			else
			{
				$userAccount = DatabaseObj::getUserAccountFromID($userID);

				if ($userAccount['result'] != '')
				{
					$resultArray['result'] = TPX_ONLINE_ERROR_INVALIDUSERID;
				}
			}

			if (($userID != -1) && ($resultArray['result'] == TPX_ONLINE_ERROR_NONE))
			{
				require_once(__DIR__.'/../Customer/Customer_model.php');

				$brandingArray = DatabaseObj::getBrandingFromCode($userAccount['webbrandcode']);
				$brandCode = $userAccount['webbrandcode'];
				$resultArray['onlinedesignerurl'] = UtilsObj::correctPath($brandingArray['onlinedesignerurl'], "/", false);

				$projectListArray = Customer_model::getOnlineProjectList($userID, ($pMode == TPX_USER_PROJECT_LIST_MODE_GET));

				// make sure there is no error with the call to get the projects list
				if ($projectListArray['error'] == '')
				{
					// massage the data from getOnlineProjectList so that the recordid, statusdescription and cancompleteorder are removed
					// also add the onlinedesignerurl to the thumbnail path
					$projectListArray['projects'] = array_map(function($pItem) use ($resultArray, $pMode)
					{
						$returnItem = array_filter($pItem, function($pValue, $pKey)
						{
							return !in_array($pKey, ["recordid", "statusdescription", "cancompleteorder"]);
						}, ARRAY_FILTER_USE_BOTH);

						return $returnItem;

					}, $projectListArray['projects']);

					$resultArray['projects'] = $projectListArray['projects'];

					if (method_exists('OnlineBasketAPI', 'usersProjectList'))
					{
						$resultArray['projects'] = OnlineBasketAPI::usersProjectList($resultArray['projects']);
					}

					$resultArray['count'] = count($resultArray['projects']);
				}
				else
				{
					$resultArray['result'] = TPX_ONLINE_ERROR_DATABASE;
				}
			}
			else
			{
				if ($userID != -1)
				{
					$resultArray['result'] = TPX_ONLINE_ERROR_INVALIDUSERID;
				}
			}

			if ($pMode == TPX_USER_PROJECT_LIST_MODE_VIEW)
			{
				// clean up any authentication data records
				AuthenticateObj::deleteAuthenticationDataRecords();
			}
		}

		if ($pMode == TPX_USER_PROJECT_LIST_MODE_VIEW)
		{
			$resultArray['template'] = $usersProjectListInitArray['template'];
			$resultArray['templateparams'] = $usersProjectListInitArray['templateparams'];

			// Invoke the view to display the projects list
			OnlineAPI_view::usersProjectList($resultArray, $pMode, $brandCode);
		}
		else
		{
			// Return the data formatted for an API call
			OnlineAPI_view::returnResultAPI($resultArray, $resultArray['languagecode'], TPX_BASKETWORKFLOWTYPE_LOWLEVELAPI, $brandCode);
		}
	}

	/**
	 * Endpoint for leaving project list for low level API only.
	 *
	 * @return void
	 */
	static function leaveUsersProjectList()
	{
		// Include the low level API EDL script
		self::includeOnlineBasketAPI(TPX_BASKETWORKFLOWTYPE_LOWLEVELAPI);

		if (method_exists('OnlineBasketAPI', 'leaveUsersProjectList'))
		{
			$leaveProjectParamArray = array();
			$leaveProjectParamArray['redirecturl'] = "";
			$leaveProjectParamArray['ssoprivatedata'] = array();

			$cookieeReadArray = AuthenticateObj::readSSOLLCookie();

			// Use Low Level API SSO cookie to lookup the authentication record
			if ($cookieeReadArray['cookievalue'] != '')
			{
				$authKey = $cookieeReadArray['cookievalue'];

				$authenticationRecord = AuthenticateObj::getAuthenticationDataRecord(TPX_AUTHENTICATIONTYPE_LOWLEVEL, $authKey, false);

				if ($authenticationRecord['result'] == '')
				{
					$leaveProjectParamArray['ssoprivatedata'] = $authenticationRecord['data'];
				}
			}

			// Invoke the leaveProjectList function with the parameters we have set up
			$leaveProjectListArray = OnlineBasketAPI::leaveUsersProjectList($leaveProjectParamArray);

			// Update the database with sso private data changes
			if (! empty($leaveProjectListArray['ssoprivatedata']))
			{
				if ($authKey == '')
				{
					$authenticationInsertArray = AuthenticateObj::createDataStoreRecord($leaveProjectListArray['ssoprivatedata'],'','',TPX_AUTHENTICATIONTYPE_LOWLEVEL, TPX_USER_AUTH_REASON_LOWLEVEL_SSO, 0, false);

					if ($authenticationInsertArray['result'] == '')
					{
						setcookie(TPX_SSO_LL_COOKIE_NAME, $authenticationInsertArray['authkey'], 0, '/', '', UtilsObj::needSecureCookies());
					}
				}
				else
				{
					$authenticationInsertArray = AuthenticateObj::updateAuthenticationRecordData($authKey, $leaveProjectListArray['ssoprivatedata']);
				}
			}

			// if the redirect URL is set then redirect to it.
			if ($leaveProjectListArray['redirecturl'] != '')
			{
				OnlineAPI_view::redirect($leaveProjectListArray['redirecturl']);
			}
		}
	}

	/**
	* End point that generates the share preview link.
	*
	* @global $gConstants Global constants array.
	*/
	static function generateSharePreviewLink($pProjectRef = '', $pRequestedFromOnlineDesigner = false)
	{
		global $gConstants;

		$resultArray = array('result' => '', 'resultmessage' => '', 'sharelink' => '');
		$projectRef = UtilsObj::getGETParam('projectref', $pProjectRef);
		$languageCode = UtilsObj::getGETParam('l', $gConstants['defaultlanguagecode']);

		if ($languageCode == '')
		{
			$languageCode = UtilsObj::getBrowserLocale();
		}

		if ($projectRef != '')
		{
			if (file_exists('../Customise/scripts/EDL_OnlineBasketAPI.php'))
			{
				require_once('../Customise/scripts/EDL_OnlineBasketAPI.php');

				if (method_exists('OnlineBasketAPI', 'generateSharePreviewLink'))
				{
					$shareData = array();
					$shareData['sharelink'] = '';
					$shareData['projectref'] = $projectRef;

					$generateSharePreviewLinkResult = OnlineBasketAPI::generateSharePreviewLink($shareData);

					if (array_key_exists('sharelink', $generateSharePreviewLinkResult))
					{
						$resultArray['sharelink'] = $generateSharePreviewLinkResult['sharelink'];
					}
				}
			}
		}
		else
		{
			$resultArray['result'] = TPX_ONLINE_ERROR_PROJECTDOESNOTEXIST;
		}

		if ($pRequestedFromOnlineDesigner)
		{
			return $resultArray;
		}
		else
		{
			OnlineAPI_view::returnResultAPI($resultArray, $languageCode, TPX_BASKETWORKFLOWTYPE_LOWLEVELAPI, '');
		}
	}


	/**
	* Function to clean up any old mawebdata cookies
	* that are left around after a user has previously logged in via multiline.
	* This is to prevent a build up of cookies that could reach the header field server limit
	*
	*/
	static function cleanUpMAWebCookies()
	{
		foreach ($_COOKIE as $cookie => $value)
		{
			if (substr($cookie, 0, 9) == 'mawebdata')
			{
				setcookie($cookie, '', 1, '/', '', UtilsObj::needSecureCookies(), true);
			}
		}
	}

	static function highLevelKeepProject()
	{
		require_once '../AjaxAPI/AjaxAPI_model.php';
		global $ac_config;

		$brandCode = '';
		$inputParamArray = self::getAPIInputParameters();

		$browserLanguageCode = $inputParamArray['langcode'];
		$brandCode = UtilsObj::getGlobalValue('gDefaultSiteBrandingCode', '');

		$result = AjaxAPI_Model::keepOnlineProject($inputParamArray['projectref'], $ac_config);

		if ($result['status'])
		{
			$local = OnlineAPI_model::keepOnlineProject($inputParamArray['projectref'], DatabaseObj::getGlobalDBConnection());
		}

		OnlineAPI_view::returnResultAPI($result, $browserLanguageCode, TPX_BASKETWORKFLOWTYPE_HIGHLEVELAPI, $brandCode);
	}

	/**
	* End point that generates the product data when changing layout in online
	*
	* @array $pData command data from online
	* @return array containing product data to return to online
	*/
	static function getProductListChangeProduct($pData)
	{
		$returnArray = array();
		$returnArray['result'] = '';
		$returnArray['autoupdatelist'] = [];

		$productCollectionCode = $pData['productcollectioncode'];
		$productLayoutCode = $pData['productlayoutcode'];
		$brandCode = $pData['brandcode'];
		$companyCode = $pData['companycode'];
		$groupCode = $pData['groupcode'];
		$productTreesData = $pData['producttreesdata'];

		$autoupdatelist = OnlineAPI_model::buildAutoUpdateProductList($productCollectionCode, $productLayoutCode, $groupCode, $brandCode, $companyCode, $productTreesData);

		//$returnArray['data']['result'] ERROR
		$returnArray['autoupdatelist'] = $autoupdatelist;
		return $returnArray;
	}

	static function recursiveDiff(Array $array1, Array $array2): Array
    {
        $result = array();
        foreach($array1 as $key => $val) {
            if(is_array($val) && isset($array2[$key])) {
                $tmp = self::recursiveDiff($val, $array2[$key]);
                if($tmp) {
                    $result[$key] = $tmp;
                }
            }
            elseif(!isset($array2[$key])) {
                $result[$key] = null;
            }
            elseif($val !== $array2[$key]) {
                $result[$key] = $array2[$key];
            }

            if(isset($array2[$key])) {
                unset($array2[$key]);
            }
        }
        return array_merge($result, $array2);
    }

    static function getWebViewCartItem()
    {
        global $gConstants;

        $languageCode = UtilsObj::getGETParam('l', $gConstants['defaultlanguagecode']);
        $authKey = UtilsObj::getPOSTParam('authkey');
        $projectRef = UtilsObj::getPOSTParam('projectref');

        $resultArray = array('result' => TPX_ONLINE_ERROR_NONE, 'resultmessage' => '', 'project' => []);

        // clean up any authentication data records
        AuthenticateObj::deleteAuthenticationDataRecords();

        $authenticationRecord = AuthenticateObj::getAuthenticationDataRecord(TPX_AUTHENTICATIONTYPE_LOWLEVEL, $authKey, true);

        if ($authenticationRecord['found'])
        {
            $userAccountID = $authenticationRecord['ref'];
            $project = OnlineAPI_model::getWebViewCartItemFromOrderDataCache($projectRef);

            if ($project['error'] == TPX_ONLINE_ERROR_NONE)
            {
                $resultArray['project']['projectref'] = $projectRef;
                $resultArray['project']['name'] = $project['project']['items'][0]['projectname'];
                $resultArray['project']['productname'] = LocalizationObj::getLocaleString($project['project']['items'][0]['productname'], '', true);
                $resultArray['project']['projectpreviewthumbnail'] = $project['project']['items'][0]['projectpreviewthumbnail'];
            }
            else{
                $resultArray['result'] = $project['error'];
                $resultArray['resultmessage'] = $project['errormessage'];
            }
        }
        else {
            $resultArray['result'] = TPX_ONLINE_ERROR_LOWLEVELINTERNALERROR;
        }

        OnlineAPI_view::returnJSON($resultArray, TPX_BASKETWORKFLOWTYPE_LOWLEVELAPI, '');
    }

    static function webViewCheckout()
    {
        global $gSession;
        global $gConstants;

        $languageCode = UtilsObj::getGETParam('l', $gConstants['defaultlanguagecode']);
        $cartData = json_decode($_POST['cartdata'], true);
        $authKey = $cartData['authkey'];
        $projectRefs = $cartData['projectrefs'];

        // clean up any authentication data records
        AuthenticateObj::deleteAuthenticationDataRecords();

        $authenticationRecord = AuthenticateObj::getAuthenticationDataRecord(TPX_AUTHENTICATIONTYPE_LOWLEVEL, $authKey, true);

        if ($authenticationRecord['found'])
        {
            $userID = $authenticationRecord['ref'];
            $userDataArray = DatabaseObj::getUserAccountFromID($userID);

            $cartDataArray = OnlineAPI_model::getProjectsForWebViewCheckout($projectRefs, $userID, $userDataArray['groupcode']);

            if (count($cartDataArray['projectreflist']) > 0) {
                $cartDataArray['languagecode'] = $languageCode;

                // set the onlineclienttime session variable from toapixonline
                // this will force any calls to the maweb which go through order.initialise to use this value rather than the
                // one in the cookie. this is becasue the cookie one might be too old.
                $gSession['onlineclienttime'] = time();

                // Although session data has not been created at this point we need to initialise userAddressUpdated & userid
                // so they can be passed to the external shopping cart.
                $gSession['useraddressupdated'] = $userDataArray['addressupdated'];
                $gSession['userid'] = $userID;

                $resultArray = OnlineAPI_model::externalCheckout($cartDataArray, TPX_BASKETWORKFLOWTYPE_HIGHLEVELCHECKOUT, '');

                if ($resultArray['result'] == TPX_ONLINE_ERROR_NONE)
                {

                    DatabaseObj::startSession($userID, $userDataArray['login'], $userDataArray['contactfirstname'] . ' ' . $userDataArray['contactlastname'],
                        TPX_LOGIN_CUSTOMER, $userDataArray['companycode'], $userDataArray['owner'], $userDataArray['webbrandcode'],  $userDataArray['groupcode'], '', array());
                }

            }
            else {
                $resultArray['result'] = TPX_ONLINE_ERROR_HIGHLEVELBASKETEMPTY;
            }

        }
        else {
            $resultArray['result'] = TPX_ONLINE_ERROR_LOWLEVELINTERNALERROR;
        }

        OnlineAPI_view::returnResultAPI($resultArray, $languageCode, TPX_BASKETWORKFLOWTYPE_LOWLEVELAPI, '');

    }

    static function webViewRemoveItemsFromCart()
    {
        global $gConstants;

        $result = TPX_ONLINE_ERROR_NONE;
        $resultParam = '';

        $languageCode = UtilsObj::getGETParam('l', $gConstants['defaultlanguagecode']);
        $cartData = json_decode($_POST['cartdata'], true);
        $authKey = $cartData['authkey'];
        $projectRefs = $cartData['projectrefs'];

        // clean up any authentication data records
        AuthenticateObj::deleteAuthenticationDataRecords();

        $authenticationRecord = AuthenticateObj::getAuthenticationDataRecord(TPX_AUTHENTICATIONTYPE_LOWLEVEL, $authKey, true);

        if ($authenticationRecord['found'])
        {
            foreach($projectRefs as $projectRef)
            {
                $paramArray = array();
                $paramArray['projectreflist'] = array(0 => $projectRef);
                $paramArray['forcekill'] = 1;
                $paramArray['canunlock'] = 1;
                $paramArray['purgedays'] = 0;
                $paramArray['action'] = 'removefrombasket';
                $paramArray['basketref'] = '';

                $checkDeleteSessionArray = OnlineAPI_model::checkDeleteSession($paramArray);

                if ($checkDeleteSessionArray['error'] != '')
                {
                    $result = $checkDeleteSessionArray['error'];
                    $resultParam = $checkDeleteSessionArray['error'];
                }
            }

            if ($result == TPX_ONLINE_ERROR_NONE) {

                $deleteProjectOrderCacheResult = OnlineAPI_model::deleteProjectOrderDataCacheRecords($projectRefs);

                if ($deleteProjectOrderCacheResult['error'] == TPX_ONLINE_ERROR_NONE)
                {
                    $projectRefArray = array('projectreflist' => $projectRefs);

                    $clearProjectBatchRefResult = OnlineAPI_model::clearProjectBatchRef($projectRefArray);

                    if ($clearProjectBatchRefResult['error'] != '')
                    {
                        $result = $clearProjectBatchRefResult['error'];
                        $resultParam = $clearProjectBatchRefResult['error'];
                    }
                }
                else {

                    $result = $deleteProjectOrderCacheResult['error'];
                    $resultParam = $deleteProjectOrderCacheResult['errorparam'];
                }
            }

            $resultArray['result'] = $result;
            $resultArray['resultparam'] = $resultParam;
        }
        else {
            $resultArray['result'] = TPX_ONLINE_ERROR_LOWLEVELINTERNALERROR;
        }

        OnlineAPI_view::returnResultAPI($resultArray, $languageCode, TPX_BASKETWORKFLOWTYPE_LOWLEVELAPI, '');
    }

    static function webViewOrderConfirm(){

        $languageCode = UtilsObj::getPOSTParam('langcode', 'en');

        require_once('../AppAPI/AppAPI_model.php');
        $resultArray = AppAPI_model::orderConfirm();

        OnlineAPI_view::returnResultAPI($resultArray, $languageCode, TPX_BASKETWORKFLOWTYPE_LOWLEVELAPI, '');

    }
}

?>
