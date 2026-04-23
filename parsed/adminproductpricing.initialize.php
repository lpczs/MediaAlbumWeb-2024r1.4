<?php

/* AdminProductPricing.initialize: <fusebox:set name="myFusebox['thisCircuit']" value="AdminProductPricing"> */
$myFusebox['thisCircuit'] = "AdminProductPricing";
/* AdminProductPricing.initialize: <fusebox:set name="myFusebox['thisFuseaction']" value="initialize"> */
$myFusebox['thisFuseaction'] = "initialize";
/* AdminProductPricing.initialize: <fusebox:instantiate object="control" class="AdminProductPricing_control"> */
include_once($application["fusebox"]["WebRootToAppRootPath"]."AdminProductPricing/AdminProductPricing_control.php");
$control = new AdminProductPricing_control;
/* AdminProductPricing.initialize: <fusebox:invoke object="control" methodcall="assertRequestMethod(['GET'])"> */
$control->assertRequestMethod(['GET']);
/* AdminProductPricing.initialize: <fusebox:invoke object="control" methodcall="initialize()"> */
$control->initialize();
/* AdminProductPricing.initialize: <fusebox:set name="myFusebox['thisCircuit']" value="AdminProductPricing"> */
$myFusebox['thisCircuit'] = "AdminProductPricing";
/* AdminProductPricing.initialize: <fusebox:set name="myFusebox['thisFuseaction']" value="initialize"> */
$myFusebox['thisFuseaction'] = "initialize";
/* AdminProductPricing.initialize: <fusebox:set name="myFusebox['thisCircuit']" value="AdminProductPricing"> */
$myFusebox['thisCircuit'] = "AdminProductPricing";
/* AdminProductPricing.initialize: <fusebox:set name="myFusebox['thisFuseaction']" value="initialize"> */
$myFusebox['thisFuseaction'] = "initialize";

?>