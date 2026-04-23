<?php

namespace Taopix\ControlCentre\Controller;

use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Query;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use GuzzleHttp\Client;
use Taopix\ControlCentre\Helper\Experience\Experience;
use Taopix\ControlCentre\Enum\Experience\ExperienceType;
use Taopix\ControlCentre\Entity\Brand;
use Taopix\ControlCentre\Entity\ProductCollectionLink;
use Taopix\ControlCentre\Entity\SystemConfig;
use Taopix\ControlCentre\Common\URLFormatting;
use Taopix\ControlCentre\Entity\LicenseKey;
use Taopix\ControlCentre\Entity\Product as EntityProduct;
use Taopix\ControlCentre\Enum\Experience\ExperienceAssignMode;
use Taopix\ControlCentre\Enum\Product;
use Taopix\ControlCentre\Common\CommonFunctions;
use Taopix\ControlCentre\Entity\UserSystemPreferences as UserSystemPreferencesEntity;
use Taopix\ControlCentre\Enum\Unit;
use Taopix\ControlCentre\Helper\Theme\ThemeManager;
use Taopix\ControlCentre\Enum\UserSystemPreferences;

class ExperienceController extends AbstractController
{
	/**
	 * Get list of experience data
	 *
	 * @param Request $request
	 * @param ManagerRegistry $doctrine
	 * @return JsonResponse
	 */
	#[Route('/api/experience/getListData', name: 'getExperienceListData', methods: [Request::METHOD_OPTIONS, Request::METHOD_GET])]
	public function getExperienceListData(Request $request, ManagerRegistry $doctrine): JsonResponse
	{
		$response = new JsonResponse(null, 200, [
			'Access-Control-Allow-Headers' => ['Authorization', 'Content-Type']
		]);

		if (Request::METHOD_OPTIONS === $request->getMethod()) {
			$response->headers->add(['Access-Control-Max-Age' => 7200]);
			return $response;
		}

		$experienceType = $request->get('experiencetype', 0);
		$returnData = new \stdClass();

		try {
			$ac_config = $this->getParameter('maw.config');
			$systemConfigArray = $doctrine->getManager()->getRepository(SystemConfig::class)->findOneBy([]);

			$urlFormatter = new URLFormatting();
			$endpoint = $urlFormatter->correctURL($ac_config['TAOPIXONLINEURL'], "/", true) . 'api/experience/listexperiencedata'
				.   '?experiencetype=' . $experienceType
				.   '&systemkey=' . $systemConfigArray->getSystemKey()
				.   '&tenantid=' . $systemConfigArray->getTenantid()
			;

			$experienceHelper = new Experience();
			$httpClient = new Client([
				'verify' => CommonFunctions::getCurlPEMFilePath($this->getParameter('kernel.project_dir'))
			]);
			$responseBody = "";
			$responseGuzzle = $httpClient->get($endpoint, []);

			// Get the contents of the body of the response.
			$responseBody = $responseGuzzle->getBody()->getContents();
			$returnData = json_decode($responseBody);

			$baseExperienceArray = $experienceHelper->getBaseExperience(Product\Type::PHOTO_BOOK->value, false);
			$calendarBaseExperienceArray = $experienceHelper->getBaseExperience(Product\Type::CALENDAR->value, false);
			$retroPrintBaseExperienceArray = $experienceHelper->getBaseExperience(Product\Type::PHOTO_BOOK->value, true);
			$schema = $experienceHelper->getSchemaArray();

			foreach($returnData->data as &$data) {
				$base = match($data->productType) {
					Product\Type::CALENDAR->value  => $calendarBaseExperienceArray,
					-1, Product\Type::PHOTO_BOOK->value  => ($data->retroPrint) ? $retroPrintBaseExperienceArray : $baseExperienceArray
				};

				$index = ExperienceType::cases()[$data->experienceType]->name;
				$baseDataArray = ($index === 'FULL') ? $base : $base[$index];
				$data->data = (array) $experienceHelper->keyRename(CommonFunctions::array_merge_recursive_ex((array) $baseDataArray, (array) $data->data));
			}

			$returnData->schema = (ExperienceType::cases()[$experienceType] === ExperienceType::FULL) ? $schema : $schema[ExperienceType::cases()[$experienceType]->name];
			$returnData->baseExperience = [
				Product\Type::PHOTO_BOOK->value . '|0' => $experienceHelper->keyRename((array) $baseExperienceArray),
				Product\Type::CALENDAR->value . '|0'=> $experienceHelper->keyRename((array) $calendarBaseExperienceArray),
				Product\Type::PHOTO_BOOK->value . '|1'  => $experienceHelper->keyRename((array) $retroPrintBaseExperienceArray)
			];
			$returnData->features =  [
				'ai' => ($systemConfigArray->getConfig() & 64) ? true : false,
				'retroPrints' => (isset($ac_config['ALLOWRETROPRINTS']) && $ac_config['ALLOWRETROPRINTS'] === "1"),
				'scaleBeforeUpload' => (isset($ac_config['ALLOWIMAGESCALINGBEFORE']) && $ac_config['ALLOWIMAGESCALINGBEFORE'] === "1")
			];
		} catch (\GuzzleHttp\Exception\ClientException|\GuzzleHttp\Exception\ServerException $e) {
			$returnData->error = new \stdClass();
			$returnData->error->code = 400;
			$returnData->error->fullMessage = 'str_ErrorFailedToLoadData';
		}

		return $response
				->setData($returnData)
				->setStatusCode((!isset($returnData->error->code)) ? 200 : $returnData->error->code);
	}

