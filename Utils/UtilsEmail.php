<?php
require_once(__DIR__ . '/../libs/external/EmailContent/converthtml2text.php');
require_once(__DIR__ . '/../Utils/TaopixOAuthProvider.php');
require_once(__DIR__ . '/../Utils/TaopixOAuthRefreshToken.php');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception as phpmailerException;
use PHPMailer\PHPMailer\OAuth;

class TaopixMailer extends PHPMailer
{
    /**
   	* Initialises a phpmailer object
   	*
   	* @author Kevin Gale
	* @since Version 1.5.3
 	*/
    public function __construct()
    {
        // constructor - configure the email via the preferences
        global $gConstants;

        $this->IsSMTP();

        $this->Timeout = 30;
        $this->CharSet = 'utf-8';
   		$this->Encoding = 'base64';
    }

    function getEmailTemplate($pMessageRootPath, $pWebBrandCode, $pWebAppName, $pHomeURL, $pLocale, $pUserID, $pParamArray)
    {
        // return the email template data based on the parameters
        global $ac_config;
        global $gConstants;
        global $gSession;

        $result = Array();

        $htmlMessage = '';
        $plainTextMessage = '';
		$plainTextEmailContent = '';

        $smarty = SmartyObj::newSmarty('Email', $pWebBrandCode, $pWebAppName, $pLocale, false, false);
        $smarty->templateSubPath = 'email';

        // build the brand URL based on default brand if pHomeURL is empty.
        $defaults = DatabaseObj::getBrandingFromCode('');
        if ($pHomeURL == '')
        {
            $pHomeURL = UtilsObj::correctPath($defaults['displayurl']) . ($ac_config['WEBBRANDFOLDERNAME'] == '' ? 'Branding' : $ac_config['WEBBRANDFOLDERNAME']) . '/' . $smarty->webBrandFolder . '/';
        }

        $homeURL = $pHomeURL;

        if ($homeURL == '')
        {
            $homeURL = $ac_config['HOMEURL'];
        }

        if (($gConstants['optionwscrp']) && (file_exists('../Customise/scripts/EDL_ExternalCustomerAccount.php')))
        {
            require_once('../Customise/scripts/EDL_ExternalCustomerAccount.php');

            if (method_exists('ExternalCustomerAccountObj', 'ssoSetHomeURL'))
            {
                $paramArray = array();
                $paramArray['ssotoken'] = $gSession['userdata']['ssotoken'];
                $paramArray['ssoprivatedata'] = $gSession['userdata']['ssoprivatedata'];
                $paramArray['brandcode'] = $pWebBrandCode;
                $paramArray['url'] = $homeURL;

                // call the ssoSetHomeURL command so that the licensee can add any extra parameters they might need adding to the
                // home URL in the email
                $homeURL = ExternalCustomerAccountObj::ssoSetHomeURL($paramArray);
            }
        }

        $smarty->assign('homeurl', $homeURL);

        if ($pUserID > 0)
        {
            $userAccountArray = DatabaseObj::getUserAccountFromID($pUserID);
            $smarty->assign('contactfirstname', $userAccountArray['contactfirstname']);
            $smarty->assign('contactlastname', $userAccountArray['contactlastname']);
            $smarty->assign('emailaddress', $userAccountArray['emailaddress']);
        }

		// Get email logo.
		$brandingArray = DatabaseObj::getBrandingFromCode($pWebBrandCode);

		$brandFileInfo = DatabaseObj::getBrandAssetData($brandingArray['id'], TPX_BRANDING_FILE_TYPE_EMAIL_LOGO, true);

		$brandImage = '';

		// If a logo has been found, use it...
		if ($brandFileInfo['data']['id'] > 0)
		{
			$brandImage = UtilsObj::correctPath($ac_config['CONTROLCENTREASSETSPATH'], DIRECTORY_SEPARATOR, true) . 'images/' . $brandFileInfo['data']['path'];
			$imageData = file_get_contents($brandImage);

			$brandImage = "data:" . $brandFileInfo['data']['mime'] . ";base64," . base64_encode($imageData);
		}
		else
		{
			// ...otherwise use the default for the brand or the default logo.png
            $brandImage = str_replace(array('Customise/email/', 'Branding/' . $brandingArray['name'] . '/email/'), '', $brandFileInfo['data']['path']);
		}

		$smarty->assign('brandLogo', $brandImage);

        //test if the html template exist
        $sHtmlTemplate = $smarty->getLocaleTemplate($pMessageRootPath . '/email.html', $pLocale);

        if ($pParamArray)
        {
            foreach($pParamArray as $param => $value)
            {
                if (!is_array($value))
                {
                    if ($sHtmlTemplate == '')
                    {
                        $sValue = str_replace(array('<br>', '<br/>', '<br />'), array("\n","\n","\n"), $value);
                    } else {
                    	$sValue = str_replace( "\n", '<br>', UtilsObj::encodeString($value));
                    }

                    switch((string)$param)
                    {
                        case ('emailContent'): // emailContent is the tag that contents the HTML code generated from DisplayJobTicket function
                        case ('emailTitle'): // emailTitle is the tag that contents the HTML code generated for the email subject
                        {
                            if ($sHtmlTemplate == '')
                            {
                                $ConvertHTML2Text = new ConvertHTML2Text();
                                $value = $ConvertHTML2Text->Convert($value);
                            }
							else if ('' !== $value)
							{
								// emailContent needs to be converted to plaintext separately for if the HTML email is viewed as plaintext in the email client.
								$ConvertHTML2Text = new ConvertHTML2Text();
                                $plainTextEmailContent = $ConvertHTML2Text->Convert($value);
							}
                            $smarty->assign($param, $value);
                            break;
                        }

                        default:
                        {
                            $smarty->assign($param, $sValue);
                            break;
                        }
                    }
                }
                else
                {
                    $bufValue = $value;
					if (! is_array($bufValue))
					{
						for ($i = 0; $i < count($bufValue); $i++)
						{
							if ($sHtmlTemplate == '')
							{
								$bufValue[$i] = str_replace(array('<br>', '<br/>', '<br />'), array("\n","\n","\n"), UtilsObj::encodeString($bufValue[$i]));
							} else {
								$bufValue[$i] = str_replace( "\n", '<br>', UtilsObj::encodeString($bufValue[$i]));
							}
						}
					}
					else
					{
						for ($i = 0; $i < count($bufValue); $i++)
						{
							foreach ($bufValue[$i] as $keyVal => $bufVal)
							{
								if ($sHtmlTemplate == '')
								{
									$bufValue[$i][$keyVal] = str_replace(array('<br>', '<br/>', '<br />'), array("\n","\n","\n"), UtilsObj::encodeString($bufVal));
								}
								else
								{
									$bufValue[$i][$keyVal] = str_replace( "\n", '<br>', UtilsObj::encodeString($bufVal));
								}
							}
						}
					}
					$smarty->assign($param, $bufValue);
                }
            }
        }

		// Get the Branded email text
		$brandFooterText = DatabaseObj::getBrandCustomText($brandingArray['id'], TPX_BRANDING_TEXT_TYPE_SIGNATURE);

		if (($brandFooterText['data']['enabled'] == 1) && ($brandFooterText['data']['data'] !== ''))
		{
			$emailSignature = $brandFooterText['data']['data'];

			$brandFooterText = LocalizationObj::getLocaleString($emailSignature, $pLocale, true);

			// Convert html to plain text if require
			if ($sHtmlTemplate == '')
			{
				$ConvertHTML2Text = new ConvertHTML2Text();
				$brandFooterText = $ConvertHTML2Text->Convert($brandFooterText);
			}
			else
			{
				// emailContent needs to be converted to plaintext separately for if the HTML email is viewed as plaintext in the email client.
				$ConvertHTML2Text = new ConvertHTML2Text();
				$plainTextEmailContent = $ConvertHTML2Text->Convert($brandFooterText);
			}

			// If html template, convert line breaks to <br>
			if ($sHtmlTemplate != '')
			{
				$brandFooterText = str_replace("\n", '<br>', UtilsObj::encodeString($brandFooterText, true));
			}
		}
		else
		{
			$brandFooterText ='';
		}

		$smarty->assign('emailsignature', $brandFooterText);

        if ($sHtmlTemplate == '')
        {
            // we don't have a html template so search for the plain text template
            $plainTextMessage = $smarty->fetchLocale($pMessageRootPath . '/email.txt', $pLocale);
        }
        else
        {
            //load html template
            $htmlMessage = $smarty->fetchLocale($pMessageRootPath . '/email.html', $pLocale);

			if ($plainTextEmailContent != '')
			{
				// Override the emailContent variable with the plaintext version of the emailContent for the plaintext message.
				$smarty->assign('emailContent', $plainTextEmailContent);
			}

            // we have a html template so we must use its path to get the plain text template
            $plainTextMessage = $smarty->fetchLocale($smarty->lastTemplateParentPath . '/email.txt', $pLocale);
        }
        $result['html'] = $htmlMessage;
        $result['plain'] = $plainTextMessage;
        $result['templateparentpath'] = $smarty->lastTemplateParentPath;

        return $result;
    }


