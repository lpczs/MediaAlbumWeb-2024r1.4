<?php

namespace Taopix\Core\Config;

use Taopix\Core\HashMap;

class Config
{
    protected $configFilePath;
    protected $config;

	function __construct($pConfigFilePath)
	{

        if (file_exists($pConfigFilePath))
        {
            $this->configFilePath = $pConfigFilePath;

            $this->config = new HashMap(Config::readConfigFile($this->configFilePath));
        }
        else
        {
            throw new \Exception("Config file not found: " . $pConfigFilePath);
        }
	}

    function getGlobalConfig()
    {
        return $this->config->asArray();
    }

    function getConfigFile()
    {
        return $this->configFilePath;
    }

    public function __get($pName)
    {
        return $this->config[$pName];
    }

    public function __set($pName, $pValue)
    {
        return $this->config[$pName] = $pValue;
    }

    public function getWithDefault($pName, $pDefault)
    {
        if ($this->config[$pName])
        {
            return $this->config[$pName];
        }
        else
        {
            return $pDefault;
        }
    }

    public function mergeConfig(\Taopix\Core\Config\Config $pNewConfig, $pPreserveDuplicates = false)
    {
        $newSettingsArray = $pNewConfig->getGlobalConfig();

        foreach ($newSettingsArray as $key => $value)
        {
            if ($this->config[$key])
            {
                if (!$pPreserveDuplicates)
                {
                    $this->config[$key] = $value;
                }
            }
            else
            {
                $this->config[$key] = $value;
            }
        }

    }

    static function readConfigFile($pConfigFilePath)
    {
        // read a config file and return it as an exploded array
        $resultArray = Array();
        $comment = '#';

        $fp = fopen($pConfigFilePath, 'rb');
        if ($fp)
        {
            // determine if the file has a utf-8 bom and skip it if it does
            $bom = fread($fp, 3);
            if ($bom != "\xEF\xBB\xBF")
            {
                rewind($fp);
            }

            while (!feof($fp))
            {
                $line = trim(fgets($fp));
                if ($line && !preg_match("/^$comment/", $line))
                {
                    $pieces = explode('=', $line, 2);

                    $option = trim($pieces[0]);
                    if (count($pieces) > 1)
                    {
                        $value = trim($pieces[1]);
                    }
                    else
                    {
                        $value = '';
                    }
                    $resultArray[$option] = $value;
                }
            }

            fclose($fp);
        }

        return $resultArray;
    }

}