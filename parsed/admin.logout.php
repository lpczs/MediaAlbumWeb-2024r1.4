<?php

/* Admin.logout: <fusebox:set name="myFusebox['thisCircuit']" value="Admin">   */
$myFusebox['thisCircuit'] = "Admin";
/* Admin.logout: <fusebox:set name="myFusebox['thisFuseaction']" value="logout"> */
$myFusebox['thisFuseaction'] = "logout";
/* Admin.logout: <fusebox:instantiate object="control" class="Admin_control">  */
include_once($application["fusebox"]["WebRootToAppRootPath"]."Admin/Admin_control.php");
$control = new Admin_control;
/* Admin.logout: <fusebox:invoke object="control" methodcall="logout()">       */
$control->logout();
/* Admin.logout: <fusebox:set name="myFusebox['thisCircuit']" value="Admin">   */
$myFusebox['thisCircuit'] = "Admin";
/* Admin.logout: <fusebox:set name="myFusebox['thisFuseaction']" value="logout"> */
$myFusebox['thisFuseaction'] = "logout";
/* Admin.logout: <fusebox:set name="myFusebox['thisCircuit']" value="Admin">   */
$myFusebox['thisCircuit'] = "Admin";
/* Admin.logout: <fusebox:set name="myFusebox['thisFuseaction']" value="logout"> */
$myFusebox['thisFuseaction'] = "logout";

?>