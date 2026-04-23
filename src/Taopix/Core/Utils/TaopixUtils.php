<?php

namespace Taopix\Core\Utils;

use Exception;
use ZipArchive;

require_once __DIR__ . '/../../../../libs/internal/curl/Curl.php';
require_once __DIR__ . '/../../../../Utils/UtilsConstants.php';
require_once __DIR__ . '/../../../../Utils/UtilsDatabase.php';
require_once __DIR__ . '/../../../../Utils/Utils.php';
require_once __DIR__ . '/../../../../Utils/UtilsLocalization.php';
require_once __DIR__ . '/../../../../Utils/UtilsAuthenticate.php';
require_once __DIR__ . '/../../../../OnlineAPI/OnlineAPI_model.php';
require_once __DIR__ . '/../../../../Utils/UtilsRoute.php';
require_once __DIR__ . '/../../../../Utils/UtilsSmarty.php';
require_once __DIR__ . '/../../../../AppAPI/AppAPI_model.php';
require_once __DIR__ . '/../../../../AppProductionAPI/AppProductionAPI_model.php';


class TaopixUtils
{
	/**
	 * Gets the value of a key from an array.
	 *
	 * @param array $pParamArray Array to read.
	 * @param string $pKey Key to get value for.
	 * @param string $pDefaultValue Optional. Default value if key does not exist.
	 */
	public function getArrayParam(array $pParamArray, string $pKey, $pDefaultValue = '')
	{
		return \UtilsObj::getArrayParam($pParamArray, $pKey, $pDefaultValue);
	}

	/**
	 * Open and return a connection to the database.
	 */
	public function getGlobalDBConnection()
	{
		global $ac_config;
		$ac_config = $this->getACConfig();
		if (!array_key_exists('ALLOWSELFSIGNEDSSLCERTIFICATES', $ac_config)) {
			$ac_config['ALLOWSELFSIGNEDSSLCERTIFICATES'] = 0;
		}

		// if the ALLOWSELFSIGNEDSSLCERTIFICATES is set to 0 then we must set CURLOPT_SSL_VERIFYPEER to true.
		$ac_config['SSLVERIFYPEER'] = ($ac_config['ALLOWSELFSIGNEDSSLCERTIFICATES'] == 0);

		return \DatabaseObj::getGlobalDBConnection();
	}

	/**
	 * Get the available status of a desktop project thumbnail from the project ref
	 *
	 * @param string projectref to check
	 * @return array error array with the value stored in the available key
	 */
	public function getDesktopProjectThumbnailAvailabilityFromProjectRef(string $pProjectRef)
	{
		return \DatabaseObj::getDesktopProjectThumbnailAvailabilityFromProjectRef($pProjectRef);
	}

	/**
	 * Gets the web URL for the project thumbnail of the passed desktop project ref
	 *
	 * @param string $pProjectRef The project ref of the project
	 * @return string The URL path to the thumbnail
	 */
	public function buildDesktopProjectThumbnailWebURL(string $pProjectRef)
	{
		return \UtilsObj::buildDesktopProjectThumbnailWebURL($pProjectRef);
	}

	/*
     * Function bind parameters dynamically
     * @params: dbconnection Object, datatype String, valueArray Array
     * @return: true/false
     *
     */
	public function bindParams($pStmt, $pDataTypes, $pValuesArray)
	{
		return \DatabaseObj::bindParams($pStmt, $pDataTypes, $pValuesArray);
	}

	/**
	 * Bind result dynamically.
	 * @param mysqli_stmt $pStmt Current query statement object.
	 * @param array $pColumnList Array of column names.
	 * @param string pKeyName
	 * @return string Array containing each result row
	 */
	public function bindResult($pStmt, array $pColumnList, string $pKeyName): array
	{
		$params = [];
		$data = [];
		$result = [];

		foreach ($pColumnList as $col_name) {
			$params[$col_name] = &$data[$col_name];
		}

		if (call_user_func_array(array($pStmt, 'bind_result'), array_values($params))) {
			$copy = function ($a) {
				return $a;
			};

			$result = array();
			while ($pStmt->fetch()) {
				$mappedArray = array_map($copy, $params);

				if ($pKeyName !== '') {
					$result[($pKeyName !== '') ? $mappedArray[$pKeyName] : ''] =  $mappedArray;
				} else {
					$result = $mappedArray;
				}
			}
		}

		return $result;
	}

	/**
	 * Attempts to determine the type of the provided value and return the MySQL
	 * bind type value.
	 *
	 * @param mixed $pValue Value to get type for.
	 * @return string MySQL bind type.
	 */
	public function getBindType($pValue): string
	{
		$type = 's';

		switch (gettype($pValue)) {
			case 'string': {
					$type = 's';
					break;
				}
			case 'integer':
			case 'boolean': {
					$type = 'i';
					break;
				}
		}

		return $type;
	}

