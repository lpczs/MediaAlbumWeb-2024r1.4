<?php

class DataRedactionAPI_model
{
	/**
	 * requestRedaction
	 * - user has requested their account be removed, set the redaction request based on the mode configured in the brand
	 * - single user id using the current open session
	 * - mode of redaction set in brand, passed via GET
	 *
	 * @global $gSession
	 * @return array
	 */
	static function requestRedaction()
	{
		global $gSession;

		$resultArray = array('result' => '', 'resultparam' => '');

		// update the account's redaction progress to TPX_REDACTION_REQUESTED or TPX_REDACTION_AUTHORISED_BY_USER
		// based on the redaction mode
		$userID = $gSession['userid'];
		$redactionMode = (int)UtilsObj::getGETParam('mode', 0);
		$redactionDays = (int)UtilsObj::getGETParam('days', 0);
		$reasonString = '';
		$redactionProgress = 0;
		$action = '';

		// if the redaction days is set to 0, set the redaction time to before now, allowing the task to be executed immediately
		$redactionCalculationDays = ($redactionDays === 0) ? -1 : $redactionDays;
		$redactionDate = date('Y-m-d H:i:s', (time() + (60 * 60 * 24 * $redactionCalculationDays)));

		switch ($redactionMode)
		{
			case TPX_REDACTION_MODE_REQUEST:
			{
				// TPX_CUSTOMER_REDACTION_REQUESTED
				$action = 'REQUESTED';
				$reasonString = 'User Requested';
				$redactionProgress = TPX_REDACTION_REQUESTED;

				break;
			}

			case TPX_REDACTION_MODE_ALLOW:
			{
				// TPX_CUSTOMER_REDACTION_PENDING
				$action = 'AUTHORISED';
				$reasonString = 'User Authorised';
				$redactionProgress = TPX_REDACTION_AUTHORISED_BY_USER;

				break;
			}

			case TPX_REDACTION_MODE_IMMEDIATE:
			{
				// TPX_REDACTION_MODE_IMMEDIATE
				$action = 'AUTHORISED';
				$reasonString = 'User Authorised';
				$redactionProgress = TPX_REDACTION_AUTHORISED_BY_USER;

				break;
			}
		}

		$canRedact = self::canRedactAccounts(array($userID), (($redactionMode === TPX_REDACTION_MODE_IMMEDIATE) && ($redactionDays === 0)));

		// only 1 user is passed, and will be listed in the disallow array,
		// check if the [order] array hold the users name, any [session] entries can be ignored.
		if (count($canRedact['order']) > 0)
		{
			$redactionProgress = 0;
			$resultArray = array('result' => 'str_ErrorActiveOrders', 'resultparam' => $userID, 'error' => '');

			// get the error string
			$smarty = SmartyObj::newSmarty('AdminCustomers');

			$resultArray['error'] = $smarty->get_config_vars('str_ErrorActiveOrders');
		}

		if ($redactionProgress != 0)
		{
			$resultArray = DatabaseObj::updateRedactionProgress(TPX_REDACTION_NONE, $redactionProgress, $userID, '', $redactionDate, $reasonString);

			if ($resultArray['result'] == '')
			{
				$userData = DatabaseObj::getUserAccountFromID($userID);
				$brandData = DatabaseObj::getBrandingFromCode($userData['webbrandcode']);

				$redactAccount = array();

				$redactAccount['id'] = $userID;
				$redactAccount['brandcode'] = $userData['webbrandcode'];
				$redactAccount['login'] = $userData['login'];
				$redactAccount['emailaddress'] = $userData['emailaddress'];
				$redactAccount['contactfirstname'] = $userData['contactfirstname'];
				$redactAccount['contactlastname'] = $userData['contactlastname'];
				$redactAccount['contactfullname'] = $userData['contactfirstname'] . ' ' . $redactAccount['contactlastname'];
				$redactAccount['redactionnotificationdays'] = $redactionDays;
				$redactAccount['redactiondate'] = $redactionDate;
				$redactAccount['redactiondatelocal'] = LocalizationObj::formatLocaleDateTime($redactAccount['redactiondate'], '');
				$redactAccount['brandname'] = $brandData['name'];
				$redactAccount['displayurl'] = $brandData['displayurl'];
				$redactAccount['smtpadminaddress'] = $brandData['smtpadminaddress'];
				$redactAccount['targetuserid'] = $userID;


				// create the redaction event
				if ($redactionProgress === TPX_REDACTION_AUTHORISED_BY_USER)
				{
					if ($redactionMode === TPX_REDACTION_MODE_IMMEDIATE)
					{
						// send email to user confirming
						$emailObj = new TaopixMailer();
						$emailObj->sendTemplateEmail('customer_immediateredaction', $redactAccount['brandcode'], $redactAccount['brandname'],
													$redactAccount['displayurl'], '', $redactAccount['contactfullname'],
													$redactAccount['emailaddress'], '', '', 0, $redactAccount);
					}
					else
					{
						// no need to create the event if the redaction is set to immediate
						$eventResultArray = DatabaseObj::createEvent('TAOPIX_DATADELETION', '', '', '', $redactionDate, 0, '', '', $userID, TPX_REDACTION_AUTHORISED_BY_USER, '', '', '', '', 0, 0, 0, '', '', $userID);

						// send email to user confirming
						$emailObj = new TaopixMailer();
						$emailObj->sendTemplateEmail('customer_requestforredaction', $redactAccount['brandcode'], $redactAccount['brandname'],
													$redactAccount['displayurl'], '', $redactAccount['contactfullname'],
													$redactAccount['emailaddress'], '', '', 0, $redactAccount);
					}
				}
				else if ($redactionProgress == TPX_REDACTION_REQUESTED)
				{
					// email to user and admin regarding the request
					$emailObj = new TaopixMailer();
					$emailObj->sendTemplateEmail('admin_requestforredaction', $redactAccount['brandcode'], $redactAccount['brandname'],
												$redactAccount['displayurl'], '', $redactAccount['contactfullname'],
												$redactAccount['smtpadminaddress'], '', '', 0, $redactAccount);

					$emailObj = new TaopixMailer();
					$emailObj->sendTemplateEmail('customer_requestforredaction', $redactAccount['brandcode'], $redactAccount['brandname'],
												$redactAccount['displayurl'], '', $redactAccount['contactfullname'],
												$redactAccount['emailaddress'], '', '', 0, $redactAccount);

				}

				// add entry to activity log
				$actionNotes = $gSession['userid'] . ' ' . $reasonString;
				DatabaseObj::updateActivityLog($gSession['ref'], 0, $gSession['userid'], $gSession['userlogin'], $gSession['username'], 0, 'REDACTION', $action, $actionNotes, 1);

				if ($redactionMode === TPX_REDACTION_MODE_IMMEDIATE)
				{
					// trigger the next redaction step, to deactivate the account
					$queueResult = self::queueRedaction($redactAccount['id']);
				}

			}
		}

		return $resultArray;
	}

	/**
	 * authoriseRedaction
	 * - authorise or decline the redaction request or authorise direct from admin of control centre
	 * -
	 *
	 * @return type
	 */
	static function authoriseRedaction()
	{
		global $gSession;

		$resultArray = array('result' => '', 'resultparam' => '');

		// update the account's redaction progress to TPX_REDACTION_AUTHORISED_BY_LICENSEE when TPX_REDACTION_REQUESTED set by user
		// or TPX_REDACTION_DECLINED if the admin declines the redaction request
		$userID = explode(',', UtilsObj::getGETParam('userid', 0));
		$authorised = UtilsObj::getGETParam('auth', 0);

		$resultArray = self::authoriseRedaction2($userID, $authorised);
	}


	static function authoriseRedaction2($pUserID, $pAuthorised)
	{
		global $gSession;

		$resultArray = array('result' => '', 'resultparam' => '', 'data' => array('updated' => array(), 'failed' => array()));

		foreach ($pUserID as $theUser)
		{
			// update the account's redaction progress to TPX_REDACTION_AUTHORISED_BY_LICENSEE when TPX_REDACTION_REQUESTED set by user
			// or TPX_REDACTION_DECLINED if the admin declines the redaction request
			$redactionState = 0;
			$redactionProgress = 0;
			$reasonString = '';
			$action = '';

			$userData = DatabaseObj::getUserAccountFromID($theUser);
			$brandData = DatabaseObj::getBrandingFromCode($userData['webbrandcode']);

			switch ($pAuthorised)
			{
				case 0:
				{
					// decline redaction request - TPX_REDACTION_DECLINED
					$redactionState = TPX_REDACTION_REQUESTED;
					$redactionProgress = TPX_REDACTION_DECLINED;
					$reasonString = 'User Requested, Declined';
					$action = 'DECLINED';
					$redactionDate = '0000-00-00 00:00:00';

					break;
				}

				case 1:
				{
					// authorise redaction request - TPX_REDACTION_AUTHORISED_BY_LICENSEE
					$redactionProgress = TPX_REDACTION_AUTHORISED_BY_LICENSEE;
					$action = 'AUTHORISED';
					if ($userData['redactionprogress'] == TPX_REDACTION_REQUESTED)
					{
						$redactionState = TPX_REDACTION_REQUESTED;
						$reasonString = 'User Requested, Authorised';
						$redactionDate = $userData['redactiondate'];
					}
					else if ($userData['redactionprogress'] == TPX_REDACTION_DECLINED)
					{
						$redactionState = TPX_REDACTION_DECLINED;
						$reasonString = 'Authorised';
						$redactionDate = date('Y-m-d H:i:s', (time() + (60 * 60 * 24 * $brandData['redactionnotificationdays'])));
					}
					else
					{
						$redactionState = TPX_REDACTION_NONE;
						$reasonString = 'Authorised';
						$redactionDate = date('Y-m-d H:i:s', (time() + (60 * 60 * 24 * $brandData['redactionnotificationdays'])));
					}

					break;
				}

				case 2:
				{
					// set the redaction state to TPX_REDACTION_AUTHORISED_BY_LICENSEE, used for automatic redaction
					$redactionState = TPX_REDACTION_NONE;
					$redactionProgress = TPX_REDACTION_AUTHORISED_BY_LICENSEE;
					$reasonString = 'Authorised';
					$action = 'AUTHORISED';
					$redactionDate = date('Y-m-d H:i:s', (time() + (60 * 60 * 24 * $brandData['redactionnotificationdays'])));

					break;
				}
			}

			if ($redactionProgress != 0)
			{
				$updateResult = DatabaseObj::updateRedactionProgress($redactionState, $redactionProgress, $theUser, '', $redactionDate, $reasonString);

				if ($updateResult['result'] == '')
				{
					$resultArray['data']['updated'][] = $theUser;
				}
				else
				{
					$resultArray['data']['failed'][] = $theUser;
				}

				// set defaults for activity log
				$ref = 0;
				$userlogin = '';
				$username = '';
				$uid = 0;

				if (! empty($gSession))
				{
					// there is a session, use these values
					$ref = $gSession['ref'];
					$userlogin = $gSession['userlogin'];
					$username = $gSession['username'];
					$uid = $gSession['userid'];
				}

				$actionNotes = $theUser . ' ' . $reasonString;
				DatabaseObj::updateActivityLog($ref, 0, $uid, $userlogin, $username, 0, 'REDACTION', $action, $actionNotes, 1);
			}
		}

		return $resultArray;
	}

