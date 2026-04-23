<?php
use Security\ControlCentreCSP;

class Share_view
{
	static function displayLogin($webBrandCode, $uniqueId, $orderItemId, $pSource, $error = '')
	{
		global $gSession;

		$smarty = SmartyObj::newSmarty('Login', $webBrandCode, $gSession['webbrandapplicationname']);

		$smarty->assign('footer', $smarty->getLocaleTemplate('footer.tpl', ''));

        $smarty->assign('ref2', $uniqueId);
        $smarty->assign('orderitemid', $orderItemId);
        $smarty->assign('source', $pSource);
        $smarty->assign('webbrandcode', $webBrandCode);

        $smarty->assign('error', $smarty->get_config_vars($error));

		$smarty->cachePage = false; // do not allow the page to be cached so that the browser grabs the correct language

        if ($gSession['ismobile'] == true)
        {
            $languageHTMLList = LocalizationObj::buildSystemLanguageList(UtilsObj::getBrowserLocale(), true);
            $smarty->assign('systemlanguagelist', $languageHTMLList);

            $smarty->assign('header',  $smarty->getLocaleTemplate('header_small.tpl',''));
            $smarty->displayLocale('share/previewlogin_small.tpl');
        }
        else
        {
            $languageHTMLList = LocalizationObj::buildSystemLanguageList(UtilsObj::getBrowserLocale(), false);
            $smarty->assign('systemlanguagelist', $languageHTMLList);

            $smarty->assign('header',  $smarty->getLocaleTemplate('header_large.tpl',''));

            $smarty->assign('sidebaradditionalinfo', $smarty->getLocaleTemplate('sidebaradditionalinfo_login.tpl', ''));
            $smarty->assign('sidebarleft', $smarty->getLocaleTemplate('sidebarleft_login.tpl', ''));
            $smarty->assign('sidebarleft_default', $smarty->getLocaleTemplate('sidebarleft_default.tpl', ''));

            $smarty->displayLocale('share/previewlogin_large.tpl');
        }
	}

	static function shareAddToAny($result)
	{
		echo $result;
	}

