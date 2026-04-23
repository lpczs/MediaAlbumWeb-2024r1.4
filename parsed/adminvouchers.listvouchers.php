<?php

/* AdminVouchers.listVouchers: <fusebox:set name="myFusebox['thisCircuit']" value="AdminVouchers"> */
$myFusebox['thisCircuit'] = "AdminVouchers";
/* AdminVouchers.listVouchers: <fusebox:set name="myFusebox['thisFuseaction']" value="listVouchers"> */
$myFusebox['thisFuseaction'] = "listVouchers";
/* AdminVouchers.listVouchers: <fusebox:instantiate object="control" class="AdminVouchers_control"> */
include_once($application["fusebox"]["WebRootToAppRootPath"]."AdminVouchers/AdminVouchers_control.php");
$control = new AdminVouchers_control;
/* AdminVouchers.listVouchers: <fusebox:invoke object="control" methodcall="assertRequestMethod(['POST'])"> */
$control->assertRequestMethod(['POST']);
/* AdminVouchers.listVouchers: <fusebox:invoke object="control" methodcall="assertCsrfToken()"> */
$control->assertCsrfToken();
/* AdminVouchers.listVouchers: <fusebox:invoke object="control" methodcall="listVouchers()"> */
$control->listVouchers();
/* AdminVouchers.listVouchers: <fusebox:set name="myFusebox['thisCircuit']" value="AdminVouchers"> */
$myFusebox['thisCircuit'] = "AdminVouchers";
/* AdminVouchers.listVouchers: <fusebox:set name="myFusebox['thisFuseaction']" value="listVouchers"> */
$myFusebox['thisFuseaction'] = "listVouchers";
/* AdminVouchers.listVouchers: <fusebox:set name="myFusebox['thisCircuit']" value="AdminVouchers"> */
$myFusebox['thisCircuit'] = "AdminVouchers";
/* AdminVouchers.listVouchers: <fusebox:set name="myFusebox['thisFuseaction']" value="listVouchers"> */
$myFusebox['thisFuseaction'] = "listVouchers";

?>