	/**
	 * setRedactionError
	 * - set the status of the redaction process to error
	 *
	 * @param type $pUserID
	 * @param type $pErrorMessage
	 * @return array
	 */
	static function setRedactionError($pUserID, $pErrorMessage)
	{
		$resultArray = array('result' => '', 'resultparam' => '');

		$dbObj = self::getGlobalDBConnection();

		$pNewState = TPX_REDACTION_ERROR;
		$pReasonMessage = $pErrorMessage;

		// update the redaction progress of the users listed
		if ($dbObj)
		{
			$sql = 'UPDATE `USERS` SET `redactionprogress` = ?, `redactionreason` = CAT(`redactionreason`, ", ", ?) ';
			$sql .= 'WHERE (`protectfromredaction` = 0) AND (`id` = ?)';

			if ($stmt = $dbObj->prepare($sql))
			{
				if ($stmt->bind_param('isi', $pNewState, $pReasonMessage, $pUserID))
				{
					if ($stmt->execute())
					{
						$resultArray['result'] = 'str_DatabaseError';
						$resultArray['resultparam'] = __FUNCTION__ . ' - execute: error (' . $dbObj->error . ')';
					}

					$stmt->free_result();
					$stmt->close();
					$stmt = null;
				}
				else
				{
					$resultArray['result'] = 'str_DatabaseError';
					$resultArray['resultparam'] = __FUNCTION__ . ' - bind_param: error (' . $dbObj->error . ')';
				}
			}
			else
			{
				$resultArray['result'] = 'str_DatabaseError';
				$resultArray['resultparam'] = __FUNCTION__ . ' - prepare: error (' . $dbObj->error . ')';
			}
			$dbObj->close();
		}




		return $resultArray;
	}

	/**
	 * updateRedactionProgress
	 * - update the redaction process for each of the accounts
	 *
	 * @global type $ac_config
	 * @global $gSession $gSession
	 * @global type $gConstants
	 * @return type
	 */
	static function updateRedactionProgress()
	{
		global $ac_config;
		global $gSession;
		global $gConstants;

		$resultArray = array('result' => '', 'resultparam' => '');

		// update the account's redaction progress to the next stage

		$userID = UtilsObj::getGETParam('userid', '');
		$redactionMode = UtilsObj::getGETParam('mode', 0);
		$redactionError = UtilsObj::getGETParam('error', 0);
		$errorMessage = UtilsObj::getGETParam('message', '');

		if ($redactionError == 0)
		{
			switch ($redactionMode)
			{
				case TPX_REDACTION_AUTHORISED_BY_LICENSEE:
				case TPX_REDACTION_AUTHORISED_BY_USER:
				{
					// the account has been authorised for redaction,
					// first set the account to inactive to prevent the user logging in, the set to queue for redaction
					$resultArray = self::queueRedaction($userID);

					break;
				}

				case TPX_REDACTION_QUEUED:
				{
					// the account is ready to start the redaction process,
					// queue the events needed for the next stages (Production, online, ftp, control centre files) and set to in progress
					$resultArray = self::initiateRedaction($userID);

					break;
				}

			}
		}
		else
		{
			$userIDArray = explode(',', $userID);

			foreach ($userIDArray as $theUser)
			{
				// an error has been sent to the process,
				// set the redaction progress to error, preventing the following stages being executed
				$resultArray = self::setRedactionError($theUser, $errorMessage);
			}
		}

		return $resultArray;
	}

	/**
	 * queueRedaction
	 *  - set the redaction progress to TPX_REDACTION_QUEUED, this will include deactivating the listed users accounts
	 *
	 * @param type $pUserIDList
	 * @return array
	 */
	static function queueRedaction($pUserIDList)
	{
		global $gSession;

		$resultArray = array('result' => '', 'resultparam' => '');

		$sql = 'UPDATE `USERS` SET `active` = 0, `redactionprogress` = ?, `redactionstate` = 0 ';
		$sql .= 'WHERE `id` IN (' . $pUserIDList . ') AND (`protectfromredaction` = 0) AND (`redactionprogress` = ? OR `redactionprogress` = ?) ';
		$sql .= ' AND `redactiondate` < now() ';

		$redactionCurrentStage = array();
		$redactionCurrentStage[0] = TPX_REDACTION_AUTHORISED_BY_LICENSEE;
		$redactionCurrentStage[1] = TPX_REDACTION_AUTHORISED_BY_USER;
		$redactionProgress = TPX_REDACTION_QUEUED;

		$rowCount = 0;

		// update the records
		$dbObj = DatabaseObj::getGlobalDBConnection();
		if ($dbObj)
		{
			if ($stmt = $dbObj->prepare($sql))
			{
				if ($stmt->bind_param('iii', $redactionProgress, $redactionCurrentStage[0], $redactionCurrentStage[1]))
				{
					if ($stmt->execute())
					{
						$rowCount = $stmt->affected_rows;
					}
					else
					{
						$resultArray['result'] = 'str_DatabaseError';
						$resultArray['resultparam'] = __FUNCTION__ . ' execute ' . $dbObj->error;
					}
				}
				else
				{
					$resultArray['result'] = 'str_DatabaseError';
					$resultArray['resultparam'] = __FUNCTION__ . ' bind_param ' . $dbObj->error;
				}

				$stmt->free_result();
				$stmt->close();
				$stmt = null;
			}
			else
			{
				$resultArray['result'] = 'str_DatabaseError';
				$resultArray['resultparam'] = __FUNCTION__ . ' prepare ' . $dbObj->error;
			}

			// if no errors have occurred, log the action and generate a trigger
			if (($rowCount > 0) && ($resultArray['result'] == ''))
			{
				$userIDArray = explode(',', $pUserIDList);

				if (empty($_SERVER['REMOTE_ADDR']))
				{
					// $_SERVER avriable not set when script is executed by cron, force a default of localhost
					$_SERVER['REMOTE_ADDR'] = '127.0.0.1';
				}

				// set defaults for activity log
				$ref = 0;
				$userlogin = '';
				$username = '';

				if (!empty($gSession))
				{
					// there is a session, use these values
					$ref = $gSession['ref'];
					$userlogin = $gSession['userlogin'];
					$username = $gSession['username'];
				}

				foreach ($userIDArray as $uid)
				{
					$actionNotes = $uid . ' Queued for redaction';
					DatabaseObj::updateActivityLog($ref, 0, $uid, $userlogin, $username, 0, 'REDACTION', 'QUEUED', $actionNotes, 1);
					$eventResultArray = DatabaseObj::createEvent('TAOPIX_DATADELETION', '', '', '', date("Y-m-d H:i:s"), 0, '', '', $uid, TPX_REDACTION_QUEUED, '', '', '', '', 0, 0, 0, '', '', $uid);
				}
			}
			else if ($rowCount == 0)
			{
				// no rows changed, keep event
				$resultArray['result'] = '0';
				$resultArray['resultparam'] = '';
			}

			$dbObj->close();
		}
		else
		{
			$resultArray['result'] = 'str_DatabaseError';
			$resultArray['resultparam'] = __FUNCTION__ . ' connect ' . $dbObj->error;
		}

		return $resultArray;
	}

	/**
	 * initiateRedaction
	 *  - set the redaction progress to TPX_REDACTION_QUEUED, this will include set the events for each of the individual task to be carried out
	 * 		production, ftp, online, control centre files
	 *
	 * @param type $pUserIDList
	 * @return array
	 */
	static function initiateRedaction($pUserIDList)
	{
		global $gSession;

		$resultArray = array('result' => '', 'resultparam' => '');

		// create and queue events
		$initialiseRedaction = false;

		$userIDArray = explode(',', $pUserIDList);

		// if no errors occured, update the user records
		if ($resultArray['result'] == '')
		{
			$sql = 'UPDATE `USERS` SET redactionprogress = ?, `redactionstate` = ?';
			$sql .= ' WHERE id IN (' . $pUserIDList . ') AND (protectfromredaction = 0) AND (redactionprogress = ?) ';

			$redactionCurrentStage = TPX_REDACTION_QUEUED;
			$redactionProgress = TPX_REDACTION_IN_PROGRESS;
			$redactionInitialState = 0;

			// update the records
			$dbObj = DatabaseObj::getGlobalDBConnection();
			if ($dbObj)
			{
				if ($stmt = $dbObj->prepare($sql))
				{
					if ($stmt->bind_param('iii', $redactionProgress, $redactionInitialState, $redactionCurrentStage))
					{
						if ($stmt->execute())
						{
							$resultArray['data']['count'] = $stmt->affected_rows;
							$initialiseRedaction = true;
						}
						else
						{
							$resultArray['result'] = 'str_DatabaseError';
							$resultArray['resultparam'] = __FUNCTION__ . ' execute ' . $dbObj->error;
						}
					}
					else
					{
						$resultArray['result'] = 'str_DatabaseError';
						$resultArray['resultparam'] = __FUNCTION__ . ' bind_param ' . $dbObj->error;
					}

					$stmt->free_result();
					$stmt->close();
					$stmt = null;
				}
				else
				{
					$resultArray['result'] = 'str_DatabaseError';
					$resultArray['resultparam'] = __FUNCTION__ . ' prepare ' . $dbObj->error;
				}

				// if no errors have occurred, log the action and generate a trigger
				if ($resultArray['result'] == '')
				{
					if (empty($_SERVER['REMOTE_ADDR']))
					{
						// $_SERVER avriable not set when script is executed by cron, force a default of localhost
						$_SERVER['REMOTE_ADDR'] = '127.0.0.1';
					}

					// set defaults for activity log
					$ref = 0;
					$userlogin = '';
					$username = '';

					if (!empty($gSession))
					{
						// there is a session, use these values
						$ref = $gSession['ref'];
						$userlogin = $gSession['userlogin'];
						$username = $gSession['username'];
					}

					$userIDArray = explode(',', $pUserIDList);
					foreach ($userIDArray as $uid)
					{
						$actionNotes = $uid . ' Redaction started';
						DatabaseObj::updateActivityLog($ref, 0, $uid, $userlogin, $username, 0, 'REDACTION', 'STARTED', $actionNotes, 1);
					}
				}

				$dbObj->close();
			}
			else
			{
				$resultArray['result'] = 'str_DatabaseError';
				$resultArray['resultparam'] = __FUNCTION__ . ' connect ' . $dbObj->error;
			}
		}

		// check if all records have been updated to the next stage
		if (count($userIDArray) > $resultArray['data']['count'])
		{
			// number of accounts passed is not the same as the number of accounts affected
			$updateResult = DatabaseObj::getUpdateStates($userIDArray, TPX_REDACTION_IN_PROGRESS);

			$resultArray['data']['updated'] = $updateResult['data']['updated'];
			$resultArray['data']['failed'] = $updateResult['data']['failed'];
		}


		// start creating events for each process
		if ($initialiseRedaction)
		{
			foreach ($userIDArray as $uid)
			{
				// create events for each of the sub tasks
				$eventResultArray = array();

				$eventResultArray[TPX_REDACTION_ONLINE] = DatabaseObj::createEvent('TAOPIX_DATADELETION', '', '', '', date("Y-m-d H:i:s"), 0, '', '', $uid, TPX_REDACTION_IN_PROGRESS, TPX_REDACTION_ONLINE, '', '', '', 0, 0, 0, '', '', $uid);
				$eventResultArray[TPX_REDACTION_PRODUCTION] = DatabaseObj::createEvent('TAOPIX_DATADELETION', '', '', '', date("Y-m-d H:i:s"), 0, '', '', $uid, TPX_REDACTION_IN_PROGRESS, TPX_REDACTION_PRODUCTION, '', '', '', 0, 0, 0, '', '', $uid);
				$eventResultArray[TPX_REDACTION_ORDER_UPLOAD] = DatabaseObj::createEvent('TAOPIX_DATADELETION', '', '', '', date("Y-m-d H:i:s"), 0, '', '', $uid, TPX_REDACTION_IN_PROGRESS, TPX_REDACTION_ORDER_UPLOAD, '', '', '', 0, 0, 0, '', '', $uid);
				$eventResultArray[TPX_REDACTION_CONTROL_CENTRE_FILES] = DatabaseObj::createEvent('TAOPIX_DATADELETION', '', '', '', date("Y-m-d H:i:s"), 0, '', '', $uid, TPX_REDACTION_IN_PROGRESS, TPX_REDACTION_CONTROL_CENTRE_FILES, '', '', '', 0, 0, 0, '', '', $uid);
			}
		}
		return $resultArray;
	}

