<?php
/* Smarty version 4.5.3, created on 2026-03-09 03:45:22
  from 'C:\TAOPIX\MediaAlbumWeb\templates\customer\mainajax_small.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.5.3',
  'unifunc' => 'content_69ae42526fa783_41170386',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '045671aa753d5eca358c7dec40b7b0c83a27012d' => 
    array (
      0 => 'C:\\TAOPIX\\MediaAlbumWeb\\templates\\customer\\mainajax_small.tpl',
      1 => 1729602708,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_69ae42526fa783_41170386 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'C:\\TAOPIX\\MediaAlbumWeb\\libs\\internal\\smarty\\plugins\\function.csrf_token.php','function'=>'smarty_function_csrf_token',),));
?>
<!-- YOUR ORDERS -->

<?php if ($_smarty_tpl->tpl_vars['section']->value == 'yourorders') {?>

<div id="orderMainPanel" class="productPanel">

    <div id="contentNavigationForm" class="contentNavigation">

        <div class="btnDoneTop" id="backButton" data-decorator="fnShowPanelAccount" data-visible="false" data-div-id="">
            <img class="backImage" src="<?php echo $_smarty_tpl->tpl_vars['webroot']->value;?>
/images/icons/back-arrow.png" alt="<" />
            <div class="btnDone"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_TitleMyAccount');?>
</div>
            <div class="clear"></div>
        </div>

    </div> <!-- contentNavigation -->

    <div id="contentRightScrollForm" class="contentScrollCart">

        <div class="contentVisible">

            <div class="pageLabel">
                <?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_MenuTitleYourOrders');?>

            </div>

        <!-- TEMP ORDERS -->

        <?php if ($_smarty_tpl->tpl_vars['tempordercount']->value > 0) {?>

            <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['temporderlist']->value, 'row', false, NULL, 'orders', array (
));
$_smarty_tpl->tpl_vars['row']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['row']->value) {
$_smarty_tpl->tpl_vars['row']->do_else = false;
?>

             <div class="outerBox outerBoxMarginBottom">

                <div class="outerBoxPadding">

                    <div class="orderDate">
                        <span class="orderLabelMedium"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelOrderNum');?>
:</span> <?php echo $_smarty_tpl->tpl_vars['row']->value['ordernumber'];?>
<br />
                        <span class="orderLabelMedium"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelOrderDate');?>
:</span> <?php echo $_smarty_tpl->tpl_vars['row']->value['formattedorderdate'];?>

                    </div> <!-- ordernumber -->

                <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['row']->value['product'], 'product', false, 'index', 'productloop', array (
));
$_smarty_tpl->tpl_vars['product']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['index']->value => $_smarty_tpl->tpl_vars['product']->value) {
$_smarty_tpl->tpl_vars['product']->do_else = false;
?>

                    <div class="clickable innerBox" data-decorator="fnShowOrderDetails" data-show="true" data-product-id="<?php echo $_smarty_tpl->tpl_vars['product']->value['id'];?>
">

                        <div class="orderLabel">

                            <div class="orderProductLabel">
                                <?php echo $_smarty_tpl->tpl_vars['product']->value['projectname'];?>

                            </div> <!-- componentLabel -->

                            <div class="orderProductBtnDetail">
                            </div>

                            <div class="clear"></div>

                        </div> <!-- orderLabel -->

                        <div class="contentDescription">

                            <div class="descriptionProduct">
                                <?php echo $_smarty_tpl->tpl_vars['product']->value['productname'];?>

                            </div>

                            <div class="descriptionStatus">

                    <?php if ($_smarty_tpl->tpl_vars['product']->value['status'] == 0 && $_smarty_tpl->tpl_vars['product']->value['source'] == 0) {?>

                                <span class="orderLabelStatus"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelStatus');?>
:</span>
                                <span class="statusWaitForFiles"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelStatusWaitingForFiles');?>
<span class="statusWarning"> / <?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_MenuTitlePayLaterOrders');?>
</span></span>

                    <?php } elseif ($_smarty_tpl->tpl_vars['product']->value['status'] == 0 && $_smarty_tpl->tpl_vars['product']->value['source'] == 1) {?>

                                <span class="orderLabelStatus"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelStatus');?>
:</span>
                                <span class="statusWarning"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelStatusWaitingForPayment');?>
</span>

                    <?php } elseif ($_smarty_tpl->tpl_vars['product']->value['status'] < 60) {?>

                                <span class="orderLabelStatus"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelStatus');?>
:</span>
                                <span class="statusWarning"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelStatusWaitingForPayment');?>
</span>

                    <?php }?>

                            </div> <!-- descriptionStatus -->

                        </div> <!-- contentDescription -->

                    </div> <!-- clickable innerBox -->

                <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?> 
                </div> <!-- outerBoxPadding -->

                <div class="itemSection outerBoxPadding">

                    <div class="itemSectionLabel itemSectionTotalLabel">
                        <?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelOrderTotal');?>

                    </div>

                    <div class="itemTotalNumber">
                        <?php echo $_smarty_tpl->tpl_vars['row']->value['formattedordertotal'];?>

                    </div>

                    <div class="clear"></div>

                </div> <!-- itemSection outerBoxPadding -->

            </div> <!-- outerBox outerBoxMarginTop -->

            <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?> 
        <?php }?> 
        <!-- END TEMP ORDERS -->

        <!-- ORDERS -->

        <?php if ($_smarty_tpl->tpl_vars['ordercount']->value > 0) {?>

            <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['orderlist']->value, 'row', false, NULL, 'orders', array (
));
$_smarty_tpl->tpl_vars['row']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['row']->value) {
$_smarty_tpl->tpl_vars['row']->do_else = false;
?>

             <div class="outerBox outerBoxMarginBottom">

                <div class="outerBoxPadding">

                    <div class="orderDate">
                        <span class="orderLabelMedium"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelOrderNum');?>
:</span> <?php echo $_smarty_tpl->tpl_vars['row']->value['ordernumber'];?>
<br />
                        <span class="orderLabelMedium"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelOrderDate');?>
:</span> <?php echo $_smarty_tpl->tpl_vars['row']->value['formattedorderdate'];?>

                    </div> <!-- ordernumber -->

                <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['row']->value['product'], 'product', false, 'index', 'productloop', array (
));
$_smarty_tpl->tpl_vars['product']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['index']->value => $_smarty_tpl->tpl_vars['product']->value) {
$_smarty_tpl->tpl_vars['product']->do_else = false;
?>

                    <div class="clickable innerBox" data-decorator="fnShowOrderDetails" data-show="true" data-product-id="<?php echo $_smarty_tpl->tpl_vars['product']->value['id'];?>
">

                        <div class="orderLabel">

                            <div class="orderProductLabel">
                                <?php echo $_smarty_tpl->tpl_vars['product']->value['projectname'];?>

                            </div> <!-- componentLabel -->

                            <div class="orderProductBtnDetail">
                            </div>

                            <div class="clear"></div>

                        </div> <!-- orderLabel -->

                        <div class="contentDescription">

                            <div>
                                <?php echo $_smarty_tpl->tpl_vars['product']->value['productname'];?>

                            </div>

                            <div class="descriptionStatus">

                    <?php if ($_smarty_tpl->tpl_vars['product']->value['orderstatus'] == 0) {?>

                        <?php if ($_smarty_tpl->tpl_vars['product']->value['status'] == 0 && $_smarty_tpl->tpl_vars['product']->value['source'] == 0) {?>

                                <span class="orderLabelStatus"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelStatus');?>
:</span>
                                <span class="statusWaitForFiles"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelStatusWaitingForFiles');?>
</span>

                        <?php } elseif ($_smarty_tpl->tpl_vars['product']->value['status'] == 0 && $_smarty_tpl->tpl_vars['product']->value['source'] == 1) {?>

                                <span class="orderLabelStatus"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelStatus');?>
:</span>
                                <span class="statusInProduction"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelStatusInProduction');?>
</span>

                        <?php } elseif ($_smarty_tpl->tpl_vars['product']->value['status'] == 60) {?>

                                <span class="orderLabelStatus"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelStatus');?>
:</span>
                                <span class="statusShipped"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelStatusShipped');?>
</span>

						<?php } elseif ($_smarty_tpl->tpl_vars['product']->value['status'] == 65) {?>

								<span class="orderLabelStatus"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelStatus');?>
:</span>
								<span class="statusReadyToCollect"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelStatusReadyToCollectAtStore');?>
</span>

						<?php } elseif ($_smarty_tpl->tpl_vars['product']->value['status'] == 66) {?>

								<span class="orderLabelStatus"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelStatus');?>
:</span>
								<span class="statusCompleted"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelStatusCompleted');?>
</span>

                        <?php } else { ?>

								<span class="orderLabelStatus"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelStatus');?>
:</span>
                                <span class="statusInProduction"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelStatusInProduction');?>
</span>

                        <?php }?>

                    <?php } elseif ($_smarty_tpl->tpl_vars['product']->value['orderstatus'] == 1) {?> 
                                <span class="orderLabelStatus"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelStatus');?>
:</span>
                                <span class="statusCancelled"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelStatusCancelled');?>
</span>

                    <?php } else { ?> 
                                <span class="orderLabelStatus"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelStatus');?>
:</span>
                                <span class="statusCompleted"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelStatusCompleted');?>
</span>

                    <?php }?> 
                            </div> <!-- descriptionStatus -->

                        </div> <!-- contentDescription -->

                    </div> <!-- clickable innerBox -->

                <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?> 
                </div> <!-- outerBoxPadding -->

                <div class="itemSection outerBoxPadding">

                    <div class="itemSectionLabel itemSectionTotalLabel">
                        <?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelOrderTotal');?>

                    </div>

                    <div class="itemTotalNumber">
                        <?php echo $_smarty_tpl->tpl_vars['row']->value['formattedordertotal'];?>

                    </div>

                    <?php if ($_smarty_tpl->tpl_vars['row']->value['showpaymentstatus'] == 1) {?>
                        <?php if ($_smarty_tpl->tpl_vars['row']->value['paymentreceived'] == 1) {?>
                            <p class="paymentstatus paid"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelStatusPaymentReceived');?>
</p>
                        <?php } else { ?>
                            <p class="paymentstatus waitingforpayment"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelStatusWaitingForPayment');?>
</p>
                        <?php }?>
                    <?php }?>

                    <div class="clear"></div>

                </div> <!-- itemSection outerBoxPadding -->

            </div> <!-- outerBox outerBoxMarginTop -->

            <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?> 
        <?php } else { ?>

            <?php if ($_smarty_tpl->tpl_vars['tempordercount']->value == 0) {?>

            <div class="outerBox outerBoxPadding">
                <?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelNoOrders');?>

            </div>

            <?php }?> 
        <?php }?>

        <!-- END ORDERS -->

        </div> <!-- contentVisible -->

    </div> <!-- contentScrollCart -->

</div> <!-- orderMainPanel -->

<div id="orderDetailPanel" class="productPanel">

    <div id="contentNavigationDetail" class="contentNavigation">

        <div class="btnDoneTop" id="backButton" data-decorator="fnShowOrderDetails" data-show="false" data-product-id="">
            <img class="backImage" src="<?php echo $_smarty_tpl->tpl_vars['webroot']->value;?>
/images/icons/back-arrow.png" alt="<" />
            <div class="btnDone"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_MenuTitleYourOrders');?>
</div>
            <div class="clear"></div>
        </div>

    </div> <!-- contentNavigation -->

    <div id="contentRightScrollDetail" class="contentScrollCart">

        <div class="contentVisible">

        <?php if (($_smarty_tpl->tpl_vars['tempordercount']->value > 0) || ($_smarty_tpl->tpl_vars['ordercount']->value > 0)) {?>

            <?php if ($_smarty_tpl->tpl_vars['tempordercount']->value > 0) {?>

                <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['temporderlist']->value, 'row', false, NULL, 'orders', array (
));
$_smarty_tpl->tpl_vars['row']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['row']->value) {
$_smarty_tpl->tpl_vars['row']->do_else = false;
?>

                    <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['row']->value['product'], 'product', false, 'index', 'productloop', array (
));
$_smarty_tpl->tpl_vars['product']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['index']->value => $_smarty_tpl->tpl_vars['product']->value) {
$_smarty_tpl->tpl_vars['product']->do_else = false;
?>

            <div id="productDetail<?php echo $_smarty_tpl->tpl_vars['product']->value['id'];?>
" style="display: none;">

                <div class="pageLabel">
                    <?php echo $_smarty_tpl->tpl_vars['product']->value['projectname'];?>

                </div>

                <div class="outerBox outerBoxPadding outerBoxMarginBottom productDetailBloc">

                    <div class="descriptionProduct">
                        <?php echo $_smarty_tpl->tpl_vars['product']->value['productname'];?>

                    </div>

                    <div class="orderDetail">
                        <span class="orderLabelMedium"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelOrderNum');?>
:</span> <?php echo $_smarty_tpl->tpl_vars['row']->value['ordernumber'];?>
<br />
                        <span class="orderLabelMedium"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelOrderDate');?>
:</span> <?php echo $_smarty_tpl->tpl_vars['row']->value['formattedorderdate'];?>
<br />

                        <?php if ($_smarty_tpl->tpl_vars['product']->value['orderstatus'] == 0) {?>

                            <?php if ($_smarty_tpl->tpl_vars['product']->value['status'] == 0 && $_smarty_tpl->tpl_vars['product']->value['source'] == 0) {?>

                        <span class="orderLabelMedium"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelStatus');?>
:</span>
                        <span class="statusWaitForFiles"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelStatusWaitingForFiles');?>
</span>

                            <?php } elseif ($_smarty_tpl->tpl_vars['product']->value['status'] == 0 && $_smarty_tpl->tpl_vars['product']->value['source'] == 1) {?>

                        <span class="orderLabelMedium"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelStatus');?>
:</span>
                        <span class="statusInProduction"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelStatusInProduction');?>
</span>

                            <?php } elseif ($_smarty_tpl->tpl_vars['product']->value['status'] == 60) {?>

						<span class="orderLabelMedium"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelStatus');?>
:</span>
                        <span class="statusShipped"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelStatusShipped');?>
</span>

							<?php } elseif ($_smarty_tpl->tpl_vars['product']->value['status'] == 65) {?>

						<span class="orderLabelMedium"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelStatus');?>
:</span>
						<span class="statusReadyToCollect"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelStatusReadyToCollectAtStore');?>
</span>

							<?php } elseif ($_smarty_tpl->tpl_vars['product']->value['status'] == 66) {?>

						<span class="orderLabelMedium"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelStatus');?>
:</span>
                        <span class="statusCompleted"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelStatusCompleted');?>
</span>

                            <?php } else { ?>

						<span class="orderLabelMedium"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelStatus');?>
:</span>
                        <span class="statusInProduction"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelStatusInProduction');?>
</span>

                            <?php }?>

                        <?php } elseif ($_smarty_tpl->tpl_vars['product']->value['orderstatus'] == 1) {?> 
                        <span class="orderLabelMedium"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelStatus');?>
:</span>
                        <span class="statusCancelled"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelStatusCancelled');?>
</span>

                        <?php } else { ?> 
                        <span class="orderLabelMedium"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelStatus');?>
:</span>
                        <span class="statusCompleted"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelStatusCompleted');?>
</span>

                        <?php }?> 
                    </div> <!-- orderDetail -->

                </div> <!-- outerBox outerBoxPadding outerBoxMarginBottom productDetailBloc -->

                        <?php if (($_smarty_tpl->tpl_vars['row']->value['status'] > 0) && ($_smarty_tpl->tpl_vars['product']->value['parentorderitemid'] == 0)) {?>

                <div class="paddingReorderBtn">

                    <div class="btnAction btnContinue" data-decorator="fnExecutePayNow" data-session-id="<?php echo $_smarty_tpl->tpl_vars['row']->value['sessionid'];?>
">
                        <div class="btnContinueContent"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelPayNow');?>
</div>
                    </div>

                </div>

                        <?php }?> 
                        <?php if (($_smarty_tpl->tpl_vars['product']->value['previewsonline'] == 1) && ($_smarty_tpl->tpl_vars['product']->value['parentorderitemid'] == 0)) {?>

                <div class="linkAction" data-decorator="fnShowPreview" data-url="<?php echo $_smarty_tpl->tpl_vars['webbrandweburl']->value;
echo htmlspecialchars((string)$_smarty_tpl->tpl_vars['product']->value['previewurl'], ENT_QUOTES, 'UTF-8', true);?>
&amp;ref=<?php echo $_smarty_tpl->tpl_vars['session']->value;?>
&amp;id=<?php echo $_smarty_tpl->tpl_vars['product']->value['id'];?>
">

                    <div class="changeBtnText">
                        <?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ButtonPreview');?>

                    </div>

                    <div class="changeBtnImg">
                        <img class="navigationArrow" src="<?php echo $_smarty_tpl->tpl_vars['webroot']->value;?>
/images/icons/change-arrow.png" alt= ">" />
                    </div>

                    <div class="clear"></div>

                </div> <!-- linkAction -->

                        <?php }?> 
            </div> <!-- productDetailXXX -->

                    <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?> 
                <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>

            <?php }?>

            <?php if ($_smarty_tpl->tpl_vars['ordercount']->value > 0) {?>

                <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['orderlist']->value, 'row', false, NULL, 'orders', array (
));
$_smarty_tpl->tpl_vars['row']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['row']->value) {
$_smarty_tpl->tpl_vars['row']->do_else = false;
?>

                    <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['row']->value['product'], 'product', false, 'index', 'productloop', array (
));
$_smarty_tpl->tpl_vars['product']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['index']->value => $_smarty_tpl->tpl_vars['product']->value) {
$_smarty_tpl->tpl_vars['product']->do_else = false;
?>

            <div id="productDetail<?php echo $_smarty_tpl->tpl_vars['product']->value['id'];?>
" style="display: none;">

				<input type="hidden" id="onlineProjectOrderDetail<?php echo $_smarty_tpl->tpl_vars['product']->value['projectref'];?>
" data-productident="<?php echo $_smarty_tpl->tpl_vars['product']->value['productindent'];?>
"
                data-workflowtype="<?php echo $_smarty_tpl->tpl_vars['product']->value['workflowtype'];?>
" />

				<input type="hidden" id="onlineProjectOrderLabel<?php echo $_smarty_tpl->tpl_vars['product']->value['projectref'];?>
" data-projectname="<?php echo $_smarty_tpl->tpl_vars['product']->value['projectname'];?>
" />

                <div class="pageLabel">
                    <?php echo $_smarty_tpl->tpl_vars['product']->value['projectname'];?>

                </div>

                <div class="outerBox outerBoxPadding outerBoxMarginBottom productDetailBloc">

                    <div class="nameProduct">
                        <?php echo $_smarty_tpl->tpl_vars['product']->value['productname'];?>

                    </div>

                    <div class="orderDetail">
                        <span class="orderLabelMedium"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelOrderNum');?>
:</span> <?php echo $_smarty_tpl->tpl_vars['row']->value['ordernumber'];?>
<br />
                        <span class="orderLabelMedium"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelOrderDate');?>
:</span> <?php echo $_smarty_tpl->tpl_vars['row']->value['formattedorderdate'];?>
<br />

                        <?php if ($_smarty_tpl->tpl_vars['product']->value['orderstatus'] == 0) {?>

                            <?php if ($_smarty_tpl->tpl_vars['product']->value['status'] == 0 && $_smarty_tpl->tpl_vars['product']->value['source'] == 0) {?>

                        <span class="orderLabelMedium"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelStatus');?>
:</span>
                        <span class="statusWaitForFiles"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelStatusWaitingForFiles');?>
</span>

                            <?php } elseif ($_smarty_tpl->tpl_vars['product']->value['status'] == 0 && $_smarty_tpl->tpl_vars['product']->value['source'] == 1) {?>

                        <span class="orderLabelMedium"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelStatus');?>
:</span>
                        <span class="statusInProduction"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelStatusInProduction');?>
</span>

                            <?php } elseif ($_smarty_tpl->tpl_vars['product']->value['status'] == 60) {?>

						<span class="orderLabelMedium"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelStatus');?>
:</span>
						<span class="statusShipped"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelStatusShipped');?>
</span>

							<?php } elseif ($_smarty_tpl->tpl_vars['product']->value['status'] == 65) {?>

						<span class="orderLabelMedium"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelStatus');?>
:</span>
						<span class="statusReadyToCollect"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelStatusReadyToCollectAtStore');?>
</span>

							<?php } elseif ($_smarty_tpl->tpl_vars['product']->value['status'] == 66) {?>

						<span class="orderLabelMedium"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelStatus');?>
:</span>
						<span class="statusCompleted"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelStatusCompleted');?>
</span>

                            <?php } else { ?>

						<span class="orderLabelMedium"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelStatus');?>
:</span>
                        <span class="statusInProduction"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelStatusInProduction');?>
</span>

                            <?php }?>

                        <?php } elseif ($_smarty_tpl->tpl_vars['product']->value['orderstatus'] == 1) {?> 
                        <span class="orderLabelMedium"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelStatus');?>
:</span>
                        <span class="statusCancelled"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelStatusCancelled');?>
</span>

                        <?php } else { ?> 
                        <span class="orderLabelMedium"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelStatus');?>
:</span>
                        <span class="statusCompleted"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelStatusCompleted');?>
</span>

                        <?php }?> 
                    </div> <!-- orderDetail -->

                </div> <!-- outerBox outerBoxPadding outerBoxMarginBottom productDetailBloc -->

					<?php if (($_smarty_tpl->tpl_vars['row']->value['status'] > 0) && ($_smarty_tpl->tpl_vars['product']->value['source'] == 1) && ($_smarty_tpl->tpl_vars['product']->value['parentorderitemid'] == 0) && ($_smarty_tpl->tpl_vars['row']->value['orderstatus'] == 0) && ($_smarty_tpl->tpl_vars['product']->value['canmodify'] == 1) && ($_smarty_tpl->tpl_vars['product']->value['isowner'] == 1)) {?>

						<div class="linkAction" data-decorator="fnExecuteButtonAction" data-target="3" data-project-name="<?php echo htmlspecialchars((string)$_smarty_tpl->tpl_vars['product']->value['projectname'], ENT_QUOTES, 'UTF-8', true);?>
" data-application-name="<?php echo htmlspecialchars((string)$_smarty_tpl->tpl_vars['webbrandapplicationname']->value, ENT_QUOTES, 'UTF-8', true);?>
" data-project-ref ="<?php echo $_smarty_tpl->tpl_vars['product']->value['projectref'];?>
" data-workflow-type="<?php echo $_smarty_tpl->tpl_vars['product']->value['workflowtype'];?>
" data-product-ident="<?php echo $_smarty_tpl->tpl_vars['product']->value['productindent'];?>
" data-wizard-mode="<?php echo $_smarty_tpl->tpl_vars['product']->value['wizardmode'];?>
">
							<div class="changeBtnText"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ButtonContinueEditing');?>
</div>
							<div class="clear"></div>
						</div>

					<?php }?>

					<?php if ($_smarty_tpl->tpl_vars['product']->value['parentorderitemid'] == 0) {?>
						<?php if ((((($_smarty_tpl->tpl_vars['product']->value['source'] == 1) && ($_smarty_tpl->tpl_vars['ishighlevel']->value == 0) && ($_smarty_tpl->tpl_vars['product']->value['isowner'] == 1)) || (($_smarty_tpl->tpl_vars['product']->value['source'] == 1) && ($_smarty_tpl->tpl_vars['ishighlevel']->value == 1) && ($_smarty_tpl->tpl_vars['basketref']->value != '') && ($_smarty_tpl->tpl_vars['basketref']->value != 'tpxgnbr') && ($_smarty_tpl->tpl_vars['product']->value['isowner'] == 1))))) {?>

							<div class="linkAction" data-decorator="fnExecuteButtonAction" data-target="4" data-project-name="<?php echo htmlspecialchars((string)$_smarty_tpl->tpl_vars['product']->value['projectname'], ENT_QUOTES, 'UTF-8', true);?>
" data-application-name="<?php echo htmlspecialchars((string)$_smarty_tpl->tpl_vars['webbrandapplicationname']->value, ENT_QUOTES, 'UTF-8', true);?>
" data-project-ref="<?php echo $_smarty_tpl->tpl_vars['product']->value['projectref'];?>
" data-workflow-type="<?php echo $_smarty_tpl->tpl_vars['product']->value['workflowtype'];?>
" data-product-ident="<?php echo $_smarty_tpl->tpl_vars['product']->value['productindent'];?>
" data-wizard-mode="<?php echo $_smarty_tpl->tpl_vars['product']->value['wizardmode'];?>
">
								<div class="changeBtnText"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ButtonDuplicateProject');?>
</div>
								<div class="clear"></div>
							</div>

						<?php }?>
					<?php }?>

                    <?php if (($_smarty_tpl->tpl_vars['row']->value['status'] > 0) && ($_smarty_tpl->tpl_vars['product']->value['canreorder'] == $_smarty_tpl->tpl_vars['kCanReorder']->value) && ($_smarty_tpl->tpl_vars['product']->value['parentorderitemid'] == 0)) {?>
						<div class="paddingReorderBtn">
							<div class="btnAction btnContinue" data-decorator="fnExecuteButtonAction" data-target="1" data-project-name="<?php echo htmlspecialchars((string)$_smarty_tpl->tpl_vars['product']->value['projectname'], ENT_QUOTES, 'UTF-8', true);?>
" data-application-name="<?php echo htmlspecialchars((string)$_smarty_tpl->tpl_vars['webbrandapplicationname']->value, ENT_QUOTES, 'UTF-8', true);?>
" data-project-ref="<?php echo $_smarty_tpl->tpl_vars['product']->value['projectref'];?>
" data-workflow-type="<?php echo $_smarty_tpl->tpl_vars['product']->value['workflowtype'];?>
" data-product-ident="<?php echo $_smarty_tpl->tpl_vars['product']->value['productindent'];?>
" data-wizard-mode="<?php echo $_smarty_tpl->tpl_vars['product']->value['wizardmode'];?>
">
								<div class="btnContinueContent"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelReorder');?>
</div>
							</div>
						</div>
                    <?php }?>


                    <?php if ($_smarty_tpl->tpl_vars['row']->value['orderstatus'] != 1 && ($_smarty_tpl->tpl_vars['product']->value['parentorderitemid'] == 0)) {?>
                        <?php if (($_smarty_tpl->tpl_vars['product']->value['dataavailable'] == 1)) {?>
                            <?php if ($_smarty_tpl->tpl_vars['row']->value['status'] != 0) {?>

                                <?php if ($_smarty_tpl->tpl_vars['product']->value['isShared'] == true) {?>
                                    <div class="linkAction" data-decorator="fnExecuteButtonAction" data-target="2" data-project-name="<?php echo htmlspecialchars((string)$_smarty_tpl->tpl_vars['product']->value['projectname'], ENT_QUOTES, 'UTF-8', true);?>
" data-application-name="<?php echo htmlspecialchars((string)$_smarty_tpl->tpl_vars['webbrandapplicationname']->value, ENT_QUOTES, 'UTF-8', true);?>
" data-project-ref="<?php echo $_smarty_tpl->tpl_vars['product']->value['projectref'];?>
" data-workflow-type="<?php echo $_smarty_tpl->tpl_vars['product']->value['workflowtype'];?>
" data-product-ident="<?php echo $_smarty_tpl->tpl_vars['product']->value['productindent'];?>
" data-wizard-mode="<?php echo $_smarty_tpl->tpl_vars['product']->value['wizardmode'];?>
">
                                        <div class="changeBtnText"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelUnshare');?>
</div>
                                        <div class="clear"></div>
                                    </div> <!-- linkAction -->
                                <?php } else { ?>                                     <div class="linkAction" data-decorator="fnExecuteButtonAction" data-target="2" data-project-name="<?php echo htmlspecialchars((string)$_smarty_tpl->tpl_vars['product']->value['projectname'], ENT_QUOTES, 'UTF-8', true);?>
" data-application-name="<?php echo htmlspecialchars((string)$_smarty_tpl->tpl_vars['webbrandapplicationname']->value, ENT_QUOTES, 'UTF-8', true);?>
" data-project-ref="<?php echo $_smarty_tpl->tpl_vars['product']->value['projectref'];?>
" data-workflow-type="<?php echo $_smarty_tpl->tpl_vars['product']->value['workflowtype'];?>
" data-product-ident="<?php echo $_smarty_tpl->tpl_vars['product']->value['productindent'];?>
" data-wizard-mode="<?php echo $_smarty_tpl->tpl_vars['product']->value['wizardmode'];?>
"style="display:none;">
                                        <div class="changeBtnText"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelUnshare');?>
</div>
                                        <div class="clear"></div>
                                    </div> <!-- linkAction -->
                                <?php }?> 
                                <?php if ($_smarty_tpl->tpl_vars['row']->value['origorderid'] == 0) {?>
                                    <div class="linkAction" data-decorator="fnExecuteButtonAction" data-target="0" data-project-name="<?php echo htmlspecialchars((string)$_smarty_tpl->tpl_vars['product']->value['projectname'], ENT_QUOTES, 'UTF-8', true);?>
" data-application-name="<?php echo htmlspecialchars((string)$_smarty_tpl->tpl_vars['webbrandapplicationname']->value, ENT_QUOTES, 'UTF-8', true);?>
" data-project-ref="<?php echo $_smarty_tpl->tpl_vars['product']->value['projectref'];?>
" data-workflow-type="<?php echo $_smarty_tpl->tpl_vars['product']->value['workflowtype'];?>
" data-product-ident="<?php echo $_smarty_tpl->tpl_vars['product']->value['productindent'];?>
" data-wizard-mode="<?php echo $_smarty_tpl->tpl_vars['product']->value['wizardmode'];?>
">
                                        <div class="changeBtnText"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelShare');?>
</div>
                                        <div class="changeBtnImg"><img class="navigationArrow" src="<?php echo $_smarty_tpl->tpl_vars['webroot']->value;?>
/images/icons/change-arrow.png" alt= ">" /></div>
                                        <div class="clear"></div>
                                    </div> <!-- linkAction -->
                                <?php }?> 
                            <?php } elseif ($_smarty_tpl->tpl_vars['product']->value['source'] == 1) {?> 
                                <?php if ($_smarty_tpl->tpl_vars['product']->value['isShared'] == true) {?>
                                    <div class="linkAction" data-decorator="fnExecuteButtonAction" data-target="2" data-project-name="<?php echo htmlspecialchars((string)$_smarty_tpl->tpl_vars['product']->value['projectname'], ENT_QUOTES, 'UTF-8', true);?>
" data-application-name="<?php echo htmlspecialchars((string)$_smarty_tpl->tpl_vars['webbrandapplicationname']->value, ENT_QUOTES, 'UTF-8', true);?>
" data-project-ref="<?php echo $_smarty_tpl->tpl_vars['product']->value['projectref'];?>
" data-workflow-type="<?php echo $_smarty_tpl->tpl_vars['product']->value['workflowtype'];?>
" data-product-ident="<?php echo $_smarty_tpl->tpl_vars['product']->value['productindent'];?>
" data-wizard-mode="<?php echo $_smarty_tpl->tpl_vars['product']->value['wizardmode'];?>
">
                                        <div class="changeBtnText"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelUnshare');?>
</div>
                                        <div class="clear"></div>
                                    </div> <!-- linkAction -->
                                <?php } else { ?>                                     <div class="linkAction" data-decorator="fnExecuteButtonAction" data-target="2" data-project-name="<?php echo htmlspecialchars((string)$_smarty_tpl->tpl_vars['product']->value['projectname'], ENT_QUOTES, 'UTF-8', true);?>
" data-application-name="<?php echo htmlspecialchars((string)$_smarty_tpl->tpl_vars['webbrandapplicationname']->value, ENT_QUOTES, 'UTF-8', true);?>
" data-project-ref="<?php echo $_smarty_tpl->tpl_vars['product']->value['projectref'];?>
" data-workflow-type="<?php echo $_smarty_tpl->tpl_vars['product']->value['workflowtype'];?>
" data-product-ident="<?php echo $_smarty_tpl->tpl_vars['product']->value['productindent'];?>
" data-wizard-mode="<?php echo $_smarty_tpl->tpl_vars['product']->value['wizardmode'];?>
" style="display:none;">
                                        <div class="changeBtnText"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelUnshare');?>
</div>
                                        <div class="clear"></div>
                                    </div> <!-- linkAction -->
                                <?php }?> 
                                <div class="linkAction" data-decorator="fnExecuteButtonAction" data-target="0" data-project-name="<?php echo htmlspecialchars((string)$_smarty_tpl->tpl_vars['product']->value['projectname'], ENT_QUOTES, 'UTF-8', true);?>
" data-application-name="<?php echo htmlspecialchars((string)$_smarty_tpl->tpl_vars['webbrandapplicationname']->value, ENT_QUOTES, 'UTF-8', true);?>
" data-project-ref="<?php echo $_smarty_tpl->tpl_vars['product']->value['projectref'];?>
" data-workflow-type="<?php echo $_smarty_tpl->tpl_vars['product']->value['workflowtype'];?>
" data-product-ident="<?php echo $_smarty_tpl->tpl_vars['product']->value['productindent'];?>
" data-wizard-mode="<?php echo $_smarty_tpl->tpl_vars['product']->value['wizardmode'];?>
">
                                    <div class="changeBtnText"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelShare');?>
</div>
                                    <div class="changeBtnImg"><img class="navigationArrow" src="<?php echo $_smarty_tpl->tpl_vars['webroot']->value;?>
/images/icons/change-arrow.png" alt= ">" /></div>
                                    <div class="clear"></div>
                                </div> <!-- linkAction -->

                            <?php }?> 
                        <?php }?> 
                        <?php if ($_smarty_tpl->tpl_vars['product']->value['previewsonline'] == 1) {?>
                            <div class="linkAction" data-decorator="fnShowPreview" data-url="<?php echo $_smarty_tpl->tpl_vars['webbrandweburl']->value;
echo htmlspecialchars((string)$_smarty_tpl->tpl_vars['product']->value['previewurl'], ENT_QUOTES, 'UTF-8', true);?>
&amp;ref=<?php echo $_smarty_tpl->tpl_vars['session']->value;?>
&amp;id=<?php echo $_smarty_tpl->tpl_vars['product']->value['id'];?>
">
                                <div class="changeBtnText"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ButtonPreview');?>
</div>
                                <div class="changeBtnImg"><img class="navigationArrow" src="<?php echo $_smarty_tpl->tpl_vars['webroot']->value;?>
/images/icons/change-arrow.png" alt= ">" /></div>
                                <div class="clear"></div>
                            </div> <!-- linkAction -->
                        <?php }?> 
                    <?php }?> 
                <?php if (($_smarty_tpl->tpl_vars['row']->value['orderstatus'] > 0)) {?>
                    <div class="linkAction btnWarning deleteOrderButton" id="deleteOrderButton" data-decorator="fnDeleteOrderLine" data-orderid="<?php echo $_smarty_tpl->tpl_vars['row']->value['orderid'];?>
" data-ordernumber="<?php echo $_smarty_tpl->tpl_vars['row']->value['ordernumber'];?>
" data-ref="<?php echo $_smarty_tpl->tpl_vars['session']->value;?>
" data-ssotoken="<?php echo $_smarty_tpl->tpl_vars['ssotoken']->value;?>
">
                        <div class="changeBtnText"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ButtonDelete');?>
</div>
                        <div class="changeBtnImg"></div>
                        <div class="clear"></div>
                    </div>
                <?php }?>



            </div> <!-- productDetailXXX -->

                    <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?> 
                <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?> 
            <?php }?> 
            <form id="submitform" name="submitform" method="post" action="#" accept-charset="utf-8">
                <input type="hidden" id="ref" name="ref" value="<?php echo $_smarty_tpl->tpl_vars['session']->value;?>
" />
                <input type="hidden" id="ssotoken" name="ssotoken" value="<?php echo $_smarty_tpl->tpl_vars['ssotoken']->value;?>
" />
                <input type="hidden" id="fsaction" name="fsaction" value="" />
                <input type="hidden" id="orderitemid" name="orderitemid" value="" />
                <input type="hidden" id="action" name="action" value="" />
                <input type="hidden" id="csrf_token" name="csrf_token" value="<?php echo smarty_function_csrf_token(array(),$_smarty_tpl);?>
" />
            </form>

        <?php }?> 
        </div> <!-- contentVisible -->

    </div> <!-- contentScrollCart -->

</div> <!-- orderDetailPanel -->

<div id="orderPreviewPanel" class="previewPanel">

</div> <!-- previewPanel -->

<?php }?> 
<!-- END YOUR ORDERS -->

<!-- ACCOUNT DETAILS -->

<?php if ($_smarty_tpl->tpl_vars['section']->value == 'accountdetails') {?>

    <?php if ($_smarty_tpl->tpl_vars['addressupdated']->value != 0) {?>

<div id="contentNavigationForm" class="contentNavigation">

    <div class="btnDoneTop" data-decorator="fnShowPanelAccount" data-visible="false" data-div-id="" id="backButton">
        <img class="backImage" src="<?php echo $_smarty_tpl->tpl_vars['webroot']->value;?>
/images/icons/back-arrow.png" alt="<" />
        <div class="btnDone"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ButtonCancel');?>
</div>
        <div class="clear"></div>
    </div>

</div> <!-- contentNavigation -->

    <?php }?>

<div id="contentRightScrollForm" class="contentScrollCart">

    <div class="contentVisible">

        <div class="pageLabel">
            <?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_MenuTitleAccountDetails');?>

        </div>

        <?php if ($_smarty_tpl->tpl_vars['message']->value != '') {?>

        <div class="subLabelMessage">
            <?php echo $_smarty_tpl->tpl_vars['message']->value;?>

        </div>

        <?php }?> 
        <div id="changeAccountDetailForm">
            <?php if ($_smarty_tpl->tpl_vars['customerupdateauthrequired']->value) {?>
                <div id="verifyPasswordFormContainer" style="display: none">
                    <div class="formLine1">
                        <label for="password"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelRenterPassword');?>
:</label>
                        <img src="<?php echo $_smarty_tpl->tpl_vars['brandroot']->value;?>
/images/asterisk.png" alt="*"/>
                    </div>

                    <div class="formLine2">
                        <input type="password" id="password" name="password" value="" class="middle" style="width:100%"/>
                        <img class="error_form_image" id="passwordcompulsory" src="<?php echo $_smarty_tpl->tpl_vars['brandroot']->value;?>
/images/asterisk.png" alt="*" />
                        <div class="clear"></div>
                    </div>
                </div>
            <?php }?>
            <div class="outerBox outerBoxPadding account-section">
                <div class="formLine1">
                    <label for="email"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelEmailAddress');?>
:</label>
                    <img src="<?php echo $_smarty_tpl->tpl_vars['brandroot']->value;?>
/images/asterisk.png" alt="*"/>
                </div>

                <div class="formLine2">
                    <input type="email" id="email_account" name="email_account" value="<?php echo $_smarty_tpl->tpl_vars['email']->value;?>
" class="middle" autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false"/>
                    <img class="error_form_image" id="emailcompulsory" src="<?php echo $_smarty_tpl->tpl_vars['brandroot']->value;?>
/images/asterisk.png" alt="*"/>
                    <div class="clear"></div>
                </div>

				<?php if ($_smarty_tpl->tpl_vars['showPendingMessage']->value == 1) {?>
				<div class="informationContainer">
					<p class="informationHeader"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_TitleEmailChangePending');?>
</p>
					<p class="informationMessage"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_MessageEmailChangePending');?>
</p>
				</div>
				<?php }?>
            </div>

            <div class="outerBox outerBoxPadding">

                <div id="ajaxdiv"></div>

                <div class="top_gap">

                    <div class="formLine1">
                        <label for="telephonenumber"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelTelephoneNumber');?>
:</label>
                        <img src="<?php echo $_smarty_tpl->tpl_vars['brandroot']->value;?>
/images/asterisk.png" alt="*"/>
                    </div>

                    <div class="formLine2">
                        <input type="tel" id="telephonenumber_account" name="telephonenumber_account" data-decorator="fnCJKHalfWidthFullWidthToASCII" value="<?php echo $_smarty_tpl->tpl_vars['telephonenumber']->value;?>
" autocorrect="off" autocapitalize="off" spellcheck="false"/>
                        <img class="error_form_image" id="telephonenumbercompulsory" src="<?php echo $_smarty_tpl->tpl_vars['brandroot']->value;?>
/images/asterisk.png" alt="*"/>
                        <div class="clear"></div>
                    </div>

                </div>
            </div>

        </div> <!-- outerBox outerBoxPadding -->

         <div class="paddingBtnBottomPage">

            <div class="btnAction btnContinue" data-decorator="fnVerifyAddress">
                <div class="btnContinueContent"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ButtonUpdate');?>
</div>
            </div>

        </div>

    </div <!-- contentVisible -->

</div> <!-- contentScrollCart -->

<?php }?>

<!-- END ACCOUNT DETAILS -->

<!-- CHANGE PASSWORD -->

<?php if ($_smarty_tpl->tpl_vars['section']->value == 'changepassword') {?>

<div id="contentNavigationForm" class="contentNavigation">

    <div class="btnDoneTop" data-decorator="fnShowPanelAccount" data-visible="false" data-div-id="" id="backButton">
        <img class="backImage" src="<?php echo $_smarty_tpl->tpl_vars['webroot']->value;?>
/images/icons/back-arrow.png" alt="<" />
        <div class="btnDone"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ButtonCancel');?>
</div>
        <div class="clear"></div>
    </div>

</div> <!-- contentNavigation -->

<div id="contentRightScrollForm" class="contentScrollCart">

    <div class="contentVisible">

        <div class="pageLabel">
            <?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_MenuTitleChangePassword');?>

        </div>

        <div id="changePasswordForm" class="outerBox outerBoxPadding">

            <div>

                <div class="formLine1">
                    <label for="oldpassword"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelCurrentPassword');?>
:</label>
                    <img src="<?php echo $_smarty_tpl->tpl_vars['brandroot']->value;?>
/images/asterisk.png" alt="*"/>
                </div>

                <div class="formLine2">
                    <input type="password" id="oldpassword" name="oldpassword" value="" class="middle" />
                    <img class="error_form_image" id="oldpasswordcompulsory" src="<?php echo $_smarty_tpl->tpl_vars['brandroot']->value;?>
/images/asterisk.png" alt="*" />
                    <div class="clear"></div>
                </div>

            </div>

            <div class="top_gap">

                <div class="formLine1">
                    <label for="newpassword"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelNewPassword');?>
: </label>
                    <img src="<?php echo $_smarty_tpl->tpl_vars['brandroot']->value;?>
/images/asterisk.png" alt="*"/>
                </div>

                <div class="formLine2">
                    <div class="password-input-wrap">
                        <input type="password" id="newpassword" name="newpassword" value="" class="middle" data-decorator="fnHandlePasswordStrength"/>
						<button type="button" class="password-visibility password-show" data-decorator="fnToggleNewPasword"></button>
                        <div class="progress-wrap">
                            <progress id="strengthvalue" value="0" min="0" max="5"></progress>
							<p><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelPasswordStrength');?>
: <span id="strengthtext"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelStartTyping');?>
</span></p>
                        </div>
                    </div>
                    <img class="error_form_image" id="newpasswordcompulsory" src="<?php echo $_smarty_tpl->tpl_vars['brandroot']->value;?>
/images/asterisk.png" alt="*" />
                    <div class="clear"></div>
                </div>

            </div>

        </div> <!-- outerBox outerBoxPadding -->

        <div class="paddingBtnBottomPage">

            <div class="btnAction btnContinue" data-decorator="fnCheckFormChangePassword">
                <div class="btnContinueContent"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ButtonUpdate');?>
</div>
            </div>

        </div>

    </div> <!-- contentVisible -->

</div> <!-- contentScrollCart -->

<?php }?>

<!-- END CHANGE PASSWORD -->

<!-- CHANGE PREFERENCES -->

<?php if ($_smarty_tpl->tpl_vars['section']->value == 'changepreferences') {?>

<div id="contentNavigationForm" class="contentNavigation">

    <div class="btnDoneTop" id="backButton" data-decorator="fnCheckFormChangePreferences">
        <img class="backImage" src="<?php echo $_smarty_tpl->tpl_vars['webroot']->value;?>
/images/icons/back-arrow.png" alt="<" />
        <div class="btnDone"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ButtonDone');?>
</div>
        <div class="clear"></div>
    </div>

</div> <!-- contentNavigation -->

<div id="contentRightScrollForm" class="contentScrollCart">

    <div class="contentVisible">

        <div class="pageLabel">
            <?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_MenuTitleChangePreferences');?>

        </div>

        <div class="outerBox">

            <ul class="marketingInfo">
                <li class="optionListNoBorder outerBoxPadding">
                    <input type="checkbox" name="sendmarketinginfo" id="subscribed" value="1" <?php if ($_smarty_tpl->tpl_vars['sendmarketinginfo']->value == 1) {?> checked="checked"<?php }?> />

                        <label class="listLabel" for="subscribed">
                            <span><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelMarketingSubscribe');?>
</span>
                        </label>
                    <div class="clear"></div>
                </li>
            </ul>

        </div> <!-- outerBox -->

    </div> <!-- contentVisible -->

</div> <!-- contentScrollCart -->

<?php }?>

<!-- END CHANGE PREFERENCES -->

<!-- OPEN EXISTING PROJECT -->

<?php if ($_smarty_tpl->tpl_vars['section']->value == 'existingonlineprojects') {?>

<div id="onlineMainPanel" class="onlinePanel">

    <div id="contentNavigationForm" class="contentNavigation">

        <div class="btnDoneTop" data-decorator="fnShowPanelAccount" data-visible="false" data-div-id="" id="backButton">
            <img class="backImage" src="<?php echo $_smarty_tpl->tpl_vars['webroot']->value;?>
/images/icons/back-arrow.png" alt="<" />
            <div class="btnDone"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_TitleMyAccount');?>
</div>
            <div class="clear"></div>
        </div>

    </div> <!-- contentNavigation -->

    <div id="contentRightScrollForm" class="contentScrollCart">

        <div class="contentVisible" id="contentContainer">

            <div class="pageLabel">
                <?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelOnlineProjects');?>

            </div>

            <?php if ($_smarty_tpl->tpl_vars['maintenancemode']->value == true) {?>

            <div class="outerBox outerBoxPadding">
               <?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ErrorMaintenanceMode');?>

            </div>

            <?php } else { ?>

                <?php if (sizeof($_smarty_tpl->tpl_vars['projects']->value) > 0) {?>
                    <?php if ($_smarty_tpl->tpl_vars['showpurgeall']->value) {?>
                        <div id="purgeAllMessage" class="purgeAllMessage">
                            <p><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_MessageProjectsFlaggedForPurge');?>
 <a href="#" id="purgeAllLink" data-decorator="purgeFlaggedProjects"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_MessageDeleteAllFlaggedProjects');?>
</a></p>
                        </div>
                    <?php }?>
                <div id="contentExistingProject">

                    <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['projects']->value, 'row', false, NULL, 'project', array (
));
$_smarty_tpl->tpl_vars['row']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['row']->value) {
$_smarty_tpl->tpl_vars['row']->do_else = false;
?>

                    <div class="clickable" id="contentItemBloc<?php echo $_smarty_tpl->tpl_vars['row']->value['projectref'];?>
" data-decorator="fnShowOnlineOptions" data-show="true" data-product-id="<?php echo $_smarty_tpl->tpl_vars['row']->value['projectref'];?>
">

                         <div class="outerBox outerBoxMarginBottom">

                            <div class="projectLabel">

                                <div class="orderProductLabel" id="orderProductLabel<?php echo $_smarty_tpl->tpl_vars['row']->value['projectref'];?>
">
                                    <?php echo $_smarty_tpl->tpl_vars['row']->value['name'];?>

                                </div> <!-- componentLabel -->

                                <div class="orderProductBtnDetail">
                                </div>

                                <div class="clear"></div>

                            </div> <!-- projectLabel -->

                            <div class="contentDescription">

                                <div class="descriptionProduct">
                                    <?php echo $_smarty_tpl->tpl_vars['row']->value['productname'];?>

                                </div>

                                <div class="orderDetail" id="orderDetail">
                                    <?php if ($_smarty_tpl->tpl_vars['row']->value['dateofpurge'] != '') {?>
                                        <span class="dateofpurge">
                                            <span class="label-purge-date"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_MessageProjectDueToBePurged');?>
 <?php echo $_smarty_tpl->tpl_vars['row']->value['dateofpurge'];?>
</span> <a href="#" class="keepProjectLink" data-decorator="fnKeepOnlineProject" data-projectref="<?php echo $_smarty_tpl->tpl_vars['row']->value['projectref'];?>
"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_MessageKeepProject');?>
</a>
                                            <br />
                                        </span>
                                    <?php }?>
                                    <span class="orderLabelMedium"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelCreated');?>
</span><?php echo $_smarty_tpl->tpl_vars['row']->value['datecreated'];?>


                        <?php if ($_smarty_tpl->tpl_vars['row']->value['statusdescription'] != '') {?>

                                    <br />
                                    <span class="orderLabelMedium"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelStatus');?>
:</span>
                                    <span id="statusDescription<?php echo $_smarty_tpl->tpl_vars['row']->value['projectref'];?>
" class="statusInProduction"><?php echo $_smarty_tpl->tpl_vars['row']->value['statusdescription'];?>
</span>

                        <?php }?>

                                </div> <!-- descriptionStatus -->

                            </div> <!-- contentDescription -->

                        </div> <!-- projectLine -->

                    </div> <!-- clickable -->

                    <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?> 
                </div>

                <?php } else { ?>

                <div class="outerBox outerBoxPadding">
                    <?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelNoOnlineProject');?>

                </div>

                <?php }?>

            <?php }?>

        </div> <!-- contentVisible -->

    </div> <!-- contentScrollCart -->

</div> <!-- onlinePanel -->

<div id="onlineDetailPanel" class="onlinePanel">

    <div id="contentNavigationDetail" class="contentNavigation">

        <div class="btnDoneTop" id="backButton" data-decorator="fnShowOnlineOptions" data-show="false" data-product-id="">
            <img class="backImage" src="<?php echo $_smarty_tpl->tpl_vars['webroot']->value;?>
/images/icons/back-arrow.png" alt="<" />
            <div class="btnDone"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelOnlineProjects');?>
</div>
            <div class="clear"></div>
        </div>

    </div> <!-- contentNavigation -->

    <div id="contentRightScrollDetail" class="contentScrollCart">

        <div class="contentVisible" id="contentRightScrollDetailVisible">

        <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['projects']->value, 'row', false, NULL, 'project', array (
));
$_smarty_tpl->tpl_vars['row']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['row']->value) {
$_smarty_tpl->tpl_vars['row']->do_else = false;
?>

            <div id="onlineProjectDetail<?php echo $_smarty_tpl->tpl_vars['row']->value['projectref'];?>
"
            data-projectname="<?php echo $_smarty_tpl->tpl_vars['row']->value['name'];?>
"
            data-productident="<?php echo $_smarty_tpl->tpl_vars['row']->value['productident'];?>
"
                data-workflowtype="<?php echo $_smarty_tpl->tpl_vars['row']->value['workflowtype'];?>
" style="display: none;">

                <div class="pageLabel" id="pageLabel<?php echo $_smarty_tpl->tpl_vars['row']->value['projectref'];?>
">
                    <?php echo $_smarty_tpl->tpl_vars['row']->value['name'];?>

                </div>

                <div class="outerBox outerBoxPadding outerBoxMarginBottom productDetailBloc">

                    <div class="nameProduct">
                        <?php echo $_smarty_tpl->tpl_vars['row']->value['productname'];?>

                    </div>

                    <div class="orderDetail" id="detailOrderDetail">
                         <span class="orderLabelMedium"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelCreated');?>
</span><?php echo $_smarty_tpl->tpl_vars['row']->value['datecreated'];?>


            <?php if ($_smarty_tpl->tpl_vars['row']->value['statusdescription'] != '') {?>

                        <br />
                        <span class="orderLabelMedium"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelStatus');?>
:</span>
                        <span id="detailStatusDescription<?php echo $_smarty_tpl->tpl_vars['row']->value['projectref'];?>
" class="statusInProduction"><?php echo $_smarty_tpl->tpl_vars['row']->value['statusdescription'];?>
</span>
            <?php }?>

                    </div> <!-- orderDetail -->

                 </div> <!-- outerBox outerBoxPadding outerBoxMarginBottom productDetailBloc -->

            <?php if ($_smarty_tpl->tpl_vars['row']->value['cancompleteorder'] == 1) {?>

                <div id="completeOrderButton">

                    <div class="btnAction btnContinue btnComplete" data-decorator="fnOnlineProjectsButtonAction" data-button="completeorder" data-wizard-mode="<?php echo $_smarty_tpl->tpl_vars['row']->value['wizardmode'];?>
" data-work-type="<?php echo $_smarty_tpl->tpl_vars['row']->value['workflowtype'];?>
">
                        <div class="btnContinueContent" ><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ButtonCompleteOrder');?>
</div>
                    </div>

                </div>

            <?php } else { ?>

				<?php if ($_smarty_tpl->tpl_vars['row']->value['canedit'] == 1) {?>

                <div id="continueOrderButton">

                    <div class="btnAction btnContinue btnComplete" data-decorator="fnOnlineProjectsButtonAction" data-button="continueediting" data-wizard-mode="<?php echo $_smarty_tpl->tpl_vars['row']->value['wizardmode'];?>
" data-work-type="<?php echo $_smarty_tpl->tpl_vars['row']->value['workflowtype'];?>
">
                        <div class="btnContinueContent"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ButtonContinueEditing');?>
</div>
                    </div>

                </div>

				<?php }?>

			<?php }?>

                <div class="linkOnlineAction">

            <?php if (($_smarty_tpl->tpl_vars['row']->value['cancompleteorder'] == 1) && ($_smarty_tpl->tpl_vars['row']->value['canedit'] == 1)) {?>

                    <div id="continueOrderButton" class="linkAction" data-decorator="fnOnlineProjectsButtonAction" data-button="continueediting" data-wizard-mode="<?php echo $_smarty_tpl->tpl_vars['row']->value['wizardmode'];?>
" data-work-type="<?php echo $_smarty_tpl->tpl_vars['row']->value['workflowtype'];?>
">

                        <div class="changeBtnText">
                            <?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ButtonContinueEditing');?>

                        </div>

                        <div class="changeBtnImg">
                            <img class="navigationArrow" src="<?php echo $_smarty_tpl->tpl_vars['webroot']->value;?>
/images/icons/change-arrow.png" alt= ">" />
                        </div>

                        <div class="clear"></div>

                    </div> <!-- linkAction -->

            <?php }?>

                    <div class="linkAction" data-decorator="fnOnlineProjectsButtonAction" data-button="duplicate" data-wizard-mode="<?php echo $_smarty_tpl->tpl_vars['row']->value['wizardmode'];?>
" data-work-type="<?php echo $_smarty_tpl->tpl_vars['row']->value['workflowtype'];?>
">

                        <div class="changeBtnText">
                            <?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ButtonDuplicateProject');?>

                        </div>

                        <div class="changeBtnImg">
                            <img class="navigationArrow" src="<?php echo $_smarty_tpl->tpl_vars['webroot']->value;?>
/images/icons/change-arrow.png" alt= ">" />
                        </div>

                        <div class="clear"></div>

                    </div> <!-- linkAction -->

                    <div class="linkAction" data-decorator="fnOnlineProjectsButtonAction" data-button="rename" data-wizard-mode="<?php echo $_smarty_tpl->tpl_vars['row']->value['wizardmode'];?>
" data-work-type="<?php echo $_smarty_tpl->tpl_vars['row']->value['workflowtype'];?>
">

                        <div class="changeBtnText">
                            <?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ButtonRenameProject');?>

                        </div>

                        <div class="changeBtnImg">
                            <img class="navigationArrow" src="<?php echo $_smarty_tpl->tpl_vars['webroot']->value;?>
/images/icons/change-arrow.png" alt= ">" />
                        </div>

                        <div class="clear"></div>

                    </div> <!-- linkAction -->

                <?php if ($_smarty_tpl->tpl_vars['row']->value['canedit'] == 1) {?>
                    <div class="linkAction" data-decorator="fnOnlineProjectsButtonAction" data-button="share" data-wizard-mode="<?php echo $_smarty_tpl->tpl_vars['row']->value['wizardmode'];?>
" data-work-type="<?php echo $_smarty_tpl->tpl_vars['row']->value['workflowtype'];?>
">

                        <div class="changeBtnText">
                            <?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ButtonShareProject');?>

                        </div>

                        <div class="changeBtnImg">
                            <img class="navigationArrow" src="<?php echo $_smarty_tpl->tpl_vars['webroot']->value;?>
/images/icons/change-arrow.png" alt= ">" />
                        </div>

                        <div class="clear"></div>

                    </div> <!-- linkAction -->
                <?php }?>

                </div>

                    <?php if ($_smarty_tpl->tpl_vars['row']->value['candelete'] == 1) {?>

                <div id="deleteOrderButton" class="linkAction" data-decorator="fnOnlineProjectsButtonAction" data-button="delete" data-wizard-mode="<?php echo $_smarty_tpl->tpl_vars['row']->value['wizardmode'];?>
" data-work-type="<?php echo $_smarty_tpl->tpl_vars['row']->value['workflowtype'];?>
">

                   <div class="deleteBtnText">
                       <?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ButtonDeleteProject');?>

                   </div>

               </div> <!-- linkAction -->

                    <?php }?>

            </div> <!-- productDetailXXX -->

            <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?> 
        </div> <!-- contentVisible -->

    </div> <!-- contentScrollCart -->

</div> <!-- orderDetailPanel -->

<?php }?>


<?php if (($_smarty_tpl->tpl_vars['section']->value == 'existingonlineprojects') || ($_smarty_tpl->tpl_vars['section']->value == 'yourorders')) {?>

<div id="onlineNameFormPanel" class="onlinePanel">

    <div id="contentNavigationNameForm" class="contentNavigation">

        <div class="btnDoneTop" id="backButton" data-decorator="fnShowOPActionPanel" data-show="false">
            <img class="backImage" src="<?php echo $_smarty_tpl->tpl_vars['webroot']->value;?>
/images/icons/back-arrow.png" alt="<" />
            <div class="btnDone"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ButtonBack');?>
</div>
            <div class="clear"></div>
        </div>

    </div> <!-- contentNavigation -->

    <div id="contentRightScrollNameForm" class="contentScrollCart">

        <div class="contentVisible">

            <div class="pageLabel" id="opActionPanelTitle">
            </div>

            <div class="outerBox outerBoxPadding">

                <div id="opActionPanelLabel">
                </div>

                <div id="sharelink-tip" class="tip-popout">
                    <p><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ToolTipLinkCopied');?>
</p>
                </div>
                <div class="containerInputForm">
                    <input type="text" name="projectname" id="projectname" value="" maxlength="75" />
                </div>

            </div> <!-- outerBox outerBoxPadding -->

            <div class="paddingBtnBottomPage">
                <div class="btnAction btnContinue" id="opActionPanelBtnAction">
                    <div class="btnContinueContent" id="opActionPanelBtn"></div>
                </div>
            </div>

        </div> <!-- contentVisible -->

    </div> <!-- contentScrollCart -->

</div> <!-- onlineNameFormPanel -->

<?php }?>

<!-- END OPEN EXISTING PROJECT -->
<?php }
}
