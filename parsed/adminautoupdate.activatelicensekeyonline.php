<?php

/* AdminAutoUpdate.activateLicenseKeyOnline: <fusebox:set name="myFusebox['thisCircuit']" value="AdminAutoUpdate"> */
$myFusebox['thisCircuit'] = "AdminAutoUpdate";
/* AdminAutoUpdate.activateLicenseKeyOnline: <fusebox:set name="myFusebox['thisFuseaction']" value="activateLicenseKeyOnline"> */
$myFusebox['thisFuseaction'] = "activateLicenseKeyOnline";
/* AdminAutoUpdate.activateLicenseKeyOnline: <fusebox:instantiate object="control" class="AdminAutoUpdate_control"> */
include_once($application["fusebox"]["WebRootToAppRootPath"]."AdminAutoUpdate/AdminAutoUpdate_control.php");
$control = new AdminAutoUpdate_control;
/* AdminAutoUpdate.activateLicenseKeyOnline: <fusebox:invoke object="control" methodcall="assertRequestMethod(['POST'])"> */
$control->assertRequestMethod(['POST']);
/* AdminAutoUpdate.activateLicenseKeyOnline: <fusebox:invoke object="control" methodcall="assertCsrfToken()"> */
$control->assertCsrfToken();
/* AdminAutoUpdate.activateLicenseKeyOnline: <fusebox:invoke object="control" methodcall="activateLicenseKeyOnline()"> */
$control->activateLicenseKeyOnline();
/* AdminAutoUpdate.activateLicenseKeyOnline: <fusebox:set name="myFusebox['thisCircuit']" value="AdminAutoUpdate"> */
$myFusebox['thisCircuit'] = "AdminAutoUpdate";
/* AdminAutoUpdate.activateLicenseKeyOnline: <fusebox:set name="myFusebox['thisFuseaction']" value="activateLicenseKeyOnline"> */
$myFusebox['thisFuseaction'] = "activateLicenseKeyOnline";
/* AdminAutoUpdate.activateLicenseKeyOnline: <fusebox:set name="myFusebox['thisCircuit']" value="AdminAutoUpdate"> */
$myFusebox['thisCircuit'] = "AdminAutoUpdate";
/* AdminAutoUpdate.activateLicenseKeyOnline: <fusebox:set name="myFusebox['thisFuseaction']" value="activateLicenseKeyOnline"> */
$myFusebox['thisFuseaction'] = "activateLicenseKeyOnline";

?>