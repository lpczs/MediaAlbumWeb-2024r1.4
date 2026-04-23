<?php

/* Customer.displayOnlineProjectList: <fusebox:set name="myFusebox['thisCircuit']" value="Customer"> */
$myFusebox['thisCircuit'] = "Customer";
/* Customer.displayOnlineProjectList: <fusebox:set name="myFusebox['thisFuseaction']" value="displayOnlineProjectList"> */
$myFusebox['thisFuseaction'] = "displayOnlineProjectList";
/* Customer.displayOnlineProjectList: <fusebox:instantiate object="control" class="Customer_control"> */
include_once($application["fusebox"]["WebRootToAppRootPath"]."Customer/Customer_control.php");
$control = new Customer_control;
/* Customer.displayOnlineProjectList: <fusebox:invoke object="control" methodcall="assertRequestMethod(['POST'])"> */
$control->assertRequestMethod(['POST']);
/* Customer.displayOnlineProjectList: <fusebox:invoke object="control" methodcall="assertCsrfToken()"> */
$control->assertCsrfToken();
/* Customer.displayOnlineProjectList: <fusebox:invoke object="control" methodcall="displayOnlineProjectList()"> */
$control->displayOnlineProjectList();
/* Customer.displayOnlineProjectList: <fusebox:set name="myFusebox['thisCircuit']" value="Customer"> */
$myFusebox['thisCircuit'] = "Customer";
/* Customer.displayOnlineProjectList: <fusebox:set name="myFusebox['thisFuseaction']" value="displayOnlineProjectList"> */
$myFusebox['thisFuseaction'] = "displayOnlineProjectList";
/* Customer.displayOnlineProjectList: <fusebox:set name="myFusebox['thisCircuit']" value="Customer"> */
$myFusebox['thisCircuit'] = "Customer";
/* Customer.displayOnlineProjectList: <fusebox:set name="myFusebox['thisFuseaction']" value="displayOnlineProjectList"> */
$myFusebox['thisFuseaction'] = "displayOnlineProjectList";

?>