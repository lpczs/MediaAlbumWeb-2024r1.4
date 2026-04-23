<?php

require_once('../Utils/UtilsAddress.php');

use Taopix\Connector\Shopify\ShopifyConnector;
use Taopix\Connector\Shopify\Product;

class AdminConnectors_model
{
	static function connectorsList($pParamArray)
	{
		$connectorItem = array();
		$resultArray = array();
		$resultArray['data'] = array();
		$typesArray = array();
		$paramArray = array();
		$stmtArray = array();
		$totalCount = 0;
		$recordID = 0;
		$brandID = 0;
		$brandingCompany = '';
		$brandingOwner = '';
		$brandingCode = '';
		$brandingName = '';
		$applicationName = '';
		$connectorURL = '';
		$connectorKey = '';
		$connectorSecret = '';
		$connectorInstallURL = '';
		$connectorAccessToken1 = '';
		$connectorAccessToken2 = '';
		$isActive = 0;
		$error = '';

		$start = (isset($pParamArray['start'])) ? $pParamArray['start'] : 0;
		$limit = (isset($pParamArray['limit'])) ? $pParamArray['limit'] : 100;

		$sortBy = (isset($pParamArray['sort'])) ? $pParamArray['sort'] : '';
		$sortDir = (isset($pParamArray['dir'])) ? $pParamArray['dir'] : '';
		$searchFields = $pParamArray['fields'];

		$dbObj = DatabaseObj::getGlobalDBConnection();
		if ($dbObj) {
			$customSort = '';
			if ($sortBy != '') {
				switch ($sortBy) {
					case 'foldername':
						$sortBy = 'applicationname ' . $sortDir;
						break;
					case 'connectorurllink':
						$sortBy = 'connectorurl ' . $sortDir;
						break;
				}
				$customSort = ', ' . $sortBy;
			}

			if ($searchFields != '') {
				$searchQuery = $pParamArray['query'];
				$selectedfields = explode(',', str_replace('"', "", str_replace("]", "", str_replace("[", "", $searchFields))));

				if ($searchQuery != '') {
					foreach ($selectedfields as $value) {
						switch ($value) {
							case 'foldername':
								$value = 'applicationname';
								break;
							case 'connectorurllink':
								$value = 'connectorurl';
								break;
						}
						$stmtArray[] = '(`' . $value . '` LIKE ?)';
						$paramArray[] = '%' . $searchQuery . '%';
						$typesArray[] = 's';
					}
				}
			}

			if (count($stmtArray) > 0) {
				$stmtArray = ' AND (' . join(' OR ', $stmtArray) . ')';
			} else {
				$stmtArray = '';
			}

			$sql = '
			SELECT 
			`C`.`id`, `B`.`id` AS brandid, `B`.`companycode`, `B`.`owner`, `B`.`code`, `B`.`name`, `B`.`applicationname`, `C`.`connectorurl`, 
			`C`.`connectorkey`, `C`.`connectorsecret`, `C`.`connectorinstallurl`, `C`.`connectoraccesstoken1`, `C`.`connectoraccesstoken2`,`active` 
			FROM `CONNECTORS` `C`
			INNER JOIN  `BRANDING` `B` ON `B`.`code` = `C`.`brandcode`
			WHERE `C`.`connectorname` = "SHOPIFY"
			' . $stmtArray . ' 
			LIMIT ' . $limit . ' OFFSET ' . $start;



			if ($stmt = $dbObj->prepare($sql)) {
				$bindOK = DatabaseObj::bindParams($stmt, $typesArray, $paramArray);
				if ($bindOK) {
					$stmt->bind_result(
						$recordID, $brandID, $brandingCompany, $brandingOwner, $brandingCode, $brandingName, $applicationName,
						$connectorURL, $connectorKey, $connectorSecret, $connectorInstallURL, $connectorAccessToken1, $connectorAccessToken2,
						$isActive
					);
					if ($stmt->execute()) {
						if ($stmt->store_result()) {
							$totalCount = $stmt->num_rows;

							while ($stmt->fetch()) {
								$connectorItem['recordID'] = $recordID;
								$connectorItem['brandID'] = $brandID;
								$connectorItem['brandingCompany'] = $brandingCompany;
								$connectorItem['brandingOwner'] = $brandingOwner;
								$connectorItem['brandingCode'] = $brandingCode;
								$connectorItem['brandingName'] = $brandingName;
								$connectorItem['applicationName'] = $applicationName;
								$connectorItem['connectorURL'] = $connectorURL;
								$connectorItem['connectorKey'] = $connectorKey;
								$connectorItem['connectorSecret'] = $connectorSecret;
								$connectorItem['connectorInstallURL'] = $connectorInstallURL;
								$connectorItem['connectorAccessToken1'] = $connectorAccessToken1;
								$connectorItem['connectorAccessToken2'] = $connectorAccessToken2;
								$connectorItem['isActive'] = $isActive;
								$connectorItem['connectorStatus'] = self::getConnectorStatus($connectorURL, $connectorKey, $connectorSecret, $connectorInstallURL, $connectorAccessToken1, $connectorAccessToken2);
								array_push($resultArray['data'], $connectorItem);
							}
						} else {
							$error = __FUNCTION__ . ' store_result ' . $dbObj->error;
						}
					} else {
						$error = __FUNCTION__ . ' execute ' . $dbObj->error;
					}
				} else {
					$error = __FUNCTION__ . ' bind params ' . $dbObj->error;
				}
			}
		}
		$dbObj->close();

		$resultArray['error'] = $error;
		$resultArray['totalCount'] = $totalCount;
		return $resultArray;
	}

