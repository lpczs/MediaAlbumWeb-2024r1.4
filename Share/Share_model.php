<?php
require_once('../Utils/UtilsDatabase.php');
require_once('../Order/Order_model.php');
require_once('../Utils/UtilsLocalization.php');
require_once('../libs/internal/curl/Curl.php');
require_once('../OnlineAPI/OnlineAPI_model.php');
require_once('../AppAPI/AppAPI_model.php');

class Share_model
{
    static function login()
    {
        global $gSession;

        $resultArray = Array();
        $result = '';

        $pSource = UtilsObj::getPOSTParam('source');
        $pUniqueRef = UtilsObj::getPOSTParam('ref2');
        $pOrderItemId = UtilsObj::getPOSTParam('orderitemid');
        $pPassword = UtilsObj::getPOSTParam('password');
        $pWebBrandCode = UtilsObj::getPOSTParam('webbrandcode');
        $isMobile = UtilsObj::getPOSTParam('mobile');
		$passwordFormat = UtilsObj::getPOSTParam('format', TPX_PASSWORDFORMAT_MD5);
		$passwordValid = false;

        $gSession['ismobile'] = ($isMobile == 'true') ? true : false;

        if ($pSource && $pUniqueRef && $pOrderItemId && $pPassword)
        {
            // check if passwords match
            $sharedInfo = self::isShared($pOrderItemId, $pUniqueRef);
            $previewPassword = $sharedInfo['password'];

			// check password is valid based on password format
			$verifyPasswordResult = AuthenticateObj::verifyPassword($pPassword, $previewPassword, $passwordFormat);
			$passwordValid = $verifyPasswordResult['data']['passwordvalid'];

            if ($passwordValid)
            {
                $resultArray = self::previewPrepareFiles($pSource, $pUniqueRef, $pOrderItemId);

                if (!empty($resultArray['pages']) || $resultArray['result'] != '' || $resultArray['ordersource'] == TPX_SOURCE_ONLINE)
                {
                    if (($resultArray['productionstatus'] == TPX_ITEM_STATUS_AWAITING_FILES) && ($resultArray['canupload'] == 1) &&
                            ($resultArray['ordersource'] == TPX_SOURCE_ONLINE))
                    {
                        Share_view::previewNotAvailable();
                    }
                    else
                    {
                        Share_view::preview($resultArray, TPX_PREVIEW_SHARED, true);
                    }
                }
                else
                {
                    Share_view::previewNotFound(true);
                }
            }
            else
            {
                $result = "str_ErrorPreviewPasswordWrong";
                Share_view::displayLogin($pWebBrandCode, $pUniqueRef, $pOrderItemId, $pSource, $result);
            }
        }
        else
        {
            $result = "str_ErrorConnectFailure";
            Share_view::displayLogin($pWebBrandCode, $pUniqueRef, $pOrderItemId, $pSource, $result);
        }
    }

    static function generateHash($pOrderItemID)
    {
        global $gSession;

        $rand = rand();
        $userId = $gSession['userid'];
        $date = time();

        $md5Hash = md5($userId . '' . $pOrderItemID . '' . $date . '' . $rand);

        return $md5Hash;
    }

    /**
     * @param $pData array with properties: 'projectref'
     * @return array with properties: 'shareurl'
     */
    static function getShareOnlineProjectURL($pData)
    {
        global $gSession;

        $projectRef = $pData['projectref'];

        // Online passes 'webbrandcode' -- controlcentre accesses $gSession
        $webBrandCode = array_key_exists('webbrandcode',$pData) ? $pData['webbrandcode'] : $gSession['webbrandcode'];
        $shareMethod = 'SHAREONLINE_0';

        require_once('../Utils/UtilsDatabase.php');
        $webBrandArray = DatabaseObj::getBrandingFromCode($webBrandCode);
        $shareLinkDomain = UtilsObj::getBrandedDisplayUrl($webBrandCode);

        if ($webBrandArray['sharehidebranding'] === 1)
        {
            $shareMethod = 'SHAREONLINE_1';
            $shareLinkDomain = $webBrandArray['previewdomainurl'];
        }

        $shareRecord = self::getExistingOnlineShareRecord($projectRef, $shareMethod);

        if ($shareRecord['hash'] === '')
        {
            $md5Hash = self::generateHash($projectRef);
            $shareRecord = [
                'productcodeorprojectref' => $projectRef ,
                'webbrandcode' => $webBrandCode,
                'hash' => $md5Hash,
                'sharemethod' => $shareMethod,
                'sharedaction' => 'SHAREONLINEPROJECT'
            ];
            self::insertSharedItemsRecord($shareRecord);
        }
        else
        {
            $md5Hash = $shareRecord['hash'];
        }

        // Populate the web brand session to enable access to all related branding on the session
        AuthenticateObj::setSessionWebBrand($shareRecord['webbrandcode']);

        $shareURL = $shareLinkDomain . '?fsaction=OnlineAPI.previewSharedProject&ref2=' . $md5Hash;

        return [
            'shareurl' => $shareURL,
            'brandcode' => $webBrandCode
        ];
    }

    /**
     * getExistingOnlineShareRecord
     * -- if an existing record exists in the database for the current project ref then return the string otherwise
     *    return null
     *
     * @param $projectRef string
     * @param $shareMethod string
     * @return string|null
     */
    static function getExistingOnlineShareRecord($projectRef,$shareMethod)
    {

        $dbObj = DatabaseObj::getGlobalDBConnection();
        $resultArray = [
            'result' => TPX_ONLINE_ERROR_NONE,
            'resultparam' => '',
            'hash' => ''
        ];

        if ($dbObj)
        {
            if ($stmt = $dbObj->prepare('SELECT `uniqueid`,`webbrandcode` FROM `SHAREDITEMS` WHERE (`method` = ? AND `productcode` = ?)'))
            {
                if ($stmt->bind_param('ss', $shareMethod, $projectRef))
                {
                    if ($stmt->bind_result($uniqueid, $webBrandCode))
                    {
                        if ($stmt->execute())
                        {
                            if ($stmt->fetch())
                            {
                                $resultArray['hash'] = $uniqueid;
                                $resultArray['webbrandcode'] = $webBrandCode;
                            }
                        }
                        else
                        {
                            $resultArray['result'] = 'str_DatabaseError';
                            $resultArray['resultparam'] = 'share showPreview execute ' . $dbObj->error;
                        }
                    }
                    else
                    {
                        $resultArray['result'] = 'str_DatabaseError';
                        $resultArray['resultparam'] = 'share showPreview bindresult ' . $dbObj->error;
                    }
                }
                else
                {
                    $resultArray['result']= 'str_DatabaseError';
                    $resultArray['resultparam']  = 'share showPreview bindresult ' . $dbObj->error;
                }
            }
            $stmt->free_result();
            $stmt->close();
            $stmt = null;
        }
        else
        {
            $resultArray['result']  = 'str_DatabaseError';
            $resultArray['resultparam'] = 'share showPreview bind ' . $dbObj->error;
        }

        $dbObj->close();

        return $resultArray;
    }

    static function getProjectRefUsingHash($hash)
    {
        $dbObj = DatabaseObj::getGlobalDBConnection();
        $resultArray = [
            'result' => TPX_ONLINE_ERROR_NONE,
            'resultparam' => '',
            'projectRef' => '',
            'brandcode' => ''
        ];

        if ($dbObj)
        {
            if ($stmt = $dbObj->prepare('SELECT `productcode`, `webbrandcode`, `method` FROM `SHAREDITEMS` WHERE `uniqueid` = ?'))
            {
                if ($stmt->bind_param('s', $hash))
                {
                    if ($stmt->bind_result($productCode, $webBrandCode, $method))
                    {
                        if ($stmt->execute())
                        {
                            if ($stmt->fetch())
                            {
                                $resultArray['projectref'] = $productCode;
                                $resultArray['brandcode'] = $webBrandCode;
                                $resultArray['method'] = $method;
                            }
                            else
                            {
                                // There were no results using this hash
                                $resultArray['result'] = TPX_ONLINE_ERROR_PROJECTDOESNOTEXIST;
                                $resultArray['resultparam'] = 'getProjectRefUsingHash() fetch' . $dbObj->error;
                            }
                        }
                        else
                        {
                            $resultArray['result'] = 'str_DatabaseError';
                            $resultArray['resultparam'] = 'getProjectRefUsingHash() execute ' . $dbObj->error;
                        }
                    }
                    else
                    {
                        $resultArray['result'] = 'str_DatabaseError';
                        $resultArray['resultparam'] = 'getProjectRefUsingHash() bindresult ' . $dbObj->error;
                    }
                }
                else
                {
                    $resultArray['result']= 'str_DatabaseError';
                    $resultArray['resultparam']  = 'getProjectRefUsingHash() bindparam' . $dbObj->error;
                }
            }
            $stmt->free_result();
            $stmt->close();
            $stmt = null;
        }
        else
        {
            $resultArray['result']  = 'str_DatabaseError';
            $resultArray['resultparam'] = 'getProjectRefUsingHash() prepare' . $dbObj->error;
        }

        $dbObj->close();
        return $resultArray;
    }

