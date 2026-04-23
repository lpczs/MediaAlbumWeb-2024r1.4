<?php

namespace Extension\Script;

use Extension\Script\Exception\ExtensionScriptMethodNotFoundException;
use Extension\Script\Exception\ExtensionScriptNotLoadedException;
use Extension\Script\Exception\UnhandledExtensionScriptErrorException;
use Extension\Script\Callback\ShippingExtensionScriptCallback;
use PricingEngine\BCMath;

class ShippingExtensionScript extends AbstractExtensionScript
{
	const FILE_NAME = 'EDL_ShippingRateAPI.php';
	const CLASS_NAME = 'ShippingRateAPI';

	/**
	 * @var mixed[]
	 */
	private $session;

	/**
	 * @var string
	 */
	private $callbackClassName;

	/**
	 * Constructor
	 *
	 * @param string $extensionPath
	 * @param mixed[] $session
	 * @param string $callbackClassName
	 */
	public function __construct($extensionPath, &$session, $callbackClassName = ShippingExtensionScriptCallback::class)
	{
		parent::__construct($extensionPath);
		$this->session = &$session;
		$this->callbackClassName = $callbackClassName;
	}

	/**
	 * Get the extension bootstrap file to include
	 *
	 * @return string
	 */
	protected function getExtensionFilePath()
	{
		return $this->extensionPath . DIRECTORY_SEPARATOR . self::FILE_NAME;
	}

	/**
	 * Get the extension class name
	 *
	 * @return string
	 */
	protected function getExtensionClassName()
	{
		return self::CLASS_NAME;
	}

	/**
	 * @param $shippingMethodEntry
	 */
	private function setShippingMethodDefaults(&$shippingMethodEntry)
	{
		call_user_func_array([$this->callbackClassName, 'setShippingMethodDefaults'], [&$shippingMethodEntry]);
	}

	/**
	 * @param $shippingMethodCode
	 * @param $shippingMethodName
	 * @param $shippingMethodEntry
	 * @param $groupCode
	 */
	private function setCollectFromStoreValues($shippingMethodCode, $shippingMethodName, &$shippingMethodEntry, $groupCode)
	{
		call_user_func_array([$this->callbackClassName, 'setCollectFromStoreValues'], [
			$shippingMethodCode,
			$shippingMethodName,
			&$shippingMethodEntry,
			$groupCode]
		);
	}

	/**
	 * @param $taxCode
	 * @return mixed[]
	 */
	private function getTaxRate($taxCode)
	{
		return call_user_func_array([$this->callbackClassName, 'getTaxRate'], [$taxCode]);
	}

	/**
	 * @param $sourceAddressArray
	 * @param $sourceKeyPrefix
	 * @param $destinationArray
	 * @param $destinationKeyPrefix
	 * @param $copyingDefaultAddress
	 * @param $addressFieldsOnly
	 */
	private function copyArrayAddressFields($sourceAddressArray, $sourceKeyPrefix, &$destinationArray,
		$destinationKeyPrefix, $copyingDefaultAddress, $addressFieldsOnly)
	{
		call_user_func_array([$this->callbackClassName, 'copyArrayAddressFields'], [
			$sourceAddressArray,
			$sourceKeyPrefix,
			&$destinationArray,
			$destinationKeyPrefix,
			$copyingDefaultAddress,
			$addressFieldsOnly
		]);
	}

	private function getShippingRateFromMethodAndRateCode($methodCode, $rateCode, $groupCode, $companyCode)
	{
		return call_user_func_array([$this->callbackClassName, 'getShippingRateFromMethodAndRateCode'], [
			$methodCode,
			$rateCode,
			$groupCode,
			$companyCode
		]);
	}

