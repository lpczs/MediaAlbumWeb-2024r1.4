<?php

/* Admin3DPreview.modelListGrid: <fusebox:set name="myFusebox['thisCircuit']" value="Admin3DPreview"> */
$myFusebox['thisCircuit'] = "Admin3DPreview";
/* Admin3DPreview.modelListGrid: <fusebox:set name="myFusebox['thisFuseaction']" value="modelListGrid"> */
$myFusebox['thisFuseaction'] = "modelListGrid";
/* Admin3DPreview.modelListGrid: <fusebox:instantiate object="control" class="Admin3DPreview_control"> */
include_once($application["fusebox"]["WebRootToAppRootPath"]."Admin3DPreview/Admin3DPreview_control.php");
$control = new Admin3DPreview_control;
/* Admin3DPreview.modelListGrid: <fusebox:invoke object="control" methodcall="assertRequestMethod(['GET'])"> */
$control->assertRequestMethod(['GET']);
/* Admin3DPreview.modelListGrid: <fusebox:invoke object="control" methodcall="modelListGrid()"> */
$control->modelListGrid();
/* Admin3DPreview.modelListGrid: <fusebox:set name="myFusebox['thisCircuit']" value="Admin3DPreview"> */
$myFusebox['thisCircuit'] = "Admin3DPreview";
/* Admin3DPreview.modelListGrid: <fusebox:set name="myFusebox['thisFuseaction']" value="modelListGrid"> */
$myFusebox['thisFuseaction'] = "modelListGrid";
/* Admin3DPreview.modelListGrid: <fusebox:set name="myFusebox['thisCircuit']" value="Admin3DPreview"> */
$myFusebox['thisCircuit'] = "Admin3DPreview";
/* Admin3DPreview.modelListGrid: <fusebox:set name="myFusebox['thisFuseaction']" value="modelListGrid"> */
$myFusebox['thisFuseaction'] = "modelListGrid";

?>