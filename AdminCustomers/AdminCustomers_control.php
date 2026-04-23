<?php

require_once('../Utils/UtilsAuthenticate.php');
require_once('../AdminCustomers/AdminCustomers_model.php');
require_once('../AdminCustomers/AdminCustomers_view.php');

use Security\RequestValidationTrait;

class AdminCustomers_control
{
	use RequestValidationTrait;

	static function initialize()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
		    AdminCustomers_view::initialize();
		}
	}

	static function customerActivate()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
			$result = AdminCustomers_model::customerActivate();
			AdminCustomers_view::customerActivate($result);
 
        }
	}

	static function customerList()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
		    $resultArray = AdminCustomers_model::displayList();
		}
	}

	static function customerAddDisplay()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
		    $resultArray = AdminCustomers_model::displayAdd();
            AdminCustomers_view::displayAdd($resultArray);
        }
	}

	static function customerAdd()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
            $resultArray = AdminCustomers_model::customerAdd();
        }
	}

	static function customerEditDisplay()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
            $customerID = $_GET['id'];
            if ($customerID)
            {
                $resultArray = AdminCustomers_model::displayEdit($customerID);
                AdminCustomers_view::displayEdit($resultArray);
            }
            else
            {
                AdminCustomers_view::initialize();
            }
        }
	}

	static function customerEdit()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
            $resultArray = AdminCustomers_model::customerEdit();
        }
	}

	static function customerDelete()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
			$resultArray = AdminCustomers_model::customerDelete();
            AdminCustomers_view::displayDeletionResults($resultArray);
        }
	}


	static function customerRedact()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
            AdminCustomers_model::customerRedact();
        }
	}

	static function customerRedactDecline()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
            AdminCustomers_model::customerRedactDecline();
        }
	}

	static function customerExportDisplay()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
			AdminCustomers_view::displayCustomerExport();
		}
	}

	static function customerExport()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
			$companyCode = isset($_POST['companyCode']) ? $_POST['companyCode'] : '';
			$groupCode = isset($_POST['groupCode']) ? $_POST['groupCode'] : '';
			$brandCode = isset($_POST['brandCode']) && $_POST['brandCode'] != '-1' ? $_POST['brandCode'] : '';
			$countryCode = isset($_POST['countryCode']) && $_POST['countryCode'] != '-1' ? $_POST['countryCode'] : '';
			$format = isset($_POST['exportFileFormat']) && in_array($_POST['exportFileFormat'], ['csv', 'tsv', 'xml']) ? $_POST['exportFileFormat'] : 'csv';

			$filters = [
				'companyCode' => $companyCode,
				'groupCode' => $groupCode,
				'brandCode' => $brandCode,
				'countryCode' => $countryCode,
				'contactEmail' => isset($_POST['contactEmail']) ? $_POST['contactEmail'] : '',
				'contactLastName' => isset($_POST['contactLastName']) ? $_POST['contactLastName'] : ''
			];

			AdminCustomers_model::customerExport($companyCode, $groupCode, $brandCode, $format, $filters);
		}
	}
}

?>