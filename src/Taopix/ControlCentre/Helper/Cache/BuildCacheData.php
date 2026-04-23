<?php

namespace Taopix\ControlCentre\Helper\Cache;

use DateTime;
use DateTimeZone;
use Doctrine\Persistence\ManagerRegistry;
use Taopix\ControlCentre\Entity\CacheData;
use Taopix\ControlCentre\Entity\Component;
use Taopix\ControlCentre\Entity\ComponentCategory;
use Taopix\ControlCentre\Entity\Currency;
use Taopix\ControlCentre\Entity\Price;
use Taopix\ControlCentre\Entity\PriceLink;
use Taopix\ControlCentre\Entity\Product;
use Taopix\ControlCentre\Entity\TaxCode;
use Taopix\ControlCentre\Entity\LicenseKey;
use Taopix\ControlCentre\Entity\Brand;
use Taopix\ControlCentre\Entity\TaxZone;
use Taopix\ControlCentre\Enum\Product\Type;
use function array_column;
use function array_diff;
use function array_filter;
use function array_keys;
use function array_map;
use function array_values;
use function array_unique;
use function array_key_exists;
use function explode;
use function implode;
use function in_array;
use function str_ends_with;
use function uasort;

class BuildCacheData
{
	/** @var Currency|null  */
	private ?Currency $currency = null;

	/** @var TaxCode[] */
	private array $taxCodes = [];

	private ?TaxZone $taxZone = null;

	private ?CacheData $currentCache = null;

	private array $priceLists = [
		'product' => [],
		'component' => [],
	];

	private array $linkedProductTrees = [];
	private array $componentTree = [];
	private array $componentListForDefaultPrices = [];

	/** @var Price[] */
	private array $prices = [];

	/** @var Price[] */
	private array $defaultPrices = [];

	/** @var Component[] */
	private array $components = [];

	/** @var ComponentCategory[] */
	private array $componentCategories = [];

	private array $priceListIds = [];


	public function __construct(private readonly ManagerRegistry $doctrine, private LicenseKey $licenseKey, private Brand $brand, private readonly array $config)
	{
		$currencyCode = $this->licenseKey->isUseDefaultCurrency() ? $config['defaultCurrencyCode'] : $this->licenseKey->getCurrencyCode();
		$this->currency = $this->doctrine->getRepository(Currency::class)->findOneBy(['code' => $currencyCode]);

		$this->taxZone = $this->doctrine->getRepository(TaxZone::class)->getTaxZone(array_unique([$this->brand->getCompanyCode(), '']),
			$this->licenseKey->getCountryCode(),
			$this->licenseKey->getRegionCode(),
		);
	}

	/**
	 * Build the cache based on the passed options.
	 *
	 * @param array $options
	 * @return CacheData
	 */
	public function buildCache(array $options): array
	{
		$this->clearData();

		$this->getCurrentCache($options['collectionCode']);

		// Get all items for this collectionCode and companyCode
		$items = $this->doctrine->getRepository(Product::class)->getAutoUpdateProductList([
			'companyCode' => $this->brand->getCompanyCode(),
			'collectionCode' => $options['collectionCode'],
			'active' => 1,
			'createNewProjects' => 1,
			'deleted' => 0,
		]);
		$this->populateComponentLinkedAndPriceArrays([
			'productCodes' => \array_column($items, 'code'),
			'groupCodes' => [$this->licenseKey->getGroupCode(), '']
		]);
		if (!empty($this->componentListForDefaultPrices)) {
			$this->getDefaultPrices();
		}
		$this->getAllPrices();
		$this->getAllComponents();

		return $this->buildCacheData($items);
	}

