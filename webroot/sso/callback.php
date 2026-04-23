<?php
error_reporting(E_ALL);

ini_set('log_errors', true);

// step back to the webroot directory
chdir('../');

require_once('../initialize.php');
require_once('../Utils/UtilsDeviceDetection.php');

// if the default brand is empty then attempt to get it from the display URL
if ($gDefaultSiteBrandingCode == '')
{
	$gDefaultSiteBrandingCode = DatabaseObj::getBrandFromWebUrl($_SERVER['HTTP_HOST']);

	// we only need to set the brand code if the code is different to what was extracted in initialize
	if ($gDefaultSiteBrandingCode != '')
	{
		AuthenticateObj::setSessionWebBrand($gDefaultSiteBrandingCode);
	}
}

$result = '';
$resultParam = '';
$returnURL = '';
$paramArray = array();
$paramArray['ssoprivatedata'] = array();
$paramArray['ssotoken'] = '';
$ssoReason = -1;
$ref = 0;

// only allow this callback to be ran if sso is configured
if (($gConstants['optionwscrp']) && (file_exists('../Customise/scripts/EDL_ExternalCustomerAccount.php')))
{
    require_once('../Customise/scripts/EDL_ExternalCustomerAccount.php');

    $result = UtilsObj::getGETParam('error', '');
}
else
{
	$result = 'str_ErrorSSOGeneral';
	$resultParam = 'Error Code: ' . TPX_SSO_ERROR_CODE_NOT_CONFIGURED;
	error_log('SSO Not setup');
}

if ($result == '')
{
	// read the sso cookie
	$readCookieReturnArray = AuthenticateObj::readSSOCookie();

	// make sure there were no errors when getting the cookie
	if ($readCookieReturnArray['result'] == '')
	{
		// get the cookie value from the array
		$ssoKey = $readCookieReturnArray['cookievalue'];

		// get the data from the authentication table which was previously saved as part of the sso login workflow
		$ssoDataRecordReturnArray = AuthenticateObj::getSSODataRecord($ssoKey);

		if ($ssoDataRecordReturnArray['result'] == '')
		{
			// reason == -1 means that the record was not found
			if ($ssoDataRecordReturnArray['reason'] == -1)
			{
				$result = 'str_ErrorSSOGeneral';
				$resultParam = 'Error Code: ' . TPX_SSO_ERROR_CODE_MISSING_DATABASE_RECORD;
				error_log('No authentication data store records found for key: ' . $ssoKey);
			}
			else
			{
				$paramArray['ssoprivatedata'] = $ssoDataRecordReturnArray['data'];
				$returnURL = $ssoDataRecordReturnArray['originurl'];
				$ssoReason = $ssoDataRecordReturnArray['reason'];
				$ref = $ssoDataRecordReturnArray['ref'];
			}
		}
		else
		{
			$result = $ssoDataRecordReturnArray['result'];
			$resultParam = $ssoDataRecordReturnArray['resultparam'];
		}

		// only proceed if no error has occured
		if ($result == '')
		{
			// if there is a ssoCallback function call it and pass in the data from the datastore
			if ((method_exists('ExternalCustomerAccountObj', 'ssoCallback')) && ($result == ''))
			{
				$callbackArray = ExternalCustomerAccountObj::ssoCallback($paramArray);

				if ($callbackArray['result'] == '')
				{
					// update the data store with the data recieved from the callback
					$ssoDataUpdateReturnArray = AuthenticateObj::updateAuthenticationDataRecord($ssoKey, $callbackArray['ssoprivatedata'], false);

					if ($ssoDataUpdateReturnArray['result'] != '')
					{
			        	$result = $ssoDataUpdateReturnArray['result'];
			        	$resultParam = $ssoDataUpdateReturnArray['resultparam'];
					}
		        }
		        else
		        {
		        	$result = $callbackArray['result'];
		        	$resultParam = $callbackArray['resultparam'];
		        }
			}
		}
	}
	else
	{
		$result = $readCookieReturnArray['result'];
		$resultParam = $readCookieReturnArray['resultparam'];
	}
}

// process the result
if (($result == '') && ($returnURL != ''))
{
	// if the reaosn is to edit the project the call to redirect has to be done as a result to an ajax call
	if (($ssoReason == TPX_USER_AUTH_REASON_ONLINE_PROJECT_EDIT) || ($ssoReason == TPX_USER_AUTH_REASON_HIGHLEVEL_ONLINE_PROJECT_CONTINUE_EDIT))
	{
		require_once('../Welcome/Welcome_view.php');

		// If the ref is not greater than 0, this could be if it is an old record before the ref was added to the AUTHENTICATIONDATASTORE, try to parse it from the $returnURL.
		if ($ref <= 0)
		{
			$parsedURL = parse_url($returnURL);
			parse_str($parsedURL['query'], $queryArray);

			if (array_key_exists['ref'])
			{
				$ref = $queryArray['ref'];
			}
		}

		if ($ref > 0)
		{
			// Restore the session so we can get the csrftoken.
			$gSession = DatabaseObj::getSessionData($ref);

			Welcome_view::ajaxSSORedirect($returnURL, $ssoReason);
		}
		else
		{
			$result = 'str_ErrorSSOGeneral';
			$resultParam = 'Error Code: ' . TPX_SSO_ERROR_CODE_MISSING_SESSION_REF;
			error_log('Unable to load session, no ref found.');

			$browserDataArray = UtilsObj::detectDevice(false);

			Welcome_view::displaySSOError($result, $resultParam, ($browserDataArray['ismobiledevice'] == "1") ? true : false);
		}
	}
	else
	{
		setcookie(TPX_SSO_COOKIE_NAME, null, 0, '/', '', UtilsObj::needSecureCookies());

		// perform a temporary re-direct to the specified url 
		header('Location: ' . $returnURL, true, 302);
			
		// do not cache the re-direction (temporary re-directs still get cached by the browser)
		header('Expires: ' . gmdate('D, j M Y H:i:s') . ' GMT');
		header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
		header('Cache-Control: no-store, no-cache, must-revalidate');
		header('Cache-Control: post-check=0, pre-check=0', false);
		header('Pragma: no-cache');
	}

}
else
{
	if (($returnURL == '') && ($result == ''))
	{
		$result = 'str_ErrorSSOGeneral';
		$resultParam = 'Error Code: ' . TPX_SSO_ERROR_CODE_MISSING_REDIRECT_URL;
		error_log('No Return URL set.');
	}

	// display the error
	require_once('../Welcome/Welcome_view.php');
	
	$browserDataArray = UtilsObj::detectDevice(false);

	Welcome_view::displaySSOError($result, $resultParam, ($browserDataArray['ismobiledevice'] == "1") ? true : false);
}

?>