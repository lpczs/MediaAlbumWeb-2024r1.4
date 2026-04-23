<?php

/* AdminGiftCards.displayList: <fusebox:set name="myFusebox['thisCircuit']" value="AdminGiftCards"> */
$myFusebox['thisCircuit'] = "AdminGiftCards";
/* AdminGiftCards.displayList: <fusebox:set name="myFusebox['thisFuseaction']" value="displayList"> */
$myFusebox['thisFuseaction'] = "displayList";
/* AdminGiftCards.displayList: <fusebox:instantiate object="control" class="AdminGiftCards_control"> */
include_once($application["fusebox"]["WebRootToAppRootPath"]."AdminGiftCards/AdminGiftCards_control.php");
$control = new AdminGiftCards_control;
/* AdminGiftCards.displayList: <fusebox:invoke object="control" methodcall="assertRequestMethod(['GET'])"> */
$control->assertRequestMethod(['GET']);
/* AdminGiftCards.displayList: <fusebox:invoke object="control" methodcall="displayList()"> */
$control->displayList();
/* AdminGiftCards.displayList: <fusebox:set name="myFusebox['thisCircuit']" value="AdminGiftCards"> */
$myFusebox['thisCircuit'] = "AdminGiftCards";
/* AdminGiftCards.displayList: <fusebox:set name="myFusebox['thisFuseaction']" value="displayList"> */
$myFusebox['thisFuseaction'] = "displayList";
/* AdminGiftCards.displayList: <fusebox:set name="myFusebox['thisCircuit']" value="AdminGiftCards"> */
$myFusebox['thisCircuit'] = "AdminGiftCards";
/* AdminGiftCards.displayList: <fusebox:set name="myFusebox['thisFuseaction']" value="displayList"> */
$myFusebox['thisFuseaction'] = "displayList";

?>