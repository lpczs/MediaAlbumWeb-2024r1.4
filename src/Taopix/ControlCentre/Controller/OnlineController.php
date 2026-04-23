<?php

namespace Taopix\ControlCentre\Controller;

use Exception;
use Doctrine\Persistence\ManagerRegistry;
use Http\Discovery\Exception\NotFoundException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Serializer\SerializerInterface;
use Taopix\ControlCentre\Entity\ApplicationFile;
use Taopix\ControlCentre\Entity\Brand;
use Taopix\ControlCentre\Entity\CacheData;
use Taopix\ControlCentre\Entity\LicenseKey;
use Taopix\ControlCentre\Entity\PriceLink;
use Taopix\ControlCentre\Entity\Product;
use Taopix\ControlCentre\Entity\ProductCollectionLink;
use Taopix\ControlCentre\Entity\User;
use Symfony\Component\Routing\Annotation\Route;
use Taopix\ControlCentre\Helper\Cache\BuildCacheData;
use Taopix\ControlCentre\Helper\Online\Formatter\LicenseData;
use Taopix\ControlCentre\Helper\Online\ProjectData;
use Taopix\ControlCentre\Helper\Online\Formatter\BrandData;
use Taopix\ControlCentre\Helper\Online\PriceAndComponents;

class OnlineController extends AbstractController
{
	#[Route('/api/online/createCacheData', name: 'createCacheData', methods: [Request::METHOD_OPTIONS, Request::METHOD_POST])]
	public function getLicenseAndBrandInfo(Request $request, ManagerRegistry $doctrine, SerializerInterface $serializer): JsonResponse
	{
		$response = new JsonResponse(null, 200, [
			'Access-Control-Allow-Headers' => ['Authorization', 'Content-Type']
		]);

		if (Request::METHOD_OPTIONS === $request->getMethod()) {
			$response->headers->add(['Access-Control-Max-Age' => 7200]);
			return $response;
		}

		$details = $request->toArray();
		$projectDataBuilder = new ProjectData($doctrine, $serializer, $this->getParameter('maw.config'));

		return $response->setData($projectDataBuilder->getData($details));
	}

	#[Route('/api/online/test', name: 'test', methods: [Request::METHOD_OPTIONS, Request::METHOD_GET])]
	public function test(Request $request, ManagerRegistry $doctrine)
	{
		$response = new JsonResponse(null, 200, [
			'Access-Control-Allow-Headers' => ['Authorization', 'Content-Type']
		]);

		if (Request::METHOD_OPTIONS === $request->getMethod()) {
			$response->headers->add(['Access-Control-Max-Age' => 7200]);
			return $response;
		}

		$data = [];

        return $response->setData($data);
	}

	public function getProductConfig(Request $request, ManagerRegistry $doctrine): JsonResponse
	{
		$details = $request->toArray();
		$response = new JsonResponse();

		$user = null;
		if (0 < $details['userId']) {
			$user = $doctrine->getRepository(User::class)->findOneBy(['id' => $details['userId']]);
		}

		if (($details['validateLicenseKey'] ?? false) && null !== $user) {
			if ($user->getGroupCode() !== $details['groupCode']) {
				//TODO: Possibly switch the license code here if required.
				throw new NotFoundHttpException('Invalid license key');
			}
		}

		$licenseKey = $doctrine->getRepository(LicenseKey::class)->findOneBy(['groupCode' => $details['groupCode']]);

		// Check we found the license key and it is active and available for online
		if (null === $licenseKey || !$licenseKey->isActive() || !$licenseKey->isAvailableOnline()) {
			throw new NotFoundHttpException('License Key not active');
		}
		$brand = $doctrine->getRepository(Brand::class)->findOneBy([
			'code' => $licenseKey->getWebBrandCode()
		]);

		// Validate that we found the brand for the license key.
		if (null === $brand || !$brand->isActive()) {
			throw new NotFoundHttpException('Brand code');
		}
		$applicationFile = $doctrine->getRepository(ApplicationFile::class)->findOneBy([
			'ref' => $details['collectionCode'],
		]);

		// Validate that the collection is active.
		if (null === $applicationFile || !$applicationFile->isActive()) {
			throw new NotFoundHttpException('Application file not active');
		}

		$productCollectionLink = $doctrine->getRepository(ProductCollectionLink::class)->findOneBy([
			'collectionCode' => $details['collectionCode'],
			'productCode' => $details['layoutCode'],
		]);

		// Validate that the layout is found and active in online.
		if (null === $productCollectionLink || !$productCollectionLink->isAvailableOnline()) {
			throw new NotFoundHttpException('Product Collection');
		}

		$product = $doctrine->getRepository(Product::class)->findOneBy([
			'code' => $details['layoutCode'],
		]);

		if (null === $product || !$product->isActive()) {
			throw new NotFoundHttpException('Product');
		}

		$details['licenseKey'] = $licenseKey->asArray();
		$details['brand'] = $brand->asArray();
		$details['collection'] = $productCollectionLink->asArray();
		$details['product'] = $product->asArray();
		$details['user'] = $user->asArray();
		$details['priceLink'] = $doctrine->getRepository(PriceLink::class)->getComponentPaths(['groupCode' => $details['groupCode']]);

		return $response->setData($details);
	}
}
