<?php

/* AdminTaopixOnlineFontLists.savefontlist: <fusebox:set name="myFusebox['thisCircuit']" value="AdminTaopixOnlineFontLists"> */
$myFusebox['thisCircuit'] = "AdminTaopixOnlineFontLists";
/* AdminTaopixOnlineFontLists.savefontlist: <fusebox:set name="myFusebox['thisFuseaction']" value="savefontlist"> */
$myFusebox['thisFuseaction'] = "savefontlist";
/* AdminTaopixOnlineFontLists.savefontlist: <fusebox:instantiate object="control" class="AdminTaopixOnlineFontLists_control"> */
include_once($application["fusebox"]["WebRootToAppRootPath"]."AdminTaopixOnlineFontLists/AdminTaopixOnlineFontLists_control.php");
$control = new AdminTaopixOnlineFontLists_control;
/* AdminTaopixOnlineFontLists.savefontlist: <fusebox:invoke object="control" methodcall="saveFontList()"> */
$control->saveFontList();
/* AdminTaopixOnlineFontLists.savefontlist: <fusebox:set name="myFusebox['thisCircuit']" value="AdminTaopixOnlineFontLists"> */
$myFusebox['thisCircuit'] = "AdminTaopixOnlineFontLists";
/* AdminTaopixOnlineFontLists.savefontlist: <fusebox:set name="myFusebox['thisFuseaction']" value="savefontlist"> */
$myFusebox['thisFuseaction'] = "savefontlist";
/* AdminTaopixOnlineFontLists.savefontlist: <fusebox:set name="myFusebox['thisCircuit']" value="AdminTaopixOnlineFontLists"> */
$myFusebox['thisCircuit'] = "AdminTaopixOnlineFontLists";
/* AdminTaopixOnlineFontLists.savefontlist: <fusebox:set name="myFusebox['thisFuseaction']" value="savefontlist"> */
$myFusebox['thisFuseaction'] = "savefontlist";

?>