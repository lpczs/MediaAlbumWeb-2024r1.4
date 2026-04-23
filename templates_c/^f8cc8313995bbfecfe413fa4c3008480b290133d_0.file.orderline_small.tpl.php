<?php
/* Smarty version 4.5.3, created on 2026-03-07 07:52:37
  from 'C:\TAOPIX\MediaAlbumWeb\templates\order\orderline_small.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.5.3',
  'unifunc' => 'content_69abd945a29943_92673738',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'f8cc8313995bbfecfe413fa4c3008480b290133d' => 
    array (
      0 => 'C:\\TAOPIX\\MediaAlbumWeb\\templates\\order\\orderline_small.tpl',
      1 => 1729602708,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_69abd945a29943_92673738 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'C:\\TAOPIX\\MediaAlbumWeb\\libs\\external\\vendor\\smarty\\smarty\\libs\\plugins\\modifier.count.php','function'=>'smarty_modifier_count',),1=>array('file'=>'C:\\TAOPIX\\MediaAlbumWeb\\libs\\external\\vendor\\smarty\\smarty\\libs\\plugins\\modifier.replace.php','function'=>'smarty_modifier_replace',),));
if ($_smarty_tpl->tpl_vars['orderline']->value['orderlineid'] != -1) {?>

    <?php if ($_smarty_tpl->tpl_vars['call_action']->value == 'init') {?> 
<div class="outerBox outerBoxWithTab">

    <div class="itemTitle">
        <?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelHeaderItem');?>
 <?php echo $_smarty_tpl->tpl_vars['orderline']->value['orderlineid'];?>

    </div>

    <div class="clear"></div>

    <div>

        <?php if ($_smarty_tpl->tpl_vars['stage']->value == 'qty') {?>

        <div class="orderLine" id="ordertableobj_<?php echo $_smarty_tpl->tpl_vars['orderline']->value['orderlineid'];?>
">

        <?php } else { ?>

        <div class="orderLine">

        <?php }?>

    <?php }?> 
<!-- START ITEM SECTION -->

    <div class="contentTextInside">

        <div class="itemDetail">

            <div class="outerBoxPadding">

                <div class="productTitle">
                    <div id="productName<?php echo $_smarty_tpl->tpl_vars['stage']->value;?>
" class="productName">
                        <?php echo $_smarty_tpl->tpl_vars['orderline']->value['projectname'];?>

                    </div> <!-- componentLabel -->

                    <div id="productPrice<?php echo $_smarty_tpl->tpl_vars['stage']->value;?>
" class="productPrice">
                        <?php if ($_smarty_tpl->tpl_vars['orderline']->value['itemshowproductsell'] == 1) {?>
                			<?php echo $_smarty_tpl->tpl_vars['orderline']->value['itemproducttotalsell'];?>

                		<?php }?>
                    </div>

                    <div class="clear"></div>
                </div> <!-- productTitle -->

                <div class="productCollectionTitle">
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

    <?php if ($_smarty_tpl->tpl_vars['orderline']->value['itemproductinfo'] != '') {?>

                <div class="product-info"><?php echo $_smarty_tpl->tpl_vars['orderline']->value['itemproductinfo'];?>
</div>

    <?php }?> 
            <!-- EXTERNAL ASSETS -->

    <?php if (($_smarty_tpl->tpl_vars['orderline']->value['displayassets'] == true) && (sizeof($_smarty_tpl->tpl_vars['orderline']->value['itemexternalassets']) > 0)) {?>

            <div class="innerBox">

                <div class="sectionLabel innerBoxPadding">

                    <div class="componentLabel">
                        <?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_TitleExternalAssets');?>

                    </div> <!-- componentLabel -->

                    <div class="componentPrice">
                        <?php echo $_smarty_tpl->tpl_vars['orderline']->value['totalprice'];?>

                    </div> <!-- componentPrice -->

                    <div class="clear"></div>

                </div> <!-- sectionLabel innerBoxPadding -->

                <div class="componentBloc innerBoxPadding">

		<?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['orderline']->value['itemexternalassets'], 'asset');
$_smarty_tpl->tpl_vars['asset']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['asset']->value) {
$_smarty_tpl->tpl_vars['asset']->do_else = false;
?>

                    <div class="componentLabel">
                        <?php echo $_smarty_tpl->tpl_vars['asset']->value['pagename'];?>
:<?php echo $_smarty_tpl->tpl_vars['asset']->value['name'];?>

                    </div> <!-- componentLabel -->

                    <div class="componentPrice">
                        <?php echo $_smarty_tpl->tpl_vars['asset']->value['charge'];?>

                    </div> <!-- componentPrice -->

                    <div class="clear"></div>

		<?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?> 
                </div> <!-- componentBloc -->

                <div class="clear"></div>

            </div> <!-- innerBox -->

	<?php }?> 
            <!-- END EXTERNAL ASSETS -->

            <!-- Calendar Customisations -->
    <?php if (smarty_modifier_count($_smarty_tpl->tpl_vars['orderline']->value['calendarcustomisations']) > 0) {?>

                <div class="sectionLabel">

    <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['orderline']->value['calendarcustomisations'], 'calendarcustomisations');
$_smarty_tpl->tpl_vars['calendarcustomisations']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['calendarcustomisations']->value) {
$_smarty_tpl->tpl_vars['calendarcustomisations']->do_else = false;
?>

                    <div class="calComponentLabel"><?php echo $_smarty_tpl->tpl_vars['calendarcustomisations']->value['name'];?>
</div>
                    <div class="componentPrice calComponentPrice"><?php echo $_smarty_tpl->tpl_vars['calendarcustomisations']->value['formattedtotalsell'];?>
</div>

                    <div class="clear"></div>
    <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
                </div> <!-- componentBloc -->

                <div class="clear"></div>

    <?php }?>
			<!--  AI COMPONENT -->
	<?php if (sizeof($_smarty_tpl->tpl_vars['orderline']->value['aicomponent']) > 0) {?>

        <div class="componentContainer smart-design-component">
            <div class="innerBox">
                <div class="sectionLabel innerBoxPadding">
                    <div class="componentLabel">
                        <?php echo $_smarty_tpl->tpl_vars['orderline']->value['aicomponent']['name'];?>

                    </div> 
                    <div class="componentPrice">
                        <?php echo $_smarty_tpl->tpl_vars['orderline']->value['aicomponent']['formattedtotalsell'];?>

                    </div> 
                    <div class="clear"></div>
                </div>
                <div class="componentBloc innerBoxPadding">
                    <img class="componentPreview" src="<?php echo htmlspecialchars((string)$_smarty_tpl->tpl_vars['orderline']->value['aicomponent']['previewsrc'], ENT_QUOTES, 'UTF-8', true);?>
"/>
                    <div class="componentContentText">
                        <div class="componentDescription"><?php echo $_smarty_tpl->tpl_vars['orderline']->value['aicomponent']['componentinfo'];?>
</div>
                    </div> <!-- componentContentText  or componentContentTextLong-->
                    <div class="clear"></div>
                </div>
            </div>
        </div>

		<div class="clear"></div>
			<!-- END AI COMPONENT -->
	<?php }?>
            <!-- SINGLE PRINTSs -->

    <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['orderline']->value['itempictures'], 'sizegroup', false, NULL, 'singleprints', array (
));
$_smarty_tpl->tpl_vars['sizegroup']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['sizegroup']->value) {
$_smarty_tpl->tpl_vars['sizegroup']->do_else = false;
?>

                <div class="innerBox">

                    <div class="sectionLabel innerBoxPadding">

                        <div class="componentLabel">
                            <?php echo $_smarty_tpl->tpl_vars['sizegroup']->value['groupdisplayname'];?>

                        </div> <!-- componentLabel -->
                        <div class="componentPrice">
                            <?php echo $_smarty_tpl->tpl_vars['sizegroup']->value['formatedgrouptotalsell'];?>

                        </div>
                        <div class="clear"></div>

                    </div> <!-- sectionLabel innerBoxPadding -->

                    <div class="componentBloc innerBoxPadding">

						<div class="singlePrintBlocDetailsCount">
							<?php if ($_smarty_tpl->tpl_vars['sizegroup']->value['picturecount'] > 1) {?>
								<?php echo smarty_modifier_replace($_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelPrints'),'^0',$_smarty_tpl->tpl_vars['sizegroup']->value['picturecount']);?>

							<?php } else { ?>
								<?php echo smarty_modifier_replace($_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelPrint'),'^0',$_smarty_tpl->tpl_vars['sizegroup']->value['picturecount']);?>

							<?php }?>
						</div>
						<div class="clear"></div>

                   </div> <!-- componentBloc innerBoxPadding -->

                <div class="clear"></div>

            </div> <!-- innerBox -->


    <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?> 
            <!-- END SINGLE PRINTSs -->

            <!-- CHECKBOXES START -->

    <?php if (sizeof($_smarty_tpl->tpl_vars['orderline']->value['checkboxes']) > 0) {?>

        <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['orderline']->value['checkboxes'], 'checkbox');
$_smarty_tpl->tpl_vars['checkbox']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['checkbox']->value) {
$_smarty_tpl->tpl_vars['checkbox']->do_else = false;
?>

            <?php if ($_smarty_tpl->tpl_vars['checkbox']->value['showcomponentname'] == true) {?>

                <?php if ((($_smarty_tpl->tpl_vars['stage']->value == 'payment' && $_smarty_tpl->tpl_vars['checkbox']->value['checked'] == 1) || $_smarty_tpl->tpl_vars['stage']->value == 'qty')) {?>

                    <?php if (!(isset($_smarty_tpl->tpl_vars['bTitleComponent']->value)) && ($_smarty_tpl->tpl_vars['stage']->value == 'payment')) {?>

                        <?php $_smarty_tpl->_assignInScope('bTitleComponent', "true");?>

            <div class="showHideComponents" data-decorator="fnShowHideComponents" data-orderlineid="<?php echo $_smarty_tpl->tpl_vars['orderline']->value['orderlineid'];?>
" data-isfooter="false">
                <span id="linkToggle_<?php echo $_smarty_tpl->tpl_vars['orderline']->value['orderlineid'];?>
" class="showHideTitle hidden"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelShowOptions');?>
</span>
            </div>

            <div id="contentCustomise_<?php echo $_smarty_tpl->tpl_vars['orderline']->value['orderlineid'];?>
" <?php if ($_smarty_tpl->tpl_vars['stage']->value == 'payment') {?>style="display:none;"<?php }?>>

                    <?php }?>

                    <?php if ($_smarty_tpl->tpl_vars['stage']->value == 'qty') {?>

            <div id="componentContent_<?php echo $_smarty_tpl->tpl_vars['checkbox']->value['orderlineid'];?>
" class="innerBox">

                    <?php } else { ?>

            <div class="innerBox">

                    <?php }?>

                <div class="sectionLabel innerBoxPadding">

                    <div class="componentLabel">

                        <?php echo $_smarty_tpl->tpl_vars['checkbox']->value['itemcomponentcategoryname'];?>


                    </div> <!-- componentLabel -->

                        <?php if (($_smarty_tpl->tpl_vars['checkbox']->value['checked']) || ($_smarty_tpl->tpl_vars['checkbox']->value['totalsell'] == $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelNotAvailable'))) {?>

                    <div class="componentPrice">
                        <?php echo $_smarty_tpl->tpl_vars['checkbox']->value['totalsell'];?>

                    </div>

                        <?php }?> 
                    <div class="clear"></div>

                </diV> <!-- sectionLabel innerBoxPadding -->

                <div class="componentBloc innerBoxPadding">

                        <?php if (($_smarty_tpl->tpl_vars['stage']->value == 'qty')) {?>

                    <div class="checkboxBloc">

                        <?php } else { ?>

                    <div>

                        <?php }?>

                        <?php if (($_smarty_tpl->tpl_vars['checkbox']->value['haspreview'] > 0) && ($_smarty_tpl->tpl_vars['stage']->value == 'qty')) {?>

                        <img class="componentPreview" src="<?php echo htmlspecialchars((string)$_smarty_tpl->tpl_vars['checkbox']->value['componentpreviewsrc'], ENT_QUOTES, 'UTF-8', true);?>
" alt=""/>
                        <div class="componentContentText">

                        <?php } else { ?> 
                        <div class="componentContentTextLong">

                        <?php }?> 
                            <div class="componentTitle">
                                <?php echo $_smarty_tpl->tpl_vars['checkbox']->value['itemcomponentname'];?>

                            </div>

                        <?php if (!empty($_smarty_tpl->tpl_vars['checkbox']->value['itemcomponentinfo']) && ($_smarty_tpl->tpl_vars['stage']->value == 'qty')) {?>

                            <div class="componentDescription">
                                <?php echo $_smarty_tpl->tpl_vars['checkbox']->value['itemcomponentinfo'];?>

                            </div>

                        <?php }?> 
                        <?php if (!empty($_smarty_tpl->tpl_vars['checkbox']->value['itemcomponentmoreinfolinkurl'])) {?>

                          <div class="componentDescription">
                            <a href="<?php echo $_smarty_tpl->tpl_vars['checkbox']->value['itemcomponentmoreinfolinkurl'];?>
" target="_blank"><?php echo $_smarty_tpl->tpl_vars['checkbox']->value['itemcomponentmoreinfolinktext'];?>
</a>
                          </div>

                        <?php }?> 
                        <?php if (!empty($_smarty_tpl->tpl_vars['checkbox']->value['itemcomponentpriceinfo']) && ($_smarty_tpl->tpl_vars['stage']->value == 'qty')) {?>

                            <div class="componentDescription">
                                <?php echo $_smarty_tpl->tpl_vars['checkbox']->value['itemcomponentpriceinfo'];?>

                            </div>

                        <?php }?> 
                        <?php if (($_smarty_tpl->tpl_vars['stage']->value == 'qty') && (!$_smarty_tpl->tpl_vars['checkbox']->value['checked'])) {?>

                            <div class="componentCheckBoxPrice">
                                (<?php echo $_smarty_tpl->tpl_vars['checkbox']->value['totalsell'];?>
)
                            </div>

                        <?php }?> 
                        <?php if (($_smarty_tpl->tpl_vars['checkbox']->value['pricingmodel'] == 7 || $_smarty_tpl->tpl_vars['checkbox']->value['pricingmodel'] == 8) && ($_smarty_tpl->tpl_vars['checkbox']->value['checked'] == 1)) {?>

                            <ul class="componentList">
                                <li><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelQuantity');?>
: <span class="componentListNumber"><?php echo $_smarty_tpl->tpl_vars['checkbox']->value['quantity'];?>
</span></li>
                            </ul>

                        <?php }?> 
                        <?php if ($_smarty_tpl->tpl_vars['stage']->value == 'payment') {?>

                            <?php if ($_smarty_tpl->tpl_vars['checkbox']->value['metadatahtml']) {?>

                                <?php echo $_smarty_tpl->tpl_vars['checkbox']->value['metadatahtml'];?>


                            <?php }?> 
                        <?php }?> 
                        </div> <!-- componentContentText  or componentContentTextLong-->

                        <div class="clear"></div>

                    </div> <!-- checkboxBloc -->

                        <?php if ($_smarty_tpl->tpl_vars['stage']->value == 'qty') {?>

                            <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['checkbox']->value['itemcomponentbuttons'], 'button');
$_smarty_tpl->tpl_vars['button']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['button']->value) {
$_smarty_tpl->tpl_vars['button']->do_else = false;
?>

                    <div class="checkboxBtn">

                        <div class="onOffSwitch">
                            <input type="checkbox" id="onOffSwitch_<?php echo $_smarty_tpl->tpl_vars['checkbox']->value['orderlineid'];?>
" class="onOffSwitchCheckbox" name="onOffSwitch" data-decorator="fnCheckboxEffectAction" data-orderlineid="<?php echo $_smarty_tpl->tpl_vars['orderline']->value['orderlineid'];?>
" data-checkboxlineid="<?php echo $_smarty_tpl->tpl_vars['checkbox']->value['orderlineid'];?>
" data-trigger="change" <?php if ($_smarty_tpl->tpl_vars['checkbox']->value['checked']) {?>checked="checked"<?php }?> />
                            <label for="onOffSwitch_<?php echo $_smarty_tpl->tpl_vars['checkbox']->value['orderlineid'];?>
" class="onOffSwitchLabel">
                                <div class="onOffSwitchInner" ontxt="" offtxt=""></div>
                                <div id="checkbox_<?php echo $_smarty_tpl->tpl_vars['checkbox']->value['orderlineid'];?>
" class="onOffSwitchButton"></div>
                            </label>
                        </div>

                    </div> <!-- checkboxBtn -->

                            <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?> 
                        <?php }?> 
                    <div class="clear"></div>

                </div> <!-- componentBloc innerBoxPadding -->

                <div class="clear"></div>

                        <?php if ($_smarty_tpl->tpl_vars['stage']->value == 'qty') {?>

                            <?php if (($_smarty_tpl->tpl_vars['checkbox']->value['checked'] == 1) && (($_smarty_tpl->tpl_vars['checkbox']->value['metadatahtml']) || ($_smarty_tpl->tpl_vars['checkbox']->value['pricingmodel'] == 7 || $_smarty_tpl->tpl_vars['checkbox']->value['pricingmodel'] == 8))) {?>

                <div class="contentChangeBtn outerBoxPadding" data-decorator="fnSetHashUrl" data-hash-url="componentView|<?php echo $_smarty_tpl->tpl_vars['orderline']->value['orderlineid'];?>
|<?php echo $_smarty_tpl->tpl_vars['checkbox']->value['orderlineid'];?>
">

                    <div class="changeBtnText">
                        <?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelOptionsAndExtras');?>


                        <?php if ($_smarty_tpl->tpl_vars['checkbox']->value['isonekeywordmandatory'] == true) {?>

                            <img class="valueRequiredImg" src="<?php echo $_smarty_tpl->tpl_vars['brandroot']->value;?>
/images/asterisk.png" alt="*" />

                        <?php }?> 
                    </div>

                    <div class="changeBtnImg">
                        <img class="navigationArrow" src="<?php echo $_smarty_tpl->tpl_vars['webroot']->value;?>
/images/icons/change-arrow.png" alt= ">" />
                    </div>

                    <div class="clear"></div>

                </div> <!-- contentChangeBtn outerBoxPadding -->

                            <?php }?> 
                        <?php }?> 
            </div> <!-- innerBox -->

                    <?php }?>  
                <?php }?> 
            <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?> 
        <?php }?> 

	<!-- END CHECKBOXES -->

    <!-- START COMPONENTS -->

        <?php if (!(isset($_smarty_tpl->tpl_vars['bTitleComponent']->value)) && (sizeof($_smarty_tpl->tpl_vars['orderline']->value['sections']) > 0) && ($_smarty_tpl->tpl_vars['stage']->value == 'payment')) {?>

            <?php $_smarty_tpl->_assignInScope('bTitleComponent', "true");?>

            <div class="showHideComponents" data-decorator="fnShowHideComponents" data-orderlineid="<?php echo $_smarty_tpl->tpl_vars['orderline']->value['orderlineid'];?>
" data-isfooter="false">
                <span id="linkToggle_<?php echo $_smarty_tpl->tpl_vars['orderline']->value['orderlineid'];?>
" class="showHideTitle hidden"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelShowOptions');?>
</span>
            </div>

            <div id="contentCustomise_<?php echo $_smarty_tpl->tpl_vars['orderline']->value['orderlineid'];?>
" <?php if ($_smarty_tpl->tpl_vars['stage']->value == 'payment') {?>style="display:none;"<?php }?>>

        <?php }?>

        <div class="componentContainer">
            <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['orderline']->value['sections'], 'section');
$_smarty_tpl->tpl_vars['section']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['section']->value) {
$_smarty_tpl->tpl_vars['section']->do_else = false;
?> 
                <?php if ($_smarty_tpl->tpl_vars['section']->value['showcomponentname'] == true) {?>

                    <?php if ($_smarty_tpl->tpl_vars['stage']->value == 'qty') {?>

            <div id="componentContent_<?php echo $_smarty_tpl->tpl_vars['section']->value['orderlineid'];?>
" class="innerBox">

                    <?php } else { ?>

            <div class="innerBox">

                    <?php }?>

                        <div class="sectionLabel innerBoxPadding">

                            <div class="componentLabel">

                                <?php echo $_smarty_tpl->tpl_vars['section']->value['sectionlabel'];?>


                            </div> <!-- componentLabel -->

                            <div class="componentPrice">

                                <?php echo $_smarty_tpl->tpl_vars['section']->value['itemcomponenttotalsell'];?>


                            </div> <!-- componentPrice -->

                            <div class="clear"></div>

                        </diV> <!-- sectionLabel innerBoxPadding -->

                    <?php if ($_smarty_tpl->tpl_vars['stage']->value == 'qty') {?>

                        <div id="componentrow_<?php echo $_smarty_tpl->tpl_vars['section']->value['orderlineid'];?>
" class="componentBloc innerBoxPadding">

                    <?php } else { ?>

                        <div class="componentBloc innerBoxPadding">

                    <?php }?>

                    <?php if (($_smarty_tpl->tpl_vars['section']->value['haspreview'] > 0) && ($_smarty_tpl->tpl_vars['stage']->value == 'qty')) {?>

                            <img class="componentPreview" src="<?php echo htmlspecialchars((string)$_smarty_tpl->tpl_vars['section']->value['componentpreviewsrc'], ENT_QUOTES, 'UTF-8', true);?>
" alt=""/>
                            <div class="componentContentText">

                    <?php } else { ?> 
                            <div class="componentContentTextLong">

                    <?php }?> 

                                <div class="componentTitle"><?php echo $_smarty_tpl->tpl_vars['section']->value['itemcomponentname'];?>
</div>

                    <?php if (!empty($_smarty_tpl->tpl_vars['section']->value['itemcomponentinfo']) && ($_smarty_tpl->tpl_vars['stage']->value == 'qty')) {?>

                                <div class="componentDescription">

                                    <?php echo $_smarty_tpl->tpl_vars['section']->value['itemcomponentinfo'];?>


                                </div>

                    <?php }?> 
                    <?php if (!empty($_smarty_tpl->tpl_vars['section']->value['itemcomponentmoreinfolinkurl'])) {?>

                                <div class="componentDescription">
                                  <a href="<?php echo $_smarty_tpl->tpl_vars['section']->value['itemcomponentmoreinfolinkurl'];?>
" target="_blank"><?php echo $_smarty_tpl->tpl_vars['section']->value['itemcomponentmoreinfolinktext'];?>
</a>
                                </div>

                    <?php }?> 
                    <?php if (!empty($_smarty_tpl->tpl_vars['section']->value['itemcomponentpriceinfo']) && ($_smarty_tpl->tpl_vars['stage']->value == 'qty')) {?>

                                <div class="componentDescription">

                                    <?php echo $_smarty_tpl->tpl_vars['section']->value['itemcomponentpriceinfo'];?>


                                </div>

                    <?php }?> 
                    <?php if (($_smarty_tpl->tpl_vars['section']->value['pricingmodel'] == 7) || ($_smarty_tpl->tpl_vars['section']->value['pricingmodel'] == 8)) {?>

                                <ul class="componentList">
                                    <li><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelQuantity');?>
: <span class="componentListNumber"><?php echo $_smarty_tpl->tpl_vars['section']->value['quantity'];?>
</span></li>
                                </ul>

                    <?php }?> 
                    <?php if ((sizeof($_smarty_tpl->tpl_vars['section']->value['subsections']) > 0) || (sizeof($_smarty_tpl->tpl_vars['section']->value['checkboxes'])) > 0) {?>

                        <?php $_smarty_tpl->_assignInScope('ulopen', 'false' ,false ,8);?>

                        <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['section']->value['subsections'], 'subsection');
$_smarty_tpl->tpl_vars['subsection']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['subsection']->value) {
$_smarty_tpl->tpl_vars['subsection']->do_else = false;
?> 
                            <?php if ($_smarty_tpl->tpl_vars['subsection']->value['showcomponentname'] == true) {?>

                                <?php if ($_smarty_tpl->tpl_vars['ulopen']->value == "false") {?>

                                    <?php $_smarty_tpl->_assignInScope('ulopen', "true");?>

                                <ul class="componentList">

                                <?php }?> 
                                    <li><?php echo $_smarty_tpl->tpl_vars['subsection']->value['itemcomponentname'];?>
</li>

                                    <?php if (($_smarty_tpl->tpl_vars['subsection']->value['pricingmodel'] == 7) || ($_smarty_tpl->tpl_vars['subsection']->value['pricingmodel'] == 8)) {?>

                                <ul class="componentList">
                                    <li><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelQuantity');?>
: <span class="componentListNumber"><?php echo $_smarty_tpl->tpl_vars['subsection']->value['quantity'];?>
</span></li>
                                </ul>

                                    <?php }?> 

                                <?php if ($_smarty_tpl->tpl_vars['stage']->value == 'payment') {?>

                                    <?php if ($_smarty_tpl->tpl_vars['subsection']->value['metadatahtml']) {?>

                                        <?php echo $_smarty_tpl->tpl_vars['subsection']->value['metadatahtml'];?>


                                    <?php }?> 
                                <?php }?> 
                            <?php }?> 
                        <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?> 
                        <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['section']->value['checkboxes'], 'checkbox');
$_smarty_tpl->tpl_vars['checkbox']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['checkbox']->value) {
$_smarty_tpl->tpl_vars['checkbox']->do_else = false;
?> 
                            <?php if (($_smarty_tpl->tpl_vars['checkbox']->value['showcomponentname'] == true) && ($_smarty_tpl->tpl_vars['checkbox']->value['checked'] == 1)) {?>

                                <?php if ($_smarty_tpl->tpl_vars['ulopen']->value == "false") {?>

                                    <?php $_smarty_tpl->_assignInScope('ulopen', "true");?>

                                <ul class="componentList">

                                <?php }?> 
                                    <li><?php echo $_smarty_tpl->tpl_vars['checkbox']->value['itemcomponentcategoryname'];?>
</li>

                                    <?php if (($_smarty_tpl->tpl_vars['checkbox']->value['pricingmodel'] == 7) || ($_smarty_tpl->tpl_vars['checkbox']->value['pricingmodel'] == 8)) {?>

                                <ul class="componentList">
                                    <li><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelQuantity');?>
: <span class="componentListNumber"><?php echo $_smarty_tpl->tpl_vars['checkbox']->value['quantity'];?>
</span></li>
                                </ul>

                                    <?php }?> 
                                <?php if ($_smarty_tpl->tpl_vars['stage']->value == 'payment') {?>

                                    <?php if ($_smarty_tpl->tpl_vars['checkbox']->value['metadatahtml']) {?>

                                        <?php echo $_smarty_tpl->tpl_vars['checkbox']->value['metadatahtml'];?>


                                    <?php }?> 
                                <?php }?> 
                            <?php }?> 
                        <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?> 
                        <?php if ($_smarty_tpl->tpl_vars['ulopen']->value == "true") {?>

                                </ul>

                        <?php }?> 
                    <?php }?> 
                    <?php if ($_smarty_tpl->tpl_vars['stage']->value == 'payment') {?>

                        <?php if ($_smarty_tpl->tpl_vars['section']->value['metadatahtml']) {?>

                            <?php echo $_smarty_tpl->tpl_vars['section']->value['metadatahtml'];?>


                        <?php }?> 
                    <?php }?> 
                            </div> <!-- componentContentText  or componentContentTextLong-->

                            <div class="clear"></div>

                        </div> <!-- componentBloc innerBoxPadding -->

                    <?php if ($_smarty_tpl->tpl_vars['stage']->value == 'qty') {?>

                        <?php if ((($_smarty_tpl->tpl_vars['section']->value['pricingmodel'] == 7) || ($_smarty_tpl->tpl_vars['section']->value['pricingmodel'] == 8)) || (sizeof($_smarty_tpl->tpl_vars['section']->value['subsections']) > 0) || (sizeof($_smarty_tpl->tpl_vars['section']->value['itemcomponentbuttons']) > 0) || ($_smarty_tpl->tpl_vars['section']->value['metadatahtml']) || (sizeof($_smarty_tpl->tpl_vars['section']->value['checkboxes']) > 0) || ($_smarty_tpl->tpl_vars['subsection']->value['metadatahtml'])) {?>

                        <div class="contentChangeBtn outerBoxPadding" data-decorator="fnSetHashUrl" data-hash-url="componentView|<?php echo $_smarty_tpl->tpl_vars['orderline']->value['orderlineid'];?>
|<?php echo $_smarty_tpl->tpl_vars['section']->value['orderlineid'];?>
">

                            <div class="changeBtnText">
                                <?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelOptionsAndExtras');?>


                                <?php if ($_smarty_tpl->tpl_vars['section']->value['isonekeywordmandatory'] == true) {?>

                                    <img class="valueRequiredImg" src="<?php echo $_smarty_tpl->tpl_vars['brandroot']->value;?>
/images/asterisk.png" alt="*" />

                                <?php }?> 
                            </div>

                            <div class="changeBtnImg">
                                <img class="navigationArrow" src="<?php echo $_smarty_tpl->tpl_vars['webroot']->value;?>
/images/icons/change-arrow.png" alt= ">" />
                            </div>

                            <div class="clear"></div>

                        </div> <!-- contentChangeBtn outerBoxPadding -->

                        <?php }?> 
                    <?php }?> 
                    </div> <!-- innerBox -->

                <?php }?> 
            <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>         </div>
        <!-- END COMPONENTS -->

        <!-- START LINEFOOTER SECTIONS -->

        <?php if (sizeof($_smarty_tpl->tpl_vars['orderline']->value['linefootersections']) > 0) {?>

            <?php if (!(isset($_smarty_tpl->tpl_vars['bTitleComponent']->value)) && ($_smarty_tpl->tpl_vars['stage']->value == 'payment')) {?>

                <?php $_smarty_tpl->_assignInScope('bTitleComponent', "true");?>

        <div class="showHideComponents" data-decorator="fnShowHideComponents" data-orderlineid="<?php echo $_smarty_tpl->tpl_vars['orderline']->value['orderlineid'];?>
" data-isfooter="false">
            <span id="linkToggle_<?php echo $_smarty_tpl->tpl_vars['orderline']->value['orderlineid'];?>
" class="showHideTitle hidden"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelShowOptions');?>
</span>
        </div>

        <div id="contentCustomise_<?php echo $_smarty_tpl->tpl_vars['orderline']->value['orderlineid'];?>
" <?php if ($_smarty_tpl->tpl_vars['stage']->value == 'payment') {?>style="display:none;"<?php }?>>

            <?php }?>

            <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['orderline']->value['linefootersections'], 'section');
$_smarty_tpl->tpl_vars['section']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['section']->value) {
$_smarty_tpl->tpl_vars['section']->do_else = false;
?> 
                <?php if ($_smarty_tpl->tpl_vars['stage']->value == 'qty') {?>

                <div id="componentContent_<?php echo $_smarty_tpl->tpl_vars['section']->value['orderlineid'];?>
" class="innerBox">

                <?php } else { ?>

                <div class="innerBox">

                <?php }?>

                    <div class="sectionLabel innerBoxPadding">

                        <div class="componentLabel">
                            <?php echo $_smarty_tpl->tpl_vars['section']->value['sectionlabel'];?>

                        </div>

                        <div class="componentPrice">
                            <?php echo $_smarty_tpl->tpl_vars['section']->value['itemcomponenttotalsell'];?>

                        </div>

                        <div class="clear"></div>

                    </div> <!-- sectionLabel innerBoxPadding -->

                    <?php if ($_smarty_tpl->tpl_vars['section']->value['showcomponentname'] == true) {?>

                        <?php if ($_smarty_tpl->tpl_vars['stage']->value == 'qty') {?>

                    <div id="componentrow_<?php echo $_smarty_tpl->tpl_vars['section']->value['orderlineid'];?>
" class="componentBloc innerBoxPadding">

                        <?php } else { ?>

                    <div class="componentBloc innerBoxPadding">

                        <?php }?>

                        <?php if (($_smarty_tpl->tpl_vars['section']->value['haspreview'] > 0) && ($_smarty_tpl->tpl_vars['stage']->value == 'qty')) {?>

                        <img class="componentPreview" src="<?php echo htmlspecialchars((string)$_smarty_tpl->tpl_vars['section']->value['componentpreviewsrc'], ENT_QUOTES, 'UTF-8', true);?>
" alt=""/>
                        <div class="componentContentText">

                        <?php } else { ?> 
                        <div class="componentContentTextLong">

                        <?php }?> 
                            <div class="componentTitle">
                                <?php echo $_smarty_tpl->tpl_vars['section']->value['itemcomponentname'];?>

                            </div>

                        <?php if (!empty($_smarty_tpl->tpl_vars['section']->value['itemcomponentinfo']) && ($_smarty_tpl->tpl_vars['stage']->value == 'qty')) {?>

                            <div class="componentDescription">
                                <?php echo $_smarty_tpl->tpl_vars['section']->value['itemcomponentinfo'];?>

                            </div>

                        <?php }?> 
                        <?php if (!empty($_smarty_tpl->tpl_vars['section']->value['itemcomponentmoreinfolinkurl'])) {?>

                                <div class="componentDescription">
                                  <a href="<?php echo $_smarty_tpl->tpl_vars['section']->value['itemcomponentmoreinfolinkurl'];?>
" target="_blank"><?php echo $_smarty_tpl->tpl_vars['section']->value['itemcomponentmoreinfolinktext'];?>
</a>
                                </div>

                        <?php }?> 
                        <?php if (!empty($_smarty_tpl->tpl_vars['section']->value['itemcomponentpriceinfo']) && ($_smarty_tpl->tpl_vars['stage']->value == 'qty')) {?>

                            <div class="componentDescription">
                                <?php echo $_smarty_tpl->tpl_vars['section']->value['itemcomponentpriceinfo'];?>

                            </div>

                        <?php }?> 
                        <?php if (($_smarty_tpl->tpl_vars['section']->value['pricingmodel'] == 7) || ($_smarty_tpl->tpl_vars['section']->value['pricingmodel'] == 8)) {?>

                            <ul class="componentList">
                                <li><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelQuantity');?>
: <span class="componentListNumber"><?php echo $_smarty_tpl->tpl_vars['section']->value['quantity'];?>
</span></li>
                            </ul>

                        <?php }?> 
                        <?php if ((sizeof($_smarty_tpl->tpl_vars['section']->value['subsections']) > 0) || (sizeof($_smarty_tpl->tpl_vars['section']->value['checkboxes']) > 0)) {?>

                            <?php $_smarty_tpl->_assignInScope('ulopen', 'false' ,false ,8);?>

                            <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['section']->value['subsections'], 'subsection');
$_smarty_tpl->tpl_vars['subsection']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['subsection']->value) {
$_smarty_tpl->tpl_vars['subsection']->do_else = false;
?> 
                                <?php if ($_smarty_tpl->tpl_vars['subsection']->value['showcomponentname'] == true) {?>

                                    <?php if ($_smarty_tpl->tpl_vars['ulopen']->value == "false") {?>

                                        <?php $_smarty_tpl->_assignInScope('ulopen', "true");?>

                            <ul class="componentList">

                                    <?php }?> 
                                <li><?php echo $_smarty_tpl->tpl_vars['subsection']->value['itemcomponentname'];?>
</li>

                                <?php if (($_smarty_tpl->tpl_vars['subsection']->value['pricingmodel'] == 7) || ($_smarty_tpl->tpl_vars['subsection']->value['pricingmodel'] == 8)) {?>

                                <ul class="componentList">
                                    <li><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelQuantity');?>
: <span class="componentListNumber"><?php echo $_smarty_tpl->tpl_vars['subsection']->value['quantity'];?>
</span></li>
                                </ul>

                                <?php }?> 
                                    <?php if ($_smarty_tpl->tpl_vars['stage']->value == 'payment') {?>

                                        <?php if ($_smarty_tpl->tpl_vars['subsection']->value['metadatahtml']) {?>

                                            <?php echo $_smarty_tpl->tpl_vars['subsection']->value['metadatahtml'];?>


                                        <?php }?> 
                                    <?php }?> 
                                <?php }?> 
                            <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?> 
                            <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['section']->value['checkboxes'], 'checkbox');
$_smarty_tpl->tpl_vars['checkbox']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['checkbox']->value) {
$_smarty_tpl->tpl_vars['checkbox']->do_else = false;
?> 
                                <?php if (($_smarty_tpl->tpl_vars['checkbox']->value['showcomponentname'] == true) && ($_smarty_tpl->tpl_vars['checkbox']->value['checked'] == 1)) {?>

                                    <?php if ($_smarty_tpl->tpl_vars['ulopen']->value == "false") {?>

                                        <?php $_smarty_tpl->_assignInScope('ulopen', "true");?>

                            <ul class="componentList">

                                    <?php }?> 
                                <li><?php echo $_smarty_tpl->tpl_vars['checkbox']->value['itemcomponentname'];?>
</li>

                                    <?php if (($_smarty_tpl->tpl_vars['checkbox']->value['pricingmodel'] == 7) || ($_smarty_tpl->tpl_vars['checkbox']->value['pricingmodel'] == 8)) {?>

                                <ul class="componentList">
                                    <li><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelQuantity');?>
: <span class="componentListNumber"><?php echo $_smarty_tpl->tpl_vars['checkbox']->value['quantity'];?>
</span></li>
                                </ul>

                                    <?php }?> 
                                    <?php if ($_smarty_tpl->tpl_vars['stage']->value == 'payment') {?>

                                        <?php if ($_smarty_tpl->tpl_vars['checkbox']->value['metadatahtml']) {?>

                                            <?php echo $_smarty_tpl->tpl_vars['checkbox']->value['metadatahtml'];?>


                                        <?php }?> 
                                    <?php }?> 
                                <?php }?> 
                            <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?> 
                            <?php if ($_smarty_tpl->tpl_vars['ulopen']->value == "true") {?>

                            </ul> <!-- componentList -->

                            <?php }?> 
                        <?php }?> 
                        <?php if ($_smarty_tpl->tpl_vars['stage']->value == 'payment') {?>

                            <?php if ($_smarty_tpl->tpl_vars['section']->value['metadatahtml']) {?>

                                <?php echo $_smarty_tpl->tpl_vars['section']->value['metadatahtml'];?>


                            <?php }?> 
                        <?php }?> 
                        </div>  <!-- componentContentText  or componentContentTextLong-->

                        <div class="clear"></div>

                    </div> <!-- componentBloc innerBoxPadding -->

                        <?php if ($_smarty_tpl->tpl_vars['stage']->value == 'qty') {?>

                            <?php if ((($_smarty_tpl->tpl_vars['section']->value['pricingmodel'] == 7) || ($_smarty_tpl->tpl_vars['section']->value['pricingmodel'] == 8)) || (sizeof($_smarty_tpl->tpl_vars['section']->value['subsections']) > 0) || (sizeof($_smarty_tpl->tpl_vars['section']->value['itemcomponentbuttons']) > 0) || ($_smarty_tpl->tpl_vars['section']->value['metadatahtml']) || (sizeof($_smarty_tpl->tpl_vars['section']->value['checkboxes']) > 0) || ($_smarty_tpl->tpl_vars['subsection']->value['metadatahtml'])) {?>

                        <div class="contentChangeBtn outerBoxPadding" data-decorator="fnSetHashUrl" data-hash-url="componentView|<?php echo $_smarty_tpl->tpl_vars['orderline']->value['orderlineid'];?>
|<?php echo $_smarty_tpl->tpl_vars['section']->value['orderlineid'];?>
">

                            <div class="changeBtnText">
                                <?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelOptionsAndExtras');?>


                                <?php if ($_smarty_tpl->tpl_vars['section']->value['isonekeywordmandatory'] == true) {?>

                                <img class="valueRequiredImg" src="<?php echo $_smarty_tpl->tpl_vars['brandroot']->value;?>
/images/asterisk.png" alt="*" />

                                <?php }?>                             </div>

                            <div class="changeBtnImg">
                                <img class="navigationArrow" src="<?php echo $_smarty_tpl->tpl_vars['webroot']->value;?>
/images/icons/change-arrow.png" alt= ">" />
                            </div>

                            <div class="clear"></div>

                        </div> <!-- contentChangeBtn outerBoxPadding -->

                            <?php }?> 
                        <?php }?> 
                    <?php }?> 
                </div>

                <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?> 
            <?php }?> 

        <!-- END LINEFOOTER SECTIONS -->

        <!-- LINEFOOTER CHECKBOXES -->

        <?php if ((sizeof($_smarty_tpl->tpl_vars['orderline']->value['linefootercheckboxes']) > 0) && !(isset($_smarty_tpl->tpl_vars['bTitleComponent']->value)) && ($_smarty_tpl->tpl_vars['stage']->value == 'payment')) {?>

            <?php $_smarty_tpl->_assignInScope('bTitleComponent', "true");?>

        <div class="showHideComponents" data-decorator="fnShowHideComponents" data-orderlineid="<?php echo $_smarty_tpl->tpl_vars['orderline']->value['orderlineid'];?>
" data-isfooter="false">
            <span id="linkToggle_<?php echo $_smarty_tpl->tpl_vars['orderline']->value['orderlineid'];?>
" class="showHideTitle hidden"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelShowOptions');?>
</span>
        </div>

        <div id="contentCustomise_<?php echo $_smarty_tpl->tpl_vars['orderline']->value['orderlineid'];?>
" <?php if ($_smarty_tpl->tpl_vars['stage']->value == 'payment') {?>style="display:none;"<?php }?>>

        <?php }?>


        <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['orderline']->value['linefootercheckboxes'], 'checkbox');
$_smarty_tpl->tpl_vars['checkbox']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['checkbox']->value) {
$_smarty_tpl->tpl_vars['checkbox']->do_else = false;
?>

            <?php if ($_smarty_tpl->tpl_vars['checkbox']->value['showcomponentname'] == true) {?>

                <?php if ($_smarty_tpl->tpl_vars['stage']->value == 'qty' || ($_smarty_tpl->tpl_vars['stage']->value == 'payment' && $_smarty_tpl->tpl_vars['checkbox']->value['checked'] == 1)) {?>

                    <?php if ($_smarty_tpl->tpl_vars['stage']->value == 'qty') {?>

                <div id="componentContent_<?php echo $_smarty_tpl->tpl_vars['checkbox']->value['orderlineid'];?>
" class="innerBox">

                    <?php } else { ?>

                <div class="innerBox">

                    <?php }?>

                    <div class="sectionLabel innerBoxPadding">

                        <div class="componentLabel">
                            <?php echo $_smarty_tpl->tpl_vars['checkbox']->value['itemcomponentcategoryname'];?>

                        </div>

                    <?php if ($_smarty_tpl->tpl_vars['checkbox']->value['checked'] || $_smarty_tpl->tpl_vars['checkbox']->value['totalsell'] == $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelNotAvailable')) {?>

                        <div class="componentPrice">
                            <?php echo $_smarty_tpl->tpl_vars['checkbox']->value['totalsell'];?>

                        </div>

                    <?php }?> 
                        <div class="clear"></div>

                    </div> <!-- sectionLabel innerBoxPadding-->

                    <div class="componentBloc innerBoxPadding">

                    <?php if (($_smarty_tpl->tpl_vars['stage']->value == 'qty')) {?>

                        <div class="checkboxBloc">

                    <?php } else { ?>

                        <div>

                    <?php }?>

                    <?php if (($_smarty_tpl->tpl_vars['checkbox']->value['haspreview'] > 0) && ($_smarty_tpl->tpl_vars['stage']->value == 'qty')) {?>

                            <img class="componentPreview" src="<?php echo htmlspecialchars((string)$_smarty_tpl->tpl_vars['checkbox']->value['componentpreviewsrc'], ENT_QUOTES, 'UTF-8', true);?>
" alt=""/>
                            <div class="componentContentText">

                    <?php } else { ?> 
                            <div class="componentContentTextLong">

                    <?php }?> 
                                <div class="componentTitle">
                                    <?php echo $_smarty_tpl->tpl_vars['checkbox']->value['itemcomponentname'];?>

                                </div>

                    <?php if (!empty($_smarty_tpl->tpl_vars['checkbox']->value['itemcomponentinfo']) && ($_smarty_tpl->tpl_vars['stage']->value == 'qty')) {?>

                                <div class="componentDescription">
                                    <?php echo $_smarty_tpl->tpl_vars['checkbox']->value['itemcomponentinfo'];?>

                                </div>

                    <?php }?> 
                    <?php if (!empty($_smarty_tpl->tpl_vars['checkbox']->value['itemcomponentmoreinfolinkurl'])) {?>

                                <div class="componentDescription">
                                  <a href="<?php echo $_smarty_tpl->tpl_vars['checkbox']->value['itemcomponentmoreinfolinkurl'];?>
" target="_blank"><?php echo $_smarty_tpl->tpl_vars['checkbox']->value['itemcomponentmoreinfolinktext'];?>
</a>
                                </div>

                    <?php }?>                     

                    <?php if (!empty($_smarty_tpl->tpl_vars['checkbox']->value['itemcomponentpriceinfo']) && ($_smarty_tpl->tpl_vars['stage']->value == 'qty')) {?>

                                <div class="componentDescription">
                                    <?php echo $_smarty_tpl->tpl_vars['checkbox']->value['itemcomponentpriceinfo'];?>

                                </div>

                    <?php }?> 

                    <?php if (($_smarty_tpl->tpl_vars['stage']->value == 'qty') && (!$_smarty_tpl->tpl_vars['checkbox']->value['checked'])) {?>

                                <div class="componentCheckBoxPrice">
                                    (<?php echo $_smarty_tpl->tpl_vars['checkbox']->value['totalsell'];?>
)
                                </div>

                    <?php }?> 
                    <?php if ((($_smarty_tpl->tpl_vars['checkbox']->value['pricingmodel'] == 7) || ($_smarty_tpl->tpl_vars['checkbox']->value['pricingmodel'] == 8)) && ($_smarty_tpl->tpl_vars['checkbox']->value['checked'] == 1)) {?>

                                <ul class="componentList">
                                    <li><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelQuantity');?>
: <span class="componentListNumber"><?php echo $_smarty_tpl->tpl_vars['checkbox']->value['quantity'];?>
</span></li>
                                </ul>

                    <?php }?> 
                    <?php if ($_smarty_tpl->tpl_vars['stage']->value == 'payment') {?>

                        <?php if ($_smarty_tpl->tpl_vars['checkbox']->value['metadatahtml']) {?>

                            <?php echo $_smarty_tpl->tpl_vars['checkbox']->value['metadatahtml'];?>


                        <?php }?> 
                    <?php }?> 
                            </div> <!-- componentContentText  or componentContentTextLong-->

                            <div class="clear"></div>

                        </div> <!-- checkboxBloc -->

                    <?php if ($_smarty_tpl->tpl_vars['stage']->value == 'qty') {?>

                        <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['checkbox']->value['itemcomponentbuttons'], 'button');
$_smarty_tpl->tpl_vars['button']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['button']->value) {
$_smarty_tpl->tpl_vars['button']->do_else = false;
?>

                        <div class="checkboxBtn">

                            <div class="onOffSwitch">
                                <input type="checkbox" id="onOffSwitch_<?php echo $_smarty_tpl->tpl_vars['checkbox']->value['orderlineid'];?>
" class="onOffSwitchCheckbox" name="onOffSwitch" data-decorator="fnCheckboxEffectAction" data-orderlineid="<?php echo $_smarty_tpl->tpl_vars['orderline']->value['orderlineid'];?>
" data-checkboxlineid="<?php echo $_smarty_tpl->tpl_vars['checkbox']->value['orderlineid'];?>
" data-trigger="change" <?php if ($_smarty_tpl->tpl_vars['checkbox']->value['checked']) {?>checked="checked"<?php }?> />
                                <label for="onOffSwitch_<?php echo $_smarty_tpl->tpl_vars['checkbox']->value['orderlineid'];?>
" class="onOffSwitchLabel">
                                    <div class="onOffSwitchInner" ontxt="" offtxt=""></div>
                                    <div id="checkbox_<?php echo $_smarty_tpl->tpl_vars['checkbox']->value['orderlineid'];?>
" class="onOffSwitchButton"></div>
                                </label>
                            </div>

                        </div> <!-- checkboxBtn -->

                        <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?> 
                    <?php }?> 
                        <div class="clear"></div>

                    </div>  <!-- componentBloc innerBoxPadding -->

                    <div class="clear"></div>

                    <?php if ($_smarty_tpl->tpl_vars['stage']->value == 'qty') {?>

                        <?php if (($_smarty_tpl->tpl_vars['checkbox']->value['checked'] == 1) && (($_smarty_tpl->tpl_vars['checkbox']->value['metadatahtml']) || ($_smarty_tpl->tpl_vars['checkbox']->value['pricingmodel'] == 7 || $_smarty_tpl->tpl_vars['checkbox']->value['pricingmodel'] == 8))) {?>

                    <div class="contentChangeBtn outerBoxPadding" data-decorator="fnSetHashUrl" data-hash-url="componentView|<?php echo $_smarty_tpl->tpl_vars['orderline']->value['orderlineid'];?>
|<?php echo $_smarty_tpl->tpl_vars['checkbox']->value['orderlineid'];?>
">

                        <div class="changeBtnText">
                            <?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelOptionsAndExtras');?>


                            <?php if ($_smarty_tpl->tpl_vars['checkbox']->value['isonekeywordmandatory'] == true) {?>

                                <img class="valueRequiredImg" src="<?php echo $_smarty_tpl->tpl_vars['brandroot']->value;?>
/images/asterisk.png" alt="*" />

                            <?php }?> 
                        </div>

                        <div class="changeBtnImg">
                            <img class="navigationArrow" src="<?php echo $_smarty_tpl->tpl_vars['webroot']->value;?>
/images/icons/change-arrow.png" alt= ">" />
                        </div>

                        <div class="clear"></div>

                    </div> <!-- contentChangeBtn outerBoxPadding -->

                        <?php }?> 
                    <?php }?> 
                </div> <!-- innerBox -->

                <?php }?> 
            <?php }?> 
        <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?> 
        <!-- END LINEFOOTER CHECKBOXES -->

        <?php if ((isset($_smarty_tpl->tpl_vars['bTitleComponent']->value)) && ($_smarty_tpl->tpl_vars['stage']->value == 'payment')) {?>

            <?php $_smarty_tpl->_assignInScope('bTitleComponent', "true");?>

        </div>

        <?php }?>


            </div> <!-- outerBoxPadding -->

    <!-- ITEM TOTAL SETCION -->

            <div class="itemSection outerBoxPadding">

    <?php if ((($_smarty_tpl->tpl_vars['orderline']->value[$_smarty_tpl->tpl_vars['lockqty']->value] != true) && ($_smarty_tpl->tpl_vars['stage']->value != 'payment')) && empty($_smarty_tpl->tpl_vars['orderline']->value['itemqtydropdown'])) {?>

                <div class="itemSectionLabelInput">

    <?php } else { ?>

                <div class="itemSectionLabel">

    <?php }?>

                    <?php echo smarty_modifier_replace($_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelItemQuantity'),'^0',$_smarty_tpl->tpl_vars['orderline']->value['orderlineid']);?>

                </div>

                <div class="itemQuantityNumber">

    <?php if (($_smarty_tpl->tpl_vars['orderline']->value[$_smarty_tpl->tpl_vars['lockqty']->value] == true) || ($_smarty_tpl->tpl_vars['stage']->value == 'payment')) {?>

                    <span class="quantityText"><?php echo $_smarty_tpl->tpl_vars['orderline']->value['itemqty'];?>
</span>

    <?php } else { ?> 
                    <input id="hiddeqty_<?php echo $_smarty_tpl->tpl_vars['orderline']->value['orderlineid'];?>
" type="hidden" class="hiddeqty" value="<?php echo $_smarty_tpl->tpl_vars['orderline']->value['itemqty'];?>
"/>

        <?php if (empty($_smarty_tpl->tpl_vars['orderline']->value['itemqtydropdown'])) {?>

                    <input id="itemqty_<?php echo $_smarty_tpl->tpl_vars['orderline']->value['orderlineid'];?>
" type="number" class="quantityInput" maxlength="8" value="<?php echo $_smarty_tpl->tpl_vars['orderline']->value['itemqty'];?>
" data-decorator="fnUpdateOrderQty" data-lineid="<?php echo $_smarty_tpl->tpl_vars['orderline']->value['orderlineid'];?>
" data-trigger="keyup" />
                    <img class="itemQuantityRefresh" src="<?php echo $_smarty_tpl->tpl_vars['brandroot']->value;?>
/images/icons/refresh.png" alt="<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelOrderUpdateItemTotal');?>
" title="<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelOrderUpdateItemTotal');?>
" data-decorator="fnUpdateOrderQty" data-trigger="click" data-lineid="<?php echo $_smarty_tpl->tpl_vars['orderline']->value['orderlineid'];?>
" />
                    <div class="clear"></div>

        <?php } else { ?> 
                    <select id="itemqty_<?php echo $_smarty_tpl->tpl_vars['orderline']->value['orderlineid'];?>
" class="" data-decorator="fnUpdateOrderQty" data-trigger="change" data-lineid="<?php echo $_smarty_tpl->tpl_vars['orderline']->value['orderlineid'];?>
" >

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
                </div> <!-- itemQuantityNumber -->

                <div class="clear"></div>

            </div> <!-- itemSection outerBoxPadding -->

    <?php if ($_smarty_tpl->tpl_vars['stage']->value == 'qty') {?>

            <div class="itemSection outerBoxPadding">

                <div class="itemSectionLabel itemSectionTotalLabel">
                    <?php echo smarty_modifier_replace($_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelItemTotal'),'^0',$_smarty_tpl->tpl_vars['orderline']->value['orderlineid']);?>

                </div>

                <div class="itemTotalNumber">
                    <?php echo $_smarty_tpl->tpl_vars['orderline']->value['itemcompletetotal'];?>

                </div>

                <div class="clear"></div>

            </div> <!-- itemSection outerBoxPadding -->
    <?php }?>
            <div class="clear"></div>

    <!--  END ITEM TOTAL SECTION -->

        </div> <!-- itemDetailcontentTextInside -->

        <div class="clear"></div>

   </div> <!-- contentTextInside -->

<!-- END ITEM SECTION -->

<!-- TOTAL SECTION -->

    <div class="lineTotal">

    <!-- PAYMENT  -->

    <?php if (($_smarty_tpl->tpl_vars['stage']->value == 'payment')) {?>

        <!-- NO VOUCHER  -->

        <?php if ((!$_smarty_tpl->tpl_vars['orderline']->value['itemvoucherapplied']) || ($_smarty_tpl->tpl_vars['vouchersection']->value == 'SHIPPING') || (($_smarty_tpl->tpl_vars['vouchersection']->value == 'TOTAL') && !(($_smarty_tpl->tpl_vars['differenttaxrates']->value) && (!$_smarty_tpl->tpl_vars['specialvouchertype']->value)))) {?>

            <!-- DIFFERNETTAXRATES AND SHOWPRICES WITH TAX  -->

            <?php if (($_smarty_tpl->tpl_vars['differenttaxrates']->value) && ($_smarty_tpl->tpl_vars['showpriceswithtax']->value)) {?>

                <?php if (($_smarty_tpl->tpl_vars['vouchersection']->value == 'TOTAL') && ($_smarty_tpl->tpl_vars['specialvouchertype']->value)) {?>

                    <?php if (($_smarty_tpl->tpl_vars['orderline']->value['itemdiscountvalueraw'] > 0)) {?>

        <div class="itemSection outerBoxPadding">
            <div class="itemSectionLabel"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelSubTotal');?>
</div>
            <div class="itemTotalNumber"><?php echo $_smarty_tpl->tpl_vars['orderline']->value['itemcompletetotal'];?>
</div>
            <div class="clear"></div>
        </div> <!-- itemSection outerBoxPadding -->

        <div class="itemSection outerBoxPadding">
            <div class="itemSectionLabel"><?php echo $_smarty_tpl->tpl_vars['orderline']->value['itemdiscountname'];?>
</div>
            <div class="itemTotalNumber"><?php echo $_smarty_tpl->tpl_vars['orderline']->value['itemdiscountvalue'];?>
</div>
            <div class="clear"></div>
        </div> <!-- itemSection outerBoxPadding -->

        <div class="itemSection outerBoxPadding">
            <div class="itemSectionLabel itemSectionTotalLabel"><?php echo smarty_modifier_replace($_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelItemTotal'),'^0',$_smarty_tpl->tpl_vars['orderline']->value['orderlineid']);?>
</div>
            <div class="itemTotalNumber"><?php echo $_smarty_tpl->tpl_vars['orderline']->value['itemdiscountedvalue'];?>
</div>
            <div class="clear"></div>
        </div> <!-- itemSection outerBoxPadding -->

                    <?php } else { ?> 
        <div class="itemSection outerBoxPadding">
            <div class="itemSectionLabel"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelSubTotal');?>
</div>
            <div class="itemTotalNumber"><?php echo $_smarty_tpl->tpl_vars['orderline']->value['itemcompletetotal'];?>
</div>
            <div class="clear"></div>
        </div> <!-- itemSection outerBoxPadding -->

                    <?php }?> 
                    <!-- SHOWZEROTAX OR DONT SHOWZEROTAX AND THE TAX > 0  -->

                    <?php if ((($_smarty_tpl->tpl_vars['showzerotax']->value) || ((!$_smarty_tpl->tpl_vars['showzerotax']->value) && ($_smarty_tpl->tpl_vars['orderline']->value['itemtaxtotalraw'] > 0)))) {?>

        <div class="line-sub-total-small-bottom">
            <?php echo $_smarty_tpl->tpl_vars['orderline']->value['includesitemtaxtext'];?>

        </div>

                    <?php }?> 
                <?php } else { ?> 
        <div class="itemSection outerBoxPadding">
            <div class="itemSectionLabel itemSectionTotalLabel"><?php echo smarty_modifier_replace($_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelItemTotal'),'^0',$_smarty_tpl->tpl_vars['orderline']->value['orderlineid']);?>
</div>
            <div class="itemTotalNumber"><?php echo $_smarty_tpl->tpl_vars['orderline']->value['itemcompletetotal'];?>
</div>
            <div class="clear"></div>
        </div> <!-- itemSection outerBoxPadding -->

                    <!-- SHOWTAXBREAKDOWN  -->

                    <?php if ($_smarty_tpl->tpl_vars['showtaxbreakdown']->value) {?>

                        <?php if ((($_smarty_tpl->tpl_vars['showzerotax']->value) || ((!$_smarty_tpl->tpl_vars['showzerotax']->value) && ($_smarty_tpl->tpl_vars['orderline']->value['itemtaxtotalraw'] > 0)))) {?>

        <div class="line-sub-total-small-bottom">
            <?php echo $_smarty_tpl->tpl_vars['orderline']->value['includesitemtaxtext'];?>

        </div>

                        <?php }?> 
                    <?php }?> 
                <?php }?> 
            <?php }?> 
            <!-- DIFFERNETTAXRATES AND DONT SHOWPRICESWITHTAX  -->

            <?php if (($_smarty_tpl->tpl_vars['differenttaxrates']->value) && (!$_smarty_tpl->tpl_vars['showpriceswithtax']->value)) {?>

                <!-- VALUE SET TOTAL VOUCHER  -->

                <?php if (($_smarty_tpl->tpl_vars['vouchersection']->value == 'TOTAL') && ($_smarty_tpl->tpl_vars['specialvouchertype']->value)) {?>

                    <?php if ($_smarty_tpl->tpl_vars['orderline']->value['itemdiscountvalueraw'] > 0) {?>

        <div class="itemSection outerBoxPadding">
            <div class="itemSectionLabel"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelSubTotal');?>
</div>
            <div class="itemTotalNumber"><?php echo $_smarty_tpl->tpl_vars['orderline']->value['itemcompletetotal'];?>
</div>
            <div class="clear"></div>
        </div> <!-- itemSection outerBoxPadding -->

        <div class="itemSection outerBoxPadding">
            <div class="itemSectionLabel"><?php echo $_smarty_tpl->tpl_vars['orderline']->value['itemdiscountname'];?>
</div>
            <div class="itemTotalNumber"><?php echo $_smarty_tpl->tpl_vars['orderline']->value['itemdiscountvalue'];?>
</div>
            <div class="clear"></div>
        </div> <!-- itemSection outerBoxPadding -->

                    <?php }?> 
                    <!-- SHOWZEROTAX OR DONT SHOWZEROTAX AND THE TAX > 0  -->

                    <?php if ((($_smarty_tpl->tpl_vars['showzerotax']->value) || ((!$_smarty_tpl->tpl_vars['showzerotax']->value) && ($_smarty_tpl->tpl_vars['orderline']->value['itemtaxtotalraw'] > 0)))) {?>

        <div class="itemSection outerBoxPadding">
            <div class="itemSectionLabel"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelSubTotal');?>
</div>
            <div class="itemTotalNumber"><?php echo $_smarty_tpl->tpl_vars['orderline']->value['itemdiscountedvalue'];?>
</div>
            <div class="clear"></div>
        </div> <!-- itemSection outerBoxPadding -->

        <div class="itemSection outerBoxPadding">
            <div class="itemSectionLabel"><?php echo $_smarty_tpl->tpl_vars['orderline']->value['itemtaxratename'];?>
 (<?php echo $_smarty_tpl->tpl_vars['orderline']->value['itemtaxrate'];?>
%):</div>
            <div class="itemTotalNumber"><?php echo $_smarty_tpl->tpl_vars['orderline']->value['itemtaxtotal'];?>
</div>
            <div class="clear"></div>
        </div> <!-- itemSection -->

                    <?php }?> 
                <?php } else { ?> 
                    <!-- SHOWZEROTAX OR DONT SHOWZEROTAX AND THE TAX > 0  -->

                    <?php if ((($_smarty_tpl->tpl_vars['showzerotax']->value) || ((!$_smarty_tpl->tpl_vars['showzerotax']->value) && ($_smarty_tpl->tpl_vars['orderline']->value['itemtaxtotalraw'] > 0)))) {?>

        <div class="itemSection outerBoxPadding">
            <div class="itemSectionLabel"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelSubTotal');?>
</div>
            <div class="itemTotalNumber"><?php echo $_smarty_tpl->tpl_vars['orderline']->value['itemcompletetotal'];?>
</div>
            <div class="clear"></div>
        </div> <!-- itemSection outerBoxPadding -->

        <div class="itemSection outerBoxPadding">
            <div class="itemSectionLabel"><?php echo $_smarty_tpl->tpl_vars['orderline']->value['itemtaxratename'];?>
 (<?php echo $_smarty_tpl->tpl_vars['orderline']->value['itemtaxrate'];?>
%)</div>
            <div class="itemTotalNumber"><?php echo $_smarty_tpl->tpl_vars['orderline']->value['itemtaxtotal'];?>
</div>
            <div class="clear"></div>
        </div> <!-- itemSection outerBoxPadding -->

                    <?php }?> 
                <?php }?> 
        <div class="itemSection outerBoxPadding">
            <div class="itemSectionLabel itemSectionTotalLabel"><?php echo smarty_modifier_replace($_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelItemTotal'),'^0',$_smarty_tpl->tpl_vars['orderline']->value['orderlineid']);?>
</div>
            <div class="itemTotalNumber"><?php echo $_smarty_tpl->tpl_vars['orderline']->value['itemtotal'];?>
</div>
            <div class="clear"></div>
        </div> <!-- itemSection outerBoxPadding -->

            <?php }?> 
            <!-- NOT DIFFERNETTAXRATES  -->
            <!-- NOT DIFFERNET TAX-RATES  -->
            <?php if ((!$_smarty_tpl->tpl_vars['differenttaxrates']->value)) {?>
                    <?php if (($_smarty_tpl->tpl_vars['orderline']->value['itemdiscountvalueraw'] > 0)) {?>
        <div class="itemSection outerBoxPadding">
            <div class="itemSectionLabel"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelSubTotal');?>
</div>
            <div class="itemTotalNumber"><?php echo $_smarty_tpl->tpl_vars['orderline']->value['itemcompletetotal'];?>
</div>
            <div class="clear"></div>
        </div> <!-- itemSection outerBoxPadding -->

        <div class="itemSection outerBoxPadding">
            <div class="itemSectionLabel"><?php echo $_smarty_tpl->tpl_vars['orderline']->value['itemdiscountname'];?>
</div>
            <div class="itemTotalNumber"><?php echo $_smarty_tpl->tpl_vars['orderline']->value['itemdiscountvalue'];?>
</div>
            <div class="clear"></div>
        </div> <!-- itemSection outerBoxPadding -->
                    <?php }?>
        <div class="itemSection outerBoxPadding">
            <div class="itemSectionLabel itemSectionTotalLabel"><?php echo smarty_modifier_replace($_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelItemTotal'),'^0',$_smarty_tpl->tpl_vars['orderline']->value['orderlineid']);?>
</div>
            <div class="itemTotalNumber"><?php echo $_smarty_tpl->tpl_vars['orderline']->value['itemtotal'];?>
</div>
            <div class="clear"></div>
        </div> <!-- itemSection outerBoxPadding -->

            <?php }?> 
        <?php } else { ?> 
            <!-- PRODUCT VOUCHER  -->

            <?php if ($_smarty_tpl->tpl_vars['vouchersection']->value == 'PRODUCT') {?>

                <?php if ($_smarty_tpl->tpl_vars['orderline']->value['itemdiscountvalueraw'] > 0) {?>

        <div class="itemSection outerBoxPadding">
            <div class="itemSectionLabel"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelSubTotal');?>
</div>
            <div class="itemTotalNumber"><?php echo $_smarty_tpl->tpl_vars['orderline']->value['itemcompletetotal'];?>
</div>
            <div class="clear"></div>
        </div> <!-- itemSection outerBoxPadding -->

        <div class="itemSection outerBoxPadding">
            <div class="itemSectionLabel"><?php echo $_smarty_tpl->tpl_vars['orderline']->value['itemdiscountname'];?>
</div>
            <div class="itemTotalNumber"><?php echo $_smarty_tpl->tpl_vars['orderline']->value['itemdiscountvalue'];?>
</div>
            <div class="clear"></div>
        </div> <!-- itemSection outerBoxPadding -->

                <?php }?> 
                <!-- DIFFERNETTAXRATES AND SHOWPRICES WITH TAX -->

                <?php if ((($_smarty_tpl->tpl_vars['differenttaxrates']->value) && ($_smarty_tpl->tpl_vars['showpriceswithtax']->value))) {?>

        <div class="itemSection outerBoxPadding">
            <div class="itemSectionLabel itemSectionTotalLabel"><?php echo smarty_modifier_replace($_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelItemTotal'),'^0',$_smarty_tpl->tpl_vars['orderline']->value['orderlineid']);?>
</div>
            <div class="itemTotalNumber"><?php echo $_smarty_tpl->tpl_vars['orderline']->value['itemdiscountedvalue'];?>
</div>
            <div class="clear"></div>
        </div> <!-- itemSection outerBoxPadding -->

                    <!-- SHOWTAXBREAKDOWN  -->

                    <?php if (($_smarty_tpl->tpl_vars['showtaxbreakdown']->value)) {?>

                        <?php if ((($_smarty_tpl->tpl_vars['showzerotax']->value) || ((!$_smarty_tpl->tpl_vars['showzerotax']->value) && ($_smarty_tpl->tpl_vars['orderline']->value['itemtaxtotalraw'] > 0)))) {?>

        <div class="line-sub-total-small-bottom">
            <?php echo $_smarty_tpl->tpl_vars['orderline']->value['includesitemtaxtext'];?>

        </div>

                        <?php }?> 
                    <?php }?> 
                <?php }?> 
                <!-- DIFFERNETTAXRATES AND DONT SHOWPRICESWITHTAX -->

                <?php if ((($_smarty_tpl->tpl_vars['differenttaxrates']->value) && (!$_smarty_tpl->tpl_vars['showpriceswithtax']->value))) {?>

                    <!-- SHOWZEROTAX OR DONT SHOWZEROTAX AND THE TAX > 0 -->

                    <?php if ((($_smarty_tpl->tpl_vars['showzerotax']->value) || ((!$_smarty_tpl->tpl_vars['showzerotax']->value) && ($_smarty_tpl->tpl_vars['orderline']->value['itemtaxtotalraw'] > 0)))) {?>

        <div class="itemSection outerBoxPadding">
            <div class="itemSectionLabel"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelSubTotal');?>
</div>
            <div class="itemTotalNumber"><?php echo $_smarty_tpl->tpl_vars['orderline']->value['itemdiscountedvalue'];?>
</div>
            <div class="clear"></div>
        </div> <!-- itemSection outerBoxPadding -->

        <div class="itemSection outerBoxPadding">
            <div class="itemSectionLabel"><?php echo $_smarty_tpl->tpl_vars['orderline']->value['itemtaxratename'];?>
 (<?php echo $_smarty_tpl->tpl_vars['orderline']->value['itemtaxrate'];?>
%)</div>
            <div class="itemTotalNumber"><?php echo $_smarty_tpl->tpl_vars['orderline']->value['itemtaxtotal'];?>
</div>
            <div class="clear"></div>
        </div> <!-- itemSection outerBoxPadding -->

                    <?php }?> 
        <div class="itemSection outerBoxPadding">
            <div class="itemSectionLabel itemSectionTotalLabel"><?php echo smarty_modifier_replace($_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelItemTotal'),'^0',$_smarty_tpl->tpl_vars['orderline']->value['orderlineid']);?>
</div>
            <div class="itemTotalNumber"><?php echo $_smarty_tpl->tpl_vars['orderline']->value['itemtotal'];?>
</div>
            <div class="clear"></div>
        </div> <!-- itemSection outerBoxPadding -->

                <?php }?> 
                <!-- NOT DIFFERNETTAXRATES -->
                <!-- NOT DIFFERNET TAX RATES -->

                <?php if ((!$_smarty_tpl->tpl_vars['differenttaxrates']->value)) {?>


        <div class="itemSection outerBoxPadding">
            <div class="itemSectionLabel itemSectionTotalLabel"><?php echo smarty_modifier_replace($_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelItemTotal'),'^0',$_smarty_tpl->tpl_vars['orderline']->value['orderlineid']);?>
</div>
            <div class="itemTotalNumber"><?php echo $_smarty_tpl->tpl_vars['orderline']->value['itemdiscountedvalue'];?>
</div>
            <div class="clear"></div>
        </div> <!-- itemSection outerBoxPadding -->

                <?php }?> 
            <?php }?> 
            <!-- VALUE OFF TOTAL VOUCHER  -->

            <?php if ((($_smarty_tpl->tpl_vars['vouchersection']->value == 'TOTAL') && (($_smarty_tpl->tpl_vars['differenttaxrates']->value) && (!$_smarty_tpl->tpl_vars['specialvouchertype']->value)))) {?>

                <?php if ($_smarty_tpl->tpl_vars['orderline']->value['itemdiscountvalueraw'] > 0) {?>

        <div class="itemSection outerBoxPadding">
            <div class="itemSectionLabel"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelSubTotal');?>
</div>
            <div class="itemTotalNumber"><?php echo $_smarty_tpl->tpl_vars['orderline']->value['itemcompletetotal'];?>
</div>
            <div class="clear"></div>
        </div> <!-- itemSection outerBoxPadding -->

        <div class="itemSection outerBoxPadding">
            <div class="itemSectionLabel"><?php echo $_smarty_tpl->tpl_vars['orderline']->value['itemdiscountname'];?>
</div>
            <div class="itemTotalNumber"><?php echo $_smarty_tpl->tpl_vars['orderline']->value['itemdiscountvalue'];?>
</div>
            <div class="clear"></div>
        </div> <!-- itemSection outerBoxPadding -->

                <?php }?>
                <?php if ((!$_smarty_tpl->tpl_vars['showpriceswithtax']->value)) {?>

                    <!-- SHOWZEROTAX OR DONT SHOWZEROTAX AND THE TAX > 0  -->

                    <?php if ((($_smarty_tpl->tpl_vars['showzerotax']->value) || ((!$_smarty_tpl->tpl_vars['showzerotax']->value) && ($_smarty_tpl->tpl_vars['orderline']->value['itemtaxtotalraw'] > 0)))) {?>

        <div class="itemSection outerBoxPadding">
            <div class="itemSectionLabel"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelSubTotal');?>
</div>
            <div class="itemTotalNumber"><?php echo $_smarty_tpl->tpl_vars['orderline']->value['itemdiscountedvalue'];?>
</div>
            <div class="clear"></div>
        </div> <!-- itemSection outerBoxPadding -->

        <div class="itemSection outerBoxPadding">
            <div class="itemSectionLabel"><?php echo $_smarty_tpl->tpl_vars['orderline']->value['itemtaxratename'];?>
 (<?php echo $_smarty_tpl->tpl_vars['orderline']->value['itemtaxrate'];?>
%)</div>
            <div class="itemTotalNumber"><?php echo $_smarty_tpl->tpl_vars['orderline']->value['itemtaxtotal'];?>
</div>
            <div class="clear"></div>
        </div> <!-- itemSection outerBoxPadding -->

        <div class="itemSection outerBoxPadding">
            <div class="itemSectionLabel itemSectionTotalLabel"><?php echo smarty_modifier_replace($_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelItemTotal'),'^0',$_smarty_tpl->tpl_vars['orderline']->value['orderlineid']);?>
</div>
            <div class="itemTotalNumber"><?php echo $_smarty_tpl->tpl_vars['orderline']->value['itemtotal'];?>
</div>
            <div class="clear"></div>
        </div> <!-- itemSection outerBoxPadding -->

                    <?php } else { ?> 
        <div class="itemSection outerBoxPadding">
            <div class="itemSectionLabel itemSectionTotalLabel"><?php echo smarty_modifier_replace($_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelItemTotal'),'^0',$_smarty_tpl->tpl_vars['orderline']->value['orderlineid']);?>
</div>
            <div class="itemTotalNumber"><?php echo $_smarty_tpl->tpl_vars['orderline']->value['itemdiscountedvalue'];?>
</div>
            <div class="clear"></div>
        </div> <!-- itemSection outerBoxPadding -->

                    <?php }?> 
                <?php } else { ?> 
        <div class="itemSection outerBoxPadding">
            <div class="itemSectionLabel itemSectionTotalLabel"><?php echo smarty_modifier_replace($_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelItemTotal'),'^0',$_smarty_tpl->tpl_vars['orderline']->value['orderlineid']);?>
</div>
            <div class="itemTotalNumber"><?php echo $_smarty_tpl->tpl_vars['orderline']->value['itemtotal'];?>
</div>
            <div class="clear"></div>
        </div> <!-- itemSection outerBoxPadding -->

                        <!-- SHOWZEROTAX OR DONT SHOWZEROTAX AND THE TAX > 0  -->

                    <?php if ((($_smarty_tpl->tpl_vars['showzerotax']->value) || ((!$_smarty_tpl->tpl_vars['showzerotax']->value) && ($_smarty_tpl->tpl_vars['orderline']->value['itemtaxtotalraw'] > 0)))) {?>

        <div class="line-sub-total-small-bottom">
            <?php echo $_smarty_tpl->tpl_vars['orderline']->value['includesitemtaxtext'];?>

        </div>

                    <?php }?> 
                <?php }?> 
            <?php }?> 
        <?php }?> 
    <?php }?> 
	</div> <!-- lineTotal -->

<!-- END TOTAL SECTION -->

<?php if ($_smarty_tpl->tpl_vars['call_action']->value == 'init') {?> 
        </div> <!-- orderLine -->

    </div>

</div> <!-- itemOrderLine -->

    <?php }?> 
<?php }?> <?php }
}
