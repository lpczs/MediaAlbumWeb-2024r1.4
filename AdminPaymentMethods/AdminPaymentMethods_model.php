<?php

require_once('../Utils/UtilsDatabase.php');

class AdminPaymentMethods_model
{
    static function getGridData()
    {
    	global $gConstants;
    	
    	$paymentMethodsList = DatabaseObj::getPaymentMethodsList();
    	
    	if(!$gConstants['optioncfs'])
		{
	    	$itemCount = count($paymentMethodsList);
	    	for ($i = 0; $i < $itemCount; $i++)
	    	{
	    		if($paymentMethodsList[$i]['code'] == 'PAYINSTORE')
	    		{
	    			unset($paymentMethodsList[$i]);
	    		}
	    	}
		}
    	
    	return $paymentMethodsList;	
    }
    
    static function paymentMethodActivate()
    {
        global $gSession;
        
        $resultArray = Array();
        $ids = $_POST['ids'];
        $codes = $_POST['codelist'];
        $codeList = explode(',',$codes);
        
        $idList = explode(',',$ids);
        $active = $_POST['active'];
        if ($active != '0') $active = 1;
        
        $itemCount = count($idList);
                
        $dbObj = DatabaseObj::getGlobalDBConnection();
        if ($dbObj)
        {
            if ($stmt = $dbObj->prepare('UPDATE `PAYMENTMETHODS` SET `active` = ? WHERE `id` = ?'))
            {
                for($i=0; $i < $itemCount; $i++)
        		{
	                if ($stmt->bind_param('ii', $active, $idList[$i]))
	                {
	                    if ($stmt->execute())
	                    {
	                        if ($active == 1)
	                        {
	                            DatabaseObj::updateActivityLog($gSession['ref'], 0, $gSession['userid'], $gSession['userlogin'], $gSession['username'], 0, 
	                                    'ADMIN', 'PAYMENTMETHOD-DEACTIVATE', $idList[$i] . ' ' . $codeList[$i], 1);
	                        }
	                        else
	                        {
	                            DatabaseObj::updateActivityLog($gSession['ref'], 0, $gSession['userid'], $gSession['userlogin'], $gSession['username'], 0, 
	                                    'ADMIN', 'PAYMENTMETHOD-ACTIVATE', $idList[$i] . ' ' . $codeList[$i], 1);
	                        }
	                    }
	                   
	                    $resultArray[$i]['recordid'] = $idList[$i];
	                    $resultArray[$i]['isactive'] = $active;
	                }
            	}
                $stmt->free_result();
	            $stmt->close();
	            $stmt = null;
            }
            $dbObj->close();
        }
        return $resultArray;
    }
    
    static function displayEdit($pID)
	{
	    $resultArray = Array();
	    
	    $paymentMethodID = 0;
        $paymentMethodCode = '';
        $paymentMethodName = '';
        $availableWhenShipping = 0;
        $availableWhenNotShipping = 0;
        $isActive = 0;
        
        $dbObj = DatabaseObj::getGlobalDBConnection();
	    if ($dbObj)
	    {
	        if ($stmt = $dbObj->prepare('SELECT `id`, `code`, `name`, `availablewhenshipping`, `availablewhennotshipping`, `active` FROM `PAYMENTMETHODS` WHERE `id` = ?'))
	        {
	            if ($stmt->bind_param('i', $pID))
	            {
                    if ($stmt->execute())
                    {
                        if ($stmt->store_result())
                        { 
                            if ($stmt->num_rows > 0)
                            {
                                if ($stmt->bind_result($paymentMethodID, $paymentMethodCode, $paymentMethodName, $availableWhenShipping, $availableWhenNotShipping, $isActive))
            	                {    	                    	               
                                    if (!$stmt->fetch())
                                    {
                                        $error = 'displayEdit fetch ' . $dbObj->error;
                                    }
                                }
                                else
                                {
                                    $error = 'displayEdit bind result ' . $dbObj->error;
                                }
                            }
                        }
                        else
                        {
                            $error = 'displayEdit store result ' . $dbObj->error;
                        }
                    }
                    else
                    {
                        $error = 'displayEdit execute ' . $dbObj->error;
                    }
                }
                else 
                {
                    $error = 'displayEdit bind params ' . $dbObj->error;
                }
                $stmt->free_result();
	            $stmt->close();
	            $stmt = null;
            }
            else
            {
                $error = 'displayEdit prepare ' . $dbObj->error;
            }
            $dbObj->close();
        }

        $resultArray['id'] = $paymentMethodID;
        $resultArray['code'] = $paymentMethodCode;
        $resultArray['name'] = $paymentMethodName;
        $resultArray['availablewhenshipping'] = $availableWhenShipping;
        $resultArray['availablewhennotshipping'] = $availableWhenNotShipping;
        $resultArray['isactive'] = $isActive;
        
        return $resultArray;
    }
    
    static function paymentMethodEdit()
    {
        global $gSession;
        
        $result = '';
        $resultParam = '';
        
        $id = $_GET['id'];
        $code = $_POST['code'];
        $name = html_entity_decode($_POST['name'], ENT_QUOTES);
        $availableWhenShipping = $_POST['availablewhenshipping'];
        $availableWhenNotShipping = $_POST['availablewhennotshipping'];
        $isActive = $_POST['isactive'];
        
        if (($id > 0) && ($name !=''))
        {
            $dbObj = DatabaseObj::getGlobalDBConnection();
            if ($dbObj)
            {
                if ($stmt = $dbObj->prepare('UPDATE `PAYMENTMETHODS` SET `name` = ?, `availablewhenshipping` = ?, `availablewhennotshipping` = ?, `active` = ? WHERE `id` = ?'))
                {
                    if ($stmt->bind_param('siiii', $name, $availableWhenShipping, $availableWhenNotShipping, $isActive, $id))
                    {
                        if ($stmt->execute())
                        {
                            DatabaseObj::updateActivityLog($gSession['ref'], 0, $gSession['userid'], $gSession['userlogin'], $gSession['username'], 0, 
                                'ADMIN', 'PAYMENTMETHOD-UPDATE', $id . ' ' . $code, 1);
                        }
                        else
                        {
                            $result = 'str_DatabaseError';
                            $resultParam = 'paymentMethodEdit execute ' . $dbObj->error;
                        }
                    }
                    else
                    {
                        // could not bind parameters
                        $result = 'str_DatabaseError';
                        $resultParam = 'paymentMethodEdit bind ' . $dbObj->error;
                    }
                    $stmt->free_result();
	                $stmt->close();
	                $stmt = null;
                }
                else
                {
                    // could not prepare statement
                    $result = 'str_DatabaseError';
                    $resultParam = 'paymentMethodEdit prepare ' . $dbObj->error;
                }
                $dbObj->close();
            }
            else
            {
                // could not open database connection
                $result = 'str_DatabaseError';
                $resultParam = 'paymentMethodEdit connect ' . $dbObj->error;
            }
        }
        
        $resultArray['result'] = $result;
        $resultArray['resultparam'] = $resultParam;
        $resultArray['id'] = $id;
        $resultArray['name'] = $name;
        $resultArray['availablewhenshipping'] = $availableWhenShipping;
        $resultArray['availablewhennotshipping'] = $availableWhenNotShipping;
        $resultArray['isactive'] = $isActive;
        
        return $resultArray;
    }  
}
?>
