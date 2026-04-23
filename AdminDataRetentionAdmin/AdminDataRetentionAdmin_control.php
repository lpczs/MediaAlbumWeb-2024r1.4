<?php

require_once('../Utils/UtilsAuthenticate.php');
require_once('../AdminDataRetentionAdmin/AdminDataRetentionAdmin_model.php');
require_once('../AdminDataRetentionAdmin/AdminDataRetentionAdmin_view.php');

use Security\RequestValidationTrait;

class AdminDataRetentionAdmin_control
{
	use RequestValidationTrait;

	static function initialize()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
			$viewParams = array('taskSchedulerActive' => 0, 'purgeTasksActive' => 0, 'archiveTasksActive' => 0, 'volumeAvailable' => 0);
			$viewParams['taskSchedulerActive'] = AdminDataRetentionAdmin_model::taskSchedulerActive();
			
			// Only check if the tasks are active if the task scheduler is active.
			if ($viewParams['taskSchedulerActive'] === 1)
			{
				$viewParams['purgeTasksActive'] = AdminDataRetentionAdmin_model::getTaskActive('TAOPIX_ONLINEPURGETASK');
				$viewParams['archiveTasksActive'] = AdminDataRetentionAdmin_model::getTaskActive('TAOPIX_ONLINEARCHIVETASK');
				$viewParams['volumeAvailable'] = AdminDataRetentionAdmin_model::archiveVolumeActive();
			}

			AdminDataRetentionAdmin_view::displayGrid($viewParams);
		}
	}


	static function getGridData()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
			$resultArray = AdminDataRetentionAdmin_model::getGridData();
			AdminDataRetentionAdmin_view::getGridData($resultArray);
		}
	}


    static function dataPolicyAddDisplay()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
		    $resultArray = AdminDataRetentionAdmin_model::displayAdd();
			AdminDataRetentionAdmin_view::displayAdd($resultArray);
		}
	}

	/**
	 * Add a new data policy into the database.
	 */
	static function dataPolicyAdd()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
			// Read the new values from the POST.
			$postDataArray = filter_input_array(INPUT_POST);

			$policyData = array();

			// Make sure all values are set to defaults.
			$policyDefaults = AdminDataRetentionAdmin_model::getDefaultValues();

			// Set the list of values to be used when creating the new data retention policy.
			$policyData['code'] = strtoupper(UtilsObj::getArrayParam($postDataArray, 'code', $policyDefaults['code'] ));
			$policyData['name'] = html_entity_decode(UtilsObj::getArrayParam($postDataArray, 'name', $policyDefaults['name']));
			$policyData['active'] = (int) UtilsObj::getArrayParam($postDataArray, 'active', $policyDefaults['active']);

			// Loop over each item in the lookup order array.
			foreach (AdminDataRetentionAdmin_model::getLookupOrder() as $policyKey)
			{
				// Loop over each key in the defaults for the current key and set the value.
				foreach ($policyDefaults[$policyKey] as $policySettingKey => $policySetting)
				{
					$postedValue = UtilsObj::getArrayParam($postDataArray, $policyKey . $policySettingKey, $policyDefaults[$policyKey][$policySettingKey]);

					// If the field was inactive we will have sent back an empty value, or in some cases a 0.
					if (trim($postedValue) == '')
					{
						$postedValue = $policyDefaults[$policyKey][$policySettingKey];
					}

					$policyData[$policyKey][$policySettingKey] = $postedValue;
				}
			}

			$resultArray = AdminDataRetentionAdmin_model::dataPolicyAdd($policyData);
			AdminDataRetentionAdmin_view::dataPolicySave($resultArray);
		}
	}


	static function dataPolicyEditDisplay()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
            $policyID = filter_input(INPUT_GET, 'id');

			if ($policyID)
            {
                $resultArray = AdminDataRetentionAdmin_model::displayEdit($policyID);
                AdminDataRetentionAdmin_view::displayEdit($resultArray['data'][0]);
            }
            else
            {
                self::initialize();
            }
        }
	}


	static function dataPolicyEdit()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
            $resultArray = AdminDataRetentionAdmin_model::dataPolicyEdit();
          	AdminDataRetentionAdmin_view::dataPolicySave($resultArray);
        }
	}


	static function dataPolicyDelete()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
            $deleteResultArray = AdminDataRetentionAdmin_model::dataPolicyDelete();
			AdminDataRetentionAdmin_view::dataPolicyDelete($deleteResultArray);
        }
	}

	/**
	 * Sets the active status for the given policy.
	 */
	static function setPolicyActiveStatus()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
            $setPolicyActiveStatusResult = AdminDataRetentionAdmin_model::setPolicyActiveStatus();
			AdminDataRetentionAdmin_view::setPolicyActiveStatus($setPolicyActiveStatusResult);
        }
	}

}

?>