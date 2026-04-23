<?php
/* Smarty version 4.5.3, created on 2026-03-07 07:52:42
  from 'C:\TAOPIX\MediaAlbumWeb\templates\order\orderfooter_small.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.5.3',
  'unifunc' => 'content_69abd94a075ba2_06374789',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '2c6373c12765c4bb4200fca336e279e9cd72a9c9' => 
    array (
      0 => 'C:\\TAOPIX\\MediaAlbumWeb\\templates\\order\\orderfooter_small.tpl',
      1 => 1729602708,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_69abd94a075ba2_06374789 (Smarty_Internal_Template $_smarty_tpl) {
if ((sizeof($_smarty_tpl->tpl_vars['orderfootersections']->value) > 0) || (sizeof($_smarty_tpl->tpl_vars['orderfootercheckboxes']->value) > 0)) {?>

    <?php if ($_smarty_tpl->tpl_vars['call_action']->value == 'init') {?>

        <?php if ($_smarty_tpl->tpl_vars['stage']->value == 'qty') {?>

<div class="orderFooter" id="orderFooter">

        <?php } else { ?>

<div class="orderFooter">

        <?php }?>

    <?php }?> 
    <!-- START ORDERFOOTER COMPONENTS -->

    <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['orderfootersections']->value, 'section');
$_smarty_tpl->tpl_vars['section']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['section']->value) {
$_smarty_tpl->tpl_vars['section']->do_else = false;
?> 
        <?php if ($_smarty_tpl->tpl_vars['section']->value['showcomponentname'] == true) {?>

            <?php if (!(isset($_smarty_tpl->tpl_vars['bTitleOrder']->value))) {?>

    <div class="sectionLabelLegendFooter outerBoxPadding">
        <?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelAdditionalItems');?>

    </div>

                <?php if ($_smarty_tpl->tpl_vars['stage']->value == 'qty') {?>

    <div id="contentFooter" class="outerBoxPadding outerBoxNoPaddingTop">

                <?php } else { ?>

    <div class="outerBoxPadding outerBoxNoPaddingTop">

                <?php }?>


                <?php if (($_smarty_tpl->tpl_vars['stage']->value == 'payment')) {?>

    <div class="showHideComponents" data-decorator="fnShowHideComponents" data-orderlineid="orderfooter" data-isfooter="true">
        <span id="linkToggle_orderfooter" class="showHideTitle hidden"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelShowDetails');?>
</span>
    </div>

    <div id="contentCustomise_orderfooter" style="display:none;">

                <?php }?>

                <?php $_smarty_tpl->_assignInScope('bTitleOrder', "true");?>

            <?php }?> 
            <?php if (($_smarty_tpl->tpl_vars['stage']->value == 'qty')) {?>

        <div  class="innerBox" id="componentContent_<?php echo $_smarty_tpl->tpl_vars['section']->value['orderlineid'];?>
">

            <?php } else { ?>

        <div  class="innerBox">

            <?php }?>

            <div class="sectionLabel innerBoxPadding">

                <div class="componentLabel">

                    <?php echo $_smarty_tpl->tpl_vars['section']->value['sectionlabel'];?>


                </div> <!-- componentLabel -->

                <div class="componentPrice">
                    <?php echo $_smarty_tpl->tpl_vars['section']->value['itemcomponenttotalsell'];?>

                </div>

                <div class="clear"></div>

            </diV> <!-- sectionLabel innerBoxPadding -->

            <?php if (($_smarty_tpl->tpl_vars['stage']->value == 'qty')) {?>

            <div id="componentrow_<?php echo $_smarty_tpl->tpl_vars['section']->value['orderlineid'];?>
" class="componentBloc innerBoxPadding" >

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
                </div>  <!-- componentContentText  or componentContentTextLong-->

                <div class="clear"></div>

            </div> <!-- componentBloc innerBoxPadding -->

            <?php if ($_smarty_tpl->tpl_vars['stage']->value == 'qty') {?>

                <?php if ((($_smarty_tpl->tpl_vars['section']->value['pricingmodel'] == 7) || ($_smarty_tpl->tpl_vars['section']->value['pricingmodel'] == 8)) || (sizeof($_smarty_tpl->tpl_vars['section']->value['subsections']) > 0) || (sizeof($_smarty_tpl->tpl_vars['section']->value['itemcomponentbuttons']) > 0) || ($_smarty_tpl->tpl_vars['section']->value['metadatahtml']) || (sizeof($_smarty_tpl->tpl_vars['section']->value['checkboxes']) > 0) || ($_smarty_tpl->tpl_vars['subsection']->value['metadatahtml'])) {?>

            <div class="contentChangeBtn outerBoxPadding" data-decorator="fnSetHashUrl" data-hash-url="componentView|-1|<?php echo $_smarty_tpl->tpl_vars['section']->value['orderlineid'];?>
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
        </div>  <!-- innerBox -->

        <?php }?> 
    <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?> 
    <!-- END ORDERFOOTER COMPONENTS -->

    <!-- ORDERFOOTER CHECKBOXES START -->

    <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['orderfootercheckboxes']->value, 'checkbox');
$_smarty_tpl->tpl_vars['checkbox']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['checkbox']->value) {
$_smarty_tpl->tpl_vars['checkbox']->do_else = false;
?>

        <?php if ($_smarty_tpl->tpl_vars['checkbox']->value['showcomponentname'] == true) {?>

            <?php if (!(isset($_smarty_tpl->tpl_vars['bTitleOrder']->value)) && (($_smarty_tpl->tpl_vars['stage']->value == 'qty') || ($_smarty_tpl->tpl_vars['stage']->value == 'payment' && $_smarty_tpl->tpl_vars['checkbox']->value['checked'] == 1))) {?>

    <div class="sectionLabelLegendFooter outerBoxPadding">
        <?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelAdditionalItems');?>

    </div>

                <?php if (($_smarty_tpl->tpl_vars['stage']->value == 'qty')) {?>

    <div id="contentFooter" class="outerBoxPadding">

                <?php } else { ?>

    <div class="outerBoxPadding">

                <?php }?>

                <?php if (($_smarty_tpl->tpl_vars['stage']->value == 'payment')) {?>

        <div class="showHideComponents" data-decorator="fnShowHideComponents" data-orderlineid="orderfooter" data-isfooter="true">
            <span id="linkToggle_orderfooter" class="showHideTitle hidden"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelShowDetails');?>
</span>
        </div>

        <div id="contentCustomise_orderfooter" style="display:none;">

                <?php }?>

                <?php $_smarty_tpl->_assignInScope('bTitleOrder', "true");?>

            <?php }?> 
            <?php if (($_smarty_tpl->tpl_vars['stage']->value == 'qty') || ($_smarty_tpl->tpl_vars['stage']->value == 'payment' && $_smarty_tpl->tpl_vars['checkbox']->value['checked'] == 1)) {?>


                <?php if (($_smarty_tpl->tpl_vars['stage']->value == 'qty')) {?>

        <div id="componentContent_<?php echo $_smarty_tpl->tpl_vars['checkbox']->value['orderlineid'];?>
" class="innerBox">

                <?php } else { ?>

        <div class="innerBox">

                <?php }?>

            <div class="sectionLabel innerBoxPadding">

                <div class="componentLabel">

                    <?php echo $_smarty_tpl->tpl_vars['checkbox']->value['itemcomponentcategoryname'];?>


                </div> <!-- componentLabel -->

                <?php if ($_smarty_tpl->tpl_vars['checkbox']->value['checked'] || $_smarty_tpl->tpl_vars['checkbox']->value['totalsell'] == $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelNotAvailable')) {?>

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

                    <?php if (!empty($_smarty_tpl->tpl_vars['checkbox']->value['itemcomponentinfo'])) {?>

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
" class="onOffSwitchCheckbox" name="onOffSwitch" data-decorator="fnCheckboxEffectAction" data-orderlineid="-1" data-checkboxlineid="<?php echo $_smarty_tpl->tpl_vars['checkbox']->value['orderlineid'];?>
" data-trigger="change"<?php if ($_smarty_tpl->tpl_vars['checkbox']->value['checked']) {?>checked="checked"<?php }?> />
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

            <div class="contentChangeBtn outerBoxPadding" data-decorator="fnSetHashUrl" data-hash-url="componentView|-1|<?php echo $_smarty_tpl->tpl_vars['checkbox']->value['orderlineid'];?>
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
    <?php if ((isset($_smarty_tpl->tpl_vars['bTitleOrder']->value))) {?>

        <?php if (($_smarty_tpl->tpl_vars['stage']->value == 'payment')) {?>

    </div> <!-- contentCustomise_orderfooter -->

        <?php }?>

    </div> <!-- contentFooter -->

    <?php }?> 
    <!-- END ORDER FOOTER CHECKBOXES -->

    <div class="footerPriceSection">

        <?php if ($_smarty_tpl->tpl_vars['stage']->value == 'qty') {?>

            <?php if (($_smarty_tpl->tpl_vars['showpriceswithtax']->value == false)) {?>

         <div class="itemSection outerBoxPadding">
            <div class="itemSectionLabel itemSectionTotalLabel"><?php echo $_smarty_tpl->tpl_vars['orderfootersubtotalname']->value;?>
:</div>
            <div class="itemTotalNumber"><?php echo $_smarty_tpl->tpl_vars['orderfooteritemstotalsell']->value;?>
</div>
            <div class="clear"></div>
        </div>

            <?php } else { ?> 
         <div class="itemSection outerBoxPadding">
            <div class="itemSectionLabel itemSectionTotalLabel"><?php echo $_smarty_tpl->tpl_vars['orderfootersubtotalname']->value;?>
:</div>
            <div class="itemTotalNumber"><?php echo $_smarty_tpl->tpl_vars['orderfootertotal']->value;?>
</div>
            <div class="clear"></div>
        </div>

            <?php }?> 
        <div class="clear"></div>

        <?php }?> 

        <?php if ($_smarty_tpl->tpl_vars['stage']->value == 'payment') {?>

            <?php if (($_smarty_tpl->tpl_vars['showpriceswithtax']->value == false)) {?>

                <?php if ((($_smarty_tpl->tpl_vars['showzerotax']->value) || ((!$_smarty_tpl->tpl_vars['showzerotax']->value) && ($_smarty_tpl->tpl_vars['orderfootertaxtotalraw']->value > 0)))) {?>

                    <?php if (($_smarty_tpl->tpl_vars['differenttaxrates']->value)) {?>
         <div class="itemSection outerBoxPadding">
            <div class="itemSectionLabel itemSectionTotalLabel"><?php echo $_smarty_tpl->tpl_vars['orderfootersubtotalname']->value;?>
:</div>
            <div class="itemTotalNumber"><?php echo $_smarty_tpl->tpl_vars['orderfootersubtotal']->value;?>
</div>
            <div class="clear"></div>
        </div>
                    <?php }?>

                    <?php if (($_smarty_tpl->tpl_vars['showtaxbreakdown']->value)) {?>

                        <?php if (($_smarty_tpl->tpl_vars['differenttaxrates']->value)) {?>

                            <?php if (($_smarty_tpl->tpl_vars['footertaxratesequal']->value == 1)) {?>

         <div class="itemSection outerBoxPadding">
            <div class="itemSectionLabel itemSectionTotalLabel"><?php echo $_smarty_tpl->tpl_vars['orderfootertaxname']->value;?>
 (<?php echo $_smarty_tpl->tpl_vars['orderfootertaxrate']->value;?>
%):</div>
            <div class="itemTotalNumber"><?php echo $_smarty_tpl->tpl_vars['orderfootertaxtotal']->value;?>
</div>
            <div class="clear"></div>
        </div>
                            <?php } else { ?>

        <div class="itemSection outerBoxPadding">
            <div class="itemSectionLabel itemSectionTotalLabel"><?php echo $_smarty_tpl->tpl_vars['orderfootertaxname']->value;?>
:</div>
            <div class="itemTotalNumber"><?php echo $_smarty_tpl->tpl_vars['orderfootertaxtotal']->value;?>
</div>
            <div class="clear"></div>
        </div>
                            <?php }?>

                        <?php }?>

                    <?php }?>

                <?php }?>

            <?php }?>

            <?php if (($_smarty_tpl->tpl_vars['showpriceswithtax']->value == false) && (($_smarty_tpl->tpl_vars['showzerotax']->value) || ((!$_smarty_tpl->tpl_vars['showzerotax']->value) && ($_smarty_tpl->tpl_vars['orderfootertaxtotalraw']->value > 0))) && ($_smarty_tpl->tpl_vars['differenttaxrates']->value)) {?>

         <div class="itemSection outerBoxPadding">
            <div class="itemSectionLabel itemSectionTotalLabel"><?php echo $_smarty_tpl->tpl_vars['orderfootertotalname']->value;?>
:</div>
            <div class="itemTotalNumber"><?php echo $_smarty_tpl->tpl_vars['orderfootertotal']->value;?>
</div>
            <div class="clear"></div>
        </div>

            <?php } else { ?>

         <div class="itemSection outerBoxPadding">
            <div class="itemSectionLabel itemSectionTotalLabel"><?php echo $_smarty_tpl->tpl_vars['orderfootersubtotalname']->value;?>
:</div>
            <div class="itemTotalNumber"><?php echo $_smarty_tpl->tpl_vars['orderfootersubtotal']->value;?>
</div>
            <div class="clear"></div>
        </div>

            <?php }?>

            <?php if (($_smarty_tpl->tpl_vars['showpriceswithtax']->value)) {?>

                <?php if ((($_smarty_tpl->tpl_vars['showzerotax']->value) || ((!$_smarty_tpl->tpl_vars['showzerotax']->value) && ($_smarty_tpl->tpl_vars['orderfootertaxtotalraw']->value > 0)))) {?>

                    <?php if (($_smarty_tpl->tpl_vars['showtaxbreakdown']->value)) {?>

                        <?php if (($_smarty_tpl->tpl_vars['differenttaxrates']->value)) {?>

        <div class="itemTotalNumber outerBoxPadding">
            <?php echo $_smarty_tpl->tpl_vars['includesorderfootertaxtext']->value;?>

        </div>
                        <?php }?>

                    <?php }?>

                <?php }?>

            <?php }?>

        <?php }?>

    </div> <!-- footerPriceSection -->

    <?php if ($_smarty_tpl->tpl_vars['call_action']->value == 'init') {?>

</div> <!-- orderFooter -->

    <?php }?> 
<?php }?> <?php }
}