	public function sendEmailContents($header, $body, $messageTitle, $pLocale, $pEmailName, $pEmailAddress, $pBCCEmailName, $pBCCEmailAddress, $pWebBrandCode, $pEmailReplyToName = '', $pEmailReplyToAddress = '', $serverDetails = array())
	{
		if (count($serverDetails) == 0)
		{
	        // brand email settings
	    	$brandSettings = DatabaseObj::getBrandingFromCode($pWebBrandCode);
	    	if ($brandSettings['usedefaultemailsettings'] == 1)
	    	{
		        // default email settings
	        	$brandSettings = DatabaseObj::getBrandingFromCode('');
	    	}

	    	if (($pEmailReplyToName != '') && ($pEmailReplyToAddress != ''))
	    	{
	    		$serverDetails['smtpsystemfromname'] = $pEmailReplyToName;
	    		$serverDetails['smtpsystemreplytoaddress'] = $pEmailReplyToAddress;
				$serverDetails['smtpsystemreplytoname'] = $pEmailReplyToName;
	    	}
	    	else
	    	{
	    		$serverDetails['smtpsystemfromname'] = $brandSettings['smtpsystemfromname'];
	    		$serverDetails['smtpsystemreplytoaddress'] = $brandSettings['smtpsystemreplytoaddress'];
				$serverDetails['smtpsystemreplytoname'] = $brandSettings['smtpsystemreplytoname'];
	    	}
			$serverDetails['smtpaddress'] = $brandSettings['smtpaddress'];
			$serverDetails['smtpport'] = $brandSettings['smtpport'];
			$serverDetails['smtpsystemfromaddress'] = $brandSettings['smtpsystemfromaddress'];
			$serverDetails['smtpauth'] = $brandSettings['smtpauth'];
			$serverDetails['smtpauthusername'] = $brandSettings['smtpauthusername'];
			$serverDetails['smtpauthpassword'] = $brandSettings['smtpauthpassword'];
            $serverDetails['smtptype'] = $brandSettings['smtptype'];
		}

        $emailObj2 = new TaopixMailer();
        $sendingResult = $emailObj2->taopixSendEmailContents($header, $body, $messageTitle, $pLocale, $pEmailName, $pEmailAddress, $pBCCEmailName, $pBCCEmailAddress, $pWebBrandCode, $serverDetails);

		return $sendingResult;
	}


