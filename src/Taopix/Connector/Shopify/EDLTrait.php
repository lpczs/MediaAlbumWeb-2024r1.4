<?php
namespace Taopix\Connector\Shopify;

use Taopix\Connector\Connector;
use Taopix\Core\Utils\TaopixUtils;
use PricingEngine\Enum\FinancialPrecision;
use PricingEngine\BCMath;
trait EDLTrait
{
	/**
	 * Creates a Shopify product from the supplied project data.
	 *
	 * @param \Taopix\Connector\Shopify\ShopifyConnector $pConnector
	 * @param array $pProjectData Project item data array.
	 * @return array Shopify productCreate result object.
	 */
	static function createShopifyProduct(\Taopix\Connector\Shopify\ShopifyConnector $pConnector, array $pProjectData): array
	{
		$tempProductArrayData = Array();
		$price = ($pConnector->getPricesIncludeTax() == 1) ? $pProjectData['itemtotalsellwithtax'] : $pProjectData['itemtotalsell'];
		$cost = $pProjectData['itemtotalcost'];

		if (isset($pProjectData['qty']))
		{
			$price = bcdiv($price, $pProjectData['qty'], FinancialPrecision::PLACES);
			if (isset($pProjectData['currencydecimalplaces']))
			{
				$price = BCMath::round($price, $pProjectData['currencydecimalplaces']);
			}

			$cost = bcdiv($cost, $pProjectData['qty'], FinancialPrecision::PLACES);
			if (isset($pProjectData['currencydecimalplaces']))
			{
				$cost = BCMath::round($cost, $pProjectData['currencydecimalplaces']);
			}
		}
		$projectDataDescription = '';

		if (isset($pProjectData['producttype']))
		{
			if ($pProjectData['producttype'] == TPX_PRODUCTCOLLECTIONTYPE_PHOTOPRINTS)
			{
				$lang = (isset($pProjectData['shoplocale'])) ? $pProjectData['shoplocale'] : 'en';
				$projectDataDescriptionArray = $pConnector->getUtils()->preparePhotoPrints($pProjectData, $lang);
				$projectDataDescription = $pConnector->getUtils()->componentDescriptionHTML($projectDataDescriptionArray);
			}
			else
			{
				$projectDataDescription = $pProjectData['componentsummary'];
			}
		}

		$thumbnailMetafield = [];
		if (isset($pProjectData['projectpreviewthumbnail']))
		{
			if ($pProjectData['projectpreviewthumbnail'] != '')
			{
				$thumbnailMetafield = 
					[
						'namespace' => 'taopix',
						'key' => 'taopix_project_thumbnail',
						'value' => $pProjectData['projectpreviewthumbnail'],
						'type' => 'file_reference'
					];
			}
		}

		// Build project data.
		$projectData = [
			'price' => $price,
			'productcode' => $pProjectData['productcode'],
			'collectioncode' => $pProjectData['collectioncode'],
			'ownercode' => $pProjectData['productcollectionorigownercode'],
			'collectionname' => $pProjectData['collectionname'],
			'metafields' => [
				$thumbnailMetafield
			],
			'images' => [],
			'variants' => [
				[
					'inventoryItem' => 
					[
						'cost' => $cost,
						'tracked' => false
					]
					,
					'price' => $price,
					'weight' => $pProjectData['productunitweight'],
					'metafields' => [],
					'sku' => $pProjectData['productskucode']
				]
			],
			'description' => $projectDataDescription 

		];

		try
		{
			// Create the project.
			$tempProductArrayData = $pConnector->createTempProduct($projectData);
		}
		catch (\Throwable $pError)
		{
			error_log($pError->getMessage() . ' ' . $pError->getLine() . ' ' . $pError->getFile());
		}

		return $tempProductArrayData;
	}

	/**
	 * Takes the supplied image path and uploads it to Shopify's AWS and assigns it to a product.
	 *
	 * @param \Taopix\Connector\Shopify\ShopifyConnector $pConnector Connector instance to use.
	 * @param string $pProductID Product ID to assign the image to.
	 * @param string $pImagePath Path of the image to assign to the product. 
	 */
	static function createProductImage(\Taopix\Connector\Shopify\ShopifyConnector $pConnector, string $pProductID, string $pImagePath)
	{
		// Write image to a temporary file.
		$tmpFile = tmpfile();

		$result = [];

		if ($tmpFile !== false)
		{
			// Write image to a temp file so that it can be sent via PUT.
			fwrite($tmpFile, file_get_contents($pImagePath));
			fseek($tmpFile, 0);

			try
			{
				$result[] = $pConnector->createTempProductImage($pProductID, [
					[
						'src' =>  $tmpFile,
						'mimeType' => 'image/jpeg',
						'filename' => 'thumbnail.jpg',
						'resource' => 'IMAGE',
						'fileSize' => (string) filesize(stream_get_meta_data($tmpFile)['uri'])
					]
				]);
			}
			catch (\Throwable $pError)
			{
				error_log($pError->getMessage() . ' ' . $pError->getLine() . ' ' . $pError->getFile());
			}
		}

		// Remove the temporary file.
		fclose($tmpFile);

		return $result;
	}

	/**
	 * Adds a Shopify product to a collection in Shopify that matches the tax level set on the product in Taopix.
	 *
	 * @param \Taopix\Connector\Shopify\ShopifyConnector $pConnector Connector instance to use.
	 * @param string $pProductID Shopify product ID.
	 * @param int $pTaxLevel Taopix product tax level.
	 */
	static function addProductToTaxCollection(\Taopix\Connector\Shopify\ShopifyConnector $pConnector, string $pProductID, int $pTaxLevel): void
	{
		// Get the tax collections from Shopify.
		list(,$taxLevelCollections) = $pConnector->getTaxLevelCollections();

		// Filter by the product tax level.
		$taxCollection = $taxLevelCollections->getByTitle('Tax Level ' . $pTaxLevel)[0];

		if ($taxCollection)
		{
			self::addProductToCollection($pConnector, $taxCollection->getStoreFrontID(), $pProductID);
		}
	}

