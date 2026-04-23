<?php

/* OnlineAPI.signInInit: <fusebox:set name="myFusebox['thisCircuit']" value="OnlineAPI"> */
$myFusebox['thisCircuit'] = "OnlineAPI";
/* OnlineAPI.signInInit: <fusebox:set name="myFusebox['thisFuseaction']" value="signInInit"> */
$myFusebox['thisFuseaction'] = "signInInit";
/* OnlineAPI.signInInit: <fusebox:instantiate object="control" class="OnlineAPI_control"> */
include_once($application["fusebox"]["WebRootToAppRootPath"]."OnlineAPI/OnlineAPI_control.php");
$control = new OnlineAPI_control;
/* OnlineAPI.signInInit: <fusebox:invoke object="control" methodcall="highLevelSignInInit()"> */
$control->highLevelSignInInit();
/* OnlineAPI.signInInit: <fusebox:set name="myFusebox['thisCircuit']" value="OnlineAPI"> */
$myFusebox['thisCircuit'] = "OnlineAPI";
/* OnlineAPI.signInInit: <fusebox:set name="myFusebox['thisFuseaction']" value="signInInit"> */
$myFusebox['thisFuseaction'] = "signInInit";
/* OnlineAPI.signInInit: <fusebox:set name="myFusebox['thisCircuit']" value="OnlineAPI"> */
$myFusebox['thisCircuit'] = "OnlineAPI";
/* OnlineAPI.signInInit: <fusebox:set name="myFusebox['thisFuseaction']" value="signInInit"> */
$myFusebox['thisFuseaction'] = "signInInit";

?>