<?php

/* OnlineAPI.hlLogout: <fusebox:set name="myFusebox['thisCircuit']" value="OnlineAPI"> */
$myFusebox['thisCircuit'] = "OnlineAPI";
/* OnlineAPI.hlLogout: <fusebox:set name="myFusebox['thisFuseaction']" value="hlLogout"> */
$myFusebox['thisFuseaction'] = "hlLogout";
/* OnlineAPI.hlLogout: <fusebox:instantiate object="control" class="OnlineAPI_control"> */
include_once($application["fusebox"]["WebRootToAppRootPath"]."OnlineAPI/OnlineAPI_control.php");
$control = new OnlineAPI_control;
/* OnlineAPI.hlLogout: <fusebox:invoke object="control" methodcall="highLevelLogout()"> */
$control->highLevelLogout();
/* OnlineAPI.hlLogout: <fusebox:set name="myFusebox['thisCircuit']" value="OnlineAPI"> */
$myFusebox['thisCircuit'] = "OnlineAPI";
/* OnlineAPI.hlLogout: <fusebox:set name="myFusebox['thisFuseaction']" value="hlLogout"> */
$myFusebox['thisFuseaction'] = "hlLogout";
/* OnlineAPI.hlLogout: <fusebox:set name="myFusebox['thisCircuit']" value="OnlineAPI"> */
$myFusebox['thisCircuit'] = "OnlineAPI";
/* OnlineAPI.hlLogout: <fusebox:set name="myFusebox['thisFuseaction']" value="hlLogout"> */
$myFusebox['thisFuseaction'] = "hlLogout";

?>