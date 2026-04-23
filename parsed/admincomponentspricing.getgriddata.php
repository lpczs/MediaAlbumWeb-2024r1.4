<?php

/* AdminComponentsPricing.getGridData: <fusebox:set name="myFusebox['thisCircuit']" value="AdminComponentsPricing"> */
$myFusebox['thisCircuit'] = "AdminComponentsPricing";
/* AdminComponentsPricing.getGridData: <fusebox:set name="myFusebox['thisFuseaction']" value="getGridData"> */
$myFusebox['thisFuseaction'] = "getGridData";
/* AdminComponentsPricing.getGridData: <fusebox:instantiate object="control" class="AdminComponentsPricing_control"> */
include_once($application["fusebox"]["WebRootToAppRootPath"]."AdminComponentsPricing/AdminComponentsPricing_control.php");
$control = new AdminComponentsPricing_control;
/* AdminComponentsPricing.getGridData: <fusebox:invoke object="control" methodcall="assertRequestMethod(['POST'])"> */
$control->assertRequestMethod(['POST']);
/* AdminComponentsPricing.getGridData: <fusebox:invoke object="control" methodcall="assertCsrfToken()"> */
$control->assertCsrfToken();
/* AdminComponentsPricing.getGridData: <fusebox:invoke object="control" methodcall="getGridData()"> */
$control->getGridData();
/* AdminComponentsPricing.getGridData: <fusebox:set name="myFusebox['thisCircuit']" value="AdminComponentsPricing"> */
$myFusebox['thisCircuit'] = "AdminComponentsPricing";
/* AdminComponentsPricing.getGridData: <fusebox:set name="myFusebox['thisFuseaction']" value="getGridData"> */
$myFusebox['thisFuseaction'] = "getGridData";
/* AdminComponentsPricing.getGridData: <fusebox:set name="myFusebox['thisCircuit']" value="AdminComponentsPricing"> */
$myFusebox['thisCircuit'] = "AdminComponentsPricing";
/* AdminComponentsPricing.getGridData: <fusebox:set name="myFusebox['thisFuseaction']" value="getGridData"> */
$myFusebox['thisFuseaction'] = "getGridData";

?>