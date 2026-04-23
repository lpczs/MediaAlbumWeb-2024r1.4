<?php

/* AdminVouchers.addDisplay: <fusebox:set name="myFusebox['thisCircuit']" value="AdminVouchers"> */
$myFusebox['thisCircuit'] = "AdminVouchers";
/* AdminVouchers.addDisplay: <fusebox:set name="myFusebox['thisFuseaction']" value="addDisplay"> */
$myFusebox['thisFuseaction'] = "addDisplay";
/* AdminVouchers.addDisplay: <fusebox:instantiate object="control" class="AdminVouchers_control"> */
include_once($application["fusebox"]["WebRootToAppRootPath"]."AdminVouchers/AdminVouchers_control.php");
$control = new AdminVouchers_control;
/* AdminVouchers.addDisplay: <fusebox:invoke object="control" methodcall="assertRequestMethod(['GET'])"> */
$control->assertRequestMethod(['GET']);
/* AdminVouchers.addDisplay: <fusebox:invoke object="control" methodcall="voucherAddDisplay()"> */
$control->voucherAddDisplay();
/* AdminVouchers.addDisplay: <fusebox:set name="myFusebox['thisCircuit']" value="AdminVouchers"> */
$myFusebox['thisCircuit'] = "AdminVouchers";
/* AdminVouchers.addDisplay: <fusebox:set name="myFusebox['thisFuseaction']" value="addDisplay"> */
$myFusebox['thisFuseaction'] = "addDisplay";
/* AdminVouchers.addDisplay: <fusebox:set name="myFusebox['thisCircuit']" value="AdminVouchers"> */
$myFusebox['thisCircuit'] = "AdminVouchers";
/* AdminVouchers.addDisplay: <fusebox:set name="myFusebox['thisFuseaction']" value="addDisplay"> */
$myFusebox['thisFuseaction'] = "addDisplay";

?>