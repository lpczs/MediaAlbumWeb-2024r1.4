<?php

namespace Taopix\ControlCentre\Helper\Online;

use GuzzleHttp\Client;
use Doctrine\Persistence\ManagerRegistry;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Taopix\ControlCentre\Entity\SystemConfig;
use Taopix\ControlCentre\Entity\Brand;
use Taopix\ControlCentre\Common\URLFormatting;

class Branding
{
    private ?SystemConfig $systemConfig;

    public function __construct(private readonly Client $client, private readonly ManagerRegistry $doctrine)
    {
        $this->systemConfig = $this->doctrine->getRepository(SystemConfig::class)->findOneBy([]);
    }

    public function applyToOnline(int $brandId, string $method): ResponseInterface | false
    {
        $brand = $this->doctrine->getRepository(Brand::class)->find($brandId);
        // if we cannot find the brand or the one we find is the default
        if (!$brand) {
            throw new \Exception('Unable to find brand');
        }

        if ('' === $brand->getOnlineApiUrl()) {
            throw new \Exception('No Online API Url configured');
        }

        if ('' === $brand->getOnlineUiUrl()) {
           throw new \Exception('No Online UI URL configured');
        }

        $uiDir = '';
        if ('' === $brand->getCode()) {
            $parsedUiUrl = parse_url($brand->getOnlineUiUrl());
            $uiDirParts = \explode('/', \trim(($parsedUiUrl['path'] ?? ''), " \n\r\t\v\0\\"));
            if (!empty($uiDirParts)) {
                $uiDir = array_pop($uiDirParts);
            }
        }
        $ciphering = "aes-128-cbc";
		$options = 0;
        $brandCode = '' === $brand->getCode() ? $uiDir : $brand->getCode();
        $brandName = '' === $brand->getCode() ? $uiDir : $brand->getName();
        $encryption_iv = $brand->getOnlineAppKeyEntropyValue();

        if ($encryption_iv == '')
        {
            $encryption_iv = \bin2hex(\openssl_random_pseudo_bytes(\openssl_cipher_iv_length($ciphering)));

            $brand->setOnlineAppKeyEntropyValue($encryption_iv);
            $this->doctrine->getManager()->persist($brand);
            $this->doctrine->getManager()->flush();
        }

        // Validate online and ui are hosted on the same machine.
        $onlineApiUrl = parse_url($brand->getOnlineApiUrl(), PHP_URL_HOST);
        $uiUrl = parse_url($brand->getOnlineUiUrl(), PHP_URL_HOST);
        if ($onlineApiUrl !== $uiUrl) {
            throw new \Exception('Api and UI dont appear to be hosted on the same machine ui = ' . $uiUrl . ' Api = ' . $onlineApiUrl);
        }

        $systemKey = $this->systemConfig->getSystemKey();

        $urlFormatter = new URLFormatting();

        $encryption_key = \openssl_digest($systemKey, 'sha256', TRUE);
        $encryption = \openssl_encrypt('BRANDCODE<' . $brand->getCode() . '>' . $urlFormatter->correctURL($brand->getOnlineUiUrl(), "/", false), $ciphering, $encryption_key, $options, \hex2bin($encryption_iv));

        $uri = "/api/branding/" . $method;
        $response = $this->client->post($uri, [
            'json' => [
                'systemKey' => $this->systemConfig->getSystemKey(),
                "tenantId" => $this->systemConfig->getTenantid(),
                "code" => $brandCode,
                "name" => $brandName,
                "baseURL" => $urlFormatter->correctURL($brand->getOnlineApiUrl(), "/", false),
                "pubKey" => $encryption,
            ]
        ]);

        return $response;
    }
}
