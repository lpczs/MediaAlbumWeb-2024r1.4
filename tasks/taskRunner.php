<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once(__DIR__ . '/../libs/external/vendor/autoload.php');

set_time_limit(60);

echo '*';

$filePath = dirname($argv[0]);
chdir($filePath);

require_once('../Utils/UtilsCoreIncludes.php');
require_once('../Utils/UtilsEmail.php');
require_once('../libs/internal/curl/Curl.php');

$ac_config = UtilsObj::readConfigFile('../config/mediaalbumweb.conf');

if (!array_key_exists('ALLOWSELFSIGNEDSSLCERTIFICATES', $ac_config))
{
	$ac_config['ALLOWSELFSIGNEDSSLCERTIFICATES'] = 0;
}

// if the ALLOWSELFSIGNEDSSLCERTIFICATES is set to 0 then we must set CURLOPT_SSL_VERIFYPEER to true.
$ac_config['SSLVERIFYPEER'] = ($ac_config['ALLOWSELFSIGNEDSSLCERTIFICATES'] == 0);

$gConstants = DatabaseObj::getConstants();

$taskResult = '';
$taskFile = '';

$taskCode = isset($argv[1]) ? $argv[1] : '';
$eventID = isset($argv[2]) ? $argv[2] : 0;


// get the offset between the server time and PHP time
// we do this so that we can just use the PHP time() function and apply the offset
$serverTimeOffset = strtotime(DatabaseObj::getServerTime()) - time();


class TaskObj
{
	public static function writeLogEntry($pMessage)
	{
		UtilsObj::writeLogEntry($pMessage);
	}

	public static function getGlobalDBConnection()
	{
		return DatabaseObj::getGlobalDBConnection();
	}

	public static function acquireDBMutex($pName, $pWaitTime = 0)
	{
		// acquire a database mutex with a default wait time of zero
		// waiting any longer for a mutex could cause multiple tasks of the same type to trigger which we do not want
		return DatabaseObj::acquireDBMutex($pName, $pWaitTime);
	}

	public static function dbMutexFree($pName)
	{
		return DatabaseObj::dbMutexFree($pName);
	}

	public static function waitForNoDBMutex($pName, $pTimeout)
	{
		return DatabaseObj::waitForNoDBMutex($pName, $pTimeout);
	}

	public static function releaseDBMutex($pName)
	{
		return DatabaseObj::releaseDBMutex($pName);
	}

    public static function getEventsByTaskCode($pTaskCode, $pLimit = -1)
    {
        return DatabaseObj::getActiveEventsByTaskCode($pTaskCode, $pLimit);
    }

    public static function updateEvent($pEventID, $pStatusCode, $pStatusMessage)
    {
    	global $serverTimeOffset;

        $runTime = time() + $serverTimeOffset;
        $nextRunTime = date('Y-m-d H:i:s', ($runTime + 60));

        return DatabaseObj::updateEvent($pEventID, date('Y-m-d H:i:s', $runTime), $nextRunTime, $pStatusCode, $pStatusMessage);
    }

    public static function getTask($pTaskCode)
    {
        return DatabaseObj::getTask($pTaskCode);
    }

    public static function getEventByID($pEventID)
    {
        return DatabaseObj::getEventById($pEventID, true);
    }

    public static function getBrandingFromCode($pBrandCode)
    {
    	return DatabaseObj::getBrandingFromCode($pBrandCode);
    }

    public static function getServerTime($pOffsetMinutes = 0)
    {
    	return DatabaseObj::getServerTime($pOffsetMinutes);
    }

    public static function getLocaleString($pLocalizedString, $pLanguage, $pUseFirstAvailable = false)
    {
    	return LocalizationObj::getLocaleString($pLocalizedString, $pLanguage, $pUseFirstAvailable);
    }

    public static function createEvent($pTaskCode, $pCompanyCode, $pGroupCode, $pBrandCode, $pNextRunTime, $pParentid, $pParam1, $pParam2, $pParam3, $pParam4, $pParam5, $pParam6, $pParam7, $pParam8, $pOrderHeaderID = 0, $pOrderItemID = 0, $pUserID = 0, $pTask1 = '', $pTask2 = '')
    {
    	return DatabaseObj::createEvent($pTaskCode, $pCompanyCode, $pGroupCode, $pBrandCode, $pNextRunTime, $pParentid, $pParam1, $pParam2, $pParam3, $pParam4, $pParam5, $pParam6, $pParam7, $pParam8, $pOrderHeaderID, $pOrderItemID, $pUserID, $pTask1, $pTask2, $pUserID);
    }

    public static function getLicenseKeyFromCode($pGroupCode)
    {
    	return DatabaseObj::getLicenseKeyFromCode($pGroupCode);
    }

    public static function getBrandLicenseKeyCodes($pBrandCode)
    {
    	return DatabaseObj::getBrandLicenseKeyCodes($pBrandCode);
    }

