<?php

use Security\RequestValidationTrait;
use Security\PasswordValidationTrait;

require_once(__DIR__.'/../Utils/UtilsAddress.php');
require_once(__DIR__.'/../Utils/UtilsAuthenticate.php');
require_once(__DIR__.'/../Customer/Customer_model.php');
require_once(__DIR__.'/../Customer/Customer_view.php');
require_once(__DIR__.'/../Welcome/Welcome_control.php');

class Customer_control
{
	use RequestValidationTrait;
	use PasswordValidationTrait;

    static function initialize()
    {
        self::initialize2();
 	}

    static function initialize2()
    {
        global $gSession;
        global $gConstants;
        global $ac_config;

        $resultArray = Customer_model::initialize();

        if ($resultArray['result'] == '')
        {
            $userArray = DatabaseObj::getUserAccountFromID($gSession['userid']);
            $brandCode = $userArray['webbrandcode'];
			$showOnlineProjects = 1;
			$isHighLevel = 0;
			$hasFlaggedProjects = false;

			// check if the online designer is active
			if ($gConstants['optiondesol'] == 1)
			{
				$hasFlaggedProjects = Customer_model::getProjectsFlaggedForPurgeState($gSession['userid'], $ac_config);
				$isHighLevel = UtilsObj::isBrandUsingHighLevelAPI($brandCode);

				// if multi line is off, check to make sure no records exist in the `ONLINEBASKET`
				if (! $isHighLevel)
				{
					$userBasketCountArray = DatabaseObj::getBasketCountForUser($gSession['userid']);
					$showOnlineProjects = ($userBasketCountArray['count'] > 0) ? 0 : 1;
				}
				else
				{
					$showOnlineProjects = 0;
				}
			}
			else
			{
				$showOnlineProjects = 0;
			}

			$resultArray['section'] = 'menu';
            $resultArray['user'] = $userArray;
            $resultArray['message'] = '';
            $resultArray['isConfirmation'] = '';
            $resultArray['ishighlevel'] = $isHighLevel;
            $resultArray['showprojectsbutton'] = $showOnlineProjects;
            $resultArray['hasflaggedprojects'] = $hasFlaggedProjects;
            Customer_view::display($resultArray);
        }
        else
        {
            Welcome_control::processLogout2($resultArray['result']);
        }
    }

    static function yourOrders()
    {
        global $gSession;
        global $gConstants;

        if (AuthenticateObj::WebSessionActive() == 1)
        {
            $userArray = DatabaseObj::getUserAccountFromID($gSession['userid']);
            $brandCode = $userArray['webbrandcode'];
			$showOnlineProjects = 1;
			$isHighLevel = 0;

			// check if the online designer is active
			if ($gConstants['optiondesol'] == 1)
			{
				$isHighLevel = UtilsObj::isBrandUsingHighLevelAPI($brandCode);

				// if multi line is off, check to make sure no records exist in the `ONLINEBASKET`
				if (! $isHighLevel)
				{
					$userBasketCountArray = DatabaseObj::getBasketCountForUser($gSession['userid']);
					$showOnlineProjects = ($userBasketCountArray['count'] > 0) ? 0 : 1;
				}
				else
				{
					$showOnlineProjects = 0;
				}
			}
			else
			{
				$showOnlineProjects = 0;
			}

            $resultArray = Array();
            $resultArray['result'] = 0;
            $resultArray['section'] = 'yourorders';
            $resultArray['user'] = $userArray;
            $resultArray['orders'] = Customer_model::getOrderList();
            $resultArray['message'] = '';
            $resultArray['isConfirmation'] = '';
            $resultArray['ishighlevel'] = $isHighLevel;
            $resultArray['showprojectsbutton'] = $showOnlineProjects;

            Customer_view::display($resultArray, $gSession['ismobile']);
        }
        elseif (AuthenticateObj::WebSessionActive() == 0)
        {
            Welcome_control::processLogout2(TPX_USER_LOGOUT_REASON_SESSION_EXPIRED);
        }
        else
        {
            Welcome_control::processLogout2(TPX_USER_LOGOUT_REASON_USER_LOGOUT);
        }
    }

