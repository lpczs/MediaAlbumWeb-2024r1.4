<?php

namespace Taopix\ControlCentre\Controller;

use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Taopix\ControlCentre\Entity\Brand;
use Taopix\ControlCentre\Common\URLFormatting;
use Taopix\ControlCentre\Common\CommonFunctions;
use Taopix\ControlCentre\Helper\Online\Branding;
use GuzzleHttp\Client;

class BrandController extends AbstractController
{
    #[Route('/api/brand/applyBrandUIConfig', name: 'applyBrandUIConfig', methods: [Request::METHOD_OPTIONS, Request::METHOD_POST])]
    public function applyBrandUIConfig(Request $request, ManagerRegistry $doctrine): JsonResponse
    {
        $response = new JsonResponse(null, 200, [
            'Access-Control-Allow-Headers' => ['Authorization', 'Content-Type']
        ]);

        if (Request::METHOD_OPTIONS === $request->getMethod()) {
            $response->headers->add(['Access-Control-Max-Age' => 7200]);
            return $response;
        }

        $brandId = $request->getPayload()->get('brandId', -1);
        $endPoint = $request->getPayload()->get('endpoint');

        $helper = new Branding($this->getClient($doctrine), $doctrine);

        try {
            $result = $helper->applyToOnline($brandId, $endPoint);
        } catch (\Exception $e) {
            return $response->setStatusCode(Response::HTTP_BAD_REQUEST)->setData($e->getMessage());
        }

        if (Response::HTTP_OK !== $result->getStatusCode()) {
            $response->setStatusCode(Response::HTTP_BAD_REQUEST);
        }

        return $response;
    }

    private function getClient(ManagerRegistry $doctrine)
    {
        global $ac_config;
        $ac_config = $this->getParameter('maw.config');

        $defaultBrand = $doctrine->getRepository(Brand::class)->findOneBy(['code' => '']);

        //TAOPIXONLINEURL FROM AC_CONFIG OR DEFAULT BRAND ONLINE DESIGNER URL?
        $endpoint = (new URLFormatting())->correctURL($defaultBrand->getOnlineDesignerUrl());

        return new Client([
            'base_uri' => $endpoint,
            'verify' => CommonFunctions::getCurlPEMFilePath($this->getParameter('kernel.project_dir'))
        ]);
    }
}
