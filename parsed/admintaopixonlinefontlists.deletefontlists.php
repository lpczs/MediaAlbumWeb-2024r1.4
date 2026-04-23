<?php

/* AdminTaopixOnlineFontLists.deletefontlists: <fusebox:set name="myFusebox['thisCircuit']" value="AdminTaopixOnlineFontLists"> */
$myFusebox['thisCircuit'] = "AdminTaopixOnlineFontLists";
/* AdminTaopixOnlineFontLists.deletefontlists: <fusebox:set name="myFusebox['thisFuseaction']" value="deletefontlists"> */
$myFusebox['thisFuseaction'] = "deletefontlists";
/* AdminTaopixOnlineFontLists.deletefontlists: <fusebox:instantiate object="control" class="AdminTaopixOnlineFontLists_control"> */
include_once($application["fusebox"]["WebRootToAppRootPath"]."AdminTaopixOnlineFontLists/AdminTaopixOnlineFontLists_control.php");
$control = new AdminTaopixOnlineFontLists_control;
/* AdminTaopixOnlineFontLists.deletefontlists: <fusebox:invoke object="control" methodcall="deleteFontLists()"> */
$control->deleteFontLists();
/* AdminTaopixOnlineFontLists.deletefontlists: <fusebox:set name="myFusebox['thisCircuit']" value="AdminTaopixOnlineFontLists"> */
$myFusebox['thisCircuit'] = "AdminTaopixOnlineFontLists";
/* AdminTaopixOnlineFontLists.deletefontlists: <fusebox:set name="myFusebox['thisFuseaction']" value="deletefontlists"> */
$myFusebox['thisFuseaction'] = "deletefontlists";
/* AdminTaopixOnlineFontLists.deletefontlists: <fusebox:set name="myFusebox['thisCircuit']" value="AdminTaopixOnlineFontLists"> */
$myFusebox['thisCircuit'] = "AdminTaopixOnlineFontLists";
/* AdminTaopixOnlineFontLists.deletefontlists: <fusebox:set name="myFusebox['thisFuseaction']" value="deletefontlists"> */
$myFusebox['thisFuseaction'] = "deletefontlists";

?>