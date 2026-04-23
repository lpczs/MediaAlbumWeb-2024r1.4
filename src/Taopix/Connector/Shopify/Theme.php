<?php

namespace Taopix\Connector\Shopify;
use Taopix\Connector\Shopify\Entity\Theme as ThemeEntity;
use Taopix\Connector\Shopify\Collection\ThemeCollection;
use Taopix\Connector\Shopify\Asset;
use PHPShopify\ShopifySDK;
use PHPShopify\Exception\CurlException as PHPShopifyCurlException;
use Taopix\Connector\Shopify\Collection\AssetCollection;
use Taopix\Connector\Shopify\Exceptions\ThemeException;
use Taopix\Core\Utils\TaopixUtils;

class Theme
{
	use GraphQLTrait;

	/**
	 * @const themeZipFilename
	 */
	const THEME_ZIPFILENAME = 'dawn_taopix.zip';
	
	/**
	 * @const themeName
	 */
	const THEME_NAME = 'Taopix Dawn';

	/**
	 * @var ShopifySDK
	 */
	private $shopifySDK = null;

	/**
	 * @var ThemeCollection
	 */
	private $themeCollection = null;

	/**
	 * @var string
	 */
	private $controlCentreURL = '';

	/**
	 * @var TaopixUtils
	 */
	private $utils = null;

	/**
	 * @var array
	 */
	private $applyThemeErrors = [];

	/**
	 * Returns the Taopix Theme Name
	 * 
	 * @return string The Theme Name
	 */
	public function getTPXThemeName(): string
	{
		return self::THEME_NAME;
	}

	/**
	 * Returns the Taopix Theme Zipfile Name
	 * 
	 * @return string The Theme Zip name
	 */
	public function getTPXThemeZipName(): string
	{
		return self::THEME_ZIPFILENAME;
	}

	/**
	 * Sets the ShopifySDK instance.
	 * 
	 * @param ShopifySDK $pShopifySDK ShopifySDK instance to set.
	 * @return Theme Theme instance.
	 */
	public function setShopifySDK(ShopifySDK $pShopifySDK): Theme
	{
		$this->shopifySDK = $pShopifySDK;
		return $this;
	}

	/**
	 * Returns the ShopifySDK instance.
	 * 
	 * @return ShopifySDK ShopifySDK instance.
	 */
	public function getShopifySDK(): ShopifySDK
	{
		return $this->shopifySDK;
	}

	/**
	 * Sets the theme collection.
	 *
	 * @param ThemeCollection $pThemeCollection Theme collection to set.
	 * @return Theme Theme instance.
	 */
	public function setThemeCollection(ThemeCollection $pThemeCollection): Theme
	{
		$this->themeCollection = $pThemeCollection;
		return $this;
	}

	/**
	 * Returns the theme collection.
	 *
	 * @return ThemeCollection Collection of themes.
	 */
	public function getThemeCollection(): ThemeCollection
	{
		return $this->themeCollection;
	}

	/**
	 * Sets the Control Centre URL.
	 *
	 * @param string $pControlCentreURL Control Centre URL to set.
	 * @return Theme Theme instance.
	 */
	public function setControlCentreURL(string $pControlCentreURL): Theme
	{
		$this->controlCentreURL = $pControlCentreURL;
		return $this;
	}

	/**
	 * Returns the Control Centre URL value.
	 * 
	 * @return string The Control Centre URL.
	 */
	public function getControlCentreURL(): string
	{
		return $this->controlCentreURL;
	}

	/**
	 * Sets the Taopix Utils helper class.
	 *
	 * @param TaopixUtils $pUtils Taopix Utils class to set.
	 * @return Theme Theme instance.
	 */
	function setUtils(TaopixUtils $pUtils): Theme
	{
		$this->utils = $pUtils;
		return $this;
	}

	/**
	 * Returns the Taopix Utils helper class instance.
	 *
	 * @return TaopixUtils $pUtils Taopix Utils class.
	 */
	function getUtils(): TaopixUtils
	{
		return $this->utils;
	}

	/**
	 * Adds a theme error to the array.
	 *
	 * @param string $pError Error to add.
	 * @return Theme Theme instance.
	 */
	private function addApplyThemeError(string $pError): Theme
	{
		$this->applyThemeErrors[] = $pError;
		return $this;
	}

	/**
	 * Returns the theme error array.
	 *
	 * @return array Array of theme errors.
	 */
	public function getThemeErrors(): array
	{
		return $this->applyThemeErrors;
	}

	public function __construct(ShopifySDK $pShopifySDK, TaopixUtils $pUtils, string $pControlCentreURL)
	{
		$this->setShopifySDK($pShopifySDK)
			->setUtils($pUtils)
			->setThemeCollection($this->requestThemes())
			->setControlCentreURL($pControlCentreURL);
	}

	/**
	 * Requests the themes available on the Shopify store.
	 *
	 * @return ThemeCollection Collection of Shopify themes.
	 */
	public function requestThemes(): ThemeCollection
	{
		return new ThemeCollection($this->shopifySDK->Theme->get());
	}

	/**
	 * Get sthe main theme from the theme tollection.
	 *
	 * @return ThemeEntity The theme marked as main.
	 */
	public function getMainTheme(): ThemeEntity
	{
		return $this->themeCollection->getThemeByRole('main');
	}

	/**
	 * Runs the install process for themes.
	 *
	 * @param bool pPushTPXTheme should the install push taopix theme default true
	 */
	public function install(bool $pPushTPXTheme)
	{
		$mainTheme = $this->getMainTheme();
		$this->pushSnippet($mainTheme);
		
		//reset timeout between major actions as workload causing script timeout 
		set_time_limit(60);
		$asset = new Asset($this->shopifySDK, $mainTheme);
		$this->injectTemplates($asset, $mainTheme);
		
		//reset timeout between major actions as workload causing script timeout 
		set_time_limit(60);
		$this->injectTranslations($asset, $mainTheme);
		
		//reset timeout between major actions as workload causing script timeout 
		set_time_limit(60);
		if ($pPushTPXTheme)
		{
			$this->pushTaopixTheme();
		}
	}

