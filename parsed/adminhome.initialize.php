<?php

/* AdminHome.initialize: <fusebox:set name="myFusebox['thisCircuit']" value="AdminHome"> */
$myFusebox['thisCircuit'] = "AdminHome";
/* AdminHome.initialize: <fusebox:set name="myFusebox['thisFuseaction']" value="initialize"> */
$myFusebox['thisFuseaction'] = "initialize";
/* AdminHome.initialize: <fusebox:instantiate object="control" class="AdminHome_control"> */
include_once($application["fusebox"]["WebRootToAppRootPath"]."AdminHome/AdminHome_control.php");
$control = new AdminHome_control;
/* AdminHome.initialize: <fusebox:invoke object="control" methodcall="assertRequestMethod(['GET'])"> */
$control->assertRequestMethod(['GET']);
/* AdminHome.initialize: <fusebox:invoke object="control" methodcall="initialize()"> */
$control->initialize();
/* AdminHome.initialize: <fusebox:set name="myFusebox['thisCircuit']" value="AdminHome"> */
$myFusebox['thisCircuit'] = "AdminHome";
/* AdminHome.initialize: <fusebox:set name="myFusebox['thisFuseaction']" value="initialize"> */
$myFusebox['thisFuseaction'] = "initialize";
/* AdminHome.initialize: <fusebox:set name="myFusebox['thisCircuit']" value="AdminHome"> */
$myFusebox['thisCircuit'] = "AdminHome";
/* AdminHome.initialize: <fusebox:set name="myFusebox['thisFuseaction']" value="initialize"> */
$myFusebox['thisFuseaction'] = "initialize";

?>