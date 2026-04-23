<?php

/* AdminAutoUpdate.activateProductCollection: <fusebox:set name="myFusebox['thisCircuit']" value="AdminAutoUpdate"> */
$myFusebox['thisCircuit'] = "AdminAutoUpdate";
/* AdminAutoUpdate.activateProductCollection: <fusebox:set name="myFusebox['thisFuseaction']" value="activateProductCollection"> */
$myFusebox['thisFuseaction'] = "activateProductCollection";
/* AdminAutoUpdate.activateProductCollection: <fusebox:instantiate object="control" class="AdminAutoUpdate_control"> */
include_once($application["fusebox"]["WebRootToAppRootPath"]."AdminAutoUpdate/AdminAutoUpdate_control.php");
$control = new AdminAutoUpdate_control;
/* AdminAutoUpdate.activateProductCollection: <fusebox:invoke object="control" methodcall="assertRequestMethod(['POST'])"> */
$control->assertRequestMethod(['POST']);
/* AdminAutoUpdate.activateProductCollection: <fusebox:invoke object="control" methodcall="assertCsrfToken()"> */
$control->assertCsrfToken();
/* AdminAutoUpdate.activateProductCollection: <fusebox:invoke object="control" methodcall="activateProductCollection()"> */
$control->activateProductCollection();
/* AdminAutoUpdate.activateProductCollection: <fusebox:set name="myFusebox['thisCircuit']" value="AdminAutoUpdate"> */
$myFusebox['thisCircuit'] = "AdminAutoUpdate";
/* AdminAutoUpdate.activateProductCollection: <fusebox:set name="myFusebox['thisFuseaction']" value="activateProductCollection"> */
$myFusebox['thisFuseaction'] = "activateProductCollection";
/* AdminAutoUpdate.activateProductCollection: <fusebox:set name="myFusebox['thisCircuit']" value="AdminAutoUpdate"> */
$myFusebox['thisCircuit'] = "AdminAutoUpdate";
/* AdminAutoUpdate.activateProductCollection: <fusebox:set name="myFusebox['thisFuseaction']" value="activateProductCollection"> */
$myFusebox['thisFuseaction'] = "activateProductCollection";

?>