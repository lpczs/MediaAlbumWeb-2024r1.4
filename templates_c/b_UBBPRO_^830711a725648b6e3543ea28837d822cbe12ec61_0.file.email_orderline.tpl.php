<?php
/* Smarty version 4.5.3, created on 2026-03-14 01:35:16
  from 'C:\TAOPIX\MediaAlbumWeb\Branding\\ubbpro\email\customer_orderconfirmation3\email_orderline.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.5.3',
  'unifunc' => 'content_69b4bb54a349b2_94456889',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '830711a725648b6e3543ea28837d822cbe12ec61' => 
    array (
      0 => 'C:\\TAOPIX\\MediaAlbumWeb\\Branding\\\\ubbpro\\email\\customer_orderconfirmation3\\email_orderline.tpl',
      1 => 1653710194,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_69b4bb54a349b2_94456889 (Smarty_Internal_Template $_smarty_tpl) {
if ($_smarty_tpl->tpl_vars['orderline']->value['orderlineid'] != -1) {?>
    <span class="txt_linebreak"></span>

    <div class="order-line">    
    
        <table width="600" style="margin-bottom:10px; border: solid 1px white; font-size: 12px; color: #575757; font-family: Lucida Grande;">
            <tr style="color: #575757; font-family: Lucida Grande;">
                <?php if ($_smarty_tpl->tpl_vars['orderline']->value['assetrequest'] != '' && $_smarty_tpl->tpl_vars['thumbnailtype']->value != 'none' && $_smarty_tpl->tpl_vars['showthumbnail']->value != 0) {?>
                    <td valign="top" bgcolor="#FFFFFF" height="65" width="75" rowspan="2" style="word-break:break-all; padding: 10px;">
                        <img class="componentPreview"  src="<?php echo htmlspecialchars((string)$_smarty_tpl->tpl_vars['orderline']->value['assetrequest'], ENT_QUOTES, 'UTF-8', true);?>
" height="65" width="75">
                    </td>

                    <td bgcolor="#FFFFFF" style="word-break:break-all; padding: 10px;" width="200">
                        <?php echo $_smarty_tpl->tpl_vars['orderline']->value['itemproductname'];?>

                    </td>
                    <td bgcolor="#FFFFFF" style="word-break:break-all; padding: 10px;" width="130">
                        <?php echo $_smarty_tpl->tpl_vars['orderline']->value['itemqty'];?>

                    </td>
                    <td bgcolor="#FFFFFF" align="right" style="word-break:break-all;  padding: 10px;" width="130">
                        <?php echo $_smarty_tpl->tpl_vars['orderline']->value['itemproducttotalsell'];?>

                    </td>
                <?php } else { ?>
                    <td style="word-break:break-all; padding: 10px;" width="295">
                        <?php echo $_smarty_tpl->tpl_vars['orderline']->value['itemproductname'];?>

                    </td>
                    <td style="word-break:break-all; padding: 10px;" width="130">
                        <?php echo $_smarty_tpl->tpl_vars['orderline']->value['itemqty'];?>

                    </td>
                    <td align="right" style="word-break:break-all; padding: 10px;" width="130">
                        <?php echo $_smarty_tpl->tpl_vars['orderline']->value['itemproducttotalsell'];?>

                    </td>
                <?php }?>
            </tr>
            <tr style="color: #575757; font-family: Lucida Grande;">
                <td style="word-break:break-all;  padding: 10px;" colspan="3">
                    <?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelProjectName');?>
: <?php echo $_smarty_tpl->tpl_vars['orderline']->value['projectname'];?>

                </td>
            </tr>
        </table>
                
        <table width="600" style="margin-bottom: 10px; width: 600px; border: solid 1px white; font-size: 12px; color: #575757; font-family: Lucida Grande;">
        <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['orderline']->value['itemexternalassets'], 'asset');
$_smarty_tpl->tpl_vars['asset']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['asset']->value) {
$_smarty_tpl->tpl_vars['asset']->do_else = false;
?>
            <?php if ($_smarty_tpl->tpl_vars['orderline']->value['displayassets'] == true) {?>
                <tr style="color: #575757; font-family: Lucida Grande;">
                    <td style="word-break:break-all; padding: 10px;" width="295">
                         <?php echo $_smarty_tpl->tpl_vars['asset']->value['pagename'];?>
: <?php echo $_smarty_tpl->tpl_vars['asset']->value['name'];?>
 
                    </td>
                    <td width="130" style="word-break:break-all; padding: 10px;">
                    </td>
                    <td  width="130" style="word-break:break-all; padding: 10px;" align="right">
                         <?php echo $_smarty_tpl->tpl_vars['asset']->value['charge'];?>

                    </td>
                </tr>
            <?php }?>
        <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
        </table>
                
        <!-- Single print start -->
        <table width="600" style="margin-bottom: 10px; border: solid 1px white; font-size: 12px; color: #575757; font-family: Lucida Grande;">
        <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['orderline']->value['itempictures'], 'sizegroup');
$_smarty_tpl->tpl_vars['sizegroup']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['sizegroup']->value) {
$_smarty_tpl->tpl_vars['sizegroup']->do_else = false;
?>
            <tr style="color: #575757; font-family: Lucida Grande;">
                <td style="background-color:#e8e9ed; word-break:break-all; padding: 10px;" width="535" colspan="3">
                    <?php echo $_smarty_tpl->tpl_vars['sizegroup']->value['groupdisplayname'];?>

                </td>
            </tr>
            <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['sizegroup']->value['pictures'], 'picture');
$_smarty_tpl->tpl_vars['picture']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['picture']->value) {
$_smarty_tpl->tpl_vars['picture']->do_else = false;
?>
                <tr style="color: #575757; font-family: Lucida Grande;">
                    <td style="word-break:break-all; padding: 10px;" width="295">
                        <?php echo $_smarty_tpl->tpl_vars['picture']->value['picturename'];?>

                    </td>
                    <td style="word-break:break-all; padding: 10px;" width="130">
                        <?php echo $_smarty_tpl->tpl_vars['picture']->value['componentqty'];?>

                    </td>
                    <td style="word-break:break-all; padding: 10px;" width="130" align="right">
                        <?php echo $_smarty_tpl->tpl_vars['picture']->value['totalsell'];?>

                    </td>
                </tr>
            <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>    
            <tr style="color: #575757; font-family: Lucida Grande;">
                <td style="word-break:break-all; padding: 10px;" align="right" colspan="3">
                    <span style="font-weight:bold; color:#666666"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelSubTotal');?>
: </span>
                    <span style="color:#8C4D4E"><?php echo $_smarty_tpl->tpl_vars['sizegroup']->value['formatedgrouptotalsell'];?>
</span>
                </td>
            </tr>
        <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
        </table>    
        
        
        <!-- checkboxes start -->
        <?php if ($_smarty_tpl->tpl_vars['orderline']->value['orderlineid'] != -1) {?>
            <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['orderline']->value['checkboxes'], 'checkbox');
$_smarty_tpl->tpl_vars['checkbox']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['checkbox']->value) {
$_smarty_tpl->tpl_vars['checkbox']->do_else = false;
?>
                <?php if ($_smarty_tpl->tpl_vars['checkbox']->value['showcomponentname'] == true) {?>
                    <?php if ($_smarty_tpl->tpl_vars['checkbox']->value['checked']) {?>
                        <table width="600" style="margin-bottom: 10px; border: solid 1px white; font-size: 12px; color: #575757; font-family: Lucida Grande;">
                            <tr style="color: #575757; font-family: Lucida Grande;">
                                <?php if ($_smarty_tpl->tpl_vars['checkbox']->value['haspreview'] > 0 && $_smarty_tpl->tpl_vars['showthumbnail']->value != 0) {?>
                                    <td valign="top" height="65" width="75" rowspan="2" style="word-break:break-all; padding: 10px;">
                                        <img class="componentPreview"  src="<?php echo htmlspecialchars((string)$_smarty_tpl->tpl_vars['checkbox']->value['componentpreviewsrc'], ENT_QUOTES, 'UTF-8', true);?>
" height="65" width="75">
                                    </td>

                                    <td style="word-break:break-all; padding: 10px;" width="200">
                                        <?php echo $_smarty_tpl->tpl_vars['checkbox']->value['itemcomponentprompt'];?>
 - <?php echo $_smarty_tpl->tpl_vars['checkbox']->value['itemcomponentname'];?>

                                    </td>
                                    <td style="word-break:break-all; padding: 10px;" width="130">
                                        <?php echo $_smarty_tpl->tpl_vars['checkbox']->value['quantity'];?>

                                    </td>
                                    <td align="right" style="word-break:break-all; padding: 10px;" width="130">
                                        <?php echo $_smarty_tpl->tpl_vars['checkbox']->value['totalsell'];?>

                                    </td>
                                <?php } else { ?>
                                    <td style="word-break:break-all; padding: 10px;" width="295">
                                        <?php echo $_smarty_tpl->tpl_vars['checkbox']->value['itemcomponentprompt'];?>
 - <?php echo $_smarty_tpl->tpl_vars['checkbox']->value['itemcomponentname'];?>

                                    </td>
                                    <td style="word-break:break-all; padding: 10px;" width="130">
                                        <?php echo $_smarty_tpl->tpl_vars['checkbox']->value['quantity'];?>

                                    </td>
                                    <td align="right" style="word-break:break-all; padding: 10px;" width="130">
                                        <?php echo $_smarty_tpl->tpl_vars['checkbox']->value['totalsell'];?>

                                    </td>
                                <?php }?>
                            </tr>
                            <?php if (($_smarty_tpl->tpl_vars['checkbox']->value['itemcomponentinfo'] != '' || $_smarty_tpl->tpl_vars['checkbox']->value['itemcomponentpriceinfo'] != '')) {?>
                                <tr>
                                    <?php if ($_smarty_tpl->tpl_vars['checkbox']->value['haspreview'] > 0 && $_smarty_tpl->tpl_vars['showthumbnail']->value != 0) {?>
                                        <td colspan="3" width="460" style="word-break:break-all; padding: 10px;">
                                    <?php } else { ?>
                                        <td colspan="3" width="535" style="word-break:break-all; padding: 10px;">
                                    <?php }?>
                                            <?php if ($_smarty_tpl->tpl_vars['checkbox']->value['itemcomponentinfo'] != '') {?>
                                                <div class="checkbox-info">
                                                    <span class="txt_itemcomponentinfo"><?php echo $_smarty_tpl->tpl_vars['checkbox']->value['itemcomponentinfo'];?>
</span>
                                                </div>
                                            <?php }?>
                                            <?php if ($_smarty_tpl->tpl_vars['checkbox']->value['itemcomponentpriceinfo'] != '') {?>
                                                <div class="checkbox-info">
                                                    <span class="txt_itemcomponentpriceinfo"><?php echo $_smarty_tpl->tpl_vars['checkbox']->value['itemcomponentpriceinfo'];?>
</span>
                                                </div>
                                            <?php }?>
                                        </td>
                                </tr>
                            <?php }?>
                        </table>

                        <?php if ($_smarty_tpl->tpl_vars['checkbox']->value['metadatahtml']) {?>
                            <table width="600" style="border: solid 1px white; font-size: 12px; color: #575757; font-family: Lucida Grande;">
                                <tr>
                                    <td width="600" style="word-break:break-all; padding: 10px;">
                                        <span class="component-metadata<?php if (!$_smarty_tpl->tpl_vars['checkbox']->value['checked']) {?> invisible<?php }?>"><?php echo $_smarty_tpl->tpl_vars['checkbox']->value['metadatahtml'];?>
</span>
                                    </td>
                                </tr>

                            </table>
                        <?php }?>
                    <?php }?>
                <?php }?>
            <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
        <?php }?>
        <!-- checkboxes end -->
        
        
        
        
        <!-- sections start -->
        <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['orderline']->value['sections'], 'section');
$_smarty_tpl->tpl_vars['section']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['section']->value) {
$_smarty_tpl->tpl_vars['section']->do_else = false;
?>         <?php if ($_smarty_tpl->tpl_vars['section']->value['showcomponentname'] == true) {?>
            <table width="600" style="margin-bottom:10px; border: solid 1px white; font-size: 12px; color: #575757; font-family: Lucida Grande;">
                <tr style="color: #575757; font-family: Lucida Grande;">
                    <?php if ($_smarty_tpl->tpl_vars['section']->value['haspreview'] > 0 && $_smarty_tpl->tpl_vars['showthumbnail']->value != 0) {?>
                        <td valign="top" bgcolor="#FFFFFF" height="65" width="75" rowspan="2" style="word-break:break-all; padding: 10px;">
                            <img class="productpreview-container"  src="<?php echo htmlspecialchars((string)$_smarty_tpl->tpl_vars['section']->value['componentpreviewsrc'], ENT_QUOTES, 'UTF-8', true);?>
" height="65" width="75">
                        </td>

                        <td valign="top" bgcolor="#FFFFFF" style="word-break:break-all; padding: 10px;" width="200">
                            <span class="txt_componentname">
                            <?php if ($_smarty_tpl->tpl_vars['section']->value['count'] <= 1 || $_smarty_tpl->tpl_vars['section']->value['prompt'] == '') {?>
                                <span class="section-title"><?php echo $_smarty_tpl->tpl_vars['section']->value['sectionlabel'];?>
</span> <span> - </span>
                                <span><?php echo $_smarty_tpl->tpl_vars['section']->value['itemcomponentname'];?>
</span>
                            <?php } else { ?>
                                <span class="section-title"><?php echo $_smarty_tpl->tpl_vars['section']->value['sectionlabel'];?>
</span>
                                <br>
                                <span><?php echo $_smarty_tpl->tpl_vars['section']->value['itemcomponentname'];?>
</span>
                            <?php }?>
                            </span>
                        </td>
                        <td valign="top" bgcolor="#FFFFFF" style="word-break:break-all; padding: 10px;" width="130">
                            <?php if ($_smarty_tpl->tpl_vars['section']->value['pricingmodel'] == 7 || $_smarty_tpl->tpl_vars['section']->value['pricingmodel'] == 8) {?>
                                <div class="paymentcomponentquantity">
                                    <span class="txt_qty"><?php echo $_smarty_tpl->tpl_vars['section']->value['quantity'];?>
</span> 
                                </div>
                            <?php }?>
                        </td>
                        <td valign="top" bgcolor="#FFFFFF" align="right" style="word-break:break-all; padding: 10px;" width="130">
                            <span class="component-price"><span class="txt_price"><?php echo $_smarty_tpl->tpl_vars['section']->value['totalsell'];?>
</span></span>
                        </td>
                    <?php } else { ?>
                        <td valign="top" style="word-break:break-all; padding: 10px;" width="295">
                            <span class="txt_componentname">
                            <?php if ($_smarty_tpl->tpl_vars['section']->value['count'] <= 1 || $_smarty_tpl->tpl_vars['section']->value['prompt'] == '') {?>
                                <span class="section-title"><?php echo $_smarty_tpl->tpl_vars['section']->value['sectionlabel'];?>
</span> <span> - </span>
                                <span><?php echo $_smarty_tpl->tpl_vars['section']->value['itemcomponentname'];?>
</span>
                            <?php } else { ?>
                                <span class="section-title"><?php echo $_smarty_tpl->tpl_vars['section']->value['sectionlabel'];?>
</span>
                                <br>
                                <span><?php echo $_smarty_tpl->tpl_vars['section']->value['itemcomponentname'];?>
</span>
                            <?php }?>
                            </span>
                        </td>
                        <td valign="top" style="word-break:break-all;" width="130">
                            <?php if ($_smarty_tpl->tpl_vars['section']->value['pricingmodel'] == 7 || $_smarty_tpl->tpl_vars['section']->value['pricingmodel'] == 8) {?>
                                <div class="paymentcomponentquantity">
                                    <span class="txt_qty"><?php echo $_smarty_tpl->tpl_vars['section']->value['quantity'];?>
</span> 
                                </div>
                            <?php }?>
                        </td>
                        <td valign="top" align="right" style="word-break:break-all; padding: 10px;" width="130">
                            <span class="component-price"><span class="txt_price"><?php echo $_smarty_tpl->tpl_vars['section']->value['totalsell'];?>
</span></span>
                        </td>
                    <?php }?>
                </tr>
                
                <?php if (($_smarty_tpl->tpl_vars['section']->value['itemcomponentinfo'] != '' || $_smarty_tpl->tpl_vars['section']->value['itemcomponentpriceinfo'] != '')) {?>
                <tr>
                    <?php if ($_smarty_tpl->tpl_vars['section']->value['haspreview'] > 0 && $_smarty_tpl->tpl_vars['showthumbnail']->value != 0) {?>
                        <td colspan="3" width="500" style="word-break:break-all; padding: 10px;">
                    <?php } else { ?>
                        <td colspan="3" width="500" style="word-break:break-all; padding: 10px;">
                    <?php }?>
                    <?php if ($_smarty_tpl->tpl_vars['section']->value['itemcomponentinfo'] != '') {?>
                        <div class="section-info" style="word-break:break-all; padding: 10px;">
                            <span class="txt_itemcomponentinfo"><?php echo $_smarty_tpl->tpl_vars['section']->value['itemcomponentinfo'];?>
</span>
                        </div>
                    <?php }?>
                    <?php if ($_smarty_tpl->tpl_vars['section']->value['itemcomponentpriceinfo'] != '') {?>
                        <div class="section-info" style="word-break:break-all; padding: 10px;">
                            <span class="txt_itemcomponentpriceinfo"><?php echo $_smarty_tpl->tpl_vars['section']->value['itemcomponentpriceinfo'];?>
</span>
                        </div>
                    <?php }?>
                    </td>
                </tr>
                <?php }?>
            </table>
        
            <?php if ($_smarty_tpl->tpl_vars['section']->value['metadatahtml']) {?>
                <span class="component-metadata"><?php echo $_smarty_tpl->tpl_vars['section']->value['metadatahtml'];?>
</span>
            <?php }?>
        
        <?php }?>
        
    <!-- sub-sections of component start -->
    <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['section']->value['subsections'], 'subsection');
$_smarty_tpl->tpl_vars['subsection']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['subsection']->value) {
$_smarty_tpl->tpl_vars['subsection']->do_else = false;
?>         <?php if ($_smarty_tpl->tpl_vars['subsection']->value['showcomponentname'] == true) {?>
            <table width="600" style="margin-bottom: 10px; border: solid 1px white; font-size: 12px; color: #575757; font-family: Lucida Grande;">
                <tr style="color: #575757; font-family: Lucida Grande;">
                    <?php if ($_smarty_tpl->tpl_vars['subsection']->value['haspreview'] > 0 && $_smarty_tpl->tpl_vars['showthumbnail']->value != 0) {?>
                        <td valign="top" height="65" width="75" rowspan="2" style="word-break:break-all; padding: 10px;">
                            <img class="componentPreview"  src="<?php echo htmlspecialchars((string)$_smarty_tpl->tpl_vars['subsection']->value['componentpreviewsrc'], ENT_QUOTES, 'UTF-8', true);?>
" height="65" width="75">
                        </td>

                        <td style="word-break:break-all; padding: 10px;" width="200" >
                            <span class="txt_subcomponentname">
                                <?php if ($_smarty_tpl->tpl_vars['subsection']->value['count'] <= 1 || $_smarty_tpl->tpl_vars['subsection']->value['prompt'] == '') {?>
                                    <span class="section-title"><?php echo $_smarty_tpl->tpl_vars['subsection']->value['sectionlabel'];?>
</span><span> - </span>
                                    <span><?php echo $_smarty_tpl->tpl_vars['subsection']->value['itemcomponentname'];?>
</span>
                                <?php } else { ?>
                                    <span class="section-title"><?php echo $_smarty_tpl->tpl_vars['subsection']->value['sectionlabel'];?>
</span>
                                    <br>
                                    <span><?php echo $_smarty_tpl->tpl_vars['subsection']->value['itemcomponentname'];?>
</span>
                                <?php }?>
                            </span>
                        </td>
                        <td style="word-break:break-all; padding: 10px;" width="130">
                            <?php if ($_smarty_tpl->tpl_vars['subsection']->value['pricingmodel'] == 7 || $_smarty_tpl->tpl_vars['subsection']->value['pricingmodel'] == 8) {?>
                                <div class="paymentsubcomponentquantity">
                                    <span class="txt_subqty"><?php echo $_smarty_tpl->tpl_vars['subsection']->value['quantity'];?>
</span>
                                </div>
                            <?php }?>
                        </td>
                        <td align="right" style="word-break:break-all; padding: 10px;" width="130">
                            <span class="component-price"><span class="txt_price"><?php echo $_smarty_tpl->tpl_vars['subsection']->value['totalsell'];?>
</span></span>
                        </td>
                    <?php } else { ?>
                        <td style="word-break:break-all; padding: 10px;" width="295">
                            <span class="txt_subcomponentname">
                                <?php if ($_smarty_tpl->tpl_vars['subsection']->value['count'] <= 1 || $_smarty_tpl->tpl_vars['subsection']->value['prompt'] == '') {?>
                                    <span class="section-title"><?php echo $_smarty_tpl->tpl_vars['subsection']->value['sectionlabel'];?>
</span><span> - </span>
                                    <span><?php echo $_smarty_tpl->tpl_vars['subsection']->value['itemcomponentname'];?>
</span>
                                <?php } else { ?>
                                    <span class="section-title"><?php echo $_smarty_tpl->tpl_vars['subsection']->value['sectionlabel'];?>
</span>
                                    <br>
                                    <span><?php echo $_smarty_tpl->tpl_vars['subsection']->value['itemcomponentname'];?>
</span>
                                <?php }?>
                            </span>
                        </td>
                        <td style="word-break:break-all;padding: 10px;" width="130">
                            <?php if ($_smarty_tpl->tpl_vars['subsection']->value['pricingmodel'] == 7 || $_smarty_tpl->tpl_vars['subsection']->value['pricingmodel'] == 8) {?>
                                <div class="paymentsubcomponentquantity">
                                    <span class="txt_subqty"><?php echo $_smarty_tpl->tpl_vars['subsection']->value['quantity'];?>
</span>
                                </div>
                            <?php }?>
                        </td>
                        <td align="right" style="word-break:break-all; padding: 10px;" width="130">
                            <span class="component-price"><span class="txt_price"><?php echo $_smarty_tpl->tpl_vars['subsection']->value['totalsell'];?>
</span></span>
                        </td>
                    <?php }?>

                </tr>
                 <?php if (($_smarty_tpl->tpl_vars['subsection']->value['itemcomponentinfo'] != '' || $_smarty_tpl->tpl_vars['subsection']->value['itemcomponentpriceinfo'] != '')) {?>
                <tr>
                    <?php if ($_smarty_tpl->tpl_vars['subsection']->value['haspreview'] > 0 && $_smarty_tpl->tpl_vars['showthumbnail']->value != 0) {?>
                        <td colspan="3" width="500" style="word-break:break-all; padding: 10px;">
                    <?php } else { ?>
                     <td colspan="3" width="600" style="word-break:break-all; padding: 10px;">
                    <?php }?>
                        <?php if ($_smarty_tpl->tpl_vars['subsection']->value['itemcomponentinfo'] != '') {?>
                            <div class="section-info">
                                <span class="txt_itemsubcomponentinfo"><?php echo $_smarty_tpl->tpl_vars['subsection']->value['itemcomponentinfo'];?>
</span>
                            </div>
                        <?php }?>
                        <?php if ($_smarty_tpl->tpl_vars['subsection']->value['itemcomponentpriceinfo'] != '') {?>
                            <div class="section-info">
                                <span class="txt_itemsubcomponentpriceinfo"><?php echo $_smarty_tpl->tpl_vars['subsection']->value['itemcomponentpriceinfo'];?>
</span>
                            </div>
                        <?php }?>
                    </td>
                </tr>
                <?php }?>
            </table>

        <?php if ($_smarty_tpl->tpl_vars['subsection']->value['metadatahtml']) {?>
            <span id="metadatarow_<?php echo $_smarty_tpl->tpl_vars['subsection']->value['orderlineid'];?>
" class="component-metadata">
                <span class="txt_subComponentMetaData">
                    <?php echo $_smarty_tpl->tpl_vars['subsection']->value['metadatahtml'];?>

                </span>
            </span>
        <?php }?>
    <?php }?>
    <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?> 
    <!-- sub-sections of component end -->

    <div class="clear"></div>    
        
    <!-- checkboxes inside component start-->
    <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['section']->value['checkboxes'], 'checkbox');
$_smarty_tpl->tpl_vars['checkbox']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['checkbox']->value) {
$_smarty_tpl->tpl_vars['checkbox']->do_else = false;
?>         <?php if ($_smarty_tpl->tpl_vars['checkbox']->value['showcomponentname'] == true) {?>
            <?php if ($_smarty_tpl->tpl_vars['checkbox']->value['checked'] == 1) {?>
                <table width="600" style="margin-Bottom:10px; border: solid 1px white; font-size: 12px; color: #575757; font-family: Lucida Grande;">
                    <tr style="color: #575757; font-family: Lucida Grande;">
                        <?php if ($_smarty_tpl->tpl_vars['checkbox']->value['haspreview'] > 0 && $_smarty_tpl->tpl_vars['showthumbnail']->value != 0) {?>
                            <td bgcolor="#FFFFFF" valign="top" height="65" width="75" rowspan="2" style="word-break:break-all; padding: 10px;">
                                <img class="componentPreview"  src="<?php echo $_smarty_tpl->tpl_vars['checkbox']->value['componentpreviewsrc'];?>
" height="65" width="75">
                            </td>

                            <td valign="top" bgcolor="#FFFFFF" style="word-break:break-all; padding: 10px;" width="200">
                                <span class="txt_subcomponentname">
                                    <span class="component-name"><?php echo $_smarty_tpl->tpl_vars['checkbox']->value['itemcomponentprompt'];?>
</span> 
                                    <span> - <?php echo $_smarty_tpl->tpl_vars['checkbox']->value['itemcomponentname'];?>
</span>
                                </span>
                            </td>
                            <td valign="top" bgcolor="#FFFFFF" style="word-break:break-all; padding: 10px;" width="130">
                                <?php if (($_smarty_tpl->tpl_vars['checkbox']->value['pricingmodel'] == 7 || $_smarty_tpl->tpl_vars['checkbox']->value['pricingmodel'] == 8)) {?>
                                    <div class="paymentsubcomponentquantity">
                                        <span class="txt_subqty"><?php echo $_smarty_tpl->tpl_vars['checkbox']->value['quantity'];?>
</span>
                                    </div>
                                <?php }?>
                            </td>
                            <td valign="top" bgcolor="#FFFFFF" align="right" style="word-break:break-all; padding: 10px;" width="130">
                                <span class="component-price"><span class="txt_price"><?php echo $_smarty_tpl->tpl_vars['checkbox']->value['totalsell'];?>
</span></span>
                            </td>
                        <?php } else { ?>
                            <td valign="top" style="word-break:break-all; padding: 10px;" width="295">
                                <span class="txt_subcomponentname">
                                    <span class="component-name"><?php echo $_smarty_tpl->tpl_vars['checkbox']->value['itemcomponentprompt'];?>
</span> 
                                    <span> - <?php echo $_smarty_tpl->tpl_vars['checkbox']->value['itemcomponentname'];?>
</span>
                                </span>
                            </td>
                            <td valign="top" style="word-break:break-all; padding: 10px;" width="130">
                                <?php if (($_smarty_tpl->tpl_vars['checkbox']->value['pricingmodel'] == 7 || $_smarty_tpl->tpl_vars['checkbox']->value['pricingmodel'] == 8)) {?>
                                    <div class="paymentsubcomponentquantity">
                                        <span class="txt_subqty"><?php echo $_smarty_tpl->tpl_vars['checkbox']->value['quantity'];?>
</span>
                                    </div>
                                <?php }?>
                            </td>
                            <td valign="top" align="right" style="word-break:break-all; padding: 10px;" width="130">
                                <span class="component-price"><span class="txt_price"><?php echo $_smarty_tpl->tpl_vars['checkbox']->value['totalsell'];?>
</span></span>
                            </td>
                        <?php }?>

                    </tr>
                     <?php if (($_smarty_tpl->tpl_vars['checkbox']->value['itemcomponentinfo'] != '' || $_smarty_tpl->tpl_vars['checkbox']->value['itemcomponentpriceinfo'])) {?> 
                    <tr>
                        <?php if ($_smarty_tpl->tpl_vars['subsection']->value['haspreview'] > 0 && $_smarty_tpl->tpl_vars['showthumbnail']->value != 0) {?>
                        <td colspan="3" width="500" style="word-break:break-all; padding: 10px;">
                        <?php } else { ?>
                        <td colspan="3" width="600" style="word-break:break-all; padding: 10px;">
                        <?php }?>
                        <?php if ($_smarty_tpl->tpl_vars['checkbox']->value['itemcomponentinfo'] != '') {?>
                            <div style="background-color:white; padding: 10px;" class="checkbox-info">
                                <span class="txt_itemsubcomponentinfo">
                                    <?php echo $_smarty_tpl->tpl_vars['checkbox']->value['itemcomponentinfo'];?>

                                </span>
                            </div>
                        <?php }?> 
                        <?php if ($_smarty_tpl->tpl_vars['checkbox']->value['itemcomponentpriceinfo'] != '') {?> 
                            <div style="background-color:white; word-wrap: break-word; padding: 10px;" class="checkbox-info">
                                <span class="txt_itemsubcomponentpriceinfo">
                                    <?php echo $_smarty_tpl->tpl_vars['checkbox']->value['itemcomponentpriceinfo'];?>

                                </span>
                            </div>
                        <?php }?> 
                        </td>
                    </tr>
                    <?php }?>
                </table>

                <div class="clear"></div>
                <?php if ($_smarty_tpl->tpl_vars['checkbox']->value['metadatahtml']) {?>
                    <span id="metadatarow_<?php echo $_smarty_tpl->tpl_vars['checkbox']->value['orderlineid'];?>
" class="component-metadata<?php if (!$_smarty_tpl->tpl_vars['checkbox']->value['checked']) {?> invisible<?php }?>"><span class="txt_subComponentMetaData"><?php echo $_smarty_tpl->tpl_vars['checkbox']->value['metadatahtml'];?>
</span></span>
                <?php }?>
            <?php }?>
        <?php }?>
    <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>     <!-- checkboxes inside component end -->    
        
<?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?> 
    <!-- sections end -->

    <div class="clear"></div>   
<?php }?> 
    <!-- linefooter sections start -->
    <?php if ($_smarty_tpl->tpl_vars['orderline']->value['orderlineid'] != -1) {?>
    <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['orderline']->value['linefootersections'], 'section');
$_smarty_tpl->tpl_vars['section']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['section']->value) {
$_smarty_tpl->tpl_vars['section']->do_else = false;
?>     <?php if ($_smarty_tpl->tpl_vars['section']->value['showcomponentname'] == true) {?>
            <table width="600" style="margin-bottom:10px; border: solid 1px white; font-size: 12px; color: #575757; font-family: Lucida Grande;">
                <tr style="color: #575757; font-family: Lucida Grande;">
                    <?php if ($_smarty_tpl->tpl_vars['section']->value['haspreview'] > 0 && $_smarty_tpl->tpl_vars['showthumbnail']->value != 0) {?>
                        <td valign="top" height="65" width="75" rowspan="2" style="word-break:break-all; padding: 10px;">
                            <img class="componentPreview"  src="<?php echo htmlspecialchars((string)$_smarty_tpl->tpl_vars['section']->value['componentpreviewsrc'], ENT_QUOTES, 'UTF-8', true);?>
" height="65" width="75">
                        </td>
                        <td valign="top" style="word-break:break-all; padding: 10px;" width="200">
                             <span class="txt_componentname">
                                <?php if ($_smarty_tpl->tpl_vars['section']->value['count'] <= 1 || $_smarty_tpl->tpl_vars['section']->value['prompt'] == '') {?>
                                    <span class="section-title"><?php echo $_smarty_tpl->tpl_vars['section']->value['sectionlabel'];?>
</span> <span> - </span>
                                    <span><?php echo $_smarty_tpl->tpl_vars['section']->value['itemcomponentname'];?>
</span>
                                <?php } else { ?>
                                    <span class="section-title"><?php echo $_smarty_tpl->tpl_vars['section']->value['sectionlabel'];?>
</span>
                                    <br>
                                    <span><?php echo $_smarty_tpl->tpl_vars['section']->value['itemcomponentname'];?>
</span>
                                <?php }?>
                            </span>
                        </td>
                        <td valign="top" style="word-break:break-all; padding: 10px;" width="130">
                            <?php if ($_smarty_tpl->tpl_vars['section']->value['pricingmodel'] == 7 || $_smarty_tpl->tpl_vars['section']->value['pricingmodel'] == 8) {?>
                                <div class="paymentcomponentquantity">
                                    <span class="txt_qty"><?php echo $_smarty_tpl->tpl_vars['section']->value['quantity'];?>
</span> 
                                </div>
                            <?php }?>
                        </td>
                        <td valign="top" align="right" style="word-break:break-all; padding: 10px;" width="130">
                            <span class="component-price"><span class="txt_price"><?php echo $_smarty_tpl->tpl_vars['section']->value['totalsell'];?>
</span></span>
                        </td>
                    <?php } else { ?>
                        <td valign="top" style="word-break:break-all; padding: 10px;" width="295">
                             <span class="txt_componentname">
                                <?php if ($_smarty_tpl->tpl_vars['section']->value['count'] <= 1 || $_smarty_tpl->tpl_vars['section']->value['prompt'] == '') {?>
                                    <span class="section-title"><?php echo $_smarty_tpl->tpl_vars['section']->value['sectionlabel'];?>
</span> <span> - </span>
                                    <span><?php echo $_smarty_tpl->tpl_vars['section']->value['itemcomponentname'];?>
</span>
                                <?php } else { ?>
                                    <span class="section-title"><?php echo $_smarty_tpl->tpl_vars['section']->value['sectionlabel'];?>
</span>
                                    <br>
                                    <span><?php echo $_smarty_tpl->tpl_vars['section']->value['itemcomponentname'];?>
</span>
                                <?php }?>
                            </span>
                        </td>
                        <td valign="top" style="word-break:break-all; padding: 10px;" width="130">
                            <?php if ($_smarty_tpl->tpl_vars['section']->value['pricingmodel'] == 7 || $_smarty_tpl->tpl_vars['section']->value['pricingmodel'] == 8) {?>
                                <div class="paymentcomponentquantity">
                                    <span class="txt_qty"><?php echo $_smarty_tpl->tpl_vars['section']->value['quantity'];?>
</span> 
                                </div>
                            <?php }?>
                        </td>
                        <td valign="top" align="right" style="word-break:break-all; padding: 10px;" width="130">
                            <span class="component-price"><span class="txt_price"><?php echo $_smarty_tpl->tpl_vars['section']->value['totalsell'];?>
</span></span>
                        </td>
                    <?php }?>

                </tr>
                <?php if (($_smarty_tpl->tpl_vars['section']->value['itemcomponentinfo'] != '' || $_smarty_tpl->tpl_vars['section']->value['itemcomponentpriceinfo'] != '')) {?>
                <tr>
                    <?php if ($_smarty_tpl->tpl_vars['section']->value['haspreview'] > 0 && $_smarty_tpl->tpl_vars['showthumbnail']->value != 0) {?>
                        <td valign="top" colspan="3" width="500" style="word-break:break-all; padding: 10px;">
                    <?php } else { ?>
                        <td valign="top" colspan="3" width="600" style="word-break:break-all; padding: 10px;">
                    <?php }?>
                            <?php if ($_smarty_tpl->tpl_vars['section']->value['itemcomponentinfo'] != '') {?>
                                <div class="section-info">
                                    <span class="txt_itemcomponentinfo"><?php echo $_smarty_tpl->tpl_vars['section']->value['itemcomponentinfo'];?>
</span>
                                </div>
                            <?php }?>
                            <?php if ($_smarty_tpl->tpl_vars['section']->value['itemcomponentpriceinfo'] != '') {?>
                                <div class="section-info">
                                    <span class="txt_itemcomponentpriceinfo"><?php echo $_smarty_tpl->tpl_vars['section']->value['itemcomponentpriceinfo'];?>
</span>
                                </div>
                            <?php }?>
                        </td>
                </tr>
                <?php }?>
            </table>
            
        <div>    
            <?php if ($_smarty_tpl->tpl_vars['section']->value['metadatahtml']) {?>
                <span class="component-metadata"><?php echo $_smarty_tpl->tpl_vars['section']->value['metadatahtml'];?>
</span>
            <?php }?>
        </div>
        <?php }?>

        <!-- sub-sections of linefooter component start -->
        <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['section']->value['subsections'], 'subsection');
$_smarty_tpl->tpl_vars['subsection']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['subsection']->value) {
$_smarty_tpl->tpl_vars['subsection']->do_else = false;
?>                 <?php if ($_smarty_tpl->tpl_vars['subsection']->value['showcomponentname'] == true) {?>
                    <table width="600" border="0px" style="margin-bottom:10px; font-size: 12px; color: white; font-family: Lucida Grande;">
                    <tr style="color: #575757; font-family: Lucida Grande;">
                        <?php if ($_smarty_tpl->tpl_vars['subsection']->value['haspreview'] > 0 && $_smarty_tpl->tpl_vars['showthumbnail']->value != 0) {?>
                            <td valign="top" height="65" width="75" rowspan="2" style="word-break:break-all; padding: 10px;">
                                <img class="componentPreview"  src="<?php echo htmlspecialchars((string)$_smarty_tpl->tpl_vars['subsection']->value['componentpreviewsrc'], ENT_QUOTES, 'UTF-8', true);?>
" height="65" width="75">
                            </td>

                            <td style="word-break:break-all;" width="200" style="word-break:break-all; padding: 10px;">
                                <span class="txt_subcomponentname">
                                    <?php if ($_smarty_tpl->tpl_vars['subsection']->value['count'] <= 1 || $_smarty_tpl->tpl_vars['subsection']->value['prompt'] == '') {?>
                                        <span class="section-title"><?php echo $_smarty_tpl->tpl_vars['subsection']->value['sectionlabel'];?>
</span><span> - </span>
                                        <span><?php echo $_smarty_tpl->tpl_vars['subsection']->value['itemcomponentname'];?>
</span>
                                    <?php } else { ?>
                                        <span class="section-title"><?php echo $_smarty_tpl->tpl_vars['subsection']->value['sectionlabel'];?>
</span>
                                        <br>
                                        <span><?php echo $_smarty_tpl->tpl_vars['subsection']->value['itemcomponentname'];?>
</span>
                                    <?php }?>
                                </span>
                            </td>
                            <td style="word-break:break-all; padding: 10px;" width="130">
                                <?php if ($_smarty_tpl->tpl_vars['subsection']->value['pricingmodel'] == 7 || $_smarty_tpl->tpl_vars['subsection']->value['pricingmodel'] == 8) {?>
                                    <div class="paymentsubcomponentquantity">
                                        <span class="txt_subqty"><?php echo $_smarty_tpl->tpl_vars['subsection']->value['quantity'];?>
</span>
                                    </div>
                                <?php }?>
                            </td>
                            <td align="right" style="word-break:break-all; padding: 10px;" width="130">
                                <span class="txt_price"><span class="component-price"><?php echo $_smarty_tpl->tpl_vars['subsection']->value['totalsell'];?>
</span></span>
                            </td>
                        <?php } else { ?>
                            <td style="word-break:break-all; padding: 10px;" width="295">
                                <span class="txt_subcomponentname">
                                    <?php if ($_smarty_tpl->tpl_vars['subsection']->value['count'] <= 1 || $_smarty_tpl->tpl_vars['subsection']->value['prompt'] == '') {?>
                                        <span class="section-title"><?php echo $_smarty_tpl->tpl_vars['subsection']->value['sectionlabel'];?>
</span><span> - </span>
                                        <span><?php echo $_smarty_tpl->tpl_vars['subsection']->value['itemcomponentname'];?>
</span>
                                    <?php } else { ?>
                                        <span class="section-title"><?php echo $_smarty_tpl->tpl_vars['subsection']->value['sectionlabel'];?>
</span>
                                        <br>
                                        <span><?php echo $_smarty_tpl->tpl_vars['subsection']->value['itemcomponentname'];?>
</span>
                                    <?php }?>
                                </span>
                            </td>
                            <td style="word-break:break-all; padding: 10px;" width="130">
                                <?php if ($_smarty_tpl->tpl_vars['subsection']->value['pricingmodel'] == 7 || $_smarty_tpl->tpl_vars['subsection']->value['pricingmodel'] == 8) {?>
                                    <div class="paymentsubcomponentquantity">
                                        <span class="txt_subqty"><?php echo $_smarty_tpl->tpl_vars['subsection']->value['quantity'];?>
</span>
                                    </div>
                                <?php }?>
                            </td>
                            <td align="right" style="word-break:break-all; padding: 10px;" width="130">
                                <span class="txt_price"><span class="component-price"><?php echo $_smarty_tpl->tpl_vars['subsection']->value['totalsell'];?>
</span></span>
                            </td>
                        <?php }?>

                    </tr>
                    <?php if (($_smarty_tpl->tpl_vars['subsection']->value['itemcomponentinfo'] != '' || $_smarty_tpl->tpl_vars['subsection']->value['itemcomponentpriceinfo'] != '')) {?>
                    <tr>
                        <?php if ($_smarty_tpl->tpl_vars['subsection']->value['haspreview'] > 0 && $_smarty_tpl->tpl_vars['showthumbnail']->value != 0) {?>
                            <td colspan="3" width="500" style="word-break:break-all; padding: 10px;">
                        <?php } else { ?>
                            <td colspan="3" width="600" style="word-break:break-all; padding: 10px;">
                        <?php }?>
                            <?php if ($_smarty_tpl->tpl_vars['subsection']->value['itemcomponentinfo'] != '') {?>
                                <div class="section-info">
                                    <span class="txt_itemsubcomponentinfo"><?php echo $_smarty_tpl->tpl_vars['subsection']->value['itemcomponentinfo'];?>
</span>
                                </div>
                            <?php }?>
                            <?php if ($_smarty_tpl->tpl_vars['subsection']->value['itemcomponentpriceinfo'] != '') {?>
                                <div class="section-info">
                                    <span class="txt_itemsubcomponentpriceinfo"><?php echo $_smarty_tpl->tpl_vars['subsection']->value['itemcomponentpriceinfo'];?>
</span>
                                </div>
                            <?php }?>
                            </td>
                    </tr>
                    <?php }?>
                </table>

                <?php if ($_smarty_tpl->tpl_vars['subsection']->value['metadatahtml']) {?>
                    <span id="metadatarow_<?php echo $_smarty_tpl->tpl_vars['subsection']->value['orderlineid'];?>
" class="component-metadata">
                        <span class="txt_subComponentMetaData">
                            <?php echo $_smarty_tpl->tpl_vars['subsection']->value['metadatahtml'];?>

                        </span>
                    </span>
                <?php }?>
        <?php }?>
    <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?> 
    <!-- sub-sections of linefooter component end -->       
     
    <div class="clear"></div>

    <!-- checkboxes inside linefooter component start -->
        <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['section']->value['checkboxes'], 'checkbox');
$_smarty_tpl->tpl_vars['checkbox']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['checkbox']->value) {
$_smarty_tpl->tpl_vars['checkbox']->do_else = false;
?>             <?php if ($_smarty_tpl->tpl_vars['checkbox']->value['showcomponentname'] == true) {?>
                <?php if ($_smarty_tpl->tpl_vars['checkbox']->value['checked'] == 1) {?>
                <table width="600" border="0px" style="margin-bottom:10px; font-size: 12px; color: white; font-family: Lucida Grande;">
                    <tr style="color: #575757; font-family: Lucida Grande;">
                        <?php if ($_smarty_tpl->tpl_vars['checkbox']->value['haspreview'] > 0 && $_smarty_tpl->tpl_vars['showthumbnail']->value != 0) {?>
                            <td valign="top"  width="65px" rowspan="2" style="word-break:break-all;">
                                <img class="componentPreview"  src="<?php echo $_smarty_tpl->tpl_vars['checkbox']->value['componentpreviewsrc'];?>
" height="65" width="75">
                            </td>

                            <td style="word-break:break-all; padding: 10px;" width="200">
                                <span class="txt_subcomponentname">
                                    <span class="component-name"><?php echo $_smarty_tpl->tpl_vars['checkbox']->value['itemcomponentprompt'];?>
</span> 
                                    <span> - <?php echo $_smarty_tpl->tpl_vars['checkbox']->value['itemcomponentname'];?>
</span>
                                </span>
                            </td>
                            <td style="word-break:break-all; padding: 10px;" width="130">
                                <?php if (($_smarty_tpl->tpl_vars['checkbox']->value['pricingmodel'] == 7 || $_smarty_tpl->tpl_vars['checkbox']->value['pricingmodel'] == 8)) {?>
                                    <div class="paymentsubcomponentquantity">
                                        <span class="txt_subqty"><?php echo $_smarty_tpl->tpl_vars['checkbox']->value['quantity'];?>
</span>
                                    </div>
                                <?php }?>
                            </td>
                            <td align="right" style="word-break:break-all; padding: 10px;" width="130">
                                <span class="component-price"><span class="txt_price"><?php echo $_smarty_tpl->tpl_vars['checkbox']->value['totalsell'];?>
</span></span>
                            </td>
                        <?php } else { ?>
                            <td style="word-break:break-all; padding: 10px;" width="295">
                                <span class="txt_subcomponentname">
                                    <span class="component-name"><?php echo $_smarty_tpl->tpl_vars['checkbox']->value['itemcomponentprompt'];?>
</span> 
                                    <span> - <?php echo $_smarty_tpl->tpl_vars['checkbox']->value['itemcomponentname'];?>
</span>
                                </span>
                            </td>
                            <td style="word-break:break-all; padding: 10px;" width="130">
                                <?php if (($_smarty_tpl->tpl_vars['checkbox']->value['pricingmodel'] == 7 || $_smarty_tpl->tpl_vars['checkbox']->value['pricingmodel'] == 8)) {?>
                                    <div class="paymentsubcomponentquantity">
                                        <span class="txt_subqty"><?php echo $_smarty_tpl->tpl_vars['checkbox']->value['quantity'];?>
</span>
                                    </div>
                                <?php }?>
                            </td>
                            <td align="right" style="word-break:break-all; padding: 10px;" width="130">
                                <span class="component-price"><span class="txt_price"><?php echo $_smarty_tpl->tpl_vars['checkbox']->value['totalsell'];?>
</span></span>
                            </td>
                        <?php }?>

                    </tr>
                     <?php if (($_smarty_tpl->tpl_vars['checkbox']->value['itemcomponentinfo'] != '' || $_smarty_tpl->tpl_vars['checkbox']->value['itemcomponentpriceinfo'])) {?> 
                    <tr>
                        <?php if ($_smarty_tpl->tpl_vars['subsection']->value['haspreview'] > 0 && $_smarty_tpl->tpl_vars['showthumbnail']->value != 0) {?>
                        <td colspan="3" width="500" style="word-break:break-all; padding: 10px;">
                        <?php } else { ?>
                        <td colspan="3" width="600" style="word-break:break-all; padding: 10px;">
                        <?php }?>
                        <?php if ($_smarty_tpl->tpl_vars['checkbox']->value['itemcomponentinfo'] != '') {?>
                            <div style="background-color:white; word-wrap: break-word" class="checkbox-info">
                                <span class="txt_itemsubcomponentinfo">
                                    <?php echo $_smarty_tpl->tpl_vars['checkbox']->value['itemcomponentinfo'];?>

                                </span>
                            </div>
                        <?php }?> 
                        <?php if ($_smarty_tpl->tpl_vars['checkbox']->value['itemcomponentpriceinfo'] != '') {?> 
                            <div style="background-color:white; word-wrap: break-word" class="checkbox-info">
                                <span class="txt_itemsubcomponentpriceinfo">
                                    <?php echo $_smarty_tpl->tpl_vars['checkbox']->value['itemcomponentpriceinfo'];?>

                                </span>
                            </div>
                        <?php }?> 
                        </td>
                    </tr>
                    <?php }?>
                </table>

                    <?php if ($_smarty_tpl->tpl_vars['checkbox']->value['metadatahtml']) {?>
                        <span id="metadatarow_<?php echo $_smarty_tpl->tpl_vars['checkbox']->value['orderlineid'];?>
" class="component-metadata<?php if (!$_smarty_tpl->tpl_vars['checkbox']->value['checked']) {?> invisible<?php }?>"><span class="txt_subComponentMetaData"><?php echo $_smarty_tpl->tpl_vars['checkbox']->value['metadatahtml'];?>
</span></span>
                    <?php }?>
                
            <?php }?>
        <?php }?>
    <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?> 
    <!-- checkboxes inside linefooter component end -->

    <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>     <?php }?>
    <!-- linefooter sections end -->    

    <div class="clear"></div>

    <!-- linefooter checkboxes start -->
    <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['orderline']->value['linefootercheckboxes'], 'checkbox');
$_smarty_tpl->tpl_vars['checkbox']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['checkbox']->value) {
$_smarty_tpl->tpl_vars['checkbox']->do_else = false;
?>
    <?php if ($_smarty_tpl->tpl_vars['checkbox']->value['showcomponentname'] == true) {?>
        <?php if ($_smarty_tpl->tpl_vars['checkbox']->value['checked'] == 1) {?>
            <table width="600" border="0px" style="margin-bottom:10px; font-size: 12px; color: white; font-family: Lucida Grande;">
                <tr style="color: #575757; font-family: Lucida Grande;">
                    <?php if ($_smarty_tpl->tpl_vars['checkbox']->value['haspreview'] > 0 && $_smarty_tpl->tpl_vars['showthumbnail']->value != 0) {?>
                        <td valign="top" height="65" width="75" rowspan="2" style="word-break:break-all; padding: 10px;">
                            <img class="componentPreview"  src="<?php echo htmlspecialchars((string)$_smarty_tpl->tpl_vars['checkbox']->value['componentpreviewsrc'], ENT_QUOTES, 'UTF-8', true);?>
" height="65" width="75">
                        </td>

                        <td style="word-break:break-all; padding: 10px;" width="200">
                            <span class="txt_subcomponentname">
                                <span class="component-name"><?php echo $_smarty_tpl->tpl_vars['checkbox']->value['itemcomponentprompt'];?>
</span>  <span> - <?php echo $_smarty_tpl->tpl_vars['checkbox']->value['itemcomponentname'];?>
 </span>
                            </span>
                        </td>
                        <td style="word-break:break-all; padding: 10px;" width="130">
                            <?php if (($_smarty_tpl->tpl_vars['checkbox']->value['pricingmodel'] == 7 || $_smarty_tpl->tpl_vars['checkbox']->value['pricingmodel'] == 8)) {?>
                                <div class="paymentsubcomponentquantity">
                                    <span class="txt_subqty"><?php echo $_smarty_tpl->tpl_vars['checkbox']->value['quantity'];?>
</span>
                                </div>
                            <?php }?>
                        </td>
                        <td align="right" style="word-break:break-all; padding: 10px;" width="130">
                            <span class="txt_price"><span class="component-price"><?php echo $_smarty_tpl->tpl_vars['checkbox']->value['totalsell'];?>
</span></span>
                        </td>
                    <?php } else { ?>
                        <td style="word-break:break-all; padding: 10px;" width="295">
                            <span class="txt_subcomponentname">
                                <span class="component-name"><?php echo $_smarty_tpl->tpl_vars['checkbox']->value['itemcomponentprompt'];?>
</span>  <span> - <?php echo $_smarty_tpl->tpl_vars['checkbox']->value['itemcomponentname'];?>
 </span>
                            </span>
                        </td>
                        <td style="word-break:break-all; padding: 10px;" width="130">
                            <?php if (($_smarty_tpl->tpl_vars['checkbox']->value['pricingmodel'] == 7 || $_smarty_tpl->tpl_vars['checkbox']->value['pricingmodel'] == 8)) {?>
                                <div class="paymentsubcomponentquantity">
                                    <span class="txt_subqty"><?php echo $_smarty_tpl->tpl_vars['checkbox']->value['quantity'];?>
</span>
                                </div>
                            <?php }?>
                        </td>
                        <td align="right" style="word-break:break-all; padding: 10px;" width="130">
                            <span class="txt_price"><span class="component-price"><?php echo $_smarty_tpl->tpl_vars['checkbox']->value['totalsell'];?>
</span></span>
                        </td>
                    <?php }?>

                </tr>
                <?php if ($_smarty_tpl->tpl_vars['checkbox']->value['itemcomponentinfo'] != '' || $_smarty_tpl->tpl_vars['checkbox']->value['itemcomponentpriceinfo'] != '') {?>
                <tr>
                    <?php if ($_smarty_tpl->tpl_vars['checkbox']->value['haspreview'] > 0 && $_smarty_tpl->tpl_vars['showthumbnail']->value != 0) {?>
                    <td colspan="3" width="500" style="word-break:break-all; padding: 10px;">
                    <?php } else { ?>
                    <td colspan="3" width="600" style="word-break:break-all; padding: 10px;">
                    <?php }?>
                        <?php if ($_smarty_tpl->tpl_vars['checkbox']->value['itemcomponentinfo'] != '') {?><div class="checkbox-info"><span class="txt_itemcomponentinfo"><?php echo $_smarty_tpl->tpl_vars['checkbox']->value['itemcomponentinfo'];?>
</span></div><?php }?>
                        <?php if ($_smarty_tpl->tpl_vars['checkbox']->value['itemcomponentpriceinfo'] != '') {?><div class="checkbox-info"><span class="txt_itemcomponentpriceinfo"><?php echo $_smarty_tpl->tpl_vars['checkbox']->value['itemcomponentpriceinfo'];?>
</span></div><?php }?>
                    </td>
                </tr>
                <?php }?>
            </table>

            <?php if ($_smarty_tpl->tpl_vars['checkbox']->value['metadatahtml']) {?>
                <span id="metadatarow_<?php echo $_smarty_tpl->tpl_vars['checkbox']->value['orderlineid'];?>
" class="component-metadata<?php if (!$_smarty_tpl->tpl_vars['checkbox']->value['checked']) {?> invisible<?php }?>"><span class="txt_subComponentMetaData"><?php echo $_smarty_tpl->tpl_vars['checkbox']->value['metadatahtml'];?>
</span></span>
            <?php }?>
               
            <?php }?>
        <?php }?>
    <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>

    <!-- linefooter checkboxes end -->

            
    </div>
<?php if ($_smarty_tpl->tpl_vars['orderline']->value['orderlineid'] != -1) {?>
    <div class="line-total">
    <span class="txt_linebreak"></span>
        
            
            <?php if ((!$_smarty_tpl->tpl_vars['orderline']->value['itemvoucherapplied']) || ($_smarty_tpl->tpl_vars['vouchersection']->value == 'SHIPPING') || (($_smarty_tpl->tpl_vars['vouchersection']->value == 'TOTAL') && !(($_smarty_tpl->tpl_vars['differenttaxrates']->value) && (!$_smarty_tpl->tpl_vars['specialvouchertype']->value)))) {?>

                                <?php if ((($_smarty_tpl->tpl_vars['differenttaxrates']->value) && ($_smarty_tpl->tpl_vars['showpriceswithtax']->value))) {?>

                    <?php if ((($_smarty_tpl->tpl_vars['vouchersection']->value == 'TOTAL') && ($_smarty_tpl->tpl_vars['specialvouchertype']->value))) {?>
                            <div class="line-sub-total">
                                <span class="total-heading"><span class="txt_linetotal"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelSubTotal');?>
:</span></span>
                                <span class="order-line-price"><span class="txt_price"><?php echo $_smarty_tpl->tpl_vars['orderline']->value['itemcompletetotal'];?>
</span></span>
                            </div>

                        <?php if (($_smarty_tpl->tpl_vars['orderline']->value['itemdiscountvalueraw'] > 0)) {?>
                            <div class="line-sub-total">
                                <span class="total-heading"><span class="txt_linetotal"><?php echo $_smarty_tpl->tpl_vars['orderline']->value['itemdiscountname'];?>
:</span></span>
                                <span class="order-line-price"><span class="txt_price"><?php echo $_smarty_tpl->tpl_vars['orderline']->value['itemdiscountvalue'];?>
</span></span>
                            </div>
                            <div class="line-sub-total">
                                <span class="total-heading"><span class="txt_linetotal"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelOrderItemListItemTotal');?>
:</span></span>
                                <span class="order-line-price"><span class="txt_price"><?php echo $_smarty_tpl->tpl_vars['orderline']->value['itemdiscountedvalue'];?>
</span></span>
                            </div>
                        <?php }?>
                        { * SHOWZEROTAX OR DONT SHOWZEROTAX AND THE TAX > 0 * }
                        <?php if ((($_smarty_tpl->tpl_vars['showzerotax']->value) || ((!$_smarty_tpl->tpl_vars['showzerotax']->value) && ($_smarty_tpl->tpl_vars['orderline']->value['itemtaxtotalraw'] > 0)))) {?>
                            <div class="line-sub-total"><span class="txt_includetaxtext"><?php echo $_smarty_tpl->tpl_vars['orderline']->value['includesitemtaxtext'];?>
</span></div>
                        <?php }?>
                    <?php } else { ?>
                            <div class="line-sub-total">
                                <span class="total-heading"><span class="txt_linetotal"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelOrderItemListItemTotal');?>
:</span></span>
                                <span class="order-line-price"><span class="txt_price"><?php echo $_smarty_tpl->tpl_vars['orderline']->value['itemcompletetotal'];?>
</span></span>
                            </div>

                                                <?php if (($_smarty_tpl->tpl_vars['showtaxbreakdown']->value)) {?>
                            <?php if ((($_smarty_tpl->tpl_vars['showzerotax']->value) || ((!$_smarty_tpl->tpl_vars['showzerotax']->value) && ($_smarty_tpl->tpl_vars['orderline']->value['itemtaxtotalraw'] > 0)))) {?>
                            <div class="line-sub-total"><span class="txt_includetaxtext"><?php echo $_smarty_tpl->tpl_vars['orderline']->value['includesitemtaxtext'];?>
</span></div>
                            <?php }?>
                        <?php }?>
                    <?php }?>
                <?php }?>

                                <?php if ((($_smarty_tpl->tpl_vars['differenttaxrates']->value) && (!$_smarty_tpl->tpl_vars['showpriceswithtax']->value))) {?>

                                        <?php if ((($_smarty_tpl->tpl_vars['vouchersection']->value == 'TOTAL') && ($_smarty_tpl->tpl_vars['specialvouchertype']->value))) {?>
                            <?php if (($_smarty_tpl->tpl_vars['orderline']->value['itemdiscountvalueraw'] > 0)) {?>
                                <div class="line-sub-total">
                                    <span class="total-heading"><span class="txt_linetotal"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelSubTotal');?>
:</span></span>
                                    <span class="order-line-price"><span class="txt_price"><?php echo $_smarty_tpl->tpl_vars['orderline']->value['itemcompletetotal'];?>
</span></span>
                                </div>
                                <div class="line-sub-total">
                                    <span class="total-heading"><span class="txt_linetotal"><?php echo $_smarty_tpl->tpl_vars['orderline']->value['itemdiscountname'];?>
:</span></span>
                                    <span class="order-line-price"><span class="txt_price"><?php echo $_smarty_tpl->tpl_vars['orderline']->value['itemdiscountvalue'];?>
</span></span>
                                </div>
                            <?php }?>
                            { * SHOWZEROTAX OR DONT SHOWZEROTAX AND THE TAX > 0 * }
                            <?php if ((($_smarty_tpl->tpl_vars['showzerotax']->value) || ((!$_smarty_tpl->tpl_vars['showzerotax']->value) && ($_smarty_tpl->tpl_vars['orderline']->value['itemtaxtotalraw'] > 0)))) {?>
                                <div class="line-sub-total">
                                    <span class="total-heading"><span class="txt_linetotal"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelSubTotal');?>
:</span></span>
                                    <span class="order-line-price"><span class="txt_price"><?php echo $_smarty_tpl->tpl_vars['orderline']->value['itemdiscountedvalue'];?>
</span></span>
                                </div>
                                <div class="line-sub-total">
                                    <span class="total-heading"><span class="txt_linetotal"><?php echo $_smarty_tpl->tpl_vars['orderline']->value['itemtaxratename'];?>
 (<?php echo $_smarty_tpl->tpl_vars['orderline']->value['itemtaxrate'];?>
%):</span></span>
                                    <span class="order-line-price"><span class="txt_price"><?php echo $_smarty_tpl->tpl_vars['orderline']->value['itemtaxtotal'];?>
</span></span>
                                </div>
                            <?php }?>

                    <?php } else { ?>

                        { * SHOWZEROTAX OR DONT SHOWZEROTAX AND THE TAX > 0 * }
                        <?php if ((($_smarty_tpl->tpl_vars['showzerotax']->value) || ((!$_smarty_tpl->tpl_vars['showzerotax']->value) && ($_smarty_tpl->tpl_vars['orderline']->value['itemtaxtotalraw'] > 0)))) {?>

                            <div class="line-sub-total">
                                <span class="total-heading"><span class="txt_linetotal"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelSubTotal');?>
:</span></span>
                                <span class="order-line-price"><span class="txt_price"><?php echo $_smarty_tpl->tpl_vars['orderline']->value['itemcompletetotal'];?>
</span></span>
                            </div>
                            <div class="line-sub-total">
                                <span class="total-heading"><span class="txt_linetotal"><?php echo $_smarty_tpl->tpl_vars['orderline']->value['itemtaxratename'];?>
 (<?php echo $_smarty_tpl->tpl_vars['orderline']->value['itemtaxrate'];?>
%):</span></span>
                                <span class="order-line-price"><span class="txt_price"><?php echo $_smarty_tpl->tpl_vars['orderline']->value['itemtaxtotal'];?>
</span></span>
                            </div>
                        <?php }?>
                    <?php }?>

                        <div class="line-sub-total">
                            <span class="total-heading"><span class="txt_linetotal"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelOrderItemListItemTotal');?>
:</span></span>
                            <span class="order-line-price"><span class="txt_price"><?php echo $_smarty_tpl->tpl_vars['orderline']->value['itemtotal'];?>
</span></span>
                        </div>
                <?php }?>

                                <?php if ((!$_smarty_tpl->tpl_vars['differenttaxrates']->value)) {?>
                    <?php if ((($_smarty_tpl->tpl_vars['orderline']->value['itemvoucherapplied'] == 1) && ($_smarty_tpl->tpl_vars['applyVoucherAsLineDiscount']->value == true))) {?>
                    <div class="line-sub-total">
                        <span class="total-heading"><span class="txt_linetotal"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelSubTotal');?>
:</span></span>
                        <span class="order-line-price"><span class="txt_price"><?php echo $_smarty_tpl->tpl_vars['orderline']->value['itemsubtotal'];?>
</span></span>
                    </div>
                    <div class="line-sub-total">
                        <span class="total-heading"><span class="txt_linetotal"><?php echo $_smarty_tpl->tpl_vars['orderline']->value['itemdiscountname'];?>
:</span></span>
                        <span class="order-line-price"><span class="txt_price"><?php echo $_smarty_tpl->tpl_vars['orderline']->value['itemdiscountvalue'];?>
</span></span>
                    </div>
                    <?php }?>
                    <div class="line-sub-total">
                        <span class="total-heading"><span class="txt_linetotal"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelOrderItemListItemTotal');?>
:</span></span>
                        <span class="order-line-price"><span class="txt_price"><?php echo $_smarty_tpl->tpl_vars['orderline']->value['itemtotal'];?>
</span></span>
                    </div>

                <?php }?>

            <?php } else { ?>

                                <?php if (($_smarty_tpl->tpl_vars['vouchersection']->value == 'PRODUCT')) {?>
                    <?php if (($_smarty_tpl->tpl_vars['orderline']->value['itemdiscountvalueraw'] > 0)) {?>
                    <div class="line-sub-total">
                        <span class="total-heading"><span class="txt_linetotal"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelSubTotal');?>
:</span></span>
                        <span class="order-line-price"><span class="txt_price"><?php echo $_smarty_tpl->tpl_vars['orderline']->value['itemcompletetotal'];?>
</span></span>
                    </div>
                    <div class="line-sub-total">
                        <span class="total-heading"><span class="txt_linetotal"><?php echo $_smarty_tpl->tpl_vars['orderline']->value['itemdiscountname'];?>
:</span></span>
                        <span class="order-line-price"><span class="txt_price"><?php echo $_smarty_tpl->tpl_vars['orderline']->value['itemdiscountvalue'];?>
</span></span>
                    </div>
                    <?php }?>
                                        <?php if ((($_smarty_tpl->tpl_vars['differenttaxrates']->value) && ($_smarty_tpl->tpl_vars['showpriceswithtax']->value))) {?>

                        <div class="line-sub-total">
                            <span class="total-heading"><span class="txt_linetotal"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelOrderItemListItemTotal');?>
:</span></span>
                            <span class="order-line-price"><span class="txt_price"><?php echo $_smarty_tpl->tpl_vars['orderline']->value['itemdiscountedvalue'];?>
</span></span>
                        </div>

                                                <?php if (($_smarty_tpl->tpl_vars['showtaxbreakdown']->value)) {?>
                            <?php if ((($_smarty_tpl->tpl_vars['showzerotax']->value) || ((!$_smarty_tpl->tpl_vars['showzerotax']->value) && ($_smarty_tpl->tpl_vars['orderline']->value['itemtaxtotalraw'] > 0)))) {?>
                            <div class="line-sub-total"><span class="txt_includetaxtext"><?php echo $_smarty_tpl->tpl_vars['orderline']->value['includesitemtaxtext'];?>
</span></div>
                            <?php }?>
                        <?php }?>
                    <?php }?>

                                        <?php if ((($_smarty_tpl->tpl_vars['differenttaxrates']->value) && (!$_smarty_tpl->tpl_vars['showpriceswithtax']->value))) {?>

                        { * SHOWZEROTAX OR DONT SHOWZEROTAX AND THE TAX > 0 * }
                        <?php if ((($_smarty_tpl->tpl_vars['showzerotax']->value) || ((!$_smarty_tpl->tpl_vars['showzerotax']->value) && ($_smarty_tpl->tpl_vars['orderline']->value['itemtaxtotalraw'] > 0)))) {?>

                            <div class="line-sub-total">
                                <span class="total-heading"><span class="txt_linetotal"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelSubTotal');?>
:</span></span>
                                <span class="order-line-price"><span class="txt_price"><?php echo $_smarty_tpl->tpl_vars['orderline']->value['itemdiscountedvalue'];?>
</span></span>
                            </div>
                            <div class="line-sub-total">
                                <span class="total-heading"><span class="txt_linetotal"><?php echo $_smarty_tpl->tpl_vars['orderline']->value['itemtaxratename'];?>
 (<?php echo $_smarty_tpl->tpl_vars['orderline']->value['itemtaxrate'];?>
%):</span></span>
                                <span class="order-line-price"><span class="txt_price"><?php echo $_smarty_tpl->tpl_vars['orderline']->value['itemtaxtotal'];?>
</span></span>
                            </div>
                        <?php }?>
                        <div class="line-sub-total">
                            <span class="total-heading"><span class="txt_linetotal"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelOrderItemListItemTotal');?>
:</span></span>
                            <span class="order-line-price"><span class="txt_price"><?php echo $_smarty_tpl->tpl_vars['orderline']->value['itemtotal'];?>
</span></span>
                        </div>
                    <?php }?>

                                        <?php if ((!$_smarty_tpl->tpl_vars['differenttaxrates']->value)) {?>

                        <div class="line-sub-total">
                            <span class="total-heading"><span class="txt_linetotal"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelOrderItemListItemTotal');?>
:</span></span>
                            <span class="order-line-price"><span class="txt_price"><?php echo $_smarty_tpl->tpl_vars['orderline']->value['itemdiscountedvalue'];?>
</span></span>
                        </div>

                    <?php }?>

                <?php }?>

                                <?php if ((($_smarty_tpl->tpl_vars['vouchersection']->value == 'TOTAL') && ((($_smarty_tpl->tpl_vars['differenttaxrates']->value) && (!$_smarty_tpl->tpl_vars['specialvouchertype']->value)) || ($_smarty_tpl->tpl_vars['applyVoucherAsLineDiscount']->value == true)))) {?>
                    <?php if (($_smarty_tpl->tpl_vars['orderline']->value['itemdiscountvalueraw'] > 0)) {?>
                        <div class="line-sub-total">
                            <span class="total-heading"><span class="txt_linetotal"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelSubTotal');?>
:</span></span>
                            <span class="order-line-price"><span class="txt_price"><?php echo $_smarty_tpl->tpl_vars['orderline']->value['itemcompletetotal'];?>
</span></span>
                        </div>
                        <div class="line-sub-total">
                            <span class="total-heading"><span class="txt_linetotal"><?php echo $_smarty_tpl->tpl_vars['orderline']->value['itemdiscountname'];?>
:</span></span>
                            <span class="order-line-price"><span class="txt_price"><?php echo $_smarty_tpl->tpl_vars['orderline']->value['itemdiscountvalue'];?>
</span></span>
                        </div>
                    <?php }?>
                    <?php if ((!$_smarty_tpl->tpl_vars['showpriceswithtax']->value)) {?>
                        { * SHOWZEROTAX OR DONT SHOWZEROTAX AND THE TAX > 0 * }
                        <?php if ((($_smarty_tpl->tpl_vars['showzerotax']->value) || ((!$_smarty_tpl->tpl_vars['showzerotax']->value) && ($_smarty_tpl->tpl_vars['orderline']->value['itemtaxtotalraw'] > 0)))) {?>
                            <div class="line-sub-total">
                                <span class="total-heading"><span class="txt_linetotal"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelSubTotal');?>
:</span></span>
                                <span class="order-line-price"><span class="txt_price"><?php echo $_smarty_tpl->tpl_vars['orderline']->value['itemdiscountedvalue'];?>
</span></span>
                            </div>
                            <div class="line-sub-total">
                                <span class="total-heading"><span class="txt_linetotal"><?php echo $_smarty_tpl->tpl_vars['orderline']->value['itemtaxratename'];?>
 (<?php echo $_smarty_tpl->tpl_vars['orderline']->value['itemtaxrate'];?>
%):</span></span>
                                <span class="order-line-price"><span class="txt_price"><?php echo $_smarty_tpl->tpl_vars['orderline']->value['itemtaxtotal'];?>
</span></span>
                            </div>
                            <div class="line-sub-total">
                                <span class="total-heading"><span class="txt_linetotal"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelOrderItemListItemTotal');?>
:</span></span>
                                <span class="order-line-price"><span class="txt_price"><?php echo $_smarty_tpl->tpl_vars['orderline']->value['itemtotal'];?>
</span></span>
                            </div>
                        <?php } else { ?>
                            <div class="line-sub-total">
                                <span class="total-heading"><span class="txt_linetotal"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelOrderItemListItemTotal');?>
:</span></span>
                                <span class="order-line-price"><span class="txt_price"><?php echo $_smarty_tpl->tpl_vars['orderline']->value['itemdiscountedvalue'];?>
</span></span>
                            </div>
                        <?php }?>
                    <?php } else { ?>
                        <div class="line-sub-total">
                            <span class="total-heading"><span class="txt_linetotal"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelOrderItemListItemTotal');?>
:</span></span>
                            <span class="order-line-price"><span class="txt_price"><?php echo $_smarty_tpl->tpl_vars['orderline']->value['itemtotal'];?>
</span></span>
                        </div>

                        { * SHOWZEROTAX OR DONT SHOWZEROTAX AND THE TAX > 0 * }
                        <?php if ((($_smarty_tpl->tpl_vars['showzerotax']->value) || ((!$_smarty_tpl->tpl_vars['showzerotax']->value) && ($_smarty_tpl->tpl_vars['orderline']->value['itemtaxtotalraw'] > 0)))) {?>
                            <div class="line-sub-total"><span class="txt_includetaxtext"><?php echo $_smarty_tpl->tpl_vars['orderline']->value['includesitemtaxtext'];?>
</span></div>
                        <?php }?>
                    <?php }?>
                <?php }?>
            <?php }?>
    </div>
    <?php }?>
    <span class="txt_singleLineDivider"></span>

    <div class="clear"></div>
    <?php }
}