	private function buildCacheData(array $items): array
	{
		$structure = [
			'result' => '',
			'resultparam' => '',
			'productlist' => []
		];

		foreach ($items as $key => $productDetails) {
            // Layout not priced for this key.
            if (!\array_key_exists($productDetails['code'], $this->priceLists['product'])) {
                continue;
            }

			$priceSubKey = \array_key_exists($this->licenseKey->getGroupCode(), $this->priceLists['product'][$productDetails['code']]) ? $this->licenseKey->getGroupCode() : 'global';
			$price = $this->getPriceData($this->priceLists['product'][$productDetails['code']][$priceSubKey]['id']);

			$configKey = \array_key_exists($productDetails['code'], $this->linkedProductTrees) ? $this->linkedProductTrees[$productDetails['code']] : $productDetails['code'];
			$componentConfig = $this->componentTree[$configKey] ?? [];
            $taxRate = $this->getTaxCode('' === $this->licenseKey->getTaxCode() ? $this->taxZone->getTaxLevelCode($productDetails['taxLevel']) : $this->licenseKey->getTaxCode());
			$structure['productlist'][] = [
				'id' => $productDetails['id'],
				'collectioncode' => $productDetails['collectionCode'],
				'code' => $productDetails['code'],
				'type' => $productDetails['collectionType'],
				'pricingmodel' => $price->getPricingModel(),
				'price' => $price->getPrice(),
				'pricedescription' => $this->priceLists['product'][$productDetails['code']][$priceSubKey]['description'],
				'pricetaxrate' => $this->getTaxCode($price->getTaxCode())?->getRate() ?? '0.00', // This gets the tax code if we return null we use 0.00 as the price tax rate.
				'qtyisdropdown' => (int) $price->isQuantityIsDropDown(),
				'productoption' => $productDetails['productOptions'],
				'pricetransformationstage' => $productDetails['priceTransformationStage'],
				'isactive' => ($productDetails['active'] && $price->isActive()),
				'company' => $this->licenseKey->getCompanyCode(),
				'coverlist' => $this->processComponentSection($componentConfig, '$COVER\\', $configKey),
				'paperlist' => $this->processComponentSection($componentConfig, '$PAPER\\', $configKey),
				'singleprintlist' => Type::PHOTO_PRINT->value === $productDetails['collectionType'] ? $this->processComponentSection($componentConfig, '$SINGLEPRINT\\', $configKey) : [],
				'singleprintoptionlist' => Type::PHOTO_PRINT->value === $productDetails['collectionType'] ? $this->processComponentSection($componentConfig, '$SINGLEPRINTOPTION\\', $configKey) : [],
				'calendarcustomisationlist' => $this->processComponentSection($componentConfig, '$CALENDARCUSTOMISATION\\', $configKey),
				'taopixailist' => $this->processComponentSection($componentConfig, '$TAOPIXAI\\', $configKey),
				'taxrate' => $taxRate?->getRate() ?? '0.0000',
				'publishversion' => $productDetails['publishVersion'],
				'collectionthumbnailresourceref' => $productDetails['collectionThumbnailResourceRef'],
				'collectionthumbnailresourcedatauid' => $productDetails['collectionThumbnailResourceDataUid'],
				'collectionpreviewresourceref' =>  $productDetails['collectionPreviewResourceRef'],
				'collectionpreviewresourcedatauid' => $productDetails['collectionPreviewResourceDataUid'],
				'collectionsortlevel' => $productDetails['collectionSortLevel'],
				'collectiontextengineversion' => $productDetails['collectionTextEngineVersion'],
				'producttarget' => $productDetails['productTarget'],
				'productminpagecount' => $productDetails['productMinPageCount'],
				'productaimodedesktop' => $productDetails['productAiModeDesktop'],
				'productselectormodedesktop' => $productDetails['productSelectorModeDesktop']
			];
		}

		$this->currentCache->setCacheArray($structure)
			->setCacheVersion($this->config['cacheVersion']);
		$this->doctrine->getManager()->flush();

		return $structure;
	}

	private function processComponentSection(array $components, string $section, string $productCode): array
	{
		$return = [];
		$list = $components[$section] ?? [];

		// Single print options are formatted in size => components rather than a flat list of components.
		if ('$SINGLEPRINTOPTION\\' === $section) {
			$list = array_filter(
				array_filter($components, function($item) use ($section) {
					return str_ends_with($item, $section);
				}, ARRAY_FILTER_USE_KEY)
			, function($item) { return !empty($item); });

			foreach ($list as $key => $components) {
				$pathParts = explode('\\', $key);
				$return[$pathParts[1]] = $this->processComponents($components, $key, $productCode);
			}
			return $return;
		}

		return $this->processComponents($list, $section, $productCode);
	}

	private function processComponents(array $componentList, string $section, string $productCode): array
	{
		$return = [];
		foreach ($componentList as $key => $componentCode) {
			$component = $this->getComponent($componentCode);
			/** @var ComponentCategory $componentCategory */
			$componentCategory = $this->getComponentCategory($component->getCategoryCode());
			$componentPrices = $this->priceLists['component'][$productCode] ?? [];
			$defaultPrice = $this->defaultPrices[$componentCode] ?? [];

			/*
			 * If there is no component price or default price for this component then it should not be listed.
			 */
			if ((empty($componentPrices) || empty($componentPrices[$section]) || empty($componentPrices[$section][$componentCode])) && empty($defaultPrice)) {
				continue;
			}

			$priceDetails = $componentPrices[$section][$componentCode] ?? $defaultPrice;
			$priceKey = array_key_exists($this->licenseKey->getGroupCode(), $priceDetails) ? $this->licenseKey->getGroupCode() : 'global';

			$price = $this->getPriceData($priceDetails[$priceKey]['id']);
			$formattedPrice = $price->unpackPriceString();

			$return[] = [
				'id' => $component->getId(),
				'code' => $component->getCode(),
				'localcode' => $component->getLocalCode(),
				'name' => $component->getName(),
				'moreinfolinkurl' => $component->getMoreInfoLinkUrl(),
				'moreinfolinktext' => $component->getMoreInfoLinkText(),
				'default' => (int) $priceDetails[$priceKey]['default'],
				'requirespagecount' => (int) $componentCategory->isRequiresPageCount(),
				'minpagecount' => $component->getMinimumPageCount(),
				'maxpagecount' => $component->getMaximumPageCount(),
				'pricingmodel' => $price->getPricingModel(),
				'pricedata' => $price->getPrice(),
				'sell' => $formattedPrice[0]['unitsell'],
				'sortorder' => $priceDetails[$priceKey]['sortOrder'],
				'quantityisdropdown' => (int) $price->isQuantityIsDropDown(),
				'pricetaxcode' => $price->getTaxCode(),
				'pricetaxrate' => $this->getTaxCode($price->getTaxCode())?->getRate() ?? '0.00',
			];
		}

		uasort($return, function($a, $b) { return $a <=> $b; });
		return $return;
	}

