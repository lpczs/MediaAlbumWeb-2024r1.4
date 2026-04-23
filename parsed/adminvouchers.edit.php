<?php

/* AdminVouchers.edit: <fusebox:set name="myFusebox['thisCircuit']" value="AdminVouchers"> */
$myFusebox['thisCircuit'] = "AdminVouchers";
/* AdminVouchers.edit: <fusebox:set name="myFusebox['thisFuseaction']" value="edit"> */
$myFusebox['thisFuseaction'] = "edit";
/* AdminVouchers.edit: <fusebox:instantiate object="control" class="AdminVouchers_control"> */
include_once($application["fusebox"]["WebRootToAppRootPath"]."AdminVouchers/AdminVouchers_control.php");
$control = new AdminVouchers_control;
/* AdminVouchers.edit: <fusebox:invoke object="control" methodcall="assertRequestMethod(['POST'])"> */
$control->assertRequestMethod(['POST']);
/* AdminVouchers.edit: <fusebox:invoke object="control" methodcall="assertCsrfToken()"> */
$control->assertCsrfToken();
/* AdminVouchers.edit: <fusebox:invoke object="control" methodcall="voucherEdit()"> */
$control->voucherEdit();
/* AdminVouchers.edit: <fusebox:set name="myFusebox['thisCircuit']" value="AdminVouchers"> */
$myFusebox['thisCircuit'] = "AdminVouchers";
/* AdminVouchers.edit: <fusebox:set name="myFusebox['thisFuseaction']" value="edit"> */
$myFusebox['thisFuseaction'] = "edit";
/* AdminVouchers.edit: <fusebox:set name="myFusebox['thisCircuit']" value="AdminVouchers"> */
$myFusebox['thisCircuit'] = "AdminVouchers";
/* AdminVouchers.edit: <fusebox:set name="myFusebox['thisFuseaction']" value="edit"> */
$myFusebox['thisFuseaction'] = "edit";

?>