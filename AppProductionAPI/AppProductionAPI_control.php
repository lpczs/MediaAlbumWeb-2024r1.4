<?php

require_once('../AppProductionAPI/AppProductionAPI_model.php');
require_once('../AppProductionAPI/AppProductionAPI_view.php');

class AppProductionAPI_control
{
    /**
   	* Entry point for all production api commands
   	*
   	* The command is provided in the POST parameters cmd and cmd2
   	* These are then decrypted to obtain the plain text command name
 	*
   	* @author Kevin Gale
   	* @version 3.0.0
	* @since Version 1.0.0
 	*/
 	static function callback()
	{
	    if (array_key_exists('cmd', $_POST) && (array_key_exists('cmd2', $_POST)))
	    {
	        $cmd = $_POST['cmd'];
	        $cmd2 = $_POST['cmd2'];
	        $key = UtilsObj::getPOSTParam('key');

	        $decryptResultArray = AppProductionAPI_model::decryptCommand($cmd, $cmd2, $key);

	        if ($decryptResultArray['sessionactive'] == true)
	        {
                switch ($decryptResultArray['cmd'])
                {
                    case 'LOGIN':
                        $resultArray = AppProductionAPI_model::login($decryptResultArray);
                        AppProductionAPI_view::login($resultArray);
                        break;
                    case 'LOGOUT':
                        AppProductionAPI_model::logout();
                        AppProductionAPI_view::logout();
                        break;
                    case 'SYSTEMCONFIG':
                    	$webURL = AppProductionAPI_model::systemConfig();
                        AppProductionAPI_view::systemConfig($webURL);
                        break;
                    case 'PRODUCTIONSITES':
                        $resultArray = AppProductionAPI_model::getProductionSites();
                        AppProductionAPI_view::getProductionSites($resultArray);
                        break;
                    case 'PRODUCTIONQUEUE':
                        $resultArray = AppProductionAPI_model::getProductionQueue();
                        AppProductionAPI_view::getProductionQueue($resultArray);
                        break;
                    case 'OUTPUTFORMATS':
                        $resultArray = AppProductionAPI_model::getOutputFormats();
                        AppProductionAPI_view::getOutputFormats($resultArray);
                        break;
                    case 'UPDATEORDERPAYMENTSTATUS':
                        AppProductionAPI_model::updateOrderPaymentStatusPOST();
                        AppProductionAPI_view::updateOrderPaymentStatus();
                        break;
                    case 'UPDATEORDERACTIVESTATUS':
                        AppProductionAPI_model::updateOrderActiveStatusPOST();
                        AppProductionAPI_view::updateOrderActiveStatus();
                        break;
                    case 'UPDATEITEMACTIVESTATUS':
                        AppProductionAPI_model::updateItemActiveStatusPOST();
                        AppProductionAPI_view::updateItemActiveStatus();
                        break;
                    case 'UPDATEITEMFILESRECEIVEDSTATUS':
                        AppProductionAPI_model::updateItemFilesReceivedStatusPOST();
                        AppProductionAPI_view::updateItemFilesReceivedStatus();
						break;
					case 'UPDATEITEMDECRYPTQUEUESTATUS':
						$result = AppProductionAPI_model::updateItemDecryptQueueStatusPOST();
						AppProductionAPI_view::updateItemDecryptQueueStatus($result);
						break;
                    case 'UPDATEITEMDECRYPTSTATUS':
                        AppProductionAPI_model::updateItemDecryptStatusPOST();
                        AppProductionAPI_view::updateItemDecryptStatus();
                        break;
                    case 'UPDATEITEMCONVERTSTATUS':
                        AppProductionAPI_model::updateItemConvertStatusPOST();
                        AppProductionAPI_view::updateItemConvertStatus();
                        break;
                    case 'UPDATEITEMSTATUS':
                        AppProductionAPI_model::updateItemStatusPOST();
                        AppProductionAPI_view::updateItemStatus();
                        break;
                    case 'UPDATEITEMONHOLDSTATUS':
                        $resultArray = AppProductionAPI_model::updateItemOnHoldStatusPOST();
                        AppProductionAPI_view::updateItemOnHoldStatus($resultArray);
                        break;
                    case 'UPDATEITEMOUTPUTSTATUS':
                        $resultArray = AppProductionAPI_model::updateItemOutputStatusPOST();
                        AppProductionAPI_view::updateItemOutputStatus($resultArray);
                        break;
                    case 'UPDATEITEMFINISHINGSTATUS':
                        AppProductionAPI_model::updateItemFinishingStatusPOST();
                        AppProductionAPI_view::updateItemFinishingStatus();
                        break;
                    case 'UPDATEITEMSHIPPINGSTATUS':
                        AppProductionAPI_model::updateItemShippingStatusPOST();
                        AppProductionAPI_view::updateItemShippingStatus();
                        break;
                    case 'UPDATEITEMCANMODIFYSTATUS':
                        AppProductionAPI_model::updateItemCanModifyStatusPOST();
                        AppProductionAPI_view::updateItemCanModifyStatus();
                        break;
                    case 'UPDATEITEMCANUPLOADFILESSTATUS':
                        AppProductionAPI_model::updateItemCanUploadFilesStatusPOST();
                        AppProductionAPI_view::updateItemCanUploadFilesStatus();
                        break;
                    case 'UPDATEITEMCANUPLOADFILESOVERRIDEPRODUCTCODESTATUS':
                        AppProductionAPI_model::updateItemCanUploadFilesOverrideProductCodeStatusPOST();
                        AppProductionAPI_view::updateItemCanUploadFilesOverrideProductCodeStatus();
                        break;
                    case 'UPDATEITEMCANUPLOADFILESOVERRIDEPAGECOUNTSTATUS':
                        AppProductionAPI_model::updateItemCanUploadFilesOverridePageCountStatusPOST();
                        AppProductionAPI_view::updateItemCanUploadFilesOverridePageCountStatus();
                        break;
                    case 'UPDATEITEMCANUPLOADFILESOVERRIDESAVESTATUS':
                        AppProductionAPI_model::updateItemCanUploadFilesOverrideSaveStatusPOST();
                        AppProductionAPI_view::updateItemCanUploadFilesOverrideSaveStatus();
                        break;
                    case 'UPDATEITEMIMPORTSTATUS':
                        AppProductionAPI_model::updateItemImportStatus();
                        AppProductionAPI_view::updateItemImportStatus();
                        break;
                    case 'ITEMACTIONLIST':
                    	$resultArray = AppProductionAPI_model::performItemActionListPOST();
                        AppProductionAPI_view::performItemActionList($resultArray);
                        break;
                    case 'REROUTEITEMS':
                        $resultArray = AppProductionAPI_model::reRouteItems();
                        AppProductionAPI_view::reRouteItems($resultArray);
                        break;
                    case 'ORDERSTATUSLIST':
                        $resultArray = AppProductionAPI_model::getOrderStatusList();
                        AppProductionAPI_view::getOrderStatusList($resultArray);
                        break;
                    case 'ORDERSTATUSDATA':
                        $resultArray = AppProductionAPI_model::getOrderStatusData();
                        AppProductionAPI_view::getOrderStatusData($resultArray);
                        break;
                    case 'JOBINFO':
                        $resultArray = AppProductionAPI_model::getJobInfo();
                        AppProductionAPI_view::getJobInfo($resultArray);
                        break;
                    case 'OUTPUTDEVICES':
                        $resultArray = AppProductionAPI_model::getOutputDevices();
                        AppProductionAPI_view::getOutputDevices($resultArray);
                        break;
                    case 'OUTPUTDEVICEADD':
                        $resultArray = AppProductionAPI_model::outputDeviceAdd();
                        AppProductionAPI_view::outputDeviceAdd($resultArray);
                        break;
                    case 'OUTPUTDEVICEEDIT':
                        $resultArray = AppProductionAPI_model::outputDeviceEdit();
                        AppProductionAPI_view::outputDeviceEdit($resultArray);
                        break;
                    case 'OUTPUTDEVICEDELETE':
                        $resultArray = AppProductionAPI_model::outputDeviceDelete();
                        AppProductionAPI_view::outputDeviceDelete($resultArray);
                        break;
                    case 'OUTPUTFORMATADD':
                        $resultArray = AppProductionAPI_model::outputFormatAdd();
                        AppProductionAPI_view::outputFormatAdd($resultArray);
                        break;
                    case 'OUTPUTFORMATEDIT':
                        $resultArray = AppProductionAPI_model::outputFormatEdit();
                        AppProductionAPI_view::outputFormatEdit($resultArray);
                        break;
                    case 'OUTPUTFORMATDELETE':
                        $resultArray = AppProductionAPI_model::outputFormatDelete();
                        AppProductionAPI_view::outputFormatDelete($resultArray);
                        break;
                    case 'PRODUCTS':
                        $resultArray = AppProductionAPI_model::getProductsList();
                        AppProductionAPI_view::getProductsList($resultArray);
                        break;
                    case 'COMPONENTS':
                        $resultArray = AppProductionAPI_model::getComponentsList();
                        AppProductionAPI_view::getComponentsList($resultArray);
                        break;
                    case 'GETROW':
                        $resultArray = AppProductionAPI_model::getRow();
                        AppProductionAPI_view::getRow($resultArray);
                        break;
                    case 'FINDOFFLINEORDER':
                        $resultArray = AppProductionAPI_model::findOfflineOrder();
                        AppProductionAPI_view::findOfflineOrder($resultArray);
                        break;
                    case 'CREATEOFFLINEORDER':
                        $resultArray = AppProductionAPI_model::createOfflineOrder();
                        AppProductionAPI_view::createOfflineOrder($resultArray);
                        break;
                    case 'BRANDS':
                        $resultArray = AppProductionAPI_model::getBrands();
                        AppProductionAPI_view::getBrands($resultArray);
                        break;
					case 'GETPRODUCTIONEVENTS':
						$resultArray = AppProductionAPI_model::getProductionEvents();
						AppProductionAPI_view::getProductionEvents($resultArray);
						break;
					case 'UPDATEPRODUCTIONEVENTSTATUS':
						$resultArray = AppProductionAPI_model::updateProductionEventStatus();
						AppProductionAPI_view::updateProductionEventStatus($resultArray);
						break;
                }
            }
            else
            {
                AppProductionAPI_view::sessionExpired();
            }
	    }
	    else
	    {
	        AppProductionAPI_view::unknownCommand();
	    }
	}

	static function epwHPJDFCallback()
	{
	    AppProductionAPI_model::epwHPJDFCallback();
	}
	
	static function epwHPPrintOSCallback()
	{
		AppProductionAPI_model::epwHPPrintOSCallback();
	}
}

?>