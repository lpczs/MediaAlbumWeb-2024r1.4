<?php

require_once('../Utils/UtilsAddress.php');

class AdminConstants_model
{
    static function displayEdit()
    {
        global $gConstants;
        
        $resultArray = Array();

		// get the constants
		$gConstants = DatabaseObj::getConstants();
        
        $resultArray['constants'] = $gConstants;
        $resultArray['currencylist'] = DatabaseObj::getCurrencyList();

        return $resultArray;
    }
    
	static function constantsEdit()
    {
        global $gSession;
        global $gConstants;
        
        $resultArray = Array();
        
        $result = '';
        $resultParam = '';
        
        $countryCode = $_POST['location'];
        $currencyCode = $_POST['currency'];
       	$languageCode = $_POST['language'];
     
        $taxAddress = $_POST['taxaddress'];
        
        $ipAccessList = str_replace(' ', '', $_POST['ipaccesslist']);
        $ipAccessList = str_replace(array("\r", "\r\n", "\n"), '', $ipAccessList);
        
        $creditLimit = $_POST['creditlimit'];
        // could contain decimal separator other then '.'
        $decimalSeparator = LocalizationObj::getLocaleDecimalPoint($gSession['browserlanguagecode']);
        $creditLimit = str_replace($decimalSeparator, '.', $creditLimit);

		$maxLoginAttemptOptions = array(
			'options' => array(
				'default' => 10,
				'min_range' => 3
			)
		);
		$accountLockoutTimeOptions = array(
			'options' => array(
				'default' => 15,
				'min_range' => 1
			)
		);
		$maxIPLoginAttemptsOptions = array(
			'options' => array(
				'default' => 15,
				'min_range' => 5
			)
		);
		$maxIPLoginAttemptsMinutesOptions = array(
			'options' => array(
				'default' => 15,
				'min_range' => 1
			)
		);
		$minPasswordScoreOptions = array(
			'options' => array(
				'default' => 2,
				'min_range' => 0,
				'max_range' => 4
			)
		);

		$customerUpdateAuthRequiredOptions = array(
			'options' => array(
				'default' => 1,
				'min_range' => 0,
				'max_range' => 1
			)
		);

		$maxLoginAttempts = filter_input(INPUT_POST, 'maxloginattempts', FILTER_VALIDATE_INT, $maxLoginAttemptOptions);
		$accountLockoutTime = filter_input(INPUT_POST, 'accountlockouttime', FILTER_VALIDATE_INT, $accountLockoutTimeOptions);

		$maxIPLoginAttempts = filter_input(INPUT_POST, 'maxiploginattempts', FILTER_VALIDATE_INT, $maxIPLoginAttemptsOptions);
		$maxIPLoginAttemptsMinutes = filter_input(INPUT_POST, 'maxiploginattemptsminutes', FILTER_VALIDATE_INT, $maxIPLoginAttemptsMinutesOptions);

        $minPasswordScore = filter_input(INPUT_POST, 'minpasswordscore', FILTER_VALIDATE_INT, $minPasswordScoreOptions);
		$customerUpdateAuthRequired = filter_input(INPUT_POST, 'customerupdateauthrequired', FILTER_VALIDATE_INT, $customerUpdateAuthRequiredOptions);

		$dbObj = DatabaseObj::getGlobalDBConnection();
        if ($dbObj)
        {
        	// update CONSTANTS
            $constantsArray = DatabaseObj::getConstants();
            if ($constantsArray['recordid'] > 0)
            {
				$sql = 'UPDATE `CONSTANTS` SET
							`defaultcurrencycode` = ?, `defaultlanguagecode` = ?, `defaultcreditlimit` = ?, `maxloginattempts` = ?,
							`accountlockouttime` = ?, `maxiploginattempts` = ?, `maxiploginattemptsminutes` = ?, `minpasswordscore` = ?,
                       		`customerupdateauthrequired` = ?
						WHERE `id` = ?';

                if ($stmt = $dbObj->prepare($sql))
                {
                    if ($stmt->bind_param('ssdiiiiiii', $currencyCode, $languageCode, $creditLimit, $maxLoginAttempts, $accountLockoutTime, $maxIPLoginAttempts, $maxIPLoginAttemptsMinutes, $minPasswordScore, $customerUpdateAuthRequired, $constantsArray['recordid']))
                    {
                        if ($stmt->execute())
                        {
                            DatabaseObj::updateActivityLog($gSession['ref'], 0, $gSession['userid'], $gSession['userlogin'], $gSession['username'], 0, 
                                'ADMIN', 'CONSTANTS-UPDATE', '', 1);
                        }
                        else
                        {
                            $result = 'str_DatabaseError';
                            $resultParam = 'constantsEdit execute ' . $dbObj->error;
                        }
                    }
                    else
                    {
                        // could not bind parameters
                        $result = 'str_DatabaseError';
                        $resultParam = 'constantsEdit bind ' . $dbObj->error;
                    }
                    $stmt->free_result();
                    $stmt->close();
	                $stmt = null;
                }
                else
                {
                    // could not prepare statement
                    $result = 'str_DatabaseError';
                    $resultParam = 'constantsEdit prepare ' . $dbObj->error;
                }
            }

        	// update COMPANIES
            if ($stmt = $dbObj->prepare('UPDATE `COMPANIES` SET `countrycode` = ?, `countryname` = (SELECT `name` FROM `COUNTRIES` WHERE `isocode2` = ?), `taxaddress` = ?, `ipaccesslist` = ?  WHERE `code` = ""'))
            {
                if ($stmt->bind_param('ssis', $countryCode, $countryCode, $taxAddress, $ipAccessList))
                {
                    if ($stmt->execute())
                    {
                        DatabaseObj::updateActivityLog($gSession['ref'], 0, $gSession['userid'], $gSession['userlogin'], $gSession['username'], 0, 
                            'ADMIN', 'COMPANY-UPDATE', $countryCode . ' - ' . $taxAddress, 1);
                    }
                    else
                    {
                        $result = 'str_DatabaseError';
                        $resultParam = 'companyEdit execute ' . $dbObj->error;
                    }
                }
                else
                {
                    // could not bind parameters
                    $result = 'str_DatabaseError';
                    $resultParam = 'companyEdit bind ' . $dbObj->error;
                }
                $stmt->free_result();
				$stmt->close();
                $stmt = null;
            }
            else
            {
                // could not prepare statement
                $result = 'str_DatabaseError';
                $resultParam = 'companyEdit prepare ' . $dbObj->error;
            }
            
            $dbObj->close();
        }
        else
        {
            // could not open database connection
            $result = 'str_DatabaseError';
            $resultParam = 'constantsEdit connect ' . $dbObj->error;
        }

        $resultArray['result'] = $result;
        $resultArray['resultparam'] = $resultParam;
        
        return $resultArray;
    }
}
?>
