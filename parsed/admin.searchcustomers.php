<?php

/* Admin.searchCustomers: <fusebox:set name="myFusebox['thisCircuit']" value="Admin"> */
$myFusebox['thisCircuit'] = "Admin";
/* Admin.searchCustomers: <fusebox:set name="myFusebox['thisFuseaction']" value="searchCustomers"> */
$myFusebox['thisFuseaction'] = "searchCustomers";
/* Admin.searchCustomers: <fusebox:instantiate object="control" class="Admin_control"> */
include_once($application["fusebox"]["WebRootToAppRootPath"]."Admin/Admin_control.php");
$control = new Admin_control;
/* Admin.searchCustomers: <fusebox:invoke object="control" methodcall="assertRequestMethod(['POST'])"> */
$control->assertRequestMethod(['POST']);
/* Admin.searchCustomers: <fusebox:invoke object="control" methodcall="assertCsrfToken()"> */
$control->assertCsrfToken();
/* Admin.searchCustomers: <fusebox:invoke object="control" methodcall="searchCustomers()"> */
$control->searchCustomers();
/* Admin.searchCustomers: <fusebox:set name="myFusebox['thisCircuit']" value="Admin"> */
$myFusebox['thisCircuit'] = "Admin";
/* Admin.searchCustomers: <fusebox:set name="myFusebox['thisFuseaction']" value="searchCustomers"> */
$myFusebox['thisFuseaction'] = "searchCustomers";
/* Admin.searchCustomers: <fusebox:set name="myFusebox['thisCircuit']" value="Admin"> */
$myFusebox['thisCircuit'] = "Admin";
/* Admin.searchCustomers: <fusebox:set name="myFusebox['thisFuseaction']" value="searchCustomers"> */
$myFusebox['thisFuseaction'] = "searchCustomers";

?>