	/**
	 * Push the required snippets to the main theme on Shopify.
	 *
	 * @param ThemeEntity $pTheme ThemeEntity instance to use.
	 */
	private function pushSnippet(ThemeEntity $pTheme): void
	{
		$snippet = new Snippet($this->getShopifySDK(), $pTheme);
		$snippet->pushTaopixProductTileLiquid();
		$snippet->pushTaopixProductDropDownLiquid();
		$snippet->pushTaopixProductButtonLiquid();
		$snippet->pushTaopixProductLiquid();
		$snippet->pushTaopixCartLiquid();
		$snippet->pushTaopixMyProjectsLiquid();

		$snippet->pushTaopixVariantCardLiquid();
		$snippet->pushTaopixProductFormVariant();
		$snippet->pushTaopixVariant();
		$snippet->pushTaopixCollectionsByVariant();
		$snippet->pushTaopixMainProductVariant();
		$snippet->pushTaopixCollectionByVariantJSON();
		$snippet->pushTaopixProductVariantJSON();
	}

	/**
	 * Inject the Taopix code into the theme.
	 *
	 * @param Asset $pAsset Asset instance.
	 * @param ThemeEntity $pTheme The theme to apply the changes to.
	 */
	private function injectTemplates(Asset $pAsset, ThemeEntity $pTheme): void
	{
		// Use individual try/catches, if one template cannot be modified then
		// still try to apply the changes to the other templates.
		// Changes will need to be applied manually if 1 (or more) fails.

		try
		{
			$this->injectCartTemplateChanges($pAsset, $pTheme);
		}
		catch (ThemeException $pError)
		{
			$this->addApplyThemeError($pError->getMessage());
		}

		try
		{
			$this->injectProductTemplateChanges($pAsset);
		}
		catch (ThemeException $pError)
		{
			$this->addApplyThemeError($pError->getMessage());
		}

		try
		{
			$this->injectCustomersAccountTemplateChanges($pAsset);
		}
		catch (ThemeException $pError)
		{
			$this->addApplyThemeError($pError->getMessage());
		}
		
		try
		{
			$this->injectProductGridChanges($pAsset);
		}
		catch (ThemeException $pError)
		{
			$this->addApplyThemeError($pError->getMessage());
		}

		try
		{
			$this->injectSearchChanges($pAsset);
		}
		catch (ThemeException $pError)
		{
			$this->addApplyThemeError($pError->getMessage());
		}

		try
		{
			$this->injectPredictiveSearchChanges($pAsset);
		}
		catch (ThemeException $pError)
		{
			$this->addApplyThemeError($pError->getMessage());
		}

		try
		{
			$this->injectPriceChanges($pAsset);
		}
		catch (ThemeException $pError)
		{
			$this->addApplyThemeError($pError->getMessage());
		}
		
		try
		{
			$this->injectGlobalJSChanges($pAsset, $pTheme);
		}
		catch (ThemeException $pError)
		{
			$this->addApplyThemeError($pError->getMessage());
		}

		try
		{
			$this->injectFacetChanges($pAsset, $pTheme);
		}
		catch (ThemeException $pError)
		{
			$this->addApplyThemeError($pError->getMessage());
		}
		
		try
		{
			$this->injectProductFormChanges($pAsset, $pTheme);
		}
		catch (ThemeException $pError)
		{
			$this->addApplyThemeError($pError->getMessage());
		}

	}

