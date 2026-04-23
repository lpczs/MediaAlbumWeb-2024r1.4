<?php

require_once('../Utils/UtilsAddress.php');
require_once('../Utils/UtilsDatabase.php');
require_once('../Utils/UtilsDataExport.php');
require_once('../Utils/UtilsSmarty.php');

class AdminExportManual_model
{
	
	static function displayForm()
	{
        global $gConstants;
        
        $resultArray['countrylist'] 	    = UtilsAddressObj::getCountryList();
        $resultArray['defaultlanguagecode'] = $gConstants['defaultlanguagecode'];
        $resultArray['message'] 		    = '';
        return $resultArray;
	}


	static function report()
    {
        global $gSession;

        $start = (isset($_POST['start']) ? (integer)$_POST['start'] : 0);
        $limit = (integer)$_POST['limit'];
        $sort = $_POST['sort'];
        $dir = $_POST['dir'];
        $filterValue = $_POST['filterValue'];
        $startDate = $_POST['startDate'];
        $endDate = $_POST['endDate'];
        $dateType = $_POST['dateType'];
        $filterType = $_POST['filterType'];
        $filterValue = $_POST['filterValue'];
        $companyCode = isset($_POST['companyFilter']) ? $_POST['companyFilter'] : '';
        
        //init cache for localization
        LocalizationObj::formatLocaleDateTime('0000-00-00 00:00:00');
        UtilsObj::formatCurrencyNumber(0, 0, $gSession['browserlanguagecode'], '', '');
        
        $summaryArray = array();
        
		if ($gSession['userdata']['companycode'] != '')
		{
			$companyCode = $gSession['userdata']['companycode'];
		}
		
	    $brandCode = ($gSession['userdata']['webbrandcode'] != '') ? $gSession['userdata']['webbrandcode'] : '';
		
		if ($gSession['userdata']['usertype'] == TPX_LOGIN_BRAND_OWNER && $filterValue == '')
		{
			$filterValue = $brandCode;
			$filterType = 'BRAND';
		}

	    $sortParam = '';
        if ($sort && $dir)
        {
        	$sortParam = ' ORDER BY `' . $sort . '` ' . $dir;
        }
        
        $totalCount = 0;
        
        $tempArray = Array();
        $tempArray = DataExportObj::selectExportIdListByDate($startDate, $endDate, $dateType, $filterType, $filterValue, $companyCode);
        
        // get labels for billing and shipping details
        $smarty = SmartyObj::newSmarty('AdminExport');
        $strLabelName = $smarty->get_config_vars('str_LabelName');
        $strLabelTelephoneNumber = $smarty->get_config_vars('str_LabelTelephoneNumber');
        $strLabelEmailAddress = $smarty->get_config_vars('str_LabelEmailAddress');
        
        if (count($tempArray) > 0)
        {
	        $tempArrayString = '(' . implode(',', $tempArray) . ')';
	        
		    $dbObj = DatabaseObj::getGlobalDBConnection();
		    if ($dbObj)
			{
				if ($companyCode == '' || $gSession['userdata']['usertype'] == TPX_LOGIN_BRAND_OWNER)
				{
					if ($brandCode == '')
					{
						$stmt = $dbObj->prepare('SELECT SQL_CALC_FOUND_ROWS `ordernumber`, `orderdate`, `outputtimestamp`, `shippeddate`, `productname`, oi.qty, `webbrandcode`, `total`, 
						`currencysymbol`, `currencysymbolatfront`, `currencydecimalplaces`,
						`billingcustomertelephonenumber`, `billingcustomeremailaddress`, `billingcontactfirstname`, `billingcontactlastname`,
						`shippingcustomertelephonenumber`, `shippingcustomeremailaddress`, `shippingcontactfirstname`, `shippingcontactlastname`
						FROM `ORDERHEADER` oh
						JOIN `ORDERITEMS` oi ON (oh.id = oi.orderid)
						JOIN `ORDERSHIPPING` os ON (oi.orderid = os.orderid)
						WHERE oh.id IN '.$tempArrayString . $sortParam . ' LIMIT ' . $limit . ' OFFSET ' . $start);
					}
					else
					{
						if ($stmt = $dbObj->prepare('SELECT SQL_CALC_FOUND_ROWS `ordernumber`, `orderdate`, `outputtimestamp`, `shippeddate`, `productname`, oi.qty, `webbrandcode`, `total`, 
						`currencysymbol`, `currencysymbolatfront`, `currencydecimalplaces`,
						`billingcustomertelephonenumber`, `billingcustomeremailaddress`, `billingcontactfirstname`, `billingcontactlastname`,
						`shippingcustomertelephonenumber`, `shippingcustomeremailaddress`, `shippingcontactfirstname`, `shippingcontactlastname`
						FROM `ORDERHEADER` oh
						JOIN `ORDERITEMS` oi ON (oh.id = oi.orderid)
						JOIN `ORDERSHIPPING` os ON (oi.orderid = os.orderid)
						WHERE oh.id IN '.$tempArrayString.' AND oh.webbrandcode = ?'. $sortParam . ' LIMIT ' . $limit . ' OFFSET ' . $start))
						{
							$stmt->bind_param('s', $brandCode);
						}
					}
				}
				else
				{

					if ($stmt = $dbObj->prepare('SELECT SQL_CALC_FOUND_ROWS `ordernumber`, `orderdate`, `outputtimestamp`, `shippeddate`, `productname`, oi.qty, `webbrandcode`, `total`, 
					`currencysymbol`, `currencysymbolatfront`, `currencydecimalplaces`,
					`billingcustomertelephonenumber`, `billingcustomeremailaddress`, `billingcontactfirstname`, `billingcontactlastname`,
					`shippingcustomertelephonenumber`, `shippingcustomeremailaddress`, `shippingcontactfirstname`, `shippingcontactlastname`
					FROM `ORDERHEADER` oh
					JOIN `ORDERITEMS` oi ON (oh.id = oi.orderid)
					JOIN `ORDERSHIPPING` os ON (oi.orderid = os.orderid)
					WHERE oh.id IN '.$tempArrayString.' AND oi.currentcompanycode = ? ' . $sortParam . ' LIMIT ' . $limit . ' OFFSET ' . $start))
					{
						$stmt->bind_param('s', $companyCode);
					}
				}
				
				if ($stmt->bind_result($ordernumber, $orderdate, $outputtimestamp, $shippeddate, $productname, $qty, $webBrandCode, $total, 
										$currencysymbol, $currencysymbolatfront, $currencydecimalplaces,
										$billingcustomertelephonenumber, $billingcustomeremailaddress, $billingcontactfirstname, $billingcontactlastname,
										$shippingcustomertelephonenumber, $shippingcustomeremailaddress, $shippingcontactfirstname, $shippingcontactlastname))
				{
					if ($stmt->execute())
					{
						$bufArr = array();
						while ($stmt->fetch())
						{
							$bufArr['ordernumber'] = "'" . $ordernumber ."'";
							$bufArr['orderdate'] = "'" . LocalizationObj::formatLocaleDateTime($orderdate) ."'";
							$bufArr['productname'] = "'" . UtilsObj::ExtJSEscape(LocalizationObj::getLocaleString($productname, $gSession['browserlanguagecode'], true)) ."'";
							$bufArr['webbrandcode'] = "'" . UtilsObj::ExtJSEscape(($webBrandCode == '' ? '&nbsp;' : $webBrandCode)) ."'";
							$bufArr['total'] = "'" . UtilsObj::formatCurrencyNumber($total, $currencydecimalplaces, $gSession['browserlanguagecode'], $currencysymbol, $currencysymbolatfront) ."'";
        					$bufArr['shippeddate'] = "'" . ($shippeddate == '0000-00-00 00:00:00' ? '&nbsp;' : LocalizationObj::formatLocaleDateTime($shippeddate))  ."'";
							$bufArr['qty'] = "'" . $qty . "'";
        					$bufArr['billingaddress'] = "'" . UtilsObj::ExtJSEscape($billingcontactfirstname . ' ' . $billingcontactlastname) . '<br>'.UtilsObj::ExtJSEscape($billingcustomertelephonenumber) . '<br>'.UtilsObj::ExtJSEscape($billingcustomeremailaddress)."'";
        					$bufArr['shippingaddress'] = "'" . UtilsObj::ExtJSEscape($shippingcontactfirstname . ' ' . $shippingcontactlastname) . '<br>'.UtilsObj::ExtJSEscape($shippingcustomertelephonenumber) . '<br>'.UtilsObj::ExtJSEscape($shippingcustomeremailaddress)."'";
        					
							array_push($summaryArray, '[' . join(',', $bufArr) . ']');
						}
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
				
				$dbObj->close();
			}
	    }

        echo '[['.$totalCount.'],' . join(',', $summaryArray) . ']';
        
        return;
    }


    static function reportExport()
    {
        global $gSession;
       
       	$startDate = $_GET['startdate'];
        $endDate = $_GET['enddate'];       
        $filterType = $_GET['filtertype'];
        $filterValue = $_GET['filtervalue'];
        $languagecode = $_GET['languagecode'];
        $dateType = $_GET['datetype'];
        $exportFormat = $_GET['exportformat'];
        $includePaymentData = $_GET['includepaymentdata'];
        $beautifyXML = $_GET['beautifyxml'];
        $companyCode = isset($_GET['companyFilter']) ? $_GET['companyFilter'] : '';
        
        $fileName = 'Report_' . date('d_M_Y_His');
        
        // check to see if we are logged in as a brand owner. If we are then there will be no brang dropdown box for selection.
        // therefore if None is selected from the filtertype then we must make sure we set the filter type to BRAND and assign the relevant webbrandcode to the filter value.
        if (($gSession['userdata']['usertype'] == TPX_LOGIN_BRAND_OWNER) && ($filterType == ''))
		{
			$filterType = 'BRAND';
			$filterValue = $gSession['userdata']['webbrandcode'];
		}
		
       	switch ($exportFormat)
       	{
			case 'XML':
				header('Content-Type: text/xml');
				header('Content-Disposition: Attachment; filename=' . $fileName . '.xml');
				break;
			case 'TXT':
				header('Content-Type: text/tab-separated-values');
				header('Content-Disposition: Attachment; filename=' . $fileName . '.txt');
				break;
		}	
		
		header('Pragma: no-cache'); 
		header('Expires: 0');
        
		$xmlobj = DataExportObj::getExportCompleteLong($startDate, $endDate, $filterType, $filterValue, $languagecode, $dateType, $includePaymentData, $companyCode);
        $text = DataExportObj::exportDataGenerate($xmlobj, $exportFormat, $beautifyXML, 'order');
       	
       	echo $text;
       	
       	return;
 	}
}
?>
