<?php

require_once('../Utils/UtilsAddress.php');
require_once('../Utils/UtilsAuthenticate.php');
require_once('../Utils/UtilsDatabase.php');
require_once('../CollectFromStore/CollectFromStore_model.php');
require_once('../CollectFromStore/CollectFromStore_view.php');

use Security\RequestValidationTrait;

class CollectFromStore_control 
{
	use RequestValidationTrait;

	static function initialize() 
	{ 
		if (AuthenticateObj::adminSessionActive() == 1)	
		{ 
			CollectFromStore_view::initialize(); 
		} 
	} 	
	
	static function listOrders()
	{
		if (AuthenticateObj::adminSessionActive() == 1)	
		{
            $resultArray = CollectFromStore_model::listOrders();
            CollectFromStore_view::listOrders($resultArray); 
		}   
	}
	
	static function callback()
	{   
	    if (AuthenticateObj::adminSessionActive() == 1)	
		{
            if (array_key_exists('cmd', $_REQUEST)) 
	    	{
	        	switch ($_REQUEST['cmd'])
	        	{
	            	case 'BOOKEDIN':
	                	CollectFromStore_model::markBooked();
	                	break;
	            	case 'SHIPPED':
	                	CollectFromStore_model::markShipped();
	                	break;    
	            	case 'COLLECTED':
	            		CollectFromStore_model::markCollected();
	                	break;
	            	default:
                    	CollectFromStore_view::unknownCommand();
                    	break;
	        	}
	    	}
		}
	}
}
?>