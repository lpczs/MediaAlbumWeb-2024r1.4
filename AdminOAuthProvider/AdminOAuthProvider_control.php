<?php
use Security\RequestValidationTrait;
use League\OAuth2\Client\Provider\GenericProvider;
use League\OAuth2\Client\Provider\Google;
use TheNetworg\OAuth2\Client\Provider\Azure;

require_once('../Utils/UtilsAuthenticate.php');
require_once('../Utils/TaopixOAuthProvider.php');
require_once('../AdminOAuthProvider/AdminOAuthProvider_view.php');

class AdminOAuthProvider_control
{
	use RequestValidationTrait;

	public static function initialize()
	{
		global $gSession;

		if (1 !== AuthenticateObj::adminSessionActive()) {
			return;
		}

		AdminOAuthProvider_view::initialize();
	}

	public static function getGridData()
	{
		global $gSession;

		if (1 !== AuthenticateObj::adminSessionActive()) {
			return;
		}

		$providerObject = new TaopixOAuthProvider(0, DatabaseObj::getGlobalDBConnection(), []);
		$list = $providerObject->listAll();
		$total = count($list['data']);
		array_unshift($list['data'], [$total]);

		echo json_encode($list['data']);
	}

	public static function getProvider()
	{
		global $gSession;

		if (1 !== AuthenticateObj::adminSessionActive()) {
			return;
		}
		$providerId = (int) UtilsObj::getGETParam('id', 0);
		$providerObject = new TaopixOAuthProvider($providerId, DatabaseObj::getGlobalDBConnection(), []);

		AdminOAuthProvider_view::formView($providerObject->getData());
	}

	public static function delete()
	{
		global $gSession;

		if (1 !== AuthenticateObj::adminSessionActive()) {
			return;
		}

		$listIds = [];
		$providerIds = UtilsObj::getPOSTParam('id', 0);
		if (false !== strpos($providerIds, ',')) {
			$listIds = explode(',', $providerIds);
			$providerId = (int) $listIds[0];
		} else {
			$providerId = (int) $providerIds;
		}

		$providerObject = new TaopixOAuthProvider($providerId, DatabaseObj::getGlobalDBConnection(), []);
		$assignedProviders = $providerObject->assigedProviders();
		$toDelete = [];

		if (!empty($listIds)) {
			$toDelete = array_diff($listIds, $assignedProviders);
			$deleteResult = $providerObject->deleteMultiple($toDelete);
		} else {
			if (in_array($providerId, $assignedProviders)) {
				$deleteResult = [
					'success' => false,
					'msg' => 'str_MessageUnableToDeleteProviderIsAssignedToABrand',
				];
			} else {
				$deleteResult = $providerObject->delete();
			}
		}

		$smarty = SmartyObj::newSmarty('AdminOAuthProvider');

		if (!$deleteResult['success']) {
			$deleteResult['msg'] = str_replace('^0', $deleteResult['errorparam'], $smarty->get_config_vars($deleteResult['msg']));
		}

		if (!empty($listIds) && count($listIds) !== count($toDelete)) {
			$deleteResult['msg'] = $smarty->get_config_vars('str_MessageUnableToDeleteProvidersAssignedToBrands');
		}

		echo json_encode($deleteResult);
	}

	public static function save()
	{
		global $gSession;

		if (1 !== AuthenticateObj::adminSessionActive()) {
			return;
		}

		$providerId = (int) UtilsObj::getPOSTParam('id', 0);
		$providerObject = new TaopixOAuthProvider($providerId, DatabaseObj::getGlobalDBConnection(), []);
		$initialSecret = UtilsObj::getPOSTParam('initialsecret', '');

		$dataArray = [
			'id' => $providerId,
			'providername' => UtilsObj::getPOSTParam('providername', ''),
			'scopes' => UtilsObj::getPOSTParam('scopes', ''),
			'clientid' => UtilsObj::getPOSTParam('clientid', ''),
			'clientsecret' => UtilsObj::getPOSTParam('clientsecret', ''),
			'provider' => UtilsObj::getPOSTParam('provider', ''),
			'authurl' => UtilsObj::getPOSTParam('authurl', ''),
			'tokenurl' => UtilsObj::getPOSTParam('tokenurl', ''),
			'ownerurl' => UtilsObj::getPOSTParam('ownerurl', ''),
			'tenantid' => UtilsObj::getPOSTParam('tenantid', ''),
		];

		$dataArray['provider'] = '' !== $dataArray['provider'] ? '\\' . $dataArray['provider'] : $dataArray['provider'];

		$validationResult = self::saveDataValid($dataArray);

		// If the client secret is not the same as the initial secret encode this.
		if ($dataArray['clientsecret'] !== $initialSecret) {
			$dataArray['clientsecret'] = base64_encode($dataArray['clientsecret']);
		}

		// Validation result is in the format we want to send back if there is an error so bail early if invalid data has been sent.
		if (!$validationResult['success']) {
			echo json_encode($validationResult);
			return;
		}

		$saveResult = $providerObject->setData($dataArray)->save();

		if (!$saveResult['success']) {
			$smarty = SmartyObj::newSmarty('AdminOAuthProvider');
			$saveResult['msg'] = str_replace('^0', $saveResult['errorparam'], $smarty->get_config_vars($saveResult['msg']));
		}

		echo json_encode($saveResult);
	}

	private static function saveDataValid(array $dataArray)
	{
		$smarty = SmartyObj::newSmarty('AdminOAuthProvider');
		$requiredFields = ['providername', 'clientid', 'clientsecret', 'provider'];
		$requiredForCustomProvider = ['authurl', 'tokenurl', 'ownerurl', 'scopes'];
		$validProviders = [
			ltrim(Google::class, " \t\n\r\0\x0B\\"),
			ltrim(Azure::class, " \t\n\r\0\x0B\\"),
			ltrim(GenericProvider::class, " \t\n\r\0\x0B\\"),
		];

		$valid = true;
		$errors = [];
		foreach ($dataArray as $key => $value) {
			// Check value is a fully required field.
			if (in_array($key, $requiredFields) && empty($value)) {
				$valid = false;
				$errors[] = $key;
			}

			if ('provider' === $key && !in_array(ltrim($dataArray['provider'], " \t\n\r\0\x0B\\"), $validProviders)) {
				$valid = false;
				$errors[] = $key;
			}

			// Custom providers we need more fields to be valid.
			if (GenericProvider::class === ltrim($dataArray['provider'], " \t\n\r\0\x0B\\") && in_array($key, $requiredForCustomProvider) && empty($value)) {
				$valid = false;
				$errors[] = $key;
			}

			// Azure, Currently unsure of the default scopes for azure so make sure there is something provided by the user.
			if ($key === 'scopes' && Azure::class === ltrim($dataArray['provider'], " \t\n\r\0\x0B\\") && empty($value)) {
				$valid = false;
				$errors[] = $key;
			}
		}

		return [
			'success' => $valid,
			'msg' => $valid ? '' : $smarty->get_config_vars('str_MessageValidationFailure'),
			'invalid' => $errors,
		];
	}
}