	private function getComponent(string $componentCode): Component
	{
		// If this component has not been retrieved do so
		if (!array_key_exists($componentCode, $this->components)) {
			$this->components[$componentCode] = $this->doctrine->getRepository(Component::class)->findOneBy(['code' => $componentCode]);
		}


		return $this->components[$componentCode];
	}

	private function getComponentCategory(string $categoryCode): ComponentCategory
	{
		// If the component category has not been retrieved do so.
		if (!array_key_exists($categoryCode, $this->componentCategories)) {
			$this->componentCategories[$categoryCode] = $this->doctrine->getRepository(ComponentCategory::class)->findOneBy(['code' => $categoryCode]);
		}

		return $this->componentCategories[$categoryCode];
	}

	private function getPriceData(int $priceId): Price
	{
		// Get the price details if we dont have it already.
		if (!\array_key_exists($priceId, $this->prices)) {
			$this->prices[$priceId] = $this->doctrine->getRepository(Price::class)->findOneBy(['id' => $priceId]);
		}

		return $this->prices[$priceId];
	}

	private function getTaxCode(string $taxCode): TaxCode | null
	{
		// get the tax code if we have not done so.
		if (!array_key_exists($taxCode, $this->taxCodes)) {
			$this->taxCodes[$taxCode] = $this->doctrine->getRepository(TaxCode::class)->findOneBy(['code' => $taxCode]);
		}

		return $this->taxCodes[$taxCode];
	}
	private function clearData(): void
	{
		// Clear values as build is called.
		$this->priceLists = [
			'product' => [],
			'component' => [],
		];
		$this->linkedProductTrees = [];
		$this->componentTree = [];
		$this->componentListForDefaultPrices = [];
		$this->prices = [];
		$this->defaultPrices = [];
		$this->components = [];
		$this->componentCategories = [];
		$this->priceListIds = [];
	}

	private function populateComponentLinkedAndPriceArrays(array $options, bool $recursedLookup = false): void
	{
        $componentAndPriceInfo = $this->doctrine->getRepository(PriceLink::class)->getComponentLinkAndPriceInfo($options);
        if ($recursedLookup && 0 === \count($componentAndPriceInfo)) {
            return;
        }

		\array_map(function($item) {
			if ('' !== $item['linkedProductCode']) {
				$this->linkedProductTrees[$item['productCode']] = $item['linkedProductCode'];
			} else {
				$licenseKey = '' === $item['groupCode'] ? 'global' : $item['groupCode'];
				if ('' === $item['componentCode']) {
					if ($item['priceId'] > 0) {
						if (!\array_key_exists($item['productCode'], $this->priceLists['product'])) {
							$this->priceLists['product'][$item['productCode']] = [];
						}
						$this->priceLists['product'][$item['productCode']][$licenseKey] = [
							'id' => $item['priceId'],
							'description' => $item['priceDescription'],
						];

						if (!\in_array($item['priceId'], $this->priceListIds)) {
							$this->priceListIds[] = $item['priceId'];
						}
					}
				} else {
					if ($item['priceId'] > 0) {

						if (!\in_array($item['priceId'], $this->priceListIds)) {
							$this->priceListIds[] = $item['priceId'];
						}
						if (!\array_key_exists($item['productCode'], $this->priceLists['component'])) {
							$this->priceLists['component'][$item['productCode']] = [];
						}
						if (!\array_key_exists($item['parentPath'], $this->priceLists['component'][$item['productCode']])) {
							$this->priceLists['component'][$item['productCode']][$item['parentPath']] = [];
						}
						if (!\array_key_exists($item['componentCode'], $this->priceLists['component'][$item['productCode']][$item['parentPath']])) {
							$this->priceLists['component'][$item['productCode']][$item['parentPath']][$item['componentCode']] = [];
						}
						$this->priceLists['component'][$item['productCode']][$item['parentPath']][$item['componentCode']][$licenseKey] = [
							'id' => $item['priceId'],
							'description' => $item['priceDescription'],
							'inheritParentQty' => $item['inheritParentQty'],
							'sortOrder' => $item['sortOrder'],
							'default' => $item['default'],
						];
					} else {
						if (!\array_key_exists($item['productCode'], $this->componentTree)) {
							$this->componentTree[$item['productCode']] = [];
						}
						if (!\array_key_exists($item['parentPath'], $this->componentTree[$item['productCode']])) {
							$this->componentTree[$item['productCode']][$item['parentPath']] = [];
						}
						$this->componentTree[$item['productCode']][$item['parentPath']][] = $item['componentCode'];
						$this->componentListForDefaultPrices[] = $item['componentCode'];
					}
				}
			}
		}, $componentAndPriceInfo);

		// Work out if we have linked items from another collection, these will be productCodes that have not been passed initially
		$productsFromAltCollections = \array_diff(\array_values($this->linkedProductTrees), \array_keys($this->priceLists['product']));

		// If we have items that are not matched call this function with the updated data.
		if (!empty($productsFromAltCollections) && !$recursedLookup) {
			$this->populateComponentLinkedAndPriceArrays([
				'productCodes' => $productsFromAltCollections,
				'groupCodes' => $options['groupCodes'],
			], true);
		}
	}

