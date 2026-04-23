<?php

/* AdminTaopixOnlineImageServersAdmin.initialize: <fusebox:set name="myFusebox['thisCircuit']" value="AdminTaopixOnlineImageServersAdmin"> */
$myFusebox['thisCircuit'] = "AdminTaopixOnlineImageServersAdmin";
/* AdminTaopixOnlineImageServersAdmin.initialize: <fusebox:set name="myFusebox['thisFuseaction']" value="initialize"> */
$myFusebox['thisFuseaction'] = "initialize";
/* AdminTaopixOnlineImageServersAdmin.initialize: <fusebox:instantiate object="control" class="AdminTaopixOnlineImageServersAdmin_control"> */
include_once($application["fusebox"]["WebRootToAppRootPath"]."AdminTaopixOnlineImageServersAdmin/AdminTaopixOnlineImageServersAdmin_control.php");
$control = new AdminTaopixOnlineImageServersAdmin_control;
/* AdminTaopixOnlineImageServersAdmin.initialize: <fusebox:invoke object="control" methodcall="assertRequestMethod(['GET'])"> */
$control->assertRequestMethod(['GET']);
/* AdminTaopixOnlineImageServersAdmin.initialize: <fusebox:invoke object="control" methodcall="initialize()"> */
$control->initialize();
/* AdminTaopixOnlineImageServersAdmin.initialize: <fusebox:set name="myFusebox['thisCircuit']" value="AdminTaopixOnlineImageServersAdmin"> */
$myFusebox['thisCircuit'] = "AdminTaopixOnlineImageServersAdmin";
/* AdminTaopixOnlineImageServersAdmin.initialize: <fusebox:set name="myFusebox['thisFuseaction']" value="initialize"> */
$myFusebox['thisFuseaction'] = "initialize";
/* AdminTaopixOnlineImageServersAdmin.initialize: <fusebox:set name="myFusebox['thisCircuit']" value="AdminTaopixOnlineImageServersAdmin"> */
$myFusebox['thisCircuit'] = "AdminTaopixOnlineImageServersAdmin";
/* AdminTaopixOnlineImageServersAdmin.initialize: <fusebox:set name="myFusebox['thisFuseaction']" value="initialize"> */
$myFusebox['thisFuseaction'] = "initialize";

?>