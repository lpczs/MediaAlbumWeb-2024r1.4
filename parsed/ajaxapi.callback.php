<?php

/* AjaxAPI.callback: <fusebox:set name="myFusebox['thisCircuit']" value="AjaxAPI"> */
$myFusebox['thisCircuit'] = "AjaxAPI";
/* AjaxAPI.callback: <fusebox:set name="myFusebox['thisFuseaction']" value="callback"> */
$myFusebox['thisFuseaction'] = "callback";
/* AjaxAPI.callback: <fusebox:instantiate object="control" class="AjaxAPI_control"> */
include_once($application["fusebox"]["WebRootToAppRootPath"]."AjaxAPI/AjaxAPI_control.php");
$control = new AjaxAPI_control;
/* AjaxAPI.callback: <fusebox:invoke object="control" methodcall="callback()"> */
$control->callback();
/* AjaxAPI.callback: <fusebox:set name="myFusebox['thisCircuit']" value="AjaxAPI"> */
$myFusebox['thisCircuit'] = "AjaxAPI";
/* AjaxAPI.callback: <fusebox:set name="myFusebox['thisFuseaction']" value="callback"> */
$myFusebox['thisFuseaction'] = "callback";
/* AjaxAPI.callback: <fusebox:set name="myFusebox['thisCircuit']" value="AjaxAPI"> */
$myFusebox['thisCircuit'] = "AjaxAPI";
/* AjaxAPI.callback: <fusebox:set name="myFusebox['thisFuseaction']" value="callback"> */
$myFusebox['thisFuseaction'] = "callback";

?>