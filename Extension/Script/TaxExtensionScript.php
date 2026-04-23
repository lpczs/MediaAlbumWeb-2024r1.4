<?php

namespace Extension\Script;

use Extension\Script\Callback\TaxExtensionScriptCallback;
use Extension\Script\Exception\ExtensionScriptMethodNotFoundException;
use Extension\Script\Exception\ExtensionScriptNotLoadedException;
use Extension\Script\Exception\UnhandledExtensionScriptErrorException;
use PricingEngine\BCMath;
use PricingEngine\Enum\ExtensionScript;
use PricingEngine\Tax\TaxRate;
use PricingEngine\Tax\TaxRateInterface;

class TaxExtensionScript extends AbstractExtensionScript
{
	const FILE_NAME = 'EDL_TaxCalculation.php';
	const CLASS_NAME = 'TaxCalculationAPI';

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
	public function __construct($extensionPath, &$session, $callbackClassName = TaxExtensionScriptCallback::class)
	{
		parent::__construct($extensionPath);
		$this->session = &$session;
		$this->callbackClassName = $callbackClassName;
	}

	/**
	 * Get the full extension script file path to include
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
	 * @return TaxRateInterface|null
	 * @throws ExtensionScriptMethodNotFoundException
	 * @throws ExtensionScriptNotLoadedException
	 * @throws UnhandledExtensionScriptErrorException
	 */
	public function getShippingTaxRate()
	{
		$arguments = [];
		$arguments['brandcode'] = $this->session['webbrandcode'];
		$arguments['groupcode'] = $this->session['licensekeydata']['groupcode'];
		$arguments['groupdata'] = $this->session['licensekeydata']['groupdata'];
		$arguments['browserlanguagecode'] = $this->session['browserlanguagecode'];
		$arguments['currencycode'] = $this->session['order']['currencycode'];
		$arguments['currencyexchange'] = $this->session['order']['currencyexchangerate'];
		$arguments['currencydecimalplaces'] = $this->session['order']['currencydecimalplaces'];
		$arguments['taxcalculationaddress'] = [];
		$arguments['customershippingaddress'] = [];
		$arguments['customerbillingaddress'] = [];
		$arguments['cartitems']['lineitems'] = $this->session['items'];
		$arguments['cartitems']['orderfooteritems']['orderfootersections'] = $this->session['order']['orderFooterSections'];
		$arguments['cartitems']['orderfooteritems']['orderfootercheckboxes'] = $this->session['order']['orderFooterCheckboxes'];

		$arguments['shipping'] = [
			'shippingmethodcode' => $this->session['shipping'][0]['shippingmethodcode'],
			'shippingratecode' => $this->session['shipping'][0]['shippingratecode'],
			'shippingratecost' => $this->session['shipping'][0]['shippingratecost'],
			'shippingratesell' => $this->session['shipping'][0]['shippingratesell'],
			'shippingratesellnotax' => $this->session['shipping'][0]['shippingratesellnotax'],
			'shippingratesellwithtax' => $this->session['shipping'][0]['shippingratesellwithtax'],
			'shippingratepricetaxcode' => $this->session['shipping'][0]['shippingratepricetaxcode'],
			'shippingratepricetaxrate' => $this->session['shipping'][0]['shippingratepricetaxrate'],
			'shippingratediscountvalue' => $this->session['shipping'][0]['shippingratediscountvalue']
		];

		$this->copyAddressFields($arguments);
		$result = $this->callExtension('getShippingTaxRate', $arguments);

		if (ExtensionScript::TAX_SCRIPT_TAX_CODE != substr(@$result['customtaxdetails']['code'], 0, 13)) {
			return null;
		}

		return new TaxRate(
			$result['customtaxdetails']['code'],
			BCMath::round($result['customtaxdetails']['rate'], 4),
			$result['customtaxdetails']['description']
		);
	}

	/**
	 * @param array $arguments
	 */
	private function copyAddressFields(&$arguments)
	{
		call_user_func_array([$this->callbackClassName, 'copyAddressFields'], [&$this->session, &$arguments]);
	}
}
