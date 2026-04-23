<?php

require_once('../DataRedactionAPI/DataRedactionAPI_model.php');
require_once('../DataRedactionAPI/DataRedactionAPI_view.php');
require_once('../Welcome/Welcome_control.php');
require_once('../Utils/UtilsEmail.php');

class DataRedactionAPI_control
{
	static function requestRedaction()
	{
		global $gSession;

		if (AuthenticateObj::WebSessionActive() == 1)
        {
            $resultArray = DataRedactionAPI_model::requestRedaction();
            DataRedactionAPI_view::returnResult($resultArray);
        }
        elseif (AuthenticateObj::WebSessionActive() == 0)
        {
            Welcome_control::processLogout2(TPX_USER_LOGOUT_REASON_SESSION_EXPIRED);
        }
        else
        {
            Welcome_control::processLogout2(TPX_USER_LOGOUT_REASON_USER_LOGOUT);
        }
	}

	static function authoriseRedaction()
	{
		// update the account to be authorised or declined by the licensee
		$resultArray = DataRedactionAPI_model::authoriseRedaction();
        DataRedactionAPI_view::returnResult($resultArray);
	}


	static function updateRedactionProgress()
	{
		// update the progress of the redaction of user data
        $resultArray = DataRedactionAPI_model::updateRedactionProgress();
        DataRedactionAPI_view::returnResult($resultArray);
	}


	static function startRedactionTask($pUserIDList, $pTaskID, $pSubTaskID, $pBrandCode, $pSystemConfig)
	{
		// find the accounts which have not been accessed for longer than the configured days
		// update the progress of the redaction of user data
        $resultArray = DataRedactionAPI_model::startRedactionTask($pUserIDList, $pTaskID, $pSubTaskID, $pBrandCode, $pSystemConfig);

        return $resultArray;
	}

	static function flagUnusedAccounts()
	{
		// find the accounts which have not been accessed for longer than the configured days
        $resultArray = DataRedactionAPI_model::flagUnusedAccounts();

        return $resultArray;
	}

	static function queueFlaggedAccounts()
	{
		// find the accounts which have not been accessed and have been flagged for redaction
        $resultArray = DataRedactionAPI_model::queueFlaggedAccounts();

        return $resultArray;
	}

	static function updateProductionRedaction()
	{
		// update the progress of the redaction of user data in production
        $resultArray = DataRedactionAPI_model::updateProductionRedaction();
        DataRedactionAPI_view::returnResult($resultArray);
	}

}

?>