    static function accountDetails()
    {
        global $gSession;
        global $gConstants;

        if (AuthenticateObj::WebSessionActive() == 1)
        {
            $userArray = DatabaseObj::getUserAccountFromID($gSession['userid']);
            $brandCode = $userArray['webbrandcode'];
			$showOnlineProjects = 1;
			$isHighLevel = 0;

			// check if the online designer is active
			if ($gConstants['optiondesol'] == 1)
			{
				$isHighLevel = UtilsObj::isBrandUsingHighLevelAPI($brandCode);

				// if multi line is off, check to make sure no records exist in the `ONLINEBASKET`
				if (! $isHighLevel)
				{
					$userBasketCountArray = DatabaseObj::getBasketCountForUser($gSession['userid']);
					$showOnlineProjects = ($userBasketCountArray['count'] > 0) ? 0 : 1;
				}
				else
				{
					$showOnlineProjects = 0;
				}
			}
			else
			{
				$showOnlineProjects = 0;
			}

			$hasOutstandingEmailChange = AuthenticateObj::hasOutstandingEmailChange($gSession['userid']);

			if ($hasOutstandingEmailChange['error'] === '')
			{
				$userArray['pendingEmailChange'] = ($hasOutstandingEmailChange['data']['pendingemailupdates'] > 0);
			}

            $resultArray = Array();
            $resultArray['result'] = 0;
            $resultArray['section'] = 'accountdetails';
            $resultArray['user'] = $userArray;
            $resultArray['message'] = '';
            $resultArray['isConfirmation'] = '';
            $resultArray['ishighlevel'] = $isHighLevel;
            $resultArray['showprojectsbutton'] = $showOnlineProjects;

            Customer_view::display($resultArray, $gSession['ismobile']);
        }
        elseif (AuthenticateObj::WebSessionActive() == 0)
        {
            Welcome_control::processLogout2(TPX_USER_LOGOUT_REASON_SESSION_EXPIRED);
        }
        else
        {
            Welcome_control::processLogout2(TPX_USER_LOGOUT_REASON_USER_LOGOUT);
        }
    }

    static function updateAccountDetails()
    {
        global $gSession;
        global $gConstants;

		if (AuthenticateObj::WebSessionActive() == 1)
        {
            $resultArray = Array();
            $resultArray['result'] = 0;
            $resultMessage = Customer_model::updateAccountDetails(TPX_CUSTOMER_ACCOUNT_OVERRIDE_REASON_CUSTOMERUPDATEDETAILS);

            $resultArray['message'] = $resultMessage['result'];
            $resultArray['isConfirmation'] = $resultMessage['isConfirmation'];
            $resultArray['section'] = 'accountdetails';
            if ($resultArray['isConfirmation'])
            {
                $resultArray['section'] = 'menu';
            }
            else
            {
                $resultArray['section'] = 'accountdetails';
            }

			$userArray = DatabaseObj::getUserAccountFromID($gSession['userid']);
            $brandCode = $userArray['webbrandcode'];
			$showOnlineProjects = 1;
			$isHighLevel = 0;

			// check if the online designer is active
			if ($gConstants['optiondesol'] == 1)
			{
				$isHighLevel = UtilsObj::isBrandUsingHighLevelAPI($brandCode);

				// if multi line is off, check to make sure no records exist in the `ONLINEBASKET`
				if (! $isHighLevel)
				{
					$userBasketCountArray = DatabaseObj::getBasketCountForUser($gSession['userid']);
					$showOnlineProjects = ($userBasketCountArray['count'] > 0) ? 0 : 1;
				}
				else
				{
					$showOnlineProjects = 0;
				}
			}
			else
			{
				$showOnlineProjects = 0;
			}

            $resultArray['user'] = $userArray;
            $resultArray['ishighlevel'] = $isHighLevel;
            $resultArray['showprojectsbutton'] = $showOnlineProjects;

            if ($gSession['ismobile'] == true)
            {
                Customer_view::updateAjaxAction($resultArray);
            }
            else
            {
                Customer_view::display($resultArray);
            }
        }
        elseif (AuthenticateObj::WebSessionActive() == 0)
        {
            Welcome_control::processLogout2(TPX_USER_LOGOUT_REASON_SESSION_EXPIRED);
        }
        else
        {
            Welcome_control::processLogout2(TPX_USER_LOGOUT_REASON_USER_LOGOUT);
        }
    }