	static function startRedactionTask($pUserIDList, $pTaskID, $pSubTaskID, $pBrandCode, $pSystemConfig)
	{
		$resultArray = array('result' => 2, 'resultparam' => '');

		switch ($pTaskID)
		{
			case TPX_REDACTION_AUTHORISED_BY_LICENSEE:
			case TPX_REDACTION_AUTHORISED_BY_USER:
			{
				// update to queued
				$resultArray = self::queueRedaction($pUserIDList);
				break;
			}

			case TPX_REDACTION_QUEUED:
			{
				// update to in progress
				$resultArray = self::initiateRedaction($pUserIDList);
				break;
			}

			case TPX_REDACTION_IN_PROGRESS:
			{
				// check sub task
				switch ($pSubTaskID)
				{
					case TPX_REDACTION_ONLINE:
					{
						// send a purge command to online to flag all projects by the listed users for imediate removal
						$resultArray = self::queuePurgeRedaction($pUserIDList, $pSystemConfig);

						break;
					}

					case TPX_REDACTION_PRODUCTION:
					{
						// place entries in the production events table to queue for production to remove
						$resultArray = self::queueProductionRedaction($pUserIDList);

						break;
					}

					case TPX_REDACTION_ORDER_UPLOAD:
					{
						// delete files via ftp for the users
						$resultArray = self::uploadFileRedaction($pUserIDList, $pSystemConfig);

						break;
					}

					case TPX_REDACTION_CONTROL_CENTRE_FILES:
					{
						// remove local files
						$resultArray = self::localFileRedaction($pUserIDList);

						break;
					}
				}

				// check the status of this stage of redaction if all passed, move to next stage
				$checkProgressResult = self::checkProgress();

				break;
			}

			case TPX_REDACTION_CONTROL_CENTRE:
			{
				// remove the control centre data
				$resultArray = self::redactControlCentreData($pUserIDList);

				break;
			}
		}

		return $resultArray;
	}


	/**
	 *
	 */
	static function checkProductionEventStatus()
	{
		$progressArray = array();
		$progressCount = 0;
		$actionCode = TPX_PRODUCTIONAUTOMATION_ACTION_ORDER_DELETELOCALDATA;
		$actionCodeData = TPX_PRODUCTIONAUTOMATION_ACTION_ORDER_DELETELOCALDATA;
		$inProgress = TPX_REDACTION_IN_PROGRESS;
		$complete = TPX_PRODUCTION_EVENT_STATUS_SUCCESSFUL;


		// select the users from the production events table with actioncode = TPX_PRODUCTIONAUTOMATION_ACTION_ORDER_DELETELOCALDATA
		// and where user has a redaction progress = TPX_REDACTION_IN_PROGRESS
		$dbObj = DatabaseObj::getGlobalDBConnection();
		if ($dbObj)
		{
			$sql = 'SELECT p.userid, p.status FROM `PRODUCTIONEVENTS` p
					JOIN `USERS` u ON u.id = p.userid
					WHERE (p.actioncode = ?) AND (u.redactionprogress = ?)';

			if ($stmt = $dbObj->prepare($sql))
			{
				if ($stmt->bind_param('ii', $actionCode, $inProgress))
				{
					if ($stmt->execute())
					{
						if ($stmt->store_result())
						{
							$progressCount = $stmt->num_rows;
							if ($progressCount > 0)
							{
								if ($stmt->bind_result($userID, $status))
								{
									while ($stmt->fetch())
									{
										// preset the success to TPX_PRODUCTION_EVENT_STATUS_SUCCESSFUL
										if (empty($progressArray[$userID]))
										{
											$progressArray[$userID] = TPX_PRODUCTION_EVENT_STATUS_SUCCESSFUL;
										}

										// if the production task is not in a success state, change the final result
										if ($status != TPX_PRODUCTION_EVENT_STATUS_SUCCESSFUL)
										{
											$progressArray[$userID] = $status;
										}
									}
								}
								else
								{
									$resultArray['result'] = 'str_DatabaseError';
									$resultArray['resultparam'] = __FUNCTION__ . ' bind_result: ' . $dbObj->error;
								}
							}
						}
						else
						{
							$resultArray['result'] = 'str_DatabaseError';
							$resultArray['resultparam'] = __FUNCTION__ . ' store_result: ' . $dbObj->error;
						}
					}
					else
					{
						$resultArray['result'] = 'str_DatabaseError';
						$resultArray['resultparam'] = __FUNCTION__ . ' execute: ' . $dbObj->error;
					}
				}
				else
				{
					$resultArray['result'] = 'str_DatabaseError';
					$resultArray['resultparam'] = __FUNCTION__ . ' bind_param: ' . $dbObj->error;
				}
				$stmt->free_result();
				$stmt->close();
				$stmt = null;
			}
			else
			{
				$resultArray['result'] = 'str_DatabaseError';
				$resultArray['resultparam'] = __FUNCTION__ . ' prepare ' . $dbObj->error;
			}

			// check for users with progress of TPX_REDACTION_IN_PROGRESS, and check for no production entries

				// select users, and check if the actions have been complete
				// if no actions for the user, set a record to flag production deletion events to complete
				$sql = 'SELECT u.id, IFNULL(p.actioncode, ?), IFNULL(p.status, ?) FROM `USERS` u
					LEFT JOIN `PRODUCTIONEVENTS` p ON u.id = p.userid
					WHERE (u.redactionprogress = ?) AND (u.redactionstate = 166)';

				if ($stmt = $dbObj->prepare($sql))
				{
					if ($stmt->bind_param('iii', $actionCode, $complete, $inProgress))
					{
						if ($stmt->execute())
						{
							if ($stmt->store_result())
							{
								$progressCount = $stmt->num_rows;
								if ($progressCount > 0)
								{
									if ($stmt->bind_result($userID, $actionCodeData, $status))
									{
										while ($stmt->fetch())
										{
											// preset the success to TPX_PRODUCTION_EVENT_STATUS_SUCCESSFUL
											if (empty($progressArray[$userID]))
											{
												$progressArray[$userID] = TPX_PRODUCTION_EVENT_STATUS_SUCCESSFUL;
											}

											if ($actionCodeData == TPX_PRODUCTIONAUTOMATION_ACTION_ORDER_DELETELOCALDATA)
											{
												// if the production task is not in a success state, change the final result
												if ($status != TPX_PRODUCTION_EVENT_STATUS_SUCCESSFUL)
												{
													$progressArray[$userID] = $status;
												}
											}
										}
									}
									else
									{
										$resultArray['result'] = 'str_DatabaseError';
										$resultArray['resultparam'] = __FUNCTION__ . ' bind_result: ' . $dbObj->error;
									}
								}
							}
							else
							{
								$resultArray['result'] = 'str_DatabaseError';
								$resultArray['resultparam'] = __FUNCTION__ . ' store_result: ' . $dbObj->error;
							}
						}
						else
						{
							$resultArray['result'] = 'str_DatabaseError';
							$resultArray['resultparam'] = __FUNCTION__ . ' execute: ' . $dbObj->error;
						}
					}
					else
					{
						$resultArray['result'] = 'str_DatabaseError';
						$resultArray['resultparam'] = __FUNCTION__ . ' bind_param: ' . $dbObj->error;
					}
					$stmt->free_result();
					$stmt->close();
					$stmt = null;
				}
				else
				{
					$resultArray['result'] = 'str_DatabaseError';
					$resultArray['resultparam'] = __FUNCTION__ . ' prepare ' . $dbObj->error;
				}


			$dbObj->close();
		}
		else
		{
			$resultArray['result'] = 'str_DatabaseError';
			$resultArray['resultparam'] = __FUNCTION__ . ' connect ' . $dbObj->error;
		}


		// check for any completed or failed production events and set the user status
		foreach ($progressArray as $userID => $userProgress)
		{
			switch ($userProgress)
			{
				case TPX_PRODUCTION_EVENT_STATUS_SUCCESSFUL:
				{
					// all events have completed successfully
					$resultArray = self::updateInProgressState(TPX_REDACTION_PRODUCTION, TPX_REDACTION_STAGE_COMPLETE, $userID);

					// check the overall progress of the file deletion, and update if all are complete
					self::checkProgress();

					break;
				}

				case TPX_PRODUCTION_EVENT_STATUS_FAILED:
				{
					// one or more events have failed
					$resultArray = self::updateInProgressState(TPX_REDACTION_PRODUCTION, TPX_REDACTION_STAGE_ERROR, $userID);

					break;
				}
			}
		}
	}


	/**
	 * checkProgress
	 * - check the progress of the redaction and if all have completed create an event to move to the final stage TPX_REDACTION_CONTROL_CENTRE
	 *
	 */
	static function checkProgress()
	{
		global $gSession;

		// populate the defaults for updates to the activity log
		if (empty($_SERVER['REMOTE_ADDR']))
		{
			// $_SERVER avriable not set when script is executed by cron, force a default of localhost
			$_SERVER['REMOTE_ADDR'] = '127.0.0.1';
		}

		// set defaults for activity log
		$ref = 0;
		$userlogin = '';
		$username = '';

		if (!empty($gSession))
		{
			// there is a session, use these values
			$ref = $gSession['ref'];
			$userlogin = $gSession['userlogin'];
			$username = $gSession['username'];
		}

		$stateCheck = TPX_REDACTION_IN_PROGRESS;

		$resultArray = array('result' => '', 'resultparam' => '');

		$userID = 0;
		$status = 0;
		$progressCount = 0;
		$redactionProgressArray = array();

		// check the records which are in progress
		$dbObj = DatabaseObj::getGlobalDBConnection();
		if ($dbObj)
		{
			$sql = 'SELECT `id`, `redactionstate` FROM `USERS` WHERE `redactionprogress` = ?';

			if ($stmt = $dbObj->prepare($sql))
			{
				if ($stmt->bind_param('i', $stateCheck))
				{
					if ($stmt->execute())
					{
						if ($stmt->store_result())
						{
							$progressCount = $stmt->num_rows;
							if ($progressCount > 0)
							{
								if ($stmt->bind_result($userID, $status))
								{
									while ($stmt->fetch())
									{
										$redactionProgressArray[$userID]['state'] = $status;
									}
								}
								else
								{
									$resultArray['result'] = 'str_DatabaseError';
									$resultArray['resultparam'] = __FUNCTION__ . ' bind_result: ' . $dbObj->error;
								}
							}
						}
						else
						{
							$resultArray['result'] = 'str_DatabaseError';
							$resultArray['resultparam'] = __FUNCTION__ . ' store_result: ' . $dbObj->error;
						}
					}
					else
					{
						$resultArray['result'] = 'str_DatabaseError';
						$resultArray['resultparam'] = __FUNCTION__ . ' execute: ' . $dbObj->error;
					}
				}
				else
				{
					$resultArray['result'] = 'str_DatabaseError';
					$resultArray['resultparam'] = __FUNCTION__ . ' bind_param: ' . $dbObj->error;
				}
				$stmt->free_result();
				$stmt->close();
				$stmt = null;
			}
			else
			{
				$resultArray['result'] = 'str_DatabaseError';
				$resultArray['resultparam'] = __FUNCTION__ . ' prepare ' . $dbObj->error;
			}
			$dbObj->close();
		}
		else
		{
			$resultArray['result'] = 'str_DatabaseError';
			$resultArray['resultparam'] = __FUNCTION__ . ' connect ' . $dbObj->error;
		}

		// check the state and process
		if ($resultArray['result'] == '')
		{
			// for all accounts determine the status and set to TPX_REDACTION_CONTROL_CENTRE if all subtasks are complete
			// or TPX_REDACTION_ERROR if any have returned an error.
			if ($progressCount > 0)
			{
				// set up sql for all users
				$sql = 'UPDATE `USERS` SET redactionprogress = ?';
				$sql .= ' WHERE (id = ?) AND (protectfromredaction = 0) AND (redactionprogress = ?) ';

				$redactionCurrentStage = TPX_REDACTION_IN_PROGRESS;
				$redactionProgress = TPX_REDACTION_CONTROL_CENTRE;

				foreach ($redactionProgressArray as $userID => $userRedaction)
				{
					// 255 = 1111 1111 all sub tasks Failed
					// 170 = 1010 1010 all sub tasks Complete
					//  85 = 0101 0101 all sub tasks in progress
					$statusValue = $userRedaction['state'];

					// all sub tasks passed, update status to TPX_REDACTION_CONTROL_CENTRE
					if ($statusValue == 170)
					{
						// update the records
						$dbObj = DatabaseObj::getGlobalDBConnection();
						if ($dbObj)
						{
							if ($stmt = $dbObj->prepare($sql))
							{
								if ($stmt->bind_param('iii', $redactionProgress, $userID, $redactionCurrentStage))
								{
									if (!$stmt->execute())
									{
										$resultArray['result'] = 'str_DatabaseError';
										$resultArray['resultparam'] = __FUNCTION__ . ' execute ' . $dbObj->error;
									}
								}
								else
								{
									$resultArray['result'] = 'str_DatabaseError';
									$resultArray['resultparam'] = __FUNCTION__ . ' bind_param ' . $dbObj->error;
								}

								$stmt->free_result();
								$stmt->close();
								$stmt = null;
							}
							else
							{
								$resultArray['result'] = 'str_DatabaseError';
								$resultArray['resultparam'] = __FUNCTION__ . ' prepare ' . $dbObj->error;
							}

							// if no errors have occurred, log the action and generate an event
							if ($resultArray['result'] == '')
							{
								$actionNotes = $userID . ' Control Centre Data Redaction Started';
								DatabaseObj::updateActivityLog($ref, 0, $userID, $userlogin, $username, 0, 'REDACTION', 'CC STARTED', $actionNotes, 1);
								DatabaseObj::createEvent('TAOPIX_DATADELETION', '', '', '', date("Y-m-d H:i:s"), 0, '', '', $userID, TPX_REDACTION_CONTROL_CENTRE, '', '', '', '', 0, 0, 0, '', '', $userID);
							}

							$dbObj->close();
						}
						else
						{
							$resultArray['result'] = 'str_DatabaseError';
							$resultArray['resultparam'] = __FUNCTION__ . ' connect ' . $dbObj->error;
						}
					}
					else
					{
						// check if any have errors
						$userRedaction['online'] = ($statusValue & 3);
						$userRedaction['production'] = ($statusValue & 12) / 4;
						$userRedaction['uploads'] = ($statusValue & 48) / 16;
						$userRedaction['ccfiles'] = ($statusValue & 192) / 64;

						$actionNotes = $userID . ' ';
						$logError = false;

						if ($userRedaction['online'] == 3)
						{
							// online purge failed
							$logError = true;
							$actionNotes .= 'Online Purge Failed, ';
						}

						if ($userRedaction['production'] == 3)
						{
							// production redaction failed
							$logError = true;
							$actionNotes .= 'Production Redaction Failed, ';
						}

						if ($userRedaction['uploads'] == 3)
						{
							// upload redaction failed
							$logError = true;
							$actionNotes .= 'Uploaded File Redaction Failed, ';
						}

						if ($userRedaction['ccfiles'] == 3)
						{
							// cc file redaction failed
							$logError = true;
							$actionNotes .= 'Control Centre File Redaction Failed, ';
						}

						// if an errors have occurred, log the action and set the error
						if ($logError)
						{
							DatabaseObj::updateActivityLog($ref, 0, $userID, $userlogin, $username, 0, 'REDACTION', 'ERROR', $actionNotes, 1);
							DatabaseObj::updateRedactionProgress(TPX_REDACTION_IN_PROGRESS, TPX_REDACTION_ERROR, $userID, '', date('Y-m-d H:i:s'), '');
						}
					}
				}
			}
		}

		return $resultArray;
	}

