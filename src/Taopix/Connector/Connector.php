<?php
namespace Taopix\Connector;

use Taopix\Core\Utils\TaopixUtils;

class Connector
{
	/**
	 * @var string
	 */
	protected $apiKey = '';

	/**
	 * @var string
	 */
	protected $apiSecret = '';
	
	/**
	 * @var string
	 */
	protected $accessToken = '';
	
	/**
	 * @var TaopixUtils
	 */
	protected $utils;

	/**
	 * @var string
	 */
	protected $brandCode = '';

	/**
	 * @var string
	 */
	protected $applicationName = '';

	/**
	 * @var int
	 */
	protected $redactionNotificationDays = 0;

	/**
	 * @var string
	 */
	protected $licenseKeyCode = '';

	/**
	 * @var string
	 */
	protected $controlCentreURL = '';
	
	/**
	 * @var string
	 */
	protected $brandControlCentreURL = '';

	/**
	 * @var string
	 */
	protected $connectorURL = '';

	/**
	 * @var string
	 */
	protected $onlineURL = '';

	/**
	 * @var string
	 */
	protected $onlineUiURL = '';

	/**
	 * @var string
	 */
	protected $onlineApiURL = '';

	/**
	 * @var string
	 */
	protected $primaryDomain = '';

	/**
	* @var int
	*/
	protected $pricesIncludeTax = 0;

	/**
	 * @var Array
	 */
	protected $acConfig;

	/**
	 * @var int
	 */
	protected $id = 0;

	/**
	 * @var boolean
	 */
	protected $lineItemQtyProtected = false;

	/**
	 * Sets the api key 
	 *
	 * @param string $pApiKey api key of the store
	 * @return Connector Connector instance.
	 */		
	function setApiKey(string $pApiKey): Connector
	{
		$this->apiKey = $pApiKey;
		return $this;
	}

	/**
	 * Returns the api key
	 *
	 * @return String API key
	 */	
	function getApiKey(): string
	{
		return $this->apiKey;
	}

	/**
	 * Sets the Api Secret
	 *
	 * @param string $pApiSecret api secret for the store
	 * @return Connector Connector instance.
	 */		
	function setApiSecret(string $pApiSecret): Connector
	{
		$this->apiSecret = $pApiSecret;
		return $this;
	}

	/**
	 * Returns the API secret
	 *
	 * @return String api secret
	 */	
	function getApiSecret(): string
	{
		return $this->apiSecret;
	}

	/**
	 * Sets the access token 
	 *
	 * @param string $pAccessToken accesstoken to set
	 * @return Connector Connector instance.
	 */	
	function setAccessToken(string $pAccessToken): Connector
	{
		$this->accessToken = $pAccessToken;
		return $this;
	}

	/**
	 * Returns the Access Token
	 *
	 * @return String access token
	 */		
	function getAccessToken(): string
	{
		return $this->accessToken;
	}

	/**
	 * Sets the Online URL
	 *
	 * @param string $pOnlineURL url of the online designer
	 * @return Connector Connector instance.
	 */	
	function setOnlineURL(string $pOnlineURL): Connector
	{
		$this->onlineURL = $pOnlineURL;
		return $this;
	}

	/**
	 * Returns the online url
	 *
	 * @return String online designer url
	 */		
	function getOnlineURL(): string
	{
		return $this->onlineURL;
	}

	/**
	 * Sets the Online UI URL
	 *
	 * @param string $pOnlineUiURL url of the online designer
	 * @return Connector Connector instance.
	 */	
	function setOnlineUiURL(string $pOnlineUiURL): Connector
	{
		$this->onlineUiURL = $pOnlineUiURL;
		return $this;
	}

	/**
	 * Returns the online UI url
	 *
	 * @return String online designer UI url
	 */		
	function getOnlineUiURL(): string
	{
		return $this->onlineUiURL;
	}

	/**
	 * Sets the Online API URL
	 *
	 * @param string $pOnlineApiURL url of the online designer
	 * @return Connector Connector instance.
	 */	
	function setOnlineApiURL(string $pOnlineApiURL): Connector
	{
		$this->onlineApiURL = $pOnlineApiURL;
		return $this;
	}

