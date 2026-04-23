<?php
/*
Taopix E-Commerce Integration API Example
Version 2.5 - 1st November 2018
Copyright 2011 - 2018 Taopix Limited
*/
class OnlineBasketAPI
{
	static function createProject($pParamArray)
	{
		$result = $pParamArray;

		$result['result'] = '';
		$result['useraccount'] = array();
		$result['updategroupcode'] = 0;
		$result['updateaccountdetails'] = 0;
		$result['updateaccountbalance'] = 0;
		$result['updategiftcardbalance'] = 0;
		$result['ssotoken'] = '';
		$result['ssoprivatedata'] = array();
		$result['assetservicedata'] = array();
		$result['ssoexpiredate'] = '';

		// This function receives the decoded parameters from a standard Taopix Online Product if one has been used.
		// These function input parameters can be overwritten.
		// Please refer to documentation for a full list of input parameters.

		//$result['groupcode'] = 'NEWGROUPCODE';
		//$result['collectioncode'] = 'NEWCOLLECTIONCODE';
		//$result['productcode'] = 'NEWPRODUCTCODE';

		return $result;
	}

	static function editProject($pParamArray)
	{
		$result = $pParamArray;

		$result['result'] = '';
		$result['useraccount'] = array();
		$result['updategroupcode'] = 0;
		$result['updateaccountdetails'] = 0;
		$result['updateaccountbalance'] = 0;
		$result['updategiftcardbalance'] = 0;
		$result['ssotoken'] = '';
		$result['ssoprivatedata'] = array();
		$result['assetservicedata'] = array();
		$result['ssoexpiredate'] = '';

		return $result;
	}

	static function renameProject($pParamArray)
	{
		$result = $pParamArray;

		$result['result'] = '';

		return $result;
	}

	static function duplicateProject($pParamArray)
	{
		$result = $pParamArray;

		$result['result'] = '';

		return $result;
	}

	static function deleteProject($pParamArray)
	{
		$result = $pParamArray;

		$result['result'] = '';

		return $result;
	}

	static function deleteUnflagProject($pParamArray)
	{
		$result = $pParamArray;

		$result['result'] = '';

		return $result;
	}

	static function touchProject($pParamArray)
	{
		$result = $pParamArray;

		$result['result'] = '';

		return $result;
	}

	static function externalCheckout($pParamArray)
	{
		$result = $pParamArray;

		$result['result'] = '';

		return $result;
	}

	static function clearProjectBatchRef($pParamArray)
	{
		$result = $pParamArray;

		$result['result'] = '';

		return $result;
	}

	static function lockProject($pParamArray)
	{
		$result = $pParamArray;

		$result['result'] = '';

		return $result;
	}

	static function unlockProject($pParamArray)
	{
		$result = $pParamArray;

		$result['result'] = '';

		return $result;
	}

	static function initialise($pParamArray)
	{
		$shoppingCartURL = '';

		$resultArray['result'] = '';
		$resultArray['shoppingcarturl'] = $shoppingCartURL;

		return $resultArray;
	}

	static function projectNotifications($pParamArray)
	{
		$resultArray = array();
		$resultArray['result'] = '';

		return $resultArray;
	}

	static function queryOrderStatus($pParamArray)
	{
		$result = $pParamArray;

		$result['result'] = '';

		return $result;
	}

	static function prepareReorderInit($pParamArray)
	{
		$result = $pParamArray;

		$result['result'] = '';

		return $result;
	}

	static function prepareReorderResult($pParamArray)
	{
		$result = '';

		return $result;
	}

	static function checkDataAvailable($pParamArray)
	{
		$result = $pParamArray;

		$result['result'] = '';

		return $result;
	}

	static function leaveUsersProjectList($pParamArray)
	{
		$result = $pParamArray;

		$result['result'] = '';

		return $result;
	}

	static function usersProjectListInit($pParamArray)
	{
		$result = $pParamArray;

		$result['result'] = '';

		return $result;
	}

	static function usersProjectList($pParamArray)
	{
		$result = $pParamArray;

		return $result;
	}

	static function generateSharePreviewLink($pParamArray)
	{
		$result = $pParamArray;

		$result['result'] = '';
		$result['sharelink'] = "";

		return $result;
	}


}
?>