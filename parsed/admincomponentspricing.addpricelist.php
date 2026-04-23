<?php

/* AdminComponentsPricing.addPriceList: <fusebox:set name="myFusebox['thisCircuit']" value="AdminComponentsPricing"> */
$myFusebox['thisCircuit'] = "AdminComponentsPricing";
/* AdminComponentsPricing.addPriceList: <fusebox:set name="myFusebox['thisFuseaction']" value="addPriceList"> */
$myFusebox['thisFuseaction'] = "addPriceList";
/* AdminComponentsPricing.addPriceList: <fusebox:instantiate object="control" class="AdminComponentsPricing_control"> */
include_once($application["fusebox"]["WebRootToAppRootPath"]."AdminComponentsPricing/AdminComponentsPricing_control.php");
$control = new AdminComponentsPricing_control;
/* AdminComponentsPricing.addPriceList: <fusebox:invoke object="control" methodcall="assertRequestMethod(['POST'])"> */
$control->assertRequestMethod(['POST']);
/* AdminComponentsPricing.addPriceList: <fusebox:invoke object="control" methodcall="assertCsrfToken()"> */
$control->assertCsrfToken();
/* AdminComponentsPricing.addPriceList: <fusebox:invoke object="control" methodcall="addPriceList();"> */
$control->addPriceList();;
/* AdminComponentsPricing.addPriceList: <fusebox:set name="myFusebox['thisCircuit']" value="AdminComponentsPricing"> */
$myFusebox['thisCircuit'] = "AdminComponentsPricing";
/* AdminComponentsPricing.addPriceList: <fusebox:set name="myFusebox['thisFuseaction']" value="addPriceList"> */
$myFusebox['thisFuseaction'] = "addPriceList";
/* AdminComponentsPricing.addPriceList: <fusebox:set name="myFusebox['thisCircuit']" value="AdminComponentsPricing"> */
$myFusebox['thisCircuit'] = "AdminComponentsPricing";
/* AdminComponentsPricing.addPriceList: <fusebox:set name="myFusebox['thisFuseaction']" value="addPriceList"> */
$myFusebox['thisFuseaction'] = "addPriceList";

?>