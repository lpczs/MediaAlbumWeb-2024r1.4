<?php

/* AjaxAPI.getAddressForm: <fusebox:set name="myFusebox['thisCircuit']" value="AjaxAPI"> */
$myFusebox['thisCircuit'] = "AjaxAPI";
/* AjaxAPI.getAddressForm: <fusebox:set name="myFusebox['thisFuseaction']" value="getAddressForm"> */
$myFusebox['thisFuseaction'] = "getAddressForm";
/* AjaxAPI.getAddressForm: <fusebox:instantiate object="control" class="AjaxAPI_control"> */
include_once($application["fusebox"]["WebRootToAppRootPath"]."AjaxAPI/AjaxAPI_control.php");
$control = new AjaxAPI_control;
/* AjaxAPI.getAddressForm: <fusebox:invoke object="control" methodcall="getAddressForm()"> */
$control->getAddressForm();
/* AjaxAPI.getAddressForm: <fusebox:set name="myFusebox['thisCircuit']" value="AjaxAPI"> */
$myFusebox['thisCircuit'] = "AjaxAPI";
/* AjaxAPI.getAddressForm: <fusebox:set name="myFusebox['thisFuseaction']" value="getAddressForm"> */
$myFusebox['thisFuseaction'] = "getAddressForm";
/* AjaxAPI.getAddressForm: <fusebox:set name="myFusebox['thisCircuit']" value="AjaxAPI"> */
$myFusebox['thisCircuit'] = "AjaxAPI";
/* AjaxAPI.getAddressForm: <fusebox:set name="myFusebox['thisFuseaction']" value="getAddressForm"> */
$myFusebox['thisFuseaction'] = "getAddressForm";

?>