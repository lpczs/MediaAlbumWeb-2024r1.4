<?php

namespace Taopix\Connector\Shopify;

use Taopix\Connector\Shopify\Collection\ProductCollection;
use Taopix\Connector\Shopify\Entity\Locale as LocaleEntity;
use Taopix\Connector\Shopify\Collection\LocaleCollection;
use Taopix\Core\Utils\TaopixUtils;

class Product
{
	use GraphQLTrait;
	use CurlTrait;

	/**
	 * @var \GraphQL\Client
	 */
	private $graphQL;

	/**
	 * @var String
	 */
	private $currencyCode = '';

	/**
	 * @var TaopixUtils
	 */
	protected $utils;

	/**
	 * @var Array
	 */
	protected $acConfig;	

	/**
	 * @var LocaleCollection
	 */
	private $localeCollection = null;

	/**
	 * Sets the GraphQL instance,
	 *
	 * @param \GraphQL\Client $pGraphQL GraphQL instance to set.
	 * @return Product Product instance.
	 */
	public function setGraphQL(\GraphQL\Client $pGraphQL): Product
	{
		$this->graphQL = $pGraphQL;
		return $this;
	}

	/**
	 * Returns the GraphQL instance.
	 *
	 * @return \GraphQL\Client GraphQL instance.
	 */
	public function getGraphQL(): \GraphQL\Client
	{
		return $this->graphQL;
	}

	/**
	 * Sets the Currency Code
	 *
	 * @param string pCurrencyCode
	 * @return Product Product instance.
	 */
	public function setCurrencyCode($pCurrencyCode): Product
	{
		$this->currencyCode = $pCurrencyCode;
		return $this;
	}

	/**
	 * Returns the Currency Code
	 *
	 * @return string currency code
	 */
	public function getCurrencyCode(): String
	{
		return $this->currencyCode;
	}

	/**
	 * Sets the TaopixUtils instance
	 *
	 * @param TaopixUtils $pUtils TaopixUtils instance to set 
	 * @return Product Product instance.
	 */
	function setUtils(TaopixUtils $pUtils): Product
	{
		$this->utils = $pUtils;
		return $this;
	}

	/**
	 * Returns the TaopixUtils instance.
	 *
	 * @return TaopixUtils instance.
	 */	
	function getUtils(): TaopixUtils
	{
		return $this->utils;
	}

	/**
	 * Sets the AC Config instance
	 *
	 * @param array $pACConfig AC Config to set 
	 * @return Product Product instance.
	 */
	function setACConfig(array $pACConfig): Product
	{
		$this->acConfig = $pACConfig;
		return $this;
	}

	/**
	 * Returns the Ac Config
	 *
	 * @return array system config.
	 */	
	function getACConfig(): array
	{
		return $this->acConfig;
	}

	public function __construct(\GraphQL\Client $pGraphQL)
	{
		$this->setGraphQL($pGraphQL);
		$this->setLocaleCollection($this->requestLocales());
		$this->setCurrencyCode($this->requestCurrencyCode());
		$this->setUtils(new TaopixUtils());
		$this->setACConfig($this->getUtils()->getACConfig());
	}

	/**
	 * Sets the locale collection.
	 *
	 * @param LocaleCollection $pLocaleCollection locale collection to set.
	 * @return Locale locale instance.
	 */
	public function setLocaleCollection(LocaleCollection $pLocaleCollection): LocaleCollection
	{
		$this->localeCollection = $pLocaleCollection;
		return $this->localeCollection;
	}

	/**
	 * Returns the locale collection.
	 *
	 * @return LocaleCollection Collection of locales.
	 */
	public function getLocaleCollection(): LocaleCollection
	{
		return $this->localeCollection;
	}

	/**
	 * Requests the locales available on the Shopify store.
	 *
	 * @return LocaleCollection Collection of Shopify locales.
	 */
	public function requestLocales(): LocaleCollection
	{
		$query = 	(new \GraphQL\Query('shopLocales'))
			->setSelectionSet([
				'locale',
				'primary'
			]);

		$returnedCollection = $this->runGraphQLQuery($this->graphQL, $query, false);
		$returnedLocaleCollection = [];

		foreach ($returnedCollection->shopLocales as $localeCollection) {
			$tempLocale = new LocaleEntity();
			$tempLocale->setLocale($localeCollection->locale);
			$tempLocale->setIsPrimary($localeCollection->primary);
			$returnedLocaleCollection[] = $tempLocale;
		}

		return new LocaleCollection($returnedLocaleCollection);
	}

	/**
	 * Get the primary locale from the locale collection.
	 *
	 * @return LocaleEntity The locale marked as primary.
	 */
	public function getPrimaryLocale(): LocaleEntity
	{
		return $this->localeCollection->getPrimaryLocale();
	}

