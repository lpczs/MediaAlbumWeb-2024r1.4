<?php

/* Customer.updatePassword: <fusebox:set name="myFusebox['thisCircuit']" value="Customer"> */
$myFusebox['thisCircuit'] = "Customer";
/* Customer.updatePassword: <fusebox:set name="myFusebox['thisFuseaction']" value="updatePassword"> */
$myFusebox['thisFuseaction'] = "updatePassword";
/* Customer.updatePassword: <fusebox:instantiate object="control" class="Customer_control"> */
include_once($application["fusebox"]["WebRootToAppRootPath"]."Customer/Customer_control.php");
$control = new Customer_control;
/* Customer.updatePassword: <fusebox:invoke object="control" methodcall="assertRequestMethod(['POST'])"> */
$control->assertRequestMethod(['POST']);
/* Customer.updatePassword: <fusebox:invoke object="control" methodcall="assertCsrfToken()"> */
$control->assertCsrfToken();
/* Customer.updatePassword: <fusebox:invoke object="control" methodcall="updatePassword()"> */
$control->updatePassword();
/* Customer.updatePassword: <fusebox:set name="myFusebox['thisCircuit']" value="Customer"> */
$myFusebox['thisCircuit'] = "Customer";
/* Customer.updatePassword: <fusebox:set name="myFusebox['thisFuseaction']" value="updatePassword"> */
$myFusebox['thisFuseaction'] = "updatePassword";
/* Customer.updatePassword: <fusebox:set name="myFusebox['thisCircuit']" value="Customer"> */
$myFusebox['thisCircuit'] = "Customer";
/* Customer.updatePassword: <fusebox:set name="myFusebox['thisFuseaction']" value="updatePassword"> */
$myFusebox['thisFuseaction'] = "updatePassword";

?>