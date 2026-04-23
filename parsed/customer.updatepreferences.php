<?php

/* Customer.updatePreferences: <fusebox:set name="myFusebox['thisCircuit']" value="Customer"> */
$myFusebox['thisCircuit'] = "Customer";
/* Customer.updatePreferences: <fusebox:set name="myFusebox['thisFuseaction']" value="updatePreferences"> */
$myFusebox['thisFuseaction'] = "updatePreferences";
/* Customer.updatePreferences: <fusebox:instantiate object="control" class="Customer_control"> */
include_once($application["fusebox"]["WebRootToAppRootPath"]."Customer/Customer_control.php");
$control = new Customer_control;
/* Customer.updatePreferences: <fusebox:invoke object="control" methodcall="assertRequestMethod(['POST'])"> */
$control->assertRequestMethod(['POST']);
/* Customer.updatePreferences: <fusebox:invoke object="control" methodcall="assertCsrfToken()"> */
$control->assertCsrfToken();
/* Customer.updatePreferences: <fusebox:invoke object="control" methodcall="updatePreferences()"> */
$control->updatePreferences();
/* Customer.updatePreferences: <fusebox:set name="myFusebox['thisCircuit']" value="Customer"> */
$myFusebox['thisCircuit'] = "Customer";
/* Customer.updatePreferences: <fusebox:set name="myFusebox['thisFuseaction']" value="updatePreferences"> */
$myFusebox['thisFuseaction'] = "updatePreferences";
/* Customer.updatePreferences: <fusebox:set name="myFusebox['thisCircuit']" value="Customer"> */
$myFusebox['thisCircuit'] = "Customer";
/* Customer.updatePreferences: <fusebox:set name="myFusebox['thisFuseaction']" value="updatePreferences"> */
$myFusebox['thisFuseaction'] = "updatePreferences";

?>