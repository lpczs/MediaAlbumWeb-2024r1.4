<?php

/* AdminCustomers.initialize: <fusebox:set name="myFusebox['thisCircuit']" value="AdminCustomers"> */
$myFusebox['thisCircuit'] = "AdminCustomers";
/* AdminCustomers.initialize: <fusebox:set name="myFusebox['thisFuseaction']" value="initialize"> */
$myFusebox['thisFuseaction'] = "initialize";
/* AdminCustomers.initialize: <fusebox:instantiate object="control" class="AdminCustomers_control"> */
include_once($application["fusebox"]["WebRootToAppRootPath"]."AdminCustomers/AdminCustomers_control.php");
$control = new AdminCustomers_control;
/* AdminCustomers.initialize: <fusebox:invoke object="control" methodcall="assertRequestMethod(['GET'])"> */
$control->assertRequestMethod(['GET']);
/* AdminCustomers.initialize: <fusebox:invoke object="control" methodcall="initialize()"> */
$control->initialize();
/* AdminCustomers.initialize: <fusebox:set name="myFusebox['thisCircuit']" value="AdminCustomers"> */
$myFusebox['thisCircuit'] = "AdminCustomers";
/* AdminCustomers.initialize: <fusebox:set name="myFusebox['thisFuseaction']" value="initialize"> */
$myFusebox['thisFuseaction'] = "initialize";
/* AdminCustomers.initialize: <fusebox:set name="myFusebox['thisCircuit']" value="AdminCustomers"> */
$myFusebox['thisCircuit'] = "AdminCustomers";
/* AdminCustomers.initialize: <fusebox:set name="myFusebox['thisFuseaction']" value="initialize"> */
$myFusebox['thisFuseaction'] = "initialize";

?>