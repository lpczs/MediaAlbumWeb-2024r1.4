<?php

class AdminProduction_view
{
   
    static function initialize($pResultArray) 
    {
        global $gConstants;

        $defaultOwner = $pResultArray['userowner'];

        $smarty = SmartyObj::newSmarty('AdminProduction');
        $smarty->assign('optioncfs', ($gConstants['optioncfs'] ? true : false));
        $smarty->assign('optionms', ($gConstants['optionms'] ? 'true' : 'false'));

        $productionSiteList = array();
        $itemList = array();
        if (isset($pResultArray['productionsites'])) 
        {
            $itemList = $pResultArray['productionsites'];
            array_unshift($itemList, Array('code' => '', 'name' => $smarty->get_config_vars('str_LabelProductionSitesUnallocated')));
            array_unshift($itemList, Array('code' => '**ALL**', 'name' => $smarty->get_config_vars('str_ShowAll')));
        }
        $itemCount = count($itemList);
        $siteCompany = array();

        for ($i = 0; $i < $itemCount; $i++)
        {
        	$productionSiteCode = $itemList[$i]['code'];
        	if (isset($itemList[$i]['companyCode']))
        	{
        		$siteCompany[] = '"' . $productionSiteCode . '":' . '"' . $itemList[$i]['companyCode'] . '"';
        	}
        	$productionSiteList[] = array('id' => $productionSiteCode, 'name' => UtilsObj::encodeString($itemList[$i]['name']));
        }
        $siteCompany = '{' . join(',', $siteCompany) . '}';

        $orderStatusList = self::getOrderFilterStatuses($smarty);
        $statusList = array();

        if (isset($pResultArray['orderstatuslist'])) 
        {
            $statusList = $pResultArray['orderstatuslist'];
            
        }
        $statusCount = count($statusList);

        for ($i = 0; $i < $statusCount; $i++)
        {
        	$statusID = $statusList[$i]['id'];
            $statusName = $statusList[$i]['name'];
        	$orderStatusList[] = array('id' => $statusID, 'name' => UtilsObj::encodeString($statusName));
        }

        $smarty->assign('productionsites', $productionSiteList);
        $smarty->assign('defaultowner', $defaultOwner);
        $smarty->assign('statuslist', $orderStatusList);
        $smarty->assign('languagecode',UtilsObj::getBrowserLocale());
        $smarty->assign('gridpagesize',TPX_PRODUCTIONINCC_GRIDPAGESIZE);

        $prefData = 0;
        $prefDataArray = $pResultArray['prefdata']['data'];

        if (count($prefDataArray) > 0) 
        {
            $prefData = $prefDataArray[0]['data'];
        }

        $smarty->assign('prefdata', $prefData);

        //constants
        $smarty->assign('TPX_ORDER_STATUS_IN_PROGRESS', TPX_ORDER_STATUS_IN_PROGRESS);
        $smarty->assign('TPX_ORDER_STATUS_CANCELLED', TPX_ORDER_STATUS_CANCELLED);
        $smarty->assign('TPX_ORDER_STATUS_COMPLETED', TPX_ORDER_STATUS_COMPLETED);
        $smarty->assign('TPX_ORDER_STATUS_CONVERTED', TPX_ORDER_STATUS_CONVERTED);
        $smarty->assign('TPX_ITEM_STATUS_PRINTED', TPX_ITEM_STATUS_PRINTED);
		$smarty->assign('TPX_ITEM_STATUS_FINISHING_COMPLETE', TPX_ITEM_STATUS_FINISHING_COMPLETE);
		$smarty->assign('TPX_ITEM_STATUS_SHIPPED_TO_CUSTOMER', TPX_ITEM_STATUS_SHIPPED_TO_CUSTOMER);
		$smarty->assign('TPX_ITEM_STATUS_SHIPPED_TO_DISTRIBUTION_CENTRE', TPX_ITEM_STATUS_SHIPPED_TO_DISTRIBUTION_CENTRE);
		$smarty->assign('TPX_ITEM_STATUS_SHIPPED_RECEIVED_AT_DISTRIBUTION_CENTRE', TPX_ITEM_STATUS_SHIPPED_RECEIVED_AT_DISTRIBUTION_CENTRE);
		$smarty->assign('TPX_ITEM_STATUS_SHIPPED_TO_STORE_FROM_DISTRIBUTION_CENTRE', TPX_ITEM_STATUS_SHIPPED_TO_STORE_FROM_DISTRIBUTION_CENTRE);
		$smarty->assign('TPX_ITEM_STATUS_SHIPPED_TO_STORE_DIRECTLY', TPX_ITEM_STATUS_SHIPPED_TO_STORE_DIRECTLY);
		$smarty->assign('TPX_ITEM_STATUS_SHIPPED_RECEIVED_AT_STORE', TPX_ITEM_STATUS_SHIPPED_RECEIVED_AT_STORE);
		$smarty->assign('TPX_ITEM_STATUS_SHIPPED_COLLECTED_BY_CUSTOMER', TPX_ITEM_STATUS_SHIPPED_COLLECTED_BY_CUSTOMER);
        $smarty->assign('TPX_ITEM_STATUS_DOWNLOAD_FILES_FTP_ERROR',TPX_ITEM_STATUS_DOWNLOAD_FILES_FTP_ERROR);
        $smarty->assign('TPX_ITEM_STATUS_IMPORT_FILES_ERROR',TPX_ITEM_STATUS_IMPORT_FILES_ERROR);
        $smarty->assign('TPX_ITEM_STATUS_DECRYPTING_FILES_ERROR',TPX_ITEM_STATUS_DECRYPTING_FILES_ERROR);
        $smarty->assign('TPX_ITEM_STATUS_RAW_FILES_RENDER_ERROR',TPX_ITEM_STATUS_RAW_FILES_RENDER_ERROR);
        $smarty->assign('TPX_ITEM_STATUS_CONVERTING_FILES_ERROR',TPX_ITEM_STATUS_CONVERTING_FILES_ERROR);
        $smarty->assign('TPX_ITEM_STATUS_PRINTING_FILES_ERROR',TPX_ITEM_STATUS_PRINTING_FILES_ERROR);

        $smarty->assign('dateformat', LocalizationObj::getLocaleFormatValue('str_DateFormat'));

        $smarty->displayLocale('admin/production/list.tpl');
    }

