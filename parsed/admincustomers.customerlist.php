<?php

/* AdminCustomers.customerList: <fusebox:set name="myFusebox['thisCircuit']" value="AdminCustomers"> */
$myFusebox['thisCircuit'] = "AdminCustomers";
/* AdminCustomers.customerList: <fusebox:set name="myFusebox['thisFuseaction']" value="customerList"> */
$myFusebox['thisFuseaction'] = "customerList";
/* AdminCustomers.customerList: <fusebox:instantiate object="control" class="AdminCustomers_control"> */
include_once($application["fusebox"]["WebRootToAppRootPath"]."AdminCustomers/AdminCustomers_control.php");
$control = new AdminCustomers_control;
/* AdminCustomers.customerList: <fusebox:invoke object="control" methodcall="assertRequestMethod(['POST'])"> */
$control->assertRequestMethod(['POST']);
/* AdminCustomers.customerList: <fusebox:invoke object="control" methodcall="assertCsrfToken()"> */
$control->assertCsrfToken();
/* AdminCustomers.customerList: <fusebox:invoke object="control" methodcall="customerList()"> */
$control->customerList();
/* AdminCustomers.customerList: <fusebox:set name="myFusebox['thisCircuit']" value="AdminCustomers"> */
$myFusebox['thisCircuit'] = "AdminCustomers";
/* AdminCustomers.customerList: <fusebox:set name="myFusebox['thisFuseaction']" value="customerList"> */
$myFusebox['thisFuseaction'] = "customerList";
/* AdminCustomers.customerList: <fusebox:set name="myFusebox['thisCircuit']" value="AdminCustomers"> */
$myFusebox['thisCircuit'] = "AdminCustomers";
/* AdminCustomers.customerList: <fusebox:set name="myFusebox['thisFuseaction']" value="customerList"> */
$myFusebox['thisFuseaction'] = "customerList";

?>