	/**
	 * Injects the Taopix code into the cart-template.liquid theme file.
	 *
	 * @throws ThemeException If unable to apply the modification, target replacement may not exist for example.
	 * @param Asset $pAsset Asset instance.
	 */
	private function injectCartTemplateChanges(Asset $pAsset, ThemeEntity $pTheme): void
	{
		$error = '';
		$key = 'sections/main-cart-items.liquid';

		try 
		{
			$cartTitleLinkFound = 0;
			$titleLinkFound = 0;
			$quantitySelectorPlusFound = 0;
			$quantitySelectorMinusFound = 0;
			$quantitySelectorInputFound = 0; 
			$assetEntity = $pAsset->requestAsset($key);
			$template = $assetEntity->getValue();
			$codeToInject = <<<CTI
			{% if item.properties.__taopix_project_id %}
				{{ item.product.title | escape }}
			{% else %}
CTI;

			$codeToInjectAfter = <<<CTI
			{% endif %}
			
			{% render 'tpx-cart' item: item %}
CTI;

			// Detect if the template already has the changes.
			$matches = [];
			$templateHasChanges = preg_match('/' . trim($codeToInject) . '/is', $template, $matches);

			if ($templateHasChanges === 0)
			{
				// Template does not have the changes.

				$matches = [];
				$pattern = '/(<a href="{{ item.url }}" class="cart-item__name h4 break">)(...*?)(<\/a>)/is';
				$cartTitleLinkFound = preg_match($pattern, $template, $matches);

				if ($cartTitleLinkFound === 1)
				{
					// We expect $matches to have 4 parts.
					if (count($matches) === 4)
					{
						// Store the current code.
						$origElement = $matches[0];

						$newTemplateParts = [
							$codeToInject,
							$origElement,
							$codeToInjectAfter
						];

						$newTemplate = preg_replace($pattern, implode("\n", $newTemplateParts), $template);
						$template = $newTemplate;
					}
					else
					{
						if ($cartTitleLinkFound === 0)
						{
							$error = 'Cart title link not found in template: ' . $key;
						}
						else
						{
							$error = 'Unable to inject code into template: ' . $key;
						}
					}
				}
				else
				{
					$error = 'Unable to run regular expression on template ' . $key . '. Regex error: ' . preg_last_error_msg();
				}
			}
			else if ($templateHasChanges === false)
			{
				$error = 'Unable to run regular expression on template ' . $key . '. Regex error: ' . preg_last_error_msg();
			}


			// Detect if the template already has the title changes.
			$titleMatches = [];
			$titleCodeToInject = <<<CTI
			{% unless item.properties.__taopix_project_id %}
CTI;

			$titleTemplateHasChanges = preg_match('/' . trim($titleCodeToInject) . '/is', $template, $titleMatches);

			if ($titleTemplateHasChanges === 0)
			{
				// Template does not have the changes.

				$titleMatches = [];
				$titlePattern = '/(<a href="{{ item.url }}" class="cart-item__link" aria-hidden="true" tabindex="-1">)(.*?)(<\/a>)/is';
				$titleLinkFound = preg_match($titlePattern, $template, $titleMatches);

				if ($titleLinkFound === 1)
				{
					// We expect $matches to have 4 parts.
					if (count($titleMatches) === 4)
					{
						// Store the current code.
						$origElement = $titleMatches[0];

						$newTemplateParts = [
							$titleCodeToInject,
							$origElement,
							'{% endunless %}'
						];

						$newTemplate = preg_replace($titlePattern, implode("\n", $newTemplateParts), $template);
						$template = $newTemplate;
					}
					else
					{
						if ($titleLinkFound === 0)
						{
							$error = 'Cart Image link not found in template: ' . $key;
						}
						else
						{
							$error = 'Unable to inject code into template: ' . $key;
						}
					}
				}
				else
				{
					$error = 'Unable to run regular expression on template ' . $key . '. Regex error: ' . preg_last_error_msg();
				}
			}
			else if ($titleTemplateHasChanges === false)
			{
				$error = 'Unable to run regular expression on template ' . $key . '. Regex error: ' . preg_last_error_msg();
			}

			// Detect if the template already has the quantitySelectorPlus changes.
			$quantitySelectorPlusMatches = [];
			$quantitySelectorPlusCodeToInject = <<<CTI
			{% if quantity_selection == true %}
CTI;

			$quantitySelectorPlusMinusTemplateHasChanges = preg_match('/' . trim($quantitySelectorPlusCodeToInject) . '/is', $template, $quantitySelectorPlusMatches);

			if ($quantitySelectorPlusMinusTemplateHasChanges === 0)
			{
				// Template does not have the changes.

				$quantitySelectorPlusMatches = [];
				$quantitySelectorPlusPattern = '/(<button class="quantity__button no-js-hidden" name="plus" type="button">)(.*?)(<\/button>)/is';
				$quantitySelectorPlusFound = preg_match($quantitySelectorPlusPattern, $template, $quantitySelectorPlusMatches);

				if ($quantitySelectorPlusFound === 1)
				{
					// We expect $matches to have 4 parts.
					if (count($quantitySelectorPlusMatches) === 4)
					{
						// Store the current code.
						$origElement = $quantitySelectorPlusMatches[0];

						$newTemplateParts = [
							$quantitySelectorPlusCodeToInject,
							$origElement,
							'{% endif %}'
						];

						$newTemplate = preg_replace($quantitySelectorPlusPattern, implode("\n", $newTemplateParts), $template);
						$template = $newTemplate;
					}
					else
					{
						if ($quantitySelectorPlusFound === 0)
						{
							$error = 'Quantity Plus Button not found in template: ' . $key;
						}
						else
						{
							$error = 'Unable to inject code into template: ' . $key;
						}
					}
				}
				else
				{
					$error = 'Unable to run regular expression on template ' . $key . '. Regex error: ' . preg_last_error_msg();
				}
			}
			else if ($quantitySelectorPlusMinusTemplateHasChanges === false)
			{
				$error = 'Unable to run regular expression on template ' . $key . '. Regex error: ' . preg_last_error_msg();
			}

			// Detect if the template already has the quantitySelectorMinus changes.
			$quantitySelectorMinusMatches = [];
			$quantitySelectorMinusCodeToInject = <<<CTI
			{% if quantity_selection == true %}
CTI;
			//Using same has changes check as "plus"
			if ($quantitySelectorPlusMinusTemplateHasChanges === 0)
			{
				// Template does not have the changes.

				$quantitySelectorMinusMatches = [];
				$quantitySelectorMinusPattern = '/(<button class="quantity__button no-js-hidden" name="minus" type="button">)(.*?)(<\/button>)/is';
				$quantitySelectorMinusFound = preg_match($quantitySelectorMinusPattern, $template, $quantitySelectorMinusMatches);

				if ($quantitySelectorMinusFound === 1)
				{
					// We expect $matches to have 4 parts.
					if (count($quantitySelectorMinusMatches) === 4)
					{
						// Store the current code.
						$origElement = $quantitySelectorMinusMatches[0];

						$newTemplateParts = [
							$quantitySelectorMinusCodeToInject,
							$origElement,
							'{% endif %}'
						];

						$newTemplate = preg_replace($quantitySelectorMinusPattern, implode("\n", $newTemplateParts), $template);
						$template = $newTemplate;
					}
					else
					{
						if ($quantitySelectorMinusFound === 0)
						{
							$error = 'Quantity Minus Button not found in template: ' . $key;
						}
						else
						{
							$error = 'Unable to inject code into template: ' . $key;
						}
					}
				}
				else
				{
					$error = 'Unable to run regular expression on template ' . $key . '. Regex error: ' . preg_last_error_msg();
				}
			}
			else if ($quantitySelectorPlusMinusTemplateHasChanges === false)
			{
				$error = 'Unable to run regular expression on template ' . $key . '. Regex error: ' . preg_last_error_msg();
			}

			// Detect if the template already has the quantitySelectorInput changes.
			$quantitySelectorInputMatches = [];
			$quantitySelectorInputCodeToCheck = <<<CTI
			{% unless quantity_selection == true %}
CTI;

			$quantitySelectorInputCodeToInject = <<<CTI
			{% unless quantity_selection == true %}
				readonly
			{% endunless %}
CTI;

			$quantitySelectorInputTemplateHasChanges = preg_match('/' . trim($quantitySelectorInputCodeToCheck) . '/is', $template, $quantitySelectorInputMatches);

			if ($quantitySelectorInputTemplateHasChanges === 0)
			{
				// Template does not have the changes.
				$quantitySelectorInputMatches = [];
				$quantitySelectorInputPattern = '/(<input class="quantity__input")(.*?)(>)/is';
				$quantitySelectorInputFound = preg_match($quantitySelectorInputPattern, $template, $quantitySelectorInputMatches);

				if ($quantitySelectorInputFound === 1)
				{
					// We expect $matches to have 4 parts.
					if (count($quantitySelectorInputMatches) === 4)
					{
						// Store the current code.
						$origElement = $quantitySelectorInputMatches[0];
						$pos = strrpos($origElement, '>');
						$newElement = substr_replace($origElement, $quantitySelectorInputCodeToInject, $pos-1, 0);
			
						$newTemplateParts = [
							$newElement
						];

						$newTemplate = preg_replace($quantitySelectorInputPattern, implode("\n", $newTemplateParts), $template);
						$template = $newTemplate;
					}
					else
					{
						if ($quantitySelectorInputFound === 0)
						{
							$error = 'Quantity Input Field not found in template: ' . $key;
						}
						else
						{
							$error = 'Unable to inject code into template: ' . $key;
						}
					}
				}
				else
				{
					$error = 'Unable to run regular expression on template ' . $key . '. Regex error: ' . preg_last_error_msg();
				}
			}
			else if ($quantitySelectorInputTemplateHasChanges === false)
			{
				$error = 'Unable to run regular expression on template ' . $key . '. Regex error: ' . preg_last_error_msg();
			}

			// Detect if the template already has the quantitySelectorInput changes.
			$quantitySelectionMatches = [];
			$quantitySelectionCodeToCheck = <<<CTI
			{% assign quantity_selection = true %}
CTI;

			$quantitySelectionCodeToInject = <<<CTI
			{% assign quantity_selection = true %}
			{%- if item.properties.__taopix_project_quantityprotected -%}
				{% assign quantity_selection = false %}
			{%- endif -%}
CTI;

			$quantitySelectionTemplateHasChanges = preg_match('/' . trim($quantitySelectionCodeToCheck) . '/is', $template, $quantitySelectionMatches);

			if ($quantitySelectionTemplateHasChanges === 0)
			{
				// Template does not have the changes.
				$quantitySelectionMatches = [];
				$quantitySelectionPattern = '/(<td class="cart-item__quantity">)(.*?)(<\/td>)/is';
				$quantitySelectionFound = preg_match($quantitySelectionPattern, $template, $quantitySelectionMatches);

				if ($quantitySelectionFound === 1)
				{
					// We expect $matches to have 4 parts.
					if (count($quantitySelectionMatches) === 4)
					{
						// Store the current code.
						$origElement = $quantitySelectionMatches[0];
			
						$newTemplateParts = [
							$quantitySelectionCodeToInject,
							$origElement
						];

						$newTemplate = preg_replace($quantitySelectionPattern, implode("\n", $newTemplateParts), $template);
						$template = $newTemplate;
					}
					else
					{
						if ($quantitySelectionFound === 0)
						{
							$error = 'Quantity Selection Variable not found in template: ' . $key;
						}
						else
						{
							$error = 'Unable to inject code into template: ' . $key;
						}
					}
				}
				else
				{
					$error = 'Unable to run regular expression on template ' . $key . '. Regex error: ' . preg_last_error_msg();
				}
			}
			else if ($quantitySelectionTemplateHasChanges === false)
			{
				$error = 'Unable to run regular expression on template ' . $key . '. Regex error: ' . preg_last_error_msg();
			}

			if (($cartTitleLinkFound === 1) || ($titleLinkFound === 1) || ($quantitySelectorPlusFound === 1) || ($quantitySelectorMinusFound === 1) || ($quantitySelectorInputFound === 1) || ($quantitySelectionFound === 1) )
			{
				$pAsset->pushAsset($key, $template);
			}
		}
		catch(PHPShopifyCurlException $error)
		{
			$error = $key . ' not found for installed theme';
		}

		if ($error !== '')
		{
			throw new ThemeException($error);
		}
	}

