<?php

require_once('../Utils/UtilsDataExport.php');
require_once('../Utils/UtilsEmail.php');
require_once('../Share/Share_model.php');
require_once('../Utils/UtilsLocalization.php');

use Security\PasswordValidationTrait;

class Customer_model
{
    use PasswordValidationTrait;

    /**
     * Used to determine which labels can potentially be
     * returned when a user updates their account
     *
     * @var array
     */
    public static $columnStringsMap = [
        'emailaddress' => ['str_LabelEmailAddress'],
        'countryname' => ['str_LabelCountry'],
        'companyname' => ['str_LabelCompanyName'],
        'contactfirstname' =>  ['str_LabelFirstName'],
        'contactlastname' => ['str_LabelLastName'],
        'address1' => ['str_LabelAddressLine1'],
        'address2' => ['str_LabelAddressLine2'],
        'address3' => ['str_LabelAddressLine3', 'str_LabelSuburb'],
        'address4' => ['str_LabelAddressLine4'],
        'city' => ['str_LabelTownCity'],
        'county' => ['str_LabelCounty'],
        'state' => ['str_LabelState', 'str_LabelProvince'],
        'postcode' => ['str_LabelPostCode', 'str_LabelZIPCode', 'str_LabelPostalCode'],
        'telephonenumber' => ['str_LabelTelephoneNumber'],
        'registeredtaxnumber' => ['str_TaxNumber'],
        'registeredtaxnumbertype' => ['str_TaxNumberType']
    ];

    /**
     * The fields are available for all countries
     *
     * @var array
     */
    public static $globalColumnStrings = [
        'str_LabelTelephoneNumber',
        'str_LabelEmailAddress'
    ];

    /**
     * Brazilian tax number strings
     *
     * @var array
     */
    public static $taxNumberTypes = [
        1 => 'str_LabelCustomerTaxNumberTypePersonal',
        2 => 'str_LabelCustomerTaxNumberTypeCorporate'
    ];

    static function initialize()
    {
        global $gSession;

        $resultArray = Array();
        $result = '';
        $resultParam = '';

        if ($gSession['ref'] > 0)
        {
            if (AuthenticateObj::WebSessionActive() == 1)
            {
                DatabaseObj::updateSession();
            }
        }
        else
        {
            $result = 'str_ErrorNoSessionRef';
        }

        $resultArray['result'] = $result;
        $resultArray['resultparam'] = $resultParam;

        return $resultArray;
    }

    static function getOrderList()
    {
        global $gSession;
		global $gConstants;

        $resultArray = Array();
        $orderItems = Array();
        $tempOrderItems = Array();
        $currentDateTime = DatabaseObj::getServerTime();
		$error = '';
        $errorParam = '';
        $onlineProjectThumbnails = [];

		$orderItemsArray = DatabaseObj::getOrderListCart($gSession['userid'], $gSession['browserlanguagecode']);

		if ($orderItemsArray['error'] == '')
		{
			$sharedInfo = Share_model::getSharedItemsForUser($gSession['userid']);
			if ($sharedInfo['result'] == '')
			{
				//only get online project list if both online enabled and online order present
				if (($gConstants['optiondesol']) && ($orderItemsArray['onlineprojectpresent']))
				{
					$onlineProjectListResult = self::getOnlineWizardModeList($orderItemsArray['items']);

					if ($onlineProjectListResult['error'] != '')
					{
						$error = $onlineProjectListResult['error'];
						$errorParam = $onlineProjectListResult['errorparam'];
					}

                    // Get a list of project preview thumbnails from online for the projects.
                    $onlineProjectThumbnailResult = self::getOnlineProjectThumbnails($orderItemsArray['items']);
                    $onlineProjectThumbnails = $onlineProjectThumbnailResult['data'];
				}

				if ($error == '')
				{
					foreach ($orderItemsArray['items'] as $row)
					{
						$includeOrder = true;
						$isOwner = 0;
						$wizardMode = 0;

						if ($row['temporder'] == 1)
						{
							if (($row['orderstatus'] == TPX_ORDER_STATUS_CANCELLED) || ($row['orderstatus'] == TPX_ORDER_STATUS_CONVERTED))
							{
								$includeOrder = false;
							}

							if ($row['temporderexpirydate'] < $currentDateTime)
							{
								$includeOrder = false;
							}
						}

						if ($includeOrder)
						{
							$projectPreviewThumbnail = '';

							// get wizard and online thumbnail data, only runs if online
							if (($row['source'] == TPX_SOURCE_ONLINE) && ($gConstants['optiondesol']))
							{
								self::getOnlineProjectWizard($row['projectref'], $onlineProjectListResult['projects'], $wizardMode, $isOwner);
								// Set the preview thumbnail path if it exists and the order item isn't a companion.
								$projectPreviewThumbnail = ((array_key_exists($row['projectref'], $onlineProjectThumbnails)) && ($row['parentorderitemid'] === 0)) ? $onlineProjectThumbnails[$row['projectref']] : '';
							}
							else
							{
								$isOwner = 1;

								//get the desktop thumbnail data
								$projectThumbnailResultArray = DatabaseObj::getDesktopProjectThumbnailAvailabilityFromProjectRef($row['projectref']);
								
								//if we have found a valid desktop thumbnail then build the URL for it
								if (($projectThumbnailResultArray['error'] === '') && ($projectThumbnailResultArray['available'] === true))
								{
									$projectPreviewThumbnail = UtilsObj::buildDesktopProjectThumbnailWebURL($row['projectref']);
								}
							}

							if ($row['previewsonline'] == 1)
							{
								$row['previewurl'] = '?fsaction=Customer.showPreview&ref2=' . $row['uploadref'] . '&projectref=' . $row['projectref'];
							}

							if (($row['temporder'] == 1) && ($row['orderstatus'] == TPX_ORDER_STATUS_IN_PROGRESS))
							{
								if (! isset($tempOrderItems[$row['ordernumber']]))
								{
									$tempOrderItems[$row['ordernumber']] = array(
										'ordernumber' => $row['ordernumber'],
										'paymentreceived' => $row['paymentreceived'],
										'formattedorderdate' => $row['formattedorderdate'],
										'formatteddateexpires' => $row['formatteddateexpires'],
										'formattedordertotal' => $row['formattedordertotal'],
										'status' => $row['status'],
										'sessionid' => $row['sessionid'],
										'orderstatus' => $row['orderstatus'],
										'canreorder' => $row['canreorder'],
										'canmodify' => $row['canmodify'],
										'orderid' => $row['orderid'],
										'product' => array(array(
												'id' => $row['id'],
												'previewimage' => $row['previewimage'],
												'productname' => UtilsObj::escapeInputForHTML($row['productname']),
												'projectname' => UtilsObj::escapeInputForHTML($row['projectname']),
												'projectref' => $row['projectref'],
												'status' => $row['status'],
												'source' => $row['source'],
												'previewsonline' => $row['previewsonline'],
												'previewurl' => isset($row['previewurl']) ? $row['previewurl'] : '',
												'orderstatus' => $row['orderstatus'],
												'workflowtype' => $row['workflowtype'],
												'productindent' => $row['productindent'],
												'wizardmode' => $wizardMode,
												'canmodify' => $row['canmodify'],
												'parentorderitemid' => $row['parentorderitemid'],
												'isowner' => $isOwner,
                                                'dataavailable' => $row['dataavailable'],
                                                'projectpreviewthumbnail' => $projectPreviewThumbnail
										))
									);
								}
								else
								{
									if ($row['status'] == 0)
									{
										 $tempOrderItems[$row['ordernumber']]['status'] = $row['status'];
									}

									$tempOrderItems[$row['ordernumber']]['product'][] = array(
										'id' => $row['id'],
										'previewimage' => $row['previewimage'],
										'productname' => UtilsObj::escapeInputForHTML($row['productname']),
										'projectname' => UtilsObj::escapeInputForHTML($row['projectname']),
										'projectref' => $row['projectref'],
										'status' => $row['status'],
										'source' => $row['source'],
										'previewsonline' => $row['previewsonline'],
										'previewurl' => isset($row['previewurl']) ? $row['previewurl'] : '',
										'orderstatus' => $row['orderstatus'],
										'canreorder' => $row['canreorder'],
										'canmodify' => $row['canmodify'],
										'parentorderitemid' => $row['parentorderitemid'],
										'workflowtype' => $row['workflowtype'],
										'productindent' => $row['productindent'],
										'wizardmode' => $wizardMode,
										'isowner' => $isOwner,
										'dataavailable' => $row['dataavailable'],
                                        'projectpreviewthumbnail' => $projectPreviewThumbnail
									);
								}
							}

							if ($row['temporder'] == 0)
							{
								// has this line item been shared
								$row['isShared'] = false;
								if ($row['orderstatus'] != TPX_ORDER_STATUS_CANCELLED)
								{
									if (array_key_exists($row['id'], $sharedInfo['data']))
									{
										$row['isShared'] = (($sharedInfo['data'][$row['id']]['shared'] == true) && ($sharedInfo['data'][$row['id']]['unshared'] == false));
									}
								}

								if (! isset($orderItems[$row['ordernumber']]))
								{
									$orderItems[$row['ordernumber']] = array(
										'ordernumber' => $row['ordernumber'],
										'paymentreceived' => $row['paymentreceived'],
										'formattedorderdate' => $row['formattedorderdate'],
										'formatteddateexpires' => $row['formatteddateexpires'],
										'formattedordertotal' => $row['formattedordertotal'],
										'status' => $row['status'],
										'orderstatus' => $row['orderstatus'],
										'showpaymentstatus' => ($row['orderstatus'] != TPX_ORDER_STATUS_CANCELLED ? 1 : 0),
										'canreorder' => $row['canreorder'],
										'canmodify' => $row['canmodify'],
										'orderid' => $row['orderid'],
										'product' => array(array(
												'id' => $row['id'],
												'previewimage' => $row['previewimage'],
												'productname' => UtilsObj::escapeInputForHTML($row['productname']),
												'projectname' => UtilsObj::escapeInputForHTML($row['projectname']),
												'projectref' => $row['projectref'],
												'isShared' => $row['isShared'],
												'status' => $row['status'],
												'source' => $row['source'],
												'previewsonline' => $row['previewsonline'],
												'previewurl' => isset($row['previewurl']) ? $row['previewurl'] : '',
												'orderstatus' => $row['orderstatus'],
												'workflowtype' => $row['workflowtype'],
												'productindent' => $row['productindent'],
												'wizardmode' => $wizardMode,
												'canmodify' => $row['canmodify'],
												'parentorderitemid' => $row['parentorderitemid'],
												'isowner' => $isOwner,
												'dataavailable' => $row['dataavailable'],
												'canreorder' => $row['canreorder'],
                                                'projectpreviewthumbnail' => $projectPreviewThumbnail
										))
									);

								}
								else
								{
									$orderItems[$row['ordernumber']]['product'][] = array(
										'id' => $row['id'],
										'previewimage' => $row['previewimage'],
										'productname' => UtilsObj::escapeInputForHTML($row['productname']),
										'projectname' => UtilsObj::escapeInputForHTML($row['projectname']),
										'projectref' => $row['projectref'],
										'isShared' => $row['isShared'],
										'status' => $row['status'],
										'source' => $row['source'],
										'previewsonline' => $row['previewsonline'],
										'previewurl' => isset($row['previewurl']) ? $row['previewurl'] : '',
										'orderstatus' => $row['orderstatus'],
										'canreorder' => $row['canreorder'],
										'canmodify' => $row['canmodify'],
										'parentorderitemid' => $row['parentorderitemid'],
										'workflowtype' => $row['workflowtype'],
										'productindent' => $row['productindent'],
										'wizardmode' => $wizardMode,
										'isowner' => $isOwner,
										'dataavailable' => $row['dataavailable'],
                                        'projectpreviewthumbnail' => $projectPreviewThumbnail
									);

									// we need to check to see if the showpayment status is already set to 1.
									// if it is not then we must check each additional item in the order.
									// if at least one item has a status of active then showpaymentstatus should be set to 1.
									if ($orderItems[$row['ordernumber']]['showpaymentstatus'] != 1)
									{
										$orderItems[$row['ordernumber']]['showpaymentstatus'] = ($row['orderstatus'] != TPX_ORDER_STATUS_CANCELLED ? 1 : 0);
									}
								}
							}
						}
					}

					$resultArray['orders'] = &$orderItems;
					$resultArray['temporders'] = &$tempOrderItems;
				}
			}
			else
			{
				$error = $sharedInfo['result'];
				$errorParam = $sharedInfo['resultparam'];
			}
		}
		else
		{
			$error = $orderItemsArray['error'];
			$errorParam = $orderItemsArray['errorparam'];
		}

		$resultArray['error'] = $error;
		$resultArray['errorparam'] = $errorParam;

        return $resultArray;
    }

