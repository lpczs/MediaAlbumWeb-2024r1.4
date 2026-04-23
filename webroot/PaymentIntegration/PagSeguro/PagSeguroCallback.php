<?php

require __DIR__ . '/../../../libs/external/vendor/autoload.php';

// we must perform the standard initialization as we have bypassed the Fusebox framework
error_reporting(E_ALL);
ini_set('log_errors', true);

// step back to the webroot directory
chdir('../../');

require_once('../Utils/UtilsCoreIncludes.php');
require_once('../Order/Order_control.php');
require_once('../Order/PaymentIntegration/Pagseguro.php');
require_once('../Order/PaymentIntegration/PaymentIntegration.php');
$ac_config = UtilsObj::readConfigFile('../config/mediaalbumweb.conf');

// iniitialise a dummy Pagseguro object for the sole purpose of using querying the server
// initialising the whole object has a minimal memory impact while ensuring that the api query will always be carried out the same way
$config = PaymentIntegrationObj::readCCIConfigFile('../config/PAGSEGURO.conf', '', '');
$dummySession = array();
$communicationPagseguroObject = new Pagseguro($config, $dummySession, $_GET, $_POST);

// if theres no transaction ID in the get then the callback isn't coming from Pagseguro and can be safely ignored
if (array_key_exists('notificationCode', $_POST))
{
	$pagseguroTransactionID = $_POST['notificationCode'];

	global $gSession;

	// initialise the xml holding variable as a global so the Pagseguro server only needs to be called once and the xml only parsed once
	global $callbackQueryResultXML;

	$serverResponse = $communicationPagseguroObject->queryPagseguroTransaction($pagseguroTransactionID, 'automatic');

	try
	{
		$callbackQueryResultXML = new SimpleXMLElement($serverResponse);

		if (! isset($callbackQueryResultXML['errors']))
		{
			// retrieve the session reference from the XML
			$_GET['ref'] = (string) $callbackQueryResultXML->reference;

			// get the constants
			$gConstants = DatabaseObj::getConstants();

			// use the retrieved session reference to generate the session
			$gSession = AuthenticateObj::getCurrentSessionData();

			$gAuthSession = true;

			// Now we have the session initialised proceed through the automatic callback as normal
			Order_control::ccAutomaticCallback();
		}
		else
		{
			PaymentIntegrationObj::logPaymentGatewayData($communicationPagseguroObject->getConfig(), DatabaseObj::getServerTime(), $callbackQueryResultXML['errors']['error'][0]['message'], 'Pagseguro Callback returned error array');
		}
	}
	catch (exception $e)
	{
		PaymentIntegrationObj::logPaymentGatewayData($communicationPagseguroObject->getConfig(), DatabaseObj::getServerTime(), $serverResponse, 'Pagseguro Callback Curl failure');
	}
}

?>