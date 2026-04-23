<?php

namespace Taopix\Connector\Shopify;

require_once __DIR__ . '/../../../../libs/external/vendor/autoload.php';

trait CurlTrait
{
	protected function initCURL(string $pURL, array $pCurlOptions): array
	{
		$curl = curl_init();

		// Common settings.
		curl_setopt($curl, CURLOPT_URL, $pURL);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_USERAGENT, \UtilsObj::getArrayParam($_SERVER, 'HTTP_USER_AGENT'));
		curl_setopt($curl, CURLOPT_CAINFO, \UtilsObj::getCurlPEMFilePath());
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
		curl_setopt($curl, CURLOPT_HTTPHEADER,array('Origin: myshopify.com'));

		// Set options passed to the function.
		curl_setopt_array($curl, $pCurlOptions);

		$response = curl_exec($curl);
		$httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		curl_close($curl);

		$responseObj = json_decode($response);

		return [$httpCode, $responseObj];
	}	
}
