<?php

/* Customer.updateAccountDetails: <fusebox:set name="myFusebox['thisCircuit']" value="Customer"> */
$myFusebox['thisCircuit'] = "Customer";
/* Customer.updateAccountDetails: <fusebox:set name="myFusebox['thisFuseaction']" value="updateAccountDetails"> */
$myFusebox['thisFuseaction'] = "updateAccountDetails";
/* Customer.updateAccountDetails: <fusebox:instantiate object="control" class="Customer_control"> */
include_once($application["fusebox"]["WebRootToAppRootPath"]."Customer/Customer_control.php");
$control = new Customer_control;
/* Customer.updateAccountDetails: <fusebox:invoke object="control" methodcall="assertRequestMethod(['POST'])"> */
$control->assertRequestMethod(['POST']);
/* Customer.updateAccountDetails: <fusebox:invoke object="control" methodcall="assertCsrfToken()"> */
$control->assertCsrfToken();
/* Customer.updateAccountDetails: <fusebox:invoke object="control" methodcall="updateAccountDetails()"> */
$control->updateAccountDetails();
/* Customer.updateAccountDetails: <fusebox:set name="myFusebox['thisCircuit']" value="Customer"> */
$myFusebox['thisCircuit'] = "Customer";
/* Customer.updateAccountDetails: <fusebox:set name="myFusebox['thisFuseaction']" value="updateAccountDetails"> */
$myFusebox['thisFuseaction'] = "updateAccountDetails";
/* Customer.updateAccountDetails: <fusebox:set name="myFusebox['thisCircuit']" value="Customer"> */
$myFusebox['thisCircuit'] = "Customer";
/* Customer.updateAccountDetails: <fusebox:set name="myFusebox['thisFuseaction']" value="updateAccountDetails"> */
$myFusebox['thisFuseaction'] = "updateAccountDetails";

?>