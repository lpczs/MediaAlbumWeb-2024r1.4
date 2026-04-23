<?php

use Security\RequestValidationTrait;

require_once('../Utils/UtilsAuthenticate.php');
require_once('../Utils/UtilsDatabase.php');
require_once('../AdminProduction/AdminProduction_model.php');
require_once('../AdminProduction/AdminProduction_view.php');

class AdminProduction_control
{
	use RequestValidationTrait;

	static function initialize()
	{
		if (AuthenticateObj::adminSessionActive() == 1)	
		{
			global $gSession;
			global $gConstants;

			$resultArray = AdminProduction_model::initialize();

			$userOwner = '**ALL**';
			if ($gConstants['optionms'] && $gSession['userdata']['userowner'] != '')
			{
				$userOwner = $gSession['userdata']['userowner'];
			}
			$resultArray['userowner'] = $userOwner;

			AdminProduction_view::initialize($resultArray);
		}
	}

	static function getListData()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
			global $gConstants;
			global $gSession;

			$searchQuery = UtilsObj::getPOSTParam('query');
			
			//getProductionQueue() in Production API requires searchstring post variable
			$_POST['searchstring'] = $searchQuery;

			//When searching always force full data retreival with datelastmodified 0
			if ($searchQuery != '')
			{
				$_POST['datelastmodified'] = 0;
			}
	
			//If multi site and production site user then only return items from users site
			if ($gConstants['optionms'] && $gSession['userdata']['userowner'] != '')
			{
				$_POST['owner'] = $gSession['userdata']['userowner'];
			}

			$resultArray = AdminProduction_model::getListData();
			$measurementunit = UtilsObj::getPOSTParam('measurementunit');
			$resultArray['measurementunit'] = $measurementunit;

			AdminProduction_view::getListData($resultArray);
		}
	}

	static function orderDetailsDisplay()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
			//getJobInfo() in Production API requires POST variables 
			$_POST['orderitemid'] = UtilsObj::getGETParam('id');
			$_POST['orderid'] = UtilsObj::getGETParam('orderid');
			$_POST['langcode'] = UtilsObj::getGETParam('langcode', 'en');

			$resultArray = AdminProduction_model::orderDetailsDisplay();
			$resultArray['orderlineid'] = UtilsObj::getGETParam('id');
			$resultArray['statusid'] = UtilsObj::getGETParam('statusid');
			$resultArray['statusdescription'] = UtilsObj::getGETParam('status');
			$resultArray['itemactivestatus'] = UtilsObj::getGETParam('itemactivestatus');
			$resultArray['measurementunit']  = UtilsObj::getGETParam('measurementunit');

			AdminProduction_view::orderDetailsDisplay($resultArray);
		}
	}

	static function onHoldDisplay()
	{
		AdminProduction_view::onHoldDisplay();
	}

	static function updateItemOnHoldStatus()
	{
		$resultArray = AdminProduction_model::updateItemOnHoldStatus([
			'orderitemidlist' => UtilsObj::getPOSTParam('idlist'),
			'onholdstatus' => UtilsObj::getPOSTParam('onhold'),
			'onholdreason' => UtilsObj::getPOSTParam('reason')
		]);
	}

	static function confirmPaymentDisplay()
	{
		AdminProduction_view::confirmPaymentDisplay();
	}	

	static function updateOrderPaymentStatus()
    {
		$resultArray = AdminProduction_model::updateOrderPaymentStatus([
			'orderidlist' => UtilsObj::getPOSTParam('orderidlist'),
			'paymentreceived' => UtilsObj::getPOSTParam('paymentreceived'),
			'paymentreceiveddate' => UtilsObj::getPOSTParam('paymentreceiveddate')
		]);
    }

	static function preferencesDisplay()
	{
		$resultArray = AdminProduction_model::preferencesDisplay();
		AdminProduction_view::preferencesDisplay($resultArray);
	}	

	static function updatePreferences()
    {
		$data = json_encode(UtilsObj::getPOSTParam('data'));
		$resultArray = AdminProduction_model::updatePreferences(['data' => $data]);
    }

	static function updateItemCanUploadFilesStatus()
    {
		$resultArray = AdminProduction_model::updateItemCanUploadFilesStatus([
			'orderitemid' => UtilsObj::getPOSTParam('idlist'),
			'canuploadfiles' => UtilsObj::getPOSTParam('canuploadfiles')
		]);
    }

	static function updateOverrideSaveStatus()
    {
		$resultArray = AdminProduction_model::updateOverrideSaveStatus([
			'orderitemid' => UtilsObj::getPOSTParam('idlist'),
			'overridesave' => UtilsObj::getPOSTParam('canuploadenablesaveoverride')
		]);
    }
	
	static function updateItemCanModifyStatus()
    {
		$resultArray = AdminProduction_model::updateItemCanModifyStatus([
			'orderitemid' => UtilsObj::getPOSTParam('idlist'),
			'canmodify' => UtilsObj::getPOSTParam('canmodify')
		]);
    }

	static function updateItemCanUploadFilesOverrideProductCodeStatus()
    {
		$resultArray = AdminProduction_model::updateItemCanUploadFilesOverrideProductCodeStatus([
			'orderitemid' => UtilsObj::getPOSTParam('idlist'),
			'overrideproductcode' => UtilsObj::getPOSTParam('overrideproductcode')
		]);
    }
	
	static function updateItemCanUploadFilesOverridePageCountStatus()
    {
		$resultArray = AdminProduction_model::updateItemCanUploadFilesOverridePageCountStatus([
			'orderitemid' => UtilsObj::getPOSTParam('idlist'),
			'overridepagecount' => UtilsObj::getPOSTParam('overridepagecount')
		]);
    }

	static function updateItemActiveStatus()
	{
		global $gSession;
		
		$resultArray = AdminProduction_model::updateItemActiveStatus([
			'orderitemid' => UtilsObj::getPOSTParam('idlist'),
			'userid' => $gSession['userid'],
			'itemactivestatus' => UtilsObj::getPOSTParam('itemactivestatus')
		]);
	}

	static function updateItemStatus()
	{	
		$resultArray = AdminProduction_model::updateItemStatus([
			'orderitemid' => UtilsObj::getPOSTParam('idlist'),
			'itemstatus' => UtilsObj::getPOSTParam('itemstatus')
		]);
	}

	static function shippingDisplay()
	{
		AdminProduction_view::shippingDisplay();
	}

	static function updateItemShippingStatus()
	{	
		global $gSession;

		AdminProduction_model::updateItemShippingStatus([
			'idlist' => UtilsObj::getPOSTParam('idlist'),
			'itemtrackingref' => UtilsObj::getPOSTParam('shippingtrackingreference'),
			'userid' => $gSession['userid'],
			'itemshippingdate' => UtilsObj::getPOSTParam('itemshippingdate')
		]);
	}

	static function statusCheck()
	{
		$result = AdminProduction_model::statusCheck([
			'data' => UtilsObj::getPOSTParam('data')
		]);
		AdminProduction_view::statusCheck($result);
	}
}

?>