<?php

/* AdminTaopixOnlineVolumesAdmin.getgriddata: <fusebox:set name="myFusebox['thisCircuit']" value="AdminTaopixOnlineVolumesAdmin"> */
$myFusebox['thisCircuit'] = "AdminTaopixOnlineVolumesAdmin";
/* AdminTaopixOnlineVolumesAdmin.getgriddata: <fusebox:set name="myFusebox['thisFuseaction']" value="getgriddata"> */
$myFusebox['thisFuseaction'] = "getgriddata";
/* AdminTaopixOnlineVolumesAdmin.getgriddata: <fusebox:instantiate object="control" class="AdminTaopixOnlineVolumesAdmin_control"> */
include_once($application["fusebox"]["WebRootToAppRootPath"]."AdminTaopixOnlineVolumesAdmin/AdminTaopixOnlineVolumesAdmin_control.php");
$control = new AdminTaopixOnlineVolumesAdmin_control;
/* AdminTaopixOnlineVolumesAdmin.getgriddata: <fusebox:invoke object="control" methodcall="assertRequestMethod(['GET'])"> */
$control->assertRequestMethod(['GET']);
/* AdminTaopixOnlineVolumesAdmin.getgriddata: <fusebox:invoke object="control" methodcall="getGridData()"> */
$control->getGridData();
/* AdminTaopixOnlineVolumesAdmin.getgriddata: <fusebox:set name="myFusebox['thisCircuit']" value="AdminTaopixOnlineVolumesAdmin"> */
$myFusebox['thisCircuit'] = "AdminTaopixOnlineVolumesAdmin";
/* AdminTaopixOnlineVolumesAdmin.getgriddata: <fusebox:set name="myFusebox['thisFuseaction']" value="getgriddata"> */
$myFusebox['thisFuseaction'] = "getgriddata";
/* AdminTaopixOnlineVolumesAdmin.getgriddata: <fusebox:set name="myFusebox['thisCircuit']" value="AdminTaopixOnlineVolumesAdmin"> */
$myFusebox['thisCircuit'] = "AdminTaopixOnlineVolumesAdmin";
/* AdminTaopixOnlineVolumesAdmin.getgriddata: <fusebox:set name="myFusebox['thisFuseaction']" value="getgriddata"> */
$myFusebox['thisFuseaction'] = "getgriddata";

?>