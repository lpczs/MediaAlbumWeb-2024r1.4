<?php

/**
 * Move the orderData files from the original loaction: ..../webroot/OrderData/Thumbnails/pages/
 * to the new location in the Taopix data folder: ..../taopixdata/controlcentre/orderpreviews/
 */

define('__ROOT__', dirname(__FILE__, 2));

require_once(__ROOT__ . '/Utils/Utils.php');
require_once(__ROOT__ . '/Utils/UtilsDatabase.php');
require_once(__ROOT__ . '/libs/external/vendor/autoload.php');

use Taopix\Migrate\ManageOrderData;

// Set unlimited script timeout.
set_time_limit(0);

// Read the config file.
$configPath = __ROOT__ . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'mediaalbumweb.conf';
$ac_config = UtilsObj::readConfigFile($configPath);

// Make sure the destination path is set in the config.
if (array_key_exists('CONTROLCENTREORDERPREVIEWPATH', $ac_config))
{
    $configArr = [
        'root' => __ROOT__,
        'destination' => $ac_config['CONTROLCENTREORDERPREVIEWPATH']
    ];

    $migration = new ManageOrderData($configArr);

    $migration->startMigration();

    $migrationFinal = $migration->getStatus();

    $migration->showResults();
}
else
{
    echo "Error - Missing config setting: CONTROLCENTREORDERPREVIEWPATH" . PHP_EOL;
}
