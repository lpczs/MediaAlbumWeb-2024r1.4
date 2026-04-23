<?php

use Security\RequestValidationTrait;

require_once('../Utils/UtilsAuthenticate.php');
require_once('../AdminConnectors/AdminConnectors_model.php');
require_once('../AdminConnectors/AdminConnectors_view.php');

class AdminConnectors_control
{
	use RequestValidationTrait;

	static function initialize()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
			AdminConnectors_view::initialize();
		}
	}

	static function connectorsList()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
		    $resultArray = AdminConnectors_model::connectorsList([
				'start' => UtilsObj::getPOSTParam('start'),
				'limit' => UtilsObj::getPOSTParam('limit'),
				'sort' => UtilsObj::getPOSTParam('sort'),
				'dir' => UtilsObj::getPOSTParam('dir'),
				'fields' => UtilsObj::getPOSTParam('fields'),
				'query' => UtilsObj::getPOSTParam('query')
			]);
			AdminConnectors_view::connectorsList($resultArray);
		}
	}

	static function connectorsDisplayEdit()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
			$connectorID = UtilsObj::getGETParam('id', 0);

		    $resultArray = AdminConnectors_model::displayEdit($connectorID);
			AdminConnectors_view::displayEdit($resultArray);
		}
	}

	static function connectorsEdit()
	{
		$resultArray = UtilsObj::getReturnArray();

		if (AuthenticateObj::adminSessionActive() == 1)
		{
			$id = UtilsObj::getPOSTParam('id', 0);
			$resultArray = AdminConnectors_model::connectorsEdit([
				'id' => $id,
				'connectorurl' => UtilsObj::getPOSTParam('connectorurl'),
				'connectorprimarydomain' => UtilsObj::getPOSTParam('connectorprimarydomain'),
				'connectorkey' => UtilsObj::getPOSTParam('connectorkey'),
				'connectorsecret' =>  UtilsObj::getPOSTParam('connectorsecret'),
				'connectorinstallurl' => UtilsObj::getPOSTParam('connectorinstallurl'),
				'pricesincludetax' => UtilsObj::getPOSTParam('pricesincludetax')
			]);
			AdminConnectors_view::outputResult($resultArray);
		}
	}

	static function connectorsAdd()
	{
		$resultArray = UtilsObj::getReturnArray();

		if (AuthenticateObj::adminSessionActive() == 1)
		{
			$connectorName = UtilsObj::getPOSTParam('connectorname', '');

			// Insert the Online details.
			$resultArray = AdminConnectors_model::connectorsAdd($connectorName, [
				'connectorurl' => UtilsObj::getPOSTParam('connectorurl'),
				'connectorkey' => UtilsObj::getPOSTParam('connectorkey'),
				'connectorsecret' =>  UtilsObj::getPOSTParam('connectorsecret'),
				'connectorinstallurl' => UtilsObj::getPOSTParam('connectorinstallurl'),
				'connectorprimarydomain' => UtilsObj::getPOSTParam('connectorprimarydomain'),
				'pricesincludetax' => UtilsObj::getPOSTParam('pricesincludetax')
			]);

			AdminConnectors_view::outputResult($resultArray);
		}
	}

	static function connectorsDisplayAdd()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
			$connectorID = UtilsObj::getGETParam('id', 0);

			$resultArray = AdminConnectors_model::displayEdit($connectorID);
			AdminConnectors_view::displayEdit($resultArray);
		}
	}

	static function connectorsDelete()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
			$shopURL = UtilsObj::getPOSTParam('shopurl');
			$connectorID = UtilsObj::getPOSTParam('id', 0);

			$resultArray = AdminConnectors_model::connectorsDelete(['id' => $connectorID, 'shopurl' => $shopURL]);
			AdminConnectors_view::outputResult($resultArray);
		}
	}

	static function connectorsSyncProductsDisplay()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
			$connectorID = UtilsObj::getGETParam('id', 0);
		    $resultArray = AdminConnectors_model::syncProductsEditDisplay($connectorID);
			AdminConnectors_view::syncProductsEditDisplay($resultArray);
		}
	}

	static function connectorsSyncProducts()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
			$resultArray = AdminConnectors_model::syncProductsEdit();
			AdminConnectors_view::syncProductsEdit($resultArray);
		}
	}	

	static function connectorsRebuildTheme()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
			$result = AdminConnectors_model::connectorsRebuildTheme();
			AdminConnectors_view::connectorsRebuildTheme($result);
		}
	}

	static function connectorsInstallTaopixTheme()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
			$shopURL = UtilsObj::getPOSTParam('shopurl');
			$result = AdminConnectors_model::connectorsInstallTaopixTheme($shopURL);
			AdminConnectors_view::connectorsInstallTaopixTheme($result);
		}
	}

}

?>