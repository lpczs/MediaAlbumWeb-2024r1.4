<?php

/* AdminAutoUpdate.initializeScrapbookPictures: <fusebox:set name="myFusebox['thisCircuit']" value="AdminAutoUpdate"> */
$myFusebox['thisCircuit'] = "AdminAutoUpdate";
/* AdminAutoUpdate.initializeScrapbookPictures: <fusebox:set name="myFusebox['thisFuseaction']" value="initializeScrapbookPictures"> */
$myFusebox['thisFuseaction'] = "initializeScrapbookPictures";
/* AdminAutoUpdate.initializeScrapbookPictures: <fusebox:instantiate object="control" class="AdminAutoUpdate_control"> */
include_once($application["fusebox"]["WebRootToAppRootPath"]."AdminAutoUpdate/AdminAutoUpdate_control.php");
$control = new AdminAutoUpdate_control;
/* AdminAutoUpdate.initializeScrapbookPictures: <fusebox:invoke object="control" methodcall="assertRequestMethod(['GET'])"> */
$control->assertRequestMethod(['GET']);
/* AdminAutoUpdate.initializeScrapbookPictures: <fusebox:invoke object="control" methodcall="initializeScrapbookPictures()"> */
$control->initializeScrapbookPictures();
/* AdminAutoUpdate.initializeScrapbookPictures: <fusebox:set name="myFusebox['thisCircuit']" value="AdminAutoUpdate"> */
$myFusebox['thisCircuit'] = "AdminAutoUpdate";
/* AdminAutoUpdate.initializeScrapbookPictures: <fusebox:set name="myFusebox['thisFuseaction']" value="initializeScrapbookPictures"> */
$myFusebox['thisFuseaction'] = "initializeScrapbookPictures";
/* AdminAutoUpdate.initializeScrapbookPictures: <fusebox:set name="myFusebox['thisCircuit']" value="AdminAutoUpdate"> */
$myFusebox['thisCircuit'] = "AdminAutoUpdate";
/* AdminAutoUpdate.initializeScrapbookPictures: <fusebox:set name="myFusebox['thisFuseaction']" value="initializeScrapbookPictures"> */
$myFusebox['thisFuseaction'] = "initializeScrapbookPictures";

?>