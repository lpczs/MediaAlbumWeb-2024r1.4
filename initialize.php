<?php
// main initialization which is called by fusebox
// before any other processing is performed
require __DIR__ . '/libs/external/vendor/autoload.php';

error_reporting(E_ALL);

ini_set('log_errors', true);

mysqli_report(MYSQLI_REPORT_ERROR);

require_once('../Utils/UtilsCoreIncludes.php');

// read the config file
$ac_config = UtilsObj::readConfigFile('../config/mediaalbumweb.conf');

$basePath = UtilsObj::getTaopixWebInstallPath();
if (file_exists($basePath . '/preBootActions.php'))
{
	include $basePath . '/preBootActions.php';
}

if (!array_key_exists('ALLOWSELFSIGNEDSSLCERTIFICATES', $ac_config))
{
	$ac_config['ALLOWSELFSIGNEDSSLCERTIFICATES'] = 0;
}

// if the ALLOWSELFSIGNEDSSLCERTIFICATES is set to 0 then we must set CURLOPT_SSL_VERIFYPEER to true.
$ac_config['SSLVERIFYPEER'] = ($ac_config['ALLOWSELFSIGNEDSSLCERTIFICATES'] == 0);

// get the constants
$gConstants = DatabaseObj::getConstants();

if (array_key_exists('SENTRYKEYPHPURL', $ac_config))
{
	$config = ['dsn' => $ac_config['SENTRYKEYPHPURL']];
	if (file_exists("../version.txt"))
	{
		$config['release'] = file_get_contents("../version.txt");
	}

	Sentry\init($config);
}

// get the session
$gSession = AuthenticateObj::getCurrentSessionData();

if ($gSession['ref'] > 0)
{
    if ($gSession['browserlanguagecode'] === '')
    {
        $gSession['browserlanguagecode'] = UtilsObj::getBrowserLocale();
    }
}
else
{
    global $gDefaultSiteBrandingCode;
    if ($gDefaultSiteBrandingCode == '')
	{
		if (isset($_SERVER['HTTP_HOST']))
		{
			$host = $_SERVER['HTTP_HOST'];
			$gDefaultSiteBrandingCode = DatabaseObj::getBrandFromWebUrl($host);
		}
	}
    AuthenticateObj::setSessionWebBrand($gDefaultSiteBrandingCode);
}

$gAuthSession = true;

// Init order
$gOrder = null;
