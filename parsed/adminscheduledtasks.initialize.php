<?php

/* AdminScheduledTasks.initialize: <fusebox:set name="myFusebox['thisCircuit']" value="AdminScheduledTasks"> */
$myFusebox['thisCircuit'] = "AdminScheduledTasks";
/* AdminScheduledTasks.initialize: <fusebox:set name="myFusebox['thisFuseaction']" value="initialize"> */
$myFusebox['thisFuseaction'] = "initialize";
/* AdminScheduledTasks.initialize: <fusebox:instantiate object="control" class="AdminScheduledTasks_control"> */
include_once($application["fusebox"]["WebRootToAppRootPath"]."AdminScheduledTasks/AdminScheduledTasks_control.php");
$control = new AdminScheduledTasks_control;
/* AdminScheduledTasks.initialize: <fusebox:invoke object="control" methodcall="assertRequestMethod(['GET'])"> */
$control->assertRequestMethod(['GET']);
/* AdminScheduledTasks.initialize: <fusebox:invoke object="control" methodcall="initialize()"> */
$control->initialize();
/* AdminScheduledTasks.initialize: <fusebox:set name="myFusebox['thisCircuit']" value="AdminScheduledTasks"> */
$myFusebox['thisCircuit'] = "AdminScheduledTasks";
/* AdminScheduledTasks.initialize: <fusebox:set name="myFusebox['thisFuseaction']" value="initialize"> */
$myFusebox['thisFuseaction'] = "initialize";
/* AdminScheduledTasks.initialize: <fusebox:set name="myFusebox['thisCircuit']" value="AdminScheduledTasks"> */
$myFusebox['thisCircuit'] = "AdminScheduledTasks";
/* AdminScheduledTasks.initialize: <fusebox:set name="myFusebox['thisFuseaction']" value="initialize"> */
$myFusebox['thisFuseaction'] = "initialize";

?>