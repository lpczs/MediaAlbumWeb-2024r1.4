<?php

/* AdminAutoUpdate.editLicenseKeyDisplay: <fusebox:set name="myFusebox['thisCircuit']" value="AdminAutoUpdate"> */
$myFusebox['thisCircuit'] = "AdminAutoUpdate";
/* AdminAutoUpdate.editLicenseKeyDisplay: <fusebox:set name="myFusebox['thisFuseaction']" value="editLicenseKeyDisplay"> */
$myFusebox['thisFuseaction'] = "editLicenseKeyDisplay";
/* AdminAutoUpdate.editLicenseKeyDisplay: <fusebox:instantiate object="control" class="AdminAutoUpdate_control"> */
include_once($application["fusebox"]["WebRootToAppRootPath"]."AdminAutoUpdate/AdminAutoUpdate_control.php");
$control = new AdminAutoUpdate_control;
/* AdminAutoUpdate.editLicenseKeyDisplay: <fusebox:invoke object="control" methodcall="assertRequestMethod(['GET'])"> */
$control->assertRequestMethod(['GET']);
/* AdminAutoUpdate.editLicenseKeyDisplay: <fusebox:invoke object="control" methodcall="editLicenseKeyDisplay()"> */
$control->editLicenseKeyDisplay();
/* AdminAutoUpdate.editLicenseKeyDisplay: <fusebox:set name="myFusebox['thisCircuit']" value="AdminAutoUpdate"> */
$myFusebox['thisCircuit'] = "AdminAutoUpdate";
/* AdminAutoUpdate.editLicenseKeyDisplay: <fusebox:set name="myFusebox['thisFuseaction']" value="editLicenseKeyDisplay"> */
$myFusebox['thisFuseaction'] = "editLicenseKeyDisplay";
/* AdminAutoUpdate.editLicenseKeyDisplay: <fusebox:set name="myFusebox['thisCircuit']" value="AdminAutoUpdate"> */
$myFusebox['thisCircuit'] = "AdminAutoUpdate";
/* AdminAutoUpdate.editLicenseKeyDisplay: <fusebox:set name="myFusebox['thisFuseaction']" value="editLicenseKeyDisplay"> */
$myFusebox['thisFuseaction'] = "editLicenseKeyDisplay";

?>