<?php

define('__ROOT__', realpath(dirname(dirname(__FILE__))));

// Include required files.
require_once __ROOT__ . '/Utils/UtilsDatabase.php';
require_once __ROOT__ . '/Utils/Utils.php';
require_once(__ROOT__ . '/libs/external/vendor/autoload.php');

use GuzzleHttp\Client;

// OS Types.
define('TPX_OS_TYPE_WINDOWS', 0);
define('TPX_OS_TYPE_UNIX', 1);
define('TPX_OS_TYPE_MAC', 2);


// Set unlimited script timeout.
set_time_limit(0);

class OnlineBrandPush
{
    private $db;

    public function __construct(private mixed $config)
    {
        $this->db = DatabaseObj::getGlobalDBConnection();
    }

    public function run()
    {
        $id = -1;
        $code = '';
        $apiUrl = '';

        $query = "SELECT `id`, `code`, `onlineapiurl` FROM `branding` WHERE onlinedesignerurl != '' ORDER BY `id` ASC";

        if ($stmt = $this->db->prepare($query)) {
            if ($stmt->bind_result($id, $code, $apiUrl)) {
                if ($stmt->execute()) {
                    while ($stmt->fetch()) {
                        if ($this->callInternal($id)) {
                            echo "brand ".('' !== $code ? $code : 'default brand')." has been updated on the online server" . PHP_EOL;
                        } else {
                            echo "There was an error updating brand $code on the online server" . PHP_EOL;
                        }
                    }
                }
            }
        }
    }

    private function callInternal(int $brandId)
    {
        $client = new Client([
            'base_uri' => UtilsObj::correctPath($this->config['WEBURL']),
            'verify' => UtilsObj::getCurlPEMFilePath()
        ]);

        try {
            $response = $client->post('api/brand/applyBrandUIConfig', [
                'json' => [
                    'brandId' => $brandId,
                    'endpoint' => 'update'
                ]
            ]);
        } catch (Throwable $e) {
            echo $e->getMessage();
            return false;
        }

        if (200 === $response->getStatusCode()) {
            return true;
        }
        return false;
    }
}

// Read the config file.
$ac_config = UtilsObj::readConfigFile(__ROOT__ . '/config/mediaalbumweb.conf');

$push = new OnlineBrandPush($ac_config);
$push->run();
