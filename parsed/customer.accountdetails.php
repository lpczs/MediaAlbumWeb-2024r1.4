<?php

/* Customer.accountDetails: <fusebox:set name="myFusebox['thisCircuit']" value="Customer"> */
$myFusebox['thisCircuit'] = "Customer";
/* Customer.accountDetails: <fusebox:set name="myFusebox['thisFuseaction']" value="accountDetails"> */
$myFusebox['thisFuseaction'] = "accountDetails";
/* Customer.accountDetails: <fusebox:instantiate object="control" class="Customer_control"> */
include_once($application["fusebox"]["WebRootToAppRootPath"]."Customer/Customer_control.php");
$control = new Customer_control;
/* Customer.accountDetails: <fusebox:invoke object="control" methodcall="assertRequestMethod(['POST'])"> */
$control->assertRequestMethod(['POST']);
/* Customer.accountDetails: <fusebox:invoke object="control" methodcall="assertCsrfToken()"> */
$control->assertCsrfToken();
/* Customer.accountDetails: <fusebox:invoke object="control" methodcall="accountDetails()"> */
$control->accountDetails();
/* Customer.accountDetails: <fusebox:set name="myFusebox['thisCircuit']" value="Customer"> */
$myFusebox['thisCircuit'] = "Customer";
/* Customer.accountDetails: <fusebox:set name="myFusebox['thisFuseaction']" value="accountDetails"> */
$myFusebox['thisFuseaction'] = "accountDetails";
/* Customer.accountDetails: <fusebox:set name="myFusebox['thisCircuit']" value="Customer"> */
$myFusebox['thisCircuit'] = "Customer";
/* Customer.accountDetails: <fusebox:set name="myFusebox['thisFuseaction']" value="accountDetails"> */
$myFusebox['thisFuseaction'] = "accountDetails";

?>