<?php

/* Admin3DPreview.addDisplay: <fusebox:set name="myFusebox['thisCircuit']" value="Admin3DPreview"> */
$myFusebox['thisCircuit'] = "Admin3DPreview";
/* Admin3DPreview.addDisplay: <fusebox:set name="myFusebox['thisFuseaction']" value="addDisplay"> */
$myFusebox['thisFuseaction'] = "addDisplay";
/* Admin3DPreview.addDisplay: <fusebox:instantiate object="control" class="Admin3DPreview_control"> */
include_once($application["fusebox"]["WebRootToAppRootPath"]."Admin3DPreview/Admin3DPreview_control.php");
$control = new Admin3DPreview_control;
/* Admin3DPreview.addDisplay: <fusebox:invoke object="control" methodcall="assertRequestMethod(['GET'])"> */
$control->assertRequestMethod(['GET']);
/* Admin3DPreview.addDisplay: <fusebox:invoke object="control" methodcall="addDisplay()"> */
$control->addDisplay();
/* Admin3DPreview.addDisplay: <fusebox:set name="myFusebox['thisCircuit']" value="Admin3DPreview"> */
$myFusebox['thisCircuit'] = "Admin3DPreview";
/* Admin3DPreview.addDisplay: <fusebox:set name="myFusebox['thisFuseaction']" value="addDisplay"> */
$myFusebox['thisFuseaction'] = "addDisplay";
/* Admin3DPreview.addDisplay: <fusebox:set name="myFusebox['thisCircuit']" value="Admin3DPreview"> */
$myFusebox['thisCircuit'] = "Admin3DPreview";
/* Admin3DPreview.addDisplay: <fusebox:set name="myFusebox['thisFuseaction']" value="addDisplay"> */
$myFusebox['thisFuseaction'] = "addDisplay";

?>