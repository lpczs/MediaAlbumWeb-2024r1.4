<?php

/* AdminCustomers.add: <fusebox:set name="myFusebox['thisCircuit']" value="AdminCustomers"> */
$myFusebox['thisCircuit'] = "AdminCustomers";
/* AdminCustomers.add: <fusebox:set name="myFusebox['thisFuseaction']" value="add"> */
$myFusebox['thisFuseaction'] = "add";
/* AdminCustomers.add: <fusebox:instantiate object="control" class="AdminCustomers_control"> */
include_once($application["fusebox"]["WebRootToAppRootPath"]."AdminCustomers/AdminCustomers_control.php");
$control = new AdminCustomers_control;
/* AdminCustomers.add: <fusebox:invoke object="control" methodcall="assertRequestMethod(['POST'])"> */
$control->assertRequestMethod(['POST']);
/* AdminCustomers.add: <fusebox:invoke object="control" methodcall="assertCsrfToken()"> */
$control->assertCsrfToken();
/* AdminCustomers.add: <fusebox:invoke object="control" methodcall="customerAdd()"> */
$control->customerAdd();
/* AdminCustomers.add: <fusebox:set name="myFusebox['thisCircuit']" value="AdminCustomers"> */
$myFusebox['thisCircuit'] = "AdminCustomers";
/* AdminCustomers.add: <fusebox:set name="myFusebox['thisFuseaction']" value="add"> */
$myFusebox['thisFuseaction'] = "add";
/* AdminCustomers.add: <fusebox:set name="myFusebox['thisCircuit']" value="AdminCustomers"> */
$myFusebox['thisCircuit'] = "AdminCustomers";
/* AdminCustomers.add: <fusebox:set name="myFusebox['thisFuseaction']" value="add"> */
$myFusebox['thisFuseaction'] = "add";

?>