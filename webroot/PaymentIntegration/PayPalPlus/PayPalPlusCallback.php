<?php

require __DIR__ . '/../../../libs/external/vendor/autoload.php';

error_reporting(E_ALL);
ini_set('log_errors', true);

// step back to the webroot directory
chdir('../../');

// include required files
require_once('../Utils/UtilsCoreIncludes.php');
require_once('../Utils/UtilsDatabase.php');
require_once('../Order/Order_control.php');

// read the config file
$ac_config = UtilsObj::readConfigFile('../config/mediaalbumweb.conf');

$ref = 0;

$dataStream = fopen('php://input', 'r');

if ($dataStream)
{
	$streamContents = stream_get_contents($dataStream);

	$streamArray = json_decode($streamContents, 1);

	// retrieve the ref from the CCILOG using the payment reference
	$dbObj = DatabaseObj::getGlobalDBConnection();
	if ($dbObj)
	{
		// get the sessionid from the parent ccilog, child ccilogs may not have a sessionid
		$sql = 'SELECT `sessionid`
				FROM `ccilog` cl
				WHERE cl.transactionid = ?
				AND cl.`parentlogid` = 0
				ORDER BY cl.datecreated
				DESC LIMIT 1';

		if (($stmt = $dbObj->prepare($sql)))
		{
			if ($stmt->bind_param('s', $streamArray['resource']['parent_payment']))
			{
				if ($stmt->execute())
				{
					if ($stmt->bind_result($ref))
					{
						if (! $stmt->fetch())
						{
							error_log(__FILE__ . ' fetch ' . $dbObj->error);
						}
					}
					else
					{
						error_log(__FILE__ . ' bind_result ' . $dbObj->error);
					}
				}
				else
				{
					error_log(__FILE__ . ' execute ' . $dbObj->error);
				}
			}
			else
			{
				error_log(__FILE__ . ' bind_param ' . $dbObj->error);
			}
			
			$stmt->free_result();
			$stmt->close();
		}
		else
		{
			error_log(__FILE__ . ' prepare ' . $dbObj->error);
		}
		$dbObj->close();
	}
	else
	{
		error_log(__FILE__ . ' unable to connect to datebase ' . $dbObj->error);
	}

	if ($ref > 0)
	{
		$_GET['ref'] = $ref;
		$_GET['pm'] = 'PAYPAL';
		$_POST['paypalpluswebhook'] = $streamArray;

		Order_control::ccAutomaticCallback();
	}
}