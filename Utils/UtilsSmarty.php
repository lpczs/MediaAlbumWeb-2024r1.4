<?php

use Security\ContentSecurityPolicy;
use Security\ControlCentreCSP;

function smartyStripSpace($tpl_output, $smarty)
{
    $tpl_output = str_replace('<nl>',"\n", $tpl_output);

    return $tpl_output;
}

class MySmarty extends Smarty
{
    public $langCode = '';
    public $webBrandCode = '';
    public $webBrandFolder = '';
    public $webBrandPath = 'branding';
    public $templateSubPath = 'templates';
    public $lastTemplatePath = '';
    public $lastTemplateParentPath = '';
    public $cachePage = false;
    public $cacheSeconds = 86400; // allow caching for one day

    function get_config_vars($pConfigName)
    {
        return $this->getConfigVars($pConfigName);
    }

    function get_template_vars($pConfigName)
    {
        return $this->getTemplateVars($pConfigName);
    }

    function getLocaleTemplatePath($pTemplateResource, $pLocale = '')
    {
        // return the path to the localized template resource

        $templateResource = '';

        if ($pLocale == '')
        {
            $theLocale = UtilsObj::getBrowserLocale();
        }
        else
        {
            $theLocale = $pLocale;
        }

        $templateFileName = '';
        $templateFileExtension = '';

        $dotPos = strrpos($pTemplateResource, '.');
        if ($dotPos > 0)
        {
            $templateFileName = substr($pTemplateResource, 0, $dotPos);
            $templateFileExtension = substr($pTemplateResource, $dotPos);
        }
        else
        {
            $templateFileName = $pTemplateResource;
        }

        $templateResource = $templateFileName . '_' . $theLocale . $templateFileExtension;
        if (! $this->templateExists($templateResource))
        {
            $templateResource = $templateFileName . '_' . substr($theLocale, 0, 2) . $templateFileExtension;
            if (! $this->templateExists($templateResource))
            {
                $templateResource = $pTemplateResource;
                if (! $this->templateExists($templateResource))
                {
                    $templateResource = '';
                }
            }
        }

        $this->lastTemplatePath = $templateResource;

        $thePath = substr($templateResource, 0, strrpos($templateResource, '/'));

        if (substr($thePath, 0, 5) == 'file:')
        {
            $thePath = substr($thePath, 5);

			// if the template resource was for either the [Customise] or [Brading] template_dir
			// then we must correct the path so that it becomes a relative path.
			// this is so that images within the emails can be handled correctly.
            if (substr($thePath, 0, 1) == '[')
			{
				$lastPos = strpos($thePath, ']');

				$templateDirKey = substr($thePath, 1, $lastPos -1);
				$thePath = $this->getTemplateDir($templateDirKey) . substr($thePath, $lastPos + 2);
			}
        }
        $this->lastTemplateParentPath = $thePath;

        return $templateResource;
    }

    /**
     * This is bascially getLocaleTemplate but without setting the compile_id. getLocaleTemplate needs to be changed to use this function
     * but at the time of writting there was no time.
     *
     * The function will use the branding and customisation hierarchy to find the Smarty path to a template.
     *
     * @param resource $pTemplateResource, The name of the template which you are trying to find the path for
     * @param string $pLocale The Language the template is for.
     * @return array A hash array which presents the template path (template) and the smorty unique complie id (compile_id)
     */
    function getLocaleTemplateInfo($pTemplateResource, $pLocale = '')
    {
        $returnArray = array('compile_id' => '', 'template'=>'',);

        // if we have a brand we must check that first
        if ($this->webBrandCode != '')
        {
            // attempt to get the template from the branding path
            $returnArray['template'] = $this->getLocaleTemplatePath('file:[Branding]/' . $this->webBrandFolder . '/' . $this->templateSubPath . '/' . $pTemplateResource, $pLocale);
        }

        if ($returnArray['template']  == '')
        {
            // we don't have a template yet so check to see if we have a customised template
            $returnArray['template']  = $this->getLocaleTemplatePath('file:[Customise]/' . $this->templateSubPath . '/' . $pTemplateResource, $pLocale);

            if ($returnArray['template']  == '')
            {
                // the template is not in the customise path
                $returnArray['template']  = $this->getLocaleTemplatePath($pTemplateResource, $pLocale);
            }
            else
            {
                // set a unique compile_id for the customise folder
                $returnArray['compile_id'] = 'customise_';
            }
        }
        else
        {
            // set a unique compile_id based on the brand code so that templates with the same name but in different brands are unique
            $returnArray['compile_id'] = 'b_' . $this->webBrandCode . '_';
        }

        return $returnArray;

    }

