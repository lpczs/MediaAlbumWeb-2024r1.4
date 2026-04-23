<?php

/* AdminTaopixOnlineProductURLAdmin.getGridData: <fusebox:set name="myFusebox['thisCircuit']" value="AdminTaopixOnlineProductURLAdmin"> */
$myFusebox['thisCircuit'] = "AdminTaopixOnlineProductURLAdmin";
/* AdminTaopixOnlineProductURLAdmin.getGridData: <fusebox:set name="myFusebox['thisFuseaction']" value="getGridData"> */
$myFusebox['thisFuseaction'] = "getGridData";
/* AdminTaopixOnlineProductURLAdmin.getGridData: <fusebox:instantiate object="control" class="AdminTaopixOnlineProductURLAdmin_control"> */
include_once($application["fusebox"]["WebRootToAppRootPath"]."AdminTaopixOnlineProductURLAdmin/AdminTaopixOnlineProductURLAdmin_control.php");
$control = new AdminTaopixOnlineProductURLAdmin_control;
/* AdminTaopixOnlineProductURLAdmin.getGridData: <fusebox:invoke object="control" methodcall="assertRequestMethod(['POST'])"> */
$control->assertRequestMethod(['POST']);
/* AdminTaopixOnlineProductURLAdmin.getGridData: <fusebox:invoke object="control" methodcall="assertCsrfToken()"> */
$control->assertCsrfToken();
/* AdminTaopixOnlineProductURLAdmin.getGridData: <fusebox:invoke object="control" methodcall="getGridData()"> */
$control->getGridData();
/* AdminTaopixOnlineProductURLAdmin.getGridData: <fusebox:set name="myFusebox['thisCircuit']" value="AdminTaopixOnlineProductURLAdmin"> */
$myFusebox['thisCircuit'] = "AdminTaopixOnlineProductURLAdmin";
/* AdminTaopixOnlineProductURLAdmin.getGridData: <fusebox:set name="myFusebox['thisFuseaction']" value="getGridData"> */
$myFusebox['thisFuseaction'] = "getGridData";
/* AdminTaopixOnlineProductURLAdmin.getGridData: <fusebox:set name="myFusebox['thisCircuit']" value="AdminTaopixOnlineProductURLAdmin"> */
$myFusebox['thisCircuit'] = "AdminTaopixOnlineProductURLAdmin";
/* AdminTaopixOnlineProductURLAdmin.getGridData: <fusebox:set name="myFusebox['thisFuseaction']" value="getGridData"> */
$myFusebox['thisFuseaction'] = "getGridData";

?>