<?php

namespace Taopix\ControlCentre\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;

define('__ROOT__', dirname(__FILE__, 5));
require_once(__ROOT__ . '/libs/external/vendor/autoload.php');
require_once(__ROOT__ . '/Utils/Utils.php');

require_once (__ROOT__ . '/Utils/UtilsConstants.php');
require_once (__ROOT__ . '/Utils/UtilsDatabase.php');
require_once(__ROOT__ . '/Utils/UtilsSmarty.php');
require_once(__ROOT__ . '/Utils/UtilsAuthenticate.php');
require_once(__ROOT__ . '/Utils/UtilsLocalization.php');
require_once(__ROOT__ . '/AdminCustomers/AdminCustomers_model.php');
require_once(__ROOT__ . '/AjaxAPI/AjaxAPI_model.php');

class FuseBoxController extends AbstractController
{
    protected JsonResponse $response;

    public function __construct()
    {
        global $ac_config;
        $ac_config = \UtilsObj::readConfigFile(__ROOT__ . '/config/mediaalbumweb.conf');

        global $gSession;
        $gSession = \AuthenticateObj::getCurrentSessionData();

        global $gConstants;
        $gConstants = \DatabaseObj::getConstants();

        $this->response = new JsonResponse(null, 200, [
            'Access-Control-Allow-Headers' => ['Authorization', 'Content-Type']
        ]);
    }
}
