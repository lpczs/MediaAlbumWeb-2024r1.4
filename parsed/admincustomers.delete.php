<?php

/* AdminCustomers.delete: <fusebox:set name="myFusebox['thisCircuit']" value="AdminCustomers"> */
$myFusebox['thisCircuit'] = "AdminCustomers";
/* AdminCustomers.delete: <fusebox:set name="myFusebox['thisFuseaction']" value="delete"> */
$myFusebox['thisFuseaction'] = "delete";
/* AdminCustomers.delete: <fusebox:instantiate object="control" class="AdminCustomers_control"> */
include_once($application["fusebox"]["WebRootToAppRootPath"]."AdminCustomers/AdminCustomers_control.php");
$control = new AdminCustomers_control;
/* AdminCustomers.delete: <fusebox:invoke object="control" methodcall="assertRequestMethod(['POST'])"> */
$control->assertRequestMethod(['POST']);
/* AdminCustomers.delete: <fusebox:invoke object="control" methodcall="assertCsrfToken()"> */
$control->assertCsrfToken();
/* AdminCustomers.delete: <fusebox:invoke object="control" methodcall="customerDelete()"> */
$control->customerDelete();
/* AdminCustomers.delete: <fusebox:set name="myFusebox['thisCircuit']" value="AdminCustomers"> */
$myFusebox['thisCircuit'] = "AdminCustomers";
/* AdminCustomers.delete: <fusebox:set name="myFusebox['thisFuseaction']" value="delete"> */
$myFusebox['thisFuseaction'] = "delete";
/* AdminCustomers.delete: <fusebox:set name="myFusebox['thisCircuit']" value="AdminCustomers"> */
$myFusebox['thisCircuit'] = "AdminCustomers";
/* AdminCustomers.delete: <fusebox:set name="myFusebox['thisFuseaction']" value="delete"> */
$myFusebox['thisFuseaction'] = "delete";

?>