    static function changePassword()
    {
        global $gSession;
        global $gConstants;

        if (AuthenticateObj::WebSessionActive() == 1)
        {
            $userArray = DatabaseObj::getUserAccountFromID($gSession['userid']);
            $brandCode = $userArray['webbrandcode'];
			$showOnlineProjects = 1;
			$isHighLevel = 0;

			// check if the online designer is active
			if ($gConstants['optiondesol'] == 1)
			{
				$isHighLevel = UtilsObj::isBrandUsingHighLevelAPI($brandCode);

				// if multi line is off, check to make sure no records exist in the `ONLINEBASKET`
				if (! $isHighLevel)
				{
					$userBasketCountArray = DatabaseObj::getBasketCountForUser($gSession['userid']);
					$showOnlineProjects = ($userBasketCountArray['count'] > 0) ? 0 : 1;
				}
				else
				{
					$showOnlineProjects = 0;
				}
			}
			else
			{
				$showOnlineProjects = 0;
			}

            $resultArray = Array();
            $resultArray['result'] = 0;
            $resultArray['section'] = 'changepassword';
            $resultArray['user'] = $userArray;
            $resultArray['message'] = '';
            $resultArray['isConfirmation'] = '';
            $resultArray['ishighlevel'] = $isHighLevel;
            $resultArray['showprojectsbutton'] = $showOnlineProjects;
            Customer_view::display($resultArray, $gSession['ismobile']);
        }
        elseif (AuthenticateObj::WebSessionActive() == 0)
        {
            Welcome_control::processLogout2(TPX_USER_LOGOUT_REASON_SESSION_EXPIRED);
        }
        else
        {
            Welcome_control::processLogout2(TPX_USER_LOGOUT_REASON_USER_LOGOUT);
        }
    }

    static function updatePassword()
    {
        global $gSession;
        global $gConstants;

        if (AuthenticateObj::WebSessionActive() == 1)
        {
            $userArray = DatabaseObj::getUserAccountFromID($gSession['userid']);
            $brandCode = $userArray['webbrandcode'];
			$showOnlineProjects = 1;
			$isHighLevel = 0;

			// check if the online designer is active
			if ($gConstants['optiondesol'] == 1)
			{
				$isHighLevel = UtilsObj::isBrandUsingHighLevelAPI($brandCode);

				// if multi line is off, check to make sure no records exist in the `ONLINEBASKET`
				if (! $isHighLevel)
				{
					$userBasketCountArray = DatabaseObj::getBasketCountForUser($gSession['userid']);
					$showOnlineProjects = ($userBasketCountArray['count'] > 0) ? 0 : 1;
				}
				else
				{
					$showOnlineProjects = 0;
				}
			}
			else
			{
				$showOnlineProjects = 0;
			}

            $resultArray = Array();
            $resultArray['result'] = 0;
            $resultMessage = Customer_model::updatePassword();
            $resultArray['message'] = $resultMessage['result'];
            $resultArray['isConfirmation'] = $resultMessage['isConfirmation'];
            $resultArray['user'] = $userArray;
            $resultArray['ishighlevel'] = $isHighLevel;
            $resultArray['showprojectsbutton'] = $showOnlineProjects;

            if ($resultArray['isConfirmation']){
                $resultArray['section'] = 'menu';
            }
            else
            {
                $resultArray['section'] = 'changepassword';
            }

            if ($gSession['ismobile'] == true)
            {
                Customer_view::updateAjaxAction($resultArray);
            }
            else
            {
                Customer_view::display($resultArray);
            }
        }
        elseif (AuthenticateObj::WebSessionActive() == 0)
        {
            Welcome_control::processLogout2(TPX_USER_LOGOUT_REASON_SESSION_EXPIRED);
        }
        else
        {
            Welcome_control::processLogout2(TPX_USER_LOGOUT_REASON_USER_LOGOUT);
        }
    }

