<?php

/* OnlineAPI.checkout: <fusebox:set name="myFusebox['thisCircuit']" value="OnlineAPI"> */
$myFusebox['thisCircuit'] = "OnlineAPI";
/* OnlineAPI.checkout: <fusebox:set name="myFusebox['thisFuseaction']" value="checkout"> */
$myFusebox['thisFuseaction'] = "checkout";
/* OnlineAPI.checkout: <fusebox:instantiate object="control" class="OnlineAPI_control"> */
include_once($application["fusebox"]["WebRootToAppRootPath"]."OnlineAPI/OnlineAPI_control.php");
$control = new OnlineAPI_control;
/* OnlineAPI.checkout: <fusebox:invoke object="control" methodcall="highLevelCheckout()"> */
$control->highLevelCheckout();
/* OnlineAPI.checkout: <fusebox:set name="myFusebox['thisCircuit']" value="OnlineAPI"> */
$myFusebox['thisCircuit'] = "OnlineAPI";
/* OnlineAPI.checkout: <fusebox:set name="myFusebox['thisFuseaction']" value="checkout"> */
$myFusebox['thisFuseaction'] = "checkout";
/* OnlineAPI.checkout: <fusebox:set name="myFusebox['thisCircuit']" value="OnlineAPI"> */
$myFusebox['thisCircuit'] = "OnlineAPI";
/* OnlineAPI.checkout: <fusebox:set name="myFusebox['thisFuseaction']" value="checkout"> */
$myFusebox['thisFuseaction'] = "checkout";

?>