	/**
	 * Send experience data to online to store
	 *
	 * @param Request $request
	 * @param ManagerRegistry $doctrine
	 * @return JsonResponse
	 */
	#[Route('/api/experience/saveData', name: 'saveExperienceData', methods: [Request::METHOD_OPTIONS, Request::METHOD_POST])]
	public function saveExperienceData(Request $request, ManagerRegistry $doctrine): JsonResponse
	{
		$response = new JsonResponse(null, 200, [
			'Access-Control-Allow-Headers' => ['Authorization', 'Content-Type']
		]);

		if (Request::METHOD_OPTIONS === $request->getMethod()) {
			$response->headers->add(['Access-Control-Max-Age' => 7200]);
			return $response;
		}

		$returnData = new \stdClass();

		try {
			$experienceHelper = new Experience();
			$experience = json_decode($request->get('experience', ''),true);

			$experienceDiff = CommonFunctions::recursiveDiff(
				$experienceHelper->getBaseExperience($experience['productType'], $experience['retroPrint'])[ExperienceType::cases()[$experience['experienceType']]->name],
				$experienceHelper->keyRename($experience['data'],false)
			);
			$experience['data'] = $experienceDiff;

			if (!isset($experience['code']) || $experience['code'] === '') {
				$experience['code'] = CommonFunctions::createRandomCode(12,true);
			}

			$ac_config = $this->getParameter('maw.config');
			$systemConfigArray = $doctrine->getManager()->getRepository(SystemConfig::class)->findOneBy([]);

			$urlFormatter = new URLFormatting();
			$endpoint = $urlFormatter->correctURL($ac_config['TAOPIXONLINEURL'], "/", true) . 'api/experience/saveexperiencedata';
			$httpClient = new Client(['verify' => CommonFunctions::getCurlPEMFilePath($this->getParameter('kernel.project_dir'))]);
			$responseBody = "";

			$responseGuzzle = $httpClient->post($endpoint, [
				'form_params' => [
					'experienceData' => json_encode($experience),
					'systemkey' => $systemConfigArray->getSystemKey(),
					'tenantid' => $systemConfigArray->getTenantid(),
				]
			]);

			// Get the contents of the body of the response.
			$responseBody = $responseGuzzle->getBody()->getContents();
			$returnData = json_decode($responseBody);
		} catch (\GuzzleHttp\Exception\ClientException|\GuzzleHttp\Exception\ServerException $e) {
			$returnData->error = new \stdClass();
			$returnData->error->code = 400;
			$returnData->error->fullMessage = 'str_ErrorFailedToSaveData';
		}

		return $response
					->setData($returnData)
					->setStatusCode((!isset($returnData->error->code)) ? 200 : $returnData->error->code);
	}

