<?php

/* AdminTaopixOnlineProductURLAdmin.productURLExport: <fusebox:set name="myFusebox['thisCircuit']" value="AdminTaopixOnlineProductURLAdmin"> */
$myFusebox['thisCircuit'] = "AdminTaopixOnlineProductURLAdmin";
/* AdminTaopixOnlineProductURLAdmin.productURLExport: <fusebox:set name="myFusebox['thisFuseaction']" value="productURLExport"> */
$myFusebox['thisFuseaction'] = "productURLExport";
/* AdminTaopixOnlineProductURLAdmin.productURLExport: <fusebox:instantiate object="control" class="AdminTaopixOnlineProductURLAdmin_control"> */
include_once($application["fusebox"]["WebRootToAppRootPath"]."AdminTaopixOnlineProductURLAdmin/AdminTaopixOnlineProductURLAdmin_control.php");
$control = new AdminTaopixOnlineProductURLAdmin_control;
/* AdminTaopixOnlineProductURLAdmin.productURLExport: <fusebox:invoke object="control" methodcall="assertRequestMethod(['GET'])"> */
$control->assertRequestMethod(['GET']);
/* AdminTaopixOnlineProductURLAdmin.productURLExport: <fusebox:invoke object="control" methodcall="productURLExport()"> */
$control->productURLExport();
/* AdminTaopixOnlineProductURLAdmin.productURLExport: <fusebox:set name="myFusebox['thisCircuit']" value="AdminTaopixOnlineProductURLAdmin"> */
$myFusebox['thisCircuit'] = "AdminTaopixOnlineProductURLAdmin";
/* AdminTaopixOnlineProductURLAdmin.productURLExport: <fusebox:set name="myFusebox['thisFuseaction']" value="productURLExport"> */
$myFusebox['thisFuseaction'] = "productURLExport";
/* AdminTaopixOnlineProductURLAdmin.productURLExport: <fusebox:set name="myFusebox['thisCircuit']" value="AdminTaopixOnlineProductURLAdmin"> */
$myFusebox['thisCircuit'] = "AdminTaopixOnlineProductURLAdmin";
/* AdminTaopixOnlineProductURLAdmin.productURLExport: <fusebox:set name="myFusebox['thisFuseaction']" value="productURLExport"> */
$myFusebox['thisFuseaction'] = "productURLExport";

?>