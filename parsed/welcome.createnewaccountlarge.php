<?php

/* Welcome.createNewAccountLarge: <fusebox:set name="myFusebox['thisCircuit']" value="Welcome"> */
$myFusebox['thisCircuit'] = "Welcome";
/* Welcome.createNewAccountLarge: <fusebox:set name="myFusebox['thisFuseaction']" value="createNewAccountLarge"> */
$myFusebox['thisFuseaction'] = "createNewAccountLarge";
/* Welcome.createNewAccountLarge: <fusebox:instantiate object="control" class="Welcome_control"> */
include_once($application["fusebox"]["WebRootToAppRootPath"]."Welcome/Welcome_control.php");
$control = new Welcome_control;
/* Welcome.createNewAccountLarge: <fusebox:invoke object="control" methodcall="assertRequestMethod(['POST'])"> */
$control->assertRequestMethod(['POST']);
/* Welcome.createNewAccountLarge: <fusebox:invoke object="control" methodcall="assertCsrfToken()"> */
$control->assertCsrfToken();
/* Welcome.createNewAccountLarge: <fusebox:invoke object="control" methodcall="createNewAccountLarge()"> */
$control->createNewAccountLarge();
/* Welcome.createNewAccountLarge: <fusebox:set name="myFusebox['thisCircuit']" value="Welcome"> */
$myFusebox['thisCircuit'] = "Welcome";
/* Welcome.createNewAccountLarge: <fusebox:set name="myFusebox['thisFuseaction']" value="createNewAccountLarge"> */
$myFusebox['thisFuseaction'] = "createNewAccountLarge";
/* Welcome.createNewAccountLarge: <fusebox:set name="myFusebox['thisCircuit']" value="Welcome"> */
$myFusebox['thisCircuit'] = "Welcome";
/* Welcome.createNewAccountLarge: <fusebox:set name="myFusebox['thisFuseaction']" value="createNewAccountLarge"> */
$myFusebox['thisFuseaction'] = "createNewAccountLarge";

?>