	/**
	 * Returns the online API url
	 *
	 * @return String online designer API url
	 */		
	function getOnlineApiURL(): string
	{
		return $this->onlineApiURL;
	}

	/**
	 * Sets the Brand Code
	 *
	 * @param string $pBrandCode brandcode to set
	 * @return Connector Connector instance.
	 */		
	function setBrandCode(string $pBrandCode): Connector
	{
		$this->brandCode = $pBrandCode;
		return $this;
	}

	/**
	 * Returns the brand code
	 *
	 * @return String brand code
	 */		
	function getBrandCode(): string
	{
		return $this->brandCode;
	}

	/**
	 * Sets the Brand Application Name
	 *
	 * @param string $pApplicationName Application Name to set
	 * @return Connector Connector instance.
	 */		
	function setApplicationName(string $pApplicationName): Connector
	{
		$this->applicationName = $pApplicationName;
		return $this;
	}

	/**
	 * Returns the Brand Application Name
	 *
	 * @return String application name
	 */		
	function getApplicationName(): string
	{
		return $this->applicationName;
	}

	/**
	 * Sets the Brand Redaction notification days
	 *
	 * @param string $pRedactionNotificationDays Redaction notification days to set
	 * @return Connector Connector instance.
	 */		
	function setRedactionNotificationDays(string $pRedactionNotificationDays): Connector
	{
		$this->redactionNotificationDays = $pRedactionNotificationDays;
		return $this;
	}

	/**
	 * Returns the Redaction Notification days for the brand
	 *
	 * @return int Redaction notification days
	 */		
	function getRedactionNotificationDays(): int
	{
		return $this->redactionNotificationDays;
	}

	/**
	 * Sets the License key code
	 *
	 * @param string $pLicenseKeyCode license key to set
	 * @return Connector Connector instance.
	 */		
	function setLicenseKeyCode(string $pLicenseKeyCode): Connector
	{
		$this->licenseKeyCode = $pLicenseKeyCode;
		return $this;
	}

	/**
	 * Returns the license key code
	 *
	 * @return String license key code
	 */	
	function getLicenseKeyCode(): string
	{
		return $this->licenseKeyCode;
	}

	/**
	 * Sets the Control Centre URL
	 *
	 * @param string $pControlCentreURL url of the control centre
	 * @return Connector Connector instance.
	 */		
	public function setControlCentreURL(string $pControlCentreURL): Connector
	{
		$this->controlCentreURL = $pControlCentreURL;
		return $this;
	}

	/**
	 * Returns the control centre url
	 *
	 * @return String control centre url
	 */		
	public function getControlCentreURL(): string
	{
		return $this->controlCentreURL;
	}

	/**
	 * Sets the branded Control Centre URL
	 *
	 * @param string $pControlCentreURL branded url of the control centre
	 * @return Connector Connector instance.
	 */		
	public function setBrandControlCentreURL(string $pControlCentreURL): Connector
	{
		$this->brandControlCentreURL = $pControlCentreURL;
		return $this;
	}

	/**
	 * Returns the branded control centre url
	 *
	 * @return String branded control centre url
	 */		
	public function getBrandControlCentreURL(): string
	{
		return $this->brandControlCentreURL;
	}

	/**
	 * Sets the Connector URL
	 *
	 * @param string $pConnectorURL url of the store
	 * @return Connector Connector instance.
	 */	
	public function setConnectorURL(string $pConnectorURL): Connector
	{
		$this->connectorURL = $pConnectorURL;
		return $this;
	}

	/**
	 * Returns the Connector URL
	 *
	 * @return String connector url
	 */		
	public function getConnectorURL(): string
	{
		return $this->connectorURL;
	}

	/**
	 * Sets the TaopixUtils instance
	 *
	 * @param TaopixUtils $pUtils TaopixUtils instance to set 
	 * @return Connector Connector instance.
	 */	
	public function setUtils(TaopixUtils $pUtils): Connector
	{
		$this->utils = $pUtils;
		return $this;
	}

	/**
	 * Returns the TaopixUtils instance.
	 *
	 * @return TaopixUtils instance.
	 */		
	public function getUtils(): TaopixUtils
	{
		return $this->utils;
	}

