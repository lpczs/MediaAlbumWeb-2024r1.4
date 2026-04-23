<?php

require_once('../Utils/UtilsAddress.php');
require_once('../Utils/UtilsDatabase.php');
require_once('../Utils/UtilsSmarty.php');
require_once('../Utils/UtilsLocalization.php');
require_once('../Utils/UtilsRoute.php');

class AdminDataRetentionAdmin_view
{
	static function displayGrid($pParamArray)
	{
		global $gConstants;

        $smarty = SmartyObj::newSmarty('AdminDataPolicies');
        $smarty->assign('optioncfs', ($gConstants['optioncfs'] ? true : false));
        $smarty->assign('optionms', ($gConstants['optionms'] ? true : false));

        $smarty->assign('taskSchedulerActive', $pParamArray['taskSchedulerActive']);
        $smarty->assign('purgeTasksActive', $pParamArray['purgeTasksActive']);
        $smarty->assign('archiveTasksActive', $pParamArray['archiveTasksActive']);
        $smarty->assign('volumeAvailable', $pParamArray['volumeAvailable']);

        $smarty->displayLocale('admin/datapolicies/datapoliciesgrid.tpl');
	}

	static function getGridData($pPolicyDataArray)
    {
		$smarty = SmartyObj::newSmarty('AdminDataPolicies');

		echo '[';

		if ($pPolicyDataArray['error'] == '')
		{
			if (isset($pPolicyDataArray['grid']))
			{
				$policyCount = count($pPolicyDataArray['grid']);

				$outputStr = '[' . $policyCount . '],' . implode(",", $pPolicyDataArray['grid']);

				echo $outputStr;
			}
		}
		else
		{
			echo "[0],['','','','','','" . $smarty->get_config_vars($pPolicyDataArray['error']) . "']";
		}

		echo ']';
	}

	static function displayEntry($pID, $pTitle, $pResultArray)
    {
        $smarty = SmartyObj::newSmarty('AdminDataPolicies');

		$smarty->assign('policy', json_encode($pResultArray));

		$smarty->assign('title', $smarty->get_config_vars($pTitle));
        $smarty->assign('code', $pResultArray['code']);
        $smarty->assign('name', $pResultArray['name']);
        $smarty->assign('active', $pResultArray['active']);
        $smarty->assign('assignedtobrandslist', $pResultArray['assignedtobrandslist']);

		if ($pID == 0)
      	{
      		$smarty->assign('isEdit', 0);
      	}
      	else
      	{
      		$smarty->assign('isEdit', 1);
      	}

        $smarty->assign('minDormantDays', TPX_PURGE_MINIMUM_DORMANT_DAYS);
        $smarty->assign('minOrderedDormantDays', TPX_PURGE_MINIMUM_ORDERED_DORMANT_DAYS);
        $smarty->assign('minPurgeDays', TPX_PURGE_MINIMUM_PURGE_DAYS);
        $smarty->assign('minWarningDays', TPX_PURGE_MINIMUM_PURGE_WITH_EMAIL_DAYS);

        $smarty->displayLocale('admin/datapolicies/datapoliciesedit.tpl');
    }


    static function dataPolicyDelete($pResultArray)
    {
        $smarty = SmartyObj::newSmarty('AdminDataPolicies');

		$deletedList = implode(',', $pResultArray['policyIDs']);

		if ($pResultArray['allDeleted'] == 0)
		{
			$msg = $smarty->get_config_vars($pResultArray['result']);
			$title = $smarty->get_config_vars('str_TitleWarning');
			$alldeleted = '0';
		}
		else
		{
			$msg = $smarty->get_config_vars($pResultArray['result']);
			$title = $smarty->get_config_vars('str_TitleWarning');
			$alldeleted = '1';
		}

		echo '{"success":true, "title":"' . $title . '", "msg":"' . $msg . '", "alldeleted":"' . $alldeleted . '", "idlist":"' . $deletedList . '"}';
	}


	static function dataPolicySave($pResultArray)
    {
		$dataToSend = '';
       	$smarty = SmartyObj::newSmarty('AdminDataPolicies');

    	if ($pResultArray['result'] != '')
        {
            $msg = str_replace('^0', $pResultArray['resultparam'], $smarty->get_config_vars($pResultArray['result']));
			$title = $smarty->get_config_vars('str_TitleError');

			$dataToSend = '{"success":false, "title":"' . $title . '", "msg":"' . $msg . '"}';
        }
        else
        {
			$dataToSend = '{"success":true, ' . $pResultArray['serialized'] . '}';
        }

		echo $dataToSend;
    }

    static function displayAdd($pResultArray)
	{
        self::displayEntry($pResultArray['id'], 'str_TitleNewPolicy', $pResultArray);
    }

    static function displayEdit($pResultArray)
	{
	   self::displayEntry($pResultArray['id'], 'str_TitleEditPolicy', $pResultArray);
    }

	/**
	 * Returns json result when make active/make inactive buttons are pressed on the data retention policies grid.
	 *
	 * @param array $pResultArray Result array passed by controller.
	 */
	static function setPolicyActiveStatus($pResultArray)
	{
    	if ($pResultArray['result'] != '')
        {
			// Create a smarty object.
			$smarty = SmartyObj::newSmarty('AdminDataPolicies');
			$title = $smarty->get_config_vars('str_TitleError');

			$dataToSend = '{"success":false, "title":"' . $title . '", "msg":"' . $pResultArray['resultparam'] . '"}';
        }
        else
        {
			$dataToSend = '{"success":true}';
        }

		echo $dataToSend;
	}
}

?>
