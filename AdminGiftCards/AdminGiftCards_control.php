<?php

require_once('../Utils/UtilsAuthenticate.php');
require_once('../Utils/UtilsDatabase.php');
require_once('../Utils/UtilsConstants.php');
require_once('../AdminVouchers/AdminVouchers_model.php');
require_once('../AdminGiftCards/AdminGiftCards_model.php');
require_once('../AdminGiftCards/AdminGiftCards_view.php');

use Security\RequestValidationTrait;

class AdminGiftCards_control
{
	use RequestValidationTrait;

	static function listGiftCards()
	{
        if (AuthenticateObj::adminSessionActive() == 1)
        {	    
            $resultArray = AdminGiftCards_model::listGiftcards();
          
		  	if ($resultArray['result']=='')
			{
            	AdminGiftCards_view::displayListData($resultArray);
        	}
		}
	}

	static function displayList()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
		    AdminGiftCards_view::displayList();
		}
	}

	static function giftcardAddDisplay()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
		    $resultArray = array();
		    
		    $resultArray['code'] = AdminVouchers_model::createRandomVoucherCode();
		    
		    AdminGiftCards_model::buildEditLists($resultArray);

            AdminGiftCards_view::displayAdd($resultArray);
        }
	}

	static function giftcardAdd()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
            $resultArray = AdminGiftCards_model::giftcardAdd();
            
            AdminGiftCards_view::giftcardAdd($resultArray);
        }
	}

	static function giftcardEditDisplay()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
            $giftcardid = $_GET['giftcardid'];
            $resultArray = AdminGiftCards_model::displayEdit($giftcardid);  

            AdminGiftCards_model::buildEditLists($resultArray);

            //If the voucher has been redeemed don't allow editing
            if ($resultArray['redeemuserid']>0)
                $resultArray['displayMode'] = TPX_READONLY_FORM_TYPE; // readonly window
            else   
                $resultArray['displayMode'] = TPX_ADD_EDIT_FORM_TYPE; // edit window
                
           	AdminGiftCards_view::displayEntry('str_TitleEditGiftCard', $resultArray, 'str_ButtonUpdate', '', '', 'AdminGiftCards');
        }
    }

    static function giftcardEdit()
    {
        if (AuthenticateObj::adminSessionActive() == 1)
        {
            $resultArray = AdminGiftCards_model::giftcardEdit();
            
            AdminGiftCards_view::giftcardEdit($resultArray);
        }      
	}

	static function giftcardActivate()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
            $resultArray = AdminGiftCards_model::giftcardActivate();
            AdminGiftCards_view::giftcardActivate($resultArray);
        }
	}    

	static function giftcardDelete()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{   
            $resultArray = AdminGiftCards_model::giftcardDelete(explode(',', $_POST['idlist']));
            AdminGiftCards_view::giftcardDelete($resultArray);
        }
	}    


	static function giftcardCreateDisplay()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
            $resultArray = Array();
            
		    AdminGiftCards_model::buildEditLists($resultArray);
            AdminGiftCards_view::displayCreate($resultArray);
        }
	}    

    static function giftcardCreate()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
            $resultArray = AdminGiftCards_model::giftcardCreate();
            AdminGiftCards_view::giftcardCreate($resultArray);
        }
	}
	
	static function giftcardImportDisplay()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
            $resultArray = Array();
            
		    AdminGiftCards_model::buildEditLists($resultArray);
            AdminGiftCards_view::displayImport($resultArray);
        }
	}    
	
	static function giftcardImport()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
            $resultArray = AdminGiftCards_model::giftcardImport();
            AdminGiftCards_view::giftcardImport($resultArray);
        }
	}
	
	static function giftcardDeleteNew()
	{
	    global $gSession;
        
		if (AuthenticateObj::adminSessionActive() == 1)
		{            
            $resultArray = AdminGiftCards_model::giftcardDelete($gSession['giftcardcreationresult']);
            
            AdminGiftCards_model::clearSavedGiftcardList();
            
            AdminGiftCards_view::giftcardDelete($resultArray);
        }
	}
		
	static function giftcardReturnToList()
	{
	    if (AuthenticateObj::adminSessionActive() == 1)
	    {
            AdminGiftCards_model::clearSavedGiftcardList();
            self::displayList();
        }
	}

	static function giftcardExport()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{	
		    $resultArray = AdminGiftCards_model::giftcardExport();
            AdminGiftCards_view::giftcardExport($resultArray);
        }
	}

}

?>