	/**
	 * return the correct language string
	 *
	 * @param pLocalizedString full string to read
	 * @param pLanguage language to extract
	 * @param pUseFirstAvailable bool
	 * @return string the language string
	 */
	public function getLocaleString($pLocalizedString, $pLanguage, $pUseFirstAvailable = false)
	{
		return \LocalizationObj::getLocaleString($pLocalizedString, $pLanguage, $pUseFirstAvailable);
	}

	/**
	 * correct the supplied path making sure it either has or has not got a trailing separator
	 *
	 * @param pSourcePath path to correct
	 * @param pSeparator directory seperator
	 * @param pTrailing bool with a trailing slash be included in the return value
	 * @return string the corrected path
	 */
	public function correctPath($pSourcePath, $pSeparator = "/", $pTrailing = true)
	{
		return \UtilsObj::correctPath($pSourcePath, $pSeparator, $pTrailing);
	}

	/**
	 * Write to file
	 *
	 * @param pTextFilePath file to write
	 * @param pText data to write
	 * @return bool success
	 */
	public function writeTextFile($pTextFilePath, $pText)
	{
		return \UtilsObj::writeTextFile($pTextFilePath, $pText);
	}

	/**
	 * Deletes file
	 *
	 * @param pPath file to delete
	 * @return bool success
	 */
	public function deleteFile($pPath)
	{
		return \UtilsObj::deleteFile($pPath);
	}

	/**
	 * Return contens of file
	 *
	 * @param pTextFilePath file to read
	 * @return string file contents
	 */
	public function readTextFile($pTextFilePath)
	{
		return \UtilsObj::readTextFile($pTextFilePath);
	}

	/**
	 * Returns a system constants ac.
	 *
	 * @return array System constants array.
	 */
	public function getConstants(): array
	{
		return \DatabaseObj::getConstants();
	}

	/**
	 * Returns a user account from the account code.
	 *
	 * @param string $pAccountCode Account code to lookup an user with.
	 * @return array User account array.
	 */
	public function getUserAccountFromAccountCode(string $pAccountCode): array
	{
		return \DatabaseObj::getUserAccountFromAccountCode($pAccountCode);
	}

	/**
	 * Returns a user account from brand and login.
	 *
	 * @param string $pBrandCode Brand code to match with user account.
	 * @param string $pLogin Login account to lookup.
	 * @return array User account array.
	 */
	public function getUserAccountFromBrandAndLogin(string $pBrandCode, string $pLogin): array
	{
		return \DatabaseObj::getUserAccountFromBrandAndLogin($pBrandCode, $pLogin);
	}

	/**
	 * Returns a user account from brand and login.
	 *
	 * @param string $pGroupCode License key code to match with user account.
	 */
	public function createEmptyUserAccount(string $pGroupCode): array
	{
		$licenseKeyDataArray = \DatabaseObj::getLicenseKeyFromCode($pGroupCode);

		$brandCode = $licenseKeyDataArray['webbrandcode'];

		$brandingArray = \DatabaseObj::getBrandingFromCode($brandCode);

		return \AuthenticateObj::createEmptyUserAccount($licenseKeyDataArray, $pGroupCode, $brandCode, $brandingArray);
	}

	/**
	 * Returns a user account from brand and login.
	 *
	 * @param string $pGroupCode License key code to match with user account.
	 */
	public function updateOrInsertExternalAccount(
		int $pUserAccountID,
		array $pUserAccount,
		string $pBrandCode,
		string $pGroupcode,
		string $pCompanyCode
	): array {

		return \AuthenticateObj::updateOrInsertExternalAccount(
			$pUserAccountID,
			$pUserAccount,
			true,
			-1,
			'',
			'',
			$pBrandCode,
			$pGroupcode,
			$pCompanyCode,
			false,
			false,
			false,
			false
		);
	}

	/**
	 * Returns a best matched production site array
	 *
	 * @param array $pOrderData order data retireved from the call to getProjectOrderData.
	 */
	public function routeOrder(array $pOrderData): array
	{
		$orderData = $pOrderData;

		foreach ($orderData['cartarray'] as &$lineItem) {
			$siteArray = \RoutingObj::routeOrder(
				$orderData['headerarray']['webbrandcode'],
				$pOrderData['headerarray']['groupcode'],
				$orderData['headerarray']['userid'],
				$lineItem['productcode'],
				'',
				'',
				$orderData['shippingdata']['shippingcustomercountrycode'],
				$orderData['shippingdata']['shippingcustomeraddress1'],
				$orderData['shippingdata']['shippingcustomeraddress2'],
				$orderData['shippingdata']['shippingcustomeraddress3'],
				$orderData['shippingdata']['shippingcustomeraddress4'],
				$orderData['shippingdata']['shippingcustomercity'],
				$orderData['shippingdata']['shippingcustomerregioncode'],
				$orderData['shippingdata']['shippingcustomerpostcode'],
				''
			);

			$lineItem['sitecode'] = $siteArray['routesitecode'];
		}

		return $orderData;
	}

	/**
	 * read a config file and return it as an exploded array
	 *
	 * @param pConfigFilePath path to config file.
	 * @return array config in array
	 */
	public function readConfigFile($pConfigFilePath): array
	{
		return \UtilsObj::readConfigFile($pConfigFilePath);
	}

