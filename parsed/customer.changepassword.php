<?php

/* Customer.changePassword: <fusebox:set name="myFusebox['thisCircuit']" value="Customer"> */
$myFusebox['thisCircuit'] = "Customer";
/* Customer.changePassword: <fusebox:set name="myFusebox['thisFuseaction']" value="changePassword"> */
$myFusebox['thisFuseaction'] = "changePassword";
/* Customer.changePassword: <fusebox:instantiate object="control" class="Customer_control"> */
include_once($application["fusebox"]["WebRootToAppRootPath"]."Customer/Customer_control.php");
$control = new Customer_control;
/* Customer.changePassword: <fusebox:invoke object="control" methodcall="assertRequestMethod(['POST'])"> */
$control->assertRequestMethod(['POST']);
/* Customer.changePassword: <fusebox:invoke object="control" methodcall="assertCsrfToken()"> */
$control->assertCsrfToken();
/* Customer.changePassword: <fusebox:invoke object="control" methodcall="changePassword()"> */
$control->changePassword();
/* Customer.changePassword: <fusebox:set name="myFusebox['thisCircuit']" value="Customer"> */
$myFusebox['thisCircuit'] = "Customer";
/* Customer.changePassword: <fusebox:set name="myFusebox['thisFuseaction']" value="changePassword"> */
$myFusebox['thisFuseaction'] = "changePassword";
/* Customer.changePassword: <fusebox:set name="myFusebox['thisCircuit']" value="Customer"> */
$myFusebox['thisCircuit'] = "Customer";
/* Customer.changePassword: <fusebox:set name="myFusebox['thisFuseaction']" value="changePassword"> */
$myFusebox['thisFuseaction'] = "changePassword";

?>