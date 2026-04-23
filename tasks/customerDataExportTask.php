<?php

use DataExport\Exporter\CustomerExporter;
use DataExport\Exporter\DataSetExporterInterface;
use DataExport\Writer\DataFileSpec;
use DataExport\Writer\ExportWriterFactory;
use DataExport\Writer\WriterInterface;

require_once(__DIR__ . '/../Utils/UtilsEmail.php');
require_once(__DIR__ . '/../Utils/UtilsDatabase.php');
require_once(__DIR__ . '/../Utils/UtilsAuthenticate.php');

ini_set('display_errors', 1);
error_reporting(E_ALL);

class customerDataExportTask
{
	/**
	 *
	 */
	const ZIP_EXTENSION = '.zip';

	/**
	 * Default execution time limit for each export if not
	 * overridden in the configuration file
	 */
	const DEFAULT_EXEC_TIME_LIMIT = 180;

	/**
	 * The batch size before stopping and creating a new event
	 * to process a subsequent batch
	 */
	const DEFAULT_BATCH_COUNT = 100000;

	/**
	 * Number of records to process between sleeps
	 */
	const RECORD_PROCESSING_SLEEP_COUNT = 5000;

	/**
	 * Period of time to sleep for
	 */
	const RECORD_PROCESSING_SLEEP_DURATION = 1;

	/**
	 * Register task
	 *
	 * Register task by defining default settings for the
	 * task and return as an associate array.
	 *
	 * @return array
	 */
	public static function register()
	{

		global $ac_config;
		$timeLimit = !empty($ac_config['DATAEXPORTTIMELIMIT']) ?
			(int) $ac_config['DATAEXPORTTIMELIMIT'] : self::DEFAULT_EXEC_TIME_LIMIT;

		$batchSize = !empty($ac_config['DATAEXPORTBATCHSIZE']) ?
			(int) $ac_config['DATAEXPORTBATCHSIZE'] : self::DEFAULT_BATCH_COUNT;

		$defaultSettings = [
			'type' => '0',
			'code' => 'TAOPIX_CUSTOMERDATAEXPORT',
			'intervalType' => '1',
			'maxRunCount' => '10',
			'timeLimit' => $timeLimit,
			'batchSize' => $batchSize,
		];

		return $defaultSettings;
	}

	/**
	 * Run task
	 *
	 * Trigger the running of the task, supplying the event id
	 * of the task to be handled.
	 *
	 * @param $pEventID
	 * @return string
	 */
	public static function run($pEventID)
	{
		// Set the gSession var as global as we use this in smarty.
		global $gSession;

		$pEventID = (int) $pEventID[0];

		// Get list of events for the task
		$defaultSettings = self::register();
		$timeLimit = $defaultSettings['timeLimit'];
		$taskCode = $defaultSettings['code'];

		// Set the session to be empty.
		$gSession = AuthenticateObj::createSessionDataArray();

		try
		{
			// Get list of events and a database connection
			$events = self::getEventList($pEventID, $taskCode);
			$connection = DatabaseObj::getGlobalDBConnection();
			$smarty = SmartyObj::newSmarty('AdminCustomers');

			// For each event, run the appropriate export
			foreach ($events as $event)
			{
				// Reset the max execution time
				self::resetMaxExecutionTimeLimit($timeLimit);

				$eventId = $event['id'];

				// Force an increment of the run count in case a timeout occurs
				TaskObj::updateEvent($eventId, 0, '');

				// Data set (e.g. customer)
				$dataSet = $event['param1'];

				// Data selection filters
				$filters = json_decode($event['param2'], true);

				// File format (e.g. csv, tsv, xml)
				$format = $event['param3'];

				// Beautifying output (by supported writers)
				$beautify = $event['param4'];

				// Last batch id to be processed (or empty string)
				$lastId = $event['param6'];

				// Parent event id (or self if not set)
				$parentEventId = !empty($event['parentId']) ? $event['parentId'] : $eventId;

				// Create exporter for specific data set
				$exporter = self::createExporter($connection, $smarty, $dataSet, $filters, $defaultSettings['batchSize'], $lastId);

				// Generate paths
				$exportPath = self::generateExportBasePath($dataSet);
				$batchPath = self::generateBatchFilePath($exportPath, $parentEventId);
				$fileName = $batchPath . DIRECTORY_SEPARATOR . self::generateExportFileName($eventId);

				$dataFileSpec = new DataFileSpec(
					$exporter->getNormalizedHeader(),
					$exporter->getHeaders(),
					$exporter->getRootElementName(),
					$exporter->getDataElementName()
				);

				// Create writer for specific output format
				$writer = ExportWriterFactory::create($format, $dataFileSpec, $fileName, $beautify);

				// Export data. Only zip and send the notification if all batches are done.
				list($batchProcessingDone, $accumulativeRecordCount) = self::exportData($event, $parentEventId, $exporter, $writer, $defaultSettings['batchSize']);
				if ($batchProcessingDone)
				{
					//$zipFile = $exportPath . self::generateExportFileName($parentEventId) . self::ZIP_EXTENSION;
					//self::compressBatch($zipFile, $batchPath); // temporarily disabled due to issues timing out compression of large datasets
					self::sendNotification($event, $batchPath, $accumulativeRecordCount, $smarty);
				}
			}
		}
		catch (Exception $ex)
		{
			return 'en ' . $ex->getMessage();
		}

		return '';
	}

