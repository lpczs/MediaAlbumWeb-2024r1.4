<?php

/* AdminMetadataKeywords.initialize: <fusebox:set name="myFusebox['thisCircuit']" value="AdminMetadataKeywords"> */
$myFusebox['thisCircuit'] = "AdminMetadataKeywords";
/* AdminMetadataKeywords.initialize: <fusebox:set name="myFusebox['thisFuseaction']" value="initialize"> */
$myFusebox['thisFuseaction'] = "initialize";
/* AdminMetadataKeywords.initialize: <fusebox:instantiate object="control" class="AdminMetadataKeywords_control"> */
include_once($application["fusebox"]["WebRootToAppRootPath"]."AdminMetadataKeywords/AdminMetadataKeywords_control.php");
$control = new AdminMetadataKeywords_control;
/* AdminMetadataKeywords.initialize: <fusebox:invoke object="control" methodcall="assertRequestMethod(['GET'])"> */
$control->assertRequestMethod(['GET']);
/* AdminMetadataKeywords.initialize: <fusebox:invoke object="control" methodcall="initialize()"> */
$control->initialize();
/* AdminMetadataKeywords.initialize: <fusebox:set name="myFusebox['thisCircuit']" value="AdminMetadataKeywords"> */
$myFusebox['thisCircuit'] = "AdminMetadataKeywords";
/* AdminMetadataKeywords.initialize: <fusebox:set name="myFusebox['thisFuseaction']" value="initialize"> */
$myFusebox['thisFuseaction'] = "initialize";
/* AdminMetadataKeywords.initialize: <fusebox:set name="myFusebox['thisCircuit']" value="AdminMetadataKeywords"> */
$myFusebox['thisCircuit'] = "AdminMetadataKeywords";
/* AdminMetadataKeywords.initialize: <fusebox:set name="myFusebox['thisFuseaction']" value="initialize"> */
$myFusebox['thisFuseaction'] = "initialize";

?>