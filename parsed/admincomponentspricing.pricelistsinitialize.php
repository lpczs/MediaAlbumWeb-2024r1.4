<?php

/* AdminComponentsPricing.priceListsInitialize: <fusebox:set name="myFusebox['thisCircuit']" value="AdminComponentsPricing"> */
$myFusebox['thisCircuit'] = "AdminComponentsPricing";
/* AdminComponentsPricing.priceListsInitialize: <fusebox:set name="myFusebox['thisFuseaction']" value="priceListsInitialize"> */
$myFusebox['thisFuseaction'] = "priceListsInitialize";
/* AdminComponentsPricing.priceListsInitialize: <fusebox:instantiate object="control" class="AdminComponentsPricing_control"> */
include_once($application["fusebox"]["WebRootToAppRootPath"]."AdminComponentsPricing/AdminComponentsPricing_control.php");
$control = new AdminComponentsPricing_control;
/* AdminComponentsPricing.priceListsInitialize: <fusebox:invoke object="control" methodcall="assertRequestMethod(['GET'])"> */
$control->assertRequestMethod(['GET']);
/* AdminComponentsPricing.priceListsInitialize: <fusebox:invoke object="control" methodcall="priceListsInitialize()"> */
$control->priceListsInitialize();
/* AdminComponentsPricing.priceListsInitialize: <fusebox:set name="myFusebox['thisCircuit']" value="AdminComponentsPricing"> */
$myFusebox['thisCircuit'] = "AdminComponentsPricing";
/* AdminComponentsPricing.priceListsInitialize: <fusebox:set name="myFusebox['thisFuseaction']" value="priceListsInitialize"> */
$myFusebox['thisFuseaction'] = "priceListsInitialize";
/* AdminComponentsPricing.priceListsInitialize: <fusebox:set name="myFusebox['thisCircuit']" value="AdminComponentsPricing"> */
$myFusebox['thisCircuit'] = "AdminComponentsPricing";
/* AdminComponentsPricing.priceListsInitialize: <fusebox:set name="myFusebox['thisFuseaction']" value="priceListsInitialize"> */
$myFusebox['thisFuseaction'] = "priceListsInitialize";

?>