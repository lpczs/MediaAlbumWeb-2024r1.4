<?php
	// Change working directory
	define('__ROOT__', dirname(dirname(__FILE__)));

	// Include vendor autoload.
	require_once(__ROOT__ . '/libs/external/vendor/autoload.php');

	$gParametersArray = new Taopix\Core\Arguments([
		new Taopix\Core\CLI\ArgumentOption('h', 'help', 'Display this help screen'),
		new Taopix\Core\CLI\ArgumentOption('s:', 'script:', 'The script to run'),
        new Taopix\Core\CLI\ArgumentOption('d:', 'database:', 'The database to run the script against'),
        new Taopix\Core\CLI\ArgumentOption('p:', 'password:', 'The root user password'),
        new Taopix\Core\CLI\ArgumentOption('h:', 'host:', 'The database host'),
		new Taopix\Core\CLI\ArgumentOption('q', 'quiet', 'Run the install with no output')
	]);
	
    $outputter = new \Taopix\Core\Outputter\OutputterTTY();

	if ($gParametersArray['help'])
	{
		// blank the screen
		$outputter->output($gParametersArray->usage());
		exit;
	}

    $dbConnection = new mysqli(getCLIParam('host'), 'root', getCLIParam('password'), getCLIParam('database'));

    if (($dbConnection) && (! mysqli_connect_errno()))
    {
        $dbConnection->autocommit(true);
    }
    else
    {
        $outputter->output("#ERROR");
        $outputter->output(mysqli_connect_error());
        exit;
    }

    $outputter->output(getCLIParam('script'));

    $importData = file_get_contents(getCLIParam('script'));

    $importDataArray = splitFileContent($importData);

	$truncateSQL = "SELECT Concat('TRUNCATE TABLE ',table_schema,'.',TABLE_NAME, ';') as 'truncate_command' FROM INFORMATION_SCHEMA.TABLES where table_schema in ('" . getCLIParam('database') . "')";

    if ($result = $dbConnection->query($truncateSQL)) {
        while($obj = $result->fetch_object()){
            array_unshift($importDataArray, $obj->truncate_command);
        }
    }
    $result->close(); 


    foreach ($importDataArray as $query)
    {
		$r = mysqli_query($dbConnection, $query);

		if (!$r)
		{
			$outputter->output("#ERROR");
			$outputter->output($query);
			$outputter->output(mysqli_error($dbConnection));
			exit;
		}
		else
		{
			// stop the script if we have any warning & error code = 1048
			$warning = mysqli_get_warnings($dbConnection);

			if ($warning)
			{
				if ($warning->message != '')
				{

					$outputter->output("#WARNING");
					$outputter->output($warning->message);
				}
			}
		}
    }

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
					$sql[] = trim($line);
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

	function getCLIParam($pParamName, $pDefault = "")
	{
		global $gParametersArray;

		return isset($gParametersArray[$pParamName])?$gParametersArray[$pParamName]:$pDefault;
	}