	/**
	 * Create all folders in path
	 *
	 * @param pSource path to create.
	 * @return bool success
	 */
	public function createAllFolders($pSource): bool
	{
		return \UtilsObj::createAllFolders($pSource);
	}

	/**
	 * Returns the path for the folder containing the product collection resources for a specific version date
	 *
	 * @param string $pCollectionCode The collection code for the resources
	 * @param string $pCollectionVersionDate The version date for the resources
	 * @return string The path to the folder containing the resources
	 */
	public function getProductCollectionResourceFolderPath($pCollectionCode, $pCollectionVersionDate): string
	{
		return \UtilsObj::getProductCollectionResourceFolderPath($pCollectionCode, $pCollectionVersionDate);
	}

	/*
     * Return a file extension compare to the image type.
     *
     * @param $pImageMimeType File mime type.
     * @return File extension.
     */
	public function getExtensionFromImageType($pImageMimeType): string
	{
		return \UtilsObj::getExtensionFromImageType($pImageMimeType);
	}

	/*
     * Return a file mime_type
     *
     * @param $pFile File to extract mime type from .
     * @return File mimetype .
     */
	public function getMimeTypeFromFile(string $pFile): string
	{
		// read mime type
		$finfo = finfo_open(FILEINFO_MIME_TYPE);
		$ftype = finfo_buffer($finfo, file_get_contents($pFile));
		finfo_close($finfo);

		return $ftype;
	}


	/*
     * return an array containing the currency record for the specified currency code
     *
     * @param $pCurrencyCode
     * @return array currency array .
     */
	public function getCurrency($pCurrencyCode): array
	{
		return \DatabaseObj::getCurrency($pCurrencyCode);
	}

	/**
	 * Creates a new Smarty instance.
	 *
	 * @param string $pSection Which section to use.
	 * @param string $pWebBrandCode Brandcode to use.
	 * @param string $pWebAppName Web app name.
	 * @param string $pLocale Language code of which language to use.
	 */
	public function newSmartyObj(string $pSection, string $pWebBrandCode = '', string $pWebAppName = '', string $pLocale = '')
	{
		return \SmartyObj::newSmarty($pSection, $pWebBrandCode, $pWebAppName, $pLocale, false);
	}

	/**
	 * Authorise redaction on a customer.
	 *
	 * @param array $pUserID Taopix user ID to redact.
	 * @param int $pAuthorised Authorised flag.
	 * @return array Result array.
	 */
	public function authoriseRedaction2(array $pUserID, int $pAuthorised)
	{
		require_once __DIR__ . '/../../../../DataRedactionAPI/DataRedactionAPI_model.php';
		return \DataRedactionAPI_model::authoriseRedaction2($pUserID, $pAuthorised);
	}

	/**
	 * Returns the path for the folder containing the CONNECTOR collection resources for a specific version date
	 *
	 * @param string $pCollectionCode The collection code for the resources
	 * @param string $pCollectionVersionDate The version date for the resources
	 * @global array $ac_config Config settings array
	 * @return string The path to the folder containing the resources
	 */
	public function getConnectorResourceFolderPath($pCollectionCode, $pCollectionVersionDate): string
	{
		global $ac_config;
		$ac_config = $this->getACConfig();

		$datePath = date("YmdHis", strtotime($pCollectionVersionDate));
		$collectionResourcePath = \UtilsObj::correctPath($ac_config['CONNECTORRESOURCESPATH'], DIRECTORY_SEPARATOR, true) . 'resources' . DIRECTORY_SEPARATOR . $pCollectionCode . DIRECTORY_SEPARATOR . $datePath;

		return $collectionResourcePath;
	}

	/**
	 * Returns an array containing the exploded price data.
	 *
	 * @param integer $pPricingModel
	 * @param string $pPriceString
	 *
	 * @return array
	 */
	public function priceStringToArray($pPricingModel, $pPriceString)
	{
		return \DatabaseObj::priceStringToArray($pPricingModel, $pPriceString);
	}

	/**
	 * Generates the auto-update data for TAOPIX™ Designer or the system data for TAOPIX™ Builder
	 *
	 * @static
	 *
	 * @return array
	 *   the result array will contain the data to be echo'd back to the calling application
	 *
	 */
	public function systemUpdateProcess(
		$pOwnerCode,
		$pOwnerCode2,
		$pProductCollectionCode,
		$pGroupCode,
		$pWebBrandCode,
		$pAppVersion,
		$pOSVersion,
		$pCPUType,
		$pAPIVersion,
		$pLanguageCode,
		$pCurrentProductCategoryVersion,
		$pCurrentCalendarDataVersion,
		$pUploadRefData,
		$pPreviousProductAutoUpdateCacheVersion,
		$pPreviousMasksAutoUpdateCacheVersion,
		$pPreviousBackgroundsAutoUpdateCacheVersion,
		$pPreviousScrapbookAutoUpdateCacheVersion,
		$pPreviousFramesAutoUpdateCacheVersion,
		$pAppPlatform
	) {
		return \AppAPI_model::systemUpdateProcess(
			$pOwnerCode,
			$pOwnerCode2,
			$pProductCollectionCode,
			$pGroupCode,
			$pWebBrandCode,
			$pAppVersion,
			$pOSVersion,
			$pCPUType,
			$pAPIVersion,
			$pLanguageCode,
			$pCurrentProductCategoryVersion,
			$pCurrentCalendarDataVersion,
			$pUploadRefData,
			$pPreviousProductAutoUpdateCacheVersion,
			$pPreviousMasksAutoUpdateCacheVersion,
			$pPreviousBackgroundsAutoUpdateCacheVersion,
			$pPreviousScrapbookAutoUpdateCacheVersion,
			$pPreviousFramesAutoUpdateCacheVersion,
			$pAppPlatform
		);
	}

