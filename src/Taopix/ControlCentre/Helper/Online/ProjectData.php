<?php

namespace Taopix\ControlCentre\Helper\Online;

use Exception;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Lock\LockFactory;
use Symfony\Component\Lock\Store\FlockStore;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessor;
use Symfony\Component\Serializer\SerializerInterface;
use Taopix\ControlCentre\Entity\ApplicationFile;
use Taopix\ControlCentre\Entity\Brand;
use Taopix\ControlCentre\Entity\CacheData;
use Taopix\ControlCentre\Entity\Constants;
use Taopix\ControlCentre\Entity\Currency;
use Taopix\ControlCentre\Entity\Keyword;
use Taopix\ControlCentre\Entity\LicenseKey;
use Taopix\ControlCentre\Entity\PriceLink;
use Taopix\ControlCentre\Helper\Asset\PreviewRequest;
use Taopix\ControlCentre\Helper\Cache\BuildCacheData;

class ProjectData
{
	private LicenseKey|null $licenseKey = null;
    private Brand|null $brand = null;
    private Brand|null $defaultBrand = null;
    private PreviewRequest|null $previewRequest = null;
	private CacheData|null $currentCache = null;
	private Constants|null $constants = null;
    private PropertyAccessor|null $accessor = null;

	private array $options = [];
	private array $requiredProducts = [];
	private array $cacheVersions = [];
	private int $retries = 0;

    private array $componentTrees = [];
    private array $productLinks = [];
    private array $requiredTrees = [];

    private array $keywords = [];
    private array $processedTrees = [];

    private bool $lineFooterCreated = false;

	public function __construct(private readonly ManagerRegistry $doctrine, private readonly SerializerInterface $serializer, private readonly array $config = [])
    {
        $this->accessor = PropertyAccess::createPropertyAccessor();
    }

	/**
	 * Returns the data that online requires for project creation or project opening.
	 *
	 * @param array $options Details for what we are trying to create.
	 * @return array
	 * @throws Exception
	 */
	public function getData(array $options): array
	{
		$this->options = $options;

        if (!empty($this->options['availableLayouts'])) {
            $this->populateRequiredProductsAndVersions();
        }

		// Get the license key and brand.
		$this->getConstants();
		$this->getLicenseKey();
		$this->getBrand($this->licenseKey->getWebBrandCode());
		$this->getPriceCacheData();

		$brandAndLicenseValid = $this->licenseAndBrandCacheValid();
		$priceCacheValid = $this->priceCacheValid();

		if ($brandAndLicenseValid && $priceCacheValid) {
			return ['cacheValid' => true];
		}

        $currency = $this->getCurrencyDetails($this->licenseKey->isUseDefaultCurrency() ? $this->constants->getDefaultCurrency() : $this->licenseKey->getCurrencyCode());

		return [
			'brand' => $brandAndLicenseValid ? 'valid' : \array_merge($this->serializer->normalize($this->brand, null, ['groups' => ['brand-details']]), [
                'defaultLanguage' => $this->constants->getDefaultLanguageCode(),
                'passwordStrengthScore' => $this->constants->getMinPasswordScore(),
            ]),
			'license' => $brandAndLicenseValid ? 'valid' : \array_merge($this->serializer->normalize($this->licenseKey, null, ['groups' => ['license-details']]), ['currency' => $currency]),
			'price' => $this->buildPriceData(),
		];
	}

    private function getCurrencyDetails(string $currencyCode): array
    {
        /** @var Currency $currency */
        $currency = $this->doctrine->getManager()->getRepository(Currency::class)->findOneBy(['code' => $currencyCode]);

        return $this->serializer->normalize($currency, null, ['groups' => ['currency-details']]);
    }

	/**
	 * Populates the licenseKey details or throws an exception if the key is not found or inactive.
	 *
	 * @return void
	 * @throws Exception
	 */
	private function getLicenseKey(): void
	{
		$this->licenseKey = $this->doctrine->getRepository(LicenseKey::class)->findOneBy([
			'groupCode' => $this->options['groupCode'],
			'active' => true,
		]);

		if (null === $this->licenseKey) {
			throw new Exception('License key not available'); // TODO: Update to a http status error other than 500
		}
	}

