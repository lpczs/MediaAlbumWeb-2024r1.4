<?php
ini_set('opcache.enable', 0);
require_once __DIR__ . '/../../../Order/PaymentIntegration/Request/CurlHandler.php';

function cURLRequest($pURL, $parameterArray, $method = 'POST', $headers = [], $json = false)
{
    //open connection
    $ch = curl_init();

    //set the url, number of POST vars, POST data
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_URL, $pURL);

    if ($method === 'POST') {
        curl_setopt($ch, CURLOPT_POST, true);
        if ($json) {
            $params = json_encode($parameterArray);
        } else {
            $params = http_build_query($parameterArray);
        }
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
    } else {
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
    }

    //execute post
    $result = curl_exec($ch);

    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $err = curl_error($ch);

    if ($err) {
        error_log("cURL Error:" . $err);
    }

    //close connection
    curl_close($ch);

    return ['code' => $httpcode, 'response' => $result];
}

$accessToken = $_POST['access_token'];
$requestId = $_POST['request_id'];
$merchantOrderId = $_POST['merchant_order_id'];
$unlimitServer = $_POST['unlimit_server'];
$url = $unlimitServer . "payments/?request_id=" . $requestId . "&merchant_order_id=" . $merchantOrderId;

$authHeader = ["Authorization: Bearer " . $accessToken, "Accept: application/json", "Content-Type: application/json"]; 
$response = cURLRequest($url, [], 'GET', $authHeader, true);

//401 {"code": 401,"response": "{\"name\":\"INVALID_TOKEN\",\"message\":\"Expired access token\"}"}
if ($response['code'] === 401)
{
    header("HTTP/1.1 401 Unauthorized");
    header('Content-Encoding: none');
    header('Content-Length: '.ob_get_length());
    header('Connection: close');
    flush();
    exit;
}

echo json_encode($response,true);

?>