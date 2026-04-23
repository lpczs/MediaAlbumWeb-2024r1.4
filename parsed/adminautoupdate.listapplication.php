<?php

/* AdminAutoUpdate.listApplication: <fusebox:set name="myFusebox['thisCircuit']" value="AdminAutoUpdate"> */
$myFusebox['thisCircuit'] = "AdminAutoUpdate";
/* AdminAutoUpdate.listApplication: <fusebox:set name="myFusebox['thisFuseaction']" value="listApplication"> */
$myFusebox['thisFuseaction'] = "listApplication";
/* AdminAutoUpdate.listApplication: <fusebox:instantiate object="control" class="AdminAutoUpdate_control"> */
include_once($application["fusebox"]["WebRootToAppRootPath"]."AdminAutoUpdate/AdminAutoUpdate_control.php");
$control = new AdminAutoUpdate_control;
/* AdminAutoUpdate.listApplication: <fusebox:invoke object="control" methodcall="assertRequestMethod(['POST'])"> */
$control->assertRequestMethod(['POST']);
/* AdminAutoUpdate.listApplication: <fusebox:invoke object="control" methodcall="assertCsrfToken()"> */
$control->assertCsrfToken();
/* AdminAutoUpdate.listApplication: <fusebox:invoke object="control" methodcall="listApplication()"> */
$control->listApplication();
/* AdminAutoUpdate.listApplication: <fusebox:set name="myFusebox['thisCircuit']" value="AdminAutoUpdate"> */
$myFusebox['thisCircuit'] = "AdminAutoUpdate";
/* AdminAutoUpdate.listApplication: <fusebox:set name="myFusebox['thisFuseaction']" value="listApplication"> */
$myFusebox['thisFuseaction'] = "listApplication";
/* AdminAutoUpdate.listApplication: <fusebox:set name="myFusebox['thisCircuit']" value="AdminAutoUpdate"> */
$myFusebox['thisCircuit'] = "AdminAutoUpdate";
/* AdminAutoUpdate.listApplication: <fusebox:set name="myFusebox['thisFuseaction']" value="listApplication"> */
$myFusebox['thisFuseaction'] = "listApplication";

?>