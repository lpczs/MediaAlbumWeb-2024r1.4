<?php

require_once('../Utils/UtilsAuthenticate.php');
require_once('../AdminVouchers/AdminVouchers_control.php');
require_once('../AdminVouchersSingle/AdminVouchersSingle_model.php');
require_once('../AdminVouchersSingle/AdminVouchersSingle_view.php');

use Security\RequestValidationTrait;

class AdminVouchersSingle_control
{
	use RequestValidationTrait;

    static function voucherDeleteExpired()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
            AdminVouchersSingle_model::voucherDeleteExpired();
            //AdminVouchers_control::displayList();
        }
	}
	
}

?>