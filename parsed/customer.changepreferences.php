<?php

/* Customer.changePreferences: <fusebox:set name="myFusebox['thisCircuit']" value="Customer"> */
$myFusebox['thisCircuit'] = "Customer";
/* Customer.changePreferences: <fusebox:set name="myFusebox['thisFuseaction']" value="changePreferences"> */
$myFusebox['thisFuseaction'] = "changePreferences";
/* Customer.changePreferences: <fusebox:instantiate object="control" class="Customer_control"> */
include_once($application["fusebox"]["WebRootToAppRootPath"]."Customer/Customer_control.php");
$control = new Customer_control;
/* Customer.changePreferences: <fusebox:invoke object="control" methodcall="assertRequestMethod(['POST'])"> */
$control->assertRequestMethod(['POST']);
/* Customer.changePreferences: <fusebox:invoke object="control" methodcall="assertCsrfToken()"> */
$control->assertCsrfToken();
/* Customer.changePreferences: <fusebox:invoke object="control" methodcall="changePreferences()"> */
$control->changePreferences();
/* Customer.changePreferences: <fusebox:set name="myFusebox['thisCircuit']" value="Customer"> */
$myFusebox['thisCircuit'] = "Customer";
/* Customer.changePreferences: <fusebox:set name="myFusebox['thisFuseaction']" value="changePreferences"> */
$myFusebox['thisFuseaction'] = "changePreferences";
/* Customer.changePreferences: <fusebox:set name="myFusebox['thisCircuit']" value="Customer"> */
$myFusebox['thisCircuit'] = "Customer";
/* Customer.changePreferences: <fusebox:set name="myFusebox['thisFuseaction']" value="changePreferences"> */
$myFusebox['thisFuseaction'] = "changePreferences";

?>