    /**
     * @param Array $recordData Array of parameters
     *        Required Properties:      hash
     *        Optional Properties:      sharedaction, sharemethod, userid, orderid, productcode, brandcode, sharedwith
     *                                  previewpassword, productcodeorprojectif
     * @return bool
     */
    static function insertSharedItemsRecord($pRecordData)
    {
        // OPTIONAL $recordData properties
        $sharedAction = UtilsObj::getArrayParam($pRecordData,'sharedaction','');
        $shareMethod = UtilsObj::getArrayParam($pRecordData,'sharemethod','');
        $userID = UtilsObj::getArrayParam($pRecordData,'userid',0);
        $orderID  = UtilsObj::getArrayParam($pRecordData,'orderid',0);
        $orderItemID  = UtilsObj::getArrayParam($pRecordData,'orderitemid',0);
        $productCodeOrProjectRef  = UtilsObj::getArrayParam($pRecordData,'productcodeorprojectref','');
        $sharedWith  = UtilsObj::getArrayParam($pRecordData,'sharedwith','');
        $previewPassword  = UtilsObj::getArrayParam($pRecordData,'previewpassword','');
        $active  = UtilsObj::getArrayParam($pRecordData,'$active',1);

        // REQUIRED $recordData properties
        $md5Hash = $pRecordData['hash'];
        $brandCode  = $pRecordData['webbrandcode'];


        $resultArray = [];
        $result = '';
        $resultParam = '';

        $dbObj = DatabaseObj::getGlobalDBConnection();
        if ($dbObj)
        {
            if ($stmt = $dbObj->prepare('INSERT INTO `SHAREDITEMS` (`datecreated`, `datemodified`, `action`, 
                        `method`, `uniqueid`, `userid`, `orderitemid`, `orderid`, `productcode`, `webbrandcode`,
						`recipient`, `password`, `active`) VALUES (now(), now(), ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'))
            {
                if ($stmt->bind_param('sssiiissssi', $sharedAction, $shareMethod, $md5Hash,
                    $userID, $orderItemID, $orderID, $productCodeOrProjectRef, $brandCode, $sharedWith,
                    $previewPassword, $active))
                {
                    if (!$stmt->execute())
                    {
                        $result = 'str_DatabaseError';
                        $resultParam = 'insertSharedItemsRecord() execute ' . $dbObj->error;
                    }
                }
                else
                {
                    $result = 'str_DatabaseError';
                    $resultParam = 'insertSharedItemsRecord() bindparam ' . $dbObj->error;
                }
                $stmt->free_result();
                $stmt->close();
                $stmt = null;
            }
            else
            {
                $result = 'str_DatabaseError';
                $resultParam = 'insertSharedItemsRecord() prepare ' . $dbObj->error;
            }

            $dbObj->close();
        }

        $resultArray['result'] = $result;
        $resultArray['resultparam'] = $resultParam;

        self::removeOldOnlineSharedProjectRecords();

        return $resultArray;
    }

    static function removeOldOnlineSharedProjectRecords()
    {
        global $ac_config;
        
        // delete all shared online project records older than the set config value
        $recordIDArray = array();
        $recordCount = 0;
        $recordID = 0;

        if (! array_key_exists('SHAREDONLINEPROJECTLINKLIFESPAN', $ac_config))
        {
            $ac_config['SHAREDONLINEPROJECTLINKLIFESPAN'] = 90;
        }

        $dbObj = DatabaseObj::getGlobalDBConnection();
        if ($dbObj)
        {
            // find all the records which have expired
            $sqlStatement = "SELECT `id` FROM `SHAREDITEMS` WHERE (`datecreated` <= now() - INTERVAL ? DAY) AND `action` = 'SHAREONLINEPROJECT' order by id desc";

            if ($stmt = $dbObj->prepare($sqlStatement))
            {
                if ($stmt->bind_param('i', $ac_config['SHAREDONLINEPROJECTLINKLIFESPAN']))
                {
                    if ($stmt->execute())
                    {
                        if ($stmt->store_result())
                        {
                            if ($stmt->num_rows > 0)
                            {
                                if ($stmt->bind_result($recordID))
                                {
                                    while ($stmt->fetch())
                                    {
                                        $recordIDArray[] = $recordID;
                                        $recordCount++;
                                    }
                                }
                                else
                                {
                                    // could not bind result
                                    $result = 'str_DatabaseError';
                                    $resultParam = __FUNCTION__ . ' bind result: ' . $dbObj->error;
                                }
                            }
                        }
                        else
                        {
                            // could not store result
                            $result = 'str_DatabaseError';
                            $resultParam = __FUNCTION__ . ' store result: ' . $dbObj->error;
                        }
                    }
                    else
                    {
                        // could not execute statement
                        $result = 'str_DatabaseError';
                        $resultParam = __FUNCTION__ . ' execute: ' . $dbObj->error;
                    }
                }

                $stmt->free_result();
                $stmt->close();
                $stmt = null;
            }
            else
            {
                // could not prepare statement
                $result = 'str_DatabaseError';
                $resultParam = __FUNCTION__ . ' prepare: ' . $dbObj->error;
            }

            // delete all the records which have expired
            if ($recordCount > 0)
            {
                $recordIDs = implode(',', $recordIDArray);

                if ($stmt = $dbObj->prepare('DELETE FROM `SHAREDITEMS` WHERE `id` in (' . $recordIDs  . ')'))
                {
                    if (!$stmt->execute())
                    {
                        // could not execute statement
                        $result = 'str_DatabaseError';
                        $resultParam = __FUNCTION__ . ' execute: ' . $dbObj->error;
                    }

                    $stmt->close();
                }
                else
                {
                    // could not prepare statement
                    $result = 'str_DatabaseError';
                    $resultParam = __FUNCTION__ . ' prepare: ' . $dbObj->error;
                }
            }

            $dbObj->close();
        }
    }

    static function share($pUserId, $pOrderItemId, $pShareMethod, $pSharedAction, $pSharedWith, $pPreviewPassword, $pWebBrandDisplayURL = '', $pPasswordFormat = TPX_PASSWORDFORMAT_MD5)
    {
        global $gConstants;

		require_once('../Utils/UtilsLocalization.php');

        $resultArray = Array();
        $result = '';
        $resultParam = '';

        $productCode = '';
        $groupCode = '';
        $orderID = 0;
        $orderNumber = 0;
        $brandCode = '';
        $sharedUrl = '';
        $active = 1;

        $dbObj = DatabaseObj::getGlobalDBConnection();
        if ($dbObj)
        {
            if ($stmt = $dbObj->prepare('SELECT oi.productcode, oh.groupcode, oh.id, oh.ordernumber, oh.webbrandcode
        								FROM ORDERITEMS oi JOIN ORDERHEADER oh ON oh.id = oi.orderid
        								WHERE oi.id = ?'))
            {
                if ($stmt->bind_param('i', $pOrderItemId))
                {
                    if ($stmt->execute())
                    {
                        if ($stmt->store_result())
                        {
                            if ($stmt->num_rows > 0)
                            {
                                if ($stmt->bind_result($productCode, $groupCode, $orderID, $orderNumber, $brandCode))
                                {
                                    if (!$stmt->fetch())
                                    {
                                        $result = 'str_DatabaseError';
                                        $resultParam = 'share select fetch ' . $dbObj->error;
                                    }
                                }
                                else
                                {
                                    $result = 'str_DatabaseError';
                                    $resultParam = 'share select bind result ' . $dbObj->error;
                                }
                            }
                        }
                        else
                        {
                            $result = 'str_DatabaseError';
                            $resultParam = 'share select store results ' . $dbObj->error;
                        }
                    }
                    else
                    {
                        $result = 'str_DatabaseError';
                        $resultParam = 'share select execute ' . $dbObj->error;
                    }
                }
                else
                {
                    $result = 'str_DatabaseError';
                    $resultParam = 'share select bind ' . $dbObj->error;
                }
                $stmt->free_result();
                $stmt->close();
                $stmt = null;
            }
            else
            {
                $result = 'str_DatabaseError';
                $resultParam = 'share select prepare ' . $dbObj->error;
            }

            if ($result == '')
            {
                $md5Hash = self::generateHash($pOrderItemId);

				if ($pPreviewPassword != '')
				{
					// calculate preview password hash based on if the page is secure or not
					$previewPasswordHash = AuthenticateObj::generatePasswordHash($pPreviewPassword, $pPasswordFormat);

					if ($previewPasswordHash['result'] == '')
					{
						$pPreviewPassword = $previewPasswordHash['data'];
					}
					else
					{
						$result = $previewPasswordHash['result'];
						$resultParam = $previewPasswordHash['resultparam'];
					}
				}

				if ($result == '')
				{
                    $recordData = [
                        'sharedaction' => $pSharedAction,
                        'sharemethod' => $pShareMethod,
                        'hash' => $md5Hash,
                        'userid' => $pUserId,
                        'orderitemid' => $pOrderItemId,
                        'oderid' => $orderID,
                        'productcodeorprojectref' => $productCode,
                        'webbrandcode' => $brandCode,
                        'sharedwith' => $pSharedWith,
                        'previewpassword' => $pPreviewPassword,
                        'active' => $active
                    ];
                    $insertResult = self::insertSharedItemsRecord($recordData);
                    $result = $insertResult['result'];
                    $resultParam = $insertResult['resultparam'];

				}
            }
            $dbObj->close();
        }
        else
        {
            $result = 'str_DatabaseError';
            $resultParam = 'share connect ' . $dbObj->error;
        }

        $sharedUrl = $pWebBrandDisplayURL . '?fsaction=Share.preview&ref2=' . $md5Hash;

        if (($gConstants['optionwscrp']) && (file_exists('../Customise/scripts/EDL_ExternalCustomerAccount.php')))
        {
            require_once('../Customise/scripts/EDL_ExternalCustomerAccount.php');

            if (method_exists('ExternalCustomerAccountObj', 'ssoSetHomeURL'))
            {
                $paramArray = array();
                $paramArray['ssotoken'] = '';
                $paramArray['ssoprivatedata'] = array();
                $paramArray['brandcode'] = $brandCode;
                $paramArray['url'] = $sharedUrl;

                // call the ssoSetHomeURL command so that the licensee can add any extra parameters they might need adding to the
                // share URL in the email
                $ssoSharedUrl = ExternalCustomerAccountObj::ssoSetHomeURL($paramArray);

                // only use the sso url if one is returned
                if ($ssoSharedUrl != "")
                {
                    $sharedUrl = $ssoSharedUrl;
                }
            }
        }

        $resultArray['result'] = $result;
        $resultArray['resultparam'] = $resultParam;
        $resultArray['sharedurl'] = $sharedUrl;

        return $resultArray;
    }

    static function shareAddToAny()
    {
        global $gSession;

        $userId = $gSession['userid'];

        $orderItemID = isset($_POST['orderItemId']) ? $_POST['orderItemId'] : 0;
        $sharedMethod = isset($_POST['method']) ? $_POST['method'] : '';
        $previewPassword = isset($_POST['previewPassword']) ? $_POST['previewPassword'] : '';
		$passwordFormat = isset($_POST['format']) ? $_POST['format'] : TPX_PASSWORDFORMAT_MD5;

		// Retrieve the order item details and confirm the order item belongs to the user
		// requesting the share
		$orderItemArray = DatabaseObj::getOrderItemById((int) $orderItemID);
		if ($orderItemArray['result'] == '')
        {
			if ($userId == $orderItemArray['userid'])
            {
				$resultArray = self::share($userId, $orderItemID, $sharedMethod, 'SHARE', '', $previewPassword, $gSession['webbranddisplayurl'], $passwordFormat);
				$result = $resultArray['sharedurl'];
            }
            else
            {
				$result = 'str_AccessDenied';
            }
        }
        else
        {
			$result = $orderItemArray['result'];
        }

        return $result;
    }

    static function shareByEmail()
    {
        global $gSession;

        // include the email creation module
        require_once('../Utils/UtilsEmail.php');

        $result = '';
        $resultParam = '';
        $sharedUrl = Array();
        $sharedNames = Array();

        $userId = $gSession['userid'];
        $orderItemID = isset($_POST['orderItemId']) ? $_POST['orderItemId'] : '';
        $emailTitle = isset($_POST['title']) ? $_POST['title'] : '';
        $emailAddress = isset($_POST['recipients']) ? str_replace(',', ';', $_POST['recipients']) : '';
        $emailText = isset($_POST['message']) ? $_POST['message'] : '';
        $previewPassword = isset($_POST['previewPassword']) ? $_POST['previewPassword'] : '';
		$passwordFormat = isset($_POST['format']) ? $_POST['format'] : '';

        $orderItemIDArray = explode(',', $orderItemID);

        $emailAddressArray = explode(';', $emailAddress);
        $emailAddressCount = count($emailAddressArray);

        $userAccount = DatabaseObj::getUserAccountFromID($userId);
        $emailFromName = $userAccount['contactfirstname'] . ' ' . $userAccount['contactlastname'];
        $emailFromAddress = $userAccount['emailaddress'];

        for ($i = 0; $i < count($orderItemIDArray); $i++)
        {
            $orderItemID = $orderItemIDArray[$i];
            $orderItemArray = DatabaseObj::getOrderItemById($orderItemID * 1);
            if ($orderItemArray['result'] == '')
            {
                if ($userId == $orderItemArray['userid'])
                {
					for ($j = 0; $j < $emailAddressCount; $j++)
					{
                        //Remove any white space from the email
                        $emailAddressArray[$j] = str_replace(' ', '', $emailAddressArray[$j]);
                        
                        $resultArray = self::share($userId, $orderItemID, 'EMAIL', 'SHARE', $emailAddressArray[$j], $previewPassword,
							$gSession['webbranddisplayurl'], $passwordFormat);
						$sharedNames[$j] = $orderItemArray['projectname'];
						$sharedUrl[$j] = $resultArray['sharedurl'];

						if ($resultArray['result'] == '')
						{
							$emailObj = new TaopixMailer();
							$emailObj->sendTemplateEmail('customer_shared', $gSession['webbrandcode'], $gSession['webbrandapplicationname'],
								$gSession['webbranddisplayurl'], $gSession['browserlanguagecode'], '', $emailAddressArray[$j], '', '',
								$userId,
								Array(
									'orderitemid' => $orderItemID,
									'userid' => $userId,
									'sharedurl' => $sharedUrl[$j],
									'emailTitle' => $emailTitle,
									'emailText' => $emailText,
									'sharednames' => $sharedNames[$j],
									'targetuserid' => $userId
								), $emailFromName, $emailFromAddress
							);
						}
						else
						{
							$result .= $resultArray['result'];
							$resultParam .= $resultArray['resultparam'];
						}
					}
				}
				else
                {
                    $result .= 'str_AccessDenied';
                    $resultParam .= 'Order item does not belong to session user: user IDs do not match.';
                }
            }

            $result .= $orderItemArray['result'];
            $resultParam .= $orderItemArray['resultparam'];
        }

        $encodeArray = array('result' => $result, 'resultparam' => UtilsObj::encodeString($resultParam, true));

        echo json_encode($encodeArray);
    }

    static function mailTo()
    {
        global $gSession;

        // include the email creation module
        require_once('../Utils/UtilsEmail.php');

        $resultArray['result'] = '';
        $resultArray['resultparam'] = '';

        $userId = $gSession['userid'];
        $orderItemID = isset($_POST['orderItemId']) ? $_POST['orderItemId'] : '';
        $emailTitle = isset($_POST['title']) ? $_POST['title'] : '';
        $emailAddress = isset($_POST['recipients']) ? $_POST['recipients'] : '';
        $emailText = isset($_POST['message']) ? $_POST['message'] : '';
        $previewPassword = isset($_POST['previewPassword']) ? $_POST['previewPassword'] : '';
		$passwordFormat = isset($_POST['format']) ? $_POST['format'] : '';

        $orderItemArray = DatabaseObj::getOrderItemById($orderItemID * 1);
        if ($orderItemArray['result'] == '')
        {
			if ($userId == $orderItemArray['userid'])
			{
				$shareResultArray = self::share($userId, $orderItemID, 'EMAIL', 'SHARE', $emailAddress, $previewPassword,
					$gSession['webbranddisplayurl'], $passwordFormat);

				if ($shareResultArray['result'] == '')
				{
					$emailObj = new TaopixMailer();
					$resultMailTemplate = $emailObj->getEmailTemplate(
						'customer_shared',
						$gSession['webbrandcode'],
						$gSession['webbrandapplicationname'],
						$gSession['webbranddisplayurl'],
						$gSession['browserlanguagecode'],
						'',
						Array(
							'orderitemid' => $orderItemID,
							'userid' => $userId,
							'sharedurl' => $shareResultArray['sharedurl'],
							'emailTitle' => $emailTitle,
							'emailText' => $emailText,
							'sharednames' => $orderItemArray['projectname']
						)
					);
					//change html break point
					$body = str_replace(array('<br>', '<br/>', '<br />'), array("\n","\n","\n"), html_entity_decode($resultMailTemplate['plain']));
                    $resultArray['resultparam'] = 'mailto:' . $emailAddress . '?subject=' . rawurlencode($emailTitle) . '&body=' . rawurlencode($body);
				}
				else
				{
					$resultArray['result'] = $shareResultArray['result'];
                    $resultArray['resultparam'] = $shareResultArray['resultparam'];
				}
            }
            else
            {
				$resultArray['result'] = 'str_AccessDenied';
                $resultArray['resultparam'] = 'Order item does not belong to session user: user IDs do not match.';
            }
        }
        else
        {
            $resultArray['result'] = $orderItemArray['result'];
            $resultArray['resultparam'] = $orderItemArray['resultparam'];
        }

        echo json_encode($resultArray);
    }

    static function preview($pPreviewHash)
    {
        $resultArray = self::showPreview('SHARE', $pPreviewHash, 0);

        return $resultArray;
    }

    static function previewPrepareFiles($pSource, $pUniqueRef, $pOrderItemId)
    {
		global $gSession;

        $resultArray = Array();
        $result = '';
        $resultParam = '';
        $uploadRef = '';
        $shareRef = '';
        $sharedMethod = '';
        $recipients = '';
        $reorderAction = '';
        $pagesArray = Array();
        $previewPages = Array();
        $projectName = '';
        $productName = '';
        $previewType = 0;
        $previewCoverType = 0;
        $previewAutoFlip = 0;
        $previewThumbnailsView = 0;
        $previewThumbnails = 0;
        $macDownloadUrl = '';
        $win32DownloadUrl = '';
        $previewLicenseKey = '';
        $previewVersion = 0;
        $orderDateCreated = 0;
        $canOrder = true;
        $orderCancelled = false;
        $orderItemActive = TPX_ORDER_STATUS_IN_PROGRESS;
        $pageCount = 0;
        $orderSource = TPX_SOURCE_DESKTOP;
        $uploadDataType = TPX_UPLOAD_DATA_TYPE_RENDERED;
        $previewsOnline = 0;
        $externalPreviewURL = '';
        $orderID = 0;
        $productCode = '';
        $productType = 0;
        $brandCode = '';
        $canUpload = 0;
        $productionStatus = 0;
        $orderFromPreview = 3;
        $brandOrderfrompreview = 1;
        $shareByEmailMethod = 1;
		$tempOrder = 0;
		$origorderID = 0;
		$pageFlipSettings = array();
        $userId = $gSession['userid'];

        if ($pUniqueRef && $pSource)
        {
            $dbObj = DatabaseObj::getGlobalDBConnection();
            if ($dbObj)
            {
                // get uploadref for the shared item
                if ($pSource == 'CUSTOMER')
                {
                    $uploadRef = $pUniqueRef;
                    $sharedAction = 'CUSTOMER PREVIEW';
                    $reorderAction = 'CUSTOMER REORDER';
                }
                else
                {
                    $shareRef = $pUniqueRef;
                    $sharedAction = 'SHARE PREVIEW';
                    $reorderAction = 'SHARE REORDER';

					$sql = 'SELECT si.method, si.orderitemid, si.recipient, oi.uploadref
	    					FROM `SHAREDITEMS` si
								INNER JOIN ORDERITEMS oi ON si.orderitemid = oi.id
	    					WHERE (si.uniqueid = ?)
								AND ((si.action = "SHARE") OR (si.action = "CUSTOMER NOTIFICATION"))';
					$stmt = $dbObj->prepare($sql);

                    if ($stmt)
                    {
                        if ($stmt->bind_param('s', $shareRef))
                        {
                            if ($stmt->execute())
                            {
                                if ($stmt->store_result())
                                {
                                    if ($stmt->num_rows > 0)
                                    {
                                        if ($stmt->bind_result($sharedMethod, $pOrderItemId, $recipients, $uploadRef))
                                        {
                                            if (!$stmt->fetch())
                                            {
                                                $result = 'str_DatabaseError';
                                                $resultParam = 'share showPreview fetch ' . $dbObj->error;
                                            }
                                        }
                                        else
                                        {
                                            $result = 'str_DatabaseError';
                                            $resultParam = 'share showPreview bind result ' . $dbObj->error;
                                        }
                                    }
                                }
                                else
                                {
                                    $result = 'str_DatabaseError';
                                    $resultParam = 'share showPreview store result ' . $dbObj->error;
                                }
                            }
                            else
                            {
                                $result = 'str_DatabaseError';
                                $resultParam = 'share showPreview execute ' . $dbObj->error;
                            }
                        }
                        else
                        {
                            $result = 'str_DatabaseError';
                            $resultParam = 'share showPreview bind params ' . $dbObj->error;
                        }

                        $stmt->free_result();
                        $stmt->close();
                        $stmt = null;
                    }
                    else
                    {
                        $result = 'str_DatabaseError';
                        $resultParam = 'share showPreview prepare ' . $dbObj->error;
                    }
                }

                // get info about the original order
                if ($stmt = $dbObj->prepare('SELECT oi.productcollectioncode, oi.productcode, oh.groupcode, oh.id, oh.ordernumber, oh.webbrandcode, oh.temporder, oi.projectname,
	    					oi.productname, p.previewtype, p.previewcovertype, p.previewautoflip, p.previewthumbnailsview, p.previewthumbnails,
							br.macdownloadurl, br.win32downloadurl, br.previewlicensekey, oh.datecreated, oi.active, oi.status, oi.source,
							oi.uploaddatatype, oi.previewsonline, oi.projectref, oi.projectbuildduration, oi.userid, oi.canupload, oi.producttype,
                            br.orderfrompreview as brandorderfrompreview, li.orderfrompreview, br.sharebyemailmethod, oh.temporder, oi.canreorder, oh.origorderid
							FROM ORDERITEMS oi
                            JOIN ORDERHEADER oh ON oh.id = oi.orderid
                            JOIN PRODUCTS p ON p.code = oi.productcode
                            JOIN BRANDING br ON br.code = oh.webbrandcode
                            JOIN LICENSEKEYS as li ON li.groupcode = oh.groupcode
							WHERE (oi.id = ?)
                                AND (oi.uploadref = ?)'))
                {
                    if ($stmt->bind_param('is', $pOrderItemId, $uploadRef))
                    {
                        if ($stmt->execute())
                        {
                            if ($stmt->store_result())
                            {
                                if ($stmt->num_rows > 0)
                                {
                                    if ($stmt->bind_result($productCollectionCode, $productCode, $groupCode, $orderID, $orderNumber,
                                                    $brandCode, $tempOrder, $projectName, $productName, $previewType, $previewCoverType,
                                                    $previewAutoFlip, $previewThumbnailsView, $previewThumbnails,
                                                    $macDownloadUrl, $win32DownloadUrl, $previewLicenseKey, $orderDateCreated,
                                                    $orderItemActive, $productionStatus, $orderSource, $uploadDataType,
                                                    $previewsOnline, $projectRef, $projectBuildDuration, $orderUserID, $canUpload, $productType,
                                                    $brandOrderfrompreview, $orderFromPreview, $shareByEmailMethod, $tempOrder, $orderItemCanReorder,
													$origorderID))
                                    {
                                        if (!$stmt->fetch())
                                        {
                                            $result = 'str_DatabaseError';
                                            $resultParam = 'share showPreview fetch ' . $dbObj->error;
                                        }
                                    }
                                    else
                                    {
                                        $result = 'str_DatabaseError';
                                        $resultParam = 'share showPreview bind result ' . $dbObj->error;
                                    }
                                }
                            }
                            else
                            {
                                $result = 'str_DatabaseError';
                                $resultParam = 'share showPreview store result ' . $dbObj->error;
                            }
                        }
                        else
                        {
                            $result = 'str_DatabaseError';
                            $resultParam = 'share showPreview execute ' . $dbObj->error;
                        }
                    }
                    else
                    {
                        $result = 'str_DatabaseError';
                        $resultParam = 'share showPreview bind ' . $dbObj->error;
                    }
                    $stmt->free_result();
                    $stmt->close();
                    $stmt = null;
                }
                else
                {
                    $result = 'str_DatabaseError';
                    $resultParam = 'share showPreview prepare ' . $dbObj->error;
                }

                if ($orderItemActive == TPX_ORDER_STATUS_CANCELLED)
                {
                    $orderCancelled = true;
                }

                // if such order exists then record the preview
                if (($orderID > 0) && ($result == ''))
                {
                    // check if it has been unshared
                    $sharedInfo = self::isShared($pOrderItemId, $shareRef);
                    if (($pSource != 'CUSTOMER') && ($sharedInfo['unshared']))
                    {
                        $result = 'str_ErrorPreviewUnshared';
                    }
                    else
                    {
                        // check if preview hasn't expired yet
                        $brandingArray = DatabaseObj::getBrandingFromCode($brandCode);
                        $googleAnalyticsCode = $brandingArray['googleanalyticscode'];
                        $previewExpires = $brandingArray['previewexpires'];

                        if ($previewExpires)
                        {
                            $expiryDate = strtotime(date("Y-m-d h:i:s", strtotime($orderDateCreated)) . " +" . ($brandingArray['previewexpiresdays'] - 1) . " days");
                            $time = strtotime(date("Y-m-d h:i:s", strtotime($orderDateCreated)));

                            if ($expiryDate < $time)
                            {
                                $canOrder = false;
                            }
                            else
                            {
                                $canOrder = true;
                            }
                        }

                        // if the order is a temp order then we aren't allowed to re-order
                        if ($tempOrder == 1)
                        {
                            $canOrder = false;
                        }

                        // if we have an order created using Taopix online and we still have not recieved the files we cant order.
                        if (($orderSource == TPX_SOURCE_ONLINE) && ($productionStatus == TPX_ITEM_STATUS_AWAITING_FILES))
                        {
                            $canOrder = false;
                        }

                        //branding preference for order option
                        if ($orderFromPreview == 2)
                        {
                            if ($brandOrderfrompreview == 0)
                            {
                                $canOrder = false;
                            }
                        }
                        else
                        {
                            if ($orderFromPreview == 0)
                            {
                                $canOrder = false;
                            }
                        }

                        if (($canOrder) && ($orderItemCanReorder != TPX_ITEM_CAN_REORDER))
                        {
                        	$canOrder = false;
                        }

                        if ($stmt = $dbObj->prepare('INSERT INTO `SHAREDITEMS` (`datecreated`, `datemodified`, `action`, `method`, `uniqueid`, `userid`, `orderitemid`, `orderid`, `productcode`, `webbrandcode`,
				        	`recipient`) VALUES (now(), now(), ?, ?, ?, ?, ?, ?, ?, ?, ?)'))
                        {
                            if ($stmt->bind_param('sssisssss', $sharedAction, $sharedMethod, $shareRef, $userId, $pOrderItemId, $orderID,
                                            $productCode, $brandCode, $recipients))
                            {
                                if (!$stmt->execute())
                                {
                                    $result = 'str_DatabaseError';
                                    $resultParam = 'share showPreview execute ' . $dbObj->error;
                                }
                            }
                            else
                            {
                                $result = 'str_DatabaseError';
                                $resultParam = 'share showPreview bind ' . $dbObj->error;
                            }
                            $stmt->free_result();
                            $stmt->close();
                            $stmt = null;
                        }
                        else
                        {
                            $result = 'str_DatabaseError';
                            $resultParam = 'share showPreview prepare ' . $dbObj->error;
                        }
                    }

                    if ($result == '')
                    {
                        $pageRef = '';
                        $pageName = '';
                        $pageWidth = 0;
                        $pageHeight = 0;
						$productPageFormat = 0;
                        $productSpreadPageFormat = 0;
                        $productCover1Format = 0;
                        $productCover2Format = 0;
						$currentPageName = '';

                        if ($stmt = $dbObj->prepare('SELECT ot.pageref, ot.pagename, ot.width, ot.height, oi.productspreadpageformat,
														oi.productpageformat, oi.productcover1format, oi.productcover2format, ot.version
													FROM ORDERITEMS oi, ORDERTHUMBNAILS ot
													WHERE (oi.id = ?)
														AND (ot.uploadref = oi.uploadref)
													ORDER BY ot.pageref ASC'))
                        {
                            if ($stmt->bind_param('s', $pOrderItemId))
                            {
                                if ($stmt->bind_result($pageRef, $pageName, $pageWidth, $pageHeight, $productSpreadPageFormat,
														$productPageFormat, $productCover1Format, $productCover2Format, $previewVersion))
                                {
                                    if ($stmt->execute())
                                    {
                                        while ($stmt->fetch())
                                        {
											// prevent page name to be duplicated
											$thePageName = $pageName;
											if (! in_array($pageRef, Array('fc', 'fcsp', 'fcfr', 'fcbk', 'fcff', 'fcbf', 'bc')))
											{
												if ($currentPageName != $pageName)
												{
													$currentPageName = $pageName;
												}
												else
												{
													$thePageName = ' ';
												}
											}

											$pagesArray[$pageRef] = array('pagename' => $thePageName, 'width' => $pageWidth, 'height' => $pageHeight);
                                        }
                                    }
                                    else
                                    {
                                        $result = 'str_DatabaseError';
                                        $resultParam = 'share showPreview execute ' . $dbObj->error;
                                    }
                                }
                                else
                                {
                                    $result = 'str_DatabaseError';
                                    $resultParam = 'share showPreview bindresult ' . $dbObj->error;
                                }
                            }
                            else
                            {
                                $result = 'str_DatabaseError';
                                $resultParam = 'share showPreview bind ' . $dbObj->error;
                            }

                            $stmt->free_result();
                            $stmt->close();
                            $stmt = null;
                        }
                        else
                        {
                            $result = 'str_DatabaseError';
                            $resultParam = 'share showPreview prepare ' . $dbObj->error;
                        }

                        if ($result == '')
                        {
                            $frontCover = '';
                            $backCover = '';
                            $alwaysOpened = 'false';
                            $pageWidth = 300;
                            $pageHeight = 400;
							$coverWidth = 300;
                            $coverHeight = 400;
                            $pageCount = 0;
							$hasCover = 'false';

                            if ($previewCoverType == TPX_PREVIEW_COVER_HARD)
                            {
                                $isHardCover = 'true';
                            }
                            else
                            {
                                $isHardCover = 'false';
                            }


                            // determine if there is a front cover and include it if one is present
                            if ($productCover1Format != 0)
                            {
                                if ($productCover1Format == 1)
                                {
                                    // single front cover
                                    $frontCover = 'fc';
                                }
                                elseif ($productCover1Format == 2)
                                {
                                    // combined cover
                                    $frontCover = 'fcfr';
                                    $backCover = 'fcbk';
                                }

                                if (isset($pagesArray[$frontCover]))
                                {
                                    $previewPages[$frontCover] = $pagesArray[$frontCover];
                                }
                                else
                                {
                                    // the cover entry could not be found (missing database records?) so force no cover
                                    $productCover1Format = 0;
                                }
                            }

                            // determine if there is a single back cover
                            if (($productCover1Format < 2) && ($productCover2Format == 1))
                            {
                                $backCover = 'bc';
                            }


                            // if there is no front cover prevent it from appearing in the page turning
                            if ($productCover1Format == 0)
                            {
                                $isHardCover = 'false';
                                $alwaysOpened = 'true';
                            }


                            // if there is no inside left add a blank left page
                            if ($productSpreadPageFormat == 1)
                            {
								$previewPages['noinsideleft'] = array();
                            }


                            // write all the pages which are not covers
                            foreach ($pagesArray as $pageRef => $pageData)
                            {
                                if (! in_array($pageRef, Array('fc', 'fcsp', 'fcfr', 'fcbk', 'fcff', 'fcbf', 'bc')))
                                {
                                    if ((int) $pageData['width'] > 0)
                                    {
                                        $pageWidth = (int) $pageData['width'];
                                    }

                                    if ((int) $pageData['width'] > 0)
                                    {
                                        $pageHeight = (int) $pageData['height'];
                                    }
                                    $pageCount++;

                                    $previewPages[$pageRef] = $pagesArray[$pageRef];
                                }
								else
								{
									$hasCover = 'true';
									if ($productCover1Format == 1)
									{
										// single front cover
										if ($pageRef == 'fc')
										{
											if ((int) $pageData['width'] > 0)
											{
												$coverWidth = (int) $pageData['width'];
											}

											if ((int) $pageData['width'] > 0)
											{
												$coverHeight = (int) $pageData['height'];
											}
										}
									}
									elseif ($productCover1Format == 2)
									{
										// combined cover
										if ($pageRef == 'fcfr')
										{
											if ((int) $pageData['width'] > 0)
											{
												$coverWidth = (int) $pageData['width'];
											}

											if ((int) $pageData['width'] > 0)
											{
												$coverHeight = (int) $pageData['height'];
											}
										}
									}
								}
                            }


                            // offset the page count by the first page number
                            $pageCount += $productSpreadPageFormat;

                            // if we have no outside right add a blank right page for a spread project
                            if (($pageCount % 2 > 0) && ($productPageFormat == 1))
                            {
								$previewPages['nooutsideright'] = array();
                            }


                            // insert the back cover
                            if (($backCover) && ($pagesArray[$backCover]))
                            {
                                $previewPages[$backCover] = $pagesArray[$backCover];
                            }


                            // set the other page turning properties
                            $pageFlipSettings['hardcover'] = $isHardCover;
                            $pageFlipSettings['hascover'] = $hasCover;
                            $pageFlipSettings['alwaysopened'] = $alwaysOpened;
                            $pageFlipSettings['pagewidth'] = $pageWidth;
                            $pageFlipSettings['pageheight'] = $pageHeight;
							$pageFlipSettings['coverwidth'] = $coverWidth;
                            $pageFlipSettings['coverheight'] = $coverHeight;
                            if ($productType == TPX_PRODUCTCOLLECTIONTYPE_SINGLEPRINTS)
                            {
                                $pageFlipSettings['pagescale'] = 'false';
                            }
							else
							{
								$pageFlipSettings['pagescale'] = 'true';
							}

                            $pageFlipSettings['startautoflip'] = (($previewAutoFlip == 1) ? 'true' : 'false');
                            $pageFlipSettings['verticalmode'] = (($previewType == TPX_PREVIEW_DISPLAY_VERT_PAGETURN) ? 'true' : 'false');
                            $pageFlipSettings['contentpreviewenabled'] = (($previewThumbnails == 1) ? 'true' : 'false');
							$pageFlipSettings['buttonthumbnailenabled'] = (($previewThumbnailsView == 1) ? 'true' : 'false');
                            $pageFlipSettings['productpageformat'] = (($productPageFormat == 0) ? 'true' : 'false');

                            $thumbnailsWidth = 60;
                            $thumbnailsHeight = ($thumbnailsWidth / $pageWidth) * $pageHeight;

							if ($thumbnailsHeight > 110)
							{
								$ratio = 110 / $thumbnailsHeight;
								$thumbnailsHeight = 110;
								$thumbnailsWidth = $thumbnailsWidth * $ratio;
							}

                            $pageFlipSettings['thumbnailwidth'] = $thumbnailsWidth;
                            $pageFlipSettings['thumbnailheight'] = $thumbnailsHeight;
                        }
                    }
                }
                $dbObj->close();
            }
            else
            {
                $result = 'str_DatabaseError';
                $resultParam = 'share showPreview connect ' . $dbObj->error;
            }
        }

        if (($orderSource == TPX_SOURCE_ONLINE) && ($previewsOnline == 0))
        {
			$origUserId = 0;
            $exisistingPreviewData = Array();
            $previewType = TPX_PREVIEW_DISPLAY_EXTERNAL;

            $exisistingPreviewData['projectref'] = $projectRef;
            $exisistingPreviewData['brandcode'] = $brandCode;
            $exisistingPreviewData['groupcode'] = $groupCode;
            $exisistingPreviewData['orderuserid'] = $orderUserID;
            $exisistingPreviewData['productcollectioncode'] = $productCollectionCode;
            $exisistingPreviewData['productcode'] = $productCode;
            $exisistingPreviewData['previewviewsource'] = $pSource;
            $exisistingPreviewData['workflowtype'] = $productType;

			// if it's a reoder the original userid need to be passed
			while (($origorderID > 0) && ($result == ''))
			{
				$sql = 'SELECT userid, origorderid
						FROM ORDERHEADER
						WHERE id = ?';
				$stmt = $dbObj->prepare($sql);
				if ($stmt)
				{
					if ($stmt->bind_param('i', $origorderID))
					{
						if ($stmt->bind_result($origUserId, $origorderID))
						{
							if ($stmt->execute())
							{
								if ($stmt->fetch())
								{
									 $exisistingPreviewData['orderuserid'] = $origUserId;
								}
								else
								{
									$result = 'str_DatabaseError';
									$resultParam = 'get original user id fetch ' . $dbObj->error;
								}
							}
							else
							{
								$result = 'str_DatabaseError';
								$resultParam = 'get original user id execute ' . $dbObj->error;
							}
						}
						else
						{
							$result = 'str_DatabaseError';
							$resultParam = 'get original user id bindresult ' . $dbObj->error;
						}
					}
					else
					{
						$result = 'str_DatabaseError';
						$resultParam = 'get original user id bind ' . $dbObj->error;
					}

					$stmt->free_result();
					$stmt->close();
					$stmt = null;
				}
				else
				{
					$result = 'str_DatabaseError';
					$resultParam = 'get original user id prepare ' . $dbObj->error;
				}
			}

			if ($result == '')
			{
				$externalPreviewURL = self::requestExternalPreviewURL($exisistingPreviewData);
			}
        }

        if ($previewType != TPX_PREVIEW_DISPLAY_EXTERNAL)
        {
            // if we have less tahn two pages or it's a single page project the slide preview is used
            if (($pageCount < 2) || ($productType == TPX_PRODUCTCOLLECTIONTYPE_SINGLEPRINTS))
            {
                // we have less than 2 pages so force the slide show
                $previewType = 0;
            }
            else
            {
                // if the preview data version supports page turning previews and the preview type is one of the page turning types then use the page turning component
                if (($previewVersion >= 2) && (($previewType == TPX_PREVIEW_DISPLAY_VERT_PAGETURN) || ($previewType == TPX_PREVIEW_DISPLAY_HORIZ_PAGETURN)))
                {
                    $previewType = 1;
                }
                else
                {
                    // use the slide show
                    $previewType = 0;
                }
            }
        }

        $resultArray['result'] = $result;
        $resultArray['resultparam'] = $resultParam;
        $resultArray['method'] = $sharedMethod;
        $resultArray['userid'] = $userId;
        $resultArray['orderitem'] = $pOrderItemId;
        $resultArray['orderid'] = $orderID;
        $resultArray['productcode'] = $productCode;
        $resultArray['producttype'] = $productType;
        $resultArray['brandcode'] = $brandCode;
		$resultArray['googleanalyticscode'] = $googleAnalyticsCode;
        $resultArray['recipients'] = $recipients;
        $resultArray['sharedref'] = $shareRef;
        $resultArray['uploadref'] = $uploadRef;
        $resultArray['reorderaction'] = $reorderAction;

        // Generate the path to the thumbnails folder.
        $thumbnailPathData = UtilsObj::generateOrderThumbnailsPath($uploadRef, false);

        //test if the image exist in server
        foreach ($previewPages as $sKey => $aValue)
        {
			if (($sKey != 'noinsideleft') && ($sKey != 'nooutsideright'))
			{
				$fileImg = $thumbnailPathData['actual'] . '/' . $sKey . '.jpg';
				if (!file_exists($fileImg) || !is_readable($fileImg))
				{
					$previewPages = array();
					break;
				}
			}
        }

        $resultArray['pages'] = $previewPages;
        $resultArray['thumbnailpath'] = $thumbnailPathData['web'];
        $resultArray['projectname'] = $projectName;
        $resultArray['productname'] = $productName;
        $resultArray['displaytype'] = $previewType;
        $resultArray['previewlicensekey'] = $previewLicenseKey;
        $resultArray['macdownloadurl'] = $macDownloadUrl;
        $resultArray['win32downloadurl'] = $win32DownloadUrl;
        $resultArray['canorder'] = $canOrder;
        $resultArray['ordercancelled'] = $orderCancelled;
        $resultArray['ordersource'] = $orderSource;
        $resultArray['externalpreviewurl'] = $externalPreviewURL;
        $resultArray['canupload'] = $canUpload;
		$resultArray['temporder'] = $tempOrder;
        $resultArray['productionstatus'] = $productionStatus;
        $resultArray['sharebyemailmethod'] = $shareByEmailMethod;
        $resultArray['source'] = $pSource;
		$resultArray['pageflipsettings'] = $pageFlipSettings;

        return $resultArray;
    }

    // if it's a preview from customer screen then find files by uploadref.
    // if it's a share then unique ref will be a sharing id
    static function showPreview($pSource, $pUniqueRef, $pOrderItemId)
    {
        global $gSession;

        $resultArray = Array();
        $uploadRef = '';
        $shareRef = '';
        $sharedMethod = '';
        $recipients = '';
        $result = '';
        $resultParam = '';
        $reorderAction = '';
        $previewPages = Array();
        $projectName = '';
        $productName = '';
        $macDownloadUrl = '';
        $win32DownloadUrl = '';
        $canOrder = false;
        $orderID = 0;
        $productCode = '';
        $brandCode = '';
        $productType = '';
        $productionStatus = TPX_ITEM_STATUS_AWAITING_FILES;
        $canUpload = 0;
		$tempOrder = 0;

        $userId = $gSession['userid'];

        // check if need to show login screen
        if ($pUniqueRef && $pSource)
        {
            $dbObj = DatabaseObj::getGlobalDBConnection();
            if ($dbObj)
            {
                // get uploadref for the shared item
                if ($pSource == 'CUSTOMER')
                {
                    $uploadRef = $pUniqueRef;
                    $sharedAction = 'CUSTOMER PREVIEW';
                    $reorderAction = 'CUSTOMER REORDER';
                }
                else
                {
                    $shareRef = $pUniqueRef;
                    $sharedAction = 'SHARE PREVIEW';
                    $reorderAction = 'SHARE REORDER';

                    $stmt = $dbObj->prepare('SELECT si.method, si.orderitemid, si.recipient, oi.uploadref, oi.producttype, oi.status,
                                                    oi.canupload, oh.temporder
                                                FROM `SHAREDITEMS` si
                                                    INNER JOIN ORDERITEMS oi ON si.orderitemid = oi.id
													INNER JOIN `ORDERHEADER` oh ON oh.id = oi.orderid
                                                WHERE (si.uniqueid = ?)
                                                    AND ((si.action = "SHARE") OR (si.action = "CUSTOMER NOTIFICATION"))');

                    if ($stmt)
                    {
                        if ($stmt->bind_param('s', $shareRef))
                        {
                            if ($stmt->execute())
                            {
                                if ($stmt->store_result())
                                {
                                    if ($stmt->num_rows > 0)
                                    {
                                        if ($stmt->bind_result($sharedMethod, $pOrderItemId, $recipients, $uploadRef, $productType, $productionStatus,
												$canUpload, $tempOrder))
                                        {
                                            if (!$stmt->fetch())
                                            {
                                                $result = 'str_DatabaseError';
                                                $resultParam = 'share showPreview fetch ' . $dbObj->error;
                                            }
                                        }
                                        else
                                        {
                                            $result = 'str_DatabaseError';
                                            $resultParam = 'share showPreview bind result ' . $dbObj->error;
                                        }
                                    }
                                    else
                                    {
                                        $result = 'str_DatabaseError';
                                        $resultParam = 'share showPreview bind num rows ' . $dbObj->error;
                                    }
                                }
                                else
                                {
                                    $result = 'str_DatabaseError';
                                    $resultParam = 'share showPreview bind num rows ' . $dbObj->error;
                                }
                            }
                            else
                            {
                                $result = 'str_DatabaseError';
                                $resultParam = 'share showPreview execute ' . $dbObj->error;
                            }
                        }
                        else
                        {
                            $result = 'str_DatabaseError';
                            $resultParam = 'share showPreview bind ' . $dbObj->error;
                        }

                        $stmt->free_result();
                        $stmt->close();
                        $stmt = null;
                    }
                    else
                    {
                        $result = 'str_DatabaseError';
                        $resultParam = 'share showPreview prepare ' . $dbObj->error;
                    }
                }
            }
        }

        if ($pOrderItemId)
        {
            $sharedInfo = self::isShared($pOrderItemId, $shareRef);

            $previewPassword = $sharedInfo['password'];
            $brandCode = $sharedInfo['webbrandcode'];

            // if it's a customer previewing own book then don't ask for the password
            if (($previewPassword != '') && ($pSource != 'CUSTOMER'))
            {
                $result = 'str_ErrorPreviewLogin';

                $resultArray['result'] = $result;
                $resultArray['resultparam'] = $resultParam;
                $resultArray['method'] = $sharedMethod;
                $resultArray['userid'] = $userId;
                $resultArray['orderitem'] = $pOrderItemId;
                $resultArray['orderid'] = $orderID;
                $resultArray['productcode'] = $productCode;
                $resultArray['producttype'] = $productType;
                $resultArray['brandcode'] = $brandCode;
                $resultArray['recipients'] = $recipients;
                $resultArray['sharedref'] = $shareRef;
                $resultArray['uploadref'] = $uploadRef;
                $resultArray['reorderaction'] = $reorderAction;
                $resultArray['pages'] = $previewPages;
                $resultArray['projectname'] = $projectName;
                $resultArray['productname'] = $productName;
                $resultArray['displaytype'] = 0;
                $resultArray['macdownloadurl'] = $macDownloadUrl;
                $resultArray['win32downloadurl'] = $win32DownloadUrl;
                $resultArray['canorder'] = $canOrder;
                $resultArray['source'] = $pSource;
                $resultArray['uniqueref'] = $pUniqueRef;
                $resultArray['productionstatus'] = $productionStatus;
                $resultArray['canupload'] = $canUpload;
				$resultArray['temporder'] = $tempOrder;
            }
            else
            {
                $resultArray = self::previewPrepareFiles($pSource, $pUniqueRef, $pOrderItemId);
            }
        }
        return $resultArray;
    }

    static function reorder()
    {
        global $gSession;

        $resultArray = Array();
        $result = '';
        $resultParam = '';

        $userId = $gSession['userid'];
        $orderItemID = (isset($_POST['orderitemid'])) ? (int) $_POST['orderitemid'] : 0;
        $previewHash = (isset($_POST['ref2'])) ? $_POST['ref2'] : '';
        $sharedMethod = (isset($_POST['method'])) ? $_POST['method'] : '';
        $recipients = (isset($_POST['recipient'])) ? $_POST['recipient'] : '';
        $sharedAction = (isset($_POST['action'])) ? $_POST['action'] : '';

        $dbObj = DatabaseObj::getGlobalDBConnection();
        if ($dbObj)
        {
            $languageCode = 'en';
            $ownerCode = '';
            $productType = 0;
            $shareID = 0;
            $productCollectionCode = '';
            $productCollectionName = '';
            $productCode = '';
            $productName = '';
            $productPageFormat = 0;
            $productSpreadPageFormat = 0;
            $productCover1Format = 0;
            $productCover2Format = 0;
            $productOutputFormat = 0;
            $productHeight = 0.00;
            $productWidth = 0.00;
            $pageCount = 0;
            $uploadGroupCode = '';
            $uploadOrderID = 0;
            $uploadOrderNumber = '';
            $uploadOrderItemID = 0;
            $uploadRef = '';
            $projectRef = '';
            $projectRefOrig = '';
            $projectName = '';
            $appVersion = '';
            $orderPageCount = 0;
            $groupCode = '';
            $groupData = '';
            $shoppingCartType = TPX_SHOPPINGCARTTYPE_INTERNAL;
            $currencyExchangeRate = 1.0000;
            $useDefaultCurrency = 1;
            $currencyCode = '';
            $currencyDecimalPlaces = 2;
            $jobTicketTemplate = 'jobticket_large';
            $groupName = '';
            $groupAddress1 = '';
            $groupAddress2 = '';
            $groupAddress3 = '';
            $groupAddress4 = '';
            $groupAddressCity = '';
            $groupAddressCounty = '';
            $groupAddressState = '';
            $groupPostCode = '';
            $groupCountryCode = '';
            $groupEmailAddress = '';
            $groupTelephoneNumber = '';
            $groupContactFirstName = '';
            $groupContactLastName = '';
            $coverCode = '';
            $paperCode = '';
            $shoppingCartURL = '';
            $orderHeaderId = 0;
            $orderNumber = 0;
            $brandCode = '';
            $uploadAppVersion = '';
            $uploadAppPlatform = '';
            $uploadAppCPUType = '';
            $uploadAppOSVersion = '';
            $isOflineOrder = 0;
            $uploadMethod = 0;
            $currentOwner = '';
            $projectStartTime = '';
            $projectDuration = 0;
            $projectDataSize = 0;
            $projectUploadDuration = 0;
            $canUpload = 1;
            $previewsOnline = 0;
            $productCollectionOrigOwnerCode = '';
            $source = TPX_SOURCE_DESKTOP;
            $productOptions = TPX_PRODUCTOPTION_PRICING_NON;
            $projectAiMode = TPX_AIMODE_DISABLED;

            if ($stmt = $dbObj->prepare('SELECT oh.ownercode, oi.projectref, oi.projectreforig, oi.projectname, oi.projectbuildstartdate,
                oi.projectbuildduration, oi.productcollectionname, oi.productcollectioncode, oi.productcollectionorigownercode, oi.productcode,
	      		oi.productname, oi.producttype, oi.productpageformat,
				oi.productpageformat, oi.productspreadpageformat, oi.productcover1format, oi.productcover2format, oi.productoutputformat, oi.productheight, oi.productwidth,
				oi.pagecount, oi.uploadgroupcode, oi.uploadorderid, oi.uploadordernumber, oi.uploadorderitemid, oi.uploadref, oi.uploadappversion, oi.pagecountpurchased,
				oh.groupcode, oh.groupdata, oh.shoppingcarttype, oh.id, oh.ordernumber, oh.webbrandcode, oi.uploadappversion, oi.uploadappplatform, oi.uploadappcputype, oi.uploadapposversion,
				oi.uploaddatasize, oi.uploadduration, oh.offlineorder, oi.uploadmethod, oi.currentowner, oi.previewsonline, oi.canupload, oi.source, oi.productoptions,
				oi.projectaimode
                FROM ORDERITEMS oi
				JOIN ORDERHEADER oh ON oh.id = oi.orderid WHERE oi.id = ?'))
            {
                if ($stmt->bind_param('i', $orderItemID))
                {
                    if ($stmt->execute())
                    {
                        if ($stmt->store_result())
                        {
                            if ($stmt->num_rows > 0)
                            {
                                if ($stmt->bind_result($ownerCode, $projectRef, $projectRefOrig, $projectName, $projectStartTime,
                                                $projectDuration, $productCollectionName, $productCollectionCode, $productCollectionOrigOwnerCode, $productCode,
                                                $productName, $productType, $productPageFormat, $productPageFormat,
                                                $productSpreadPageFormat, $productCover1Format, $productCover2Format, $productOutputFormat,
                                                $productHeight, $productWidth, $pageCount, $uploadGroupCode, $uploadOrderID,
                                                $uploadOrderNumber, $uploadOrderItemID, $uploadRef, $appVersion, $orderPageCount,
                                                $groupCode, $groupData, $shoppingCartType, $orderHeaderId, $orderNumber, $brandCode,
                                                $uploadAppVersion, $uploadAppPlatform, $uploadAppCPUType, $uploadAppOSVersion,
                                                $projectDataSize, $projectUploadDuration, $isOflineOrder, $uploadMethod, $currentOwne,
                                                $previewsOnline, $canUpload, $source, $productOptions, $projectAiMode))
                                {
                                    if (!$stmt->fetch())
                                    {
                                        $result = 'str_DatabaseError';
                                        $resultParam = 'reorder select fetch ' . $dbObj->error;
                                    }
                                }
                                else
                                {
                                    $result = 'str_DatabaseError';
                                    $resultParam = 'reorder select bind result ' . $dbObj->error;
                                }
                            }
                            else
                            {
                                $result = 'str_DatabaseError';
                                $resultParam = 'reorder select num rows ' . $dbObj->error;
                            }
                        }
                        else
                        {
                            $result = 'str_DatabaseError';
                            $resultParam = 'reorder select store result ' . $dbObj->error;
                        }
                    }
                    else
                    {
                        $result = 'str_DatabaseError';
                        $resultParam = 'reorder select execute ' . $dbObj->error;
                    }
                }
                else
                {
                    $result = 'str_DatabaseError';
                    $resultParam = 'reorder select bind params ' . $dbObj->error;
                }


                $stmt->free_result();
                $stmt->close();
                $stmt = null;

                if ($result == '')
                {
                    if ($previewHash != '')
                    {
                        if ($stmt = $dbObj->prepare('SELECT `id` FROM `SHAREDITEMS` WHERE (`uniqueid` = ?) AND (`action` = "SHARE")'))
                        {
                            if ($stmt->bind_param('s', $previewHash))
                            {
                                if ($stmt->execute())
                                {
                                    if ($stmt->store_result())
                                    {
                                        if ($stmt->num_rows > 0)
                                        {
                                            if ($stmt->bind_result($shareID))
                                            {
                                                $stmt->fetch();
                                            }
                                            else
                                            {
                                                $result = 'str_DatabaseError';
                                                $resultParam = 'reorder get shareid bind result ' . $dbObj->error;
                                            }
                                        }
                                    }
                                    else
                                    {
                                        $result = 'str_DatabaseError';
                                        $resultParam = 'reorder get shareid store results ' . $dbObj->error;
                                    }
                                }
                                else
                                {
                                    $result = 'str_DatabaseError';
                                    $resultParam = 'reorder get shareid execute ' . $dbObj->error;
                                }
                            }
                            else
                            {
                                $result = 'str_DatabaseError';
                                $resultParam = 'reorder get shareid bind ' . $dbObj->error;
                            }

                            $stmt->free_result();
                            $stmt->close();
                            $stmt = null;
                        }
                        else
                        {
                            $result = 'str_DatabaseError';
                            $resultParam = 'reorder get shareid prepare ' . $dbObj->error;
                        }

                        if ($result == '')
                        {
                            if ($stmt = $dbObj->prepare('INSERT INTO `SHAREDITEMS` (`datecreated`, `action`, `method`, `uniqueid`, `userid`, `orderitemid`, `orderid`, `productcode`, `webbrandcode`,
								`recipient`) VALUES (now(), ?, ?, ?, ?, ?, ?, ?, ?, ?)'))
                            {
                                if ($stmt->bind_param('sssisssss', $sharedAction, $sharedMethod, $previewHash, $userId, $orderItemID,
                                                $orderHeaderId, $productCode, $brandCode, $recipients))
                                {
                                    if (!$stmt->execute())
                                    {
                                        $result = 'str_DatabaseError';
                                        $resultParam = 'reorder insert share execute ' . $dbObj->error;
                                    }
                                }
                                else
                                {
                                    $result = 'str_DatabaseError';
                                    $resultParam = 'reorder insert share bind ' . $dbObj->error;
                                }
                                $stmt->free_result();
                                $stmt->close();
                                $stmt = null;
                            }
                            else
                            {
                                $result = 'str_DatabaseError';
                                $resultParam = 'reorder insert share prepare ' . $dbObj->error;
                            }
                        }
                    }
                }
            }
            else
            {
                $result = 'str_DatabaseError';
                $resultParam = 'reorder select prepare ' . $dbObj->error;
            }
            $dbObj->close();

    
            // if we have no errors determine if the user's group code exists in the database
            if ($result == '')
            {
                $licenseKeyArray = DatabaseObj::getLicenseKeyFromCode($groupCode);

                // Set correct companycode for the customer. Check to see if the license key belongs to a brand if it does
                // use the brand company code. If not use the license key companycode
                $companyCode = $licenseKeyArray['companyCode'];

                if ($licenseKeyArray['webbrandcode'] != '')
                {
                    $currencyCode = $licenseKeyArray['currencycode'];
                    if ($useDefaultCurrency == 1)
                    {
                        $currencyExchangeRate = 1;
                    }
                    else
                    {
                        // get exchangerate from database;
                        $currency = DatabaseObj::getCurrency($currencyCode);
                        if ($result == '')
                        {
                            $currencyExchangeRate = $currency['exchangerate'];
                            $currencyDecimalPlaces = $currency['decimalplaces'];
                        }
                    }
                }

                $productArray = DatabaseObj::getProductFromCollectionCodeAndLayoutCode($productCollectionCode, $productCode);
				$productPriceArray = DatabaseObj::getProductPrice($productCode, $groupCode, $companyCode, $currencyExchangeRate, $currencyDecimalPlaces, -1);

                // get the code for the product tree linking for the product
                $productLinkingArray = DatabaseObj::getApplicableProductLinkCode($productCode);

                if ($productLinkingArray['error'] == '')
                {
                    if ($productLinkingArray['linkedcode'] != '')
                    {
                        $componentTreeProductCode = $productLinkingArray['linkedcode'];
                    }
                    else
                    {
                        $componentTreeProductCode = $productCode;
                    }
                }

				// first check to see if the product is not deleted and it is still active.
               	if (($productArray['isactive'] == 1) && ($productArray['deleted'] == 0) && ($productLinkingArray['error'] == ''))
               	{
					// if the product has not been deleted and it is still active we need to check if there is still a valid price.
					if ($productPriceArray['result'] == '')
					{
						$productAssetArray = DatabaseObj::getOrderItemComponentAssets($orderItemID);
						$pictureArray = DatabaseObj::getOrderItemComponentSinglePrints($orderItemID, false);

						// Check to see if all SINGLEPRINT components for the orderline have a price.
						$hasPictureLookup = false;
						if (array_key_exists('key', $pictureArray['pictures']))
						{
							$pictures = $pictureArray['pictures']['key'];
							$hasPictureLookup = true;
						}
						else
						{
							$pictures = $pictureArray['pictures'];
						}

						$consolidatedPicturesSizeStockArray = array();
						$applyBasePriceLineSubtract = true;

						if ($productOptions == TPX_PRODUCTOPTION_PRICING_PERCOMPONENTSUBCOMPONENT)
						{
							// consolidate the singleprint prices
							foreach ($pictures as $picture)
							{
								if ($hasPictureLookup)
								{
									$picture = $pictureArray['pictures']['data'][$picture];
								}

								$componentSubComponentKey = $picture['code'];

								if ($picture['subcode'] != '')
								{
									$componentSubComponentKey .= '.' . $picture['subcode'];
								}

								if (!array_key_exists($componentSubComponentKey, $consolidatedPicturesSizeStockArray))
								{
									$consolidatedPicturesSizeStockArray[$componentSubComponentKey]['qty'] = $picture['qty'];
									$consolidatedPicturesSizeStockArray[$componentSubComponentKey]['basepricelinesubtractapplied'] = false;
								}
								else
								{
									$consolidatedPicturesSizeStockArray[$componentSubComponentKey]['qty'] += $picture['qty'];
								}
							}
						}

						foreach ($pictures as $picture)
						{
							if ($hasPictureLookup)
							{
								$picture = $pictureArray['pictures']['data'][$picture];
							}

							$lineBreakQTY = $picture['qty'];

							$componentSubComponentKey = $picture['code'];

							if ($picture['subcode'] != '')
							{
								$componentSubComponentKey .= '.' . $picture['subcode'];
							}

							if ($productOptions == TPX_PRODUCTOPTION_PRICING_PERCOMPONENTSUBCOMPONENT)
							{
								$lineBreakQTY = $consolidatedPicturesSizeStockArray[$componentSubComponentKey]['qty'];

								$applyBasePriceLineSubtract = false;

								if (! $consolidatedPicturesSizeStockArray[$componentSubComponentKey]['basepricelinesubtractapplied'])
								{
									$applyBasePriceLineSubtract = true;
								}
							}

							if ($applyBasePriceLineSubtract)
							{
								$applyBasePrice = 1;
							}
							else
							{
								$applyBasePrice = 0;
							}

							$componentCode = 'SINGLEPRINT' . '.' . $picture['code'];

							$pictureArrayCacheKey = $companyCode . '.' . $groupCode . '.' . $productCode;
							$pictureArrayCacheKey .= '.' . $componentCode . '.' . $pageCount . '.' . $lineBreakQTY . '.' . $picture['qty'] . '.' . $applyBasePrice;
							$picturePriceArray = DatabaseObj::getPriceCacheData($pictureArrayCacheKey);

							if (count($picturePriceArray) == 0)
							{
								$picturePriceArray = DatabaseObj::getPrice('$SINGLEPRINT\\', $componentCode, false, $componentTreeProductCode, $groupCode,
											$companyCode, $currencyExchangeRate, $currencyDecimalPlaces, -1, $pageCount, $lineBreakQTY, $picture['qty'], false, false,
											-1, 0, '', $applyBasePriceLineSubtract);
								DatabaseObj::setPriceCacheData($pictureArrayCacheKey, $picturePriceArray);
							}

							$result = $picturePriceArray['result'];

							if ($result == '')
							{
								if ($picture['subcode'] != '')
								{
									// check to see if all SINGLEPRINT SUBCOMPONENTS for the orderline have a price
									$subComponentParentPath = '$SINGLEPRINT\\' . $picture['code'] . '\\$SINGLEPRINTOPTION\\';
									$subComponentCode = 'SINGLEPRINTOPTION' . '.' . $picture['subcode'];

									$subComponentArrayCacheKey = $companyCode . '.' . $groupCode . '.' . $productCode;
									$subComponentArrayCacheKey .= '.' . $subComponentCode . '.' . $pageCount . '.' . $lineBreakQTY . '.' . $picture['qty']. '.' . $applyBasePrice;
									$subComponentPriceArray = DatabaseObj::getPriceCacheData($subComponentArrayCacheKey);

									if (count($subComponentPriceArray) == 0)
									{
										$subComponentPriceArray = DatabaseObj::getPrice($subComponentParentPath, $subComponentCode, false, $componentTreeProductCode, $groupCode,
												$companyCode, $currencyExchangeRate, $currencyDecimalPlaces, -1, $pageCount, $lineBreakQTY, $picture['qty'], false, false,
												-1, 0, '', $applyBasePriceLineSubtract);
										DatabaseObj::setPriceCacheData($subComponentArrayCacheKey, $subComponentPriceArray);
									}

									if ($productOptions == TPX_PRODUCTOPTION_PRICING_PERCOMPONENTSUBCOMPONENT)
									{
										$consolidatedPicturesSizeStockArray[$componentSubComponentKey]['basepricelinesubtractapplied'] = true;
									}

									$result = $subComponentPriceArray['result'];

									if ($result != '')
									{
										$result = 'str_SinglePrintNoPriceAvailableError';
										$resultParam = LocalizationObj::getLocaleString($picture['subname'], $languageCode, true);
										break;
									}
								}
							}
							else
							{
								$result = 'str_SinglePrintNoPriceAvailableError';
								$resultParam = LocalizationObj::getLocaleString($picture['name'], $languageCode, true);
								break;
							}
						}

                        $calendarCustomisationsOrderArray = DatabaseObj::getOrderItemComponentCalendarComponents($orderItemID);
                        $calendarCustomisationsArray = array();

                        $emptyCalendarComptItemArray = array();
                        $emptyCalendarComptItemArray['componentname'] = '';
                        $emptyCalendarComptItemArray['componentcategory'] = 'CALENDARCUSTOMISATION';
                        $emptyCalendarComptItemArray['componentcode'] = '';
                        $emptyCalendarComptItemArray['info'] = '';
                        $emptyCalendarComptItemArray['skucode'] = '';
                        $emptyCalendarComptItemArray['unitsell'] = 0.00;
                        $emptyCalendarComptItemArray['unitcost'] = 0.00;
                        $emptyCalendarComptItemArray['unitweight'] = 0.00;
                        $emptyCalendarComptItemArray['totalcost'] = 0.00;
                        $emptyCalendarComptItemArray['totalsell'] = 0.00;
                        $emptyCalendarComptItemArray['totaltax'] = 0.00;
                        $emptyCalendarComptItemArray['totalsellnotax'] = 0.00;
                        $emptyCalendarComptItemArray['totalsellwithtax'] = 0.00;
                        $emptyCalendarComptItemArray['totalweight'] = 0.00;
                        $emptyCalendarComptItemArray['pricetaxcode'] = '';
                        $emptyCalendarComptItemArray['pricetaxrate'] = '';
                        $emptyCalendarComptItemArray['islist'] = 1;
                        $emptyCalendarComptItemArray['pricingmodel'] = TPX_PRICINGMODEL_PERPRODCMPQTY;
                        $emptyCalendarComptItemArray['metadata'] = array();
                        $emptyCalendarComptItemArray['subtotal'] = 0.00;
                        $emptyCalendarComptItemArray['componentqty'] = 0;
                        $emptyCalendarComptItemArray['orderfootertaxname'] = '';
                        $emptyCalendarComptItemArray['orderfootertaxrate'] = 0.00;
                        $emptyCalendarComptItemArray['discountvalue'] = 0.00;
                        $emptyCalendarComptItemArray['discountedtax'] = 0.00;
                        $emptyCalendarComptItemArray['priceinfo'] = '';
                        $emptyCalendarComptItemArray['path'] = '$CALENDARCUSTOMISATION\\';
                        $emptyCalendarComptItemArray['used'] = false;

                        // set up a list of empty customisation items
                        $calendarCustomisationsArray['calendarcustomisations']['CALENDARCUSTOMISATION.DATE'] = $emptyCalendarComptItemArray;
                        $calendarCustomisationsArray['calendarcustomisations']['CALENDARCUSTOMISATION.DATE']['componentcode'] = 'CALENDARCUSTOMISATION.DATE';
                        $calendarCustomisationsArray['calendarcustomisations']['CALENDARCUSTOMISATION.EVENTSET'] = $emptyCalendarComptItemArray;
                        $calendarCustomisationsArray['calendarcustomisations']['CALENDARCUSTOMISATION.EVENTSET']['componentcode'] = 'CALENDARCUSTOMISATION.EVENTSET';
                        $calendarCustomisationsArray['calendarcustomisations']['CALENDARCUSTOMISATION.ANY'] = $emptyCalendarComptItemArray;
                        $calendarCustomisationsArray['calendarcustomisations']['CALENDARCUSTOMISATION.ANY']['componentcode'] = 'CALENDARCUSTOMISATION.ANY';


                        // first find out which calendar components have been used in order
                        $dateFoundInOrder = false;
                        $eventSetFoundInOrder = false;
                        $anyFoundInOrder = false;

                        $dateQty = 0;
                        $eventSetQty = 0;

                        foreach ($calendarCustomisationsOrderArray['calendarcustomisations'] as $calendarCustomisations)
                        {
                            if ($calendarCustomisations['componentcode'] == 'DATE')
                            {
                                $dateFoundInOrder = true;
                                $dateQty = $calendarCustomisations['componentqty'];
                                $calendarCustomisationsArray['calendarcustomisations']['CALENDARCUSTOMISATION.DATE']['componentqty'] = $dateQty;
                            }

                            if ($calendarCustomisations['componentcode'] == 'EVENTSET')
                            {
                                $eventSetFoundInOrder = true;
                                $eventSetQty = $calendarCustomisations['componentqty'];
                                $calendarCustomisationsArray['calendarcustomisations']['CALENDARCUSTOMISATION.EVENTSET']['componentqty'] = $eventSetQty;
                            }

                            // if both date and eventset are found stop the loop since we have all we need
                            if (($dateFoundInOrder) && ($eventSetFoundInOrder))
                            {
                                break;
                            }

                            // no need to look for any if either date or eventsets are found
                            if ((! $dateFoundInOrder) && (! $eventSetFoundInOrder))
                            {
                                if ($calendarCustomisations['componentcode'] == 'ANY')
                                {
                                    $anyFoundInOrder = true;
                                    $customAny = $calendarCustomisations;
                                }
                            }
                        }

                        if (($dateFoundInOrder) || ($eventSetFoundInOrder) || ($anyFoundInOrder))
                        {

                            // get all the calendar customisations which are attached to the products component tree
                            $calendarCustomisationsDBArray = DatabaseObj::getComponentsInOrderSectionByCategory('$CALENDARCUSTOMISATION\\', 'CALENDARCUSTOMISATION',
                                                                                    $companyCode, $componentTreeProductCode, $groupCode, 1.0, 2, -1, -1, -1, '', false, true);

                            $componentItemCount = count($calendarCustomisationsDBArray['component']);

                            $useAny = true;
                            $anyFound = false;

                            // look through all the components from the database and set the relevant data to the calendar customisation array items
                            for ($j = 0; $j < $componentItemCount; $j++)
                            {
                                $componentArray = $calendarCustomisationsDBArray['component'][$j];

                                $code = $componentArray['code'];

                                $calendarCustomisationsArray['calendarcustomisations'][$code]['used'] = true;
                                $calendarCustomisationsArray['calendarcustomisations'][$code]['componentcode'] = $code;
                                $calendarCustomisationsArray['calendarcustomisations'][$code]['componentname'] = $componentArray['name'];
                                $calendarCustomisationsArray['calendarcustomisations'][$code]['skucode'] = $componentArray['skucode'];
                                $calendarCustomisationsArray['calendarcustomisations'][$code]['info'] = $componentArray['info'];
                                $calendarCustomisationsArray['calendarcustomisations'][$code]['unitcost'] = $componentArray['unitcost'];
                                $calendarCustomisationsArray['calendarcustomisations'][$code]['unitweight'] = $componentArray['unitweight'];
                                $calendarCustomisationsArray['calendarcustomisations'][$code]['pricingmodel'] = $componentArray['pricingmodel'];
                                $calendarCustomisationsArray['calendarcustomisations'][$code]['pricetaxcode'] = $componentArray['pricetaxcode'];
                                $calendarCustomisationsArray['calendarcustomisations'][$code]['pricetaxrate'] = $componentArray['pricetaxrate'];
                                $calendarCustomisationsArray['calendarcustomisations'][$code]['priceinfo'] = $componentArray['priceinfo'];

                                // set the qty from the data sent from the desktop or online designer
                                if (($code == 'CALENDARCUSTOMISATION.EVENTSET') && ($eventSetFoundInOrder))
                                {
                                    $useAny = false;
                                }
                                else if (($code == 'CALENDARCUSTOMISATION.DATE') && ($dateFoundInOrder))
                                {
                                    $useAny = false;
                                }
                                else if ($code == 'CALENDARCUSTOMISATION.ANY')
                                {

                                    if ($dateFoundInOrder)
                                    {
                                        $calendarCustomisationsArray['calendarcustomisations']['CALENDARCUSTOMISATION.ANY']['componentqty'] += $dateQty;
                                        $anyFound = true;
                                    }

                                    if ($eventSetFoundInOrder)
                                    {
                                        $calendarCustomisationsArray['calendarcustomisations']['CALENDARCUSTOMISATION.ANY']['componentqty'] += $eventSetQty;
                                        $anyFound = true;
                                    }
                                }
                            }

                            // ANY should only be used when DATE and EVENTSET are both missing
                            if (!$useAny)
                            {
                                unset($calendarCustomisationsArray['calendarcustomisations']['CALENDARCUSTOMISATION.ANY']);
                            }

                            foreach ($calendarCustomisationsArray['calendarcustomisations'] as &$calendarCustomisation)
                            {
                                if (($calendarCustomisation['componentqty'] > 0) && ($calendarCustomisation['used']))
                                {
                                    // get the price from the database and make sure that it is valid and the quantity is in range
                                    $calcustomPriceArray = DatabaseObj::getPrice($calendarCustomisation['path'], $calendarCustomisation['componentcode'], false,
                                                                                    $componentTreeProductCode, $groupCode, $companyCode, $currencyExchangeRate,
                                                                                    $currencyDecimalPlaces, -1, -1, $calendarCustomisation['componentqty'], $calendarCustomisation['componentqty'], true, true, -1, 0, '', true);

                                    if (($calcustomPriceArray['result'] != '') || ($calcustomPriceArray['isactive'] == 0) || ($calcustomPriceArray['newqty'] != $calendarCustomisation['componentqty']))
                                    {
                                        $result = 'str_ErrorNoComponent';
                                        break;
                                    }
                                }
                            }

                            unset($calendarCustomisation);
						}
						
						// find out whether an AI Component was used in the previous order
						$AIComponentArray = DatabaseObj::getOrderItemComponentAIComponents($orderItemID);
						if (!empty($AIComponentArray['aicomponents']))
						{
							// we only need to know that an AI Component is present but we want the data from the component table rather than the previous order 
							$tempAIComponentArray = DatabaseObj::getComponentByCode("TAOPIXAI.TAOPIXAI");

							$tempAIComponentArray['path'] = '$TAOPIXAI\\';
							$tempAIComponentArray['componentcode'] = "TAOPIXAI.TAOPIXAI";
							$tempAIComponentArray['used'] = true;
							$tempAIComponentArray['componentqty'] = 1;

							$AIComponentArray = $tempAIComponentArray;
						}
						else 
						{
							// there is no component so empty out the array
							$AIComponentArray = Array();
						}
						
					}
					else
					{
						$result = $productPriceArray['result'];
					}

                }
                else
                {
                	$result = 'str_ErrorProductNotAvailable2';
					$resultParam = LocalizationObj::getLocaleString($productArray['name'], $languageCode, true);
                }

                if ($result == '')
                {
                    $cartArray = Array();

                    $cartItemArray = Array();
                    $cartItemArray['shareid'] = $shareID;
                    $cartItemArray['source'] = $source;
                    $cartItemArray['productoptions'] = $productOptions;
                    $cartItemArray['pricetransformationstage'] = $productArray['pricetransformationstage'];
                    $cartItemArray['origorderitemid'] = $orderItemID;
                    $cartItemArray['uploadgroupcode'] = $uploadGroupCode;
                    $cartItemArray['uploadorderid'] = $uploadOrderID;
                    $cartItemArray['uploadordernumber'] = $uploadOrderNumber;
                    $cartItemArray['uploadorderitemid'] = $uploadOrderItemID;
                    $cartItemArray['uploadref'] = $uploadRef;
                    $cartItemArray['collectioncode'] = $productCollectionCode;
                    $cartItemArray['collectionname'] = $productCollectionName;
                    $cartItemArray['productcode'] = $productCode;
                    $cartItemArray['productskucode'] = $productArray['skucode'];
                    $cartItemArray['productname'] = $productArray['name'];
                    $cartItemArray['producttype'] = $productType;
                    $cartItemArray['productpageformat'] = $productPageFormat;
                    $cartItemArray['productspreadformat'] = $productSpreadPageFormat;
                    $cartItemArray['productcover1format'] = $productCover1Format;
                    $cartItemArray['productcover2format'] = $productCover2Format;
                    $cartItemArray['productoutputformat'] = $productOutputFormat;
                    $cartItemArray['productheight'] = $productHeight;
                    $cartItemArray['productwidth'] = $productWidth;
                    $cartItemArray['productdefaultpagecount'] = $productArray['defaultpagecount'];
                    $cartItemArray['projectref'] = $projectRef;
                    $cartItemArray['projectreforig'] = $projectRefOrig;
                    $cartItemArray['projectname'] = $projectName;
                    $cartItemArray['projectstarttime'] = $projectStartTime;
                    $cartItemArray['projectduration'] = $projectDuration;
                    $cartItemArray['pagecount'] = $pageCount;
                    $cartItemArray['producttaxlevel'] = $productArray['taxlevel'];
                    $cartItemArray['productunitcost'] = $productArray['unitcost'];
                    $cartItemArray['productunitweight'] = $productArray['weight'];
                    $cartItemArray['covercode'] = $coverCode;
                    $cartItemArray['papercode'] = $paperCode;
                    $cartItemArray['uploadappversion'] = $uploadAppVersion;
                    $cartItemArray['uploadappplatform'] = $uploadAppPlatform;
                    $cartItemArray['uploadappcputype'] = $uploadAppCPUType;
                    $cartItemArray['uploadapposversion'] = $uploadAppOSVersion;
                    $cartItemArray['uploaddatasize'] = $projectDataSize;
                    $cartItemArray['uploadduration'] = $projectUploadDuration;
                    $cartItemArray['componenttreeproductcode'] = $componentTreeProductCode;

                    $cartItemArray['externalassets'] = $productAssetArray['externalassets'];
                    $cartItemArray['pictures'] = $pictureArray['pictures'];
                    $cartItemArray['calendarcustomisations'] = $calendarCustomisationsArray['calendarcustomisations'];

                    $cartItemArray['previewsonline'] = $previewsOnline;
                    $cartItemArray['canupload'] = $canUpload;

					$cartItemArray['productcollectionorigownercode'] = $productCollectionOrigOwnerCode;
					
					if (!empty($AIComponentArray))
					{
						$cartItemArray['aicomponent'] = $AIComponentArray;
                    }
                    
                    $cartItemArray['projectaimode'] = $projectAiMode;

                    $cartArray[] = $cartItemArray;

                    $resultArray = Order_model::orderSessionInitialize($languageCode, $appVersion, $licenseKeyArray['webbrandcode'],
                                    $shoppingCartType, '', $jobTicketTemplate, $licenseKeyArray['showpriceswithtax'],
                                    $licenseKeyArray['showtaxbreakdown'], $licenseKeyArray['showzerotax'],
                                    $licenseKeyArray['showalwaystaxtotal'], $ownerCode, $groupCode, $groupData, $groupName, $groupAddress1,
                                    $groupAddress2, $groupAddress3, $groupAddress4, $groupAddressCity, $groupAddressCounty,
                                    $groupAddressState, $groupPostCode, $groupCountryCode, $groupTelephoneNumber, $groupEmailAddress,
                                    $groupContactFirstName, $groupContactLastName, $gSession['ref'], $cartArray, TPX_BASKETWORKFLOWTYPE_NORMAL, '', true);

                    if ($resultArray['result'] != '')
                    {
                        $result = $resultArray['result'];
                        $resultParam = $resultArray['resultparam'];
                    }

                    //Set the batch ref to be the session ref with a timestamp

                    $gSession['items'][0]['itemuploadbatchref'] = $gSession['ref'] . '_' . time();

                    if ($gSession['ref'] > 0)
                    {
                        $currentLine = 0;
                        $gSession['order']['isreorder'] = 1;
                        $gSession['order']['origorderid'] = $orderHeaderId;
                        $gSession['order']['origordernumber'] = $orderNumber;
                        $gSession['items'][$currentLine]['origorderitemid'] = $orderItemID;
                        $gSession['order']['isofflineorder'] = $isOflineOrder;

						// customer is already logged in so do not authenticate again in the cart
						$gSession['authenticatecookie'] = 0;
						$gSession['browserlanguagecode'] = UtilsObj::getBrowserLocale();

                        // check to see if the upload method of the orignal order was MAIL.
                        // if it is then we need to force the production site of the reorder to the production site of the orignal order.
                        if ($uploadMethod == TPX_UPLOAD_DELIVERY_METHOD_MAIL)
                        {
                            // store the production site of the orignal order
                            $gSession['order']['offlineordersitecode'] = $currentOwner;
                        }

                        $cartSessionStarted = false;
                        if (AuthenticateObj::WebSessionActive() == 1)
                        {
                            $shoppingCartURL = UtilsObj::getBrandedWebUrl() . '?fsaction=Order.reorder&ref=' . $gSession['ref'];
                        }
                        else
                        {
                            $shoppingCartURL = UtilsObj::getBrandedWebUrl() . '?fsaction=Order.initialize&ref=' . $gSession['ref'];
                            $cartSessionStarted = true;
                        }

                        if ($shoppingCartType > TPX_SHOPPINGCARTTYPE_INTERNAL)
                        {
                            AppAPI_model::includeExternalShoppingCart();
                            if (method_exists('ExternalShoppingCart', 'initialise'))
                            {
                                if ($gSession['userid'] > 0)
                                {
                                    $userDataArray = DatabaseObj::getUserAccountFromID($gSession['userid']);
                                    $userLogin = $userDataArray['login'];
                                    $userAccountCode = $userDataArray['accountcode'];
                                    $userStatus = $userDataArray['addressupdated'];
                                }
                                else
                                {
                                    $userLogin = '';
                                    $userAccountCode = '';
                                    $userStatus = 1;
                                }

                                $externalCartInitArray = Array();
                                $externalCartInitArray['apiversion'] = 0;
                                $externalCartInitArray['languagecode'] = $languageCode;
                                $externalCartInitArray['ownercode'] = $ownerCode;
                                $externalCartInitArray['groupcode'] = $groupCode;
                                $externalCartInitArray['brandcode'] = $licenseKeyArray['webbrandcode'];
                                $externalCartInitArray['groupdata'] = $groupData;
                                $externalCartInitArray['userid'] = $gSession['userid'];
                                $externalCartInitArray['userlogin'] = $gSession['userlogin'];
                                $externalCartInitArray['userssotoken'] = $gSession['userdata']['ssotoken'];
                                $externalCartInitArray['userssoprivatedata'] = $gSession['userdata']['ssoprivatedata'];
                                $externalCartInitArray['useraccountcode'] = $userAccountCode;
                                $externalCartInitArray['userstatus'] = $userStatus;
                                $externalCartInitArray['uuid'] = '';
                                $externalCartInitArray['ref'] = $gSession['ref'];
                                $externalCartInitArray['origorderid'] = $orderHeaderId;
                                $externalCartInitArray['origordernumber'] = $orderNumber;
                                $externalCartInitArray['shoppingcarturl'] = $shoppingCartURL;
                                $externalCartInitArray['reorder'] = 1;
                                $externalCartInitArray['batchref'] = $gSession['ref'];
                                $externalCartInitArray['items'] = $cartArray;

                                $externalShoppingCartConfig = ExternalShoppingCart::initialise($externalCartInitArray);

                                if ($externalShoppingCartConfig['result'] == '')
                                {
                                    if (($externalShoppingCartConfig['usecustomshoppingcart']) && ($externalShoppingCartConfig['shoppingcarturl'] != ''))
                                    {
                                        $shoppingCartURL = $externalShoppingCartConfig['shoppingcarturl'];

                                        // start the web session as the user will not be logging in via taopix web to start it
                                        $recordID = DatabaseObj::startSession(-1, '', '', TPX_LOGIN_API, '', '',
                                                        $externalCartInitArray['brandcode'], '', '', array());

                                        $cartSessionStarted = true;
                                    }
                                    else
                                    {
                                        $gSession['order']['shoppingcarttype'] = TPX_SHOPPINGCARTTYPE_INTERNAL;
                                    }
                                }
                                else
                                {
                                    $result = 'CUSTOMERROR';
                                    $resultParam = $externalShoppingCartConfig['result'];
                                }
                            }
                        }

                        // load order in session for user
                        if (! $cartSessionStarted)
                        {
                            $recordID = DatabaseObj::startSession($gSession['userid'], $gSession['userlogin'], $gSession['username'],
                                            TPX_LOGIN_CUSTOMER, $gSession['userdata']['companycode'], $gSession['userdata']['userowner'],
                                            $gSession['userdata']['webbrandcode'], '', '', array());
                            DatabaseObj::updateActivityLog($recordID, 0, $gSession['userid'], $gSession['userlogin'], $gSession['username'],
                                    0, 'CUSTOMER', 'REORDER', '', 1);
                        }

                        DatabaseObj::updateSession();
                    }
                    header("Location: " . $shoppingCartURL);
                    return true;
                }
                else
                {
                    $resultArray['action'] = $sharedAction;
                    $resultArray['orderitemid'] = $orderItemID;
                    $resultArray['result'] = $result;
                    $resultArray['resultparam'] = $resultParam;

                    return $resultArray;
                }
            }
        }
    }

    static function getOriginalSharedItem($pSharedId)
    {
        $resultArray = Array();

        $result = '';
        $resultParam = '';
        $orderID = 0;
        $orderItemID = 0;
        $userId = 0;
        $action = '';
        $active = 0;

        $dbObj = DatabaseObj::getGlobalDBConnection();
        if ($dbObj)
        {
            if ($stmt = $dbObj->prepare('SELECT action, orderid, orderitemid, userid, active FROM `SHAREDITEMS`
	    								WHERE (uniqueid = ?) AND ((action = "SHARE") OR (action = "CUSTOMER NOTIFICATION"))
	    								ORDER BY id DESC LIMIT 1'))
            {
                if ($stmt->bind_param('s', $pSharedId))
                {
                    if ($stmt->execute())
                    {
                        if ($stmt->store_result())
                        {
                            if ($stmt->num_rows > 0)
                            {
                                if ($stmt->bind_result($action, $orderID, $orderItemID, $userId, $active))
                                {
                                    if (!$stmt->fetch())
                                    {
                                        $result = 'str_DatabaseError';
                                        $resultParam = 'share getOriginalSharedItem no item ' . $dbObj->error;
                                    }
                                }
                                else
                                {
                                    $result = 'str_DatabaseError';
                                    $resultParam = 'share getOriginalSharedItem bind result ' . $dbObj->error;
                                }
                            }
                            else
                            {
                                $result = 'str_DatabaseError';
                                $resultParam = 'share getOriginalSharedItem num rows ' . $dbObj->error;
                            }
                        }
                        else
                        {
                            $result = 'str_DatabaseError';
                            $resultParam = 'share getOriginalSharedItem store result ' . $dbObj->error;
                        }
                    }
                    else
                    {
                        $result = 'str_DatabaseError';
                        $resultParam = 'share getOriginalSharedItem execute ' . $dbObj->error;
                    }
                }
                else
                {
                    $result = 'str_DatabaseError';
                    $resultParam = 'share getOriginalSharedItem bind ' . $dbObj->error;
                }

                $stmt->free_result();
                $stmt->close();
                $stmt = null;
            }
            else
            {
                $result = 'str_DatabaseError';
                $resultParam = 'share getOriginalSharedItem prepare ' . $dbObj->error;
            }

            $dbObj->close();
        }
        else
        {
            $result = 'str_DatabaseError';
            $resultParam = 'share getOriginalSharedItem connect ' . $dbObj->error;
        }

        $resultArray['result'] = $result;
        $resultArray['resultparam'] = $resultParam;
        $resultArray['orderid'] = $orderID;
        $resultArray['orderitemid'] = $orderItemID;
        $resultArray['userid'] = $userId;
        $resultArray['action'] = $action;
        $resultArray['active'] = $active;

        return $resultArray;
    }

    static function isShared($pOrderItemId, $pUniqueId, $pIsItem = false)
    {
        $resultArray = Array();
        $result = '';
        $resultParam = '';
        $isShared = false;
        $shareCreated = '';
        $shareModified = '';
        $previewPassword = '';
        $webBrandCode = '';

        $dbObj = DatabaseObj::getGlobalDBConnection();
        if ($dbObj)
        {
            if ($pIsItem)
            {
                $sSql = 'SELECT datecreated, datemodified, password, webbrandcode
                            FROM `SHAREDITEMS`
                            WHERE orderid = ?
                                AND action = "SHARE"
                                AND datecreated = datemodified';
                $stmt = $dbObj->prepare($sSql);
                $bindOk = $stmt->bind_param('i', $pOrderItemId);
            }
            else
            {
                if ($pUniqueId != '')
                {
                    $stmt = $dbObj->prepare('SELECT datecreated, datemodified, password, webbrandcode FROM `SHAREDITEMS`
                                                WHERE (orderitemid = ?)
                                                    AND (action = "SHARE")
                                                    AND (uniqueid = ?)
                                                ORDER BY id DESC LIMIT 1');
                    $bindOk = $stmt->bind_param('is', $pOrderItemId, $pUniqueId);
                }
                else
                {
                    $stmt = $dbObj->prepare('SELECT datecreated, datemodified, password, webbrandcode FROM `SHAREDITEMS`
                                                WHERE (orderitemid = ?)
                                                    AND (action = "SHARE")
                                                ORDER BY id DESC LIMIT 1');
                    $bindOk = $stmt->bind_param('i', $pOrderItemId);
                }
            }

            if ($stmt)
            {
                if ($bindOk)
                {
                    if ($stmt->execute())
                    {
                        if ($stmt->store_result())
                        {
                            if ($stmt->num_rows > 0)
                            {
                                if ($stmt->bind_result($shareCreated, $shareModified, $previewPassword, $webBrandCode))
                                {
                                    if ($stmt->fetch())
                                    {
                                        $isShared = true;
                                    }
                                    else
                                    {
                                        $result = 'str_DatabaseError';
                                        $resultParam = 'share isShared fetch ' . $dbObj->error;
                                    }
                                }
                                else
                                {
                                    $result = 'str_DatabaseError';
                                    $resultParam = 'share isShared bind result ' . $dbObj->error;
                                }
                            }
                        }
                        else
                        {
                            $result = 'str_DatabaseError';
                            $resultParam = 'share isShared store results ' . $dbObj->error;
                        }
                    }
                    else
                    {
                        $result = 'str_DatabaseError';
                        $resultParam = 'share isShared execute ' . $dbObj->error;
                    }
                }
                else
                {
                    $result = 'str_DatabaseError';
                    $resultParam = 'share isShared bind ' . $dbObj->error;
                }

                $stmt->free_result();
                $stmt->close();
                $stmt = null;
            }
            else
            {
                $result = 'str_DatabaseError';
                $resultParam = 'share isShared prepare ' . $dbObj->error;
            }

            $dbObj->close();
        }
        else
        {
            $result = 'str_DatabaseError';
            $resultParam = 'share isShared connect ' . $dbObj->error;
        }

        $resultArray['result'] = $result;
        $resultArray['resultparam'] = $resultParam;
        $resultArray['shared'] = $isShared;
        $resultArray['unshared'] = (strtotime($shareCreated) == strtotime($shareModified)) ? false : true;
        $resultArray['password'] = $previewPassword;
        $resultArray['webbrandcode'] = $webBrandCode;

        return $resultArray;
    }


    /*
     * getSharedItemsForUser
     *  get a list of shared items when displaying user orders
     *
     * $pUserID - user id of the current signed in user
     */
    static function getSharedItemsForUser($pUserID)
    {
        $resultArray = Array();
        $result = '';
        $resultParam = '';
        $isShared = false;
        $shareCreated = '';
        $shareModified = '';
        $previewPassword = '';
        $webBrandCode = '';
        $orderID = '';
        $orderItemID = '';
        $shareList = array();

        $dbObj = DatabaseObj::getGlobalDBConnection();
        if ($dbObj)
        {
            // create the SQL to get a list of shared items for a specific user
            $sSql = 'SELECT `datecreated`, `datemodified`, `orderitemid`, `orderid`, `webbrandcode`, `password` ';
            $sSql .= 'FROM `SHAREDITEMS` ';
            $sSql .= 'WHERE `userid` = ? ';
            $sSql .= 'AND `action` = "SHARE" ';
            $sSql .= 'AND `datecreated` = `datemodified`';

            if ($stmt = $dbObj->prepare($sSql))
            {
                if ($bindOk = $stmt->bind_param('i', $pUserID))
                {
                    if ($stmt->execute())
                    {
                        if ($stmt->store_result())
                        {
                            if ($stmt->num_rows > 0)
                            {
                                if ($stmt->bind_result($shareCreated, $shareModified, $orderItemID, $orderID, $webBrandCode, $previewPassword))
                                {
                                    // read the results and enter each into an array to be returned
                                    while ($stmt->fetch())
                                    {
                                        $shareInfo = array();
                                        $shareInfo['shared'] = true;
                                        $shareInfo['unshared'] = false;
                                        $shareInfo['orderitemid'] = $orderItemID;
                                        $shareInfo['orderid'] = $orderID;
                                        $shareInfo['webbrandcode'] = $webBrandCode;
                                        $shareInfo['password'] = $previewPassword;

                                        // only the last entry is required, overwrite the previous entry
                                        $shareList[$orderItemID] = $shareInfo;
                                    }
                                }
                                else
                                {
                                    $result = 'str_DatabaseError';
                                    $resultParam = 'share isShared bind result ' . $dbObj->error;
                                }
                            }
                        }
                        else
                        {
                            $result = 'str_DatabaseError';
                            $resultParam = 'share isShared store results ' . $dbObj->error;
                        }
                    }
                    else
                    {
                        $result = 'str_DatabaseError';
                        $resultParam = 'share isShared execute ' . $dbObj->error;
                    }
                }
                else
                {
                    $result = 'str_DatabaseError';
                    $resultParam = 'share isShared bind ' . $dbObj->error;
                }

                $stmt->free_result();
                $stmt->close();
                $stmt = null;
            }
            else
            {
                $result = 'str_DatabaseError';
                $resultParam = 'share isShared prepare ' . $dbObj->error;
            }

            $dbObj->close();
        }
        else
        {
            $result = 'str_DatabaseError';
            $resultParam = 'share isShared connect ' . $dbObj->error;
        }

        $resultArray['result'] = $result;
        $resultArray['resultparam'] = $resultParam;
        $resultArray['data'] = $shareList;

        return $resultArray;
    }

    static function unshare()
    {
        global $gSession;

        $resultArray = Array();
        $result = '';
        $resultParam = '';
        $active = 0;
        $iNbShared = 0;

        $orderItemID = isset($_POST['orderItemId']) ? $_POST['orderItemId'] : 0;
        $userId = $gSession['userid'];

        $dataType = '';
        $subStatement = '';
        $valueArray = Array();
        $subStatementArray = Array();

        $valueArray[0] = $active;
        $orderitemIDArray = explode(",", $orderItemID);
        $iFirstId = $orderitemIDArray[0];
        foreach ($orderitemIDArray as $key => $value)
        {
            $dataType .= "i";
            $subStatementArray[] = " (`orderitemid` = ?) ";
            $valueArray[] = $value;
        }

        //attach ii for 'active' and 'userid'
        $dataType .= "ii";
        $valueArray[] = $userId;
        $subStatement = implode("OR", $subStatementArray);

        if (isset($_POST['orderItemId']) && $_POST['orderItemId'] != '')
        {
            $dbObj = DatabaseObj::getGlobalDBConnection();

            if ($dbObj)
            {
                $sql = 'UPDATE `SHAREDITEMS` SET `datemodified` = now(), `active` = ? ';
                $sql .= 'WHERE (' . $subStatement . ') AND (`action` = "SHARE") AND (`userid` = ?)';

                if ($stmt = $dbObj->prepare($sql))
                {
                    if (DatabaseObj::bindParams($stmt, $dataType, $valueArray))
                    {
                        if (!$stmt->execute())
                        {
                            $result = 'str_DatabaseError';
                            $resultParam = 'share unshare execute ' . $dbObj->error;
                        }
                    }
                    else
                    {
                        $result = 'str_DatabaseError';
                        $resultParam = 'share unshare bind ' . $dbObj->error;
                    }

                    $stmt->free_result();
                    $stmt->close();
                    $stmt = null;
                }
                else
                {
                    $result = 'str_DatabaseError';
                    $resultParam = 'share unshare prepare ' . $dbObj->error;
                }
                $dbObj->close();
            }
            else
            {
                $result = 'str_DatabaseError';
                $resultParam = 'share unshare connect ' . $dbObj->error;
            }
        }
        else
        {
            $result = 'str_ErrorConnectFailure';
            $resultParam = 'share unshare no orderitemid';
        }

        $resultArray['result'] = $result;
        $resultArray['resultparam'] = $resultParam;
        $resultArray['nbShared'] = $iNbShared;

        return $resultArray;
    }

    static function unShareList()
    {
        global $gSession;

        $iOrderId = isset($_GET['orderid']) ? $_GET['orderid'] : 0;
        $iUserId = $gSession['userid'];
        $resultArray = array();
        $orderItemID = 0;
        $name = '';
        $projectName = '';
        $productCode = '';

        $dbObj = DatabaseObj::getGlobalDBConnection();

        if ($dbObj)
        {
            $sSql = 'SELECT `si`.`orderitemid`, `oi`.`productname`, `oi`.`projectname`, `pr`.`code`
                        FROM `SHAREDITEMS` AS si
                            INNER JOIN `ORDERITEMS` AS oi ON oi.id = si.orderitemid
                            INNER JOIN PRODUCTS AS pr ON pr.code = oi.productcode
	    				WHERE si.orderid = ?
                            AND si.action = "SHARE"
                            AND si.userid = ?
                            and si.datecreated = si.datemodified
                        GROUP BY si.orderitemid
	    				ORDER BY si.orderitemid';
            $stmt = $dbObj->prepare($sSql);

            if ($stmt)
            {
                $stmt->attr_set(MYSQLI_STMT_ATTR_CURSOR_TYPE, MYSQLI_CURSOR_TYPE_READ_ONLY);
                if (DatabaseObj::bindParams($stmt, 'ii', array($iOrderId, $iUserId)))
                {
                    if ($stmt->bind_result($orderItemID, $name, $projectName, $productCode))
                    {
                        if ($stmt->execute())
                        {
                            // process each item
                            while ($stmt->fetch())
                            {
                                $filePath = UtilsObj::getAssetRequest($productCode, 'products');
                                $resultArray[] = array(
                                    'text' => LocalizationObj::getLocaleString($name, $gSession['browserlanguagecode'], true) . ' (' . $projectName . ')',
                                    'img' => ($filePath === '') ? $gSession['webbrandwebroot'] . '/images/no_image-2x.jpg' : $filePath,
                                    'id' => $orderItemID
                                );
                            }
                        }
                    }

                    $stmt->free_result();
                    $stmt->close();
                    $stmt = null;
                }
                $dbObj->close();
            }
            return $resultArray;
        }
    }

    static function requestExternalPreviewURL($pParamArray)
    {
        global $gSession;

        $previewExisitingProjectDataArray = array();
        $previewExisitingProjectDataArray['groupcode'] = $pParamArray['groupcode'];
        $previewExisitingProjectDataArray['webbrandcode'] = $pParamArray['brandcode'];
        $previewExisitingProjectDataArray['projectref'] = $pParamArray['projectref'];
        $previewExisitingProjectDataArray['userid'] = $pParamArray['orderuserid'];
        $previewExisitingProjectDataArray['productcollectioncode'] = $pParamArray['productcollectioncode'];
        $previewExisitingProjectDataArray['productlayoutcode'] = $pParamArray['productcode'];
        $previewExisitingProjectDataArray['previewviewsource'] = $pParamArray['previewviewsource'];
        $previewExisitingProjectDataArray['workflowtype'] = $pParamArray['workflowtype'];
        $previewExisitingProjectDataArray['loadedstatus'] = TPX_PROJECT_LOADED_WORKING;
        $previewExisitingProjectDataArray['templateref'] = '';

		$resultData = OnlineAPI_model::openOnlineProject(TPX_OPEN_MODE_PREVIEW_EXISITING, array(), $previewExisitingProjectDataArray, false, false);

        if ($resultData['maintenancemode'] == true)
        {
            $resultData['brandurl'] .= "&lang=" . $gSession['browserlanguagecode'];
        }

        return $resultData['brandurl'] . '#/preview/';
    }
}
?>
