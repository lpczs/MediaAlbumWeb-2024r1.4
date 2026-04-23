<?php

/* AdminComponentsPricing.priceListAddDisplay: <fusebox:set name="myFusebox['thisCircuit']" value="AdminComponentsPricing"> */
$myFusebox['thisCircuit'] = "AdminComponentsPricing";
/* AdminComponentsPricing.priceListAddDisplay: <fusebox:set name="myFusebox['thisFuseaction']" value="priceListAddDisplay"> */
$myFusebox['thisFuseaction'] = "priceListAddDisplay";
/* AdminComponentsPricing.priceListAddDisplay: <fusebox:instantiate object="control" class="AdminComponentsPricing_control"> */
include_once($application["fusebox"]["WebRootToAppRootPath"]."AdminComponentsPricing/AdminComponentsPricing_control.php");
$control = new AdminComponentsPricing_control;
/* AdminComponentsPricing.priceListAddDisplay: <fusebox:invoke object="control" methodcall="assertRequestMethod(['GET'])"> */
$control->assertRequestMethod(['GET']);
/* AdminComponentsPricing.priceListAddDisplay: <fusebox:invoke object="control" methodcall="priceListAddDisplay()"> */
$control->priceListAddDisplay();
/* AdminComponentsPricing.priceListAddDisplay: <fusebox:set name="myFusebox['thisCircuit']" value="AdminComponentsPricing"> */
$myFusebox['thisCircuit'] = "AdminComponentsPricing";
/* AdminComponentsPricing.priceListAddDisplay: <fusebox:set name="myFusebox['thisFuseaction']" value="priceListAddDisplay"> */
$myFusebox['thisFuseaction'] = "priceListAddDisplay";
/* AdminComponentsPricing.priceListAddDisplay: <fusebox:set name="myFusebox['thisCircuit']" value="AdminComponentsPricing"> */
$myFusebox['thisCircuit'] = "AdminComponentsPricing";
/* AdminComponentsPricing.priceListAddDisplay: <fusebox:set name="myFusebox['thisFuseaction']" value="priceListAddDisplay"> */
$myFusebox['thisFuseaction'] = "priceListAddDisplay";

?>