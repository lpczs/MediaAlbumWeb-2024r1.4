<?php

/* OnlineAPI.myAccountInit: <fusebox:set name="myFusebox['thisCircuit']" value="OnlineAPI"> */
$myFusebox['thisCircuit'] = "OnlineAPI";
/* OnlineAPI.myAccountInit: <fusebox:set name="myFusebox['thisFuseaction']" value="myAccountInit"> */
$myFusebox['thisFuseaction'] = "myAccountInit";
/* OnlineAPI.myAccountInit: <fusebox:instantiate object="control" class="OnlineAPI_control"> */
include_once($application["fusebox"]["WebRootToAppRootPath"]."OnlineAPI/OnlineAPI_control.php");
$control = new OnlineAPI_control;
/* OnlineAPI.myAccountInit: <fusebox:invoke object="control" methodcall="highLevelMyAccountInit()"> */
$control->highLevelMyAccountInit();
/* OnlineAPI.myAccountInit: <fusebox:set name="myFusebox['thisCircuit']" value="OnlineAPI"> */
$myFusebox['thisCircuit'] = "OnlineAPI";
/* OnlineAPI.myAccountInit: <fusebox:set name="myFusebox['thisFuseaction']" value="myAccountInit"> */
$myFusebox['thisFuseaction'] = "myAccountInit";
/* OnlineAPI.myAccountInit: <fusebox:set name="myFusebox['thisCircuit']" value="OnlineAPI"> */
$myFusebox['thisCircuit'] = "OnlineAPI";
/* OnlineAPI.myAccountInit: <fusebox:set name="myFusebox['thisFuseaction']" value="myAccountInit"> */
$myFusebox['thisFuseaction'] = "myAccountInit";

?>