	/**
	 * Correct the price if tax needs to be removed.
	 *
	 * @param float $price The price to correct.
	 * @param boolean $pRemoveTax True to remove tax.
	 * @param float $taxRate The tax rate.
	 * @return float The corrected price.
	 */
	private function correctPrice($pPrice, $pRemoveTax, $pTaxRate)
	{
		$price = $pPrice;

		if (($pRemoveTax == true) && ($pTaxRate > 0.00)) {
			$price = ($price / ($pTaxRate + 100)) * 100;
		}

		return $price;
	}

	/**
	 * Process component price.
	 *
	 * @param array $components Array of components.
	 * @param array $subComponents Array of subcomponent
	 * @param array $qtyInfo Quantity info.
	 * @param int $licenseKeyTaxRate Tax rate from license key.
	 * @param boolean $removeTax True to remove tax.
	 * @return float The component price.
	 */
	private function processComponentPrice($components, $subComponents, $qtyInfo, $licenseKeyTaxRate, &$removeTax)
	{
		$lowest = -1;
		$lowestCompTaxRate = -1;

		// No components so we need to add nothing.
		if (empty($components)) {
			return 0.00;
		}

		foreach ($components as $compKey => $compDetails) {
			if (isset($compDetails['price'])) {
				if ('' === $compDetails['price']) {
					continue;
				}
			}

			$componentPrice = $this->priceStringToArray($compDetails['pricingmodel'], $compDetails['pricedata']);

			$qty = in_array($compDetails['pricingmodel'], [TPX_PRICINGMODEL_PERSIDEQTY, TPX_PRICINGMODEL_PERSIDEPERPRODPERCMPQTY]) ? $qtyInfo['pages'] : $qtyInfo['product'];
			$selectedComponent = array_filter($componentPrice, function ($price) use ($qtyInfo, $compDetails) {
				switch ($compDetails['pricingmodel']) {
					case TPX_PRICINGMODEL_PERQTY:
						return $qtyInfo['product'] >= $price['startqty'] && $qtyInfo['product'] <= $price['endqty'];
						break;

					case TPX_PRICINGMODEL_PERSIDEQTY:
						return $qtyInfo['pages'] >= $price['startpagecount'] && $qtyInfo['pages'] <= $price['endpagecount'];
						break;

					case TPX_PRICINGMODEL_PERPRODCMPQTY:
						return $qtyInfo['product'] >= $price['startcmpqty'] && $qtyInfo['product'] <= $price['startcmpqty'];
						break;

					case TPX_PRICINGMODEL_PERSIDEPERPRODPERCMPQTY:
						return $qtyInfo['pages'] >= $price['startcmpqty'] && $qtyInfo['pages'] <= $price['endcmpqty'];
						break;

					default:
						return false;
				}
			});

			if (empty($selectedComponent)) {
				$selectedComponent = [$componentPrice[0]];
				switch ($compDetails['pricingmodel']) {
					case TPX_PRICINGMODEL_PERQTY:
						$qty = $componentPrice[0]['startqty'];
						break;
					case TPX_PRICINGMODEL_PERSIDEQTY:
						$qty = $componentPrice[0]['startpagecount'];
						break;
					case TPX_PRICINGMODEL_PERPRODCMPQTY:
					case TPX_PRICINGMODEL_PERSIDEPERPRODPERCMPQTY:
						$qty = $componentPrice[0]['startcmpqty'];
						break;
				}
			}

			if ($lowest > ($selectedComponent[0]['unitsell'] * $qty) || -1 === $lowest) {
				$lowest = ($selectedComponent[0]['unitsell'] * $qty);
				$lowestCompTaxRate = $compDetails['pricetaxrate'];
			}

			if (!empty($subComponents)) {
				// Make sure the updated qty is passed down to the sub components.
				$qtyKey = in_array($compDetails['pricingmodel'], [TPX_PRICINGMODEL_PERSIDEQTY, TPX_PRICINGMODEL_PERSIDEPERPRODPERCMPQTY]) ? 'pages' : 'product';
				$qtyInfo[$qtyKey] = $qty;

				if (isset($subComponents[$compDetails['localcode']]))
				{
					$lowest += $this->processComponentPrice($subComponents[$compDetails['localcode']], [], $qtyInfo, $licenseKeyTaxRate, $removeTax);

				}
			}
		}

		if ($lowestCompTaxRate != $licenseKeyTaxRate) {
			//if the tax rates on the component and license key are different then we will supply the nett component price
			$removeTax = true;
		}

		//correct prices for tax
		$lowest = $this->correctPrice($lowest, $removeTax, $lowestCompTaxRate);

		return max(0, $lowest);
	}

