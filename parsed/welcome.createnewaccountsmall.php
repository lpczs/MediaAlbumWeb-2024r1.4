<?php

/* Welcome.createNewAccountSmall: <fusebox:set name="myFusebox['thisCircuit']" value="Welcome"> */
$myFusebox['thisCircuit'] = "Welcome";
/* Welcome.createNewAccountSmall: <fusebox:set name="myFusebox['thisFuseaction']" value="createNewAccountSmall"> */
$myFusebox['thisFuseaction'] = "createNewAccountSmall";
/* Welcome.createNewAccountSmall: <fusebox:instantiate object="control" class="Welcome_control"> */
include_once($application["fusebox"]["WebRootToAppRootPath"]."Welcome/Welcome_control.php");
$control = new Welcome_control;
/* Welcome.createNewAccountSmall: <fusebox:invoke object="control" methodcall="assertRequestMethod(['POST'])"> */
$control->assertRequestMethod(['POST']);
/* Welcome.createNewAccountSmall: <fusebox:invoke object="control" methodcall="assertCsrfToken()"> */
$control->assertCsrfToken();
/* Welcome.createNewAccountSmall: <fusebox:invoke object="control" methodcall="createNewAccountSmall()"> */
$control->createNewAccountSmall();
/* Welcome.createNewAccountSmall: <fusebox:set name="myFusebox['thisCircuit']" value="Welcome"> */
$myFusebox['thisCircuit'] = "Welcome";
/* Welcome.createNewAccountSmall: <fusebox:set name="myFusebox['thisFuseaction']" value="createNewAccountSmall"> */
$myFusebox['thisFuseaction'] = "createNewAccountSmall";
/* Welcome.createNewAccountSmall: <fusebox:set name="myFusebox['thisCircuit']" value="Welcome"> */
$myFusebox['thisCircuit'] = "Welcome";
/* Welcome.createNewAccountSmall: <fusebox:set name="myFusebox['thisFuseaction']" value="createNewAccountSmall"> */
$myFusebox['thisFuseaction'] = "createNewAccountSmall";

?>