	static function displayEdit($pConnectorID)
	{
		$resultArray = self::getConnector($pConnectorID);
		$resultArray['webbrandinglist'] = self::getLicenseKeyAndBrandList();

		return $resultArray;
	}

	static function connectorsEdit($pConnectorData)
	{
		global $gSession;
		$resultArray = UtilsObj::getReturnArray();
		$error = '';
		$errorParam = '';

		$id = UtilsObj::getArrayParam($pConnectorData, 'id');

		if ($id > 0) {
			$connectorURL = UtilsObj::getArrayParam($pConnectorData, 'connectorurl');
			$connectorPrimaryDomain = UtilsObj::getArrayParam($pConnectorData, 'connectorprimarydomain');
			$connectorKey = UtilsObj::getArrayParam($pConnectorData, 'connectorkey');
			$connectorSecret = UtilsObj::encryptData(UtilsObj::getArrayParam($pConnectorData, 'connectorsecret'), $gSession['licensekeydata']['systemkey'], true);
			$connectorInstallUrl = UtilsObj::getArrayParam($pConnectorData, 'connectorinstallurl');
			$pricesIncludeTax = UtilsObj::getArrayParam($pConnectorData, 'pricesincludetax');

			$dbObj = DatabaseObj::getGlobalDBConnection();

			if ($dbObj) {
				$stmt = $dbObj->prepare('UPDATE 
											`CONNECTORS` 
										SET 
											`connectorurl` = ?, `connectorkey` = ?, `connectorsecret` = ?, `connectorinstallurl` = ?, 
											`connectorprimarydomain` = ?, `pricesincludetax` = ?
                                        WHERE `id` = ?');

				if ($stmt) {
					if ($stmt->bind_param(
						'ssssssi',
						$connectorURL, $connectorKey, $connectorSecret, $connectorInstallUrl, $connectorPrimaryDomain, $pricesIncludeTax, $id
					)) {
						if (!$stmt->execute()) {
							$error = 'str_DatabaseError';
							$errorParam = 'connectorsEdit execute ' . $dbObj->error;
						}
					} else {
						// could not bind parameters
						$error = 'str_DatabaseError';
						$errorParam = 'connectorsEdit bind ' . $dbObj->error;
					}
					if ($stmt) {
						$stmt->free_result();
						$stmt->close();
					}
				} else {
					// could not prepare statement
					$error = 'str_DatabaseError';
					$errorParam = 'connectorsEdit prepare ' . $dbObj->error;
				}
			} else {
				// could not open database connection
				$error = 'str_DatabaseError';
				$errorParam = 'connectorsEdit connect ' . $dbObj->error;
			}
		}

		$resultArray['error'] = $error;
		$resultArray['errorparam'] = $errorParam;
		return $resultArray;
	}

	static function connectorsAdd($pConnectorName, $pConnectorData)
	{
		global $gSession;
		$dataName = 'connectorid';
		$resultArray = UtilsObj::getReturnArray($dataName);
		$error = '';
		$errorParam = '';

		$connectorName = $pConnectorName;
		$brandAndLicenceKey = UtilsObj::getPOSTParam('webbrandinglist');
		$brandAndLicenseKeyArray = explode("@@", $brandAndLicenceKey);
		$brandCode = $brandAndLicenseKeyArray[0];
		$licenseKeyCode = $brandAndLicenseKeyArray[1];
		$connectorURL = UtilsObj::getArrayParam($pConnectorData, 'connectorurl');
		$connectorPrimaryDomain = UtilsObj::getArrayParam($pConnectorData, 'connectorprimarydomain');
		$connectorKey = UtilsObj::getArrayParam($pConnectorData, 'connectorkey');
		$connectorSecret = UtilsObj::encryptData(UtilsObj::getArrayParam($pConnectorData, 'connectorsecret'), $gSession['licensekeydata']['systemkey'], true);
		$connectorInstallUrl = UtilsObj::getArrayParam($pConnectorData, 'connectorinstallurl');
		$pricesIncludeTax = UtilsObj::getArrayParam($pConnectorData, 'pricesincludetax');

		if ($brandCode === 'Default') {
			$brandCode = '';
		}

		$dbObj = DatabaseObj::getGlobalDBConnection();

		if ($dbObj) {
			$stmt = $dbObj->prepare('INSERT INTO `CONNECTORS` (`connectorname`, `brandcode`, `connectorurl`, `connectorprimarydomain`,
			`connectorkey`, `connectorsecret`, `connectorinstallurl`, `licensekeycode`, `pricesincludetax` ) 
			VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)');

			if ($stmt) {
				if ($stmt->bind_param(
					'sssssssss',
					$connectorName, $brandCode, $connectorURL, $connectorPrimaryDomain, $connectorKey, $connectorSecret,
					$connectorInstallUrl, $licenseKeyCode, $pricesIncludeTax
				)) {
					if ($stmt->execute()) {
						$resultArray[$dataName] = $dbObj->insert_id;
					} else {
						$error = 'str_DatabaseError';
						$errorParam = 'connectorsAdd execute ' . $dbObj->error;	
					}
				} else {
					// could not bind parameters
					$error = 'str_DatabaseError';
					$errorParam = 'connectorsAdd bind ' . $dbObj->error;
				}

				if ($stmt) {
					$stmt->free_result();
					$stmt->close();
				}
			} else {
				// could not prepare statement
				$error = 'str_DatabaseError';
				$errorParam = 'connectorsAdd prepare ' . $dbObj->error;
			}
		} else {
			// could not open database connection
			$error = 'str_DatabaseError';
			$errorParam = 'connectorsAdd connect ' . $dbObj->error;
		}

		$resultArray['error'] = $error;
		$resultArray['errorparam'] = $errorParam;
		return $resultArray;
	}

