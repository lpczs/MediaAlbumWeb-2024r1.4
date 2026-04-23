<?php

/* AdminTaopixOnlineFontLists.initialize: <fusebox:set name="myFusebox['thisCircuit']" value="AdminTaopixOnlineFontLists"> */
$myFusebox['thisCircuit'] = "AdminTaopixOnlineFontLists";
/* AdminTaopixOnlineFontLists.initialize: <fusebox:set name="myFusebox['thisFuseaction']" value="initialize"> */
$myFusebox['thisFuseaction'] = "initialize";
/* AdminTaopixOnlineFontLists.initialize: <fusebox:instantiate object="control" class="AdminTaopixOnlineFontLists_control"> */
include_once($application["fusebox"]["WebRootToAppRootPath"]."AdminTaopixOnlineFontLists/AdminTaopixOnlineFontLists_control.php");
$control = new AdminTaopixOnlineFontLists_control;
/* AdminTaopixOnlineFontLists.initialize: <fusebox:invoke object="control" methodcall="initialize()"> */
$control->initialize();
/* AdminTaopixOnlineFontLists.initialize: <fusebox:set name="myFusebox['thisCircuit']" value="AdminTaopixOnlineFontLists"> */
$myFusebox['thisCircuit'] = "AdminTaopixOnlineFontLists";
/* AdminTaopixOnlineFontLists.initialize: <fusebox:set name="myFusebox['thisFuseaction']" value="initialize"> */
$myFusebox['thisFuseaction'] = "initialize";
/* AdminTaopixOnlineFontLists.initialize: <fusebox:set name="myFusebox['thisCircuit']" value="AdminTaopixOnlineFontLists"> */
$myFusebox['thisCircuit'] = "AdminTaopixOnlineFontLists";
/* AdminTaopixOnlineFontLists.initialize: <fusebox:set name="myFusebox['thisFuseaction']" value="initialize"> */
$myFusebox['thisFuseaction'] = "initialize";

?>