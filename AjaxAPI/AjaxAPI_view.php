<?php
use Security\ControlCentreCSP;
require_once('../Order/Order_view.php');

class AjaxAPI_view {

	static function makeInput($name,$value, $readonly, $fieldLabel, $allowblank){
		$onblurListener = '';

		if ($name == 'mainpostcode')
		{
			$onblurListener = ", listeners: {'blur': {fn: function(obj){CJKHalfWidthFullWidthToASCII(obj.getEl().dom, true)}}}";
		}

		return 'new Ext.form.TextField({fieldLabel: "' . $fieldLabel . '",
										name: "' . $name . '",
										id: "' . $name . '",
										value:"' . UtilsObj::ExtJSEscape($value) . '",
										readOnly:"' . $readonly . '",
										allowBlank: ' . $allowblank . ',validateOnBlur:true' . $onblurListener.'})';
	}

	static function makeSelect($name,$value, $readonly, $fieldLabel, $keyCode, $valueCode, $allowblank)
	{
		$store = Array(); for($i=0; $i<count($value); $i++){	$store[] = "['".UtilsObj::ExtJSEscape($value[$i][$keyCode])."','".UtilsObj::ExtJSEscape($value[$i][$valueCode])."']"; }

		$combobox = "new Ext.form.ComboBox({ id: '".$name."', name: '".$name."', hiddenName:'".$name."_hn',	hiddenId:'".$name."_hi',	mode: 'local', editable: false,
   				forceSelection: true,valueField: 'field_id', displayField: 'field_name', useID: true, post: true, fieldLabel:'".$fieldLabel."',
				store: new Ext.data.ArrayStore({ id: 0, fields: ['field_id', 'field_name'],	data: ".'['.join(',',$store).']'." }), triggerAction: 'all', allowBlank: ".$allowblank.", validateOnBlur:true, width:200 })";
		return $combobox;
	}

	static function getControl($controlData)
	{
		if($controlData['type']=='txt')
			return self::makeInput($controlData['name'],$controlData['value'], $controlData['readonly'], $controlData['fieldlabel'], $controlData['allowblank']);
		else
			return self::makeSelect($controlData['name'],$controlData['value'], $controlData['readonly'], $controlData['fieldlabel'],$controlData['keyCode'],$controlData['valueCode'], $controlData['allowblank']);
	}

	static function extJsAddressForm($fieldsArray){
		$resultArray = array();
		$formFields = '';
		$fieldWidth = $_GET['fieldWidth'];

		foreach ($fieldsArray as $key=>$value)
		{
			$resultArray[] = self::getControl($fieldsArray[$key]);
		}

		// panel id must be unique
		$formFields = "new Ext.Panel({ id: 'addressFormPanel', layout: 'form', plain:true, autoWidth:true, defaults:{width: $fieldWidth}, items: [";
		$formFields .= join(",", $resultArray) . ']})';

    	echo $formFields;
    	return;
	}

	static function ExtJsShippingRegion($fieldsArray){
		$regions = array();
		$usedRegions = array();
		for ($i=0; $i < count($fieldsArray['regions']); $i++)
		{
			if (!in_array($fieldsArray['regions'][$i]['code'],$usedRegions))
			{
				$regions[] = '["'.$fieldsArray['regions'][$i]['code'].'","'.$fieldsArray['regions'][$i]['name'].'"]';
				$usedRegions[] = $fieldsArray['regions'][$i]['code'];
			}
		}

		$result = "{'code': '".$fieldsArray['countryCode']."', 'regions': [".join(',', $regions)."]}";

    	echo $result;

    	return;
	}



	static function addressForm($resultArray)
	{
		global $gSession;

		$smarty = SmartyObj::newSmarty('', $gSession['webbrandcode'], '', '', false, false);

		$smarty->assign('result', $resultArray['result']);
		$smarty->assign('addressForm', $resultArray['addressform']);
		$smarty->assign('countryCode', $resultArray['countrycode']);
		$smarty->assign('countryName', $resultArray['countryname']);
		$smarty->assign('countryList', $resultArray['countrylist']);
		$smarty->assign('regionList', $resultArray['regionlist']);
		$smarty->assign('region', $resultArray['region']);
		$smarty->assign('tablewidth', $resultArray['tablewidth']);

		$registeredTaxNumberTypesArray = array
		(
			array('id' => TPX_REGISTEREDTAXNUMBERTYPE_NA, 'name' => $smarty->get_config_vars('str_LabelMakeSelection')),
			array('id' => TPX_REGISTEREDTAXNUMBERTYPE_PERSONAL, 'name' => $smarty->get_config_vars('str_LabelCustomerTaxNumberTypePersonal')),
			array('id' => TPX_REGISTEREDTAXNUMBERTYPE_CORPORATE, 'name' => $smarty->get_config_vars('str_LabelCustomerTaxNumberTypeCorporate'))
		);

		$smarty->assign('registeredtaxnumbertypes', $registeredTaxNumberTypesArray);

		if ($resultArray['editmode'] == '1')
		{
			$smarty->assign('readonly', 'readonly="readonly"');
		}
		else
		{
			$smarty->assign('readonly', '');
		}

		header ("Content-Type: text/html; charset=utf-8");

		$smarty->display('ajaxapi.tpl');
	}

	static function emailTest($result)
	{
		if ($result == '')
		{
			echo "{'success':'true', 'msg':'" . '' . "' }";
		}
		else
		{
			echo '{"success":false,	"msg":"' . UtilsObj::encodeString($result) . '"}';
		}
		return;
	}

	static function emailTestJson($result)
	{
		if ($result == '')
		{
			echo "{'success':'true', 'msg':'" . '' . "' }";
		}
		else
		{
			echo '{"success":false,	"msg":"' . UtilsObj::encodeString($result) . '"}';
		}

		return;
	}


	static function addressVerification($pResult)
	{
		echo $pResult;
	}

	static function autoSuggest($pResult)
	{
		header ("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Date in the past
		header ("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); // always modified
		header ("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
		header ("Pragma: no-cache"); // HTTP/1.0
		header ("Content-Type: application/json; charset=utf-8");

		echo "{\"results\": [";
		$arr = array();

		$suggestions = $pResult['suggestions'];

		for ($i = 0; $i < count($suggestions); $i++)
		{
			$arr[] = "{\"id\": \"". $i ."\", \"value\": \"" . $suggestions[$i] . "\", \"info\": \"\"}";
		}

		echo implode(", ", $arr);
		echo "]}";
	}

	static function comboDataStore($pResultArray)
    {
		echo '[';

		$itemCount = count($pResultArray);

		for ($i = 0; $i < $itemCount; $i++)
		{
			$item = $pResultArray[$i];

			echo "['" . $item['id'] . "',";

			if (array_key_exists('applicationname',$pResultArray[$i]))
			{
				echo "'" . UtilsObj::encodeString($item['applicationname'], true) . "',";
			}
			else
			{
				echo "'" . UtilsObj::encodeString($item['name'], true) . "',";
			}

			echo "'" . UtilsObj::encodeString($item['code'], true) . "']";

			if ($i != $itemCount - 1)
			{
				echo ",";
			}
		}
		echo ']';
    }

    static function licenseDataStore($pResultArray)
    {
		$activeOnly = UtilsObj::getGETParam('activeonly', 0);

		echo '[';

		$itemCount = count($pResultArray);

		for ($i = 0; $i < $itemCount; $i++)
		{
			$item = $pResultArray[$i];

			if ($activeOnly == 1)
			{
				if ($item['active'] == '1')
				{
					echo "['" . $item['id'] . "',";
					echo "'" . $item['id'] .' - '. UtilsObj::encodeString($item['name'], true) . "']";

					if ($i != $itemCount - 1)
					{
						echo ",";
					}
				}
			}
			else
			{
				echo "['" . $item['id'] . "',";
				echo "'" . $item['id'] .' - '. UtilsObj::encodeString($item['name'], true) . "']";

				if ($i != $itemCount - 1)
				{
					echo ",";
				}
			}
		}
		echo ']';
    }

    static function countryDataStore($pResultArray)
	{
		echo '[';

		$itemCount = count($pResultArray);
		for ($i = 0; $i < $itemCount; $i++)
		{
			$item = $pResultArray[$i];

			echo "['" . $item['id'] . "',";
			echo "'" . UtilsObj::encodeString($item['name'], true) . "']";

			if ($i != $itemCount - 1)
			{
				echo ",";
			}
		}

		echo ']';
	}

    static function productCollectionDataStore($pResultArray)
    {
		echo '[';

		$itemCount = count($pResultArray);

		for ($i = 0; $i < $itemCount; $i++)
		{
			$item = $pResultArray[$i];

			echo "['" . $item['code'] . "',";
			echo "'" . $item['code'] .' - '. UtilsObj::encodeString($item['name'], true) . "']";

			if ($i != $itemCount - 1)
			{
				echo ",";
			}

		}
		echo ']';
    }


	static function unknownCommand()
	{
		$smarty = SmartyObj::newSmarty('', '', '', '', false, false);

		$smarty->assign('result', 'ERROR');
	    $smarty->assign('resultParam', 'Unknown Command.');

	    $smarty->display('ajaxapi.tpl');
	}

	static function storeLocator($pStoreList)
	{
		Order_view::storeLocator($pStoreList, false);
	}

	static function storeLocatorExternal($pResultArray)
	{
		Order_view::storeLocatorExternal($pResultArray, false);
	}

	static function storeInformation($pStoreInformation)
	{
		global $gSession;

        $smarty = SmartyObj::newSmarty('Order', '', '', '', false, false);
		$smarty->assign('storedetails', $pStoreInformation['storedetails']);
		$smarty->assign('storename', $pStoreInformation['storename']);
		$smarty->assign('storeopeningtimes', $pStoreInformation['storeopeningtimes']);
		$smarty->assign('telephonenumber', $pStoreInformation['telephonenumber']);
		$smarty->assign('emailaddress', $pStoreInformation['emailaddress']);
		$smarty->assign('storeurl', $pStoreInformation['storeurl']);
		$smarty->assign('information', $pStoreInformation['information']);

        if ($gSession['ismobile'] == true)
        {
            $smarty->displayLocale('order/storeinformation_small.tpl');
        }
        else
        {
            $smarty->displayLocale('order/storeinformation_large.tpl');
        }
	}

	static function getCompaniesLicensekeys($pDataArray)
	{
		$tableDataArray = self::getLicensekeysTable($pDataArray);

		$tableData = $tableDataArray['licensekeytable'];

		echo $tableData;
	}

	static function getLicensekeysTable($pDataArray)
	{
		$smarty = SmartyObj::newSmarty('AdminProductPricing');

		$itemsList = Array();
		$itemsArray = $pDataArray['items'];
		$itemCount = count($itemsArray);

		for ($i = 0; $i < $itemCount; $i++)
		{
			array_push($itemsList, $itemsArray[$i]['groupcode']);
		}

		$allCodesArray = $pDataArray['groupcodes'];
		$itemCount = count($allCodesArray);

		$resultArray = Array();
		$tableData = '';
		$colCount = 0;
		$maxColumns = 6;
		$addedItemCount = 0;
		$selectedItemCount = 0;

		$tableData .= '<table class="keylist" cellpadding="5" cellspacing="0" width="100%">';
		for ($i = 0; $i < count($allCodesArray); $i++)
		{
			$groupCode = $allCodesArray[$i]['code'];

			if ($groupCode == '')
			{
				$name = '<label>'. '<i>' . $smarty->get_config_vars('str_LabelDefault') . '</i>'.'</label>';
			}
			else
			{
				$name = '<label>'. $groupCode.'</label>';
			}

			if ($colCount == 0)
			{
				$tableData .= '<tr class="text">';
			}

			if ($addedItemCount < $maxColumns)
			{
				if ($colCount == 0)
				{
					$tableData .= '<td class="tableHeadBorderLeft">';
				}
				else
				{
					$tableData .= '<td class="tableHeadBorderMiddle">';
				}
			}
			else
			{
				if ($colCount == 0)
				{
					$tableData .= '<td class="tableColBorderLeft">';
				}
				else
				{
					$tableData .= '<td class="tableColBorderMiddle">';
				}
			}
			$tableData .= '<input type="checkbox" id="item' . $addedItemCount . '" name="item' . $addedItemCount . '" value="' . $groupCode . '" ';

			if (in_array($groupCode, $itemsList))
			{
				$tableData .= 'checked ';
				$selectedItemCount++;
			}

			$tableData .= ' onClick="return updateCheckboxCount(this);">' . $name . '</td>';

			$colCount++;
			$addedItemCount++;

			if ($colCount == $maxColumns)
			{
				$tableData .= '</tr>';
				$colCount = 0;
			}
		}

		// fill the rest of the row
		if ($addedItemCount > 0)
		{
			if (($addedItemCount > $maxColumns) && ($colCount > 0))
			{
				$colCount = $maxColumns - $colCount;
				if ($addedItemCount < $maxColumns)
				{
					$tableData .= '<td class="tableHeadBorderMiddle" colspan="' . $colCount . '">&nbsp;</td>';
				}
				else
				{
					$tableData .= '<td class="tableColBorderMiddle" colspan="' . $colCount . '">&nbsp;</td>';
				}
				$tableData .= '</tr>';
			}
		}
		else
		{
			$tableData .= '<tr class="text"><td class="tableHeadBorderLeft"><i>' . $smarty->get_config_vars('str_LabelNotAvailable') . '</i></td></tr>';
		}

		$tableData .= '</table>';

		$resultArray['licensekeytable'] = $tableData;
		$resultArray['addeditemcount'] = $addedItemCount;
		$resultArray['selecteditemcount'] = $selectedItemCount;

		return $resultArray;
	}

	/**
	 * Formats and outputs product list array.
	 *
	 * Modified: -
	 *
	 * @param array $resultProductList
	 *  An array of code/name pairs: array(['code'] => 'code', ['name'] => 'name')
	 *
	 * @param string $outputMathod
	 *  Use ECHO to echo an output to be used in ExtJs. To return a string use any other value.
	 *
	 * @return array
	 *
	 * @since Version 2.5.2
	 * @author Dasha Salo
	 */
	static function getProductList($resultProductList, $outputMathod = 'ECHO')
	{
		$itemCount = count($resultProductList);
		$outputString = '';

		$smarty = SmartyObj::newSmarty('AdminProductPricing');

		$outputString = "[['', '".$smarty->get_config_vars('str_LabelDefault')."']";
		for ($i = 0; $i < $itemCount; $i++)
		{
			$item = $resultProductList[$i];
			$outputString .= ",['" . $item['code'] . "',";
			$outputString .= "'" . $item['code'] .' - '. UtilsObj::encodeString($item['name'], true) . "']";
		}

		$outputString .= ']';

		if ($outputMathod == 'ECHO')
		{
			echo $outputString;
			return '';
		}
		else
		{
			return $outputString;
		}
	}


	/**
	 * Echos license key and product lists. To use for AJAX.
	 *
	 * Modified: -
	 *
	 * @param array $resultLicenseKeys
	 * @param array $resultProducts
	 *
	 * @return string
	 *
	 * @since Version 2.5.2
	 * @author Dasha Salo
	 */
	static function getCompaniesLicensekeysAndProducts($resultLicenseKeys, $resultProducts)
	{
		$tableDataArrayLicenseKeys = AjaxAPI_view::getLicensekeysTable($resultLicenseKeys);
		$tableDataLicenseKeys = $tableDataArrayLicenseKeys['licensekeytable'];

		$dataStringProducts = AjaxAPI_view::getProductList($resultProducts, 'OTHER');

		echo "['".$tableDataLicenseKeys."', ".$dataStringProducts."]";
	}


	static function getPriceLists($pResultArray)
	{
		$smarty = SmartyObj::newSmarty('AdminProductPricing');

		// check to see whether the Custom price should be displayed as an option in the drop down.
		if ($pResultArray['displaycustom'] == '1')
		{
			array_unshift($pResultArray['pricelists'], Array('id' =>'-1', 'price' => '', 'pricelistcode' => '', 'pricelistlocalcode' => '', 'pricelistname' => $smarty->get_config_vars('str_LabelCustomPrice') , 'qtyisdropdown' => '0', 'isactive' => '0' , 'decimalplaces' => '0', 'taxcode' => ''));
		}

		$string = '[';

		$itemCount = count($pResultArray['pricelists']);

		for ($i = 0; $i < $itemCount; $i++)
		{
			$item = $pResultArray['pricelists'][$i];

			$string .= "['" . $item['id'] . "',";
			$string .= "'" . $item['pricelistlocalcode'] . "',";

			if($item['id'] == '-1')
			{
				$string .= "'" . UtilsObj::encodeString($item['pricelistname'], true) . "',";
			}
			else
			{
				$string .= "'" . UtilsObj::encodeString($item['pricelistlocalcode']." - ".$item['pricelistname'], true) . "',";
			}

			$string .= "'" . $item['price'] . "',";
			$string .= "'" . $item['isactive'] . "',";
			$string .= "'" . $item['decimalplaces'] . "',";
			$string .= "'" . $item['qtyisdropdown'] . "',";
			$string .= "'" . $item['taxcode'] . "']";

			if ($i != $itemCount - 1)
			{
				$string .= ",";
			}
		}
		$string .= ']';

		echo $string;
	}


	/**
	 * Echos the change component pane to the quantity page during order process.
	 *
	 * @param array $pResultArray
	 *
	 * @return NIL
	 *
	 * @since Version 3.0.0
	 * @author Steffen Haugk
	 */
    static function changeComponentLarge($pResultArray)
    {
        global $gSession;

		// get quantity based on order line id
        $quantity = 1;
		foreach ($gSession['items'] as $item)
		{
			if ($item['orderlineid'] == $pResultArray['orderlineid'])
			{
		        $quantity = $item['itemqty'];
			}
		}

        $smarty = SmartyObj::newSmarty('Order', $gSession['webbrandcode'], $gSession['webbrandapplicationname']);

        // include the system language selector
		$languageHTMLList = LocalizationObj::buildSystemLanguageList(UtilsObj::getBrowserLocale(), false);
        $smarty->assign('systemlanguagelist', $languageHTMLList);
        $smarty->assign('time', time());

        $itemCount = count($pResultArray['component']);
        for ($i = 0; $i < $itemCount; $i++)
        {
			$pResultArray['component'][$i]['name'] = UtilsObj::escapeInputForHTML($pResultArray['component'][$i]['name']);
			$pResultArray['component'][$i]['info'] = UtilsObj::escapeInputForHTML($pResultArray['component'][$i]['info']);
			$pResultArray['component'][$i]['assetrequest'] = UtilsObj::getAssetRequest($pResultArray['component'][$i]['code'], 'components');
        }

        $smarty->assign('componentlist', $pResultArray['component']);
        $smarty->assign('componentcount', $itemCount);
        $smarty->assign('componentcode', $pResultArray['defaultcode']);
        $smarty->assign('imageurl', UtilsObj::getBrandedWebUrl());

        $smarty->assign('previousstage', $pResultArray['previousstage']);
        $smarty->assign('stage', 'qty');
        $smarty->assign('section', $pResultArray['sectionorderlineid']);
        $smarty->assign('sectionname', UtilsObj::escapeInputForHTML(LocalizationObj::getLocaleString($pResultArray['sectionname'], $gSession['browserlanguagecode'], true)));
        $smarty->assign('sectionorderlineid', $pResultArray['sectionorderlineid']);
        $smarty->assign('orderlineid', $pResultArray['orderlineid']);

        $smarty->displayLocale('order/changecomponent_large.tpl');
    }


	/**
	 * Echos one orderline, e.g. after quantity or component has been updated.
	 *
	 * @param array $pResultArray
	 *
	 * @return NIL
	 *
	 * @since Version 3.0.0
	 * @author Steffen Haugk
	 */
    static function updateOrderLineLarge($pResultArray, $pReturn = true, $pAjaxCall = false)
    {
		global $gSession;

        $smarty = SmartyObj::newSmarty('Order', $gSession['webbrandcode'], $gSession['webbrandapplicationname']);

        $stage = 'qty';
        $strLabelNotAvailable = $smarty->get_config_vars('str_LabelNotAvailable');
		$strLabelRemove = $smarty->get_config_vars('str_LabelRemove');
		$strLabelAdd = $smarty->get_config_vars('str_LabelAdd');
		$strLabelChange = $smarty->get_config_vars('str_LabelChange');
		$orderLineData = Order_view::displayOrderLineJobTicket($pResultArray, 'qty', $pResultArray['itemindex'], $pResultArray['orderlineid'], $smarty);
		//check the validity of price for all the order
        $orderCanContinueResult = self::checkOrderCanContinue($smarty);

		// number of order items
		$orderItemsCount = count($gSession['items']);
        $smarty->assign('orderitemscount', $orderItemsCount);
        $smarty->assign('currencyname', LocalizationObj::getLocaleString($gSession['order']['currencyname'], $gSession['browserlanguagecode'], true));
		$smarty->assign('orderline', $orderLineData);
		$smarty->assign('stage', $stage);

        $orderLineHTML = $smarty->fetchLocale('order/orderline_large.tpl', $gSession['browserlanguagecode']);
		$orderLineHTML = rawurlencode($orderLineHTML);

		$orderFooterSections = Order_view::prepareSections($gSession['order']['orderFooterSections'], -1, $stage, $strLabelNotAvailable, $strLabelChange, $strLabelAdd, $strLabelRemove);
        $orderFooterCheckboxes = Order_view::prepareCheckboxes($gSession['order']['orderFooterCheckboxes'], -1, $strLabelNotAvailable, $strLabelAdd, $strLabelRemove);

        $showPricesWithTax = ($gSession['order']['showpriceswithtax'] == 1) ? true : false;
		$showTaxBreakdown = ($gSession['order']['showtaxbreakdown'] == 1) ? true : false;
		$showZeroTax = ($gSession['order']['showzerotax'] == 1) ? true : false;
		$differentTaxRates = ($gSession['items'][0]['itemtaxrate'] != $gSession['shipping'][0]['shippingratetaxrate']) ? true : false;
		$showItemTax = false;

        if ($showTaxBreakdown && $differentTaxRates == true)
		{
			if ($gSession['items'][0]['itemtaxtotal'] != 0 || $showZeroTax)
			{
				$showItemTax = true;
			}

			if ($gSession['shipping'][0]['shippingratetaxtotal'] != 0 || $showZeroTax)
			{
				$showShippingTax = true;
			}
		}

		if (($showItemTax == true && $showPricesWithTax==false) || ((($gSession['order']['voucherdiscountsection'] == 'TOTAL') && ($differentTaxRates == true)) && ($gSession['order']['voucheractive'] == 1)))
		{
			$orderFooterSubTotalName = 'str_LabelOrderFooterSubTotal';
		}
		else
		{
			$orderFooterSubTotalName = 'str_LabelOrderFooterTotal';
		}

        $smarty->assign('call_action', 'update'); // updating display order
		$smarty->assign('orderfootersubtotalname',$smarty->get_config_vars($orderFooterSubTotalName));
		$smarty->assign('hasorderfooter', $gSession['order']['orderfootersubtotal']);
		$smarty->assign('orderfootersubtotal', Order_view::formatOrderCurrencyNumber($gSession['order']['orderfootersubtotal'], $stage));
		$smarty->assign('orderfootertotalname',$smarty->get_config_vars('str_LabelOrderFooterTotal'));
		$smarty->assign('orderfootertotal', Order_view::formatOrderCurrencyNumber($gSession['order']['orderfootertotal'], $stage));

        if($showPricesWithTax)
        {
            $smarty->assign('orderfooteritemstotalsell', Order_view::formatOrderCurrencyNumber($gSession['order']['orderfootertotalwithtaxnodiscount'], $stage));
        }
        else
        {
            $smarty->assign('orderfooteritemstotalsell', Order_view::formatOrderCurrencyNumber($gSession['order']['orderfootertotalnotaxnodiscount'], $stage));
        }

        $smarty->assign('orderfootersections', $orderFooterSections);
		$smarty->assign('orderfootercheckboxes', $orderFooterCheckboxes);

        $orderFooterHTML = $smarty->fetchLocale('order/orderfooter_large.tpl', $gSession['browserlanguagecode']);
        $orderFooterHTML = rawurlencode($orderFooterHTML);

        $dataArray = array(
            'orderlineid' => $orderLineData['orderlineid'],
            'quantity' => $orderLineData['itemqty'],
            'hasproductprice' => ($orderLineData['hasproductprice'] ? 1 : 0)
        );

        $returnArray = array(
            'orderLineHTML' => $orderLineHTML,
            'orderFooterHTML' => $orderFooterHTML,
            'data' => $dataArray,
            'ordercancontinue' => $orderCanContinueResult,
            'vouchermessage' => $smarty->get_config_vars($pResultArray['vouchermessage'])
        );

        if($pReturn)
        {
            header('Content-Type: application/json; charset=utf-8');

            echo json_encode($returnArray);
        }
        else
        {
            return $returnArray;
        }
	}

    static function checkOrderCanContinue($psmarty){

        global $gSession;

        $countItems = count($gSession['items']);
        $count = 0;
        $orderCanContinue = true;
        while(($count < $countItems) && $orderCanContinue){
            $items = $gSession['items'][$count];
            $orderLineData = Order_view::displayOrderLineJobTicket($items, 'qty', $count, $items['orderlineid'], $psmarty);

            Order_view::orderCanContinue($orderLineData['sections'], $orderCanContinue, true);

            if ($orderCanContinue)
            {
                Order_view::orderCanContinue($orderLineData['itempictures'], $orderCanContinue, false);
            }

            if ($orderCanContinue)
            {
                Order_view::orderCanContinue($items['lineFooterCheckboxes'], $orderCanContinue, false);

                if ($orderCanContinue)
                {
                    Order_view::orderCanContinue($items['lineFooterSections'], $orderCanContinue, true);
                }
            }

            if ($orderCanContinue)
            {
                Order_view::orderCanContinue($items['checkboxes'], $orderCanContinue, false);
            }

            if ($orderCanContinue)
            {
                Order_view::orderCanContinue($gSession['order']['orderFooterCheckboxes'], $orderCanContinue, false);

                if ($orderCanContinue)
                {
                    Order_view::orderCanContinue($gSession['order']['orderFooterSections'], $orderCanContinue, true);
                }
            }
            $count++;
        }
        return ($orderCanContinue == true ? 1 : 0);
    }


    static function updateOrderLineAllLarge($pResultArray)
    {
        $arrayJson = array();
        foreach($pResultArray as $sKey => $result)
        {
            $result['content'] = $sKey;
            $arrayJson[] = $result;
        }

        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($arrayJson);
    }

    static function updateOrderLineAllSmall($pResultArray, $pOrderTotal)
    {
        global $gSession;


        $resultArray = array();
        $arrayJson = array();
        foreach($pResultArray as $sKey => $result)
        {
            $result['content'] = $sKey;
            $arrayJson[] = $result;
        }
        $resultArray['data'] = $arrayJson;

		$orderTotal = Order_view::formatOrderCurrencyNumber($pOrderTotal, 'qty');
        $resultArray['orderTotal'] = $orderTotal;

        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($resultArray);
    }

    static function getComponentCategories($pResultArray)
	{
		$string = '[';

		$itemCount = count($pResultArray);

		for ($i = 0; $i < $itemCount; $i++)
		{
			$item = $pResultArray[$i];

			$string .= "['" . $item['id'] . "',";
			$string .= "'" .  $item['companycode'] . "',";
			$string .= "'" .  $item['code'] . "',";

			if ($item['code'] != 'SECTIONS')
			{
				$string .= "'" . UtilsObj::encodeString(LocalizationObj::getLocaleString($item['name'], '', true),true) . "',";
			}
			else
			{
				$string .= "'" . $item['name'] . "',";
			}

			$string .= "'" . UtilsObj::encodeString(LocalizationObj::getLocaleString($item['prompt'], '', true),true) . "',";
			$string .= "'" . $item['pricingmodel'] . "',";
			$string .= "'" . $item['islist'] . "',";
			$string .= "'" . $item['active'] . "',";
			$string .= "'" . $item['requirespagecount']. "']";

			if ($i != $itemCount - 1)
			{
				$string .= ",";
			}
		}
		$string .= ']';

		echo $string;
	}

	static function updateOrderSummary($pTotalsArray)
	{
		global $gSession;

		$smarty = SmartyObj::newSmarty('Order', $gSession['webbrandcode'], $gSession['webbrandapplicationname']);

		$itemTotal = Order_view::formatOrderCurrencyNumber($pTotalsArray['itemtotal'], 'qty');
		$shippingCost = Order_view::formatOrderCurrencyNumber($pTotalsArray['shippingcost'], 'qty');
		$orderTotal = Order_view::formatOrderCurrencyNumber($pTotalsArray['ordertotal'], 'qty');

		$responseHtml = array();

		$responseHtml['htmlCartSummary'] = '<div class="contentDotted">
                                                <div class="titleDetailPanel">'. $smarty->get_config_vars('str_LabelOrderItemListItemTotal') . ': </div>
                                                <div class="sidebaraccount_gap priceBold">' . $itemTotal . '</div>
                                                <div class="contentDottedImage"></div>
                                            </div>
                                            <div class="contentDotted">
                                                <div class="titleDetailPanel">'. $smarty->get_config_vars('str_LabelOrderShippingCost') . ': </div>
                                                <div class="sidebaraccount_gap priceBold">' . $shippingCost . '</div>
                                                <div class="contentDottedImage"></div>
                                            </div>
                                            <div class="content">
                                                <div class="titleDetailPanelBold">'. $smarty->get_config_vars('str_LabelOrderTotal') . ': </div>
                                                <div class="order-line-price-panel sidebaraccount_gap" id="ordertotaltopayvalueside">' . $orderTotal . '</div>
                                            </div>';
		$responseHtml['htmlOrderSummary'] = '';

		echo json_encode($responseHtml);
	}

	static function updateCollectFromStoreItemSubTotal($pItemSubTotal)
	{
		global $gSession;

		$formattedItemSubTotal = UtilsObj::formatCurrencyNumber($pItemSubTotal, $gSession['order']['currencydecimalplaces'], $gSession['browserlanguagecode'], $gSession['order']['currencysymbol'], $gSession['order']['currencysymbolatfront']);

		echo $formattedItemSubTotal;
	}

    static function getTaxCodeList($pResultArray)
    {

    	$smarty = SmartyObj::newSmarty('');

    	array_unshift($pResultArray, Array('id' => 0, 'code' => '', 'name' => $smarty->get_config_vars('str_LabelNone')));

    	$string = '[';

		$itemCount = count($pResultArray);

		for ($i = 0; $i < $itemCount; $i++)
		{
			$item = $pResultArray[$i];

			$string .= "['" . $item['id'] . "',";
			$string .= "'" . $item['code'] . "',";

			if ($item['code'] != '')
			{
				$string .= "'" . $item['code'] . ' - ' . UtilsObj::encodeString(LocalizationObj::getLocaleString($item['name'], '', true),true) . ' - ' . number_format($item['rate'], 2, '.', '') . "%']";
			}
			else
			{
				$string .= "'" . $item['name'] . "']";

			}

			if ($i != $itemCount - 1)
			{
				$string .= ",";
			}
		}

		$string .= ']';

		echo $string;
    }

    static function getTermsAndConditions($pResultArray)
    {
    	global $gSession;

		$smarty = SmartyObj::newSmarty('TermsAndConditions', $gSession['webbrandcode'], $gSession['webbrandapplicationname']);

		if ($pResultArray['template'] == 'ORDER')
		{
            $smarty->displayLocale('order/ordertermsandconditions.tpl');
        }
		else
		{
            $smarty->displayLocale('newaccounttermsandconditions.tpl');
		}
    }

    static function changeBillingAddressDisplay($pResultArray)
    {
        $resultArray = Order_view::changeAddressDisplay($pResultArray, 'billing', false, false);
        echo json_encode($resultArray);
    }

    static function changeShippingMethod($pResultArray)
    {
        $resultArray = Order_view::displayJobTicket($pResultArray, 'shipping', true, false, true, false, 'shipping');
		$resultArray['forcechangeaddressdisplay'] = false;
        echo json_encode($resultArray);
    }

    static function changeShippingAddressDisplay($pResultArray)
    {
        $resultArray = Order_view::changeAddressDisplay($pResultArray, 'shipping', false, false);
        $resultArray['forcechangeaddressdisplay'] = true;

        echo json_encode($resultArray);
    }

    static function changeAddressRefresh($pResultArray)
    {
        $resultArray = Order_view::displayJobTicket($pResultArray, 'shipping', true, false, true, true, 'shipping');
        echo json_encode($resultArray);
    }

    static function orderContinueAjax($pResultArray)
    {
        global $gSession;

        if ($pResultArray['result'] == true)
        {
            $resultArray = Order_view::orderContinueAjax($pResultArray);

			// If the result from orderContinueAjax is not an array decode it so it is.
			if (! is_array($resultArray))
			{
				$resultArray = json_decode($resultArray, true);
			}

            $resultArray['result'] = true;
            echo json_encode($resultArray);
        }
        else
        {
            $smarty = SmartyObj::newSmarty('Login', $gSession['webbrandcode'], $gSession['webbrandapplicationname']);
            if ($pResultArray['message'] != '')
            {
                $pResultArray['message'] = $smarty->get_config_vars($pResultArray['message']);
            }
            echo json_encode($pResultArray);
        }
    }

    static function selectStoreDisplay($pResultArray)
    {
        $resultArray = Order_view::selectStoreDisplay($pResultArray['store']);
        if ($_GET['refreshshipping'] == true)
        {
            $resultArray['shipping'] = Order_view::displayJobTicket($pResultArray['shipping'], 'shipping', true, false, true, false, 'shipping', $pResultArray['store']['removestore']);
        }
        echo json_encode($resultArray);
    }

    static function selectStore($pResultArray)
    {
        global $gSession;

        // get store address
        $shippingMethodCode = $gSession['shipping'][0]['shippingmethodcode'];
        $formattedStoreAddress = UtilsAddressObj::formatAddress($gSession['shipping'][0]['shippingMethods'][$shippingMethodCode]['storeAddress'], 'store', "\r");
        $formattedStoreAddress = UtilsObj::encodeString($formattedStoreAddress, false);
        $formattedStoreAddress = str_replace("\r", ",", $formattedStoreAddress);

        $resultArray = Order_view::displayJobTicket($pResultArray, 'shipping', true, false, true, false, 'shipping');

        echo json_encode(array('storeid' => $gSession['shipping'][0]['storeid'], 'storeaddress' => $formattedStoreAddress, 'template' => $resultArray['template']));
    }

    static function orderCancel($pMainWebSiteURL)
    {
        $resultArray = Order_view::displayCancellation($pMainWebSiteURL);
        echo json_encode($resultArray);
    }

    static function changeComponentSmall($pResultArray, $pReturn = false)
    {
        global $gSession;

		// get quantity based on order line id
        $quantity = 1;
		foreach ($gSession['items'] as $item)
		{
			if ($item['orderlineid'] == $pResultArray['orderlineid'])
			{
		        $quantity = $item['itemqty'];
			}
		}

        $smarty = SmartyObj::newSmarty('Order', $gSession['webbrandcode'], $gSession['webbrandapplicationname']);
        $smarty->assign('time', time());

        $itemCount = count($pResultArray['component']);
        for ($i = 0; $i < $itemCount; $i++)
        {
			$pResultArray['component'][$i]['name'] = UtilsObj::escapeInputForHTML($pResultArray['component'][$i]['name']);
			$pResultArray['component'][$i]['assetrequest'] = UtilsObj::getAssetRequest($pResultArray['component'][$i]['code'], 'components');
        }

        $smarty->assign('componentlist', $pResultArray['component']);
        $smarty->assign('componentcount', $itemCount);
        $smarty->assign('componentcode', $pResultArray['defaultcode']);
        $smarty->assign('imageurl', UtilsObj::getBrandedWebUrl());

        $smarty->assign('previousstage', $pResultArray['previousstage']);
        $smarty->assign('stage', 'qty');
        $smarty->assign('section', $pResultArray['sectionorderlineid']);
        $smarty->assign('sectionname', LocalizationObj::getLocaleString($pResultArray['sectionname'], $gSession['browserlanguagecode'], true));
        $smarty->assign('sectionorderlineid', $pResultArray['sectionorderlineid']);
        $smarty->assign('orderlineid', $pResultArray['orderlineid']);

        if ($pReturn)
        {
            return $smarty->fetchLocale('order/changecomponent_small.tpl');
        }
        else
        {
            $smarty->displayLocale('order/changecomponent_small.tpl');
        }
    }

    static function updateOrderLineSmall($pResultArray, $pReturn, $pOrderTotal = -1)
    {
		global $gSession;

        $smarty = SmartyObj::newSmarty('Order', $gSession['webbrandcode'], $gSession['webbrandapplicationname']);

        $stage = 'qty';
        $strLabelNotAvailable = $smarty->get_config_vars('str_LabelNotAvailable');
		$orderLineData = Order_view::displayOrderLineJobTicket($pResultArray, 'qty', $pResultArray['itemindex'], $pResultArray['orderlineid'], $smarty);
		//check the validity of price for all the order
        $orderCanContinueResult = self::checkOrderCanContinue($smarty);

		// number of order items
		$orderItemsCount = count($gSession['items']);
        $smarty->assign('orderitemscount', $orderItemsCount);
        $smarty->assign('currencyname', LocalizationObj::getLocaleString($gSession['order']['currencyname'], $gSession['browserlanguagecode'], true));
		$smarty->assign('orderline', $orderLineData);
		$smarty->assign('stage', $stage);

        $orderLineHTML = $smarty->fetchLocale('order/orderline_small.tpl', $gSession['browserlanguagecode']);
		$orderLineHTML = rawurlencode($orderLineHTML);

		$orderFooterSections = Order_view::prepareSections($gSession['order']['orderFooterSections'], -1, $stage, $strLabelNotAvailable, '', '', '');
        $orderFooterCheckboxes = Order_view::prepareCheckboxes($gSession['order']['orderFooterCheckboxes'], -1, $strLabelNotAvailable, '', '');

        $showPricesWithTax = ($gSession['order']['showpriceswithtax'] == 1) ? true : false;
		$showTaxBreakdown = ($gSession['order']['showtaxbreakdown'] == 1) ? true : false;
		$showZeroTax = ($gSession['order']['showzerotax'] == 1) ? true : false;
		$differentTaxRates = ($gSession['items'][0]['itemtaxrate'] != $gSession['shipping'][0]['shippingratetaxrate']) ? true : false;
		$showItemTax = false;

        if ($showTaxBreakdown && $differentTaxRates == true)
		{
			if ($gSession['items'][0]['itemtaxtotal'] != 0 || $showZeroTax)
			{
				$showItemTax = true;
			}

			if ($gSession['shipping'][0]['shippingratetaxtotal'] != 0 || $showZeroTax)
			{
				$showShippingTax = true;
			}
		}

		if (($showItemTax == true && $showPricesWithTax==false) ||
            ((($gSession['order']['voucherdiscountsection'] == 'TOTAL') && ($differentTaxRates == true)) && ($gSession['order']['voucheractive'] == 1)))
		{
			$orderFooterSubTotalName = 'str_LabelOrderFooterSubTotal';
		}
		else
		{
			$orderFooterSubTotalName = 'str_LabelOrderFooterTotal';
		}

        $smarty->assign('call_action', 'update'); // updating display order
		$smarty->assign('orderfootersubtotalname',$smarty->get_config_vars($orderFooterSubTotalName));
		$smarty->assign('hasorderfooter', $gSession['order']['orderfootersubtotal']);
		$smarty->assign('orderfootersubtotal', Order_view::formatOrderCurrencyNumber($gSession['order']['orderfootersubtotal'], $stage));
		$smarty->assign('orderfootertotalname',$smarty->get_config_vars('str_LabelOrderFooterTotal'));
		$smarty->assign('orderfootertotal', Order_view::formatOrderCurrencyNumber($gSession['order']['orderfootertotal'], $stage));

        if($showPricesWithTax)
        {
            $smarty->assign('orderfooteritemstotalsell', Order_view::formatOrderCurrencyNumber($gSession['order']['orderfootertotalwithtaxnodiscount'], $stage));
        }
        else
        {
            $smarty->assign('orderfooteritemstotalsell', Order_view::formatOrderCurrencyNumber($gSession['order']['orderfootertotalnotaxnodiscount'], $stage));
        }

        $smarty->assign('orderfootersections', $orderFooterSections);
		$smarty->assign('orderfootercheckboxes', $orderFooterCheckboxes);

        if ($orderLineData['orderlineid'] == TPX_ORDERFOOTER_ID)
        {
            $componentDetailHTML = $smarty->fetchLocale('order/orderfootercomponentdetail_small.tpl', $gSession['browserlanguagecode']);
        }
        else
        {
            $componentDetailHTML = $smarty->fetchLocale('order/componentdetail_small.tpl', $gSession['browserlanguagecode']);
        }
        $componentDetailHTML = rawurlencode($componentDetailHTML);

        if ($orderLineData['orderlineid'] == TPX_ORDERFOOTER_ID)
        {
            $subcomponentDetailHTML = $smarty->fetchLocale('order/orderfootersubcomponentdetail_small.tpl', $gSession['browserlanguagecode']);
        }
        else
        {
            $subcomponentDetailHTML = $smarty->fetchLocale('order/subcomponentdetail_small.tpl', $gSession['browserlanguagecode']);
        }
        $subcomponentDetailHTML = rawurlencode($subcomponentDetailHTML);

        $orderFooterHTML = $smarty->fetchLocale('order/orderfooter_small.tpl', $gSession['browserlanguagecode']);
        $orderFooterHTML = rawurlencode($orderFooterHTML);

		$orderTotal = Order_view::formatOrderCurrencyNumber($pOrderTotal, $stage);

        $dataArray = array(
            'orderlineid' => $orderLineData['orderlineid'],
            'sectionorderlineid' => $pResultArray['sectionorderlineid'],
            'quantity' => $orderLineData['itemqty'],
            'hasproductprice' => ($orderLineData['hasproductprice'] ? 1 : 0)
        );

        $data = json_encode($dataArray);

        $returnArray = array(
            'orderLineHTML' => $orderLineHTML,
            'componentDetailHTML' => $componentDetailHTML,
            'subcomponentDetailHTML' => $subcomponentDetailHTML,
            'orderFooterHTML' => $orderFooterHTML,
            'orderTotal' =>  $orderTotal,
            'data' => $dataArray,
            'ordercancontinue' => $orderCanContinueResult,
            'vouchermessage' => $smarty->get_config_vars($pResultArray['vouchermessage']));

        if ($pReturn)
        {
            $json = json_encode($returnArray);
            header('Content-Type: application/json; charset=utf-8');
            echo $json;
        }
        else
        {
            return $returnArray;
        }
	}

    static function changeGiftCard($pResultArray)
    {
        if ($pResultArray['action'] == 'add')
        {
            Order_view::addGiftCard($pResultArray['canuseaccount']);
        }
        else
        {
            Order_view::deleteGiftCard($pResultArray['canuseaccount']);
        }
    }

    static function renameExistingOnlineProject($pResultArray)
    {
		global $gSession;

		if (!$pResultArray['maintenancemode'])
		{
			if ($pResultArray['projectdetails']['nameexists'] != '')
			{
	            $smarty = SmartyObj::newSmarty('Customer', $gSession['webbrandcode'], $gSession['webbrandapplicationname']);
				$pResultArray['projectdetails']['nameexists'] = $smarty->get_config_vars($pResultArray['projectdetails']['nameexists']);
			}
			else if ($pResultArray['projectdetails']['restoremessage'] != 0)
			{
				$smarty = SmartyObj::newSmarty('Customer', $gSession['webbrandcode'], $gSession['webbrandapplicationname']);
				switch ($pResultArray['projectdetails']['restoremessage'])
				{
					case TPX_ARCHIVE_RESTORE_FAILED:
					{
						$pResultArray['projectdetails']['restoremessage'] = $smarty->get_config_vars('str_MessageUnableToRestoreProject');
						break;
					}
					case TPX_ARCHIVE_RESTORE_IN_PROGRESS:
					{
						$pResultArray['projectdetails']['restoremessage'] = $smarty->get_config_vars('str_MessageRestoringProject');
						break;
					}
				}
			}
		}

		echo json_encode($pResultArray);
    }

    static function openOnlineProject($pResultArray)
	{
		global $gSession;

		if ($gSession['userdata']['ssotoken'] != '')
		{
			$webURL = UtilsObj::getBrandedWebUrl($gSession['webbrandcode']);

			$urlPartsArray = parse_url($webURL);

			header('Access-Control-Allow-Origin: ' . $urlPartsArray['scheme'] . "://" . $urlPartsArray['host']);
			header('Access-Control-Allow-Credentials: true');
		}

		header('Content-Type: application/json; charset=utf-8');

		if (($pResultArray['error'] != '') || ($pResultArray['errorparam'] != ''))
		{
			if ($pResultArray['projectdetails']['restoremessage'] != 0)
			{
				$smarty = SmartyObj::newSmarty('Customer', $gSession['webbrandcode'], $gSession['webbrandapplicationname']);
				switch ($pResultArray['projectdetails']['restoremessage'])
				{
					case TPX_ARCHIVE_RESTORE_FAILED:
					{
						$pResultArray['errorparam'] = $smarty->get_config_vars('str_MessageUnableToRestoreProject');
						break;
					}
					case TPX_ARCHIVE_RESTORE_IN_PROGRESS:
					{
						$pResultArray['errorparam'] = $smarty->get_config_vars('str_MessageRestoringProject');
						break;
					}
				}
			}
			else
			{
				$smarty = SmartyObj::newSmarty('CommunicationFailed', $gSession['webbrandcode'], $gSession['webbrandapplicationname']);
				$pResultArray['errorparam'] = $smarty->get_config_vars('str_titleCommunicationFailed');
			}
		}

		echo json_encode($pResultArray);
	}

	static function completeOrder($pResultArray)
	{
		header('Content-Type: application/json; charset=utf-8');

		echo $pResultArray['brandurl'];
	}

	static function duplicateOnlineProject($pResultArray)
    {
		global $gSession;

		$projectDetailsArray = $pResultArray['projectdetails'];
        $resultArray = array(
			'error' => $pResultArray['error'],
			'html' => '',
			'nameexists' => '',
			'htmloption' => '',
			'maintenancemode' => $pResultArray['maintenancemode'],
			'projectref' => '',
			'productident' => $projectDetailsArray['productident'],
			'workflowtype' => $projectDetailsArray['workflowtype'],
			'projectexists' => '',
			'restoremessage' => ''
		);

		if ($resultArray['maintenancemode'] == false)
		{
			$resultArray['projectref'] = $projectDetailsArray['projectref'];
			$resultArray['projectexists'] = $projectDetailsArray['projectexists'];

			if (! empty($projectDetailsArray['restoremessage']))
			{
				$resultArray['restoremessage'] = $projectDetailsArray['restoremessage'];
			}

			if ($resultArray['projectexists'] == true)
			{
				$smarty = SmartyObj::newSmarty('Customer', $gSession['webbrandcode'], $gSession['webbrandapplicationname']);
				if ($projectDetailsArray['nameexists'] != '')
				{
					$resultArray['nameexists'] = $smarty->get_config_vars($projectDetailsArray['nameexists']);
				}
				else
				{
					if ($gSession['ismobile'] == true)
					{
						$resultArray['html'] = '
						<div class="clickable" id="contentItemBloc' . $projectDetailsArray['projectref'] . '" data-decorator="fnShowOnlineOptions" data-show="true" data-product-id="' . $projectDetailsArray['projectref'] . '">

							 <div class="outerBox outerBoxMarginBottom">

								<div class="projectLabel">

									<div class="orderProductLabel" id="orderProductLabel' . $projectDetailsArray['projectref'] . '">
										' . UtilsObj::escapeInputForHTML($projectDetailsArray['projectname']) . '
									</div> <!-- componentLabel -->

									<div class="orderProductBtnDetail"></div>

									<div class="clear"></div>

								</div> <!-- projectLabel -->

								<div class="contentDescription">

									<div class="descriptionProduct">
										' . $projectDetailsArray['productlayoutname'] . '
									</div>

									<div class="orderDetail">
										<span class="orderLabelMedium">' . $smarty->get_config_vars('str_LabelCreated') . '</span> ' . $projectDetailsArray['datecreated'] . '<br />
									</div>

								</div> <!-- contentDescription -->

							</div> <!-- outerBox -->

						</div> <!-- contentItemBloc -->';

						$str_ButtonContinueEditing = $smarty->get_config_vars('str_ButtonContinueEditing');
						$str_ButtonDuplicateProject = $smarty->get_config_vars('str_ButtonDuplicateProject');
						$str_ButtonRenameProject = $smarty->get_config_vars('str_ButtonRenameProject');
						$str_ButtonDeleteProject = $smarty->get_config_vars('str_ButtonDeleteProject');

						$resultArray['htmloption'] = '
						<div id="onlineProjectDetail' . $projectDetailsArray['projectref'] . '" data-projectname="' . UtilsObj::escapeInputForHTML($projectDetailsArray['projectname']) . '" data-productident="' . $projectDetailsArray['productident'] . '"
							data-workflowtype="' . $projectDetailsArray['workflowtype'] . '" style="display: none;">

							<div class="pageLabel" id="pageLabel' . $projectDetailsArray['projectref'] . '">
								' . UtilsObj::escapeInputForHTML($projectDetailsArray['projectname']) . '
							</div>

							<div class="outerBox outerBoxPadding outerBoxMarginBottom productDetailBloc">

								<div class="nameProduct">
								   ' . $projectDetailsArray['productlayoutname'] . '
								</div>

								 <div class="orderDetail">
									<span class="orderLabelMedium">' . $smarty->get_config_vars('str_LabelCreated') . '</span> ' . $projectDetailsArray['datecreated'] . '
								</div>

							</div> <!-- outerBox outerBoxPadding outerBoxMarginBottom productDetailBloc -->

							<div>
								<div class="btnAction btnContinue btnComplete" data-decorator="fnOnlineProjectsButtonAction" data-button="continueediting" data-wizard-mode="' . $projectDetailsArray['wizardmode'] . '" data-work-type="' . $projectDetailsArray['workflowtype'] . '">
									<div class="btnContinueContent">' . $str_ButtonContinueEditing . '</div>
								</div>

								<div class="linkAction" data-decorator="fnOnlineProjectsButtonAction" data-button="duplicate" data-wizard-mode="' . $projectDetailsArray['wizardmode'] . '" data-work-type="' . $projectDetailsArray['workflowtype'] . '">

									<div class="changeBtnText">
										' . $str_ButtonDuplicateProject . '
									</div>

									<div class="changeBtnImg">
										<img class="navigationArrow" src="' . UtilsObj::correctPath($gSession['webbrandwebroot']) . '/images/icons/change-arrow.png" alt= ">" />
									</div>

									<div class="clear"></div>

								</div> <!-- linkAction -->

								<div class="linkAction" data-decorator="fnOnlineProjectsButtonAction" data-button="rename" data-wizard-mode="' . $projectDetailsArray['wizardmode'] . '" data-work-type="' . $projectDetailsArray['workflowtype'] . '">

									<div class="changeBtnText">
										' . $str_ButtonRenameProject . '
									</div>

									<div class="changeBtnImg">
										<img class="navigationArrow" src="' . UtilsObj::correctPath($gSession['webbrandwebroot']) . '/images/icons/change-arrow.png" alt= ">" />
									</div>

									<div class="clear"></div>

								</div> <!-- linkAction -->

								<div class="linkAction" data-decorator="fnOnlineProjectsButtonAction" data-button="delete" data-wizard-mode="' . $projectDetailsArray['wizardmode'] . '" data-work-type="' . $projectDetailsArray['workflowtype'] . '">

									<div class="deleteBtnText">
										' . $str_ButtonDeleteProject . '
									</div>

								</div> <!-- linkAction -->

							</div>

						</div> <!-- productDetailXXX -->';

					}
					else
					{
						// get the browser compatibility to disable some none accessible action
						$browserArray = OnlineAPI_model::checkBrowsers();
						if ($browserArray['browsersupported'] == 1)
						{
							$canEdit = 1;
						}
						else
						{
							$canEdit = 0;
						}

						$thumbnailPath = '';

						if ($projectDetailsArray['thumbnailpath'] != '')
						{
							$thumbnailPath = UtilsObj::correctPath($pResultArray['onlinedesignerurl']) . $projectDetailsArray['thumbnailpath'];
						}

						// Determine if the Content Security Policy is active.
						$cspActive = UtilsObj::getCSPActive();

						if (($projectDetailsArray['projectpreviewthumbnail'] !== '') && ($cspActive))
						{
							// Add the thumbnail domain to the list for CSP.
							$parsedUrl = parse_url($projectDetailsArray['projectpreviewthumbnail']);
							$domain = $parsedUrl['scheme'] . '://' . $parsedUrl['host'];

							$cspBuilder = ControlCentreCSP::getInstance(UtilsObj::getGlobalValue('ac_config'));
							$cspBuilder->getBuilder()->addSource('image-src', $domain);
						}

						$html = '<div  class="contentRow"
									id="' . $projectDetailsArray['projectref'] . '" data-projectname="' . UtilsObj::escapeInputForHTML($projectDetailsArray['projectname']) . '" data-productident="' . $projectDetailsArray['productident'] . '"
									data-status="' . $projectDetailsArray['projectstatus'] . '" data-cancompleteorder="' . $projectDetailsArray['cancompleteorder'] . '" data-canedit="' . $canEdit . '" data-candelete="1" data-workflowtype="' . $projectDetailsArray['workflowtype'] . '">
									<div class="bloc_content">
										<div class="previewHolder projectRowHighLight">
											<div class="previewItem">
												<div id="img_' . $projectDetailsArray['projectref'] . '" class="product-preview-wrap">';
													if ($projectDetailsArray['projectpreviewthumbnail'] != '')
													{
														$html .= '<img src="' . $projectDetailsArray['projectpreviewthumbnail']. '" class="product-preview-image" data-asset="' . $thumbnailPath . '" alt="" />';
													}
													else if ($projectDetailsArray['thumbnailpath'] != '')
													{
													   $html .= '<img src="' . $thumbnailPath . '" class="product-preview-image" alt="" />';
													}
													else
													{
														$html .= '<img src="' . UtilsObj::correctPath($gSession['webbrandwebroot']) . '/images/no_image-2x.jpg" class="product-preview-image" alt="" />';
													}
												$html .= '
												</div>
												<div class="previewItemText onlinePreview">
													<div class="textProduct" id="name_' . $projectDetailsArray['projectref'] . '">
														' . UtilsObj::escapeInputForHTML($projectDetailsArray['projectname']) . '
													</div>
													<div class="contentDescription">
														<div class="description-product">
															' . $projectDetailsArray['productlayoutname'] . '
														</div>
														<div class="ordernumber">
															<span class="label-order-number">' . $smarty->get_config_vars('str_LabelCreated') . '</span> ' . $projectDetailsArray['datecreated'] . '
														</div>
													</div>
													<div class="online-production-status">
														<span id="statusDescription' . $projectDetailsArray['projectref'] . '" class="previewItemDetail textGreen">
                                                        ' . ($projectDetailsArray['statusdescription'] !== '' ? $smarty->get_config_vars($projectDetailsArray['statusdescription']) : '') . '
														</span>
													</div>
													<div class="clear"></div>
												</div>
											</div>
										</div>
										<div class="clear"></div>
									</div>
								</div>';

						$toReplace = array("\r\n", "\n", "\r");
						$resultArray['html'] = str_replace($toReplace, "", $html);
					}
				}
			}
			else if ($projectDetailsArray['restoremessage'] != 0)
			{
				$smarty = SmartyObj::newSmarty('Customer', $gSession['webbrandcode'], $gSession['webbrandapplicationname']);
				switch ($projectDetailsArray['restoremessage'])
				{
					case TPX_ARCHIVE_RESTORE_FAILED:
					{
						$resultArray['restoremessage'] = $smarty->get_config_vars('str_MessageUnableToRestoreProject');
						break;
					}
					case TPX_ARCHIVE_RESTORE_IN_PROGRESS:
					{
						$resultArray['restoremessage'] = $smarty->get_config_vars('str_MessageRestoringProject');
						break;
					}
				}
			}
		}

		echo json_encode($resultArray);
    }

	static function checkDeleteSession($pResultArray)
	{
		echo json_encode($pResultArray);
	}

	static function getShareOnlineProjectURL($pResultArray)
	{
		echo json_encode($pResultArray);
	}


	/*
	 *
	 */
	static function updateCompanionQty($pResultArray)
	{
		global $gSession;

		$smarty = SmartyObj::newSmarty('Order', $gSession['webbrandcode'], $gSession['webbrandapplicationname']);

		$itemTotal = Order_view::formatOrderCurrencyNumber($pResultArray['itemtotal'], 'qty');
		$shippingCost = Order_view::formatOrderCurrencyNumber($pResultArray['shippingcost'], 'qty');
		$orderTotal = Order_view::formatOrderCurrencyNumber($pResultArray['ordertotal'], 'qty');

		$pResultArray['htmlCartSummary'] = '<div class="contentDotted">
                                                <div class="titleDetailPanel">'. $smarty->get_config_vars('str_LabelOrderItemListItemTotal') . ': </div>
                                                <div class="sidebaraccount_gap priceBold">' . $itemTotal . '</div>
                                                <div class="contentDottedImage"></div>
                                            </div>
                                            <div class="contentDotted">
                                                <div class="titleDetailPanel">'. $smarty->get_config_vars('str_LabelOrderShippingCost') . ': </div>
                                                <div class="sidebaraccount_gap priceBold">' . $shippingCost . '</div>
                                                <div class="contentDottedImage"></div>
                                            </div>
                                            <div class="content">
                                                <div class="titleDetailPanelBold">'. $smarty->get_config_vars('str_LabelOrderTotal') . ': </div>
                                                <div class="order-line-price-panel sidebaraccount_gap" id="ordertotaltopayvalueside">' . $orderTotal . '</div>
                                            </div>';
		$pResultArray['htmlOrderSummary'] = '';

		echo json_encode($pResultArray);
	}

	static function getCCIRecord($pResultArray)
	{
		echo json_encode($pResultArray);
	} 
	static function processPaymentTokenResponse($pResultArray)
	{
		echo json_encode($pResultArray);
    }

    /**
     * Return the result of the check for a login being unique. 
     */
    static function processUserLoginUniqueCheck($pResultArray)
	{
        echo json_encode($pResultArray);
    }

    static function returnJSON($pResult)
	{
		echo json_encode($pResult);
	}
}

?>
