<?php

/* AdminOAuthProvider.getGridData: <fusebox:set name="myFusebox['thisCircuit']" value="AdminOAuthProvider"> */
$myFusebox['thisCircuit'] = "AdminOAuthProvider";
/* AdminOAuthProvider.getGridData: <fusebox:set name="myFusebox['thisFuseaction']" value="getGridData"> */
$myFusebox['thisFuseaction'] = "getGridData";
/* AdminOAuthProvider.getGridData: <fusebox:instantiate object="control" class="AdminOAuthProvider_control"> */
include_once($application["fusebox"]["WebRootToAppRootPath"]."AdminOAuthProvider/AdminOAuthProvider_control.php");
$control = new AdminOAuthProvider_control;
/* AdminOAuthProvider.getGridData: <fusebox:invoke object="control" methodcall="assertRequestMethod(['POST'])"> */
$control->assertRequestMethod(['POST']);
/* AdminOAuthProvider.getGridData: <fusebox:invoke object="control" methodcall="getGridData()"> */
$control->getGridData();
/* AdminOAuthProvider.getGridData: <fusebox:set name="myFusebox['thisCircuit']" value="AdminOAuthProvider"> */
$myFusebox['thisCircuit'] = "AdminOAuthProvider";
/* AdminOAuthProvider.getGridData: <fusebox:set name="myFusebox['thisFuseaction']" value="getGridData"> */
$myFusebox['thisFuseaction'] = "getGridData";
/* AdminOAuthProvider.getGridData: <fusebox:set name="myFusebox['thisCircuit']" value="AdminOAuthProvider"> */
$myFusebox['thisCircuit'] = "AdminOAuthProvider";
/* AdminOAuthProvider.getGridData: <fusebox:set name="myFusebox['thisFuseaction']" value="getGridData"> */
$myFusebox['thisFuseaction'] = "getGridData";

?>