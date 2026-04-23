<?php
/* Smarty version 4.5.3, created on 2026-03-14 01:34:40
  from 'C:\TAOPIX\MediaAlbumWeb\templates\order\orderfooter_large.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.5.3',
  'unifunc' => 'content_69b4bb30621d12_74780465',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'e9fa0b1a8656975fcad186d1977f0eecc41d5ba4' => 
    array (
      0 => 'C:\\TAOPIX\\MediaAlbumWeb\\templates\\order\\orderfooter_large.tpl',
      1 => 1729602708,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_69b4bb30621d12_74780465 (Smarty_Internal_Template $_smarty_tpl) {
if ((sizeof($_smarty_tpl->tpl_vars['orderfootersections']->value) > 0) || (sizeof($_smarty_tpl->tpl_vars['orderfootercheckboxes']->value) > 0)) {?>
    <?php $_smarty_tpl->_assignInScope('multilinedesc', "false");?>
    <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['orderfootersections']->value, 'section');
$_smarty_tpl->tpl_vars['section']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['section']->value) {
$_smarty_tpl->tpl_vars['section']->do_else = false;
?>         <?php if ($_smarty_tpl->tpl_vars['section']->value['showcomponentname'] == true) {?>
            <?php if (!(isset($_smarty_tpl->tpl_vars['bTitleOrder']->value))) {?>
    <?php if ($_smarty_tpl->tpl_vars['call_action']->value == 'init') {?>
<div class="orderFooter" id="orderFooter">
    <?php }?>
    <div class="sectionLabelLegendFooter">
        <?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelAdditionalItems');?>

		<span class="linkToggleFooter">
            <?php if ($_smarty_tpl->tpl_vars['stage']->value == 'qty') {?>
            <span id="link_footer" data-decorator="fnToggleGeneric" data-lineid="footer" data-idelm="contentFooter" data-colour="white"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_OrderHide');?>
</span>
            <?php } else { ?>
            <span id="link_footer" data-decorator="fnToggleGeneric" data-lineid="footer" data-idelm="contentFooter" data-colour="white"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_OrderShow');?>
</span>
            <?php }?>
		</span>
    </div>
    <div id="contentFooter" class="content" <?php if ($_smarty_tpl->tpl_vars['stage']->value == 'payment') {?>style="display:none;"<?php }?>>
                <?php $_smarty_tpl->_assignInScope('bTitleOrder', "true");?>
            <?php }?>
        <div id="componentrow_<?php echo $_smarty_tpl->tpl_vars['section']->value['orderlineid'];?>
" class="componentbloc">
			<div class="section-title-header">
			<?php if (($_smarty_tpl->tpl_vars['section']->value['sectionlabel'] != '')) {?>
				<span class="section-category-name"><?php echo $_smarty_tpl->tpl_vars['section']->value['sectionlabel'];?>
:</span> <span class="section-category-prompt"><?php echo $_smarty_tpl->tpl_vars['section']->value['prompt'];?>
</span>
			<?php }?>
			</div>

            <div class="componentrow">
            <?php if ($_smarty_tpl->tpl_vars['section']->value['haspreview'] > 0) {?>
                <img class="component-preview" src="<?php echo htmlspecialchars((string)$_smarty_tpl->tpl_vars['section']->value['componentpreviewsrc'], ENT_QUOTES, 'UTF-8', true);?>
" alt=""/>
                <div class="componentSectionTitle">
            <?php } else { ?>
                <div class="componentSectionTitleLong">
            <?php }?>
                    <div class="componentContentText">
                        <div class="section-title"><?php echo $_smarty_tpl->tpl_vars['section']->value['itemcomponentname'];?>
</div>
            <?php if ($_smarty_tpl->tpl_vars['section']->value['haspreview'] > 0) {?>
                <?php if (!empty($_smarty_tpl->tpl_vars['section']->value['itemcomponentinfo'])) {?>
                        <div class="section-info">
                            <?php echo $_smarty_tpl->tpl_vars['section']->value['itemcomponentinfo'];?>

                        </div>
                    <?php $_smarty_tpl->_assignInScope('multilinedesc', "true");?>
                <?php }?>
                <?php if (!empty($_smarty_tpl->tpl_vars['section']->value['itemcomponentmoreinfolinkurl'])) {?>
                        <div class="subsection-moreinfo">
                            <a href="<?php echo $_smarty_tpl->tpl_vars['section']->value['itemcomponentmoreinfolinkurl'];?>
" target="_blank"><?php echo $_smarty_tpl->tpl_vars['section']->value['itemcomponentmoreinfolinktext'];?>
</a>
                        </div>
                    <?php $_smarty_tpl->_assignInScope('multilinedesc', "true");?>
                <?php }?>
                <?php if (!empty($_smarty_tpl->tpl_vars['section']->value['itemcomponentpriceinfo'])) {?>
                        <div class="section-info">
                            <?php echo $_smarty_tpl->tpl_vars['section']->value['itemcomponentpriceinfo'];?>

                        </div>
                    <?php $_smarty_tpl->_assignInScope('multilinedesc', "true");?>
                <?php }?>
            <?php } else { ?>
                <?php if (!empty($_smarty_tpl->tpl_vars['section']->value['itemcomponentinfo'])) {?>
                        <div class="section-info-long">
                            <?php echo $_smarty_tpl->tpl_vars['section']->value['itemcomponentinfo'];?>

                        </div>
                    <?php $_smarty_tpl->_assignInScope('multilinedesc', "true");?>
                <?php }?>
                <?php if (!empty($_smarty_tpl->tpl_vars['section']->value['itemcomponentmoreinfolinkurl'])) {?>
                        <div class="subsection-moreinfo-long">
                            <a href="<?php echo $_smarty_tpl->tpl_vars['section']->value['itemcomponentmoreinfolinkurl'];?>
" target="_blank"><?php echo $_smarty_tpl->tpl_vars['section']->value['itemcomponentmoreinfolinktext'];?>
</a>
                        </div>
                    <?php $_smarty_tpl->_assignInScope('multilinedesc', "true");?>
                <?php }?>
                <?php if (!empty($_smarty_tpl->tpl_vars['section']->value['itemcomponentpriceinfo'])) {?>
                        <div class="section-info-long">
                            <?php echo $_smarty_tpl->tpl_vars['section']->value['itemcomponentpriceinfo'];?>

                        </div>
                    <?php $_smarty_tpl->_assignInScope('multilinedesc', "true");?>
                <?php }?>
            <?php }?>
                        <div class="clear"></div>
                    </div>
            <!-- START add-edit-change-remove links -->
            <?php if ($_smarty_tpl->tpl_vars['stage']->value == 'qty') {?>
                <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['section']->value['itemcomponentbuttons'], 'button');
$_smarty_tpl->tpl_vars['button']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['button']->value) {
$_smarty_tpl->tpl_vars['button']->do_else = false;
?>
                    <div class="contentBtn btnRight <?php if ($_smarty_tpl->tpl_vars['multilinedesc']->value == "true") {?>paddingCenter<?php }?>" data-decorator="<?php echo $_smarty_tpl->tpl_vars['button']->value['action'];?>
" data-orderlineid="-1" data-sectionlineid="<?php echo $_smarty_tpl->tpl_vars['section']->value['orderlineid'];?>
">
                        <div class="btn-white-left" ></div>
                        <div class="btn-white-middle <?php echo $_smarty_tpl->tpl_vars['button']->value['class'];?>
"><?php echo $_smarty_tpl->tpl_vars['button']->value['label'];?>
</div>
                        <div class="btn-white-right"></div>
                    </div>
                    <div class="clear"></div>
                <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
            <?php }?>
            <!-- END add-edit-change-remove links -->
                </div>
            <?php if ($_smarty_tpl->tpl_vars['section']->value['pricingmodel'] == 7 || $_smarty_tpl->tpl_vars['section']->value['pricingmodel'] == 8) {?>
                <div class="quantity <?php if ($_smarty_tpl->tpl_vars['multilinedesc']->value == "true") {?>paddingCenter<?php }?>">
                <?php if (($_smarty_tpl->tpl_vars['stage']->value == 'payment')) {?>
                    <span class="quantityText"><?php echo $_smarty_tpl->tpl_vars['section']->value['quantity'];?>
</span>
                <?php } else { ?>
                    <input id="hiddeqty_<?php echo $_smarty_tpl->tpl_vars['section']->value['orderlineid'];?>
" type="hidden" class="hiddeqtyCpt" value="<?php echo $_smarty_tpl->tpl_vars['section']->value['quantity'];?>
"/>
                    <?php if (empty($_smarty_tpl->tpl_vars['section']->value['itemqtydropdown'])) {?>
                    <input id="itemqty_<?php echo $_smarty_tpl->tpl_vars['section']->value['orderlineid'];?>
" type="text" class="quantity" maxlength="8" value="<?php echo $_smarty_tpl->tpl_vars['section']->value['quantity'];?>
" data-decorator="fnUpdateComponentQty" data-lineid="<?php echo $_smarty_tpl->tpl_vars['section']->value['orderlineid'];?>
" data-itemqty="<?php echo $_smarty_tpl->tpl_vars['section']->value['itemqty'];?>
" data-trigger="keyup" />
                    <img class="refresh" src="<?php echo $_smarty_tpl->tpl_vars['brandroot']->value;?>
/images/icons/refresh.png" alt="<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelOrderUpdateItemTotal');?>
" title="<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelOrderUpdateItemTotal');?>
" data-decorator="fnUpdateComponentQty" data-lineid="<?php echo $_smarty_tpl->tpl_vars['section']->value['orderlineid'];?>
" data-itemqty="<?php echo $_smarty_tpl->tpl_vars['section']->value['itemqty'];?>
" data-trigger="click" />
                    <?php } else { ?>
                    <select id="itemqty_<?php echo $_smarty_tpl->tpl_vars['section']->value['orderlineid'];?>
" class="" data-decorator="fnUpdateComponentQty" data-lineid="<?php echo $_smarty_tpl->tpl_vars['section']->value['orderlineid'];?>
" data-itemqty="<?php echo $_smarty_tpl->tpl_vars['section']->value['itemqty'];?>
" data-trigger="change">
                        <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['section']->value['itemqtydropdown'], 'qtyValue');
$_smarty_tpl->tpl_vars['qtyValue']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['qtyValue']->value) {
$_smarty_tpl->tpl_vars['qtyValue']->do_else = false;
?>
                        <option <?php if ($_smarty_tpl->tpl_vars['qtyValue']->value == $_smarty_tpl->tpl_vars['section']->value['quantity']) {?>selected="selected"<?php }?> value=<?php echo $_smarty_tpl->tpl_vars['qtyValue']->value;?>
><?php echo $_smarty_tpl->tpl_vars['qtyValue']->value;?>
</option>
                        <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
                    </select>
                    <?php }?>
                <?php }?>
                </div>
            <?php }?>
                <div class="component-price <?php if ($_smarty_tpl->tpl_vars['multilinedesc']->value == "true") {?>paddingCenter<?php }?>">
                    <?php echo $_smarty_tpl->tpl_vars['section']->value['totalsell'];?>

                </div>
                <?php if ($_smarty_tpl->tpl_vars['multilinedesc']->value) {?>
                    <?php $_smarty_tpl->_assignInScope('multilinedesc', "false");?>
                <?php }?>
                <div class="clear"></div>
            </div>
            <?php if ($_smarty_tpl->tpl_vars['section']->value['metadatahtml']) {?>
            <div id="metadatarow_<?php echo $_smarty_tpl->tpl_vars['section']->value['orderlineid'];?>
" class="component-metadata">
                <?php echo $_smarty_tpl->tpl_vars['section']->value['metadatahtml'];?>

            </div>
            <?php }?>
            <?php if ($_smarty_tpl->tpl_vars['stage']->value == 'payment') {?>
                <!-- VALUE OFF TOTAL VOUCHER -->
            <div class="line-total">
                <?php if ((($_smarty_tpl->tpl_vars['vouchersection']->value == 'TOTAL') && (($_smarty_tpl->tpl_vars['differenttaxrates']->value) && (!$_smarty_tpl->tpl_vars['specialvouchertype']->value)))) {?>
                    <?php if (($_smarty_tpl->tpl_vars['section']->value['discountvalueraw'] > 0)) {?>
                <div class="line-sub-total">
                    <span class="discount-heading"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelSubTotal');?>
:</span>
                    <span class="discount-price"><?php echo $_smarty_tpl->tpl_vars['section']->value['totalsell'];?>
</span>
                </div>
                <div class="line-sub-total">
                    <span class="discount-heading"><?php echo $_smarty_tpl->tpl_vars['section']->value['discountname'];?>
:</span>
                    <span class="discount-price"><?php echo $_smarty_tpl->tpl_vars['section']->value['discountvalue'];?>
</span>
                </div>
                    <?php }?>
                    <?php if ((!$_smarty_tpl->tpl_vars['showpriceswithtax']->value)) {?>
                        <!-- SHOWZEROTAX OR DONT SHOWZEROTAX AND THE TAX > 0 -->
                        <?php if ((($_smarty_tpl->tpl_vars['showzerotax']->value) || ((!$_smarty_tpl->tpl_vars['showzerotax']->value) && ($_smarty_tpl->tpl_vars['section']->value['displaytaxraw'] > 0)))) {?>
                <div class="line-sub-total">
                    <span class="discount-heading"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelSubTotal');?>
:</span>
                    <span class="discount-price"><?php echo $_smarty_tpl->tpl_vars['section']->value['subtotal'];?>
</span>
                </div>
                <div class="line-sub-total">
                    <span class="discount-heading"><?php echo $_smarty_tpl->tpl_vars['section']->value['taxratename'];?>
 (<?php echo $_smarty_tpl->tpl_vars['section']->value['taxrate'];?>
%):</span>
                    <span class="discount-price"><?php echo $_smarty_tpl->tpl_vars['section']->value['displaytax'];?>
</span>
                </div>
                <div class="line-sub-total">
                    <span class="discount-heading"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelOrderItemListItemTotal');?>
:</span>
                    <span class="discount-price"><?php echo $_smarty_tpl->tpl_vars['section']->value['displayprice'];?>
</span>
                </div>
                        <?php } else { ?>
                <div class="line-sub-total">
                    <span class="discount-heading"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelOrderItemListItemTotal');?>
:</span>
                    <span class="discount-price"><?php echo $_smarty_tpl->tpl_vars['section']->value['displayprice'];?>
</span>
                </div>
                        <?php }?>
                    <?php } else { ?>
                <div class="line-sub-total">
                    <span class="discount-heading"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelOrderItemListItemTotal');?>
:</span>
                    <span class="discount-price"><?php echo $_smarty_tpl->tpl_vars['section']->value['subtotal'];?>
</span>
                </div>
                        <!-- SHOWZEROTAX OR DONT SHOWZEROTAX AND THE TAX > 0 -->
                        <?php if (($_smarty_tpl->tpl_vars['showtaxbreakdown']->value)) {?>
                            <?php if ((($_smarty_tpl->tpl_vars['showzerotax']->value) || ((!$_smarty_tpl->tpl_vars['showzerotax']->value) && ($_smarty_tpl->tpl_vars['section']->value['displaytaxraw'] > 0)))) {?>
                <div class="line-sub-total-small-bottom"><?php echo $_smarty_tpl->tpl_vars['section']->value['includesitemtaxtext'];?>
</div>
                            <?php }?>
                        <?php }?>
                    <?php }?>
                <?php } else { ?>
                    <!-- DIFFERNETTAXRATES AND SHOWPRICES WITH TAX -->
                    <?php if ((($_smarty_tpl->tpl_vars['differenttaxrates']->value) && ($_smarty_tpl->tpl_vars['showpriceswithtax']->value))) {?>
                        <?php if ((($_smarty_tpl->tpl_vars['vouchersection']->value == 'TOTAL') && ($_smarty_tpl->tpl_vars['specialvouchertype']->value))) {?>
                    <div class="line-sub-total">
                        <span class="discount-heading"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelSubTotal');?>
:</span>
                        <span class="discount-price"><?php echo $_smarty_tpl->tpl_vars['section']->value['totalsell'];?>
</span>
                    </div>
                            <?php if (($_smarty_tpl->tpl_vars['section']->value['discountvalueraw'] > 0)) {?>
                    <div class="line-sub-total">
                        <span class="discount-heading"><?php echo $_smarty_tpl->tpl_vars['section']->value['discountname'];?>
:</span>
                        <span class="discount-price"><?php echo $_smarty_tpl->tpl_vars['section']->value['discountvalue'];?>
</span>
                    </div>
                            <!-- SHOWZEROTAX OR DONT SHOWZEROTAX AND THE TAX > 0 -->
                            <?php if ((($_smarty_tpl->tpl_vars['showzerotax']->value) || ((!$_smarty_tpl->tpl_vars['showzerotax']->value) && ($_smarty_tpl->tpl_vars['section']->value['displaytaxraw'] > 0)))) {?>
                    <div class="line-sub-total">
                        <span class="discount-heading"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelSubTotal');?>
:</span>
                        <span class="discount-price"><?php echo $_smarty_tpl->tpl_vars['section']->value['subtotal'];?>
</span>
                    </div>
                            <?php }?>
                            <?php }?>
                            <!-- SHOWZEROTAX OR DONT SHOWZEROTAX AND THE TAX > 0 -->
                            <?php if ((($_smarty_tpl->tpl_vars['showzerotax']->value) || ((!$_smarty_tpl->tpl_vars['showzerotax']->value) && ($_smarty_tpl->tpl_vars['section']->value['displaytaxraw'] > 0)))) {?>
                    <div class="line-sub-total">
                        <span class="discount-heading"><?php echo $_smarty_tpl->tpl_vars['section']->value['taxratename'];?>
 (<?php echo $_smarty_tpl->tpl_vars['section']->value['taxrate'];?>
%):</span>
                        <span class="discount-price"><?php echo $_smarty_tpl->tpl_vars['section']->value['displaytax'];?>
</span>
                    </div>
                            <?php }?>
                    <div class="line-sub-total">
                        <span class="discount-heading"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelOrderItemListItemTotal');?>
:</span>
                        <span class="discount-price"><?php echo $_smarty_tpl->tpl_vars['section']->value['displayprice'];?>
</span>
                    </div>
                        <?php } else { ?>
                    <div class="line-sub-total">
                        <span class="discount-heading"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelOrderItemListItemTotal');?>
:</span>
                        <span class="discount-price"><?php echo $_smarty_tpl->tpl_vars['section']->value['subtotal'];?>
</span>
                    </div>
                            <!-- SHOWTAXBREAKDOWN -->
                            <?php if (($_smarty_tpl->tpl_vars['showtaxbreakdown']->value)) {?>
                                <?php if ((($_smarty_tpl->tpl_vars['showzerotax']->value) || ((!$_smarty_tpl->tpl_vars['showzerotax']->value) && ($_smarty_tpl->tpl_vars['section']->value['displaytaxraw'] > 0)))) {?>
                    <div class="line-sub-total-small-bottom"><?php echo $_smarty_tpl->tpl_vars['section']->value['includesitemtaxtext'];?>
</div>
                                <?php }?>
                            <?php }?>
                        <?php }?>
                    <?php }?>
                    <!-- DIFFERNETTAXRATES AND DONT SHOWPRICESWITHTAX -->
                    <?php if ((($_smarty_tpl->tpl_vars['differenttaxrates']->value) && (!$_smarty_tpl->tpl_vars['showpriceswithtax']->value))) {?>
                        <?php if ((($_smarty_tpl->tpl_vars['vouchersection']->value == 'TOTAL') && ($_smarty_tpl->tpl_vars['specialvouchertype']->value))) {?>
                    <div class="line-sub-total">
                        <span class="discount-heading"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelSubTotal');?>
:</span>
                        <span class="discount-price"><?php echo $_smarty_tpl->tpl_vars['section']->value['totalsell'];?>
</span>
                    </div>
                            <?php if (($_smarty_tpl->tpl_vars['section']->value['discountvalueraw'] > 0)) {?>
                    <div class="line-sub-total">
                        <span class="discount-heading"><?php echo $_smarty_tpl->tpl_vars['section']->value['discountname'];?>
:</span>
                        <span class="discount-price"><?php echo $_smarty_tpl->tpl_vars['section']->value['discountvalue'];?>
</span>
                    </div>
                            <!-- SHOWZEROTAX OR DONT SHOWZEROTAX AND THE TAX > 0 -->
                            <?php if ((($_smarty_tpl->tpl_vars['showzerotax']->value) || ((!$_smarty_tpl->tpl_vars['showzerotax']->value) && ($_smarty_tpl->tpl_vars['section']->value['displaytaxraw'] > 0)))) {?>
                    <div class="line-sub-total">
                        <span class="discount-heading"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelSubTotal');?>
:</span>
                        <span class="discount-price"><?php echo $_smarty_tpl->tpl_vars['section']->value['subtotal'];?>
</span>
                    </div>
                            <?php }?>
                            <?php }?>
                            <!-- SHOWZEROTAX OR DONT SHOWZEROTAX AND THE TAX > 0 -->
                            <?php if ((($_smarty_tpl->tpl_vars['showzerotax']->value) || ((!$_smarty_tpl->tpl_vars['showzerotax']->value) && ($_smarty_tpl->tpl_vars['section']->value['displaytaxraw'] > 0)))) {?>
                    <div class="line-sub-total">
                        <span class="discount-heading"><?php echo $_smarty_tpl->tpl_vars['section']->value['taxratename'];?>
 (<?php echo $_smarty_tpl->tpl_vars['section']->value['taxrate'];?>
%):</span>
                        <span class="discount-price"><?php echo $_smarty_tpl->tpl_vars['section']->value['displaytax'];?>
</span>
                    </div>
                            <?php }?>
                    <div class="line-sub-total">
                        <span class="discount-heading"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelOrderItemListItemTotal');?>
:</span>
                        <span class="discount-price"><?php echo $_smarty_tpl->tpl_vars['section']->value['displayprice'];?>
</span>
                    </div>
                        <?php } else { ?>
                            <!-- SHOWZEROTAX OR DONT SHOWZEROTAX AND THE TAX > 0 -->
                            <?php if ((($_smarty_tpl->tpl_vars['showzerotax']->value) || ((!$_smarty_tpl->tpl_vars['showzerotax']->value) && ($_smarty_tpl->tpl_vars['section']->value['displaytaxraw'] > 0)))) {?>
                    <div class="line-sub-total">
                        <span class="discount-heading"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelSubTotal');?>
:</span>
                        <span class="discount-price"><?php echo $_smarty_tpl->tpl_vars['section']->value['totalsell'];?>
</span>
                    </div>
                    <div class="line-sub-total">
                        <span class="discount-heading"><?php echo $_smarty_tpl->tpl_vars['section']->value['taxratename'];?>
 (<?php echo $_smarty_tpl->tpl_vars['section']->value['taxrate'];?>
%):</span>
                        <span class="discount-price"><?php echo $_smarty_tpl->tpl_vars['section']->value['displaytax'];?>
</span>
                    </div>
                    <div class="line-sub-total">
                        <span class="discount-heading"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelOrderItemListItemTotal');?>
:</span>
                        <span class="discount-price"><?php echo $_smarty_tpl->tpl_vars['section']->value['displayprice'];?>
</span>
                    </div>
                            <?php } else { ?>
                    <div class="line-sub-total">
                        <span class="discount-heading"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelOrderItemListItemTotal');?>
:</span>
                        <span class="discount-price"><?php echo $_smarty_tpl->tpl_vars['section']->value['displayprice'];?>
</span>
                    </div>
                            <?php }?>
                        <?php }?>

                    <?php }?>
                    <!-- NOT DIFFERNETTAXRATES -->
                    <?php if ((!$_smarty_tpl->tpl_vars['differenttaxrates']->value)) {?>
                            <?php if ((($_smarty_tpl->tpl_vars['section']->value['discountvalueraw'] > 0) && ($_smarty_tpl->tpl_vars['applyVoucherAsLineDiscount']->value))) {?>
            <div class="line-sub-total">
                <span class="discount-heading"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelSubTotal');?>
:</span>
                <span class="discount-price"><?php echo $_smarty_tpl->tpl_vars['section']->value['totalsell'];?>
</span>
            </div>
            <div class="line-sub-total">
                <span class="discount-heading"><?php echo $_smarty_tpl->tpl_vars['section']->value['discountname'];?>
:</span>
                <span class="discount-price"><?php echo $_smarty_tpl->tpl_vars['section']->value['discountvalue'];?>
</span>
            </div>
                            <?php }?>
                            <div class="line-sub-total">
                    <span class="discount-heading"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelOrderItemListItemTotal');?>
:</span>
                    <span class="discount-price"><?php echo $_smarty_tpl->tpl_vars['section']->value['subtotal'];?>
</span>
                </div>
                    <?php }?>
                <?php }?>
            </div>
            <?php }?>

            <!-- sub-sections of order footer component start -->
        <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['section']->value['subsections'], 'subsection');
$_smarty_tpl->tpl_vars['subsection']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['subsection']->value) {
$_smarty_tpl->tpl_vars['subsection']->do_else = false;
?>             <?php if ($_smarty_tpl->tpl_vars['subsection']->value['showcomponentname'] == true) {?>
        <div id="componentrow_<?php echo $_smarty_tpl->tpl_vars['subsection']->value['orderlineid'];?>
" class="subsection">
            <div class="subsectionBloc">
				<div class="section-title-header">
				<?php if (($_smarty_tpl->tpl_vars['subsection']->value['sectionlabel'] != '')) {?>
					<span class="section-category-name"><?php echo $_smarty_tpl->tpl_vars['subsection']->value['sectionlabel'];?>
:</span> <span class="section-category-prompt"><?php echo $_smarty_tpl->tpl_vars['subsection']->value['prompt'];?>
</span>
				<?php }?>
				</div>
                <?php if ($_smarty_tpl->tpl_vars['subsection']->value['haspreview'] > 0) {?>
                <img class="component-preview" src="<?php echo htmlspecialchars((string)$_smarty_tpl->tpl_vars['subsection']->value['componentpreviewsrc'], ENT_QUOTES, 'UTF-8', true);?>
" alt="" />
                <div class="componentSubSectionTitle">
                <?php } else { ?>
                <div class="componentSubSectionTitleLong">
                <?php }?>
                    <div class="componentContentText">
                        <div class="section-title"><?php echo $_smarty_tpl->tpl_vars['subsection']->value['itemcomponentname'];?>
</div>
                <?php if ($_smarty_tpl->tpl_vars['subsection']->value['haspreview'] > 0) {?>
                    <?php if (!empty($_smarty_tpl->tpl_vars['subsection']->value['itemcomponentinfo'])) {?>
                        <div class="subsection-info">
                            <?php echo $_smarty_tpl->tpl_vars['subsection']->value['itemcomponentinfo'];?>

                        </div>
                        <?php $_smarty_tpl->_assignInScope('multilinedesc', "true");?>
                    <?php }?>
                    <?php if (!empty($_smarty_tpl->tpl_vars['subsection']->value['itemcomponentmoreinfolinkurl'])) {?>
                        <div class="subsection-moreinfo">
                            <a href="<?php echo $_smarty_tpl->tpl_vars['subsection']->value['itemcomponentmoreinfolinkurl'];?>
" target="_blank"><?php echo $_smarty_tpl->tpl_vars['subsection']->value['itemcomponentmoreinfolinktext'];?>
</a>
                        </div>
                        <?php $_smarty_tpl->_assignInScope('multilinedesc', "true");?>
                    <?php }?>
                    <?php if (!empty($_smarty_tpl->tpl_vars['subsection']->value['itemcomponentpriceinfo'])) {?>
                        <div class="subsection-info">
                            <?php echo $_smarty_tpl->tpl_vars['subsection']->value['itemcomponentpriceinfo'];?>

                        </div>
                        <?php $_smarty_tpl->_assignInScope('multilinedesc', "true");?>
                    <?php }?>
                <?php } else { ?>
                    <?php if (!empty($_smarty_tpl->tpl_vars['subsection']->value['itemcomponentinfo'])) {?>
                        <div class="subsection-info-long">
                            <?php echo $_smarty_tpl->tpl_vars['subsection']->value['itemcomponentinfo'];?>

                        </div>
                        <?php $_smarty_tpl->_assignInScope('multilinedesc', "true");?>
                    <?php }?>
                    <?php if (!empty($_smarty_tpl->tpl_vars['subsection']->value['itemcomponentmoreinfolinkurl'])) {?>
                        <div class="subsection-moreinfo-long">
                            <a href="<?php echo $_smarty_tpl->tpl_vars['subsection']->value['itemcomponentmoreinfolinkurl'];?>
" target="_blank"><?php echo $_smarty_tpl->tpl_vars['subsection']->value['itemcomponentmoreinfolinktext'];?>
</a>
                        </div>
                        <?php $_smarty_tpl->_assignInScope('multilinedesc', "true");?>
                    <?php }?>
                    <?php if (!empty($_smarty_tpl->tpl_vars['subsection']->value['itemcomponentpriceinfo'])) {?>
                        <div class="subsection-info-long">
                            <?php echo $_smarty_tpl->tpl_vars['subsection']->value['itemcomponentpriceinfo'];?>

                        </div>
                        <?php $_smarty_tpl->_assignInScope('multilinedesc', "true");?>
                    <?php }?>
                <?php }?>
                        <div class="clear"></div>
                    </div>
                <!-- START add-edit-change-remove links -->
                <?php if ($_smarty_tpl->tpl_vars['stage']->value == 'qty') {?>
                    <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['subsection']->value['itemcomponentbuttons'], 'button');
$_smarty_tpl->tpl_vars['button']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['button']->value) {
$_smarty_tpl->tpl_vars['button']->do_else = false;
?>
                    <div class="contentBtn btnRight <?php if ($_smarty_tpl->tpl_vars['multilinedesc']->value == "true") {?>paddingCenter<?php }?>" data-decorator="<?php echo $_smarty_tpl->tpl_vars['button']->value['action'];?>
" data-orderlineid="-1" data-sectionlineid="<?php echo $_smarty_tpl->tpl_vars['subsection']->value['orderlineid'];?>
">
                        <div class="btn-white-left" ></div>
                        <div class="btn-white-middle <?php echo $_smarty_tpl->tpl_vars['button']->value['class'];?>
"><?php echo $_smarty_tpl->tpl_vars['button']->value['label'];?>
</div>
                        <div class="btn-white-right"></div>
                    </div>
                    <div class="clear"></div>
                    <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
                <?php }?>
                <!-- END add-edit-change-remove links -->

                </div>
                <?php if ($_smarty_tpl->tpl_vars['subsection']->value['pricingmodel'] == 7 || $_smarty_tpl->tpl_vars['subsection']->value['pricingmodel'] == 8) {?>
                <div class="quantity <?php if ($_smarty_tpl->tpl_vars['multilinedesc']->value == "true") {?>paddingCenter<?php }?>">
                    <?php if (($_smarty_tpl->tpl_vars['stage']->value == 'payment')) {?>
                    <span class="quantityText">
                        <?php echo $_smarty_tpl->tpl_vars['subsection']->value['quantity'];?>

                    </span>
                    <?php } else { ?>
                    <input id="hiddeqty_<?php echo $_smarty_tpl->tpl_vars['subsection']->value['orderlineid'];?>
" type="hidden" class="hiddeqtyCpt" value="<?php echo $_smarty_tpl->tpl_vars['subsection']->value['quantity'];?>
"/>
                        <?php if (empty($_smarty_tpl->tpl_vars['subsection']->value['itemqtydropdown'])) {?>
                    <input id="itemqty_<?php echo $_smarty_tpl->tpl_vars['subsection']->value['orderlineid'];?>
" type="text" class="quantity" maxlength="8" value="<?php echo $_smarty_tpl->tpl_vars['subsection']->value['quantity'];?>
" data-decorator="fnUpdateComponentQty" data-lineid="<?php echo $_smarty_tpl->tpl_vars['subsection']->value['orderlineid'];?>
" data-itemqty="<?php echo $_smarty_tpl->tpl_vars['subsection']->value['itemqty'];?>
" data-trigger="keypress" />
                    <img class="refresh" src="<?php echo $_smarty_tpl->tpl_vars['brandroot']->value;?>
/images/icons/refresh.png" alt="<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelOrderUpdateItemTotal');?>
" title="<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelOrderUpdateItemTotal');?>
" data-decorator="fnUpdateComponentQty" data-lineid="<?php echo $_smarty_tpl->tpl_vars['subsection']->value['orderlineid'];?>
" data-itemqty="<?php echo $_smarty_tpl->tpl_vars['subsection']->value['itemqty'];?>
" data-trigger="click">
                        <?php } else { ?>
                    <select id="itemqty_<?php echo $_smarty_tpl->tpl_vars['subsection']->value['orderlineid'];?>
" class="" data-decorator="fnUpdateComponentQty" data-lineid="<?php echo $_smarty_tpl->tpl_vars['subsection']->value['orderlineid'];?>
" data-itemqty="<?php echo $_smarty_tpl->tpl_vars['subsection']->value['itemqty'];?>
" data-trigger="change" >
                            <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['subsection']->value['itemqtydropdown'], 'qtyValue');
$_smarty_tpl->tpl_vars['qtyValue']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['qtyValue']->value) {
$_smarty_tpl->tpl_vars['qtyValue']->do_else = false;
?>
                        <option <?php if ($_smarty_tpl->tpl_vars['qtyValue']->value == $_smarty_tpl->tpl_vars['subsection']->value['quantity']) {?>selected="selected"<?php }?> value="<?php echo $_smarty_tpl->tpl_vars['qtyValue']->value;?>
"><?php echo $_smarty_tpl->tpl_vars['qtyValue']->value;?>
</option>
                            <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
                    </select>
                        <?php }?>
                    <?php }?>
                </div>
                <?php }?>
                <div class="component-price <?php if ($_smarty_tpl->tpl_vars['multilinedesc']->value == "true") {?>paddingCenter<?php }?>">
                    <?php echo $_smarty_tpl->tpl_vars['subsection']->value['totalsell'];?>

                </div>
                <?php if ($_smarty_tpl->tpl_vars['multilinedesc']->value) {?>
                    <?php $_smarty_tpl->_assignInScope('multilinedesc', "false");?>
                <?php }?>
                <div class="clear"></div>
            </div>
                <?php if ($_smarty_tpl->tpl_vars['subsection']->value['metadatahtml']) {?>
            <div id="metadatarow_<?php echo $_smarty_tpl->tpl_vars['subsection']->value['orderlineid'];?>
" class="subcomponent-metadata">
                <?php echo $_smarty_tpl->tpl_vars['subsection']->value['metadatahtml'];?>

            </div>
                <?php }?>
        </div>
        <div class="clear"></div>
                <?php if ($_smarty_tpl->tpl_vars['stage']->value == 'payment') {?>
        <div class="line-total">
                    <!-- VALUE OFF TOTAL VOUCHER -->
                    <?php if ((($_smarty_tpl->tpl_vars['vouchersection']->value == 'TOTAL') && (($_smarty_tpl->tpl_vars['differenttaxrates']->value) && (!$_smarty_tpl->tpl_vars['specialvouchertype']->value)))) {?>
                        <?php if (($_smarty_tpl->tpl_vars['subsection']->value['discountvalueraw'] > 0)) {?>
            <div class="line-sub-total">
                <span class="discount-heading"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelSubTotal');?>
:</span>
                <span class="discount-price"><?php echo $_smarty_tpl->tpl_vars['subsection']->value['totalsell'];?>
</span>
            </div>
            <div class="line-sub-total">
                <span class="discount-heading"><?php echo $_smarty_tpl->tpl_vars['subsection']->value['discountname'];?>
:</span>
                <span class="discount-price"><?php echo $_smarty_tpl->tpl_vars['subsection']->value['discountvalue'];?>
</span>
            </div>
                        <?php }?>
                        <?php if ((!$_smarty_tpl->tpl_vars['showpriceswithtax']->value)) {?>
                            <!-- SHOWZEROTAX OR DONT SHOWZEROTAX AND THE TAX > 0 -->
                            <?php if ((($_smarty_tpl->tpl_vars['showzerotax']->value) || ((!$_smarty_tpl->tpl_vars['showzerotax']->value) && ($_smarty_tpl->tpl_vars['subsection']->value['displaytaxraw'] > 0)))) {?>
            <div class="line-sub-total">
                <span class="discount-heading"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelSubTotal');?>
:</span>
                <span class="discount-price"><?php echo $_smarty_tpl->tpl_vars['subsection']->value['subtotal'];?>
</span>
            </div>
            <div class="line-sub-total">
                <span class="discount-heading"><?php echo $_smarty_tpl->tpl_vars['subsection']->value['taxratename'];?>
 (<?php echo $_smarty_tpl->tpl_vars['subsection']->value['taxrate'];?>
%):</span>
                <span class="discount-price"><?php echo $_smarty_tpl->tpl_vars['subsection']->value['displaytax'];?>
</span>
            </div>
            <div class="line-sub-total">
                <span class="discount-heading"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelOrderItemListItemTotal');?>
:</span>
                <span class="discount-price"><?php echo $_smarty_tpl->tpl_vars['subsection']->value['displayprice'];?>
</span>
            </div>
                            <?php } else { ?>
            <div class="line-sub-total">
                <span class="discount-heading"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelOrderItemListItemTotal');?>
:</span>
                <span class="discount-price"><?php echo $_smarty_tpl->tpl_vars['subsection']->value['displayprice'];?>
</span>
            </div>
                            <?php }?>
                        <?php } else { ?>
            <div class="line-sub-total">
                <span class="discount-heading"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelOrderItemListItemTotal');?>
:</span>
                <span class="discount-price"><?php echo $_smarty_tpl->tpl_vars['subsection']->value['subtotal'];?>
</span>
            </div>
                            <!-- SHOWZEROTAX OR DONT SHOWZEROTAX AND THE TAX > 0 -->
                            <?php if (($_smarty_tpl->tpl_vars['showtaxbreakdown']->value)) {?>
                                <?php if ((($_smarty_tpl->tpl_vars['showzerotax']->value) || ((!$_smarty_tpl->tpl_vars['showzerotax']->value) && ($_smarty_tpl->tpl_vars['subsection']->value['displaytaxraw'] > 0)))) {?>
            <div class="line-sub-total-small-bottom"><?php echo $_smarty_tpl->tpl_vars['subsection']->value['includesitemtaxtext'];?>
</div>
                                <?php }?>
                            <?php }?>
                        <?php }?>
                    <?php } else { ?>
                        <!-- DIFFERNETTAXRATES AND SHOWPRICES WITH TAX -->
                        <?php if ((($_smarty_tpl->tpl_vars['differenttaxrates']->value) && ($_smarty_tpl->tpl_vars['showpriceswithtax']->value))) {?>
                        <?php if ((($_smarty_tpl->tpl_vars['vouchersection']->value == 'TOTAL') && ($_smarty_tpl->tpl_vars['specialvouchertype']->value))) {?>
            <div class="line-sub-total">
                <span class="discount-heading"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelSubTotal');?>
:</span>
                <span class="discount-price"><?php echo $_smarty_tpl->tpl_vars['subsection']->value['totalsell'];?>
</span>
            </div>
                            <?php if (($_smarty_tpl->tpl_vars['subsection']->value['discountvalueraw'] > 0)) {?>
            <div class="line-sub-total">
                <span class="discount-heading"><?php echo $_smarty_tpl->tpl_vars['subsection']->value['discountname'];?>
:</span>
                <span class="discount-price"><?php echo $_smarty_tpl->tpl_vars['subsection']->value['discountvalue'];?>
</span>
            </div>
                            <!-- SHOWZEROTAX OR DONT SHOWZEROTAX AND THE TAX > 0 -->
                            <?php if ((($_smarty_tpl->tpl_vars['showzerotax']->value) || ((!$_smarty_tpl->tpl_vars['showzerotax']->value) && ($_smarty_tpl->tpl_vars['subsection']->value['displaytaxraw'] > 0)))) {?>
            <div class="line-sub-total">
                <span class="discount-heading"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelSubTotal');?>
:</span>
                <span class="discount-price"><?php echo $_smarty_tpl->tpl_vars['subsection']->value['subtotal'];?>
</span>
            </div>
                            <?php }?>
                            <?php }?>
                            <!-- SHOWZEROTAX OR DONT SHOWZEROTAX AND THE TAX > 0 -->
                            <?php if ((($_smarty_tpl->tpl_vars['showzerotax']->value) || ((!$_smarty_tpl->tpl_vars['showzerotax']->value) && ($_smarty_tpl->tpl_vars['subsection']->value['displaytaxraw'] > 0)))) {?>
            <div class="line-sub-total">
                <span class="discount-heading"><?php echo $_smarty_tpl->tpl_vars['subsection']->value['taxratename'];?>
 (<?php echo $_smarty_tpl->tpl_vars['subsection']->value['taxrate'];?>
%):</span>
                <span class="discount-price"><?php echo $_smarty_tpl->tpl_vars['subsection']->value['displaytax'];?>
</span>
            </div>
                            <?php }?>
            <div class="line-sub-total">
                <span class="discount-heading"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelOrderItemListItemTotal');?>
:</span>
                <span class="discount-price"><?php echo $_smarty_tpl->tpl_vars['subsection']->value['displayprice'];?>
</span>
            </div>
                        <?php } else { ?>
            <div class="line-sub-total">
                <span class="discount-heading"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelOrderItemListItemTotal');?>
:</span>
                <span class="discount-price"><?php echo $_smarty_tpl->tpl_vars['subsection']->value['subtotal'];?>
</span>
            </div>
                            <!-- SHOWTAXBREAKDOWN -->
                            <?php if (($_smarty_tpl->tpl_vars['showtaxbreakdown']->value)) {?>
                                <?php if ((($_smarty_tpl->tpl_vars['showzerotax']->value) || ((!$_smarty_tpl->tpl_vars['showzerotax']->value) && ($_smarty_tpl->tpl_vars['subsection']->value['displaytaxraw'] > 0)))) {?>
            <div class="line-sub-total-small-bottom"><?php echo $_smarty_tpl->tpl_vars['subsection']->value['includesitemtaxtext'];?>
</div>
                                <?php }?>
                            <?php }?>
                            <?php }?>
                        <?php }?>
                        <!-- DIFFERNETTAXRATES AND DONT SHOWPRICESWITHTAX -->
                        <?php if ((($_smarty_tpl->tpl_vars['differenttaxrates']->value) && (!$_smarty_tpl->tpl_vars['showpriceswithtax']->value))) {?>
                            <?php if ((($_smarty_tpl->tpl_vars['vouchersection']->value == 'TOTAL') && ($_smarty_tpl->tpl_vars['specialvouchertype']->value))) {?>
            <div class="line-sub-total">
                <span class="discount-heading"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelSubTotal');?>
:</span>
                <span class="discount-price"><?php echo $_smarty_tpl->tpl_vars['subsection']->value['totalsell'];?>
</span>
            </div>
                                <?php if (($_smarty_tpl->tpl_vars['subsection']->value['discountvalueraw'] > 0)) {?>
            <div class="line-sub-total">
                <span class="discount-heading"><?php echo $_smarty_tpl->tpl_vars['subsection']->value['discountname'];?>
:</span>
                <span class="discount-price"><?php echo $_smarty_tpl->tpl_vars['subsection']->value['discountvalue'];?>
</span>
            </div>
                                    <!-- SHOWZEROTAX OR DONT SHOWZEROTAX AND THE TAX > 0 -->
                                    <?php if ((($_smarty_tpl->tpl_vars['showzerotax']->value) || ((!$_smarty_tpl->tpl_vars['showzerotax']->value) && ($_smarty_tpl->tpl_vars['subsection']->value['displaytaxraw'] > 0)))) {?>
            <div class="line-sub-total">
                <span class="discount-heading"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelSubTotal');?>
:</span>
                <span class="discount-price"><?php echo $_smarty_tpl->tpl_vars['subsection']->value['subtotal'];?>
</span>
            </div>
                                    <?php }?>
                                <?php }?>
                                <!-- SHOWZEROTAX OR DONT SHOWZEROTAX AND THE TAX > 0 -->
                                <?php if ((($_smarty_tpl->tpl_vars['showzerotax']->value) || ((!$_smarty_tpl->tpl_vars['showzerotax']->value) && ($_smarty_tpl->tpl_vars['subsection']->value['displaytaxraw'] > 0)))) {?>
            <div class="line-sub-total">
                <span class="discount-heading"><?php echo $_smarty_tpl->tpl_vars['subsection']->value['taxratename'];?>
 (<?php echo $_smarty_tpl->tpl_vars['subsection']->value['taxrate'];?>
%):</span>
                <span class="discount-price"><?php echo $_smarty_tpl->tpl_vars['subsection']->value['displaytax'];?>
</span>
            </div>
                                <?php }?>
            <div class="line-sub-total">
                <span class="discount-heading"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelOrderItemListItemTotal');?>
:</span>
                <span class="discount-price"><?php echo $_smarty_tpl->tpl_vars['subsection']->value['displayprice'];?>
</span>
            </div>
                            <?php } else { ?>
                                <!-- SHOWZEROTAX OR DONT SHOWZEROTAX AND THE TAX > 0 -->
                                <?php if ((($_smarty_tpl->tpl_vars['showzerotax']->value) || ((!$_smarty_tpl->tpl_vars['showzerotax']->value) && ($_smarty_tpl->tpl_vars['subsection']->value['displaytaxraw'] > 0)))) {?>
            <div class="line-sub-total">
                <span class="discount-heading"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelSubTotal');?>
:</span>
                <span class="discount-price"><?php echo $_smarty_tpl->tpl_vars['subsection']->value['totalsell'];?>
</span>
            </div>
            <div class="line-sub-total">
                <span class="discount-heading"><?php echo $_smarty_tpl->tpl_vars['subsection']->value['taxratename'];?>
 (<?php echo $_smarty_tpl->tpl_vars['subsection']->value['taxrate'];?>
%):</span>
                <span class="discount-price"><?php echo $_smarty_tpl->tpl_vars['subsection']->value['displaytax'];?>
</span>
            </div>
            <div class="line-sub-total">
                <span class="discount-heading"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelOrderItemListItemTotal');?>
:</span>
                <span class="discount-price"><?php echo $_smarty_tpl->tpl_vars['subsection']->value['displayprice'];?>
</span>
            </div>
                                <?php } else { ?>
            <div class="line-sub-total">
                <span class="discount-heading"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelOrderItemListItemTotal');?>
:</span>
                <span class="discount-price"><?php echo $_smarty_tpl->tpl_vars['subsection']->value['displayprice'];?>
</span>
            </div>
                                <?php }?>
                            <?php }?>
                        <?php }?>
                        <!-- NOT DIFFERNETTAXRATES -->
                        <?php if ((!$_smarty_tpl->tpl_vars['differenttaxrates']->value)) {?>
                            <?php if ((($_smarty_tpl->tpl_vars['subsection']->value['discountvalueraw'] > 0) && ($_smarty_tpl->tpl_vars['applyVoucherAsLineDiscount']->value))) {?>
            <div class="line-sub-total">
                <span class="discount-heading"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelSubTotal');?>
:</span>
                <span class="discount-price"><?php echo $_smarty_tpl->tpl_vars['subsection']->value['totalsell'];?>
</span>
            </div>
            <div class="line-sub-total">
                <span class="discount-heading"><?php echo $_smarty_tpl->tpl_vars['subsection']->value['discountname'];?>
:</span>
                <span class="discount-price"><?php echo $_smarty_tpl->tpl_vars['subsection']->value['discountvalue'];?>
</span>
            </div>
                            <?php }?>
            <div class="line-sub-total">
                <span class="discount-heading"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelOrderItemListItemTotal');?>
:</span>
                <span class="discount-price"><?php echo $_smarty_tpl->tpl_vars['subsection']->value['subtotal'];?>
</span>
            </div>
                        <?php }?>
                    <?php }?>
        </div>
                <?php }?>
            <?php }?>
        <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>         <!-- sub-sections of order footer component end -->

        <!-- checkboxes inside component start -->
        <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['section']->value['checkboxes'], 'checkbox');
$_smarty_tpl->tpl_vars['checkbox']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['checkbox']->value) {
$_smarty_tpl->tpl_vars['checkbox']->do_else = false;
?>             <?php if ($_smarty_tpl->tpl_vars['checkbox']->value['showcomponentname'] == true) {?>
                <?php if (($_smarty_tpl->tpl_vars['stage']->value == 'qty') || ($_smarty_tpl->tpl_vars['stage']->value == 'payment' && $_smarty_tpl->tpl_vars['checkbox']->value['checked'] == 1)) {?>
        <div id="componentrow_<?php echo $_smarty_tpl->tpl_vars['checkbox']->value['orderlineid'];?>
" class="subsection">
			<div class="section-title-header">
			<?php if (($_smarty_tpl->tpl_vars['checkbox']->value['itemcomponentcategoryname'] != '')) {?>
				<span class="section-category-name"><?php echo $_smarty_tpl->tpl_vars['checkbox']->value['itemcomponentcategoryname'];?>
:</span> <span class="section-category-prompt"><?php echo $_smarty_tpl->tpl_vars['checkbox']->value['itemcomponentprompt'];?>
</span>
			<?php }?>
			</div>
            <div class="subcheckboxBloc">
                    <?php if ($_smarty_tpl->tpl_vars['checkbox']->value['haspreview'] > 0) {?>
                <img class="component-preview" src="<?php echo htmlspecialchars((string)$_smarty_tpl->tpl_vars['checkbox']->value['componentpreviewsrc'], ENT_QUOTES, 'UTF-8', true);?>
" alt="" />
                <div class="componentSubSectionTitle">
                    <?php } else { ?>
                <div class="componentSubSectionTitleLong">
                    <?php }?>
                     <div class="componentContentText">
                        <div class="section-title"><?php echo $_smarty_tpl->tpl_vars['checkbox']->value['itemcomponentname'];?>
</div>
                    <!-- START add-edit-change-remove links -->
                    <?php if ($_smarty_tpl->tpl_vars['checkbox']->value['haspreview'] > 0) {?>
                        <?php if (!empty($_smarty_tpl->tpl_vars['checkbox']->value['itemcomponentinfo'])) {?>
                        <div class="subsection-info">
                            <?php echo $_smarty_tpl->tpl_vars['checkbox']->value['itemcomponentinfo'];?>

                        </div>
                            <?php $_smarty_tpl->_assignInScope('multilinedesc', "true");?>
                        <?php }?>
                        <?php if (!empty($_smarty_tpl->tpl_vars['checkbox']->value['itemcomponentmoreinfolinkurl'])) {?>
                        <div class="subsection-moreinfo">
                            <a href="<?php echo $_smarty_tpl->tpl_vars['checkbox']->value['itemcomponentmoreinfolinkurl'];?>
" target="_blank"><?php echo $_smarty_tpl->tpl_vars['checkbox']->value['itemcomponentmoreinfolinktext'];?>
</a>
                        </div>
                          <?php $_smarty_tpl->_assignInScope('multilinedesc', "true");?>
                        <?php }?>
                        <?php if (!empty($_smarty_tpl->tpl_vars['checkbox']->value['itemcomponentpriceinfo'])) {?>
                        <div class="subsection-info">
                            <?php echo $_smarty_tpl->tpl_vars['checkbox']->value['itemcomponentpriceinfo'];?>

                        </div>
                            <?php $_smarty_tpl->_assignInScope('multilinedesc', "true");?>
                        <?php }?>
                    <?php } else { ?>
                        <?php if (!empty($_smarty_tpl->tpl_vars['checkbox']->value['itemcomponentinfo'])) {?>
                        <div class="subsection-info-long">
                            <?php echo $_smarty_tpl->tpl_vars['checkbox']->value['itemcomponentinfo'];?>

                        </div>
                            <?php $_smarty_tpl->_assignInScope('multilinedesc', "true");?>
                        <?php }?>
                        <?php if (!empty($_smarty_tpl->tpl_vars['checkbox']->value['itemcomponentmoreinfolinkurl'])) {?>
                          <div class="subsection-moreinfo-long">
                              <a href="<?php echo $_smarty_tpl->tpl_vars['checkbox']->value['itemcomponentmoreinfolinkurl'];?>
" target="_blank"><?php echo $_smarty_tpl->tpl_vars['checkbox']->value['itemcomponentmoreinfolinktext'];?>
</a>
                          </div>
                          <?php $_smarty_tpl->_assignInScope('multilinedesc', "true");?>
                        <?php }?>
                        <?php if (!empty($_smarty_tpl->tpl_vars['checkbox']->value['itemcomponentpriceinfo'])) {?>
                        <div class="subsection-info-long">
                            <?php echo $_smarty_tpl->tpl_vars['checkbox']->value['itemcomponentpriceinfo'];?>

                        </div>
                             <?php $_smarty_tpl->_assignInScope('multilinedesc', "true");?>
                        <?php }?>
                    <?php }?>
                    <?php if ($_smarty_tpl->tpl_vars['stage']->value == 'qty' && $_smarty_tpl->tpl_vars['checkbox']->value['totalsell'] != $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelNotAvailable')) {?>
                    (<?php echo $_smarty_tpl->tpl_vars['checkbox']->value['totalsell'];?>
)
                    <?php }?>
                        <div class="clear"></div>
                    </div>
                    <?php if ($_smarty_tpl->tpl_vars['stage']->value == 'qty' && $_smarty_tpl->tpl_vars['checkbox']->value['totalsell'] != $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelNotAvailable')) {?>
                        <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['checkbox']->value['itemcomponentbuttons'], 'button');
$_smarty_tpl->tpl_vars['button']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['button']->value) {
$_smarty_tpl->tpl_vars['button']->do_else = false;
?>
                            <div class="contentBtn btnRight <?php if ($_smarty_tpl->tpl_vars['multilinedesc']->value == "true") {?>paddingCenter<?php }?>" data-decorator="<?php echo $_smarty_tpl->tpl_vars['button']->value['action'];?>
" data-orderlineid="-1" data-sectionlineid="<?php echo $_smarty_tpl->tpl_vars['checkbox']->value['orderlineid'];?>
">
                                 <div class="btn-white-left" ></div>
                                 <div class="btn-white-middle <?php echo $_smarty_tpl->tpl_vars['button']->value['class'];?>
"><?php echo $_smarty_tpl->tpl_vars['button']->value['label'];?>
</div>
                                 <div class="btn-white-right"></div>
                             </div>
                            <div class="clear"></div>
                        <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
                    <?php }?>
                    <!-- END add-edit-change-remove links -->
                </div>
                    <?php if (($_smarty_tpl->tpl_vars['checkbox']->value['pricingmodel'] == 7 || $_smarty_tpl->tpl_vars['checkbox']->value['pricingmodel'] == 8) && $_smarty_tpl->tpl_vars['checkbox']->value['checked'] == 1) {?>
                <div class="quantity <?php if ($_smarty_tpl->tpl_vars['multilinedesc']->value == "true") {?>paddingCenter<?php }?>">
                        <?php if ($_smarty_tpl->tpl_vars['stage']->value == 'payment') {?>
                    <span class="quantityText"><?php echo $_smarty_tpl->tpl_vars['checkbox']->value['quantity'];?>
</span>
                        <?php } else { ?>
                    <input id="hiddeqty_<?php echo $_smarty_tpl->tpl_vars['checkbox']->value['orderlineid'];?>
" type="hidden" class="hiddeqtyCpt" value="<?php echo $_smarty_tpl->tpl_vars['checkbox']->value['quantity'];?>
"/>
                            <?php if (empty($_smarty_tpl->tpl_vars['checkbox']->value['itemqtydropdown'])) {?>
                    <input id="itemqty_<?php echo $_smarty_tpl->tpl_vars['checkbox']->value['orderlineid'];?>
" type="text" class="quantity" maxlength="8" value="<?php echo $_smarty_tpl->tpl_vars['checkbox']->value['quantity'];?>
" data-decorator="fnUpdateComponentQty" data-trigger="keyup" data-lineid="<?php echo $_smarty_tpl->tpl_vars['checkbox']->value['orderlineid'];?>
" data-itemqty="<?php echo $_smarty_tpl->tpl_vars['checkbox']->value['itemqty'];?>
" />
                    <img class="refresh" src="<?php echo $_smarty_tpl->tpl_vars['brandroot']->value;?>
/images/icons/refresh.png" alt="<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelOrderUpdateItemTotal');?>
" title="<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelOrderUpdateItemTotal');?>
" data-decorator="fnUpdateComponentQty" data-trigger="click" data-lineid="<?php echo $_smarty_tpl->tpl_vars['checkbox']->value['orderlineid'];?>
" data-itemqty="<?php echo $_smarty_tpl->tpl_vars['checkbox']->value['itemqty'];?>
" />
                            <?php } else { ?>
                    <select id="itemqty_<?php echo $_smarty_tpl->tpl_vars['checkbox']->value['orderlineid'];?>
" class="" data-decorator="fnUpdateComponentQty" data-trigger="change" data-lineid="<?php echo $_smarty_tpl->tpl_vars['checkbox']->value['orderlineid'];?>
" data-itemqty="<?php echo $_smarty_tpl->tpl_vars['checkbox']->value['itemqty'];?>
">
                                <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['checkbox']->value['itemqtydropdown'], 'qtyValue');
$_smarty_tpl->tpl_vars['qtyValue']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['qtyValue']->value) {
$_smarty_tpl->tpl_vars['qtyValue']->do_else = false;
?>
                        <option <?php if ($_smarty_tpl->tpl_vars['qtyValue']->value == $_smarty_tpl->tpl_vars['checkbox']->value['quantity']) {?>selected="selected"<?php }?> value="<?php echo $_smarty_tpl->tpl_vars['qtyValue']->value;?>
"><?php echo $_smarty_tpl->tpl_vars['qtyValue']->value;?>
</option>
                                <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
                    </select>
                            <?php }?>
                        <?php }?>
                </div>
                    <?php }?>
                    <?php if ($_smarty_tpl->tpl_vars['checkbox']->value['checked'] || $_smarty_tpl->tpl_vars['checkbox']->value['totalsell'] == $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelNotAvailable')) {?>
                <div class="component-price <?php if ($_smarty_tpl->tpl_vars['multilinedesc']->value == "true") {?>paddingCenter<?php }?>">
                    <?php echo $_smarty_tpl->tpl_vars['checkbox']->value['totalsell'];?>

                </div>
                    <?php }?>
                    <?php if ($_smarty_tpl->tpl_vars['multilinedesc']->value) {?>
                        <?php $_smarty_tpl->_assignInScope('multilinedesc', "false");?>
                    <?php }?>
                <div class="clear"></div>
            </div>
                    <?php if ((($_smarty_tpl->tpl_vars['checkbox']->value['metadatahtml']) && ($_smarty_tpl->tpl_vars['checkbox']->value['checked']))) {?>
            <div id="metadatarow_<?php echo $_smarty_tpl->tpl_vars['checkbox']->value['orderlineid'];?>
" class="subcomponent-metadata<?php if (!$_smarty_tpl->tpl_vars['checkbox']->value['checked']) {?> invisible<?php }?>">
                <?php echo $_smarty_tpl->tpl_vars['checkbox']->value['metadatahtml'];?>

            </div>
                    <?php }?>
        </div>
        <div class="clear"></div>
                    <?php if ($_smarty_tpl->tpl_vars['checkbox']->value['checked'] || $_smarty_tpl->tpl_vars['checkbox']->value['totalsell'] == $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelNotAvailable')) {?>
                        <?php if ($_smarty_tpl->tpl_vars['stage']->value == 'payment') {?>
        <div class="line-total">
                            <!-- VALUE OFF TOTAL VOUCHER -->
                            <?php if ((($_smarty_tpl->tpl_vars['vouchersection']->value == 'TOTAL') && (($_smarty_tpl->tpl_vars['differenttaxrates']->value) && (!$_smarty_tpl->tpl_vars['specialvouchertype']->value)))) {?>
                                <?php if (($_smarty_tpl->tpl_vars['checkbox']->value['discountvalueraw'] > 0)) {?>
            <div class="line-sub-total">
                <span class="discount-heading"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelSubTotal');?>
:</span>
                <span class="discount-price"><?php echo $_smarty_tpl->tpl_vars['checkbox']->value['totalsell'];?>
</span>
            </div>
            <div class="line-sub-total">
                <span class="discount-heading"><?php echo $_smarty_tpl->tpl_vars['checkbox']->value['discountname'];?>
:</span>
                <span class="discount-price"><?php echo $_smarty_tpl->tpl_vars['checkbox']->value['discountvalue'];?>
</span>
            </div>
                                <?php }?>
                                <?php if ((!$_smarty_tpl->tpl_vars['showpriceswithtax']->value)) {?>
                                    <!-- SHOWZEROTAX OR DONT SHOWZEROTAX AND THE TAX > 0 -->
                                    <?php if ((($_smarty_tpl->tpl_vars['showzerotax']->value) || ((!$_smarty_tpl->tpl_vars['showzerotax']->value) && ($_smarty_tpl->tpl_vars['checkbox']->value['displaytaxraw'] > 0)))) {?>
            <div class="line-sub-total">
                <span class="discount-heading"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelSubTotal');?>
:</span>
                <span class="discount-price"><?php echo $_smarty_tpl->tpl_vars['checkbox']->value['subtotal'];?>
</span>
            </div>
            <div class="line-sub-total">
                <span class="discount-heading"><?php echo $_smarty_tpl->tpl_vars['checkbox']->value['taxratename'];?>
 (<?php echo $_smarty_tpl->tpl_vars['checkbox']->value['taxrate'];?>
%):</span>
                <span class="discount-price"><?php echo $_smarty_tpl->tpl_vars['checkbox']->value['displaytax'];?>
</span>
            </div>
            <div class="line-sub-total">
                <span class="discount-heading"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelOrderItemListItemTotal');?>
:</span>
                <span class="discount-price"><?php echo $_smarty_tpl->tpl_vars['checkbox']->value['displayprice'];?>
</span>
            </div>
                                    <?php } else { ?>
            <div class="line-sub-total">
                <span class="discount-heading"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelOrderItemListItemTotal');?>
:</span>
                <span class="discount-price"><?php echo $_smarty_tpl->tpl_vars['checkbox']->value['displayprice'];?>
</span>
            </div>
                                    <?php }?>
                                <?php } else { ?>
            <div class="line-sub-total">
                <span class="discount-heading"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelOrderItemListItemTotal');?>
:</span>
                <span class="discount-price"><?php echo $_smarty_tpl->tpl_vars['checkbox']->value['subtotal'];?>
</span>
            </div>
                                    <!-- SHOWZEROTAX OR DONT SHOWZEROTAX AND THE TAX > 0 -->
                                    <?php if (($_smarty_tpl->tpl_vars['showtaxbreakdown']->value)) {?>
                                        <?php if ((($_smarty_tpl->tpl_vars['showzerotax']->value) || ((!$_smarty_tpl->tpl_vars['showzerotax']->value) && ($_smarty_tpl->tpl_vars['checkbox']->value['displaytaxraw'] > 0)))) {?>
            <div class="line-sub-total-small-bottom"><?php echo $_smarty_tpl->tpl_vars['checkbox']->value['includesitemtaxtext'];?>
</div>
                                        <?php }?>
                                    <?php }?>
                                <?php }?>
                            <?php } else { ?>
                                <!-- DIFFERNETTAXRATES AND SHOWPRICES WITH TAX -->
                                <?php if ((($_smarty_tpl->tpl_vars['differenttaxrates']->value) && ($_smarty_tpl->tpl_vars['showpriceswithtax']->value))) {?>
                                    <?php if ((($_smarty_tpl->tpl_vars['vouchersection']->value == 'TOTAL') && ($_smarty_tpl->tpl_vars['specialvouchertype']->value))) {?>
            <div class="line-sub-total">
                <span class="discount-heading"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelSubTotal');?>
:</span>
                <span class="discount-price"><?php echo $_smarty_tpl->tpl_vars['checkbox']->value['totalsell'];?>
</span>
            </div>
                                        <?php if (($_smarty_tpl->tpl_vars['checkbox']->value['discountvalueraw'] > 0)) {?>
            <div class="line-sub-total">
                <span class="discount-heading"><?php echo $_smarty_tpl->tpl_vars['checkbox']->value['discountname'];?>
:</span>
                <span class="discount-price"><?php echo $_smarty_tpl->tpl_vars['checkbox']->value['discountvalue'];?>
</span>
            </div>
                                            <!-- SHOWZEROTAX OR DONT SHOWZEROTAX AND THE TAX > 0 -->
                                            <?php if ((($_smarty_tpl->tpl_vars['showzerotax']->value) || ((!$_smarty_tpl->tpl_vars['showzerotax']->value) && ($_smarty_tpl->tpl_vars['checkbox']->value['displaytaxraw'] > 0)))) {?>
            <div class="line-sub-total">
                <span class="discount-heading"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelSubTotal');?>
:</span>
                <span class="discount-price"><?php echo $_smarty_tpl->tpl_vars['checkbox']->value['subtotal'];?>
</span>
            </div>
                                            <?php }?>
                                        <?php }?>
                                        <!-- SHOWZEROTAX OR DONT SHOWZEROTAX AND THE TAX > 0 -->
                                        <?php if ((($_smarty_tpl->tpl_vars['showzerotax']->value) || ((!$_smarty_tpl->tpl_vars['showzerotax']->value) && ($_smarty_tpl->tpl_vars['checkbox']->value['displaytaxraw'] > 0)))) {?>
            <div class="line-sub-total">
                <span class="discount-heading"><?php echo $_smarty_tpl->tpl_vars['checkbox']->value['taxratename'];?>
 (<?php echo $_smarty_tpl->tpl_vars['checkbox']->value['taxrate'];?>
%):</span>
                <span class="discount-price"><?php echo $_smarty_tpl->tpl_vars['checkbox']->value['displaytax'];?>
</span>
            </div>
                                        <?php }?>
            <div class="line-sub-total">
                <span class="discount-heading"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelOrderItemListItemTotal');?>
:</span>
                <span class="discount-price"><?php echo $_smarty_tpl->tpl_vars['checkbox']->value['displayprice'];?>
</span>
            </div>
                                    <?php } else { ?>
            <div class="line-sub-total">
                <span class="discount-heading"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelOrderItemListItemTotal');?>
:</span>
                <span class="discount-price"><?php echo $_smarty_tpl->tpl_vars['checkbox']->value['subtotal'];?>
</span>
            </div>
                                        <!-- SHOWTAXBREAKDOWN -->
                                        <?php if (($_smarty_tpl->tpl_vars['showtaxbreakdown']->value)) {?>
                                            <?php if ((($_smarty_tpl->tpl_vars['showzerotax']->value) || ((!$_smarty_tpl->tpl_vars['showzerotax']->value) && ($_smarty_tpl->tpl_vars['checkbox']->value['displaytaxraw'] > 0)))) {?>
            <div class="line-sub-total-small-bottom"><?php echo $_smarty_tpl->tpl_vars['checkbox']->value['includesitemtaxtext'];?>
</div>
                                            <?php }?>
                                        <?php }?>
                                    <?php }?>
                                <?php }?>
                                <!-- DIFFERNETTAXRATES AND DONT SHOWPRICESWITHTAX -->
                                <?php if ((($_smarty_tpl->tpl_vars['differenttaxrates']->value) && (!$_smarty_tpl->tpl_vars['showpriceswithtax']->value))) {?>
                                    <?php if ((($_smarty_tpl->tpl_vars['vouchersection']->value == 'TOTAL') && ($_smarty_tpl->tpl_vars['specialvouchertype']->value))) {?>
            <div class="line-sub-total">
                <span class="discount-heading"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelSubTotal');?>
:</span>
                <span class="discount-price"><?php echo $_smarty_tpl->tpl_vars['checkbox']->value['totalsell'];?>
</span>
            </div>
                                        <?php if (($_smarty_tpl->tpl_vars['checkbox']->value['discountvalueraw'] > 0)) {?>
            <div class="line-sub-total">
                <span class="discount-heading"><?php echo $_smarty_tpl->tpl_vars['checkbox']->value['discountname'];?>
:</span>
                <span class="discount-price"><?php echo $_smarty_tpl->tpl_vars['checkbox']->value['discountvalue'];?>
</span>
            </div>
                                            <!-- SHOWZEROTAX OR DONT SHOWZEROTAX AND THE TAX > 0 -->
                                            <?php if ((($_smarty_tpl->tpl_vars['showzerotax']->value) || ((!$_smarty_tpl->tpl_vars['showzerotax']->value) && ($_smarty_tpl->tpl_vars['checkbox']->value['displaytaxraw'] > 0)))) {?>
            <div class="line-sub-total">
                <span class="discount-heading"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelSubTotal');?>
:</span>
                <span class="discount-price"><?php echo $_smarty_tpl->tpl_vars['checkbox']->value['subtotal'];?>
</span>
            </div>
                                            <?php }?>
                                        <?php }?>
                                        <!-- SHOWZEROTAX OR DONT SHOWZEROTAX AND THE TAX > 0 -->
                                        <?php if ((($_smarty_tpl->tpl_vars['showzerotax']->value) || ((!$_smarty_tpl->tpl_vars['showzerotax']->value) && ($_smarty_tpl->tpl_vars['checkbox']->value['displaytaxraw'] > 0)))) {?>
            <div class="line-sub-total">
                <span class="discount-heading"><?php echo $_smarty_tpl->tpl_vars['checkbox']->value['taxratename'];?>
 (<?php echo $_smarty_tpl->tpl_vars['checkbox']->value['taxrate'];?>
%):</span>
                <span class="discount-price"><?php echo $_smarty_tpl->tpl_vars['checkbox']->value['displaytax'];?>
</span>
            </div>
                                    <?php }?>
            <div class="line-sub-total">
                <span class="discount-heading"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelOrderItemListItemTotal');?>
:</span>
                <span class="discount-price"><?php echo $_smarty_tpl->tpl_vars['checkbox']->value['displayprice'];?>
</span>
            </div>
                                    <?php } else { ?>
                                        <!-- SHOWZEROTAX OR DONT SHOWZEROTAX AND THE TAX > 0 -->
                                        <?php if ((($_smarty_tpl->tpl_vars['showzerotax']->value) || ((!$_smarty_tpl->tpl_vars['showzerotax']->value) && ($_smarty_tpl->tpl_vars['checkbox']->value['displaytaxraw'] > 0)))) {?>
            <div class="line-sub-total">
                <span class="discount-heading"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelSubTotal');?>
:</span>
                <span class="discount-price"><?php echo $_smarty_tpl->tpl_vars['checkbox']->value['totalsell'];?>
</span>
            </div>
            <div class="line-sub-total">
                <span class="discount-heading"><?php echo $_smarty_tpl->tpl_vars['checkbox']->value['taxratename'];?>
 (<?php echo $_smarty_tpl->tpl_vars['checkbox']->value['taxrate'];?>
%):</span>
                <span class="discount-price"><?php echo $_smarty_tpl->tpl_vars['checkbox']->value['displaytax'];?>
</span>
            </div>
            <div class="line-sub-total">
                <span class="discount-heading"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelOrderItemListItemTotal');?>
:</span>
                <span class="discount-price"><?php echo $_smarty_tpl->tpl_vars['checkbox']->value['displayprice'];?>
</span>
            </div>
                                        <?php } else { ?>
            <div class="line-sub-total">
                <span class="discount-heading"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelOrderItemListItemTotal');?>
:</span>
                <span class="discount-price"><?php echo $_smarty_tpl->tpl_vars['checkbox']->value['displayprice'];?>
</span>
            </div>
                                        <?php }?>
                                    <?php }?>
                                <?php }?>
                                <!-- NOT DIFFERNETTAXRATES -->
                                <?php if ((!$_smarty_tpl->tpl_vars['differenttaxrates']->value)) {?>
                                    <?php if ((($_smarty_tpl->tpl_vars['checkbox']->value['discountvalueraw'] > 0) && ($_smarty_tpl->tpl_vars['applyVoucherAsLineDiscount']->value))) {?>
            <div class="line-sub-total">
                <span class="discount-heading"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelSubTotal');?>
:</span>
                <span class="discount-price"><?php echo $_smarty_tpl->tpl_vars['checkbox']->value['totalsell'];?>
</span>
            </div>
            <div class="line-sub-total">
                <span class="discount-heading"><?php echo $_smarty_tpl->tpl_vars['checkbox']->value['discountname'];?>
:</span>
                <span class="discount-price"><?php echo $_smarty_tpl->tpl_vars['checkbox']->value['discountvalue'];?>
</span>
            </div>
                                    <?php }?>
            <div class="line-sub-total">
                <span class="discount-heading"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelOrderItemListItemTotal');?>
:</span>
                <span class="discount-price"><?php echo $_smarty_tpl->tpl_vars['checkbox']->value['subtotal'];?>
</span>
            </div>
                                <?php }?>
                            <?php }?>
        </div>
                        <?php }?>
                    <?php }?>
                <?php }?>
            <?php }?>
        <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>             <!-- checkboxes inside component end -->
        </div>
        <?php }?>

    <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?> 
    <!-- orderfooter checkboxes start -->
    <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['orderfootercheckboxes']->value, 'checkbox');
$_smarty_tpl->tpl_vars['checkbox']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['checkbox']->value) {
$_smarty_tpl->tpl_vars['checkbox']->do_else = false;
?>
        <?php if ($_smarty_tpl->tpl_vars['checkbox']->value['showcomponentname'] == true) {?>
            <?php if (!(isset($_smarty_tpl->tpl_vars['bTitleOrder']->value)) && (($_smarty_tpl->tpl_vars['stage']->value == 'qty') || ($_smarty_tpl->tpl_vars['stage']->value == 'payment' && $_smarty_tpl->tpl_vars['checkbox']->value['checked'] == 1))) {?>

                <?php if ($_smarty_tpl->tpl_vars['call_action']->value == 'init') {?>
<div class="orderFooter" id="orderFooter">
                <?php }?>

    <div class="sectionLabelLegendFooter">
        <?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelAdditionalItems');?>

		<span class="linkToggleFooter">
			<?php if ($_smarty_tpl->tpl_vars['stage']->value == 'qty') {?>
				<span id="link_footer" data-decorator="fnToggleGeneric" data-lineid="footer" data-idelm="contentFooter" data-colour="white"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_OrderHide');?>
</span>
			<?php } else { ?>
				<span id="link_footer" data-decorator="fnToggleGeneric" data-lineid="footer" data-idelm="contentFooter" data-colour="white"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_OrderShow');?>
</span>
			<?php }?>
		</span>
    </div>
    <div id="contentFooter" <?php if ($_smarty_tpl->tpl_vars['stage']->value == 'payment') {?>style="display:none;"<?php }?>>
                <?php $_smarty_tpl->_assignInScope('bTitleOrder', "true");?>
            <?php }?>
            <?php if (($_smarty_tpl->tpl_vars['stage']->value == 'qty') || ($_smarty_tpl->tpl_vars['stage']->value == 'payment' && $_smarty_tpl->tpl_vars['checkbox']->value['checked'] == 1)) {?>
        <div id="componentrow_<?php echo $_smarty_tpl->tpl_vars['checkbox']->value['orderlineid'];?>
" class="checkbox">
			<div class="section-title-header">
				<?php if (($_smarty_tpl->tpl_vars['checkbox']->value['itemcomponentcategoryname'] != '')) {?>
				<span class="section-category-name"><?php echo $_smarty_tpl->tpl_vars['checkbox']->value['itemcomponentcategoryname'];?>
:</span> <span class="section-category-prompt"><?php echo $_smarty_tpl->tpl_vars['checkbox']->value['itemcomponentprompt'];?>
</span>
				<?php }?>
			</div>

            <div class="checkboxBloc">
                <?php if ($_smarty_tpl->tpl_vars['checkbox']->value['haspreview'] > 0) {?>
                <img src="<?php echo htmlspecialchars((string)$_smarty_tpl->tpl_vars['checkbox']->value['componentpreviewsrc'], ENT_QUOTES, 'UTF-8', true);?>
" alt="" class="component-preview" />
                <div class="component-name">
                <?php } else { ?>
                <div class="component-name-long">
                <?php }?>
                    <div class="componentContentText">
                        <div class="section-title"><?php echo $_smarty_tpl->tpl_vars['checkbox']->value['itemcomponentname'];?>
</div>
                    <?php if ($_smarty_tpl->tpl_vars['checkbox']->value['haspreview'] > 0) {?>
                        <?php if (!empty($_smarty_tpl->tpl_vars['checkbox']->value['itemcomponentinfo'])) {?>
                        <div class="subsection-info">
                            <?php echo $_smarty_tpl->tpl_vars['checkbox']->value['itemcomponentinfo'];?>

                        </div>
                            <?php $_smarty_tpl->_assignInScope('multilinedesc', "true");?>
                        <?php }?>
                        <?php if (!empty($_smarty_tpl->tpl_vars['checkbox']->value['itemcomponentmoreinfolinkurl'])) {?>
                        <div class="subsection-moreinfo">
                            <a href="<?php echo $_smarty_tpl->tpl_vars['checkbox']->value['itemcomponentmoreinfolinkurl'];?>
" target="_blank"><?php echo $_smarty_tpl->tpl_vars['checkbox']->value['itemcomponentmoreinfolinktext'];?>
</a>
                        </div>
                            <?php $_smarty_tpl->_assignInScope('multilinedesc', "true");?>
                        <?php }?>
                        <?php if (!empty($_smarty_tpl->tpl_vars['checkbox']->value['itemcomponentpriceinfo'])) {?>
                        <div class="subsection-info">
                            <?php echo $_smarty_tpl->tpl_vars['checkbox']->value['itemcomponentpriceinfo'];?>

                        </div>
                            <?php $_smarty_tpl->_assignInScope('multilinedesc', "true");?>
                        <?php }?>
                    <?php } else { ?>
                        <?php if (!empty($_smarty_tpl->tpl_vars['checkbox']->value['itemcomponentinfo'])) {?>
                        <div class="subsection-info-long">
                            <?php echo $_smarty_tpl->tpl_vars['checkbox']->value['itemcomponentinfo'];?>

                        </div>
                            <?php $_smarty_tpl->_assignInScope('multilinedesc', "true");?>
                        <?php }?>
                        <?php if (!empty($_smarty_tpl->tpl_vars['checkbox']->value['itemcomponentmoreinfolinkurl'])) {?>
                        <div class="subsection-moreinfo-long">
                            <a href="<?php echo $_smarty_tpl->tpl_vars['checkbox']->value['itemcomponentmoreinfolinkurl'];?>
" target="_blank"><?php echo $_smarty_tpl->tpl_vars['checkbox']->value['itemcomponentmoreinfolinktext'];?>
</a>
                        </div>
                          <?php $_smarty_tpl->_assignInScope('multilinedesc', "true");?>
                        <?php }?>
                        <?php if (!empty($_smarty_tpl->tpl_vars['checkbox']->value['itemcomponentpriceinfo'])) {?>
                        <div class="subsection-info-long">
                            <?php echo $_smarty_tpl->tpl_vars['checkbox']->value['itemcomponentpriceinfo'];?>

                        </div>
                             <?php $_smarty_tpl->_assignInScope('multilinedesc', "true");?>
                        <?php }?>
                    <?php }?>
                    <?php if ($_smarty_tpl->tpl_vars['stage']->value == 'qty' && $_smarty_tpl->tpl_vars['checkbox']->value['totalsell'] != $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelNotAvailable')) {?>
                        (<?php echo $_smarty_tpl->tpl_vars['checkbox']->value['totalsell'];?>
)
                    <?php }?>
                        <div class="clear"></div>
                    </div>
                    <!-- START add-edit-change-remove links -->
                <?php if ($_smarty_tpl->tpl_vars['stage']->value == 'qty' && $_smarty_tpl->tpl_vars['checkbox']->value['totalsell'] != $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelNotAvailable')) {?>
                    <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['checkbox']->value['itemcomponentbuttons'], 'button');
$_smarty_tpl->tpl_vars['button']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['button']->value) {
$_smarty_tpl->tpl_vars['button']->do_else = false;
?>
                    <div class="contentBtn btnRight <?php if ($_smarty_tpl->tpl_vars['multilinedesc']->value == "true") {?>paddingCenter<?php }?>" data-decorator="<?php echo $_smarty_tpl->tpl_vars['button']->value['action'];?>
" data-orderlineid="-1" data-sectionlineid="<?php echo $_smarty_tpl->tpl_vars['checkbox']->value['orderlineid'];?>
">
                        <div class="btn-white-left" ></div>
                        <div class="btn-white-middle <?php echo $_smarty_tpl->tpl_vars['button']->value['class'];?>
"><?php echo $_smarty_tpl->tpl_vars['button']->value['label'];?>
</div>
                        <div class="btn-white-right"></div>
                    </div>
                    <div class="clear"></div>
                    <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
                <?php }?>
                <!-- END add-edit-change-remove links -->
                </div>
                <?php if (($_smarty_tpl->tpl_vars['checkbox']->value['pricingmodel'] == 7 || $_smarty_tpl->tpl_vars['checkbox']->value['pricingmodel'] == 8) && $_smarty_tpl->tpl_vars['checkbox']->value['checked'] == 1) {?>
                <div class="quantity <?php if ($_smarty_tpl->tpl_vars['multilinedesc']->value == "true") {?>paddingCenter<?php }?>">
                    <?php if ($_smarty_tpl->tpl_vars['stage']->value == 'payment') {?>
                    <span class="quantityText"><?php echo $_smarty_tpl->tpl_vars['checkbox']->value['quantity'];?>
</span>
                    <?php } else { ?>
                    <input id="hiddeqty_<?php echo $_smarty_tpl->tpl_vars['checkbox']->value['orderlineid'];?>
" type="hidden" class="hiddeqtyCpt" value="<?php echo $_smarty_tpl->tpl_vars['checkbox']->value['quantity'];?>
"/>
                        <?php if (empty($_smarty_tpl->tpl_vars['checkbox']->value['itemqtydropdown'])) {?>
                    <input id="itemqty_<?php echo $_smarty_tpl->tpl_vars['checkbox']->value['orderlineid'];?>
" type="text" class="quantity" maxlength="8" value="<?php echo $_smarty_tpl->tpl_vars['checkbox']->value['quantity'];?>
" data-decorator="fnUpdateComponentQty" data-trigger="keyup" data-lineid="<?php echo $_smarty_tpl->tpl_vars['checkbox']->value['orderlineid'];?>
" data-itemqty="<?php echo $_smarty_tpl->tpl_vars['checkbox']->value['itemqty'];?>
" />
                    <img class="refresh" src="<?php echo $_smarty_tpl->tpl_vars['brandroot']->value;?>
/images/icons/refresh.png" alt="<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelOrderUpdateItemTotal');?>
" title="<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelOrderUpdateItemTotal');?>
" data-decorator="fnUpdateComponentQty" data-trigger="click" data-lineid="<?php echo $_smarty_tpl->tpl_vars['checkbox']->value['orderlineid'];?>
" data-itemqty="<?php echo $_smarty_tpl->tpl_vars['checkbox']->value['itemqty'];?>
" />
                        <?php } else { ?>
                    <select id="itemqty_<?php echo $_smarty_tpl->tpl_vars['checkbox']->value['orderlineid'];?>
" class="" data-decorator="fnUpdateComponentQty" data-trigger="change" data-lineid="<?php echo $_smarty_tpl->tpl_vars['checkbox']->value['orderlineid'];?>
" data-itemqty="<?php echo $_smarty_tpl->tpl_vars['checkbox']->value['itemqty'];?>
" >
                            <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['checkbox']->value['itemqtydropdown'], 'qtyValue');
$_smarty_tpl->tpl_vars['qtyValue']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['qtyValue']->value) {
$_smarty_tpl->tpl_vars['qtyValue']->do_else = false;
?>
                        <option <?php if ($_smarty_tpl->tpl_vars['qtyValue']->value == $_smarty_tpl->tpl_vars['checkbox']->value['quantity']) {?>selected="selected"<?php }?> value="<?php echo $_smarty_tpl->tpl_vars['qtyValue']->value;?>
"><?php echo $_smarty_tpl->tpl_vars['qtyValue']->value;?>
</option>
                            <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
                    </select>
                        <?php }?>
                    <?php }?>
                </div>
                <?php }?>
                <?php if ($_smarty_tpl->tpl_vars['checkbox']->value['checked'] || $_smarty_tpl->tpl_vars['checkbox']->value['totalsell'] == $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelNotAvailable')) {?>
                    <div class="component-price <?php if ($_smarty_tpl->tpl_vars['multilinedesc']->value == "true") {?>paddingCenter<?php }?>">
                        <?php echo $_smarty_tpl->tpl_vars['checkbox']->value['totalsell'];?>

                    </div>
                <?php }?>
                 <?php if ($_smarty_tpl->tpl_vars['multilinedesc']->value) {?>
                    <?php $_smarty_tpl->_assignInScope('multilinedesc', "false");?>
                <?php }?>
                <div class="clear"></div>
            </div>
                <?php if (($_smarty_tpl->tpl_vars['checkbox']->value['metadatahtml']) && ($_smarty_tpl->tpl_vars['checkbox']->value['checked'])) {?>
            <div id="metadatarow_<?php echo $_smarty_tpl->tpl_vars['checkbox']->value['orderlineid'];?>
" class="component-metadata<?php if (!$_smarty_tpl->tpl_vars['checkbox']->value['checked']) {?> invisible<?php }?>">
                <?php echo $_smarty_tpl->tpl_vars['checkbox']->value['metadatahtml'];?>

            </div>
                <?php }?>
            <div class="clear"></div>
        </div>
        <div class="clear"></div>
                <?php if ($_smarty_tpl->tpl_vars['checkbox']->value['checked'] || $_smarty_tpl->tpl_vars['checkbox']->value['totalsell'] == $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelNotAvailable')) {?>
                    <?php if ($_smarty_tpl->tpl_vars['stage']->value == 'payment') {?>
        <div class="line-total">
                        <!-- VALUE OFF TOTAL VOUCHER -->
                        <?php if ((($_smarty_tpl->tpl_vars['vouchersection']->value == 'TOTAL') && (($_smarty_tpl->tpl_vars['differenttaxrates']->value) && (!$_smarty_tpl->tpl_vars['specialvouchertype']->value)))) {?>
                            <?php if (($_smarty_tpl->tpl_vars['checkbox']->value['discountvalueraw'] > 0)) {?>
            <div class="line-sub-total">
                <span class="discount-heading"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelSubTotal');?>
:</span>
                <span class="discount-price"><?php echo $_smarty_tpl->tpl_vars['checkbox']->value['totalsell'];?>
</span>
            </div>
            <div class="line-sub-total">
                <span class="discount-heading"><?php echo $_smarty_tpl->tpl_vars['checkbox']->value['discountname'];?>
:</span>
                <span class="discount-price"><?php echo $_smarty_tpl->tpl_vars['checkbox']->value['discountvalue'];?>
</span>
            </div>
                            <?php }?>
                            <?php if ((!$_smarty_tpl->tpl_vars['showpriceswithtax']->value)) {?>
                                <!-- SHOWZEROTAX OR DONT SHOWZEROTAX AND THE TAX > 0 -->
                                <?php if ((($_smarty_tpl->tpl_vars['showzerotax']->value) || ((!$_smarty_tpl->tpl_vars['showzerotax']->value) && ($_smarty_tpl->tpl_vars['checkbox']->value['displaytaxraw'] > 0)))) {?>
            <div class="line-sub-total">
                <span class="discount-heading"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelSubTotal');?>
:</span>
                <span class="discount-price"><?php echo $_smarty_tpl->tpl_vars['checkbox']->value['subtotal'];?>
</span>
            </div>
            <div class="line-sub-total">
                <span class="discount-heading"><?php echo $_smarty_tpl->tpl_vars['checkbox']->value['taxratename'];?>
 (<?php echo $_smarty_tpl->tpl_vars['checkbox']->value['taxrate'];?>
%):</span>
                <span class="discount-price"><?php echo $_smarty_tpl->tpl_vars['checkbox']->value['displaytax'];?>
</span>
            </div>
            <div class="line-sub-total">
                <span class="discount-heading"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelOrderItemListItemTotal');?>
:</span>
                <span class="discount-price"><?php echo $_smarty_tpl->tpl_vars['checkbox']->value['displayprice'];?>
</span>
            </div>
                            <?php } else { ?>
            <div class="line-sub-total">
                <span class="discount-heading"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelOrderItemListItemTotal');?>
:</span>
                <span class="discount-price"><?php echo $_smarty_tpl->tpl_vars['checkbox']->value['displayprice'];?>
</span>
            </div>
                                <?php }?>
                            <?php } else { ?>
            <div class="line-sub-total">
                <span class="discount-heading"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelOrderItemListItemTotal');?>
:</span>
                <span class="discount-price"><?php echo $_smarty_tpl->tpl_vars['checkbox']->value['subtotal'];?>
</span>
            </div>
                                <!-- SHOWZEROTAX OR DONT SHOWZEROTAX AND THE TAX > 0 -->
                                <?php if (($_smarty_tpl->tpl_vars['showtaxbreakdown']->value)) {?>
                                    <?php if ((($_smarty_tpl->tpl_vars['showzerotax']->value) || ((!$_smarty_tpl->tpl_vars['showzerotax']->value) && ($_smarty_tpl->tpl_vars['checkbox']->value['displaytaxraw'] > 0)))) {?>
            <div class="line-sub-total-small-bottom"><?php echo $_smarty_tpl->tpl_vars['checkbox']->value['includesitemtaxtext'];?>
</div>
                                    <?php }?>
                                <?php }?>
                            <?php }?>
                        <?php } else { ?>

                            <!-- DIFFERNETTAXRATES AND SHOWPRICES WITH TAX -->
                            <?php if ((($_smarty_tpl->tpl_vars['differenttaxrates']->value) && ($_smarty_tpl->tpl_vars['showpriceswithtax']->value))) {?>
                        <?php if ((($_smarty_tpl->tpl_vars['vouchersection']->value == 'TOTAL') && ($_smarty_tpl->tpl_vars['specialvouchertype']->value))) {?>
            <div class="line-sub-total">
                <span class="discount-heading"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelSubTotal');?>
:</span>
                <span class="discount-price"><?php echo $_smarty_tpl->tpl_vars['checkbox']->value['totalsell'];?>
</span>
            </div>
                            <?php if (($_smarty_tpl->tpl_vars['checkbox']->value['discountvalueraw'] > 0)) {?>
            <div class="line-sub-total">
                <span class="discount-heading"><?php echo $_smarty_tpl->tpl_vars['checkbox']->value['discountname'];?>
:</span>
                <span class="discount-price"><?php echo $_smarty_tpl->tpl_vars['checkbox']->value['discountvalue'];?>
</span>
            </div>
                            <!-- SHOWZEROTAX OR DONT SHOWZEROTAX AND THE TAX > 0 -->
                            <?php if ((($_smarty_tpl->tpl_vars['showzerotax']->value) || ((!$_smarty_tpl->tpl_vars['showzerotax']->value) && ($_smarty_tpl->tpl_vars['checkbox']->value['displaytaxraw'] > 0)))) {?>
            <div class="line-sub-total">
                <span class="discount-heading"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelSubTotal');?>
:</span>
                <span class="discount-price"><?php echo $_smarty_tpl->tpl_vars['checkbox']->value['subtotal'];?>
</span>
            </div>
                            <?php }?>
                            <?php }?>
                            <!-- SHOWZEROTAX OR DONT SHOWZEROTAX AND THE TAX > 0 -->
                            <?php if ((($_smarty_tpl->tpl_vars['showzerotax']->value) || ((!$_smarty_tpl->tpl_vars['showzerotax']->value) && ($_smarty_tpl->tpl_vars['checkbox']->value['displaytaxraw'] > 0)))) {?>
            <div class="line-sub-total">
                <span class="discount-heading"><?php echo $_smarty_tpl->tpl_vars['checkbox']->value['taxratename'];?>
 (<?php echo $_smarty_tpl->tpl_vars['checkbox']->value['taxrate'];?>
%):</span>
                <span class="discount-price"><?php echo $_smarty_tpl->tpl_vars['checkbox']->value['displaytax'];?>
</span>
            </div>
                        <?php }?>
            <div class="line-sub-total">
                <span class="discount-heading"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelOrderItemListItemTotal');?>
:</span>
                <span class="discount-price"><?php echo $_smarty_tpl->tpl_vars['checkbox']->value['displayprice'];?>
</span>
            </div>
                        <?php } else { ?>
            <div class="line-sub-total">
                <span class="discount-heading"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelOrderItemListItemTotal');?>
:</span>
                <span class="discount-price"><?php echo $_smarty_tpl->tpl_vars['checkbox']->value['subtotal'];?>
</span>
            </div>
                                <!-- SHOWTAXBREAKDOWN -->
                                <?php if (($_smarty_tpl->tpl_vars['showtaxbreakdown']->value)) {?>
                                    <?php if ((($_smarty_tpl->tpl_vars['showzerotax']->value) || ((!$_smarty_tpl->tpl_vars['showzerotax']->value) && ($_smarty_tpl->tpl_vars['checkbox']->value['displaytaxraw'] > 0)))) {?>
            <div class="line-sub-total-small-bottom"><?php echo $_smarty_tpl->tpl_vars['checkbox']->value['includesitemtaxtext'];?>
</div>
                                    <?php }?>
                                <?php }?>
                                <?php }?>

                            <?php }?>
                            <!-- DIFFERNETTAXRATES AND DONT SHOWPRICESWITHTAX -->
                            <?php if ((($_smarty_tpl->tpl_vars['differenttaxrates']->value) && (!$_smarty_tpl->tpl_vars['showpriceswithtax']->value))) {?>
                        <?php if ((($_smarty_tpl->tpl_vars['vouchersection']->value == 'TOTAL') && ($_smarty_tpl->tpl_vars['specialvouchertype']->value))) {?>
            <div class="line-sub-total">
                <span class="discount-heading"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelSubTotal');?>
:</span>
                <span class="discount-price"><?php echo $_smarty_tpl->tpl_vars['checkbox']->value['totalsell'];?>
</span>
            </div>
                            <?php if (($_smarty_tpl->tpl_vars['checkbox']->value['discountvalueraw'] > 0)) {?>
            <div class="line-sub-total">
                <span class="discount-heading"><?php echo $_smarty_tpl->tpl_vars['checkbox']->value['discountname'];?>
:</span>
                <span class="discount-price"><?php echo $_smarty_tpl->tpl_vars['checkbox']->value['discountvalue'];?>
</span>
            </div>
                            <!-- SHOWZEROTAX OR DONT SHOWZEROTAX AND THE TAX > 0 -->
                            <?php if ((($_smarty_tpl->tpl_vars['showzerotax']->value) || ((!$_smarty_tpl->tpl_vars['showzerotax']->value) && ($_smarty_tpl->tpl_vars['checkbox']->value['displaytaxraw'] > 0)))) {?>
            <div class="line-sub-total">
                <span class="discount-heading"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelSubTotal');?>
:</span>
                <span class="discount-price"><?php echo $_smarty_tpl->tpl_vars['checkbox']->value['subtotal'];?>
</span>
            </div>
                            <?php }?>
                            <?php }?>
                            <!-- SHOWZEROTAX OR DONT SHOWZEROTAX AND THE TAX > 0 -->
                            <?php if ((($_smarty_tpl->tpl_vars['showzerotax']->value) || ((!$_smarty_tpl->tpl_vars['showzerotax']->value) && ($_smarty_tpl->tpl_vars['checkbox']->value['displaytaxraw'] > 0)))) {?>
            <div class="line-sub-total">
                <span class="discount-heading"><?php echo $_smarty_tpl->tpl_vars['checkbox']->value['taxratename'];?>
 (<?php echo $_smarty_tpl->tpl_vars['checkbox']->value['taxrate'];?>
%):</span>
                <span class="discount-price"><?php echo $_smarty_tpl->tpl_vars['checkbox']->value['displaytax'];?>
</span>
            </div>
                            <?php }?>
            <div class="line-sub-total">
                <span class="discount-heading"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelOrderItemListItemTotal');?>
:</span>
                <span class="discount-price"><?php echo $_smarty_tpl->tpl_vars['checkbox']->value['displayprice'];?>
</span>
            </div>
                        <?php } else { ?>
                                    <!-- SHOWZEROTAX OR DONT SHOWZEROTAX AND THE TAX > 0 -->
                                    <?php if ((($_smarty_tpl->tpl_vars['showzerotax']->value) || ((!$_smarty_tpl->tpl_vars['showzerotax']->value) && ($_smarty_tpl->tpl_vars['checkbox']->value['displaytaxraw'] > 0)))) {?>
            <div class="line-sub-total">
                <span class="discount-heading"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelSubTotal');?>
:</span>
                <span class="discount-price"><?php echo $_smarty_tpl->tpl_vars['checkbox']->value['totalsell'];?>
</span>
            </div>
            <div class="line-sub-total">
                <span class="discount-heading"><?php echo $_smarty_tpl->tpl_vars['checkbox']->value['taxratename'];?>
 (<?php echo $_smarty_tpl->tpl_vars['checkbox']->value['taxrate'];?>
%):</span>
                <span class="discount-price"><?php echo $_smarty_tpl->tpl_vars['checkbox']->value['displaytax'];?>
</span>
            </div>
            <div class="line-sub-total">
                <span class="discount-heading"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelOrderItemListItemTotal');?>
:</span>
                <span class="discount-price"><?php echo $_smarty_tpl->tpl_vars['checkbox']->value['displayprice'];?>
</span>
            </div>
                                <?php } else { ?>
            <div class="line-sub-total">
                <span class="discount-heading"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelOrderItemListItemTotal');?>
:</span>
                <span class="discount-price"><?php echo $_smarty_tpl->tpl_vars['checkbox']->value['displayprice'];?>
</span>
            </div>
                                    <?php }?>
                                <?php }?>
                            <?php }?>
                            <!-- NOT DIFFERNETTAXRATES -->
                            <?php if ((!$_smarty_tpl->tpl_vars['differenttaxrates']->value)) {?>
                                <?php if ((($_smarty_tpl->tpl_vars['checkbox']->value['discountvalueraw'] > 0) && ($_smarty_tpl->tpl_vars['applyVoucherAsLineDiscount']->value))) {?>
            <div class="line-sub-total">
                <span class="discount-heading"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelSubTotal');?>
:</span>
                <span class="discount-price"><?php echo $_smarty_tpl->tpl_vars['checkbox']->value['totalsell'];?>
</span>
            </div>
            <div class="line-sub-total">
                <span class="discount-heading"><?php echo $_smarty_tpl->tpl_vars['checkbox']->value['discountname'];?>
:</span>
                <span class="discount-price"><?php echo $_smarty_tpl->tpl_vars['checkbox']->value['discountvalue'];?>
</span>
            </div>
                                <?php }?>

            <div class="line-sub-total">
                <span class="discount-heading"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelOrderItemListItemTotal');?>
:</span>
                <span class="discount-price"><?php echo $_smarty_tpl->tpl_vars['checkbox']->value['subtotal'];?>
</span>
            </div>
                            <?php }?>
                        <?php }?>
        </div>
                    <?php }?>
                <?php }?>
            <?php }?>
        <?php }?>
    <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
    <?php if ((isset($_smarty_tpl->tpl_vars['bTitleOrder']->value))) {?>
        <?php if ($_smarty_tpl->tpl_vars['stage']->value == 'payment') {?>
        <div class="marginBottomSub"></div>
        <?php }?>
    </div>
    <?php }?>

    <?php if ((isset($_smarty_tpl->tpl_vars['bTitleOrder']->value))) {?>

    <!-- order footer checkboxes end -->
    <div class="contentTotalOrderfooter">
        <?php if ($_smarty_tpl->tpl_vars['stage']->value == 'qty') {?>
            <?php if (($_smarty_tpl->tpl_vars['showpriceswithtax']->value == false)) {?>
        <div class="orderfooter-sub-total">
            <span class="total-heading"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelOrderFooterTotal');?>
:</span>
            <span class="order-line-price"><?php echo $_smarty_tpl->tpl_vars['orderfooteritemstotalsell']->value;?>
</span>
            <div class="clear"></div>
        </div>
            <?php } else { ?>
        <div class="orderfooter-sub-total">
            <span class="total-heading"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelOrderFooterTotal');?>
:</span>
            <span class="order-line-price"><?php echo $_smarty_tpl->tpl_vars['orderfootertotal']->value;?>
</span>
            <div class="clear"></div>
        </div>
            <?php }?>
        <?php }?>
                <?php if ($_smarty_tpl->tpl_vars['stage']->value == 'payment') {?>
            <?php if (($_smarty_tpl->tpl_vars['showpriceswithtax']->value == false)) {?>
                <?php if ((($_smarty_tpl->tpl_vars['showzerotax']->value) || ((!$_smarty_tpl->tpl_vars['showzerotax']->value) && ($_smarty_tpl->tpl_vars['orderfootertaxtotalraw']->value > 0)))) {?>
                    <?php if (($_smarty_tpl->tpl_vars['differenttaxrates']->value)) {?>
        <div class="orderfooter-sub-total">
            <span class="total-heading"><?php echo $_smarty_tpl->tpl_vars['orderfootersubtotalname']->value;?>
:</span>
            <span class="order-line-price"><?php echo $_smarty_tpl->tpl_vars['orderfootersubtotal']->value;?>
</span>
            <div class="clear"></div>
        </div>
                    <?php }?>
                    <?php if (($_smarty_tpl->tpl_vars['showtaxbreakdown']->value)) {?>
                        <?php if (($_smarty_tpl->tpl_vars['differenttaxrates']->value)) {?>
                            <?php if (($_smarty_tpl->tpl_vars['footertaxratesequal']->value == 1)) {?>
        <div class="orderfooter-sub-total">
            <span class="discount-heading"><?php echo $_smarty_tpl->tpl_vars['orderfootertaxname']->value;?>
 (<?php echo $_smarty_tpl->tpl_vars['orderfootertaxrate']->value;?>
%):</span>
            <span class="discount-price"><?php echo $_smarty_tpl->tpl_vars['orderfootertaxtotal']->value;?>
</span>
        </div>
                            <?php } else { ?>
        <div class="orderfooter-sub-total">
            <span class="discount-heading"><?php echo $_smarty_tpl->tpl_vars['orderfootertaxname']->value;?>
:</span>
            <span class="discount-price"><?php echo $_smarty_tpl->tpl_vars['orderfootertaxtotal']->value;?>
</span>
        </div>
                            <?php }?>
                        <?php }?>
                    <?php }?>
                <?php }?>
            <?php }?>

            <?php if (($_smarty_tpl->tpl_vars['showpriceswithtax']->value == false) && (($_smarty_tpl->tpl_vars['showzerotax']->value) || ((!$_smarty_tpl->tpl_vars['showzerotax']->value) && ($_smarty_tpl->tpl_vars['orderfootertaxtotalraw']->value > 0))) && ($_smarty_tpl->tpl_vars['differenttaxrates']->value)) {?>
        <div class="orderfooter-sub-total">
            <span class="discount-heading"><?php echo $_smarty_tpl->tpl_vars['orderfootertotalname']->value;?>
:</span>
            <span class="discount-price"><?php echo $_smarty_tpl->tpl_vars['orderfootertotal']->value;?>
</span>
        </div>
            <?php } else { ?>
        <div class="orderfooter-sub-total">
            <span class="total-heading"><?php echo $_smarty_tpl->tpl_vars['orderfootersubtotalname']->value;?>
:</span>
            <span class="order-line-price"><?php echo $_smarty_tpl->tpl_vars['orderfootersubtotal']->value;?>
</span>
            <div class="clear"></div>
        </div>
            <?php }?>

            <?php if (($_smarty_tpl->tpl_vars['showpriceswithtax']->value)) {?>
                <?php if ((($_smarty_tpl->tpl_vars['showzerotax']->value) || ((!$_smarty_tpl->tpl_vars['showzerotax']->value) && ($_smarty_tpl->tpl_vars['orderfootertaxtotalraw']->value > 0)))) {?>
                    <?php if (($_smarty_tpl->tpl_vars['showtaxbreakdown']->value)) {?>
                        <?php if (($_smarty_tpl->tpl_vars['differenttaxrates']->value)) {?>
        <div class="line-sub-total-small-bottom">
            <?php echo $_smarty_tpl->tpl_vars['includesorderfootertaxtext']->value;?>

        </div>
                        <?php }?>
                    <?php }?>
                <?php }?>
            <?php }?>


        <?php }?>
    </div>
    <?php }?>

    <?php if ((isset($_smarty_tpl->tpl_vars['bTitleOrder']->value)) && ($_smarty_tpl->tpl_vars['call_action']->value == 'init')) {?>
</div>
    <?php }
}?> <?php }
}
