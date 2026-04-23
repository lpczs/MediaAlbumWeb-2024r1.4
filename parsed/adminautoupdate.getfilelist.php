<?php

/* AdminAutoUpdate.getFileList: <fusebox:set name="myFusebox['thisCircuit']" value="AdminAutoUpdate"> */
$myFusebox['thisCircuit'] = "AdminAutoUpdate";
/* AdminAutoUpdate.getFileList: <fusebox:set name="myFusebox['thisFuseaction']" value="getFileList"> */
$myFusebox['thisFuseaction'] = "getFileList";
/* AdminAutoUpdate.getFileList: <fusebox:instantiate object="control" class="AdminAutoUpdate_control"> */
include_once($application["fusebox"]["WebRootToAppRootPath"]."AdminAutoUpdate/AdminAutoUpdate_control.php");
$control = new AdminAutoUpdate_control;
/* AdminAutoUpdate.getFileList: <fusebox:invoke object="control" methodcall="assertRequestMethod(['POST'])"> */
$control->assertRequestMethod(['POST']);
/* AdminAutoUpdate.getFileList: <fusebox:invoke object="control" methodcall="assertCsrfToken()"> */
$control->assertCsrfToken();
/* AdminAutoUpdate.getFileList: <fusebox:invoke object="control" methodcall="getFileList()"> */
$control->getFileList();
/* AdminAutoUpdate.getFileList: <fusebox:set name="myFusebox['thisCircuit']" value="AdminAutoUpdate"> */
$myFusebox['thisCircuit'] = "AdminAutoUpdate";
/* AdminAutoUpdate.getFileList: <fusebox:set name="myFusebox['thisFuseaction']" value="getFileList"> */
$myFusebox['thisFuseaction'] = "getFileList";
/* AdminAutoUpdate.getFileList: <fusebox:set name="myFusebox['thisCircuit']" value="AdminAutoUpdate"> */
$myFusebox['thisCircuit'] = "AdminAutoUpdate";
/* AdminAutoUpdate.getFileList: <fusebox:set name="myFusebox['thisFuseaction']" value="getFileList"> */
$myFusebox['thisFuseaction'] = "getFileList";

?>