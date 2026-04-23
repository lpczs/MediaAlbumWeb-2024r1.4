<?php

/* Welcome.initialize: <fusebox:set name="myFusebox['thisCircuit']" value="Welcome"> */
$myFusebox['thisCircuit'] = "Welcome";
/* Welcome.initialize: <fusebox:set name="myFusebox['thisFuseaction']" value="initialize"> */
$myFusebox['thisFuseaction'] = "initialize";
/* Welcome.initialize: <fusebox:instantiate object="control" class="Welcome_control"> */
include_once($application["fusebox"]["WebRootToAppRootPath"]."Welcome/Welcome_control.php");
$control = new Welcome_control;
/* Welcome.initialize: <fusebox:invoke object="control" methodcall="assertRequestMethod(['GET', 'POST'])"> */
$control->assertRequestMethod(['GET', 'POST']);
/* Welcome.initialize: <fusebox:invoke object="control" methodcall="initialize()"> */
$control->initialize();
/* Welcome.initialize: <fusebox:set name="myFusebox['thisCircuit']" value="Welcome"> */
$myFusebox['thisCircuit'] = "Welcome";
/* Welcome.initialize: <fusebox:set name="myFusebox['thisFuseaction']" value="initialize"> */
$myFusebox['thisFuseaction'] = "initialize";
/* Welcome.initialize: <fusebox:set name="myFusebox['thisCircuit']" value="Welcome"> */
$myFusebox['thisCircuit'] = "Welcome";
/* Welcome.initialize: <fusebox:set name="myFusebox['thisFuseaction']" value="initialize"> */
$myFusebox['thisFuseaction'] = "initialize";

?>