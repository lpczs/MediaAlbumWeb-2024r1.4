<?php

/* Admin3DPreview.unLink3DPreviewModelToProducts: <fusebox:set name="myFusebox['thisCircuit']" value="Admin3DPreview"> */
$myFusebox['thisCircuit'] = "Admin3DPreview";
/* Admin3DPreview.unLink3DPreviewModelToProducts: <fusebox:set name="myFusebox['thisFuseaction']" value="unLink3DPreviewModelToProducts"> */
$myFusebox['thisFuseaction'] = "unLink3DPreviewModelToProducts";
/* Admin3DPreview.unLink3DPreviewModelToProducts: <fusebox:instantiate object="control" class="Admin3DPreview_control"> */
include_once($application["fusebox"]["WebRootToAppRootPath"]."Admin3DPreview/Admin3DPreview_control.php");
$control = new Admin3DPreview_control;
/* Admin3DPreview.unLink3DPreviewModelToProducts: <fusebox:invoke object="control" methodcall="assertRequestMethod(['POST'])"> */
$control->assertRequestMethod(['POST']);
/* Admin3DPreview.unLink3DPreviewModelToProducts: <fusebox:invoke object="control" methodcall="assertCsrfToken()"> */
$control->assertCsrfToken();
/* Admin3DPreview.unLink3DPreviewModelToProducts: <fusebox:invoke object="control" methodcall="unLink3DPreviewModelToProducts()"> */
$control->unLink3DPreviewModelToProducts();
/* Admin3DPreview.unLink3DPreviewModelToProducts: <fusebox:set name="myFusebox['thisCircuit']" value="Admin3DPreview"> */
$myFusebox['thisCircuit'] = "Admin3DPreview";
/* Admin3DPreview.unLink3DPreviewModelToProducts: <fusebox:set name="myFusebox['thisFuseaction']" value="unLink3DPreviewModelToProducts"> */
$myFusebox['thisFuseaction'] = "unLink3DPreviewModelToProducts";
/* Admin3DPreview.unLink3DPreviewModelToProducts: <fusebox:set name="myFusebox['thisCircuit']" value="Admin3DPreview"> */
$myFusebox['thisCircuit'] = "Admin3DPreview";
/* Admin3DPreview.unLink3DPreviewModelToProducts: <fusebox:set name="myFusebox['thisFuseaction']" value="unLink3DPreviewModelToProducts"> */
$myFusebox['thisFuseaction'] = "unLink3DPreviewModelToProducts";

?>