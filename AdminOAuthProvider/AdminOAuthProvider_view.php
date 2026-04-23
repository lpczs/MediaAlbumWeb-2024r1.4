<?php

use League\OAuth2\Client\Provider\Google;
use TheNetworg\OAuth2\Client\Provider\Azure;
use League\OAuth2\Client\Provider\GenericProvider;

class AdminOAuthProvider_view
{
	public static function initialize()
	{
		$smarty = SmartyObj::newSmarty('AdminOAuthProvider');
		$smarty->assign('providerList', self::providerList($smarty));
		$smarty->displayLocale('admin/oauthprovider/grid.tpl');
	}

	private static function providerList($smarty, $asObject = true)
	{
		$baseArray = [
			'' => $smarty->get_config_vars('str_LabelSelectProvider'),
			Google::class => $smarty->get_config_vars('str_LabelProviderGoogle'),
			Azure::class => $smarty->get_config_vars('str_LabelProviderAzure'),
			GenericProvider::class => $smarty->get_config_vars('str_LabelProviderCustom')
		];

		$data = $asObject ? $baseArray : array_map(function($key, $value) { return [$key, $value]; }, array_keys($baseArray), array_values($baseArray));
		return json_encode($data);
	}

	public static function formView(array $providerData = [])
	{
		$smarty = SmartyObj::newSmarty('AdminOAuthProvider');
		$smarty->assign('formProviderList', self::providerList($smarty, false));
		$smarty->assign('providerData', json_encode($providerData));
		$smarty->displayLocale('admin/oauthprovider/form.tpl');
	}
}