	private function getDefaultPrices(): void
	{
		/** @var PriceLink[] $prices */
		$prices = $this->doctrine->getRepository(PriceLink::class)->findBy([
			'productCode' => '',
			'groupCode' => [$this->licenseKey->getGroupCode(), ''],
			'componentCode' => \array_unique($this->componentListForDefaultPrices),
		]);

		\array_map(function($price) {
			if (!\array_key_exists($price->getComponentCode(), $this->defaultPrices)) {
				$this->defaultPrices[$price->getComponentCode()] = [];
			}
			$key = '' === $price->getGroupCode() ? 'global' : $price->getGroupCode();
			$this->defaultPrices[$price->getComponentCode()][$key] = [
				'id' => $price->getPriceId(),
				'description' => $price->getPriceDescription(),
				'inheritParentQty' => $price->getInheritParentQty(),
				'sortOrder' => $price->getSortOrder(),
				'default' => $price->isDefault(),
			];
			if (!in_array($price->getPriceId(), $this->priceListIds)) {
				$this->priceListIds[] = $price->getPriceId();
			}
		}, $prices);
	}

	public function getAllPrices(): void
	{
		$prices = $this->doctrine->getRepository(Price::class)->findBy(['id' => $this->priceListIds]);

		\array_map(function($price) { $this->prices[$price->getId()] = $price; }, $prices);
	}

	public function getAllComponents(): void
	{
		if (empty($this->componentListForDefaultPrices)) {
			return;
		}
		/** @var Component[] $components */
		$components = $this->doctrine->getRepository(Component::class)->findBy(['code' => \array_unique($this->componentListForDefaultPrices)]);
		$categoryCodes = [];

		\array_map(function($component) use (&$categoryCodes) {
			$this->components[$component->getCode()] = $component;
			if (!\in_array($component->getCategoryCode(), $categoryCodes)) {
				$categoryCodes[] = $component->getCategoryCode();
			}
		}, $components);

		if (!empty($categoryCodes)) {
			/** @var ComponentCategory[] $categories */
			$categories = $this->doctrine->getRepository(ComponentCategory::class)->findBy(['code' => $categoryCodes]);
			\array_map(function($category) { $this->componentCategories[$category->getCode()] = $category; }, $categories);
		}
	}

	private function getCurrentCache(string $collectionCode)
	{
		$this->currentCache = $this->doctrine->getRepository(CacheData::class)->findOneBy(['dataCacheKey' => implode('.', [
			$this->licenseKey->getGroupCode(),
			$this->brand->getCompanyCode(),
			$collectionCode,
		])]);

		// If we have no cache for this item, cache data is empty or its a product that is being requested for the first time, generate a new cachedata record.
		if (null === $this->currentCache) {
			$this->currentCache = (new CacheData())
				->setDateCreated(new DateTime('now', new DateTimeZone('UTC')))
				->setDataCacheKey(implode('.', [
					$this->licenseKey->getGroupCode(),
					$this->brand->getCompanyCode(),
					$collectionCode,
				]))
				->setGroupCode($this->licenseKey->getGroupCode())
				->setCompanyCode($this->brand->getCompanyCode());
			$this->doctrine->getManager()->persist($this->currentCache);
		}
	}
}