	/**
	 * Gets the desired brand information, will return the default brand if the brand associated with the key is inactive or deleted.
	 *
	 * @param string $code Brand code to get details for.
	 * @return void
	 * @throws Exception
	 */
	private function getBrand(string $code = ''): void
	{
		$this->brand = $this->doctrine->getRepository(Brand::class)->findOneBy([
			'code' => $code,
			'active' => true,
		]);

        // Get details for the default brand as we might need them here.
        if ('' !== $code) {
            $this->defaultBrand = $this->doctrine->getRepository(Brand::class)->findOneBy([
                'code' => '',
                'active' => true,
            ]);
        }

		if (null === $this->brand) {
			throw new Exception('Brand not available'); // TODO: Update to a http status error other than 500
		}

        // Update the web url if it is empty to be populated with the default callback url.
        if ('' !== $code && '' === $this->brand->getWebUrl()) {
            $this->brand->setWebUrl($this->defaultBrand->getWebUrl());
        }

        if (!\str_ends_with($this->brand->getWebUrl(), '/')) {
            $this->brand->setWebUrl($this->brand->getWebUrl().'/');
        }
	}

	/**
	 * Validate that the cache values from online are the same as the system data.
	 *
	 * @return bool
	 */
	private function licenseAndBrandCacheValid(): bool
	{
		if ($this->brand->getDateLastModified()->format('c') !== $this->options['brandLastModified'] ||
			$this->licenseKey->getCacheVersion() !== $this->options['cacheVersion']) {
			return false;
		}

		return true;
	}

	/**
	 * Populates the cacheVersions to check and the required products based on the details sent by online.
	 *
	 * @return void
	 */
	private function populateRequiredProductsAndVersions(): void
	{
        foreach ($this->options['availableLayouts']['products'] as $key => $details) {
            $this->cacheVersions[] = $details['cacheVersion'];
            $this->requiredProducts[] = $details['productCode'];
        }

        $this->cacheVersions = \array_unique($this->cacheVersions);
        $this->requiredProducts = \array_unique($this->requiredProducts);
	}

	/**
	 * Validates if the passed cacheValues are valid with the current system data.
	 *
	 * @return bool
	 */
	private function priceCacheValid(): bool
	{
		/*
		 * Validate that the cacheData exists.
		 * Validate that the cacheData key matches the value sent by online.
		 * Validate that the cacheData key matches the value from the license key.
		 */
		if (null === $this->currentCache || $this->currentCache->getDataCacheKey() !== implode('.', [$this->options['groupCode'], '', $this->options['collectionCode']])
			|| $this->currentCache->getCacheVersion() !== $this->licenseKey->getCacheVersion()) {
			return false;
		}

		/*
		 * cacheVersion details
		 * Empty - online did not have any cacheData for the required products. This will require some build operation
		 * 1 - Online had one cache version for all required products it sent. This may require a build operation if the cacheVersions do not match
		 * 2+ - Online had differing cache versions for one or more of the required products. This will require some build operation.
		 */
		if (empty($this->cacheVersions) || 1 !== count($this->cacheVersions) || $this->currentCache->getCacheVersion() !== $this->cacheVersions[0]) {
			return false;
		}

		return true;
	}

	/**
	 * Sets the current cache data if this has been build by some process.
	 *
	 * @return void
	 */
	private function getPriceCacheData(): void
	{
		$this->currentCache = $this->doctrine->getRepository(CacheData::class)->findOneBy(['dataCacheKey' => implode('.', [
				$this->licenseKey->getGroupCode(),
				$this->brand->getCompanyCode(),
				$this->options['collectionCode'],
			])
		]);
	}

	private function cacheValid(): bool
	{
		return null !== $this->currentCache && $this->licenseKey->getCacheVersion() === $this->currentCache->getCacheVersion();
	}

