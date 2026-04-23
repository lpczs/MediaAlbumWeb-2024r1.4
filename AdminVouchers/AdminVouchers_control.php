<?php

require_once('../Utils/UtilsAuthenticate.php');
require_once('../Utils/UtilsDatabase.php');
require_once('../AdminVouchers/AdminVouchers_model.php');
require_once('../AdminVouchers/AdminVouchers_view.php');
require_once('../AdminVouchersPromotion/AdminVouchersPromotion_control.php');

use Security\RequestValidationTrait;

class AdminVouchers_control
{
	use RequestValidationTrait;	
	
	/**
	 * Echos back an array of vouchers for the promotion id.
	 * 
	 * @param  $_GET['promotionid']
	 *
	 * @since Version 3.0.0
	 * @author Dasha Salo
	 * */
	static function listVouchers()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
			$promotionID = UtilsObj::getGETParam('promotionid', 0);
			AdminVouchers_model::listVouchers($promotionID);
		}
	}
	
	/**
	 * Shows voucherlist.tpl template
	 *
	 * @since Version 1.0.0
	 * @author Kevin Gale
	 * @author Dasha Salo
	 * @version 3.0.0
	 */
	static function displayList()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
			$promotionID = isset($_GET['promotionid']) ? $_GET['promotionid'] : 0;
		    // ? do i need this now?
		    $resultArray = AdminVouchers_model::displayList($promotionID);
		    AdminVouchers_view::displayList($resultArray, 'voucherslist.tpl');
		}
	} 
	
	static function displayVouchersWindow()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
			$promotionID = $_GET['promotionid'];
		    $resultArray = AdminVouchers_model::displayList($promotionID);
			AdminVouchers_view::displayList($resultArray, 'voucherslistwindow.tpl');
		}
	} 
	
	
	static function voucherAddDisplay()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
		    $resultArray = Array();
		    
		    $resultArray['promotionId'] = $_GET['promotionId'];
		    if ($resultArray['promotionId'] > 0)
		    {
		        $resultArray['promotion'] = DatabaseObj::getVoucherPromotionFromID($resultArray['promotionId']);
		    }
		    $resultArray['code'] = AdminVouchers_model::createRandomVoucherCode();
		    
		    AdminVouchers_model::buildEditLists($resultArray);
		    
            AdminVouchers_view::displayAdd($resultArray);
        }
	}    

	static function voucherAdd()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
            AdminVouchers_model::voucherAdd();
        }
	}

	static function voucherEditDisplay()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
            $voucherID = $_GET['id'];
            $resultArray = AdminVouchers_model::displayEdit($voucherID);

            $usedInSession = AdminVouchers_model::isUsedInSession($resultArray['code']);

            if ($usedInSession['resultvalue'] == 1)
            {
              	$smarty = SmartyObj::newSmarty('AdminVouchers');
	        	$msg = $smarty->get_config_vars('str_VoucherInUse');
				$title = $smarty->get_config_vars('str_TitleError');
      	      	echo 'function initialize(){ Ext.MessageBox.show({ title: "'.$title.'", msg: "'.$msg.'", buttons: Ext.MessageBox.OK, animEl: "mb9", icon: Ext.MessageBox.ERROR}); globalFlagDialog = false; singleVoucherEditWindowExists = false;}; ';
            }
            else
            {
              	$resultArray['promotionid'] = $_GET['promotionId'];
                
             	AdminVouchers_model::buildEditLists($resultArray);
                
               	$resultArray['displayMode'] = 0; // add or edit window
               	AdminVouchers_view::displayEntry('str_TitleEditVoucher', $resultArray, 'str_ButtonUpdate', '', '', 'AdminVouchers');
            }
        }
	}    

	static function voucherEdit()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
            AdminVouchers_model::voucherEdit();
        }      
	}

	static function voucherActivate()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
            AdminVouchers_model::voucherActivate();
        }
	}    

	static function voucherDelete()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
            AdminVouchers_model::voucherDelete();
        }
	}

	static function voucherCreateDisplay()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
            $resultArray = Array();
            
            $resultArray['promotionid'] = $_GET['promotionId'];
            if ($resultArray['promotionid'] > 0)
		    {
		        $resultArray['promotion'] = DatabaseObj::getVoucherPromotionFromID($resultArray['promotionid']);
		    }
		    AdminVouchers_model::buildEditLists($resultArray);
            AdminVouchers_view::displayCreate($resultArray);
        }
	}    

    static function voucherCreate()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
            $resultArray['promotionid'] = $_POST['promotionid'];
            $resultArray = AdminVouchers_model::voucherCreate();
        }
	}
	
	static function voucherImportDisplay()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
            $resultArray = Array();
            
            $resultArray['promotionid'] = $_GET['promotionId'];
            
            if ($resultArray['promotionid'] > 0)
		    {
		        $resultArray['promotion'] = DatabaseObj::getVoucherPromotionFromID($resultArray['promotionid']);
		    }
		    AdminVouchers_model::buildEditLists($resultArray);
            AdminVouchers_view::displayImport($resultArray);
        }
	}    
	
	 static function voucherImport()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
            $resultArray = AdminVouchers_model::voucherImport();
            $resultArray['promotionid'] = $_GET['promotionid'];
        }
	}
	
	static function voucherDeleteNew()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
            AdminVouchers_model::voucherDeleteNew();
        }
	}
		
	static function voucherReturnToList()
	{
	    if (AuthenticateObj::adminSessionActive() == 1)
	    {
            AdminVouchers_model::clearSavedVoucherList();
            self::displayList();
        }
	}

	static function voucherExport()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
			$promotionId = $_GET['promotionid'];
			
		    $resultArray = AdminVouchers_model::voucherExport($promotionId);
            AdminVouchers_view::voucherExport($resultArray);
        }
	}

}

?>