	/**
	 * Gets the Taopix products to sync to connector 
	 *
	 * @param pVendorName to retrieve correct connector
	 * @param bool $pRetainProductNames determins if  variant names be updated
	 * @return array An array containing new products and update products to sync
	 */
	public function getTaopixProductsToSync(string $pVendorName, bool $pRetainProductNames): array
	{
		$ac_config = $this->getACConfig();
		$utils = $this->getUtils();
		$db = $utils->getGlobalDBConnection();
		$locale = $this->getPrimaryLocale()->getLocale();

		$id = 0;
		$metadata = '';
		$metadatalength = '';
		$productsactive = 0;
		$connectorproduct_id = '';
		$collectioncode = '';
		$collectionname = '';
		$collectiondescription = '';
		$collectiontype = 0;
		$productcode = '';
		$productname = '';
		$versiondate = '';
		$productdescription = '';
		$productid = 0;
		$weight = 0;
		$pricingmodel = 0;
		$price = 0;
		$collectionpreviewresourceref = '';
		$productpreviewresourceref = '';
		$weburl = '';
		$brandcode = '';
		$licenskeycode = '';
		$pricesincludetax = 0;
		$applicationname = '';
		$resultArray = array();
		$resultArray['error'] = '';

		$sql = $this->getProductSQL();

		if ($stmt = $db->prepare($sql)) {
			if ($stmt->bind_param('s', $pVendorName)) {
				if ($stmt->bind_result(
					$id, $collectioncode, $collectionname, $collectiondescription, $collectiontype, $productcode, $productname,
					$productdescription, $pricingmodel, $price, $licenskeycode, $weight, $productid, $connectorproduct_id,
					$metadata, $metadatalength, $productsactive, $versiondate, $collectionpreviewresourceref, $productpreviewresourceref,
					$weburl, $brandcode, $pricesincludetax, $applicationname
				)) {
					if ($stmt->execute()) {
						while ($stmt->fetch()) {

							if ($metadatalength > 0) {
								$metadata = gzuncompress($metadata, $metadatalength);
							}

							if ($weburl === '') {
								$weburl = $ac_config['WEBURL'];
							}

							$weburl = $utils->correctPath($weburl);

							if (!isset($resultArray['products'][$collectioncode])) {
								$resultArray['products'][$collectioncode]['collectionid'] = $id;
								$resultArray['products'][$collectioncode]['collectioncode'] = $collectioncode;
								$resultArray['products'][$collectioncode]['collectionname'] = $utils->getLocaleString($collectionname, $locale, true);
								$resultArray['products'][$collectioncode]['collectiondescription'] = $utils->getLocaleString($collectiondescription, $locale, true);
								$resultArray['products'][$collectioncode]['collectiontype'] = $collectiontype;
								$resultArray['products'][$collectioncode]['collectiontypestring'] = $collectiontype;
								$resultArray['products'][$collectioncode]['connectorlink_productid'] = $connectorproduct_id;
								$resultArray['products'][$collectioncode]['licenskeycode'] = $licenskeycode;
								$resultArray['products'][$collectioncode]['metadata'] = $metadata;
								$resultArray['products'][$collectioncode]['productsactive'] = $productsactive;
								$resultArray['products'][$collectioncode]['collectionresourcefolderpath'] = $utils->getProductCollectionResourceFolderPath($collectioncode, $versiondate);
								$resultArray['products'][$collectioncode]['collectionpreviewresourceref'] = $collectionpreviewresourceref;
								$resultArray['products'][$collectioncode]['collectionversiondate'] = $versiondate;
								$resultArray['products'][$collectioncode]['brandcode'] = $brandcode;
								$resultArray['products'][$collectioncode]['weburl'] = $weburl;
								$resultArray['products'][$collectioncode]['pricesincludetax'] = $pricesincludetax;
								$resultArray['products'][$collectioncode]['applicationname'] = $applicationname;
							}

							if ($productdescription == '') {
								$productdescription = $collectiondescription;
							}

							$resultArray['products'][$collectioncode]['layouts'][$productcode]['productcode'] = $productcode;
							$resultArray['products'][$collectioncode]['layouts'][$productcode]['productname'] = $utils->getLocaleString($productname, $locale, true);
							$resultArray['products'][$collectioncode]['layouts'][$productcode]['productdescription'] = $utils->getLocaleString($productdescription, $locale, true);
							$resultArray['products'][$collectioncode]['layouts'][$productcode]['price'] = $price;
							$resultArray['products'][$collectioncode]['layouts'][$productcode]['pricingmodel'] = $pricingmodel;
							$resultArray['products'][$collectioncode]['layouts'][$productcode]['weight'] = $weight;
							$resultArray['products'][$collectioncode]['layouts'][$productcode]['productid'] = $productid;
							$resultArray['products'][$collectioncode]['layouts'][$productcode]['fromprice'] = 0;
							$resultArray['products'][$collectioncode]['layouts'][$productcode]['productpreviewresourceref'] = $productpreviewresourceref;
						}
					} else {
						$resultArray['error'] = 'Error executing query ' . $db->error;
					}
				} else {
					$resultArray['error'] = 'Error binding result ' . $db->error;
				}
			}
			$stmt->free_result();
			$stmt->close();
			$stmt = null;
		} else {
			$resultArray['error'] = 'Error in prepared statement ' . $db->error;
		}

		$collection = [];
		$productCollection = [];
		$productCollectionUpdate = [];

		if (isset($resultArray['products'])) {
			foreach ($resultArray['products'] as $collection) {
				$variants = [];
				$collectionmetadata = json_decode($collection['metadata']);
				$metafieldid_productid = '';
				$metafieldid_description = '';
				$variantid = '';
				$media = [];
				$options = "Layout";
				$availableOptions = [];
				$collectionTitle = $collection['collectionname'];
				//to be determined by the presence of a position node in the cache 
				$oldCache = true;

				//if we have cached data then this is an update and we should keep the options from the cache
				if ($collectionmetadata != '')
				{
					if (isset($collectionmetadata->title)) {
						$collectionTitle = $collectionmetadata->title;
					}
					if (isset($collectionmetadata->options)) {
						$options = '';
						foreach($collectionmetadata->options as $optionData)
						{
							$options .= ($options != '') ? ',' : ''; 
							$options .= $optionData->name;

							if ($optionData->name != "Layout") {
								$availableOptions[] = $optionData->values;
							}
						}
					}

					if(isset($collectionmetadata->images)) {
						$images = $collectionmetadata->images;
						if (count($images) > 0) {
							//if we have position data in the cache sort by it and turn off old cache mode
							if(isset($images[0]->position)) {
								$oldCache = false;
								usort($images, 'self::imageSort');
							}
							foreach($images as $image) {
								$media[] = ['id' => $image->admin_graphql_api_id, 'altText' => isset($image->alt) ? $image->alt : ''];
							}
						}
					}

					if (isset($collectionmetadata->variants->edges)) {
						foreach ($collectionmetadata->variants->edges as $variantData) {
							$variantid = '';
							$metafieldid_productid = '';
							$metafieldid_description = '';

							if (isset($variantData->node->id)) {
								$variantid = $variantData->node->id;
							}

							//if we are in old cache mode with no position data the variant images need adding seperately
							if ($oldCache) {
								if (isset($variantData->node->image->id)) {
									if ($variantData->node->image->id != '')
									{
										$media[] = ['id' => $variantData->node->image->id];
									}
								}
							}

							if (isset($variantData->node->metafield_taopix_product_id->value)) {
								$metafieldid_productid = $variantData->node->metafield_taopix_product_id->value;
								$productDataArray = explode(".", $metafieldid_productid);

								$collectioncode = $productDataArray[0];
								$layoutcode = $productDataArray[1];

								$fromPrice = $this->getFromPrice($layoutcode, $collection['licenskeycode'], $collection['brandcode'], $collection['pricesincludetax']);

								//if this layout hasn't been deleted and/or deactivated in taopix so ahead and add the variant to the array
								if (isset($resultArray['products'][$collectioncode]['layouts'][$layoutcode]))
								{
									$variants[] =
									[
										'id' => $variantid,
										'weight' => $resultArray['products'][$collectioncode]['layouts'][$layoutcode]['weight'],
										'price' => $fromPrice,
										'title' => '',
										'imageSrc' => '',
										'metafields' => []
									];
								}
							}
						}
					}
				} 
				else 
				{
					$productImage = $this->prepImage(
						$collection['collectionpreviewresourceref'],
						$collection['collectionresourcefolderpath'],
						$collection['collectioncode'],
						$collection['collectionversiondate'],
						$collection['weburl']
					);
	
					if ($productImage != '') {
						$media[] = ['src' => $productImage];
					}
				}

				//variant
				foreach ($collection['layouts'] as $layout) {
					$result = [];
					$taopix_product_id = $collection['collectioncode'] . '.' . $layout['productcode'] . '.' . $collection['licenskeycode'];
					$exists = false;

					if ($collectionmetadata != '') {
						if (isset($collectionmetadata->variants->edges)) {
							foreach ($collectionmetadata->variants->edges as $variantData) {
								if (isset($variantData->node->metafield_taopix_product_id->value)) {
									if ($taopix_product_id == $variantData->node->metafield_taopix_product_id->value) {
										$exists = true;
									}
								}
							}
						}
					}

					if (!$exists) {
						$fromPrice = $this->getFromPrice($layout['productcode'], $collection['licenskeycode'], $collection['brandcode'], $collection['pricesincludetax']);
						$variantTitle = $layout['productname'] . ' (Layout Code: ' . $layout['productcode'] . ')';
						$variantMetafields = [];

						$productImage = $this->prepImage(
							$layout['productpreviewresourceref'],
							$collection['collectionresourcefolderpath'],
							$collection['collectioncode'],
							$collection['collectionversiondate'],
							$collection['weburl']
						);

						if ($productImage != '') {
							$media[] = ['src' => $productImage];
						}

						$weight = $layout['weight'];
						$variantDescription = $layout['productdescription'];
						$variantMetafields[] =
							[
								'namespace' => 'taopix',
								'key' => 'taopix_product_id',
								'value' => $taopix_product_id
							];

						$variantMetafields[] = 
							[
								'namespace' => 'taopix',
								'key' => 'taopix_description',
								'value' => $variantDescription,
								'type' => 'multi_line_text_field'
							];

						$variantOptions = [];
						if (isset($availableOptions[0]))
						{
							foreach($availableOptions[0] as $val){
								if (isset($availableOptions[1]))
								{
									foreach ($availableOptions[1] as $val2) {
										$result[] = [$variantTitle, $val, $val2];
									}
								}
								else 
								{
									$result[] = [$variantTitle, $val];
								}
							}
						}
						else
						{
							$result[] = [$variantTitle];
						}

						foreach($result as $variantOptions)
						{	
							$variants[] =
							[
								'id' => '',
								'weight' => $layout['weight'],
								'price' => $fromPrice,
								'metafields' => $variantMetafields,
								'title' => $variantTitle,
								'description' => $layout['productdescription'],
								'imageSrc' => $productImage,
								'options' => $variantOptions
							];
						}
					}
				}

				if ($collection['connectorlink_productid'] == '') {
					$productCollection[] = [
						'title' => $collectionTitle,
						'descriptionHTML' => $collection['collectiondescription'],
						'vendor' => $collection['applicationname'],
						'productType' => "taopix",
						'handle' => $collection['collectioncode'],
						'tags' => "taopix",
						'published' => true, //Always publish new products to the Online Store Sales Channel
						'status' => $this->getStatusTextFromFlag($collection['productsactive']),
						'metafields' => [
							[
								'namespace' => 'taopix',
								'key' => 'taopix_product_id',
								'value' => $collection['collectioncode']
							]
						],
						'images' => $media,
						'variants' => $variants,
						'options' => explode(",",$options)
					];
				} else {
					$productCollectionUpdate[] = [
						'id' => $collection['connectorlink_productid'],
						'title' => $collectionTitle,
						'variants' => $variants,
						'metafields' => [
							[
								'namespace' => '',
								'key' => '',
								'value' => ''
							]
						],
						'images' => $media,
						'options' => explode(",",$options)
					];
				}
			}
		}

		$returnArray = [];
		$returnArray['newProducts'] = new ProductCollection($productCollection);
		$returnArray['updateProducts'] = new ProductCollection($productCollectionUpdate);

		return $returnArray;
	}

