<?php

namespace Taopix\ControlCentre\Controller;

use Doctrine\Persistence\ManagerRegistry;
use GuzzleHttp\Client;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Taopix\ControlCentre\Common\URLFormatting;
use Taopix\ControlCentre\Entity\Brand;
use Taopix\ControlCentre\Common\CommonFunctions;
use Taopix\ControlCentre\Entity\Constants;
use Taopix\ControlCentre\Entity\SystemConfig;

define('__LOCALROOT__', dirname(__FILE__, 5));
require_once(__LOCALROOT__ . '/libs/external/vendor/autoload.php');

require_once(__LOCALROOT__ . '/AdminTaopixOnlineProductURLAdmin/AdminTaopixOnlineProductURLAdmin_model.php');

class ProductsController extends FuseBoxController
{
    /**
     * Return a list of themes
     *
     * @param Request $request
     * @param ManagerRegistry $doctrine
     * @return JsonResponse
     */
    #[Route('/api/products/list', name: 'list', methods: [Request::METHOD_OPTIONS, Request::METHOD_GET])]
    public function getProductsList(Request $request, ManagerRegistry $doctrine): JsonResponse
    {
        if (Request::METHOD_OPTIONS === $request->getMethod()) {
            $this->response->headers->add(['Access-Control-Max-Age' => 7200]);
            return $this->response;
        }
        $config = $doctrine->getRepository(SystemConfig::class)->find(1);

        $_POST['groupcode'] = $_GET['groupcode'];
        $_POST['filter'] = $_GET['filter'] ?? 'ACTIVE';
		$_POST['groupdatastatus'] = $_GET['groupdatastatus'] ?? 0;
		$_POST['groupdata'] = $_GET['groupdata'] ?? "";
		$_POST['collectioncode'] = $_GET['collectioncode'] ?? "-1";
		$_POST['wizstatus']= $_GET['wizstatus'] ?? 0;
		$_POST['wizparams'] = $_GET['wizparams'] ?? "";
		$_POST['uioverridemode'] = $_GET['uioverridemode'] ?? "-1";
		$_POST['aimodeoverride'] = $_GET['aimodeoverride'] ?? "-1";

        if ($config->getConfig() & 2)
        {
        	$_POST['companycode'] = $_GET['companycode'] ?? '';
        }

		// read any custom parameter data
		$_POST['cpstatus'] = $_GET['cpstatus'] ?? 0;
		$_POST['cpdata'] = json_decode($_GET['cpdata'], true) || [];

        $payload = \AdminTaopixOnlineProductURLAdmin_model::getGridData();

        return $this->response->setData($payload['urldata']);
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
