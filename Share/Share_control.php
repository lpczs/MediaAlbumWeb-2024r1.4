<?php

use Security\RequestValidationTrait;

require_once(__DIR__.'/../Utils/UtilsAuthenticate.php');
require_once(__DIR__.'/../Utils/UtilsDatabase.php');
require_once(__DIR__.'/../Share/Share_model.php');
require_once(__DIR__.'/../Share/Share_view.php');
require_once(__DIR__.'/../Welcome/Welcome_control.php');

class Share_control
{
	use RequestValidationTrait;

	static function login()
	{
		Share_model::login();
	}

	static function shareAddToAny()
	{
 		if (AuthenticateObj::WebSessionActive() == 1)
        {
			$result = Share_model::shareAddToAny();
			Share_view::shareAddToAny($result);
		}
        else
        {
            echo 'str_ErrorSessionExpired';
        }
    }

 	static function shareByEmail()
 	{
 		if (AuthenticateObj::WebSessionActive() == 1)
        {
			Share_model::shareByEmail();
		}
        elseif (AuthenticateObj::WebSessionActive() == 0)
        {
            Welcome_control::processLogout2('str_ErrorSessionExpired');
        }
        else
        {
            Welcome_control::processLogout2('');
        }
 	}

    static function mailTo()
 	{
		if (AuthenticateObj::WebSessionActive() == 1)
        {
			Share_model::mailTo();
		}
        elseif (AuthenticateObj::WebSessionActive() == 0)
        {
            Welcome_control::processLogout2('str_ErrorSessionExpired');
        }
        else
        {
            Welcome_control::processLogout2('');
        }
 	}

	static function preview()
 	{
        UtilsObj::setSessionDeviceData();

        DatabaseObj::updateSession();

        // check if user needs to login
 		$previewHash = UtilsObj::getGETParam('ref2');
        $sharedItem = Share_model::getOriginalSharedItem($previewHash);

 		if ($sharedItem['result'] == '')
 		{
 			if ($sharedItem['active'] == 1)
 			{
 				// if item has been shared then no login required. otherwise it's a customer preview and we need to authenticate first
				if ($sharedItem['action'] == 'SHARE')
				{
					$resultArray = Share_model::preview($previewHash);

                    if(!empty($resultArray['pages']) || ($resultArray['result'] != '') || ($resultArray['ordersource'] == TPX_SOURCE_ONLINE))
                    {
                        if (($resultArray['productionstatus'] == TPX_ITEM_STATUS_AWAITING_FILES) && ($resultArray['canupload'] == 1) &&
                        	($resultArray['ordersource'] == TPX_SOURCE_ONLINE))
                        {
                        	Share_view::previewNotAvailable(true);
                        }
                        else
                        {
                            if ($resultArray['result'] == 'str_ErrorPreviewLogin')
                            {
                                Share_view::displayLogin($resultArray['brandcode'], $resultArray['uniqueref'], $resultArray['orderitem'], $resultArray['source']);
                            }
                            else
                            {
                                Share_view::preview($resultArray, TPX_PREVIEW_SHARED, true);
                            }
                        }
                    }
                    else
                    {
                        Share_view::previewNotFound(true);
                    }
				}
				else
				{
					if (AuthenticateObj::WebSessionActive() == 1)
					{
						$resultArray = Share_model::preview($previewHash);

                        if (! empty($resultArray['pages']) || ($resultArray['result'] != '') || ($resultArray['ordersource'] == TPX_SOURCE_ONLINE))
                        {
                            if (($resultArray['productionstatus'] == TPX_ITEM_STATUS_AWAITING_FILES) && ($resultArray['canupload'] == 1) &&
                                ($resultArray['ordersource'] == TPX_SOURCE_ONLINE))
                            {
                                Share_view::previewNotAvailable(true);
                            }
                            else
                            {
                                Share_view::preview($resultArray, TPX_PREVIEW_CUSTOMER, true);
                            }
                        }
                        else
                        {
                            Share_view::previewNotFound(true);
                        }
					}
					elseif (AuthenticateObj::WebSessionActive() == 0)
					{
						Welcome_control::processLogout2(TPX_USER_LOGOUT_REASON_SESSION_EXPIRED);
					}
					else
					{
                        // use the current host to get the brand code
                        global $gSession;

                        $brandCode = $gSession['webbrandcode'];

                        // attempt to login, this will cause the ssoLogin function to be called if configured
                        $ssoResultArray = AuthenticateObj::authenticateLogin(TPX_USER_AUTH_REASON_WEB_CUSTOMER_SHARE_PREVIEW, -1, false, UtilsObj::getBrowserLocale(), 
                            $brandCode, '', '', '', TPX_PASSWORDFORMAT_CLEARTEXT, '', true, true, true, '', array(), array());

                        // if the result is empty but the user account id has been populated attempt to log the user in
                        if (($ssoResultArray['result'] == '') && ($ssoResultArray['useraccountid'] > 0))
                        {
                            Welcome_view::processLogin($ssoResultArray);
                        }
                        elseif ($ssoResultArray['result'] == 'SSOREDIRECT')
                        {
                            // redirect to the user to the SSO CRM system
                            AuthenticateObj::ssoRedirect($ssoResultArray);
                        }
                        else
                        {
                            // if there is no result (and no user account) then SSO hasn't been used so call the normal function
                            if ($ssoResultArray['result'] == '')
                            {
                                Welcome_control::processLogout2(TPX_USER_LOGOUT_REASON_USER_LOGOUT);
                            }
                            else
                            {
                                // if there is a result then an error with SSO must have occured
                                Welcome_view::displaySSOError($ssoResultArray['result'], $ssoResultArray['resultparam']);
                            }
                        }
					}
				}
			}
			else
			{
				Share_view::previewNotAvailable(true);
			}
 		}
 		else
		{
			Share_view::previewNotAvailable(true);
		}
 	}

	static function reorder()
 	{
        UtilsObj::setSessionDeviceData();

        DatabaseObj::updateSession();

 		$resultArray = Share_model::reorder();

 		// Reorder will return true when redirecting
 		if (true !== $resultArray)
		{
			Share_view::reorderUnavailable($resultArray);
		}
 	}


 	static function unshare()
 	{
		if (AuthenticateObj::WebSessionActive() == 1)
        {
			$resultArray = Share_model::unshare();
			Share_view::unshare($resultArray);
		}
        elseif (AuthenticateObj::WebSessionActive() == 0)
        {
            Welcome_control::processLogout2('str_ErrorSessionExpired');
        }
        else
        {
            Welcome_control::processLogout2('');
        }
 	}

    static function unShareList()
 	{
 		$resultArray = Share_model::unShareList();
 		Share_view::unShareList($resultArray);
 	}
}

?>