	/**
	 * generate the price cache data.
	 *
	 * @param DatabaseObj $db Database instance.
	 * @param string $groupCode Group code.
	 * @param string $brandCode brand code.
	 */
	private function generateCacheData($db, $groupCode, $brandCode)
	{
		$collectionCode = '';
		$collectionCodes = [];
		$query = 'SELECT DISTINCT(collectioncode) FROM productcollectionlink';

		$stmt  = $db->prepare($query);

		if (false === $stmt) {
			throw new Exception(__FUNCTION__ . ' prepare: ' . $db->error);
		}

		if (!$stmt->bind_result($collectionCode)) {
			throw new Exception(__FUNCTION__ . ' Bind result: ' . $db->error);
		}

		if (!$stmt->execute()) {
			throw new Exception(__FUNCTION__ . ' Execute: ' . $db->error);
		}

		while ($stmt->fetch()) {
			$collectionCodes[] = $collectionCode;
		}

		// Generate the cache data.
		array_map(function ($item) use ($groupCode, $brandCode) {
			$this->systemUpdateProcess(
				'',
				'',
				$item,
				$groupCode,
				$brandCode,
				'',
				'',
				'',
				'1',
				'en',
				'',
				'',
				'',
				'',
				'',
				'',
				'',
				'',
				''
			);
		}, $collectionCodes);
	}

	/**
	 * Generates from price data to sync with shopify
	 *
	 * @return array
	 *   the result array will contain the from price for each layout code
	 */
	public function populateFromPricesArray(string $groupCode, $db, bool $pPricesIncludeTax): array
	{
		global $showPricesWithTax;
		global $removeTax;

		$showPricesWithTax = false;
		$removeTax = false;

		try {
			$productData = [];
			$cacheKey = '';
			$cacheResult = '';
			$serializedDataLength = 0;

			// Generate any cache data.
			$this->generateCacheData($db, $groupCode, '');

			$query = 	'	SELECT `c`.`datacachekey`, `c`.`cachedata`, `l`.`showpriceswithtax`, `c`.`serializeddatalength`
							FROM `cachedata` `c`
							INNER JOIN `licensekeys` AS `l` ON `l`.`groupcode` = `c`.`groupcode`
							WHERE `c`.`groupcode` = ?
						';

			$stmt = $db->prepare($query);

			if (false === $stmt) {
				throw new Exception('Prepare: ' . $db->error);
			}

			if (!$stmt->bind_param('s', $groupCode)) {
				throw new Exception('Bind param: ' . $db->error);
			}

			if (!$stmt->bind_result($cacheKey, $cacheResult, $showPricesWithTax, $serializedDataLength)) {
				throw new Exception('Bind result: ' . $db->error);
			}

			if (!$stmt->execute()) {
				throw new Exception('Execute: ' . $db->error);
			}
			$priceMap = [];
			while ($stmt->fetch()) {

				if ($serializedDataLength > 49152)
				{
					$cacheResult = gzuncompress($cacheResult, $serializedDataLength);
				}
				$cachedData = unserialize($cacheResult);

				foreach ($cachedData['productlist'] as $key => $productData) {
					// No price data continue on.
					if ('' === $productData['price']) {
						continue;
					}

					$qtyInfo = [
						'product' => 1,
						'pages' => $productData['productminpagecount'],
					];

					if ($productData['pricetaxrate'] != $productData['taxrate']) {
						$removeTax = true;
					}

					$productPriceData = $this->priceStringToArray($productData['pricingmodel'], $productData['price']);
					$selectedProductPrice = array_filter($productPriceData, function ($price) {
						return 1 >= $price['startqty'] || 1 <= $price['endqty'];
					});

					$lowestProductPrice = ($selectedProductPrice[0]['startqty'] * $selectedProductPrice[0]['unitsell']) + $selectedProductPrice[0]['baseprice'];

					$pricedKeys = ['coverlist', 'paperlist', 'singleprintlist', 'calendarcustomisationlist', 'taopixailist'];

					foreach ($pricedKeys as $t => $n) {
						if (empty($productData[$n])) {
							continue;
						}

						$compPrice = ($selectedProductPrice[0]['startqty'] * $this->processComponentPrice($productData[$n], ('singleprintlist' === $n ? $productData['singleprintoptionlist'] : []), $qtyInfo, $productData['taxrate'], $removeTax));

						//correct prices for tax
						$lowestProductPrice = $this->correctPrice($lowestProductPrice, $removeTax, $productData['pricetaxrate']);

						if ($showPricesWithTax) {
							if ($removeTax) {
								//if the tax rates are different then we will have the nett price so we add the assets total and then add the tax
								$lowestProductPrice += $compPrice;
								if ($pPricesIncludeTax == 1) {
									$lowestProductPrice = $lowestProductPrice + (($lowestProductPrice * $productData['taxrate']) / 100);
								}
							} else {

								//if the tax rates are the same then we already have tax applied so we add tax onto the assets and then sum the two values

								if ($pPricesIncludeTax == 1) {
									$compPrice = $compPrice + (($compPrice * $productData['taxrate']) / 100);
								}
								$lowestProductPrice = $lowestProductPrice + $compPrice;
							}
						} else {
							//if prices do not include tax and tax has been added then remove it now (all tax rates are the same so we can just use the product tax rate)
							if ((!$removeTax) && ($productData['pricetaxrate'] > 0.00)) {
								$lowestProductPrice = ($lowestProductPrice / ($productData['pricetaxrate'] + 100)) * 100;
							}

							$lowestProductPrice = $lowestProductPrice + $compPrice;
						}
					}
					$priceMap[$productData['code']] = $lowestProductPrice;
				}
			}
		} catch (Exception $ex) {
			echo $ex->getMessage() . PHP_EOL;
			echo $ex->getTraceAsString() . PHP_EOL;
		}
		return $priceMap;
	}

