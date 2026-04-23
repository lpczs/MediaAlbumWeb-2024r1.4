<?php

/* Admin3DPreview.deleteModel: <fusebox:set name="myFusebox['thisCircuit']" value="Admin3DPreview"> */
$myFusebox['thisCircuit'] = "Admin3DPreview";
/* Admin3DPreview.deleteModel: <fusebox:set name="myFusebox['thisFuseaction']" value="deleteModel"> */
$myFusebox['thisFuseaction'] = "deleteModel";
/* Admin3DPreview.deleteModel: <fusebox:instantiate object="control" class="Admin3DPreview_control"> */
include_once($application["fusebox"]["WebRootToAppRootPath"]."Admin3DPreview/Admin3DPreview_control.php");
$control = new Admin3DPreview_control;
/* Admin3DPreview.deleteModel: <fusebox:invoke object="control" methodcall="assertRequestMethod(['POST'])"> */
$control->assertRequestMethod(['POST']);
/* Admin3DPreview.deleteModel: <fusebox:invoke object="control" methodcall="assertCsrfToken()"> */
$control->assertCsrfToken();
/* Admin3DPreview.deleteModel: <fusebox:invoke object="control" methodcall="deleteModel()"> */
$control->deleteModel();
/* Admin3DPreview.deleteModel: <fusebox:set name="myFusebox['thisCircuit']" value="Admin3DPreview"> */
$myFusebox['thisCircuit'] = "Admin3DPreview";
/* Admin3DPreview.deleteModel: <fusebox:set name="myFusebox['thisFuseaction']" value="deleteModel"> */
$myFusebox['thisFuseaction'] = "deleteModel";
/* Admin3DPreview.deleteModel: <fusebox:set name="myFusebox['thisCircuit']" value="Admin3DPreview"> */
$myFusebox['thisCircuit'] = "Admin3DPreview";
/* Admin3DPreview.deleteModel: <fusebox:set name="myFusebox['thisFuseaction']" value="deleteModel"> */
$myFusebox['thisFuseaction'] = "deleteModel";

?>