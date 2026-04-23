<?php

/* AdminGiftCards.listGiftCards: <fusebox:set name="myFusebox['thisCircuit']" value="AdminGiftCards"> */
$myFusebox['thisCircuit'] = "AdminGiftCards";
/* AdminGiftCards.listGiftCards: <fusebox:set name="myFusebox['thisFuseaction']" value="listGiftCards"> */
$myFusebox['thisFuseaction'] = "listGiftCards";
/* AdminGiftCards.listGiftCards: <fusebox:instantiate object="control" class="AdminGiftCards_control"> */
include_once($application["fusebox"]["WebRootToAppRootPath"]."AdminGiftCards/AdminGiftCards_control.php");
$control = new AdminGiftCards_control;
/* AdminGiftCards.listGiftCards: <fusebox:invoke object="control" methodcall="assertRequestMethod(['POST'])"> */
$control->assertRequestMethod(['POST']);
/* AdminGiftCards.listGiftCards: <fusebox:invoke object="control" methodcall="assertCsrfToken()"> */
$control->assertCsrfToken();
/* AdminGiftCards.listGiftCards: <fusebox:invoke object="control" methodcall="listGiftCards()"> */
$control->listGiftCards();
/* AdminGiftCards.listGiftCards: <fusebox:set name="myFusebox['thisCircuit']" value="AdminGiftCards"> */
$myFusebox['thisCircuit'] = "AdminGiftCards";
/* AdminGiftCards.listGiftCards: <fusebox:set name="myFusebox['thisFuseaction']" value="listGiftCards"> */
$myFusebox['thisFuseaction'] = "listGiftCards";
/* AdminGiftCards.listGiftCards: <fusebox:set name="myFusebox['thisCircuit']" value="AdminGiftCards"> */
$myFusebox['thisCircuit'] = "AdminGiftCards";
/* AdminGiftCards.listGiftCards: <fusebox:set name="myFusebox['thisFuseaction']" value="listGiftCards"> */
$myFusebox['thisFuseaction'] = "listGiftCards";

?>