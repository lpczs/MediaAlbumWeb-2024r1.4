<?php
//get the location of the taskscheduler.php file
$pathOfScheduler =  __FILE__;

//strip the tasksceduler.php from the directory path
$pos = strrpos($pathOfScheduler, "\\");
$directoy = substr($pathOfScheduler, 0, $pos);

//Then change directory to the tasks directory
chdir($directoy);
$root =  getcwd() . '/..';

if (file_exists($root . '/Order/PaymentIntegration/Paygent.php'))
{
	require_once('../Order/PaymentIntegration/Paygent.php');
	$paymentInquiry = PaygentObj::paymentInquiry();
}

?>