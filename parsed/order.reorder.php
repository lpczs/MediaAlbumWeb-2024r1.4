<?php

/* Order.reorder: <fusebox:set name="myFusebox['thisCircuit']" value="Order">  */
$myFusebox['thisCircuit'] = "Order";
/* Order.reorder: <fusebox:set name="myFusebox['thisFuseaction']" value="reorder"> */
$myFusebox['thisFuseaction'] = "reorder";
/* Order.reorder: <fusebox:instantiate object="control" class="Order_control"> */
include_once($application["fusebox"]["WebRootToAppRootPath"]."Order/Order_control.php");
$control = new Order_control;
/* Order.reorder: <fusebox:invoke object="control" methodcall="assertRequestMethod(['GET'])"> */
$control->assertRequestMethod(['GET']);
/* Order.reorder: <fusebox:invoke object="control" methodcall="reorder()">     */
$control->reorder();
/* Order.reorder: <fusebox:set name="myFusebox['thisCircuit']" value="Order">  */
$myFusebox['thisCircuit'] = "Order";
/* Order.reorder: <fusebox:set name="myFusebox['thisFuseaction']" value="reorder"> */
$myFusebox['thisFuseaction'] = "reorder";
/* Order.reorder: <fusebox:set name="myFusebox['thisCircuit']" value="Order">  */
$myFusebox['thisCircuit'] = "Order";
/* Order.reorder: <fusebox:set name="myFusebox['thisFuseaction']" value="reorder"> */
$myFusebox['thisFuseaction'] = "reorder";

?>