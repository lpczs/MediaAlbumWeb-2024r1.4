<?php

/* Welcome.processLogin: <fusebox:set name="myFusebox['thisCircuit']" value="Welcome"> */
$myFusebox['thisCircuit'] = "Welcome";
/* Welcome.processLogin: <fusebox:set name="myFusebox['thisFuseaction']" value="processLogin"> */
$myFusebox['thisFuseaction'] = "processLogin";
/* Welcome.processLogin: <fusebox:instantiate object="control" class="Welcome_control"> */
include_once($application["fusebox"]["WebRootToAppRootPath"]."Welcome/Welcome_control.php");
$control = new Welcome_control;
/* Welcome.processLogin: <fusebox:invoke object="control" methodcall="assertRequestMethod(['POST'])"> */
$control->assertRequestMethod(['POST']);
/* Welcome.processLogin: <fusebox:invoke object="control" methodcall="assertCsrfToken()"> */
$control->assertCsrfToken();
/* Welcome.processLogin: <fusebox:invoke object="control" methodcall="processLogin()"> */
$control->processLogin();
/* Welcome.processLogin: <fusebox:set name="myFusebox['thisCircuit']" value="Welcome"> */
$myFusebox['thisCircuit'] = "Welcome";
/* Welcome.processLogin: <fusebox:set name="myFusebox['thisFuseaction']" value="processLogin"> */
$myFusebox['thisFuseaction'] = "processLogin";
/* Welcome.processLogin: <fusebox:set name="myFusebox['thisCircuit']" value="Welcome"> */
$myFusebox['thisCircuit'] = "Welcome";
/* Welcome.processLogin: <fusebox:set name="myFusebox['thisFuseaction']" value="processLogin"> */
$myFusebox['thisFuseaction'] = "processLogin";

?>