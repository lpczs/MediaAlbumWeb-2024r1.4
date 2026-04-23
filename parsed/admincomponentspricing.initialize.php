<?php

/* AdminComponentsPricing.initialize: <fusebox:set name="myFusebox['thisCircuit']" value="AdminComponentsPricing"> */
$myFusebox['thisCircuit'] = "AdminComponentsPricing";
/* AdminComponentsPricing.initialize: <fusebox:set name="myFusebox['thisFuseaction']" value="initialize"> */
$myFusebox['thisFuseaction'] = "initialize";
/* AdminComponentsPricing.initialize: <fusebox:instantiate object="control" class="AdminComponentsPricing_control"> */
include_once($application["fusebox"]["WebRootToAppRootPath"]."AdminComponentsPricing/AdminComponentsPricing_control.php");
$control = new AdminComponentsPricing_control;
/* AdminComponentsPricing.initialize: <fusebox:invoke object="control" methodcall="assertRequestMethod(['GET'])"> */
$control->assertRequestMethod(['GET']);
/* AdminComponentsPricing.initialize: <fusebox:invoke object="control" methodcall="initialize()"> */
$control->initialize();
/* AdminComponentsPricing.initialize: <fusebox:set name="myFusebox['thisCircuit']" value="AdminComponentsPricing"> */
$myFusebox['thisCircuit'] = "AdminComponentsPricing";
/* AdminComponentsPricing.initialize: <fusebox:set name="myFusebox['thisFuseaction']" value="initialize"> */
$myFusebox['thisFuseaction'] = "initialize";
/* AdminComponentsPricing.initialize: <fusebox:set name="myFusebox['thisCircuit']" value="AdminComponentsPricing"> */
$myFusebox['thisCircuit'] = "AdminComponentsPricing";
/* AdminComponentsPricing.initialize: <fusebox:set name="myFusebox['thisFuseaction']" value="initialize"> */
$myFusebox['thisFuseaction'] = "initialize";

?>