    function sendTemplateEmail($pMessageRootPath, $pWebBrandCode, $pWebAppName, $pHomeURL, $pLocale, $pEmailName, $pEmailAddress, $pBCCEmailName, $pBCCEmailAddress, $pUserID, $pParamArray, $pEmailReplyToName = '', $pEmailReplyToAddress = '')
    {
    	$resultArray = $this->generateEmailContents($pMessageRootPath, $pWebBrandCode, $pWebAppName, $pHomeURL, $pLocale, $pEmailName, $pEmailAddress, $pBCCEmailName, $pBCCEmailAddress, $pUserID, $pParamArray, $pEmailReplyToName, $pEmailReplyToAddress);

        if (is_array($resultArray))
        {
           	$orderHeaderID = UtilsObj::getArrayParam($pParamArray, 'orderid', 0);
    		$orderItemID = UtilsObj::getArrayParam($pParamArray, 'orderitemid', 0);
    		$targetUserID = UtilsObj::getArrayParam($pParamArray, 'targetuserid', 0);

           	DatabaseObj::createEvent('TAOPIX_EMAIL', '', '', $pWebBrandCode, '', 0, $resultArray[0], $resultArray[1], $resultArray[2], $pLocale, $pEmailAddress, $pEmailName, $pBCCEmailAddress, $pBCCEmailName, $orderHeaderID, $orderItemID, $pUserID, '', '', $targetUserID);
		}

    	return true;
    }

    function sendTemplateBulkEmail($pMessageRootPath, $pWebBrandCode, $pWebAppName, $pHomeURL, $pLocale, $pEmailName, $pEmailAddress, $pBCCEmailName, $pBCCEmailAddress, $pUserID, $pParamArray, $pEmailReplyToName = '', $pEmailReplyToAddress = '')
    {
    	$resultArray = $this->generateEmailContents($pMessageRootPath, $pWebBrandCode, $pWebAppName, $pHomeURL, $pLocale, $pEmailName, $pEmailAddress, $pBCCEmailName, $pBCCEmailAddress, $pUserID, $pParamArray, $pEmailReplyToName, $pEmailReplyToAddress);

        if (is_array($resultArray))
        {
           	$orderHeaderID = UtilsObj::getArrayParam($pParamArray, 'orderid', 0);
    		$orderItemID = UtilsObj::getArrayParam($pParamArray, 'orderitemid', 0);
    		$targetUserID = UtilsObj::getArrayParam($pParamArray, 'targetuserid', 0);

           	DatabaseObj::createEvent('TAOPIX_BULKEMAIL', '', '', $pWebBrandCode, '', 0, $resultArray[0], $resultArray[1], $resultArray[2], $pLocale, $pEmailAddress, $pEmailName, $pBCCEmailAddress, $pBCCEmailName, $orderHeaderID, $orderItemID, $pUserID, '', '', $targetUserID);
		}

    	return true;
    }


