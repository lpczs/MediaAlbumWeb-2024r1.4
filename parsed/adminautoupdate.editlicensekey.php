<?php

/* AdminAutoUpdate.editLicenseKey: <fusebox:set name="myFusebox['thisCircuit']" value="AdminAutoUpdate"> */
$myFusebox['thisCircuit'] = "AdminAutoUpdate";
/* AdminAutoUpdate.editLicenseKey: <fusebox:set name="myFusebox['thisFuseaction']" value="editLicenseKey"> */
$myFusebox['thisFuseaction'] = "editLicenseKey";
/* AdminAutoUpdate.editLicenseKey: <fusebox:instantiate object="control" class="AdminAutoUpdate_control"> */
include_once($application["fusebox"]["WebRootToAppRootPath"]."AdminAutoUpdate/AdminAutoUpdate_control.php");
$control = new AdminAutoUpdate_control;
/* AdminAutoUpdate.editLicenseKey: <fusebox:invoke object="control" methodcall="assertRequestMethod(['POST'])"> */
$control->assertRequestMethod(['POST']);
/* AdminAutoUpdate.editLicenseKey: <fusebox:invoke object="control" methodcall="assertCsrfToken()"> */
$control->assertCsrfToken();
/* AdminAutoUpdate.editLicenseKey: <fusebox:invoke object="control" methodcall="editLicenseKey()"> */
$control->editLicenseKey();
/* AdminAutoUpdate.editLicenseKey: <fusebox:set name="myFusebox['thisCircuit']" value="AdminAutoUpdate"> */
$myFusebox['thisCircuit'] = "AdminAutoUpdate";
/* AdminAutoUpdate.editLicenseKey: <fusebox:set name="myFusebox['thisFuseaction']" value="editLicenseKey"> */
$myFusebox['thisFuseaction'] = "editLicenseKey";
/* AdminAutoUpdate.editLicenseKey: <fusebox:set name="myFusebox['thisCircuit']" value="AdminAutoUpdate"> */
$myFusebox['thisCircuit'] = "AdminAutoUpdate";
/* AdminAutoUpdate.editLicenseKey: <fusebox:set name="myFusebox['thisFuseaction']" value="editLicenseKey"> */
$myFusebox['thisFuseaction'] = "editLicenseKey";

?>