<?php

/* AdminExportManual.export: <fusebox:set name="myFusebox['thisCircuit']" value="AdminExportManual"> */
$myFusebox['thisCircuit'] = "AdminExportManual";
/* AdminExportManual.export: <fusebox:set name="myFusebox['thisFuseaction']" value="export"> */
$myFusebox['thisFuseaction'] = "export";
/* AdminExportManual.export: <fusebox:instantiate object="control" class="AdminExportManual_control"> */
include_once($application["fusebox"]["WebRootToAppRootPath"]."AdminExportManual/AdminExportManual_control.php");
$control = new AdminExportManual_control;
/* AdminExportManual.export: <fusebox:invoke object="control" methodcall="assertRequestMethod(['GET'])"> */
$control->assertRequestMethod(['GET']);
/* AdminExportManual.export: <fusebox:invoke object="control" methodcall="export()"> */
$control->export();
/* AdminExportManual.export: <fusebox:set name="myFusebox['thisCircuit']" value="AdminExportManual"> */
$myFusebox['thisCircuit'] = "AdminExportManual";
/* AdminExportManual.export: <fusebox:set name="myFusebox['thisFuseaction']" value="export"> */
$myFusebox['thisFuseaction'] = "export";
/* AdminExportManual.export: <fusebox:set name="myFusebox['thisCircuit']" value="AdminExportManual"> */
$myFusebox['thisCircuit'] = "AdminExportManual";
/* AdminExportManual.export: <fusebox:set name="myFusebox['thisFuseaction']" value="export"> */
$myFusebox['thisFuseaction'] = "export";

?>