	/**
	 * Clear the max execution time of the script, ensuring an export won't be bound
	 * by time limits.
	 *
	 * @param int $timeLimit
	 */
	private static function resetMaxExecutionTimeLimit($timeLimit)
	{
		set_time_limit($timeLimit);
	}

	/**
	 * Export the data to the writer
	 *
	 * @param mixed[] $event
	 * @param int|null $parentEventId
	 * @param $exporter
	 * @param WriterInterface $writer
	 * @param int $batchSize
	 * @return mixed[]
	 * @throws Exception
	 */
	private static function exportData($event, $parentEventId, DataSetExporterInterface $exporter, WriterInterface $writer, $batchSize)
	{
		$eventId = $event['id'];
		$userID = $event['userid'];
		$batchProcessingDone = true;

		// Add records
		foreach ($exporter as $dataRecord)
		{
			$writer->write($dataRecord);

			if (($exporter->getRecordCount() % self::RECORD_PROCESSING_SLEEP_COUNT) === 0)
			{
				sleep(self::RECORD_PROCESSING_SLEEP_DURATION);
			}
		}

		$accumulativeRecordCount = ((int) $event['param7']) + $exporter->getRecordCount();
		if ($exporter->getRecordCount() === $batchSize)
		{
			// Create a new event for the next batch
			DatabaseObj::createEvent('TAOPIX_CUSTOMERDATAEXPORT',
				$event['companyCode'],
				$event['groupCode'],
				$event['webBrandCode'],
				'',
				$parentEventId,
				$event['param1'],
				$event['param2'],
				$event['param3'],
				$event['param4'],
				$event['param5'],
				$exporter->getLastId(),
				$accumulativeRecordCount,
				'',
				0,
				0,
				$userID,
				'',
				'',
				0
			);

			$batchProcessingDone = false;
		}

		$writer->finalise();
		TaskObj::updateEvent($eventId, 2, 'en Data exported');

		$user = DatabaseObj::getUserAccountFromID($userID);
		DatabaseObj::updateActivityLog(
			-1,
			0,
			$userID,
			$user['login'],
			$user['contactfirstname'] . ' ' . $user['contactlastname'],
			0,
			'CUSTOMER',
			'CUSTOMEREXPORTBATCHCOMPLETED',
			'',
			1
		);

		return [$batchProcessingDone, $accumulativeRecordCount];
	}