	/**
	 * Sets the Primary Domain
	 *
	 * @param string $pPrimaryDomain Primary domain of the store
	 * @return Connector Connector instance.
	 */	
	public function setPrimaryDomain(string $pPrimaryDomain): Connector
	{
		$this->primaryDomain = 'https://' . $pPrimaryDomain;
		return $this;
	}

	/**
	 * Returns the Primary Domain
	 *
	 * @return String primary domain.
	 */		
	public function getPrimaryDomain(): string
	{
		return $this->primaryDomain;
	}

	/**
	 * Sets the PricesIncludeTax boolean
	 *
	 * @param int $pricesIncludeTax vale to set
	 * @return Product Product instance.
	 */
	public function setPricesIncludeTax(int $pricesIncludeTax): Connector
	{
		$this->pricesIncludeTax = $pricesIncludeTax;
		return $this;
	}

	/**
	 * Returns the pricesIncludeTax
	 *
	 * @return int pricesIncludeTax
	 */	
	public function getPricesIncludeTax(): int
	{
		return $this->pricesIncludeTax;
	}	

	/**
	 * Sets the AC Config instance
	 *
	 * @param array $pACConfig AC Config to set 
	 * @return Product Product instance.
	 */
	public function setACConfig(array $pACConfig): Connector
	{
		$this->acConfig = $pACConfig;
		return $this;
	}

	/**
	 * Returns the Ac Config
	 *
	 * @return array system config.
	 */	
	public function getACConfig(): array
	{
		return $this->acConfig;
	}

	/**
	 * Sets the connectorid
	 *
	 * @param string $pConnectorID id of the connector
	 * @return Connector Connector instance.
	 */		
	function setConnectorID(int $pConnectorID): Connector
	{
		$this->id = $pConnectorID;
		return $this;
	}

	/**
	 * Returns the id of the connector
	 *
	 * @return int connector id
	 */	
	function getConnectorID(): int
	{
		return $this->id;
	}

	/**
	 * Sets the setLineItemQtyProtected
	 *
	 * @param string $pLineItemQtyProtected on the connector
	 * @return Connector Connector instance.
	 */		
	function setLineItemQtyProtected(int $pLineItemQtyProtected): Connector
	{
		$this->lineItemQtyProtected = $pLineItemQtyProtected;
		return $this;
	}

	/**
	 * Returns the getLineItemQtyProtected of the connector
	 *
	 * @return int getLineItemQtyProtected
	 */	
	function getLineItemQtyProtected(): bool
	{
		return $this->lineItemQtyProtected;
	}


	public function __construct($pConnectorName, $pQueryArray)
	{
		$this->setUtils(new TaopixUtils());
		$getConnectorDetailsResult = $this->getConnectorDetails($pConnectorName, $pQueryArray);

		if ($getConnectorDetailsResult['result'] !== '')
		{
			throw new \Exception($getConnectorDetailsResult['result']);
		}

		$this
			->setConnectorURL($getConnectorDetailsResult['connectordetails']['connectorurl'])
			->setRedactionNotificationDays($getConnectorDetailsResult['connectordetails']['redactionnotificationdays'])
			->setControlCentreURL($this->getUtils()->correctPath($getConnectorDetailsResult['connectordetails']['weburl']));
		return $getConnectorDetailsResult['connectordetails'];
	}

