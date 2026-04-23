<?php

/* AdminTaopixOnlineVolumesAdmin.initialize: <fusebox:set name="myFusebox['thisCircuit']" value="AdminTaopixOnlineVolumesAdmin"> */
$myFusebox['thisCircuit'] = "AdminTaopixOnlineVolumesAdmin";
/* AdminTaopixOnlineVolumesAdmin.initialize: <fusebox:set name="myFusebox['thisFuseaction']" value="initialize"> */
$myFusebox['thisFuseaction'] = "initialize";
/* AdminTaopixOnlineVolumesAdmin.initialize: <fusebox:instantiate object="control" class="AdminTaopixOnlineVolumesAdmin_control"> */
include_once($application["fusebox"]["WebRootToAppRootPath"]."AdminTaopixOnlineVolumesAdmin/AdminTaopixOnlineVolumesAdmin_control.php");
$control = new AdminTaopixOnlineVolumesAdmin_control;
/* AdminTaopixOnlineVolumesAdmin.initialize: <fusebox:invoke object="control" methodcall="assertRequestMethod(['GET'])"> */
$control->assertRequestMethod(['GET']);
/* AdminTaopixOnlineVolumesAdmin.initialize: <fusebox:invoke object="control" methodcall="initialize()"> */
$control->initialize();
/* AdminTaopixOnlineVolumesAdmin.initialize: <fusebox:set name="myFusebox['thisCircuit']" value="AdminTaopixOnlineVolumesAdmin"> */
$myFusebox['thisCircuit'] = "AdminTaopixOnlineVolumesAdmin";
/* AdminTaopixOnlineVolumesAdmin.initialize: <fusebox:set name="myFusebox['thisFuseaction']" value="initialize"> */
$myFusebox['thisFuseaction'] = "initialize";
/* AdminTaopixOnlineVolumesAdmin.initialize: <fusebox:set name="myFusebox['thisCircuit']" value="AdminTaopixOnlineVolumesAdmin"> */
$myFusebox['thisCircuit'] = "AdminTaopixOnlineVolumesAdmin";
/* AdminTaopixOnlineVolumesAdmin.initialize: <fusebox:set name="myFusebox['thisFuseaction']" value="initialize"> */
$myFusebox['thisFuseaction'] = "initialize";

?>