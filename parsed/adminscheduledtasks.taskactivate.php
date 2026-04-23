<?php

/* AdminScheduledTasks.taskActivate: <fusebox:set name="myFusebox['thisCircuit']" value="AdminScheduledTasks"> */
$myFusebox['thisCircuit'] = "AdminScheduledTasks";
/* AdminScheduledTasks.taskActivate: <fusebox:set name="myFusebox['thisFuseaction']" value="taskActivate"> */
$myFusebox['thisFuseaction'] = "taskActivate";
/* AdminScheduledTasks.taskActivate: <fusebox:instantiate object="control" class="AdminScheduledTasks_control"> */
include_once($application["fusebox"]["WebRootToAppRootPath"]."AdminScheduledTasks/AdminScheduledTasks_control.php");
$control = new AdminScheduledTasks_control;
/* AdminScheduledTasks.taskActivate: <fusebox:invoke object="control" methodcall="assertRequestMethod(['POST'])"> */
$control->assertRequestMethod(['POST']);
/* AdminScheduledTasks.taskActivate: <fusebox:invoke object="control" methodcall="assertCsrfToken()"> */
$control->assertCsrfToken();
/* AdminScheduledTasks.taskActivate: <fusebox:invoke object="control" methodcall="taskActivate()"> */
$control->taskActivate();
/* AdminScheduledTasks.taskActivate: <fusebox:set name="myFusebox['thisCircuit']" value="AdminScheduledTasks"> */
$myFusebox['thisCircuit'] = "AdminScheduledTasks";
/* AdminScheduledTasks.taskActivate: <fusebox:set name="myFusebox['thisFuseaction']" value="taskActivate"> */
$myFusebox['thisFuseaction'] = "taskActivate";
/* AdminScheduledTasks.taskActivate: <fusebox:set name="myFusebox['thisCircuit']" value="AdminScheduledTasks"> */
$myFusebox['thisCircuit'] = "AdminScheduledTasks";
/* AdminScheduledTasks.taskActivate: <fusebox:set name="myFusebox['thisFuseaction']" value="taskActivate"> */
$myFusebox['thisFuseaction'] = "taskActivate";

?>