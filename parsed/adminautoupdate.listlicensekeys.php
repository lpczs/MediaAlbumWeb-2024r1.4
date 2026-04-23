<?php

/* AdminAutoUpdate.listLicenseKeys: <fusebox:set name="myFusebox['thisCircuit']" value="AdminAutoUpdate"> */
$myFusebox['thisCircuit'] = "AdminAutoUpdate";
/* AdminAutoUpdate.listLicenseKeys: <fusebox:set name="myFusebox['thisFuseaction']" value="listLicenseKeys"> */
$myFusebox['thisFuseaction'] = "listLicenseKeys";
/* AdminAutoUpdate.listLicenseKeys: <fusebox:instantiate object="control" class="AdminAutoUpdate_control"> */
include_once($application["fusebox"]["WebRootToAppRootPath"]."AdminAutoUpdate/AdminAutoUpdate_control.php");
$control = new AdminAutoUpdate_control;
/* AdminAutoUpdate.listLicenseKeys: <fusebox:invoke object="control" methodcall="assertRequestMethod(['POST'])"> */
$control->assertRequestMethod(['POST']);
/* AdminAutoUpdate.listLicenseKeys: <fusebox:invoke object="control" methodcall="assertCsrfToken()"> */
$control->assertCsrfToken();
/* AdminAutoUpdate.listLicenseKeys: <fusebox:invoke object="control" methodcall="listLicenseKeys()"> */
$control->listLicenseKeys();
/* AdminAutoUpdate.listLicenseKeys: <fusebox:set name="myFusebox['thisCircuit']" value="AdminAutoUpdate"> */
$myFusebox['thisCircuit'] = "AdminAutoUpdate";
/* AdminAutoUpdate.listLicenseKeys: <fusebox:set name="myFusebox['thisFuseaction']" value="listLicenseKeys"> */
$myFusebox['thisFuseaction'] = "listLicenseKeys";
/* AdminAutoUpdate.listLicenseKeys: <fusebox:set name="myFusebox['thisCircuit']" value="AdminAutoUpdate"> */
$myFusebox['thisCircuit'] = "AdminAutoUpdate";
/* AdminAutoUpdate.listLicenseKeys: <fusebox:set name="myFusebox['thisFuseaction']" value="listLicenseKeys"> */
$myFusebox['thisFuseaction'] = "listLicenseKeys";

?>