	private function generateFilteredList(): array
	{
        $collectionAppFile = $this->doctrine->getRepository(ApplicationFile::class)->findOneBy([
            'type' => 0,
            'ref' => $this->options['collectionCode'],
        ]);
        $filteredItems = $collectionAppFile->isActive() ? \array_values(\array_filter(($this->currentCache->getCacheArray()['productlist'] ?? []), function($item) {
            $available = \in_array($item['code'], $this->requiredProducts);

            if ($available) {
                $this->getLinkedTreeData($item);
            }

            return $available;
        })) : [];

        $this->getComponentTrees();

		return array_map(function($item) {
            $componentInfo = $this->productLinks[$item['code']];
            $treeCode = '' === $componentInfo['linkedTree'] ? $item['code'] : $componentInfo['linkedTree'];
			$item['producttreedata'] = $this->getProcessedTree($treeCode);
			return $item;
		}, $filteredItems);
	}
	/**
	 * Build the price structure correctly.
	 * @return array
	 * @throws Exception
	 */
	private function buildPriceData(): array
	{
		if ($this->cacheValid()) {
			// Filter the list of products in the cache data to be based on the items we want from online, and populate the components.
			return $this->generateFilteredList();
		}

		$lockFactory = new LockFactory(new FlockStore());
		// Build the lock based on the key used by the cache not the cache version, as multiple changes to the license key would trigger multiple cache version updates.
		$lock = $lockFactory->createLock(implode('.', [
			$this->licenseKey->getGroupCode(),
			$this->brand->getCompanyCode(),
			$this->options['collectionCode'],
		]));

		while (!$lock->acquire())
		{
			if (10 < $this->retries) {
				throw new Exception('Cache build taking too long');
			}

			/*
			 * Reset the licenseKey, brand and currentCache values in case of changes during the build process. This can occur if the cache data is being built
			 * and there is a change to the license key, this will result in differing cache values, meaning the cache needs a rebuild.
			 * In most instances details here will not change but it's safer to ensure we have the correct details.
			 */
			$this->getLicenseKey();
			$this->getBrand($this->licenseKey->getWebBrandCode());
			$this->getPriceCacheData();

            // Cache details are valid now so return these.
			if ($this->cacheValid())
			{
				return $this->generateFilteredList();
			}

			sleep(2);
			$this->retries++;
		}

		$builder = new BuildCacheData($this->doctrine, $this->licenseKey, $this->brand, [
			'defaultCurrencyCode' => $this->constants->getDefaultCurrency(),
			'cacheVersion' => $this->licenseKey->getCacheVersion(),
		]);
		$builder->buildCache([
			'collectionCode' => $this->options['collectionCode'],
			'requiredProducts' => $this->requiredProducts,
		]);

        // Lock was acquired so release it now.
		$lock->release();
		$this->getPriceCacheData();

		return $this->generateFilteredList();
	}

	/**
	 * Get the details from the constants table.
	 * @return void
	 */
	private function getConstants(): void
	{
		$constants = $this->doctrine->getRepository(Constants::class)->findAll();
		$this->constants = $constants[0];
	}

    private function getLinkedTreeData(array $item): void
    {
        $linkedProductCode = $this->doctrine->getRepository(PriceLink::class)->getLinkedProductCode($item['code']);
        $this->productLinks[$item['code']] = ['productCode' => $item['code'], 'linkedTree' => $linkedProductCode];
        $targetCode = '' === $linkedProductCode ? $item['code'] : $linkedProductCode;
        if (!\in_array($targetCode, $this->requiredTrees)) {
            $this->requiredTrees[] = $targetCode;
        }
    }