	/**
	 * Send experience assignment only to online to store
	 *
	 * @param Request $request
	 * @param ManagerRegistry $doctrine
	 * @return JsonResponse
	 */
	#[Route('/api/experience/saveAssignmentData', name: 'saveExperienceAssignmentData', methods: [Request::METHOD_OPTIONS, Request::METHOD_POST])]
	public function saveExperienceAssignmentData(Request $request, ManagerRegistry $doctrine): JsonResponse
	{
		$response = new JsonResponse(null, 200, [
			'Access-Control-Allow-Headers' => ['Authorization', 'Content-Type']
		]);

		if (Request::METHOD_OPTIONS === $request->getMethod()) {
			$response->headers->add(['Access-Control-Max-Age' => 7200]);
			return $response;
		}

		$returnData = new \stdClass();

		try {
			$assignmentData = json_decode($request->get('data', ''),true);

			$ac_config = $this->getParameter('maw.config');
			$systemConfigArray = $doctrine->getManager()->getRepository(SystemConfig::class)->findOneBy([]);

			$urlFormatter = new URLFormatting();
			$endpoint = $urlFormatter->correctURL($ac_config['TAOPIXONLINEURL'], "/", true) . 'api/experience/saveexperienceassignment';
			$httpClient = new Client(['verify' => CommonFunctions::getCurlPEMFilePath($this->getParameter('kernel.project_dir'))]);
			$responseBody = "";

			$responseGuzzle = $httpClient->post($endpoint, [
				'form_params' => [
					'assignmentData' => json_encode($assignmentData),
					'systemkey' => $systemConfigArray->getSystemKey(),
					'tenantid' => $systemConfigArray->getTenantid()
				]
			]);

			// Get the contents of the body of the response.
			$responseBody = $responseGuzzle->getBody()->getContents();
			$returnData = json_decode($responseBody);
		} catch (\GuzzleHttp\Exception\ClientException|\GuzzleHttp\Exception\ServerException $e) {
			$returnData->error = new \stdClass();
			$returnData->error->code = 400;
			$returnData->error->fullMessage = 'str_ErrorFailedToSaveData';
        }

        return $response->setEncodingOptions(JSON_UNESCAPED_UNICODE)
            ->setData($returnData)
            ->setStatusCode((!isset($returnData->error->code)) ? 200 : $returnData->error->code);
	}

	/**
	 * Live product search results
	 *
	 * @param Request $request
	 * @param ManagerRegistry $doctrine
	 * @return JsonResponse
	 */
	#[Route('/api/experience/liveSearch', name: 'experienceLiveSearch', methods: [Request::METHOD_OPTIONS, Request::METHOD_POST])]
	public function liveSearch(Request $request, ManagerRegistry $doctrine): JsonResponse
	{
		$response = new JsonResponse(null, 200, [
			'Access-Control-Allow-Headers' => ['Authorization', 'Content-Type']
		]);

		if (Request::METHOD_OPTIONS === $request->getMethod()) {
			$response->headers->add(['Access-Control-Max-Age' => 7200]);
			return $response;
		}

		try {
			$ac_config = $this->getParameter('maw.config');
			$experienceHelper = new Experience();
			$query = $request->get('query', '');
			$searchResultArray = $doctrine->getManager()->getRepository(ProductCollectionLink::class)->getProductsForLiveSearch($query);

			$returnData = ['results' => []];

			if (count($searchResultArray) > 0) {
				foreach($searchResultArray as $searchResult) {

					$urlFormatter = new URLFormatting();
					$folderpath = $urlFormatter->correctURL($ac_config['PRODUCTCOLLECTIONRESOURCESPATH'], DIRECTORY_SEPARATOR, true);
					$folderpath .= $searchResult['collectionCode'] . DIRECTORY_SEPARATOR . $searchResult['versionDate']->format('YmdHis');

					$previewresourceref = $searchResult['productPreviewResourceRef'];
					if ($previewresourceref == '')
					{
						$previewresourceref = $searchResult['collectionPreviewResourceRef'];
					}

					$path = $folderpath . DIRECTORY_SEPARATOR . $previewresourceref . '.dat';

					$returnData['results'][] = [
						'id' => $searchResult['id'],
						'productCode' => $searchResult['productCode'],
						'collectionCode' => $searchResult['collectionCode'],
						'productName' => $searchResult['productName'],
						'collectionName' => $searchResult['collectionName'],
						'base64Img' => (file_exists($path)) ? base64_encode(file_get_contents($path)) : ''
					];
				}
			}
		} catch (\GuzzleHttp\Exception\ClientException|\GuzzleHttp\Exception\ServerException $e) {
			$returnData['error'] = ['code' => 400, 'fullMessage' => 'str_ErrorFailedToLoadData'];
		}

		return $response
					->setData($returnData)
					->setStatusCode((!isset($returnData['error']['code'])) ? 200 : $returnData['error']['code']);
	}