	/**
	 * updateProgress
	 * -
	 *
	 * @param type $pOperation
	 * @param type $pState
	 */
	static function updateInProgressState($pOperation, $pState, $pUser)
	{
		$resultArray = array('result' => '', 'resultparam' => '');

		// update the pOperation bits with the success or fail state pState
		$sql = 'UPDATE `USERS` ';
		$sql .= ' SET `redactionstate` = (`redactionstate` & ?) | (`redactionstate` & ?) | (`redactionstate` & ?) | (`redactionstate` & ?) | ?';
		$sql .= ' WHERE `id` = ?';

		$bindParams = array('iiiiii', TPX_REDACTION_CC_FILES_VALUE, TPX_REDACTION_UPLOAD_VALUE, TPX_REDACTION_PRODUCTION_VALUE, TPX_REDACTION_ONLINE_VALUE, 0, $pUser);

		// determine which bits to changes, and the new values
		switch ($pOperation)
		{
			case TPX_REDACTION_ONLINE:
				{
					$bindParams[4] = 0;
					$bindParams[5] = $pState;
					break;
				}

			case TPX_REDACTION_PRODUCTION:
				{
					$bindParams[3] = 0;
					$bindParams[5] = $pState * 4;
					break;
				}

			case TPX_REDACTION_ORDER_UPLOAD:
				{
					$bindParams[2] = 0;
					$bindParams[5] = $pState * (4 * 4);
					break;
				}

			case TPX_REDACTION_CONTROL_CENTRE_FILES:
				{
					$bindParams[1] = 0;
					$bindParams[5] = $pState * (4 * 4 * 4);
					break;
				}
		}


		$dbObj = DatabaseObj::getGlobalDBConnection();
		if ($dbObj)
		{
			if ($stmt = $dbObj->prepare($sql))
			{
				$bindOK = call_user_func_array(array(&$stmt, 'bind_param'), UtilsObj::makeValuesReferenced($bindParams));

				if ($bindOK)
				{
					if (!$stmt->execute())
					{
						$resultArray['result'] = 'str_DatabaseError';
						$resultArray['resultparam'] = __FUNCTION__ . ' execute: ' . $dbObj->error;
					}
				}
				else
				{
					$resultArray['result'] = 'str_DatabaseError';
					$resultArray['resultparam'] = __FUNCTION__ . ' bind_param: ' . $dbObj->error;
				}

				$stmt->free_result();
				$stmt->close();
				$stmt = null;
			}
			else
			{
				$resultArray['result'] = 'str_DatabaseError';
				$resultArray['resultparam'] = __FUNCTION__ . ' prepare: ' . $dbObj->error;
			}
			$dbObj->close();
		}

		return $resultArray;
	}

	/**
	 * queuePurgeRedaction
	 *  - send commands to the purge queue, flagging projects for the specified users
	 *
	 */
	static function queuePurgeRedaction($pUserIDList, $pSystemConfigArray)
	{
		global $gSession;
		global $gConstants;

		$resultArray = array('result' => 2, 'resultparam' => '');

		$projectListArray = array();
		$message = '';

		// create tasks to send to purge via TQueue
		// flag user projects, pass the details for this as a redaction job.

		$userIDArray = explode(',', $pUserIDList);

		$dateTime = new \DateTime('now', new \DateTimeZone('UTC'));

		$defaults = [
			'operation' => 'flag',
			'target' => 'projects',
			'ownercode' => $pSystemConfigArray['ownercode'],
			'tenantid' => $pSystemConfigArray['tenantid'],
			'verbose' => TPX_INTERNALTASK_PURGE_VERBOSELEVEL,
			'date' => $dateTime->format('Y-m-d H:i:s'),
			'datekey' => $dateTime->format('YmdHis'),
			'batchsize' => TPX_INTERNALTASK_PURGE_RECORDLIMIT,
			'type' => TPX_INTERNALTASK_PURGE_CLEANUP_PROJECTS,
			'config' => [
				'redaction' => [
					'age' => 0,
					'email' => 0,
					'days' => 0,
					'emailfrequency' => 0,
				],
			],
		];


		foreach ($userIDArray as $userID)
		{
			// only send commands if has online component installed
			if ($gConstants['optiondesol'])
			{
				// set the redaction state as queued for online
				$resultArray['data'][$userID] = self::updateInProgressState(TPX_REDACTION_ONLINE, TPX_REDACTION_STAGE_IN_PROGRESS, $userID);

				$resultArray['result'] = 2;
				$resultArray['resultparam'] = '';

				// Generate the redaction command.
				$commandToQueue = array_merge($defaults, ['userid' => $userID]);

				// As we may have more than one user passed here the date key needs to not collide with any others, so append the userid.
				$commandToQueue['datekey'] .= '-' . $userID;

				// send the command to the online server, and add to queue
				$queuePurgeResult = self::queuePurgeCommand($commandToQueue, $pSystemConfigArray['ownercode'], $pSystemConfigArray['systemkey']);

				if ($queuePurgeResult['result'] != '')
				{
					$resultArray['result'] = 3;
					$resultArray['resultparam'] = $queuePurgeResult['resultparam'];
				}
			}

			// set the redaction state as complete or error for online
			$resultArray['data'][$userID] = self::updateInProgressState(TPX_REDACTION_ONLINE, $resultArray['result'], $userID);
		}

		return $resultArray;
	}

	static function queuePurgeCommand($pPurgeCommandData)
	{
		global $ac_config;

		$resultArray = array();
		$result = '';
		$resultParam = '';

		$dataToEncrypt = array('directive' => $pPurgeCommandData, 'type' => 'purge');
		$pushPurgeCommandResult = CurlObj::sendByPost($ac_config['TAOPIXONLINEURL'], 'DataRetentionAPI.queueDataRetentionJob', $dataToEncrypt);

		if ($pushPurgeCommandResult['error'] == '')
		{
			if ($pushPurgeCommandResult['data']['error'] != '')
			{
				$result = $pushPurgeCommandResult['data']['error'];
				$resultParam = $pushPurgeCommandResult['data']['errorparam'];
			}
		}
		else
		{
			$result = $pushPurgeCommandResult['error'];
			$resultParam = $pushPurgeCommandResult['errorparam'];
		}

		$resultArray['result'] = $result;
		$resultArray['resultparam'] = $resultParam;

		return $resultArray;
	}


	/**
	 * queueProductionRedaction
	 *  - insert entries into the productionEvents table to allow production to delete project data
	 */
	static function queueProductionRedaction($pUserIDList)
	{
		global $gSession;
		global $gConstants;

		$resultArray = array('result' => '', 'resultparam' => '');

		$projectListArray = array();
		$actionCode = TPX_PRODUCTIONAUTOMATION_ACTION_ORDER_DELETELOCALDATA;
		$message = '';
		$actionStatus = TPX_REDACTION_STAGE_IN_PROGRESS;
		$productionRowsAdded = 0;

		$dbObj = DatabaseObj::getGlobalDBConnection();
		if ($dbObj)
		{
			// get a list of projects based on the user id, and insert them as actions in the production events table
			$sql = 'INSERT INTO `PRODUCTIONEVENTS` (`datecreated`, `userid`, `orderitemid`, `actioncode`, `message`, `status`)';
			$sql .= ' SELECT now(), `userid`, `id`, ?, ?, ? from `ORDERITEMS` WHERE `userid` IN (' . $pUserIDList . ')';

			$bindParams = array('isi', $actionCode, $message, $actionStatus);

			if ($gConstants['optionms'])
			{
				$sql = 'INSERT INTO `PRODUCTIONEVENTS` (`datecreated`, `companycode`, `owner`, `userid`, `orderitemid`, `actioncode`, `message`, `status`)';
				$sql .= ' SELECT now(), IF(`origcompanycode` = `currentcompanycode`, `origcompanycode`, `currentcompanycode`), '
						. '				IF(`origowner` = `currentowner`, `origowner`, `currentowner`), `userid`, `id`, ?, ?, ? '
						. 'FROM `ORDERITEMS` WHERE `userid` IN (' . $pUserIDList . ')';
				$sql .= ' UNION ';
				$sql .= ' SELECT now(), IF(`origcompanycode` = `currentcompanycode`, `currentcompanycode`, `origcompanycode`), '
						. '				IF(`origowner` = `currentowner`, `currentowner`, `origowner`), `userid`, `id`, ?, ?, ? '
						. 'FROM `ORDERITEMS` WHERE `userid` IN (' . $pUserIDList . ') AND (`origcompanycode` != "" AND `origowner` != "")';

				$bindParams[0] .= 'isi';
				$bindParams[] = $actionCode;
				$bindParams[] = $message;
				$bindParams[] = $actionStatus;
			}


			if ($stmt = $dbObj->prepare($sql))
			{
				$bindOK = call_user_func_array(array(&$stmt, 'bind_param'), UtilsObj::makeValuesReferenced($bindParams));

				if ($bindOK)
				{
					if (!$stmt->execute())
					{
						$resultArray['result'] = 'str_DatabaseError';
						$resultArray['resultparam'] = __FUNCTION__ . ' execute: ' . $dbObj->error;
					}
					else
					{
						// check if rows were added to the database
						$productionRowsAdded = $stmt->affected_rows;
					}
				}
				else
				{
					$resultArray['result'] = 'str_DatabaseError';
					$resultArray['resultparam'] = __FUNCTION__ . ' bind_param: ' . $dbObj->error;
				}

				$stmt->free_result();
				$stmt->close();
				$stmt = null;
			}
			else
			{
				$resultArray['result'] = 'str_DatabaseError';
				$resultArray['resultparam'] = __FUNCTION__ . ' prepare: ' . $dbObj->error;
			}

			$dbObj->close();
		}

		// set the redaction state as queued for production
		$usersToUpdateArray = explode(',', $pUserIDList);

		foreach ($usersToUpdateArray as $userID)
		{
			$resultArray['data'][$userID] = self::updateInProgressState(TPX_REDACTION_PRODUCTION, TPX_REDACTION_STAGE_IN_PROGRESS, $userID);
		}

		return $resultArray;
	}

