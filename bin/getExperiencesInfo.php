<?php

/**
 * Send data from CC to Online to create settings experiences and assign experiences to brands / keys
 * Only works when no assignments found
 */

define('__ROOT__', dirname(__FILE__, 2));

echo __ROOT__ . "\n";

require_once(__ROOT__ . '/Utils/Utils.php');
require_once(__ROOT__ . '/Utils/UtilsDatabase.php');
require_once(__ROOT__ . '/libs/external/vendor/autoload.php');

// Set unlimited script timeout.
set_time_limit(0);

use GuzzleHttp\Client;
use Taopix\ControlCentre\Enum\Experience\ExperienceType;
use Taopix\ControlCentre\Enum\Experience\ExperienceSystemType;

global $gSession;
global $ac_config;
global $gConstants;

// Read the config file.
$configPath = __ROOT__ . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'mediaalbumweb.conf';
$ac_config = UtilsObj::readConfigFile($configPath);

$params = readParameters();

$productInfo = getProductInfo($params['collectioncode'], $params['layoutcode']);

printInfo($productInfo['error']);

if ($productInfo['error'] != '') 
{
    exit;
}

printLine("CLI Params:");
printLine(print_r($params,true));

blankLine();
blankLine();

printLine("Product Info:");
printLine(print_r($productInfo,true));

blankLine();
blankLine();

unset($params['error']);
$key = implode('.', $params);

$ac_config['SSLVERIFYPEER'] = 0;

$systemConfigArray = DatabaseObj::getSystemConfig();
$tenantID = $systemConfigArray['tenantid'];
$gConstants = DatabaseObj::getConstants();

$endpoint = UtilsObj::correctPath($ac_config['TAOPIXONLINEURL'], "/", true) . 'api/experience/getExperience'
            .   '?key=' . $key
            .   '&producttype=' . $productInfo['collectiontype']
            .   '&retroprint=' . $productInfo['retroprints']
            .   '&systemkey=' . $systemConfigArray['systemkey']
            .   '&tenantid=' . $tenantID;

$httpClient = new Client(['verify' => UtilsObj::getCurlPEMFilePath()]);
$responseBody = "";
$response = $httpClient->get($endpoint, []);

// Get the contents of the body of the response.
$responseBody = $response->getBody()->getContents();
$returnData = json_decode($responseBody);

printLine("Experience Data from Online:");
blankLine();
printLine("Info on where data is coming from (legacy mode etc):");
printLine(print_r($returnData->data->info,true));
blankLine();
printLine("Experience Data prior to merge");
printLine(print_r($returnData->data->preBaseMergeExperienceData,true));
blankLine();
printLine("Full Experience Data");
printLine(print_r($returnData->data->experienceData,true));
blankLine();
printLine("Experience template data");
printLine(print_r($returnData->data->experienceArray,true));
blankLine();
printLine("Legacy settings from Online DB:");
printLine(print_r($returnData->data->legacySettings,true));
blankLine();
blankLine();

if ($params['createmode'] === 'true') {
    doCreateExperience($returnData->data->experienceDataRaw, $params, $productInfo, $httpClient, UtilsObj::correctPath($ac_config['WEBURL'], "/", true));
}

function printInfo($pError)
{
	printLine("----------------------------------");
	printLine("TAOPIX Experience Info");
	printLine("----------------------------------");
	blankLine();
	printLine("Description:");
	printLine("         This script will display info about the experience which will be used for a specified product");
    printLine("         Collection and Layout are required");
    printLine("         Brand and Group are optional and if not supplied \"Any brand or Any key\" option will be assumed");
	blankLine();
	printLine("Standard Usage:");
    printLine("         getExperienceInfo.php --brandcode <BRANDCODE> --groupcode <GROUPCODE> --collectioncode <COLLECTIONCODE> --layoutcode <LAYOUTCODE>");
    blankLine();
    printLine("Advanced Usage:");
    printLine("         getExperienceInfo.php --brandcode <BRANDCODE> --groupcode <GROUPCODE> --collectioncode <COLLECTIONCODE> --layoutcode <LAYOUTCODE> --createmode true --title <TITLEOFEXPERIENCE>");
    blankLine();
    printLine("Note:");
    printLine("         If your Collection code/layout code/title contain a SPACE please wrap them in quotes");
	blankLine();
    blankLine();
	if ($pError != '')
	{
		printLine("****** ERROR - Invalid option(s) passed: " . $pError);
	}
}

