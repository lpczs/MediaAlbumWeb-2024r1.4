<?php


ini_set('opcache.enable', 0);

require_once('../Utils/UtilsAddress.php');
require_once('../AppAPI/AppAPI_model.php');

use GuzzleHttp\Client;
use Taopix\ControlCentre\Helper\Create\Project;
class OnlineAPI_model
{
	static function duplicateRenameOnlineProject($pParamArray)
	{
		require_once('../Utils/UtilsLocalization.php');

		$resultArray = array(
			'error' => '',
			'maintenancemode' => false,
			'projectdetails' => array()
		);

		// set minlife to 0 if we are renaming projects as minlife is not needed.
		$minlife = 0;

		if ($pParamArray['cmd'] == 'DUPLICATEPROJECT')
		{
			$minlife = $pParamArray['minlife'];
		}

		$postParamArray = array(
			'cmd' => $pParamArray['cmd'],
			'data' =>  array(
				'projectref' => $pParamArray['projectref'],
				'projectname' => $pParamArray['projectname'],
				'canunlock' => $pParamArray['canunlock'],
				'browserlanguagecode' => $pParamArray['browserlanguagecode'],
				'minlife' => $minlife,
				'ccnotificationsenabled' => $pParamArray['ccnotificationsenabled'],
				'basketapiworkflowtype' => $pParamArray['basketapiworkflowtype'],
				'houroffset' => LocalizationObj::getBrowserHourOffset()
			)
		);

		$curlPutResultArray = self::curlPutToTaopixOnline($postParamArray);


		if ($curlPutResultArray['error'] == '')
		{
            if ($curlPutResultArray['data']['result'] != TPX_ONLINE_ERROR_MAINTENANCEMODE)
            {
				if ($curlPutResultArray['data']['error'] != '')
				{
					$resultArray['error'] = $curlPutResultArray['data']['error'];
					$resultArray['projectdetails'] = $curlPutResultArray['data']['projectdetails'];
				}
				else
				{
					$resultArray['onlinedesignerurl'] = '';
					$resultArray['projectdetails'] = $curlPutResultArray['data']['projectdetails'];

					// if we are duplicating a project we need to grab the relevant online designer url
					// so that the thumbnail points to the correct server
					if ($pParamArray['cmd'] == 'DUPLICATEPROJECT')
					{
						global $gSession;

						$brandingDefaults = DatabaseObj::getBrandingFromCode($gSession['webbrandcode']);
						$resultArray['onlinedesignerurl'] = $brandingDefaults['onlinedesignerurl'];
					}
				}
            }
            else
            {
                $resultArray['maintenancemode'] = true;
            }
		}
		else
		{
			$resultArray['projectdetails'] = array(
				'projectref' => '',
				'productident' => '',
				'workflowtype' => '',
				'projectexists' => '',
				'restoremessage' => ''
			);

			$resultArray['error'] = $curlPutResultArray['error'];
		}

        return $resultArray;
	}

	/**
	 * checkDeleteSession
	 *
	 * This function will check if a session exists or delete the session is force is 1
	 * This function can delete the project too if the action is delete
	 *
	 */
    static function checkDeleteSession($pParamArray)
    {
		global $gSession;

        $projectRefArray = $pParamArray['projectreflist'];
		$forceKill = $pParamArray['forcekill'];
		$purgeDays = $pParamArray['purgedays'];
		$canUnlock = $pParamArray['canunlock'];
		$action = $pParamArray['action'];
		$basketRef = $pParamArray['basketref'];

		// check if the project is in production
		$getOrderItemsModifyStatusArray = self::getOrderItemsModifyStatus($projectRefArray, $action, $basketRef);
		$getOrderItemsModifyStatusArray['maintenancemode'] = false;

		if ($getOrderItemsModifyStatusArray['error'] == '')
		{
			$requireLoaded = false;
			$deleteProjectCommandProjectRefArray = array();
			$sessionCommandProjectRefArray = array();

			foreach($getOrderItemsModifyStatusArray['projectitemarray'] as &$projectItem)
			{
				$shoppingCartSessionRef = $projectItem['shoppingcartsessionref'];

				// prevent order sessions from being cancelled when online project previews are being initialised.
				if (($shoppingCartSessionRef) && ($action != 'previewexisting'))
				{
					if ($forceKill == 1)
					{
						if (! $requireLoaded)
						{
							// project has been found in shopping cart make sure the user is not in payment process
							require_once('../AppAPI/AppAPI_model.php');
							require_once('../Utils/UtilsAuthenticate.php');
							$requireLoaded = true;
						}

						// We have identified that a shopping cart session already exisited for the projects being ordered. We need to take a copy of gSession.
						// The reason for this is due the fact that we have to repopulate gSession so that cancelOrderSession can work
						// as the logic in that function works off gSession. If we dont take a copy of gSession then we would lose everything
						// for the new order attempt.
						$sessionBackup = $gSession;

						// attempt to cancel the existing shopping cart session
						// first load the existing shopping cart session as cancelling may involve more than just deleting a session record
						$gSession = DatabaseObj::getSessionData($shoppingCartSessionRef);

						// perform the cancel
						$cancelOrderSessionArray = AppAPI_model::cancelOrderSession(4, $gSession['browserlanguagecode'], 1);

						// we need to forget the old shopping cart session and replace it with session data for the current order attempt.
						$gSession = $sessionBackup;

						if ($cancelOrderSessionArray['result'] == 'ORDERCANCELCONFIRM' || $cancelOrderSessionArray['result']  == 'CANCEL')
						{
							$projectItem['sessionactive'] = false;
							$projectItem['sessiontype'] = '';

							if ($action == 'delete')
							{
								$deleteProjectCommandProjectRefArray[] = $projectItem['projectref'];
							}
							else
							{
								$sessionCommandProjectRefArray[] = $projectItem['projectref'];
							}
						}
					}
					else
					{
						$projectItem['sessionactive'] = true;
						$projectItem['sessiontype'] = 'shoppingcart';
					}
				}
				else
				{
					if ($action == 'delete')
					{
						$deleteProjectCommandProjectRefArray[] = $projectItem['projectref'];
					}
					else
					{
						$sessionCommandProjectRefArray[] = $projectItem['projectref'];
					}
				}
			}

			$deleteProjectCommandProjectRefArrayCount = count($deleteProjectCommandProjectRefArray);
			$sessionCommandProjectRefArrayCount = count($sessionCommandProjectRefArray);

			if ($deleteProjectCommandProjectRefArrayCount > 0)
			{
				$postParam = array(
					'cmd' => 'DELETEPROJECT',
					'data' => array(
						'projectreflist' => $deleteProjectCommandProjectRefArray,
						'purgedays' => $purgeDays,
						'canunlock' => $canUnlock
					)
				);

				$curlPutResultArray = self::curlPutToTaopixOnline($postParam);

				if ($curlPutResultArray['error'] != '')
				{
					$getOrderItemsModifyStatusArray['error'] = $curlPutResultArray['error'];
				}
				else
				{
				    if ($curlPutResultArray['data']['result'] == TPX_ONLINE_ERROR_MAINTENANCEMODE)
				    {
				        $getOrderItemsModifyStatusArray['maintenancemode'] = true;
				    }
				    else
				    {
				    	$projectRefDataArray = $curlPutResultArray['data']['projectreflist'];

				    	foreach ($projectRefDataArray as &$project)
						{

							$getOrderItemsModifyStatusArray['projectitemarray'][$project['projectref']]['error'] = $project['result'];

							if ($project['result'] == TPX_ONLINE_ERROR_PROJECTLOCKED)
							{
								$getOrderItemsModifyStatusArray['projectitemarray'][$project['projectref']]['projectlocked'] = true;
							}

							if ($project['result'] == TPX_ONLINE_ERROR_PROJECTDOESNOTEXIST)
							{
								$getOrderItemsModifyStatusArray['projectitemarray'][$project['projectref']]['projectexists'] = false;
							}
						}

				    }
				}
			}

			if ($sessionCommandProjectRefArrayCount > 0)
			{
				// force the session to be deleted and delete the project if action is 'delete'
				if ($forceKill == 1)
				{
					$postParam = array(
						'cmd' => 'KILLOPENEDDESIGNSESSION',
						'data' => array(
							'projectreflist' => $sessionCommandProjectRefArray,
							'purgedays' => $purgeDays,
							'canunlock' => $canUnlock,
							'action' => $action
						)
					);
				}
				else
				{
					$postParam = array(
						'cmd' => 'CHECKPROJECTOPENINGSTATUS',
						'data' => array(
							'projectreflist' => $sessionCommandProjectRefArray,
							'purgedays' => $purgeDays,
							'canunlock' => $canUnlock,
							'action' => $action
						)
					);
				}

				$curlPutResultArray = self::curlPutToTaopixOnline($postParam);

				if ($curlPutResultArray['error'] == '')
				{
                    if ($curlPutResultArray['data']['result'] != TPX_ONLINE_ERROR_MAINTENANCEMODE)
                    {
						$projectRefDataArray = $curlPutResultArray['data']['projectitems'];

						foreach ($projectRefDataArray as &$project)
						{
							$getOrderItemsModifyStatusArray['projectitemarray'][$project['projectref']]['projectexists'] = $project['projectexists'];
							$getOrderItemsModifyStatusArray['projectitemarray'][$project['projectref']]['flightcheckstatus'] = $project['flightcheckstatus'];

							if ($project['error'] == TPX_ONLINE_ERROR_PROJECTLOCKED)
							{
								$getOrderItemsModifyStatusArray['projectitemarray'][$project['projectref']]['projectlocked'] = 1;
							}

							if ($forceKill == 0)
							{
								if ($project['projectrefcount'] > 0)
								{
									$getOrderItemsModifyStatusArray['projectitemarray'][$project['projectref']]['sessionactive'] = true;
									$getOrderItemsModifyStatusArray['projectitemarray'][$project['projectref']]['sessiontype'] = 'taopixonline';
								}
							}
						}
					}
					else
					{
						$getOrderItemsModifyStatusArray['maintenancemode'] = true;
					}
				}
				else
				{
					$getOrderItemsModifyStatusArray['error'] = $curlPutResultArray['error'];
				}
			}
		}
		else
		{
			$getOrderItemsModifyStatusArray['error'] = $getOrderItemsModifyStatusArray['error'];
		}

        return $getOrderItemsModifyStatusArray;
    }

	/**
	 * Request alternative layout data from online including cache information
	 *
	 * @param string $pProductLayoutCode layout code of the selected product
	 * @param string $pProductCollectionCode collection code of the selected product
	 * @param string $pTenantID tenant id
	 * @param string $pDataCacheKey data cache key to check for online cache
	 * @return void
	 */
	public static function getProductTreesAPIData($pProductLayoutCode, $pProductCollectionCode, $pTenantID, $pDataCacheKey): array
    {
    	 $resultArray = array(
			'error' => '',
			'data' => ''
		);

    	$postParam = array(
			'cmd' => 'GETPRODUCTTREESDATA',
			'data' => array('productlayoutcode' => $pProductLayoutCode,
								'productcollectioncode' => $pProductCollectionCode,
										'tenantid' => $pTenantID,
											'datacachekey' => $pDataCacheKey
							));

		$curlPutResultArray = self::curlPutToTaopixOnline($postParam);

		if ($curlPutResultArray['error'] == '')
		{
			if (! empty($curlPutResultArray['data']))
			{
				$resultArray['data'] = $curlPutResultArray['data'];
			}
		}
		else
		{
			$resultArray['error'] = $curlPutResultArray['error'];
		}

		return $resultArray;
    }

    static function getEditProjectAPIData($pProjectRef, $pNewUserID)
    {
    	 $resultArray = array(
			'error' => TPX_ONLINE_ERROR_NONE,
			'groupcode' => '',
			'collectioncode' => '',
			'collectionname' => '',
			'layoutcode' => '',
			'layoutname' => '',
			'projectname' => '',
			'userid' => 0
		);

    	$postParam = array(
			'cmd' => 'GETEDITPROJECTAPIDATA',
			'data' => array('projectref' => $pProjectRef, 'newuserid' => $pNewUserID));

		$curlPutResultArray = self::curlPutToTaopixOnline($postParam);

		if ($curlPutResultArray['error'] == '')
		{
			$projectDetails = array('restoremessage' => '');
			if (! empty($curlPutResultArray['data']['projectdetails']))
			{
				$projectDetails = $curlPutResultArray['data']['projectdetails'];
			}


			if ($projectDetails['restoremessage'] != '')
			{
				$resultArray['error'] = $projectDetails['restoremessage'];
				$resultArray['projectdetails'] = $projectDetails;
			}
			else
			{
				$projectData = $curlPutResultArray['data']['project'];

				$resultArray['error'] = $curlPutResultArray['data']['error'];
				$resultArray['groupcode'] = $projectData['groupcode'];
				$resultArray['workflowtype'] = $projectData['type'];
				$resultArray['wizardmodeonline'] = $projectData['wizardmodeonline'];
				$resultArray['collectioncode'] = $projectData['collectioncode'];
				$resultArray['collectionname'] = $projectData['collectionname'];
				$resultArray['layoutcode'] = $projectData['layoutcode'];
				$resultArray['layoutname'] = $projectData['layoutname'];
				$resultArray['projectname'] = $projectData['projectname'];
				$resultArray['userid'] = $projectData['userid'];
				$resultArray['loadedstatus'] = $projectData['loadedstatus'];
				$resultArray['templateref'] = $projectData['templateref'];
				$resultArray['originalref'] = $projectData['originalref'];
				$resultArray['3dmodelsystemresourcefileid'] = $projectData['3dmodelsystemresourcefileid'];
				$resultArray['featuretoggle'] = $projectData['featuretoggle'];
				$resultArray['onlineeditormode'] = $projectData['onlineeditormode'];
				$resultArray['showswitcheditor'] = $projectData['showswitcheditor'];
				$resultArray['automaticallyapplyperfectlyclearmode'] = $projectData['automaticallyapplyperfectlyclearmode'];
				$resultArray['minimumprintsperproject'] = $projectData['minimumprintsperproject'];
				$resultArray['orderstatus'] = $projectData['orderstatus'];
			}
		}
		else
		{
			$resultArray['error'] = $curlPutResultArray['error'];
		}
		$resultArray['error'] = $resultArray['error'] === '' ? TPX_ONLINE_ERROR_NONE : $resultArray['error'];
		return $resultArray;
    }

    static function openOnlineProject($pOpenMode, $pParamArray, $pPreviewExisitingProjectDataArray, $pEditProjectFromAPI, $pCompleteOrder)
    {
    	global $gSession;

		$systemConfigDataArray = DatabaseObj::getSystemConfig();
        $tenantID = $systemConfigDataArray['tenantid'];
        $projectRef = UtilsObj::getGETParam('projectref', '');
        $workflow = UtilsObj::getGETParam('workflowtype', 1);
		$onlineSessionResult = array('error' => '', 'errorparam' => '', 'brandurl' => '', 'maintenancemode' => '', 'projectref' => '', 'projectname' => '', 'userid' => 0);

		$productIdentData = array();
		$productIdentData['3dmodelsystemresourcefileid'] = '';
		$productIdentData['onlinedesignerlogolinkurl'] = '';
		$productIdentData['onlinedesignerlogolinktooltip'] = '';
		$productIdentData['featuretoggle'] = '';
		$productIdentData['minimumprintsperproject'] = 1;
        $productIdentData['usedefaultimagescalingbefore'] = 0;
		$productIdentData['imagescalingbeforeenabled'] = 0;
        $productIdentData['imagescalingbefore'] = 0.00;
        $productIdentData['aimodeoverride'] = -1;
        $productIdentData['usedefaultaveragepicturesperpage'] = 0;
        $productIdentData['averagepicturesperpage'] = 0;

		$collectionName = '';
		$layoutName = '';
		$collectionCode = '';
		$layoutCode = '';
        $groupCode = '';

        if (($pOpenMode == TPX_OPEN_MODE_EXISTING_PROJECT) && (! $pEditProjectFromAPI))
        {
        	$productIdent = $_GET['productindent'];

            $productIdentData = explode(chr(10), UtilsObj::decryptData($productIdent, $systemConfigDataArray['systemkey'], true), 2);

			$productIdentData = UtilsObj::parseProductURLIdentData($productIdentData, $_GET);
			$groupCode = $gSession['licensekeydata']['groupcode'];
            $collectionCode = $productIdentData['collectioncode'];
            $layoutCode = $productIdentData['layoutcode'];

            $productIdentData['editprojectnameonfirstsave'] = 0;
        	$productIdentData['basketapiworkflowtype'] = 0;
			$productIdentData['experienceoverrides'] = [];

			$getEditProjectAPIDataArray = self::getEditProjectAPIData($projectRef, 0);

			if ($getEditProjectAPIDataArray['error'] === TPX_ONLINE_ERROR_NONE)
			{
				$productIdentData['loadedstatus'] = $getEditProjectAPIDataArray['loadedstatus'];
				$productIdentData['templateref'] = $getEditProjectAPIDataArray['templateref'];
				$productIdentData['originalref'] = $getEditProjectAPIDataArray['originalref'];
				$productIdentData['3dmodelsystemresourcefileid'] = $getEditProjectAPIDataArray['3dmodelsystemresourcefileid'];
				$productIdentData['onlinedesignerlogolinkurl'] = UtilsObj::getArrayParam($pParamArray, 'onlinedesignerlogolinkurl', '');
				$productIdentData['onlinedesignerlogolinktooltip'] = UtilsObj::getArrayParam($pParamArray, 'onlinedesignerlogolinktooltip', '');
				$productIdentData['onlinedesignercdnurl'] = UtilsObj::getArrayParam($pParamArray, 'onlinedesignercdnurl', '');
				$productIdentData['featuretoggle'] = $getEditProjectAPIDataArray['featuretoggle'];
				$productIdentData['allowupsell'] = ($getEditProjectAPIDataArray['orderstatus'] == 1) ? false : true;
				$onlineEditorMode = $getEditProjectAPIDataArray['onlineeditormode'];
				$switchEditor = $getEditProjectAPIDataArray['showswitcheditor'];

				$productIdentData['onlineeditormode'] = $onlineEditorMode;
				$productIdentData['enableswitchingeditor'] = $switchEditor;
				$productIdentData['automaticallyapplyperfectlyclearmode'] = $getEditProjectAPIDataArray['automaticallyapplyperfectlyclearmode'];
				$productIdentData['minimumprintsperproject'] = $getEditProjectAPIDataArray['minimumprintsperproject'];
				$productIdentData['canshareproject'] = (! array_key_exists('canshareproject', $pParamArray)) ? 0 : $pParamArray['canshareproject'];

				$collectionName = $getEditProjectAPIDataArray['collectionname'];
        		$layoutName = $getEditProjectAPIDataArray['layoutname'];

				$getOrderItemsModifyStatusArray = self::getOrderItemsModifyStatus([$projectRef], 'editing', '');

				if (($getOrderItemsModifyStatusArray['error'] == '') && ($getOrderItemsModifyStatusArray['projectitemarray'][$projectRef]['orderfound']))
				{
					$productIdentData['orderfound'] = $getOrderItemsModifyStatusArray['projectitemarray'][$projectRef]['orderfound'];
				}
			}
			else
			{
				if ($getEditProjectAPIDataArray['projectdetails']['restoremessage'] != '')
				{
					$onlineSessionResult['error'] = TPX_ONLINE_ERROR_PROJECTDOESNOTEXIST;
					$onlineSessionResult['errorparam'] = $getEditProjectAPIDataArray['projectdetails']['restoremessage'];
					$onlineSessionResult['projectdetails'] = $getEditProjectAPIDataArray['projectdetails'];
				}
				else
				{
					$onlineSessionResult['error'] = TPX_ONLINE_ERROR_PROJECTDOESNOTEXIST;
				}
			}
        }
        else if (($pOpenMode == TPX_OPEN_MODE_EXISTING_PROJECT) && ($pEditProjectFromAPI))
        {
        	$productIdentData = $pParamArray;
        	$workflow = $pParamArray['workflowtype'];
        	$collectionName = $pParamArray['collectionname'];
        	$layoutName = $pParamArray['layoutname'];
        	$layoutCode = $pParamArray['layoutcode'];
        	$collectionCode = $pParamArray['collectioncode'];
			$groupCode = $pParamArray['groupcode'];
        }
        else if ($pOpenMode == TPX_OPEN_MODE_PREVIEW_EXISITING)
        {
            $groupCode = $pPreviewExisitingProjectDataArray['groupcode'];
        }

		if ($onlineSessionResult['error'] == '')
		{
			$ssoResult = '';
			$ssoResultParam = '';
			$ssoToken = '';
			$ssoPrivateDataArray = Array();
			$assetServiceDataArray = Array();
			$ssoExpireDate = '';

			// set the authenticate sso reason
			$ssoReason = TPX_USER_AUTH_REASON_ONLINE_PROJECT_EDIT;

			// retrieve the default language when creating new projects
			$languageCode = UtilsObj::getGETParam('l', '');

			// determine the default language code
			if ($languageCode == '')
			{
				$languageCode = UtilsObj::getBrowserLocale();
			}
			// extract the license key data
			$licenseKeyDataArray = DatabaseObj::getLicenseKeyFromCode($groupCode);
			$companyCode = $licenseKeyDataArray['companyCode'];
			$startOnlineSession = false;

			if ($licenseKeyDataArray['isactive'] == 1 && $licenseKeyDataArray['availableonline'] == 1)
			{
				// we don't need to authenticate opening a project for preview and if the call has come from an API call
				if (($pOpenMode != TPX_OPEN_MODE_PREVIEW_EXISITING) && (! $pEditProjectFromAPI))
				{
					// attempt to perform a single sign-on to the system
					$authenticateLoginArray = AuthenticateObj::authenticateLogin($ssoReason, $gSession['ref'], false, $languageCode,
																$licenseKeyDataArray['webbrandcode'], $groupCode, '', '', TPX_PASSWORDFORMAT_CLEARTEXT,
																'', true, false, true, $gSession['userdata']['ssotoken'], $gSession['userdata']['ssoprivatedata'], array());

					if ($authenticateLoginArray['result'] == '')
					{
						$userID = $authenticateLoginArray['useraccountid'];
						$userName = $authenticateLoginArray['username'];
						$ssoToken = $authenticateLoginArray['ssotoken'];
						$ssoPrivateDataArray = $authenticateLoginArray['ssoprivatedata'];
						$ssoExpireDate = $authenticateLoginArray['ssoexpiredate'];
						$assetServiceDataArray = $authenticateLoginArray['assetservicedata'];
					}
					else
					{
						$ssoResult = $authenticateLoginArray['result'];
						$ssoResultParam = $authenticateLoginArray['resultparam'];
					}

					// process the result of the single sign-on request
					switch ($ssoResult)
					{
						case 'SSOREDIRECT':
						{
							// redirect to grab the single sign-on token
							$onlineSessionResult['brandurl'] = $ssoResultParam;
							$onlineSessionResult['error'] = '';
							$onlineSessionResult['errorparam'] = '';
							$onlineSessionResult['maintenancemode'] = false;

							break;
						}
						case '':
						{
							$productIdentData['userid'] = $userID;
							$productIdentData['username'] = $userName;

							// pass the sso details over to online
							$productIdentData['ssotoken'] = $ssoToken;
							$productIdentData['ssoprivatedata'] = $ssoPrivateDataArray;
							$productIdentData['ssoexpiredate'] = $ssoExpireDate;
							$productIdentData['assetservicedata'] = $assetServiceDataArray;

							$startOnlineSession = true;

							break;
						}
						default:
						{
							// any other error
							$onlineSessionResult['brandurl'] = '';
							$onlineSessionResult['error'] = $ssoResult;
							$onlineSessionResult['errorparam'] = $ssoResultParam;
							$onlineSessionResult['maintenancemode'] = false;

							break;
						}
					}
				}
				else
				{
					if ($pOpenMode == TPX_OPEN_MODE_PREVIEW_EXISITING)
					{
                        $collectionCode = $pPreviewExisitingProjectDataArray['productcollectioncode'];
                        $layoutCode = $pPreviewExisitingProjectDataArray['productlayoutcode'];
                        $projectRef = $pPreviewExisitingProjectDataArray['projectref'];
                        $productIdentData['collectioncode'] = $collectionCode;
                        $productIdentData['layoutcode'] = $layoutCode;
                        $productIdentData['groupcode'] = $groupCode;
                        $productIdentData['projectref'] = $projectRef;

                        // For the preview force the editor to be advanced.
						$productIdentData['onlineeditormode'] = TPX_ONLINE_EDITOR_MODE_ADVANCED;

						// if previewviewsource is customer then we no that this has came from the preview generated from the customer account orders page
						// and has not come from any API so we can set the defaults as it is not controlled externally from the API
						$productIdentData['cansignin'] = ($pPreviewExisitingProjectDataArray['previewviewsource'] == '') ? $pParamArray['cansignin'] : 1;
						$productIdentData['canshareproject'] = ($pPreviewExisitingProjectDataArray['previewviewsource'] == '') ? $pParamArray['canshareproject'] : 1;
						$productIdentData['basketapiworkflowtype'] = TPX_BASKETWORKFLOWTYPE_NORMAL;

						if ($pPreviewExisitingProjectDataArray['previewviewsource'] == '')
						{
							if ($pParamArray['basketapiworkflowtype'] == TPX_BASKETWORKFLOWTYPE_NORMAL)
							{
								$productIdentData['basketapiworkflowtype'] = TPX_BASKETWORKFLOWTYPE_HIGHLEVELAPI;
							}
							else
							{
								$productIdentData['basketapiworkflowtype'] = TPX_BASKETWORKFLOWTYPE_LOWLEVELAPI;
							}
						}
					}


					$startOnlineSession = true;
				}

				if ($startOnlineSession)
				{
                    $brandingArray = DatabaseObj::getBrandingFromCode($licenseKeyDataArray['webbrandcode']);
					$productArray = DatabaseObj::getProductFromCollectionCodeAndLayoutCode($collectionCode, $layoutCode);
					$productIdentData['imagescalingbeforeenabled'] = $productArray['imagescalingbeforeenabled'];
					$productIdentData['imagescalingbefore'] = $productArray['imagescalingbefore'];
					$productIdentData['usedefaultimagescalingbefore'] = $productArray['usedefaultimagescalingbefore'];
					$productIdentData['usedefaultaveragepicturesperpage'] = $productArray['usedefaultaveragepicturesperpage'];
					$productIdentData['averagepicturesperpage'] = $productArray['averagepicturesperpage'];
					$productIdentData['retroprints'] = $productArray['retroprints'];

					// attempt to configure the logo link url, only if we have been able to acquire the license key data
					// and the onlinedesignerlogolinkurl has not already been set i.e it was passed via low level editProject
					if (($licenseKeyDataArray['result'] === '') && ($productIdentData['onlinedesignerlogolinkurl'] === ''))
					{
						// pass the logo link url set in control centre into the low level API
						if ($licenseKeyDataArray['usedefaultonlinedesignerlogolinkurl'] == 1)
						{
							// use url set by the brand
							$productIdentData['onlinedesignerlogolinkurl'] = $brandingArray['onlinedesignerlogolinkurl'];
							$productIdentData['onlinedesignerlogolinktooltip'] = $brandingArray['onlinedesignerlogolinktooltip'];
						}
						else
						{
							// use url set by the licensekey
							$productIdentData['onlinedesignerlogolinkurl'] = $licenseKeyDataArray['onlinedesignerlogolinkurl'];
							$productIdentData['onlinedesignerlogolinktooltip'] = $licenseKeyDataArray['onlinedesignerlogolinktooltip'];
						}
					}
					else
					{
						// make sure the 'onlinedesignerlogolinkurl' elements are part of the $productIdentData array
						if (! array_key_exists('onlinedesignerlogolinkurl', $productIdentData))
						{
							$productIdentData['onlinedesignerlogolinkurl'] = '';
							$productIdentData['onlinedesignerlogolinktooltip'] = '';
						}
					}

					$projectRef = '';
					$projectName = '';
					$designURL = '';
					$error = '';
					$userName = '';

					if (TPX_PRODUCT_TYPE_SINGLE_PRINTS !== $productArray['collectiontype'])
					{
                        $pParamArray['openmode'] = $pOpenMode;
						// this is to handle different arrays being passed in/created from different calls
						$pParamArray = array_merge($pParamArray, $productIdentData);
						$pParamArray['userid'] = ($pParamArray['userid'] === 0) ? $getEditProjectAPIDataArray['userid'] : $pParamArray['userid'];
						$pParamArray['theme'] = $brandingArray['theme'];
						$pParamArray['experienceoverrides']['orderfound'] = $pParamArray['orderfound'] ?? null;

						//need user name from DB if registered user
						if ($pParamArray['userid'] > 0) {
							$userAccount = DatabaseObj::getUserAccountFromID($pParamArray['userid']);

							if ($userAccount['result'] == '') {
								$userName = $userAccount['contactfirstname'] . ' ' . $userAccount['contactlastname'];
							}
						}
						$pParamArray['username'] = $userName;

						try {
							$client = new Client();
							$urlCreator = new Project($client, UtilsObj::correctPath($brandingArray['onlinedesignerurl'], '/', false), $pParamArray['userid'], $pParamArray);
							$projectRef = $urlCreator->getProjectRef();
							$projectName = $urlCreator->getProjectName();
							$designURL = $urlCreator->getDesignerURL();
						}
						catch (GuzzleHttp\Exception\ClientException|GuzzleHttp\Exception\ServerException $e) {
							$errorData = json_decode($e->getResponse()->getBody()->getContents(), true);
							$error = $errorData['error']['code'];
						}

						$userID = $pParamArray['userid'];
					}
					else
					{
						$dataCacheKey = $groupCode . '.' . $companyCode . '.' . $collectionCode;
						$productTreesData = OnlineAPI_model::getProductTreesAPIData($layoutCode, $collectionCode, $tenantID, $dataCacheKey);
						$productIdentData['producttreesdata'] = $productTreesData;

						$paramData = self::prepareParamDataToCreateOnlineSession($pOpenMode, $projectRef, $pPreviewExisitingProjectDataArray,
						$productIdentData, $workflow);

						if ($pOpenMode == TPX_OPEN_MODE_PREVIEW_EXISITING)
						{
							$paramData['projectref'] = $pPreviewExisitingProjectDataArray['projectref'];
							$paramData['workflow'] = $pPreviewExisitingProjectDataArray['workflowtype'];
						}
						else
						{
							$paramData['projectref'] = $pParamArray['projectref'];
							$paramData['workflow'] = $pParamArray['workflowtype'];
						}

						if ($pCompleteOrder)
						{
							$paramData['completeorder'] = 1;
						}
						else
						{
							$paramData['completeorder'] = 0;
						}

						$paramData['collectionname'] = $collectionName;
						$paramData['layoutname'] = $layoutName;
						$paramData['tenantid'] = $tenantID;

						$createSessionResult = AuthenticateObj::createOnlineSession($paramData);
						$error = $createSessionResult['error'];
						$projectRef = $createSessionResult['projectref'];
						$projectName = $createSessionResult['projectname'];
						$designURL = $createSessionResult['brandurl'] . '&lsp=1';
						$userID = $createSessionResult['userid'];
					}

					$onlineSessionResult['error'] = $error;
					$onlineSessionResult['projectref'] = $projectRef;
					$onlineSessionResult['projectname'] = $projectName;
					$onlineSessionResult['brandurl'] = $designURL;
					$onlineSessionResult['userid'] = $userID;
				}
			}
			else
			{
                $onlineSessionResult['error'] = TPX_ONLINE_ERROR_INACTIVELICENSEKEY;
			}
		}

		return $onlineSessionResult;
    }

    static function deleteUnflagProject($pProjectRefList)
    {
    	  $resultArray = array(
			'error' => '',
			'projectreflist' => array()
		);

    	$postParam = array(
			'cmd' => 'DELETEUNFLAGPROJECT',
			'data' => $pProjectRefList);

		$curlPutResultArray = self::curlPutToTaopixOnline($postParam);

		if ($curlPutResultArray['error'] == '')
		{
			$resultArray['projectreflist'] = $curlPutResultArray['data']['projectreflist'];
		}
		else
		{
			$resultArray['error'] = $curlPutResultArray['error'];
		}

		return $resultArray;
    }

    static function touchProject($pParamArray)
    {
    	 $resultArray = array(
			'error' => '',
			'projectreflist' => array()
		);

    	$postParam = array(
			'cmd' => 'TOUCHPROJECT',
			'data' => array('projectreflist' => $pParamArray['projectreflist'],
							'newminlife' => $pParamArray['newminlife']));

		$curlPutResultArray = self::curlPutToTaopixOnline($postParam);

		if ($curlPutResultArray['error'] == '')
		{
			$resultArray['projectreflist'] = $curlPutResultArray['data']['projectreflist'];
		}
		else
		{
			$resultArray['error'] = $curlPutResultArray['error'];
		}

		return $resultArray;
    }

    static function lockProject($pParamArray)
    {
    	$resultArray = array('error' => '', 'projectreflist' => array());

    	$postParam = array(
			'cmd' => 'LOCKPROJECT',
			'data' => array('projectreflist' => $pParamArray['projectreflist'],
							'lockperiod' => $pParamArray['lockperiod']));

		$curlPutResultArray = self::curlPutToTaopixOnline($postParam);

		if ($curlPutResultArray['error'] == '')
		{
			$resultArray['projectreflist'] = $curlPutResultArray['data']['projectreflist'];
		}
		else
		{
			$resultArray['error'] = $curlPutResultArray['error'];
		}

		return $resultArray;
    }

    static function unlockProject($pParamArray)
    {
    	 $resultArray = array('error' => '', 'resultlist' => array());

    	$postParam = array(
			'cmd' => 'UNLOCKPROJECT',
			'data' => array('projectreflist' => $pParamArray['projectreflist']));

		$curlPutResultArray = self::curlPutToTaopixOnline($postParam);

		if ($curlPutResultArray['error'] == '')
		{
			$resultArray['projectreflist'] = $curlPutResultArray['data']['projectreflist'];
		}
		else
		{
			$resultArray['error'] = $curlPutResultArray['error'];
		}

		return $resultArray;
    }

