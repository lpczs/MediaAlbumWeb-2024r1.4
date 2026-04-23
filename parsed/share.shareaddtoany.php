<?php

/* Share.shareAddToAny: <fusebox:set name="myFusebox['thisCircuit']" value="Share"> */
$myFusebox['thisCircuit'] = "Share";
/* Share.shareAddToAny: <fusebox:set name="myFusebox['thisFuseaction']" value="shareAddToAny"> */
$myFusebox['thisFuseaction'] = "shareAddToAny";
/* Share.shareAddToAny: <fusebox:instantiate object="control" class="Share_control"> */
include_once($application["fusebox"]["WebRootToAppRootPath"]."Share/Share_control.php");
$control = new Share_control;
/* Share.shareAddToAny: <fusebox:invoke object="control" methodcall="assertRequestMethod(['POST'])"> */
$control->assertRequestMethod(['POST']);
/* Share.shareAddToAny: <fusebox:invoke object="control" methodcall="assertCsrfToken()"> */
$control->assertCsrfToken();
/* Share.shareAddToAny: <fusebox:invoke object="control" methodcall="shareAddToAny()"> */
$control->shareAddToAny();
/* Share.shareAddToAny: <fusebox:set name="myFusebox['thisCircuit']" value="Share"> */
$myFusebox['thisCircuit'] = "Share";
/* Share.shareAddToAny: <fusebox:set name="myFusebox['thisFuseaction']" value="shareAddToAny"> */
$myFusebox['thisFuseaction'] = "shareAddToAny";
/* Share.shareAddToAny: <fusebox:set name="myFusebox['thisCircuit']" value="Share"> */
$myFusebox['thisCircuit'] = "Share";
/* Share.shareAddToAny: <fusebox:set name="myFusebox['thisFuseaction']" value="shareAddToAny"> */
$myFusebox['thisFuseaction'] = "shareAddToAny";

?>