    function getLocaleTemplate($pTemplateResource, $pLocale = '')
    {
        // return the path to the template based on the localization, branding & customisation setup

        // NOTE: This function needs changing to consume the getLocaleTemplateInfo function.

        $templatePath = '';

        // if we have a brand we must check that first
        if ($this->webBrandCode != '')
        {
            // attempt to get the template from the branding path
            $templatePath = $this->getLocaleTemplatePath('file:[Branding]/' . $this->webBrandFolder . '/' . $this->templateSubPath . '/' . $pTemplateResource, $pLocale);
        }

        if ($templatePath == '')
        {
            // we don't have a template yet so check to see if we have a customised template
            $templatePath = $this->getLocaleTemplatePath('file:[Customise]/' . $this->templateSubPath . '/' . $pTemplateResource, $pLocale);

            if ($templatePath == '')
            {
                // the template is not in the customise path
                $templatePath = $this->getLocaleTemplatePath($pTemplateResource, $pLocale);

                // reset the compile_id
                $this->compile_id = '';
            }
            else
            {
                // set a unique compile_id for the customise folder
                $this->compile_id = 'customise_';
            }
        }
        else
        {
            // set a unique compile_id based on the brand code so that templates with the same name but in different brands are unique
            $this->compile_id = 'b_' . $this->webBrandCode . '_';
        }

        return $templatePath;
    }

	function getLocaleWebRootTemplate($pTemplateResource, $pLocale = '')
	{
        // return the path to the template based on the localization setup
		$templatePath = $this->getLocaleTemplatePath($pTemplateResource, $pLocale);

		// reset the compile_id
		$this->compile_id = '';

        return $templatePath;
    }

	function getLocaleCss($pCssFileName)
	{
        // Return the path to the css file based on the localization, branding & customisation setup
		$cssFiles = array();
        $cssPath = '';

		// Check Branding folder
        if ($this->webBrandCode != '')
        {
			$cssFiles = glob('../' . $this->webBrandPath . '/' . $this->webBrandFolder . '/css/' . $pCssFileName . '*.css');
		}

		// Check the Customise folder
        if (count($cssFiles) == 0)
        {
			$cssFiles = glob('../Customise/css/' . $pCssFileName . '*.css');
		}

		// If $cssFiles is still not set, check the default css file
		if (count($cssFiles) == 0)
		{
			$cssFiles = glob('../webroot/css/' . $pCssFileName . '*.css');
		}

		if (count($cssFiles) > 0)
		{
			// Select the most recent version of the file
			array_multisort(array_map('filemtime', $cssFiles), SORT_NUMERIC, SORT_DESC, $cssFiles);

			$cssPath = $cssFiles[0];
		}

		return $cssPath;
	}

