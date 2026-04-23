<?php

require_once('../AdminVouchers/AdminVouchers_model.php');

class AdminVouchersSingle_model
{
    static function voucherDeleteExpired($pVoucherType = TPX_VOUCHER_TYPE_DISCOUNT)
    {
        global $gSession;
        global $gConstants;
        
        $voucherArray = Array();
        $totalItemCount = 0;
        $itemCount = 0;
        $result = '';
		$resultParam = '';
		$voucherID = '';
        $voucherCode = '';
        $totalDeleteLimit = 20000;
        
        $dbObj = DatabaseObj::getGlobalDBConnection();
        
        if ($dbObj)
        {
            $voucherTypeWhere = '(`type`';
            
            if ($pVoucherType == TPX_VOUCHER_TYPE_GIFTCARD)
                $voucherTypeWhere .= ' = ';
            else
                $voucherTypeWhere .= ' < ';
            
            $voucherTypeWhere .=  TPX_VOUCHER_TYPE_GIFTCARD . ')';   
            $limit = ' limit 10000';
            
            if ($gConstants['optionms'])
			{
				if ($gSession['userdata']['usertype'] == TPX_LOGIN_COMPANY_ADMIN)
				{
					$stmt = $dbObj->prepare('SELECT `id`, `code` FROM VOUCHERS WHERE `companycode` = ? AND ' . $voucherTypeWhere .' AND TIMESTAMPDIFF(MINUTE, `enddate`, NOW()) > 0' . $limit);
					$bindOK = $stmt->bind_param('s', $gSession['userdata']['companycode']);
				}
				else
				{
					$stmt = $dbObj->prepare('SELECT `id`, `code` FROM VOUCHERS WHERE ' . $voucherTypeWhere .' AND TIMESTAMPDIFF(MINUTE, `enddate`, NOW()) > 0' . $limit);
					$bindOK = true;
				}	
			}
			else
			{
				$stmt = $dbObj->prepare('SELECT `id`, `code` FROM VOUCHERS WHERE ' . $voucherTypeWhere .' AND TIMESTAMPDIFF(MINUTE, `enddate`, NOW()) > 0' . $limit);
				$bindOK = true;
			} 

			while($totalItemCount < $totalDeleteLimit)
			{
				UtilsObj::resetPHPScriptTimeout(60);
				
				if ($stmt)
				{
					if ($bindOK)
					{
						if ($stmt->execute())
						{
							if ($stmt->bind_result($voucherID, $voucherCode))
							{
								while ($stmt->fetch())
								{
									$voucherItem['id'] = $voucherID;
									$voucherItem['code'] = $voucherCode;
									$voucherArray[] = $voucherItem;
								}
							}
							else
							{
								$result = 'str_DatabaseError';
								$resultParam = 'voucherDeleteExpired bindResult ' . $dbObj->error;
							}
						}
						else
						{
							$result = 'str_DatabaseError';
							$resultParam = 'voucherDeleteExpired execute ' . $dbObj->error;
						}
					
						$stmt->free_result();
					}
					else
					{
						$result = 'str_DatabaseError';
						$resultParam = 'voucherDeleteExpired bind ' . $dbObj->error;
					}
				}
				else
				{
					$result = 'str_DatabaseError';
					$resultParam = 'voucherDeleteExpired prepare ' . $dbObj->error;
				}
			
				
				$counter = 0;
				$voucherIDString = '';
				$processedVoucherArray = array();
				$itemCount = count($voucherArray);
				
				if ($itemCount > 0)
				{
					$totalItemCount += $itemCount;
				
					$lastIndex = $itemCount - 1;
					
					for ($i = 0; $i < $itemCount; $i++)
					{            
						$processedVoucherArray[] = array('id' => $voucherArray[$i]['id'], 'code' => $voucherArray[$i]['code']);
			
						$voucherIDString .= $voucherArray[$i]['id'] . ',';
						$counter++;
			
						if (($counter == 1000) || ($i == $lastIndex))
						{
							$voucherIDString = substr($voucherIDString, 0, -1);
				
							$dbObj->query('START TRANSACTION');
				
							AdminVouchers_model::voucherDelete2($dbObj, $voucherIDString, $processedVoucherArray);
				
							$dbObj->query('COMMIT');
				
							$processedVoucherArray = array();
							$counter = 0;
							$voucherIDString = '';
						}
					}
				}
				else
				{
					break;
				}
				
				$voucherArray = array();
			}
			
			$stmt->close();
			$stmt = null;
        }
        
        $dbObj->close();

        if ($result == '')
		{
			echo "{'success':'true', 'msg':'" . '' . "' }";
		}
		else
		{
			echo '{"success":false,	"msg":"' . $resultParam . '"}';
        }
		return;
    }

}
?>