    public static function sendTemplateEmail($pMessageRootPath, $pWebBrandCode, $pWebAppName, $pHomeURL, $pLocale, $pEmailName, $pEmailAddress, $pBCCEmailName, $pBCCEmailAddress, $pUserID, $pParamArray, $pEmailReplyToName = '', $pEmailReplyToAddress = '')
    {
    	$emailObj = new TaopixMailer();
		$emailObj->sendTemplateEmail($pMessageRootPath, $pWebBrandCode, $pWebAppName, $pHomeURL, $pLocale, $pEmailName, $pEmailAddress, $pBCCEmailName, $pBCCEmailAddress, $pUserID, $pParamArray, $pEmailReplyToName, $pEmailReplyToAddress);
    }

    public static function sendTemplateBulkEmail($pMessageRootPath, $pWebBrandCode, $pWebAppName, $pHomeURL, $pLocale, $pEmailName, $pEmailAddress, $pBCCEmailName, $pBCCEmailAddress, $pUserID, $pParamArray, $pEmailReplyToName = '', $pEmailReplyToAddress = '')
    {
    	$emailObj = new TaopixMailer();
		$emailObj->sendTemplateBulkEmail($pMessageRootPath, $pWebBrandCode, $pWebAppName, $pHomeURL, $pLocale, $pEmailName, $pEmailAddress, $pBCCEmailName, $pBCCEmailAddress, $pUserID, $pParamArray, $pEmailReplyToName, $pEmailReplyToAddress);
    }

    public static function put($pURL, $pFields, $pRetries, $pTimeouts)
    {
    	return CurlObj::put($pURL, $pFields, $pRetries, $pTimeouts);
    }

    public static function readCompressedArray($pArray)
    {
        $uncompressedData = UtilsObj::readCompressedData($pArray);

        return unserialize($uncompressedData);
    }

    static function compressArray($pArray)
    {
        return UtilsObj::compressArray($pArray);
    }

    static function convertBytesToMB($pValue)
    {
        return UtilsObj::convertBytesToMB($pValue);
    }

    static function compressData($pData)
    {
        return UtilsObj::compressData($pData);
    }

    static function correctPath($pWebBrandURL)
    {
    	return UtilsObj::correctPath($pWebBrandURL);
    }

    static function getSystemConfig()
    {
    	return DatabaseObj::getSystemConfig();
    }

    static function updateTaskStatus($pTaskCode, $pParams, $pDBObj)
    {
    	DatabaseObj::updateTaskStatus($pTaskCode, $pParams, $pDBObj);
    }

    static function sendToTaopixOnline($pCommand, $pData, $pFile = array())
    {
    	global $ac_config;

    	$pData['cmd'] = $pCommand;

		return CurlObj::sendByPost(UtilsObj::correctPath($ac_config['TAOPIXONLINEURL']), 'PushAPI.callback', $pData, $pFile);
    }

}



// get the filename for this task from the database
$taskDetails = TaskObj::getTask($taskCode);
if ($taskDetails['result'] != '')
{
	$taskResult = $taskDetails['resultParam'];
}
else
{
	$taskFile = $taskDetails['scriptFileName'];
}

if (($taskResult == '') && ($taskFile != ''))
{
	// include task file if it exists
	$className = str_replace('.php', '', $taskFile);

	if ($taskDetails['internal'] == 0)
	{
		$taskFile = '../Customise/scripts/tasks/' . $taskFile;
	}

	if (file_exists($taskFile))
	{
		// aquire the mutex (without retrying) to track that the task process is running
		$mutexName = 'taopixtask_' . $taskCode;
		$mutexResultArray = TaskObj::acquireDBMutex($mutexName);

		if ($mutexResultArray['result'] == true)
		{
			$dbObj = TaskObj::getGlobalDBConnection();

			// mark task as in process
			TaskObj::updateTaskStatus($taskCode, array('runstatus' => array('value' => TPX_TASKMANAGER_STATUS_INPROCESS, 'type' => 'i')), $dbObj);

			$dbObj->close();

			// remove dead events for the task
			TaskObj::writeLogEntry('Task: ' . $taskCode . '. Removing Dead Events.');
			DatabaseObj::removeDeadEvents($taskCode, $taskDetails['deleteExpiredInterval']);


			// include task file and run it
			require_once($taskFile);

			if (method_exists($className, 'run'))
			{
				$taskResult = call_user_func(array($className, 'run'), array($eventID));
			}
			else
			{
				$taskResult = '{str_ErrorNoRunMethod}';
			}


			// release the mutex
			TaskObj::releaseDBMutex($mutexName);
		}
		else
		{
			// could not obtain a mutex

			if ($mutexResultArray['error'] == 'ALREADYRUNNING')
			{
				// no error occurred and the mutex must have already existed (ie: task of this type already running)
				$taskResult = 'ALREADYRUNNING';
			}
			else
			{
				// an error occurred while trying to obtain the mutex
				$taskResult = '{str_DatabaseError}';
			}
		}
	}
	else
	{
		$taskResult = '{str_ErrorScriptNotFound}';
	}
}
else
{
	// could not find the script to run
	$taskResult = '{str_ErrorNoScriptName}';
}


echo 'TAOPIX_TASK_COMPLETION';
echo $taskResult;

?>