    function sendTemplateTestEmail($pMessageRootPath, $pWebBrandCode, $pWebAppName, $pHomeURL, $pLocale, $pEmailName, $pEmailAddress, $pBCCEmailName, $pBCCEmailAddress, $pUserID, $pParamArray, $pServerDetails)
    {
    	$resultArray = $this->generateEmailContents($pMessageRootPath, $pWebBrandCode, $pWebAppName, $pHomeURL, $pLocale, $pEmailName, $pEmailAddress, $pBCCEmailName, $pBCCEmailAddress, $pUserID, $pParamArray, '', '', $pServerDetails);

        $theResult = $this->sendEmailContents($resultArray[0], $resultArray[1], $resultArray[2], $pLocale, $pEmailName, $pEmailAddress, $pBCCEmailName, $pBCCEmailAddress, $pWebBrandCode, '', '', $pServerDetails);

    	return $theResult['resultParam'];
    }


    function generateEmailContents($pMessageRootPath, $pWebBrandCode, $pWebAppName, $pHomeURL, $pLocale, $pEmailName, $pEmailAddress, $pBCCEmailName, $pBCCEmailAddress, $pUserID, $pParamArray, $pEmailReplyToName, $pEmailReplyToAddress, $pServerDetails = array())
    {
    	// send the specified email template
        global $gSession;

		if (count($pServerDetails) == 0)
		{
	        // brand email settings
	    	$brandSettings = DatabaseObj::getBrandingFromCode($pWebBrandCode);
	    	if ($brandSettings['usedefaultemailsettings'] == 1)
	    	{
		        // default email settings
	        	$brandSettings = DatabaseObj::getBrandingFromCode('');
	    	}

			$pServerDetails['smtpaddress'] = $brandSettings['smtpaddress'];
			$pServerDetails['smtpport'] = $brandSettings['smtpport'];
			$pServerDetails['smtpsystemfromname'] = $brandSettings['smtpsystemfromname'];
			$pServerDetails['smtpsystemfromaddress'] = $brandSettings['smtpsystemfromaddress'];
			$pServerDetails['smtpsystemreplytoaddress'] = $brandSettings['smtpsystemreplytoaddress'];
			$pServerDetails['smtpsystemreplytoname'] = $brandSettings['smtpsystemreplytoname'];
			$pServerDetails['smtpauth'] = $brandSettings['smtpauth'];
			$pServerDetails['smtpauthusername'] = $brandSettings['smtpauthusername'];
			$pServerDetails['smtpauthpassword'] = $brandSettings['smtpauthpassword'];
            $pServerDetails['smtptype'] = $brandSettings['smtptype'];
		}

		$this->Host = $pServerDetails['smtpaddress'];
		$this->Port = $pServerDetails['smtpport'];
        $this->SMTPSecure = $pServerDetails['smtptype'];
        $this->SMTPAutoTLS = false;

		if (($pEmailReplyToName != '') && ($pEmailReplyToAddress != ''))
		{
			$this->FromName = $pEmailReplyToName;
		}
		else
		{
			$this->FromName = $pServerDetails['smtpsystemfromname'];
		}

    	$this->From = $pServerDetails['smtpsystemfromaddress'];


		$this->ClearReplyTos();
    	if (($pEmailReplyToName != '') && ($pEmailReplyToAddress != ''))
		{
			$this->AddReplyTo($pEmailReplyToAddress, $pEmailReplyToName);
		}
		else
		{
			$this->AddReplyTo($pServerDetails['smtpsystemreplytoaddress'], $pServerDetails['smtpsystemreplytoname']);
		}

		if ($pServerDetails['smtpauth'] == '1')
		{
    		$this->SMTPAuth = true;
    		$this->Username = $pServerDetails['smtpauthusername'];
    		$this->Password = base64_decode($pServerDetails['smtpauthpassword']);
		}

        $result = '';

		// if no smtp server address has been assigned don't attempt to send the email
        if ($this->Host == '')
        {
            return $result;
        }

        $this->SetLanguage($pLocale);

        $message = $this->getEmailTemplate($pMessageRootPath, $pWebBrandCode, $pWebAppName, $pHomeURL, $pLocale, $pUserID, $pParamArray);
        $messageTitle = '';

        $htmlMessage = $message['html'];
        $plainTextMessage = $message['plain'];

        // get the first line of the plain text message as the message title
        $lineEndingsArray= array("\r\n", "\r");
        $plainTextMessage = str_replace($lineEndingsArray, "\n", $plainTextMessage);
        $tagPos = stripos($plainTextMessage, "\n");

        if ($tagPos > 0)
        {
            $messageTitle = substr($plainTextMessage, 0, $tagPos);
            $plainTextMessage = substr($plainTextMessage, $tagPos + 1);
        }

        // process the html message
        if ($htmlMessage != '')
        {
            // find the message title
            $tagPos = stripos($htmlMessage, '<title>');
            if ($tagPos > 0)
            {
                 $tag = substr($htmlMessage, $tagPos + 7);
                 $tagPos = stripos($tag, '</title>');
                 $messageTitle = trim(substr($tag, 0, $tagPos));
            }
            $this->AltBody = $plainTextMessage;

            $this->MsgHTML($htmlMessage, $message['templateparentpath']);
        }
        else
        {
            $this->Body = $plainTextMessage;
        }

        $this->Subject = $messageTitle;

        // split the email address and email name as we may be sending to multiple recipients
        $emailAddressArray = explode(';', $pEmailAddress);
        $emailNameArray = explode(';', $pEmailName);
        $itemCount = count($emailAddressArray);
        $logEntryDetail = '';

        for ($i = 0; $i < $itemCount; $i++)
        {
            $emailAddress = (isset($emailAddressArray[$i]) ? trim($emailAddressArray[$i]) : '');
            $emailName = (isset($emailNameArray[$i]) ? trim($emailNameArray[$i]) : '');

            if ($emailAddress != '')
            {
                if ($i == 0)
                {
                    $this->AddAddress($emailAddress, $emailName);
                    $logEntryDetail = 'To: ' . $emailName . '<' . $emailAddress . '>';
                }
                else
                {
                    $this->AddCC($emailAddress, $emailName);
                    $logEntryDetail .= "\nCC: " . $emailName . '<' . $emailAddress . '>';
                }
            }
        }

        // add the bcc address if it has been provided
        if ($pBCCEmailAddress != '')
        {
			// split the email address and email name as we may be sending to multiple recipients
			$emailAddressArray = explode(';', $pBCCEmailAddress);
			$emailNameArray = explode(';', $pBCCEmailName);
			$itemCount = count($emailAddressArray);
			for ($i = 0; $i < $itemCount; $i++)
			{
					$emailAddress = trim($emailAddressArray[$i]);
					if($emailNameArray != '')
					{
						$emailName = trim($emailNameArray[$i]);
					}
				if ($emailAddress != '')
				{
					$this->AddBCC($emailAddress, $emailName);
					$logEntryDetail .= "\nBCC: " . $emailName . '<' . $emailAddress . '>';
				}
			}
        }

        $logEntryDetail .= "\nSubject: " . $messageTitle . "\n";

        // increase the standard php timeout
        UtilsObj::resetPHPScriptTimeout(60);

        $resultArray = $this->taopixGetEmailContents();
        $resultArray[] = $messageTitle;

        return $resultArray;
	}