	/**
	 * get connector details from the DB
	 *
	 * @param string $pConnectorName type of connector e.g. SHOPIFY 
	 * @param array $pQueryArray array to build up sql query 
	 * @return array connector details
	 */
	protected function getConnectorDetails(string $pConnectorName, array $pQueryArray): array
	{
		$utils = $this->getUtils();
		$db = $utils->getGlobalDBConnection();
		$systemConfigArray = $utils->getSystemConfig();
		$ac_config = $utils->getACConfig();

		if ($db)
		{
			$fields = $pQueryArray['fields'];
			$refFields = $pQueryArray['ref'];
			$refTypes = $pQueryArray['reftype'];
			$valueArray = Array();
			$resultName = Array();
			$fieldTypesArray = Array();

			$resultArray = Array('result' => '', 'connectordetails' => array());
			$resultName[] = 'weburl';
			$resultName[] = 'brandurl';
			$resultName[] = 'onlinedesignerurl';
			$resultName[] = 'onlineuiurl';
			$resultName[] = 'applicationname';
			$resultName[] = 'redactionnotificationdays';

			$queryString = '';
			$i = 0;
			foreach ($fields as $key => $value)
			{
				$queryString .= '`CONNECTORS`.`' . $value . '`,';
				$resultName[] = $value;
				$i++;
			}

			$refFieldString = '';
			$i = 0;
			foreach ($refFields as $key => $value)
			{
				if ($refFieldString != '')
				{
					$refFieldString .= 'AND ';	
				}
				$refFieldString .= '`CONNECTORS`.`' . $value . '` = ? ';
				$i++;
			}

			$valueArray = $pQueryArray['refvalue'];
			$fieldTypesArray = $refTypes;

			//add the connectorname
			$valueArray[] = $pConnectorName;
			$fieldTypesArray[] = 's';

			$queryString = substr($queryString, 0, -1);

			$query = 'SELECT
					CASE 
						WHEN `BRANDING`.`weburl` <> "" THEN `BRANDING`.`weburl`
						WHEN `BRANDING`.`weburl` = "" AND `BRANDING`.`displayurl` <> "" THEN `BRANDING`.`displayurl`
						ELSE (SELECT `weburl` FROM `BRANDING` WHERE `code` = "")
					END AS `weburl`,
					CASE
						WHEN `BRANDING`.`weburl` <> "" THEN `BRANDING`.`weburl`
						WHEN `BRANDING`.`weburl` = "" AND `BRANDING`.`displayurl` <> "" THEN `BRANDING`.`displayurl`
						WHEN `BRANDING`.`code` <> "" AND `BRANDING`.`weburl` = "" AND `BRANDING`.`displayurl` = "" 
						THEN CONCAT((SELECT `weburl` FROM `BRANDING` WHERE `code` = ""), "/' . $ac_config['WEBBRANDFOLDERNAME'] . '/", `BRANDING`.`name`)
						ELSE (SELECT `weburl` FROM `BRANDING` WHERE `code` = "")
					END AS `brandurl`,
					CASE 
						WHEN `BRANDING`.`onlinedesignerurl` <> "" THEN `BRANDING`.`onlinedesignerurl`
						ELSE (SELECT `onlinedesignerurl` FROM `BRANDING` WHERE `code` = "")
					END AS `onlinedesignerurl`, 
					CASE 
						WHEN `BRANDING`.`onlineuiurl` <> "" THEN `BRANDING`.`onlineuiurl`
						ELSE (SELECT `onlineuiurl` FROM `BRANDING` WHERE `code` = "")
					END AS `onlineuiurl`, 
					`BRANDING`.`applicationname`, `BRANDING`.`redactionnotificationdays`,
					 ' . $queryString . ' FROM `CONNECTORS` 
					 INNER JOIN `BRANDING` ON `BRANDING`.`code` = `CONNECTORS`.`brandcode`
					 WHERE (' . $refFieldString . ' AND `connectorname` = ?)';

			if ($stmt = $db->prepare($query))
			{
				// bind param dynamically
				if ($this->getUtils()->bindParams($stmt, $fieldTypesArray, $valueArray))
				{
					if ($stmt->execute())
					{
						if ($stmt->store_result())
						{
							if ($stmt->num_rows > 0)
							{
								// bind result dynamically
								$resultArray['connectordetails'] = $this->getUtils()->bindResult($stmt, $resultName, '');

								if (isset($resultArray['connectordetails']['connectorsecret']))
								{
									$resultArray['connectordetails']['connectorsecret'] = $this->getUtils()->decryptData($resultArray['connectordetails']['connectorsecret'], $systemConfigArray['secret'], true);
								}
							}
						}
						else
						{
							$resultArray['result'] = __FUNCTION__ . ' store result ' . $db->error;
						}

						$stmt->free_result();
						$stmt->close();
						$stmt = null;
					}
					else
					{
						$resultArray['result'] = __FUNCTION__ . ' execute  ' . $db->error;
					}
				}
				else
				{
					$resultArray['result'] = __FUNCTION__ . ' bind params  ' . $db->error;
				}
			}
			else
			{
				$resultArray['result'] = __FUNCTION__ . ' prepare  ' . $db->error;
			}

			$db->close();
		}
		else
		{
			$resultArray['result'] = __FUNCTION__ . ' Cant connect to database';
		}	

		return $resultArray;
	}