	/**
	 * Sort images by position
	 *
	 * @param object $object1
	 * @param object $object2
	 * @return bool
	 */
	private static function imageSort(Object $object1, Object $object2) { 
		return $object1->position > $object2->position; 
	}

	/**
	 * create the jsonl bulk file to upload
	 * 
	 * @param ProductCollection pProductCollection taopix collections/products to sync.
	 * @param string $pMode is this an INSERT or UPDATE
	 * @param string $pBrandCode  brandccode 
	 * @return string the filename of the bulkfile 
	 */
	public function syncProducts(ProductCollection $pProductCollection, string $pMode, string $pBrandCode): string
	{
		$productJSON = $pProductCollection->getProperties();

		return $this->createBulkFile($productJSON, $pMode, $pBrandCode);
	}

	/**
	 * Run mutation to generate upload URL 
	 *
	 * @param array pProductMutation mutation to insert product into shopify
	 * @return stdClass The query result object
	 */
	public function insertProduct(array $pProductMutation) : \stdClass
	{
		$mutation = (new \GraphQL\Mutation('productCreate'))
			->setVariables([new \GraphQL\Variable('input', 'ProductInput', true)])
			->setArguments(['input' => '$input'])
			->setSelectionSet(
				[
					(new \GraphQL\Query('product'))->setSelectionSet([
						'id',
						'storefrontId',
						'legacyResourceId',
						'title',
						(new \GraphQL\Query('variants'))
							->setArguments(['first' => 1])
							->setSelectionSet([
								(new \GraphQL\Query('edges'))->setSelectionSet([
									(new \GraphQL\Query('node'))->setSelectionSet([
										'legacyResourceId',
										'storefrontId'
									])
								])
							])
					]),
					(new \GraphQL\Query('userErrors'))->setSelectionSet([
						'field',
						'message'
					])
				]
			);
		return $this->runGraphQLQuery($this->graphQL, $mutation, false, ['input' => $pProductMutation]);
	}

