<?php

/* Share.shareByEmail: <fusebox:set name="myFusebox['thisCircuit']" value="Share"> */
$myFusebox['thisCircuit'] = "Share";
/* Share.shareByEmail: <fusebox:set name="myFusebox['thisFuseaction']" value="shareByEmail"> */
$myFusebox['thisFuseaction'] = "shareByEmail";
/* Share.shareByEmail: <fusebox:instantiate object="control" class="Share_control"> */
include_once($application["fusebox"]["WebRootToAppRootPath"]."Share/Share_control.php");
$control = new Share_control;
/* Share.shareByEmail: <fusebox:invoke object="control" methodcall="assertRequestMethod(['POST'])"> */
$control->assertRequestMethod(['POST']);
/* Share.shareByEmail: <fusebox:invoke object="control" methodcall="assertCsrfToken()"> */
$control->assertCsrfToken();
/* Share.shareByEmail: <fusebox:invoke object="control" methodcall="shareByEmail()"> */
$control->shareByEmail();
/* Share.shareByEmail: <fusebox:set name="myFusebox['thisCircuit']" value="Share"> */
$myFusebox['thisCircuit'] = "Share";
/* Share.shareByEmail: <fusebox:set name="myFusebox['thisFuseaction']" value="shareByEmail"> */
$myFusebox['thisFuseaction'] = "shareByEmail";
/* Share.shareByEmail: <fusebox:set name="myFusebox['thisCircuit']" value="Share"> */
$myFusebox['thisCircuit'] = "Share";
/* Share.shareByEmail: <fusebox:set name="myFusebox['thisFuseaction']" value="shareByEmail"> */
$myFusebox['thisFuseaction'] = "shareByEmail";

?>