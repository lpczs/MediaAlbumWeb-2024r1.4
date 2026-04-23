<?php

/* Share.reorder: <fusebox:set name="myFusebox['thisCircuit']" value="Share">  */
$myFusebox['thisCircuit'] = "Share";
/* Share.reorder: <fusebox:set name="myFusebox['thisFuseaction']" value="reorder"> */
$myFusebox['thisFuseaction'] = "reorder";
/* Share.reorder: <fusebox:instantiate object="control" class="Share_control"> */
include_once($application["fusebox"]["WebRootToAppRootPath"]."Share/Share_control.php");
$control = new Share_control;
/* Share.reorder: <fusebox:invoke object="control" methodcall="assertRequestMethod(['POST'])"> */
$control->assertRequestMethod(['POST']);
/* Share.reorder: <fusebox:invoke object="control" methodcall="assertCsrfToken()"> */
$control->assertCsrfToken();
/* Share.reorder: <fusebox:invoke object="control" methodcall="reorder()">     */
$control->reorder();
/* Share.reorder: <fusebox:set name="myFusebox['thisCircuit']" value="Share">  */
$myFusebox['thisCircuit'] = "Share";
/* Share.reorder: <fusebox:set name="myFusebox['thisFuseaction']" value="reorder"> */
$myFusebox['thisFuseaction'] = "reorder";
/* Share.reorder: <fusebox:set name="myFusebox['thisCircuit']" value="Share">  */
$myFusebox['thisCircuit'] = "Share";
/* Share.reorder: <fusebox:set name="myFusebox['thisFuseaction']" value="reorder"> */
$myFusebox['thisFuseaction'] = "reorder";

?>