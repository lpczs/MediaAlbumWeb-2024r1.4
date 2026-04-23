<?php

/* Admin3DPreview.setModelActivateStatus: <fusebox:set name="myFusebox['thisCircuit']" value="Admin3DPreview"> */
$myFusebox['thisCircuit'] = "Admin3DPreview";
/* Admin3DPreview.setModelActivateStatus: <fusebox:set name="myFusebox['thisFuseaction']" value="setModelActivateStatus"> */
$myFusebox['thisFuseaction'] = "setModelActivateStatus";
/* Admin3DPreview.setModelActivateStatus: <fusebox:instantiate object="control" class="Admin3DPreview_control"> */
include_once($application["fusebox"]["WebRootToAppRootPath"]."Admin3DPreview/Admin3DPreview_control.php");
$control = new Admin3DPreview_control;
/* Admin3DPreview.setModelActivateStatus: <fusebox:invoke object="control" methodcall="assertRequestMethod(['POST'])"> */
$control->assertRequestMethod(['POST']);
/* Admin3DPreview.setModelActivateStatus: <fusebox:invoke object="control" methodcall="assertCsrfToken()"> */
$control->assertCsrfToken();
/* Admin3DPreview.setModelActivateStatus: <fusebox:invoke object="control" methodcall="setModelActivateStatus()"> */
$control->setModelActivateStatus();
/* Admin3DPreview.setModelActivateStatus: <fusebox:set name="myFusebox['thisCircuit']" value="Admin3DPreview"> */
$myFusebox['thisCircuit'] = "Admin3DPreview";
/* Admin3DPreview.setModelActivateStatus: <fusebox:set name="myFusebox['thisFuseaction']" value="setModelActivateStatus"> */
$myFusebox['thisFuseaction'] = "setModelActivateStatus";
/* Admin3DPreview.setModelActivateStatus: <fusebox:set name="myFusebox['thisCircuit']" value="Admin3DPreview"> */
$myFusebox['thisCircuit'] = "Admin3DPreview";
/* Admin3DPreview.setModelActivateStatus: <fusebox:set name="myFusebox['thisFuseaction']" value="setModelActivateStatus"> */
$myFusebox['thisFuseaction'] = "setModelActivateStatus";

?>