    static function externalCheckout($pParamArray, $pBasketAPIWorkFlowType, $pHighLevelBasketRef)
	{
		$returnArray = Array('result' => TPX_ONLINE_ERROR_NONE, 'resultmessage' => '');

		$smarty = SmartyObj::newSmarty('Customer', '', '', $pParamArray['languagecode']);
		$projectFailedValidationCount = 0;

		if (count($pParamArray['projectreflist']) > 0)
		{
			$validationParamArray = array();
			$validationParamArray['projectreflist'] = $pParamArray['projectreflist'];


			if ($pBasketAPIWorkFlowType == TPX_BASKETWORKFLOWTYPE_HIGHLEVELCHECKOUT)
			{
				$validationParamArray['forcekill'] = 1;
			}
			else
			{
				$validationParamArray['forcekill'] = 0;
			}

			$validationParamArray['purgedays'] = 0;
			$validationParamArray['canunlock'] = 0;
			$validationParamArray['action'] = 'editing';
			$validationParamArray['basketref'] = '';

			$validateCheckoutResult = self::checkDeleteSession($validationParamArray);

			// first we must perform the following validation
			// make sure the project exisits
			// make sure there are no open design sessions for the projects
			// make sure the project has passed the flight check successfully
			if ($validateCheckoutResult['error'] == '')
			{
				foreach ($validateCheckoutResult['projectitemarray'] as &$project)
				{
					$result = TPX_ONLINE_ERROR_NONE;
					$resultMessage = '';

					$projectRef = $project['projectref'];

					if (! $project['orderfound'])
					{
						if ($project['sessionactive'] == true)
						{
							$result = TPX_ONLINE_ERROR_PROJECTALREADYOPEN;
							$resultMessage = $smarty->get_config_vars('str_WarningTerminateOtherSession');
							$projectFailedValidationCount++;
						}
						else
						{
							if ($project['projectexists'] == false)
							{
								$result = TPX_ONLINE_ERROR_PROJECTDOESNOTEXIST;
								$resultMessage = $smarty->get_config_vars('str_ErrorProjectDoesNotExist');
								$projectFailedValidationCount++;
							}
							else
							{
								if ($project['flightcheckstatus'] == 0)
								{
									$result = TPX_ONLINE_ERROR_FLIGHTCHECKINCOMPLETE;
									$resultMessage = $smarty->get_config_vars('str_ErrorFlightCheckIncomplete');
									$projectFailedValidationCount++;
								}
							}
						}
					}
					else
					{
						$result = TPX_ONLINE_ERROR_PROJECTINPRODUCTION;
						$resultMessage = $smarty->get_config_vars('str_ErrorOrderInProduction');
						$projectFailedValidationCount++;
					}

					$returnArray[$projectRef]['result'] = $result;
					$returnArray[$projectRef]['resultmessage'] = $resultMessage;

					if ($pBasketAPIWorkFlowType != TPX_BASKETWORKFLOWTYPE_HIGHLEVELCHECKOUT)
					{
						$returnArray[$projectRef]['cartdata'] = Array();
					}
				}
			}


			// if all validation has passed we now need to check if any external assets have expired.
			if ($projectFailedValidationCount == 0)
			{
				$projectsHaveNoExpiredAssets = true;
				$assetExpiredMessage = $smarty->get_config_vars('str_ErrorFlightCheckExternalAssetExpired');
				$serverUTCServerTime = DatabaseObj::getServerTimeUTC();

				$cartData = self::prepareCartData($pParamArray['cartdata'], $pBasketAPIWorkFlowType, $pHighLevelBasketRef);

				$cartItemCount = $cartData['cartitemcount'];

				// loop round the line items in the cart to see if any projects are using exteranl assets.
				foreach ($cartData['cartarray'] as $lineItem)
				{
					$itemExternalAssets = $lineItem['externalassets'];

					if (! empty($itemExternalAssets))
					{
						foreach ($itemExternalAssets as $externalAsset)
						{
							// check to see if the assets are set to expire.
							if ($externalAsset['expirationdate'] != '0000-00-00 00:00:00' && $externalAsset['expirationdate'] != '')
							{
								// check to see if the asset has expired
								if (strtotime($externalAsset['expirationdate']) < strtotime($serverUTCServerTime))
								{
									$projectsHaveNoExpiredAssets = false;

									$returnArray['result'] = TPX_ONLINE_ERROR_EXTERNALCHECKOUTERROR;

									$projectRef = $lineItem['projectref'];
									$returnArray[$projectRef]['result'] = TPX_ONLINE_ERROR_EXTERNALASSETEXPIRED;
									$returnArray[$projectRef]['resultmessage'] = $assetExpiredMessage;
								}
							}
						}
					}
				}
			}
			else
			{
				$returnArray['result'] = TPX_ONLINE_ERROR_EXTERNALCHECKOUTERROR;
			}

			// if all of the abouve validation has passed then we must check to make sure the product configuration is still valid.
			if (($projectFailedValidationCount == 0) && ($projectsHaveNoExpiredAssets))
			{
				$orderDataResultArray = AppAPI_model::order($cartData);
				$orderResult = $orderDataResultArray['result'];

				if ($orderResult != 'ORDER')
				{
					$returnArray['result'] = TPX_ONLINE_ERROR_EXTERNALCHECKOUTERROR;

					if (substr($orderResult, 0, 4) == 'str_')
					{
						$smarty = SmartyObj::newSmarty('AppAPI', '', '', $pParamArray['languagecode']);
						$orderResult = $smarty->get_config_vars($orderResult);
					}
					else if ($orderResult == 'INACTIVEPRODUCT')
					{
						$smarty = SmartyObj::newSmarty('Order', '', '', $pParamArray['languagecode']);

						$msg = $smarty->getConfigVars('str_ErrorProductNotAvailable2');
						$msg = str_replace(['^0', '^1'], [$orderDataResultArray['inactiveproductcollectioncode'], $orderDataResultArray['inactiveproductcollectioncode']], $msg);

						$orderResult = $msg;
					}

					$returnArray['resultmessage'] = $orderResult;
					$projectFailedValidationCount++;
				}
				else
				{
					if ($pBasketAPIWorkFlowType == TPX_BASKETWORKFLOWTYPE_HIGHLEVELCHECKOUT)
					{
						$returnArray['shoppingcarturl'] = $orderDataResultArray['shoppingcarturl'];
                        $returnArray['statusurl'] = $orderDataResultArray['statusurl'];
					}
				}

			}

			// if the product validation is still valid then we must return the cartdata for each project.
			// we must then lock the project.
			if (($projectFailedValidationCount == 0) && ($projectsHaveNoExpiredAssets) && ($pBasketAPIWorkFlowType == TPX_BASKETWORKFLOWTYPE_LOWLEVELAPIEXTERNALCHECKOUT))
			{
				$lockProjectParams = Array('projectreflist' => $pParamArray['projectreflist'], 'lockperiod' => $pParamArray['lockperiod']);
				$lockProjectResult = self::lockProject($pParamArray);

				foreach ($orderDataResultArray['items'] as $lineItem)
				{
					$returnCartDataArray = Array();
					$returnCartDataArray = $pParamArray['cartdata'][0];
					unset($returnCartDataArray['items']);

					$projectRef = $lineItem['projectref'];

					$returnCartDataArray['items'] = $lineItem;
					$returnArray[$projectRef]['cartdata'] = $returnCartDataArray;
				}
			}
		}
		else
		{
			$returnArray['result'] = TPX_ONLINE_ERROR_EXTERNALCHECKOUTERROR;
		}

		return $returnArray;
	}

	static function prepareCartData($pCartItemArray, $pBasketAPIWorkFlowType, $pHighLevelBasketRef)
	{
		$orderData = Array();
		$cartItemCount = count($pCartItemArray);

		$headerItem = $pCartItemArray[0];

		$headerArray = Array();
        $headerArray['apiversion'] = 6;
		$headerArray['appversion'] = $headerItem['appversion'];
		$headerArray['appdataversion'] = $headerItem['appdataversion'];
		$headerArray['uuid'] = '';
		$headerArray['userid'] = $headerItem['userid'];
		$headerArray['languagecode'] = $headerItem['languagecode'];
		$headerArray['batchref'] = $headerItem['batchref'];
		$headerArray['basketapiworkflowtype'] = $pBasketAPIWorkFlowType;
		$headerArray['highlevelbasketref'] = $pHighLevelBasketRef;
		$headerArray['canuseexternalcart'] = 1;
		$headerArray['outputdeliverymethods'] = 'RAWUPLOAD';
		$headerArray['ownercode'] = $headerItem['ownercode'];
		$headerArray['groupcode'] = $headerItem['groupcode'];
		$headerArray['groupdata'] = $headerItem['groupdata'];
		$headerArray['groupname'] = '';
		$headerArray['groupaddress1'] = '';
		$headerArray['groupaddress2'] = '';
		$headerArray['groupaddress3'] = '';
		$headerArray['groupaddress4'] = '';
		$headerArray['groupaddresscity'] = '';
		$headerArray['groupaddresscounty'] = '';
		$headerArray['groupaddressstate'] = '';
		$headerArray['groupemailaddress'] = '';
		$headerArray['grouptelephonenumber'] = '';
		$headerArray['groupcontactfirstname'] = '';
		$headerArray['groupcontactlastname'] = '';
		$headerArray['grouppostcode'] = '';
		$headerArray['groupcountrycode'] = '';
		$headerArray['devicesettings'] = 'unknown';
		$headerArray['projectcount'] = $cartItemCount;
		$headerArray['source'] = TPX_SOURCE_ONLINE;
		$headerArray['ssotoken'] = $headerItem['ssotoken'];
		$headerArray['ssoprivatedata'] = $headerItem['ssoprivatedata'];

		$orderData['headerarray'] = $headerArray;
		$orderData['cartitemcount'] = $cartItemCount;

		$cartArray = Array();

		for ($i = 0; $i < $cartItemCount; $i++)
		{
			$item = $pCartItemArray[$i]['items'][0];

			$cartArray['source'] = TPX_SOURCE_ONLINE;
			$cartArray['productcode'] = $item['productcode'];
			$cartArray['productname'] = $item['productname'];
			$cartArray['productskucode'] = $item['productskucode'];
			$cartArray['productdefaultpagecount'] = $item['productdefaultpagecount'];
			$cartArray['producttaxlevel'] = $item['producttaxlevel'];
			$cartArray['productunitcost'] = $item['productunitcost'];
			$cartArray['productunitweight'] = $item['productunitweight'];
			$cartArray['projectname'] = $item['projectname'];
			$cartArray['productheight'] = $item['productheight'];
			$cartArray['productwidth'] = $item['productwidth'];
			$cartArray['covercode'] = $item['covercode'];
			$cartArray['papercode'] = $item['papercode'];
			$cartArray['pagecount'] = $item['pagecount'];
			$cartArray['uploadref'] = $item['uploadref'];
			$cartArray['projectref'] = $item['projectref'];
			$cartArray['projectreforig'] = $item['projectreforig'];
			$cartArray['producttype'] = $item['producttype'];

			$productOptions = TPX_PRODUCTOPTION_PRICING_NON;

			if ($item['producttype'] == TPX_PRODUCTCOLLECTIONTYPE_SINGLEPRINTS)
			{
				if (array_key_exists('productoptions', $item))
				{
					$productOptions = $item['productoptions'];
				}
				else
				{
					$productOptions = TPX_PRODUCTOPTION_PRICING_PERPICTURE;
				}
			}

			$cartArray['productoptions'] = $productOptions;
			$cartArray['productspreadformat'] = $item['productspreadformat'];
			$cartArray['productpageformat'] = $item['productpageformat'];
			$cartArray['productoutputformat'] = $item['productoutputformat'];
			$cartArray['productcover1format'] = $item['productcover1format'];
			$cartArray['productcover2format'] = $item['productcover2format'];
			$cartArray['collectioncode'] = $item['collectioncode'];
			$cartArray['collectionname'] = $item['collectionname'];
			$cartArray['shareid'] = $item['shareid'];
			$cartArray['origorderitemid'] = $item['origorderitemid'];
			$cartArray['uploadgroupcode'] = $item['uploadgroupcode'];
			$cartArray['uploadorderid'] = $item['uploadorderid'];
			$cartArray['uploadordernumber'] = $item['uploadordernumber'];
			$cartArray['uploadorderitemid'] = $item['uploadorderitemid'];
			$cartArray['canupload'] = $item['canupload'];
			$cartArray['previewsonline'] = $item['previewsonline'];
			$cartArray['uploadappversion'] = $item['uploadappversion'];
			$cartArray['uploadappplatform'] = $item['uploadappplatform'];
			$cartArray['uploadappcputype'] = $item['uploadappcputype'];
			$cartArray['uploadapposversion'] = $item['uploadapposversion'];
			$cartArray['projectstarttime'] = $item['projectstarttime'];
			$cartArray['projectduration'] = $item['projectduration'];
			$cartArray['uploaddatasize'] = $item['uploaddatasize'];
			$cartArray['uploadduration'] = $item['uploadduration'];
			$cartArray['productcollectionorigownercode'] = $item['productcollectionorigownercode'];
			$cartArray['externalassets'] = $item['externalassets'];
			$cartArray['pictures'] = $item['pictures'];
			$cartArray['calendarcustomisations'] = $item['calendarcustomisations'];

			if (array_key_exists('aicomponent', $item))
			{
				$cartArray['aicomponent'] = $item['aicomponent'];
			}

			$cartArray['projectaimode'] = $item['projectaimode'];
			$cartArray['components'] = $item['components'];

			$orderData['cartarray'][] = $cartArray;

		}

		return $orderData;
	}

	static function getBasketContentsForCheckOut($pBasketRef)
	{
    	$basketContentsArray = array('projectreflist' => array(), 'cartdata' => array(), 'lockperiod' => 0, 'userid' => 0);

        $dbObj = DatabaseObj::getGlobalDBConnection();
		$userID = 0;
		$basketRef = $pBasketRef;

		// we must check to see if there is a valid user session for the current basketref
		$highLevelBasketUserSesionResultArray = AuthenticateObj::checkHighLevelBasketSessionActive($pBasketRef);

		if (($highLevelBasketUserSesionResultArray['result'] == '') && ($highLevelBasketUserSesionResultArray['sessionactive'] == 1))
		{
			$userID = $highLevelBasketUserSesionResultArray['userid'];
		}

        if ($dbObj)
        {
            if ($stmt = $dbObj->prepare("SELECT `groupcode`, `basketref`, `projectref`, `projectdata`, `projectdatalength`, `userid` FROM `ONLINEBASKET` WHERE ((`basketref` = ?) OR (`userid` = ?)) AND `inbasket` = 1"))
            {
				if ($stmt->bind_param('si', $pBasketRef, $userID))
				{
					if ($stmt->execute())
					{
						if ($stmt->bind_result($groupCode, $basketRef, $projectRef, $projectData, $projectDataLength, $userID))
						{
							while ($stmt->fetch())
							{
								// we have the projectdata data now unserialize it back into an array
								if ($projectDataLength > 0)
								{
									$projectData = gzuncompress($projectData, $projectDataLength);
								}

								$projectItem = unserialize($projectData);

								$basketContentsArray['projectreflist'][] = $projectRef;
								$basketContentsArray['cartdata'][] = $projectItem;
								$basketContentsArray['userid'] = $userID;
								$basketContentsArray['groupcode'] = $groupCode;
								$basketContentsArray['basketref'] = $basketRef;

							}
						}
						else
						{
							$error = __FUNCTION__ . ' bind_result: ' . $dbObj->error;
						}
					}
					else
					{
						$error = __FUNCTION__ . ' execute: ' . $dbObj->error;
					}
				}
				else
				{
					$error = __FUNCTION__ . ' bind: ' . $dbObj->error;
				}

                $stmt->free_result();
                $stmt->close();
                $stmt = null;
            }
            else
            {
                $error = __FUNCTION__ . ' prepare: ' . $dbObj->error;
            }
            $dbObj->close();
        }

    	return $basketContentsArray;
	}

	static function clearProjectBatchRef($pParamArray)
    {
    	$resultArray = array();

    	$postParam = array(
			'cmd' => 'CLEARPROJECTBATCHREF',
			'data' => array('projectreflist' => $pParamArray['projectreflist']));

		$curlPutResultArray = self::curlPutToTaopixOnline($postParam);

		if ($curlPutResultArray['error'] == '')
		{
			$resultArray['error'] = '';
			$resultArray['projectreflist'] = $curlPutResultArray['data']['projectreflist'];
		}
		else
		{
			$resultArray['error'] = $curlPutResultArray['error'];
		}

		return $resultArray;
    }

	static function createBasketRecord($pWebBrandCode, $pGroupCode, $pBasketRef)
	{
		$resultArray = array();
        $result = '';
        $resultParam = '';
        $basketRecordID = 0;
        $expirationTime = 0;

        $dbObj = DatabaseObj::getGlobalDBConnection();

		$productSelectorBrowserUTC = UtilsObj::getGETParam('prtz', UtilsObj::getPOSTParam('prtz', 0));

		if ($dbObj)
		{
			if (($pBasketRef != TPX_ONLINE_BASKETAPI_DEFAULTBASKETREF) && ($pBasketRef != '') && ($productSelectorBrowserUTC == 0))
			{
				// we need to create a place holder record in the basket table when the user signs in or registers from the online desinger.
				// at this point we should already have a valid basket ref so we need the expire date from the other basket records and
				// assign it to the place holder record.

				if ($stmt = $dbObj->prepare('SELECT `basketexpiredate` FROM `ONLINEBASKET` WHERE `basketref` = ? ORDER BY `id` DESC'))
				{
					if ($stmt->bind_param('s', $pBasketRef))
					{
						if ($stmt->bind_result($formattedBasketExpireDate))
						{
							if ($stmt->execute())
							{
								$stmt->fetch();
							}
							else
							{
								$result = 'str_DatabaseError';
								$resultParam = 'createBasketRecord retrieve basketexpiredate execute ' . $dbObj->error;
							}
						}
						else
						{
							$result = 'str_DatabaseError';
							$resultParam = 'createBasketRecord retrieve basketexpiredate bind result ' . $dbObj->error;
						}
					}
					else
					{
						// could not bind parameters
						$result = 'str_DatabaseError';
						$resultParam = 'createBasketRecord retrieve basketexpiredate bind ' . $dbObj->error;
					}

					$stmt->free_result();
					$stmt->close();
					$stmt = null;
				}
				else
				{
					// could not prepare statement
					$result = 'str_DatabaseError';
					$resultParam = 'createBasketRecord retrieve basketexpiredate prepare ' . $dbObj->error;
				}
			}
			else
			{
				$expirationTime = self::generateBasketCookieExpiryDate($productSelectorBrowserUTC, $pWebBrandCode);
				$formattedBasketExpireDate = date('Y-m-d H:i:s', $expirationTime);
			}

			if ($result == '')
			{
				if ($stmt = $dbObj->prepare('INSERT INTO `ONLINEBASKET` (`id`, `datecreated`, `basketexpiredate`, `inbasket`, `groupcode`) VALUES (0, NOW(), ?, 0, ?)'))
				{
					if ($stmt->bind_param('ss', $formattedBasketExpireDate, $pGroupCode))
					{
						if ($stmt->execute())
						{
							$basketRecordID = $dbObj->insert_id;
						}
						else
						{
							// could not execute statement
							$result = 'str_DatabaseError';
							$resultParam = 'createBasketRecord execute ' . $dbObj->error;
						}
					}
					else
					{
						$result = 'str_DatabaseError';
						$resultParam = 'createBasketRecord bind ' . $dbObj->error;
					}
					$stmt->free_result();
					$stmt->close();
					$stmt = null;
				}
				else
				{
					// could not prepare statement
					$result = 'str_DatabaseError';
					$resultParam = 'createBasketRecord prepare ' . $dbObj->error;
				}
			}

			$dbObj->close();
		}
		else
		{
			// could not open database connection
			$result = 'str_DatabaseError';
			$resultParam = 'createBasketRecord connect ' . $dbObj->error;
		}

		$resultArray['result'] = $result;
        $resultArray['resultparam'] = $resultParam;
        $resultArray['basketrecordid'] = $basketRecordID;
        $resultArray['basketexpiredate'] = $expirationTime;

        return $resultArray;
	}

	static function updateBasketRecordBasketRef($pRecordID, $pBasketRef, $pUserID, $pWebBrandCode, $pGroupCode)
	{
		$resultArray = array();
        $result = '';
        $resultParam = '';

        $dbObj = DatabaseObj::getGlobalDBConnection();

        if ($dbObj)
		{
			if ($stmt = $dbObj->prepare('UPDATE `ONLINEBASKET` SET `webbrandcode` = ?, `groupcode` = ?, `basketref` = ?, `userid` = ? WHERE `id` = ?'))
			{
				if ($stmt->bind_param('sssii', $pWebBrandCode, $pGroupCode, $pBasketRef, $pUserID, $pRecordID))
				{
					if (! $stmt->execute())
					{
						// could not execute statement
						$result = 'str_DatabaseError';
						$resultParam = 'updateBasketRecordBasketRefAndToken execute ' . $dbObj->error;
					}
				}
				else
				{
					$result = 'str_DatabaseError';
					$resultParam = 'updateBasketRecordBasketRefAndToken bind ' . $dbObj->error;
				}

				$stmt->free_result();
				$stmt->close();
				$stmt = null;
			}
			else
			{
				// could not prepare statement
				$result = 'str_DatabaseError';
				$resultParam = 'updateBasketRecordBasketRefAndToken prepare ' . $dbObj->error;
			}

			$dbObj->close();
		}
		else
		{
			// could not open database connection
			$result = 'str_DatabaseError';
			$resultParam = 'updateBasketRecordBasketRefAndToken connect ' . $dbObj->error;
		}

		$resultArray['result'] = $result;
        $resultArray['resultparam'] = $resultParam;

        return $resultArray;
	}

	static function updateBasketRecordBasketRefAndToken($pRecordID, $pOnlineBasketUID, $pBasketRef, $pUserID, $pWebBrandCode, $pGroupCode)
	{
		$resultArray = array();
        $result = '';
        $resultParam = '';

		$seconds = time();
        $rand = mt_rand(0, $seconds);
        $returnToken = md5($seconds . $rand);
        $tokenToStore = '';

		if ($pOnlineBasketUID != '')
		{
        	$tokenToStore = str_rot13($returnToken . $pOnlineBasketUID);
		}

        $dbObj = DatabaseObj::getGlobalDBConnection();

        if ($dbObj)
		{
			if ($stmt = $dbObj->prepare('UPDATE `ONLINEBASKET` SET `webbrandcode` = ?, `groupcode` = ?, `basketref` = ?, `token` = ?, `userid` = ? WHERE `id` = ?'))
			{
				if ($stmt->bind_param('ssssii', $pWebBrandCode, $pGroupCode, $pBasketRef, $tokenToStore, $pUserID, $pRecordID))
				{
					if (! $stmt->execute())
					{
						// could not execute statement
						$result = 'str_DatabaseError';
						$resultParam = 'updateBasketRecordBasketRefAndToken execute ' . $dbObj->error;
					}
				}
				else
				{
					$result = 'str_DatabaseError';
					$resultParam = 'updateBasketRecordBasketRefAndToken bind ' . $dbObj->error;
				}

				$stmt->free_result();
				$stmt->close();
				$stmt = null;
			}
			else
			{
				// could not prepare statement
				$result = 'str_DatabaseError';
				$resultParam = 'updateBasketRecordBasketRefAndToken prepare ' . $dbObj->error;
			}

			$dbObj->close();
		}
		else
		{
			// could not open database connection
			$result = 'str_DatabaseError';
			$resultParam = 'updateBasketRecordBasketRefAndToken connect ' . $dbObj->error;
		}

		$resultArray['result'] = $result;
        $resultArray['resultparam'] = $resultParam;
        $resultArray['token'] = $returnToken;

        return $resultArray;
	}

	static function lookUpBasketFromToken($pOnlineBasketUID, $pToken)
	{
    	$returnArray = array();
		$result = '';
		$resultParam = '';
		$basketRef = '';
		$groupCode = '';

        $dbObj = DatabaseObj::getGlobalDBConnection();

		$lookUpToken = str_rot13($pToken . $pOnlineBasketUID);

        if ($dbObj)
        {
            if ($stmt = $dbObj->prepare("SELECT `groupcode`, `basketref` FROM `ONLINEBASKET` WHERE `token` = ?"))
            {
				if ($stmt->bind_param('s', $lookUpToken))
				{
					if ($stmt->execute())
					{
						if ($stmt->bind_result($groupCode, $basketRef))
						{
							$stmt->fetch();
						}
						else
						{
							$result = 'str_DatabaseError';
							$resultParam = 'lookUpBasketFromToken bind result ' . $dbObj->error;
						}
					}
					else
					{
						$result = 'str_DatabaseError';
						$resultParam = 'lookUpBasketFromToken execute ' . $dbObj->error;
					}
				}
				else
				{
					$result = 'str_DatabaseError';
					$resultParam = 'lookUpBasketFromToken bind param ' . $dbObj->error;
				}

                $stmt->free_result();
                $stmt->close();
                $stmt = null;
            }
            else
            {
                $result = 'str_DatabaseError';
				$resultParam = 'lookUpBasketFromToken prepare ' . $dbObj->error;
            }
            $dbObj->close();
        }

		$returnArray['result'] = $result;
		$returnArray['resultparam'] = $resultParam;
		$returnArray['basketref'] = $basketRef;
		$returnArray['groupcode'] = $groupCode;

    	return $returnArray;
	}

	static function emptyBasketToken($pBasketRef)
	{
		$resultArray = array();
        $result = '';
        $resultParam = '';

        $dbObj = DatabaseObj::getGlobalDBConnection();

        if ($dbObj)
		{
			if ($stmt = $dbObj->prepare('UPDATE `ONLINEBASKET` SET `token` = "" WHERE `basketref` = ?'))
			{
				if ($stmt->bind_param('s', $pBasketRef))
				{
					if (! $stmt->execute())
					{
						// could not execute statement
						$result = 'str_DatabaseError';
						$resultParam = 'emptyBasketToken execute ' . $dbObj->error;
					}
				}
				else
				{
					$result = 'str_DatabaseError';
					$resultParam = 'emptyBasketToken bind ' . $dbObj->error;
				}

				$stmt->free_result();
				$stmt->close();
				$stmt = null;
			}
			else
			{
				// could not prepare statement
				$result = 'str_DatabaseError';
				$resultParam = 'emptyBasketToken prepare ' . $dbObj->error;
			}

			$dbObj->close();
		}
		else
		{
			// could not open database connection
			$result = 'str_DatabaseError';
			$resultParam = 'emptyBasketToken connect ' . $dbObj->error;
		}

		$resultArray['result'] = $result;
        $resultArray['resultparam'] = $resultParam;

        return $resultArray;
	}