	/**
	 * @param mixed[] $shippingMethods
	 * @return mixed[]
	 * @throws ExtensionScriptMethodNotFoundException
	 * @throws ExtensionScriptNotLoadedException
	 * @throws UnhandledExtensionScriptErrorException
	 */
	public function buildShippingMethodsList($shippingMethods)
	{
		$inputShippingMethods = [];
		$shippingMethodCodes = [];
		$newShippingMethodCodes = [];
		$newShippingMethodsList = [];
		$taxRateCache = [];

		foreach ($shippingMethods as $shippingMethod) {
			if (!in_array($shippingMethod['methodcode'], $shippingMethodCodes)) {
				$shippingMethodCodes[] = $shippingMethod['methodcode'];
			}

			$inputShippingMethods[] = [
				'shippingratecode' => $shippingMethod['ratecode'],
				'shippingrateinfo' => $shippingMethod['info'],
				'shippingratecost' => $shippingMethod['cost'],
				'shippingratesell' => $shippingMethod['sell'],
				'shippingmethodcode' => $shippingMethod['methodcode'],
				'shippingratepricetaxcode' => $shippingMethod['taxcode'],
				'orderminvalue' => $shippingMethod['orderminvalue'],
				'ordermaxvalue' => $shippingMethod['ordermaxvalue']
			];
		}

		// Call shipping script to build shipping method list
		$paramArray = $this->buildShippingAPIParams($inputShippingMethods);
		$result = $this->callExtension('buildShippingMethodsList', $paramArray);

		$this->session['shipping'][0]['shippingprivatedata'] = $result['privatedata'];

		// record customershippingcountrycode so we can determine if the customer has changed country
		// this can be used by the script to set resetselection if needed
		// if $this->session['shipping'][0]['shippingprivatedata']['customershippingcountrycode'] is empty then we know it's the first time
		$this->session['shipping'][0]['shippingprivatedata']['customershippingcountrycode'] = $paramArray['shippingaddress']['shippingcustomercountrycode'];

		// build a list of shipping methods codes
		// this is used to calulcate new methods added by the script
		foreach ($result['shippingmethodslist'] as $outputShippingMethods) {
			$newShippingMethodCodes[] = $outputShippingMethods['shippingmethodcode'];
		}

		// check for new shipping methods from the edl script and look it up to make sure it is a valid shipping method
		$shippingMethodCodeDiffArray = array_diff($newShippingMethodCodes, $shippingMethodCodes);

		// store processed shipping methods so we can filter out duplicates
		$processedShippingMethods = [];

		// loop through all the shipping methods returned by the script and process them
		foreach ($result['shippingmethodslist'] as $theShippingMethod) {
			$shippingMethodTemp = [];

			// filter out duplicate shipping methods
			if (!in_array($theShippingMethod['shippingmethodcode'], $processedShippingMethods)) {
				// check if the rate has been added from the script and not originally from taopix
				if (in_array($theShippingMethod['shippingmethodcode'], $shippingMethodCodeDiffArray)) {
					// lookup shipping rate info from database as we don't have the details at this point
					$getShippingRateFromCodeResult = $this->getShippingRateFromMethodAndRateCode(
						$theShippingMethod['shippingmethodcode'],
						$theShippingMethod['shippingratecode'],
						$this->session['licensekeydata']['groupcode'],
						$this->session['userdata']['companycode']
					);

					// if the rate has been found then set the shipping method to the values from the database
					// these will be overwritten with what was returned from the script later on
					if ($getShippingRateFromCodeResult['error'] == '') {
						$shippingMethodTemp = $getShippingRateFromCodeResult['data'];

						// check if this shipping method already exists in the session, if not then create the method with default/empty settings
						// this prevents collect from store details being overwritten if a collect from store method is added via the script
						if (! array_key_exists($theShippingMethod['shippingmethodcode'], $this->session['shipping'][0]['shippingMethods'])) {
							$shippingMethodEntry = array();
							$this->setShippingMethodDefaults($shippingMethodEntry);

							if ($shippingMethodTemp['collectfromstore']) {
								$this->setCollectFromStoreValues(
									$shippingMethodTemp['methodcode'],
									$shippingMethodTemp['methodname'],
									$shippingMethodEntry,
									$this->session['licensekeydata']['groupcode']
								);
							}

							$this->session['shipping'][0]['shippingMethods'][$theShippingMethod['shippingmethodcode']] = $shippingMethodEntry;
							$this->session['shipping'][0]['shippingMethods'][$theShippingMethod['shippingmethodcode']]['payInStoreAllowed'] = $shippingMethodTemp['payinstoreallowed'];
						}
					}
				} else {
					// shipping method was returned by taopix so use the existing details
					// these will be overwritten with what was returned from the script later on
					$methodCode = $theShippingMethod['shippingmethodcode'];
					$origShippingMethodArray = array_values(array_filter($shippingMethods, function($pValue) use($methodCode) {
						return $pValue['methodcode'] == $methodCode;
					}));

					if (isset($origShippingMethodArray[0])) {
						$shippingMethodTemp = $origShippingMethodArray[0];
					}
				}

				$processedShippingMethods[] = $theShippingMethod['shippingmethodcode'];
			}

			// if we have some shipping methods then apply the changes from the script
			// we may not have any shipping methods if they are not set for the correct zone, etc. and none have been added by the script
			if (count($shippingMethodTemp) > 0) {
				// apply changes from the script
				$shippingMethodTemp['ratecode'] = (isset($theShippingMethod['shippingratecode'])) ? $theShippingMethod['shippingratecode'] : $shippingMethodTemp['ratecode'];
				$shippingMethodTemp['info'] = (isset($theShippingMethod['shippingrateinfo'])) ? $theShippingMethod['shippingrateinfo'] : $shippingMethodTemp['info'];
				$shippingMethodTemp['cost'] = (isset($theShippingMethod['shippingratecost'])) ? $theShippingMethod['shippingratecost'] : $shippingMethodTemp['cost'];
				$shippingMethodTemp['sell'] = (isset($theShippingMethod['shippingratesell'])) ? $theShippingMethod['shippingratesell'] : $shippingMethodTemp['sell'];

				// look up the taxrate value from the shippingratepricetaxcode value either returned from taopix or the script
				// only look up the tax rate if the code has been changed
				if ($theShippingMethod['shippingratepricetaxcode'] != '' &&
					$shippingMethodTemp['taxcode'] != $theShippingMethod['shippingratepricetaxcode']) {
					// cache the gettaxrate() results so we don't need to call the database everytime the same taxcode is used
					if (! array_key_exists($theShippingMethod['shippingratepricetaxcode'], $taxRateCache)) {
						// taxrate does not exist in the cache so we need to look it up from the database
						$taxDataArray = $this->getTaxRate($theShippingMethod['shippingratepricetaxcode']);

						if ($taxDataArray['result'] == '') {
							$shippingMethodTemp['taxcode'] = $theShippingMethod['shippingratepricetaxcode'];
							$shippingMethodTemp['taxrate'] = $taxDataArray['rate'];

							$taxRateCache[$theShippingMethod['shippingratepricetaxcode']] = $taxDataArray;
						} else {
							$shippingMethodTemp['taxcode'] = '';
							$shippingMethodTemp['taxrate'] = 0.00;
						}
					} else {
						// read from cache
						$shippingMethodTemp['taxcode'] = $theShippingMethod['shippingratepricetaxcode'];
						$shippingMethodTemp['taxrate'] =$taxRateCache[$theShippingMethod['shippingratepricetaxcode']]['rate'];
					}
				} else {
					$shippingMethodTemp['taxcode'] = '';
					$shippingMethodTemp['taxrate'] = 0.00;
				}

				$newShippingMethodsList[] = $shippingMethodTemp;
			}
		}

		return [$newShippingMethodsList, $result['resetselection'] ? true : false];
	}