	/**
	 * Create the bulk import file ready for shopify.
	 *
	 * @param array $productCollectionJSON locale collection to set.
	 * @param string $pMode insert or update
	 * @param string $pBrandCode brand code
	 * @return string filename of the generated bulk upload jsonl file
	 */
	public function createBulkFile(array $productCollectionJSON, string $pMode, string $pBrandCode): string
	{
		$ac_config = $this->getACConfig();

		$utils = $this->getUtils();
		$productJSON = [];

		// Write JSONL file 
		$filePath = $ac_config['CONNECTORRESOURCESPATH'];
		$filePath = $utils->correctPath($filePath, DIRECTORY_SEPARATOR, true);

		if ($pBrandCode != '')
		{
			$filePath .= $pBrandCode . DIRECTORY_SEPARATOR;
		}

		$utils->createAllFolders($filePath);

		if ($pMode === 'INSERT') {
			$filename = $filePath . 'BulkUploadCreate.jsonl';
		} else {
			$filename = $filePath . 'BulkUploadUpdate.jsonl';
		}

		foreach ($productCollectionJSON as $collection) {
			//If update operation leave published and status unchanged
			if ($pMode === 'UPDATE') {
				unset($collection['published']);
				unset($collection['status']);
			}

			$productJSON[] = json_encode(['input' => $collection]);
		}

		$productJSON = implode(PHP_EOL, $productJSON);
		$utils->writeTextFile($filename, $productJSON);

		return $filename;
	}

	/**
	 * Process the bulk import upload to shopify.
	 *
	 * @param string $pBulkFilename filename & path to upload
	 * @param string $pMode insert or update
	 * @return string bulk operation ID
	 */
	public function processBulkFile(string $pBulkFilename, string $pMode): string
	{
		$bulkOperationId = '';

		try {
			$result = $this->bulkUploadFile([
				[
					'src' =>  $pBulkFilename,
					'mimeType' => 'text/jsonl',
					'filename' => 'bulk_op_vars.jsonl',
					'resource' => 'BULK_MUTATION_VARIABLES',
					'httpMethod' => 'POST',
					'fileSize' => (string) filesize($pBulkFilename)
				]
			], $pMode);
			
			$bulkOperationId = $result->bulkOperationRunMutation->bulkOperation->id;
		} catch (\Throwable $pError) {
			error_log($pError->getMessage() . ' ' . $pError->getLine() . ' ' . $pError->getFile());
		}

		return $bulkOperationId;
	}

