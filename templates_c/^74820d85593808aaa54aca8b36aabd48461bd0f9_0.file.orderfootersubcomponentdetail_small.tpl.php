<?php
/* Smarty version 4.5.3, created on 2026-03-07 07:52:52
  from 'C:\TAOPIX\MediaAlbumWeb\templates\order\orderfootersubcomponentdetail_small.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.5.3',
  'unifunc' => 'content_69abd954b1f934_39355853',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '74820d85593808aaa54aca8b36aabd48461bd0f9' => 
    array (
      0 => 'C:\\TAOPIX\\MediaAlbumWeb\\templates\\order\\orderfootersubcomponentdetail_small.tpl',
      1 => 1729602708,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_69abd954b1f934_39355853 (Smarty_Internal_Template $_smarty_tpl) {
?><div class="contentNavigation" id="contentNavigationOrderFooterSubComponentDetail">
    <div class="btnDoneTop" data-decorator="fnDoneFromComponent" data-subcomponent="true">
       <img class="backImage" src="<?php echo $_smarty_tpl->tpl_vars['webroot']->value;?>
/images/icons/back-arrow.png" alt="<" />
       <div class="btnDone"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ButtonDone');?>
</div>
       <div class="clear"></div>
    </div>
</div>

<div id="contentRightScrollOrderFooterSubComponentDetail" class="contentScrollCart">

    <div class="contentVisible">

        <div class="pageLabel">
            <?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelOptionsAndExtras');?>

        </div>

         <!-- SUBCOMPONENT -->

     <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['orderfootersections']->value, 'section');
$_smarty_tpl->tpl_vars['section']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['section']->value) {
$_smarty_tpl->tpl_vars['section']->do_else = false;
?>

         <?php if ($_smarty_tpl->tpl_vars['section']->value['showcomponentname'] == true) {?>

             <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['section']->value['subsections'], 'subsection');
$_smarty_tpl->tpl_vars['subsection']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['subsection']->value) {
$_smarty_tpl->tpl_vars['subsection']->do_else = false;
?>

                 <?php if ($_smarty_tpl->tpl_vars['subsection']->value['showcomponentname'] == true) {?>

                     <?php if ((($_smarty_tpl->tpl_vars['subsection']->value['pricingmodel'] == 7) || ($_smarty_tpl->tpl_vars['subsection']->value['pricingmodel'] == 8)) || (sizeof($_smarty_tpl->tpl_vars['subsection']->value['itemcomponentbuttons']) > 0) || ($_smarty_tpl->tpl_vars['subsection']->value['metadatahtml'])) {?>

         <div class="componentDetail outerBoxMarginBottom outerBox" id="subcomponentDetail_<?php echo $_smarty_tpl->tpl_vars['subsection']->value['orderlineid'];?>
" style="display:none;">

             <div>

                 <div class="sectionLabel outerBoxPadding">
                     <div class="subcomponentLabel">
                         <?php echo $_smarty_tpl->tpl_vars['subsection']->value['sectionlabel'];?>

                     </div>
                     <div class="subcomponentPrice">
                         <?php echo $_smarty_tpl->tpl_vars['subsection']->value['totalsell'];?>

                     </div>
                     <div class="clear"></div>
                 </diV>

                 <div>

                     <div id="componentRowDetail_<?php echo $_smarty_tpl->tpl_vars['subsection']->value['orderlineid'];?>
" class="componentBloc outerBoxPadding" >

                         <?php if ($_smarty_tpl->tpl_vars['subsection']->value['haspreview'] > 0) {?>

                         <img class="componentPreview" src="<?php echo htmlspecialchars((string)$_smarty_tpl->tpl_vars['subsection']->value['componentpreviewsrc'], ENT_QUOTES, 'UTF-8', true);?>
" alt=""/>
                         <div class="componentDetailContentText">

                         <?php } else { ?> 
                         <div class="componentDetailContentTextLong">

                         <?php }?> 
                         <?php if (!empty($_smarty_tpl->tpl_vars['subsection']->value['info'])) {?>

                             <div class="componentDescription">
                                 <?php echo $_smarty_tpl->tpl_vars['subsection']->value['itemcomponentinfo'];?>

                             </div>

                         <?php }?>
                         <?php if (!empty($_smarty_tpl->tpl_vars['subsection']->value['itemcomponentpriceinfo'])) {?>

                             <div class="componentDescription">
                                 <?php echo $_smarty_tpl->tpl_vars['subsection']->value['itemcomponentpriceinfo'];?>

                             </div>

                         <?php }?> 
                         </div> <!-- componentDetailContentTextLong OR componentDetailContentText -->

                         <div class="clear"></div>

                     </div> <!-- componentBloc outerBoxPadding -->

                     <?php if (($_smarty_tpl->tpl_vars['subsection']->value['pricingmodel'] == 7) || ($_smarty_tpl->tpl_vars['subsection']->value['pricingmodel'] == 8)) {?>

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

                                <input id="hiddeqty_<?php echo $_smarty_tpl->tpl_vars['subsection']->value['orderlineid'];?>
" type="hidden" class="hiddeqtyCpt" value="<?php echo $_smarty_tpl->tpl_vars['subsection']->value['quantity'];?>
"/>

                        <?php if (empty($_smarty_tpl->tpl_vars['subsection']->value['itemqtydropdown'])) {?>

                                <input id="itemqty_<?php echo $_smarty_tpl->tpl_vars['subsection']->value['orderlineid'];?>
" type="number" class="quantityInput" maxlength="8" value="<?php echo $_smarty_tpl->tpl_vars['subsection']->value['quantity'];?>
"  data-decorator="fnUpdateComponentQty" data-trigger="keyup" data-lineid="<?php echo $_smarty_tpl->tpl_vars['subsection']->value['orderlineid'];?>
" data-itemqty="<?php echo $_smarty_tpl->tpl_vars['subsection']->value['itemqty'];?>
" />
                                <img class="refresh" src="<?php echo $_smarty_tpl->tpl_vars['brandroot']->value;?>
/images/icons/refresh.png" alt="<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelOrderUpdateItemTotal');?>
" title="<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelOrderUpdateItemTotal');?>
" data-decorator="fnUpdateComponentQty" data-trigger="click" data-lineid="<?php echo $_smarty_tpl->tpl_vars['subsection']->value['orderlineid'];?>
" data-itemqty="<?php echo $_smarty_tpl->tpl_vars['subsection']->value['itemqty'];?>
" />
                                <div class="clear"></div>

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
                            </div>
                            <div class="clear"></div>

                         </div> <!-- itemQuantityNumber -->

                         <div class="clear"></div>

                     </div> <!-- itemSection outerBoxPadding -->

                     <?php }?>

                 </div>

                <?php if ((sizeof($_smarty_tpl->tpl_vars['subsection']->value['itemcomponentbuttons']) > 0)) {?>

                <div class="contentChangeBtn outerBoxPadding" data-decorator="fnChangeComponent" data-orderlineid="-1" data-sectionlineid="<?php echo $_smarty_tpl->tpl_vars['subsection']->value['orderlineid'];?>
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
                 <?php if ($_smarty_tpl->tpl_vars['subsection']->value['metadatahtml']) {?>

                 <div class="componentMetadata outerBoxPadding metadataId<?php echo $_smarty_tpl->tpl_vars['section']->value['orderlineid'];?>
_<?php echo $_smarty_tpl->tpl_vars['subsection']->value['orderlineid'];?>
">
                     <?php echo $_smarty_tpl->tpl_vars['subsection']->value['metadatahtml'];?>

                 </div>

                 <?php }?>

             </div> <!-- componentContentDetail_XXXXX -->

         </div>

             <?php }?>

         <?php }?>

     <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>

         <!-- END SUBCOMPONENT -->

         <!-- CHECKBOXES -->

     <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['section']->value['checkboxes'], 'checkbox');
$_smarty_tpl->tpl_vars['checkbox']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['checkbox']->value) {
$_smarty_tpl->tpl_vars['checkbox']->do_else = false;
?>

         <?php if ($_smarty_tpl->tpl_vars['checkbox']->value['showcomponentname'] == true) {?>

             <?php if (($_smarty_tpl->tpl_vars['checkbox']->value['checked'] == 1) && (($_smarty_tpl->tpl_vars['checkbox']->value['metadatahtml']) || ($_smarty_tpl->tpl_vars['checkbox']->value['pricingmodel'] == 7 || $_smarty_tpl->tpl_vars['checkbox']->value['pricingmodel'] == 8))) {?>

         <div class="componentDetail outerBox outerBoxMarginBottom" id="subcomponentDetail_<?php echo $_smarty_tpl->tpl_vars['checkbox']->value['orderlineid'];?>
" style="display:none;">

             <div class="sectionLabel outerBoxPadding">
                 <div class="subcomponentLabel">
                     <?php echo $_smarty_tpl->tpl_vars['checkbox']->value['sectionlabel'];?>

                 </div>
                 <div class="subcomponentPrice">
                     <?php echo $_smarty_tpl->tpl_vars['checkbox']->value['totalsell'];?>

                 </div>
                 <div class="clear"></div>
             </diV>

             <div>

                 <div id="componentRowDetail_<?php echo $_smarty_tpl->tpl_vars['checkbox']->value['orderlineid'];?>
" class="componentBloc outerBoxPadding" >

                     <?php if ($_smarty_tpl->tpl_vars['checkbox']->value['haspreview'] > 0) {?>

                     <img class="componentPreview" src="<?php echo htmlspecialchars((string)$_smarty_tpl->tpl_vars['checkbox']->value['componentpreviewsrc'], ENT_QUOTES, 'UTF-8', true);?>
" alt=""/>
                     <div class="componentDetailContentText">

                     <?php } else { ?> 
                     <div class="componentDetailContentTextLong">

                     <?php }?> 
                     <?php if (!empty($_smarty_tpl->tpl_vars['checkbox']->value['info'])) {?>

                         <div class="componentDescription">
                             <?php echo $_smarty_tpl->tpl_vars['checkbox']->value['itemcomponentinfo'];?>

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

                    <?php if (empty($_smarty_tpl->tpl_vars['checkbox']->value['itemqtydropdown'])) {?>

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

                 <?php if ($_smarty_tpl->tpl_vars['checkbox']->value['metadatahtml']) {?>

                 <div class="componentMetadata outerBoxPadding metadataId<?php echo $_smarty_tpl->tpl_vars['section']->value['orderlineid'];?>
_<?php echo $_smarty_tpl->tpl_vars['checkbox']->value['orderlineid'];?>
">
                     <?php echo $_smarty_tpl->tpl_vars['checkbox']->value['metadatahtml'];?>

                 </div>

                 <?php }?>

             </div> <!-- componentContentDetail_XXXXX -->

         </div>

                     <?php }?>

                 <?php }?>

             <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>

             <!-- END CHECKBOXES -->

         <?php }?>

     <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>

    </div> <!-- contentVisible -->

</div>  <!-- contentScrollCart --><?php }
}
