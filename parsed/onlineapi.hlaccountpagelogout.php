<?php

/* OnlineAPI.hlAccountPageLogout: <fusebox:set name="myFusebox['thisCircuit']" value="OnlineAPI"> */
$myFusebox['thisCircuit'] = "OnlineAPI";
/* OnlineAPI.hlAccountPageLogout: <fusebox:set name="myFusebox['thisFuseaction']" value="hlAccountPageLogout"> */
$myFusebox['thisFuseaction'] = "hlAccountPageLogout";
/* OnlineAPI.hlAccountPageLogout: <fusebox:instantiate object="control" class="OnlineAPI_control"> */
include_once($application["fusebox"]["WebRootToAppRootPath"]."OnlineAPI/OnlineAPI_control.php");
$control = new OnlineAPI_control;
/* OnlineAPI.hlAccountPageLogout: <fusebox:invoke object="control" methodcall="highLevelAccountPageLogout()"> */
$control->highLevelAccountPageLogout();
/* OnlineAPI.hlAccountPageLogout: <fusebox:set name="myFusebox['thisCircuit']" value="OnlineAPI"> */
$myFusebox['thisCircuit'] = "OnlineAPI";
/* OnlineAPI.hlAccountPageLogout: <fusebox:set name="myFusebox['thisFuseaction']" value="hlAccountPageLogout"> */
$myFusebox['thisFuseaction'] = "hlAccountPageLogout";
/* OnlineAPI.hlAccountPageLogout: <fusebox:set name="myFusebox['thisCircuit']" value="OnlineAPI"> */
$myFusebox['thisCircuit'] = "OnlineAPI";
/* OnlineAPI.hlAccountPageLogout: <fusebox:set name="myFusebox['thisFuseaction']" value="hlAccountPageLogout"> */
$myFusebox['thisFuseaction'] = "hlAccountPageLogout";

?>