    function displayLocale($pTemplateResource, $pLocale = '', $pDisableCsp = false)
    {
        global $gSession;
        global $ac_config;

        $nonceVal = '';
        $nonce = '';

        // Content Security Policy is on by default for the customer pages.
        // Do not apply a policy to the admin pages.
        if (isset($gSession['userdata']['usertype']))
        {
            // Make sure to compare type, as '' == 0, which will turn on CSP for TPX_LOGIN_SYSTEM_ADMIN.
            $cspActive = (($gSession['userdata']['usertype'] === TPX_LOGIN_CUSTOMER) || ($gSession['userdata']['usertype'] === ''));
        }
        else
        {
            $cspActive = true;
        }

        if ($pDisableCsp) {
            $cspActive = false;
        }

        $frameHeaders = UtilsObj::getFrameHeaders();

        if (array_key_exists('CONTENTSECURITYPOLICY', $ac_config))
        {
            if ($ac_config['CONTENTSECURITYPOLICY'] === 'DISABLED')
            {
                $cspActive = false;
            }
        }

        if ($cspActive)
        {
            try {
				// Set the brandcode we are building the CSP for.
				$brandCode = ($this->webBrandCode != '') ? $this->webBrandCode : 'DEFAULT';

                // build the csp
                $policy = ControlCentreCSP::getInstance($ac_config);
                $policy->setFrameOptions($frameHeaders)
                    ->addIgnoredDirective('form-action')
                    ->build($brandCode);

                // set the nonce
                $nonceVal = $policy->nonce();
                $nonce = sprintf('nonce="%s"', $nonceVal);

                // send the header - this must be done after generating the nonce
                $policy->sendHeader();
            } catch (\Exception $e) {
                error_log(sprintf("Error generating CSP : %s", $e->getMessage()));
            }
        }
        else
        {
            $cspString = '';
            // iframe security headers only allow all if not sent
            if ($frameHeaders['frameancestor'] != 'ALLOW')
            {
                $ancestor = $frameHeaders['frameancestor'];
                $cspString = " frame-ancestors " . (is_array($ancestor) ? implode(' ', $ancestor) : $ancestor);

                if ($frameHeaders['xframeoption'] != 'MULTI')
                {
                    header("X-Frame-Options: " . $frameHeaders['xframeoption']);
                }
            }

            if ($cspString != '')
            {
                header("Content-Security-Policy: " . $cspString);
            }
        }

        $this->assign('nonce', $nonce);
        $this->assign('nonceraw', $nonceVal);

        // display to localized template
        if ($this->cachePage == false)
        {
            header("Expires: " . gmdate("D, j M Y H:i:s") . " GMT");
            header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
            header("Cache-Control: no-store, no-cache, must-revalidate");
            header("Cache-Control: post-check=0, pre-check=0", false);
            header("Pragma: no-cache");
        }
        else
        {
            header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
            header("Cache-Control: max-age=" . $this->cacheSeconds . ", must-revalidate");
        }

		// exception for payment gateway simulator
		if (array_key_exists('USEPAYMENTGATEWAYSIMULATOR', $ac_config))
		{
			$usePaymentGatewaySimulator = $ac_config['USEPAYMENTGATEWAYSIMULATOR'];
		}
		else
		{
			$usePaymentGatewaySimulator = 0;
		}

		if ($usePaymentGatewaySimulator == 1)
		{
			$paymentUrl = $this->get_template_vars('payment_url');
			if (($paymentUrl != '') && (strpos(strtolower($paymentUrl), 'simulator.executesimulator') !== false))
			{
				error_log('Payment Gateway Simulator Active');
				$this->assign('payment_url', $paymentUrl . '&ref=' . $gSession['ref']);
			}
		}

        $this->display($this->getLocaleTemplate($pTemplateResource, $pLocale));
    }

    function fetchLocale($pTemplateResource, $pLocale = '')
    {
        // return the localized template data

        return $this->fetch($this->getLocaleTemplate($pTemplateResource, $pLocale));
    }

    function fetchLocaleWebRoot($pTemplateResource, $pLocale = '')
    {
        // return the localized template data
        return $this->fetch($this->getLocaleWebRootTemplate($pTemplateResource, $pLocale));
	}

    function fetchLocaleEmail($pTemplateResource, $pLocale = '')
    {
        // return the localized template data

        return $this->fetch($this->getLocaleEmailTemplate($pTemplateResource, $pLocale));
    }

