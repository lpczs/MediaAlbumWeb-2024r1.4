<?php

/* AdminComponentsPricing.getPriceListGridData: <fusebox:set name="myFusebox['thisCircuit']" value="AdminComponentsPricing"> */
$myFusebox['thisCircuit'] = "AdminComponentsPricing";
/* AdminComponentsPricing.getPriceListGridData: <fusebox:set name="myFusebox['thisFuseaction']" value="getPriceListGridData"> */
$myFusebox['thisFuseaction'] = "getPriceListGridData";
/* AdminComponentsPricing.getPriceListGridData: <fusebox:instantiate object="control" class="AdminComponentsPricing_control"> */
include_once($application["fusebox"]["WebRootToAppRootPath"]."AdminComponentsPricing/AdminComponentsPricing_control.php");
$control = new AdminComponentsPricing_control;
/* AdminComponentsPricing.getPriceListGridData: <fusebox:invoke object="control" methodcall="assertRequestMethod(['POST'])"> */
$control->assertRequestMethod(['POST']);
/* AdminComponentsPricing.getPriceListGridData: <fusebox:invoke object="control" methodcall="assertCsrfToken()"> */
$control->assertCsrfToken();
/* AdminComponentsPricing.getPriceListGridData: <fusebox:invoke object="control" methodcall="getPriceListGridData()"> */
$control->getPriceListGridData();
/* AdminComponentsPricing.getPriceListGridData: <fusebox:set name="myFusebox['thisCircuit']" value="AdminComponentsPricing"> */
$myFusebox['thisCircuit'] = "AdminComponentsPricing";
/* AdminComponentsPricing.getPriceListGridData: <fusebox:set name="myFusebox['thisFuseaction']" value="getPriceListGridData"> */
$myFusebox['thisFuseaction'] = "getPriceListGridData";
/* AdminComponentsPricing.getPriceListGridData: <fusebox:set name="myFusebox['thisCircuit']" value="AdminComponentsPricing"> */
$myFusebox['thisCircuit'] = "AdminComponentsPricing";
/* AdminComponentsPricing.getPriceListGridData: <fusebox:set name="myFusebox['thisFuseaction']" value="getPriceListGridData"> */
$myFusebox['thisFuseaction'] = "getPriceListGridData";

?>