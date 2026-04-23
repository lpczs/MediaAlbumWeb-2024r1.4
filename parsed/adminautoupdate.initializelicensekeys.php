<?php

/* AdminAutoUpdate.initializeLicenseKeys: <fusebox:set name="myFusebox['thisCircuit']" value="AdminAutoUpdate"> */
$myFusebox['thisCircuit'] = "AdminAutoUpdate";
/* AdminAutoUpdate.initializeLicenseKeys: <fusebox:set name="myFusebox['thisFuseaction']" value="initializeLicenseKeys"> */
$myFusebox['thisFuseaction'] = "initializeLicenseKeys";
/* AdminAutoUpdate.initializeLicenseKeys: <fusebox:instantiate object="control" class="AdminAutoUpdate_control"> */
include_once($application["fusebox"]["WebRootToAppRootPath"]."AdminAutoUpdate/AdminAutoUpdate_control.php");
$control = new AdminAutoUpdate_control;
/* AdminAutoUpdate.initializeLicenseKeys: <fusebox:invoke object="control" methodcall="assertRequestMethod(['GET'])"> */
$control->assertRequestMethod(['GET']);
/* AdminAutoUpdate.initializeLicenseKeys: <fusebox:invoke object="control" methodcall="initializeLicenseKeys()"> */
$control->initializeLicenseKeys();
/* AdminAutoUpdate.initializeLicenseKeys: <fusebox:set name="myFusebox['thisCircuit']" value="AdminAutoUpdate"> */
$myFusebox['thisCircuit'] = "AdminAutoUpdate";
/* AdminAutoUpdate.initializeLicenseKeys: <fusebox:set name="myFusebox['thisFuseaction']" value="initializeLicenseKeys"> */
$myFusebox['thisFuseaction'] = "initializeLicenseKeys";
/* AdminAutoUpdate.initializeLicenseKeys: <fusebox:set name="myFusebox['thisCircuit']" value="AdminAutoUpdate"> */
$myFusebox['thisCircuit'] = "AdminAutoUpdate";
/* AdminAutoUpdate.initializeLicenseKeys: <fusebox:set name="myFusebox['thisFuseaction']" value="initializeLicenseKeys"> */
$myFusebox['thisFuseaction'] = "initializeLicenseKeys";

?>