	function MsgHTML($pMessage, $pBasedir = '', $pAdvanced = false)
	{
		$cidHash = array();

		preg_match_all("/(src|background)=\"(.*)\"/Ui", $pMessage, $images);

        if(isset($images[2]))
        {
            foreach($images[2] as $imgindex => $url)
            {

				// we need to handle URLs containing parent dir traversal (..) this is due to PHPMailer ignoring those.
				// we also have to embed the image as PHPMailer would try to calculate the path again corrupting the path
				// we have already worked out.
				if (strpos($url, '..') !== false)
				{
					if (strlen($pBasedir) > 1 && '/' != substr($pBasedir, -1))
					{
                        $pBasedir .= '/';
                    }

					$filename = basename($url);
					$cid = hash('sha256', $url) . '@taopixmailer.0';
					$path = realpath($pBasedir . $url);

                    if ($this->addEmbeddedImage($path, $cid, $filename, 'base64', static::_mime_types((string) static::mb_pathinfo($filename, PATHINFO_EXTENSION))))
                    {
                        $pMessage = preg_replace('/' . $images[1][$imgindex] . '=["\']' . preg_quote($url, '/') . '["\']/Ui',
                            $images[1][$imgindex] . '="cid:' . $cid . '"', $pMessage);
                    }

					continue;
				}

				// if "cid:" is found at the begining of the string then call function to embed image from BLOB data
				if (strpos($url, 'cid:') === 0)
				{
					$valuesArray = explode(":",$url);
					$idCode = $valuesArray[1];
					$idType = $valuesArray[2];
					$srcID = $idCode;
					$error = false;

					// check to make sure we have not already processed the image based off of idCode
					if (! $this->cidExists($idCode))
					{
						$mimeType = '';
						// If the type is project thumbnail execute a request to the image sever to get the data.
						if (($idType === 'projectthumbnailonline') || ($idType === 'projectthumbnaildesktop'))
						{
							if ($idType === 'projectthumbnaildesktop')
							{
								$getProjectThumbnailResult = self::getDesktopProjectThumbnail($idCode);

								if ($getProjectThumbnailResult['error'] === '')
								{
									$data = $getProjectThumbnailResult['data'];
									$mimeType = 'image/jpeg';
								}
							}
							else
							{
								$getProjectThumbnailResult = self::getOnlineProjectThumbnail($idCode);

								if (array_key_exists($idCode, $getProjectThumbnailResult))
								{
									$data = base64_decode($getProjectThumbnailResult[$idCode]['thumbnail']['data']);
									$mimeType = $getProjectThumbnailResult[$idCode]['thumbnail']['mimetype'];
								}
							}
						}
						else
						{
							// Load the product asset.
							$assetData = self::getEmbededAsset($idCode, $idType);

							// Check if a file has been found.
							if ($assetData['data'] !== '')
							{
								$data = $assetData['data'];
								$srcID = $assetData['uniqueid'];
								$mimeType = $assetData['mimetype'];
							}
							else
							{
								$error === true;
							}
						}

						if (! $error)
						{
							// Hash the blob data to see if we have this exact image already.
							$hash = hash('sha256', $data);
							$existingHashCid = array_search($hash, $cidHash);

							if ($existingHashCid === false)
							{
								// If we do not have an asset with this image add it.
								$this->addStringEmbeddedImage($data, $srcID, 'embed' . $imgindex, 'base64', $mimeType);
								$cidHash[$srcID] = $hash;
							}
							else
							{
								// Update the cid for the image/background we are looking at to be the cid for the image we attached previously.
								// Prevents attaching the same image twice, even if the  equested code is different.
								$srcID = $existingHashCid;
							}

							// Remove the type from the string and correct the id if it has changed.
							$pMessage = preg_replace("/cid:" . $idCode . ":" . $idType ."/Ui", "cid:" . $srcID, $pMessage);
						}
					}
				}
			}
		}

		return parent::MsgHTML($pMessage, $pBasedir, $pAdvanced);
	}

