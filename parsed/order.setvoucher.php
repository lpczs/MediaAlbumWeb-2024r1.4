<?php

/* Order.setVoucher: <fusebox:set name="myFusebox['thisCircuit']" value="Order"> */
$myFusebox['thisCircuit'] = "Order";
/* Order.setVoucher: <fusebox:set name="myFusebox['thisFuseaction']" value="setVoucher"> */
$myFusebox['thisFuseaction'] = "setVoucher";
/* Order.setVoucher: <fusebox:instantiate object="control" class="Order_control"> */
include_once($application["fusebox"]["WebRootToAppRootPath"]."Order/Order_control.php");
$control = new Order_control;
/* Order.setVoucher: <fusebox:invoke object="control" methodcall="assertRequestMethod(['POST'])"> */
$control->assertRequestMethod(['POST']);
/* Order.setVoucher: <fusebox:invoke object="control" methodcall="assertCsrfToken()"> */
$control->assertCsrfToken();
/* Order.setVoucher: <fusebox:invoke object="control" methodcall="setVoucher()"> */
$control->setVoucher();
/* Order.setVoucher: <fusebox:set name="myFusebox['thisCircuit']" value="Order"> */
$myFusebox['thisCircuit'] = "Order";
/* Order.setVoucher: <fusebox:set name="myFusebox['thisFuseaction']" value="setVoucher"> */
$myFusebox['thisFuseaction'] = "setVoucher";
/* Order.setVoucher: <fusebox:set name="myFusebox['thisCircuit']" value="Order"> */
$myFusebox['thisCircuit'] = "Order";
/* Order.setVoucher: <fusebox:set name="myFusebox['thisFuseaction']" value="setVoucher"> */
$myFusebox['thisFuseaction'] = "setVoucher";

?>