	/**
	 * uploadFileRedaction
	 * - remove any files uploaded via ftp
	 *
	 */
	static function uploadFileRedaction($pUserIDList, $pSystemConfigArray)
	{
		global $ac_config;

		$itemID = 0;
		$orderID = 0;
		$userID = 0;
		$uploadGroupCode = '';
		$uploadOrderID = '';
		$uploadOrderNumber = '';
		$uploadRef = '';
		$productCollectionOrigOwnerCode = '';

		$resultArray = array('result' => 2, 'resultparam' => '', 'data' => array());

		$userIDArray = explode(',', $pUserIDList);
		foreach ($userIDArray as $uid)
		{
			$resultArray['data'][$uid] = array('result' => TPX_REDACTION_STAGE_COMPLETE, 'resultparam' => '');
		}

		$orderListArray = array();

		// find all projects for all users listed
		$dbObj = DatabaseObj::getGlobalDBConnection();

		if ($dbObj)
		{
			$sql = 'SELECT `id`, `orderid`, `userid`, `uploadgroupcode`, `uploadorderid`, `uploadordernumber`, `uploadref`, `productcollectionorigownercode` ';
			$sql .= ' FROM `ORDERITEMS` WHERE `userid` IN (' . $pUserIDList . ')';

			if ($stmt = $dbObj->prepare($sql))
			{
				if ($stmt->execute())
				{
					if ($stmt->store_result())
					{
						if ($stmt->num_rows > 0)
						{
							if ($stmt->bind_result($itemID, $orderID, $userID, $uploadGroupCode, $uploadOrderID, $uploadOrderNumber, $uploadRef, $productCollectionOrigOwnerCode))
							{
								while ($stmt->fetch())
								{
									$tempArray = array();

									$tempArray['id'] = $itemID;
									$tempArray['orderid'] = $orderID;
									$tempArray['userid'] = $userID;
									$tempArray['uploadgroupcode'] = $uploadGroupCode;
									$tempArray['uploadorderid'] = $uploadOrderID;
									$tempArray['uploadordernumber'] = $uploadOrderNumber;
									$tempArray['uploadref'] = $uploadRef;
									$tempArray['productcollectionorigownercode'] = $productCollectionOrigOwnerCode;

									$orderListArray[$userID][] = $tempArray;
								}
							}
							else
							{
								$resultArray['result'] = 'str_DatabaseError';
								$resultArray['resultparam'] = __FUNCTION__ . ' bind_result: ' . $dbObj->error;
							}
						}
					}
					else
					{
						$resultArray['result'] = 'str_DatabaseError';
						$resultArray['resultparam'] = __FUNCTION__ . ' store_result: ' . $dbObj->error;
					}
				}
				else
				{
					$resultArray['result'] = 'str_DatabaseError';
					$resultArray['resultparam'] = __FUNCTION__ . ' execute: ' . $dbObj->error;
				}

				$stmt->free_result();
				$stmt->close();
				$stmt = null;
			}
			else
			{
				$resultArray['result'] = 'str_DatabaseError';
				$resultArray['resultparam'] = __FUNCTION__ . ' prepare ' . $dbObj->error;
			}
			$dbObj->close();
		}
		else
		{
			$resultArray['result'] = 'str_DatabaseError';
			$resultArray['resultparam'] = __FUNCTION__ . ' connect ' . $dbObj->error;
		}

		// get the root of the upload directory from the config
		$ftpRootArray = array_filter(explode('/', $ac_config['FTPORDERSROOTPATH']), 'strlen');
		$ftpRoot = implode('/', $ftpRootArray);

		$pathConfig = array('path' => $ftpRoot, 'prefix' => 'Order');
		$ftpResult = array();

		// use the list of orders to remove all files from the ftp server
		foreach ($orderListArray as $uid => $userOrders)
		{
			$ftpResult[$uid]['result'] = 0;
			$ftpResult[$uid]['resultparam'] = '';

			foreach ($userOrders as $upload)
			{
				$ordRoot = $pathConfig['path'] . '/';

				if ($ac_config['FTPGROUPORDERSBYCODE'] == 1)
				{
					$ordRoot .= $upload['uploadgroupcode'] . '/';
				}

				$paths = [
					'uploadnumber' => $upload['uploadordernumber'],
					'uploadref' => $upload['uploadref']
					];

				$curlResult = CurlObj::ftpDeleteRecursive($ordRoot, [$paths['uploadnumber'], 'Order_' . $paths['uploadref']], 5, 30);
		
				$ftpResult[$uid][$upload['uploadordernumber']] = $curlResult;

				if ($curlResult['error'] != 0)
				{
					$ftpResult[$uid]['result'] = TPX_REDACTION_STAGE_ERROR;
					$ftpResult[$uid]['resultparam'] = $upload['uploadordernumber'] . ' - ' . $curlResult['errorparam'];
					$resultArray['data'][$uid]['result'] = TPX_REDACTION_STAGE_ERROR;
				}
			}
		}

		foreach ($userIDArray as $uid)
		{
			$resultArray['data'][$uid] = self::updateInProgressState(TPX_REDACTION_ORDER_UPLOAD, $resultArray['data'][$uid]['result'], $uid);
		}

		return $resultArray;
	}


	/**
	 * localFileRedaction
	 * - remove local files such as thumbnails
	 *
	 */
	static function localFileRedaction($pUserIDList)
	{
		global $ac_config;

		$userID = 0;
		$uploadRef = '';
		$dateCreated = '';

		$resultArray = array('result' => 2, 'resultparam' => '', 'data' => array());

		$pathsArray = array();
        $pathsArray['pages'] = UtilsObj::correctPath($ac_config['CONTROLCENTREORDERPREVIEWPATH'], DIRECTORY_SEPARATOR, true);
		$pathsArray['xml'] = implode(DIRECTORY_SEPARATOR, array('webroot', 'OrderData', 'Thumbnails', 'xml'));

		$uploadRefArray = array();
		$unlinkResult = array();

		$userIDArray = explode(',', $pUserIDList);

		foreach ($userIDArray as $uid)
		{
			$resultArray['data'][$uid] = array('result' => 2, 'resultparam' => '');
		}

		// find all projects for all users listed
		$dbObj = DatabaseObj::getGlobalDBConnection();
		if ($dbObj)
		{
			$sql = 'SELECT `userid`, `uploadref`, `datecreated` ';
			$sql .= ' FROM `ORDERITEMS` WHERE `userid` IN (' . $pUserIDList . ')';

			if ($stmt = $dbObj->prepare($sql))
			{
				if ($stmt->execute())
				{
					if ($stmt->store_result())
					{
						if ($stmt->num_rows > 0)
						{
							if ($stmt->bind_result($userID, $uploadRef, $dateCreated))
							{
								while ($stmt->fetch())
								{
									$uploadRefArray[$userID][] = [
                                        'ref' => $uploadRef,
                                        'created' => strtotime($dateCreated)
                                    ];
								}
							}
							else
							{
								$resultArray['result'] = 'str_DatabaseError';
								$resultArray['resultparam'] = __FUNCTION__ . ' bind_result: ' . $dbObj->error;
							}
						}
					}
					else
					{
						$resultArray['result'] = 'str_DatabaseError';
						$resultArray['resultparam'] = __FUNCTION__ . ' store_result: ' . $dbObj->error;
					}
				}
				else
				{
					$resultArray['result'] = 'str_DatabaseError';
					$resultArray['resultparam'] = __FUNCTION__ . ' execute: ' . $dbObj->error;
				}

				$stmt->free_result();
				$stmt->close();
				$stmt = null;
			}
			else
			{
				$resultArray['result'] = 'str_DatabaseError';
				$resultArray['resultparam'] = __FUNCTION__ . ' prepare: ' . $dbObj->error;
			}

			$dbObj->close();
		}
		else
		{
			$resultArray['result'] = 'str_DatabaseError';
			$resultArray['resultparam'] = __FUNCTION__ . ' connect: ' . $dbObj->error;
		}

		// if a list of order items has been obtained
		if ($resultArray['result'] == 2)
		{
			// use the list of orders to remove all files
			foreach ($uploadRefArray as $userID => $uploadRefList)
			{
				$unlinkResult[$userID]['result'] = '';
				$unlinkResult[$userID]['resultparam'] = '';

				foreach ($uploadRefList as $uploadData)
				{
                    // Create the path for the uploaded thumbnails.
                    $uploadRef = $uploadData['ref'];
                    $pageDir = UtilsObj::correctPath($pathsArray['pages'] . date('Y/m/d/H/', $uploadData['created']) . $uploadRef, DIRECTORY_SEPARATOR, true);

					// delete files and log to remove directory entries in orderthumbnails
					$unlinkResult[$userID][$uploadRef]['result'] = '';
					$unlinkResult[$userID][$uploadRef]['resultparam'] = '';
					$unlinkResult[$userID][$uploadRef]['deleted'] = array();

					// delete the thumbnails
					if (file_exists($pageDir))
					{
						// list all files in the directory and delete
						$pageFiles = array_diff(scandir($pageDir), array('..', '.'));

						foreach ($pageFiles as $fileName)
						{
							if (unlink($pageDir . $fileName))
							{
								$unlinkResult[$userID][$uploadRef]['data']['deleted'][] = $fileName;
							}
							else
							{
								$unlinkResult[$userID][$uploadRef]['result'] = 1;
								$unlinkResult[$userID][$uploadRef]['resultparam'] = 'Failed to delete ' . $fileName;
								$unlinkResult[$userID][$uploadRef]['data']['failed'][] = $fileName;
							}
						}

						// delete the directory
						if (rmdir($pageDir))
						{
							$unlinkResult[$userID][$uploadRef]['data']['deleted'][] = $pageDir;
						}
						else
						{
							$unlinkResult[$userID][$uploadRef]['result'] = 1;
							$unlinkResult[$userID][$uploadRef]['resultparam'] = 'Failed to delete directory ' . $uploadRef;
							$unlinkResult[$userID][$uploadRef]['data']['failed'][] = $pageDir;
						}
					}
					else
					{
						// log the directory as removed as it does not exist
						$unlinkResult[$userID][$uploadRef]['data']['deleted'][] = $pageDir;
					}

					// delete the xml file used for page turning
					$xmlFile = '..' . DIRECTORY_SEPARATOR . $pathsArray['xml'] . DIRECTORY_SEPARATOR . $uploadRef . '.xml';
					if (file_exists($xmlFile))
					{
						// delete the xml data file
						if (unlink($xmlFile))
						{
							$unlinkResult[$userID][$uploadRef]['data']['deleted'][] = $xmlFile;
						}
						else
						{
							$unlinkResult[$userID][$uploadRef]['result'] = 1;
							$unlinkResult[$userID][$uploadRef]['resultparam'] = 'Failed to delete ' . $xmlFile;
							$unlinkResult[$userID][$uploadRef]['data']['failed'][] = $xmlFile;
						}
					}
					else
					{
						// file does not exist, mark as deleted
						$unlinkResult[$userID][$uploadRef]['data']['deleted'][] = $xmlFile;
					}

					// if no errors during file removal, make list of project refs to remove from the database
					if ($unlinkResult[$userID][$uploadRef]['result'] == 0)
					{
						// add " around ref for use in sql
						$unlinkResult[$userID]['deleted'][] = '"' . $uploadRef . '"';
					}
				}
			}

			$dbObj = DatabaseObj::getGlobalDBConnection();
			if ($dbObj)
			{
				// for each of the users, use the list of deleted projects to remove the entries from the database
				foreach ($unlinkResult as $userID => $unlinkUserResult)
				{
					if (count($unlinkUserResult['deleted']) > 0)
					{
						$uploadRefList = implode(',', $unlinkUserResult['deleted']);

						$sql = 'DELETE FROM `ORDERTHUMBNAILS` WHERE `uploadref` IN (' . $uploadRefList . ')';

						if ($stmt = $dbObj->prepare($sql))
						{
							if ($stmt->execute())
							{
								$unlinkResult[$userID]['count'] = $stmt->affected_rows;
							}
							else
							{
								$unlinkResult[$userID]['result'] = 'str_DatabaseError';
								$unlinkResult[$userID]['resultparam'] = __FUNCTION__ . ' execute (Delete): ' . $dbObj->error;
							}

							$stmt->free_result();
							$stmt->close();
							$stmt = null;
						}
						else
						{
							$unlinkResult[$userID]['result'] = 'str_DatabaseError';
							$unlinkResult[$userID]['resultparam'] = __FUNCTION__ . ' prepare (Delete): ' . $dbObj->error;
						}
					}

					// set the redaction state based on the result of the unlink
					if ($unlinkResult[$userID]['result'] != '')
					{
						$resultArray['data'][$userID]['result'] = TPX_REDACTION_STAGE_ERROR;
					}
				}

				$dbObj->close();
			}
		}

		foreach ($userIDArray as $uid)
		{
			if ($resultArray['data'][$uid]['result'] !== TPX_REDACTION_STAGE_ERROR)
			{
				$desktopProjectThumbnailResultArray = self::deleteDesktopProjectThumbnails($uid);

				if ($desktopProjectThumbnailResultArray['error'] === '')
				{
					// update the progress of the redaction for the current user
					$resultArray['data'][$userID]['result'] = TPX_REDACTION_STAGE_COMPLETE;
				}
				else
				{
					$resultArray['data'][$userID]['result'] = TPX_REDACTION_STAGE_ERROR;
				}
			}
		}

		foreach ($userIDArray as $uid)
		{
			$resultArray['data'][$uid] = self::updateInProgressState(TPX_REDACTION_CONTROL_CENTRE_FILES, $resultArray['data'][$uid]['result'], $uid);
		}

		return $resultArray;
	}


