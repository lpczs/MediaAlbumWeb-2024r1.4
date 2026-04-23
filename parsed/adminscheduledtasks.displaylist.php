<?php

/* AdminScheduledTasks.displayList: <fusebox:set name="myFusebox['thisCircuit']" value="AdminScheduledTasks"> */
$myFusebox['thisCircuit'] = "AdminScheduledTasks";
/* AdminScheduledTasks.displayList: <fusebox:set name="myFusebox['thisFuseaction']" value="displayList"> */
$myFusebox['thisFuseaction'] = "displayList";
/* AdminScheduledTasks.displayList: <fusebox:instantiate object="control" class="AdminScheduledTasks_control"> */
include_once($application["fusebox"]["WebRootToAppRootPath"]."AdminScheduledTasks/AdminScheduledTasks_control.php");
$control = new AdminScheduledTasks_control;
/* AdminScheduledTasks.displayList: <fusebox:invoke object="control" methodcall="assertRequestMethod(['POST'])"> */
$control->assertRequestMethod(['POST']);
/* AdminScheduledTasks.displayList: <fusebox:invoke object="control" methodcall="assertCsrfToken()"> */
$control->assertCsrfToken();
/* AdminScheduledTasks.displayList: <fusebox:invoke object="control" methodcall="displayList()"> */
$control->displayList();
/* AdminScheduledTasks.displayList: <fusebox:set name="myFusebox['thisCircuit']" value="AdminScheduledTasks"> */
$myFusebox['thisCircuit'] = "AdminScheduledTasks";
/* AdminScheduledTasks.displayList: <fusebox:set name="myFusebox['thisFuseaction']" value="displayList"> */
$myFusebox['thisFuseaction'] = "displayList";
/* AdminScheduledTasks.displayList: <fusebox:set name="myFusebox['thisCircuit']" value="AdminScheduledTasks"> */
$myFusebox['thisCircuit'] = "AdminScheduledTasks";
/* AdminScheduledTasks.displayList: <fusebox:set name="myFusebox['thisFuseaction']" value="displayList"> */
$myFusebox['thisFuseaction'] = "displayList";

?>