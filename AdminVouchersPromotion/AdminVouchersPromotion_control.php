<?php

require_once('../Utils/UtilsAuthenticate.php');
require_once('../AdminVouchers/AdminVouchers_view.php');
require_once('../AdminVouchersPromotion/AdminVouchersPromotion_model.php');
require_once('../AdminVouchersPromotion/AdminVouchersPromotion_view.php');

use Security\RequestValidationTrait;

class AdminVouchersPromotion_control
{
	use RequestValidationTrait;

	static function initialize()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
			AdminVouchersPromotion_view::displayList();
		}
	} 
	
	static function listPromotions()
	{ 
		if (AuthenticateObj::adminSessionActive() == 1)
		{
		    AdminVouchersPromotion_model::listPromotions();
		}
	}

	static function promotionActivate()
	{
        if (AuthenticateObj::adminSessionActive() == 1)
        {
            AdminVouchersPromotion_model::promotionActivate();
        }
	}    
	
	static function promotionAddDisplay()
	{
        if (AuthenticateObj::adminSessionActive() == 1)
        {
            $resultArray = AdminVouchersPromotion_model::displayAdd();
            AdminVouchersPromotion_view::displayAdd($resultArray);
        }
	}    

	static function promotionAdd()
	{
        if (AuthenticateObj::adminSessionActive() == 1)
        {
            $promotionArray = AdminVouchersPromotion_model::promotionAdd();
        }
	}

	static function promotionEditDisplay()
	{
        if (AuthenticateObj::adminSessionActive() == 1)
        {
            $promotionID = $_GET['id'];
            if ($promotionID)
            {
                $resultArray = AdminVouchersPromotion_model::displayEdit($promotionID);
                AdminVouchersPromotion_view::displayEdit($resultArray);
            }
        }		
	}    

	static function promotionEdit()
	{
        if (AuthenticateObj::adminSessionActive() == 1)
        {
            $promotionArray = AdminVouchersPromotion_model::promotionEdit();
        }
	}

	static function promotionDelete()
	{
        if (AuthenticateObj::adminSessionActive() == 1)
        {
            $deleteResultArray = AdminVouchersPromotion_model::promotionDelete();
        }
	}
}

?>