    static function preview($pResultArray, $previewOwner, $pLoadFullTemplate)
    {
        global $gSession;

        $smarty = SmartyObj::newSmarty('Share', $gSession['webbrandcode'], $gSession['webbrandapplicationname']);
        $smarty->assign('projectname', UtilsObj::encodeString($pResultArray['projectname'], false));
        $smarty->assign('productname', UtilsObj::encodeString(LocalizationObj::getLocaleString($pResultArray['productname'], $gSession['browserlanguagecode'], true), false));
        $smarty->assign('externalpreviewurl', $pResultArray['externalpreviewurl']);
        $smarty->assign('orderitemid', $pResultArray['orderitem']);
        $smarty->assign('canOrder', $pResultArray['canorder']);
        $smarty->assign('ordercancelled', $pResultArray['ordercancelled']);
        $smarty->assign('previewowner', $previewOwner);
        $smarty->assign('displaytype', $pResultArray['displaytype']);
        $smarty->assign('session', $gSession['ref']);
        $smarty->assign('ordersource', $pResultArray['ordersource']);
		$smarty->assign('googleanalyticscode', $pResultArray['googleanalyticscode']);

        if ($pResultArray['result'] != '')
        {
            $pResultArray['resultparam'] = $smarty->get_config_vars($pResultArray['result']);
        }

        $smarty->assign('ref2', $pResultArray['sharedref']);
        $smarty->assign('uploadref', $pResultArray['uploadref']);
        $smarty->assign('method', $pResultArray['method']);
        $smarty->assign('orderid', $pResultArray['orderid']);
        $smarty->assign('productcode', $pResultArray['productcode']);
        $smarty->assign('webbrandcode', $pResultArray['brandcode']);
        $smarty->assign('recipient', $pResultArray['recipients']);
        $smarty->assign('userid', $pResultArray['userid']);
        $smarty->assign('reorderaction', $pResultArray['reorderaction']);
        $smarty->assign('thumbnailpath', $pResultArray['thumbnailpath']);

		//test if the image exist in server
        foreach ($pResultArray['pages'] as $sKey => $aValue)
        {
			if (($sKey != 'noinsideleft') && ($sKey != 'nooutsideright'))
			{
				$pResultArray['pages'][$sKey]['pagename'] = LocalizationObj::getLocaleString($aValue['pagename'], $gSession['browserlanguagecode'], true);
			}
        }

        $smarty->assign('pages', $pResultArray['pages']);
        $smarty->assign('previewlicensekey', $pResultArray['previewlicensekey']);

        if( $pResultArray['producttype'] == TPX_PRODUCTCOLLECTIONTYPE_SINGLEPRINTS)
        {
            $smarty->assign('galleria', 'singleprint');
        }
        else
        {
            $smarty->assign('galleria', 'default');
        }

		$smarty->assign('pageflipsettings', $pResultArray['pageflipsettings']);

        // Determine if the Content Security Policy is active.
        $cspActive = true;
        $ac_config = UtilsObj::getGlobalValue('ac_config', []);

        if ((array_key_exists('CONTENTSECURITYPOLICY', $ac_config)) && ($ac_config['CONTENTSECURITYPOLICY'] === 'DISABLED'))
        {
            $cspActive = false;
        }

        // Add the unsafe-eval to allow the pageflip script to execute.
        if (($pResultArray['displaytype'] == 1) && ($pResultArray['previewlicensekey'] != '') && $cspActive)
        {
            $cspBuilder = ControlCentreCSP::getInstance(UtilsObj::getGlobalValue('ac_config'));

            $cspBuilder->getBuilder()->setAllowUnsafeEval('script-src', true);
        }

        if ($pResultArray['macdownloadurl'] == 'http://')
        {
            $pResultArray['macdownloadurl'] = '';
        }
        if ($pResultArray['win32downloadurl'] == 'http://')
        {
            $pResultArray['win32downloadurl'] = '';
        }
        $smarty->assign('macdownloadurl', $pResultArray['macdownloadurl']);
        $smarty->assign('win32downloadurl', $pResultArray['win32downloadurl']);
        $smarty->assign('staractionlabel', $smarty->get_config_vars('str_LabelShareNow'));

        if ($previewOwner == TPX_PREVIEW_CUSTOMER)
        {
			if ($pResultArray['temporder'] == false)
			{
				$smarty->assign('promomessagelabel', $smarty->get_config_vars('str_LabelPromoMessageShare'));
			}
			else
			{
				$smarty->assign('promomessagelabel', $smarty->get_config_vars('str_LabelPromoMessageProject'));
			}
        }
        else
        {
            $smarty->assign('promomessagelabel', $smarty->get_config_vars('str_LabelPromoMessageBuy'));
        }

        $smarty->assign('result', $pResultArray['result']);
        $smarty->assign('resultparam', $pResultArray['resultparam']);
        $smarty->assign('sharebyemailmethod', $pResultArray['sharebyemailmethod']);
		$smarty->assign('temporder', $pResultArray['temporder']);
        $smarty->assign('webbrandapplicationname', UtilsObj::escapeInputForJavaScript($gSession['webbrandapplicationname']));

        // we need to load a full template
        if ($gSession['ismobile'] == true)
        {
            if ($pLoadFullTemplate == true)
            {
                $smarty->assign('header', $smarty->getLocaleTemplate('header_small.tpl', ''));
                $smarty->displayLocale('share/previewshare_small.tpl');
            }
            else
            {
                $resultArray['template'] = $smarty->fetchLocale('share/previewcustomer_small.tpl');
                $resultArray['javascript'] = $smarty->fetchLocale('share/preview.tpl');
				
				if (($pResultArray['displaytype'] == 1) && ($pResultArray['previewlicensekey'] != ''))
				{
					$resultArray['scripturl1'] = UtilsObj::correctPath($gSession['webbrandwebroot']) . '/utils/pageturning/pageflip5/js/jquery-1.11.1.min.js';
					$resultArray['scripturl2'] = UtilsObj::correctPath($gSession['webbrandwebroot']) . '/utils/pageturning/pageflip5/js/pageflip5-min.js';
					$resultArray['css'] = UtilsObj::correctPath($gSession['webbrandwebroot']) . UtilsObj::getVersionedFileName('/css/pageflip.css');
				}
				else
				{
					$resultArray['scripturl1'] = '';
					$resultArray['scripturl2'] = '';
					$resultArray['css'] = '';
				}

				if (isset($_GET['callback']))
				{
					echo $_GET['callback'] . '(' . json_encode($resultArray) . ')';
				}
				else
				{
					echo json_encode($resultArray);
				}
            }
        }
        else
        {
            $smarty->assign('header', $smarty->getLocaleTemplate('header_large.tpl', ''));
            $smarty->displayLocale('share/preview_large.tpl');
        }
    }

