<?php

class OnlineBasketHighLevelAPI
{
	static function createProject($pParamArray)
	{
		$result = $pParamArray;
		
		$result['result'] = '';
		
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
	
	static function signInInit($pParamArray)
	{
		$result = $pParamArray;
		
		$result['result'] = '';
		
		return $result;
	}
	
	static function registerInit($pParamArray)
	{
		$result = $pParamArray;
		
		$result['result'] = '';
		
		return $result;
	}
}
?>