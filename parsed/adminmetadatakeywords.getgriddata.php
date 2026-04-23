<?php

/* AdminMetadataKeywords.getGridData: <fusebox:set name="myFusebox['thisCircuit']" value="AdminMetadataKeywords"> */
$myFusebox['thisCircuit'] = "AdminMetadataKeywords";
/* AdminMetadataKeywords.getGridData: <fusebox:set name="myFusebox['thisFuseaction']" value="getGridData"> */
$myFusebox['thisFuseaction'] = "getGridData";
/* AdminMetadataKeywords.getGridData: <fusebox:instantiate object="control" class="AdminMetadataKeywords_control"> */
include_once($application["fusebox"]["WebRootToAppRootPath"]."AdminMetadataKeywords/AdminMetadataKeywords_control.php");
$control = new AdminMetadataKeywords_control;
/* AdminMetadataKeywords.getGridData: <fusebox:invoke object="control" methodcall="assertRequestMethod(['POST'])"> */
$control->assertRequestMethod(['POST']);
/* AdminMetadataKeywords.getGridData: <fusebox:invoke object="control" methodcall="assertCsrfToken()"> */
$control->assertCsrfToken();
/* AdminMetadataKeywords.getGridData: <fusebox:invoke object="control" methodcall="getGridData()"> */
$control->getGridData();
/* AdminMetadataKeywords.getGridData: <fusebox:set name="myFusebox['thisCircuit']" value="AdminMetadataKeywords"> */
$myFusebox['thisCircuit'] = "AdminMetadataKeywords";
/* AdminMetadataKeywords.getGridData: <fusebox:set name="myFusebox['thisFuseaction']" value="getGridData"> */
$myFusebox['thisFuseaction'] = "getGridData";
/* AdminMetadataKeywords.getGridData: <fusebox:set name="myFusebox['thisCircuit']" value="AdminMetadataKeywords"> */
$myFusebox['thisCircuit'] = "AdminMetadataKeywords";
/* AdminMetadataKeywords.getGridData: <fusebox:set name="myFusebox['thisFuseaction']" value="getGridData"> */
$myFusebox['thisFuseaction'] = "getGridData";

?>