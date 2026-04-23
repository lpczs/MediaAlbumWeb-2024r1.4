<?php
/* Smarty version 4.5.3, created on 2026-03-07 07:52:34
  from 'C:\TAOPIX\MediaAlbumWeb\templates\order\jobticketajax_small.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.5.3',
  'unifunc' => 'content_69abd942ae9f75_49361663',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'd9cf1d983e527d0852ee20635402ce0fa7286e70' => 
    array (
      0 => 'C:\\TAOPIX\\MediaAlbumWeb\\templates\\order\\jobticketajax_small.tpl',
      1 => 1729602708,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:order/componentdetail_small.tpl' => 1,
    'file:order/orderfootercomponentdetail_small.tpl' => 1,
    'file:order/subcomponentdetail_small.tpl' => 1,
    'file:order/orderfootersubcomponentdetail_small.tpl' => 1,
    'file:order/shippingmethodlist.tpl' => 1,
  ),
),false)) {
function content_69abd942ae9f75_49361663 (Smarty_Internal_Template $_smarty_tpl) {
?> <!-- QTY PANELS -->

<?php if ($_smarty_tpl->tpl_vars['stage']->value == 'qty') {?>

<div id="contentPanelQty" class="contentLeftPanel">

    <div id="contentLeftScrollQty" class="contentScrollCart">

        <div class="contentVisible">

<?php }?> 
<?php if ($_smarty_tpl->tpl_vars['stage']->value == 'shipping') {?>

    <?php if ($_smarty_tpl->tpl_vars['isrefreshcall']->value == false) {?>

<div id="contentPanelShipping" class="contentLeftPanel">

    <?php }?> 
    <div id="shippingBack" class="contentNavigation">

        <div class="btnDoneTop" id="backButton" data-decorator="fnSetHashUrl" data-hash-url="qty">
            <img class="backImage" src="<?php echo $_smarty_tpl->tpl_vars['webroot']->value;?>
/images/icons/back-arrow.png" alt="<" />
            <div class="btnDone"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ButtonBack');?>
</div>
            <div class="clear"></div>
        </div> <!-- btnDoneTop -->

    </div>

    <div id="contentLeftScrollShipping" class="contentScrollCart">

        <div class="contentVisible">

<?php }?> 
<?php if ($_smarty_tpl->tpl_vars['stage']->value == 'payment') {?>

<div id="contentPanelPayment" class="contentLeftPanel">

    <div id="paymentBack" class="contentNavigation">

        <div class="btnDoneTop" id="backButton" data-decorator="fnSetHashUrl" data-hash-url="shipping">
            <img class="backImage" src="<?php echo $_smarty_tpl->tpl_vars['webroot']->value;?>
/images/icons/back-arrow.png" alt="<" />
            <div class="btnDone"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ButtonBack');?>
</div>
            <div class="clear"></div>
        </div> <!-- btnDoneTop -->

    </div>

    <div id="contentLeftScrollPayment" class="contentScrollCart">

        <div class="contentVisible">

<?php }?> 
            <div class="pageLabel">

<?php if ($_smarty_tpl->tpl_vars['stage']->value == 'qty') {?>
    <?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelNavigationCart');?>

<?php }?>

<?php if ($_smarty_tpl->tpl_vars['stage']->value == 'shipping') {?>
    <?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelNavigationShippingBilling');?>

<?php }?>

<?php if ($_smarty_tpl->tpl_vars['stage']->value == 'payment') {?>
    <?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelOrderSummaryPayment');?>

<?php }?>

            </div> <!-- pageLabel -->

            <div id="contentHolder">

        <!-- METADATA -->

<?php if (($_smarty_tpl->tpl_vars['metadatalayout']->value != '') && ($_smarty_tpl->tpl_vars['stage']->value != 'payment')) {?>

            <div class="contentMetaDataBloc">

                <div class="summaryTab summaryTabMargin">
                    <?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelAdditionalInformation');?>

                </div>

                <div class="clear"></div>
                <div>
                    <?php echo $_smarty_tpl->tpl_vars['metadatalayout']->value;?>

                </div>

            </div> <!-- contentMetaDataBloc -->

<?php }?> 
        <!-- END METADATA -->


        <!-- ORDER ITEMS -->

<?php if (($_smarty_tpl->tpl_vars['stage']->value == 'qty') || ($_smarty_tpl->tpl_vars['stage']->value == 'payment')) {?>

            <div id="orderContent">

            <!-- ORDER LINES START -->

    <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['orderitems']->value, 'orderitem', false, NULL, 'orderItemsLoop', array (
));
$_smarty_tpl->tpl_vars['orderitem']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['orderitem']->value) {
$_smarty_tpl->tpl_vars['orderitem']->do_else = false;
?>

        <?php $_smarty_tpl->_subTemplateRender(((string)$_smarty_tpl->tpl_vars['orderline']->value), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('orderline'=>$_smarty_tpl->tpl_vars['orderitem']->value), 0, true);
?>

    <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?> 
            <!-- END ORDER LINES -->

            <!-- ORDER FOOTER START -->

    <?php $_smarty_tpl->_subTemplateRender(((string)$_smarty_tpl->tpl_vars['orderfooter']->value), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, true);
?>

            <!-- END ORDER FOOTER -->

            </div> <!-- orderContent -->

<?php }?> 
            <!-- END ORDER ITEMS -->

            <!-- PAYMENT SUMMARY SECTION -->

<?php if ($_smarty_tpl->tpl_vars['stage']->value == 'payment') {?>

    <?php if ($_smarty_tpl->tpl_vars['metadatalayout']->value != '') {?>

            <div class="outerBox outerBoxWithTab">

                <div class="summaryTab summaryTabMargin">
                    <?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelAdditionalInformation');?>

                </div>

                <div class="clear"></div>
                <div class="componentMetadataAdditionnal componentMetadata contentMetadata outerBoxPadding">
                    <?php echo $_smarty_tpl->tpl_vars['metadatalayout']->value;?>

                </div>

            </div> <!-- outerBox outerBoxPadding outerBoxWithTab -->

    <?php }?> 
            <!-- SHIPPING SUMMARY DETAILS -->

            <div class="outerBox outerBoxWithTab">

                <div class="summaryTab">
                    <?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_TitleOrderStageShipping');?>

                </div>
                <div class="clear"></div>

                <div class="content contentPayment ">

                    <div class="contentAddress">

                        <div class="contentAddressBody">

                            <div class="shippingSummary outerBoxPadding">
                                <div class="shippingTitle">
                                    <?php echo $_smarty_tpl->tpl_vars['shippingStoreAddressLabel']->value;?>

                                </div>
                                <div class="shippingText">
                                    <?php echo $_smarty_tpl->tpl_vars['shippingaddress']->value;?>

                                </div>
                            </div> <!-- shippingSummary -->

                            <div class="billingSummary outerBoxPadding">
                                <div class="shippingTitle">
                                    <?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelBillingAddress');?>

                                </div>
                                <div class="shippingText">
                                    <?php echo $_smarty_tpl->tpl_vars['billingaddress']->value;?>

                                </div>
                            </div> <!-- billingSummary -->

                            <div class="clear"></div>
                        </div> <!-- contentAddressBody -->

                    </div> <!-- contentAddress -->

                    <div class="itemSection outerBoxPadding">

                        <div id="shippingLabel" class="itemSectionLabel itemSectionTotalLabel">
                            <?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelOrderShipping');?>
 (<?php echo $_smarty_tpl->tpl_vars['shippingmethodname']->value;?>
):
                        </div>

                        <div id="shippingPrice" class="itemTotalNumber">
                            <?php echo $_smarty_tpl->tpl_vars['ordertotalshipping']->value;?>

                        </div>
                        <div class="clear"></div>

                    </div> <!-- itemSection outerBoxPadding -->

                </div> <!-- content contentPayment -->

                <div class="shippingTotalDetails">

                    <!-- VOUCHER -->

    <?php if (($_smarty_tpl->tpl_vars['vouchersection']->value == 'SHIPPING') || (($_smarty_tpl->tpl_vars['vouchersection']->value == 'TOTAL') && ($_smarty_tpl->tpl_vars['differenttaxrates']->value == true) && (!$_smarty_tpl->tpl_vars['specialvouchertype']->value))) {?>

                    <!-- SHIPPING VOUCHER  -->

        <?php if ($_smarty_tpl->tpl_vars['vouchersection']->value == 'SHIPPING') {?>

            <?php if ($_smarty_tpl->tpl_vars['shippingdiscountvalueraw']->value > 0) {?>

                    <div class="itemSection outerBoxPadding">

                        <div class="itemSectionLabel">
                            <?php echo $_smarty_tpl->tpl_vars['shippingdiscountname']->value;?>
:
                        </div>

                        <div class="itemTotalNumber">
                            <?php echo $_smarty_tpl->tpl_vars['shippingdiscountvalue']->value;?>

                        </div>
                        <div class="clear"></div>

                    </div> <!-- itemSection outerBoxPadding -->

            <?php }?> 
                    <!-- SHOW SHIPPING TAX  -->

            <?php if ($_smarty_tpl->tpl_vars['showshippingtax']->value) {?>

                    <!-- SHOW PRICES WITH TAX  -->

                <?php if ($_smarty_tpl->tpl_vars['showpriceswithtax']->value) {?>

                    <?php if ($_smarty_tpl->tpl_vars['shippingdiscountvalueraw']->value > 0) {?>

                    <div class="itemSection outerBoxPadding">

                        <div class="itemSectionLabel">
                            <?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelOrderShippingTotal');?>
:
                        </div>

                        <div class="itemTotalNumber">
                            <?php echo $_smarty_tpl->tpl_vars['shippingdiscountedvalue']->value;?>

                        </div>
                        <div class="clear"></div>

                    </div> <!-- itemSection outerBoxPadding -->

                    <?php }?> 
                    <div class="line-sub-total-small-bottom">
                        <?php echo $_smarty_tpl->tpl_vars['includesshippingtaxtext']->value;?>

                    </div>

                <?php } else { ?> 
                    <?php if ($_smarty_tpl->tpl_vars['shippingdiscountvalueraw']->value > 0) {?>

                    <div class="itemSection outerBoxPadding">

                        <div class="itemSectionLabel">
                            <?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelSubTotal');?>
:
                        </div>

                        <div class="itemTotalNumber">
                            <?php echo $_smarty_tpl->tpl_vars['shippingdiscountedvalue']->value;?>

                        </div>
                        <div class="clear"></div>

                    </div> <!-- itemSection outerBoxPadding -->

                    <?php }?> 
                    <div class="itemSection outerBoxPadding">

                        <div class="itemSectionLabel">
                            <?php echo $_smarty_tpl->tpl_vars['shippingtaxname']->value;?>
 (<?php echo $_smarty_tpl->tpl_vars['shippingtaxrate']->value;?>
%):
                        </div>

                        <div class="itemTotalNumber">
                            <?php echo $_smarty_tpl->tpl_vars['shippingtaxtotal']->value;?>

                        </div>
                        <div class="clear"></div>

                    </div> <!-- itemSection outerBoxPadding -->

                    <div class="itemSection outerBoxPadding">

                        <div class="itemSectionLabel">
                            <?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelOrderShippingTotal');?>
:
                        </div>

                        <div class="itemTotalNumber">
                            <?php echo $_smarty_tpl->tpl_vars['shippingtotal']->value;?>

                        </div>
                        <div class="clear"></div>

                    </div> <!-- itemSection outerBoxPadding -->

                <?php }?> 
            <?php } else { ?> 
                <?php if ($_smarty_tpl->tpl_vars['shippingdiscountvalueraw']->value > 0) {?>

                    <div class="itemSection outerBoxPadding">

                        <div class="itemSectionLabel">
                            <?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelOrderShippingTotal');?>
:
                        </div>

                        <div class="itemTotalNumber">
                            <?php echo $_smarty_tpl->tpl_vars['shippingtotal']->value;?>

                        </div>
                        <div class="clear"></div>

                    </div> <!-- itemSection outerBoxPadding -->

                <?php }?> 
            <?php }?> 
        <?php }?> 
                    <!-- TOTAL VOUCHER  -->

        <?php if ((($_smarty_tpl->tpl_vars['vouchersection']->value == 'TOTAL') && ($_smarty_tpl->tpl_vars['differenttaxrates']->value == true) && (!$_smarty_tpl->tpl_vars['specialvouchertype']->value))) {?>

            <?php if ($_smarty_tpl->tpl_vars['shippingdiscountvalueraw']->value > 0) {?>

                    <div class="itemSection outerBoxPadding">

                        <div class="itemSectionLabel">
                            <?php echo $_smarty_tpl->tpl_vars['shippingdiscountname']->value;?>
:
                        </div>

                        <div class="itemTotalNumber">
                            <?php echo $_smarty_tpl->tpl_vars['shippingdiscountvalue']->value;?>

                        </div>
                        <div class="clear"></div>

                    </div> <!-- itemSection outerBoxPadding -->

            <?php }?> 
                    <!-- SHOW SHIPPING TAX  -->

            <?php if ($_smarty_tpl->tpl_vars['showshippingtax']->value) {?>

                    <!-- SHOW PRICES WITH TAX  -->

                <?php if ($_smarty_tpl->tpl_vars['showpriceswithtax']->value) {?>

                    <?php if ($_smarty_tpl->tpl_vars['shippingdiscountvalueraw']->value > 0) {?>

                    <div class="itemSection outerBoxPadding">

                        <div class="itemSectionLabel">
                            <?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelOrderShippingTotal');?>
:
                        </div>

                        <div class="itemTotalNumber">
                            <?php echo $_smarty_tpl->tpl_vars['shippingdiscountedvalue']->value;?>

                        </div>

                        <div class="clear"></div>
                    </div> <!-- itemSection outerBoxPadding -->

                    <?php }?> 
                    <div class="line-sub-total-small-bottom">
                        <?php echo $_smarty_tpl->tpl_vars['includesshippingtaxtext']->value;?>

                    </div>

                <?php } else { ?> 
                    <?php if ($_smarty_tpl->tpl_vars['shippingdiscountvalueraw']->value > 0) {?>

                    <div class="itemSection outerBoxPadding">

                        <div class="itemSectionLabel">
                            <?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelSubTotal');?>
:
                        </div>

                        <div class="itemTotalNumber">
                            <?php echo $_smarty_tpl->tpl_vars['shippingdiscountedvalue']->value;?>

                        </div>
                        <div class="clear"></div>

                    </div> <!-- itemSection outerBoxPadding -->

                    <?php }?> 
                    <div class="itemSection outerBoxPadding">

                        <div class="itemSectionLabel">
                            <?php echo $_smarty_tpl->tpl_vars['shippingtaxname']->value;?>
 (<?php echo $_smarty_tpl->tpl_vars['shippingtaxrate']->value;?>
%):
                        </div>

                        <div class="itemTotalNumber">
                            <?php echo $_smarty_tpl->tpl_vars['shippingtaxtotal']->value;?>

                        </div>
                        <div class="clear"></div>

                    </div> <!-- itemSection outerBoxPadding -->

                    <div class="itemSection outerBoxPadding">

                        <div class="itemSectionLabel">
                            <?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelOrderShippingTotal');?>
:
                        </div>

                        <div class="itemTotalNumber">
                            <?php echo $_smarty_tpl->tpl_vars['shippingtotal']->value;?>

                        </div>
                        <div class="clear"></div>

                    </div> <!-- itemSection outerBoxPadding -->

                <?php }?> 
            <?php } else { ?> 
                <?php if ($_smarty_tpl->tpl_vars['shippingdiscountvalueraw']->value > 0) {?>

                    <div class="itemSection outerBoxPadding">

                        <div class="itemSectionLabel">
                            <?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelOrderShippingTotal');?>
:
                        </div>

                        <div class="itemTotalNumber">
                            <?php echo $_smarty_tpl->tpl_vars['shippingtotal']->value;?>

                        </div>
                        <div class="clear"></div>

                    </div> <!-- itemSection outerBoxPadding -->

                <?php }?> 
            <?php }?>  
        <?php }?> 
    <?php } else { ?> 
        <?php if (((($_smarty_tpl->tpl_vars['vouchersection']->value == 'TOTAL') && ($_smarty_tpl->tpl_vars['differenttaxrates']->value == true) && ($_smarty_tpl->tpl_vars['specialvouchertype']->value)) || (($_smarty_tpl->tpl_vars['vouchersection']->value == 'TOTAL') && ($_smarty_tpl->tpl_vars['differenttaxrates']->value == false) && ($_smarty_tpl->tpl_vars['applyVoucherAsLineDiscount']->value == true)))) {?>

            <?php if ($_smarty_tpl->tpl_vars['shippingdiscountvalueraw']->value > 0) {?>

                    <div class="itemSection outerBoxPadding">
                        <div class="itemSectionLabel">
                            <?php echo $_smarty_tpl->tpl_vars['shippingdiscountname']->value;?>
:
                        </div>

                        <div class="itemTotalNumber">
                            <?php echo $_smarty_tpl->tpl_vars['shippingdiscountvalue']->value;?>

                        </div>
                        <div class="clear"></div>

                    </div> <!-- itemSection outerBoxPadding -->

                <?php if (($_smarty_tpl->tpl_vars['showpriceswithtax']->value)) {?>

                    <div class="itemSection outerBoxPadding">

                        <div class="itemSectionLabel">
                            <?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelOrderShippingTotal');?>
:
                        </div>

                        <div class="itemTotalNumber">
                            <?php echo $_smarty_tpl->tpl_vars['shippingdiscountedvalue']->value;?>

                        </div>
                        <div class="clear"></div>

                    </div> <!-- itemSection outerBoxPadding -->

                <?php } else { ?> 
                    <div class="itemSection outerBoxPadding">

                        <div class="itemSectionLabel">
                            <?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelSubTotal');?>
:
                        </div>

                        <div class="itemTotalNumber">
                            <?php echo $_smarty_tpl->tpl_vars['shippingdiscountedvalue']->value;?>

                        </div>
                        <div class="clear"></div>

                    </div> <!-- itemSection outerBoxPadding -->

                <?php }?> 
            <?php }?> 
                    <!-- SHOW SHIPPING TAX  -->

            <?php if ($_smarty_tpl->tpl_vars['showshippingtax']->value) {?>

                    <!-- SHOW PRICES WITH TAX  -->

                <?php if ($_smarty_tpl->tpl_vars['showpriceswithtax']->value) {?>

                    <div class="line-sub-total-small-bottom">
                        <?php echo $_smarty_tpl->tpl_vars['includesshippingtaxtext']->value;?>

                    </div>

                <?php } else { ?> 
                    <div class="itemSection outerBoxPadding">

                        <div class="itemSectionLabel">
                            <?php echo $_smarty_tpl->tpl_vars['shippingtaxname']->value;?>
 (<?php echo $_smarty_tpl->tpl_vars['shippingtaxrate']->value;?>
%):
                        </div>

                        <div class="itemTotalNumber">
                            <?php echo $_smarty_tpl->tpl_vars['shippingtaxtotal']->value;?>

                        </div>
                        <div class="clear"></div>

                    </div> <!-- itemSection outerBoxPadding -->

                    <div class="itemSection outerBoxPadding">

                        <div class="itemSectionLabel">
                            <?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelOrderShippingTotal');?>
:
                        </div>

                        <div class="itemTotalNumber">
                            <?php echo $_smarty_tpl->tpl_vars['shippingtotal']->value;?>

                        </div>
                        <div class="clear"></div>

                    </div> <!-- itemSection outerBoxPadding -->

                <?php }?> 
            <?php }?> 
        <?php } else { ?> 
            <!-- SHOW SHIPPING TAX  -->

            <?php if ($_smarty_tpl->tpl_vars['showshippingtax']->value) {?>

                <!-- SHOW PRICES WITH TAX  -->

                <?php if ($_smarty_tpl->tpl_vars['showpriceswithtax']->value) {?>

                    <div class="line-sub-total-small-bottom">
                        <?php echo $_smarty_tpl->tpl_vars['includesshippingtaxtext']->value;?>

                    </div>

                <?php } else { ?> 
                    <div class="itemSection outerBoxPadding">
                        <div class="itemSectionLabel">
                            <?php echo $_smarty_tpl->tpl_vars['shippingtaxname']->value;?>
 (<?php echo $_smarty_tpl->tpl_vars['shippingtaxrate']->value;?>
%):
                        </div>
                        <div class="itemTotalNumber">
                            <?php echo $_smarty_tpl->tpl_vars['shippingtaxtotal']->value;?>

                        </div>
                        <div class="clear"></div>
                    </div>

                    <div class="itemSection outerBoxPadding">
                        <div class="itemSectionLabel">
                            <?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelOrderShippingTotal');?>
:
                        </div>
                        <div class="itemTotalNumber">
                            <?php echo $_smarty_tpl->tpl_vars['shippingtotal']->value;?>

                        </div>
                        <div class="clear"></div>
                    </div>

                <?php }?> 
            <?php }?> 
        <?php }?> 
    <?php }?> 
                </div> <!-- shippingTotalDetails -->

            </div> <!-- outerBox outerBoxWithTab -->

            <!-- END SHIPPING SUMMARY DETAILS -->

            <!-- VOUCHERS -->

    <?php if ($_smarty_tpl->tpl_vars['showvouchers']->value == true) {?>

            <div class="outerBox outerBoxWithTab">

                <div class="summaryTab">
                    <?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_SectionTitleVouchers');?>

                </div>
                <div class="clear"></div>

                <div class="orderSummaryColor outerBoxPadding" id="ordertotalsummary">

                    <?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelEnterOrderVoucher');?>
<br /><br />

        <?php if ($_smarty_tpl->tpl_vars['voucherstatus']->value != '') {?>

                    <b><?php echo $_smarty_tpl->tpl_vars['voucherstatus']->value;?>
</b><br /><br />

        <?php }?> 
        <?php if (($_smarty_tpl->tpl_vars['defaultdiscountactive']->value == false) && ($_smarty_tpl->tpl_vars['vouchercode']->value != '')) {?>
                    <div class="formLine2">
                        <input type="text" id="vouchercode" name="vouchercode" placeholder="<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelVoucherCode');?>
" data-trigger="keyup" data-decorator="fnForceUpperAlphaNumeric" class="voucherinput" value="<?php echo $_smarty_tpl->tpl_vars['vouchercode']->value;?>
" readonly="readonly" />
                    </div>
                    <div class="clear"></div>
        <?php } else { ?>
                    <div class="formLine2">
                        <input type="text" id="vouchercode" name="vouchercode" placeholder="<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelVoucherCode');?>
" data-trigger="keyup" data-decorator="fnForceUpperAlphaNumeric" class="voucherinput" value="" />
                    </div>
                    <div class="clear"></div>
        <?php }?>

        <?php if (($_smarty_tpl->tpl_vars['vouchercode']->value == '') || (($_smarty_tpl->tpl_vars['vouchercode']->value != '') && ($_smarty_tpl->tpl_vars['defaultdiscountactive']->value == true))) {?>

                    <div id="setvoucher">

                        <div class="btnAction btnRedeem" data-decorator="setVoucher">
                            <div class="btnBlueContent"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelRedeem');?>
</div>
                        </div>
                        <div class="clear"></div>

                    </div> <!-- setvoucher -->

        <?php }?> 
        <?php if (($_smarty_tpl->tpl_vars['vouchercode']->value != '') && ($_smarty_tpl->tpl_vars['defaultdiscountactive']->value == false)) {?>

                    <div id="removevoucher">

                       <div class="btnAction btnRedeem" data-decorator="removeVoucher">
                            <div class="btnBlueContent"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelRemove');?>
</div>
                        </div>
                        <div class="clear"></div>

                    </div> <!-- removevoucher -->

        <?php }?> 
                </div> <!-- orderSummaryVoucher -->

            </div> <!-- outerBox outerBoxWithTab -->

    <?php }?> 
            <!-- END VOUCHERS -->

            <!-- GIFT CARDS -->

    <?php if ($_smarty_tpl->tpl_vars['showgiftcardsbalance']->value == true) {?>

            <div class="outerBox outerBoxWithTab">

                <div class="summaryTab">
                    <?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_SectionTitleGiftCards');?>

                </div>
                <div class="clear"></div>

                <div class="orderSummaryColor outerBoxPadding">

                    <?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelEnterOrderGiftCard');?>
<br /><br />

        <?php if ($_smarty_tpl->tpl_vars['giftcardstatus']->value != '') {?>

                    <b><?php echo $_smarty_tpl->tpl_vars['giftcardstatus']->value;?>
</b><br /><br />

        <?php }?> 
                    <div class="formLine2">
                        <input type="text" id="giftcardcode" name="giftcardcode" placeholder="<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelGiftCardCode');?>
" value="" class="voucherinput" data-trigger="keypress" data-decorator="fnForceUpperAlphaNumeric" />
                    </div>
                    <div class="clear"></div>

                    <div id="setgiftcard">
                        <div class="btnAction btnRedeem" data-decorator="setGiftCard">
                            <div class="btnBlueContent"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelRedeem');?>
</div>
                        </div>
                        <div class="clear"></div>
                    </div> <!-- setgiftcard -->

                </div> <!-- orderSummaryGiftCards -->

            </div> <!-- contentGiftCardsBloc -->

    <?php }?> 
            <!-- END GIFT CARDS -->

            <!-- SUMMARY SUBTOTAL -->

            <div class="totalLineDetail outerBoxPadding">

    <?php if (((($_smarty_tpl->tpl_vars['vouchersection']->value == 'TOTAL') && ($_smarty_tpl->tpl_vars['differenttaxrates']->value == false)) || (($_smarty_tpl->tpl_vars['vouchersection']->value == 'TOTAL') && ($_smarty_tpl->tpl_vars['differenttaxrates']->value) && ($_smarty_tpl->tpl_vars['specialvouchertype']->value)))) {?>

        <?php if (($_smarty_tpl->tpl_vars['vouchersection']->value == 'TOTAL') && ($_smarty_tpl->tpl_vars['differenttaxrates']->value == false)) {?>

            <?php if (($_smarty_tpl->tpl_vars['applyVoucherAsLineDiscount']->value == true)) {?>

                <div>
                    <div class="totalLabel">
                        <?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelOrderSubTotal');?>
:
                    </div>
                    <div class="totalNumber">
                        <?php echo $_smarty_tpl->tpl_vars['ordersubtotal']->value;?>

                    </div>
                    <div class="clear"></div>
                </div>

            <?php } else { ?>

                <div>
                    <div class="totalLabel">
                        <?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelOrderSubTotal');?>
:
                    </div>
                    <div class="totalNumber">
                        <?php echo $_smarty_tpl->tpl_vars['orderbeforediscounttotalvalue']->value;?>

                    </div>
                    <div class="clear"></div>
                </div>

                <div>
                    <div class="totalLabel">
                        <?php echo $_smarty_tpl->tpl_vars['orderaftertotaldiscountname']->value;?>
:
                    </div>
                    <div class="totalNumber">
                        <?php echo $_smarty_tpl->tpl_vars['ordertotaldiscountvalue']->value;?>

                    </div>
                    <div class="clear"></div>
                </div>

            <?php }?>

        <?php }?> 
    <?php } else { ?> 
        <?php if (($_smarty_tpl->tpl_vars['differenttaxrates']->value == false) && ($_smarty_tpl->tpl_vars['showpriceswithtax']->value == false) && (($_smarty_tpl->tpl_vars['hastotaltax']->value == true) || ($_smarty_tpl->tpl_vars['showzerotax']->value == true))) {?>

                <div>
                    <div class="totalLabel">
                        <?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelOrderSubTotal');?>
:
                    </div>
                    <div class="totalNumber">
                        <?php echo $_smarty_tpl->tpl_vars['ordersubtotal']->value;?>

                    </div>
                    <div class="clear"></div>
                </div>

        <?php }?> 
    <?php }?> 
                <div>
                   <div class="totalLabel">
                       <?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelOrderShippingCost');?>
:
                   </div>
                   <div class="totalNumber">
                       <?php echo $_smarty_tpl->tpl_vars['ordershippingcost']->value;?>

                   </div>
                   <div class="clear"></div>
               </div>

    <?php if (($_smarty_tpl->tpl_vars['differenttaxrates']->value == false) && ($_smarty_tpl->tpl_vars['showpriceswithtax']->value == false) && (($_smarty_tpl->tpl_vars['hastotaltax']->value == true) || ($_smarty_tpl->tpl_vars['showzerotax']->value == true))) {?>

                <div>
                    <div class="totalLabel">
                        <?php echo $_smarty_tpl->tpl_vars['itemtaxname']->value;?>
 (<?php echo $_smarty_tpl->tpl_vars['itemtaxrate']->value;?>
%):
                    </div>
                    <div class="totalNumber">
                        <?php echo $_smarty_tpl->tpl_vars['ordertotaltax']->value;?>

                    </div>
                    <div class="clear"></div>
                </div>

    <?php }?> 
    <?php if ($_smarty_tpl->tpl_vars['ordergiftcardtotal']->value > 0 || $_smarty_tpl->tpl_vars['disabled_giftcard']->value == 'disabled') {?>

                <div>
                    <div class="totalLabel">
                        <?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelOrderTotal');?>
:
                    </div>
                    <div class="totalNumber">
                        <?php echo $_smarty_tpl->tpl_vars['ordertotal']->value;?>

                    </div>
                    <div class="clear"></div>
                </div>

        <?php if (((($_smarty_tpl->tpl_vars['showalwaystaxtotal']->value == true) || ($_smarty_tpl->tpl_vars['showtaxbreakdown']->value == true) || ($_smarty_tpl->tpl_vars['showzerotax']->value == true)) && ($_smarty_tpl->tpl_vars['includestaxtotaltext']->value != ''))) {?>

                <div <?php if (($_smarty_tpl->tpl_vars['ordergiftcardtotal']->value == 0 || $_smarty_tpl->tpl_vars['disabled_giftcard']->value == 'disabled')) {?> style="display:none" <?php }?> class="line-sub-total-small" id="includetaxtextwithgiftcard">
                    <?php echo $_smarty_tpl->tpl_vars['includestaxtotaltext']->value;?>

                </div>

        <?php }?> 
                <div>
                    <div class="totalLabel">
                        <span id="giftbutton" title="<?php echo $_smarty_tpl->tpl_vars['tooltipGiftcardButton']->value;?>
" class="classGift<?php echo $_smarty_tpl->tpl_vars['add_delete_giftcard']->value;?>
" data-decorator="changeGiftCard"></span>

                        <span id="giftcard" class="<?php echo $_smarty_tpl->tpl_vars['disabled_giftcard']->value;?>
">
                            <?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelGiftCard');?>

                        </span>
                    </div>

                    <div class="totalNumber <?php echo $_smarty_tpl->tpl_vars['disabled_giftcard']->value;?>
" id="giftcardamount">
                        <?php echo $_smarty_tpl->tpl_vars['ordergiftcardtotalvalue']->value;?>

                    </div>
                    <div class="clear"></div>

                </div> <!-- giftcard -->

    <?php }?> 
            </div> <!-- totalLineDetail outerBoxPadding -->

            <!-- END SUMMARY SUBTOTAL -->

            <!-- AMOUNT TO PAY -->

            <div class="amountToPay outerBoxPadding">

                <div class="totalLabel">
                    <?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelAmountToPay');?>
:
                </div>

                <div class="totalNumber" id="ordertotaltopayvalue">
                    <?php echo $_smarty_tpl->tpl_vars['ordertotaltopayvalue']->value;?>

                </div>
                <div class="clear"></div>

            </div> <!-- amountToPay -->

    <?php if ((($_smarty_tpl->tpl_vars['showalwaystaxtotal']->value == true) || ($_smarty_tpl->tpl_vars['showtaxbreakdown']->value == true) || ($_smarty_tpl->tpl_vars['showzerotax']->value == true))) {?>

            <div <?php if ($_smarty_tpl->tpl_vars['ordergiftcardtotal']->value > 0 && $_smarty_tpl->tpl_vars['disabled_giftcard']->value != 'disabled') {?> style="display:none" <?php }?> class="line-sub-total-small-bottom" id="includetaxtextwithoutgiftcard">
                <?php echo $_smarty_tpl->tpl_vars['includestaxtotaltext']->value;?>

            </div>

    <?php }?> 
            <!-- END AMOUNT TO PAY  -->

            <!-- PAYMENT METHODS LIST -->

            <div <?php if ($_smarty_tpl->tpl_vars['hidepayments']->value) {?> style="display:none" <?php }?> id="paymenttableobj" class="contentPaymentMethod">

                <div class="summaryTab summaryTabMargin">
                    <?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelPaymentMethod');?>

                </div>

                <div class="clear"></div>
                <div id="paymentMethodsList">
                    <?php echo $_smarty_tpl->tpl_vars['paymentmethodslist']->value;?>

                </div>

            </div> <!-- contentPaymentMethod -->

            <!-- END PAYMENT METHODS LIST -->

    <?php if ($_smarty_tpl->tpl_vars['showtermsandconditions']->value == 1) {?>

            <div id="ordertermsandconditionscontainer" class="outerBox outerBoxPadding outerBoxMarginTop">

                <input class="inputTermsAndConditions" type="checkbox" name="ordertermsandconditions" id="ordertermsandconditions" data-decorator="acceptTermsAndConditions">
                <label class="labelTermsAndConditions" for="ordertermsandconditions" id="labelTermsAndConditions"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelTermsAndConditionsAgreement');?>
 <a id="ordertermsandconditionslink" href="#" data-decorator="orderTermsAndConditions" class="termsAndConditionsLink"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_TitleTermsAndConditions');?>
</a></label>
                <div class="clear"></div>

            </div><!-- termsAndConditions -->

    <?php }?> 
<?php }?> 
            <!-- END PAYMENT SUMMARY SECTION -->

            <!-- SHIPPING PANEL -->

<?php if ($_smarty_tpl->tpl_vars['stage']->value == 'shipping') {?>

            <div id="addressHolder" class="shippingContent">

                <div class="outerBox">

                    <div class="outerBoxPadding">

                        <div class="shippingTitle">
                            <?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelShippingMethod');?>

                        </div>

                        <div id="shippingSelectedName" class="shippingText">
                            <?php echo $_smarty_tpl->tpl_vars['shippingmethodselectedlabel']->value;?>
<br />
                            <span class="methodPrice"><?php echo $_smarty_tpl->tpl_vars['shippingselectedrate']->value;?>
</span>
                        </div>

                    </div>

                    <div class="contentChangeBtn outerBoxPadding" data-decorator="fnSetHashUrl" data-hash-url="changeMethod">

                        <div class="changeBtnText">
                            <?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ButtonChange');?>

                        </div>

                        <div class="changeBtnImg">
                            <img class="navigationArrow" src="<?php echo $_smarty_tpl->tpl_vars['webroot']->value;?>
/images/icons/change-arrow.png" alt=">" />
                        </div>
                        <div class="clear"></div>

                    </div> <!-- contentChangeBtn outerBoxPadding outerBoxPadding -->

                </div> <!-- outerBox outerBoxMarginTop -->

                <div id="shippingAddress" class="outerBox outerBoxMarginTop">

                    <div class="outerBoxPadding">

                        <div id="labelShippingStoreAddress" class="shippingTitle">
                            <?php echo $_smarty_tpl->tpl_vars['initialShippingStoreAddressLabel']->value;?>

                        </div>
                        <div id="shippingStoreAddress" class="shippingText">
                            <?php if ((($_smarty_tpl->tpl_vars['collectFromStoreCode']->value != '') || ($_smarty_tpl->tpl_vars['collectFromStore']->value != 1))) {?>
                                <?php echo $_smarty_tpl->tpl_vars['initialShippingStoreAddress']->value;?>

                            <?php }?>
                        </div>

                    </div>

    <?php if ((($_smarty_tpl->tpl_vars['canmodifyshipping']->value == true) || ($_smarty_tpl->tpl_vars['canmodifybilling']->value == true) || ($_smarty_tpl->tpl_vars['optionCFS']->value))) {?>

        <?php if ($_smarty_tpl->tpl_vars['canmodifyshipping']->value == true) {?>

                    <div id="changeShippingDiv" class="contentChangeBtn outerBoxPadding" <?php if ($_smarty_tpl->tpl_vars['collectFromStore']->value == 1) {?>style="display:none"<?php }?> data-decorator="fnSetHashUrl" data-hash-url="changeShippingAddress">

                        <div id="changeshipping" class="changeBtnText">
                            <?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ButtonChange');?>

                        </div>

                        <div class="changeBtnImg">
                            <img class="navigationArrow" src="<?php echo $_smarty_tpl->tpl_vars['webroot']->value;?>
/images/icons/change-arrow.png" alt=">" />
                        </div>
                        <div class="clear"></div>

                    </div> <!-- contentChangeBtn outerBoxPadding -->

        <?php }?> 
        <?php if ($_smarty_tpl->tpl_vars['storeisfixed']->value == 0) {?>
                    <input type="hidden" id="shippingcfscontact" name="shippingcfscontact" value="<?php if ($_smarty_tpl->tpl_vars['collectFromStore']->value == 0) {?>0<?php } else { ?>1<?php }?>" />

                    <div id="selectStoreDiv" class="contentChangeBtn outerBoxPadding" <?php if ($_smarty_tpl->tpl_vars['collectFromStore']->value == 0) {?>style="display:none"<?php }?> data-decorator="fnSetHashUrl" data-hash-url="store|<?php echo $_smarty_tpl->tpl_vars['shippingmethodcfscode']->value;?>
">

                        <div id="selectStoreButton" class="changeBtnText">
                            <?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ButtonSelectStore');?>

                        </div>

                        <div class="changeBtnImg">
                            <img class="navigationArrow" src="<?php echo $_smarty_tpl->tpl_vars['webroot']->value;?>
/images/icons/change-arrow.png" alt= ">" />
                        </div>
                        <div class="clear"></div>

                    </div> <!-- contentChangeBtn outerBoxPadding -->

                    <div id="editStoreContactDetailsDiv" class="contentChangeBtn outerBoxPadding" <?php if ((($_smarty_tpl->tpl_vars['collectFromStore']->value == 0) || ($_smarty_tpl->tpl_vars['collectFromStoreCode']->value == ''))) {?>style="display:none"<?php }?> data-decorator="fnSetHashUrl" data-hash-url="changeShippingAddressContactDetails">

                        <div id="editStoreContactDetailsButton" class="changeBtnText">
                            <?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ButtonEditCollectionDetails');?>

                        </div>

                        <div class="changeBtnImg">
                            <img class="navigationArrow" src="<?php echo $_smarty_tpl->tpl_vars['webroot']->value;?>
/images/icons/change-arrow.png" alt= ">" />
                        </div>
                        <div class="clear"></div>

                    </div> <!-- contentChangeBtn outerBoxPadding -->

        <?php }?> 
    <?php }?> 
                </div> <!-- outerBox outerBoxMarginTop -->

                <div id="billingAddress" class="outerBox outerBoxMarginTop">

                    <div class="outerBoxPadding">

                        <div class="shippingTitle">
                            <?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelBillingAddress');?>

                        </div>

                        <div class="shippingText">

                            <div id="sameasshippingaddressobj" <?php if ((($_smarty_tpl->tpl_vars['canmodifyshipping']->value == false) || ($_smarty_tpl->tpl_vars['canmodifybilling']->value == false))) {?>style="display:none"<?php }?> class="shippingSameAddress">
                                <input type="checkbox" class="sameasshippingaddress" id="sameasshippingaddress" name="sameasshippingaddress" <?php if (($_smarty_tpl->tpl_vars['sameshippingandbillingaddress']->value == true)) {?>checked="checked"<?php }?> data-decorator="setSameAddress" <?php if (($_smarty_tpl->tpl_vars['collectFromStore']->value == 1)) {?>disabled="disabled"<?php }?> />
                                <label for="sameasshippingaddress" <?php if (($_smarty_tpl->tpl_vars['collectFromStore']->value == 1)) {?>class="disabled"<?php }?>>
                                    <?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelSameAsShippingAddress');?>

                                </label>
                            </div>

                            <div>
                                <?php echo $_smarty_tpl->tpl_vars['billingaddress']->value;?>

                            </div>

                        </div> <!-- shippingText -->

                    </div>

                    <?php if ((($_smarty_tpl->tpl_vars['sameshippingandbillingaddress']->value == true) && ($_smarty_tpl->tpl_vars['canmodifyshipping']->value == true))) {?>

                    <div class="contentChangeBtn outerBoxPadding" id="changebilling"  disabled="disabled" <?php if (($_smarty_tpl->tpl_vars['canmodifybilling']->value == false)) {?>style="display:none"<?php }?>>
                        <div id="changeBillingLabel" class="changeBtnText disabled">
                            <?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ButtonChange');?>

                        </div>

                        <div id="changeBillingImage" class="changeBtnImg disabled">
                            <img class="navigationArrow" src="<?php echo $_smarty_tpl->tpl_vars['webroot']->value;?>
/images/icons/change-arrow.png" alt=">" />
                        </div>
                        <div class="clear"></div>

                    </div><!-- contentChangeBtn outerBoxPadding -->

                    <?php } else { ?>

                     <div class="contentChangeBtn outerBoxPadding" id="changebilling" <?php if (($_smarty_tpl->tpl_vars['canmodifybilling']->value == false)) {?>style="display:none"<?php }?> data-decorator="showChangeBillingAddress">
                        <div id="changeBillingLabel" class="changeBtnText">
                            <?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ButtonChange');?>

                        </div>

                        <div id="changeBillingImage" class="changeBtnImg">
                            <img class="navigationArrow" src="<?php echo $_smarty_tpl->tpl_vars['webroot']->value;?>
/images/icons/change-arrow.png" alt=">" />
                        </div>
                        <div class="clear"></div>

                    </div><!-- contentChangeBtn outerBoxPadding -->

                    <?php }?>

                </div> <!-- outerBox outerBoxMarginTop -->

            </div> <!-- addressHolder -->

            <div class="totalLineDetail outerBoxPadding">

                <div>
                    <div class="totalLabel">
                        <?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelOrderSubTotal');?>
:
                    </div>
                    <div class="totalNumber">
                        <?php echo $_smarty_tpl->tpl_vars['orderitemstotalsell']->value;?>

                    </div>
                    <div class="clear"></div>
                </div>

                <div>
                    <div class="totalLabel">
                        <?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_labelShippingTotal');?>
:
                    </div>
                    <div class="totalNumber">
                        <?php echo $_smarty_tpl->tpl_vars['ordershippingcost']->value;?>

                    </div>
                    <div class="clear"></div>
                </div>

            </div> <!-- totalLineDetail outerBoxPadding -->

<?php }?> 
            <!-- END SHIPPING PANEL -->

            <!-- TOTAl SECTION -->

<?php if ($_smarty_tpl->tpl_vars['stage']->value == 'shipping') {?>

            <div class="total">

                <div class="totalLabel"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_labelTotal');?>
:</div>
                <div class="totalNumber" id="orderTotal">
                    <?php echo $_smarty_tpl->tpl_vars['ordertotal']->value;?>

                </div>
                <div class="clear"></div>

            </div> <!-- total -->

<?php } else { ?> 
    <?php if ($_smarty_tpl->tpl_vars['stage']->value == 'qty') {?>

            <div class="total">
                <div class="totalLabel"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_labelTotal');?>
:</div>
                <div class="totalNumber" id="orderTotal">
                    <?php echo $_smarty_tpl->tpl_vars['ordertotal']->value;?>

                </div>
                <div class="clear"></div>
            </div> <!-- total -->

            <div class="quantityTotalNotice">
                <?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelTotalNotice');?>

            </div>

    <?php }?> 
<?php }?> 
            <div class="buttonBottomSection">

<?php if ($_smarty_tpl->tpl_vars['stage']->value == 'qty') {?>

                <div class="btnAction btnContinue" data-decorator="acceptDataEntry">
                    <div class="btnContinueContent"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ButtonContinue');?>
</div>
                </div>

<?php } else { ?> 
    <?php if ($_smarty_tpl->tpl_vars['stage']->value == 'payment') {?>

                <div id="ordercontinuebutton" data-decorator="orderButtonCompleteOrder">
                    <div id="btnConfirm" class="btnAction btnContinue">
                        <div id="btnContinueContentFinal" class="btnContinueContent"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ButtonConfirmOrder');?>
</div>
                    </div>
                </div>

    <?php } else { ?>                 <div data-decorator="acceptDataEntry">
                    <div class="btnAction btnContinue">
                        <div id="btnContinueContent" class="btnContinueContent"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ButtonContinue');?>
</div>
                    </div>
                </div>

    <?php }?> 
<?php }?> 
                <div class="linkAction" data-decorator="cancelOrderConfirmation">
                    <div class="deleteBtnText"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ButtonCancelOrder');?>
</div>
                </div>

            </div> <!-- buttonBottomSection -->

        </div> <!--  contentHolder -->

        </div> <!-- contentVisible -->

    </div> <!-- contentScrollCart -->

<?php if ($_smarty_tpl->tpl_vars['stage']->value == 'shipping') {?>

    <?php if ($_smarty_tpl->tpl_vars['isrefreshcall']->value == false) {?>

</div> <!-- contentLeftPanel -->

    <?php }?> 
<?php } else { ?> 
</div> <!-- contentLeftPanel -->

<?php }?> 
<?php if ($_smarty_tpl->tpl_vars['stage']->value == 'qty') {?>

<div id="contentPanelComponent" class="contentRightPanel">

    <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['orderitems']->value, 'orderitem');
$_smarty_tpl->tpl_vars['orderitem']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['orderitem']->value) {
$_smarty_tpl->tpl_vars['orderitem']->do_else = false;
?>

    <div id="componentContainer<?php echo $_smarty_tpl->tpl_vars['orderitem']->value['orderlineid'];?>
" style="display:none;">

        <?php $_smarty_tpl->_subTemplateRender("file:order/componentdetail_small.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('orderline'=>$_smarty_tpl->tpl_vars['orderitem']->value), 0, true);
?>

    </div>

    <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?> 
    <div id="componentContainer-1" style="display:none;">

        <?php $_smarty_tpl->_subTemplateRender("file:order/orderfootercomponentdetail_small.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>

    </div>

</div> <!-- contentRightPanel -->


<div id="contentPanelSubComponent" class="contentRightPanel">

    <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['orderitems']->value, 'orderitem');
$_smarty_tpl->tpl_vars['orderitem']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['orderitem']->value) {
$_smarty_tpl->tpl_vars['orderitem']->do_else = false;
?>

    <div id="subcomponentContainer<?php echo $_smarty_tpl->tpl_vars['orderitem']->value['orderlineid'];?>
" style="display:none;">

        <?php $_smarty_tpl->_subTemplateRender("file:order/subcomponentdetail_small.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('orderline'=>$_smarty_tpl->tpl_vars['orderitem']->value), 0, true);
?>

    </div>

    <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?> 
    <div id="subcomponentContainer-1" style="display:none;">

       <?php $_smarty_tpl->_subTemplateRender("file:order/orderfootersubcomponentdetail_small.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>

   </div>

</div> <!-- contentRightPanel -->

<div id="contentPanelComponentChoice" class="contentRightPanel">

    <div id="contentNavigationChoice" class="contentNavigation">

        <div class="buttonTopSection">

            <div class="btnLeftSection" id="choiceBackButton">
                <img class="backImage" src="<?php echo $_smarty_tpl->tpl_vars['webroot']->value;?>
/images/icons/back-arrow.png" alt="<" />
                <div class="btnDone"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ButtonCancel');?>
</div>
                <div class="clear"></div>
            </div>

            <div id="updateChoiceBtn" class="btnRightSection">
                <div class="btnUpdate"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ButtonUpdate');?>
</div>
            </div>
            <div class="clear"></div>

        </div> <!-- buttonTopSection -->

    </div> <!-- contentNavigation -->

    <div id="contentRightScrollChoice" class="contentScrollCart">
        <div id="contentVisibleChoice" class="contentVisible"></div>
    </div>

</div> <!-- contentPanelCompenentChoice -->

<?php }?> 
<?php if ($_smarty_tpl->tpl_vars['stage']->value == 'shipping') {?>

    <?php if ($_smarty_tpl->tpl_vars['isrefreshcall']->value == false) {?>

<!-- SHIPPING PANELS -->

<div id="contentPanelMethodList" class="contentRightPanel">

    
    <?php $_smarty_tpl->_subTemplateRender('file:order/shippingmethodlist.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>
    <!-- END SHIPPING METHOD LIST -->

</div> <!-- contentRightPanel -->

<div id="contentPanelSelectStore" class="contentRightPanel">

</div> <!-- contentPanelSelectStore -->

<div id="contentPanelUpdateAddress" class="contentRightPanel">

</div> <!-- contentPanelSelectStore -->

<!-- END SHIPPING PANELS -->

    <?php }?> 
<?php }?> 
<?php if ($_smarty_tpl->tpl_vars['stage']->value == 'payment') {?>

<div id="contentPanelPaymentgateway" class="contentRightPanel">
</div> <!-- contentPanelPaymentGateway -->

<div id="contentPanelConfirmation" class="contentRightPanel">
</div> <!-- contentPanelConfirmation -->

<?php }?> <?php }
}