	function taopixGetEmailContents() {
    try {
      if ((count($this->to) + count($this->cc) + count($this->bcc)) < 1) {
        throw new phpmailerException($this->Lang('provide_address'), self::STOP_CRITICAL);
      }

      // Set whether the message is multipart/alternative
      if(!empty($this->AltBody)) {
        $this->ContentType = 'multipart/alternative';
      }

      $this->error_count = 0; // reset errors
      $this->SetMessageType();
      $body = $this->CreateBody();
      $header = $this->CreateHeader();


      if (empty($this->Body)) {
        throw new phpmailerException($this->Lang('empty_message'), self::STOP_CRITICAL);
      }

      // digitally sign with DKIM if enabled
      if ($this->DKIM_domain && $this->DKIM_private) {
        $header_dkim = $this->DKIM_Add($header,$this->Subject,$body);
        $header = str_replace("\r\n","\n",$header_dkim) . $header;
      }

      $resultArray = array($header, $body);
      return $resultArray;

    }
    catch (phpmailerException $e) {
      $this->SetError($e->getMessage());
      if ($this->exceptions) {
        throw $e;
      }
      error_log($e->getMessage());
      return false;
    }
  }


  function taopixSmtpSend($header, $body) {
    $bad_rcpt = array();
    if(!$this->SmtpConnect()) {
      return array('result' => 1, 'resultParam' => 'en smtp_connect_failed');
      //throw new phpmailerException($this->Lang('smtp_connect_failed'), self::STOP_CRITICAL);
    }
    $smtp_from = ($this->Sender == '') ? $this->From : $this->Sender;
    if(!$this->smtp->Mail($smtp_from)) {
      return array('result' => 1, 'resultParam' => 'en from_failed');
      //throw new phpmailerException($this->Lang('from_failed') . $smtp_from, self::STOP_CRITICAL);
    }

    // Attempt to send attach all recipients
    foreach($this->to as $to) {
      if (!$this->smtp->Recipient($to[0])) {
        $bad_rcpt[] = $to[0];
        // implement call back function if it exists
        $isSent = 0;
        $this->doCallback($isSent,$to[0],'','',$this->Subject,$body, $smtp_from, []);
      } else {
        // implement call back function if it exists
        $isSent = 1;
        $this->doCallback($isSent,$to[0],'','',$this->Subject,$body, $smtp_from, []);
      }
    }
    foreach($this->cc as $cc) {
      if (!$this->smtp->Recipient($cc[0])) {
        $bad_rcpt[] = $cc[0];
        // implement call back function if it exists
        $isSent = 0;
        $this->doCallback($isSent,'',$cc[0],'',$this->Subject,$body, $smtp_from, []);
      } else {
        // implement call back function if it exists
        $isSent = 1;
        $this->doCallback($isSent,'',$cc[0],'',$this->Subject,$body, $smtp_from, []);
      }
    }
    foreach($this->bcc as $bcc) {
      if (!$this->smtp->Recipient($bcc[0])) {
        $bad_rcpt[] = $bcc[0];
        // implement call back function if it exists
        $isSent = 0;
        $this->doCallback($isSent,'','',$bcc[0],$this->Subject,$body, $smtp_from, []);
      } else {
        // implement call back function if it exists
        $isSent = 1;
        $this->doCallback($isSent,'','',$bcc[0],$this->Subject,$body, $smtp_from, []);
      }
    }

    if (count($bad_rcpt) > 0 ) { //Create error message for any bad addresses
      $badaddresses = implode(', ', $bad_rcpt);
      return array('result' => 1, 'resultParam' => 'en recipients_failed');
      //throw new phpmailerException($this->Lang('recipients_failed') . $badaddresses);
    }
    if(!$this->smtp->Data($header . $body)) {
      return array('result' => 1, 'resultParam' => 'en data_not_accepted');
      //throw new phpmailerException($this->Lang('data_not_accepted'), self::STOP_CRITICAL);
    }
    if($this->SMTPKeepAlive == true) {
      $this->smtp->Reset();
    }
    return array('result' => 2, 'resultParam' => '');
  }

