<?php

/* Welcome.resetPasswordRequest: <fusebox:set name="myFusebox['thisCircuit']" value="Welcome"> */
$myFusebox['thisCircuit'] = "Welcome";
/* Welcome.resetPasswordRequest: <fusebox:set name="myFusebox['thisFuseaction']" value="resetPasswordRequest"> */
$myFusebox['thisFuseaction'] = "resetPasswordRequest";
/* Welcome.resetPasswordRequest: <fusebox:instantiate object="control" class="Welcome_control"> */
include_once($application["fusebox"]["WebRootToAppRootPath"]."Welcome/Welcome_control.php");
$control = new Welcome_control;
/* Welcome.resetPasswordRequest: <fusebox:invoke object="control" methodcall="assertRequestMethod(['POST'])"> */
$control->assertRequestMethod(['POST']);
/* Welcome.resetPasswordRequest: <fusebox:invoke object="control" methodcall="assertCsrfToken()"> */
$control->assertCsrfToken();
/* Welcome.resetPasswordRequest: <fusebox:invoke object="control" methodcall="resetPasswordRequest()"> */
$control->resetPasswordRequest();
/* Welcome.resetPasswordRequest: <fusebox:set name="myFusebox['thisCircuit']" value="Welcome"> */
$myFusebox['thisCircuit'] = "Welcome";
/* Welcome.resetPasswordRequest: <fusebox:set name="myFusebox['thisFuseaction']" value="resetPasswordRequest"> */
$myFusebox['thisFuseaction'] = "resetPasswordRequest";
/* Welcome.resetPasswordRequest: <fusebox:set name="myFusebox['thisCircuit']" value="Welcome"> */
$myFusebox['thisCircuit'] = "Welcome";
/* Welcome.resetPasswordRequest: <fusebox:set name="myFusebox['thisFuseaction']" value="resetPasswordRequest"> */
$myFusebox['thisFuseaction'] = "resetPasswordRequest";

?>