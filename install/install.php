<?php
ini_set('display_errors', true);
ini_set('error_reporting', E_ALL);
mysqli_report(MYSQLI_REPORT_ERROR);
	// OS Types
	define('TPX_OS_TYPE_WINDOWS', 0);
	define('TPX_OS_TYPE_UNIX', 1);
	define('TPX_OS_TYPE_MAC', 2);

	define('TPX_DBSCHEMA_MAW', 0);

	// Change working directory
	chdir(dirname(__FILE__));
	require_once '../libs/external/vendor/autoload.php';
	require_once '../Utils/UtilsDatabase.php';
	require_once '../Utils/Utils.php';
	require_once '../Utils/UtilsAuthenticate.php';
	require_once '../Utils/UtilsConstants.php';

	// include install flag object
	require_once(dirname(__FILE__) . '/UtilsInstallFlags.php');

	$scriptPath = dirname(__FILE__) . "/scripts/";
	$ac_config = UtilsObj::readConfigFile('../config/mediaalbumweb.conf');
    $systemConfig = array();

	// Make sure all required PHP Extensions are installed correctly before doing anything.
	checkSystemEnvironment();

	// Confirm install or backup
	selectAction();

	/**
	* Get connection to current database
	* @author Loc Dinh
	* @return connection handler
	*/
	function getTaopixConnection()
	{

		global $ac_config;

		$dbConnection = new mysqli($ac_config['DBHOST'], $ac_config['DBUSER'], $ac_config['DBPASS'], $ac_config['DBNAME']);
		if (($dbConnection) && (! mysqli_connect_errno()))
		{
			$dbConnection->autocommit(true);
			return $dbConnection;
		}
		else
		{
			print mysqli_connect_error();
			return false;
		}

	}


	/**
	* Select initial actions
	* @author Loc Dinh
	*/
	function selectAction()
	{

		promptMessage("initialSelect");
		$answer = trim(fgets(STDIN));
		if($answer!=1 && $answer!=2 && $answer!=3)
		{
			selectAction();
		}
		elseif($answer==1)
		{
			confirmVersion();
			promptMessage("beforeConnectAsRoot");
			install();
		}
		elseif($answer==2)
		{
			upgrade();
		}
		else
		{
			echo "\n";
			exit(0);
		}
	}


	/**
	* Verify Root Login
	* @author Loc Dinh
	* @return dbConnection/false
	*/
	function verifyRootLogin($pMode = 'install')
	{
		global $ac_config;

		$result = array();
        $serverIp = array_key_exists('DBHOST', $ac_config) ? $ac_config['DBHOST'] : '';

		if ($pMode != 'upgrade')
		{
			promptMessage("serverIp");
			$serverIp = trim(fgets(STDIN));
			if (strtoupper($serverIp) == 'A')
            {
                selectAction();
            }
		}

		promptMessage("rootUser");
		$rootUser = trim(fgets(STDIN));
		if (strtoupper($rootUser) == 'A')
        {
            selectAction();
        }

		promptMessage("rootPassword");
		$rootPassword = trim(fgets(STDIN));
		echo "\n";
		if (strtoupper($rootPassword) == 'A')
        {
            selectAction();
        }

		if ($pMode == 'upgrade')
		{
			$connection = new mysqli($serverIp, $rootUser, $rootPassword, $ac_config['DBNAME']);
		}
		else
		{
			$connection = new mysqli($serverIp, $rootUser, $rootPassword);
		}

		if($connection->connect_error)
		{
			promptMessage("unableToConnectAsRoot");

			if ($pMode == 'upgrade')
			{
				upgrade(0, 0);
			}
			else
			{
				install();
			}

		}
		else
		{
			// Check MySQL strict mode, stop if it is on.
			checkMySQLStrictMode($connection);

			$result['connection'] = $connection;
			$result['serverIp'] = $serverIp;
			promptMessage("verifyRootLogin");
			return $result;
		}
	}


	/**
	* Prompt users to backup database
	* @author Loc Dinh
	*/
	function confirmBackup()
	{

		promptMessage("confirmBackup");
		$answer = trim(fgets(STDIN));
        if (strtoupper($answer) == 'A')
        {
            selectAction();
        }
		if(strtoupper($answer) != 'Y')
		{
			promptMessage("forceBackup");
			exit(0);
		}
	}


	/**
	* Verify password for existing database user
	* @return Boolean
	*/
	function checkDbPassword($pServerIp, $pDbUser, $pDbPassword)
	{
		$result = false;

		if (($pServerIp != '') && ($pDbUser != '') && ($pDbPassword != ''))
		{
			$dbConnection = new mysqli($pServerIp, $pDbUser, $pDbPassword);
			if(($dbConnection) && (! mysqli_connect_errno()))
			{
				$result = true;
			}
			else
			{
				promptMessage("invalidDbPassword");
				$result = false;
			}
		}

		return $result;
	}


	/**
	* - Create new database
	* - Create new db user account
	* - Assign previleges to new user
	* @author Loc Dinh
	*/
	function install()
	{
		global $ac_config;

		$rootConfig = verifyRootLogin('install');
		$connection = $rootConfig['connection'];
		$serverIp   = $rootConfig['serverIp'];
		$dbConfig = array();

		promptMessage("databaseName");
		$dbName = checkDbExistence($connection);

		echo "\nPlease enter the details of an existing user or the name of the database user you wish to create.\n";
		$dbUserNameArray = checkUserExist($connection, '');
		$dbUserName = $dbUserNameArray['username'];
		$userExist = $dbUserNameArray['existinguser'];

		promptMessage("databasePassword");
		$dbPassword = trim(fgets(STDIN));
		if (strtoupper($dbPassword) == 'A')
        {
            selectAction();
        }

		if($userExist == 1)
		{
			while(! checkDbPassword($serverIp, $dbUserName, $dbPassword))
			{
				$dbUserNameArray = checkUserExist($connection, '');
				$dbUserName = $dbUserNameArray['username'];
				$userExist = $dbUserNameArray['existinguser'];

				promptMessage("databasePassword");
				$dbPassword = trim(fgets(STDIN));
				if (strtoupper($dbPassword) == 'A')
                {
                    selectAction();
                }
			}
		}


		echo "\n Create new database '".$dbName."' with new database user '".$dbUserName."'. Continue? (Y/N): ";
		$continue = strtoupper(trim(fgets(STDIN)));
		echo "\n";

        if (strtoupper($continue) == 'A')
        {
            selectAction();
        }

		while($continue!='Y' && $continue!='N')
		{
			echo "\n Create new database '".$dbName."' with new database user '".$dbUserName."'. Continue? (Y/N): ";
			$continue = trim(fgets(STDIN));
			echo "\n";
		}
		if(strtoupper($continue) == 'N')
		{
			exit(0);
		}
        if (strtoupper($continue) == 'A')
        {
            selectAction();
        }

		if (mysqli_query($connection,"CREATE DATABASE `".$dbName."` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci"))
		{
            $hostPart = 'mysql' === $serverIp ? '%' : $serverIp;
            if (!mysqli_query($connection,"CREATE USER '".$dbUserName."'@'".$hostPart."' IDENTIFIED BY '".$dbPassword."';")) { echo "Failed to create user\n"; exit(0); }
            if (!mysqli_query($connection,"GRANT ALL PRIVILEGES ON `".$dbName."`.* TO '".$dbUserName."'@'".$hostPart."' WITH GRANT OPTION")) { echo "Failed to privilege user\n"; exit(0); }
            if (!mysqli_query($connection,"FLUSH PRIVILEGES")) { echo "Failed to flush\n"; exit(0); }

			$dbConfig['serverIp']   = $rootConfig['serverIp'];
			$dbConfig['dbName']     = $dbName;
			$dbConfig['dbUserName'] = $dbUserName;
			$dbConfig['dbPassword'] = $dbPassword;

			// copy the details read form the file into the global config file so that the database object works
			$ac_config['DBHOST'] = $dbConfig['serverIp'];
			$ac_config['DBNAME'] = $dbConfig['dbName'];
			$ac_config['DBUSER'] = $dbConfig['dbUserName'];
			$ac_config['DBPASS'] = $dbConfig['dbPassword'];

			replaceConfigFile($dbConfig);
			promptMessage("databaseCreated");
            $newConnection = new mysqli($serverIp, $dbUserName, $dbPassword, $dbName);
            $max = 3;
            $tries = 0;
            while (null !== $newConnection->connect_error && $max > $tries) {
                usleep(10);
                $newConnection = new mysqli($serverIp, $dbUserName, $dbPassword, $dbName);
                $tries++;
            }

			// enter Control centre Administrator account details
			echo "\n Enter Control Centre administrator username: ";
			$controlCentreAdminUser = trim(fgets(STDIN));
			while (trim($controlCentreAdminUser) == '')
			{
				echo "\n Enter Control Centre administrator username: ";
				$controlCentreAdminUser = trim(fgets(STDIN));
			}

            if (strtoupper($controlCentreAdminUser) == 'A')
            {
                selectAction();
            }


			echo "\n Enter Control Centre administrator password: ";
			$controlCentreAdminPassword = trim(fgets(STDIN));
            if (strtoupper($controlCentreAdminPassword) == 'A')
            {
                selectAction();
            }

			while (trim($controlCentreAdminPassword) == '')
			{
				echo "\n Enter Control Centre administrator password: ";
				$controlCentreAdminPassword = trim(fgets(STDIN));
			}

			while (strlen($controlCentreAdminPassword) < 8)
			{
				echo "\n The password should be at least 8 characters long, please choose a different one: ";
				$controlCentreAdminPassword = trim(fgets(STDIN));
			}

			checkAdminAccountDetailsForNewInstallation($controlCentreAdminUser, $controlCentreAdminPassword);

			runScript('install', $newConnection, $controlCentreAdminUser, $controlCentreAdminPassword);
		}
		else
		{
			promptMessage("databaseError",mysqli_error($connection));
                        readline("Press Return key to exit");
			exit(1);
		}
	}


	function checkAdminAccountDetailsForNewInstallation(&$pControlCentreAdminUser = '', &$pControlCentreAdminPassword = '')
	{

		if ($pControlCentreAdminUser == '')
		{
			// enter Control centre Administrator account details
			echo "\n Enter Control Centre administrator username: ";
			$pControlCentreAdminUser = trim(fgets(STDIN));
			while (trim($pControlCentreAdminUser) == '')
			{
				echo "\n Enter Control Centre administrator username: ";
				$pControlCentreAdminUser = trim(fgets(STDIN));
			}

            if (strtoupper($pControlCentreAdminUser) == 'A')
            {
                selectAction();
            }

		}


		if ($pControlCentreAdminPassword == '')
		{
			echo "\n Enter Control Centre administrator password: ";
			$pControlCentreAdminPassword = trim(fgets(STDIN));
			while ($pControlCentreAdminPassword == '')
			{
				echo "\n Enter Control Centre administrator password: ";
				$pControlCentreAdminPassword = trim(fgets(STDIN));
			}

            if (strtoupper($pControlCentreAdminPassword) == 'A')
            {
                selectAction();
            }
		}


		// warn users if their password is in the black list of weak password.
		$fileName = 'passwordBlackList.txt';
		$handle = fopen($fileName, "r");
		$passwordBlackListStr = fread($handle, filesize($fileName));
		fclose($handle);
		$passwordBlackListArr = explode(",", $passwordBlackListStr);

		while (in_array(md5($pControlCentreAdminPassword), $passwordBlackListArr) || strlen($pControlCentreAdminPassword) < 8)
		{
			if (strlen($pControlCentreAdminPassword) < 8)
			{
				echo "\n The password should be at least 8 characters long, please choose a different one: ";
				$pControlCentreAdminPassword = trim(fgets(STDIN));
			}
			else
			{
				echo "\n The password you entered appears to be unsecure, please choose a different one: ";
				$pControlCentreAdminPassword = trim(fgets(STDIN));
			}
		}

		echo "\n Your Control Centre admin account is going to be created with the following details: \n";
		echo "\n Username: " . $pControlCentreAdminUser . " \n";
		echo " Password: " . $pControlCentreAdminPassword . " \n";
		echo "\n Is this correct? (Y/N): ";
		$answer = strtoupper(trim(fgets(STDIN)));
		if ($answer != 'Y')
		{
			$pControlCentreAdminUser = '';
			$pControlCentreAdminPassword = '';
			checkAdminAccountDetailsForNewInstallation($pControlCentreAdminUser, $pControlCentreAdminPassword);
		}
	}

	/**
	* Perform upgrade
	* @author Loc Dinh
	* param: pConfirmBackup - turn on/off the question to ask 'have you backed up'?
	* param: pConfirmVersion - turn on/off the question to confirm the version to upgrade to?
	*/
	function upgrade($pConfirmBackup = 1, $pConfirmVersion = 1)
	{
    	global $ac_config;

		$error = checkDbEngine();
		if($error == '')
		{
			// check PHP version is >= 7.2
			checkPHPVersion();

			$connection = getTaopixConnection();

			fixDuplicatedPriceListCode($connection);
			// check MySQL strictmode, stop if it is on
			checkMySQLStrictMode($connection);

			if ($pConfirmVersion == 1)
			{
				confirmVersion();
			}

			if ($pConfirmBackup == 1)
			{
				confirmBackup();
			}

			runScript('upgrade', $connection, '', '');
		}
		else
		{
			echo "\n #ERROR: Invalid database engine found in following tables: \n".$error."\n";
                        readline("Press Return key to exit");
			exit(1);
		}
	}


	/**
	* Ask user to confirm upgrade to version
	* @author Loc Dinh
	*/
	function confirmVersion()
	{
		global $scriptPath;
		global $ac_config;

		// Get filenames in Script folder.
		if ($dh = opendir($scriptPath))
		{
			$fileName_array = array();
			while (($fileName = readdir($dh)) !== false)
			{
				if($fileName != "." && $fileName != "..")
				{
					if(preg_match("/\.sql$/",$fileName))
					{
						$fileName_array[] = $fileName;
					}
				}
			}
			closedir($dh);
			sort($fileName_array);
		}

		// get the version number out of the last file name
		$lastScriptName = end($fileName_array);


		$webVersionUpdateStatement = '';
	   	$textData = UtilsObj::readTextFile($scriptPath . $lastScriptName);
		$textData = str_replace("\r\n", "\n", $textData);
		$textData = str_replace("\r", "\n", $textData);

	   	$textDataArray = splitFileContent($textData);

	   	for ($i = 0; $i < count($textDataArray); $i++)
	   	{
	   		// find the query that update webversionnumber
	   		if ( (strpos($textDataArray[$i],"UPDATE") !== false) && (strpos($textDataArray[$i],"SET") !== false) &&
	   			(strpos($textDataArray[$i],"`webversionnumber`") !== false) && (strpos($textDataArray[$i],"`SYSTEMCONFIG`") !== false))
	   		{
	   			$webVersionUpdateStatement = $textDataArray[$i];
	   		}

			// find the query that update webversionstring
	   		if ( (strpos($textDataArray[$i],"UPDATE") !== false) && (strpos($textDataArray[$i],"SET") !== false) &&
	   			(strpos($textDataArray[$i],"`webversionstring`") !== false) && (strpos($textDataArray[$i],"`SYSTEMCONFIG`") !== false))
	   		{
	   			$webVersionStringUpdateStatement = $textDataArray[$i];
	   		}

	   	}

	   	if ($webVersionUpdateStatement == '')
	   	{
	   		echo "\n #ERROR: Can not find the webversionnumber in the last sql file.";
                        readline("Press Return key to exit");
	   		exit(1);
	   	}
	   	else
	   	{
			// If found the query that updates the webversion, try to extract the webversion out of it.
	   		$pieces = explode("=", $webVersionUpdateStatement, 2);
	   		$webVersion = $pieces[1];

			// If found the query that updates the webversion, try to extract the webversion string out of it.
	   		$pieces = explode("=", $webVersionStringUpdateStatement, 2);
	   		$webVersionString = $pieces[1];

	   	}

		$cleanWebVersion = str_replace(Array('\'',';', ' '), '', $webVersion);

		$cleanWebVersionString = str_replace(Array('\'',';', ' '), '', $webVersionString);

		$question = "\n You are going to install/upgrade to version: " . $cleanWebVersionString  . "\n";
		$question .= " Is it correct? (Y/N): ";

		echo $question;

		$answer = strtoupper(trim(fgets(STDIN)));
        if (strtoupper($answer) == 'A')
        {
            selectAction();
        }

		if ($answer != 'Y')
		{
			echo "\n Please check the version then run this upgrade again.\n\n";
                        readline("Press Return key to exit");
			exit(1);
		}
	}


	/**
	* Check database engine, if not InnoDb then stop script
	* @author Loc Dinh
	* @return String
	*/
	function checkDbEngine()
	{
		global $ac_config;
		$connection = getTaopixConnection();

		$tempArray = array();
		$dbTableStatus_query = mysqli_query($connection,"SHOW TABLE STATUS FROM ".$ac_config['DBNAME']);

		$invalidEngineTables = "";
		while($c = $dbTableStatus_query->fetch_object())
		{

			if($c->Engine != 'InnoDB')
			{
				$invalidEngineTables .= "\n".$c->Name."\n";
			}
		}

		return $invalidEngineTables;
	}



	/**
	* Read Script folder & run scripts inside
	* @author Loc Dinh
	*/
	function runScript($pMode, $pConnection, $pAdminUser, $pAdminPassword)
	{

		global $scriptPath;
		global $ac_config;
        global $systemConfig;

		$scriptNameList	='';

		// remove the time limit when running the scripts
		set_time_limit(0);

		// Get filenames in Script folder.
		if ($dh = opendir($scriptPath))
		{
			$fileName_array = array();
			while (($fileName = readdir($dh)) !== false)
			{
				if($fileName != "." && $fileName != "..")
				{
					if(preg_match("/\.sql$/",$fileName))
					{
						$fileName_array[] = $fileName;
					}
				}
			}
			closedir($dh);
			sort($fileName_array);

			if ($pMode == 'upgrade')
			{
				$rootConnection = verifyRootLogin('upgrade');
				$pConnection = $rootConnection['connection'];

				// check the installflags column exists
				$columnExistsResult = InstallFlagsObj::columnExists($ac_config['DBNAME'], 'ONLINEBASKET', 'saved');

				if (! $columnExistsResult['exists'])
				{
					InstallFlagsObj::addColumnIfNotExist($ac_config['DBNAME'], 'ONLINEBASKET', 'saved', 'TINYINT(1)', 0, 'layoutname');
				}

				$finalFileName_array = getStartingPoint($fileName_array, $pConnection);
				if(count($finalFileName_array) > 0)
				{
					echo "\n Upgrade will start from script '".$finalFileName_array[0]."'. Continue? (Y/N): ";
					$continue = strtoupper(trim(fgets(STDIN)));
                    if (strtoupper($continue) == 'A')
                    {
                        selectAction();
                    }

					while($continue != 'Y' && $continue != 'N')
					{
						echo "\n Upgrade will start from script '".$finalFileName_array[0]."'. Continue? (Y/N): ";
						$continue = trim(fgets(STDIN));
					}
					echo "\n";

					if(strtoupper($continue) == 'N')
					{
						exit(0);
					}
				}
			}
			else
			{
				$finalFileName_array = $fileName_array;
			}


			$scriptCount = count($finalFileName_array);
			if($scriptCount > 0)
			{
				$result = '';
				for($i = 0; $i < count($finalFileName_array); $i++)
				{
                    // read the version information and queries from the script file
                    $currentScriptInformation = parseScriptFile($finalFileName_array[$i]);

					$pos = strpos($finalFileName_array[$i], "0021_");
					if($pos!==false)
					{
						runStoredProcedure($pConnection, "dropIndexIfExists");
					}


					// Create stored procedure before running script 21
					$pos = strpos($finalFileName_array[$i], "0021_");
					if($pos!==false)
					{
						runStoredProcedure($pConnection, "defaultPriceConvert");
					}

					// Drop stored procedure before running script 22.
					$pos = strpos($finalFileName_array[$i], "0022_");
					if($pos!==false)
					{
						mysqli_query($pConnection, "DROP PROCEDURE IF EXISTS defaultPriceConvert");
					}

					/*  Check "orderid" index before excecuting script 0031_,
					 *	Remove it if it has been created from the previous scripts.
					 */
					$pos = strpos($finalFileName_array[$i], "0031_");
					if($pos!==false)
					{
						runStoredProcedure($pConnection, "dropIndexIfExists");
					}

					/*  Check "lastruntime" index on EVENTS table before excecuting script 0114_,
					 *	Remove it if it has been created from the previous scripts.
					 */
					$pos = strpos($finalFileName_array[$i],"0114_");
					if($pos!==false)
					{
						runStoredProcedure($pConnection,"dropIndexIfExists2");
					}

					// Drop stored procedure before running script 15.
					$pos = strpos($finalFileName_array[$i],"0115_");
					if($pos!==false)
					{
						mysqli_query($pConnection,"DROP PROCEDURE IF EXISTS dropIndexIfExists2");
					}

					/*  Create database triggers for the auto update cache.
					 *	Introduced in version 4.0.0a3
					 */
					$pos = strpos($finalFileName_array[$i], "0126_");
					if($pos!==false)
                    {
                        runTriggers($pConnection, 'updateLicenseKeyCacheVersion');
                    }

					/*  Create database triggers for adding and editing new customers.
					 *	Introduced in version 4.1.0a2
					 */
					$pos = strpos($finalFileName_array[$i],"0157_");
					if($pos!==false)
                    {
                        runTriggers($pConnection, 'userAccountTrigger');
                    }

					/*  Create procedure to optimise data returned by Auto-Update
					 *	Introduced in version 4.2.0a14
					 */
					$pos = strpos($finalFileName_array[$i],"0194_");
                    if($pos!==false)
                    {
                        runStoredProcedure($pConnection,"autoUpdateCacheUpdateBrandingVersion");
					}

					/*  Create trigger to optimise data returned by Auto-Update
					 *	Introduced in version 4.2.0a14
					 */
					$pos = strpos($finalFileName_array[$i],"0194_");
					if($pos!==false)
					{
                        runTriggers($pConnection, 'applicationFilesAutoUpdateCache');
                    }

					/*  Create trigger to add triggers to keywords for Component Upsell
					 *	Introduced in version 2022r1
					 */
					$pos = strpos($finalFileName_array[$i],"0545_");
					if($pos!==false)
					{
                        runTriggers($pConnection, 'metadataTrigger');
                    }

					/*  Create procedure to Add column if exists
					 *	Introduced in version 2023r1.3
					 */
					$pos = strpos($finalFileName_array[$i],"0558_");
                    if($pos!==false)
                    {
                        runStoredProcedure($pConnection,'addColumn');
					}

					$externalScriptRunner = new ExternalScriptRunner(array(TPX_DBSCHEMA_MAW => $ac_config['DBNAME']), $pConnection, $pMode, $ac_config);

					// script which will be called pre SQL install script
					$externalScriptRunner->registerPreScript('moveAssetsFromDatabase', '2021.2.0', '2021.2.0.4');

					// script which will be called post SQL install script
					$externalScriptRunner->registerPostScript('sessionUpdate2017R1A2', '2017.1.0', '2017.1.0.2');
					$externalScriptRunner->registerPostScript('updateDesktopDependencies', '2017.1.0', '2017.1.0.24');
					$externalScriptRunner->registerPostScript('updateSessionPicturesStructure', '2018.3.0', '2018.3.0.20');
					$externalScriptRunner->registerPostScript('sessionUpdatePricingEngine', '2018.3.0', '2018.3.0.63');
					$externalScriptRunner->registerPostScript('sessionUpdatePhotoPrintsTransformationFlag', '2018.3.0', '2018.3.0.67');
					$externalScriptRunner->registerPostScript('addBulkEmailSettingsToControlCentreConfig', '2018.3.0', '2018.3.0.92');
					$externalScriptRunner->registerPostScript('createUnlockSystemAccountUser', '2018.4.0', '2018.4.0.29');
					$externalScriptRunner->registerPostScript('createDataExportFolder', '2018.5.0', '2018.5.0.7');
					$externalScriptRunner->registerPostScript('addResetPasswordExpirySettingsToControlCentreConfig', '2018.5.0', '2018.5.0.10');
        			$externalScriptRunner->registerPostScript('generateCsrfSigningKey', '2018.5.0', '2018.5.0.2');
					$externalScriptRunner->registerPostScript('addBrandingCustomisationSettings', '2018.5.0', '2018.5.0.34');
					$externalScriptRunner->registerPostScript('weChatSessionUpdates', '2019.1.0', '2019.1.0.61');
					$externalScriptRunner->registerPostScript('generateCSPInformation', '2020.1.0', '2020.1.0.6');
					$externalScriptRunner->registerPostScript('addOrderPreviewsSettings', '2020.2.0', '2020.2.0.5');
					$externalScriptRunner->registerPostScript('addAIMode', '2020.3.0', '2020.3.0.6');
					$externalScriptRunner->registerPostScript('addOrderStatusCacheSettings', '2021.2.0', '2021.2.0.2');
					$externalScriptRunner->registerPostScript('convertProductCategoriesToGroups', '2021.3.1', '2021.3.1.1');
					$externalScriptRunner->registerPostScript('addDesktopResourcesSettings', '2021.2.0', '2021.2.0.8');
					$externalScriptRunner->registerPostScript('updateConfig', '2021.3.0', '2021.3.0.3');
					$externalScriptRunner->registerPostScript('addOnlineCachePath', '2021.3.0', '2021.3.0.3');
					$externalScriptRunner->registerPostScript('updateSessionAutoIncrement', '2021.3.0', '2021.3.0.3');
					$externalScriptRunner->registerPostScript('addMetaDataKeywordsPathToControlCentreConfig', '2021.3.1', '2021.3.1.1');
					$externalScriptRunner->registerPostScript('updateSessionProductLinking', '2022.1.0', '2022.1.0.1');
					$externalScriptRunner->registerPostScript('validateOnlineCachePath', '2022.1.0', '2022.1.0.3');
					$externalScriptRunner->registerPostScript('addOAuthStatusCacheSettings', '2023.1.0', '2023.1.0.0');
                    $externalScriptRunner->registerPostScript('addPromoPanelSettings', '2023.1.0', '2023.1.0.0');
                    $externalScriptRunner->registerPostScript('updateShopifyWebhooksAndCache', '2024.1.0', '2024.1.0.0');
                    $externalScriptRunner->registerPostScript('newOnlineConfig', '2024.1.0', '2024.1.0.0');


                    // Use the version numbers in the upgrade script to determine any actions required -
					// Version 5.0.0

					$updateData = array();
					$updateSessionData = array();
                    $updateExternalCartProjectData = [];
                    $updateOnlineBasketData = [];

                    switch ($currentScriptInformation['releaseVersion'])
                    {
                        case '5.0.0':
                        {
                            /*  Remove Yugoslavia as country if not used
                             *	Introduced in version 5.0.0a3 (script 0226) */

                            // Check if the country is used by a user
                            $result = mysqli_query($pConnection, 'SELECT id FROM `USERS` WHERE `countrycode` = "YU"');
                            if ($result->num_rows == 0)
                            {
                                // Check if the country is used by a company address
                                $result = mysqli_query($pConnection, 'SELECT id FROM `COMPANIES` WHERE `countrycode` = "YU"');
                                if ($result->num_rows == 0)
                                {
                                    // Check if the country is used by a site address
                                    $result = mysqli_query($pConnection, 'SELECT id FROM `SITES` WHERE `countrycode` = "YU"');
                                    if ($result->num_rows == 0)
                                    {
                                        // Check if the country is used by a billing address
                                        $result = mysqli_query($pConnection, 'SELECT id FROM `ORDERHEADER` WHERE `billingcustomercountrycode` = "YU"');
                                        if ($result->num_rows == 0)
                                        {
                                            // Check if the country is used by a shipping address
                                            $result = mysqli_query($pConnection, 'SELECT id FROM `ORDERSHIPPING` WHERE `shippingcustomercountrycode` = "YU"');
                                            if ($result->num_rows == 0)
                                            {
                                                // Check if the country is used by a license key address
                                                $result = mysqli_query($pConnection, 'SELECT id FROM `LICENSEKEYS` WHERE `countrycode` = "YU"');
                                                if ($result->num_rows == 0)
                                                {
                                                    mysqli_query($pConnection, 'DELETE FROM `COUNTRIES` WHERE `isocode2` = "YU"');
                                                }
                                            }

                                        }
                                    }
                                }
                            }
                            $result->close();

                            break;
                        }

                    }

					$preScriptOutput = $externalScriptRunner->runPreScripts($currentScriptInformation['releaseVersion'], $currentScriptInformation['versionNumber']);

					$scriptResult = actionScriptOutput($pConnection, $preScriptOutput, $updateSessionData, $updateExternalCartProjectData, $updateOnlineBasketData);

					if ($scriptResult != '')
					{
						echo "#ERROR: " . $scriptResult . "\r\n";
                                                readline("Press Return key to exit");
						exit(1);
					}

                    // run the update script
					promptMessage("startScript", $finalFileName_array[$i]);
                    $scriptName = executeScriptQuery($currentScriptInformation, $pConnection);
					$scriptNameList .= "\n ".$scriptName;
					promptMessage("endScript", $finalFileName_array[$i]);

					// Insert a dummy record after creating SYSTEMCONFIG table in Script 0014.
					$pos = strpos($finalFileName_array[$i], "0014_");
					if($pos!==false)
					{
						// Add lastinstallscriptnumber if not exist
						addColumnIfNotExist($pConnection);

						// Insert new record in SYSTEMCONFIG with lastinstallscriptnumber
						$lastScriptPrefix = explode("_",$scriptName);
						if($lastScriptPrefix[0] == '0014')
						{
							mysqli_query($pConnection,"INSERT INTO `SYSTEMCONFIG` (`datecreated`,`lastinstallscriptnumber`) VALUES (now(),'".$lastScriptPrefix[0]."')");
						}
					}


                    /*  Update store procedure to fix issue in fb6075
                     *	Introduced in version 4.3.0
                     */
					$pos = strpos($finalFileName_array[$i],"0224_");
					if ($pos !== false)
					{
                        mysqli_query($pConnection, "DROP PROCEDURE IF EXISTS `autoUpdateCacheUpdateBrandingVersion`;");
                        runStoredProcedure($pConnection,"AUCACHE_UPDATEBRANDINGVERSION");
                        runTriggers($pConnection, 'fixApplicationFilesAutoUpdateCache');
                    }

                    // Use the version numbers in the upgrade script to determine any actions required -
                    // Version 5.0.0
                    // Version 2016r2

					$updateSessionData = array();
                    $updateExternalCartProjectData = [];
                    $updateOnlineBasketData = [];

                    switch ($currentScriptInformation['releaseVersion'])
                    {
                        case '5.0.0':
                        {
                            // update the session with extra information
                            $updateSessionData[] = array('path' => array('items'),
                            						'changes' => array(array('key' => 'itemproductcollectionorigownercode', 'insert' => $systemConfig['ownercode'])));
                            $updateSessionData[] = array('path' => array('order'),
                            						'changes' => array(array(
                    														array('key' => 'voucherapplicationmethod', 'insert' => TPX_VOUCHER_APPLY_EACH_MATCHING_PRODUCT),
                    														array('key' => 'voucherapplytoqty', 'insert' => 9999),
																			array('key' => 'ordertotalitemdiscountable', 'insert' => 0),
																			array('key' => 'onlineclienttime', 'insert' => 0)
																		)));
                            break;
                        }
                        case '2016.2.0':
                        {
                        	// update every session changing the case of the userAddressUpdated session item
                            $updateSessionData[] = array('path' => array(), 'changes' => array(array('key' => 'userAddressUpdated', 'changekey' => 'useraddressupdated')));

                        	break;
                        }
                    }

                    switch ($currentScriptInformation['versionNumber'])
					{
                        case '2016.2.0.10':
						{
							if (array_key_exists('HIGHLEVELBASKETAPIBRANDS', $ac_config))
							{
								$multiLineBasketBrandArray = explode(',', $ac_config['HIGHLEVELBASKETAPIBRANDS']);
								$defaultKey = array_search('DEFAULT', $multiLineBasketBrandArray);
								$brandString = '';

								// there is the DEFAULT brand found so we must set it to empty for the default brand code
								if ($defaultKey !== false)
								{
									$multiLineBasketBrandArray[$defaultKey] = '';
								}

								$brandString = '"' . implode('","', $multiLineBasketBrandArray) . '"';
								mysqli_query($pConnection, "UPDATE `BRANDING` SET `onlinedesignerusemultilineworkflow` = 1 WHERE `code` IN (". $brandString . ")");
							}

							break;
						}
						case '2016.2.0.28':
						{
							if ($pMode == 'install')
							{
								// add the saved column to the ONLINEBASKET table
								InstallFlagsObj::addColumnIfNotExist($ac_config['DBNAME'], 'ONLINEBASKET', 'saved', 'TINYINT(1)', 0, 'layoutname');
							}

							break;
  						}
						case '2016.3.0.1':
                        {
                            // update the session with extra information
                            $updateSessionData[] = array('path' => array('order'),
                            						'changes' => array(
														array('key' => 'jobtickettemplate', 'update' => 'jobticket_large', 'condition' => array('==', 'jobticket'))
													));

							if ($pMode == 'upgrade')
							{
								require_once(dirname(__FILE__) . '/updateRegisteredTaxNumberForBrazil.php');

								echo "\nStarting updateRegisteredTaxNumberForBrazil process.\n";

								$updateBrazilTaxNumberResult = updateRegisteredTaxNumberForBrazil::run();

								if ($updateBrazilTaxNumberResult['error'] == '')
								{
									echo "\nupdateRegisteredTaxNumberForBrazil process complete.\n";
								}
								else
								{
									echo "\nupdateRegisteredTaxNumberForBrazil process failed...\n";
									echo "\n" . $updateBrazilTaxNumberResult['errorparam'] . "\n";
								}
							}

                            // add new key for externalcartscriptexists, default to 0
							$updateSessionData[] = array('path' => array('order'),
                            						'changes' => array(
														array('key' => 'externalcartscriptexists', 'insert' => 0)
													));
                           break;
                        }
                    }

					$postScriptOutput = $externalScriptRunner->runPostScripts($currentScriptInformation['releaseVersion'], $currentScriptInformation['versionNumber']);

					$scriptResult = actionScriptOutput($pConnection, $postScriptOutput, $updateSessionData, $updateExternalCartProjectData, $updateOnlineBasketData);

					if ($scriptResult != '')
					{
						echo "#ERROR: " . $scriptResult . "\r\n";
                                                readline("Press Return key to exit");
						exit(1);
					}

                }

				// Add lastinstallscriptnumber if not exist
				addColumnIfNotExist($pConnection);

				//insert new record in SYSTEMCONFIG with lastinstallscriptnumber
				$lastScriptPrefix = explode("_",$scriptName);

				mysqli_query($pConnection,"UPDATE `SYSTEMCONFIG` SET `lastinstallscriptnumber` = '".$lastScriptPrefix[0]."'");

				// Set default URLs if it is a new installation.
				if($pMode != 'upgrade')
				{
					mysqli_query($pConnection,"UPDATE `BRANDING` SET `displayurl` = '".$ac_config['WEBURL']."', `weburl` = '".$ac_config['WEBURL']."' WHERE code = '' AND name = '' ");
				}


				// update control centre admin user & password if this is new installation
				if($pMode == 'install')
				{
					$generatePasswordHash = AuthenticateObj::generatePasswordHash(hash('md5', $pAdminPassword), TPX_PASSWORDFORMAT_MD5);
					if ($generatePasswordHash['result'] == '')
					{
						$passwordHash = "'" . $generatePasswordHash['data'] . "'";
					}
					else
					{
						$passwordHash = "MD5('" . $pAdminPassword . "')";
					}

					mysqli_query($pConnection, "UPDATE `USERS` SET `login` = '".$pAdminUser."', `password` = ".$passwordHash." WHERE `login` = 'administrator' AND `customer` = 0 AND `usertype` = 0");
				}

				promptMessage("executedScriptList",$scriptNameList);

				promptMessage("promptRunWebConfigurator");

				if($pMode == 'upgrade')
				{
					checkAdminPassword($pConnection);
				}
				exit(0);
			}
			else
			{
				promptMessage("dbUpToDate");
				exit(0);
			}
		}
		else
		{
			die('Unable to read Scripts folder');
		}
	}

	function actionScriptOutput($pConnection, $pScriptOutputs, $pSessionDataArray, $pExternalCartProjectData, $pOnlineBasketData)
	{
		$sessionUpdateData = $pSessionDataArray;
        $externalCartProjectData = $pExternalCartProjectData;
        $onlineBasketChangesData = $pOnlineBasketData;
		$result = '';

		if (count($pScriptOutputs) > 0)
		{
			foreach ($pScriptOutputs as $scriptOutput)
			{
				if ($scriptOutput['result'] == '')
				{
					foreach ($scriptOutput['triggers'] as $triggerDetails)
					{
						runTriggers($pConnection, $triggerDetails['filename'], $triggerDetails['schema']);
					}

					foreach ($scriptOutput['procedures'] as $procedureDetails)
					{
						runStoredProcedure($pConnection, $procedureDetails['filename'], $procedureDetails['schema']);
					}

					// only get the session changes when performing an upgrade
					foreach ($scriptOutput['sessionchanges'] as $sessionChanges)
					{
						$sessionUpdateData[] = $sessionChanges;
					}

					// only get the external cart changes when performing an upgrade
					foreach ($scriptOutput['externalcartchanges'] as $externalCartChanges)
					{
						$externalCartProjectData[] = $externalCartChanges;
                    }

                    // only get the external cart changes when performing an upgrade
					foreach ($scriptOutput['onlinebasketchanges'] as $onlineBasketChanges)
					{
						$onlineBasketChangesData[] = $onlineBasketChanges;
					}
				}
				else
				{
					$result = "ERROR: " . $scriptOutput['result'] . "\r\n";
					break;
				}
			}
		}

		// if there is some session data to update then update it
		// this should only ever be populated when we are doing and upgrade
		if (count($sessionUpdateData) > 0)
		{
			updateSessionData($sessionUpdateData);
		}

		// if there is some external cart data to update then update it
		// this should only ever be populated when we are doing and upgrade
		if (($result === '') &&  (count($externalCartProjectData) > 0))
		{
			$result = updateProjectData($pConnection, $externalCartProjectData, 'projectorderdatacache');
		}

        // if there is some multi lines data to update then update it
		// this should only ever be populated when we are doing and upgrade
		if (($result === '') &&  (count($onlineBasketChangesData) > 0))
		{
			$result = updateProjectData($pConnection, $onlineBasketChangesData, 'onlinebasket');
		}


		return $result;
	}

	/**
	* Split the script file content into an array based on semicolons
	* @author Loc Dinh
	* @return String
	*/
	function splitFileContent($pStr)
	{

		$quote = '';
		$line = '';
		$sql = array();
		for ($i = 0; $i < strlen($pStr); $i++)
		{

			$line .= $pStr[$i];
			if (!isset($ignoreNextChar) || !$ignoreNextChar)
			{
				if ($pStr[$i] == ';' && $quote == '')
				{
					$sql[] = trim($line, " \n\r\t\v\0;");
					$line = '';
				}
				elseif ($pStr[$i] == '\\')
				{
					// Escape char; ignore the next char in the string
					$ignoreNextChar = TRUE;
				}
				elseif ($pStr[$i] == '"' || $pStr[$i] == "'" || $pStr[$i] == '`')
				{
					if ( $quote == '' ) // Start of a new quoted string; ends with same quote char
						$quote = $pStr[$i];
					elseif ($pStr[$i] == $quote ) // Current char matches quote char; quoted string ends
						$quote = '';
				}
				elseif($pStr[$i]=='\n' && $quote =='')
				{
					$line = '';
				}
			}
			else
			{
				$ignoreNextChar = FALSE;
			}
		}
		return $sql;
	}


    /**
     * parseScriptFile
     * - parse the script file and extract the version numbers are sql to execute
     *
     * @return array - version number of script and contents
     */
    function parseScriptFile($pScriptFile)
    {
		global $scriptPath;

        $scriptInformation = array();
        $versionNumber = '';
        $versionString = '';

        $scriptPrefix = explode("_", $pScriptFile);

        $scriptNumber = (int)$scriptPrefix[0];

        $textData = UtilsObj::readTextFile($scriptPath.$pScriptFile);
        $textData = str_replace("\r\n", "\n", $textData);
        $textData = str_replace("\r", "\n", $textData);

        $textData = "SET default_storage_engine = InnoDB;\n
                         SET character_set_results = utf8;\n
                         SET character_set_client = utf8;\n
                         SET character_set_connection = utf8;\n " . $textData;
        $textData = splitFileContent($textData);

        if ($scriptNumber > 21)
        {
            // script 22 was first to have webversionnumber and webversionstring in a nice format
            foreach ($textData as $query)
            {
                // get the version number from the script (5.0.0.1)
                if (strpos($query, '`webversionnumber`') > 0)
                {
                    $temp = explode('=', $query);
                    $versionNumber = str_replace(array('\'', ' ', ';'), '', $temp[1]);

                    // get the relesae version from the version number (5.0.0)
                    $versionArray = explode('.', $versionNumber);
                    while (count($versionArray) > 3)
                    {
                        array_pop($versionArray);
                    }
                    $releaseVersion = implode('.', $versionArray);
                }

                // get the version string from the script (5.0.0a1)
                if (strpos($query, '`webversionstring`') > 0)
                {
                    $temp = explode('=', $query);
                    $versionString = str_replace(array('\'', ' ', ';'), '', $temp[1]);
                }
            }
        }

        $scriptInformation['scriptFile'] = $pScriptFile;
        $scriptInformation['scriptNumber'] = $scriptNumber;
        $scriptInformation['textData'] = $textData;
        $scriptInformation['versionNumber'] = $versionNumber;
        $scriptInformation['versionString'] = $versionString;
        $scriptInformation['releaseVersion'] = $releaseVersion;

        return $scriptInformation;
    }

    /**
	 * Execute sql statements
	 */
	function executeScriptQuery($pScriptInformation, $pConnection)
	{
		foreach ($pScriptInformation['textData'] as $query)
		{
			//check if this statment should be ignored
			$ignored = ignoreStatement($query);

			if (!empty($query) && !$ignored)
			{
				$r = mysqli_query($pConnection,$query);
				if (!$r)
				{
					echo "\n".$query;
					promptMessage("executeFailed", $pScriptInformation['scriptFile'], mysqli_error($pConnection));
                                        readline("Press Return key to exit");
					exit(1);
				}
				else
				{
					// stop the script if we have any warning & error code = 1048
					$warning = mysqli_get_warnings($pConnection);

                    echo "\n".$query;
					if ($warning instanceof \mysqli_warning)
					{
 						if ($warning->errno == 1048)
 						{
 							promptMessage("executeFailed", $pScriptInformation['scriptFile'], $warning->message);
                                                        readline("Press Return key to exit");
 							exit(1);
 						}
 						else
 						{
							promptMessage("executeOK");
							$scriptName = $pScriptInformation['scriptFile'];
 						}
					} else {
                        promptMessage("executeOK");
                        $scriptName = $pScriptInformation['scriptFile'];
                    }
				}
			}
		}
		return $scriptName;
	}



	/**
	 * Detect the entry point for upgrade
	 * @author Loc Dinh
	 * @return Array
	 */
	function getStartingPoint($pFileName_array,$pConnection)
	{
        global $gConstants;
        global $gSession;
    	global $ac_config;
    	global $systemConfig;

        $result = '';
		$actionCode = '';
		$startPoint = 0;
        $systemConfig = getLastInstallNumber();

        $lastInstallScriptNumber = $systemConfig['lastinstallscriptnumber'];

        // Check lastInstallScriptNumer in SYSTEMCONFIGURE in table first
        // if empty means current version is < 3.0.0a5, then look at ACTIVITYLOG table for actioncode
        if($pConnection && $lastInstallScriptNumber == '')
        {
		   $systemconfig = mysqli_query($pConnection, 'SELECT `actioncode`,`actionnotes` FROM `ACTIVITYLOG` WHERE `id` = (SELECT MAX(id) FROM `ACTIVITYLOG`) AND sectioncode= "UPGRADE" AND `actionnotes` LIKE "%FINISHED%"');

		   while($c = $systemconfig->fetch_object())
		   {

			   $actionCode = $c->actioncode;
			   $actionNotes = $c->actionnotes;
		   }


		   if($actionCode != '')
		   {
				$fileCount = count($pFileName_array);

				for($i=0;$i<$fileCount;$i++)
				{
					$newActionCode = '';
					// detect part number of 3.0.0a2
					if($actionNotes != 'FINISHED')
					{
						$partNumber_array = explode("FINISHED",$actionNotes);
						$partNumber = trim($partNumber_array[0]);
						$newActionCode = strtolower($actionCode." ".$partNumber);
					}
					else
					{
						$newActionCode = $actionCode;
					}

					$pos = strpos($pFileName_array[$i], $newActionCode);
					if($pos!= false)
					{
						$startPoint = $i;
					}
				}
			}
			else
			{
				$count = count($pFileName_array);
				echo "\nUnable to detect database's version, please input the version you want to start with: \n\n";
				for($i=0;$i<$count;$i++)
				{
					$j= $i+1;
					echo $j." - ".$pFileName_array[$i]."\n";
				}
				echo "\n(1 - ".$count."): ";
				$startPoint = trim(fgets(STDIN));
                if (strtoupper($startPoint) == 'A')
                {
                    selectAction();
                }

				while($startPoint > $count)
				{
					echo "You have entered an invalid number, please try again: ";
					$startPoint = trim(fgets(STDIN));
				}
				$startPoint = $startPoint - 2;
			}
		}
		else
		{
			// if lastInstallScriptNumber in SYSTEMCONFIGURE exist means the current
			for($i=0;$i<count($pFileName_array);$i++)
			{

				$number = explode("_", $pFileName_array[$i], 2);
				if($number[0] == $lastInstallScriptNumber)
				{
					$startPoint = $i;
					break;
				}
				else
				{
					$startPoint = false;
				}
			}
		}

		if ($startPoint !== false)
		{
			for($i=0;$i<=$startPoint;$i++)
			{
				unset($pFileName_array[$i]);
			}
			// to reset array keys
			$pFileName_array = array_values($pFileName_array);
		}
		else
		{
			echo "\n  #ERROR: Last installed script number is not found in any script name. Please double check and try again. \n\n";
                        readline("Press Return key to exit");
			exit(1);
		}


      return $pFileName_array;
	}



	/**
	* Get last install script number
	*/
	function getLastInstallNumber()
	{
        global $ac_config;
        $connection = getTaopixConnection();

        $exists = false;
        $tableExists = false;

        $returnArray = array('success' => true, 'error' => '', 'id' => 0, 'lastinstallscriptnumber' => '');

        //Check if table SYSTEMCONFIG exists
        $table = mysqli_query($connection, "SELECT count(*) as Exist FROM information_schema.tables WHERE table_name='SYSTEMCONFIG' AND TABLE_SCHEMA='" . $ac_config['DBNAME'] . "'");
        while ($c = $table->fetch_object())
        {
            if ($c->Exist != 0)
            {
                $tableExists = true;
                break;
            }
        }

        //Check if lastinstallscriptnumber exist
        if ($tableExists)
        {
            $columns = mysqli_query($connection, "SHOW columns FROM `SYSTEMCONFIG`");
            while ($c = $columns->fetch_object())
            {
                if ($c->Field == 'lastinstallscriptnumber')
                {
                    $exists = true;
                    break;
                }
            }
        }
		else
		{
			$returnArray['success'] = false;
			$returnArray['error'] = 'SYSTEMCONFIG Error: unable to find system config information';
		}

        if ($exists)
        {
            fixIssueWithWrongPrefixNumber($connection);
            $sql = "SELECT `lastinstallscriptnumber` FROM `SYSTEMCONFIG` WHERE `id` = (SELECT MAX(id) FROM `SYSTEMCONFIG`)";

            $lastinstallscriptnumber = mysqli_query($connection, $sql);
            while ($c = $lastinstallscriptnumber->fetch_object())
            {
                $returnArray['lastinstallscriptnumber'] = $c->lastinstallscriptnumber;
            }
        }
		else
		{
			$returnArray['success'] = false;
			$returnArray['error'] = 'SYSTEMCONFIG Error: unable to find last install script number';
		}

		return $returnArray;
    }


    /**
     * getListOfSessions
     * - read the sessions table and return a list of session id's
     */
    function getListOfSessions()
    {
        // return an array of session id's from the sessions table
        $resultArray = array();

        $sessionCount = 0;
        $sessionList = array();

        $connection = getTaopixConnection();
        $stmt = mysqli_query($connection, "SELECT `id` FROM `SESSIONDATA`");
        while ($i = $stmt->fetch_object())
        {
            $sessionList[] = $i->id;
            $sessionCount++;
        }

        $resultArray['count'] = $sessionCount;
        $resultArray['sessionsids'] = $sessionList;


        return $resultArray;
    }

    function changeKeyName(&$pArray, $pOldKey, $pNewKey)
    {
        $changed = false;

        if (array_key_exists($pOldKey, $pArray))
        {
            $pArray[$pNewKey] = $pArray[$pOldKey];
            $changed = deleteItem($pArray, $pOldKey);
        }

        return $changed;
    }

    function insertValue(&$pArray, $pKey, $pValue)
    {
        $changed = false;

        if (!array_key_exists($pKey, $pArray))
        {
            $pArray[$pKey] = $pValue;
            $changed = true;
        }

        return $changed;
    }

    function updateValue(&$pArray, $pKey, $pValue, $pCondition)
    {
        $changed = false;
        $makeChange = false;

        if (array_key_exists($pKey, $pArray))
        {
			if ((is_array($pCondition)) && (count($pCondition) > 0))
			{
				switch($pCondition[0])
				{
					case '==':
					{
						$makeChange = ($pArray[$pKey] == $pCondition[1]);
						break;
					}
					case '!=':
					{
						$makeChange = ($pArray[$pKey] != $pCondition[1]);
						break;
					}
					case '>':
					{
						$makeChange = ($pArray[$pKey] > $pCondition[1]);
						break;
					}
					case '>=':
					{
						$makeChange = ($pArray[$pKey] >= $pCondition[1]);
						break;
					}
					case '<':
					{
						$makeChange = ($pArray[$pKey] < $pCondition[1]);
						break;
					}
					case '<=':
					{
						$makeChange = ($pArray[$pKey] <= $pCondition[1]);
						break;
					}
				}

				if ($makeChange)
				{
					$pArray[$pKey] = $pValue;
					$changed = true;
				}
			}
			else
			{
				$pArray[$pKey] = $pValue;
				$changed = true;
			}
        }


        return $changed;
    }

    function insertAndUpdateValue(&$pArray, $pKey, $pValue)
    {
        $pArray[$pKey] = $pValue;

        return true;
    }

    function deleteItem(&$pArray, $pKey)
    {
        unset($pArray[$pKey]);

        return true;
    }

    function makeChanges(&$pItemToChange, $pChanges, $pIndex = -1)
    {
        if (array_key_exists('changekey', $pChanges))
        {
            return changeKeyName($pItemToChange, $pChanges['key'], $pChanges['changekey']);
        }
        else if (array_key_exists('delete', $pChanges))
        {
            return deleteItem($pItemToChange, $pChanges['key']);
        }
        else if (array_key_exists('insert', $pChanges))
        {
            return insertValue($pItemToChange, $pChanges['key'], $pChanges['insert']);
        }
        else if (array_key_exists('update', $pChanges))
        {
            return updateValue($pItemToChange, $pChanges['key'], $pChanges['update'], $pChanges['condition']);
        }
        else if (array_key_exists('insert_update', $pChanges))
        {
            if ((is_int($pChanges['key'])) && ($pIndex != -1))
            {
                if ($pChanges['key'] == $pIndex)
                {
                    return insertAndUpdateValue($pItemToChange, $pChanges['key'], $pChanges['insert_update']);
                }
                else
                {
                    return false;
                }
            }
            else
            {
                return insertAndUpdateValue($pItemToChange, $pChanges['key'], $pChanges['insert_update']);
            }
        }
        else
        {
            return false;
        }
    }

    function isArrayAllKeyInt($pInputArray)
    {
        if(!is_array($pInputArray))
        {
            return false;
        }

        if(count($pInputArray) <= 0)
        {
            return true;
        }

        return array_unique(array_map("is_int", array_keys($pInputArray))) === array(true);
    }

    /**
     * updateSessionData
     * - if the session data needs to be changed (insert, update or delete), read in session data, apply the changes and write the session back to the database
     *
     * $pUpdateInformation - this is an array of configuration of what you want to change.
	 * the first item in the array is an array of the path. left empty this identifies that the item is on the root, else each item in the array will be used as the path to the item.
	 * a change is made up of a key and an action. the key is the identifier of the item in the path. if the path resolved to a integer based array, and you want to target a single item from that array, the key must be an int.
	 * if the path resolved to a integer based array and each item are in turn an associative array the key must be a string
	 * if the path resolves to an associative array then they key must be a string.
     * there are 5 actions:
     * 	update: the value of the identified item will be updated. the value can be optionally updated if it matches the passed criteria.
     * 	insert: if the key is missing then the item will be inserted. if the item is found it will be untouched.
     *  insert_update: if the key is found it will be updated. if the key is missing it will be inserted.
     *  changekey: the key of the identified item will be changed
     *  delete: the identified item will be deleted.

	 * the value which is used to update, insert or change is stored in the action.

	 * here are some examples:

	 * 1)
	 * look for the array userdata and delete all items with the key webbrandcode.
	 * if userdata is an integer based array it will loop around all items in the array a perform the delete
     * $updateSessionData[] = array('path' => array('userdata'),
     *                         'changes' => array(
     *                                             array('key' => 'webbrandcode', 'delete' => true)
     *                                         ));

	 * 2)
	 * look for the array ssoprivatedata in the userdata array
	 * update the value in the item with the key username to to chris2
	 * insert or update the key id2 with the value 4321
	 * insert if missing the key middlename with or to the value bill
	 * find the key countrycode and change it to countryfoobar

     * $updateSessionData[] = array('path' => array('userdata', 'ssoprivatedata'),
     *                         'changes' => array(
     *                                             array('key' => 'username', 'update' => 'chris2'),
     *                                             array('key' => 'id2', 'insert_update' => '4321'),
     *                                             array('key' => 'middlename', 'insert' => 'bill'),
     *                                            array('key' => 'countrycode', 'changekey' => 'countryfoobar')
     *                                         ));

	 * 3)
	 * look for the array testlots in the ssoprivatedata array in the userdata array and find the item with the integer index of 1
	 * update the value in the item with the key test2 to Global Data
	 * insert or update the key test5 with the value New or Updated
	 * insert if missing the key test4 with or to the value This is a new key
	 * find the key test1 and change it to testOne

     * $updateSessionData[] = array(  'path' => array('userdata','ssoprivatedata','testlots', 1),
     *                        'changes' => array(
     *                                             array('key' => 'test1', 'changekey' => 'testOne'),
     *                                             array('key' => 'test4', 'insert' => 'This is a new key'),
     *                                             array('key' => 'test2', 'update' => 'Global Data'),
     *                                             array('key' => 'test5', 'insert_update' => 'New or Updated')));

	 * 4)
	 * look for the array called array1 and change the item with an index of 3 to -3 or insert an item 3 with the value -3

     * $updateSessionData[] = array('path' => array('array1'),
     *                        'changes' => array(
     *                                             array('key' => 3, 'insert_update' => -3)
     *                                         )
     *                         );

	 * 5)
	 * look for the array called order and conditionally update the value of jobtickettemplate to jobticket_large if the current value of jobtickettemplate is equal to "jobticket"
	 * possible test operators are: ==, !=, >, >=, < and <=

	 * $updateSessionData[] = array('path' => array('order'),
     *                        'changes' => array(
	 *												array('key' => 'jobtickettemplate', 'update' => 'jobticket_large', 'condition' => array('==', 'jobticket'))));

	**/

    function updateSessionData($pUpdateInformation)
    {
        if (count($pUpdateInformation) > 0)
        {
            // get a list of Session ID's
            $sessionIDList = getListOfSessions();

            // if any sessions exist, update as needed
            if ($sessionIDList['count'] > 0)
            {
                // loop around each session and update
                foreach ($sessionIDList['sessionsids'] as $idVal)
                {
 					// Apply the change into the session data array.
					list ($sessionDirty, $sessionData) = applyChangeToItem($pUpdateInformation, DatabaseObj::getSessionData($idVal));

                    // update the session in the database
                    if ($sessionDirty)
                    {
                        DatabaseObj::updateSession2($sessionData);
                    }
                }
            }
        }
    }


	/**
	* Add 'lastinstallscriptnumber' if not exist
	* @author Loc Dinh
	*/
	function addColumnIfNotExist($pConnection)
	{
		// Check if table SYSTEMCONFIG exists
		$fieldExists = false;
		$table = mysqli_query($pConnection, "SELECT count(*) as Exist FROM information_schema.tables WHERE table_name='SYSTEMCONFIG' ");
		while($c = $table->fetch_object())
		{
			if($c->Exist != 0)
			{
				// Check if field 'lastinstallscriptnumber' exists
				$columns = mysqli_query($pConnection,"SHOW columns FROM `SYSTEMCONFIG`");
				while($c = $columns->fetch_object())
				{

					if($c->Field == 'lastinstallscriptnumber')
					{
						$fieldExists = true;
						break;
					}
				}
			}
		}

		if(!$fieldExists)
		{
			mysqli_query($pConnection,"ALTER TABLE `SYSTEMCONFIG` ADD `lastinstallscriptnumber` VARCHAR(4) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL");
		}
    }


    /**
	* Check if database name already exist
	* @author Loc Dinh
	* @return String
	*/
    function checkDbExistence($pRootConnection)
    {

    	$dbName = trim(fgets(STDIN));
		if (strtoupper($dbName) == 'A')
        {
            selectAction();
        }

		$dbName_query = mysqli_query($pRootConnection,"SELECT SCHEMA_NAME AS databbasename FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '".$dbName."'" );
    	if($dbName_query)
    	{
    		$c = $dbName_query->fetch_object();

    		if ($c)
    		{
				if($c->databbasename != '')
				{
					promptMessage('dbAlreadyExist');
					$dbName = checkDbExistence($pRootConnection);
				}
			}
		}
		return $dbName;
	}


   /**
	* Check if database User already exist
	* @author Loc Dinh
	* @return String
	*/
    function checkUserExist($pRootConnection, $pExistingUser = '')
    {
		// set up the result array
		$resultArray = array('existinguser' => 0, 'username' => '');

		$inputValue = '';

		if ($pExistingUser == '')
		{
			// first time in, ask user
			while (trim($inputValue) == '')
			{
				promptMessage('databaseUser');
				$inputValue = trim(fgets(STDIN));
			}
		}
		else
        {
			// if the user name to check has been passed in, the user has been found in the database,
			// ask if it to be used, repeat until 'Y' or 'N' answer
			while ((strtoupper($inputValue) != 'Y') && (strtoupper($inputValue) != 'N'))
			{
				promptMessage('dbUserExist');
				$inputValue = trim(fgets(STDIN));
			}
		}

		switch (strtoupper($inputValue))
		{
			case 'A':
			{
				// if an 'a' (abort) has been entered, return to the main menu
				 selectAction();

				 break;
			}

			case 'Y':
			{
				// use the existing database user
				$inputValue = $pExistingUser;
				$resultArray['existinguser'] = 1;

				break;
			}

			case 'N':
			{
				// prompt for new user, then do the check, do not pass the existing user name
				$existingCheckArray = checkUserExist($pRootConnection, '');
				$resultArray['existinguser'] = $existingCheckArray['existinguser'];
				$inputValue = $existingCheckArray['username'];

				break;
			}

			default:
			{
				// check if the entered username exists
				$dbUser_query = mysqli_query($pRootConnection, "SELECT user as dbuser FROM mysql.user WHERE user = '" . $inputValue . "'");
				if($dbUser_query)
				{
					$c = $dbUser_query->fetch_object();

					if ($c)
					{
						if($c->dbuser != '')
						{
							$existingCheckArray = checkUserExist($pRootConnection, $inputValue);
							$resultArray['existinguser'] = $existingCheckArray['existinguser'];
							$inputValue = $existingCheckArray['username'];
						}
					}
				}
				break;
			}
		}

		// populate the return array
		$resultArray['username'] = $inputValue;

		return $resultArray;
    }



    /**
	* Replace existing medialAlbumweb.conf by a new one with new database details
	* @author Loc Dinh
	*/
    function replaceConfigFile($pDbConfig)
    {

	   	$textData = UtilsObj::readTextFile("../config/mediaalbumweb.conf");
		if(!$textData)
		{
			promptMessage('configFileDoesNotExist');
                        readline("Press Return key to exit");
			exit(1);
		}
		$textData 	 = str_replace("\r\n", "\n", $textData);
		$textData 	 = str_replace("\r", "\n", $textData);
		$content_row = explode("\n",$textData);

		for($i=0;$i<count($content_row);$i++)
		{
			$pos = strpos($content_row[$i],"DBHOST=");
			if($pos!== false)
			{
				$content_row[$i] = "DBHOST=".$pDbConfig['serverIp'];
			}

			$pos = strpos($content_row[$i],"DBUSER=");
			if($pos!== false)
			{
				$content_row[$i] = "DBUSER=".$pDbConfig['dbUserName'];
			}

			$pos = strpos($content_row[$i],"DBPASS=");
			if($pos!== false)
			{
				$content_row[$i] = "DBPASS=".$pDbConfig['dbPassword'];
			}

			$pos = strpos($content_row[$i],"DBNAME=");
			if($pos!== false)
			{
				$content_row[$i] = "DBNAME=".$pDbConfig['dbName'];
			}
		}

		// Backup configuration file and mirror the original files permissions.
		// User and group ownership are ignored (as we might not have permission to change this)
		// The original file is either writeable or its not, don't modify its user or group ownership
		// in order to preserve the original settings set by the administrator/user manual.
		$configurationFileName = __DIR__.'/../config/mediaalbumweb.conf';
		$backupConfigurationFileName = sprintf(__DIR__.'/../config/mediaalbumweb.%s.conf',  date('d.m.y'));
		copy($configurationFileName, $backupConfigurationFileName) or die('Unable to backup current file');
		chmod($backupConfigurationFileName, fileperms($configurationFileName));

		// Write configuration to disk
		$textData = implode("\r\n", $content_row);
    	file_put_contents($configurationFileName, $textData);
    }


    function clearScreen()
    {
        $osType = TPX_OS_TYPE_UNIX;

        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN')
        {
            $osType = TPX_OS_TYPE_WINDOWS;
        }
        else if (strtoupper(substr(PHP_OS, 0, 3)) === 'DAR')
        {
            $osType = TPX_OS_TYPE_MAC;
        }

		if ($osType == TPX_OS_TYPE_WINDOWS)
		{
			system('cls');
		}
		else
		{
			system('clear');
		}
    }


	// function to dectect statements which shouldn't be run
	function ignoreStatement($pQuery)
	{

		$ignoreString = 'ADD COLUMN `lastinstallscriptnumber`';

		if(strstr($pQuery,$ignoreString))
		{
			return true;
		}
		else
		{
			return false;
		}
	}


	function runStoredProcedure($pConnection,$pProcedureName)
	{
		global $scriptPath;

		$textData = UtilsObj::readTextFile($scriptPath."storedProcedures/".$pProcedureName.".sql");
		$textData = str_replace("\r\n", "\n", $textData);
		$textData = str_replace("\r", "\n", $textData);

		mysqli_query($pConnection,"DROP PROCEDURE IF EXISTS ".$pProcedureName.";");

		$r = mysqli_query($pConnection,$textData);
		if (!$r)
		{
			echo "\n******************** STORED PROCEDURE ********************\n".$textData;
			promptMessage("executeFailed",$pProcedureName,mysqli_error($pConnection));
                        readline("Press Return key to exit");
			exit(1);
		}
		else
		{
			echo "\n".$textData;
			promptMessage("executeOK");

			//Calling procedure that require params will fail so wrap in try block.
			try {
				// Call procedure after it is created
				mysqli_query($pConnection,"CALL ".$pProcedureName."();");
			}
			catch (\Throwable $e) {
				echo "\n Couldn't run the prodedure. continuing. ";
			}
		}
	}


	// scan through all existing passwords & promp user if they are in the black list
	function checkAdminPassword($pConnection)
	{
		global $scriptPath;
		$vulnerableAdminList = '';

		$fileName = 'passwordBlackList.txt';
		$handle = fopen($fileName, "r");
		$passwordBlackListStr = fread($handle, filesize($fileName));
		fclose($handle);

		$passwordBlackListArr = explode(",", $passwordBlackListStr);

		$vulnerableCounter = 1;
		$existingAdminDetails = mysqli_query($pConnection,"SELECT `login`, `password` FROM `USERS` WHERE `customer` = 0");
		while($c = $existingAdminDetails->fetch_object())
	   	{
 		    $adminLogin = $c->login;
 		    $adminPassword = $c->password;

	   		for ($i = 0; $i < count($passwordBlackListArr); $i++)
			{
				if ($adminPassword == $passwordBlackListArr[$i])
				{
					$vulnerableAdminList .= " " . $vulnerableCounter . " - " . $adminLogin . "\n";
					$vulnerableCounter++;
				}
			}
	   	}

		if ($vulnerableAdminList != '')
		{
			echo "\n\n #WARNING: The following non-customer accounts may be using vulnerable passwords and it is strongly recommended that those passwords are changed:";
			echo "\n\n".$vulnerableAdminList." \n";
		}
	}



	function runTriggers($pConnection, $pTriggerFileName, $pUseSchema = null)
	{
		global $scriptPath;

		if (!empty($pUseSchema))
		{
			mysqli_query($pConnection, "USE " . $pUseSchema . ";");
		}

		$textData = UtilsObj::readTextFile($scriptPath . "triggers/" . $pTriggerFileName . ".sql");
		$textData = str_replace("\r\n", "\n", $textData);
		$textData = str_replace("\r", "\n", $textData);

		$triggers = explode('$$', $textData);

		foreach ($triggers as $trigger)
		{
			// check trigger != '' because at the end of the script there is a $$ which will cause a warning because the last string is empty
			if (trim($trigger) != '')
			{
				$r = mysqli_query($pConnection,trim($trigger));
				if (!$r)
				{
					echo "\n******************** TRIGGER ********************\n".$textData;
					promptMessage("executeFailed",$pTriggerFileName,mysqli_error($pConnection));
                                        readline("Press Return key to exit");
					exit(1);
				}
				else
				{
					echo "\n".$trigger;
					promptMessage("executeOK");
				}
			}

		}

	}


	function checkSystemEnvironment()
	{
		checkPHPVersion();

		$requiredExtension = Array('curl','mbstring','openssl','mysqli','zip');

		$loadedExtension = get_loaded_extensions();

		// return the common elements between 2 arrays.
		$intersectArray = array_intersect($requiredExtension, $loadedExtension);

		// if the array above is different from the requiredExtension that means one or more required extensions haven't been installed, stop the script.
		$differences = array_diff($requiredExtension, $intersectArray);

		if (count($differences) > 0)
		{
			$unloadedList = '';
			foreach($differences as $value)
			{
				$unloadedList = $value.", ";
			}
			$unloadedList = substr($unloadedList, 0, -2);
			echo "\n #ERROR: Following extensions have not been loaded: ".$unloadedList." \n\n";
                        readline("Press Return key to exit");
			exit(1);
		}
	}


	/**
	 * Check that the MySQL configuration does not have strict mode configured
	 *
	 * For transactional tables, an error occurs for invalid or missing values in a data-change statement when either STRICT_ALL_TABLES or
	 * STRICT_TRANS_TABLES is enabled. The statement is aborted and rolled back.
	 * For nontransactional tables, the behavior is the same for either mode if the bad value occurs in the first row to be inserted or
	 * updated: The statement is aborted and the table remains unchanged. If the statement inserts or modifies multiple rows and the bad
	 * value occurs in the second or later row, the result depends on which strict mode is enabled:
	 *
	 *  STRICT_ALL_TABLES: MySQL returns an error and ignores the rest of the rows. However, because the earlier rows have been inserted or
	 *						updated, the result is a partial update. To avoid this, use single-row statements, which can be aborted without
	 *						changing the table.
	 *
	 *  STRICT_TRANS_TABLES: MySQL converts an invalid value to the closest valid value for the column and inserts the adjusted value.
	 *						If a value is missing, MySQL inserts the implicit default value for the column data type. In either case,
	 *						MySQL generates a warning rather than an error and continues processing the statement.
	 */
	function checkMySQLStrictMode($pConnection)
	{
		$strictmode_query = mysqli_query($pConnection, "SELECT @@GLOBAL.sql_mode AS strictmode");

		if ($strictmode_query)
    	{
			$restrictedFlagsArray = array('STRICT_TRANS_TABLES', 'STRICT_ALL_TABLES');

    		$c = $strictmode_query->fetch_object();
			$strictMode = $c->strictmode;
            $strictArray = explode(',', $strictMode);

			$intersectArray = array_intersect($restrictedFlagsArray, $strictArray);

			if (count($intersectArray) > 0)
			{
				echo "\n #ERROR: MySQL strict-mode is on, please turn it off and run this script again. \n\n";
                                readline("Press Return key to exit");
				exit(1);
			}
		}
	}

	/**
	 *
	 * Check the installed version of PHP is greater or equal to PHP 7.2
	 *
	 */
	function checkPHPVersion()
	{
		if (! version_compare(PHP_VERSION, '7.2', '>='))
		{
			echo "\n #ERROR: This version of PHP  is not supported " . PHP_VERSION . "\n\n";
                        readline("Press Return key to exit");
			exit(1);
		}
	}


	/*
	*	Check duplicated pricelist code, if found any in PRICES table then
	*   append them with their record id in order to make them unique
	*/
	function fixDuplicatedPriceListCode($pConnection)
	{
		global $ac_config;
		$resultArray = Array();
		$pricesTableExist = 0;

		$result = Array();
		$temptResult = Array();

		// Check if table PRICES exists and pricelistcode collumn exists.
		$checkPriceTable = mysqli_query($pConnection,"SELECT * FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = '".$ac_config['DBNAME']."' AND TABLE_NAME = 'PRICES' AND COLUMN_NAME = 'pricelistcode'");
		if($checkPriceTable)
		{
			$checkPriceTable->fetch_assoc();
			if ($checkPriceTable->num_rows > 0)
			{
				// Get all pricelist records
				$query = mysqli_query($pConnection,"SELECT `id`,`pricelistcode` FROM `PRICES` WHERE ispricelist = 1");
				if($query)
				{
					while($rows = $query->fetch_object())
					{
						$temptResult['id'] = $rows->id;
						$temptResult['pricelistcode'] = $rows->pricelistcode;

						$result[] = $temptResult;
					}

					$count = count($result);
					// Searching for records which have the same pricelistcode.
					for($i = 0; $i < $count; $i++)
					{
						for($j = $i+1; $j < $count; $j++)
						{
							if($result[$i]['pricelistcode'] == $result[$j]['pricelistcode'])
							{
								mysqli_query($pConnection,"UPDATE `PRICES` SET `pricelistcode` = CONCAT(`pricelistcode`,`id`) WHERE `id` = '".$result[$j]['id']."'");
								break;
							}
						}
					}
				}
			}
		}
	}

	function fixIssueWithWrongPrefixNumber($pConnection)
	{
		mysqli_query($pConnection,"UPDATE SYSTEMCONFIG SET lastinstallscriptnumber = '0091' WHERE webversionnumber = '3.2.1' AND lastinstallscriptnumber = '0100'");
	}


    /**
	* Display common messages being used
	* @author Loc Dinh
	*/
    function promptMessage($pMessageType,$pSecondParam = '',$pThirdParam = '')
	{
		switch ($pMessageType)
		{
			case "initialSelect":
				clearScreen();
				echo "\nTAOPIX CONTROL CENTRE INSTALLATION\n
                \nPlease select: \n\n 1 - New Installation. \n\n 2 - Upgrade. \n\n 3 - Exit. \n\n Input 'a' to abort the process and return to this stage at anytime.\n\n (1/2/3): ";
			break;
			case "beforeConnectAsRoot":
				echo "\n===================== NEW INSTALLATION =====================\n
					  \n(Please enter 'q' at any point if you want to quit)\n ";
			break;
			case "serverIp":
				echo "\n============================================================\n
					  \nInstallation requires administrator create the database and user account
					  \n IP Address: ";
			break;
			case "rootUser":
				echo "\n MySQL Administrator User: ";
			break;
			case "rootPassword":
				echo "\n MySQL Administrator Password: ";
			break;
			case "unableToConnectAsRoot":
				echo "\n #ERROR: Unable to connect using the credential provided, please try again. \n ";
			break;
			case "beforeInputDbDetails":
				echo "\n============================================================\n
					  \nPlease enter database details you wish to create.\n ";
			break;
			case "confirmBackup":
				echo "\n===================== UPGRADE =====================\n
				      \nDo you have a working backup of the current database? (Y/N) ";
			break;
			case "forceBackup":
				echo "\n Please backup your database and run this upgrade again. \n\n";
			break;
			case "databaseName":
				echo "\n Database name: ";
			break;
			case "databaseUser":
				echo "\n Database username: ";
			break;
			case "databasePassword":
				echo "\n Database password: ";
			break;
			case "databaseCreated":
				echo "\n DATABASE CREATED SUCCESSFULLY\n";
			break;
			case "databaseError":
				echo "#ERROR: ".$pSecondParam;
			break;
			case "startScript":
				echo "\n\n**************************************************************************";
				echo "\n*		START SCRIPT ".$pSecondParam."										";
				echo "\n**************************************************************************  \n";
			break;
			case "endScript":
				echo "\n\n**************************************************************************";
				echo "\n*		END SCRIPT ".$pSecondParam."										";
				echo "\n**************************************************************************  \n";
			break;
			case "executedScriptList":
				echo "\n FOLLOWING SCRIPTS HAVE BEEN EXECUTED SUCCESSFULLY \n".$pSecondParam." \n\n";
			break;
			case "promptRunWebConfigurator":
				echo "\n PLEASE NOW RUN THE TAOPIX CONFIGURATOR TO COMPLETE THE INSTALL. \n\n";
			break;
			case "dbUpToDate":
				echo "\n YOUR DATABASE IS UP TO DATE, NO UPGRADE REQUIRED. \n\n";
			break;
			case "executeFailed":
				echo "\n\n\n #ERROR: FAILED. AN ERROR OCCURRED IN ".$pSecondParam." : ".$pThirdParam."  \n\n";
			break;
			case "executeOK":
				echo "\n -------------------- OK --------------------";
			break;
			case "dbAlreadyExist":
				echo "\nDatabase name already exist, please use a different one: ";
			break;
			case "dbUserExist":
				echo "\nThis username already exist, do you want to use it? (Y/N): ";
			break;
			case "invalidDbPassword":
				echo "\n #ERROR: Password is invalid, please try again. \n";
			break;
			case "configFileDoesNotExist":
				echo "\n #ERROR: Configuration file does not exist. \n\n";
			break;
		}
	}

	abstract class ExternalScript
	{
		private $returnArray = array(	'result' => '',
										'sessionchanges' => array(),
										'externalcartchanges' => array(),
										'triggers' => array(),
										'procedures' => array(),
                                        'onlinebasketchanges' => array());

		protected $dbNames = array();
		protected $dbConnection;
		protected $mode = '';
		protected $config = array();

    	function __construct($pDBNames, $pDBConnection, $pMode, $pConfig)
    	{
        	$this->dbNames = $pDBNames;
        	$this->dbConnection = $pDBConnection;
        	$this->mode = $pMode;
        	$this->config = $pConfig;
    	}

    	public function setResult($pResultText)
    	{
    		$this->returnArray['result'] = $pResultText;
    	}

    	public function addSessionChange($pSessionChangeArray)
    	{
    		$this->returnArray['sessionchanges'][] = $pSessionChangeArray;
    	}

		public function addExternalCartChange($pExternalCartChangeArray)
    	{
    		$this->returnArray['externalcartchanges'][] = $pExternalCartChangeArray;
        }

        public function addOnlineBasketChange($pOnlineBasketChangerray)
    	{
    		$this->returnArray['onlinebasketchanges'][] = $pOnlineBasketChangerray;
    	}

    	public function addTrigger($pTriggerFileName, $pSchema)
    	{
    		$this->returnArray['triggers'][] = array('filename' => $pTriggerFileName, 'schema' => $pSchema);
    	}

    	public function addStoredProcedure($pProcedureFileName, $pSchema)
    	{
    		$this->returnArray['procedures'][] = array('filename' => $pProcedureFileName, 'schema' => $pSchema);
    	}

		public abstract function run();

		public function getReturnData()
		{
			return $this->returnArray;
		}

	}

	class ExternalScriptRunner
	{
		const PRE_SCRIPT = 'PRE';
		const POST_SCRIPT = 'POST';

		private $installScripts = array(self::PRE_SCRIPT => array(), self::POST_SCRIPT => array());

		private $dbNames = array();
		private $dbConnection;
		private $mode = '';
		private $config = array();

    	function __construct($pDBNames, $pDBConnection, $pMode, $pConfig)
    	{
        	$this->dbNames = $pDBNames;
        	$this->dbConnection = $pDBConnection;
        	$this->mode = $pMode;
        	$this->config = $pConfig;
    	}

		public function registerPreScript($pClassName, $pReleaseVersion, $pMilestoneVersion)
		{
			return $this->registerScript($pClassName, $pReleaseVersion, $pMilestoneVersion, self::PRE_SCRIPT);
		}

		public function registerPostScript($pClassName, $pReleaseVersion, $pMilestoneVersion)
		{
			return $this->registerScript($pClassName, $pReleaseVersion, $pMilestoneVersion, self::POST_SCRIPT);
		}

		private function registerScript($pClassName, $pReleaseVersion, $pMilestoneVersion, $pType)
		{
			$this->installScripts[$pType][] = array(	'releaseversion' => $pReleaseVersion,
														'milestoneversion' => $pMilestoneVersion,
														'classname' => $pClassName);
		}

		function runPreScripts($pReleaseVersion, $pMilestoneVersion)
		{
			return $this->runScripts($pReleaseVersion, $pMilestoneVersion, self::PRE_SCRIPT);
		}

		function runPostScripts($pReleaseVersion, $pMilestoneVersion)
		{
			return $this->runScripts($pReleaseVersion, $pMilestoneVersion, self::POST_SCRIPT);
		}

		function runScripts($pReleaseVersion, $pMilestoneVersion, $pType)
		{
			$scriptsOutput = array();

			if (count($this->installScripts[$pType]) > 0)
			{
				$first = true;
				$scriptRan = false;

				foreach ($this->installScripts[$pType] as $versionArray)
				{
					// if the release version and the milestone versions match then we only need to check that the version the script is for is the release version
					// or
					// if the release and milestone versions don't match we need to check both versions for the script
					if ((($versionArray['releaseversion'] == $pReleaseVersion) && ($pReleaseVersion == $pMilestoneVersion)) ||
							(($versionArray['releaseversion'] == $pReleaseVersion) && ($versionArray['milestoneversion'] == $pMilestoneVersion) && ($pReleaseVersion != $pMilestoneVersion)))
					{
						if ($first)
						{
							echo "\n\n**************************************************************************";
							echo "\n*		START EXTERNAL " . $pType . "-SCRIPTS 							";
							echo "\n**************************************************************************  \n";

							$first = false;
						}

						$scriptsOutput[] = $this->runScript($versionArray['classname']);
					}
				}

				if (!$first)
				{
					echo "\n\n**************************************************************************";
					echo "\n*		END EXTERNAL " . $pType . "-SCRIPTS 								";
					echo "\n**************************************************************************  \n";
				}
			}

			return $scriptsOutput;
		}

		private function runScript($pClassName)
		{
			$returnData = array('result' => 'Not an instance of ExternalScript');

			include_once(dirname(__FILE__) . DIRECTORY_SEPARATOR  . $pClassName . ".php");

			echo "\n---------------------------------------------------------------------------";
			echo "\nClass Name: " . $pClassName;
			echo "\nScript Name: " . $pClassName . ".php";
			echo "\n---------------------------------------------------------------------------\n";

			$scriptClass = new $pClassName($this->dbNames, $this->dbConnection, $this->mode, $this->config);

			// make sure the class is an instance of ExternalScript
			if ($scriptClass instanceof ExternalScript)
			{
				$scriptClass->run();

				$returnData = $scriptClass->getReturnData();
			}

			return $returnData;

		}
	}

	/**
	 * Apply a list of changes to an item.
	 *
	 * @param array $pUpdateInformation List of changes to be done.
	 * @param array $pItem Item to be changed.
	 * @return array DirtyStatus and Item updated.
	 */
	function applyChangeToItem($pUpdateInformation, $pItem)
	{
		$itemDirty = false;
		$item = $pItem;

		foreach ($pUpdateInformation as $updateInfo)
		{
			// make sure there are some changes for the path
			$changesCount = count($updateInfo['changes']);

			if ($changesCount > 0)
			{
				// set the current node to the root
				$node = &$item;

				$pathCount = 0;

				// get the path if there is a path set, else we assume the changes are for the root
				if (!empty($updateInfo['path']))
				{
					$pathCount = count($updateInfo['path']);
				}

				// if changes are not for the root, find the node which they are for from the path
				if ($pathCount > 0)
				{
					for ($i = 0; $i < $pathCount; $i++)
					{
						$pathItem = $updateInfo['path'][$i];

						// if the path doesn't exist in the the session then stop and set the node to null;
						if (array_key_exists($pathItem, $node))
						{
							// set the node to a reference to the current path
							$node = &$node[$pathItem];
						}
						else
						{
							$node = null;
							break;
						}
					}
				}

				// if the node was found then apply the changes
				if ($node != null)
				{
					// determine if the node is an array or not
					if (isArrayAllKeyInt($node))
					{
						// loop around each item in the node and apply the changes
						$nodeCount = count($node);

						for ($l = 0; $l < $nodeCount; $l++)
						{
							$nodeItem = &$node[$l];

							for ($i = 0; $i < $changesCount; $i++)
							{
								$itemDirty = $itemDirty || makeChanges($nodeItem, $updateInfo['changes'][$i]);
							}
						}
					}
					else
					{
						// apply the changes to the node which isn't an array of items
						for ($i = 0; $i < $changesCount; $i++)
						{
							$itemDirty = $itemDirty || makeChanges($node, $updateInfo['changes'][$i]);
						}
					}
				}
			}
		}

		return [$itemDirty, $item];
	}

	/**
	* - if project data needs to be changed (insert, update or delete), read in external data, apply the changes and write the new data back to the database
	*
	* $pUpdateInformation - this is an array of configuration of what you want to change.
	* The first item in the array is an array of the path. left empty this identifies that the item is on the root, else each item in the array will be used as the path to the item.
	* A change is made up of a key and an action. the key is the identifier of the item in the path.
	* There are 5 actions:
	* 	update: the value of the identified item will be updated. the value can be optionally updated if it matches the passed criteria.
	* 	insert: if the key is missing then the item will be inserted. if the item is found it will be untouched.
	*  insert_update: if the key is found it will be updated. if the key is missing it will be inserted.
	*  changekey: the key of the identified item will be changed
	*  delete: the identified item will be deleted.
	*
	* the value which is used to update, insert or change is stored in the action.
	*
	* Example:
	*
	* update the value in the items with the key username to to chris2
	* insert or update the key id2 with the value 4321
	* insert if missing the key middlename with the value bill
	* find the key countrycode and change it to countryfoobar
	* remove the key postcode from the existing data.
	*
	* $updateProjectData[] = array(
	*		'path' => array('items'),
	*      'changes' => array(
	*			array('key' => 'username', 'update' => 'chris2'),
	*			array('key' => 'id2', 'insert_update' => '4321'),
	*			array('key' => 'middlename', 'insert' => 'bill'),
	*			array('key' => 'countrycode', 'changekey' => 'countryfoobar'),
	*			array('key' => 'postcode' => 'delete' => true)
	*		)
	* );
	**/
	function updateProjectData($pConnection, $pUpdateInformation, $pTableName)
	{
		// get a list of project data ID's
		$projectDataList = getListOfProjectData($pTableName);
		$updateList = [];

		// loop around each record and update them.
		foreach ($projectDataList as $projectData)
		{
			// Aplply the change into the project data array.
			list ($dirtyRecord, $updatedProjectData) = applyChangeToItem($pUpdateInformation, $projectData['projectdata']);

			// Store the changed value.
			if ($dirtyRecord)
			{
				$updateList[$projectData['id']] = $updatedProjectData;
			}
		}

		return saveProjectData($pConnection, $updateList, $pTableName);
	}

	/**
	 * Read the project data from a table and return a list of id's and data
     *
     * @param $pTableName Table name.
	 */
	function getListOfProjectData($pTableName)
	{
		// return an array of session id's from the sessions table
		$recordList = [];

		$connection = getTaopixConnection();
		$stmt = mysqli_query($connection, "SELECT `id`, `projectdata`, `projectdatalength` FROM `" . $pTableName . "`");

		if ($stmt)
		{
			while ($item = $stmt->fetch_object())
			{
				$projectData = $item->projectdata;

				// we have the projectdata data now unserialize it back into an array
				if ($item->projectdatalength > 0)
				{
					$projectData = gzuncompress($projectData, $item->projectdatalength);
				}

				$projectItem = unserialize($projectData);

				$recordList[] = ['id' => $item->id, 'projectdata' => $projectItem];
			}
		}

		return $recordList;
	}

	/**
	 * Update the projectorderdatacache table from an array passed by parameter.
	 * Serialize the data array and compress it if required.
	 *
	 * @param connection $pConnection Connection object.
	 * @param array $pUpdateList List of records to be updated.
     * @param string $pTableName Table name to store the data.
	 * @return string Execution result.
	 */
	function saveProjectData($pConnection, $pUpdateList, $pTableName)
	{
		$result = '';
		$stmt = $pConnection->prepare('UPDATE `' . $pTableName . '`
										SET `projectdata` = ? ,
											`projectdatalength` = ?
										WHERE `id` = ?');

		if ($stmt)
		{
			// update the database with the changed values
			foreach($pUpdateList as $id => $value)
			{
				// convert the cart array into a serialized string
				$serializedProjectData = serialize($value);
				$serializedProjectDataLength = strlen($serializedProjectData);

				// Check the size of the serialized string to make sure the length of the string is not greater than what a mediumblob column can hold.
				// If it is greater, then we must compress the string and record the size of the orignal string before compression.
				// If it is not greater, then we can store the datalegth as 0.
				// When reading the data back the 0 indicates that the data has not been compressed which in turn means we wont attempt to uncompress it.
				if ($serializedProjectDataLength > 15728640)
				{
					$serializedProjectData = gzcompress($serializedProjectData, 9);
				}
				else
				{
					$serializedProjectDataLength = 0;
				}

				if ($stmt->bind_param('sii', $serializedProjectData, $serializedProjectDataLength, $id))
				{
					if (! $stmt->execute())
					{
						$result = __FUNCTION__ . ' execute: ' . $pConnection->error;
					}
				}
				else
				{
					$result = __FUNCTION__ . ' bind_param: ' . $pConnection->error;
				}
			}

			$stmt->free_result();
            $stmt->close();
			$stmt = null;
		}
		else
		{
			$result = __FUNCTION__ . ' prepare: ' . $pConnection->error;
		}

		return $result;
    }
?>