    static function getOrderFilterStatuses($pSmarty)
	{
		$statusArray = [];

		$statusArray = [
                    ['id' => '', 'name' => $pSmarty->get_config_vars('str_LabelAllActiveOrders')],
					['id' => TPX_ITEM_STATUS_AWAITING_FILES, 'name' => self::getStatusText($pSmarty, TPX_ITEM_STATUS_AWAITING_FILES, '')],
					['id' => TPX_ITEM_STATUS_FILES_ON_REMOTE_FTP_SERVER . ',' . TPX_ITEM_STATUS_DOWNLOAD_FILES_FTP_ERROR, 'name' => $pSmarty->get_config_vars('str_QueueFilterReadyToDownload')],		
					['id' => TPX_ITEM_STATUS_FILES_RECEIVED . ',' . TPX_ITEM_STATUS_DECRYPTING_FILES_ERROR, 'name' => $pSmarty->get_config_vars('str_QueueFilterReadyToDecrypt')],	
					['id' => TPX_ITEM_STATUS_DECRYPTED_FILES . ',' . TPX_ITEM_STATUS_CONVERTING_FILES_ERROR, 'name' => $pSmarty->get_config_vars('str_QueueFilterReadyToConvert')],
					['id' => TPX_ITEM_STATUS_READY_TO_PRINT . ',' . TPX_ITEM_STATUS_PRINTING_FILES_ERROR, 'name' => $pSmarty->get_config_vars('str_QueueFilterReadyToPrint')],
                    ['id' => TPX_ITEM_STATUS_PRINTING_SENT_TO_EXTERNAL_WORKFLOW, 'name' => self::getStatusText($pSmarty, TPX_ITEM_STATUS_PRINTING_SENT_TO_EXTERNAL_WORKFLOW, '')],
                    ['id' => TPX_ITEM_STATUS_PRINTED, 'name' => $pSmarty->get_config_vars('str_QueueFilterReadyToFinish')],
                    ['id' => TPX_ITEM_STATUS_FINISHING_COMPLETE, 'name' => $pSmarty->get_config_vars('str_QueueFilterReadyToShip')],
                    ['id' =>   TPX_ITEM_STATUS_SHIPPED_TO_CUSTOMER
                        . ',' .TPX_ITEM_STATUS_SHIPPED_TO_DISTRIBUTION_CENTRE
                        . ',' .TPX_ITEM_STATUS_SHIPPED_RECEIVED_AT_DISTRIBUTION_CENTRE
                        . ',' .TPX_ITEM_STATUS_SHIPPED_TO_STORE_FROM_DISTRIBUTION_CENTRE
                        . ',' .TPX_ITEM_STATUS_SHIPPED_TO_STORE_DIRECTLY
                        . ',' .TPX_ITEM_STATUS_SHIPPED_RECEIVED_AT_STORE
                        . ',' .TPX_ITEM_STATUS_SHIPPED_COLLECTED_BY_CUSTOMER, 'name' => $pSmarty->get_config_vars('str_QueueFilterReadyToComplete')],
                    ['id' => '100', 'name' => $pSmarty->get_config_vars('str_LabelProductionOnHold')],
                    ['id' => '200', 'name' => $pSmarty->get_config_vars('str_QueueFilterWaitingForPaymentConfirmation')],
                    ['id' => '300', 'name' => $pSmarty->get_config_vars('str_QueueFilterCancelledOrders')],
                    ['id' => '400', 'name' => $pSmarty->get_config_vars('str_QueueFilterCompletedOrders')],
                    ['id' =>   TPX_ITEM_STATUS_DOWNLOAD_FILES_FTP_ERROR 
                        . ',' .TPX_ITEM_STATUS_IMPORT_FILES_ERROR
                        . ',' .TPX_ITEM_STATUS_DECRYPTING_FILES_ERROR
                        . ',' .TPX_ITEM_STATUS_RAW_FILES_RENDER_ERROR
                        . ',' .TPX_ITEM_STATUS_CONVERTING_FILES_ERROR
                        . ',' .TPX_ITEM_STATUS_PRINTING_FILES_ERROR, 'name' => $pSmarty->get_config_vars('str_Error')]
		];

		return $statusArray;
	}