    function getLocaleEmailTemplate($pTemplateResource, $pLocale = '')
    {
        // return the path to the email template based on the localization, branding & customisation setup

        $templatePath = '';

        // if we have a brand we must check that first
        if ($this->webBrandCode != '')
        {
            // attempt to get the template from the branding path
            $templatePath = $this->getLocaleTemplatePath('file:[Branding]/' . $this->webBrandFolder . '/email/' . $pTemplateResource, $pLocale);
        }

        if ($templatePath == '')
        {
            // we don't have a template yet so check to see if we have a customised template
            $templatePath = $this->getLocaleTemplatePath('file:[Customise]/email/' . $pTemplateResource, $pLocale);
            if ($templatePath == '')
            {
                // the template is not in the customise path
                $templatePath = $this->getLocaleTemplatePath($pTemplateResource, $pLocale);

                // reset the compile_id
                $this->compile_id = '';
            }
            else
            {
                // set a unique compile_id for the customise folder
                $this->compile_id = 'customise_';
            }
        }
        else
        {
            // set a unique compile_id based on the brand code so that templates with the same name but in different brands are unique
            $this->compile_id = 'b_' . $this->webBrandCode . '_';
        }

        return $templatePath;
    }

    function loadConfigFile($pSection, $pConfigFileName)
    {
        $configFound = false;

        $currentConfigDir = $this->getConfigDir();
        $currentConfigDir = $currentConfigDir[0];

        $langConfPath = $currentConfigDir . '/' . $pConfigFileName;
        if (file_exists($langConfPath))
        {
            $this->configLoad($pConfigFileName, $pSection);
            if ($this->getConfigVars('str_LanguageName') != '')
            {
                $configFound = true;
            }
        }

        return $configFound;
    }
}

