<?php

/* AdminExportManual.initialize: <fusebox:set name="myFusebox['thisCircuit']" value="AdminExportManual"> */
$myFusebox['thisCircuit'] = "AdminExportManual";
/* AdminExportManual.initialize: <fusebox:set name="myFusebox['thisFuseaction']" value="initialize"> */
$myFusebox['thisFuseaction'] = "initialize";
/* AdminExportManual.initialize: <fusebox:instantiate object="control" class="AdminExportManual_control"> */
include_once($application["fusebox"]["WebRootToAppRootPath"]."AdminExportManual/AdminExportManual_control.php");
$control = new AdminExportManual_control;
/* AdminExportManual.initialize: <fusebox:invoke object="control" methodcall="assertRequestMethod(['GET'])"> */
$control->assertRequestMethod(['GET']);
/* AdminExportManual.initialize: <fusebox:invoke object="control" methodcall="initialize()"> */
$control->initialize();
/* AdminExportManual.initialize: <fusebox:set name="myFusebox['thisCircuit']" value="AdminExportManual"> */
$myFusebox['thisCircuit'] = "AdminExportManual";
/* AdminExportManual.initialize: <fusebox:set name="myFusebox['thisFuseaction']" value="initialize"> */
$myFusebox['thisFuseaction'] = "initialize";
/* AdminExportManual.initialize: <fusebox:set name="myFusebox['thisCircuit']" value="AdminExportManual"> */
$myFusebox['thisCircuit'] = "AdminExportManual";
/* AdminExportManual.initialize: <fusebox:set name="myFusebox['thisFuseaction']" value="initialize"> */
$myFusebox['thisFuseaction'] = "initialize";

?>