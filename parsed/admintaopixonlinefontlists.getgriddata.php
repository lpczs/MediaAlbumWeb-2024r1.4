<?php

/* AdminTaopixOnlineFontLists.getgriddata: <fusebox:set name="myFusebox['thisCircuit']" value="AdminTaopixOnlineFontLists"> */
$myFusebox['thisCircuit'] = "AdminTaopixOnlineFontLists";
/* AdminTaopixOnlineFontLists.getgriddata: <fusebox:set name="myFusebox['thisFuseaction']" value="getgriddata"> */
$myFusebox['thisFuseaction'] = "getgriddata";
/* AdminTaopixOnlineFontLists.getgriddata: <fusebox:instantiate object="control" class="AdminTaopixOnlineFontLists_control"> */
include_once($application["fusebox"]["WebRootToAppRootPath"]."AdminTaopixOnlineFontLists/AdminTaopixOnlineFontLists_control.php");
$control = new AdminTaopixOnlineFontLists_control;
/* AdminTaopixOnlineFontLists.getgriddata: <fusebox:invoke object="control" methodcall="getGridData()"> */
$control->getGridData();
/* AdminTaopixOnlineFontLists.getgriddata: <fusebox:set name="myFusebox['thisCircuit']" value="AdminTaopixOnlineFontLists"> */
$myFusebox['thisCircuit'] = "AdminTaopixOnlineFontLists";
/* AdminTaopixOnlineFontLists.getgriddata: <fusebox:set name="myFusebox['thisFuseaction']" value="getgriddata"> */
$myFusebox['thisFuseaction'] = "getgriddata";
/* AdminTaopixOnlineFontLists.getgriddata: <fusebox:set name="myFusebox['thisCircuit']" value="AdminTaopixOnlineFontLists"> */
$myFusebox['thisCircuit'] = "AdminTaopixOnlineFontLists";
/* AdminTaopixOnlineFontLists.getgriddata: <fusebox:set name="myFusebox['thisFuseaction']" value="getgriddata"> */
$myFusebox['thisFuseaction'] = "getgriddata";

?>