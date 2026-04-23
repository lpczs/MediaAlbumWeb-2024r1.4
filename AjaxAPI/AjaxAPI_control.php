<?php

use Security\RequestValidationTrait;

require_once(__DIR__.'/../AjaxAPI/AjaxAPI_model.php');
require_once(__DIR__.'/../AjaxAPI/AjaxAPI_view.php');

class AjaxAPI_control
{
	use RequestValidationTrait;

	static function getAddressForm()
	{
		$resultArray = AjaxAPI_model::extJsAddressForm(true);
		AjaxAPI_view::extJsAddressForm($resultArray);
	}

	static function verifyAddress()
	{
		AjaxAPI_model::extJsAddressVerification();
	}

	static function getRegionList(){
		$resultArray = AjaxAPI_model::ExtJsShippingRegion();
		AjaxAPI_view::ExtJsShippingRegion($resultArray);
	}

	static function emailTest()
	{
		$result = AjaxAPI_model::emailTest();
	    AjaxAPI_view::emailTestJson($result);
	}

	static function callback()
	{
	    if (array_key_exists('cmd', $_REQUEST))
	    {
	        switch ($_REQUEST['cmd'])
	        {
	            case 'ADDRESSFORM':
					self::assertRequestMethod(['GET']);

					$resultArray = AjaxAPI_model::addressForm();
					AjaxAPI_view::addressForm($resultArray);
					break;
	            case 'ADDRESSVERIFICATION':
					$result = AjaxAPI_model::addressVerification();
	                AjaxAPI_view::addressVerification($result);
	                break;
	            case 'AUTOSUGGEST':
					$result = AjaxAPI_model::autoSuggest();
	                AjaxAPI_view::autoSuggest($result);
	                break;
	            case 'EMAILTEST':
					self::assertRequestMethod(['POST']);
					self::assertCsrfToken();

					$result = AjaxAPI_model::emailTest();
	                AjaxAPI_view::emailTest($result);
	                break;
				case 'PRODSITESCOMBO':
					self::assertRequestMethod(['GET']);

					$result = AjaxAPI_model::productionSitesComboStore();
	                AjaxAPI_view::comboDataStore($result);
	                break;
	            case 'COMPANIESCOMBO':
					self::assertRequestMethod(['GET']);

					$result = AjaxAPI_model::companiesComboStore();
	                AjaxAPI_view::comboDataStore($result);
	                break;
				case 'STORESCOMBO':
					self::assertRequestMethod(['GET']);

	                $result = AjaxAPI_model::storesComboStore();
	                AjaxAPI_view::comboDataStore($result);
	                break;
	            case 'BRANDCOMBO':
					self::assertRequestMethod(['GET']);

					$result = AjaxAPI_model::brandComboStore();
	                AjaxAPI_view::comboDataStore($result);
	                break;
	            case 'LICENSECOMBO':
					self::assertRequestMethod(['GET']);

					$result = AjaxAPI_model::licenseComboStore();
	                AjaxAPI_view::licenseDataStore($result);
	                break;
				case 'COUNTRYCOMBO':
					self::assertRequestMethod(['GET']);

					$result = AjaxAPI_model::countryComboStore();
					AjaxAPI_view::countryDataStore($result);
					break;
	            case 'PRODUCTCOLLECTIONCOMBO':
					self::assertRequestMethod(['GET']);

					$result = AjaxAPI_model::productCollectionComboStore();
	                AjaxAPI_view::productCollectionDataStore($result);
	                break;
	            case 'COUNTRYPANEL':
					self::assertRequestMethod(['GET']);

					AjaxAPI_model::countryPanel();
	                break;
	            case 'STORELOCATOR':
					self::assertRequestMethod(['GET']);

	                $result = AjaxAPI_model::storeLocator();
	                AjaxAPI_view::storeLocator($result);
	                break;
	            case 'STORELOCATOREXTERNAL':
					self::assertRequestMethod(['GET']);

	                $result = AjaxAPI_model::storeLocatorExternal();
	                AjaxAPI_view::storeLocatorExternal($result);
	                break;
	            case 'STOREINFORMATION':
					self::assertRequestMethod(['GET']);

	                $result = AjaxAPI_model::storeInformation();
	                AjaxAPI_view::storeInformation($result);
	                break;
	            case 'STOREINFORMATIONEXTERNAL':
					self::assertRequestMethod(['GET']);

	                $result = AjaxAPI_model::storeInformationExternal();
	                AjaxAPI_view::storeInformation($result);
	                break;
	            case 'COMPANIESLICENSEKEYS':
	                $result = AjaxAPI_model::getCompaniesLicensekeys();
	                AjaxAPI_view::getCompaniesLicensekeys($result);
	                break;
	            case 'COMPANIESPRODUCTS':
	                $result = AjaxAPI_model::getCompaniesProducts();
	                AjaxAPI_view::getProductList($result);
	                break;
	            case 'COMPANIESLICENSEKEYSANDPRODUCTS':
	                $resultLicenseKeys = AjaxAPI_model::getCompaniesLicensekeys();
	                $resultProducts = AjaxAPI_model::getCompaniesProducts();
	                AjaxAPI_view::getCompaniesLicensekeysAndProducts($resultLicenseKeys, $resultProducts);
	                break;
				case 'PRICELISTS':
					self::assertRequestMethod(['GET']);

					$result = AjaxAPI_model::getPriceLists();
	            	AjaxAPI_view::getPriceLists($result);
	             	break;
				case 'UPDATEQTYLARGE':
					self::assertRequestMethod(['POST']);
					self::assertCsrfToken();

					$result = AjaxAPI_model::updateQty();
					AjaxAPI_view::updateOrderLineLarge($result);
					break;
				case 'UPDATECOMPONENTQTYLARGE':
					self::assertRequestMethod(['POST']);
					self::assertCsrfToken();

					AjaxAPI_model::updateComponentQty();
					$result = AjaxAPI_model::updateQty();
					AjaxAPI_view::updateOrderLineLarge($result);
					break;
                case 'UPDATEORDERQTYALLLARGE':
					self::assertRequestMethod(['POST']);
					self::assertCsrfToken();

                    $result = AjaxAPI_model::updateQtyAll();
                    AjaxAPI_view::updateOrderLineAllLarge($result);
					break;
	            case 'CHANGECOMPONENTLARGE':
					self::assertRequestMethod(['GET']);

	                $result = AjaxAPI_model::changeComponent();
	                AjaxAPI_view::changeComponentLarge($result);
	                break;
	            case 'UPDATECOMPONENTLARGE':
					self::assertRequestMethod(['POST']);
					self::assertCsrfToken();

	                $result = AjaxAPI_model::updateComponent();
	                AjaxAPI_view::updateOrderLineLarge($result);
	                break;
	            case 'UPDATECHECKBOXLARGE':
					self::assertRequestMethod(['POST']);
					self::assertCsrfToken();

	                $result = AjaxAPI_model::updateCheckbox();
	                AjaxAPI_view::updateOrderLineLarge($result);
	                break;
	            case 'COMPONETCATEGORYLIST':
					self::assertRequestMethod(['GET']);

					$result = AjaxAPI_model::getComponentCategories();
	                AjaxAPI_view::getComponentCategories($result);
	                break;
				case 'UPDATEORDERSUMMARY':
					self::assertRequestMethod(['POST']);
					self::assertCsrfToken();

	                $result = AjaxAPI_model::updateOrderSummaryLarge();
	                AjaxAPI_view::updateOrderSummary($result);
	                break;
	            case 'SAVETEMPMETADATA':
					self::assertRequestMethod(['POST']);
					self::assertCsrfToken();

	                $result = AjaxAPI_model::saveTempMetadata();
	                break;
	            case 'CFSCHANGESHIPPINGMETHOD':
					self::assertRequestMethod(['POST']);
					self::assertCsrfToken();

	                $result = AjaxAPI_model::cfsChangeShippingMethod();
	                AjaxAPI_view::updateCollectFromStoreItemSubTotal($result);
	                break;
	            case 'GETTAXCODELIST':
					self::assertRequestMethod(['GET']);

					$result = AjaxAPI_model::getTaxCodeList();
	            	AjaxAPI_view::getTaxCodeList($result);
	             	break;
				case 'TERMSANDCONDITIONS':
					self::assertRequestMethod(['GET']);

					$result = AjaxAPI_model::getTermsAndConditions();
	            	AjaxAPI_view::getTermsAndConditions($result);
	             	break;
                case 'CHANGEBILLINGADDRESSDISPLAY':
					self::assertRequestMethod(['POST']);
					self::assertCsrfToken();

                    $resultArray = AjaxAPI_model::changeBillingAddressDisplay();
					AjaxAPI_view::changeBillingAddressDisplay($resultArray);
                    break;
                case 'CHANGEBILLINGADDRESS':
					self::assertRequestMethod(['POST']);
					self::assertCsrfToken();

                    $resultArray = AjaxAPI_model::changeBillingAddress();
                    AjaxAPI_view::changeAddressRefresh($resultArray);
                    break;
                case 'CHANGESHIPPINGMETHOD':
					self::assertRequestMethod(['POST']);
					self::assertCsrfToken();

                    $resultArray = AjaxAPI_model::changeShippingMethod();
                    if ($resultArray['forcechangeaddressdisplay'])
                    {
                        $resultArray = AjaxAPI_model::changeShippingAddressDisplay();
                        AjaxAPI_view::changeShippingAddressDisplay($resultArray);
                    }
                    else
                    {
                        AjaxAPI_view::changeShippingMethod($resultArray);
                    }
                    break;
                case 'CHANGESHIPPINGADDRESSDISPLAY':
					self::assertRequestMethod(['POST']);
					self::assertCsrfToken();

                    $resultArray = AjaxAPI_model::changeShippingAddressDisplay();
					$resultArray['shippingcfscontact'] =  UtilsObj::getPOSTParam('shippingcfscontact',0);
					AjaxAPI_view::changeShippingAddressDisplay($resultArray);
                    break;
                case 'CHANGESHIPPINGADDRESS':
					self::assertRequestMethod(['POST']);
					self::assertCsrfToken();

                    $resultArray = AjaxAPI_model::changeShippingAddress();
                    AjaxAPI_view::changeAddressRefresh($resultArray);
                    break;
				case 'UPDATEACCOUNTDETAILS':
					self::assertRequestMethod(['POST']);
					self::assertCsrfToken();

                    $resultArray = AjaxAPI_model::updateAccountDetails();
                    AjaxAPI_view::orderContinueAjax($resultArray);
                    break;
                case 'COPYSHIPPINGADDRESS':
					self::assertRequestMethod(['POST']);
					self::assertCsrfToken();

                    $resultArray = AjaxAPI_model::copyShippingAddress();
                    AjaxAPI_view::changeAddressRefresh($resultArray);
                    break;
                case 'CHANGEADDRESSCANCEL':
					self::assertRequestMethod(['POST']);
					self::assertCsrfToken();

                    $resultArray = AjaxAPI_model::changeAddressCancel();
                    AjaxAPI_view::changeAddressRefresh($resultArray);
                    break;
                case 'SELECTSTOREDISPLAY':
					self::assertRequestMethod(['POST']);
					self::assertCsrfToken();

                    $resultArray = AjaxAPI_model::selectStoreDisplay();
                    AjaxAPI_view::selectStoreDisplay($resultArray);
                    break;
                case 'SELECTSTORE':
					self::assertRequestMethod(['POST']);
					self::assertCsrfToken();

                    $resultArray = AjaxAPI_model::selectStore();
                    AjaxAPI_view::selectStore($resultArray);
                    break;
                case 'ORDERCONTINUE':
					self::assertRequestMethod(['POST']);
					self::assertCsrfToken();

                    $resultArray = AjaxAPI_model::orderContinue();
                    AjaxAPI_view::orderContinueAjax($resultArray);
                    break;
                case 'ORDERBACK':
					self::assertRequestMethod(['POST']);
					self::assertCsrfToken();

                    $resultArray = AjaxAPI_model::orderBack();
                    AjaxAPI_view::orderContinueAjax($resultArray);
                    break;
                case 'ORDERCANCEL':
					self::assertRequestMethod(['POST']);
					self::assertCsrfToken();

                    $mainWebSiteURL = AjaxAPI_model::orderCancel();
                    AjaxAPI_view::orderCancel($mainWebSiteURL);
                    break;
                case 'UPDATEQTYSMALL':
					self::assertRequestMethod(['POST']);
					self::assertCsrfToken();

					$result = AjaxAPI_model::updateQty();
                    $orderTotal = AjaxAPI_model::updateOrderSummarySmall();
					AjaxAPI_view::updateOrderLineSmall($result, true, $orderTotal);
					break;
                case 'UPDATEORDERQTYALLSMALL':
					self::assertRequestMethod(['POST']);
					self::assertCsrfToken();

                    $result = AjaxAPI_model::updateQtyAll();
                    $orderTotal = AjaxAPI_model::updateOrderSummarySmall();
                    AjaxAPI_view::updateOrderLineAllSmall($result, $orderTotal);
					break;
                case 'CHANGECOMPONENTSMALL':
					self::assertRequestMethod(['GET']);

	                $result = AjaxAPI_model::changeComponent();
	                AjaxAPI_view::changeComponentSmall($result);
	                break;
                case 'UPDATECOMPONENTSMALL':
					self::assertRequestMethod(['POST']);
					self::assertCsrfToken();

	                $result = AjaxAPI_model::updateComponent();
                    $orderTotal = AjaxAPI_model::updateOrderSummarySmall();
	                AjaxAPI_view::updateOrderLineSmall($result, true, $orderTotal);
	                break;
                case 'UPDATECOMPONENTQTYSMALL':
					self::assertRequestMethod(['POST']);
					self::assertCsrfToken();

					AjaxAPI_model::updateComponentQty();
					$result = AjaxAPI_model::updateQty();
                    $orderTotal = AjaxAPI_model::updateOrderSummarySmall();
					AjaxAPI_view::updateOrderLineSmall($result, true, $orderTotal);
					break;
                 case 'UPDATECHECKBOXSMALL':
					 self::assertRequestMethod(['POST']);
					 self::assertCsrfToken();

	                $result = AjaxAPI_model::updateCheckbox();
                    $orderTotal = AjaxAPI_model::updateOrderSummarySmall();
	                AjaxAPI_view::updateOrderLineSmall($result, true, $orderTotal);
	                break;
                case 'SETGIFTCARD':
					self::assertRequestMethod(['POST']);
					self::assertCsrfToken();

                    $result = AjaxAPI_model::setGiftCard();
	                AjaxAPI_view::orderContinueAjax($result);
                    break;
                case 'CHANGEGIFTCARD':
					self::assertRequestMethod(['POST']);
					self::assertCsrfToken();

                    $result = AjaxAPI_model::changeGiftCard();
                    AjaxAPI_view::changeGiftCard($result);
                    break;
                case 'SETVOUCHER':
					self::assertRequestMethod(['POST']);
					self::assertCsrfToken();

                    $result = AjaxAPI_model::setVoucher();
	                AjaxAPI_view::orderContinueAjax($result);
                    break;
                case 'UPDATECOMPANIONQTY':
					self::assertRequestMethod(['POST']);
					self::assertCsrfToken();

                    $result = AjaxAPI_model::updateCompanionQty();
	                AjaxAPI_view::updateCompanionQty($result);
                    break;
				case 'DUPLICATEONLINEPROJECT':
					self::assertRequestMethod(['POST']);
					self::assertCsrfToken();

					$isHighLevel = UtilsObj::getGETParam('ishighlevel', 0);

					if ($isHighLevel)
					{
						require_once('../OnlineAPI/OnlineAPI_control.php');

						$_POST['projectref'] = UtilsObj::cleanseInput(UtilsObj::getGETParam('projectref', ''), true);
						$_POST['mawebhlbr'] = UtilsObj::cleanseInput(UtilsObj::getGETParam('mawebhlbr', ''), true);
						$_POST['projectname'] = UtilsObj::cleanseInput(UtilsObj::getGETParam('projectname', ''), false);
						$_POST['browserutc'] = UtilsObj::cleanseInput(UtilsObj::getGETParam('dummy', '0'), true);
						$_POST['productident'] = UtilsObj::cleanseInput(UtilsObj::getGETParam('productident', '0'), true);

						// If coming from Control Centre the language code won't be passed, so get it from UtilsObj::getBrowserLocale().
						$_POST['browserlocale'] = UtilsObj::cleanseLanguageCode(UtilsObj::getPOSTParam('browserlocale'), UtilsObj::getBrowserLocale());

						OnlineAPI_control::highLevelDuplicateProject(false);
					}
					else
					{
						if (AuthenticateObj::WebSessionActive() == 1)
						{
							require_once('../OnlineAPI/OnlineAPI_model.php');

							$paramArray = array();
							$paramArray['browserlanguagecode'] = UtilsObj::getBrowserLocale();
							$paramArray['projectref'] = UtilsObj::cleanseInput(UtilsObj::getGETParam('projectref', ''), true);
							$paramArray['projectname'] = UtilsObj::cleanseInput(UtilsObj::getGETParam('projectname', ''), false);
							$paramArray['tzoffset'] = UtilsObj::cleanseInput(UtilsObj::getGETParam('tzoffset', '0'), true);
							$paramArray['minlife'] = 0;
							$paramArray['canunlock'] = 0;
							$paramArray['ccnotificationsenabled'] = false;
							$paramArray['basketapiworkflowtype'] = TPX_BASKETWORKFLOWTYPE_NORMAL;
							$paramArray['cmd'] = 'DUPLICATEPROJECT';

							$result = OnlineAPI_model::duplicateRenameOnlineProject($paramArray);
							AjaxAPI_view::duplicateOnlineProject($result);
						}
						else
						{
							echo json_encode(array('error' => 'str_ErrorSessionExpired'));
						}
					}
                    break;
                case 'RENAMEONLINEPROJECT':
					self::assertRequestMethod(['POST']);
					self::assertCsrfToken();

					if (AuthenticateObj::WebSessionActive() == 1)
					{
						require_once('../OnlineAPI/OnlineAPI_model.php');

						$paramArray = array();
						$paramArray['browserlanguagecode'] = UtilsObj::getBrowserLocale();
						$paramArray['projectref'] = UtilsObj::cleanseInput(UtilsObj::getGETParam('projectref', ''), true);
						$paramArray['projectname'] = UtilsObj::cleanseInput(UtilsObj::getGETParam('projectname', ''), false);
						$paramArray['canunlock'] = 0;
						$paramArray['ccnotificationsenabled'] = false;
						$paramArray['basketapiworkflowtype'] = TPX_BASKETWORKFLOWTYPE_NORMAL;
						$paramArray['cmd'] = 'RENAMEPROJECT';

						// project was restored, allow action
						$result = OnlineAPI_model::duplicateRenameOnlineProject($paramArray);
						AjaxAPI_view::renameExistingOnlineProject($result);
					}
					else
					{
						echo json_encode(array('error' => 'str_ErrorSessionExpired'));
					}
                    break;
                case 'OPENONLINEPROJECT':
	            case 'COMPLETEORDER':
					self::assertRequestMethod(['POST']);
					self::assertCsrfToken();

        			$isHighLevel =  UtilsObj::getGETParam('ishighlevel', 0);

                	if ($isHighLevel)
                	{
						require_once('../OnlineAPI/OnlineAPI_control.php');

						$_POST['projectref'] = UtilsObj::getGETParam('projectref', '');
						$_POST['mawebhlbr'] = UtilsObj::getGETParam('mawebhlbr', TPX_ONLINE_BASKETAPI_DEFAULTBASKETREF);

						OnlineAPI_control::highLevelEditProject();
                	}
                	else
                	{
						if (AuthenticateObj::WebSessionActive() == 1)
						{
							require_once('../OnlineAPI/OnlineAPI_model.php');

							$paramArray = array();
                            $paramArray['languagecode'] = UtilsObj::getBrowserLocale();
                            $paramArray['defaultlanguagecode'] = UtilsObj::getBrowserLocale();
                            $paramArray['projectref'] = UtilsObj::getGETParam('projectref', '');
							$paramArray['workflowtype'] = UtilsObj::getGETParam('workflowtype', '');
							$paramArray['canshareproject'] = true;
							$paramArray['basketref'] = '';
							$paramArray['ccnotificationsenabled'] = false;
							$paramArray['forcekill'] = 1;

							// project was restored, allow action
							$result = OnlineAPI_model::openOnlineProject(TPX_OPEN_MODE_EXISTING_PROJECT, $paramArray, array(), false, ($_REQUEST['cmd'] == 'COMPLETEORDER'));
							AjaxAPI_view::openOnlineProject($result);
						}
						else
						{
							echo json_encode(array('errorparam' => 'str_ErrorSessionExpired'));
						}
                	}

	            	break;
                case 'CHECKDELETESESSION':
                	self::assertRequestMethod(['POST']);
                	self::assertCsrfToken();

					if (AuthenticateObj::WebSessionActive() == 1)
					{
						require_once('../OnlineAPI/OnlineAPI_model.php');

						$paramArray = array();
						$paramArray['projectreflist'] = array(UtilsObj::cleanseInput(UtilsObj::getGETParam('projectref', ''), true));
						$paramArray['forcekill'] = UtilsObj::cleanseInput(UtilsObj::getGETParam('forcekill', 0), true);
						$paramArray['purgedays'] = 0;
						$paramArray['canunlock'] = 0;
						$paramArray['action'] = UtilsObj::cleanseInput(UtilsObj::getGETParam('action', 0), true);
						$paramArray['basketref'] = '';

						$result = OnlineAPI_model::checkDeleteSession($paramArray);
						AjaxAPI_view::checkDeleteSession($result);
					}
					else
					{
						AjaxAPI_view::checkDeleteSession(array('error' => 'str_ErrorSessionExpired'));
					}
					break;
				/**
				 * This end point is used to check if the order had been inserted into
				 * the CCILOG table if it has we know it is safe to redirect to the manual
				 */
				case 'QUERYCCITABLE':
					self::assertRequestMethod(['POST']);
					self::assertCsrfToken();

					$sessionRef = $_POST['cciref'];

					$result = AjaxAPI_model::getCCIRecord($sessionRef);

					AjaxAPI_view::getCCIRecord($result);

					break;
				case 'PROCESSPAYMENTTOKEN':
					self::assertRequestMethod(['POST']);

					$resultArray = array();
					$paymentToken = UtilsObj::getPOSTParam('token', '');
					$CCIType = UtilsObj::getPOSTParam('ccitype', '');

					// if we do not have a token then check the POST body
					// payments via the paymentRequest API send the token in the body.
					if ($paymentToken == '')
					{
						$entityBody = file_get_contents('php://input');
						$dataArray = json_decode($entityBody, true);
						$paymentToken = $dataArray['token'];
						$CCIType = $dataArray['ccitype'];
					}

					$processTokenResultArray = AjaxAPI_model::processPaymentToken($CCIType, $paymentToken);
					$resultArray['error'] = $processTokenResultArray['error'];
					$resultArray['errormessage'] = $processTokenResultArray['errormessage'];
					$resultArray['redirecturl'] = $processTokenResultArray['redirecturl'];

					// if we have succeeded processing the token then we must populate POST variables with the response variables from the gateway.
           			// this is so we can call the CCAutomaticCallback function to mimic a normal server to server workflow.
					if ($resultArray['error'] == '')
					{
						require_once('../Order/Order_control.php');
						$_POST = $processTokenResultArray['data'];

						Order_control::ccAutomaticCallback();
					}

					AjaxAPI_view::processPaymentTokenResponse($resultArray);

					break;
                case 'CHECKLOGINUNIQUE':
                    // Check that the potential login update is not already in use.
                    self::assertRequestMethod(['POST']);

			        // Get the new login from the POST.
                    $loginToCheck = UtilsObj::cleanseInput(UtilsObj::getPOSTParam('login', ''), true);

					if ($loginToCheck != '')
					{
                        // Check the value is not already in use.
						$resultArray = AjaxAPI_model::processUserLoginUniqueCheck($loginToCheck);
					}
					else
					{
                        // Value was empty, return an error.
						$resultArray = array('result' => 'str_MessageCompulsoryEmaiInvalid', 'resultparam' => '');
                    }

                    AjaxAPI_view::processUserLoginUniqueCheck($resultArray);

					break;
				case 'GETSHAREONLINEPROJECTURL':
					self::assertRequestMethod(['GET']);
					require_once('../Share/Share_model.php');

					$paramArray = [];
					$paramArray['projectref'] = UtilsObj::cleanseInput(UtilsObj::getGETParam('projectref', ''));
					$getShareURLResult = Share_model::getShareOnlineProjectURL($paramArray);
					AjaxAPI_view::getShareOnlineProjectURL($getShareURLResult);
					break;
				case 'KEEPONLINEPROJECT':
					self::assertRequestMethod(['POST']);
					global $ac_config;
					$projectRef = UtilsObj::cleanseInput(UtilsObj::getGETParam('projectref', ''));
					$keepResult = AjaxAPI_Model::keepOnlineProject($projectRef, $ac_config);
					AjaxAPI_view::returnJSON($keepResult);
					break;
				case 'PURGEFLAGGEDPROJECTS':
					self::assertRequestMethod(['POST']);
					global $ac_config;
					global $gSession;
					$purgeResult = AjaxAPI_Model::purgeFlaggedProjects($gSession['userid'], $ac_config);
					AjaxAPI_view::returnJSON($purgeResult);
					break;
	        	default:
                    AjaxAPI_view::unknownCommand();
				break;
	        }
	    }
	    else
	    {
	        AjaxAPI_view::unknownCommand();
	    }
	}
}

?>