    static function changePreferences()
    {
        global $gSession;
        global $gConstants;

        if (AuthenticateObj::WebSessionActive() == 1)
        {
            $userArray = DatabaseObj::getUserAccountFromID($gSession['userid']);
            $brandCode = $userArray['webbrandcode'];
			$showOnlineProjects = 1;
			$isHighLevel = 0;

			// check if the online designer is active
			if ($gConstants['optiondesol'] == 1)
			{
				$isHighLevel = UtilsObj::isBrandUsingHighLevelAPI($brandCode);

				// if multi line is off, check to make sure no records exist in the `ONLINEBASKET`
				if (! $isHighLevel)
				{
					$userBasketCountArray = DatabaseObj::getBasketCountForUser($gSession['userid']);
					$showOnlineProjects = ($userBasketCountArray['count'] > 0) ? 0 : 1;
				}
				else
				{
					$showOnlineProjects = 0;
				}
			}
			else
			{
				$showOnlineProjects = 0;
			}

            $resultArray = Array();
            $resultArray['result'] = 0;
            $resultArray['section'] = 'changepreferences';
            $resultArray['user'] = $userArray;
            $resultArray['message'] = '';
            $resultArray['isConfirmation'] = '';
            $resultArray['ishighlevel'] = $isHighLevel;
            $resultArray['showprojectsbutton'] = $showOnlineProjects;

            Customer_view::display($resultArray, $gSession['ismobile']);
        }
        elseif (AuthenticateObj::WebSessionActive() == 0)
        {
            Welcome_control::processLogout2(TPX_USER_LOGOUT_REASON_SESSION_EXPIRED);
        }
        else
        {
            Welcome_control::processLogout2(TPX_USER_LOGOUT_REASON_USER_LOGOUT);
        }
    }

    static function updatePreferences()
    {
        global $gSession;
        global $gConstants;

        if (AuthenticateObj::WebSessionActive() == 1)
        {
            $userArray = DatabaseObj::getUserAccountFromID($gSession['userid']);
            $brandCode = $userArray['webbrandcode'];
			$showOnlineProjects = 1;
			$isHighLevel = 0;

			// check if the online designer is active
			if ($gConstants['optiondesol'] == 1)
			{
				$isHighLevel = UtilsObj::isBrandUsingHighLevelAPI($brandCode);

				// if multi line is off, check to make sure no records exist in the `ONLINEBASKET`
				if (! $isHighLevel)
				{
					$userBasketCountArray = DatabaseObj::getBasketCountForUser($gSession['userid']);
					$showOnlineProjects = ($userBasketCountArray['count'] > 0) ? 0 : 1;
				}
				else
				{
					$showOnlineProjects = 0;
				}
			}
			else
			{
				$showOnlineProjects = 0;
			}

            $resultArray = Array();
            $resultArray['result'] = 0;
            $resultMessage = Customer_model::updatePreferences();
            $resultArray['message'] = $resultMessage['result'];
            $resultArray['isConfirmation'] = $resultMessage['isConfirmation'];
            if ($resultArray['isConfirmation']){
                $resultArray['section'] = 'menu';
            } else {
                $resultArray['section'] = 'changepreferences';
            }
            $resultArray['user'] = $userArray;
            $resultArray['ishighlevel'] = $isHighLevel;
            $resultArray['showprojectsbutton'] = $showOnlineProjects;

            if ($gSession['ismobile'] == true)
            {
                Customer_view::updateAjaxAction($resultArray);
            }
            else
            {
                Customer_view::display($resultArray);
            }
        }
        elseif (AuthenticateObj::WebSessionActive() == 0)
        {
            Welcome_control::processLogout2(TPX_USER_LOGOUT_REASON_SESSION_EXPIRED);
        }
        else
        {
            Welcome_control::processLogout2(TPX_USER_LOGOUT_REASON_USER_LOGOUT);
        }
    }


