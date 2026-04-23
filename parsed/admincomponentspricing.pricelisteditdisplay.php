<?php

/* AdminComponentsPricing.priceListEditDisplay: <fusebox:set name="myFusebox['thisCircuit']" value="AdminComponentsPricing"> */
$myFusebox['thisCircuit'] = "AdminComponentsPricing";
/* AdminComponentsPricing.priceListEditDisplay: <fusebox:set name="myFusebox['thisFuseaction']" value="priceListEditDisplay"> */
$myFusebox['thisFuseaction'] = "priceListEditDisplay";
/* AdminComponentsPricing.priceListEditDisplay: <fusebox:instantiate object="control" class="AdminComponentsPricing_control"> */
include_once($application["fusebox"]["WebRootToAppRootPath"]."AdminComponentsPricing/AdminComponentsPricing_control.php");
$control = new AdminComponentsPricing_control;
/* AdminComponentsPricing.priceListEditDisplay: <fusebox:invoke object="control" methodcall="assertRequestMethod(['GET'])"> */
$control->assertRequestMethod(['GET']);
/* AdminComponentsPricing.priceListEditDisplay: <fusebox:invoke object="control" methodcall="priceListEditDisplay()"> */
$control->priceListEditDisplay();
/* AdminComponentsPricing.priceListEditDisplay: <fusebox:set name="myFusebox['thisCircuit']" value="AdminComponentsPricing"> */
$myFusebox['thisCircuit'] = "AdminComponentsPricing";
/* AdminComponentsPricing.priceListEditDisplay: <fusebox:set name="myFusebox['thisFuseaction']" value="priceListEditDisplay"> */
$myFusebox['thisFuseaction'] = "priceListEditDisplay";
/* AdminComponentsPricing.priceListEditDisplay: <fusebox:set name="myFusebox['thisCircuit']" value="AdminComponentsPricing"> */
$myFusebox['thisCircuit'] = "AdminComponentsPricing";
/* AdminComponentsPricing.priceListEditDisplay: <fusebox:set name="myFusebox['thisFuseaction']" value="priceListEditDisplay"> */
$myFusebox['thisFuseaction'] = "priceListEditDisplay";

?>