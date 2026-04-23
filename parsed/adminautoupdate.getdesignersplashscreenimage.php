<?php

/* AdminAutoUpdate.getDesignerSplashScreenImage: <fusebox:set name="myFusebox['thisCircuit']" value="AdminAutoUpdate"> */
$myFusebox['thisCircuit'] = "AdminAutoUpdate";
/* AdminAutoUpdate.getDesignerSplashScreenImage: <fusebox:set name="myFusebox['thisFuseaction']" value="getDesignerSplashScreenImage"> */
$myFusebox['thisFuseaction'] = "getDesignerSplashScreenImage";
/* AdminAutoUpdate.getDesignerSplashScreenImage: <fusebox:instantiate object="control" class="AdminAutoUpdate_control"> */
include_once($application["fusebox"]["WebRootToAppRootPath"]."AdminAutoUpdate/AdminAutoUpdate_control.php");
$control = new AdminAutoUpdate_control;
/* AdminAutoUpdate.getDesignerSplashScreenImage: <fusebox:invoke object="control" methodcall="assertRequestMethod(['GET'])"> */
$control->assertRequestMethod(['GET']);
/* AdminAutoUpdate.getDesignerSplashScreenImage: <fusebox:invoke object="control" methodcall="getDesignerSplashScreenImage() "> */
$control->getDesignerSplashScreenImage() ;
/* AdminAutoUpdate.getDesignerSplashScreenImage: <fusebox:set name="myFusebox['thisCircuit']" value="AdminAutoUpdate"> */
$myFusebox['thisCircuit'] = "AdminAutoUpdate";
/* AdminAutoUpdate.getDesignerSplashScreenImage: <fusebox:set name="myFusebox['thisFuseaction']" value="getDesignerSplashScreenImage"> */
$myFusebox['thisFuseaction'] = "getDesignerSplashScreenImage";
/* AdminAutoUpdate.getDesignerSplashScreenImage: <fusebox:set name="myFusebox['thisCircuit']" value="AdminAutoUpdate"> */
$myFusebox['thisCircuit'] = "AdminAutoUpdate";
/* AdminAutoUpdate.getDesignerSplashScreenImage: <fusebox:set name="myFusebox['thisFuseaction']" value="getDesignerSplashScreenImage"> */
$myFusebox['thisFuseaction'] = "getDesignerSplashScreenImage";

?>