	private function getComponentTrees()
    {
        $componentTreeData = $this->doctrine->getRepository(PriceLink::class)->getComponentTree([
            'companyCode' => $this->brand->getCompanyCode(),
            'groupCode' => $this->options['groupCode'],
            'productCodes' => $this->requiredTrees,
        ]);

        if (null === $this->previewRequest) {
            $this->previewRequest = new PreviewRequest($this->brand->getWebUrl(), $this->config);
        }

        $this->componentTrees = \array_reduce($componentTreeData, function ($carry, $treeRecord) {
            // Make sure product exists at a root level.
            $productNode = $this->accessor->getValue($carry, '['.$treeRecord['productcode'].']');
            if (null === $productNode) {
                $this->accessor->setValue($carry, '['.$treeRecord['productcode'].']', $this->createTopLevelNode());
            }

            /*
             * Normalize the path name trim leading $ and trailing \, replace \ with . then remove other $ from the path name.
             * eg $COVER\ becomes COVER
             * $COVER\COMPONENT\$SUBCOMPONENT becomes COVER.COMPONENT.SUBCOMPONENT
             */
            $normalizedPath = \str_replace(['\\', '$'], ['.children.', ''], \trim($treeRecord['parentpath'], " \t\n\r\0\x0B\$\\"));
            $pathParts = \explode('.', $normalizedPath);
            // Get preview and, keywords for this item.
            $treeRecord['previewimage'] = $this->previewRequest->getAssetPath($treeRecord['code'], 'components');
            $treeRecord['keywords'] = 0 !== $treeRecord['keywordgroupheaderid'] ? $this->getKeywords($treeRecord['keywordgroupheaderid']) : [];
            $formattedPath = !empty($pathParts) ? \array_merge([$treeRecord['productcode'], 'children'], $pathParts) : [$treeRecord['productcode'], 'children'];
            if ('LINEFOOTER' === $pathParts[0] && !$this->lineFooterCreated) {
                $lineFooter = $this->createTopLevelNode([
                    'text' => 'LINEFOOTER',
                    'sectioncode' => 'LINEFOOTER',
                    'parentpath' => $pathParts[0],
                    'sectionname' => 'LINEFOOTER',
                    'islist' => 1,
                    'allowinherit' => 0, // TODO: update this
                    'previewimage' => '',
                    'prompt' => 'LINEFOOTER',
                    'displaystage' => 3,
                    'requirespagecount' => 0,
                ]);
                $this->lineFooterCreated = true;
                $this->accessor->setValue($carry, '['.$treeRecord['productcode'].'][children][LINEFOOTER]', $lineFooter);
            }
            if ($treeRecord['islist']) {
                // Create the category and add the item we are as well.
                $pathString = '['.\implode('][', $formattedPath).']';
                $category = $this->accessor->getValue($carry, $pathString);
                if (null === $category) {
                    $item = 1 === \count($pathParts) ? $this->createTopLevelNode([
                        'text' => $treeRecord['categoryname'],
                        'sectioncode' => $treeRecord['sectioncode'],
                        'parentpath' => $pathParts[0],
                        'sectionname' => $treeRecord['name'],
                        'islist' => (int)$treeRecord['islist'],
                        'allowinherit' => 0, // TODO: update this
                        'previewimage' => $treeRecord['previewimage'],
                        'prompt' => $treeRecord['categoryprompt'],
                        'displaystage' => $treeRecord['displaystage'],
                        'requirespagecount' => (int)$treeRecord['requirespagecount'],
                    ]) : [
                        'children' => [],
                        'code' => '',
                        'componentcode' => $treeRecord['sectioncode'],
                        'default' => 0,
                        'displaystage' => $treeRecord['displaystage'],
                        'info' => $treeRecord['info'],
                        'inheritparentqty' => (int) $treeRecord['inheritparentqty'],
                        'islist' => (int) $treeRecord['islist'],
                        'keywords' => [],
                        'maximumpagecount' => $treeRecord['maximumpagecount'],
                        'minimumpagecount' => $treeRecord['minimumpagecount'],
                        'parentpath' => \implode('.', \array_filter($pathParts, function ($path) { return 'children' !== $path; })),
                        'previewimage' => $treeRecord['previewimage'],
                        'price' => '',
                        'pricetaxcode' => '',
                        'pricetaxrate' => '',
                        'pricingmodel' => 0,
                        'prompt' => 'LINEFOOTER' === $pathParts[0] ? $treeRecord['categoryprompt'] : '',
                        'qtyisdropdown' => 0,
                        'requirespagecount' => (int) $treeRecord['requirespagecount'],
                        'sectioncode' => $treeRecord['sectioncode'],
                        'sortorder' => 0,
                        'text' => 'LINEFOOTER' === $pathParts[0] ? $treeRecord['categoryname'] : $treeRecord['name'],
                    ];
                    $this->accessor->setValue($carry, $pathString, $item);
                }
                $subCode = $pathString.'[children]['.$treeRecord['localcode'].']';
                $subComponent = $this->accessor->getValue($carry, $subCode);
                if (null === $subComponent || (!$treeRecord['isdefaultprice'] && $subComponent['isdefaultprice'])) {
                    $this->accessor->setValue($carry, $subCode, [
                        'text' => $treeRecord['name'],
                        'info' => $treeRecord['info'],
                        'sectioncode' => $treeRecord['sectioncode'],
                        'componentcode' => $treeRecord['localcode'],
                        'code' => $treeRecord['code'],
                        'islist' => (int) $treeRecord['islist'],
                        'isdefaultprice' => $treeRecord['isdefaultprice'],
                        'default' => (int) $treeRecord['isdefault'],
                        'pricingmodel' => $treeRecord['pricingmodel'],
                        'price' => $treeRecord['price'],
                        'pricetaxrate' => $treeRecord['taxrate'],
                        'taxcode' => $treeRecord['taxcode'],
                        'parentpath' => \implode('.', \array_filter($pathParts, function ($path) { return 'children' !== $path; })),
                        'inheritparentqty' => (int) $treeRecord['inheritparentqty'],
                        'sortorder' => $treeRecord['sortorder'],
                        'quantityisdropdown' => (int) $treeRecord['quantityisdropdown'],
                        'previewimage' => $treeRecord['previewimage'],
                        'children' => ($subComponent['children'] ?? []),
                        'keywords' => $treeRecord['keywords'],
                        'prompt' => $treeRecord['categoryprompt'],
                        'displaystage' => $treeRecord['displaystage'],
                        'requirespagecount' => (int) $treeRecord['requirespagecount'],
                        'minimumpagecount' => $treeRecord['minimumpagecount'],
                        'maximumpagecount' => $treeRecord['maximumpagecount'],
                    ]);
                }
            } else {
                $componentPathString = '['.\implode('][', $formattedPath).']';
                $checkString = '';
                if (\str_ends_with($componentPathString, '[]')) {
                    $checkString = \substr($componentPathString, 0, -2);
                    $componentPathString = \substr($componentPathString, 0, -1).$treeRecord['sortorder'] . ']';
                } else {
                    $checkString = $componentPathString. '[children]';
                    $componentPathString .= '[children]['.$treeRecord['sortorder'].']';
                }
                $component = $this->accessor->getValue($carry, $componentPathString);
                $children = $this->accessor->getValue($carry, $checkString);
                if (null === $component && !empty($children)) {
                    $match = \array_values(\array_filter($children, function ($item) use ($treeRecord) {
                        return $item['code'] === $treeRecord['code'];
                    }));
                    if (!empty($match)) {
                        $component = $match[0];
                    }
                }
                if (null === $component || (!$treeRecord['isdefaultprice'] && $component['isdefaultprice'])) {
                    $this->accessor->setValue($carry, $componentPathString, [
                        'text' => $treeRecord['name'],
                        'info' => $treeRecord['info'],
                        'sectioncode' => '' !== $treeRecord['parentpath'] ? $treeRecord['sectioncode'] : '',
                        'componentcode' => $treeRecord['localcode'],
                        'code' => $treeRecord['code'],
                        'islist' => (int) $treeRecord['islist'],
                        'isdefaultprice' => $treeRecord['isdefaultprice'],
                        'default' => (int) $treeRecord['isdefault'],
                        'pricingmodel' => $treeRecord['pricingmodel'],
                        'price' => $treeRecord['price'],
                        'pricetaxrate' => $treeRecord['taxrate'],
                        'taxcode' => $treeRecord['taxcode'],
                        'parentpath' => \implode('.', \array_filter($pathParts, function ($path) { return 'children' !== $path; })),
                        'inheritparentqty' => (int) $treeRecord['inheritparentqty'],
                        'sortorder' => $treeRecord['sortorder'],
                        'quantityisdropdown' => (int) $treeRecord['quantityisdropdown'],
                        'previewimage' => $treeRecord['previewimage'],
                        'children' => ($component['children'] ?? []),
                        'keywords' => $treeRecord['keywords'],
                        'prompt' => $treeRecord['categoryprompt'],
                        'displaystage' => $treeRecord['displaystage'],
                        'requirespagecount' => (int) $treeRecord['requirespagecount'],
                        'minimumpagecount' => $treeRecord['minimumpagecount'],
                        'maximumpagecount' => $treeRecord['maximumpagecount'],
                    ]);
                }
            }
            return $carry;
        }, []);
	}

