<?php

/* AdminAbout.initialize: <fusebox:set name="myFusebox['thisCircuit']" value="AdminAbout"> */
$myFusebox['thisCircuit'] = "AdminAbout";
/* AdminAbout.initialize: <fusebox:set name="myFusebox['thisFuseaction']" value="initialize"> */
$myFusebox['thisFuseaction'] = "initialize";
/* AdminAbout.initialize: <fusebox:instantiate object="control" class="AdminAbout_control"> */
include_once($application["fusebox"]["WebRootToAppRootPath"]."AdminAbout/AdminAbout_control.php");
$control = new AdminAbout_control;
/* AdminAbout.initialize: <fusebox:invoke object="control" methodcall="assertRequestMethod(['GET'])"> */
$control->assertRequestMethod(['GET']);
/* AdminAbout.initialize: <fusebox:invoke object="control" methodcall="initialize()"> */
$control->initialize();
/* AdminAbout.initialize: <fusebox:set name="myFusebox['thisCircuit']" value="AdminAbout"> */
$myFusebox['thisCircuit'] = "AdminAbout";
/* AdminAbout.initialize: <fusebox:set name="myFusebox['thisFuseaction']" value="initialize"> */
$myFusebox['thisFuseaction'] = "initialize";
/* AdminAbout.initialize: <fusebox:set name="myFusebox['thisCircuit']" value="AdminAbout"> */
$myFusebox['thisCircuit'] = "AdminAbout";
/* AdminAbout.initialize: <fusebox:set name="myFusebox['thisFuseaction']" value="initialize"> */
$myFusebox['thisFuseaction'] = "initialize";

?>