	/**
	 * Perform the bulk import of products to shopify.
	 *
	 * @param array $productCollectionJSON locale collection to set
	 * @param string $pMode INSERT or UPDATE
	 * @return stdClass The query result object
	 */
	public function bulkUploadFile(array $pJsonMutation, string $pMode): \stdClass
	{
		$srcList = [];

		$jsonMutation = array_map(function ($pMutation) use (&$srcList) {
			// Remove the src from the array, as it isn't part of the createStagedUploads mutation, but we will need it later.
			$srcList[] = ['src' => $pMutation['src'], 'filesize' => $pMutation['fileSize'], 'mimetype' => $pMutation['mimeType'], 'resource' => $pMutation['resource']];
			unset($pMutation['src']);
			return $pMutation;
		}, $pJsonMutation);

		$generateStagedUploadsResult = $this->generateStagedUploads($jsonMutation);

		$success = [];
		$url = '';
		if (!array_key_exists('message', $generateStagedUploadsResult->stagedUploadsCreate->userErrors)) {
			foreach ($generateStagedUploadsResult->stagedUploadsCreate->stagedTargets as $stagedTarget) {
				$currentSrc = current($srcList);

				foreach ($stagedTarget->parameters as $param) {
					if ($param->name === 'key') {
						$url = $param->value;
					}
				}

				$uploadedFileResult[] = $this->postBulkProductUploadFile($currentSrc, $stagedTarget);

				if ($uploadedFileResult[0]) {
					$success[] = [
						'originalSource' => $url,
						'alt' => '',
						'mediaContentType' => $currentSrc['resource']
					];
				}

				next($srcList);
			}
		}

		if (count($success) > 0) {
			$result = $this->completeBulkUpload($success, $pMode);
		}

		return $result;
	}

	/**
	 * Run mutation to generate upload URL 
	 *
	 * @param array pUploadDataMutation mutation to prepare bulk upload
	 * @return stdClass The query result object
	 */
	private function generateStagedUploads(array $pUploadDataMutation): \stdClass
	{
		$mutation = (new \GraphQL\Mutation('stagedUploadsCreate'))
			->setVariables([new \GraphQL\Variable('input', '[StagedUploadInput!]', true)])
			->setArguments(['input' => '$input'])
			->setSelectionSet([
				(new \GraphQL\Query('stagedTargets'))->setSelectionSet([
					'url',
					'resourceUrl',
					(new \GraphQL\Query('parameters'))->setSelectionSet([
						'name',
						'value'
					])
				]),
				(new \GraphQL\Query('userErrors'))->setSelectionSet([
					'field',
					'message'
				])
			]);

		return $this->runGraphQLQuery($this->graphQL, $mutation, false, ['input' => $pUploadDataMutation]);
	}

	/**
	 * Post the bulk upload file to url supplied by shopify 
	 *
	 * @param array pSource data to post including the jsonl file
	 * @param object pStagedTarget data inc URL to post to
	 * @return stdClass The query result object
	 */
	private function postBulkProductUploadFile(array $pSource, object $pStagedTarget): array
	{
		$postFields = [];

		foreach ($pStagedTarget->parameters as $param) {
			$postFields[$param->name] = $param->value;
		}

		$postFields['file'] = curl_file_create($pSource['src'], 'text/jsonl', 'file');
		$data = [CURLOPT_POST => true, CURLOPT_POSTFIELDS => $postFields, CURLOPT_HEADER => true, CURLOPT_HTTPHEADER => ['Content-Type:multipart/form-data']];

		list($httpCode, $uploadResponse) = $this->initCURL($pStagedTarget->url, $data);
		return [$httpCode === 201, $postFields['key']];
	}

	/**
	 * bulkOperationRunMutation to complete the data import
	 *
	 * @param array pProductMutation contains stagedUploadPath
	 * @param string pMode to determine if productCreate or productUpdate mutation
	 * @return stdClass The query result object
	 */
	private function completeBulkUpload(array $pProductMutation, string $pMode): \stdClass
	{

		if ($pMode === 'INSERT') {
			$productActionMutation = "mutation productCreate(\$input: ProductInput!) 
				{ productCreate(input: \$input) {
					product {
						id
						title
						createdAt
						updatedAt
						variants(first: 100) {
							edges {
								node {
									id
									title
									metafield_taopix_product_id: metafield(namespace: \"taopix\", key: \"taopix_product_id\") {
										id
										value
									}
									metafield_taopix_product_description: metafield(namespace: \"taopix\", key: \"taopix_description\") {
										id
										value
									}
									image {
										id
									}
								}
							}
						}
						metafield(namespace: \"taopix\", key: \"taopix_product_id\") {
							id,
							value
						}
						options {
							name,
							values
						}
					}
					userErrors {
						field
						message
					}
				}
			}
			";
		} else {
			$productActionMutation = "mutation productUpdate(\$input: ProductInput!) 
				{ productUpdate(input: \$input) { 
					product {
						id
						title
						createdAt
						updatedAt
						variants(first: 100) {
							edges {
								node {
									id
									title
									metafield_taopix_product_id: metafield(namespace: \"taopix\", key: \"taopix_product_id\") {
										id
										value
									}
									metafield_taopix_product_description: metafield(namespace: \"taopix\", key: \"taopix_description\") {
										id
										value
									}
									image {
										id
									}
								}
							}
						}
						metafield(namespace: \"taopix\", key: \"taopix_product_id\") {
							id,
							value
						}
						options {
							name,
							values
						}
					}
					userErrors {
						field
						message
					}
				}
			}
			";
		}

		$bulkOperationMutation = (new \GraphQL\Mutation('bulkOperationRunMutation'))
			->setArguments(['mutation' => preg_replace("\t|\n|\r", " ", $productActionMutation), 'stagedUploadPath' => $pProductMutation[0]['originalSource']])
			->setSelectionSet(
				[
					(new \GraphQL\Query('bulkOperation'))->setSelectionSet([
						'id',
						'url',
						'status'
					]),
					(new \GraphQL\Query('userErrors'))->setSelectionSet([
						'field',
						'message'
					])
				]
			);

		return $this->runGraphQLQuery($this->graphQL, $bulkOperationMutation, false, []);
	}

