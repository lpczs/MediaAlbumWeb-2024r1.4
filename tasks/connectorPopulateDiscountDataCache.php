<?php
/*
 * This task is used to cache discount data from shopify
 */

ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once(__DIR__ . '/../libs/external/vendor/autoload.php');
require_once('../Utils/UtilsCoreIncludes.php');

require '../libs/external/vendor/autoload.php';
use Taopix\Connector\Shopify\ShopifyConnector;
use Taopix\Connector\Shopify\Product;

set_time_limit(60);

$ac_config = UtilsObj::readConfigFile('../config/mediaalbumweb.conf');
$gConstants = DatabaseObj::getConstants();
global $gSession;

class connectorPopulateDiscountDataCache 
{
	// define default settings for this task
	static function register()
	{
		$defaultSettings = array();

		/*
		* $defaultSettings('type') defines type of tasks
		* 0 - scheduled
		* 1 - service
		* 2 - manual
		*/
		$defaultSettings['type'] = '0';
		$defaultSettings['code'] = 'TAOPIX_CONNECTORPOPULATEDISCOUNTDATACACHE';
		$defaultSettings['name'] = 'it italian desciption<p>fr french description<p>es spanish text';

		/*
		* $defaultSettings('intervalType') defines inteval value
		* 1 - Number of minutes
		* 2 - Exact time of the day
		* 3 - Number of days
		*/

		$defaultSettings['intervalType'] = '1';
		$defaultSettings['intervalValue'] = '1';
		$defaultSettings['maxRunCount'] = '10';
		$defaultSettings['deleteCompletedDays'] = '5';

		return $defaultSettings;
	}

    static function run($pEventID, $pTaskCode = '')
    {
		$taskCode = self::register();
        $taskCode = $taskCode['code'];
		UtilsObj::resetPHPScriptTimeout(30);

		$resultMessage = '';

		$systemConfig = DatabaseObj::getSystemConfig();
		$gSession['licensekeydata']['systemkey'] = $systemConfig['systemkey'];

		$resultArray = UtilsObj::getReturnArray();
		$recordID = 0;
		$connectorURL = '';
		$connectorPrimaryDomain = '';
		$connectorKey = '';
		$connectorSecret = '';
		$connectorInstallURL = '';
		$connectorName = '';
		$brandCode = '';
		$licenseKeyCode = '';
		$pricesIncludeTax = 0;

		// Setup default.
		$connectorArray = [];

		$dbObj = DatabaseObj::getGlobalDBConnection();

		if ($dbObj) {
			$stmt = $dbObj->prepare('SELECT 
										`id`, `connectorurl`, `connectorprimarydomain`, `connectorkey`, `connectorsecret`, `connectorinstallurl`, 
										`connectorname`, `brandcode`, `licensekeycode`, `pricesincludetax`
									FROM
										`CONNECTORS`
									WHERE 
										`connectoraccesstoken1` <> ""
									');

			if ($stmt) {
				if ($stmt->execute()) {
					if ($stmt->store_result()) {
						if ($stmt->num_rows > 0) {
							if ($stmt->bind_result(
								$recordID, $connectorURL, $connectorPrimaryDomain, $connectorKey, $connectorSecret, $connectorInstallURL,
								$connectorName, $brandCode, $licenseKeyCode, $pricesIncludeTax
							)) {
								while ($stmt->fetch()) {
									$connectorArray[$recordID]['connectorid'] = $recordID;
									$connectorArray[$recordID]['connectorurl'] = UtilsObj::ExtJSEscape($connectorURL);
									$connectorArray[$recordID]['connectorprimarydomain'] = UtilsObj::ExtJSEscape($connectorPrimaryDomain);
									$connectorArray[$recordID]['connectorkey'] = UtilsObj::ExtJSEscape($connectorKey);
									$connectorArray[$recordID]['connectorsecret'] = UtilsObj::ExtJSEscape(UtilsObj::decryptData($connectorSecret, $gSession['licensekeydata']['systemkey'], true));
									$connectorArray[$recordID]['connectorinstallurl'] = UtilsObj::ExtJSEscape($connectorInstallURL);
									$connectorArray[$recordID]['connectorname'] = UtilsObj::ExtJSEscape($connectorName);
									$connectorArray[$recordID]['brandcode'] = UtilsObj::ExtJSEscape($brandCode);
									$connectorArray[$recordID]['licensekeycode'] = UtilsObj::ExtJSEscape($licenseKeyCode);
									$connectorArray[$recordID]['pricesincludetax'] = $pricesIncludeTax;
								}
							} else {
								$resultArray['error'] = __FUNCTION__ . ' bind result error: ' . $dbObj->error;
							}
						}
					} else {
						$resultArray['error'] = __FUNCTION__ . ' store result error: ' . $dbObj->error;
					}
				} else {
					$resultArray['error'] = __FUNCTION__ . ' execute error: ' . $dbObj->error;
				}

				$stmt->free_result();
				$stmt->close();
				$stmt = null;
			}

			$dbObj->close();
		} else {
			$resultArray['error'] = __FUNCTION__ . ' Unable to connect to the database: ' . $dbObj->error;
		}

		foreach($connectorArray as $connector)
		{
			try
			{
				$shopURL = $connector['connectorurl'] . '.myshopify.com';
				$brandCode = $connector['brandcode'];
				$shopifyConnector = new ShopifyConnector($shopURL);
				$graphQL = $shopifyConnector->initGraphQL();
				$product = new Product($graphQL);  
				$ac_config = $shopifyConnector->getACConfig();     

				$filePath = $ac_config['CONNECTORRESOURCESPATH'];
				$filePath = UtilsObj::correctPath($filePath, DIRECTORY_SEPARATOR, true);

				if ($brandCode != '')
				{
					$filePath .= $brandCode . DIRECTORY_SEPARATOR; 
				}

				$filePath .= 'discounts' . DIRECTORY_SEPARATOR;

				$extension = '.jsonl';
				$priceRulesName = 'priceRules' . $extension;
				$automaticBasicDiscountName = 'automaticBasicDiscount' . $extension;
				$automaticBxgyDiscountName = 'automaticBxgyDiscount' . $extension;
				$priceRulesFileFullPath = $filePath . $priceRulesName;
				$automaticBasicDiscountFileFullPath = $filePath . $automaticBasicDiscountName;
				$automaticBxgyDiscountFileFullPath = $filePath . $automaticBxgyDiscountName;
				
				if (!file_exists($priceRulesFileFullPath))
				{
					$shopifyConnector->discountDataBulkQuery('PRICERULES');
				}
				else if (!file_exists($automaticBasicDiscountFileFullPath))
				{
					$shopifyConnector->discountDataBulkQuery('AUTOMATICBASIC');
				}
				else if (!file_exists($automaticBxgyDiscountFileFullPath))
				{
					$shopifyConnector->discountDataBulkQuery('AUTOMATICBXGY');
				}

				//delete any old files which are more than 3 days old
				UtilsObj::deleteOldFiles($filePath . 'complete' . DIRECTORY_SEPARATOR, 4320);
			}
			catch(Exception $e)
			{
				$resultMessage = 'en ' . $e->getMessage();
				TaskObj::writeLogEntry($resultMessage);
			}
		}

		return $resultMessage;
	}

}

?>