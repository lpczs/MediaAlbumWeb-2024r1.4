<?php

/* AdminAutoUpdate.getPromoPanelImage: <fusebox:set name="myFusebox['thisCircuit']" value="AdminAutoUpdate"> */
$myFusebox['thisCircuit'] = "AdminAutoUpdate";
/* AdminAutoUpdate.getPromoPanelImage: <fusebox:set name="myFusebox['thisFuseaction']" value="getPromoPanelImage"> */
$myFusebox['thisFuseaction'] = "getPromoPanelImage";
/* AdminAutoUpdate.getPromoPanelImage: <fusebox:instantiate object="control" class="AdminAutoUpdate_control"> */
include_once($application["fusebox"]["WebRootToAppRootPath"]."AdminAutoUpdate/AdminAutoUpdate_control.php");
$control = new AdminAutoUpdate_control;
/* AdminAutoUpdate.getPromoPanelImage: <fusebox:invoke object="control" methodcall="assertRequestMethod(['GET'])"> */
$control->assertRequestMethod(['GET']);
/* AdminAutoUpdate.getPromoPanelImage: <fusebox:invoke object="control" methodcall="getPromoPanelImage()"> */
$control->getPromoPanelImage();
/* AdminAutoUpdate.getPromoPanelImage: <fusebox:set name="myFusebox['thisCircuit']" value="AdminAutoUpdate"> */
$myFusebox['thisCircuit'] = "AdminAutoUpdate";
/* AdminAutoUpdate.getPromoPanelImage: <fusebox:set name="myFusebox['thisFuseaction']" value="getPromoPanelImage"> */
$myFusebox['thisFuseaction'] = "getPromoPanelImage";
/* AdminAutoUpdate.getPromoPanelImage: <fusebox:set name="myFusebox['thisCircuit']" value="AdminAutoUpdate"> */
$myFusebox['thisCircuit'] = "AdminAutoUpdate";
/* AdminAutoUpdate.getPromoPanelImage: <fusebox:set name="myFusebox['thisFuseaction']" value="getPromoPanelImage"> */
$myFusebox['thisFuseaction'] = "getPromoPanelImage";

?>