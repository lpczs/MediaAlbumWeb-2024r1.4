<?php
/* Smarty version 4.5.3, created on 2026-03-14 01:34:31
  from 'C:\TAOPIX\MediaAlbumWeb\templates\order\orderline_large.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.5.3',
  'unifunc' => 'content_69b4bb27a7ec06_20611006',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '4c48334e6f39a72964f2f6004b01c0e6021a01a8' => 
    array (
      0 => 'C:\\TAOPIX\\MediaAlbumWeb\\templates\\order\\orderline_large.tpl',
      1 => 1729602708,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_69b4bb27a7ec06_20611006 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'C:\\TAOPIX\\MediaAlbumWeb\\libs\\external\\vendor\\smarty\\smarty\\libs\\plugins\\modifier.replace.php','function'=>'smarty_modifier_replace',),));
if ($_smarty_tpl->tpl_vars['orderline']->value['parentorderitemid'] == 0) {?>
    <?php $_smarty_tpl->_assignInScope('isCompanion', false);
} else { ?>
    <?php $_smarty_tpl->_assignInScope('isCompanion', true);
}?>

<?php if ($_smarty_tpl->tpl_vars['orderline']->value['orderlineid'] != -1) {?>
    <?php $_smarty_tpl->_assignInScope('multilinedesc', "false");?>
    <?php if ($_smarty_tpl->tpl_vars['call_action']->value == 'init') {?>
<div class="orderLineItem <?php if ($_smarty_tpl->tpl_vars['isCompanion']->value == true) {?>companion-line-item<?php }?>">
	<?php if ($_smarty_tpl->tpl_vars['isCompanion']->value == true) {?>
		<div class="companion-paper-clip-container">
		</div>
	<?php }?>
    <div class="item-list-title-bar">
	<?php if ($_smarty_tpl->tpl_vars['isCompanion']->value == true) {?>
        <div class="title-current"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_TitleCompanionAlbum');?>
</div>
	<?php } else { ?>
        <div class="title-current"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelHeaderItem');?>
 <?php echo $_smarty_tpl->tpl_vars['orderline']->value['orderlineid'];?>
</div>
	<?php }?>
	    <div class="title-quantity"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelQuantity');?>
</div>
        <div class="title-price"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelPrice');?>
</div>

        <div class="clear"></div>
    </div>
    <div class="content contentBloc">
        <div class="order-line <?php if ($_smarty_tpl->tpl_vars['isCompanion']->value == true) {?>companion-order-line<?php }?>" id="ordertableobj_<?php echo $_smarty_tpl->tpl_vars['orderline']->value['orderlineid'];?>
">
    <?php }?>
    <div class="contentTextInside">
		<?php if ($_smarty_tpl->tpl_vars['isCompanion']->value === true && $_smarty_tpl->tpl_vars['orderline']->value['assetrequest'] !== '') {?>
        <div class="product-preview-wrap">
		    <img class="product-preview-image" src="<?php echo htmlspecialchars((string)$_smarty_tpl->tpl_vars['orderline']->value['assetrequest'], ENT_QUOTES, 'UTF-8', true);?>
" alt="" />
        </div>
		<?php $_smarty_tpl->_assignInScope('productHeaderClass', " companion-product-header");?>
		<?php } elseif ($_smarty_tpl->tpl_vars['isCompanion']->value === true && $_smarty_tpl->tpl_vars['orderline']->value['assetrequest'] === '') {?>
        <div class="product-preview-wrap">
		    <img class="product-preview-image" src="<?php echo $_smarty_tpl->tpl_vars['brandroot']->value;?>
/images/companion_placeholder.png" alt="" />
        </div>
		<?php $_smarty_tpl->_assignInScope('productHeaderClass', " companion-product-header");?>
		<?php } elseif ($_smarty_tpl->tpl_vars['isCompanion']->value == false && $_smarty_tpl->tpl_vars['orderline']->value['projectthumbnail'] !== '') {?>
        <div class="product-preview-wrap">
		    <img class="product-preview-image" src="<?php echo htmlspecialchars((string)$_smarty_tpl->tpl_vars['orderline']->value['projectthumbnail'], ENT_QUOTES, 'UTF-8', true);?>
" data-asset="<?php echo htmlspecialchars((string)$_smarty_tpl->tpl_vars['orderline']->value['assetrequest'], ENT_QUOTES, 'UTF-8', true);?>
" alt="" />
        </div>
		<?php $_smarty_tpl->_assignInScope('productHeaderClass', '');?>
		<?php } elseif ($_smarty_tpl->tpl_vars['isCompanion']->value == false && $_smarty_tpl->tpl_vars['orderline']->value['assetrequest'] !== '') {?>
        <div class="product-preview-wrap">
		    <img class="product-preview-image" src="<?php echo htmlspecialchars((string)$_smarty_tpl->tpl_vars['orderline']->value['assetrequest'], ENT_QUOTES, 'UTF-8', true);?>
" alt="" />
        </div>
		<?php $_smarty_tpl->_assignInScope('productHeaderClass', '');?>
		<?php } else { ?>
		<div class="product-preview-wrap">
			<img class="product-preview-image" src="<?php echo $_smarty_tpl->tpl_vars['brandroot']->value;?>
/images/no_image-2x.jpg" alt="" />
		</div>
		<?php }?>
		<div class="product-header<?php echo $_smarty_tpl->tpl_vars['productHeaderClass']->value;?>
">
			<div class="product-bloc">
	
	<?php if ($_smarty_tpl->tpl_vars['isCompanion']->value == true) {?>
                <h3 class="product-title">
                    <?php echo $_smarty_tpl->tpl_vars['orderline']->value['itemproductname'];?>

                </h3>
	<?php } else { ?>
                <h3 class="product-title">
                <?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelProjectName');?>
: <?php echo $_smarty_tpl->tpl_vars['orderline']->value['projectname'];?>

                </h3>
                <div class="product-collection-title">
                    <?php echo $_smarty_tpl->tpl_vars['orderline']->value['itemproductname'];?>

                </div>
            <?php if ($_smarty_tpl->tpl_vars['orderline']->value['itemproducttype'] != 2) {?>
                <div class="product-collection-page-count">
                    <?php echo $_smarty_tpl->tpl_vars['orderline']->value['itempagecount'];?>

                    <?php if ($_smarty_tpl->tpl_vars['orderline']->value['itempagecount'] == 1) {?>
                        <?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelPage');?>

                    <?php } else { ?>
                        <?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelPages');?>

                    <?php }?>
                </div>
            <?php }?>
    <?php }?>
    <?php if ($_smarty_tpl->tpl_vars['orderline']->value['itemproductinfo'] != '') {?>
                <span class="product-info"><?php echo $_smarty_tpl->tpl_vars['orderline']->value['itemproductinfo'];?>
</span>
    <?php }?>
            </div>
            <div class="quantity">
    <?php if (($_smarty_tpl->tpl_vars['orderline']->value[$_smarty_tpl->tpl_vars['lockqty']->value] == true) || ($_smarty_tpl->tpl_vars['stage']->value == 'payment')) {?>
                <span class="quantityText"><?php echo $_smarty_tpl->tpl_vars['orderline']->value['itemqty'];?>
</span>
    <?php } else { ?>
        <input id="hiddeqty_<?php echo $_smarty_tpl->tpl_vars['orderline']->value['orderlineid'];?>
" type="hidden" class="hiddeqty" value="<?php echo $_smarty_tpl->tpl_vars['orderline']->value['itemqty'];?>
"/>
        <?php if (empty($_smarty_tpl->tpl_vars['orderline']->value['itemqtydropdown'])) {?>
                <input id="itemqty_<?php echo $_smarty_tpl->tpl_vars['orderline']->value['orderlineid'];?>
" type="text" class="quantity" data-decorator="fnUpdateOrderQty" maxlength="8" value="<?php echo $_smarty_tpl->tpl_vars['orderline']->value['itemqty'];?>
" data-lineid="<?php echo $_smarty_tpl->tpl_vars['orderline']->value['orderlineid'];?>
" />
                <img class="refresh" data-decorator="fnUpdateOrderQty" src="<?php echo $_smarty_tpl->tpl_vars['brandroot']->value;?>
/images/icons/refresh.png" alt="<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelOrderUpdateItemTotal');?>
" title="<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelOrderUpdateItemTotal');?>
" data-lineid="<?php echo $_smarty_tpl->tpl_vars['orderline']->value['orderlineid'];?>
" data-trigger="click" />
        <?php } else { ?>
                <select id="itemqty_<?php echo $_smarty_tpl->tpl_vars['orderline']->value['orderlineid'];?>
" data-decorator="fnUpdateOrderQty" data-trigger="change" data-lineid="<?php echo $_smarty_tpl->tpl_vars['orderline']->value['orderlineid'];?>
">
                <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['orderline']->value['itemqtydropdown'], 'qtyValue');
$_smarty_tpl->tpl_vars['qtyValue']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['qtyValue']->value) {
$_smarty_tpl->tpl_vars['qtyValue']->do_else = false;
?>
                    <option <?php if ($_smarty_tpl->tpl_vars['qtyValue']->value == $_smarty_tpl->tpl_vars['orderline']->value['itemqty']) {?>selected="selected"<?php }?> value="<?php echo $_smarty_tpl->tpl_vars['qtyValue']->value;?>
"><?php echo $_smarty_tpl->tpl_vars['qtyValue']->value;?>
</option>
                <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
                </select>
        <?php }?>
    <?php }?>
            </div>
            <div class="component-price priceBig">
                <?php if ($_smarty_tpl->tpl_vars['orderline']->value['itemshowproductsell'] == 1) {?>
                	<?php echo $_smarty_tpl->tpl_vars['orderline']->value['itemproducttotalsell'];?>

                <?php }?>
            </div>
            <div class="clear"></div>

    <?php if ($_smarty_tpl->tpl_vars['orderline']->value['displayassets'] == true) {?>
		<?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['orderline']->value['itemexternalassets'], 'asset');
$_smarty_tpl->tpl_vars['asset']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['asset']->value) {
$_smarty_tpl->tpl_vars['asset']->do_else = false;
?>
				<div class="product-bloc">
					<?php echo $_smarty_tpl->tpl_vars['asset']->value['pagename'];?>
: <?php echo $_smarty_tpl->tpl_vars['asset']->value['name'];?>

				</div>
				<div class="component-price-asset">
					<?php echo $_smarty_tpl->tpl_vars['asset']->value['charge'];?>

				</div>
				<div class="clear"></div>
		<?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
	<?php }?>

    <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['orderline']->value['itempictures'], 'sizegroup');
$_smarty_tpl->tpl_vars['sizegroup']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['sizegroup']->value) {
$_smarty_tpl->tpl_vars['sizegroup']->do_else = false;
?>
				<div class="photoPrintGroup">
					<div class="photoPrintGroupName" >
						<?php echo $_smarty_tpl->tpl_vars['sizegroup']->value['groupdisplayname'];?>

					</div>

					<div class="photoPrintGroupCount">
						<?php if ($_smarty_tpl->tpl_vars['sizegroup']->value['picturecount'] > 1) {?>
							<?php echo smarty_modifier_replace($_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelPrints'),'^0',$_smarty_tpl->tpl_vars['sizegroup']->value['picturecount']);?>

						<?php } else { ?>
							<?php echo smarty_modifier_replace($_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelPrint'),'^0',$_smarty_tpl->tpl_vars['sizegroup']->value['picturecount']);?>

						<?php }?>
					</div>

					<div class="photoPrintTotalSell">
						<?php echo $_smarty_tpl->tpl_vars['sizegroup']->value['formatedgrouptotalsell'];?>

					</div>
					<div class="clear"></div>
				</div>
    <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>

    <?php if (sizeof($_smarty_tpl->tpl_vars['orderline']->value['calendarcustomisations']) > 0) {?>

            <div class = "calblock" >

    <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['orderline']->value['calendarcustomisations'], 'calendarcustomisations');
$_smarty_tpl->tpl_vars['calendarcustomisations']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['calendarcustomisations']->value) {
$_smarty_tpl->tpl_vars['calendarcustomisations']->do_else = false;
?>

                    <div class = "calcomponentrow" >
                        <div class="calendarcomp-bloc">
                            <?php echo $_smarty_tpl->tpl_vars['calendarcustomisations']->value['name'];?>

                        </div>

                        <div class="calendarcomp_price">
                            <?php echo $_smarty_tpl->tpl_vars['calendarcustomisations']->value['formattedtotalsell'];?>

                        </div>
                    </div>
                    <div class="clear"></div>
    <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>

            </div>

    <?php }?>


		</div>
		<div class="clear"></div>
   </div>

	<?php if (sizeof($_smarty_tpl->tpl_vars['orderline']->value['aicomponent']) > 0) {?>
        <div class="customisationOption smart-design-component">
            <div class="componentbloc">
                <div class="section-title-header">
                    <span class="section-category-name"><?php echo $_smarty_tpl->tpl_vars['orderline']->value['aicomponent']['name'];?>
</span>
                </div>
                <div class="componentrow">
                    <img class="component-preview" src="<?php echo htmlspecialchars((string)$_smarty_tpl->tpl_vars['orderline']->value['aicomponent']['previewsrc'], ENT_QUOTES, 'UTF-8', true);?>
"/>
                    <div class="componentSectionTitle ">
                        <div class="component-name" >
                            <div class="componentContentText">
                                <?php echo $_smarty_tpl->tpl_vars['orderline']->value['aicomponent']['componentinfo'];?>

                            </div>
                        </div>
                    </div>
                    <div class="component-price">
                        <?php echo $_smarty_tpl->tpl_vars['orderline']->value['aicomponent']['formattedtotalsell'];?>

                    </div>
                    <div class="clear"></div>
                </div>
            </div>
        </div>
	<?php }?>

<?php }?>

<!-- checkboxes start -->
<?php if ($_smarty_tpl->tpl_vars['orderline']->value['orderlineid'] != -1) {?>
    <?php if (sizeof($_smarty_tpl->tpl_vars['orderline']->value['checkboxes']) > 0) {?>
        <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['orderline']->value['checkboxes'], 'checkbox');
$_smarty_tpl->tpl_vars['checkbox']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['checkbox']->value) {
$_smarty_tpl->tpl_vars['checkbox']->do_else = false;
?>
            <?php if ($_smarty_tpl->tpl_vars['checkbox']->value['showcomponentname'] == true) {?>
                <?php if (!(isset($_smarty_tpl->tpl_vars['bTitleComponent']->value)) && (($_smarty_tpl->tpl_vars['stage']->value == 'qty') || ($_smarty_tpl->tpl_vars['stage']->value == 'payment' && $_smarty_tpl->tpl_vars['checkbox']->value['checked'] == 1))) {?>
    <div class="customisationsHeader">
	<?php if ($_smarty_tpl->tpl_vars['isCompanion']->value == true) {?>
		<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelCompanionAlbumOptions');?>

	<?php } else { ?>
        <?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelProductOptions');?>

	<?php }?>
        <span class="linkToggle">
                    <?php if ($_smarty_tpl->tpl_vars['stage']->value == 'payment') {?>
            <span id="link_<?php echo $_smarty_tpl->tpl_vars['orderline']->value['orderlineid'];?>
" class="hide-product-options-link" data-decorator="fnToggleGeneric" data-lineid="<?php echo $_smarty_tpl->tpl_vars['orderline']->value['orderlineid'];?>
" data-idelm="contentCustomise_<?php echo $_smarty_tpl->tpl_vars['orderline']->value['orderlineid'];?>
" data-colour="grey"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_OrderShow');?>
</span>
                    <?php } else { ?>
            <span id="link_<?php echo $_smarty_tpl->tpl_vars['orderline']->value['orderlineid'];?>
" class="hide-product-options-link" data-decorator="fnToggleGeneric" data-lineid="<?php echo $_smarty_tpl->tpl_vars['orderline']->value['orderlineid'];?>
" data-idelm="contentCustomise_<?php echo $_smarty_tpl->tpl_vars['orderline']->value['orderlineid'];?>
" data-colour="grey"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_OrderHide');?>
</span>
                    <?php }?>
        </span>
        <div class="clear"></div>
		<?php $_smarty_tpl->_assignInScope('bTitleComponent', "true");?>
    </div>

    <div id="contentCustomise_<?php echo $_smarty_tpl->tpl_vars['orderline']->value['orderlineid'];?>
" <?php if ($_smarty_tpl->tpl_vars['stage']->value == 'payment') {?>style="display:none;"<?php }?>>
                <?php }?>
                <?php if ((($_smarty_tpl->tpl_vars['stage']->value == 'payment' && $_smarty_tpl->tpl_vars['checkbox']->value['checked'] == 1) || $_smarty_tpl->tpl_vars['stage']->value == 'qty')) {?>
		<div class="customisationOption <?php if ($_smarty_tpl->tpl_vars['isCompanion']->value == true) {?>companion-customisationOption<?php }?>">
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
					<img class="component-preview" src="<?php echo htmlspecialchars((string)$_smarty_tpl->tpl_vars['checkbox']->value['componentpreviewsrc'], ENT_QUOTES, 'UTF-8', true);?>
" alt=""/>
					<div class="component-name <?php if ($_smarty_tpl->tpl_vars['isCompanion']->value == true) {?>companion-component-name<?php }?>">
						<?php } else { ?>
					<div class="component-name-long <?php if ($_smarty_tpl->tpl_vars['isCompanion']->value == true) {?>companion-component-name-long<?php }?>">
						<?php }?>
						<div class="componentContentText">
							<div class="section-title">
							<?php if (($_smarty_tpl->tpl_vars['checkbox']->value['itemcomponentname'] != '')) {?>
								<span class="section-category-name"><?php echo $_smarty_tpl->tpl_vars['checkbox']->value['itemcomponentname'];?>
</span>
							<?php }?>
							</div>
							<?php if (!empty($_smarty_tpl->tpl_vars['checkbox']->value['itemcomponentinfo'])) {?>
							<div class="checkbox-info">
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
							<div class="checkbox-info">
								<?php echo $_smarty_tpl->tpl_vars['checkbox']->value['itemcomponentpriceinfo'];?>

							</div>
								<?php $_smarty_tpl->_assignInScope('multilinedesc', "true");?>
							<?php }?>
							<?php if ($_smarty_tpl->tpl_vars['stage']->value == 'qty') {?>
							(<?php echo $_smarty_tpl->tpl_vars['checkbox']->value['totalsell'];?>
)
							<?php }?>
							<div class="clear"></div>
						</div>
							<?php if ($_smarty_tpl->tpl_vars['stage']->value == 'qty') {?>
								<?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['checkbox']->value['itemcomponentbuttons'], 'button');
$_smarty_tpl->tpl_vars['button']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['button']->value) {
$_smarty_tpl->tpl_vars['button']->do_else = false;
?>
						<div class="contentBtn btnRight <?php if ($_smarty_tpl->tpl_vars['multilinedesc']->value == "true") {?>paddingCenter<?php }?>" data-decorator="<?php echo $_smarty_tpl->tpl_vars['button']->value['action'];?>
" data-orderlineid="<?php echo $_smarty_tpl->tpl_vars['orderline']->value['orderlineid'];?>
" data-sectionlineid="<?php echo $_smarty_tpl->tpl_vars['checkbox']->value['orderlineid'];?>
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
					</div>
						<?php if (($_smarty_tpl->tpl_vars['checkbox']->value['pricingmodel'] == 7 || $_smarty_tpl->tpl_vars['checkbox']->value['pricingmodel'] == 8) && $_smarty_tpl->tpl_vars['checkbox']->value['checked'] == 1) {?>
					<div class="quantity <?php if ($_smarty_tpl->tpl_vars['multilinedesc']->value == "true") {?>paddingCenter<?php }?>">
							<?php if ($_smarty_tpl->tpl_vars['stage']->value == 'payment') {?>
						<span class="quantityText <?php if ($_smarty_tpl->tpl_vars['isCompanion']->value == true) {?>companion-quantityText<?php }?>"><?php echo $_smarty_tpl->tpl_vars['checkbox']->value['quantity'];?>
</span>
							<?php } else { ?>
						<input id="hiddeqty_<?php echo $_smarty_tpl->tpl_vars['checkbox']->value['orderlineid'];?>
" type="hidden" class="hiddeqtyCpt" value="<?php echo $_smarty_tpl->tpl_vars['checkbox']->value['quantity'];?>
"/>
								<?php if (empty($_smarty_tpl->tpl_vars['checkbox']->value['itemqtydropdown'])) {?>
						<input id="itemqty_<?php echo $_smarty_tpl->tpl_vars['checkbox']->value['orderlineid'];?>
" type="text" class="quantity" data-decorator="fnUpdateComponentQty" data-trigger="keypress" data-lineid="<?php echo $_smarty_tpl->tpl_vars['checkbox']->value['orderlineid'];?>
" data-itemqty="<?php echo $_smarty_tpl->tpl_vars['checkbox']->value['itemqty'];?>
" maxlength="8" value="<?php echo $_smarty_tpl->tpl_vars['checkbox']->value['quantity'];?>
" />
						<img class="refresh" data-decorator="fnUpdateComponentQty" src="<?php echo $_smarty_tpl->tpl_vars['brandroot']->value;?>
/images/icons/refresh.png" data-trigger="click" data-lineid="<?php echo $_smarty_tpl->tpl_vars['checkbox']->value['orderlineid'];?>
" data-itemqty="<?php echo $_smarty_tpl->tpl_vars['checkbox']->value['itemqty'];?>
" alt="<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelOrderUpdateItemTotal');?>
" title="<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelOrderUpdateItemTotal');?>
" />
								<?php } else { ?>
						<select id="itemqty_<?php echo $_smarty_tpl->tpl_vars['checkbox']->value['orderlineid'];?>
" data-decorator="fnUpdateComponentQty" data-trigger="change" data-lineid="<?php echo $_smarty_tpl->tpl_vars['checkbox']->value['orderlineid'];?>
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
						<?php if (($_smarty_tpl->tpl_vars['checkbox']->value['metadatahtml']) && ($_smarty_tpl->tpl_vars['checkbox']->value['checked'])) {?>
				<div id="metadatarow_<?php echo $_smarty_tpl->tpl_vars['checkbox']->value['orderlineid'];?>
" class="component-metadata">
					<?php echo $_smarty_tpl->tpl_vars['checkbox']->value['metadatahtml'];?>

				</div>
						<?php }?>
			</div>
		</div>
        <div class="clear"></div>
                <?php }?>
            <?php }?>
        <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
    <?php }
}?>
<!-- checkboxes end -->

<!-- sections start -->
<?php if (sizeof($_smarty_tpl->tpl_vars['orderline']->value['sections']) > 0 && !(isset($_smarty_tpl->tpl_vars['bTitleComponent']->value))) {?>
    <div class="customisationsHeader <?php if ($_smarty_tpl->tpl_vars['isCompanion']->value == true) {?>companion-customisations<?php }?>">
	<?php if ($_smarty_tpl->tpl_vars['isCompanion']->value == true) {?>
		<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelCompanionAlbumOptions');?>

	<?php } else { ?>
        <?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelProductOptions');?>

	<?php }?>
		<span class="linkToggle">
    <?php if ($_smarty_tpl->tpl_vars['stage']->value == 'payment') {?>
			<span id="link_<?php echo $_smarty_tpl->tpl_vars['orderline']->value['orderlineid'];?>
" class="hide-product-options-link" data-decorator="fnToggleGeneric" data-lineid="<?php echo $_smarty_tpl->tpl_vars['orderline']->value['orderlineid'];?>
" data-idelm="contentCustomise_<?php echo $_smarty_tpl->tpl_vars['orderline']->value['orderlineid'];?>
" data-colour="grey"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_OrderShow');?>
</span>
    <?php } else { ?>
			<span id="link_<?php echo $_smarty_tpl->tpl_vars['orderline']->value['orderlineid'];?>
" class="hide-product-options-link" data-decorator="fnToggleGeneric" data-lineid="<?php echo $_smarty_tpl->tpl_vars['orderline']->value['orderlineid'];?>
" data-idelm="contentCustomise_<?php echo $_smarty_tpl->tpl_vars['orderline']->value['orderlineid'];?>
" data-colour="grey"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_OrderHide');?>
</span>
    <?php }?>
		</span>
        <div class="clear"></div>
    </div>
    <div id="contentCustomise_<?php echo $_smarty_tpl->tpl_vars['orderline']->value['orderlineid'];?>
" <?php if ($_smarty_tpl->tpl_vars['stage']->value == 'payment') {?>style="display:none;"<?php }?>>
	<?php $_smarty_tpl->_assignInScope('bTitleComponent', "true");
}?>
    <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['orderline']->value['sections'], 'section');
$_smarty_tpl->tpl_vars['section']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['section']->value) {
$_smarty_tpl->tpl_vars['section']->do_else = false;
?> 	<div class="customisationOption <?php if ($_smarty_tpl->tpl_vars['isCompanion']->value == true) {?>companion-customisationOption<?php }?>">
		<div id="componentContent_<?php echo $_smarty_tpl->tpl_vars['section']->value['orderlineid'];?>
">
			<?php if ($_smarty_tpl->tpl_vars['section']->value['showcomponentname'] == true) {?>
			<div id="componentrow_<?php echo $_smarty_tpl->tpl_vars['section']->value['orderlineid'];?>
" class="componentbloc" >
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
					<div class="componentSectionTitle <?php if ($_smarty_tpl->tpl_vars['isCompanion']->value == true) {?>companion-componentSectionTitle<?php }?>">
				<?php } else { ?>
					<div class="componentSectionTitleLong <?php if ($_smarty_tpl->tpl_vars['isCompanion']->value == true) {?>companion-componentSectionTitleLong<?php }?>">
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
							<div class="section-moreinfo">
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
							<div class="section-moreinfo-long">
								<a href="<?php echo $_smarty_tpl->tpl_vars['section']->value['itemcomponentmoreinfolinkurl'];?>
" target="_blank"><?php echo $_smarty_tpl->tpl_vars['section']->value['itemcomponentmoreinfolinktext'];?>
</a>
							</div>
							<?php $_smarty_tpl->_assignInScope('multilinedesc', "true");?>
					<?php }?>
					<?php if (!empty($_smarty_tpl->tpl_vars['section']->value['itemcomponentpriceinfo'])) {?>
							<div class="clear"></div>
							<div class="section-info-long">
								<?php echo $_smarty_tpl->tpl_vars['section']->value['itemcomponentpriceinfo'];?>

							</div>
							<?php $_smarty_tpl->_assignInScope('multilinedesc', "true");?>
					<?php }?>
				<?php }?>
							<div class="clear"></div>
						</div>
				<?php if ($_smarty_tpl->tpl_vars['stage']->value == 'qty') {?>
					<?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['section']->value['itemcomponentbuttons'], 'button');
$_smarty_tpl->tpl_vars['button']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['button']->value) {
$_smarty_tpl->tpl_vars['button']->do_else = false;
?>
						<div class="contentBtn btnRight <?php if ($_smarty_tpl->tpl_vars['multilinedesc']->value == "true") {?>paddingCenter<?php }?>" data-decorator="<?php echo $_smarty_tpl->tpl_vars['button']->value['action'];?>
" data-orderlineid="<?php echo $_smarty_tpl->tpl_vars['orderline']->value['orderlineid'];?>
" data-sectionlineid="<?php echo $_smarty_tpl->tpl_vars['section']->value['orderlineid'];?>
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
				</div>
				<?php if ($_smarty_tpl->tpl_vars['section']->value['pricingmodel'] == 7 || $_smarty_tpl->tpl_vars['section']->value['pricingmodel'] == 8) {?>
					<div class="quantity <?php if ($_smarty_tpl->tpl_vars['multilinedesc']->value == "true") {?>paddingCenter<?php }?>">
					<?php if ($_smarty_tpl->tpl_vars['stage']->value == 'payment') {?>
						<span class="quantityText <?php if ($_smarty_tpl->tpl_vars['isCompanion']->value == true) {?>companion-quantityText<?php }?>"><?php echo $_smarty_tpl->tpl_vars['section']->value['quantity'];?>
</span>
					<?php } else { ?>
						<input id="hiddeqty_<?php echo $_smarty_tpl->tpl_vars['section']->value['orderlineid'];?>
" type="hidden" class="hiddeqtyCpt" value="<?php echo $_smarty_tpl->tpl_vars['section']->value['quantity'];?>
"/>
						<?php if (empty($_smarty_tpl->tpl_vars['section']->value['itemqtydropdown'])) {?>
						<input id="itemqty_<?php echo $_smarty_tpl->tpl_vars['section']->value['orderlineid'];?>
" type="text" class="quantity" data-decorator="fnUpdateComponentQty" data-trigger="keypress" data-lineid="<?php echo $_smarty_tpl->tpl_vars['section']->value['orderlineid'];?>
" data-itemqty="<?php echo $_smarty_tpl->tpl_vars['section']->value['itemqty'];?>
" maxlength="8" value="<?php echo $_smarty_tpl->tpl_vars['section']->value['quantity'];?>
" />
						<img class="refresh" data-decorator="fnUpdateComponentQty" src="<?php echo $_smarty_tpl->tpl_vars['brandroot']->value;?>
/images/icons/refresh.png" data-trigger="click" data-lineid="<?php echo $_smarty_tpl->tpl_vars['section']->value['orderlineid'];?>
" data-itemqty="<?php echo $_smarty_tpl->tpl_vars['section']->value['itemqty'];?>
" alt="<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelOrderUpdateItemTotal');?>
" title="<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelOrderUpdateItemTotal');?>
" />
						<?php } else { ?>
						<select id="itemqty_<?php echo $_smarty_tpl->tpl_vars['section']->value['orderlineid'];?>
" data-decorator="fnUpdateComponentQty" data-trigger="change" data-lineid="<?php echo $_smarty_tpl->tpl_vars['section']->value['orderlineid'];?>
" data-itemqty="<?php echo $_smarty_tpl->tpl_vars['section']->value['itemqty'];?>
">
							<?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['section']->value['itemqtydropdown'], 'qtyValue');
$_smarty_tpl->tpl_vars['qtyValue']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['qtyValue']->value) {
$_smarty_tpl->tpl_vars['qtyValue']->do_else = false;
?>
							<option <?php if ($_smarty_tpl->tpl_vars['qtyValue']->value == $_smarty_tpl->tpl_vars['section']->value['quantity']) {?>selected="selected"<?php }?> value="<?php echo $_smarty_tpl->tpl_vars['qtyValue']->value;?>
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
						<?php echo $_smarty_tpl->tpl_vars['section']->value['totalsell'];?>

					</div>
					<?php if ($_smarty_tpl->tpl_vars['multilinedesc']->value) {?>
						<?php $_smarty_tpl->_assignInScope('multilinedesc', "false");?>
					<?php }?>
					<div class="clear"></div>
				</div>
					<?php if ($_smarty_tpl->tpl_vars['section']->value['metadatahtml']) {?>
				<div class="component-metadata">
					<?php echo $_smarty_tpl->tpl_vars['section']->value['metadatahtml'];?>

				</div>
				<?php }?>
			</div>
			<?php }?>
			<!-- sub-sections of component start -->
			<?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['section']->value['subsections'], 'subsection');
$_smarty_tpl->tpl_vars['subsection']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['subsection']->value) {
$_smarty_tpl->tpl_vars['subsection']->do_else = false;
?> 				<?php if ($_smarty_tpl->tpl_vars['subsection']->value['showcomponentname'] == true) {?>
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
" alt=""/>
					<div class="componentSubSectionTitle <?php if ($_smarty_tpl->tpl_vars['isCompanion']->value == true) {?>companion-componentSubSectionTitle<?php }?>">
					<?php } else { ?>
					<div class="componentSubSectionTitleLong <?php if ($_smarty_tpl->tpl_vars['isCompanion']->value == true) {?>companion-componentSubSectionTitleLong<?php }?>">
					<?php }?>
						<div class="componentContentText">
							<div class="section-title"><?php echo $_smarty_tpl->tpl_vars['subsection']->value['itemcomponentname'];?>
</div>
					<!-- START add-edit-change-remove links -->

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
					<?php if ($_smarty_tpl->tpl_vars['stage']->value == 'qty') {?>
						<?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['subsection']->value['itemcomponentbuttons'], 'button');
$_smarty_tpl->tpl_vars['button']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['button']->value) {
$_smarty_tpl->tpl_vars['button']->do_else = false;
?>
						<div class="contentBtn btnRight <?php if ($_smarty_tpl->tpl_vars['multilinedesc']->value == "true") {?>paddingCenter<?php }?>" data-decorator="<?php echo $_smarty_tpl->tpl_vars['button']->value['action'];?>
" data-orderlineid="<?php echo $_smarty_tpl->tpl_vars['orderline']->value['orderlineid'];?>
" data-sectionlineid="<?php echo $_smarty_tpl->tpl_vars['subsection']->value['orderlineid'];?>
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
" type="text" class="quantity" data-decorator="fnUpdateComponentQty" data-trigger="keypress" data-lineid="<?php echo $_smarty_tpl->tpl_vars['subsection']->value['orderlineid'];?>
" data-itemqty="<?php echo $_smarty_tpl->tpl_vars['subsection']->value['itemqty'];?>
" maxlength="8" value="<?php echo $_smarty_tpl->tpl_vars['subsection']->value['quantity'];?>
" />
						<img class="refresh" data-decorator="fnUpdateComponentQty" src="<?php echo $_smarty_tpl->tpl_vars['brandroot']->value;?>
/images/icons/refresh.png" data-trigger="click" data-lineid="<?php echo $_smarty_tpl->tpl_vars['subsection']->value['orderlineid'];?>
" data-itemqty="<?php echo $_smarty_tpl->tpl_vars['subsection']->value['itemqty'];?>
" alt="<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelOrderUpdateItemTotal');?>
" title="<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelOrderUpdateItemTotal');?>
" />
							<?php } else { ?>
						<select id="itemqty_<?php echo $_smarty_tpl->tpl_vars['subsection']->value['orderlineid'];?>
" data-decorator="fnUpdateComponentQty" data-trigger="change" data-lineid="<?php echo $_smarty_tpl->tpl_vars['subsection']->value['orderlineid'];?>
" data-itemqty="<?php echo $_smarty_tpl->tpl_vars['subsection']->value['itemqty'];?>
">
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
				<div class="subcomponent-metadata">
					<?php echo $_smarty_tpl->tpl_vars['subsection']->value['metadatahtml'];?>

				</div>
					<?php }?>
			</div>
				<?php }?>
			<?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?> 			 <!-- sub-sections of component end -->

			<!-- checkboxes inside component start -->
			<?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['section']->value['checkboxes'], 'checkbox');
$_smarty_tpl->tpl_vars['checkbox']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['checkbox']->value) {
$_smarty_tpl->tpl_vars['checkbox']->do_else = false;
?> 				<?php if ($_smarty_tpl->tpl_vars['checkbox']->value['showcomponentname'] == true) {?>
					<?php if (($_smarty_tpl->tpl_vars['stage']->value == 'qty') || ($_smarty_tpl->tpl_vars['stage']->value == 'payment' && $_smarty_tpl->tpl_vars['checkbox']->value['checked'] == 1)) {?>
		<div ><!--class="customisationOption <?php if ($_smarty_tpl->tpl_vars['isCompanion']->value == true) {?>companion-customisationOption<?php }?>"> -->
			<div id="componentrow_<?php echo $_smarty_tpl->tpl_vars['checkbox']->value['orderlineid'];?>
" class="subsection">
				<div class="subcheckboxBloc">
					<div class="section-title-header">
						<?php if (($_smarty_tpl->tpl_vars['checkbox']->value['itemcomponentcategoryname'] != '')) {?>
						<span class="section-category-name"><?php echo $_smarty_tpl->tpl_vars['checkbox']->value['itemcomponentcategoryname'];?>
:</span> <span class="section-category-prompt"><?php echo $_smarty_tpl->tpl_vars['checkbox']->value['itemcomponentprompt'];?>
</span>
						<?php }?>
					</div>
						<?php if ($_smarty_tpl->tpl_vars['checkbox']->value['haspreview'] > 0) {?>
					<img class="component-preview" src="<?php echo htmlspecialchars((string)$_smarty_tpl->tpl_vars['checkbox']->value['componentpreviewsrc'], ENT_QUOTES, 'UTF-8', true);?>
" alt=""/>
					<div class="componentSubSectionTitle <?php if ($_smarty_tpl->tpl_vars['isCompanion']->value == true) {?>companion-componentSubSectionTitle<?php }?>">
						<?php } else { ?>
					<div class="componentSubSectionTitleLong <?php if ($_smarty_tpl->tpl_vars['isCompanion']->value == true) {?>companion-componentSubSectionTitleLong<?php }?>">
						<?php }?>
						 <div class="componentContentText">
							<div class="section-title"><?php echo $_smarty_tpl->tpl_vars['checkbox']->value['itemcomponentname'];?>
</div>
						<?php if (!empty($_smarty_tpl->tpl_vars['checkbox']->value['itemcomponentinfo'])) {?>
						<div class="checkbox-info">
							<?php echo $_smarty_tpl->tpl_vars['checkbox']->value['itemcomponentinfo'];?>

						</div>
							<?php $_smarty_tpl->_assignInScope('multilinedesc', "true");?>
						<?php }?>
						<?php if (!empty($_smarty_tpl->tpl_vars['checkbox']->value['itemcomponentmoreinfolinkurl'])) {?>
							<div class="checkbox-moreinfo">
								<a href="<?php echo $_smarty_tpl->tpl_vars['checkbox']->value['itemcomponentmoreinfolinkurl'];?>
" target="_blank"><?php echo $_smarty_tpl->tpl_vars['checkbox']->value['itemcomponentmoreinfolinktext'];?>
</a>
							</div>
							<?php $_smarty_tpl->_assignInScope('multilinedesc', "true");?>
						<?php }?>
						<?php if (!empty($_smarty_tpl->tpl_vars['checkbox']->value['itemcomponentpriceinfo'])) {?>
						<div class="checkbox-info">
							<?php echo $_smarty_tpl->tpl_vars['checkbox']->value['itemcomponentpriceinfo'];?>

						</div>
							<?php $_smarty_tpl->_assignInScope('multilinedesc', "true");?>
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
" data-orderlineid="<?php echo $_smarty_tpl->tpl_vars['orderline']->value['orderlineid'];?>
" data-sectionlineid="<?php echo $_smarty_tpl->tpl_vars['checkbox']->value['orderlineid'];?>
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
					</div>
						<?php if (($_smarty_tpl->tpl_vars['checkbox']->value['pricingmodel'] == 7 || $_smarty_tpl->tpl_vars['checkbox']->value['pricingmodel'] == 8) && $_smarty_tpl->tpl_vars['checkbox']->value['checked'] == 1) {?>
					<div class="quantity <?php if ($_smarty_tpl->tpl_vars['multilinedesc']->value == "true") {?>paddingCenter<?php }?>">
							<?php if ($_smarty_tpl->tpl_vars['stage']->value == 'payment') {?>
						<span class="quantityText <?php if ($_smarty_tpl->tpl_vars['isCompanion']->value == true) {?>companion-quantityText<?php }?>"><?php echo $_smarty_tpl->tpl_vars['checkbox']->value['quantity'];?>
</span>
							<?php } else { ?>
						<input id="hiddeqty_<?php echo $_smarty_tpl->tpl_vars['checkbox']->value['orderlineid'];?>
" type="hidden" class="hiddeqtyCpt" value="<?php echo $_smarty_tpl->tpl_vars['checkbox']->value['quantity'];?>
"/>
								<?php if (empty($_smarty_tpl->tpl_vars['checkbox']->value['itemqtydropdown'])) {?>
						<input id="itemqty_<?php echo $_smarty_tpl->tpl_vars['checkbox']->value['orderlineid'];?>
" type="text" class="quantity" data-decorator="fnUpdateComponentQty" data-trigger="keypress" data-lineid="<?php echo $_smarty_tpl->tpl_vars['checkbox']->value['orderlineid'];?>
" data-itemqty="<?php echo $_smarty_tpl->tpl_vars['checkbox']->value['itemqty'];?>
" maxlength="8" value="<?php echo $_smarty_tpl->tpl_vars['checkbox']->value['quantity'];?>
" />
						<img class="refresh" data-decorator="fnUpdateComponentQty" src="<?php echo $_smarty_tpl->tpl_vars['brandroot']->value;?>
/images/icons/refresh.png" data-trigger="click" data-lineid="<?php echo $_smarty_tpl->tpl_vars['checkbox']->value['orderlineid'];?>
" data-itemqty="<?php echo $_smarty_tpl->tpl_vars['checkbox']->value['itemqty'];?>
" alt="<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelOrderUpdateItemTotal');?>
" title="<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelOrderUpdateItemTotal');?>
" />
								<?php } else { ?>
						<select id="itemqty_<?php echo $_smarty_tpl->tpl_vars['checkbox']->value['orderlineid'];?>
" data-decorator="fnUpdateComponentQty" data-trigger="change" data-lineid="<?php echo $_smarty_tpl->tpl_vars['checkbox']->value['orderlineid'];?>
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
					</div>

			<div class="clear"></div>
					<?php }?>
				<?php }?>
			<?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?> 			<!-- checkboxes inside component end -->
		</div>
    </div>
    <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?> 	<!-- sections end -->

	<!-- linefooter sections start -->
    <?php if ($_smarty_tpl->tpl_vars['orderline']->value['orderlineid'] != -1) {?>
        <?php if (sizeof($_smarty_tpl->tpl_vars['orderline']->value['linefootersections']) > 0) {?>

            <?php if (!(isset($_smarty_tpl->tpl_vars['bTitleComponent']->value))) {?>
 <div class="customisationsHeader <?php if ($_smarty_tpl->tpl_vars['isCompanion']->value == true) {?>companion-customisations<?php }?>">
	<?php if ($_smarty_tpl->tpl_vars['isCompanion']->value == true) {?>
		<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelCompanionAlbumOptions');?>

	<?php } else { ?>
        <?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelProductOptions');?>

	<?php }?>
		<span class="linkToggle">
    <?php if ($_smarty_tpl->tpl_vars['stage']->value == 'payment') {?>
			<span id="link_<?php echo $_smarty_tpl->tpl_vars['orderline']->value['orderlineid'];?>
" class="hide-product-options-link" data-decorator="fnToggleGeneric" data-lineid="<?php echo $_smarty_tpl->tpl_vars['orderline']->value['orderlineid'];?>
" data-idelm="contentCustomise_<?php echo $_smarty_tpl->tpl_vars['orderline']->value['orderlineid'];?>
" data-colour="grey"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_OrderShow');?>
</span>
    <?php } else { ?>
			<span id="link_<?php echo $_smarty_tpl->tpl_vars['orderline']->value['orderlineid'];?>
" class="hide-product-options-link" data-decorator="fnToggleGeneric" data-lineid="<?php echo $_smarty_tpl->tpl_vars['orderline']->value['orderlineid'];?>
" data-idelm="contentCustomise_<?php echo $_smarty_tpl->tpl_vars['orderline']->value['orderlineid'];?>
" data-colour="grey"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_OrderHide');?>
</span>
    <?php }?>
		</span>
        <div class="clear"></div>
    </div>

    <div id="contentCustomise_<?php echo $_smarty_tpl->tpl_vars['orderline']->value['orderlineid'];?>
" <?php if ($_smarty_tpl->tpl_vars['stage']->value == 'payment') {?>style="display:none;"<?php }?>>
				<?php $_smarty_tpl->_assignInScope('bTitleComponent', "true");?>
            <?php }?>

            <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['orderline']->value['linefootersections'], 'section');
$_smarty_tpl->tpl_vars['section']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['section']->value) {
$_smarty_tpl->tpl_vars['section']->do_else = false;
?> 		<div class="customisationOption <?php if ($_smarty_tpl->tpl_vars['isCompanion']->value == true) {?>companion-customisationOption<?php }?>">
                <?php if ($_smarty_tpl->tpl_vars['section']->value['showcomponentname'] == true) {?>
    <div id="componentrow_<?php echo $_smarty_tpl->tpl_vars['section']->value['orderlineid'];?>
" class="componentbloc">
		<div class="section-title-header">
					<?php if (($_smarty_tpl->tpl_vars['section']->value['sectionlabel'] != '')) {?>
			<span class="section-category-name"><?php echo $_smarty_tpl->tpl_vars['section']->value['sectionlabel'];?>
:</span> <span class="section-category-prompt"><?php echo $_smarty_tpl->tpl_vars['section']->value['prompt'];?>
</span>
					<?php }?>
		</div>
		<div class="componentrow" >
                    <?php if ($_smarty_tpl->tpl_vars['section']->value['haspreview'] > 0) {?>
            <img class="component-preview" src="<?php echo htmlspecialchars((string)$_smarty_tpl->tpl_vars['section']->value['componentpreviewsrc'], ENT_QUOTES, 'UTF-8', true);?>
" alt=""/>
            <div class="componentSectionTitle <?php if ($_smarty_tpl->tpl_vars['isCompanion']->value == true) {?>companion-componentSectionTitle<?php }?>">
                    <?php } else { ?>
            <div class="componentSectionTitleLong <?php if ($_smarty_tpl->tpl_vars['isCompanion']->value == true) {?>companion-componentSectionTitleLong<?php }?>">
                    <?php }?>
                <div class="componentContentText">
                    <div class="section-title">
                        <?php echo $_smarty_tpl->tpl_vars['section']->value['itemcomponentname'];?>

                    </div>
                    <?php if ($_smarty_tpl->tpl_vars['section']->value['haspreview'] > 0) {?>
                        <?php if (!empty($_smarty_tpl->tpl_vars['section']->value['itemcomponentinfo'])) {?>
                    <div class="section-info">
                        <?php echo $_smarty_tpl->tpl_vars['section']->value['itemcomponentinfo'];?>

                    </div>
                            <?php $_smarty_tpl->_assignInScope('multilinedesc', "true");?>
                        <?php }?>
						<?php if (!empty($_smarty_tpl->tpl_vars['section']->value['itemcomponentmoreinfolinkurl'])) {?>
					<div class="section-moreinfo">
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
					<div class="section-moreinfo-long">
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
" data-orderlineid="<?php echo $_smarty_tpl->tpl_vars['orderline']->value['orderlineid'];?>
" data-sectionlineid="<?php echo $_smarty_tpl->tpl_vars['section']->value['orderlineid'];?>
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
                <span class="quantityText <?php if ($_smarty_tpl->tpl_vars['isCompanion']->value == true) {?>companion-quantityText<?php }?>"><?php echo $_smarty_tpl->tpl_vars['section']->value['quantity'];?>
</span>
                        <?php } else { ?>
                <input id="hiddeqty_<?php echo $_smarty_tpl->tpl_vars['section']->value['orderlineid'];?>
" type="hidden" class="hiddeqtyCpt" value="<?php echo $_smarty_tpl->tpl_vars['section']->value['quantity'];?>
"/>
                            <?php if (empty($_smarty_tpl->tpl_vars['section']->value['itemqtydropdown'])) {?>
                <input id="itemqty_<?php echo $_smarty_tpl->tpl_vars['section']->value['orderlineid'];?>
" type="text" class="quantity" data-decorator="fnUpdateComponentQty" data-trigger="keypress" data-lineid="<?php echo $_smarty_tpl->tpl_vars['section']->value['orderlineid'];?>
" data-itemqty="<?php echo $_smarty_tpl->tpl_vars['section']->value['itemqty'];?>
" maxlength="8" value="<?php echo $_smarty_tpl->tpl_vars['section']->value['quantity'];?>
" />
                <img class="refresh" data-decorator="fnUpdateComponentQty" src="<?php echo $_smarty_tpl->tpl_vars['brandroot']->value;?>
/images/icons/refresh.png" data-trigger="click" data-lineid="<?php echo $_smarty_tpl->tpl_vars['section']->value['orderlineid'];?>
" data-itemqty="<?php echo $_smarty_tpl->tpl_vars['section']->value['itemqty'];?>
" alt="<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelOrderUpdateItemTotal');?>
" title="<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelOrderUpdateItemTotal');?>
" />
                            <?php } else { ?>
                <select id="itemqty_<?php echo $_smarty_tpl->tpl_vars['section']->value['orderlineid'];?>
" data-decorator="fnUpdateComponentQty" data-trigger="change" data-lineid="<?php echo $_smarty_tpl->tpl_vars['section']->value['orderlineid'];?>
" data-itemqty="<?php echo $_smarty_tpl->tpl_vars['section']->value['itemqty'];?>
">
                                <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['section']->value['itemqtydropdown'], 'qtyValue');
$_smarty_tpl->tpl_vars['qtyValue']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['qtyValue']->value) {
$_smarty_tpl->tpl_vars['qtyValue']->do_else = false;
?>
                    <option <?php if ($_smarty_tpl->tpl_vars['qtyValue']->value == $_smarty_tpl->tpl_vars['section']->value['quantity']) {?>selected="selected"<?php }?> value="<?php echo $_smarty_tpl->tpl_vars['qtyValue']->value;?>
"><?php echo $_smarty_tpl->tpl_vars['qtyValue']->value;?>
</option>
                                <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
                </select>
                            <?php }?>

                        <?php }?>
                <div class="clear"></div>
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
    </div>
                <?php }?>
                <!-- sub-sections of linefooter component start -->

                <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['section']->value['subsections'], 'subsection');
$_smarty_tpl->tpl_vars['subsection']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['subsection']->value) {
$_smarty_tpl->tpl_vars['subsection']->do_else = false;
?>                     <?php if ($_smarty_tpl->tpl_vars['subsection']->value['showcomponentname'] == true) {?>
    <div id="componentrow_<?php echo $_smarty_tpl->tpl_vars['subsection']->value['orderlineid'];?>
" class="subsection">
        <div class="subsectionBloc">
			<div class="section-title-header">
						<?php if (($_smarty_tpl->tpl_vars['section']->value['sectionlabel'] != '')) {?>
				<span class="section-category-name"><?php echo $_smarty_tpl->tpl_vars['subsection']->value['sectionlabel'];?>
:</span> <span class="section-category-prompt"><?php echo $_smarty_tpl->tpl_vars['subsection']->value['prompt'];?>
</span>
						<?php }?>
			</div>
						<?php if ($_smarty_tpl->tpl_vars['subsection']->value['haspreview'] > 0) {?>
                <img class="component-preview" src="<?php echo htmlspecialchars((string)$_smarty_tpl->tpl_vars['subsection']->value['componentpreviewsrc'], ENT_QUOTES, 'UTF-8', true);?>
" alt=""/>
                <div class="componentSubSectionTitle <?php if ($_smarty_tpl->tpl_vars['isCompanion']->value == true) {?>companion-componentSubSectionTitle<?php }?>">
                        <?php } else { ?>
                <div class="componentSubSectionTitleLong <?php if ($_smarty_tpl->tpl_vars['isCompanion']->value == true) {?>companion-componentSubSectionTitleLong<?php }?>">
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
" data-orderlineid="<?php echo $_smarty_tpl->tpl_vars['orderline']->value['orderlineid'];?>
" data-sectionlineid="<?php echo $_smarty_tpl->tpl_vars['subsection']->value['orderlineid'];?>
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
                    <span class="quantityText <?php if ($_smarty_tpl->tpl_vars['isCompanion']->value == true) {?>companion-quantityText<?php }?>"><?php echo $_smarty_tpl->tpl_vars['subsection']->value['quantity'];?>
</span>
                            <?php } else { ?>
                    <input id="hiddeqty_<?php echo $_smarty_tpl->tpl_vars['subsection']->value['orderlineid'];?>
" type="hidden" class="hiddeqtyCpt" value="<?php echo $_smarty_tpl->tpl_vars['subsection']->value['quantity'];?>
"/>
                                <?php if (empty($_smarty_tpl->tpl_vars['subsection']->value['itemqtydropdown'])) {?>
                    <input id="itemqty_<?php echo $_smarty_tpl->tpl_vars['subsection']->value['orderlineid'];?>
" type="text" class="quantity" data-decorator="fnUpdateComponentQty" data-trigger="keypress" data-lineid="<?php echo $_smarty_tpl->tpl_vars['subsection']->value['orderlineid'];?>
" data-itemqty="<?php echo $_smarty_tpl->tpl_vars['subsection']->value['itemqty'];?>
" maxlength="8" value="<?php echo $_smarty_tpl->tpl_vars['subsection']->value['quantity'];?>
" />
                    <img class="refresh" data-decorator="fnUpdateComponentQty" src="<?php echo $_smarty_tpl->tpl_vars['brandroot']->value;?>
/images/icons/refresh.png" data-trigger="click" data-lineid="<?php echo $_smarty_tpl->tpl_vars['subsection']->value['orderlineid'];?>
" data-itemqty="<?php echo $_smarty_tpl->tpl_vars['subsection']->value['itemqty'];?>
" alt="<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelOrderUpdateItemTotal');?>
" title="<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelOrderUpdateItemTotal');?>
" />
                                <?php } else { ?>
                    <select id="itemqty_<?php echo $_smarty_tpl->tpl_vars['subsection']->value['orderlineid'];?>
" data-decorator="fnUpdateComponentQty" data-trigger="change" data-lineid="<?php echo $_smarty_tpl->tpl_vars['subsection']->value['orderlineid'];?>
" data-itemqty="<?php echo $_smarty_tpl->tpl_vars['subsection']->value['itemqty'];?>
">
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
                    <div class="clear"></div>
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
                    <?php }?>
                <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>                 <!-- sub-sections of linefooter component end -->

                <!-- checkboxes inside linefooter component start -->
                <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['section']->value['checkboxes'], 'checkbox');
$_smarty_tpl->tpl_vars['checkbox']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['checkbox']->value) {
$_smarty_tpl->tpl_vars['checkbox']->do_else = false;
?>                     <?php if ($_smarty_tpl->tpl_vars['checkbox']->value['showcomponentname'] == true) {?>
                        <?php if ($_smarty_tpl->tpl_vars['stage']->value == 'qty' || ($_smarty_tpl->tpl_vars['stage']->value == 'payment' && $_smarty_tpl->tpl_vars['checkbox']->value['checked'] == 1)) {?>
    <div id="componentrow_<?php echo $_smarty_tpl->tpl_vars['checkbox']->value['orderlineid'];?>
" class="subsection">
        <div class="subcheckboxBloc">
			<div class="section-title-header">
			<?php if (($_smarty_tpl->tpl_vars['checkbox']->value['itemcomponentcategoryname'] != '')) {?>
				<span class="section-category-name"><?php echo $_smarty_tpl->tpl_vars['checkbox']->value['itemcomponentcategoryname'];?>
:</span> <span class="section-category-prompt"><?php echo $_smarty_tpl->tpl_vars['checkbox']->value['itemcomponentprompt'];?>
</span>
			<?php }?>
			</div>
                            <?php if ($_smarty_tpl->tpl_vars['checkbox']->value['haspreview'] > 0) {?>
            <img class="component-preview" src="<?php echo htmlspecialchars((string)$_smarty_tpl->tpl_vars['checkbox']->value['componentpreviewsrc'], ENT_QUOTES, 'UTF-8', true);?>
" alt=""/>
            <div class="componentSubSectionTitle <?php if ($_smarty_tpl->tpl_vars['isCompanion']->value == true) {?>companion-componentSubSectionTitle<?php }?>">
                            <?php } else { ?>
            <div class="componentSubSectionTitleLong <?php if ($_smarty_tpl->tpl_vars['isCompanion']->value == true) {?>companion-componentSubSectionTitleLong<?php }?>">
                            <?php }?>
                <div class="componentContentText">
                    <div class="section-title"><?php echo $_smarty_tpl->tpl_vars['checkbox']->value['itemcomponentname'];?>
</div>
                    <!-- START add-edit-change-remove links -->
                        <?php if (!empty($_smarty_tpl->tpl_vars['checkbox']->value['itemcomponentinfo'])) {?>
                    <div class="checkbox-info">
                        <?php echo $_smarty_tpl->tpl_vars['checkbox']->value['itemcomponentinfo'];?>

                    </div>
                            <?php $_smarty_tpl->_assignInScope('multilinedesc', "true");?>
                        <?php }?>
						<?php if (!empty($_smarty_tpl->tpl_vars['checkbox']->value['itemcomponentmoreinfolinkurl'])) {?>
                    <div class="checkbox-moreinfo">
                        <a href="<?php echo $_smarty_tpl->tpl_vars['checkbox']->value['itemcomponentmoreinfolinkurl'];?>
" target="_blank"><?php echo $_smarty_tpl->tpl_vars['checkbox']->value['itemcomponentmoreinfolinktext'];?>
</a>
                    </div>
							<?php $_smarty_tpl->_assignInScope('multilinedesc', "true");?>
                		<?php }?>
                        <?php if (!empty($_smarty_tpl->tpl_vars['checkbox']->value['itemcomponentpriceinfo'])) {?>
                    <div class="checkbox-info">
                        <?php echo $_smarty_tpl->tpl_vars['checkbox']->value['itemcomponentpriceinfo'];?>

                    </div>
                            <?php $_smarty_tpl->_assignInScope('multilinedesc', "true");?>
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
" data-orderlineid="<?php echo $_smarty_tpl->tpl_vars['orderline']->value['orderlineid'];?>
" data-sectionlineid="<?php echo $_smarty_tpl->tpl_vars['checkbox']->value['orderlineid'];?>
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
                <span class="quantityText <?php if ($_smarty_tpl->tpl_vars['isCompanion']->value == true) {?>companion-quantityText<?php }?>"><?php echo $_smarty_tpl->tpl_vars['checkbox']->value['quantity'];?>
</span>
                                <?php } else { ?>
                <input id="hiddeqty_<?php echo $_smarty_tpl->tpl_vars['checkbox']->value['orderlineid'];?>
" type="hidden" class="hiddeqtyCpt" value="<?php echo $_smarty_tpl->tpl_vars['checkbox']->value['quantity'];?>
"/>
                                    <?php if (empty($_smarty_tpl->tpl_vars['checkbox']->value['itemqtydropdown'])) {?>
                <input id="itemqty_<?php echo $_smarty_tpl->tpl_vars['checkbox']->value['orderlineid'];?>
" type="text" class="quantity" data-decorator="fnUpdateComponentQty" data-trigger="keypress" data-lineid="<?php echo $_smarty_tpl->tpl_vars['checkbox']->value['orderlineid'];?>
" data-itemqty="<?php echo $_smarty_tpl->tpl_vars['checkbox']->value['itemqty'];?>
" maxlength="8" value="<?php echo $_smarty_tpl->tpl_vars['checkbox']->value['quantity'];?>
" />
                <img class="refresh" data-decorator="fnUpdateComponentQty" src="<?php echo $_smarty_tpl->tpl_vars['brandroot']->value;?>
/images/icons/refresh.png" data-trigger="click" data-lineid="<?php echo $_smarty_tpl->tpl_vars['checkbox']->value['orderlineid'];?>
" data-itemqty="<?php echo $_smarty_tpl->tpl_vars['checkbox']->value['itemqty'];?>
" alt="<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelOrderUpdateItemTotal');?>
" title="<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelOrderUpdateItemTotal');?>
" />
                                    <?php } else { ?>
                <select id="itemqty_<?php echo $_smarty_tpl->tpl_vars['checkbox']->value['orderlineid'];?>
" data-decorator="fnUpdateComponentQty" data-trigger="change" data-lineid="<?php echo $_smarty_tpl->tpl_vars['checkbox']->value['orderlineid'];?>
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
                        <?php }?>
                    <?php }?>
                <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?> 					</div>
            <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>         <?php }?>
	<!-- linefooter sections end -->
    <?php }?>
	<!-- linefooter checkboxes start -->
        <?php if (sizeof($_smarty_tpl->tpl_vars['orderline']->value['linefootercheckboxes']) > 0) {?>

            <?php if (!(isset($_smarty_tpl->tpl_vars['bTitleComponent']->value))) {?>
 <div class="customisationsHeader <?php if ($_smarty_tpl->tpl_vars['isCompanion']->value == true) {?>companion-customisations<?php }?>">
	<?php if ($_smarty_tpl->tpl_vars['isCompanion']->value == true) {?>
		<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelCompanionAlbumOptions');?>

	<?php } else { ?>
        <?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelProductOptions');?>

	<?php }?>
		<span class="linkToggle">
    <?php if ($_smarty_tpl->tpl_vars['stage']->value == 'payment') {?>
			<span id="link_<?php echo $_smarty_tpl->tpl_vars['orderline']->value['orderlineid'];?>
" class="hide-product-options-link" data-decorator="fnToggleGeneric" data-lineid="<?php echo $_smarty_tpl->tpl_vars['orderline']->value['orderlineid'];?>
" data-idelm="contentCustomise_<?php echo $_smarty_tpl->tpl_vars['orderline']->value['orderlineid'];?>
" data-colour="grey"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_OrderShow');?>
</span>
    <?php } else { ?>
			<span id="link_<?php echo $_smarty_tpl->tpl_vars['orderline']->value['orderlineid'];?>
" class="hide-product-options-link" data-decorator="fnToggleGeneric" data-lineid="<?php echo $_smarty_tpl->tpl_vars['orderline']->value['orderlineid'];?>
" data-idelm="contentCustomise_<?php echo $_smarty_tpl->tpl_vars['orderline']->value['orderlineid'];?>
" data-colour="grey"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_OrderHide');?>
</span>
    <?php }?>
		</span>
        <div class="clear"></div>
    </div>

    <div id="contentCustomise_<?php echo $_smarty_tpl->tpl_vars['orderline']->value['orderlineid'];?>
" <?php if ($_smarty_tpl->tpl_vars['stage']->value == 'payment') {?>style="display:none;"<?php }?>>
				<?php $_smarty_tpl->_assignInScope('bTitleComponent', "true");?>
            <?php }?>




	<?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['orderline']->value['linefootercheckboxes'], 'checkbox');
$_smarty_tpl->tpl_vars['checkbox']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['checkbox']->value) {
$_smarty_tpl->tpl_vars['checkbox']->do_else = false;
?>
        <?php if ($_smarty_tpl->tpl_vars['checkbox']->value['showcomponentname'] == true) {?>
            <?php if ($_smarty_tpl->tpl_vars['stage']->value == 'qty' || ($_smarty_tpl->tpl_vars['stage']->value == 'payment' && $_smarty_tpl->tpl_vars['checkbox']->value['checked'] == 1)) {?>
		<div class="customisationOption <?php if ($_smarty_tpl->tpl_vars['isCompanion']->value == true) {?>companion-customisationOption<?php }?>">

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
" class="component-preview" alt=""/>
            <div class="component-name <?php if ($_smarty_tpl->tpl_vars['isCompanion']->value == true) {?>companion-component-name<?php }?>">
                <?php } else { ?>
            <div class="component-name-long <?php if ($_smarty_tpl->tpl_vars['isCompanion']->value == true) {?>companion-component-name-long<?php }?>">
                <?php }?>
                <div class="componentContentText">
                    <div class="section-title"><?php echo $_smarty_tpl->tpl_vars['checkbox']->value['itemcomponentname'];?>
</div>
            <!-- START add-edit-change-remove links -->
                <?php if (!empty($_smarty_tpl->tpl_vars['checkbox']->value['itemcomponentinfo'])) {?>
                    <div class="checkbox-info">
                        <?php echo $_smarty_tpl->tpl_vars['checkbox']->value['itemcomponentinfo'];?>

                    </div>
                <?php }?>
                <?php if (!empty($_smarty_tpl->tpl_vars['checkbox']->value['itemcomponentmoreinfolinkurl'])) {?>
                    <div class="checkbox-moreinfo">
                        <a href="<?php echo $_smarty_tpl->tpl_vars['checkbox']->value['itemcomponentmoreinfolinkurl'];?>
" target="_blank"><?php echo $_smarty_tpl->tpl_vars['checkbox']->value['itemcomponentmoreinfolinktext'];?>
</a>
                    </div>
                <?php }?>
                <?php if (!empty($_smarty_tpl->tpl_vars['checkbox']->value['itemcomponentpriceinfo'])) {?>
                    <div class="checkbox-info">
                        <?php echo $_smarty_tpl->tpl_vars['checkbox']->value['itemcomponentpriceinfo'];?>

                    </div>
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
" data-orderlineid="<?php echo $_smarty_tpl->tpl_vars['orderline']->value['orderlineid'];?>
" data-sectionlineid="<?php echo $_smarty_tpl->tpl_vars['checkbox']->value['orderlineid'];?>
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
            </div>
        <!--    <div class="clear"></div> -->
                <!-- END add-edit-change-remove links -->
                <?php if (($_smarty_tpl->tpl_vars['checkbox']->value['pricingmodel'] == 7 || $_smarty_tpl->tpl_vars['checkbox']->value['pricingmodel'] == 8) && $_smarty_tpl->tpl_vars['checkbox']->value['checked'] == 1) {?>
            <div class="quantity <?php if ($_smarty_tpl->tpl_vars['multilinedesc']->value == "true") {?>paddingCenter<?php }?>">
                    <?php if ($_smarty_tpl->tpl_vars['stage']->value == 'payment') {?>
                <span class="quantityText <?php if ($_smarty_tpl->tpl_vars['isCompanion']->value == true) {?>companion-quantityText<?php }?>"><?php echo $_smarty_tpl->tpl_vars['checkbox']->value['quantity'];?>
</span>
                    <?php } else { ?>
                <input id="hiddeqty_<?php echo $_smarty_tpl->tpl_vars['checkbox']->value['orderlineid'];?>
" type="hidden" class="hiddeqtyCpt" value="<?php echo $_smarty_tpl->tpl_vars['checkbox']->value['quantity'];?>
"/>
                        <?php if (empty($_smarty_tpl->tpl_vars['checkbox']->value['itemqtydropdown'])) {?>
                <input id="itemqty_<?php echo $_smarty_tpl->tpl_vars['checkbox']->value['orderlineid'];?>
" type="text" class="quantity" data-decorator="fnUpdateComponentQty" data-trigger="keypress" data-lineid="<?php echo $_smarty_tpl->tpl_vars['checkbox']->value['orderlineid'];?>
" data-itemqty="<?php echo $_smarty_tpl->tpl_vars['checkbox']->value['itemqty'];?>
" maxlength="8" value="<?php echo $_smarty_tpl->tpl_vars['checkbox']->value['quantity'];?>
" />
                <img class="refresh" data-decorator="fnUpdateComponentQty" src="<?php echo $_smarty_tpl->tpl_vars['brandroot']->value;?>
/images/icons/refresh.png" data-trigger="click" data-lineid="<?php echo $_smarty_tpl->tpl_vars['checkbox']->value['orderlineid'];?>
" data-itemqty="<?php echo $_smarty_tpl->tpl_vars['checkbox']->value['itemqty'];?>
" alt="<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelOrderUpdateItemTotal');?>
" title="<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelOrderUpdateItemTotal');?>
" />
                        <?php } else { ?>
                <select id="itemqty_<?php echo $_smarty_tpl->tpl_vars['checkbox']->value['orderlineid'];?>
" data-decorator="fnUpdateComponentQty" data-trigger="change" data-lineid="<?php echo $_smarty_tpl->tpl_vars['checkbox']->value['orderlineid'];?>
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
" class="component-metadata<?php if (!$_smarty_tpl->tpl_vars['checkbox']->value['checked']) {?> invisible<?php }?>">
            <?php echo $_smarty_tpl->tpl_vars['checkbox']->value['metadatahtml'];?>

        </div>
                <?php }?>
    </div>
	</div>
            <?php }?>
        <?php }?>
    <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
	<?php }?>
    <?php if ((isset($_smarty_tpl->tpl_vars['bTitleComponent']->value))) {?>
    </div>
    <?php }?>
    <!-- linefooter checkboxes end -->
    <?php if ($_smarty_tpl->tpl_vars['orderline']->value['orderlineid'] != -1) {?>
	<div class="line-total">
        <!-- QTY  -->
        <?php if (($_smarty_tpl->tpl_vars['stage']->value == 'qty')) {?>
            <div class="line-sub-total">
				<?php if ($_smarty_tpl->tpl_vars['isCompanion']->value == true) {?>
                <span class="total-heading companion-total-heading"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelOrderCompanionItemListItemTotal');?>
:</span>
				<?php } else { ?>
                <span class="total-heading"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelOrderItemListItemTotal');?>
:</span>
				<?php }?>
                <span class="discount-price"><?php echo $_smarty_tpl->tpl_vars['orderline']->value['itemcompletetotal'];?>
</span>
                <div class="clear"></div>
            </div>
        <?php }?>

        <!-- PAYMENT  -->
        <?php if (($_smarty_tpl->tpl_vars['stage']->value == 'payment')) {?>
            <!-- NO VOUCHER  -->
            <?php if ((!$_smarty_tpl->tpl_vars['orderline']->value['itemvoucherapplied']) || ($_smarty_tpl->tpl_vars['vouchersection']->value == 'SHIPPING') || (($_smarty_tpl->tpl_vars['vouchersection']->value == 'TOTAL') && !(($_smarty_tpl->tpl_vars['differenttaxrates']->value) && (!$_smarty_tpl->tpl_vars['specialvouchertype']->value)))) {?>

                <!-- DIFFERNETTAXRATES AND SHOWPRICES WITH TAX  -->
                <?php if ((($_smarty_tpl->tpl_vars['differenttaxrates']->value) && ($_smarty_tpl->tpl_vars['showpriceswithtax']->value))) {?>

                    <?php if ((($_smarty_tpl->tpl_vars['vouchersection']->value == 'TOTAL') && ($_smarty_tpl->tpl_vars['specialvouchertype']->value))) {?>
                        <?php if (($_smarty_tpl->tpl_vars['orderline']->value['itemdiscountvalueraw'] > 0)) {?>
                            <div class="line-sub-total">
                                <span class="discount-heading"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelSubTotal');?>
:</span>
                                <span class="discount-price"><?php echo $_smarty_tpl->tpl_vars['orderline']->value['itemcompletetotal'];?>
</span>
                            </div>
                            <div class="line-sub-total-nopadding">
                                <span class="discount-heading"><?php echo $_smarty_tpl->tpl_vars['orderline']->value['itemdiscountname'];?>
:</span>
                                <span class="discount-price"><?php echo $_smarty_tpl->tpl_vars['orderline']->value['itemdiscountvalue'];?>
</span>
                            </div>
                            <div class="line-sub-total-small-top">
                                <span class="discount-heading"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelOrderItemListItemTotal');?>
:</span>
                                <span class="discount-price"><?php echo $_smarty_tpl->tpl_vars['orderline']->value['itemdiscountedvalue'];?>
</span>
                            </div>
                        <?php } else { ?>
                            <div class="line-sub-total">
                                <span class="discount-heading"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelSubTotal');?>
:</span>
                                <span class="discount-price"><?php echo $_smarty_tpl->tpl_vars['orderline']->value['itemcompletetotal'];?>
</span>
                            </div>
                        <?php }?>
                        <!-- SHOWZEROTAX OR DONT SHOWZEROTAX AND THE TAX > 0  -->
                        <?php if ((($_smarty_tpl->tpl_vars['showzerotax']->value) || ((!$_smarty_tpl->tpl_vars['showzerotax']->value) && ($_smarty_tpl->tpl_vars['orderline']->value['itemtaxtotalraw'] > 0)))) {?>
                            <div class="line-sub-total-small-bottom"><?php echo $_smarty_tpl->tpl_vars['orderline']->value['includesitemtaxtext'];?>
 </div>
                        <?php }?>
                    <?php } else { ?>
                        <div class="line-sub-total">
                            <span class="discount-heading"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelOrderItemListItemTotal');?>
:</span>
                            <span class="discount-price"><?php echo $_smarty_tpl->tpl_vars['orderline']->value['itemcompletetotal'];?>
</span>
                        </div>

                        <!-- SHOWTAXBREAKDOWN  -->
                        <?php if (($_smarty_tpl->tpl_vars['showtaxbreakdown']->value)) {?>
        					<?php if ((($_smarty_tpl->tpl_vars['showzerotax']->value) || ((!$_smarty_tpl->tpl_vars['showzerotax']->value) && ($_smarty_tpl->tpl_vars['orderline']->value['itemtaxtotalraw'] > 0)))) {?>
        						<div class="line-sub-total-small-bottom"><?php echo $_smarty_tpl->tpl_vars['orderline']->value['includesitemtaxtext'];?>
 </div>
        					<?php }?>
                        <?php }?>
                    <?php }?>

                <?php }?>

                <!-- DIFFERNETTAXRATES AND DONT SHOWPRICESWITHTAX  -->
                <?php if ((($_smarty_tpl->tpl_vars['differenttaxrates']->value) && (!$_smarty_tpl->tpl_vars['showpriceswithtax']->value))) {?>

                    <!-- VALUE SET TOTAL VOUCHER  -->
                    <?php if ((($_smarty_tpl->tpl_vars['vouchersection']->value == 'TOTAL') && ($_smarty_tpl->tpl_vars['specialvouchertype']->value))) {?>
                        <?php if (($_smarty_tpl->tpl_vars['orderline']->value['itemdiscountvalueraw'] > 0)) {?>
                            <div class="line-sub-total">
                                <span class="discount-heading"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelSubTotal');?>
:</span>
                                <span class="discount-price"><?php echo $_smarty_tpl->tpl_vars['orderline']->value['itemcompletetotal'];?>
</span>
                            </div>
                            <div class="line-sub-total-nopadding">
                                <span class="discount-heading"><?php echo $_smarty_tpl->tpl_vars['orderline']->value['itemdiscountname'];?>
:</span>
                                <span class="discount-price"><?php echo $_smarty_tpl->tpl_vars['orderline']->value['itemdiscountvalue'];?>
</span>
                            </div>
                        <?php }?>
                        <!-- SHOWZEROTAX OR DONT SHOWZEROTAX AND THE TAX > 0  -->
                        <?php if ((($_smarty_tpl->tpl_vars['showzerotax']->value) || ((!$_smarty_tpl->tpl_vars['showzerotax']->value) && ($_smarty_tpl->tpl_vars['orderline']->value['itemtaxtotalraw'] > 0)))) {?>
                            <div class="line-sub-total">
                                <span class="discount-heading"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelSubTotal');?>
:</span>
                                <span class="discount-price"><?php echo $_smarty_tpl->tpl_vars['orderline']->value['itemdiscountedvalue'];?>
</span>
                            </div>
                            <div class="line-sub-total-nopadding">
                                <span class="discount-heading"><?php echo $_smarty_tpl->tpl_vars['orderline']->value['itemtaxratename'];?>
 (<?php echo $_smarty_tpl->tpl_vars['orderline']->value['itemtaxrate'];?>
%):</span>
                                <span class="discount-price"><?php echo $_smarty_tpl->tpl_vars['orderline']->value['itemtaxtotal'];?>
</span>
                            </div>
                        <?php }?>
                    <?php } else { ?>
                        <!-- SHOWZEROTAX OR DONT SHOWZEROTAX AND THE TAX > 0  -->
                        <?php if ((($_smarty_tpl->tpl_vars['showzerotax']->value) || ((!$_smarty_tpl->tpl_vars['showzerotax']->value) && ($_smarty_tpl->tpl_vars['orderline']->value['itemtaxtotalraw'] > 0)))) {?>
                            <div class="line-sub-total">
                                <span class="discount-heading"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelSubTotal');?>
:</span>
                                <span class="discount-price"><?php echo $_smarty_tpl->tpl_vars['orderline']->value['itemcompletetotal'];?>
</span>
                            </div>
                            <div class="line-sub-total-nopadding">
                                <span class="discount-heading"><?php echo $_smarty_tpl->tpl_vars['orderline']->value['itemtaxratename'];?>
 (<?php echo $_smarty_tpl->tpl_vars['orderline']->value['itemtaxrate'];?>
%):</span>
                                <span class="discount-price"><?php echo $_smarty_tpl->tpl_vars['orderline']->value['itemtaxtotal'];?>
</span>
                            </div>
                        <?php }?>
                    <?php }?>

					<div class="line-sub-total">
						<span class="discount-heading"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelOrderItemListItemTotal');?>
:</span>
						<span class="discount-price"><?php echo $_smarty_tpl->tpl_vars['orderline']->value['itemtotal'];?>
</span>
					</div>

                <?php }?>

                <!-- NOT DIFFERNETTAXRATES  -->
                <?php if ((!$_smarty_tpl->tpl_vars['differenttaxrates']->value)) {?>
                    <?php if (($_smarty_tpl->tpl_vars['orderline']->value['itemdiscountvalueraw'] > 0)) {?>
                    <div class="line-sub-total">
                        <span class="discount-heading"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelSubTotal');?>
:</span>
                        <span class="discount-price"><?php echo $_smarty_tpl->tpl_vars['orderline']->value['itemcompletetotal'];?>
</span>
                    </div>
                    <div class="line-sub-total-nopadding">
                        <span class="discount-heading"><?php echo $_smarty_tpl->tpl_vars['orderline']->value['itemdiscountname'];?>
:</span>
                        <span class="discount-price"><?php echo $_smarty_tpl->tpl_vars['orderline']->value['itemdiscountvalue'];?>
</span>
                    </div>
                    <?php }?>
                    <div class="line-sub-total">
                        <span class="discount-heading"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelOrderItemListItemTotal');?>
:</span>
                        <span class="discount-price"><?php echo $_smarty_tpl->tpl_vars['orderline']->value['itemtotal'];?>
</span>
                    </div>
                <?php }?>

            <?php } else { ?>

                <!-- PRODUCT VOUCHER  -->
                <?php if (($_smarty_tpl->tpl_vars['vouchersection']->value == 'PRODUCT')) {?>
                    <?php if (($_smarty_tpl->tpl_vars['orderline']->value['itemdiscountvalueraw'] > 0)) {?>
                    <div class="line-sub-total">
                        <span class="discount-heading"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelSubTotal');?>
:</span>
                        <span class="discount-price"><?php echo $_smarty_tpl->tpl_vars['orderline']->value['itemcompletetotal'];?>
</span>
                    </div>
                    <div class="line-sub-total-nopadding">
                        <span class="discount-heading"><?php echo $_smarty_tpl->tpl_vars['orderline']->value['itemdiscountname'];?>
:</span>
                        <span class="discount-price"><?php echo $_smarty_tpl->tpl_vars['orderline']->value['itemdiscountvalue'];?>
</span>
                    </div>
                    <?php }?>

                    <!-- DIFFERNETTAXRATES AND SHOWPRICES WITH TAX   -->
                    <?php if ((($_smarty_tpl->tpl_vars['differenttaxrates']->value) && ($_smarty_tpl->tpl_vars['showpriceswithtax']->value))) {?>

                        <div class="line-sub-total">
                            <span class="discount-heading"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelOrderItemListItemTotal');?>
:</span>
                            <span class="discount-price"><?php echo $_smarty_tpl->tpl_vars['orderline']->value['itemdiscountedvalue'];?>
</span>
                        </div>

                        <!-- SHOWTAXBREAKDOWN  -->
                        <?php if (($_smarty_tpl->tpl_vars['showtaxbreakdown']->value)) {?>
	                        <?php if ((($_smarty_tpl->tpl_vars['showzerotax']->value) || ((!$_smarty_tpl->tpl_vars['showzerotax']->value) && ($_smarty_tpl->tpl_vars['orderline']->value['itemtaxtotalraw'] > 0)))) {?>
	                            <div class="line-sub-total-small-bottom"><?php echo $_smarty_tpl->tpl_vars['orderline']->value['includesitemtaxtext'];?>
</div>
                            <?php }?>
                        <?php }?>
                    <?php }?>

                    <!-- DIFFERNETTAXRATES AND DONT SHOWPRICESWITHTAX   -->
                    <?php if ((($_smarty_tpl->tpl_vars['differenttaxrates']->value) && (!$_smarty_tpl->tpl_vars['showpriceswithtax']->value))) {?>

                        <!-- SHOWZEROTAX OR DONT SHOWZEROTAX AND THE TAX > 0  -->
                        <?php if ((($_smarty_tpl->tpl_vars['showzerotax']->value) || ((!$_smarty_tpl->tpl_vars['showzerotax']->value) && ($_smarty_tpl->tpl_vars['orderline']->value['itemtaxtotalraw'] > 0)))) {?>
                            <div class="line-sub-total">
                                <span class="discount-heading"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelSubTotal');?>
:</span>
                                <span class="discount-price"><?php echo $_smarty_tpl->tpl_vars['orderline']->value['itemdiscountedvalue'];?>
</span>
                            </div>
                            <div class="line-sub-total-nopadding">
                                <span class="discount-heading"><?php echo $_smarty_tpl->tpl_vars['orderline']->value['itemtaxratename'];?>
 (<?php echo $_smarty_tpl->tpl_vars['orderline']->value['itemtaxrate'];?>
%):</span>
                                <span class="discount-price"><?php echo $_smarty_tpl->tpl_vars['orderline']->value['itemtaxtotal'];?>
</span>
                            </div>
                        <?php }?>
						<div class="line-sub-total">
							<span class="discount-heading"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelOrderItemListItemTotal');?>
:</span>
							<span class="discount-price"><?php echo $_smarty_tpl->tpl_vars['orderline']->value['itemtotal'];?>
</span>
						</div>

                    <?php }?>

                    <!-- NOT DIFFERNETTAXRATES  -->
                    <?php if ((!$_smarty_tpl->tpl_vars['differenttaxrates']->value)) {?>

                        <div class="line-sub-total">
                            <span class="discount-heading"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelOrderItemListItemTotal');?>
:</span>
                            <span class="discount-price"><?php echo $_smarty_tpl->tpl_vars['orderline']->value['itemdiscountedvalue'];?>
</span>
                        </div>

                    <?php }?>

                <?php }?>

                <!-- VALUE OFF TOTAL VOUCHER  -->
                <?php if ((($_smarty_tpl->tpl_vars['vouchersection']->value == 'TOTAL') && (($_smarty_tpl->tpl_vars['differenttaxrates']->value) && (!$_smarty_tpl->tpl_vars['specialvouchertype']->value)))) {?>
                    <?php if (($_smarty_tpl->tpl_vars['orderline']->value['itemdiscountvalueraw'] > 0)) {?>
                    <div class="line-sub-total">
                        <span class="discount-heading"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelSubTotal');?>
:</span>
                        <span class="discount-price"><?php echo $_smarty_tpl->tpl_vars['orderline']->value['itemcompletetotal'];?>
</span>
                    </div>
                    <div class="line-sub-total-nopadding">
                        <span class="discount-heading"><?php echo $_smarty_tpl->tpl_vars['orderline']->value['itemdiscountname'];?>
:</span>
                        <span class="discount-price"><?php echo $_smarty_tpl->tpl_vars['orderline']->value['itemdiscountvalue'];?>
</span>
                    </div>
                    <?php }?>
                    <?php if ((!$_smarty_tpl->tpl_vars['showpriceswithtax']->value)) {?>
                        <!-- SHOWZEROTAX OR DONT SHOWZEROTAX AND THE TAX > 0  -->
                        <?php if ((($_smarty_tpl->tpl_vars['showzerotax']->value) || ((!$_smarty_tpl->tpl_vars['showzerotax']->value) && ($_smarty_tpl->tpl_vars['orderline']->value['itemtaxtotalraw'] > 0)))) {?>
                            <div class="line-sub-total">
                                <span class="discount-heading"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelSubTotal');?>
:</span>
                                <span class="discount-price"><?php echo $_smarty_tpl->tpl_vars['orderline']->value['itemdiscountedvalue'];?>
</span>
                            </div>
                            <div class="line-sub-total-nopadding">
                                <span class="discount-heading"><?php echo $_smarty_tpl->tpl_vars['orderline']->value['itemtaxratename'];?>
 (<?php echo $_smarty_tpl->tpl_vars['orderline']->value['itemtaxrate'];?>
%):</span>
                                <span class="discount-price"><?php echo $_smarty_tpl->tpl_vars['orderline']->value['itemtaxtotal'];?>
</span>
                            </div>
                            <div class="line-sub-total">
                                <span class="discount-heading"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelOrderItemListItemTotal');?>
:</span>
                                <span class="discount-price"><?php echo $_smarty_tpl->tpl_vars['orderline']->value['itemtotal'];?>
</span>
                            </div>
                        <?php } else { ?>
                            <div class="line-sub-total">
                                <span class="discount-heading"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelOrderItemListItemTotal');?>
:</span>
                                <span class="discount-price"><?php echo $_smarty_tpl->tpl_vars['orderline']->value['itemdiscountedvalue'];?>
</span>
                            </div>
                        <?php }?>
                    <?php } else { ?>
                        <div class="line-sub-total">
                            <span class="discount-heading"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelOrderItemListItemTotal');?>
:</span>
                            <span class="discount-price"><?php echo $_smarty_tpl->tpl_vars['orderline']->value['itemtotal'];?>
</span>
                        </div>

                        <!-- SHOWZEROTAX OR DONT SHOWZEROTAX AND THE TAX > 0  -->

                        <?php if ((($_smarty_tpl->tpl_vars['showzerotax']->value) || ((!$_smarty_tpl->tpl_vars['showzerotax']->value) && ($_smarty_tpl->tpl_vars['orderline']->value['itemtaxtotalraw'] > 0)))) {?>
                            <div class="line-sub-total-small-bottom"><?php echo $_smarty_tpl->tpl_vars['orderline']->value['includesitemtaxtext'];?>
</div>
                        <?php }?>
                    <?php }?>
                <?php }?>

            <?php }?>

        <?php }?>
	</div>
	<?php }
if ($_smarty_tpl->tpl_vars['call_action']->value == 'init') {?>
        </div>
    </div>
</div>
<?php }
}
}
