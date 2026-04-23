<?php

require_once('../AppAPI/AppAPI_model.php');
require_once('../AppAPI/AppAPI_view.php');

class AppAPI_control
{
	static function callback()
	{
	    if (array_key_exists('cmd', $_POST))
	    {
	        switch ($_POST['cmd'])
	        {
	            case 'ORDER':
                    $orderDataResult = AppAPI_model::prepareOrderData();

                    $resultArray = AppAPI_model::order($orderDataResult);

                    AppAPI_view::order($resultArray);

	                break;
	            case 'ORDERCONFIRM':
	                $resultArray = AppAPI_model::orderConfirm();
	                AppAPI_view::order($resultArray);

                    break;
                case 'ORDERSESSIONCANCEL':
                    $resultArray = AppAPI_model::cancelOrderSessionPOST();
	                AppAPI_view::order($resultArray);

                    break;
                case 'UPLOADCOMPLETED':
	                $resultArray = AppAPI_model::uploadCompleted();
	                AppAPI_view::uploadCompleted($resultArray);

                    break;
	            case 'TESTCONNECTION':
	                AppAPI_view::testConnection();

                    break;
	            case 'GETLICENSEKEY':
	                $resultArray = AppAPI_model::getLicenseKey();
	                AppAPI_view::getLicenseKey($resultArray);

                    break;
	            case 'SYSTEMUPDATE':
	                $resultArray = AppAPI_model::systemUpdate();
	                AppAPI_view::systemUpdate($resultArray);

                    break;
                case 'GETDYNAMICGRAPHICS':
                    $resultArray = AppAPI_model::getDynamicGraphics();
	                AppAPI_view::getDynamicGraphics($resultArray);

                    break;
                case 'UPLOADPRODUCTCATEGORIESINIT':
                	// note. for uploading product categories we are only interested in authenticating the creator
                	$resultArray = AppAPI_model::uploadSystemDataInit();
                	$resultArray['type'] = -1;
	                AppAPI_view::uploadSystemDataInit($resultArray);

                    break;
                case 'UPLOADPRODUCTCATEGORIES':
                	$resultArray = AppAPI_model::uploadProductCategories();
	                AppAPI_view::uploadSystemUpdate($resultArray);

                    break;
                case 'UPLOADPRODUCTSINIT':
                	$resultArray = AppAPI_model::uploadSystemDataInit();
                	$resultArray['type'] = TPX_APPLICATION_FILE_TYPE_PRODUCTCOLLECTION;
	                AppAPI_view::uploadSystemDataInit($resultArray);

                    break;
                case 'UPLOADPRODUCTSUPDATE':
                	$resultArray = AppAPI_model::uploadProductsUpdate();
	                AppAPI_view::uploadSystemUpdate($resultArray);

                    break;
                case 'UPLOADMASKSINIT':
                    $resultArray = AppAPI_model::uploadSystemDataInit();
                	$resultArray['type'] = TPX_APPLICATION_FILE_TYPE_MASK;
	                AppAPI_view::uploadSystemDataInit($resultArray);

                    break;
                case 'UPLOADMASKSUPDATE':
                	$resultArray = AppAPI_model::uploadMasksUpdate();
	                AppAPI_view::uploadSystemUpdate($resultArray);

                    break;
                case 'UPLOADBACKGROUNDSINIT':
                    $resultArray = AppAPI_model::uploadSystemDataInit();
                	$resultArray['type'] = TPX_APPLICATION_FILE_TYPE_BACKGROUND;
	                AppAPI_view::uploadSystemDataInit($resultArray);

                    break;
                case 'UPLOADBACKGROUNDSUPDATE':
                	$resultArray = AppAPI_model::uploadBackgroundsUpdate();
	                AppAPI_view::uploadSystemUpdate($resultArray);

                    break;
                case 'UPLOADSCRAPBOOKINIT':
                    $resultArray = AppAPI_model::uploadSystemDataInit();
                	$resultArray['type'] = TPX_APPLICATION_FILE_TYPE_PICTURE;
	                AppAPI_view::uploadSystemDataInit($resultArray);

                    break;
                case 'UPLOADSCRAPBOOKUPDATE':
                	$resultArray = AppAPI_model::uploadScrapbookUpdate();
	                AppAPI_view::uploadSystemUpdate($resultArray);

                    break;
				case 'UPLOADFRAMESINIT':
				    $resultArray = AppAPI_model::uploadSystemDataInit();
                	$resultArray['type'] = TPX_APPLICATION_FILE_TYPE_FRAME;
	                AppAPI_view::uploadSystemDataInit($resultArray);

                    break;
                case 'UPLOADFRAMESUPDATE':
                	$resultArray = AppAPI_model::uploadFramesUpdate();
	                AppAPI_view::uploadSystemUpdate($resultArray);

                    break;
                case 'UPLOADLICENSEKEYSINIT':
                    $resultArray = AppAPI_model::uploadSystemDataInit();
                	$resultArray['type'] = TPX_APPLICATION_FILE_TYPE_LICENSEKEY;
	                AppAPI_view::uploadSystemDataInit($resultArray);

                    break;
                case 'UPLOADLICENSEKEYSUPDATE':
                	$resultArray = AppAPI_model::uploadLicenseKeysUpdate();
	                AppAPI_view::uploadSystemUpdate($resultArray);

                    break;
                case 'UPLOADCLIENTSINIT':
                    $resultArray = AppAPI_model::uploadSystemDataInit();
                	$resultArray['type'] = TPX_APPLICATION_FILE_TYPE_APPLICATION_BUILD;
	                AppAPI_view::uploadSystemDataInit($resultArray);

                    break;
                case 'UPLOADCLIENTSUPDATE':
                	$resultArray = AppAPI_model::uploadClientsUpdate();
	                AppAPI_view::uploadSystemUpdate($resultArray);

                    break;
                 case 'UPLOADCALENDARDATAINIT':
                    // note. for uploading calendar data we are only interested in authenticating the creator
                	$resultArray = AppAPI_model::uploadSystemDataInit();
                	$resultArray['type'] = -1;
	                AppAPI_view::uploadSystemDataInit($resultArray);

                    break;
                case 'UPLOADCALENDARDATA':
                	$resultArray = AppAPI_model::uploadCalendarData();
	                AppAPI_view::uploadSystemUpdate($resultArray);

                    break;
				case 'UPLOADPROJECTTHUMBNAILSINIT':
					$resultArray = AppAPI_model::uploadProjectThumbnailsInit();
					AppAPI_view::uploadProjectThumbnailsInit($resultArray);
					break;
				case 'UPLOADPROJECTTHUMBNAILS':
					$resultArray = AppAPI_model::uploadProjectThumbnails();
					AppAPI_view::uploadProjectThumbnails($resultArray);
					break;
				case 'GETACCOUNTPAGESURL':
					$groupCode = UtilsObj::getPOSTParam('groupcode');
					$result = AppAPI_model::getAccountPagesURL($groupCode);
					AppAPI_view::getAccountPagesURL($result);

					break;
				case 'GETASSETSERVICEREQUESTINFORMATION':
					$OAuthVersion = UtilsObj::getPOSTParam('oauthversion', '');
					$resultArray = AppAPI_model::getAssetServiceRequestInformation($OAuthVersion);
					AppAPI_view::getAssetServiceRequestInformation($resultArray);

					break;
				case 'GETASSETSERVICEAUTHCODE':
					$stateCode = UtilsObj::getPOSTParam('state', '');
					$resultArray = AppAPI_model::getAssetServiceAuthCode($stateCode);
					AppAPI_view::getAssetServiceAuthCode($resultArray);
					
					break;
	            default:
                    AppAPI_view::unknownCommand();

                    break;
	        }
	    }
	    else
	    {
	        AppAPI_view::unknownCommand();
	    }
	}
}

?>