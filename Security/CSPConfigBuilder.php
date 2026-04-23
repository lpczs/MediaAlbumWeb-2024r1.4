<?php

namespace Security;

class CSPConfigBuilder
{
	private $cspConfig = [];
	private $rawConfig = '';
	private $filePath = '';

	public function __construct()
	{
		$this->filePath = dirname(__DIR__) . '/config/csp.config.json';
	}

	/**
	 * Read the config file that we have generated if it exists.
	 * Sets rawConfig and cspConfig properties.
	 *
	 * @returns void
	 */
	public function readFile()
	{
		if (file_exists($this->filePath))
		{
			$this->rawConfig = \file_get_contents($this->filePath);

			if ($this->rawConfig !== false)
			{
				$this->cspConfig = \json_decode($this->rawConfig, true);
				if ($this->cspConfig === null)
				{
					$this->cspConfig = [];
				}
			}
		}
	}

	/**
	 * Returns the csp rules for the brand and any metadata rules that need to be added.
	 *
	 * @param string $pBrandCode
	 * @return array
	 */
	public function getBrandCSP($pBrandCode)
	{
		if ($this->rawConfig === '')
		{
			$this->readFile();
		}

		$brandedInfo = array_key_exists($pBrandCode, $this->cspConfig) ? $this->cspConfig[$pBrandCode] : [];
		$metaDataInfo = array_key_exists('ALL', $this->cspConfig) ? $this->cspConfig['ALL'] : [];

		return array_merge_recursive($brandedInfo, $metaDataInfo);
	}

	/**
	 * Writes the content of the cspConfig variable to a file.
	 * Returns the number of bytes written or false on error.
	 *
	 * @return int | false
	 */
	public function writeFile()
	{
		$writeResult = 0;
		$newContents = \json_encode($this->cspConfig);

		if ($newContents !== $this->rawConfig)
		{
			if (file_exists($this->filePath))
			{
				// If the file is not currently writable make it writeable.
				if (! is_writable($this->filePath))
				{
					chmod($this->filePath, 0764);
				}
			}

			$writeResult = \file_put_contents($this->filePath, $newContents);
		}

		return $writeResult;
	}

	/**
	 * Returns an array containing the differences between the passed arrays.
	 *
	 * @param array $pNewConfig New config array.
	 * @param array $pCurrentConfig Current config array.
	 * @return array
	 */
	private function diffConfigs($pNewConfig, $pCurrentConfig)
	{
		$result = [];

		foreach ($pNewConfig as $key => $val)
		{
			if (is_array($val) && isset($pCurrentConfig[$key]))
			{
				$tmp = $this->diffConfigs($val, $pCurrentConfig[$key]);
				if ($tmp)
				{
					$result[$key] = $tmp;
				}
			}
			elseif (!isset($pCurrentConfig[$key]))
			{
				$result[$key] = null;
			}
			elseif ($val !== $pCurrentConfig[$key])
			{
				$result[$key] = $pCurrentConfig[$key];
			}

			if (isset($pCurrentConfig[$key]))
			{
				unset($pCurrentConfig[$key]);
			}
		}

		$result = array_merge($result, $pCurrentConfig);

		return $result;
	}

	/**
	 * Processes the passed csp details and generates a config file .
	 *
	 * @param array $pDetails Array containing csp rules.
	 * @return int | false
	 */
	public function buildCSPConfig($pDetails)
	{
		$returnValue = null;
		$this->readFile();
		$newArray = $this->buildCSPConfigArray($pDetails);
		$changes = 0;

		foreach ($newArray as $brandCode => $config)
		{
			$current = isset($this->cspConfig[$brandCode]) ? $this->cspConfig[$brandCode] : [];

			$diffs = $this->diffConfigs($config, $current);

			if ((count($diffs) > 0))
			{
				$this->cspConfig[$brandCode] = $config;
				$changes++;
			}
		}

		if ($changes > 0)
		{
			$writeResponse = $this->writeFile();

			$returnValue = $writeResponse;
		}

		return $returnValue;
	}

	/**
	 * Removes the csp configuration for a given brand code.
	 * @param string $pBrandCodes Brand codes to remove csp details for.
	 * @return bool|int Returns number of bytes written or false on error.
	 */
	public function removeCSPConfigKeys($pBrandCodes)
	{
		$this->readFile();
		$removed = 0;
		$returnValue = false;

		foreach ($pBrandCodes as $brandCode)
		{
			if (isset($this->cspConfig[$brandCode]))
			{
				unset($this->cspConfig[$brandCode]);
				$removed++;
			}
		}

		// If we have removed one or more keys from the cspConfig update the file
		if ($removed > 0)
		{
			$this->writeFile();
		}

		return $returnValue;
	}

