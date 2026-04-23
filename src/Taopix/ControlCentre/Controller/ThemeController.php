<?php

namespace Taopix\ControlCentre\Controller;

use Doctrine\Persistence\ManagerRegistry;
use GuzzleHttp\Client;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Taopix\ControlCentre\Common\URLFormatting;
use Taopix\ControlCentre\Entity\Brand;
use Taopix\ControlCentre\Helper\Theme\ThemeManager;
use Taopix\ControlCentre\Common\CommonFunctions;

class ThemeController extends AbstractController
{
    /**
     * Return a list of themes
     *
     * @param Request $request
     * @param ManagerRegistry $doctrine
     * @return JsonResponse
     */
    #[Route('/api/theme/list', name: 'themeList', methods: [Request::METHOD_OPTIONS, Request::METHOD_GET])]
    public function getThemeList(Request $request, ManagerRegistry $doctrine): JsonResponse
    {
        $response = new JsonResponse(null, 200, [
            'Access-Control-Allow-Headers' => ['Authorization', 'Content-Type']
        ]);

        if (Request::METHOD_OPTIONS === $request->getMethod()) {
            $response->headers->add(['Access-Control-Max-Age' => 7200]);
            return $response;
        }

        $manager = new ThemeManager($this->getClient($doctrine), $doctrine);
        $payload = $manager->getThemeData();

        return $response->setData($payload);
    }

    #[Route('/api/theme/save-theme', name: 'saveTheme', methods: [Request::METHOD_OPTIONS, Request::METHOD_POST])]
    public function saveTheme(Request $request, ManagerRegistry $doctrine): JsonResponse
    {
        $response = new JsonResponse(null, 200, [
            'Access-Control-Allow-Headers' => ['Authorization', 'Content-Type']
        ]);

        if (Request::METHOD_OPTIONS === $request->getMethod()) {
            $response->headers->add(['Access-Control-Max-Age' => 7200]);
            return $response;
        }

        try {
            $theme = (new ThemeManager($this->getClient($doctrine), $doctrine))->updateTheme($request->getContent());
            $response->setData($theme);
        } catch(\Exception $e) {
            $response->setStatusCode(Response::HTTP_BAD_REQUEST)->setContent($e->getMessage());
        }

        return $response;
    }

    #[Route('/api/theme/delete-theme', name: 'deleteTheme', methods: [Request::METHOD_OPTIONS, Request::METHOD_POST])]
    public function deleteTheme(Request $request, ManagerRegistry $doctrine): JsonResponse
    {
        $response = new JsonResponse(null, 200, [
            'Access-Control-Allow-Headers' => ['Authorization', 'Content-Type']
        ]);

        if (Request::METHOD_OPTIONS === $request->getMethod()) {
            $response->headers->add(['Access-Control-Max-Age' => 7200]);
            return $response;
        }

        $params = json_decode($request->getContent(), true);

        try {
            (new ThemeManager($this->getClient($doctrine), $doctrine))->deleteTheme($params);
        } catch (\Exception $e) {
            $response->setStatusCode(Response::HTTP_BAD_REQUEST)->setContent($e->getMessage());
        }

        return $response;
    }

    #[Route('/api/theme/save-colour-scheme', name: 'saveColourScheme', methods: [Request::METHOD_OPTIONS, Request::METHOD_POST])]
    public function saveColourScheme(Request $request, ManagerRegistry $doctrine): JsonResponse
    {
        $response = new JsonResponse(null, 200, [
            'Access-Control-Allow-Headers' => ['Authorization', 'Content-Type']
        ]);

        if (Request::METHOD_OPTIONS === $request->getMethod()) {
            $response->headers->add(['Access-Control-Max-Age' => 7200]);
            return $response;
        }

        try {
            $theme = (new ThemeManager($this->getClient($doctrine), $doctrine))
                ->updateColourScheme($request->getContent());
            $response->setData($theme);
        } catch(\Exception $e) {
            $response->setStatusCode(Response::HTTP_BAD_REQUEST)->setContent($e->getMessage());
        }

        return $response;
    }

    #[Route('/api/theme/delete-colour-scheme', name: 'deleteColourScheme', methods: [Request::METHOD_OPTIONS, Request::METHOD_POST])]
    public function deleteColourScheme(Request $request, ManagerRegistry $doctrine): JsonResponse
    {
        $response = new JsonResponse(null, 200, [
            'Access-Control-Allow-Headers' => ['Authorization', 'Content-Type']
        ]);

        if (Request::METHOD_OPTIONS === $request->getMethod()) {
            $response->headers->add(['Access-Control-Max-Age' => 7200]);
            return $response;
        }

        $params = json_decode($request->getContent(), true);

        try {
            (new ThemeManager($this->getClient($doctrine), $doctrine))->deleteColourScheme($params);
        } catch (\Exception $e) {
            $response->setStatusCode(Response::HTTP_BAD_REQUEST)->setContent($e->getMessage());
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
