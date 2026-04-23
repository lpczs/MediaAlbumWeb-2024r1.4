<?php
// step back to the webroot directory
chdir('../');

require __DIR__ . '/../../libs/external/vendor/autoload.php';

require_once('../Utils/UtilsCoreIncludes.php');
require_once('../Order/Order_model.php');
require_once('../AppApi/AppAPI_model.php');

// read the config file
$ac_config = UtilsObj::readConfigFile('../config/mediaalbumweb.conf');

// get the constants
$gConstants = DatabaseObj::getConstants();
$gSession = AuthenticateObj::getCurrentSessionData();

$writeResult = '';

if (array_key_exists("oauth_verifier", $_GET))
{
    // oauth1
    $code = UtilsObj::getGETParam('oauth_verifier', '');
    $state = UtilsObj::getGETParam('state', '');
    $error = "";
}
else
{
    // oauth2
    $code = UtilsObj::getGETParam("code", "");
    $state = UtilsObj::getGETParam("state", "");
    $error = UtilsObj::getGETParam("error", "");
}

$success = false;

if ($state === "")
{
    // if we do not have a state string something has gone wrong and we cannot inform the designer and thus need to display the error screen
    $writeResult = "NOSTATE";
}

if ($writeResult === "")
{
    if ($code !== "")
    {
        $updateResultArray = AuthenticateObj::updateAuthenticationDataRecord($state, array("success" => TPX_OAUTH_STATUS_SUCCESS, "code" => $code), true);
        $success = true;
    }
    elseif ($error !== "")
    {
        $updateResultArray = AuthenticateObj::updateAuthenticationDataRecord($state, array("success" => TPX_OAUTH_STATUS_FAILURE, "code" => $error), true);
    }
    else
    {
        // we have no error but also no auth code thus something has gone wrong
        $updateResultArray = AuthenticateObj::updateAuthenticationDataRecord($state, array("success" => TPX_OAUTH_STATUS_FAILURE, "code" => "NOAUTHCODE"), true);
    }

    $idResultArray = AuthenticateObj::getAuthenticationDataStoreRecordID($state, TPX_AUTHENTICATIONTYPE_ASSETSERVICE);

    if (($updateResultArray['result'] === "") && ($idResultArray['error'] === "") && ($idResultArray['id'] > 0))
    {   
        // update the cache file for the designer to run head requests against to reduce the overhead of polling the server for a result
        if ($success === true)
        {
            AppAPI_model::writeOAuthStatusFile($state, "SUCCESS", $idResultArray['id']);
        }
        else
        {
            AppAPI_model::writeOAuthStatusFile($state, "ERROR", $idResultArray['id']);
        }
    }
    else
    {
        // only set the write error if we had a failure in updating the database as we will need to inform the user to manually cancel the process
        // we do not care about the id request failing as the designer will periodically do a full query
        $writeResult = $updateResultArray['result'];
    }

    // clean up the data store
    AuthenticateObj::deleteAuthenticationDataRecords();
}

$smarty = SmartyObj::newSmarty('OAuth', '', '', '', false, false);

$msg = '';

if ($writeResult === '')
{
    // we have succesfully inserted the authorisation code into the database so display a success message
    $msg = $smarty->get_config_vars("str_MessageSafeToCloseWindow");
}
else
{
    // we have not been able to update the database and thus cannot inform the designer that the process has failed
    // thus we must tell the user to manually restart the process
    $msg = $smarty->get_config_vars("str_MessageCloseWindowAndTryAgain");
}

echo "<h1>" . $msg . " </h1>";
?>