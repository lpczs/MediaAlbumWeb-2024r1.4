<?php

namespace Taopix\Core\CLI;

use Taopix\Core\Arguments;
use Taopix\Core\Config\Config;
use Taopix\Core\Debugable;

use \UtilsObj as Utils;
use \DatabaseObj as Database;

abstract class CLI extends Debugable
{
	protected $arguments;
	protected $scriptName;
	protected $config;
	protected $logFileName;
	private $database;

	function __construct(Config $pConfig, $pScriptName, $pTimeLinit, Arguments $pArguments)
	{
		set_time_limit($pTimeLinit);

		$this->config = $pConfig;

		$this->scriptName = $pScriptName;
		
		$this->arguments = $pArguments;

		$this->logFileName = $this->getLogFileName();

		$this->init();

		global $gConfig;

		$gConfig = $this->config->getGlobalConfig();

        if (array_key_exists("DATADIR", $gConfig))
        {
            Utils::$dataDirectory = $gConfig['DATADIR'];
        }

        if (array_key_exists("LOGDIR", $gConfig))
        {
            Utils::$logsDirectory = $gConfig['LOGDIR'];
        }

	}

	private function getLogFileName()
	{
		$dotPos = 0;
		$logFile = "";

        if (($dotPos = strrpos($this->scriptName, '.')) !== false)
        {
            $logFile = substr($this->scriptName, 0, $dotPos);
        }

        return $logFile;
	}

	public function getLogFileFullPath()
	{
		return Utils::getLogsPath() . $this->logFileName . '.' . date('Y-m-d') . '.log';
	}

	private function init()
	{
        error_reporting(E_ALL);

        ini_set('log_errors', true);

        ini_set("error_log", Utils::getLogsPath() . $this->logFileName . '.errors.' . date('Y-m-d') . '.log');

        if (!$this->config->ALLOWSELFSIGNEDSSLCERTIFICATES)
        {
            $this->config->ALLOWSELFSIGNEDSSLCERTIFICATES = 0;
        }

		if (!$this->config->CCNOTIFICATIONSLIMIT)
        {
            $this->config->CCNOTIFICATIONSLIMIT = 50;
        }

        // if the ALLOWSELFSIGNEDSSLCERTIFICATES is set to 0 then we must set CURLOPT_SSL_VERIFYPEER to true.
        $this->config->SSLVERIFYPEER = ($this->config->ALLOWSELFSIGNEDSSLCERTIFICATES == 0);

        $this->config->logname = $this->logFileName;
    }

	function getConfigItem($pKey)
	{
		return $this->config->$pKey;
	}

	function getConfig()
	{
		return $this->config;
	}

	protected function end_proc($pExitCode = 0)
	{
		exit($pExitCode);
	}

	function clearScreen()
	{
		Utils::clearScreen();
	}

	function run(){}
}