	/*
     * recursively delete a folder and its contents
     *
     * @param $pSource - folder to be deleted
     */
	public function deleteFolder($pSource)
	{
		return \UtilsObj::deleteFolder($pSource);
	}

	/*
     * bankers rounding
     *
	 * // $dVal is value to round
     * // $iDec specifies number of decimal places to retain
     */
	public function bround($dVal, $iDec)
	{
		return \UtilsObj::bround($dVal, $iDec);
	}

	/**
	 * Returns the system config
	 *
	 * @return array System config array.
	 */
	public function getACConfig(): array
	{
		return \UtilsObj::readConfigFile(__DIR__ . '/../../../../config/mediaalbumweb.conf');
	}

	/**
	 * Creates a Taopix event.
	 *
	 * @param string $pTaskCode Taopix task code.
	 * @param string $pCompanyCode Company code.
	 * @param string $pGroupCode Group code.
	 * @param string $pBrandCode Brand code.
	 * @param string $pNextRunTime Next run time date in Y-md-d h:i:s format.
	 * @param int $pParentid Parent task ID.
	 * @param mixed $pParam1 Event data to store.
	 * @param mixed $pParam2 Event data to store.
	 * @param mixed $pParam3 Event data to store.
	 * @param mixed $pParam4 Event data to store.
	 * @param mixed $pParam5 Event data to store.
	 * @param mixed $pParam6 Event data to store.
	 * @param mixed $pParam7 Event data to store.
	 * @param mixed $pParam8 Event data to store.
	 * @param int $pOrderHeaderID Order header ID for an order.
	 * @param int $pOrderItemID Order item ID for an order item.
	 * @param int $pUserID User ID of the user that triggered the event.
	 * @param string $pTask1 Event task 1 value.
	 * @param string $pTask2 Event task 2 value.
	 * @param int $pTargetUserID User ID of the target user.
	 * @return array Result array.
	 */
	public function createEvent(string $pTaskCode, string $pCompanyCode, string $pGroupCode, string $pBrandCode, string $pNextRunTime, int $pParentid, $pParam1, $pParam2,
	 	$pParam3, $pParam4, $pParam5, $pParam6, $pParam7, $pParam8, int $pOrderHeaderID, int $pOrderItemID, int $pUserID, string $pTask1, string $pTask2,
		int $pTargetUserID)
	{
		return \DatabaseObj::createEvent($pTaskCode, $pCompanyCode, $pGroupCode, $pBrandCode, $pNextRunTime, $pParentid, $pParam1, $pParam2, $pParam3,
			$pParam4, $pParam5, $pParam6, $pParam7, $pParam8, $pOrderHeaderID, $pOrderItemID, $pUserID, $pTask1, $pTask2, $pTargetUserID);
	}

	/*
	* Update ProjectOrderDataCache
	*
	* @param bool $pOrderMode is this an order
	* @param $pProjectRefList string projectrefs to update
	* @param $pConnectorID int connector ID
	* @param $pExternalProductID string external system product ID
	* @param $pOrderDate string date order placed
	* @param $pOrderNumber string order number
	*
	* @return array result of update.
	*/
   public function updateProjectOrderDataCache($pProjectRefList, $pOrderDate, $pOrderNumber)
   {
	   return \DatabaseObj::updateProjectOrderDataCache($pProjectRefList, $pOrderDate, $pOrderNumber);
   }

   /**
	* Decrypts data
     *
	 * @param $pData string - encrypted data to be decrypted
	 * @param $pSecret string - secret for decryption
	 * @param $pURLSafe bool - url safe
     * @return string the decrypted data
     *
	*/
	public function decryptData($pData, $pSecret, $pURLSafe)
	{
			return \UtilsObj::decryptData($pData, $pSecret, $pURLSafe);
	}

