<?php

/* AdminProductPricing.addPriceList: <fusebox:set name="myFusebox['thisCircuit']" value="AdminProductPricing"> */
$myFusebox['thisCircuit'] = "AdminProductPricing";
/* AdminProductPricing.addPriceList: <fusebox:set name="myFusebox['thisFuseaction']" value="addPriceList"> */
$myFusebox['thisFuseaction'] = "addPriceList";
/* AdminProductPricing.addPriceList: <fusebox:instantiate object="control" class="AdminProductPricing_control"> */
include_once($application["fusebox"]["WebRootToAppRootPath"]."AdminProductPricing/AdminProductPricing_control.php");
$control = new AdminProductPricing_control;
/* AdminProductPricing.addPriceList: <fusebox:invoke object="control" methodcall="assertRequestMethod(['POST'])"> */
$control->assertRequestMethod(['POST']);
/* AdminProductPricing.addPriceList: <fusebox:invoke object="control" methodcall="assertCsrfToken()"> */
$control->assertCsrfToken();
/* AdminProductPricing.addPriceList: <fusebox:invoke object="control" methodcall="addPriceList();"> */
$control->addPriceList();;
/* AdminProductPricing.addPriceList: <fusebox:set name="myFusebox['thisCircuit']" value="AdminProductPricing"> */
$myFusebox['thisCircuit'] = "AdminProductPricing";
/* AdminProductPricing.addPriceList: <fusebox:set name="myFusebox['thisFuseaction']" value="addPriceList"> */
$myFusebox['thisFuseaction'] = "addPriceList";
/* AdminProductPricing.addPriceList: <fusebox:set name="myFusebox['thisCircuit']" value="AdminProductPricing"> */
$myFusebox['thisCircuit'] = "AdminProductPricing";
/* AdminProductPricing.addPriceList: <fusebox:set name="myFusebox['thisFuseaction']" value="addPriceList"> */
$myFusebox['thisFuseaction'] = "addPriceList";

?>