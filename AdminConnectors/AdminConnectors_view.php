<?php

class AdminConnectors_view
{

    static function initialize()
    {
        $smarty = SmartyObj::newSmarty('AdminConnectors');

        $adminLabel = SmartyObj::getParamValue('Admin', 'str_Title');
        $smarty->assign('adminlabel', $adminLabel);
        $smarty->assign('TPX_CONNECTOR_CONNECTED', TPX_CONNECTOR_CONNECTED);

        $smarty->displayLocale('admin/connectors/connectors.tpl');
    }

    static function displayEdit($pResultArray)
    {
        $smarty = SmartyObj::newSmarty('AdminConnectors');

        $itemList = $pResultArray['webbrandinglist'];
        $itemCount = count($itemList);

        for ($i = 0; $i < $itemCount; $i++) {
            $item = $itemList[$i];
            
            $brandCode = $item['webBrandCode'];
            $licenseKeyCode = $item['id'];

            if ($brandCode == '') {
                $brandCode = $smarty->get_config_vars('str_LabelDefault');
            }

            $display = $licenseKeyCode;

            $brandListBuf[] = '["' . $brandCode . "@@" . $licenseKeyCode . '","' . $display . ' (' . $smarty->get_config_vars('str_LabelBrand') . ': ' . $brandCode . ')"]';
        }

        $webBrandingList = '[' . join(',', $brandListBuf) . ']';
        $smarty->assign('webbrandinglist', $webBrandingList);

        $selectedBrandCode = isset($pResultArray['data']['brandcode']) ? $pResultArray['data']['brandcode'] : 0;
        $selectedLicenseKeyCode = isset($pResultArray['data']['licensekeycode']) ? $pResultArray['data']['licensekeycode'] : 0;

        $selectedBrandCodeDisplay = $smarty->get_config_vars('str_LabelBrand') . ': ' . $selectedBrandCode;

        if ($selectedBrandCode == '') {
            $selectedBrandCodeDisplay = $smarty->get_config_vars('str_LabelBrand') . ': ' . $smarty->get_config_vars('str_LabelDefault');
        }

        $smarty->assign('brandinglicensekeycode', $selectedBrandCode . '@@' . $selectedLicenseKeyCode);
        $smarty->assign('brandinglicensekeycodedisplay', $selectedLicenseKeyCode . ' (' . $selectedBrandCodeDisplay . ')');
        $smarty->assign('connectorid', $pResultArray['data']['connectorid']);
        $smarty->assign('connectorurl', $pResultArray['data']['connectorurl']);
        $smarty->assign('connectorprimarydomain', $pResultArray['data']['connectorprimarydomain']);
        $smarty->assign('connectorkey', $pResultArray['data']['connectorkey']);
        $smarty->assign('connectorsecret', $pResultArray['data']['connectorsecret']);
        $smarty->assign('connectorinstallurl', $pResultArray['data']['connectorinstallurl']);
        $smarty->assign('pricesincludetax', $pResultArray['data']['pricesincludetax']);

        $smarty->displayLocale('admin/connectors/connectorsedit.tpl');
    }

    static function connectorsEdit($pResultArray)
    {
        if ($pResultArray['result'] != '') {
            $smarty = SmartyObj::newSmarty('AdminConnectors');
            echo '{"success": false, "msg": "' . $smarty->get_config_vars('str_MessageError') . '"}';
        } else {
            echo '{"success": true}';
        }
    }

    static function outputResult($pResultArray)
    {
        if ($pResultArray['error'] == '') {
            echo '{"success": true,	"msg":""}';
        } else {
            // If the error is a database error replace ^0 with the resultParam message.
            if ($pResultArray['error'] == 'str_DatabaseError') {
                $smarty = SmartyObj::newSmarty('AdminConnectors');

                // Get error string to display.
                $errorString = $smarty->get_config_vars($pResultArray['error']);

                $errorString = str_replace('^0', $pResultArray['errorparam'], $errorString);
            }

            echo '{"success": false, "msg":"' . UtilsObj::ExtJSEscape($errorString) . '"}';
        }
    }

    static function syncProductsEditDisplay($pResultArray)
    {
        global $gConstants;

        $connectorID = $pResultArray['data']['connector']['connectorid'];
        $productsActive = $pResultArray['data']['connector']['productsactive'];

        $newCount = $pResultArray['data']['connector']['newcount'];
        $updateCount = $pResultArray['data']['connector']['updatecount'];
        $shopURL = $pResultArray['data']['connector']['shopurl'];

        $smarty = SmartyObj::newSmarty('AdminConnectors');
        $smarty->assign('optionms', ($gConstants['optionms'] ? true : false));

        $smarty->assign('shopurl', $shopURL);

        $smarty->assign('connectorid', $connectorID);
        $smarty->assign('productsactive', $productsActive);

        $smarty->assign('newcount', $newCount);
        $smarty->assign('updatecount', $updateCount);

        $smarty->assign('inprogress', $pResultArray['data']['connector']['inprogress']);

        $smarty->displayLocale('admin/connectors/connectorssyncproducts.tpl');
    }

