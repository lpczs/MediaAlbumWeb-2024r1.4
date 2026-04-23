<?php
error_reporting(E_ALL);

ini_set('log_errors', true);

// step back to the webroot directory
chdir('../');

require_once('../initialize.php');
require_once('../Utils/UtilsDeviceDetection.php');
 
$result = '';
$resultParam = '';
$ssoKey = '';
$ssoNewKey = '';
$brandcode = '';
$ssoSystemURL = '';
$cookieName = '';

// only allow this callback to be ran if sso is configured
if (($gConstants['optionwscrp']) && (file_exists('../Customise/scripts/EDL_ExternalCustomerAccount.php')))
{
    require_once('../Customise/scripts/EDL_ExternalCustomerAccount.php');

    $ssoKey = UtilsObj::getGETParam('ssoref', '');
    $brandCode = UtilsObj::getGETParam('brandcode', '');

    if ($ssoKey == '')
    {
		$result = 'str_ErrorSSOGeneral';
		$resultParam = 'Error Code: ' . TPX_SSO_ERROR_CODE_EMPTY_COOKIE;
		error_log('SSO Empty Ref');
    }
    else
    {
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
				$redirectionURL = $ssoDataRecordReturnArray['originurl'];
				$ssoSystemURL = $ssoDataRecordReturnArray['ssourl'];
				$ssoNewKey = hash("sha256", uniqid());

				// add the sso key to the redirection url
				// this is because we have changed the original sso key so the end point needs to know the new one
				$redirectionURL = UtilsObj::addURLParameter($redirectionURL, 'ssokey', $ssoNewKey);

				$ssoDataRecordReturnArray = AuthenticateObj::updateSSODataRecord($ssoKey, $ssoNewKey, $redirectionURL);

				if ($ssoDataRecordReturnArray['result'] != '')
				{
					$result = $ssoDataRecordReturnArray['result'];
					$resultParam = $ssoDataRecordReturnArray['resultparam'];
				}

			}
		}
		else
		{
			$result = $ssoDataRecordReturnArray['result'];
			$resultParam = $ssoDataRecordReturnArray['resultparam'];
		}
	}
}
else
{
	$result = 'str_ErrorSSOGeneral';
	$resultParam = 'Error Code: ' . TPX_SSO_ERROR_CODE_NOT_CONFIGURED;
	error_log('SSO Not setup');
}

if ($result == '')
{
	// set the cookie which will be picked up and deleted by the callback url when the CRM calls back
	setcookie(TPX_SSO_COOKIE_NAME, $ssoNewKey, 0, '/', '', UtilsObj::needSecureCookies());

	// perform a temporary re-direct to the specified url 
	header('Location: ' . $ssoSystemURL, true, 302);
		
	// do not cache the re-direction (temporary re-directs still get cached by the browser)
	header('Expires: ' . gmdate('D, j M Y H:i:s') . ' GMT');
	header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
	header('Cache-Control: no-store, no-cache, must-revalidate');
	header('Cache-Control: post-check=0, pre-check=0', false);
	header('Pragma: no-cache');

}
else
{
	// display the error
	require_once('../Welcome/Welcome_view.php');
	
	$browserDataArray = UtilsObj::detectDevice(false);

	Welcome_view::displaySSOError($result, $resultParam, ($browserDataArray['ismobiledevice'] == "1") ? true : false);
}

?>