	/**
	 * Query to return status of last Bulk operation
	 *
	 * @param string $pProductID shopify product global identifier
	 * @return stdClass The query result object
	 */
	public function deleteTempProduct(string $pProductID): \stdClass
	{
		$mutation = (new \GraphQL\Mutation('productDelete'))
			->setVariables([new \GraphQL\Variable('input', 'ProductDeleteInput', true)])
			->setArguments(['input' => '$input'])
			->setSelectionSet(
				[
					(new \GraphQL\Query('userErrors'))->setSelectionSet([
						'field',
						'message'
					])
				]
			);

		return $this->runGraphQLQuery($this->graphQL, $mutation, false, ['input' => ['id' => $pProductID]]);
	}

	/**
	 * Query to return status of last Bulk operation
	 *
	 * @param string $pType Bulkoperation type (QUERY or MUTATION)
	 * @return stdClass The query result object
	 */
	public function pollBulkOperationStatus(string $pType = 'MUTATION'): \stdClass
	{
		$query = (new \GraphQL\Query('currentBulkOperation'))
			->setVariables([new \GraphQL\Variable('type', 'BulkOperationType', true)])
			->setArguments(['type' => '$type'])
			->setSelectionSet(
				[
					'id',
					'query',
					'status',
					'completedAt',
					'url'
				]
			);

		return $this->runGraphQLQuery($this->graphQL, $query, false, ['type' => $pType]);
	}

	/**
	 * Store the results of the bulk operation as JSONL file
	 * 
	 * @param array $pPollResults result of the poll graphql query
	 * @param string $pBrandCode brandcode
	 * @param string $pMode discounts mode auto or price rule
	 * @return string filename of the results file created 
	 */
	public function storeBulkOperationResults(array $pPollResults, string $pBrandCode, string $pMode = ''): string
	{
		$status = $pPollResults['status'];
		$currentOperationID = str_replace("gid://shopify/BulkOperation/", "", $pPollResults['id']);
		$return = '';

		if ($status === 'COMPLETED') {
			$utils = $this->getUtils();
			$ac_config = $this->getACConfig();
			$fileContents = '';

			// Write JSONL file 
			$filePath = $ac_config['CONNECTORRESOURCESPATH'];
			$filePath = $utils->correctPath($filePath, DIRECTORY_SEPARATOR, true);
			if ($pBrandCode != '')
			{
				$filePath .= $pBrandCode . DIRECTORY_SEPARATOR;
			}
			$extension = '.jsonl';

			$resultsUrl = $pPollResults['url'];
			
			if ($pMode == '')
			{
				$filename = $filePath . 'bulk-' . $currentOperationID . $extension;
			}
			elseif ($pMode == 'deliveryProfiles') 
			{
				$filePath .= 'deliveryProfiles' . DIRECTORY_SEPARATOR;
				$filename = $filePath . $pMode . $extension;
			}
			else
			{
				$filePath .= 'discounts' . DIRECTORY_SEPARATOR;
				$filename = $filePath . $pMode . $extension;
			}

			$utils->createAllFolders($filePath);
			if ($resultsUrl != '') 
			{
				$fileContents = file_get_contents($resultsUrl);
			}
			$utils->writeTextFile($filename, $fileContents);

			$return = $filename;
		}

		return $return;
	}

	/**
	 * Requests the currency code on the Shopify store.
	 *
	 * @return string currency code
	 */
	public function requestCurrencyCode(): string
	{
		$query = 	(new \GraphQL\Query('shop'))
			->setSelectionSet([
				'currencyCode'
			]);

		$return = $this->runGraphQLQuery($this->graphQL, $query, false);

		return $return->shop->currencyCode;
	}

	/**
	 * Get from price to pass to shopify
	 *
	 * @param string $pLayoutCode product layout code
	 * @param string $pLicenskeycode license key code
	 * @param string $pBrandCode brand code
	 * 
	 * @return string from price of specified product
	 */
	public function getFromPrice(string $pLayoutCode, string $pLicenskeycode, string $pBrandCode, int $pPricesIncludesTax): string
	{
		global $gSession;

		if (!isset($gSession)) {
			$gSession = [];
			$gSession['order']['externalcartscriptexists'] = 0;

			// first check if branded script exists. If not then check for global script.
			if (file_exists(__DIR__ . '/../../../../Customise/scripts/EDL_ExternalShoppingCart_' . $pBrandCode . '.php')) {
				$gSession['order']['externalcartscriptexists'] = 1;
			} elseif (file_exists(__DIR__ . '/../../../../Customise/scripts/EDL_ExternalShoppingCart.php')) {
				$gSession['order']['externalcartscriptexists'] = 1;
			}
		}

		$utils = $this->getUtils();
		$db = $utils->getGlobalDBConnection();
		$fromPrice = 0.00;

		if (count($this->getFromPriceArray()) == 0) {
			$this->setFromPriceArray($utils->populateFromPricesArray($pLicenskeycode, $db, $pPricesIncludesTax));
		}

		$fromPriceArray = $this->getFromPriceArray();

		if (isset($fromPriceArray[$pLayoutCode])) {
			$fromPrice = (float) $fromPriceArray[$pLayoutCode];
		}

		$currencyArray = $utils->getCurrency($this->getCurrencyCode());
		$currencyExchangeRate = (float) $currencyArray['exchangerate'];
		$currencyDecimalPlaces = (float) $currencyArray['decimalplaces'];
		
		$fromPrice = (float) $currencyExchangeRate * $fromPrice;
		$fromPrice = $utils->formatNumber($fromPrice, $currencyDecimalPlaces);

		return $fromPrice;
	}

