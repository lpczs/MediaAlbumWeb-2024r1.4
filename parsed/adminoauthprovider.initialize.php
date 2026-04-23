<?php

/* AdminOAuthProvider.initialize: <fusebox:set name="myFusebox['thisCircuit']" value="AdminOAuthProvider"> */
$myFusebox['thisCircuit'] = "AdminOAuthProvider";
/* AdminOAuthProvider.initialize: <fusebox:set name="myFusebox['thisFuseaction']" value="initialize"> */
$myFusebox['thisFuseaction'] = "initialize";
/* AdminOAuthProvider.initialize: <fusebox:instantiate object="control" class="AdminOAuthProvider_control"> */
include_once($application["fusebox"]["WebRootToAppRootPath"]."AdminOAuthProvider/AdminOAuthProvider_control.php");
$control = new AdminOAuthProvider_control;
/* AdminOAuthProvider.initialize: <fusebox:invoke object="control" methodcall="assertRequestMethod(['GET'])"> */
$control->assertRequestMethod(['GET']);
/* AdminOAuthProvider.initialize: <fusebox:invoke object="control" methodcall="initialize()"> */
$control->initialize();
/* AdminOAuthProvider.initialize: <fusebox:set name="myFusebox['thisCircuit']" value="AdminOAuthProvider"> */
$myFusebox['thisCircuit'] = "AdminOAuthProvider";
/* AdminOAuthProvider.initialize: <fusebox:set name="myFusebox['thisFuseaction']" value="initialize"> */
$myFusebox['thisFuseaction'] = "initialize";
/* AdminOAuthProvider.initialize: <fusebox:set name="myFusebox['thisCircuit']" value="AdminOAuthProvider"> */
$myFusebox['thisCircuit'] = "AdminOAuthProvider";
/* AdminOAuthProvider.initialize: <fusebox:set name="myFusebox['thisFuseaction']" value="initialize"> */
$myFusebox['thisFuseaction'] = "initialize";

?>