    static function addProjectToOnlineBasket($pParamArray)
    {
        $resultArray = array();
        $result = '';
        $resultParam = '';

        $dbObj = DatabaseObj::getGlobalDBConnection();

        if ($dbObj)
		{
			if ($stmt = $dbObj->prepare('UPDATE `ONLINEBASKET` SET `webbrandcode` = ?, `groupcode` = ?, `basketref` = ?, `basketexpiredate` = ?, `projectref` = ?, `userid` = ?,
					`projectname` = ?, `collectioncode` = ?, `collectionname` = ?, `layoutcode` = ?, `layoutname` = ?, `projectdata` = ?, `inbasket` = -1 WHERE `id` = ?'))
			{
				if ($stmt->bind_param('sssssissssssi', $pParamArray['webbrandcode'], $pParamArray['groupcode'], $pParamArray['basketref'], $pParamArray['basketexpiredate'], $pParamArray['projectref'],
					$pParamArray['userid'], $pParamArray['projectname'], $pParamArray['collectioncode'], $pParamArray['collectionname'], $pParamArray['layoutcode'],
					$pParamArray['layoutname'], $pParamArray['projectdata'], $pParamArray['basketrecordid']))
				{
					if ($stmt->execute())
					{
						if ($stmt2 = $dbObj->prepare('UPDATE `ONLINEBASKET` SET `basketexpiredate` = ? WHERE `basketref` = ?'))
						{
							if ($stmt2->bind_param('ss', $pParamArray['basketexpiredate'], $pParamArray['basketref']))
							{
								if (!$stmt2->execute())
								{
									// could not execute statement
									$result = 'str_DatabaseError';
									$resultParam = 'addProjectToOnlineBasket updateBasketExpire execute ' . $dbObj->error;
								}
							}
							else
							{
								// could not bind parameters
								$result = 'str_DatabaseError';
								$resultParam = 'addProjectToOnlineBasket updateBasketExpire bind ' . $dbObj->error;
							}
						}
						else
						{
							// could not prepare statement
							$result = 'str_DatabaseError';
							$resultParam = 'addProjectToOnlineBasket updateBasketExpire prepare ' . $dbObj->error;
						}

						$stmt2->free_result();
						$stmt2->close();
						$stmt2 = null;
					}
					else
					{
						// could not execute statement
						$result = 'str_DatabaseError';
						$resultParam = 'addProjectToOnlineBasket execute ' . $dbObj->error;
					}
				}
				else
				{
					// could not bind parameters
					$result = 'str_DatabaseError';
					$resultParam = 'addProjectToOnlineBasket bind ' . $dbObj->error;
				}

				$stmt->free_result();
				$stmt->close();
				$stmt = null;
			}
			else
			{
				// could not prepare statement
				$result = 'str_DatabaseError';
				$resultParam = 'addProjectToOnlineBasket prepare ' . $dbObj->error;
			}

			$dbObj->close();
		}
		else
		{
			// could not open database connection
			$result = 'str_DatabaseError';
			$resultParam = 'addProjectToOnlineBasket connect ' . $dbObj->error;
		}

		if ($pParamArray['userid'] > 0)
		{
			self::updateUserIDBasketRefForProjectsInBasket($pParamArray['userid'], $pParamArray['basketref']);
		}

		$resultArray['result'] = $result;
        $resultArray['resultparam'] = $resultParam;

        return $resultArray;
    }

    static function addDuplicateProjectToBasket($pParamArray)
    {
        $resultArray = array();
        $result = '';
        $resultParam = '';

        $dbObj = DatabaseObj::getGlobalDBConnection();

        if ($dbObj)
		{
			if ($stmt = $dbObj->prepare('INSERT INTO `ONLINEBASKET` (`id`, `datecreated`, `webbrandcode`,`groupcode`, `basketref`, `basketexpiredate`, `projectref`, `userid`,
					`projectname`, `collectioncode`, `collectionname`, `layoutcode`, `layoutname`, `saved`, `projectdata`) VALUES (0, now(), ?,?,?,?,?,?,?,?,?,?,?,?,?)'))
			{
				if ($stmt->bind_param('sssssisssssis', $pParamArray['webbrandcode'], $pParamArray['groupcode'], $pParamArray['basketref'], $pParamArray['basketexpiredate'], $pParamArray['projectref'],
					$pParamArray['userid'], $pParamArray['projectname'], $pParamArray['collectioncode'], $pParamArray['collectionname'], $pParamArray['layoutcode'],
					$pParamArray['layoutname'], $pParamArray['saved'], $pParamArray['projectdata']))
				{
					if (! $stmt->execute())
					{
						// could not execute statement
						$result = 'str_DatabaseError';
						$resultParam = 'addDuplicateProjectToBasket execute ' . $dbObj->error;
					}
				}
				else
				{
					// could not bind parameters
					$result = 'str_DatabaseError';
					$resultParam = 'addDuplicateProjectToBasket bind ' . $dbObj->error;
				}

				$stmt->free_result();
				$stmt->close();
				$stmt = null;
			}
			else
			{
				// could not prepare statement
				$result = 'str_DatabaseError';
				$resultParam = 'addDuplicateProjectToBasket prepare ' . $dbObj->error;
			}

			$dbObj->close();
		}
		else
		{
			// could not open database connection
			$result = 'str_DatabaseError';
			$resultParam = 'addDuplicateProjectToBasket connect ' . $dbObj->error;
		}

		$resultArray['result'] = $result;
        $resultArray['resultparam'] = $resultParam;

        return $resultArray;
    }

    static function getProjectFromBasketToDuplicate($pOriginalProjectref, $pNewProjectRef, $pNewProjectName)
	{
    	$returnArray = array();
		$result = '';
		$resultParam = '';

        $dbObj = DatabaseObj::getGlobalDBConnection();

        if ($dbObj)
        {
            if ($stmt = $dbObj->prepare("SELECT `webbrandcode`, `groupcode`, `basketref`, `basketexpiredate`, `userid`, `collectioncode`, `collectionname`, `layoutcode`, `layoutname`, `saved` FROM `ONLINEBASKET` WHERE `projectref` = ?"))
            {
				if ($stmt->bind_param('s', $pOriginalProjectref))
				{
					if ($stmt->execute())
					{
						if ($stmt->bind_result($webBrandCode, $groupCode, $basketRef, $basketExpireDate, $userID, $collectionCode, $collectionName, $layoutCode, $layoutName, $saved))
						{
							if ($stmt->fetch())
							{
								$returnArray['webbrandcode'] = $webBrandCode;
								$returnArray['groupcode'] = $groupCode;
								$returnArray['basketref'] = $basketRef;
								$returnArray['basketexpiredate'] = $basketExpireDate;
								$returnArray['projectref'] = $pNewProjectRef;
								$returnArray['userid'] = $userID;
								$returnArray['projectname'] = $pNewProjectName;
								$returnArray['collectioncode'] = $collectionCode;
								$returnArray['collectionname'] = $collectionName;
								$returnArray['layoutcode'] = $layoutCode;
								$returnArray['layoutname'] = $layoutName;
								$returnArray['saved'] = $saved;
								$returnArray['projectdata'] = '';
							}
						}
						else
						{
							$result = 'str_DatabaseError';
							$resultParam = 'getProjectFromBasketToDuplicate bind result ' . $dbObj->error;
						}
					}
					else
					{
						$result = 'str_DatabaseError';
						$resultParam = 'getProjectFromBasketToDuplicate execute ' . $dbObj->error;
					}
				}
				else
				{
					$result = 'str_DatabaseError';
					$resultParam = 'getProjectFromBasketToDuplicate bind param ' . $dbObj->error;
				}

                $stmt->free_result();
                $stmt->close();
                $stmt = null;
            }
            else
            {
                $result = 'str_DatabaseError';
				$resultParam = 'getProjectFromBasketToDuplicate prepare ' . $dbObj->error;
            }
            $dbObj->close();
        }

		$returnArray['result'] = $result;
		$returnArray['resultparam'] = $resultParam;

    	return $returnArray;
	}

    static function updateProjectInOnlineBasket($pProjectRef, $pUserID, $pProjectDataArray, $pProjectName)
    {
    	$resultArray = array();
        $result = '';
        $resultParam = '';

    	$serializedProjectData = serialize($pProjectDataArray);
        $serializedProjectDataLength = strlen($serializedProjectData);

        if ($serializedProjectDataLength > 15728640)
        {
            $serializedProjectData = gzcompress($serializedProjectData, 9);
        }
        else
        {
            $serializedProjectDataLength = 0;
        }

    	$dbObj = DatabaseObj::getGlobalDBConnection();

        if ($dbObj)
        {
			if ($stmt = $dbObj->prepare('UPDATE `ONLINEBASKET` SET `userid` = ?, `projectdata` = ?, `projectdatalength` = ?, `projectname` = ?, `inbasket` = 1 WHERE `projectref` = ?'))
			{
				if ($stmt->bind_param('isiss',  $pUserID, $serializedProjectData, $serializedProjectDataLength, $pProjectName, $pProjectRef))
				{
					if (! $stmt->execute())
					{
						// could not bind parameters
						$result = 'str_DatabaseError';
						$resultParam = 'updateProjectInOnlineBasket section execute ' . $dbObj->error;
					}
				}
				else
				{
					// could not bind parameters
					$result = 'str_DatabaseError';
					$resultParam = 'updateProjectInOnlineBasket section bind ' . $dbObj->error;
				}

				$stmt->free_result();
				$stmt->close();
				$stmt = null;
			}
			else
			{
				$result = 'str_DatabaseError';
				$resultParam = 'updateProjectInOnlineBasket section prepare ' . $dbObj->error;
			}

            $dbObj->close();
        }

		$resultArray['result'] = $result;
		$resultArray['resultparam'] = $resultParam;

		return $resultArray;
    }

    static function retrieveBasketContents($pBasketRef, $pInBasket, $pIncludeProjectPreview, $pLocale = '')
    {
    	require_once '../Utils/UtilsLocalization.php';

    	$error = '';
    	$userID = 0;
    	$totalBasketCount = 0;
    	$basketContentsArray = array('items' => array(), 'basketcount' => 0);

        if ($pBasketRef != TPX_ONLINE_BASKETAPI_DEFAULTBASKETREF)
		{
			$dbObj = DatabaseObj::getGlobalDBConnection();

			// we must check to see if there is a valid user session for the current basketref
			$highLevelBasketUserSesionResultArray = AuthenticateObj::checkHighLevelBasketSessionActive($pBasketRef);

			if (($highLevelBasketUserSesionResultArray['result'] == '') && ($highLevelBasketUserSesionResultArray['sessionactive'] == 1))
			{
				$userID = $highLevelBasketUserSesionResultArray['userid'];
			}

			if ($dbObj)
			{
				if ($stmt = $dbObj->prepare("SELECT `datecreated`, `groupcode`, `projectref`, `projectname`, `collectioncode`, `collectionname`, `layoutcode`, `layoutname`, `saved`, `dateofpurge` FROM `ONLINEBASKET` WHERE ((`basketref` = ?) OR (`userid` = ?)) AND `inbasket` = ? AND `projectref` != ''"))
				{
					if ($stmt->bind_param('sii', $pBasketRef, $userID, $pInBasket))
					{
						if ($stmt->execute())
						{
							$stmt->store_result();

							$totalBasketCount = $stmt->num_rows;

							if ($stmt->bind_result($dateCreated, $groupCode, $projectRef, $projectName, $collectionCode, $collectionName, $layoutCode, $layoutName, $saved, $dateOfPurge))
							{
								while ($stmt->fetch())
								{
									$basketItem = array();
									$basketItem['dateandtimecreated'] = $dateCreated;
									$basketItem['groupcode'] = $groupCode;
									$basketItem['projectref'] = $projectRef;
									$basketItem['projectname'] = $projectName;
									$basketItem['collectioncode'] = $collectionCode;
									$basketItem['collectionname'] = $collectionName;
									$basketItem['layoutcode'] = $layoutCode;
									$basketItem['layoutname'] = $layoutName;
									$basketItem['projectpreviewthumbnail'] = '';
									$basketItem['dateofpurge'] = '0000-00-00 00:00:00' === $dateOfPurge ? '' : LocalizationObj::formatLocaleDateTime($dateOfPurge, $pLocale);
									if ($pInBasket == 0)
									{
										$basketItem['projectsaved'] = $saved;
									}

									$basketContentsArray['items'][] = $basketItem;
								}
							}
							else
							{
								$error = __FUNCTION__ . ' bind_result: ' . $dbObj->error;
							}
						}
						else
						{
							$error = __FUNCTION__ . ' execute: ' . $dbObj->error;
						}
					}
					else
					{
						$error = __FUNCTION__ . ' bind: ' . $dbObj->error;
					}

					$stmt->free_result();
					$stmt->close();
					$stmt = null;
				}
				else
				{
					$error = __FUNCTION__ . ' prepare: ' . $dbObj->error;
				}
				$dbObj->close();
			}

			if ($error == '')
			{
				usort($basketContentsArray['items'], array('self', 'sortDateCreated'));

				if (($pIncludeProjectPreview) && (count($basketContentsArray['items']) > 0))
				{
					// Build a list of all projects in the cart to be able to request the preview thumbnails for them all at once.
					$projectRefToRequestPreview = array_map(function($pItem)
					{
						return $pItem['projectref'];
					}, $basketContentsArray['items']);

					// Request the thumbnail URLs.
					$requestProjectPreviewThumbnailResult = UtilsObj::requestProjectPreviewThumbnail($projectRefToRequestPreview);

					if ($requestProjectPreviewThumbnailResult['error'] === '')
					{
						$projectThumbnailData = $requestProjectPreviewThumbnailResult['data'];

						// Loop over the items and inject the project preview thumbnail.
						foreach ($basketContentsArray['items'] as &$item)
						{
							if (array_key_exists($item['projectref'], $projectThumbnailData))
							{
								$projectPreviewThumbnailData = $projectThumbnailData[$item['projectref']];

								if ($projectPreviewThumbnailData['error'] === TPX_ONLINE_ERROR_NONE)
								{
									$item['projectpreviewthumbnail'] = $projectPreviewThumbnailData['thumbnail'];
								}
							}
							else
							{
								$item['layoutthumbnail'] = UtilsObj::getAssetRequest($item['layoutcode'], 'products');
							}
						}
					}
				}
			}
		}

        $basketContentsArray['basketcount'] = $totalBasketCount;

    	return $basketContentsArray;
    }

    static function retrieveBasketCount($pBasketRef)
    {
    	$error = '';
    	$userID = 0;
    	$totalBasketCount = 0;
    	$basketCountArray = array('error' => '', 'basketcount' => 0);

        $dbObj = DatabaseObj::getGlobalDBConnection();

		// we must check to see if there is a valid user session for the current basketref
		$highLevelBasketUserSesionResultArray = AuthenticateObj::checkHighLevelBasketSessionActive($pBasketRef);

		if (($highLevelBasketUserSesionResultArray['result'] == '') && ($highLevelBasketUserSesionResultArray['sessionactive'] == 1))
		{
			$userID = $highLevelBasketUserSesionResultArray['userid'];
		}

        if ($dbObj)
        {
            if ($stmt = $dbObj->prepare("SELECT `datecreated`, `groupcode`, `projectref`, `projectname`, `collectioncode`, `collectionname`, `layoutcode`, `layoutname`, `saved` FROM `ONLINEBASKET` WHERE ((`basketref` = ?) OR (`userid` = ?)) AND `inbasket` = 1 AND `projectref` != ''"))
            {
				if ($stmt->bind_param('si', $pBasketRef, $userID))
				{
					if ($stmt->execute())
					{
						$stmt->store_result();

						$totalBasketCount = $stmt->num_rows;
					}
					else
					{
						$error = __FUNCTION__ . ' execute: ' . $dbObj->error;
					}
				}
				else
				{
					$error = __FUNCTION__ . ' bind: ' . $dbObj->error;
				}

                $stmt->free_result();
                $stmt->close();
                $stmt = null;
            }
            else
            {
                $error = __FUNCTION__ . ' prepare: ' . $dbObj->error;
            }
            $dbObj->close();
        }

		$basketCountArray['error'] = $error;
        $basketCountArray['basketcount'] = $totalBasketCount;

    	return $basketCountArray;
    }

    static function sortDateCreated($pVal1, $pVal2)
	{
		$t1 = strtotime($pVal1['dateandtimecreated']);
		$t2 = strtotime($pVal2['dateandtimecreated']);

		return $t2 - $t1;
	}

    static function removeItemsFromBasket($pProjectRefList)
    {
    	$resultArray = array();
        $result = '';
        $resultParam = '';

    	$dbObj = DatabaseObj::getGlobalDBConnection();

        if ($dbObj)
        {
			if ($stmt = $dbObj->prepare('UPDATE `ONLINEBASKET` SET `inbasket` = 0 WHERE `projectref` IN ('. $pProjectRefList .')'))
			{
				if (! $stmt->execute())
				{
					// could not bind parameters
					$result = 'str_DatabaseError';
					$resultParam = 'removeItemsFromBasket section execute ' . $dbObj->error;
				}

				$stmt->free_result();
				$stmt->close();
				$stmt = null;
			}
			else
			{
				$result = 'str_DatabaseError';
				$resultParam = 'removeItemsFromBasket section prepare ' . $dbObj->error;
			}

            $dbObj->close();
        }

		$resultArray['result'] = $result;
		$resultArray['resultparam'] = $resultParam;

		return $resultArray;
    }

    static function updateProjectNameInBasket($pProjectRef, $pNewProjectName)
    {
    	$resultArray = array();
        $result = '';
        $resultParam = '';
        $projectData = '';
        $unserialisedProjectData = array();
        $serialisedProjectData = '';

    	$dbObj = DatabaseObj::getGlobalDBConnection();

        if ($dbObj)
        {
			if ($stmt = $dbObj->prepare('SELECT `projectdata` FROM `ONLINEBASKET` WHERE `projectref` = ?'))
			{
				if ($stmt->bind_param('s', $pProjectRef))
				{
					if ($stmt->execute())
					{
						if ($stmt->bind_result($projectData))
						{
							if ($stmt->fetch())
							{
								$unserialisedProjectData = unserialize($projectData);
							}
							else
							{
								// could not fetch
								$result = 'str_DatabaseError: Could not fetch';
								$resultParam = 'updateProjectNameInBasket select fetch' . $dbObj->error;
							}

						}
						else
						{
							// could not bind result
							$result = 'str_DatabaseError: Could not bind result';
							$resultParam = 'updateProjectNameInBasket select bind result ' . $dbObj->error;
						}
					}
					else
					{
						// could not execute
						$result = 'str_DatabaseError: Could not execute';
						$resultParam = 'updateProjectNameInBasket select execute ' . $dbObj->error;
					}
				}
				else
				{
					// could not bind parameters
					$result = 'str_DatabaseError: Could not bind parameters';
					$resultParam = 'updateProjectNameInBasket select bind ' . $dbObj->error;
				}

				$stmt->free_result();
				$stmt->close();
				$stmt = null;
			}
			else
			{
				$result = 'str_DatabaseError: Could not prepare';
				$resultParam = 'updateProjectNameInBasket select prepare ' . $dbObj->error;
			}

			if ($result == '')
			{
				// check to make sure that there was actually project data to unserialise and update
				if ($unserialisedProjectData != false)
				{
					// update the projectname in the unserialised data.
					$unserialisedProjectData['items'][0]['projectname'] = $pNewProjectName;

					// serialize the project data so it can be updated in the basket table.
					$serialisedProjectData = serialize($unserialisedProjectData);
				}

				if ($stmt = $dbObj->prepare('UPDATE `ONLINEBASKET` SET `projectname` = ?, `projectdata` = ? WHERE `projectref` = ?'))
				{
					if ($stmt->bind_param('sss', $pNewProjectName, $serialisedProjectData, $pProjectRef))
					{
						if (! $stmt->execute())
						{
							// could not execute
							$result = 'str_DatabaseError';
							$resultParam = 'updateProjectNameInBasket section execute ' . $dbObj->error;
						}
					}
					else
					{
						// could not bind parameters
						$result = 'str_DatabaseError';
						$resultParam = 'updateProjectNameInBasket section bind ' . $dbObj->error;
					}

					$stmt->free_result();
					$stmt->close();
					$stmt = null;
				}
				else
				{
					$result = 'str_DatabaseError';
					$resultParam = 'updateProjectNameInBasket section prepare ' . $dbObj->error;
				}
			}

            $dbObj->close();
        }

		$resultArray['result'] = $result;
		$resultArray['resultparam'] = $resultParam;

		return $resultArray;
    }

    static function emptyBasket($pBasketRef, $pForceKill)
    {
    	$resultArray = array();
        $result = '';
        $resultParam = '';
		$projectRefArray = array();

    	$dbObj = DatabaseObj::getGlobalDBConnection();

        if ($dbObj)
        {
			if ($stmt = $dbObj->prepare('SELECT `projectref` FROM `ONLINEBASKET` WHERE (`basketref` = ?) AND (`inbasket` = 1)'))
			{
				if ($stmt->bind_param('s', $pBasketRef))
				{
					if ($stmt->execute())
					{
						if ($stmt->bind_result($projectRef))
						{
							while ($stmt->fetch())
							{
								$projectRefArray[] = $projectRef;
							}
						}
						else
						{
							$result = __FUNCTION__ . ' bind_result: ' . $dbObj->error;
						}
					}
					else
					{
						$result = __FUNCTION__ . ' execute: ' . $dbObj->error;
					}
				}
				else
				{
					$result = __FUNCTION__ . ' bind: ' . $dbObj->error;
				}

				$stmt->free_result();
				$stmt->close();
				$stmt = null;
			}
			else
			{
				$result = __FUNCTION__ . ' prepare: ' . $dbObj->error;
			}

			$dbObj->close();
        }

        $formattedProjectRefList = '';

		foreach($projectRefArray as $projectRef)
		{
			$paramArray = array();
			$paramArray['projectreflist'] = array(0 => $projectRef);
			$paramArray['forcekill'] = $pForceKill;
			$paramArray['canunlock'] = 1;
			$paramArray['purgedays'] = 0;
			$paramArray['action'] = 'removefrombasket';
			$paramArray['basketref'] = $pBasketRef;

			$checkDeleteSessionArray = OnlineAPI_model::checkDeleteSession($paramArray);

			if ($checkDeleteSessionArray['error'] == '')
			{
				if (($checkDeleteSessionArray['projectitemarray'][$projectRef]['shoppingcartsessionref'] == 0) || ($pForceKill == 1))
				{
					$formattedProjectRefList .= "'" . $projectRef . "',";
				}
				else
				{
					$formattedProjectRefList = '';
					$result = TPX_ONLINE_ERROR_HIGHLEVELPROJECTACTIVECHECKOUTSESSION;
					$resultParam = TPX_ONLINE_ERROR_HIGHLEVELPROJECTACTIVECHECKOUTSESSION;
					break;
				}
			}
			else
			{
				$result = $checkDeleteSessionArray['error'];
				$resultParam = $checkDeleteSessionArray['error'];
			}
		}

		$formattedProjectRefList = substr($formattedProjectRefList, 0, -1);

		if ($formattedProjectRefList != '')
		{
			$removeItemArray = self::removeItemsFromBasket($formattedProjectRefList);

			if ($removeItemArray['result'] == '')
			{
				$projectRefArray = array('projectreflist' => $projectRefArray);

				$clearProjectBatchRefResult = self::clearProjectBatchRef($projectRefArray);

				if ($clearProjectBatchRefResult['error'] == '')
				{
					$result = $clearProjectBatchRefResult['error'];
					$resultParam = $clearProjectBatchRefResult['error'];
				}
			}
			else
			{
				$result = $removeItemArray['result'];
				$resultParam = $removeItemArray['resultparam'];
			}
		}

		$resultArray['result'] = $result;
		$resultArray['resultparam'] = $resultParam;

		return $resultArray;
    }

    static function displayLogin($pGroupCode, $pFromRegisterLink)
    {
		global $gSession;

        $ref = -1;
        $canCreateAccounts = 0;

		$licenseKeyArray = DatabaseObj::getLicenseKeyFromCode($pGroupCode);
		$canCreateAccounts = $licenseKeyArray['cancreateaccounts'];

		if ($pFromRegisterLink)
		{
			$ref = 0;
		}

        $resultArray['ref'] = $ref;
        $resultArray['info'] = '';
        $resultArray['cancreateaccounts'] = $canCreateAccounts;
        $resultArray['groupcode'] = $pGroupCode;
        $resultArray['login'] = UtilsObj::getPOSTParam('login');
		$resultArray['fromregisterlink'] = $pFromRegisterLink;

        UtilsObj::setSessionDeviceData();

        DatabaseObj::updateSession();

        return $resultArray;
    }

    static function updateUserIDBasketRefForProjectsInBasket($pUserID, $pBasketRef)
    {
		$resultArray = array();
        $result = '';
        $resultParam = '';
		$basketExpireDate = '0000-00-00 00:00:00';
		$projectRefListToUpdateArray = array();
		$guestUserIDListArray = array();

        $dbObj = DatabaseObj::getGlobalDBConnection();

        if ($dbObj)
        {
			// we need update the userid for all records using the current basketref
			if ($stmt = $dbObj->prepare('SELECT `basketexpiredate` FROM `ONLINEBASKET` WHERE `basketref` = ? ORDER BY `id` DESC'))
			{
				if ($stmt->bind_param('s', $pBasketRef))
				{
					if ($stmt->bind_result($basketExpireDate))
                    {
						if ($stmt->execute())
						{
							$stmt->fetch();
						}
						else
						{
							$result = 'str_DatabaseError';
							$resultParam = 'retrieve basketexpiredate execute ' . $dbObj->error;
						}
					}
					else
					{
						$result = 'str_DatabaseError';
						$resultParam = 'retrieve basketexpiredate bind result ' . $dbObj->error;
					}
				}
				else
				{
					// could not bind parameters
					$result = 'str_DatabaseError';
					$resultParam = 'retrieve basketexpiredate bind ' . $dbObj->error;
				}

				$stmt->free_result();
				$stmt->close();
				$stmt = null;
			}
			else
			{
				// could not prepare statement
				$result = 'str_DatabaseError';
				$resultParam = 'retrieve basketexpiredate prepare ' . $dbObj->error;
			}

			if ($result == '')
			{
				if ($stmt = $dbObj->prepare('SELECT `projectref`, `userid` FROM `ONLINEBASKET` WHERE `basketref` = ? AND `userid` < 0'))
				{
					if ($stmt->bind_param('s', $pBasketRef))
					{
						if ($stmt->bind_result($projectRef, $guestUserID))
						{
							if ($stmt->execute())
							{
								while ($stmt->fetch())
								{
									$guestUserIDListArray[] = $guestUserID;
									$projectRefListToUpdateArray[] = $projectRef;
								}
							}
							else
							{
								$result = 'str_DatabaseError';
								$resultParam = 'retrieve projectref execute ' . $dbObj->error;
							}
						}
						else
						{
							$result = 'str_DatabaseError';
							$resultParam = 'retrieve projectref bind result ' . $dbObj->error;
						}
					}
					else
					{
						// could not bind parameters
						$result = 'str_DatabaseError';
						$resultParam = 'retrieve projectref bind ' . $dbObj->error;
					}

					$stmt->free_result();
					$stmt->close();
					$stmt = null;
				}
				else
				{
					// could not prepare statement
					$result = 'str_DatabaseError';
					$resultParam = 'retrieve projectref prepare ' . $dbObj->error;
				}

				if (count($projectRefListToUpdateArray) > 0)
				{
					$updateOnlineProjectsUserIDResult = self::updateOnlineProjectsUserID($pUserID, $guestUserIDListArray);

					if ($updateOnlineProjectsUserIDResult['error'] == '')
					{
						$projectRefListToUpdateCount = count($projectRefListToUpdateArray);
						$bindParamsArray = array('i');
						$bindParamsArray = array_merge($bindParamsArray, array($pUserID), $projectRefListToUpdateArray);
						$sqlInParamString = '';

						for ($i = 0; $i < $projectRefListToUpdateCount; $i++)
						{
							$bindParamsArray[0] .= 's';
							$sqlInParamString .= ',?';
						}

						// remove first comma from the start of the string
						$sqlInParamString = substr($sqlInParamString, 1);

						// we need update the userid for all records using the current basketref
						if ($stmt = $dbObj->prepare("UPDATE `ONLINEBASKET` SET `userid` = ? WHERE `projectref` IN (" . $sqlInParamString . ")"))
						{
							$bindOK = call_user_func_array(array(&$stmt, 'bind_param'), UtilsObj::makeValuesReferenced($bindParamsArray));

							if ($bindOK)
							{
								if (! $stmt->execute())
								{
									$result = 'str_DatabaseError';
									$resultParam = 'updateUserIDBasketRefForProjectsInBasket userid execute ' . $dbObj->error;
								}
							}
							else
							{
								// could not bind parameters
								$result = 'str_DatabaseError';
								$resultParam = 'updateUserIDBasketRefForProjectsInBasket userid bind ' . $dbObj->error;
							}

							$stmt->free_result();
							$stmt->close();
							$stmt = null;
						}
						else
						{
							// could not prepare statement
							$result = 'str_DatabaseError';
							$resultParam = 'updateUserIDBasketRefForProjectsInBasket userid prepare ' . $dbObj->error;
						}
					}
					else
					{
						$result = $updateOnlineProjectsUserIDResult['error'];
						$resultParam = $updateOnlineProjectsUserIDResult['errorparam'];
					}
				}

				if ($result == '')
				{
					// we need update all records for the userid to use the most up to date basketref
					if ($stmt = $dbObj->prepare('UPDATE `ONLINEBASKET` SET `basketref` = ?, `basketexpiredate` = ? WHERE `userid` = ?'))
					{
						if ($stmt->bind_param('ssi', $pBasketRef, $basketExpireDate, $pUserID))
						{
							if (! $stmt->execute())
							{
								$result = 'str_DatabaseError';
								$resultParam = 'updateUserIDBasketRefForProjectsInBasket basketref execute ' . $dbObj->error;
							}
						}
						else
						{
							// could not bind parameters
							$result = 'str_DatabaseError';
							$resultParam = 'updateUserIDBasketRefForProjectsInBasket basketref bind ' . $dbObj->error;
						}

						$stmt->free_result();
						$stmt->close();
						$stmt = null;
					}
					else
					{
						// could not prepare statement
						$result = 'str_DatabaseError';
						$resultParam = 'updateUserIDBasketRefForProjectsInBasket basketref prepare ' . $dbObj->error;
					}
				}
			}

			$dbObj->close();
        }
		else
		{
			$result = 'str_DatabaseError';
			$resultParam = 'updateUserIDBasketRefForProjectsInBasket connect ' . $dbObj->error;
		}

        $resultArray['result'] = $result;
		$resultArray['resultparam'] = $resultParam;

		return $resultArray;
    }

    static function updateOnlineProjectsUserID($pUserID, $pGuestUserIDList)
    {
		$returnArray = array('error' => '', 'errorparam' => '');
		$error = '';
		$errorParam = '';

		$postParam = array(
			'cmd' => 'UPDATEHIGHLEVELPROJECTSUSERID',
			'data' => array(
				'userid' => $pUserID,
				'guestuserlist' => $pGuestUserIDList
			)
		);

		$curlPutResultArray = self::curlPutToTaopixOnline($postParam);

		// check if the cURL request failed
		if ($curlPutResultArray['error'] != '')
		{
			$error = $curlPutResultArray['error'];
		}
		else
		{
			// check for any errors returned from Online
			if ($curlPutResultArray['data']['error'] != '')
			{
				$error = $curlPutResultArray['data']['error'];
			}
		}

		$returnArray['error'] = $error;
		$returnArray['errorparam'] = $errorParam;

        return $returnArray;
    }

    static function generateBasketCookieExpiryDate($pBrowserUTC, $pWebBrandCode)
    {
		$hl_config = UtilsObj::readWebBrandConfigFile('../config/onlinebaskethighlevelapi.conf', $pWebBrandCode);
		$basketCookieExpiryDays = 60;

		if ((array_key_exists('BASKETCOOKIEEXPIRYDAYS', $hl_config)) && (($hl_config['BASKETCOOKIEEXPIRYDAYS'] != '') && ($hl_config['BASKETCOOKIEEXPIRYDAYS'] > 0)))
		{
			$basketCookieExpiryDays = $hl_config['BASKETCOOKIEEXPIRYDAYS'];
		}

    	$expirationTime = time() + (($basketCookieExpiryDays * 24) * 60 * 60); // 60 days in seconds

		$hourOffset = round(((int) $pBrowserUTC - time()) / (60 * 60), 2);
		$expirationTime = $expirationTime + ($hourOffset * 60 * 60);

		return $expirationTime;
    }

    static function generateBasketRef($pBasketRecordID)
    {
    	$recordID = (int) $pBasketRecordID;
    	$keyBase = rand(11, 15);

		list($usec, $sec) = explode(" ", microtime());
		$usecArray = explode('.', $usec);

		$basketRef = base_convert($recordID, 10, $keyBase) . self::generateBasketRefElement($recordID . dechex(self::generateBasketRefElement($usecArray[1])) .
			dechex(rand(0, 255)) . dechex(rand(0, self::generateBasketRefElement($usecArray[1])))) . self::generateBasketRefElement(dechex(rand(0, 255))) .
			dechex(rand(0, $usecArray[1]));

		return $basketRef;
    }

    static function generateBasketRefElement($pValue)
	{
		$newValue = '';

		$value = (string) $pValue;

		$count = strlen($value);

		for ($i = $count -2; $i >= 0; $i--)
		{
			$next = ord($value[$i + 1]);

			$xorResult = (ord($value[$i]) + $i) ^ ($next);

			$newValue .= $xorResult;
		}

		return $newValue;
	}

    static function getOrderItemsModifyStatus($pProjectRefArray, $pAction, $pBasketRef)
	{
		$error = '';
		$canModify = 1;
		$orderFound = false;

		$tempItemArray = array();

		$projectRefList = '';
		$projectRefCount = count($pProjectRefArray);

        if ($projectRefCount > 0)
        {
            for ($i = 0; $i < $projectRefCount; $i++)
            {
            	$projectRef = $pProjectRefArray[$i];

            	// set the default not found state
            	$tempItemArray[$projectRef]['error'] = '';
            	$tempItemArray[$projectRef]['projectref'] = $projectRef;
            	$tempItemArray[$projectRef]['sessionactive'] = false;
            	$tempItemArray[$projectRef]['sessiontype'] = '';
            	$tempItemArray[$projectRef]['projectexists'] = true;
            	$tempItemArray[$projectRef]['canmodify'] = $canModify;
            	$tempItemArray[$projectRef]['orderfound'] = $orderFound;
            	$tempItemArray[$projectRef]['shoppingcartsessionref'] = 0;
            	$tempItemArray[$projectRef]['projectlocked'] = 0;

            	$projectRefList .= "'" . $projectRef . "',";
            }

            //remove trailing commas
			$projectRefList = substr($projectRefList, 0, -1);
        }

        // get projectref from SESSIONDATA to find out if a project has been added to shopping cart
        $dbObj = DatabaseObj::getGlobalDBConnection();

        if ($dbObj)
        {
			$sql = "SELECT `projectref`, `canmodify`
					FROM `ORDERITEMS`
					WHERE `projectref` IN (" . $projectRefList . ")";

			$stmt = $dbObj->prepare($sql);

            if ($stmt)
            {
				if ($stmt->execute())
				{
					if ($stmt->bind_result($orderItemProjectRef, $canModify))
					{
						while ($stmt->fetch())
						{
							$tempItemArray[$orderItemProjectRef]['canmodify'] = $canModify;
							$tempItemArray[$orderItemProjectRef]['orderfound'] = true;
						}
					}
					else
					{
						$error = __FUNCTION__ . ' bind_result: ' . $dbObj->error;
					}
				}
				else
				{
					$error = __FUNCTION__ . ' execute: ' . $dbObj->error;
				}

                $stmt->free_result();
                $stmt->close();
                $stmt = null;
            }
            else
            {
                $error = __FUNCTION__ . ' prepare: ' . $dbObj->error;
            }
            $dbObj->close();
        }

		foreach ($tempItemArray as &$item)
		{
			if (($item['orderfound'] == false) || (($item['canmodify'] == 1) && (($pAction == 'editing') || ($pAction == 'removefrombasket'))))
			{
				if ($pAction == 'removefrombasket')
				{
					$checkShoppingCartSessionByBasketRefResult = self::checkShoppingCartSessionByBasketRef($pBasketRef);
					$item['shoppingcartsessionref'] = $checkShoppingCartSessionByBasketRefResult['shoppingcartsessionref'];
				}
				else
				{
					$cartSessionResult = self::checkShoppingCartSession($item['projectref']);
					$item['shoppingcartsessionref'] = $cartSessionResult['shoppingcartsessionref'];
				}
			}
		}

		return array('error' => $error, 'projectitemarray' => $tempItemArray);
	}

	static function curlPutToTaopixOnline($pPostParamArray)
	{
		global $ac_config;

		require_once('../libs/internal/curl/Curl.php');

		return CurlObj::sendByPut($ac_config['TAOPIXONLINEURL'], 'ProjectAPI.callback', $pPostParamArray);
	}

	static function isProductLayoutActive($pLayoutCode, $pProductList, $pProductCollectionCode, $pWorkFlowMode)
    {
    	$layoutActive = true;

    	$dbObj = DatabaseObj::getGlobalDBConnection();
		$collectionDeleted = 0;
        $collectionActive = 0;

		if ($dbObj)
		{
			if ($stmt = $dbObj->prepare('SELECT `active`, `deleted` FROM `APPLICATIONFILES` WHERE (`ref` = ?)'))
			{
				if ($stmt->bind_param('s', $pProductCollectionCode))
				{
					if ($stmt->bind_result($collectionActive, $collectionDeleted))
					{
						if ($stmt->execute())
						{
							$stmt->fetch();
						}
					}
				}

				$stmt->free_result();
				$stmt->close();
				$stmt = null;
			}

			$dbObj->close();
		}

    	if (($collectionDeleted == 0) && ($collectionActive == 1))
    	{
			foreach ($pProductList as $productLayout)
			{
				if ($productLayout['code'] === $pLayoutCode)
				{
					if ($productLayout['isactive'] == 0)
					{
						$layoutActive = false;
					}

					if (($pWorkFlowMode == TPX_PRODUCTCOLLECTIONTYPE_SINGLEPRINTS) && (count($productLayout['singleprintlist']) <= 0))
					{
						$layoutActive = false;
					}
					break;
				}
			}
        }
        else
        {
        	$layoutActive = false;
        }

		if ($layoutActive)
		{
			$productArray = DatabaseObj::getProductFromCollectionCodeAndLayoutCode($pProductCollectionCode, $pLayoutCode);

			if ($productArray['availableonline'] == 0)
			{
				$layoutActive = false;
			}
		}

        return $layoutActive;
    }


    static function checkBrowsers()
    {
    	$returnArray = array();
    	$browserSupported = true;
    	$browser = '';
    	$version = TPX_ONLINESUPPORTED_BROWSER_UNKNOW;
    	$platform = '';
    	$platformVersion = 0.0;
    	$httpUserAgent = $_SERVER['HTTP_USER_AGENT'];
    	$hideSafari = false;
    	$hideChrome = false;
		$hideFirefox = false;
		$hideEdge = false;
    	$hideSafariDownload  = false;
    	$hideChromeDownload  = false;
		$hideFirefoxDownload  = false;
		$hideEdgeDownload = false;

		// determine the browser version
    	if (stripos($httpUserAgent, 'OPR') !== false)
    	{
			$versionString = explode('/', stristr($httpUserAgent, 'OPR'));
            $versionArray = explode(' ', $versionString[1]);

			$version = $versionArray[0];
            $browser = TPX_ONLINESUPPORTED_BROWSER_OPERA;
		}
		else if (stripos($httpUserAgent, 'SamsungBrowser') !== false)
    	{
			// Since March 21, 2016, Samsung User-Agent String format includes 'SamsungBrowser' in Mobile and SmartTV's
			// http://developer.samsung.com/technical-doc/view.do?v=T000000203
			// these can be identified and the incompatable browser message can be displayed.
			// use of the SamsungBrowser will also depend upon the version of the operating system
			$versionString = explode('/', stristr($httpUserAgent, 'SamsungBrowser'));
            $versionArray = explode(' ', $versionString[1]);

			$version = $versionArray[0];
            $browser = TPX_ONLINESUPPORTED_BROWSER_SAMSUNG;
		}
		else if (preg_match("/MicroMessenger\/([0-9\.]+)\s+/i", $httpUserAgent, $matches))
		{
			// wechat browser
			$version = $matches[1];

			$browser = TPX_ONLINESUPPORTED_BROWSER_WECHAT;
		}
		else if (stripos($httpUserAgent, 'opera') !== false)
		{
			$result = stristr($httpUserAgent, 'opera');

			if(preg_match('/Version\/(10.*)$/', $result, $matches))
			{
				$version = $matches[1];
			}
			else if(preg_match('/\//', $result))
			{
				$versionString = explode('/', str_replace("(", " ", $result));
				$versionArray = explode(' ', $versionString[1]);
				$version = $versionArray[0];
			}
			else
			{
				$versionArray = explode(' ',stristr($result, 'opera'));
				$version = isset($versionArray[1]) ? $versionArray[1] : "";
			}

			$browser = TPX_ONLINESUPPORTED_BROWSER_OPERA;
		}
    	else if ((stripos($httpUserAgent, 'Trident') !== false) && (stripos($httpUserAgent, 'rv:') !== false))
    	{
            // Trident (MSHTML) is the microsoft layout engine for Windows versions of Internet Explorer
            // rv: gives the version of ie
            // only ie 11+ will contain 'Trident' and 'rv:' in the user agent string, older versions contain 'MSIE'
    		if (preg_match("/rv:([0-9\.a-z]+)/i", $httpUserAgent, $matches))
    		{
            	$version = $matches[1];
                $browser = TPX_ONLINESUPPORTED_BROWSER_INTERNETEXPLORER;
            }
    	}
    	else if ((stripos($httpUserAgent, 'msie') !== false) && (stripos($httpUserAgent, 'opera') === false))
    	{
            // Internet Explorer versions before 11
    		$versionString = explode(' ', stristr(str_replace(';' ,'; ' , $httpUserAgent), 'msie'));
        	$browser = TPX_ONLINESUPPORTED_BROWSER_INTERNETEXPLORER;
        	$version = str_replace(array('(' , ')' ,';'), '', $versionString[1]);
    	}
		else if(stripos($httpUserAgent,'iPhone') !== false)
    	{
    		$browser = TPX_ONLINESUPPORTED_BROWSER_IPHONE;

			if (stripos($httpUserAgent, 'criOS') !== false)
			{
				$versionString = explode(' ', stristr($httpUserAgent,'iPhone OS'));

				if (isset($versionString[2]) )
				{
					$version = $versionString[2];
					$version = str_replace('_', '.', $version);
				}
			}
			else if (stripos($httpUserAgent, 'FBAN/') !== false)
			{
                $browser = TPX_ONLINESUPPORTED_BROWSER_FBIOS;
			}
			else if (stripos($httpUserAgent, 'Version') !== false)
			{
				$versionString = explode('/', stristr($httpUserAgent, 'Version'));

				if (isset($versionString[1]))
				{
					$versionArray = explode(' ', $versionString[1]);
					$version = $versionArray[0];
				}
			}
			else
			{
				$versionString = explode(' ', stristr($httpUserAgent,'iPhone OS'));

				if (isset($versionString[2]) )
				{
					$version = $versionString[2];
					$version = str_replace('_', '.', $version);
				}
			}
    	}
    	else if (stripos($httpUserAgent, 'iPad') !== false)
    	{
    		$versionString = stristr($httpUserAgent, 'Version');

    		if ($versionString === false)
    		{
				$chromeVersionString = explode(' ', stristr($httpUserAgent, 'iPad'));
				if (isset($chromeVersionString[3]))
				{
					$version = $chromeVersionString[3];
					$version = str_replace('_','.',$version);
					$browser = TPX_ONLINESUPPORTED_BROWSER_IPAD;
				}
    		}

    		if ($versionString !== false)
    		{
	    		$versionStringArray = explode('/', $versionString);

				if (isset($versionStringArray[1]))
				{
					$versionArray = explode(' ', $versionStringArray[1]);
					$version = $versionArray[0];
				}

            	$browser = TPX_ONLINESUPPORTED_BROWSER_IPAD;
            }
    	}
    	else if ((stripos($httpUserAgent, 'Chrome') !== false) || (stripos($httpUserAgent, 'CriOS') !== false))
    	{
            if (stripos($httpUserAgent, 'Chrome') !== false)
            {
                $versionString = explode('/', stristr($httpUserAgent, 'Chrome'));
            }
			else
			{
				$versionString = explode('/', stristr($httpUserAgent, 'CriOS'));
			}

			$versionArray = explode(' ', $versionString[1]);

			$version = $versionArray[0];
			$browser = TPX_ONLINESUPPORTED_BROWSER_CHROME;
            $hideChrome = true;
            $hideFirefox = true;
    	}
    	else if (stripos($httpUserAgent, 'safari') === false)
    	{
    		if (preg_match("/Firefox[\/ \(]([^ ;\)]+)/i", $httpUserAgent, $matches))
    		{
            	$version = $matches[1];
                $browser = TPX_ONLINESUPPORTED_BROWSER_FIREFOX;
            }
            else if (preg_match("/Firefox$/i", $httpUserAgent, $matches))
            {
				$browser = TPX_ONLINESUPPORTED_BROWSER_FIREFOX;
            }
			else if(stripos($httpUserAgent,'iPhone') !== false) // internal iphone webkit browser
			{
				if (preg_match('/iPhone OS ([0-9_]*)/is', $httpUserAgent, $versionArray))
				{
					$version = str_replace('_', '.', $versionArray[1]);
					$browser = TPX_ONLINESUPPORTED_BROWSER_IPHONE;
				}
			}
    	}
    	else if (stripos($httpUserAgent, 'Android') !== false)
    	{
    		$versionString = explode(' ', stristr($httpUserAgent, 'Android'));

			if (isset($versionString[1]))
			{
				$versionArray = explode(' ', $versionString[1]);
				$version = $versionArray[0];
			}

            $browser = TPX_ONLINESUPPORTED_BROWSER_ANDROID;
    	}
    	else if ((stripos($httpUserAgent, 'Safari') !== false) && (stripos($httpUserAgent, 'iPhone') === false) && (stripos($httpUserAgent, 'iPod') === false))
    	{
    		$versionString = explode('/', stristr($httpUserAgent, 'Version'));

			if (isset($versionString[1]))
			{
				$versionArray = explode(' ', $versionString[1]);
				$version = $versionArray[0];
			}

			$browser = TPX_ONLINESUPPORTED_BROWSER_SAFARI;
    	}

    	$pos = strpos($version, '.');
    	$versionNumber = (int) substr($version, 0, $pos);
    	$versionFloat = (float) $version; // use the floating point version for SamsungBrowser

		// determine the platform and version
		if (stripos($httpUserAgent, 'windows') !== false)
		{
			$platform = TPX_ONLINESUPPORTED_PLATFORM_WINDOWS;
			$hideSafari = true;
		}
		else if (stripos($httpUserAgent, 'iPad') !== false)
		{
			$platform = TPX_ONLINESUPPORTED_PLATFORM_IPAD;
			$hideFirefox = true;
			$hideSafariDownload = true;
			$hideChromeDownload = true;
			$hideEdge = true;
		}
		else if (stripos($httpUserAgent, 'mac') !== false)
		{
			$platform = TPX_ONLINESUPPORTED_PLATFORM_APPLE;
			$hideEdge = true;
		}
		else if (stripos($httpUserAgent, 'android') !== false)
		{
			$platform = TPX_ONLINESUPPORTED_PLATFORM_ANDROID;
			$hideSafari = true;
			$hideFirefox = true;
			$hideFirefoxDownload = true;
			$hideChromeDownload = true;
			$hideEdge = true;

			$versionString = explode(' ', stristr($httpUserAgent, 'android'));
			$versionArray = explode(';', $versionString[1]);
			$plaformArray = explode('.', $versionArray[0], 2);
			$platformVersion = (int) $plaformArray[0];
		}
		else if (stripos($httpUserAgent, 'win') !== false)
		{
			$platform = TPX_ONLINESUPPORTED_PLATFORM_WINDOWS;
			$hideSafari = true;
		}

    	switch ($browser)
		{
			case TPX_ONLINESUPPORTED_BROWSER_INTERNETEXPLORER:
				$browserSupported = false;
				break;
			case TPX_ONLINESUPPORTED_BROWSER_CHROME:
				if ($versionNumber < TPX_ONLINESUPPORTED_BROWSER_VERSION_CHROME)
				{
					$browserSupported = false;
				}
				break;
			case TPX_ONLINESUPPORTED_BROWSER_FIREFOX:
				if ($versionNumber < TPX_ONLINESUPPORTED_BROWSER_VERSION_FIREFOX)
				{
					$browserSupported = false;
				}
				break;
			case TPX_ONLINESUPPORTED_BROWSER_IPHONE:
			case TPX_ONLINESUPPORTED_BROWSER_IPAD:
				if ($versionNumber < TPX_ONLINESUPPORTED_IOS_VERSION)
				{
					$hideFirefox = true;

					$browserSupported = false;
				}
				break;
			case TPX_ONLINESUPPORTED_BROWSER_ANDROID:
					$browserSupported = false;
				break;
			case TPX_ONLINESUPPORTED_BROWSER_SAFARI:
				if ($versionNumber < TPX_ONLINESUPPORTED_BROWSER_VERSION_SAFARI)
				{
					$browserSupported = false;
				}
				break;
			case TPX_ONLINESUPPORTED_BROWSER_SAMSUNG:
				$browserSupported = false;
				if ((($platformVersion >= 6) && ($versionFloat > 5)) ||
					(($platformVersion >= 7) && ($versionFloat >= 5)) ||
                    $versionFloat >= 6)
				{
					$browserSupported = true;
				}
				break;
			case TPX_ONLINESUPPORTED_BROWSER_OPERA:
				// android 8.0 now used OPR in its build number in the UA string so a check is required to allow usage of android 8.0 chrome
				if (($platform == TPX_ONLINESUPPORTED_PLATFORM_ANDROID) && ($platformVersion >= 8))
				{
					$browserSupported = true;
				}
				else
				{
					$browserSupported = false;
				}
				break;
			case TPX_ONLINESUPPORTED_BROWSER_WECHAT:
				// not aware of any wechat browsers which are not supported so let them all through
				// wechat doesn't allow you to use the software if it is not running on the latest version
				$browserSupported = true;

				break;
			case TPX_ONLINESUPPORTED_BROWSER_FBIOS:
				// Allow the facebook in app browser for the facebook app and messenger.
				$browserSupported = true;

				break;
			default:
				$browserSupported = false;
		}

 		$returnArray['browsersupported'] = $browserSupported;
 		$returnArray['hidesafari'] = $hideSafari;
 		$returnArray['hidesafaridownload'] = $hideSafariDownload;
 		$returnArray['hidefirefox'] = $hideFirefox;
 		$returnArray['hidefirefoxdownload'] = $hideFirefoxDownload;
 		$returnArray['hidechrome'] = $hideChrome;
		$returnArray['hidechromedownload'] = $hideChromeDownload;
		$returnArray['hideedgedownload'] = $hideEdgeDownload;
		$returnArray['hideedge'] = $hideEdge;

    	return $returnArray;
    }


	static function sendCommunicationFailedEmail($pWebSessionData, $pErrorCode, $errorMessage = '')
    {
		// include the email creation module
		require_once('../Utils/UtilsEmail.php');

    	global $gSession;
    	global $ac_config;

    	$webBrandArray = DatabaseObj::getBrandingFromCode($gSession['webbrandcode']);

        $errorHappenDateTime = date('d-m-Y H:i:s', time());
        $url = $ac_config['TAOPIXONLINEURL'];
        $errorCode = $pErrorCode;

        $smarty = SmartyObj::newSmarty('AdminErrorNotification', $gSession['webbrandcode'], $gSession['webbrandapplicationname']);

		$errorTitle = $smarty->get_config_vars('str_TitleCommunicationFailed');

        $errorMsg = $smarty->get_config_vars('str_WarningCommunicationFailed');
		$errorMsg .= "\n" . $smarty->get_config_vars('str_LabelErrorTime') . ' ' . $errorHappenDateTime ;
		$errorMsg .= "\n" . $smarty->get_config_vars('str_LabelErrorURL')  . ' ' . $url ;
		$errorMsg .= "\n" . $smarty->get_config_vars('str_LabelErrorCode') . ' ' . $errorCode ;
		if ($errorMessage != '')
		{
			$errorMsg .= "\n" . $smarty->get_config_vars('str_LabelErrorMessage') . ' ' . $errorMessage ;
		}

    	$emailObj = new TaopixMailer();
        $emailObj->sendTemplateEmail('admin_errornotification', $pWebSessionData['webbrandcode'], $pWebSessionData['webbrandapplicationname'],
        								$pWebSessionData['webbranddisplayurl'], $pWebSessionData['browserlanguagecode'], $webBrandArray['smtpadminname'],
        								$webBrandArray['smtpadminaddress'], '', '', 0, Array(
            'errorTitle' => $errorTitle,
            'errorMessage' => $errorMsg
        ));
    }


    static function checkDeviceSupportedForProductWizardModeOnline($pOnlineEditorMode, $pIsLargeScreen, $pCollectionType, $pWizardModeOnline)
    {
    	$resultArray = array();
    	$deviceSupported = true;

        if ((($pCollectionType == TPX_PRODUCTCOLLECTIONTYPE_PHOTOBOOK) && (! $pIsLargeScreen) &&
        	(($pWizardModeOnline == TPX_WIZARD_MODE_OFF) || ($pWizardModeOnline == TPX_WIZARD_MODE_PICTURE_SELECTION_ONLY))) &&
			($pOnlineEditorMode != TPX_ONLINE_EDITOR_MODE_EASY))
        {
			$deviceSupported = false;
        }
        else if ((($pCollectionType == TPX_PRODUCTCOLLECTIONTYPE_PROOFBOOK) && (! $pIsLargeScreen)) && ($pOnlineEditorMode != TPX_ONLINE_EDITOR_MODE_EASY))
        {
        	$deviceSupported = false;
        }


        $resultArray['devicesupported'] = $deviceSupported;

        return $resultArray;
    }

    static function checkShoppingCartSession($pProjectRef)
    {
        $returnArray = array('error' => '', 'shoppingcartsessionref' => 0);
        $shoppingCartSessionRef = 0;
        $error = '';

        // get projectref from SESSIONDATA to find out if a project has been added to shopping cart
        $dbObj = DatabaseObj::getGlobalDBConnection();

        if ($dbObj)
        {
			$sql = 'SELECT `id` FROM `SESSIONDATA`
					WHERE `projectref` = ?
						AND `sessionexpiredate` <> "0000-00-00 00:00:00"
						AND `sessionenabled` = 1
						AND `sessionactive` = 1
						AND `ordersession` = 1';
			$stmt = $dbObj->prepare($sql);
            if ($stmt)
            {
                if ($stmt->bind_param('s', $pProjectRef))
                {
                    if ($stmt->execute())
                    {
                        if ($stmt->bind_result($shoppingCartSessionRef))
                        {
                            if ($stmt->fetch())
                            {
                                $returnArray['shoppingcartsessionref'] = $shoppingCartSessionRef;
                            }
                        }
                        else
                        {
                            $error = __FUNCTION__ . ' bind_result: ' . $dbObj->error;
                        }
                    }
                    else
                    {
                        $error = __FUNCTION__ . ' execute: ' . $dbObj->error;
                    }
                }
                else
                {
                    $error = __FUNCTION__ . ' bind_param: ' . $dbObj->error;
                }

                $stmt->free_result();
                $stmt->close();
                $stmt = null;
            }
            else
            {
                $error = __FUNCTION__ . ' prepare: ' . $dbObj->error;
            }

            $dbObj->close();
        }

        $returnArray['error'] = $error;

        return $returnArray;
    }

    static function checkShoppingCartSessionByBasketRef($pBasketRef)
    {
        $returnArray = array('error' => '', 'shoppingcartsessionref' => 0);
        $shoppingCartSessionRef = 0;
        $error = '';

        // get projectref from SESSIONDATA to find out if a project has been added to shopping cart
        $dbObj = DatabaseObj::getGlobalDBConnection();

        if ($dbObj)
        {
			$sql = 'SELECT `id` FROM `SESSIONDATA`
					WHERE `basketref` = ?
						AND `sessionexpiredate` <> "0000-00-00 00:00:00"
						AND `sessionenabled` = 1
						AND `sessionactive` = 1
						AND `ordersession` = 1';
			$stmt = $dbObj->prepare($sql);
            if ($stmt)
            {
                if ($stmt->bind_param('s', $pBasketRef))
                {
                    if ($stmt->execute())
                    {
                        if ($stmt->bind_result($shoppingCartSessionRef))
                        {
                            if ($stmt->fetch())
                            {
                                $returnArray['shoppingcartsessionref'] = $shoppingCartSessionRef;
                            }
                        }
                        else
                        {
                            $error = __FUNCTION__ . ' bind_result: ' . $dbObj->error;
                        }
                    }
                    else
                    {
                        $error = __FUNCTION__ . ' execute: ' . $dbObj->error;
                    }
                }
                else
                {
                    $error = __FUNCTION__ . ' bind_param: ' . $dbObj->error;
                }

                $stmt->free_result();
                $stmt->close();
                $stmt = null;
            }
            else
            {
                $error = __FUNCTION__ . ' prepare: ' . $dbObj->error;
            }

            $dbObj->close();
        }

        $returnArray['error'] = $error;

        return $returnArray;
    }

    static function checkWizardDevice($pWizModeIn, $pProductCollectionType, $pDeviceDataArray, $pOnlineBasketAPIWorkflowType, $pOnlineEditorMode)
    {
       	$wizCheck = false;
        $largeScreen = true;

		if ($pOnlineBasketAPIWorkflowType == TPX_BASKETWORKFLOWTYPE_LOWLEVELAPI)
		{
			$largeScreen = ($pDeviceDataArray['screensize'] == "1") ? true : false;
        }
        else
        {
        	global $gSession;
        	$largeScreen = $gSession['islargescreen'];
        }

        if ($pProductCollectionType == TPX_PRODUCTCOLLECTIONTYPE_SINGLEPRINTS)
        {
            $wizCheck = true;
        }

        if (($pProductCollectionType == TPX_PRODUCTCOLLECTIONTYPE_PHOTOBOOK) && ($pWizModeIn < TPX_WIZARD_MODE_REPLACE_LAYOUT))
        {

			// Easy mode is available for all wizard mode.
			if ($pOnlineEditorMode == TPX_ONLINE_EDITOR_MODE_ADVANCED)
			{
				$wizCheck = $largeScreen;
			}
			else
			{
				$wizCheck = true;
			}
        }

        if ((($pProductCollectionType == TPX_PRODUCTCOLLECTIONTYPE_PHOTOBOOK) || ($pProductCollectionType == TPX_PRODUCTCOLLECTIONTYPE_CALENDAR)) &&
			($pWizModeIn >= TPX_WIZARD_MODE_REPLACE_LAYOUT))
        {
			$wizCheck = true;
        }


        return $wizCheck;
    }


    static function updateReorderState($pReorderState, $pProjectsList)
	{
		// update the canreorder flag for projects passed in $pProjectsArray to the value $pReorderState
		$returnArray = array('error' => '', 'data' => '');

        $dbObj = DatabaseObj::getGlobalDBConnection();

        if ($dbObj)
        {
			$sql = 'UPDATE `ORDERITEMS`
					SET `canreorder` = ?
					WHERE `projectref` IN (' . $pProjectsList . ')';

			$stmt = $dbObj->prepare($sql);
            if ($stmt)
            {
                if ($stmt->bind_param('i', $pReorderState))
                {
                    if (! $stmt->execute())
                    {
						$returnArray['error'] = __FUNCTION__ . ' execute: ' . $dbObj->error;
                    }
                }
                else
                {
					$returnArray['error'] = __FUNCTION__ . ' bind_param: ' . $dbObj->error;
                }

                $stmt->free_result();
                $stmt->close();
                $stmt = null;
            }
            else
            {
				$returnArray['error'] = __FUNCTION__ . ' prepare: ' . $dbObj->error;
            }

            $dbObj->close();
        }

        return $returnArray;
	}

    static function createFlaggedProjectEmails($pData)
	{
		// include the email creation module
		require_once('../Utils/UtilsEmail.php');
		require_once('../Utils/UtilsLocalization.php');

    	global $gSession;
    	global $ac_config;

    	$language = array_key_exists('language', $pData) ? $pData['language'] : '';

		// get the user information
		$pData['user'] = DatabaseObj::getUserAccountFromID($pData['userid']);

	    $webBrandArray = DatabaseObj::getBrandingFromCode($pData['user']['webbrandcode']);

		$homeURL = $webBrandArray['displayurl'];

		if ($webBrandArray['usemultilinebasketworkflow'] == 1 && $webBrandArray['onlinedesignerlogouturl'] != '')
		{
			$homeURL = $webBrandArray['onlinedesignerlogouturl'];
		}

        $projectData = unserialize($pData['projects']);

		foreach ($projectData as &$purgedata)
		{
			$purgedata['purge'] = LocalizationObj::formatLocaleDate($purgedata['purge'], $language);
			$purgedata['product'] = LocalizationObj::getLocaleString($purgedata['layoutname'], $language);
		}

		if (($pData['user']['result'] != 'str_ErrorNoAccount') && ($pData['user']['emailaddress'] != ''))
		{
			$paramArray = array();
			$paramArray['emailaddress'] = $pData['user']['emailaddress'];
			$paramArray['contactfirstname'] = $pData['user']['contactfirstname'];
			$paramArray['contactfullname'] = $pData['user']['contactfirstname'] . ' ' . $pData['user']['contactlastname'];
			$paramArray['login'] = $pData['user']['login'];

			$paramArray['targetuserid'] = $pData['userid'];
			$paramArray['orderedprojectlist'] = [];
			$paramArray['unorderedprojectlist'] = [];

			foreach ($projectData as $key => $projectDetails) {
				$thumbnail = '';
				$thumbnailData = ! empty($pData['thumbnails']) ? ($pData['thumbnails'][$projectDetails['ref']] ?? null) : null;

				if (null !== $thumbnailData && 0 === $thumbnailData['error']) {
					$thumbnail = 'data:' . $thumbnailData['thumbnail']['mimetype'] . ';base64,' . $thumbnailData['thumbnail']['data'];
				}

				if ('' === $thumbnail) {
					// Get from layoutcode ?
					$thumbnail = UtilsObj::getAssetRequest($projectDetails['layoutcode'], 'product');
				}

				$keyName = 'ordered' === strtolower($projectDetails['type']) ? 'orderedprojectlist' : 'unorderedprojectlist';
				$projectDetails['thumbnail'] = $thumbnail;
				$paramArray[$keyName][] = $projectDetails;
			}

			$templateToUse = 'customer_flaggedforpurge';

			if (array_key_exists('level', $pData) && $pData['level'] != '')
			{
				$templateToUse .= '_' . $pData['level'];
			}

			$emailObj = new TaopixMailer();
			$emailObj->sendTemplateEmail($templateToUse, $webBrandArray['code'], $webBrandArray['applicationname'],
        								$homeURL, $language, $paramArray['contactfullname'],
        								$paramArray['emailaddress'], '', '', 0, $paramArray);
		}
	}

    static function deleteOnlineBasketData($pProjectsData)
	{
		// update the canreorder flag for projects passed in $pProjectsArray to the value $pReorderState
		$returnArray = array('error' => '', 'data' => '');

        $dbObj = DatabaseObj::getGlobalDBConnection();

        if ($dbObj)
        {
			$sql = 'DELETE FROM `ONLINEBASKET` WHERE `projectref` IN (' . $pProjectsData . ')';

			$stmt = $dbObj->prepare($sql);
            if ($stmt)
            {
				if (! $stmt->execute())
				{
					$returnArray['error'] = __FUNCTION__ . ' execute: ' . $dbObj->error;
				}

                $stmt->free_result();
                $stmt->close();
                $stmt = null;
            }
            else
            {
				$returnArray['error'] = __FUNCTION__ . ' prepare: ' . $dbObj->error;
            }

            $dbObj->close();
        }

        return $returnArray;
	}

	static function deleteHighLevelUserSession($pBasketRef)
	{
		$error = '';

		$dbObj = DatabaseObj::getGlobalDBConnection();

        if ($dbObj)
        {
            if ($stmt = $dbObj->prepare('DELETE FROM `SESSIONDATA` WHERE `basketref` = ?'))
            {
                if ($stmt->bind_param('s', $pBasketRef))
                {
                    if (!$stmt->execute())
                    {
                    	$error = __FUNCTION__ . ' execute: ' . $dbObj->error;
                    }
                }
                else
                {
                	$error = __FUNCTION__ . ' bind: ' . $dbObj->error;
                }

                $stmt->free_result();
                $stmt->close();
                $stmt = null;
            }
            else
            {
            	$error = __FUNCTION__ . ' prepare: ' . $dbObj->error;
            }

            if ($stmt = $dbObj->prepare('UPDATE `ONLINEBASKET` SET `basketexpiredate` = now() WHERE `basketref` = ?'))
            {
                if ($stmt->bind_param('s', $pBasketRef))
                {
                    if (!$stmt->execute())
                    {
                    	$error = __FUNCTION__ . ' update basketexpire execute: ' . $dbObj->error;
                    }
                }
                else
                {
                	$error = __FUNCTION__ . '  update basketexpire bind: ' . $dbObj->error;
                }

                $stmt->free_result();
                $stmt->close();
                $stmt = null;
            }
            else
            {
            	$error = __FUNCTION__ . '  update basketexpire prepare: ' . $dbObj->error;
            }

           	 $dbObj->close();
        }

        return $error;
	}

	static function deleteUserSession($pUserID)
	{
		$error = '';

		$dbObj = DatabaseObj::getGlobalDBConnection();

        if ($dbObj)
        {
            if ($stmt = $dbObj->prepare('DELETE FROM `SESSIONDATA` WHERE `userid` = ?'))
            {
                if ($stmt->bind_param('i', $pUserID))
                {
                    if (!$stmt->execute())
                    {
                    	$error = __FUNCTION__ . ' execute: ' . $dbObj->error;
                    }
                }
                else
                {
                	$error = __FUNCTION__ . ' bind: ' . $dbObj->error;
                }

                $stmt->free_result();
                $stmt->close();
                $stmt = null;
            }
            else
            {
            	$error = __FUNCTION__ . ' prepare: ' . $dbObj->error;
            }

           	$dbObj->close();
        }

        return $error;
	}

	static function buildHighLevelProjectParams($pProjectParams, $pMethodName, $pWebBrandCode)
	{
		$resultArray = $pProjectParams;

		$hl_config = UtilsObj::readWebBrandConfigFile('../config/onlinebaskethighlevelapi.conf', $pWebBrandCode);

		if ((array_key_exists('MINLIFE', $hl_config)) && ($hl_config['MINLIFE'] != ''))
		{
			$resultArray['minlife'] = (int) $hl_config['MINLIFE'];
		}

		if (($pMethodName == 'createProject') || ($pMethodName == 'editProject'))
		{
			if ((array_key_exists('ABANDONURL', $hl_config)) && ($hl_config['ABANDONURL'] != ''))
			{
				$resultArray['abandonurl'] = $hl_config['ABANDONURL'];
			}

			if ((array_key_exists('ABANDONNAME', $hl_config)) && ($hl_config['ABANDONNAME'] != ''))
			{
				$resultArray['abandonname'] = $hl_config['ABANDONNAME'];
			}

			if ((array_key_exists('CANSIGNIN', $hl_config)) && ($hl_config['CANSIGNIN'] != ''))
			{
				$resultArray['cansignin'] = (int) $hl_config['CANSIGNIN'];
			}

			if ((array_key_exists('CANSIGNOUT', $hl_config)) && ($hl_config['CANSIGNOUT'] != ''))
			{
				$resultArray['cansignout'] = (int) $hl_config['CANSIGNOUT'];
			}

			if ((array_key_exists('DISABLEBACKBUTTON', $hl_config)) && ($hl_config['DISABLEBACKBUTTON'] != ''))
			{
				$resultArray['disablebackbutton'] = $hl_config['DISABLEBACKBUTTON'];
			}
		}

		if (file_exists('../Customise/scripts/EDL_OnlineHighLevelBasketAPI.php'))
        {
            require_once('../Customise/scripts/EDL_OnlineHighLevelBasketAPI.php');
        }

        if (($pMethodName == 'createProject') && (method_exists('OnlineBasketHighLevelAPI', $pMethodName)))
		{
			$resultArray = OnlineBasketHighLevelAPI::createProject($resultArray);
		}
		elseif (($pMethodName == 'editProject') && (method_exists('OnlineBasketHighLevelAPI', $pMethodName)))
		{
			$resultArray = OnlineBasketHighLevelAPI::editProject($resultArray);
		}
		elseif (($pMethodName == 'duplicateProject') && (method_exists('OnlineBasketHighLevelAPI', $pMethodName)))
		{
			$resultArray = OnlineBasketHighLevelAPI::duplicateProject($resultArray);
		}
		elseif (($pMethodName == 'deleteProject') && (method_exists('OnlineBasketHighLevelAPI', $pMethodName)))
		{
			$resultArray = OnlineBasketHighLevelAPI::deleteProject($resultArray);
		}

		return $resultArray;
	}

	static function transferUserOnlineProjectsToBasket($pBrandCode, $pGroupCode, $pUserID, $pBasketRef, $pBasketExpireDate, $pBrowserLanguageCode)
	{
		$result = '';
		$resultParam = '';
		$skipProjectRefArray = array();
		$onlineProjectRefArray = array();

        $projectCount = 0;

		// if the user had projects assigned to them before multiline was enabled we need to add entries for them in the online basket table
		// this involves a server call to online so we need to avoid that if possible
		// we also need to filter out projects which have already been ordered as they should not appear in the user's basket
		$dbObj = DatabaseObj::getGlobalDBConnection();
        if ($dbObj)
        {
			// try to determine if we have already retrieved the list of projects for this user
			// we do this by looking for rows that have a name but no code
			// this only works because we used an existing function to retrieve the project list which did not include the codes
            if ($stmt = $dbObj->prepare('SELECT COUNT(`id`) FROM `ONLINEBASKET` WHERE `collectioncode` = "" AND `collectionname` != ""
            								AND `layoutcode` = "" AND `layoutname` != "" AND userid = ?'))
            {
                if ($stmt->bind_param('i', $pUserID))
                {
                    if ($stmt->bind_result($projectCount))
                    {
                        if ($stmt->execute())
                        {
                            $stmt->fetch();
                        }
                        else
                        {
							$result = 'str_DatabaseError';
							$resultParam = 'transferUserOnlineProjectsToBasket execute: ' . $dbObj->error;
                        }
                    }
                    else
                    {
                        $result = 'str_DatabaseError';
						$resultParam = 'transferUserOnlineProjectsToBasket bind_result: ' . $dbObj->error;
                    }
                }
                else
                {
                    $result = 'str_DatabaseError';
					$resultParam = 'transferUserOnlineProjectsToBasket bind_param: ' . $dbObj->error;
                }

                if ($stmt)
                {
                    $stmt->free_result();
                    $stmt->close();
                    $stmt = null;
                }
            }
            else
            {
                $result = 'str_DatabaseError';
				$resultParam = 'transferUserOnlineProjectsToBasket prepare: ' . $dbObj->error;
            }


			// we need to go retrieve the previous user project as they are not in the basket
			if ($result == '')
			{
				// get a list of projects craeted by the user in the online designer
					require_once('../libs/internal/curl/Curl.php');

					$postParamArray = array(
						'cmd' => 'GETPROJECTLIST',
						'data' => array(
							'userid' => $pUserID,
							'defaultlanguagecode' => $pBrowserLanguageCode,
							'hourOffset' => LocalizationObj::getBrowserHourOffset()
						)
					);

					$projectListDataArray = self::curlPutToTaopixOnline($postParamArray);

					if ($projectListDataArray['error'] === '')
					{
						$unserializedDataArray = $projectListDataArray['data'];

						if ($unserializedDataArray['error'] == '')
						{
							if ($unserializedDataArray['result'] != TPX_ONLINE_ERROR_MAINTENANCEMODE)
							{
								// extract the project refs from the returned data
								$onlineProjectRefArray = array_map(function($project) { return $project['projectref']; }, $unserializedDataArray['projects'] );
							}
						}
						else
						{
							$error = $unserializedDataArray['error'];
							$errorParam = $unserializedDataArray['errorparam'];
						}
					}

					// have any projects been returned
					if (count($onlineProjectRefArray) > 0)
					{
						// get all project refs assigned to the user with entries in the ONLINEBASKET
						if ($stmt = $dbObj->prepare('SELECT `projectref`, `dateofpurge` FROM `ONLINEBASKET` WHERE `userid` = ?'))
						{
							if ($stmt->bind_param('i', $pUserID))
							{
								if ($stmt->bind_result($basketItemProjectRef, $dateOfPurge))
								{
									if ($stmt->execute())
									{
										while ($stmt->fetch())
										{
											$skipProjectRefArray[$basketItemProjectRef] = [
												'ref' => $basketItemProjectRef,
												'dateofpurge' => $dateOfPurge,
											];
										}
									}
									else
									{
										$result = 'str_DatabaseError';
										$resultParam = 'transferUserOnlineProjectsToBasket get basketitems execute: ' . $dbObj->error;
									}
								}
								else
								{
									$result = 'str_DatabaseError';
									$resultParam = 'transferUserOnlineProjectsToBasket get basketitems bind_result: ' . $dbObj->error;
								}
							}
							else
							{
								$result = 'str_DatabaseError';
								$resultParam = 'transferUserOnlineProjectsToBasket get basketitems bind_param: ' . $dbObj->error;
							}

							if ($stmt)
							{
								$stmt->free_result();
								$stmt->close();
								$stmt = null;
							}
						}
						else
						{
							$result = 'str_DatabaseError';
							$resultParam = 'transferUserOnlineProjectsToBasket get basketitems prepare: ' . $dbObj->error;
						}

						// continue if no error has occurred
						if ($result == '')
						{
							// retrieve a list of all user projects which have an entry in the orderitems table
							$sql =  "SELECT `projectref` FROM `ORDERITEMS` WHERE `projectref` IN ('" . implode("','", $onlineProjectRefArray) . "')";
							if ($stmt = $dbObj->prepare($sql))
							{
								if ($stmt->bind_result($orderItemProjectRef))
								{
									if ($stmt->execute())
									{
										while ($stmt->fetch())
										{
											// If we have not tracked this project previously add it to the skip for inserts.
											if (! array_key_exists($orderItemProjectRef, $skipProjectRefArray))
											{
												$skipProjectRefArray[$orderItemProjectRef] = [
													'ref' => $orderItemProjectRef,
													'dateofpurge' => null, // Date of purge set to null as this isn't tracked on the order items.
												];
											}
										}
									}
									else
									{
										$result = 'str_DatabaseError';
										$resultParam = 'transferUserOnlineProjectsToBasket get orderitems execute: ' . $dbObj->error;
									}
								}
								else
								{
									$result = 'str_DatabaseError';
									$resultParam = 'transferUserOnlineProjectsToBasket get orderitems bind_result: ' . $dbObj->error;
								}

								if ($stmt)
								{
									$stmt->free_result();
									$stmt->close();
									$stmt = null;
								}
							}
							else
							{
								$result = 'str_DatabaseError';
								$resultParam = 'transferUserOnlineProjectsToBasket get orderitems prepare: ' . $dbObj->error;
							}
						}

							if ($result == '')
							{
								// perform all inserts within a transaction
								if ($dbObj->query('START TRANSACTION'))
								{
									$sql = 'INSERT INTO `ONLINEBASKET` (`datecreated`, `webbrandcode`, `groupcode`, `basketref`, `basketexpiredate`,
																		`projectref`, `userid`, `projectname`, `collectionname`, `layoutname`, `saved`, `inbasket`, `dateofpurge`)
											VALUES (NOW(), ?, ?, ?, ?, ?, ?, ?, ?, ?, 1, 0, ?)';
									$updatePurgeDate = 'UPDATE `ONLINEBASKET` SET `dateofpurge` = ? WHERE `projectref` = ?';

									$stmt = $dbObj->prepare($sql);
									$purgeDateStmt = $dbObj->prepare($updatePurgeDate);

									if (false !== $stmt && false !== $purgeDateStmt)
									{
										foreach ($unserializedDataArray['projects'] as $project)
										{
											// we only want to insert the projects that have not been ordered and projects that do not already exist in the users basket.
											if (! array_key_exists($project['projectref'], $skipProjectRefArray))
											{
												if ($stmt->bind_param('sssssissss', $pBrandCode, $pGroupCode, $pBasketRef, $pBasketExpireDate,
																		$project['projectref'], $pUserID, $project['name'], $project['collectionname'],
																		$project['productname'], $project['dateofpurgeraw']))
												{
													if (! $stmt->execute())
													{
														// could not execute statement
														$result = 'str_DatabaseError';
														$resultParam = 'addOnlineProjectsToBasket execute ' . $dbObj->error;
													}
												}
												else
												{
													// could not bind parameters
													$result = 'str_DatabaseError';
													$resultParam = 'addOnlineProjectsToBasket bind params ' . $dbObj->error;
												}
											}
											else
											{
												// Update the purge date for projects if they are different.
												if ($skipProjectRefArray[$project['projectref']]['dateofpurge'] != $project['dateofpurgeraw'])
												{
													if ($purgeDateStmt->bind_param('ss', $project['dateofpurgeraw'], $project['projectref']))
													{
														if (!$purgeDateStmt->execute())
														{
															$result = 'str_DatabaseError';
															$resultParam = 'addOnlineProjectsToBasket update purge date execute ' . $dbObj->error;
														}
													}
													else
													{
														$result = 'str_DatabaseError';
														$resultParam = 'addOnlineProjectsToBasket update purge date bind ' . $dbObj->error;
													}
												}
											}

											if ($result != '')
											{
												break;
											}
										}

										if ($stmt)
										{
											$stmt->free_result();
											$stmt->close();
											$stmt = null;
										}

										if ($purgeDateStmt)
										{
											$purgeDateStmt->free_result();
											$purgeDateStmt->close();
											$purgeDateStmt = null;
										}
									}
									else
									{
										// could not prepare statement
										$result = 'str_DatabaseError';
										$resultParam = 'addOnlineProjectsToBasket prepare ' . $dbObj->error;
									}

									if ($result == '')
									{
										// commit
										if (! $dbObj->query('COMMIT'))
										{
											// if the commit fails, rollback
											$dbObj->query('ROLLBACK');

											$result = 'str_DatabaseError';
											$resultParam = 'addOnlineProjectsToBasket commit ' . $dbObj->error;
										}
									}
									else
									{
										// rollback
										$dbObj->query('ROLLBACK');
									}
								}
								else
								{
									// failed to start the transaction
									$result = 'str_DatabaseError';
									$resultParam = 'addOnlineProjectsToBasket start transaction ' . $dbObj->error;
								}
							}
					}
			}



            $dbObj->close();
        }
		else
		{
			$result = 'str_DatabaseError';
			$resultParam = 'transferUserOnlineProjectsToBasket unable to connect';
		}

		$resultArray = array('result' => $result, 'resultParam' => $resultParam);

		return $resultArray;
	}

	static function updateProductInBasket($pProjectRef, $pProductcode, $pProductname)
	{
		$resultArray = array();
        $result = '';
        $resultParam = '';

    	$dbObj = DatabaseObj::getGlobalDBConnection();

        if ($dbObj)
        {
			if ($stmt = $dbObj->prepare('UPDATE `ONLINEBASKET` SET `layoutcode` = ?, `layoutname` = ? WHERE `projectref` = ?'))
			{
				if ($stmt->bind_param('sss', $pProductcode, $pProductname, $pProjectRef))
				{
					if (! $stmt->execute())
					{
						// could not execute
						$result = 'str_DatabaseError';
						$resultParam = 'updateProductInBasket section execute ' . $dbObj->error;
					}
				}
				else
				{
					// could not bind parameters
					$result = 'str_DatabaseError';
					$resultParam = 'updateProductInBasket section bind ' . $dbObj->error;
				}

				$stmt->free_result();
				$stmt->close();
				$stmt = null;
			}
			else
			{
				$result = 'str_DatabaseError';
				$resultParam = 'updateProductInBasket section prepare ' . $dbObj->error;
			}

            $dbObj->close();
        }

		$resultArray['result'] = $result;
		$resultArray['resultparam'] = $resultParam;

		return $resultArray;
	}

	static function logLowLevelNotifcationTask($pNotificationArray)
	{
		$successfullNotificationRecordIDs = array();
		$lowLevelActionNotifationArray = array();

		$processedGroupCodeArray = array();

		foreach ($pNotificationArray as &$notification)
		{
			$groupCode = $notification['data']['groupcode'];

			// to prevent multiple database calls for the licensekey brand lookup
			// cache the already processed groupcode as we already know the brand it is assigned to.
			if (array_key_exists($groupCode, $processedGroupCodeArray))
			{
				$notification['data']['webbrandcode'] = $processedGroupCodeArray[$groupCode];
			}
			else
			{
				$licenseKeyArray = DatabaseObj::getLicenseKeyFromCode($groupCode);
				$webBrandCode = $licenseKeyArray['webbrandcode'];

				$notification['data']['webbrandcode'] = $webBrandCode;
				$processedGroupCodeArray[$groupCode] = $webBrandCode;
			}

			$lowLevelActionNotifationIDArray[] = $notification['id'];
		}

		$taskInfo = DatabaseObj::getTask('TAOPIX_CCNOTIFICATION');

		$serializedNotifications = serialize($pNotificationArray);

		$logEventResultArray = DatabaseObj::createEvent('TAOPIX_CCNOTIFICATION', '', '', '', $taskInfo['nextRunTime'], 0, TPX_CCNOTIFICATION_TARGET_LLAPI,
			$serializedNotifications, '', '', '', '', '', '', 0, 0, 0, '', '', 0);

		if ($logEventResultArray['result'] == '')
		{
			$successfullNotificationRecordIDs = array_merge($successfullNotificationRecordIDs, $lowLevelActionNotifationIDArray);
		}

		return $successfullNotificationRecordIDs;
	}

	static function updateOnlineProjectsBasketData($pNotificationArray)
	{
		$notificationRecordIDs = array(
		    'success' => array(),
            'fail' => array()
        );
		$saveProjectRefArray = array();
		$savedProjectRefList = '';

		foreach ($pNotificationArray as $notification)
		{
			$action = $notification['action'];

			switch ($action)
			{
				case TPX_CCNOTIFICATION_ACTION_SAVEPROJECT:
				{
                    $resultArray = self::saveProjectInOnlineBasket($notification['data']['projectref']);

                    if ($resultArray['result'] == '')
                    {
                        $notificationRecordIDs['success'][] = $notification['id'];
                    }
                    else
                    {
                        $notificationRecordIDs['fail'][] = $notification['id'];
                    }

					break;
				}
				case TPX_CCNOTIFICATION_ACTION_RENAMEPROJECT:
				{
					$resultArray = self::updateProjectNameInBasket($notification['data']['projectref'], $notification['data']['newprojectname']);

					if ($resultArray['result'] == '')
                    {
                        $notificationRecordIDs['success'][] = $notification['id'];
                    }
                    else
                    {
                        $notificationRecordIDs['fail'][] = $notification['id'];
                    }

					break;
				}
				case TPX_CCNOTIFICATION_ACTION_CHANGEPRODUCT:
				{
					$resultArray = self::updateProductInBasket($notification['data']['projectref'], $notification['data']['productcode'], $notification['data']['productname']);

                    if ($resultArray['result'] == '')
                    {
                        $notificationRecordIDs['success'][] = $notification['id'];
                    }
                    else
                    {
                        $notificationRecordIDs['fail'][] = $notification['id'];
                    }

					break;
				}
				case TPX_CCNOTIFICATION_ACTION_SHOWINPROJECTLIST:
				{
					$resultArray = self::updateProjectToShowInList($notification['data']['projectref']);

                    if ($resultArray['result'] == '')
                    {
                        $notificationRecordIDs['success'][] = $notification['id'];
                    }
                    else
                    {
                        $notificationRecordIDs['fail'][] = $notification['id'];
                    }

					break;
				}
			}
		}

		return $notificationRecordIDs;
	}

    /**
     * @param string $pProjectRef
     * @return array
     */
    static function saveProjectInOnlineBasket($pProjectRef)
    {
        $resultArray = array();
        $result = '';
        $resultParam = '';

        $dbObj = DatabaseObj::getGlobalDBConnection();

        if ($dbObj)
        {
            if ($stmt = $dbObj->prepare('UPDATE `ONLINEBASKET` SET `saved` = 1, `inbasket` = 0 WHERE `projectref` = ?'))
            {
                if ($stmt->bind_param('s', $pProjectRef))
                {
                    if ($stmt->execute())
                    {
                        if ($stmt->affected_rows == 0)
                        {
                            $result = 'str_DatabaseError: No affected rows';
                            $resultParam = 'saveProjectInBasket select no affected rows ' . $dbObj->error;
                        }
                    }
                    else
                    {
                        $result = 'str_DatabaseError: Could not execute';
                        $resultParam = 'saveProjectInBasket select execute ' . $dbObj->error;
                    }
                }
                else
                {
                    $result = 'str_DatabaseError: Could not bind parameters';
                    $resultParam = 'saveProjectInBasket select bind ' . $dbObj->error;
                }

                $stmt->free_result();
                $stmt->close();
                $stmt = null;
            }
            else
            {
                $result = 'str_DatabaseError';
                $resultParam = 'update onlinebasket saved flag prepare ' . $dbObj->error;
            }
        }

        $resultArray['result'] = $result;
        $resultArray['resultparam'] = $resultParam;

        return $resultArray;
    }

	/**
     * @param string $pProjectRef
     * @return array
     */
    static function updateProjectToShowInList($pProjectRef)
    {
        $resultArray = array();
        $result = '';
        $resultParam = '';

        $dbObj = DatabaseObj::getGlobalDBConnection();

        if ($dbObj)
        {
            if ($stmt = $dbObj->prepare('UPDATE `ONLINEBASKET` SET `inbasket` = 0 WHERE `projectref` = ? AND `inbasket` = -1'))
            {
                if ($stmt->bind_param('s', $pProjectRef))
                {
                    if ($stmt->execute())
                    {
                        if ($stmt->affected_rows == 0)
                        {
                            $result = 'str_DatabaseError: No affected rows';
                            $resultParam = 'updateProjectToShowInList select no affected rows ' . $dbObj->error;
                        }
                    }
                    else
                    {
                        $result = 'str_DatabaseError: Could not execute';
                        $resultParam = 'updateProjectToShowInList select execute ' . $dbObj->error;
                    }
                }
                else
                {
                    $result = 'str_DatabaseError: Could not bind parameters';
                    $resultParam = 'updateProjectToShowInList select bind ' . $dbObj->error;
                }

                $stmt->free_result();
                $stmt->close();
                $stmt = null;
            }
            else
            {
                $result = 'str_DatabaseError';
                $resultParam = 'updateProjectToShowInList prepare ' . $dbObj->error;
            }
        }

        $resultArray['result'] = $result;
        $resultArray['resultparam'] = $resultParam;

        return $resultArray;
    }

	static function resetPasswordRequest($pWebBrandCode, $pLogin, $pPasswordFormat)
	{
        $loginToUse = $pLogin;
        $emailAccountArray = array();

        $userArray = DatabaseObj::getUserAccountFromBrandAndLogin($pWebBrandCode, $loginToUse);

        if ('str_ErrorNoAccount' == $userArray['result'])
        {
            // Check if the online forgotten password request is using the email address.
            if (UtilsObj::validateEmailAddress($loginToUse))
            {
                // The reset attempt was made with an email address, get a list of customer accounts which use that email address.
                $userEmailAccountArray = DatabaseObj::getValidUserAccountsForEmailAndBrand($pWebBrandCode, $loginToUse, '', $pPasswordFormat);

                // Multiple possible accounts been matched from forgotten password, (matching email address).
                if ($userEmailAccountArray['result'] == '')
                {
                    // Check the number of accounts found for the email address.
                    if ($userEmailAccountArray['count'] == 1)
                    {
                        // A single account matched the details, use this to login.
                        $loginToUse = $userEmailAccountArray['accounts'][0]['login'];
                    }
                    else
                    {
                        // More than 1 account with the email address was found.
                        // Record the accounts which match the entered email address.
                        $emailAccountArray = $userEmailAccountArray['accounts'];
                    }
                }
            }
            else
            {
                // No matching accounts have been found, and the value is not an email address. Remove
                // the username name, which will still allow a reset code to be generated, but will
                // not link the code to an account.
                $loginToUse = '';
            }
        }

        if (count($emailAccountArray) > 1)
        {
            // Multiple accounts have been found.
            return AuthenticateObj::resetPasswordRequestMultipleAccounts(-1, $pWebBrandCode, $loginToUse, $pPasswordFormat, '', $emailAccountArray);
        }
        else
        {
            // Only a single account was found
            return AuthenticateObj::resetPasswordRequest(-1, $pWebBrandCode, $loginToUse, $pPasswordFormat, '', '');
        }
	}

	/*
	* Performs user login.
	*
	* Authenticates an user account against the database. If authentication was successful
	* then also starts the session.
	*
	*/
    static function processLogin($pFromOnlineDesigner, $pIsMobile)
    {
		return AuthenticateObj::processLogin($pFromOnlineDesigner, $pIsMobile);
    }

    static function processOnlineLogin($pData, $pIsMobile)
    {
		return AuthenticateObj::processLogin(1, $pIsMobile, $pData);
    }

    static function createNewOnlineAccount($pData)
    {
		return AuthenticateObj::createNewAccount($pData);
    }

	static function createNewAccount()
    {
        return AuthenticateObj::createNewAccount();
    }

    static function prepareParamDataToCreateOnlineSession($pOpenMode, $pProjectRef, $pPreviewExisitingProjectDataArray, $pProductIdentData, $pWorkflowType)
    {
        global $gSession;
        global $gConstants;
        global $ac_config;

        $result = '';
        $systemConfigArray = DatabaseObj::getSystemConfig();
        $ownerCode = $systemConfigArray['ownercode'];
		$tenantID = $systemConfigArray['tenantid'];

		// create a empty empty array which has the same structure as the web session.
        $onlineArray = AuthenticateObj::createSessionDataArray();

        $browserLanguageCode = '';
        $checkOutName = '';
        $abandonName = '';
        $abandonURL = '';
        $projectName = '';
        $isMobile = false;
        $isLargeScreen = false;
        $guestWorkFlowMode = TPX_GUESTWORKFLOWMODE_DISABLED;
        $disableBackButton = 0;
        $canSignIn = 1;
        $canSignOut = 1;
        $editProjectNameOnFirstSave = 1;
        $basketAPIWorkFlowtype = TPX_BASKETWORKFLOWTYPE_NORMAL;
        $basketRef = '';
		$loadedStatus = TPX_PROJECT_LOADED_TEMPLATE;
        $templateRef = '';
        $originalRef = '';
        $ccNotificationsEnabled = false;
		$threeDPreview = 0;
		$productLayoutCode = '';
		$productCollectionCode = '';
		$newWizardMode = -1;
		$enableSwitchingEditor = 0;
		$canShareProject = true;
		$automaticallyApplyPerfectlyClearMode = TPX_AUTOMATICALLYAPPLYPERFECTLYCLEAR_MODE_OFF;
		$insertDeleteButtonsVisibility = TPX_INSERTDELETEBUTTONS_VISIBILITY_VISIBLE;
		$totalPagesDropdownMode = TPX_TOTALPAGES_DROPDOWN_MODE_ENABLED;
		$setInsertDeleteButtonsVisibility = true;
		$setTotalPagesDropdownMode = true;
        $requirePasswordForSessionInactivity = true;
        $averagePagesPerPage = 0;

        $onlineArray['ssotoken'] = '';
        $onlineArray['ssoprivatedata'] = array();
        $onlineArray['assetservicedata'] = array();
        $onlineArray['maxsessionexpiretime'] = '';

        $populateAutoLayoutSettings = true;

        switch ($pOpenMode)
        {
            case TPX_OPEN_MODE_NEW_PROJECT:
            {
                $browserLanguageCode = $pProductIdentData['languagecode'];
                $productCollectionCode = $pProductIdentData['collectioncode'];
                $productLayoutCode = $pProductIdentData['productcode'];
                $groupCode = $pProductIdentData['groupcode'];
                $groupData = $pProductIdentData['groupdata'];
                $companyCode = $pProductIdentData['companycode'];
                $brandCode = $pProductIdentData['brandcode'];
                $minLife = $pProductIdentData['minlife'];
                $canSignIn = $pProductIdentData['cansignin'];
                $canSignOut = $pProductIdentData['cansignout'];
                $guestWorkFlowMode = $pProductIdentData['guestworkflowmode'];
                $disableBackButton = $pProductIdentData['disablebackbutton'];
				$checkOutName = $pProductIdentData['checkoutname'];
				$abandonName = $pProductIdentData['abandonname'];
        		$abandonURL = $pProductIdentData['abandonurl'];
				$projectName = $pProductIdentData['projectname'];
				$editProjectNameOnFirstSave = $pProductIdentData['editprojectnameonfirstsave'];
        		$basketAPIWorkFlowtype = $pProductIdentData['basketapiworkflowtype'];
        		$basketRef = $pProductIdentData['basketref'];
        		$ccNotificationsEnabled = $pProductIdentData['ccnotificationsenabled'];
				$newWizardMode = $pProductIdentData['newwizardmode'];
				$enableSwitchingEditor = $pProductIdentData['enableswitchingeditor'];
				$canShareProject = $pProductIdentData['canshareproject'];
				$automaticallyApplyPerfectlyClearMode = $pProductIdentData['automaticallyapplyperfectlyclearmode'];
				$requirePasswordForSessionInactivity = $pProductIdentData['requirepasswordforsessioninactivity'];

                $login = '';
                $userID = $pPreviewExisitingProjectDataArray['userid'];

                if ($pPreviewExisitingProjectDataArray['userid'] > 0)
                {
                	$login = $pPreviewExisitingProjectDataArray['login'];
                }

                $projectRef = '';
                $userName = $pPreviewExisitingProjectDataArray['username'];
                $isMobile = $gSession['ismobile'];
                $isLargeScreen = $gSession['islargescreen'];

                $licenseKeyDataArray = DatabaseObj::getLicenseKeyFromCode($groupCode);

                AuthenticateObj::setSessionWebBrand($brandCode);
                DatabaseObj::updateSession();

                $workflowType = $pWorkflowType;

                $rand = UtilsObj::createRandomString(4);
                $batchRef = $onlineArray['batchref'] = $rand . '_' . UtilsObj::createRandomString(10);

                $onlineArray['licensekeydata']['groupdata'] = $groupData;

                $onlineArray['ssotoken'] = $pProductIdentData['ssotoken'];
                $onlineArray['ssoprivatedata'] = $pProductIdentData['ssoprivatedata'];
                $onlineArray['assetservicedata'] = $pProductIdentData['assetservicedata'];
                $onlineArray['maxsessionexpiretime'] = $pProductIdentData['ssoexpiredate'];

				$onlineArray['customparameters'] = $pProductIdentData['customparameters'];

				// Insert and delete buttons visibility from the URL paramter.
				if (array_key_exists('idbv', $pProductIdentData['customparameters']) && (self::isPageControlsSettingValidate($pProductIdentData['customparameters']['idbv'])))
				{
					$insertDeleteButtonsVisibility = $pProductIdentData['customparameters']['idbv'];
					$setInsertDeleteButtonsVisibility = false;
				}

				// Total page count interaction from the URL paramter.
				if (array_key_exists('tpdm', $pProductIdentData['customparameters']) && (self::isPageControlsSettingValidate($pProductIdentData['customparameters']['tpdm'])))
				{
					$totalPagesDropdownMode = $pProductIdentData['customparameters']['tpdm'];
					$setTotalPagesDropdownMode = false;
				}
                break;
            }
            case TPX_OPEN_MODE_EXISTING_PROJECT:
            {
				if (array_key_exists('editprojectfromapi', $pProductIdentData))
                {
                	$productCollectionCode = $pProductIdentData['collectioncode'];
                	$productLayoutCode = $pProductIdentData['layoutcode'];
                	$browserLanguageCode = $pProductIdentData['languagecode'];
                	$groupCode = $pProductIdentData['groupcode'];
					$brandCode = $pProductIdentData['webbrandcode'];
					$userID = $pProductIdentData['userid'];
					$minLife = $pProductIdentData['minlife'];
					$canSignIn = $pProductIdentData['cansignin'];
					$canSignOut = $pProductIdentData['cansignout'];
					$disableBackButton = $pProductIdentData['disablebackbutton'];
					$checkOutName = $pProductIdentData['checkoutname'];
					$abandonName = $pProductIdentData['abandonname'];
        			$abandonURL = $pProductIdentData['abandonurl'];
        			$editProjectNameOnFirstSave = $pProductIdentData['editprojectnameonfirstsave'];
					$ccNotificationsEnabled = $pProductIdentData['ccnotificationsenabled'];
					$basketAPIWorkFlowtype = $pProductIdentData['basketapiworkflowtype'];
					$requirePasswordForSessionInactivity = $pProductIdentData['requirepasswordforsessioninactivity'];

        			if ($basketAPIWorkFlowtype == TPX_BASKETWORKFLOWTYPE_HIGHLEVELAPI)
        			{
        				$basketRef = $pProductIdentData['basketref'];
        			}

					$userName = '';
					$rand = UtilsObj::createRandomString(4);
                	$batchRef = $onlineArray['batchref'] = $rand . '_' . UtilsObj::createRandomString(10);

                	if ((($userID < 0) && ($canSignIn == 0)) || ($basketAPIWorkFlowtype == TPX_BASKETWORKFLOWTYPE_HIGHLEVELAPI))
                	{
                		$guestWorkFlowMode = TPX_GUESTWORKFLOWMODE_AUTOMATIC;
                	}
                }
                else
                {
                	$productCollectionCode = $pProductIdentData['collectioncode'];
                	$productLayoutCode = $pProductIdentData['layoutcode'];
                	$browserLanguageCode = $gSession['browserlanguagecode'];
                	$groupCode = $gSession['licensekeydata']['groupcode'];
					$brandCode = $gSession['webbrandcode'];
                    $userID = $gSession['userid'];
                    $userName = $gSession['username'];
					$batchRef = $gSession['ref'] . '_' . UtilsObj::createRandomString(10);
					$minLife = -1;
                }

				$projectRef = $pProjectRef;
				$workflowType = $pWorkflowType;
				$loadedStatus = $pProductIdentData['loadedstatus'];
                $templateRef = $pProductIdentData['templateref'];
                $originalRef = $pProductIdentData['originalref'];
				$threeDPreview = $pProductIdentData['3dmodelsystemresourcefileid'];
				$canShareProject = $pProductIdentData['canshareproject'];

                $sessionExpireArray = DatabaseObj::getSessionExpire($gSession['ref']);
                $sessionExpireTime = $sessionExpireArray['sessionexpiretime'];

                $licenseKeyDataArray = DatabaseObj::getLicenseKeyFromCode($groupCode);

                $userAccount = DatabaseObj::getUserAccountFromID($userID);
                $userName = $userAccount['contactfirstname'] . ' ' . $userAccount['contactlastname'];
                $login = $userAccount['login'];
				$companyCode = $userAccount['companycode'];

                $isMobile = $gSession['ismobile'];
                $isLargeScreen = $gSession['islargescreen'];

                $onlineArray['sessionexpiredate'] = $sessionExpireTime;

                $onlineArray['ssotoken'] = $pProductIdentData['ssotoken'];
                $onlineArray['ssoprivatedata'] = $pProductIdentData['ssoprivatedata'];
                $onlineArray['assetservicedata'] = $pProductIdentData['assetservicedata'];
                $onlineArray['maxsessionexpiretime'] = $pProductIdentData['ssoexpiredate'];

				$enableSwitchingEditor = $pProductIdentData['enableswitchingeditor'];
				$automaticallyApplyPerfectlyClearMode = $pProductIdentData['automaticallyapplyperfectlyclearmode'];
                break;
            }
            case TPX_OPEN_MODE_PREVIEW_EXISITING:
            {
            	$populateAutoLayoutSettings = false;
				$browserLanguageCode = $gSession['browserlanguagecode'];
                $groupCode = $pPreviewExisitingProjectDataArray['groupcode'];
                $brandCode = $pPreviewExisitingProjectDataArray['webbrandcode'];
                $userID = $pPreviewExisitingProjectDataArray['userid'];
                $userAccount = DatabaseObj::getUserAccountFromID($userID);
                $login = $userAccount['login'];
                $companyCode = $userAccount['companycode'];

                $projectRef = $pPreviewExisitingProjectDataArray['projectref'];
                $userName = $gSession['username'];
                $productCollectionCode = $pPreviewExisitingProjectDataArray['productcollectioncode'];
                $productLayoutCode = $pPreviewExisitingProjectDataArray['productlayoutcode'];
				$workflowType = $pPreviewExisitingProjectDataArray['workflowtype'];
				$basketAPIWorkFlowtype = $pProductIdentData['basketapiworkflowtype'];

                // if the preview source is from a shared link then we need to pass the session expire time 1 day into the future.
                // this is because we do not have a MediaAlbumWebSession
                if ($pPreviewExisitingProjectDataArray['previewviewsource'] == 'SHARE')
                {
                    $sessionExpireTime = strtotime(DatabaseObj::getServerTime(24 * 60));
				}

				if ($basketAPIWorkFlowtype == TPX_BASKETWORKFLOWTYPE_HIGHLEVELAPI)
				{
					$guestWorkFlowMode = TPX_GUESTWORKFLOWMODE_AUTOMATIC;
				}
				else if ($basketAPIWorkFlowtype == TPX_BASKETWORKFLOWTYPE_LOWLEVELAPI)
				{
					$canSignIn = $pProductIdentData['cansignin'];

					// if the the project is still a guest project and cansignin is disabled then force
					// automatic guest workflow mode. This is to prevent the signin dialog from appearing
					if (($userID < 0) && ($canSignIn == 0))
					{
						$guestWorkFlowMode = TPX_GUESTWORKFLOWMODE_AUTOMATIC;
					}
				}

                $licenseKeyDataArray = DatabaseObj::getLicenseKeyFromCode($groupCode);
                $batchRef = '';
				$minLife = -1;
				$loadedStatus = $pPreviewExisitingProjectDataArray['loadedstatus'];
				$templateRef = $pPreviewExisitingProjectDataArray['templateref'];

				// Page controls are turned off.
				$insertDeleteButtonsVisibility = TPX_INSERTDELETEBUTTONS_VISIBILITY_HIDDEN;
				$totalPagesDropdownMode = TPX_TOTALPAGES_DROPDOWN_MODE_DISABLED;
				$setInsertDeleteButtonsVisibility = false;
				$setTotalPagesDropdownMode = false;
                break;
            }
        }

        $webBrandDataArray = DatabaseObj::getBrandingFromCode($brandCode);
        $onlineDesignerURL = $webBrandDataArray['onlinedesignerurl'];
        $onlineDesignerCDNURL = $webBrandDataArray['onlinedesignercdnurl'];
        $onlineDesignerLogoutURL = $webBrandDataArray['onlinedesignerlogouturl'];
        $savePromptDelay = $webBrandDataArray['onlinedesignersigninregisterpromptdelay'];

		$onlineArray['componentupsellsettings'] = ($licenseKeyDataArray['usedefaultcomponentupsellsettings']) ? $webBrandDataArray['componentupsellsettings'] : $licenseKeyDataArray['componentupsellsettings'];

		if ($pOpenMode === TPX_OPEN_MODE_EXISTING_PROJECT)
		{
			if (! $pProductIdentData['allowupsell'])
			{
				$onlineArray['componentupsellsettings'] = 0;
			}
		}

        if (($licenseKeyDataArray['isactive'] == 1) && ($licenseKeyDataArray['availableonline'] == 1))
        {
			$onlineArray['autoupdateproductlist'] = [];

			// If we are starting a new project or opening an exisiting not in preview mode, get the product data.
			// This data is needed for both live pricing and for the alternative layout list.
			if (($pOpenMode === TPX_OPEN_MODE_NEW_PROJECT) || ($pOpenMode === TPX_OPEN_MODE_EXISTING_PROJECT))
			{
				$autoUpdateProductList = self::buildAutoUpdateProductList($productCollectionCode, $productLayoutCode, $groupCode, $brandCode, $companyCode, $pProductIdentData['producttreesdata']);

				$onlineArray['autoupdateproductlist'] = $autoUpdateProductList;
				$onlineArray['autoupdateproductlist'][0]['showpriceswithtax'] = $licenseKeyDataArray['showpriceswithtax'];

				if ($pOpenMode === TPX_OPEN_MODE_NEW_PROJECT)
				{
					if (! self::isProductLayoutActive($productLayoutCode, $onlineArray['autoupdateproductlist'], $productCollectionCode, $pWorkflowType))
					{
						$result = TPX_ONLINE_ERROR_PRODUCTNOTAVAILABLE;
					}
				}
			}

            $webBrandName = $webBrandDataArray['name'];
            $webBrandApplicationName = $webBrandDataArray['applicationname'];
            $onlineDesignerURL = $webBrandDataArray['onlinedesignerurl'];
            $onlineDesignerCDNURL = $webBrandDataArray['onlinedesignercdnurl'];
            $onlineDesignerLogoutURL = $webBrandDataArray['onlinedesignerlogouturl'];
            $savePromptDelay = $webBrandDataArray['onlinedesignersigninregisterpromptdelay'];

            // get the currency for the license key
            if ($licenseKeyDataArray['usedefaultcurrency'] == 1)
            {
                $currencyDataArray = DatabaseObj::getCurrency($gConstants['defaultcurrencycode']);
                $currencyDataArray['exchangerate'] = 1; // we are using the default currency so set the exchange rate to 1
				$currencyCode = $gConstants['defaultcurrencycode'];
            }
            else
            {
                $currencyDataArray = DatabaseObj::getCurrency($licenseKeyDataArray['currencycode']);
				$currencyCode = $licenseKeyDataArray['currencycode'];
            }

            $currencySymbol = $currencyDataArray['symbol'];
            $currencySymbolAtFront = $currencyDataArray['symbolatfront'];
            $currencyExchangeRate = $currencyDataArray['exchangerate'];
            $currencyDecimalPlaces = $currencyDataArray['decimalplaces'];
            $localeDecimalPoint = LocalizationObj::getLocaleDecimalPoint($gSession['browserlanguagecode']);
            $localeThousandSeperator = LocalizationObj::getLocaleThousandsSeparator($gSession['browserlanguagecode']);

            // default the image scaling values
			$onlineArray['imagescalingbefore'] = 0.0;

			// use maxuploadmp instead of imagescalingafter due to legacy reasons
			$onlineArray['maxuploadmp'] = 0.0;

			// is the global setting for image scaling before on/off...
            $isImageScalingBeforeEnabled = UtilsObj::getArrayParam($ac_config, 'ALLOWIMAGESCALINGBEFORE', 0) == 1 ? true : false;

            // if it's enabled follow the hierarchy (product > license key > brand)...
            if ($isImageScalingBeforeEnabled)
            {
                // if the product has image scaling before enabled, use it...
                if ($pProductIdentData['imagescalingbeforeenabled'] == 1)
                {
                    $onlineArray['imagescalingbefore'] = $pProductIdentData['imagescalingbefore'];
                }
                // if the product has the default enabled, fall back onto the license key/brand ...
                else if ($pProductIdentData['usedefaultimagescalingbefore'] == 1)
                {
                    // if the license key image scaling value is set use that else use the one from the brand
                    if ($licenseKeyDataArray['usedefaultimagescalingbefore'] == 0)
                    {
                        if ($licenseKeyDataArray['imagescalingbeforeenabled'])
                        {
                            $onlineArray['imagescalingbefore'] = $licenseKeyDataArray['imagescalingbefore'];
                        }
                    }
                    else
                    {
                        if ($webBrandDataArray['imagescalingbeforeenabled'])
                        {
                            $onlineArray['imagescalingbefore'] = $webBrandDataArray['imagescalingbefore'];
                        }
                    }
                }
                // else -> do nothing. if product use default & enable are not set, do not apply image scaling
            }

            // if before upload scaling is enabled then clamp the upper and lower bounds
            if ($onlineArray['imagescalingbefore'] > 0)
            {
	            if ($onlineArray['imagescalingbefore'] < 2.00)
	            {
	            	$onlineArray['imagescalingbefore'] = 2.00;
	            }
	            else if ($onlineArray['imagescalingbefore'] > 999.99)
	            {
	            	$onlineArray['imagescalingbefore'] = 999.99;
	            }
			}

			// if the license key image scaling value is set use that else use the one from the brand
            if ($licenseKeyDataArray['usedefaultimagescalingafter'] == 0)
            {
            	if ($licenseKeyDataArray['imagescalingafterenabled'])
            	{
            		$onlineArray['maxuploadmp'] = $licenseKeyDataArray['imagescalingafter'];
            	}
            }
            else
            {
            	if ($webBrandDataArray['imagescalingafterenabled'])
            	{
            		$onlineArray['maxuploadmp'] = $webBrandDataArray['imagescalingafter'];
            	}
            }

            // if after upload scaling is enabled then clamp the upper and lower bounds
            if ($onlineArray['maxuploadmp'] > 0)
            {
	            if ($onlineArray['maxuploadmp'] < 30.00)
	            {
	            	$onlineArray['maxuploadmp'] = 30.00;
	            }
	            else if ($onlineArray['maxuploadmp'] > 999.99)
	            {
	            	$onlineArray['maxuploadmp'] = 999.99;
	            }
	        }

			$onlineArray['showshufflelayoutoptions'] = 0;
            $shuffleLayout = 0;

            if ($licenseKeyDataArray['usedefaultshufflelayout'] == 0)
            {
            	$onlineArray['showshufflelayoutoptions'] = $licenseKeyDataArray['showshufflelayoutoptions'];

            	if ($licenseKeyDataArray['showshufflelayoutoptions'])
            	{
            		 $shuffleLayout = $licenseKeyDataArray['shufflelayout'];
            	}
            }
            else
            {
            	$onlineArray['showshufflelayoutoptions'] = $webBrandDataArray['showshufflelayoutoptions'];

            	if ($webBrandDataArray['showshufflelayoutoptions'])
            	{
            		 $shuffleLayout = $webBrandDataArray['shufflelayout'];
            	}
            }

            $onlineArray['shufflelayoutleftright'] = false;
            $onlineArray['shufflelayoutspread'] = false;
            $onlineArray['shufflelayoutpictures'] = false;

            if ($shuffleLayout != 0)
            {
				$onlineArray['shufflelayoutleftright'] = (($shuffleLayout & 1) > 0) ? true : false;
				$onlineArray['shufflelayoutspread'] = (($shuffleLayout & 2) > 0) ? true : false;
				$onlineArray['shufflelayoutpictures'] = (($shuffleLayout & 4) > 0) ? true : false;
            }

            $onlineArray['showautodesignlayoutoptions'] = 0;
            $onlineArray['autodesignlayout'] = 0;

            // populateAutoLayoutSettings is set for new and editting projects
	        if ($populateAutoLayoutSettings)
	        {
	        	$autoLayoutConfig = UtilsObj::readConfigFile('../config/autolayout.conf');

	        	// only attempt to parse the auto layout settings if there is a config file
	        	if (count($autoLayoutConfig) > 0)
	        	{
		        	// settingToCheck is an array of all the different combinations which the autolayout config could be set up with
		        	// 0 - the current productcollection and current layout
		        	// 1 - any productcolleciton and the current layout
		        	// 2 - the current productcollection and any layout
		        	// 3 - any productcollection and any layout
		        	$settingsToCheck = array($productCollectionCode  . '.' . $productLayoutCode, '*.' . $productLayoutCode, $productCollectionCode . '.*', '*.*');
		        	$found = false;
		        	$counter = 0;
		        	$max = count($settingsToCheck);

		        	// loop around each settingToCheck until we have found the settings or we have run out of possibilities
		        	while ((!$found) && ($counter < $max))
		        	{
		        		$autoLayoutSetting = self::getAutolayoutSetting($autoLayoutConfig, $settingsToCheck[$counter], $found);
		        		$counter++;
		        	}

	            	if ($found)
	            	{
		            	// set up the online array with the settings
		            	// if nothing found then the defaults will be used
		            	$onlineArray['showautodesignlayoutoptions'] = $autoLayoutSetting['showautodesignlayoutoptions'];
		            	$onlineArray['autodesignlayout'] = $autoLayoutSetting['autodesignlayout'];
	            	}
	            }
	        }

            // Upsell Pages count.
            // If the product has upsell count value.
            if ($pProductIdentData['usedefaultaveragepicturesperpage'] == 0)
            {
                $onlineArray['averagepicturesperpage'] = $pProductIdentData['averagepicturesperpage'];
            }
            // if the product has the default enabled, fall back onto the license key/brand ...
            else
            {
                // if the license key upsell value is set use that else use the one from the brand.
                if ($licenseKeyDataArray['usedefaultaveragepicturesperpage'] == 0)
                {
                    $onlineArray['averagepicturesperpage'] = $licenseKeyDataArray['averagepicturesperpage'];
                }
                else
                {
                    $onlineArray['averagepicturesperpage'] = $webBrandDataArray['averagepicturesperpage'];
                }
            }

            $onlineArray['ismobile'] = $isMobile;
            $onlineArray['islargescreen'] = $isLargeScreen;
            $onlineArray['collectioncode'] = $productCollectionCode;
            $onlineArray['productlayoutcode'] = $productLayoutCode;
            $onlineArray['workflowtype'] = $workflowType;
            $onlineArray['userid'] = $userID;
            $onlineArray['login'] = $login;
            $onlineArray['webbrandcode'] = $brandCode;
            $onlineArray['webbrandname'] = $webBrandName;
            $onlineArray['onlinedesignerurl'] = $onlineDesignerURL;
            $onlineArray['onlinedesignercdnurl'] = $onlineDesignerCDNURL;
            $onlineArray['onlinedesignerlogouturl'] = $onlineDesignerLogoutURL;
            $onlineArray['webbrandapplicationname'] = $webBrandApplicationName;
            $onlineArray['webbranddefaultcommunicationpreference'] = $gSession['webbranddefaultcommunicationpreference'];
            $onlineArray['browserlanguagecode'] = $browserLanguageCode;
            $onlineArray['defaultlanguagecode'] = $gConstants['defaultlanguagecode'];
            $onlineArray['currencysymbol'] = $currencySymbol;
            $onlineArray['currencysymbolatfront'] = $currencySymbolAtFront;
            $onlineArray['currencyexchangerate'] = $currencyExchangeRate;
            $onlineArray['currencydecimalplaces'] = $currencyDecimalPlaces;
            $onlineArray['currencycode'] = $currencyCode;
            $onlineArray['currencyisonumber'] = $currencyDataArray['isonumber'];
            $onlineArray['currencyname'] = $currencyDataArray['name'];
            $onlineArray['localedecimalpoint'] = $localeDecimalPoint;
            $onlineArray['localethousandseperator'] = $localeThousandSeperator;
            $onlineArray['taopixwebcallbackurl'] = $gSession['webbrandwebroot'];
            $onlineArray['batchref'] = $batchRef;
            $onlineArray['licensekeydata']['ownercode'] = $ownerCode;
            $onlineArray['licensekeydata']['groupcode'] = $groupCode;
            $onlineArray['licensekeydata']['companycode'] = $companyCode;
            $onlineArray['licensekeydata']['countrycode'] = $licenseKeyDataArray['countrycode'];
            $onlineArray['projectref'] = $projectRef;
            $onlineArray['completeorder'] = 0;
            $onlineArray['checkoutname'] = $checkOutName;
            $onlineArray['abandonname'] = $abandonName;
            $onlineArray['abandonurl'] = $abandonURL;
            $onlineArray['minlife'] = $minLife;
            $onlineArray['basketapiworkflowtype'] = $basketAPIWorkFlowtype;
            $onlineArray['basketref'] = $basketRef;
            $onlineArray['cansignin'] = $canSignIn;
            $onlineArray['cansignout'] = $canSignOut;
            $onlineArray['disablebackbutton'] = $disableBackButton;
            $onlineArray['projectname'] = $projectName;
			$onlineArray['cancreateaccounts'] = true;
			$onlineArray['editprojectnameonfirstsave'] = $editProjectNameOnFirstSave;
			$onlineArray['ccnotificationsenabled'] = $ccNotificationsEnabled;
			$onlineArray['requirepasswordforsessioninactivity'] = $requirePasswordForSessionInactivity;

            if ($licenseKeyDataArray['cancreateaccounts'] == 0)
            {
            	$onlineArray['cancreateaccounts'] = false;
            }

            $onlineArray['guestworkflowmode'] = $guestWorkFlowMode;
            $onlineArray['username'] = $userName;
            $onlineArray['onlinedesignersigninregisterpromptdelay'] = $savePromptDelay;
			$onlineArray['loadedstatus'] = $loadedStatus;
            $onlineArray['templateref'] = $templateRef;
            $onlineArray['originalref'] = $originalRef;
            $onlineArray['3dmodelsystemresourcefileid'] = $pProductIdentData['3dmodelsystemresourcefileid'];
			$onlineArray['onlinedesignerlogolinkurl'] = $pProductIdentData['onlinedesignerlogolinkurl'];
			$onlineArray['onlinedesignerlogolinktooltip'] = $pProductIdentData['onlinedesignerlogolinktooltip'];
			$onlineArray['newwizardmode'] = $newWizardMode;
			$onlineArray['onlineeditormode'] = $pProductIdentData['onlineeditormode'];
			$onlineArray['enableswitchingeditor'] = $enableSwitchingEditor;
			$onlineArray['canshareproject'] = $canShareProject;
			$onlineArray['automaticallyapplyperfectlyclearmode'] = $automaticallyApplyPerfectlyClearMode;
			$onlineArray['minimumprintsperproject'] = $pProductIdentData['minimumprintsperproject'];

			// copy the google tag manager setting from brand record
			$onlineArray['googletagmanageronlinecode'] = $webBrandDataArray['googletagmanageronlinecode'];

			// Smart Guides settings.
			$onlineArray['smartguidesenable'] = 0;
            $onlineArray['smartguidesobjectguidecolour'] = TPX_SMARTGUIDES_OBJECT_GUIDECOLOUR;
            $onlineArray['smartguidespageguidecolour'] = TPX_SMARTGUIDES_PAGE_GUIDECOLOUR;

			if ($licenseKeyDataArray['usedefaultsmartguidessettings'] == 0)
            {
            	$onlineArray['smartguidesenable'] = $licenseKeyDataArray['smartguidesenable'];
            	$onlineArray['smartguidesobjectguidecolour'] = $licenseKeyDataArray['smartguidesobjectguidecolour'];
            	$onlineArray['smartguidespageguidecolour'] = $licenseKeyDataArray['smartguidespageguidecolour'];
            }
            else
            {
				$onlineArray['smartguidesenable'] = $webBrandDataArray['smartguidesenable'];
            	$onlineArray['smartguidesobjectguidecolour'] = $webBrandDataArray['smartguidesobjectguidecolour'];
            	$onlineArray['smartguidespageguidecolour'] = $webBrandDataArray['smartguidespageguidecolour'];
            }

			$onlineArray['sizeandpositionmeasurementunits'] = TPX_COORDINATE_SCALE_INCHES;

			if ($licenseKeyDataArray['usedefaultsizeandpositionsettings'] == 0)
			{
				$onlineArray['sizeandpositionmeasurementunits'] = $licenseKeyDataArray['sizeandpositionmeasurementunits'];
			}
			else
			{
				$onlineArray['sizeandpositionmeasurementunits'] = $webBrandDataArray['sizeandpositionmeasurementunits'];
			}

			// Toggle feature
			$onlineArray['featuretoggle'] = $pProductIdentData['featuretoggle'];

			$onlineArray['aimodeoverride'] = $pProductIdentData['aimodeoverride'];

			// Password strength for new accounts.
            $onlineArray['passwordstrengthmin'] = $gConstants['minpasswordscore'];

			// New users registration uses an email address.
            $onlineArray['registerusingemail'] = $webBrandDataArray['registerusingemail'];

			// Show/hide insert/delete pages buttons.
			if ($setInsertDeleteButtonsVisibility)
			{
				if ($licenseKeyDataArray['usedefaultinsertdeletebuttonsvisibility'] == 0)
				{
					$insertDeleteButtonsVisibility = $licenseKeyDataArray['insertdeletebuttonsvisibility'];
				}
				else
				{
					$insertDeleteButtonsVisibility = $webBrandDataArray['insertdeletebuttonsvisibility'];
				}
			}

			$onlineArray['insertdeletebuttonsvisibility'] = $insertDeleteButtonsVisibility;

			// Total pages dropdown mode.
			if ($setTotalPagesDropdownMode)
			{
				if ($licenseKeyDataArray['usedefaulttotalpagesdropdownmode'] == 0)
				{
					$totalPagesDropdownMode = $licenseKeyDataArray['totalpagesdropdownmode'];
				}
				else
				{
					$totalPagesDropdownMode = $webBrandDataArray['totalpagesdropdownmode'];
				}
			}

			$onlineArray['totalpagesdropdownmode'] = $totalPagesDropdownMode;

			// Retro prints
			$onlineArray['retroprints'] = $pProductIdentData['retroprints'];
		}
        else
        {
            $result = TPX_ONLINE_ERROR_INACTIVELICENSEKEY;
        }

        $onlineArray['openmode'] = $pOpenMode;
        $onlineArray['result'] = $result;

        return $onlineArray;
	}

	/**
	 * Checks the passed value is a valid page control option.
	 *
	 * @param int $pPageControlsSettingValue The value of the setting.
	 *
	 * @return bool True if the setting is valid.
	 */
	static function isPageControlsSettingValidate($pPageControlsSettingValue)
	{
		$isPageControlsSettingValid = false;

		// Check the value is a number and it is a single digit.
		if (is_numeric($pPageControlsSettingValue) && (strlen($pPageControlsSettingValue) == 1))
		{
			$isPageControlsSettingValid = true;
		}

		return $isPageControlsSettingValid;
	}

    static function getAutolayoutSetting($pAutoLayoutConfig, $pSettingString, &$pFound)
    {
    	$pFound = false;

		$setting = UtilsObj::getArrayParam($pAutoLayoutConfig, $pSettingString, '');

		if ($setting != '')
		{
			$pFound = true;
		}

		return self::parseAutoLayoutSettings($setting);
    }

    static function parseAutoLayoutSettings($pSettingString)
    {
    	$returnArray = array('showautodesignlayoutoptions' => 0, 'autodesignlayout' => 0);

    	// make sure the delminater is present
    	if (strrpos($pSettingString, ',') !== false)
    	{
    		// split the string using the a comma
    		list($visible, $on) = explode(',', $pSettingString);

    		// the setting is on if it equals a 1 or the word On
    		$returnArray['showautodesignlayoutoptions'] = (strtolower($visible) == '1' || strtolower($visible) == 'on') ? 1 : 0;
    		$returnArray['autodesignlayout'] = (strtolower($on) == '1' || strtolower($on) == 'on') ? 1 : 0;
    	}

    	return $returnArray;

    }

    static function queryOrderStatus($pParamArray)
    {
        $resultArray = array();
		$status = -1;
		$canModify = 0;
		$projectRef = '';

        $dbObj = DatabaseObj::getGlobalDBConnection();

        if ($dbObj)
        {
			foreach ($pParamArray['projectreflist'] as $projectRef => $orderItemID)
			{
				$resultArray['projectreflist'][$projectRef]['result'] = TPX_ONLINE_ERROR_PROJECTDOESNOTEXIST;
				$resultArray['projectreflist'][$projectRef]['resultmessage'] = '';
				$resultArray['projectreflist'][$projectRef]['canedit'] = 0;
				$resultArray['projectreflist'][$projectRef]['orderstatus'] = 0;
				$resultArray['projectreflist'][$projectRef]['projectref'] = $projectRef;

				$validProjectRef = preg_match('/^[0-9]+[_]{1}[0-9]+$/', $projectRef);

				if ($validProjectRef > 0)
				{
					$sql = 'SELECT `status`, `canmodify`, `projectref` FROM `ORDERITEMS` WHERE `projectref` = ? and `source` = 1';

					if ($orderItemID != '' && $orderItemID > 0)
					{
						$sql.= ' AND `id` = ?';
					}
					else
					{
						$sql.= ' AND `origorderitemid` = ?';
						$orderItemID = 0;
					}

					if ($stmt = $dbObj->prepare($sql))
					{
						if ($stmt->bind_param('si', $projectRef, $orderItemID))
						{
							if ($stmt->bind_result($status, $canModify, $projectRef))
							{
								if ($stmt->execute())
								{
									if ($stmt->fetch())
									{
										// filter the current production status
										if ($status == TPX_ITEM_STATUS_AWAITING_FILES)
										{
											$status = 0; // waiting for files
										}
										elseif (($status >= TPX_ITEM_STATUS_FILES_ON_REMOTE_FTP_SERVER) && ($status < TPX_ITEM_STATUS_SHIPPED_TO_CUSTOMER))
										{
											$status = 1; // item in production
										}
										elseif ($status == TPX_ITEM_STATUS_SHIPPED_TO_CUSTOMER)
										{
											$status = 2; // item shipped to customer
										}
										elseif (($status >= TPX_ITEM_STATUS_SHIPPED_TO_DISTRIBUTION_CENTRE) && ($status < TPX_ITEM_STATUS_SHIPPED_RECEIVED_AT_STORE))
										{
											$status = 3; // item shipped to distribution centre / store
										}
										elseif ($status == TPX_ITEM_STATUS_SHIPPED_RECEIVED_AT_STORE)
										{
											$status = 4; // item received at store
										}
										elseif ($status == TPX_ITEM_STATUS_SHIPPED_COLLECTED_BY_CUSTOMER)
										{
											$status = 5; // item collected by customer
										}

										$resultArray['projectreflist'][$projectRef]['result'] = '';
										$resultArray['projectreflist'][$projectRef]['resultmessage'] = '';
										$resultArray['projectreflist'][$projectRef]['canedit'] = $canModify;
										$resultArray['projectreflist'][$projectRef]['orderstatus'] = $status;
									}
								}
								else
								{
									$resultArray['error'] = 'str_DatabaseError';
									$resultArray['errorparam'] = 'getUserProjectStatusList execute: ' . $dbObj->error;
								}
							}
							else
							{
								$resultArray['error'] = 'str_DatabaseError';
								$resultArray['errorparam'] = 'getUserProjectStatusList bind_result: ' . $dbObj->error;
							}
						}
						else
						{
							$resultArray['error'] = 'str_DatabaseError';
							$resultArray['errorparam'] = 'getUserProjectStatusList bind_param: ' . $dbObj->error;
						}

						// if the query failed for some reason we may still have a statement so get rid of it now
						if ($stmt)
						{
							$stmt->free_result();
							$stmt->close();
							$stmt = null;
						}
					}
					else
					{
						$resultArray['error'] = 'str_DatabaseError';
						$resultArray['errorparam'] = 'getUserProjectStatusList prepare: ' . $dbObj->error;
					}
				}
            }
            $dbObj->close();
        }
		else
		{
			$resultArray['error'] = 'str_DatabaseError';
			$resultArray['errorparam'] = 'getUserProjectStatusList connection unable';
		}

        return $resultArray;
    }

    static function reorder($pParamArray)
    {
        $resultArray = Array();
        $result = '';
        $resultParam = '';
		$retroPrintsComponents = [];

        $projectRef = $pParamArray['projectref'];
        $orderItemID = $pParamArray['orderitemid'];
        $languageCode =  $pParamArray['languagecode'];
		$systemConfigArray = DatabaseObj::getSystemConfig();

        $dbObj = DatabaseObj::getGlobalDBConnection();

        if ($dbObj)
        {
            $ownerCode = '';
            $productType = 0;
            $shareID = 0;
            $productCollectionCode = '';
            $productCollectionName = '';
            $productCode = '';
            $productName = '';
            $productPageFormat = 0;
            $productSpreadPageFormat = 0;
            $productCover1Format = 0;
            $productCover2Format = 0;
            $productOutputFormat = 0;
            $productHeight = 0.00;
            $productWidth = 0.00;
            $pageCount = 0;
            $uploadGroupCode = '';
            $uploadOrderID = 0;
            $uploadOrderNumber = '';
            $uploadOrderItemID = 0;
            $uploadRef = '';
            $projectRefOrig = '';
            $projectName = '';
            $appVersion = '';
            $orderPageCount = 0;
            $groupCode = '';
            $groupData = '';
            $shoppingCartType = TPX_SHOPPINGCARTTYPE_EXTERNAL;
            $currencyExchangeRate = 1.0000;
            $useDefaultCurrency = 1;
            $currencyCode = '';
            $currencyDecimalPlaces = 2;
            $groupName = '';
            $groupAddress1 = '';
            $groupAddress2 = '';
            $groupAddress3 = '';
            $groupAddress4 = '';
            $groupAddressCity = '';
            $groupAddressCounty = '';
            $groupAddressState = '';
            $groupPostCode = '';
            $groupCountryCode = '';
            $groupEmailAddress = '';
            $groupTelephoneNumber = '';
            $groupContactFirstName = '';
            $groupContactLastName = '';
            $coverCode = '';
            $paperCode = '';
            $orderHeaderId = 0;
            $orderNumber = 0;
            $brandCode = '';
            $uploadAppVersion = '';
            $uploadAppPlatform = '';
            $uploadAppCPUType = '';
            $uploadAppOSVersion = '';
            $isOflineOrder = 0;
            $uploadMethod = 0;
            $currentOwner = '';
            $projectStartTime = '';
            $projectDuration = 0;
            $projectDataSize = 0;
            $projectUploadDuration = 0;
            $canUpload = 1;
            $previewsOnline = 0;
            $productCollectionOrigOwnerCode = '';
            $source = TPX_SOURCE_ONLINE;
            $productOptions = TPX_PRODUCTOPTION_PRICING_NON;
			$dataAvailable = 0;
			$userID = 0;
			$itemTotalSell = 0.00;
			$itemProductTotalSell = 0.00;
			$itemSubTotal = 0.00;

			$sql = "SELECT oh.ownercode, oi.projectref, oi.projectreforig, oi.projectname, oi.projectbuildstartdate,
                oi.projectbuildduration, oi.productcollectionname, oi.productcollectioncode, oi.productcollectionorigownercode, oi.productcode,
	      		oi.productname, oi.producttype, oi.productpageformat,
				oi.productpageformat, oi.productspreadpageformat, oi.productcover1format, oi.productcover2format, oi.productoutputformat, oi.productheight, oi.productwidth,
				oi.pagecount, oi.uploadgroupcode, oi.uploadorderid, oi.uploadordernumber, oi.uploadorderitemid, oi.uploadref, oi.uploadappversion, oi.pagecountpurchased,
				oh.groupcode, oh.groupdata, oh.shoppingcarttype, oh.id, oh.ordernumber, oh.webbrandcode, oi.uploadappversion, oi.uploadappplatform, oi.uploadappcputype, oi.uploadapposversion,
				oi.uploaddatasize, oi.uploadduration, oh.offlineorder, oi.uploadmethod, oi.currentowner, oi.previewsonline, oi.canupload, oi.source, oi.productoptions, oi.userid,
				oi.totalsell, oi.producttotalsell, oi.subtotal,
				IF (oi.productiondata=0 AND oi.ftpdata=0 AND oi.dbdata = '0000-00-00 00:00:00', 1, 0) as dataavailable
				FROM ORDERITEMS oi
				JOIN ORDERHEADER oh ON oh.id = oi.orderid WHERE oi.projectref = ?";

			if ($orderItemID != '' && $orderItemID > 0)
			{
				$sql.= ' AND oi.id = ?';
			}
			else
			{
				$sql.= ' AND oi.origorderitemid = ?';
				$orderItemID = 0;
			}

            if ($stmt = $dbObj->prepare($sql))
            {
                if ($stmt->bind_param('si', $projectRef, $orderItemID))
                {
                    if ($stmt->execute())
                    {
                        if ($stmt->store_result())
                        {
                            if ($stmt->num_rows > 0)
                            {
                                if ($stmt->bind_result($ownerCode, $projectRef, $projectRefOrig, $projectName, $projectStartTime,
                                                $projectDuration, $productCollectionName, $productCollectionCode, $productCollectionOrigOwnerCode, $productCode,
                                                $productName, $productType, $productPageFormat, $productPageFormat,
                                                $productSpreadPageFormat, $productCover1Format, $productCover2Format, $productOutputFormat,
                                                $productHeight, $productWidth, $pageCount, $uploadGroupCode, $uploadOrderID,
                                                $uploadOrderNumber, $uploadOrderItemID, $uploadRef, $appVersion, $orderPageCount,
                                                $groupCode, $groupData, $shoppingCartType, $orderHeaderId, $orderNumber, $brandCode,
                                                $uploadAppVersion, $uploadAppPlatform, $uploadAppCPUType, $uploadAppOSVersion,
                                                $projectDataSize, $projectUploadDuration, $isOflineOrder, $uploadMethod, $currentOwner,
                                                $previewsOnline, $canUpload, $source, $productOptions, $userID, $itemTotalSell, $itemProductTotalSell,
												$itemSubTotal, $dataAvailable))
                                {
                                    if (!$stmt->fetch())
                                    {
                                        $result = 'str_DatabaseError';
                                        $resultParam = 'reorder select fetch ' . $dbObj->error;
                                    }
                                }
                                else
                                {
                                    $result = 'str_DatabaseError';
                                    $resultParam = 'reorder select bind result ' . $dbObj->error;
                                }
                            }
                            else
                            {
                                $result = 'str_DatabaseError';
                                $resultParam = 'reorder select num rows ' . $dbObj->error;
                            }
                        }
                        else
                        {
                            $result = 'str_DatabaseError';
                            $resultParam = 'reorder select store result ' . $dbObj->error;
                        }
                    }
                    else
                    {
                        $result = 'str_DatabaseError';
                        $resultParam = 'reorder select execute ' . $dbObj->error;
                    }
                }
                else
                {
                    $result = 'str_DatabaseError';
                    $resultParam = 'reorder select bind params ' . $dbObj->error;
                }


                $stmt->free_result();
                $stmt->close();
                $stmt = null;
            }
            else
            {
                $result = 'str_DatabaseError';
                $resultParam = 'reorder select prepare ' . $dbObj->error;
            }
            $dbObj->close();


            // if we have no errors determine if the user's group code exists in the database
            if ($result == '')
            {
                $licenseKeyArray = DatabaseObj::getLicenseKeyFromCode($groupCode);

                // Set correct companycode for the customer. Check to see if the license key belongs to a brand if it does
                // use the brand company code. If not use the license key companycode
                $companyCode = $licenseKeyArray['companyCode'];

                if ($licenseKeyArray['webbrandcode'] != '')
                {
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

                $productArray = DatabaseObj::getProductFromCollectionCodeAndLayoutCode($productCollectionCode, $productCode);
				$productPriceArray = DatabaseObj::getProductPrice($productCode, $groupCode, $companyCode, $currencyExchangeRate, $currencyDecimalPlaces, -1);

				if ($productPriceArray['result'] == '')
				{
					// get the product linking code for use in all component checks
					$productLinkingArray = DatabaseObj::getApplicableProductLinkCode($productCode);

					if ($productLinkingArray['error'] == '')
					{
						$productTreeProductCode = $productLinkingArray['linkedcode'];
					}
					else
					{
						// cannot safely price the product
						$result = ['error'];
					}
				}
				// first check to see if the product is not deleted and it is still active.
               	if (($result == '') && ($productArray['isactive'] == 1) && ($productArray['deleted'] == 0))
               	{
					// if the product has not been deleted and it is still active we need to check if there is still a valid price.
					if ($productPriceArray['result'] == '')
					{
						$productAssetArray = DatabaseObj::getOrderItemComponentAssets($orderItemID);
						$pictureArray = DatabaseObj::getOrderItemComponentSinglePrints($orderItemID, true);

						$consolidatedPicturesSizeStockArray = array();
						$applyBasePriceLineSubtract = true;

						if ($productOptions == TPX_PRODUCTOPTION_PRICING_PERCOMPONENTSUBCOMPONENT)
						{
							// consolidate the singleprint prices
							foreach ($pictureArray['pictures'] as $picture)
							{
								$componentSubComponentKey = $picture['componentcode'];

								if ($picture['subcomponentcode'] != '')
								{
									$componentSubComponentKey .= '.' . $picture['subcomponentcode'];
								}

								if (!array_key_exists($componentSubComponentKey, $consolidatedPicturesSizeStockArray))
								{
									$consolidatedPicturesSizeStockArray[$componentSubComponentKey]['qty'] = $picture['componentqty'];
									$consolidatedPicturesSizeStockArray[$componentSubComponentKey]['basepricelinesubtractapplied'] = false;
								}
								else
								{
									$consolidatedPicturesSizeStockArray[$componentSubComponentKey]['qty'] += $picture['componentqty'];
								}
							}
						}


						// check to see if all SINGLEPRINT components for the orderline have a price
						foreach ($pictureArray['pictures'] as $picture)
						{
							$lineBreakQTY = $picture['componentqty'];

							$componentSubComponentKey = $picture['componentcode'];

							if ($picture['subcomponentcode'] != '')
							{
								$componentSubComponentKey .= '.' . $picture['subcomponentcode'];
							}

							if ($productOptions == TPX_PRODUCTOPTION_PRICING_PERCOMPONENTSUBCOMPONENT)
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

							$componentCode = 'SINGLEPRINT' . '.' . $picture['componentcode'];

							$pictureArrayCacheKey = $companyCode . '.' . $groupCode . '.' . $productTreeProductCode;
							$pictureArrayCacheKey .= '.' . $componentCode . '.' . $pageCount . '.' . $lineBreakQTY . '.' . $picture['componentqty'] . '.' . $applyBasePrice;

							$picturePriceArray = DatabaseObj::getPriceCacheData($pictureArrayCacheKey);

							if (count($picturePriceArray) == 0)
							{
								$picturePriceArray = DatabaseObj::getPrice('$SINGLEPRINT\\', $componentCode, false, $productTreeProductCode, $groupCode,
											$companyCode, $currencyExchangeRate, $currencyDecimalPlaces, -1, $pageCount, $lineBreakQTY, $picture['componentqty'], false, false,
											-1, 0, '', $applyBasePriceLineSubtract);
								DatabaseObj::setPriceCacheData($pictureArrayCacheKey, $picturePriceArray);
							}

							$result = $picturePriceArray['result'];

							if ($result == '')
							{
								if ($picture['subcomponentcode'] != '')
								{
									// check to see if all SINGLEPRINT SUBCOMPONENTS for the orderline have a price
									$subComponentParentPath = '$SINGLEPRINT\\' . $picture['componentcode'] . '\\$SINGLEPRINTOPTION\\';
									$subComponentCode = 'SINGLEPRINTOPTION' . '.' . $picture['subcomponentcode'];

									$subComponentArrayCacheKey = $companyCode . '.' . $groupCode . '.' . $productTreeProductCode;
									$subComponentArrayCacheKey .= '.' . $subComponentCode . '.' . $pageCount . '.' . $lineBreakQTY . '.' . $picture['componentqty'] . '.' . $applyBasePrice;

									$subComponentPriceArray = DatabaseObj::getPriceCacheData($subComponentArrayCacheKey);

									if (count($subComponentPriceArray) == 0)
									{
										$subComponentPriceArray = DatabaseObj::getPrice($subComponentParentPath, $subComponentCode, false, $productTreeProductCode, $groupCode,
												$companyCode, $currencyExchangeRate, $currencyDecimalPlaces, -1, $pageCount, $lineBreakQTY, $picture['componentqty'], false, false,
												-1, 0, '', $applyBasePriceLineSubtract);
										DatabaseObj::setPriceCacheData($subComponentArrayCacheKey, $subComponentPriceArray);
									}

									if ($productOptions == TPX_PRODUCTOPTION_PRICING_PERCOMPONENTSUBCOMPONENT)
									{
										$consolidatedPicturesSizeStockArray[$componentSubComponentKey]['basepricelinesubtractapplied'] = true;
									}

									$result = $subComponentPriceArray['result'];

									if ($result != '')
									{
										$result = 'str_SinglePrintNoPriceAvailableError';
										$resultParam = LocalizationObj::getLocaleString($picture['subcomponentname'], $languageCode, true);
										break;
									}
								}
							}
							else
							{
								$result = 'str_SinglePrintNoPriceAvailableError';
								$resultParam = LocalizationObj::getLocaleString($picture['componentname'], $languageCode, true);
								break;
							}
						}

						$calendarCustomisationsArray = array();
						$calendarCustomisationsArray['calendarcustomisations'] = array();

                        if ($productType == TPX_PRODUCT_TYPE_CALENDAR)
						{
							$calendarCustomisationsOrderArray = DatabaseObj::getOrderItemComponentCalendarComponents($orderItemID);

							$emptyCalendarComptItemArray = array();
							$emptyCalendarComptItemArray['componentname'] = '';
							$emptyCalendarComptItemArray['componentcategory'] = 'CALENDARCUSTOMISATION';
							$emptyCalendarComptItemArray['componentcode'] = '';
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
							$calendarCustomisationsArray['calendarcustomisations']['CALENDARCUSTOMISATION.DATE'] = $emptyCalendarComptItemArray;
							$calendarCustomisationsArray['calendarcustomisations']['CALENDARCUSTOMISATION.DATE']['componentcode'] = 'CALENDARCUSTOMISATION.DATE';
							$calendarCustomisationsArray['calendarcustomisations']['CALENDARCUSTOMISATION.EVENTSET'] = $emptyCalendarComptItemArray;
							$calendarCustomisationsArray['calendarcustomisations']['CALENDARCUSTOMISATION.EVENTSET']['componentcode'] = 'CALENDARCUSTOMISATION.EVENTSET';
							$calendarCustomisationsArray['calendarcustomisations']['CALENDARCUSTOMISATION.ANY'] = $emptyCalendarComptItemArray;
							$calendarCustomisationsArray['calendarcustomisations']['CALENDARCUSTOMISATION.ANY']['componentcode'] = 'CALENDARCUSTOMISATION.ANY';


							// first find out which calendar components have been used in order
							$dateFoundInOrder = false;
							$eventSetFoundInOrder = false;
							$anyFoundInOrder = false;

							$dateQty = 0;
							$eventSetQty = 0;

							foreach ($calendarCustomisationsOrderArray['calendarcustomisations'] as $calendarCustomisations)
							{
								if ($calendarCustomisations['componentcode'] == 'DATE')
								{
									$dateFoundInOrder = true;
									$dateQty = $calendarCustomisations['componentqty'];
									$calendarCustomisationsArray['calendarcustomisations']['CALENDARCUSTOMISATION.DATE']['componentqty'] = $dateQty;
								}

								if ($calendarCustomisations['componentcode'] == 'EVENTSET')
								{
									$eventSetFoundInOrder = true;
									$eventSetQty = $calendarCustomisations['componentqty'];
									$calendarCustomisationsArray['calendarcustomisations']['CALENDARCUSTOMISATION.EVENTSET']['componentqty'] = $eventSetQty;
								}

								// if both date and eventset are found stop the loop since we have all we need
								if (($dateFoundInOrder) && ($eventSetFoundInOrder))
								{
									break;
								}

								// no need to look for any if either date or eventsets are found
								if ((! $dateFoundInOrder) && (! $eventSetFoundInOrder))
								{
									if ($calendarCustomisations['componentcode'] == 'ANY')
									{
										$anyFoundInOrder = true;
										$customAny = $calendarCustomisations;
									}
								}
							}

							if (($dateFoundInOrder) || ($eventSetFoundInOrder) || ($anyFoundInOrder))
							{

								// get all the calendar customisations which are attached to the products component tree
								$calendarCustomisationsDBArray = DatabaseObj::getComponentsInOrderSectionByCategory('$CALENDARCUSTOMISATION\\', 'CALENDARCUSTOMISATION',
																						$companyCode, $productTreeProductCode, $groupCode, 1.0, 2, -1, -1, -1, '', false, true);

								$componentItemCount = count($calendarCustomisationsDBArray['component']);

								$useAny = true;
								$anyFound = false;

								// look through all the components from the database and set the relevant data to the calendar customisation array items
								for ($j = 0; $j < $componentItemCount; $j++)
								{
									$componentArray = $calendarCustomisationsDBArray['component'][$j];

									$code = $componentArray['code'];

									$calendarCustomisationsArray['calendarcustomisations'][$code]['used'] = true;
									$calendarCustomisationsArray['calendarcustomisations'][$code]['componentcode'] = $code;
									$calendarCustomisationsArray['calendarcustomisations'][$code]['componentname'] = $componentArray['name'];
									$calendarCustomisationsArray['calendarcustomisations'][$code]['skucode'] = $componentArray['skucode'];
									$calendarCustomisationsArray['calendarcustomisations'][$code]['info'] = $componentArray['info'];
									$calendarCustomisationsArray['calendarcustomisations'][$code]['unitcost'] = $componentArray['unitcost'];
									$calendarCustomisationsArray['calendarcustomisations'][$code]['unitweight'] = $componentArray['unitweight'];
									$calendarCustomisationsArray['calendarcustomisations'][$code]['pricingmodel'] = $componentArray['pricingmodel'];
									$calendarCustomisationsArray['calendarcustomisations'][$code]['pricetaxcode'] = $componentArray['pricetaxcode'];
									$calendarCustomisationsArray['calendarcustomisations'][$code]['pricetaxrate'] = $componentArray['pricetaxrate'];
									$calendarCustomisationsArray['calendarcustomisations'][$code]['priceinfo'] = $componentArray['priceinfo'];

									// set the qty from the data sent from the desktop or online designer
									if (($code == 'CALENDARCUSTOMISATION.EVENTSET') && ($eventSetFoundInOrder))
									{
										$useAny = false;
									}
									else if (($code == 'CALENDARCUSTOMISATION.DATE') && ($dateFoundInOrder))
									{
										$useAny = false;
									}
									else if ($code == 'CALENDARCUSTOMISATION.ANY')
									{

										if ($dateFoundInOrder)
										{
											$calendarCustomisationsArray['calendarcustomisations']['CALENDARCUSTOMISATION.ANY']['componentqty'] += $dateQty;
											$anyFound = true;
										}

										if ($eventSetFoundInOrder)
										{
											$calendarCustomisationsArray['calendarcustomisations']['CALENDARCUSTOMISATION.ANY']['componentqty'] += $eventSetQty;
											$anyFound = true;
										}
									}
								}

								// ANY should only be used when DATE and EVENTSET are both missing
								if (!$useAny)
								{
									unset($calendarCustomisationsArray['calendarcustomisations']['CALENDARCUSTOMISATION.ANY']);
								}

								foreach ($calendarCustomisationsArray['calendarcustomisations'] as &$calendarCustomisation)
								{
									if (($calendarCustomisation['componentqty'] > 0) && ($calendarCustomisation['used']))
									{
										// get the price from the database and make sure that it is valid and the quantity is in range
										$calcustomPriceArray = DatabaseObj::getPrice($calendarCustomisation['path'], $calendarCustomisation['componentcode'], false,
																						$productTreeProductCode, $groupCode, $companyCode, $currencyExchangeRate,
																						$currencyDecimalPlaces, -1, -1, $calendarCustomisation['componentqty'], $calendarCustomisation['componentqty'], true, true, -1, 0, '', true);

										if (($calcustomPriceArray['result'] != '') || ($calcustomPriceArray['isactive'] == 0) || ($calcustomPriceArray['newqty'] != $calendarCustomisation['componentqty']))
										{
											$result = 'str_ErrorNoComponent';
											break;
										}
									}
								}

								unset($calendarCustomisation);
							}
                        }
						else if ($productType === TPX_PRODUCT_TYPE_PHOTO_BOOK)
						{
							// Get the Retro Prints components. Only a photobook can have Retro Prints components.
							$getOrderItemComponentRetroPrintsComponentsResult = DatabaseObj::getOrderItemComponentRetroPrintsComponents($orderItemID);

							if ($getOrderItemComponentRetroPrintsComponentsResult['result'] === '')
							{
								$retroPrintsComponents = $getOrderItemComponentRetroPrintsComponentsResult['retroprintscomponents'];
							}
							else
							{
								$result = $getOrderItemComponentRetroPrintsComponentsResult['result'];
								$resultParam = $getOrderItemComponentRetroPrintsComponentsResult['resultparam'];
							}
						}
					}
					else
					{
						$result = $productPriceArray['result'];
					}

                }
                else
                {
                	$result = 'str_ErrorProductNotAvailable2';
					$resultParam = LocalizationObj::getLocaleString($productArray['name'], $languageCode, true);
                }

                if ($result == '')
                {
                    $cartArray = Array();

                    $cartItemArray = Array();
                    $cartItemArray['shareid'] = $shareID;
                    $cartItemArray['source'] = $source;
                    $cartItemArray['origorderitemid'] = $orderItemID;
                    $cartItemArray['uploadgroupcode'] = $uploadGroupCode;
                    $cartItemArray['uploadorderid'] = $uploadOrderID;
                    $cartItemArray['uploadordernumber'] = $uploadOrderNumber;
                    $cartItemArray['uploadorderitemid'] = $uploadOrderItemID;
                    $cartItemArray['uploadref'] = $uploadRef;
                    $cartItemArray['collectioncode'] = $productCollectionCode;
                    $cartItemArray['collectionname'] = $productCollectionName;
                    $cartItemArray['productcode'] = $productCode;
                    $cartItemArray['productskucode'] = $productArray['skucode'];
                    $cartItemArray['productname'] = $productArray['name'];
                    $cartItemArray['producttype'] = $productType;
                    $cartItemArray['productpageformat'] = $productPageFormat;
                    $cartItemArray['productspreadformat'] = $productSpreadPageFormat;
                    $cartItemArray['productcover1format'] = $productCover1Format;
                    $cartItemArray['productcover2format'] = $productCover2Format;
                    $cartItemArray['productoutputformat'] = $productOutputFormat;
                    $cartItemArray['productheight'] = $productHeight;
                    $cartItemArray['productwidth'] = $productWidth;
                    $cartItemArray['productdefaultpagecount'] = $productArray['defaultpagecount'];
                    $cartItemArray['projectref'] = $projectRef;
                    $cartItemArray['projectreforig'] = $projectRefOrig;
                    $cartItemArray['projectname'] = $projectName;
                    $cartItemArray['projectstarttime'] = $projectStartTime;
                    $cartItemArray['projectduration'] = $projectDuration;
                    $cartItemArray['pagecount'] = $pageCount;
                    $cartItemArray['producttaxlevel'] = $productArray['taxlevel'];
                    $cartItemArray['productunitcost'] = $productArray['unitcost'];
                    $cartItemArray['productunitweight'] = $productArray['weight'];
                    $cartItemArray['covercode'] = $coverCode;
                    $cartItemArray['papercode'] = $paperCode;
                    $cartItemArray['uploadappversion'] = $uploadAppVersion;
                    $cartItemArray['uploadappplatform'] = $uploadAppPlatform;
                    $cartItemArray['uploadappcputype'] = $uploadAppCPUType;
                    $cartItemArray['uploadapposversion'] = $uploadAppOSVersion;
                    $cartItemArray['uploaddatasize'] = $projectDataSize;
                    $cartItemArray['uploadduration'] = $projectUploadDuration;

                    $cartItemArray['externalassets'] = $productAssetArray['externalassets'];
                    $cartItemArray['pictures'] = $pictureArray['pictures'];
                    $cartItemArray['calendarcustomisations'] = $calendarCustomisationsArray['calendarcustomisations'];

                    $cartItemArray['previewsonline'] = $previewsOnline;
					$cartItemArray['canupload'] = $canUpload;

					$cartItemArray['itemtotalsell'] = $itemTotalSell;
					$cartItemArray['itemproducttotalsell'] = $itemProductTotalSell;
					$cartItemArray['itemsubtotal'] = $itemSubTotal;
					$cartItemArray['qty'] = 1;

					$cartItemArray['productcollectionorigownercode'] = $productCollectionOrigOwnerCode;

					if ($source === TPX_SOURCE_ONLINE)
					{
						// Request the thumbnail URL.
						$requestProjectPreviewThumbnailResult = UtilsObj::requestProjectPreviewThumbnail([$projectRef]);

						if ($requestProjectPreviewThumbnailResult['error'] === '')
						{
							$projectThumbnailData = $requestProjectPreviewThumbnailResult['data'];

							$projectPreviewThumbnailData = $projectThumbnailData[$projectRef];

							if ($projectPreviewThumbnailData['error'] === TPX_ONLINE_ERROR_NONE)
							{
								$cartItemArray['projectpreviewthumbnail'] = $projectPreviewThumbnailData['thumbnail'];
							}
						}
					}
					else
					{
						//get the desktop thumbnail data
						$projectThumbnailResultArray = DatabaseObj::getDesktopProjectThumbnailAvailabilityFromProjectRef($projectRef);

						//if we have found a valid desktop thumbnail then build the URL for it
						if (($projectThumbnailResultArray['error'] === '') && ($projectThumbnailResultArray['available'] === true))
						{
							$cartItemArray['projectpreviewthumbnail'] = UtilsObj::buildDesktopProjectThumbnailWebURL($projectRef);
						}
					}

					// Only add the key if the project has Retro Prints components.
					if (count($retroPrintsComponents) > 0)
					{
						$cartItemArray['retroprints'] = $retroPrintsComponents;
					}

                    $cartArray[] = $cartItemArray;

					if (method_exists('OnlineBasketAPI', 'prepareReorderResult'))
					{
						$tempSessionResultArray = DatabaseObj::insertOrderSessionDataRecord($projectRef, '');

						if ($tempSessionResultArray['result'] == '')
						{
							$ref = $tempSessionResultArray['ref'];

							// the session is not really needed so just delete it again
							DatabaseObj::deleteSession($ref);
						}

						$reorderResultArray = Array();
						$reorderResultArray['apiversion'] = 0;
						$reorderResultArray['languagecode'] = $languageCode;
						$reorderResultArray['ownercode'] = $ownerCode;
						$reorderResultArray['groupcode'] = $groupCode;
						$reorderResultArray['brandcode'] = $licenseKeyArray['webbrandcode'];
						$reorderResultArray['groupdata'] = $groupData;
						$reorderResultArray['userid'] = $userID;
						$reorderResultArray['userlogin'] = '';
						$reorderResultArray['userssotoken'] = '';
						$reorderResultArray['userssoprivatedata'] = '';
						$reorderResultArray['useraccountcode'] = '';
						$reorderResultArray['userstatus'] = 1;
						$reorderResultArray['uuid'] = '';
						$reorderResultArray['origorderid'] = $orderHeaderId;
						$reorderResultArray['origordernumber'] = $orderNumber;
						$reorderResultArray['shoppingcarturl'] = '';
						$reorderResultArray['reorder'] = 1;
						$reorderResultArray['batchref'] = $ref;
						$reorderResultArray['items'] = $cartArray;
						$reorderResultArray['dataavailable'] = $dataAvailable;

						$prepareReorderResult = OnlineBasketAPI::prepareReorderResult($reorderResultArray);

						if ($prepareReorderResult != '')
						{
							$result = 'CUSTOMERROR';
							$resultParam = $prepareReorderResult;
						}
						else
						{
							DatabaseObj::addProjectOrderDataCache($reorderResultArray, array());
						}
					}
                }

                $resultArray['result'] = $result;
                $resultArray['resultparam'] = $resultParam;

                return $resultArray;
            }
        }
    }


	/**
	 * restoreOnlineProject
	 *  - send the command to online to restore a project archive if required
	 */
	static function restoreOnlineProject($pParamArray)
	{
		$resultArray = array(
			'error' => '',
			'errorparam' => '',
			'maintenancemode' => false,
			'restorestatus' => array()
		);

		$postParamArray = array(
			'cmd' => 'RESTOREARCHIVEDPROJECT',
			'data' =>  array('projectref' => $pParamArray['projectref'])
		);

		// send the restore command to online
		$curlPutResultArray = self::curlPutToTaopixOnline($postParamArray);

		if ($curlPutResultArray['error'] == '')
		{
            if ($curlPutResultArray['data']['result'] != TPX_ONLINE_ERROR_MAINTENANCEMODE)
            {
				$resultArray['restorestatus'] = $curlPutResultArray['data']['projectdetails'];
            }
            else
            {
                $resultArray['maintenancemode'] = true;
            }
		}
		else
		{
			$resultArray['error'] = $curlPutResultArray['error'];
		}

        return $resultArray;
	}

	static function validateBasketRefForProjectRef($pBasketRef, $pProjectRef)
	{
		$result = TPX_ONLINE_ERROR_NONE;

		$databaseResult = '';
		$databaseResultParam = '';
		$basketRecordBasketID = 0;
		$basketRecordBasketRef = '';
		$basketRecordUserID = 0;

		// first look for a basket record that matches the project ref
		$dbObj = DatabaseObj::getGlobalDBConnection();
		if ($dbObj)
		{
			if ($stmt = $dbObj->prepare('SELECT `id`, `basketref`, `userid` FROM `ONLINEBASKET` WHERE `projectref` = ?'))
			{
				if ($stmt->bind_param('s', $pProjectRef))
				{
					if ($stmt->execute())
					{
						if ($stmt->bind_result($basketRecordBasketID, $basketRecordBasketRef, $basketRecordUserID))
						{
							if (! $stmt->fetch())
							{
								$databaseResult = 'str_DatabaseError';
								$databaseResultParam = __FUNCTION__ . ' fetch: ' . $dbObj->error;
							}
						}
						else
						{
							$databaseResult = 'str_DatabaseError';
							$databaseResultParam = __FUNCTION__ . ' bind_result: ' . $dbObj->error;
						}
					}
					else
					{
						$databaseResult = 'str_DatabaseError';
						$databaseResultParam = __FUNCTION__ . ' execute: ' . $dbObj->error;
					}
				}
				else
				{
					$databaseResult = 'str_DatabaseError';
					$databaseResultParam = __FUNCTION__ . ' bind: ' . $dbObj->error;
				}

				$stmt->free_result();
				$stmt->close();
				$stmt = null;
			}
			else
			{
				$databaseResult = 'str_DatabaseError';
				$databaseResultParam = __FUNCTION__ . ' prepare: ' . $dbObj->error;
			}

			$dbObj->close();
		}


		// validate the basket record details
		if ($databaseResult == '')
		{
			// make sure we retrieved a basket record
			if ($basketRecordBasketID > 0)
			{
				// if the basket record has a user id it means the user has at some point been signed-in during the lifetime of this project
				// this means the user can only perform actions on this project while signed-in so we need to make sure they are still signed-in
				// if the basket record does not have a user id it means the project still belongs to a guest so we can relax the checks we perform
				if ($basketRecordUserID > 0)
				{
					// the project in the basket record belongs to a user

					// is the input basket ref valid and does it match the one from the basket record we have retrieved?
					if (($pBasketRef == '') || ($pBasketRef == TPX_ONLINE_BASKETAPI_DEFAULTBASKETREF) || ($pBasketRef <> $basketRecordBasketRef))
					{
						// the basket ref is not valid

						// currently we don't care why so just report that the session has expired
						$result = TPX_ONLINE_ERROR_HIGHLEVELSESSIONEXPIRED;
					}
					else
					{
						// the basket ref appears to be valid

						// as the user was signed-in there should also be an active session which matches the basket ref
						// we need to make sure that this session is still valid
						$highLevelBasketUserSesionResultArray = AuthenticateObj::checkHighLevelBasketSessionActive($pBasketRef);

						if (($highLevelBasketUserSesionResultArray['result'] != '') || ($highLevelBasketUserSesionResultArray['sessionactive'] != 1)
							|| ($highLevelBasketUserSesionResultArray['userid'] != $basketRecordUserID))
						{
							// the session is not valid

							// currently we don't care why so just report that the session has expired
							$result = TPX_ONLINE_ERROR_HIGHLEVELSESSIONEXPIRED;
						}
						else
						{
							// the session is still valid

							// the signed-in user action can continue and there is nothing else to validate
						}
					}
				}
				else
				{
					// the project in the basket belongs to a guest

					// is the basket ref valid?
					if (($pBasketRef == '') || ($pBasketRef == TPX_ONLINE_BASKETAPI_DEFAULTBASKETREF))
					{
						// the basket ref is not valid anymore

						// we should not allow any other action to continue as although the user is a guest they are possibly not the same guest
						$result = TPX_ONLINE_ERROR_HIGHLEVELBASKETEXPIRED;
					}
					else
					{
						// the basket ref is still valid

						// the guest action can continue and there is nothing else to validate
					}
				}
			}
			else
			{
				// there appears to be no basket record for this project

				// this should never happen so return an internal error
				$result = TPX_ONLINE_ERROR_HIGHLEVELINTERNALERROR;
			}
		}
		else
		{
			// a database error occurred

			// this should never happen so return an internal error
			$result = TPX_ONLINE_ERROR_HIGHLEVELINTERNALERROR;
		}

        return $result;
	}

	static function checkIfBasketIsAssignedToAUser($pBasketRef, $pUserID)
	{
		$resultArray = array();
		$result = '';
		$resultParam = '';

		$basketRefAlreadyAssigned = true;

		if (($pBasketRef != '') && ($pBasketRef != TPX_ONLINE_BASKETAPI_DEFAULTBASKETREF))
		{
			$dbObj = DatabaseObj::getGlobalDBConnection();

			if ($dbObj)
			{
				if ($stmt = $dbObj->prepare('SELECT `userid` FROM `ONLINEBASKET` WHERE `basketref` = ? AND ((`userid` > 0) AND (`userid` != ?))'))
				{
					if ($stmt->bind_param('si', $pBasketRef, $pUserID))
					{
						if ($stmt->execute())
						{
							$stmt->store_result();

							 if ($stmt->num_rows == 0)
							 {
								$basketRefAlreadyAssigned = false;
							 }
						}
						else
						{
							$result = 'str_DatabaseError';
							$resultParam = 'retrieve checkIfBasketIsAssignedToAUser execute ' . $dbObj->error;
						}

					}
					else
					{
						// could not bind parameters
						$result = 'str_DatabaseError';
						$resultParam = 'retrieve checkIfBasketIsAssignedToAUser bind ' . $dbObj->error;
					}

					$stmt->free_result();
					$stmt->close();
					$stmt = null;
				}
				else
				{
					// could not prepare statement
					$result = 'str_DatabaseError';
					$resultParam = 'retrieve checkIfBasketIsAssignedToAUser prepare ' . $dbObj->error;
				}

				$dbObj->close();
			}
			else
			{
				$result = 'str_DatabaseError';
				$resultParam = 'checkIfBasketIsAssignedToAUser connect ' . $dbObj->error;
			}
		}

		$resultArray['result'] = $result;
		$resultArray['resultparam'] = $resultParam;
		$resultArray['basketrefalreadyassigned'] = $basketRefAlreadyAssigned;

		return $resultArray;
	}

	/**
	 * Return the editor which will be used to open the project and the satus of the switch button.
	 *
	 * @param int $pCurrentOnlineEditorMode Current value of teh online editor mode (-1 by default).
	 * @param string $pGroupCode Active group code.
	 * @param array $pLicenseKeyData Licence key data if it has been requested previously.
	 * @param array $pBrandingData Brading data if it has been requested previously.
	 * @param int $pEnableSwitchingEditor Current value of the switch button display status.
	 * @return array Value of the editor to be used and switch button display status.
	 */
	static function getOnlineEditorSettings(&$pCurrentOnlineEditorMode, $pGroupCode, $pLicenseKeyData,
		$pBrandingData, &$pEnableSwitchingEditor)
	{
		$brandingArray = $pBrandingData;
		$licenseKeyData = $pLicenseKeyData;

		// check if the mode has not been fixed by OnlineBasketAPI script and it's a correct mode.
		if ($pCurrentOnlineEditorMode == -1)
		{
			// Detect if the licence key is loaded correctly.
			if ((empty($pLicenseKeyData) || ($pLicenseKeyData['groupcode'] != $pGroupCode)))
			{
				$licenseKeyData = DatabaseObj::getLicenseKeyFromCode($pGroupCode);
			}

			// Check branding data if licence key respect branding data.
			if ($licenseKeyData['usedefaultonlineeditormode'])
			{
				if (empty($pBrandingData))
				{
					$brandingArray = DatabaseObj::getBrandingFromCode($licenseKeyData['webbrandcode']);
				}

				$pCurrentOnlineEditorMode = $brandingArray['onlineeditormode'];
			}
			else
			{
				$pCurrentOnlineEditorMode = $licenseKeyData['onlineeditormode'];
			}
		}


		// check if the mode has not been fixed by OnlineBasketAPI script and it's a correct mode.
		if ($pEnableSwitchingEditor == -1)
		{
			// Detect if the licence key is loaded correctly.
			if ((empty($pLicenseKeyData) || ($pLicenseKeyData['groupcode'] != $pGroupCode)))
			{
				$licenseKeyData = DatabaseObj::getLicenseKeyFromCode($pGroupCode);
			}

			// Check branding data if licence key respect branding data.
			if ($licenseKeyData['usedefaultonlineeditormode'])
			{
				if (empty($pBrandingData))
				{
					$brandingArray = DatabaseObj::getBrandingFromCode($licenseKeyData['webbrandcode']);
				}

				$pEnableSwitchingEditor = $brandingArray['enableswitchingeditor'];
			}
			else
			{
				$pEnableSwitchingEditor = $licenseKeyData['enableswitchingeditor'];
			}
		}
	}

	/**
	 * Queries the DB to find if a certain order item still has it's order files. These files could be cleaned up by the
	 * order data deletion task.
	 *
	 * @param int $pOrderItemID The ID of the ORDERITEM record you wish to query.
	 * @return array dataavaialbe key will be set to true or false.
	 */
	static function checkDataAvailable($pParamArray)
	{
		$resultArray = array();
		$resultArray['orders'] = array();
		$resultArray['result'] = TPX_ONLINE_ERROR_NONE;

        $orderItemIds = implode(',', $pParamArray['orderitemids']);

        if (UtilsObj::checkNumberCSV($orderItemIds))
        {
            $resultArray['result'] = TPX_ONLINE_ERROR_INVALIDPARAMETER;
            return $resultArray;
        }

		$orderItemDataArray = DatabaseObj::getOrderItemsByIds($pParamArray['orderitemids']);

		$result = $orderItemDataArray['result'];

		if ($result == "")
		{
			$foundOrders = array();

			foreach ($orderItemDataArray['orders'] as $order)
			{
				$resultArray['orders'][] = array( 	'orderitemid' => $order['orderitemid'],
													'dataavailable' => $order['dataavailable'],
													'result' => TPX_ONLINE_ERROR_NONE);

				$foundOrders[] = $order['orderitemid'];
			}

			$notFoundOrders = array_diff($pParamArray['orderitemids'], $foundOrders);

			$notFoundOrdersCount = count($notFoundOrders);

			for ($i = 0; $i < $notFoundOrdersCount; $i++)
			{
				$resultArray['orders'][] = array( 	'orderitemid' => $notFoundOrders[$i],
													'dataavailable' => false,
													'result' => TPX_ONLINE_ERROR_ORDERDOESNOTEXIST);
			}

		}
		else
		{
			$resultArray['result'] = TPX_ONLINE_ERROR_LOWLEVELINTERNALERROR;
		}

		return $resultArray;
    }

    static function keepOnlineProject($projectRef, $database)
	{
		$resultArray = [
			'error' => '',
			'errorparam' => '',
		];

		$query = 'UPDATE `ONLINEBASKET` SET `dateofpurge` = "0000-00-00 00:00:00" WHERE `projectref` = ?';

		try {
			if (!$stmt = $database->prepare($query)) {
				throw new Exception(__METHOD__ . ' prepare: ' . $database->error, 'str_DatabaseError');
			}

			if (!$stmt->bind_param('s', $projectRef)) {
				throw new Exception(__METHOD__ . ' bind param: ' . $database->error, 'str_DatabaseError');
			}

			if (! $stmt->execute()) {
				throw new Exception(__METHOD__ . ' execute: ' . $database->error, 'str_DatabaseError');
			}
		}
		catch (Exception $ex)
		{
			$resultArray['error'] = $ex->getCode();
			$resultArray['errorparam'] = $ex->getMessage();
		}

		return $resultArray;
	}

	/**
	 * Gets product component data from DB
	 *
	 * @param array pProductCodeArray contains product codes we wish to build tree for
	 * @param string pCompanyCode company code
	 * @param string pGroupCode license key code
	 *
	 * @return array array of product component data to build tree with
	 */
	static function getProductTreeData($pProductCodeArray, $pCompanyCode, $pGroupCode): array
	{
		global $gSession;
		global $ac_config;

        // return an array containing the components currently attached to the product
        $result = '';
        $resultParam = '';
        $resultArray = Array();
        $id = 0;

		$parentid = 0;
		$companycode = '';
		$productcode = '';
		$groupcode = '';
		$parentPath = '';
		$sectionCode = '';
		$sortorder = 0;
		$isDefault = 0;
		$priceid = 0;
		$inheritParentQty = 0;
		$componentCompanyCode = '';
		$componentCode = '';
		$localcode = '';
		$name = '';
		$info = '';
		$categoryCode = '';
		$pricingmodel = 0;
		$price = '';
		$islist = 0;
		$decimalplaces = 0;
		$categoryActive = 0;
		$componentActive = 0;
		$keywordGroupHeaderId = 0;
		$minimumPageCount = 0;
		$maximumPageCount = 0;
		$moreInfoLinkUrl = '';
		$moreInfoLinkText = '';
		$requiresPageCount = 0;
		$taxCode = '';
		$taxRate = '';
		$quantityIsDropdown = 0;
		$displayStage = 0;
		$categoryName = '';
		$categoryPrompt = '';
		$parentSectionName = '';
		$parentSectionPrompt = '';
		$parentSectionDisplayStage = '';
		$isDefaultPrice = 0;

		$paramArray = [];
		$typesArray = [];

		if (count($pProductCodeArray) > 0)
		{
			foreach($pProductCodeArray as $productCode)
			{
				$linkedCode = '';
				$thisProductLinkingArray = DatabaseObj::getApplicableProductLinkCode($productCode);

				if ($thisProductLinkingArray['error'] == '')
				{
					$linkedCode = ($thisProductLinkingArray['linkedcode'] != '') ? $thisProductLinkingArray['linkedcode'] : $productCode;
				}

				$linkedProductCodeArray[$productCode] = ['productcode' => $productCode, 'linkedcode' => $linkedCode ];

				$targetCode = ($linkedCode == '') ? $productCode : $linkedCode;

				if (!isset($treeProductCodeArray[$targetCode]))
				{
					$treeProductCodeArray[$targetCode] = $targetCode;
				}
			}

			$productCodeList = "'" . implode("','", $treeProductCodeArray) . "'";

			$paramArray = [ $pCompanyCode, $pGroupCode, $pCompanyCode, $pGroupCode ];
			$typesArray = ['s','s','s','s'];

			$dbObj = DatabaseObj::getGlobalDBConnection();

			$sql =  '	SELECT `pl`.`id`, `pl`.`parentid`, `pl`.`companycode`, `pl`.`productcode`, `pl`.`groupcode`, `pl`.`parentpath`,
						`pl`.`sectioncode`, `pl`.`sortorder`, `pl`.`isdefault`, `pl`.`priceid`, `pl`.`inheritparentqty`, `cmp`.`companycode`, `cmp`.`code`, `cmp`.`localcode`,
						`cmp`.`name`, `cmp`.`info`, `cmp`.`categorycode`, `cc`.`pricingmodel`, `pr`.`price`, `cc`.`islist`, `cc`.`componentpricingdecimalplaces`, `cc`.`active`, `cmp`.`active`,
						`cmp`.`keywordgroupheaderid`, `cmp`.`minimumpagecount`, `cmp`.`maximumpagecount`, `cmp`.`moreinfolinkurl`, `cmp`.`moreinfolinktext`, `cc`.`requirespagecount`,
						ifnull(`tr`.`code`,"") as taxcode, ifnull(`tr`.`rate`,0) as taxrate, `pr`.`quantityisdropdown`, `cc`.`name` as `categoryname`, `cc`.`prompt` as `categoryprompt`,
						`pcc`.`name` as `parentsectionname`, `pcc`.`prompt` as `parentsectionprompt`, `cc`.`displaystage`, `pcc`.`displaystage` as `parentsectiondisplaystage`,
						(`pl`.`priceid` < 0) AS isdefaultprice
						FROM `PRICELINK` as pl
						INNER JOIN `PRICELINK` `p2` ON `p2`.`componentcode` = `pl`.`componentcode` AND
							((`pl`.`priceid` = -1 AND `p2`.`productcode` = "") OR (`pl`.`priceid` > 0 AND `pl`.`id` = `p2`.`id`)) AND
							(`p2`.`companycode` = ? OR `p2`.`companycode` = "") AND
							(`p2`.`groupcode` = ? OR `p2`.`groupcode` = "") AND
							(`p2`.`active` = 1)
						LEFT JOIN `COMPONENTS` cmp ON `cmp`.`code` = `pl`.`componentcode`
						LEFT JOIN `PRICES` pr ON `pr`.`id` = `p2`.`priceid`
						LEFT JOIN `TAXRATES` tr ON `tr`.`code` = `pr`.`taxcode`
						LEFT JOIN `COMPONENTCATEGORIES` cc ON `cc`.`code` = `cmp`.`categorycode`
						LEFT JOIN `COMPONENTCATEGORIES` pcc ON `pl`.`sectioncode` = `pcc`.`code`
						WHERE (( `pl`.`productcode` IN (' . $productCodeList . ') ) )
						AND (`pl`.`componentcode` <> "")
						AND ((`pl`.`companycode` = ?) OR (`pl`.`companycode` = ""))
						AND ((`pl`.`groupcode` = ?) OR (`pl`.`groupcode` = ""))
						AND (`cmp`.`active` = 1)
						AND (`pl`.`active` = 1)
						AND (`cc`.`active` = 1)
						AND (`pr`.`active` = 1)
						AND (`pl`.`parentpath` NOT LIKE \'$ORDERFOOTER%\')
						ORDER BY `productcode`, `sortorder`, `parentpath`
					';

			if ($dbObj)
			{
				// determine the product and its components
				if ($stmt = $dbObj->prepare($sql))
				{
					$bindOK = DatabaseObj::bindParams($stmt, $typesArray, $paramArray);
					if ($bindOK)
					{
						if ($stmt->bind_result($id, $parentid, $companycode, $productcode, $groupcode, $parentPath,
													$sectionCode, $sortorder, $isDefault, $priceid, $inheritParentQty, $componentCompanyCode, $componentCode, $localcode,
													$name, $info, $categoryCode, $pricingmodel, $price, $islist, $decimalplaces, $categoryActive, $componentActive,
													$keywordGroupHeaderId, $minimumPageCount, $maximumPageCount, $moreInfoLinkUrl, $moreInfoLinkText, $requiresPageCount,
													$taxCode, $taxRate, $quantityIsDropdown, $categoryName, $categoryPrompt, $parentSectionName,
													$parentSectionPrompt, $displayStage, $parentSectionDisplayStage, $isDefaultPrice
												))
						{
							if ($stmt->execute())
							{
								while ($stmt->fetch())
								{
									$doInsert = true;

									$itemArray['id'] = $id;
									$itemArray['parentid'] = $parentid;
									$itemArray['priceid'] = $priceid;
									$itemArray['companycode'] = $companycode;
									$itemArray['productcode'] = $productcode;
									$itemArray['groupcode'] = $groupcode;
									$itemArray['parentpath'] = $parentPath;
									$itemArray['sectioncode'] = $sectionCode;
									$itemArray['sortorder'] = $sortorder;
									$itemArray['isdefault'] = $isDefault;
									$itemArray['companycode'] = $componentCompanyCode;
									$itemArray['code'] = $componentCode;
									$itemArray['localcode'] = $localcode;
									$itemArray['name'] = $name;
									$itemArray['info'] = $info;
									$itemArray['pricingmodel'] = $pricingmodel;
									$itemArray['price'] = $price;
									$itemArray['categorycode'] = $categoryCode;
									$itemArray['islist'] = $islist;
									$itemArray['decimalplaces'] = $decimalplaces;
									$itemArray['categoryactive'] = $categoryActive;
									$itemArray['componentactive'] = $componentActive;
									$itemArray['inheritparentqty'] = $inheritParentQty;
									$itemArray['pathdepth'] = substr_count($parentPath, '\\');
									$itemArray['keywordgroupheaderid'] = $keywordGroupHeaderId;
									$itemArray['keywords'] = [];
									$itemArray['minimumpagecount'] = $minimumPageCount;
									$itemArray['maximumpagecount'] = $maximumPageCount;
									$itemArray['moreinfolinkurl'] = $moreInfoLinkUrl;
									$itemArray['moreinfolinktext'] = $moreInfoLinkText;
									$itemArray['requirespagecount'] = $requiresPageCount;
									$itemArray['taxcode'] = $taxCode;
									$itemArray['taxrate'] = $taxRate;
									$itemArray['quantityisdropdown'] = $quantityIsDropdown;
									$itemArray['displaystage'] = $displayStage;
									$itemArray['categoryname'] = $categoryName;
									$itemArray['categoryprompt'] = $categoryPrompt;
									$itemArray['parentsectionname'] = $parentSectionName;
									$itemArray['parentsectionprompt'] = $parentSectionPrompt;
									$itemArray['parentsectiondisplaystage'] = $parentSectionDisplayStage;
									$itemArray['isdefaultprice'] = $isDefaultPrice;

									foreach ($linkedProductCodeArray as $linkedProduct)
									{
										if (strtoupper($linkedProduct['linkedcode']) == strtoupper($productcode))
										{
											if (isset($resultArray[$linkedProduct['productcode']]))
											{
												foreach ($resultArray[$linkedProduct['productcode']] as $componentIndex => &$component)
												{
													//check if theres already a price row for this component
													if (($component['parentpath'] == $itemArray['parentpath']) && ($component['code'] == $itemArray['code']))
													{
														//if the current item IS NOT default price and the found row IS default price then remove the found row and insert current item instead
														if ((!$isDefaultPrice) && ($component['isdefaultprice']))
														{
															$doInsert = true;
															unset($resultArray[$linkedProduct['productcode']][$componentIndex]);
															$resultArray[$linkedProduct['productcode']] = array_merge($resultArray[$linkedProduct['productcode']]);
														}
														//if the current item IS default price and the found row IS NOT default price then keep the found row and don't insert this item
														else if (($isDefaultPrice) && (!$component['isdefaultprice']))
														{
															$doInsert = false;
														}
													}
												}
											}
											if ($doInsert)
											{
												$resultArray[$linkedProduct['productcode']][] = $itemArray;
											}
										}
									}
								}

								if (count($resultArray) > 0)
								{
									$webURL = UtilsObj::getWebURl();

									foreach($resultArray as $productkey => $product)
									{
										foreach($product as $key => $item)
										{
											$resultArray[$productkey][$key]['previewimage'] = UtilsObj::getAssetRequest($item['code'], 'components', $webURL);

											if ($item['keywordgroupheaderid'] > 0)
											{
												$resultArray[$productkey][$key]['keywords'] = array_map(function($keyword) use ($webURL) {
													$keyword['flags'] = str_replace('[WEBROOT]/', $webURL, $keyword['flags']);
													return $keyword;
												}, MetaDataObj::getKeywordList('COMPONENT', '', '', $item['keywordgroupheaderid']));
											}
										}
									}
								}
							}
							else
							{
								// could not execute statement
								$result = 'str_DatabaseError';
								$resultParam = __FUNCTION__.'1 execute ' . $dbObj->error;
							}
						}
						else
						{
							// could not bind result
							$result = 'str_DatabaseError';
							$resultParam = __FUNCTION__.'1 bind result ' . $dbObj->error;
						}
					}
					else
					{
						// could not bind parameters
						$result = 'str_DatabaseError';
						$resultParam = __FUNCTION__.'1 bind params ' . $dbObj->error;
					}
					$stmt->free_result();
					$stmt->close();
					$stmt = null;
				}
				else
				{
					// could not prepare statement
					$result = 'str_DatabaseError';
					$resultParam = __FUNCTION__.'1 prepare ' . $dbObj->error;
				}
				$stmt = null;

				$dbObj->close();
			}
			else
			{
				// could not open database connection
				$result = 'str_DatabaseError';
				$resultParam = 'getComponentPriceRowByCode connect ' . $dbObj->error;
			}
		}

        return $resultArray;
	}

	/**
	 * Convert associative array to indexed array to shrink data size
	 *
	 * @param array pArray associative array to convert
	 * @param bool pTop optional if true then we are at the top level
	 *
	 * @return array indexed array to return
	 */
	static function reMapKeys($pArray, $pTop = false)
	{
		$passArray = $pArray;

		if ((!$pTop) && (is_array($pArray)))
		{
			$passArray = array_values($pArray);
		}

		if (is_array($passArray))
		{
			return array_map('self::remapKeys', $passArray);
		}
		else
		{
			return $passArray;
		}
	}

	/**
	 * Build Top level product node for for product tree
	 *
	 * @param string pProductCode item data
	 * @return array data for the node
	 */
	static function buildProductNode($pProductCode)
	{
		return self::buildNode([
			"categoryname" => 'PRODUCT',
			"categoryprompt" => 'PRODUCT',
			"sectioncode" => 'PRODUCT',
			"parentpath" => "",
			"name" => 'PRODUCT',
			"islist" => 0,
			"previewimage" => str_replace('[WEBROOT]', UtilsObj::getBrandedWebUrl(), UtilsObj::getAssetRequest($pProductCode, 'products')),
			"categoryprompt" => '',
			"displaystage" => '',
			"parentsectionname" => '',
			"parentsectionprompt" => '',
			"requirespagecount" => 0
		]);
	}

	/**
	 * Build node for product tree
	 *
	 * @param array pTheItem item data
	 * @param string pText optional text node of the item
	 * @param bool pIsChild optional if true build child node otherwise its a parent node
	 * @param array pOverrides optional to overide data in pTheItem
	 *
	 * @return array data for the node
	 */
	static function buildNode($pTheItem, $pText = '', $pIsChild = false, $pOverrides = [])
	{
		if (count($pOverrides) > 0)
		{
			foreach($pOverrides as $key=>$value)
			{
				if (isset($pTheItem[$key])) {
					$pTheItem[$key] = $value;
				}
			}
		}

		return ($pIsChild) ? self::buildChildNode($pText, $pTheItem) : self::buildParentNode($pTheItem);
	}

	/**
	 * Build parent node for product tree
	 *
	 * @param array pTheItem item data
	 *
	 * @return array data for the node
	 */
	static function buildParentNode($pTheItem)
	{
		if ($pTheItem['sectioncode'] == 'LINEFOOTER')
		{
			$pTheItem['name'] = 'LINEFOOTER';
			$pTheItem['parentsectionname'] = 'LINEFOOTER';
			$pTheItem['parentsectionprompt'] = 'LINEFOOTER';
			$pTheItem['previewimage'] = '';
			// The linefooter section is available all the time,
			// but the sub-components control at which stage they appear.
			$pTheItem['displaystage'] = TPX_COMPONENT_DISPLAY_STAGE_ALL;
		}

		return [
			"text" => $pTheItem['parentsectionname'],
			"sectioncode" => $pTheItem['sectioncode'],
			"componentcode" => "",
			"parentpath" => UtilsObj::encodeString($pTheItem['parentpath'],true),
			"sectionname" => UtilsObj::encodeString($pTheItem['name'], true),
			"islist" => $pTheItem['islist'],
			"allowinherit" => 0,
			"previewimage" => $pTheItem['previewimage'],
			"children" => [],
			"keywords" => [],
			"categoryprompt" => $pTheItem['parentsectionprompt'],
			"displaystage" => $pTheItem['displaystage'],
			"requirespagecount" => $pTheItem['requirespagecount']
		];
	}

	/**
	 * Build child node for product tree
	 *
	 * @param string pText text node of the item
	 * @param array pTheItem item data
	 *
	 * @return array data for the node
	 */
	static function buildChildNode($pText, $pTheItem)
	{
		return [
			"text" => $pText,
			"info" => $pTheItem['info'],
			"sectioncode" => $pTheItem['sectioncode'],
			"componentcode" => $pTheItem['localcode'],
			"code" => $pTheItem['code'],
			"islist" => $pTheItem['islist'],
			"isdefault" => $pTheItem['isdefault'],
			"pricingmodel" => $pTheItem['pricingmodel'],
			"price" => $pTheItem['price'],
			"taxrate" => $pTheItem['taxrate'],
			"taxcode" => $pTheItem['taxcode'],
			"parentpath" => UtilsObj::encodeString($pTheItem['parentpath'], true),
			"inheritparentqty" => $pTheItem['inheritparentqty'],
			"sortorder" => $pTheItem['sortorder'],
			"quantityisdropdown" => $pTheItem['quantityisdropdown'],
			"previewimage" => $pTheItem['previewimage'],
			"children" => [],
			"keywords" => isset($pTheItem['keywords']) ? $pTheItem['keywords'] : [],
			"categoryprompt" => $pTheItem['categoryprompt'],
			"displaystage" => $pTheItem['displaystage'],
			"requirespagecount" => $pTheItem['requirespagecount'],
			"minpagecount" => $pTheItem['minimumpagecount'],
			"maxpagecount" => $pTheItem['maximumpagecount']
		];
	}

	/**
	 * Build product tree structure
	 *
	 * @param array pDataArray byref product component tree data
	 * @param string pStartIndex
	 * @param string pDepth depth in the tree
	 * @param string pParentPath path to the parent component
	 * @param string pNextNodeID by ref
	 * @param array pProcessedItemsArray by ref array of already processed items
	 * @param string pSectionCode current section code
	 * @param array treeArray by ref
	 *
	 * @return array product tree
	 */
	static function buildProductTree(&$pDataArray, $pStartIndex, $pDepth, $pParentPath, &$pNextNodeID, &$pProcessedItemsArray, $pSectionCode, &$treeArray)
    {
		global $gSession;

    	$hasSection = false;
    	$componentChildrenOpen = false;
    	$componentHadChildrenOpen = false;
    	$itemCount = count($pDataArray);
		$previousParentPath = '';
		$previousSectionCode = $pSectionCode;

    	for ($i = $pStartIndex; $i < $itemCount; $i ++)
    	{
    		$includeDefaultClass = false;
    		$theItem = &$pDataArray[$i];

    		if (! in_array($theItem['parentid'], $pProcessedItemsArray))
    		{
	    		$parentPathDepth = substr_count($theItem['parentpath'], '.');

	    		if ($parentPathDepth == $pDepth)
		    	{
		    		$theItem['depth'] = $pDepth; // set the depth incase we need it later

		    		if ($previousParentPath != $theItem['parentpath'])
		    		{
		    			// new section
			 			if ($previousSectionCode != $theItem['sectioncode'])
			 			{
							$pNextNodeID++;

							//$index = ($theItem['parentpath'] == '') ? 'PRODUCT' : $theItem['parentpath'];
							$index = $theItem['parentpath'];

							if ($theItem['parentpath'] == $theItem['sectioncode'])
							{
								if (!isset($treeArray[$index]))
								{
									$treeArray[$index] =  self::buildNode($theItem);
								}
							}
							else
							{
								//Need to build up the path
								$parentPathArray = [];
								$pathArrayLength = 0;

								if ($theItem['parentpath'] != '')
								{
									$parentPathArray = explode('.', $theItem['parentpath']);
									$pathArrayLength = count($parentPathArray);
								}
								$parentIndex = '';
								$insertParentPoint = &$treeArray;
								$count = 0;

								foreach($parentPathArray as $pathSection)
								{
									$count++;

									if ($parentIndex != '')
									{
										$parentIndex .= '.';
									}

									$parentIndex .= $pathSection;

									$insertParentPoint = &$insertParentPoint[$parentIndex];

									if (!isset($insertParentPoint))
									{
										if ($count == 1)
										{
											$insertParentPoint = self::buildNode($theItem, $pathSection,
																					false, [
																							'sectioncode' => $pathSection,
																							'parentpath' => $pathSection,
																							'children' => [],
																							'keywords' => []
																						]);
										}
										else
										{
											//parent path one level higher than index
											//Hit this point when section nested under another section

											$newParentPath = substr($parentIndex, 0, strrpos($parentIndex,'.'));
											$insertParentPoint = self::buildNode($theItem, $theItem['parentsectionname'],
																					true, [
																							'localcode' => $theItem['sectioncode'],
																							'code' => '',
																							'isdefault' => 0,
																							'pricingmodel' => 0,
																							'price' => '',
																							'taxrate' => '',
																							'taxcode' => '',
																							'parentpath' => $newParentPath,
																							'inheritparentqty' => 0,
																							'sortorder' => 0,
																							'children' => [],
																							'keywords' => [],
																							'categoryprompt' => $theItem['parentsectionprompt'],
																							'displaystage' => $theItem['parentsectiondisplaystage'],
																							'requirespagecount' => $theItem['requirespagecount'],
																							'minimumpagecount' => $theItem['minimumpagecount'],
																							'maximumpagecount' => $theItem['maximumpagecount']
																						]);
										}
									}

									if ($count < $pathArrayLength)
									{
										$insertParentPoint = &$insertParentPoint['children'];
									}
								}

								if (!isset($insertParentPoint))
								{
									$insertParentPoint = self::buildNode($theItem, $theItem['categoryname'],
																					true, [
																							'localcode' => '',
																							'code' => '',
																							'isdefault' => 0,
																							'pricingmodel' => 0,
																							'price' => '',
																							'taxrate' => '',
																							'taxcode' => '',
																							'inheritparentqty' => 0,
																							'sortorder' => 0,
																							'children' => [],
																							'keywords' => []
																						]);
								}
							}

							$hasSection = true;
							$componentHadChildrenOpen = false;
			 			}

			 			$pProcessedItemsArray[] = $theItem['parentid'];
		    		}

		    		// process component
		    		if ($theItem['parentid'] > 0)
		    		{
		    			$pNextNodeID++;

		    			//check to see if the item is a list or checkbox component
		    			if ($theItem['islist'] == '1')
			    		{
			    			$iconCls = 'silk-list';

			    			if ($theItem['isdefault'] == '1')
			    			{
			    				$includeDefaultClass = true;
			    				$text = UtilsObj::encodeString($theItem['name'], true);
			    			}
			    			else
			    			{
			    				$text = UtilsObj::encodeString($theItem['name'], true);
			    			}
			    		}
			    		else
			    		{
			    			if ($theItem['isdefault'] == '1')
			    			{
			    				$iconCls = 'checkboxComponentChecked';
			    			}
			    			else
			    			{
			    				$iconCls = 'checkboxComponentUnchecked';
			    			}

			    			$text = UtilsObj::encodeString($theItem['name'], true);
			    		}

						$insertIndex = $theItem['code'];

						switch ($theItem['depth'])
						{
							case 0:
								if ($theItem['parentpath'] == '' && $theItem['sectioncode'] == 'PRODUCT')
								{
									$insertPoint = &$treeArray;
								}
								else
								{
									$parentIndex = $theItem['parentpath'] == '' ? $theItem['sectioncode'] : $theItem['parentpath'];
									if (!isset($treeArray[$parentIndex]))
									{
										$treeArray[$parentIndex] = self::buildNode($theItem);
									}
									$insertPoint = &$treeArray[$parentIndex]['children'];
								}
								break;

							//depth 1 or more
							default:
								$parentPathArray = explode('.', $theItem['parentpath']);
								$parentIndex = '';
								$insertPoint = &$treeArray;

								$pathArrayLength = count($parentPathArray);
								$count = 0;

								foreach($parentPathArray as $pathSection)
								{
									$count++;

									if ($parentIndex != '')
									{
										$parentIndex .= '.';
									}

									$parentIndex .= $pathSection;

									$insertPoint = &$insertPoint[$parentIndex];

									if (!isset($insertPoint))
									{
										if ($count == 1)
										{
											$insertPoint = self::buildNode($theItem, $pathSection,
																					false, [
																							'sectioncode' => $pathSection,
																							'parentpath' => $pathSection
																						]);
										}
										else
										{
						 					//parent path one level higher than index
											//Hit this point when section nested under itself

											$newParentPath = substr($parentIndex, 0, strrpos($parentIndex,'.'));
											$insertPoint = self::buildNode($theItem, $theItem['parentsectionname'],
																					true, [
																							'localcode' => '',
																							'code' => '',
																							'isdefault' => 0,
																							'pricingmodel' => 0,
																							'price' => '',
																							'taxrate' => '',
																							'taxcode' => '',
																							'parentpath' => $newParentPath,
																							'inheritparentqty' => 0,
																							'sortorder' => 0,
																							'children' => [],
																							'keywords' => [],
																							'categoryprompt' => $theItem['parentsectionprompt'],
																							'displaystage' => $theItem['parentsectiondisplaystage'],
																							'requirespagecount' => $theItem['requirespagecount']
																						]);
										}
									}

									if ($count < $pathArrayLength)
									{
										$insertPoint = &$insertPoint['children'];
									}
								}

								$insertPoint = &$insertPoint['children'];
								$insertIndex = $theItem['parentpath'] . '.' . $theItem['localcode'];
							break;
						}

						if (!isset($insertPoint[$insertIndex]))
						{
							$insertPoint[$insertIndex] = self::buildNode($theItem, $text, true);
						}

			 			// is the component a list
			 			if ($theItem['islist'] == '1')
			 			{
			 				// it is a list so we can have sub-components
			 				$componentChildrenOpen = true;

			 				// this is a new open node so we did not previously have an open node
			 				$componentHadChildrenOpen = false;
			 			}
			 			else
			 			{
			 				// it is a checkbox so we cannot have sub-components
			 				$componentChildrenOpen = false;

			 				// do not allow the node we have closed to be re-opened
			 				$componentHadChildrenOpen = false;
			 			}
				    }

		    		$pProcessedItemsArray[] = $theItem['parentid'];

		    		$previousParentPath = $theItem['parentpath'];
		    		$previousSectionCode = $theItem['sectioncode'];
		    	}
		    	else
		    	{
		    		$theItem['depth'] = $parentPathDepth; // set the depth incase we need it later

		    		// are we now deeper in the tree
		    		if ($parentPathDepth == $pDepth + 1)
		    		{
		    			$previousParentPathLen = strlen($previousParentPath);
		    			$startOfNewItemPath = substr($theItem['parentpath'], 0, $previousParentPathLen);

		    			//check to see if the start of the new path matches the previous path. if it does then this is a sub component
		    			if ($startOfNewItemPath == $previousParentPath)
						{
							// do we have an open child node
							if (! $componentChildrenOpen)
							{
								// we don't have an open child node but did we previously have one open that has been closed
								if ($componentHadChildrenOpen)
								{
									// we had one open that was closed so re-open it
									$componentHadChildrenOpen = false;
									$componentChildrenOpen = true;
								}
							}

							// its a sub component
							self::buildProductTree($pDataArray, $i, $parentPathDepth, $theItem['parentpath'], $pNextNodeID, $pProcessedItemsArray, $previousSectionCode, $treeArray);

							$pProcessedItemsArray[] = $theItem['parentid'];

							if ($componentChildrenOpen)
							{
								$componentChildrenOpen = false;
								$componentHadChildrenOpen = true;
							}
						}
		    		}
		    		elseif ($parentPathDepth < $pDepth)
		    		{
		    			// we are not deeper so just exit the loop
		    			break;
		    		}
		    	}
    		}
     	}

    	if ($componentChildrenOpen)
		{
			$componentChildrenOpen = false;
			$componentHadChildrenOpen = true;
		}

		return $treeArray;
    }

	/**
	 * Build product tree data
	 *
	 * @param array $pDataArray product component tree data
	 * @return array Array product tree
	 */
	static function getProductTree($pDataArray): array
	{
	 	$priceLinkParentIdList = Array();
	 	$recordArray = Array();
	 	$tree = [];
		$treeChildren =[];
	 	$lastParentID = -1;
		$treeData = [];

		if (count($pDataArray) > 0)
	 	{
		 	// pre processing to sort the pricelinks
		 	$sortedComponentList = Array();

		 	for ($i = 0; $i < count($pDataArray); $i++)
		 	{
		 		$priceLinkParentIdList = Array();

		 		$item = $pDataArray[$i];
		 		$sortedComponentList[] = $item;

		 		$lastParentID = $item['parentid'];

				if (! in_array($item['parentid'], $priceLinkParentIdList))
		 		{
		 			$priceLinkParentIdList[] = $item['parentid'];
		 		}

				// get path depth, to check if the item is a sub component
				$itemDepth = substr_count($item['parentpath'], '\\');

				$item['parentpath'] = str_replace('$', '', $item['parentpath']);
				$item['parentpath'] = str_replace('\\', '.', $item['parentpath']);

				if (substr($item['parentpath'],-1) == '.')
				{
					$item['parentpath'] = substr($item['parentpath'], 0, -1);
				}

				$recordArray[$i]['parentid'] = $item['parentid'];
				$recordArray[$i]['companycode'] = $item['companycode'];
				$recordArray[$i]['productcode'] = $item['productcode'];
				$recordArray[$i]['sectioncode'] = $item['sectioncode'];
				$recordArray[$i]['sortorder'] = $item['sortorder'];
				$recordArray[$i]['parentpath'] = $item['parentpath'];
				$recordArray[$i]['code'] = $item['code'];
				$recordArray[$i]['localcode'] = $item['localcode'];
				$recordArray[$i]['name'] = $item['name'];
				$recordArray[$i]['info'] = $item['info'];
				$recordArray[$i]['pricingmodel'] = $item['pricingmodel'];
				$recordArray[$i]['price'] = $item['price'];
				$recordArray[$i]['categorycode'] = $item['categorycode'];
				$recordArray[$i]['islist'] = $item['islist'];
				$recordArray[$i]['isdefault'] = $item['isdefault'];
				$recordArray[$i]['decimalplaces'] = $item['decimalplaces'];
				$recordArray[$i]['categoryactive'] = $item['categoryactive'];
				$recordArray[$i]['componentactive'] = $item['componentactive'];
				$recordArray[$i]['depth'] = $itemDepth;
				$recordArray[$i]['inheritparentqty'] = $item['inheritparentqty'];
				$recordArray[$i]['allowinherit'] = 0;
				$recordArray[$i]['keywords'] = $item['keywords'];
				$recordArray[$i]['taxcode'] = $item['taxcode'];
				$recordArray[$i]['taxrate'] = $item['taxrate'];
				$recordArray[$i]['quantityisdropdown'] = $item['quantityisdropdown'];
				$recordArray[$i]['previewimage'] = $item['previewimage'];
				$recordArray[$i]['displaystage'] = $item['displaystage'];
				$recordArray[$i]['categoryname'] = $item['categoryname'];
				$recordArray[$i]['categoryprompt'] = $item['categoryprompt'];
				$recordArray[$i]['parentsectionname'] = $item['parentsectionname'];
				$recordArray[$i]['parentsectionprompt'] = $item['parentsectionprompt'];
				$recordArray[$i]['parentsectiondisplaystage'] = $item['parentsectiondisplaystage'];
				$recordArray[$i]['requirespagecount'] = $item['requirespagecount'];
				$recordArray[$i]['minimumpagecount'] = $item['minimumpagecount'];
				$recordArray[$i]['maximumpagecount'] = $item['maximumpagecount'];

				/*
				 * Only allow inheritparentqty if the item is
				 * 1, not a single print option or calendar customisation option,
				 * 2, sub component list item with depth >= 3
				 * 3, a check box item with depth >= 2
				 */
				if (($item['sectioncode'] != 'SINGLEPRINTOPTION') && ($item['sectioncode'] != 'CALENDARCUSTOMISATION'))
				{
					if ((($item['islist'] === 1) && ($itemDepth >= 3)) ||
						(($item['islist'] === 0) && ($itemDepth >= 2)))
					{
						// only inherit if the pricing model includes component qty
						if ((TPX_PRICINGMODEL_PERPRODCMPQTY === $item['pricingmodel']) || (TPX_PRICINGMODEL_PERSIDEPERPRODPERCMPQTY === $item['pricingmodel']))
						{
							$recordArray[$i]['allowinherit'] = 1;
						}
					}
				}

		 		$i2 = $i;

				$dummyPriceLinkIDRequired = true;

		 		// find all entries that match and append them in order to the sorted list
		 		while ($i2 < count($pDataArray))
		 		{
		 			$item2 = $pDataArray[$i2];

		 			// compare the two entries
		 			if (($item['parentpath'] == $item2['parentpath']) && ($item['localcode'] == $item2['localcode']))
		 			{
						// if we know the item has a pricelink record with a priceid of -1
						// then we know a dummy record has been created for this componet
						if ($item2['priceid'] == -1)
						{
							$dummyPriceLinkIDRequired = false;
						}

						// the entries match so append this one to the sorted list
		 				$sortedComponentList[] = $item2;

		 				if (! in_array($item2['parentid'], $priceLinkParentIdList))
				 		{
				 			$priceLinkParentIdList[] = $item2['parentid'];
				 		}

						$lastParentID = $item2['parentid'];

						// remove this item from the original list so that it is not processed again
		 				if ($i2 != $i)
		 				{
		 					array_splice($pDataArray, $i2, 1);
		 				}
		 				else
		 				{
		 					$i2++;
		 				}
		 			}
		 			else
		 			{
		 				// the item is different so increase the counter
		 				$i2++;
		 			}
		 		}

				 // we have detected that the componenet does not have a dummy record
				 // append a -1 pricelink id to the node so the javascript knows that we need to
				 // create a dummy priclink record to be sent to the server on the tree save
				 if ($dummyPriceLinkIDRequired)
				 {
					$priceLinkParentIdList[] = -1;
				 }

		 		$recordArray[$i]['pricelinkids'] = implode(',', $priceLinkParentIdList);
		 	}

		 	$existingItemsArray = Array();
		 	$dummyParentID = 0;

		 	$productCode = $recordArray[0]['productcode'];

		 	// loop round each item in the data array so that we can insert dummy items into the right position in the array so that we have the full structure of the tree
		 	for ($i = 0; $i < count($recordArray); $i++)
		 	{
		 		if (! in_array($recordArray[$i]['parentid'], $existingItemsArray))
		 		{
			 		$existingItemsArray[] = $recordArray[$i]['parentid'];
			 		$searchForPath = $recordArray[$i]['parentpath'] . '.' . $recordArray[$i]['localcode'];

			 		for ($j = $i; $j < count($recordArray); $j++)
			 		{
			 			if ($searchForPath != $recordArray[$j]['parentpath'])
			 			{
			 				$dummyItem  = array('parentid' => --$dummyParentID,
							 					'companycode' => $recordArray[$i]['companycode'],
												'productcode' => $recordArray[$i]['productcode'],
			 									'sectioncode' => $recordArray[$i]['sectioncode'],
												'sortorder' => $recordArray[$i]['sortorder'],
			 									'parentpath' => $searchForPath,
												'code' => $recordArray[$i]['code'],
												'localcode' => $recordArray[$i]['localcode'] ,
			 									'name' => $recordArray[$i]['name'],
												'pricingmodel' => $recordArray[$i]['pricingmodel'],
												'price' => $recordArray[$i]['price'],
			 									'categorycode' => $recordArray[$i]['categorycode'],
												'islist' => $recordArray[$i]['islist'],
			 									'isdefault' => $recordArray[$i]['isdefault'],
												'pricelinkids' => $recordArray[$i]['pricelinkids'],
												'categoryactive' => $recordArray[$i]['categoryactive'],
												'componentactive' => $recordArray[$i]['componentactive'],
												'inheritparentqty' => $recordArray[$i]['inheritparentqty'],
												'allowinherit' => $recordArray[$i]['allowinherit'],
												'taxcode' => $recordArray[$i]['taxcode'],
												'taxrate' => $recordArray[$i]['taxrate'],
												'quantityisdropdown' => $recordArray[$i]['quantityisdropdown'],
												'previewimage' => $recordArray[$i]['previewimage'],
												"categoryname" => $recordArray[$i]['categoryname'],
												"categoryprompt" => $recordArray[$i]['categoryprompt'],
												"parentsectionname" => $recordArray[$i]['parentsectionname'],
												"parentsectionprompt" => $recordArray[$i]['parentsectionprompt'],
												"parentsectiondisplaystage" => $recordArray[$i]['parentsectiondisplaystage'],
												"displaystage" => $recordArray[$i]['displaystage'],
												"requirespagecount" => $recordArray[$i]['requirespagecount']
											);

			 				$existingItemsArray[] = $dummyParentID;
			 				array_splice($recordArray, $j + 1, 0, array($dummyItem));
			 				break;
			 			}
			 		}
		 		}
		 	}

		 	$existingItemsArray = Array();
		 	$sortedRecordArray = Array();
		 	$itemCount = count($recordArray);

		 	// loop round the new record array so that we can sort the array into the correct order
		 	for ($i = 0; $i < count($recordArray); $i++)
		 	{
		 		if (! in_array($recordArray[$i]['parentid'], $existingItemsArray))
		 		{
		 			$sortedRecordArray[] = $recordArray[$i];
			 		$existingItemsArray[] = $recordArray[$i]['parentid'];

			 		$searchForPath = $recordArray[$i]['parentpath'] . $recordArray[$i]['localcode'];

			 		for ($j = 0; $j < count($recordArray); $j++)
			 		{
			 			if (substr($recordArray[$j]['parentpath'], 0, strlen($searchForPath)) == $searchForPath)
			 			{
			 				if (! in_array($recordArray[$j]['parentid'], $existingItemsArray))
			 				{
			 					$sortedRecordArray[] = $recordArray[$j];
			 					$existingItemsArray[] = $recordArray[$j]['parentid'];
			 				}
			 			}
			 		}
		 		}
			}
			$recordArray = $sortedRecordArray;

			// start building the tree list
		 	$nextNodeID = 1;
		 	$processedItemsArray = Array();

			// add LINEFOOTER sections to the array as default if there are any sections starting with $LINEFOOTER
			$dummmyLineFooterArray = array('id' => '998', 'parentid'=> --$dummyParentID, 'companycode' => '', 'productcode'  => $productCode, 'groupcode' => '', 'sortorder' => '998', 'sectioncode'  => 'LINEFOOTER', 'parentpath' => 'LINEFOOTER', 'code' => 'LINEFOOTER', 'localcode' => 'LINEFOOTER', 'name'=> '',
						'pricingmodel' => 0, 'categorycode' => '', 'islist' => 1, 'isdefault' => 0, 'priclinkids' => '' , 'categoryactive' => 1, 'requirespagecount' => 0);

			$existingItemsArray = Array();

			// add LINEFOOTER and ORDERFOOTER in the correct order in the recordArray
			for ($i = 0; $i < count($recordArray); $i++)
		 	{
		 		$theItem = $recordArray[$i];

		 		if (substr($theItem['parentpath'], 0, 12) == 'LINEFOOTER')
		 		{
		 			if (! in_array($dummmyLineFooterArray['id'], $existingItemsArray))
		 			{
		 				array_splice($recordArray, $i, 0, array($dummmyLineFooterArray));
		 				$existingItemsArray[] = $dummmyLineFooterArray['id'];
		 			}
		 		}
		 	}

		 	$treeChildren = self::buildProductTree($recordArray, 0, 0, '', $nextNodeID, $processedItemsArray, '',$treeData);

			$tree['PRODUCT'] = self::buildProductNode($productCode);
			$tree['PRODUCT']['children'] = $treeChildren;
	 	}
	 	else
	 	{
	 		$tree = [];
	 	}

		return $tree;
    }

	/**
	 * Build product tree data
	 *
	 * @param array $pProductCodeArray product codes to build trees for
	 * @param string $pCompanyCode company code
     * @param string $pGroupCode license key code
     * @param string $pCacheVersion cache version to add to the tree
	 * @return array Array containing product trees
	 */
	static function buildFullTreeData($pProductCodeArray, $pCompanyCode, $pGroupCode, $pCacheVersion)
	{
		$allTreeDataArray = self::getProductTreeData($pProductCodeArray, $pCompanyCode, $pGroupCode);

		$returnArray = [];

		foreach($allTreeDataArray as $treeProductCode => $treeDataArray)
		{
			$treeArray = self::getProductTree($treeDataArray);

			$treeArray = UtilsObj::reMapKeys($treeArray, true);
			$json = json_encode($treeArray);
			$jsonLength = strlen($json);

			$returnArray[$treeProductCode] = [$json, $jsonLength, $pCacheVersion];
		};

		return $returnArray;
	}

	/**
	 * Build product list
	 *
	 * @param string $pProductCollectionCode
	 * @param string $pProductLayoutCode
     * @param string $pGroupCode
     * @param string $pBrandCode
     * @param string $pCompanyCode
     * @param array $pProductTreesData
	 * @return array Array containing product list
	 */
	static function buildAutoUpdateProductList($pProductCollectionCode, $pProductLayoutCode, $pGroupCode, $pBrandCode, $pCompanyCode, $pProductTreesData)
	{
		$systemUpdateResult = AppAPI_model::systemUpdateProcess('', '', $pProductCollectionCode, $pGroupCode, $pBrandCode, '' , '', '', '1', 'en',
														'', '', '', '', '', '', '', '', '');

		$cacheVersion = $systemUpdateResult['productcacheversion'];

		if (isset($pProductTreesData['data']['alternativelayoutdata']))
		{
			$productLayoutDataArray = $pProductTreesData['data']['alternativelayoutdata']['productLayoutCodeArray'];
			$productLayoutCodeArray = [];
			$dataRequiredproductLayoutCodeArray = [];
			$productTreesArray = [];

			//FILTER OUT THOSE ALREADY IN ONLINE CACHE
			array_map(function ($layout) use ($cacheVersion, &$productLayoutCodeArray, &$dataRequiredproductLayoutCodeArray)
				{
					$layoutCode = $layout['layoutcode'];
					$productLayoutCodeArray[] = $layoutCode;
					if ($layout['cacheversion'] != $cacheVersion)
					{
						$dataRequiredproductLayoutCodeArray[] = $layoutCode;
					}
				}, $productLayoutDataArray
			);

			if (count($dataRequiredproductLayoutCodeArray) > 0)
			{
				$productTreesArray = self::buildFullTreeData($dataRequiredproductLayoutCodeArray, $pCompanyCode, $pGroupCode, $cacheVersion);
			}

			foreach($systemUpdateResult['products']['productlist'] as $key => &$product)
			{
				if (!in_array($product['code'], $productLayoutCodeArray))
				{
					unset($systemUpdateResult['products']['productlist'][$key]);
				}
				else
				{
					$treeData = '';
					if (isset($productTreesArray[$product['code']]))
					{
						$treeData = $productTreesArray[$product['code']];
					}
					else
					{
						//if product code was not in the trees arrays but is in required array then it should send an empty tree
						if (in_array($product['code'], $dataRequiredproductLayoutCodeArray))
						{
							$treeData = [];
							$treeData['PRODUCT'] = self::buildProductNode($product['code']);
							$treeData = UtilsObj::reMapKeys($treeData, true);
							$json = json_encode($treeData);
							$jsonLength = strlen($json);
							$treeData = [$json, $jsonLength, $cacheVersion];
						}
					}
					$product['producttreedata'] = $treeData;
				}
			}

			//rebase the keys which have been unset
			$systemUpdateResult['products']['productlist'] = array_merge($systemUpdateResult['products']['productlist']);
		}

		$componentNameListToUnset = ["coverlist", "paperlist", "calendarcustomisationlist", "taopixailist"];
		foreach ($componentNameListToUnset as $componentName)
		{
			foreach ($systemUpdateResult['products']['productlist'] as &$product)
			{
				unset($product[$componentName]);
			}
		}

		return $systemUpdateResult['products']['productlist'];
	}

    static function getWebViewCartItemFromOrderDataCache($pProjectRef)
    {
        $resultArray = array('error' => TPX_ONLINE_ERROR_NONE, 'errormessage' => '');
        $projectItem = [];

        $dbObj = DatabaseObj::getGlobalDBConnection();

        if ($dbObj)
        {
            if ($stmt = $dbObj->prepare('SELECT `projectref`, `projectdata`, `projectdatalength` FROM `projectorderdatacache` WHERE `projectref` = ?'))
            {
                if ($stmt->bind_param('s', $pProjectRef))
                {
                    if ($stmt->execute())
                    {
                        if ($stmt->store_result())
                        {
                            if ($stmt->num_rows > 0)
                            {
                                if ($stmt->bind_result($projectRef, $projectData, $projectDataLength))
                                {
                                    if ($stmt->fetch())
                                    {
                                        // we have the projectdata data now unserialize it back into an array
                                        if ($projectDataLength > 0)
                                        {
                                            $projectData = gzuncompress($projectData, $projectDataLength);
                                        }

                                        $projectItem = unserialize($projectData);
                                    }
                                }
                                else
                                {
                                    // could not bind result
                                    $resultArray['error'] = TPX_DATA_API_ERROR_DATABASE;
                                    $resultArray['errormessage'] = 'Error: Unable to bind result: ' . $dbObj->error;
                                }
                            }
                            else
                            {
                                $resultArray['error'] = TPX_DATA_API_ERROR_NOITEMSFOUND;
                                $resultArray['errormessage'] = 'Error: No matching records';
                            }
                        }
                        else
                        {
                            // could not store result
                            $resultArray['error'] = TPX_DATA_API_ERROR_DATABASE;
                            $resultArray['errormessage'] = 'Error: Unable to store result: ' . $dbObj->error;
                        }
                    }
                    else
                    {
                        // could not execute statement
                        $resultArray['error'] = TPX_DATA_API_ERROR_DATABASE;
                        $resultArray['errormessage'] = 'Error: Find order failed: ' . $dbObj->error;
                    }

                }
                else
                {
                    // could not bind parameters
                    $resultArray['error'] = TPX_DATA_API_ERROR_DATABASE;
                    $resultArray['errormessage'] = 'retrieve getWebViewCartItemFromOrderDataCache bind ' . $dbObj->error;
                }

                $stmt->free_result();
                $stmt->close();
                $stmt = null;
            }
            else
            {
                // could not prepare statement
                $resultArray['error'] = TPX_DATA_API_ERROR_DATABASE;
                $resultArray['errormessage'] = 'retrieve getWebViewCartItemFromOrderDataCache prepare ' . $dbObj->error;
            }

            $dbObj->close();
        }
        else
        {
            $resultArray['error'] = TPX_DATA_API_ERROR_DATABASE;
            $resultArray['errormessage'] = 'getWebViewCartItemFromOrderDataCache connect ' . $dbObj->error;
        }

        $resultArray['project'] = $projectItem;

        return $resultArray;
    }

    static function getProjectsForWebViewCheckout($pProjectRefList, $pUserID, $pGroupCode)
    {
        $checkoutContentsArray = array('projectreflist' => array(), 'cartdata' => array(), 'lockperiod' => 0, 'userid' => 0);

        $bindPlaceHolders = array();
        $bindParamChars = '';

        foreach ($pProjectRefList as $projectRef)
        {
            $bindPlaceHolders[] = '?';
            $bindParamChars .= 's';
        }
        $queryIn = implode(',', $bindPlaceHolders);

        $dbObj = DatabaseObj::getGlobalDBConnection();

        if ($dbObj)
        {
            $sql = 'SELECT `projectref`, `projectdata`, `projectdatalength` FROM `projectorderdatacache` WHERE `projectref` IN (' . $queryIn . ') AND `source` = 1';

            if ($stmt = $dbObj->prepare($sql))
            {
                if ($stmt->bind_param($bindParamChars, ...$pProjectRefList))
                {
                    if ($stmt->execute())
                    {
                        if ($stmt->store_result())
                        {
                            if ($stmt->num_rows > 0)
                            {
                                if ($stmt->bind_result($projectRef, $projectData, $projectDataLength))
                                {
                                    while ($stmt->fetch())
                                    {
                                        // we have the projectdata data now unserialize it back into an array
                                        if ($projectDataLength > 0)
                                        {
                                            $projectData = gzuncompress($projectData, $projectDataLength);
                                        }

                                        $projectItem = unserialize($projectData);

                                        $checkoutContentsArray['projectreflist'][] = $projectRef;
                                        $checkoutContentsArray['cartdata'][] = $projectItem;
                                        $checkoutContentsArray['userid'] = $pUserID;
                                        $checkoutContentsArray['groupcode'] = $pGroupCode;
                                    }
                                }
                                else
                                {
                                    // could not bind result
                                    $resultArray['error'] = TPX_DATA_API_ERROR_DATABASE;
                                    $resultArray['errormessage'] = 'Error: Unable to bind result: ' . $dbObj->error;
                                }
                            }
                            else
                            {
                                $resultArray['error'] = TPX_DATA_API_ERROR_NOITEMSFOUND;
                                $resultArray['errormessage'] = 'Error: No matching records';
                            }
                        }
                        else
                        {
                            // could not store result
                            $resultArray['error'] = TPX_DATA_API_ERROR_DATABASE;
                            $resultArray['errormessage'] = 'Error: Unable to store result: ' . $dbObj->error;
                        }
                    }
                    else
                    {
                        $error = __FUNCTION__ . ' execute: ' . $dbObj->error;
                    }
                }
                else
                {
                    $error = __FUNCTION__ . ' bind: ' . $dbObj->error;
                }

                $stmt->free_result();
                $stmt->close();
                $stmt = null;
            }
            else
            {
                $error = __FUNCTION__ . ' prepare: ' . $dbObj->error;
            }
            $dbObj->close();
        }

        return $checkoutContentsArray;
    }

    static function deleteProjectOrderDataCacheRecords($pProjectRefArray){

        $returnArray = UtilsObj::getReturnArray();
        $error = TPX_ONLINE_ERROR_NONE;
        $errorParam = '';

        $dbObj = DatabaseObj::getGlobalDBConnection();

        $projectRefs = "'" . implode("','", $pProjectRefArray) . "'";

        if ($dbObj)
        {
            if ($stmt = $dbObj->prepare('DELETE FROM `PROJECTORDERDATACACHE` WHERE `projectref` in (' . $projectRefs  . ')'))
            {
                if (!$stmt->execute())
                {
                    // could not execute statement
                    $error = TPX_ONLINE_ERROR_DATABASE;
                    $errorParam = __FUNCTION__ . ' execute: ' . $dbObj->error;
                }

                $stmt->close();
            }
            else
            {
                // could not prepare statement
                $error = TPX_ONLINE_ERROR_DATABASE;
                $errorParam = __FUNCTION__ . ' prepare: ' . $dbObj->error;
            }

            $dbObj->close();
        }
        else
        {
            $error = TPX_ERROR_CODE_DATABASE;
            $errorParam = __FUNCTION__ . ' connection: Unable to connect to database';
        }

        $returnArray['error'] = $error;
        $returnArray['errorparam'] = $errorParam;

        return $returnArray;
    }
}

?>