	/**
	 * Builds Array of csp rules to deal with.
	 *
	 * @param array $pDetails Associative array of brandcpde => csp rules to build.
	 * @return array
	 */
	public function buildCspConfigArray($pDetails)
	{
		$csp = [];
		foreach ($pDetails as $brandCode => $details)
		{
			if ($brandCode !== 'ALL')
			{
				$csp[$brandCode] = $this->processKey($details);
			}
			else
			{
				$csp[$brandCode] = $this->processMetaData($details);
			}
		}

		return $csp;
	}

	/**
	 * Process details into csp builder formatted rules.
	 *
	 * @param array $pDetails associative array containing urls, and flag for analytics and tag manager.
	 * @return array.
	 */
	private function processKey($pDetails)
	{
		$returnArray = [];

		foreach ($pDetails as $key => $information)
		{
			// Reset the csp rules array for each key
			$cspArray = [];

			if ($key === 'urls')
			{
				$cspArray = $this->processUrls($information);
			}
			else if ($key === 'analytics')
			{
				if ($information === true)
				{
					$cspArray = [
						'script-src' => [
							'www.google-analytics.com',
						],
						'img-src' => [
							'www.google-analytics.com',
						],
						'connect-src' => [
							'www.google-analytics.com',
						],
					];
				}
			}
			else if ($key === 'tagmanager')
			{
				if ($information === true)
				{
					$cspArray = [
						'script-src' => [
							'www.googletagmanager.com',
						],
						'img-src' => [
							'www.googletagmanager.com',
						],
					];
				}
			}

			// Set return array to be a merged version of the current return array and additional csp rules.
			$returnArray = array_merge_recursive($returnArray, $cspArray);
		}

		return $returnArray;
	}

	/**
	 * Process urls supplied from csp brand config
	 *
	 * @param array $pDetails associative array containing brand urls.
	 * @return array
	 */
	private function processUrls($pDetails)
	{
		$cspArray = [
			'child-src' => [],
			'font-src' => [],
			'frame-src' => [],
			'img-src' => [],
			'script-src' => [],
			'style-src' => [],
		];

		foreach ($pDetails as $urlType => $url)
		{
			if (trim($url) !== '')
			{
				switch ($urlType)
				{
					case 'displayUrl':
					case 'webUrl':
					case 'defaultWebUrl':
					{
						if (! in_array($url, $cspArray['script-src']))
						{
							$cspArray['script-src'][] = $url;
							$cspArray['img-src'][] = $url;
							$cspArray['style-src'][] = $url;
							$cspArray['font-src'][] = $url;
							$cspArray['connect-src'][] = $url;
						}
						break;
					}
					case 'onlineDesignerUrl':
					{
						$cspArray['img-src'][] = $url;
						$cspArray['frame-src'][] = $url;
						$cspArray['child-src'][] = $url;
						break;
					}
				}
			}
		}

		return $cspArray;
	}

	/**
	 * Process array of urls for metadata images to be loaded from.
	 *
	 * @param array $pDetails Array of urls to process over.
	 * @return array
	 */
	private function processMetaData($pDetails)
	{
		$returnArray = [];

		foreach ($pDetails as $key => $information)
		{
			// Reset the csp rules array for each key
			$cspArray = [
				'img-src' => [],
			];

			if ($key === 'urls')
			{
				foreach ($information as $imageSource)
				{
					$cspArray['img-src'][] = $imageSource;
				}
			}

			$returnArray = array_merge_recursive($returnArray, $cspArray);
		}

		return $returnArray;
	}

	/**
	 * Parses a URL into a CSP format.
	 *
	 * @param string $pUrl URL we are working with.
	 * @return string
	 */
	public function parseUrl($pUrl)
	{
		$returnUrl = '';

		if ($pUrl !== '')
		{
			$urlInfo = parse_url($pUrl);

			if ($urlInfo !== false)
			{
				$returnUrl = (isset($urlInfo['scheme']) ? $urlInfo['scheme'] : 'http') . '://' . $urlInfo['host'] . (isset($urlInfo['port']) ? ':' . $urlInfo['port'] : '');
			}
		}

		return $returnUrl;
	}
}