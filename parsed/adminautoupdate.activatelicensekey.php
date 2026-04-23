<?php

/* AdminAutoUpdate.activateLicenseKey: <fusebox:set name="myFusebox['thisCircuit']" value="AdminAutoUpdate"> */
$myFusebox['thisCircuit'] = "AdminAutoUpdate";
/* AdminAutoUpdate.activateLicenseKey: <fusebox:set name="myFusebox['thisFuseaction']" value="activateLicenseKey"> */
$myFusebox['thisFuseaction'] = "activateLicenseKey";
/* AdminAutoUpdate.activateLicenseKey: <fusebox:instantiate object="control" class="AdminAutoUpdate_control"> */
include_once($application["fusebox"]["WebRootToAppRootPath"]."AdminAutoUpdate/AdminAutoUpdate_control.php");
$control = new AdminAutoUpdate_control;
/* AdminAutoUpdate.activateLicenseKey: <fusebox:invoke object="control" methodcall="assertRequestMethod(['POST'])"> */
$control->assertRequestMethod(['POST']);
/* AdminAutoUpdate.activateLicenseKey: <fusebox:invoke object="control" methodcall="assertCsrfToken()"> */
$control->assertCsrfToken();
/* AdminAutoUpdate.activateLicenseKey: <fusebox:invoke object="control" methodcall="activateLicenseKey()"> */
$control->activateLicenseKey();
/* AdminAutoUpdate.activateLicenseKey: <fusebox:set name="myFusebox['thisCircuit']" value="AdminAutoUpdate"> */
$myFusebox['thisCircuit'] = "AdminAutoUpdate";
/* AdminAutoUpdate.activateLicenseKey: <fusebox:set name="myFusebox['thisFuseaction']" value="activateLicenseKey"> */
$myFusebox['thisFuseaction'] = "activateLicenseKey";
/* AdminAutoUpdate.activateLicenseKey: <fusebox:set name="myFusebox['thisCircuit']" value="AdminAutoUpdate"> */
$myFusebox['thisCircuit'] = "AdminAutoUpdate";
/* AdminAutoUpdate.activateLicenseKey: <fusebox:set name="myFusebox['thisFuseaction']" value="activateLicenseKey"> */
$myFusebox['thisFuseaction'] = "activateLicenseKey";

?>