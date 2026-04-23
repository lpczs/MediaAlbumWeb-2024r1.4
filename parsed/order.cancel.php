<?php

/* Order.cancel: <fusebox:set name="myFusebox['thisCircuit']" value="Order">   */
$myFusebox['thisCircuit'] = "Order";
/* Order.cancel: <fusebox:set name="myFusebox['thisFuseaction']" value="cancel"> */
$myFusebox['thisFuseaction'] = "cancel";
/* Order.cancel: <fusebox:instantiate object="control" class="Order_control">  */
include_once($application["fusebox"]["WebRootToAppRootPath"]."Order/Order_control.php");
$control = new Order_control;
/* Order.cancel: <fusebox:invoke object="control" methodcall="assertRequestMethod(['POST'])"> */
$control->assertRequestMethod(['POST']);
/* Order.cancel: <fusebox:invoke object="control" methodcall="assertCsrfToken()"> */
$control->assertCsrfToken();
/* Order.cancel: <fusebox:invoke object="control" methodcall="cancel()">       */
$control->cancel();
/* Order.cancel: <fusebox:set name="myFusebox['thisCircuit']" value="Order">   */
$myFusebox['thisCircuit'] = "Order";
/* Order.cancel: <fusebox:set name="myFusebox['thisFuseaction']" value="cancel"> */
$myFusebox['thisFuseaction'] = "cancel";
/* Order.cancel: <fusebox:set name="myFusebox['thisCircuit']" value="Order">   */
$myFusebox['thisCircuit'] = "Order";
/* Order.cancel: <fusebox:set name="myFusebox['thisFuseaction']" value="cancel"> */
$myFusebox['thisFuseaction'] = "cancel";

?>