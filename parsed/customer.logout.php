<?php

/* Customer.logout: <fusebox:set name="myFusebox['thisCircuit']" value="Customer"> */
$myFusebox['thisCircuit'] = "Customer";
/* Customer.logout: <fusebox:set name="myFusebox['thisFuseaction']" value="logout"> */
$myFusebox['thisFuseaction'] = "logout";
/* Customer.logout: <fusebox:instantiate object="control" class="Customer_control"> */
include_once($application["fusebox"]["WebRootToAppRootPath"]."Customer/Customer_control.php");
$control = new Customer_control;
/* Customer.logout: <fusebox:invoke object="control" methodcall="assertRequestMethod(['POST'])"> */
$control->assertRequestMethod(['POST']);
/* Customer.logout: <fusebox:invoke object="control" methodcall="assertCsrfToken()"> */
$control->assertCsrfToken();
/* Customer.logout: <fusebox:invoke object="control" methodcall="logout()">    */
$control->logout();
/* Customer.logout: <fusebox:set name="myFusebox['thisCircuit']" value="Customer"> */
$myFusebox['thisCircuit'] = "Customer";
/* Customer.logout: <fusebox:set name="myFusebox['thisFuseaction']" value="logout"> */
$myFusebox['thisFuseaction'] = "logout";
/* Customer.logout: <fusebox:set name="myFusebox['thisCircuit']" value="Customer"> */
$myFusebox['thisCircuit'] = "Customer";
/* Customer.logout: <fusebox:set name="myFusebox['thisFuseaction']" value="logout"> */
$myFusebox['thisFuseaction'] = "logout";

?>