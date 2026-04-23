<?php

/* Welcome.initForgotPassword: <fusebox:set name="myFusebox['thisCircuit']" value="Welcome"> */
$myFusebox['thisCircuit'] = "Welcome";
/* Welcome.initForgotPassword: <fusebox:set name="myFusebox['thisFuseaction']" value="initForgotPassword"> */
$myFusebox['thisFuseaction'] = "initForgotPassword";
/* Welcome.initForgotPassword: <fusebox:instantiate object="control" class="Welcome_control"> */
include_once($application["fusebox"]["WebRootToAppRootPath"]."Welcome/Welcome_control.php");
$control = new Welcome_control;
/* Welcome.initForgotPassword: <fusebox:invoke object="control" methodcall="assertRequestMethod(['POST', 'GET'])"> */
$control->assertRequestMethod(['POST', 'GET']);
/* Welcome.initForgotPassword: <fusebox:invoke object="control" methodcall="assertCsrfToken()"> */
$control->assertCsrfToken();
/* Welcome.initForgotPassword: <fusebox:invoke object="control" methodcall="initForgotPassword()"> */
$control->initForgotPassword();
/* Welcome.initForgotPassword: <fusebox:set name="myFusebox['thisCircuit']" value="Welcome"> */
$myFusebox['thisCircuit'] = "Welcome";
/* Welcome.initForgotPassword: <fusebox:set name="myFusebox['thisFuseaction']" value="initForgotPassword"> */
$myFusebox['thisFuseaction'] = "initForgotPassword";
/* Welcome.initForgotPassword: <fusebox:set name="myFusebox['thisCircuit']" value="Welcome"> */
$myFusebox['thisCircuit'] = "Welcome";
/* Welcome.initForgotPassword: <fusebox:set name="myFusebox['thisFuseaction']" value="initForgotPassword"> */
$myFusebox['thisFuseaction'] = "initForgotPassword";

?>