    static function getListData($pResultArray)
	{
		
		$smarty = SmartyObj::newSmarty('AdminProduction');
        $data = [];
        $data = $pResultArray['queuelist'];
        $measurementunit = $pResultArray['measurementunit'];
        
		echo '[';

		$itemCount = count($data);

		echo '[' . $itemCount . '],';

		for ($i = 0; $i < $itemCount; $i++)
		{
			$item = $data[$i];

            if ($item['orderid'] != '<eol>')
            {
                $height = $item['productheight'];
                $width = $item['productwidth'];

                if ($measurementunit == TPX_COORDINATE_SCALE_MILLIMETRES)
                {
                    $height = round(UtilsObj::convertCoordinate($height, TPX_COORDINATE_SCALE_INCHES, TPX_COORDINATE_SCALE_MILLIMETRES, 6), 2);
                    $width = round(UtilsObj::convertCoordinate($width, TPX_COORDINATE_SCALE_INCHES, TPX_COORDINATE_SCALE_MILLIMETRES, 6), 2);
                }

                $height = ($height != '0.000000') ? $height : $smarty->get_config_vars('str_NotApplicable');
                $width = ($width != '0.000000') ? $width : $smarty->get_config_vars('str_NotApplicable');

                if ($height == $smarty->get_config_vars('str_NotApplicable') && $width == $smarty->get_config_vars('str_NotApplicable'))
                {
                    $dimensions = $smarty->get_config_vars('str_NotApplicable');
                }
                else
                {
                    $dimensions = $width . ' x ' . $height;
                }

                $ordernumber = $item['ordernumber'];
                $ordernumber = ($item['orderlinenumber'] > 0) ? $ordernumber . '.' . $item['orderlinenumber']: $ordernumber;
                $temporderexpirydate = ($item['temporderexpirydate'] == '0000-00-00 00:00:00') ? '' : $item['temporderexpirydate'];
                $filesreceivedtimestamp = ($item['filesreceivedtimestamp'] == '0000-00-00 00:00:00') ? '' : $item['filesreceivedtimestamp'];
                $onholdLabel = UtilsObj::ExtJSEscape($smarty->get_config_vars('str_LabelProductionOnHold'));
                $onholdLabel .= ($item['onholdreason'] !== '') ? ' - ' . UtilsObj::ExtJSEscape($item['onholdreason']) : '';
                $status = ($item['onhold'] == 1) ? $onholdLabel : self::getStatusText($smarty, $item['status'], $item['orderstatus'], $item['statusdescription']);
                $onhold = ($item['onhold'] == 1) ? 'true' : 'false';
                $expired = 'false';

                if ($item['temporderexpirydate'] !== '0000-00-00 00:00:00')
                {
                    $expirydate = new DateTime($item['temporderexpirydate']);
                    $now = new DateTime();
                    $status = ($expirydate < $now) ? $smarty->get_config_vars('str_OrderStatusExpired') : $status;
                    $expired = 'true';
                }

                $brandcode = ($item['brandcode'] == '') ? $smarty->get_config_vars('str_LabelDefault') : $item['brandcode'];

                echo "[";

                    echo "'" . $item['id'] . "',";
                    echo "'" . $item['orderdate'] . "',";
                    echo "'" . $ordernumber . "',";                
                    echo "'" . UtilsObj::ExtJSEscape($item['contactfirstname'] . ' ' . $item['contactlastname']) . "',";
                    echo "'" . UtilsObj::ExtJSEscape($item['productname']) . "',";
                    echo "'" . $item['qty'] . "',";
                    echo "'" . self::getYesNoFromBool($smarty, $item['paymentreceived']) . "',";
                    echo "'" . UtilsObj::ExtJSEscape($item['projectname']) . "',";
                    echo "'" . $status . "',";
                    echo "'" . self::getSourceText($smarty, $item['source']) . "',";
                    echo "'" . UtilsObj::ExtJSEscape($item['accountcode']) . "',";
                    echo "'" . UtilsObj::ExtJSEscape($brandcode) . "',";
                    echo "'" . UtilsObj::ExtJSEscape($item['companyname']) . "',";
                    echo "'" . UtilsObj::ExtJSEscape($item['covername']) . "',";
                    echo "'" . self::getDataFormatText($smarty,$item['uploaddatatype']) . "',";
                    echo "'" . $temporderexpirydate . "',";
                    echo "'" . $filesreceivedtimestamp . "',";
                    echo "'" . UtilsObj::ExtJSEscape($item['groupcode']) . "',";
                    echo "'" . UtilsObj::ExtJSEscape($item['papername']) . "',";
                    echo "'" . $dimensions . "',";
                    echo "'" . sprintf('%07d', $item['id']) . "',";
                    echo "'" . UtilsObj::ExtJSEscape($item['productoutputformatname']) . "',";
                    echo "'" . UtilsObj::ExtJSEscape(self::getUploadMethodText($smarty, $item['uploadmethod'], $item['source'])) . "',";
                    echo "'" . UtilsObj::ExtJSEscape($item['uploadref']) . "',";
                    echo "'" . $item['orderid'] . "',";
                    echo "'" . UtilsObj::ExtJSEscape($item['orderstatus']) . "',";
                    echo "'" . UtilsObj::ExtJSEscape($item['status']) . "',";
                    echo "'" . UtilsObj::ExtJSEscape($expired) . "',";
                    echo "'" . UtilsObj::ExtJSEscape($onhold) . "',";
                echo "]";

                if ($i != $itemCount - 1)
                {
                    echo ",";
                }
            }
		}
		echo ']';
	}

