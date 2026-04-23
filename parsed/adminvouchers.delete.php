<?php

/* AdminVouchers.delete: <fusebox:set name="myFusebox['thisCircuit']" value="AdminVouchers"> */
$myFusebox['thisCircuit'] = "AdminVouchers";
/* AdminVouchers.delete: <fusebox:set name="myFusebox['thisFuseaction']" value="delete"> */
$myFusebox['thisFuseaction'] = "delete";
/* AdminVouchers.delete: <fusebox:instantiate object="control" class="AdminVouchers_control"> */
include_once($application["fusebox"]["WebRootToAppRootPath"]."AdminVouchers/AdminVouchers_control.php");
$control = new AdminVouchers_control;
/* AdminVouchers.delete: <fusebox:invoke object="control" methodcall="assertRequestMethod(['POST'])"> */
$control->assertRequestMethod(['POST']);
/* AdminVouchers.delete: <fusebox:invoke object="control" methodcall="assertCsrfToken()"> */
$control->assertCsrfToken();
/* AdminVouchers.delete: <fusebox:invoke object="control" methodcall="voucherDelete()"> */
$control->voucherDelete();
/* AdminVouchers.delete: <fusebox:set name="myFusebox['thisCircuit']" value="AdminVouchers"> */
$myFusebox['thisCircuit'] = "AdminVouchers";
/* AdminVouchers.delete: <fusebox:set name="myFusebox['thisFuseaction']" value="delete"> */
$myFusebox['thisFuseaction'] = "delete";
/* AdminVouchers.delete: <fusebox:set name="myFusebox['thisCircuit']" value="AdminVouchers"> */
$myFusebox['thisCircuit'] = "AdminVouchers";
/* AdminVouchers.delete: <fusebox:set name="myFusebox['thisFuseaction']" value="delete"> */
$myFusebox['thisFuseaction'] = "delete";

?>