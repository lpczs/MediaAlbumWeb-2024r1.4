<?php

require __DIR__ . '/../../../libs/external/vendor/autoload.php';

error_reporting(E_ALL);
ini_set('log_errors', true);

// Step back to the webroot directory.
chdir('../../');

require_once('../Utils/UtilsCoreIncludes.php');
require_once('../Order/Order_control.php');
require_once('../Order/PaymentIntegration/PaymentIntegration.php');
require_once('../Order/PaymentIntegration/WebPay.php');

// Read the config file.
$ac_config = UtilsObj::readConfigFile('../config/mediaalbumweb.conf');

// Get the constants.
$gConstants = DatabaseObj::getConstants();
$_GET['ref'] = UtilsObj::getGETParam('ref');

$gSession = AuthenticateObj::getCurrentSessionData();

$webPay = PaymentIntegrationObj::createPaymentGatewayInstanceReferenced($gSession, 'WebPay');

$_SERVER['HTTP_ACCEPT_LANGUAGE'] = $gSession['browserlanguagecode'];

$gAuthSession = true;

$tokenAuth = UtilsObj::getPOSTParam('token_ws');

if ($tokenAuth != '')
{
	if ($gSession['webpayStage'] == 'initialisation')
	{		
		if ($tokenAuth == $gSession['webpayToken'])
		{
			$webPayConfig = $webPay->getConfig();

			// Get the transaction results from WebPay.
			$transaction = $webPay->initializeTransaction($webPayConfig['ENVIROMENT'], $webPayConfig['COMMERCECODE']);

			if ((is_array($transaction)) && (array_key_exists('error', $transaction)))
			{
				// `Transaction errored out.
				DatabaseObj::updateSession();

				$actionURL = UtilsObj::correctPath($gSession['webbrandweburl']) . '?fsaction=Order.ccManualCallback&ref=' . UtilsObj::getGETParam('ref');

				$webPay->redirect($actionURL, $transaction);
			}
			else
			{
				$transactionResult = $transaction->getTransactionResult($tokenAuth);
				$gSession['webpayParameters'] = serialize($transactionResult);									
		
				if ($transactionResult->detailOutput->responseCode == 0)
				{					
					$gSession['webpayStage'] = 'acknowledgement';				

					// Set the ccidata to remember we have jumped to WebPay.
					$gSession['order']['ccidata'] = 'start';
					
					DatabaseObj::updateSession();
					
					// Redirect to voucher page.
					$webPay->redirect($transactionResult->urlRedirection, array('token_ws' => $tokenAuth));
				}
				else
				{
					// `Transaction was rejected, so redirect to the manual callback page to display the error.
					DatabaseObj::updateSession();

					$actionURL = UtilsObj::correctPath($gSession['webbrandweburl']) . '?fsaction=Order.ccManualCallback&ref=' . UtilsObj::getGETParam('ref');

					$webPay->redirect($actionURL, $transactionResult);
				}
			}
		}
	}
	else if ($gSession['webpayStage'] == 'acknowledgement')
	{
		// Returned from WebPay receipt page, so redirect to the manual callback page to display the order confirmation page.
		$actionURL = UtilsObj::correctPath($gSession['webbrandweburl']) . '?fsaction=Order.ccManualCallback&ref=' . UtilsObj::getGETParam('ref');

		$webPay->redirect($actionURL, '');
	}
}
else
{
	// Transaction was cancelled, so redirect to the cancelled callback page.
	$actionURL = UtilsObj::correctPath($gSession['webbrandweburl']) . '?fsaction=Order.ccCancelCallback&ref=' . UtilsObj::getGETParam('ref');

	$webPay->redirect($actionURL, $_POST);
}
?>