	/**
	 * Send experience id to online to request deletion
	 *
	 * @param Request $request
	 * @param ManagerRegistry $doctrine
	 * @return JsonResponse
	 */
	#[Route('/api/experience/delete', name: 'deleteExperience', methods: [Request::METHOD_OPTIONS, Request::METHOD_POST])]
	public function deleteExperience(Request $request, ManagerRegistry $doctrine): JsonResponse
	{
		$response = new JsonResponse(null, 200, [
			'Access-Control-Allow-Headers' => ['Authorization', 'Content-Type']
		]);

		if (Request::METHOD_OPTIONS === $request->getMethod()) {
			$response->headers->add(['Access-Control-Max-Age' => 7200]);
			return $response;
		}

		try {
			$experienceHelper = new Experience();
			$experienceIdArray = json_decode($request->get('experienceIdArray', ''));

			$ac_config = $this->getParameter('maw.config');
			$systemConfigArray = $doctrine->getManager()->getRepository(SystemConfig::class)->findOneBy([]);

			$urlFormatter = new URLFormatting();
			$endpoint = $urlFormatter->correctURL($ac_config['TAOPIXONLINEURL'], "/", true) . 'api/experience/delete';
			$httpClient = new Client(['verify' => CommonFunctions::getCurlPEMFilePath($this->getParameter('kernel.project_dir'))]);
			$responseBody = "";

			$responseGuzzle = $httpClient->post($endpoint, [
				'form_params' => [
					'experienceIdArray' => json_encode($experienceIdArray),
					'systemkey' => $systemConfigArray->getSystemKey(),
					'tenantid' => $systemConfigArray->getTenantid()
				]
			]);

			// Get the contents of the body of the response.
			$responseBody = $responseGuzzle->getBody()->getContents();
			$returnData = json_decode($responseBody);

		} catch (\GuzzleHttp\Exception\ClientException|\GuzzleHttp\Exception\ServerException $e) {
			$returnData->error = new \stdClass();
			$returnData->error->code = 400;
			$returnData->error->fullMessage = 'str_ErrorFailedToDeleteData';
		}

		return $response
					->setData($returnData)
					->setStatusCode((!isset($returnData->error->code)) ? 200 : $returnData->error->code);
	}

	/**
	 * Delete individual experience assignment
	 *
	 * @param Request $request
	 * @param ManagerRegistry $doctrine
	 * @return JsonResponse
	 */
	#[Route('/api/experience/deleteAssignment', name: 'deleteAssignment', methods: [Request::METHOD_OPTIONS, Request::METHOD_POST])]
	public function deleteAssignment(Request $request, ManagerRegistry $doctrine): JsonResponse
	{
		$response = new JsonResponse(null, 200, [
			'Access-Control-Allow-Headers' => ['Authorization', 'Content-Type']
		]);

		if (Request::METHOD_OPTIONS === $request->getMethod()) {
			$response->headers->add(['Access-Control-Max-Age' => 7200]);
			return $response;
		}

		$ac_config = $this->getParameter('maw.config');
		$systemConfigArray = $doctrine->getManager()->getRepository(SystemConfig::class)->findOneBy([]);

		$urlFormatter = new URLFormatting();
		$endpoint = $urlFormatter->correctURL($ac_config['TAOPIXONLINEURL'], "/", true) . 'api/experience/deleteAssignment';
		$httpClient = new Client(['verify' => CommonFunctions::getCurlPEMFilePath($this->getParameter('kernel.project_dir'))]);
		$responseBody = "";
		$returnData = new \stdClass();

		try {
			$responseGuzzle = $httpClient->post($endpoint, [
				'form_params' => [
					'assignmentKey' => $request->get('assignmentKey', ''),
					'templateType' => $request->get('templateType', ''),
					'assignmentType' => $request->get('assignmentType', ''),
					'productType' => $request->get('productType', ''),
					'retroPrint' => $request->get('retroPrint', ''),
					'systemkey' => $systemConfigArray->getSystemKey(),
					'tenantid' => $systemConfigArray->getTenantid()
				]
			]);

			// Get the contents of the body of the response.
			$responseBody = $responseGuzzle->getBody()->getContents();
			$returnData = json_decode($responseBody);
		} catch (\GuzzleHttp\Exception\ClientException|\GuzzleHttp\Exception\ServerException $e) {
			$returnData->error = new \stdClass();
			$returnData->error->code = 400;
			$returnData->error->fullMessage = 'str_ErrorFailedToDeleteData';
        }

        return $response->setEncodingOptions(JSON_UNESCAPED_UNICODE)
            ->setData($returnData)
            ->setStatusCode((!isset($returnData->error->code)) ? 200 : $returnData->error->code);
	}

