<?php

/* Customer.initialize: <fusebox:set name="myFusebox['thisCircuit']" value="Customer"> */
$myFusebox['thisCircuit'] = "Customer";
/* Customer.initialize: <fusebox:set name="myFusebox['thisFuseaction']" value="initialize"> */
$myFusebox['thisFuseaction'] = "initialize";
/* Customer.initialize: <fusebox:instantiate object="control" class="Customer_control"> */
include_once($application["fusebox"]["WebRootToAppRootPath"]."Customer/Customer_control.php");
$control = new Customer_control;
/* Customer.initialize: <fusebox:invoke object="control" methodcall="assertRequestMethod(['POST'])"> */
$control->assertRequestMethod(['POST']);
/* Customer.initialize: <fusebox:invoke object="control" methodcall="assertCsrfToken()"> */
$control->assertCsrfToken();
/* Customer.initialize: <fusebox:invoke object="control" methodcall="initialize()"> */
$control->initialize();
/* Customer.initialize: <fusebox:set name="myFusebox['thisCircuit']" value="Customer"> */
$myFusebox['thisCircuit'] = "Customer";
/* Customer.initialize: <fusebox:set name="myFusebox['thisFuseaction']" value="initialize"> */
$myFusebox['thisFuseaction'] = "initialize";
/* Customer.initialize: <fusebox:set name="myFusebox['thisCircuit']" value="Customer"> */
$myFusebox['thisCircuit'] = "Customer";
/* Customer.initialize: <fusebox:set name="myFusebox['thisFuseaction']" value="initialize"> */
$myFusebox['thisFuseaction'] = "initialize";

?>