<?php

/* Admin3DPreview.upload3DPreviewModel: <fusebox:set name="myFusebox['thisCircuit']" value="Admin3DPreview"> */
$myFusebox['thisCircuit'] = "Admin3DPreview";
/* Admin3DPreview.upload3DPreviewModel: <fusebox:set name="myFusebox['thisFuseaction']" value="upload3DPreviewModel"> */
$myFusebox['thisFuseaction'] = "upload3DPreviewModel";
/* Admin3DPreview.upload3DPreviewModel: <fusebox:instantiate object="control" class="Admin3DPreview_control"> */
include_once($application["fusebox"]["WebRootToAppRootPath"]."Admin3DPreview/Admin3DPreview_control.php");
$control = new Admin3DPreview_control;
/* Admin3DPreview.upload3DPreviewModel: <fusebox:invoke object="control" methodcall="assertRequestMethod(['POST'])"> */
$control->assertRequestMethod(['POST']);
/* Admin3DPreview.upload3DPreviewModel: <fusebox:invoke object="control" methodcall="assertCsrfToken()"> */
$control->assertCsrfToken();
/* Admin3DPreview.upload3DPreviewModel: <fusebox:invoke object="control" methodcall="upload3DPreviewModel()"> */
$control->upload3DPreviewModel();
/* Admin3DPreview.upload3DPreviewModel: <fusebox:set name="myFusebox['thisCircuit']" value="Admin3DPreview"> */
$myFusebox['thisCircuit'] = "Admin3DPreview";
/* Admin3DPreview.upload3DPreviewModel: <fusebox:set name="myFusebox['thisFuseaction']" value="upload3DPreviewModel"> */
$myFusebox['thisFuseaction'] = "upload3DPreviewModel";
/* Admin3DPreview.upload3DPreviewModel: <fusebox:set name="myFusebox['thisCircuit']" value="Admin3DPreview"> */
$myFusebox['thisCircuit'] = "Admin3DPreview";
/* Admin3DPreview.upload3DPreviewModel: <fusebox:set name="myFusebox['thisFuseaction']" value="upload3DPreviewModel"> */
$myFusebox['thisFuseaction'] = "upload3DPreviewModel";

?>