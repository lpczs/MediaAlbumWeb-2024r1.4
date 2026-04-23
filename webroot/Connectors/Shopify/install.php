<?php
chdir('../../');

require_once '../libs/external/vendor/autoload.php';

use Taopix\Connector\Shopify\ShopifyConnector;

$shopifyConnector = new ShopifyConnector($_GET['shop']);

if ($shopifyConnector->verifyHash())
{
	try
	{
		$shopifyConnector->install();
	}
	catch(Exception $pError)
	{
		echo sprintf('<p>Error: %s in file %s on line %d</p>', $pError->getMessage(), $pError->getFile(),  $pError->getLine());
	}
}
else
{
	throw new Exception('Hash not valid');
}