	/**
	 * redactControlCentreData
	 * - delete all data from the control centre for the specified user
	 *
	 */
	static function redactControlCentreData($pUserID)
	{
		global $ac_config;

		$fieldName = '';

		$metadataFields = array();

		$resultArray = array('result' => '', 'resultparam' => '');

		// find all projects for all users listed
		$dbObj = DatabaseObj::getGlobalDBConnection();
		if ($dbObj)
		{
			// get the field names of the metadata table
			$sql = "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = '" . $ac_config['DBNAME'] . "' AND TABLE_NAME = 'METADATA' AND COLUMN_NAME LIKE 'keyword%'";

			if ($stmt = $dbObj->prepare($sql))
			{
				if ($stmt->execute())
				{
					if ($stmt->store_result())
					{
						$keyColCount = $stmt->num_rows;
						if ($keyColCount > 0)
						{
							if ($stmt->bind_result($fieldName))
							{
								while ($stmt->fetch())
								{
									$metadataFields[] = $fieldName;
								}
							}
							else
							{
								$resultArray['result'] = 'str_DatabaseError';
								$resultArray['resultparam'] = __FUNCTION__ . ' bind_result: ' . $dbObj->error;
							}
						}
					}
					else
					{
						$resultArray['result'] = 'str_DatabaseError';
						$resultArray['resultparam'] = __FUNCTION__ . ' store_result: ' . $dbObj->error;
					}
				}
				else
				{
					$resultArray['result'] = 'str_DatabaseError';
					$resultArray['resultparam'] = __FUNCTION__ . ' execute: ' . $dbObj->error;
				}

				$stmt->free_result();
				$stmt->close();
				$stmt = null;
			}
			else
			{
				$resultArray['result'] = 'str_DatabaseError';
				$resultArray['resultparam'] = __FUNCTION__ . ' prepare ' . $dbObj->error;
			}

			// build the queries to execute on the control centre data base
			if ($keyColCount > 0)
			{
				$queryArray['METADATA']['sql'] = 'UPDATE `METADATA` SET ';
				for ($kcc = 0; $kcc < $keyColCount; $kcc++)
				{
					if ($kcc > 0)
					{
						$queryArray['METADATA']['sql'] .= ',';
					}
					$queryArray['METADATA']['sql'] .= '`' . $metadataFields[$kcc] . '` = ""';
				}

				$queryArray['METADATA']['sql'] .= ' WHERE `userid` = ?';
			}

			$queryArray['SHAREDITEMS']['sql'] = 'DELETE FROM `SHAREDITEMS` WHERE `userid` = ?';

			$queryArray['ONLINEBASKET']['sql'] = 'DELETE FROM `ONLINEBASKET` WHERE `userid` = ?';

			$queryArray['EVENTS']['sql'] = 'DELETE FROM `EVENTS` WHERE `targetuserid` = ?';

			$queryArray['ORDERSHIPPING']['sql'] = 'UPDATE `ORDERSHIPPING` SET `shippingcustomername` = "", `shippingcustomeraddress1` = "", '
					. '`shippingcustomeraddress2` = "",	`shippingcustomeraddress3` = "", `shippingcustomeraddress4` = "", `shippingcustomerpostcode` = "", '
					. ' `shippingcustomertelephonenumber` = "", `shippingcustomeremailaddress` = "", `shippingcontactfirstname` = "", '
					. '`shippingcontactlastname` = "" '
					. ' WHERE `userid` = ?';

			$queryArray['ORDERITEMCOMPONENTS']['sql'] = 'UPDATE `ORDERITEMCOMPONENTS` SET `setname` = CONCAT("Set ", setid)'
					. ' WHERE `userid` = ? AND `setid` > 0';

			$queryArray['ORDERITEMS']['sql'] = 'UPDATE `ORDERITEMS` SET `shareid` = 0, `projectname` = "", `jobticketoutputsubfoldername` = "", '
					. ' `pagesoutputsubfoldername` = "", `cover1outputsubfoldername` = "", `cover2outputsubfoldername` = "", `xmloutputsubfoldername` = "", '
					. ' `jobticketoutputfilename` = "", `pagesoutputfilename` = "", `cover1outputfilename` = "", `cover2outputfilename` = "", '
					. ' `xmloutputfilename` = "", '
					. ' `previewsonline` = "0" '
					. ' WHERE `userid` = ?';

			$queryArray['ORDERHEADER']['sql'] = 'UPDATE `ORDERHEADER` SET `designeruuid` = "", `useripaddress` = "", `billingcustomeraccountcode` = "", '
					. ' `billingcustomername` = "", `billingcustomeraddress1` = "", `billingcustomeraddress2` = "", `billingcustomeraddress3` = "", '
					. ' `billingcustomeraddress4` = "", `billingcustomerpostcode` = "", `billingcustomertelephonenumber` = "", '
					. ' `billingcustomeremailaddress` = "", `billingcontactfirstname` = "", `billingcontactlastname` = "", '
					. ' `billingcustomerregisteredtaxnumbertype` = "", `billingcustomerregisteredtaxnumber` = "" '
					. ' WHERE `userid` = ?';

			$queryArray['CCILOG']['sql'] = 'UPDATE `CCILOG` SET `transactionid` = "", `authorisationid` = "", `cardnumber` = "", `cvvflag` = "", '
					. ' `payeremail` = "", `payerid` = "", `formattedtransactionid` = "", `formattedauthorisationid` = "", `formattedcardnumber` = "" '
					. ' WHERE `userid` = ?';

			$queryArray['ACTIVITYLOG']['sql'] = 'UPDATE `ACTIVITYLOG` SET `ipaddress` = "", `userlogin` = `userid`, `username` = ""'
					. ' WHERE `userid` = ?';

			$queryArray['USERS']['sql'] = 'UPDATE `USERS` SET `login` = `id`, `password` = "", `accountcode` = "", `companyname` = "", `address1` = "", '
					. '`address2` = "", `address3` = "", `address4` = "", `postcode` = "", `telephonenumber` = "", `emailaddress` = "", `contactfirstname` = "", '
					. '`contactlastname` = "", `registeredtaxnumbertype` = "", `registeredtaxnumber` = 0, `sendmarketinginfo` = "" '
					. ' WHERE `id` = ?';


			// execute the queries in a transaction, any errors, rollback
            $dbObj->query('START TRANSACTION');

			// loop around the query array, break on failure
			foreach ($queryArray as $tableName => $tableData)
			{
				$sql = $tableData['sql'];

				if ($stmt = $dbObj->prepare($sql))
				{
					if ($stmt->bind_param('i', $pUserID))
					{
						if ($stmt->execute())
						{
							$resultArray['count'] = $stmt->affected_rows;
							$resultArray['result'] = '';
							$resultArray['resultparam'] = '';
						}
						else
						{
							$resultArray['result'] = 'str_DatabaseError';
							$resultArray['resultparam'] = __FUNCTION__ . ' execute (' . $tableName . '): ' . $dbObj->error;
						}
					}
					else
					{
						$resultArray['result'] = 'str_DatabaseError';
						$resultArray['resultparam'] = __FUNCTION__ . ' bind_param (' . $tableName . '): ' . $dbObj->error;
					}
					$stmt->free_result();
					$stmt->close();
					$stmt = null;
				}
				else
				{
					$resultArray['result'] = 'str_DatabaseError';
					$resultArray['resultparam'] = __FUNCTION__ . ' prepare (' . $tableName . '): ' . $dbObj->error;
				}

				if ($resultArray['result'] != '')
				{
					$dbObj->query('ROLLBACK');
					break;
				}
			}


			if ($resultArray['result'] == '')
			{
				// update the redaction state
				$sql = 'UPDATE `USERS` SET `redactionprogress` = ?';
				$sql .= ' WHERE `id` = ? AND (`protectfromredaction` = 0) AND (`redactionprogress` = ?)';

				$redactionCurrentStage = TPX_REDACTION_CONTROL_CENTRE;
				$redactionProgress = TPX_REDACTION_COMPLETE;

				// update the records
				if ($stmt = $dbObj->prepare($sql))
				{
					if ($stmt->bind_param('iii', $redactionProgress, $pUserID, $redactionCurrentStage))
					{
						if (!$stmt->execute())
						{
							$resultArray['result'] = 'str_DatabaseError';
							$resultArray['resultparam'] = __FUNCTION__ . ' execute ' . $dbObj->error;
						}
					}
					else
					{
						$resultArray['result'] = 'str_DatabaseError';
						$resultArray['resultparam'] = __FUNCTION__ . ' bind_param ' . $dbObj->error;
					}

					$stmt->free_result();
					$stmt->close();
					$stmt = null;
				}
				else
				{
					$resultArray['result'] = 'str_DatabaseError';
					$resultArray['resultparam'] = __FUNCTION__ . ' prepare ' . $dbObj->error;
				}

			}

			// if no errors, commit the transaction
			if ($resultArray['result'] == '')
			{
				$dbObj->query('COMMIT');
			}
			else
			{
				$dbObj->query('ROLLBACK');
			}

            $dbObj->close();
		}

		return $resultArray;
	}


