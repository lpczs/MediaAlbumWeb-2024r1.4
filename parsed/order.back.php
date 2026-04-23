<?php

/* Order.back: <fusebox:set name="myFusebox['thisCircuit']" value="Order">     */
$myFusebox['thisCircuit'] = "Order";
/* Order.back: <fusebox:set name="myFusebox['thisFuseaction']" value="back">   */
$myFusebox['thisFuseaction'] = "back";
/* Order.back: <fusebox:instantiate object="control" class="Order_control">    */
include_once($application["fusebox"]["WebRootToAppRootPath"]."Order/Order_control.php");
$control = new Order_control;
/* Order.back: <fusebox:invoke object="control" methodcall="assertRequestMethod(['POST'])"> */
$control->assertRequestMethod(['POST']);
/* Order.back: <fusebox:invoke object="control" methodcall="assertCsrfToken()"> */
$control->assertCsrfToken();
/* Order.back: <fusebox:invoke object="control" methodcall="orderBack()">      */
$control->orderBack();
/* Order.back: <fusebox:set name="myFusebox['thisCircuit']" value="Order">     */
$myFusebox['thisCircuit'] = "Order";
/* Order.back: <fusebox:set name="myFusebox['thisFuseaction']" value="back">   */
$myFusebox['thisFuseaction'] = "back";
/* Order.back: <fusebox:set name="myFusebox['thisCircuit']" value="Order">     */
$myFusebox['thisCircuit'] = "Order";
/* Order.back: <fusebox:set name="myFusebox['thisFuseaction']" value="back">   */
$myFusebox['thisFuseaction'] = "back";

?>