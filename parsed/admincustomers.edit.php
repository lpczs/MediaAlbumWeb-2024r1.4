<?php

/* AdminCustomers.edit: <fusebox:set name="myFusebox['thisCircuit']" value="AdminCustomers"> */
$myFusebox['thisCircuit'] = "AdminCustomers";
/* AdminCustomers.edit: <fusebox:set name="myFusebox['thisFuseaction']" value="edit"> */
$myFusebox['thisFuseaction'] = "edit";
/* AdminCustomers.edit: <fusebox:instantiate object="control" class="AdminCustomers_control"> */
include_once($application["fusebox"]["WebRootToAppRootPath"]."AdminCustomers/AdminCustomers_control.php");
$control = new AdminCustomers_control;
/* AdminCustomers.edit: <fusebox:invoke object="control" methodcall="assertRequestMethod(['POST'])"> */
$control->assertRequestMethod(['POST']);
/* AdminCustomers.edit: <fusebox:invoke object="control" methodcall="assertCsrfToken()"> */
$control->assertCsrfToken();
/* AdminCustomers.edit: <fusebox:invoke object="control" methodcall="customerEdit()"> */
$control->customerEdit();
/* AdminCustomers.edit: <fusebox:set name="myFusebox['thisCircuit']" value="AdminCustomers"> */
$myFusebox['thisCircuit'] = "AdminCustomers";
/* AdminCustomers.edit: <fusebox:set name="myFusebox['thisFuseaction']" value="edit"> */
$myFusebox['thisFuseaction'] = "edit";
/* AdminCustomers.edit: <fusebox:set name="myFusebox['thisCircuit']" value="AdminCustomers"> */
$myFusebox['thisCircuit'] = "AdminCustomers";
/* AdminCustomers.edit: <fusebox:set name="myFusebox['thisFuseaction']" value="edit"> */
$myFusebox['thisFuseaction'] = "edit";

?>