	static function connectorsDelete($pConnectorData)
	{
		$resultArray = UtilsObj::getReturnArray();
		$error = '';
		$errorParam = '';
		$id = $pConnectorData['id'];

		$matches = [];
		preg_match('/(.*).myshopify.com/', $pConnectorData['shopurl'], $matches);
		$shopURL = $matches[1];

		if ($id > 0) {
			$dbObj = DatabaseObj::getGlobalDBConnection();
			if ($dbObj) {

				$stmt = $dbObj->prepare('DELETE FROM `CONNECTORS` WHERE `id` = ?');

				if ($stmt) {
					if ($stmt->bind_param('i', $id)) {
						if ($stmt->execute()) {
						} else {
							$error = 'str_DatabaseError';
							$errorParam = 'connectorsDelete execute ' . $dbObj->error;
						}
					} else {
						// could not bind parameters
						$error = 'str_DatabaseError';
						$errorParam = 'connectorsDelete bind ' . $dbObj->error;
					}
					if ($stmt) {
						$stmt->free_result();
						$stmt->close();
					}
				} else {
					// could not prepare statement
					$error = 'str_DatabaseError';
					$errorParam = 'connectorsDelete prepare ' . $dbObj->error;
				}

				$stmt = $dbObj->prepare('DELETE FROM `CONNECTORSPRODUCTCOLLECTIONLINK` WHERE `connectorurl` = ?');

				if ($stmt) {
					if ($stmt->bind_param('s', $shopURL)) {
						if ($stmt->execute()) {
						} else {
							$error = 'str_DatabaseError';
							$errorParam = 'connectorsDelete collectionlink execute ' . $dbObj->error;
						}
					} else {
						// could not bind parameters
						$error = 'str_DatabaseError';
						$errorParam = 'connectorsDelete collectionlink bind ' . $dbObj->error;
					}
					if ($stmt) {
						$stmt->free_result();
						$stmt->close();
					}
				} else {
					// could not prepare statement
					$error = 'str_DatabaseError';
					$errorParam = 'connectorsDelete collectionlink prepare ' . $dbObj->error;
				}
				$dbObj->close();
			} else {
				// could not open database connection
				$error = 'str_DatabaseError';
				$errorParam = 'connectorsDelete connect ' . $dbObj->error;
			}
		}

		$resultArray['error'] = $error;
		$resultArray['errorparam'] = $errorParam;
		return $resultArray;
	}