	/**
	 * Retrieves the configuration data for this TAOPIX system
	 *
	 * @static
	 *
	 * @return array
	 *   the result array will contain the response of the database operation
	 */
	static function getSystemConfig()
	{
		return \DatabaseObj::getSystemConfig();
	}

	/**
	 * Format price for shopify
	 *
	 * @param $pTheNumber float - the number to be formatted
	 * @param $pDecimalPlaces int - number of decimal places
	 *
	 * @return float - the formatted number
	 */
	public function formatNumber($pTheNumber, $pDecimalPlaces)
	{
		 return number_format($pTheNumber, $pDecimalPlaces, '.', '');
	}

	/**
	 * Builds array of component data for single prints
	 *
	 * @param array $pProjectData full project data
	 * @param string $pLang locale to use with component names
	 * @return array array of component data
	 */
	public function preparePhotoPrints(array $pProjectData, string $pLang = 'en'): Array
	{
		$pictures = $pProjectData['pictures'];
		$dataArray = [];

		foreach ($pictures as $picture)
		{
			$componentcode = $picture['componentcode'];
			$subcomponentcode = trim($picture['subcomponentcode']);
			$componentname = $this->getLocaleString($picture['componentname'], $pLang, true);
			$subcomponentname = $this->getLocaleString($picture['subcomponentname'], $pLang, true);
			$componentQty = $picture['componentqty'];
			$subComponentQty = $picture['componentqty'];

			//if Component already used add the Qty together
			if (isset($dataArray[$componentcode]))
			{
				$componentQty = $dataArray[$componentcode]['qty'] + $picture['componentqty'];
			}
			else
			{
				$dataArray[$componentcode]['componentcode'] = $componentcode;
				$dataArray[$componentcode]['componentname'] = $componentname;
			}
			$dataArray[$componentcode]['qty'] = $componentQty;

			//if Sub Component already used add the Qty together
			if ($subcomponentcode != '')
			{
				if (isset($dataArray[$componentcode]['subcomponents'][$subcomponentcode]))
				{
					$subComponentQty = $dataArray[$componentcode]['subcomponents'][$subcomponentcode]['qty'] + $picture['componentqty'];
				}
				else
				{
					$dataArray[$componentcode]['subcomponents'][$subcomponentcode]['subcomponentcode'] = $subcomponentcode;
					$dataArray[$componentcode]['subcomponents'][$subcomponentcode]['subcomponentname'] = $subcomponentname;
				}

				$dataArray[$componentcode]['subcomponents'][$subcomponentcode]['qty'] = $subComponentQty;
			}

		}

		return $dataArray;
	}

	/**
	 * Creates display HTML from component array
	 *
	 * @param array $pComponentDataArray Component Data to build HTML from
	 * @return string HTML to display
	 */
	public function componentDescriptionHTML(array $pComponentDataArray): string
	{
		$componentHTML = '<ul class="tpx_component">';

		foreach($pComponentDataArray as $component)
		{
			$subComponentHTML = '';
			if (isset($component['subcomponents']))
			{
				if (count($component['subcomponents']))
				{
					$subComponentHTML .= '<ul class="tpx_subcomponent">';
					foreach($component['subcomponents'] as $subcomponent)
					{
						$subComponentHTML .= '<li class="tpx_subcomponentitem">'. $subcomponent['qty'] . ' x ' . $subcomponent['subcomponentname'] . '</li>';
					}
					$subComponentHTML .= '</ul>';
				}
			}
			$componentHTML .= '<li class="tpx_componentitem">';
			if ($subComponentHTML != '')
			{
				$componentHTML .= $component['componentname'] . $subComponentHTML . '</li>';
			}
			else
			{
				$componentHTML .=  $component['qty'] . ' x ' . $component['componentname'] . '</li>';
			}
		}

		$componentHTML .= '</ul>';

		return $componentHTML;
	}

	/**
	 * Moves a temporary file into the correct location, also creating the directory if necessary.
	 *
	 * @param string $pTempPath Location of the temporary file to move.
	 * @param string $pDestinationPath Location to move the file to.
	 * @return string Empty if no errors, else contains the error string key.
	 */
	public function moveUploadedFile(string $pTempPath, string $pDestinationPath)
	{
        return \UtilsObj::moveUploadedFile( $pTempPath, $pDestinationPath);
	}

	/*
    * Write data to a given file in the logs directory.
    *
    * @param $pFileName name of the file to write to.
    * @param $pError the error string or an error array to log.
    * @param $pExtraData any extra data to be logged against the error.
    */
	public function writeToDebugFileInLogsFolder(string $pFileName, string $pError = '', array $pExtraData = array())
	{
		return \UtilsObj::writeToDebugFileInLogsFolder($pFileName, $pError,  $pExtraData);
	}

	public function createRandomString(int $pLength, bool $pUppercase = false)
	{
		return \UtilsObj::createRandomString($pLength, $pUppercase);
	}