	/**
	 * Get list of experience data
	 *
	 * @param Request $request
	 * @param ManagerRegistry $doctrine
	 * @return JsonResponse
	 */
	#[Route('/api/experience/getOverviewListData', name: 'getOverviewListData', methods: [Request::METHOD_OPTIONS, Request::METHOD_GET])]
	public function getExperienceOverviewListData(Request $request, ManagerRegistry $doctrine): JsonResponse
	{
		$response = new JsonResponse(null, 200, [
			'Access-Control-Allow-Headers' => ['Authorization', 'Content-Type']
		]);

		if (Request::METHOD_OPTIONS === $request->getMethod()) {
			$response->headers->add(['Access-Control-Max-Age' => 7200]);
			return $response;
		}

		try {
			$productType = intval($request->get('productType', -1));
			$retroPrint = $request->get('retroPrint', false);
			$mode = intval($request->get('mode', 0));
			$searchTerm = $request->get('search', '');
			$brandKeyFilter = $request->get('brandKeyFilter', '');
			$page = intval($request->get('page', 1));
			$userId = intval($request->get('userId', 0));

			$ac_config = $this->getParameter('maw.config');
			$systemConfigArray = $doctrine->getManager()->getRepository(SystemConfig::class)->findOneBy([]);


			$returnData = [
				'themes' => [],
				'assignment' => [],
				'collections' => [],
				'brands' => [],
				'templates' => [],
				'page' => $page,
				'features' => [
					'ai' => ($systemConfigArray->getConfig() & 64) ? true : false,
					'retroPrints' => (isset($ac_config['ALLOWRETROPRINTS']) && $ac_config['ALLOWRETROPRINTS'] === "1"),
					'scaleBeforeUpload' => (isset($ac_config['ALLOWIMAGESCALINGBEFORE']) && $ac_config['ALLOWIMAGESCALINGBEFORE'] === "1")
				],
				'userPrefs' => '',
				'totalRecords' => 0
			];

			if ((ExperienceAssignMode::BRAND_KEY->value === $mode) || $page === 1) {
				$userPrefs = $doctrine->getManager()->getRepository(UserSystemPreferencesEntity::class)->findOneBy(['userId' => $userId, 'type' => UserSystemPreferences\Type::BULKASSIGN_VIEW->value]);

				$urlFormatter = new URLFormatting();
				$endpoint = $urlFormatter->correctURL($ac_config['TAOPIXONLINEURL'], "/", true);

				$httpClient = new Client(['base_uri' => $endpoint, 'verify' => CommonFunctions::getCurlPEMFilePath($this->getParameter('kernel.project_dir'))]);
				$responseGuzzle = $httpClient->get(
							'api/experience/getexperienceassignmentdata'
						.   '?productType=' . $productType
						.	 '&retroPrint=' . $retroPrint
						.   '&mode=' . $mode
						.   '&search=' . $searchTerm
						.   '&systemkey=' . $systemConfigArray->getSystemKey()
						.   '&tenantid=' . $systemConfigArray->getTenantid()
				, []);
				$onlineResponse = json_decode($responseGuzzle->getBody()->getContents());
				$themeManager = new ThemeManager($httpClient, $doctrine);

				$returnData['themes'] = $themeManager->getThemeData()['payload']['themeList'];
				$returnData['assignment'] = $onlineResponse->data->assignments;
				$returnData['templates'] = $onlineResponse->data->templates;
				$returnData['userPrefs'] = ($userPrefs) ? json_decode($userPrefs->getData()) : '';

				$brandCode = '*';
				$keyCode = '*';

				if ($brandKeyFilter !== '') {
					[$brandCode, $keyCode] = explode('.', $brandKeyFilter);
					$brandsArray = $doctrine->getManager()->getRepository(Brand::class)->findBy(['code' => $brandCode],['applicationName' => 'ASC']);
				} else {
					$brandsArray = $doctrine->getManager()->getRepository(Brand::class)->findBy([],['applicationName' => 'ASC']);
				}

				if($keyCode === '*') {
					$licenseKeys = $doctrine->getManager()->getRepository(LicenseKey::class)->findBy([],['name' => 'ASC']);
				} else {
					$licenseKeys = $doctrine->getManager()->getRepository(LicenseKey::class)->findBy(['groupCode' => $keyCode],['name' => 'ASC']);
				}

				foreach ($brandsArray as $brand) {
					$returnData['brands'][$brand->getCode()] = [
						'code' => $brand->getCode(),
						'name' => $brand->getApplicationName(),
						'licenseKeys' => []
					];
				}

				foreach ($licenseKeys as $licenseKey) {
					if (isset($returnData['brands'][$licenseKey->getWebBrandCode()])) {
						$returnData['brands'][$licenseKey->getWebBrandCode()]['licenseKeys'][] = [
							'code' => $licenseKey->getGroupCode(),
							'name' => $licenseKey->getName()
						];
					}
				}
			}

			if (ExperienceAssignMode::PRODUCT->value === $mode)
			{
				$productsArray = $doctrine->getManager()->getRepository(ProductCollectionLink::class)->getProductsForExperienceOverview([
					'searchTerm' => $searchTerm,
					'availableOnline' => true,
					'collectionType' => $productType,
					'retroPrints' => ($retroPrint === 'true')
				], $page);

				foreach($productsArray as $product) {
					$index = $product['collectionCode'] . '.' . $product['productCode'];

					if (!isset($returnData['collections'][$product['collectionCode']]['collectionName'])) {
						$returnData['collections'][$product['collectionCode']]['collectionName'] = $product['collectionName'];
					}

					$returnData['collections'][$product['collectionCode']]['products'][$product['productCode']] = [
						'code' => $product['productCode'],
						'name' => $product['productName']
					];
				}

				if ($page === 1) {
					$total = $doctrine->getManager()->getRepository(ProductCollectionLink::class)->getProductsForExperienceOverview([
						'searchTerm' => $searchTerm,
						'availableOnline' => true,
						'collectionType' => $productType,
						'retroPrints' => ($retroPrint === 'true')
					], $page, true);
					$totalCount = ((count($total) > 0 && array_key_exists('count', $total[0])) ? $total[0]['count'] : 0);
					$returnData['totalRecords'] = $totalCount;
				}
			}
			else
			{
				foreach($returnData['brands'] as $index => $brand) {
					foreach($brand['licenseKeys'] as $keyIndex => $key) {
						if ($searchTerm !== '') {
							if (
								!CommonFunctions::like($key['name'], $searchTerm) && !CommonFunctions::like($brand['name'], $searchTerm)
								&& !CommonFunctions::like($key['code'], $searchTerm) && !CommonFunctions::like($brand['code'], $searchTerm)
							) {
								unset($returnData['brands'][$index]['licenseKeys'][$keyIndex]);
							}
						}
					}

					if (count($returnData['brands'][$index]['licenseKeys']) === 0
						&& !CommonFunctions::like($brand['code'], $searchTerm)
						&& !CommonFunctions::like($brand['name'], $searchTerm)
					) {
						if ($searchTerm !== '') {
							unset($returnData['brands'][$index]);
						}
					}
				}
			}
		} catch (\GuzzleHttp\Exception\ClientException|\GuzzleHttp\Exception\ServerException $e) {
			$returnData['error'] = ['code' => 400, 'fullMessage' => 'str_ErrorFailedToLoadData'];
		}

		return $response
					->setData($returnData)
					->setStatusCode((!isset($returnData['error']['code'])) ? 200 : $returnData['error']['code']);
	}