	/**
	 * Compress the batch in to a single zip file
	 *
	 * @param string $zipFileName
	 * @param string $batchPath
	 * @throws Exception
	 */
	private static function compressBatch($zipFileName, $batchPath)
	{
		// Compress the file
		$zip = new ZipArchive();

		if (!$zip->open($zipFileName, ZIPARCHIVE::CREATE | ZipArchive::EXCL)) {
			throw new Exception(sprintf('Unable to start new zip file for filename "%s".', $zipFileName));
		}

		if (!$zip->addGlob($batchPath . '*')) {
			throw new Exception(sprintf('Unable to add glob batch path "%s" to zip file "%s".', $batchPath, $zipFileName));
		}

		if (!$zip->close()) {
			throw new Exception(sprintf('Unable to close zip file "%s".', $zipFileName));
		}

		// Removing batch directory
		if (false !== ($dh = opendir($batchPath))) {
			while (false !== ($entry = readdir($dh))) {
				if ($entry != "." && $entry != "..") {
					unlink($batchPath . $entry);
				}
			}

			closedir($dh);
			rmdir($batchPath);
		}
	}

	/**
	 * Send a notification, if required
	 *
	 * @param mixed[] $event
	 * @param string $zipFile
	 * @param int $accumulativeRecordCount
	 * @param Smarty $smarty
	 */
	private static function sendNotification($event, $zipFile, $accumulativeRecordCount, Smarty $smarty)
	{
		$userID = $event['userid'];
		$filters = json_decode($event['param2'], true);
		$languageCode = $event['param5'];

		// get the default brand settings
		$brandingDefaults = DatabaseObj::getBrandingFromCode('');

		$emailWebBrandCode = $brandingDefaults['code'];
		$emailWebBrandApplicationName = $brandingDefaults['applicationname'];
		$emailWebBrandDisplayURL = $brandingDefaults['displayurl'];
		$emailName = $brandingDefaults['smtpadminname'];
		$emailAddress = $brandingDefaults['smtpadminaddress'];
		$emailNameBCC = '';
		$emailAddressBCC = '';
		$defaultBrandAdminEmailActive = ($brandingDefaults['smtpadminactive'] == 1) && ($brandingDefaults['smtpadminname'] != '') ? true : false;

		$userArray = DatabaseObj::getUserAccountFromID($userID);

		if ($userArray['usertype'] == TPX_LOGIN_COMPANY_ADMIN)
		{
			$emailName = $userArray['contactfirstname'] . ' ' . $userArray['contactlastname'];
			$emailAddress = $userArray['emailaddress'];
			$emailNameBCC = $brandingDefaults['smtpadminname'];
			$emailAddressBCC = $brandingDefaults['smtpadminaddress'];
		}

		if ($defaultBrandAdminEmailActive)
		{
			$i18nFilters = [];
			$all = $smarty->get_config_vars('str_LabelAll');

			/** @var string $filerName */

			// Company
			$filerName = $smarty->get_config_vars('str_LabelCompany');
			$i18nFilters[$filerName] = empty($filters['companyCode']) ? $all : $filters['companyCode'];

			// Brand
			$filerName = $smarty->get_config_vars('str_LabelBrand');
			$i18nFilters[$filerName] = empty($filters['brandCode']) ? $all : $filters['brandCode'];

			// License key
			$filerName = $smarty->get_config_vars('str_LabelLicenseKey');
			$i18nFilters[$filerName] = empty($filters['groupCode']) ? $all : $filters['groupCode'];

			// Country
			$filerName = $smarty->get_config_vars('str_LabelCountry');
			$i18nFilters[$filerName] = empty($filters['countryCode']) ? $all : UtilsAddressObj::getCountryNameFromCode($filters['countryCode']);

			// Email address
			$filerName = $smarty->get_config_vars('str_LabelEmailAddress');
			$i18nFilters[$filerName] = $filters['contactEmail'];

			// Last name
			$filerName = $smarty->get_config_vars('str_LabelLastName');
			$i18nFilters[$filerName] = $filters['contactLastName'];

			// Generate and send email
			$emailObj = new TaopixMailer();
			$emailObj->sendTemplateEmail(
				'admin_customerdataexport',
				$emailWebBrandCode,
				$emailWebBrandApplicationName,
				$emailWebBrandDisplayURL,
				$languageCode,
				$emailName,
				$emailAddress,
				$emailNameBCC,
				$emailAddressBCC,
				0,
				[
					'filepath' => $zipFile,
					'recordcount' => $accumulativeRecordCount,
					'filters' => [$i18nFilters],
					'login' => $userArray['login']
				]
			);
		}
	}

