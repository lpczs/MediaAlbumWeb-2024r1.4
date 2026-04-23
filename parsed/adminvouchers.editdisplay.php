<?php

/* AdminVouchers.editDisplay: <fusebox:set name="myFusebox['thisCircuit']" value="AdminVouchers"> */
$myFusebox['thisCircuit'] = "AdminVouchers";
/* AdminVouchers.editDisplay: <fusebox:set name="myFusebox['thisFuseaction']" value="editDisplay"> */
$myFusebox['thisFuseaction'] = "editDisplay";
/* AdminVouchers.editDisplay: <fusebox:instantiate object="control" class="AdminVouchers_control"> */
include_once($application["fusebox"]["WebRootToAppRootPath"]."AdminVouchers/AdminVouchers_control.php");
$control = new AdminVouchers_control;
/* AdminVouchers.editDisplay: <fusebox:invoke object="control" methodcall="assertRequestMethod(['GET'])"> */
$control->assertRequestMethod(['GET']);
/* AdminVouchers.editDisplay: <fusebox:invoke object="control" methodcall="voucherEditDisplay()"> */
$control->voucherEditDisplay();
/* AdminVouchers.editDisplay: <fusebox:set name="myFusebox['thisCircuit']" value="AdminVouchers"> */
$myFusebox['thisCircuit'] = "AdminVouchers";
/* AdminVouchers.editDisplay: <fusebox:set name="myFusebox['thisFuseaction']" value="editDisplay"> */
$myFusebox['thisFuseaction'] = "editDisplay";
/* AdminVouchers.editDisplay: <fusebox:set name="myFusebox['thisCircuit']" value="AdminVouchers"> */
$myFusebox['thisCircuit'] = "AdminVouchers";
/* AdminVouchers.editDisplay: <fusebox:set name="myFusebox['thisFuseaction']" value="editDisplay"> */
$myFusebox['thisFuseaction'] = "editDisplay";

?>