	static function flagUnusedAccounts()
	{
		$userID = 0;
		$brandCode = '';
		$login = '';
		$emailAddress = '';
		$contactFirstName = '';
		$contactLastName = '';
		$redactionNotificationDays = 0;
		$brandName = '';
		$displayURL = '';
		$redactionDate = '0000-00-00 00:00:00';

		$autoRedactionCount = 0;
		$autoRedactionArray = array();
		$automaticredactiondays = 0;

		$resultArray = array();
		$resultArray['result'] = 2;
		$resultArray['resultparam'] = '';

		$dbObj = DatabaseObj::getGlobalDBConnection();
		if ($dbObj)
		{
			// look up account eligable for automatic redaction
			$sql = 'SELECT `u`.`id`, `u`.`webbrandcode`, `u`.`login`, `u`.`emailaddress`, `u`.`contactfirstname`, `u`.`contactlastname`,
						`b`.`name`, `b`.`displayurl`, `b`.`redactionnotificationdays`, `automaticredactiondays`, DATE_ADD(now(), INTERVAL `redactionnotificationdays` DAY)
					FROM `USERS` `u`
					LEFT JOIN `BRANDING` `b` ON `u`.`webbrandcode` = `b`.`code`
					WHERE
						`u`.`customer` = 1 AND
						`b`.`automaticredactionenabled` = 1 AND
						`u`.`redactionprogress` = ? AND
						`u`.`protectfromredaction` = 0 AND
						`u`.`datecreated` < DATE_SUB(NOW(), INTERVAL `automaticredactiondays` DAY) AND
						`u`.`lastlogindate` < DATE_SUB(NOW(), INTERVAL `automaticredactiondays` DAY)';

			$bindParam = TPX_REDACTION_NONE;

			if ($stmt = $dbObj->prepare($sql))
			{
				if ($stmt->bind_param('i', $bindParam))
				{
					if ($stmt->execute())
					{
						if ($stmt->store_result())
						{
							$autoRedactionCount = $stmt->num_rows;
							if ($autoRedactionCount > 0)
							{
								if ($stmt->bind_result($userID, $brandCode, $login, $emailAddress, $contactFirstName, $contactLastName, $brandName, $displayURL, $redactionNotificationDays, $automaticredactiondays, $redactionDate))
								{
									while ($stmt->fetch())
									{
										$tempArray = array();
										$tempArray['id'] = $userID;
										$tempArray['brandcode'] = $brandCode;
										$tempArray['brandname'] = $brandName;
										$tempArray['displayurl'] = $displayURL;
										$tempArray['login'] = $login;
										$tempArray['emailaddress'] = $emailAddress;
										$tempArray['contactfirstname'] = $contactFirstName;
										$tempArray['contactlastname'] = $contactLastName;
										$tempArray['contactfullname'] = $contactFirstName . ' ' . $contactLastName;
										$tempArray['redactiondays'] = $automaticredactiondays;
										$tempArray['redactionnotificationdays'] = $redactionNotificationDays;
										$tempArray['redactiondate'] = $redactionDate;
										$tempArray['redactiondatelocal'] = LocalizationObj::formatLocaleDateTime($tempArray['redactiondate'], '');
										$tempArray['targetuserid'] = $userID;

										$autoRedactionArray[] = $tempArray;
									}
								}
								else
								{
									$resultArray['result'] = 'str_DatabaseError';
									$resultArray['resultparam'] = __FUNCTION__ . ' bind_result: ' . $dbObj->error;
								}
							}
						}
						else
						{
							$resultArray['result'] = 'str_DatabaseError';
							$resultArray['resultparam'] = __FUNCTION__ . ' store_result: ' . $dbObj->error;
						}
					}
					else
					{
						$resultArray['result'] = 'str_DatabaseError';
						$resultArray['resultparam'] = __FUNCTION__ . ' execute: ' . $dbObj->error;
					}
				}
				else
				{
					$resultArray['result'] = 'str_DatabaseError';
					$resultArray['resultparam'] = __FUNCTION__ . ' execute: ' . $dbObj->error;
				}

				$stmt->free_result();
				$stmt->close();
				$stmt = null;
			}
			else
			{
				$resultArray['result'] = 'str_DatabaseError';
				$resultArray['resultparam'] = __FUNCTION__ . ' prepare: ' . $dbObj->error;
			}

			$dbObj->close();
		}
		else
		{
			$resultArray['result'] = 'str_DatabaseError';
			$resultArray['resultparam'] = __FUNCTION__ . ' connect: ' . $dbObj->error;
		}

		if (($autoRedactionCount > 0) && ($resultArray['result'] == 2))
		{
			// accounts have been found eligable for automatic redaction, flag and send email
			// flag accounts
			foreach ($autoRedactionArray as $redactAccount)
			{
				$updateResult = DatabaseObj::updateRedactionProgress(TPX_REDACTION_NONE, TPX_REDACTION_AUTHORISED_BY_LICENSEE, $redactAccount['id'], '', $redactAccount['redactiondate'], 'Automatic');

				if ($updateResult['result'] != '')
				{
					$resultArray['result'] = 1;
					$resultArray['resultparam'] = $updateResult['resultparam'];
				}
				else
				{
					// send emails
					$emailObj = new TaopixMailer();
					$emailObj->sendTemplateEmail('customer_flaggedforredaction', $redactAccount['brandcode'], $redactAccount['brandname'],
												$redactAccount['displayurl'], '', $redactAccount['contactfullname'],
												$redactAccount['emailaddress'], '', '', 0, $redactAccount);
				}
			}
		}

		return $resultArray;
	}


	static function queueFlaggedAccounts()
	{
		$userID = 0;
		$brandCode = '';
		$login = '';
		$emailAddress = '';
		$contactFirstName = '';
		$contactLastName = '';
		$redactionProgress = 0;
		$brandName = '';
		$displayURL = '';
		$redactionDate = '0000-00-00 00:00:00';

		$autoRedactionCount = 0;
		$autoRedactionArray = array();

		$resultArray = array();
		$resultArray['result'] = 2;
		$resultArray['resultparam'] = '';

		$dbObj = DatabaseObj::getGlobalDBConnection();
		if ($dbObj)
		{
			// look up account authorised for redaction and past the redaction date
			$sql = 'SELECT `u`.`id`, `u`.`webbrandcode`, `u`.`login`, `u`.`emailaddress`, `u`.`contactfirstname`, `u`.`contactlastname`,
						`b`.`name`, `b`.`displayurl`, `u`.`redactionprogress`, `u`.`redactiondate`
					FROM `USERS` `u`
					LEFT JOIN `BRANDING` `b` ON `u`.`webbrandcode` = `b`.`code`
					WHERE
						`u`.`customer` = 1 AND
						`b`.`automaticredactionenabled` = 1 AND
						((`u`.`redactionprogress` = ?) OR (`u`.`redactionprogress` = ?)) AND
						`u`.`protectfromredaction` = 0 AND
						`u`.`redactiondate` < NOW()';

			$bindParam = array(TPX_REDACTION_AUTHORISED_BY_LICENSEE, TPX_REDACTION_AUTHORISED_BY_USER);

			if ($stmt = $dbObj->prepare($sql))
			{
				if ($stmt->bind_param('ii', $bindParam[0], $bindParam[1]))
				{
					if ($stmt->execute())
					{
						if ($stmt->store_result())
						{
							$autoRedactionCount = $stmt->num_rows;
							if ($autoRedactionCount > 0)
							{
								if ($stmt->bind_result($userID, $brandCode, $login, $emailAddress, $contactFirstName, $contactLastName, $brandName, $displayURL, $redactionProgress, $redactionDate))
								{
									while ($stmt->fetch())
									{
										$tempArray = array();
										$tempArray['id'] = $userID;
										$tempArray['brandcode'] = $brandCode;
										$tempArray['brandname'] = $brandName;
										$tempArray['displayurl'] = $displayURL;
										$tempArray['login'] = $login;
										$tempArray['emailaddress'] = $emailAddress;
										$tempArray['contactfirstname'] = $contactFirstName;
										$tempArray['contactlastname'] = $contactLastName;
										$tempArray['contactfullname'] = $contactFirstName . ' ' . $contactLastName;
										$tempArray['redactionprogress'] = $redactionProgress;
										$tempArray['redactiondate'] = $redactionDate;
										$tempArray['redactiondatelocal'] = LocalizationObj::formatLocaleDateTime($tempArray['redactiondate'], '');

										$autoRedactionArray[] = $tempArray;
									}
								}
								else
								{
									$resultArray['result'] = 'str_DatabaseError';
									$resultArray['resultparam'] = __FUNCTION__ . ' bind_result: ' . $dbObj->error;
								}
							}
						}
						else
						{
							$resultArray['result'] = 'str_DatabaseError';
							$resultArray['resultparam'] = __FUNCTION__ . ' store_result: ' . $dbObj->error;
						}
					}
					else
					{
						$resultArray['result'] = 'str_DatabaseError';
						$resultArray['resultparam'] = __FUNCTION__ . ' execute: ' . $dbObj->error;
					}
				}
				else
				{
					$resultArray['result'] = 'str_DatabaseError';
					$resultArray['resultparam'] = __FUNCTION__ . ' bind_param: ' . $dbObj->error;
				}

				$stmt->free_result();
				$stmt->close();
				$stmt = null;
			}
			else
			{
				$resultArray['result'] = 'str_DatabaseError';
				$resultArray['resultparam'] = __FUNCTION__ . ' prepare: ' . $dbObj->error;
			}
			$dbObj->close();
		}
		else
		{
			$resultArray['result'] = 'str_DatabaseError';
			$resultArray['resultparam'] = __FUNCTION__ . ' connect: ' . $dbObj->error;
		}

		if (($autoRedactionCount > 0) && ($resultArray['result'] == 2))
		{
			// accounts have been found scheduled for redaction, queue the accounts
			foreach ($autoRedactionArray as $redactAccount)
			{
				$queueResult = self::queueRedaction($redactAccount['id']);

				if ($queueResult['result'] != '')
				{
					$resultArray['result'] = 1;
					$resultArray['resultparam'] = $queueResult['resultparam'];
				}
			}
		}

		return $resultArray;
	}


