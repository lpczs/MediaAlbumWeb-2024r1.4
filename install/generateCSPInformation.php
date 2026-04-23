<?php
use Security\CSPConfigBuilder;

class generateCSPInformation extends ExternalScript
{
	private $cspDetails = [];

	public function run()
	{
		$this->printMsg('Generating Base CSP Details.');
		$this->getBrandDetails();
		$this->getMetaDataImageUrls();

		$builder = new CSPConfigBuilder();

		$buildResponse = $builder->buildCspConfig($this->cspDetails);

		if ($buildResponse === null)
		{
			$this->printMsg('Base CSP Details existed and no changes were needed.');
		}
		else if ($buildResponse === false)
		{
			$this->printMsg('Unable to write to csp.config.json in config folder.');
		}
		else
		{
			$filePath = '../config/csp.config.json';

			// Set the correct permissions and owner for the file.
			if (file_exists($filePath))
			{
				// Set the permissions on the file.
				chmod($filePath, 0764);

				// Set the owner and group to be correct for the config file, this should be tpxapache:tpxsrv
				chown($filePath, 'tpxapache');
			}
			$this->printMsg('Base CSP Rules generated.');
		}

		// Add config keys to mediaalbumweb.conf
		$this->addConfigKeys();
	}

	/**
	 * Sets details for this brand, web, display, and online urls.
	 * Also sets if analytics and or tag manager are configured for the brand.
	 *
	 * @returns void
	 */
	private function getBrandDetails()
	{
		$code = '';
		$displayUrl = '';
		$webUrl = '';
		$onlineDesignerUrl = '';
		$analyticsCode = '';
		$tagManagerCode = '';

		$query = "SELECT `code`, `displayurl`, `weburl`, `onlinedesignerurl`, `googleanalyticscode`, `googletagmanagercccode` FROM `branding` ORDER BY `code` ASC";

		if ($stmt = $this->dbConnection->prepare($query))
		{
			if ($stmt->bind_result($code, $displayUrl, $webUrl, $onlineDesignerUrl, $analyticsCode, $tagManagerCode))
			{
				if ($stmt->execute())
				{
					while($stmt->fetch())
					{
						$defaultWebUrl = '';

						if ($code === '')
						{
							$code = 'DEFAULT';
						}
						else
						{
							$defaultWebUrl = $this->cspDetails['DEFAULT']['urls']['webUrl'];
						}

						if (! array_key_exists($code, $this->cspDetails))
						{
							$this->cspDetails[$code] = [
								'urls' => [],
								'analytics' => false,
								'tagmanager' => false,
							];
						}

						$this->cspDetails[$code]['urls'] = [
							'displayUrl' => $this->parseUrl(trim($displayUrl)),
							'webUrl' => $this->parseUrl(trim($webUrl)),
							'onlineDesignerUrl' => $this->parseUrl(trim($onlineDesignerUrl)),
						];
						
						
						if (($code !== 'DEFAULT') && (! in_array($defaultWebUrl, $this->cspDetails[$code]['urls'])))
						{
							$this->cspDetails[$code]['urls']['defaultWebUrl'] = $defaultWebUrl;
						}

						if ($analyticsCode !== '')
						{
							$this->cspDetails[$code]['analytics'] = true;
						}

						if ($tagManagerCode !== '')
						{
							$this->cspDetails[$code]['tagmanager'] = true;
						}
					}
				}
			}
		}
	}

	/**
	 * Returns an array containing the unique urls for metadata images.
	 *
	 * @returns array
	 */
	private function getMetaDataImageUrls()
	{
		$flagData = '';
		$query = "SELECT `flags` FROM `keywords` WHERE `flags` LIKE '%<p>%'";

		$this->cspDetails['ALL'] = [
			'urls' => [],
		];

		if ($stmt = $this->dbConnection->prepare($query))
		{
			if ($stmt->bind_result($flagData))
			{
				if ($stmt->execute())
				{
					while($stmt->fetch())
					{
						$flags = explode('<br>', $flagData);

						foreach ($flags as $flag)
						{
							if (strstr($flag, '<p>') !== false)
							{
								$flagInfo = explode('<p>', $flag);

								// Check if we have a second part to the flag info, which is not empty and contains a url looking string.
								if (isset($flagInfo[1]) && (trim($flagInfo[1]) != '') && (strstr($flagInfo[1], '://') !== false))
								{
									$urlInfo = $this->parseUrl(trim($flagInfo[1]));

									if (! in_array($urlInfo, $this->cspDetails['ALL']['urls']))
									{
										$this->cspDetails['ALL']['urls'][] = $urlInfo;
									}
								}
							}
						}
					}
				}
			}
		}
	}

	/**
	 * Parses a URL into a CSP format.
	 *
	 * @param string $pUrl URL we are working with.
	 * @return string
	 */
	private function parseUrl($pUrl)
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

	/**
	 * prints a message to the screen.
	 *
	 * @param string $pMsg The message text.
	 */
	private function printMsg($pMsg)
	{
		echo $pMsg . PHP_EOL;
	}

	/**
	 * Adds missing config keys to mediaalbumweb.conf
	 */
	private function addConfigKeys()
	{
		$additionalConfig = '';
		$error = '';
		$keyNames = [
			'CONTENTSECURITYPOLICYIMGSRC',
			'CONTENTSECURITYPOLICYCONNECTSRC',
			'CONTENTSECURITYPOLICYSTYLESRC',
			'CONTENTSECURITYPOLICYOBJECTSRC',
			'CONTENTSECURITYPOLICYMEDIASRC',
			'CONTENTSECURITYPOLICYFONTSRC',
			'CONTENTSECURITYPOLICYCHILDSRC',
			'CONTENTSECURITYPOLICYSCRIPTSRC',
			'CONTENTSECURITYPOLICYFRAMESRC',
		];
		$numberOfKeysToAdd = count($keyNames);

		$ac_config = UtilsObj::readConfigFile('../config/mediaalbumweb.conf');

		for ($i = 0; $i < $numberOfKeysToAdd; $i++)
		{
			if (! isset($ac_config[$keyNames[$i]]))
			{
				$additionalConfig .= $keyNames[$i] . '=' . PHP_EOL;
			}
		}

		if ($additionalConfig !== '')
		{
			if ($fp = @fopen('../config/mediaalbumweb.conf', 'a'))
			{
				fwrite($fp, PHP_EOL . $additionalConfig);
				fclose($fp);
			}
			else
			{
				$error = 'Unable to open mediaalbumweb.conf to add CSP keys.';
			}
		}

		if ($error != '')
		{
			$this->printMsg($error);
		}
		else
		{
			$this->printMsg('CSP Config keys added to mediaalbumweb.conf');
		}
	}
}