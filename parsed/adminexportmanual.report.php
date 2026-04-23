<?php

/* AdminExportManual.report: <fusebox:set name="myFusebox['thisCircuit']" value="AdminExportManual"> */
$myFusebox['thisCircuit'] = "AdminExportManual";
/* AdminExportManual.report: <fusebox:set name="myFusebox['thisFuseaction']" value="report"> */
$myFusebox['thisFuseaction'] = "report";
/* AdminExportManual.report: <fusebox:instantiate object="control" class="AdminExportManual_control"> */
include_once($application["fusebox"]["WebRootToAppRootPath"]."AdminExportManual/AdminExportManual_control.php");
$control = new AdminExportManual_control;
/* AdminExportManual.report: <fusebox:invoke object="control" methodcall="assertRequestMethod(['POST'])"> */
$control->assertRequestMethod(['POST']);
/* AdminExportManual.report: <fusebox:invoke object="control" methodcall="assertCsrfToken()"> */
$control->assertCsrfToken();
/* AdminExportManual.report: <fusebox:invoke object="control" methodcall="report()"> */
$control->report();
/* AdminExportManual.report: <fusebox:set name="myFusebox['thisCircuit']" value="AdminExportManual"> */
$myFusebox['thisCircuit'] = "AdminExportManual";
/* AdminExportManual.report: <fusebox:set name="myFusebox['thisFuseaction']" value="report"> */
$myFusebox['thisFuseaction'] = "report";
/* AdminExportManual.report: <fusebox:set name="myFusebox['thisCircuit']" value="AdminExportManual"> */
$myFusebox['thisCircuit'] = "AdminExportManual";
/* AdminExportManual.report: <fusebox:set name="myFusebox['thisFuseaction']" value="report"> */
$myFusebox['thisFuseaction'] = "report";

?>