<?php
//get the location of the this file
$pathOfScheduler = __FILE__;

//strip the filename from the directory path
$pos = strrpos($pathOfScheduler, "\\");
$directory = substr($pathOfScheduler, 0, $pos);

//Then change directory to the tasks directory
chdir($directory);
$root =  getcwd() . '/..';

if (file_exists($root . '/Order/PaymentIntegration/DirecPay.php'))
{
	require_once('../Order/PaymentIntegration/DirecPay.php');
	$paymentInquiry = DirecPayObj::paymentInquiry();
}
?>