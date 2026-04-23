<?php

/* AdminAutoUpdate.getBannerImage: <fusebox:set name="myFusebox['thisCircuit']" value="AdminAutoUpdate"> */
$myFusebox['thisCircuit'] = "AdminAutoUpdate";
/* AdminAutoUpdate.getBannerImage: <fusebox:set name="myFusebox['thisFuseaction']" value="getBannerImage"> */
$myFusebox['thisFuseaction'] = "getBannerImage";
/* AdminAutoUpdate.getBannerImage: <fusebox:instantiate object="control" class="AdminAutoUpdate_control"> */
include_once($application["fusebox"]["WebRootToAppRootPath"]."AdminAutoUpdate/AdminAutoUpdate_control.php");
$control = new AdminAutoUpdate_control;
/* AdminAutoUpdate.getBannerImage: <fusebox:invoke object="control" methodcall="assertRequestMethod(['GET'])"> */
$control->assertRequestMethod(['GET']);
/* AdminAutoUpdate.getBannerImage: <fusebox:invoke object="control" methodcall="getBannerImage() "> */
$control->getBannerImage() ;
/* AdminAutoUpdate.getBannerImage: <fusebox:set name="myFusebox['thisCircuit']" value="AdminAutoUpdate"> */
$myFusebox['thisCircuit'] = "AdminAutoUpdate";
/* AdminAutoUpdate.getBannerImage: <fusebox:set name="myFusebox['thisFuseaction']" value="getBannerImage"> */
$myFusebox['thisFuseaction'] = "getBannerImage";
/* AdminAutoUpdate.getBannerImage: <fusebox:set name="myFusebox['thisCircuit']" value="AdminAutoUpdate"> */
$myFusebox['thisCircuit'] = "AdminAutoUpdate";
/* AdminAutoUpdate.getBannerImage: <fusebox:set name="myFusebox['thisFuseaction']" value="getBannerImage"> */
$myFusebox['thisFuseaction'] = "getBannerImage";

?>