	/**
	 * Updates the connector with the access tokens
	 *
	 * @param string $pBrandCode brandcode
	 * @param array $pConnectorDetails array of connector details to update
	 * @return void
	 */
	protected function updateConnector(string $pBrandCode, array $pConnectorDetails)
	{
		$utils = $this->getUtils();
		$db = $utils->getGlobalDBConnection();

		$result = '';
		$setQuery = [];
		$bindTypes = [];
		$bindValues = [];

		foreach ($pConnectorDetails as $key => $value)
		{
			$setQuery[] = $key . ' = ?';
			$bindTypes[] = $utils->getBindType($value);
			$bindValues[] = $value;
		}

		$bindTypes[] = 's';
		$bindValues[] = $pBrandCode;

		if ($db)
		{
			$stmt = $db->prepare('UPDATE `CONNECTORS` SET ' . implode(',', $setQuery) . ' WHERE `brandcode` = ?');

			if ($stmt)
			{
				if ($utils->bindParams($stmt, $bindTypes, $bindValues))
				{
					if (!$stmt->execute())
					{
						// could not execute
						$result = __FUNCTION__ . 'updateConnector execute ' . $db->error;
					}
				}
				else
				{
					// could not bind parameters
					$result ='updateConnector bind ' . $db->error;
				}

				$stmt->free_result();
				$stmt->close();
				
			}
			else
			{
				// could not prepare statement
				$result = 'updateConnector prepare ' . $db->error;
			}

			$db->close();
		}
		else
		{
			// could not open database connection
			$result = 'updateConnector connect ' . $db->error;
		}

		if ($result !== '')
		{
			throw new \Exception($result);
		}
	}

	/**
	 * Creates a customer user account with the account code set tothe customer ID from the connector store.
	 *
	 * @param array $pUserDetails Array containing user details.
	 * @return array Taopix user account array.
	 */
	public function createUserAccount(array $pUserDetails, bool $pIsOrder): array
	{
		$utils = $this->getUtils();
		$customerID = $utils->getArrayParam($pUserDetails, 'id', '');

		// Check if a user account exists and use that one.
		$userAccount = $utils->getUserAccountFromAccountCode($customerID);

		if ($userAccount['result'] === 'str_ErrorNoAccount')
		{
			// An existing account does not exist with that account code, so we create a new user.
			$userAccount = $utils->createEmptyUserAccount($utils->getArrayParam($pUserDetails, 'groupcode', $this->getLicenseKeyCode()));
			
			$userAccount['login'] = $this->generateTempUserLogin();
			$userAccount['accountcode'] = $customerID;
			$userAccount['contactfirstname'] = $utils->getArrayParam($pUserDetails, 'firstname', '');
			$userAccount['contactlastname'] = $utils->getArrayParam($pUserDetails, 'lastname', '');
			$userAccount['emailaddress'] = $utils->getArrayParam($pUserDetails, 'email', '');
			$userAccount['telephonenumber'] = $utils->getArrayParam($pUserDetails, 'phone', '');
			$userAccount['isactive'] = 1;
			$userAccount['usertype'] = TPX_LOGIN_CUSTOMER;
		}

		if ($pIsOrder)
		{
			$userAccount = $utils->updateOrInsertExternalAccount($userAccount['recordid'], $userAccount, $userAccount['webbrandcode'], $userAccount['groupcode'], $userAccount['companycode']);
		}

		$userAccount['requirepasswordforsessioninactivity'] = false;
		return $userAccount;
	}

