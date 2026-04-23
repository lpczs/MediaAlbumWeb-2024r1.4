<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

define('TPX_INTERNALTASK_UPLOADREMINDERINTERVALDAYS', 5);

class uploadReminder
{
    // define default settings for this task 
    static function register()
    {
        $defaultSettings = array();

        /*
         * $defaultSettings('type') defines type of tasks
         * 0 - scheduled
         * 1 - service
         * 2 - manual
         */
        $defaultSettings['type'] = '0';
        $defaultSettings['code'] = 'TAOPIX_UPLOADREMINDER';
        $defaultSettings['name'] = 'it italian desciption<p>fr french description<p>es spanish text';

        /*
         * $defaultSettings('intervalType') defines inteval value
         * 1 - Number of minutes
         * 2 - Exact time of the day
         * 3 - Number of days
         */
         
        $defaultSettings['intervalType'] = '2';
        $defaultSettings['intervalValue'] = '00:00:00';
        $defaultSettings['maxRunCount'] = '10';
        $defaultSettings['deleteCompletedDays'] = '5';
        
        return $defaultSettings;
    }

    // function to run this task
    static function run($pEventID)
    {
        $resultMessage = '';
        $sendingResult = array();
        
        try
        {
            $taskCode = self::register();
            $taskCode = $taskCode['code'];

			TaskObj::writeLogEntry('Task: ' . $taskCode . '. Retrieving Order Records.');

			$dbObj = DatabaseObj::getGlobalDBConnection();
			
			$serverTime = TaskObj::getServerTime();
			$serverTimeOffset = date('Y-m-d H:i:s', strtotime($serverTime) - TPX_INTERNALTASK_UPLOADREMINDERINTERVALDAYS * (24 * 60 * 60));
			$orderItemStatus = TPX_ITEM_STATUS_AWAITING_FILES;
			$uploadMethod = TPX_UPLOAD_DELIVERY_METHOD_INTERNET;
			
			if ($dbObj)
			{ 
				if ($stmt = $dbObj->prepare('SELECT `oh`.`id`, `oi`.`id`, `oh`.`datecreated`, `oh`.`ordernumber`, `oh`.`webbrandcode`, `oh`.`userid`, `oh`.`languagecode`, `oh`.`billingcustomeremailaddress`, 
					`oh`.`billingcontactfirstname`, `oh`.`billingcontactlastname`,  `oi`.`projectname`, `oi`.`productcollectionname`, `oi`.`productname`, `br`.`applicationname`, `br`.`displayurl`
					FROM `ORDERITEMS` oi 
					LEFT JOIN `ORDERHEADER` oh ON oh.id = oi.orderid
					LEFT JOIN `BRANDING` br ON br.code = oh.webbrandcode  
					WHERE `oh`.`datecreated` >= ? AND `oi`.`status` = ? AND `oi`.`uploadmethod` = ? AND `oi`.`active` = 0 AND `oi`.`source` = 0'))
				{
					
					$stmt->attr_set(MYSQLI_STMT_ATTR_CURSOR_TYPE, MYSQLI_CURSOR_TYPE_READ_ONLY);

					if ($stmt->bind_param('sii', $serverTimeOffset, $orderItemStatus, $uploadMethod))
                	{
						if ($stmt->bind_result($orderHeaderID, $orderItemID, $dateCreated, $orderNumber, $webBrandCode, $userID, $languageCode, $customerEmailAddress, $customerFirstName, $customerLastName, 
							$projectName, $productCollectionName, $productName, $webBrandApplicationName, $webBrandDisplayUrl))
						{
							if ($stmt->execute())
							{
								while ($stmt->fetch())
								{
									TaskObj::sendTemplateEmail('customer_uploadreminder', $webBrandCode, $webBrandApplicationName,
										$webBrandDisplayUrl, $languageCode,
										$customerFirstName . ' ' . $customerLastName, $customerEmailAddress,
										'', '',
										$userID,
										Array(
											'orderid' => $orderHeaderID,
											'orderitemid' => $orderItemID,
											'ordernumber' => $orderNumber,
											'orderdate' => $dateCreated,
											'webbrandcode' => $webBrandCode,
											'projectname' => $projectName,
											'productcollectionname' => TaskObj::getLocaleString($productCollectionName, $languageCode, true),
											'productname' => TaskObj::getLocaleString($productName, $languageCode, true),
											'billingcontactfirstname' => $customerFirstName
										),
										'', ''
									);
								}
							}
						}
					}
				}

				$stmt->free_result();
				$stmt->close();
			}
        } 
        catch (Exception $e)
        {
            $resultMessage = 'en ' . $e->getMessage();
        }

        return $resultMessage;
    }
}

?>