    static function showPreview()
    {
		$uploadRef = (isset($_GET['ref2'])) ? $_GET['ref2'] : '';
        $orderItemId = (isset($_GET['id'])) ? $_GET['id'] : 0;
        $projectRef = (isset($_GET['projectref'])) ? $_GET['projectref'] : '';

        $canCreateAccounts = 0;
        $originalOrderItem = DatabaseObj::getOriginalOrderLineFromUploadRef($uploadRef, $orderItemId);

        if ($originalOrderItem['result'] == '')
        {
            $licenseKey = $originalOrderItem['groupcode'];
            $licenseKeyArray = DatabaseObj::getLicenseKeyFromCode($licenseKey);
            $canCreateAccounts = $licenseKeyArray['cancreateaccounts'];
        }

		if (AuthenticateObj::WebSessionActive() == 1)
        {
            $resultArray = Customer_model::showPreview();

			if (! empty($resultArray['pages']))
			{
				// display the preview with the existing pages
				Customer_view::showPreview($resultArray);
			}
			else
			{
				// if the project is from online, check if it can be used to display the preview
				if ($resultArray['ordersource'] == TPX_SOURCE_ONLINE)
				{
					// project has come from online

					// Check the preview is available, or if the order data has been deleted.
					if ((($resultArray['productionstatus'] == TPX_ITEM_STATUS_AWAITING_FILES) && ($resultArray['canupload'] == 1)) ||
						($originalOrderItem['dataavailable'] === 0))
					{
						Share_view::previewNotAvailable(false);
					}
					else
					{
						// send notification to restore project, if required
						$paramArray = array('projectref' => $projectRef);

						$doRestore = OnlineAPI_model::restoreOnlineProject($paramArray);

						// a status of 1 means the project has been restored (or did not need to be restored)
						// and is now in the database
						if ($doRestore['restorestatus']['status'] == 1)
						{
							// project now exists in the database, show preview
							Customer_view::showPreview($resultArray);
						}
						else
						{
							// restore failed, show preview not available notice.
							Share_view::previewNotAvailable(false);
						}
					}
				}
				else
				{
					// the preview cannot be displayed
					Share_view::previewNotAvailable(false);
				}
			}
        }
        elseif (AuthenticateObj::WebSessionActive() == 0)
        {
            Welcome_control::processLogout(TPX_USER_LOGOUT_REASON_SESSION_EXPIRED, $canCreateAccounts, 'str_ErrorSessionExpired');
        }
        else
        {
            Welcome_control::processLogout(TPX_USER_LOGOUT_REASON_SESSION_EXPIRED, $canCreateAccounts, '');
        }
    }