	/**
	 * Generate Experience Upgrade Data
	 *
	 * @param Request $request
	 * @param ManagerRegistry $doctrine
	 * @return JsonResponse
	 */
	#[Route('/api/experience/upgrade', name: 'upgradeData', methods: [Request::METHOD_OPTIONS, Request::METHOD_GET])]
	public function upgrade(Request $request, ManagerRegistry $doctrine, SerializerInterface $serializer): JsonResponse
	{
		$response = new JsonResponse(null, 200, [
			'Access-Control-Allow-Headers' => ['Authorization', 'Content-Type']
		]);

		if (Request::METHOD_OPTIONS === $request->getMethod()) {
			$response->headers->add(['Access-Control-Max-Age' => 7200]);
			return $response;
		}

		$returnData = [
			'data' => [],
			'error' => []
		];

		try {
			$ac_config = $this->getParameter('maw.config');

			$onlineData = [
				'ALLOWRETROPRINTS' => (isset($ac_config['ALLOWRETROPRINTS']) && $ac_config['ALLOWRETROPRINTS'] === "1") ? true : false,
				'SETTINGS' => [
					'licenseKeys' => [],
					'brands' => [],
					'products' => []
				],
				'EDITOR' => [
					'licenseKeys' => [],
					'brands' => [],
					'products' => []
				]
			];

			$systemConfigArray = $doctrine->getManager()->getRepository(SystemConfig::class)->findOneBy([]);

			foreach ($doctrine->getManager()->getRepository(EntityProduct::class)->getExperienceUpgradeData(Query::HYDRATE_OBJECT) as $product) {
				if (isset($onlineData['SETTINGS']['products'][$product['code']])) {
					array_push($onlineData['SETTINGS']['products'][$product['code']]['collections'], $product['collectionCode']);
				} else {
					$onlineData['SETTINGS']['products'][$product['code']] = [
						'useDefaultImageScalingBefore' => $product['useDefaultImageScalingBefore'],
						'scaleBeforeUpload' => $product['imageScalingBeforeEnabled'],
						'maxScaleBeforeUploadMP' => $product['imageScalingBefore'],
						'collections' => [$product['collectionCode']]
					];
				}
			}

			foreach ($doctrine->getManager()->getRepository(LicenseKey::class)->getExperienceUpgradeData() as $licenseKey) {
				$onlineData['SETTINGS']['licenseKeys'][$licenseKey['groupCode']] = [
					'groupCode' => $licenseKey['groupCode'], //->getGroupCode(),
					'webBrandCode' => $licenseKey['webBrandCode'], //->webBrandCode(),
					'savePromptDelay' => $licenseKey['onlineDesignerSigninRegisterPromptDelay'],
					'scaleBeforeUpload' => ($licenseKey['imageScalingBeforeEnabled'] ===1),
					'maxScaleBeforeUploadMP' => $licenseKey['imageScalingBefore'],
					'scaleAfterUpload' => ($licenseKey['imageScalingAfterEnabled']===1),
					'maxScaleAfterUploadMP' => $licenseKey['imageScalingAfter'],
					'enhanceAllUserImagesAuto' => ($licenseKey['automaticallyApplyPerfectlyClear']===1),
					'allowCustomersToOptOut' => ($licenseKey['allowUsersToTogglePerfectlyClear']===1),
					'logoLinkUrl' => $licenseKey['onlineDesignerLogoLinkUrl'],
					'logoLinkToolTipText' => $licenseKey['onlineDesignerLogoLinkTooltip'],
					'units' => Unit\Unit::cases()[(int) $licenseKey['sizeAndPositionMeasurementUnits']]->name,
					'guestWorkflowMode' => $licenseKey['onlineDesignerGuestWorkflowMode']
				];
				$onlineData['EDITOR']['licenseKeys'][$licenseKey['groupCode']] = [
					'webBrandCode' => $licenseKey['webBrandCode'],
					'onlineEditorMode' => $licenseKey['onlineEditorMode'],
					'enableSwitchingEditor' => ($licenseKey['enableSwitchingEditor'] === 1),
					'recommendedAveragePicturesPerPage' => $licenseKey['averagePicturesPerPage']
				];
			}

			$brands = $doctrine->getManager()->getRepository(Brand::class)->findAll();

			foreach ($brands as $brand) {
				$onlineData['SETTINGS']['brands'][$brand->getCode()] = [
					'webBrandCode' => $brand->getCode(),
					'savePromptDelay' => $brand->getOnlineDesignerSigninRegisterPromptDelay(),
					'scaleBeforeUpload' => $brand->isImageScalingBeforeEnabled(),
					'maxScaleBeforeUploadMP' => $brand->getImageScalingBefore(),
					'scaleAfterUpload' => $brand->isImageScalingAfterEnabled(),
					'maxScaleAfterUploadMP' => $brand->getImageScalingAfter(),
					'enhanceAllUserImagesAuto' => $brand->isAutomaticallyApplyPerfectlyClear(),
					'allowCustomersToOptOut' => $brand->isAllowUsersToTogglePerfectlyClear(),
					'logoLinkUrl' => $brand->getOnlineDesignerLogoLinkUrl(),
					'logoLinkToolTipText' => $brand->getOnlineDesignerLogoLinkTooltip(),
					'units' => Unit\Unit::cases()[(int) $brand->getSizeAndPositionMeasurementUnits()]->name
				];
				$onlineData['EDITOR']['brands'][$brand->getCode()] = [
					'onlineEditorMode' => $brand->getOnlineEditorMode(),
					'enableSwitchingEditor' => $brand->isEnableSwitchingEditor(),
					'recommendedAveragePicturesPerPage' => $brand->getAveragePicturesPerPage()
				];
			}

			$urlFormatter = new URLFormatting();
			$endpoint = $urlFormatter->correctURL($ac_config['TAOPIXONLINEURL'], "/", true) . 'api/experience/storeupgradedata';

			$httpClient = new Client(['verify' => CommonFunctions::getCurlPEMFilePath($this->getParameter('kernel.project_dir'))]);
			$responseGuzzle = $httpClient->post($endpoint, [
				'form_params' => [
					'onlineData' => json_encode($onlineData,true),
					'systemkey' => $systemConfigArray->getSystemKey(),
					'tenantid' => $systemConfigArray->getTenantid(),
				]
			]);

			$returnData['data'] = json_decode($responseGuzzle->getBody()->getContents());
		} catch (\GuzzleHttp\Exception\ClientException|\GuzzleHttp\Exception\ServerException $e) {
			$returnData['error'] = ['code' => 400, 'fullMessage' => 'str_ErrorFailedToSaveData'];
		}

		return $response
					->setData($returnData['data'])
					->setStatusCode((!isset($returnData['error']['code'])) ? 200 : $returnData['error']['code']);
	}