	/**
	 * Get a list of events to process
	 *
	 * @param int $pEventID
	 * @param string $pTaskCode
	 * @return mixed
	 * @throws Exception
	 */
	private static function getEventList($pEventID, $pTaskCode)
	{
		if ($pEventID > 0)
		{
			$eventsList = TaskObj::getEventByID($pEventID);
		}
		else
		{
			$eventsList = TaskObj::getEventsByTaskCode($pTaskCode, 1);
		}

		if ($eventsList['result'] != '')
		{
			throw new Exception($eventsList['result']);
		}

		return $eventsList['events'];
	}

	/**
	 * Create an exporter for the data set type and filters supplied
	 *
	 * @param mysqli $connection
	 * @param Smarty $smarty
	 * @param string $dataSet
	 * @param mixed[] $filters
	 * @param int $batchCount
	 * @param int|null $lastId
	 * @return DataSetExporterInterface
	 * @throws Exception
	 */
	private static function createExporter($connection, $smarty, $dataSet, $filters, $batchCount, $lastId)
	{
		switch (strtolower($dataSet))
		{
			case 'customer': return new CustomerExporter($connection, $smarty, $filters, $batchCount, $lastId);
		}

		throw new Exception(sprintf('Unsupported export data set "%s"', $dataSet));
	}

	/**
	 * @param string $dataSet
	 * @return string
	 * @throws Exception
	 */
	private static function generateExportBasePath($dataSet)
	{
		global $ac_config;

		if (!isset($ac_config['PRIVATEDATAEXPORTPATH'])) {
			throw new Exception('Configuration parameter PRIVATEDATAEXPORTPATH not set.');
		}

		if (!file_exists($ac_config['PRIVATEDATAEXPORTPATH'])) {
			throw new Exception('Configuration parameter PRIVATEDATAEXPORTPATH path does not exist.');
		}

		$dataPath = $ac_config['PRIVATEDATAEXPORTPATH'] . DIRECTORY_SEPARATOR . $dataSet;

		// Creating data set path
		if (!file_exists($dataPath)) {
			if (!mkdir($dataPath, 0777, true)) {
				throw new Exception(sprintf('Failed to create data export path "%s"', $dataPath));
			}
		}

		return realpath($dataPath);
	}

	/**
	 * @param string $basePath
	 * @param int $eventId
	 * @return string
	 * @throws Exception
	 */
	private static function generateBatchFilePath($basePath, $eventId)
	{
		$dataPath = $basePath . DIRECTORY_SEPARATOR . $eventId;

		// Creating data set path
		if (!file_exists($dataPath)) {
			if (!mkdir($dataPath, 0777, true)) {
				throw new Exception(sprintf('Failed to create data export path "%s"', $dataPath));
			}
		}

		return realpath($dataPath);
	}

	/**
	 * Generate an export file name, excluding the extension
	 *
	 * @param int $eventId
	 * @return string
	 * @throws Exception
	 */
	private static function generateExportFileName($eventId)
	{
		return sprintf('%d-%s', $eventId, date('d_M_Y_His'));
	}
}
