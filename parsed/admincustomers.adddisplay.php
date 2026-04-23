<?php

/* AdminCustomers.addDisplay: <fusebox:set name="myFusebox['thisCircuit']" value="AdminCustomers"> */
$myFusebox['thisCircuit'] = "AdminCustomers";
/* AdminCustomers.addDisplay: <fusebox:set name="myFusebox['thisFuseaction']" value="addDisplay"> */
$myFusebox['thisFuseaction'] = "addDisplay";
/* AdminCustomers.addDisplay: <fusebox:instantiate object="control" class="AdminCustomers_control"> */
include_once($application["fusebox"]["WebRootToAppRootPath"]."AdminCustomers/AdminCustomers_control.php");
$control = new AdminCustomers_control;
/* AdminCustomers.addDisplay: <fusebox:invoke object="control" methodcall="assertRequestMethod(['GET'])"> */
$control->assertRequestMethod(['GET']);
/* AdminCustomers.addDisplay: <fusebox:invoke object="control" methodcall="customerAddDisplay()"> */
$control->customerAddDisplay();
/* AdminCustomers.addDisplay: <fusebox:set name="myFusebox['thisCircuit']" value="AdminCustomers"> */
$myFusebox['thisCircuit'] = "AdminCustomers";
/* AdminCustomers.addDisplay: <fusebox:set name="myFusebox['thisFuseaction']" value="addDisplay"> */
$myFusebox['thisFuseaction'] = "addDisplay";
/* AdminCustomers.addDisplay: <fusebox:set name="myFusebox['thisCircuit']" value="AdminCustomers"> */
$myFusebox['thisCircuit'] = "AdminCustomers";
/* AdminCustomers.addDisplay: <fusebox:set name="myFusebox['thisFuseaction']" value="addDisplay"> */
$myFusebox['thisFuseaction'] = "addDisplay";

?>