<?php

/* AdminMetadataKeywordsGroups.initialize: <fusebox:set name="myFusebox['thisCircuit']" value="AdminMetadataKeywordsGroups"> */
$myFusebox['thisCircuit'] = "AdminMetadataKeywordsGroups";
/* AdminMetadataKeywordsGroups.initialize: <fusebox:set name="myFusebox['thisFuseaction']" value="initialize"> */
$myFusebox['thisFuseaction'] = "initialize";
/* AdminMetadataKeywordsGroups.initialize: <fusebox:instantiate object="control" class="AdminMetadataKeywordsGroups_control"> */
include_once($application["fusebox"]["WebRootToAppRootPath"]."AdminMetadataKeywordsGroups/AdminMetadataKeywordsGroups_control.php");
$control = new AdminMetadataKeywordsGroups_control;
/* AdminMetadataKeywordsGroups.initialize: <fusebox:invoke object="control" methodcall="assertRequestMethod(['GET'])"> */
$control->assertRequestMethod(['GET']);
/* AdminMetadataKeywordsGroups.initialize: <fusebox:invoke object="control" methodcall="initialize()"> */
$control->initialize();
/* AdminMetadataKeywordsGroups.initialize: <fusebox:set name="myFusebox['thisCircuit']" value="AdminMetadataKeywordsGroups"> */
$myFusebox['thisCircuit'] = "AdminMetadataKeywordsGroups";
/* AdminMetadataKeywordsGroups.initialize: <fusebox:set name="myFusebox['thisFuseaction']" value="initialize"> */
$myFusebox['thisFuseaction'] = "initialize";
/* AdminMetadataKeywordsGroups.initialize: <fusebox:set name="myFusebox['thisCircuit']" value="AdminMetadataKeywordsGroups"> */
$myFusebox['thisCircuit'] = "AdminMetadataKeywordsGroups";
/* AdminMetadataKeywordsGroups.initialize: <fusebox:set name="myFusebox['thisFuseaction']" value="initialize"> */
$myFusebox['thisFuseaction'] = "initialize";

?>