    static function orderDetailsDisplay($pResultArray)
	{
        
        $smarty = SmartyObj::newSmarty('AdminProduction');
        $sourceText = self::getSourceText($smarty, $pResultArray['jobticket']['source']);
        $dataTypeText = self::getDataFormatText($smarty, $pResultArray['jobticket']['uploaddatatype']);
        $uploadMethodText = self::getUploadMethodText($smarty, $pResultArray['jobticket']['uploadmethod'], $pResultArray['jobticket']['source']);

        $shippedStatus = (($pResultArray['jobticket']['shippeddate'] > '0000-00-00 00:00:00') && ($pResultArray['statusid'] > TPX_ITEM_STATUS_FINISHING_COMPLETE)) ? 1 : 0;
        $shippedStatus = self::getYesNoFromBool($smarty, $shippedStatus);
        $trackingNumber = ($pResultArray['statusid'] > TPX_ITEM_STATUS_FINISHING_COMPLETE) ? $pResultArray['jobticket']['shippingtrackingreference'] : '';
        $originalOrder = $pResultArray['originalorder']['ordernumber'];
        $originalOrder = ($originalOrder == $pResultArray['jobticket']['ordernumber'] ? '' : $originalOrder);
        $brandcode = ($pResultArray['jobticket']['webbrandcode'] == '') ? $smarty->get_config_vars('str_LabelDefault') : $pResultArray['jobticket']['webbrandcode'];
        $measurementunit = $pResultArray['measurementunit'];
        $voucher = ($pResultArray['jobticket']['vouchercode'] != '') ? $pResultArray['jobticket']['vouchercode'] : $smarty->get_config_vars('str_LabelNone');

        $height = $pResultArray['jobticket']['productheight'];
        $width = $pResultArray['jobticket']['productwidth'];

        if ($measurementunit == TPX_COORDINATE_SCALE_MILLIMETRES)
        {
            $height = round(UtilsObj::convertCoordinate($height, TPX_COORDINATE_SCALE_INCHES, TPX_COORDINATE_SCALE_MILLIMETRES, 6), 2);
            $width = round(UtilsObj::convertCoordinate($width, TPX_COORDINATE_SCALE_INCHES, TPX_COORDINATE_SCALE_MILLIMETRES, 6), 2);
        }

        $height = ($height != '0.000000') ? $height : $smarty->get_config_vars('str_NotApplicable');
        $width = ($width != '0.000000') ? $width : $smarty->get_config_vars('str_NotApplicable');

        $orderDate = LocalizationObj::formatLocaleDateTime($pResultArray['jobticket']['orderdate']);
        $paymentReceivedDate = ($pResultArray['jobticket']['paymentreceiveddate'] == '0000-00-00 00:00:00') ? $smarty->get_config_vars('str_LabelNo') : LocalizationObj::formatLocaleDateTime($pResultArray['jobticket']['paymentreceiveddate']);
        $taxnumber = ($pResultArray['jobticket']['billingcustomerregisteredtaxnumber'] != '') ? $pResultArray['jobticket']['billingcustomerregisteredtaxnumber'] : $smarty->get_config_vars('str_NotApplicable');

        //orderinfo
        $smarty->assign('originalordernumber', $originalOrder);
        $smarty->assign('statustext', self::getStatusText($smarty, $pResultArray['statusid'], $pResultArray['itemactivestatus'], $pResultArray['statusdescription']));
        $smarty->assign('orderdate', $orderDate);
        $smarty->assign('ordernumber', $pResultArray['jobticket']['ordernumber']);
        $smarty->assign('itemnumber', $pResultArray['jobticket']['itemnumber']);
        $smarty->assign('itemcount', 1);
        $smarty->assign('orderlineid', sprintf('%07d', $pResultArray['orderlineid']));
        $smarty->assign('groupcode', UtilsObj::ExtJSEscape($pResultArray['jobticket']['groupcode']));
        $smarty->assign('groupdata', UtilsObj::ExtJSEscape($pResultArray['jobticket']['groupdata']));
        $smarty->assign('brand', UtilsObj::ExtJSEscape($brandcode . ' - ' . $pResultArray['jobticket']['applicationname']));
        $smarty->assign('vouchercode', UtilsObj::ExtJSEscape($voucher));
        $smarty->assign('projectname', UtilsObj::ExtJSEscape($pResultArray['jobticket']['projectname']));
        $smarty->assign('productcode', UtilsObj::ExtJSEscape($pResultArray['jobticket']['productcode']));
        $smarty->assign('productskucode', UtilsObj::ExtJSEscape($pResultArray['jobticket']['productskucode']));
        $smarty->assign('productname', UtilsObj::ExtJSEscape($pResultArray['jobticket']['productname']));
        $smarty->assign('productdimensions', $width . ' x ' . $height);
        $smarty->assign('pagecountpurchased', $pResultArray['jobticket']['pagecountpurchased']);
        $smarty->assign('pagecount', $pResultArray['jobticket']['pagecount']);
        $smarty->assign('qty', $pResultArray['jobticket']['qty']);
        $smarty->assign('ordertotal', $pResultArray['jobticket']['formattedordertotal']);
        $smarty->assign('ordergiftcardtotal', $pResultArray['jobticket']['formattedordergiftcardtotal']);
        $smarty->assign('ordertotaltopay', $pResultArray['jobticket']['formattedordertotaltopay']);
        $smarty->assign('itemactivestatus', $pResultArray['itemactivestatus']);

        //shippinginfo
        $smarty->assign('shippingcustomername', UtilsObj::ExtJSEscape($pResultArray['jobticket']['shippingcustomername']));
        $smarty->assign('shippingcustomeraddress1', UtilsObj::ExtJSEscape($pResultArray['jobticket']['shippingcustomeraddress1']));
        $smarty->assign('shippingcustomeraddress2', UtilsObj::ExtJSEscape($pResultArray['jobticket']['shippingcustomeraddress2']));
        $smarty->assign('shippingcustomeraddress3', UtilsObj::ExtJSEscape($pResultArray['jobticket']['shippingcustomeraddress3']));
        $smarty->assign('shippingcustomeraddress4', UtilsObj::ExtJSEscape($pResultArray['jobticket']['shippingcustomeraddress4']));
        $smarty->assign('shippingcustomercity', UtilsObj::ExtJSEscape($pResultArray['jobticket']['shippingcustomercity']));
        $smarty->assign('shippingcustomercounty', UtilsObj::ExtJSEscape($pResultArray['jobticket']['shippingcustomercounty']));
        $smarty->assign('shippingcustomerstate', UtilsObj::ExtJSEscape($pResultArray['jobticket']['shippingcustomerstate']));
        $smarty->assign('shippingcustomerpostcode', UtilsObj::ExtJSEscape($pResultArray['jobticket']['shippingcustomerpostcode']));
        $smarty->assign('shippingcustomercountryname', UtilsObj::ExtJSEscape($pResultArray['jobticket']['shippingcustomercountryname']));
        $smarty->assign('shippingcustomertelephonenumber', UtilsObj::ExtJSEscape($pResultArray['jobticket']['shippingcustomertelephonenumber']));
        $smarty->assign('shippingcustomeremailaddress', UtilsObj::ExtJSEscape($pResultArray['jobticket']['shippingcustomeremailaddress']));
        $smarty->assign('shippingcontactfirstname', UtilsObj::ExtJSEscape($pResultArray['jobticket']['shippingcontactfirstname']));
        $smarty->assign('shippingcontactlastname', UtilsObj::ExtJSEscape($pResultArray['jobticket']['shippingcontactlastname']));
        $smarty->assign('shippingmethod', UtilsObj::ExtJSEscape($pResultArray['jobticket']['shippingmethodname']));
        $smarty->assign('shippingstatus', $shippedStatus);
        $smarty->assign('shippingtrackingreference', UtilsObj::ExtJSEscape($trackingNumber));

        //billinginfo
        $smarty->assign('billingcustomeraccountcode', UtilsObj::ExtJSEscape($pResultArray['jobticket']['billingcustomeraccountcode']));
        $smarty->assign('billingcustomername', UtilsObj::ExtJSEscape($pResultArray['jobticket']['billingcustomername']));
        $smarty->assign('billingcustomeraddress1', UtilsObj::ExtJSEscape($pResultArray['jobticket']['billingcustomeraddress1']));
        $smarty->assign('billingcustomeraddress2', UtilsObj::ExtJSEscape($pResultArray['jobticket']['billingcustomeraddress2']));
        $smarty->assign('billingcustomeraddress3', UtilsObj::ExtJSEscape($pResultArray['jobticket']['billingcustomeraddress3']));
        $smarty->assign('billingcustomeraddress4', UtilsObj::ExtJSEscape($pResultArray['jobticket']['billingcustomeraddress4']));
        $smarty->assign('billingcustomercity', UtilsObj::ExtJSEscape($pResultArray['jobticket']['billingcustomercity']));
        $smarty->assign('billingcustomercounty', UtilsObj::ExtJSEscape($pResultArray['jobticket']['billingcustomercounty']));
        $smarty->assign('billingcustomerstate', UtilsObj::ExtJSEscape($pResultArray['jobticket']['billingcustomerstate']));
        $smarty->assign('billingcustomerpostcode', UtilsObj::ExtJSEscape($pResultArray['jobticket']['billingcustomerpostcode']));
        $smarty->assign('billingcustomercountryname',UtilsObj::ExtJSEscape($pResultArray['jobticket']['billingcustomercountryname']));
        $smarty->assign('billingcustomertelephonenumber', UtilsObj::ExtJSEscape($pResultArray['jobticket']['billingcustomertelephonenumber']));
        $smarty->assign('billingcustomeremailaddress', UtilsObj::ExtJSEscape($pResultArray['jobticket']['billingcustomeremailaddress']));
        $smarty->assign('billingcontactfirstname', UtilsObj::ExtJSEscape($pResultArray['jobticket']['billingcontactfirstname']));
        $smarty->assign('billingcontactlastname', UtilsObj::ExtJSEscape($pResultArray['jobticket']['billingcontactlastname']));
        $smarty->assign('billingcustomerregisteredtaxnumber', UtilsObj::ExtJSEscape($taxnumber));
        $smarty->assign('paymentmethodname', UtilsObj::ExtJSEscape($pResultArray['jobticket']['paymentmethodname']));
        $smarty->assign('ccitype', UtilsObj::ExtJSEscape($pResultArray['jobticket']['ccitype']));
        $smarty->assign('ccitransactionid', UtilsObj::ExtJSEscape($pResultArray['jobticket']['ccitransactionid']));
        $smarty->assign('cciresponsecode', UtilsObj::ExtJSEscape($pResultArray['jobticket']['cciresponsecode']));
        $smarty->assign('cciresponsedescription', UtilsObj::ExtJSEscape($pResultArray['jobticket']['cciresponsedescription']));
        $smarty->assign('paymentreceiveddate',UtilsObj::ExtJSEscape( $paymentReceivedDate ));

        //otherinfo
        $smarty->assign('source', UtilsObj::ExtJSEscape($pResultArray['jobticket']['source']));
        $smarty->assign('sourcetext', $sourceText);
        $smarty->assign('uploaddatatype', $dataTypeText);
        $smarty->assign('uploadmethod', $uploadMethodText);
        $smarty->assign('canuploadfiles', $pResultArray['jobinfo']['canuploadfiles']);
        $smarty->assign('canuploadenablesaveoverride', $pResultArray['jobinfo']['canuploadenablesaveoverride']);
        $smarty->assign('canmodify', $pResultArray['jobinfo']['canmodify']);
        $smarty->assign('canuploadproductcodeoverride', $pResultArray['jobinfo']['canuploadproductcodeoverride']);
        $smarty->assign('canuploadpagecountoverride', $pResultArray['jobinfo']['canuploadpagecountoverride']);

        $otherInfoArray = [];
        array_push($otherInfoArray, ['label' => $smarty->get_config_vars('str_LabelProductionUploadRef'), 'data' => UtilsObj::ExtJSEscape($pResultArray['jobticket']['uploadref'])]);
        array_push($otherInfoArray, ['label' => $smarty->get_config_vars('str_LabelProductionFilesReceivedTimeStamp'), 'data' => $pResultArray['jobinfo']['filesreceivedtimestamp'] != '0000-00-00 00:00:00' ? LocalizationObj::formatLocaleDateTime($pResultArray['jobinfo']['filesreceivedtimestamp']) : '']);
        array_push($otherInfoArray, ['label' => $smarty->get_config_vars('str_LabelProductionFilesReceivedBy'), 'data' => UtilsObj::ExtJSEscape($pResultArray['jobinfo']['filesreceivedusername'])]);
        array_push($otherInfoArray, ['label' => $smarty->get_config_vars('str_LabelProductionFilesDecryptionTimeStamp'), 'data' => $pResultArray['jobinfo']['decrypttimestamp'] != '0000-00-00 00:00:00' ? LocalizationObj::formatLocaleDateTime($pResultArray['jobinfo']['decrypttimestamp']) : '']);
        array_push($otherInfoArray, ['label' => $smarty->get_config_vars('str_LabelProductionFilesDecryptedBy'), 'data' => UtilsObj::ExtJSEscape($pResultArray['jobinfo']['decryptusername'])]);
        array_push($otherInfoArray, ['label' => $smarty->get_config_vars('str_LabelProductionFilesConversionTimeStamp'), 'data' => $pResultArray['jobinfo']['converttimestamp'] != '0000-00-00 00:00:00' ? LocalizationObj::formatLocaleDateTime($pResultArray['jobinfo']['converttimestamp']) : '']);
        array_push($otherInfoArray, ['label' => $smarty->get_config_vars('str_LabelProductionFilesConvertedBy'), 'data' => UtilsObj::ExtJSEscape($pResultArray['jobinfo']['convertusername'])]);
        array_push($otherInfoArray, ['label' => $smarty->get_config_vars('str_LabelProductionOutputFormat'), 'data' => UtilsObj::ExtJSEscape($pResultArray['jobinfo']['convertoutputformatname'])]);
        array_push($otherInfoArray, ['label' => $smarty->get_config_vars('str_LabelProductionOrderPrintedTimeStamp'), 'data' => $pResultArray['jobinfo']['outputtimestamp'] != '0000-00-00 00:00:00' ? LocalizationObj::formatLocaleDateTime($pResultArray['jobinfo']['outputtimestamp']) : '']);
        array_push($otherInfoArray, ['label' => $smarty->get_config_vars('str_LabelProductionOrderPrintedBy'), 'data' => UtilsObj::ExtJSEscape($pResultArray['jobinfo']['outputusername'])]);
        array_push($otherInfoArray, ['label' => $smarty->get_config_vars('str_LabelProductionJobTicketOutputDevice'), 'data' => UtilsObj::ExtJSEscape($pResultArray['jobinfo']['jobticketoutputdevicename'])]);
        array_push($otherInfoArray, ['label' => $smarty->get_config_vars('str_LabelProductionPagesOutputDevice'), 'data' => UtilsObj::ExtJSEscape($pResultArray['jobinfo']['pagesoutputdevicename'])]);
        array_push($otherInfoArray, ['label' => $smarty->get_config_vars('str_LabelProductionCoverOutputDevice'), 'data' => UtilsObj::ExtJSEscape($pResultArray['jobinfo']['cover1outputdevicename'])]);
        array_push($otherInfoArray, ['label' => $smarty->get_config_vars('str_LabelProductionBackCoverOutputDevice'), 'data' => UtilsObj::ExtJSEscape($pResultArray['jobinfo']['cover2outputdevicename'])]);
        array_push($otherInfoArray, ['label' => $smarty->get_config_vars('str_LabelProductionXMLOutputDevice'), 'data' => UtilsObj::ExtJSEscape($pResultArray['jobinfo']['xmloutputdevicename'])]);
        array_push($otherInfoArray, ['label' => $smarty->get_config_vars('str_LabelProductionOrderFinishingTimeStamp'), 'data' => $pResultArray['jobinfo']['finishtimestamp'] != '0000-00-00 00:00:00' ? LocalizationObj::formatLocaleDateTime($pResultArray['jobinfo']['finishtimestamp']) : '']);
        array_push($otherInfoArray, ['label' => $smarty->get_config_vars('str_LabelProductionOrderFinishingBy'), 'data' => UtilsObj::ExtJSEscape($pResultArray['jobinfo']['finishusername'])]);
        array_push($otherInfoArray, ['label' => $smarty->get_config_vars('str_LabelProductionShippedTimeStamp'), 'data' => $pResultArray['jobinfo']['shippedtimestamp'] != '0000-00-00 00:00:00' ? LocalizationObj::formatLocaleDateTime($pResultArray['jobinfo']['shippedtimestamp']) : '']);
        array_push($otherInfoArray, ['label' => $smarty->get_config_vars('str_LabelProductionShippedBy'), 'data' => UtilsObj::ExtJSEscape($pResultArray['jobinfo']['shippedusername'])]);
        array_push($otherInfoArray, ['label' => $smarty->get_config_vars('str_LabelProductionPaymentReceivedTimeStamp'), 'data' => $pResultArray['jobinfo']['paymentreceivedtimestamp'] != '0000-00-00 00:00:00' ? LocalizationObj::formatLocaleDateTime($pResultArray['jobinfo']['paymentreceivedtimestamp']) : '']);
        array_push($otherInfoArray, ['label' => $smarty->get_config_vars('str_LabelProductionPaymentConfirmedBy'), 'data' => UtilsObj::ExtJSEscape($pResultArray['jobinfo']['paymentreceivedusername'])]);
        array_push($otherInfoArray, ['label' => $smarty->get_config_vars('str_LabelProductionTotalOrders'), 'data' => UtilsObj::ExtJSEscape($pResultArray['jobticket']['userordercount'])]);
        array_push($otherInfoArray, ['label' => $smarty->get_config_vars('str_LabelProductionTotalOrderLines'), 'data' => $pResultArray['jobticket']['userorderitemcount']]);
        array_push($otherInfoArray, ['label' => $smarty->get_config_vars('str_LabelProductionSmartDesignerUsed'), 'data' => self::getYesNoFromBool($smarty, $pResultArray['jobinfo']['projectaimode'])]);
          
        $smarty->assign('otherinfolist', $otherInfoArray);

        $smarty->displayLocale('admin/production/orderdetails.tpl');
	}