    static function updateGiftCard()
    {
        global $gSession;
        global $gConstants;

		if (AuthenticateObj::WebSessionActive() == 1)
        {

			// get gift card values
			$pVoucherCode = $_POST['giftcardcode'];
			$pBackToSection = $_POST['giftcardaction'];
			$showgiftcardmessage = $_POST['showgiftcardmessage'];
			$userArray = DatabaseObj::getUserAccountFromID($gSession['userid']);
			$licenseKeyCode = $userArray['groupcode'];
			$licenseKeyArray = DatabaseObj::getLicenseKeyFromCode($licenseKeyCode);

			// check and update the users gift card balance
			$resultArray = DatabaseObj::getVoucher($pVoucherCode, 0, '', $licenseKeyCode, $gSession['userid'], time(), $licenseKeyArray['companyCode'], TPX_VOUCHER_TYPE_GIFTCARD);

			if ($pBackToSection == 'existingonlineprojects')
			{
				$projectListResult = Customer_model::getOnlineProjectList($gSession['userid']);
				$resultArray['projects'] = $projectListResult['projects'];
			}
			else
			{
				$resultArray['orders'] = Customer_model::getOrderList();
			}

			// return to the page that sent the command
			$resultArray['section'] = $pBackToSection;

			$userArray = DatabaseObj::getUserAccountFromID($gSession['userid']);
			$brandCode = $userArray['webbrandcode'];
			$showOnlineProjects = 1;
			$isHighLevel = 0;

			// check if the online designer is active
			if ($gConstants['optiondesol'] == 1)
			{
				$isHighLevel = UtilsObj::isBrandUsingHighLevelAPI($brandCode);

				// if multi line is off, check to make sure no records exist in the `ONLINEBASKET`
				if (! $isHighLevel)
				{
					$userBasketCountArray = DatabaseObj::getBasketCountForUser($gSession['userid']);
					$showOnlineProjects = ($userBasketCountArray['count'] > 0) ? 0 : 1;
				}
				else
				{
					$showOnlineProjects = 0;
				}
			}
			else
			{
				$showOnlineProjects = 0;
			}

			$resultArray['user'] = $userArray;
			$resultArray['ishighlevel'] = $isHighLevel;
            $resultArray['showprojectsbutton'] = $showOnlineProjects;
			$resultArray['message'] = '';
			$resultArray['isConfirmation'] = '';

			$gSession['showgiftcardmessage'] = $showgiftcardmessage;
			DatabaseObj::updateSession();

			if ($gSession['ismobile'] == true)
			{
				Customer_view::updateGiftCard($resultArray);
			}
			else
			{
				Customer_view::display($resultArray);
			}
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


	static function logout()
	{
		Welcome_control::processLogout2(TPX_USER_LOGOUT_REASON_USER_LOGOUT);
	}


    static function displayOnlineProjectList()
    {
        global $gSession;
        global $gConstants;

		if (AuthenticateObj::WebSessionActive() == 1)
		{
			$userArray = DatabaseObj::getUserAccountFromID($gSession['userid']);
			$brandCode = $userArray['webbrandcode'];
			$showOnlineProjects = 1;
			$isHighLevel = 0;

			// check if the online designer is active
			if ($gConstants['optiondesol'] == 1)
			{
				$isHighLevel = UtilsObj::isBrandUsingHighLevelAPI($brandCode);

				// if multi line is off, check to make sure no records exist in the `ONLINEBASKET`
				if (! $isHighLevel)
				{
					$userBasketCountArray = DatabaseObj::getBasketCountForUser($gSession['userid']);
					$showOnlineProjects = ($userBasketCountArray['count'] > 0) ? 0 : 1;
				}
				else
				{
					$showOnlineProjects = 0;
				}
			}
			else
			{
				$showOnlineProjects = 0;
			}

			$resultArray = Customer_model::getOnlineProjectList($gSession['userid']);

			$resultArray['result'] = 0;
			$resultArray['section'] = 'existingonlineprojects';
			$resultArray['user'] = $userArray;
			$resultArray['ishighlevel'] = $isHighLevel;
            $resultArray['showprojectsbutton'] = $showOnlineProjects;
			$resultArray['message'] = '';
			$resultArray['isConfirmation'] = '';

			Customer_view::display($resultArray, $gSession['ismobile']);

		}
		elseif (AuthenticateObj::WebSessionActive() == 0)
		{
			Welcome_control::processLogout2(TPX_USER_LOGOUT_REASON_SESSION_EXPIRED);
		}
		else
		{
			Welcome_control::processLogout2(TPX_USER_LOGOUT_REASON_USER_LOGOUT);
		}
    }

    static function verifyPassword()
    {
        if(AuthenticateObj::WebSessionActive() == 1)
        {
            $returnArray = static::validatePassword('password', 'format');

            Customer_view::verifyPassword($returnArray);

        }
        elseif (AuthenticateObj::WebSessionActive() == 0)
        {
            Welcome_control::processLogout2(TPX_USER_LOGOUT_REASON_SESSION_EXPIRED);
        }
        else
        {
            Welcome_control::processLogout2(TPX_USER_LOGOUT_REASON_USER_LOGOUT);
        }
    }

    static function deleteOrder()
	{
		// Get the global config and constants as we use this later in the process and rather than grab them when needed we pass them through.
		global $ac_config;
		global $gConstants;
		global $gSession;

		if(AuthenticateObj::WebSessionActive() == 1)
		{
			require_once(__DIR__.'/../DataRedactionAPI/DataRedactionAPI_model.php');
			$orderId = (int) UtilsObj::getArrayParam($_POST, 'orderid', -1);

			if ($orderId !== -1)
			{
				$configDetails = [
					'ac_config' => $ac_config,
					'constants' => $gConstants,
				];

				// Operation to flag order as deleted.
				$projectDeleted = Customer_model::deleteOrder($orderId, $configDetails, $gSession['userid']);

				echo json_encode($projectDeleted);
			}
		}
		elseif (AuthenticateObj::WebSessionActive() == 0)
		{
			Welcome_control::processLogout2(TPX_USER_LOGOUT_REASON_SESSION_EXPIRED);
		}
		else
		{
			Welcome_control::processLogout2(TPX_USER_LOGOUT_REASON_USER_LOGOUT);
		}
	}
}

?>