<?php
chdir('../../');

require '../libs/external/vendor/autoload.php';
require '../Utils/Utils.php';

use Taopix\Connector\Shopify\ShopifyConnector;

// pull request data from Shopify proxy request.
// GET params are stripped from proxy so we need to read them from REQUEST_URI
$proxyRequestData = [];
parse_str(parse_url($_SERVER['REQUEST_URI'], PHP_URL_QUERY), $proxyRequestData);

$shopifyConnector = new ShopifyConnector($proxyRequestData['shop']);

if ($shopifyConnector->verifyProxyHash($proxyRequestData))
{
	switch(UtilsObj::getGetParam('action'))
	{
		case 'create':
		{
			if ($shopifyConnector->detectDevice())
			{
				try
				{
					$productID = UtilsObj::getPOSTParam('product_id');
					$deviceDetection = UtilsObj::getPOSTParam('dd');
					$customer = [
						'id' =>  UtilsObj::getPOSTParam('customerid'),
						'email' =>  UtilsObj::getPOSTParam('customeremail'),
						'firstname' =>  UtilsObj::getPOSTParam('customerfirstname'),
						'lastname' =>  UtilsObj::getPOSTParam('customerlastname'),
					];
					$browserLanguageCode = UtilsObj::getPOSTParam('l');

					$customParams = array_filter($_POST, function($value, $key) {
						return substr($key, 0, 17) == TPX_CUSTOMPARAM_KEY_PREFIX;
					}, ARRAY_FILTER_USE_BOTH);
					$designURL = $shopifyConnector->createProject($productID, $deviceDetection, $customer, $browserLanguageCode, $customParams)['redirecturl'];
					redirectToDesigner($designURL, $proxyRequestData['shop']);
				}
				catch (Throwable $pError)
				{
					echo sprintf('<p>Error: %s in file %s on line %d</p>', $pError->getMessage(), $pError->getFile(),  $pError->getLine());
				}
			}

			break;
		}
		case 'designer':
		{
			$route = $proxyRequestData['route'] ?? '';
			$ref = $proxyRequestData['ref'];
			$ref = ($route !== '') ? $ref . '#' . $route : $ref;

			$designURL = (isset($proxyRequestData['lsp'])) ? sprintf('%s?action=OnlineDesigner.initialise&ref=%s', $shopifyConnector->getOnlineURL(), $ref) : sprintf('%s?ref=%s', $shopifyConnector->getOnlineUiURL(), $ref);

			$parsedDesignURL = parse_url($designURL);

			$designerDomain = $parsedDesignURL['scheme'] . '://' . $parsedDesignURL['host'];

			// Variables to pass to designer.php.
			$controlCentreURL = $shopifyConnector->getUtils()->correctPath($shopifyConnector->getControlCentreURL(), '/', false);

			include (__DIR__ . '/views/designer.php');
			break;
		}
		case 'edit':
		{
			if ($shopifyConnector->detectDevice())
			{
				$projectRef = UtilsObj::getArrayParam($proxyRequestData, 'projectref');
				$deviceDetection = UtilsObj::getPOSTParam('dd');
				$customerID = UtilsObj::getArrayParam($proxyRequestData, 'customerid', null);
				$browserLanguageCode = UtilsObj::getArrayParam($proxyRequestData, 'l');
				$designURL = $shopifyConnector->editProject($projectRef, $customerID, $deviceDetection, $browserLanguageCode);
				redirectToDesigner($designURL, $proxyRequestData['shop']);
			}
			break;
		}
		case 'duplicate':
		{
			if ($shopifyConnector->detectDevice())
			{
				$projectRef = UtilsObj::getArrayParam($proxyRequestData, 'projectref');
				$deviceDetection = UtilsObj::getPOSTParam('dd');
				$customerID = UtilsObj::getArrayParam($proxyRequestData, 'customerid', null);
				$projectName = UtilsObj::getArrayParam($proxyRequestData, 'projectname', null);
				$browserLanguageCode = UtilsObj::getArrayParam($proxyRequestData, 'l');
				$designURL = $shopifyConnector->duplicateProject($projectRef, $projectName, $customerID, $deviceDetection, $browserLanguageCode);
				redirectToDesigner($designURL, $proxyRequestData['shop']);
			}
			break;
		}
		case 'projects':
		{
			$loggedInCustomerID = UtilsObj::getArrayParam($proxyRequestData, 'logged_in_customer_id');
			$customerID = UtilsObj::getArrayParam($proxyRequestData, 'customer');

			//projects page should only be displayed if user is logged into shopify
			if ($loggedInCustomerID === '')
			{
				break;
			}

			// Initialise the Shopify SDK so we can make requests.
			$shopifyConnector->initShopifySDK();

			// Get the shop secret.
			$shopMetaFields = $shopifyConnector->requestShopMetaFields();
			$secretMetaField = $shopMetaFields->getByNameSpaceAndKey('taopix', 'secret')[0];
			$loggedInCustomerHash = $shopifyConnector->generateUserIDHash($loggedInCustomerID, $secretMetaField->getValue());

			//check same user logged into shopify as is being requested
			if ($loggedInCustomerHash !== $customerID)
			{
				break;
			}

			$browserLanguageCode = UtilsObj::getArrayParam($proxyRequestData, 'l');
			$designURL = sprintf('%s?fsaction=OnlineAPI.viewUsersProjectList&customerid=%s&l=%s', $shopifyConnector->getBrandControlCentreURL(), $customerID, $browserLanguageCode);
			$parsedDesignURL = parse_url($designURL);

			// Variables to pass to designer.php.
			$controlCentreURL = $shopifyConnector->getUtils()->correctPath($shopifyConnector->getControlCentreURL(), '/', false);
			$designerDomain = $shopifyConnector->getUtils()->correctPath($shopifyConnector->getOnlineURL(), '/', false);

			include (__DIR__ . '/views/designer.php');
			break;
		}
		case 'share':
		{
			if ($shopifyConnector->detectDevice())
			{
				$projectRef = UtilsObj::getArrayParam($proxyRequestData, 'projectref');
				$deviceDetection = UtilsObj::getPOSTParam('dd');
				$browserLanguageCode = UtilsObj::getArrayParam($proxyRequestData, 'l');
				$designURL = $shopifyConnector->previewProject($projectRef, $deviceDetection, $browserLanguageCode);
				redirectToDesigner($designURL, $proxyRequestData['shop']);
			}
			break;
		}
		case 'boot':
		{
			try
			{
				$productID = UtilsObj::getPOSTParam('product_id');
				$deviceDetection = UtilsObj::getPOSTParam('dd');
				$customer = [
					'id' =>  UtilsObj::getPOSTParam('customerid'),
					'email' =>  UtilsObj::getPOSTParam('customeremail'),
					'firstname' =>  UtilsObj::getPOSTParam('customerfirstname'),
					'lastname' =>  UtilsObj::getPOSTParam('customerlastname'),
				];
				$browserLanguageCode = UtilsObj::getPOSTParam('l');

				$customParams = array_filter($_POST, function($value, $key) {
					return substr($key, 0, 17) == TPX_CUSTOMPARAM_KEY_PREFIX;
				}, ARRAY_FILTER_USE_BOTH);

				$createResponse = $shopifyConnector->createProject($productID, $deviceDetection, $customer, $browserLanguageCode, $customParams);
				$designURL = $createResponse['redirecturl'];
				$onlineApiUrl = $createResponse['onlineapiurl'];

				echo json_encode(['designurl' => $designURL, 'onlineapiurl' => $onlineApiUrl]);

			}
			catch (Throwable $pError)
			{
				echo sprintf('<p>Error: %s in file %s on line %d</p>', $pError->getMessage(), $pError->getFile(),  $pError->getLine());
			}
		}

		break;
	}
}

/**
 * Redirects to /tools/designer/designer to display the Online Designer.
 *
 * @param string $pDesignURL Designer initialise URL.
 * @param string $pShopURL Shopify store URL.
 */
function redirectToDesigner(string $pDesignURL, string $pShopURL)
{
	parse_str($pDesignURL, $parsedDesignerURL);

	if (array_key_exists('lsp', $parsedDesignerURL))
	{
		$redirectURL = sprintf('%s://%s/tools/designer/designer?ref=%s',  UtilsObj::getArrayParam($_SERVER, 'REQUEST_SCHEME'), $pShopURL, UtilsObj::getArrayParam($parsedDesignerURL, 'ref')) . '&lsp=1';
	}
	else
	{
		$query = UtilsObj::getArrayParam(parse_url($pDesignURL), 'query') . '&route=' . parse_url($pDesignURL, PHP_URL_FRAGMENT);
		$redirectURL = sprintf('%s://%s/tools/designer/designer?%s',  UtilsObj::getArrayParam($_SERVER, 'REQUEST_SCHEME'), $pShopURL, $query);
	}

	header('Location: ' . $redirectURL);
	exit;
}
