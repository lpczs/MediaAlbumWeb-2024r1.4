<?php

/* AdminVouchers.displayList: <fusebox:set name="myFusebox['thisCircuit']" value="AdminVouchers"> */
$myFusebox['thisCircuit'] = "AdminVouchers";
/* AdminVouchers.displayList: <fusebox:set name="myFusebox['thisFuseaction']" value="displayList"> */
$myFusebox['thisFuseaction'] = "displayList";
/* AdminVouchers.displayList: <fusebox:instantiate object="control" class="AdminVouchers_control"> */
include_once($application["fusebox"]["WebRootToAppRootPath"]."AdminVouchers/AdminVouchers_control.php");
$control = new AdminVouchers_control;
/* AdminVouchers.displayList: <fusebox:invoke object="control" methodcall="assertRequestMethod(['GET'])"> */
$control->assertRequestMethod(['GET']);
/* AdminVouchers.displayList: <fusebox:invoke object="control" methodcall="displayList()"> */
$control->displayList();
/* AdminVouchers.displayList: <fusebox:set name="myFusebox['thisCircuit']" value="AdminVouchers"> */
$myFusebox['thisCircuit'] = "AdminVouchers";
/* AdminVouchers.displayList: <fusebox:set name="myFusebox['thisFuseaction']" value="displayList"> */
$myFusebox['thisFuseaction'] = "displayList";
/* AdminVouchers.displayList: <fusebox:set name="myFusebox['thisCircuit']" value="AdminVouchers"> */
$myFusebox['thisCircuit'] = "AdminVouchers";
/* AdminVouchers.displayList: <fusebox:set name="myFusebox['thisFuseaction']" value="displayList"> */
$myFusebox['thisFuseaction'] = "displayList";

?>