	/**	
	 * Copys Dat files from collectionresources into connectors with appropriate extension
	 * 
	 * @param string $pResourceRef - collection resource reference
	 * @param string $pResourceFolderPath - collection resources folder path
	 * @param string $pCollectionCode - collection code
	 * @param string $pCollectionVersionDate - version date of the collection
	 * @param string $pWeburl - brand weburl\
	 * 
	 * @return string URL path to image
	*/
	static function prepImage(string $pResourceRef, string $pResourceFolderPath, string $pCollectionCode, string $pCollectionVersionDate, string $pWeburl): string
	{
		$utils = new TaopixUtils;
		$currentProductResourceImage = $pResourceFolderPath . DIRECTORY_SEPARATOR . $pResourceRef . '.dat';

		$newResourceImageFileName = '';
		$productSrc = '';

		if (file_exists($currentProductResourceImage)) {
			$mimeType = $utils->getMimeTypeFromFile($currentProductResourceImage);
			$extension = $utils->getExtensionFromImageType($mimeType);

			$newResourceImageFileName = $utils->getConnectorResourceFolderPath($pCollectionCode, $pCollectionVersionDate) . DIRECTORY_SEPARATOR;
			$utils->createAllFolders($newResourceImageFileName);
			$newResourceImageFileName .= $pResourceRef . $extension;
			$copyResult = false;

			if (!file_exists($newResourceImageFileName)) {
				$copyResult = copy($currentProductResourceImage, $newResourceImageFileName);
			}
		}

		if (file_exists($newResourceImageFileName)) {
			$productSrc = $pWeburl . 'connectors/resources/' . rawurlencode($pCollectionCode) . '/';
			$productSrc .= date("YmdHis", strtotime($pCollectionVersionDate));
			$productSrc .= '/' . $pResourceRef . $extension;
		}

		return $productSrc;
	}

	/**
	 * Gets the Taopix count of products to sync to connector 
	 *
	 * @param pVendorName to retrieve appropriate connector
	 * 
	 * @return array containing counts of new and updated products 
	 */
	public function getCountTaopixProductsToSync(string $pVendorName): array
	{
		$utils = $this->getUtils();
		$db = $utils->getGlobalDBConnection();

		$newCount = 0;
		$updateCount = 0;
		$id = 0;
		$licenskeycode = '';
		$metadata = '';
		$metadatalength = '';
		$productsactive = 0;
		$connectorproduct_id = '';
		$collectioncode = '';
		$collectionname = '';
		$collectiondescription = '';
		$collectiontype = 0;
		$productcode = '';
		$productname = '';
		$versiondate = '';
		$productdescription = '';
		$productid = 0;
		$weight = 0;
		$pricingmodel = 0;
		$price = 0;
		$collectionpreviewresourceref = '';
		$productpreviewresourceref = '';
		$weburl = '';
		$brandcode = '';
		$layoutcount = 0;
		$priceincludetax = 0;
		$applicationname = '';
		$resultArray = array();
		$resultArray['error'] = '';

		$sql = $this->getProductSQL();

		if ($stmt = $db->prepare($sql)) {
			if ($stmt->bind_param('s', $pVendorName)) {
				if ($stmt->bind_result(
					$id, $collectioncode, $collectionname, $collectiondescription, $collectiontype, $productcode,
					$productname, $productdescription, $pricingmodel, $price, $licenskeycode, $weight, $productid,
					$connectorproduct_id, $metadata, $metadatalength, $productsactive, $versiondate, $collectionpreviewresourceref,
					$productpreviewresourceref, $weburl, $brandcode, $priceincludetax, $applicationname
				)) {
					if ($stmt->execute()) {
						while ($stmt->fetch()) {

							if (!isset($resultArray['products'][$collectioncode])) {
								$resultArray['products'][$collectioncode] = 0;

								if ($connectorproduct_id == '') {
									$newCount++;
								} else {
									$updateCount++;
								}
							}
						}
					} else {
						$resultArray['error'] = 'Error executing query ' . $db->error;
					}
				} else {
					$resultArray['error'] = 'Error binding result ' . $db->error;
				}
			}
			$stmt->free_result();
			$stmt->close();
			$stmt = null;
		} else {
			$resultArray['error'] = 'Error in prepared statement ' . $db->error;
		}

		$returnArray = [];
		$returnArray['newProductsCount'] = $newCount;
		$returnArray['updateProductsCount'] = $updateCount;

		return $returnArray;
	}