	static function getOnlineProjectWizard($pProjectRef, $pProjectsList, &$pWizardMode, &$pIsOwner)
	{
		// make sure the item exists in the array before accessing it
		if (array_key_exists($pProjectRef, $pProjectsList))
		{
			//project ref is used as key in associative array allowing the wizard mode to be assigned without any further sorting
			$pWizardMode = $pProjectsList[$pProjectRef];
			$pIsOwner = 1;
		}
	}

    static function updateAccountDetails($pUpdateAccountReason)
    {
        global $gConstants;
        global $gSession;

        $resultArray = Array();
        $result = '';
        $userAccountArray = array();
        $isConfirmation = 0;
        $isAuthenticated = true;
        $canUpdateAccount = true;
        $isExternalAccount = 0;

		$userAccountArray['reason'] = $pUpdateAccountReason;
		$userAccountArray['countrycode'] = UtilsObj::getPOSTParam('countrycode');

		// see if there are special address fields like
		// add1=add41, add42 - add43
		// meaning address1 = add41 + ", "  + add42 + " - " + add43
		// and     address4 = add41 + "<p>" + add42 + "<p>" + add43
		UtilsAddressObj::specialAddressFields($userAccountArray['countrycode']);

		$userAccountArray['contactfirstname'] = UtilsObj::getPOSTParam('contactfname');
		$userAccountArray['contactlastname'] = UtilsObj::getPOSTParam('contactlname');
		$userAccountArray['companyname'] = UtilsObj::getPOSTParam('companyname');
		$userAccountArray['address1'] = UtilsObj::getPOSTParam('address1');
		$userAccountArray['address2'] = UtilsObj::getPOSTParam('address2');
		$userAccountArray['address3'] = UtilsObj::getPOSTParam('address3');
		$userAccountArray['address4'] = UtilsObj::getPOSTParam('address4');

		// we need to check to see if the string contains @@TAOPIXTAG@@. If it does then this means that it is a special address field.
		// we then need to convert @@TAOPIXTAG@@ back to a <p> so that it can be stored correctly in the database.
		$userAccountArray['address4'] = implode('<p>', mb_split('@@TAOPIXTAG@@', $userAccountArray['address4']));

		$userAccountArray['city'] = UtilsObj::getPOSTParam('city');
		$userAccountArray['state'] = UtilsObj::getPOSTParam('state');
		$userAccountArray['county'] = UtilsObj::getPOSTParam('county');
		$userAccountArray['regioncode'] = UtilsObj::getPOSTParam('regioncode');
		$userAccountArray['region'] = UtilsObj::getPOSTParam('region');
		$userAccountArray['postcode'] = UtilsObj::getPOSTParam('postcode');
		$userAccountArray['countryname'] = UtilsObj::getPOSTParam('countryname');
		$userAccountArray['telephonenumber'] = UtilsObj::getPOSTParam('telephonenumber');
		$userAccountArray['addressupdated'] = 1;

		// Email information.
		$userAccountArray['emailaddress'] = UtilsObj::getPOSTParam('email');
		$userAccountArray['originalemail'] = UtilsObj::getPOSTParam('originalemail');

		// registered tax number information
        $userAccountArray['registeredtaxnumbertype'] = UtilsObj::getPOSTParam('registeredtaxnumbertype', 0);
        $userAccountArray['registeredtaxnumber'] = UtilsObj::getPOSTParam('registeredtaxnumber');

        $origUserAccountArray = DatabaseObj::getUserAccountFromID($gSession['userid']);
        $userAccountArray['datecreated'] = $origUserAccountArray['datecreated'];

		/*
		 * If customer re-authentication is active, we need to re-validate their password, this is only applicable when address updated flag is not 2.
		 * When the address updated flag is 2 this is a new user from online, we do not want to reprompt them for their password.
		 */
		if (($gConstants['customerupdateauthrequired'] == 1) && ($origUserAccountArray['addressupdated'] != 2))
		{
			$isCustomerValid = static::validatePassword('confirmpassword', 'confirmformat', $origUserAccountArray);
			if (($isCustomerValid['result'] == 'str_ErrorNoPassword') || ($isCustomerValid['result'] == 'str_MessageAuthMode_Password'))
			{
				$isAuthenticated = false;
				$result = $isCustomerValid['result'];
			}
		}

		// if the address updated flag is set to 2 then we know this is a new customer account from online.
		// the user has been forced to update their address so we must ignore all other license key/customer address settings at this point
		if ($origUserAccountArray['addressupdated'] == 2)
		{
			$userAccountArray['canmodifyshippingaddress'] = 1;
			$userAccountArray['canmodifybillingaddress'] = 1;
			$userAccountArray['canmodifycontactdetails'] = 1;
		}
		else
		{
			// check to see if the customer record is using the licenskey address preferences.
			// if it is then we must check to see if the license key allows for the shipping or billing address to be modified.
			if ($origUserAccountArray['defaultaddresscontrol'] == 1)
			{
				$licenseKeyArray = DatabaseObj::getLicenseKeyFromCode($origUserAccountArray['groupcode']);
				$userAccountArray['canmodifyshippingaddress'] = $licenseKeyArray['canmodifyshippingaddress'];
				$userAccountArray['canmodifybillingaddress'] = $licenseKeyArray['canmodifybillingaddress'];
				$userAccountArray['canmodifycontactdetails'] = $licenseKeyArray['canmodifyshippingcontactdetails'];
			}
			else
			{
				$userAccountArray['canmodifyshippingaddress'] = $origUserAccountArray['canmodifyshippingaddress'];
				$userAccountArray['canmodifybillingaddress'] = $origUserAccountArray['canmodifybillingaddress'];
				$userAccountArray['canmodifycontactdetails'] = $origUserAccountArray['canmodifyshippingcontactdetails'];
			}
		}

		$userAccountArray['usedefaultcurrency'] = $origUserAccountArray['usedefaultcurrency'];
		$userAccountArray['currencycode'] = $origUserAccountArray['currencycode'];

		if (($userAccountArray['canmodifyshippingaddress'] == 0) && ($userAccountArray['canmodifybillingaddress'] == 0) && ($userAccountArray['canmodifycontactdetails'] == 1))
		{
			$userAccountArray['companyname'] = $origUserAccountArray['companyname'];
			$userAccountArray['address1'] = $origUserAccountArray['address1'];
			$userAccountArray['address2'] = $origUserAccountArray['address2'];
			$userAccountArray['address3'] = $origUserAccountArray['address3'];
			$userAccountArray['address4'] = $origUserAccountArray['address4'];
			$userAccountArray['city'] = $origUserAccountArray['city'];
			$userAccountArray['state'] = $origUserAccountArray['state'];
			$userAccountArray['county'] = $origUserAccountArray['county'];
			$userAccountArray['regioncode'] = $origUserAccountArray['regioncode'];
			$userAccountArray['region'] = $origUserAccountArray['region'];
			$userAccountArray['postcode'] = $origUserAccountArray['postcode'];
			$userAccountArray['countryname'] = $origUserAccountArray['countryname'];
			$userAccountArray['countrycode'] = $origUserAccountArray['countrycode'];
        	$userAccountArray['registeredtaxnumbertype'] = $origUserAccountArray['registeredtaxnumbertype'];
        	$userAccountArray['registeredtaxnumber'] = $origUserAccountArray['registeredtaxnumber'];
		}
		else
		{
			// if the user was able to change the address i.e country then
		     // check to see if the Taopix Customer Account API script is present.
			 if (($gConstants['optionwscrp']) && (file_exists("../Customise/scripts/EDL_TaopixCustomerAccountAPI.php")))
			 {
				 require_once('../Customise/scripts/EDL_TaopixCustomerAccountAPI.php');

				// If the customer account override function exists pass account details to the external script
				if (method_exists('CustomerAccountAPI', 'customerAccountOverride'))
				{
					$userAccountArray['groupcode'] = $origUserAccountArray['groupcode'];
					$userAccountArray['brandcode'] = $gSession['webbrandcode'];

					$userAccountArray = CustomerAccountAPI::customerAccountOverride($userAccountArray);
				}
			 }
		}

  		// first attempt to update the account details via an external script
		if (($gConstants['optionwscrp']) && (file_exists('../Customise/scripts/EDL_ExternalCustomerAccount.php')))
		{
			require_once('../Customise/scripts/EDL_ExternalCustomerAccount.php');

			if (method_exists('ExternalCustomerAccountObj', 'updateAccountDetails'))
			{
				$isExternalAccount = 1;

				// create a user account array for the values we can change
				$externalUserAccountArray = Array();
				$externalUserAccountArray['companyname'] = $userAccountArray['companyname'];
				$externalUserAccountArray['address1'] = $userAccountArray['address1'];
				$externalUserAccountArray['address2'] = $userAccountArray['address2'];
				$externalUserAccountArray['address3'] = $userAccountArray['address3'];
				$externalUserAccountArray['address4'] = $userAccountArray['address4'];
				$externalUserAccountArray['city'] = $userAccountArray['city'];
				$externalUserAccountArray['county'] = $userAccountArray['county'];
				$externalUserAccountArray['state'] = $userAccountArray['state'];
				$externalUserAccountArray['regioncode'] = $userAccountArray['regioncode'];
				$externalUserAccountArray['region'] = $userAccountArray['region'];
				$externalUserAccountArray['postcode'] = $userAccountArray['postcode'];
				$externalUserAccountArray['countrycode'] = $userAccountArray['countrycode'];
				$externalUserAccountArray['countryname'] = $userAccountArray['countryname'];
				$externalUserAccountArray['telephonenumber'] = $userAccountArray['telephonenumber'];
				$externalUserAccountArray['emailaddress'] = $userAccountArray['emailaddress'];
				$externalUserAccountArray['contactfirstname'] = $userAccountArray['contactfirstname'];
				$externalUserAccountArray['contactlastname'] = $userAccountArray['contactlastname'];
				$externalUserAccountArray['registeredtaxnumbertype'] = $userAccountArray['registeredtaxnumbertype'];
				$externalUserAccountArray['registeredtaxnumber'] = $userAccountArray['registeredtaxnumber'];
				$externalUserAccountArray['usedefaultpaymentmethods'] = $origUserAccountArray['usedefaultpaymentmethods'];
				$externalUserAccountArray['paymentmethods'] = $origUserAccountArray['paymentmethods'];
				$externalUserAccountArray['taxcode'] = $origUserAccountArray['taxcode'];
				$externalUserAccountArray['shippingtaxcode'] = $origUserAccountArray['shippingtaxcode'];
				$externalUserAccountArray['uselicensekeyforshippingaddress'] = $origUserAccountArray['uselicensekeyforshippingaddress'];
				$externalUserAccountArray['canmodifyshippingaddress'] = $origUserAccountArray['canmodifyshippingaddress'];
				$externalUserAccountArray['canmodifyshippingcontactdetails'] = $origUserAccountArray['canmodifyshippingcontactdetails'];
				$externalUserAccountArray['uselicensekeyforbillingaddress'] = $origUserAccountArray['uselicensekeyforbillingaddress'];
				$externalUserAccountArray['canmodifybillingaddress'] = $origUserAccountArray['canmodifybillingaddress'];
				$externalUserAccountArray['useremaildestination'] = $origUserAccountArray['useremaildestination'];
				$externalUserAccountArray['defaultaddresscontrol'] = $origUserAccountArray['defaultaddresscontrol'];
				$externalUserAccountArray['canmodifypassword'] = $origUserAccountArray['canmodifypassword'];
				$externalUserAccountArray['creditlimit'] = $origUserAccountArray['creditlimit'];
				$externalUserAccountArray['accountbalancedifference'] = 0.00;
				$externalUserAccountArray['giftcardbalancedifference'] = 0.00;
				$externalUserAccountArray['sendmarketinginfo'] = $origUserAccountArray['sendmarketinginfo'];
				$externalUserAccountArray['isactive'] = $origUserAccountArray['isactive'];


				// update the user account via the external script
				$paramArray = Array();
				$paramArray['languagecode'] = UtilsObj::getBrowserLocale();
				$paramArray['isadmin'] = 0;
				$paramArray['id'] = $gSession['userid'];
				$paramArray['origgroupcode'] = $origUserAccountArray['groupcode'];
				$paramArray['origbrandcode'] = $gSession['webbrandcode'];
				$paramArray['origlogin'] = $origUserAccountArray['login'];
				$paramArray['origaccountcode'] = $origUserAccountArray['accountcode'];
				$paramArray['newgroupcode'] = $origUserAccountArray['groupcode'];
				$paramArray['newbrandcode'] = $gSession['webbrandcode'];
				$paramArray['newlogin'] = $origUserAccountArray['login'];
				$paramArray['newaccountcode'] = $origUserAccountArray['accountcode'];
				$paramArray['passwordchanged'] = 0;
				$paramArray['passwordformat'] = TPX_PASSWORDFORMAT_MD5;
				$paramArray['password'] = $origUserAccountArray['password'];
				$paramArray['status'] = $origUserAccountArray['addressupdated'];
				$paramArray['useraccount'] = $externalUserAccountArray;

				$result = ExternalCustomerAccountObj::updateAccountDetails($paramArray);

				if ($result == '')
				{
					$result = 'str_LabelAccountDetailsUpdated';
					$isConfirmation = 1;
				}
				else
				{
					$canUpdateAccount = false;
				}
			}
		}

        // unless we have received an error while updating an external account we always want to update the taopix account
		if ($canUpdateAccount && $isAuthenticated)
		{
			$dbObj = DatabaseObj::getConnection();
			if ($dbObj)
			{
				if ($stmt = $dbObj->prepare('UPDATE `USERS` SET `companyname` = ?, `address1` = ?, `address2` = ?, `address3` = ?, `address4` = ?,
					`city` = ?, `county` = ?, `state` = ?, `regioncode` = ?, `region` = ?, `addressupdated` = ?, `postcode` = ?,
					`countrycode` = ?, `countryname` = ?, `telephonenumber` = ?, `contactfirstname` = ?, `contactlastname` = ?, `registeredtaxnumbertype` = ?,
					`registeredtaxnumber` = ?, `usedefaultcurrency` = ?, `currencycode` = ? 
					WHERE (`id` = ?) AND ((? = 1) OR (? = 1) OR (? = 1) OR (? = 1))'))
				{
                    if ($stmt->bind_param('sssss' . 'sssss' . 'isss' . 'sssi' . 'sis' . 'iiiii',
                                $userAccountArray['companyname'], $userAccountArray['address1'], $userAccountArray['address2'], $userAccountArray['address3'], $userAccountArray['address4'],
                                $userAccountArray['city'], $userAccountArray['county'], $userAccountArray['state'], $userAccountArray['regioncode'], $userAccountArray['region'],
                                $userAccountArray['addressupdated'], $userAccountArray['postcode'], $userAccountArray['countrycode'], $userAccountArray['countryname'],
                                $userAccountArray['telephonenumber'], $userAccountArray['contactfirstname'], $userAccountArray['contactlastname'], $userAccountArray['registeredtaxnumbertype'],
                                $userAccountArray['registeredtaxnumber'], $userAccountArray['usedefaultcurrency'], $userAccountArray['currencycode'],
								$gSession['userid'], $userAccountArray['canmodifyshippingaddress'], $userAccountArray['canmodifybillingaddress'], $userAccountArray['canmodifycontactdetails'], $isExternalAccount))
					{
						if ($stmt->execute())
						{
							if (($dbObj->affected_rows == 1) || ($isExternalAccount == 1))
							{
                                $gSession['username'] = $userAccountArray['contactfirstname'] . ' ' . $userAccountArray['contactlastname'];
                                DatabaseObj::updateSession();

								DatabaseObj::updateActivityLog($gSession['ref'], 0, $gSession['userid'], $gSession['userlogin'],
										$gSession['username'], 0, 'CUSTOMER', 'UPDATEACCOUNTDETAILS', '', 1);

								DataExportObj::EventTrigger(TPX_TRIGGER_CUSTOMER_EDIT, 'CUSTOMER', $gSession['userid'], 0);

								// only set the result if this is not an external account
								if ($isExternalAccount == 0)
								{
									$result = 'str_LabelAccountDetailsUpdated';
									$isConfirmation = 1;
								}
							}
							else
							{
								if ($dbObj->affected_rows == -1)
								{
									DatabaseObj::updateActivityLog($gSession['ref'], 0, $gSession['userid'], $gSession['userlogin'],
											$gSession['username'], 0, 'CUSTOMER', 'UPDATEACCOUNTDETAILS', 'str_ErrorNoAccount', 0);
								}
                                else if ($dbObj->affected_rows == 0)
                                {
                                    $result = 'str_LabelAccountDetailsUpdated';
                                    $isConfirmation = 1;
                                }
							}
						}
					}

					$stmt->free_result();
					$stmt->close();
				}

				$dbObj->close();
			}
        }

		// If we have updated account details and this is not an initial account details updated.
		if (('str_LabelAccountDetailsUpdated' == $result) && ($origUserAccountArray['addressupdated'] != 2))
        {
            self::sendAccountChangesEmail($origUserAccountArray, $userAccountArray['emailaddress'], $userAccountArray['originalemail']);
        }


        $resultArray['isConfirmation'] = $isConfirmation;
    	$resultArray['result'] = $result;

    	return $resultArray;
    }

    /**
     * @param $pNewEmail
     * @param $pOriginalEmail
     * @param $pOrigAccountArray
     */
	static function sendAccountChangesEmail($pOrigAccountArray, $pNewEmail, $pOriginalEmail)
	{
	    global $gSession;
	    global $ac_config;

		// no changes recorded
		$numChanges = 0;

		// Get the brand information before generating the email.
        $brandSettings = DatabaseObj::getBrandingFromCode($gSession['webbrandcode']);

        // get smarty to transform column names
        $smarty = SmartyObj::newSmarty('');

        // Create the link to be used in the email, to allow the email address to be updated.
        $baseURL = $brandSettings['displayurl'];

        // Build the brand URL based on default brand if displayurl is empty.
        $defaults = DatabaseObj::getBrandingFromCode('');
        if ($baseURL == '')
        {
            $baseURL = UtilsObj::correctPath($defaults['displayurl']) . ($ac_config['WEBBRANDFOLDERNAME'] == '' ? 'Branding' : $ac_config['WEBBRANDFOLDERNAME']) . '/' . $brandSettings['name'] . '/';
        }

        // perform another call against the database to get the updated array for easy comparison
        $updatedUserAccountArray = DatabaseObj::getUserAccountFromID($gSession['userid']);

        // get a list of updates the user has made
        $updates = array_diff_assoc($updatedUserAccountArray, $pOrigAccountArray);

        // has the email address changed?
        $hasEmailAddressChanged = ($pNewEmail !== $pOriginalEmail);

        // count the changes
        $numChanges += count($updates);

        // get the country address format
        $country = UtilsAddressObj::getCountry($updatedUserAccountArray['countrycode']);

        // split the label string into an array
        $labelArray = explode(',', $country['fieldlabels']);
        if(array_key_exists(0, $labelArray) && trim($labelArray[0]) == '') {
            $labelArray = [];
        }
        $fieldLabels = array_values($labelArray);

        // empty array of final changes
        $changes = [];

        // we need to generate a list of changed fields => values that have been
        // changed and get them in a format supported by the taopix mailer
        $disposable = array_filter(array_keys($updates), function($key) use ($updates, $smarty, $fieldLabels, &$changes) {
            // does the column name exist in either of the string maps?
            if (!array_key_exists($key, static::$columnStringsMap)) {
                return false;
            }

            // extract the array of possible labels for the field
            $map = static::$columnStringsMap[$key];

            // set the default smarty tag
            $tag = $map[0];

            // get the position of the $key in $columnStringsMap
            $pos = array_search($key, array_keys(static::$columnStringsMap)) - 1;

            // get the intersecting point between the labels and reset the array keys
            $tags = array_values(array_intersect($fieldLabels, $map));

            // check we have values from the previous step
            $tagCount = count($tags);

            // if we have a positive result
            if ($tagCount > 0) {
                // check to see if we have an updated position
                $validPos = array_search($tags[0], $fieldLabels);
                // update it if we do
                if(gettype($validPos) !== 'boolean') {
                    $pos = $validPos;
                }
                // overwrite the smarty tag with the custom country label
                $tag = $tags[0];
            }

            // return false if we have an empty value
            if (!strlen($tag)) {
                return false;
            }

            // if we have a field label ($tag), ensure it's in the selected countries field labels.
            // or in the global field labels. if it's not in either, omit it from the email.
            if ((!empty($fieldLabels)) && (!in_array($tag, $fieldLabels)) && !in_array($tag, static::$globalColumnStrings)) {
                return false;
            }

            // format the label
            $formattedLabel = $smarty->get_config_vars($tag);
            // set the formatted key => value
            $changes[$pos] = [sprintf("%s : %s", $formattedLabel, (strlen(trim($updates[$key])) < 1 ? "Blank": $updates[$key]))];
            return true;
        });

        // re-order the final array to match the form
        ksort($changes);

        //reset the array keys so TaopixMailer behaves
        $changes = array_values($changes);

        // only send an email if there have been changes & the email address
        // has not been modified
        if ($numChanges > 0 && !$hasEmailAddressChanged)
        {
            // send a list of changed fields from the update.
            (new TaopixMailer())->sendTemplateEmail('customer_detailsupdated', $gSession['webbrandcode'],
                $brandSettings['applicationname'], $baseURL, $gSession['browserlanguagecode'], '',
                $updatedUserAccountArray['emailaddress'], '', '', $gSession['userid'],
                ['changes' => $changes, 'user' => $updatedUserAccountArray['contactfirstname']]
            );
        }

        // if the email address has changed
        if ($hasEmailAddressChanged)
        {
            self::sendEmailUpdateRequestNotification($baseURL, $brandSettings['applicationname'], $pNewEmail, $pOriginalEmail, $updatedUserAccountArray, $changes);
        }
    }

	/**
	 * Send an email to the proposed new email address to make sure it is valid.
	 *
	 * @param $pBaseURL
	 * @param $pAppName
	 * @param string $pNewEmail - New email address to be verified.
	 * @param string $pOriginalEmail - Current email address assigned to the account.
	 * @param array $pUserAccountArray - account details of the user being updated.
	 * @param $changes - List of changes made by the user (not including email).
	 * @global array $gSession
	 */
    static function sendEmailUpdateRequestNotification($pBaseURL, $pAppName, $pNewEmail, $pOriginalEmail, $pUserAccountArray, $changes)
    {
        global $gSession;

        $systemConfigArray = DatabaseObj::getSystemConfig();

        // Generate a link to send via email and data to update the email address.
        $tokenString = UtilsObj::createRandomString(32);

        $hmacStr = $gSession['userid'] . $pUserAccountArray['datecreated'] . $pOriginalEmail . $tokenString;

        $updateEmailRequestData = array();
        $updateEmailRequestData['token'] = $tokenString;
        $updateEmailRequestData['userid'] = $gSession['userid'];
        $updateEmailRequestData['new'] = $pNewEmail;
        $updateEmailRequestData['original'] = $pOriginalEmail;
        $updateEmailRequestData['hmac'] = hash_hmac('sha256', $hmacStr, $systemConfigArray['systemkey']);

        $emailUpdateRequestStr = 'tkn=' . $updateEmailRequestData['token'];
        $emailUpdateRequestStr .= chr(10) . 'hmac=' . $updateEmailRequestData['hmac'];

		$displayUserName = (($pOriginalEmail != $gSession['userlogin']) && ($pNewEmail != $gSession['userlogin']));
        // Set the initial template for the email.
        $emailTemplate = 'customer_emailupdaterequest';

        // Initial email parameters.
        $emailParamArray = array(
            "userid" => $gSession['userid'],
            "newemail" => $pNewEmail,
            "originalemail" => $pOriginalEmail,
            "username" => $gSession['userlogin'],
            "displayurl" => $pBaseURL,
            "changes" => $changes,
			"displayusername" => $displayUserName
        );

        // Test to make sure the email is not already in use.
        $emailUniqueCheckResult = DatabaseObj::checkLoginUnique($gSession['webbrandcode'], $pNewEmail);

        if ('' == $emailUniqueCheckResult['result'])
        {
            // No specific account was found with the email address in the login field.
            // Check for the email usage in the email address field.
            $userEmailAccountArray = DatabaseObj::getValidUserAccountsForEmailAndBrand($gSession['webbrandcode'], $pNewEmail, '', 0, false);

            if ('str_ErrorNoAccount' == $userEmailAccountArray['result'])
            {
                // No other accounts are using the email address in the contact field.
                // Create a record in the AUTHENTICATIONDATASTORE table so the email can be updated from an email link.
                $authenticateRecordResult = AuthenticateObj::createEmailResetDataRecord($updateEmailRequestData, $pBaseURL, TPX_AUTHENTICATIONTYPE_EMAILUPDATE, TPX_USER_AUTH_REASON_EMAIL_UPDATE, $gSession['userid']);

                // Add the AUTHENTICATIONDATASTORE key to the data to be passed via email.
                $emailUpdateRequestStr .= chr(10) . 'authkey=' . $authenticateRecordResult['authkey'];

                // Encrtpt the data for the link in the email.
                $resetEmailData = UtilsObj::encryptData($emailUpdateRequestStr, $systemConfigArray['systemkey'], true);

                // Add the parameters to the URL.
                $resetURL = $pBaseURL . '?fsaction=Welcome.updateEmailRequest&red=' . $resetEmailData;

                // Add the reset url for the link to the data required for the email.
                $emailParamArray['reseturl'] = $resetURL;
            }
            else
            {
                // An account is already using the email, send a email address exists email.
                $emailTemplate .= 'exists';
                $resetEmailData = 'Email in use.';
            }
        }
        else
        {
            // An account is already using the email, send a email address exists email.
            $emailTemplate .= 'exists';
            $resetEmailData = 'Email in use.';
        }

        // Generate the email notification to send to the user.
        $emailObj = new TaopixMailer();
        $emailObj->sendTemplateEmail($emailTemplate, $gSession['webbrandcode'], $pAppName, $pBaseURL, '', $gSession['username'], $pNewEmail, '', '', $gSession['userid'], $emailParamArray, '', '');

        // Log the request in the activity log.
        DatabaseObj::updateActivityLog(0, 0, $gSession['userid'], $gSession['userlogin'], $gSession['username'], 0, 'CUSTOMER', 'UPDATEEMAILREQUEST', $resetEmailData, 1);
    }

    static function updatePassword()
    {
    	global $gConstants;
        global $gSession;

		$resultArray = Array();
        $result = '';
        $isConfirmation = 0;
        $accountUpdated = false;
        $canUpdateAccount = true;
		$currentPassword = '';
		$newPasswordHash = '';

        $origPassword = $_POST['data1'];
        $newPassword = $_POST['data2'];
        $passwordFormat = (int) UtilsObj::getPOSTParam('format', TPX_PASSWORDFORMAT_MD5);

		// get the user account details based on id

		$dbObj = DatabaseObj::getConnection();
		if ($dbObj)
		{
			// select the current password
			if ($stmt = $dbObj->prepare('SELECT `password` FROM `USERS` WHERE `id` = ?'))
			{
				if ($stmt->bind_param('i', $gSession['userid']))
				{
					if ($stmt->execute())
					{
						if ($stmt->bind_result($currentPassword))
						{
							if (! $stmt->fetch())
							{
								$result = 'str_DatabaseError';
								error_log(__FUNCTION__ . ' Unable to fetch results ' . $dbObj->error);
							}
						}
						else
						{
							$result = 'str_DatabaseError';
							error_log(__FUNCTION__ . ' Unable to bind result ' . $dbObj->error);
						}
					}
					else
					{
						$result = 'str_DatabaseError';
						error_log(__FUNCTION__ . ' Unable to execute query ' . $dbObj->error);
					}
				}
				else
				{
					$result = 'str_DatabaseError';
					error_log(__FUNCTION__ . ' Unable to bind param ' . $dbObj->error);
				}
			}
			else
			{
				$result = 'str_DatabaseError';
				error_log(__FUNCTION__ . ' Unable to prepare query ' . $dbObj->error);
			}
		}

		if ($result == '')
		{
			// verify the password is correct
			$verifyPasswordResult = AuthenticateObj::verifyPassword($origPassword, $currentPassword, $passwordFormat);

			$canUpdateAccount = $verifyPasswordResult['data']['passwordvalid'];

			if (! $canUpdateAccount)
			{
				$result = 'str_ErrorNoAccount';
			}

			if ($canUpdateAccount)
			{
				// calculate password hash for the new password based on if the page is secure or not
				$generatePasswordHashResult = AuthenticateObj::generatePasswordHash($newPassword, $passwordFormat);

				if ($generatePasswordHashResult['result'] == '')
				{
					$newPasswordHash = $generatePasswordHashResult['data'];
				}
				else
				{
					$result = $generatePasswordHashResult['result'];
					$canUpdateAccount = false;
				}
			}
		}

		// first attempt to update the password via an external script
		if (($gConstants['optionwscrp']) && (file_exists('../Customise/scripts/EDL_ExternalCustomerAccount.php')))
		{
			require_once('../Customise/scripts/EDL_ExternalCustomerAccount.php');

			if (method_exists('ExternalCustomerAccountObj', 'updatePassword'))
			{
				$userAccountArray = DatabaseObj::getUserAccountFromID($gSession['userid']);

				$paramArray = Array();
				$paramArray['languagecode'] = UtilsObj::getBrowserLocale();
				$paramArray['groupcode'] = $userAccountArray['groupcode'];
				$paramArray['brandcode'] = $gSession['webbrandcode'];
				$paramArray['id'] = $gSession['userid'];
				$paramArray['login'] = $userAccountArray['login'];
				$paramArray['accountcode'] = $userAccountArray['accountcode'];
				$paramArray['passwordformat'] = $passwordFormat;
				$paramArray['origpassword'] = $origPassword;
				$paramArray['newpassword'] = $newPassword;

                $scriptResult = ExternalCustomerAccountObj::updatePassword($paramArray);

                if ($scriptResult === '')
                {
                    $accountUpdated = true;
					$canUpdateAccount = true;

                    $result = '';
                }
                elseif ($scriptResult === 'NOTHANDLED')
                {
                    // the script hasn't handled the update so just continue with the current result
                    //nothing else to do
                }
                else
                {
                    $canUpdateAccount = false;

                    $result = $scriptResult;
                }

			}
		}

		// if we have not updated an external account attempt to update one in the taopix database
		if ($canUpdateAccount)
		{
			$dbObj = DatabaseObj::getConnection();
			if ($dbObj)
			{
				if ($stmt = $dbObj->prepare('UPDATE `USERS` SET `password` = ? WHERE `id` = ? AND `password` = ? AND `modifypassword` = 1'))
				{
					if ($stmt->bind_param('sis', $newPasswordHash, $gSession['userid'], $currentPassword))
					{
						if ($stmt->execute())
						{
							if ($dbObj->affected_rows == 1)
							{
								$accountUpdated = true;
							}
							else
							{
								if (! $accountUpdated)
								{
									if ($dbObj->affected_rows == '-1')
									{
										DatabaseObj::updateActivityLog($gSession['ref'], 0, $gSession['userid'], $gSession['userlogin'], $gSession['username'], 0, 'CUSTOMER', 'UPDATEPASSWORD', 'str_ErrorNoAccount', 0);
									}

									$result = 'str_ErrorNoAccount';
								}
							}
						}
					}

					$stmt->free_result();
					$stmt->close();
				}
        	}

            $dbObj->close();
        }


    	if ($accountUpdated)
    	{
			DatabaseObj::updateActivityLog($gSession['ref'], 0, $gSession['userid'], $gSession['userlogin'], $gSession['username'], 0, 'CUSTOMER', 'UPDATEPASSWORD', '', 1);
			DataExportObj::EventTrigger(TPX_TRIGGER_PASSWORD_RESET, 'CUSTOMER', $gSession['userid'], 0);

    		$isConfirmation = 1;
    		$result = 'str_LabelPasswordUpdated';
    	}

    	$resultArray['isConfirmation'] = $isConfirmation;
    	$resultArray['result'] = $result;

    	return $resultArray;
    }

    static function updatePreferences()
    {
        global $gConstants;
        global $gSession;

        $resultArray = Array();
        $result = '';
        $isConfirmation = 0;
        $canUpdateAccount = true;
        $isExternalAccount = false;

        $sendMarketingInfo = (int) UtilsObj::getPOSTParam('sendmarketinginfo', 1);

		$userAccountArray = DatabaseObj::getUserAccountFromID($gSession['userid']);

		// first attempt to update the preferences via an external script
		if (($gConstants['optionwscrp']) && (file_exists('../Customise/scripts/EDL_ExternalCustomerAccount.php')))
		{
			require_once('../Customise/scripts/EDL_ExternalCustomerAccount.php');

			if (method_exists('ExternalCustomerAccountObj', 'updateAccountPrefs'))
			{
				$isExternalAccount = true;

				$paramArray = Array();
				$paramArray['languagecode'] = UtilsObj::getBrowserLocale();
				$paramArray['groupcode'] = $userAccountArray['groupcode'];
				$paramArray['brandcode'] = $gSession['webbrandcode'];
				$paramArray['id'] = $gSession['userid'];
				$paramArray['login'] = $userAccountArray['login'];
				$paramArray['accountcode'] = $userAccountArray['accountcode'];
				$paramArray['sendmarketinginfo'] = $sendMarketingInfo;

				$result = ExternalCustomerAccountObj::updateAccountPrefs($paramArray);

				if ($result == '')
				{
					$result = 'str_LabelPreferencesUpdated';
					$isConfirmation = 1;
				}
				else
				{
					$canUpdateAccount = false;
				}
			}
		}

		// unless we have received an error while updating an external account we always want to update the taopix account
		if ($canUpdateAccount)
		{
			$dbObj = DatabaseObj::getConnection();
			if ($dbObj)
			{
				$sql = 'UPDATE `USERS` SET `sendmarketinginfo` = ? ';

				// only update the marking opt in date if it is set on and the current DB value is off
				if (((int)$sendMarketingInfo == 1) && ($userAccountArray['sendmarketinginfo'] == 0))
				{
					$sql .= ', `sendmarketinginfooptindate` = NOW()';
				}

				$sql .= ' WHERE `id` = ?';

				if ($stmt = $dbObj->prepare($sql))
				{
					if ($stmt->bind_param('ii', $sendMarketingInfo, $gSession['userid']))
					{
						if ($stmt->execute())
						{
							if ($dbObj->affected_rows == 1)
							{
								DatabaseObj::updateActivityLog($gSession['ref'], 0, $gSession['userid'], $gSession['userlogin'], $gSession['username'], 0, 'CUSTOMER', 'UPDATEPREFERENCES', $sendMarketingInfo . " " . $gSession['userid'], 1);

								DataExportObj::EventTrigger(TPX_TRIGGER_CUSTOMER_EDIT, 'CUSTOMER', $gSession['userid'], 0);
							}

							// only set the result if this is not an external account
							if (! $isExternalAccount)
							{
								$result = 'str_LabelPreferencesUpdated';
								$isConfirmation = 1;
							}
						}
					}

					$stmt->free_result();
					$stmt->close();
				}
			}

			$dbObj->close();
		}

        $resultArray['isConfirmation'] = $isConfirmation;
    	$resultArray['result'] = $result;

    	return $resultArray;
    }

    static function showPreview()
    {
        // ref2 should be an uploadref for the preview and id is a orderitem id
        $uploadRef = (isset($_GET['ref2'])) ? $_GET['ref2'] : '';
        $orderItemId = (isset($_GET['id'])) ? $_GET['id'] : 0;

        return Share_model::showPreview('CUSTOMER', $uploadRef, $orderItemId);
    }

    static function getOnlineProjectStatusList($pUserID)
    {
        $resultArray = UtilsObj::getReturnArray();
		$id = -1;
		$status = -1;
		$canModify = 0;
		$canUpload = 0;
		$projectRef = 0;
		$orderID = -1;
		$uploadRef = 0;
		$active = 0;

        $dbObj = DatabaseObj::getGlobalDBConnection();
        if ($dbObj)
        {
			$sql = 'SELECT `id`, `status`, `canmodify`, `canupload`, `projectref`, `orderid`, `uploadref`, `active`
					FROM `ORDERITEMS`
					WHERE `source` = 1
						AND userid = ? ORDER BY `id` DESC';
			$stmt = $dbObj->prepare($sql);

            if ($stmt)
            {
                if ($stmt->bind_param('i', $pUserID))
                {
                    if ($stmt->bind_result($id, $status, $canModify, $canUpload, $projectRef, $orderID, $uploadRef, $active))
                    {
                        if ($stmt->execute())
                        {
                            while($stmt->fetch())
                            {
								$projectRow = Array();

								// if the order has been marked as complete in production we want to set it to complete in the UI to match the your orders page and designer
								if ($active !== TPX_ORDER_STATUS_COMPLETED)
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
								}
								else
								{
									$status = 5; // displays as complete
								}

								$projectRow['id'] = $id;
                                $projectRow['status'] = $status;
                                $projectRow['canmodify'] = $canModify;
                                $projectRow['canupload'] = $canUpload;
                                $projectRow['projectref'] = $projectRef;
                                $projectRow['orderid'] = $orderID;
                                $projectRow['uploadref'] = $uploadRef;
                                $projectRow['orderstatus'] = $active;

                                $resultArray['data'][] = $projectRow;
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
            $dbObj->close();
        }
		else
		{
			$resultArray['error'] = 'str_DatabaseError';
			$resultArray['errorparam'] = 'getUserProjectStatusList connection unable';
		}
        return $resultArray;
    }

    static function getOnlineProjectList($pUserID, $pServerToServerCall = false)
    {
        global $ac_config;
        global $gConstants;

        $resultArray = array();
        $systemConfigDataArray = DatabaseObj::getSystemConfig();
		$error = '';
		$errorParam = '';
		$projectArray = array();
        $inMaintenanceMode = false;
		$hasPurgeableProjects = false;

		if ($systemConfigDataArray['result'] == '')
		{
			$orderedProjectStatusList = self::getOnlineProjectStatusList($pUserID);

			if ($orderedProjectStatusList['error'] == '')
			{
				$dataToEncrypt = array(
					'cmd' => 'GETPROJECTLIST',
					'data' => array(
						'userid' => $pUserID,
						'defaultlanguagecode' => UtilsObj::getBrowserLocale(),
						'hourOffset' => LocalizationObj::getBrowserHourOffset()
					)
				);

				$projectListDataArray = CurlObj::sendByPut($ac_config['TAOPIXONLINEURL'], 'ProjectAPI.callback', $dataToEncrypt);

				if ($projectListDataArray['error'] === '')
				{
					if ($projectListDataArray['data']['error'] == '')
					{
						if ($projectListDataArray['data']['result'] != TPX_ONLINE_ERROR_MAINTENANCEMODE)
						{

							$projectList = $orderedProjectStatusList['data'];
							$projectCount = count($projectList);

							// get the browser compatibility to disable some none accessible action
							$browserArray = OnlineAPI_model::checkBrowsers();

							// process the project list for localization
							foreach ($projectListDataArray['data']['projects'] as &$project)
							{
								// Set the flag for purgable projects to true if we have one or more projects due to be purged, only do this once.
								if ('' !== $project['dateofpurge'] && false === $hasPurgeableProjects)
								{
									$hasPurgeableProjects = true;
								}

								$project['productname'] = LocalizationObj::getLocaleString($project['productname'], '', true);
								$project['collectionname'] = LocalizationObj::getLocaleString($project['collectionname'], '', true);
								$project['candelete'] = 1;

								if ($browserArray['browsersupported'] == 1 || $pServerToServerCall)
								{
									$project['canedit'] = 1;
								}
								else
								{
									$project['canedit'] = 0;
									$project['cancompleteorder'] = 0;
								}

								if ($projectCount > 0)
								{
									for ($i = 0; $i < $projectCount; $i++)
									{
										// sale order exist & we have an record in ORDERITEMS
										if ($project['projectref'] == $projectList[$i]['projectref'])
										{
											switch ($projectList[$i]['status'])
											{
												case 0:
												case 1:
												case 3:
												{
													$project['statusdescription'] = 'str_LabelStatusInProduction';
													break;
												}
												case 2:
												{
													$project['statusdescription'] = 'str_LabelStatusShipped';
													break;
												}
												case 4:
												{
													$project['statusdescription'] = 'str_LabelStatusReadyToCollectAtStore';
													break;
												}
												case 5:
												{
													$project['statusdescription'] = 'str_LabelStatusCompleted';
													break;
												}
											}

											if ($project['canedit'])
											{
												$project['canedit'] = $projectList[$i]['canmodify'];
											}

											$project['candelete'] = 0;
											$project['cancompleteorder'] = 0;
											break;
										}
									}
								}

							}

							$projectArray = $projectListDataArray['data']['projects'];
						}
						else
						{
							$inMaintenanceMode = true;
						}
					}
					else
					{
						$error = $projectListDataArray['data']['error'];
						$errorParam = $projectListDataArray['data']['errorparam'];
					}
				}
				else
				{
					$error = $projectListDataArray['error'];
					$errorParam = $projectListDataArray['error'];
				}
			}
			else
			{
				$error = $orderedProjectStatusList['error'];
				$errorParam = $orderedProjectStatusList['errorparam'];
			}
		}
		else
		{
			$error = $systemConfigDataArray['result'];
			$errorParam = $systemConfigDataArray['resultparam'];
		}

		$resultArray['error'] = $error;
		$resultArray['errorparam'] = $errorParam;
		$resultArray['projects'] = $projectArray;
        $resultArray['maintenancemode'] = $inMaintenanceMode;
        $resultArray['purgableprojects'] = $hasPurgeableProjects;

        return $resultArray;
    }

	/**
	 * getOnlineWizardModeList
	 *
	 * Contacts the online server and retrieves the wizard mode for online projects
	 *
	 * @param array $pProjectRefsArray An array of projectrefs to be sorted and then the online project refs sent to the online server
	 * @return array Associative Array containing error, errorparam and a list of projects and their wizard status in an array using the projectref as the array key
	 */
	static function getOnlineWizardModeList($pProjectRefsArray)
	{
		global $ac_config;

		//initialise variables and arrays
		$error = '';
		$errorParam = '';
		$returnArray = array();
		$projectRefsToSend = array();

		//build array of project refs to send to online
		foreach ($pProjectRefsArray as $theRef)
		{
			//source 1 is for online projects so it only adds projects if their source is online
			if ($theRef['source'] == 1)
			{
				$projectRefsToSend[] = $theRef['projectref'];
			}
		}

		//create data to send to online
		$dataToEncrypt = array(
					'cmd' => 'GETONLINEWIZARDMODE',
					'data' => array(
						'projectrefsarray' => $projectRefsToSend
					)
			);

		$projectWizardModeArray = CurlObj::sendByPut($ac_config['TAOPIXONLINEURL'], 'ProjectAPI.callback', $dataToEncrypt);

		//check for errors and assign if applicable
		if ($projectWizardModeArray['error'] == '')
		{
			if ($projectWizardModeArray['data']['error'] == '')
			{
				$returnArray['projects'] = $projectWizardModeArray['data']['projects'];
			}
			else
			{
				$error = $projectWizardModeArray['data']['error'];
				$errorParam = $projectWizardModeArray['data']['error'];
			}
		}
		else
		{
			$error = $projectWizardModeArray['error'];
			$errorParam = $projectWizardModeArray['error'];
		}

		$returnArray['error'] = $error;
		$returnArray['errorparam'] = $errorParam;

		return $returnArray;
	}
    
    /**
     * Contacts the online server and retrieves the thumbnail paths for the online projects
     * 
     * @param array $pProjectRefsArray An array of projectrefs to be sorted and then the online project refs sent to the online server
	 * @return array Associative Array containing error, errorparam and a list of projects and their wizard status in an array using the projectref as the array key
     */
    static function getOnlineProjectThumbnails($pProjectRefsArray)
    {
		$returnArray = [
            'error' => '',
            'errorparam' => '',
            'data' => []
        ];
		$projectRefsToSend = [];

		// Build the array of project refs to send to online.
		foreach ($pProjectRefsArray as $theProjectData)
		{
			// Only add online project refs.
			if (TPX_SOURCE_ONLINE === $theProjectData['source'])
			{
				$projectRefsToSend[] = $theProjectData['projectref'];
			}
		}

		// Create the data to send to online.
		$dataToEncrypt = [
            'displaymode' => 1,
            'projectreflist' => $projectRefsToSend
		];
		
		$getProjectThumbnailAPIPathResult = UtilsObj::getProjectThumbnailAPIPutParams('displayThumbnail', $dataToEncrypt);

		$apiResult = CurlObj::put($getProjectThumbnailAPIPathResult['url'], $getProjectThumbnailAPIPathResult['params'], TPX_CURL_RETRY, TPX_CURL_TIMEOUT);

        if ('' === $apiResult['error'])
        {
            // Decode the data.
            $thumbnailData = json_decode($apiResult['data'], true);

            // Extract the thumbnail paths. Use array_walk to maintain the array keys (projectref).
            array_walk($thumbnailData, function(&$a) {
                return $a = $a['thumbnail']; 
            });

            $returnArray['data'] = $thumbnailData;
        }
        else
        {
            $returnArray['error'] = $apiResult['error'];
            $returnArray['errorparam'] = $apiResult['errorparam'];
        }

        return $returnArray;
    }

    static function deleteOrder($pOrderId, $pConfigDetails, $pUserId)
	{
		$canDelete = true;
		$isReorder = false;
		$deleteFailedReason = '';
		$linkedReorderItems = [];
		$items = [];

		$dbObj = DatabaseObj::getGlobalDBConnection();

		// If we did not get a database object bail out early.
		if (! $dbObj)
		{
			return [
				'status' => false,
				'reasonCode' => 'NODBCONNECTION',
			];
		}

		// Get order items.
		$orderDetails = self::getOrderDetailsForDelete($pOrderId, $pUserId, $dbObj);

		if ('' === $orderDetails['error'])
		{
			// We are only a redorder if the origorderid is not 0.
			$isReorder = (0 !== $orderDetails['data']['origorderid']);
			$items = self::getOrderLineItems('orderid', $pOrderId, $dbObj);

			if ('' === $items['error'])
			{
				foreach ($items['data'] as $key => $itemDetails)
				{
					$canDelete = ($itemDetails['active'] >= 1);

					// If we find an item that is not complete break out of this loop, no point in processing further.
					if (false === $canDelete)
					{
						$deleteFailedReason = 'NOTCOMPLETE';
						break;
					}

					if (! $isReorder)
					{
						$reorderInformation = self::checkItemCompleteStateInReorders($itemDetails['uploaditemid'], $dbObj);
						$canDelete = $reorderInformation['canDelete'];

						if (! $canDelete)
						{
							$deleteFailedReason = 'OPENREORDER';
							// Exit out of the loop
							break;
						}
						else
						{
							$linkedReorderItems = array_merge($linkedReorderItems, $reorderInformation['linkedItems']);
						}
					}
				}
			}
			else
			{
				$canDelete = false;
			}
		}
		else
		{
			$canDelete = false;
			$deleteFailedReason = $orderDetails['errorparam'];
		}

		if ($canDelete)
		{
			// Perform the order redaction in a transaction.
			if ($dbObj->begin_transaction())
			{
				$redactionResult = self::redactOrder($pOrderId, $items['data'], $isReorder, $linkedReorderItems, $dbObj, $pConfigDetails);

				if ($redactionResult['deleted'] && $dbObj->commit())
				{
					$canDelete = $redactionResult['deleted'];
				}
				else
				{
					$dbObj->rollback();
					$deleteFailedReason = $redactionResult['failedReason'];
				}
			}
		}

		return [
			'status' => $canDelete,
			'reasonCode' => $deleteFailedReason,
		];
	}

	static function getOrderDetailsForDelete($pOrderId, $pUserId, $pDbObj)
	{
		$returnArray = [
			'error' => '',
			'errorparam' => '',
			'data' => []
		];

		$orderId = -1;
		$origOrderId = -1;

		$query = "SELECT `oh`.`id`, `oh`.`origorderid` FROM `ORDERHEADER` AS `oh` WHERE `oh`.`id` = ? AND `oh`.`userid` = ?";

		if ($stmt = $pDbObj->prepare($query))
		{
			if ($stmt->bind_param('ii', $pOrderId, $pUserId))
			{
				if ($stmt->bind_result($orderId, $origOrderId))
				{
					if ($stmt->execute())
					{
						while ($stmt->fetch())
						{
							$returnArray['data'] = [
								'orderid' => $orderId,
								'origorderid' => $origOrderId,
							];
						}

						if (empty($returnArray['data']))
						{
							$returnArray['error'] = 'str_SomeError';
							$returnArray['errorparam'] = 'No Project Found';
						}
					}
					else
					{
						$returnArray['error'] = 'str_DatabaseError';
						$returnArray['errorparam'] = __METHOD__ . ' execute: ' . $pDbObj->error;
					}
				}
				else
				{
					$returnArray['error'] = 'str_DatabaseError';
					$returnArray['errorparam'] = __METHOD__ . ' bind result: ' . $pDbObj->error;
				}
			}
			else
			{
				$returnArray['error'] = 'str_DatabaseError';
				$returnArray['errorparam'] = __METHOD__ . ' bind param: ' . $pDbObj->error;
			}
		}
		else
		{
			$returnArray['error'] = 'str_DatabaseError';
			$returnArray['errorparam'] = __METHOD__ . ' prepare: ' . $pDbObj->error;
		}

		return $returnArray;
	}

	static function getOrderLineItems($pColName, $pQueryId, $pDbObj)
	{
		$returnArray = [
			'error' => '',
			'errorparam' => '',
			'data' => []
		];

		$itemId = -1;
		$uploadItemId = -1;
		$origItemId = -1;
		$userId = -1;
		$source = -1;
		$status = -1;
		$active = -1;
		$orderId = -1;
		$companyCode = '';
		$origCompanyCode = '';
		$ownerCode = '';
		$origOwnerCode = '';
		$projectRef = '';
		$uploadRef = '';
		$dateCreated = '';
		$uploadGroupCode = '';

		$query = 'SELECT `oi`.`id`, `oi`.`uploadorderitemid`, `oi`.`origorderitemid`, `oi`.`userid`, `oi`.`currentcompanycode`, `oi`.`origcompanycode`, ' .
					'`oi`.`currentowner`, `oi`.`origowner`, `oi`.`projectref`, `oi`.`uploadref`, `oi`.`source`, `oi`.`status`, `oi`.`active`, `oi`.`orderid`, `oi`.`datecreated`, `oi`.`uploadgroupcode` ' .
					'FROM `ORDERITEMS` AS `oi` WHERE ';
		if ('orderid' === $pColName)
		{
			$query .= '`oi`.`orderid` = ?';
		}
		else
		{
			$query .= '`oi`.`uploadorderitemid` = ? AND `oi`.`origorderitemid` != 0';
		}

		if ($stmt = $pDbObj->prepare($query))
		{
			if ($stmt->bind_param('i', $pQueryId))
			{
				if ($stmt->bind_result($itemId, $uploadItemId, $origItemId, $userId, $companyCode, $origCompanyCode,
					$ownerCode, $origOwnerCode, $projectRef, $uploadRef, $source, $status, $active, $orderId, $dateCreated, $uploadGroupCode))
				{
					if ($stmt->execute())
					{
						while ($stmt->fetch())
						{
							$returnArray['data'][] = [
								'itemid' => $itemId,
								'uploaditemid' => $uploadItemId,
								'origitemid' => $origItemId,
								'userid' => $userId,
								'projectref' => $projectRef,
								'uploadref' => $uploadRef,
								'source' => $source,
								'companycode' => ($companyCode === $origCompanyCode ? $origCompanyCode : $companyCode),
								'ownercode' => ($ownerCode === $origOwnerCode ? $origOwnerCode : $ownerCode),
								'status' => $status,
								'active' => $active,
								'orderid' => $orderId,
								'datecreated' => strtotime($dateCreated),
								'groupcode' => $uploadGroupCode,
							];
						}
					}
					else
					{
						$returnArray['error'] = 'str_DatabaseError';
						$returnArray['errorparam'] = __METHOD__ . ' execute: ' . $pDbObj->error;
					}
				}
				else
				{
					$returnArray['error'] = 'str_DatabaseError';
					$returnArray['errorparam'] = __METHOD__ . ' bind result: ' . $pDbObj->error;
				}
			}
			else
			{
				$returnArray['error'] = 'str_DatabaseError';
				$returnArray['errorparam'] = __METHOD__ . ' bind param: ' . $pDbObj->error;
			}
		}
		else
		{
			$returnArray['error'] = 'str_DatabaseError';
			$returnArray['errorparam'] = __METHOD__ . ' prepare: ' . $pDbObj->error;
		}

		return $returnArray;
	}

	static function checkItemCompleteStateInReorders($pItemId, $pDbObj)
	{
		$returnStatus = true;
		$reorderItems = self::getOrderLineItems('uploadorderitemid', $pItemId, $pDbObj);

		if ('' === $reorderItems['error'])
		{
			if (! empty($reorderItems['data']))
			{
				$orderStatus = array_unique(array_column($reorderItems['data'], 'active'));
				// Check if 0 is in the orderStatus and invert this so we are checking that it is not in there.
				$returnStatus = !in_array(0, $orderStatus);
			}
		}
		else
		{
			// We got an error so bail out.
			$returnStatus = false;
		}

		return [
			'canDelete' => $returnStatus,
			'linkedItems' => $reorderItems['data']
		];
	}

	static function redactOrder($pOrderId, $pItems, $pIsReorder, $pLinkedItems, $pDbObj, $pConfigDetails)
	{
		$return = [
			'deleted' => true,
			'failedReason' => '',
		];

		try
		{
			$itemIds = array_column($pItems, 'itemid');

			// If we have linked projects set the reorder state to be 0 for any linked items, as we know we are removing the parent item.
			if (! empty($pLinkedItems))
			{
				$itemIds = array_merge($itemIds, array_column($pLinkedItems, 'itemid'));
			}

			DataRedactionAPI_model::setOrderDeletionReorderState($itemIds, $pDbObj);

			if (! $pIsReorder)
			{
				// Filter out online projects/desktop projects into arrays.
				$onlineProjects = array_filter($pItems, function($item) { return TPX_SOURCE_ONLINE === $item['source']; });
				$designerProjects = array_column(array_filter($pItems, function($item) { return TPX_SOURCE_DESKTOP === $item['source']; }), 'projectref');

				// Send details for online projects to be purged.
				if (! empty($onlineProjects))
				{
					DataRedactionAPI_model::queueOrderDeletionPurgeRedaction($onlineProjects);
				}

				// Remove desktop thumbnails.
				if (! empty($designerProjects))
				{
					$designerRemoval = UtilsObj::deleteDesktopProjectThumbnails($designerProjects);

					if ('' !== $designerRemoval['error'])
					{
						throw new Exception($designerRemoval['errorparam']);
					}
				}

				// Remove any local files for items.
				DataRedactionAPI_model::orderDeletionLocalFileRedaction($pItems, $pConfigDetails['ac_config'], $pDbObj);

				// Queue production events
				DataRedactionAPI_model::queueOrderDeletionProductionEvents($pItems, $pDbObj);
			}

			// Redact the order information.
			DataRedactionAPI_model::redactOrderDeletionInformation($pOrderId, $pDbObj);

		}
		catch (Exception $ex)
		{
			$return['deleted'] = false;
			$return['failedReason'] = $ex->getMessage();
		}

		return $return;
	}

	public static function getProjectsFlaggedForPurgeState($pUserId, $ac_config)
	{
		$hasFlaggedForPurge = false;

		//create data to send to online
		$dataToEncrypt = array(
			'cmd' => 'USERHASFLAGGEDPROJECTS',
			'data' => array(
				'userid' => $pUserId
			)
		);

		try {
			$request = CurlObj::sendByPut($ac_config['TAOPIXONLINEURL'], 'ProjectAPI.callback', $dataToEncrypt);
			
			//check for errors and assign if applicable
			if ($request['error'] == '')
			{
				if ($request['data']['error'] == '')
				{
					$hasFlaggedForPurge = $request['data']['flagged'];
				}
				else
				{
					throw new Exception($request['data']['errorparam'], TPX_ONLINE_ERROR_COMMUNICATION_FAILED);
				}
			}
			else
			{
				throw new Exception($request['errorparam'], TPX_ONLINE_ERROR_COMMUNICATION_FAILED);
			}
		}
		catch (Exception $ex)
		{
			error_log($ex->getCode() . ': ' . $ex->getMessage());
		}

		return $hasFlaggedForPurge;
	}
}
?>