  function taopixSendEmailContents($header, $body, $messageTitle, $pLocale, $pEmailName, $pEmailAddress, $pBCCEmailName, $pBCCEmailAddress, $pWebBrandCode, $serverDetails)
  {
  	$resultArray = array();
  	$result = 2;
  	$resultParam = '';

  	try
  	{
		$this->Host = $serverDetails['smtpaddress'];
		$this->Port  = $serverDetails['smtpport'];
        $this->SMTPSecure = $serverDetails['smtptype'];
        $this->SMTPAutoTLS = false;
		$this->FromName = $serverDetails['smtpsystemfromname'];
		$this->From = $serverDetails['smtpsystemfromaddress'];
		$this->ClearReplyTos();
		$this->AddReplyTo($serverDetails['smtpsystemreplytoaddress'], $serverDetails['smtpsystemreplytoname']);

		if ($serverDetails['smtpauth'] == '1')
		{
	   		$this->SMTPAuth = true;
	   		$this->Username = $serverDetails['smtpauthusername'];
	   		$this->Password = base64_decode($serverDetails['smtpauthpassword']);
		} elseif(2 === (int)$serverDetails['smtpauth']) {
			// This is an oauth request.
			if (0 === $serverDetails['oauthprovider'] || 0 === $serverDetails['oauthtoken']) {
				throw new phpmailerException('Invalid configuration missing OAuth provider or Token');
			}
			$providerContainer = new TaopixOAuthProvider($serverDetails['oauthprovider'], DatabaseObj::getGlobalDBConnection());
			$tokenManager = new TaopixOAuthRefreshToken(DatabaseObj::getGlobalDBConnection());
			$token = $tokenManager->findByParams(['id' => $serverDetails['oauthtoken']]);

			$oAuth = new OAuth([
				'provider' => $providerContainer->getProvider(),
				'userName' => $serverDetails['smtpsystemfromaddress'],
				'clientSecret' => base64_decode($providerContainer->getValue('clientsecret')),
				'clientId' => $providerContainer->getValue('clientid'),
				'refreshToken' => base64_decode($token['refreshtoken']),
			]);
			$this->SMTPAuth = true;
			$this->SMTPAutoTLS = true;
			$this->AuthType = 'XOAUTH2';
			$this->setOAuth($oAuth);
		}

	  	$this->SetLanguage($pLocale, '../libs/PHPMailer_v5.1/language/');
	  	$this->Subject = $messageTitle;

	  	 // split the email address and email name as we may be sending to multiple recipients
	    $emailAddressArray = explode(';', $pEmailAddress);
	    $emailNameArray = explode(';', $pEmailName);
	    $itemCount = count($emailAddressArray);
	    $logEntryDetail = '';
	    for ($i = 0; $i < $itemCount; $i++)
	    {
	        $emailAddress = (isset($emailAddressArray[$i]) ? trim($emailAddressArray[$i]) : '');
	        $emailName = (isset($emailNameArray[$i]) ? trim($emailNameArray[$i]) : '');

            if ($emailAddress != '')
            {
                if ($i == 0)
                {
                    $this->AddAddress($emailAddress, $emailName);
                    $logEntryDetail = 'To: ' . $emailName . '<' . $emailAddress . '>';
                }
                else
                {
                    $this->AddCC($emailAddress, $emailName);
                    $logEntryDetail .= "\nCC: " . $emailName . '<' . $emailAddress . '>';
                }
            }
        }

        // add the bcc address if it has been provided
        if ($pBCCEmailAddress != '')
        {
			// split the email address and email name as we may be sending to multiple recipients
			$emailAddressArray = explode(';', $pBCCEmailAddress);
			$emailNameArray = explode(';', $pBCCEmailName);
			$itemCount = count($emailAddressArray);
			for ($i = 0; $i < $itemCount; $i++)
			{
					$emailAddress = trim($emailAddressArray[$i]);
					if ($emailNameArray != '')
					{
						$emailName = trim($emailNameArray[$i]);
					}
				if ($emailAddress != '')
				{
					$this->AddBCC($emailAddress, $emailName);
					$logEntryDetail .= "\nBCC: " . $emailName . '<' . $emailAddress . '>';
				}
			}
        }

  		// Choose the mailer and send through it
		if ($this->Mailer == 'sendmail')
		{
			$sendResult = $this->SendmailSend($header, $body);
			$sendResult = array('result' => 2, 'resultParam' => '');
		}
		else
		{
			$sendResult = $this->taopixSmtpSend($header, $body);
		}

		$result = $sendResult['result'];
		$resultParam = $sendResult['resultParam'];
    }
    catch (phpmailerException $e)
    {
    	$this->SetError($e->getMessage());
      	$resultParam = 'en ' . $e->getMessage();
      	$result = 1;
    }
    return array('result' => $result, 'resultParam' => $resultParam);
  }

