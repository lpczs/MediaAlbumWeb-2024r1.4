<?php

/* OnlineAPI.emptyBasket: <fusebox:set name="myFusebox['thisCircuit']" value="OnlineAPI"> */
$myFusebox['thisCircuit'] = "OnlineAPI";
/* OnlineAPI.emptyBasket: <fusebox:set name="myFusebox['thisFuseaction']" value="emptyBasket"> */
$myFusebox['thisFuseaction'] = "emptyBasket";
/* OnlineAPI.emptyBasket: <fusebox:instantiate object="control" class="OnlineAPI_control"> */
include_once($application["fusebox"]["WebRootToAppRootPath"]."OnlineAPI/OnlineAPI_control.php");
$control = new OnlineAPI_control;
/* OnlineAPI.emptyBasket: <fusebox:invoke object="control" methodcall="highLevelEmptyBasket()"> */
$control->highLevelEmptyBasket();
/* OnlineAPI.emptyBasket: <fusebox:set name="myFusebox['thisCircuit']" value="OnlineAPI"> */
$myFusebox['thisCircuit'] = "OnlineAPI";
/* OnlineAPI.emptyBasket: <fusebox:set name="myFusebox['thisFuseaction']" value="emptyBasket"> */
$myFusebox['thisFuseaction'] = "emptyBasket";
/* OnlineAPI.emptyBasket: <fusebox:set name="myFusebox['thisCircuit']" value="OnlineAPI"> */
$myFusebox['thisCircuit'] = "OnlineAPI";
/* OnlineAPI.emptyBasket: <fusebox:set name="myFusebox['thisFuseaction']" value="emptyBasket"> */
$myFusebox['thisFuseaction'] = "emptyBasket";

?>