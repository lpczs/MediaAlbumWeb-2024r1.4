<?php

/* Order.refresh: <fusebox:set name="myFusebox['thisCircuit']" value="Order">  */
$myFusebox['thisCircuit'] = "Order";
/* Order.refresh: <fusebox:set name="myFusebox['thisFuseaction']" value="refresh"> */
$myFusebox['thisFuseaction'] = "refresh";
/* Order.refresh: <fusebox:instantiate object="control" class="Order_control"> */
include_once($application["fusebox"]["WebRootToAppRootPath"]."Order/Order_control.php");
$control = new Order_control;
/* Order.refresh: <fusebox:invoke object="control" methodcall="assertRequestMethod(['POST'])"> */
$control->assertRequestMethod(['POST']);
/* Order.refresh: <fusebox:invoke object="control" methodcall="assertCsrfToken()"> */
$control->assertCsrfToken();
/* Order.refresh: <fusebox:invoke object="control" methodcall="refresh()">     */
$control->refresh();
/* Order.refresh: <fusebox:set name="myFusebox['thisCircuit']" value="Order">  */
$myFusebox['thisCircuit'] = "Order";
/* Order.refresh: <fusebox:set name="myFusebox['thisFuseaction']" value="refresh"> */
$myFusebox['thisFuseaction'] = "refresh";
/* Order.refresh: <fusebox:set name="myFusebox['thisCircuit']" value="Order">  */
$myFusebox['thisCircuit'] = "Order";
/* Order.refresh: <fusebox:set name="myFusebox['thisFuseaction']" value="refresh"> */
$myFusebox['thisFuseaction'] = "refresh";

?>