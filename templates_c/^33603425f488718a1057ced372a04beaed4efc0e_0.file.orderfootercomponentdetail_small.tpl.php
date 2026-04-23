<?php
/* Smarty version 4.5.3, created on 2026-03-07 07:52:48
  from 'C:\TAOPIX\MediaAlbumWeb\templates\order\orderfootercomponentdetail_small.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.5.3',
  'unifunc' => 'content_69abd950129791_86643403',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '33603425f488718a1057ced372a04beaed4efc0e' => 
    array (
      0 => 'C:\\TAOPIX\\MediaAlbumWeb\\templates\\order\\orderfootercomponentdetail_small.tpl',
      1 => 1729602708,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_69abd950129791_86643403 (Smarty_Internal_Template $_smarty_tpl) {
?><div class="contentNavigation" id="contentNavigationOrderFooterComponentDetail">
    <div class="btnDoneTop" data-decorator="fnDoneFromComponent" data-subcomponent="false">
       <img class="backImage" src="<?php echo $_smarty_tpl->tpl_vars['webroot']->value;?>
/images/icons/back-arrow.png" alt="<" />
       <div class="btnDone"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ButtonDone');?>
</div>
       <div class="clear"></div>
    </div>
</div>

<div id="contentRightScrollOrderFooterComponentDetail" class="contentScrollCart">

    <div class="contentVisible">

        <div class="pageLabel">
           <?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelOptionsAndExtras');?>

        </div>

    <?php if ($_smarty_tpl->tpl_vars['stage']->value == 'qty') {?>

        <!-- COMPONENT DETAIL -->

        <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['orderfootersections']->value, 'section');
$_smarty_tpl->tpl_vars['section']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['section']->value) {
$_smarty_tpl->tpl_vars['section']->do_else = false;
?> 
            <?php if ($_smarty_tpl->tpl_vars['section']->value['showcomponentname'] == true) {?>

                <?php if ((($_smarty_tpl->tpl_vars['section']->value['pricingmodel'] == 7) || ($_smarty_tpl->tpl_vars['section']->value['pricingmodel'] == 8)) || (sizeof($_smarty_tpl->tpl_vars['section']->value['subsections']) > 0) || (sizeof($_smarty_tpl->tpl_vars['section']->value['itemcomponentbuttons']) > 0) || ($_smarty_tpl->tpl_vars['section']->value['metadatahtml']) || (sizeof($_smarty_tpl->tpl_vars['section']->value['checkboxes']) > 0)) {?>

       <div id="componentDetail_<?php echo $_smarty_tpl->tpl_vars['section']->value['orderlineid'];?>
" class="componentDetail">

            <div class="outerBox">

                <div>

                    <div class="sectionLabel outerBoxPadding">
                        <div class="subcomponentLabel">
                            <?php echo $_smarty_tpl->tpl_vars['section']->value['itemcomponentname'];?>

                        </div>
                        <div class="subcomponentPrice">
                            <?php echo $_smarty_tpl->tpl_vars['section']->value['totalsell'];?>

                        </div>
                        <div class="clear"></div>
                    </diV>

                    <div id="componentContentDetail_<?php echo $_smarty_tpl->tpl_vars['section']->value['orderlineid'];?>
">

                        <div id="componentRowDetail_<?php echo $_smarty_tpl->tpl_vars['section']->value['orderlineid'];?>
" class="componentBloc outerBoxPadding" >

                        <?php if ($_smarty_tpl->tpl_vars['section']->value['haspreview'] > 0) {?>

                            <img class="componentPreview" src="<?php echo htmlspecialchars((string)$_smarty_tpl->tpl_vars['section']->value['componentpreviewsrc'], ENT_QUOTES, 'UTF-8', true);?>
" alt=""/>
                            <div class="componentDetailContentText">

                        <?php } else { ?> 
                            <div class="componentDetailContentTextLong">

                        <?php }?> 
                        <?php if (!empty($_smarty_tpl->tpl_vars['section']->value['itemcomponentinfo'])) {?>

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
                        <?php if (!empty($_smarty_tpl->tpl_vars['section']->value['itemcomponentpriceinfo'])) {?>

                                <div class="componentDescription">
                                    <?php echo $_smarty_tpl->tpl_vars['section']->value['itemcomponentpriceinfo'];?>

                                </div>

                        <?php }?> 
                            </div> <!-- componentDetailContentTextLong OR componentDetailContentText -->

                            <div class="clear"></div>

                        </div> <!-- componentBloc outerBoxPadding -->

                        <?php if (($_smarty_tpl->tpl_vars['section']->value['pricingmodel'] == 7) || ($_smarty_tpl->tpl_vars['section']->value['pricingmodel'] == 8)) {?>

                        <div class="itemSection outerBoxPadding">
                            <div class="itemSectionLabel">
                                <div class="formLine1">
                                    <label for="itemqty_<?php echo $_smarty_tpl->tpl_vars['section']->value['orderlineid'];?>
"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelQuantity');?>
</label>
                                </div>
                                <div class="clear"></div>
                            </div>
                            <div class="itemQuantityNumber">

                                <div class="formLine2">

                                    <input id="hiddeqty_<?php echo $_smarty_tpl->tpl_vars['section']->value['orderlineid'];?>
" type="hidden" class="hiddeqtyCpt" value="<?php echo $_smarty_tpl->tpl_vars['section']->value['quantity'];?>
"/>

                            <?php if (empty($_smarty_tpl->tpl_vars['section']->value['itemqtydropdown'])) {?>

                                    <input id="itemqty_<?php echo $_smarty_tpl->tpl_vars['section']->value['orderlineid'];?>
" type="number" class="quantityInput" maxlength="8" value="<?php echo $_smarty_tpl->tpl_vars['section']->value['quantity'];?>
" data-decorator="fnUpdateComponentQty" data-trigger="keyup" data-lineid="<?php echo $_smarty_tpl->tpl_vars['section']->value['orderlineid'];?>
" data-itemqty="<?php echo $_smarty_tpl->tpl_vars['section']->value['itemqty'];?>
" />
                                    <img class="refresh" src="<?php echo $_smarty_tpl->tpl_vars['brandroot']->value;?>
/images/icons/refresh.png" alt="<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelOrderUpdateItemTotal');?>
" title="<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelOrderUpdateItemTotal');?>
" data-decorator="fnUpdateComponentQty" data-trigger="click" data-lineid="<?php echo $_smarty_tpl->tpl_vars['section']->value['orderlineid'];?>
" data-itemqty="<?php echo $_smarty_tpl->tpl_vars['section']->value['itemqty'];?>
" />
                                    <div class="clear"></div>

                            <?php } else { ?> 
                                    <select id="itemqty_<?php echo $_smarty_tpl->tpl_vars['section']->value['orderlineid'];?>
" data-decorator="fnUpdateComponentQty" data-trigger="change" data-lineid="<?php echo $_smarty_tpl->tpl_vars['section']->value['orderlineid'];?>
" data-itemqty="<?php echo $_smarty_tpl->tpl_vars['section']->value['itemqty'];?>
" >

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
                                </div>
                                <div class="clear"></div>

                            </div> <!-- itemQuantityNumber -->

                            <div class="clear"></div>

                        </div> <!-- itemSection outerBoxPadding -->

                    <?php }?>

                    </div> <!-- componentContentDetail_XXXXX -->

                </div>

                    <?php if ((sizeof($_smarty_tpl->tpl_vars['section']->value['itemcomponentbuttons']) > 0)) {?>

                <div class="contentChangeBtn outerBoxPadding" data-decorator="fnChangeComponent" data-orderlineid="-1" data-sectionlineid="<?php echo $_smarty_tpl->tpl_vars['section']->value['orderlineid'];?>
">
                    <div class="changeBtnText">
                        <?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelChange');?>

                    </div>
                    <div class="changeBtnImg">
                        <img alt="&gt;" src="<?php echo $_smarty_tpl->tpl_vars['webroot']->value;?>
/images/icons/change-arrow.png" class="navigationArrow">
                    </div>
                    <div class="clear"></div>
                </div>

                    <?php }?> 
                    <?php if ($_smarty_tpl->tpl_vars['section']->value['metadatahtml']) {?>
                <div class="componentMetadata outerBoxPadding metadataId<?php echo $_smarty_tpl->tpl_vars['section']->value['orderlineid'];?>
">
                    <?php echo $_smarty_tpl->tpl_vars['section']->value['metadatahtml'];?>

                </div>
                    <?php }?>

            </div> <!-- componentDetail -->

            <?php if ((sizeof($_smarty_tpl->tpl_vars['section']->value['subsections']) > 0) || (sizeof($_smarty_tpl->tpl_vars['section']->value['checkboxes']) > 0)) {?>

            <div class="contenSubComponent">

                <!-- SUBCOMPONENT -->

                <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['section']->value['subsections'], 'subsection');
$_smarty_tpl->tpl_vars['subsection']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['subsection']->value) {
$_smarty_tpl->tpl_vars['subsection']->do_else = false;
?> 
                    <?php if ($_smarty_tpl->tpl_vars['subsection']->value['showcomponentname'] == true) {?>

                <div id="componentContent_<?php echo $_smarty_tpl->tpl_vars['section']->value['orderlineid'];?>
_<?php echo $_smarty_tpl->tpl_vars['subsection']->value['orderlineid'];?>
" class="contentSubComponentBloc outerBox outerBoxMarginBottom outerBoxMarginTop">
                    <div class="sectionLabel outerBoxPadding">
                        <div class="subcomponentLabel">
                            <?php echo $_smarty_tpl->tpl_vars['subsection']->value['sectionlabel'];?>

                        </div>
                        <div class="subcomponentPrice">
                            <?php echo $_smarty_tpl->tpl_vars['subsection']->value['totalsell'];?>

                        </div>
                        <div class="clear"></div>
                    </diV>

                    <div id="componentContent_<?php echo $_smarty_tpl->tpl_vars['subsection']->value['orderlineid'];?>
">

                        <div id="componentrow_<?php echo $_smarty_tpl->tpl_vars['subsection']->value['orderlineid'];?>
" class="componentBloc outerBoxPadding" >

                        <?php if ($_smarty_tpl->tpl_vars['subsection']->value['haspreview'] > 0) {?>

                            <img class="componentPreview" src="<?php echo htmlspecialchars((string)$_smarty_tpl->tpl_vars['subsection']->value['componentpreviewsrc'], ENT_QUOTES, 'UTF-8', true);?>
" alt=""/>
                            <div class="componentDetailContentText">

                        <?php } else { ?> 
                            <div class="componentDetailContentTextLong">

                        <?php }?> 
                                <div class="componentTitle"><?php echo $_smarty_tpl->tpl_vars['subsection']->value['itemcomponentname'];?>
</div>

                        <?php if (!empty($_smarty_tpl->tpl_vars['subsection']->value['itemcomponentinfo'])) {?>

                                <div class="componentDescription">
                                    <?php echo $_smarty_tpl->tpl_vars['subsection']->value['itemcomponentinfo'];?>

                                </div>

                        <?php }?>
                        <?php if (!empty($_smarty_tpl->tpl_vars['subsection']->value['itemcomponentmoreinfolinkurl'])) {?>

                            <div class="componentDescription">
                              <a href="<?php echo $_smarty_tpl->tpl_vars['subsection']->value['itemcomponentmoreinfolinkurl'];?>
" target="_blank"><?php echo $_smarty_tpl->tpl_vars['subsection']->value['itemcomponentmoreinfolinktext'];?>
</a>
                            </div>

                        <?php }?> 
                        <?php if (!empty($_smarty_tpl->tpl_vars['subsection']->value['itemcomponentpriceinfo'])) {?>

                                <div class="componentDescription">
                                    <?php echo $_smarty_tpl->tpl_vars['subsection']->value['itemcomponentpriceinfo'];?>

                                </div>

                        <?php }?> 
                        <?php if (($_smarty_tpl->tpl_vars['subsection']->value['pricingmodel'] == 7) || ($_smarty_tpl->tpl_vars['subsection']->value['pricingmodel'] == 8)) {?>

                                <ul class="componentList">
                                    <li><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelQuantity');?>
: <span class="componentListNumber"><?php echo $_smarty_tpl->tpl_vars['subsection']->value['quantity'];?>
</span></li>
                                </ul>

                        <?php }?> 
                            </div>
                            <div class="clear"></div>
                        </div>

                        <?php if ($_smarty_tpl->tpl_vars['stage']->value == 'qty') {?>

                            <?php if ((($_smarty_tpl->tpl_vars['subsection']->value['pricingmodel'] == 7) || ($_smarty_tpl->tpl_vars['subsection']->value['pricingmodel'] == 8)) || (sizeof($_smarty_tpl->tpl_vars['subsection']->value['itemcomponentbuttons']) > 0) || ($_smarty_tpl->tpl_vars['subsection']->value['metadatahtml'])) {?>


                        <div class="contentChangeBtn outerBoxPadding" data-decorator="fnSetHashUrl" data-hash-url="subComponentView|-1|<?php echo $_smarty_tpl->tpl_vars['section']->value['orderlineid'];?>
|<?php echo $_smarty_tpl->tpl_vars['subsection']->value['orderlineid'];?>
">
                            <div class="changeBtnText">
                                <?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelOptionsAndExtras');?>


                                <?php if ($_smarty_tpl->tpl_vars['subsection']->value['isonekeywordmandatory'] == true) {?>

                                <img class="valueRequiredImg" src="<?php echo $_smarty_tpl->tpl_vars['brandroot']->value;?>
/images/asterisk.png" alt="*" />

                                <?php }?> 
                            </div>
                            <div class="changeBtnImg">
                                <img class="navigationArrow" src="<?php echo $_smarty_tpl->tpl_vars['webroot']->value;?>
/images/icons/change-arrow.png" alt= ">" />
                            </div>
                            <div class="clear"></div>
                        </div>

                            <?php }?> 
                        <?php }?> 
                    </div> <!-- componentBloc outerBoxPadding -->

                </div>

                    <?php }?> 
                <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?> 
                <!-- END SUBCOMPONENT -->

                <!-- CHECKBOXES INSIDE COMPONENT -->

                <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['section']->value['checkboxes'], 'checkbox');
$_smarty_tpl->tpl_vars['checkbox']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['checkbox']->value) {
$_smarty_tpl->tpl_vars['checkbox']->do_else = false;
?> 
                    <?php if ($_smarty_tpl->tpl_vars['checkbox']->value['showcomponentname'] == true) {?>

                        <?php if (((($_smarty_tpl->tpl_vars['stage']->value == 'payment') && ($_smarty_tpl->tpl_vars['checkbox']->value['checked'] == 1)) || $_smarty_tpl->tpl_vars['stage']->value == 'qty')) {?>

                <div id="componentContent_<?php echo $_smarty_tpl->tpl_vars['section']->value['orderlineid'];?>
_<?php echo $_smarty_tpl->tpl_vars['checkbox']->value['orderlineid'];?>
" class="contentSubComponentBloc outerBox outerBoxMarginBottom outerBoxMarginTop">
                    <div class="sectionLabel outerBoxPadding">
                        <div class="subcomponentLabel">
                            <?php echo $_smarty_tpl->tpl_vars['checkbox']->value['itemcomponentcategoryname'];?>

                        </div>

                            <?php if (($_smarty_tpl->tpl_vars['checkbox']->value['checked']) || ($_smarty_tpl->tpl_vars['checkbox']->value['totalsell'] == $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelNotAvailable'))) {?>

                        <div class="subcomponentPrice">
                            <?php echo $_smarty_tpl->tpl_vars['checkbox']->value['totalsell'];?>

                        </div>

                            <?php }?> 
                        <div class="clear"></div>
                    </diV>

                    <div id="componentContent_<?php echo $_smarty_tpl->tpl_vars['checkbox']->value['orderlineid'];?>
" class="componentBloc outerBoxPadding">

                            <?php if ($_smarty_tpl->tpl_vars['stage']->value == 'qty') {?>

                        <div class="checkboxBloc">

                            <?php } else { ?>

                        <div>

                            <?php }?>

                            <?php if ($_smarty_tpl->tpl_vars['checkbox']->value['haspreview'] > 0) {?>

                            <img class="componentPreview" src="<?php echo htmlspecialchars((string)$_smarty_tpl->tpl_vars['checkbox']->value['componentpreviewsrc'], ENT_QUOTES, 'UTF-8', true);?>
" alt=""/>
                            <div class="componentDetailContentText">

                            <?php } else { ?> 
                            <div class="componentDetailContentTextLong">

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
                            <?php if (!empty($_smarty_tpl->tpl_vars['checkbox']->value['itemcomponentpriceinfo'])) {?>

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
                            <?php if (($_smarty_tpl->tpl_vars['checkbox']->value['pricingmodel'] == 7 || $_smarty_tpl->tpl_vars['checkbox']->value['pricingmodel'] == 8) && $_smarty_tpl->tpl_vars['checkbox']->value['checked'] == 1) {?>

                                <ul class="componentList">
                                    <li><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelQuantity');?>
: <span class="componentListNumber"><?php echo $_smarty_tpl->tpl_vars['checkbox']->value['quantity'];?>
</span></li>
                                </ul>

                            <?php }?> 
                            </div>
                            <div class="clear"></div>
                        </div>

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
" data-trigger="change" <?php if ($_smarty_tpl->tpl_vars['checkbox']->value['checked']) {?>checked="checked"<?php }?> />
                                <label for="onOffSwitch_<?php echo $_smarty_tpl->tpl_vars['checkbox']->value['orderlineid'];?>
" class="onOffSwitchLabel">
                                    <div class="onOffSwitchInner" ontxt="" offtxt=""></div>
                                    <div id="checkbox_<?php echo $_smarty_tpl->tpl_vars['checkbox']->value['orderlineid'];?>
" class="onOffSwitchButton"></div>
                                </label>
                            </div>
                        </div>

                                <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?> 
                            <?php }?> 
                        <div class="clear"></div>
                    </div>
                    <div class="clear"></div>

                            <?php if ($_smarty_tpl->tpl_vars['stage']->value == 'qty') {?>

                                <?php if (($_smarty_tpl->tpl_vars['checkbox']->value['checked'] == 1) && (($_smarty_tpl->tpl_vars['checkbox']->value['metadatahtml']) || ($_smarty_tpl->tpl_vars['checkbox']->value['pricingmodel'] == 7 || $_smarty_tpl->tpl_vars['checkbox']->value['pricingmodel'] == 8))) {?>

                    <div class="contentChangeBtn outerBoxPadding" data-decorator="fnSetHashUrl" data-hash-url="subComponentView|-1|<?php echo $_smarty_tpl->tpl_vars['section']->value['orderlineid'];?>
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
                    </div>

                                <?php }?> 
                            <?php }?> 
                </div>

                        <?php }?>  
                    <?php }?>

                <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?> 
                <!-- END CHECKBOXES INSIDE COMPONENT -->

            </div> <!-- contentSubComponent -->

                <?php }?> 
        </div> <!-- componentDetail_XXXXX -->

                    <?php }?> 
                <?php }?> 
            <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?> 

            <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['orderfootercheckboxes']->value, 'checkbox');
$_smarty_tpl->tpl_vars['checkbox']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['checkbox']->value) {
$_smarty_tpl->tpl_vars['checkbox']->do_else = false;
?>

                <?php if ($_smarty_tpl->tpl_vars['checkbox']->value['showcomponentname'] == true) {?>

                    <?php if (($_smarty_tpl->tpl_vars['checkbox']->value['checked'] == 1) && (($_smarty_tpl->tpl_vars['checkbox']->value['metadatahtml']) || ($_smarty_tpl->tpl_vars['checkbox']->value['pricingmodel'] == 7 || $_smarty_tpl->tpl_vars['checkbox']->value['pricingmodel'] == 8))) {?>

        <div id="componentDetail_<?php echo $_smarty_tpl->tpl_vars['checkbox']->value['orderlineid'];?>
" class="componentDetail">

            <div class="outerBox">

                <div>

                    <div class="sectionLabel outerBoxPadding">
                        <div class="subcomponentLabel">
                            <?php echo $_smarty_tpl->tpl_vars['checkbox']->value['itemcomponentname'];?>

                        </div>
                        <div class="subcomponentPrice">
                            <?php echo $_smarty_tpl->tpl_vars['checkbox']->value['totalsell'];?>

                        </div>
                        <div class="clear"></div>
                    </diV>

                    <div id="componentContentDetail_<?php echo $_smarty_tpl->tpl_vars['checkbox']->value['orderlineid'];?>
">

                        <div id="componentRowDetail_<?php echo $_smarty_tpl->tpl_vars['checkbox']->value['orderlineid'];?>
" class="componentBloc outerBoxPadding" >

                    <?php if ($_smarty_tpl->tpl_vars['checkbox']->value['haspreview'] > 0) {?>

                            <img class="componentPreview" src="<?php echo htmlspecialchars((string)$_smarty_tpl->tpl_vars['checkbox']->value['componentpreviewsrc'], ENT_QUOTES, 'UTF-8', true);?>
" alt=""/>
                            <div class="componentDetailContentText">

                    <?php } else { ?> 
                            <div class="componentDetailContentTextLong">

                    <?php }?> 
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
                    <?php if (!empty($_smarty_tpl->tpl_vars['checkbox']->value['itemcomponentpriceinfo'])) {?>

                                <div class="componentDescription">
                                    <?php echo $_smarty_tpl->tpl_vars['checkbox']->value['itemcomponentpriceinfo'];?>

                                </div>

                    <?php }?> 
                            </div> <!-- componentDetailContentTextLong OR componentDetailContentText -->

                            <div class="clear"></div>

                        </div> <!-- componentBloc outerBoxPadding -->

                    <?php if (($_smarty_tpl->tpl_vars['checkbox']->value['pricingmodel'] == 7) || ($_smarty_tpl->tpl_vars['checkbox']->value['pricingmodel'] == 8)) {?>

                        <div class="itemSection outerBoxPadding">
                            <div class="itemSectionLabel">
                                <div class="formLine1">
                                    <label for="itemqty_<?php echo $_smarty_tpl->tpl_vars['checkbox']->value['orderlineid'];?>
"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelQuantity');?>
</label>
                                </div>
                                <div class="clear"></div>
                            </div>
                            <div class="itemQuantityNumber">

                                <div class="formLine2">

                                    <input id="hiddeqty_<?php echo $_smarty_tpl->tpl_vars['checkbox']->value['orderlineid'];?>
" type="hidden" class="hiddeqtyCpt" value="<?php echo $_smarty_tpl->tpl_vars['checkbox']->value['quantity'];?>
"/>

                        <?php if (empty($_smarty_tpl->tpl_vars['section']->value['itemqtydropdown'])) {?>

                                    <input id="itemqty_<?php echo $_smarty_tpl->tpl_vars['checkbox']->value['orderlineid'];?>
" type="number" class="quantityInput" maxlength="8" value="<?php echo $_smarty_tpl->tpl_vars['checkbox']->value['quantity'];?>
" data-decorator="fnUpdateComponentQty" data-trigger="keyup" data-lineid="<?php echo $_smarty_tpl->tpl_vars['checkbox']->value['orderlineid'];?>
" data-itemqty="<?php echo $_smarty_tpl->tpl_vars['checkbox']->value['itemqty'];?>
" />
                                    <img class="refresh" src="<?php echo $_smarty_tpl->tpl_vars['brandroot']->value;?>
/images/icons/refresh.png" alt="<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelOrderUpdateItemTotal');?>
" title="<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelOrderUpdateItemTotal');?>
" data-decorator="fnUpdateComponentQty" data-trigger="click" data-lineid="<?php echo $_smarty_tpl->tpl_vars['checkbox']->value['orderlineid'];?>
" data-itemqty="<?php echo $_smarty_tpl->tpl_vars['checkbox']->value['itemqty'];?>
" />
                                    <div class="clear"></div>

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
                                </div>
                                <div class="clear"></div>

                            </div> <!-- itemQuantityNumber -->

                            <div class="clear"></div>

                        </div> <!-- itemSection outerBoxPadding -->

                    <?php }?>

                    </div> <!-- componentContentDetail_XXXXX -->

                </div>

                    <?php if ($_smarty_tpl->tpl_vars['checkbox']->value['metadatahtml']) {?>

                <div class="componentMetadata outerBoxPadding metadataId<?php echo $_smarty_tpl->tpl_vars['checkbox']->value['orderlineid'];?>
">
                    <?php echo $_smarty_tpl->tpl_vars['checkbox']->value['metadatahtml'];?>

                </div>

                    <?php }?>

            </div> <!-- componentDetail -->

     </div> <!-- componentDetail_XXXXX -->

                <?php }?>  
            <?php }?> 
        <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>


    <?php }?> 
    </div> <!-- contentVisible -->

</div>  <!-- contentScrollCart -->

<!-- END COMPONENT DETAIL --><?php }
}