	static function getConnector($pConnectorID)
	{
		// return the connector data for the specified brand record id
		global $gSession;
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
		$connectorArray['connectorid'] = $recordID;
		$connectorArray['connectorurl'] = $connectorURL;
		$connectorArray['connectorkey'] = $connectorKey;
		$connectorArray['connectorsecret'] = $connectorSecret;
		$connectorArray['connectorinstallurl'] = $connectorInstallURL;
		$connectorArray['connectorname'] = $connectorName;
		$connectorArray['brandcode'] = $brandCode;
		$connectorArray['licensekeycode'] = $licenseKeyCode;
		$connectorArray['connectorprimarydomain'] = $connectorPrimaryDomain;
		$connectorArray['pricesincludetax'] = $pricesIncludeTax;

		// If we are populating an existing connector window run the query.
		if ($pConnectorID > 0) {
			$dbObj = DatabaseObj::getGlobalDBConnection();

			if ($dbObj) {
				$stmt = $dbObj->prepare('SELECT 
											`id`, `connectorurl`, `connectorprimarydomain`, `connectorkey`, `connectorsecret`, `connectorinstallurl`, 
											`connectorname`, `brandcode`, `licensekeycode`, `pricesincludetax`
										FROM
											`CONNECTORS`
										WHERE
											`id` = ?');

				if ($stmt) {
					if ($stmt->bind_param('i', $pConnectorID)) {
						if ($stmt->execute()) {
							if ($stmt->store_result()) {
								if ($stmt->num_rows > 0) {
									if ($stmt->bind_result(
										$recordID, $connectorURL, $connectorPrimaryDomain, $connectorKey, $connectorSecret, $connectorInstallURL,
										$connectorName, $brandCode, $licenseKeyCode, $pricesIncludeTax
									)) {
										while ($stmt->fetch()) {
											$connectorArray['connectorid'] = $recordID;
											$connectorArray['connectorurl'] = UtilsObj::ExtJSEscape($connectorURL);
											$connectorArray['connectorprimarydomain'] = UtilsObj::ExtJSEscape($connectorPrimaryDomain);
											$connectorArray['connectorkey'] = UtilsObj::ExtJSEscape($connectorKey);
											$connectorArray['connectorsecret'] = UtilsObj::ExtJSEscape(UtilsObj::decryptData($connectorSecret, $gSession['licensekeydata']['systemkey'], true));
											$connectorArray['connectorinstallurl'] = UtilsObj::ExtJSEscape($connectorInstallURL);
											$connectorArray['connectorname'] = UtilsObj::ExtJSEscape($connectorName);
											$connectorArray['brandcode'] = UtilsObj::ExtJSEscape($brandCode);
											$connectorArray['licensekeycode'] = UtilsObj::ExtJSEscape($licenseKeyCode);
											$connectorArray['pricesincludetax'] = $pricesIncludeTax;
										}
									} else {
										$returnArray['error'] = __FUNCTION__ . ' bind result error: ' . $dbObj->error;
									}
								}
							} else {
								$returnArray['error'] = __FUNCTION__ . ' store result error: ' . $dbObj->error;
							}
						} else {
							$returnArray['error'] = __FUNCTION__ . ' execute error: ' . $dbObj->error;
						}
					} else {
						$returnArray['error'] = __FUNCTION__ . ' bind param error: ' . $dbObj->error;
					}

					$stmt->free_result();
					$stmt->close();
					$stmt = null;
				}

				$dbObj->close();
			} else {
				$returnArray['error'] = __FUNCTION__ . ' Unable to connect to the database: ' . $dbObj->error;
			}
		}

		$resultArray['data'] = $connectorArray;
		return $resultArray;
	}

	public static function getConnectorStatus($pConnectorURL, $pConnectorKey, $pConnectorSecret, $pConnectorInstallURL, $pConnectorAccessToken) {
		$connectorStatus = TPX_CONNECTOR_READY;

		if ($pConnectorURL == '' || $pConnectorKey == '' || $pConnectorSecret == '') {
			$connectorStatus = TPX_CONNECTOR_NOTCONFIGURED;
		}
		else if ($pConnectorInstallURL == '') {
			$connectorStatus = TPX_CONNECTOR_PENDING;
		}
		else if ($pConnectorAccessToken != '') {
			$connectorStatus = TPX_CONNECTOR_CONNECTED;
		}

		return $connectorStatus;
	}

	public static function connectorsRebuildTheme()
	{
		$shopURL = UtilsObj::getPOSTParam('shopurl');
		$shopifyConnector = new ShopifyConnector($shopURL);

		// Initialise the Shopify SDK so we can make requests.
		$shopifyConnector->initShopifySDK();

		// Apply theme changes, e.g. push snippet, inject code.
		$shopifyConnector->applyThemeChanges(false);

		return $shopifyConnector->getThemeErrors();
	}

	public static function connectorsInstallTaopixTheme($pShopURL)
	{
		$shopifyConnector = new ShopifyConnector($pShopURL);
		$shopifyConnector->initShopifySDK();
		$shopifyConnector->pushTaopixTheme();
		
		return $shopifyConnector->getThemeErrors();
	}

	static function syncProductsEdit()
	{
		$resultArray = UtilsObj::getReturnArray();
		$error = '';
		$errorParam = '';

		$productStatusActive = 0;
		$productRetainNames = false;

		$dbObj = DatabaseObj::getGlobalDBConnection();
		$id = UtilsObj::getPOSTParam('id');

		if ($id > 0) {
			$productStatusActive = UtilsObj::getPOSTParam('productsactive');
			$productRetainNames = UtilsObj::getPOSTParam('retainnames', 0) == 1;

			if ($dbObj) {
				$stmt = $dbObj->prepare('UPDATE 
											`CONNECTORS` 
										SET 
											`productsactive` = ?
                                        WHERE `id` = ?');

				if ($stmt) {
					if ($stmt->bind_param(
						'ii',
						$productStatusActive, $id
					)) {
						if (!$stmt->execute()) {
							$error = 'str_DatabaseError';
							$errorParam = 'connectorsEdit execute ' . $dbObj->error;
						}
					} else {
						// could not bind parameters
						$error = 'str_DatabaseError';
						$errorParam = 'connectorsEdit bind ' . $dbObj->error;
					}
					if ($stmt) {
						$stmt->free_result();
						$stmt->close();
					}
				} else {
					// could not prepare statement
					$error = 'str_DatabaseError';
					$errorParam = 'connectorsEdit prepare ' . $dbObj->error;
				}
			} else {
				// could not open database connection
				$error = 'str_DatabaseError';
				$errorParam = 'connectorsEdit connect ' . $dbObj->error;
			}
		}

		self::createSyncEvent($id, $productRetainNames);

		$resultArray['error'] = $error;
		$resultArray['errorparam'] = $errorParam;
		return $resultArray;
	}

	static function createSyncEvent($pConnectorID, $productRetainNames)
	{
		$connectorDetails = self::getConnector($pConnectorID);
		$brandCode = $connectorDetails['data']['brandcode'];
		$shopURL = $connectorDetails['data']['connectorurl'] . '.myshopify.com';
		$licenseKeyCode = $connectorDetails['data']['licensekeycode'];

		$shopifyConnector = new ShopifyConnector($shopURL);
		$graphQL = $shopifyConnector->initGraphQL();
		$product = new Product($graphQL);

		$productsToSync = $product->getTaopixProductsToSync($shopifyConnector->getVendorNameFromShopURL(), $productRetainNames);

		$insertBulkCount = $productsToSync['newProducts']->count();
		$updateBulkCount = $productsToSync['updateProducts']->count();

		if ($insertBulkCount > 0) {
			$product->syncProducts($productsToSync['newProducts'], 'INSERT', $brandCode);
		}

		if ($updateBulkCount > 0) {
			$product->syncProducts($productsToSync['updateProducts'], 'UPDATE', $brandCode);
		}

		if ($insertBulkCount > 0 || $updateBulkCount > 0) {
			$param1 = '';
			$param2 = '';
			$param3 = $shopURL;

			DatabaseObj::createEvent(
				'TAOPIX_CONNECTORPRODUCTSYNC',
				'',
				$licenseKeyCode,
				$brandCode,
				'',
				0,
				$param1,
				$param2,
				$param3,
				'',
				'',
				'',
				'',
				'',
				0,
				0,
				0,
				'',
				'',
				0
			);
		}
	}

	static function syncProductsEditDisplay($pConnectorID)
	{
		global $ac_config;

		$resultArray = self::getConnectorSyncSettings($pConnectorID);
		$brandCode = $resultArray['data']['connector']['brandcode'];
		$shopURL = $resultArray['data']['connector']['connectorurl'] . '.myshopify.com';

		$shopifyConnector = new ShopifyConnector($shopURL);
		$graphQL = $shopifyConnector->initGraphQL();
		$product = new Product($graphQL);

		$productsToSync = $product->getCountTaopixProductsToSync($shopifyConnector->getVendorNameFromShopURL());

		$newCount = $productsToSync['newProductsCount'];
		$updateCount = $productsToSync['updateProductsCount'];

		$resultArray['data']['connector']['shopurl'] = $shopURL;
		$resultArray['data']['connector']['newcount'] = $newCount;
		$resultArray['data']['connector']['updatecount'] = $updateCount;

		//CHECK IF FILES ALREADY EXISTS
		//MEANING SYNC IN PROGRESS

		$filePath = $ac_config['CONNECTORRESOURCESPATH'];
		$filePath = UtilsObj::correctPath($filePath, DIRECTORY_SEPARATOR, true);
		$filePath .= $brandCode . DIRECTORY_SEPARATOR;

		$newName = $filePath . 'BulkUploadCreate.jsonl';
		$updateName = $filePath . 'BulkUploadUpdate.jsonl';

		$inProgress = 'false';

		if (file_exists($newName) || file_exists($updateName)) {
			$inProgress = 'true';
		}
		$resultArray['data']['connector']['inprogress'] = $inProgress;

		return $resultArray;
	}

	static function getConnectorSyncSettings($pConnectorID)
	{
		// return the connector data for the specified brand record id
		$resultArray = UtilsObj::getReturnArray();
		$resultArray['data']['connector'] = [];
		$recordID = 0;
		$productStatusActive = 0;
		$licenseKeyCode = '';
		$brandCode = '';
		$connectorURL = '';
		$pricesIncludeTax = 0;

		// If we are populating an existing connector window run the query.
		if ($pConnectorID > 0) {
			$dbObj = DatabaseObj::getGlobalDBConnection();

			if ($dbObj) {
				$stmt = $dbObj->prepare('SELECT 
											`id`, `brandcode`, `licensekeycode`, `productsactive`, `connectorurl`, `pricesincludetax`
										FROM
											`CONNECTORS`
										WHERE
											(
												`id` = ? 
											)');

				if ($stmt) {
					if ($stmt->bind_param('i', $pConnectorID)) {
						if ($stmt->execute()) {
							if ($stmt->store_result()) {
								if ($stmt->num_rows > 0) {
									if ($stmt->bind_result(
										$recordID, $brandCode, $licenseKeyCode, $productStatusActive, $connectorURL, $pricesIncludeTax
									)) {
										while ($stmt->fetch()) {
											$connectorArray = [];
											$connectorArray['connectorid'] = $recordID;
											$connectorArray['brandcode'] = UtilsObj::ExtJSEscape($brandCode);
											$connectorArray['licensekeycode'] = UtilsObj::ExtJSEscape($licenseKeyCode);
											$connectorArray['productsactive'] = $productStatusActive;
											$connectorArray['connectorurl'] = UtilsObj::ExtJSEscape($connectorURL);
											$connectorArray['pricesincludetax'] = $pricesIncludeTax;
										}
									} else {
										$returnArray['error'] = __FUNCTION__ . ' bind result error: ' . $dbObj->error;
									}
								}
							} else {
								$returnArray['error'] = __FUNCTION__ . ' store result error: ' . $dbObj->error;
							}
						} else {
							$returnArray['error'] = __FUNCTION__ . ' execute error: ' . $dbObj->error;
						}
					} else {
						$returnArray['error'] = __FUNCTION__ . ' bind param error: ' . $dbObj->error;
					}

					$stmt->free_result();
					$stmt->close();
					$stmt = null;
				}

				$dbObj->close();
			} else {
				$returnArray['error'] = __FUNCTION__ . ' Unable to connect to the database: ' . $dbObj->error;
			}
		}

		$returnArray['data']['connector'] = $connectorArray;
		return $returnArray;
	}

	static function getLicenseKeyAndBrandList()
	{
		$groupListArray = array();
		$id = 0;
		$groupCode = '';
		$name = '';
		$webBrandCode = '';

		$sql = 'SELECT `id`, `groupcode`, `name`, `webbrandcode` FROM `LICENSEKEYS`';

		$dbObj = DatabaseObj::getGlobalDBConnection();
		if ($dbObj) {
			$stmt = $dbObj->prepare($sql);
			$bindOK = true;

			if ($stmt) {
				if ($bindOK); {
					if ($stmt->bind_result($id, $groupCode, $name, $webBrandCode)) {
						if ($stmt->execute()) {
							while ($stmt->fetch()) {
								$groupListItem['id'] = $groupCode;
								$groupListItem['name'] = $groupCode . ' - ' . $name;
								$groupListItem['webBrandCode'] = $webBrandCode;
								array_push($groupListArray, $groupListItem);
							}
						}
					}
					$stmt->free_result();
					$stmt->close();
				}
			}
			$dbObj->close();
		}

		return $groupListArray;
	}
}
