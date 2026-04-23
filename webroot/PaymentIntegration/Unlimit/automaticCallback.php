<?php
require __DIR__ . '/../../../libs/external/vendor/autoload.php';

// we must perform the standard initialization as we have bypassed the Fusebox framework
error_reporting(E_ALL);
ini_set('log_errors', true);

// step back to the webroot directory
chdir('../../');

require_once('../Utils/UtilsCoreIncludes.php');
require_once('../Order/Order_control.php');

// read the config file
$ac_config = UtilsObj::readConfigFile('../config/mediaalbumweb.conf');

// get the constants
$gConstants = DatabaseObj::getConstants();
$gSession = AuthenticateObj::getCurrentSessionData();
$gAuthSession = true;
$params = json_decode(@file_get_contents('php://input'),true);

$response = [
    "code" => 200,
    "response" => "{\"data\": ''}"
];

if (array_key_exists('merchant_order', $params)) {
    if (array_key_exists('id', $params['merchant_order'])) {
        $_GET['ref'] = explode('@@', $params['merchant_order']['id'])[0];
        $gSession['order']['ccitype'] = 'UNLIMIT';
        $response = Order_control::ccAutomaticCallback();
    }
} 

echo json_encode($response,true);

?>