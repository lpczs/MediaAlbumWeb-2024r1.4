<?php
chdir('../../');

require __DIR__ . '/../../../libs/external/vendor/autoload.php';
require '../Utils/UtilsConstants.php';

use Taopix\Connector\Taopix\TaopixConnector;

$headers = getallheaders();
$webHookTopic = $headers['X-Taopix-Topic'];

$taopixConnector = new TaopixConnector('Taopix', []);

$ac_config = $taopixConnector->getACConfig();
$gConstants = $taopixConnector->getUtils()->getConstants();

$payload = @file_get_contents('php://input');
$payloadArray = json_decode($payload, true);

if ($taopixConnector->verifyWebhookHash($payload))
{
	switch($webHookTopic)
	{
		case 'orders/paid':
		{
			$taopixConnector->ordersPaid($payloadArray);
			break;
		}
		case 'voucher/create':
		{
			$taopixConnector->createVoucher($payloadArray);
			break;
		}
		case 'voucher/update':
		{
			$taopixConnector->updateVoucher($payloadArray);
			break;
		}
		case 'voucher/delete':
		{
			$taopixConnector->deleteVoucher($payloadArray);
			break;
		}
        case 'user/create':
        {
            $taopixConnector->createUserAccountFromWebView($payloadArray);
            break;
        }
        case 'user/login':
        {
            $taopixConnector->processLoginFromWebView($payloadArray);
            break;
        }
        case 'user/resetpassword':
        {
            $taopixConnector->processForgotPasswordFromWebView($payloadArray);
            break;
        }
  	}
}
else
{
	$taopixConnector->setWebhookError('Signature Mismatch');
	header("HTTP/1.1 400");
}