	/**
	 * Injects Taopix changes into the product-template.liquid template file.
	 *
	 * @throws ThemeException If unable to apply the modification, target replacement may not exist for example.
	 * @param Asset $pAsset Asset instance.
	 */
	private function injectProductTemplateChanges(Asset $pAsset): void
	{
		$error = '';
		$key = 'sections/main-product.liquid';

		try
		{
			$formFound = 0;
			$quantitySelectorFound = 0;
			$shareSelectorFound = 0;
			$variantPickerSelectorFound = 0;

			$assetEntity = $pAsset->requestAsset($key);
			$template = $assetEntity->getValue();
			$codeToTest = <<<CTI
			{%- render 'tpx-product' product: product
CTI;

			$codeToInject = <<<CTI
			{%- if product.tags contains 'taopix' -%}
                  {% for tpxblock in section.blocks %}
                  	{% if tpxblock.type == 'variant_picker' %}
                  		{% assign theBlock = tpxblock %}
                  	{% endif %}
                  {% endfor %}
					{%- render 'tpx-product' product: product, block: theBlock -%}
                {%- else -%}
                  {%- if product.tags contains 'taopix_hidden_product' -%}             
             		{% comment %}Don't allow access to buy from temp product{% endcomment %}
                  {%- else -%}
CTI;

			// Detect if the template already has the changes.
			$matches = [];
			$templateHasChanges = preg_match('/' . trim($codeToTest) . '/is', $template, $matches);

			if ($templateHasChanges === 0)
			{
				// Template does not have the changes.

				// Locate the form to modify.
				$matches = [];
				$formFound = preg_match('/(\{%- form \'product\', product, id: product_form_id)(...*?)(\{%- endform -%\})/is', $template, $matches);
				if ($formFound === 1)
				{
					// We expect $matches to have 4 parts.
					if (count($matches) === 4)
					{
						// Store the current form code.
						$origForm = $matches[0];

						$newTemplateParts = [
							$codeToInject,
							$origForm,
							'{%- endif -%}',
							'{%- endif -%}'
						];

						$newTemplate = preg_replace('/(\{%- form \'product\', product, id: product_form_id)(...*?)(\{%- endform -%\})/is', implode("\n", $newTemplateParts), $template);
						$template = $newTemplate;
					}
					else
					{
						if ($formFound === 0)
						{
							$error = 'Product form not found in template: ' . $key;
						}
						else
						{
							$error = 'Unable to inject code into template: ' . $key;
						}
					}
				}
				else
				{
					$error = 'Unable to run regular expression on template ' . $key . '. Regex error: ' . preg_last_error_msg();
				}
			}
			else if ($templateHasChanges === false)
			{
				$error = 'Unable to run regular expression on template ' . $key . '. Regex error: ' . preg_last_error_msg();
			}

			$codeToInjectQuantity = <<<CTI
			{% unless product.tags contains 'taopix' or product.tags contains 'taopix_hidden_product' %}
CTI;

			// Detect if the template already has the changes.
			$matches = [];
			$templateHasChanges = preg_match('/' . trim($codeToInjectQuantity) . '/is', $template, $matches);

			if ($templateHasChanges === 0)
			{
				// Template does not have the changes.

				// Locate the form to modify.
				$matches = [];
				$quantitySelectorFound = preg_match('/(<div class="product-form__input product-form__quantity)(...*?)(<\/div>)/is', $template, $matches);
				if ($quantitySelectorFound === 1)
				{
					// We expect $matches to have 4 parts.
					if (count($matches) === 4)
					{
						// Store the current code.
						$orig = $matches[0];

						$newTemplateParts = [
							$codeToInjectQuantity,
							$orig,
							'{%- endunless -%}'
						];

						$newTemplate = preg_replace('/(<div class="product-form__input product-form__quantity)(...*?)(<\/div>)/is', implode("\n", $newTemplateParts), $template);
						$template = $newTemplate;
					}
					else
					{
						if ($quantitySelectorFound === 0)
						{
							$error = 'Quantity section not found in template: ' . $key;
						}
						else
						{
							$error = 'Unable to inject code into template: ' . $key;
						}
					}
				}
				else
				{
					$error = 'Unable to run regular expression on template ' . $key . '. Regex error: ' . preg_last_error_msg();
				}
			}
			else if ($templateHasChanges === false)
			{
				$error = 'Unable to run regular expression on template ' . $key . '. Regex error: ' . preg_last_error_msg();
			}

			$codeToInjectShare = <<<CTI
			{% unless product.tags contains 'taopix_hidden_product' %}
CTI;

			// Detect if the template already has the changes.
			$matches = [];
			$templateHasChanges = preg_match('/' . trim($codeToInjectShare) . '/is', $template, $matches);

			if ($templateHasChanges === 0)
			{
				// Template does not have the changes.

				// Locate the share section to modify.
				$matches = [];
				$shareSelectorFound = preg_match('/(<share-button)(...*?)(<\/share-button>)/is', $template, $matches);
				if ($shareSelectorFound === 1)
				{
					// We expect $matches to have 4 parts.
					if (count($matches) === 4)
					{
						// Store the current code.
						$orig = $matches[0];

						$newTemplateParts = [
							$codeToInjectShare,
							$orig,
							'{%- endunless -%}'
						];

						$newTemplate = preg_replace('/(<share-button)(...*?)(<\/share-button>)/is', implode("\n", $newTemplateParts), $template);
						$template = $newTemplate;
					}
					else
					{
						if ($shareSelectorFound === 0)
						{
							$error = 'Share section not found in template: ' . $key;
						}
						else
						{
							$error = 'Unable to inject code into template: ' . $key;
						}
					}
				}
				else
				{
					$error = 'Unable to run regular expression on template ' . $key . '. Regex error: ' . preg_last_error_msg();
				}
			}
			else if ($templateHasChanges === false)
			{
				$error = 'Unable to run regular expression on template ' . $key . '. Regex error: ' . preg_last_error_msg();
			}

			$codeToInjectVariantPicker = <<<CTI
			{% unless product.tags contains 'taopix' %}
CTI;

			// Detect if the template already has the changes.
			$matches = [];
			$templateHasChanges = preg_match('/' . trim($codeToInjectVariantPicker) . '/is', $template, $matches);

			if ($templateHasChanges === 0)
			{
				// Template does not have the changes.

				// Locate the variant picker section to modify.
				$matches = [];
				$variantPickerSelectorFound = preg_match('/(\{%- unless product.has_only_default_variant -%\})(...*?)(\{%- endunless -%\})/is', $template, $matches);
				if ($variantPickerSelectorFound === 1)
				{
					// We expect $matches to have 4 parts.
					if (count($matches) === 4)
					{
						// Store the current code.
						$orig = $matches[0];

						$newTemplateParts = [
							$codeToInjectVariantPicker,
							$orig,
							'{%- endunless -%}'
						];

						$newTemplate = preg_replace('/(\{%- unless product.has_only_default_variant -%\})(...*?)(\{%- endunless -%\})/is', implode("\n", $newTemplateParts), $template);
						$template = $newTemplate;
					}
					else
					{
						if ($variantPickerSelectorFound === 0)
						{
							$error = 'Variant Picker section not found in template: ' . $key;
						}
						else
						{
							$error = 'Unable to inject code into template: ' . $key;
						}
					}
				}
				else
				{
					$error = 'Unable to run regular expression on template ' . $key . '. Regex error: ' . preg_last_error_msg();
				}
			}
			else if ($templateHasChanges === false)
			{
				$error = 'Unable to run regular expression on template ' . $key . '. Regex error: ' . preg_last_error_msg();
			}

			if (($formFound === 1) || ($quantitySelectorFound === 1) || ($shareSelectorFound === 1) || ($variantPickerSelectorFound === 1))
			{
				$pAsset->pushAsset($key, $template);
			}
		}
		catch(PHPShopifyCurlException $error)
		{
			$error = $key . ' not found for installed theme';
		}

		if ($error !== '')
		{
			throw new ThemeException($error);
		}
	}

	/**
	 * Injects Taopix changes into the customers/account.liquid template file.
	 *
	 * @throws ThemeException If unable to apply the modification, target replacement may not exist for example.
	 * @param Asset $pAsset Asset instance.
	 */
	private function injectCustomersAccountTemplateChanges(Asset $pAsset): void
	{
		$error = '';
		$key = 'templates/customers/account.liquid';

		try
		{
			$assetEntity = $pAsset->requestAsset($key);
			$template = $assetEntity->getValue();
			$codeToInject = <<<CTI
			{% render 'tpx-myprojects' customer: customer %}
CTI;

			// Detect if the template already has the changes.
			$matches = [];
			$templateHasChanges = preg_match('/' . trim($codeToInject) . '/is', $template, $matches);

			if ($templateHasChanges === 0)
			{
				// Add the code to inject before the final closing div
				$pos = strrpos($template, '</div>');
				$newTemplate = substr_replace($template, $codeToInject, $pos, 0);

				$pAsset->pushAsset($key, $newTemplate);
			}
			else if ($templateHasChanges === false)
			{
				$error = 'Unable to run regular expression on template ' . $key . '. Regex error: ' . preg_last_error_msg();
			}
		}
		catch(PHPShopifyCurlException $error)
		{
			$error = $key . ' not found for installed theme';
		}

		if ($error !== '')
		{
			throw new ThemeException($error);
		}
	}

	/**
	 * Injects Taopix changes into the main-collection-product-grid.liquid template file.
	 *
	 * @throws ThemeException If unable to apply the modification, target replacement may not exist for example.
	 * @param Asset $pAsset Asset instance.
	 */
	private function injectProductGridChanges(Asset $pAsset): void
	{
		$error = '';
		$key = 'sections/main-collection-product-grid.liquid';

		try
		{
			$assetEntity = $pAsset->requestAsset($key);
			$template = $assetEntity->getValue();
			$codeToInject = <<<CTI
			{%- unless product.tags contains 'taopix_hidden_product' -%}
CTI;
			$codeToInjectEnd = <<<CTI
			{%- endunless -%}
CTI;

			// Detect if the template already has the changes.
			$matches = [];
			$templateHasChanges = preg_match('/' . trim($codeToInject) . '/is', $template, $matches);

			if ($templateHasChanges === 0)
			{
				// Add the code to inject before the grid__item li
				$pos = strrpos($template, '<li class="grid__item">');
				$newTemplate = substr_replace($template, $codeToInject, $pos, 0);

				// Add the code to close the unless
				$pos = strrpos($newTemplate, '</li>');
				$newTemplate = substr_replace($newTemplate, $codeToInjectEnd, $pos+5, 0);

				$pAsset->pushAsset($key, $newTemplate);
			}
			else if ($templateHasChanges === false)
			{
				$error = 'Unable to run regular expression on template ' . $key . '. Regex error: ' . preg_last_error_msg();
			}
		}
		catch(PHPShopifyCurlException $error)
		{
			$error = $key . ' not found for installed theme';
		}

		if ($error !== '')
		{
			throw new ThemeException($error);
		}
	}

	/**
	 * Injects Taopix changes into the main-search.liquid template file.
	 *
	 * @throws ThemeException If unable to apply the modification, target replacement may not exist for example.
	 * @param Asset $pAsset Asset instance.
	 */
	private function injectSearchChanges(Asset $pAsset): void
	{
		$error = '';
		$key = 'sections/main-search.liquid';

		try
		{
			$assetEntity = $pAsset->requestAsset($key);
			$template = $assetEntity->getValue();
			$codeToInject = <<<CTI
			{%- unless item.tags contains 'taopix_hidden_product' -%}
CTI;
			$codeToInjectEnd = <<<CTI
			{%- endunless -%}
CTI;

			// Detect if the template already has the changes.
			$matches = [];
			$templateHasChanges = preg_match('/' . trim($codeToInject) . '/is', $template, $matches);

			if ($templateHasChanges === 0)
			{
				// Add the code to inject before the grid__item li
				$pos = strrpos($template, '<li class="grid__item');
				$newTemplate = substr_replace($template, $codeToInject, $pos, 0);

				// Add the code to close the unless
				$pos = strrpos($newTemplate, '</li>');
				$newTemplate = substr_replace($newTemplate, $codeToInjectEnd, $pos+5, 0);

				$pAsset->pushAsset($key, $newTemplate);
			}
			else if ($templateHasChanges === false)
			{
				$error = 'Unable to run regular expression on template ' . $key . '. Regex error: ' . preg_last_error_msg();
			}
		}
		catch(PHPShopifyCurlException $error)
		{
			$error = $key . ' not found for installed theme';
		}

		if ($error !== '')
		{
			throw new ThemeException($error);
		}
	}

	/**
	 * Injects Taopix changes into the predictive-search.liquid template file.
	 *
	 * @throws ThemeException If unable to apply the modification, target replacement may not exist for example.
	 * @param Asset $pAsset Asset instance.
	 */
	private function injectPredictiveSearchChanges(Asset $pAsset): void
	{
		$error = '';
		$key = 'sections/predictive-search.liquid';

		try
		{
			$assetEntity = $pAsset->requestAsset($key);
			$template = $assetEntity->getValue();
			$codeToInject = <<<CTI
			{%- unless product.tags contains 'taopix_hidden_product' -%}
CTI;
			$codeToInjectEnd = <<<CTI
			{%- endunless -%}
CTI;

			// Detect if the template already has the changes.
			$matches = [];
			$templateHasChanges = preg_match('/' . trim($codeToInject) . '/is', $template, $matches);

			if ($templateHasChanges === 0)
			{
				// Add the code to inject before the li
				$pos = strrpos($template, '<li id="predictive-search-option-{{ forloop.index }}"');
				$newTemplate = substr_replace($template, $codeToInject, $pos, 0);

				// Add the code to close the unless before the endfor
				$pos = strrpos($newTemplate, '{%- endfor -%}');
				$newTemplate = substr_replace($newTemplate, $codeToInjectEnd, $pos, 0);

				$pAsset->pushAsset($key, $newTemplate);
			}
			else if ($templateHasChanges === false)
			{
				$error = 'Unable to run regular expression on template ' . $key . '. Regex error: ' . preg_last_error_msg();
			}
		}
		catch(PHPShopifyCurlException $error)
		{
			$error = $key . ' not found for installed theme';
		}

		if ($error !== '')
		{
			throw new ThemeException($error);
		}
	}

	/**
	 * Adds Taopix changes into the price.liquid theme file.
	 *
	 * @throws ThemeException If unable to apply the modification, target replacement may not exist for example.
	 * @param Asset $pAsset Asset instance.
	 */
	private function injectPriceChanges(Asset $pAsset): void
	{
		$error = '';
		$key = 'snippets/price.liquid';

		try 
		{
			$found = 0;
			$assetEntity = $pAsset->requestAsset($key);
			$template = $assetEntity->getValue();
			$codeToTest = '<span id="thePrice" class="price-item price-item--regular">';
			$codeToInject = <<<CTI
			<span id="thePrice" class="price-item price-item--regular">
				{{ money_price }}
			</span>
CTI;
			// Detect if the template already has the changes.
			$matches = [];
			$templateHasChanges = preg_match('/' . trim($codeToTest) . '/is', $template, $matches);

			if ($templateHasChanges === 0)
			{
				// Template does not have the changes.

				$matches = [];
				$pattern = '/(<span class="price-item price-item--regular">)(...*?)(<\/span>)/is';
				$found = preg_match($pattern, $template, $matches);

				if ($found === 1)
				{
					// We expect $matches to have 4 parts.
					if (count($matches) === 4)
					{
						$newTemplate = preg_replace($pattern, $codeToInject, $template);
						$pAsset->pushAsset($key, $newTemplate);
					}
					else
					{
						if ($found === 0)
						{
							$error = 'Price span not found in template: ' . $key;
						}
						else
						{
							$error = 'Unable to inject code into template: ' . $key;
						}
					}
				}
				else
				{
					$error = 'Unable to run regular expression on template ' . $key . '. Regex error: ' . preg_last_error_msg();
				}
			}
			else if ($templateHasChanges === false)
			{
				$error = 'Unable to run regular expression on template ' . $key . '. Regex error: ' . preg_last_error_msg();
			}
		}
		catch(PHPShopifyCurlException $error)
		{
			$error = $key . ' not found for installed theme';
		}

		if ($error !== '')
		{
			throw new ThemeException($error);
		}
	}

	/**
	 * Injects the Taopix code into the global.js theme file.
	 *
	 * @throws ThemeException If unable to apply the modification, target replacement may not exist for example.
	 * @param Asset $pAsset Asset instance.
	 */
	private function injectGlobalJSChanges(Asset $pAsset, ThemeEntity $pTheme): void
	{
		$error = '';
		$key = 'assets/global.js';

		try 
		{
			$updateOptionsFound = 0;
			$updateMasterIdFound = 0;
			$assetEntity = $pAsset->requestAsset($key);
			$template = $assetEntity->getValue();
			$codeToTest = 'this.options = Array.from(this.querySelectorAll(\'select\'), (select) => select.options[select.selectedIndex].text)';
			$codeToInject = <<<CTI
updateOptions() {
				this.options = Array.from(this.querySelectorAll('select'), (select) => select.options[select.selectedIndex].text);
			}
CTI;

			// Detect if the template already has the changes.
			$matches = [];
			$templateHasChanges = preg_match('/' . trim($codeToTest) . '/is', $template, $matches);

			if ($templateHasChanges === 0)
			{
				// Template does not have the changes.

				$matches = [];
				$pattern = '/(updateOptions\(\) \{)(...*?)(\})/is';
				$updateOptionsFound = preg_match($pattern, $template, $matches);

				if ($updateOptionsFound === 1)
				{
					// We expect $matches to have 4 parts.
					if (count($matches) === 4)
					{
						$newTemplate = preg_replace($pattern, $codeToInject, $template, 1);
						$template = $newTemplate;
					}
					else
					{
						if ($updateOptionsFound === 0)
						{
							$error = 'updateOptions function not found in template: ' . $key;
						}
						else
						{
							$error = 'Unable to inject code into template: ' . $key;
						}
					}
				}
				else
				{
					$error = 'Unable to run regular expression on template ' . $key . '. Regex error: ' . preg_last_error_msg();
				}
			}
			else if ($templateHasChanges === false)
			{
				$error = 'Unable to run regular expression on template ' . $key . '. Regex error: ' . preg_last_error_msg();
			}

			// Detect if the template already has the title changes.
			$updateMasterIdCodeToInject = <<<CTI
updateMasterId() {
				this.currentVariant = this.getVariantData().find((variant) => {
				  return !variant.options.map((option, index) => {
					
					let found = false;
					if (option.includes('(Layout Code:')) {
					  found = option.includes((this.options[index].split("/"))[0]);
					  if (!found) {
						if ((this.options[index].split("."))[1]) {
							   found = option.includes((this.options[index].split("."))[1]);
						}
					  }
					}         
					else 
					{
					  found = this.options[index] === option;
					  if (!found) {
						if (this.options[0]) {
						  if (this.options[0].includes(option)) {
							  found = true;
						  }
						}               
					  }
					}
					
					return found;
				  }).includes(false);
				});
			}
CTI;
			$matches = [];
			$masterIdPattern = '/(updateMasterId\(\)\s*\{)(...*?)(\}\).includes\(false\)\;\s*\}\);\s*\})/is';
			$updateMasterIdFound = preg_match($masterIdPattern, $template, $matches);

			if ($updateMasterIdFound === 1)
			{
				// We expect $matches to have 4 parts.
				if (count($matches) === 4)
				{
					$newTemplate = preg_replace($masterIdPattern, $updateMasterIdCodeToInject, $template);
					$template = $newTemplate;
				}
				else
				{
					if ($updateMasterIdFound === 0)
					{
						$error = 'updateMasterId function not found in template: ' . $key;
					}
					else
					{
						$error = 'Unable to inject code into template: ' . $key;
					}
				}
			}
			else
			{
				$error = 'Unable to run regular expression on template ' . $key . '. Regex error: ' . preg_last_error_msg();
			}

			if (($updateOptionsFound === 1) || ($updateMasterIdFound === 1))
			{
				$pAsset->pushAsset($key, $template);
			}
		}
		catch(PHPShopifyCurlException $error)
		{
			$error = $key . ' not found for installed theme';
		}

		if ($error !== '')
		{
			throw new ThemeException($error);
		}
	}
	
	/**
	 * Injects the Taopix code into the facets.liquid theme file.
	 *
	 * @throws ThemeException If unable to apply the modification, target replacement may not exist for example.
	 * @param Asset $pAsset Asset instance.
	 */
	private function injectFacetChanges(Asset $pAsset, ThemeEntity $pTheme): void
	{
		$error = '';
		$key = 'snippets/facets.liquid';

		try 
		{
			$found = 0;
			$assetEntity = $pAsset->requestAsset($key);
			$template = $assetEntity->getValue();
			$codeToTest = '<h2 class="product-count__text text-body">';

			// Detect if the template still has the tag we want to remove
			$matches = [];
			$templateHasChanges = preg_match('/' . trim($codeToTest) . '/is', $template, $matches);

			if ($templateHasChanges === 1)
			{
				// Template needs to be updated
				$matches = [];
				$pattern = '/(<h2 class="product-count__text text-body">)(...*?)(<\/h2>)/is';
				$found = preg_match($pattern, $template, $matches);

				if ($found === 1)
				{
					// We expect $matches to have 4 parts.
					if (count($matches) === 4)
					{
						$newTemplate = preg_replace($pattern, '', $template);
						$pAsset->pushAsset($key, $newTemplate);
					}
					else
					{
						if ($found === 0)
						{
							$error = 'Product count h2 tag not found in template: ' . $key;
						}
						else
						{
							$error = 'Unable to inject code into template: ' . $key;
						}
					}
				}
				else
				{
					$error = 'Unable to run regular expression on template ' . $key . '. Regex error: ' . preg_last_error_msg();
				}
			}
			else if ($templateHasChanges === false)
			{
				$error = 'Unable to run regular expression on template ' . $key . '. Regex error: ' . preg_last_error_msg();
			}
		}
		catch(PHPShopifyCurlException $error)
		{
			$error = $key . ' not found for installed theme';
		}

		if ($error !== '')
		{
			throw new ThemeException($error);
		}
	}

	/**
	 * Adds Taopix changes into the product-form.js theme file.
	 *
	 * @throws ThemeException If unable to apply the modification, target replacement may not exist for example.
	 * @param Asset $pAsset Asset instance.
	 */
	private function injectProductFormChanges(Asset $pAsset): void
	{
		$error = '';
		$key = 'assets/product-form.js';

		try 
		{
			$found = 0;
			$assetEntity = $pAsset->requestAsset($key);
			$template = $assetEntity->getValue();
			$codeToInject = "if (this.form.querySelector('[name=id]')) this.form.querySelector('[name=id]').disabled = false;";
			$codeToTest = "if \(this.form.querySelector\(\'\[name=id\]\'\)\) this.form.querySelector\(\'\[name=id\]\'\).disabled = false\;";

			// Detect if the template already has the changes.
			$matches = [];
			$templateHasChanges = preg_match('/' . trim($codeToTest) . '/is', $template, $matches);

			if ($templateHasChanges === 0)
			{
				// Template does not have the changes
				$matches = [];
				$pattern = '/(this.form.querySelector\(\'\[name=id\]\'\).disabled = false\;)/is';
				$found = preg_match($pattern, $template, $matches);

				if ($found === 1)
				{
					// We expect $matches to have 2 parts.
					if (count($matches) === 2)
					{
						$newTemplate = preg_replace($pattern, $codeToInject, $template);
						$pAsset->pushAsset($key, $newTemplate);
					}
					else
					{
						if ($found === 0)
						{
							$error = 'JS not found in template: ' . $key;
						}
						else
						{
							$error = 'Unable to inject code into template: ' . $key . ' :: Regex error: ' . preg_last_error_msg();
						}
					}
				}
				else
				{
					$error = 'Unable to run regular expression on template ' . $key . '. Regex error: ' . preg_last_error_msg();
				}
			}
			else if ($templateHasChanges === false)
			{
				$error = 'Unable to run regular expression on template ' . $key . '. Regex error: ' . preg_last_error_msg();
			}
		}
		catch(PHPShopifyCurlException $error)
		{
			$error = $key . ' not found for installed theme';
		}

		if ($error !== '')
		{
			throw new ThemeException($error);
		}
	}

	/**
	 * Inject translations into Shopify.
	 * Defaults to English if the Shopify language is not supported by Taopix.
	 *
	 * @param Asset $pAsset Asset instance
	 */
	public function injectTranslations(Asset $pAsset): void
	{
		// Get all the assets under "locales" from the Shopify template.
		$localeAssets = $this->getLocaleAssets($pAsset);
		
		if (count($localeAssets) > 0)
		{
			foreach ($localeAssets as $locale)
			{
				$localeKey = $locale->getKey();

				if (strpos($localeKey, 'schema') == false)
				{
					// Request the individual template.
					$asset = $pAsset->requestAsset($localeKey);

					// Read the language code from the key.
					$matched = preg_match('/locales\/([\w-]+)(\.default)?\.json/i', $localeKey, $langMatches);

					// Default to English if a match could not be made.
					$langCode = 'en';

					if ($matched === 1)
					{
						// Match our internal language code.
						$langCode = $this->reMapLangCode(strtolower(str_replace('-', '_', $langMatches[1])));
					}

					$smarty = ($this->getUtils())->newSmartyObj('Connectors', '', '', $langCode);

					// Convert template to an array.
					$languageArray = json_decode($asset->getValue(), true);

					// Inject the new language strings.
					$languageArray['taopix'] = [
						'create_now' => $smarty->get_config_vars('str_CreateNow'),
						'edit_project' => $smarty->get_config_vars('str_EditProject'),
						'duplicate_project' => $smarty->get_config_vars('str_DuplicateProject'),
						'variant_label' => $smarty->get_config_vars('str_VariantLabel'),
						'my_projects' => $smarty->get_config_vars('str_MyProjects'),
						'manage_personalised_products' => $smarty->get_config_vars('str_ManageYourPersonalisedProducts'),
						'price_from' => $smarty->get_config_vars('str_PriceFrom')
					];

					// Push asset back to Shopify.
					$pAsset->pushAsset($localeKey, json_encode($languageArray));
				}
			}
		}
	}

	/**
	 * Returns an asset collection containing locale template files.
	 *
	 * @param Asset $pAsset Asset instance.
	 * @return AssetCollection Collection of loacles template files.
	 */
	private function getLocaleAssets(Asset $pAsset): AssetCollection
	{
		$assetCollection = $pAsset->requestAssets();
		return $assetCollection->getAssetsByKeyPrefix('locales/');
	}

	/**
	 * Maps Shopify languages codes to Taopix.
	 *
	 * @param string $pLangCode Language code to remap.
	 * @return string Remapped language code.
	 */
	private function reMapLangCode(string $pLangCode): string
	{
		$langCode = $pLangCode;
		$langMap = [
			'pt_br' => 'pt',
			'pt_pt' => 'pt'
		];

		if (array_key_exists($langCode, $langMap))
		{
			$langCode = $langMap[$langCode];
		}

		return $langCode;
	}

	/**
	 * Pushes Taopix Customised Dawn Theme unpublished
	 */
	public function pushTaopixTheme(): void
	{
		$utils = $this->getUtils();
		$ac_config = $utils->getACConfig();

		//Check if a Taopix Dawn Theme Already Exists
		$themesByName = $this->themeCollection->getThemeByName($this->getTPXThemeName());
		if (count($themesByName) > 0)
		{
			$this->addApplyThemeError('EXISTS');
		}
		else
		{
			$resourcesZipFilePath = $utils->correctPath($ac_config['CONNECTORRESOURCESPATH'] , DIRECTORY_SEPARATOR, true);
			$utils->createAllFolders($resourcesZipFilePath);
			$resourcesZipFilePath .= $this->getTPXThemeZipName();
			
			$srcZipFilePath = __DIR__ . DIRECTORY_SEPARATOR . 'Themes' . DIRECTORY_SEPARATOR . $this->getTPXThemeZipName();
			
			if (file_exists($srcZipFilePath)) 
			{
				copy($srcZipFilePath, $resourcesZipFilePath);
				
				$themeData = 
				[
					"name" => $this->getTPXThemeName(),
					"src" => $this->getControlCentreURL() . '/connectors/' . $this->getTPXThemeZipName(),
					"role" => "unpublished"
				];

				try 
				{            
					$this->shopifySDK->Theme->post($themeData);
					$utils->deleteFile($resourcesZipFilePath);
				}
				catch (ThemeException $pError)
				{
					$this->addApplyThemeError($pError->getMessage());
				}
			} 
			else
			{
				$this->addApplyThemeError('Theme files not present');
			}
		}
	}
}
