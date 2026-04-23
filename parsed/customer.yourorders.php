<?php

/* Customer.yourOrders: <fusebox:set name="myFusebox['thisCircuit']" value="Customer"> */
$myFusebox['thisCircuit'] = "Customer";
/* Customer.yourOrders: <fusebox:set name="myFusebox['thisFuseaction']" value="yourOrders"> */
$myFusebox['thisFuseaction'] = "yourOrders";
/* Customer.yourOrders: <fusebox:instantiate object="control" class="Customer_control"> */
include_once($application["fusebox"]["WebRootToAppRootPath"]."Customer/Customer_control.php");
$control = new Customer_control;
/* Customer.yourOrders: <fusebox:invoke object="control" methodcall="assertRequestMethod(['POST'])"> */
$control->assertRequestMethod(['POST']);
/* Customer.yourOrders: <fusebox:invoke object="control" methodcall="assertCsrfToken()"> */
$control->assertCsrfToken();
/* Customer.yourOrders: <fusebox:invoke object="control" methodcall="yourOrders()"> */
$control->yourOrders();
/* Customer.yourOrders: <fusebox:set name="myFusebox['thisCircuit']" value="Customer"> */
$myFusebox['thisCircuit'] = "Customer";
/* Customer.yourOrders: <fusebox:set name="myFusebox['thisFuseaction']" value="yourOrders"> */
$myFusebox['thisFuseaction'] = "yourOrders";
/* Customer.yourOrders: <fusebox:set name="myFusebox['thisCircuit']" value="Customer"> */
$myFusebox['thisCircuit'] = "Customer";
/* Customer.yourOrders: <fusebox:set name="myFusebox['thisFuseaction']" value="yourOrders"> */
$myFusebox['thisFuseaction'] = "yourOrders";

?>