<?php

/* Admin3DPreview.get3DModelList: <fusebox:set name="myFusebox['thisCircuit']" value="Admin3DPreview"> */
$myFusebox['thisCircuit'] = "Admin3DPreview";
/* Admin3DPreview.get3DModelList: <fusebox:set name="myFusebox['thisFuseaction']" value="get3DModelList"> */
$myFusebox['thisFuseaction'] = "get3DModelList";
/* Admin3DPreview.get3DModelList: <fusebox:instantiate object="control" class="Admin3DPreview_control"> */
include_once($application["fusebox"]["WebRootToAppRootPath"]."Admin3DPreview/Admin3DPreview_control.php");
$control = new Admin3DPreview_control;
/* Admin3DPreview.get3DModelList: <fusebox:invoke object="control" methodcall="assertRequestMethod(['GET'])"> */
$control->assertRequestMethod(['GET']);
/* Admin3DPreview.get3DModelList: <fusebox:invoke object="control" methodcall="get3DModelList()"> */
$control->get3DModelList();
/* Admin3DPreview.get3DModelList: <fusebox:set name="myFusebox['thisCircuit']" value="Admin3DPreview"> */
$myFusebox['thisCircuit'] = "Admin3DPreview";
/* Admin3DPreview.get3DModelList: <fusebox:set name="myFusebox['thisFuseaction']" value="get3DModelList"> */
$myFusebox['thisFuseaction'] = "get3DModelList";
/* Admin3DPreview.get3DModelList: <fusebox:set name="myFusebox['thisCircuit']" value="Admin3DPreview"> */
$myFusebox['thisCircuit'] = "Admin3DPreview";
/* Admin3DPreview.get3DModelList: <fusebox:set name="myFusebox['thisFuseaction']" value="get3DModelList"> */
$myFusebox['thisFuseaction'] = "get3DModelList";

?>