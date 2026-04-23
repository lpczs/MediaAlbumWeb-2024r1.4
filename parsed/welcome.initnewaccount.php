<?php

/* Welcome.initNewAccount: <fusebox:set name="myFusebox['thisCircuit']" value="Welcome"> */
$myFusebox['thisCircuit'] = "Welcome";
/* Welcome.initNewAccount: <fusebox:set name="myFusebox['thisFuseaction']" value="initNewAccount"> */
$myFusebox['thisFuseaction'] = "initNewAccount";
/* Welcome.initNewAccount: <fusebox:instantiate object="control" class="Welcome_control"> */
include_once($application["fusebox"]["WebRootToAppRootPath"]."Welcome/Welcome_control.php");
$control = new Welcome_control;
/* Welcome.initNewAccount: <fusebox:invoke object="control" methodcall="assertRequestMethod(['POST'])"> */
$control->assertRequestMethod(['POST']);
/* Welcome.initNewAccount: <fusebox:invoke object="control" methodcall="assertCsrfToken()"> */
$control->assertCsrfToken();
/* Welcome.initNewAccount: <fusebox:invoke object="control" methodcall="initNewAccount()"> */
$control->initNewAccount();
/* Welcome.initNewAccount: <fusebox:set name="myFusebox['thisCircuit']" value="Welcome"> */
$myFusebox['thisCircuit'] = "Welcome";
/* Welcome.initNewAccount: <fusebox:set name="myFusebox['thisFuseaction']" value="initNewAccount"> */
$myFusebox['thisFuseaction'] = "initNewAccount";
/* Welcome.initNewAccount: <fusebox:set name="myFusebox['thisCircuit']" value="Welcome"> */
$myFusebox['thisCircuit'] = "Welcome";
/* Welcome.initNewAccount: <fusebox:set name="myFusebox['thisFuseaction']" value="initNewAccount"> */
$myFusebox['thisFuseaction'] = "initNewAccount";

?>