<?php

/* AdminOAuthProvider.getProvider: <fusebox:set name="myFusebox['thisCircuit']" value="AdminOAuthProvider"> */
$myFusebox['thisCircuit'] = "AdminOAuthProvider";
/* AdminOAuthProvider.getProvider: <fusebox:set name="myFusebox['thisFuseaction']" value="getProvider"> */
$myFusebox['thisFuseaction'] = "getProvider";
/* AdminOAuthProvider.getProvider: <fusebox:instantiate object="control" class="AdminOAuthProvider_control"> */
include_once($application["fusebox"]["WebRootToAppRootPath"]."AdminOAuthProvider/AdminOAuthProvider_control.php");
$control = new AdminOAuthProvider_control;
/* AdminOAuthProvider.getProvider: <fusebox:invoke object="control" methodcall="assertRequestMethod(['GET'])"> */
$control->assertRequestMethod(['GET']);
/* AdminOAuthProvider.getProvider: <fusebox:invoke object="control" methodcall="getProvider()"> */
$control->getProvider();
/* AdminOAuthProvider.getProvider: <fusebox:set name="myFusebox['thisCircuit']" value="AdminOAuthProvider"> */
$myFusebox['thisCircuit'] = "AdminOAuthProvider";
/* AdminOAuthProvider.getProvider: <fusebox:set name="myFusebox['thisFuseaction']" value="getProvider"> */
$myFusebox['thisFuseaction'] = "getProvider";
/* AdminOAuthProvider.getProvider: <fusebox:set name="myFusebox['thisCircuit']" value="AdminOAuthProvider"> */
$myFusebox['thisCircuit'] = "AdminOAuthProvider";
/* AdminOAuthProvider.getProvider: <fusebox:set name="myFusebox['thisFuseaction']" value="getProvider"> */
$myFusebox['thisFuseaction'] = "getProvider";

?>