	/**
	 * Returns SQL to retrieve products to sync
	 * @return String SQL query
	 */
	private function getProductSQL(): string
	{
		return 'SELECT 	
				`pcl`.`id`, 
				`pcl`.`collectioncode`, 
				`af`.`name` as `collectionname`, 
				`af`.`description` as `collectiondescription`, 
				`pcl`.`collectiontype`, 
				`pcl`.`productcode`, 
				`pcl`.`productname`,
				`pcl`.`productdescription`, 
				`p`.`pricingmodel`,
				`p`.`price`,
				`c`.`licensekeycode`,
				`prod`.`weight`,
				`prod`.`id`,
				`lnk`.`connectorproduct_id`,
				`lnk`.`metadata`,
				`lnk`.`metadatalength`,
				`c`.`productsactive`,
				`af`.`versiondate`,
				`pcl`.`collectionpreviewresourceref`,
				`pcl`.`productpreviewresourceref`,
				`b`.`weburl`, 
				`c`.`brandcode`,
				`c`.`pricesincludetax`,
				`b`.`applicationname`
		FROM `PRODUCTCOLLECTIONLINK` pcl
		INNER JOIN `CONNECTORS` c ON c.connectorurl = ?
		INNER JOIN `APPLICATIONFILES` af ON af.ref = pcl.collectioncode
		INNER JOIN `PRICELINK` pl ON pl.productcode = pcl.productcode 
			AND (pl.groupcode = c.licensekeycode OR pl.groupcode = "")
			AND pl.sectioncode = "PRODUCT"
		INNER JOIN `PRICES` p ON p.id = pl.priceid
		INNER JOIN `PRODUCTS` prod ON prod.code = pcl.productcode
		INNER JOIN `BRANDING` b ON b.code = c.brandcode
		LEFT OUTER JOIN `CONNECTORSPRODUCTCOLLECTIONLINK` lnk ON (lnk.collectioncode = pcl.collectioncode AND lnk.connectorurl = c.connectorurl)
		WHERE (af.type = 0) AND (af.deleted = 0) AND (af.active = 1) AND (pl.active = 1) AND (p.active = 1) AND (prod.active = 1)
		ORDER BY pcl.collectioncode, pcl.productcode';
	}

	
	protected $fromPriceArray = [];

	/**
	 * Set the array of product prices
	 * 
	 * @param array $pFromPriceArray - array of product prices. 
	 * @return Product
	 */
	function setFromPriceArray($pFromPriceArray): Product
	{
		$this->fromPriceArray = $pFromPriceArray;
		return $this;
	}


	/**
	 * Return the array of product prices
	 * 
	 * @return array - array of product prices
	 */
	function getFromPriceArray(): array
	{
		return $this->fromPriceArray;
	}

	/**
	 * Convert active flag bool to status string
	 *
	 * @param bool pActiveFlag - true Active, false DRAFT
	 * @return string Active or Draft
	 */
	private static function getStatusTextFromFlag(bool $pActiveFlag): string
	{
		$statusText = 'DRAFT';

		if ($pActiveFlag) {
			$statusText = 'ACTIVE';
		}

		return $statusText;
	}

	/**
	 * Returns SQL to retrieve single product image data to sync
	 * 
	 * @return String SQL query
	 */
	private function getSingleProductImageSQL(): string
	{
		return 'SELECT 	
				`af`.`versiondate`,
				`pcl`.`collectionpreviewresourceref`,
				`pcl`.`productpreviewresourceref`,
				`prod`.`code`
		FROM `PRODUCTCOLLECTIONLINK` pcl
		INNER JOIN `APPLICATIONFILES` af ON af.ref = pcl.collectioncode
		INNER JOIN `PRODUCTS` prod ON prod.code = pcl.productcode
		WHERE pcl.collectioncode = ? 
		ORDER BY pcl.collectioncode, pcl.productcode';
	}

	/**
	 * Returns URL to Collection / Product thumbnail image
	 * 
	 * @param String pWebUrl Brand web url
	 * @param String pCollectionCode Product collection code
	 * @param String pProductCode Product Layout Code
	 * @return String Image URL
	 */
	public function catalogProductImage(string $pWebUrl, string $pCollectionCode, string $pProductCode = ''): string
	{
		$utils = $this->getUtils();
		$db = $utils->getGlobalDBConnection();

		$versiondate  = '';
		$collectionpreviewresourceref = '';
		$productpreviewresourceref = '';
		$productcode = '';
		$resultArray = [];
		$resultArray['data'] = [];

		$sql = $this->getSingleProductImageSQL();

		if ($db) {
			if ($stmt = $db->prepare($sql)) {
				if ($stmt->bind_param('s', $pCollectionCode)) {
					if ($stmt->bind_result(
						$versiondate, $collectionpreviewresourceref, $productpreviewresourceref, $productcode
					)) {
						if ($stmt->execute()) {
							while ($stmt->fetch()) {

								$resultArray['data'][$productcode] = [
									'productpreviewresourceref' => $productpreviewresourceref,
									'collectionpreviewresourceref' => $collectionpreviewresourceref,
									'versiondate' => $versiondate
								];

							}
						} else {
							$resultArray['error'] = 'Error executing query ' . $db->error;
						}
					} else {
						$resultArray['error'] = 'Error binding result ' . $db->error;
					}
				}
				$stmt->free_result();
				$stmt->close();
				$stmt = null;
			} else {
				$resultArray['error'] = 'Error in prepared statement ' . $db->error;
			}
			$db->close();
		}

		$previewresourceref = '';
		$versiondate = '';
		$collectionresourcefolderpath = '';

		if ($pProductCode != '')
		{	
			if (isset($resultArray['data'][$pProductCode]))
			{
				$previewresourceref = $resultArray['data'][$pProductCode]['productpreviewresourceref'];
				if ($previewresourceref == '')
				{
					$previewresourceref = $resultArray['data'][$pProductCode]['collectionpreviewresourceref'];
				}
				$versiondate = $resultArray['data'][$pProductCode]['versiondate'];
			}
		}
		else
		{
			foreach ($resultArray['data'] as $productdata) { 
				$previewresourceref = $productdata['collectionpreviewresourceref'];
				$versiondate = $productdata['versiondate'];
				break;
			} 
		}

		$collectionresourcefolderpath = $utils->getProductCollectionResourceFolderPath($pCollectionCode, $versiondate);

		$imageURL = $this->prepImage(
			$previewresourceref,
			$collectionresourcefolderpath,
			$pCollectionCode,
			$versiondate,	
			$pWebUrl
		);

		return $imageURL;
	}
}