	/**
	 * @param mixed[] $shippingRatesList
	 * @throws ExtensionScriptMethodNotFoundException
	 * @throws ExtensionScriptNotLoadedException
	 * @throws UnhandledExtensionScriptErrorException
	 */
	public function setDefaultShippingMethod($shippingRatesList)
	{
		$shippingMethodList = [];
		$existingShippingMethodCodeArray = [];

		// Prepare script call parameters
		foreach ($shippingRatesList as $shippingRate) {
			if (!in_array($shippingRate['methodcode'], $existingShippingMethodCodeArray)) {
				$existingShippingMethodCodeArray[] = $shippingRate['methodcode'];
			}

			$shippingMethodList[] = [
				'shippingratecode' => $shippingRate['ratecode'],
				'shippingrateinfo' => $shippingRate['info'],
				'shippingratecost' => $shippingRate['cost'],
				'shippingratesell' => $shippingRate['sell'],
				'shippingmethodcode' => $shippingRate['methodcode'],
				'taxcode' => $shippingRate['taxcode'],
				'taxrate' => $shippingRate['taxrate'],
				'orderminvalue' => $shippingRate['orderminvalue'],
				'ordermaxvalue' => $shippingRate['ordermaxvalue'],
			];
		}

		// Call script
		$paramArray = $this->buildShippingAPIParams($shippingMethodList);
		$defaultShippingMethod = $this->callExtension('setDefaultShippingMethod', $paramArray);

		// check a shipping method with this method code exists
		if (($defaultShippingMethod == '') || (! in_array($defaultShippingMethod, $existingShippingMethodCodeArray))) {
			$this->session['shipping'][0]['shippingmethodcode'] = $shippingRatesList[0]['methodcode'];
			$this->session['shipping'][0]['shippingmethodname'] = $shippingRatesList[0]['methodname'];
			$this->session['shipping'][0]['shippingmethodusedefaultshippingaddress'] = $shippingRatesList[0]['usedefaultshippingaddress'];
			$this->session['shipping'][0]['shippingmethodusedefaultbillingaddress'] = $shippingRatesList[0]['usedefaultbillingaddress'];
			$this->session['shipping'][0]['shippingmethodcanmodifycontactdetails'] = $shippingRatesList[0]['canmodifycontactdetails'];
			$this->session['shipping'][0]['shippingratecode'] = $shippingRatesList[0]['ratecode'];
			$this->session['shipping'][0]['shippingrateinfo'] = $shippingRatesList[0]['info'];
			$this->session['shipping'][0]['shippingratecost'] = BCMath::round($shippingRatesList[0]['cost'], $this->session['order']['currencydecimalplaces']);
			$this->session['shipping'][0]['shippingratesell'] = BCMath::round($shippingRatesList[0]['sell'], $this->session['order']['currencydecimalplaces']);
			$this->session['shipping'][0]['shippingratepricetaxcode'] = $shippingRatesList[0]['taxcode'];
			$this->session['shipping'][0]['shippingratepricetaxrate'] =  BCMath::round($shippingRatesList[0]['taxrate'], $this->session['order']['currencydecimalplaces']);

			if (isset($shippingRatesList[0]['payInStoreAllowed'])) {
				$this->session['shipping'][0]['payinstoreallowed'] = $shippingRatesList[0]['payInStoreAllowed'];
			} else {
				$this->session['shipping'][0]['payinstoreallowed'] = false;
			}

			$this->session['order']['shippingrequiresdelivery'] = $shippingRatesList[0]['requiresdelivery'];
		} else {
			$defaultShippingMethodArray = array_values(array_filter($shippingRatesList, function($pValue) use($defaultShippingMethod) {
				return $pValue['methodcode'] == $defaultShippingMethod;
			}));

			$this->session['shipping'][0]['shippingmethodcode'] = $defaultShippingMethodArray[0]['methodcode'];
			$this->session['shipping'][0]['shippingmethodname'] = $defaultShippingMethodArray[0]['methodname'];
			$this->session['shipping'][0]['shippingmethodusedefaultshippingaddress'] = $defaultShippingMethodArray[0]['usedefaultshippingaddress'];
			$this->session['shipping'][0]['shippingmethodusedefaultbillingaddress'] = $defaultShippingMethodArray[0]['usedefaultbillingaddress'];
			$this->session['shipping'][0]['shippingmethodcanmodifycontactdetails'] = $defaultShippingMethodArray[0]['canmodifycontactdetails'];
			$this->session['shipping'][0]['shippingratecode'] = $defaultShippingMethodArray[0]['ratecode'];
			$this->session['shipping'][0]['shippingrateinfo'] = $defaultShippingMethodArray[0]['info'];
			$this->session['shipping'][0]['shippingratecost'] = BCMath::round($defaultShippingMethodArray[0]['cost'], $this->session['order']['currencydecimalplaces']);
			$this->session['shipping'][0]['shippingratesell'] = BCMath::round($defaultShippingMethodArray[0]['sell'], $this->session['order']['currencydecimalplaces']);
			$this->session['shipping'][0]['shippingratepricetaxcode'] = $defaultShippingMethodArray[0]['taxcode'];
			$this->session['shipping'][0]['shippingratepricetaxrate'] = BCMath::round($defaultShippingMethodArray[0]['taxrate'], 4);

			if (isset($defaultShippingMethodArray[0]['payInStoreAllowed'])) {
				$this->session['shipping'][0]['payinstoreallowed'] = $defaultShippingMethodArray[0]['payInStoreAllowed'];
			} else {
				$this->session['shipping'][0]['payinstoreallowed'] = false;
			}

			$this->session['order']['shippingrequiresdelivery'] = $defaultShippingMethodArray[0]['requiresdelivery'];
		}
	}

