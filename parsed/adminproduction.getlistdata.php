<?php

/* AdminProduction.getListData: <fusebox:set name="myFusebox['thisCircuit']" value="AdminProduction"> */
$myFusebox['thisCircuit'] = "AdminProduction";
/* AdminProduction.getListData: <fusebox:set name="myFusebox['thisFuseaction']" value="getListData"> */
$myFusebox['thisFuseaction'] = "getListData";
/* AdminProduction.getListData: <fusebox:instantiate object="control" class="AdminProduction_control"> */
include_once($application["fusebox"]["WebRootToAppRootPath"]."AdminProduction/AdminProduction_control.php");
$control = new AdminProduction_control;
/* AdminProduction.getListData: <fusebox:invoke object="control" methodcall="assertRequestMethod(['POST'])"> */
$control->assertRequestMethod(['POST']);
/* AdminProduction.getListData: <fusebox:invoke object="control" methodcall="assertCsrfToken()"> */
$control->assertCsrfToken();
/* AdminProduction.getListData: <fusebox:invoke object="control" methodcall="getListData()"> */
$control->getListData();
/* AdminProduction.getListData: <fusebox:set name="myFusebox['thisCircuit']" value="AdminProduction"> */
$myFusebox['thisCircuit'] = "AdminProduction";
/* AdminProduction.getListData: <fusebox:set name="myFusebox['thisFuseaction']" value="getListData"> */
$myFusebox['thisFuseaction'] = "getListData";
/* AdminProduction.getListData: <fusebox:set name="myFusebox['thisCircuit']" value="AdminProduction"> */
$myFusebox['thisCircuit'] = "AdminProduction";
/* AdminProduction.getListData: <fusebox:set name="myFusebox['thisFuseaction']" value="getListData"> */
$myFusebox['thisFuseaction'] = "getListData";

?>