    static function getSourceText($pSmarty, $pSource)
    {
        $sourceText = '';

        if (TPX_SOURCE_ONLINE == $pSource)
        {
            $sourceText = $pSmarty->get_config_vars('str_LabelProductionSourceOnline');
        } else {
            $sourceText = $pSmarty->get_config_vars('str_LabelProductionSourceDesktop');
        }

        return UtilsObj::ExtJSEscape($sourceText);
    }
    
    static function getDataFormatText($pSmarty, $pFormat)
    {
        $formatText = '';

        if (TPX_UPLOAD_DATA_TYPE_RENDERED == $pFormat)
        {
            $formatText = $pSmarty->get_config_vars('str_LabelProductionDataTypeRendered');
        } else {
            $formatText = $pSmarty->get_config_vars('str_LabelProductionDataTypeProjectElements');
        }

        return UtilsObj::ExtJSEscape($formatText);
    }

    static function getUploadMethodText($pSmarty, $pMethod, $pSource)
    {
        $methodText = $pSmarty->get_config_vars('str_NotApplicable');

        if (TPX_UPLOAD_DELIVERY_METHOD_INTERNET == $pMethod)
        {
            $methodText = $pSmarty->get_config_vars('str_LabelProductionUploadMethodInternet');
        } else {
            $methodText = $pSmarty->get_config_vars('str_LabelProductionUploadMethodMail');
        }

        return UtilsObj::ExtJSEscape($methodText);
    }

