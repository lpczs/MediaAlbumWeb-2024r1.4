<?php

/* OnlineAPI.basketInit: <fusebox:set name="myFusebox['thisCircuit']" value="OnlineAPI"> */
$myFusebox['thisCircuit'] = "OnlineAPI";
/* OnlineAPI.basketInit: <fusebox:set name="myFusebox['thisFuseaction']" value="basketInit"> */
$myFusebox['thisFuseaction'] = "basketInit";
/* OnlineAPI.basketInit: <fusebox:instantiate object="control" class="OnlineAPI_control"> */
include_once($application["fusebox"]["WebRootToAppRootPath"]."OnlineAPI/OnlineAPI_control.php");
$control = new OnlineAPI_control;
/* OnlineAPI.basketInit: <fusebox:invoke object="control" methodcall="highLevelBasketInitialise()"> */
$control->highLevelBasketInitialise();
/* OnlineAPI.basketInit: <fusebox:set name="myFusebox['thisCircuit']" value="OnlineAPI"> */
$myFusebox['thisCircuit'] = "OnlineAPI";
/* OnlineAPI.basketInit: <fusebox:set name="myFusebox['thisFuseaction']" value="basketInit"> */
$myFusebox['thisFuseaction'] = "basketInit";
/* OnlineAPI.basketInit: <fusebox:set name="myFusebox['thisCircuit']" value="OnlineAPI"> */
$myFusebox['thisCircuit'] = "OnlineAPI";
/* OnlineAPI.basketInit: <fusebox:set name="myFusebox['thisFuseaction']" value="basketInit"> */
$myFusebox['thisFuseaction'] = "basketInit";

?>