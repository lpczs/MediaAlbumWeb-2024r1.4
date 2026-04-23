<?php

/* Admin3DPreview.link3DPreviewModelToProducts: <fusebox:set name="myFusebox['thisCircuit']" value="Admin3DPreview"> */
$myFusebox['thisCircuit'] = "Admin3DPreview";
/* Admin3DPreview.link3DPreviewModelToProducts: <fusebox:set name="myFusebox['thisFuseaction']" value="link3DPreviewModelToProducts"> */
$myFusebox['thisFuseaction'] = "link3DPreviewModelToProducts";
/* Admin3DPreview.link3DPreviewModelToProducts: <fusebox:instantiate object="control" class="Admin3DPreview_control"> */
include_once($application["fusebox"]["WebRootToAppRootPath"]."Admin3DPreview/Admin3DPreview_control.php");
$control = new Admin3DPreview_control;
/* Admin3DPreview.link3DPreviewModelToProducts: <fusebox:invoke object="control" methodcall="assertRequestMethod(['POST'])"> */
$control->assertRequestMethod(['POST']);
/* Admin3DPreview.link3DPreviewModelToProducts: <fusebox:invoke object="control" methodcall="assertCsrfToken()"> */
$control->assertCsrfToken();
/* Admin3DPreview.link3DPreviewModelToProducts: <fusebox:invoke object="control" methodcall="link3DPreviewModelToProducts()"> */
$control->link3DPreviewModelToProducts();
/* Admin3DPreview.link3DPreviewModelToProducts: <fusebox:set name="myFusebox['thisCircuit']" value="Admin3DPreview"> */
$myFusebox['thisCircuit'] = "Admin3DPreview";
/* Admin3DPreview.link3DPreviewModelToProducts: <fusebox:set name="myFusebox['thisFuseaction']" value="link3DPreviewModelToProducts"> */
$myFusebox['thisFuseaction'] = "link3DPreviewModelToProducts";
/* Admin3DPreview.link3DPreviewModelToProducts: <fusebox:set name="myFusebox['thisCircuit']" value="Admin3DPreview"> */
$myFusebox['thisCircuit'] = "Admin3DPreview";
/* Admin3DPreview.link3DPreviewModelToProducts: <fusebox:set name="myFusebox['thisFuseaction']" value="link3DPreviewModelToProducts"> */
$myFusebox['thisFuseaction'] = "link3DPreviewModelToProducts";

?>