    static function syncProductsEdit($pResultArray)
    {
        if ($pResultArray['error'] != '') {
            $smarty = SmartyObj::newSmarty('AdminConnectors');
            echo '{"success": false, "msg": "' . $smarty->get_config_vars('str_MessageError') . '"}';
        } else {
            echo '{"success": true}';
        }
    }

    static function connectorsRebuildTheme($pResult)
    {
        if (count($pResult) > 0) {
            echo '{"success": false, "msg": "' . $pResult[0] . '"}';
        } else {
            echo '{"success": true}';
        }
    }

    static function connectorsInstallTaopixTheme($pResult)
	{
		if (count($pResult) > 0) {
            $smarty = SmartyObj::newSmarty('AdminConnectors');
            $errorMsg = $pResult[0];
            if ($pResult[0] == 'EXISTS')
            {
                $errorMsg = $smarty->get_config_vars('str_TaopixThemeAlreadyExists');
            }

            echo '{"success": false, "msg": "' . $errorMsg . '"}';
        } else {
            echo '{"success": true}';
        }
	}

    static function connectorsList($pResultArray)
    {
        if ($pResultArray['error'] == '') {
            $smarty = SmartyObj::newSmarty('AdminConnectors');
            $totalCount = $pResultArray['totalCount'];
            $connectorsArray = [];

            foreach ($pResultArray['data'] as $connector) {
                $connectorItem = [];
                $folderName = '';
                $connectorStatusText = '';
                $connectorStatus = 0;

                $recordID = $connector['recordID'];
                $brandingCode = $connector['brandingCode'];
                $brandID = $connector['brandID'];
                $brandingName = $connector['brandingName'];
                $brandingCompany = $connector['brandingCompany'];
                $applicationName = $connector['applicationName'];
                $connectorURL = $connector['connectorURL'] . '.myshopify.com';
                $connectorURLLink = '<a target="_BLANK" href="//' . $connectorURL . '">' . $connectorURL . '</a>';

                $connectorStatus = $connector['connectorStatus'];
                $connectorStatusText = self::writeConnectorStatusText($smarty, $connectorStatus, $connector['connectorInstallURL']);

                if ($brandingName == '') {
                    $brandingName = '<i>' . $smarty->get_config_vars('str_LabelDefault') . '</i>';
                }

                $folderName = $brandingCode;
                if ($folderName == '') {
                    $folderName = $smarty->get_config_vars('str_LabelDefault');
                }
                $folderName .= ' - ' . $applicationName;

                $connectorItem['recordid'] = "'" . UtilsObj::ExtJSEscape($recordID) . "'";
                $connectorItem['code'] = "'" . UtilsObj::ExtJSEscape($brandingCode) . "'";
                $connectorItem['company'] = "'" . UtilsObj::ExtJSEscape($brandingCompany) . "'";
                $connectorItem['foldername'] = "'" . UtilsObj::ExtJSEscape($folderName) . "'";
                $connectorItem['connectorurllink'] = "'" . UtilsObj::ExtJSEscape($connectorURLLink) . "'";
                $connectorItem['connectorstatustext'] = "'" . UtilsObj::ExtJSEscape($connectorStatusText) . "'";
                $connectorItem['connectorstatus'] = "'" . UtilsObj::ExtJSEscape($connectorStatus) . "'";
                $connectorItem['brandid'] = "'" . UtilsObj::ExtJSEscape($brandID) . "'";
                $connectorItem['connectorurl'] = "'" . UtilsObj::ExtJSEscape($connectorURL) . "'";

                array_push($connectorsArray, '[' . join(',', $connectorItem) . ']');
            }

            $returnArray = join(',', $connectorsArray);
            if ($returnArray != '') {
                $returnArray = ', ' . $returnArray;
            }

            echo '[[' . $totalCount . ']' . $returnArray . ']';
        }
    }

    static function writeConnectorStatusText($pSmarty, $pConnectorStatus, $pConnectorInstallURL)
    {
        $connectorInstallURL = $pConnectorInstallURL;
        $connectorStatusText = $pSmarty->get_config_vars('str_ConnectorStatusNotConfigured');

        switch ($pConnectorStatus) {
            case TPX_CONNECTOR_NOTCONFIGURED:
                $connectorStatusText = $pSmarty->get_config_vars('str_ConnectorStatusNotConfigured');
                break;

            case TPX_CONNECTOR_PENDING:
                $connectorStatusText = $pSmarty->get_config_vars('str_ConnectorStatusReady');
                break;

            case TPX_CONNECTOR_READY:
                $connectorInstallURL = str_replace(["http://", "https://"], "", $connectorInstallURL);
                $connectorInstallURL = "//" . $connectorInstallURL;

                $connectorStatusText = $pSmarty->get_config_vars('str_ConnectorStatusInstallReady');
                $connectorStatusText = str_replace("^0", $connectorInstallURL, $connectorStatusText);
                break;

            case TPX_CONNECTOR_CONNECTED:
                $connectorStatusText = $pSmarty->get_config_vars('str_ConnectorStatusConfigured');
                break;
        }

        return $connectorStatusText;
    }
}