	/**
	 * save column data to preferences
	 *
	 * @param Request $request
	 * @param ManagerRegistry $doctrine
	 * @return JsonResponse
	 */
	#[Route('/api/experience/saveAssignmentColumnDataToDB', name: 'saveAssignmentColumnDataToDB', methods: [Request::METHOD_OPTIONS, Request::METHOD_POST])]
	public function saveAssignmentColumnDataToDB(Request $request, ManagerRegistry $doctrine): JsonResponse
	{
		$response = new JsonResponse(null, 200, [
			'Access-Control-Allow-Headers' => ['Authorization', 'Content-Type']
		]);

		if (Request::METHOD_OPTIONS === $request->getMethod()) {
			$response->headers->add(['Access-Control-Max-Age' => 7200]);
			return $response;
		}

		$data = $request->get('data', '');
		$userId = $request->get('userId', '');
		$type = UserSystemPreferences\Type::BULKASSIGN_VIEW->value;
		$returnCode = 200;

		try {
			$dataLength = mb_strlen($data);

			if ($dataLength > 49152) {
				$data = gzcompress($data, 9);
			} else {
				$dataLength = 0;
			}

			$formattedData = [
				'type' => $type,
				'userId' => $userId,
				'data' => $data,
				'dataLength' => $dataLength
			];

			$item = $doctrine->getRepository(UserSystemPreferencesEntity::class)->findOneBy([
				'userId' => $userId,
				'type' => $type
			]) ?? new UserSystemPreferencesEntity;
			$item->populate($formattedData);
			$doctrine->getManager()->persist($item);
			$doctrine->getManager()->flush();

		} catch (\Throwable $e) {
			error_log('****************************');
			error_log(print_r($e,true));
			error_log('****************************');
			$returnCode = 400;
        }

        return $response->setEncodingOptions(JSON_UNESCAPED_UNICODE)
            ->setStatusCode($returnCode);
	}
}
