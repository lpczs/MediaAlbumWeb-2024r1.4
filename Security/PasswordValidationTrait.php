<?php

namespace Security;

use AuthenticateObj;
use DatabaseObj;
use UtilsObj;

/**
 * Trait PasswordValidationTrait
 * @package Security
 */
trait PasswordValidationTrait
{
    /**
     * @param $pPasswordField
     * @param $pFormatField
     * @param array $pUserAccountArray
     * @return array
     */
    public static function validatePassword($pPasswordField, $pFormatField, $pUserAccountArray = [])
    {
        global $gSession, $gConstants;

        $returnArray = [
            'result' => '',
            'valid' => false
        ];

        // if there's no user array passed into the method
        if (empty($pUserAccountArray))
        {
            $pUserAccountArray = DatabaseObj::getUserAccountFromID($gSession['userid']);
        }

        // grab the password
        $password = filter_input(INPUT_POST, $pPasswordField);

        // a bit pointless to use an int filter on this field...
        $format = filter_input(INPUT_POST, $pFormatField, FILTER_VALIDATE_INT);

        // check the password length
        if (!strlen($password))
        {
            $returnArray['result'] = 'str_ErrorNoPassword';
        }
        else
        {
            // check to see if an external account is being used, and call the validation on the external script
            if (($gConstants['optionwscrp']) && (file_exists('../Customise/scripts/EDL_ExternalCustomerAccount.php')))
            {
                require_once('../Customise/scripts/EDL_ExternalCustomerAccount.php');

                if (method_exists('ExternalCustomerAccountObj', 'authenticate'))
                {
                    $paramArray = [
                        'languagecode' => UtilsObj::getBrowserLocale(),
                        'login' => $pUserAccountArray['login'],
                        'password' => $password,
                        'passwordformat' => $format,
                        'useraccount' => $pUserAccountArray,
                        'designergroupcode' => $pUserAccountArray['groupcode'],
                        'accountgroupcode' => $pUserAccountArray['groupcode'],
                        'brandcode' => $gSession['webbrandcode'],
                        'accountcode' => $pUserAccountArray['accountcode']
                    ];

                    $verifyPasswordResult = \ExternalCustomerAccountObj::authenticate($paramArray);
                    if ($verifyPasswordResult['result'] != '')
                    {
                        if ($verifyPasswordResult['result'] === 'NOTHANDLED')
                        {
                            $returnArray = static::validatePasswordInternal($password, $pUserAccountArray['password'], $format);
                        }
                        else
                        {
                            $returnArray['result'] = $verifyPasswordResult['result'];
                        }
                    }
                    else
                    {
                        $returnArray['valid'] = true;
                    }
                }
            }
            else
            {
                $returnArray = static::validatePasswordInternal($password, $pUserAccountArray['password'], $format);
            }
        }
        return $returnArray;
    }

    /**
     * @param $pCheckPassword
     * @param $pUserPassword
     * @param $pPasswordFormat
     * @return array
     */
    public static function validatePasswordInternal($pCheckPassword, $pUserPassword, $pPasswordFormat)
    {
        $returnArray = [
            'valid' => false,
            'result' => ''
        ];

        $verifyPasswordResult = AuthenticateObj::verifyPassword($pCheckPassword, $pUserPassword, $pPasswordFormat);
        $isValid = $verifyPasswordResult['data']['passwordvalid'];
        if (!$isValid)
        {
            $returnArray['result'] = 'str_MessageAuthMode_Password';
        }

        $returnArray['valid'] = $isValid;
        return $returnArray;
    }
}