	static function getEmbededAsset($pIDCode, $pIDType)
	{
		global $ac_config;

		$fileContent = '';
		$mimetype = '';

		$uniqueID = md5($pIDCode);
		$folderPath = UtilsObj::correctPath($ac_config['CONTROLCENTREPREVIEWSPATH'], DIRECTORY_SEPARATOR, true) . $pIDType . DIRECTORY_SEPARATOR . $uniqueID;

        if (is_dir($folderPath))
		{
			$files = array_diff(scandir($folderPath), array('..', '.'));

			if (count($files) > 0)
			{
				$fileContent = file_get_contents($folderPath . DIRECTORY_SEPARATOR . $files[2]);

				$finfo = finfo_open(FILEINFO_MIME_TYPE);
				$mimetype = finfo_buffer($finfo, $fileContent);
				finfo_close($finfo);
			}
		}

		return ['data' => $fileContent, 'uniqueid' => $uniqueID, 'mimetype' => $mimetype];
	}

	/**
	 * Gets the base64 version of the project thumbnail from the image server.
	 *
	 * @param string $pProjectRef Ref of the project to get thumbnail for.
	 * @return array Array containing the thumbnail base64 data and mimietype.
	 */
	static function getOnlineProjectThumbnail($pProjectRef)
	{
		require_once(__DIR__ . '/../libs/internal/curl/Curl.php');

		$returnArray = UtilsObj::getReturnArray();
		$thumbURL = UtilsObj::getProjectThumbnailAPIPath('displayThumbnail', ['projectreflist' => [$pProjectRef], 'displaymode' => 0]);
		$cURLResult = CurlObj::get($thumbURL, TPX_CURL_RETRY, TPX_CURL_TIMEOUT);

		if ($cURLResult['error'] === '')
		{
			$returnArray = json_decode($cURLResult['data'], true);
		}

		return $returnArray;
	}

	/**
	 * Gets the binary jpeg data of the project thumbnail from the control centre URL
	 *
	 * @param string Ref of the project to get the thumbnail for
	 * @return array Array containing the jpeg data and any curl errors
	 */
	static function getDesktopProjectThumbnail($pProjectRef)
	{
		require_once(__DIR__ . '/../libs/internal/curl/Curl.php');

		$thumbURL = UtilsObj::buildDesktopProjectThumbnailWebURL($pProjectRef);

		$cURLResult = CurlObj::get($thumbURL, TPX_CURL_RETRY, TPX_CURL_TIMEOUT);

		return $cURLResult;
	}
}

?>