    /**
     * Preview Shared Project view -- note that we are setting $pShareHideBranding to 0.  This is so that in the
     * case where the projectRef is missing or corrupt, the user will not be able to access any brand info
     *
     * @param $pResultArray
     * @param int $pShareHideBranding
     */
    static function previewSharedProject($pResultArray, $pShareHideBranding = 1)
    {
        global $gSession;

        $smarty = SmartyObj::newSmarty('Share', $gSession['webbrandcode'], $gSession['webbrandapplicationname']);
        $smarty->assign('session', $gSession['ref']);
        $smarty->assign('sharehidebranding', $pShareHideBranding);
        $smarty->assign('webbrandapplicationname', UtilsObj::escapeInputForJavaScript($gSession['webbrandapplicationname']));


        // Check for errors before asigning smarty variables
        if ($pResultArray['result'] != TPX_ONLINE_ERROR_NONE)
        {
            $smarty->assign('error', $smarty->get_config_vars('str_LabelPreviewNotFoundMessage'));
        }
        else
        {
            $smarty->assign('projectname', UtilsObj::encodeString($pResultArray['projectname'], false));
            $smarty->assign('designurl', $pResultArray['designurl']);
        }

        $smarty->assign('header', $smarty->getLocaleTemplate('header_share_online.tpl', ''));
        $smarty->displayLocale('share/preview_online.tpl');
    }

    static function unshare($pResultArray)
    {
        $encodeArray = array();
        $encodeArray['success'] = 'true';
        $encodeArray['title'] = '';
        $encodeArray['msg'] = '';

        if ($pResultArray['result'] == '')
        {
            $encodeArray['nbShared'] = $pResultArray['nbShared'];
        }
        else
        {
            $encodeArray['success'] = 'false';
            $encodeArray['title'] = $pResultArray['result'];
            $encodeArray['msg'] = $pResultArray['resultparam'];
        }

        echo json_encode($encodeArray);
    }

    static function previewNotFound($pLoadFullTemplate)
    {
    	global $gSession;

    	$smarty = SmartyObj::newSmarty('Share', $gSession['webbrandcode'], $gSession['webbrandapplicationname']);

		$smarty->assign('footer', $smarty->getLocaleTemplate('footer.tpl', ''));

        // we need to load a full template
        if ($gSession['ismobile'] == true)
        {
            if ($pLoadFullTemplate == true)
            {
                $languageHTMLList = LocalizationObj::buildSystemLanguageList(UtilsObj::getBrowserLocale(), true);
                $smarty->assign('systemlanguagelist', $languageHTMLList);

                $smarty->assign('header',  $smarty->getLocaleTemplate('header_small.tpl',''));

                $smarty->displayLocale('share/previewnotfound_small.tpl');
            }
            else
            {
                $resultArray['template'] = $smarty->fetchLocale('share/previewnotfoundcustomer_small.tpl');
                $resultArray['javascript'] = '';
                echo json_encode($resultArray);
            }
        }
        else
        {
            $languageHTMLList = LocalizationObj::buildSystemLanguageList(UtilsObj::getBrowserLocale(), false);
            $smarty->assign('systemlanguagelist', $languageHTMLList);

            $smarty->assign('header',  $smarty->getLocaleTemplate('header_large.tpl',''));

            $smarty->assign('sidebarleft', $smarty->getLocaleTemplate('sidebarleft_login.tpl', ''));
            $smarty->assign('sidebarleft_default', $smarty->getLocaleTemplate('sidebarleft_default.tpl', ''));

            $smarty->displayLocale('share/previewnotfound_large.tpl');
        }
    }

