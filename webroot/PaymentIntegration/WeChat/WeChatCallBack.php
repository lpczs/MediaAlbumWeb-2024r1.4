<?php 
require __DIR__ . '/../../../libs/external/vendor/autoload.php';

error_reporting(E_ALL);
ini_set('log_errors', true);

// step back to the webroot directory
chdir('../../');

require_once('../Utils/UtilsCoreIncludes.php');
require_once('../Order/Order_control.php');
require_once('../Utils/UtilsCoreIncludes.php');
$ac_config = UtilsObj::readConfigFile('../config/mediaalbumweb.conf');

//Echo the reponse so wechat can verify the callback

$xmlString = '<xml>
			<return_code><![CDATA[SUCCESS]]></return_code>
			</xml>';

echo $xmlString;

//Read the XML from the POST body
$xmlResponse = file_get_contents('php://input');

//Try and parse the XML
try{

	//Parse the XML into an object
	$parsedXML = new SimpleXMLElement($xmlResponse);

}catch(Exception $e){
	error_log('Failed to parse xml ' . $e);
}

//Pull the session ref from the out_trade_no explode it on the _
$ref = explode('_', (string) $parsedXML->out_trade_no);
$ref = $ref[0];

//Assign the $_GET['ref'] to be the parsed ref so we can get the session
$_GET['ref'] = $ref;

// get the constants
$gConstants = DatabaseObj::getConstants();

// use the retrieved session reference to generate the session
$gSession = AuthenticateObj::getCurrentSessionData();

$gAuthSession = true;

Order_control::ccAutomaticCallback();
	
?>