function readParameters()
{
	$parametersArray = array(
		"cc:" => "collectioncode:",
		"lc:" => "layoutcode:",
        "gc:" => "groupcode:",
        "bc:" => "brandcode:",
        "cm:" => "createmode:",
        "title:" => "title:");

	$cliOptionsArray = getopt(implode('', array_keys($parametersArray)), $parametersArray);

	$scriptOptions = array( 'brandcode' => '*',
                            'groupcode' => '*', 
                            'collectioncode' => '', 
                            'layoutcode' => '', 
                            'createmode' => '', 
                            'title' => '', 
                            'error' => '');

    if ((UtilsObj::getArrayParam($cliOptionsArray, 'collectioncode', '') == '') || (UtilsObj::getArrayParam($cliOptionsArray, 'layoutcode', '') == ''))
    {
        $scriptOptions['error'] = "\nPlease specify a layout code and collection code \n\n";
    }

    if ((UtilsObj::getArrayParam($cliOptionsArray, 'createmode', 'false') == 'true') && (UtilsObj::getArrayParam($cliOptionsArray, 'title', '') == ''))
    {
        $scriptOptions['error'] = "\nCreate mode requires a title \n\n";
    }

    if ($scriptOptions['error'] == '')
    {
        $scriptOptions['brandcode'] = UtilsObj::getArrayParam($cliOptionsArray, 'brandcode', '*');
        $scriptOptions['groupcode'] = UtilsObj::getArrayParam($cliOptionsArray, 'groupcode', '*');
        $scriptOptions['layoutcode'] = UtilsObj::getArrayParam($cliOptionsArray, 'layoutcode', '');
        $scriptOptions['collectioncode'] = UtilsObj::getArrayParam($cliOptionsArray, 'collectioncode', '');
        $scriptOptions['createmode'] = UtilsObj::getArrayParam($cliOptionsArray, 'createmode', 'false');
        $scriptOptions['title'] = UtilsObj::getArrayParam($cliOptionsArray, 'title', '');
	}

	return $scriptOptions;
}

function blankLine()
{
	printLine("");
}

function printLine($pData)
{
	echo $pData . "\n";
}

function getProductInfo($pCollectionCode, $pLayoutCode) 
{
    $collectiontype = 0;
    $retroprints = 0;
    $returnArray = [
        'collectiontype' => 0,
        'retroprints' => 0,
        'error' => '',
        'errorParam' => ''
    ];

    $sql = "    SELECT	pcl.`collectiontype`, p.`retroprints`   
                FROM PRODUCTCOLLECTIONLINK pcl
                INNER JOIN PRODUCTS p ON p.`code` = pcl.`productcode`
                WHERE pcl.`productcode` = ?
                AND pcl.`collectioncode` = ?
                LIMIT 1
            ";

    $dbObj = DatabaseObj::getGlobalDBConnection();
    if ($dbObj)
    {
        if ($stmt = $dbObj->prepare($sql))
        {
            if ($stmt->bind_param('ss', $pLayoutCode, $pCollectionCode))
            {
                $stmt->bind_result(
                    $collectiontype, $retroprints
                );

                if ($stmt->execute())
                {
                    while ($stmt->fetch())
                    {
                        $returnArray['collectiontype'] = $collectiontype;
                        $returnArray['retroprints'] = $retroprints;
                    }
                }
                else
                {
                    $returnArray['error'] = 'str_DatabaseError';
                    $returnArray['errorParam'] = 'execute ' . $dbObj->error;
                }

                if ($stmt) {
                    $stmt->free_result();
                    $stmt->close();
                }
            } else {
                // could not bind params
                $returnArray['error'] = 'str_DatabaseError';
                $returnArray['errorParam'] = 'bind ' . $dbObj->error;
            }
        } else {
            // could not prepare statement
            $returnArray['error'] = 'str_DatabaseError';
            $returnArray['errorParam'] = 'prepare ' . $dbObj->error;
        }
    } 
    else 
    {
        // could not open database connection
        $returnArray['error'] = 'str_DatabaseError';
        $returnArray['errorParam'] = 'connect ' . $dbObj->error;
    }

    return $returnArray;
}

function doCreateExperience($data, $params, $productInfo, $httpClient, $ccurl) 
{
    $experienceData = (array) $data;

    foreach (ExperienceType::cases() as $experienceType) {
        if ($experienceType->name !== 'FULL') 
        {
            $experience = (array) $experienceData[$experienceType->name];
            $postParams = [
                "id" =>  -1,
                "experienceType" => $experienceType->value,
                "name" => $params['title'],
                "productType" => $productInfo['collectiontype'],
                "retroPrint" => $productInfo['retroprints'],
                "data"=> $experience,
                "dataLength" => 0, //not used at this point
                "assignment" => [],
                "systemType" => ExperienceSystemType::CUSTOM->value
            ];

            $endpoint = $ccurl . 'api/experience/saveData';
            $response = $httpClient->post($endpoint, [
                'form_params' => [
					'experience' => json_encode($postParams)
				]
            ]);

            error_log("==========================================");
            error_log("creation result " . $experienceType->name . " " . $params['title'] . " - " . $response->getStatusCode());
            error_log("==========================================");
        }
    }

}