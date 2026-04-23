<?php

/* AdminTaopixOnlineImageServersAdmin.getgriddata: <fusebox:set name="myFusebox['thisCircuit']" value="AdminTaopixOnlineImageServersAdmin"> */
$myFusebox['thisCircuit'] = "AdminTaopixOnlineImageServersAdmin";
/* AdminTaopixOnlineImageServersAdmin.getgriddata: <fusebox:set name="myFusebox['thisFuseaction']" value="getgriddata"> */
$myFusebox['thisFuseaction'] = "getgriddata";
/* AdminTaopixOnlineImageServersAdmin.getgriddata: <fusebox:instantiate object="control" class="AdminTaopixOnlineImageServersAdmin_control"> */
include_once($application["fusebox"]["WebRootToAppRootPath"]."AdminTaopixOnlineImageServersAdmin/AdminTaopixOnlineImageServersAdmin_control.php");
$control = new AdminTaopixOnlineImageServersAdmin_control;
/* AdminTaopixOnlineImageServersAdmin.getgriddata: <fusebox:invoke object="control" methodcall="assertRequestMethod(['GET'])"> */
$control->assertRequestMethod(['GET']);
/* AdminTaopixOnlineImageServersAdmin.getgriddata: <fusebox:invoke object="control" methodcall="getGridData()"> */
$control->getGridData();
/* AdminTaopixOnlineImageServersAdmin.getgriddata: <fusebox:set name="myFusebox['thisCircuit']" value="AdminTaopixOnlineImageServersAdmin"> */
$myFusebox['thisCircuit'] = "AdminTaopixOnlineImageServersAdmin";
/* AdminTaopixOnlineImageServersAdmin.getgriddata: <fusebox:set name="myFusebox['thisFuseaction']" value="getgriddata"> */
$myFusebox['thisFuseaction'] = "getgriddata";
/* AdminTaopixOnlineImageServersAdmin.getgriddata: <fusebox:set name="myFusebox['thisCircuit']" value="AdminTaopixOnlineImageServersAdmin"> */
$myFusebox['thisCircuit'] = "AdminTaopixOnlineImageServersAdmin";
/* AdminTaopixOnlineImageServersAdmin.getgriddata: <fusebox:set name="myFusebox['thisFuseaction']" value="getgriddata"> */
$myFusebox['thisFuseaction'] = "getgriddata";

?>