    static function previewNotAvailable($pLoadFullTemplate)
    {
    	global $gSession;

    	$smarty = SmartyObj::newSmarty('Share', $gSession['webbrandcode'], $gSession['webbrandapplicationname']);

		$smarty->assign('footer', $smarty->getLocaleTemplate('footer.tpl', ''));
		$smarty->assign('sidebarleft', $smarty->getLocaleTemplate('sidebarleft_login.tpl', ''));
        $smarty->assign('sidebarleft_default', $smarty->getLocaleTemplate('sidebarleft_default.tpl', ''));

        if ($gSession['ismobile'] == true)
        {
            $smarty->assign('header',  $smarty->getLocaleTemplate('header_small.tpl',''));

            $languageHTMLList = LocalizationObj::buildSystemLanguageList(UtilsObj::getBrowserLocale(), true);
            $smarty->assign('systemlanguagelist', $languageHTMLList);

            if ($pLoadFullTemplate == true)
            {
                $smarty->displayLocale('share/previewunavailable_small.tpl');
            }
            else
            {
                $resultArray['template'] = $smarty->fetchLocale('share/previewunavailablecustomer_small.tpl');
                $resultArray['javascript'] = '';
                echo json_encode($resultArray);
            }
        }
        else
        {
            $smarty->assign('header',  $smarty->getLocaleTemplate('header_large.tpl',''));

            $languageHTMLList = LocalizationObj::buildSystemLanguageList(UtilsObj::getBrowserLocale(), false);
            $smarty->assign('systemlanguagelist', $languageHTMLList);

            $smarty->displayLocale('share/previewunavailable_large.tpl');
        }
    }

    static function reorderUnavailable($pResultArray)
    {
    	global $gSession;

    	$smarty = SmartyObj::newSmarty('Share', $gSession['webbrandcode'], $gSession['webbrandapplicationname']);


		$smarty->assign('footer', $smarty->getLocaleTemplate('footer.tpl', ''));

        $smarty->assign('action', $pResultArray['action']);
        $smarty->assign('orderitemid', $pResultArray['orderitemid']);

        SmartyObj::replaceParams($smarty, $pResultArray['result'], $pResultArray['resultparam']);
    	$smarty->assign('result', $smarty->get_template_vars($pResultArray['result']));

        if ($gSession['ismobile'] == true)
        {
            $languageHTMLList = LocalizationObj::buildSystemLanguageList(UtilsObj::getBrowserLocale(), true);
            $smarty->assign('systemlanguagelist', $languageHTMLList);

            $smarty->assign('header',  $smarty->getLocaleTemplate('header_small.tpl',''));
            $smarty->displayLocale('share/reorderunavailable_small.tpl');
        }
        else
        {
            $languageHTMLList = LocalizationObj::buildSystemLanguageList(UtilsObj::getBrowserLocale(), false);
            $smarty->assign('systemlanguagelist', $languageHTMLList);

            $smarty->assign('header',  $smarty->getLocaleTemplate('header_large.tpl',''));

            $smarty->assign('sidebarleft', $smarty->getLocaleTemplate('sidebarleft_login.tpl', ''));
            $smarty->assign('sidebarleft_default', $smarty->getLocaleTemplate('sidebarleft_default.tpl', ''));

            $smarty->displayLocale('share/reorderunavailable_large.tpl');
        }
    }

    static function unShareList($aResult)
    {
        echo json_encode($aResult);
    }
}

?>