	private function buildShippingAPIParams($pShippingMethodList)
	{
		$paramArray = array();
		$paramArray['brandcode'] = $this->session['webbrandcode'];
		$paramArray['groupcode'] = $this->session['licensekeydata']['groupcode'];
		$paramArray['groupdata'] = $this->session['licensekeydata']['groupdata'];
		$paramArray['browserlanguagecode'] = $this->session['browserlanguagecode'];
		$paramArray['currencycode'] = $this->session['order']['currencycode'];
		$paramArray['currencydecimalplaces'] = $this->session['order']['currencydecimalplaces'];
		$paramArray['currencyexchangerate'] = $this->session['order']['currencyexchangerate'];
		$paramArray['shippingweight'] = $this->session['order']['ordertotalshippingweight'];
		$paramArray['shippingaddress'] = array();
		$paramArray['billingaddress'] = array();
		$paramArray['cartitems']['lineitems'] = $this->session['items'];
		$paramArray['cartitems']['orderfooteritems']['orderfootersections'] = $this->session['order']['orderFooterSections'];
		$paramArray['cartitems']['orderfooteritems']['orderfootercheckboxes'] = $this->session['order']['orderFooterCheckboxes'];
		$paramArray['shippingmethodslist'] = $pShippingMethodList;
		$paramArray['privatedata'] = $this->session['shipping'][0]['shippingprivatedata'];

		$this->copyArrayAddressFields($this->session['shipping'][0], 'shippingcustomer', $paramArray['shippingaddress'], 'shipping', false, true);
		$this->copyArrayAddressFields($this->session['order'], 'billingcustomer', $paramArray['billingaddress'], 'billing', false, true);

		return $paramArray;
	}
}