	static function canRedactAccounts($pUserIDArray, $pBypassSession)
    {
        $error = '';
        $resultParam = '';
        $resultArray = array('error' => '', 'errorparam' => '', 'redact' => array(), 'disallow' => array(), 'session' => array(), 'order' => array());

        $controlCentreSession = array();

        $checkStatus = TPX_ORDER_STATUS_IN_PROGRESS;

        // check the accounts do not have orders in production
        $dbObj = DatabaseObj::getGlobalDBConnection();

        if ($dbObj)
        {
            // get a list of all projects linked to the specified user, still in progress
            $sql = 'SELECT `id`, `uploadorderitemid`, `userid`, `active`
                    FROM `ORDERITEMS`
                    WHERE `uploadorderitemid` IN (SELECT `id`
                                                  FROM `ORDERITEMS`
                                                  WHERE `userid` = ?)
                        AND (`active` = ?)';

            if ($stmt = $dbObj->prepare($sql))
            {
                foreach ($pUserIDArray as $userToCheck)
                {
                    if ($stmt->bind_param('ii', $userToCheck, $checkStatus))
                    {
                        if ($stmt->execute())
                        {
                            if ($stmt->store_result())
                            {
                                if ($stmt->num_rows > 0)
                                {
                                    // can not delete
                                    $resultArray['disallow'][] = array('id' => $userToCheck, 'reason' => 'orders');
                                }
                                else
                                {
                                    // No active projects, check sessions
									$controlCentreSession[] = $userToCheck;
                                }
                            }
                            else
                            {
                                $error = 'str_DatabaseError';
                                $resultParam = __FUNCTION__ . ' store_result: ' . $dbObj->error;
                            }
                        }
                        else
                        {
                            $error = 'str_DatabaseError';
                            $resultParam = __FUNCTION__ . ' execute ' . $dbObj->error;
                        }
                    }
                    else
                    {
                        // could not bind parameters
                        $error = 'str_DatabaseError';
                        $resultParam = __FUNCTION__ . ' bind_param ' . $dbObj->error;
                    }
                    $stmt->free_result();
                }
                $stmt->close();
                $stmt = null;
            }
            else
            {
                // could not prepare statement
                $error = 'str_DatabaseError';
                $resultParam = __FUNCTION__ . ' prepare ' . $dbObj->error;
            }

            $dbObj->close();
        }
        else
        {
            // could not open database connection
            $error = 'str_DatabaseError';
            $resultParam = __FUNCTION__ . ' connect ' . $dbObj->error;
        }


        if ($error == '')
        {
            // check for active sessions in control centre
            if (count($controlCentreSession) > 0)
            {
				foreach ($controlCentreSession as $userIDSession)
				{
					if ($pBypassSession)
					{
						$resultArray['redact'][] = $userIDSession;
					}
					else
					{
						$activeSessionsData = AuthenticateObj::userSessionActive($userIDSession);

						if ($activeSessionsData['sessionactive'] == 1)
						{
							// active session found can not delete
							$resultArray['disallow'][] = array('id' => $userIDSession, 'reason' => 'session');
						}
						else
						{
							$resultArray['redact'][] = $userIDSession;
						}
					}
				}
			}
		}
        $customerListDisallowCount = count($resultArray['disallow']);

        // provides customer name for error message
        if ($customerListDisallowCount > 0)
        {
            for ($i = 0; $i < $customerListDisallowCount; $i++)
            {
                $userData = DatabaseObj::getUserAccountFromID($resultArray['disallow'][$i]['id']);

                if ($resultArray['disallow'][$i]['reason'] == 'session')
                {
                    $resultArray['session'][] = $userData['contactfirstname'] . ' ' . $userData['contactlastname'];
                }
                else
                {
                    $resultArray['order'][] = $userData['contactfirstname'] . ' ' . $userData['contactlastname'];
                }
            }
        }
        $resultArray['error'] = $error;
        $resultArray['errorparam'] = $resultParam;

        return $resultArray;
    }

	static function deleteDesktopProjectThumbnails($pUserID)
	{
		$resultArray = UtilsObj::getReturnArray();
		$error = '';
		$errorParam = '';

		//get a list of projectrefs with thumbnails to delete
		$projectRefResultArray = DatabaseObj::getDesktopProjectThumbnailsFromUserID($pUserID);

		if ($projectRefResultArray['error'] === '')
		{
			$projectRefArray = $projectRefResultArray['projectrefs'];
			
			if (count($projectRefArray) > 0)
			{
				$deletionResultArray = UtilsObj::deleteDesktopProjectThumbnails($projectRefArray);
				$error = $deletionResultArray['error'];
				$errorParam = $deletionResultArray['errorparam'];
			}
		}
		else
		{
			$error = $projectRefResultArray['error'];
			$errorParam = $projectRefResultArray['errorparam'];
		}

		$resultArray['error'] = $error;
		$resultArray['errorparam'] = $errorParam;

		return $resultArray;
	}

	static function redactOrderDeletionInformation($pOrderId, $pDbObj)
	{
		$queries = [
			'DELETE FROM `SHAREDITEMS` WHERE `orderid` = ?',
			'UPDATE `ORDERSHIPPING` SET `shippingcustomername` = "", `shippingcustomeraddress1` = "", `shippingcustomeraddress2` = "", '
			. '`shippingcustomeraddress3` = "", `shippingcustomeraddress4` = "", `shippingcustomerpostcode` = "", `shippingcustomertelephonenumber` = "", '
			. '`shippingcustomeremailaddress` = "", `shippingcontactfirstname` = "", `shippingcontactlastname` = "" WHERE `orderid` = ?',
			'UPDATE `ORDERITEMS` SET `shareid` = 0, `projectname` = "", `jobticketoutputsubfoldername` = "", '
			. ' `pagesoutputsubfoldername` = "", `cover1outputsubfoldername` = "", `cover2outputsubfoldername` = "", `xmloutputsubfoldername` = "", '
			. ' `jobticketoutputfilename` = "", `pagesoutputfilename` = "", `cover1outputfilename` = "", `cover2outputfilename` = "", '
			. ' `xmloutputfilename` = "", `previewsonline` = "0" WHERE `orderid` = ?',
			'UPDATE `ORDERHEADER` SET `designeruuid` = "", `useripaddress` = "", `billingcustomeraccountcode` = "", '
			. ' `billingcustomername` = "", `billingcustomeraddress1` = "", `billingcustomeraddress2` = "", `billingcustomeraddress3` = "", '
			. ' `billingcustomeraddress4` = "", `billingcustomerpostcode` = "", `billingcustomertelephonenumber` = "", '
			. ' `billingcustomeremailaddress` = "", `billingcontactfirstname` = "", `billingcontactlastname` = "", '
			. ' `billingcustomerregisteredtaxnumbertype` = "", `billingcustomerregisteredtaxnumber` = "", `redacted` = 1 WHERE `id` = ?',
			'UPDATE `METADATAVALUES` SET `value` = "" WHERE metadataid IN (SELECT `id` FROM `METADATA` WHERE `orderid`=?)',
			'UPDATE `CCILOG` SET `transactionid` = "", `authorisationid` = "", `cardnumber` = "", `cvvflag` = "", '
			. ' `payeremail` = "", `payerid` = "", `formattedtransactionid` = "", `formattedauthorisationid` = "", `formattedcardnumber` = "" '
			. ' WHERE `orderid` = ?',
		];

		foreach ($queries as $key => $query)
		{
			$stmt = $pDbObj->prepare($query);

			if (false === $stmt)
			{
				throw new Exception(__METHOD__ . ' Prepare ' . $key . ': ' . $pDbObj->error);
			}

			if (! $stmt->bind_param('i', $pOrderId))
			{
				throw new Exception(__METHOD__ . ' Bind param ' . $key . ': ' . $pDbObj->error);
			}

			if (! $stmt->execute())
			{
				throw new Exception(__METHOD__ . ' Execute ' . $key . ': ' . $pDbObj->error);
			}
		}
	}

	static function queueOrderDeletionPurgeRedaction($pOnlineProjects)
	{
		$systemConf = DatabaseObj::getSystemConfig();

		$dateTime = new DateTime('now', new DateTimeZone('UTC'));
		$commandBase = [
			'ownercode' => '',
			'tenantid' => $systemConf['tenantid'],
			'verbose' => 0,
			'date' => $dateTime->format('Y-m-d H:i:s'),
			'datekey' => $dateTime->format('YmdHis'),
			'batchsize' => 1000,
			'operation' => 'flag',
			'target' => 'projects',
		];

		foreach ($pOnlineProjects as $projectData)
		{
			// Copy the command base as a new command.
			$command = array_merge([], $commandBase);
			$command['projectref'] = $projectData['projectref'];
			$command['ownercode'] = $projectData['ownercode'];
			$command['licensekeys'] = [$projectData['groupcode']];

			$queueCommandResult = self::queuePurgeCommand($command);

			if ('' !== $queueCommandResult['result'])
			{
				throw new Exception('Queue purge command error - ' . $queueCommandResult['resultparam']);
			}
		}
	}

	static function queueOrderDeletionProductionEvents($pOrderItems, $pDbObj)
	{
		// get a list of projects based on the user id, and insert them as actions in the production events table
		$query = 'INSERT INTO `PRODUCTIONEVENTS` (`datecreated`, `owner`, `companycode`, `userid`, `orderitemid`, `actioncode`, `message`, `status`) VALUES (now(), ?, ?, ?, ?, ?, ?, ?)';

		$stmt = $pDbObj->prepare($query);

		if (false === $stmt)
		{
			throw new Exception(__METHOD__ . ' Prepare error: ' . $pDbObj->error);
		}

		$actionCode = TPX_PRODUCTIONAUTOMATION_ACTION_ORDER_DELETELOCALDATA;
		$message = '';
		$actionStatus = TPX_REDACTION_STAGE_IN_PROGRESS;

		foreach ($pOrderItems as $item)
		{
			$owner = $item['ownercode'];
			$company = $item['companycode'];
			$user = $item['userid'];
			$itemId = $item['itemid'];

			if (! $stmt->bind_param('ssiiisi', $owner, $company, $user, $itemId, $actionCode, $message, $actionStatus))
			{
				throw new Exception(__METHOD__ . ' Bind param error: ' . $pDbObj->error);
			}

			if (! $stmt->execute())
			{
				throw new Exception(__METHOD__ . ' Execute error: ' . $pDbObj->error);
			}
		}
	}

	static function orderDeletionLocalFileRedaction($pOrderItems, $pACConfig, $pDbObj)
	{
		$basePagePath = UtilsObj::correctPath($pACConfig['CONTROLCENTREORDERPREVIEWPATH'], DIRECTORY_SEPARATOR, true);
		$xmlPath = implode(DIRECTORY_SEPARATOR, array('webroot', 'OrderData', 'Thumbnails', 'xml'));

		foreach ($pOrderItems as $item)
		{
			$pagePath = UtilsObj::correctPath($basePagePath . date('Y/m/d/H/', $item['datecreated']) . $item['uploadref'], DIRECTORY_SEPARATOR, true);
			$thumbnailRemoval = self::removeDirectoryContents($pagePath, true);

			// If we have removed the thumbnails remove the data relating to these.
			if ($thumbnailRemoval)
			{
				self::removeOrderThumbnailData($item['uploadref'], $pDbObj);
			}

			$xmlFile = '..' . DIRECTORY_SEPARATOR . $xmlPath . DIRECTORY_SEPARATOR . $item['uploadref'] . '.xml';

			if (file_exists($xmlFile))
			{
				unlink($xmlFile);
			}
		}
	}

	static function removeDirectoryContents($pPath, $pRemoveDirIfEmpty = true)
	{
		$successfulDelete = true;

		// Only try to get the directory contents if the path exists, otherwise we assume that the folder didnt exist in the first instance.
		if (is_dir($pPath))
		{
			// Get all items in the path supplied.
			$pageFiles = new DirectoryIterator($pPath);

			// Loop over each item in the directory and remove it if its not . or ..
			foreach ($pageFiles as $file)
			{
				if (!$file->isDot())
				{
					if (!unlink($file->getPathName()))
					{
						// We have had a failure so track that
						$successfulDelete = false;
					}
				}
			}
			unset($pageFiles);

			// Only try to remove the parent directory if we have not failed to delete any files and we are wanting to remove the parent folder.
			if ($successfulDelete && $pRemoveDirIfEmpty)
			{
				rmdir($pPath);
			}
		}

		return $successfulDelete;
	}

	static function removeOrderThumbnailData($pUploadRef, $pDbObj)
	{
		$query = 'DELETE FROM `ORDERTHUMBNAILS` WHERE `uploadref`=?';

		$stmt = $pDbObj->prepare($query);
		if (false === $stmt)
		{
			throw new Exception(__METHOD__ . ' prepare error: ' . $pDbObj->error);
		}

		if (!$stmt->bind_param('s', $pUploadRef))
		{
			throw new Exception(__METHOD__ . ' bind param error: ' . $pDbObj->error);
		}

		if (! $stmt->execute())
		{
			throw new Exception(__METHOD__ . ' execute error: ' . $pDbObj->error);
		}
	}

	static function setOrderDeletionReorderState($pItemIds, $pDbObj)
	{
		$query = 'UPDATE `ORDERITEMS` SET `canreorder` = 0, `dbdata` = now() WHERE `id` IN (' . join(', ', $pItemIds) . ')';

		$stmt = $pDbObj->prepare($query);

		if (false === $stmt) {
			throw new Exception(__METHOD__ . ' Prepare error: ' . $pDbObj->error);
		}

		if (!$stmt->execute())
		{
			throw new Exception(__METHOD__ . ' Prepare error: ' . $pDbObj->error);
		}
	}
}
?>