class SmartyObj
{
    static function newSmartySuper($section, $pWebRootParent, $pWebBrandCode, $pWebAppName, $pLocale, $setRef, $trimOutput, $configFileName)
    {
        // main template config code that performs important tasks such as determining the language file and the path to use as the webroot

        global $ac_config;
        global $gConstants;
        global $gSession;

        $webBrandArray = DatabaseObj::getBrandingFromCode($pWebBrandCode);

        $smarty = new MySmarty();
        $smarty->muteUndefinedOrNullWarnings();
        
        $pluginsDirArray = $smarty->getPluginsDir();
        $pluginsDirArray[] = '../libs/internal/smarty/plugins';

        $smarty->setPluginsDir($pluginsDirArray);
        $smarty->webBrandCode = $pWebBrandCode;
        $smarty->webBrandFolder = $webBrandArray['name'];
        $smarty->config_booleanize = false;

		// Setup template directories.
		$templateDirectories = [
			'../Customise/templates',
			'../templates',
			'Customise' => '../Customise',
			'Branding' => '../Branding'
		];

		if ($pWebBrandCode !== '')
		{
			// If this is not the default brand, add the brand templates folder to the directory list.
			// This is to fix templates that include other templates not using the brand file.
			// Needs to be added to the top of the list so it is checked first.
			array_unshift($templateDirectories, '../Branding/' . $webBrandArray['name'] . '/templates');
		}

		$smarty->setTemplateDir($templateDirectories);

        $smarty->compile_dir = '../templates_c';
        $smarty->setConfigDir('../lang');
        $smarty->error_reporting = E_ALL & ~E_NOTICE;

        $configFileName = ' ' . $configFileName;

        if ($pWebAppName == '')
        {
			$pWebAppName = $webBrandArray['applicationname'];
        }

        if ($pLocale == '')
        {
            $pLocale = UtilsObj::getBrowserLocale();
        }

        if ($ac_config['WEBBRANDFOLDERNAME'] != '')
        {
            $smarty->webBrandPath = $ac_config['WEBBRANDFOLDERNAME'];
        }

        // load the language config file based on the provided locale
        $langConfFile = $pLocale . $configFileName;

        if ($smarty->loadConfigFile($section, $langConfFile) == false)
        {
            // try getting the first two characters of the language
            $langConfFile = substr($pLocale, 0, 2) . $configFileName;
            if ($smarty->loadConfigFile($section, $langConfFile) == false)
            {
                // try the default language
                $langConfFile = $gConstants['defaultlanguagecode'] . $configFileName;
                if ($smarty->loadConfigFile($section, $langConfFile) == false)
                {
                    $smarty->configLoad('en' . $configFileName, $section);
                    $smarty->langCode = 'en';
                }
                else
                {
                    $smarty->langCode = $gConstants['defaultlanguagecode'];
                }
            }
            else
            {
                $smarty->langCode = substr($pLocale, 0, 2);
            }
        }
        else
        {
            $smarty->langCode = $pLocale;
        }

        $origConfigPath = $smarty->getConfigDir();
        $origConfigPath = $origConfigPath[0];

        $overrideConfigLoaded = false;

        // load the web brand language override config file
        if ($pWebBrandCode != '')
        {
            $newConfigDir  = '../' . $smarty->webBrandPath . '/' . $smarty->webBrandFolder . '/lang';
            $smarty->setConfigDir($newConfigDir);
            $langConfFile = $smarty->langCode . $configFileName;
            if (file_exists($newConfigDir . '/' . $langConfFile))
            {
                $smarty->configLoad($langConfFile, $section);
                $overrideConfigLoaded = true;
            }
            else
            {
                // if we can not find the language file and the language code is more than 2 chars use the truncated language code
                if (strlen($smarty->langCode) > 2)
                {
                    $langConfFile = substr($smarty->langCode, 0, 2) . $configFileName;
                    if (file_exists($newConfigDir . '/' . $langConfFile))
                    {
                        $smarty->configLoad($langConfFile, $section);
                        $overrideConfigLoaded = true;
                    }
                }
            }
        }

        // load the customise language override config file
        if ($overrideConfigLoaded == false)
        {
            $newConfigDir  = '../Customise/lang';
            $smarty->setConfigDir($newConfigDir);

            $langConfFile = $smarty->langCode . $configFileName;
            if (file_exists($newConfigDir . '/' . $langConfFile))
            {
                $smarty->configLoad($langConfFile, $section);
                $overrideConfigLoaded = true;
            }
            else
            {
                // if we can not find the language file and the language code is more than 2 chars use the truncated language code
                if (strlen($smarty->langCode) > 2)
                {
                    $langConfFile = substr($smarty->langCode, 0, 2) . $configFileName;
                    if (file_exists($newConfigDir . '/' . $langConfFile))
                    {
                        $smarty->configLoad($langConfFile, $section);
                        $overrideConfigLoaded = true;
                    }
                }
            }
        }

        $smarty->setConfigDir($origConfigPath);

        $webBrandURL = UtilsObj::getBrandedWebUrl($pWebBrandCode);

        $smarty->assign('appname', $pWebAppName);
        $smarty->assign('server', $webBrandURL);
        if (! empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off')
		{
            $smarty->assign('serverprotocol', 'https://');
        }
		else
		{
            $smarty->assign('serverprotocol', 'http://');
        }

        // the webroot & brandroot variables need to be set based on the source url
        $webRootParent = $pWebRootParent;

        if (isset($_SERVER['REQUEST_URI']))
        {
			if (strpos(strtolower($_SERVER['REQUEST_URI']), strtolower($smarty->webBrandPath . '/' . $smarty->webBrandFolder)) > -1)
			{
				// the path is inside the branding so we must step back to the standard webroot path
				$webRootParent = '../../';
			}
		}

		// Determine the paths for the custom assets.
		$customBranding = array();
		$customBranding[TPX_BRANDING_FILE_TYPE_CC_LOGO] = '';
		$customBranding[TPX_BRANDING_FILE_TYPE_MARKETING] = '';

		foreach ($customBranding as $typeRef => $typeLink)
		{
			$typeData = DatabaseObj::getBrandAssetData($webBrandArray['id'], $typeRef, true);

			if ($typeData['data']['id'] > 0)
			{
				$typeLink = UtilsObj::correctPath($webBrandURL, '/', true) . 'brandassets/images/' . $typeData['data']['path'];
			}
			else
			{
				$typeLink = str_replace('../webroot', '', $typeData['data']['path']);
			}

            $customBranding[$typeRef] = $typeLink;
		}

		// Assign the branded assets.
		$smarty->assign('headerlogoasset', $customBranding[TPX_BRANDING_FILE_TYPE_CC_LOGO]);
		$smarty->assign('sidebarasset', $customBranding[TPX_BRANDING_FILE_TYPE_MARKETING]);

        if ($pWebBrandCode == '')
        {
            $smarty->assign('webroot', $webRootParent . UtilsObj::correctPath($ac_config['WEBROOT'], '/', false));
            $smarty->assign('brandroot', $webRootParent . UtilsObj::correctPath($ac_config['WEBROOT'], '/', false));
        }
        else
        {
            if ($webBrandArray['isactive'] == 1)
            {
                if ($webBrandArray['displayurl'] != '')
                {
                    $smarty->assign('brandroot', UtilsObj::correctPath($webBrandArray['displayurl'], '/', false));
                }
                else
                {
                    $smarty->assign('brandroot', $webRootParent . UtilsObj::correctPath($ac_config['WEBROOT']) . $smarty->webBrandPath . '/' . $smarty->webBrandFolder);
                }
                if ($webBrandArray['weburl'] != '')
                {
                    $smarty->assign('webroot', UtilsObj::correctPath($webBrandArray['weburl'], '/', false));
                }
                else
                {
                    $smarty->assign('webroot', $webRootParent . UtilsObj::correctPath($ac_config['WEBROOT'], '/', false));
                }
            }
            else
            {
                $smarty->assign('webroot', $webRootParent . UtilsObj::correctPath($ac_config['WEBROOT'], '/', false));
                $smarty->assign('brandroot', $webRootParent . UtilsObj::correctPath($ac_config['WEBROOT'], '/', false));
            }
        }

        $favIconPath = '';

        // if we have a brand we must check that first
        if ($smarty->webBrandCode != '')
        {
            // attempt to get the file from the branding path
            if (file_exists('../webroot/' . $smarty->webBrandPath . '/' . $smarty->webBrandFolder . '/images/favicon.ico'))
            {
                $favIconPath = $smarty->getTemplateVars('brandroot') . '/images/favicon.ico';
            }
        }

        if ($favIconPath == '')
        {
            // attempt to get the file from webbroot path
            if (file_exists ('../webroot/images/favicon.ico'))
            {
                $favIconPath = $smarty->getTemplateVars('webroot') . '/images/favicon.ico';
            }
        }

        // add favicon
        $smarty->assign('faviconpath', $favIconPath);

        if ($setRef == true)
        {
        	$sessionRef = AuthenticateObj::getSessionRef();
        	$smarty->assign('ref', $sessionRef);
        }

		 if (isset($gSession['userdata']['ssotoken']))
        {
            $smarty->assign('ssotoken', $gSession['userdata']['ssotoken']);
        }


        if ($trimOutput == true)
        {
            $smarty->registerFilter('output', 'smartyStripSpace');
        }

        if (isset($gSession['userdata']['usertype']))
        {
            $smarty->assign('userType', $gSession['userdata']['usertype']);
        }

        $smarty->assign('optionCFS', $gConstants['optioncfs']);
        $smarty->assign('optionMS', $gConstants['optionms']);
        $smarty->assign('optionAMS', $gConstants['optionams']);
        $smarty->assign('optionMC', $gConstants['optionmc']);
        $smarty->assign('optionDESOL', $gConstants['optiondesol']);
        $smarty->assign('optionHCC', $gConstants['optionhcc']);
        $smarty->assign('optionHOLDES', $gConstants['optionholdes']);
        $smarty->assign('optionsSCNTR', $gConstants['optionscntr']);

        $smarty->assign('langCode', $smarty->langCode);
        $smarty->assign('mainwebsiteurl', $webBrandArray['mainwebsiteurl']);
        $smarty->assign('googletagmanagercccode', $webBrandArray['googletagmanagercccode']);

        if (isset($gSession['ismobile']) && $gSession['ismobile'] == true)
        {
            $smarty->assign('issmallscreen', 'true');
        }
        else
        {
            $smarty->assign('issmallscreen', 'false');
        }

        return $smarty;
    }

    static function newSmarty($section, $pWebBrandCode = '', $pWebAppName = '', $pLocale = '', $setRef = true, $trimOutput = true, $configFileName = 'strings.conf')
    {
        // return a new smarty object for the specified parameters

        return self::newSmartySuper($section, '', $pWebBrandCode, $pWebAppName, $pLocale, $setRef, $trimOutput, $configFileName);
    }

    static function newSmartyFromWebRoot($section, $pWebRootParent, $pWebBrandCode = '', $pWebAppName = '', $pLocale = '', $setRef = true, $trimOutput = true, $configFileName = 'strings.conf')
    {
        // return a new smarty object for the specified parameters including the webroot

        return self::newSmartySuper($section, $pWebRootParent, $pWebBrandCode, $pWebAppName, $pLocale, $setRef, $trimOutput, $configFileName);
    }

    static function replaceParams($smarty, $varName, $param1, $encode = false)
    {
        // replace a smarty config file value

        $config_var = $smarty->getConfigVars($varName);

        if ($config_var)
        {
            $text = str_replace('^0', $param1, $config_var);

            if ($encode)
            {
            	$text = UtilsObj::encodeString($text, true);
            }

            $smarty->clearConfig($varName);
            $smarty->assign($varName, $text);
        }
    }

    static function getParamValue($section, $varName)
    {
        // return a smarty config file value

        $smarty = self::newSmarty($section, '', '', '', false, false);

        return $smarty->getConfigVars($varName);
    }

    static function getParamValueLocale($section, $varName, $locale)
    {
        // return a smarty config file value for a given locale

        $smarty = self::newSmarty($section, '', '', $locale, false, false);

        return $smarty->getConfigVars($varName);
    }

    static function getLanguageList()
    {
		// build the list of available languages by examining the language string files

		$resultArray = Array();

		$configLocale = DatabaseObj::getSystemConfig();
        $supportedLocalesArray = explode(',', $configLocale['supportedlocales']);

		$smarty = self::newSmarty('', '', '', 'en', false, false);

        $srcdir = $smarty->getConfigDir();
        $srcdir = $srcdir[0];

		if ($curdir = opendir($srcdir))
		{
			while ($file = readdir($curdir))
			{
				if (($file != '.') && ($file != '..') && (substr($file, -12) == 'strings.conf'))
				{
					$langConfFile = $srcdir . '/' . $file;
					if (is_file($langConfFile))
					{
                        $smarty->clearConfig('str_LanguageCode');
						if ($smarty->loadConfigFile('', $file) == true)
						{
                            $langCode = $smarty->getConfigVars('str_LanguageCode');
							if ($langCode != '')
							{
                                $languageItem['code'] = $smarty->getConfigVars('str_LanguageCode');
                                $languageItem['name'] = $smarty->getConfigVars('str_LanguageName');

								// always have english at the top of the list
								if ($langCode == 'en')
								{
									array_unshift($resultArray, $languageItem);
								}
								else
								{
									if (in_array($langCode, $supportedLocalesArray) || in_array('ALL', $supportedLocalesArray))
                                    {
                                        array_push($resultArray, $languageItem);
                                    }
                                }
                            }
                        }
                    }
                }
			}

			closedir($curdir);
		}

		return $resultArray;
	}
}
?>