    static function getYesNoFromBool($pSmarty, $Value)
    {
        $returnText = $pSmarty->get_config_vars('str_LabelNo');

        if ($Value == 1)
        {
            $returnText = $pSmarty->get_config_vars('str_LabelYes');
        } 

        return UtilsObj::ExtJSEscape($returnText);
    }

    static function getStatusText($pSmarty, $pStatus, $pItemActiveStatus, $pStatusDescription = '')
    {
        $returnText = '';

        if ($pItemActiveStatus > TPX_ORDER_STATUS_IN_PROGRESS)
        {
            if ($pItemActiveStatus == TPX_ORDER_STATUS_CANCELLED)
            {
                $returnText = $pSmarty->get_config_vars('str_OrderStatusCancelled');
            }

            if ($pItemActiveStatus == TPX_ORDER_STATUS_COMPLETED)
            {
                $returnText = $pSmarty->get_config_vars('str_OrderStatusCompleted');
            }
        } 
        else
        {
            switch ($pStatus)
            {
                case TPX_ITEM_STATUS_AWAITING_FILES:
                    $returnText = $pSmarty->get_config_vars('str_OrderItemStatusAwaitingFiles');
                break;
                
                case TPX_ITEM_STATUS_FILES_ON_REMOTE_FTP_SERVER:
                    $returnText = $pSmarty->get_config_vars('str_OrderItemStatusFilesOnFTPServer');
                break;

                case TPX_ITEM_STATUS_DOWNLOAD_FILES_QUEUED:
                    $returnText = $pSmarty->get_config_vars('str_OrderItemStatusDownloadQueued');
                break;

                case TPX_ITEM_STATUS_DOWNLOADING_FROM_REMOTE_FTP_SITE:
                    $returnText = $pSmarty->get_config_vars('str_OrderItemStatusDownloadingFiles');
                break;

                case TPX_ITEM_STATUS_DOWNLOAD_FILES_FTP_ERROR:
                    $returnText = $pSmarty->get_config_vars('str_OrderItemStatusDownloadError');
                break;

                case TPX_ITEM_STATUS_FILES_RECEIVED:
                    $returnText = $pSmarty->get_config_vars('str_OrderItemStatusFilesReceived');
                break;

                case TPX_ITEM_STATUS_IMPORT_FILES_ERROR:
                    $returnText = $pSmarty->get_config_vars('str_OrderItemStatusImportFilesError');
                break;

                case TPX_ITEM_STATUS_DECRYPT_FILES_QUEUED:
                    $returnText = $pSmarty->get_config_vars('str_OrderItemStatusDecryptFilesQueued');
                break;

                case TPX_ITEM_STATUS_DECRYPTING_FILES:
                    $returnText = $pSmarty->get_config_vars('str_OrderItemStatusDecryptingFiles');
                break;

                case TPX_ITEM_STATUS_DECRYPTING_FILES_ERROR:
                    $returnText = $pSmarty->get_config_vars('str_OrderItemStatusErrorDecryptingFiles');
                break;

                case TPX_ITEM_STATUS_DECRYPTED_FILES:
                    $returnText = $pSmarty->get_config_vars('str_OrderItemStatusFilesDecrypted');
                break;

                case TPX_ITEM_STATUS_RAW_FILES_READY_TO_PROCESS:
                    $returnText = $pSmarty->get_config_vars('str_OrderItemStatusProjectElementsReadyToProcess');
                break;

                case TPX_ITEM_STATUS_RAW_FILES_QUEUED_FOR_RENDER_SUBMISSION:
                    $returnText = $pSmarty->get_config_vars('str_OrderItemStatusProjectElementsRenderSubmissionQueued');
                break;

                case TPX_ITEM_STATUS_RAW_FILES_QUEUED_FOR_RENDERING:
                    $returnText = $pSmarty->get_config_vars('str_OrderItemStatusProjectElementsRenderQueued');
                break;

                case TPX_ITEM_STATUS_RAW_FILES_RENDER_ERROR:
                    $returnText = $pSmarty->get_config_vars('str_OrderItemStatusProjectElementsRenderError');
                break;

                case TPX_ITEM_STATUS_CONVERT_FILES_QUEUED:
                    $returnText = $pSmarty->get_config_vars('str_OrderItemStatusConvertFilesQueued');
                break;

                case TPX_ITEM_STATUS_CONVERTING_FILES_ERROR:
                    $returnText = $pSmarty->get_config_vars('str_OrderItemStatusErrorConvertingFiles');
                break;

                case TPX_ITEM_STATUS_CONVERTED_FILES:
                    $returnText = $pSmarty->get_config_vars('str_OrderItemStatusFilesConverted');
                break;

                case TPX_ITEM_STATUS_READY_TO_PRINT:
                    $returnText = $pSmarty->get_config_vars('str_QueueFilterReadyToPrint');
                break;

                case TPX_ITEM_STATUS_PRINT_FILES_QUEUED:
                    $returnText = $pSmarty->get_config_vars('str_OrderItemStatusPrintingFilesQueued');
                break;

                case TPX_ITEM_STATUS_PRINTING_FILES_PRINTING:
                    $returnText = $pSmarty->get_config_vars('str_OrderItemStatusPrintingFilesPrinting');
                break;

                case TPX_ITEM_STATUS_PRINTING_FILES_ERROR:
                    $returnText = $pSmarty->get_config_vars('str_OrderItemStatusErrorSendingToDevice');
                break;

                case TPX_ITEM_STATUS_PRINTING_SENT_TO_EXTERNAL_WORKFLOW:
                    $returnText = $pSmarty->get_config_vars('str_OrderItemStatusPrintedSentToExternalWorkflow');
                break;

                case TPX_ITEM_STATUS_PRINTED:
                    $returnText = $pSmarty->get_config_vars('str_OrderItemStatusPrinted');
                break;

                case TPX_ITEM_STATUS_FINISHING_COMPLETE:
                    $returnText = $pSmarty->get_config_vars('str_OrderItemStatusFinishingComplete');
                break;

                case TPX_ITEM_STATUS_SHIPPED_TO_CUSTOMER:
                    $returnText = $pSmarty->get_config_vars('str_JobTicketShippedToCustomerLabel');
                break;

                case TPX_ITEM_STATUS_SHIPPED_TO_DISTRIBUTION_CENTRE:
                    $returnText = $pSmarty->get_config_vars('str_JobTicketShippedToDistributionCentreLabel');
                break;

                case TPX_ITEM_STATUS_SHIPPED_RECEIVED_AT_DISTRIBUTION_CENTRE:
                    $returnText = $pSmarty->get_config_vars('str_JobTicketShippedReceivedAtDistributionCentreLabel');
                break;

                case TPX_ITEM_STATUS_SHIPPED_TO_STORE_DIRECTLY:
                    $returnText = $pSmarty->get_config_vars('str_JobTicketShippedToStoreLabel');
                break;

                case TPX_ITEM_STATUS_SHIPPED_TO_STORE_FROM_DISTRIBUTION_CENTRE:
                    $returnText = $pSmarty->get_config_vars('str_JobTicketShippedDistributionCentreShippedToStoreLabel');
                break;

                case TPX_ITEM_STATUS_SHIPPED_RECEIVED_AT_STORE:
                    $returnText = $pSmarty->get_config_vars('str_JobTicketShippedReceivedAtStoreLabel');
                break;

                case TPX_ITEM_STATUS_SHIPPED_COLLECTED_BY_CUSTOMER:
                    $returnText = $pSmarty->get_config_vars('str_JobTicketShippedCollectedByCustomerLabel');
                break;
            }
        }

        if ($pStatusDescription != '') {
            $returnText .= ' - ' . $pStatusDescription;
        }

        return UtilsObj::ExtJSEscape($returnText);
    }

