<?php

/* Customer.showPreview: <fusebox:set name="myFusebox['thisCircuit']" value="Customer"> */
$myFusebox['thisCircuit'] = "Customer";
/* Customer.showPreview: <fusebox:set name="myFusebox['thisFuseaction']" value="showPreview"> */
$myFusebox['thisFuseaction'] = "showPreview";
/* Customer.showPreview: <fusebox:instantiate object="control" class="Customer_control"> */
include_once($application["fusebox"]["WebRootToAppRootPath"]."Customer/Customer_control.php");
$control = new Customer_control;
/* Customer.showPreview: <fusebox:invoke object="control" methodcall="assertRequestMethod(['POST', 'GET'])"> */
$control->assertRequestMethod(['POST', 'GET']);
/* Customer.showPreview: <fusebox:invoke object="control" methodcall="assertCsrfToken()"> */
$control->assertCsrfToken();
/* Customer.showPreview: <fusebox:invoke object="control" methodcall="showPreview()"> */
$control->showPreview();
/* Customer.showPreview: <fusebox:set name="myFusebox['thisCircuit']" value="Customer"> */
$myFusebox['thisCircuit'] = "Customer";
/* Customer.showPreview: <fusebox:set name="myFusebox['thisFuseaction']" value="showPreview"> */
$myFusebox['thisFuseaction'] = "showPreview";
/* Customer.showPreview: <fusebox:set name="myFusebox['thisCircuit']" value="Customer"> */
$myFusebox['thisCircuit'] = "Customer";
/* Customer.showPreview: <fusebox:set name="myFusebox['thisFuseaction']" value="showPreview"> */
$myFusebox['thisFuseaction'] = "showPreview";

?>