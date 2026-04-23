<?php

/* AdminVouchers.add: <fusebox:set name="myFusebox['thisCircuit']" value="AdminVouchers"> */
$myFusebox['thisCircuit'] = "AdminVouchers";
/* AdminVouchers.add: <fusebox:set name="myFusebox['thisFuseaction']" value="add"> */
$myFusebox['thisFuseaction'] = "add";
/* AdminVouchers.add: <fusebox:instantiate object="control" class="AdminVouchers_control"> */
include_once($application["fusebox"]["WebRootToAppRootPath"]."AdminVouchers/AdminVouchers_control.php");
$control = new AdminVouchers_control;
/* AdminVouchers.add: <fusebox:invoke object="control" methodcall="assertRequestMethod(['POST'])"> */
$control->assertRequestMethod(['POST']);
/* AdminVouchers.add: <fusebox:invoke object="control" methodcall="assertCsrfToken()"> */
$control->assertCsrfToken();
/* AdminVouchers.add: <fusebox:invoke object="control" methodcall="voucherAdd()"> */
$control->voucherAdd();
/* AdminVouchers.add: <fusebox:set name="myFusebox['thisCircuit']" value="AdminVouchers"> */
$myFusebox['thisCircuit'] = "AdminVouchers";
/* AdminVouchers.add: <fusebox:set name="myFusebox['thisFuseaction']" value="add"> */
$myFusebox['thisFuseaction'] = "add";
/* AdminVouchers.add: <fusebox:set name="myFusebox['thisCircuit']" value="AdminVouchers"> */
$myFusebox['thisCircuit'] = "AdminVouchers";
/* AdminVouchers.add: <fusebox:set name="myFusebox['thisFuseaction']" value="add"> */
$myFusebox['thisFuseaction'] = "add";

?>