    static function onHoldDisplay()
    {
        $smarty = SmartyObj::newSmarty('AdminProduction');

        $smarty->displayLocale('admin/production/onhold.tpl');
    }

    static function preferencesDisplay($pResultArray)
    {
        $smarty = SmartyObj::newSmarty('AdminProduction');
        $prefData = '';
        $prefDataArray = $pResultArray['data'];

        if (count($prefDataArray) > 0) 
        {
            $prefData = $prefDataArray[0]['data'];
        }

        $prefData = ($prefData != '') ? json_decode($prefData) : $prefData;

        $smarty->assign('prefdata', $prefData);
        $smarty->assign('TPX_COORDINATE_SCALE_INCHES', TPX_COORDINATE_SCALE_INCHES);
        $smarty->assign('TPX_COORDINATE_SCALE_MILLIMETRES', TPX_COORDINATE_SCALE_MILLIMETRES);

        $smarty->displayLocale('admin/production/preferences.tpl');
    }

    static function confirmPaymentDisplay()
    {
        $smarty = SmartyObj::newSmarty('AdminProduction');

        $smarty->assign('dateformat', LocalizationObj::getLocaleFormatValue('str_DateFormat'));
        $smarty->assign('timeformat', LocalizationObj::getLocaleFormatValue('str_TimeFormat'));
        $smarty->displayLocale('admin/production/confirmpayment.tpl');
    }

    static function shippingDisplay()
    {
        $smarty = SmartyObj::newSmarty('AdminProduction');
        $smarty->assign('dateformat', LocalizationObj::getLocaleFormatValue('str_DateFormat'));
        $smarty->assign('timeformat', LocalizationObj::getLocaleFormatValue('str_TimeFormat'));
        $smarty->displayLocale('admin/production/ship.tpl');
    }

    static function statusCheck($pResult)
    {
        $stringResult = $pResult ? 'true' : 'false';
        echo '{"success": ' . $stringResult . '}';
    }
}

?>
