<?php

/* AdminMetadataKeywordsGroups.getGridData: <fusebox:set name="myFusebox['thisCircuit']" value="AdminMetadataKeywordsGroups"> */
$myFusebox['thisCircuit'] = "AdminMetadataKeywordsGroups";
/* AdminMetadataKeywordsGroups.getGridData: <fusebox:set name="myFusebox['thisFuseaction']" value="getGridData"> */
$myFusebox['thisFuseaction'] = "getGridData";
/* AdminMetadataKeywordsGroups.getGridData: <fusebox:instantiate object="control" class="AdminMetadataKeywordsGroups_control"> */
include_once($application["fusebox"]["WebRootToAppRootPath"]."AdminMetadataKeywordsGroups/AdminMetadataKeywordsGroups_control.php");
$control = new AdminMetadataKeywordsGroups_control;
/* AdminMetadataKeywordsGroups.getGridData: <fusebox:invoke object="control" methodcall="assertRequestMethod(['POST'])"> */
$control->assertRequestMethod(['POST']);
/* AdminMetadataKeywordsGroups.getGridData: <fusebox:invoke object="control" methodcall="assertCsrfToken()"> */
$control->assertCsrfToken();
/* AdminMetadataKeywordsGroups.getGridData: <fusebox:invoke object="control" methodcall="getGridData()"> */
$control->getGridData();
/* AdminMetadataKeywordsGroups.getGridData: <fusebox:set name="myFusebox['thisCircuit']" value="AdminMetadataKeywordsGroups"> */
$myFusebox['thisCircuit'] = "AdminMetadataKeywordsGroups";
/* AdminMetadataKeywordsGroups.getGridData: <fusebox:set name="myFusebox['thisFuseaction']" value="getGridData"> */
$myFusebox['thisFuseaction'] = "getGridData";
/* AdminMetadataKeywordsGroups.getGridData: <fusebox:set name="myFusebox['thisCircuit']" value="AdminMetadataKeywordsGroups"> */
$myFusebox['thisCircuit'] = "AdminMetadataKeywordsGroups";
/* AdminMetadataKeywordsGroups.getGridData: <fusebox:set name="myFusebox['thisFuseaction']" value="getGridData"> */
$myFusebox['thisFuseaction'] = "getGridData";

?>