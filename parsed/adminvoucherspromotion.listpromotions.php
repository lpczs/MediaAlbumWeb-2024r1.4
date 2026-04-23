<?php

/* AdminVouchersPromotion.listPromotions: <fusebox:set name="myFusebox['thisCircuit']" value="AdminVouchersPromotion"> */
$myFusebox['thisCircuit'] = "AdminVouchersPromotion";
/* AdminVouchersPromotion.listPromotions: <fusebox:set name="myFusebox['thisFuseaction']" value="listPromotions"> */
$myFusebox['thisFuseaction'] = "listPromotions";
/* AdminVouchersPromotion.listPromotions: <fusebox:instantiate object="control" class="AdminVouchersPromotion_control"> */
include_once($application["fusebox"]["WebRootToAppRootPath"]."AdminVouchersPromotion/AdminVouchersPromotion_control.php");
$control = new AdminVouchersPromotion_control;
/* AdminVouchersPromotion.listPromotions: <fusebox:invoke object="control" methodcall="assertRequestMethod(['POST'])"> */
$control->assertRequestMethod(['POST']);
/* AdminVouchersPromotion.listPromotions: <fusebox:invoke object="control" methodcall="assertCsrfToken()"> */
$control->assertCsrfToken();
/* AdminVouchersPromotion.listPromotions: <fusebox:invoke object="control" methodcall="listPromotions()"> */
$control->listPromotions();
/* AdminVouchersPromotion.listPromotions: <fusebox:set name="myFusebox['thisCircuit']" value="AdminVouchersPromotion"> */
$myFusebox['thisCircuit'] = "AdminVouchersPromotion";
/* AdminVouchersPromotion.listPromotions: <fusebox:set name="myFusebox['thisFuseaction']" value="listPromotions"> */
$myFusebox['thisFuseaction'] = "listPromotions";
/* AdminVouchersPromotion.listPromotions: <fusebox:set name="myFusebox['thisCircuit']" value="AdminVouchersPromotion"> */
$myFusebox['thisCircuit'] = "AdminVouchersPromotion";
/* AdminVouchersPromotion.listPromotions: <fusebox:set name="myFusebox['thisFuseaction']" value="listPromotions"> */
$myFusebox['thisFuseaction'] = "listPromotions";

?>