    private function getProcessedTree(string $treeCode): string|array
    {
        if (\array_key_exists($treeCode, $this->processedTrees)) {
            return $this->processedTrees[$treeCode];
        }

        $treeData = $this->componentTrees[$treeCode] ?? $this->createTopLevelNode();
        $this->processedTrees[$treeCode] = $this->unkeyChildrenAndKeywords($treeData);
        return $this->processedTrees[$treeCode];
    }

    private function createTopLevelNode(array $overrides = []): array
    {
        return \array_merge([
            'text' => '',
            'sectioncode' => 'PRODUCT',
            'componentcode' => '',
            'parentpath' => '',
            'sectionname' => 'PRODUCT',
            'islist' => 0,
            'allowinherit' => 0,
            'previewimage' => '',
            'children' => [],
            'keywords' => [],
            'categoryprompt' => '',
            'displaystage' => '',
            'requirespagecount' => 0,
        ], $overrides);
    }

    private function unkeyChildrenAndKeywords(array $data): array
    {
        $data['children'] = \array_reduce($data['children'], function($carry, $item) {
            $carry[] = \is_array($item) ? $this->unkeyChildrenAndKeywords($item) : $item;
            return $carry;
        }, []);
        $data['keywords'] = \array_values($data['keywords'] ?? []);

        return $data;
    }

    private function getKeywords(int $headerId): array
    {
        if (!\array_key_exists($headerId, $this->keywords)) {
            $keywords = \array_reduce($this->doctrine->getRepository(Keyword::class)->getKeywordList($headerId), function ($carry, $keyword) {
                if (!\in_array($keyword['code'], $carry)) {
                    $carry[$keyword['code']] = $keyword;
                    $carry[$keyword['code']]['flags'] = \str_replace('[WEBROOT]/', $this->brand->getWebUrl(), $carry[$keyword['code']]['flags']);
                }
                return $carry;
            }, []);
            $this->keywords[$headerId] = $keywords;
        }

        return $this->keywords[$headerId];
    }
}