	public function insertProductGroupLinkRecord($pGroupID, $pAssigneeCode, $pAssigneeType)
	{
		return \DatabaseObj::insertProductGroupLinkRecord($pGroupID, $pAssigneeCode, $pAssigneeType);
	}

	public function deleteProductGroupLinkRecordsByAssigneeCode($pCode, $pAssigneeType)
	{
		return \DatabaseObj::deleteProductGroupLinkRecordsByAssigneeCode($pCode, $pAssigneeType);
	}

	/**
	 * Gets the group header record ID from the passed name
	 * @param string $pName The name of the group
	 * @return array standard taopix return array with the group id in the data key
	 */
	public function getProductGroupIDFromName($pName)
	{
		$resultArray = \UtilsObj::getReturnArray();
		$error = '';
		$errorParam = '';
		$resultID = 0;

		$dbObj = \DatabaseObj::getGlobalDBConnection();

		if ($dbObj)
		{
			$sql = 'SELECT `id` FROM `productgroupheader` WHERE `name` = ?';
			$bindDataType = "s";

			if ($stmt = $dbObj->prepare($sql))
			{
				if ($stmt->bind_param($bindDataType, $pName))
				{
					if ($stmt->execute())
					{
						if ($stmt->store_result())
						{
							if ($stmt->num_rows == 1)
							{
								if ($stmt->bind_result($resultID))
								{
									if ($stmt->fetch())
                                    {
										$resultArray['data'] = $resultID;
									}
									else
									{
										$error = 'str_DatabaseError';
										$errorParam = __FUNCTION__ . ' fetch error: ' . $dbObj->error;
									}
								}
								else
								{
									$error = 'str_DatabaseError';
									$errorParam = __FUNCTION__ . ' bindresult error: ' . $dbObj->error;
								}
							}
							else
							{
								$error = 'str_DatabaseError';
								$errorParam = __FUNCTION__ . ' numrows error: ' . $dbObj->error;
							}
						}
						else
						{
							$error = 'str_DatabaseError';
							$errorParam = __FUNCTION__ . ' storeresult error: ' . $dbObj->error;
						}
					}
					else
					{
						$error = 'str_DatabaseError';
						$errorParam = __FUNCTION__ . ' execute error: ' . $dbObj->error;
					}
				}
				else
				{
					$error = 'str_DatabaseError';
					$errorParam = __FUNCTION__ . ' bindparam error: ' . $dbObj->error;
				}

				$stmt->free_result();
				$stmt->close();
				$stmt = null;
			}
			else
			{
				$error = 'str_DatabaseError';
				$errorParam = __FUNCTION__ . ' prepare error: ' . $dbObj->error;
			}

			$dbObj->close();
		}
		else
		{
			$error = 'str_DatabaseError';
			$errorParam = __FUNCTION__ . ' connect error: ' . $dbObj->error;
		}

		$resultArray['error'] = $error;
		$resultArray['errorparam'] = $errorParam;

		return $resultArray;
	}

    public function createNewAccount(array $pData){

        return \AuthenticateObj::createNewAccount($pData);
    }

    public function processLogin(array $pData){

        return \AuthenticateObj::processLogin(1, false, $pData);
    }

    public function getLicenseKeyFromCode(string $pGroupCode) {

        return \DatabaseObj::getLicenseKeyFromCode($pGroupCode);
    }

    public function getClientIPAddress(){

        return \UtilsObj::getClientIPAddress();
    }

    public function createDataStoreRecord($pPrivateDataArray, $pOriginURL, $pSSOURL, $pType, $pReason, $pRef, $pJSON){

        return \AuthenticateObj::createDataStoreRecord($pPrivateDataArray, $pOriginURL, $pSSOURL, $pType, $pReason, $pRef, $pJSON);
    }

    static function resetPasswordRequest($pWebBrandCode, $pLogin, $pPasswordFormat)
    {
        return \OnlineAPI_model::resetPasswordRequest($pWebBrandCode, $pLogin, $pPasswordFormat);
    }

    static function getAuthenticationDataRecord($pType, $pAuthKey, $pExpectJSONData){

        return \AuthenticateObj::getAuthenticationDataRecord($pType, $pAuthKey, $pExpectJSONData);
    }

    static function getUserAccountFromID($userID){

        return \DatabaseObj::getUserAccountFromID($userID);
    }

    public function updateOrderActiveStatus($pOrderID, $pUserID, $pActiveStatus)
    {
        \AppProductionAPI_model::updateOrderActiveStatus($pOrderID, $pUserID, $pActiveStatus);
    }

    public function updateOrderPaymentStatus($pOrderIDList, $pUserID, $pOrderPaymentReceived, $pOrderPaymentReceivedDate)
    {
        \AppProductionAPI_model::updateOrderPaymentStatus($pOrderIDList, $pUserID, $pOrderPaymentReceived, $pOrderPaymentReceivedDate);
    }

    public function getBrowserLocale(){
        return \UtilsObj::getBrowserLocale();
    }
}
