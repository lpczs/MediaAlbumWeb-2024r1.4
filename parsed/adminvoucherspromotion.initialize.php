<?php

/* AdminVouchersPromotion.initialize: <fusebox:set name="myFusebox['thisCircuit']" value="AdminVouchersPromotion"> */
$myFusebox['thisCircuit'] = "AdminVouchersPromotion";
/* AdminVouchersPromotion.initialize: <fusebox:set name="myFusebox['thisFuseaction']" value="initialize"> */
$myFusebox['thisFuseaction'] = "initialize";
/* AdminVouchersPromotion.initialize: <fusebox:instantiate object="control" class="AdminVouchersPromotion_control"> */
include_once($application["fusebox"]["WebRootToAppRootPath"]."AdminVouchersPromotion/AdminVouchersPromotion_control.php");
$control = new AdminVouchersPromotion_control;
/* AdminVouchersPromotion.initialize: <fusebox:invoke object="control" methodcall="assertRequestMethod(['GET'])"> */
$control->assertRequestMethod(['GET']);
/* AdminVouchersPromotion.initialize: <fusebox:invoke object="control" methodcall="initialize()"> */
$control->initialize();
/* AdminVouchersPromotion.initialize: <fusebox:set name="myFusebox['thisCircuit']" value="AdminVouchersPromotion"> */
$myFusebox['thisCircuit'] = "AdminVouchersPromotion";
/* AdminVouchersPromotion.initialize: <fusebox:set name="myFusebox['thisFuseaction']" value="initialize"> */
$myFusebox['thisFuseaction'] = "initialize";
/* AdminVouchersPromotion.initialize: <fusebox:set name="myFusebox['thisCircuit']" value="AdminVouchersPromotion"> */
$myFusebox['thisCircuit'] = "AdminVouchersPromotion";
/* AdminVouchersPromotion.initialize: <fusebox:set name="myFusebox['thisFuseaction']" value="initialize"> */
$myFusebox['thisFuseaction'] = "initialize";

?>