	/**
	 * Generates a temp user login name.
	 *
	 * @return string The temp user login value.
	 */
	private function generateTempUserLogin(): string
	{
		$login = '';
		$generateAttempts = 10;

		while ($generateAttempts > 0)
		{
			$generateAttempts--;

			// Generate a new user name, based on unique string.
			$login = 'tempuser' . substr(uniqid(), -6);

			// Check that the new login can be used, otherwise carry out the regeneration again.
			$uniqueLoginCheck = $this->getUtils()->getUserAccountFromBrandAndLogin($this->getBrandCode(), $login);

			if ($uniqueLoginCheck['result'] === 'str_ErrorNoAccount')
			{
				// No account exists, so new temp user name can be used.
				$generateAttempts = -1;
				$userAccount['login'] = $login;
			}
			else if ($uniqueLoginCheck['result'] !== '')
			{
				// An error other than no account has been returned, the process can not continue.
				throw new \Exception($uniqueLoginCheck['resultparam'], $uniqueLoginCheck['result']);

				$generateAttempts = -1;
			}
		}

		return $login;
	}
	
	/**
	 * Determines if device detection has already been set, or 
	 * runs the detect.php scripts to generate one.
	 * 
	 * @return bool True if device detection string exists.
	 */
	public function detectDevice(): bool
	{
		if (! array_key_exists('dd', $_POST))
		{
			include(__DIR__ . '/views/detect.php');
			return false;
		}
		else
		{
			return true;
		}
	}

	/**
	 * Function to execute on an install.
	 */
	public function install() {}

	/**
	 * Function to execute to generate a new Online project.
	 *
	 * @param string $pProductID The product ID of the product to create a project for.
	 * @param string $pDeviceDetection Device detection string.
	 * @param array $pCustomer Array containing customer account details.
	 * @param string $pLanguageCode Language code to open the Taopix Online designer in.
	 * @param array $pCustomParams Array containing any Taopix custom params for project modification.
	 * @return array The designer URL & online api url
	 */
	public function createProject(string $pProductID, string $pDeviceDetection, array $pCustomer, string $pLanguageCode, array $pCustomParams): array 
	{
		return ['redirecturl' => '', 'onlineapiurl' => ''];
	}

	/**
	 * Calls the editProject to be able to edit a Taopix project.
	 *
	 * @param string $pProjectRef Project ref of the project to edit.
	 * @param string $pCustomerID Hashed customer ID.
	 * @param string $pDeviceDetection Device detection string.
	 * @param string $pLanguageCode Language code to open the Taopix Online designer in.
	 * @return string The design URL to edit the project.
	 */
	public function editProject(string $pProjectRef, string $pCustomerID, string $pDeviceDetection, string $pLanguageCode): string
	{
		return '';
	}

	/**
	 * Calls the duplicateProject to be able to duplicate a Taopix project, and then
	 * calls editProject to open it for editing.
	 *
	 * @param string $pProjectRef Project ref of the project to duplicate.
	 * @param string $pCustomerID Hashed customer ID.
	 * @param string $pDeviceDetection Device detection string.
	 * @param string $pLanguageCode Language code to open the Taopix Online designer in.
	 * @return string The design URL to edit the duplicated project.
	 */
	public function duplicateProject(string $pProjectRef, string $pProjectName, string $pCustomerID, string $pDeviceDetection, string $pLanguageCode): string
	{
		return '';
	}

	/**
	 * Calls the previewProject to be able to show a preview of the project.
	 *
	 * @param string $pProjectRef Project ref of the project to duplicate.
	 * @param string $pDeviceDetection Device detection string.
	 * @param string $pLanguageCode Language code to open the project list in.
	 * @return string The design URL to share project.
	 */
	public function previewProject(string $pProjectRef, string $pDeviceDetection, string $pLanguageCode): string
	{
		return '';
	}

	/**
	 * Function to execute when an orderNotification is received.
	 *
	 * @param array $pNotificationArray Notification data array.
	 */
	public function orderNotification(array $pNotificationArray): string
	{
		return '';
	}

	/**
	 * Function to perform Taopix order item routing.
	 *
	 * @param array $pOrderData order data retireved from the call to getProjectOrderData.
	 */
	public function routeOrderItems(array $pOrderData): array
	{
		global $gConstants;

		$orderData = $pOrderData;
		
		if ($gConstants['optionms'])
		{
			$orderData = $this->getUtils()->routeOrder($orderData);
		}
		
		return $orderData;
	}