	/**
	 * Adds a Shopify Product to a Shopify product collection.
	 *
	 * @param \Taopix\Connector\Shopify\ShopifyConnector $pConnector Connector instance to use.
	 * @param string $pCollectionID ID of the Shopify product collection to add the product to.
	 * @param string $pProductID ID of the Shopify product to add to the collection.
	 */
	static function addProductToCollection(\Taopix\Connector\Shopify\ShopifyConnector $pConnector, string $pCollectionID, string $pProductID): void
	{
		$addProductsToCollectionResult = $pConnector->addProductsToCollection($pCollectionID, [$pProductID]);

		if (array_key_exists('code', $addProductsToCollectionResult->collectionAddProducts->userErrors))
		{
			$errorObj = $addProductsToCollectionResult->collectionAddProducts->userErrors;
			throw new \Exception($errorObj->message . ' ' . implode(',', $errorObj->field), $errorObj->code);
		}
	}

	/**
	 * Get the project preview thumbnail for a desktop order.
	 *
	 * @param string $pProjectRef Project ref to get the thumbnail for.
	 * @return string The thumbnail URL if available.
	 */
	static function getDesktopProjectPreviewThumbnail(string $pProjectRef): string
	{
		$taopixUtils = new TaopixUtils();

		$thumbnailURL = '';
		$thumbnailAvailableArray = $taopixUtils->getDesktopProjectThumbnailAvailabilityFromProjectRef($pProjectRef);

		if (($thumbnailAvailableArray['error'] === '') && ($thumbnailAvailableArray['available'] === true))
		{
			$thumbnailURL = $taopixUtils->buildDesktopProjectThumbnailWebURL($pProjectRef);
		}

		return $thumbnailURL;
	}

	/**
	 * Builds the path to the checkout file in the system temp folder.
	 *
	 * @param string $pFileName Filename of the checkout file.
	 * @return string Path to the checkout file.
	 */
	static function getCheckoutFilePath(string $pFileName): string
	{
		return sprintf('%s%s%s.inf', sys_get_temp_dir(), DIRECTORY_SEPARATOR, $pFileName);
	}

	/**
	 * Writes the content to the checkout file.
	 *
	 * @throws \Exception If content could not be written to the file.
	 * @param string $pFileName Filename of the checkout file.
	 * @param string $pContent A JSON string of content to write to the checkout file.
	 */
	static function writeCheckoutFile(string $pFileName, string $pContent): void
	{
		$checkoutFile = self::getCheckoutFilePath($pFileName);
		$fileWritten = file_put_contents($checkoutFile, $pContent);

		if (! $fileWritten)
		{
			throw new \Exception('Unable to write checkout file', $pFileName);
		}
	}

	/**
	 * Reads and decodes the content of the checkout file.
	 *
	 * @param string $pFileName Filename of the checkout file.
	 * @return \stdClass Checkout file contents.
	 */
	static function readCheckoutFile(string $pFileName): \stdClass
	{
		$checkoutFile = self::getCheckoutFilePath($pFileName);
		return json_decode(file_get_contents($checkoutFile));
	}

	/**
	 * Checks if the checkout file exists on the filesystem.
	 *
	 * @param string $pFileName Filename of the checkout file.
	 * @return bool True if the file exists.
	 */
	static function checkCheckoutFileExists(string $pFileName): bool
	{
		$checkoutFile = self::getCheckoutFilePath($pFileName);
		return file_exists($checkoutFile);
	}

	/**
	 * Deletes the checkout file.
	 *
	 * @throws \Exception If file could not be deleted.
	 * @param string $pFileName Filename of the checkout file.
	 */
	static function deleteCheckoutFile(string $pFileName)
	{
		$checkoutFile = self::getCheckoutFilePath($pFileName);

		if (file_exists($checkoutFile))
		{
			if (! @unlink($checkoutFile))
			{
				throw new \Exception('Unable to delete checkout file for ' . $pFileName, $pFileName);
			}
		}
	}

	/**
	 * Gets connector store URL from brand code
	 *
	 * @param string $pBrandCode brandcode
	 * @return string URL of the connector
	 */
	static function getConnectorURLFromBrandCode(string $pBrandCode): String 
	{
		$queryArray = ['fields' => ['connectorurl'],			
							'ref'=> ['brandcode'],
							'refvalue' => [$pBrandCode],
							'reftype'=> ['s']
							];
		
		$connectorDetails = new Connector('SHOPIFY', $queryArray);

		return $connectorDetails->getConnectorURL() . '.myshopify.com';
	}

	/**
	 * Creates a Taopix customer user based on data passed from the connector store.
	 *
	 * @param Connector $pConnector Connector instance.
	 * @param array $pUserDetails Array of user details.
	 * @return array Taopix user account details.
	 */
	static function createUserAccountEDL(Connector $pConnector, array $pUserDetails, bool $pIsOrder = false): array
	{
		$userAccount = [];

		try
		{
			$userAccount = $pConnector->createUserAccount($pUserDetails, $pIsOrder);
		}
		catch (\Exception $pError)
		{
			throw new \Exception($pError->getMessage(), $pError->getCode());
		}

		return $userAccount;
	}

	/**
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
	static function updateProjectOrderDataCache($pProjectRefList, $pOrderDate, $pOrderNumber)
	{
		$utils = new TaopixUtils();
		$utils->updateProjectOrderDataCache($pProjectRefList, $pOrderDate, $pOrderNumber);
	}
}
