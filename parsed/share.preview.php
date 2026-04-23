<?php

/* Share.preview: <fusebox:set name="myFusebox['thisCircuit']" value="Share">  */
$myFusebox['thisCircuit'] = "Share";
/* Share.preview: <fusebox:set name="myFusebox['thisFuseaction']" value="preview"> */
$myFusebox['thisFuseaction'] = "preview";
/* Share.preview: <fusebox:instantiate object="control" class="Share_control"> */
include_once($application["fusebox"]["WebRootToAppRootPath"]."Share/Share_control.php");
$control = new Share_control;
/* Share.preview: <fusebox:invoke object="control" methodcall="assertRequestMethod(['POST', 'GET'])"> */
$control->assertRequestMethod(['POST', 'GET']);
/* Share.preview: <fusebox:invoke object="control" methodcall="assertCsrfToken()"> */
$control->assertCsrfToken();
/* Share.preview: <fusebox:invoke object="control" methodcall="preview()">     */
$control->preview();
/* Share.preview: <fusebox:set name="myFusebox['thisCircuit']" value="Share">  */
$myFusebox['thisCircuit'] = "Share";
/* Share.preview: <fusebox:set name="myFusebox['thisFuseaction']" value="preview"> */
$myFusebox['thisFuseaction'] = "preview";
/* Share.preview: <fusebox:set name="myFusebox['thisCircuit']" value="Share">  */
$myFusebox['thisCircuit'] = "Share";
/* Share.preview: <fusebox:set name="myFusebox['thisFuseaction']" value="preview"> */
$myFusebox['thisFuseaction'] = "preview";

?>