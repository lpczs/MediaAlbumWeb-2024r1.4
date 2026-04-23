<?php
chdir('../../');

require __DIR__ . '/../../../libs/external/vendor/autoload.php';
require '../Utils/UtilsConstants.php';

use Taopix\Connector\Shopify\ShopifyConnector;

$headers = getallheaders();
$storeURL = $headers['X-Shopify-Shop-Domain'];
$webHookTopic = $headers['X-Shopify-Topic'];

$shopifyConnector = new ShopifyConnector($storeURL);

$gConstants = $shopifyConnector->getUtils()->getConstants();

$payload = @file_get_contents('php://input');
$payloadArray = json_decode($payload, true);
    
header("HTTP/1.1 200 OK");
header('Content-Encoding: none');
header('Content-Length: '.ob_get_length());
header('Connection: close');
flush();

if ($shopifyConnector->verifyWebhookHash($payload))
{
	switch($webHookTopic)
	{	
		case 'orders/paid':
		{
			//only process if this contains a taopix product
			if (!strpos($payload, '__taopix_project_id') === false) {
				$shopifyConnector->ordersPaid($payloadArray);
			}
			break;
		}
		case 'products/delete':
		{
			$shopifyConnector->productDeletedViaShopify($payloadArray);
			break;
		}
		case 'customers/data_request':
		{
			$shopifyConnector->requestShopifyCustomerData($payloadArray);
			break;	
		}
		case 'customers/redact':
		{
			$shopifyConnector->deleteShopifyCustomerData($payloadArray);
			break;
		}
		case 'shop/redact':
		{
			$shopifyConnector->deleteShopifyShopData($payloadArray);
			break;
		}
		case 'products/update':
		{
			$shopifyConnector->productUpdatedViaShopify($payloadArray);
			break;
		}
		case 'bulk_operations/finish':
		{
			$shopifyConnector->bulkQueryComplete($payloadArray);
			break;
		}
		case 'profiles/create':
		case 'profiles/update':
		case 'profiles/delete':
		{
			$shopifyConnector->deliveryProfileUpdateTask();
			break;
		}
		case 'discounts/create':
		case 'discounts/update':
		case 'discounts/delete':
		{
			error_log(print_r($payloadArray,true));
			break;
		}
  }
}