	/**
	 * Function to get the component upsell settings from either the brand or the licensekey.
	 *
	 * @param array $pLicenseKeyCode license key code to get the settings from and also look up the brand from if needed.
	 */
	public function getComponentUpSellConfig(string $pLicenseKeyCode): int
	{
		$utils = $this->getUtils();
		$db = $utils->getGlobalDBConnection();
		$componentUpSellSetting = 0;
		$result = '';

		if ($db)
		{
			$stmt = $db->prepare('SELECT IF(lk.usedefaultcomponentupsellsettings = 1, (SELECT componentupsellsettings FROM BRANDING br WHERE lk.webbrandcode = br.code), lk.componentupsellsettings)
			FROM LICENSEKEYS lk WHERE groupcode = ?');

			if ($stmt)
			{
				if ($stmt->bind_param('s', $pLicenseKeyCode))
				{
					if ($stmt->execute())
					{
						if ($stmt->store_result())
						{
							if ($stmt->num_rows > 0)
							{
								// bind result dynamically
								if ($stmt->bind_result($componentUpSellSetting))
								{
									if (!$stmt->fetch())
									{
										$result = __FUNCTION__ . ' fetch ' . $db->error;
									}
								}
								else
								{
									$result = __FUNCTION__ . ' bind result ' . $db->error;
								}

							}
						}
						else
						{
							$result = __FUNCTION__ . ' store result ' . $db->error;
						}

						$stmt->free_result();
						$stmt->close();
						$stmt = null;
					}
					else
					{
						$result = __FUNCTION__ . ' execute  ' . $db->error;
					}
				}
				else
				{
					// could not bind parameters
					$result = __FUNCTION__ . ' bind  ' . $db->error;
				}
			}
			else
			{
				// could not prepare statement
				$result =  __FUNCTION__ . ' prepare  ' . $db->error;
			}

			$db->close();
		}
		else
		{
			// could not open database connection
			$result =  __FUNCTION__ . ' connect  ' . $db->error;
		}

		if ($result !== '')
		{
			throw new \Exception($result);
		}

		return $componentUpSellSetting;
	}


    public function checkOrderExists(string $pOrderNumber): array
    {
        $db = $this->getUtils()->getGlobalDBConnection();

		$pOrderNumber = "'" . $pOrderNumber . "'";

        $result = '';
        $resultArray = array('result' => '', 'resultparam' => '', 'orderfound' => false, 'orderid' => 0, 'ordernumber' => '');

        $orderID = 0;
        $orderNumber = '';

        if ($db)
        {
            $stmt = $db->prepare('SELECT `id`, `ordernumber` FROM ORDERHEADER WHERE `ordernumber` = ?');

            if ($stmt)
            {
                if ($stmt->bind_param('s', $pOrderNumber))
                {
                    if ($stmt->execute())
                    {
                        if ($stmt->store_result())
                        {
                            if ($stmt->num_rows > 0)
                            {
                                if ($stmt->bind_result($orderID, $orderNumber))
                                {
                                    if ($stmt->fetch())
                                    {
                                        $resultArray['orderfound'] = true;
                                        $resultArray['orderid'] = $orderID;
                                        $resultArray['ordernumber'] = $orderNumber;
                                    }
                                    else {
                                        $result = __FUNCTION__ . ' fetch ' . $db->error;
                                    }
                                }
                                else
                                {
                                    $result = __FUNCTION__ . ' bind result ' . $db->error;
                                }
                            }
                        }
                        else
                        {
                            $result = __FUNCTION__ . ' store result ' . $db->error;
                        }

                        $stmt->free_result();
                        $stmt->close();
                        $stmt = null;
                    }
                    else
                    {
                        $result = __FUNCTION__ . ' execute  ' . $db->error;
                    }
                }
                else
                {
                    // could not bind parameters
                    $result = __FUNCTION__ . ' bind  ' . $db->error;
                }
            }
            else
            {
                // could not prepare statement
                $result =  __FUNCTION__ . ' prepare  ' . $db->error;
            }

            $db->close();
        }
        else
        {
            // could not open database connection
            $result =  __FUNCTION__ . ' connect  ' . $db->error;
        }

        $resultArray['result'] = $result;

        return $resultArray;
    }
}
